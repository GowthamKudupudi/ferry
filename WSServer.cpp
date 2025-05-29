/*
 * Gowtham Kudupudi 2018/07/26
 * ©FerryFair
 */

//#if defined(LWS_USE_POLARSSL)
//#else
//#if defined(LWS_USE_MBEDTLS)
//#else
//#if defined(LWS_OPENSSL_SUPPORT) && defined(LWS_HAVE_SSL_CTX_set1_param)
///* location of the certificate revocation list */
//extern char crl_path[1024];
//#endif
//#endif
//#endif

#include "global.h"
#include "WSServer.h"
#include "FerryStream.h"
#include "cap.h"
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <getopt.h>
#include <string.h>
#include <math.h>
#include <sys/time.h>
#include <assert.h>
#ifdef WIN32
#else
#include <syslog.h>
#endif
#include <signal.h>
#include <map>
#include <vector>
#include <iostream>
#include <string>
#include <filesystem>
#include <sys/stat.h>
#include <set>
#include <ferrybase/JPEGImage.h>
#include <FFJSON.h>
#include <logger.h>
#include <ferrybase/mystdlib.h>
#include <ferrybase/myconverters.h>
#include <ferrybase/metaphone3.h>
#include <iostream>
#include <malloc.h>
#include <functional>
#include <chrono>
#include <deque>

typedef const char* ccp;
using namespace std;
using namespace std::placeholders;

enum {EHLO, STARTTLS, STARTTLS_WAIT, AUTH, FROM, TO, DATA, BODY, QUIT, END};

struct mg_mgr mgr, mail_mgr;

const char* mail_server = "tcp://localhost:25";
const char* admin = "Necktwi";
const char* admin_pass = "tornshoes";
const char* to = nullptr;
const char* from = "FerryFair";
const char* plaintxtHdr = "content-type: text/plain\r\n";
char subj[64];
char mesg[128];

bool s_quit = false;
bool sendMail = false;

const uint thnsPrSrch = 25;
QuadHldr thnsTree;
Metaphone3Encoder m3e;
map<string, FFJSON*>* nameints;
FFJSON* fnameints=nullptr;
vector<map<QuadNode*, uint>> qpmapvec;
set<void*> setffset;

struct UintName {
   vector<uint> vu;
   vector<string> mwd;
};
struct CompNameWt {
   bool operator () (const map<string, FFJSON*>::iterator it1,
                     const map<string, FFJSON*>::iterator it2) const {
      return it1->second->val.number > it2->second->val.number;
   }
} cmpNmWt;
map<const string*, uint> mitpos;

vector<string> metaname (string name) {
   vector<string> r = explode(name);
   for (int k=0;k<r.size();++k) {
      r[k]= m3e.encode(r[k]).first;   
   }
   return r;
}

vector<uint> nametouint (vector<string>& mstr) {
   uint bitCode=0;
   vector<uint> r;
   for (int k=0;k<mstr.size();++k) {
      map<string,FFJSON*>::iterator it = nameints->find(mstr[k]);
      int d=0;   
      if (it==nameints->end()) {
         d=nameints->size();
      } else {
         d = (int)mitpos[&it->first];
      }
      div_t bi = div(d,(8*sizeof(uint)));
      for (int i=r.size();i<=bi.quot;++i) {
         if (i>=qpmapvec.size()) {
            qpmapvec.push_back(map<QuadNode*, uint>());
         }
         r.push_back(0);
      }
      bitCode=1<<bi.rem;
      r[bi.quot]|=bitCode;
   }
   return r;
}

template<typename T, typename U>
void* fpxor (T* a, U* b, int8_t ind) {
   size_t t = (size_t)(((size_t)a xor (size_t)b));
   int8_t* c = (int8_t*)&t;
   if (c[7]!=0) {
      printf("error: msbs are not equal\n");
   }
   c[7]=ind;
   return (void*)t;
}

template<typename T, typename U>
tuple<void*, int8_t> bpxor (T* a, U* b) {
   int8_t n = ((int8_t*)&a)[7];
   ((int8_t*)&a)[7]=0;
   void* m = (void*)(((size_t)a) xor (size_t)b);
   return tuple<void*, int8_t>{m, n};
}

tuple<void*,int8_t> getNode (NdNPrn n) {
   return bpxor(n.qh->qp, n.prn);
}

static void parseHTTPHeader (const char* uri, size_t len,
                             FFJSON& sessionData) {
   unsigned int i=0;
   unsigned int pairStartPin=i;
   while(uri[i]!='\0') {
      if(uri[i]=='\n') {
         if(uri[i-1]=='\n' || (uri[i-1]=='\r' && uri[i-2]=='\n')) {
            if ((bool)sessionData["content-type"] &&
               strstr((ccp)sessionData["content-type"],"text")) {
               sessionData["content"]=string(uri+i+1, len-i+1);
            }
            return;
         };
         
         int k=i;
         if (uri[i-1]=='\r') k=i-1;
         if (pairStartPin==0) {
            int j=pairStartPin;
            while (uri[j]!=' ' && j<k) {
               ++j;
            }
            if (j==k) goto line_done;
            sessionData["path"]=string(uri,j);
            ++j;
            sessionData["version"]=string(uri+j,k-j);
         } else {
            int j=pairStartPin;
            while (uri[pairStartPin]==' ') ++pairStartPin;
            while (uri[j]!=':' && j<k) ++j;
            if (j==k) goto line_done;
            int keySize = j-pairStartPin;
            ++j;
            ++j;
            string keyStr(uri+pairStartPin,keySize);
            strLower(keyStr);
            sessionData[keyStr] = string(uri+j,k-j);
            printf("%s: %.*s\n", keyStr.c_str(), k-j, uri+j);
         }
line_done:
         pairStartPin=i+1;
      }
      ++i;
   }
}

string get_subdomain (const char* host) {
   string hoststr(host);
   if(!config["hostName"]) return string();
   int domainpos =
      hoststr.find(tolower(string((ccp)config["hostName"])).c_str());
   int portpos=hoststr.find(":");
   if (domainpos > 1)
      return hoststr.substr(0, domainpos-1);
   else
      return portpos>1?hoststr.substr(0,portpos):hoststr;
}

void get_data_in_url (const char* url, FFJSON& data) {
   unsigned i = 0;
   unsigned pairStartPin=i;
   string first,second;
   while (url[i]!='\0' && url[i]!='?') ++i;
   pairStartPin=++i;
   while (url[i]!='\0') {
      if(url[i]=='='){
         first=string(url+pairStartPin,i-pairStartPin);
         pairStartPin=i+1;
      } else if (url[i+1]=='&' || url[i+1]=='\0') {
         second=string(url+pairStartPin, i+1-pairStartPin);
         pairStartPin=i+2;
         data[first]=second;
      }
      ++i;
   }
}

void get_cookies (const char* c, FFJSON& fc) {
   unsigned i = 0;
   unsigned pairStartPin=i;
   string first,second;
   while (c[i]!='\0') {
      if(c[i]=='='){
         while(c[pairStartPin]==' ')++pairStartPin;
         first=string(c+pairStartPin,i-pairStartPin);
         pairStartPin=i+1;
      } else if (c[i+1]==';' || c[i+1]=='\0') {
         second=string(c+pairStartPin, i+1-pairStartPin);
         pairStartPin=i+2;
         fc[first]=second;
      }
      ++i;
   }
}

struct CompThingNameMatch {
   bool operator () (const tuple<FFJSON*,int8_t>& t1,
                     const tuple<FFJSON*,int8_t>& t2) const {
      return (get<1>(t1) < get<1>(t2));
   }
};
void mailfn (
   struct mg_connection *c, int ev, void *ev_data, void *fn_data
) {
   uint8_t *state = (uint8_t *) c->label;
   if (ev == MG_EV_OPEN) {
         // c->is_hexdumping = 1;
   } else if (ev == MG_EV_READ) {
      if (c->recv.len > 0 && c->recv.buf[c->recv.len - 1] == '\n') {
         MG_INFO(("<-- %d %s", (int) c->recv.len - 2, c->recv.buf));
         c->recv.len = 0;
         if (*state == EHLO) {
            mg_printf(c, "EHLO %s\r\n", getMachineName().c_str());
            *state = STARTTLS;
         } else if (*state == STARTTLS) {
            mg_printf(c, "STARTTLS\r\n");
            *state = STARTTLS_WAIT;
         } else if (*state == STARTTLS_WAIT) {
            struct mg_tls_opts opts =
               {.ca = "/etc/ssl/certs/ca-certificates.crt"};
            mg_tls_init(c, &opts);
            *state = AUTH;
         } else if (*state == AUTH) {
            char a[100], b[300] = "";
            size_t n = mg_snprintf(a, sizeof(a), "%c%s%c%s", 0, admin, 0,
                                   admin_pass);
            mg_base64_encode((uint8_t *) a, n, b);
            mg_printf(c, "AUTH PLAIN %s\r\n", b);
            *state = FROM;
         } else if (*state == FROM) {
            mg_printf(c, "MAIL FROM: <%s@%s>\r\n", admin,
                      (ccp)config["hostName"]);
            *state = TO;
         } else if (*state == TO) {
            mg_printf(c, "RCPT TO: <%s>\r\n", to);
            *state = DATA;
         } else if (*state == DATA)  {
            mg_printf(c, "DATA\r\n");
            *state = BODY;
         } else if (*state == BODY) {
            mg_printf(c, "From: %s <%s@%s>\r\n", from, admin,
                      (ccp)config["hostName"]);     // Mail header
            mg_printf(c, "Subject: %s\r\n", subj);          // Mail header
            mg_printf(c, "\r\n");                           // End of headers
            mg_printf(c, "%s\r\n", mesg);                   // Mail body
            mg_printf(c, ".\r\n");                          // End of body
            *state = QUIT;
         } else if (*state == QUIT) {
            mg_printf(c, "QUIT\r\n");
            *state = END;
         } else {
            c->is_draining = 1;
            MG_INFO(("end"));
         }
         MG_INFO(("--> %.*s", (int) c->send.len - 2, c->send.buf));
      }
   } else if (ev == MG_EV_CLOSE) {
      s_quit = true;
   } else if (ev == MG_EV_TLS_HS) {
      MG_INFO(("TLS handshake done! Sending EHLO again"));
      mg_printf(c, "EHLO %s\r\n", getMachineName().c_str());
   }
   (void) fn_data, (void) ev_data;
}

bool isValidThingName (FFJSON& tname) {
   if (tname.isType(FFJSON::STRING) && tname.size<=64) {
      ccp ctname = tname;
      for (uint i=0; i<tname.size; ++i) {
         char c = ctname[i];
         if (!((c>='a' && c<='z') || (c>='A' && c<='Z') || (c>='0' && c<='9')
               || (c=='-' || c==' ' || c=='.'))) {
            return false;
         }
      }
   }
   return true;
}

bool isValidThingDetails (FFJSON& tname) {
   if (tname.isType(FFJSON::STRING) && tname.size<=256) {
      ccp ctname = tname;
      for (uint i=0; i<tname.size; ++i) {
         char c = ctname[i];
         if (!((c>=' ' && c<='~') || c=='\n')) {
            return false;
         }
      }
      if (strstr(ctname, "<script")) {
         return false;
      }
   }
   return true;
}

bool isValidLocation (FFJSON& cloc) {
   if (cloc.isType(FFJSON::ARRAY) && cloc.size==2) {
      if (cloc[0].isType(FFJSON::NUMBER) && cloc[1].isType(FFJSON::NUMBER)) {
         return true;
      }
   }
   return false;
}
void ptswap (vector<NdNPrn>& pts, uint one, uint two) {
   NdNPrn temp = pts[one];
   pts[one]=pts[two];
   pts[two]=temp;
}
void quickSort (vector<NdNPrn>& pts, int start, int end) {
   if (end-start<=0) {
      return;
   } else if (end-start==1) {
      if (pts[start].ds>pts[end].ds) {
         ptswap(pts, start, end);
      }
   } else {
      float cmp = pts[start].ds;
      int lowind=start;
      int picker=start+1;
      while (picker<=end) {
         if (pts[picker].ds<cmp) {
            ptswap(pts, picker, lowind);
            ++lowind;
         }
         ++picker;
      }
      if (lowind-start >= 2) {
         quickSort(pts, start, lowind-1);
      }
      if (lowind==start) {
         ++lowind;
      }
      if (end-lowind>=1) {
         quickSort(pts, lowind, end);
      }
   }
}
void tls_ntls_common (
   struct mg_connection* c, int ev, void* ev_data, void* fn_data
) {
   struct mg_http_serve_opts opts = {
      .root_dir = (ccp)config["homeFolder"]
   };   // Serve local dir
   if (ev == MG_EV_HTTP_MSG) {
      unsigned char b[4];
      b[0] = c->rem.ip & 0xFF;
      b[1] = (c->rem.ip >> 8) & 0xFF;
      b[2] = (c->rem.ip >> 16) & 0xFF;
      b[3] = (c->rem.ip >> 24) & 0xFF;
      ffl_notice(FPL_HTTPSERV, "Remote IP: %d.%d.%d.%d-------------------",
                 b[0], b[1], b[2], b[3]);
      struct mg_http_message* hm = (struct mg_http_message*) ev_data;
      //ffl_notice(FPL_HTTPSERV, "hm->uri:\n%s", hm->uri.ptr);
      FFJSON sessionData, cookie;
      string subdomain;
      ccp referer=nullptr;char proto[8]="https"; int protolen;
      ccp username = nullptr, password = nullptr;
      ccp jsonHeader = "content-type: text/json\r\n";
      ccp headers = jsonHeader, path;
      string bid;
      parseHTTPHeader((ccp)hm->uri.ptr, strlen(hm->uri.ptr), sessionData);
      if (!sessionData["host"]) return;
      subdomain=get_subdomain(sessionData["host"]);
      ffl_notice(FPL_HTTPSERV, "subdomain: %s",subdomain.c_str());
      FFJSON& vhost = (bool)config["virtualWebHosts"][subdomain]?
         config["virtualWebHosts"][subdomain]:config;
      FFJSON& rbs=vhost["rbs"];
      FFJSON& users=vhost["users"];
      if (vhost["rootdir"])
         opts.root_dir=(ccp)vhost["rootdir"];
      if (sessionData["cookie"])get_cookies(sessionData["cookie"], cookie);
      ffl_notice(FPL_HTTPSERV, "cookie[bid]: %s",(ccp)cookie["bid"]);
      if (cookie["bid"]) {
         bid=(ccp)cookie["bid"];
      }
      auto now = chrono::system_clock::now();
      auto now_ms =
         chrono::time_point_cast<chrono::milliseconds>(now);
      long lepoch = now_ms.time_since_epoch().count();
      if (!sessionData["referer"]) goto nextproto;
      referer=sessionData["referer"];
      username = strstr(referer,":");
      protolen = username - referer;
      if (username==nullptr || protolen<0 || protolen>=8) {
         ffl_debug(FPL_HTTPSERV, "badproto");
         mg_http_reply(c, 200, headers, "badproto");
         goto done;
      }
      sprintf(proto,"%.*s",protolen,(ccp)sessionData["referer"]);
     nextproto:
      username=nullptr;
      ffl_debug(FPL_HTTPSERV, "proto: %s",proto);
      ffl_notice(FPL_HTTPSERV, "Serving: %s", opts.root_dir);
      path = sessionData["path"];
      if (!strcmp(path, "/cookie")) {
         //cookie
         ffl_notice(FPL_HTTPSERV, "cookie");
         FFJSON inmsg(string(hm->body.ptr, hm->body.len));
         if (bid.length())
            if(rbs[bid])
               goto gotbid;
        newbid:
         bid = random_alphnuma_string();
        bidcheck:
         if (rbs[bid]) {
            bid=random_alphnuma_string();
            goto bidcheck;
         }
         rbs[bid]["ip"]=c->rem.ip;
        gotbid:
         if ((uint32_t)rbs[bid]["ip"]!=c->rem.ip) {
            goto newbid;
         }
         rbs[bid]["ts"]=now;
         FFJSON reply;
         username = rbs[bid]["user"];
         if (username) {
            rbs[bid]["urts"]=lepoch;
            reply = users[(ccp)rbs[bid]["user"]];
         }
         reply["bid"]=bid;
         Pts pts;
         if (!inmsg["geoposition"].isType(FFJSON::UNDEFINED) &&
             inmsg["geoposition"].size==2
         ) {
            pts.c.x=(float)inmsg["geoposition"][1];
            pts.c.y=(float)inmsg["geoposition"][0];
            rbs[bid]["geoposition"] = inmsg["geoposition"];
         }
         //CompareByDistanceToCenter comp(C.x, C.y);
         //std::set<FFJSON*, CompareByDistanceToCenter> pts(comp);
         thnsTree.getPointsFromQuad(pts);
         int k=reply["things"].size;
         for (uint i = 0; i<pts.pts.size(); ++i) {
            NdNPrn& nd = pts.pts[i];
            FFJSON* f;
            if (nd.prn==(QuadNode*)-1) {
               f = (FFJSON*)nd.qh;
            } else {
               auto aa = getNode(nd);
               f = (FFJSON*)get<0>(aa);
            }
            ccp un = (*f)["user"]["name"];
            if (!username || strcmp(un,username)) {
               reply["things"][k]=f;
               ++k;
            }
         }
         mg_http_reply(c, 200, headers, "%s",
                       reply.stringify(true).c_str());
         rbs.save();
      } else if (!strcmp(path, "/login")) {
         //login
         ffl_notice(FPL_HTTPSERV, "Login");
         if (!bid.length() || !rbs[bid]) {
            mg_http_reply(c, 200, headers, "{%Q:%Q}", "error", "nobid");
            goto done;
         }
         FFJSON body(string(hm->body.ptr, hm->body.len));
         username=body["username"];password=body["password"];
         ffl_notice(FPL_HTTPSERV, "\nUser: %s\nPass: %s", username, password);
         if (!users[username]) {
            mg_http_reply(c, 200, headers, "{%Q:%s}", "login","false");
            goto done;
         }
         FFJSON& user=users[username];
         cout << "password:" << (ccp)user["password"] << endl;
         if (user["password"] && !user["inactive"] &&
             !strcmp(password,user["password"])
         ) {
            rbs[bid]["user"]=user["name"];
            rbs[bid]["ip"]=c->rem.ip;
            user["bid"]=bid;
            rbs[bid]["urts"]=lepoch;
            mg_http_reply(c, 200, headers, "%s",
                          user.stringify(true).c_str());
            rbs.save();
            users.save();
         } else {
            mg_http_reply(c, 200, headers, "{%Q:%s}", "login","false");
         }
      } else if (!strcmp(path, "/logout")) {
         //login
         ffl_notice(FPL_HTTPSERV, "Logout");
         if (!bid.length() || !rbs[bid]) {
            mg_http_reply(c, 200, headers, "{%Q:%Q}", "error", "nobid");
            goto done;
         }
         rbs[bid]["user"]=NULL;
         mg_http_reply(c, 200, headers, "{%Q:%s}", "logout","true");
         rbs.save();
      } else if (!strcmp(path, "/captcha")) {
         //login
         ffl_notice(FPL_HTTPSERV, "captcha");
         if (!bid.length() || !rbs[bid]) {
            mg_http_reply(c, 200, headers, "{%Q:%Q}", "error", "nobid");
            goto done;
         }
         string tempPath(string(opts.root_dir)+"/tmp/"+bid+".jpg");
         string randstr = random_alphnuma_string(7);
         cap randcap(randstr, tempPath, 7, 288, 68, 40, 80, 48);
         rbs[bid]["captcha"]=randstr;
         randcap.save();
         mg_http_reply(c, 200, headers, "{%Q:%s}", "cap","true");
         rbs.save();
      } else if (!strcmp(path, "/signup")) {
         //signup
         bool recovery=false;
         ffl_notice(FPL_HTTPSERV, "Signup");
         if (!bid.length() || !rbs[bid]) {
            mg_http_reply(c, 200, headers, "{%Q:%Q}", "error", "nobid");
            goto done;
         }
         FFJSON body(string(hm->body.ptr, hm->body.len));
         if (body["email"].isType(FFJSON::STRING)) {
            body["email"]=tolower(string((ccp)body["email"]));
         }
         if (body["username"]) {
            username=body["username"];
            ffl_debug(FPL_HTTPSERV, "User: %s\nPass: %s\nEmail: %s",
                      username, password, (ccp)body["email"]);
         } else if (body["email"]) {
            recovery=true;
            std::map<string,FFJSON*>* emln = users.val.pairs;
            if (emln->find(string((ccp)body["email"]))!=emln->end()) {
               FFJSON* ffemln = (*emln)[string((ccp)body["email"])];
               FFJSON::Link* link =
                  ffemln->getFeaturedMember(FFJSON::FM_LINK).link;
               username=(*link)[0].c_str();
               ffl_debug(FPL_HTTPSERV, "username: %s", username);
            } else {
               ffl_warn(FPL_HTTPSERV, "%s Email not registered.",
                        (ccp)body["email"]);
               mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent",
                             -5, "msg",
                             "Email not registered!");
               goto done;
            }
         }
         password=body["password"];
         FFJSON& user=users[username];
         if (!recovery && user &&
             (user["activationKey"] && !user["inactive"])) {
            ffl_warn(FPL_HTTPSERV, "User already exists.");
            mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent",
                          -1, "msg",
                          "Username already taken, choose an another :|");
            goto done;
         } else if (!recovery && user["inactive"] &&
                    strcmp(body["email"],user["email"])) {
            ffl_warn(FPL_HTTPSERV, "User exists; mail mismatch");
            mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent",
                          -2, "msg",
                          "Username already taken, choose an another :|");
            goto done;
         } else if (
            !recovery && user["password"] &&
            users[(ccp)body["email"]].val.fptr!=&users[username]
         ) {
            ffl_warn(FPL_HTTPSERV, "Email already registered.");
            mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent",
                          -3, "msg",
                          "Email already registered! Try resetting password");
            goto done;
         } else if (!recovery &&
                    !(validUsername(string(username)) && strlen(username)<=32)
         ) {
            ffl_warn(FPL_HTTPSERV, "Invalid password");
            mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent",
                          -6, "msg", "Invalid password X|");
            goto done;
         } else if (
            !(password!=nullptr && validMD5(string(password)))
         ) {
            ffl_warn(FPL_HTTPSERV, "Invalid password");
            mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent",
                          -6, "msg", "Invalid password X|");
            goto done;
         } else if (!body["captcha"] || !rbs[bid]["captcha"] ||
                    strcmp((ccp)body["captcha"],(ccp)rbs[bid]["captcha"])!=0
         ) {
            ffl_warn(FPL_HTTPSERV, "Captcha mismatch.");
            mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent",
                          -4, "msg", "Captcha mismatch, hmm!");
            goto done;
         } else if (!body["consent"]) {
            ffl_warn(FPL_HTTPSERV, "Captcha mismatch.");
            mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent",
                          -5, "msg",
                          "U didn't consent to this tool usage :/");
            goto done;
         }
         if (!recovery) {
            user["email"] = body["email"];
            user["password"] = password;
            users[(ccp)user["email"]].addLink(users,username);
            user["inactive"]=true;
            filesystem::path
               usrpth(string(opts.root_dir)+string("/upload/")+username);
            filesystem::create_directory(usrpth);
         } else {
            user["newpassword"] = password;
         }
         string actKey = random_alphnuma_string();
         user["activationKey"]=actKey;
         ffl_notice(FPL_HTTPSERV, "actKey: %s",actKey.c_str());
         to=user["email"];
         sprintf(subj, "User activation link");
         sprintf(mesg, "Open %s://%s/activate?user=%s&key=%s to activate "
                 "%s", proto, (ccp)sessionData["host"], username,
                 (ccp)user["activationKey"], username);
         mg_connect(&mail_mgr, mail_server, mailfn, NULL);
         while(!s_quit)
            mg_mgr_poll(&mail_mgr, 100);
         s_quit=false;
         mg_http_reply(c, 200, headers, "{%Q:%d,%Q:%Q}", "actEmailSent", 2,
                       "msg", "Activation mail sent to ur email :D");
         rbs.save();
         users.save();
      } else if (strstr(path, "/activate?")) {
         FFJSON data;
         get_data_in_url(path, data);
         username=data["user"];
         FFJSON& user=users[username];
         if ((!user["password"] || !user["inactive"]) &&
             !user["newpassword"]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "wrongKey" );
         } else if (!strcmp(user["activationKey"],data["key"])) {
            if (user["newpassword"]) {
               user["password"]=user["newpassword"];
               user["newpassword"]=false;
            }
            user["name"]=username;
            user["inactive"]=false;
            mg_http_reply(c, 200, headers, "%s activated.", username);
            users.save();
         } else {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "wrongKey" );
         }
      } else if (strstr(path, "/search")) {
         if (!bid.length() || !rbs[bid]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "nobid");
            goto done;
         }
         FFJSON body(string(hm->body.ptr, hm->body.len));
         if (body["search"].isType(FFJSON::UNDEFINED)) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "nosearch");
            goto done;
         }
         ccp srchStr = body["search"];
         Pts pts;
         vector<string> mstr = metaname(srchStr);
         pts.ina = nametouint(mstr);
         FFJSON reply;
         int k=0;
         if (!body["geoposition"].isType(FFJSON::UNDEFINED) &&
             body["geoposition"].size==2
         ) {
            pts.c.x=(float)body["geoposition"][1];
            pts.c.y=(float)body["geoposition"][0];
            rbs[bid]["geoposition"] = body["geoposition"];
         }
         CompThingNameMatch cTNM;
         multiset<tuple<FFJSON*, int8_t>, CompThingNameMatch> score(cTNM);
         thnsTree.getPointsFromQuad(pts);
         for (int i=0;i<pts.pts.size();++i) {
            NdNPrn& nd = pts.pts[i];
            FFJSON* f;
            if (nd.prn==(QuadNode*)-1) {
               f = (FFJSON*)nd.qh;
            } else {
               auto aa = getNode(nd);
               f = (FFJSON*)get<0>(aa);
            }
            score.insert({f,nd.d.x});
         }
         multiset<tuple<FFJSON*, int8_t>, CompThingNameMatch>::iterator it=
            score.begin();
         while (it!=score.end()) {
            reply["things"][k]=*(get<0>(*it));
            ++k;++it;
         }
         reply["things"][0];
         mg_http_reply(c, 200, headers, "%s",
                       reply.stringify(true).c_str());
      } else if (strstr(path, "/update")) {
         if (!bid.length() || !rbs[bid]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "nobid");
            goto done;
         }
         username=(ccp)rbs[bid]["user"];
         if (!sessionData["content"]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "No Content!");
            goto done;
         }
         FFJSON cntnt((ccp)sessionData["content"]);
         FFJSON& user = users[username];
         if (cntnt["things"]) {
            FFJSON& cthings = cntnt["things"];
            FFJSON& uthings = user["things"];
            bool newthing=false;
            int id = 0;
            if (!(bool)uthings) {
               uthings.init("[]");
            }
            int j=0;
            for (int i=0; i<cthings.size; ++i) {
               if (!cthings[i])
                  continue;
               if (i>uthings.size) {
                  mg_http_reply(c, 400, headers, "{%Q:%Q}",
                                "error", "sizeExceeded");
                  goto done;
               }
               FFJSON& cfname = cthings[i]["name"];
               string cname((ccp)cfname);
               if (!isValidThingName(cfname)) {
                  mg_http_reply(c, 400, headers, "{%Q:%Q}",
                                "error", "invalidThingName");
                  goto done;
               }
               while (j<uthings.size &&
                      (int)uthings[j]["id"]!=(int)cthings[i]["id"]) {
                  ++j;
               }
               bool locChanged = false;
               bool nameChanged = false;
               cthings[i].erase("user");
               vector<string> mstr;
               vector<uint> ina;
               if (!uthings[j]) {
                  uthings[j]["id"] = uthings.size?
                     ((int)uthings[j-1]["id"]) + 1 : 0;
                  FFJSON& ln = uthings[j]["user"].addLink(users, username);
                  if (!ln)
                     delete &ln;
                  nameChanged=true;
                  if (cthings[i]["location"]) {
                     locChanged=true;
                  }
               } else {
                  string uname(uthings[j]["name"]?(ccp)uthings[j]["name"]:"");
                  strLower(cname);
                  strLower(uname);
                  if (strcmp(cname.c_str(),uname.c_str())) {
                     mstr = metaname(uname);
                     for (int k=0; k<mstr.size(); ++k) {
                        map<string, FFJSON*>::iterator it =
                           nameints->find(mstr[k]);
                        if (it->second->val.number==1) {
                           mitpos.erase(mitpos.find(&it->first));
                           fnameints->erase(mstr[k]);
                        } else {
                           --(*nameints)[mstr[k]]->val.number;
                        }
                     }
                     nameChanged=true;
                     uthings[j]["name"]=cthings[i]["name"];
                  }
                  FFJSON& cloc = cthings[i]["location"];
                  FFJSON& uloc = uthings[j]["location"];
                  if (!isValidLocation(cloc)) {
                     mg_http_reply(c, 400, headers, "{%Q:%Q}",
                                "error", "invalidLocation");
                     goto done;
                  }
                  if (!nameChanged && ((double)cloc[0]!=(double)uloc[0] ||
                      (double)cloc[1]!=(double)uloc[1])) {
                     mstr = metaname(uname);
                     locChanged=true;
                  }
                  if (nameChanged||locChanged) {
                     ina = nametouint(mstr);
                     thnsTree.insert(uthings[j], ina, true);
                  }
               }
               uthings[j]["location"]=cthings[i]["location"];
               if (cthings[i]["details"]) {
                  if (isValidThingDetails(cthings[i]["details"])) {
                     uthings[j]["details"]=cthings[i]["details"];
                  } else {
                     mg_http_reply(c, 400, headers, "{%Q:%Q}",
                                "error", "invalidThingDetails");
                     goto done;
                  }
               }
               if (nameChanged) {
                  mstr=metaname(cname);
                  for (int k=0; k<mstr.size(); ++k) {
                     map<string, FFJSON*>::iterator it =
                        nameints->find(mstr[k]);
                     if (it==nameints->end()) {
                        (*fnameints)[mstr[k]]=1;
                        it=nameints->find(mstr[k]);
                        mitpos[&it->first]=nameints->size()-1;
                     } else {
                        ++(*nameints)[mstr[k]]->val.number;
                     }
                  }
                  ina=nametouint(mstr);
               }
               if (locChanged||nameChanged) {
                  thnsTree.insert(uthings[j], ina);
               }
            }
         }
         mg_http_reply(c, 200, headers, "%s", user.stringify(true).c_str());
         vhost.save();
      } else if (strstr(path, "/owl")) {
         if (!bid.length() || !rbs[bid] || !rbs[bid]["user"]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error",
                          "nobidOrNotSignedIn");
            goto done;
         }
         username=(ccp)rbs[bid]["user"];
         if (!sessionData["content"]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "No Content!");
            goto done;
         }
         FFJSON cntnt((ccp)sessionData["content"]);
         FFJSON& user = users[username];
         FFJSON& things = user["things"];
         FFJSON& fQs = cntnt["Qs"];
         FFJSON::Iterator it;
         long urts;
         long lmts;
         if (!fQs) {
            goto rqs;
         }
         it = fQs.begin();
         while (it!=fQs.end()) {
            ccp tuser = (ccp)it;
            if (!strcmp(tuser,username)) {
               mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "yay!");
               goto done;
            }
            FFJSON::Iterator tit;
            if (tuser) {
               tit  = users.find(tuser);
            }
            if (!tuser || tit==users.end()) {
               mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "yay!");
               goto done;
            }
            FFJSON& tfuser = users[tuser];
            FFJSON& tfthings = tfuser["things"];
            tit = it->begin();
            while (tit!=it->end()) {
               ccp ctid = (ccp)tit;
               if (!ctid) {
                  mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "yay!");
                  goto done;
               }
               int tid = atoi(ctid);
               int tind = 0;
               int last = tfthings.size;
               last = tid<last?tid:last;
               for (int i=last-1;i>=0;++i) {
                  if ((int)tfthings[i]["id"]==tid) {
                     tind=i;
                     break;
                  }
               }
               if (tind<0 || tind>=last) {
                  mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "yay!");
                  goto done;
               }
               FFJSON& rmsgs = tfthings[tind]["rmsgs"];
               if (!rmsgs) {
                  rmsgs.init("[]");
               }
               FFJSON& smsgs = users[username]["smsgs"];
               if (!smsgs) {
                  smsgs.init("[]");
               }
               int rmind=rmsgs.size;
               int smind=smsgs.size;
               int rmid=1;
               if (rmind) {
                  rmid = (int)rmsgs[rmind-1]["id"]+1;
               }
               rmsgs[rmind]["id"]=rmid;
               rmsgs[rmind]["user"]=username;
               rmsgs[rmind]["msg"]=*tit;
               rmsgs[rmind]["ts"]=lepoch;
               rmsgs[rmind]["new"]=true;
               smsgs[smind]["user"]=tuser;
               smsgs[smind]["tid"]=tid;
               smsgs[smind]["mid"]=rmid;
               *tit=rmid;
               ++tit;
            }
            tfuser["lmts"]=lepoch;
            ++it;
         }
         cntnt["status"]=1;
        rqs:
         FFJSON& fRs = cntnt["Rs"];
         if (!fRs) {
            goto news;
         }
         it = fRs.begin();
         while (it!=fRs.end()) {
            ccp ctid = (ccp)it;
            if (!ctid) {
               mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "yay!");
               goto done;
            }
            int tid = atoi(ctid);
            int tind = 0;
            int last = things.size;
            last = tid<last?tid:last;
            for (int i=last-1;i>=0;++i) {
               if ((int)things[i]["id"]==tid) {
                  tind=i;
                  break;
               }
            }
            if (tind<0 || tind>=last) {
               mg_http_reply(c, 400, headers, "{%Q:%Q}", "error",
                             "yay!");
               goto done;                     
            }
            FFJSON& rmsgs = things[tind]["rmsgs"];
            FFJSON::Iterator tit = it->begin();
            while (tit!=it->end()) {
               int mid = (int)*tit;
               last = rmsgs.size;
               last = mid<last?mid:last;
               for (int i=last-1;i>=0;++i) {
                  if ((int)rmsgs[i]["id"]==mid) {
                     mid=i;
                     break;
                  }
               }
               if (mid>=last || mid<0) {
                  mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "yay!");
                  goto done;
               }
               rmsgs[mid].erase("new");
               ++tit;
            }
            ++it;
         }
         cntnt["status"]=1;
        news:
         if (!user["lmts"]) {
            goto owldone;
         }
         urts = (long)rbs[bid]["urts"];
         lmts = (long)user["lmts"];
         if (urts>lmts) {
            goto owldone;
         }
         for (int i=0; i<things.size; ++i) {
            FFJSON& rmsgs = things[i]["rmsgs"];
            for (int j=0;j<rmsgs.size;++j) {
               FFJSON& msg = rmsgs[j];
               long mts = (long)msg["ts"];
               if (mts<urts) {
                  continue;
               }
               cntnt["news"][to_string((int)things[i]["id"])]
                  [to_string(j)]=msg;
            }
         }
         rbs[bid]["urts"]=lepoch;
         cntnt["status"]=1;
     owldone:
         cntnt["status"]=1;
         mg_http_reply(c, 200, headers, "%s", cntnt.stringify(true).c_str());
         users.save();
         rbs.save();
      } else if (strstr(path, "/upload?")) {
         //upload?
         if (!bid.length() || !rbs[bid] || !rbs[bid]["user"]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error",
                          "nobidOrNotSignedIn");
            goto done;
         }
         username=(ccp)rbs[bid]["user"];
         FFJSON& user=users[username];
         int maxThings = (bool)user["maxThings"]?
            user["maxThings"]:vhost["config"]["defaultMaxThings"];
         int maxThingPics = (bool)user["maxThings"]?
            user["maxThings"]:vhost["config"]["defaultMaxThingPics"];
         FFJSON data;
         get_data_in_url(path, data);
         int thingId = atoi((ccp)data["thingId"]);
         int picId = atoi((ccp)data["picId"]);
         int fofst = atoi((ccp)data["offset"]);
         int chnkSz= atoi((ccp)data["chunkSize"]);
         int ttlSz = atoi((ccp)data["totalSize"]);
         int thngi = -1;
         if (fofst!=0) {
            FFJSON& ptgs=user["pendingThings"];
            if(thingId!=(int)ptgs["thingId"]){
               mg_http_reply(c, 400, headers, "{%Q:%Q}", "error",
                             "noSuchThingId" );
               goto done;                  
            };
            picId=ptgs["picId"];
            thngi=ptgs["thngi"];
            goto gotThingId;
         }
         if (thingId < 0) {
            if ((bool)user["things"] && user["things"].size>=maxThings) {
               ffl_notice (
                  FPL_HTTPSERV,
                  "user[\"things\"].size: %d", user["things"].size
               );
               mg_http_reply(c, 400, headers, "{%Q:%Q}", "error",
                             "thingsAreAtMax" );
               goto done;
            }
            if ((bool)user["things"] && user["things"].size) {
               thngi=user["things"].size;
               thingId=(int)user["things"][thngi-1]["id"]+1;
            } else {
               thngi=0;
               thingId=0;
            }
         } else {
            int tSize = user["things"].size-1;
            for (int i=(thingId<tSize?thingId:tSize); i>=0; --i) {
               if ((int)user["things"][i]["id"]==thingId) {
                  thngi=i;
                  break;
               }
            }
            if (thngi<0) {
               mg_http_reply(c, 400, headers, "{%Q:%Q}", "error",
                             "noSuchThingId" );
               goto done;
            }
         }
        gotThingId:
//         ffl_notice(FPL_HTTPSERV, "Serving: %s", opts.root_dir);
         ffl_notice (
            FPL_HTTPSERV,
            "picId: %d, maxThingPics: %d, thingId: %d, thngi: %d",
            picId, maxThingPics, thingId, thngi
         );
         if (picId >= maxThingPics) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error",
                          "picsAreAtMax" );
            goto done;
         }
         string upldpth(opts.root_dir);
         upldpth += "/upload/";
         upldpth += username;
         upldpth += "/";
         upldpth += to_string(thingId);
         upldpth += ".";
         upldpth += to_string(picId);
         upldpth +=".jpg";
         ffl_notice(FPL_HTTPSERV, "receiving: %s", upldpth.c_str());
         if (fofst==0) {
            FFJSON& uts = user["things"];
            uts[thngi]["id"]=thingId;
            if (!uts[thngi]["user"]) {
               uts[thngi]["user"].addLink(users, username);
            }
            FFJSON& ups = uts[thngi]["pics"];
            ups[picId]["partial"] = true;
            if (fofst+chnkSz<ttlSz) {
               FFJSON& ptgs=user["pendingThings"];
               ptgs["thingId"]=thingId;
               ptgs["picId"]=picId;
               ptgs["thngi"]=thngi;
            }
         }
         char msg[30];
         sprintf(msg, "{\"thingId\":%d,\"picId\":%d", thingId, picId);
         mg_http_upload(
            c, hm, &mg_fs_posix, upldpth.c_str(), 2999999, msg);
         if (fofst+chnkSz >= ttlSz) {
            user.erase("pendingThings");
            user["things"][thngi]["pics"][picId].erase("partial");
            user["things"][thngi]["pics"][picId]["ts"]=lepoch;
            printf("pendingThings\n");
            users.save();
         }
      } else {
         if (strstr(path, "/upload") || //restricted directories
            strstr(path, "/tmp")
         ) {
            if (!bid.length() || !rbs[bid]) {
               mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "nobid");
               goto done;
            }
         }
         mg_http_serve_dir(c, (mg_http_message*)ev_data, &opts);
      }
     done: 
      if (valgrind_test && !--valgrind_count)
         force_exit=true;
   }
}

void fn (
   struct mg_connection *c, int ev, void *ev_data, void *fn_data
) {
   tls_ntls_common(c, ev, ev_data, fn_data);
}

void fn_tls (
   struct mg_connection *c, int ev, void *ev_data, void *fn_data
) {
   if (ev == MG_EV_ACCEPT) {
      struct mg_tls_opts opts = {
//         .cert = "/etc/letsencrypt/live/ferryfair.com/cert.pem",
//         .certkey = "/etc/letsencrypt/live/ferryfair.com/privkey.pem"
         .cert = "/etc/letsencrypt/live/ferryfair.com/signed_chain.crt",
         .certkey = "/etc/letsencrypt/live/ferryfair.com/domain.key"
      };
      mg_tls_init(c, &opts);
   }
   tls_ntls_common(c, ev, ev_data, fn_data);
}

//set<void*> qpset;
void QuadNode::seti (vector<uint>& ina) {
   for (int i=0; i<ina.size();++i) {
      if (ina[i]!=0) {
         uint& lni = qpmapvec[i][this];
         lni |= ina[i];
         // if (i==0 && lni&0x80) {
         //    printf("apple set\n");
         // }
      }
   }
}
vector<map<QuadNode*,uint>::iterator> qpfind (QuadNode* qp) {
   vector<map<QuadNode*, uint>::iterator> vit;
   map<QuadNode*, uint>::iterator it;
   for (int i=0; i<qpmapvec.size();++i) {
      it = qpmapvec[i].find(qp);
      if (it!=qpmapvec[i].end()) {
         for (int j=vit.size(); j<i;++j) {
            vit.push_back(qpmapvec[j].end());
         }
         vit.push_back(it);
      }
   }
   return vit;
}

bool isQuad (map<QuadNode*,uint>::iterator it) {
   return qpmapvec[0].end()!=it;
}
QuadNode* QuadHldr::qn () {
   QuadNode* r=nullptr;
   for (int i=0; i<qpmapvec.size(); ++i) {
      r = qpmapvec[i].upper_bound((QuadNode*)(this-4))->first;
      if (this-(QuadHldr*)r <4) {
         return r;
      }
   }
   return r;
}
uint QuadNode::hasName (vector<uint>& ina,
                        vector<map<QuadNode*,uint>::iterator> vit) {
   if (!ina.size()) {
      return -1;
   }
   uint count=vit.size();
   uint size = ina.size();
   size = size<count? size:count;
   count = 0;
   for (int i=0;i<size;++i) {
      count += countSetBits(ina[i] & vit[i]->second);
   }
   return count;
}

vector<uint> qpIna (vector<map<QuadNode*,uint>::iterator> vit) {
   vector<uint> ina;
   for (int i=0;i<vit.size();++i) {
      ina.push_back(vit[i]->second);
   }
   return ina;
}

uint ffHasName (FFJSON& ff, vector<uint>& ina) {
   if (!ina.size()) {
      return -1;
   }
   vector<string> mstr = metaname((ccp)ff["name"]);
   vector<uint> nina = nametouint(mstr);
   uint count=0;
   for (int i=0;i<ina.size();++i) {
      count += countSetBits(ina[i] & nina[i]);
   }
   return count;
}

vector<uint> QuadHldr::getIntNames (QuadNode* tQN, int8_t tind,
                                    QuadNode* pQN, int8_t ind) {
   vector<uint> r;
   if (!fp) {
      return r;
   }
   QuadNode* resfp = (QuadNode*)get<0>(bpxor(fp, pQN));
   uint a=0;
   for (uint i=0; i<qpmapvec.size(); ++i) {
      map<QuadNode*, uint>::iterator it = qpmapvec[i].find(resfp);
      if (it!=qpmapvec[i].end()) {
         r.push_back(it->second);
         a|=it->second;
      } else {
         r.push_back(0);
      }
   }
   if (!a) {
      vector<string> mstr = metaname((ccp)(*(FFJSON*)resfp)["name"]);
      r=nametouint(mstr);
   }
   return r;
}

void QuadNode::updateIntNames (QuadNode* tQN, int8_t tind,
                               QuadNode* pQN, int8_t ind) {
   QuadHldr* qh = (QuadHldr*)this;
   vector<uint> r;
   for (int8_t i=0;i<4;++i,++qh) {
      vector<uint> lr = qh->getIntNames(tQN, tind, pQN, ind);
      for (int8_t j=0;j<lr.size();++j) {
         if (j==lr.size()) {
            r.push_back(0);
         }
         uint& ui = r[j];
         ui|=lr[j];
      }
   }
   seti(r);
}

QuadNode::~QuadNode () {
   map<QuadNode*,uint>::iterator qit = qpmapvec[0].find(this);
   if (qit!=qpmapvec[0].end()) {
      qpmapvec[0].erase(qit);
   }
}

uint QuadHldr::insert (FFJSON& rF, vector<uint>& ina, bool deleteLeaf,
                       uint level, float x, float y, QuadNode* tQN, int8_t tind,
                       QuadNode* pQN, int8_t ind, int8_t sn) {
   //printf("x,y: %lf,%lf\n", x, y);
   if (fp==nullptr || (void*)fp==pQN) {
      fp = (FFJSON*)fpxor(&rF, pQN, ind);
      //printf("rF:%p,%s inserted\n", &rF,rF["location"].stringify().c_str());
      return level;
   }
   void* resfp = get<0>(bpxor(fp, pQN));
   set<FFJSON*>* ressfp = (set<FFJSON*>*)resfp;
   set<void*>::iterator sit = setffset.find(resfp);
   vector<map<QuadNode*,uint>::iterator> qit = qpfind((QuadNode*)resfp);
   if (!qit.size()) {
      bool isS=sit != setffset.end();
      if (deleteLeaf) {
         if (!isS && resfp == (void*)&rF) {
            fp=nullptr;
            return 1;
         } else if (isS) {
            set<FFJSON*>::iterator it = ressfp->find(&rF);
            if (it!=ressfp->end()) {
               ressfp->erase(it);
            }
            if (!ressfp->size()) {
               delete ressfp;
               fp=nullptr;
               return 1;
            } else {
               return 0;
            }
         }
         return 0;
      }
      if (resfp == (void*)&rF)
         return level;
      FFJSON* tfp = isS ? *ressfp->begin() : (FFJSON*)resfp;
      //printf("tfp: %p,%p,%p\n",tfp, fp, pQN);
      if ((float)(*tfp)["location"][0]==(float)rF["location"][0] &&
          (float)(*tfp)["location"][1]==(float)rF["location"][1]) {
         if (!isS) {
            sp = new set<FFJSON*>();
            setffset.insert(sp);
            sp->insert(tfp);
            sp->insert(&rF);
            sp=(set<FFJSON*>*)fpxor(sp, pQN, ind);
         } else {
            ressfp->insert(&rF);
         }
         return level;
      }
      FFJSON& tmp = *tfp;
      qp = new QuadNode();
      if (!sn) {
         vector<string> mstr = metaname((ccp)(*(FFJSON*)resfp)["name"]);
         vector<uint> nina = nametouint(mstr);
         for (int i=0; i<nina.size();++i) {
            if (ina.size()<=i) {
               ina.push_back(nina[i]);
            } else {
               ina[i]=ina[i]|nina[i];
            }
         }
      }
      qp->seti(ina);
      FFJSON* xorfp = (FFJSON*)fpxor(resfp,tQN,tind);
      //printf("%p,%p\n", resfp, xorfp);
      if ((double)tmp["location"][1] >= x) {
         if ((double)tmp["location"][0] >= y) {
            qp->en.fp=xorfp;
         } else {
            qp->es.fp=xorfp;
         }
      } else {
         if ((double)tmp["location"][0] >= y) {
            qp->wn.fp=xorfp;
         } else {
            qp->ws.fp=xorfp;
         }
      }
      //printf("qp: %p, tQN: %p\n", qp, tQN);
      qp = (QuadNode*)fpxor(qp,pQN,ind);
      return insert(rF,ina,deleteLeaf,level,x, y, tQN, tind, pQN, ind, 1);
   } else {
      float dx = 180/(pow(2,level+1));
      float dy = 90/(pow(2,level+1));
      FFJSON* pxorrf = (FFJSON*)fpxor(&rF,tQN,tind);
      QuadNode* qpres = (QuadNode*)resfp;
      QuadHldr* qh = (QuadHldr*)qpres;
      uint returnv=0;
      if (!sn)
         qpres->seti(ina);
      float lx = rF["location"][1];
      float ly = rF["location"][0];
      int8_t qind =  lx>= x?0:1;
      int8_t xs=qind==0?1:-1;
      qind<<=1;
      int8_t ys = ly >= y?1:-1;
      fflush(stdout);
      qind |= ys>=0?0:1;
      //printf("%f,%f,%f,%f,%d,%d,%d\n",x,y,lx,ly,xs,ys,qind);
      qh+=qind;
      if (qh->fp==nullptr) {
         qh->fp=pxorrf;
      } else {
         returnv = qh->insert(
            rF, ina, deleteLeaf, level+1, x+xs*dx, y+ys*dy, qpres, qind, tQN, tind, sn);
      }
      if (deleteLeaf) {
         if (returnv) {
            qh = (QuadHldr*)qpres;
            if (returnv>1) {
               qh = &qpres->en;
               qind = 0;
               xs=0;
               for (;qind<4;++qind,++qh) {
                  if (qh->fp!=nullptr) {
                     ++xs;
                     if (xs>1) {
                        return 1;
                     }
                     pxorrf=(FFJSON*)qh;
                  }
               }
               if (xs) {
                  qh=(QuadHldr*)pxorrf;
                  pxorrf=(FFJSON*)get<0>(bpxor(qh->fp, tQN));
                  delete qpres;
                  qp = (QuadNode*)fpxor(pxorrf, pQN,ind);
               } else {
                  delete qpres;
                  qp=nullptr;
                  return 2;
               }
            }
            qpres->updateIntNames(tQN,tind,pQN,ind);
         }
         return 0;
      }
      //printf("rF: %p,%s:%p:%p inserted\n", &rF,
      //       rF["location"].stringify().c_str(),
      //       pQN,pxorrf);
   }
   return level;
}
bool Circle::grabIfNearest (FFJSON& f) {
   if (!nf) {
      nf=&f;
      return true;
   }
   float x1 = x - (double)f["location"][1];
   float y1 = y - (double)f["location"][0];
   float x2 = x - (double)(*nf)["location"][1];
   float y2 = y - (double)(*nf)["location"][0];
   //x1 = x1<0?-x1:x1;
   //y1 = y1<0?-y1:y1;
   //x2 = x2<0?-x2:x2;
   //y2 = y2<0?-y2:y2;
   if ((pow(x1,2)+pow(y1,2)) < (pow(x2,2)+pow(y2,2))) {
      //if (x1+y1 < x2+y2) {
      nf=&f;
      return true;
   }
   return false;
}
void QuadHldr::print (Circle& c, uint level, QuadNode* tQN, int8_t tind,
                      QuadNode* pQN, int8_t ind) {
   if (fp==nullptr) {
      printf("%.*s%d: %p(%p)\n", level,"|||||||||||||||||||||||||||||||||||||||||",
             tind, this, nullptr);
      return;
   }
   QuadNode* resqp = (QuadNode*)get<0>(bpxor(fp,pQN));
   vector<map<QuadNode*,uint>::iterator> qit = qpfind((QuadNode*)resqp);
   if (!qit.size()) {
      set<void*>::iterator sit = setffset.find(resqp);
      bool isS=sit != setffset.end();
      if (isS) {
         set<FFJSON*>& sf = *(set<FFJSON*>*)*sit;
         resqp=(QuadNode*)*sf.begin();
      }
      FFJSON& f = *(FFJSON*)resqp;
      if (c.nf!=(FFJSON*)1)
         c.grabIfNearest(f);
      printf("%.*s%d: %p(%p(%d))%s\n",level,
             "||||||||||||||||||||||||||||||||||||||||||||||||||||||||||",
             tind, this, &f, isS, f["location"].stringify().c_str());
   } else {
      QuadHldr* qh = (QuadHldr*)resqp;
      printf("%.*s%d: %p(%p)\n", level,"|||||||||||||||||||||||||||||||||||||||||",
             tind, this, qh);
      for (int i=0;i<4;++i,++qh) {
         qh->print(c, level+1, resqp,i,tQN,tind);
      }
   }
}

void NdNPrn::print () const {
   auto aa = getNode(*this);
   printf ("%p((%d)%p),%d,%d-%f\n", qh, qh->fp!=nullptr, get<0>(aa), d.x,
           d.y,ds);
   fflush(stdout);
}
void printpts (const vector<NdNPrn>& pts) {
   for (int i=0; i<pts.size(); ++i) {
      const NdNPrn& nd = pts[i];
      nd.print();
   }
}

QuadNode::QuadNode () {
   en.fp=nullptr;
   es.fp=nullptr;
   wn.fp=nullptr;
   ws.fp=nullptr;
}

int QuadHldr::addThis (Pts& pts, Direction d, float dx, float ds,
                        QuadNode* pQN , int8_t ind, int noChk) {
   NdNPrn n = {this, pQN, dx, ds, d, ind};
   bool there = false;
   if (noChk==1) {
      pts.pts.push_back(n);
      return 1;
   }
   for (int i=pts.pts.size()-1; i>pts.ni; --i) {
      if (pts.pts[i].qh == this) {
         NdNPrn nd = pts.pts[i];
         if (noChk==-1 && nd.d.x==d.x and nd.d.y==d.y) {
            return -1;
         }
         NdNPrn nd2;
         if (pts.pts[i-1].qh == this) {
            nd2 = pts.pts[i-1];
         }
         pts.pts.erase(
            nd2.qh?pts.pts.begin()+(i-1):pts.pts.begin()+i, pts.pts.begin()+i+1);
         if ((d.x!=0 && nd.d.x==-d.x) || (d.y!=0 && nd.d.y==-d.y)) {
            pts.pts.push_back(nd);
            if (nd2.qh) {
               pts.pts.push_back(nd2);
            } else {
               pts.pts.push_back(n);   
            }
            return 1;
         } else if (nd2.qh && ((d.x!=0 && nd2.d.x==-d.x) ||
                               (d.y!=0 && nd2.d.y==-d.y))) {
            pts.pts.push_back(nd2);
            pts.pts.push_back(n);   
            return 1;
         } else {
            if (!nd2.qh && n.d.x!=0&&n.d.y!=0) {
               nd.d.x=n.d.x;
               nd.d.y=n.d.y;
            }
            if (nd2.qh) {
               pts.pts.push_back(nd2);
            }
            pts.pts.push_back(nd);
            return 1;
         }
      }
   }
   if (!there) {
      pts.pts.push_back(n);
   }
   return 1;
}

uint QuadHldr::addChildrenOnEdge (
   Pts& pts, Direction d, QuadNode* pQN, int8_t ind, float dx, float ds,
   bool noChk
) {
   if (!qp) {
      uint ncnt = addThis(pts, {(int8_t)-d.x,(int8_t)-d.y}, dx, ds,
                          pQN,ind,noChk);
      return ncnt;
   }
   QuadNode* resqp = (QuadNode*)get<0>(bpxor(fp, pQN));
   vector<map<QuadNode*,uint>::iterator> qit = qpfind((QuadNode*)resqp);
   if (!(qit.size() && resqp->hasName(pts.ina,qit))) {
      uint ncnt = addThis(pts, {(int8_t)-d.x,(int8_t)-d.y}, dx, ds,
                          pQN,ind,noChk);
      return ncnt;
   }
   uint c = 0;
   uint8_t lind=0;
   QuadHldr* qh = &resqp->en;
   QuadNode* tQN;
   tQN=qn();
   uint8_t tind = this-(QuadHldr*)tQN;
   int8_t ix;
   int8_t iy;
   vector<uint8_t> vind;
   if (d.x!=0) {
      ix=d.x==1?0:1;
      if (d.y!=0) {
         iy=d.y==1?0:1;
         lind=ix<<1|iy;
         vind.push_back(lind);
      } else {
         iy=0;
         lind=ix<<1|iy;
         vind.push_back(lind);
         iy=1;
         lind=ix<<1|iy;
         vind.push_back(lind);
      }
   } else {
      ix=0;
      iy=d.y==1?0:1;
      lind=ix<<1|iy;
      vind.push_back(lind);
      ix=1;
      lind=ix<<1|iy;
      vind.push_back(lind);
   }
   int nni = pts.nni;
   pts.nni = pts.pts.size()-1;
   for (int i=0;i<vind.size();++i) {
      lind = vind[i];
      qh = (QuadHldr*)resqp+lind;
      if (qh->fp) {
         QuadNode* resqp = (QuadNode*)get<0>(bpxor(qh->fp, tQN));
         vector<map<QuadNode*,uint>::iterator> qit =
            qpfind((QuadNode*)resqp);
         if (qit.size() && resqp->hasName(pts.ina,qit)) {
            int z = qh->addChildrenOnEdge(pts, d, tQN, tind, dx/2,
                                          ds-dx/4,-1);
            if (z==-1) {
               return -1;
            }
            c+=z;
            continue;
         }
      }
      int z = qh->addThis(pts, {(int8_t)-d.x,(int8_t)-d.y}, dx/2, ds-dx/4, tQN,
                          tind, -1);
      if (z==-1) {
         return -1;
      }
      ++c;
   }
   pts.nni = nni;
   return c;
}
uint QuadHldr::findNeighbours (Pts& pts, QuadNode* tQN, int8_t tind,
                               QuadNode* pQN, int8_t ind, float dx, float ds,
                               Direction d, bool notChild) {
   if (tQN==nullptr) {
      return 0;
   }
   short sign=1;
   short minus=-1;
   QuadNode* resqp = (QuadNode*)get<0>(bpxor(fp, pQN));
   QuadHldr* tQH = (QuadHldr*)tQN;
   uint8_t ix=0;
   int8_t lind = tind;
   ix = lind;
   uint8_t iy = 1&ix;
   ix>>=1;
   uint ncnt = 0;
   // determine the neighbour quadrant
   if (d.x!=0 || d.y!=0) {
      int8_t rx = ix-d.x;//quadrant index is opposite to direction
      int8_t ry = iy-d.y;
      lind = rx<<1|ry;
      if (rx>1 || rx<0 || ry>1 || ry<0) {
         // goto parent
         if (pQN == nullptr) {
            return 0;
         }
         QuadHldr* pQH = &pQN->en+ind;
         auto tuppqh = bpxor(pQH->fp, tQN);
         QuadNode* ppQN = (QuadNode*)get<0>(tuppqh);
         //printf("fn:ppQN:%p\n", ppQN);
         lind=get<1>(tuppqh);
         uint ptscnt = pQH->findNeighbours(
            pts, pQN, ind, (QuadNode*)ppQN, lind, 2*dx, ds,
            {(int8_t)((rx>1||rx<0)?d.x:0),(int8_t)((ry>1||ry<0)?d.y:0)}, true);
         if (ptscnt && ptscnt!=-1) {
            NdNPrn ndprn = pts.pts.back();
            pts.pts.pop_back();
            tuppqh = getNode(ndprn);
            QuadHldr* presqp = (QuadHldr*)get<0>(tuppqh);
            int8_t iix, iiy, lind, pind;
            vector<map<QuadNode*,uint>::iterator> qit =
               qpfind((QuadNode*)presqp);
            if (!(qit.size() && ((QuadNode*)presqp)->hasName(pts.ina,qit)) ||
               ndprn.qh->fp==nullptr) {
               ndprn.qh->addThis(pts, d, ndprn.dx, ndprn.ds, ndprn.prn,
                                 ndprn.ind);
               if (notChild) {
                  return -1;
               } else {
                  return 1;
               }
            }
            iix = d.x==1?1:(d.x==0?ix:0);
            iiy = d.y==1?1:(d.y==0?iy:0);
            lind = iiy|iix<<1;
            presqp+=lind;
            ppQN = ndprn.qh->qn();
            pind = ndprn.qh-(QuadHldr*)ppQN;
            if (notChild) {
               ncnt =
                  presqp->addThis(pts,d, dx, ds+dx/2, ppQN,pind, true);
            } else {
               int z =presqp->addChildrenOnEdge(
                  pts, {(int8_t)-d.x,(int8_t)-d.y}, ppQN, pind, dx, ds+dx/2);
               if (z==-1) {
                  ncnt+=0;
               }
            }
         } else if (ptscnt==-1) {
            if (d.x!=0 && d.y!=0) {
               NdNPrn& n = pts.pts[pts.pts.size()-1];
               if (d.x==-n.d.x || d.y==-n.d.y) {
                  NdNPrn& n2 = pts.pts[pts.pts.size()-2];
                  if (n.qh!=n2.qh) {
                     pts.pts.push_back(n);
                     n.d=d;
                  }
               } else {
                  n.d=d;
               }
            }
            if (notChild) {
               return -1;
            } else {
               return 1;
            }
         }
      } else {
         tQH+=lind;
         if (notChild) {
            ncnt=tQH->addThis(pts,d,dx,ds+dx/2,pQN,ind, true);
         } else {
            int z = tQH->addChildrenOnEdge(pts, {(int8_t)-d.x,(int8_t)-d.y},
                                           pQN, ind, dx, ds+dx/2);
            if (z==-1) {
               ncnt+=0;
            }
         }
      }
   } else {
      pts.ni=pts.pts.size()-1;
      ix = ix==0?1:-1;
      iy = iy==0?1:-1;
      for (short i=-1; i <=1; ++i) {
         for (short j=-1; j <= 1; ++j) {
            if (i==0 && j==0)
               continue;
            d.x=i; d.y=j;
            ncnt+=findNeighbours(pts, tQN, tind, pQN, ind, dx, dx/2, d);
         }
      }
      //printpts(pts.pts);
      ++pts.ni;
      pts.nni = pts.pts.size()-1;
      if (pts.nni<0)
         return 0;
      quickSort(pts.pts,pts.ni,pts.nni);
      //printf("---------\n");
      //printpts(pts.pts);
      //printf("---------\n");
      uint pni=pts.ni;
      while (pts.ni<pts.pts.size() && pni<pts.minPts) {
         NdNPrn nd = pts.pts[pts.ni];
         tQN=nd.qh->qn();
         tind=nd.qh-(QuadHldr*)tQN;
         //pts.nni=pts.pts.size();
         ncnt+=nd.qh->findNeighbours(
            pts, tQN, tind, nd.prn, nd.ind, nd.dx, nd.ds, nd.d);
         //quickSort(pts.pts, pts.nni, pts.pts.size()-1);
         if (nd.d.x!=0 && nd.d.y!=0) {
            Direction dd = nd.d;
            dd.x = 0;
            //pts.nni=pts.pts.size();
            ncnt+=nd.qh->findNeighbours(
               pts, tQN, tind, nd.prn, nd.ind, nd.dx, nd.ds, dd);
            //quickSort(pts.pts, pts.nni, pts.pts.size()-1);
            dd.x = nd.d.x;
            dd.y=0;
            //pts.nni=pts.pts.size();
            ncnt+=nd.qh->findNeighbours(
               pts, tQN, tind, nd.prn, nd.ind, nd.dx, nd.ds, dd);
            //quickSort(pts.pts, pts.nni, pts.pts.size()-1);
         }
         // if (pts.ni==pts.nni) {
         //    quickSort(pts.pts, pts.nni+1, pts.pts.size()-1);
         //    pts.nni=pts.pts.size()-1;
         // }
         quickSort(pts.pts, pts.ni+1, pts.pts.size()-1);
         if (nd.qh->fp && pts.ni>pni) {
            QuadNode* resqp = (QuadNode*)get<0>(getNode(nd));
            vector<map<QuadNode*,uint>::iterator> qit =
               qpfind((QuadNode*)resqp);
            if (!qit.size()) {
               set<void*>::iterator sit = setffset.find(resqp);
               bool isS=sit != setffset.end();
               if (isS) {
                  set<FFJSON*>& sf = *(set<FFJSON*>*)*sit;
                  set<FFJSON*>::iterator sfit = sf.begin();
                  while (sfit!=sf.end()) {
                     //break;
                     int8_t matchcount = (int8_t)ffHasName(**sfit, pts.ina);
                     if (matchcount) {
                        pts.pts[pni] = {(QuadHldr*)*sfit,
                           (QuadNode*)-1,nd.dx,nd.ds,{matchcount,0},0};
                        ++pni;
                        if (pni>pts.ni) {
                           pts.pts.insert(
                              pts.pts.begin()+pni,
                              distance(sfit, sf.end()),{0});
                           pts.ni+=distance(sfit, sf.end())-1;
                        }
                     }
                     ++sfit;
                  }
               } else {
                  int8_t matchcount = (int8_t)ffHasName((*(FFJSON*)resqp),
                                                    pts.ina);
                  if (matchcount) {
                     pts.pts[pni] = pts.pts[pts.ni];
                     pts.pts[pni].d.x = matchcount;
                     ++pni;
                  }
               }
            }
         }
         ++pts.ni;
      }
      // for (int i=0; i< pts.pts.size()-1;++i) {
      //    for (int j=i+1; j<pts.pts.size(); ++j) {
      //       if (pts.pts[i].qh==pts.pts[j].qh &&
      //           !((pts.pts[i].d.x!=0 && pts.pts[i].d.x == -pts.pts[j].d.x)
      //            || (pts.pts[i].d.y!=0 && pts.pts[i].d.y ==
      //                -pts.pts[j].d.y))) {
      //          printf("%d:%p(%d,%d) == %d:%p(%d,%d)\n",
      //                 i, pts.pts[i].qh, pts.pts[i].d.x, pts.pts[i].d.y,
      //                 j, pts.pts[j].qh, pts.pts[j].d.x, pts.pts[j].d.y);
      //       }
      //    }
      // }
      // printpts(pts.pts);
      pts.pts.erase(pts.pts.begin()+pni, pts.pts.end());
   }
   return ncnt;
}


// void QuadNode::del (QuadNode* tQN, int8_t tind,
//                     QuadNode* pQN, int8_t ind) {
//    QuadHldr* qh = &en;
//    for (int8_t i = 0; i < 4; ++i,++qh) {
//       qh->del(this, i, tQN, tind);
//    }
// }

// void QuadHldr::del (QuadNode* tQN, int8_t tind,
//                     QuadNode* pQN, int8_t ind) {
//    if (!fp) {
//       return;
//    }
//    QuadNode* resfp = (QuadNode*)get<0>(bpxor(fp,pQN));
//    map<QuadNode*,int>::iterator qit = qpfind(resfp);
//    bool isQ = isQuad(qit);
//    if (!isQ) {
//       delete resfp;
//    } else {
//       resfp->del(tQN, tind, pQN, ind);
//       qpset.erase(qit);
//       delete resfp;
//    }
//    fp=nullptr;
// }

uint QuadHldr::getPointsFromQuad (
   Pts& pts, uint level, float x, float y, QuadNode* tQN,
   int8_t tind, QuadNode* pQN, int8_t ind
) {
   float dx = 180/(pow(2,level+1));
   if (fp==nullptr) {
      return findNeighbours(pts, tQN, tind, pQN, ind, dx);
   }
   void* resfp = get<0>(bpxor(fp,pQN));
   vector<map<QuadNode*,uint>::iterator> qit = qpfind((QuadNode*)resfp);
   if (!qit.size()) {
      set<void*>::iterator sit = setffset.find(resfp);
      bool isS=sit != setffset.end();
      if (isS) {
         set<FFJSON*>& sf = *(set<FFJSON*>*)*sit;
         set<FFJSON*>::iterator sfit = sf.begin();
         while (sfit!=sf.end()) {
            int8_t matchcount = (int8_t)ffHasName(**sfit, pts.ina);
            if (matchcount) {
               pts.pts.push_back(
                  {(QuadHldr*)*sfit,(QuadNode*)-1,dx,dx,{matchcount,0},0});
            }
            ++sfit;
         }
      } else if (ffHasName(*(FFJSON*)resfp, pts.ina)) {
         pts.pts.push_back({this,pQN});
      }
      return findNeighbours(pts, tQN, tind, pQN, ind, dx);
   } else {
      QuadNode* resqp = (QuadNode*)resfp;
      int8_t matchcount = (int8_t)resqp->hasName(pts.ina,qit);
      if (!matchcount) {
         return findNeighbours(pts, tQN, tind, pQN, ind, dx);
      }
      QuadHldr* qh = &resqp->en;
      int xsign=1;
      int ysign=1;
      int minus=-1;
      int8_t lind=0;
      float dy = 90/(pow(2,level+1));
      for (lind=0;lind<4; ++lind, ++qh, xsign*=ysign, ysign*=minus) {
         if (xsign*pts.c.x>=xsign*x && ysign*pts.c.y>=ysign*y) {
            break;
         }
      }
      float cx = x+xsign*dx;
      float cy = y+ysign*dy;
      return qh->getPointsFromQuad(pts, level+1, cx, cy,
                                   resqp, lind, tQN, tind);
   }
   return level;
}

int8_t Direction::abs () {
   int8_t xx = 1+x;
   int8_t yy = 1+y;
   return yy+2*xx;
}
void makeThngsTree () {
   QuadNode q;
   fnameints =
      &config["virtualWebHosts"]["underconstruction"]["nameints"];
   nameints = fnameints->val.pairs;
   map<string, FFJSON*>::iterator nit = nameints->begin();
   multiset<map<string, FFJSON*>::iterator, CompNameWt> namewtset(cmpNmWt);
   while (nit!=nameints->end()) {
      namewtset.insert(nit);
      ++nit;
   }
   uint i=0;
   multiset<map<string, FFJSON*>::iterator, CompNameWt>::iterator mit
      = namewtset.begin();
   while (mit!=namewtset.end()) {
      mitpos[&((*mit)->first)]=i;
      ++i;
      ++mit;
   }
   FFJSON& users = config["virtualWebHosts"]["underconstruction"]["users"];
   FFJSON::Iterator it = users.begin();
   FFJSON::Iterator tit;
   while (it!= users.end()) {
      if (it->isType(FFJSON::LINK)) {
         ++it;
         continue;
      }
      string user = it.getIndex();
      FFJSON& uthings = (*it)["things"];
      tit = uthings.begin();
      while (tit!=uthings.end()) {
         if (!((*tit)["name"].isType(FFJSON::UNDEFINED) ||
               (*tit)["location"].isType(FFJSON::UNDEFINED))) {
            vector<string> mstr = metaname((ccp)(*tit)["name"]);
            vector<uint> ina = nametouint(mstr);
            uint level=thnsTree.insert((*tit), ina);
         }
         // Circle c;
         // c.nf=(FFJSON*)1;
         // printf("%s\n", (*tit)["location"].stringify().c_str());
         // thnsTree.print(c);
         ++tit;
      }
      ++it;
   }
}

WSServer::WSServer (
   const char* pcHostName, int iDebugLevel,
   int iPort, int iSecurePort, const char* pcSSLCertFilePath,
   const char* pcSSLPrivKeyFilePath, const char* pcSSLCAFilePath,
   bool bDaemonize, int iRateUs, const char* pcInterface,
   const char* pcClient, int iOpts,
   #ifndef LWS_NO_CLIENT
      const char* pcAddress, unsigned int uiOldus,
   #ifdef LIBWEBSOCKETS
   struct lws* pWSI,
   #endif
   #endif
   int iSysLogOptions
) {
   ffl_info(FPL_HTTPSERV, "Building quad tree..");
   ffl_debug(FPL_HTTPSERV, "malloc size: %d\n",
             malloc_usable_size(&config["virtualWebHosts"]));
   set<FFJSON*>* pvc = new set<FFJSON*>();
   QuadNode* qp = new QuadNode();
   Qn2* qp2 = new Qn2();
   FFJSON* fp = new FFJSON();
   QuadHldr* qh = new QuadHldr();
   char* ppvc = (char*)&pvc;
   char a = (char)ppvc[7];
   // printf("pvc size:%zu:%zu\n"
   //        "pvc: %lx, a: %02x\n"
   //        "qh size:%zu:%zu\n"
   //        "qp size:%zu:%zu\n"
   //        "qp mod size: %lu\n"
   //        "fp  size:%zu:%zu\n", malloc_usable_size(pvc), sizeof(ppvc),
   //        (size_t)pvc, a, malloc_usable_size(qh), sizeof(*qh),
   //        malloc_usable_size(qp2), sizeof(Qn2), ((long)qp+3)%sizeof(*qp),
   //        malloc_usable_size(fp), sizeof(*fp));
   
   makeThngsTree();
   Pts pts;
   vector<string> mstr = metaname("car");
   pts.ina=nametouint(mstr);
   //Circle c = {180.0, 90.0, 10.5};
   //Circle c = {0.1, 0.1, 10.5};
   //Circle c = {0.9, 0.8, 10.5};
   //pts.c = {77.7584640, 12.9826816, 10.5};
   pts.c = {77.7617416, 12.9892349, 10.5};
   printf("c: %f,%f\n", pts.c.x, pts.c.y);
   FerryTimeStamp ftsStart;
   FerryTimeStamp ftsEnd;
   FerryTimeStamp ftsDiff;
   ftsStart.Update();
   thnsTree.print(pts.c);
   //ina.push_back(0x80);
   thnsTree.getPointsFromQuad(pts);
   ftsEnd.Update();
   ftsDiff = ftsEnd - ftsStart;
   cout << "%TEST_FINISHED% time=" << ftsDiff << " test21\n" << endl;
   std::vector<NdNPrn>::iterator it = pts.pts.begin();
   it = pts.pts.begin();
   while (it!=pts.pts.end()) {
      FFJSON* fp;
      if (it->prn==(QuadNode*)-1) {
         fp = (FFJSON*)it->qh;
      } else {
         fp = (FFJSON*)get<0>(getNode(*it));
      }
      printf("%s\n",(*fp)["location"].stringify().c_str());
      ++it;
   }
   // pts.c.nf=nullptr;
   // thnsTree.print(pts.c);
   // printf("nf: %s\n", (*pts.c.nf)["location"].stringify().c_str());
   mg_mgr_init(&mgr);
   mg_mgr_init(&mail_mgr);
   char httpsport[16];
   char httpport[16];
   sprintf(httpsport, "0.0.0.0:%d", (int)config["HTTPSPort"]);
   sprintf(httpport, "0.0.0.0:%d", (int)config["HTTPPort"]);
   mg_http_listen(&mgr, httpsport, fn_tls, NULL);
   mg_http_listen(&mgr, httpport, fn, NULL);
   while (!force_exit) {
      mg_mgr_poll(&mgr, 1000);
   }
}

WSServer::~WSServer () {
   
}


//string WSServer::per_session_data__http::vhost();
