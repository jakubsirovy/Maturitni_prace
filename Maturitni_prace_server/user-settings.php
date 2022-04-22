<?php
// Inicializace relace
session_start();
 
// Zjistí, jestli je uživatel přihlášený a jestli nevypršel jeho čas spojení
require "controllers/login-check.php";

// Zahrň login k databázi
require_once "controllers/config.php";

// Zjistí, jestli je uživatel Admin a jestli uživatel vůbec existuje. Pokud uživatel neexistuje, je odhlášen.
require "controllers/isadmin.php";
// Admin podmínka, pokdu admin == 0, presmeruje se na index.php
if($admin[0] != 1){
    header("location: index.php");
    exit;
}

// Zjistí, jestli je adminovi povolena registrace
require "controllers/is-allowed-admin-registration.php";

function changeBool($pdo, $col) {
    try {
        $userId =  $_POST['id'];
        $stmt = $pdo->prepare("SELECT $col FROM users WHERE id = :userid");
        $stmt->bindParam(":userid", $userId, PDO::PARAM_STR);
        $stmt->execute();
    
        // Výsledek SQL dotazu se uloží do proměnné
        $garaz = $stmt->fetch();

        // Pokud je 1 změň na 0 a obrácně
        if($garaz[0] == 1) {
            $stmt = $pdo->prepare("UPDATE users SET $col = 0 WHERE id = :userid");
            $stmt->bindParam(":userid", $userId, PDO::PARAM_STR);
            $stmt->execute();
        }
        else{
            $stmt = $pdo->prepare("UPDATE users SET $col = 1 WHERE id = :userid");
            $stmt->bindParam(":userid", $userId, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Podmínky s využití funkcí
if(isset($_POST['setAdmin'])){
    changeBool($pdo, "admin");
}

if(isset($_POST['brana1'])){
    changeBool($pdo, "brana1");
}

if(isset($_POST['brana2'])){
    changeBool($pdo, "brana2");
}

if(isset($_POST['garaz'])){
    changeBool($pdo, "garaz");
}

// Odstranění uživatele
if(isset($_POST['deleteUser'])){
    try {
        $userId = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :userid");
        $stmt->bindParam(":userid", $userId, PDO::PARAM_STR);
        $stmt->execute();
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
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
            <?php echo '<li><a class="dropdown-item" href="controllers/deleteme.php" onClick="return confirmSubmitMe(\'' . $_SESSION["username"] . '\');">Odstranit účet</a></li>';?>
            <li><a class="dropdown-item" href="controllers/logout.php">Odhlásit se</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid pt-3 pb-5 bg-dark text-white text-center">
    <h1 class="pt-5">Vítej uživateli <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>!</h1>
</div>

<?php
try {
    $stmt = $pdo->prepare("SELECT id, username, admin, brana1, brana2, garaz FROM users");
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '  <table><tr>
                <th>Jméno</th>
                <th>Admin</th>
                <th>Brána 1</th>
                <th>Brána 2</th>
                <th>Garáž</th>
                <th>Odstranit uživatele</th>
            </tr>';
    foreach ($users as $user) {
        echo '<tr><td>' . $user['username'] . '</td><td>
        <form method="POST">
        <input type="hidden" name="id" value="'. $user['id'] .'">
        <input type="submit" name="setAdmin" value="'. $user['admin'] .'"/>
        </form>
        </td><td>
        <form method="POST">
        <input type="hidden" name="id" value="'. $user['id'] .'">
        <input type="submit" name="brana1" value="'. $user['brana1'] .'"/>
        </form>
        </td><td>
        <form method="POST">
        <input type="hidden" name="id" value="'. $user['id'] .'">
        <input type="submit" name="brana2" value="'. $user['brana2'] .'"/>
        </form>
        </td><td>
        <form method="POST">
        <input type="hidden" name="id" value="'. $user['id'] .'">
        <input type="submit" name="garaz" value="'. $user['garaz'] .'"/>
        </form>
        </td><td>
        <form method="POST">
        <input type="hidden" name="id" value="'. $user['id'] .'">
        <input type="submit" name="deleteUser" value="x" onClick="return confirmSubmit(\''. $user['username'] .'\');"/>
        </form>
        </td></tr>';
    }
    echo '</table>';
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
unset($stmt);
unset($pdo);
?>

<script>
function confirmSubmit(deleteUsername) {
var agree = confirm("Opravdu si přejete odstranit uživatele " + deleteUsername + "?");
if (agree)
    return true;
else
    return false;
}
function confirmSubmitMe(deleteMe) {
var agree = confirm("Opravdu si přejete odstranit svůj účet " + deleteMe + "?");
if (agree)
    return true;
else
    return false;
}
</script>

</body>
</html>