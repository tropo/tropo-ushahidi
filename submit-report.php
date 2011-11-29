<?php

/**
 * Tropo PHP script for submitting incident reports to an Ushahidi instance.
 * @author Mark Headd
 * @author Aaron Huslage
 * @copyright Voxeo Labs 2011
 * 
 */

// Constants used to geocode an address.
define("MAPS_LOOKUP_BASE_URL", "http://maps.googleapis.com/maps/api/geocode/json");
define("USHAHIDI_BASE_URL", "");
define("USHAHIDI_USER_NAME", "");
define("USHAHIDI_PASSWORD", "");

// Date of the incident (set at script load).
$date = getdate();

// Required incident parameters.
$params = array();
$params['task'] = "report";
$params['incident_title'] = "SMS Reported incident";
$params['incident_date'] = zerPad($date["mon"]) . "/" . $date["mday"] . "/" . $date["year"];
$params['incident_hour'] = $date["hours"];
$params['incident_minute'] = $date["minutes"];
$params['incident_ampm'] = "";
$params['incident_category'] = "SMS report";
$params['location_name'] = "SMS report";

/**
 * 
 * Utility function to zero pad dates (required by Ushahidi).
 * @param integer $date
 * @return string $date
 */
function zerPad($date) {
	if($date < 10) {
		return "0".$date;
	}
	else {
		return $date;
	}
}

/**
 * 
 * Get the lat / lon for an address
 * @param string $address
 */
function geoCodeAddress($address) {

	$address = str_replace(" ", "+", $address);
	$url = MAPS_LOOKUP_BASE_URL."?address=".$address."&sensor=false";
	return makeCurlCall($url, "GET");
}

/**
 * 
 * Submit an incident report to Ushahidi.
 * @param array $params
 */
function submitIncidentReport(Array $params) {
	return makeCurlCall(USHAHIDI_BASE_URL, "POST");
}

/**
 * 
 * Heler method to make an HTTP request.
 * @param string $url
 * @param string $method
 */
function makeCurlCall($url, $method="GET") {

	global $params;
	
	// Set up cURL call.
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	if($method == "POST") {

		$data = "";
		foreach($params as $key => $value) {
			$data .= "$key=$value&";
		}

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: multipart/form-data', 'Content-length: '.strlen($data)));
		curl_setopt($ch, CURLOPT_USERPWD, USHAHIDI_USER_NAME.":".USHAHIDI_PASSWORD);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}

	// Execute.
	$output = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	// Return results.
	if($code != '200') {
		throw new $exceptionType($exceptionMessage);
	}
	else {
		$result = $metod == "POST" ? true : $output;
		return $result;
	}

}

try {

	// Get the address submited by the user.
	$report = ask("", array("choices" => "[ANY]"));

	// Get the incient decription.
	$params['incident_description'] = $report->value;

	// Get the address of the incident (use entire message and let Google API parse it out).
	$address = $report->value;

	// Geocode address.
	$geocoded_address = json_decode(geoCodeAddress($address));
	$location = $geocoded_address->results[0]->geometry->location;
	$params['latitude'] = $location->lat;
	$params['longitude'] = $location->lng;

	if(submitIncidentReport($params)) {
		say("Thank you, your report has been submitted.");
	}
	else {
		say("Sorry, could not submit your report.");
	}

}

catch (Exception $ex) {
	_log("*** ". $ex->getMessage() . " ***");
	say("Sorry, could not submit your report.");
	hangup();
}


?>