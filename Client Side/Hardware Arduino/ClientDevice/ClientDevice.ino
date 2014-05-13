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
#define GLOBLOCK_RFID_RX_PIN 9
#define GLOBLOCK_RFID_TX_PIN 8

/*   VARIABLES  */
String bufferedInput, clientInput, clientMessage, response, tagID;
char inputByte, clientHeader, requestType;
int inputResult, inputLength;

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

/* RFID Object */
ID20Reader rfid(GLOBLOCK_RFID_RX_PIN, GLOBLOCK_RFID_TX_PIN); //Create an instance of ID20Reader. //GLOBLOCK_RFID_RX_PIN not used (no transmission , but must be declared

/* Communication paths */
//R2D :        Reader to Arduino Device
//R2D2 C3PO :  Reader to Arduino Device to Client (3 Part Object Header, Body, Footer)

/*
---------------------------------------------------------------- */

/* 
METHODS
---------------------------------------------------------------- */

/*  
SETUP  
* Initialize serial to Baudrate and set pinmode for indicator led
*/  void setup(){
      pinMode(GLOBLOCK_INDICATOR_LED, OUTPUT);
      Serial.begin(GLOBLOCK_SERIAL_BAUDRATE);
      // TO DO - LCDwrite Write Starting Device
      // TO DO - LCDwrite Waiting for Client Application
    }

/*  
LOOP  
* Clear values in memory, and test comms R2D2
*/  void loop(){
      clearValues();
      verifyDevice();
      verifyClient();
    }
    
/*  
CLEAR VALUES
* Clear values in memory by assigning empty or 0, to prevent incorrect responses
*/  void clearValues(){
      inputResult = 0;              
      bufferedInput = clientInput = clientMessage = response = tagID = response = "";
    }

/*  
VERIFY DEVICE  
* Attempts to read a tag from the RFID Reader to Device (R2D2) and if successful, serializes the tagID in the client 3 part object format
*/  void verifyDevice(){
      /* R2D - RFID Tag Read on ID12A/ID20 */
      rfid.read(); 
      if (rfid.available()) {
        tagID = rfid.get();   // TO DO - Test Tag value length and structure
        serializeC3PO(tagID, 0);
      }
    }

/*  
VERIFY CLIENT 
* Attempts to read serial input from the Client Application and if successful, handles the client input in 'handleClientInput()'
*/  void verifyClient(){
      inputResult = readSerialInputData();
      if (inputResult == GLOBLOCK_SERIAL_SUCCESS) {
        handleClientInput();
      } else if (inputResult == GLOBLOCK_SERIAL_FAILURE) {
        //Flash green - and alert still running
      } else { 
        handleError("SERIAL DATA UNSCUCCESSFUL - MALFORMED HEADER");
      }
    }
/*  
READ SERIAL INPUT DATA  
* Attempts to read serial input from the client and if successful empties the buffer and assigned to 'clientInput'
* Returns SUCCESS/FAILURE code
*/  int readSerialInputData(){
      int inputComplete = 0;
      if(Serial.available()){
        if (!(Serial.available()>0)) return GLOBLOCK_SERIAL_FAILURE;
        do{
          bufferedInput += (char)Serial.read();
          if (inputByte == GLOBLOCK_SERIAL_DATA_FTR || inputByte == GLOBLOCK_SERIAL_DATA_HDR) inputComplete++;
          allowToBuffer(10);
        } while((inputComplete<3) && (Serial.available()));
        clientInput = bufferedInput;
        return GLOBLOCK_SERIAL_SUCCESS;
      } else {
        return GLOBLOCK_SERIAL_FAILURE;
      }
    }

/*  
HANDLE DATA
* Breaks up clientInput to define header, requestType and clientMessage, and carries out appropriate method call defined by header type
* If no valid header is found, calls handleError()
*/  void handleClientInput(){
      /* Assignments */
      inputLength = clientInput.length();
      clientMessage = "Undefined";
      clientHeader = clientInput.charAt(0);
      requestType = clientInput.charAt(1);
      if (inputLength > 3)clientMessage = clientInput.substring(3, inputLength-1);
      /* Switch for Header types */
      switch (requestType) {
        case 'H'://Handshake 
          serializeC3PO("HANDSHAKE", 1);
          break;
        case 'D'://Display to LCD    
          displayOnLCD(clientMessage);
          serializeC3PO("DISPLAY COMPLETE", 1);
          break;
        case 'T'://Test RFID tag ID   
          serializeC3PO("55378008AA", 0);
          break;
        case 'L'://Sit and wait for tag ID, Display "Listening" to LCD    
          displayOnLCD("Listening");
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
      // LCD Display Warning
      // displayOnLCD(errorMessage);
      serializeC3PO(errorMessage, -1);
      successAlert(3, 250);
    }

/*  
DISPLAY DATA
* This method is called if the 'D' header is sent from client and returns a basic success response through callBack.  
* Allows scope for LCD display, and further reporting functionality, such as last ID read from device etc..
*/  void displayOnLCD(String lcdMessage){
      //LCDDisplay(dataMessage);
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
SERIALIZE C3PO
* Format the data, depending on type in the correct format for Client 3 Part Object reponse, and print to the serial object  
*/  void serializeC3PO(String data, int typeCode){
        response += GLOBLOCK_SERIAL_DATA_STX;  
        switch (typeCode) {
          case 0: //Tag read initiated client comms
            response += "#H#TAG";
            response += "#B#TAGID:";
            break;
          case 1:
            response += "#H#REQUEST REPONSE";
            response += "#B#DATA:";
            break;
          case -1:
            response += "#H#REQUEST ERROR";
            response += "#B#ERROR:";
            break;
          default:
            response += "#H#UNKNOWN";
            response += "#B#UNKNOWN:";
            break;
        }
        response += data;
        response += "#F#COMPLETE";
        response += GLOBLOCK_SERIAL_DATA_ETX;
        successAlert(5, 100);
        Serial.println(response);
    }

/* 
COMPLETE
---------------------------------------------------------------- */
