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


?>
<html>
<head>

<title></title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {font-family:돋음;font-size:9pt;}

tr {font-family:돋음;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;font-size:9pt;}

@media print { .notprint {display: none;} } /* 인쇄시 불필요한 부분 비활성화 */


#glvtalbe {

	border-collapse: collapse;
}

#glvtalbe td{
	
	border:1px solid #999;
}

</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
resizeTo(700,620);
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
<tr>
	<td align=center>
	<table border=0 cellpadding=0 cellspacing=0 width=50%>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center style="font-size:28"><B>견 적 서</B></td>
	</tr>
	<tr><td height=10></td></tr>
	<tr><td height=1 bgcolor=#787878></td></tr>
	</table>
	</td>
</tr>
<tr><td height=10></td></tr>
<tr>
	<td valign=bottom style="padding:7">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr>
		<td width=40% valign=bottom>
		<table border=0 cellpadding=0 cellspacing=0 width=100% id="glvtalbe">
		<col width=60></col>
		<col width=></col>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">견적일자</td>
			<td style="padding-left:5"><?php echo date("Y")."년 ".date("m")."월 ".date("d")."일 ".date("H")."시 ".date("i")."분"?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">유효기간</td>
			<td style="padding-left:5">견적 후 일주일</td>
		</tr>
		</table>
		</td>
		<td width=4% nowrap></td>
		<td width=56%>
		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed"  id="glvtalbe">
		<col width=120></col>
		<col width=></col>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5;position:relative">
				사업자등록번호
				<img src="../data/shopimages/etc/est_seal.png" style="position:absolute;top:2px;left:220px;width:60px;">
			</td>
			<td style="padding-left:5"><?=$_data->companynum?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">회 사 명</td>
			<td style="padding-left:5"><?=$_data->companyname?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">판 매 원</td>
			<td style="padding-left:5"><?=$_data->shopname?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">대표자 성명</td>
			<td style="padding-left:5"><?=$_data->companyowner?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">업태/종목</td>
			<td style="padding-left:5"><?=$_data->companybiz?> / <?=$_data->companyitem?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">사업장 주소</td>
			<td style="padding-left:5"><?=$_data->companyaddr?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">사업장 전화번호</td>
			<td style="padding-left:5"><?=$_data->info_tel?></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td style="padding:7">
<?php 
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



		$estlist.="<tr>\n";
		$estlist.="	<td align=center>{$cnt}</td>\n";
		$estlist.="	<td align=center>".viewselfcode($row->productname,$row->selfcode).$opt1.$opt2."</td>\n";
		$estlist.="	<td align=center>{$row->production}</td>\n";
		$estlist.="	<td align=center>{$row->quantity}개</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format($before_sellprice)."원</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format($salemoney)."원</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format($price)."원</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format($vat)."원</td>\n";
		$estlist.="</tr>\n";
	}
	pmysql_free_result($result);
?>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed;">
	<tr>
		<td width=50%>※ 아래와 같이 견적합니다.</td>
		<td width=50% align=right>견적합계 : <?=number_format($total)?>원, VAT포함</td>
	</tr>
	</table>
	
	
	
	
	<div style="background:url(../data/shopimages/etc/est_background.png) no-repeat 220px 50px">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed;"  id="glvtalbe" >
	<col width=30></col>
	<col width=></col>
	<col width=90></col>
	<col width=50></col>
	<col width=80></col>
	<col width=80></col>
	<col width=70></col>
	<col width=70></col>
	<tr bgcolor="f4f4f4" height=25>
		<td align=center>No</td>
		<td align=center>상품명</td>
		<td align=center>제조사</td>
		<td align=center>수량</td>
		<td align=center>상품단가</td>
		<td align=center>회원할인</td>
		<td align=center>상품금액</td>
		<td align=center>세액</td>
	</tr>
<?php 
	echo $estlist;
	if($cnt<15) {
		for($i=$cnt;$i<=15;$i++) {
			echo "<tr>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "</tr>\n";
		}
	}
?>
	</table>
	</div>



	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td height=2></td></tr>
	</table>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed"  id="glvtalbe">
	<col width=30></col>
	<col width=></col>
	<col width=90></col>
	<col width=50></col>
	<col width=80></col>
	<col width=80></col>
	<col width=70></col>
	<tr bgcolor=#ffffff>
		<td colspan=5 align=right style="padding-right:5">상품금액</td>
		<td colspan=2 align=right style="padding-right:5"><?=number_format($total)?>원</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td colspan=5 align=right style="padding-right:5">부가세(10%)</td>
		<td colspan=2 align=right style="padding-right:5"><?=number_format($vat_tot)?>원</td>
	</tr>

	<tr bgcolor=#ffffff>
		<td colspan=5 align=right style="padding-right:5">합 계</td>
		<td colspan=2 align=right style="padding-right:5"><?=number_format($total+$vat_tot)?>원</td>
	</tr>
	<tr bgcolor=#f4f4f4>
		<td colspan=7 style="padding-left:5">비 고</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td colspan=7 style="padding:10">
		<?=$_data->estimate_msg?>&nbsp;
		</td>
	</tr>
	</table>
	</td>
</tr>

<tr class="notprint">
	<td align=center style="padding-top:10">
	<A HREF="javascript:print()"><img src="<?=$Dir?>images/common/estimate/icon_print.gif" border=0></A>
	&nbsp;
	<A HREF="javascript:window.close();"><img src="<?=$Dir?>images/common/estimate/icon_close.gif" border=0></A>
	</td>
</tr>

<tr><td height=20 ></td></tr>
</table>
<script>print();</script>
</body>
</html>
