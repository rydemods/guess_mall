<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$imagepath=$Dir.DataDir."shopimages/product/";

$keyword = $_POST["keyword"];

$page_numberic_type=1;
if ($keyword){
	$qry.= "AND (productname || productcode) LIKE '%{$keyword}%' ";

	$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a  WHERE 1=1 ";
	$sql0.= $qry;
	$paging = new newPaging($sql0,10,15);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
	$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date, modifydate, sabangnet_flag ";
	$sql.= "FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode and c_maincate=1) WHERE 1=1 ";
	$sql.= $qry." ";
	$sql.= "ORDER BY regdate DESC ";


	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	$cnt=0;
	$arrayData = array();
	while($row=pmysql_fetch_object($result)) {
		$arrayData[] = $row;
	}
}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>묶음 상품 검색</title>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="/css/admin.css" type="text/css">
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 230;

	window.resizeTo(oWidth,oHeight);
}

function submitProduct(){
	if($("input[name='sabangnet_productcode[]']:checked").length == 0){
		alert("상품을 선택 하지 않으셨습니다.");
	}else{
		//ID_product_layer
		$("input[name='sabangnet_productcode[]']:checked").each(function(){			
			$("#ID_product_layer", opener.document).append("<div>"+$(this).val()+" : "+$(this).attr('alt')+"<input type = 'hidden' name = 'sabangnet_set_productcode[]' value = '"+$(this).val()+"'></div>");
		})
	}
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>묶음 상품 검색</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>묶음 상품 검색</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode value = 'sabangProductModify'>
	<input type=hidden name=prcode>
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">			
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td>
			<!-- 테이블스타일01 -->
			<div class="table_style01 pt_20">
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<th><span>상품검색</span></th>
						<td><input class="w200" type="text" name="keyword" onfocus="this.value=''; this.style.color='#000000'; this.style.textAlign='left';" <?=$keyword?"value=".$keyword:"style=\"color:'#bdbdbd';text-align:center;\" value=\"상품명 상품코드\""?>></td>
					</tr>
				</table>
				<p class="ta_c"><a href="#"><input type="image" src="../admin/images/icon_search.gif" alt="검색" /></a></p>
			</div>
		</tr>
	</table>



	<TABLE WIDTH="550" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
		<TR>
			<TD>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="18">&nbsp;</td>
					<td>&nbsp;</td>
					<td width="18">&nbsp;</td>
				</tr>
				<tr>
					<td width="18">&nbsp;</td>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
									<div class="table_style02">
										<table cellpadding="5" cellspacing="0" width="100%" bgcolor="#F3F3F3">
											<tr align=center>
												<th width = '40'>선택</th>
												<th width = '40'>NO</th>
												<th width = '100'>이미지</th>
												<th>상품명</th>
											</tr>
											<?if(count($arrayData) > 0){?>
												<?foreach($arrayData as $k => $v){?>
												<?$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);?>
												<tr style = 'background:#fff;'>
													<td><input type = 'checkbox' name = 'sabangnet_productcode[]' value = '<?=$v->productcode?>' alt = "<?=$v->productname?>"></td>
													<td><?=$number?></td>
													<?if (ord($v->tinyimage) && file_exists($imagepath.$v->tinyimage)){?>
														<td align="center" width="70"><img src="<?=$imagepath.$v->tinyimage?>" style="width:70px" border="0"></td>
													<?}else{?>
														<td align="center" width="70"><img src="<?=$Dir?>images/product_noimg.gif" style="width:70px" border="0"></td>
													<?}?>
													<td align = 'left'><?=$v->productname?></td>
												</tr>
												<?$cnt++;?>
												<?}?>
											<?}?>
										</table>
									</div>
									<?php
										echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
										echo "	<div class=\"page_navi\">";
										echo "		<ul>";
										if($page_numberic_type) echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
										echo "		</ul>";
										echo "	</div>";
										echo "</div>";
									?>
								</td>
							</tr>
							<tr>
								<td height=10></td>
							</tr>
						</table>
					</td>
					<td width="18">&nbsp;</td>
				</tr>
				<tr>
					<td width="18">&nbsp;</td>
					<td align="center">
						<a href="javascript:submitProduct();"><img src="images/myicon_upload_btn.gif" width="85" height="24" border="0" vspace="5" border=0></a>&nbsp;&nbsp;
						<a href="javascript:window.close();"><img src="images/btn_closea.gif" width="69" height="24" border="0" vspace="5" border=0></a>
					</td>
					<td width="18">&nbsp;</td>
				</tr>
			</table>
			</TD>
		</TR>
	</TABLE>
</form>
<?php if(ord($onload)) echo "<script>alert('$onload');opener.location.reload();</script>"; ?>
</body>
</html>
