<?php

header('Cache-Control: max-age=2592000, public');
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
header('Content-type: application/json');


$ip = '';
if (!empty($_SERVER['REQUEST_URI'])) $ip = $_SERVER['REQUEST_URI'];
$ips = explode('/', $ip);

if (empty($ips[1])) return;


$v4 = filter_var($ips[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
$v6 = filter_var($ips[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

if ($v4 || $v6) {
	$sock = fsockopen('127.0.0.1', 6379, $errno, $errstr, 1);
	fwrite($sock, 'GET geoip:' . $ips[1] . "\r\n");
	$redis = '';
	//do { $redis .= fgets($sock); } while (substr($redis, -2) !== "\r\n");
	$redis .= fgets($sock);
	$redis = substr($redis, 0, -2);
	if ($redis === '$-1') {
		$redis = NULL;
	} else {
		$redis = fgets($sock);
		$redis = substr($redis, 0, -2);
		echo $redis;
		return;
	}
}

require_once('_geoip/geoipcity.inc');
require_once('_geoip/geoipregionvars.php');
geoip_load_shared_mem('_geoip/GeoLiteCity.dat');
$gi = geoip_open('_geoip/GeoLiteCity.dat', GEOIP_SHARED_MEMORY);

if ($v4) {
	$out = geoip_record_by_addr($gi, $ips[1]);
} else if ($v6) {
	$out = geoip_record_by_addr_v6($gi, $ips[1]);
}

if (!empty($out)) {
	if (!empty($sock)) {
		$key = 'geoip:' . $ips[1];
		$val = json_encode($out);
		$x = "*3\r\n$3\r\nSET\r\n$" . strlen($key) . "\r\n" . $key . "\r\n$" . strlen($val) . "\r\n" . $val . "\r\n";
		fwrite($sock, $x);
	}
	echo json_encode($out);
}

geoip_close($gi);
