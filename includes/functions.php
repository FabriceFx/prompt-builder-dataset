<?php
/**
 * Translation helper
 * @param string $key
 * @return string
 */
function t($key)
{
    global $trans;
    return $trans[$key] ?? $key;
}

/**
 * Helper to generate URL
 * @param string $path Route path (e.g., '/logic')
 * @return string Full URL
 */
function url($path = '')
{
    // Ensure path starts with / if not empty
    if ($path && $path[0] !== '/') {
        $path = '/' . $path;
    }
    return BASE_URL . $path;
}


/**
 * Load JSON file with error handling
 * @param string $path
 * @return array
 */
function loadJson($path)
{
    if (!file_exists($path)) {
        die("Error: JSON file not found at $path");
    }
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error: Invalid JSON format in $path - " . json_last_error_msg());
    }
    return $data;
}

/**
 * String cleaning for sorting (removes accents etc.)
 * @param string $str
 * @return string
 */
function cleanForSort($str)
{
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(
    ['à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'],
    ['a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y'],
        $str
    );
    return $str;
}
?>