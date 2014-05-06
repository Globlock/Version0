/*
----------------------------------------------------------------
Author: Alex Quigley
Project Info: 4th Year as part of BSHCE4, with the National College of Ireland
Project Name: Globlock
Project Title: 2 Factor Document Access control repository and file tokenization
Project Overview: Globlock provides a document management system and file repository with 2 factor authentication, using open source hardware and software.
Version: 1.0

Implementation:
  [Header Start][Identifier][Footer][Optional Data]
  E.G.   #X#streamofdata
  
  #H# = Handshake
  #R# = Report results
  #D# = Display Data on LCD
  #T# = Test RFID

----------------------------------------------------------------
Controller Board: Arduino UNO R3
----------------------------------------------------------------

Code developed and modified from codeproject tutorial, AVAILABLE here:
http://www.codeproject.com/Articles/473828/Arduino-Csharp-and-Serial-Interface
----------------------------------------------------------------
*/

/* 
INCLUDES, METHODS, GLOBAL & VARIABLE DECLARATIONS 
---------------------------------------------------------------- */
/*   INCLUDES   */
#include <WString.h> //Official Arduino string library

/*   CONSTANTS   */
#define GLOBLOCK_INDICATOR_LED 13
#define GLOBLOCK_ACTION_SUCCESS 0
#define GLOBLOCK_ACTION_FAILURE -1
#define GLOBLOCK_SERIAL_DATA_UNAVAILABLE 250
#define GLOBLOCK_SERIAL_DATA_STX "\x02"
#define GLOBLOCK_SERIAL_DATA_ETX "\x03"
#define GLOBLOCK_SERIAL_DATA_DLR "|"
#define GLOBLOCK_SERIAL_DATA_HDR '#'
#define GLOBLOCK_SERIAL_DATA_FTR '#'
#define GLOBLOCK_SERIAL_BAUDRATE 9600

/*   VARIABLES  */
String command = "";

/*   METHOD DECLARATIONS   */
int readSerialInputString(String *inputData);
void returnHandshake();
void displayReport();
void displayData();
void handleError(String errorMessage);
boolean testRFID();
/*
---------------------------------------------------------------- */

/* 
METHODS
---------------------------------------------------------------- */

/*  
SETUP  
* Initialize serial to Baudrate and set pinmode for indicator led
*/
void setup(){
  pinMode(GLOBLOCK_INDICATOR_LED, OUTPUT);
  Serial.begin(GLOBLOCK_SERIAL_BAUDRATE);
}

/*  
LOOP  
* Tests 
*/
void loop(){
  String inputData = "";
  char dataHeader, dataType;
  int inputResult = readSerialInput(&inputData);

  if (inputResult == GLOBLOCK_ACTION_SUCCESS) {
    
    dataHeader = inputData.charAt(0);
    dataType = inputData.charAt(1);
    
    switch (dataType) {
      case 'H'://Handshake 
        returnHandshake();   
        break;
      case 'R'://Report to LCD
        displayReport();    
        break;
      case 'D'://Display to LCD    
        displayData();
        break;
      case 'T'://Display to LCD    
        testRFID();
        break;
      case '\n':
        break;
      default:
        handleError("INVALID HEADER");
        break;
    }
  } else if (inputResult == GLOBLOCK_SERIAL_DATA_UNAVAILABLE){
    //Flash green
  } else if (inputResult == GLOBLOCK_ACTION_FAILURE){
    handleError("SERIAL DATA UNSCUCCESSFUL - MALFORMED");
  }
}

void handleError(String errorMessage){
     digitalWrite(GLOBLOCK_INDICATOR_LED, HIGH);
     delay(250);
     digitalWrite(GLOBLOCK_INDICATOR_LED, LOW);
     delay(250);
}

int readSerialInput(String *inputData){
  int serialStatus = GLOBLOCK_ACTION_SUCCESS;
  
  if(Serial.available()){
    char byteIn;
    int packet = 0;
    do{
      byteIn = (char)Serial.read();
      *inputData += byteIn; 
      if (byteIn == GLOBLOCK_SERIAL_DATA_FTR || byteIn == GLOBLOCK_SERIAL_DATA_HDR) packet++;
    } while(packet <2 && Serial.available());
  } else {
    serialStatus = GLOBLOCK_SERIAL_DATA_UNAVAILABLE;
  }
  return serialStatus;
}

void returnHandshake(){
  Serial.print(GLOBLOCK_SERIAL_DATA_STX);
  Serial.print("#HR#RESPONSE");
  Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
  Serial.print("#B1#EMPTYDATA");
  Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
  Serial.print("#FR#COMPLETE");
  Serial.print(GLOBLOCK_SERIAL_DATA_ETX);
}

void testRFID(){
  Serial.print(GLOBLOCK_SERIAL_DATA_STX);
  Serial.print("#HR#TEST RFID RESPONSE");
  Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
  Serial.print("#B1#FFAA10205691");
  Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
  Serial.print("#FR#COMPLETE");
  Serial.print(GLOBLOCK_SERIAL_DATA_ETX);
}

void displayReport(){
}
void displayData(){
}
boolean testRFID(){
}

//  String inputString = "";         // a string to hold incoming data
//  boolean stringComplete = false;  // whether the string is complete
//  
//  void serialEvent() {
//    while (Serial.available()) {
//      // get the new byte:
//      char inChar = (char)Serial.read(); 
//      // add it to the inputString:
//      inputString += inChar;
//      // if the incoming character is a newline, set a flag
//      // so the main loop can do something about it:
//      if (inChar == '\n') {
//        stringComplete = true;
//      } 
//    }
//  }
//  
//  void loop() {
//    // print the string when a newline arrives:
//    if (stringComplete) {
//      Serial.println(inputString); 
//      // clear the string:
//      inputString = "";
//      stringComplete = false;
//    }
//    //digitalWrite(ledPin, HIGH);
//    //delay(1000);
//    //digitalWrite(ledPin, LOW);
//    //delay(1000);
//  }
