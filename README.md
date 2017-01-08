# Tarifrechner Modul für Kreadea

## Backend Modul für Contao 3

Das Modul enthält 2 identische Datenbanktabellen (tl_huf_1 und tl_huf_2), in denen die Preise für Stückguttransporte definiert sind. Der Preis ist abhängig vom Gewicht, das zu transportieren ist. Die Tabellen lassen sich mit dem csv-imorter "import_from_csv" aktualisieren.

Der Frontend-User kann über 2 identische Formulare (Contao-Formulargenerator) den Preis für einen Stückguttransport berechnen. Dies geschieht per Ajax-Request.

![Frontend](module_info/image1.JPG?raw=true "Frontend")
