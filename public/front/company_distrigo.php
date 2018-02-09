<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$sql = "SELECT * FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
if($crow=pmysql_fetch_object($result)) {
	
} else {
	$crow->introtype="C";
}
pmysql_free_result($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회사소개</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<div id="container">
	<?include "side_nav_about.php";?>
	<div class="contents_withleft">
		<div class="title">
			<h2><img src="/image/company/title_company.gif" alt="커뮤니티" /></h2>
			<div class="path">
				<ul>
					<li class="home">홈&nbsp;&gt;&nbsp;</li>
					<li>회사소개&nbsp;&gt;&nbsp;</li>
					<li>물류센터 오시는 길</li>
				</ul>
			</div>
		</div>
		<div class="company">
			<ul>
				<li><img src="/image/cp_08_1.jpg" alt="커뮤니티" /></li>
			</ul>
		</div>
	</div>	<!-- contents_withleft 끝 -->
</div><!-- //container 끝 -->


<div class="clearboth"></div>



<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
