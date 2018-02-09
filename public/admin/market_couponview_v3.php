<?php // hspark
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	$coupon_types = array(
		'1'=>'즉시발급 쿠폰',
		'2'=>'신규가입 쿠폰',
		'3'=>'기념일 쿠폰',
		'4'=>'첫구매 쿠폰',
		'6'=>'다운로드 쿠폰',
		'7'=>'페이퍼 쿠폰',
		'9'=>'무료배송 쿠폰',
		'10'=>'생일 쿠폰',
		'11'=>'상품구매 후기 쿠폰',
		'12'=>'구매 수량 충족 쿠폰',
		'13'=>'구매 금액 충족 쿠폰',
		'14'=>'주말 출석 쿠폰',
		'15'=>'회원 등급별 쿠폰',
		'16'=>'일반발급 쿠폰'
	);

	if(ord($_ShopInfo->getId())==0){
		echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
		exit;
	}

	$coupon_code=$_POST["coupon_code"];

	$imagepath=$Dir.DataDir."shopimages/etc/";

	$sql = "SELECT * FROM tblcouponinfo WHERE coupon_code = '{$coupon_code}' ";
	$result = pmysql_query($sql,get_db_conn());
	if(!$row=pmysql_fetch_object($result)) {
		echo "<script>alert('해당 쿠폰 정보가 존재하지 않습니다.');window.close();</script>";
		exit;
	}
	pmysql_free_result($result);

	// 구매 수량 충족
	if($row->coupon_type == '12') $order_accept_text	= "&nbsp;(구매 수량 : ".number_format($row->order_accept_quantity)."개 이상)";

	// 구매 금액 충족
	if($row->coupon_type == '13') $order_accept_text	= "&nbsp;(구매 금액 : ".number_format($row->order_accept_price)."원 이상)";

	// 쿠폰 발급 대상
	if($row->coupon_type == '1' || $row->coupon_type == '9' || $row->coupon_type == '15'){
		if($row->sel_gubun == 'A') $sel_gubun_text	= '전체회원';
		if($row->sel_gubun == 'G'){
			list($sel_gubun_text)=pmysql_fetch_array(pmysql_query("SELECT group_name FROM tblmembergroup where group_code='{$row->sel_group}' "));
			$sel_gubun_text."등급";
		}
		if($row->sel_gubun == 'M' || $row->sel_gubun == 'E'){
			list($issue_members_cnt)=pmysql_fetch_array(pmysql_query("SELECT count(*) as issue_members_cnt FROM tblcouponissue_standby where coupon_code='{$coupon_code}' "));
			list($issue_member_id)=pmysql_fetch_array(pmysql_query("SELECT id FROM tblcouponissue_standby where coupon_code='{$coupon_code}' order by cis_no limit 1"));
			
			if ($issue_members_cnt == 1) {	
				$sel_gubun_text	= $issue_member_id;		
			} else {
				$sel_gubun_text	= "<div style = 'height:100px;overflow-y:auto;'>\n";
				$sql_ismem = "SELECT * FROM tblcouponissue_standby where coupon_code='{$coupon_code}' order by cis_no";
				$result_ismem = pmysql_query($sql_ismem,get_db_conn());
				$ismem_cnt	= 1;
				while($row_ismem = pmysql_fetch_object($result_ismem)) {
					$sel_gubun_text	.= "<div>".$ismem_cnt.". ".$row_ismem->id."</div>\n";
					$ismem_cnt++;
				}
				$sel_gubun_text	.= "</div>\n";
			}
		}
	}
	if($row->coupon_type == '3' || $row->coupon_type == '10'){
		// 발급 시점
		$issue_days_ago_text	= number_format($row->issue_days_ago)."일 전";
	} else if($row->coupon_type == '14'){
		// 발급 시점
		$issue_days_ago_text	= "주단위 주말";
	}  else if($row->coupon_type == '15'){
		// 발급 시점
		$issue_days_ago_text	= "월단위";
	} else {
		// 경로
		if ($row->join_rote == 'A') $join_rote_text = "전체";
		if ($row->join_rote == 'P') $join_rote_text = "PC";
		if ($row->join_rote == 'M') $join_rote_text = "모바일 웹";
		if ($row->join_rote == 'T') $join_rote_text = "모바일 APP";
	}

	// 혜택
	if($row->sale_type == '1' || $row->sale_type == '2') $sale2_text = "[할인율 쿠폰] ";
	if($row->sale_type == '3' || $row->sale_type == '4') $sale2_text = "[금액 쿠폰] ";

	if($row->sale_type<=2) $dan="%";
	else $dan="원";

	$maxPrice = $row->sale_max_money?" (최대 ".number_format($row->sale_max_money)."원)":'';
	$sale_text	= $sale2_text.number_format($row->sale_money).$dan.$maxPrice;
	if ($row->coupon_type == '9') $sale_text	= "배송료 무료 (개별배송료 제외)";
	
	//1회 발급 수량
	if($row->coupon_type != '7') {
		$one_issue_limit_text = $row->one_issue_limit=="1"?"1장":number_format($row->one_issue_quantity)."장";
	}

	// 쿠폰 사용 방법
	if($row->coupon_use_type == "1") $coupon_use_type = "장바구니";
	else $coupon_use_type = "상품쿠폰";

	// 쿠폰 사용 범위
	if ($row->coupon_is_mobile == 'A') $coupon_is_mobile_text = "전체";
	if ($row->coupon_is_mobile == 'P') $coupon_is_mobile_text = "PC";
	if ($row->coupon_is_mobile == 'M') $coupon_is_mobile_text = "모바일 웹";
	if ($row->coupon_is_mobile == 'T') $coupon_is_mobile_text = "모바일 APP";
	if ($row->coupon_is_mobile == 'B') $coupon_is_mobile_text = "PC + 모바일 웹";
	if ($row->coupon_is_mobile == 'C') $coupon_is_mobile_text = "PC + 모바일 APP";
	if ($row->coupon_is_mobile == 'D') $coupon_is_mobile_text = "모바일 웹 + 모바일 APP";

	//사용 제한
	if ($row->mini_type == 'P' || $row->mini_type == '') {
		$mini_type_text	= $row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상";
	} else if ($row->mini_type == 'Q') {
		$mini_type_text	= $row->mini_quantity=="0"?"제한 없음":number_format($row->mini_quantity)."개 이상";
	}

	//유효 기간
	if($row->date_start>0) {
		$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." 부터 ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2)." 까지";
		if($row->coupon_type == '14') $date .= ", 해당 주말 동안";
		if($row->coupon_type == '15') $date .= ", 해당 월 동안";
		$date .= " 사용가능";
	} else {
		$date  = "발급일 부터 ".abs($row->date_start)."일동안, ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2)." 까지 사용가능";
	}

	//제외 상품군
	if($row->use_con_type2=="Y") {
		$check_productcode	= $row->productcode;
	} else if($row->use_con_type2=="N") {					
		$check_productcode	= $row->not_productcode;
	}

	$prleng=strlen($check_productcode);

	if($check_productcode=="ALL") {
		$product="없음";
	} else if($check_productcode=="CATEGORY") {
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
				$diffProduct = "포함";
				if($row->use_con_type2=="N") $diffProduct = "제외";
				$_[] = $row2->product;
			}
			$__[] = "<div>[".$diffProduct." 카테고리] ".implode(" > ",$_)."</div>";
			pmysql_free_result($result2);
		}
		$product = implode("",$__);
		pmysql_free_result($resultCate);
	} else if($check_productcode=="GOODS") {
		$sql2 = "SELECT productname as product FROM tblproduct a JOIN tblcouponproduct b on a.productcode = b.productcode WHERE coupon_code = '{$coupon_code}'";
		$result2 = pmysql_query($sql2,get_db_conn());
		$count = 1;
		while($row2 = pmysql_fetch_object($result2)) {
			$diffProduct = "포함";
			if($row->use_con_type2=="N") $diffProduct = "제외";

			$_[] = "<div>[".$diffProduct." 상품] ".$count.". ".$row2->product."</div>";
			$count++;
		}
		$product = implode("",$_);
		pmysql_free_result($result2);
	} else if($check_productcode=="BRANDSEASONS") {
		$sql2 = "SELECT brandname as product, season_year, season FROM tblproductbrand a JOIN tblcouponbrandseason b on a.bridx = b.bridx WHERE coupon_code = '{$coupon_code}' order by no";

		$result2 = pmysql_query($sql2,get_db_conn());
		$count = 1;
		while($row2 = pmysql_fetch_object($result2)) {
			$diffProduct = "포함";
			if($row->use_con_type2=="N") $diffProduct = "제외";
			if ($row2->season_year) {
				list($_season)=pmysql_fetch("SELECT season_kor_name FROM tblproductseason where season_year='{$row2->season_year}' AND season='{$row2->season}' ");
			} else {
				$_season	= "전체";
			}

			$_[] = "<div>[".$diffProduct." 브랜드시즌] ".$count.". ".$row2->product." > ".$_season."</div>";
			$count++;
		}
		$product = implode("",$_);
		pmysql_free_result($result2);
	}

	// 제품 상세 쿠폰 노출 설정
	if($row->coupon_type == '6') {
		if($row->detail_auto == 'N'){
			$detail_auto_text	= '노출 안함';
		} else if($row->detail_auto == 'Y'){
			$detail_auto_text	= '노출 함';
			if ($row->display_img_type == '1') {
				$detail_auto_text .= '(기본이미지 사용)';
			} else if ($row->display_img_type == '2') {
				$detail_auto_text .= '(직접 업로드)';
				//echo $imagepath.$row->display_img;
				if(file_exists($imagepath.$row->display_img)) {
					$detail_auto_text .= "<br><img src='{$imagepath}{$row->display_img}.gif'>";
				}
			}
		}
	}

	// 발행 쿠폰 수량
	if($row->coupon_type == '7') {
		$issue_max_no_text	= number_format($row->issue_max_no)."장";
	}


?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>쿠폰 상세 정보</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
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
	var oWidth = document.all.table_body.clientWidth + 36;
	var oHeight = document.all.table_body.clientHeight + 150;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>

<div class="pop_top_title"><p>쿠폰 상세 정보</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="700" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
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
			<td width="100%" style='padding-bottom:5px'><IMG height=9 src="images/point.gif" border=0>&nbsp;<b style='font-size:13px;color:#508900;'>쿠폰 발급 정보</b></td>
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
					<th><span>쿠폰 발급 구분</span></th>
					<td class="td_con1"><b><font color="black"><?=$coupon_types[$row->coupon_type]?></font></b><?=$order_accept_text?></td>
				</tr>
				<?if($sel_gubun_text !=''){?>
				<tr>
					<th><span>쿠폰 발급 대상</span></th>
					<td class="td_con1"><?=$sel_gubun_text?></td>
				</tr>
				<?}?>
				<?if($join_rote_text!=''){?>
				<tr>
					<th><span>경로</span></th>
					<td class="td_con1"><?=$join_rote_text?></td>
				</tr>
				<?}?>
				<?if($issue_days_ago_text!=''){?>
				<tr>
					<th><span>발급 시점</span></th>
					<td class="td_con1"><?=$issue_days_ago_text?></td>
				</tr>
				<?}?>
				<tr>
					<th><span>쿠폰 명</span></th>
					<td class="td_con1"><b><font color="black"><?=$row->coupon_name?></font></b></td>
				</tr>
				<tr>
					<th><span>쿠폰 설명</span></th>
					<td class="td_con1"><?=$row->description?$row->description:"&nbsp;"?></td>
				</tr>
				<tr>
					<th><span>혜택</span></th>
					<td class="td_con1"><?=$sale_text?></td>
				</tr>
				<?if($one_issue_limit_text!=''){?>
				<tr>
					<th><span>1회 발급</span></th>
					<td class="td_con1"><?=$one_issue_limit_text?></td>
				</tr>
				<?}?>
			</TABLE>
            </div>
			</td>
		</tr>
		<tr>
			<td width="100%" height="10">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" style='padding-bottom:5px'><IMG height=9 src="images/point.gif" border=0>&nbsp;<b style='font-size:13px;color:#508900;'>쿠폰 사용 정보</b></td>
		</tr>
		<tr>
			<td width="100%">
            <div class="table_style01">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>사용 방법</span></th>
					<td><?=$coupon_use_type?></td>
				</tr>
				<tr>
					<th><span>사용 범위</span></th>
					<td><?=$coupon_is_mobile_text?></td>
				</tr>
				<tr>
					<th><span>사용 제한</span></th>
					<td class="td_con1"><?=$mini_type_text?></td>
				</tr>
				<tr>
					<th><span>유효 기간</span></th>
					<td class="td_con1"><?=$date?><br></td>
				</tr>
			</TABLE>
            </div>
			</td>
		</tr>
		<tr>
			<td width="100%" height="10">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" style='padding-bottom:5px'><IMG height=9 src="images/point.gif" border=0>&nbsp;<b style='font-size:13px;color:#508900;'>쿠폰 부가 정보</b></td>
		</tr>
		<tr>
			<td width="100%">
            <div class="table_style01">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<tr>
				<th><span>상품군</span></th>
				<td class="td_con1">
					<?if($check_productcode == "GOODS"){?>
						<div style = 'height:100px;overflow-y:auto;'><?=$product?></div>
					<?}else if($check_productcode == "CATEGORY"){?>
						<div style = 'height:50px;overflow-y:auto;'><?=$product?></div>
					<?}else if($check_productcode == "BRANDSEASONS"){?>
						<div style = 'height:50px;overflow-y:auto;'><?=$product?></div>
					<?}else{?>
						<div><?=$product?></div>
					<?}?>
				</td>
			</tr>			
			<?php if($detail_auto_text!=""){?>
			<tr>
				<th><span>상품상세 자동노출</span></th>
				<td class="td_con1"><?=$detail_auto_text?></td>
			</tr>
			<?php }?>
			<?php if($issue_max_no_text!=""){?>
			<tr>
				<th><span>발행 쿠폰 수량</span></th>
				<td class="td_con1"><?=$issue_max_no_text?></td>
			</tr>
			<?}?>
			</TABLE>
            </div>
			</td>
		</tr>
		<tr>
			<td width="100%" height="10">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" style='padding-bottom:5px'><IMG height=9 src="images/point.gif" border=0>&nbsp;<b style='font-size:13px;color:#508900;'>발급 쿠폰 정보</b></td>
		</tr>
		<tr>
			<td width="100%">
            <div class="table_style01">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<tr>
				<th><span>발급 쿠폰 수량</span></th>
				<td class="td_con1"><b><?=number_format($row->issue_no)?>개</b>(실 누적 발급 쿠폰 수)</td>
			</tr>
			</TABLE>
			</div>
			</td>
		</tr>
		<tr>
			<td width="100%" height="10">&nbsp;</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</TABLE>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td height=50 align="center" style='border-top:1px solid #cbcbcb;'><a href="javascript:window.close()"><img src="images/btn_close.gif"  border=0></a></td>
</tr>
</table>

</body>
</html>