<?php
header('Content-Type: application/json');

$bereich = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['bereich'] ?? 'default');
$filename = __DIR__ . "/data/abwesenheiten_" . $bereich . ".json";

$data = file_get_contents("php://input");
if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Keine Daten empfangen"]);
    exit;
}

// Stelle sicher, dass das data-Verzeichnis existiert
$dir = __DIR__ . "/data";
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Speichere die Daten
file_put_contents($filename, $data);
echo json_encode(["status" => "success"]);
