<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################


$todayY = $_POST['todayY'];
$todayM = $_POST['todayM'];
$todayD = $_POST['todayD'];
$prcode = $_POST['prcode'];

if($prcode){
	$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}'";
}else{
	$sql = "SELECT 
						a.*
						,b.productname
						, b.img_i
						, b.img_s
						, b.img_m
						, b.img_l 
						, b.tinyimage
					from tblproductoneday a
					LEFT JOIN tblproduct b on a.productcode = b.productcode
					where applydate = '{$todayY}-{$todayM}-{$todayD}'";
}

$result = pmysql_query($sql,get_db_conn());

$row=pmysql_fetch_object($result);


$imagepath=$Dir.DataDir."shopimages/product/";
?>

<?php include("header.php"); ?>
<style>td {line-height:18pt;}</style>
<link rel="styleSheet" href="/css/admin.css" type="text/css"></link>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('MainPrdtFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--

function move_save()
{
	if(validate()){
		parent.OndayApply();
	}

}

function goList(){
	parent.GoList();
}

function validate(){
	var dcprice = document.form1.dcprice;
	if(dcprice.value==""||dcprice.value==null){
		alert("특가를 입력해야 합니다.");
		dcprice.focus();
		return false;
	}
	
	if(dcprice.value.match(/[^0-9]/g)){
		alert("특가는 숫자만 입력해야 합니다.");
		dcprice.value=dcprice.value.replace(/[^0-9]/g,'');
		dcprice.focus();
		return false;
	}
	
	return true;
}

//-->
</SCRIPT>
<form name=form1 action="market_onedayprice_indb.php" method=post target="">
<input type="hidden" name="mode" value="regist">
<input type="hidden" name="prcode" value="<?=$row->productcode?>">
<input type="hidden" name="sellprice" value="<?=$row->sellprice?>">

	<div class="main_view_setup_wrap" style="margin-top:15px;">
		<div class="group">&nbsp;<?=$todayY?>&nbsp;.<?=$todayM?>&nbsp;.<?=$todayD?></div>

		<div class="list" style="border: 0px solid;">

			<div class="table_main_setup"  id="divscroll" style="width: 480px;height:400px;overflow-x:hidden;overflow-y:auto;">
				<div class="table_style01">
					<table width=100% cellpadding=0 cellspacing=0 border=0>
						<tr>
								<th><span>상품이미지</span></th>
								<td style="text-align: left">
								<?php if($row->tinyimage){	?>
									<img src="<?=$imagepath.$row->tinyimage?>" width="100px">
								<?php } ?>
								</td>
							</tr>
							<tr>
								<th><span>상품명</span></th>
								<td style="text-align: left"><?=$row->productname?></td>
							</tr>
							<tr>
								<th><span>원가</span></th>
								<td style="text-align: left">
								<?php if($row->sellprice){?>
									<?=$row->sellprice?> 원
								<?php }?>
								</td>
						</tr>
						<tr>
							<th><span>특가</span></th>
							<td style="text-align: left">
								<input class="w200" type="text" name="dcprice" maxlength="25" value="<?=$row->dcprice?>"> 원
							</td>
						</tr>
						<tr>
							<th><span>메모</span></th>
							<td style="text-align: left"><textarea name="memo" style="width: 250px;height:160px;"><?=$row->memo?></textarea></td>
						</tr>
					</table>
					
				</div>
			</div>
		</div>	

	</div>
</form>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<TD align=center><a href="javascript:move_save();"><img src="images/btn_confirm_com.gif" border="0"></a>&nbsp;&nbsp;<a href="javascript:goList();"><img src="images/btn_list_com.gif" border="0"></a></TD>
</tr>
</table>

<form name=form_reg action="market_onedayprice_indb.php" method=post>
	<input type="hidden" name="mode" value="regist">
	<input type="hidden" name="prcode" value="<?=$row->productcode?>">
	<input type="hidden" name="">
</form>

<?php if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?php }?>
<?=$onload?>
