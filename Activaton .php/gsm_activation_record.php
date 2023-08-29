<?php

if (isset($_GET['serial']) != "") 
{

$serialNumber =  $_GET['serial'];
function model($type = "")
{
    $model = "";
if($type == "meid")
{
    $model = 'devices/MEID/';
}
if($type == "gsm")
{
    $model = 'devices/GSM/';
}
return $model;
}
$devicefolder = 'devices/'.$serialNumber.'/';

if (!file_exists($devicefolder))  {
  header("404 Not Found", true, 404);
   exit;
};






$previuosResponse = file_get_contents($devicefolder.'response.xml');

$decodedrequest = new DOMDocument;
$decodedrequest->loadXML($previuosResponse);
$nodes = $decodedrequest->getElementsByTagName('dict')->item(0)->getElementsByTagName('dict')->item(1)->getElementsByTagName('*');

for ($i = 0; $i < (int)$nodes->length - 1; $i=$i+1) {
    switch ($nodes->item($i)->nodeValue ) {
        case "AccountToken": $accountTokenBase64 = $nodes->item($i + 1)->nodeValue; break;
		case "AccountTokenCertificate": $accountTokenCertificateBase64 = $nodes->item($i + 1)->nodeValue; break;
		case "AccountTokenSignature": $accountTokenSignature = $nodes->item($i + 1)->nodeValue; break;
		case "DeviceCertificate": $deviceCertificate = $nodes->item($i + 1)->nodeValue; break;
		case "FairPlayKeyData": $fairPlayKeyData = $nodes->item($i + 1)->nodeValue; break;
    }
}

//Get WilcardTicket
$accountTokenDecoded = base64_decode($accountTokenBase64);
$accountTokenDecodedTmp = explode('"WildcardTicket" = "', $accountTokenDecoded)[1];
$wildcardTicket = explode('";', $accountTokenDecodedTmp)[0];

$activationrecord = 
'<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>AccountToken</key>
	<data>'.$accountTokenBase64.'</data>
	<key>AccountTokenCertificate</key>
	<data>'.$accountTokenCertificateBase64.'</data>
	<key>AccountTokenSignature</key>
	<data>'.$accountTokenSignature.'</data>
	<key>DeviceCertificate</key>
	<data>'.$deviceCertificate.'</data>
	<key>DeviceConfigurationFlags</key>
	<string>0</string>
	<key>FairPlayKeyData</key>
	<data>'.$fairPlayKeyData.'</data>
	<key>LDActivationVersion</key>
	<integer>2</integer>
	<key>unbrick</key>
	<true/>
	<key>WildcardTicketToRemove</key>
	<data>'.$wildcardTicket.'</data>
</dict>
</plist>';

header('Content-type: application/xml');
header('Content-Length: '.strlen($activationrecord));
echo $activationrecord;

die();
}
else

	header("404 Not Found", true, 404);
    exit;