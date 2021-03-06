<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 216000,
    ]);
}

    //require("errorLib.php");

    function debugToConsole($msg) {
        echo "<script>console.log(".json_encode($msg).")</script>";
    }
    $loginData = json_decode(file_get_contents("data/loginData.json"), true);

    $subjects = $loginData[$_SESSION["userId"]]["subj"];
    // var_dump($subjects);

    $examsReservations = json_decode(file_get_contents("data/examData.json"), true);

    $ret = file_get_contents("privateArea.html");

    $admissionReq = "";

    foreach( $examsReservations["admissionReq"] as $singleReq ){

        if ( in_array(preg_split ("/\-/", $singleReq["exam"])[0], $subjects)  && $singleReq["show"] == true ){

            $admissionReq .= " ".$loginData[$singleReq['userId']]["name"]." ".$loginData[$singleReq['userId']]["surname"]." ".$singleReq["exam"]." <a href=\"http://localhost:3000/admit.php?userId=".urlencode($singleReq['userId'])."&exam=".urlencode($singleReq["exam"])."\" target=\"_new\">admit</a> <a href=\"http://localhost:3000/refute.php?userId=".urlencode($singleReq['userId'])."&exam=".urlencode($singleReq["exam"])."\">refute</a>  <br>";
        }
    }

    $ret = str_replace( "<!-- exam reservation requests -->", $admissionReq, $ret );

    $admitted = "";

    foreach( $examsReservations["admitted"] as $singleReq ){

        if ( in_array(preg_split ("/\-/", $singleReq["exam"])[0], $subjects) ){

            $admitted .= " ".$loginData[$singleReq['userId']]["name"]." ".$loginData[$singleReq['userId']]["surname"]." ".$singleReq["exam"]." <br>";
        }
    }

    if ( isset($_GET["error"]) ){

        $ret = throwBsError( "<!-- error -->", $_GET["error"], $ret );
    }

    $ret = str_replace( "<!-- admitted -->", $admitted, $ret );

    echo $ret;
    // echo $ret;

?>