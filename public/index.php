<?php
// TEST MODIFICATION POUR GIT STATUS - <?php // echo date('Y-m-d H:i:s'); ?> (Laissez ce commentaire de test ou supprimez-le une fois que Git voit les modifs)

/*
 * index.php (dans public/)
 * Point d'entrée de l'application MVC (Front Controller).
 * Gère l'affichage de la page d'accueil par défaut via le routeur (quand implémenté).
 */

// --- Configuration Initiale & Débogage des Chemins ---
// Afficher toutes les erreurs pendant le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Current Directory (getcwd()): " . getcwd() . "<br>";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "__DIR__: " . __DIR__ . "<br>";

// Le ROOT_PATH doit pointer vers la racine de votre projet 'eigaphp'
$rootPathCalculated = dirname(__DIR__); // Depuis /public/, dirname(__DIR__) remonte à /eigaphp/
echo "Calculated ROOT_PATH: " . $rootPathCalculated . "<br>";

$configFileExpectedPath = $rootPathCalculated . '/config/config.php';
echo "PHP is trying to include: " . $configFileExpectedPath . "<br>";

if (file_exists($configFileExpectedPath)) {
    echo "SUCCESS: Config file FOUND at " . $configFileExpectedPath . "<br>";
    // Si trouvé, nous pouvons maintenant l'inclure EN TOUTE SÉCURITÉ pour la suite
    // Mais pour le débogage initial du 404, on pourrait toujours vouloir voir les listes de dossiers
    // Donc, nous ne l'incluons PAS ici pour l'instant SI L'EXIT EST ACTIF
} else {
    echo "ERROR: Config file NOT FOUND at " . $configFileExpectedPath . "<br>";
    echo "Checking one level up for config directory in case ROOT_PATH is public itself... (Ne devrait pas être le cas ici)<br>";
    $altConfigFileExpectedPath = dirname(dirname(__DIR__)) . '/config/config.php'; // Si ROOT_PATH = public (improbable ici)
    echo "Alternative check path: " . $altConfigFileExpectedPath . "<br>";
    if (file_exists($altConfigFileExpectedPath)) {
         echo "SUCCESS: Config file FOUND at " . $altConfigFileExpectedPath . "<br>";
    } else {
         echo "ERROR: Config file NOT FOUND at " . $altConfigFileExpectedPath . " either.<br>";
    }
    // Essayons de lister le contenu de ROOT_PATH
    echo "<pre>Contents of ROOT_PATH (" . htmlspecialchars($rootPathCalculated) . "):\n";
    print_r(scandir($rootPathCalculated));
    echo "</pre>";
    // Essayons de lister le contenu de PUBLIC_PATH (qui est __DIR__ ici)
     $publicPathCalculated = __DIR__;
    echo "<pre>Contents of PUBLIC_PATH (" . htmlspecialchars($publicPathCalculated) . "):\n";
    print_r(scandir($publicPathCalculated));
    echo "</pre>";
    // Essayons de lister le contenu de un niveau AU-DESSUS de ROOT_PATH (ce serait TEST/ sur votre MAMP)
    $oneLevelUpFromRoot = dirname($rootPathCalculated);
     echo "<pre>Contents of one level up from ROOT_PATH (" . htmlspecialchars($oneLevelUpFromRoot) . "):\n";
    print_r(scandir($oneLevelUpFromRoot));
    echo "</pre>";

    exit; // Arrêter ici TANT que le fichier config.php n'est pas trouvé et que le 404 persiste
}

// ---- SI LE DEBUG A MONTRE "SUCCESS" ET QUE LE 404 EST RÉSOLU, VOUS POUVEZ DÉCOMMENTER LA LIGNE CI-DESSOUS ----
// ---- ET COMMENTER TOUT LE BLOC ECHO CI-DESSUS (DE getcwd() à exit;) ----
require_once ROOT_PATH . '/config/config.php'; // Inclut maintenant avec le chemin absolu correct

// ----- DÉBUT DU RESTE DE VOTRE LOGIQUE DE PAGE D'ACCUEIL OU D'APPEL AU ROUTEUR -----
// Pour l'instant, je vais garder votre logique de page d'accueil,
// même si dans un vrai MVC, cela serait géré par un contrôleur et des vues.

// --- Vérification si session_start() a déjà été appelé (normalement dans config.php ou App.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Fonctions d'aide pour récupérer les données de TMDB ---
// ... (Le reste de vos fonctions fetch_tmdb_movies et display_movie_grid_section restent ici pour l'instant) ...
if (!function_exists('fetch_tmdb_movies')) {
    function fetch_tmdb_movies($endpoint, $params = [], $max_results = 10) {
        // Assurez-vous que TMDB_API_KEY est disponible (elle vient de config.php)
        if (!defined('TMDB_API_KEY') || empty(TMDB_API_KEY) || TMDB_API_KEY === 'YOUR_ACTUAL_TMDB_API_KEY') {
            error_log("TMDB_API_KEY non configurée pour fetch_tmdb_movies (vérifiez que config.php est bien inclus AVANT).");
            return [];
        }
        $base_api_url = "https://api.themoviedb.org/3/";
        $default_params = [
            'api_key' => TMDB_API_KEY,
            'language' => 'fr-FR',
            'page' => 1
        ];
        $query_params = http_build_query(array_merge($default_params, $params));
        $url = $base_api_url . $endpoint . "?" . $query_params;

        $response_json = @file_get_contents($url);
        if ($response_json === false) {
            error_log("Erreur lors de la récupération des données TMDB pour l'endpoint: " . $endpoint);
            return [];
        }
        $data = json_decode($response_json, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['results'])) {
            error_log("Erreur de décodage JSON ou résultats manquants pour l'endpoint: " . $endpoint);
            return [];
        }
        return array_slice($data['results'], 0, $max_results);
    }
}

if (!function_exists('display_movie_grid_section')) {
    function display_movie_grid_section($title, $movies, $section_id = '') {
        // Assurez-vous que BASE_URL est défini (vient de config.php)
        if (!defined('BASE_URL')) {
            define('BASE_URL', '/'); // Valeur par défaut d'urgence, à corriger dans config.php
            error_log("BASE_URL non définie (vérifiez config.php et son inclusion). Utilisation de '/' par défaut.");
        }
        if (empty($movies)) {
            return;
        }
        $section_id_attr = $section_id ? 'id="' . htmlspecialchars($section_id) . '"' : '';
        echo '<section class="movie-list-section card" ' . $section_id_attr . ' aria-labelledby="' . htmlspecialchars(strtolower(str_replace(' ', '-', $title))) . '-heading">';
        echo '  <h2 id="' . htmlspecialchars(strtolower(str_replace(' ', '-', $title))) . '-heading">' . htmlspecialchars($title) . '</h2>';
        echo '  <div class="movies-grid homepage-grid">';

        foreach ($movies as $movie) {
            if (empty($movie['id']) || empty($movie['title'])) continue;

            $movie_id = (int)$movie['id'];
            $movie_title = htmlspecialchars($movie['title']);
            $poster_path = $movie['poster_path'] ?? null;
            $release_year = !empty($movie['release_date']) ? substr($movie['release_date'], 0, 4) : '';
            $link_title = $movie_title . ($release_year ? " ({$release_year})" : '');

            $poster_url = $poster_path
                ? "https://image.tmdb.org/t/p/w300" . htmlspecialchars($poster_path)
                : BASE_URL . "assets/images/no_poster_available.png";
            $poster_alt = $poster_path ? "Affiche de " . $movie_title : "Pas d'affiche disponible";

            echo '<article class="movie-item">';
            echo '  <a href="' . BASE_URL . 'movie_details.php?id=' . $movie_id . '" title="' . htmlspecialchars($link_title) . '" aria-label="Détails pour ' . htmlspecialchars($link_title) . '" class="movie-poster-link">';
            echo '    <img src="' . $poster_url . '" alt="' . htmlspecialchars($poster_alt) . '" loading="lazy" class="movie-poster-grid"/>';
            echo '  </a>';
            echo '  <div class="movie-item-info">';
            echo '    <h3 class="movie-item-title"><a href="' . BASE_URL . 'movie_details.php?id=' . $movie_id . '">' . $movie_title . '</a></h3>';
            if ($release_year) {
                echo '    <p class="movie-item-year">' . $release_year . '</p>';
            }
            echo '  </div>';
            echo '</article>';
        }
        echo '  </div>';
        echo '</section>';
    }
}

// --- Récupération des différentes listes de films ---
$pageTitle = "Accueil - " . (defined('SITE_NAME') ? SITE_NAME : "EigaNights"); // SITE_NAME vient de config.php
$number_of_movies_per_section = 12;

$trendingMovies = fetch_tmdb_movies('trending/movie/week', [], $number_of_movies_per_section);
$popularMovies = fetch_tmdb_movies('movie/popular', [], $number_of_movies_per_section);
$topRatedMovies = fetch_tmdb_movies('movie/top_rated', [], $number_of_movies_per_section);
$upcomingMovies = fetch_tmdb_movies('movie/upcoming', ['region' => 'FR'], $number_of_movies_per_section);

$searchResults = [];
$searchQueryDisplay = '';
if (isset($_GET['search'])) {
    $searchQueryParam = trim($_GET['search']);
    if (!empty($searchQueryParam)) {
        $searchQueryDisplay = htmlspecialchars($searchQueryParam, ENT_QUOTES, 'UTF-8');
        $pageTitle = "Recherche: " . $searchQueryDisplay . " - " . (defined('SITE_NAME') ? SITE_NAME : "EigaNights");
        $searchResults = fetch_tmdb_movies('search/movie', ['query' => $searchQueryParam], 20);
    }
}

// Avant d'inclure header.php, s'assurer que BASE_URL et $pageTitle sont définis.
// $pageTitle est déjà défini. BASE_URL vient de config.php
// Pour l'instant, includes/header.php et footer.php ne sont pas vraiment dans un style MVC "pur"
// mais nous essayons d'abord de faire fonctionner l'application de base.
// Dans un MVC pur, le header et footer seraient gérés par un layout dans les Vues.

// S'assurer que le chemin vers les includes est correct si ce fichier n'est pas un vrai MVC controller
// Dans un MVC avec la structure : eigaphp/app/Views/layouts/default.php
// Ce serait : include_once APP_PATH . '/Views/layouts/default.php';
// Pour votre structure actuelle, ce sera plutôt si header.php est à la racine de eigaphp/includes :
if (defined('ROOT_PATH')) { // ROOT_PATH est défini plus haut (eigaphp/)
    include_once ROOT_PATH . '/includes/header.php';
} else {
    // Solution de secours très basique, devrait être évitée.
    include_once __DIR__ . '/../includes/header.php'; // Moins robuste
}

?>

<main class="container homepage-content">

    <?php foreach (['message', 'error', 'warning'] as $msgKey): ?>
        <?php if (!empty($_SESSION[$msgKey])): ?>
            <div class="alert <?php echo $msgKey === 'error' ? 'alert-danger' : ($msgKey === 'warning' ? 'alert-warning' : 'alert-success'); ?>" role="alert">
                <?php echo htmlspecialchars($_SESSION[$msgKey]); unset($_SESSION[$msgKey]); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!empty($searchResults)): ?>
        <?php display_movie_grid_section('Résultats pour "' . $searchQueryDisplay . '"', $searchResults, 'search-results'); ?>
    <?php else: ?>
        <?php display_movie_grid_section('Films à la Tendance cette semaine', $trendingMovies, 'trending-movies'); ?>
        <?php display_movie_grid_section('Films Populaires du Moment', $popularMovies, 'popular-movies'); ?>
        <?php display_movie_grid_section('Films les Mieux Notés', $topRatedMovies, 'top-rated-movies'); ?>
        <?php display_movie_grid_section('Prochaines Sorties en France', $upcomingMovies, 'upcoming-movies'); ?>
    <?php endif; ?>

</main>

<?php
if (defined('ROOT_PATH')) {
    include_once ROOT_PATH . '/includes/footer.php';
} else {
    include_once __DIR__ . '/../includes/footer.php'; // Moins robuste
}
?>