<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-2";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$type=$_POST["type"];
$up_yy=$_POST["up_yy"];
$up_limit_amt=$_POST["up_limit_amt"];

if ($mode=="edit") {
	$sql = "SELECT * FROM tblpoint_staff_limit ";
	$sql.= "WHERE yy = '{$up_yy}' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_object($result)) {
		$up_yy=$row->yy;
		$up_limit_amt=$row->limit_amt;
	} else {
		alert_go($up_yy.'년도 복지할인 제한금액이 존재하지 않습니다.');
	}
	pmysql_free_result($result);
}

if ($type=="insert") {
	if (!$up_yy || !$up_limit_amt) {
		alert_go('필수 입력 항목 등록이 잘못되었습니다.');
	}

	$sql = "SELECT yy FROM tblpoint_staff_limit WHERE yy = '{$up_yy}' ";
	$result = pmysql_query($sql,get_db_conn());
	$rows = (boolean)pmysql_num_rows($result);
	pmysql_free_result($result);
	if ($rows) {
		alert_go($up_yy.'년도 복지할인 제한금액이 이미 등록되었습니다.');
	}

	$sql = "INSERT INTO tblpoint_staff_limit(
	yy		,
	limit_amt		,
	regdt) VALUES (
	'{$up_yy}', 
	'{$up_limit_amt}', 
	'".date("YmdHis")."')";
	pmysql_query($sql,get_db_conn());

	getErpStaffLimitPoint($up_yy);

	alert_go($up_yy.'년도 복지할인 제한금액 추가가 완료되었습니다.');
} else if ($type=="edit") {

	$sql = "SELECT yy FROM tblpoint_staff_limit WHERE yy = '{$up_yy}' ";
	$result = pmysql_query($sql,get_db_conn());
	if (!$row = pmysql_fetch_object($result)) {
		alert_go($up_yy.'년도 복지할인 제한금액이 존재하지 않습니다.');
	}
	pmysql_free_result($result);

	$sql = "UPDATE tblpoint_staff_limit SET ";
	$sql.= "limit_amt		= '{$up_limit_amt}' ";
	$sql.= "WHERE yy = '{$up_yy}' ";
	pmysql_query($sql,get_db_conn());

	getErpStaffLimitPoint($up_yy);

	alert_go($up_yy.'년도 복지할인 제한금액 수정이 완료되었습니다.');
}

$mode = $mode ? $mode : "insert";
if ($mode=="edit") {
	if ($disabled_form) {
		$button_value = "images/btn_edit1.gif";
	} else {
		$button_value = "images/btn_edit2.gif";
	}
} else if ($mode=="insert") {
	if ($disabled_form) {
		$button_value = "images/btn_badd1.gif";
	} else {
		$button_value = "images/btn_badd2.gif";
	}
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(form1.up_limit_amt.value.length<=0) {
		alert(" 제한금액을 입력하세요.");
		form1.up_limit_amt.focus();
		return;
	}
	form1.type.value=type;
	form1.submit();
}

function check_form(mode,yy) {
	form2.mode.value=mode;
	form2.up_yy.value=yy;
	form2.submit();
}
$(document).ready( function() {
	$('.numbersOnly').keyup(function () { 
		this.value = this.value.replace(/[^0-9\.]/g,'');
	});
});
//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원등급 설정 &gt;<span>복지할인 제한금액관리</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
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
					<div class="title_depth3">복지할인 제한금액관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>년도별 임직원 복지할인 제한금액을 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">복지할인 제한금액 목록</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <col width="140" />
                <col width="" />
                <col width="140" />
				<TR>
					<th>년도</th>
					<th>제한금액</th>
					<th>수정</th>
				</TR>
<?php
				$count = 0;
				$sql = "SELECT * ";
				$sql.= "FROM tblpoint_staff_limit ORDER BY yy ASC ";
				$result = pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					$count++;
					echo "<tr>\n";
					echo "	<TD>{$row->yy}</td>\n";
					echo "	<TD>".number_format($row->limit_amt)."원</td>\n";
					echo "	<TD><a href=\"javascript:check_form('edit','{$row->yy}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></td>\n";
					echo "</tr>\n";

				}
				pmysql_free_result($result);

				if ($count == 0) {
					echo "<tr>\n";
					echo "	<TD colspan=\"4\">등록된 복지할인 제한금액이 없습니다.</td>\n";
					echo "</tr>\n";

				}
?>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">복지할인 제한금액 목록</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 년도별로 임직원 복지할인 제한금액을 설정 할 수 있습니다.</li>
                        </ul>
                    </div>
                </td>
            </tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th width="200px"><span>년도/제한금액</span></th>
					<td class="td_con1">
					<select name=up_yy>
					<?
					$now_yy	= date("Y") + 10;
					for($k=2017;$k < $now_yy;$k++){
					?>
					<option value="<?=$k?>"<?=$k==$up_yy?'selected':''?>><?=$k?>년</option>
					<?}?>
					</select>					
					<input type=text name=up_limit_amt value="<?=$up_limit_amt?>" size=20 maxlength=20 class="input numbersOnly" style="text-align:right;"> 원</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('<?=$mode?>');"><img src="<?=$button_value?>" border="0"></a></td>
			</tr>
			</form>
			<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=mode>
			<input type=hidden name=up_yy>
			</form>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>년도별 임직원 복지할인 제한금액을 관리할 수 있습니다.</li>
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
