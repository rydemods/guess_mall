<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

if($_POST["type"] == "modify"){
	$sql = "UPDATE tblcouponinfo SET coupon_name = '".$_POST['coupon_name']."' WHERE coupon_code = '".$_POST['coupon_code']."'";
	$result = pmysql_query($sql, get_db_conn());
	if($_POST['productcode'] == "GOODS"){
		$set_productcode = $_POST['set_productcode'];

		pmysql_query("DELETE FROM tblcouponproduct WHERE coupon_code = '".$_POST['coupon_code']."'", get_db_conn());
		
		if(is_array($set_productcode)) $set_productcode = array_unique($set_productcode);		
		foreach($set_productcode as $v){
			pmysql_query("INSERT INTO tblcouponproduct (coupon_code, productcode) VALUES ('".$_POST['coupon_code']."', '{$v}')", get_db_conn());
		}
	}else if($_POST['productcode'] == "CATEGORY"){
		$set_productcode = $_POST['set_productcode'];

		pmysql_query("DELETE FROM tblcouponcategory WHERE coupon_code = '".$_POST['coupon_code']."'", get_db_conn());


		if(is_array($set_productcode)) $set_productcode = array_unique($set_productcode);	
		foreach($set_productcode as $v){
			pmysql_query("INSERT INTO tblcouponcategory (coupon_code, categorycode) VALUES ('".$_POST['coupon_code']."', '{$v}')", get_db_conn());
		}
	}
	echo "<script>alert('해당 쿠폰 정보를 수정했습니다.');opener.location.href = 'market_couponlist.php';window.close();</script>";
	exit;
}

$coupon_code = $_POST["coupon_code"];
$sql = "SELECT * FROM tblcouponinfo WHERE coupon_code = '{$coupon_code}' ";
$result = pmysql_query($sql,get_db_conn());
if(!$row=pmysql_fetch_object($result)) {
	echo "<script>alert('해당 쿠폰 정보가 존재하지 않습니다.');window.close();</script>";
	exit;
}
pmysql_free_result($result);

$arissuetype =array("D"=>"삭제 쿠폰","M"=>"회원 가입시 발급","N"=>"즉시 발급용 쿠폰","Y"=>"쿠폰 클릭시 발급","A"=>"자동 발급","P"=>"직접 입력 발급");
if($row->date_start>0) { 
	$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)."[".substr($row->date_start,8,2).":00] ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2)."[".substr($row->date_end,8,2).":00]";
	$date2 = substr($row->date_start,4,2)."/".substr($row->date_start,6,2)." ~ ".substr($row->date_end,4,2)."/".substr($row->date_end,6,2);
} else {
	$date = abs($row->date_start)."일동안";
	$date2 = date("m/d")." ~ ".date("m/d",strtotime(abs($row->date_start).' day'));
}
if($row->sale_type<=2) {
	$dan="%";
} else {
	$dan="원";
}
if($row->sale_type%2==0) {
	$sale = "할인";
} else {
	$sale = "적립";
}

if($row->delivery_type=='Y')$delivery="배송비 포함";
else $delivery="배송비 미포함";

$prleng=strlen($row->productcode);
if($row->productcode=="ALL") {
	$product="전체상품";
} else if($row->productcode=="CATEGORY") {
	$sqlCate = "SELECT categorycode FROM tblcouponcategory WHERE coupon_code = '{$coupon_code}'";
	$resultCate = pmysql_query($sqlCate,get_db_conn());
	$__=array();
	while($rowCate = pmysql_fetch_object($resultCate)) {
		$sql2 = "SELECT code_name as product FROM tblproductcode WHERE code_a='".substr($rowCate->categorycode,0,3)."' ";
		if(substr($rowCate->categorycode,3,3)!="000") {
			$sql2.= "AND (code_b='".substr($rowCate->categorycode,3,3)."' OR code_b='000') ";
			if(substr($rowCate->categorycode,6,3)!="000") {
				$sql2.= "AND (code_c='".substr($rowCate->categorycode,6,3)."' OR code_c='000') ";
				if(substr($rowCate->categorycode,9,3)!="000") {
					$sql2.= "AND (code_d='".substr($rowCate->categorycode,9,3)."' OR code_d='000') ";
				} else {
					$sql2.= "AND code_d='000' ";
				}
			} else {
				$sql2.= "AND code_c='000' ";
			}
		} else {
			$sql2.= "AND code_b='000' AND code_c='000' ";
		}
		$sql2.= "AND type IN ('L', 'LX', 'LM', 'LMX') 
		ORDER BY code_a,code_b,code_c,code_d ASC ";
		$result2 = pmysql_query($sql2,get_db_conn());
		$_=array();
		while($row2=pmysql_fetch_object($result2)) {
			$diffProduct = "";
			if($row->use_con_type2=="N") $diffProduct = " - 제외";
			$_[] = $row2->product;
		}
		$__[] = "<div style='padding:5px 0px;'><a href=\"javascript:;\" onClick=\"javascript:$(this).parent().remove();\"><img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' /></a>&nbsp;&nbsp;".implode(" > ",$_)."".$diffProduct."<input type = 'hidden' name ='set_productcode[]' value = '".$rowCate->categorycode."'></div>";
		pmysql_free_result($result2);
	}
	$product = implode("",$__);
	pmysql_free_result($resultCate);
} else if($row->productcode=="GOODS") {
	$sql2 = "SELECT productname as product, b.productcode FROM tblproduct a JOIN tblcouponproduct b on a.productcode = b.productcode WHERE coupon_code = '{$coupon_code}'";
	$result2 = pmysql_query($sql2,get_db_conn());
	$count = 1;
	while($row2 = pmysql_fetch_object($result2)) {
		$diffProduct = "";
		if($row->use_con_type2=="N") $diffProduct = " - 제외";
		//$_[] = "<div>".$count.". ".$row2->product."".$diffProduct."<input type = 'hidden' name ='set_productcode[]' value = '".$row2->productcode."'>&nbsp;&nbsp;<a href = 'javascript:;' class = 'CLS_objDelete'><b>[삭제]</b></a></div>";
		$_[] = "<div style='padding:5px 0px;'><a href=\"javascript:;\" onClick=\"javascript:$(this).parent().remove();\"><img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' /></a>&nbsp;&nbsp;".$row2->product."".$diffProduct."<input type = 'hidden' name ='set_productcode[]' value = '".$row2->productcode."'></div>";
		$count++;
	}
	$product = implode("",$_);
	pmysql_free_result($result2);
}
if($row->member=="ALL") {
	$membermsg = "[전체회원]";
} else if($row->member!="") {
	$sql2 = "SELECT group_name FROM tblmembergroup WHERE group_code='{$row->member}' ";
	$result2 = pmysql_query($sql2,get_db_conn());
	if($row2 = pmysql_fetch_object($result2)) $membermsg = "[회원등급 : {$row2->group_name}]";
	else $membermsg = "[개별회원]";
	pmysql_free_result($result2);
} else {
	$membermsg = "[개별회원]";
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>쿠폰 정보 수정 페이지</title>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
	document.onkeydown = CheckKeyPress;
	document.onkeyup = CheckKeyPress;
	function CheckKeyPress() {
		ekey = event.keyCode;

		if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
			event.keyCode = 0;
			return false;
		}
	}

	function PageResize() {
		var oWidth = document.all.table_body.clientWidth + 320;
		var oHeight = document.all.table_body.clientHeight + 120;

		window.resizeTo(oWidth,oHeight);
	}
	function ChoiceProduct(type){
		if(type == '2'){
			window.open("about:blank","coupon_product","width=600,height=150,scrollbars=no");
			document.form2.action = "coupon_productchoice2.php";
			$("#ID_addProduct").show();
		}else if(type == '3'){
			window.open("about:blank","coupon_product","width=700,height=800,scrollbars=yes");
			document.form2.action = "coupon_productchoice3.php";
			$("#ID_addProduct").show();
		}
		document.form2.submit();
	}
//-->
</SCRIPT>
</head>
<div class="pop_top_title"><p>쿠폰 정보 수정 페이지</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
	<tr>
		<td background="images/member_zipsearch_bg.gif">			
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method='POST'>
				<input type='hidden' name='type' value = 'modify'>
				<input type='hidden' name='coupon_code' value="<?=$coupon_code?>">
				<input type='hidden' name='productcode' value="<?=$row->productcode?>">
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
									<td width="100%"><IMG height=9 src="images/icon_9.gif" width=13 border=0><b>쿠폰 기본정보</b></td>
								</tr>
								<tr>
									<td width="100%">
										<div class="table_style01">
											<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
												<col width = "20%"><col width = "*%">
												<tr>
													<th><span>쿠폰코드</span></th>
													<td class="td_con1"><SPAN class=font_orange><B><?=$row->coupon_code?></B></SPAN></td>
												</tr>
												<tr>
													<th><span>쿠폰이름</span></th>
													<td class="td_con1">
														<input maxlength='100' size='70' name='coupon_name' value = '<?=$row->coupon_name?>' class="input">
														<br><span class="font_orange"><b>예)새 봄맞이10% 할인쿠폰이벤트~</b></span>
													</td>
												</tr>
											</TABLE>
										</div>
									</td>
								</tr>
								<tr>
									<td width="100%" height="25">&nbsp;</td>
								</tr>
								<tr>
									<td width="100%"><IMG height=9 src="images/icon_9.gif" width=13 border=0><b><font color="black">쿠폰 부가정보</font></b></td>
								</tr>
								<tr>
									<td width="100%">
										<div class="table_style01">
											<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
												<col width = "20%"><col width = "*%">
												<tr id = 'ID_addProduct'>
													<th>
														<span>적용 상품군</span>
														<?if($row->productcode == "GOODS"){?>
															<div>&nbsp;&nbsp;<a href="javascript:ChoiceProduct(3);"><img src="images/btn_select2.gif" border="0" hspace="2"></a></div>											
														<?}else if($row->productcode == "CATEGORY"){?>
															<div>&nbsp;&nbsp;<a href="javascript:ChoiceProduct(2);"><img src="images/btn_select2.gif" border="0" hspace="2"></a></div>		
														<?}?>
													</th>
													<td class="td_con1">
														<?if($row->productcode == "GOODS" || $row->productcode == "CATEGORY"){?>
															<div id = 'ID_productLayer' style = 'height:350px;overflow-y:auto;'><?=$product?></div>
														<?}else{?>
															<div><?=$product?></div>
														<?}?>
													</td>
												</tr>
											</TABLE>
										</div>
									</td>
								</tr>
							</table>
						</td>
						<td width="18">&nbsp;</td>
					</tr>
					<tr>
						<td width="18">&nbsp;</td>
						<td align="center">
							<a href="javascript:;"><input type = 'image' src="img/btn/btn_cate_modify.gif" border=0></a>
							<a href="javascript:window.close();"><img src="images/btn_close.gif" border=0></a>
						</td>
						<td width="18">&nbsp;</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</TABLE>

<form name=form2 action="coupon_productchoice2.php" method=post target=coupon_product>
</form>
</body>
</html>