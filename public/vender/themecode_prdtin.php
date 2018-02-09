<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language=javascript src="themeCtgrPrdt.js.php"></script>
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
					<FONT COLOR="#ffffff"><B>테마 카테고리 상품진열</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>테마 카테고리 상품진열</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 내 상품을 자유롭게 테마 카테고리에 진열할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 특성에 맞게 테마를 설정하여 카테고리를 효과적으로 운영해 보세요.</td>
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
				
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 내 상품 찾기</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td align=center valign=top bgcolor=F0F0F0 style=padding:10>

					<table width=100% border=0 cellspacing=0 cellpadding=0>

					<form name="prdListFrm" method="post">
					<input type="hidden" name="sectcode" value="">

					<tr valign=top>
						<td>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=45><img src=images/sub_text02.gif border=0></td>
							<td>
							<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td>
								<select name="code" style=width:100 onchange="ACodeSendIt(this.options[this.selectedIndex].value);">
								<option value="">--선택하세요--</option>
<?
								$sql = "SELECT SUBSTR(productcode,1,3) as prcode FROM tblproduct ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "AND display='Y' GROUP BY prcode ";
								$result=pmysql_query($sql,get_db_conn());
								$codes="";
								while($row=pmysql_fetch_object($result)) {
									$codes.=$row->prcode.",";
								}
								pmysql_free_result($result);
								if(strlen($codes)>0) {
									$codes=ltrim($codes,',');
									$prcodelist=str_replace(',','\',\'',$codes);
								}
								if(strlen($prcodelist)>0) {
									$sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode ";
									$sql.= "WHERE code_a IN ('".$prcodelist."') AND code_b='000' AND code_c='000' ";
									$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
									$result=pmysql_query($sql,get_db_conn());
									while($row=pmysql_fetch_object($result)) {
										echo "<option value=\"".$row->code_a."\">".$row->code_name."</option>\n";
									}
									pmysql_free_result($result);
								}
?>
								</select>
								</td>
								<td><iframe name="BCodeCtgr" src="product_code.ctgr.php" width="100" height="23" scrolling=no frameborder=no></iframe></td>
								<td><iframe name="CCodeCtgr" src="product_code.ctgr.php" width="100" height="23" scrolling=no frameborder=no></iframe></td>
								<td><iframe name="DCodeCtgr" src="product_code.ctgr.php" width="100" height="23" scrolling=no frameborder=no></iframe></td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
						<td align=right>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=45><img src=images/sub_text03.gif border=0></td>
							<td><input type=text name="goodNm" size=18 value="" class=txt onkeydown="if(event.keyCode == 13) return f_getData();"> <img src=images/btn_search01.gif border=0 align=absmiddle style="cursor:hand" onClick="f_getData()" ></td>
						</tr>
						</table>
						</td>
					</tr>

					</form>

					</table>

					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr>
					<td>
					<iframe name="PrdtListIfrm" src="product_prlist.select.php" width="100%" height="190" scrolling=no frameborder=no style="background:FFFFFF"></iframe>
					</td>
				</tr>



				<tr>
					<td valign=top>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr height=114 valign=top style=padding-top:10>
						<td width=50%> </td>
						<td width=204 nowrap align=center background=images/center_bg09.gif>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td style=color:2A97A7 class=small>* 선택한 상품을<br>&nbsp; 테마 카테고리로<br>&nbsp; 복사합니다.</td>
						</tr>
						<tr>
							<td height=10></td>
						</tr>
						<tr>
							<td align=center><img src=images/btn_copy02.gif border=0 style="cursor:hand" onClick="copyPrdInfo();"></td>
						</tr>
						</table>
						</td>
						<td width=50%> </td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td valign=top>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr valign=top>
							<td style=padding-top:2><img src="images/icon_dot03.gif" border=0 align=absmiddle> 테마 카테고리</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height=4></td>
					</tr>
					<tr>
						<td height=1 bgcolor=E6567B></td>
					</tr>

					<form name="ThemePrdtListFrm" method="post" >
					<input type="hidden" name="theme_sectcode" value="0">

					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table width=100% border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=45><img src=images/sub_text04.gif border=0></td>
							<td>
							<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td>
								<select name="ThemeACodeCtgr" style="width:170px" onchange="ThemeACodeIt(document.prdListFrm, this.options[this.selectedIndex]);SelCtgrPrdtList();">
								<option value="0">---대분류----</a>
<?
								$sql = "SELECT code_a,code_b,code_name FROM tblvenderthemecode ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' AND code_b='000' ";
								$sql.= "ORDER BY sequence DESC ";
								$result=pmysql_query($sql,get_db_conn());
								while($row=pmysql_fetch_object($result)) {
									echo "<option value=\"".$row->code_a."\">".$row->code_name."</option>\n";
								}
								pmysql_free_result($result);
?>
								</select>
								</td>
								<td>
								<iframe id="ThemeBCodeCtgr" src="product_themecode.ctgr.php" width="170" height="23" scrolling=no frameborder=no></iframe>
								</td>
							</tr>
							</table>
							</td>
							<td align=right>
							<table border=0 cellspacing=0 cellpadding=0>
							<tr>
								<td width=45><img src=images/sub_text03.gif border=0></td>
								<td><input type=text name="themeGoodNm" size=18 value="" class=txt onkeydown="if(event.keyCode == 13) return ThemeSelCtgrPrdtList();" > <img src=images/btn_search01.gif border=0 align=absmiddle style="cursor:hand" onClick="ThemeSelCtgrPrdtList();"></td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height=5></td>
					</tr>
					</form>
					<tr>
						<td valign=top align=center>
						<iframe name="ThemePrdtListIfrm" src="product_themeprlist.select.php" width="100%" height="460" scrolling=no frameborder=no></iframe>
						</td>
					</tr>
					</table>
					</td>
				</tr>



				</table>

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

<SCRIPT FOR=BCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>
<SCRIPT FOR=CCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>
<SCRIPT FOR=DCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php include("copyright.php"); ?>
