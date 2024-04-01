#include "cap.h"
#define cimg_use_jpeg
#include <CImg.h>
#include <jpeglib.h>
#include <string>
#include <iostream>
using namespace cimg_library;

inline double crand() {
   return 1-2*cimg::rand();
}

cap::cap (
   std::string text, std::string filename, int count, int width, int height,
   int offset, int quality, int fontSize
) :text(text), filename(filename), count(count), width(width), height(height),
   offset(offset), quality(quality), fontSize(fontSize)
{}

cap::~cap() {
   
}

int cap::save(){
   const char* captcha_text((text).c_str());
   const char* file_o((filename).c_str());
   
  // Create captcha image
  //----------------------

  // Write colored and distorted text
   CImg<unsigned char> captcha(width,height,1,3,0), color(3);
   const unsigned char red[] = { 255,0,0 }, green[] = { 0,255,0 },
      blue[] = { 0,0,255 };

   char letter[2] = { 0 };
   for (unsigned int k = 0; k<count; ++k) {
      CImg<unsigned char> tmp;
      *letter = captcha_text[k];
      if (*letter) {
         cimg_forX(color,i) color[i] = (unsigned char)(128+(std::rand()%127));
         tmp.draw_text((int)(2+8*cimg::rand()),
                       (int)(12*cimg::rand()),
                       letter,
                       red,
                       0,
                       1,
                       fontSize).resize(-100,-100,1,3);
//      const unsigned int dir = std::rand()%4, wph = tmp.width()+tmp.height();
//      cimg_forXYC(tmp,x,y,v) {
//        const int val = dir==0?x+y:(dir==1?x+tmp.height()-y:(dir==2?y+tmp.width()-x:tmp.width()-x+tmp.height()-y));
//        tmp(x,y,v) = (unsigned char)cimg::max(0.0f,cimg::min(255.0f,1.5f*tmp(x,y,v)*val/wph));
//      }
      //if (std::rand()%2) tmp = (tmp.get_dilate(3)-=tmp);
      //tmp.blur((float)cimg::rand()*0.8f).normalize(0,255);
         const float sin_offset = (float)crand()*3,
            sin_freq = (float)crand()/7;
         cimg_forYC(captcha,y,v)
            captcha.get_shared_row(y,0,v).shift(
               (int)(4*std::cos(y*sin_freq+sin_offset)));
         captcha.draw_image(count+offset*k,tmp);
      }
   }

  // Add geometric and random noise
   CImg<unsigned char> copy = (+captcha).fill(0);
   for (unsigned int l = 0; l<3; ++l) {
      if (l) copy.blur(0.5f).normalize(0,148);
      for (unsigned int k = 0; k<10; ++k) {
         cimg_forX(color,i) color[i] = (unsigned char)(128 + cimg::rand()*127);
         if (cimg::rand()<0.5f)
            copy.draw_circle(
               (int)(cimg::rand()*captcha.width()), (int)(cimg::rand()*captcha.height()),
               (int)(cimg::rand()*30),color.data(),0.6f,~0U);
         else copy.draw_line((int)(cimg::rand()*captcha.width()),
                             (int)(cimg::rand()*captcha.height()),
                             (int)(cimg::rand()*captcha.width()),
                             (int)(cimg::rand()*captcha.height()),
                             color.data(),0.6f);
      }
   }
   captcha|=copy;
   captcha.noise(10,2);

   captcha = (+captcha).fill(255) - captcha;

   // Write output image and captcha text
   //-------------------------------------
   //std::printf("%s\n",captcha_text);

   captcha.save_jpeg(file_o, quality);

   //std::printf("*********************\n");
   return 0;

}
