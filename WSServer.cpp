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

enum { EHLO, STARTTLS, STARTTLS_WAIT, AUTH, FROM, TO, DATA, BODY, QUIT, END };

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
map<string,QuadHldr> thnsTreeMap;
Metaphone3Encoder m3e;

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
      FFJSON& things=vhost["things"];
      if (vhost["rootdir"])
         opts.root_dir=(ccp)vhost["rootdir"];
      if (sessionData["cookie"])get_cookies(sessionData["cookie"], cookie);
      ffl_notice(FPL_HTTPSERV, "cookie[bid]: %s",(ccp)cookie["bid"]);
      if (cookie["bid"]) {
         bid=(ccp)cookie["bid"];
      }
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
         if (!inmsg["bid"].isType(FFJSON::UNDEFINED)) {
            if (strcmp(inmsg["bid"],"undefined") || inmsg["bid"]) {
               bid=(ccp)inmsg["bid"];
               if(rbs[bid])
                  goto gotbid;
            }
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
            rbs[bid]["ts"]=chrono::high_resolution_clock::now();
            FFJSON reply;
            username = rbs[bid]["user"];
            if (username) {
               reply = users[(ccp)rbs[bid]["user"]];
            }
            reply["bid"]=bid;
            FFJSON::Iterator tit = things.begin();
            std::set<FFJSON*, CompareByDistanceToCenter>::iterator atit;
            Circle C = {12.91, 77.61, 0.5};
            if (!inmsg["geoposition"].isType(FFJSON::UNDEFINED) &&
                inmsg["geoposition"].size==2
            ) {
               C.x=(float)inmsg["geoposition"][0];
               C.y=(float)inmsg["geoposition"][1];
               rbs[bid]["geoposition"] = inmsg["geoposition"];
            }
            CompareByDistanceToCenter comp(C.x, C.y);
            std::set<FFJSON*, CompareByDistanceToCenter> pts(comp);
            // while (tit != things.end()) {
            //    string thing = tit.getIndex();
            //    thnsTreeMap[thing].getPointsFromRadius(pts, C);
            //    ++tit;
            // }
            atit = pts.begin();
            int k=reply["things"].size;
            while (atit != pts.end()) {
               ccp un = (**atit)["user"]["name"];
               if (!username || strcmp(un,username)) {
                  reply["things"][k]=*(*atit);
                  ++k;
               }
               ++atit;
            }
            mg_http_reply(c, 200, headers, "%s",
                          reply.stringify(true).c_str());
            rbs.save();
         } else {
            mg_http_reply(c, 200, headers, "{%Q:%Q}", "error", "nobid");
         }
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
            rbs[bid]["user"]=username;
            rbs[bid]["ip"]=c->rem.ip;
            user["bid"]=bid;
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
         if (!body["search"]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "nosearch");
            goto done;
         }
         ccp srchStr = body["search"];
         vector<string> swds = explode(srchStr);
         vector<map<string,QuadHldr>::iterator> vtit;
         for (int k=0;k<swds.size();++k) {
            string mwd = m3e.encode(swds[k]).first;
            map<string,QuadHldr>::iterator tit =
               thnsTreeMap.lower_bound(mwd);
            if (tit!=thnsTreeMap.end()) {
               vtit.push_back(tit);
            }
         }
         if (!vtit.size()) {
            mg_http_reply(c, 200, headers, "{%Q:%Q}", "error", "noresults");
            goto done;
         }
         FFJSON reply;
         int k=0;
         std::set<FFJSON*, CompareByDistanceToCenter>::iterator atit;
         Circle C = {12.91, 77.61, 0.5};
         if (!body["geoposition"].isType(FFJSON::UNDEFINED) &&
             body["geoposition"].size==2
         ) {
            C.x=(float)body["geoposition"][0];
            C.y=(float)body["geoposition"][1];
            rbs[bid]["geoposition"] = body["geoposition"];
         }
         CompareByDistanceToCenter comp(C.x, C.y);
         map<FFJSON*, uint> score;
         for (int k=0;k<vtit.size();++k) {
            set<FFJSON*, CompareByDistanceToCenter> pts(comp);
//            vtit[k]->second.getPointsFromRadius(pts, C);
            set<FFJSON*, CompareByDistanceToCenter>::iterator it =
               pts.begin();
            while (it!=pts.end()) {
               uint& s= score[*it];
               ++s;
               ++it;
            }
         }
         list<pair<FFJSON*, uint>> vp;
         map<FFJSON*, uint>::iterator it=score.begin();
         while (it!=score.end()) {
            list<pair<FFJSON*, uint>>::iterator lit = vp.begin();
            while(1){
               if (it->second>lit->second) {
                  vp.insert(lit,pair(it->first, it->second));
                  break;
               }
               ++lit;
               if (lit==vp.end()) {
                  vp.insert(lit,pair(it->first, it->second));
                  break;
               }
            }
            ++it;
         }
         list<pair<FFJSON*, uint>>::iterator lit = vp.begin();
         while (lit != vp.end()) {
            reply["things"][k]=*(lit->first);
            ++k;
            ++lit;
         }
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
               if (!validThingName(string((ccp)cthings[i]["name"]))) {
                  mg_http_reply(c, 400, headers, "{%Q:%Q}",
                                "error", "invalidName");
                  goto done;
               }
               while (j<uthings.size &&
                      (int)uthings[j]["id"]!=(int)cthings[i]["id"]) {
                  ++j;
               }
               bool locChanged = false;
               bool nameChanged = false;
               string cname((ccp)cthings[i]["name"]);
               vector<string> cwds = explode(cname);
               for (int k=0; k<cwds.size(); ++k) {
                  cwds[k]=m3e.encode(cwds[k]).first;
               }
               cthings[i].erase("user");
               if (!uthings[j]) {
                  cthings[i]["id"] = uthings.size?
                     ((int)uthings[j-1]["id"]) + 1 : 0;
                  FFJSON& ln = uthings[j]["user"].addLink(users, username);
                  if (!ln)
                     delete &ln;
                  nameChanged=true;
               } else {
                  string uname((ccp)uthings[j]["name"]);
                  strLower(cname);
                  strLower(uname);
                  vector<string> uwds = explode(uname);
                  for (int k=0; k<uwds.size(); ++k) {
                     uwds[k]=m3e.encode(uwds[k]).first;
                  }
                  if (strcmp(cname.c_str(),uname.c_str())) {
                     for (int k=0; k<uwds.size(); ++k) {
                        things[uwds[k]].erase(&uthings[j]);
                        nameChanged=true;
                     }
                  }
                  FFJSON& cloc = cthings[i]["location"];
                  FFJSON& uloc = uthings[j]["location"];
                  if ((double)cloc[0]!=(double)uloc[0] ||
                      (double)cloc[1]!=(double)uloc[1] || nameChanged) {
                     for (int k=0; k<uwds.size(); ++k) {
                        QuadHldr& qh = thnsTreeMap[uwds[k]];
                        qh.insert(uthings[j], true);
                     }
                     locChanged=true;
                  }
               }
               uthings[j]=cthings[i];
               if (locChanged || nameChanged) {
                  for (int k=0; k<cwds.size(); ++k) {
                     QuadHldr& qh = thnsTreeMap[cwds[k]];
                     qh.insert(uthings[j]);
                  }
               }
               for (int k=0; k<cwds.size(); ++k) {
                  FFJSON& ln = things[cwds[k]][].addLink(
                     vhost, string("users.")+username+".things."+to_string(j));
                  if (!ln)
                     delete &ln;
               }
            }
         }
         mg_http_reply(c, 200, headers, "%s", user.stringify(true).c_str());
         vhost.save();
      } else if (strstr(path, "/upload?")) {
         //upload?
         if (!bid.length() || !rbs[bid]) {
            mg_http_reply(c, 400, headers, "{%Q:%Q}", "error", "nobid");
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
            int tSize = user["things"].size;
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
         sprintf(msg, "{\"thingId\":%d,\"picId\":%d", thingId,picId);
         mg_http_upload(
            c, hm, &mg_fs_posix, upldpth.c_str(), 2999999, msg);
         if (fofst+chnkSz >= ttlSz) {
            user.erase("pendingThings");
            user["things"][thngi]["pics"][picId].erase("partial");
            auto now = chrono::high_resolution_clock::now();
            auto now_ms =
               std::chrono::time_point_cast<std::chrono::milliseconds>(now);
            auto epoch = now_ms.time_since_epoch();
            long lepoch = epoch.count();
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

set<void*> qpset;
set<void*> setffset;

template<typename T, typename U>
void* fpxor (T* a, U* b, char ind) {
   size_t t = (size_t)(((size_t)a xor (size_t)b));
   char* c = (char*)&t;
   if (c[7]!=0) {
      printf("error: msbs are not equal\n");
   }
   c[7]=ind;
   return (void*)t;
}

template<typename T, typename U>
tuple<void*, char> bpxor (T* a, U* b) {
   char n = ((char*)&a)[7];
   ((char*)&a)[7]=0;
   void* m = (void*)(((size_t)a) xor (size_t)b);
   return tuple<void*, char>{m, n};
}
tuple<void*,char> getNode (NdNPrn n) {
   return bpxor(n.qh->qp, n.prn);
}
uint QuadHldr::insert (FFJSON& rF, bool deleteLeaf, uint level, float x,
                       float y, QuadNode* tQN, char tind,
                       QuadNode* pQN, char ind) {
   //printf("x,y: %lf,%lf\n", x, y);
   if (fp==nullptr || (void*)fp==pQN) {
      fp = (FFJSON*)fpxor(&rF, pQN, ind);
      //printf("rF:%p,%s inserted\n", &rF,rF["location"].stringify().c_str());
      return level;
   }
   void* resfp = get<0>(bpxor(fp, pQN));
   set<FFJSON*>* ressfp = (set<FFJSON*>*)resfp;
   set<void*>::iterator qit = qpset.find(resfp);
   set<void*>::iterator sit = setffset.find(resfp);
   bool isQ=qit != qpset.end();
   if (!isQ) {
      bool isS=sit != setffset.end();
      if (deleteLeaf) {
         if (!isS && resfp == (void*)&rF) {
            fp=(FFJSON*)pQN;
            return level;
         } else {
            set<FFJSON*>::iterator it = ressfp->find(&rF);
            if (it!=ressfp->end()) {
               ressfp->erase(it);
            }
         }
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
      FFJSON* xorfp = (FFJSON*)fpxor(resfp,tQN,tind);
      //printf("%p,%p\n", resfp, xorfp);
      if ((double)tmp["location"][0] >= x) {
         if ((double)tmp["location"][1] >= y) {
            qp->en.fp=xorfp;
         } else {
            qp->es.fp=xorfp;
         }
      } else {
         if ((double)tmp["location"][1] >= y) {
            qp->wn.fp=xorfp;
         } else {
            qp->ws.fp=xorfp;
         }
      }
      qpset.insert(qp);
      //printf("qp: %p, tQN: %p\n", qp, tQN);
      qp = (QuadNode*)fpxor(qp,pQN,ind);
      return insert(rF,deleteLeaf,level,x, y, tQN, tind, pQN, ind);
   } else {
      float dx = 180/(pow(2,level+1));
      float dy = 90/(pow(2,level+1));
      FFJSON* pxorrf = (FFJSON*)fpxor(&rF,tQN,tind);
      QuadNode* qpres = (QuadNode*)resfp;
      if ((double)rF["location"][0] >= x) {
         if ((double)rF["location"][1] >= y) {
            if (qpres->en.fp==nullptr) {
               qpres->en.fp=pxorrf;
            } else {
               return qpres->en.insert(
                  rF,deleteLeaf, level+1, x+dx, y+dy, qpres, 0, tQN, tind);
            }
         } else {
            if (qpres->es.fp==nullptr) {
               qpres->es.fp=pxorrf;
            } else {
               return qpres->es.insert(
                  rF,deleteLeaf, level+1, x+dx, y-dy, qpres, 1, tQN, tind);
            }
         }
      } else {
         if ((double)rF["location"][1] >= y) {
            if (qpres->wn.fp==nullptr) {
               qpres->wn.fp=pxorrf;
            } else {
               return qpres->wn.insert(
                  rF, deleteLeaf, level+1, x-dx, y+dy, qpres, 2, tQN, tind);
            }
         } else {
            if (qpres->ws.fp==nullptr) {
               qpres->ws.fp=pxorrf;
            } else {
               return qpres->ws.insert(
                  rF, deleteLeaf, level+1, x-dx, y-dy, qpres, 3, tQN, tind);
            }
         }
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
   float x1 = x - (double)f["location"][0];
   float y1 = y - (double)f["location"][1];
   float x2 = x - (double)(*nf)["location"][0];
   float y2 = y - (double)(*nf)["location"][1];
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

void QuadHldr::print (Circle& c, uint level, QuadNode* tQN, char tind,
                      QuadNode* pQN, char ind) {
   if (fp==nullptr) {
      printf("%.*s%d: %p\n", level,"|||||||||||||||||||||||||||||||||||||||||",
             tind, nullptr);
      return;
   }
   QuadNode* resqp = (QuadNode*)get<0>(bpxor(fp,pQN));
   set<void*>::iterator qit = qpset.find(resqp);
   set<void*>::iterator sit = setffset.find(resqp);
   bool isQ = qit != qpset.end();
   bool isS = sit != setffset.end();
   
   if (!isQ) {
      FFJSON& f = *(FFJSON*)resqp;
      if (c.nf!=(FFJSON*)1)
         c.grabIfNearest(f);
      printf("%.*s%d: %p%s\n",level,
             "||||||||||||||||||||||||||||||||||||||||||||||||||||||||||",
             tind, &f, f["location"].stringify().c_str());
   } else {
      QuadHldr* qh = (QuadHldr*)resqp;
      printf("%.*s%d: %p\n", level,"|||||||||||||||||||||||||||||||||||||||||",
             tind, qh);
      for (int i=0;i<4;++i,++qh) {
         qh->print(c, level+1, resqp,i,tQN,tind);
      }
   }
}
QuadNode::QuadNode () {
   en.fp=nullptr;
   es.fp=nullptr;
   wn.fp=nullptr;
   ws.fp=nullptr;
}
// uint QuadHldr::addAllLeavesInRadius (
//    set<FFJSON*,CompareByDistanceToCenter>& pts, QuadNode* pQN
// ) {
//    if (fp==nullptr) {
//       return 0;
//    }
//    void* resfp = pxor(fp, pQN);
//    set<void*>::iterator qit = qpset.find(qp);
//    set<void*>::iterator sit = setffset.find(sp);
//    bool isQ = qit != qpset.end();
//    bool isS = sit != setffset.end();
//    if (!isQ) {
//       if (isF) {
//          pts.insert(resfp);
//          return 1;
//       }
//    }
//    QuadHldr* qh = &qp->en;
//    uint leafCount=0;
//    for (int i=0;i<4; ++i, ++qh) {
//       leafCount+=qh->addAllLeavesInRadius(pts, (QuadNode*)resfp);
//    }
//    return leafCount;
// };

// uint QuadHldr::getPointsFromRadius (
//    set<FFJSON*, CompareByDistanceToCenter>& pts, Circle& c, uint minPts,
//    uint level, float x, float y, QuadNode* pQN
// ) {
//    if (fp==nullptr || (void*)fp==pQN) {
//       return 0;
//    }
//    void* resfp = pxor(fp,pQN);
//    set<void*>::iterator fit = ffpset.find(fp);
//    set<void*>::iterator sit = setffset.find(sp);
//    bool isF = fit != ffpset.end();
//    if (isF || sit != setffset.end()) {
//       FFJSON* tmp;
//       if (isF)
//          tmp = (FFJSON*)resfp;
//       else
//          tmp = *((set<FFJSON*>*)resfp)->begin();
//       float cx = (float)(*tmp)["location"][0];
//       float cy = (float)(*tmp)["location"][1];
//       float dist = pow(pow(cx-c.x,2)+pow(cy-c.y,2), 0.5);
//       if (dist<=c.r) {
//          if (isF) {
//             pts.insert((FFJSON*)resfp);
//             return 1;
//          } else {
//             set<FFJSON*>::iterator it = ((set<FFJSON*>*)resfp)->begin();
//             while (it!=((set<FFJSON*>*)resfp)->end()) {
//                pts.insert(*it);
//                ++it;
//             }
//             return ((set<FFJSON*>*)resfp)->size();
//          }
//       }
//       return 0;
//    } else {
//       QuadHldr* qh = &((QuadNode*)resfp)->en;
//       float dx = 180/(pow(2,level+1));
//       float dy = 90/(pow(2,level+1));
//       int xsign=1;
//       int ysign=1;
//       int minus=-1;
//       float ddist = pow(pow(dx,2)+pow(dy,2), 0.5);
//       uint leafCount=0;
//       for (int i=0;i<4; ++i, ++qh, xsign*=ysign, ysign*=minus) {
//          if (qh->qp==nullptr)
//             continue;
//          float cx = x + xsign*dx;
//          float cy = y + ysign*dy;
//          float dist = pow(pow(cx-c.x,2)+pow(cy-c.y,2), 0.5);
// //         if (dist-ddist<=c.r) {
//          if (leafCount < minPts) {
//             leafCount+=
//                qh->getPointsFromRadius(pts, c, minPts, level+1, cx, cy,
//                                        (QuadNode*)resfp);
//             if (leafCount>=minPts)
//                return leafCount;
//          }
//       }
//       return leafCount;
//    }
// }

uint QuadHldr::addChildrenOnEdge (Direction d, vector<NdNPrn>& pts,
                                  QuadNode* pQN, char ind) {
   if (!qp || qp==pQN)
      return 0;
   QuadNode* resqp = (QuadNode*)get<0>(bpxor(fp, pQN));
   set<void*>::iterator qit = qpset.find(resqp);
   bool isQ=qit != qpset.end();
   if (!isQ) {
      NdNPrn n = {this, pQN, {(char)-d.x,(char)-d.y}};
      pts.push_back(n);
      return 1;
   }
   uint c = 0;
   uchar lind=0;
   QuadHldr* qh = &resqp->en;
   QuadNode* pQN2;
   set<void*>::iterator it = qpset.upper_bound(this-4);
   pQN2=(QuadNode*)*it;
   char x = 0;
   char y = 0;
   char *f, *ff;
   char *n, *nn;
   if (d.x!=0) {
      n = &y; nn=&d.y;
      f = &x; ff=&d.x;
   } else {
      n = &x; nn=&d.x;
      f = &y; ff=&d.y;
   }
   char j;
   if (*nn!=0) {
      j=1;
      if (*nn>0) {
         *n=0;
      } else {
         *n=1;
      }
   } else {
      j=2;
      *n=0;
   }
   for (*n=*nn>0?0:1; *n<j; *n+=1) {
      lind=y|x<<1;
      QuadHldr* lqh = qh+lind;
      uint cc = lqh->addChildrenOnEdge(d, pts, pQN2, lind);
      if (cc==0) {
         *f=~*f;
         ind = y|x<<1;
         lqh = qh+ind;
         cc = lqh->addChildrenOnEdge(d, pts, pQN2, lind);
         *f=~*f;
      }
      c+=cc;
   }
   return c;
}

uint QuadHldr::findNeighbours (vector<NdNPrn>& pts,
                               QuadNode* pQN, char ind, Direction d,
                               bool notChild) {
   short sign=1;
   short minus=-1;
   uint ni=pts.size();
   QuadNode* resqp = (QuadNode*)get<0>(bpxor(fp, pQN));
   QuadHldr* tQH;
   uchar ix=0;
   set<void*>::iterator it = qpset.upper_bound(this-4);
   tQH=(QuadHldr*)*it;
   char lind;
   ix = this-tQH;
   uchar iy = 1&ix;
   ix>>=1;
   uint ncnt = 0;
   // determine the neighbour quadrant
   if (d.x!=0 || d.y!=0) {
      char rx = ix-d.x;//quadrant index is opposite to direction
      char ry = iy-d.y;
      lind = rx<<1|ry;
      if (rx>1 || rx<0 || ry>1 || ry<0 || !(tQH+lind)->fp) {
         // goto parent
         if (pQN == nullptr) {
            return 0;
         }
         QuadHldr* pQH = &pQN->en+ind;
         auto tuppqh = bpxor(pQH->fp, tQH);
         QuadNode* ppQN = (QuadNode*)get<0>(tuppqh);
         printf("fn:ppQN:%p\n", ppQN);
         lind=get<1>(tuppqh);
         Direction td = d;
        corners:
         uint ptscnt = pQH->findNeighbours(pts, (QuadNode*)ppQN, lind, td,
                                           true);
         if (ptscnt) {
            NdNPrn ndprn = pts.back();
            tuppqh = getNode(ndprn);
            QuadHldr* presqp = (QuadHldr*)get<0>(tuppqh);
            set<void*>::iterator qit = qpset.find(presqp);
            bool isQ=qit != qpset.end();
            if (!isQ) {
               return 1;
            }
            pts.pop_back();
            char iix = td.x==1?1:(d.x==0?ix:0);
            char iiy = td.y==1?1:(d.y==0?iy:0);
            char lind = iiy|iix<<1;
            presqp+=lind;
            it = qpset.upper_bound(ndprn.qh-4);
            if (notChild) {
               if (presqp->fp) {
                  pts.push_back({presqp,(QuadNode*)*it});
                  ncnt+=1;
               }
            } else {
               ncnt+=presqp->addChildrenOnEdge(
                  {(char)-td.x, (char)-td.y}, pts, (QuadNode*)*it,
                  ndprn.qh-(QuadHldr*)*it);
            }
         }
         if (!(tQH+lind)->fp && d.x!=0 & d.y!=0 && !notChild) {
            if (td.x!=0 && td.y!=0) {
               td.x=0;
               goto corners;
            } else if (!td.x) {
               td.x=d.x;
               td.y=0;
               goto corners;
            }
         }
      } else {
         tQH+=lind;
         if (notChild) {
            pts.push_back({tQH,pQN,d});
            ncnt=1;
         } else {
            ncnt = tQH->addChildrenOnEdge({(char)-d.x, (char)-d.y},
                                          pts, pQN, ind);
         }
      }
   } else {
      for (short i=-1; i <=1; ++i) {
         for (short j=-1; j <= 1; ++j) {
            if (i==0 && j==0)
               continue;
            d.x=i; d.y=j;
            ncnt+=findNeighbours(pts, pQN, ind, d);
         }
      }
      while (pts.size()-ni>0) {
         ncnt+=pts[ni].qh->findNeighbours(
            pts, pts[ni].prn, get<1>(getNode(pts[ni])), pts[ni].d);
         if (pts[ni].d.x!=0 && pts[ni].d.y!=0) {
            ncnt+=pts[ni].qh->findNeighbours(
               pts, pts[ni].prn, get<1>(getNode(pts[ni])), {pts[ni].d.x,0});
            ncnt+=pts[ni].qh->findNeighbours(
               pts, pts[ni].prn, get<1>(getNode(pts[ni])), {0,pts[ni].d.y});
         }
         ++ni;
      }
   }
   return ncnt;
}

// uint addDirectedChildren (Directiron d; deque<WholeQuadNode>& qcqh,
//                           QuadNode* tQN, char tind,
//                           QuadNode* pQN, char ind) {
//    ncxsign=-rxsign*cxsign;
//    ncysign=-rysign*cysign;
//    float qx = cit->x;
//    float qy = cit->y;
//    float ddx = cit->dx/2;
//    float ddy = cit->dy/2;
//    float nx = qx + ncxsign*ddx;
//    float ny = qy + ncysign*ddy;
//    QuadNode* qn = cit->qn;
//    lind = cit->ind;
//    QuadNode* pqn = cit->pqh.node;
//    void* vp = (void*)qn;
//    set<void*>::iterator qit = qpset.find(vp);
//    set<void*>::iterator sit = setffset.find(vp);
//    bool isQ = qit != qpset.end();
//    bool isS = sit != setffset.end();
//    if (!isQ) {
//       continue;
//    }
//    int xind=nxsign>0?0:1;
//    xind<<=1;
//    xind|=nysign>0?0:1;
//    QuadHldr* qh = &qn->en+xind;
//    if (qh->fp != nullptr) {
//       wqn.qn=(QuadNode*)get<0>(bpxor(qh->fp, pqn));
//       wqn.x=nx;
//       wqn.y=ny;
//       wqn.dx=ddx;
//       wqn.dy=ddy;
//       wqn.xsign=ncxsign;
//       wqn.ysign=ncysign;
//       wqn.rxsign=nxsign;
//       wqn.rysign=nysign;
//       wqn.pqh.node=qn;
//       wqn.ind=wqn.pqh.ind=lind;
//       qcqh.push_back(wqn);
//       dist = pow(c.x-nx,2)+pow(c.y-ny,2);
//       if (dist<nearest) {
//          nearest=dist;
//          nearestQH=qh;
//          nearestInd=lind;
//          nearestpQN=pqn;
//       }
//    }
//    bool addOther = false;
//    if (nxsign==cxsign) {
//       ncxsign=-ncxsign;
//       addOther=true;
//    }
//    if (nysign==cysign) {
//       ncysign=-ncysign;
//       addOther=true;
//    }
//    if (addOther) {
//       nx = qx + ncxsign*ddx;
//       ny = qy + ncysign*ddy;
//       int xind=nxsign>0?0:1;
//       xind<<=1;
//       xind|=nysign>0?0:1;
//       qh = &qn->en+xind;
//       if (qh->fp != nullptr) {
//          wqn.qn=(QuadNode*)get<0>(bpxor(qh->fp, pqn));
//          wqn.x=nx;
//          wqn.y=ny;
//          wqn.dx=ddx;
//          wqn.dy=ddy;
//          wqn.xsign=ncxsign;
//          wqn.ysign=ncysign;
//          wqn.rxsign=nxsign;
//          wqn.rysign=nysign;
//          wqn.pqh.node=qn;
//          wqn.ind=wqn.pqh.ind=lind;
//          qcqh.push_back(wqn);
//          dist = pow(c.x-nx,2)+pow(c.y-ny,2);
//          if (dist<nearest) {
//             nearest=dist;
//             nearestQH=qh;
//             nearestInd=lind;
//             nearestpQN=pqn;
//          }
//       }
//    }
// }

void QuadNode::del (QuadNode* tQN, char tind,
                    QuadNode* pQN, char ind) {
   QuadHldr* qh = &en;
   for (char i = 0; i < 4; ++i,++qh) {
      qh->del(this, i, tQN, tind);
   }
}

void QuadHldr::del (QuadNode* tQN, char tind,
                    QuadNode* pQN, char ind) {
   if (!fp) {
      return;
   }
   QuadNode* resfp = (QuadNode*)get<0>(bpxor(fp,pQN));
   set<void*>::iterator qit = qpset.find(resfp);
   bool isQ = qit != qpset.end();
   if (!isQ) {
      delete resfp;
   } else {
      resfp->del(tQN, tind, pQN, ind);
      delete resfp;
   }
   fp=nullptr;
}

uint QuadHldr::getPointsFromQuad (
   vector<NdNPrn>& pts, Circle& c, uint minPts,
   uint level, ParentQuadHldr* pQH,  bool pseudo, float shortestRDist
) {
   if (fp==nullptr) {
      return 0;
   }
   QuadNode* tQN = pQH?pQH->node:nullptr;
   char tind = tQN?pQH->ind:0;
   QuadNode* pQN = pQH?pQH->pQH?pQH->pQH->node:nullptr:nullptr;
   char ind = pQN?pQH->pQH->ind:0;
   void* resfp = get<0>(bpxor(fp,pQN));
   set<void*>::iterator qit = qpset.find(resfp);
   set<void*>::iterator sit = setffset.find(resfp);
   bool isQ = qit != qpset.end();
   if (!isQ && !pseudo) {
      pts.push_back({this, pQN});
   } else {
      QuadNode* resqp = (QuadNode*)resfp;
      deque<WholeQuadNode> qcqh;
      ParentQuadHldr ptqh(&qcqh);
      ptqh.node=resqp;
      ptqh.pQH=pQH;
      float x, y;
      if (pQH==nullptr) {
         x=0;y=0;
      } else {
         x=pQH->x;y=pQH->y;
      }
      printf("%f,%f\n", x, y);
      fflush(stdout);
      float dx = 180/(pow(2,level+1));
      float dy = 90/(pow(2,level+1));
      float ddx = 180/(pow(2,level+2));
      float ddy = 90/(pow(2,level+2));
      QuadHldr* qh = &resqp->en;
      QuadHldr* cqh = nullptr;
      float cx,cy;
      int xsign=1;
      int ysign=1;
      int minus=-1;
      int cxsign, cysign;
      float dist;
      float nearest=201.3;
      QuadHldr* nearestQH=nullptr;
      QuadNode* nearestpQN=nullptr;
      char nearestInd=0;
      deque<WholeQuadNode>::iterator it,eit;
      WholeQuadNode wqn;
      char lind=0;
      QuadNode* nqn=nullptr;
      QuadHldr* nqh=nullptr;
      if (pseudo) {
         nqn = new QuadNode();
         qh = (QuadHldr*)nqn;
         printf("cqh: %p\n", qh);
      }
      for (lind=0;lind<4; ++lind, ++qh, xsign*=ysign, ysign*=minus) {
         cxsign=xsign;cysign=ysign;
         if (xsign*c.x>=xsign*x && ysign*c.y>=ysign*y) {
            break;
         }
      }
      if (pseudo) {
         qh->qp=(QuadNode*)fpxor(resqp,tQN,ind);
         qp = (QuadNode*)fpxor(nqn, pQN, ind);
      }
      cqh = qh;
      cx = x+cxsign*dx;
      cy = y+cysign*dy;
      ptqh.x=cx;
      ptqh.y=cy;
      if (!qh->fp) {
         nqh = cqh = new QuadHldr();
         printf("cqh: %p\n", cqh);         
         FFJSON* tmp = new FFJSON();
         (*tmp)["location"][0]=c.x;
         (*tmp)["location"][1]=c.y;
         cqh->insert(*tmp, false, level+1, cx, cy, resqp, lind, tQN, tind);
         pseudo=true;
      }
      ptqh.ind=lind;
      if (!pQH)
         goto imNibrs;
      it = pQH->pqcqh->begin();
      eit = pQH->pqcqh->end();
      if (it==eit)
         goto imNibrs;
      // remove far neighbours and add child neighbours
      while(1) {
         if (it==eit)
            break;
         deque<WholeQuadNode>::iterator cit=it;
         ++it;
         int nxsign = cit->xsign;
         int nysign = cit->ysign;
         int rxsign = cit->rxsign;
         int rysign = cit->rysign;
         int ncxsign;
         int ncysign;
         uchar count=0;
         char inverted=1;
         if ((nxsign==cxsign && rxsign!=cxsign) ||
             (nysign==cysign && rysign!=cysign) && !pseudo) {
            pQH->pqcqh->erase(cit);
         } else {
            float qx = cit->x;
            float qy = cit->y;
            float ddx = cit->dx/2;
            float ddy = cit->dy/2;
            QuadNode* qn = cit->qn;
            lind = cit->ind;
            QuadNode* pqn = cit->pqh.node;
            void* vp = (void*)qn;
            set<void*>::iterator qit = qpset.find(vp);
            set<void*>::iterator sit = setffset.find(vp);
            bool isQ = qit != qpset.end();
            bool isS = sit != setffset.end();
            if (!isQ) {
               FFJSON& f = (*(FFJSON*)qn)["location"];
               double fx = (double)f[0];
               double fy = (double)f[1];
               dist = pow(c.x-fx,2)+pow(c.y-fy,2);
               if (dist<nearest) {
                  nearest=dist;
                  nearestQH=(QuadHldr*)qn;
                  nearestInd=lind;
                  nearestpQN=pqn;
               }
               pQH->pqcqh->erase(cit);
               continue;
            }
            bool addOther = false;
           addInv:
            ncxsign=-inverted*rxsign*cxsign;
            ncysign=-inverted*rysign*cysign;
           addChldNbrs:
            float nx = qx + ncxsign*ddx;
            float ny = qy + ncysign*ddy;
            int xind=ncxsign>0?0:1;
            xind<<=1;
            xind|=ncysign>0?0:1;
            QuadHldr* qh = &qn->en+xind;
            if (qh->fp != nullptr) {
               wqn.qn=(QuadNode*)get<0>(bpxor(qh->fp, pqn));
               wqn.x=nx;
               wqn.y=ny;
               wqn.dx=ddx;
               wqn.dy=ddy;
               wqn.xsign=ncxsign;
               wqn.ysign=ncysign;
               wqn.rxsign=nxsign;
               wqn.rysign=nysign;
               wqn.pqh.node=qn;
               wqn.ind=wqn.pqh.ind=lind;
               qcqh.push_back(wqn);
               ++count;
               printf("nbr: %f,%f\n", nx, ny);
               //qh->print(c, qn, xind, pqn, lind);
               dist = pow(c.x-nx,2)+pow(c.y-ny,2);
               if (dist<nearest) {
                  nearest=dist;
                  nearestQH=qh;
                  nearestInd=lind;
                  nearestpQN=pqn;
               }
            }
            if (addOther)
               goto pseudochk;
            if (nxsign==cxsign) {
               ncxsign=-ncxsign;
               addOther=true;
            }
            if (nysign==cysign) {
               ncysign=-ncysign;
               addOther=true;
            }
            if (addOther) {
               goto addChldNbrs;
            }
           pseudochk:
            if (pseudo && !count) {
               inverted=-inverted;
               addOther=false;
               goto addInv;
            }
         }
      }
      // add child neighbours in the node
     imNibrs:
      qh = &resqp->en;
      int nxsign=1;
      int nysign=1;
      for (int i=0,ysign=xsign=1; i<4;
           ++i, ++qh, xsign*=ysign, ysign*=minus) {
         float qx = x+xsign*dx;
         float qy = y+ysign*dy;
         if (!qh->fp || (xsign==cxsign && ysign==cysign)) {
            continue;
         }
         QuadNode* qn = (QuadNode*)get<0>(bpxor(qh->fp,tQN));
         set<void*>::iterator qit = qpset.find(qn);
         if (qit==qpset.end()) {
            if (pseudo) {
               FFJSON& f = (*(FFJSON*)qn)["location"];
               double fx = (double)f[0];
               double fy = (double)f[1];
               dist = pow(c.x-fx,2)+pow(c.y-fy,2);
               if (dist<nearest) {
                  nearest=dist;
                  nearestQH=qh;
                  nearestInd=tind;
                  nearestpQN=tQN;
               }
            }
            continue;
         }
         nxsign = cxsign;
         nysign = cysign;
         float nx = qx + nxsign*ddx;
         float ny = qy + nysign*ddy;
         int xind=nxsign>0?0:1;
         int yind=nysign>0?0:1;
         lind = xind<<1|yind;
         QuadHldr* qqh = &qn->en+lind;
         if (qqh->qp != nullptr) {
            auto aa = bpxor(qqh->qp, resqp);
            wqn.qn=(QuadNode*)get<0>(aa);
            wqn.x=nx;
            wqn.y=ny;
            wqn.dx=ddx;
            wqn.dy=ddy;
            wqn.xsign=nxsign;
            wqn.ysign=nysign;
            wqn.rxsign=xsign;
            wqn.rysign=ysign;
            wqn.pqh.node=resqp;
            wqn.pqh.ind=tind;
            wqn.ind=lind;
            qcqh.push_back(wqn);
            printf("inbr: %f,%f\n", nx, ny);
            dist = pow(c.x-nx,2)+pow(c.y-ny,2);
            if (dist<nearest) {
               nearest=dist;
               nearestQH=qqh;
               nearestInd=i;
               nearestpQN=resqp;
            }
         }
         bool addOther = false;
         if (nxsign==xsign) {
            nxsign=-nxsign;
            addOther=true;
         }
         if (nysign==ysign) {
            nysign=-nysign;
            addOther=true;
         }
         if (addOther) {
            nx = qx + nxsign*ddx;
            ny = qy + nysign*ddy;
            int xind=nxsign>0?0:1;
            int yind=nysign>0?0:1;
            lind = xind<<1|yind;
            QuadHldr* qqh = &qn->en+lind;
            if (qqh->fp != nullptr) {
               auto aa = bpxor(qqh->qp, resqp);
               wqn.qn=(QuadNode*)get<0>(aa);
               wqn.x=nx;
               wqn.y=ny;
               wqn.dx=ddx;
               wqn.dy=ddy;
               wqn.xsign=nxsign;
               wqn.ysign=nysign;
               wqn.rxsign=xsign;
               wqn.rysign=ysign;
               wqn.pqh.node=qn;
               wqn.ind=lind;
               wqn.pqh.ind=tind;
               qcqh.push_back(wqn);
               printf("inbr: %f,%f\n", nx, ny);
               dist = pow(c.x-nx,2)+pow(c.y-ny,2);
               if (dist<nearest) {
                  nearest=dist;
                  nearestQH=qqh;
                  nearestInd=i;
                  nearestpQN=resqp;
               }
            }
         }
      }
      if (!qcqh.empty() || (pQH && !pQH->pqcqh->empty()) || !pseudo) {
         NdNPrn pCenFF = {};
         ptqh.x=cx; ptqh.y=cy;
         cqh->getPointsFromQuad(pts, c, minPts, level+1, &ptqh, pseudo);
         if (pts.size()<=1) {
            if (pts.size()==1) {
               pCenFF = pts.back();
            } else {
               if (!pseudo) {
                  pCenFF.qh = cqh;
                  pCenFF.prn = tQN;
               } else {
                  pCenFF.qh = nearestQH;
                  pCenFF.prn = nearestpQN;
               }
               pts.push_back(pCenFF);
            }
            auto aa = getNode(pCenFF);
            pCenFF.qh->findNeighbours(pts, pCenFF.prn, get<1>(aa));
         }
      } else if (nearestQH) {
         pts.push_back({nearestQH, nearestpQN});
      }
      if (pseudo) {
         if (nqn) {
            nqn->del(resqp, ptqh.ind, tQN, tind);
            delete nqn;
         }
         if (nqh) {
            nqh->del(resqp, ptqh.ind, tQN, tind);
            delete nqh;
         }
      }
   }
   return level;
}

void makeThngsTree () {
   QuadNode q;
   FFJSON& things = config["virtualWebHosts"]["underconstruction"]["things"];
   FFJSON::Iterator it = things.begin();
   FFJSON::Iterator tit;
   while (it!= things.end()) {
      string thing = it.getIndex();
      tit = it->begin();
      while (tit!=it->end()) {
         QuadHldr& qh = thnsTreeMap[thing];
         uint level=qh.insert((*tit->val.fptr));
         Circle c;
         c.nf=(FFJSON*)1;
         qh.print(c);
         printf ("insertLevel: %u\n", level);
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
   printf("pvc size:%zu:%zu\n"
          "pvc: %lx, a: %02x\n"
          "qh size:%zu:%zu\n"
          "qp size:%zu:%zu\n"
          "qp mod size: %lu\n"
          "fp  size:%zu:%zu\n", malloc_usable_size(pvc), sizeof(ppvc),
          (size_t)pvc, a, malloc_usable_size(qh), sizeof(*qh),
          malloc_usable_size(qp2), sizeof(Qn2), ((long)qp+3)%sizeof(*qp),
          malloc_usable_size(fp), sizeof(*fp));
   
   makeThngsTree();
   Circle c = {0.174, 0.27, 10.5};
   //Circle c = {13.004896,77.753464, 10.5};
   printf("c: %f,%f\n", c.x, c.y);
   CompareByDistanceToCenter comp(c.x, c.y);
   vector<NdNPrn> pts;
   thnsTreeMap["PTMN"].getPointsFromQuad(pts, c);
   std::vector<NdNPrn>::iterator it = pts.begin();
   it = pts.begin();
   while (it!=pts.end()) {
      FFJSON* fp = (FFJSON*)get<0>(getNode(*it));
      printf("x: %lf, y: %lf\n",(double)(*fp)["location"][0],
              (double)(*fp)["location"][1]);
      ++it;
   }
   c.nf=nullptr;
   thnsTreeMap["PTMN"].print(c);
   printf("nf: %s\n", (*c.nf)["location"].stringify().c_str());
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
