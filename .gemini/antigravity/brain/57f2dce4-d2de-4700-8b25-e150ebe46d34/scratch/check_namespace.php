<?php
$paths = [
    'vendor/filament/filament/src/Schemas/Components/Section.php',
    'vendor/filament/schemas/src/Components/Section.php',
    'vendor/filament/forms/src/Components/Section.php',
];

foreach ($paths as $path) {
    echo $path . ": " . (file_exists($path) ? "EXISTS" : "MISSING") . "\n";
}
