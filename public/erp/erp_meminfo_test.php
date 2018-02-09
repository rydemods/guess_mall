<?
/*
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
*/
// 탈퇴시
/*
$data	= array(
			'MEMBERNO' => '1000153', 
			'MEMBER_OUT' => 'Y'
);
*/


// 수정시
$data	= array(
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
);

$json = json_encode($data);
//print_r($json);

$url = 'http://test-hott.ajashop.co.kr/erp/erp_meminfo.php';

$response =curl($url, $json);
$res = json_decode($response);	
//print_r($response);
print_r($res);
//print_r($res->APPR_YN);
//print_r($res->APPR_REJECT_REASON);

function curl($url,$data_string=""){
	$ch = curl_init($url);                                                                
	curl_setopt($ch, CURLOPT_POST, 1);                                                           
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    /*
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($data_string))                                                                       
	);                
    */
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
	curl_setopt($ch ,CURLOPT_HTTPHEADER,array ("Content-Type: application/x-www-form-urlencoded; charset=utf-8"));

	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
} 

?>