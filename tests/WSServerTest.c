/*
 * File:   WSServerTest.c
 * Author: Gowtham
 *
 * Created on Dec 12, 2013, 11:32:23 AM
 */

#include <stdio.h>
#include <stdlib.h>
#include "WSServer.h"
#include <vector>
#include <memory>

/*
 * Simple C Test Suite
 */

using namespace std;
void testSizes () {
   vector<char>* pvc = new vector<char>();
   vector<int>* pvi = new vector<int>();
   QuadNode* pq = new QuadNode();
   printf("pvc size:%xd\n"
          "pvi size:%xd\n"
          "pq  size:%xd\n", pvc, pvi, pq);
   delete pvc;
   delete pvi;
   delete pq;
}

void testWSServer () {
    int argc=1;
    char argv[1][9]={"WSServer"};
    int result = WSServer(argc, argv);
    if (1 /*check result*/) {
        printf("%%TEST_FAILED%% time=0 testname=testWSServer (WSServerTest) message=error message sample\n");
    }
}

int main(int argc, char** argv) {
    printf("%%SUITE_STARTING%% WSServerTest\n");
    printf("%%SUITE_STARTED%%\n");
    
    printf("%%TEST_STARTED%%  testWSServer (WSServerTest)\n");
    //testWSServer();
    testSizes();
    printf("%%TEST_FINISHED%% time=0 testWSServer (WSServerTest)\n");

    printf("%%SUITE_FINISHED%% time=0\n");

    return (EXIT_SUCCESS);
}
