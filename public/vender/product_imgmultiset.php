<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

if($_venderdata->grant_product[1]!="Y") {
	alert_go("상품정보 수정 권한이 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",-1);
}
include("header.php"); ?>
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

function SearchPrd() {
	document.sForm.target="PrdtListIfrm";
	document.sForm.action="product_imgmultiset.prlist.php";
	document.sForm.submit();

	document.all["PrdtImgIfrm"].style.height=0;
	document.etcform.prcode.value="";
	document.etcform.target="PrdtImgIfrm";
	document.action="blank.php";
	document.etcform.submit();
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
					<FONT COLOR="#ffffff"><B>상품 다중이미지 등록/관리</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>상품 다중이미지 등록/관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 카테고리 분류/검색으로 다중이미지를 등록합니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 삭제버튼 클릭시 해당 상품으로 등록된 다중이미지가 모두 삭제됩니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 다중이미지는 최대 10개까지 등록할 수 있습니다.</td>
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
						<form name="sForm" method="post">
						<input type="hidden" name="code" value="<?=$code?>">
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<tr>
								<td>
								<select name="code1" style=width:155 onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
								<option value="">------ 대 분 류 ------</option>
<?
								$sql = "SELECT SUBSTR(productcode,1,3) as prcode FROM tblproduct ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
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
									$result=pmysql_query($sql,get_db_conn());
									while($row=pmysql_fetch_object($result)) {
										echo "<option value=\"".$row->code_a."\"";
										if($row->code_a==substr($code,0,3)) echo " selected";
										echo ">".$row->code_name."</option>\n";
									}
									pmysql_free_result($result);
								}
?>
								</select>
								</td>
								<td></td>
								<td>
								<iframe name="BCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>&depth=2" width="155" height="21" scrolling=no frameborder=no></iframe>
								</td>
								<td></td>
								<td><iframe name="CCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,6)?>&select_code=<?=$code?>&depth=3" width="155" height="21" scrolling=no frameborder=no></iframe></td>
								<td></td>
								<td><iframe name="DCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,9)?>&select_code=<?=$code?>&depth=4" width="155" height="21" scrolling=no frameborder=no></iframe></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<tr>
								<td>
								<select name=disptype style="width:100%">
								<option value="">진열/대기상품 전체</option>
								<option value="Y" <?if($disptype=="Y")echo"selected";?>>진열상품만 검색</option>
								<option value="N" <?if($disptype=="N")echo"selected";?>>대기상품만 검색</option>
								</select>
								</td>

								<td></td>

								<td>
								<select name="s_check" style="width:100%">
								<option value="name" <?if($s_check=="name")echo"selected";?>>상품명으로 검색</option>
								<option value="code" <?if($s_check=="code")echo"selected";?>>상품코드로 검색</option>
								</select>
								</td>

								<td></td>

								<td><input type=text name=search value="<?=$search?>" style="width:100%"></td>

								<td></td>

								<td><A HREF="javascript:SearchPrd()"><img src=images/btn_inquery03.gif border=0></A></td>
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

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=5></td></tr>
				<tr>
					<td>
					<iframe name="PrdtListIfrm" src="product_imgmultiset.prlist.php" width="100%" height="300" scrolling=no frameborder=no style="background:FFFFFF"></iframe>
					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr>
					<td>
					<iframe name="PrdtImgIfrm" src="blank.php" width="100%" height='800'  scrolling=no frameborder=no style="background:FFFFFF"></iframe>
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

<form name=etcform method=post>
<input type=hidden name=prcode>
</form>
<?=$onload?>
<?php include("copyright.php"); ?>
