<?php

    require __DIR__ . '/vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    require("errorLib.php");

    function debugToConsole($msg) {
        echo "<script>console.log(".json_encode($msg).")</script>";
    }

function main(){


    if( $_SERVER['REQUEST_METHOD'] === 'POST' ){

        $key = "example_key";
        $jwt = $_POST["token"];

        try {
            $decoded = (array) JWT::decode($jwt, $key, array('HS256'));

            if ( time() > $decoded["exp"] ){
                echo throwBsError("<!-- error -->", "token is expired", file_get_contents("accessexam.html"));
                return;
            }

            $userData = json_decode(file_get_contents("data/loginData.json"), true);
            $examData = json_decode(file_get_contents("data/examData.json"), true);

            $userId = $decoded["student-id"];
            $exam = $decoded["exam"];

            if ( array_key_exists("$userId:$exam", $examData["admitted"]) ){
                echo "<h3>$exam exam</h3>";
                echo "<h5>userId: $userId</h5>";
                echo "student: ".$userData[$userId]["name"]." ".$userData[$userId]["surname"];
                echo "<br>";
                echo "<br>";
                echo "<a href=\"http://localhost:3000/index.html\"> go back </a>";
            }

            unset($examData["admitted"]["$userId:$exam"]);
            file_put_contents("data/examData.json", json_encode($examData));
        }
        catch (Exception $e){

            echo throwBsError("<!-- error -->", "backend error: $e", file_get_contents("accessexam.html"));
        }

    }
}

main();

?>