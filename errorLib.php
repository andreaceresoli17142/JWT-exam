<?php
function throwError($errorNumber, $errNameArr, $errDescArr){
    $test = [];
    if (count($errNameArr) != 1){
        $i = 0;
        foreach ($errNameArr as $value) {
            $test += [$value => $errDescArr[$i]];
            $i++;
        }
    }
    else{
        $test += [$errNameArr => $errDescArr];
    }
    $errorObj = json_decode(json_encode($test),false);
    http_response_code($errorNumber);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($errorObj);
}

function throwBsError( $StrToReplace, $message, $sourceString ){
    return str_replace( $StrToReplace, "<div class=\"alert alert-danger\" role=\"alert\">$message</div>", $sourceString );
}
?>