<?php
header('Content-Type: application/json');

// setting toren/galon bisa kamu ganti di sini
$config = [
    "tinggi" => 20,   // cm, tinggi toren/galon penuh
    "volume" => 2    // Liter, volume penuh
];

echo json_encode($config);
?>
