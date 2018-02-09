<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
		<col width="9"></col>
		<col></col>
		<col width="60"></col>
		<tr height="19">
			<td background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/locationbg_left.gif">&nbsp;</td>
			<td bgcolor="#E2E6EA" valign="bottom" style="padding-right:10;padding-bottom:1px;"><?=$codenavi?></td>
			<td align="right" bgcolor="#E2E6EA" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/locationbg_right.gif" style="padding-right:3px;background-repeat:no-repeat;background-position:right"><A HREF="javascript:ClipCopy('http://<?=$_ShopInfo->getShopurl2()?>?<?=getenv("QUERY_STRING")?>')"><img src="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/btn_addr_copy.gif" border="0"></A></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
<?
if($_cdata->title_type=="image") {
	if(file_exists($Dir.DataDir."shopimages/etc/CODE".$code.".gif")) {
		echo "<tr>\n";
		echo "	<td align=center><img src=\"".$Dir.DataDir."shopimages/etc/CODE".$code.".gif\" border=0 align=absmiddle></td>\n";
		echo "</tr>\n";
	}
} else if($_cdata->title_type=="html") {
	if(strlen($_cdata->title_body)>0) {
		echo "<tr>\n";
		echo "	<td align=center>";
		if (strpos(strtolower($_cdata->title_body),"<table")!==false)
			echo $_cdata->title_body;
		else
			echo str_replace("\n","<br>",$_cdata->title_body);
		echo "	</td>\n";
		echo "</tr>\n";
	}
}
?>

<?if($_data->ETCTYPE["CODEYES"]!="N") {?>
<?
	$iscode=false;
	if(strlen($likecode)==3) {			//1차분류 (1차에 속한 모든 2차,3차분류를 보여준다) - 3차가 있는지 검사
		//1차가 최종분류일 경우엔 아무것도 보여주지 않는다.
		if($_cdata->type!="LX" && $_cdata->type!="TX") {	//하위분류가 있을 경우에만
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE codeA='".$codeA."' AND codeB!='000' AND codeC!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;
			$iscode=true;
			pmysql_free_result($result);

			$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
			$sql.= "WHERE codeA='".$codeA."' ";
			$sql.= "AND codeB!='000' AND codeC='000' AND codeD='000' AND group_code!='NO' ";
			$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

			if($cnt>0) {
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					if($i>0) $category_list.="<tr><td colspan=\"2\" style=\"border-bottom:#F0F0F0 1px solid;\"><img width=0></td></tr>\n";
					$category_list.="<tr>";
					$category_list.="	<td width=\"25%\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_iconaa.gif\" border=\"0\" align=\"absmiddle\"><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\"><FONT class=upcodename>".$row->code_name."</font></a></td>\n";
					$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
					if(!strstr($row->type,"X")) {
						$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
						$sql.= "WHERE codeA='".$row->codeA."' ";
						$sql.= "AND codeB='".$row->codeB."' AND codeC!='000' AND codeD='000' AND group_code!='NO' ";
						$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
						$sql.= "ORDER BY sequence DESC ";
						$result2=pmysql_query($sql,get_db_conn());
						$j=0;
						while($row2=pmysql_fetch_object($result2)) {
							if($j>0) $category_list.=" | ";
							$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->codeA.$row2->codeB.$row2->codeC.$row2->codeD."\"><FONT class=subcodename>".$row2->code_name."</font></a>";
							$j++;
						}
						pmysql_free_result($result2);
					}

					$category_list.="	</td>\n";
					$category_list.="</tr>\n";
					$i++;
				}
			} else {
				$category_list.="<tr>";
				$category_list.="	<td style=\"padding:10px;\" class=subcodename>";
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					if($i>0) $category_list.=" | ";
					$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
					$i++;
				}
				$category_list.="	</td>\n";
				$category_list.="</tr>\n";
			}
			$category_list.="</table>\n";
			pmysql_free_result($result);
		}
	} else if(strlen($likecode)==6) {	//2차분류 (2차에 속한 모든 3차,4차분류를 보여준다) - 4차가 있는지 검사
		//2차가 최종분류일 경우엔 1차에 속한 2차를 보여준다
		if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//하위분류가 있을 경우에만
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC!='000' AND codeD!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;
			$iscode=true;
			pmysql_free_result($result);

			$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
			$sql.= "WHERE codeA='".$codeA."' ";
			$sql.= "AND codeB='".$codeB."' AND codeC!='000' AND codeD='000' AND group_code!='NO' ";
			$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$category_list="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			if($cnt>0) {
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					if($i>0) $category_list.="<tr><td colspan=\"2\" style=\"border-bottom:#F0F0F0 1px solid;\"><img width=0></td></tr>\n";
					$category_list.="<tr>";
					$category_list.="	<td width=\"25%\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_iconaa.gif\" border=\"0\" align=\"absmiddle\"><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\"><FONT class=upcodename>".$row->code_name."</FONT></a></td>\n";
					$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
					if(!strstr($row->type,"X")) {
						$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
						$sql.= "WHERE codeA='".$row->codeA."' ";
						$sql.= "AND codeB='".$row->codeB."' AND codeC='".$row->codeC."' AND codeD!='000' AND group_code!='NO' ";
						$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
						$sql.= "ORDER BY sequence DESC ";
						$result2=pmysql_query($sql,get_db_conn());
						$j=0;
						while($row2=pmysql_fetch_object($result2)) {
							if($j>0) $category_list.=" | ";
							$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->codeA.$row2->codeB.$row2->codeC.$row2->codeD."\"><FONT class=subcodename>".$row2->code_name."</FONT></a>";
							$j++;
						}
						pmysql_free_result($result2);
					}

					$category_list.="	</td>\n";
					$category_list.="</tr>\n";
					$i++;
				}
			} else {
				$category_list.="<tr>";
				$categ
