<?php
// Zahrň login k databázi
require_once "controllers/config.php";

// Zjistí, jestli je uživateli povolena registrace
require "controllers/is-allowed-user-registration.php";

// Podmínka pro povolení registrace
if($uAllowed[0] != 1){
    header("location: login.php");
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
    <link rel="stylesheet" href="stylesheet/styyyl.css">
    <title>Registrace</title>
</head>
<body class="login-body">
    <div class="center-wrapper wrapper-login">
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
                <input style="float: right;" type="reset" class="btn btn-link ml-2" value="Resetovat">
            </div>
            <p>Máte účet? <a href="login.php">Přihlašte se zde</a>.</p>
        </form>
    </div>
</body>
</html>