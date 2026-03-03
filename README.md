# FF Signature Field - Fluent Forms Unterschrift-Plugin

Ein leichtgewichtiges WordPress-Plugin, das Fluent Forms um ein digitales Unterschrift-Feld erweitert. Benutzer koennen direkt im Formular mit Maus, Finger oder Stift unterschreiben.

---

## Features

- Canvas-basiertes Signaturfeld mit sofortiger Zeichenfunktion
- Funktioniert mit Maus (Desktop), Finger (Touchscreen) und Stylus (Tablet)
- Kein externes JavaScript, alles inline, keine CDN-Abhaengigkeit
- Retina-/HiDPI-Unterstuetzung (automatische Skalierung)
- Loeschen-Button zum Zuruecksetzen der Unterschrift
- Gestrichelte Linie als visuelle Orientierung
- Pflichtfeld-Validierung (optional aktivierbar)
- Unterschrift wird als PNG-Bild gespeichert
- Kompatibel mit bedingter Logik und mehrstufigen Formularen
- Responsiv, passt sich automatisch an die Formularbreite an

---

## Voraussetzungen

- WordPress 5.6 oder hoeher
- PHP 7.4 oder hoeher
- Fluent Forms 5.0+ (getestet mit Version 6.1.19)

---

## Installation

### Variante A - Manuell per FTP / Dateimanager

1. Erstelle den Ordner wp-content/plugins/ff-signature-field/
2. Lade die Datei ff-signature-field.php in diesen Ordner hoch
3. Gehe im WordPress-Admin zu Plugins und aktiviere FF Signature Field

### Variante B - Als ZIP hochladen

1. Packe den Ordner ff-signature-field/ (mit der PHP-Datei darin) als ZIP
2. Gehe zu Plugins, Installieren, Plugin hochladen
3. ZIP-Datei auswaehlen, installieren und aktivieren

---

## Verwendung

1. Oeffne ein Formular im Fluent Forms Editor
2. In der Seitenleiste unter Erweiterte Felder findest du das Feld Unterschrift
3. Ziehe es per Drag and Drop an die gewuenschte Stelle im Formular
4. Optional: Aktiviere unter Allgemein, Validierung die Pflichtfeld-Option

### Einstellbare Optionen im Editor

| Einstellung        | Beschreibung                                       |
|--------------------|----------------------------------------------------|
| Label              | Beschriftung ueber dem Signaturfeld                |
| Admin-Label        | Internes Label fuer die Einreichungsuebersicht     |
| Pflichtfeld        | Ob eine Unterschrift zum Absenden erforderlich ist |
| Hilfetext          | Optionaler Hinweistext unter dem Feld              |
| CSS-Klasse         | Eigene CSS-Klasse fuer das Container-Element       |
| Bedingte Logik     | Feld nur bei bestimmten Bedingungen anzeigen       |

---

## Wie die Unterschrift gespeichert wird

Nach dem Absenden des Formulars wird die Unterschrift automatisch verarbeitet:

1. Die Zeichnung wird als Base64-PNG im Hidden-Input uebermittelt
2. Das Plugin speichert die Unterschrift als PNG-Datei unter:
   wp-content/uploads/ff-signatures/{Einreichungs-ID}/signature-{Zeitstempel}.png
3. In der Fluent Forms Einreichung wird die Base64-Daten durch die Bild-URL ersetzt

---

## Technische Details

- Das Plugin besteht aus einer einzigen PHP-Datei (kein Build-Prozess noetig)
- JavaScript wird inline im HTML ausgegeben, keine externen JS-Dateien
- Verwendet die native Canvas API und Pointer Events (mit Fallback auf Mouse/Touch Events)
- Jedes Signaturfeld erhaelt eine eindeutige ID, sodass mehrere Felder pro Formular moeglich sind
- Die Canvas-Aufloesung wird automatisch an das Display-DPR angepasst

---

## Fehlerbehebung

Das Feld erscheint nicht im Formular:
- Pruefen ob das Plugin aktiviert ist
- Sicherstellen dass das Feld aus Erweiterte Felder (nicht Allgemeine Felder) gezogen wurde
- Browser-Cache und ggf. serverseitigen Cache leeren

Man kann nicht zeichnen:
- Browser-Konsole oeffnen (F12, Console) und auf JavaScript-Fehler pruefen
- Sicherstellen dass kein anderes Plugin oder Theme den Canvas blockiert

Die Unterschrift wird nicht gespeichert:
- Schreibrechte fuer wp-content/uploads/ pruefen
- Kontrollieren ob der Ordner ff-signatures angelegt werden kann

---

## Changelog

### 2.0.0
- Komplettes Rewrite: Alles inline, keine externen JS-Abhaengigkeiten
- Eigene Canvas-Implementierung statt externer Bibliothek
- Verbesserte Kompatibilitaet mit verschiedenen Themes und Caching-Plugins

### 1.0.0
- Erste Version mit externer signature_pad.js Bibliothek

---

## Lizenz

GPLv2 or later
