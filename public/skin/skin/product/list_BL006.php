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
			<td align="right" bgcolor="#E2E6EA" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/locationbg_right.gif" style="padding-right:3px;background-repeat:no-repeat;background-position:right"><A HREF="javascript:ClipCopy('http://<?=$_ShopInfo->getShopurl2()?>?<?=$_SERVER['QUERY_STRING']?>')"><img src="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/btn_addr_copy.gif" border="0"></A></td>
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
			echo nl2br($_cdata->title_body);
		echo "	</td>\n";
		echo "</tr>\n";
	}
}
?>

<?if($_data->ETCTYPE["CODEYES"]!="N") {?>
<?
	$iscode=false;
	if(strlen($likecode)==3) {			//1���з� (1���� ���� ��� 2��,3���з��� �����ش�) - 3���� �ִ��� �˻�
		//1���� �����з��� ��쿣 �ƹ��͵� �������� �ʴ´�.
		if($_cdata->type!="LX" && $_cdata->type!="TX") {	//�����з��� ���� ��쿡��
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE code_a='".$code_a."' AND code_b!='000' AND code_c!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;
			$iscode=true;
			pmysql_free_result($result);

			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' ";
			$sql.= "AND code_b!='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

			if($cnt>0) {
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					//if($i>0) $category_list.="<tr><td height=1 colspan=2 bgcolor=FFFFFF></td></tr>\n";
					$category_list.="<tr>";
					$category_list.="	<td width=\"25%\" bgcolor=\"#F6F6F6\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_iconaa.gif\" border=\"0\"><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>".$row->code_name."</font></a></td>\n";
					$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
					if(!strstr($row->type,"X")) {
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_a='".$row->code_a."' ";
						$sql.= "AND code_b='".$row->code_b."' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
						$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
						$sql.= "ORDER BY sequence DESC ";
						$result2=pmysql_query($sql,get_db_conn());
						$j=0;
						while($row2=pmysql_fetch_object($result2)) {
							if($j>0) $category_list.=" | ";
							$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>".$row2->code_name."</font></a>";
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
					$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
					$i++;
				}
				$category_list.="	</td>\n";
				$category_list.="</tr>\n";
			}
			$category_list.="</table>\n";
			pmysql_free_result($result);
		}
	} else if(strlen($likecode)==6) {	//2���з� (2���� ���� ��� 3��,4���з��� �����ش�) - 4���� �ִ��� �˻�
		//2���� �����з��� ��쿣 1���� ���� 2���� �����ش�
		if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//�����з��� ���� ��쿡��
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_c!='000' AND code_d!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;
			$iscode=true;
			pmysql_free_result($result);

			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' ";
			$sql.= "AND code_b='".$code_b."' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$category_list="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			if($cnt>0) {
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$category_list.="<tr>";
					$category_list.="	<td width=\"25%\" bgcolor=\"#F6F6F6\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_iconaa.gif\" border=\"0\"><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>".$row->code_name."</FONT></a></td>\n";
					$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
					if(!strstr($row->type,"X")) {
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_a='".$row->code_a."' ";
						$sql.= "AND code_b='".$row->code_b."' AND code_c='".$row->code_c."' AND code_d!='000' AND group_code!='NO' ";
						$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
						$sql.= "ORDER BY sequence DESC ";
						$result2=pmysql_query($sql,get_db_conn());
						$j=0;
						while($row2=pmysql_fetch_object($result2)) {
							if($j>0) $category_list.=" | ";
							$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>".$row2->code_name."</FONT></a>";
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
					$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
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
			$sql.= "AND code_b!='000' AND code_c='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				if($i>0) $category_list.=" | ";
				$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
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
	} else if(strlen($likecode)==9) {	//3���з� (2���� ���� ��� 3��, 4���з��� �����ش�) - 4���� �ִ��� �˻�
		//3���� �����з��� ��쿣 2���� ���� 3���� �����ش�
		if($_cdata->type!="LMX" && $_cdata->type!="TMX") {	//�����з��� ���� ��쿡��
			$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE code_a='".$code_a."' AND code_b='".$code_b."' AND code_c!='000' AND code_d!='000' AND group_code!='NO' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$cnt=$row->cnt;
			$iscode=true;
			pmysql_free_result($result);

			$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
			$sql.= "WHERE code_a='".$code_a."' ";
			$sql.= "AND code_b='".$code_b."' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$category_list="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			if($cnt>0) {
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$category_list.="<tr>";
					$category_list.="	<td width=\"25%\" bgcolor=\"#F6F6F6\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_iconaa.gif\" border=\"0\"><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=upcodename>".$row->code_name."</FONT></a></td>\n";
					$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
					if(!strstr($row->type,"X")) {
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_a='".$row->code_a."' ";
						$sql.= "AND code_b='".$row->code_b."' AND code_c='".$row->code_c."' AND code_d!='000' AND group_code!='NO' ";
						$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
						$sql.= "ORDER BY sequence DESC ";
						$result2=pmysql_query($sql,get_db_conn());
						$j=0;
						while($row2=pmysql_fetch_object($result2)) {
							if($j>0) $category_list.=" | ";
							$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->code_a.$row2->code_b.$row2->code_c.$row2->code_d."\"><FONT class=subcodename>".$row2->code_name."</FONT></a>";
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
					$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
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
			$sql.= "AND code_b='".$code_b."' AND code_c!='000' AND code_d='000' AND group_code!='NO' ";
			$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
			$sql.= "ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				if($i>0) $category_list.=" | ";
				$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
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
	} else if(strlen($likecode)==12) {	//4���з� (3���� ���� ��� 4���з��� �����ش�)
		$iscode=true;
		$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$category_list.="<tr>";
		$category_list.="	<td style=\"padding:10px;\" class=subcodename>";
		$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
		$sql.= "WHERE code_a='".$code_a."' ";
		$sql.= "AND code_b='".$code_b."' AND code_c='".$code_c."' AND code_d!='000' AND group_code!='NO' ";
		$sql.= "AND (type='LM' OR type='TM' OR type='LMX' OR type='TMX') ";
		$sql.= "ORDER BY sequence DESC ";
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			if($i>0) $category_list.=" | ";
			$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->code_a.$row->code_b.$row->code_c.$row->code_d."\"><FONT class=subcodename>";
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
			<td style="padding-left:10px;padding-bottom:5px;line-height:24px;" class=choicecodename><?=$_cdata->code_name?></td>
		</tr>
		<tr>
			<td height="2" bgcolor="#0060BF"></td>
		</tr>
		<tr>
			<td height="3" bgcolor="#F2F2F2"></td>
		</tr>
		<tr>
			<td colspan="2"><?=$category_list?></td>
		</tr>
		<tr>
			<td height="1" bgcolor="#0060BF"></td>
		</tr>
		<tr>
			<td height="1" bgcolor="#F2F2F2"></td>		
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<?}?>
<?}?>

<!-- �ű�/�α�/��õ ���� -->
<?
$special_show_cnt=0;
$special_show_list ="<tr>\n";
$special_show_list.="	<td>\n";
$special_show_list.="	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
$special_show_list.="	<tr>\n";
$special_show_list.="		<td><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_space.gif\" border=\"0\"></td>\n";
$special_show_list.="		<td width=\"100%\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_table01.gif\"></td>\n";
$special_show_list.="		<td><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_space.gif\" border=\"0\"></td>\n";
$special_show_list.="	</tr>\n";
$special_show_list.="	<tr>\n";
$special_show_list.="		<td background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_table01.gif\" border=\"0\"></td>\n";
$special_show_list.="		<td style=\"padding:10px;\">\n";
$special_show_list.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";

$arrspecialcnt=explode(",",$_cdata->special_cnt);
for ($i=0;$i<count($arrspecialcnt);$i++) {
	if (substr($arrspecialcnt[$i],0,2)=="1:") {
		$tmpsp1=substr($arrspecialcnt[$i],2);
	} else if (substr($arrspecialcnt[$i],0,2)=="2:") {
		$tmpsp2=substr($arrspecialcnt[$i],2);
	} else if (substr($arrspecialcnt[$i],0,2)=="3:") {
		$tmpsp3=substr($arrspecialcnt[$i],2);
	}
}
if(strlen($tmpsp1)>0) {
	$special_1=explode("X",$tmpsp1);
	$special_1_cols=(int)$special_1[0];
	$special_1_rows=(int)$special_1[1];
	$special_1_type=$special_1[2];
}
if(strlen($tmpsp2)>0) {
	$special_2=explode("X",$tmpsp2);
	$special_2_cols=(int)$special_2[0];
	$special_2_rows=(int)$special_2[1];
	$special_2_type=$special_2[2];
}
if(strlen($tmpsp3)>0) {
	$special_3=explode("X",$tmpsp3);
	$special_3_cols=(int)$special_3[0];
	$special_3_rows=(int)$special_3[1];
	$special_3_type=$special_3[2];
}

$plist0_tag_0_count = 2; //��ü��ǰ �±� ��� ����

$plist1_tag_1_count = 2; //�űԻ�ǰ �±� ��� ����(�̹���A��)
$plist2_tag_1_count = 5; //�űԻ�ǰ �±� ��� ����(����Ʈ��)
$plist3_tag_1_count = 2; //�űԻ�ǰ �±� ��� ����(�̹���B��)

$plist1_tag_2_count = 2; //�α��ǰ �±� ��� ����(�̹���A��)
$plist2_tag_2_count = 5; //�α��ǰ �±� ��� ����(����Ʈ��)
$plist3_tag_2_count = 2; //�α��ǰ �±� ��� ����(�̹���B��)

$plist1_tag_3_count = 2; //��õ��ǰ �±� ��� ����(�̹���A��)
$plist2_tag_3_count = 5; //��õ��ǰ �±� ��� ����(����Ʈ��)
$plist3_tag_3_count = 2; //��õ��ǰ �±� ��� ����(�̹���B��)

//�ű�
$special_1_num=$special_1_cols*$special_1_rows;
if(strstr($_cdata->special,"1")) {
	$sql = "SELECT special_list FROM tblspecialcode ";
	$sql.= "WHERE code='".$code."' AND special='1' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	$sp_list="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);

	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.reserve, a.reservetype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$special_1_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		//$special_1_type => I:�̹���A��, D:�̹���B��, L:����Ʈ��
		if($special_1_type == "I") {
			$sp_list.= "<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
			$table_width=ceil(100/$special_1_cols);
			for($j=1;$j<=$special_1_cols;$j++) {
				if($j>1)
					$sp_list.="<col width=10></col>\n";
				$sp_list.="<col width=".$table_width."%></col>\n";
			}
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i!=0 && $i%$special_1_cols!=0) {
					$sp_list.= "<td></td>";
				}
				$sp_list.= "<td align=\"center\" valign=\"top\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
				$sp_list.= "<TR height=\"100\">\n";
				$sp_list.= "	<TD align=\"center\">";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$sp_list.= "<tr>";
				$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$sp_list.= "</tr>\n";
				if($row->consumerprice!=0) {
					$sp_list.= "<tr>\n";
					$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>��</td>\n";
					$sp_list.= "</tr>\n";
				}
				$sp_list.= "<tr>\n";
				$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) {
					$sp_list.= "<tr>\n";
					$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."��</td>\n";
					$sp_list.= "</tr>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist1_tag_1_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<tr>\n";
								$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\">\n";
								$sp_list.= "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
					if($jj!=0) {
						$sp_list.= "	</td>\n";
						$sp_list.= "</tr>\n";
					}
				}
				$sp_list.= "</table>\n";
				$sp_list.= "</td>";
				$i++;

				if ($i==$special_1_num) break;
				if ($i%$special_1_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"".($special_1_cols*2-1)."\" height=\"5\"></td><tr>\n";
				}
			}
			if($i>0 && $i<$special_1_cols) {
				for($k=0; $k<($special_1_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td></td>\n";
				}
			}
		} else if($special_1_type == "L") {
			$colspan="6";
			$sp_list.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<col width=\"15%\"></col>\n";
			$sp_list.= "<col width=\"0\"></col>\n";
			$sp_list.= "<col width=\"50%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"11%\"></col>\n";
			$sp_list.= "<tr height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\">\n";
			$sp_list.= "	<td colspan=\"2\"><b><font color=\"#000000\">��ǰ����</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">��ǰ��</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">���߰���</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">�ǸŰ���</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">������</font></b></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
			$sp_list.= "</tr>\n";
			while($row=pmysql_fetch_object($result)) {
				$sp_list.= "<tr align=\"center\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
				$sp_list.= "	<td style=\"padding-top:1px;padding-bottom:1px;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>\n";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<td style=\"padding-left:5px;padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist2_tag_1_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>��</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))."��</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr>\n";
				$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
				$sp_list.= "</tr>\n";
				$i++;
			}
		} else if($special_1_type == "D") {
			$sp_list.= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i!=0 && $i%$special_1_cols!=0) {
					$sp_list.= "<td align=\"center\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_lineb.gif\" border=\"0\"></td>\n";
				}
				$sp_list.= "<td width=\"".(100/$special_1_cols)."%\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
				$sp_list.= "<col width=\"100\"></col>\n";
				$sp_list.= "<col width=\"0\"></col>\n";
				$sp_list.= "<col width=\"100%\"></col>\n";
				$sp_list.= "<TR>\n";
				$sp_list.= "	<TD align=\"center\" style=\"padding-top:1px;padding-bottom:1px;\" nowrap>";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<TD style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>\n";
				if($row->consumerprice!=0) {
					$sp_list.= "<br><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"prconsumerprice\"><strike>".number_format($row->consumerprice)."</strike>��</font>\n";
				}
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= "<br><font class=\"prprice\">".$dicker."</font>";
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
					$sp_list.= "</font>";
				} else {
					$sp_list.="<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
					$sp_list.= "</font>";
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) {
					$sp_list.= "<br><font class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."��</font>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist3_tag_1_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "</table>\n";
				$sp_list.= "</td>\n";
				$i++;
				if ($i%$special_1_cols==0) {
					$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_1_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td></tr><tr>\n";
				}
				if ($i==$special_1_num) break;
			}
			if($i>0 && $i<$special_1_cols) {
				for($k=0; $k<($special_1_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td width=\"".(100/$special_1_cols)."%\"></td>\n";
				}
			}
			if ($i!=0 && $i%$special_1_cols) {
				$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_1_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td>\n";
			}
		}
		pmysql_free_result($result);
		$sp_list.= "</tr>\n";
		$sp_list.= "</table>\n";

		if($i>0) {
			if($special_show_cnt) {
				$special_show_list.="</tr><td height=\"20\"></td></tr>\n";
			}
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_titlebg.gif\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_newtitle.gif\" border=\"0\"></td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td>\n";
			$special_show_list.="		".$sp_list."\n";
			$special_show_list.="		</td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_cnt++;
		}
	}
}

//�α�
$special_2_num=$special_2_cols*$special_2_rows;
if(strstr($_cdata->special,"2")) {
	$sql = "SELECT special_list FROM tblspecialcode ";
	$sql.= "WHERE code='".$code."' AND special='2' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	$sp_list="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);

	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.reserve, a.reservetype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$special_2_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		//$special_2_type => I:�̹���A��, D:�̹���B��, L:����Ʈ��
		if($special_2_type == "I") {
			$sp_list.= "<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
			$table_width=ceil(100/$special_2_cols);
			for($j=1;$j<=$special_2_cols;$j++) {
				if($j>1)
					$sp_list.="<col width=10></col>\n";
				$sp_list.="<col width=".$table_width."%></col>\n";
			}
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i!=0 && $i%$special_2_cols!=0) {
					$sp_list.= "<td></td>";
				}
				$sp_list.= "<td align=\"center\" valign=\"top\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
				$sp_list.= "<TR height=\"100\">\n";
				$sp_list.= "	<TD align=\"center\">";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$sp_list.= "<tr>";
				$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$sp_list.= "</tr>\n";
				if($row->consumerprice!=0) {
					$sp_list.= "<tr>\n";
					$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>��</td>\n";
					$sp_list.= "</tr>\n";
				}
				$sp_list.= "<tr>\n";
				$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) {
					$sp_list.= "<tr>\n";
					$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."��</td>\n";
					$sp_list.= "</tr>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist1_tag_2_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<tr>\n";
								$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\">\n";
								$sp_list.= "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
					if($jj!=0) {
						$sp_list.= "	</td>\n";
						$sp_list.= "</tr>\n";
					}
				}
				$sp_list.= "</table>\n";
				$sp_list.= "</td>";
				$i++;

				if ($i==$special_2_num) break;
				if ($i%$special_2_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"".($special_2_cols*2-1)."\" height=\"5\"></td><tr>\n";
				}
			}
			if($i>0 && $i<$special_2_cols) {
				for($k=0; $k<($special_2_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td></td>\n";
				}
			}
		} else if($special_2_type == "L") {
			$colspan="6";
			$sp_list.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<col width=\"15%\"></col>\n";
			$sp_list.= "<col width=\"0\"></col>\n";
			$sp_list.= "<col width=\"50%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"11%\"></col>\n";
			$sp_list.= "<tr height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\">\n";
			$sp_list.= "	<td colspan=\"2\"><b><font color=\"#000000\">��ǰ����</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">��ǰ��</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">���߰���</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">�ǸŰ���</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">������</font></b></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
			$sp_list.= "</tr>\n";
			while($row=pmysql_fetch_object($result)) {
				$sp_list.= "<tr align=\"center\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
				$sp_list.= "	<td style=\"padding-top:1px;padding-bottom:1px;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>\n";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<td style=\"padding-left:5px;padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist2_tag_2_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>��</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))."��</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr>\n";
				$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
				$sp_list.= "</tr>\n";
				$i++;
			}
		} else if($special_2_type == "D") {
			$sp_list.= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i!=0 && $i%$special_2_cols!=0) {
					$sp_list.= "<td align=\"center\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_lineb.gif\" border=\"0\"></td>\n";
				}
				$sp_list.= "<td width=\"".(100/$special_2_cols)."%\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
				$sp_list.= "<col width=\"100\"></col>\n";
				$sp_list.= "<col width=\"0\"></col>\n";
				$sp_list.= "<col width=\"100%\"></col>\n";
				$sp_list.= "<TR>\n";
				$sp_list.= "	<TD align=\"center\" style=\"padding-top:1px;padding-bottom:1px;\" nowrap>";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<TD style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>\n";
				if($row->consumerprice!=0) {
					$sp_list.= "<br><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"prconsumerprice\"><strike>".number_format($row->consumerprice)."</strike>��</font>\n";
				}
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= "<br><font class=\"prprice\">".$dicker."</font>";
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
					$sp_list.= "</font>";
				} else {
					$sp_list.="<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
					$sp_list.= "</font>";
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) {
					$sp_list.= "<br><font class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."��</font>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist3_tag_2_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "</table>\n";
				$sp_list.= "</td>\n";
				$i++;
				if ($i%$special_2_cols==0) {
					$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_2_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td></tr><tr>\n";
				}
				if ($i==$special_2_num) break;
			}
			if($i>0 && $i<$special_2_cols) {
				for($k=0; $k<($special_2_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td width=\"".(100/$special_2_cols)."%\"></td>\n";
				}
			}
			if ($i!=0 && $i%$special_2_cols) {
				$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_2_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td>\n";
			}
		}
		pmysql_free_result($result);
		$sp_list.= "</tr>\n";
		$sp_list.= "</table>\n";

		if($i>0) {
			if($special_show_cnt) {
				$special_show_list.="</tr><td height=\"20\"></td></tr>\n";
			}
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_titlebg1.gif\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_besttitle.gif\" border=\"0\"></td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td>\n";
			$special_show_list.="		".$sp_list."\n";
			$special_show_list.="		</td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_cnt++;
		}
	}
}

//��õ
$special_3_num=$special_3_cols*$special_3_rows;
if(strstr($_cdata->special,"3")) {
	$sql = "SELECT special_list FROM tblspecialcode ";
	$sql.= "WHERE code='".$code."' AND special='3' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	$sp_list="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);

	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.reserve, a.reservetype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$special_3_num;
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		//$special_3_type => I:�̹���A��, D:�̹���B��, L:����Ʈ��
		if($special_3_type == "I") {
			$sp_list.= "<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
			$table_width=ceil(100/$special_3_cols);
			for($j=1;$j<=$special_3_cols;$j++) {
				if($j>1)
					$sp_list.="<col width=10></col>\n";
				$sp_list.="<col width=".$table_width."%></col>\n";
			}
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i!=0 && $i%$special_3_cols!=0) {
					$sp_list.= "<td></td>";
				}
				$sp_list.= "<td align=\"center\" valign=\"top\">\n";
				$sp_list.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
				$sp_list.= "<TR height=\"100\">\n";
				$sp_list.= "	<TD align=\"center\">";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$sp_list.= "<tr>";
				$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$sp_list.= "</tr>\n";
				if($row->consumerprice!=0) {
					$sp_list.= "<tr>\n";
					$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>��</td>\n";
					$sp_list.= "</tr>\n";
				}
				$sp_list.= "<tr>\n";
				$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) {
					$sp_list.= "<tr>\n";
					$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."��</td>\n";
					$sp_list.= "</tr>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist1_tag_3_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<tr>\n";
								$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\">\n";
								$sp_list.= "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
					if($jj!=0) {
						$sp_list.= "	</td>\n";
						$sp_list.= "</tr>\n";
					}
				}
				$sp_list.= "</table>\n";
				$sp_list.= "</td>";
				$i++;

				if ($i==$special_3_num) break;
				if ($i%$special_3_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"".($special_3_cols*2-1)."\" height=\"5\"></td><tr>\n";
				}
			}
			if($i>0 && $i<$special_3_cols) {
				for($k=0; $k<($special_3_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td></td>\n";
				}
			}
		} else if($special_3_type == "L") {
			$colspan="6";
			$sp_list.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<col width=\"15%\"></col>\n";
			$sp_list.= "<col width=\"0\"></col>\n";
			$sp_list.= "<col width=\"50%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"11%\"></col>\n";
			$sp_list.= "<tr height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\">\n";
			$sp_list.= "	<td colspan=\"2\"><b><font color=\"#000000\">��ǰ����</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">��ǰ��</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">���߰���</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">�ǸŰ���</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">������</font></b></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
			$sp_list.= "</tr>\n";
			while($row=pmysql_fetch_object($result)) {
				$sp_list.= "<tr align=\"center\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
				$sp_list.= "	<td style=\"padding-top:1px;padding-bottom:1px;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>\n";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<td style=\"padding-left:5px;padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist2_tag_3_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>��</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))."��</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr>\n";
				$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
				$sp_list.= "</tr>\n";
				$i++;
			}
		} else if($special_3_type == "D") {
			$sp_list.= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=pmysql_fetch_object($result)) {
				if ($i!=0 && $i%$special_3_cols!=0) {
					$sp_list.= "<td align=\"center\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_lineb.gif\" border=\"0\"></td>\n";
				}
				$sp_list.= "<td width=\"".(100/$special_3_cols)."%\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
				$sp_list.= "<col width=\"100\"></col>\n";
				$sp_list.= "<col width=\"0\"></col>\n";
				$sp_list.= "<col width=\"100%\"></col>\n";
				$sp_list.= "<TR>\n";
				$sp_list.= "	<TD align=\"center\" style=\"padding-top:1px;padding-bottom:1px;\" nowrap>";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<TD style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='��ǰ����ȸ';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>\n";
				if($row->consumerprice!=0) {
					$sp_list.= "<br><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"prconsumerprice\"><strike>".number_format($row->consumerprice)."</strike>��</font>\n";
				}
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."��",1)) {
					$sp_list.= "<br><font class=\"prprice\">".$dicker."</font>";
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."��";
					if (strlen($row->option_price)!=0) $sp_list.= "(�⺻��)";
					$sp_list.= "</font>";
				} else {
					$sp_list.="<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= number_format($row->sellprice)."��";
					else $sp_list.= str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
					$sp_list.= "</font>";
				}
				if ($row->quantity=="0") $sp_list.= soldout();
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($reserveconv>0) {
					$sp_list.= "<br><font class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."��</font>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist3_tag_3_count;$ii++) {
						$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "</table>\n";
				$sp_list.= "</td>\n";
				$i++;
				if ($i%$special_3_cols==0) {
					$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_3_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td></tr><tr>\n";
				}
				if ($i==$special_3_num) break;
			}
			if($i>0 && $i<$special_3_cols) {
				for($k=0; $k<($special_3_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td width=\"".(100/$special_3_cols)."%\"></td>\n";
				}
			}
			if ($i!=0 && $i%$special_3_cols) {
				$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_3_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td>\n";
			}
		}
		pmysql_free_result($result);
		$sp_list.= "</tr>\n";
		$sp_list.= "</table>\n";

		if($i>0) {
			if($special_show_cnt) {
				$special_show_list.="</tr><td height=\"20\"></td></tr>\n";
			}
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_titlebg2.gif\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_hotitem.gif\" border=\"0\"></td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td>\n";
			$special_show_list.="		".$sp_list."\n";
			$special_show_list.="		</td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_cnt++;
		}
	}
}

$special_show_list.="		</table>\n";
$special_show_list.="		</td>\n";
$special_show_list.="		<td background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_table01.gif\" border=\"0\"></td>\n";
$special_show_list.="	</tr>\n";
$special_show_list.="	<tr>\n";
$special_show_list.="		<td><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_space.gif\" border=\"0\"></td>\n";
$special_show_list.="		<td width=\"100%\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_table01.gif\"></td>\n";
$special_show_list.="		<td><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_space.gif\" border=\"0\"></td>\n";
$special_show_list.="	</tr>\n";
$special_show_list.="	</table>\n";
$special_show_list.="	</td>\n";
$special_show_list.="</tr>\n";
$special_show_list.="<tr>\n";
$special_show_list.="	<td height=\"10\"></td>\n";
$special_show_list.="</tr>\n";

if($special_show_cnt)
	echo $special_show_list;
?>
<!-- �ű�/�α�/��õ �� -->
	<!-- ��ǰ��� ���� -->
<?if($_cdata->islist=="Y"){?>
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
			<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_stibg.gif" style="color:#ffffff;font-size:11px;"><B><?=$_cdata->code_name?></B> �� ��ϻ�ǰ : <b><?=$t_count?>��</b></td>
			<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_stimg.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td valign="top">
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<col width="12%"></col>
		<col width="0"></col>
		<col width="42%"></col>
		<col width="16%"></col>
		<col width="11%"></col>
		<col width="11%"></col>
		<col width="8%"></col>
		<tr>
			<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif" colspan="7"></td>
		</tr>
		<tr height="30" bgcolor="#F8F8F8" align="center">
			<td colspan="2"><font color="#000000">��ǰ����</font></td>
			<td><font color="#000000">��ǰ��</font></td>
			<td><font color="#000000">���ݺ���ǥ</font></td>
			<td><font color="#000000">���߰���</font></td>
			<td><font color="#000000">�ǸŰ���</font></td>
			<td><font color="#000000">��������</font></td>
		</tr>
<?
		//��ȣ, ����, ��ǰ��, ������, ����
		$tmp_sort=explode("_",$sort);
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
		}
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		if($_cdata->sort=="date2") $sql.="CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
		$sql.= "a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
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
		else {
			if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
				if(strstr($_cdata->type,"T") && strlen($t_prcode)>0) {
					$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
				} else {
					$sql.= "ORDER BY date DESC ";
				}
			} else if($_cdata->sort=="productname") {
				$sql.= "ORDER BY a.productname ";
			} else if($_cdata->sort=="production") {
				$sql.= "ORDER BY a.production ";
			} else if($_cdata->sort=="price") {
				$sql.= "ORDER BY a.sellprice ";
			}
		}
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			//if($i>0 && $i%3==0) echo "</tr></table></td></tr><tr><td><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_table01.gif\" width=\"100%\" height=\"1\" border=\"0\" vspace=\"3\"></td></tr><tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"3\" width=\"100%\"><tr>\n";
?>
		<tr>
			<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif" colspan="7"></td>
		</tr>
		<tr align="center" id="G<?=$row->productcode?>" onmouseover="quickfun_show(this,'G<?=$row->productcode?>','','row')" onmouseout="quickfun_show(this,'G<?=$row->productcode?>','none')">
			<td style="padding-top:1px;padding-bottom:1px;"><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode.$add_query?>&sort=<?=$sort?>" onmouseover="window.status='��ǰ����ȸ';return true;" onmouseout="window.status='';return true;">
<?
					if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
						echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
						$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
						if(($width[0]>80 || $width[1]>80) && $width[0]>$width[1]) {
							echo " width=\"80\"";
						} else if($width[0]>80 || $width[1]>80) {
							echo " height=\"80\"";
						}
					} else {
						echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" width=\"80\" height=\"80\"";
					}
					
?>
			></A></td>
			<td style="position:relative;"><?=($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','G','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")?></td>
			<td align="left" style="padding-left:5px;padding-right:5px;word-break:break-all;"><a href='<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode.$add_query?>&sort=<?=$sort?>' onmouseover="window.status='��ǰ����ȸ';return true;" onmouseout="window.status='';return true;"><font color="#000000" style="font-size:11px;letter-spacing:-0.5pt;"><b><?=viewselfcode($row->productname,$row->selfcode)?></b></font></a>
<?
			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$plist0_tag_0_count;$ii++) {
					$taglist[$ii]=preg_replace("/<|>/","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							echo "<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\">".$taglist[$ii]."</a>";
						}
						else {
							echo ",&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\">".$taglist[$ii]."</a>";
						}
						$jj++;
					}
				}
			}
?>
			</td>
			<TD>
				<table cellpadding="0" cellspacing="0" width="102">
				<tr>
					<td height="5"></td>
				</tr>
				<tr>
					<td width="102" height="52" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_listbox.gif">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr align="center">
						<td width="43" height="40" valign="top" style="font-size:11px;"><?=number_format($row->consumerprice)?></td>
						<td width="43" valign="middle" style="font-size:11px;"><?=number_format($row->sellprice)?></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td width="102">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr align="right">
						<td width="43" style="font-size:11px;">���۰�<br></td>
						<td width="43" style="font-size:11px;">������</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
			</td>
			<TD style="word-break:break-all;" class="prprice"><img src="<?=$Dir?>images/common/won_icon2.gif" border="0" style="margin-right:2px;"><s><?=number_format($row->consumerprice)?>��</s></td>
			<TD style="word-break:break-all;" class="prprice"><img src="<?=$Dir?>images/common/won_icon.gif" border="0" style="margin-right:2px;"><font color="#F02800" style="font-size:11px;letter-spacing:-0.5pt;"><b><?=number_format($row->sellprice)?>��</b></font></td>
			<TD style="word-break:break-all;" class="prprice">
			<?
				if(strlen($row->quantity)==0 || $row->quantity==NULL) {
					echo "������";
				} else {
					echo $row->quantity."��";
				}
			?>
			</td>
<?
			$i++;
		}
		pmysql_free_result($result);
		if($i == 0) {
			echo "<tr>\n";
			echo "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"7\"></td>";
			echo "</tr>\n";
		}
?>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="font-size:11px;" align="center"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<?}?>
	</table>
	</td>
</tr>
</table>
