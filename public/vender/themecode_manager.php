<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="tree_menu.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}
</script>

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>테마 카테고리 설정</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>테마 카테고리 설정</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵에 표시되는 기본 카테고리는 변경할 수 없습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 기본 카테고리 외에 미니샵 내 카테고리를 미니샵의 특성에 맞게 자유롭게 설정하고 추가로 진열할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 테마 카테고리 작업 후 [저장하기] 버튼을 클릭하셔야 실제 반영됩니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=10></td></tr>
			<tr>
				<td style="padding:15">

				<table width=100% border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td valign=top>
					<table width="100%" border=0 cellspacing=0 cellpadding=0>
					<col width=12></col>
					<col width=></col>
					<tr valign=top>
						<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 카테고리 노출 설정</td>
					</tr>
					<tr>
						<td bgcolor=#E1E1E1 style="padding:5">
						<table border=0 cellpadding=6 cellspacing=0 width=100% bgcolor=#FFFFFF>
						<tr>
							<td>
							<input type="radio" name="code_disptype" value="YY" style='cursor:hand' <?if($_venderdata->code_distype=="YY")echo"checked";?>>기본 카테고리 + 테마 카테고리 노출
							&nbsp;&nbsp;
							<input type="radio" name="code_disptype" value="YN" style='cursor:hand' <?if($_venderdata->code_distype=="YN")echo"checked";?>>기본 카테고리만 노출
							&nbsp;&nbsp;
							<input type="radio" name="code_disptype" value="NY" style='cursor:hand' <?if($_venderdata->code_distype=="NY")echo"checked";?>>테마 카테고리만 노출</td>
							<td align="right"><img src="images/btn_save02.gif" border="0" style="cursor:hand" onClick="tcodelistifrm.SaveCodeDispType()"></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=20></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=230></col>
				<col width=10></col>
				<col width=></col>
				<tr>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 테마 카테고리 미리보기</td>
					</tr>
					<tr><td height=3></td></tr>
					<tr>
						<td bgcolor=E1E1E1 style=padding:5>
						<div id=menutree style="width:100%;height:215;overflow:auto">
						<table bgcolor=FFFFFF width=100% border=0 cellspacing=0 cellpadding=0 >
						<tr>
							<td height=215 valign=top style=padding-left:10>
							<script language="Javascript">
							foldersTree = genFolderRoot(" &nbsp;최상위 카테고리", "themecode_manager.list.php", 'tcodelistifrm');
<?
							$sql = "SELECT code_a,code_name FROM tblvenderthemecode ";
							$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' AND code_b='000' "; 
							$sql.= "ORDER BY sequence DESC ";
							$result2=pmysql_query($sql,get_db_conn());
							while($row2=pmysql_fetch_object($result2)) {
								echo "tcode".$row2->code_a." = insFolder(foldersTree, genFolder(\"".$row2->code_name."\", \"themecode_manager.list.php?code_a=".$row2->code_a."\", \"tcodelistifrm\"));\n";

								$sql = "SELECT code_a,code_name FROM tblvenderthemecode ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "AND code_a='".$row2->code_a."' AND code_b!='000' ORDER BY sequence DESC ";
								$result=pmysql_query($sql,get_db_conn());
								while($row=pmysql_fetch_object($result)) {
									echo "insItem(tcode".$row->code_a.", genItem(\"".$row->code_name."\", \"themecode_manager.list.php?code_a=".$row->code_a."\", \"tcodelistifrm\"));\n";
								}
								pmysql_free_result($result);
							}
							pmysql_free_result($result2);

							echo "initializeDocument(foldersTree);\n";
?>
							</script>

							</td>
						</tr>
						</table>
						</div>
						</td>
					</tr>
					</table>
					</td>
					<td></td>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 테마 카테고리 수정/삭제</td>
					</tr>
					<tr><td height=3></td></tr>
					<tr>
						<td bgcolor=E1E1E1 valign=top style=padding:5>
						<iframe name="tcodelistifrm" src="themecode_manager.list.php" width="100%" height="215" scrolling=no frameborder=no style="background:FFFFFF"></iframe>
						</td>
					</tr>
					<tr><td height=5></td></tr>
					<tr><td height=1 bgcolor=D7D7D7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 style=padding:10,20>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td>카테고리명</td>
							<td><input type=text name=ctgrEdit value="" class=txt onKeydown="tcodelistifrm.f_editChange();" onChange="tcodelistifrm.f_editChange();"></td>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height=1 bgcolor=D7D7D7></td></tr>
					<tr><td height=10></td></tr>
					<tr>
						<td align=center>
						<img src=images/btn_top01.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.moveTop()">
						<img src=images/btn_up03.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.moveUp()">
						<img src=images/btn_down13.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.moveDown()">
						<img src=images/btn_bottom01.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.moveBottom()">
						&nbsp; <img src=images/btn_add03.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.addRow()">
						<img src=images/btn_delete08.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.delRow()">
						<img src=images/btn_save02.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.applyRow()">
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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
<?php include("copyright.php"; ?>
