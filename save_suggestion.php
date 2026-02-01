<?php
// api/save_suggestion.php

// 1. Sécurité : En-têtes et Méthode
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// 2. Récupération des données brutes
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// 3. Sanitization (Nettoyage basique)
$cleanData = [
    'date'      => date('Y-m-d H:i:s'),
    'ip'        => $_SERVER['REMOTE_ADDR'], // Pour limiter le spam si besoin plus tard
    'jobId'     => strip_tags($data['jobId'] ?? 'unknown'),
    'role'      => strip_tags($data['role'] ?? ''),
    'task'      => strip_tags($data['task'] ?? ''),
    'context'   => strip_tags($data['context'] ?? ''),
    'prompt'    => $data['prompt'] ?? '' // On garde le prompt brut généré
];

// Vérification minimale
if (empty($cleanData['task']) && empty($cleanData['role'])) {
    echo json_encode(['status' => 'ignored', 'message' => 'Empty content']);
    exit;
}

// 4. Stockage dans un fichier JSONL (JSON Lines)
// Format : Une ligne = Un objet JSON. Très robuste et facile à parser.
$file = __DIR__ . '/../data/suggestions.jsonl'; // Assurez-vous que le dossier "data" existe

// Création du dossier data s'il n'existe pas
if (!is_dir(__DIR__ . '/../data')) {
    mkdir(__DIR__ . '/../data', 0755, true);
}

// Protection : fichier .htaccess pour empêcher l'accès web direct aux données
$htaccess = __DIR__ . '/../data/.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Deny from all");
}

// Écriture
if (file_put_contents($file, json_encode($cleanData) . PHP_EOL, FILE_APPEND | LOCK_EX)) {
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Write failed']);
}
?>