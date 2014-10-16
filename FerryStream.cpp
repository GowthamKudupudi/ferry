/* 
 * File:   FerryStream.cpp
 * Author: Gowtham
 * 
 * Created on September 28, 2014, 5:01 PM
 */

#include "FerryStream.h"
#include "global.h"
#include <libwebsockets.h>
#include <base/ServerSocket.h>
#include <base/SocketException.h>
#include <base/mystdlib.h>
#include <base/myconverters.h>
#include <base/FFJSON.h>
#include <base/JPEGImage.h>
#include <base/logger.h>
#include <cstdlib>
#include <string>
#include <iostream>
#include <fstream>
#include <fstream>
#include <thread>
#include <exception>
#include <vector>
#include <functional>
#include <unistd.h>
#include <stdio.h>
#include <fcntl.h>
#include <sys/types.h>
#include <list>

FerryStream::FerryStream() {
}

FerryStream::FerryStream(const FerryStream& orig) {
}

FerryStream::FerryStream(ServerSocket::Connection * source, void(*funeral)(string)) : source(source), funeral(funeral) {
	string initPack;
	try {
		*source >> this->buffer;
	} catch (SocketException e) {
		throw Exception("Unable to receive a initial packet.");
	}
	initPack = this->buffer.substr(0, this->buffer.find('}') + 1);
	FFJSON* init_ffjson;
	try {
		init_ffjson = new FFJSON(initPack);
	} catch (FFJSON::Exception e) {
		throw Exception("Unable parse initial packet. FFJSON::Exception:" + string(e.what()));
	}
	try {
		if ((this->path = string((const char*) (*init_ffjson)["path"])).length() < 0) {
			throw Exception("path length didn't met required value");
			delete init_ffjson;
		};
		if ((source->MAXRECV = (int) (*init_ffjson)["MAXPACKSIZE"]) <= 0) {
			throw Exception("Max packet size didn't met required value");
			delete init_ffjson;
		};
	} catch (FFJSON::Exception e) {
		throw Exception("Illegal initial packet. FFJSON::Exception:" + string(e.what()));
		delete init_ffjson;
	}
	ffl_notice(FPL_FSTREAM_HEART, "new connection received with path :%s", this->path.c_str());
	delete init_ffjson;
	packs_to_send[this->path] = new list<FFJSON*>();
	this->heartThread = new thread(&FerryStream::heart, this);
	//pthread_create(&heartThread, NULL, (void*(*)(void*)) & this->heart, NULL);
}

FerryStream::~FerryStream() {
	delete this->source;
	std::map < std::string, bool> ::iterator i;
	i = packs_to_send.begin();
	while (i != packs_to_send.end()) {
		if (i->first.compare(this->path) == 0) {
			packs_to_send.erase(i);
			break;
		}
		i++;
	}
	std::map<std::string, std::string*> ::iterator j;
	j = streamHeads.begin();
	while (j != streamHeads.end()) {
		if (j->first.compare(this->path) == 0) {
			free((void*) j->second->c_str());
			delete j->second;
			streamHeads.erase(j);
			break;
		}
		j++;
	}
}

FerryStream::Exception::Exception(std::string e) : identifier(e) {
}

FerryStream::Exception::~Exception() {
};

const char* FerryStream::Exception::what() const throw () {
	return this->identifier.c_str();
}

void FerryStream::die() {
	suicide = true;
	delete source;
}

bool FerryStream::isConnectionAlive() {
	string testPacket = "{\"test\"}";
	try {
		*this->source << testPacket;
	} catch (SocketException e) {
		return false;
	}
	return true;
}

void FerryStream::heart(FerryStream* fs) {
	string truebuffer = "";
	bool pckEndReached = false;
	bool goodPacket = true;
	string index_str = "";
	int index = 0;
	int dindex = 7;
	string terminatingSign = "";
	int packStartIndex = -1;
	int packEndIndex = -1;
	string bridge;
	while (true && !force_exit) {
		pckEndReached = false;
		truebuffer = "";
		try {
			while (!pckEndReached && !force_exit) {
				fs->buffer = bridge + fs->buffer;
				if (terminatingSign.length() > 0 && (packEndIndex = fs->buffer.find(terminatingSign, 0)) >= 0) {
					int elength = packEndIndex + terminatingSign.length() - bridge.length();
					truebuffer += fs->buffer.substr(bridge.length(), elength);
					pckEndReached = true;
					goodPacket = true;
					terminatingSign = "";
					break;
				}
				if ((packStartIndex = (int) fs->buffer.find("{index:", packStartIndex)) >= 0) {
					if (!goodPacket) {
						ffl_err(FPL_FSTREAM_HEART, "packet %d corrupted.", index);
					}
					goodPacket = false;
					dindex = 7 + packStartIndex;
					index_str = "";
					int i = 0;
					while (fs->buffer[dindex] != ',' && i < 28) {
						index_str += fs->buffer[dindex];
						dindex++;
						i++;
					}
					index = (int) atof(index_str.c_str());
					if (fs->buffer[dindex] == ',' && index > 0) {
						terminatingSign = "endex:" + index_str + "}";
						if (terminatingSign.length() > 0 && (packEndIndex = fs->buffer.find(terminatingSign, packStartIndex)) >= 0) {
							int eindex = packEndIndex + terminatingSign.length();
							truebuffer = fs->buffer.substr(packStartIndex, eindex);
							packStartIndex += eindex + 1;
							pckEndReached = true;
							goodPacket = true;
							terminatingSign = "";
							break;
						} else {
							truebuffer = fs->buffer.substr(packStartIndex, fs->buffer.length());
						}
					} else {
						packStartIndex += 9;
					}
				} else if (terminatingSign.length() > 0) {
					truebuffer += fs->buffer.substr(bridge.length(), fs->buffer.length()); //truebuffer should be assigned prior to bridge
				}
				if (terminatingSign.length() > 0) {
					bridge = fs->buffer.substr(fs->buffer.length() - terminatingSign.length() + 1, fs->buffer.length());
				}

				*fs->source >> fs->buffer;
				packStartIndex = 0;
			}
			try {
				FFJSON* media_pack = new FFJSON(truebuffer);
				if (media_pack->isType(FFJSON::OBJ_TYPE::OBJECT) && &(*media_pack)["ferryframes"] != NULL) {
					vector<FFJSON*>* frames = (*media_pack)["ferryframes"].val.array;
					vector<FFJSON*>* sizes = (*media_pack)["framesizes"].val.array;
					char* ferrymp3 = (*media_pack)["ferrymp3"].val.string;
					int i = frames->size();
					const char* frame;
					string fn_b(ferrymedia_store_dir + string(itoa((*media_pack)["index"].val.number)));
					string fn;
					int fd;
					while (i > 0) {
						i--;
						frame = (const char*) (*frames)[i];
						if ((*frames)[i]->size != (int) (*(*sizes)[i])) {
							ffl_err(FPL_FSTREAM_HEART, "FerryStream: %s: packetNo: %s frameNo: %d frame size changed. Sent frame length :%d; Received frame length:%d.", fs->path.c_str(), fn_b.c_str(), i, (int) (*(*sizes)[i]), (*frames)[i]->size);
						}
						fn = fn_b + "-" + string(itoa(i)) + ".jpeg";
						fd = open(fn.c_str(), O_WRONLY | O_TRUNC | O_CREAT);
						int s;
						s = write(fd, frame, (*frames)[i]->size);
						close(fd);
						if (s >= 0) {
							ffl_debug(FPL_FSTREAM_HEART, "%s: frame %s successfully written", fs->path.c_str(), fn.c_str());
						} else {
							ffl_err(FPL_FSTREAM_HEART, "%s: frame %s save failed", fs->path.c_str(), fn.c_str());
						}
					}
					ofstream mp3segment(fn_b + ".mp3", std::ios_base::out | std::ios_base::binary);
					mp3segment.write(ferrymp3, (*media_pack)["ferrymp3"].size);
					mp3segment.close();
					//(*media_pack)["ferryframes"].setEFlag(B64ENCODE);
					//(*media_pack)["ferrymp3"].setEFLAG(B64ENCODE);
					fs->packetStrBuffer[(*media_pack)["index"]] = media_pack->stringify();
					packs_to_send[fs->path] = true;
					new_pck_chk = true;
					if (fs->packetBuffer.size() >= fs->packetBufferSize) {
						fs->packetStrBuffer.erase((int) (**fs->packetBuffer.begin())["index"]);
						delete (*fs->packetBuffer.begin());
						fs->packetBuffer.pop_front();
					}
					fs->packetBuffer.push_back(media_pack);
				} else {
					delete media_pack;
					fs->packetBuffer.pop_back();
				}
			} catch (FFJSON::Exception e) {
				ffl_err(FPL_FPORT, "Illegal meadia pack received on %s", fs->path.c_str());
				ffl_debug(FPL_FPORT, "FFJSON::Exception: %s", e.what());
			}

		} catch (SocketException e) {
			ffl_err(FPL_FPORT, "I, %s dying! Connection to %s lost.", fs->path.c_str(), fs->source->getDestinationIP().c_str());
			fs->funeral(fs-> path);
			delete fs;
			break;
		}

	}
}