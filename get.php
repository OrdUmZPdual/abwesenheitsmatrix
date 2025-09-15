<?php
header('Content-Type: application/json');

$monat = preg_replace('/[^0-9\-]/', '', $_GET['monat'] ?? date('Y-m'));
$bereich = $_GET['bereich'] ?? null;

$dir = __DIR__ . "/data";
$ergebnis = [];

if ($bereich && $bereich !== 'alle') {
    // 🔹 Nur ein Bereich laden (wie bisher)
    $bereich = preg_replace('/[^a-zA-Z0-9_\-]/', '', $bereich);
    $filename = "$dir/abwesenheiten_{$bereich}_{$monat}.json";

    if (!file_exists($filename)) {
        echo json_encode([]); // Leere Daten beim ersten Aufruf
        exit;
    }

    echo file_get_contents($filename);
    exit;
}

// 🔹 Bereichsübergreifende Suche
$person = $_GET['person'] ?? null;
$person = $person ? urldecode($person) : null;
$bereichGefunden = null;

foreach (glob("$dir/abwesenheiten_*_{$monat}.json") as $datei) {
    $inhalt = json_decode(file_get_contents($datei), true);
    if (is_array($inhalt)) {
        foreach ($inhalt as $name => $tage) {
            if ($person && strtolower($name) === strtolower($person)) {
                // Bereich aus Dateiname extrahieren
                if (preg_match("/abwesenheiten_(.+)_{$monat}\.json$/", basename($datei), $matches)) {
                    $bereichGefunden = $matches[1];
                }
                $ergebnis[$name] = $tage;
                break 2; // Nur diesen Mitarbeiter laden
            }
        }
    }
}

echo json_encode([
  "daten" => $ergebnis,
  "bereich" => $bereichGefunden
]);
