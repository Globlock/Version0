#include <SoftwareSerial.h> // Dependency of ID20Reader. Must include in main file
                            // Due to Arduino software limitations.
#include <ID20Reader.h>

int rx_pin = 9; //Data input pin
int tx_pin = 8; //Unused, but must be defined. (Nothing is sent from the Arduino to the reader.)
int rst_pin = 2;

ID20Reader rfid(rx_pin, tx_pin); //Create an instance of ID20Reader.

void setup() {
  Serial.begin(9600);
  pinMode(rst_pin, OUTPUT);
  resetReader();
  Serial.println("RFID Reader - Swipe a card ~~~~~");
}

void loop() {
  rfid.read(); //Receive a tag from the reader if available

  if(rfid.available()) //a tag has been read
  {
    String code = rfid.get(); //Get the tag     
  }
}

void resetReader(){
  digitalWrite(rst_pin, LOW);
  delay(150);
  Serial.println("RFID Reader - Reset");
  digitalWrite(rst_pin, HIGH);
  delay(150);
}
