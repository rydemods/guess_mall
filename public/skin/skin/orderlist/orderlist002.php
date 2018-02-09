<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding:5px;padding-top:0px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="bottom">
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu2r.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu5.gif" BORDER="0"></A></TD>
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
		<td style="padding:10px;padding-right:0px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;">* 가장 최근 주문 <font color="#F02800" style="font-size:11px;letter-spacing:-0.5pt;"><b>6개월 자료까지 제공</b></font>되며, <font color="#000000" style="font-size:11px;letter-spacing:-0.5pt;"><b>6개월 이전 자료는 일자를 지정해서 조회</b></font>하시기 바랍니다.<br>
		&nbsp;&nbsp;&nbsp;(일자별로 조회시 최대 지난 3년 동안의 주문내역 조회가 가능합니다)<br>
		*&nbsp;한 번에 조회 가능한 기간은 6개월로 일자 선택시 조회 기간을 6개월 이내로 선택하셔야 합니다.</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#FFD5A9">
		<tr>
			<td background="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/design_search_skin2_tbg.gif" style="padding:20px;">
			<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td height="26"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_text01.gif" border="0" align="absmiddle"></td>
				<td><A HREF="javascript:GoSearch('TODAY')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_btn01.gif" border="0" align="absmiddle"></A>
				<A HREF="javascript:GoSearch('15DAY')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_btn02.gif" border="0" align="absmiddle"></A>
				<A HREF="javascript:GoSearch('1MONTH')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_btn03.gif" border="0" hspace="2" align="absmiddle"></A>
				<A HREF="javascript:GoSearch('3MONTH')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_btn04.gif" border="0" align="absmiddle"></A>
				<A HREF="javascript:GoSearch('6MONTH')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_btn05.gif" border="0" hspace="2" align="absmiddle"></A></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_text02.gif" border="0" align="absmiddle"></td>
				<td><SELECT onchange="ChangeDate('s')" name="s_year" align="absmiddle" style="font-size:11px;">
				<?
				for($i=date("Y");$i>=(date("Y")-2);$i--) {
					echo "<option value=\"".$i."\"";
					if($s_year==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT> <SELECT onchange="ChangeDate('s')" name="s_month" style="font-size:11px;">
				<?
				for($i=1;$i<=12;$i++) {
					echo "<option value=\"".$i."\"";
					if($s_month==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT> <SELECT name="s_day" style="font-size:11px;">
				<?
				for($i=1;$i<=get_totaldays($s_year,$s_month);$i++) {
					echo "<option value=\"".$i."\"";
					if($s_day==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT><b> ~ </b> <SELECT onchange="ChangeDate('e')" name="e_year" style="font-size:11px;">
				<?
				for($i=date("Y");$i>=(date("Y")-2);$i--) {
					echo "<option value=\"".$i."\"";
					if($e_year==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT> <SELECT onchange="ChangeDate('e')" name="e_month" style="font-size:11px;">
				<?
				for($i=1;$i<=12;$i++) {
					echo "<option value=\"".$i."\"";
					if($e_month==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT> <SELECT name="e_day" style="font-size:11px;">
				<?
				for($i=1;$i<=get_totaldays($e_year,$e_month);$i++) {
					echo "<option value=\"".$i."\"";
					if($e_day==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT><a href="javascript:CheckForm();"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_btn06.gif" border="0" hspace="5" align="absmiddle"></a> </td>
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
	<tr>
		<td valign="bottom">
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="javascript:GoOrdGbn('A')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu01<?=($ordgbn=="A"?"on":"off")?>.gif" border="0"></A></TD>
			<TD><A HREF="javascript:GoOrdGbn('S')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu02<?=($ordgbn=="S"?"on":"off")?>.gif" border="0"></TD>
			<TD><A HREF="javascript:GoOrdGbn('C')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu03<?=($ordgbn=="C"?"on":"off")?>.gif" border="0"></TD>
			<TD><A HREF="javascript:GoOrdGbn('R')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu04<?=($ordgbn=="R"?"on":"off")?>.gif" border="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/mypersonal_skin2_menubg.gif"></TD>
		</TR>
		<tr>
			<td height="2" colspan="5" bgcolor="#FF7D04"></td>
		</tr>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="1" width="100%" border="0" bgcolor="#E7E7E7" style="table-layout:fixed">
		<!-- 주문일자, 주문 상품명, 배송상태, 배송추적, 결제방법, 결제금액, 상세정보  -->
		<col width="65"></col>
		<col></col>
		<col width="80"></col>
		<col width="100"></col>
		<col width="80"></col>
		<col width="80"></col>
		<col width="60"></col>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><font color="#333333"><b>주문일자</b></font></td>
			<td><font color="#333333"><b>주문 상품명</b></font></td>
			<td><font color="#333333"><b>배송상태</b></font></td>
			<td><font color="#333333"><b>배송추적</b></font></td>
			<td><font color="#333333"><b>결제방법</b></font></td>
			<td><font color="#333333"><b>결제금액</b></font></td>
			<td><font color="#333333"><b>상세정보</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="7" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
		$result=pmysql_query($sql,get_db_conn());
		$delicomlist=array();
		while($row=pmysql_fetch_object($result)) {
			$delicomlist[$row->code]=$row;
		}
		pmysql_free_result($result);

		$s_curtime=strtotime("$s_year-$s_month-$s_day");
		$s_curdate=date("Ymd",$s_curtime);
		$e_curtime=strtotime("$e_year-$e_month-$e_day");
		$e_curdate=date("Ymd",$e_curtime)."999999999999";

		$sql = "SELECT COUNT(*) as t_count FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
		if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
		else if($ordgbn=="C") $sql.= "AND deli_gbn IN ('C','D') ";
		else if($ordgbn=="R") $sql.= "AND deli_gbn IN ('R','E') ";
		$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
		$paging = new Paging($sql,10,10,'GoPage',true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = "SELECT ordercode, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn ";
		$sql.= "FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
		if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
		else if($ordgbn=="C") $sql.= "AND deli_gbn IN ('C','D') ";
		else if($ordgbn=="R") $sql.= "AND deli_gbn IN ('R','E') ";
		$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
		$sql.= "ORDER BY ordercode DESC ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			echo "<tr bgcolor=\"#FFFFFF\" onmouseover=\"this.style.background='#FEFBD1';\" onmouseout=\"this.style.background='#FFFFFF';\">\n";
			echo "	<td align=\"center\" style=\"font-size:8pt;padding:3;\">".substr($row->ordercode,0,4).".".substr($row->ordercode,4,2).".".substr($row->ordercode,6,2)."</td>\n";
			echo "	<td colspan=\"3\">\n";
			echo "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			echo "	<col></col>\n";
			echo "	<col width=\"1\"></col>\n";
			echo "	<col width=\"80\"></col>\n";
			echo "	<col width=\"1\"></col>\n";
			echo "	<col width=\"100\"></col>\n";
			$sql = "SELECT * FROM tblorderproduct WHERE ordercode='".$row->ordercode."' ";
			$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
			$result2=pmysql_query($sql,get_db_conn());
			$jj=0;
			while($row2=pmysql_fetch_object($result2)) {
				if($jj>0) echo "<tr><td colspan=\"5\" height=\"1\" bgcolor=\"#E7E7E7\"></tr>";
				echo "<tr>\n";
				echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt;\"><A HREF=\"javascript:OrderDetailPop('".$row->ordercode."')\" onmouseover=\"window.status='주문내역조회';return true;\" onmouseout=\"window.status='';return true;\">".$row2->productname."</a></td>\n";
				echo "	<td bgcolor=\"#E7E7E7\"></td>\n";
				echo "	<td align=\"center\" style=\"font-size:8pt;\">";
				if ($row2->deli_gbn=="C") echo "주문취소";
				else if ($row2->deli_gbn=="D") echo "취소요청";
				else if ($row2->deli_gbn=="E") echo "환불대기";
				else if ($row2->deli_gbn=="X") echo "발송준비";
				else if ($row2->deli_gbn=="Y") echo "발송완료";
				else if ($row2->deli_gbn=="N") {
					if (strlen($row->bank_date)<12 && strstr("BOQ", $row->paymethod[0])) echo "입금확인중";
					else if ($row->pay_admin_proc=="C" && $row->pay_flag=="0000") echo "결제취소";
					else if (strlen($row->bank_date)>=12 || $row->pay_flag=="0000") echo "발송준비";
					else echo "결제확인중";
				} else if ($row2->deli_gbn=="S") {
					echo "발송준비";
				} else if ($row2->deli_gbn=="R") {
					echo "반송처리";
				} else if ($row2->deli_gbn=="H") {
					echo "발송완료 [정산보류]";
				}
				echo "	</td>\n";
				echo "	<td bgcolor=\"#E7E7E7\"></td>\n";
				echo "	<td align=\"center\" style=\"font-size:8pt;padding-top:3;\">";

				$deli_url="";
				$trans_num="";
				$company_name="";
				if($row2->deli_gbn=="Y") {
					if($row2->deli_com>0 && $delicomlist[$row2->deli_com]) {
						$deli_url=$delicomlist[$row2->deli_com]->deli_url;
						$trans_num=$delicomlist[$row2->deli_com]->trans_num;
						$company_name=$delicomlist[$row2->deli_com]->company_name;
						echo $company_name."<br>";
						if(strlen($row2->deli_num)>0 && strlen($deli_url)>0) {
							if(strlen($trans_num)>0) {
								$arrtransnum=explode(",",$trans_num);
								$pattern=array("[1]","[2]","[3]","[4]");
								$replace=array(substr($row2->deli_num,0,$arrtransnum[0]),substr($row2->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row2->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row2->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
								$deli_url=str_replace($pattern,$replace,$deli_url);
							} else {
								$deli_url.=$row2->deli_num;
							}
							echo "<A HREF=\"javascript:DeliSearch('".$deli_url."')\"><img src=\"".$Dir."images/common/btn_mypagedeliview.gif\" border=\"0\"></A>";
						}
					} else {
						echo "-";
					}
				} else {
					echo "-";
				}
				echo "	</td>\n";
				echo "</tr>\n";
				$jj++;
			}
			pmysql_free_result($result2);
			echo "	</table>\n";
			echo "	</td>\n";
			echo "	<td align=\"center\" style=\"font-size:8pt;\">";
			if (strstr("B", $row->paymethod[0])) echo "무통장입금";
			else if (strstr("V", $row->paymethod[0])) echo "실시간계좌이체";
			else if (strstr("O", $row->paymethod[0])) echo "가상계좌";
			else if (strstr("Q", $row->paymethod[0])) echo "가상계좌-<FONT COLOR=\"#FF0000\">매매보호</FONT>";
			else if (strstr("C", $row->paymethod[0])) echo "신용카드";
			else if (strstr("P", $row->paymethod[0])) echo "신용카드-<FONT COLOR=\"#FF0000\">매매보호</FONT>";
			else if (strstr("M", $row->paymethod[0])) echo "휴대폰";
			else echo "";
			echo "	</td>\n";
			echo "	<td align=\"right\" style=\"font-size:8pt;padding-right:5\"><FONT COLOR=\"#EE1A02\"><B>".number_format($row->price)."원</B></FONT></td>\n";
			echo "	<td align=center><A HREF=\"javascript:OrderDetailPop('".$row->ordercode."')\" onmouseover=\"window.status='주문내역조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/mypage_detailview.gif\" border=\"0\"></A></td>\n";
			echo "</tr>\n";
			echo "<tr><td colspan=\"7\" height=\"1\" bgcolor=\"#F5F5F5\"></td></tr>\n";
			$cnt++;
		}
		pmysql_free_result($result);
?>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td align="center"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_text03.gif" border="0"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>	
				<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_tablet01_1.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_tablet01_2.gif"></td>
				<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_tablet01_3.gif" border="0"></td>
			</tr>
			<tr>
				<td background="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_tablet02_1.gif" border="0"></td>
				<td style="padding:10px;">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_table_img01.gif" border="0"></td>
					<td style="padding-left:5px;"><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_table_line.gif" border="0"></td>
					<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_table_img02.gif" border="0"></td>
					<td style="padding-left:5px;"><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_table_line.gif" border="0"></td>
					<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_table_img03.gif" border="0"></td>
					<td style="padding-left:5px;"><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_table_line.gif" border="0"></td>
					<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_table_img04.gif" border="0"></td>
				</tr>
				</table>
				</td>
				<td background="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_tablet02_2.gif"></td>
			</tr>
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_tablet03_1.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_tablet03_2.gif"></td>
				<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin2_tablet03_3.gif" border="0"></td>
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
	</table>
	</td>
</tr>
</table>
