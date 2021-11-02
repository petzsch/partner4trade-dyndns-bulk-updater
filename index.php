<?php
/**
 * @var string $username
 * @var string $password
 * @var string $targetAccount
 * @var string $allowed_user
 * @var string $allowed_pass
 */

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
require './config.php';
require 'vendor/autoload.php';

$ipv4 = file_get_contents('ipv4.txt');
$ipv6 = $_GET['ipv6'];
$ipv6prefix = substr($_GET['ipv6prefix'], 0, -5);
$req_user = $_SERVER['PHP_AUTH_USER'];
$req_pass = $_SERVER['PHP_AUTH_PW'];

if ( empty($ipv4) || empty($ipv6) || empty($ipv6prefix) || empty($req_user) || empty($req_pass) ) {
    echo "required GET parameters: ipv4, ipv6, ipv6prefix, username, password\n";
    die("execution aborted!\n");
}

//validate user and password
if ($req_user != $allowed_user || $req_pass != $allowed_pass) die("wrong credentials!");

$dir = new DirectoryIterator(dirname(__FILE__) . '/zones.d/');
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        echo $fileinfo->getFilename() . ": ";

        // read zone file to string
        $zone_file_json = file_get_contents(dirname(__FILE__) . '/zones.d/' . $fileinfo->getFilename());

        // replace placeholders with variable content
        $zone_file_filled = str_replace(['{ipv4}', '{ipv6}', '{ipv6prefix}', '{Username}', '{Password}',
            '{TargetAccount}'], [$ipv4, $ipv6, $ipv6prefix, $username, $password, $targetAccount], $zone_file_json);

        // send the zone file via post to the API
        $httpClient = new Client();

        try {
            $response = $httpClient->post(
                'https://service.partner4trade.de/live/domain.svc/rest/DnsZoneUpdate',
                [
                    RequestOptions::BODY => $zone_file_filled,
                    RequestOptions::HEADERS => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ]
                ]
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            die($e->getMessage());
        }

        $result_array = json_decode($response->getBody()->getContents(),true);
        echo $result_array['Result']['Code'] . "\n";

    }
}
