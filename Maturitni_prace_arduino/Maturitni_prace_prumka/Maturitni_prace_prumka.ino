#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <ArduinoJson.h>

ESP8266WebServer server;
char* ssid = "SPS-CL-FREE";
char* password = "spsclfree";

void setup() {
  // nastav piny jako vystupni
  pinMode(16, OUTPUT);
  pinMode(5, OUTPUT);
  pinMode(4, OUTPUT);

  // nastav hodnoty na vypnuto
  digitalWrite(16, HIGH);
  digitalWrite(5, HIGH);
  digitalWrite(4, HIGH);

  // zahaj seriovou komunikaci
  Serial.begin(115200);
  delay(5000);
  Serial.println();
  // pripoj se k Wi-Fi
  Serial.print("ESP Board MAC Address: ");
  Serial.println(WiFi.macAddress());
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid,password);

  // loading animace
  while(WiFi.status()!=WL_CONNECTED)
  {
    Serial.print(".");
    delay(500);
  }
  Serial.println("");
  Serial.println("WiFi connected.");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
  Serial.println("-------------");

  // zapni server na localhost/led
  server.on("/",relayControl);
  server.onNotFound(handleNotFound); 
  server.begin();
}

  // smycka pro obstaravani serveru
void loop() {
  server.handleClient();
}

  // 404 error
void handleNotFound(){
  server.send(404, "text/plane","");
}

  // funkce pro ovladani diod pomoci json requestu
void relayControl() {
  // ulozi do promenne IP adresu klienta
  String cIP = server.client().remoteIP().toString();

  // pokud je IP klienta povolena...
  if(cIP == "10.1.100.50"){
    Serial.print("Client IP: ");
    Serial.println(cIP);

    // json request do promenne input
    String input = server.arg("plain");
    
    StaticJsonDocument<64> doc;

    // deserializace requestu
    DeserializationError error = deserializeJson(doc, input);

    // kdyz chyba...
    if (error) {
      Serial.print(F("deserializeJson() failed: "));
      Serial.println(error.f_str());
      Serial.println("-------------");
      handleNotFound();
      return;
    }
    
    // promenne ziskane z jsonu
    String elementString = doc["element"]; // "akce"
  
    int pin;
    
    if(elementString == "akce") {
      pin = 5;
    }
    
    else {
      pin = 0;
     }
  
    Serial.println("Element:");
    Serial.println(elementString);
    Serial.println("-------------");
    
    server.send(204,"");
  
    pinDriver(pin);  
  }
  else{
  Serial.print("Bad IP: ");
  Serial.println(cIP);
  Serial.println("-------------");
  }
}

void pinDriver(int pin) {
  digitalWrite(pin, 0);
  delay(500);
  digitalWrite(pin, 1);
}
