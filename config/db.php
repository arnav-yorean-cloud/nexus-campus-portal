<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// New live cloud routing parameters for great-site.net container
$host = 'sql300.infinityfree.com'; 
$dbname = 'if0_42123234_campus_utility'; 
$username = 'if0_42123234'; 
$password = 'joolPmgVPh16kT'; // Read directly from your unmasked panel string

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection dropped: " . $e->getMessage());
}
?>
