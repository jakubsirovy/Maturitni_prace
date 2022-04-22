<?php
// Zjistí, jestli je adminovi povolena registrace
try {
    $stmt = $pdo->prepare("SELECT adminRegistrationAllowed FROM settings");
    $stmt->execute();

    $aAllowed = $stmt->fetch();
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>