<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");


	$partnerName = iconv("utf-8", "euc-kr", $_POST["partnerName"]);
	$partnerNum = iconv("utf-8", "euc-kr", $_POST["partnerNum"]);
	$level = iconv("utf-8", "euc-kr", $_POST["level"]);
	$cusType = iconv("utf-8", "euc-kr", $_POST["cusType"]);
	$cusId = iconv("utf-8", "euc-kr", $_POST["cusId"]);
	$password = md5(iconv("utf-8", "euc-kr", $_POST["password"]));
	$passwordConfirm = md5(iconv("utf-8", "euc-kr", $_POST["passwordConfirm"]));
	$ssn = iconv("utf-8", "euc-kr", $_POST["ssn"]);
	$name = iconv("utf-8", "euc-kr", $_POST["name"]);
	$email = iconv("utf-8", "euc-kr", $_POST["email"]);
	$domain = iconv("utf-8", "euc-kr", $_POST["domain"]);
	$siteName = iconv("utf-8", "euc-kr", $_POST["siteName"]);
	$contactName = iconv("utf-8", "euc-kr", $_POST["contactName"]);
	$contactRole = iconv("utf-8", "euc-kr", $_POST["contactRole"]);
	$phone = iconv("utf-8", "euc-kr", $_POST["phone"]);
	$fax = iconv("utf-8", "euc-kr", $_POST["fax"]);
	$mobile = iconv("utf-8", "euc-kr", $_POST["mobile"]);
	$zipCode = iconv("utf-8", "euc-kr", $_POST["zipCode"]);
	$addr1 = iconv("utf-8", "euc-kr", $_POST["addr1"]);
	$addr2 = iconv("utf-8", "euc-kr", $_POST["addr2"]);

	$bizType = iconv("utf-8", "euc-kr", $_POST["bizType"]);
	$bizName = iconv("utf-8", "euc-kr", $_POST["bizName"]);
	$ceoName = iconv("utf-8", "euc-kr", $_POST["ceoName"]);

	$returnURL = iconv("utf-8", "euc-kr", $_POST["returnURL"]);
	$errorURL = iconv("utf-8", "euc-kr", $_POST["errorURL"]);

	$sql = "INSERT 
				INTO 
				tblbizspring 
				(
					partnername , 
					partnernum , 
					level , 
					custype , 
					cusid , 
					password , 
					passwordconfirm , 
					ssn , 
					name , 
					email , 
					domain , 
					sitename , 
					contactname , 
					contactrole , 
					phone , 
					fax , 
					mobile , 
					zipcode , 
					addr1 , 
					addr2 , 
					biztype , 
					bizname , 
					ceoname , 
					returnurl , 
					errorurl
				) 
				VALUES 
				(
					'".$partnerName."',
					'".$partnerNum."',
					'".$level."',
					'".$cusType."',
					'".$cusId."',
					'".$password."',
					'".$passwordConfirm."',
					'".$ssn."',
					'".$name."',
					'".$email."',
					'".$domain."',
					'".$siteName."',
					'".$contactName."',
					'".$contactRole."',
					'".$phone."',
					'".$fax."',
					'".$mobile."',
					'".$zipCode."',
					'".$addr1."',
					'".$addr2."',
					'".$bizType."',
					'".$bizName."',
					'".$ceoName."', 
					'".$returnURL."',
					'".$errorURL."'
				)";
/*
	$f = fopen($_SERVER[DOCUMENT_ROOT]."/data/bizConfig2.php","w");
	fwrite($f,"<?");
	fwrite($f,"\$biz = array('bizNumber'=>'', 'bizId'=>'".$cusId."', 'bizPassword'=>'');");
	fwrite($f,"?>");
	fclose($f);
	chmod($_SERVER[DOCUMENT_ROOT]."/data/bizConfig2.php",0707);
*/
	if($insert = pmysql_query($sql, get_db_conn())){
		echo 'Ok';
	}else{
		echo "Err";
	}
?>