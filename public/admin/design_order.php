<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-3";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$templet_list=array(0=>"T01",1=>"T02",2=>"T03",3=>"L01",4=>"L02",5=>"L03",6=>"TEM01");

$type=$_POST["type"];
$design=$_POST["design"];

if($type=="update") {
	if($_shopdata->design_order!=$design) {
		$sql = "UPDATE tblshopinfo SET design_order='{$design}' ";
		pmysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");

		$_shopdata->design_order=$design;
	}
	$onload="<script>window.onload=function(){alert(\"주문서 화면 템플릿 설정이 완료되었습니다.\");}</script>";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(confirm("선택하신 디자인으로 변경하시겠습니까?")) {
		document.form1.type.value="update";
		document.form1.submit();
	}
}

function design_preview(design) {
	document.all["preview_img"].src="images/sample/order"+design+".gif";
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

</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 템플릿-페이지 본문 &gt;<span>주문서 화면 템플릿</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" >
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 타이틀 -->
					<div class="title_depth3">주문서 화면 템플릿</div>
					<div class="title_depth3_sub"><span>상품 주문서 화면 디자인을 선택하여 사용하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">현재 등록된 디자인</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td align=center><img id="preview_img" src="images/sample/order<?=$_shopdata->design_order?>.gif" border="0" class="imgline"></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" >
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%"><div class="point_title">템플릿 선택하기</div></TD>
						</TR>
						<TR>
							<TD width="100%" style="padding:10pt;" bgcolor="#f8f8f8">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%">
								<table cellpadding="0" cellspacing="0" width="100%">
<?php
		for($i=0;$i<count($templet_list);$i++) {
			echo "<tr>\n";
			echo "<td width=\"100%\" align=center>";
			echo "<img src=\"images/sample/order{$templet_list[$i]}.gif\" border=\"0\" class=\"imgline1\" onMouseOver='changeMouseOver(this);' onMouseOut=\"changeMouseOut(this,'dotted #FFFFFF');\" style='cursor:hand;' onclick='ChangeDesign({$i});'>";
			echo "<br><input type=radio id=\"idx_design{$i}\" name=design value=\"{$templet_list[$i]}\" ";
			if($_shopdata->design_order==$templet_list[$i]) echo "checked";
			echo " onclick=\"design_preview('{$templet_list[$i]}')\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\">";
			echo "</td>\n";
			echo "</tr>\n";
		}
?>
								</table>
								</td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</td>					
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>주문서 화면</span></dt>
						<dd>- 주문서 작성 및 완료 화면 동시 적용 템플릿 입니다.</dd>
                    </dl>
                    <dl><dt><span>개별 디자인은 제공되지 않습니다.</span></dt>
                    </dl>
					</div>

				</td>
			</tr>
			<tr><td height="50"></td></tr>
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