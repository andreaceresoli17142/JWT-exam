<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 216000,
    ]);
}

require("errorLib.php");

function valid_email($str) {
    return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}

function fillExams(){

    $examJson = json_decode(file_get_contents("data/examsList.json"), true)["exams"];
    $examArr = "";
    // var_dump($examJson);
    foreach( $examJson as $exam ){
        $examArr .= "<option value=\"$exam\">$exam</option>";
    }
    return str_replace( "<!-- exams -->", $examArr, file_get_contents("examReservation.html"));
}

function main(){

    $examJson = json_decode(file_get_contents("data/examsList.json"), true)["exams"];

    if( $_SERVER['REQUEST_METHOD'] === 'POST' ){

        $examsReservations = json_decode(file_get_contents("data/examData.json"), true);

        $userId = $_SESSION['userId'];
        $exam = $_POST['exam'];
        // echo $userId;

        if( !in_array($_POST['exam'], $examJson) ){
            echo throwBsError("<!-- error -->","exam does not exists", fillExams());
            return;
        }

        if( isset( $examsReservations["admissionReq"][$userId.":".$exam] ) ){
            echo throwBsError("<!-- error -->","reservation already done", fillExams());
            return;
        }

        $addExamReservation = [ $userId.":".$exam =>[ "userId" => $userId, "exam" => $exam, "show" => true ]];
        $examsReservations["admissionReq"] += $addExamReservation;
        file_put_contents("data/examData.json", json_encode($examsReservations));
    }

    $examArr = "";

    foreach( $examJson as $exam ){
        $examArr .= "<option value=\"$exam\">$exam</option>";
    }
    echo str_replace( "<!-- exams -->", $examArr, file_get_contents("examReservation.html"));
}
    main();
?>