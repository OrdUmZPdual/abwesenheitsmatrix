<?php
header('Content-Type: application/json');

$bereich = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['bereich'] ?? 'default');
$monat = preg_replace('/[^0-9\-]/', '', $_GET['monat'] ?? date('Y-m'));

$current = date('Y-m');
if ($monat < $current) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Speichern vergangener Monate nicht erlaubt"]);
    exit;
}

$data = file_get_contents("php://input");
if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Keine Daten empfangen"]);
    exit;
}

$dir = __DIR__ . "/data";
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Alte Dateien löschen (älter als 12 Monate)
$jetzt = time();
$einJahr = 365 * 24 * 60 * 60;
    foreach (glob("$dir/abwesenheiten_*.json") as $datei) {
    if (preg_match("/_(\d{4})-(\d{2})\.json$/", $datei, $matches)) {
        $dateDatum = strtotime("{$matches[1]}-{$matches[2]}-01");
        if ($dateDatum < strtotime("-12 months")) {
            unlink($datei);
        }
    }
}

$filename = "$dir/abwesenheiten_{$bereich}_{$monat}.json";
file_put_contents($filename, $data);
echo json_encode(["status" => "success"]);
