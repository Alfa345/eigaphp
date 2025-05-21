<?php
// TEST DEBUG ACTIF - <?php echo date('Y-m-d H:i:s'); ?>

// Afficher toutes les erreurs pendant le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "--- DEBUG START ---<br>";
echo "Current Directory (getcwd()): " . getcwd() . "<br>";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "__DIR__: " . __DIR__ . "<br>";

$rootPathCalculated = dirname(__DIR__);
echo "Calculated ROOT_PATH: " . $rootPathCalculated . "<br>";

$configFileExpectedPath = $rootPathCalculated . '/config/config.php';
echo "Expecting config at: " . $configFileExpectedPath . "<br>";

if (file_exists($configFileExpectedPath)) {
    echo "<strong style='color:green;'>SUCCESS: Config file was FOUND at " . $configFileExpectedPath . "</strong><br>";
} else {
    echo "<strong style='color:red;'>ERROR: Config file was NOT FOUND at " . $configFileExpectedPath . "</strong><br>";
}

echo "<pre>Contents of ROOT_PATH (" . htmlspecialchars($rootPathCalculated) . "/):\n";
if (is_dir($rootPathCalculated)) {
    print_r(scandir($rootPathCalculated));
} else {
    echo "ROOT_PATH is not a directory or does not exist.\n";
}
echo "</pre>";

$publicPathCalculated = __DIR__;
echo "<pre>Contents of PUBLIC_PATH (" . htmlspecialchars($publicPathCalculated) . "/):\n";
if (is_dir($publicPathCalculated)) {
    print_r(scandir($publicPathCalculated));
} else {
    echo "PUBLIC_PATH is not a directory or does not exist.\n";
}
echo "</pre>";
echo "--- DEBUG END ---<br>";

// Pour l'instant, nous arrêtons TOUJOURS ici pour voir la sortie du débogage.
exit;

// LE RESTE DE VOTRE FICHIER INDEX.PHP (Y COMPRIS LE require_once problématique)
// DEVRAIT ÊTRE APRÈS CETTE LIGNE "exit;".
// Example:
// require_once $rootPathCalculated . '/config/config.php';
// ... reste du code ...

?>