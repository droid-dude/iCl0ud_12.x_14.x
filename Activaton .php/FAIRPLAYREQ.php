<?php



$activation=$_POST['activation-info'];




function albert_request($query)
{

  $url = 'https://albert.apple.com/deviceservices/deviceActivation';
  $data_info=urlencode($query);
  $post_data ="activation-info=".$data_info;
  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL , $url ); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
  curl_setopt($ch, CURLOPT_TIMEOUT , 60); 
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: albert.apple.com", "Content-Type: application/x-www-form-urlencoded", "Connection: keep-alive", "Accept: */*", "Accept-Language: en-US", "Content-Length: ".strlen($post_data)));
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_USERAGENT , "iOS Device Activator (MobileActivation-353.200.48)" );
  curl_setopt($ch, CURLOPT_POST , 1); 
  curl_setopt($ch, CURLOPT_POSTFIELDS , $post_data );  

  $xml_response = curl_exec($ch); 
   
  if (curl_errno($ch)) 
  { 
    $error_message = curl_error($ch); 
    $error_no = curl_errno($ch);
    
    echo "error_message: " . $error_message . "<br>";
    echo "error_no: " . $error_no . "<br>";
  }
  curl_close($ch);
  return $xml_response;
}

$activationRamdomess="";
$uniqueDiviceID="";
$deviceType="";
$ProductType="";
$Serverftp="";
$baseSerial="St0rms1G";
$IP=$_SERVER['REMOTE_ADDR'];
file_put_contents('LASTLOGIP'.$IP.'.LOG', $IP);
if($IP == '68.65.121.182'){
    exit('404');
}
$encodedrequest = new DOMDocument;
$encodedrequest->loadXML($activation);
$activationToDecode= base64_decode($encodedrequest->getElementsByTagName('data')->item(0)->nodeValue);

$decodedrequest = new DOMDocument;
$decodedrequest->loadXML($activationToDecode);
$nodes = $decodedrequest->getElementsByTagName('dict')->item(0)->getElementsByTagName('*');

for ($i = 0; $i < $nodes->length - 1; $i=$i+2)
{
	${$nodes->item($i)->nodeValue} = preg_match('/(true|false)/', $nodes->item($i + 1)->nodeName) ? $nodes->item($i + 1)->nodeName : $nodes->item($i + 1)->nodeValue;
	switch ($nodes->item($i)->nodeValue) {
	case "ActivationRandomness": $activationRandomness = $nodes->item($i + 1)->nodeValue; break;
	case "DeviceClass": $deviceType = $nodes->item($i + 1)->nodeValue; break;
	case "DeviceCertRequest": $deviceCertRequest = $nodes->item($i + 1)->nodeValue; break;
	case "SerialNumber": $Number = $nodes->item($i + 1)->nodeValue; break;
	case "UniqueDeviceID": $uniqueDiviceID = $nodes->item($i + 1)->nodeValue; break;
	case "UniqueChipID": $ucid = $nodes->item($i + 1)->nodeValue; break;
	case "ProductType": $ProductType = $nodes->item($i + 1)->nodeValue; break;
	case "DeviceVariant": $DeviceVariant = $nodes->item($i + 1)->nodeValue; break;
	case "ProductVersion": $ProductVersion = $nodes->item($i + 1)->nodeValue; break;
	case "BasebandChipID": $BCID = $nodes->item($i + 1)->nodeValue; break;
	case "BasebandMasterKeyHash": $BMKH = $nodes->item($i + 1)->nodeValue; break;
	case "BasebandSerialNumber": $BasebandSerialNumber = $nodes->item($i + 1)->nodeValue; break;
	case "MobileEquipmentIdentifier":$meid= $nodes->item($i + 1)->nodeValue; break;
	case "BuildVersion": $BuildVersion = $nodes->item($i + 1)->nodeValue; break;
	case "RegionCode": $RegionCode = $nodes->item($i + 1)->nodeValue; break;
	case "RegionInfo": $RegionInfo = $nodes->item($i + 1)->nodeValue; break;
	case "RegulatoryModelNumber": $RegulatoryModelNumber = $nodes->item($i + 1)->nodeValue; break;
	case "kCTPostponementInfoPRIVersion": $kCTPostponementInfoPRIVersion = $nodes->item($i + 1)->nodeValue; break;
	case "kCTPostponementInfoPRLName": $kCTPostponementInfoPRLName = $nodes->item($i + 1)->nodeValue; break;
	case "InternationalMobileSubscriberIdentity": $imsi = $nodes->item($i + 1)->nodeValue; break;
	case "IntegratedCircuitCardIdentity": $iccid = $nodes->item($i + 1)->nodeValue; break;
	case "ModelNumber": $ModelNumber = $nodes->item($i + 1)->nodeValue; break;
	case "OSType": $OSType = $nodes->item($i + 1)->nodeValue; break;
	}
}
$test=$ProductType;
$uniqueDeviceID=$uniqueDiviceID;
$DeviceClass=$deviceType;
// ------- SAUVEGARDE ACTIVATIONINFO EDIT --------------------------

$signature=base64_encode($Number.';'.$deviceType);
$devicefolder = 'devices/'.$Number.'/';

if (!file_exists('devices/')) mkdir('devices/', 0777, true);

if (!file_exists($devicefolder))  mkdir($devicefolder, 0777, true);

$encodedrequest->save($devicefolder . $uniqueDiviceID.'.html');

$folder = 'devices/';
if($meid > 0)
{
    $deviceMasterfolder = "$folder/$Number/";
}
else
{
    $deviceMasterfolder = "$folder/$Number/";
}

if (!file_exists($deviceMasterfolder))  mkdir($deviceMasterfolder, 0777, true);
$fingerPrint = base64_encode($uniqueDiviceID.";".$ucid.";".$Number.";".$ProductType);

if (!file_exists($deviceMasterfolder."DeviceFingerPrint.log")) {
	file_put_contents($deviceMasterfolder."DeviceFingerPrint.log", $fingerPrint);
} else {
	$storedFinger = file_get_contents($deviceMasterfolder."DeviceFingerPrint.log");
	if($storedFinger != $fingerPrint){ 
		spoofRecorder($deviceMasterfolder, $Number);
	}
}

// GENERATE ACTIVATIONINFO WITH TEMPLATE AND UPLOAD SSH
$ActivationInfoXML = 
'<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>ActivationRequestInfo</key>
	<dict>
		<key>ActivationRandomness</key>
		<string>'.$activationRandomness.'</string>
		<key>ActivationState</key>
		<string>Unactivated</string>
		<key>FMiPAccountExists</key>
		<true/>
	</dict>
	<key>BasebandRequestInfo</key>
	<dict>
		<key>ActivationRequiresActivationTicket</key>
		<true/>
		<key>BasebandActivationTicketVersion</key>
		<string>V2</string>
		<key>BasebandChipID</key>
		<integer>7282913</integer>
		<key>BasebandMasterKeyHash</key>
		<string>AEA5CCE143668D0EFB4CE1F2C94C966A6496C6AA</string>
		<key>BasebandSerialNumber</key>
		<data>
		NE5Ksw==
		</data>
		<key>InternationalMobileEquipmentIdentity</key>
		<string>358832055421543</string>
		<key>SIMStatus</key>
		<string>kCTSIMSupportSIMStatusNotInserted</string>
		<key>SupportsPostponement</key>
		<true/>
		<key>kCTPostponementInfoPRIVersion</key>
		<string>0.1.141</string>
		<key>kCTPostponementInfoPRLName</key>
		<integer>0</integer>
		<key>kCTPostponementInfoServiceProvisioningState</key>
		<false/>
	</dict>
	<key>DeviceCertRequest</key>
	<data>
	'.$deviceCertRequest.'
	</data>
	<key>DeviceID</key>
	<dict>
		<key>SerialNumber</key>
		<string>FCHP606DG07R</string>
		<key>UniqueDeviceID</key>
		<string>'.$uniqueDeviceID.'</string>
	</dict>
	<key>DeviceInfo</key>
	<dict>
		<key>BuildVersion</key>
		<string>'.$BuildVersion.'</string>
		<key>DeviceClass</key>
		<string>'.$DeviceClass.'</string>
		<key>DeviceVariant</key>
		<string>'.$DeviceVariant.'</string>
		<key>ModelNumber</key>
		<string>'.$ModelNumber.'</string>
		<key>OSType</key>
		<string>'.$OSType.'</string>
		<key>ProductType</key>
		<string>'.$ProductType.'</string>
		<key>ProductVersion</key>
		<string>'.$ProductVersion.'</string>
		<key>RegionCode</key>
		<string>IP</string>
		<key>RegionInfo</key>
		<string>IP/A</string>
		<key>RegulatoryModelNumber</key>
		<string>'.$RegulatoryModelNumber.'</string>
		<key>UniqueChipID</key>
		<integer>'.$ucid.'</integer>
	</dict>
	<key>RegulatoryImages</key>
	<dict>
		<key>DeviceVariant</key>
		<string>'.$DeviceVariant.'</string>
	</dict>
	<key>UIKCertification</key>
	<dict>
		<key>BluetoothAddress</key>
		<string>bc:4c:c4:14:58:ac</string>
		<key>BoardId</key>
		<integer>14</integer>
		<key>ChipID</key>
		<integer>35152</integer>
		<key>EthernetMacAddress</key>
		<string>bc:4c:c4:14:58:ad</string>
		<key>UIKCertification</key>
		<data>
		MIICxjCCAm0CAQEwADCB2QIBATAKBggqhkjOPQQDAgNHADBEAiBOmykQ378M
		lvcKVkyjlHoYwKN8/WK/lHGv2zscJxnE+AIgN9zrZRpE0K7RZuZtruXkgFxV
		iM4SXByiyOPFmBdcy+MwWzAVBgcqhkjOPQIBoAoGCCqGSM49AwEHA0IABFNT
		gwNJnJnk05h2j2K9p75U96PvOBiti2J0nQNXeKWGKizCqergjKtHZqAtVBsX
		mdd3311pxQ75CsX3EUaznAagCgQIYWNzc0gAAACiFgQUfYSpUwwmRfMbGkRA
		Ps1aKCLT0dwwgcICAQEwCgYIKoZIzj0EAwIDSAAwRQIhAPDzRlZqRnm9wRmT
		1oIy5sh/AbDHSQVmitgH9NoCpoctAiB9+1hOM8Zeb1htQV8s81Xg0aou/86P
		PveOu9TIzYQNnDBbMBUGByqGSM49AgGgCgYIKoZIzj0DAQcDQgAESTAiT/2L
		1L1+0JBiUSGPumizG+wQp12JUM0T80UqWbvEE9ljAk676/zhKQBjl38/Sn06
		yO2EABYoYBIlgEi0ZKAKBAggc2tzAgAAAKCBxDCBwQIBATAKBggqhkjOPQQD
		AgNHADBEAiAtvdWemPKvE6kfMpY9pUYuvJcXbznA/oVLeEXPbzXtTgIgCBJP
		dGxZs0OZLgdfNAwJuxa+1dqcFgV1LDen2Gi9eM8wWzAVBgcqhkjOPQIBoAoG
		CCqGSM49AwEHA0IABEkwIk/9i9S9ftCQYlEhj7posxvsEKddiVDNE/NFKlm7
		xBPZYwJOu+v84SkAY5d/P0p9OsjthAAWKGASJYBItGSgCgQIIHNrcwIAAAAw
		CgYIKoZIzj0EAwIDRwAwRAIgHU83XIiKQrKl0aoXCB+yJ5i05MQBRZ52f0zt
		yzsI34MCIF6QRIRaUsTcts4Q6f9Z/ME2fo8rEM34I6/KaMcD7+6q
		</data>
		<key>WifiAddress</key>
		<string>bc:4c:c4:14:58:ab</string>
	</dict>
</dict>
</plist>';

/*/echo $activationRandomness.'/'.$BMKH.'/'.$BasebandSerialNumber.'/';
file_put_contents($devicefolder.'ActivationInfoXML-UPDATE-decoded.html',$template);
$updatemplate=base64_encode(file_get_contents($devicefolder.'ActivationInfoXML-UPDATE-decoded.html'));
$plistgen='<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>TryVaskeXML</key>
	<data>
		'.$updatemplate.'
	</data>
</dict>
</plist>
';
file_put_contents($devicefolder.$uniqueDiviceID.'-exon.plist', $plistgen);

// ===========================================

$ServerftpiPhone = new Net_SFTP(ssh_ip);
if (!$ServerftpiPhone->login('root', 'alpine')) {
    exit('03:Fail');
    echo "NOTICE - SSH TROUBLE";
}
$output = $ServerftpiPhone->put('/vaskeovergame/vaske.plist', $devicefolder.$uniqueDiviceID.'-exon.plist', 1);
//$output = $ServerftpiPhone->put('/vaskeovergame/'.$uniqueDiviceID.'.plist', $devicefolder.$uniqueDiviceID.'-exon.plist', 1);
$reload=$ServerftpiPhone->exec('killall -7 mobileactivationd');
sleep(1);

execInBackground('ideviceactivation activate -u 580d806adfe14d60ec6108d594da29dfd72fde6c -d -s http://nam1.club/donor/xml.php?mod='.$signature);
if(file_exists($devicefolder.'XMLTOPARSE.html')){
	unlink($devicefolder.'XMLTOPARSE.html');
}
$i = 0;
while ($i < 1) {
    if(file_exists($devicefolder.'XMLTOPARSE.html')){
    	$request_ticket=file_get_contents($devicefolder.'XMLTOPARSE.html');
    	$i++;
    }  
}*/
$ActivationInfoXML64 = base64_encode($ActivationInfoXML);

$private = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQC3BKrLPIBabhpr+4SvuQHnbF0ssqRIQ67/1bTfArVuUF6p9sdcv70N+r8yFxesDmpTmKitLP06szKNAO1k5JVk9/P1ejz08BMe9eAb4juAhVWdfAIyaJ7sGFjeSL015mAvrxTFcOM10F/qSlARBiccxHjPXtuWVr0fLGrhM+/AMQIDAQABAoGACGW3bHHPNdb9cVzt/p4Pf03SjJ15ujMY0XY9wUm/h1s6rLO8+/10MDMEGMlEdcmHiWRkwOVijRHxzNRxEAMI87AruofhjddbNVLt6ppW2nLCK7cEDQJFahTW9GQFzpVRQXXfxr4cs1X3kutlB6uY2VGltxQFYsj5djv7D+A72A0CQQDZj1RGdxbeOo4XzxfA6n42GpZavTlM3QzGFoBJgCqqVu1JQOzooAMRT+NPfgoE8+usIVVB4Io0bCUTWLpkEytTAkEA11rzIpGIhFkPtNc/33fvBFgwUbsjTs1V5G6z5ly/XnG9ENfLblgEobLmSmz3irvBRWADiwUx5zY6FN/Dmti56wJAdiScakufcnyvzwQZ7Rwp/61+erYJGNFtb2Cmt8NO6AOehcopHMZQBCWy1ecm/7uJ/oZ3avfJdWBI3fGv/kpemwJAGMXyoDBjpu3j26bDRz6xtSs767r+VctTLSL6+O4EaaXl3PEmCrx/U+aTjU45r7Dni8Z+wdhIJFPdnJcdFkwGHwJAPQ+wVqRjc4h3Hwu8I6llk9whpK9O70FLo1FMVdaytElMyqzQ2/05fMb7F6yaWhu+Q2GGXvdlURiA3tY0CsfM0w==
-----END RSA PRIVATE KEY-----
EOD;

$FairPlayCertChain = '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';

openssl_sign($ActivationInfoXML, $signature, $private, 'sha1WithRSAEncryption'); //sha1WithRSAEncryption
$FairPlaySignature = base64_encode($signature);

$data = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>ActivationInfoComplete</key>
	<true/>
	<key>ActivationInfoXML</key>
	<data>'.$ActivationInfoXML64.'</data>
	<key>FairPlayCertChain</key>
	<data>'.$FairPlayCertChain.'</data>
	<key>FairPlaySignature</key>
	<data>'.$FairPlaySignature.'</data>
	<key>RKCertification</key>
<data>
MIIB9zCCAZwCAQEwADCB2gIBATAKBggqhkjOPQQDAgNIADBFAiEAk0kFrgp9oIqPSyw4
CeWwPc1MAGYtjvghUvV+YvDGhicCIEE0vW+s4Zs61eFjJDzvVxAKbsHFNj7MtVrbr5zT
i4k5MFswFQYHKoZIzj0CAaAKBggqhkjOPQMBBwNCAARuSdhS4I5eL1IyV2c+G690w4DH
9DFQye4b8PMbQ7FKFnhGcUOXk0eTfeF4q+b+au3l22dbj1DdioLbCCbNFVyFoAoECCBz
a3NIAAAAohYEFIT4wv/S+twSVWiuIUZOBiBDJj+OMIG3AgEBMAoGCCqGSM49BAMCA0kA
MEYCIQDngLzCQYigVMuMh3dtsq8GxrcShp6QobrHkWEmtDwjWgIhAKeWSAcq9n+wgAav
LU5TYBDy2smBJPSJxlgnECyB29RsMFswFQYHKoZIzj0CAaAKBggqhkjOPQMBBwNCAASU
2VJGBNC+Hjw5KKv3qW9IFVBE5KdWnoMwJxku1j5+7lqSe2kYxYhT1rvPAt/r1/0wALzL
aY59NYA0Ax8rKWfWMAoGCCqGSM49BAMCA0kAMEYCIQDhoMxEfjuVQgqo9ol5O6Li1Omg
JMzaL4VCTNZVXfFv/AIhALdI44Q5KEuk0FwaycYSScndcuh5B88+NuFQn41isuwM
</data>
<key>RKSignature</key>
<data>
MEQCIBfETROMXro82io/uy53ChhYmoqvTsSSdL9K9YUxW+GLAiAhh9EZ4TRxuSqWoRqm
0cop5KHlreeLv+PwHKpXn9Vmfw==
</data>
<key>serverKP</key>
	<data>
	TlVMTA==
	</data>
	<key>signActRequest</key>
	<data>
	TlVMTA==
	</data>
</dict>
</plist>';
$activation_ticket=albert_request($data);

file_put_contents($devicefolder.'ac_records.log', $activation_ticket);
$pos = strpos($activation_ticket, "AccountToken");
if ($pos !== false) {
		$extractdata = new DOMDocument;
		$extractdata->loadXML($activation_ticket);
		$nodes = $extractdata->getElementsByTagName('dict')->item(0)->getElementsByTagName('*');
		for ($i = 0; $i < $nodes->length - 1; $i=$i+2) {
		    switch ($nodes->item($i)->nodeValue) {
		        case "FairPlayKeyData": $FPKD = $nodes->item($i + 1)->nodeValue; break;
		        case "AccountToken" : $ACW = $nodes->item($i + 1)->nodeValue; break;
		        case "DeviceCertificate" : $DCtosend = $nodes->item($i + 1)->nodeValue; break;
		    }
		}
		$pregwildj=base64_decode($ACW);
		$replacejson=str_replace(" = ", " : ", $pregwildj);
		$replacejson2 = str_replace(";", ",", $replacejson);
		$ATJSON=str_replace( (strrchr( $replacejson2,"," ) ) , "}", $replacejson2 );
		$Json=json_decode($ATJSON);
		if ( isset ( $Json->ActivationTicket ) ){
			$WILDCARD = $Json->ActivationTicket;
			$JSONENCODE= new stdClass();
			$JSONENCODE->FairPlay = $FPKD;
			$JSONENCODE->WildCard = $WILDCARD; 
		}elseif ( isset ( $Json->WildcardTicket ) ){
			$WILDCARD = $Json->WildcardTicket;
			$JSONENCODE= new stdClass();
			$JSONENCODE->FairPlay = $FPKD;
			$JSONENCODE->WildCard = $WILDCARD; 
		}else{
			exit();
		}
		$JSONENCODE->DeviceCertificate = $DCtosend;
		$Json=json_encode($JSONENCODE);
		echo $Json;
}else{
echo 'NOTAUTHORIZED//'.$template;
file_put_contents('FAILREQ.log', $Number.'//'.$activation_ticket);

}

?>
