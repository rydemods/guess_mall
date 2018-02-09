<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$ordercode = $_GET['ordercode'];
$taxrate = 10; // 세액 %

$sql="SELECT * FROM tblshopinfo ";
$result =pmysql_query($sql,get_db_conn());
if($row= pmysql_fetch_object($result)){
	$companyname=$row->companyname;
	$companynum=$row->companynum;
	$companyowner=$row->companyowner;
	$companybiz=$row->companybiz;
	$companyitem=$row->companyitem;
	$companyaddr=$row->companyaddr;
}
pmysql_free_result($result);

$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$totalprice=$row->price;
	$reserve=$row->reserve;
	$deli_price=$row->deli_price;
	$dc_price=$row->dc_price;
	$sender_name=$row->sender_name;
	$paymethod=$row->paymethod;
}
pmysql_free_result($result);
$sql="SELECT * FROM tblorderproduct WHERE ordercode='{$ordercode}' ";
$result=pmysql_query($sql,get_db_conn());
$count=0;
$etcdata=array();
while($row=pmysql_fetch_object($result)){
	$productname[$count]=$row->productname;
	$quantity[$count]=$row->quantity;
	$opt1_name[$count]=$row->opt1_name;
	$productprice[$count++]=$row->price;
	$productvattotal+=$row->price;
}
pmysql_free_result($result);

$year=date("Y");
$month=date("m");
$day=date("d");

/*
$sql = "SELECT oc.ordercode, oc.coupon_code, oc.productcode, oc.ci_no, oc.dc_price as price, pr.productname ";
$sql.= "FROM tblcoupon_order oc ";
$sql.= "LEFT JOIN tblproduct pr ON oc.productcode = pr.productcode ";
$sql.= "WHERE oc.ordercode = '".$ordercode."' ORDER BY oc.op_idx ASC ";
$result=pmysql_query($sql,get_db_conn());
while( $row = pmysql_fetch_object( $result ) ){
	$etcdata[] = $row;
}
pmysql_free_result( $result );
*/

if($addtax!="Y") {
	$totalsale=round($totalprice/(1+$taxrate/100));
	$totaltax=$totalprice-$totalsale;
	$totalsumprice=$totalprice;
} else {
	$totalsale=$totalprice;
	$totaltax=($totalprice*($taxrate/100));
	$totalsumprice=$totalsale+$totaltax;
}

?>
<STYLE TYPE=text/css>
.c01 { 
	font-family: 굴림; 
	font-size: 9pt; 
	color: blue;
	font-weight: normal;
	background: white;
}
.c02 {
	font-family: 굴림; 
	font-size: 9pt; 
	color: red;
	font-weight: normal;
	background: white;
}
tr,td {
	color: black;
	font-weight: normal;
	font-family: 굴림; 
	font-size: 9pt; 
}
.nip {
	background=#FFFFFF;
	font-size:9pt;
	font-weight: bold;
	border:0x;
}
</STYLE>
<script>

function printok(){
	if(confirm('영수증 내역을 보고 계십니다. 이정보를 출력하시겠습니까')){
		print();
	}
}

</script>
<div id=taxprint4 >
	<table width=620 border=1 cellspacing=0 cellpadding=0 bordercolor=blue style="table-layout:fixed">
	<tr>
		<td>
		<table width=100% border=1 cellspacing=0 cellpadding=0 bordercolor=blue>
		<tr>
			<td colspan=2>

			<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void  style="table-layout:fixed">
			<tr>
				<td rowspan=2 width=70% height=40>

				<table border=0 cellspacing=0 cellpadding=0>
				<tr align=center>
					<td width=160></td>
					<td rowspan=2><font color=blue size=4><b>거 래 명 세 표</b></font>&nbsp;&nbsp;</td>
					<td rowspan=2 class=c01><font size=5>(</font></td>
					<td class=c01>공 급 받 는 자</td>
					<td rowspan=2 class=c01><font size=5>)</font></td>
				</tr>
				<tr align=center>
					<td align=left>&nbsp;</td>
					<td class=c01>보 관 용</td>
				</tr>
				</table>

				</td>
				<td align=center width=10% class=c01> 책 번 호 </td>
				<td width=10% colspan=3 align=right class=c01>권</td>
				<td width=10% colspan=3 align=right class=c01>호</td>
			</tr>
			<tr>
				<td align=center class=c01> 일련번호 </td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			</table>

			</td>
		</tr>
		<tr>
			<td width=50%>

			<table width=100% height=100% border=0 cellspacing=0 cellpadding=2 bordercolor=blue frame=void>
			<tr>
				<td colspan=2>&nbsp;&nbsp;<?=$year?> <font color=blue><b>년</b></font>&nbsp; <?=$month?> <font color=blue><b>월</b></font>&nbsp; <?=$day?> <font color=blue><b>일</b></font></td>
				<td style="font-family:times new roman;" width=30% class=c01><i>no.</i></td>
			</tr>
			<tr>
				<td></td>
				<td colspan=2 valign=bottom align=right style="border-bottom:thin solid;padding-right:10px;" class=c01>
				&nbsp;<font color=black size=3><B><?=$sender_name?></B></font>&nbsp;
				<font size=3>귀하</font></td>
			</tr>
			<tr>
				<td></td>
				<td colspan=2 valign=bottom class=c01>아래와 같이 공급합니다.</td>
			</tr>
			<tr>
				<td width=20% align=center class=c01>합계금액</td>
				<td colspan=2 align=right style="padding-right:10px;" class=c01>&nbsp;<font color=black size=3><B><?=number_format($totalsumprice)?></B></font>&nbsp;<font size=3>원정</font></td>
			</tr>
			</table>

			</td>
			<td width=50%>

			<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void  style="table-layout:fixed">
			<col width=20></col>
			<col width=54></col>
			<col width=></col>
			<col width=18></col>
			<col width=></col>
			<tr align=center height=35>
				<td rowspan=4 class=c01>공<br><br>급<br><br>자</td>
				<td class=c01 style="padding-top:3px">등록번호</td>
				<td colspan=3 style="padding-left:5px;padding-top:3px"><font size=3><B><?=$companynum?></B></font></td>
			</tr>
			<tr align=center height=35>
				<td class=c01 style="padding-top:3px">상<img width=20 height=0>호<br>(법인명)</td>
				<td style="padding-left:5px;padding-top:3px"><B><?=$companyname?></B></td>
				<td class=c01 style="padding-top:3px">성명</td>
				<td style="padding-left:5px"><B><?=$companyowner?></B><img src=<?=$Dir.AdminDir?>images/taxprint_sign.gif align=absmiddle hspace=2></td>	
			</tr>
			<tr height=35>
				<td align=center class=c01 style="padding-top:3px">사 업 장<br>주<img width=20 height=0>소</td>
				<td colspan=3 style="padding-left:5px;padding-top:3px"><B><?=$companyaddr?></B></td>
			</tr>
			<tr align=center height=35>
				<td class=c01 style="padding-top:3px">업 태</td>
				<td style="padding-top:3px"><B><?=$companybiz?></B></td>
				<td class=c01 style="padding-top:3px">종목</td>
				<td style="padding-top:3px"><B><?=$companyitem?></B></td>	
			</tr>
			</table>

			</td>
		</tr>
		<tr>
			<td colspan=2>

			<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void style="table-layout:fixed">
			<col width=18></col>
			<col width=18></col>
			<col width=></col>
			<col width=33></col>
			<col width=33></col>
			<col width=59></col>
			<col width=61></col>
			<col width=45></col>
			<tr align=center>
				<td class=c01>월</td>
				<td class=c01>일</td>
				<td class=c01>품목 / 규격</td>
				<td class=c01>단위</td>
				<td class=c01>수량</td>
				<td class=c01>단가</td>
				<td class=c01>공급가액</td>
				<td class=c01>세 액</td>	
			</tr>
			<?php for($cnt=0;$cnt<$count;$cnt++) {?>
			<tr align=center>
				<td style="font-size:8pt"><?=$month?></td>
				<td style="font-size:8pt"><?=$day;?></td>
				<td style="font-size:8pt"><?=$productname[$cnt]?></td>
				<td></td>
				<td style="font-size:8pt"><?=number_format($quantity[$cnt])?></td>
				<?php
				if($addtax!="Y") {
					$taxsum=round($productprice[$cnt]/(1+$taxrate/100));
					$taxsumquantity=round($productprice[$cnt]*$quantity[$cnt]/(1+$taxrate/100));
					$taxsumsale=$productprice[$cnt]*$quantity[$cnt]-$taxsumquantity;
				} else {
				  $taxsum=$productprice[$cnt];
				  $taxsumquantity=$productprice[$cnt]*$quantity[$cnt];
				  $taxsumsale=$productprice[$cnt]*$quantity[$cnt]*($taxrate/100);
				}
				?>
				<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt"><?=number_format($taxsumquantity)?></td>
				<td align=right style="font-size:8pt"><?=number_format($taxsumsale);?></td>
			</tr>
			<?php }?>
			<?php for($k=0;$k<count($etcdata);$k++){
				$cnt++;
			?>
			<tr align=center>
				<td style="font-size:8pt"><?=$month?></td>
				<td style="font-size:8pt"><?=$day;?></td>
				<td style="font-size:8pt"><?=$etcdata[$k]->productname?></td>
				<td></td>
				<td style="font-size:8pt">1</td>
			<?php if($addtax!="Y"){
				  $taxsum=round($etcdata[$k]->price/(1+$taxrate/100));
				  $taxsumsale=$etcdata[$k]->price-$taxsum;
			   }else {
				  $taxsum=$etcdata[$k]->price;
				  $taxsumsale=$taxsum*($taxrate/100);
			   }
			?>
				<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt"><?=number_format($taxsumsale);?></td>
			</tr>
			<?php }?>
			<?php if($deli_price>0) {
			   $cnt++;
			?>
			<tr align=center>
				<td style="font-size:8pt"><?=$month?></td>
				<td style="font-size:8pt"><?=$day;?></td>
				<td style="font-size:8pt">배송료</td>
				<td>&nbsp;</td>
				<td style="font-size:8pt">1</td>
			<?php if($addtax!="Y"){
				  $taxsum=round($deli_price/(1+$taxrate/100));
				  $taxsumsale=$deli_price-$taxsum;
			   }else {
				  $taxsum=$deli_price;
				  $taxsumsale=$taxsum*($taxrate/100);
			   }
			?>
				<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt"><?=number_format($taxsumsale);?></td>
			</tr>
			<?php }?>
			<?php if($reserve>0){
			   $cnt++;
			?>
			<tr align=center>
				<td style="font-size:8pt"><?=$month?></td>
				<td style="font-size:8pt"><?=$day;?></td>
				<td style="font-size:8pt">적립금</td>
				<td>&nbsp;</td>
				<td style="font-size:8pt">1</td>
			<?php if($addtax!="Y"){
				  $taxsum=round($reserve/(1+$taxrate/100));
				  $taxsumsale=$reserve-$taxsum;
			   }else {
				  $taxsum=$reserve;
				  $taxsumsale=$taxsum*($taxrate/100);
			   }
			?>
				<td align=right style="font-size:8pt">-<?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt">-<?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt">-<?=number_format($taxsumsale);?></td>
			</tr>
			<?php }?>
			<?php if($dc_price<0){
			   $cnt++;
			?>
			<tr align=center>
				<td style="font-size:8pt"><?=$month?></td>
				<td style="font-size:8pt"><?=$day;?></td>
				<td style="font-size:8pt">우수회원 할인</td>
				<td>&nbsp;</td>
				<td style="font-size:8pt">1</td>
			<?php if($addtax!="Y"){
				  $taxsum=round($dc_price/(1+$taxrate/100));
				  $taxsumsale=$dc_price-$taxsum;
			   }else {
				  $taxsum=$dc_price;
				  $taxsumsale=$taxsum*($taxrate/100);
			   }
			?>
				<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
				<td align=right style="font-size:8pt"><?=number_format($taxsumsale);?></td>
			</tr>
			<?php } 
			  if($cnt<5){ 
				 $cnt++;
			?>
			<tr><td colspan=8 align=center class=c01> ***** 이 하 여 백 ***** </td></tr>
			<?php }
			  for($i=$cnt;$i<5;$i++){?>
			<tr align=center>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align=right>&nbsp;</td>
				<td align=right>&nbsp;</td>
				<td align=right>&nbsp;</td>
			</tr>
			<?php }?>
			<tr align=center>
				<td colspan=6 class=c01> 소 계 </td>
				<td align=right><B><?=number_format($totalsale)?></B></td>
				<td align=right><B><?=number_format($totaltax)?></B></td>
			</tr>
			</table>

			</td>
		</tr>
		<tr>
			<td colspan=2 height=26>

			<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void style="table-layout:fixed">
			<col width=70></col>
			<col width=120></col>
			<col width=70></col>
			<col width=120></col>
			<col width=70></col>
			<col width=></col>
			<tr align=center>
				<td class=c01> 미수금 </td>
				<td>&nbsp;</td>
				<td class=c01> 합 계 </td>
				<td>&nbsp;<B><?=number_format($totalsumprice)?></B></td>
				<td style="border-right:0" class=c01> 인수자 </td>
				<td align=right style="border-left:0">&nbsp;<img src=<?=$Dir.AdminDir?>images/taxprint_sign.gif align=absmiddle hspace=10></td>
			</tr>
			</table>

			</td>
		</tr>
		</table>

		</td>
	</tr>
	</table>
	<br>
	</div>
	<div>
		<button onclick='javascript:print();'> 인 쇄 </button>
	</div>