/* 
 * File:   WSServer.h
 * Author: Gowtham
 *
 * Created on December 12, 2013, 6:34 PM
 */

#ifndef WSSERVER_H
#define WSSERVER_H

#define MAX_ECHO_PAYLOAD 1024
#define LWS_NO_CLIENT

#include "FerryStream.h"
#include "mongoose.h"
#include <FFJSON.h>
#include <string>
#include <map>
#include <list>
#include <thread>

using namespace std;
using namespace placeholders;

class WSServer {
public:
   WSServer(
      const char* pcHostName,
      int iDebugLevel=15,
      int iPort=8080,
      int iSecurePort=0,
      const char* pcSSLCertFilePath="",
      const char* pcSSLPrivKeyFilePath="",
      const char* pcSSLCAFilePath="",
      bool bDaemonize=false,
      int iRateUs=0,
      const char* pcInterface="",
      const char* pcClient="",
      int iOpts=0,
      int iSysLogOptions=0
   );
   virtual ~WSServer();
private:
/*
   void fn (struct mg_connection *c, int ev, void *ev_data, void *fn_data);
   void fn_tls (struct mg_connection *c, int ev, void *ev_data, void *fn_data);
   void tls_ntls_common (struct mg_connection* c, int ev, void* ev_data,
                         void* fn_data);
   void mailfn (struct mg_connection *c, int ev, void *ev_data, void *fn_data);
   struct mg_mgr mgr, mail_mgr;

   thread_local static WSServer* toHttpListen;
   static void gfn (struct mg_connection *c, int ev, void *ev_data,
                    void *fn_data);
   static void gfn_tls (struct mg_connection *c, int ev, void *ev_data,
                        void *fn_data);
   static void gmailfn (struct mg_connection *c, int ev, void *ev_data,
                        void *fn_data);
   const char* server = "tcp://ferryfair.com:25";
   const char* user = "Necktwi";
   const char* pass = "tornshoes";
   char* to = "gowtham.kudupudi@gmail.com";

   const char* from = "FerryFair";
   const char* subj = "Test email from Mongoose library!";
   const char* mesg = "Hi!\nThis is a test message.\nBye.";

   bool s_quit = false;
*/
};

struct QuadNode;
struct Circle {
   float x;
   float y;
   float r;
};
union QuadHldr;
struct WholeQuadNode;
struct ParentQuadHldr {
   ParentQuadHldr (deque<WholeQuadNode>* pqcqh=nullptr, QuadHldr* hldr=nullptr,
                   ParentQuadHldr* pQH = nullptr, float x = 0, float y = 0) 
      : pqcqh(pqcqh), hldr(hldr), pQH(pQH), x(x), y(y)
      {
            
      }
   QuadHldr* hldr;
   ParentQuadHldr* pQH;
   float x,y;
   deque<WholeQuadNode>* pqcqh;
};
struct WholeQuadNode {
   QuadHldr* qh;
   float x,y,dx,dy;
   int xsign,ysign,rxsign,rysign;
   ParentQuadHldr pqh;
};

union QuadHldr {
   QuadHldr () {
      qp=nullptr;
   };
   QuadNode* qp;
   FFJSON* fp;
   uint insert (
      FFJSON& rF, uint level = 0,
      float x = 0.0, float y = 0.0
   );
   uint getPointsFromQuad (
      vector<FFJSON*>& pts, Circle& c,
      uint minPts = 20, uint level=0, ParentQuadHldr* pQH = nullptr,
      bool pseudo = false, float shortestRDist = 201.3
   );
};

struct QuadNode {
   QuadHldr en;
   QuadHldr es;
   QuadHldr wn;
   QuadHldr ws;
};

#endif /* WSSERVER_H */
