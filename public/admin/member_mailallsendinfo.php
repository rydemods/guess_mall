<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-3";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$date=$_POST["date"];

if ($type=="delete") {
	$sql = "SELECT * FROM tblgroupmail WHERE date = '{$date}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if ($row->issend=="N" && $row->procok=="N") {
			$sql = "DELETE FROM tblgroupmail WHERE date='{$date}' ";
			pmysql_query($sql,get_db_conn());
			unlink($Dir.DataDir."groupmail/".$row->filename);
		}
	}
	pmysql_free_result($result);
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function ViewMailling(i) {
	var p = window.open("about:blank","pop","height=550,width=750,scrollbars=yes");
	p.document.write('<title>단체메일 미리보기</title>');
	p.document.write('<style>\n');
	p.document.write('body { background-color: #FFFFFF; font-family: "굴림"; font-size: x-small; } \n');
	p.document.write('P {margin-top:2px;margin-bottom:2px;}\n');
	p.document.write('</style>\n');
	p.document.write(document.form1.body[i].value);
}

function CancelMailling(date){
	if(confirm("해당 메일 발송을 취소하시겠습니까?")){
		document.form1.type.value="delete";
		document.form1.date.value=date;
		document.form1.submit();
	}
}

function SendMailling(date){
	msg="해당 메일을 발송하시겠습니까?";
	if(date.length==0) msg="미발송된 메일 전체를 발송하시겠습니까?";
	if(confirm(msg)){
		document.sendform.date.value=date;
		document.sendform.submit();
	}
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원관리 부가기능 &gt;<span>단체메일 발송내역 관리</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">단체메일 발송내역 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 전체회원 또는 등급회원에게 발송한 내역 및 발송여부를 확인할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=sendform action="sendgroupmail_process.php" method=post target="hiddenframe">
			<input type=hidden name=date>
			</form>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=date>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td height=3>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=right>
					<A HREF="javascript:SendMailling('')"><B>미발송 메일 전체발송</B></A>
					</td>
				</tr>
				</table>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<TR>
					<th>발송일자</th>
					<th>메일제목</th>
					<th>발송건수</th>
					<th>발송구분</th>
					<th>처리</th>
				</TR>
				<input type=hidden name=body>
<?php
				$colspan=5;
				$sql = "SELECT COUNT(*) as t_count FROM tblgroupmail ";
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT * FROM tblgroupmail ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$cnt++;
					$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." ".substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
					echo "<tr>\n";
					echo "	<TD>{$str_date}</td>\n";
					echo "	<TD><div class=\"ta_l\"><b><A HREF=\"javascript:ViewMailling('{$cnt}');\">".htmlspecialchars($row->subject)."</A></b></div></td>\n";
					echo "	<TD><B><span class=\"font_orange\">".number_format($row->okcnt)."명</span></B></td>\n";
					echo "	<TD>";
					if($row->issend=="Y") echo "<img src=\"images/icon_stateend.gif\" border=\"0\">";
					else if($row->procok=="Y") echo "<img src=\"images/icon_stateing.gif\" border=\"0\">";
					else echo "<img src=\"images/icon_state.gif\" border=\"0\">";
					echo "	</td>\n";
					echo "	<TD>";
					if($row->issend=="Y") echo "-";										//발송완료
					else if($row->procok=="Y") echo "-";								//발송준비중
					else echo "<A HREF=\"javascript:CancelMailling('{$row->date}')\"><B>발송취소</B></A><br><A HREF=\"javascript:SendMailling('{$row->date}')\"><B>발송하기</B></A>";						//발송대기
					echo "	</TD>\n";
					echo "</tr>\n";
					echo "<input type=hidden name=body value=\"".htmlspecialchars($row->body)."\">";
				}
				pmysql_free_result($result);

				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center>발송/대기중인 메일 내역이 없습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
<?php				
				echo "<tr>\n";
				echo "	<td width=\"100%\" align=center class=\"font_size\">\n";
				echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				echo "	</td>\n";
				echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			</form>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>단체메일 발송내역 관리</span></dt>
							<dd>
							- 쇼핑몰 회원에게 발송된 메일은 발송건수로 확인할 수 있습니다.<br>
							- 메일발송은 네트워크 부하가 적은 새벽시간대에 발송하시길 권장합니다.<br>
							- 메일발송전 [발송대기] 클릭시 취소할 수 있습니다.
							</dd>
	
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<?=$onload?>
<?php 
include("copyright.php");
