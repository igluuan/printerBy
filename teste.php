<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "sql100.infinityfree.com";
$db   = "if0_40966513_impressoras_db";
$user = "if0_40966513";
$pass = "4STHsXIEG1WINn";

echo "DNS: ";
var_dump(gethostbyname($host));
echo "<br><br>";

try {
    new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "✅ Conexão OK";
} catch (PDOException $e) {
    echo "❌ ERRO PDO:<br>" . $e->getMessage();
}
