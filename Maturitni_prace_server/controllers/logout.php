<?php
// Inicializuje relaci
session_start();
 
// Zruší všechny proměnné relace
$_SESSION = array();
 
// Zničí relaci
session_destroy();
 
// přesměruje na přihlašovací stránku
header("location: ../login.php");
exit;
?>