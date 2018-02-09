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

$imagepath=$Dir.DataDir;

$mode=$_POST["mode"];
$deli_company=$_POST["deli_company"];
$upfile=$_FILES["upfile"];

if($mode=="upload" && ord($upfile['name']) && $upfile['size']>0) {
	$ext = strtolower(pathinfo($upfile['name'],PATHINFO_EXTENSION));
	if($ext=="csv") {
		$filename="excelupfile.txt";
//		copy($upfile['tmp_name'],$imagepath.$filename);
		unlink($imagepath.$filename);
		move_uploaded_file($upfile['tmp_name'],$imagepath.$filename);
		chmod($imagepath.$filename,0664);
		$onload="<script>window.onload=function(){ alert(\"엑셀파일 등록이 완료되었습니다.\\n\\n등록파일 배송처리를 하시기 바랍니다.\"); }</script>";
	} else {
		$onload="<script>window.onload=function(){ alert(\"파일형식이 잘못되어 업로드가 실패하였습니다.\\n\\n등록 가능한 파일은 엑셀(CSV) 파일만 등록 가능합니다.\"); }</script>";
	}
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>

<script language="JavaScript">

function CheckForm(form) {
	if(form.deli_company.value.length==0) {
		alert("택배업체를 선택하세요.");
		form.deli_company.focus();
		return;
	}
	if(form.upfile.value.length==0) {
		alert("등록할 엑셀(CSV) 파일을 선택하세요.");
		form.upfile.focus();
		return;
	}
	form.mode.value="upload";
	form.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function checkTransFrom(){
	var cnt = $(".order_idx:checked").length
	if(!cnt){
		alert("선택된 항목이 없습니다.");
		return false;
	}else{
		return true;
	}
}
function allChecked(el){
	$(".order_idx").prop("checked",el.checked);
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>주문리스트 일괄배송 관리</span></p></div></div>

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
			<td width="20" valign="top"><img src="images/space01.gif" height="1" border="0" width="20"></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">주문리스트 일괄배송 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>다수 주문건의 배송정보를 엑셀파일로 만들어 주문리스트에 일괄 반영하는 기능입니다.</span></div>
				</td>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배송정보 엑셀파일(CSV)등록 및 일괄 처리</div>
				</td>
			</tr>
			<form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
				</TR>
				<TR>
					<th><span>택배업체 선택</span></th>
					<TD><select name="deli_company" class="select" style="width:130px">
					<option value="">택배업체 선택</option>
<?php
			$sql = "SELECT code, company_name FROM tbldelicompany ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				echo "<option value=\"{$row->code}\">{$row->company_name}</option>\n";
			}
			pmysql_free_result($result);
?>
					</select></td>
				</TR>
				<TR>
					<th><span>엑셀파일 등록</span></th>
					<TD class="td_con1">
					<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly">
					<div class="file_input_div">
					<input type="button" value="찾아보기" class="file_input_button" />
					<input type=file name=upfile style="width:60%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ><br />
					</div>
					<span class="font_orange">＊엑셀(CSV) 파일만 등록 가능합니다.</span></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><p><a href="javascript:CheckForm(document.form1);"><img src="images/btn_fileup.gif" border="0"></a></p></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>

	<?php if($mode=="upload"){?>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_csvdelivery_stitle.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<form name="tran_from" action="order_csvdelivery.process.php" method="post" onsubmit="return checkTransFrom()">
				<input type="hidden" name="mode" value="order_transe">
				<input type="hidden" name="returnUrl" value="order_csvdelivery.php">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=50></col>
				<col width=140></col>
				<col width=140></col>
				<col width=140></col>
				<col width=></col>
				<TR>
					<TD height="1" colspan="5" bgcolor="#B9B9B9"></TD>
				</TR>
				<TR align="center">
					<TD class="table_cell"><input type="checkbox" name="allchecked" value="all" onclick="allChecked(this);"> 선택</TD>
					<TD class="table_cell">번호</TD>
					<TD class="table_cell1">주문일자</TD>
					<TD class="table_cell1">주문자</TD>
					<TD class="table_cell1">송장번호</TD>
					<TD class="table_cell1">상태</TD>
				</TR>
				<TR>
					<TD height="1" colspan="5" bgcolor="#EDEDED"></TD>
				</TR>
<?php
			$filepath=$imagepath.$filename;
			$fp=@fopen($filepath, "r");
			$i=1;
			while(!feof($fp)) {
				$buffer=fgets($fp,4096);
				if(strlen($buffer)>3) {
					$field=explode(",",$buffer);
					$date=substr($field[0],0,4)."/".substr($field[0],4,2)."/".substr($field[0],6,2)." (".substr($field[0],8,2).":".substr($field[0],10,2).")";
					echo "<tr align=center>\n";
					echo "	<td class=td_con2>";
					echo "		<input type='checkbox' name='order_idx[]' class='order_idx' value='{$i}'>";
					echo "		<input type='hidden' name='ordercode[".$i."]' value='".trim($field[0])."'>";
					echo "		<input type='hidden' name='deli_com[".$i."]' value='{$deli_company}'>";
					echo "		<input type='hidden' name='deli_name[".$i."]' value='{$field[1]}'>";
					echo "		<input type='hidden' name='deli_num[".$i."]' value='{$field[2]}'>";
					echo " </td>\n";
					echo "	<td class=td_con2>{$i}</td>\n";
					echo "	<td class=td_con1>{$date}</td>\n";
					echo "	<td class=td_con1>{$field[1]}</td>\n";
					echo "	<td class=td_con1>{$field[2]}</td>\n";
					echo "	<td class=td_con1><iframe src=\"order_csvdelivery.process.php?type=init&ordercode=".trim($field[0])."&deli_com={$deli_company}&deli_name=".urlencode($deli_name)."&deli_num={$field[2]}\" style='width=100%;height=28px;font-size=15px;border:0 solid #FFFFFF;' scrolling='no' frameborder='NO'></iframe></td>\n";
					echo "</tr>\n";
					echo "<tr><TD height=\"1\" colspan=\"5\" bgcolor=\"#EDEDED\"></TD></tr>\n";
					$i++;
				}
			}
?>
				<TR>
					<TD height="1" colspan="5" bgcolor="#B9B9B9"></TD>
				</TR>
				</table>
				<div style="margin-top:15px;"><input type="submit" value="선택 일괄배송처리"></div>
				</form>
				</td>
			</tr>
		<?php }?>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>주문리스트 일괄배송 관리</span></dt>
						<dd>
							- 주문 처리시 각 주문 별로 배송처리를 하지 않고, 다수의 주문을 선배송 후 배송정보를 엑셀파일(CSV)로 작성하여 일괄 적용하는 기능입니다.
						</dd>
					</dl>
					<dl>
						<dt><span>주문리스트 일괄배송 방법</span></dt>
						<dd>
							- ① 아래의 형식을 참고로 일괄배송 가능한 엑셀파일(확장자 CSV)을 작성합니다.<br />
							<b>&nbsp;&nbsp;</b><span class="font_orange">------------ 일괄배송 엑셀(CSV) 형식 ------------</span><br>
							<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_blue">주문번호,주문자,송장번호</span><br>
							<b>&nbsp;&nbsp;</b><span class="font_orange">--------------------------------------------------</span><br>
							<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20070307154752877166,홍길동,11223344<br>
							<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20070307160323501849,홍길동,55667788<br>
							<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20070307160929925273,홍길동,99001122<br>
							<b>&nbsp;&nbsp;</b><span class="font_orange">--------------------------------------------------</span>
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
