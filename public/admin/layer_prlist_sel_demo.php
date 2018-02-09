<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

include("header.php");
?>

<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; <?=$subPage_dept2_title?> &gt;<span><?=$subPage_dept3_title?> 관리</span></p></div></div>

<form name='insertForm' id='insertForm' method='POST' enctype="multipart/form-data">

<?include("layer_prlistPop.php");?>

<div class="table_style01">
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">					
<col width=140></col>
<col width=></col>
<TR>
	<th><span>관련상품</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','relationProduct');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
	<td align="left">
			<div style="margin-top:0px; margin-bottom: 0px;">								
				<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_relationProduct">	
				<input type="hidden" name="limit_relationProduct" id="limit_relationProduct" value="5"/>								
					<colgroup>
						<col width=20></col>
						<col width=50></col>
						<col width=></col>
					</colgroup>
				<?foreach($thisBannerProduct as $bannerProductKey=>$bannerProduct){?>	
					<tr align="center">
						<td style='border:0px'>
							<a name="pro_upChange" style="cursor: hand;">
								<img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
							</a>
							<br>
							<a name="pro_downChange" style="cursor: hand;">
								<img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
							</a>
						</td>
						<td style='border:0px'>
							<img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bannerProduct['tinyimage']?>" border="1"/>
							<input type='hidden' name='relationProduct[]' value='<?=$bannerProduct[productcode]?>'>
						</td>
						<td style='border:0px' align="left"><?=$bannerProduct[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bannerProduct[productcode]?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
						</td>
					</tr>
				<?}?>
				</table>
			</div>
	</td>
</TR>
</table>
</div>

<div class="table_style01">
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">					
<col width=140></col>
<col width=></col>
<TR>
	<th><span>관련상품</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','relationProduct1');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
	<td align="left">
			<div style="margin-top:0px; margin-bottom: 0px;">								
				<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_relationProduct1">	
				<input type="hidden" name="limit_relationProduct1" id="limit_relationProduct1" value=""/>								
					<colgroup>
						<col width=20></col>
						<col width=50></col>
						<col width=></col>
					</colgroup>
				<?foreach($thisBannerProduct as $bannerProductKey=>$bannerProduct){?>	
					<tr align="center">
						<td style='border:0px'>
							<a name="pro_upChange" style="cursor: hand;">
								<img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
							</a>
							<br>
							<a name="pro_downChange" style="cursor: hand;">
								<img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
							</a>
						</td>
						<td style='border:0px'>
							<img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bannerProduct['tinyimage']?>" border="1"/>
							<input type='hidden' name='relationProduct1[]' value='<?=$bannerProduct[productcode]?>'>
						</td>
						<td style='border:0px' align="left"><?=$bannerProduct[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bannerProduct[productcode]?>','relationProduct1');" border="0" style="cursor: hand;vertical-align:middle;" />
						</td>
					</tr>
				<?}?>
				</table>
			</div>
	</td>
</TR>
</table>
</div>
</form>

<!--// 하단에 추가-->
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>



<?php
include("copyright.php");
?>
