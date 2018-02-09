<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/cscenter_ascode.php");

$ordercode=$_REQUEST["ordercode"];
$productcode=$_REQUEST["productcode"];
$idx=$_REQUEST["idx"];

$ordercode="2016092916275720874A";
$productcode="002001001000000747";
$idx="4444";

#주문정보, 회원정보 가져오기
$sql="select m.* from tblorderinfo o left join tblorderproduct op on (o.ordercode=op.ordercode) left join tblmember m on (o.id=m.id) where o.ordercode='".$ordercode."' and op.idx='".$idx."'";
$result=pmysql_query($sql);
$data=pmysql_fetch_array($result);

#매장정보 가져오기
$store_sql="select * from tblstore order by name";
$store_result=pmysql_query($store_sql);


?>

<script type="text/javascript">
function add(){
	var table = document.getElementById('table');
	if (table.rows.length>39){
		alert("다중 업로드는 최대 40개만 지원합니다");
		return;
	}
	date	= new Date();
	oTr		= table.insertRow( table.rows.length );
	oTr.id	= date.getTime();
	oTd	= oTr.insertCell(0);
	tmpHTML = "<input type=file name='file[]' style='width:80%' class=line onChange='preview(this.value," + oTr.id +")'> <a href='javascript:del(" + oTr.id + ")'><img src='images/btn_del.gif' align=absmiddle></a>";
	oTd.innerHTML = tmpHTML;
}

function del(index)
{
	var table = document.getElementById('table');
    for (i=0;i<table.rows.length;i++) if (index==table.rows[i].id) table.deleteRow(i);
	
}
</script>

<table width=100% id=table cellpadding=0 cellspacing=0 border=0 style="border:0px;">
	<col class=engb align=center>
	<tr>
		<td width=100%>
			<input type=file name="file[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
			<a href="javascript:add()"><img src="images/btn_add1.gif" align=absmiddle></a>
		</td>
	</tr>
</table>

<select>
	<option value="">====== 매장선택 ======</option>
<?while($store_data=pmysql_fetch_array($store_result)){?>
	<option value="<?=$store_data["sno"]?>"><?=$store_data["name"]?></option>
<?}?>
</select>

<br>

<?foreach($as_gubun as $ag=>$agv){?>
	<input type="radio" name="as_type" value="<?=$ag?>"><?=$agv?>
<?}?>

<br>
<?foreach($as_receipt as $ar=>$arv){?>
	<input type="radio" name="receipt_type" value="<?=$ar?>"><?=$arv?>
<?}?>
<br>
<?foreach($as_depreciation as $ad=>$adv){?>
	<input type="radio" name="depreciation_type" value="<?=$ad?>"><?=$adv?>
<?}?>

<br>
<?foreach($as_repair as $ae=>$aev){?>
	<input type="radio" name="repairs_type" value="<?=$ae?>"><?=$aev?>
<?}?>

<br>
<?foreach($as_cash as $ac=>$acv){?>
	<input type="radio" name="cash_type" value="<?=$ac?>"><?=$acv?>
<?}?>