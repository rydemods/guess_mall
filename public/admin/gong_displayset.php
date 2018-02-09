<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-1";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$gong_num=$_shopdata->gong_num;
$auct_num=$_shopdata->auct_num;
$auct_sort=$_shopdata->auct_sort;
$auct_moveday=$_shopdata->auct_moveday;

$mode=$_POST["mode"];
$up_gong_num=$_POST["up_gong_num"];
$up_auct_num=$_POST["up_auct_num"];
$up_auct_sort=$_POST["up_auct_sort"];
$up_auct_moveday=$_POST["up_auct_moveday"];

if($mode=="update") {
	$sql = "UPDATE tblshopinfo SET 
	gong_num		= '{$up_gong_num}', 
	auct_num		= '{$up_auct_num}', 
	auct_sort		= '{$up_auct_sort}', 
	auct_moveday	= '{$up_auct_moveday}' ";
	pmysql_query($sql,get_db_conn());

	DeleteCache("tblshopinfo.cache");

	$onload="<script>window.onload=function(){ alert(\"경매/공동구매 설정이 완료되었습니다.\"); }</script>";
	$gong_num=$up_gong_num;
	$auct_num=$up_auct_num;
	$auct_sort=$up_auct_sort;
	$auct_moveday=$up_auct_moveday;
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	document.form1.mode.value="update";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 공구/경매 &gt; 경매/공동구매 화면설정 &gt;<span>경매/공동구매 화면설정</span></p></div></div>
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
			<?php include("menu_gong.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">경매/공동구매 화면설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>경매 및 공동구매 페이지의 상품 디스플레이 설정을 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">경매/공구 상품 디스플레이 설정<span>경매상품 진열은 한줄에 한 상품이 진열되며, 공동구매 상품은 한줄에 2개의 상품이 진열됩니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>경매상품 진열개수</span></th>
					<TD>
						<div class="table_none" >
							<table cellSpacing=0 cellPadding=0 width="100%" border=0>
								<colgroup>
									<col width="150" /><col width="" />
								</colgroup>
								<tr>
									<td><img src="images/sample/gong_img1.gif" border="0"></td>
									<td>
										<SELECT name=up_auct_num class="select" style=width:50px>
										<?php
											for($i=4;$i<=25;$i++) {
												if($auct_num==$i) {
													echo "<option value=\"{$i}\" selected>{$i}</option>\n";
												} else {
													echo "<option value=\"{$i}\">{$i}</option>\n";
												}
											}
										?>
										</SELECT>
									 개씩 경매페이지(진행중인 경매, 마감된 경매)에 진열합니다.<br><FONT class=font_orange>ex) 4개를 입력하였을 경우</FONT>
									</td>
								</tr>
							</table>
						</div>
					</TD>
				</TR>

				<TR>
					<th><span>공동구매 상품 진열개수</span></th>
					<TD>
						<div class="table_none" >
							<table cellSpacing=0 cellPadding=0 width="100%" border=0>
								<colgroup>
									<col width="150" /><col width="" />
								</colgroup>
								<tr>
									<td><img src="images/sample/gong_img2.gif" border="0"></td>
									<td>
										<SELECT name=up_gong_num class="select" style=width:50px>
										<?php
											for($i=4;$i<=20;$i+=2) {
												if($gong_num==$i) {
													echo "<option value=\"{$i}\" selected>{$i}</option>\n";
												} else {
													echo "<option value=\"{$i}\">{$i}</option>\n";
												}
											}
										?>
										</SELECT>
										개씩 공구페이지(진행중인 공구, 마감된 공구)에 진열합니다.<br><FONT class=font_orange>ex) 4개를 입력하였을 경우</FONT>
									</td>
								</tr>
							</table>
						</div>
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">전체 경매목록 화면 상품 기본정렬방법 <span>경매 페이지에 상품이 기본으로 정렬되는 방법을 선택하세요.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>기본정렬방법 선택</span></th>
					<TD>
					<INPUT id=idx_auct_sort0 type=radio value=0 name=up_auct_sort <?php if($auct_sort==0)echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_auct_sort0>경매 마감일순</LABEL> &nbsp;&nbsp;&nbsp;
					<INPUT id=idx_auct_sort1 type=radio value=1 name=up_auct_sort <?php if($auct_sort==1)echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_auct_sort1>경매 마감일 역순</LABEL>  &nbsp;&nbsp;&nbsp;
					<INPUT id=idx_auct_sort2 type=radio value=2 name=up_auct_sort <?php if($auct_sort==2)echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_auct_sort2>낮은 가격순</LABEL> &nbsp;&nbsp;&nbsp;
					<INPUT id=idx_auct_sort3 type=radio value=3 name=up_auct_sort <?php if($auct_sort==3)echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_auct_sort3>높은 가격순</LABEL> &nbsp;&nbsp;&nbsp;
					<INPUT id=idx_auct_sort4 type=radio value=4 name=up_auct_sort <?php if($auct_sort==4)echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_auct_sort4>많은 입찰자순</LABEL>
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">마감된 경매, 마감경매 목록으로 이동되는 기간설정<span>경매 마감 후 현재 진행중인 경매 목록에 진열된 상품이 마감된 경매 목록으로 이동되는 기간을 설정 할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>이동기간 설정</span></th>
					<TD class="td_con1">
					경매 마감 후 
					<SELECT name=up_auct_moveday class="select" style=width:50px>
<?php
					for($i=0;$i<=30;$i++) {
						if($i==$auct_moveday) {
							echo "<option value=\"{$i}\" selected>{$i}</option>\n";
						} else {
							echo "<option value=\"{$i}\">{$i}</option>\n";
						}
					}
?>
					</SELECT>
					 일 후에 마감경매 목록으로 이동합니다.
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>경매/공동구매 화면설정</span></dt>
							<dd>
								- 마감된 경매의 경우 설정된 기간만큼 진행중인 경매 목록에 추가 진열된 후 마감된 경매 목록으로 이동됩니다.<br>
								- 쇼핑몰의 운영 방침 및 등록된 경매 물품 수를 고려하여 적절한 값으로 설정하여 사용하시기 바랍니다.

							</dd>
						</dl>
						
					</div>
				</TABLE>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
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
