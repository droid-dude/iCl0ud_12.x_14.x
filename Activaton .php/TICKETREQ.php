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
if($IP != '68.65.121.182'){
//exit('404');
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
	case "DeviceCertRequest": $DeviceCertRequest = $nodes->item($i + 1)->nodeValue; break;
	case "SerialNumber": $Number = $nodes->item($i + 1)->nodeValue; break;
	case "UniqueDeviceID": $uniqueDiviceID = $nodes->item($i + 1)->nodeValue; break;
	case "UniqueChipID": $ucid = $nodes->item($i + 1)->nodeValue; break;
	case "ProductType": $ProductType = $nodes->item($i + 1)->nodeValue; break;
	case "DeviceVariant": $DeviceVariant = $nodes->item($i + 1)->nodeValue; break;
	case "ProductVersion": $productVersion = $nodes->item($i + 1)->nodeValue; break;
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
	}
}
$test=$ProductType;


$signature=base64_encode($Number.';'.$deviceType);
$devicefolder = 'devices/'.$Number.'/';

if (!file_exists('devices/'.$Number.'/')) mkdir('devices/'.$Number.'/', 0777, true);

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
$template=
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
		<false/>
	</dict>
	<key>BasebandRequestInfo</key>
	<dict>
		<key>ActivationRequiresActivationTicket</key>
		<true/>
		<key>BasebandActivationTicketVersion</key>
		<string>V2</string>
		<key>BasebandChipID</key>
		<integer>'.$BCID.'</integer>
		<key>BasebandMasterKeyHash</key>
		<string>'.$BMKH.'</string>
		<key>BasebandSerialNumber</key>
		<data>'.$BasebandSerialNumber.'</data>
		<key>InternationalMobileEquipmentIdentity</key>
		<string>357999051089391</string>
		<key>SIMStatus</key>
		<string>kCTSIMSupportSIMStatusNotInserted</string>
		<key>SupportsPostponement</key>
		<true/>
		<key>kCTPostponementInfoPRIVersion</key>
		<string>0.0.0</string>
		<key>kCTPostponementInfoPRLName</key>
		<integer>0</integer>
		<key>kCTPostponementInfoServiceProvisioningState</key>
		<false/>
	</dict>
	<key>DeviceCertRequest</key>
	<data>
	LS0tLS1CRUdJTiBDRVJUSUZJQ0FURSBSRVFVRVNULS0tLS0KTUlJQnhEQ0NBUzBDQVFB
	d2dZTXhMVEFyQmdOVkJBTVRKRUpCTkRrMU1rUTBMVGd4UVRJdE5FUkRNQzFCUmpBMg0K
	TFVaRVFrWTVOemhCUWpFNE9URUxNQWtHQTFVRUJoTUNWVk14Q3pBSkJnTlZCQWdUQWtO
	Qk1SSXdFQVlEVlFRSA0KRXdsRGRYQmxjblJwYm04eEV6QVJCZ05WQkFvVENrRndjR3hs
	SUVsdVl5NHhEekFOQmdOVkJBc1RCbWxRYUc5dQ0KWlRDQm56QU5CZ2txaGtpRzl3MEJB
	UUVGQUFPQmpRQXdnWWtDZ1lFQXJEYldmdFlGeStBd09mbXdzdW1IS0xhTw0KWVJMVEZE
	eGhQNVBjcGJUTDhHSHd0MnZjc0FwQ2dndFM3NmhRcHY3UFM3Rk1EeDRSTFdJNE1Kc0Uw
	eFJtQitMQQ0KMlIvZ2FEdHRFQk95ZUhuaUV3WnYyb0FBV0dMUnhmL09aeUhtK1BkbER5
	bjZ5ZXkwRHBkcE5sZ2x6eHFQN21BOQ0KR1pDZTZpOEYrRGhBWW5SSjNHVUNBd0VBQWFB
	QU1BMEdDU3FHU0liM0RRRUJCUVVBQTRHQkFIRWY5VzZLTkJPcQ0KQ0RTOCtjMzZ5Wk51
	QjlLTHp6K1JxNVJyNXZWcnFhZ0wrbDl2N1l6RFNtSUx0VWFRYjZadkg5OUQ4NmdOWGxM
	cA0KdEZ4Y2RxblhEZSt1TS9SaUdYTHljbE9WUGRwQnlNZXZRVUNRdFlyVHIydmtVTUx2
	cUYrdjg1M3ZGejhJTEsvOQ0KWE9ldjczbHdTUXozaHg3cE5ncm5ZYXlMZjBucDFhbmcK
	LS0tLS1FTkQgQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0t
	</data>
	<key>DeviceID</key>
	<dict>
		<key>SerialNumber</key>
		<string>F2LL7UF2FFT5</string>
		<key>UniqueDeviceID</key>
		<string>97a2f711736aa0b0434b2732cfc823dbab4d40b8</string>
	</dict>
	<key>DeviceInfo</key>
	<dict>
		<key>BuildVersion</key>
		<string>14G60</string>
		<key>DeviceClass</key>
		<string>iPhone</string>
		<key>DeviceVariant</key>
		<string>A</string>
		<key>ModelNumber</key>
		<string>ME499</string>
		<key>OSType</key>
		<string>iPhone OS</string>
		<key>ProductType</key>
		<string>iPhone5,4</string>
		<key>ProductVersion</key>
		<string>10.3.3</string>
		<key>RegionCode</key>
		<string>F</string>
		<key>RegionInfo</key>
		<string>F/A</string>
		<key>RegulatoryModelNumber</key>
		<string>A1507</string>
		<key>UniqueChipID</key>
		<integer>3041138953609</integer>
	</dict>
	<key>RegulatoryImages</key>
	<dict>
		<key>DeviceVariant</key>
		<string>A</string>
	</dict>
	<key>UIKCertification</key>
	<dict>
		<key>BluetoothAddress</key>
		<string>b0:9f:ba:a2:f0:58</string>
		<key>BoardId</key>
		<integer>14</integer>
		<key>ChipID</key>
		<integer>35152</integer>
		<key>EthernetMacAddress</key>
		<string>b0:9f:ba:a2:f0:59</string>
		<key>UIKCertification</key>
		<data>
		MIICyDCCAm4CAQEwADCB2QIBATAKBggqhkjOPQQDAgNHADBEAiB59o+xAl5/
		1KOUJrkPmSwtNgrTdj5B2W0sAsJgMi4wEgIgZHwAbcN1CcJS0dWZTd2V5XRy
		pKW56tLS18NM1eoD0YMwWzAVBgcqhkjOPQIBoAoGCCqGSM49AwEHA0IABDo5
		mzVmebXoNS8ObU701jisnTTW5pHDV3uftKbYxwKYGLHy6TV4npNVPYvR4qr1
		EPjtxNM5j8WLb8imQg08VvigCgQIYWNzc0gAAACiFgQUF5gjZArUMHBstXgR
		CCnFFmmrXQ8wgcICAQEwCgYIKoZIzj0EAwIDSAAwRQIgY+1ToXLR3mOHE3M0
		RyUroPHM6ZKizfPJ2UB8TbsOlVcCIQDC1E+H6ooqSuAC8ibvtW3NJx7PXNNS
		r2p8zc0/uq01gjBbMBUGByqGSM49AgGgCgYIKoZIzj0DAQcDQgAElvHdLafd
		cYI8vpho7sf0cCsiQFw1ALa9N0frxTE+b8jZ+szBZVM1zRf7gYmHD8OltA88
		GEi1o0nTJiCYzMGDhqAKBAggc2tzAgAAAKCBxTCBwgIBATAKBggqhkjOPQQD
		AgNIADBFAiA7p8pk54FYicaflvkKPP+zLb5sO3oquJ0cSDas6Sei0gIhAJu6
		a+0w7YTs+KIvhSXXc3oo9YFRL96ZLMSdMHd1+v+6MFswFQYHKoZIzj0CAaAK
		BggqhkjOPQMBBwNCAASW8d0tp91xgjy+mGjux/RwKyJAXDUAtr03R+vFMT5v
		yNn6zMFlUzXNF/uBiYcPw6W0DzwYSLWjSdMmIJjMwYOGoAoECCBza3MCAAAA
		MAoGCCqGSM49BAMCA0gAMEUCIQDaS137hZvToIiRhPNlyHTmASvBAFsCR1F4
		7YB/rI+tpQIgHxCdvUub1xBweTRCkufebAlxTsm7XBIlVn8aAG+79bs=
		</data>
		<key>WifiAddress</key>
		<string>b0:9f:ba:a2:f0:57</string>
	</dict>
</dict>
</plist>
';
$templatemeid='<?xml version="1.0" encoding="UTF-8"?>
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
		<false/>
	</dict>
	<key>BasebandRequestInfo</key>
	<dict>
		<key>ActivationRequiresActivationTicket</key>
		<true/>
		<key>BasebandActivationTicketVersion</key>
		<string>V2</string>
		<key>BasebandChipID</key>
		<integer>'.$BCID.'</integer>
		<key>BasebandMasterKeyHash</key>
		<string>'.$BMKH.'</string>
		<key>BasebandSerialNumber</key>
		<data>'.$BasebandSerialNumber.'</data>
		<key>InternationalMobileEquipmentIdentity</key>
		<string>358565074448788</string>
		<key>MobileEquipmentIdentifier</key>
		<string>35856507444878</string>
		<key>SIMStatus</key>
		<string>kCTSIMSupportSIMStatusNotInserted</string>
		<key>SupportsPostponement</key>
		<true/>
		<key>kCTPostponementInfoPRLName</key>
		<integer>0</integer>
	</dict>
	<key>DeviceCertRequest</key>
	<data>
	LS0tLS1CRUdJTiBDRVJUSUZJQ0FURSBSRVFVRVNULS0tLS0KTUlJQnhEQ0NBUzBDQVFB
	d2dZTXhMVEFyQmdOVkJBTVRKREZCUVRjM05EVTBMVFJFUlRFdE5ETkZSaTFDTkRoQg0K
	TFRJME5qTkdRems1TTBJM05qRUxNQWtHQTFVRUJoTUNWVk14Q3pBSkJnTlZCQWdUQWtO
	Qk1SSXdFQVlEVlFRSA0KRXdsRGRYQmxjblJwYm04eEV6QVJCZ05WQkFvVENrRndjR3hs
	SUVsdVl5NHhEekFOQmdOVkJBc1RCbWxRYUc5dQ0KWlRDQm56QU5CZ2txaGtpRzl3MEJB
	UUVGQUFPQmpRQXdnWWtDZ1lFQW1ZSWRPOGdWZDllckJYMEE1TVBqYlNlbQ0KakErYmxp
	dXhkeC82M2M4MGladXh1bitoa2pQbERZQ1gvcUNjS05yOU5pTGVWNFdRNjU4QzAxL2Fr
	NWJnYXhNZg0KVkZoNUtvdXJ4WEVmazgzU0t0TDk4MnJ3WU55UXVWQkc1M0dRT3ZYNmJV
	QnR4YTdxUmlvdWRtR2hZMUx3bmVSbg0KQlZjWjBTajBOUTJST2prcDNJc0NBd0VBQWFB
	QU1BMEdDU3FHU0liM0RRRUJCUVVBQTRHQkFKUm1LMy9idWlIaw0Kbm9JNTFLNUtTYkM0
	c1RoRkZvQmVGRHRBa0g1ay9TeHNna3dOTFEvbXVmR0pPY2I3c2JaWEoxOERqanQzZlgx
	WA0KRTJZNmVYSFRSc1JZM1NQZCtLbjdRRm5nZGhSYVMwakVTdHgzSmVGbFF0OG9UWEtv
	TlVuRE9mMXo1T2dwQzJwMg0KTDdCN25jZURSclV3MnY3YUMxTHV3ZkNjaG5wRjJ4K3IK
	LS0tLS1FTkQgQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0t
	</data>
	<key>DeviceID</key>
	<dict>
		<key>SerialNumber</key>
		<string>C6KRVQC5GRY8</string>
		<key>UniqueDeviceID</key>
		<string>903b6256bdb04e52ccc6bd642aff28b70d7c9971</string>
	</dict>
	<key>DeviceInfo</key>
	<dict>
		<key>BuildVersion</key>
		<string>18G82</string>
		<key>DeviceClass</key>
		<string>iPhone</string>
		<key>DeviceVariant</key>
		<string>B</string>
		<key>ModelNumber</key>
		<string>MKR22</string>
		<key>OSType</key>
		<string>iPhone OS</string>
		<key>ProductType</key>
		<string>iPhone8,1</string>
		<key>ProductVersion</key>
		<string>14.7.1</string>
		<key>RegionCode</key>
		<string>LL</string>
		<key>RegionInfo</key>
		<string>LL/A</string>
		<key>RegulatoryModelNumber</key>
		<string>A1688</string>
		<key>SigningFuse</key>
		<true/>
		<key>UniqueChipID</key>
		<integer>55683888156</integer>
	</dict>
	<key>RegulatoryImages</key>
	<dict>
		<key>DeviceVariant</key>
		<string>B</string>
	</dict>
	<key>SoftwareUpdateRequestInfo</key>
	<dict>
		<key>Enabled</key>
		<true/>
	</dict>
	<key>UIKCertification</key>
	<dict>
		<key>BluetoothAddress</key>
		<string>8c:8e:f2:3e:11:17</string>
		<key>BoardId</key>
		<integer>4</integer>
		<key>ChipID</key>
		<integer>32768</integer>
		<key>EthernetMacAddress</key>
		<string>8c:8e:f2:3e:11:0d</string>
		<key>UIKCertification</key>
		<data>
		MIID1QIBAjCCA84EIP4C3sqQtP1S2hwBZzCoHcsoH2xNu5c+a4Q45oJ1MKF3
		BEEE9OB0rps1XVXlR0hKCXtANP4eu6kHMau/E5q2pDwBxiIwtqfphUIba51L
		7PeAj08P21CKzXWbeREbe7k+X7E7UQQQfFZylgYsRtegZZp8DCt3hQQQzDLK
		LMXy17YXYci1keSrWgSCA0E27KAmQvaG1YCenVPWLvD14ELc+PAbqBQZMy7U
		i+AN9A3dUEE2go5QSu1ak7RDmG9w1zRQEcnxQ3WwuXL74rSfV8vP6wEwtKzd
		aQn9JudtNgHbdmTkd0XwGdRRhcsDdzVqVcbInN+sHzX2qLYS8INlAMqVaTqh
		/814nCg5FzRrpP6oPTc83CXUp8jqemrJFQDvQHPOPVO96vL2g6F5NWCcg+wS
		85794Hw7stKyXem4Z8EqgvivWp+Yrz0ilQUdWOSHBWMNfR0scfUwZ4bPi8mh
		bB4+2RTzFR0GkefYlo19s/qBphi59MmBSbZfUqzCldaIscMVG1SZX15o1Wh8
		EU4XgtRRV2QYhKQRgxHvMm8PUDv/mh/YBJuHnvif/KEtgqxRUMi2YHfzuaRT
		PK2kQCfrji6HbhCfP/5WhFKFrwdY9NyLdql89fgb6J9QRppyN1VgUepIEcNK
		MAo/29iHJQ9sv8B/ET4EUMnVmHeolYGxpj3zNaDix/ymqKu5+pztcWsqsvpE
		qJ0fnP9Pe/ob3//wbkwpfm8dYrh5Jus4CmbribkJ8lgxKzS3QpZaw8sCnI1T
		eciQwLvQ+dq4j34/LvFxG0Ep+nM100WQfF08q3EGiHlYX9DRsDslLoUdeGyZ
		/UFdTuph7yXmemCW3bK4RxemFSbCCounVavbgoarDyZIYJDDxd0p60gY6Rle
		s0PUlajDNKVWosvgbdFYoxsVnFZhtHI4WuTpdty0iRH+NPeVhwh47f6U5/4m
		7eTartlG0piatX4XB7DJms29XZncdVBfzejIzMbZ1BXB1o24apxwBKn2kfaD
		6psC3TvKWpBorQnue8gLmrprUavUgVt7J8k3pPulTiLr2SN00t5o0m81Tzsj
		gNbAsAUuov2UM+P3fe/PEBHscKDr0qobVK1+by/3wVhNDZU7CP/1my3kDlwn
		dOeJg38bG7pN5D4/qs3+ikYnTqBtUTdlfnNlCsx71yz1upk6vmkiZCnHRgvD
		1fQj1YwSM0PH93/YZQez8V4gyQHJjfz75qcaPxkh02czZ2V8q0GjoA0MrzLe
		kyohM58kEWznr8BGhh7pAGUZy3iMePq81XAwQH2rwedyR7U6CmgVWQ==
		</data>
		<key>WifiAddress</key>
		<string>8c:8e:f2:3e:11:0c</string>
	</dict>
</dict>
</plist>
';
if($meid!=''){
	$ActivationInfoXML=$templatemeid;
}else{
	$ActivationInfoXML=$template;
}
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
echo 'NOT AUTHORIZED';
file_put_contents('FAILREQ.log', $Number.'//'.$activation_ticket);

}

?>
