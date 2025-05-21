<?php
// --- DEBUG CHEMINS ACTIF V2 ---
$timestamp = date('Y-m-d H:i:s');
echo "<h1>DEBUG DES CHEMINS - VERSION : " . $timestamp . "</h1>";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Informations de Base</h2>";
echo "Current Directory (getcwd()): " . htmlspecialchars(getcwd(), ENT_QUOTES, 'UTF-8') . "<br>";
echo "SCRIPT_FILENAME: " . htmlspecialchars($_SERVER['SCRIPT_FILENAME'], ENT_QUOTES, 'UTF-8') . "<br>";
echo "__DIR__ (Répertoire du script courant - public/): " . htmlspecialchars(__DIR__, ENT_QUOTES, 'UTF-8') . "<br>";

// Le ROOT_PATH doit pointer vers la racine de votre projet 'eigaphp' (qui est /var/www/html/ sur le serveur)
$rootPathCalculated = dirname(__DIR__); // Depuis /public/, dirname(__DIR__) remonte à /
echo "Calculated ROOT_PATH (devrait être la racine de eigaphp sur serveur): " . htmlspecialchars($rootPathCalculated, ENT_QUOTES, 'UTF-8') . "<br>";

$configFileExpectedPath = $rootPathCalculated . '/config/config.php';
echo "Chemin attendu pour config.php : " . htmlspecialchars($configFileExpectedPath, ENT_QUOTES, 'UTF-8') . "<br>";

echo "<h2>Vérification de l'existence du fichier config.php</h2>";
if (file_exists($configFileExpectedPath)) {
    echo "<p style='color:green; font-weight:bold;'>SUCCÈS : Le fichier config.php A ÉTÉ TROUVÉ à : " . htmlspecialchars($configFileExpectedPath, ENT_QUOTES, 'UTF-8') . "</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>ERREUR : Le fichier config.php N'A PAS ÉTÉ TROUVÉ à : " . htmlspecialchars($configFileExpectedPath, ENT_QUOTES, 'UTF-8') . "</p>";
}

echo "<h2>Contenu du 'Calculated ROOT_PATH' (" . htmlspecialchars($rootPathCalculated) . "/) :</h2>";
echo "<pre>";
if (is_dir($rootPathCalculated)) {
    print_r(scandir($rootPathCalculated));
} else {
    echo "ERREUR : " . htmlspecialchars($rootPathCalculated, ENT_QUOTES, 'UTF-8') . " n'est pas un dossier ou n'existe pas.";
}
echo "</pre>";

$publicPathCalculated = __DIR__; // __DIR__ est public/
echo "<h2>Contenu du dossier 'public' (" . htmlspecialchars($publicPathCalculated) . "/) :</h2>";
echo "<pre>";
if (is_dir($publicPathCalculated)) {
    print_r(scandir($publicPathCalculated));
} else {
    echo "ERREUR : " . htmlspecialchars($publicPathCalculated, ENT_QUOTES, 'UTF-8') . " n'est pas un dossier ou n'existe pas.";
}
echo "</pre>";

echo "<hr>Fin du débogage. Le script s'arrête ici avec exit().";
exit; // TRÈS IMPORTANT: Arrêter le script ici pour voir la sortie du débogage
?>