<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$num=$_GET["num"];

#####좌측 메뉴 class='on' 을 위한 페이지코드
$page_code='review';

$sql = "SELECT * FROM tblproductreview WHERE num='{$num}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_pdata=$row;
	$date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."(".substr($row->date,8,2).":".substr($row->date,10,2).")";
} else {
	alert_go('해당 리뷰가 없습니다.','c');
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
		<div class="containerBody sub_skin">

	<!-- LNB -->
	
	<?php //include ("{$Dir}board/top_lnb.php");
			$lnb_flag = 5;
			include ($Dir.MainDir."lnb.php");
	?>
	
	<!-- #LNB -->
	<div class="right_section">

		<h3 class="title">
			REVIEW
			<p class="line_map"><a>홈</a> &gt; <a>Community</a> &gt; <a>REVIEW</a></p>
		</h3>

		<div class="customer_inquiry_wrap">
			<table class="write_table" summary="">
				<colgroup>
					<col style="width:121px" />
					<col style="width:auto" />
				</colgroup>
				<tbody>
					<tr height="40">
						<th scope="row">상품명</th>
						<td>
						<? $sql2 = "SELECT productname FROM tblproduct WHERE productcode='".$_pdata->productcode."'";
							$result2=pmysql_query($sql2,get_db_conn());
							$row2=pmysql_fetch_object($result2);
							echo $row2->productname;
							?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">제목</th>
						<td>
							<?=$_pdata->subject?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">날짜</th>
						<td>						
							<?=$date?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">평점</th>
						<td class="">						
							<?$colorStar = "";
							for($i=0;$i<$row->marks;$i++) {
								$colorStar .= "★";
							}
							$noColorStar = "";
							for($i=$row->marks;$i<5;$i++) {
								$noColorStar .= "☆";
							}
							echo $colorStar."<span>".$noColorStar."</span>";
						?>
						</td>
					</tr>
					<tr height="40">
						<th scope="row">내용</th>
						<td>
							<? if($_pdata->upfile){ ?>
								<img src="<?=$Dir.DataDir."shopimages/board/reviewbbs/".$_pdata->upfile ?>">
							<? } ?><br />
							<?=nl2br($_pdata->content)?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="mt_20" align="center"><a href="/front/reviewall.php" target="_self"><img src="../img/button/customer_notice_view_list_btn.gif"></a></div>
	</div>
		</div>
	</div>
</td>	
</tr>
</table>
		
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
		