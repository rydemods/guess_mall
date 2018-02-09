<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$printval=$_REQUEST["printval"];

if(!$printval) {
	alert_go('선택된 상품이 없습니다.','c');
}

$printval=substr($printval,0,-1);
$prlist=str_replace("|","','",$printval);

### 그룹 정보 ###
$group_code=$_ShopInfo->memgroup;
if(ord($group_code) && $group_code!=NULL) {
	$sql = "SELECT * FROM tblmembergroup WHERE group_code='{$group_code}' AND SUBSTR(group_code,1,1)!='M' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)){
		$group_type=substr($row->group_code,0,2);
		$group_level=$row->group_level;
		$group_addmoney=$row->group_addmoney;
		$group_usemoney=$row->group_usemoney;
	}
	pmysql_free_result($result);
}

//$to = "tingtang@hanmail.net";
//$to = "tingtang09@gmail.com";
$to = "tigersoft@duometis.co.kr;";
$subject ="tswe";

$message ="<style type=text/css'>
<!--
body {margin: 0 0 0; overflow:auto;}
img {border:none}
td	{font-family:'돋움,굴림';color:#4B4B4B;font-size:12px;line-height:17px;}
body {
	scrollbar-face-color: #dddddd;
	scrollbar-shadow-color: #aaaaaa;
	scrollbar-highlight-color: #ffffff;
	scrollbar-3dlight-color: #dadada;
	scrollbar-darkshadow-color: #dadada;
	scrollbar-track-color: #eeeeee;
	scrollbar-arrow-color: #ffffff;
	overflow-x:auto;overflow-y:scroll
}
A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.skin_font_size1{font-family:'돋움,굴림';font-size:11px;letter-spacing:-0.5pt;color:#666666;}
.skin_font_size2{font-family:'돋움,굴림';font-size:11px;color:#666666;}
.skin_cell1{color:#333333;font-family:'돋움,굴림';padding-bottom:13pt;padding-top:13pt;line-height:18px;letter-spacing:-0.5pt;}
.skin_cell2{color:#333333;font-family:'돋움,굴림';padding-top:10pt;line-height:18px;letter-spacing:-0.5pt; font-weight:bold;}
.skin_font_green{color:#339900;font-family:'돋움,굴림';font-size:12px;font-weight:bold;}
.skin_font_green a:link{color:#339900;font-family:'돋움,굴림';font-size:12px;}
.skin_font_green a:hover{color:#339900;font-family:'돋움,굴림';font-size:12px;}
.skin_font_green a:visited{color:#339900;font-family:'돋움,굴림';font-size:12px;}
.skin_font_blue{color:#0099CC;font-family:'돋움,굴림';font-size:12px;font-weight:bold;}
.skin_font_blue a:link{color:#0099CC;font-family:'돋움,굴림';font-size:12px;}
.skin_font_blue a:hover{color:#0099CC;font-family:'돋움,굴림';font-size:12px;}
.skin_font_blue a:visited{color:#0099CC;font-family:'돋움,굴림';font-size:12px;}
.skin_font_orange{color:#FF4C00;font-family:'돋움,굴림';font-size:12px;font-weight:bold;}
.skin_font_orange a:link{color:#FF4C00;font-family:'돋움,굴림';font-size:12px;}
.skin_font_orange a:hover{color:#FF4C00;font-family:'돋움,굴림';font-size:12px;}
.skin_font_orange a:visited{color:#FF4C00;font-family:'돋움,굴림';font-size:12px;}
-->
</style>
</head>
<body bgcolor='white' text='black' link='blue' vlink='purple' alink='red' leftmargin='0' rightmargin='0' marginwidth='0' topmargin='0' marginheight='0'>";
$message .="<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td height=10></td></tr><tr><td align=center style='font-size:28'><B>견 적 서</B></td></tr><tr><td height=10></td></tr><tr><td height=1 bgcolor=#787878></td></tr></table></td></tr><tr><td height=10></td></tr><tr><td valign=bottom style='padding:7'><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td width=40% valign=bottom><table border=0 cellpadding=0 cellspacing=0 width=100% id='glvtalbe'><col width=60></col><col width=></col><tr bgcolor=#FFFFFF><td align=right style='padding-right:5'>견적일자</td><td style='padding-left:5'>2014년 02월 11일 21시 23분</td></tr><tr bgcolor=#FFFFFF><td align=right style='padding-right:5'>유효기간</td><td style='padding-left:5'>견적 후 일주일</td></tr></table></td><td width=4% nowrap></td><td width=56%><table border=0 cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed'  id='glvtalbe'><col width=120></col><col width=></col><tr bgcolor=#FFFFFF><td align=right >사업자등록번호></td><td style='padding-left:5'>1298657701</td></tr><tr bgcolor=#FFFFFF><td align=right style='padding-right:5'>회 사 명</td><td style='padding-left:5'>(주)에코먼트</td>
</tr><tr bgcolor=#FFFFFF><td align=right style='padding-right:5'>판 매 원</td><td style='padding-left:5'>에코팩토리</td></tr><tr bgcolor=#FFFFFF><td align=right style='padding-right:5'>대표자 성명</td><td style='padding-left:5'>신정은</td></tr><tr bgcolor=#FFFFFF><td align=right style='padding-right:5'>업태/종목</td><td style='padding-left:5'>비즈동 B119호 / 제조</td></tr><tr bgcolor=#FFFFFF><td align=right style='padding-right:5'>사업장 주소</td><td style='padding-left:5'>경기 성남시 중원구 상대원1동 SKn테크노파크</td></tr><tr bgcolor=#FFFFFF><td align=right style='padding-right:5'>사업장 전화번호</td><td style='padding-left:5'>031-776-0964</td></tr></table></td></tr></table></td>
</tr><tr><td style='padding:7'><table border=0 cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed;'><tr><td width=50%>※ 아래와 같이 견적합니다.</td><td width=50% align=right>견적합계 : 800원, VAT포함</td></tr></table>	<table border=0 cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed;'  id='glvtalbe' ><col width=30></col><col width=></col><col width=90></col><col width=50></col><col width=80></col><col width=80></col><col width=70></col>
<col width=70></col><tr bgcolor='f4f4f4' height=25><td align=center>No</td><td align=center>상품명</td><td align=center>제조사</td><td align=center>수량</td><td align=center>상품단가</td><td align=center>회원할인</td><td align=center>상품금액</td><td align=center>세액</td></tr>";

	$sql = "SELECT a.basketidx, a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice,b.membergrpdc, b.option_reserve,b.production,";
	$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
	$sql.= "b.etctype,b.deli_price, b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx ";
	$sql.= "FROM tblbasket a, tblproduct b ";
	$sql.= "WHERE b.vender='0' ";
	$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.basketidx in ('".$prlist."')";
	$sql.= "AND a.productcode=b.productcode order by basketidx desc";
	$result=pmysql_query($sql,get_db_conn());

	$cnt=0;
	$total=0;
	$price=0;
	$vat=0;
	$estlist="";
	$addprice=0;


	while($row=pmysql_fetch_object($result)) {

		//######### 옵션에 따른 가격 변동 체크 ###############
		if (strlen($row->option_price)==0) {
			$price = $row->realprice;
			$sellprice=$row->sellprice;
		} else if (strlen($row->opt1_idx)>0) {
			$option_price = $row->option_price;
			$pricetok=explode(",",$option_price);
			$priceindex = count($pricetok);
			$sellprice=$pricetok[$row->opt1_idx-1];
		}

		//######### 상품 특별할인률 적용 ############
		$grpdc_ex=explode(";",$row->membergrpdc);

		foreach($grpdc_ex as $v){
			$grpdc_data=explode("-",$v);
			$grpdc_arr[$grpdc_data[0]]=$grpdc_data[1];
		}
		$dc_per=0;
		$dc_per=$grpdc_arr['lv'.$group_level];
		if($sellprice>0){
			if(strlen($group_type)>0 && $group_type!=NULL) {
				$salemoney=0;
				$salereserve=0;
				if($dc_per>0){
					$salemoney=round($sellprice*$dc_per/100,-1,PHP_ROUND_HALF_DOWN);
				}else{
					if($group_type=="SW" || $group_type=="SP") {
						if($group_type=="SW") {
							$salemoney=$group_addmoney;
						} else if($group_type=="SP") {
							$salemoney=round($sellprice*$group_addmoney/100,-1,PHP_ROUND_HALF_DOWN);
						}
					}
				}
			}
		}

		$before_sellprice=$sellprice;
		$sellprice=$sellprice-$salemoney;

		$price=round(($sellprice*$row->quantity)/1.1);
		$total+=$price;

		$vat=(($sellprice*$row->quantity)-round(($sellprice*$row->quantity)/1.1));
		$vat_tot+=$vat;
		$cnt++;

		$tok=array();
		$tok2=array();
		//옵션
		if (strlen($row->option1)>0) {
			$temp = $row->option1;
			$tok = explode(",",$temp);
		}
		if($tok[$row->opt1_idx])$opt1 = " [".($tok[$row->opt1_idx])."]";
		else $opt1 ='';

		if (strlen($row->option2)>0) {
			$temp = $row->option2;
			$tok2 = explode(",",$temp);
		}
		if($tok2[$row->opt2_idx])$opt2 = " [".($tok2[$row->opt2_idx])."]";
		else $opt2 ='';
$message .="<tr><td align=center>".$cnt."</td><td align=center>".$row->productname.$opt1.$opt2."</td><td align=center>".$row->production."</td><td align=center>".$row->quantity."개</td><td align=right style='padding-right:5'>".number_format($before_sellprice)."원</td><td align=right style='padding-right:5'>".number_format($salemoney)."원</td><td align=right style='padding-right:5'>".number_format($price)."원</td><td align=right style='padding-right:5'>".number_format($vat)."원</td></tr>";
	}
	pmysql_free_result($result);

$message .="</table><table border=0 cellpadding=0 cellspacing=0><tr><td height=2></td></tr>
</table><table border=0 cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed'  id='glvtalbe'><col width=30></col><col width=></col><col width=90></col><col width=50></col><col width=80></col><col width=80></col><col width=70></col><tr bgcolor=#ffffff><td colspan=5 align=right >상품금액</td><td colspan=2 align=right style='padding-right:5'>".number_format($total)."원</td></tr><tr bgcolor=#ffffff><td colspan=5 align=right style='padding-right:5'>부가세(10%)</td><td colspan=2 align=right style='padding-right:5'>".number_format($vat_tot)."원</td></tr><tr bgcolor=#ffffff><td colspan=5 align=right style='padding-right:5'>합 계</td><td colspan=2 align=right style='padding-right:5'>".number_format($total+$vat_tot)."원</td></tr><tr bgcolor=#f4f4f4><td colspan=7 style='padding-left:5'>비 고</td></tr><tr bgcolor=#ffffff><td colspan=7 style='padding:10'>&nbsp;</td></tr></table></body>";
$message = stripslashes($message);
$header  = 'MIME=Version: 1.0\n' . "\r\n";
$header .= 'Return-Path: <abcd@tistory.com>' . "\r\n";
$header .= 'Content-type:text/html; charset=euckr' . "\r\n"; // 캐릭터 셋과 텍스트 or html코드 쓸것인지 선언
$header .= iconv('euckr', 'euckr', 'From: 발신자<esoapschool@naver.com>'). "\r\n";
mail($to,$subject,$message,$header);
echo "메일발송";

?>
