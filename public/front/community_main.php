<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
/*
$code=$_REQUEST["code"];
if(ord($code)) {
	$sql = "SELECT * FROM tbldesignnewpage WHERE type='community' AND code='{$code}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$isnew=true;
		$newobj = new stdClass;
		$newobj->subject=$row->subject;
		$newobj->menu_type=$row->leftmenu;
		$filename=explode("",$row->filename);
		$newobj->member_type=$filename[0];
		$newobj->menu_code=$filename[1];
		$newobj->body=$row->body;
		$newobj->body=str_replace("[DIR]",$Dir,$newobj->body);
		if(strlen($newobj->member_type)>1) {
			$newobj->group_code=$newobj->member_type;
			$newobj->member_type="G";
		}
	}
	pmysql_free_result($result);
}
if($isnew!=true) {
	alert_go('해당 페이지가 존재하지 않습니다.',-1);
}

if($newobj->member_type=="Y") {
	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
		exit;
	}
} else if($newobj->member_type=="G") {
	if(strlen($_ShopInfo->getMemid())==0 || $newobj->group_code!=$_ShopInfo->getMemgroup()) {
		if(strlen($_ShopInfo->getMemid())==0) {
			Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
			exit;
		} else {
			alert_go('해당 페이지 접근권한이 없습니다.',$Dir.MainDir."main.php");
		}
	}
}
*/
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
//-->
</SCRIPT>
</HEAD>

<?php include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<link rel="stylesheet" type="text/css" href="../../css/tem_001.css" media="all" />
<!-- 상세페이지 -->
<div class="main_wrap">
	<div class="container">
		
		<!-- LNB -->
		<div class="left_lnb">

			<div class="lnb_wrap">
				<div class="lnb">
					<h1>커뮤니티</h1>
					<ul>
						<li><a href="#">타입1</a></li>
						<li><a href="#">타입2</a></li>
						<li><a href="#">타입3</a></li>
						<li><a href="#">타입4</a></li>
					</ul>
				</div>
			</div>

		</div>
		<!-- #LNB -->

		<div class="right_section">

			<div class="right_article_wrap">
				<div class="right_article">
					<h1>타입1</h1>
					<div>게시판 출력</div>
				</div>
			</div>

		</div>

	</div>
</div>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
