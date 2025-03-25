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

const char* mail_server = "tcp://ferryfair.com:25";
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
            bid = random_alphnuma_string();
           bidcheck:
            if (rbs[bid]) {
               bid=random_alphnuma_string();
               goto bidcheck;
            }
            rbs[bid]["ip"]=c->rem.ip;
           gotbid:
            if ((uint32_t)rbs[bid]["ip"]!=c->rem.ip) {
               mg_http_reply(c, 200, headers, "{%Q:%Q}", "error",
                             "ipChanged");
               goto done;
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
            thnsTree.getPointsFromRadius(pts, C);
            atit = pts.begin();
            int k=reply["things"].size;
            while (atit != pts.end()) {
               if (!username || strcmp((**atit)["user"],username)) {
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
            } else {
               user["name"]=username;
            }
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
         FFJSON::Iterator tit = things.find(string(srchStr)+'*');
         FFJSON::Iterator atit;
         FFJSON reply;
         int k=0;
         while (tit != things.end()) {
            atit = tit->begin();
            while (atit != tit->end()) {
               FFJSON::FeaturedMember fm =
                  atit->getFeaturedMember(FFJSON::FM_LINK);
               if (!username || strcmp((*fm.link)[1].c_str(),username)) {
                  (reply["things"][k]=*atit).
                     setEFlag(FFJSON::LONG_LAST_LN);
                  ++k;
               }
               ++atit;
            }
            ++tit;
            if (!strstr(tit.getIndex().c_str(), srchStr)) {
               break;
            }
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
               while ((int)uthings[j]["id"]!=(int)cthings[i]["id"] &&
                      j<uthings.size) {
                  ++j;
               }
               if (!uthings[j]) {
                  cthings[i]["id"] = uthings.size?
                     ((int)uthings[j-1]["id"]) + 1 : 0;
                  FFJSON& ln = uthings[j]["user"].addLink(users, username);
                  if (!ln)
                     delete &ln;
               } else if (strcasecmp(cthings[i]["name"],
                                     uthings[j]["name"])) {
                  things[(ccp)uthings[j]["name"]].erase(&uthings[j]);
               }
               uthings[j]=cthings[i];
               string tname((ccp)uthings[j]["name"]);
               strLower(tname);
               FFJSON& ln = things[tname][].addLink(
                  vhost, string("users.")+username+".things."+to_string(i));
               if (!ln)
                  delete &ln;
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

uint QuadHldr::insert(FFJSON& rF, uint level, float x, float y,
                      bool deleteLeaf) {
   printf("x,y: %lf,%lf\n", x, y);
   if (fp==nullptr) {
      fp=&rF;
   } else if (malloc_usable_size(fp)==24) {
      if (deleteLeaf) {
         if (fp==&rF)
            fp=nullptr;
         return level;
      }
      if (fp==&rF)
         return level;
      FFJSON& tmp = *fp;
      qp = new QuadNode();
      if ((double)tmp["location"][0] >= x) {
         if ((double)tmp["location"][1] >= y) {
            qp->en.fp=&tmp;
         } else {
            qp->es.fp=&tmp;
         }
      } else {
         if ((double)tmp["location"][1] >= y) {
            qp->wn.fp=&tmp;
         } else {
            qp->ws.fp=&tmp;
         }
      }
      return insert(rF,level,x,y,deleteLeaf);
   } else {
      if ((double)rF["location"][0] >= x) {
         if ((double)rF["location"][1] >= y) {
            if (qp->en.fp==nullptr) {
               qp->en.fp=&rF;
            } else {
               return qp->en.insert(
                  rF, level+1, x+180/(pow(2,level+1)),
                  y+90/(pow(2,level+1)));
            }
         } else {
            if (qp->es.fp==nullptr) {
               qp->es.fp=&rF;
            } else {
               return qp->es.insert(
                  rF, level+1, x+180/(pow(2, level+1)), y-90/(pow(2, level+1)), deleteLeaf);
            }
         }
      } else {
         if ((double)rF["location"][1] >= y) {
            if (qp->wn.fp==nullptr) {
               qp->wn.fp=&rF;
            } else {
               return qp->wn.insert(
                  rF, level+1, x-180/(pow(2, level+1)), y+90/(pow(2, level+1)), deleteLeaf);
            }
         } else {
            if (qp->ws.fp==nullptr) {
               qp->ws.fp=&rF;
            } else {
               return qp->ws.insert(
                  rF, level+1, x-180/(pow(2, level+1)), y-90/(pow(2, level+1)), deleteLeaf);
            }
         }
      }
   }
   return level;
}
uint QuadHldr::addAllLeavesInRadius (
   set<FFJSON*,CompareByDistanceToCenter>& pts
) {
   if (fp==nullptr) {
      return 0;
   }
   if (malloc_usable_size(fp)==24) {
      pts.insert(fp);
      return 1;
   }
   QuadHldr* qh = &qp->en;
   uint leafCount=0;
   for (int i=0;i<4; ++i, ++qh) {
      leafCount+=qh->addAllLeavesInRadius(pts);
   }
   return leafCount;
};

uint QuadHldr::getPointsFromRadius (
   set<FFJSON*, CompareByDistanceToCenter>& pts, Circle& c, uint minPts,
   uint level, float x, float y
) {
   fflush(stdout);
   if (fp==nullptr) {
      return 0;
   } else if (malloc_usable_size(fp)==24) {
      float cx = (float)(*fp)["location"][0];
      float cy = (float)(*fp)["location"][1];
      float dist = pow(pow(cx-c.x,2)+pow(cy-c.y,2), 0.5);
      if (dist<=c.r) {
         pts.insert(fp);
         return 1;
      }
      return 0;
   } else {
      QuadHldr* qh = &qp->en;
      float dx = 180/(pow(2,level+1));
      float dy = 90/(pow(2,level+1));
      int xsign=1;
      int ysign=1;
      int minus=-1;
      float ddist = pow(pow(dx,2)+pow(dy,2), 0.5);
      uint leafCount=0;
      for (int i=0;i<4; ++i, ++qh, xsign*=ysign, ysign*=minus) {
         if (qh->qp==nullptr)
            continue;
         float cx = x + xsign*dx;
         float cy = y + ysign*dy;
         float dist = pow(pow(cx-c.x,2)+pow(cy-c.y,2), 0.5);
         if (dist-ddist<=c.r) {
            leafCount+=
               qh->getPointsFromRadius(pts, c, minPts, level+1, cx, cy);
            if (leafCount>=minPts)
               return leafCount;
         }
      }
      return leafCount;
   }
}

uint QuadHldr::getPointsFromQuad (
   set<FFJSON*, CompareByDistanceToCenter>& pts, Circle& c, uint minPts,
   uint level, ParentQuadHldr* pQH,  bool pseudo, float shortestRDist
) {
   if (fp==nullptr) {
   } else if (malloc_usable_size(fp)==24) {
      pts.insert(fp);
   } else {
      deque<WholeQuadNode> qcqh;
      ParentQuadHldr ptqh(&qcqh);
      ptqh.hldr=this;
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
      QuadHldr* qh = &qp->en;
      QuadHldr* cqh = nullptr;
      float cx,cy;
      int xsign=1;
      int ysign=1;
      int minus=-1;
      int cxsign, cysign;
      float dist;
      float nearest=201.3;
      QuadHldr* nearestQH;
      deque<WholeQuadNode>::iterator it,eit;
      WholeQuadNode wqn;
      for (int i=0;i<4; ++i, ++qh, xsign*=ysign, ysign*=minus) {
         cxsign=xsign;cysign=ysign;
         if (xsign*c.x>=xsign*x && ysign*c.y>=ysign*y) {
            break;
         }
      }
      cqh = qh;
      cx = x+cxsign*dx;
      cy = y+cysign*dy;
      ptqh.x=cx;
      ptqh.y=cy;
      bool pseudo = false;
      if (!qh->fp) {
         FFJSON tmp;
         tmp["location"][0]=c.x;
         tmp["location"][1]=c.y;
         cqh = new QuadHldr();
         cqh->insert(tmp, level+1, cx, cy);
         pseudo=true;
      }
      if (!pQH)
         goto imNibrs;
      it = pQH->pqcqh->begin();
      eit = pQH->pqcqh->end();
      if (it==eit)
         goto imNibrs;
      --eit;
      while(1) {
         bool q = false;
         if (it==eit)
            q=true;
         deque<WholeQuadNode>::iterator cit=it;
         ++it;
         int nxsign = cit->xsign;
         int nysign = cit->ysign;
         int rxsign = cit->rxsign;
         int rysign = cit->rysign;
         int ncxsign;
         int ncysign;
         if ((nxsign==cxsign && rxsign!=cxsign) ||
             (nysign==cysign && rysign!=cysign)) {
            pQH->pqcqh->erase(cit);
         } else {
            ncxsign=-rxsign*cxsign;
            ncysign=-rysign*cysign;
            float qx = cit->x;
            float qy = cit->y;
            float ddx = cit->dx/2;
            float ddy = cit->dy/2;
            float nx = qx + ncxsign*ddx;
            float ny = qy + ncysign*ddy;
            QuadNode* qn = cit->qh->qp;
            if (malloc_usable_size(qn)==24) {
               continue;
            }
            int xind=nxsign>0?0:1;
            xind<<=1;
            xind|=nysign>0?0:1;
            wqn.qh=&qn->en+xind;
            if (wqn.qh->qp != nullptr) {
               wqn.x=nx;
               wqn.y=ny;
               wqn.dx=ddx;
               wqn.dy=ddy;
               wqn.xsign=ncxsign;
               wqn.ysign=ncysign;
               wqn.rxsign=nxsign;
               wqn.rysign=nysign;
               qcqh.push_back(wqn);
               dist = pow(c.x-nx,2)+pow(c.y-ny,2);
               if (dist<nearest) {
                  nearest=dist;
                  nearestQH=wqn.qh;
               }
            }
            bool addOther = false;
            if (nxsign==cxsign) {
               ncxsign=-ncxsign;
               addOther=true;
            }
            if (nysign==cysign) {
               ncysign=-ncysign;
               addOther=true;
            }
            if (addOther) {
               nx = qx + ncxsign*ddx;
               ny = qy + ncysign*ddy;
               int xind=nxsign>0?0:1;
               xind<<=1;
               xind|=nysign>0?0:1;
               wqn.qh=&qn->en+xind;
               if (wqn.qh->qp != nullptr) {
                  wqn.x=nx;
                  wqn.y=ny;
                  wqn.dx=ddx;
                  wqn.dy=ddy;
                  wqn.xsign=ncxsign;
                  wqn.ysign=ncysign;
                  wqn.rxsign=nxsign;
                  wqn.rysign=nysign;
                  qcqh.push_back(wqn);
                  dist = pow(c.x-nx,2)+pow(c.y-ny,2);
                  if (dist<nearest) {
                     nearest=dist;
                     nearestQH=wqn.qh;
                  }
               }
            }
         }
      }
      
     imNibrs:
      qh = &qp->en;
      for (int i=0,ysign=xsign=1; i<4;
           ++i, ++qh, xsign*=ysign, ysign*=minus) {
         float qx = x+xsign*dx;
         float qy = y+ysign*dy;
         if (!qh->fp || malloc_usable_size(qh->fp)==24 ||
             (xsign==cxsign && ysign==cysign)) {
            continue;
         }
         if (xsign!=cxsign) {
            if (ysign!=cysign) {
               float nx = qx - cxsign*ddx;
               float ny = qy - cysign*ddy;
               int xind=-cxsign>0?0:1;
               int yind=-cysign>0?0:1;
               xind<<=1;
               xind|=yind;
               wqn.qh=&qh->qp->en+xind;
               if (wqn.qh->qp != nullptr) {
                  wqn.x=nx;
                  wqn.y=ny;
                  wqn.dx=ddx;
                  wqn.dy=ddy;
                  wqn.xsign=-cxsign;
                  wqn.ysign=-cysign;
                  wqn.rxsign=xsign;
                  wqn.rysign=ysign;
                  qcqh.push_back(wqn);
                  dist = pow(c.x-nx,2)+pow(c.y-ny,2);
                  if (dist<nearest) {
                     nearest=dist;
                     nearestQH=wqn.qh;
                  }
               }
            } else {
               float nx = qx - cxsign*ddx;
               float ny = qy + cysign*ddy;
               int xind=-cxsign>0?0:1;
               int yind=cysign>0?0:1;
               xind<<=1;
               xind|=yind;
               wqn.qh=&qh->qp->en+xind;
               if (wqn.qh->qp != nullptr) {
                  wqn.x=nx;
                  wqn.y=ny;
                  wqn.dx=ddx;
                  wqn.dy=ddy;
                  wqn.xsign=-cxsign;
                  wqn.ysign=cysign;
                  wqn.rxsign=xsign;
                  wqn.rysign=ysign;
                  qcqh.push_back(wqn);
               }
               ny = qy - cysign*ddy;
               xind=-cxsign>0?0:1;
               yind=-cysign>0?0:1;
               xind<<=1;
               xind|=yind;
               wqn.qh=&qh->qp->en+xind;
               if (wqn.qh->qp != nullptr) {
                  wqn.x=nx;
                  wqn.y=ny;
                  wqn.dx=ddx;
                  wqn.dy=ddy;
                  wqn.xsign=-cxsign;
                  wqn.ysign=-cysign;
                  wqn.rxsign=xsign;
                  wqn.rysign=ysign;
                  qcqh.push_back(wqn);
                  dist = pow(c.x-nx,2)+pow(c.y-ny,2);
                  if (dist<nearest) {
                     nearest=dist;
                     nearestQH=wqn.qh;
                  }
               }
            }
         } else {
            if (ysign!=cysign) {
               float nx = qx + cxsign*ddx;
               float ny = qy - cysign*ddy;
               int xind=cxsign>0?0:1;
               int yind=-cysign>0?0:1;
               xind<<=1;
               xind|=yind;
               wqn.qh=&qh->qp->en+xind;
               if (wqn.qh->qp != nullptr) {
                  wqn.x=nx;
                  wqn.y=ny;
                  wqn.dx=ddx;
                  wqn.dy=ddy;
                  wqn.xsign=cxsign;
                  wqn.ysign=-cysign;
                  wqn.rxsign=xsign;
                  wqn.rysign=ysign;
                  qcqh.push_back(wqn);
                  dist = pow(c.x-nx,2)+pow(c.y-ny,2);
                  if (dist<nearest) {
                     nearest=dist;
                     nearestQH=wqn.qh;
                  }
               }
               nx = qx - cxsign*ddx;
               xind=-cxsign>0?0:1;
               yind=-cysign>0?0:1;
               xind<<=1;
               xind|=yind;
               wqn.qh=&qh->qp->en+xind;
               if (wqn.qh->qp != nullptr) {
                  wqn.x=nx;
                  wqn.y=ny;
                  wqn.dx=ddx;
                  wqn.dy=ddy;
                  wqn.xsign=-cxsign;
                  wqn.ysign=-cysign;
                  wqn.rxsign=xsign;
                  wqn.rysign=ysign;
                  qcqh.push_back(wqn);
                  dist = pow(c.x-nx,2)+pow(c.y-ny,2);
                  if (dist<nearest) {
                     nearest=dist;
                     nearestQH=wqn.qh;
                  }
               }
            }
         }
      }
   
      if (cqh) {
         if (!qcqh.empty() || (pQH && !pQH->pqcqh->empty()) || !pseudo) {
            ptqh.x=cx; ptqh.y=cy;
            cqh->getPointsFromQuad(pts, c, minPts, level+1, &ptqh, pseudo);
            if (pts.size()==0) {
               if (!pseudo) {
                  pts.insert(cqh->fp);
               } else {
                  pts.insert(nearestQH->fp);
               }
            }
         }
         if (pseudo) {
            delete cqh;
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
         uint level=thnsTree.insert((*tit->val.fptr));
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
   makeThngsTree();
   Circle c = {12.91, 77.61, 0.5};
   CompareByDistanceToCenter comp(c.x, c.y);
   std::set<FFJSON*, CompareByDistanceToCenter> pts(comp);
   //thnsTreeMap["batman"].getPointsFromQuad(pts, c);
   thnsTree.getPointsFromRadius(pts, c);
   std::set<FFJSON*, CompareByDistanceToCenter>::iterator it = pts.begin();
   it = pts.begin();
   while (it!=pts.end()) {
      printf("x: %lff, y: %lf\n",(double)(*(*it))["location"][0],
             (double)(*(*it))["location"][1]);
      ++it;
   }
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
