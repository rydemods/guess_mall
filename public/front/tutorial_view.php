<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$num=$_GET["num"];

#####좌측 메뉴 class='on' 을 위한 페이지코드
$page_code='review';

$sql = "SELECT * FROM tblproduct_tutorial WHERE tutoidx='{$num}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_pdata=$row;
} else {
	alert_go('해당 튜토리얼이 없습니다.','c');
}

pmysql_free_result($result);
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 상품평</TITLE>
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
		<div class="container1100 sub_skin">

	<!-- LNB -->
	
	<?php //include ("{$Dir}board/top_lnb.php");
			$lnb_flag = 5;
			include ($Dir.MainDir."lnb.php");
	?>
	
	<!-- #LNB -->
	<div class="right_section">
		<h3 class="title">튜토리얼</h3>

		<div class="customer_notice_wrap">
			<table class="write_table" summary="">
				<colgroup>
					<col style="width:121px" />
					<col style="width:auto" />
				</colgroup>
				<tbody>
					<tr height="40" style="text-align: center;>
						<th scope="row">상품명</th>
						<td>
						<? $sql2 = "SELECT productname FROM tblproduct WHERE productcode='".$_pdata->prcode."'";
							$result2=pmysql_query($sql2,get_db_conn());
							$row2=pmysql_fetch_object($result2);
							echo $row2->productname;
							?>
						</td>
					</tr>
					<tr height="40" ">
						<!--<th scope="row">내용</th>-->
						<td id="fSize" colspan="2" >
<?
						$tuto_content = stripslashes($_pdata->tutorial_ex);
						//exdebug($_pdata_content); 
						if(strlen($detail_filter)>0) {
							$tuto_content = preg_replace($filterpattern,$filterreplace,$tuto_content);
						}
						if (strpos($tuto_content,"table>")!=false || strpos($tuto_content,"TABLE>")!=false)
							echo "<pre>".$tuto_content."</pre>";
						else if(strpos($tuto_content,"</")!=false)
							echo nl2br($tuto_content);
						else if(strpos($tuto_content,"img")!=false || strpos($tuto_content,"IMG")!=false)
							echo nl2br($tuto_content);
						else
						echo str_replace(" ","&nbsp;",nl2br($tuto_content));
?>	
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="mt_20" align="center"><a href="/front/tutorial_board.php" target="_self"><img src="../img/button/customer_notice_view_list_btn.gif"></a></div>
	</div>
	
	
</table>
<script>

$(document).ready(function(){
	$("#fSize").find("iframe").css("max-width","740px");
});

</script>
		
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
		