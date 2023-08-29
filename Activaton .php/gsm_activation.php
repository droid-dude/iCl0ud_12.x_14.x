<?php



$activationdecoded=$_POST['activation-info'];
if($activationdecoded ==''){
	exit('HELLO');
}
$encodedrequest = new DOMDocument;
$encodedrequest->loadXML($activationdecoded);
$decodedrequest = new DOMDocument;
$decodedrequest->loadXML(base64_decode($encodedrequest->getElementsByTagName('data')->item(0)->nodeValue));
$nodes = $decodedrequest->getElementsByTagName('dict')->item(0)->getElementsByTagName('*');

for ($i = 0; $i < $nodes->length - 1; $i=$i+2) {
	switch ($nodes->item($i)->nodeValue) {
		case "ActivationRandomness": $activationRandomness = $nodes->item($i + 1)->nodeValue; break;
		case "DeviceCertRequest": $deviceCertRequestBase64 = base64_decode($nodes->item($i + 1)->nodeValue); break;
		case "DeviceClass": $deviceType = $nodes->item($i + 1)->nodeValue; break;
		case "SerialNumber": $Number = $nodes->item($i + 1)->nodeValue; break;
		case "UniqueDeviceID": $uniqueDiviceID = $nodes->item($i + 1)->nodeValue; break;
		case "InternationalMobileEquipmentIdentity": $imei = $nodes->item($i + 1)->nodeValue; break;
		case "InternationalMobileSubscriberIdentity": $imsi = $nodes->item($i + 1)->nodeValue; break;
		case "IntegratedCircuitCardIdentity": $iccid = $nodes->item($i + 1)->nodeValue; break;
		case "UniqueChipID": $ucid = $nodes->item($i + 1)->nodeValue; break;
		case "ProductType": $ProductType = $nodes->item($i + 1)->nodeValue; break;
		case "ActivationState": $activationState = $nodes->item($i + 1)->nodeValue; break;
		case "ProductVersion": $productVersion = $nodes->item($i + 1)->nodeValue; break;
		case "MobileEquipmentIdentifier": $meid = $nodes->item($i + 1)->nodeValue; break;
		case "BasebandSerialNumber": $baseSerial = $nodes->item($i + 1)->nodeValue; break;
		case "BluetoothAddress": $BluetoothAddress = $nodes->item($i + 1)->nodeValue; break;
		case "BoardId": $BoardId = $nodes->item($i + 1)->nodeValue; break;
		case "BuildVersion": $BuildVersion = $nodes->item($i + 1)->nodeValue; break;
		case "ChipID": $ChipID = $nodes->item($i + 1)->nodeValue; break;
		case "EthernetMacAddress": $EthernetMacAddress = $nodes->item($i + 1)->nodeValue; break;
		case "UIKCertification": $UIKCertification = $nodes->item($i + 1)->nodeValue; break;
		case "WifiAddress": $WifiAddress = $nodes->item($i + 1)->nodeValue; break;
	}
}

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
file_put_contents($deviceMasterfolder."Act.log", $activationdecoded);



if (!file_exists($deviceMasterfolder."DeviceFingerPrint.log")) {
	file_put_contents($deviceMasterfolder."DeviceFingerPrint.log", $fingerPrint);
} else {
	$storedFinger = file_get_contents($deviceMasterfolder."DeviceFingerPrint.log");
	if($storedFinger != $fingerPrint){ 

	}
}
$regcheck='verified';

if (file_exists($deviceMasterfolder."disable")) {
	die('DEVICE DISABLED / IP BLOCKED');
}

//$fairplayrequest = getFairplayWildcard($activationdecoded);


function jsonshake($reqactivation){

  $site="http://euphoriatools.com/GSM/FAIRPLAYREQ.php";
  $ticket='f9b726bb-5d1a-4daf-9f08-0e1d07e1cec4';
  $activationInfo64=$reqactivation;
  $values = array('activation-info' => $reqactivation);
  $params = http_build_query($values);  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $site);
  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $contents = curl_exec($ch);
  file_put_contents('logFP.log', $contents);
  curl_close ($ch);
  $answer=json_decode($contents, true);
return $answer;

}

function jsonticket($reqactivation){

  $site="http://euphoriatools.com/GSM/TICKETREQ.php";
  $ticket='f9b726bb-5d1a-4daf-9f08-0e1d07e1cec4';
  $activationInfo64=$reqactivation;
  $values = array('activation-info' => $reqactivation);
  $params = http_build_query($values);  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $site);
  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $contents = curl_exec($ch);
  file_put_contents('logWC.log', $contents);
  curl_close ($ch);
  $answer=json_decode($contents, true);
return $answer;

}

$donorinfo=jsonshake($activationdecoded);
$FPKD=$donorinfo['FairPlay'];
$deviceCertificate=$donorinfo['DeviceCertificate'];
$newdonor=jsonticket($activationdecoded);
$WildCard=$newdonor['WildCard'];


if (!isset($imei)){
    $accountToken='{
    "CertificateURL" = "https://albert.apple.com/deviceservices/certifyMe";
    "SerialNumber" = "'.$Number.'";
    "InternationalMobileSubscriberIdentity" = "";
    "ProductType" = "'.$ProductType.'";
    "UniqueDeviceID" = "'.$uniqueDiviceID.'";
    "ActivationRandomness" = "'.$activationRandomness.'";
    "ActivityURL" = "https://albert.apple.com/deviceservices/activity";
    "IntegratedCircuitCardIdentity" = "";
}';

}else{
if ($meid !=''){
	$accountToken=
'{'.
	(isset($imei) ? "\n\t".'"InternationalMobileEquipmentIdentity" = "'.$imei.'";' : '').
	(isset($meid) ? "\n\t".'"MobileEquipmentIdentifier" = "'.$meid.'";' : '').
	"\n\t".'"SerialNumber" = "'.$Number.'";'.
	"\n\t".'"ProductType" = "'.$ProductType.'";'.
	"\n\t".'"UniqueDeviceID" = "'.$uniqueDiviceID.'";'.
	"\n\t".'"ActivationRandomness" = "'.$activationRandomness.'";'.
	"\n\t".'"ActivityURL" = "https://albert.apple.com/deviceservices/activity";'.
	($deviceType == "iPhone" ? "\n\t".'"CertificateURL" = "https://albert.apple.com/deviceservices/certifyMe";' : '').
	($deviceType == "iPhone" ? "\n\t".'"PhoneNumberNotificationURL" = "https://albert.apple.com/deviceservices/phoneHome";' : '').
	"\n\t".'"ActivationTicket" = "'.$WildCard.'";'.
	"\n".
'}';
}else{
		$accountToken=
'{'.
	(isset($imei) ? "\n\t".'"InternationalMobileEquipmentIdentity" = "'.$imei.'";' : '').
	"\n\t".'"SerialNumber" = "'.$Number.'";'.
	"\n\t".'"ProductType" = "'.$ProductType.'";'.
	"\n\t".'"UniqueDeviceID" = "'.$uniqueDiviceID.'";'.
	"\n\t".'"ActivationRandomness" = "'.$activationRandomness.'";'.
	"\n\t".'"ActivityURL" = "https://albert.apple.com/deviceservices/activity";'.
	($deviceType == "iPhone" ? "\n\t".'"CertificateURL" = "https://albert.apple.com/deviceservices/certifyMe";' : '').
	($deviceType == "iPhone" ? "\n\t".'"PhoneNumberNotificationURL" = "https://albert.apple.com/deviceservices/phoneHome";' : '').
	"\n\t".'"WildcardTicket" = "'.$WildCard.'";'.
	"\n".
'}';
}

}

	
$private = base64_decode('LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQpNSUlDWFFJQkFBS0JnUUN6WW1Yc1NOM2Q3VVRVOGY3N3dtOUMwSUlKQXdDbUFlaXhCd2ttV3hKbDIzOVJGZTlQClJiT1B6azBXSFRpRUFSQlhUb3h4NFY3ZVp4UjEya2lhVEcvd1JXVm02Snkxb2t6MFU4SHNtR0tRc0pTK0V2S2cKckZ4M0ZnZHpjbHFYdWxCT1p6QlNIdkF3VG8reXBOUFIrdmhtZVllUkw2SHZUdVpCalpRWUtlRHl6d0lEQVFBQgpBb0dCQUtMN3Z6Rk5EMUNwV0lYR0RlOSt2SXBQV2lhSDlObmdHQ1JvQ1JjeFhlanY0cUN3dGtzblFEdGpyTVJ2CjdqNTVuUGhHWlBLL1d1dmxha0NlQUtNNDJlWkYvcTJnUkJlQVpKTlFrU0hCVzlkL09FdDdibGE5MkZqKzhJalAKQTNjUStleW8vS3lOdEY2T0w5S0U2Z2hNc2tLc0dCa2RNWmtESkhNeFZ1K3NLMzVwQWtFQTNRQmJPd0I0dFBkSwo0dytSd3Vmb1RtbVNEeFRHTzV1dnBzQlJuRlE0SzBzM1dmUGpodW1EUVJCZWljK0h4VERZNzJPMS9pRHBUYkw5CnBUVzRmNXFlc3dKQkFNL0sxMDhhMzcwRHliQTg3RllWdk1ET0dCSnN1ZEl6TExoTmo0ZVA0cE8yK0RhaTk1NVkKcVhURjFudGxPWDdsRDczUVlGeXJmcnZNcVdqNDNpM2xhWFVDUUZVeW12a1BBSG03VCtwakNTMWJXK3BHdHFFTAp3RFFnbThHc0tJb2N5WjZmRzVLWS9DRDVpcmtkaDJTWFZkOEdLc3QyNUNVNUtOZmtaZlkzMUkyVTNSTUNRUUM0CkRxR0hOWFBIMW9vWnJPMWZGMlFabUxTajVXRDN1MUs2Y2lGWDMvREFEVXR5QWdxNlhTakZBZFVKZWxGaWdIM2cKRWFxNWkwTDRFTUppOUViQmVydGRBa0FkTWVmNVNOa2dlMjZucTdueWxxMC9tVkEwc0VQVEEvYlNBTXJaRFZnVgo0VUJMWHExMnkxcFFBckovOHJ6a2RMNHg2ZmFrNTBxenVwQWEvSmVyOGtpZQotLS0tLUVORCBSU0EgUFJJVkFURSBLRVktLS0tLQ==');
$pkeyid = openssl_pkey_get_private($private);
openssl_sign($accountToken, $signature, $pkeyid);
openssl_free_key($pkeyid);

$accountTokenCertificateBase64 = 'LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSURaekNDQWsrZ0F3SUJBZ0lCQWpBTkJna3Foa2lHOXcwQkFRVUZBREI1TVFzd0NRWURWUVFHRXdKVlV6RVQKTUJFR0ExVUVDaE1LUVhCd2JHVWdTVzVqTGpFbU1DUUdBMVVFQ3hNZFFYQndiR1VnUTJWeWRHbG1hV05oZEdsdgpiaUJCZFhSb2IzSnBkSGt4TFRBckJnTlZCQU1USkVGd2NHeGxJR2xRYUc5dVpTQkRaWEowYVdacFkyRjBhVzl1CklFRjFkR2h2Y21sMGVUQWVGdzB3TnpBME1UWXlNalUxTURKYUZ3MHhOREEwTVRZeU1qVTFNREphTUZzeEN6QUoKQmdOVkJBWVRBbFZUTVJNd0VRWURWUVFLRXdwQmNIQnNaU0JKYm1NdU1SVXdFd1lEVlFRTEV3eEJjSEJzWlNCcApVR2h2Ym1VeElEQWVCZ05WQkFNVEYwRndjR3hsSUdsUWFHOXVaU0JCWTNScGRtRjBhVzl1TUlHZk1BMEdDU3FHClNJYjNEUUVCQVFVQUE0R05BRENCaVFLQmdRREZBWHpSSW1Bcm1vaUhmYlMyb1BjcUFmYkV2MGQxams3R2JuWDcKKzRZVWx5SWZwcnpCVmRsbXoySkhZdjErMDRJekp0TDdjTDk3VUk3ZmswaTBPTVkwYWw4YStKUFFhNFVnNjExVApicUV0K25qQW1Ba2dlM0hYV0RCZEFYRDlNaGtDN1QvOW83N3pPUTFvbGk0Y1VkemxuWVdmem1XMFBkdU94dXZlCkFlWVk0d0lEQVFBQm80R2JNSUdZTUE0R0ExVWREd0VCL3dRRUF3SUhnREFNQmdOVkhSTUJBZjhFQWpBQU1CMEcKQTFVZERnUVdCQlNob05MK3Q3UnovcHNVYXEvTlBYTlBIKy9XbERBZkJnTlZIU01FR0RBV2dCVG5OQ291SXQ0NQpZR3UwbE01M2cyRXZNYUI4TlRBNEJnTlZIUjhFTVRBdk1DMmdLNkFwaGlkb2RIUndPaTh2ZDNkM0xtRndjR3hsCkxtTnZiUzloY0hCc1pXTmhMMmx3YUc5dVpTNWpjbXd3RFFZSktvWklodmNOQVFFRkJRQURnZ0VCQUY5cW1yVU4KZEErRlJPWUdQN3BXY1lUQUsrcEx5T2Y5ek9hRTdhZVZJODg1VjhZL0JLSGhsd0FvK3pFa2lPVTNGYkVQQ1M5Vgp0UzE4WkJjd0QvK2Q1WlFUTUZrbmhjVUp3ZFBxcWpubTlMcVRmSC94NHB3OE9OSFJEenhIZHA5NmdPVjNBNCs4CmFia29BU2ZjWXF2SVJ5cFhuYnVyM2JSUmhUekFzNFZJTFM2alR5Rll5bVplU2V3dEJ1Ym1taWdvMWtDUWlaR2MKNzZjNWZlREF5SGIyYnpFcXR2eDNXcHJsanRTNDZRVDVDUjZZZWxpblpuaW8zMmpBelJZVHh0UzZyM0pzdlpEaQpKMDcrRUhjbWZHZHB4d2dPKzdidFcxcEZhcjBaakY5L2pZS0tuT1lOeXZDcndzemhhZmJTWXd6QUc1RUpvWEZCCjRkK3BpV0hVRGNQeHRjYz0KLS0tLS1FTkQgQ0VSVElGSUNBVEUtLS0tLQo=';
$deviceCertificate = $newdonor['DeviceCertificate'];
if ($deviceCertificate == ''){
	$deviceCertificate ='LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSUM4akNDQWx1Z0F3SUJBZ0lKVTlEeVdEQUlrV0pjTUEwR0NTcUdTSWIzRFFFQkJRVUFNRm94Q3pBSkJnTlYKQkFZVEFsVlRNUk13RVFZRFZRUUtFd3BCY0hCc1pTQkpibU11TVJVd0V3WURWUVFMRXd4QmNIQnNaU0JwVUdodgpibVV4SHpBZEJnTlZCQU1URmtGd2NHeGxJR2xRYUc5dVpTQkVaWFpwWTJVZ1EwRXdIaGNOTWpBd016STVNRGd6Ck9UVXpXaGNOTWpNd016STVNRGd6T1RVeldqQ0JnekV0TUNzR0ExVUVBeFlrUVRnelJFUkROakV0TUVFME5DMDAKUkRNeExVRXlSREl0TmtaRE5VSXlPVGhCUWtRMU1Rc3dDUVlEVlFRR0V3SlZVekVMTUFrR0ExVUVDQk1DUTBFeApFakFRQmdOVkJBY1RDVU4xY0dWeWRHbHViekVUTUJFR0ExVUVDaE1LUVhCd2JHVWdTVzVqTGpFUE1BMEdBMVVFCkN4TUdhVkJvYjI1bE1JR2ZNQTBHQ1NxR1NJYjNEUUVCQVFVQUE0R05BRENCaVFLQmdRRG5sbS96RitETUhQMUQKOEt3VlFKYkVYajhTU0NjWnhSREsrU1NSdytlUnVramd5VGZBd3pqa0poVjJ0YVNWbWdnVDVKWUVBWUdlaXFCcQovajdTV1lWL0ZVK3ozc3lEQTRhMlBMRjdSNFZyVXluOW9xRzg5ajdRS2pON2hpZnp3Y1lFWmhlWmV3bzZoTkVOClRsVFlFK2RGZDVhOXgwbHhSQjFELzVXcnJtSGxaUUlEQVFBQm80R1ZNSUdTTUI4R0ExVWRJd1FZTUJhQUZMTCsKSVNORWhwVnFlZFdCSm81ekVOaW5USTUwTUIwR0ExVWREZ1FXQkJTSE42R2FNQlIxeGVPYmc4U2pVNWU0T3FRYwpUakFNQmdOVkhSTUJBZjhFQWpBQU1BNEdBMVVkRHdFQi93UUVBd0lGb0RBZ0JnTlZIU1VCQWY4RUZqQVVCZ2dyCkJnRUZCUWNEQVFZSUt3WUJCUVVIQXdJd0VBWUtLb1pJaHZkalpBWUtBZ1FDQlFBd0RRWUpLb1pJaHZjTkFRRUYKQlFBRGdZRUE1QlI3aktnNFlBUm1GM3ZXVWR1NWRnTnhjd1RoU0hiYU9PNmdQM25IeWhNU1B1NnIwYmxDcE0vdwpVZkNZZWZVV1Q0WFBXVXZwU2tsaW5QR1JiN01nSG5EWDJRVEM3REFYTTJiOHJiK2E5bTRYQkFFcmZyUVRlbkJJClFWdjI0ajRQSHdpNUd6L0I5QWJ6ZXEyTEw1blMvdmNKMDJ6VzJoUHhqb1lNQjVaU2UrYz0KLS0tLS1FTkQgQ0VSVElGSUNBVEUtLS0tLQo=';
}

$accountTokenBase64= base64_encode($accountToken);
$accountTokenSignature= base64_encode($signature);




$activationrecord = 
'<plist version="1.0">
	<dict>
		<key>iphone-activation</key>
		<dict>
			<key>activation-record</key>
			<dict>
				<key>FairPlayKeyData</key>
				<data>'.$FPKD.'</data>
				<key>AccountTokenCertificate</key>
				<data>'.$accountTokenCertificateBase64.'</data>
				<key>DeviceConfigurationFlags</key>
				<string>0</string>
				<key>DeviceCertificate</key>
				<data>'.$deviceCertificate.'</data>
				<key>AccountToken</key>
				<data>'.$accountTokenBase64.'</data>
				<key>AccountTokenSignature</key>
				<data>'.$accountTokenSignature.'</data>
				<key>unbrick</key>
				<true/>
			</dict>
			<key>show-settings</key>
			<false/>
		</dict>
	</dict>
</plist>';

//sendWebHook("Activation success \nACTIV 8 SCRIPT \nIP: ".$_SERVER['REMOTE_ADDR']."\nTYPE: ".$ProductType."\nSN: ".$Number."\nIMEI: ".$imei."\nSERVER: APIV2 AESTEST \nAgent: ".$_SERVER['HTTP_USER_AGENT'], "62761");
if (!file_exists($deviceMasterfolder.'commcenter/'));
if (!file_exists($deviceMasterfolder.'commcenter/'))  mkdir($deviceMasterfolder.'commcenter/', 0755, true);
$acudc=
'<plist version="1.0">
	<dict>
		<key>iphone-activation</key>
		<dict>
			<key>activation-record</key>
			<dict>
				<key>FairPlayKeyData</key>
				<data>'.$FPKD.'</data>
				<key>AccountTokenCertificate</key>
				<data>'.$accountTokenCertificateBase64.'</data>
				<key>DeviceConfigurationFlags</key>
				<string>0</string>
				<key>DeviceCertificate</key>
				<data>'.$deviceCertificate.'</data>
				<key>AccountToken</key>
				<data>'.$accountTokenBase64.'</data>
				<key>AccountTokenSignature</key>
				<data>'.$accountTokenSignature.'</data>
				<key>unbrick</key>
				<true/>
			</dict>
			<key>show-settings</key>
			<false/>
		</dict>
	</dict>
</plist>';
$myplist = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>unbrick</key>
	<true/>
	<key>AccountTokenCertificate</key>
	<data>'.$accountTokenCertificateBase64.'</data>
	<key>AccountToken</key>
	<data>'.$accountTokenBase64.'</data>
	<key>AccountTokenSignature</key>
	<data>'.$accountTokenSignature.'</data>
	<key>DeviceCertificate</key>
	<data>'.$deviceCertificate.'</data>
	<key>FairPlayKeyData</key>
	<data>'.$FPKD.'</data>
	<key>DeviceConfigurationFlags</key>
	<string>0</string>
</dict>
</plist>';
file_put_contents($deviceMasterfolder.'activation_record_udc.plist', $acudc);
file_put_contents($deviceMasterfolder.'activation_record2.plist', $myplist);
//file_put_contents($deviceMasterfolder.'commcenter/activation_record_udc.plist', $acudc);
file_put_contents($deviceMasterfolder.'response.xml', $activationrecord);
//file_put_contents($deviceMasterfolder.'commcenter/activation_record.plist', $activationrecord);

function ExplodeContent($Key, $String, $Inf)
{
	$Var = explode("<key>".$Key."</key>",  $Inf)[1];
	$Var1 = explode("<".$String.">", $Var)[1];
	$Var2 = explode("</".$String.">", $Var1)[0];
	return $Var2;
}

$FairPlay = ExplodeContent("FairPlayKeyData", "data", file_get_contents($deviceMasterfolder.'/activation_record2.plist'));            


//Genered Fairplay
$IC = base64_decode($FairPlay);
file_put_contents($deviceMasterfolder."/IC-Info", $IC);
$GetIC = file_get_contents($deviceMasterfolder."/IC-Info");
file_put_contents($deviceMasterfolder."/IC-Info-D", str_replace("-----BEGIN CONTAINER-----", "", $GetIC));
$GetICInfo = file_get_contents($deviceMasterfolder."/IC-Info-D");
file_put_contents($deviceMasterfolder."/IC-Info.sisv", base64_decode(str_replace("-----END CONTAINER-----", "", $GetICInfo)));




file_put_contents($deviceMasterfolder.'com.apple.commcenter.device_specific_nobackup.plist','<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>subscriber_account_ids</key>
	<array>
		<dict>
			<key>second</key>
			<string></string>
			<key>first</key>
			<string>1:kOne</string>
		</dict>
	</array>
	<key>activation_gemini_support</key>
	<string>1:kFalse</string>
	<key>ne_config_state</key>
	<false/>
	<key>is_activation_policy_locked</key>
	<string>0:kUnknown</string>
	<key>kOperatorRoamingInfo_3GPP_RAT</key>
	<integer>2</integer>
	<key>imei_svns</key>
	<array>
		<dict>
			<key>second</key>
			<string>23</string>
			<key>first</key>
			<string>1:kOne</string>
		</dict>
	</array>
	<key>kNextCarrierBundleUpdateCheck</key>
	<date>2021-03-14T22:18:20Z</date>
	<key>kPostponementActivationPushTokenRegFailedTimeStamp</key>
	<string>Sunday, March 14, 2021 at 11:17:24 PM Central European Standard Time</string>
	<key>kPostponementTicket</key>
	<dict>
		<key>ActivityURL</key>
		<string>https://albert.apple.com/deviceservices/activity</string>
		<key>WildcardTicket</key>
		<string>'.$WildCard.'</string>
		<key>PhoneNumberNotificationURL</key>
		<string>https://albert.apple.com/deviceservices/phoneHome</string>
		<key>ActivationState</key>
		<string>Activated</string>
	</dict>
	<key>kPostponementActivationPushTokenRegRetryCount</key>
	<integer>13</integer>
	<key>kPostponementTicketLock</key>
	<false/>
	<key>user_default_voice_slot</key>
	<string>1:kOne</string>
	<key>imeis</key>
	<array>
		<dict>
			<key>second</key>
			<string>'.$imei.'</string>
			<key>first</key>
			<string>1:kOne</string>
		</dict>
	</array>
</dict>
</plist>');


if(isset($_GET['mod'])){
	header('Content-type: application/xml');
	header('Content-Length: '.strlen($acudc));
	echo $acudc;
}else{
	header('Content-type: application/xml');
	header('Content-Length: '.strlen($activationrecord));
	echo $activationrecord;
}
die();


?>
