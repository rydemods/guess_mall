<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$likecode = $_POST["code"];
$search_word = $_POST["search_word"];
$search_select = $_POST["search_select"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - default</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function select_code(code1) {
	var code2 = code1
	document.form1.code.value="00400"+code2;
	if(code2==null){
		document.form1.code.value="004";
	}
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
//-->
</SCRIPT>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<?include ($Dir.MainDir.$_data->menu_type.".php");
$page_code = "greeting";
/* lnb 호출 */
$lnb_flag = 3;
include ($Dir.MainDir."lnb.php");?>

	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<!-- default -->
	
		<div class="right_section">
			
			<div class="sub_title">
				<h3 class="def"><span class="kr">인사말</span></h3>
			</div>

			<div>떼르벨 인사말이 들어갈 예정입니다.</div>

		</div>
	
	
	
	
	<!-- default -->
		<input type=hidden name=code value="<?=$likecode?>">
	</form>

<div class="page"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></div>
	
<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=code value="<?=$likecode?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>
<script>
$(function() {
	var brandcode = "logo0"+document.form1.code.value.substr(5,1);
	document.getElementById(brandcode).className = document.getElementById(brandcode).className+" on";
});
</script>
</BODY>
</HTML>
