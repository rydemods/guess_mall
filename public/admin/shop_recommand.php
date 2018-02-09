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
$up_recom_ok=$_POST["up_recom_ok"];
$up_recom_memreserve=$_POST["up_recom_memreserve"];
$up_recom_addreserve=$_POST["up_recom_addreserve"];
$up_recom_limit=$_POST["up_recom_limit"];

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "recom_ok			= '{$up_recom_ok}', ";
	$sql.= "recom_memreserve	= '{$up_recom_memreserve}', ";
	$sql.= "recom_addreserve	= '{$up_recom_addreserve}', ";
	if(ord($up_recom_limit)==0) {
		$sql.= "recom_limit	= NULL ";
	} else {
		$sql.= "recom_limit	= '{$up_recom_limit}' ";
	}
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('추천인 제도 설정이 완료되었습니다.'); }</script>\n";
}

$sql = "SELECT recom_ok,recom_memreserve,recom_addreserve,recom_limit ";
$sql.= "FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$recom_ok=$row->recom_ok;
	$recom_memreserve=$row->recom_memreserve;
	$recom_addreserve=$row->recom_addreserve;
	$recom_limit=$row->recom_limit;
}
pmysql_free_result($result);

${"check_recom_ok".$recom_ok} = "checked";

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(isNaN(document.form1.up_recom_limit.value)){
		alert('추천인 인원 제한수는 숫자만 입력 가능합니다.');
		document.form1.up_recom_limit.focus();
		return;
	}
	document.form1.type.value="up";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>추천인 제도 설정</span></p></div></div>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">추천인 제도 설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>회원가입을 추천한 추천인에게 각종 혜택을 부여할 수 있습니다. 타 쇼핑몰과 차별화되는 추천인제도를 활용해 보세요.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<br />
					<div class="title_depth3_sub">추천인 적용여부</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1) 추천인 사용시 회원가입페이지에 추천인 입력란이 자동 생성됩니다.</li>
						<li>2) 추천에 대한 혜택이 있을 경우에는 부작용을 예방하기 위해 <b>실명인증서비스</b> 이용을 권장합니다.</li>
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
					<th><span>추천인 적용여부 선택</span></th>
					<TD class="td_con1"><input type=radio id="idx_recom_ok1" name=up_recom_ok value="Y" <?=$check_recom_okY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_recom_ok1>추천인 사용</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_recom_ok2" name=up_recom_ok value="N" <?=$check_recom_okN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_recom_ok2>추천인 사용불가</label></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3_sub">적립금 추가 설정</div>

				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1) My page > 적립금에서 추가된 적립금 확인 가능합니다.</li>
						<li>2) 가입즉시 추가 적립됩니다.</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>추천인에게 추가적립</span></th>
					<TD class="td_con1"><select name=up_recom_memreserve class="select">
						<option value=0>없음</option>
<?php
	$i = 100;
	while($i < 20001) {
		echo "<option ";
		if($recom_memreserve==$i) echo "selected ";
		echo "value=\"{$i}\">".number_format($i)."</option>\n";
		if($i<500) { $i = $i +100; }
		elseif($i<2000) { $i = $i +500; }
		elseif($i<5000) { $i = $i +1000; }
		else { $i = $i +5000; }
	}
?>
						</select> <span class="font_orange">* 신규회원으로 가입하도록  추천한 <b>기존회원(추천인)</b>에게 적립금 추가.</span></TD>
				</TR>
				<TR>
					<th><span>추천인을 입력한 회원</span></th>
					<TD class="td_con1"><select name=up_recom_addreserve class="select">
						<option value=0>없음
<?php
	$i = 100;
	while($i < 20001) {
		echo "<option ";
		if($recom_addreserve==$i) echo "selected ";
		echo "value=\"{$i}\">".number_format($i)."</option>\n";
		if($i<500) { $i = $i +100; }
		elseif($i<2000) { $i = $i +500; }
		elseif($i<5000) { $i = $i +1000; }
		else { $i = $i +5000; }
	}
?>
						</select> <span class="font_orange">* 신규회원 가입시 추천인을 <b>기입한 본인</b>에게 적림금 추가.</span>
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>	
					<div class="title_depth3_sub">추천인 인원 제한</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1) 1인당 추천수를 제한 할 수 있습니다.</li>
						<li>2) 숫자를 입력하지 않을 경우 무제한 추천이 가능합니다.("0"도 동일) <b>미사용시는 추천인 사용불가</b> 설정을 해주세요.</li>
					</ul>
				</div>				
				</td>
			</tr>    
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>추천 가능한 인원</span></th>
					<TD class="td_con1"><input type=text name=up_recom_limit value="<?=$recom_limit?>" size=5 maxlength=4 class="input"> 명 <span class="font_orange">* <b>추천인 사용으로 설정한 경우</b> 추천 가능한 회원수</span></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>추천한 회원이 탈퇴한 경우</span></dt>
							<dd>- 추천만 하고 탈퇴하더라도 회원가입 즉시 추가된 적립금은 환수가 안됩니다.<br>
- 추천으로 받은 적립금을 관리자가 차감할 수 있으나 회원정보 기록에 남습니다.<br>
- <a href="javascript:parent.topframe.GoMenu(3,'member_list.php');"><span class="font_blue">회원관리 > 회원정보관리 > 회원정보관리</span></a> 에서 회원의 적립금을 관리할 수 있습니다.<br>

							</dd>
						</dl>
						<dl>
							<dt><span>추천 가능한 인원의 기준</span></dt>
							<dd>- 현재 회원으로 유지되고 있는 추천한 회원을 기준으로 합니다.<br>
							- 현재 회원으로 유지되고 있는 추천한 회원을 기준으로 합니다.<br>
							- 추천만하고 탈퇴한 경우는 제한인원에 포함되지 않습니다.<br>
							- 회원 탈퇴시 관리자 인증 후 탈퇴로 설정해 놓으면 추천으로 받은 적립금을 검토하여 차감유무를 처리하는데 편리합니다.<br>
							- <a href="javascript:parent.topframe.GoMenu(1,'shop_member.php');"><span class="font_blue">상점관리 > 쇼핑몰 운영 설정 > 회원가입 관련 설정</span></a> 에서 회원 탈퇴를 설정할 수 있습니다.
							</dd>
						</dl>
						<dl>
							<dt><span>탈퇴한 회원을 추천 인원 제한에 포함시키지 않는 이유</span></dt>
							<dd>- 정상적 이유로도 탈퇴할 수 있음으로 탈퇴한 회원까지 제한인원에 포함할 경우<br><b>&nbsp;&nbsp;</b>선의의 추천을 더 할 수 없는 경우가 발생할 수 있기 때문에 제한인원에 포함시키지 않고 있습니다.
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
