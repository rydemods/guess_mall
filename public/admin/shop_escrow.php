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

$escrow_id=$_shopdata->escrow_id;
$escrow_info=$_shopdata->escrow_info;

$mode=$_POST["mode"];
if($mode=="update") {
	$temp_escrow="";
	$escrowcash_y=$_POST["escrowcash_y"];
	$escrowcash_a=$_POST["escrowcash_a"];
	$onlycard=$_POST["onlycard"];
	$nopayment=$_POST["nopayment"];
	$onlycash=$_POST["onlycash"];
	$escrow_percent=$_POST["escrow_percent"];
	$percent=$_POST["percent"];
	$escrow_limit=$_POST["escrow_limit"];

	if($escrowcash_y=="Y") $temp_escrow="escrowcash=Y|";
	else if($escrowcash_a=="A") $temp_escrow="escrowcash=A|";
	else if($onlycard=="Y") $temp_escrow="onlycard=Y|";
	else if($nopayment=="Y") $temp_escrow="nopayment=Y|";
	else if($onlycash=="Y") $temp_escrow="onlycash=Y|";

	if($escrow_limit>0) $temp_escrow.="escrow_limit={$escrow_limit}|";
	if($percent>0) $temp_escrow.="percent={$percent}|";

	$sql = "UPDATE tblshopinfo SET escrow_info='{$temp_escrow}' ";
	pmysql_query($sql,get_db_conn());
	$escrow_info=$temp_escrow;
	DeleteCache("tblshopinfo.cache");
	$onload = "<script>window.onload=function(){ alert('에스크로 결제관련 설정이 완료되었습니다.'); }</script>";
}

$escrow_info = GetEscrowType($escrow_info);

$onlycard=$escrow_info["onlycard"];
$onlycash=$escrow_info["onlycash"];
$nopayment=$escrow_info["nopayment"];
if($escrow_info["escrowcash"]=="Y") $escrowcash="Y";
else if($escrow_info["escrowcash"]=="A") $escrowcash="A";
$escrow_limit=$escrow_info["escrow_limit"];
if(ord($escrow_limit)==0) $escrow_limit=100000;

if(ord($escrow_id) && ($escrowcash=="Y" || $escrowcash=="A")) {
	$escrow="Y";
} else {
	$escrow="N";
	$escrowcash="";
	$escrow_info["escrowcash"]="";
	if($onlycash!="Y" && (ord($onlycard)==0 && ord($nopayment)==0)) $onlycash="Y";
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	<?
	//if(ord($escrow_id)) {?>
	if(document.form1.escrow_limit.value.length==0) {
		alert("결제대금 예치제(에스크로) 기준 금액을 입력해주세요.");
		document.form1.escrow_limit.focus();
		return;
	}

	if(isNaN(document.form1.percent.value)) {
		alert("에스크로 수수료는 소수점 포함한 숫자만 입력하세요.");
		document.form1.percent.focus();
		return;
	}

	if(parseInt(document.form1.percent.value)>10){
		alert("에스크로 수수료는 최대 10%까지 입력이 가능합니다.");
		document.form1.percent.focus();
		return;
	}

	if(document.form1.percent.value.length>0) {
		if(confirm("에스크로 수수료가 부과됩니다.\n\n정상적인 거래방법은 아니지만 설정을 변경하시겠습니까?")) {
			document.form1.mode.value="update";
			document.form1.submit();
		} else {
			return;
		}
	}
	<?//}?>

	if(confirm("에스크로 결제관련 설정을 변경하시겠습니까?")) {
		document.form1.mode.value="update";
		document.form1.submit();
	}
}

function change_escrow(type) {
	form=document.form1;
	<?//if(ord($escrow_id)) {?>
	if(type==1) {
		form.escrowcash_y.disabled=false;
		form.escrowcash_a.disabled=false;
		form.escrowcash_y.checked=true;
		form.escrowcash_a.checked=false;

		form.onlycard.checked=false;
		form.nopayment.checked=false;
		form.onlycash.checked=false;
		form.onlycard.disabled=true;
		form.nopayment.disabled=true;
		form.onlycash.disabled=true;
	}
	if(type==2) {
		form.escrowcash_y.checked=false;
		form.escrowcash_a.checked=true;
	}
	<?//}?>
	if(type==3) {
		<?//if(ord($escrow_id)) {?>
		form.escrowcash_y.checked=false;
		form.escrowcash_a.checked=false;
		form.escrowcash_y.disabled=true;
		form.escrowcash_a.disabled=true;
		<?//}?>
		form.onlycard.checked=true;
		form.nopayment.checked=false;
		form.onlycash.checked=false;
		form.onlycard.disabled=false;
		form.nopayment.disabled=false;
		form.onlycash.disabled=false;
	}
	if(type==4) {
		form.onlycard.checked=false;
		form.nopayment.checked=true;
		form.onlycash.checked=false;
	}
	if(type==5) {
		form.onlycard.checked=false;
		form.nopayment.checked=false;
		form.onlycash.checked=true;
	}
}

<?//if(ord($escrow_id)) {?>
function change_percent(type) {
	if(type==1) {
		document.form1.percent.value="";
		document.form1.percent.disabled=true;
		document.form1.percent.style.background="#f0f0f0";
	} else if(type==2) {
		document.form1.percent.disabled=false;
		document.form1.percent.style.background="";
		document.form1.percent.focus();
	}
}
<?//}?>
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>에스크로 결제관련 설정</span></p></div></div>

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
					<div class="title_depth3">에스크로 결제관련 설정</div>
				</td>
			</tr>
            <tr><td height="20"></td></tr>
            <tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">                        
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 쇼핑몰의 결제대금 예치제(에스크로)의 조건 설정을 하실 수 있습니다.</li>
                            <li>2) 에스크로 가입시 가입비 및 수수료는 에스크로 서비스 회사마다 다릅니다.</li>
                        </ul>
                    </div>                        
                </td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">결제대금 예치제(에스크로)선택 유무 및 조건 설정</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<tr>
				<td>
                <div class="table_style01">
				<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<th><span style="width:300">결제대금 예치제(에스크로) 기준 금액</span></th>
					<td class="td_con1">
                    	<div class="table_none">
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <TR>
                            <TD class=linebottomleft style="padding-left:10px;">
                            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                            <TR>
                                <TD><input type=text name=escrow_limit size=10 maxlength=10 onkeyup="return strnumkeyup(this);" style="PADDING-RIGHT: 5px; FONT-SIZE: 9pt; BACKGROUND: #f0f0f0; TEXT-ALIGN: right" value="<?=$escrow_limit?>">원 <span class="font_orange">* 공정거래법 시행령에 의거 지정된 금액을 입력하시면 됩니다.</span></TD>
                            </TR>
                            </TABLE>
                            </TD>
                        </TR>
                        </TABLE>
                    	</div>
					</td>
				</tr>
				</table>
                <div class="table_none">
                <table height="5" border="0"><tr><td>  </td></tr></table>
                </div>                
				<table cellpadding="0" cellspacing="0" width="100%">                
				<TR>
					<th><span style="width:300">결제대금 예치제(에스크로) 적용여부</span></th>
					<td class="td_con1">
                    <div class="table_none">
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <TR height=30>
                            <TD><input type=radio id="idx_escrow_y" name=escrow value="Y" <?php if($escrow=="Y")echo"checked";?> onclick="change_escrow(1)"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_escrow_y><span class="font_orange"><b>결제대금 예치제(에스크로)를 적용</B></label></TD>
                        </TR>
                        <TR>
                            <TD class=lineleft style="padding-left:20px;" align=middle>
                            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                            <TR>
                                <TD><input type=checkbox id="idx_escrowcash_y" name=escrowcash_y value="Y" <?php if($escrowcash=="Y")echo"checked";?> <?php if($escrow!="Y")echo"disabled";?> onclick="change_escrow(1)"> <label style='cursor:hand;TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_escrowcash_y><?=number_format($escrow_limit)?>원 <span class="font_orange"><b>이상(↑)</b></span> 결제시에만 에스크로 결제 선택 가능.</label></TD>
                            </TR>
                            <TR>
                                <TD><input type=checkbox id="idx_escrowcash_a" name=escrowcash_a value="A" <?php if($escrowcash=="A")echo"checked";?> <?php if($escrow!="Y")echo"disabled";?> onclick="change_escrow(2)"> <label style='cursor:hand;TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_escrowcash_a><?=number_format($escrow_limit)?>원 <span class="font_blue"><b>미만(↓)</b></span> 결제시에도 에스크로 결제 선택 가능.</label></td>
                            </TR>
                            </TABLE>
                            </TD>
                        </TR>
                        <TR height=30>
                            <TD><input type=radio id="idx_escrow_n" name=escrow value="N" <?php if($escrow=="N")echo"checked";?> onclick="change_escrow(3)"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_escrow_n><span class="font_orange"><b>결제대금 예치제(에스크로)를 <font color=black>미적용(법적 책임 발생)</b></label></TD>
                        </TR>
                        <TR>
                            <TD class=lineleft style="padding-left:20px;" align=middle>
                                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                                <TR>
                                    <TD><input type=checkbox id="idx_onlycard" name=onlycard value="Y" <?php if($onlycard=="Y")echo"checked";?> <?php if($escrow!="N")echo"disabled";?> onclick="change_escrow(3)"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_onlycard><?=number_format($escrow_limit)?>원이상(↑) 결제시 카드결제만 가능.</label></TD>
                                </TR>
                                <TR>
                                    <TD><input type=checkbox id="idx_nopayment" name=nopayment value="Y" <?php if($nopayment=="Y")echo"checked";?> <?php if($escrow!="N")echo"disabled";?> onclick="change_escrow(4)"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_nopayment><?=number_format($escrow_limit)?>원이상(↑) 결제시 에스크로만 제외하고 카드결제를 포함하여 모든 결제 가능.</label></TD>
                                </TR>
                                <TR>
                                    <TD><input type=checkbox id="idx_onlycash" name=onlycash value="Y" <?php if($onlycash=="Y")echo"checked";?> <?php if($escrow!="N")echo"disabled";?> onclick="change_escrow(5)"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_onlycash>금액에 상관없이 에스크로만 제외하고 카드결제를 포함하여 모든 결제 가능.</label></TD>
                                </TR>
                                </TABLE>
                            </TD>
                       	</TR>
                        </TABLE>
                        </div>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">결제대금 예치제(에스크로)수수료 부과 기능</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<th><span>에스크로에 수수료 부과</span></th>
					<td class="td_con1">
                        <div class="table_none">
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <TR>
                            <TD class=linebottomleft style="padding-left:10px;">
                            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                            <TR>
                                <TD><p><input type=radio id="idx_percent0" name="escrow_percent" value=0 <?php if($escrow_info["percent"]<=0)echo"checked";?> onclick="change_percent(1)"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_percent0>에스크로 결제시, 총 결제금액에서 추가부과 없음.</label></p></TD>
                            </TR>
                            <TR>
                                <TD><p><input type=radio id="idx_percent1" name="escrow_percent" value=1 <?php if($escrow_info["percent"]>0)echo"checked";?> onclick="change_percent(2)"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_percent1>에스크로 결제시, 총 결제금액에서 <input type=text name=percent size=5 maxlength=3 style="PADDING-RIGHT: 5px; FONT-SIZE: 9pt; BACKGROUND: #f0f0f0; TEXT-ALIGN: right" value="<?=$escrow_info["percent"]?>">% 만큼, 고객에게 더 <font color=red>부과</font>합니다.</label><br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 법적 책임 발생할 수 있습니다.</span></p><script>change_percent(<?=($escrow_info["percent"]<=0?"1":"2")?>);</script></TD>
                            </TR>
                            </TABLE>
                            </TD>
                        </TR>
                        </TABLE>
                        </div>
					</td>
				</tr>
				</table>
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
							<dt><span>에스크로 결제 수단 안내</span></dt>
							<dd>- 에스크로 결제는 가상계좌를 사용합니다.(일반 가상계좌와 동일하나 예치제 적용하는 것만 다름)<br>
- 에스크로 결제의 수수료는 가상계좌수수료+에스크로 결제 수수수가 부과됩니다.(에스크로 서비스 회사마다 다름)<Br>
- 가상계좌 외에 추가 결제수단(실시간 계좌이체, 신용카드결제, 핸드폰 결제 등) 중 적용 가능한 결제수단만 지원됩니다.
							</dd>
						</dl>
						<dl>
							<dt><span>에스크로 면제 거래</span></dt>
							<dd>- 신용카드로 구매하는 거래.<br>
- 배송이 필요하지 않은 재화 등을 구매하는 거래.(컨텐츠 등)
							</dd>
						</dl>
						<dl>
							<dt><span>에스크로 결제의 정산</span></dt>
							<dd>- 상품배송 -> 에스크로결제 서비스회사에 배송내용을 전달 -> 에스크로서비스회사에서 고객에게 구매확인 요청 -><br>
						<b>&nbsp;&nbsp;</b><span class="font_blue"><b>고객이 구매확인을</b>&nbsp;&nbsp;<b>&nbsp;&nbsp;한 경우</b></span> -> 확인일로부터 2일후 정산<br>
						<b>&nbsp;&nbsp;</b><span class="font_orange"><b>고객이 구매확인을&nbsp;안한 경우</b></span> -> 배송일로부터  5일후 상점에 자동 정산(구매자에게는 자동구매확인을 통보함)
							</dd>
						</dl>
						<dl>
							<dt><span>에스크로 가입시 가입비, 정산일과 수수료는 에스크로 서비스 회사마다 다릅니다.</span></dt>
							
						</dl>
						<dl>
							<dt><span>에스크로 서비스 회사와 가입안내는 회사홈페이지를 참조해주세요.(에스크로 서비스회사는 적용 가능한 회사만 지원됩니다)</span></dt>
					
							
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
