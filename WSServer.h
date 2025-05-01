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
#include <queue>

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
   FFJSON* nf;
   bool grabIfNearest (FFJSON& f);
};
union QuadHldr;
struct WholeQuadNode;
struct ParentQuadHldr {
   ParentQuadHldr (
      deque<WholeQuadNode>* pqcqh=nullptr, QuadNode* node=nullptr,
      ParentQuadHldr* pQH = nullptr, float x = 0, float y = 0) 
      : pqcqh(pqcqh), node(node), pQH(pQH), x(x), y(y)
      {}
   QuadNode* node;
   ParentQuadHldr* pQH;
   float x,y;
   deque<WholeQuadNode>* pqcqh;
   char ind;
};
struct WholeQuadNode {
   QuadNode* qn;
   float x,y,dx,dy;
   char xsign,ysign,rxsign,rysign;
   ParentQuadHldr pqh;
   char ind;
};
struct NdNPrn;
typedef unsigned uchar;
struct Direction {
   char x;
   char y;
   char abs ();
};
struct CompareByDistanceToCenter;
union QuadHldr {
   QuadHldr () {
      qp=nullptr;
   };
   QuadNode* qp;
   FFJSON* fp;
   set<FFJSON*>* sp;
   uint insert (
      FFJSON& rF, bool deleteLeaf = false, uint level = 0,
      float x = 0.0, float y = 0.0, QuadNode* tQN = nullptr, char tind=0,
      QuadNode* pQN = nullptr, char ind=0
   );
   uint getPointsFromQuad (
      vector<NdNPrn>& pts, Circle& c,
      uint minPts = 20, uint level=0, ParentQuadHldr* pQH = nullptr,
      bool pseudo = false, float shortestRDist = 201.3
   );
   // uint getPointsFromRadius (
   //    set<FFJSON*, CompareByDistanceToCenter>& pts, Circle& c, uint minPts=30,
   //    uint level=0, float x=0, float y=0, QuadNode* pQN = nullptr
   // );
   // uint addAllLeavesInRadius (set<FFJSON*,CompareByDistanceToCenter>& pts,
   //                            QuadNode* pQN);
   uint findNeighbours (vector<NdNPrn>& pts, uint max=0,
                        QuadNode* pQN=nullptr, char ind=0, Direction d = {0}, vector<NdNPrn>* apts = nullptr,
                        bool notChild = false);
   uint addChildrenOnEdge (Direction d, vector<NdNPrn>& pts,
                           vector<NdNPrn>* apts, QuadNode* pQN,
                           char ind, uint initsz = 0);
   uint addThis (Direction d, vector<NdNPrn>& pts, vector<NdNPrn>* apts,
                 QuadNode* pQN, char ind = 0, int initsz = 0);
   void print (Circle& c, uint level = 0, QuadNode* tQN = nullptr,
               char tind = 0, QuadNode* pQN = nullptr, char ind = 0);
   void del (QuadNode* tQN = nullptr, char tind = 0,
             QuadNode* pQN = nullptr, char ind = 0);
};

struct CompareByDistanceToCenter {
   bool operator () (FFJSON* pf1, FFJSON* pf2) const {
      FFJSON& f1 = *pf1;
      FFJSON& f2 = *pf2;
      float x1 = f1["location"][0];
      float y1 = f1["location"][1];
      float x2 = f2["location"][0];
      float y2 = f2["location"][1];
      if (x1==x2 && y1==y2) {
         return f1 < f2;
      }
      float r1 = pow(cx-x1,2) + pow(cy-y1,2);
      float r2 = pow(cx-x2,2) + pow(cy-y2,2);
      return (r1 < r2);
   }
   CompareByDistanceToCenter (float cx, float cy):cx(cx),cy(cy) {}
   float cx,cy;
};
struct QuadNode {
   QuadHldr en;
   QuadHldr es;
   QuadHldr wn;
   QuadHldr ws;
   QuadNode ();
   void del (QuadNode* tQN = nullptr, char tind = 0,
             QuadNode* pQN = nullptr, char ind = 0);
};
struct Qn2 
{
   QuadNode* p1;
   QuadNode* p2;
   QuadNode* p3;
   QuadNode* p4;
};
struct NdNPrn {
   QuadHldr* qh;
   QuadNode* prn;
   Direction d;
   char ind;
};

#endif /* WSSERVER_H */
