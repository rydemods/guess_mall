<?php 
$colspan=3;
if($reviewdate!="N") $colspan=4;
$qry = "WHERE productcode='{$productcode}' ";
if($_data->review_type=="A") $qry.= "AND display='Y' ";
$sql = "SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
$sql.= $qry;
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$t_count = (int)$row->t_count;
$totmarks = (int)$row->totmarks;
$marks=@ceil($totmarks/$t_count);
pmysql_free_result($result);
$paging = new Paging($t_count,10,15,'GoPage',true);
$gotopage = $paging->gotopage;
?>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="bottom" style="font-size:11px;letter-spacing:-0.5pt;">* 총 <font color="#F02800" style="font-size:11px;letter-spacing:-0.5pt;"><b><?=$t_count?>개</b></font>의  사용후기가 있습니다. &nbsp;&nbsp;평균평점 : 
		<?php 
						for($i=0;$i<$marks;$i++) echo "<FONT color=#000000><B>★</B></FONT>";
						for($i=$marks;$i<5;$i++) echo "<FONT color=#DEDEDE><B>★</B></FONT>";
		?>
		</td>
		<td align="right">
		<?php if ($_data->ETCTYPE["REVIEW"]=="Y") {?>
		<A HREF="<?=$Dir.FrontDir?>reviewall.php"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_btn01.gif" border="0" hspace="2"></a>
		<?php }?>
		<?php 
		if((strlen($_ShopInfo->getMemid())==0) && $_data->review_memtype=="Y") {
			echo "<A HREF=\"javascript:check_login()\"><IMG SRC=\"{$Dir}images/common/product/{$_cdata->detail_type}/pdetail_skin_write_btn02.gif\" border=\"0\"></A>";
		} else {
			echo "<A HREF=\"javascript:review_write()\"><IMG SRC=\"{$Dir}images/common/product/{$_cdata->detail_type}/pdetail_skin_write_btn02.gif\" border=\"0\"></A>";
		}
		?>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
	<span id="reviewwrite" style="display:none;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=reviewform method=post action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name=mode>
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=productcode value="<?=$productcode?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<?=($brandcode>0?"<input type=hidden name=brandcode value=\"{$brandcode}\">\n":"")?>
	<col width="6"></col>
	<col></col>
	<col width="6"></col>
	<tr>
		<td height="6" colspan="3" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_t1.gif"></td>
	</tr>
	<tr>
		<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_t2.gif"></td>
		<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_tbg.gif" style="padding:7pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="40"></col>
		<col width="80"></col>
		<col width="40"></col>
		<col width=></col>
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_name.gif"></td>
			<td><input type=text name=rname size="10" class="input"></td>
			<td align="right"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_point.gif" border="0"></td>
			<td style="font-size:11px;letter-spacing:-0.5pt;line-height:15px;"><input type=radio name=rmarks value="1" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px"><FONT color=#000000><B>★</B></FONT><input type=radio name=rmarks value=2 style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px"><FONT color=#000000><B>★★</B></FONT><input type=radio name=rmarks value=3 style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px"><FONT color=#000000><B>★★★</B></FONT><input type=radio name=rmarks value=4 style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px"><FONT color=#000000><B>★★★★</B></FONT><input type=radio name=rmarks value=5 checked style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px"><FONT color=#000000><B>★★★★★</B></FONT></td>
		</tr>
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_con.gif" border="0"></td>
			<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%"><TEXTAREA style="WIDTH: 99%; HEIGHT: 40px" name=rcontent style="padding:3pt;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;"></TEXTAREA></td>
				<td align="right"><a href="javascript:CheckReview();"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_btn03.gif" border="0"></a></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
		<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_t5.gif"></td>
	</tr>
	<tr>
		<td height="6" colspan="3" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_t3.gif"></td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	</form>
	</table>
	</span>
	</td>
</tr>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<col width="14%"></col>
	<col width=></col>
	<?php if($reviewdate!="N"){?>
	<col width="13%"></col>
	<?php }?>
	<col width="18%"></col>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_line2.gif" colspan="<?=$colspan?>"></td>
	</tr>
	<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
		<td><b>작성자</b></td>
		<td><b>사용후기</b></td>
		<?php if($reviewdate!="N"){?>
		<td><b>작성일</b></td>
		<?php }?>
		<td><b>평점</b></td>
	</tr>
	<tr>
		<td height="1" colspan="<?=$colspan?>" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_line4.gif"></td>
	</tr>
<?php 
	$sql = "SELECT * FROM tblproductreview {$qry} ";
	$sql.= "ORDER BY num DESC ";
	$sql = $paging->getSql($sql);
	$result=pmysql_query($sql,get_db_conn());
	$j=0;
	while($row=pmysql_fetch_object($result)) {
		$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$j);

		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$content=explode("=",$row->content);
		echo "<tr align=\"center\" style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\">\n";
		echo "	<td>{$row->name}</td>\n";
		echo "	<td align=\"left\">";
		if($reviewlist=="Y") {
			echo "<A HREF=\"javascript:view_review({$j})\">".titleCut(60,$content[0])."</A>";
		} else {
			echo "<A HREF=\"javascript:review_open('{$row->productcode}',{$row->num})\">".titleCut(60,$content[0])."</A>";
		}
		if(ord($content[1])) echo "<img src=\"{$Dir}images/common/review/review_replyicn.gif\" border=0 align=absmiddle>";
		echo "	</td>\n";
		if($reviewdate!="N") {
			echo "	<td>{$date}</td>\n";
		}
		echo "	<td style=\"font-size:11px;letter-spacing:-0.5pt;line-height:15px;\">";
		for($i=0;$i<$row->marks;$i++) {
			echo "<FONT color=#000000><B>★</B></FONT>";
		}
		for($i=$row->marks;$i<5;$i++) {
			echo "<FONT color=#DEDEDE><B>★</B></FONT>";
		}
		echo "	</td>\n";
		echo "</tr>\n";
		if($reviewlist=="Y") {
			echo "<tr id=reviewspan style=\"display:none; xcursor:hand\">\n";
			echo "	<td colspan={$colspan}>\n";
			echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#f0f0f0 style=\"table-layout:fixed\">\n";
			echo "	<tr>\n";
			echo "		<td style=\"border:#f0f0f0 solid 1px\">\n";
			echo "		<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#F1F1F1 style=\"table-layout:fixed\">\n";
			echo "		<tr>\n";
			echo "			<td align=center style=\"padding:8\">\n";
			echo "			<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			echo "			<tr>\n";
			echo "				<td bgcolor=#FFFFFF style=\"border:#f0f0f0 solid 1px; padding:8\">\n";
			echo "				<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			echo "				<tr><td>".nl2br($content[0])."</td></tr>\n";
			if(ord($content[1])) {
				echo "	<tr><td style=\"padding:5 5 5 10px\"><img src=\"{$Dir}images/common/review/review_replyicn2.gif\" align=absmiddle border=0> ".nl2br($content[1])."</td></tr>\n";
			}
			echo "				<tr>\n";
			echo "					<td align=right><a href=\"javascript:view_review({$j})\"><img src=\"{$Dir}images/common/review/review_close.gif\" border=0></a></td>\n";
			echo "				</tr>\n";
			echo "				</table>\n";
			echo "				</td>\n";
			echo "			</tr>\n";
			echo "			</table>\n";
			echo "			</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
		}
		echo "<tr><td height=\"1\" colspan=\"{$colspan}\" background=\"{$Dir}images/common/product/{$_cdata->detail_type}/pdetail_skin_line4.gif\"></td></tr>\n";
		$j++;
	}
	pmysql_free_result($result);
	if($j==0) {
		echo "<tr><td colspan=\"{$colspan}\" height=\"25\" align=\"center\">등록된 사용후기가 없습니다.</td></tr>\n";
		echo "<tr><td height=\"1\" colspan=\"{$colspan}\" background=\"{$Dir}images/common/product/{$_cdata->detail_type}/pdetail_skin_line4.gif\"></td></tr>\n";
	}
?>
	</table>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
<?php 
	 if($j != 0) {
	 	$list_page=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
	}
?>
	<tr>
		<td align="center" style="font-size:11px;"><?=$list_page?></td>
	</tr>
	</table>
	</td>
</tr>
</table>
