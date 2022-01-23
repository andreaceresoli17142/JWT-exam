<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 216000,
    ]);
}

require("errorLib.php");

function loginFromSession(){

    if ( isset($_SESSION["userId"]) && isset($_SESSION["pw"]) ){
        $mlCNT = $_SESSION["userId"];
        $pwCNT = $_SESSION["pw"];

        if ( $_SESSION["expt"] + 216000 < time() ){
            include 'signOut.php';
            return true;
        }

        if( isset($_POST['userId']) ){
            if ($_POST['userId'] != $mlCNT){
                return true;
            }
        }
        $strLogins = file_get_contents("data/loginData.json");
        $loginData = json_decode($strLogins, true);
        if( $loginData[$mlCNT]["pw"]["hash"] == $pwCNT){
            if ( $loginData[$mlCNT]["accountType"] == "tc" ){
                include 'privateArea.php';
            } else {
                // include 'examReservation.php';
                header("Location: http://localhost:3000/examReservation.php");
            }
            return false;
        }
    }
    return true;
}

function main(){
    if( $_SERVER['REQUEST_METHOD'] === 'POST' ){

        if( !isset($_POST['userId']) ){
            echo throwBsError("<!-- error -->","please input teacher id", file_get_contents("logInPage.html"));
            return;
        }

        if( !isset($_POST['pw']) ){
            echo throwBsError("<!-- error -->","please input password", file_get_contents("logInPage.html"));
            return;
        }

        $userId = $_POST['userId'];

        $strLogins = file_get_contents("data/loginData.json");
        $loginData = json_decode($strLogins, true);

        if ( !array_key_exists($userId, $loginData) ){
            echo throwBsError("<!-- error -->","account with this teacher id does not exists", file_get_contents("logInPage.html"));
            return;
        }

        $pw = $loginData[$userId]["pw"]["hash"];

        if( $pw == hash( "sha256", $loginData[$userId]["pw"]["salt"].$_POST['pw'], false)){
            $_SESSION["userId"] = $userId;
            $_SESSION["pw"] = $pw;
            $_SESSION["expt"] = time();
            if ( $loginData[$userId]["accountType"] == "tc" ){
                include 'privateArea.php';
            } else {
                // include 'examReservation.php';
                header("Location: http://localhost:3000/examReservation.php");
            }
            // echo "hello";
        }
        else{
            // echo "is this";
            echo throwBsError( "<!-- error -->", "wrong id or password",file_get_contents("loginPage.html"));
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