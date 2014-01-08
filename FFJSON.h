/* 
 * File:   FFJSON.h
 * Author: raheem
 *
 * Created on November 29, 2013, 4:29 PM
 */

#ifndef FFJSON_H
#define	FFJSON_H

#include <string>
#include <vector>
#include <map>
#include <exception>

class FFJSON {
public:

    class Exception : std::exception {
    public:

        Exception(std::string e) : identifier(e) {

        }

        const char* what() const throw () {
            return this->identifier.c_str();
        }

        ~Exception() throw () {
        }
    private:
        std::string identifier;
    };
    FFJSON();
    FFJSON(std::string& ffjson);
    virtual ~FFJSON();

    enum FFJSON_OBJ_TYPE {
        STRING, NUMBER, OBJECT, ARRAY, BOOL, UNRECOGNIZED
    };

    FFJSON_OBJ_TYPE type;
    std::string ffjson;
    int length = 0;
    //const char FFESCSTR[9] = "FFESCSTR";
    static void trimWhites(std::string& s);
    static void trimQuotes(std::string& s);
    FFJSON_OBJ_TYPE objectType(std::string ffjson);
    FFJSON& operator[](std::string prop);
    FFJSON& operator[](int index);
    std::string stringify(bool encode_to_base64);
    union FFValue {
        char * string;
        std::vector<FFJSON*>* array;
        std::map<std::string, FFJSON*>* pairs;
        double number;
        bool boolean;
    } val;
private:

};

#endif	/* FFJSON_H */
