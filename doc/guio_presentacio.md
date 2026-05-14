# Guió de la presentació oral – SmartSchool IoT

**Durada:** 5-7 minuts | **Format:** reunió amb client simulada

---

## Diapositiva 1 – Títol

> *"Bon dia. El nostre projecte es diu SmartSchool IoT. Us presentarem un sistema IoT dissenyat per a una escola, centrat en tres àrees: confort de les aules, assistència i seguretat."*

---

## Diapositiva 2 – Problema

> *"Hem detectat que en molts centres hi ha tres problemes: primer, el professorat passa llista manualment. Segon, no hi ha informació en temps real sobre les condicions de les aules. I tercer, alguns espais no tenen control d'accés adequat."*

- Control manual d'assistència → pèrdua de temps
- Aules sense dades ambientals → confort reduït
- Energia malgastada → llums enceses en aules buides

---

## Diapositiva 3 – Solució

> *"La nostra solució és un sistema IoT amb tres mòduls: sensors ambientals, RFID per a l'assistència, i un panell web centralitzat."*

---

## Diapositiva 4 – Arquitectura tècnica

> *"Cada aula té un ESP32 connectat als sensors. L'ESP32 envia les dades per Wi-Fi a un servidor PHP amb base de dades MySQL. El professorat ho veu tot al dashboard web."*

Diagrama: Sensors → ESP32 → Wi-Fi → API PHP → MySQL → Dashboard

---

## Diapositiva 5 – Resultats i validació

> *"Hem provat la lectura de sensors, RFID, enviament de dades i el dashboard. Tot ha funcionat correctament."*

| Prova | Resultat |
|---|---|
| Sensors | ✅ |
| RFID + assistència | ✅ |
| Enviament HTTP | ✅ |
| Dashboard web | ✅ |

---

## Diapositiva 6 – Beneficis

- ⏱ Menys temps administratiu
- 🌡 Confort millorat
- 🔒 Accés controlat
- ⚡ Menys consum energètic

---

## Diapositiva 7 – Conclusions

> *"SmartSchool IoT és un projecte viable, basat en components de baix cost. És escalable a qualsevol centre i es pot ampliar amb app mòbil o IA. Moltes gràcies."*

---

## Preguntes freqüents del client

**P: Quant costa?**
R: ~30-50€ per aula en maquinari (ESP32 ~5€, sensors 2-8€ cadascun).

**P: Privacitat dels alumnes?**
R: Dades desades localment, accés per rol, compatible amb RGPD.

**P: Es pot ampliar?**
R: Sí. Arquitectura modular, fàcil d'escalar.
