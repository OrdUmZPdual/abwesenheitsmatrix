<?php
header('Content-Type: application/json');

$monat = preg_replace('/[^0-9\-]/', '', $_GET['monat'] ?? date('Y-m'));
$bereich = $_GET['bereich'] ?? null;

$dir = __DIR__ . "/data";
$ergebnis = [];

if ($bereich && $bereich !== 'alle') {
    // 🔹 Nur ein Bereich laden
    $bereich = preg_replace('/[^a-zA-Z0-9_\-]/', '', $bereich);
    $filename = "$dir/abwesenheiten_{$bereich}_{$monat}.json";

    if (!file_exists($filename)) {
    // Vormonat berechnen
    $date = DateTime::createFromFormat('Y-m', $monat);
    $date->modify('-1 month');
    $prevMonat = $date->format('Y-m');
    $prevFile = "$dir/abwesenheiten_{$bereich}_{$prevMonat}.json";

		if (file_exists($prevFile)) {
			// Mitarbeiter aus Vormonat übernehmen
			$prevData = json_decode(file_get_contents($prevFile), true);
			$newData = [];
				foreach ($prevData as $name => $tage) {
				$newData[$name] = []; // nur Namen übernehmen, Abwesenheiten leeren
				}
			// Neue Datei für aktuellen Monat anlegen
			file_put_contents($filename, json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
			echo json_encode($newData);
			exit;
		} else {
			// Weder aktueller noch Vormonat vorhanden → leere Liste
			echo json_encode([]);
			exit;
		}
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
