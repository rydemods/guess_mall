<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/venderlib.php");

include("access.php");
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "product_myprd.ctgr.php?code="+code+"&depth=2";
	surl = "product_myprd.ctgr.php?depth=3";
	durl = "product_myprd.ctgr.php?depth=4";
	BCodeCtgr.location.href = murl;
	CCodeCtgr.location.href = surl;
	DCodeCtgr.location.href = durl;
}

function CheckForm() {
	document.sForm.submit();
}
</script>
<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<!-- <col width=740></col> -->
<col width=1300></col>
<!-- <col width=80></col> -->
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
					<FONT COLOR="#ffffff"><B>상품 엑셀 다운로드<B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>상품 엑셀 다운로드</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 상품 분류 선택후 정보 선택하여 다운로드 가능합니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 확장자 CSV 로 상품/옵션 각각 다운로드할 수 있습니다.</td>
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
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15">
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<form name="sForm" method="post" action="./product_csv_download_indb.php">
						<input type="hidden" name="vender" value="<?=$_VenderInfo->getVidx()?>">
						<input type="hidden" name="code">
						<input type="hidden" name="listnum" value="<?=$listnum?>">
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=0 style="table-layout:fixed">
							<col width=50></col>
							<col width=5></col>
							<col width=155></col>
							<col width=5></col>
							<col width=155></col>
							<col width=5></col>
							<col width=155></col>
							<col width=5></col>
							<col width=155></col>
							<tr>
								<td><u>분류</u></td>
								<td></td>
								<td>
								<select name="code1" style=width:155 onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
								<option value="">------ 대 분 류 ------</option>
<?
								$sql = "SELECT SUBSTR(b.c_category,1,3) as prcode FROM tblproduct a left join tblproductlink b on a.productcode=b.c_productcode ";
								$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "GROUP BY prcode ";
								$result=pmysql_query($sql,get_db_conn());
								$codes="";
								while($row=pmysql_fetch_object($result)) {
									$codes.=$row->prcode.",";
								}
								pmysql_free_result($result);
								if(strlen($codes)>0) {
									$codes=rtrim($codes,',');
									$prcodelist=str_replace(',','\',\'',$codes);
								}
								if(strlen($prcodelist)>0) {
									$sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode ";
									$sql.= "WHERE code_a IN ('".$prcodelist."') AND code_b='000' AND code_c='000' ";
									$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
									echo $sql;
									$result=pmysql_query($sql,get_db_conn());
									while($row=pmysql_fetch_object($result)) {
										echo "<option value=\"".$row->code_a."\"";
										if($row->code_a==substr($code,0,3)) echo " selected";
										echo ">".$row->code_name."</option>\n";
									}
									pmysql_free_result($result);
								}
?>
								</select></td>
								<td></td>
								<td><iframe name="BCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>&depth=2" width="155" height="21" scrolling=no frameborder=no></iframe></td>
								<td></td>
								<td><iframe name="CCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,6)?>&select_code=<?=$code?>&depth=3" width="155" height="21" scrolling=no frameborder=no></iframe></td>
								<td></td>
								<td><iframe name="DCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,9)?>&select_code=<?=$code?>&depth=4" width="155" height="21" scrolling=no frameborder=no></iframe></td>
							</tr>
							</table>
							</td>
							<td align=right>
							<table border=0 cellpadding=0 cellspacing=0 style="table-layout:fixed">
							<col width=50></col>
							<col width=5></col>
							<col width=155></col>
							<col width=5></col>
							<col width=></col>
							<tr>
								<td><u>정보</u></td>
								<td></td>
								<td>
								<select name="mode" style=width:155>
								<option value="download">------ 상 품 ------</option>
								<option value="download_opt">------ 옵 션 ------</option>
								</select></td>
								<td></td>
								<td><A HREF="javascript:CheckForm()"><img src=images/btn_exceldown.gif border=0></A></td>
							</tr>
							</table>
							</td>
						</tr>
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

<?php include("copyright.php"); ?>