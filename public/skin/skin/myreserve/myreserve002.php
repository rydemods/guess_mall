<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding:5px;padding-top:0px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu5r.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu6.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu9.gif" BORDER="0"></A></TD><? } ?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu7.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu8.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin2_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="padding:5px;">
		<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#FFD99F">
		<tr>
			<td background="<?=$Dir?>images/common/myreserve/<?=$_data->design_myreserve?>/mypersonal_skin2_tbg.gif" style="padding:20px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td><img src="<?=$Dir?>images/common/myreserve/<?=$_data->design_myreserve?>/mypersonal_skin2_t_text1.gif" border="0"></td>
				<td align="center" valign="bottom" style="padding-left:10px;padding-right:10px;font-size:30px;line-height:28px;letter-spacing:-0.5pt;"><font color="#FF4C00"><b><?=number_format($reserve)?>��</b></font></td>
				<td><img src="<?=$Dir?>images/common/myreserve/<?=$_data->design_myreserve?>/mypersonal_skin2_t_text2.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding:10px;padding-right:0px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;">
		* ���Բ��� ��밡���� �������� <b><?=number_format($reserve)?>��</b> �Դϴ�.<br>
		* ������ �ݾ��� <b><?=number_format($maxreserve)?>�� �̻� ����</b>�Ǿ�����, ����Ͻ� �� �ֽ��ϴ�.������ ������ ��뿩�θ� Ȯ���ϴ� �ȳ����� ���ɴϴ�. <br>
		* ������ ������ <b>�ֱ� 6�������� ����</b>�ǹǷ� ���� ���� �ٶ��ϴ�.<br>
		* �ֹ��Ϸ� �� �ο��� ���� ������ �ش� ������ Ŭ���Ͻø� �󼼳����� Ȯ���Ͻ� �� �ֽ��ϴ�.(��, �����Ͻ� �ֹ������� ��ȸ�� �Ұ����մϴ�. )
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<col width="100"></col>
		<col></col>
		<col width="80"></col>
		<col width="80"></col>
		<tr>
			<td height="2" colspan="4" bgcolor="#FF7D04"></td>
		</tr>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><font color="#333333"><b>�߻�����</b></font></td>
			<td><font color="#333333"><b>�߻�����</b></font></td>
			<td><font color="#333333"><b>�����ݾ�</b></font></td>
			<td><font color="#333333"><b>������</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="4" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$sql = "SELECT COUNT(*) as t_count FROM tblreserve ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		$paging = new Paging($sql,10,10,'GoPage',true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = "SELECT * FROM tblreserve WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		$sql.= "ORDER BY date DESC";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);

			if($cnt>0) {
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"4\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
			}

			$ordercode="";
			$orderprice="";
			$orderdata=$row->orderdata;
			if(strlen($orderdata)>0) {
				$tmpstr=explode("=",$orderdata);
				$ordercode=$tmpstr[0];
				$orderprice=$tmpstr[1];
			}

			echo "<tr height=\"28\" align=\"center\">\n";
			echo "	<td><font color=\"#333333\">".$date."</font></td>\n";
			echo "	<td align=\"left\"><nobr><a";
			if(strlen($ordercode)>0) echo " style=\"cursor:hand;\" onclick=\"OrderDetailPop('".$ordercode."')\">";
			echo "<font color=\"#333333\">".$row->content."</font></a></td>\n";
			echo "	<td>";
			if(strlen($orderprice)>0 && $orderprice>0) {
				echo "<font color=\"#F02800\"><b>".number_format($orderprice)."��</b></font>";
			} else {
				echo "-";
			}
			echo "</td>\n";
			echo "	<td><font color=\"#333333\">".number_format($row->reserve)."��</font></td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		pmysql_free_result($result);
		if ($cnt==0) {
			echo "<tr height=\"28\"><td colspan=\"4\" align=\"center\">�ش系���� �����ϴ�.</td></tr>";
		}
?>
		</table>
		</td>
	</tr>
	<tr>
		<td height="3" background="<?=$Dir?>images/common/mypersonal_skin_line3.gif"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td align="center"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
	</tr>
	</table>
	</td>
</tr>
</table>
