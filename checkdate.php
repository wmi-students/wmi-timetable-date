<?php
$jsonFile = url_get_contents('http://wmitimetable.herokuapp.com/schedules.json');
$json = json_decode($jsonFile, true);
$schedulesFile = file_get_contents('schedules.json');
$schedules = json_decode($schedulesFile, true);

$result = array_recursive_diff($json, $schedules);
$date = new DateTime();
$date = $date->format("Y-m-d G:i:s");

if(empty($result)) {
	echo 'No changes';
	if ($lastChecked = fopen('lastChecked.date', 'w')) {
		fwrite($lastChecked, $date);
		fwrite($lastChecked, PHP_EOL);
	}
} else {
	if ($dateFile = fopen('schedules.date', 'w')) {
		fwrite($dateFile, $date);
	}
	if ($newJsonFile = fopen('schedules.json', 'w')) {
		fwrite($newJsonFile, $jsonFile);
		fwrite($newJsonFile, PHP_EOL);
	}
	echo 'Updating date '.$date;
}

function url_get_contents ($url) {
	if (!function_exists('curl_init')){
		die('CURL is not installed!');
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function array_recursive_diff($array1, $array2) {
    $aReturn = array();

    foreach ($array1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $array2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = array_recursive_diff($mValue, $array2[$mKey]);
                if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
            } else {
                if ($mValue != $array2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                }
            }
        } else {
            $aReturn[$mKey] = $mValue;
        }
    }
    return $aReturn;
}
?>
