<?php

function RetrieveItems()
{
    $services = getenv("VCAP_SERVICES");
    $services_json = json_decode($services, true);
    for ($i = 0; $i < sizeof($services_json["user-provided"]); $i++){
	if ($services_json["user-provided"][$i]["name"] == "catalogAPI"){
		$catalogHost = $services_json["user-provided"][$i]["credentials"]["host"];
	}
    }
    $parsedURL = parse_url($catalogHost);
    $catalogRoute = $parsedURL["scheme"] . "://" . $parsedURL["host"];
    $url = $catalogRoute . "/items";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $curlResult = curl_exec($curl);
    $curlError = curl_error($curl);
    $curlErrno = curl_errno($curl);
    curl_close($curl);
    $firstChar = substr($curlResult, 0, 1); /* should check if $curlResult === FALSE if newer PHP */
    if ($firstChar != "{") {
        $errorObject = new stdClass();
        $errorObject->error = $curlError;
        $errorObject->errno = $curlErrno;
        return json_encode($errorObject);
    }
    return $curlResult;
}

?>
