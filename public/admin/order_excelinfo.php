<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$excel_ok=$_shopdata->excel_ok;
$excel_info=$_shopdata->excel_info;

$mode=$_POST["mode"];
$etccode=$_POST["etccode"];
$up_excel_ok=$_POST["up_excel_ok"];
$codes=$_POST["codes"];
$change=$_POST["change"];

if($mode=="insert" || $mode=="delete" || $mode=="sequence") {
	if($mode=="insert" && ord($etccode)) {
		$excel_info=$excel_info.$etccode.",";
		$onload="<script>window.onload=function(){ alert(\"선택하신 항목을 다운되는 주문서 항목에 추가하였습니다.\"); }</script>";
	} else if($mode=="delete" && ord($etccode)) {
		$excel_info=str_replace(",{$etccode},",",",$excel_info);
		$onload="<script>window.onload=function(){ alert(\"선택하신 항목을 다운되는 주문서 항목에서 삭제하였습니다.\"); }</script>";
	} else if($mode=="sequence") {
		$excel_info=$codes;
		$onload="<script>window.onload=function(){ alert(\"다운되는 주문서 항목 순서를 변경하였습니다.\"); }</script>";
	}
	if(preg_match("/,24,/"," ".$excel_info)) {
		$pattern = array(",21,",",22,",",23,",",25,",",26,");
		$replacement = array(",",",",",",",",",");
		$excel_info=str_replace($pattern,$replacement,$excel_info); 
	}
	$sql = "UPDATE tblshopinfo SET excel_info='{$excel_info}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
} else if($mode=="exceltype" && ord($up_excel_ok)) {
	$sql = "UPDATE tblshopinfo SET excel_ok='{$up_excel_ok}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$excel_ok=$up_excel_ok;
	$onload="<script>window.onload=function(){ alert(\"주문서 출력형식을 변경하였습니다.\"); }</script>";
}

$excel_name = array(
"일자",
"주문자",
"주문자 전화(XXXXXXXX)",
"주문자 전화(XX-XXXX-XXXX)",
"이메일",
"주문ID/주문번호",
"결제방법",
"결제상태",
"결제방법(상태)",
"주문금액",
"처리단계",
"받는사람",
"전화번호 비상전화",
"전화번호(XXXXXXXX)",
"비상전화(XXXXXXXX)",
"전화번호(XX-XXXX-XXXX)",
"비상전화(XX-XXXX-XXXX)",
"우편번호(XXXXXX)",
"우편번호(XXX-XXX)",
"주소",
"전달사항",
"상품명",
"옵션(특징포함)",
"갯수",
"상품명1-갯수-옵션 ^ 상품명2-갯수-옵션",
"상품가격",
"상품 적립금",
"배송료",
"사용적립금",
"입금일",
"배송일",
"주문관련메모(관리자)",
"고객알리미",
"상품명1-갯수-옵션^상품명2-갯수-옵션",
"송장번호",
"거래번호",
"상품코드",
"은행계좌(카드내역)",
"옵션",
"특징",
"상품명(태그제거안함)",
"전달사항(태그제거안함)",
"일자(시분초 표시)",
"상품별 처리여부",
"상품별 주문메세지",
"상품별 배송일",
"진열코드",
"거래처정보",
"사은품",
"번호"
);

$cnt = count($excel_name);
$excel_info2=trim($excel_info,',');
$arexcel_info = explode(",",$excel_info2);
$cnt2 = count($arexcel_info);

if(ord($blank_info)==0) $blank_info=1;
$excel_info3=" ".$excel_info;
while($num = strpos($excel_info3,",O")) {
	$temp_info=str_replace(",","",substr($excel_info3,$num+2,2))+1;
	$excel_info3=substr($excel_info3,$num+2);
	if($temp_info>$blank_info) $blank_info=$temp_info;
}

for($i=1;$i<$blank_info;$i++) $excel_name["O$i"]="공백(셀 빈칸)";

?>
<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(form) {
	if(form.up_excel_ok[0].checked==false && form.up_excel_ok[1].checked==false) {
		alert("주문서 출력 형식을 선택하세요.");
		form.up_excel_ok[1].focus();
		return;
	}
	form.mode.value="exceltype";
	form.submit();
}

function SendMode(mode) {
	if (document.form1.noest.selectedIndex==-1 && mode=="insert") {
		alert("다운 가능한 주문서 항목을 선택하세요.");
		return;
	} else if(document.form1.est.selectedIndex==-1 && mode=="delete") {
		alert("다운되는 주문서 항목을 선택하세요.");
		return;
	}
	if (mode=="insert") {
		if (confirm("선택된 주문서 항목을 다운되는 주문서 항목에 추가하시겠습니까?")) {
			document.form1.mode.value=mode;
			document.form1.etccode.value=document.form1.noest.options[document.form1.noest.selectedIndex].value;
			document.form1.submit();
		}
	} else if (mode=="delete"){
		document.form1.etccode.value=document.form1.est.options[document.form1.est.selectedIndex].value;
		if (confirm("선택된 주문서 항목을 삭제하시겠습니까?")) {
			document.form1.mode.value=mode;
			document.form1.submit();
		}
	}
}

function move(gbn) {
	change_idx = document.form1.est.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 주문서 항목을 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 주문서 항목은 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.est.length-1)) {
		alert("선택하신 주문서 항목은 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.est.options[idx].value;
	idx_text = document.form1.est.options[idx].text;

	document.form1.est.options[idx].value = document.form1.est.options[change_idx].value;
	document.form1.est.options[idx].text = document.form1.est.options[change_idx].text;

	document.form1.est.options[change_idx].value = idx_value;
	document.form1.est.options[change_idx].text = idx_text;

	document.form1.est.selectedIndex = idx;
	document.form2.change.value="Y";
}

function MoveSave() {
	if (document.form2.change.value!="Y") {
		alert("순서변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?")) return;
	codes = "";
	for (i=0;i<=(document.form1.est.length-1);i++) {
		codes+=","+document.form1.est.options[i].value;
	}
	document.form2.codes.value = codes+",";
	document.form2.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 주문조회 및 배송관리 &gt;<span>주문리스트 엑셀파일 관리</span></p></div></div>
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
			<?php include("menu_order.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">주문리스트 엑셀파일 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>주문리스트를 엑셀파일로 다운로드할 경우, 주문리스트의 각 항목 및 배열순서를 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문리스트 항목 및 배열순서 관리</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=etccode>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

				<TR>
					<TD><div class="point_title02">다운로드 가능한 주문리스트 항목</div></TD>
					<TD>&nbsp;</TD>
					<TD><div class="point_title03">다운로드 되는 주문리스트 항목</span></div></TD>
				</TR>
				<TR>
					<TD bgcolor="#f8f8f8" align="center" valign="top" style="padding:8pt;"><select name=noest size=17 style="width:100%;" class="select">
<?php
					for($i=0;$i<$cnt;$i++){
						if(!preg_match("/,{$i},/",$excel_info)){
							echo "<option value=\"{$i}\">{$excel_name[$i]}\n";
						}
					}
					echo "<option value=\"O{$blank_info}\">공백(셀 빈칸)\n";
?>
					</select></TD>
					<TD width="55" align="center"><a href="javascript:SendMode('insert');"><img src="images/icon_nero1.gif" border="0" vspace="2"></a><br><br><a href="javascript:SendMode('delete');"><img src="images/icon_nero2.gif" border="0" vspace="2"></a></TD>
					<TD  align="center" valign="top" bgcolor="#f8f8f8">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD style="padding-top:5px;">
						<table cellpadding="5" cellspacing="0" width="100%" bgcolor="#0c71c6">
						<tr>
							<td>
							<select name=est size=17 class="select" style="width:100%;" >
<?php
							for($i=0;$i<$cnt2;$i++){
								echo "<option value=\"{$arexcel_info[$i]}\">{$excel_name[$arexcel_info[$i]]}\n";
							}
?>
							</select>
							</td>
						</tr>
						</table>
						</TD>
						<TD noWrap align=middle width=50 align="center"><a href="javascript:move('up');"><img src="images/code_up.gif" border="0" vspace="0"></a><br><img src="images/code_sort.gif" border="0" vspace="2"><br><a href="javascript:move('down');"><img src="images/code_down.gif" border="0" vspace="0"></a><br><br><a href="javascript:MoveSave();"><img src="images/code_save.gif" border="0" vspace="2"></a></TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="40">* 복수상품 주문건이나 주문내용이 1열 이상인 경우, 공통 항목을 반복 출력합니다.&nbsp;&nbsp;<input type=radio name=up_excel_ok value="Y" <?php if($excel_ok=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none">예&nbsp;&nbsp;<input type=radio name=up_excel_ok value="N" <?php if($excel_ok=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none">아니요</td>
			</tr>

			<tr>
				<td align="center" height="10"></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm(document.form1);"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="sequence">
			<input type=hidden name=codes>
			<input type=hidden name=change value="N">
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>주문리스트 엑셀파일 관리</span></dt>
							<dd>
								- 주문리스트 엑셀 백업시 원하는 타입으로 각 항목 및 배열순서를 조정한 후 [저장하기] 버튼을 눌러 적용합니다.<br>
								- 주문리스트 항목중 [상품명1-갯수-옵션^상품명2-갯수-옵션] 항목과 [상품명], [옵션], [갯수], [가격] 항목은 동시 적용이 불가능합니다.<br>
								- 복수상품 주문건이나 주문내용이 1열 이상인 경우, 열마다 동일한 항목을 반복 출력할지, 공란으로 출력할지 설정합니다.
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
