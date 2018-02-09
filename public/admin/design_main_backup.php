<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/category.class.php");
include("access.php");



####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$catelist =new CATEGORYLIST();

$type=$_POST["type"];
$design=$_POST["design"];

if($type=="update" && strlen($design)==3) {
	$sql = "SELECT * FROM tbltempletinfo WHERE icon_type='{$design}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$sql = "UPDATE tblshopinfo SET
		frame_type	= '{$row->frame_type}',
		top_type	= '{$row->top_type}',
		menu_type	= '{$row->menu_type}',
		main_type	= '{$row->main_type}',
		title_type	= 'N',
		icon_type	= '{$row->icon_type}' ";
		if(pmysql_query($sql,get_db_conn())) {
			DeleteCache("tblshopinfo.cache");
			$onload="<script>window.onload=function(){alert(\"메인화면 템플릿 설정이 완료되었습니다.\");}</script>";

			$_shopdata->frame_type=$row->frame_type;
			$_shopdata->top_type=$row->top_type;
			$_shopdata->menu_type=$row->menu_type;
			$_shopdata->main_type=$row->main_type;
			$_shopdata->title_type="N";
			$_shopdata->icon_type=$row->icon_type;
		}
	}
	pmysql_free_result($result);
}

if($_shopdata->top_type=="topp"
|| $_shopdata->menu_type=="menup"
|| $_shopdata->main_type=="mainm"
|| $_shopdata->main_type=="mainn"
|| $_shopdata->main_type=="mainp"
|| $_shopdata->title_type=="Y") {
	$_shopdata->icon_type="U";
}
include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<link rel="stylesheet" type="text/css" href="DynamicTree.css">
<script src="DynamicTree.js"></script>
<script language="JavaScript">
function CheckForm() {
	if(confirm("선택하신 디자인으로 변경하시겠습니까?\n\n변경된 디자인은 쇼핑몰에 바로 적용됩니다.")) {
		document.form1.type.value="update";
		document.form1.submit();
	}
}

var icon_type="<?=$_shopdata->icon_type?>";
function design_preview(design) {
	icon_type=design;
	document.all["preview_img"].src="images/sample/main"+design+".gif";
	document.all["Bimage"].src="images/sample/main"+design+"B.gif";
}

function ChangeDesign(tmp) {
	if(typeof(document.form1["design"][tmp])=="object") {
		document.form1["design"][tmp].checked=true;
		design_preview(document.form1["design"][tmp].value);
	} else {
		document.form1["design"].checked=true;
		design_preview(document.form1["design"].value);
	}
}

function changeMouseOver(img) {
	 img.style.border='1 dotted #999999';
}
function changeMouseOut(img,dot) {
	 img.style.border="1 "+dot;
}

function preview_over(obj) {
	try {
		if(icon_type!="U") {
			document.all[obj].style.visibility = "visible";
		}
	} catch(e) {}
}

function preview_out(obj) {
	try {
		if(icon_type!="U") {
			document.all[obj].style.visibility = "hidden";
		}
	} catch(e) {}
}
function openTree(obj, chkable){
}
</script>

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; <span>디자인 스킨</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=50></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">

			<div class="product_setup_wrap"><!-- 카테고리관리 -->
					<table width="100%" cellspacing=0 cellpadding=0 border=0>
					<colgroup>
						<col width=270><col width="">
					</colgroup>
						<tr valign=top>
							<td align=left>

							<!-- 카테고리 트리 -->
							<div class="cate_tree_wrap">
									<table cellpadding="0" cellspacing="0" width="100%" height="700">
									<tr>
										<td width="100%" height="100%" valign="top">

										<table cellpadding="0" cellspacing="0" width="100%" height="100%">
											<tr>
												<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;">

												<DIV class=MsgrScroller id=contentDiv style="width:99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
													<DIV id=bodyList>

														<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
															<tr>
																<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
															</tr>
															<tr>
																<!-- 상품카테고리 목록 -->
																<td id="code_list" nowrap valign=top>
																	<div class="DynamicTree">
																		<div class="wrap" id="tree">
																			<? echo $catelist->getDesignCateTree();?>
																		</div>
																	</div>
																</td>
																<!-- 상품카테고리 목록 끝 -->
															</tr>
														</table>

													</DIV>
												</DIV>

												</td>
											</tr>
										</table>

										</td>
									</tr>
									</table>

							</div>

						</td>
						<!-- 카테고리 트리 -->

						<!-- 설정영역 -->
						<td align=left>

							<div class="title_depth3">디자인 스킨설정</div>
							<div class="title_depth3_sub">보유 스킨 리스트</div>

							<div class="table_style02">
								<table width=100% cellpadding=0 cellspacing=0>
									<colgroup>
										<col width="50" /><col width="" /><col width="80" /><col width="80" /><col width="60" /><col width="40" />
									</colgroup>
									<tr>
										<th>선택</th>
										<th>스킨명</th>
										<th>상태</th>
										<th>미리보기</th>
										<th colspan=2>비고</th>
									</tr>
									<tr>
										<td><input type="radio" name="" id="" /></td>
										<td><div class="ta_l">스킨이름 출력임돠</div></td>
										<td>사용스킨</td>
										<td><a href="#"><img src="img/btn/btn_view01.gif" alt="미리보기" /></a></td>
										<td><a href="#"><img src="img/btn/btn_download.gif" alt="다운로드" /></a></td>
										<td><a href="#"><img src="img/btn/btn_copy.gif" alt="복사" /></a></td>
									</tr>
									<tr>
										<td><input type="radio" name="" id="" /></td>
										<td><div class="ta_l">스킨이름 출력임돠~~</div></td>
										<td>작업스킨</td>
										<td><a href="#"><img src="img/btn/btn_view01.gif" alt="미리보기" /></a></td>
										<td><a href="#"><img src="img/btn/btn_download.gif" alt="다운로드" /></a></td>
										<td><a href="#"><img src="img/btn/btn_copy.gif" alt="복사" /></a></td>
									</tr>
									<tr>
										<td><input type="radio" name="" id="" /></td>
										<td><div class="ta_l">스킨이름 출력임돠~~~~~</div></td>
										<td>미적용</td>
										<td><a href="#"><img src="img/btn/btn_view01.gif" alt="미리보기" /></a></td>
										<td><a href="#"><img src="img/btn/btn_download.gif" alt="다운로드" /></a></td>
										<td><a href="#"><img src="img/btn/btn_copy.gif" alt="복사" /></a></td>
									</tr>
								</table>
							</div>

							<!-- 버튼 가운데 정렬 -->
							<div class="bbs_btn_wrap">
								<div class="bbs_btn">
									<ul>
										<li class="none"><a href="#"><img src="img/btn/btn_skin_use.gif" alt="사용스킨적용하기" /></a></li>
										<li><a href="#"><img src="img/btn/btn_skin_setup.gif" alt="작업스킨적용하기" /></a></li>
										<li><a href="#"><img src="img/btn/btn_skin_upload.gif" alt="스킨업로드" /></a></li>
									</ul>
								</div>
							</div>

							<div class="title_depth3_sub mt_20">무료 스킨 리스트</div>

							<div class="point_title ">무료스킨 선택하기</div>
							<div class="skin_select_wrap">
								<table cellpadding="0" cellspacing="0" width="100%">
									<?php
											$sql = "SELECT * FROM tbltempletinfo ORDER BY icon_type DESC ";
											$result=pmysql_query($sql,get_db_conn());
											$i=0;
											while($row=pmysql_fetch_object($result)) {
												if($i==0) echo "<tr>\n";
												if($i>0 && $i%3==0) echo "</tr>\n<tr>\n";
												if($i%3==0) {
													echo "<td width=\"246\"><p align=\"center\">";
												} else {
													echo "<td width=\"246\"><p align=\"center\">";
												}
												echo "<img src=\"images/sample/main{$row->icon_type}.gif\" border=0 width=150 style='border:1 dotted #FFFFFF' onMouseOver='changeMouseOver(this);' onMouseOut=\"changeMouseOut(this,'dotted #FFFFFF');\" style='cursor:hand;' onclick='ChangeDesign({$i});' class=\"imgline1\">";
												echo "<br><input type=radio id=\"idx_design{$i}\" name=design value=\"{$row->icon_type}\" ";
												if($_shopdata->icon_type==$row->icon_type) echo "checked";
												echo " onclick=\"design_preview('{$row->icon_type}')\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\">";
												echo "</td>\n";
												$i++;
											}
											pmysql_free_result($result);
											if($i%3!=0) {
												for($j=$i;$j<3;$j++) echo "<td width=\"246\"><p align=\"center\">&nbsp;</td>\n";
												echo "</tr>\n";
											}
									?>
								</table>
							</div>
							<!-- 버튼 가운데 정렬 -->
							<div class="bbs_btn_wrap">
								<div class="bbs_btn">
									<ul>
										<li class="none"><a href="#"><img src="images/botteon_save.gif" border="0"></a></li>
									</ul>
								</div>
							</div>

							<!-- 메뉴얼 -->
							<div class="sub_manual_wrap mt_20">
								<div class="title"><p>메뉴얼</p></div>
								<dl>
									<dt><span>제목제목제목</span></dt>
									<dd>
										  - 내용내용내용  <br />
										  - 내용내용내용 <br />
										  - 내용내용내용
									</dd>
								</dl>
							</div>





						</td>
						<!-- 설정영역 -->

						</tr>
					</table>
				</div><!-- 카테고리관리 -->


			<table cellpadding="0" cellspacing="0" width="100%">
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
					<TD width="100%" class="notice_blue"><p>쇼핑몰 메인화면 디자인을 선택하여 사용하실 수 있습니다.</p></TD>
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
					<TD><IMG SRC="images/design_main_stitle1.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td><p align="center"><img id="preview_img" src="images/sample/main<?=$_shopdata->icon_type?>.gif" border=0 style="cursor:hand;" onmouseover="preview_over('Bdiv')" onmouseout="preview_out('Bdiv')" style="border-width:3pt; border-color:rgb(222,222,222); border-style:solid;" class="imgline"><div id="Bdiv" style="position:absolute; z-index:0; visibility:hidden;"><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><tr><td><img name=Bimage src="images/sample/main<?=$_shopdata->icon_type?>B.gif"></td></tr></table></div></p></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_main_stitle2.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>메인템플릿에 따라 서브페이지 전체 디자인 컨셉이 같이 변경됩니다.</p></TD>
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
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%" height="30" background="images/blueline_bg.gif"><p align="center"><b><font color="#0099CC">템플릿 선택하기</font></b></TD>
						</TR>
						<TR>
							<TD width="100%" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
						</TR>
						<tr>
							<td height="3"></td>
						</tr>
						<TR>
							<TD width="100%" style="padding:10pt;">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%">
								<table cellpadding="0" cellspacing="0" width="100%">
<?php
		$sql = "SELECT * FROM tbltempletinfo ORDER BY icon_type DESC ";
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			if($i==0) echo "<tr>\n";
			if($i>0 && $i%3==0) echo "</tr>\n<tr>\n";
			if($i%3==0) {
				echo "<td width=\"246\"><p align=\"center\">";
			} else {
				echo "<td width=\"246\"><p align=\"center\">";
			}
			echo "<img src=\"images/sample/main{$row->icon_type}.gif\" border=0 width=150 style='border:1 dotted #FFFFFF' onMouseOver='changeMouseOver(this);' onMouseOut=\"changeMouseOut(this,'dotted #FFFFFF');\" style='cursor:hand;' onclick='ChangeDesign({$i});' class=\"imgline1\">";
			echo "<br><input type=radio id=\"idx_design{$i}\" name=design value=\"{$row->icon_type}\" ";
			if($_shopdata->icon_type==$row->icon_type) echo "checked";
			echo " onclick=\"design_preview('{$row->icon_type}')\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\">";
			echo "</td>\n";
			$i++;
		}
		pmysql_free_result($result);
		if($i%3!=0) {
			for($j=$i;$j<3;$j++) echo "<td width=\"246\"><p align=\"center\">&nbsp;</td>\n";
			echo "</tr>\n";
		}
?>
								</table>
								</td>
							</tr>
							<tr>
								<td width="100%" height="25"><hr size="1" noshade color="#EBEBEB"></td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</TD>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">템플릿 선택하기</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 원하시는 메인화면 템플릿을 선택하십시요. 선택하지 않으면 기본값으로 설정됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 템플릿 사용을 원치 않으셔서 자체 디자인을 하실 경우에는 <b>"개별디자인-메인 및 상하단"</b>에서 디자인 하신 후 옵션설정에서<br>
						<b>&nbsp;&nbsp;</b>디자인 설정을 하시면 됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 해당 이미지를 선택하신 후 "적용하기" 버튼을 누르셔야 변경이 됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 왼쪽 이미지에 마우스를 올리시면 큰 이미지를 보실 수 있습니다.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">메인본문 레이아웃 변경 및 우측의 메뉴를 보이지 않게 하기</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- <a href="javascript:parent.topframe.GoMenu(2,'design_eachmain.php');"><span class="font_blue">디자인관리 > 개별디자인 - 메인 및 상하단 > 메인본문 꾸미기</span></a> 에서 매크로명령어를 이용하여 레이아웃을 자유롭게 변경 가능합니다.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">개별 디자인</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- <a href="javascript:parent.topframe.GoMenu(2,'design_eachtopmenu.php');"><span class="font_blue">디자인관리 > 개별디자인- 메인 및 상하단</span></a> 에서 상단 와  좌측, 본문, 하단등의 개별 디자인을 할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 개별 디자인 사용시 템플릿은 적용되지 않습니다.</td>
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
</table>
<script type="text/javascript">
var tree = new DynamicTree("tree");
//tree.category = '<?=$_GET[category]?>';
tree.init();
</script>
<?=$onload?>
<?php
include("copyright.php");
