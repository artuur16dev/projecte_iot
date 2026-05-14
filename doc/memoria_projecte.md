# Memòria del projecte – SmartSchool IoT

## 1. Objectiu i context

Aquest projecte proposa un sistema IoT per digitalitzar una escola en tres àrees: **monitoratge ambiental**, **registre d'assistència** i **control d'accés**. L'objectiu és millorar el confort de les aules, reduir temps administratiu i incrementar la seguretat del centre.

## 2. Problema detectat

- Control manual d'assistència (lent i propens a errors)
- Falta de dades ambientals a les aules (temperatura, CO₂, llum)
- Despesa energètica innecessària (llums enceses en aules buides)
- Accés poc controlat a zones restringides

## 3. Solució proposada

Sistema format per tres mòduls:

1. **Monitoratge ambiental**: sensors DHT22, LDR, PIR, MQ135 a cada aula.
2. **Assistència RFID**: lector RC522 per registrar alumnes automàticament.
3. **Dashboard web**: visualització en temps real de totes les dades.

## 4. Arquitectura tècnica

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

## 5. Funcionament

1. L'ESP32 llegeix sensors cada 5 s.
2. Si presència i poca llum → encén relé.
3. Si CO₂ > 1000 ppm o T > 28°C → alerta.
4. Targeta RFID → envia UID al servidor → desa assistència.
5. El dashboard mostra l'estat en temps real.

## 6. Proves realitzades

| Prova | Resultat |
|---|---|
| Lectura DHT22 | ✅ Correcta |
| Lectura PIR | ✅ Correcta |
| Activació relé per presència | ✅ Funciona |
| Lectura RFID + enviament | ✅ Correcta |
| Dades a la BD | ✅ Desades |
| Dashboard web | ✅ Visible |
| Alerta CO₂ | ✅ Detectada |

## 7. Limitacions

- El MQ135 dóna valors aproximats (necessita calibratge).
- Dependència de la connexió Wi-Fi.
- Dades personals (assistència) requereixen gestió conforme al RGPD.

## 8. Millores futures

- App mòbil per a professorat.
- Predicció ambiental amb IA.
- Integració amb plataformes educatives.
- Notificacions automàtiques a famílies.

## 9. Conclusions

El projecte SmartSchool IoT demostra que és possible implementar un sistema de monitoratge escolar real amb components de baix cost (ESP32, DHT22, RFID). La combinació de sensors, backend PHP i dashboard web ofereix un sistema funcional, escalable i aplicable a un entorn educatiu real.
