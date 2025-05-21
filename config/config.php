<?php
// config/config.php — EigaNights site config + DB connection

// ─────────────────────────────────────────────────────────────────────────────
// 1) DEV error reporting & Essential Setup
// ─────────────────────────────────────────────────────────────────────────────
ini_set('display_errors', 1); // Set once at the top
ini_set('display_startup_errors', 1); // Changed from 2 for consistency
error_reporting(E_ALL);

// Définir ROOT_PATH pour une utilisation globale si not already defined by index.php
// This is a fallback in case config.php is included directly elsewhere, though not typical.
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__)); // Assumes config.php is in a 'config' subdirectory of the root
}

// Inclure les fonctions globales
// Path corrected to use ROOT_PATH
require_once ROOT_PATH . '/includes/function.php';

// ─────────────────────────────────────────────────────────────────────────────
// 0) Site Information (Moved after ROOT_PATH and function.php include)
// ─────────────────────────────────────────────────────────────────────────────
define('SITE_NAME', 'EigaNights');

// Corrected Google reCAPTCHA constants definition
define('RECAPTCHA_SITE_KEY', '6LdUwkMrAAAAALdfb_MYp27XCtKyVpdReqTQ86gK');
define('RECAPTCHA_SECRET_KEY', '6LdUwkMrAAAAAO3kMtU1Aioiu03pWsT9Od7GHPxZ');

// ─────────────────────────────────────────────────────────────────────────────
// 2) Session setup
// ─────────────────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─────────────────────────────────────────────────────────────────────────────
// 3) Database settings
// ─────────────────────────────────────────────────────────────────────────────
define('DB_HOST', getenv('MYSQL_HOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'eiganights');
define('DB_USER', getenv('MYSQL_USER') ?: 'root');
define('DB_PASS', getenv('MYSQL_PASSWORD') ?: 'root');
define('DB_PORT', (int)(getenv('MYSQL_PORT') ?: 3306)); // Define DB_PORT, default to 3306

// Ensure $conn is used consistently as $mysqli is scoped locally
// $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT); // Use $conn directly

if ($conn->connect_errno) {
    error_log("MySQL connect failed ({$conn->connect_errno}): {$conn->connect_error}");
    // Ensure output is safe if display_errors is on but you want a generic message
    // Consider not die()ing here if header.php/footer.php should still render a nice error page.
    die("Sorry—database temporarily unavailable. Please try again later. (DB Error: " . $conn->connect_errno . ")");
}
$conn->set_charset('utf8mb4');
// $mysqli is no longer needed if we use $conn throughout the project
// Global $mysqli variable if used elsewhere (legacy code might expect $mysqli specifically)
// if you use $mysqli in other files, you'll need $mysqli = $conn; here.
// However, most of your legacy files use $conn.

// ─────────────────────────────────────────────────────────────────────────────
// 4) TMDB API key
// ─────────────────────────────────────────────────────────────────────────────
define('TMDB_API_KEY', '94fc3b99fd623dc63ae00ab80ca1b255'); // Votre clé

// ─────────────────────────────────────────────────────────────────────────────
// 5) Base URL helper
// ─────────────────────────────────────────────────────────────────────────────
// Ensure _SERVER variables are set (they should be in a web context)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$domain = $_SERVER['HTTP_HOST'] ?? 'localhost'; // Fallback for CLI or misconfigured server
$script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php'; // Fallback
$script = rtrim(dirname($script_name), '/\\');
define('BASE_URL', $protocol . "://" . $domain . $script . '/');


// ─────────────────────────────────────────────────────────────────────────────
// 6) Monetization Settings (School Project Simulation - Simplified Random GIFs)
// ─────────────────────────────────────────────────────────────────────────────
define('PLACEHOLDER_ADS_ENABLED', true);
define('RANDOM_GIF_ADS_DIRECTORY', 'assets/videos/');
define('DEFAULT_AD_GIF_ALT_TEXT', 'Publicité animée EigaNights');

define('DIRECT_STREAMING_LINKS_ENABLED', true);
if (!defined('ALLOWED_API_REGIONS')) {
  define('ALLOWED_API_REGIONS', ['FR', 'US']);
}
define('STREAMING_PLATFORMS_OFFICIAL_LINKS', [
    8 => [
        'name' => 'Netflix',
        'logo' => 'assets/images/netflix_logo.png',
        'search_url_pattern' => 'https://www.netflix.com/search?q={MOVIE_TITLE_URL_ENCODED}'
    ],
    10 => [
        'name' => 'Amazon Prime Video',
        'logo' => 'assets/images/primevideo_logo.png',
        'search_url_pattern' => 'https://www.primevideo.com/search/?phrase={MOVIE_TITLE_URL_ENCODED}'
    ],
    337 => [
        'name' => 'Disney+',
        'logo' => 'assets/images/disney_logo.png',
        'search_url_pattern' => 'https://www.disneyplus.com/search?q={MOVIE_TITLE_URL_ENCODED}'
    ],
    2 => [
        'name' => 'Apple TV',
        'logo' => 'assets/images/appletv_logo.png',
        'search_url_pattern' => 'https://tv.apple.com/search?term={MOVIE_TITLE_URL_ENCODED}'
    ],
]);
?>