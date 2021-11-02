<?php
/**
 * @var string $username
 * @var string $password
 * @var string $targetAccount
 */

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
require './config.php';
require 'vendor/autoload.php';
if (empty($argv[1])) {
    echo "usage: php -f zone-downloader.php zone.com\n";
    echo "error: need a zone parameter\n";
    die("execution aborted!\n");
}

// get zone from API
$body = <<<END
{
	"Transaction":{
		"Username":"$username",
		"Password":"$password",
		"TargetAccount":"$targetAccount",
		"ClientTransactionId":"not applicable"
	},
	"Zone":"$argv[1]"
}
END;

$httpClient = new Client();

$response = $httpClient->post(
    'https://service.partner4trade.de/live/domain.svc/rest/DnsZoneDetail',
    [
        RequestOptions::BODY => $body,
        RequestOptions::HEADERS => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]
    ]
);

$result_array = json_decode($response->getBody()->getContents(), true);
if ($result_array['Result']['Code'] != "15000") die ("Download of zone failed!\n");

$zone_file_array = [
    "Transaction" => [
        "Username" => "{Username}",
        "Password" => "{Password}",
        "TargetAccount" => "{TargetAccount}",
        "ClientTransactionId" => "not applicable"
    ],
    "Email" => $result_array['Email'],
    "Primary" => $result_array['Primary'],
    "Records" => $result_array['Records'],
    "Ttl" => $result_array['Ttl'],
    "Zone" => $result_array['Zone']
];

$zone_file_txt = json_encode($zone_file_array, JSON_PRETTY_PRINT);
file_put_contents('zones.d/' . $argv[1] . '.zone', $zone_file_txt);
