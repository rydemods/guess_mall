<!-- H3 -->
<table width=100% border=0 cellspacing=0 cellpadding=0 style="table-layout:fixed">
<tr align=center valign=top>
	<td width=34%>
<?
	########## 1 start (160*160) ##########
	$kk=1;
	if(isset($specialprlist[$kk])) {
		echo "<table width=100% border=0 cellspacing=0 cellpadding=0 align=center id=\"M".$specialprlist[$kk]->productcode."\" onmouseover=\"quickfun_show(this,'M".$specialprlist[$kk]->productcode."','')\" onmouseout=\"quickfun_show(this,'M".$specialprlist[$kk]->productcode."','none')\">\n";
		echo "<tr>\n";
		echo "	<td height=170 align=center valign=top style=padding:5,10><A HREF=\"javascript:GoItem('".$specialprlist[$kk]->productcode."')\">";
		if (strlen($specialprlist[$kk]->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$specialprlist[$kk]->minimage)) {
			echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($specialprlist[$kk]->minimage)."\" border=0 ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$specialprlist[$kk]->minimage);
			if ($width[0]>=$width[1] && $width[0]>=160) echo "width=160 ";
			else if ($width[1]>=160) echo "height=160 ";
		} else {
			echo "<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
		}
		echo "	></a></td>\n";
		echo "</tr>\n";
		echo "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','M','".$specialprlist[$kk]->productcode."','".($specialprlist[$kk]->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
		echo "<tr>\n";
		echo "	<td align=center valign=top style=padding:0,10>\n";
		echo "	<table width=160 border=0 cellspacing=0 cellpadding=0>\n";
		echo "	<tr>\n";
		echo "		<td height=7> </td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td height=2></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td valign=top style=\"word-break:break-all;\"><A HREF=\"javascript:GoItem('".$specialprlist[$kk]->productcode."')\">".viewproductname($specialprlist[$kk]->productname,$specialprlist[$kk]->etctype,$specialprlist[$kk]->selfcode)."</a></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td height=4></td>\n";
		echo "	</tr>\n";
		if($specialprlist[$kk]->consumerprice>0) {	//소비자가
			echo "	<tr>\n";
			echo "		<td height=17 class=verdana2 style=\"color:#A7A7A7\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle><strike>".number_format($specialprlist[$kk]->consumerprice)."</strike>원";
			echo "		</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td height=17 class=verdana2 style=\"font-weight:bold;color:#FF3243 !important\">";
		if($dicker=dickerview($specialprlist[$kk]->etctype,number_format($specialprlist[$kk]->sellprice)."원",1)) {
			echo $dicker;
		} else if(strlen($_data->proption_price)==0) {
			echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle>".number_format($specialprlist[$kk]->sellprice)."원";
			if (strlen($specialprlist[$kk]->option_price)!=0) echo "(기본가)";
		} else {
			echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle>";
			if (strlen($specialprlist[$kk]->option_price)==0) echo number_format($specialprlist[$kk]->sellprice)."원";
			else echo str_replace("[PRICE]",number_format($specialprlist[$kk]->sellprice),$_data->proption_price);
		}
		if ($specialprlist[$kk]->quantity=="0") echo soldout();
		echo "		</td>\n";
		echo "	</tr>\n";
		if($specialprlist[$kk]->consumerprice<=0) {
			echo "	<tr>\n";
			echo "		<td height=17></td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td height=4></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "&nbsp;";
	}
	$kk++;
	########## 1 end ##########
?>
	</td>
	<td width=1 bgcolor=DADADA nowrap></td>
	<td width=34%>
<?
	########## 2 start (160*160) ##########
	if(isset($specialprlist[$kk])) {
		echo "<table width=100% border=0 cellspacing=0 cellpadding=0 align=center id=\"M".$specialprlist[$kk]->productcode."\" onmouseover=\"quickfun_show(this,'M".$specialprlist[$kk]->productcode."','')\" onmouseout=\"quickfun_show(this,'M".$specialprlist[$kk]->productcode."','none')\">\n";
		echo "<tr>\n";
		echo "	<td height=170 align=center valign=top style=padding:5,10><A HREF=\"javascript:GoItem('".$specialprlist[$kk]->productcode."')\">";
		if (strlen($specialprlist[$kk]->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$specialprlist[$kk]->minimage)) {
			echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($specialprlist[$kk]->minimage)."\" border=0 ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$specialprlist[$kk]->minimage);
			if ($width[0]>=$width[1] && $width[0]>=160) echo "width=160 ";
			else if ($width[1]>=160) echo "height=160 ";
		} else {
			echo "<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
		}
		echo "	></a></td>\n";
		echo "</tr>\n";
		echo "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','M','".$specialprlist[$kk]->productcode."','".($specialprlist[$kk]->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
		echo "<tr>\n";
		echo "	<td align=center valign=top style=padding:0,10>\n";
		echo "	<table width=160 border=0 cellspacing=0 cellpadding=0>\n";
		echo "	<tr>\n";
		echo "		<td height=7> </td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td height=2></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td valign=top style=\"word-break:break-all;\"><A HREF=\"javascript:GoItem('".$specialprlist[$kk]->productcode."')\">".viewproductname($specialprlist[$kk]->productname,$specialprlist[$kk]->etctype,$specialprlist[$kk]->selfcode)."</a></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td height=4></td>\n";
		echo "	</tr>\n";
		if($specialprlist[$kk]->consumerprice>0) {	//소비자가
			echo "	<tr>\n";
			echo "		<td height=17 class=verdana2 style=\"color:#A7A7A7\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle><strike>".number_format($specialprlist[$kk]->consumerprice)."</strike>원";
			echo "		</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td height=17 class=verdana2 style=\"font-weight:bold;color:#FF3243 !important\">";
		if($dicker=dickerview($specialprlist[$kk]->etctype,number_format($specialprlist[$kk]->sellprice)."원",1)) {
			echo $dicker;
		} else if(strlen($_data->proption_price)==0) {
			echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle>".number_format($specialprlist[$kk]->sellprice)."원";
			if (strlen($specialprlist[$kk]->option_price)!=0) echo "(기본가)";
		} else {
			echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle>";
			if (strlen($specialprlist[$kk]->option_price)==0) echo number_format($specialprlist[$kk]->sellprice)."원";
			else echo str_replace("[PRICE]",number_format($specialprlist[$kk]->sellprice),$_data->proption_price);
		}
		if ($specialprlist[$kk]->quantity=="0") echo soldout();
		echo "		</td>\n";
		echo "	</tr>\n";
		if($specialprlist[$kk]->consumerprice<=0) {
			echo "	<tr>\n";
			echo "		<td height=17></td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td height=4></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "&nbsp;";
	}
	$kk++;
	########## 2 end ##########
?>
	</td>
	<td width=1 bgcolor=DADADA nowrap></td>
	<td width=34%>
<?
	########## 3 start (160*160) ##########
	if(isset($specialprlist[$kk])) {
		echo "<table width=100% border=0 cellspacing=0 cellpadding=0 align=center id=\"M".$specialprlist[$kk]->productcode."\" onmouseover=\"quickfun_show(this,'M".$specialprlist[$kk]->productcode."','')\" onmouseout=\"quickfun_show(this,'M".$specialprlist[$kk]->productcode."','none')\">\n";
		echo "<tr>\n";
		echo "	<td height=170 align=center valign=top style=padding:5,10><A HREF=\"javascript:GoItem('".$specialprlist[$kk]->productcode."')\">";
		if (strlen($specialprlist[$kk]->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$specialprlist[$kk]->minimage)) {
			echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($specialprlist[$kk]->minimage)."\" border=0 ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$specialprlist[$kk]->minimage);
			if ($width[0]>=$width[1] && $width[0]>=160) echo "width=160 ";
			else if ($width[1]>=160) echo "height=160 ";
		} else {
			echo "<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
		}
		echo "	></a></td>\n";
		echo "</tr>\n";
		echo "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','M','".$specialprlist[$kk]->productcode."','".($specialprlist[$kk]->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
		echo "<tr>\n";
		echo "	<td align=center valign=top style=padding:0,10>\n";
		echo "	<table width=160 border=0 cellspacing=0 cellpadding=0>\n";
		echo "	<tr>\n";
		echo "		<td height=7> </td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td height=2></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td valign=top style=\"word-break:break-all;\"><A HREF=\"javascript:GoItem('".$specialprlist[$kk]->productcode."')\">".viewproductname($specialprlist[$kk]->productname,$specialprlist[$kk]->etctype,$specialprlist[$kk]->selfcode)."</a></td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td height=4></td>\n";
		echo "	</tr>\n";
		if($specialprlist[$kk]->consumerprice>0) {	//소비자가
			echo "	<tr>\n";
			echo "		<td height=17 class=verdana2 style=\"color:#A7A7A7\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle><strike>".number_format($specialprlist[$kk]->consumerprice)."</strike>원";
			echo "		</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td height=17 class=verdana2 style=\"font-weight:bold;color:#FF3243 !important\">";
		if($dicker=dickerview($specialprlist[$kk]->etctype,number_format($specialprlist[$kk]->sellprice)."원",1)) {
			echo $dicker;
		} else if(strlen($_data->proption_price)==0) {
			echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle>".number_format($specialprlist[$kk]->sellprice)."원";
			if (strlen($specialprlist[$kk]->option_price)!=0) echo "(기본가)";
		} else {
			echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle>";
			if (strlen($specialprlist[$kk]->option_price)==0) echo number_format($specialprlist[$kk]->sellprice)."원";
			else echo str_replace("[PRICE]",number_format($specialprlist[$kk]->sellprice),$_data->proption_price);
		}
		if ($specialprlist[$kk]->quantity=="0") echo soldout();
		echo "		</td>\n";
		echo "	</tr>\n";
		if($specialprlist[$kk]->consumerprice<=0) {
			echo "	<tr>\n";
			echo "		<td height=17></td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td height=4></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "&nbsp;";
	}
	$kk++;
	########## 3 end ##########
?>
	</td>
</tr>
</table>
