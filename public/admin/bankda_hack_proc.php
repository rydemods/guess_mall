<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");


	$bankdaUrl = "https://ssl.bankda.com/partnership/user/user_withdraw.php?".http_build_query($_POST);
	$rs = file_get_contents($bankdaUrl);

	if(trim($rs)=="ok"){
		unlink($_SERVER[DOCUMENT_ROOT]."/data/BankdaConfig.php");
		echo 'Ok';
	}else{
		//echo iconv("euc-kr", "utf-8", $rs);
		echo $rs;
	}
?>