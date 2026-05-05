<?php
$src = 'c:\xampp\htdocs\ppl - Copy (2)\nora2.0\norav3\resources\views\dashboard\registrasi.php';
$dest = 'c:\xampp\htdocs\ppl - Copy (2)\nora2.0\norav4\public\assets\css\dashboard.css';
$lines = file($src);
$css = implode("", array_slice($lines, 40, 84));
file_put_contents($dest, "\n/* V3 Registrasi Restored CSS */\n" . $css, FILE_APPEND);
echo "CSS appended successfully.\n";
