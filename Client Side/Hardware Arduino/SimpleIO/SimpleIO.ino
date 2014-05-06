void setup() {
    Serial.begin(9600);
    Serial.write("Power On");
}

void loop() {
  String content = "";
  char character;
  while(Serial.available()) {
      character = Serial.read();
      content.concat(character);
      delay(10); //Allow to buffer
  }
  if (content != "") {
    Serial.println("Content:" + content);
  }
  
}
