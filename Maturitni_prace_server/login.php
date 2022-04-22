<?php
// Initialize the session
session_start();

// Zkontrolovat, jestli již uživatel není přihlášen, pokud ano, přesměruj ho na index.php
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

// Zahrň login k databázi
require_once "controllers/config.php";

// Zjistí, jestli je uživateli povolena registrace
require "controllers/is-allowed-user-registration.php";

// Definuje proměnné a inicializuje je prázdnými proměnnými
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Zpracování údajů formuláře při odeslání formuláře
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Zkontroluje, jestli není jméno prázdné
    if(empty(trim($_POST["username"]))){
        $username_err = "Zadejte jméno uživatele";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Zkontroluje, jestli není heslo prázdné
    if(empty(trim($_POST["password"]))){
        $password_err = "Zadejte heslo.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Ověří přihlašovací údaje
    if(empty($username_err) && empty($password_err)){
        // Proměnná s příkazem pro databázi
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Proměnná
            $param_username = trim($_POST["username"]);
            
            // Přiřadí proměnné jako parametry pro předem připravený databázový příkaz
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Pokus o vykonání připraveného databázového příkazu
            if($stmt->execute()){
                // Zkontroluje, jestli uživatelské jméno existuje, pokud ano, ověří heslo
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                            // Heslo je správné, zahaj novou relaci
                            session_start();
                            
                            // Ulož data do proměnné relace
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION['expire'] = time() + (30 * 60);   // Čas ukončení relace. (čas_nyní + 30 minut), 30 * 60 sekund 
                            
                            // Přesměruj uživatele na domovskou stránku
                            header("location: index.php");
                        } else{
                            // Pokud je heslo chybné, zobraz obecnou chybovou hlášku
                            $login_err = "Chybné jméno nebo heslo.";
                        }
                    }
                } else{
                    // Pokud uživatelské jméno neexistuje, zobraz obecnou chybovou hlášku
                    $login_err = "Chybné jméno nebo heslo.";
                }
            } else{
                echo "Jejda! Někde se stala chyba. Zkuste to prosím později.";
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
    <title>Přihlásit se</title>
</head>
<body class="login-body">
    <div class="center-wrapper wrapper-login">
        <h2>Přihlásit se</h2>
        <p>Vyplňte tento formulář pro přihlášení k účtu.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mt-3 mb-3">
                <label>Jméno</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="mt-3 mb-3">
                <label>Heslo</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="mt-3 mb-3">
                <input type="submit" class="btn btn-primary" value="Přihlásit se">
            </div>
            <?php 
            if($uAllowed[0] == 1){
                echo '<p>Nemáte účet? <a href="register.php">Založit si účet</a>.</p>';
            }
            ?>
        </form>
    </div>
</body>
</html>