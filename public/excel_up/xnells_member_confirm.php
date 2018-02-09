<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/xnells_member_confirm_20141230.xls');

$ecnt=0;

$rows = $data->sheets[0]['numRows'];
//exdebug($rows);
for ($i = 1; $i <= $data->sheets[0]['numCols']; $i++) {
	echo $i."번 : ".$data->sheets[0]['cells'][3][$i]." | ";
}

/*
1번 : -아이디 [id]| 2번 : 회원등급 [group_code]| 3번 : -성명 [name]| 
4번 : -성별 [gender]1남,2여| 5번 : -나이 [age]| 6번 : -적립금 [reserve]| 
7번 : -접속 [logincnt]| 8번 : -최근접속일 [logindate]20141013173151| 9번 : -가입일 [date]20141223184338| 
10번 : 구매거부 ??| 11번 : -총구매금액 [accrue]| 12번 : -생일 [birth]2014-12-25| 
13번 : -이메일 [email]| 14번 : -전화번호 [home_tel]02-0000-0000| 15번 : -휴대폰 [mobile]000-0000-0000| 
16번 : -주소 [home_post],[home_addr]↑=↑구분자 있음 |
*/
//insert excel
$groupArray = array("VIP사업자회원(도매)"=>"0004","마니아회원"=>"0007");
for($i=2;$i<=$rows;$i++){
//for($i=82;$i<83;$i++){
	//나이 $data->sheets[0]['cells'][$i][5]
	// echo "접속 ".$data->sheets[0]['cells'][$i][7];	echo "  |  ";	
	//echo "총구매금액 ".$data->sheets[0]['cells'][$i][11];	echo "  |  "; 컬럼추가
	//echo "생일 ".$data->sheets[0]['cells'][$i][12];	echo "  |  ";
	//echo "이메일 ".$data->sheets[0]['cells'][$i][13];	echo "  |  ";
	//echo "전화번호 ".$data->sheets[0]['cells'][$i][14];	echo "  |  ";
	//echo "휴대폰 ".$data->sheets[0]['cells'][$i][15];	echo "  |  ";
	
	
	//가입일
	$date_ymd = str_replace("-","",$data->sheets[0]['cells'][$i][11]);
	$date = $date_ymd."000000";
	//암호
	$passwd = md5($data->sheets[0]['cells'][$i][1]."1234");
	//주소
	$homePost_start = strpos($data->sheets[0]['cells'][$i][18],"[");
	$homePost_end = strpos($data->sheets[0]['cells'][$i][18],"]");
	$home_post = str_replace("-","",substr($data->sheets[0]['cells'][$i][18],$homePost_start+1,7));

	$homeAdder_array = explode(" ",substr($data->sheets[0]['cells'][$i][18],$homePost_end+1));
	$home_adder = "";
	foreach($homeAdder_array as $k=>$v){
		if($k == 4){
			$home_adder.="=";
		}
			$home_adder.=$v." ";
	}
	//성별
	if($data->sheets[0]['cells'][$i][6] == "남"){
		$gender = "1";
	}else if($data->sheets[0]['cells'][$i][6] == "여"){
		$gender = "2";
	}else{
		$gender = "1";
	}
	//적립금
	//exdebug($data->sheets[0]['cells'][$i][8]);
	$priceArray = array("원",",");
	$reserve = str_replace($priceArray,"",$data->sheets[0]['cells'][$i][8]);
	//누적금
	$sumprice = str_replace($priceArray,"",$data->sheets[0]['cells'][$i][13]);
	//최근접속일
	$replaceArray = array(":","-"," ");
	$logindate = str_replace($replaceArray,"",$data->sheets[0]['cells'][$i][10]);
	
	$sql = "
		INSERT INTO tblmember 
		(
			id,
			passwd,
			name,
			gender,
			age,
			reserve,
			email,
			mobile,
			home_post,
			home_addr,
			home_tel,
			date,
			birth,
			logindate,
			logincnt,
			sumprice,
			group_code,
			office_post,
			office_addr,
			office_tel,
			confirm_yn,
			mem_type,
			nickname,
			office_name,
			office_representative,
			office_form
		)
		VALUES
		(
			'".$data->sheets[0]['cells'][$i][1]."',
			'".$passwd."',
			'".$data->sheets[0]['cells'][$i][5]."',
			'".$gender."',
			'".$data->sheets[0]['cells'][$i][7]."',
			'".$reserve."',
			'".$data->sheets[0]['cells'][$i][15]."',
			'".$data->sheets[0]['cells'][$i][17]."',
			'".$home_post."',
			'".$home_adder."',
			'".$data->sheets[0]['cells'][$i][16]."',
			'".$date."',
			'".$data->sheets[0]['cells'][$i][14]."',
			'".$logindate."',
			'".$data->sheets[0]['cells'][$i][9]."',
			'".$sumprice."',
			'0004',
			'".$home_post."',
			'".$home_adder."',
			'".$data->sheets[0]['cells'][$i][16]."',
			'Y',
			1,
			'',
			'".pmysql_escape_string($data->sheets[0]['cells'][$i][4])."',
			'".$data->sheets[0]['cells'][$i][5]."',
			'".$data->sheets[0]['cells'][$i][3]."'
		)
		";
		//exdebug($sql);
		pmysql_query($sql);
		if(pmysql_error()){
			exdebug($i."번 | ".$data->sheets[0]['cells'][$i][1]." 아이디 error");
		}else{
			exdebug($i."번 | ".$data->sheets[0]['cells'][$i][1]." 아이디 입력");
		}
		
		//exdebug($sql);
		//echo $i;
		echo "<br>";
}//for

?>