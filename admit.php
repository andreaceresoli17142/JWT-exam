<?php

    require __DIR__ . '/vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    require("errorLib.php");

    function debugToConsole($msg) {
        echo "<script>console.log(".json_encode($msg).")</script>";
    }

function main(){

    $examsReservations = json_decode(file_get_contents("data/examData.json"), true);

    if( $_SERVER['REQUEST_METHOD'] === 'GET' ){

        $userId = $_GET['userId'];
        $exam = $_GET['exam'];

        $arrayKey = $userId.":".$exam;
        // echo $arrayKey;

        $examReq = $examsReservations["admissionReq"][$arrayKey];
        unset($examsReservations["admissionReq"][$arrayKey]);

        $examsReservations["admitted"] += [$arrayKey=>[ "userId" => $userId, "exam" => $examReq["exam"] ]];

        try{
            $userData = json_decode(file_get_contents("data/loginData.json"), true);
            //throw new Exception("testing exeptions");
            //TODO: put information in a JWT and send it in the email

            $key = "example_key";
            $payload = array(
                "iss" => "http://example.org",
                "aud" => "http://example.com",
                "student-id" => $userId,
                "exam" => $exam,
                "exp" => time()+600,
                "iat" => time()
            );

            /**
             * IMPORTANT:
             * You must specify supported algorithms for your application. See
             * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
             * for a list of spec-compliant algorithms.
             */
            $JWToken = JWT::encode($payload, $key, 'HS256');
            // $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            echo $JWToken;

            // print_r($decoded);

            // $to      = $userData[$userId]["email"];
            // $subject = "$exam exam admission request";
            // $message = "you have been admitted to the exam.\nuse this token to access the exam:\n$JWToken";
            // $headers = 'From: examManager@gimelli.com' . "\r\n" .
            // 'Reply-To: examManager@gimelli.com' . "\r\n" .
            // 'X-Mailer: PHP/' . phpversion();

            // mail($to, $subject, $message, $headers);

            file_put_contents("data/examData.json", json_encode($examsReservations));
            // header("Location: http://localhost:3000/logIn.php");
        }catch(Exception $ex){
            header("Location: http://localhost:3000/privateArea.php?error=".urlencode("backend error: $ex"));
        }

    }
}

main();

?>