<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 216000,
    ]);
}

    require("errorLib.php");

    function debugToConsole($msg) {
        echo "<script>console.log(".json_encode($msg).")</script>";
    }

    $subjects = json_decode(file_get_contents("data/loginData.json"), true)[$_SESSION["teacherId"]]["subj"];
    // var_dump($subjects);

    $examsReservations = json_decode(file_get_contents("data/examData.json"), true);

    $ret = file_get_contents("privateArea.html");

    $admissionReq = "";

    foreach( $examsReservations["admissionReq"] as $singleReq ){

        if ( in_array(preg_split ("/\-/", $singleReq["exam"])[0], $subjects)  && $singleReq["show"] == true ){

            $admissionReq .= " ".$singleReq["name"]." ".$singleReq["surname"]." ".$singleReq["exam"]." <a href=\"http://localhost:3000/admit.php?email=".urlencode($singleReq["email"])."&exam=".urlencode($singleReq["exam"])."\">admit</a> <a href=\"http://localhost:3000/refute.php?email=".urlencode($singleReq["email"])."&exam=".urlencode($singleReq["exam"])."\">refute</a>  <br>";
        }
    }

    $ret = str_replace( "<!-- exam reservation requests -->", $admissionReq, $ret );

    $admitted = "";

    foreach( $examsReservations["admitted"] as $singleReq ){

        if ( in_array(preg_split ("/\-/", $singleReq["exam"])[0], $subjects) ){

            $admitted .= " ".$singleReq["name"]." ".$singleReq["surname"]." ".$singleReq["exam"]." <br>";
        }
    }

    if ( isset($_GET["error"]) ){

        $ret = throwBsError( "<!-- error -->", $_GET["error"], $ret );
    }

    $ret = str_replace( "<!-- admitted -->", $admitted, $ret );

    echo $ret;
    // echo $ret;

?>