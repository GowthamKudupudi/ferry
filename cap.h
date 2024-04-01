#ifndef SV_H
#define SV_H
#include <string>


class cap {
public:
   cap (
      std::string text, std::string filename, int count, int width = 200,
      int height = 50, int offset = 0, int quality = 0, int fontSize = 0
   );
   ~cap();
   int save();
  
private:
   std::string text;
   std::string filename;
	int count;
	int width;
	int height;
	int offset;
	int quality;
	int fontSize;
};

#endif
