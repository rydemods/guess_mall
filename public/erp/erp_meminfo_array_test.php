<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$data	= array(
			'memlist'=>array(
				array(
					'MEMBERNO' => '1000014', 
					'MEMBERNAME' => '김재수',
					'EMAIL' => 'jaisu2001@nate.com',
					'MOBILE' => '010-2688-4815',
					'WEBID' => 'jaisu2001@nate.com',
					'NEWS_YN' => 'M',
					'GENDER' => '1',
					'BIRTH' => '821010',
					'HOME_POST' => '06108',
					'HOME_ADDR' => '서울 강남구 논현로114길 5',
					'HOME_ADDR2' => '4~5층',
					'HOME_TEL' => '02-3448-0914',
					'GROUPCODE' => '0001',
					'MEMBER_OUT' => 'N',
					'STAFF_YN' => 'N',
					'STAFFCARDNO' => '',
					'MEMO' => '메모'
				), 
				array(
					'MEMBERNO' => '1000021', 
					'MEMBERNAME' => 'Jeongho,Jeong#Takumi',
					'EMAIL' => 'ikazeus@gmail.com',
					'MOBILE' => '010-4121-2734',
					'WEBID' => 'ikazeus@gmail.com',
					'NEWS_YN' => 'Y',
					'GENDER' => '',
					'BIRTH' => '',
					'HOME_POST' => '06108',
					'HOME_ADDR' => '서울 강남구 논현로114길 5',
					'HOME_ADDR2' => '4~5층',
					'HOME_TEL' => '02-3448-0913',
					'GROUPCODE' => '0005',
					'MEMBER_OUT' => 'N',
					'STAFF_YN' => 'Y',
					'STAFFCARDNO' => '10022',
					'MEMO' => '메모'
				)
			)
);

$json = json_encode($data);
//exdebug($json);

$url = 'http://test-hott.ajashop.co.kr/erp/erp_meminfo_array.php';

$response =curl($url, $json);
$res = json_decode($response);	
exdebug($res);

function curl($url,$data_string=""){
	$ch = curl_init($url);                                                                
	curl_setopt($ch, CURLOPT_POST, 1);                                                           
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($data_string))                                                                       
	);                
	$result = curl_exec($ch);
	//print_r($result);
	return $result;
	curl_close($ch);
} 

?>