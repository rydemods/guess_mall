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

$mode=$_POST["mode"];
$type=$_POST["type"];
$up_id=$_POST["up_id"];
$adminname=$_POST["adminname"];
$passwd=$_POST["passwd"];
$adminemail=$_POST["adminemail"];
$adminmobile=$_POST["adminmobile"];
$oldips=$_POST["oldips"];
$newips=$_POST["newips"];
$roleidx=$_POST["roleidx"];
$disabled=(int)$_POST["disabled"];

function getipidx($ipaddress) {
	global $_ShopInfo;
	$sql = "SELECT idx FROM tblsecurityiplist WHERE ipaddress = '{$ipaddress}' ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	$ipidx = (int)$row->idx;
	return $ipidx;
}

function isduplicatenewip($newip) {
	global $_ShopInfo;
	$sql = "SELECT ipaddress FROM tblsecurityiplist WHERE ipaddress = '{$newip}'";
	$result = pmysql_query($sql,get_db_conn());
	$rows = pmysql_num_rows($result);
	pmysql_free_result($result);
	if($rows > 0)
		return true;
	return false;
}

function setnewip($newips,$oldips) {
	global $_ShopInfo;
	$ipidxs = array();
	if($newips) {
		$newipvalues = explode(",",$newips);
		for($i=0;$i<count($newipvalues);$i++) {
			if ($newipvalues[$i]) {
				$newipadd = $newipvalues[$i];
				if(!isDuplicateNewIP($newipadd)) {
					$sql = "INSERT INTO tblsecurityiplist (ipaddress,disabled) VALUES ('{$newipadd}',0)";
					pmysql_query($sql,get_db_conn());
				}
			}
		}
	}

	if(count($oldips) > 0) {
		for($i = 0; $i < count($oldips); $i++)
			$ipidxs[] = $oldips[$i];
	}

	$newipidx = (int)-1;
	$ipexist = false;
	if($newips) {
		$newipvalues = explode(",",$newips);
		for($i=0;$i<count($newipvalues);$i++) {
			$newipidx = getIPIDX($newipvalues[$i]);
			$ipexist = false;
			if ($newipidx) {
				if(count($oldips) > 0) {
					for($j = 0; $j < count($ipidxs); $j++) {
						if($ipidxs[$j] == $newipidx) {
							$ipexist = true;
							$j = count($ipidxs);
						}
					}
					if(!$ipexist)
						$ipidxs[] = $newipidx;
				} else {
					$ipidxs[] = $newipidx;
				}
			}
		}
	}
	return (array)$ipidxs;
}

if ($mode=="edit") {
	$sql = "SELECT id, admintype, adminname, adminemail,adminmobile, disabled FROM tblsecurityadmin ";
	$sql.= "WHERE id = '{$up_id}' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_object($result)) {
		$up_id=$row->id;
		$admintype=$row->admintype;
		$adminname=$row->adminname;
		$adminemail=$row->adminemail;
		$adminmobile=$row->adminmobile;
		$disabled=$row->disabled;

		$id_readonly = "readonly";
		$id_style = "style=\"background:#eeeeee\"";

		$superadmin = false;
		if($admintype==1) {
			$superadmin = true;
			$submit = true;
			$disabled_disabled = "disabled";
			if ($up_id != $_ShopInfo->getId()) {
				$disabled_form = "disabled style=\"background:#eeeeee\"";
				$submit = false;
			}
		}
		if ($superadmin && $admintype!=1) {
			$disabled_form = "disabled style=\"background:#eeeeee\"";
		}
		${"check_".$disabled} = "checked";

	} else {
		alert_go('해당 운영자/부운영자 ID가 존재하지 않습니다.');
	}
	pmysql_free_result($result);
} else if ($mode=="del") {
	$sql = "SELECT id FROM tblsecurityadmin WHERE id = '{$up_id}' AND admintype=0 ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_object($result)) {
		$sql = "DELETE FROM tblsecurityadmin WHERE id = '{$up_id}' ";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblsecurityadminip WHERE id='{$up_id}' ";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblsecurityadminrole WHERE id = '{$up_id}' ";
		pmysql_query($sql,get_db_conn());

		alert_go("해당 아이디 ({$up_id})를 삭제하였습니다.");
	} else {
		alert_go('해당 운영자/부운영자 ID가 존재하지 않습니다.');
	}
	pmysql_free_result($result);
}

if ($type=="insert") {
	if (!$up_id || !$passwd || !$roleidx) {
		alert_go('필수 입력 항목 등록이 잘못되었습니다.');
	}
	if (!preg_match("/^[[:alnum:]]+$/", $up_id)) {
		alert_go('ID에는 영문/숫자만 입력하세요.');
	}

	$sql = "SELECT id FROM tblsecurityadmin WHERE id = '{$up_id}' ";
	$result = pmysql_query($sql,get_db_conn());
	$rows = (boolean)pmysql_num_rows($result);
	pmysql_free_result($result);
	if ($rows) {
		alert_go("({$up_id}) 아이디는 현재 사용중입니다.");
	}

	$ipidxs = (array)setnewip($newips,$oldips);
    // mysql 의 password 방식 알고리즘을 php로 구현함. 2015-10-15 jhjeong
    $shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd))));
	$sql = "INSERT INTO tblsecurityadmin(
	id		,
	passwd		,
	admintype	,
	adminname	,
	adminemail	,
	adminmobile	,
	expirydate	,
	registerdate	,
	disabled) VALUES (
	'{$up_id}', 
    '".$shadata."',
	0, 
	'{$adminname}', 
	'{$adminemail}', 
	'{$adminmobile}', 
	'0', 
	'".time()."', 
	'{$disabled}')";
	$insert = pmysql_query($sql,get_db_conn());
	if ($insert) {
		for($i = 0; $i < count($ipidxs); $i++) {
			$sql = "INSERT INTO tblsecurityadminip (id,ipidx) VALUES ('{$up_id}',{$ipidxs[$i]})";
			pmysql_query($sql,get_db_conn());
		}

		$sql = "INSERT INTO tblsecurityadminrole (id,roleidx) VALUES ('{$up_id}','{$roleidx}')";
		pmysql_query($sql,get_db_conn());
	}
	alert_go("({$up_id}) 부운영자 추가가 완료되었습니다.");
} else if ($type=="edit") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서 수정 테스트는 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$sql = "SELECT id,admintype FROM tblsecurityadmin ";
	$sql.= "WHERE id = '{$up_id}' ";
	$result = pmysql_query($sql,get_db_conn());
	if (!$row = pmysql_fetch_object($result)) {
		alert_go('해당 운영자/부운영자 ID가 존재하지 않습니다.');
	}
	pmysql_free_result($result);

	$disabled = 0;
	$superadmin = false;
	if($row->admintype==1)
		$superadmin = true;

	if (!$superadmin)
		$disabled = $_POST['disabled'];

	$ipidxs = (array)setnewip($newips,$oldips);

	$sql = "UPDATE tblsecurityadmin SET ";
	$sql.= "adminname		= '{$adminname}', ";
	$sql.= "adminemail		= '{$adminemail}', ";
	$sql.= "adminmobile		= '{$adminmobile}', ";
	if ($passwd) {
		//$sql.= "passwd		= '".md5($passwd)."', ";
        // mysql 의 password 방식 알고리즘을 php로 구현함. 2015-10-15 jhjeong
        $shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd))));
        $sql.= "passwd		= '".$shadata."', ";
	}
	$sql.= "disabled		= '{$disabled}' ";
	$sql.= "WHERE id = '{$up_id}' ";
	pmysql_query($sql,get_db_conn());
    //exdebug($sql);


	$sql = "DELETE FROM tblsecurityadminip WHERE id = '{$up_id}' ";
	pmysql_query($sql,get_db_conn());
	for($i = 0; $i < count($ipidxs); $i++) {
		$sql = "INSERT INTO tblsecurityadminip (id,ipidx) VALUES ('{$up_id}','{$ipidxs[$i]}')";
		pmysql_query($sql,get_db_conn());
	}

	if(!$superadmin) {
		$sql = "DELETE FROM tblsecurityadminrole WHERE id = '{$up_id}' ";
		pmysql_query($sql,get_db_conn());
		$sql = "INSERT INTO tblsecurityadminrole (id,roleidx) VALUES ('{$up_id}',{$roleidx})";
		pmysql_query($sql,get_db_conn());
	}
	alert_go('운영자/부운영자 정보 수정이 완료되었습니다.');
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
	if (type=="insert") {
		if(form1.up_id.value.length<=0) {
			alert("부운영자 아이디를 입력하세요.");
			form1.up_id.focus();
			return;
		}
		if (form1.passwd.value.length<=0) {
			alert("패스워드를 입력하세요.");
			form1.passwd.focus();
			return;
		}
		if (form1.passwd.value != form1.passwd2.value) {
			alert("패스워드가 일치하지 않습니다.");
			form1.passwd2.focus();
			return;
		}
	}
	if (!form1.roleidx.value) {
		alert("권한그룹 선택을 하세요.");
		form1.roleidx.focus();
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
		alert("로그인 허용여부를 선택하세요.");
		form1.disabled[0].focus();
		return;
	}
	form1.type.value=type;
	form1.submit();
}

function check_form(mode,id) {
	if (mode=="del") {
		var con=confirm("해당 운영자("+id+")를 삭제 하시겠습니까? (복구 불가능)");
		if (!con) {
			return ;
		}
	}
	form2.mode.value=mode;
	form2.up_id.value=id;
	form2.submit();
}
//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 보안설정 &gt;<span>운영자/부운영자 설정</span></p></div></div>
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
					<div class="title_depth3">운영자/부운영자 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>운영자/부운영자 정보 관리 및 생성, 부운영자 별 메뉴 사용권한/접속제한 등을 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">접근권한 그룹 목록</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <col width="140" />
                <col width="160" />
                <col width="60" />
                <col width="" />
                <col width="" />
                <col width="" />
                <col width="140" />
                <col width="60" />
                <col width="60" />
				<TR>
					<th>ID</th>
					<th>권한그룹</th>
					<th>로그인</th>
					<th>이름</th>
					<th>E-Mail</th>
					<th>연락처</th>
					<th>최근접속일</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$count = 0;
				$sql = "SELECT id, admintype, adminname, adminemail, adminmobile, lastlogintime, disabled ";
				$sql.= "FROM tblsecurityadmin ORDER BY id ASC ";
				$result = pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					$count++;
					echo "<tr>\n";
					echo "	<TD><span class=font_orange><B>{$row->id}</B></span></td>\n";
					$allowedroles = "";
					$sql = "SELECT r.description as rdesc FROM tblsecurityadminrole a, tblsecurityrole r ";
					$sql.= "WHERE a.roleidx = r.idx AND a.id = '{$row->id}' ";
					$result2 = pmysql_query($sql,get_db_conn());
					if ($row2 = pmysql_fetch_object($result2)) {
						$allowedroles = $row2->rdesc;
						echo "	<TD><span class=font_orange><B>{$allowedroles}</B></span></td>\n";
					} else {
						echo "	<TD>--/--/--</td>\n";
					}
					pmysql_free_result($result2);

					if ($row->disabled == 0) {
						echo "	<TD>가능</td>\n";
					} else {
						echo "	<TD>불가능</td>\n";
					}
					$row->adminname = $row->adminname ? $row->adminname : "--/--/--";
					$row->adminemail = $row->adminemail ? $row->adminemail : "--/--/--";
					$row->adminmobile = $row->adminmobile ? $row->adminmobile : "--/--/--";
					echo "	<TD>{$row->adminname}</td>\n";
					echo "	<TD>{$row->adminemail}</td>\n";
					echo "	<TD>{$row->adminmobile}</td>\n";
					if ($row->lastlogintime > 0) {
						echo "	<TD>".date("Y/m/d H:i:s",$row->lastlogintime)."</td>\n";
					} else {
						echo "	<TD>--/--/--</td>\n";
					}
					echo "	<TD><a href=\"javascript:check_form('edit','{$row->id}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></td>\n";
					if ($row->admintype==1) {
						echo "	<TD><img src=\"images/btn_del1.gif\" border=\"0\"></td>\n";
					} else {
						echo "	<TD><a href=\"javascript:check_form('del','{$row->id}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></td>\n";
					}
					echo "</tr>\n";

				}
				pmysql_free_result($result);

				if ($count == 0) {
					echo "<tr>\n";
					echo "	<TD colspan=\"9\">등록된 운영자/부운영자가 없습니다.</td>\n";
					echo "</tr>\n";

				}
?>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">접근권한 그룹 목록</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 운영자/부운영자 로그인은 회사 홈페이지에서만 가능합니다.</li>
                            <li>2) 비밀번호는 암호화 되어 알 수 없으며 변경만 가능합니다.(아이디는 영문, 숫자만 가능)</li>
                        </ul>
                    </div>
                </td>
            </tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th width="200px"><span>아이디</span></th>
					<td class="td_con1" width="35%"><input type=text name=up_id value="<?=$up_id?>" size=20 maxlength=20 <?=$id_readonly?> class="input" style="width:98%"></TD>
					<th width="200px"><span>이름</span></th>
					<TD class="td_con1" width="35%"><input type=text name=adminname value="<?=$adminname?>" size=20 maxlength=20 <?=$disabled_form?> class="input" style="width:99%"></TD>
				</TR>
				<TR>
					<th><span>비밀번호</span></th>
					<TD class="td_con1" ><input type=password name=passwd size=20 maxlength=20 <?=$disabled_form?> class="input" style="width:98%"></TD>
					<th><span>비밀번호 확인</span></th>
					<TD class="td_con1"><input type=password name=passwd2 size=20 maxlength=20 <?=$disabled_form?> class="input" style="width:99%"></TD>
				</TR>
				<TR>
					<th><span>E-Mail</span></th>
					<TD class="td_con1"><input type=text name=adminemail value="<?=$adminemail?>" size=30 maxlength=50 <?=$disabled_form?> class="input" style="width:98%"></TD>
					<th><span>연락처</span></th>
					<TD class="td_con1"><input type=text name=adminmobile value="<?=$adminmobile?>" size=30 maxlength=50 <?=$disabled_form?> class="input" style="width:99%"></TD>
				</TR>
				<TR>
					<th><span>접근 IP 선택</span></th>
					<TD class="td_con1" colspan="3">
<?php
				if ($mode=="edit") {
					$ipsarray = array();

					$sql = "SELECT ipidx FROM tblsecurityadminip ";
					$sql.= "WHERE id = '{$up_id}' ";
					$result = pmysql_query($sql,get_db_conn());
					while($row = pmysql_fetch_object($result)) {
						$ipsarray[] = $row->ipidx;
						if ($row->ipidx == 0) {
							$isallip = true;
						}
					}
					pmysql_free_result($result);

					$all = false;
					$sql = "SELECT idx, ipaddress FROM tblsecurityiplist ";
					$sql.= "WHERE disabled = 0 ORDER BY ipaddress";
					$result = pmysql_query($sql,get_db_conn());

					echo "<select name='oldips[]' size='10' multiple style=\"WIDTH:100%;height:100px\" class=\"textarea\" {$disabled_form}>";
					if($isallip) {
						echo chr(10).("<option value='0' selected>Any");
						$all = true;
					} else {
						echo chr(10).("<option value='0' >Any");
						$all = false;
					}
					$tempipidx = 0;
					$ipidx = 0;
					while($row = pmysql_fetch_object($result)) {
						$ipidx = (int)$row->idx;
						$ipaddress = $row->ipaddress;
						$selected = false;
						//if(!$all) {
							for($i = 0; $i < count($ipsarray); $i++) {
								$tempipidx = (int)$ipsarray[$i];
								if($ipidx == $tempipidx) {
									$selected = true;
									$i = count($ipsarray);
								}
							}

						//}
						if($selected)
							echo chr(10).("<option value={$ipidx} selected>{$ipaddress}");
						else
							echo chr(10).("<option value={$ipidx} >{$ipaddress}");
					}
					pmysql_free_result($result);

					echo "</select>";
				} else {
					$sql = "SELECT idx, ipaddress FROM tblsecurityiplist ";
					$sql.= "WHERE disabled = 0 ORDER BY ipaddress";
					$result = pmysql_query($sql,get_db_conn());

					echo "<select name='oldips[]' size='10' multiple style=\"WIDTH:100%;height:100px\" class=\"textarea\" {$disabled_form}>";
					echo "<option value='0' selected>Any";
					while($row = pmysql_fetch_object($result)) {
						$ipidx = (int)$row->idx;
						$ipaddress = $row->ipaddress;
						echo chr(10).("<option value={$ipidx}>{$ipaddress}");
					}
					pmysql_free_result($result);
					echo "</select>";
				}
?>
					</TD>
				</TR>
				<tr>
					<th><span>접근 가능한 새로운 IP</span></th>
					<TD class="td_con1" colspan="3"><textarea name=newips rows=3 cols=70 class="textarea" style="width:100%;height:50px" <?=$disabled_form?>></textarea><br><span class="font_orange">* 여러개 등록시 콤마(,)로 구분하여 입력</span></TD>
				</tr>
<?php
			if ($mode=="edit") {
				if(!$superadmin) {
					$roleid = 0;
					$sql = "SELECT roleidx FROM tblsecurityadminrole ";
					$sql.= "WHERE id = '{$up_id}' ";
					$result = pmysql_query($sql,get_db_conn());
					$row = pmysql_fetch_object($result);
					if($row->roleidx) {
						$roleidx = (int)$row->roleidx;
					}
?>
				<tr>
					<th><span>권한그룹 선택</span></th>
					<TD class="td_con1" colspan="3">
<?php
					$sql = "SELECT idx, description FROM tblsecurityrole ";
					$sql.= "WHERE disabled = 0 ORDER BY description";
					$result = pmysql_query($sql,get_db_conn());
					$flag = (boolean)pmysql_num_rows($result);
					if ($flag) {
						echo "<select name='roleidx' size='1' class=\"select\">\n";
						echo "<option value=''>권한그룹 선택</option>\n";
						$rno = 0;
						while($row = pmysql_fetch_object($result)) {
							$rno++;
							$roleidx1 = (int)$row->idx;
							$description = $row->description;
							if ($roleidx == $roleidx1) {
								echo chr(10).("<option value={$roleidx1} selected>{$description}");
							} else {
								echo chr(10).("<option value={$roleidx1}>{$description}");
							}
						}
						echo "</select>\n";
						$submit = true;
					} else {
						echo "생성된 권한그룹이 없어 수정이 불가능합니다.";
						$submit = false;
					}
					pmysql_free_result($result);
?>
					&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 그룹 및 권한등록 메뉴에서 등록</span>
					</TD>
				</tr>
<?php
				} else {
					echo chr(10).("<input type='hidden' name='roleidx' value='0'>");
				}
			} else {
?>
				<tr>
					<th><span>권한그룹 선택</span></th>
					<TD class="td_con1" colspan="3">
<?php
				$sql = "SELECT idx, description FROM tblsecurityrole ";
				$sql.= "WHERE disabled = 0 ORDER BY description";
				$result = pmysql_query($sql,get_db_conn());
				$flag = (boolean)pmysql_num_rows($result);
				if ($flag) {
					echo "<select name='roleidx' size='1' {$disabled_form} class=\"select\">\n";
					echo "<option value=''>권한그룹 선택</option>\n";
					$rno = 0;
					while($row = pmysql_fetch_object($result)) {
						$rno++;
						$roleidx = (int)$row->idx;
						$description = $row->description;
						echo chr(10).("<option value={$roleidx}>{$description}");
					}
					echo "</select>\n";
					$submit = true;
				} else {
					echo "<font color=red>권한그룹을 먼저 생성한 후 관리자를 추가하시기 바랍니다.</font>";
					$submit = false;
				}
				pmysql_free_result($result);
?>
					&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 그룹 및 권한등록 메뉴에서 등록</span>
					</TD>
				</tr>

				<?php
							}
				?>
				<tr>
					<th><span>로그인 허용여부</span></th>
					<TD class="td_con1"colspan="3"><input type=radio id="idx_disabled1" name=disabled value="0" <?=$check_0?> <?=$disabled_disabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled1>로그인 허용</label> &nbsp;&nbsp; <input type=radio id="idx_disabled2" name=disabled value="1" <?=$check_1?> <?=$disabled_disabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled2>로그인 거부</label></TD>
				</tr>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
<?php
	if ($submit) {
?>
	<tr>
		<td align="center"><a href="javascript:CheckForm('<?=$mode?>');"><img src="<?=$button_value?>" border="0"></a></td>
	</tr>
<?php
	} else {
?>
	<tr>
		<td align="center"><img src="<?=$button_value?>" border="0"></td>
	</tr>
<?php
	}
?>
			</form>
			<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=mode>
			<input type=hidden name=up_id>
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
							<li>운영자 정보 관리 및 생성, 운영자 별 메뉴 사용권한/접속제한 등을 설정할 수 있습니다.</li>
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
