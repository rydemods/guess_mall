<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0) {
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$ordercode=$_POST["ordercode"];
if(ord($ordercode)==0) {
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$taxkind=$_POST["taxkind"];
$taxsele=$_POST["taxsele"];
$taxrate=$_POST["taxrate"];
$addtax=$_POST["addtax"];


if(ord($taxrate)==0) $taxrate=10;

?>
<html>
<head>
<title>영수증 출력</title>
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
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	try {
		if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
			event.keyCode = 0;
			return false;
		}
	} catch (e) {}
}

var layername2 = new Array ('taxprint0','taxprint1','taxprint2','taxprint3','taxprint4','taxprint5');

function viewtax(text,text2){
	text=parseInt(text);
	text2=parseInt(text2);
	if(text2==0) {
		shop='taxprint'+(text*2);
		shop2='taxprint'+ parseInt(text*2+1);
	} else {
		shop='taxprint'+ parseInt(text*2 + text2 - 1);
	}
	if(document.all) {
		for(i=0;i<6;i++) document.all(layername2[i]).style.display="none";
		document.all(shop).style.display="block";
		if(text2==0) document.all(shop2).style.display="block";
	} else if(document.getElementById) {
		for(i=0;i<6;i++) document.getElementById(layername2[i]).style.display="none";
		document.getElementById(shop).style.display="block";
		if(text2==0) document.getElementById(shop2).style.display="block";
	} else if(document.layers) {
		for(i=0;i<6;i++) document.layers(layername2[i]).display="none";
		document.layers[shop].display="block";
		if(text2==0) document.layers[shop2].display="block";
	}
	if(text==0) {
		document.form.taxrate.disabled=true;
		document.form.addtax.disabled=true;
	} else {
		document.form.taxrate.disabled=false;
		document.form.addtax.disabled=false;
	}
}
function orderdetail(){
	document.orderdetail.submit();
}
function send(taxrate,addtax) {
	tax=document.form.taxrate.value;
	addtax2=document.form.addtax.checked;
	if(parseInt(tax)<0) {
		alert('음수를 입력할 수 없습니다.');
		tax=0;
	} else if(isNaN(tax)) {
		alert("숫자만 입력이 가능합니다.");
		tax=0;
	}

	if(document.form.year.value!=document.form.oldyear.value || document.form.month.value!=document.form.oldmonth.value || document.form.day.value!=document.form.oldday.value) document.form.submit();
	else if((tax!=0 && taxrate!=tax) || (addtax=="Y" && addtax2==false) || (addtax!="Y" && addtax2)) document.form.submit();
	else document.form.taxrate.value=tax;
	viewtax(document.form.taxkind.options[document.form.taxkind.selectedIndex].value,document.form.taxsele.options[document.form.taxsele.selectedIndex].value);
}
function printok(){
	if(confirm('영수증 내역을 보고 계십니다. 이정보를 출력하시겠습니까')){
		print();
	}
}

function changevalue(){
	document.company2.companynum2.value=document.company1.companynum1.value;
	document.company2.ownername2.value=document.company1.ownername1.value;
	document.company2.companyname2.value=document.company1.companyname1.value;
	document.company2.companyaddr2.value=document.company1.companyaddr1.value;
	document.company2.companybiz2.value=document.company1.companybiz1.value;
	document.company2.companyitem2.value=document.company1.companyitem1.value;
}
</script>
<body bgcolor=#FFFFFF topmargin=10 leftmargin=10 marginwidth=0 marginheight=0 oncontextmenu="return false" 1oncontextmenu="printok();return false;">
<center>
<table width=100% border=0 cellpadding=0 cellspacing=0 bgcolor=#FFFFFF>
<tr height=30>
	<form name=form method=post action="<?=$_SERVER['PHP_SELF']?>">
	<td width=20 rowspan=2>&nbsp;</td>
	<td align="left" colspan=2>
<?php 
    if(ord($year)==0){
       $year=date("Y");
       $month=date("m");
       $day=date("d");
    }

    $maxyear=date("Y");

    echo "<select size=1 name=year style=\"font-size:11px\">\n";                                   //year1
    for ($i = 1999;$i <= $maxyear; $i++) {
        if($i == $year)  echo "<option selected value=\"$i\">$i</option>\n";
        else echo "<option value=\"$i\">$i</option>\n";
    }
    echo "</select>년";
    echo "<select size=1 name=month style=\"font-size:11px\">\n";                                   //month1
    for ($i = 1;$i <= 12; $i++) {
        if($i == $month)  echo "<option selected value=\"$i\">$i</option>\n";
        else echo "<option value=\"$i\">$i</option>\n";
    }
    echo "</select>월";
    echo "<select size=1 name=day style=\"font-size:11px\">\n";                                   //day1
    for ($i = 1;$i <= 31; $i++) {
        if ($i == $day)  echo "<option selected value=\"$i\">$i</option>\n";
        else echo "<option value=\"$i\">$i</option>\n";
    }
    echo "</select>일";

    $arraykind= array("영수증","세금계산서","거래명세서");
    $arraysele= array("모두출력","공급받는자용","공급자용");
    echo "<select name=taxkind style=\"font-size:11px\">";
    for($i=0;$i<3;$i++){
         echo "<option value=".$i;
         if($taxkind==$i) echo " selected";
         echo ">{$arraykind[$i]}\n";
    }
    echo "</select>";
    echo "<select name=taxsele style=\"font-size:11px\">";
    for($i=0;$i<3;$i++){
         echo "<option value=".$i;
         if($taxsele==$i) echo " selected";
         echo ">{$arraysele[$i]}\n";
    }
    echo "</select>";
?>
	세율 <input type=text name=taxrate size=2 maxlength=3 value=<?=$taxrate?>>% 
	<input type=checkbox name=addtax value="Y" <?php if($addtax=="Y") echo " checked";?> ><b>부가세 별도계산</b>
	<a href="JavaScript:send('<?=$taxrate?>','<?=$addtax?>')"><img src="images/taxprint_view.gif" align=absmiddle border=0></a></td>
	</td>
	<td width=23 rowspan=2>&nbsp;</td>
</tr>
<tr height=30>
	<td align=right colspan=2>
	<a href="JavaScript:printok()"><img src="images/taxprint_print.gif" align=absmiddle border=0></a>
	<a href="JavaScript:orderdetail()"><img src="images/taxprint_cancel.gif" align=absmiddle border=0></a>
    </td>
</tr>
<input type=hidden name=oldyear value="<?=$year?>">
<input type=hidden name=oldmonth value="<?=$month?>">
<input type=hidden name=oldday value="<?=$day?>">
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>
<form name=orderdetail method=post action="order_detail.php">
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<?php
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
	if($row->productcode!="99999999997X") {
		if(preg_match("/^COU\d{8}X$/",$row->productcode)) {				#쿠폰
			if($row->price!=0 && $row->price!=NULL) {
				$etcdata[]=$row;
				continue;
			}
		} else if(preg_match("/^9999999999\d(X|R)$/",$row->productcode)) {
			if($row->productcode=="99999999990X") {
				continue;
			} else {
				$etcdata[]=$row;
				continue;
			}
		}

		$productname[$count]=$row->productname;
		$quantity[$count]=$row->quantity;
		$opt1_name[$count]=$row->opt1_name;
		$productprice[$count++]=$row->price;
	} else {
		$productvattotal+=$row->price;
	}
}
pmysql_free_result($result);
?>
<tr><td colspan=4 align=center bgcolor=#FFFFFF height=10>&nbsp;</td></tr>
<tr>
	<td colspan=4 align=center bgcolor=#FFFFFF>
	<table border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td>
		<div id=taxprint0 style="hide">

		<?php if (strstr("BVO",$paymethod[0])) {?>

		<table width=302 border=1 cellspacing=0 cellpadding=0 bordercolor=blue style="table-layout:fixed">
		<tr>
			<td>
			<table width=100% border=1 cellspacing=0 cellpadding=0 bordercolor=blue>
			<tr>
				<td style="border-bottom:0;">
				<table width=100% border=0 cellspacing=0 cellpadding=0 style="table-layout:fixed">
				<col width=28%></col>
				<col width=44%></col>
				<col width=38%></col>
				<tr height=40>
					<td></td>
					<td align=center class=c01> <font size=4><b>영 수 증</b></font></td>
					<td class=c01><font size=1>(공급받는자용)</font></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="border-top:0;">
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void style="table-layout:fixed">
				<col width=20></col>
				<col width=54></col>
				<col width=></col>
				<col width=20></col>
				<col width=></col>
				<tr>
					<td colspan=2 style="border-top:thin solid;" valign=bottom class=c01>No.</td>
					<td colspan=3 align=right class=c01>&nbsp;<font color=black style="font-size:12pt;"><B><?=$sender_name?></B>&nbsp;&nbsp;</font><font size=3>귀하</font>&nbsp;</td>
				</tr>
				<tr align=center height=32>
					<td rowspan=4 class=c01>공<br><br>급<br><br>자</td>
					<td class=c01>사 업 자<br>등록번호</td>
					<td colspan=3>&nbsp;<font size=3><B><?=$companynum?></B></font></td>
				</tr>
				<tr align=center height=32>
					<td class=c01>상<img width=20 height=0>호</td>
					<td>&nbsp;<B><?=$companyname?></B></td>
					<td class=c01>성명</td>
					<td>&nbsp;<B><?=$companyowner?></B><img src=images/taxprint_sign.gif align=absmiddle hspace=3></td>	
				</tr>
				<tr align=center height=32>
					<td class=c01>사 업 장<br>소 재 지</td>
					<td colspan=3>&nbsp;<B><?=$companyaddr?></B></td>
				</tr>
				<tr align=center height=32>
					<td class=c01>업<img width=20 height=0>태</td>
					<td>&nbsp;<B><?=$companybiz?></B></td>
					<td class=c01>종목</td>
					<td>&nbsp;<B><?=$companyitem?></B></td>	
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void style="table-layout:fixed">
				<col width=110></col>
				<col width=></col>
				<col width=55></col>
				<tr align=center>
					<td class=c01>작성년월일</td>
					<td style="
						border-top:thin solid;
						border-left:thin solid;
						border-right:thin solid;
						" class=c01>공급대가총액</td>
					<td class=c01>비 고</td>
				</tr>
				<tr align=center>
					<td>&nbsp;
					<B><?=$year.".{$month}.".$day;?></B>
					</td>
					<td style="
						border-bottom:thin solid;
						border-left:thin solid;
						border-right:thin solid;">&nbsp;<B>￦<?=number_format($totalprice)?></B>
					</td>
					<td align=right></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align=center style="padding:4px;" class=c01>
				위 금액을 정히 영수(청구)함
				</td>
			</tr>
			<tr>
				<td>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void>
				<tr align=center>
					<td class=c01>월</td>
					<td class=c01>일</td>
					<td class=c01>품 목</td>
					<td class=c01>수량</td>
					<td class=c01>단 가</td>
					<td class=c01>금 액</td>
				</tr>
				<?php for($cnt=0;$cnt<$count;$cnt++){?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt"><?=$productname[$cnt]?><br><?=$opt1_name[$cnt]?></td>
					<td style="font-size:8pt"><?=number_format($quantity[$cnt])?></td>
					<td align=right style="font-size:8pt"><?=number_format($productprice[$cnt])?></td>
					<td align=right style="font-size:8pt"><?=number_format($productprice[$cnt]*$quantity[$cnt]);?></td>
				</tr>
				<?php }?>
				<?php for($k=0;$k<count($etcdata);$k++){
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt"><?=$etcdata[$k]->productname?></td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt"><?=number_format($etcdata[$k]->price)?></td>
					<td align=right style="font-size:8pt"><?=number_format($etcdata[$k]->price);?></td>
				</tr>
				<?php }?>
				<?php if($productvattotal>0) {
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">부가세(VAT)</td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt"><?=number_format($productvattotal)?></td>
					<td align=right style="font-size:8pt"><?=number_format($productvattotal);?></td>
				</tr>
				<?php }?>
				<?php if($deli_price>0) {
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">배송료</td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt"><?=number_format($deli_price)?></td>
					<td align=right style="font-size:8pt"><?=number_format($deli_price);?></td>
				</tr>
				<?php }?>
				<?php if($reserve>0) {
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">적립금</td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt">-<?=number_format($reserve)?></td>
					<td align=right style="font-size:8pt">-<?=number_format($reserve);?></td>
				</tr>
				<?php }?>
				<?php if($dc_price<0) {
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">우수회원 할인</td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt"><?=number_format($dc_price)?></td>
					<td align=right style="font-size:8pt"><?=number_format($dc_price);?></td>
				</tr>
				<?php } 
				  if($cnt<10){ 
					 $cnt++;
				?>
				<tr><td colspan=6 align=center class=c01> ***** 이 하 여 백 ***** </td></tr>
				<?php }
				  for($i=$cnt;$i<10;$i++){?>
				<tr align=center>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align=right>&nbsp;</td>
					<td align=right>&nbsp;</td>
				</tr>
				<?php }?>
				</table>
				</td>
			</tr>
			<tr>
				<td align=center class=c01 height=25>
				<font size=1>부가가치세법시행규칙 제25조 규정에 의한 (영수증)으로 개정.</font>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>

		<?php }else {?>

		<table width=302 border=0 cellspacing=0 cellpadding=0 width=100%>
		<tr><td align=center>카드결제의 경우 영수증을 지원하지않습니다.</td></tr>
		</table>

		<?php }?>

		</div>
		</td>
		<td>&nbsp;</td>
		<td>
		<div id=taxprint1 style="hide">

		<?php if (strstr("BVO",$paymethod[0])) {?>

		<table width=302 border=1 cellspacing=0 cellpadding=0 bordercolor=red style="table-layout:fixed">
		<tr>
			<td>
			<table width=100% border=1 cellspacing=0 cellpadding=0 bordercolor=red>
			<tr>
				<td style="border-bottom:0;">
				<table width=100% border=0 cellspacing=0 cellpadding=0 style="table-layout:fixed">
				<col width=28%></col>
				<col width=44%></col>
				<col width=38%></col>
				<tr height=40>
					<td></td>
					<td align=center class=c02> <font size=4><b>영 수 증</b></font></td>
					<td class=c02><font size=1>(공급자용)</font></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="border-top:0;">
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
				<col width=20></col>
				<col width=54></col>
				<col width=></col>
				<col width=20></col>
				<col width=></col>
				<tr>
					<td colspan=2 style="border-top:thin solid;" valign=bottom class=c02>No.</td>
					<td colspan=3 align=right class=c02>&nbsp;<font color=black style="font-size:12pt;"><B><?=$sender_name?></B>&nbsp;&nbsp;</font><font size=3>귀하</font>&nbsp;</td>
				</tr>
				<tr align=center height=32>
					<td rowspan=4 class=c02>공<br><br>급<br><br>자</td>
					<td class=c02>사 업 자<br>등록번호</td>
					<td colspan=3>&nbsp;<font size=3><B><?=$companynum?></B></font></td>
				</tr>
				<tr align=center height=32>
					<td class=c02>상<img width=20 height=0>호</td>
					<td>&nbsp;<B><?=$companyname?></B></td>
					<td class=c02>성명</td>
					<td>&nbsp;<B><?=$companyowner?></B><img src=images/taxprint_sign.gif align=absmiddle hspace=3></td>	
				</tr>
				<tr align=center height=32>
					<td class=c02>사 업 장<br>소 재 지</td>
					<td colspan=3>&nbsp;<B><?=$companyaddr?></B></td>
				</tr>
				<tr align=center height=32>
					<td class=c02>업<img width=20 height=0>태</td>
					<td>&nbsp;<B><?=$companybiz?></B></td>
					<td class=c02>종목</td>
					<td>&nbsp;<B><?=$companyitem?></B></td>	
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
				<col width=110></col>
				<col width=></col>
				<col width=55></col>
				<tr align=center>
					<td class=c02>작성년월일</td>
					<td style="
						border-top:thin solid;
						border-left:thin solid;
						border-right:thin solid;
						" class=c02>공급대가총액</td>
					<td class=c02>비 고</td>
				</tr>
				<tr align=center>
					<td>&nbsp;
					<B><?=$year.".{$month}.".$day;?></B>
					</td>
					<td style="
						border-bottom:thin solid;
						border-left:thin solid;
						border-right:thin solid;">&nbsp;<B>￦<?=number_format($totalprice)?></B>
					</td>
					<td align=right></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align=center style="padding:4px;" class=c02>
				위 금액을 정히 영수(청구)함
				</td>
			</tr>
			<tr>
				<td>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void>
				<tr align=center>
					<td class=c02>월</td>
					<td class=c02>일</td>
					<td class=c02>품 목</td>
					<td class=c02>수량</td>
					<td class=c02>단 가</td>
					<td class=c02>금 액</td>
				</tr>
				<?php for($cnt=0;$cnt<$count;$cnt++){?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt"><?=$productname[$cnt]?><br><?=$opt1_name[$cnt]?></td>
					<td style="font-size:8pt"><?=number_format($quantity[$cnt])?></td>
					<td align=right style="font-size:8pt"><?=number_format($productprice[$cnt])?></td>
					<td align=right style="font-size:8pt"><?=number_format($productprice[$cnt]*$quantity[$cnt]);?></td>
				</tr>
				<?php }?>
				<?php for($k=0;$k<count($etcdata);$k++){
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt"><?=$etcdata[$k]->productname?></td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt"><?=number_format($etcdata[$k]->price)?></td>
					<td align=right style="font-size:8pt"><?=number_format($etcdata[$k]->price);?></td>
				</tr>
				<?php }?>
				<?php if($productvattotal>0) {
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">부가세(VAT)</td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt"><?=number_format($productvattotal)?></td>
					<td align=right style="font-size:8pt"><?=number_format($productvattotal);?></td>
				</tr>
				<?php }?>
				<?php if($deli_price>0) {
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">배송료</td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt"><?=number_format($deli_price)?></td>
					<td align=right style="font-size:8pt"><?=number_format($deli_price);?></td>
				</tr>
				<?php }?>
				<?php if($reserve>0) {
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">적립금</td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt">-<?=number_format($reserve)?></td>
					<td align=right style="font-size:8pt">-<?=number_format($reserve);?></td>
				</tr>
				<?php }?>
				<?php if($dc_price<0) {
					$cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">우수회원 할인</td>
					<td style="font-size:8pt">1</td>
					<td align=right style="font-size:8pt"><?=number_format($dc_price)?></td>
					<td align=right style="font-size:8pt"><?=number_format($dc_price);?></td>
				</tr>
				<?php } 
				  if($cnt<10){ 
					 $cnt++;
				?>
				<tr><td colspan=6 align=center class=c02> ***** 이 하 여 백 ***** </td></tr>
				<?php }
				  for($i=$cnt;$i<10;$i++){?>
				<tr align=center>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align=right>&nbsp;</td>
					<td align=right>&nbsp;</td>
				</tr>
				<?php }?>
				</table>
				</td>
			</tr>
			<tr>
				<td align=center class=c02 height=25>
				<font size=1>부가가치세법시행규칙 제25조 규정에 의한 (영수증)으로 개정.</font>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>

		<?php }else {?>

		<table width=302 border=0 cellspacing=0 cellpadding=0 width=100%>
		<tr><td align=center>카드결제의 경우 영수증을 지원하지않습니다.</td></tr>
		</table>

		<?php }?>

		</div>

		<div id=taxprint2 style="hide">
		<table width=620 border=1 cellspacing=0 cellpadding=0 bordercolor=blue style="table-layout:fixed">
		<tr>
			<td>
			<table width=100% border=1 cellspacing=0 cellpadding=0 bordercolor=blue style="table-layout:fixed">
			<tr>
				<td colspan=2>
				<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void style="table-layout:fixed">
				<tr>
					<td rowspan=2 width=70% height=40>
					<table border=0 cellspacing=0 cellpadding=0>
					<tr align=center>
						<td width=160></td>
						<td rowspan=2><font color=blue size=3><b>세 금 계 산 서</b></font>&nbsp;&nbsp;</td>
						<td class=c01 rowspan=2><font size=5>(</font></td>
						<td class=c01>공급받는자</td>
						<td class=c01 rowspan=2><font size=5>)</font></td>
					</tr>
					<tr align=center>
						<td align=left class=c01>&nbsp;&nbsp;&nbsp;<font size=1 style="font-weight:normal">(별지 제 11호 서식)</font></td>
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
				<td width=310 height=100%>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void height=100% style="table-layout:fixed">
				<col width=18></col>
				<col width=54></col>
				<col width=></col>
				<col width=18></col>
				<col width=></col>
				<tr height=27 valign=middle>
					<td rowspan=4 class=c01>공 급 자</td>
					<td align=center class=c01 style="padding-top:3px"><nobr>등록번호</td>
					<td colspan=3 style="padding-left:5px;padding-top:3px"><B><font size=3><?=$companynum?></font></B></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c01 style="padding-top:3px">상<img width=20 height=0>호<br>(법인명)</td>
					<td style="padding-left:5px;padding-top:3px"><B><?=$companyname?></B></td>
					<td align=center class=c01 style="padding-top:3px">성명</td>
					<td style="padding-left:5px"><B><?=$companyowner?></B><img src=images/taxprint_sign.gif align=absmiddle hspace=2></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c01 style="padding-top:3px">사 업 장<br>주<img width=20 height=0>소</td>
					<td colspan=3 style="padding-left:5px"><B><?=$companyaddr?></B></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c01 style="padding-top:3px">업<img width=20 height=0>태</td>
					<td style="padding-left:5px;padding-top:3px"><B><?=$companybiz?></B></td>
					<td class=c01 style="padding-top:3px">종목</td>
					<td style="padding-left:5px;padding-top:3px"><B><?=$companyitem?></B></td>
				</tr>
				</table>
				</td>
				<td width=310 height=100%>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void height=100% style="table-layout:fixed">
				<col width=18></col>
				<col width=54></col>
				<col width=></col>
				<col width=18></col>
				<col width=></col>
				<form name=company1>
				<tr height=27 valign=middle>
					<td rowspan=4 class=c01>공 급 받 는 자</td>
					<td align=center class=c01 style="padding-top:3px"><nobr>등록번호</td>
					<td colspan=3 style="padding-left:5px;padding-top:3px"><input type=text class=nip name=companynum1 size=20 maxlength=13 onKeyUp="changevalue()"></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c01 style="padding-top:3px">상<img width=20 height=0>호<br>(법인명)</td>
					<td style="padding-left:5px;padding-top:3px"><input type=text class=nip name=companyname1 size=15 maxlength=30 onKeyUp="changevalue()"></td>
					<td align=center class=c01 style="padding-top:3px">성명</td>
					<td style="padding-left:5px"><input type=text class=nip name=ownername1 size=8 maxlength=16 onKeyUp="changevalue()"><img src=images/taxprint_sign.gif align=absmiddle hspace=2></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c01 style="padding-top:3px">사 업 장<br>주<img width=20 height=0>소</td>
					<td colspan=3 style="padding-left:5px"><input type=text class=nip name=companyaddr1 size=30 maxlength=50 onKeyUp="changevalue()"></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c01 style="padding-top:3px">업<img width=20 height=0>태</td>
					<td style="padding-left:5px;padding-top:3px"><input type=text class=nip name=companybiz1 size=15 maxlength=30 onKeyUp="changevalue()"></td>
					<td class=c01 style="padding-top:3px">종목</td>
					<td style="padding-left:5px;padding-top:3px"><input type=text class=nip name=companyitem1 size=15 maxlength=30 onKeyUp="changevalue()"></td>
				</tr>
				</form>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan=2>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void style="table-layout:fixed">
				<col width=36></col>
				<col width=18></col>
				<col width=18></col>
				<col width=234></col>
				<col width=199></col>
				<col width=></col>
				<tr align=center>
					<td colspan=3 class=c01>작성일</td>
					<td class=c01>공급가액</td>
					<td class=c01>세 액</td>
					<td class=c01>비 고</td>
				</tr>
				<tr align=center>
					<td class=c01>년</td>
					<td class=c01>월</td>
					<td class=c01>일</td>
					<td rowspan=2 style="padding:0px;" height=100%>
					<table width=100% height=100% border=1 cellspacing=0 cellpadding=0 bordercolor=blue frame=void style="table-layout:fixed">
					<col width=27></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<tr align=center>
						<td class=c01>공란</td>
						<td class=c01>백</td>
						<td class=c01>십</td>
						<td class=c01>억</td>	
						<td class=c01>천</td>	
						<td class=c01>백</td>	
						<td class=c01>십</td>	
						<td class=c01>만</td>	
						<td class=c01>천</td>	
						<td class=c01>백</td>	
						<td class=c01>십</td>	
						<td class=c01>일</td>	
					</tr>
<?php 
					if($addtax!="Y") {
						$totalsale=round($totalprice/(1+$taxrate/100));
						$totaltax=$totalprice-$totalsale;
						$totalsumprice=$totalprice;
					} else {
						$totalsale=$totalprice;
						$totaltax=($totalprice*($taxrate/100));
						$totalsumprice=$totalsale+$totaltax;
					}
					$length=strlen($totalsale);
					$length2=strlen($totaltax);
?>

					<tr align=center height=24>
						<td><?=11-$length?></td>
						<td><?=($length>=11?substr($totalsale,-11,1):"")?></td>
						<td><?=($length>=10?substr($totalsale,-10,1):"")?></td>
						<td><?=($length>=9?substr($totalsale,-9,1):"")?></td>
						<td><?=($length>=8?substr($totalsale,-8,1):"")?></td>
						<td><?=($length>=7?substr($totalsale,-7,1):"")?></td>
						<td><?=($length>=6?substr($totalsale,-6,1):"")?></td>
						<td><?=($length>=5?substr($totalsale,-5,1):"")?></td>
						<td><?=($length>=4?substr($totalsale,-4,1):"")?></td>
						<td><?=($length>=3?substr($totalsale,-3,1):"")?></td>
						<td><?=($length>=2?substr($totalsale,-2,1):"")?></td>
						<td><?=($length>=1?substr($totalsale,-1,1):"")?></td>
					</tr>
					</table>
					</td>
					<td rowspan=2 style="padding:0px;" height=100%>
					<table width=100% height=100% border=1 cellspacing=0 cellpadding=0 bordercolor=blue frame=void style="table-layout:fixed">
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<tr align=center>
						<td class=c01>십</td>
						<td class=c01>억</td>	
						<td class=c01>천</td>	
						<td class=c01>백</td>	
						<td class=c01>십</td>	
						<td class=c01>만</td>	
						<td class=c01>천</td>	
						<td class=c01>백</td>	
						<td class=c01>십</td>	
						<td class=c01>일</td>	
					</tr>
					<tr align=center height=24>
						<td><?=($length2>=10?substr($totaltax,-10,1):"")?></td>
						<td><?=($length2>=9?substr($totaltax,-9,1):"")?></td>
						<td><?=($length2>=8?substr($totaltax,-8,1):"")?></td>
						<td><?=($length2>=7?substr($totaltax,-7,1):"")?></td>
						<td><?=($length2>=6?substr($totaltax,-6,1):"")?></td>
						<td><?=($length2>=5?substr($totaltax,-5,1):"")?></td>
						<td><?=($length2>=4?substr($totaltax,-4,1):"")?></td>
						<td><?=($length2>=3?substr($totaltax,-3,1):"")?></td>
						<td><?=($length2>=2?substr($totaltax,-2,1):"")?></td>
						<td><?=($length2>=1?substr($totaltax,-1,1):"")?></td>
					</tr>
					</table>
					</td>
					<td rowspan=2></td>
				</tr>
				<tr align=center height=24>
					<td><?=$year?></td>
					<td><?=$month?></td>
					<td><?=$day?></td>	
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
				<col width=40></col>
				<col width=40></col>
				<col width=61></col>
				<col width=62></col>
				<col width=45></col>
				<tr align=center>
					<td class=c01>월</td>
					<td class=c01>일</td>
					<td class=c01>품목 / 규격</td>
					<td class=c01>단위</td>
					<td class=c01>수량</td>
					<td class=c01>단가</td>
					<td class=c01>공급가액</td>
					<td class=c01>세액</td>	
				</tr>
				<?php for($cnt=0;$cnt<$count;$cnt++){?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt"><?=$productname[$cnt]?></td>
					<td></td>
					<td style="font-size:8pt"><?=number_format($quantity[$cnt])?></td>
				<?php if($addtax!="Y") {
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
				<?php if($addtax!="Y") {
					  $taxsum=round($etcdata[$k]->price/(1+$taxrate/100));
					  $taxsumsale=$etcdata[$k]->price-$taxsum;
				   } else {
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
					<td></td>
					<td style="font-size:8pt">1</td>
				<?php if($addtax!="Y") {
					  $taxsum=round($deli_price/(1+$taxrate/100));
					  $taxsumsale=$deli_price-$taxsum;
				   } else {
					  $taxsum=$deli_price;
					  $taxsumsale=$taxsum*($taxrate/100);
				   }
				?>
					<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
					<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
					<td align=right style="font-size:8pt"><?=number_format($taxsumsale);?></td>
				</tr>
				<?php }?>
				<?php if($reserve>0) {
				   $cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">적립금</td>
					<td></td>
					<td style="font-size:8pt">1</td>
				<?php if($addtax!="Y") {
					  $taxsum=round($reserve/(1+$taxrate/100));
					  $taxsumsale=$reserve-$taxsum;
				   } else {
					  $taxsum=$reserve;
					  $taxsumsale=$taxsum*($taxrate/100);
				   }
				?>
					<td align=right style="font-size:8pt">-<?=number_format($taxsum)?></td>
					<td align=right style="font-size:8pt">-<?=number_format($taxsum)?></td>
					<td align=right style="font-size:8pt">-<?=number_format($taxsumsale);?></td>
				</tr>
				<?php }?>
				<?php if($dc_price<0) {
				   $cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">우수회원 할인</td>
					<td></td>
					<td style="font-size:8pt">1</td>
				<?php if($addtax!="Y") {
					  $taxsum=round($dc_price/(1+$taxrate/100));
					  $taxsumsale=$dc_price-$taxsum;
				   } else {
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
				</table>

				</td>
			</tr>
			<tr>
				<td colspan=2>

				<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=blue frame=void style="table-layout:fixed">
				<col width=></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=101></col>
				<col width=107></col>
				<tr align=center>
					<td class=c01>합계금액</td>
					<td class=c01>현금</td>
					<td class=c01>수표</td>
					<td class=c01>어음</td>
					<td class=c01>외상미수금</td>
					<td rowspan=2 class=c01>이 금액을 영수함</td>
				</tr>
				<tr align=center>
					<td align=right><B><?=number_format($totalsumprice)?></B>&nbsp;</td>	
					<td align=right></td>	
					<td align=right></td>	
					<td align=right></td>	
					<td align=right></td>	
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

		<div id=taxprint3 style="hide">
		<table width=620 border=1 cellspacing=0 cellpadding=0 bordercolor=red style="table-layout:fixed">
		<tr>
			<td>
			<table width=100% border=1 cellspacing=0 cellpadding=0 bordercolor=red style="table-layout:fixed">
			<tr>
				<td colspan=2>
				<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
				<tr>
					<td rowspan=2 width=70% height=40>
					<table border=0 cellspacing=0 cellpadding=0>
					<tr align=center>
						<td width=160></td>
						<td rowspan=2><font color=red size=3><b>세 금 계 산 서</b></font>&nbsp;&nbsp;</td>
						<td class=c02 rowspan=2><font size=5>(</font></td>
						<td class=c02>공 급 자</td>
						<td class=c02 rowspan=2><font size=5>)</font></td>
					</tr>
					<tr align=center>
						<td align=left class=c02>&nbsp;&nbsp;&nbsp;<font size=1 style="font-weight:normal">(별지 제 11호 서식)</font></td>
						<td class=c02>보 관 용</td>
					</tr>
					</table>
					</td>
					<td align=center width=10% class=c02> 책 번 호 </td>
					<td width=10% colspan=3 align=right class=c02>권</td>
					<td width=10% colspan=3 align=right class=c02>호</td>
				</tr>
				<tr>
					<td align=center class=c02> 일련번호 </td>
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
				<td width=310 height=100%>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void height=100% style="table-layout:fixed">
				<col width=18></col>
				<col width=54></col>
				<col width=></col>
				<col width=18></col>
				<col width=></col>
				<tr height=27 valign=middle>
					<td rowspan=4 class=c02>공 급 자</td>
					<td align=center class=c02 style="padding-top:3px"><nobr>등록번호</td>
					<td colspan=3 style="padding-left:5px;padding-top:3px"><B><font size=3><?=$companynum?></font></B></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c02 style="padding-top:3px">상<img width=20 height=0>호<br>(법인명)</td>
					<td style="padding-left:5px;padding-top:3px"><B><?=$companyname?></B></td>
					<td align=center class=c02 style="padding-top:3px">성명</td>
					<td style="padding-left:5px"><B><?=$companyowner?></B><img src=images/taxprint_sign.gif align=absmiddle hspace=2></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c02 style="padding-top:3px">사 업 장<br>주<img width=20 height=0>소</td>
					<td colspan=3 style="padding-left:5px"><B><?=$companyaddr?></B></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c02 style="padding-top:3px">업<img width=20 height=0>태</td>
					<td style="padding-left:5px;padding-top:3px"><B><?=$companybiz?></B></td>
					<td class=c02 style="padding-top:3px">종목</td>
					<td style="padding-left:5px;padding-top:3px"><B><?=$companyitem?></B></td>
				</tr>
				</table>
				</td>
				<td width=310 height=100%>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void height=100% style="table-layout:fixed">
				<col width=18></col>
				<col width=54></col>
				<col width=></col>
				<col width=18></col>
				<col width=></col>
				<form name=company2>
				<tr height=27 valign=middle>
					<td rowspan=4 class=c02>공 급 받 는 자</td>
					<td align=center class=c02 style="padding-top:3px"><nobr>등록번호</td>
					<td colspan=3 style="padding-left:5px;padding-top:3px"><input type=text class=nip name=companynum2 size=20 maxlength=13 onfocus="this.blur()"></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c02 style="padding-top:3px">상<img width=20 height=0>호<br>(법인명)</td>
					<td style="padding-left:5px;padding-top:3px"><input type=text class=nip name=companyname2 size=15 maxlength=30 onfocus="this.blur()"></td>
					<td align=center class=c02 style="padding-top:3px">성명</td>
					<td style="padding-left:5px"><input type=text class=nip name=ownername2 size=8 maxlength=16 onfocus="this.blur()"><img src=images/taxprint_sign.gif align=absmiddle hspace=2></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c02 style="padding-top:3px">사 업 장<br>주<img width=20 height=0>소</td>
					<td colspan=3 style="padding-left:5px"><input type=text class=nip name=companyaddr2 size=30 maxlength=50 onfocus="this.blur()"></td>
				</tr>
				<tr height=35 valign=middle>
					<td align=center class=c02 style="padding-top:3px">업<img width=20 height=0>태</td>
					<td style="padding-left:5px;padding-top:3px"><input type=text class=nip name=companybiz2 size=15 maxlength=30 onfocus="this.blur()"></td>
					<td class=c02 style="padding-top:3px">종목</td>
					<td style="padding-left:5px;padding-top:3px"><input type=text class=nip name=companyitem2 size=15 maxlength=30 onfocus="this.blur()"></td>
				</tr>
				</form>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan=2>
				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
				<col width=36></col>
				<col width=18></col>
				<col width=18></col>
				<col width=234></col>
				<col width=199></col>
				<col width=></col>
				<tr align=center>
					<td colspan=3 class=c02>작성일</td>
					<td class=c02>공급가액</td>
					<td class=c02>세 액</td>
					<td class=c02>비 고</td>
				</tr>
				<tr align=center>
					<td class=c02>년</td>
					<td class=c02>월</td>
					<td class=c02>일</td>
					<td rowspan=2 style="padding:0px;" height=100%>
					<table width=100% height=100% border=1 cellspacing=0 cellpadding=0 bordercolor=red frame=void style="table-layout:fixed">
					<col width=27></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<col width=></col>
					<tr align=center>
						<td class=c02>공란</td>
						<td class=c02>백</td>
						<td class=c02>십</td>
						<td class=c02>억</td>	
						<td class=c02>천</td>	
						<td class=c02>백</td>	
						<td class=c02>십</td>	
						<td class=c02>만</td>	
						<td class=c02>천</td>	
						<td class=c02>백</td>	
						<td class=c02>십</td>	
						<td class=c02>일</td>	
					</tr>
<?php 
					if($addtax!="Y") {
						$totalsale=round($totalprice/(1+$taxrate/100));
						$totaltax=$totalprice-$totalsale;
						$totalsumprice=$totalprice;
					} else {
						$totalsale=$totalprice;
						$totaltax=($totalprice*($taxrate/100));
						$totalsumprice=$totalsale+$totaltax;
					}
					$length=strlen($totalsale);
					$length2=strlen($totaltax);
?>

					<tr align=center height=24>
						<td><?=11-$length?></td>
						<td><?=($length>=11?substr($totalsale,-11,1):"")?></td>
						<td><?=($length>=10?substr($totalsale,-10,1):"")?></td>
						<td><?=($length>=9?substr($totalsale,-9,1):"")?></td>
						<td><?=($length>=8?substr($totalsale,-8,1):"")?></td>
						<td><?=($length>=7?substr($totalsale,-7,1):"")?></td>
						<td><?=($length>=6?substr($totalsale,-6,1):"")?></td>
						<td><?=($length>=5?substr($totalsale,-5,1):"")?></td>
						<td><?=($length>=4?substr($totalsale,-4,1):"")?></td>
						<td><?=($length>=3?substr($totalsale,-3,1):"")?></td>
						<td><?=($length>=2?substr($totalsale,-2,1):"")?></td>
						<td><?=($length>=1?substr($totalsale,-1,1):"")?></td>
					</tr>
					</table>
					</td>
					<td rowspan=2 style="padding:0px;" height=100%>
					<table width=100% height=100% border=1 cellspacing=0 cellpadding=0 bordercolor=red frame=void style="table-layout:fixed">
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<col width=10%></col>
					<tr align=center>
						<td class=c02>십</td>
						<td class=c02>억</td>	
						<td class=c02>천</td>	
						<td class=c02>백</td>	
						<td class=c02>십</td>	
						<td class=c02>만</td>	
						<td class=c02>천</td>	
						<td class=c02>백</td>	
						<td class=c02>십</td>	
						<td class=c02>일</td>	
					</tr>
					<tr align=center height=24>
						<td><?=($length2>=10?substr($totaltax,-10,1):"")?></td>
						<td><?=($length2>=9?substr($totaltax,-9,1):"")?></td>
						<td><?=($length2>=8?substr($totaltax,-8,1):"")?></td>
						<td><?=($length2>=7?substr($totaltax,-7,1):"")?></td>
						<td><?=($length2>=6?substr($totaltax,-6,1):"")?></td>
						<td><?=($length2>=5?substr($totaltax,-5,1):"")?></td>
						<td><?=($length2>=4?substr($totaltax,-4,1):"")?></td>
						<td><?=($length2>=3?substr($totaltax,-3,1):"")?></td>
						<td><?=($length2>=2?substr($totaltax,-2,1):"")?></td>
						<td><?=($length2>=1?substr($totaltax,-1,1):"")?></td>
					</tr>
					</table>
					</td>
					<td rowspan=2></td>
				</tr>
				<tr align=center height=24>
					<td><?=$year?></td>
					<td><?=$month?></td>
					<td><?=$day?></td>	
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan=2>

				<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
				<col width=18></col>
				<col width=18></col>
				<col width=></col>
				<col width=40></col>
				<col width=40></col>
				<col width=61></col>
				<col width=62></col>
				<col width=45></col>
				<tr align=center>
					<td class=c02>월</td>
					<td class=c02>일</td>
					<td class=c02>품목 / 규격</td>
					<td class=c02>단위</td>
					<td class=c02>수량</td>
					<td class=c02>단가</td>
					<td class=c02>공급가액</td>
					<td class=c02>세액</td>	
				</tr>
				<?php for($cnt=0;$cnt<$count;$cnt++){?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt"><?=$productname[$cnt]?></td>
					<td></td>
					<td style="font-size:8pt"><?=number_format($quantity[$cnt])?></td>
				<?php if($addtax!="Y") {
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
				<?php if($addtax!="Y") {
					  $taxsum=round($etcdata[$k]->price/(1+$taxrate/100));
					  $taxsumsale=$etcdata[$k]->price-$taxsum;
				   } else {
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
					<td></td>
					<td style="font-size:8pt">1</td>
				<?php if($addtax!="Y") {
					  $taxsum=round($deli_price/(1+$taxrate/100));
					  $taxsumsale=$deli_price-$taxsum;
				   } else {
					  $taxsum=$deli_price;
					  $taxsumsale=$taxsum*($taxrate/100);
				   }
				?>
					<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
					<td align=right style="font-size:8pt"><?=number_format($taxsum)?></td>
					<td align=right style="font-size:8pt"><?=number_format($taxsumsale);?></td>
				</tr>
				<?php }?>
				<?php if($reserve>0) {
				   $cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">적립금</td>
					<td></td>
					<td style="font-size:8pt">1</td>
				<?php if($addtax!="Y") {
					  $taxsum=round($reserve/(1+$taxrate/100));
					  $taxsumsale=$reserve-$taxsum;
				   } else {
					  $taxsum=$reserve;
					  $taxsumsale=$taxsum*($taxrate/100);
				   }
				?>
					<td align=right style="font-size:8pt">-<?=number_format($taxsum)?></td>
					<td align=right style="font-size:8pt">-<?=number_format($taxsum)?></td>
					<td align=right style="font-size:8pt">-<?=number_format($taxsumsale);?></td>
				</tr>
				<?php }?>
				<?php if($dc_price<0) {
				   $cnt++;
				?>
				<tr align=center>
					<td style="font-size:8pt"><?=$month?></td>
					<td style="font-size:8pt"><?=$day;?></td>
					<td style="font-size:8pt">우수회원 할인</td>
					<td></td>
					<td style="font-size:8pt">1</td>
				<?php if($addtax!="Y") {
					  $taxsum=round($dc_price/(1+$taxrate/100));
					  $taxsumsale=$dc_price-$taxsum;
				   } else {
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
				<tr><td colspan=8 align=center class=c02> ***** 이 하 여 백 ***** </td></tr>
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
				</table>

				</td>
			</tr>
			<tr>
				<td colspan=2>

				<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
				<col width=></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=101></col>
				<col width=107></col>
				<tr align=center>
					<td class=c02>합계금액</td>
					<td class=c02>현금</td>
					<td class=c02>수표</td>
					<td class=c02>어음</td>
					<td class=c02>외상미수금</td>
					<td rowspan=2 class=c02>이 금액을 영수함</td>
				</tr>
				<tr align=center>
					<td align=right><B><?=number_format($totalsumprice)?></B>&nbsp;</td>	
					<td align=right></td>	
					<td align=right></td>	
					<td align=right></td>	
					<td align=right></td>	
				</tr>
				</table>	
					
				</td>
			</tr>
			</table>

			</td>
		</tr>
		</table>
		</div>



		<div id=taxprint4 style="hide">
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
					<td style="padding-left:5px"><B><?=$companyowner?></B><img src=images/taxprint_sign.gif align=absmiddle hspace=2></td>	
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
					<td align=right style="border-left:0">&nbsp;<img src=images/taxprint_sign.gif align=absmiddle hspace=10></td>
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

		<div id=taxprint5 style="hide">
		<table width=620 border=1 cellspacing=0 cellpadding=0 bordercolor=red style="table-layout:fixed">
		<tr>
			<td>
			<table width=100% border=1 cellspacing=0 cellpadding=0 bordercolor=red>
			<tr>
				<td colspan=2>

				<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void  style="table-layout:fixed">
				<tr>
					<td rowspan=2 width=70% height=40>

					<table border=0 cellspacing=0 cellpadding=0>
					<tr align=center>
						<td width=160></td>
						<td rowspan=2><font color=red size=4><b>거 래 명 세 표</b></font>&nbsp;&nbsp;</td>
						<td rowspan=2 class=c02><font size=5>(</font></td>
						<td class=c02>공 급 자</td>
						<td rowspan=2 class=c02><font size=5>)</font></td>
					</tr>
					<tr align=center>
						<td align=left>&nbsp;</td>
						<td class=c02>보 관 용</td>
					</tr>
					</table>

					</td>
					<td align=center width=10% class=c02> 책 번 호 </td>
					<td width=10% colspan=3 align=right class=c02>권</td>
					<td width=10% colspan=3 align=right class=c02>호</td>
				</tr>
				<tr>
					<td align=center class=c02> 일련번호 </td>
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

				<table width=100% height=100% border=0 cellspacing=0 cellpadding=2 bordercolor=red frame=void>
				<tr>
					<td colspan=2>&nbsp;&nbsp;<?=$year?> <font color=red><b>년</b></font>&nbsp; <?=$month?> <font color=red><b>월</b></font>&nbsp; <?=$day?> <font color=red><b>일</b></font></td>
					<td style="font-family:times new roman;" width=30% class=c02><i>no.</i></td>
				</tr>
				<tr>
					<td></td>
					<td colspan=2 valign=bottom align=right style="border-bottom:thin solid;padding-right:10px;" class=c02>
					&nbsp;<font color=black size=3><B><?=$sender_name?></B></font>&nbsp;
					<font size=3>귀하</font></td>
				</tr>
				<tr>
					<td></td>
					<td colspan=2 valign=bottom class=c02>아래와 같이 공급합니다.</td>
				</tr>
				<tr>
					<td width=20% align=center class=c02>합계금액</td>
					<td colspan=2 align=right style="padding-right:10px;" class=c02>&nbsp;<font color=black size=3><B><?=number_format($totalsumprice)?></B></font>&nbsp;<font size=3>원정</font></td>
				</tr>
				</table>

				</td>
				<td width=50%>

				<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void  style="table-layout:fixed">
				<col width=20></col>
				<col width=54></col>
				<col width=></col>
				<col width=18></col>
				<col width=></col>
				<tr align=center height=35>
					<td rowspan=4 class=c02>공<br><br>급<br><br>자</td>
					<td class=c02 style="padding-top:3px">등록번호</td>
					<td colspan=3 style="padding-left:5px;padding-top:3px"><font size=3><B><?=$companynum?></B></font></td>
				</tr>
				<tr align=center height=35>
					<td class=c02 style="padding-top:3px">상<img width=20 height=0>호<br>(법인명)</td>
					<td style="padding-left:5px;padding-top:3px"><B><?=$companyname?></B></td>
					<td class=c02 style="padding-top:3px">성명</td>
					<td style="padding-left:5px"><B><?=$companyowner?></B><img src=images/taxprint_sign.gif align=absmiddle hspace=2></td>	
				</tr>
				<tr height=35>
					<td align=center class=c02 style="padding-top:3px">사 업 장<br>주<img width=20 height=0>소</td>
					<td colspan=3 style="padding-left:5px;padding-top:3px"><B><?=$companyaddr?></B></td>
				</tr>
				<tr align=center height=35>
					<td class=c02 style="padding-top:3px">업 태</td>
					<td style="padding-top:3px"><B><?=$companybiz?></B></td>
					<td class=c02 style="padding-top:3px">종목</td>
					<td style="padding-top:3px"><B><?=$companyitem?></B></td>	
				</tr>
				</table>

				</td>
			</tr>
			<tr>
				<td colspan=2>

				<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
				<col width=18></col>
				<col width=18></col>
				<col width=></col>
				<col width=33></col>
				<col width=33></col>
				<col width=59></col>
				<col width=61></col>
				<col width=45></col>
				<tr align=center>
					<td class=c02>월</td>
					<td class=c02>일</td>
					<td class=c02>품목 / 규격</td>
					<td class=c02>단위</td>
					<td class=c02>수량</td>
					<td class=c02>단가</td>
					<td class=c02>공급가액</td>
					<td class=c02>세 액</td>	
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
				<tr><td colspan=8 align=center class=c02> ***** 이 하 여 백 ***** </td></tr>
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
					<td colspan=6 class=c02> 소 계 </td>
					<td align=right><B><?=number_format($totalsale)?></B></td>
					<td align=right><B><?=number_format($totaltax)?></B></td>
				</tr>
				</table>

				</td>
			</tr>
			<tr>
				<td colspan=2 height=26>

				<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
				<col width=70></col>
				<col width=120></col>
				<col width=70></col>
				<col width=120></col>
				<col width=70></col>
				<col width=></col>
				<tr align=center>
					<td class=c02> 미수금 </td>
					<td>&nbsp;</td>
					<td class=c02> 합 계 </td>
					<td>&nbsp;<B><?=number_format($totalsumprice)?></B></td>
					<td style="border-right:0" class=c02> 인수자 </td>
					<td align=right style="border-left:0">&nbsp;<img src=images/taxprint_sign.gif align=absmiddle hspace=10></td>
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

		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script>
viewtax(document.form.taxkind.options[document.form.taxkind.selectedIndex].value,document.form.taxsele.options[document.form.taxsele.selectedIndex].value);
</script>
</body>
</html>
