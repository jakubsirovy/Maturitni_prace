<?php
// Inicializace relace
session_start();

// Zjistí, jestli je uživatel přihlášený a jestli nevypršel jeho čas spojení
require "controllers/login-check.php";

// Zahrň login k databázi
require_once "controllers/config.php";

// Zjistí, jestli je adminovi povolena registrace
require "controllers/is-allowed-admin-registration.php";
// Podmínka pro povolení registrace
if($aAllowed[0] != 1){
    header("location: index.php");
    exit;
}

// Zjistí, jestli je uživatel Admin a jestli uživatel vůbec existuje. Pokud uživatel neexistuje, je odhlášen.
require "controllers/isadmin.php";
// Admin podmínka, pokdu admin == 0, presmeruje se na index.php
if($admin[0] != 1){
    header("location: index.php");
    exit;
}

// Registrace
require "controllers/register-request.php";
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
<div class="wrapper wrapper-switch">
    <h2>Registrace</h2>
    <p>Vyplňte tento formulář pro založení nového účtu.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="mt-3 mb-3">
            <label><b>Uživatelské jméno</b></label>
            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>    
        <div class="mt-3 mb-3">
            <label><b>Heslo</b></label>
            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="mt-3 mb-3">
            <label><b>Potvrdit heslo</b></label>
            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="mt-3 mb-3">
            <input type="submit" class="btn btn-primary" value="Povtrdit">
            <input style="float: right; margin: 3px;" type="reset" class="btn btn-link" value="Resetovat">
        </div>
    </form>
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

</body>
</html>