<?php
function createErrorCode($status, $message){
    return array("error" => array("code" => $status, "message" => $message));
}

exit(
    json_encode(
        createErrorCode(
            404,
            "I'm sorry the endpoint you are requesting doesn't exist.Please try sending a post request with a json object holding array of numbers called numbers to mmmr")));

?>