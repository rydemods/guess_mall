<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$ordercode=$_POST["ordercode"];
$tblgbn="temp";

$type=$_POST["type"];
$mode=$_POST["mode"];

$order_msg=$_POST["order_msg"];

$rescode=$_POST["rescode"];
$pay_admin_proc=$_POST["pay_admin_proc"];

if($type=="sort") {
	$sort=$_POST["sort"];
}

if($ordercode==NULL) {
	alert_go('잘못된 접근입니다.','c');
}

$sql="SELECT * FROM tblorderinfo{$tblgbn} WHERE ordercode='{$ordercode}'";
$result=pmysql_query($sql,get_db_conn());
$_ord=pmysql_fetch_object($result);
pmysql_free_result($result);
if(!$_ord) {
	alert_go('해당 주문내역이 존재하지 않습니다.','c');
}
$curdate = date("YmdHi",strtotime('-30 min'));

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
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>주문상세내역 보기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<STYLE TYPE="text/css"> 
<!--
body { font-size: 9pt}
td { font-size: 9pt; line-height: 15pt}
tr { font-size: 9pt}
.break {page-break-before: always;}

@media print {
	.page_screen { display:none; }
	.page_print { display:inline; }
}
@media screen {
	.page_screen { display:inline; }
	.page_print { display:none; }
}

--> 
</STYLE> 
<SCRIPT LANGUAGE="JavaScript">
<!--
//document.onkeydown = CheckKeyPress;
//document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
	   event.keyCode = 0;
	   return false;
	 }
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 30;
	//var oHeight = document.all.table_body.clientHeight + 55;
	var oHeight=650;

	window.resizeTo(oWidth,oHeight);
}

var countdeli=countdelinum=countdecan=countbank=countbacan=countvican=counttrcan=countokcan=countokhold=0;

function PagePrint(){
	if(confirm("주문상세내역을 프린트 하시겠습니까?")) {
		print();
	}
}

function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function DeliSearch(deli_url){
	window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
}

function EtcMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.etcdtl"+cnt);
	obj._tid = setTimeout("EtcView(WinObj)",200);
}
function EtcView(WinObj) {
	WinObj.style.visibility = "visible";
}
function EtcMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.etcdtl"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="PagePrint();return false;" onLoad="PageResize();">

<table border=0 cellpadding=0 cellspacing=0 width=650 style="table-layout:fixed;" id=table_body>
<tr class="page_screen">
	<td width=100% align=center>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr><td height=10></td></tr>
	<tr>
		<td align=right style="padding-right:2pt">
		<A HREF="javascript:window.close()">[닫 기]</A>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td align=center style="padding-left:3">
	<!-- 주문내역 시작 -->
	<table border=1 cellpadding=0 cellspacing=0 width=100% bordercolorlight=#E2B892 bordercolordark=#ffffff style="table-layout:fixed">
	<col width=25></col>
	<col width=></col>
	<col width=95></col>
	<col width=30></col>
	<col width=45></col>
	<col width=55></col>
	<tr bgcolor=#efefef>
		<td align=center>No</td>
		<td align=center>상품명</td>
		<td align=center>선택사항</td>
		<td align=center>수량</td>
		<td align=center>적립금</td>
		<td align=center>가격</td>
	</tr>
<?php
	$colspan=6;
	$sql = "SELECT * FROM tblorderproduct{$tblgbn} WHERE ordercode='{$_ord->ordercode}' ";
	if(ord($sort)) $sql.="ORDER BY {$sort} ";
	$result=pmysql_query($sql,get_db_conn());
	$sumquantity=0;
	$totalprice=0;
	$in_reserve=0;
	$cnt=0;
	$taxsaveprname="";
	while($row=pmysql_fetch_object($result)) {
		if(preg_match("/^COU\d{8}X$/",$row->productcode)) {
			if($row->price!=0 && $row->price!=NULL) {
				$etcdata[]=$row;
				continue;
			}
		} else if(preg_match("/^9999999999\d(X|R)$/",$row->productcode)) {
			$etcdata[]=$row;
			continue;
		} else {
			$prdata[]=$row;
		}

		$taxsaveprname.=$row->productname.",";
		$cnt++;
		$sumprice=$row->quantity*$row->price;
		$reserve=$row->quantity*$row->reserve;
		$tempopt1=$row->opt1_name;
		if($row->productcode=="99999999999R") $norecan="Y";
		if ($row->productcode!="99999999999X" && substr($row->productcode,0,3)!="COU" && $row->productcode!="99999999999R") {
			$sumquantity+=$row->quantity;
		}
		$in_reserve+=$reserve;
		$totalprice+=$sumprice;

		$optvalue="";
		if(preg_match("/^\[OPTG\d{3}\]$/",$row->opt1_name)) {
			$optioncode=$row->opt1_name;
			$row->opt1_name="";
			$sql = "SELECT opt_name FROM tblorderoption{$tblgbn} WHERE ordercode='{$_ord->ordercode}' AND productcode='{$row->productcode}' ";
			$sql.= "AND opt_idx='{$optioncode}' ";
			$result2=pmysql_query($sql,get_db_conn());
			if($row2=pmysql_fetch_object($result2)) {
				$optvalue=$row2->opt_name;
			}
			pmysql_free_result($result2);
		}
		echo "<tr bgcolor=#FFFFFF>\n";
		echo "	<td align=center style=\"font-size:8pt\"><font color=#878787>{$cnt}</td>\n";

		if(file_exists($Dir.DataDir."shopimages/product/{$row->productcode}3.gif")) $file=$row->productcode."3.gif";
		else if(file_exists($Dir.DataDir."shopimages/product/{$row->productcode}3.jpg")) $file=$row->productcode."3.jpg";
		else $file="NO";
      
		if($file!="NO") {
			echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">\n";
			echo "	<span style=\"line-height:10pt\" onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage{$cnt}');\">{$row->productname}";
			echo "	<div id=primage{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
			echo "	<table border=0 cellspacing=1 cellpadding=0 bgcolor=#000000 width=170>\n";
			echo "	<tr bgcolor=#FFFFFF>\n";
			echo "		<td align=center width=100% height=150><img name=bigimgs src=\"".$Dir.DataDir."shopimages/product/{$file}\"></td>\n";
			echo "	</tr>\n";
			echo "	<tr bgcolor=#FFFFFF>\n";
			echo "		<td height=54 bgcolor=#f5f5f5><table border=0><tr><td style=\"line-height:12pt\">예전 주문서,삭제/이동 상품은 이미지가 일치하지 않을수 있으니 <font color=red>주의하여 배송</font>바랍니다.</td></tr></table></td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</div>\n";
			if(ord($row->addcode)) echo "<br><font color=blue>특수표시 : {$row->addcode}</font>";
			if(ord($optvalue)) echo "<br><font color=red>옵션사항 : {$optvalue}</font>";
			echo "	</td>\n";
		} else {
			echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">";
			echo $row->productname;
			if(ord($row->addcode)) echo "<br><font color=blue>특수표시 : {$row->addcode}</font>";
			if(ord($optvalue)) echo "<br><font color=red>옵션사항 : {$optvalue}</font>";
			echo "	</td>\n";
		}

		echo "	<td style=\"font-size:8pt;padding:2,5;line-height:11pt\">";
		echo (ord($row->opt1_name)?$row->opt1_name."<br>":"&nbsp;");
		if(ord($row->opt2_name)) echo $row->opt2_name;
		echo "	</td>\n";
		if ($row->productcode=="99999999999X" || substr($row->productcode,0,3)=="COU" || $row->productcode=="99999999999R") {
			echo "	<td>&nbsp;</td>\n";
		} else {
			echo "	<td style=\"font-size:8pt\" align=center".($row->quantity>1?" bgcolor=#FDE9D5><font color=#000000><b>":">").$row->quantity."</td>\n";
		}
		echo "	<td align=right style=\"font-size:8pt\">".(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && substr($row->productcode,-4)!="GIFT"?number_format($reserve)."&nbsp;":"&nbsp;")."</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".(substr($row->productcode,-4)!="GIFT"?number_format($sumprice)."&nbsp;":"&nbsp;")."</td>\n";
		echo "</tr>\n";
	}
	pmysql_free_result($result);

	echo "<tr height=30 bgcolor=#efefef>\n";
	echo "	<td align=center colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1))."><B>추가비용/할인/적립내역</B></td>";
	echo "</tr>\n";

	if(count($etcdata)>0) {
		for($j=0;$j<count($etcdata);$j++) {
			$cnt++;
			$sumprice=$etcdata[$j]->price;
			$reserve=$etcdata[$j]->reserve;
			$in_reserve+=$reserve;
			$totalprice+=$sumprice;
			echo "<tr>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">{$etcdata[$j]->productname} <span class=\"page_screen\"><A style=\"cursor:hand\" onMouseOver='EtcMouseOver($cnt)' onMouseOut=\"EtcMouseOut($cnt);\"><font style=\"font-size:8pt;color:#FC5F29\">more</font></A>";
			echo "	<div id=etcdtl{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
			echo "	<table border=0 cellpadding=0 cellspacing=0 width=300 bgcolor=#A47917>\n";
			echo "	<tr><td align=center style=\"color:#FFFFFF;padding:5\"><B>###### 해당 상품명 ######</B></td></tr>\n";
			echo "	<tr><td style=\"font-size:8pt;color:#FFFFFF;padding:10;padding-top:0;line-height:11pt\">{$etcdata[$j]->order_prmsg}</td></tr>\n";
			echo "	</table>\n";
			echo "	</div>\n";
			echo "	</span>\n";
			echo "	</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">";
			if($etcdata[$j]->vender>0) {
				if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
					echo ($reserve>0?number_format($reserve):"")."&nbsp;";
				} else {
					echo "&nbsp;";
				}
			} else {
				if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
					echo ($reserve>0?number_format($reserve):"")."&nbsp;";
				} else {
					echo "&nbsp;";
				}
			}
			echo "	</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".(substr($etcdata[$j]->productcode,-4)!="GIFT"?number_format($sumprice)."&nbsp;":"&nbsp;")."</td>\n";

			echo "</tr>\n";
		}
	}

	if($_ord) {
		if (strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			$sql = "SELECT memo,group_code FROM tblmember WHERE id='{$_ord->id}' ";
			$result=pmysql_query($sql,get_db_conn());
			if ($row=pmysql_fetch_object($result)) {
				$usermemo=$row->memo;
				$group_code=$row->group_code;
			}
			pmysql_free_result($result);

			if(ord($group_code)) {
				$sql = "SELECT group_name FROM tblmembergroup WHERE group_code='{$group_code}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$group_name = $row->group_name;
				}
				pmysql_free_result($result);
			} 
		}

		$dc_price=(int)$_ord->dc_price;
		$salemoney=0;
		$salereserve=0;
		if($dc_price<>0) {
			if($dc_price>0) $salereserve=$dc_price;
			else $salemoney=-$dc_price;
			if (strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
				$sql.= "WHERE a.id='{$_ord->id}' AND b.group_code=a.group_code AND SUBSTR(b.group_code,1,1)!='M'";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$group_name=$row->group_name;
				}
				pmysql_free_result($result);
			}
			echo "<tr bgcolor=#FFFFE6>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=red>그룹회원 적립/할인 : {$group_name}</font></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($salereserve>0?number_format($salereserve)."&nbsp;":"&nbsp;")."</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($salemoney>0?"-".number_format($salemoney)."&nbsp;":"&nbsp;")."</td>\n";
			echo "</tr>\n";
			$in_reserve+=$salereserve;
		}
		if($_ord->reserve>0) {
			echo "<tr bgcolor=#F5F5F5>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#0054A6>적립금사용액</font></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">-".number_format($_ord->reserve)."&nbsp;</td>\n";
			echo "</tr>\n";
		}
		$totalprice=$totalprice+$_ord->deli_price-$salemoney-$_ord->reserve;
		if($_shopdata->card_payfee>0 && strstr("CPM",$_ord->paymethod) && $_ord->price<>$totalprice) {
			echo "<tr bgcolor=#F5F5F5>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#F26622>카드수수료</font></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:87pt\">".number_format($_ord->price-$totalprice)."&nbsp;</td>\n";
			echo "</tr>\n";
		}
		$temp = substr($_ord->ordercode,0,4)."/".substr($_ord->ordercode,4,2)."/".substr($_ord->ordercode,6,2)." ".substr($_ord->ordercode,8,2).":".substr($_ord->ordercode,10,2).":".substr($_ord->ordercode,12,2);
		$message=explode("[MEMO]",$_ord->order_msg);
		$message[0]=str_replace("\"","&quot;",$message[0]);
		$message[0]=str_replace("\"","",$message[0]);

		$message[0]=str_replace("\r\n","<br>\n&nbsp;&nbsp;",$message[0]);
		$mes1 = explode("<br>",$message[0]);
		$mescnt = count($mes1);
		$message[0]="";
		for($i=0;$i<$mescnt;$i++) {
			//$message[0].=messageview2($mes1[$i],80)."<br>";
		}
		echo "<tr ";
		if($_ord->reserve==0) echo " bgcolor=#F5F5F5";
		echo ">\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td style=\"font-size:8pt;padding:5,27\"><B>총 합계</B> </td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=center style=\"font-size:8pt\">{$sumquantity}</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X"?number_format($in_reserve)."&nbsp;":"&nbsp")."</td>\n";
		echo "	<td align=right style=\"font-size:8pt\"> ".number_format($_ord->price)."&nbsp;</td>\n";

		echo "</tr>\n";

        echo "<tr>\n";
		echo "	<td colspan={$colspan} bgcolor=#fafafa align=center>\n";
		echo "	<table border=0 cellpadding=0 cellspacing=0 width=96%>\n";
		echo "	<col width=90 style=\"padding-left:3\"></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr><td colspan=2 height=5></td></tr>\n";
		echo "	<tr>\n";
		echo "		<td>주문 일자</td>\n";
		echo "		<td>: ".$temp;
		if(($_ord->del_gbn=="Y" || $_ord->del_gbn=="R") && !strstr("Y",$_ord->deli_gbn)) {
			echo " &nbsp;&nbsp;&nbsp;<font color=blue>[주문자가 내용삭제 버튼을 누른 주문서]</font>";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>주문자</td>\n";
		echo "		<td>: ".$_ord->sender_name;
		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			echo "({$_ord->id}) ";
			if(ord($group_name)) echo " [ 그룹명 : {$group_name} ] ";
			if(ord(trim($usermemo))) {
				echo "<div id=\"membermemo_layer\" style=\"position:absolute; z-index:20; width:300;\"><table border=0 cellspacing=0 cellpadding=1 bgcolor=#7F7F65><tr><td style=\"padding:3\"><font color=#ffffff>{$usermemo}&nbsp;</td></tr></table></div>";
			}
		} else {
			echo "(비회원주문)";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		if (ord($_ord->ip)) { 
			$ip = $_ord->ip;
			echo "	<tr>\n";
			echo "		<td>주문자IP</td>\n";
			echo "		<td>: {$ip}</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td>연락처</td>\n";
		echo "		<td>: 전화 : {$_ord->sender_tel} ,";
		echo "		&nbsp;&nbsp;&nbsp;&nbsp; 이메일 : {$_ord->sender_email}</a>";
		echo "		</td>\n";
		echo "	</tr>\n";

		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			$sql = "SELECT COUNT(*) as cnt, SUM(price) as money FROM tblorderinfo ";
			$sql.= "WHERE id='{$_ord->id}' AND deli_gbn='Y'";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$ordercnt=$row->cnt;
				$ordersum=$row->money;
			}
			pmysql_free_result($result);
			echo "	<tr>\n";
			echo "		<td bgcolor=#7F7F65><font color=#ffffff>누적 주문</font></td>\n";
			echo "		<td bgcolor=#7F7F65 style=\"color:#ffffff\">: ";
			if($ordercnt!=0) {
				echo "주문횟수 {$ordercnt}건, 총주문금액 ".number_format($ordersum)." (배송완료 기준) ";
			} else {
				echo "첫구매 고객입니다.";
			}
			echo "		</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td>받는분</td>\n";
		echo "		<td>: {$_ord->receiver_name}</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td valign=top style=\"padding-top:5px\">받는 주소</td>\n";
		echo "		<td>: \n";
		$address = str_replace(array("\n","\r")," ",trim($_ord->receiver_addr));
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+7);
		}
		$post = str_replace("우편번호 : ","",$post);
		$arpost = explode("-",$post);
		echo "		우편번호 : {$arpost[0]}-{$arpost[1]}<br>\n";
		echo "		&nbsp;&nbsp;주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소: {$address}\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>연락처</td>\n";
		echo "		<td>: 전화 : {$_ord->receiver_tel1} , {$_ord->receiver_tel2}</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>결제 방법</td>\n";
		echo "		<td>: ";

		$pgdate = date("YmdHi",strtotime('-2 hour'));
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드",/*"P"=>"신용카드(매매보호)",*/"M"=>"핸드폰");

		if($_ord->pay_data=="신용카드결제 - 카드작성중" && substr($_ord->ordercode,0,12)<=$pgdate) $_ord->pay_data=$arpm[$_ord->paymethod[0]]." 에러";

		if (strstr("BOQ",$_ord->paymethod[0])) {
			if($_ord->paymethod=="B") echo "<font color=#FF5D00>무통장입금</font>\n";
			else if($_ord->paymethod[0]=="O") echo "<font color=#FF5D00>가상계좌</font>\n";
			else echo "매매보호 - 가상계좌";

			if(!strstr("CD",$_ord->deli_gbn) || $_ord->paymethod=="B") echo "【 {$_ord->pay_data} 】";
			else echo "【 계좌 취소 】";

			if (strlen($_ord->bank_date)>=12) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td><FONT COLOR=red><B>입금확인</B></FONT></td>\n";
				echo "	<td>: <B><font color=red>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")</font></B>";
			} else if(strlen($_ord->bank_date)==9) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td><FONT COLOR=red><B>입금확인</B></FONT></td>\n";
				echo "	<td>: <B><font color=red>환불</font></B>";
			}
		} else if($_ord->paymethod[0]=="M") {
			echo "핸드폰 결제【 ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>결제취소 완료</font> 】";
				else echo "<font color=red>결제가 성공적으로 이루어졌습니다.</font>";
			}
			else echo "결제가 실패되었습니다.";
			echo " 】";
		} else if($_ord->paymethod[0]=="P") {
			echo "매매보호 - 신용카드";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				else if($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 {$_ord->pay_auth_no} 】";
			}
			else echo "【 {$_ord->pay_data} 】";
		} else if ($_ord->paymethod[0]=="C") {
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

		if(strstr("QP",$_ord->paymethod[0]) && strstr("Y",$_ord->escrow_result) && $_ord->deli_gbn!="C") echo " - <font color=red><b>[구매확인]</b></font>";
		else if(strstr("QP",$_ord->paymethod[0]) && strstr("C",$_ord->escrow_result) && $_ord->deli_gbn=="C") echo " - <font color=red><b>[구매취소]</b></font>";
		echo "		</td>\n";
		echo "	</tr>\n";
		$ardelivery=array("Y"=>"발송중","N"=>"미발송","C"=>"주문취소","X"=>"배송요청","S"=>"발송준비","D"=>"취소요청","E"=>"환불대기","H"=>"배송(정산보류)");
		echo "	<tr>\n";
		echo "		<td>발송 여부</td>\n";
		echo "		<td>: <font color=#A00000>{$ardelivery[$_ord->deli_gbn]}</font>";
		if(strlen($_ord->deli_date)==14) {
			echo " - 발송셋팅일 : ".substr($_ord->deli_date,0,4)."/".substr($_ord->deli_date,4,2)."/".substr($_ord->deli_date,6,2)." (".substr($_ord->deli_date,8,2).":".substr($_ord->deli_date,10,2).")";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		if($in_reserve>0 && $_ord->deli_gbn=="N" && strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X"){
			echo "	<tr>\n";
			echo "		<td>&nbsp;</td>\n";
			echo "		<td><font color=#0000FF>&nbsp;&nbsp;* 배송완료 버튼을 누르면 회원에게 적립금</font> <font color=#A00000>".number_format($in_reserve)."원</font><font color=#0000FF>이 적립됩니다.</font></td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr height=22>\n";
		echo "		<td valign=top style=\"padding-top:5\">주문요청사항</td>\n";
		echo "		<td>: {$message[0]}</td>\n";
		echo "	</tr>\n";

		for($j=0;$j<count($prdata);$j++) {
			if(ord($prdata[$j]->order_prmsg)) {
				echo "	<tr height=22 class=\"page_screen\">\n";
				echo "		<td valign=middle>주문메세지</td>\n";
				echo "		<td style=\"padding-left:7\">";
				echo "	<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> {$prdata[$j]->productname}<BR>\n";
				echo "<textarea style=\"width:95%;height:38;overflow-x:hidden;overflow-y:auto;\" readonly>{$prdata[$j]->order_prmsg}</textarea>\n";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "<tr><td colspan=2 height=5></td></tr>\n";

				echo "	<tr height=22 class=\"page_print\">\n";
				echo "		<td valign=middle>주문메세지</td>\n";
				echo "		<td style=\"padding-left:7\">";
				echo "		<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> {$prdata[$j]->productname}<BR>\n";
				echo "		{$prdata[$j]->order_prmsg}";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "<tr><td colspan=2 height=3></td></tr>\n";
			}
		}
		if(ord($message[1])) {
			echo "	<tr height=58>\n";
			echo "		<td valign=top style=\"padding-top:8\">주문관련 메모</td>\n";
			echo "		<td style=\"padding-top:3\">: \n";
			echo "		{$message[1]}\n";
			echo "		</td>\n";
			echo "	</tr>\n";
		}
		if(ord($message[2])) {
			echo "	<tr height=58>\n";
			echo "		<td valign=top style=\"padding-top:8\">고객알리미</td>\n";
			echo "		<td style=\"padding-top:3\">: \n";
			echo "		{$message[2]}\n";
			echo "		</td>\n";
			echo "	</tr>\n";
		}
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	}
?>
	</table>
	<!-- 주문내역 끝 -->
	</td>
</tr>

<tr><td height=10></td></tr>

</table>
<?=$onload?>
</body>
</html>
