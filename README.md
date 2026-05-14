# SmartSchool IoT 🏫

Sistema IoT per a una escola intel·ligent: monitoratge ambiental a les aules, registre d'assistència amb RFID i control d'accés.

---

## 🎯 Objectius

- Millorar el confort de les aules (temperatura, humitat, llum, CO2)
- Automatitzar el registre d'assistència de l'alumnat
- Controlar l'accés a zones restringides
- Reduir el consum energètic apagant llums i ventilació en aules buides
- Centralitzar totes les dades en un panell web

---

## 🧩 Components de maquinari

| Component | Funció |
|---|---|
| ESP32 | Microcontrolador principal per aula |
| DHT22 | Sensor de temperatura i humitat |
| BH1750 / LDR | Sensor de lluminositat |
| PIR HC-SR501 | Sensor de presència |
| MQ135 | Sensor de qualitat de l'aire / CO2 |
| RC522 RFID | Lector de targetes per a assistència |
| Relé 5V | Control de llum o ventilació |

---

## 🗂️ Estructura del repositori

```
projecte_iot/
├── README.md
├── src/
│   ├── firmware/
│   │   └── esp32_aula.ino       ← Codi per a l'ESP32
│   ├── web/
│   │   ├── index.html           ← Dashboard web
│   │   ├── style.css
│   │   ├── app.js
│   │   └── api/
│   │       ├── save_data.php    ← API per rebre dades
│   │       └── get_data.php     ← API per al dashboard
│   └── database/
│       └── smartschool.sql      ← Esquema de la base de dades
└── doc/
    ├── memoria_projecte.md
    └── guio_presentacio.md
```

---

## ⚙️ Arquitectura del sistema

```
[Sensors + RFID]
      │
    ESP32  (Wi-Fi)
      │
   HTTP POST
      │
  API PHP + MySQL
      │
  Dashboard HTML/JS
```

---

## 🚀 Funcionament

1. L'ESP32 llegeix tots els sensors cada 5 segons.
2. Si detecta presència i poca llum, activa el relé (llum).
3. Si CO2 > 1000 ppm o temperatura > 28°C, genera una alerta.
4. Quan un alumne passa la targeta RFID, es registra l'assistència.
5. Totes les dades s'envien per HTTP POST a l'API PHP.
6. El dashboard web mostra l'estat en temps real.

---

## 🛠️ Tecnologies

- **Firmware:** Arduino IDE / PlatformIO (C++)
- **Backend:** PHP 8 + MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **Comunicació:** HTTP / Wi-Fi
- **Control de versions:** Git + GitHub

---

## 👥 Autors

- artuur16dev

---

## 📄 Llicència

Projecte acadèmic. Ús educatiu.
