<?php
// Base URL configuration (auto-detect or hardcoded)
// In production, you might want to hardcode this or use $_SERVER['HTTP_HOST']
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = "$protocol://$host" . ($scriptDir === '/' ? '' : $scriptDir);
define('BASE_URL', $baseUrl);

// Debug Configuration
// TODO: Passer à 'false' lors de la mise en ligne (production) pour masquer les erreurs
define('DEBUG_MODE', true);

// Language configuration
define('AVAILABLE_LANGS', ['fr', 'en']);
define('DEFAULT_LANG', 'fr');

// Paths (optional constants)
define('ROOT_PATH', dirname(__DIR__));
define('LANG_PATH', ROOT_PATH . '/lang');
define('INCLUDES_PATH', __DIR__);
?>