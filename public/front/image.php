<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 공지사항</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<div id = "testsetests" style = "height:100px;width:100px;border:1px solid red;"></div>
<div id = "procDiv"></div>




<script>
	$(document).ready(function(){
		$.ajax({ 
			type: "POST", 
			url: "./image_proc.php", 
			dataType:"html",
			success: function(result) {
				$("#procDiv").html(result);
				alert('페이지 로드!');
				

				$('.loadImage').bind('load', function(){
					$("#testsetests").html($("body").height());
				});

			},error: function(result) {
				alert("에러가 발생하였습니다."); 
			}
		}); 
	})
</script>


















<div><a href="customer_notice.php">고객센터 공지사항</a></div>
<a href="customer_faq.php">고객센터 faq</a>
<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
