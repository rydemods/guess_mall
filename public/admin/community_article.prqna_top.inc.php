<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr><td height=10></td></tr>
<tr>
	<td>
	<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#f0f0f0 style="table-layout:fixed">
	<tr>
		<td style="border:#f0f0f0 solid 1px">
		<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#F1F1F1 style="table-layout:fixed">
		<tr>
			<td align=center style="padding:5">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td bgcolor=#FFFFFF style="border:#f0f0f0 solid 1px; padding:5">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=80></col>
				<col width=8></col>
				<col width=3></col>
				<col width=15></col>
				<col width=></col>
				<col width=100></col>
				<tr>
					<td align=center>
<?php // hspark
					echo "<A HREF=\"http://{$shopurl}/front/productdetail.php?productcode={$_pdata->productcode}\" target=\"_blank\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
					if (ord($_pdata->tinyimage)) {
						echo "<img src=\"". getProductImage($Dir.DataDir.'shopimages/product/',$_pdata->tinyimage)."\" border=0 width=60>";
					} else {
						echo "<img src=\"".$Dir."images/no_img.gif\" border=0 align=center>";
					}
					echo "</A>";
?>
					</td>
					<td>&nbsp;</td>
					<td bgcolor=#f0f0f0></td>
					<td>&nbsp;</td>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr height=20>
						<td>상<img width=6 height=0>품<img width=6 height=0>명 : <A HREF="http://<?=$shopurl?>/front/productdetail.php?productcode=<?=$_pdata->productcode?>" target="_blank" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($_pdata->productname,$_pdata->etctype,"").(ord($_pdata->selfcode)?" - ".$_pdata->selfcode:"")?></FONT></A></td>
					</tr>
					<tr height=20>
						<td>상품가격 : <font class=prprice>
<?php
						if($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice)."원",1)) {
							echo $dicker;
						} else if(ord($_data->proption_price)==0) {
							echo "<img src=\"http://{$shopurl}images/common/won_icon.gif\" border=0 align=absmiddle>".number_format($_pdata->sellprice)."원";
							if (ord($_pdata->option_price)!=0) echo "(기본가)";
						} else {
							if (ord($_pdata->optionprice)==0) echo number_format($_pdata->sellprice)."원";
							else echo str_replace("[PRICE]",number_format($_pdata->sellprice),$_data->proption_price);
						}
						if ($_pdata->quantity=="0") echo soldout();
?>
						</font>
						</td>
					</tr>
					</table>
					</td>
					<td>
					<A HREF="http://<?=$shopurl?>/front/productdetail.php?productcode=<?=$_pdata->productcode?>" target="_blank" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;">상품바로가기</A>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr><td height=10></td></tr>
</table>