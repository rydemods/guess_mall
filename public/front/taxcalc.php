<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>


<form id=taxcalcform name=taxcalcform method=post action="<?=$Dir.FrontDir?>taxcalc_res.php" target=taxcalcpop>
<input type=hidden name=ordercode value="<?=$_POST[ordercode]?>">
<input type=hidden name=productname value="<?=$_POST[productname]?>">
<style type="text/css">
table {font-size:12px;border-top:2px solid #333;}
table th {font-weight:lighter; text-align:left; background-color:#f4f4f4; border-bottom:1px solid #c9c9c9; padding:5px 10px;}
table td {padding:5px 10px; border-bottom:1px solid #c9c9c9; } 
</style>
<p><img src="../images/001/tit_tax_pop.gif" alt="세금계산서 신청" /></p>
<table width="480" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<th>사업자번호</th>
		<td colspan=3><input type=text name="busino" value="" class=line required  option="regNum" label="사업자번호" size=10 maxlength=30>(숫자만기입)</td>
	</tr>
	<tr>
		<th>회사명</th>
		<td><input type=text name="company" value="" class=line required label="회사명" size=10></td>
		<th>대표자명</th>
		<th><input type=text name="name" value="" class=line required label="대표자명" size=10></th>
	</tr>
	<tr>
		<th>업태</th>
		<td><input type=text name="service" value="" class=line required label="업태" size=10></td>
		<th>종목</th>
		<th><input type=text name="item" value="" class=line required label="종목" size=10></th>
	</tr>
	<tr>
		<th>사업장주소</th>
		<td colspan=3><input type=text name="address" value="" class=line required label="사업장주소" size=40></td>
	</tr>
</table>
<div style="text-align:center; margin-top:8px;"><input type="submit" value="" style="background:url(../images/common/btn_tax_receipt01.gif) no-repeat; width:90px; height:24px; border:0px; cursor:pointer"></div>
</form>



