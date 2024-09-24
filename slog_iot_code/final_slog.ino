#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <MFRC522.h>
#include <SPI.h>
#include <Wire.h>
#include <hd44780.h>          // main hd44780 header
#include <hd44780ioClass/hd44780_I2Cexp.h>

#define RST_PIN   D3     // Reset pin for MFRC522
#define SS_PIN    D4     // Slave Select pin for MFRC522
#define BUZZER_PIN D8    // Buzzer pin

MFRC522 mfrc522(SS_PIN, RST_PIN); // Create MFRC522 instance
hd44780_I2Cexp lcd;

// const char* ssid = "MVGR_SLC";
// const char* password = "001010011100";
const char* ssid = "MVGR_SLC";
const char* password = "001010011100";

void setup() {
  Serial.begin(9600);
  SPI.begin();
  lcd.begin(16, 2);  // Initialize 16x2 LCD
  lcd.backlight();
  mfrc522.PCD_Init();
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
    lcd.print("Connecting....");
  }
  
  Serial.println("Connected to WiFi");
  Serial.println("All setup done");
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Connected To");
  lcd.setCursor(0, 1);
  lcd.print(ssid);
  delay(3000);
  lcd.clear();
  getcount();
}

void loop() {
  if(WiFi.status() == WL_CONNECTED) {
    if (!mfrc522.PICC_IsNewCardPresent()) {
    return;
  }
  // Select one of the cards.
  if (!mfrc522.PICC_ReadCardSerial()) {
    return;
  }
  // Show UID on serial monitor.
  Serial.print("UID tag :");
  String uid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    uid.concat(String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : ""));
    uid.concat(String(mfrc522.uid.uidByte[i], HEX));
  }
      uid.toUpperCase();
      Serial.println("Card UID: " + uid);
      sendSensorDataToServer(uid);
      delay(500); // Delay a bit to avoid reading multiple times
  }
  else {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Disconnected");
    lcd.setCursor(0, 1);
    lcd.print("Searching..");
    delay(500);
    WiFi.begin(ssid, password);
    delay(3000);
    getcount2();
    // Delay to display the message
  } 

}
void getcount2() {
  static HTTPClient http;
  static WiFiClient client;

  const char* serverAddress = "http://192.168.16.111/slog/getcount.php";
  http.begin(client, serverAddress);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded"); 
  String data = "sensor1=send";
  int httpResponseCode = http.POST(data);
  // const char* serverPath = "http://192.168.16.111/slog/getcount.php?sensor1=send";
  // http.begin(client, serverPath); // Use GET request
  // int httpResponseCode = http.GET();
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println(httpResponseCode);
    Serial.println(response);
    // Clear the LCD display
    lcd.clear();
    // Find the position of the "<hr>" delimiter in the response string
    int delimiterIndex = response.indexOf("<hr>");

    // If the delimiter is found
    if (delimiterIndex != -1) {
      // Extract the first part of the response string before the delimiter
      String firstPart = response.substring(0, delimiterIndex);
      // Extract the second part of the response string after the delimiter
      String secondPart = response.substring(delimiterIndex + 4); // +4 to skip "<hr>"

      // Print the first part on the first line of the LCD
      lcd.setCursor(0, 0);
      lcd.print(firstPart);
      // Print the second part on the second line of the LCD
      lcd.setCursor(0, 1);
      lcd.print(secondPart);
    }
  } 
  http.end();
}
void getcount() {
  static HTTPClient http;
  static WiFiClient client;

  const char* serverAddress = "http://192.168.16.111/slog/getcount.php";
  http.begin(client, serverAddress);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded"); 
  String data = "sensor1=send";
  int httpResponseCode = http.POST(data);
  // const char* serverPath = "http://192.168.16.111/slog/getcount.php?sensor1=send";
  // http.begin(client, serverPath); // Use GET request
  // int httpResponseCode = http.GET();
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println(httpResponseCode);
    Serial.println(response);
    // Clear the LCD display
    lcd.clear();
    // Find the position of the "<hr>" delimiter in the response string
    int delimiterIndex = response.indexOf("<hr>");

    // If the delimiter is found
    if (delimiterIndex != -1) {
      // Extract the first part of the response string before the delimiter
      String firstPart = response.substring(0, delimiterIndex);
      // Extract the second part of the response string after the delimiter
      String secondPart = response.substring(delimiterIndex + 4); // +4 to skip "<hr>"

      // Print the first part on the first line of the LCD
      lcd.setCursor(0, 0);
      lcd.print(firstPart);
      // Print the second part on the second line of the LCD
      lcd.setCursor(0, 1);
      lcd.print(secondPart);
    }
  } else {
    Serial.print("Error on HTTP request, code: ");
    Serial.println(httpResponseCode);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Connection lost");
    lcd.setCursor(0, 1);
    lcd.print("with Database");
    tone(BUZZER_PIN, 1000); // Play a 1000 Hz tone
    delay(300); // Wait for 0.3 seconds
    noTone(BUZZER_PIN); // Stop the tone
    delay(500);
    tone(BUZZER_PIN, 1000); // Play a 1000 Hz tone
    delay(300); // Wait for 0.3 seconds
    noTone(BUZZER_PIN); // Stop the tone
    delay(1000); // Adjust as needed
    getcount2();
  }
  http.end();
}

void sendSensorDataToServer(String uid) {
  static HTTPClient http;
  static WiFiClient client;

  const char* serverAddress = "http://192.168.16.111/slog/sendid.php";
  http.begin(client, serverAddress);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  String data = "sensor1=" + uid;

  int httpResponseCode = http.POST(data);
  // const char* serverPath = "http://192.168.16.111/slog/sendid.php?sensor1="+uid;
  // http.begin(client, serverPath); // Use GET request
  // int httpResponseCode = http.GET();
 

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println(httpResponseCode);
    Serial.println(response);
    // Clear the LCD display
    lcd.clear();
    // Find the position of the "<hr>" delimiter in the response string
    int delimiterIndex = response.indexOf("<hr>");

    // If the delimiter is found
    if (delimiterIndex != -1) {
      // Extract the first part of the response string before the delimiter
      String firstPart = response.substring(0, delimiterIndex);
      // Extract the second part of the response string after the delimiter
      String secondPart = response.substring(delimiterIndex + 4); // +4 to skip "<hr>"

      // Print the first part on the first line of the LCD
      lcd.setCursor(0, 0);
      lcd.print(firstPart);
      // Print the second part on the second line of the LCD
      lcd.setCursor(0, 1);
      lcd.print(secondPart);
      if ((secondPart == "I-GET Registered")||(secondPart == "O-GET Registered")) {
        tone(BUZZER_PIN, 1000); // Play a 1000 Hz tone
        delay(300); // Wait for 0.3 seconds
        noTone(BUZZER_PIN); // Stop the tone
        delay(500);
        tone(BUZZER_PIN, 1000); // Play a 1000 Hz tone
        delay(300); // Wait for 0.3 seconds
        noTone(BUZZER_PIN); // Stop the tone
      } else {
        tone(BUZZER_PIN, 1000); // Play a 1000 Hz tone
        delay(1000); // Wait for 1 second
        noTone(BUZZER_PIN); // Stop the tone
      }
      delay(2000); // Adjust as needed
      getcount();
    }
  } else {
    Serial.print("Error on HTTP request, code: ");
    Serial.println(httpResponseCode);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Connection lost");
    lcd.setCursor(0, 1);
    lcd.print("with Database");
    tone(BUZZER_PIN, 1000); // Play a 1000 Hz tone
    delay(300); // Wait for 0.3 seconds
    noTone(BUZZER_PIN); // Stop the tone
    delay(500);
    tone(BUZZER_PIN, 1000); // Play a 1000 Hz tone
    delay(300); // Wait for 0.3 seconds
    noTone(BUZZER_PIN); // Stop the tone
    delay(1000); // Adjust as needed
    getcount();
  }
  http.end();
}
