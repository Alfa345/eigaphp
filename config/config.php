<?php
// config.php — EigaNights site config + DB connection

// Inclure les fonctions globales (si generate_simulated_ad_slot_content est ici)
require_once __DIR__ . '/includes/function.php'; // Assurez-vous que ce fichier existe et contient la fonction

// Dans config.php (SI VOUS UTILISEZ Google reCAPTCHA)
define('6LdUwkMrAAAAALdfb_MYp27XCtKyVpdReqTQ86gK', '6LdUwkMrAAAAAO3kMtU1Aioiu03pWsT9Od7GHPxZ');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Définir ROOT_PATH pour une utilisation globale dans l'application (facultatif mais utile)
// Ce chemin pointe vers la racine de votre projet 'eigaphp' sur le serveur.
define('ROOT_PATH', dirname(__DIR__));

// Inclure le fichier de configuration principal
require_once ROOT_PATH . '/config/config.php'; // Devrait maintenant fonctionner

// ─────────────────────────────────────────────────────────────────────────────
// 0) Site Information
// ─────────────────────────────────────────────────────────────────────────────
define('SITE_NAME', 'EigaNights');

// ─────────────────────────────────────────────────────────────────────────────
// 1) DEV error reporting
// ─────────────────────────────────────────────────────────────────────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 2);
error_reporting(E_ALL);

// ─────────────────────────────────────────────────────────────────────────────
// 2) Session setup
// ─────────────────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─────────────────────────────────────────────────────────────────────────────
// 3) Database settings
// ─────────────────────────────────────────────────────────────────────────────
// In config.php
define('DB_HOST', getenv('MYSQL_HOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'eiganights');
define('DB_USER', getenv('MYSQL_USER') ?: 'root');
define('DB_PASS', getenv('MYSQL_PASSWORD') ?: 'root');
// You might need to remove DB_PORT if Herogu's MYSQL_HOST includes it or if it's standard port.

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$conn = $mysqli;
if ($mysqli->connect_errno) {
    error_log("MySQL connect failed ({$mysqli->connect_errno}): {$mysqli->connect_error}");
    die("Sorry—database temporarily unavailable. Please try again later.");
}
$mysqli->set_charset('utf8mb4');

// ─────────────────────────────────────────────────────────────────────────────
// 4) TMDB API key
// ─────────────────────────────────────────────────────────────────────────────
define('TMDB_API_KEY', '94fc3b99fd623dc63ae00ab80ca1b255'); // Votre clé

// ─────────────────────────────────────────────────────────────────────────────
// 5) Base URL helper
// ─────────────────────────────────────────────────────────────────────────────
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$domain   = $_SERVER['HTTP_HOST'];
$script   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $protocol . "://" . $domain . $script . '/');


// ─────────────────────────────────────────────────────────────────────────────
// 6) Monetization Settings (School Project Simulation - Simplified Random GIFs)
// ─────────────────────────────────────────────────────────────────────────────

define('PLACEHOLDER_ADS_ENABLED', true); // Mettez à false pour cacher les pubs simulées

// Chemin vers le dossier contenant VOS GIFs publicitaires (relatif à la racine du projet)
// ACTION : Assurez-vous que ce chemin est correct et que le dossier contient vos GIFs.
define('RANDOM_GIF_ADS_DIRECTORY', 'assets/videos/'); // Ou 'assets/videos/ads/' si vous avez un sous-dossier 'ads'

// Texte alternatif par défaut pour les GIFs publicitaires
define('DEFAULT_AD_GIF_ALT_TEXT', 'Publicité animée EigaNights');

// Optionnel: Lien par défaut si une pub GIF est cliquable
// define('DEFAULT_AD_GIF_LINK', 'https://votre-site.com/page-promo');


// -- Direct Streaming Links (Simulation) -- (Cette partie reste la même)
define('DIRECT_STREAMING_LINKS_ENABLED', true);
if (!defined('ALLOWED_API_REGIONS')) {
  define('ALLOWED_API_REGIONS', ['FR', 'US']);
}
define('STREAMING_PLATFORMS_OFFICIAL_LINKS', [
    8 => [
        'name' => 'Netflix',
        'logo' => 'assets/images/netflix_logo.png',
        'search_url_pattern' => 'https://www.netflix.com/search?q={MOVIE_TITLE_URL_ENCODED}' // Usually works very well
    ],
    10 => [
        'name' => 'Amazon Prime Video',
        'logo' => 'assets/images/primevideo_logo.png', // Corrected path: assuming assets/images/primevideo_logo.png
        // Option 1: More universal Prime Video link (often redirects to local version)
        'search_url_pattern' => 'https://www.primevideo.com/search/?phrase={MOVIE_TITLE_URL_ENCODED}'
        // Option 2: Stick to French site if that's your primary target
        // 'search_url_pattern' => 'https://www.amazon.fr/s?k={MOVIE_TITLE_URL_ENCODED}&i=instant-video'
    ],
    337 => [
        'name' => 'Disney+',
        'logo' => 'assets/images/disney_logo.png',
        'search_url_pattern' => 'https://www.disneyplus.com/search?q={MOVIE_TITLE_URL_ENCODED}' // Standard search
    ],
    2 => [
        'name' => 'Apple TV',
        'logo' => 'assets/images/appletv_logo.png',
        // This pattern allows Apple to use the user's current store region or default.
        // For truly region-specific links, your PHP code would need to inject the region. See notes below.
        'search_url_pattern' => 'https://tv.apple.com/search?term={MOVIE_TITLE_URL_ENCODED}'
    ],
]);
?>