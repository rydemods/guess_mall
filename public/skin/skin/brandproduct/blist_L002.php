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
			<td background="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/locationbg_left.gif">&nbsp;</td>
			<td bgcolor="#E2E6EA" valign="bottom" style="padding-right:10;padding-bottom:1px;"><?=$codenavi?></td>
			<td align="right" bgcolor="#E2E6EA" background="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/locationbg_right.gif" style="padding-right:3px;background-repeat:no-repeat;background-position:right"><A HREF="javascript:ClipCopy('http://<?=$_ShopInfo->getShopurl2()?>?<?=$_SERVER['QUERY_STRING']?>')"><img src="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/btn_addr_copy.gif" border="0"></A></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
<?
if($_bdata->title_type=="image") {
	if(file_exists($Dir.DataDir."shopimages/etc/BRD".$brandcode.".gif")) {
		echo "<tr>\n";
		echo "	<td align=center><img src=\"".$Dir.DataDir."shopimages/etc/BRD".$brandcode.".gif\" border=0 align=absmiddle></td>\n";
		echo "</tr>\n";
	}
} else if($_bdata->title_type=="html") {
	if(strlen($_bdata->title_body)>0) {
		echo "<tr>\n";
		echo "	<td align=center>";
		if (strpos(strtolower($_bdata->title_body),"<table")!=false)
			echo $_bdata->title_body;
		else
			echo nl2br($_bdata->title_body);
		echo "	</td>\n";
		echo "</tr>\n";
	}
}

if($_data->ETCTYPE["CODEYES"]!="N" && strlen($brand_qryA)>0) {

	$iscode=false;
	if(strlen($likecode)==0) {			//0차분류 (0차에 속한 모든 1차,2차분류를 보여준다) - 2차가 있는지 검사
		//0차가 최종분류일 경우엔 아무것도 보여주지 않는다.
		$sql = "SELECT COUNT(*) as cnt FROM tblproductcode ";
		$sql.= "WHERE code_b!='000' AND group_code!='NO' ";
		$sql.= $brand_qryA;
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$cnt=$row->cnt;
		$iscode=true;
		pmysql_free_result($result);

		$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
		$sql.= "WHERE code_b='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
		$sql.= "AND type IN ('L','T','LX','TX') ";
		$sql.= $brand_qryA;
		$sql.= "ORDER BY sequence DESC ";
		$result=pmysql_query($sql,get_db_conn());
		$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

		if($cnt>0) {
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				if($i>0) $category_list.="<tr><td colspan=\"2\" style=\"border-bottom:#F0F0F0 1px solid;\"><img width=0></td></tr>\n";
				$category_list.="<tr>";
				$category_list.="	<td width=\"25%\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/brandproduct/".$_bdata->list_type."/plist_skin_iconaa.gif\" border=\"0\" align=\"absmiddle\" hspace=\"5\"><a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>".$row->code_name."</font></a></td>\n";
				$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
				if(!strstr($row->type,"X")) {
					$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
					$sql.= "WHERE code_a='".$row->code_a."' ";
					$sql.= "AND code_b IN ('".implode("','",$blistcode_b[$row->code_a])."') ";
					$sql.= "AND code_c='000' AND code_d='000' AND group_code!='NO' ";
					$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
					$sql.= "ORDER BY sequence DESC ";
					$result2=pmysql_query($sql,get_db_conn());
					$j=0;
					while($row2=pmysql_fetch_object($result2)) {
						if($j>0) $category_list.=" | ";
						$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>".$row2->code_name."</font></a>";
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
				$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
				$i++;
			}
			$category_list.="	</td>\n";
			$category_list.="</tr>\n";
		}
		$category_list.="</table>\n";
		pmysql_free_result($result);
	} else if(strlen($likecode)==3) {			//1차분류 (1차에 속한 모든 2차,3차분류를 보여준다) - 3차가 있는지 검사
		//1차가 최종분류일 경우엔 아무것도 보여주지 않는다.
		if($_cdata->type!="LX" && $_cdata->type!="TX") {	//하위분류가 있을 경우에만
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' ";
			$sql.= "AND code_b IN ('".implode("','",$blistcode_b[$code_a])."') ";
			$sql.= "AND code_c!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;
			$iscode=true;
			pmysql_free_result($result);

			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' ";
			$sql.= "AND code_b IN ('".implode("','",$blistcode_b[$code_a])."') ";
			$sql.= "AND code_c='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

			if($cnt>0) {
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					if($i>0) $category_list.="<tr><td colspan=\"2\" style=\"border-bottom:#F0F0F0 1px solid;\"><img width=0></td></tr>\n";
					$category_list.="<tr>";
					$category_list.="	<td width=\"25%\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/brandproduct/".$_bdata->list_type."/plist_skin_iconaa.gif\" border=\"0\" align=\"absmiddle\" hspace=\"5\"><a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>".$row->code_name."</font></a></td>\n";
					$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
					if(!strstr($row->type,"X")) {
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_a='".$row->code_a."' AND code_b='".$row->code_b."' ";
						$sql.= "AND code_c IN ('".implode("','",$blistcode_c[$row->code_a.$row->code_b])."') ";
						$sql.= "AND code_d='000' AND group_code!='NO' ";
						$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
						$sql.= "ORDER BY sequence DESC ";
						$result2=pmysql_query($sql,get_db_conn());
						$j=0;
						while($row2=pmysql_fetch_object($result2)) {
							if($j>0) $category_list.=" | ";
							$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>".$row2->code_name."</font></a>";
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
					$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
					$i++;
				}
				$category_list.="	</td>\n";
				$category_list.="</tr>\n";
			}
			$category_list.="</table>\n";
			pmysql_free_result($result);
		} else {
			$iscode=true;
			$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			$category_list.="<tr>";
			$category_list.="	<td style=\"padding:10px;\" class=subcodename>";
			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_b='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND type IN ('L','T','LX','TX') ";
			$sql.= $brand_qryA;
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				if($i>0) $category_list.=" | ";
				$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
				if($code==$row->code_a.$row->code_b.$row->code_c.$row->code_d) {
					$category_list.="<B>".$row->code_name."</B>";
				} else {
					$category_list.="".$row->code_name."";
				}
				$category_list.="</FONT></a>";
				$i++;
			}
			$category_list.="	</td>\n";
			$category_list.="</tr>\n";
			$category_list.="</table>\n";
			pmysql_free_result($result);
		}
	} else if(strlen($likecode)==6) {	//2차분류 (2차에 속한 모든 3차,4차분류를 보여준다) - 4차가 있는지 검사
		//2차가 최종분류일 경우엔 1차에 속한 2차를 보여준다
		if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//하위분류가 있을 경우에만
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_d!='000' AND group_code!='NO' ";
			$sql.= "AND code_c IN ('".implode("','",$blistcode_c[$code_a.$code_b])."') ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;
			$iscode=true;
			pmysql_free_result($result);

			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' ";
			$sql.= "AND code_c IN ('".implode("','",$blistcode_c[$code_a.$code_b])."') ";
			$sql.= "AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			
			$result=pmysql_query($sql,get_db_conn());
			$category_list="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			if($cnt>0) {
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					if($i>0) $category_list.="<tr><td colspan=\"2\" style=\"border-bottom:#F0F0F0 1px solid;\"><img width=0></td></tr>\n";
					$category_list.="<tr>";
					$category_list.="	<td width=\"25%\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/brandproduct/".$_bdata->list_type."/plist_skin_iconaa.gif\" border=\"0\" align=\"absmiddle\" hspace=\"5\"><a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>".$row->code_name."</FONT></a></td>\n";
					$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
					if(!strstr($row->type,"X")) {
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_a='".$row->code_a."' AND code_b='".$row->code_b."' AND code_c='".$row->code_c."' ";
						$sql.= "AND code_d IN ('".implode("','",$blistcode_d[$row->code_a.$row->code_b.$row->code_c])."') ";
						$sql.= "AND group_code!='NO' ";
						$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
						$sql.= "ORDER BY sequence DESC ";
						$result2=pmysql_query($sql,get_db_conn());
						$j=0;
						while($row2=pmysql_fetch_object($result2)) {
							if($j>0) $category_list.=" | ";
							$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>".$row2->code_name."</FONT></a>";
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
					$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
					$i++;
				}
				$category_list.="	</td>\n";
				$category_list.="</tr>\n";
			}
			$category_list.="</table>\n";
			pmysql_free_result($result);
		} else {
			$iscode=true;
			$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			$category_list.="<tr>";
			$category_list.="	<td style=\"padding:10px;\" class=subcodename>";
			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' ";
			$sql.= "AND code_b IN ('".implode("','",$blistcode_b[$code_a])."') ";
			$sql.= "AND code_c='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				if($i>0) $category_list.=" | ";
				$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
				if($code==$row->code_a.$row->code_b.$row->code_c.$row->code_d) {
					$category_list.="<B>".$row->code_name."</B>";
				} else {
					$category_list.="".$row->code_name."";
				}
				$category_list.="</FONT></a>";
				$i++;
			}
			$category_list.="	</td>\n";
			$category_list.="</tr>\n";
			$category_list.="</table>\n";
			pmysql_free_result($result);
		}
	} else if(strlen($likecode)==9) {	//3차분류 (2차에 속한 모든 3차, 4차분류를 보여준다) - 4차가 있는지 검사
		//3차가 최종분류일 경우엔 2차에 속한 3차를 보여준다
		if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//하위분류가 있을 경우에만
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE code_a='".$code_a."' AND code_b='".$code_b."' ";
			$sql.= "AND code_c IN ('".implode("','",$blistcode_c[$code_a.$code_b])."') ";
			$sql.= "AND code_d!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;
			$iscode=true;
			pmysql_free_result($result);

			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' ";
			$sql.= "AND code_c IN ('".implode("','",$blistcode_c[$code_a.$code_b])."') ";
			$sql.= "AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$category_list="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			if($cnt>0) {
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					if($i>0) $category_list.="<tr><td colspan=\"2\" style=\"border-bottom:#F0F0F0 1px solid;\"><img width=0></td></tr>\n";
					$category_list.="<tr>";
					$category_list.="	<td width=\"25%\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/brandproduct/".$_bdata->list_type."/plist_skin_iconaa.gif\" border=\"0\" align=\"absmiddle\" hspace=\"5\"><a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>".$row->code_name."</FONT></a></td>\n";
					$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
					if(!strstr($row->type,"X")) {
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_a='".$row->code_a."' AND code_b='".$row->code_b."' AND code_c='".$row->code_c."' ";
						$sql.= "AND code_d IN ('".implode("','",$blistcode_d[$row->code_a.$row->code_b.$row->code_c])."') ";
						$sql.= "AND group_code!='NO' ";
						$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
						$sql.= "ORDER BY sequence DESC ";
						$result2=pmysql_query($sql,get_db_conn());
						$j=0;
						while($row2=pmysql_fetch_object($result2)) {
							if($j>0) $category_list.=" | ";
							$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>".$row2->code_name."</FONT></a>";
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
					$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
					$i++;
				}
				$category_list.="	</td>\n";
				$category_list.="</tr>\n";
			}
			$category_list.="</table>\n";
			pmysql_free_result($result);
		} else {
			$iscode=true;
			$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			$category_list.="<tr>";
			$category_list.="	<td style=\"padding:10px;\" class=subcodename>";
			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' ";
			$sql.= "AND code_c IN ('".implode("','",$blistcode_c[$code_a.$code_b])."') ";
			$sql.= "AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				if($i>0) $category_list.=" | ";
				$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
				if($code==$row->code_a.$row->code_b.$row->code_c.$row->code_d) {
					$category_list.="<B>".$row->code_name."</B>";
				} else {
					$category_list.="".$row->code_name."";
				}
				$category_list.="</FONT></a>";
				$i++;
			}
			$category_list.="	</td>\n";
			$category_list.="</tr>\n";
			$category_list.="</table>\n";
			pmysql_free_result($result);
		}
	} else if(strlen($likecode)==12) {	//4차분류 (3차에 속한 모든 4차분류만 보여준다)
		$iscode=true;
		$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$category_list.="<tr>";
		$category_list.="	<td style=\"padding:10px;\" class=subcodename>";
		$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
		$sql.= "WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_c='".$code_c."' ";
		$sql.= "AND code_d IN ('".implode("','",$blistcode_d[$code_a.$code_b.$code_c])."') ";
		$sql.= "AND group_code!='NO' ";
		$sql.= "AND type IN ('LM','TM','LMX','TMX') ";
		$sql.= "ORDER BY sequence DESC ";
		
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			if($i>0) $category_list.=" | ";
			$category_list.="<a href=\"".$Dir.FrontDir."productblist.php?".$brand_link."code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
			if($code==$row->code_a.$row->code_b.$row->code_c.$row->code_d) {
				$category_list.="<B>".$row->code_name."</B>";
			} else {
				$category_list.="".$row->code_name."";
			}
			$category_list.="</FONT></a>";
			$i++;
		}
		$category_list.="	</td>\n";
		$category_list.="</tr>\n";
		$category_list.="</table>\n";
		pmysql_free_result($result);
	}
?>
	<?if($iscode){?>
	<tr>
		<td style="padding:10px;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="25%"></col>
		<col></col>
		<tr>
			<td style="padding-left:10px;padding-bottom:5px;line-height:24px;" class=choicecodename><?=($_cdata->code_name?$_cdata->code_name:$_bdata->brandname)?></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#F7F7F7">
			<tr>
				<td bgcolor="#FFFFFF" style="border:#EEEEEE 1px solid;"><?=$category_list?></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<?}?>
<?}?>

<!-- 상품목록 시작 -->
<?
$sql = "SELECT COUNT(*) as t_count FROM tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.= $qry." ";
$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
if(strlen($not_qry)>0) {
	$sql.= $not_qry." ";
}
$paging = new Paging($sql,10,$listnum,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
?>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_stibg.gif" style="color:#ffffff;font-size:11px;"><B>브랜드 : <?=$_bdata->brandname?></B> 총 등록상품 : <b><?=$t_count?>건</b></td>
			<td><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_stimg.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<table cellpadding="2" cellspacing="0" width="100%">
			<tr>
<?
		//번호, 사진, 상품명, 제조사, 가격
		$tmp_sort=explode("_",$sort);
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
		}
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else $sql.= "ORDER BY a.productname ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			if ($i!=0 && $i%5==0) {
				echo "</tr><tr><td colspan=\"9\" height=\"10\"></td></tr>\n";
			}
			if ($i!=0 && $i%5!=0) {
				echo "<td width=\"10\" nowrap></td>";
			}
			echo "<td width=\"20%\" align=\"center\" valign=\"top\">\n";
			echo "<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" border=\"0\" id=\"G".$row->productcode."\" onmouseover=\"quickfun_show(this,'G".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'G".$row->productcode."','none')\">\n";
			echo "<TR height=\"100\">\n";
			echo "	<TD align=\"center\" valign=\"top\">";
			echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?".$brand_link."productcode=".$row->productcode.$add_query."&sort=".$sort."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
				echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) echo "height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) echo "width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) echo "width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) echo "height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			echo "	></A></td>";
			echo "</tr>\n";
			echo "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','G','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
			echo "<tr>";
			echo "	<TD align=\"center\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?".$brand_link."productcode=".$row->productcode.$add_query."&sort=".$sort."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
			echo "</tr>\n";
			if($row->consumerprice!=0) {
				echo "<tr>\n";
				echo "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				echo "</tr>\n";
			}
			echo "<tr>\n";
			echo "	<TD align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";
			if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
				echo $dicker;
			} else if(strlen($_data->proption_price)==0) {
				echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".number_format($row->sellprice)."원";
				if (strlen($row->option_price)!=0) echo "(기본가)";
			} else {
				echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
				if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
				else echo str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
			}
			if ($row->quantity=="0") echo soldout();
			echo "	</td>\n";
			echo "</tr>\n";
			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
			if($reserveconv>0) {
				echo "<tr>\n";
				echo "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</td>\n";
				echo "</tr>\n";
			}
			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$plist0_tag_0_count;$ii++) {
					$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							echo "<tr>\n";
							echo "	<td align=\"center\" style=\"word-break:break-all;\">\n";
							echo "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
						}
						else {
							echo "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
				if($jj!=0) {
					echo "	</td>\n";
					echo "</tr>\n";
				}
			}
			echo "</table>\n";
			echo "</td>";

			$i++;
		}
		if($i>0 && $i<5) {
			for($k=0; $k<(5-$i); $k++) {
				echo "<td width=\"10\" nowrap></td>\n<td></td>\n";
			}
		}
		pmysql_free_result($result);
?>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->list_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/brandproduct/<?=$_bdata->li
