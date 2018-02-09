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
$up_newbank_account=$_POST["up_newbank_account"];
$up_oldbank_account=$_POST["up_oldbank_account"];
$up_payment_type=$_POST["up_payment_type"];
$up_card_payfee=$_POST["up_card_payfee"];
if( is_null($up_card_payfee) ) $up_card_payfee = 0;
$up_bank_percent=$_POST["up_bank_percent"];
$up_card_splittype=$_POST["up_card_splittype"];
$up_card_splitmonth=$_POST["up_card_splitmonth"];
$up_card_splitprice=$_POST["up_card_splitprice"];
if( is_null($up_card_splitprice) ) $up_card_splitprice = 0;
$up_bankmess=$_POST["up_bankmess"];
$up_cardmess=$_POST["up_cardmess"];
$up_saletype=$_POST["up_saletype"];
$up_bank_miniprice=$_POST["up_bank_miniprice"];
if( is_null($up_bank_miniprice) ) $up_bank_miniprice = 0;
$up_card_miniprice=$_POST["up_card_miniprice"];
if( is_null($up_card_miniprice) ) $up_card_miniprice = 0;
$price_dc_10=$_POST["up_price_dc_10"];
$price_dc_30=$_POST["up_price_dc_30"];
$price_dc_50=$_POST["up_price_dc_50"];

// 현금할인율
if ($up_saletype=="-" && $up_bank_percent!=0) $up_bank_percent+=50;
if ($up_bank_percent!=0) $up_card_payfee = "-".$up_bank_percent;


if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "bank_miniprice			= '{$up_bank_miniprice}', ";
	$sql.= "card_miniprice			= '{$up_card_miniprice}', ";
	$sql.= "payment_type		= '{$up_payment_type}', ";
	$sql.= "card_payfee		= '{$up_card_payfee}', ";
	$sql.= "bank_account		= '{$up_oldbank_account}={$up_bankmess}={$up_cardmess}', ";
	$sql.= "card_splittype		= '{$up_card_splittype}', ";
	$sql.= "card_splitmonth		= '{$up_card_splitmonth}', ";
	$sql.= "card_splitprice		= '{$up_card_splitprice}'";
	/*
	$sql.= "price_dc_10		= '{$price_dc_10}', ";
	$sql.= "price_dc_30		= '{$price_dc_30}', ";
	$sql.= "price_dc_50		= '{$price_dc_50}' ";
	*/
	$result = pmysql_query($sql,get_db_conn());

	$log_content = "## 결제관련설정 ## - 여부:$up_card_splittype 개월수:$up_card_splitmonth 금액:$up_card_splitprice 카드수수료 : $up_card_payfee(마이너스이면 현금할인율임) $up_payment_type 최소주문가격: $bank_miniprice 카드취소주문가격 : $card_miniprice";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	$onload="<script>window.onload=function(){ alert('결제관련 설정이 완료되었습니다.'); }</script>";
} else if ($type=="add") {
	if (ord(trim($up_newbank_account))) {
		$sql = "SELECT bank_account FROM tblshopinfo ";
		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {
			if (ord($row->bank_account)){
			   $arbank_account=explode("=",$row->bank_account);
			   $sql = "UPDATE tblshopinfo SET ";
			   $sql.= "bank_account='";
			   if(ord($arbank_account[0])) {
				   $sql.=$arbank_account[0].",";
			   }
			   $sql.=$up_newbank_account."={$arbank_account[1]}={$arbank_account[2]}'";
			} else {
				$sql = "UPDATE tblshopinfo SET bank_account = '{$up_newbank_account}' ";
			}
			pmysql_query($sql,get_db_conn());
			DeleteCache("tblshopinfo.cache");
			$onload = "<script>window.onload=function(){ alert('무통장 입금 신규 계좌 추가가 완료되었습니다.'); }</script>";
		}
		pmysql_free_result($result);
	}
} else if ($type=="del") {
	if (ord(trim($up_newbank_account))) {
		$sql = "SELECT bank_account FROM tblshopinfo ";
		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {
			if (ord($row->bank_account)) {
				$bank_account=explode("=",$row->bank_account);
				$temp = $bank_account[0];
				$tok = strtok($temp,",");
				$count = 0; $temp2="";
				while ($tok) {
					if ($up_newbank_account!=$tok) {
						if ($count==0) $temp2=$tok;
						else $temp2=$temp2.",".$tok;
						$count++;
					}
					$tok = strtok(",");
				}
			}
			$sql = "UPDATE tblshopinfo SET ";
			$sql.= "bank_account = '{$temp2}={$bank_account[1]}={$bank_account[2]}' ";
			pmysql_query($sql,get_db_conn());
			DeleteCache("tblshopinfo.cache");
			$onload="<script>window.onload=function(){ alert('선택하신 무통장 입금계좌 삭제가 완료되었습니다.'); }</script>";
		}
	}
}


$sql = "SELECT bank_account,payment_type,deli_basefee,deli_miniprice,card_payfee,card_splittype, price_dc_10, price_dc_30, price_dc_50, ";
$sql.= "card_splitmonth, card_splitprice,card_miniprice,bank_miniprice FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$arbank_account=explode("=",$row->bank_account);
	$bank_account=$arbank_account[0];
	$bankmess=$arbank_account[1];
	$cardmess=$arbank_account[2];
	$payment_type = $row->payment_type;
	$deli_basefee = $row->deli_basefee;
	$deli_miniprice = $row->deli_miniprice;
	$card_payfee = $row->card_payfee;
	$bank_percent = 0;
	$card_splittype = $row->card_splittype;
	$card_splitmonth = $row->card_splitmonth;
	$card_splitprice = $row->card_splitprice;
	$card_miniprice = $row->card_miniprice;
	$bank_miniprice = $row->bank_miniprice;	
	$price_dc_10 = $row->price_dc_10;	
	$price_dc_30 = $row->price_dc_30;	
	$price_dc_50 = $row->price_dc_50;	

	if ($card_payfee<=0) { 
		$saletype="+";
		$bank_percent = abs($row->card_payfee); 
		if($bank_percent>50){
			$bank_percent-=50;
			$saletype="-";
		}
		$card_payfee=0; 
	}
}
pmysql_free_result($result);
if($card_payfee < 1) $card_payfee = 0;

${"check_payment_type".$payment_type} = "checked";
${"check_card_splittype".$card_splittype} = "checked";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var temp;
	//temp = parseInt(document.form1.up_card_payfee.value);
	//temp2 = parseInt(document.form1.up_bank_percent.value);
	/*
	if (document.form1.up_card_splitprice.value<50000) {
		alert("무이자 가능금액은 50,000원 이상입니다.(할부는 5만원이상일때 가능)");
		document.form1.up_card_splitprice.focus();
		return;
	}
	if(!(-1 < temp && temp < 100)) {
		alert("카드수수료란에는 0부터 99까지의 숫자만 가능합니다.");
		document.form1.up_card_payfee.focus();
		return;
	}
	if(!(-1 < temp2 && temp2 < 51)) {
		alert("현금결제 수수료 할인/적립율에는 0부터 50까지의 숫자만 가능합니다.");
		document.form1.up_bank_percent.focus();
		return;
	}
	if (temp!=0 && temp2!=0) {
		alert("카드결제 수수료 추가와 현금결제 할인/적립 정책은 동시사용 불가합니다.");
		return;
	}
	*/
	document.form1.type.value="up";
	document.form1.submit();
}

function AccountDel(account) {
	document.form1.up_newbank_account.value=account;
	document.form1.type.value="del";
	document.form1.submit();
}

function AccountAdd(){
	document.form1.up_newbank_account.value = document.form1.up_newbank_account1.value;
	if (document.form1.up_newbank_account2.value.length>0) {
		document.form1.up_newbank_account.value += " (예금주:" + document.form1.up_newbank_account2.value + ")";
	}
	if (document.form1.up_newbank_account.value.length==0) {
		alert("은행계좌번호를 입력하세요.");
		return;
	}
	document.form1.type.value="add";
	document.form1.submit();
}

function checkmess(){
	if(!confirm('결제업체와 무이자할부서비스 계약을 먼저 하셔야 합니다.\n계약이 안된 상태에서 무이자 적용시 결제에러가 날 수 있습니다.\n무이자 할부 계약이 되어계시면 [확인]버튼을 눌러주셔야 적용됩니다.\n[취소]버튼을 누르시면 할부 무이자 안함으로 변경됩니다.')){
		document.form1.up_card_splittype[0].checked=true; 
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>상품 결제관련 기능설정</span></p></div></div>
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
					<div class="title_depth3">상품 결제관련 기능설정</div>
				</td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=up_newbank_account>
			<input type=hidden name=up_oldbank_account value="<?=$bank_account?>">
			<tr style='display:none;'>
				<td>
                <div class="table_style01">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<th><span>결제방법 선택</span></th>
					<TD class="td_con1"><input type=radio id="idx_payment_type1" name=up_payment_type value="Y" <?=$check_payment_typeY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_payment_type1>신용카드+온라인결제</label> <input type=radio id="idx_payment_type2" name=up_payment_type value="C" <?=$check_payment_typeC?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_payment_type2>신용카드결제만 가능</label> <input type=radio id="idx_payment_type3" name=up_payment_type value="N" <?=$check_payment_typeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_payment_type3>온라인결제만 가능</label></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<!--
			<tr>
                 <td style="padding-top:3pt; padding-bottom:3pt;">                        
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 카드결제는 지불중회사와 계약 후 지불아이디(사이트코드)를 회사에 알려주셔야 사용 가능합니다.</li>
                                <li>2) 지불중계회사는 적용 가능한 회사만 지원됩니다.(회사 홈페이지 참조)</li>
                            </ul>
                        </div>
                </td>
			</tr>
			-->
<?php
/*
			<tr>
				<td>
					<div class="title_depth3_sub">신용카드 무이자</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 무이자 할부에 대한 추가수수료는 상점 부담입니다.(기본 카드결제수수료+무이자할부 수수료)</li>
                                <li>2) 무이자 할부는 지불중계회사와 별도의 계약 후 적용됩니다.(지불중계회사에서 제공하는 관리메뉴에서 신청 및 설정)</li>
                                <li>3) 무이자 할부 가능 카드, 무이자 개월수에 대한 추가수수료 차이등은 해당 지불중계회사의 안내를 받으시면 됩니다.</li>
                                <li>4) 카드사마다 자체 무이자 지원행사를 하는 경우에는 지불중계회사의 관리메뉴에서 무이자설정 종료 무이자 할부 안함으로 선택.</li>
                            </ul>
                        </div>
                </td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>무이자 할부 여부</span></th>
					<TD class="td_con1"><input type=radio id="idx_card_splittype1" name=up_card_splittype value="N" <?=$check_card_splittypeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_card_splittype1>무이자 할부 사용안함</label><BR>
					<input type=radio id="idx_card_splittype2" name=up_card_splittype value="Y" <?=$check_card_splittypeY?> onclick="checkmess()"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_card_splittype2>무이자 할부 적용(상점부담,<font color=red>전체상품적용</font>)</label><BR>
					<!--<input type=radio id="idx_card_splittype3" name=up_card_splittype value="O" <?=$check_card_splittypeO?> onclick="checkmess()"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_card_splittype3>무이자 할부 적용(상점부담,<font color=red>개별상품적용</font> → <B>[판매상품신규등록/관리]</B>에서 체크)</label>--></TD>
				</TR>
				<TR>
					<th><span>무이자 가능 개월수</span></th>
					<TD class="td_con1"><select name=up_card_splitmonth class="select">
					<?php
					for ($i=3;$i<=12;$i++) {
						echo "<option value='$i'";
						if ($i==$card_splitmonth) echo " selected";
						echo ">{$i}개월\n";
					}
					?>
					</select> 이내<FONT color=#0054a6><BR> </FONT><span class="font_blue">* 3개월로 선택시 3개월만 무이자, 나머지 일반할부<BR> * 지불중계회사 관리메뉴에서 카드마다 할부개월수 선택가능, 단, 선택한 할부개월수 외엔 모두 일반할부<BR>* 지불중계회사의 관리메뉴와 상점의 무이자 가능 개월수를 동일하게 설정</span></TD>
				</TR>
				<TR>
					<th><span>무이자 가능금액</span></th>
					<TD class="td_con1"><input type=text name=up_card_splitprice value="<?=$card_splitprice?>" size=7 maxlength=7 class="input_selected"> 이상(콤마제외)</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
*/
?>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">무통장 결제계좌 관리</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center">은행 및 계좌번호</TD>
					<TD class="table_cell" align="center">선택계좌삭제</TD>
				</TR>
				<?php
				if (ord($bank_account)) {
					$temp = $bank_account;
					$tok = strtok($temp,",");
					while ($tok) {
						echo "<TR><TD class=\"td_con1\" align=\"center\">$tok</td>\n";
						echo "<TD class=\"td_con1\" align=\"center\"><a href=\"javascript:AccountDel('$tok');\"><img src=\"images/btn_delet.gif\" border=\"0\"></a></td></tr>\n";
						$tok = strtok(",");
					}
				} else {
					echo "<TR><TD class=\"td_con1\" colspan=2 align=center>등록된 계좌정보가 없습니다.</td></tr>\n";
				}
				?>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<TD width="100%"><div class="point_title">계좌번호 입력하기</div></TD>
                    </tr>
                    <tr>
						<td width="100%">
                        <div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>은행 및 계좌번호</span></th>
							<TD class="td_con1"><input type=text name=up_newbank_account1 size=27 maxlength=50 class="input">&nbsp;&nbsp;<span class="font_orange">* 예) 00은행 123-4567-8910 방식으로 입력</span></TD>
						</TR>
						<TR>
							<th><span>예금주</span></th>
							<TD class="td_con1">
                            	<div class="table_none">
                                <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="569"><input type=text name=up_newbank_account2 size=12 maxlength=30 class="input"> <a href="javascript:AccountAdd();"><img src="images/btn_bank.gif" border="0" align=absmiddle></a></td>
                                </tr>
                                </table>
                                </div>
							</TD>
						</TR>
						</TABLE>
                        </div>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>

			<tr style='display:none;' >
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">신용카드 혜택 안내메세지 등록</div>
				</td>
			</tr>
			<!-- <tr style='display:none;' >
			                <td style="padding-top:3pt; padding-bottom:3pt;">                        
			                        도움말
						<div class="help_info01_wrap">
			                            <ul>
			                                <li>1) 결제창의 무통장 입금 기본안내 문구 <b>(반드시 주문자 성함으로 입금)</b>를 원하는 문구로 변경할 수 있습니다.</li>
			                                <li>2) 결제창의 신용카드 결제 기본안내 문구 <b>[비할인판매가]</b>를 원하는 문구로 변경할 수 있습니다.</li>
			                            </ul>
			                        </div>
			                </td>
			</tr> -->
			<tr style='display:none;' >
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<!--
				<TR>
					<th><span>무통장 입금 안내문구 변경</span></th>
					<TD class="td_con1"><input type=text name=up_bankmess value="<?=trim($bankmess)?>" size=80 maxlength=80 class="input" style="width:100%"></TD>
				</TR>
				-->
				<TR>
					<th><span>신용카드 혜택 안내문구 변경</span></th>
					<TD class="td_con1"><input type=text name=up_cardmess value="<?=trim($cardmess)?>" size=50 maxlength=50 class="input" style="width:100%"></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>

<?php
/*
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">신용카드 결제 수수료</div>
				</td>
			</tr>
			<tr>
                 <td style="padding-top:3pt; padding-bottom:3pt;">                        
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 현금구매가격 + 카드결제 수수료 방식입니다.</li>
                                <li>2) 현금구매와 카드결제에 대한 차별은 금지되어 있으며 법적 책임이 발생할 수 있습니다.</li>
                            </ul>
                        </div>                        
                </td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>카드결제 수수료 추가</span></th>
					<TD class="td_con1"><input type=text name=up_card_payfee value="<?=$card_payfee?>" size=5 maxlength=2 style="font-size:9pt" class="input">% 의 수수료를 <span class="font_orange">추가</span> (10원 단위 절사)</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">현금결제 할인/적립</div>
				</td>
			</tr>
			<tr>
                 <td style="padding-top:3pt; padding-bottom:3pt;">                        
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 현금결제시 할인/추가 적립금을 주는 방식 ( 카드결제수수료 추가와 동시 사용불가)</li>
                                <li>2) 추가 적립금은 로그인한 회원에게만 적용됩니다.(추가 할인은 일반회원에게도 적용)</li>
                                <li>3) 현금구매와 카드결제에 대한 차별은 금지되어 있으며 법적 책임이 발생할 수 있습니다.</li>
                            </ul>
                        </div>                        
                </td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>현금결제 혜택</span></th>
					<TD class="td_con1"><input type=text name=up_bank_percent value="<?=$bank_percent?>" size=5 maxlength=2 style="font-size:9pt" class="input">% <select name=up_saletype class="input">
						<option value="+" <?=($saletype=="+"?"selected":"")?>>할인
						<option value="-" <?=($saletype=="-"?"selected":"")?>>적립
						</select> 제공 (10원단위 절사, 배송비는 제외)</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">취소주문 가능금액 설정</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">                        
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 주문 가능금액 보다 작은 금액은 주문이 되지 않습니다.(순수 상품금액 기준 - 배송료 및 카드수수료 제외)</li>
                                <li>2) 0으로 입력하면 모든 금액이 주문됩니다.</li>
                                <li>3) 신용카드 주문 가능금액 보다 작은 금액은 무통장 입금이 가능합니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;신용카드 주문 가능금액은 무통장 결제보다 크거나 같아야 합니다.</li>
                                <li>4) 무통장 결제와 신용카드 결제의 차별은 금지되어 있으며 법적 책임이 발생할 수 있습니다.</li>
                            </ul>
                        </div>                        
                </td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<?
					if(!$bank_miniprice) $bank_miniprice = 0;
					if(!$card_miniprice) $card_miniprice = 0;
				?>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>무통장 결제</span></th>
					<TD class="td_con1"> 주문 가능 금액 : <input type=text name=up_bank_miniprice size=15 maxlength=7 value="<?=$bank_miniprice?>" class="input">원</TD>
				</TR>
				<TR>
					<th><span>신용카드 결제</span></th>
					<TD class="td_con1"> 주문 가능 금액 : <input type=text name=up_card_miniprice size=15 maxlength=7 value="<?=$card_miniprice?>" class="input">원</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td height="10">&nbsp;</td>
			</tr>
			
			<!--tr>
				<td>
					<div class="title_depth3_sub">구매 금액별 할인율 설정</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>10만원 이상</span></th>
					<TD class="td_con1"><input type=text name=up_price_dc_10 size=10 value="<?=$price_dc_10?>" class="input">%</TD>
				</TR>
				<TR>
					<th><span>30만원 이상</span></th>
					<TD class="td_con1"><input type=text name=up_price_dc_30 size=10 value="<?=$price_dc_30?>" class="input">%</TD>
				</TR>
				<TR>
					<th><span>50만원 이상</span></th>
					<TD class="td_con1"><input type=text name=up_price_dc_50 size=10 value="<?=$price_dc_50?>" class="input">%</TD>
				</TR>
				
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td height="10">&nbsp;</td>
			</tr-->
*/
?>			
			
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
							<li>실시간계좌이체, 가상계좌, 핸드폰결제, 에스크로결제는 PG사와 계약 후 쇼핑몰에 적용해야 합니다.</li>
							<li>이외 추가적인 결제 수단은 프로그램과 연동이 가능한 결제수단만 가능합니다. </li>
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
						</ul>

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
