<?php


function getMean ($array){
    return round((array_sum($array)/ count($array)), 3, PHP_ROUND_HALF_UP);
}

function getMedian ($array){
    sort($array);
    if(count($array)%2 == 0){
        $index1 = (count($array)/2)-1;
        $index2 = $index1+1;
        return round(($array[$index1] + $array[$index2])/2, 3, PHP_ROUND_HALF_UP);
    }
    else{
        $index = (ceil(count($array)/2))-1;
        return round($array[$index],3,PHP_ROUND_HALF_UP);
    }
}

/*
 * The values are converted to strings as the php array does not allow floats as keys, as such
 * the values had to be converted to get he counts.
 */
function getMode ($array){
    $associativeArray = [];
    foreach($array as $value){
        $valueString = strval($value);
        if(array_key_exists($valueString, $associativeArray)){
            $associativeArray[$valueString] = $associativeArray[$valueString]+1;
        }
        else{
            $associativeArray[$valueString] = 1;
        }
    }
    //If all values only have 1 count there is no mode
    if(max($associativeArray) == 1){
        return "NULL";
    }
    //Returning the array of keys because if multiple values have the same max than the set is multimodal.
    return array_keys($associativeArray, max($associativeArray), true);
}

function getRange ($array){
    return round(max($array)-min($array), 3, PHP_ROUND_HALF_UP);
}


function createErrorCode($status, $message){
    return array("error" => array("code" => $status, "message" => $message));
}

function cleanInput($data){
    return str_replace("\n", "", $data);
}

function buildResponse($data){

    try{
        $associativeArray = json_decode(cleanInput($data), true);
        if($associativeArray == null){
            http_response_code(500);
            return createErrorCode(500, "Improperly formatted JSON (didn't parse)");
        }
        if(!array_key_exists("numbers",$associativeArray)){
            http_response_code(500);
            return createErrorCode(500, "Improperly formatted JSON (Does not contain \"numbers\" item)");
        }
        $numbers = $associativeArray["numbers"];
        $responseData = array("results" => array( "mean" => getMean($numbers),
            "median" => getMedian($numbers),
            "mode" => getMode($numbers),
            "range" => getRange($numbers)));
        http_response_code(200);
        return $responseData;
    }
    catch(Exception $e){
        http_response_code(500);
        return createErrorCode(500, "Improperly formatted JSON (didn't parse)");
    }

}

switch ($_SERVER['REQUEST_METHOD'])
{
    case "GET":
        http_response_code(404);
        $data = createErrorCode(404,"Method GET not available on this endpoint");
        break;
    case "POST":

        $data = buildResponse($HTTP_RAW_POST_DATA);
        break;
    case "PUT":
        http_response_code(404);
        $data = createErrorCode(404,"Method PUT not available on this endpoint");
        break;
    case "DELETE":
        http_response_code(404);
        $data = createErrorCode(404,"Method DELETE not available on this endpoint");
        break;
    default:
        http_response_code(404);
        $data = createErrorCode(404,"Only POST available at this endpoint");
        break;

}

//Return JSON response
exit(json_encode($data));
?>