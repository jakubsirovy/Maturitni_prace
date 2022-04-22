<?php
// Zjistí, jestli je uživatel přihlášený, pokud ne, přesměruj na login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Pokud vyprší čas, tak zruš relaci připojení (po znovunačtení stránky bude uživatel odhlášen)
if (time() > $_SESSION['expire']) {
    session_destroy();
}
?>