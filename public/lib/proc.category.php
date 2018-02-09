<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	$returnData = smartSearchCategory($_GET[category], $_GET[step]);
	$option_selected = "";

	$categoryStr = "<option value=''> ".$_GET[step]."차 카테고리 </option>";
	if(count($returnData) > 0){
		foreach($returnData as $v){
			if($_GET[category]==$v['category']) $option_selected = "selected";
			else $option_selected = "";
			$categoryStr .= "<option value='".$v['category']."' ".$option_selected.">".$v['code_name']."</option>";
		}
	}
	echo iconv("EUC-KR", "UTF-8", $categoryStr);
?>