<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding:5px;padding-top:0px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu5.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu6r.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu9.gif" BORDER="0"></A></TD><? } ?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu7.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu8.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin3_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_t1.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_t2.gif"></td>
			<td><IMG SRC="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_t3.gif" border="0"></td>
		</tr>
		<tr>
			<td background="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_t4.gif"></td>
			<td width="100%" background="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_tbg.gif" style="padding:20px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td><img src="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mycp_skin3_t_text1.gif" border="0"></td>
				<td style="font-size:28px;line-height:28px;letter-spacing:-0.5pt;"><font color="#0099CC"><b><?=$coupon_cnt?>��</b></font></td>
				<td><img src="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mycp_skin3_t_text2.gif" border="0"></td>
			</tr>
			</table>
			</td>
			<td background="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_t7.gif"></td>
		</tr>
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_t5.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_t6.gif"></td>
			<td><IMG SRC="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mypersonal_skin3_t8.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" bordercolordark="black" bordercolorlight="black">
		<tr>
			<td width="100%" valign="bottom" style="padding-left:10px;font-size:11px;letter-spacing:-0.5pt;">* ���� ��� ������ �̿��ϼż� ������ �������� ������ �Ͻñ� �ٶ��ϴ�.</td>
			<td align="right" style="padding-bottom:3px;"><A HREF="#guide"><img src="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/design_mycoupon_skin_btn3.gif" border="0"></A></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<col width="70"></col>
		<col width="100"></col>
		<col width="100"></col>
		<col></col>
		<col width="80"></col>
		<tr>
			<td height="2" colspan="5" bgcolor="#76CCEB"></td>	
		</tr>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><font color="#333333"><b>������ȣ</b></font></td>
			<td><font color="#333333"><b>����</b></font></td>
			<td><font color="#333333"><b>�����ǰ</b></font></td>
			<td><font color="#333333"><b>������</b></font></td>
			<td><font color="#333333"><b>���ѻ���</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="5" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode, ";
		$sql.= "a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, b.date_start, b.date_end ";
		$sql.= "FROM tblcouponinfo a, tblcouponissue b ";
		$sql.= "WHERE b.id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND a.coupon_code=b.coupon_code AND b.date_start<='".date("YmdH")."' ";
		$sql.= "AND (b.date_end>='".date("YmdH")."' OR b.date_end='') ";
		$sql.= "AND b.used='N' ";
		$result = pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$code_a=substr($row->productcode,0,3);
			$code_b=substr($row->productcode,3,3);
			$code_c=substr($row->productcode,6,3);
			$code_d=substr($row->productcode,9,3);

			$prleng=strlen($row->productcode);

			$likecode=$code_a;
			if($code_b!="000") $likecode.=$code_b;
			if($code_c!="000") $likecode.=$code_c;
			if($code_d!="000") $likecode.=$code_d;

			if($prleng==18) $productcode[$cnt]=$row->productcode;
			else $productcode[$cnt]=$likecode;

			if($row->sale_type<=2) {
				$dan="%";
			} else {
				$dan="��";
			}
			if($row->sale_type%2==0) {
				$sale = "����";
			} else {
				$sale = "����";
			}
			
			if($row->productcode=="ALL") {
				$product="��ü��ǰ";
			} else {
				$product = getCodeLoc($row->productcode);

				if($prleng==18) {
					$sql2 = "SELECT productname as product FROM tblproduct ";
					$sql2.= "WHERE productcode='".$row->productcode."' ";
					$result2 = pmysql_query($sql2,get_db_conn());
					if($row2 = pmysql_fetch_object($result2)) {
						$product.= " > ".$row2->product;
					}
					pmysql_free_result($result2);
				}
				if($row->use_con_type2=="N") $product="[".$product."] ����";
			}

			if($cnt>0) {
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"5\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
			}
			$t = sscanf($row->date_start,'%4s%2s%2s%2s%2s%2s');
			$s_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");
			$t = sscanf($row->date_end,'%4s%2s%2s%2s%2s%2s');
			$e_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");

			$date=date("Y.m.d H",$s_time)."�� ~<br>".date("Y.m.d H",$e_time)."��";
			$date="<img src=\"".$Dir."images/common/mycoupon/design_mycoupon_skin_btn1.gif\" border=\"0\" style=\"margin-right:2pt;\" align=\"absmiddle\">".date("Y.m.d H",$s_time)."��~".date("Y.m.d H",$e_time)."��";

			echo "<tr height=\"34\" align=\"center\">\n";
			echo "	<td><font color=\"#333333\">".$row->coupon_code."</font></td>\n";
			echo "	<td><font color=\"#333333\">".number_format($row->sale_money).$dan.$sale."</font></td>\n";
			echo "	<td><font color=\"#333333\">".$product."</font></td>\n";
			echo "	<td>\n";
			echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			echo "	<tr>\n";
			echo "		<td><font color=\"#333333\">".$row->coupon_name."</font></td>\n";
			echo "	</tr>\n";
			echo "	<tr>\n";
			echo "		<td style=\"font-size:11px;letter-spacing:-0.5pt;\"><font color=\"#000000\"><b>".$date." <img src=\"".$Dir."images/common/mycoupon/design_mycoupon_skin_btn2.gif\" border=\"0\" style=\"margin-right:2pt;\" align=\"absmiddle\">".ceil(($e_time-$s_time)/(60*60*24))."��</b></font></td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "	<td><font color=\"#333333\">".($row->mini_price=="0"?"���� ����":number_format($row->mini_price)."�� �̻�")."</td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		pmysql_free_result($result);
		if ($cnt==0) {
			echo "<tr height=\"30\"><td colspan=\"5\" align=\"center\">���������� �����ϴ�.</td></tr>";
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
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="6"></col>
		<col></col>
		<col width="6"></col>
		<tr>
			<td colspan="3"><A name="guide"><IMG SRC="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mycp_skin_t_text3.gif" border="0"></a></td>
		</tr>
		<tr>
			<td height="6" colspan="3" background="<?=$Dir?>images/common/mycoupon/mycp_skin_t01.gif"></td>
		</tr>
		<tr>
			<td background="<?=$Dir?>images/common/mycoupon/mycp_skin_t02.gif"></td>
			<td style="padding:20px;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mycp_skin3_t_text4.gif" border="0" vspace="3"></td>
			</tr>
			<tr>
				<td style="letter-spacing:-0.5pt;padding-left:15px;"><b>1 �ܰ�</b> - ���� ���ÿ��� ������ �����Ͻ� &quot;������ȣ&quot;�� �����Ͻø� ���αݾ�(�Ǵ� �����ݾ�)�� ��Ÿ���ϴ�.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(��������(����)�� ���, ������(������)�� ��Ÿ���ϴ�.)<br><b>2 �ܰ�</b> - &quot;Ȯ��&quot; ��ư�� Ŭ���Ͻø�, �������� ������ �Ϸ�˴ϴ�.</td>
			</tr>
			<tr>
				<td height="30"><hr size="1" noshade color="#E5E5E5"></td>
			</tr>
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/mycoupon/<?=$_data->design_mycoupon?>/mycp_skin3_t_text5.gif" border="0" vspace="3"></td>
			</tr>
			<tr>
				<td style="letter-spacing:-0.5pt;padding-left:15px;">�� �� �������� ��밡�� �ݾ��� ������ �ֽ��ϴ�.<br>�� ������ �� �ֹ��� ���ؼ� ����� �����մϴ�.<br>�� �� �������� �������� ������ �ֽ��ϴ�.<br>�� �ֹ� �� ��ǰ/ȯ��/����� ��� �ѹ� ����Ͻ� ���� ������ �ٽ� ����Ͻ� �� �����ϴ�.<br>�� ���� ����ǰ���� ������ ������ �ش� ǰ�񿡼��� ��밡�� �մϴ�.<br>�� ����/����(%) ������ ���������� ���� ������ ���� �����ݾ׿� ����˴ϴ�.<br>�� �ش� ��ǰ�� ���� ������ �ش� ��ǰ�� ���Ž� ������ �����մϴ�.</td>
			</tr>
			</table>
			</td>
			<td background="<?=$Dir?>images/common/mycoupon/mycp_skin_t04.gif"></td>
		</tr>
		<tr>
			<td height="6" colspan="3" background="<?=$Dir?>images/common/mycoupon/mycp_skin_t03.gif"></td>
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
