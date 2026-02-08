<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Language Detection Logic
// 1. URL Parameter
if (isset($_GET['lang']) && in_array($_GET['lang'], AVAILABLE_LANGS)) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
}
// 2. Session
elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], AVAILABLE_LANGS)) {
    $lang = $_SESSION['lang'];
}
// 3. Browser / Default
else {
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? DEFAULT_LANG, 0, 2);
    $lang = in_array($browser_lang, AVAILABLE_LANGS) ? $browser_lang : DEFAULT_LANG;
}

// Load Translations
$trans = require LANG_PATH . '/fr.php'; // Always load default first (fallback)

if ($lang !== 'fr' && file_exists(LANG_PATH . '/' . $lang . '.php')) {
    $trans_specific = require LANG_PATH . '/' . $lang . '.php';
    $trans = array_merge($trans, $trans_specific);
}
?>