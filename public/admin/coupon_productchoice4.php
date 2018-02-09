<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT a.vender,a.id,a.com_name, b.brandname, b.bridx FROM tblvenderinfo a left join tblproductbrand b on a.vender=b.vender WHERE a.delflag='N' ORDER BY a.id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_array($result)) {
		$venderlist[]=$row;
	}
	pmysql_free_result($result);
}


$seasonlist=array();
$sql = "SELECT season_year, season, season_kor_name FROM tblproductseason WHERE use_yn='Y' ORDER BY season_year ASC, season_gb ASC";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_array($result)) {
	$seasonlist[]=$row;
}
pmysql_free_result($result);

?>


<link rel="stylesheet" href="style.css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".CLS_couponAccept").click(function(){
		var thisText	= "";
		var thisVal		= "";
		var brand		= $("select[name='brand']").val();
		var season		= $("select[name='season']").val();

		thisText	= $("select[name='brand'] option:selected").text();
		thisVal	= brand;
		if(season){
			thisText	= thisText + " > " + $("select[name='season'] option:selected").text();
			thisVal	= thisVal+"|"+season;
		}

		var set_brandseason	= $("#ID_productLayer input[name='set_productcode[]']", opener.document).length;
		var inProduct	= 0;
		if (set_brandseason > 0)
		{
			$("#ID_productLayer input[name='set_productcode[]']", opener.document).each(function(){
				var ex_set_alt			= $(this).val();
				var ex_set_alt_arr	= ex_set_alt.split("|");
				var ex_set_brand	= ex_set_alt_arr[0]+"|"+ex_set_alt_arr[1];
				var ex_set_season	= ex_set_alt_arr[2]+"|"+ex_set_alt_arr[3];
				//alert(ex_set_season);
			
				if (ex_set_brand == brand) {
					//alert('1');					
					if (ex_set_season == '|') {							
						//alert('2');					
						inProduct	= inProduct + 1;
					} else {
							//alert('3');		
						if (ex_set_season == season) {
							//alert('4');	
							inProduct	= inProduct + 1;
						} else {
							if (season == '|') {
								//alert('5');			
								$(this).parent().remove();
							} else {
								//alert('6');			
							}
						}
					}
				}
			});
		}

		if (inProduct == 0)
		{
			thisVal2	= brand+"|"+season;
			var tempHtml = $("#ID_productLayer", opener.document).html();
			$("#ID_productLayer", opener.document).html(tempHtml+"<div style='padding:5px 0px;'><a href=\"javascript:;\" onClick=\"javascript:$(this).parent().remove();\"><img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' /></a>&nbsp;&nbsp;"+thisText+"<input type = 'hidden' name ='set_productcode[]' value = '"+thisVal+"'></div>");
		}
		
		$("input[name='productcode']", opener.document).val("BRANDSEASONS");
	})
})
</script>
<!-- 라인맵 -->
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td>
			<div class="title_depth2"></div>
			
			<!-- 테이블스타일01 -->
			<div class="table_style01 pt_20">
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
				<colgroup>
				</colgroup>
					<tr>
						<th style="width:100px;"><span>브랜드</span></th>
						<td>
						<select name="brand" style="width:200px">
						<?
						foreach($venderlist as $k => $v) {
							echo "<option value='".$v["bridx"]."|".$v["vender"]."'>".$v["brandname"]."</option>";
						}
						?>
						</select>
						</td>
					</tr>
					<tr>
						<th style="width:100px;"><span>시즌</span></th>
						<td>
						<select name="season" style="width:200px">
						<option value="|">전체</option>
						<?
						foreach($seasonlist as $k => $v) {
							echo "<option value='".$v["season_year"]."|".$v["season"]."'>".$v["season_kor_name"]."</option>";
						}
						?>
						</select>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr><td height="50" align = 'center'><img src = '../admin/images/botteon_save.gif' class = 'hand CLS_couponAccept'></td></tr>
	</table>
</form>