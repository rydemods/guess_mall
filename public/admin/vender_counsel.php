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

$scheck=$_REQUEST["scheck"];
$search=$_REQUEST["search"];

$venderlist=array();
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
pmysql_free_result($result);

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function Search(form) {
	if(form.search.value.length==0) {
		alert("검색어를 입력하세요.");
		form.search.focus();
		return;
	}
	form.submit();
}

function search_default(){
	document.form1.scheck.value = "";
	document.form1.search.value = "";
	document.form1.submit();
}

function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function ViewCounsel(date) {
	window.open("about:blank","vendercounsel_pop","width=600,height=450,scrollbars=yes");
	document.form3.date.value=date;
	document.form3.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value = block;
	document.pageForm.gotopage.value = gotopage;
	document.pageForm.submit();
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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리  &gt; <span class="2depth_select">입점업체 상담게시판</span></td>
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
			<input type=hidden name=mode>
			<tr><td height="8"></td></tr>
			<tr>
				<td>

				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_counsel_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
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
					<TD width="100%" class="notice_blue">쇼핑몰 본사와 입점업체간의 1:1 문의에 대한 답변 및 관리를 하실 수 있습니다.</TD>
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
<?php
			$colspan=5;
			$sql = "SELECT COUNT(*) as t_count FROM tblvenderadminqna a ";
			if(ord($scheck) && ord($search)) {
				$sql.= "LEFT OUTER JOIN tblvenderinfo b ON a.vender=b.vender ";
			}
			if(ord($scheck) && ord($search)) {
				$sql.= "WHERE b.{$scheck} LIKE '%{$search}%' ";
			}
			$paging = new Paging($sql,10,15);
			$t_count = $paging->t_count;
			$gotopage = $paging->gotopage;			
?>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_counsel_stitle1.gif" WIDTH="240" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372">&nbsp;</td>
					<td width="372" align=right><img src="images/icon_8a.gif" width="13" height="13" border="0">총 게시물 : <B><?=number_format($t_count)?></B>건, &nbsp; <img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></B> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="760" colspan="<?=$colspan?>" height=1></TD>
				</TR>
				<TR>
					<TD class="table_cell1" width="43" align=center>NO</TD>
					<TD class="table_cell1" width="381" align="center">제목</TD>
					<TD class="table_cell1" width="59" align="center">입점업체</TD>
					<TD class="table_cell1" width="98" align="center">날짜</TD>
					<TD class="table_cell1" width="67" align="center">답변여부</TD>
				</TR>
				<input type=hidden name=delcheck>
				<TR>
					<TD colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
				</TR>
<?php
				$sql = "SELECT a.vender,a.subject,a.date,a.re_date FROM tblvenderadminqna a ";
				if(ord($scheck) && ord($search)) {
					$sql.= "LEFT OUTER JOIN tblvenderinfo b ON a.vender=b.vender ";
				}
				if(ord($scheck) && ord($search)) {
					$sql.= "WHERE b.{$scheck} LIKE '%{$search}%' ";
				}
				$sql.= "ORDER BY a.date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." (".substr($row->date,8,2).":".substr($row->date,10,2).")";
					echo "<tr>\n";
					echo "	<td class=\"td_con2\" align=center>{$number}</td>\n";
					echo "	<td class=\"td_con1\">&nbsp;<A HREF=\"javascript:ViewCounsel('{$row->date}');\">".strip_tags($row->subject)."</A></td>\n";
					echo "	<td class=\"td_con1\" align=center>";
					if(ord($venderlist[$row->vender]->id)) {
						echo "<A HREF=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</A>";
					} else {
						echo "-";
					}
					echo "	</td>\n";
					echo "	<td class=\"td_con1\" align=center>{$date}</td>\n";
					echo "	<td class=\"td_con1\" align=center>";
					if(strlen($row->re_date)==14) {
						echo "<img src=\"images/icon_finish.gif\" width=\"74\" height=\"25\" border=\"0\">";
					} else {
						echo "<img src=\"images/icon_nofinish.gif\" width=\"74\" height=\"25\" border=\"0\">";
					}
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<TR>\n";
					echo "	<TD colspan=\"5\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan={$colspan} align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$colspan?>" height=1></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
<?php
			echo "<tr>\n";
			echo "	<td colspan={$colspan} align=center style='font-size:11px;'>\n";
			echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
?>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" class="main_sfont_non">&nbsp;</td>
				</tr>
				<tr>
					<td width="100%" class="main_sfont_non">
					<table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" width="100%">
					<tr>
						<td width="859" bgcolor="#FFFFFF" align=center>
						<SELECT name=scheck class="select">
						<OPTION value=id <?php if($scheck=="id")echo"selected";?>>업체 아이디</OPTION>
						<OPTION value=com_name <?php if($scheck=="com_name")echo"selected";?>>회사명</OPTION>
						</SELECT>
						<INPUT type="text" name=search value="<?=$search?>" class="input">
						<A href="javascript:Search(document.form1);"><img src="images/icon_search.gif" alt=검색 align=absMiddle border=0></a>
						<A href="javascript:search_default();"><IMG src="images/icon_search_clear.gif" align=absMiddle border=0 width="68" height="25" hspace="2"></A>
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
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">입점업체 상담게시판 관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top" style="letter-spacing:-0.5pt;">- 상담게시판은 본사와 입점사간에 1:1게시판 입니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top" style="letter-spacing:-0.5pt;">- 입점사 아이디 확인 [제목]클릭후 답변처리 할 수 있습니다.</td>
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
	</table>
	</td>
</tr>

<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>

<form name=pageForm action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=scheck value="<?=$scheck?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<form name=form3 action="vender_counsel_pop.php" method=post target="vendercounsel_pop">
<input type=hidden name=date>
</form>
</table>
<?=$onload?>
<?php 
include("copyright.php");
