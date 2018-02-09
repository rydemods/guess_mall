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
$roleidx=$_POST["roleidx"];
$description=$_POST["description"];
$taskidxs=$_POST["taskidxs"];
$disabled=(int)$_POST["disabled"];

if ($mode=="edit") {
	$sql = "SELECT description, disabled FROM tblsecurityrole ";
	$sql.= "WHERE idx = '{$roleidx}'";
	$result = pmysql_query($sql,get_db_conn());
	$rows = pmysql_num_rows($result);
	if ($rows > 0) {
		$row = pmysql_fetch_object($result);
		$description = $row->description;
		$disabled = (int)$row->disabled;
	} else {
		alert_go('수정할 권한그룹 정보가 존재하지 않습니다.');
	}
	pmysql_free_result($result);

	${"check_".$disabled} = "checked";

	$sql = "SELECT taskidx FROM tblsecurityroletask WHERE roleidx = '{$roleidx}'";
	$result = pmysql_query($sql,get_db_conn());
	$taskarray = array();
	while($row = pmysql_fetch_object($result))
		$taskarray[$row->taskidx] = true;
	pmysql_free_result($result);


	if($description == "Administrator") {
		$disabled_disabled = "disabled";
	}
} else if ($mode=="del") {
	$sql = "SELECT description FROM tblsecurityrole WHERE idx = '{$roleidx}'";
	$result = pmysql_query($sql,get_db_conn());
	$rows = pmysql_num_rows($result);
	if ($rows > 0) {
		$row = pmysql_fetch_object($result);
		$description = $row->description;
	} else {
		alert_go('삭제할 권한그룹 정보가 존재하지 않습니다.');
	}
	pmysql_free_result($result);

	//tblsecurityadminrole 테이블에 해당 roleidx가 있는지 검사한다.
	$sql = "SELECT idx FROM tblsecurityadminrole WHERE roleidx = '{$roleidx}' ";
	$result = pmysql_query($sql,get_db_conn());
	$flag = (boolean)pmysql_num_rows($result);
	pmysql_free_result($result);

	if ($flag) {
		alert_go("삭제하려는 접근권한 그룹은 현재 사용중입니다.\\n\\n운영자/부운영자 설정에서 해당 권한그룹을 사용중인 운영자 정보를 변경하신 후 삭제하시기 바랍니다.");
	}

	//없다면 tblsecurityrole 테이블 해당 idx 레코드 삭제
	$sql = "DELETE FROM tblsecurityrole WHERE idx = '{$roleidx}' ";
	pmysql_query($sql,get_db_conn());

	//tblsecurityroletask 테이블 roleidx에 해당하는 레코드 삭제
	$sql = "DELETE FROM tblsecurityroletask WHERE roleidx = '{$roleidx}' ";
	pmysql_query($sql,get_db_conn());

	alert_go("해당 권한그룹 ({$description})을 삭제하였습니다.");
}

if ($type=="insert") {
	$sql = "SELECT description FROM tblsecurityrole WHERE description = '{$description}' ";
	$result = pmysql_query($sql,get_db_conn());
	$rows = pmysql_num_rows($result);
	pmysql_free_result($result);
	if ($rows>0) {
		alert_go('입력하신 권한 그룹명은 현재 사용중입니다.');
	}

	$sql = "INSERT INTO tblsecurityrole (description,disabled) VALUES ('{$description}','{$disabled}')";
	$insert = pmysql_query($sql,get_db_conn());

	if ($insert) {
		//$qry = "SELECT LAST_INSERT_ID() ";
        $qry = "SELECT currval(pg_get_serial_sequence('tblsecurityrole','idx'))";
		$res = pmysql_fetch_row(pmysql_query($qry,get_db_conn()));
		$roleidx = $res[0];

		for($i = 0; $i < count($taskidxs); $i++) {
			$taskidx = $taskidxs[$i];

			if ($taskidxs[$i] != "") {
				$sql = "INSERT INTO tblsecurityroletask (roleidx,taskidx) VALUES ('{$roleidx}','{$taskidx}')";
				$insert = pmysql_query($sql,get_db_conn());
				if ($insert) {
					if ($taskidx == 0) {
						break;
					}
				}
			}
		}
	}

	alert_go('접근권한 그룹 추가가 완료되었습니다.');
} else if ($type=="edit") {
	$taskarray = array();
	$allowalltask = false;
	for($i = 0; $i < count($taskidxs); $i++) {
		if ($taskidxs[$i] != "") {
			$taskidx = $taskidxs[$i];
			if($taskidx == 0)
				$allowalltask = true;
			if($taskidx > 0)
				$taskarray[$taskidx] = true;
		}
	}

	$sql = "SELECT description FROM tblsecurityrole ";
	$sql.= "WHERE idx != {$roleidx} AND description = '{$description}'";
	$result = pmysql_query($sql,get_db_conn());
	$rows = pmysql_num_rows($result);
	pmysql_free_result($result);
	if ($rows>0) {
		alert_go('입력하신 권한 그룹명은 현재 사용중입니다.');
	}

	$sql = "DELETE FROM tblsecurityroletask WHERE roleidx = '{$roleidx}'";
	$delete = pmysql_query($sql,get_db_conn());

	if ($delete) {
		$sql = "UPDATE tblsecurityrole SET description='{$description}', disabled={$disabled} ";
		$sql.= "WHERE idx = '{$roleidx}'";
		$update = pmysql_query($sql,get_db_conn());
		if($allowalltask) {
			$sql = "INSERT INTO tblsecurityroletask (roleidx,taskidx) VALUES ('{$roleidx}',0)";
			pmysql_query($sql,get_db_conn());
		} else {
			foreach( $taskarray as $k1=>$v1 ) {
				$taskidx = $k1;
				if($taskidx > 0) {
					$sql = "INSERT INTO tblsecurityroletask (roleidx,taskidx) VALUES ('{$roleidx}','{$taskidx}')";
					pmysql_query($sql,get_db_conn());
				}
			}
		}
	}
	alert_go('접근권한 그룹 수정이 완료되었습니다.');
}

$mode = $mode ? $mode : "insert";
if ($mode=="edit") {
	if ($disabled_disabled) {
		$button_value = "images/btn_edit1.gif";
	} else {
		$button_value = "images/btn_edit2.gif";
	}
} else if ($mode=="insert") {
	if ($disabled_disabled) {
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
	if(form1.description.value.length<=0) {
		alert("접근권한 그룹명을 입력하세요.");
		form1.description.focus();
		return;
	}

	var moreselect = true;
	var anyselected = false;
	if (form1["taskidxs[]"].options[0].selected) {
		moreselect = false;
		anyselected = true;
	}
	for(var i=1;i<form1["taskidxs[]"].length;i++){
		if ((form1["taskidxs[]"].options[i].selected) && !moreselect ) {
			alert("모든권한 [ All ] 선택시에는 다른 메뉴를 선택할 필요가 없습니다.");
			return;
		} else {
			if(form1["taskidxs[]"].options[i].selected && form1["taskidxs[]"].options[i].value) {
				anyselected = true;
			}
		}
	}
	if (!anyselected ) {
		alert("접근가능한 소메뉴를 하나이상 선택하셔야 합니다.");
		form1["taskidxs[]"].focus();
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

function check_form(mode,roleidx) {
	if (mode=="del") {
		var con=confirm("해당 접근권한 그룹을 삭제 하시겠습니까?");
		if (!con) {
			return false;
		}
	}
	form2.mode.value=mode;
	form2.roleidx.value=roleidx;
	form2.submit();
}
//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 보안설정 &gt;<span>그룹 및 권한 설정</span></p></div></div>
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
					<div class="title_depth3">그룹 및 권한 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>관리페이지 메뉴별 접근권한 그룹을 관리합니다.</span></div>
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
			<input type=hidden name=roleidx value="<?=$roleidx?>">
			<tr>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th>접근권한 그룹명</th>
					<th>사용여부</th>
					<th>그룹에 속한 운영자/부운영자</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
			$count = 0;
			$sql = "SELECT idx as roleidx, description, disabled ";
			$sql.= "FROM tblsecurityrole ORDER BY idx DESC ";
			$result = pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$count++;

				echo "<tr>\n";
				echo "	<TD><span class=font_orange>{$count} &nbsp;-&nbsp; <B>{$row->description}</B></span></td>\n";
				if ($row->disabled == 0) {
					echo "	<TD>사용함</td>\n";
				} else {
					echo "	<TD>사용안함</td>\n";
				}

				$allowedadmins = "";
				$sql = "SELECT a.id FROM tblsecurityadmin a, tblsecurityadminrole r ";
				$sql.= "WHERE a.id = r.id AND r.roleidx = {$row->roleidx}";
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
					echo "	<TD>해당 그룹에 포함된 부운영자가 없습니다.</td>\n";
				}
				echo "	<TD><a href=\"javascript:check_form('edit','{$row->roleidx}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></td>\n";
				if ($row->description == "Administrator") {
					echo "	<TD><img src=\"images/btn_del1.gif\" border=\"0\"></td>\n";
				} else {
					echo "	<TD><a href=\"javascript:check_form('del','{$row->roleidx}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></td>\n";
				}
				echo "</tr>\n";
			}
			if ($count == 0) {
				echo "<tr>\n";
				echo "	<TD colspan=\"5\">등록된 권한 그룹 정보가 없습니다.</td>\n";
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
					<div class="title_depth3_sub">접근권한 그룹 정보</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 해당 그룹의<b> <span class=font_orange>접근 가능한 메뉴를 선택</span></b>하십시요. </li>
                            <li>2) 관리페이지의 모든권한의 그룹을 생성하시려면<b> &quot;All&quot;</b>을 선택하십시요.</li>
                        </ul>
                    </div>
                </td>
            </tr>                
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>접근권한 그룹명</span></th>
					<TD class="td_con1"><input type=text name=description value="<?=$description?>" size=25 <?=$disabled_disabled?> class="input"></TD>
				</TR>
				<TR>
					<th><span>접근 가능메뉴 선택</span></th>
					<TD>
<?php
				if ($mode=="edit") {
					echo "<select name=\"taskidxs[]\" size='20' style=\"WIDTH:100%;\" class=\"textarea\" multiple ";
					if($description == "Administrator")
						echo " disabled";
?>
						>
<?php
					if($taskarray[0]) {
						echo "<option value='0' selected>All";
						$all = true;
					} else {
						echo "<option value='0' >All";
						$all = false;
					}

					$sql = "SELECT b.idx as taskidx, b.description, b.taskcode, a.taskgroupcode as taskcat ";
					//$sql.= "FROM tblsecuritytaskgroup a, tblsecuritytask b WHERE a.idx = b.taskgroupidx ORDER BY b.taskgroupidx,b.taskorder ASC";
                    $sql.= "FROM tblsecuritytaskgroup a, tblsecuritytask b WHERE a.idx = b.taskgroupidx and a.display = 'Y' and b.showmenu = 1 ORDER BY b.taskgroupidx,b.taskorder ASC";
                    //exdebug($sql);
					$result = pmysql_query($sql,get_db_conn());
					$temptaskidx = 0;
					$ltaskgroup = "";
					$taskgroup = "";
					$count = 0;
					while($row = pmysql_fetch_object($result)) {
						$count++;
						$taskidx = $row->taskidx;
						$tdescription = $row->description;
						$taskcode = $row->taskcode;

						if(!$all) {
							foreach( $taskarray as $k1=>$v1 ) {
								$temptaskidx = $k1;
								if($taskidx == $temptaskidx) {
									$selected = true;
								}
							}
						}
						$taskgroup = $row->taskcat;

						if($count == 1)
							$ltaskgroup = $taskgroup;
						if($count == 1 || $ltaskgroup != $taskgroup)
						if($count == 1)
							$ltaskgroup = $taskgroup;
						if($count == 1 || $ltaskgroup != $taskgroup) {
							$sql2 = "SELECT * FROM tblsecuritytaskgroup WHERE taskgroupcode = '{$taskgroup}' ";
							$result2 = pmysql_query($sql2,get_db_conn());
							$row2 = pmysql_fetch_object($result2);
							pmysql_free_result($result2);
							if($row2->taskgroupcode == $taskgroup) {
								echo chr(10).("<option value='' disabled style=\"background:#FF0000;color:#ffffff;\">-------------------[{$row2->taskgroupname}]-------------------");
							} else {
								continue;
							}
						}

						$ltaskgroup = $taskgroup;
						if($selected)
							echo chr(10).("<option value='{$taskidx}' selected >".$tdescription);
						else
							echo chr(10).("<option value='{$taskidx}' >".$tdescription);

						$selected = false;
					}

				} else {
?>
					<select name="taskidxs[]" size='20' style="WIDTH:100%;" class="textarea" multiple>
					<option value='0'>All
<?php
					$sql = "SELECT b.idx as taskidx, b.description, b.taskcode, a.taskgroupcode as taskcat ";
					//$sql.= "FROM tblsecuritytaskgroup a, tblsecuritytask b WHERE a.idx = b.taskgroupidx ORDER BY b.taskgroupidx,b.taskorder ASC";
                    $sql.= "FROM tblsecuritytaskgroup a, tblsecuritytask b WHERE a.idx = b.taskgroupidx and a.display = 'Y' and b.showmenu = 1 ORDER BY b.taskgroupidx,b.taskorder ASC";
                    //exdebug($sql);
					$result = pmysql_query($sql,get_db_conn());
					$temptaskidx = 0;
					$ltaskgroup = "";
					$taskgroup = "";
					$count = 0;
					while($row = pmysql_fetch_object($result)) {
						$count++;
						$taskidx = $row->taskidx;
						$tdescription = $row->description;
						$taskcode = $row->taskcode;

						$taskgroup = $row->taskcat;

						if($count == 1)
							$ltaskgroup = $taskgroup;
						if($count == 1 || $ltaskgroup != $taskgroup)
						if($count == 1)
							$ltaskgroup = $taskgroup;
						if($count == 1 || $ltaskgroup != $taskgroup) {
							$sql2 = "SELECT * FROM tblsecuritytaskgroup WHERE taskgroupcode = '{$taskgroup}' ";
							$result2 = pmysql_query($sql2,get_db_conn());
							$row2 = pmysql_fetch_object($result2);
							pmysql_free_result($result2);
							if($row2->taskgroupcode == $taskgroup) {
								echo chr(10).("<option value='' disabled style=\"background:#FF0000;color:#ffffff;\">-------------------[{$row2->taskgroupname}]-------------------");
							} else {
								continue;
							}
						}

						$ltaskgroup = $taskgroup;
						echo chr(10).("<option value='{$taskidx}' >".$tdescription);

						$selected = false;
					}
				}
?>
				</select>
					</TD>
				</TR>
				<TR>
					<th><span>사용여부</span></th>
					<TD class="td_con1">
						<input type=radio id="idx_disabled1" name=disabled value="0" <?=$check_0?> <?=$disabled_disabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled1>사용함</label>&nbsp;&nbsp;
						<input type=radio id="idx_disabled2" name=disabled value="1" <?=$check_1?> <?=$disabled_disabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled2>사용하지 않음</label>
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height="10">&nbsp;</td>
			</tr>
<?php
	if (!$disabled_disabled) {
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
			<input type=hidden name=roleidx>
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
							<li>권한그룹에서 [administrator] 그룹은 삭제하실 수 없습니다.</li>
							<li>권한 설정은 2뎁스 메뉴까지 설정할 수 있습니다.</li>
							<li>2뎁스 메뉴를 설정 시 1뎁스 메뉴도 함께 체크됩니다.</li>
							<li><b>[ 주문/매출 > 취소/반품/교환/환불 관리 ] 는 CS관리 링크메뉴</b>입니다. CS관리에 권한을 설정하지 않으면, [취소/반품/교환/환불 관리] 메뉴가 노출은 되지만 해당 페이지로 이동하실 수는 없습니다.<br> <b>해당 메뉴를 사용하실 경우 CS관리와 함께 권한설정</b>해주시기 바랍니다.</li>
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
