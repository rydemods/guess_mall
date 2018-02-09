<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$code=$_POST["code"];
$codes=$_POST["codes"];

$exposed_list_num = $_shopdata->exposed_list;
if(ord($exposed_list_num)==0) $exposed_list_num=",0,2,3,4,5,6,7,19,";

if ($type=="insert" || $type=="delete" || $type=="sequence") {
	if ($type=="insert") {
		$exposed_list_num = $exposed_list_num.$code.",";
		$onload="<script>window.onload=function(){ alert('해당 노출 항목을 추가하였습니다.');}</script>";
	} else if ($type=="delete") {
		$exposed_list_num = str_replace(",{$code},",",",$exposed_list_num);
		$onload="<script>window.onload=function(){ alert('해당 노출 항목을 삭제하였습니다.');}</script>";
	} else if ($type=="sequence") {
		$exposed_list_num=$codes;
		$onload="<script>window.onload=function(){ alert('해당 노출 항목의 순서를 변경하였습니다.');}</script>";
	}
	$sql = "UPDATE tblshopinfo SET exposed_list = '{$exposed_list_num}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
}

$exposed_list_name = array("제조회사","원산지","시중가격","판매가격","적립금","특이사항","수량(삭제불가)","옵션(삭제불가)","상품명","해외 화폐 가격","모델명","출시일","사용자정의스펙1","사용자정의스펙2","사용자정의스펙3","사용자정의스펙4","사용자정의스펙5","브랜드","진열코드","패키지(삭제불가)");

$cnt = count($exposed_list_name);

$exposed_list_num2=trim($exposed_list_num,',');
$ar_exposed_list_num = explode(",",$exposed_list_num2);
$cnt2 = count($ar_exposed_list_num);

if(ord($blanknum)==0) $blanknum=1;
$exposed_list_num3=" ".$exposed_list_num;
while($num = strpos($exposed_list_num3,",O")){
	$tempnum=str_replace(",","",substr($exposed_list_num3,$num+2,2))+1;
	$exposed_list_num3=substr($exposed_list_num3,$num+2);
	if($tempnum>$blanknum) $blanknum=$tempnum;	
}

for($i=1;$i<$blanknum;$i++) $exposed_list_name["O$i"]="공백(셀 빈칸)";

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function SendMode(mode) {
	if (document.form1.outexp.selectedIndex==-1 && mode=="insert") {
		alert("노출 항목에 추가할 항목을 선택하세요.");
		return;
	} else if(document.form1.inexp.selectedIndex==-1 && mode=="delete") {
		alert("노출 항목에서 삭제할 항목을 선택하세요.");
		return;
	}
	if (mode=="insert") {
		if (confirm("노출 항목을 추가하시겠습니까?")) {
			document.form1.type.value=mode;
			document.form1.code.value=document.form1.outexp.options[document.form1.outexp.selectedIndex].value;
			document.form1.submit();
		}
	} else if (mode=="delete"){
		document.form1.code.value=document.form1.inexp.options[document.form1.inexp.selectedIndex].value;
		if (document.form1.code.value!=6 && document.form1.code.value!=7 && document.form1.code.value!=19) {
			if (confirm("노출 항목을 삭제하시겠습니까?")) {
				document.form1.type.value=mode;
				document.form1.submit();
			}
		} else if (document.form1.code.value==6){
			alert("수량은 삭제 불가능합니다.");
			return;
		} else if (document.form1.code.value==7){
			alert("옵션은 삭제 불가능합니다.");
			return;
		} else if (document.form1.code.value==19){
			alert("패키지는 삭제 불가능합니다.");
			return;
		}
	}
}

function move(gbn) {
	change_idx = document.form1.inexp.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 항목을 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 항목은 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.inexp.length-1)) {
		alert("선택하신 항목은 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.inexp.options[idx].value;
	idx_text = document.form1.inexp.options[idx].text;

	document.form1.inexp.options[idx].value = document.form1.inexp.options[change_idx].value;
	document.form1.inexp.options[idx].text = document.form1.inexp.options[change_idx].text;

	document.form1.inexp.options[change_idx].value = idx_value;
	document.form1.inexp.options[change_idx].text = idx_text;

	document.form1.inexp.selectedIndex = idx;
	document.form2.change.value="Y";
}

function MoveSave() {
	if (document.form2.change.value!="Y") {
		alert("순서변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?")) return;
	codes = "";
	for (i=0;i<=(document.form1.inexp.length-1);i++) {
		codes+=","+document.form1.inexp.options[i].value;
	}
	codes+=",";
	document.form2.codes.value = codes;
	document.form2.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 카테고리/상품관리 &gt;<span>상품 스펙 노출관리</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 스펙 노출관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품 상세페이지에서 노출되는 각상품의 상세항목 순서를 변경할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=code>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD><div class="point_title02">상세조건 노출관리</div></TD>
					<TD>&nbsp;</TD>
					<TD><div class="point_title03">현재 노출중인 항목</div></TD>
				</TR>
				<TR>
					<TD bgcolor="#f8f8f8" valign="top" style="padding:8pt;" width="48%">
					<select name=outexp size=17 style="WIDTH:100%" size=17 class="select">
<?php
					for($i=0;$i<$cnt;$i++){
						if(!strpos($exposed_list_num,",{$i},")){
							echo "<option value=\"{$i}\">{$exposed_list_name[$i]}\n";
						}
					}
					echo "<option value=\"O{$blanknum}\">공백(셀 빈칸)\n";
?>
					</select>
					</TD>
					<TD align="center" width="55"><a href="javascript:SendMode('insert');"><img src="images/icon_nero1.gif" border="0"></a><br><br><a href="javascript:SendMode('delete');"><img src="images/icon_nero2.gif" border="0" vspace="10"></a></TD>
					<TD  align="center" bgcolor="#f8f8f8" style="padding:8,8,0,8" width="48%">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD width="100%">
						<select name=inexp size=17 style="WIDTH:100%" class="select">
<?php
						for($i=0;$i<$cnt2;$i++){
							echo "<option value=\"{$ar_exposed_list_num[$i]}\">{$exposed_list_name[$ar_exposed_list_num[$i]]}\n";
						}
?>
						</select>
						</TD>
						<TD noWrap align=middle width=50>
						<table cellpadding="0" cellspacing="0" width="34">
						<TR>
							<TD align=middle><A href="JavaScript:move('up');"><IMG src="images/code_up.gif" align=absMiddle border=0 vspace="2"></A></td>
						</tr>
						<TR>
							<TD align=middle><IMG src="images/code_sort.gif" ></td>
						 </tr>
						<TR>
							<TD align=middle><A href="JavaScript:move('down');"><IMG src="images/code_down.gif" align=absMiddle border=0 vspace="2"></A></td>
						</tr>
						<tr>
							<td height="20"></td>
						</tr>
						<TR>
							<TD align=middle><A href="JavaScript:MoveSave();"><IMG src="images/code_save.gif" align=absMiddle border=0 vspace="2"></A></td>
						</tr>
						</table>
						</TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			</form>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type value="sequence">
			<input type=hidden name=codes>
			<input type=hidden name=change value="N">
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>상품스펙 노출 관리</span></dt>
						<dd>
						- 진열순서 조정 후 [저장하기] 를 클릭해야만 적용됩니다.<br>
						- 상품진열 템플릿 선택시 가격고정형 공동구매를 사용할 경우 상품스펙기능은 지원되지 않습니다.<br>
						- 상품스펙 노출설정을 했어도 해당 스펙에 대한 정보를 입력하지 않으면 출력되지 않습니다.<br>
						- 상품스펙 [삭제하기]는 스펙출력에서 미출력되며 스펙에 입력한 정보는 삭제되지 않습니다.<Br>
						- 공백(셀 빈칸)은 스펙과 스펙사이 구분할때 사용합니다.
						</dd>
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
