<?php

echo color('blue', "[+]")." Shell Account Creator - By: GidhanB.A\n";
echo color('blue', "[+]")." Input Reff: ";
$reff = trim(fgets(STDIN));

while (1) {
    Start:
    echo color('blue', "\n[+]")." Input Nomer: ";
    $hp = trim(fgets(STDIN));
    if ($hp[0].$hp[1] !== "62") $hp = "62".substr($hp, 1);

    Start2:
    $base = gendata("dropjar.com");
    $email = $base->email;
    $fname = $base->firstname;
    $lname = $base->lastname;

    $device = random(22,3);
    $headers = array();
    $headers[] = 'User-Agent: okhttp/3.14.9';
    $headers[] = 'Content-Type: application/json; charset=UTF-8';
    $headers[] = 'cap_brand: SHELLINDONESIALIVE';
    $headers[] = 'cap_device_id: '.$device;
    $headers[] = 'cap_mobile: ';

    $reg = curl('https://apac2-auth-api.capillarytech.com/auth/v1/token/generate', '{"brand":"SHELLINDONESIALIVE","deviceId":"'.$device.'","mobile":"'.$hp.'"}', $headers);
    if (strpos($reg[1], 'SUCCESS')) {
        $regdata = json_decode($reg[1]);
        $sess = $regdata->user->sessionId;
        $req = curl('https://apac2-auth-api.capillarytech.com/auth/v1/otp/generate', '{"brand":"SHELLINDONESIALIVE","deviceId":"'.$device.'","mobile":"'.$hp.'","mobile_temp":"+62 '.substr($hp, 1).'","sessionId":"'.$sess.'"}', $headers);
        if (strpos($req[1], 'SUCCESS')) {
            echo color('blue', "[+]")." Input OTP: ";
            $otp = trim(fgets(STDIN));
            $val = curl('https://apac2-auth-api.capillarytech.com/auth/v1/otp/validate', '{"brand":"SHELLINDONESIALIVE","deviceId":"'.$device.'","mobile":"'.$hp.'","mobile_temp":"+62 '.substr($hp, 1).'","otp":"'.$otp.'","sessionId":"'.$sess.'"}', $headers);
            if (strpos($val[1], '"success":true')) {
                $valdata = json_decode($val[1]);
                $token = $valdata->auth->token;
                array_pop($headers);
                $headers[] = 'cap_mobile: '.$hp;
                $headers[] = 'cap_authorization: '.$token;
                $gas = curl('https://apac2-auth-api.capillarytech.com/mobile/v2/api/v2/customers', '{"extendedFields":{"acquisition_channel":"mobileApp","dob":"2000/12/12","verification_status":"false"},"loyaltyInfo":{"loyaltyType":"loyalty"},"profiles":[{"fields":{"app_privacy_policy":"1","goplus_tnc":"1","onboarding":"pending"},"firstName":"'.$fname.'","identifiers":[{"type":"mobile","value":"62831'.random(8,0).'"},{"type":"email","value":"'.$email.'"}],"lastName":"'.$lname.'"}],"referralCode":"'.$reff.'","statusLabel":"Active","statusLabelReason":"App Registration"}', $headers);
                if (strpos($gas[1], 'createdId')) {
                    echo color('green', "[+]")." Register successfuly!\n";
                    goto Start2;
                } else {
                    echo color('red', "[+]")." $gas[1]\n";
                }
            } else {
                echo color('red', "[+]")." Error #3: $val[1]\n";
            }
        } else {
            echo color('red', "[+]")." Error #2: $req[1]\n";
            goto Start;
        }
    } else {
        echo color('red', "[+]")." Error #1: $reg[1]\n";
    }
}

function gendata($domain = "sitik.site")
    {
        $data = json_decode(file_get_contents("https://swappery.site/data.php?qty=1&domain=".$domain))->result[0];
        return $data;
    }

function curl($url, $post, $headers, $follow = false, $method = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if ($follow == true) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		if ($method !== null) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if ($headers !== null) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if ($post !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($ch);
		$header = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		$body = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
		$cookies = array();
		foreach ($matches[1] as $item) {
			parse_str($item, $cookie);
			$cookies = array_merge($cookies, $cookie);
		}
		return array(
			$header,
			$body,
			$cookies
		);
	}

function random($length, $a)
	{
		$str = "";
		if ($a == 0) {
			$characters = array_merge(range('0', '9'));
		} elseif ($a == 1) {
			$characters = array_merge(range('a', 'z'));
		} elseif ($a == 2) {
			$characters = array_merge(range('A', 'Z'));
		} elseif ($a == 3) {
			$characters = array_merge(range('0', '9'), range('a', 'z'));
		} elseif ($a == 4) {
			$characters = array_merge(range('0', '9'), range('A', 'Z'));
		} elseif ($a == 5) {
			$characters = array_merge(range('a', 'z'), range('A', 'Z'));
		} elseif ($a == 6) {
			$characters = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'));
		}
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}
		return $str;
	}

function color($color, $text)
    {
        $arrayColor = array(
            'grey'      => '1;30',
            'red'       => '1;31',
            'green'     => '1;32',
            'yellow'    => '1;33',
            'blue'      => '1;34',
            'purple'    => '1;35',
            'nevy'      => '1;36',
            'white'     => '1;0',
        );  
        return "\033[".$arrayColor[$color]."m".$text."\033[0m";
    }
