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
*/

/* 
INCLUDES, METHODS, GLOBAL & VARIABLE DECLARATIONS 
---------------------------------------------------------------- */
/*   INCLUDES   */
#include <WString.h>         // Official Arduino string library
#include <SoftwareSerial.h>  // Dependency of ID20Reader. Must include in main file Due to Arduino software limitations.
#include <ID20Reader.h>      // ID 20 Library from https://github.com/Thumperrr/Arduino_ID20Reader

/*   CONSTANTS   */
#define GLOBLOCK_INDICATOR_LED 13
#define GLOBLOCK_SERIAL_SUCCESS 1
#define GLOBLOCK_SERIAL_FAILURE 999
#define GLOBLOCK_SERIAL_DATA_STX "\x02"
#define GLOBLOCK_SERIAL_DATA_ETX "\x03"
#define GLOBLOCK_SERIAL_DATA_DLR "|"
#define GLOBLOCK_SERIAL_DATA_HDR '#'
#define GLOBLOCK_SERIAL_DATA_FTR '#'
#define GLOBLOCK_SERIAL_DATA_NLN '\n'
#define GLOBLOCK_SERIAL_BAUDRATE 9600

/*   VARIABLES  */
String inputData;
char inputByte, dataHeader, dataType;
int inputResult;
int inputLength;
String dataMessage;

/*   METHOD DECLARATIONS   */
int readSerialInputData();
void handleData(String inputData);
void handleError(String errorMessage);
void returnHandshake(String dataMessage);
void displayReport(String dataMessage);
void displayData(String lcdMessage);
void handleError(String errorMessage);
boolean testRFID(String dataMessage);
void callBack(boolean success, String data);
int successAlert(int count, int freq);
/*
---------------------------------------------------------------- */

/* RFID Setup */

int rx_pin = 9; //Data input pin
int tx_pin = 8; //Unused, but must be defined. (Nothing is sent from the Arduino to the reader.)

ID20Reader rfid(rx_pin, tx_pin); //Create an instance of ID20Reader.

/* 
METHODS
---------------------------------------------------------------- */

/*  
SETUP  
* Initialize serial to Baudrate and set pinmode for indicator led
*/  void setup(){
      pinMode(GLOBLOCK_INDICATOR_LED, OUTPUT);
      Serial.begin(GLOBLOCK_SERIAL_BAUDRATE);
    }

/*  
LOOP  
* Tests to see if data is available and calls handleData if serial input is available
*/  void loop(){
      /* R2D - RFID Tag Read on ID12A/ID20 */
      rfid.read(); 
      if(rfid.available()) {
        String code = rfid.get();   //Get the tag
        Serial.println(code);       //Print the tag to the serial monitor for testing
      }
  
      inputData = "";
      inputResult = readSerialInputData();
      if (inputResult == GLOBLOCK_SERIAL_SUCCESS) {
        handleData(inputData);
      } else if (inputResult == GLOBLOCK_SERIAL_FAILURE) {
        //Flash green - and alert still running
      } else { 
        handleError("SERIAL DATA UNSCUCCESSFUL - MALFORMED");
      }
    }

/*  
READ SERIAL INPUT DATA  
* Attempts to read serial input and if successful, assign to inputData by reference, and returns success code
*/  int readSerialInputData(){
      int inputComplete = 0;
      if(Serial.available()){
        if (!(Serial.available()>0)) return GLOBLOCK_SERIAL_FAILURE;
        do{
          inputData += (char)Serial.read();
          if (inputByte == GLOBLOCK_SERIAL_DATA_FTR || inputByte == GLOBLOCK_SERIAL_DATA_HDR) inputComplete++;
          allowToBuffer(10);
        } while((inputComplete<3) && (Serial.available()));
        return GLOBLOCK_SERIAL_SUCCESS;
      } else {
        return GLOBLOCK_SERIAL_FAILURE;
      }
    }

/*  
HANDLE DATA
* Breaks up inputData to define header and dataMessage, and carries out appropriate method call defined by headrer type
* If no valid header is found, calls handleError()
*/  void handleData(String inputData){
      /* Assignments */
      inputLength = inputData.length();
      dataMessage = "Undefined";
      dataHeader = inputData.charAt(0);
      dataType = inputData.charAt(1);
      if (inputLength > 3)dataMessage = inputData.substring(3, inputLength-1);
      /* Switch for Header types */
      switch (dataType) {
        case 'H'://Handshake 
          returnHandshake(dataMessage);   
          break;
        case 'R'://Report to LCD
          displayReport(dataMessage);    
          break;
        case 'D'://Display to LCD    
          displayData(dataMessage);
          break;
        case 'T'://Display to LCD    
          testRFID(dataMessage);
          break;
        case 'G':
          globeAvailable(inputData);
        case '\n':
          break;
        default:
          handleError("INVALID HEADER");
          break;
        }
    }

/*  
HANDLE ERROR
* Accepts message input as a string and invokes a callBack with a flase success to client. Allows scope for LCD display
*/  void handleError(String errorMessage){
         callBack(false, errorMessage);
         // LCD Display Warning
    }

/*  
RETURN HANDSHAKE
* This method is called if the 'H' header is sent from client and returns a basic response, then calls successAlert
* If no valid header is found, calls handleError()
*/  void returnHandshake(String dataMessage){
      Serial.print(GLOBLOCK_SERIAL_DATA_STX);
      Serial.print("#H#HANDSHAKE RESPONSE");
      Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
      Serial.print("#B#" + dataMessage);
      Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
      Serial.print("#F#COMPLETE");
      Serial.print(GLOBLOCK_SERIAL_DATA_ETX);
      successAlert(5, 100);
    }

/*  
DISPLAY REPORT
* This method is called if the 'R' header is sent from client and returns a basic success response through callBack.  
* Allows scope for LCD display, and further reporting functionality, such as last ID read from device etc..
*/  void displayReport(String dataMessage){
      //LCDDisplay(dataMessage);
      callBack(true, "Display Successful");
    }

/*  
DISPLAY DATA
* This method is called if the 'D' header is sent from client and returns a basic success response through callBack.  
* Allows scope for LCD display, and further reporting functionality, such as last ID read from device etc..
*/  void displayData(String lcdMessage){
      //LCDDisplay(dataMessage);
    }

/*  
TEST RFID
* This method is called if the 'T' header is sent from client and returns a mock RFID value for testing purposes. 
*/  boolean testRFID(String dataMessage){
      Serial.print(GLOBLOCK_SERIAL_DATA_STX);
      Serial.print("#H#TEST RFID RESPONSE");
      Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
      Serial.print("#B#ID:FFAA10205691");
      Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
      Serial.print("#F#COMPLETE");
      Serial.print(GLOBLOCK_SERIAL_DATA_ETX);
      successAlert(5, 100);
    }

/*  
CALL BACK
* Returns a response to the client of 'Success' or 'Failure' and generates an appropriate successAlert.
*/  void callBack(boolean success, String data){
      if (success){
        Serial.print(GLOBLOCK_SERIAL_DATA_STX);
        Serial.print("#H#REQUEST RESPONSE");
        Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
        Serial.print("#B#SUCCEEDED:" + data);
        Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
        Serial.print("#F#COMPLETE");
        Serial.print(GLOBLOCK_SERIAL_DATA_ETX);
        successAlert(5, 100);
      } else {
        Serial.print(GLOBLOCK_SERIAL_DATA_STX);
        Serial.print("#H#REQUEST RESPONSE");
        Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
        Serial.print("#B#FAILED!" + data);
        Serial.print(GLOBLOCK_SERIAL_DATA_DLR);
        Serial.print("#F#COMPLETE");
        Serial.print(GLOBLOCK_SERIAL_DATA_ETX);
        successAlert(3, 250);
      }
    }

/*  
SUCCESS ALERT
* Recursive method that flashes Indicator LED recursively, at a particular interval
*/  int successAlert(int count, int freq){
      if ((count == 1)||(count == 0)) return 0;
      digitalWrite(GLOBLOCK_INDICATOR_LED, HIGH);
      delay(freq);
      digitalWrite(GLOBLOCK_INDICATOR_LED, LOW);
      delay(freq);
      return successAlert((count-1), freq);
    }
    
/*  
ALLOW TO BUFFER
* Delay method that allows the serial to buffer its input and prevent re-read
*/  void allowToBuffer(int bufferLimit){
      delay(bufferLimit);
    }

/*  
GLOBE AVAILABLE
* Globe is placed on the device
*/  void globeAvailable(String inputData){
  
    }

void setup() {
  Serial.begin(9600);
  Serial.println("RFID Reader - Swipe a card ~~~~~");
}

/* 
COMPLETE
---------------------------------------------------------------- */
