<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$venderlist=array();
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
pmysql_free_result($result);

$type=$_POST["type"];
$date=$_POST["date"];
$up_vender=(int)$_POST["up_vender"];
$up_subject=$_POST["up_subject"];
$up_content=$_POST["up_content"];
$up_newdate=$_POST["up_newdate"];
$vdate = date("YmdHis");

if(ord($up_subject) && $type=="insert") {
	if($up_vender!=0) {
		if(ord($venderlist[$up_vender]->id)==0) {
			$up_vender=0;
		}
	}
	$sql = "INSERT INTO tblvenderadminnotice(
	vender		,
	date		,
	access		,
	ip		,
	subject		,
	content) VALUES (
	'{$up_vender}', 
	'{$vdate}', 
	0, 
	'{$_SERVER['REMOTE_ADDR']}', 
	'{$up_subject}', 
	'{$up_content}')";
	pmysql_query($sql,get_db_conn());
	$onload="<script>alert('입점업체 공지사항 등록이 완료되었습니다.');</script>\n";
} else if (ord($date) && $type=="modify") {
	if ($mode=="result") {
		if($up_vender!=0) {
			if(ord($venderlist[$up_vender]->id)==0) {
				$up_vender=0;
			}
		}
		$sql = "UPDATE tblvenderadminnotice SET vender='{$up_vender}', subject = '{$up_subject}', ";
		$sql.= "content = '{$up_content}' ";
		if($up_newdate=="Y") $sql.= ", date = '{$vdate}' ";
		$sql.= "WHERE date = '{$date}' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>alert('공지사항 수정이 완료되었습니다.');</script>\n";
		$type='';
		$mode='';
		$date='';
	} else {
		$sql = "SELECT * FROM tblvenderadminnotice WHERE date = '{$date}' ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
		if ($row) {
			$vender=$row->vender;
			$subject = str_replace("\"","&quot;",$row->subject);
			$content = str_replace("\"","&quot;",$row->content);
		} else {
			$onload="<script>alert('수정하려는 공지사항이 존재하지 않습니다.');<script>";
			$type='';
			$mode='';
			$date='';
		}
	}
} else if (ord($date) && $type=="delete") {
	$sql = "DELETE FROM tblvenderadminnotice WHERE date = '{$date}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script> alert('입점업체 공지사항 삭제가 완료되었습니다.');</script>\n";
	$type='';
	$mode='';
	$date='';
}

if (ord($type)==0) $type="insert";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(document.form1.up_subject.value.length==0) {
		document.form1.up_subject.focus();
		alert("공지사항 제목을 입력하세요");
		return;
	}
	if(document.form1.up_content.value.length==0) {
		document.form1.up_content.focus();
		alert("공지사항 내용을 입력하세요");
		return;
	}
	if(type=="modify") {
		if(!confirm("해당 공지사항을 수정하시겠습니까?")) {
			return;
		}
		document.form1.mode.value="result";
	}
	document.form1.type.value=type;
	document.form1.submit();
}
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
function NoticeSend(type,date) {
	if(type=="delete") {
		if(!confirm("해당 공지사항을 삭제하시겠습니까?")) return;
	}
	document.form1.type.value=type;
	document.form1.date.value=date;
	document.form1.submit();
}
function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
</script>

<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리 &gt; <span class="2depth_select">입점업체 공지사항</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_vender.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=date value="<?=$date?>">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_notice_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">입점업체의 공지사항을 등록/수정/삭제 하실 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_notice_stitle1.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>																												
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=6 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="113" align="center">등록일자</TD>
					<TD class="table_cell1" width="424" align="center">제목</TD>
					<TD class="table_cell1" width="100" align="center">입점업체</TD>
					<TD class="table_cell1" width="36" align="center">조회</TD>
					<TD class="table_cell1" width="50" align="center">수정</TD>
					<TD class="table_cell1" width="50" align="center">삭제</TD>
				</TR>
				<TR>
					<TD colspan="6" background="images/table_con_line.gif"></TD>
				</TR>
<?php
				$colspan=6;
				$sql = "SELECT COUNT(*) as t_count FROM tblvenderadminnotice ";
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;			

				$sql = "SELECT * FROM tblvenderadminnotice ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." ".substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
					echo "<tr>\n";
					echo "	<td class=\"td_con2\" align=center>{$str_date}</td>\n";
					echo "	<td class=\"td_con1\" style=\"padding-left:10;padding-right:5\">{$row->subject}</td>\n";
					echo "	<td class=\"td_con1\" align=center>";
					if($row->vender==0) {
						echo "전체";
					} else {
						if(ord($venderlist[$row->vender]->id)) {
							echo "<A HREF=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</A>";
						} else {
							echo "-";
						}
					}
					echo "	</td>\n";
					echo "	<td class=\"td_con1\" align=center>{$row->access}</td>\n";
					echo "	<td class=\"td_con1\" align=center><a href=\"javascript:NoticeSend('modify','{$row->date}');\"><img src=\"images/btn_edit.gif\" width=\"50\" height=\"22\" border=\"0\"></a></td>\n";
					echo "	<td class=\"td_con1\" align=center><a href=\"javascript:NoticeSend('delete','{$row->date}');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></td>\n";
					echo "</tr>\n";
					echo "<TR>\n";
					echo "	<TD colspan={$colspan} width=\"762\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
				pmysql_free_result($result);

				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan={$colspan} align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				<TR>
					<TD colspan=6 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" class="font_size" align=center>
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_notice_stitle2.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">입점업체</TD>
					<TD class="td_con1" width="600">
					<select name="up_vender" class="select">
					<option value=0>전체공지</option>
<?php
					while(list($key,$val)=each($venderlist)) {
						if($val->delflag=="N") {
							echo "<option value=\"{$val->vender}\"";
							if($vender==$val->vender) echo " selected";
							echo ">{$val->id}";
							if(ord($val->com_name)) echo " [{$val->com_name}]";
							echo "</option>";
						}
					}
?>
					</select>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">제 목</TD>
					<TD class="td_con1" width="600"><input type=text name=up_subject value="<?=$subject?>" style="width:80%" class="input"></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">내 용</TD>
					<TD class="td_con1" width="600"><TEXTAREA style="WIDTH: 100%; HEIGHT: 200px" name=up_content class="textarea" class="input"><?php echo $content ?></TEXTAREA></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<?php if($type=="modify"){?>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">등록일 변경여부</TD>
					<TD class="td_con1" width="600"><input type=checkbox id="idx_newdate" name=up_newdate value="Y"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_newdate>해당 공지사항 등록일을 현재시간으로 변경합니다. (최근 공지로 변경)</label></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<?php }?>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td><p align="center"><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">공지사항 관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 공지사항 입력은 본사 관리자만 가능하며 입력한 공지글은 입점사 공지사항 게시판에 등록됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 공지사항은 [전체공지/입점사]별 공지로 구분할 수 있습니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
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
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	</form>

	<form name=vForm action="vender_infopop.php" method=post>
	<input type=hidden name=vender>
	</form>

	</table>

	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
