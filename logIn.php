<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 216000,
    ]);
}

//require("errorLib.php");

function loginFromSession(){

    if ( isset($_SESSION["teacherId"]) && isset($_SESSION["pw"]) ){
        $mlCNT = $_SESSION["teacherId"];
        $pwCNT = $_SESSION["pw"];

        if ( $_SESSION["expt"] + 216000 < time() ){
            include 'signOut.php';
            return true;
        }

        if( isset($_POST['teacherId']) ){
            if ($_POST['teacherId'] != $mlCNT){
                return true;
            }
        }
        $strLogins = file_get_contents("data/loginData.json");
        $loginData = json_decode($strLogins, true);
        if( $loginData[$mlCNT]["pw"]["hash"] == $pwCNT){
            include 'privateArea.php';
            return false;
        }
    }
    return true;
}

function main(){
    if( $_SERVER['REQUEST_METHOD'] === 'POST' ){

        if( !isset($_POST['teacherId']) ){
            echo str_replace("<!-- error -->","<div class=\"alert alert-danger\" role=\"alert\">please input teacher id </div>", file_get_contents("logInPage.html"));
            return;
        }

        if( !isset($_POST['pw']) ){
            echo str_replace("<!-- error -->","<div class=\"alert alert-danger\" role=\"alert\">please input password </div>", file_get_contents("logInPage.html"));
            return;
        }

        $teacherId = $_POST['teacherId'];

        $strLogins = file_get_contents("data/loginData.json");
        $loginData = json_decode($strLogins, true);

        if ( !array_key_exists($teacherId, $loginData) ){
            echo str_replace("<!-- error -->","<div class=\"alert alert-danger\" role=\"alert\">account with this teacher id does not exists </div>", file_get_contents("logInPage.html"));
            return;
        }

        $pw = $loginData[$teacherId]["pw"]["hash"];

        if( $pw == hash( "sha256", $loginData[$teacherId]["pw"]["salt"].$_POST['pw'], false)){
            $_SESSION["teacherId"] = $teacherId;
            $_SESSION["pw"] = $pw;
            $_SESSION["expt"] = time();
            include 'privateArea.php';
            // header("Location: http://localhost:3000/privateArea.html");
            // echo "hello";
        }
        else{
            // echo "is this";
            echo str_replace( "<!-- error -->", "<div class=\"alert alert-danger\" role=\"alert\">wrong id or password</div>",file_get_contents("loginPage.html"));
        }
        return;
    }
    // var_dump($_SESSION);
    // echo "working";
    header("Location: http://localhost:3000/logInPage.html");
}

if ( loginFromSession() ){

    main();
}

?>