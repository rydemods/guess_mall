<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/nexolve_member4-2.xls');

$ecnt=0;


for ($i = 2; $i <= 100; $i++) 
{

	$ch_sel_qry="select * from tblmember where m_no='".$data->sheets[0]['cells'][$i][1]."'";
	$ch_sel_result=pmysql_query($ch_sel_qry);
	$ch_sel_num=pmysql_num_rows($ch_sel_result);

	if(!$ch_sel_num){
		$checknum = explode("-" , $data->sheets[0]['cells'][$i][19]);
		$mobilenum = $checknum[2];
		$passwd = $data->sheets[0]['cells'][$i][2].$mobilenum;
		$passwd = md5($passwd);

		if($data->sheets[0]['cells'][$i][26]=="y" && $data->sheets[0]['cells'][$i][27]=="y") {
			$news_yn="Y";
		} else if($data->sheets[0]['cells'][$i][26]=="y") {
			$news_yn="M";
		} else if($data->sheets[0]['cells'][$i][27]=="y") {
			$news_yn="S";
		} else {
			$news_yn="N";
		}

		if($data->sheets[0]['cells'][$i][10] == 'm'){
			$gender = '1';
		}else{
			$gender = '2';
		}

		$home_post = str_replace("-","",$data->sheets[0]['cells'][$i][15]);
		$home_addr = $data->sheets[0]['cells'][$i][16]."=".$data->sheets[0]['cells'][$i][17];

		if($data->sheets[0]['cells'][$i][33]=="0000-00-00 00:00:00"){
			$logindate = "19700101000000";
		}else{
			$logindatea = explode(" " , $data->sheets[0]['cells'][$i][33]);
			$logindateb = explode("/",$logindatea[0]);
			$logindatec = explode(":",$logindatea[1]);
			$logindate = $logindateb[2].$logindateb[1].$logindateb[0].$logindatec[0].$logindatec[1]."00";
		}

		if($data->sheets[0]['cells'][$i][32]=="0000-00-00 00:00:00"){
			$date = "19700101000000";
		}else{
			$datea = explode(" " , $data->sheets[0]['cells'][$i][32]);
			$dateb = explode("/",$datea[0]);
			$datec = explode(":",$datea[1]);
			$date = $dateb[2].$dateb[1].$dateb[0].$datec[0].$datec[1]."00";
		}

		if($data->sheets[0]['cells'][$i][11] && $data->sheets[0]['cells'][$i][12]){
			if(strlen($data->sheets[0]['cells'][$i][12]) == 2){
				$birth = $data->sheets[0]['cells'][$i][11]."-0".substr($data->sheets[0]['cells'][$i][12],0,1)."-0".substr($data->sheets[0]['cells'][$i][12],1,1);
			}elseif(strlen($data->sheets[0]['cells'][$i][12]) == 3){
				$birth = $data->sheets[0]['cells'][$i][11]."-0".substr($data->sheets[0]['cells'][$i][12],0,1)."-".substr($data->sheets[0]['cells'][$i][12],1,2);
			}elseif(strlen($data->sheets[0]['cells'][$i][12]) == 4){
				$birth = $data->sheets[0]['cells'][$i][11]."-".substr($data->sheets[0]['cells'][$i][12],0,2)."-".substr($data->sheets[0]['cells'][$i][12],2,2);
			}else{
				$birth = "1970-01-01";
			}
		}
		
		$qry="insert into tblmember(
		id,
		passwd,
		name,
		resno,
		email,
		mobile,
		news_yn,
		gender,
		married_yn,
		job,
		birth,
		lunar,
		home_post,
		home_addr,
		home_tel,
		office_post,
		office_addr,
		office_tel,
		memo,
		reserve,
		joinip,
		ip,
		logindate,
		logincnt,
		date,
		confirm_yn,
		rec_id,
		group_code,
		member_out,
		nickname,
		mem_type,
		m_no,
		interest,
		mem_wholesale,
		sumprice,
		random_price
		)values(
		'".$data->sheets[0]['cells'][$i][2]."',
		'".$passwd."',
		'".$data->sheets[0]['cells'][$i][4]."',
		'',
		'".$data->sheets[0]['cells'][$i][14]."',
		'".$data->sheets[0]['cells'][$i][19]."',
		'".$news_yn."',
		'".$gender."',
		'".$data->sheets[0]['cells'][$i][28]."',
		'".$data->sheets[0]['cells'][$i][30]."',
		'".$birth."',
		'1',
		'".$home_post."',
		'".$home_addr."',
		'".$data->sheets[0]['cells'][$i][18]."',
		'',
		'',
		'',
		'',
		'".$data->sheets[0]['cells'][$i][25]."',
		'".$data->sheets[0]['cells'][$i][34]."',
		'".$data->sheets[0]['cells'][$i][34]."',
		'".$logindate."',
		'".$data->sheets[0]['cells'][$i][36]."',
		'".$date."',
		'Y',
		'',
		'0003',
		'N',
		'',
		0,
		'".$data->sheets[0]['cells'][$i][1]."',
		0,
		'0',
		'".$data->sheets[0]['cells'][$i][38]."',
		0
		)";
		
		//pmysql_query($qry);

echo $qry;
echo "<br/>";

	}
}//for

?>