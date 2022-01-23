<?php

    require("errorLib.php");

    function debugToConsole($msg) {
        echo "<script>console.log(".json_encode($msg).")</script>";
    }

function main(){

    $examsReservations = json_decode(file_get_contents("data/examData.json"), true);

    if( $_SERVER['REQUEST_METHOD'] === 'GET' ){

        $email = $_GET['email'];
        $exam = $_GET['exam'];

        $arrayKey = $email.":".$exam;

        $examsReservations["admissionReq"][$arrayKey]["show"] = false;
        file_put_contents("data/examData.json", json_encode($examsReservations));
        header("Location: http://localhost:3000/logIn.php");

    }
}

main();

?>