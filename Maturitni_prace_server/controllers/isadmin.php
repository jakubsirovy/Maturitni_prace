<?php
// Zjistí, jestli je uživatel Admin a jestli uživatel vůbec existuje. Pokud uživatel neexistuje, je odhlášen.
try {
    // Nastaví parametry
    $username = $_SESSION["username"];

    $stmt = $pdo->prepare("SELECT admin FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username, PDO::PARAM_STR); // :username = proměnná
    
    // Vykoná SQL příkaz
    $stmt->execute();

    // Pokud SQL vrátí řádek, tak existuje uživatel a je možné vyzvednout data
    if($stmt->rowCount() == 1) {
        $admin = $stmt->fetch();
    }
    // Pokud neexistuje uživatel -> odhlásit
    else {
        include "logout.php";
    }
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>