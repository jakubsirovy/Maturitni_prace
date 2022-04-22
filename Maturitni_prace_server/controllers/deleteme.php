<?php
// inicializuje relaci
session_start();

// Zjistí, jestli je uživatel přihlášený, pokud ne, přesměruj na login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: \login.php");
    exit;
}

// Pokud vyprší čas, tak zruš relaci připojení (po znovunačtení stránky bude uživatel odhlášen)
if (time() > $_SESSION['expire']) {
    session_destroy();
}

// Zjistí jméno uživatele a odstraní ho z databáze
try {
    // Zahrň login k databázi
    require_once "config.php";
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $username = $_SESSION["username"];
    $stmt->execute();
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
 
// Zruší všechny proměnné relace
$_SESSION = array();
 
// Zničí relaci
session_destroy();
 
// Přesměruje na přihlašovací stránku
header("location: \login.php");
exit;
?>