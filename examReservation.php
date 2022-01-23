<?php

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

        if( !isset($_POST['name']) ){
            echo throwBsError("<!-- error -->","please input name", fillExams());
            return;
        }

        if( !isset($_POST['surname']) ){
            echo throwBsError("<!-- error -->","please input surname", fillExams());
            return;
        }

        if( !isset($_POST['email']) ){
            echo throwBsError("<!-- error -->","please input email", fillExams());
            return;
        }

        if( !in_array($_POST['exam'], $examJson) ){
            echo throwBsError("<!-- error -->","exam does not exists", fillExams());
            return;
        }

        if( !valid_email($_POST['email']) ){
            echo throwBsError("<!-- error -->","invalid email address", fillExams());
            return;
        }

        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        $exam = $_POST['exam'];

        if( isset( $examsReservations["admissionReq"][$email.":".$exam] ) ){
            echo throwBsError("<!-- error -->","reservation already done", fillExams());
            return;
        }

        $addExamReservation = [ $email.":".$exam =>[ "email" => $email,  "name" => $name, "surname" => $surname, "exam" => $exam, "show" => true ]];
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