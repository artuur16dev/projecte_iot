/*
 * SmartSchool IoT – Firmware ESP32
 * Mòdul d'aula: sensors ambientals + RFID + relé
 *
 * Sensors: DHT22, LDR, PIR, MQ135
 * RFID: RC522
 * Actuador: Relé (llum)
 * Comunicació: HTTP POST cap a l'API PHP
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <SPI.h>
#include <MFRC522.h>

// ─── Configuració Wi-Fi ────────────────────────────────────────
#define WIFI_SSID     "NOM_XARXA"
#define WIFI_PASSWORD "CONTRASENYA"

// ─── URL de l'API ──────────────────────────────────────────────
#define SERVER_URL "http://192.168.1.100/smartschool/api/save_data.php"

// ─── ID d'aula ─────────────────────────────────────────────────
#define AULA_ID "A01"

// ─── Pins ──────────────────────────────────────────────────────
#define PIN_DHT22     4
#define PIN_LDR       34   // Analògic
#define PIN_PIR       13
#define PIN_MQ135     35   // Analògic
#define PIN_RELE      26

// RFID RC522 (SPI)
#define PIN_SS_RFID   5
#define PIN_RST_RFID  0

// ─── Objectes ──────────────────────────────────────────────────
DHT dht(PIN_DHT22, DHT22);
MFRC522 rfid(PIN_SS_RFID, PIN_RST_RFID);

// ─── Llindars ambientals ───────────────────────────────────────
#define LLINDAR_TEMP     28.0   // °C
#define LLINDAR_CO2      1000   // ppm (aproximació MQ135)
#define LLINDAR_LUM      300    // valor analògic (0-4095)

// ─── Variables globals ─────────────────────────────────────────
unsigned long ultimaLectura = 0;
const unsigned long INTERVAL_MS = 5000;

void setup() {
  Serial.begin(115200);

  pinMode(PIN_PIR, INPUT);
  pinMode(PIN_RELE, OUTPUT);
  digitalWrite(PIN_RELE, LOW);

  dht.begin();

  SPI.begin();
  rfid.PCD_Init();
  Serial.println("RFID inicialitzat");

  Serial.print("Connectant a Wi-Fi");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.print("Connectat. IP: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  unsigned long ara = millis();

  // Lectura periòdica de sensors
  if (ara - ultimaLectura >= INTERVAL_MS) {
    ultimaLectura = ara;

    float temperatura = dht.readTemperature();
    float humitat     = dht.readHumidity();
    int   llum        = analogRead(PIN_LDR);
    int   co2         = analogRead(PIN_MQ135);
    bool  presencia   = digitalRead(PIN_PIR);

    if (isnan(temperatura) || isnan(humitat)) {
      Serial.println("Error llegint DHT22");
      return;
    }

    Serial.printf("T=%.1f°C H=%.1f%% Llum=%d CO2=%d Pres=%d\n",
                  temperatura, humitat, llum, co2, presencia);

    // Control automàtic del relé
    if (presencia && llum < LLINDAR_LUM) {
      digitalWrite(PIN_RELE, HIGH);
      Serial.println("Relé: ENCÈS");
    } else if (!presencia) {
      digitalWrite(PIN_RELE, LOW);
      Serial.println("Relé: APAGAT");
    }

    // Alerta ambiental
    String alerta = "cap";
    if (temperatura > LLINDAR_TEMP) alerta = "temperatura_alta";
    if (co2 > LLINDAR_CO2)          alerta = "co2_alt";

    enviarDades(temperatura, humitat, llum, co2, presencia, alerta);
  }

  // Lectura RFID en temps real
  llegirRFID();
}

void enviarDades(float t, float h, int llum, int co2, bool pres, String alerta) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Sense Wi-Fi");
    return;
  }

  HTTPClient http;
  http.begin(SERVER_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String cos = "tipus=ambient";
  cos += "&aula="       + String(AULA_ID);
  cos += "&temperatura=" + String(t, 1);
  cos += "&humitat="    + String(h, 1);
  cos += "&llum="       + String(llum);
  cos += "&co2="        + String(co2);
  cos += "&presencia="  + String(pres ? 1 : 0);
  cos += "&alerta="     + alerta;

  int codi = http.POST(cos);
  Serial.printf("HTTP resposta: %d\n", codi);
  http.end();
}

void llegirRFID() {
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    return;
  }

  String uid = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  Serial.println("Targeta detectada: " + uid);

  enviarAssistencia(uid);

  rfid.PICC_HaltA();
  delay(1000);
}

void enviarAssistencia(String uid) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(SERVER_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String cos = "tipus=assistencia";
  cos += "&aula=" + String(AULA_ID);
  cos += "&uid="  + uid;

  int codi = http.POST(cos);
  Serial.printf("Assistència HTTP: %d\n", codi);
  http.end();
}
