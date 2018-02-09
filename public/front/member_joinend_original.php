<?php
session_start();
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
<TITLE><?=$_data->shoptitle?> - 회원가입</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>

</HEAD>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
</form>
</table>
<?
if($_data->icon_type=="tem_001"){
	
?>












<style type="text/css">
.wellcome{width:1000px;text-align:center;font:bold 24px MalgunGothic; color:#75b294;}
.wellcome li:first-child{margin:50px 400px;}
.wellcome li{margin:20px 0;}
</style>


<!-- start container -->
<div id="container">
	<!-- start contents -->
	<div class="contents">
	
		<div class="title">
			<h2><img src="/image/join/join_complete_title.gif" alt="가입완료" /></h2>
			<div class="path">
				<ul>
					<li class="home">홈&nbsp;&gt;&nbsp;</li>
					<li>가입완료</li>
				</ul>
			</div>
		</div>
	
		<div class="joinstep">
			<img src="/image/join/join_step3.gif" />
		</div>
		<div class="welcome">	
			<a href="/front/login.php"><img src="/image/join/join_welcome.jpg" /></a>
		</div>
	</div><!-- //end contents -->
</div><!-- //end container -->


















<?}else{?>

<div class="w690">

	<h1 class="type01"><img src="../images/001/h1_member_join.gif" alt="회원가입" /></h1>
	
	<p align=center><img src="../images/common/member_joinok.gif" alt="" /></p>
	<div class="table_style">
		<h2>※가입정보</h2>
		<form>
		<table width=100% cellpadding=0 cellspacing=0 border=0 >
			<colgroup>
				<col width="15%" /><col width="" /><col width="15%" /><col width="" />
			</colgroup>
			<tr>
				<th>이름</th>
				<td><?=$_GET['name']?></td>
				<th>아이디</th>
				<td><?=$_GET['id']?></td>
			</tr>
		</table>
		</form>
		<p class="btn_c">
			<a href="../index.php"><img src="../images/001/btn_main01.gif" alt="메인으로" /></a>
		</p>
	</div>



</div>


<?}?>

<script type="text/javascript">
	var _jn = "join";
	var _jjn = "<?=$_GET['id']?>";
</script>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
