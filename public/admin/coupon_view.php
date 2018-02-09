<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$coupon_types = array(
    '1'=>'상품 쿠폰',
    '2'=>'신규가입 쿠폰',
    '3'=>'기념일 쿠폰',
    '4'=>'첫구매 쿠폰',
    '5'=>'등급업 쿠폰',
    '6'=>'기타 쿠폰',
    '7'=>'페이퍼 쿠폰',
    '8'=>'카드 쿠폰',
    '9'=>'무료배송 쿠폰'
);

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$coupon_code=$_POST["coupon_code"];
$sql = "SELECT * FROM tblcouponinfo WHERE coupon_code = '{$coupon_code}' ";
$result = pmysql_query($sql,get_db_conn());
if(!$row=pmysql_fetch_object($result)) {
	echo "<script>alert('해당 쿠폰 정보가 존재하지 않습니다.');window.close();</script>";
	exit;
}
pmysql_free_result($result);

$arissuetype =array("D"=>"삭제 쿠폰","M"=>"회원 가입시 발급","N"=>"즉시 발급용 쿠폰","Y"=>"쿠폰 클릭시 발급","A"=>"자동 발급","P"=>"직접 입력 발급");
if($row->time_type =='D') { 
	$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)."[".substr($row->date_start,8,2).":00] ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2)."[".substr($row->date_end,8,2).":00] 까지 사용가능";
	$date2 = substr($row->date_start,4,2)."/".substr($row->date_start,6,2)." ~ ".substr($row->date_end,4,2)."/".substr($row->date_end,6,2);
} else if($row->time_type =='P') {
	$date = abs($row->date_start)."일동안, ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2)."[".substr($row->date_end,8,2).":00] 까지 사용가능";
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
		$__[] = "<div>".implode(" > ",$_)."".$diffProduct."</div>";
		pmysql_free_result($result2);
	}
	$product = implode("",$__);
	pmysql_free_result($resultCate);
} else if($row->productcode=="GOODS") {
	$sql2 = "SELECT productname as product FROM tblproduct a JOIN tblcouponproduct b on a.productcode = b.productcode WHERE coupon_code = '{$coupon_code}'";
	$result2 = pmysql_query($sql2,get_db_conn());
	$count = 1;
	while($row2 = pmysql_fetch_object($result2)) {
		$diffProduct = "";
		if($row->use_con_type2=="N") $diffProduct = " - 제외";
		$_[] = "<div>".$count.". ".$row2->product."".$diffProduct."</div>";
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
<title>쿠폰정보 보기</title>
<link rel="stylesheet" href="style.css" type="text/css">
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
	var oWidth = document.all.table_body.clientWidth + 120;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>쿠폰 상세 페이지</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>쿠폰 상세 페이지</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<tr>
	<td background="images/member_zipsearch_bg.gif">
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
				<tr>
					<th><span>쿠폰코드</span></th>
					<td class="td_con1"><SPAN class=font_orange><B><?=$row->coupon_code?></B></SPAN></td>
				</tr>
                <tr>
					<th><span>쿠폰 발급조건</span></th>
					<td class="td_con1"><b><font color="black"><?=$coupon_types[$row->coupon_type]?></font></b></td>
				</tr>
				<tr>
					<th><span>쿠폰이름</span></th>
					<td class="td_con1"><b><font color="black"><?=$row->coupon_name?><br></font></b></td>
				</tr>
				<tr>
					<th><span>유효기간</span></th>
					<td class="td_con1"><?=$date?><br></td>
				</tr>
				<tr>
					<th><span>할인<!-- /적립 --> 속성</span></th>
					<td class="td_con1">
						<?=number_format($row->sale_money).$dan.$sale?> 쿠폰
						<?=$row->sale_max_money?"[최대 ".number_format($row->sale_max_money)."원 할인]":'';?><br>
					</td>
				</tr>
				<!-- <?if($row->sale_type>=3){?>
				<tr>
					<th><span>배송비 포함 여부</span></th>
					<td class="td_con1"><?=$delivery?><br></td>
				</tr>
				<?}?> -->
				<tr>
					<th><span>쿠폰 사용가능결제금액</span></th>
					<td class="td_con1"><?=$row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상 구매시에만 사용가능"?><br></td>
				</tr>
				<tr>
					<th><span>결제방법 제한</span></th>
					<td class="td_con1"><?=$row->bank_only=="Y"?"현금 입금만 가능 (실시간 계좌이체 포함)":"제한 없음"?></td>
				</tr>
				<tr>
					<th><span>쿠폰 사용조건</span></th>
					<td class="td_con1"><?=$row->use_con_type1=="Y"?"다른 상품과 구매시에도 사용가능":"해당 상품 구매시에만 사용가능"?></td>
				</tr>
				<tr>
					<th><span>쿠폰사용 방법</span></th>
					<td>
						<?
							if($row->coupon_use_type == '1'){
								echo "장바구니 쿠폰";
							}else if($row->coupon_use_type == '2'){
								echo "상품별 쿠폰";
							}
						?>
					</td>
				</tr>
				<tr>
					<th><span>쿠폰 노출조건</span></th>
					<td>
						<?
							if($row->coupon_is_mobile == 'A'){
								echo "전체";
							}else if($row->coupon_is_mobile == 'P'){
								echo "PC";
							}else if($row->coupon_is_mobile == 'M'){
								echo "MOBILE";
							}else if($row->coupon_is_mobile == 'T'){
								echo "APP";
							}else if($row->coupon_is_mobile == 'B'){
								echo "PC + MOBILE";
							}else if($row->coupon_is_mobile == 'C'){
								echo "PC + APP";
							}
						?>
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
			<tr>
				<th><span>적용 상품군</span></th>
				<td class="td_con1">
					<?if($row->productcode == "GOODS"){?>
						<div style = 'height:100px;overflow-y:auto;'><?=$product?></div>
					<?}else if($row->productcode == "CATEGORY"){?>
						<div style = 'height:50px;overflow-y:auto;'><?=$product?></div>
					<?}else{?>
						<div><?=$product?></div>
					<?}?>
				</td>
			</tr>
			<tr>
				<th><span>쿠폰 발급조건</span></th>
				<td class="td_con1">
					<?=$arissuetype[$row->issue_type]." ".$membermsg?>
					<? if($row->issue_type=="P"){?>&nbsp;&nbsp;<a href = "coupon_view_excel_paper.php?coupon_code=<?=$coupon_code?>"><b>[Excel 다운로드]</b></a><?}?><br>
				</td>
			</tr>				
			<?php if($row->auto=="Y"){?>
			<tr>
				<th><span>상품상세 자동노출</span></th>
				<td class="td_con1"><?=$row->detail_auto=="Y"?"Yes":"No"?></td>
			</tr>
			<?php }?>
			<tr>
				<th><span>발행 가능 쿠폰 수</span></th>
				<td class="td_con1"><?=$row->issue_tot_no=="0"?"무제한":number_format($row->issue_tot_no)."명"?></td>
			</tr>
			<!-- <tr>
				<th><span>등급혜택과 동시적용</span></th>
				<td class="td_con1"><?=$row->use_point=="Y"?"Yes":"No"?></td>
			</tr> -->
			<!--tr>
				<th><span>쿠폰 이미지</span></th>
				<td class="td_con1">
                <div class="table_none">
				<table border="0" cellpadding="0" cellspacing="0" width="352" style="table-layout:fixed;">
				<col width="5"></col>
				<col width=></col>
				<col width="5"></col>
				<tr>
					<td colspan="3"><IMG SRC="<?=$Dir?>images/common/coupon_table01.gif" border="0"></td>
				</tr>
				<tr>
					<td background="<?=$Dir?>images/common/coupon_table02.gif"></td>
					<td width="100%" style="padding:3pt;" background="<?=$Dir?>images/common/coupon_bg.gif">
					<table align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding-bottom:4pt;"><IMG SRC="<?=$Dir?>images/common/coupon_title<?=$row->sale_type?>.gif" border="0"></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><font color="#585858" style="font-size:11px;letter-spacing:-0.5pt;">유효기간 : <?=$date2?></font><?=($row->bank_only=="Y"?"<font color=\"#0000FF\">(현금결제만 가능)</font>":"")?></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%" align="right"><font color="#FF5000" style="font-family:sans-serif;font-size:48px;line-height:45px"><b><font color="#FF6600" face="강산체"><?=number_format($row->sale_money)?></font></b></td>
							<td><IMG SRC="<?=$Dir?>images/common/coupon_text<?=$row->sale_type?>.gif" border="0"></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
					<td background="<?=$Dir?>images/common/coupon_table04.gif"></td>
				</tr>
				<tr>
					<td colspan="3"><IMG SRC="<?=$Dir?>images/common/coupon_table03.gif" border="0"></td>
				</tr>
				</table>
                </div>
				</td>
			</tr-->
			<tr>
				<th><span>발생된 쿠폰수</span></th>
				<td class="td_con1" width="349"><b><?=number_format($row->issue_no)?>개</b>(실 누적 발급 쿠폰 수)</td>
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
		<td align="center"><a href="javascript:window.close()"><img src="images/btn_close.gif"  border=0></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	</table>
	</td>
</tr>
</TABLE>

</body>
</html>