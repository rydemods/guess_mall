<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-4";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$mode=$_POST["mode"];
$ipidx=$_POST["ipidx"];
$ipaddress=$_POST["ipaddress"];
$description=$_POST["description"];
$disabled=(int)$_POST["disabled"];

if ($mode=="edit") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$sql = "SELECT idx as ipidx , ipaddress, disabled, description ";
	$sql.= "FROM tblsecurityiplist WHERE idx = '{$ipidx}' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_object($result)) {
		$ipaddress=$row->ipaddress;
		$description=$row->description;
		$disabled=$row->disabled;

		${"check_".$disabled} = "checked";
	} else {
		alert_go('선택하신 IP 정보가 존재하지 않습니다.');
	}
	pmysql_free_result($result);
} else if ($mode=="del") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$sql = "SELECT idx as ipidx, ipaddress FROM tblsecurityiplist ";
	$sql.= "WHERE idx = '{$ipidx}' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_object($result)) {
		$ipaddress = $row->ipaddress;
		pmysql_free_result($result);
		$sql = "SELECT ipidx FROM tblsecurityadminip WHERE ipidx = '{$ipidx}' ";
		$result = pmysql_query($sql,get_db_conn());
		$flag = (boolean)pmysql_num_rows($result);
		pmysql_free_result($result);
		if ($flag) {
			alert_go("해당 IP ({$ipaddress})는 현재 사용중인 IP입니다.\\n\\n운영자/부운영자 설정에서 해당 IP선택을 해지 하신 후 삭제하시기 바랍니다.");
		}
		$sql = "DELETE FROM tblsecurityiplist WHERE idx = '{$ipidx}' ";
		pmysql_query($sql,get_db_conn());
		alert_go("해당 IP ({$ipaddress})를 삭제하였습니다.");
	} else {
		alert_go('선택하신 IP 정보가 존재하지 않습니다.');
	}
}

if ($type=="insert") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	if (!$ipaddress || !$description) {
		alert_go('필수 입력 항목 등록이 잘못되었습니다.');
	}
	$sql = "SELECT ipaddress FROM tblsecurityiplist WHERE ipaddress = '{$ipaddress}'";
	$result = pmysql_query($sql,get_db_conn());
	$flag = pmysql_num_rows($result);
	pmysql_free_result($result);
	if ($flag) {
		alert_go('입력하신 IP 정보가 현재 사용중입니다.');
	}
	$sql = "INSERT INTO tblsecurityiplist (ipaddress,description,disabled) VALUES ";
	$sql.= "('{$ipaddress}','{$description}','{$disabled}')";
	$insert = pmysql_query($sql,get_db_conn());

	alert_go('접근 IP 추가 프로세싱이 정상적으로 처리되었습니다.');
} else if ($type=="edit") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$sql = "SELECT idx FROM tblsecurityiplist WHERE idx = '{$ipidx}' ";
	$result = pmysql_query($sql,get_db_conn());
	if (!$row = pmysql_fetch_object($result)) {
		alert_go('선택하신 IP 정보가 존재하지 않습니다.');
	}
	pmysql_free_result($result);

	$sql = "SELECT ipaddress FROM tblsecurityiplist WHERE ipaddress = '{$ipaddress}' AND idx != '{$ipidx}'";
	$result = pmysql_query($sql,get_db_conn());
	$flag = pmysql_num_rows($result);
	pmysql_free_result($result);
	if ($flag) {
		alert_go('입력하신 IP 정보가 현재 사용중입니다.');
	}

	$sql = "UPDATE tblsecurityiplist SET ";
	$sql.= "ipaddress	= '{$ipaddress}', ";
	$sql.= "description	= '{$description}', ";
	$sql.= "disabled	= '{$disabled}' ";
	$sql.= "WHERE idx = '{$ipidx}' ";
	pmysql_query($sql,get_db_conn());

	alert_go('접근 IP 정보 수정이 완료되었습니다.');
}

$mode = $mode ? $mode : "insert";
if ($mode=="edit") {
	$button_value = "images/btn_edit2.gif";
} else if ($mode=="insert") {
	$button_value = "images/btn_badd2.gif";
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(form1.ipaddress.value.length<=0) {
		alert("IP Address를 입력하세요.");
		form1.ipaddress.focus();
		return;
	}
	if (form1.description.value.length<=0) {
		alert("IP Address에 대한 설명을 입력하세요.");
		form1.description.focus();
		return;
	}
	var ra = false;
	for(var i=0;i<form1.disabled.length;i++){
		if(form1.disabled[i].checked){
			ra=true;
			break;
		}
	}
	if(!ra){
		alert("사용여부를 선택하세요.");
		form1.disabled[0].focus();
		return;
	}
	form1.type.value=type;
	form1.submit();
}

function check_form(mode,ipidx) {
	if (mode=="del") {
		var con=confirm("해당 IP 정보를 삭제 하시겠습니까?");
		if (!con) {
			return;
		}
	}
	form2.mode.value=mode;
	form2.ipidx.value=ipidx;
	form2.submit();
}
//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 보안설정 &gt;<span>접근IP 설정</span></p></div></div>
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
					<div class="title_depth3">접근IP 설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>관리페이지에 접근할 수 있는 운영자/부운영자 IP를 관리합니다.</span></div>
					<!-- 소제목 -->
					<div class="title_depth3_sub">접근IP 목록</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <col width="" />
                <col width="100" />
                <col width="" />
                <col width="60" />
                <col width="60" />
				<tr>
					<th>IP Address</th>
					<th>사용여부</th>
					<th>적용여부</th>
					<th>수정</th>
					<th>삭제</th>
				</tr>
<?php
			$count = 0;
			$sql = "SELECT idx as ipidx , ipaddress, disabled, description ";
			$sql.= "FROM tblsecurityiplist ORDER BY idx DESC ";
			$result = pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$count++;

				echo "<tr>\n";
				echo "	<TD><span class=font_orange>{$count} &nbsp;-&nbsp; <B>{$row->ipaddress}</B></span></td>\n";
				if ($row->disabled == 0) {
					echo "	<TD>사용함</td>\n";
				} else {
					echo "	<TD>사용안함</td>\n";
				}

				$allowedadmins = "";
				$sql = "SELECT a.id FROM tblsecurityadmin a, tblsecurityadminip p ";
				$sql.= "WHERE a.id = p.id AND p.ipidx = {$row->ipidx}";
				$result2 = pmysql_query($sql,get_db_conn());
				while($row2 = pmysql_fetch_object($result2)) {
					$aname = $row2->id;
					if($aname)
						$allowedadmins .= "{$aname}, ";
				}
				pmysql_free_result($result2);

				if ($allowedadmins) {
					$allowedadmins = substr($allowedadmins,0,(strlen($allowedadmins)-2));
					echo "	<TD>{$allowedadmins}</td>\n";
				} else {
					echo "	<TD>해당 아이피를 적용중인 운영자/부운영자가 없습니다.</td>\n";
				}
				echo "	<TD><a href=\"javascript:check_form('edit','{$row->ipidx}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></td>\n";
				echo "	<TD><a href=\"javascript:check_form('del','{$row->ipidx}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></td>\n";
				echo "</tr>\n";
			}
			if ($count == 0) {
				echo "<tr>\n";
				echo "	<TD colspan=\"5\">등록된 접근 IP 정보가 없습니다.</td>\n";
				echo "</tr>\n";
			}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">접근IP 등록</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ipidx value="<?=$ipidx?>">
			<tr class='hide'>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 접근 IP 추가 후 <span class=font_orange><B>운영자/부운영자 설정</B></span>에서 운영자별 접근 IP 선택을 하실 수 있습니다.</li>
                            <li>2) 유동IP의 경우 잦은 IP 변경으로 관리페이지에 접근이 차단될 수 있사오니 이점 유의하시기 바랍니다.</li>
                        </ul>
                    </div>
                </td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>IP Address</span></th>
					<TD class="td_con1"><input type=text name=ipaddress value="<?=$ipaddress?>" size=25 class="input"> <span class=font_orange>예)211.235.123.120</span></TD>
				</TR>
				<TR>
					<th><span>설명</span></th>
					<TD class="td_con1"><textarea cols=60 rows=2 name=description style="width:100%;height=85px;" class="textarea"><?=$description?></textarea></TD>
				</TR>
				<TR>
					<th><span>사용여부</span></th>
					<TD class="td_con1"><input type=radio id="idx_disabled1" name=disabled value="0" <?=$check_0?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled1>사용함</label> &nbsp;&nbsp;<input type=radio id="idx_disabled2" name=disabled value="1" <?=$check_1?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled2>사용하지 않음</label></TD>
				</TR>
				</TABLE>
				</div>
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
			<input type=hidden name=ipidx>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>접근 IP 등록 후 [ 운영자 설정 ]에서 운영자별 접근 IP 선택을 하실 수 있습니다.</li>
							<li>유동 IP의 경우 잦은 IP 변경으로 관리페이지에 접근이 차단될 수 있사오니 이점 유의하시기 바랍니다.</li>
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
