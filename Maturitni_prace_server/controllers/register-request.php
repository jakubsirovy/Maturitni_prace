<?php
// Definuje proměnné a inicializuje je prázdnými proměnnými
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Zpracování údajů formuláře při odeslání formuláře
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Ověření uživatelského jména
    if(empty(trim($_POST["username"]))){
        $username_err = "Zadejte uživatelské jméno.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Uživatelské jméno smí obsahovat pouze písmena, číslice a podtržítka.";
    } else{
        // Proměnná s příkazem pro databázi
        $sql = "SELECT id FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Proměnná
            $param_username = trim($_POST["username"]);
            
            // Přiřadí proměnné jako parametry pro předem připravený databázový příkaz
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Pokus o vykonání připraveného databázového příkazu
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Toto uživatelské jméno je zabrané. Zkuste prosím jiné.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Jejda! Někde se stala chyba. Zkuste to prosím později.";
            }

            // Zruší připravený příkaz ($stmt = null;)
            unset($stmt);
        }
    }
    
    // Ověření hesla
    if(empty(trim($_POST["password"]))){
        $password_err = "Zadejte prosím vaše heslo.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Heslo musí obsahovat nejméně 6 znaků.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Ověření potvrzení hesla
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Potvrďte vaše heslo.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Hesla se neshodují.";
        }
    }
    
    // Zkontrolovat chyby před vložením do databáze
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Proměnná s příkazem pro databázi
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
         
        if($stmt = $pdo->prepare($sql)){
            // Proměnné
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Vytvoří hash hesla
            
            // Přiřadí proměnné jako parametry pro předem připravený databázový příkaz
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            // Pokus o vykonání připraveného databázového příkazu
            if($stmt->execute()){
                // Přesměruj na stránku s přihlášením
                header("location: login.php");
            } else{
                echo "Jejda! Někde se stala chyba. Zkuste to prosím později.";
            }

            // Zruší připravený příkaz ($stmt = null;)
            unset($stmt);
        }
    }
    
    // // Zruší komunikaci s databází ($pdo = null;)
    unset($pdo);
}
?>