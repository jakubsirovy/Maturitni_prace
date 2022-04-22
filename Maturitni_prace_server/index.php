<?php
// Inicializace relace
session_start();
 
// Zjistí, jestli je uživatel přihlášený a jestli nevypršel jeho čas spojení
require "controllers/login-check.php";

// Zahrň login k databázi
require_once "controllers/config.php";

// Zjistí, jestli je uživatel Admin a jestli uživatel vůbec existuje. Pokud uživatel neexistuje, je odhlášen.
require "controllers/isadmin.php";

// Zjistí, jestli je adminovi povolena registrace
require "controllers/is-allowed-admin-registration.php";

function branaLog($pdo, $username, $element) {
    // Zkontrolovat chyby před vložením do databáze
    if(!empty($element)){

        // Proměnná s příkazem pro databázi
        $sql = "INSERT INTO brana_log (user, element) VALUES (:user, :element)";
         
        if($stmt = $pdo->prepare($sql)){
            // Proměnné
            $param_user = $username;
            $param_element = $element;
            // Přiřadí proměnné jako parametry pro předem připravený databázový příkaz
            $stmt->bindParam(":user", $param_user, PDO::PARAM_STR);
            $stmt->bindParam(":element", $param_element, PDO::PARAM_STR);
            
            // Vykoná připravený databázový příkaz
            $stmt->execute();
        }
    }
}

// Funkce sloužící k odesílání JSON požadavku pro bránu
function branaController($pdo, $username) {
    $data = array("element" => "akce");                                                                  
    $data_string = json_encode($data);
    
    //curl pozadavek
    if(isset($_POST['element']) && $_POST['element'] == "brana1")
    {
        $ch = curl_init('http://192.168.1.106/');
    }
    if(isset($_POST['element']) && $_POST['element'] == "brana2")
    {
        $ch = curl_init('http://192.168.1.13/');
    }
    if(isset($_POST['element']) && $_POST['element'] == "garaz")
    {
        $ch = curl_init('http://localhost/');
        print_r("Pozor, není nastaveno umístění odesílaní požadavku");
    }
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);
    branaLog($pdo, $username, $_POST['element']);
}

try {
    // Proměnná
    $username = $_SESSION["username"];

    $stmt = $pdo->prepare("SELECT brana1, brana2, garaz FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    
    $stmt->execute();

    // Nastaví výsledné pole jako asociativní
    $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rules as $rule) {
        $brana1 = $rule['brana1'];
        $brana2 = $rule['brana2'];
        $garaz = $rule['garaz'];
    }
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Tlačítka (zavřít, otevřít)
if(isset($_POST['button']) && $rule[$_POST['element']] == 1) {
    if($_POST['button'] == 'Otevřít / Zavřít') {
        branaController($pdo, $username);
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="stylesheet/styyyl.css">
    <title>Brána</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">SPS-CL Brána</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item">
            <?php if($admin[0] == 1) {echo '<a class="nav-item nav-link active" href="log.php">Log</a>';}?>
        </li>
        <li class="nav-item">
            <?php if($admin[0] == 1) {echo '<a class="nav-item nav-link active" href="users.php">Uživatelé</a>';}?>
        </li>
        <li class="nav-item">
            <?php if($admin[0] == 1) {echo '<a class="nav-item nav-link active" href="user-settings.php">Nastavení uživatelů</a>';}?>
        </li>
        <li class="nav-item">
            <?php if($admin[0] == 1) {echo '<a class="nav-item nav-link active" href="settings.php">Nastavení registrací</a>';}?>
        </li>
        <li class="nav-item">
            <?php if($admin[0] == 1 && $aAllowed[0] == 1) {echo '<a class="nav-item nav-link active" href="admin-register.php">Registrace uživatelů</a>';}?>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Nastavení</a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="reset-password.php">Změnit heslo</a></li>
            <?php echo '<li><a class="dropdown-item" href="controllers/deleteme.php" onClick="return confirmSubmitMe(\'' . $_SESSION["username"] . '\')">Odstranit účet</a></li>';?>
            <li><a class="dropdown-item" href="controllers/logout.php">Odhlásit se</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid pt-3 pb-5 bg-dark text-white text-center position-relative">
    <h1 class="pt-5">Vítej uživateli <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>!</h1>
</div>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100">
  <path fill="#212529" fill-opacity="1" d="M0,64L120,53.3C240,43,480,21,720,16C960,11,1200,21,1320,26.7L1440,32L1440,0L1320,0C1200,0,960,0,720,0C480,0,240,0,120,0L0,0Z"></path>
</svg>

<div class="container mt-5 index-body">
    <div class="row">
    <?php if($brana1 == 1) {echo '
        <div class="col-sm">
            <h3>Brána 1</h3>

            <form method="POST" class="m-3">
                <input type="hidden" name="element" value="brana1">
                <input class="action btn btn-primary btn-action" type="submit" name="button" value="Otevřít / Zavřít"/>
            </form>
        </div>
        ';}?>
        <?php if($brana2 == 1) {echo '
        <div class="col-sm">
            <h3>Brána 2</h3>
            
            <form method="POST" class="m-3">
                <input type="hidden" name="element" value="brana2">
                <input class="action btn btn-primary btn-action" type="submit" name="button" value="Otevřít / Zavřít"/>
            </form>
        </div>
        ';}?>
        <?php if($garaz == 1) {echo '
        <div class="col">
            <h3>Garáž</h3>
            
            <form method="POST" class="m-3">
                <input type="hidden" name="element" value="garaz">
                <input class="action btn btn-primary btn-action" type="submit" name="button" value="Otevřít / Zavřít"/>
            </form>
        </div>
        ';}?>
    </div>
</div>

<script>
function confirmSubmitMe(deleteMe) {
var agree = confirm("Opravdu si přejete odstranit svůj účet " + deleteMe + "?");
if (agree)
    return true;
else
    return false;
}
</script>

<?php
// Close statement
unset($stmt);
// Close connection
unset($pdo);
?>

</body>
</html>