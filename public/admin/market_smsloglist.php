<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
}
pmysql_free_result($result);

if(ord($sms_id)==0 || ord($sms_authkey)==0) {
	alert_go('SMS 기본환경 설정에서 SMS 아이디 및 인증키를 입력하시기 바랍니다.','market_smsconfig.php');
}

$today = date("Ymd");

$type=$_POST["type"];
$year=$_POST["year"];
$month=$_POST["month"];
$day=$_POST["day"];
$status=$_POST["status"];
if(ord($status)==0) $status="A";

if(empty($date))	$date = $today;
if(empty($year))	$year = substr($date,0,4);
if(empty($month))	$month = substr($date,4,2);
if(empty($day))		$day = substr($date,6,2);

$t_count=0;





include("header.php"); 
echo $onload;
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function sendsms(msg,tel){
	tmre = /RN_/g
	sqre = /sQ_/g
	dqre = /dQ_/g
	msg = msg.replace(tmre,"\r\n");
	msg = msg.replace(sqre,"'");
	msg = msg.replace(dqre,'"');
	window.open("about:blank","sms","width=200,height=200");
	document.smsform.number.value=tel;
	document.smsform.message.value=msg;
	document.smsform.type.value="sendfail";
	document.smsform.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; SMS 발송/관리 &gt;<span>SMS 발송내역</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr><td height="8"></td></tr>
			
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">SMS발송내역</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>SMS 발송에 대한 상세내역을 확인할 수 있습니다.</span></div>
				</td>
			</tr>
            
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC" style="padding:6pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%" height="35" background="images/blueline_bg.gif" align="center"><b><font color="#0099CC">SMS 발송내역 조회하기</font></b></TD>
						</TR>
						<TR>
							<TD width="100%">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">조회기간</TD>
								<TD class="td_con1" width="500">
<?php
								$maxyear=date("Y");
								echo "<select size=1 name=year class=\"select\">\n";
								for ($i = 2006;$i <= $maxyear; $i++) {
									if($i == $year)  echo "<option value=\"{$i}\" selected>{$i}</option>\n";
									else echo "<option value=\"{$i}\">{$i}</option>\n";
								}
								echo "</select>년 ";
								echo "<select size=1 name=month class=\"select\">\n";
								for ($i = 1;$i <= 12; $i++) {
									if($i == $month)  echo "<option value=\"{$i}\" selected>{$i}</option>\n";
									else echo "<option value=\"{$i}\">{$i}</option>\n";
								}
								echo "</select>월 ";
								echo "<select size=1 name=day class=\"select\">\n";
								echo "<option value=\"ALL\"";
								if($day=="ALL") echo " selected";
								echo ">전체</option>\n";
								for ($i = 1;$i <= 31; $i++) {
									if ($i == $day)  echo "<option value=\"{$i}\" selected>{$i}</option>\n";
									else echo "<option value=\"{$i}\">{$i}</option>\n";
								}
								echo "</select>일";
?>
								</TD>
							</TR>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"></TD>
							</TR>
							<tr>
								<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">처리상태</TD>
								<TD class="td_con1" width="500">
<?php
								echo "<select size=1 name=status class=\"select\">\n";
								echo "<option value='Y'>발송완료</option>";
								/*
								$arstatus = array ("전체보기","발송완료","발송실패","발송예정");
								$statusvalue = array ("A","Y","N","M");
								for($i=0;$i<4;$i++){
									echo "<option value=\"{$statusvalue[$i]}\"";
									if($status==$statusvalue[$i]) echo " selected";
									echo ">{$arstatus[$i]}</option>\n";
								}
								*/
								echo "</select>\n";
?>
								<a href="javascript:document.form1.submit();"><img src="images/btn_search2.gif" border="0" align=absmiddle></a>
								</TD>
							</tr>
							</TABLE>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>

			</form>

			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">

				<?$colspan=5; ?>

				<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">SMS발송내역 검색 결과</div>
					<!-- 소제목 -->
					
				</td>
			</tr>
				<tr>
					<!--<td width="100%" height=3 style="padding-bottom:3pt;" align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 SMS 발송건수 : <B><?= $t_count ?></B>건 <img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>-->
				</tr>
				<tr>
					<td width="100%">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD background="images/table_top_line.gif" colspan=5></TD>
					</TR>
					<TR>
						<TD class="table_cell" width="20" align="center">No</TD>
						<TD class="table_cell1" align="center">전송완료시간</TD>
						<TD class="table_cell1" align="center">수신번호</TD>
						<TD class="table_cell1" width="40%" align="center">메세지</TD>
						<TD class="table_cell1" align="center">처리상태</TD>
					</TR>
					<TR>
						<TD colspan="5" background="images/table_con_line.gif"></TD>
					</TR>
			<?php
				$where_month = str_pad($month,2,0,STR_PAD_LEFT);
				$where_day = str_pad($day,2,0,STR_PAD_LEFT);
				$where_date = $year.$where_month.$where_day;
				$where = " WHERE to_char(send_date,'YYYYMMDD')='{$where_date}'";
				
				$sql = "SELECT COUNT(*) as t_count FROM tblsmslog";
				$sql.= $where;
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;	
				$gotopage = $paging->gotopage;				

				$sql = "SELECT * FROM tblsmslog";
				$sql.= $where;
				$sql.= " ORDER BY send_date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			?>
							<TR>
								<TD class="td_con2" width="34" align="center"><?=$number?></TD>
								<TD class="td_con1" align="center"><?=substr($row->send_date,0,19)?></TD>
								<TD class="td_con1" align="center"><B><?=$row->to_tel_no?></B></TD>
								<TD class="td_con1" width="40%" align="center">&nbsp;<?=$row->msg?></TD>
								<TD class="td_con1" align="center">
									<B> 
								<?php
									if($row->res_msg)	:
								?>
										발송 완료
								<?php
									else	:
								?>
										<!--<font color=#0072BC>발송 예정</font>-->
										<font color=#FF0000><u>발송 실패</u></font>
								<?php
									endif;
								?>
								</B></TD>
							</TR>
			<?php
					$cnt++;
				}
			?>
							<TR>
								<TD colspan="5" background="images/table_con_line.gif"></TD>
							</TR>
			<?php
				if($cnt<1){
			?>
						<tr>
							<td class=td_con2 colspan="5" align=center>조건에 맞는 발송내역이 존재하지 않습니다.</td>
						</tr>
			<?php
				}
			?>
					<TR>
						<TD background="images/table_top_line.gif" colspan=5></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td width="100%" height=10></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" class="font_size" align="center">
						<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="25">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>SMS 발송내역 관리</span></dt>
							<dd>
							  - SMS 발송내역은 전송스케쥴 및 네트워크 상태에 따라 수분의 시간이 지연될수 있습니다. <br>
							  - SMS 발송 실패된 건에 대해서는 2일 후 일괄적으로 재충전 해드립니다. <br>
							  - SMS 발송 실패된 건의 [발송실패]를 누르시면 발송 실패한 결과를 확인할 수 있습니다. <br>
							  - SMS 발송 실패된 건의 재전송을 원하시면 실패건에 대해서 재전송이 가능합니다. <br>
							  - "전송완료시간"은 통신사(SKT, KTF, LGT)에서 알려준 시간으로서 문자메세지가 휴대폰에 도착한 시간입니다.
							</dd>	
						</dl>
				</div>
				
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>

	<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
	<input type=hidden name=year value="<?=$year?>">
	<input type=hidden name=month value="<?=$month?>">
	<input type=hidden name=day value="<?=$day?>">
	<input type=hidden name=status value="<?=$status?>">
	</form>

	<form name=smsform method=post action="sendsms.php" target=sms>
	<input type=hidden name=type>
	<input type=hidden name=number>
	<input type=hidden name=message>
	</form>

	</table>
	</td>
</tr>
</table>

<?php 
include("copyright.php");
