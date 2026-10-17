<?php

echo '<h2>GOF3-light PHP/GD Test</h2>';

echo '<p>PHP Version: ' . PHP_VERSION . '</p>';

echo '<p>Loaded php.ini: ' .
    htmlspecialchars((string) php_ini_loaded_file()) .
    '</p>';

echo '<p>GD extension: ';
var_dump(extension_loaded('gd'));
echo '</p>';

echo '<p>imagecreatetruecolor(): ';
var_dump(function_exists('imagecreatetruecolor'));
echo '</p>';

echo '<p>imagepng(): ';
var_dump(function_exists('imagepng'));
echo '</p>';

echo '<p>Endroid Builder: ';
require_once __DIR__ . '/../vendor/autoload.php';
var_dump(class_exists('Endroid\\QrCode\\Builder\\Builder'));
echo '</p>';