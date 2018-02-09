<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 페이퍼 쿠폰 발행</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery.js"></script>
<?php include($Dir."lib/style.php");?>


<script LANGUAGE="JavaScript">
<!--
	$(document).ready(function(){
		$(".submitPaper").click(function(){
			$.post("mypage_paper.ajax.php",{mode:"paper",papercode:$(this).prev().val()},function(data){
				if(data == '1' || data == '4'){
					alert("쿠폰이 발급 되었습니다.");
				}else if(data == '2'){
					alert("이미 사용한 쿠폰 번호입니다.");
				}else if(data == '3'){
					alert("해당하는 쿠폰이 없습니다.");
				}else if(data == '5'){
					alert("같은 쿠폰의 사용하지 않은 쿠폰이 존재 합니다.");
				}
			});
		})
	})
-->
</script>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<div style = 'margin:0 auto;text-align:center;padding-top:40px;'>
	페이퍼 쿠폰 발급 테스트 : <input type = 'text' size = '20' name = 'papercode'>&nbsp;&nbsp;<a href = "javascript:;" class = "submitPaper">[확인]</a>
</div>


</HTML>
