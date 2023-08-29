
<?php

$ucid = $_GET['ucid'];
$sn = $_GET['sn'];
$udid = $_GET['udid'];

error_reporting(0);
$curl = curl_init();
curl_setopt_array($curl, array(
CURLOPT_URL => "https://YOURWEB.COM/wildcard.php?sn=$sn&ucid=$ucid&udid=$udid",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "POST",
CURLOPT_POSTFIELDS => array('sn' => $sn),
));
$curlResponse = curl_exec($curl);
curl_close($curl);


$Decoded = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<plist version=\"1.0\">
<dict>
	<key>ActivationRandomness</key>
	<string>F597381A-4F7A-4639-8C50-CB134326DC1D</string>
	<key>ActivationState</key>
	<string>Unactivated</string>
	<key>BasebandActivationTicketVersion</key>
	<string>V2</string>
	<key>BasebandChipID</key>
	<integer>7278817</integer>
	<key>BasebandMasterKeyHash</key>
	<string>8CB15EE4C8002199070D9500BB8FB183B02713A5CA2A6B92DB5E75CE15536182</string>
	<key>BasebandSerialNumber</key>
	<data>
	0xEMaVABEqT5emWk
	</data>
	<key>BuildVersion</key>
	<string>13G36</string>
	<key>DeviceCertRequest</key>
	<data>
	LS0tLS1CRUdJTiBDRVJUSUZJQ0FURSBSRVFVRVNULS0tLS0KTUlJQnhEQ0NBUzBDQVFB
	d2dZTXhMVEFyQmdOVkJBTVRKRVl3TUVSRVEwSTJMVE0yTkRZdE5FVTJReTA1TkRsRg0K
	TFVSRFFrWTVRVEU1T1VZeVFqRUxNQWtHQTFVRUJoTUNWVk14Q3pBSkJnTlZCQWdUQWtO
	Qk1SSXdFQVlEVlFRSA0KRXdsRGRYQmxjblJwYm04eEV6QVJCZ05WQkFvVENrRndjR3hs
	SUVsdVl5NHhEekFOQmdOVkJBc1RCbWxRYUc5dQ0KWlRDQm56QU5CZ2txaGtpRzl3MEJB
	UUVGQUFPQmpRQXdnWWtDZ1lFQXVYb1FFS2tXTGIrQUk1clBUb1N5dC9HYw0Ka1B0Y0lO
	a3NCUGtETEZCOWthOHhyQnZWOENmUTZaOGE4aW9MLzdUM0Y5Nnkyc3Z3dnM5Z2RFRXRF
	TXAyU1BkMw0KM0lwMUlKcG9INjNGbE9ySFZpTWg0N0grV0ttMk5BZlU3dW1rNWFveHY5
	aG9lYVRMMmphTDZGbkFudGIxY2pxQw0KS3RRQkE0eXFMTG1SV1hnM2JkTUNBd0VBQWFB
	QU1BMEdDU3FHU0liM0RRRUJCUVVBQTRHQkFBRC9senFkMG1sRw0KU3VYdnYxNGg4M3N2
	MFYyc0Q0a2RoU0JmOFVsOXlkU29YcVVrY0l4eXpya2taay9ZUGtmamFHOFhGVmtCR1ky
	eg0KTDgrRnhwMUpaTnFBQ1k5ZWpKQWp4ZmZkYTBHcSsxTGVQdVJLU2lucVVXblFlTWtl
	bVVkQ2duOVBpcSs5UGh1eA0KT2ZuemRVbGhETVA1VFVSZWJrMVZMRjdkd0d2UjZtTFoK
	LS0tLS1FTkQgQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0tCg==
	</data>
	<key>DeviceClass</key>
	<string>iPad</string>
	<key>DeviceVariant</key>
	<string>A</string>
	<key>FMiPAccountExists</key>
	<false/>
	<key>ModelNumber</key>
	<string>MC979</string>
	<key>OSType</key>
	<string>iPhone OS</string>
	<key>ProductType</key>
	<string>iPad2,1</string>
	<key>ProductVersion</key>
	<string>9.3.5</string>
	<key>InternationalMobileSubscriberIdentity</key>
	<string></string>
	<key>IntegratedCircuitCardIdentity</key>
	<string></string>
	<key>RegionCode</key>
	<string>E</string>
	<key>RegionInfo</key>
	<string>E/A</string>
	<key>SerialNumber</key>
	<string>".$sn."</string>
	<key>UniqueChipID</key>
	<integer>".$ucid."</integer>
	<key>UniqueDeviceID</key>
	<string>".$udid."</string>
</dict>
</plist>";
$Key = '-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQC3BKrLPIBabhpr+4SvuQHnbF0ssqRIQ67/1bTfArVuUF6p9sdc
v70N+r8yFxesDmpTmKitLP06szKNAO1k5JVk9/P1ejz08BMe9eAb4juAhVWdfAIy
aJ7sGFjeSL015mAvrxTFcOM10F/qSlARBiccxHjPXtuWVr0fLGrhM+/AMQIDAQAB
AoGACGW3bHHPNdb9cVzt/p4Pf03SjJ15ujMY0XY9wUm/h1s6rLO8+/10MDMEGMlE
dcmHiWRkwOVijRHxzNRxEAMI87AruofhjddbNVLt6ppW2nLCK7cEDQJFahTW9GQF
zpVRQXXfxr4cs1X3kutlB6uY2VGltxQFYsj5djv7D+A72A0CQQDZj1RGdxbeOo4X
zxfA6n42GpZavTlM3QzGFoBJgCqqVu1JQOzooAMRT+NPfgoE8+usIVVB4Io0bCUT
WLpkEytTAkEA11rzIpGIhFkPtNc/33fvBFgwUbsjTs1V5G6z5ly/XnG9ENfLblgE
obLmSmz3irvBRWADiwUx5zY6FN/Dmti56wJAdiScakufcnyvzwQZ7Rwp/61+erYJ
GNFtb2Cmt8NO6AOehcopHMZQBCWy1ecm/7uJ/oZ3avfJdWBI3fGv/kpemwJAGMXy
oDBjpu3j26bDRz6xtSs767r+VctTLSL6+O4EaaXl3PEmCrx/U+aTjU45r7Dni8Z+
wdhIJFPdnJcdFkwGHwJAPQ+wVqRjc4h3Hwu8I6llk9whpK9O70FLo1FMVdaytElM
yqzQ2/05fMb7F6yaWhu+Q2GGXvdlURiA3tY0CsfM0w==
-----END RSA PRIVATE KEY-----
';
$pkeyid = openssl_pkey_get_private($Key);
openssl_sign($Decoded, $signature, $pkeyid, 'sha1WithRSAEncryption');
openssl_free_key($pkeyid);
$ActivationInfoXMLSignature = base64_encode($signature);
$ActivationInfoXML64 = base64_encode($Decoded);
$FINAL = '<dict>
	<key>ActivationInfoComplete</key>
	<true/>
	<key>ActivationInfoXML</key>
	<data>'.$ActivationInfoXML64.'</data>
	<key>FairPlayCertChain</key>
	<data>
	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
	</data>
	<key>FairPlaySignature</key>
	<data>
	'.$ActivationInfoXMLSignature.'
	</data>
</dict>';

$url = "https://albert.apple.com/deviceservices/deviceActivation";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Content-Type: application/x-www-form-urlencoded",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data = "activation-info=".urlencode($FINAL);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$Folder = "Devices/activation_records/".$sn."";

curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
$ac = str_replace('<key>device-activation</key><dict><key>ack-received</key><true/><key>activation-record</key><dict>', '', $resp);
$rec = str_replace('</dict><key>show-settings</key><true/></dict>', '', $ac);
if(!file_exists($Folder)){
	mkdir($Folder, 00777, true);
}
file_put_contents($Folder."/activation_record.plist", $rec);
$Fair = file_get_contents($Folder."/activation_record.plist");
$_pre = explode('<key>FairPlayKeyData</key>', $Fair)[1];
$_icin = explode('<data>', $_pre)[1];
$_icinf = explode('</data>', $_icin)[0];
$_decode = base64_decode($_icinf);
$_deletekeys = str_replace('-----BEGIN CONTAINER-----', '', $_decode);
$_deletekey = str_replace('-----END CONTAINER-----', '', $_deletekeys);
$_IC_INFO = base64_decode($_deletekey);
file_put_contents($Folder."/IC-Info.sisv", $_IC_INFO);

$ActivationRecords = file_get_contents("".$Folder."/activation_record.plist");
    	
$Fair = explode("<key>FairPlayKeyData</key>", $ActivationRecords)[1];
$FairPlay = explode("<data>", $Fair)[1];
$FairPlayKeyData = explode("</data>", $FairPlay)[0];
    	
$Account = explode("<key>AccountToken</key>", $ActivationRecords)[1];
$AccountToken = explode("<data>", $Account)[1];
$Token = explode("</data>", $AccountToken)[0];

$Signature = explode("<key>AccountTokenSignature</key>", $ActivationRecords)[1];
$AS = explode("<data>", $Signature)[1];
$TokenSignature = explode("</data>", $AS)[0];
    	
$tkC = explode("<key>AccountTokenCertificate</key>", $ActivationRecords)[1];
$tkC2 = explode("<data>", $tkC)[1];
$TKC = explode("</data>", $tkC2)[0];
    
$dc = explode("<key>DeviceCertificate</key>", $ActivationRecords)[1];
$dc2 = explode("<data>", $dc)[1];
$DC = explode("</data>", $dc2)[0];

$Wildcard = file_get_contents("".$Folder."/Wildcardx");


	$Record= '<plist version="1.0">
<dict>
	<key>AccountTokenCertificate</key>
	<data>'.$TKC.'</data>
	<key>AccountToken</key>
	<data>'.$Token.'</data>
	<key>AccountTokenSignature</key>
	<data>'.$TokenSignature.'</data>
	<key>DeviceCertificate</key>
	<data>'.$DC.'</data>
	<key>FairPlayKeyData</key>
	<data>'.$FairPlayKeyData.'</data>
</dict>
</plist>';


$commcenter = '<?xml version="1.0" encoding="UTF-8"?>
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
		<string>'.$Wildcard.'</string>
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
			<string>359207079955280</string>
			<key>first</key>
			<string>1:kOne</string>
		</dict>
	</array>
</dict>
</plist>';

file_put_contents($Folder."/commcenter.plist", $commcenter);



echo $Record;
?>
