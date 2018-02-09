<?php
/********************************************************************* 
// 파 일 명		: vender_management.php 
// 설     명		: 입점업체 정보관리
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 입점업체 리스트 관리
// 작 성 자		: hspark
// 수 정 자		: 2015.10.23 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "vd-1";
	$MenuCode = "vender";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode=$_POST["mode"];
	$vender=$_POST["vender"];

	$disabled=$_POST["disabled"];
	$s_check=$_POST["s_check"];
	$search=$_POST["search"];

#---------------------------------------------------------------
# 벤더 승인 상태를 변경한다.
#---------------------------------------------------------------
	if($mode=="disabled" && ord($vender) && ($disabled=="0" || $disabled=="1")) {
		$sql = "UPDATE tblvenderinfo SET disabled='{$disabled}' ";
		$sql.= "WHERE vender='{$vender}' AND delflag='N' ";
		if(pmysql_query($sql,get_db_conn())) {
			$log_content = "## 입점업체 승인상태 변경 ## - 벤더 : {$vender} , 승인여부 : ".($disabled==0?"승인":"보류")."";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

			echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.pageForm.submit();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
		}
	}

#---------------------------------------------------------------
# 검색부분을 정리한다.
#---------------------------------------------------------------
	$qry = "WHERE delflag='N' ";
	if($disabled=="Y") $qry.= "AND disabled='0' ";
	else if($disabled=="N") $qry.= "AND disabled='1' ";
	if(ord($search)) {
		if($s_check=="id") $qry.= "AND id='{$search}' ";
		else if($s_check=="com_name") $qry.= "AND com_name LIKE '%{$search}%' ";
	}

	include("header.php");  // 상단부분을 불러온다.

#---------------------------------------------------------------
# 검색쿼리 카운트 및 페이징을 정리한다.
#---------------------------------------------------------------
	$sql = "SELECT COUNT(*) as t_count FROM tblvenderinfo {$qry} ";
	$paging = new Paging($sql,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;			
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function SearchVender() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function VenderModify(vender) {
	document.form3.vender.value=vender;
	document.form3.action="vender_infomodify.php";
	document.form3.submit();
}

function VenderDetail(vender) {
	window.open("about:blank","venderdetail_pop","height=100,width=100,toolbar=no,menubar=no,scrollbars=yes,status=no");

	document.form2.vender.value=vender;
	document.form2.action="vender_detailpop.php";
	document.form2.target="venderdetail_pop";
	document.form2.submit();
}

function setVenderDisabled(vender,disabled) {
	if(disabled!="0" && disabled!="1") {
		alert("승인상태 설정이 잘못되었습니다.");
		return;
	}
	document.etcform.vender.value=vender;
	if(confirm("해당 입점업체의 승인상태를 ["+(disabled=="0"?"ON":"OFF")+"] 하시겠습니까?")) {
		document.etcform.mode.value="disabled";
		document.etcform.disabled.value=disabled;
		document.etcform.action="<?=$_SERVER['PHP_SELF']?>";
		document.etcform.target="processFrame";
		document.etcform.submit();
	}
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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리 &gt; <span class="2depth_select">입점업체 정보관리</span></td>
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
			<?php include("menu_vender.php"); // 해당 메뉴부분을 불러온다. ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_management_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>입점 업체의 정보를 수정/삭제 하실 수 있습니다.</p></TD>
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
			<form name="sForm" method="post">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="750" bgcolor="#0099CC" style="padding:6pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD height="35" background="images/blueline_bg.gif"><p align="center"><b><font color="#0099CC">입점 업체 검색 선택</font></b></TD>
						</TR>
						<TR>
							<TD width="760">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD class="td_con1" style="padding-top:10pt;" align="center"><select name=disabled class="select">
									<option value="">승인/대기업체 전체</option>
									<option value="Y" <?php if($disabled=="Y")echo"selected";?>>승인 업체만 검색</option>
									<option value="N" <?php if($disabled=="N")echo"selected";?>>대기 업체만 검색</option>
									</select>
									<select name="s_check" class="select">
									<option value="id" <?php if($s_check=="id")echo"selected";?>>업체 아이디로 검색</option>
									<option value="com_name" <?php if($s_check=="com_name")echo"selected";?>>업체명으로 검색</option>
									</select>
									<input type=text name=search value="<?=$search?>" class="input">
									<img src=images/btn_inquery03.gif border=0 style="cursor:hand" onClick="SearchVender()" align="absmiddle">
								</TD>
							</TR>
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
			<tr>
				<td height="10"></td>
			</tr>
			</form>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_management_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing="0" cellPadding="0" border="0" style="table-layout:fixed">
				<col width="45"></col>
				<col width="75"></col>
				<col width="85"></col>
				<col width=""></col>
				<col width="67"></col>
				<col width="90"></col>
				<col width="183"></col>
				<col width="45"></col>
				<col width="45"></col>
				<col width="45"></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="10" height="1"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">번호</TD>
					<TD class="table_cell1" align="center">업체ID</TD>
					<TD class="table_cell1" align="center">회사명</TD>
					<TD class="table_cell1" align="center">회사전화</TD>
					<TD class="table_cell1" align="center">담당자명</TD>
					<TD class="table_cell1" align="center">휴대전화</TD>
					<TD style="BORDER-left:#E3E3E3 1pt solid;" align="center">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<col width="25%"></col>
						<col width="25%"></col>
						<col width="25%"></col>
						<col width="25%"></col>
						<tr height="18">
							<td colspan="4" class="table_cell" align="center">상품권한</td>
						</tr>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif"></TD>
						</TR>
						<tr>
							<td class="table_cell" align="center">등록</td>
							<td class="table_cell1" align="center">수정</td>
							<td class="table_cell1" align="center">삭제</td>
							<td class="table_cell1" align="center">인증</td>
						</tr>
						</table>
					</TD>
					<TD class="table_cell1" align="center">관리</TD>
					<TD class="table_cell1" align="center">상세</TD>
					<TD class="table_cell1" align="center">승인</TD>
				</TR>
				<TR>
					<TD colspan="10" align=center background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?php
#---------------------------------------------------------------
# 벤더 정보 리스트를 불러온다.
#---------------------------------------------------------------
		$colspan=10;
		if($t_count>0) {
			$sql = "SELECT * FROM tblvenderinfo {$qry} ";
			$sql = $paging->getSql($sql);
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
				echo "	<td class=\"td_con2\" align=center>{$number}</td>\n";
				echo "	<td class=\"td_con1\" align=center><A HREF=\"".$Dir.(MinishopType=="ON"?"minishop/":"minishop.php?storeid=").$row->id."\" target=_blank>{$row->id}</A></td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;{$row->com_name}&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;{$row->com_tel}&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;{$row->p_name}&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;{$row->p_mobile}&nbsp;</td>\n";
				echo "	<td style=\"BORDER-left:#E3E3E3 1pt solid;\" align=center>\n";
				echo "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				echo "	<col width=25%></col>\n";
				echo "	<col width=25%></col>\n";
				echo "	<col width=25%></col>\n";
				echo "	<col width=25%></col>\n";
				echo "	<tr>\n";
				echo "		<td class=\"td_con2\" align=center><B>".($row->grant_product[0]=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "		<td class=\"td_con1\" align=center><B>".($row->grant_product[1]=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "		<td class=\"td_con1\" align=center><B>".($row->grant_product[2]=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "		<td class=\"td_con1\" align=center><B>".($row->grant_product[3]=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "	</tr>\n";
				echo "	</table>\n";
				echo "	</td>\n";
				echo "	<td class=\"td_con1\" align=center><A HREF=\"javascript:VenderModify({$row->vender})\">[관리]</A></td>\n";
				echo "	<td class=\"td_con1\" align=center><A HREF=\"javascript:VenderDetail({$row->vender})\">[상세]</A></td>\n";
				echo "	<td class=\"td_con1\" align=center>";
				if($row->disabled=="0") {
					echo "<img src=images/icon_on.gif border=0 align=absmiddle style=\"cursor:hand\" onclick=\"setVenderDisabled('{$row->vender}','1')\">";
				} else {
					echo "<img src=images/icon_off.gif border=0 align=absmiddle style=\"cursor:hand\" onclick=\"setVenderDisabled('{$row->vender}','0')\">";
				}
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD colspan=\"10\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
				$i++;
			}
			pmysql_free_result($result);
		} else {
			echo "<tr><td class=td_con2 colspan={$colspan} align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
		}
?>

				<TR>
					<TD background="images/table_top_line.gif" colspan="10"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			<form name=form2 method=post>
			<input type=hidden name=vender>
			</form>

			<form name="form3" method="post">
			<input type=hidden name='vender'>
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>

			<form name="pageForm" method="post">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>

			<form name=etcform method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=mode>
			<input type=hidden name=vender>
			<input type=hidden name=disabled>
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
						<td width="701"><span class="font_dotline">입점업체 정보관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 등록된 입점업체 리스트와 기본적인 정보사항을 확인할 수 있습니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 입점사 정보변경은 [관리]를 이용하여 변경할 수 있습니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 입점사 관리자 URL은 <B><font class=font_orange><A HREF="http://<?=$_ShopInfo->getShopurl()?>vender/" target="_blank">http://<?=$_ShopInfo->getShopurl()?>vender/</A></font></B> 입니다. </p></td>
					</tr>
					<!-- <tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 입점사 미니샵 관리 URL은 <B><font class=font_orange><A HREF="http://<?=$_ShopInfo->getShopurl()?>vender/" target="_blank">http://<?=$_ShopInfo->getShopurl()?>vender/</A></font></B> 입니다. </p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top"><p>- 해당업체 미니샵 URL은 <B><font class=font_orange>http://<?=$_ShopInfo->getShopurl().(MinishopType=="ON"?"minishop/":"minishop.php?storeid=")?>업체ID</font></B> 입니다. </p></td>
					</tr> -->
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
</table>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php"); // 하단부분을 불러온다. 
