<?php
// Zjistí, jestli je uživateli povolena registrace
try {
    $stmt = $pdo->prepare("SELECT userRegistrationAllowed FROM settings");
    $stmt->execute();

    $uAllowed = $stmt->fetch();
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>