<?php // hspark
//header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";

if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}

###################################### 입점기능 사용권한 체크 #######################################
$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
    $sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
            ORDER BY lower(b.brandname) ASC
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}
#####################################################################################################

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(f,obj) {
	if(obj.ctype=="X") {
		f.code.value = obj.value+"000000000";
	} else {
		f.code.value = obj.value;
	}

	burl = "product_exceldownload.ctgr.php?depth=2&code=" + obj.value;
	curl = "product_exceldownload.ctgr.php?depth=3";
	durl = "product_exceldownload.ctgr.php?depth=4";
	BCodeCtgr.location.href = burl;
	CCodeCtgr.location.href = curl;
	DCodeCtgr.location.href = durl;
}
function CheckForm(obj, flag) {
	if(flag == "PD"){
		document.form1.mode.value="download";
	}else{
		document.form1.mode.value="download_opt";
	}
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 상품 일괄관리 &gt;<span>상품 엑셀 다운로드</span></p></div></div>
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
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 엑셀 다운로드</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품정보 Excel형식으로 다운로드할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">카테고리별 상품 엑셀 다운로드</div>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>

			<form name=form1 action="./product_csv_download_indb.php" method=post>
			<input type=hidden name=mode>
			<input type="hidden" name="code" value="">
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

				<TR>
					<th><span>등록상품 입점사 선택</span></th>
					<TD>
					<select name=vender>
						<option value="">==== 전체 ====</option>
						<?
						foreach($venderlist as $key => $val) {
                            echo "<option value=\"{$val->bridx}\"";
                            if($sel_vender==$val->bridx) echo " selected";
                            echo ">{$val->brandname}</option>\n";
                        }
						?>
					</select>
					<span class="font_orange">＊다운로드할 상품 입점사를 선택하세요.</span>
					</TD>
				</TR>

				<TR>
					<th><span>상품 카테고리 선택</span></th>
					<TD>
                    <div class="table_none">
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=145></col>
					<col width=3></col>
					<col width=145></col>
					<col width=3></col>
					<col width=145></col>
					<col width=3></col>
					<col width=></col>
					<tr>
						<td>
						<select name="code1" style=width:145 onchange="ACodeSendIt(document.form1,this.options[this.selectedIndex])">
						<option value="">--- 대 분 류 전 체 ---</option>
<?
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_b='000' AND code_c='000' ";
						$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY cate_sort ";
						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)) {
							$ctype=substr($row->type,-1);
							if($ctype!="X") $ctype="";
							echo "<option value=\"{$row->code_a}\" ctype='{$ctype}'>{$row->code_name}";
							if($ctype=="X") echo " (단일분류)";
							echo "</option>\n";
						}
						pmysql_free_result($result);
?>
						</select>
						</td>
						<td></td>
						<td>
						<iframe name="BCodeCtgr" src="product_exceldownload.ctgr.php?depth=2" width="145" height="21" scrolling=no frameborder=no></iframe>
						</td>
						<td></td>
						<td><iframe name="CCodeCtgr" src="product_exceldownload.ctgr.php?depth=3" width="145" height="21" scrolling=no frameborder=no></iframe></td>
						<td></td>
						<td><iframe name="DCodeCtgr" src="product_exceldownload.ctgr.php?depth=4" width="145" height="21" scrolling=no frameborder=no></iframe></td>
					</tr>
					</table>
                    </div>
					</td>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center">
					<img src="images/btn_prd_info_dn.png" id="downloadButton" border="0" style="cursor:hand" onclick="CheckForm(document.form1, 'PD');">
					<img src="images/btn_prd_opt_dn.png" id="downloadButton" border="0" style="cursor:hand" onclick="CheckForm(document.form1, 'OPT');">
				</td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>

			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품정보 엑셀 다운로드</span></dt>
							<dd>
							- 상품정보 엑셀 다운로드 파일은 확장자 CSV 로 저장됩니다.<Br>
							- 상품정보 엑셀 다운로드할 경우 등록상품 입점사 및 상품 카테고리 별로 선택하여 다운로드 가능합니다.
							</dd>
						</dl>
					</div>
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
<?=$onload?>
<?php
include("copyright.php");
