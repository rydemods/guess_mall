<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

if(ord($_ShopInfo->getId())==0) {
	alert_go('정상적인 경로로 접근하시기 바랍니다.','c');
}

$ordercode=rtrim($_POST["ordercodes"],',');
$gbn=$_POST["gbn"];

$CurrentTime = time();

if(ord($ordercode)==0) {
	alert_go('정상적인 경로로 접근하시기 바랍니다.','c');	
}

$card_payfee=$_shopdata->card_payfee;

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$delicomlist=array();
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$delicomlist[]=$row;
}
pmysql_free_result($result);

?>
<html>
<head>
<title>주문서 출력</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=utf-8">
<STYLE TYPE="text/css"> 
<!--
body { font-size: 9pt}
td { font-size: 9pt}
tr { font-size: 9pt}
.break {page-break-before: always;}
--> 
</STYLE> 
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
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onload="this.focus();print();">
<center>
<?php
$arrordercode = explode(",",$ordercode);
$cnt = count($arrordercode);
for($i=0;$i<$cnt;$i++) {
	if($i<>0) echo "<H1 CLASS=\"break\"><br style=\"height=0;line-height:0;\">\n";



$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$arrordercode[$i]}' ";

echo $sql;
$result=pmysql_query($sql,get_db_conn());
$_ord=pmysql_fetch_object($result);
pmysql_free_result($result);


list($group_name)=pmysql_fetch("select group_name from tblmember a left join tblmembergroup b on (a.group_code=b.group_code) where a.id='{$_ord->id}'");


$order_date=substr($_ord->ordercode,0,4)."-".substr($_ord->ordercode,4,2)."-".substr($_ord->ordercode,6,2)." ".substr($_ord->ordercode,8,2).":".substr($_ord->ordercode,10,2).":".substr($_ord->ordercode,12,2);
?>

<table border=0 cellpadding=0 cellspacing=0 width=96%>
<tr><td align=center><B>주문해주셔서 감사합니다.</B></td></tr>
<tr><td height=10></td></tr>
</table>

<table border=0 cellpadding=0 cellspacing=0 width=645 bgcolor=#FFFFFF style="table-layout:fixed">
	<colgroup>
		<col width=500 /><col width= />
	</colgroup>
	<tr valign=top>
		<td>
		
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<tr height=40>
				<td colspan=2 align=center><span style="font-size:22px; font-weight:bold;">주 문&nbsp;&nbsp;&nbsp;내 역 서</span></td>
			</tr>
			<tr>
				<td><span style="font-size:16px; font-weight:bold;">Order No : <?=$arrordercode[$i]?></td>
				<td align=right>&nbsp;</span></td>
			</tr>
		</table>

		</td>
		
		<td align=right>
		<table border=1 cellpadding=3 cellspacing=0 width=120 bgcolor=#FFFFFF style="table-layout:fixed">
			<colgroup>
			<col width=20 /><col width=50 /><col width=50 />
		  </colgroup>
		  <tr>
			<td rowspan="2">확<br />인</td>
			<td align="center">준 비</td>
			<td align="center">담당자</td>
		  </tr>
		  <tr height="40">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		</table>

		</td>
	</tr>
	
</table>

<br />

<table border=0 cellpadding=0 cellspacing=0 width=650 style="table-layout:fixed">
	<tr>
		<td align=right><iframe src="http://www.barcodesinc.com/generator/image.php?code=<?=$_ord->ordercode?>&style=197&type=C128B&width=300&height=50&xres=1&font=3" width="325" height="85" frameborder=0></iframe></td>
	</tr>
 </table>


<style type="text/css">
table.order_sheet_table table {border-top:1px solid #000;}
table.order_sheet_table th {text-align:left; font-weight:lighter; padding:3px 0px; border-top:1px solid #000;}
table.order_sheet_table th.bd_none {border:none;}
table.order_sheet_table td.bd_none {border:none;}
table.order_sheet_table td {border-top:1px solid #000;}
table.order_sheet_table th.bdb {border-bottom:1px solid #000;}
table.order_sheet_table td.bdb {border-bottom:1px solid #000;}
table.order_sheet_table ul {margin:0px; padding:0px; margin-top:1px;overflow:hidden;}
table.order_sheet_table ul li {float:left;}
table.order_sheet_table ul li.price {width:100px; text-align:right;}
table.order_sheet_table ul li.space {margin-left:20px;}
</style>
<?
	$order_msg=explode("[MEMO]",$_ord->order_msg);
	$order_msg[0]=str_replace("\"","",$order_msg[0]);
	$order_msg[0]=preg_replace("/^(\r\n)*/","&nbsp;&nbsp;",$order_msg[0]);
	$order_msg[0]=str_replace("\r\n","<br>&nbsp;&nbsp;",$order_msg[0]);

	$order_msg2=$_ord->order_msg2;

	$order_prmsg="";
?>
<table border=0 cellpadding=0 cellspacing=0 width=645 class="order_sheet_table">
	<colgroup>
		<col width=85 /><col width=245 /><col width=70 /><col width=245 />
	</colgroup>
	<tr>
		<th>수령자 :</th>
		<td><?=$_ord->receiver_name?></td>
		<th>연락처 :</th>
		<td><?=$_ord->receiver_tel1?> / <?=$_ord->receiver_tel2?></td>
	</tr>
	<tr>
		<th class="bd_none">주소 : </th>
		<td class="bd_none" colspan=3><?=$_ord->receiver_addr?></td>
	</tr>
	<tr>
		<th>주문자 :</th>
		<td><?=$_ord->sender_name?> (<?=$group_name?>)</td>
		<th>주문일 :</th>
		<td><?=$order_date?></td>
	</tr>
	<tr>
		<th class="bd_none">이메일 : </th>
		<td class="bd_none"><?=$_ord->sender_email?></td>
		<th class="bd_none">연락처 : </th>
		<td class="bd_none"><?=$_ord->sender_tel?></td>
	</tr>
	<tr>
		<th>주문금액 : </th>
		<td colspan=3>
			<ul>
				<li>[</li>
				<li class="price"><?=number_format($_ord->price+abs($_ord->dc_price)+$_ord->reserve)?>원</li>
				<li>]</li>
				<li class="space">상품가격 [<?=number_format($_ord->price + abs($_ord->dc_price) +$_ord->reserve - $_ord->deli_price)?>원] + 배송비 [<?=number_format($_ord->deli_price)?>원]</li>
				<li class="space">배송방법 : 기본 배송</li>
			</ul>
		</td>
	</tr>
	<tr>
		<th>할인금액 : </th>
		<td colspan=3>
			<ul>
				<li>[</li>
				<li class="price"><?=number_format(abs($_ord->dc_price)+$_ord->reserve)?>원</li>
				<li>]</li>
				<li class="space"><!--에누리 [20,050원]--></li>
			</ul>
		</td>
	</tr>
	<tr>
		<th >결제금액 : </th>
		<td>
			<ul>
				<li>[</li>
				<li class="price"><?=number_format($_ord->price)?>원</li>
				<li>]</li>
			</ul>
		</td>
		<th>결제확인일 : </th>
		<td colspan=3 ><?=$_ord->bank_date?>&nbsp;</td>
	</tr>

	<?if(ord($order_msg[0])){?>
	<tr>
		<th>주문요청사항 : </th>
		<td colspan=3><?=$order_msg[0]?></td>
	</tr>
	<?}?>

	<tr>
		<th class="bdb" >주문메세지 : </th>
		<td class="bdb" colspan=3><?=$order_msg2?></td>
	</tr>


	<!--tr>
		<th class="bdb">고객요청사항 : </th>
		<td class="bdb" colspan=3>빠른배송</td>
	</tr-->
</table>
<br />
<table border=1 cellpadding=3 cellspacing=0 width=645 bgcolor=#FFFFFF style="table-layout:fixed">
<col width=40></col>
<?php if($vendercnt>0 && $gbn=="Y"){?>
<col width=60></col>
<?php }?>
<col width='*'></col>
<col width=95></col>
<col width=40></col>
<col width=40></col>
<col width=40></col>
<col width=70></col>
<col width=70></col>
<col width=70></col>
<tr bgcolor=#f4f4f4>
	<td align=center>번호</td>
	<?php if($vendercnt>0 && $gbn=="Y"){?>
	<td align=center>입점업체</td>
	<?php }?>
	<td align=center>상품명</td>
	<td align=center>옵션</td>
	<td align=center>수량</td>
	<td align=center>체크</td>
	<td align=center>기타</td>
	<td align=center>위치</td>
	<td align=center>결제가격</td>
	<td align=center>금액</td>
</tr>
<?php
$colspan=9;
if($vendercnt>0 && $gbn=="Y") $colspan++;

$sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$arrordercode[$i]}' order by productcode";
$result=pmysql_query($sql,get_db_conn());
$no=0;
$sumprice=0;
$sumreserve=0;
$totprice=0;
$totreserve=0;
$totquantity=0;
$etcdata=array();
$prdata=array();
while($row=pmysql_fetch_object($result)) {

	//상품수량체크
	if($row->quantity>1)$fontsize="12";
	else $fontsize="8";

	//상품이미지,금액
	list($product_img)=pmysql_fetch("select tinyimage from tblproduct where productcode='".$row->productcode."'");
	list($position)=pmysql_fetch("select position from tblproduct where productcode='".$row->productcode."'");
	if(preg_match("/^COU\d{8}X$/",$row->productcode)) {				#쿠폰
		if($row->price!=0 && $row->price!=NULL) {
			$etcdata[]=$row;
			continue;
		}
	} else if(preg_match("/^9999999999\d(X|R)$/",$row->productcode)) {
		#99999999999X : 현금결제시 결제금액에서 추가적립/할인
		#99999999998X : 에스크로 결제시 수수료
		#99999999997X : 부가세(VAT)
		#99999999990X : 상품배송비
		#99999999999R : 카드수수료
		$etcdata[]=$row;
		continue;
	} else {															#진짜상품
		$prdata[]=$row;
	}

	$no++;
	$optvalue="";
	if(preg_match("/^\[OPTG\d{3}\]$/",$row->opt1_name)) {
		$optioncode=$row->opt1_name;
		$row->opt1_name="";
		$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='{$arrordercode[$i]}' ";
		$sql.= "AND productcode='{$row->productcode}' AND opt_idx='{$optioncode}' ";
		$result2=pmysql_query($sql,get_db_conn());
		if($row2=pmysql_fetch_object($result2)) {
			$optvalue=$row2->opt_name;
		}
		pmysql_free_result($result2);
	}

	$sumprice=$row->price*$row->quantity;
	$totprice+=$sumprice;
	$isnot=false;
	if ($row->productcode!="99999999999X" && substr($row->productcode,0,3)!="COU" && $row->productcode!="99999999999R") {
		$totquantity+=$row->quantity;
		$isnot=true;
	}
	$sumreserve=$row->reserve*$row->quantity;
	$totreserve+=$sumreserve;

	$assemblestr = "";
	$packagestr = "";
	if(($_ord->paymethod!="B" || $mode!="update") && $row->assemble_idx>0 && ord(str_replace("","",str_replace(":","",$row->assemble_info)))) {
		$assemble_infoall_exp = explode("=",$row->assemble_info);

		if($row->package_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))) {
			$package_info_exp = explode(":", $assemble_infoall_exp[0]);

			$package_productcode_exp = explode("", $package_info_exp[0]);
			$package_productname_exp = explode("", $package_info_exp[1]);
			$package_sellprice = $package_info_exp[2];
			$package_packagename = $package_info_exp[3];
			
			if(count($package_info_exp)>2 && ord($package_packagename)) {
				$packagestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
				$packagestr.="	<tr>\n";
				$packagestr.="		<td colspan=\"2\" style=\"word-break:break-all;font-size:8pt;\"><font color=green><b>[</b>패키지선택 : {$package_packagename}<b>]</b></font></td>\n";
				$packagestr.="	</tr>\n";
				if(ord(str_replace("","",$package_info_exp[1]))) {
					$packagestr.="	<tr>\n";
					$packagestr.="		<td width=\"30\" valign=\"top\" nowrap><font color=\"#008000\" style=\"line-height:10px;\">│<br>└▶</font></td>\n";
					$packagestr.="		<td width=\"100%\" bgcolor=\"#DDDDDD\">\n";
					$packagestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\">\n";
					$packagestr.="		<col width=\"\"></col>\n";
					$packagestr.="		<col width=\"55\"></col>\n";
					for($k=0; $k<count($package_productname_exp); $k++) {
						if($k==0) {
							$packagestr.="		<tr bgcolor=\"#FFFFFF\">\n";
							$packagestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">{$package_productname_exp[$k]}&nbsp;</td>\n";
							$packagestr.="				<td rowspan=\"".count($package_productname_exp)."\" align=\"right\" style=\"padding-left:4px;padding-right:4px;font-size:8pt;\">".number_format((int)$package_sellprice)."</td>\n";
							$packagestr.="		</tr>\n";
						} else {
							$packagestr.="		<tr bgcolor=\"#FFFFFF\">\n";
							$packagestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">{$package_productname_exp[$k]}&nbsp;</td>\n";
							$packagestr.="		</tr>\n";
						}
					}

					$packagestr.="		</table>\n";
					$packagestr.="		</td>\n";
					$packagestr.="	</tr>\n";
				}
				$packagestr.="	</table>\n";
			}
		}

		if($row->assemble_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))) {
			$assemblestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
			$assemblestr.="	<tr height=\"2\"><td></td></tr>\n";
			$assemblestr.="	<tr>\n";
			$assemblestr.="		<td width=\"30\" valign=\"top\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">│<br>└▶</font></td>\n";
			$assemblestr.="		<td width=\"100%\" bgcolor=\"#DDDDDD\">\n";
			$assemblestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\">\n";
			
			$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

			if(count($assemble_info_exp)>2) {
				$assemble_productcode_exp = explode("", $assemble_info_exp[0]);
				$assemble_productname_exp = explode("", $assemble_info_exp[1]);
				$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

				for($k=0; $k<count($assemble_productname_exp); $k++) {
					$assemblestr.="		<col width=\"\"></col>\n";
					$assemblestr.="		<col width=\"55\"></col>\n";
					$assemblestr.="		<tr bgcolor=\"#FFFFFF\">\n";
					$assemblestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">{$assemble_productname_exp[$k]}&nbsp;</td>\n";
					$assemblestr.="				<td align=\"right\" style=\"padding-left:4px;padding-right:4px;font-size:8pt;\">".number_format((int)$assemble_sellprice_exp[$k])."</td>\n";
					$assemblestr.="		</tr>\n";
				}
			}
			$assemblestr.="		</table>\n";
			$assemblestr.="		</td>\n";
			$assemblestr.="	</tr>\n";
			$assemblestr.="	</table>\n";
		}
	}

	echo "<tr bgcolor=#FFFFFF>\n";
	echo "	<td align=center style=\"font-size:8pt\">{$no}</td>\n";
	if($vendercnt>0 && $gbn=="Y") {
		if($row->vender>0) {
			echo "	<td align=center style=\"font-size:8pt\">{$venderlist[$row->vender]->id}</td>\n";
		} else {
			echo "	<td align=center>&nbsp;</td>\n";
		}
	}
	echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">";
	/*echo "<img src='../data/shopimages/product/{$product_img}' style='width:40px;'>";*/
	echo $row->productname;
	if(ord($row->addcode)) echo "<br><font color=blue><b>[</b>특수표시 : {$row->addcode}<b>]</b></font>";
	if(ord($optvalue)) echo "<br><font color=red><b>[</b>옵션사항 : {$optvalue}<b>]</b></font>";
	echo $packagestr;
	echo $assemblestr;
	echo "	</td>\n";
	echo "	<td style=\"font-size:8pt;padding:2,5;line-height:11pt\">";
	echo (ord($row->opt1_name)?$row->opt1_name."<br>":"&nbsp;");
	if(ord($row->opt2_name)) echo $row->opt2_name;
	echo "	</td>\n";
	echo "	<td align=center style=\"font-size:".$fontsize."pt\">".($isnot?$row->quantity:"&nbsp;")."</td>\n";
	echo "	<td align=center style=\"font-size:8pt\">&nbsp;</td>\n";
	echo "	<td align=center style=\"font-size:8pt\">&nbsp;</td>\n";
	echo "	<td align=center style=\"font-size:8pt\">".$position."&nbsp;</td>\n";
	//echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($sumreserve)."</td>\n";
	echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($sumprice/$row->quantity)."</td>\n";
	echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($sumprice)."</td>\n";
	echo "</tr>\n";


	$deliveryData = "";
	$deliveryData = "<table>";
	$deliveryData .= "<tr bgcolor=#FFFFFF style=\"font-size:8pt\">\n";
	$deliveryData .= "	<td style=\"padding:2,5\" colspan={$colspan}>\n";
	$deliveryData .= "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$deliveryData .= "	<col width=150><col width=150><col width='*'>\n";	
	$deli_url="";
	$trans_num="";
	$company_name="";
	for($yy=0;$yy<count($delicomlist);$yy++) {
		if($pg_type=="B" && strstr("QP", $_ord->paymethod[0])) {
			if(ord($delicomlist[$yy]->dacom_code)) {
				if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
					$deli_url=$delicomlist[$yy]->deli_url;
					$trans_num=$delicomlist[$yy]->trans_num;
					$company_name=$delicomlist[$yy]->company_name;
				}
			}
		} else {
			if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
				$deli_url=$delicomlist[$yy]->deli_url;
				$trans_num=$delicomlist[$yy]->trans_num;
				$company_name=$delicomlist[$yy]->company_name;
			}
		}
	}
	$deliveryData .= "	<tr>\n";
	$deliveryData .= "		<td>배송업체 : \n";
	$deliveryData .= "		".(ord($company_name)?$company_name:"없음")."\n";
	$deliveryData .= "		</td>\n";
	$deliveryData .= "		<td>송장번호 : \n";
	$deliveryData .= "		".(ord($row->deli_num)?$row->deli_num:"없음")."\n";
	$deliveryData .= "		</td>\n";
	$deliveryData .= "		<td align=right>\n";
	$deliveryData .= "	배송상태 : <B>";
	switch($row->deli_gbn) {
		case 'S': $deliveryData .= "발송준비";  break;
		case 'X': $deliveryData .= "배송요청";  break;
		case 'Y': $deliveryData .= "배송";  break;
		case 'D': $deliveryData .= "<font color=blue>취소요청</font>";  break;
		case 'N': $deliveryData .= "미처리";  break;
		case 'E': $deliveryData .= "<font color=red>환불대기</font>";  break;
		case 'C': $deliveryData .= "<font color=red>주문취소</font>";  break;
		case 'R': $deliveryData .= "반송";  break;
		case 'H': $deliveryData .= "배송(<font color=red>정산보류</font>)";  break;
	}
	if($row->deli_gbn=="D" && strlen($row->deli_date)==14) $deliveryData .= " (배송)";
	$deliveryData .= "	</B>";
	$deliveryData .= "		</td>\n";
	$deliveryData .= "	</tr>\n";
	$deliveryData .= "	</table>\n";
	$deliveryData .= "	</td>\n";
	$deliveryData .= "</tr>\n";
	$deliveryData .= "</table>";



}
pmysql_free_result($result);

$deliveryColspan1 = $colspan-($colspan-2);
$deliveryColspan2 = $colspan-2;
echo "<tr height=30 bgcolor=#efefef>\n";
echo "	<td align=center colspan={$deliveryColspan1}>&nbsp;</td>";
echo "	<td align=center colspan={$deliveryColspan2}>";
echo			$deliveryData;
echo "	</td>";
echo "</tr>\n";


if(count($etcdata)>0) {
	echo "<tr height=30 bgcolor=#efefef>\n";
	echo "	<td align=center colspan={$colspan}><B>추가비용/할인/적립내역</B></td>";
	echo "</tr>\n";

	for($j=0;$j<count($etcdata);$j++) {
		$sumprice=$etcdata[$j]->price;
		$totprice+=$sumprice;
		$reserve=$etcdata[$j]->reserve;
		$totreserve+=$reserve;
		echo "<tr bgcolor=#FFFFFF>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0 && $gbn=="Y") {
			if($etcdata[$j]->vender>0) {
				echo "	<td align=center style=\"font-size:8pt\">{$venderlist[$etcdata[$j]->vender]->id}</td>\n";
			} else {
				echo "	<td align=center>&nbsp;</td>\n";
			}
		}
		echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">{$etcdata[$j]->productname}</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		if ($etcdata[$j]->productcode=="99999999999X" || $etcdata[$j]->productcode=="99999999990X" || $etcdata[$j]->productcode=="99999999997X" || substr($etcdata[$j]->productcode,0,3)=="COU" || $etcdata[$j]->productcode=="99999999999R") { // 현금결제면 수량표시안함
			echo "	<td>&nbsp;</td>\n";
		} else {
			echo "	<td align=center".($etcdata[$j]->quantity>1?" bgcolor=#FDE9D5 style=\"font-size:8pt\"><font color=#000000><b>":">").$etcdata[$j]->quantity."</td>\n";
		}
		echo "	<td align=right style=\"font-size:8pt\">";
		if($etcdata[$j]->vender>0) {
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
				if($_ord->paymethod!="B" || $mode!="update") {
					echo ($reserve>0?number_format($reserve):"")."&nbsp;";
				} else {
					echo "&nbsp;";
				}
			} else {
				echo "&nbsp;";
			}
		} else {
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
				if($_ord->paymethod!="B" || $mode!="update") {
					echo ($reserve>0?number_format($reserve):"")."&nbsp;";
				} else {
					echo "&nbsp;";
				}
			} else {
				echo "&nbsp;";
			}
		}
		echo "	</td>\n";

		echo "	<td align=right style=\"font-size:8pt\">".(substr($etcdata[$j]->productcode,-4)!="GIFT"?number_format($sumprice):"&nbsp;")."</td>\n";
		echo "</tr>\n";
	}
}

if($_ord) {
	$dc_price=(int)$_ord->dc_price;
	$salemoney=0;
	$salereserve=0;

	if($_ord->tot_price_dc){
		echo "<tr bgcolor=#FFFFE6>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0) {
			echo "	<td>&nbsp;</td>\n";
		}
		echo "	<td style=\"font-size:8pt;padding:2,5\">총 구매금액대별 할인</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">-".number_format($_ord->tot_price_dc)."</td>\n";
		echo "</tr>\n";


	}


	if($dc_price<>0) {
		if($dc_price>0) $salereserve=$dc_price;
		else $salemoney=-$dc_price;
		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
			$sql.= "WHERE a.id='{$_ord->id}' AND b.group_code=a.group_code AND SUBSTR(b.group_code,1,1)!='M' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$group_name=$row->group_name;
			}
			pmysql_free_result($result);
		}
		echo "<tr bgcolor=#FFFFE6>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0 && $gbn=="Y") {
			echo "	<td>&nbsp;</td>\n";
		}
		echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=red>그룹회원 할인 : {$group_name}</font></td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".($salereserve>0?number_format($salereserve):"&nbsp;")."</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".($salemoney>0?"-".number_format($salemoney):"&nbsp;")."</td>\n";
		echo "</tr>\n";
		$totreserve+=$salereserve;
	}
	if($_ord->reserve>0) {
		echo "<tr bgcolor=#FFFFFF>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0 && $gbn=="Y") {
			echo "	<td>&nbsp;</td>\n";
		}
		echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#0000FF>적립금 사용액</font></td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt;padding-right:3\">&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt;padding-right:3\">-".number_format($_ord->reserve)."</td>\n";
		echo "</tr>\n";
	}
	$totprice=$totprice-$salemoney-$_ord->reserve;
	if (strstr("CPM", $_ord->paymethod[0]) && $card_payfee>0 && $_ord->price<>$totprice) {
		echo "<tr bgcolor=#FFFFFF>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0 && $gbn=="Y") {
			echo "	<td>&nbsp;</td>\n";
		}
		echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#0000FF>신용카드 수수료</font></td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=right style=\"padding-right:3\">&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($_ord->price-$totprice)."</td>\n";
		echo "</tr>\n";
	}
	echo "<tr bgcolor=#FFFFFF>\n";
	echo "	<td colspan=".($colspan-7)." style=\"font-size:8pt;padding:5,27\"><B>총 합계</B> </td>\n";
	
	echo "	<td>&nbsp;</td>\n";
	echo "	<td align=center style=\"font-size:8pt\">{$totquantity}</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($_ord->price)."</td>\n";
	echo "</tr>\n";

	echo "<tr bgcolor=#FFFFFF>\n";
	echo "	<td colspan={$colspan} style=\"padding:10\">\n";
	echo "	<table border=0 cellpadding=2 cellspacing=0 width=100%>\n";
	echo "	<col width=100></col><col width=></col>\n";
	/*
	$date=substr($_ord->ordercode,0,4)."/".substr($_ord->ordercode,4,2)."/".substr($_ord->ordercode,6,2)." ".substr($_ord->ordercode,8,2).":".substr($_ord->ordercode,10,2).":".substr($_ord->ordercode,12,2);
	echo "	<tr>\n";
	echo "		<td>주문 일자</td>\n";
	echo "		<td>: {$date}</td>\n";
	echo "	</tr>\n";
	if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") $idname=$_ord->id;
	else $idname="비회원";
	echo "	<tr>\n";
	echo "		<td>주 &nbsp;문&nbsp; 자</td>\n";
	echo "		<td>: {$_ord->sender_name}({$idname})님</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td>받 &nbsp;는&nbsp; 분</td>\n";
	echo "		<td>: {$_ord->receiver_name}님</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td>받는 주소</td>\n";
	echo "		<td>: {$_ord->receiver_addr}</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td>연 &nbsp;락&nbsp; 처</td>\n";
	echo "		<td>: ".$_ord->receiver_tel1.(ord($_ord->receiver_tel2)?" , ".$_ord->receiver_tel2:"")."</td>\n";
	echo "	</tr>\n";
	*/
	if($gbn=="Y") {
		$pgdate = date("YmdHi",strtotime('-2 day'));
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

		if(($_ord->pay_data=="신용카드결제 - 카드작성중" || $_ord->pay_data=="핸드폰결제 - 작성중") && substr($_ord->ordercode,0,12)<=$pgdate) {
			$_ord->pay_data=$arpm[$_ord->paymethod[0]]." 에러";
		}

		echo "	<tr><td colspan=2 height=10></td></tr>\n";
		echo "	<tr>\n";
		echo "		<td>결제 방법</td>\n";
		echo "		<td>: ";
		if (strstr("BOQ",$_ord->paymethod[0])) {	//무통장, 가상계좌, 가상계좌 에스크로
			if($_ord->paymethod=="B") echo "<font color=#FF5D00>무통장입금</font>\n";
			else if($_ord->paymethod[0]=="O") echo "<font color=#FF5D00>가상계좌</font>\n";
			else echo "매매보호 - 가상계좌";

			if(!strstr("CD",$_ord->deli_gbn) || $_ord->paymethod=="B") echo "【 {$_ord->pay_data} 】";
			else echo "【 계좌 취소 】";

			if (strlen($_ord->bank_date)>=12) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td align=center bgcolor=#efefef>입금확인</td>\n";
				echo "	<td bgcolor=#ffffff style=\"padding-left:10\"><font color=red>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")</font>";
			} else if(strlen($_ord->bank_date)==9) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td align=center bgcolor=#efefef>입금확인</td>\n";
				echo "	<td bgcolor=#ffffff style=\"padding-left:10\">환불";
			}
		} else if($_ord->paymethod[0]=="M") {	//핸드폰 결제
			echo "핸드폰 결제【 ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>결제취소 완료</font> 】";
				else echo "<font color=red>결제가 성공적으로 이루어졌습니다.</font>";
			}
			else echo "결제가 실패되었습니다.";
			echo " 】";
		} else if($_ord->paymethod[0]=="P") {	//매매보호 신용카드
			echo "매매보호 - 신용카드";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				else if($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
			}
			else echo "【 {$_ord->pay_data} 】";
		} else if ($_ord->paymethod[0]=="C") {	//일반신용카드
			echo "<font color=#FF5D00>신용카드</font>\n";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				else if($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
			}
			else echo "【 {$_ord->pay_data} 】";
		} else if ($_ord->paymethod[0]=="V") {
			echo "실시간 계좌이체 : ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=005000> [환불]</font> 】";
				else echo "<font color=red>{$_ord->pay_data}</font>";
			}
			else echo "결제가 실패되었습니다.";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		if(strlen($_ord->bank_date)==14) {
			$bank_date=substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")";
			echo "	<tr>\n";
			echo "		<td>입금 확인</td>\n";
			echo "		<td>: {$bank_date}</td>\n";
			echo "	</tr>\n";
		}
		$deli_array=array("S"=>"발송준비","X"=>"배송요청","Y"=>"배송","D"=>"취소요청","N"=>"미처리","E"=>"환불대기","C"=>"주문취소","R"=>"반송","H"=>"배송(정산보류)");
		echo "	<tr>\n";
		echo "		<td>발송 여부</td>\n";
		echo "		<td>: {$deli_array[$_ord->deli_gbn]}";
		if(strlen($_ord->deli_date)==14) {
			$deli_date=substr($_ord->deli_date,0,4)."/".substr($_ord->deli_date,4,2)."/".substr($_ord->deli_date,6,2)." (".substr($_ord->deli_date,8,2).":".substr($_ord->deli_date,10,2).")";
			echo "&nbsp;&nbsp; 【발송세팅일 : {$deli_date}】";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		if($totreserve>0 && $_ord->deli_gbn=="N" && substr($_ord->ordercode,-1)!="X") {
			echo "<tr>\n";
			echo "	<td></td>\n";
			echo "	<td> <FONT color=#0000FF>* 배송완료 버튼을 누르면 회원에게 <FONT color=red>적립금 ".number_format($totreserve)."원</FONT>이 적립됩니다.</FONT></td>\n";
			echo "</tr>\n";
		}
	}

	echo "	<tr><td colspan=2 height=10></td></tr>\n";

	/*
	for($j=0;$j<count($prdata);$j++) {
		if(ord($prdata[$j]->order_prmsg)) {
			$order_prmsg = "";
			$order_prmsg .= "	<tr>\n";
			$order_prmsg .= "		<td valign=middle>주문메세지</td>\n";
			$order_prmsg .= "		<td>:&nbsp;&nbsp;&nbsp;{$prdata[$j]->order_prmsg}</td>\n";
			$order_prmsg .= "	</tr>\n";
		}
	}
	*/

	echo $order_prmsg;
	echo "<tr><td colspan=2 height=3></td></tr>\n";

	if($gbn=="Y" && ord($order_msg[1])) {
		echo "	<tr>\n";
		echo "		<td>주문관련메모</td>\n";
		echo "		<td>: {$order_msg[1]}</td>\n";
		echo "	</tr>\n";
	}
	if(ord($order_msg[2])) {
		echo "	<tr>\n";
		echo "		<td>고객알리미</td>\n";
		echo "		<td>: {$order_msg[2]}</td>\n";
		echo "	</tr>\n";
	}

	echo "	</table>\n";
	echo "	</td>\n";
	echo "</tr>\n";
}
?>
</table>
<?php
	if($i<>0) echo "</H1>\n";
}
?>
</center>
</body>
</html>
