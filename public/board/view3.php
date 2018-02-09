<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$num=$_GET["num"];
$gotopage = $_REQUEST["gotopage"];
//exdebug($gotopage);
$block = $_REQUEST["block"];

#####좌측 메뉴 class='on' 을 위한 페이지코드
$page_code='celeb';

$sql = "SELECT * FROM tblproductreview WHERE num='{$num}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_pdata=$row;
	$date = date("Y/m/d",$_pdata->writetime);

} else {
	alert_go('해당 게시물이 없습니다.','c');
}

pmysql_free_result($result);
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - TREND SETTER</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}

//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<tr>
	<td>
	<div class="main_wrap">
		<div class="customer_wrap">

	<!-- LNB -->
	
	<?php include ("{$Dir}board/top_lnb.php");?>
	
	<!-- #LNB -->
	<div class="content_area">
		<div class="line_map_r">홈 &gt; board &gt; <strong>TREND SETTER</strong></div>
	
		<div class="customer_inquiry_wrap">
			<table class="write_table" summary="">
				<caption>TREND SETTER</caption>
				<colgroup>
					<col style="width:121px" />
					<col style="width:auto" />
				</colgroup>
				<tbody>
					<tr height="40">
						<th scope="row">제목</th>
						<td>
							<?=$_pdata->title?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">날짜</th>
						<td>						
							<?=$date?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">내용</th>
						<td>
							<? if($_pdata->vfilename){ ?>
								<img src="<?=$Dir.DataDir."shopimages/board/stargallary/".$_pdata->vfilename ?>">
							<? } ?><br />
							<?=nl2br($_pdata->content)?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div>&nbsp;</div>
		<div align="center">
			<form name=form2 method=post action="/front/celeb.php">
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<input type="image" src="../img/button/customer_notice_view_list_btn.gif" onclick="submit">
			</form>
	</div>
	
	
</table>
		
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
		