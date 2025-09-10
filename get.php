<?php
header('Content-Type: application/json');

$bereich = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['bereich'] ?? 'default');
$filename = __DIR__ . "/data/abwesenheiten_" . $bereich . ".json";

if (!file_exists($filename)) {
    echo json_encode([]); // Leere Daten beim ersten Aufruf
    exit;
}

echo file_get_contents($filename);
