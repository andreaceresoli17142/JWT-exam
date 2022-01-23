<?php

require("errorLib.php");

function main(){
    if( $_SERVER['REQUEST_METHOD'] === 'POST'){

        if( !isset($_POST['usr']) ){
            echo str_replace("<!-- error -->","<div class=\"alert alert-danger\" role=\"alert\">please input username </div>", file_get_contents("signInPage.html"));
            return;
        }

        if( !isset($_POST['email']) ){
            echo str_replace("<!-- error -->","<div class=\"alert alert-danger\" role=\"alert\">please input email </div>", file_get_contents("signInPage.html"));
            return;
        }

        if( !isset($_POST['pw']) ){
            echo str_replace("<!-- error -->","<div class=\"alert alert-danger\" role=\"alert\">please input password </div>", file_get_contents("signInPage.html"));
            return;
        }

        $user = $_POST['usr'];
        $email = $_POST['email'];

        $strLogins = file_get_contents("data/loginData.json");
        $loginData = json_decode($strLogins, true);

        if ( array_key_exists($email, $loginData) ){
            echo str_replace("<!-- error -->","<div class=\"alert alert-danger\" role=\"alert\">email is already in use </div>", file_get_contents("signInPage.html"));
            return;
        }

        foreach ($loginData as $singleLogin) {
            if ( $singleLogin['usr'] == $user ){
                echo str_replace("<!-- error -->","<div class=\"alert alert-danger\" role=\"alert\">username is already in use </div>", file_get_contents("signInPage.html"));
                return;
            }
        }

        $newUsr = [];
        $salt = rand(0, 999999);
        $usrJsonArr = [ $email =>[ "usr" => $user, "salt" => $salt, "pw" => hash( "sha256", $salt.$_POST['pw'], false)]];

        $loginData += $usrJsonArr;
        file_put_contents("data/loginData.json", json_encode($loginData));
        header("Location: http://localhost:3000/login-index.html");
    }
    echo file_get_contents("signInPage.html");
}

main();
?>