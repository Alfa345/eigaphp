<?php
// --- FICHIER DE TEST HAUTEMENT VISIBLE V3 ---
$timestamp = date('Y-m-d H:i:s');
echo "<h1>CONFIRMATION DE DÉPLOIEMENT SUR HEROGU</h1>";
echo "<p style='font-size: 24px; color: green; font-weight: bold;'>VERSION DU : " . $timestamp . "</p>";
echo "<p>Si ce message apparaît, c'est que CE fichier exact a été déployé.</p>";
echo "<p>SCRIPT_FILENAME: " . htmlspecialchars($_SERVER['SCRIPT_FILENAME'], ENT_QUOTES, 'UTF-8') . "</p>";
echo "<p>__DIR__: " . htmlspecialchars(__DIR__, ENT_QUOTES, 'UTF-8') . "</p>";
exit; // Arrête tout ici
?>