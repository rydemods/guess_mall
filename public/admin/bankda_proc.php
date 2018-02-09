<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");


	$bankdaUrl = "https://ssl.bankda.com/partnership/user/user_join_prs.php?".http_build_query($_POST);
	$rs = file_get_contents($bankdaUrl);

	if(trim($rs)=="ok"){
		/*
		$user_name = iconv("utf-8", "euc-kr", $_POST["user_name"]);
		$user_id = iconv("utf-8", "euc-kr", $_POST["user_id"]);
		$user_pw = iconv("utf-8", "euc-kr", $_POST["user_pw"]);
		*/
		$user_name = $_POST["user_name"];
		$user_id = $_POST["user_id"];
		$user_pw = $_POST["user_pw"];

		$f = fopen($_SERVER[DOCUMENT_ROOT]."/data/BankdaConfig.php","w");
		fwrite($f,"<?");
		fwrite($f,"\$cfgBankda = array('user_name'=>'".$user_name."', 'user_id'=>'".$user_id."', 'user_pw'=>'".$user_pw."');");
		fwrite($f,"?>");
		fclose($f);
		chmod($_SERVER[DOCUMENT_ROOT]."/data/BankdaConfig.php",0707);

		include_once($Dir."data/BankdaConfig.php");
		if($cfgBankda[user_id]){
			echo 'Ok';
		}else{
			echo "Err";
		}
	}else{
		echo $rs;
	}
?>