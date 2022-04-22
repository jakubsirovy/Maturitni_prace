<?php
// Initialize the session
session_start();
 
// Zjistí, jestli je uživatel přihlášený a jestli nevypršel jeho čas spojení
require "controllers/login-check.php";

// Zahrň login k databázi
require_once "controllers/config.php";
 
// Definuje proměnné a inicializuje je prázdnými proměnnými
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Zpracování údajů formuláře při odeslání formuláře
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Ověří nové heslo
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Zadejte prosím nové heslo.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Heslo musí obsahovat alespoň 6 znaků.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Ověří potvrzení hesla
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Prosím potvrďte heslo.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Hesla se neshodují.";
        }
    }
        
    // Zkontroluje chyby před vložením do databáze
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Proměnná s příkazem pro databázi
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        
        if($stmt = $pdo->prepare($sql)){
            // Proměnné
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            //  Přiřadí proměnné jako parametry pro předem připravený databázový příkaz
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
            
            // Pokus o vykonání připraveného databázového příkazu
            if($stmt->execute()){
                // Nové heslo úspěšně nastaveni. Zničí relaci a přesměruje na přihlašovací stránku
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Jejda! Vyskytla se chyba.";
            }

            // Zruší připravený příkaz ($stmt = null;)
            unset($stmt);
        }
    }
    
    // Zruší komunikaci s databází ($pdo = null;)
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="stylesheet/styyyl.css">
    <title>Změna hesla</title>
</head>
<body class="login-body">
    <div class="wrapper wrapper-login">
        <h2>Změna hesla</h2>
        <p>Vyplňte prosím formulář pro změnu vašeho hesla.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="mt-3 mb-3">
                <label>Nové heslo</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="mt-3 mb-3">
                <label>Potvrdit heslo</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="mt-3 mb-3">
                <input type="submit" class="btn btn-primary" value="Potvrdit">
                <a style="float: right;" class="btn btn-link ml-2" href="index.php">Zrušit</a>
            </div>
        </form>
    </div>
</body>
</html>