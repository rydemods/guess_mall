<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_return1_type=$_POST["up_return1_type"];
$up_return2_type=$_POST["up_return2_type"];
$up_ordercancel=$_POST["up_ordercancel"];
$up_nocancel_msg=$_POST["up_nocancel_msg"];
$up_okcancel_msg=$_POST["up_okcancel_msg"];

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "return1_type		= '{$up_return1_type}', ";
	$sql.= "return2_type		= '{$up_return2_type}', ";
	$sql.= "ordercancel			= '{$up_ordercancel}', ";
	$sql.= "nocancel_msg		= '{$up_nocancel_msg}', ";
	$sql.= "okcancel_msg		= '{$up_okcancel_msg}' ";
	$update = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload = "<script>window.onload=function(){ alert('반품/환불 관련 설정이 완료되었습니다.'); }</script>";
}

$sql = "SELECT return1_type, return2_type, ordercancel, nocancel_msg, okcancel_msg FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$return1_type = $row->return1_type;
	$return2_type = $row->return2_type;
	$ordercancel = $row->ordercancel;
	$nocancel_msg = $row->nocancel_msg;
	$okcancel_msg = $row->okcancel_msg;
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(){
	document.form1.type.value="up";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>상품 반품/환불 기능설정</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 반품/환불 기능설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>교환/반품/환불에 대한 설정을 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">교환/반품 배송비 부담</div>
				</td>
			</tr>
			<tr>
                 <td style="padding-top:3pt; padding-bottom:3pt;">                        
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 쇼핑몰의 이용안내페이지에서 <b>교환/반품/환불</b> 안내 부분에 설정한 내용이 표기됩니다.</li>
                                <li>2) 이용안내를 개별 디자인할 경우는 제외됩니다.</li>
                            </ul>
                        </div>
                </td>
            </tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>고객 변심의 경우</span></th>
					<TD class="td_con1">고객의 변심에 의한 교환 및 반품인 경우에 배송비는 <select name="up_return2_type" class="select" style="width:100px">
						<option value="2" <?php if ($return2_type=="2") echo "selected"; ?>>소비자</option>
						<option value="1" <?php if ($return2_type=="1") echo "selected"; ?>>판매자</option>
						</select> 부담입니다.</TD>
				</TR>
				<TR>
					<th><span>상품 이상의 경우</span></th>
					<TD class="td_con1">상품의 이상에 의한 교환 및 반품인 경우에 배송비는 <select name="up_return1_type" class="select" style="width:100px">
						<option value="2" <?php if ($return1_type=="2") echo "selected"; ?>>소비자</option>
						<option value="1" <?php if ($return1_type=="1") echo "selected"; ?>>판매자</option>
						</select> 부담입니다.</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">자동 주문취소 설정</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">                        
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 구매고객이 주문내역확인에서 주문취소가 가능한 단계를 설정합니다.</li>
                                <li>2) 설정 단계 이후에는 주문서 상세보기 하단에 <b>[주문취소]</b>라는 메뉴가 나타나지 않습니다.</li>
                            </ul>
                        </div>
                </td>                
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>주문취소 가능 단계</span></th>
					<TD class="td_con1">고객의 주문취소는 쇼핑몰에서 <select name="up_ordercancel" class="select">
						<option value="0" <?php if ($ordercancel=="0") echo "selected"; ?>>주문 배송 완료</option>
						<option value="2" <?php if ($ordercancel=="2") echo "selected"; ?>>주문 발송 준비</option>
						<option value="1" <?php if ($ordercancel=="1") echo "selected"; ?>>주문 결제 완료</option>
						</select> 전에만 가능합니다.</TD>
				</TR>
				<TR>
					<th><span>주문취소시 고객메세지 설정</span></th>
					<TD class="td_con1">
                        <div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="108">주문취소 완료시</td>
                            <td width="480"><input type=text name=up_okcancel_msg value="<?=$okcancel_msg?>" size=65 maxlength=250 onKeyDown="chkFieldMaxLen(250)" style="width:100%" class="input"></td>
                        </tr>
                        <tr>
                            <td width="108">주문취소 불가 단계</td>
                            <td width="480"><input type=text name=up_nocancel_msg value="<?=$nocancel_msg?>" size=65 maxlength=250 onKeyDown="chkFieldMaxLen(250)" style="width:100%" class="input"></td>
                        </tr>
                        </table>
                        </div>
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
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
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
							<dt><span><font class="font_orange">주문리스트에 결제상태 및 처리단계의 확인 기준</b>으로 하며, <b>이미 발송한 경우는 자동주문취소 안됩니다.</b></font></span></dt>
							
						</dl>
						<dl>
							<dt><span>미입력시 기본 메시지 내용</span></dt>
							<dd><b>①발송전</b><br>
	&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" border="0">정상취소&nbsp;: 정상적으로 취소되었습니다. 주문변경 및 환불은 별도 안내해 드리겠습니다<br>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" border="0">취소불가&nbsp;: 주문변경 및 취소, 환불은 쇼핑몰로 별로 문의 바랍니다.<br>
	<b>②발송준비전</b><br>
	&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" border="0">정상취소&nbsp;: 정상적으로 취소되었습니다. 주문변경 및 환불은 별도 안내해 드리겠습니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" border="0">취소불가&nbsp;: 주문변경 및 취소, 환불은 쇼핑몰로 별로 문의 바랍니다.&nbsp;<br>
	<b>③입금전</b><br>
	&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" border="0">정상취소&nbsp;: 정상적으로 취소되었습니다. 주문변경은 별도 안내해 드리겠습니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" border="0">취소불가&nbsp;: 주문변경 및 취소는 쇼핑몰로 별로 문의 바랍니다.
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
