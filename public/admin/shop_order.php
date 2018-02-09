<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/shopdata.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$_ShopData=new ShopData($_ShopInfo);
$_data=$_ShopData->shopdata;

$type=$_POST["type"];
$bank_day=$_POST["bank_day"];
$deli_day=$_POST["deli_day"];
$order_day=$_POST["order_day"];
$order_save=$_POST["order_save"];
$pr_display=$_POST["pr_display"];

$bank_day_text=$_POST["bank_day_text"];
$deli_day_text=$_POST["deli_day_text"];
$order_day_text=$_POST["order_day_text"];

if ($type=="up") {
	if($bank_day=="Y") $bank_day=$bank_day_text;
	if($deli_day=="Y") $deli_day=$deli_day_text;
	if($order_day=="Y") $order_day=$order_day_text;
	
	$sql="update tblshopinfo set bank_day='".$bank_day."', deli_day='".$deli_day."', order_day='".$order_day."', order_save='".$order_save."', pr_display='".$pr_display."'";
	pmysql_query($sql);
	
	$onload="<script>window.onload=function(){alert('주문 정책설정이 완료되었습니다.');location.href='shop_order.php'; }</script>\n";


}

if($_data->bank_day=="N"){
	$bank_day_yn="N";
	$bank_day_text="";
}else{
	$bank_day_yn="Y";
	$bank_day_text=$_data->bank_day;
}

if($_data->deli_day=="N"){
	$deli_day_yn="N";
	$deli_day_text="";
}else{
	$deli_day_yn="Y";
	$deli_day_text=$_data->deli_day;
}

if($_data->order_day=="N"){
	$order_day_yn="N";
	$order_day_text="";
}else{
	$order_day_yn="Y";
	$order_day_text=$_data->order_day;
}

$order_save_yn=$_data->order_save?$_data->order_save:"N";
$pr_display_yn=$_data->pr_display?$_data->pr_display:"N";

$checked["bank_day"][$bank_day_yn]="checked";
$checked["deli_day"][$deli_day_yn]="checked";
$checked["order_day"][$order_day_yn]="checked";
$checked["order_save"][$order_save_yn]="checked";
$checked["pr_display"][$pr_display_yn]="checked";
?>

<?php 
include("header.php");
?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var bank_day=$(":radio[name='bank_day']:checked").val();
	var deli_day=$(":radio[name='deli_day']:checked").val();
	var order_day=$(":radio[name='order_day']:checked").val();


	if(deli_day=="Y"){
		if(isNaN($("#bank_day_text").val())){
			alert('숫자만 입력하시기 바랍니다.');
			$("#bank_day_text").focus();
			return;
		}
		if(parseInt($("#bank_day_text").val())<=0){
			alert('일수는 0일 이상 입력하셔야 합니다.');
			$("#bank_day_text").focus();
			return;
		}
	}
	if(deli_day=="Y"){
		if(isNaN($("#deli_day_text").val())){
			alert('숫자만 입력하시기 바랍니다.');
			$("#deli_day_text").focus();
			return;
		}
		if(parseInt($("#deli_day_text").val())<=0){
			alert('일수는 0일 이상 입력하셔야 합니다.');
			$("#deli_day_text").focus();
			return;
		}
	}
	if(order_day=="Y"){
		if(isNaN($("#order_day_text").val())){
			alert('숫자만 입력하시기 바랍니다.');
			$("#order_day_text").focus();
			return;
		}
		if(parseInt($("#order_day_text").val())<=0){
			alert('일수는 0일 이상 입력하셔야 합니다.');
			$("#order_day_text").focus();
			return;
		}
	}

	$("#type").val("up");
	if (confirm("적용하시겠습니끼?")) {
		$("#form1").submit();
	}
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 운영설정 &gt;<span>주문 정책설정</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 id="form1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type id="type">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">주문 정책설정</div>
				</td>
			</tr>
			
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문 정책설정</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR style="display:none">
					<th><span>무통장 입금 시 설정*</span></th>
					<TD class="td_con1">
						<input type=radio id="bank_time1" name="bank_day" value="Y" <?=$checked["bank_day"]["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=bank_time1>무통장 미 입금 시 <input type="text" name="bank_day_text" id="bank_day_text" value="<?=$bank_day_text?>" style="width:60px;">일 후 '결제취소'로 상태변경</label><br>
						<input type=radio id="bank_time2" name="bank_day" value="N" <?=$checked["bank_day"]["N"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=bank_time2>사용안함</label>
					</TD>
				</TR>
				<TR style="display:none">
					<th><span>자동 배송완료*</span></th>
					<TD class="td_con1">
						<input type=radio id="auto_deli1" name="deli_day" value="Y" <?=$checked["deli_day"]["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=auto_deli1>'배송중'으로 주문상태 변경되면 <input type="text" style="width:60px;" name="deli_day_text" id="deli_day_text" value="<?=$deli_day_text?>">일 후 '배송완료'로 상태변경</label><br>
						<input type=radio id="auto_deli2" name="deli_day" value="N" <?=$checked["deli_day"]["N"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=auto_deli2>사용안함</label>
					</TD>
				</TR>
				<TR>
					<th><span>자동 구매확정*</span></th>
					<TD class="td_con1">
						<input type=radio id="auto_order1" name="order_day" value="Y" <?=$checked["order_day"]["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=auto_order1>'배송중'으로 주문상태 변경되면 <input type="text" style="width:60px;" name="order_day_text" id="order_day_text" value="<?=$order_day_text?>">일 후 '구매확정'로 상태변경</label><br>
						<input type=radio id="auto_order2" name="order_day" value="N" <?=$checked["order_day"]["N"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=auto_order2>사용안함</label>
					</TD>
				</TR>
				<TR>
					<th><span>주문서 데이터 보관 설정*</span></th>
					<TD class="td_con1">
						<input type=radio id="order_data1" name="order_save" value="Y" <?=$checked["order_save"]["Y"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=order_data1>보관기간 5년 초과시 삭제(권장)</label> 
						<input type=radio id="order_data2" name="order_save" value="N" <?=$checked["order_save"]["N"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=order_data2>데이터 삭제 안함</label>
					</TD>
				</TR>
				<TR style="display:none">
					<th><span>상품 품절 시 노출여부*</span></th>
					<TD class="td_con1">
						<input type=radio id="pr_display1" name="pr_display" value="A" <?=$checked["pr_display"]["A"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=pr_display1>리스트/상세 노출</label> 
						<input type=radio id="pr_display2" name="pr_display" value="L" <?=$checked["pr_display"]["L"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=pr_display2>리스트에만 노출</label> 
						<input type=radio id="pr_display3" name="pr_display" value="N" <?=$checked["pr_display"]["N"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=pr_display3>노출안함</label>
					</TD>
				</TR>
				
				</TABLE>
                </div>
				</td>
			</tr>
			
           
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li><b>[정보통신망법 제 29조]에 의거 보관기간 5년 초과한 주문데이터는 개인정보보호차원에서 파기하여야 합니다.</b></li>
							<li><b>주문 데이터 삭제를 선택한 경우</b> 주문과 관련된 모든 정보가 삭제되며, 삭제된 주문서는 검색/조회가 불가능하며 회원 CRM 주문내역에서도 삭제됩니다.</li>
							<li><b>[데이터 삭제 안함]을 선택한 경우</b> 수기로 주문데이터를 삭제하지 않는 한 데이터 활용이 가능하며, <b>이 경우 발생할 수 있는 모든 법적 책임소지 및 처벌은 이용하시는 회사에 있습니다.</b></li>
							<!--<li>전체 구매금액에 따라 회원 등급을 설정 할 경우 <b>최근 5년간 주문 데이터가 합산되어 적용</b>되므로 회원의 주문금액이 기존보다 낮아질 수 있습니다.</li>
							<li>상품 품절 시 [리스트/상세 노출] 선택 시 리스트에서 상세로 이동가능하며, 상세페이지에서 결제는 불가합니다.</li>
							<li>상품 품절 시 [리스트에만 노출] 선택 시 리스트에는 노출되지만 상세페이지로 이동할 수 없습니다.</li>-->
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
						</ul>
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
