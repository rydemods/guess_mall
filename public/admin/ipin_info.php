<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$ipin=pmysql_fetch_object(pmysql_query("select ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));

$checked[ipin_check][$ipin->ipin_check]="checked";
//$checked[ipin_adult_check][$ipin->ipin_adult_check]="checked";

if(!$ipin->ipin_check) $disabled_type="disabled";


?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function CheckForm() {
	form=document.form1;
	
	if(!form.ipin_id.value){
		alert("ID를 입력하여주십시요.");
		form.ipin_id.focus();
	}else if(!form.ipin_password.value){
		alert("비밀번호를 입력하여주십시요.");
		form.ipin_password.focus();
	}
	form.submit();
}

function radio_open(type){
	if(type=='1'){
		document.getElementById("ipin_adult_check1").disabled=false;
		document.getElementById("ipin_adult_check0").disabled=false;
	
	}else{
		document.getElementById("ipin_adult_check1").disabled=true;
		document.getElementById("ipin_adult_check0").disabled=true;
	}
}
</script>

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 실명확인/아이핀 &gt;<span>아이핀 관리</span></p></div></div>

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
			<table width="100%" cellpadding="0" cellspacing="0">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">아이핀 관리</div>
				</td>
			</tr>
			
			<tr><td height="3"></td></tr>
			<form name=form1 action="realname_indb.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=mode value="ipin">
			<tr><td height="20"></td></tr>
			<tr>
				<td>
					<div class="table_style01">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
                        <th><span>회원사 ID</span></th>
						<td class="td_con1"><input type=text name="ipin_id" value="<?=$ipin->ipin_id?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"></td>
					</tr>
					<tr>
                        <th><span>회원사 Password</span></th>
						<td class="td_con1"><input type=text name="ipin_password" value="<?=$ipin->ipin_password?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"></td>
					</tr>
					<tr>
						<th><span>실명확인여부</span></th>
						<td class="td_con1">
							<input type=radio name="ipin_check" value="1" <?=$checked[ipin_check]['1']?>> 사용 
							<input type=radio name="ipin_check" value="0" <?=$checked[ipin_check]['0']?>> 사용안함</td>
					</tr>
					<!--
					<tr>
						<th><span>성인인증여부</span></th>
						<td class="td_con1">
							<input type=radio name="ipin_adult_check" id="ipin_adult_check1" value="1" <?=$disabled_type?> <?=$checked[ipin_adult_check]['1']?>> 사용(19세미만 회원가입 불가) 
							<input type=radio name="ipin_adult_check" id="ipin_adult_check0" value="0" <?=$disabled_type?> <?=$checked[ipin_adult_check]['0']?>> 사용안함</td>
					</tr>
					-->
				</table>
                </div>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			</form>
			
			<tr>
				<td>

					<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>-</span></dt>
							<dd>
								-
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
