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
$text1=$_POST["text1"];
$text2=$_POST["text2"];
$text3=$_POST["text3"];
$up_memberout_type=$_POST["up_memberout_type"];
$up_resno_type=$_POST["up_resno_type"];
$up_resno_type2=$_POST["up_resno_type2"];
$up_shop_mem_type=$_POST["up_shop_mem_type"];

if($type=="up"){
	if($up_resno_type=="Y" && $up_resno_type2=="N") $up_resno_type="Y";
	else if($up_resno_type=="Y" && $up_resno_type2=="Y") $up_resno_type="M";
	else if($up_resno_type=="N") $up_resno_type="N";

	$count=0;
	$temparray1 = explode("",$text1);
	$temparray2 = explode("",$text2);
	$temparray3 = explode("",$text3);
	$cnt=count($temparray1);
	for($i=1;$i<=$cnt;$i++){
		$temp1=trim($temparray1[$i]); $temp2=trim($temparray2[$i]); $temp3=trim($temparray3[$i]);
		if(ord($temp1) && ord($temp2) && ord($temp3)){
			if($count!=0) $temp.="=";
			$count++;
			$temp.=$temp1."={$temp2}=".$temp3;
		}
	}
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "memberout_type		= '{$up_memberout_type}', ";
	$sql.= "resno_type			= '{$up_resno_type}', ";
	$sql.= "shop_mem_type		= '{$up_shop_mem_type}', ";
	$sql.= "member_addform		= '{$temp}' ";
	
	$update = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('회원가입 관련 설정이 완료되었습니다.'); }</script>";
}

$sql = "SELECT memberout_type,resno_type,member_addform,shop_mem_type FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$memberout_type = $row->memberout_type;
$resno_type = $row->resno_type;
$shop_mem_type = $row->shop_mem_type;
if ($resno_type=="Y") $resno_type2="N";
else if ($resno_type=="M") {
	$resno_type="Y";
	$resno_type2="Y";
} else {
	$resno_type2="N";
}
if (strlen($row->member_addform)!=0) {
	$fieldarray=explode("=",$row->member_addform);
	$num=sizeof($fieldarray)/3;
	for($i=0;$i<$num;$i++){
		$field_length1[$i]=$fieldarray[$i*3+1];
		$max_length1[$i]=$fieldarray[$i*3+2];
		if (substr($fieldarray[$i*3],-1,1)=="^") {
			$field_name1[$i] = substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1);
			$field_check[$i] = "Y";
		} else {
			$field_name1[$i] = $fieldarray[$i*3];
			$field_check[$i] = "N";
		}
	}
}
pmysql_free_result($result);

${"check_resno_type".$resno_type} = "checked";
${"check_resno_type2".$resno_type2} = "checked";
${"check_memberout_type".$memberout_type} = "checked";
${"check_shop_mem_type".$shop_mem_type} = "checked";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	var isMax=false;
	form.text1.value="";
	form.text2.value="";
	form.text3.value="";
	for(i=0;i<form.field_name.length;i++){
		if(isNaN(form.field_length[i].value)){
			alert("필드 길이는 숫자만 입력 가능합니다.");
			form.field_length[i].focus();
			return;
		}
		if(isNaN(form.max_length[i].value)){
			alert("필드 최대길이는 숫자만 입력 가능합니다.");
			form.max_length[i].focus();
			return;
		}
		if(form.field_name[i].value.indexOf('')>=0){
			alert("'' 문자는 입력하실 수 없습니다.");
			form.field_name[i].focus();
			return;
		}
		if(form.field_name[i].value.indexOf('^')>=0){
			alert("'^' 문자는 입력하실 수 없습니다.");
			form.field_name[i].focus();
			return;
		}
		if(form.field_length[i].value>40){
			alert("필드길이는 최대 40까지 가능합니다.");
			form.field_length[i].focus();
			return;
		}
		if((form.field_name[i].value.length!=0 && form.field_length[i].value.length==0) || (form.field_name[i].value.length==0 && form.field_length[i].value.length!=0)){
			alert("추가입력폼 입력이 잘못되었습니다.\n\n다시 확인하시기 바랍니다.");
			if(form.field_length[i].value.length==0) form.field_length[i].focus();
			else form.field_name[i].focus();
			return;
		}
		if(form.field_name[i].value.length!=0 && form.field_length[i].value.length!=0 && form.max_length[i].value.length==0){
			isMax=true;
			form.max_length[i].value=form.field_length[i].value;
		}
		if(form.field_name[i].value.length!=0 && form.field_length[i].value.length!=0 && form.max_length[
		i].value.length!=0){
			if (form.field_check[i].checked) {
				chk_val = '^';
			} else {
				chk_val = '';
			}
			form.text1.value=form.text1.value+""+form.field_name[i].value+chk_val;
			form.text2.value=form.text2.value+""+form.field_length[i].value;
			form.text3.value=form.text3.value+""+form.max_length[i].value;
		}
	}
	if (isMax) {
		if (!confirm("최대 길이를 입력하지 않으시면 필드길이와 같은 값으로 입력됩니다.")) {
			return;
		}
	}
	form.type.value="up";
	form.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>회원관련 설정</span></p></div></div>
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
					<div class="title_depth3">회원가입 관련 설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>기본 회원가입 입력폼 + 추가 입력폼, 주민번호 사용 및 탈퇴설정을 할 수 있습니다.</span></div>
					
				</td>
			</tr>
            <tr>
            	<td><div class="title_depth3_sub">회원가입 추가 입력폼</div>
                </td>
            </tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1) 최대길이를 입력하지 않으면 필드길이와 동일하게 등록됩니다.</li>
						<li>2) 필드의 최대 길이는 250입니다.</li>
                        <li>3) 추가입력폼은 기본입력폼 하단에 표기됩니다.</li>
					</ul>
				</div>				
				</td>
			</tr>    
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=text1>
			<input type=hidden name=text2>
			<input type=hidden name=text3>
			<tr>
				<td>
				
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <col width="40" />
                <col width="" />
                <col width="100" />
                <col width="100" />
                <col width="40" />
				<TR>
					<th>순번</th>
					<th>필드명</th>
					<th>필드길이</th>
					<th>입력최대길이</th>
					<th>필수</th>
				</TR>

<?php
	for($i=0;$i<10;$i++){
		if ($i == 9) {
			$line_bottom = "bottom";
		}
?>
				<tr bgcolor=#ffffff height=25>
					<TD><?=$i+1?></TD>
					<TD><input type=text name=field_name value="<?=$field_name1[$i]?>" maxlength=45 style="width:97%" class="input"></TD>
					<TD><input type=text name=field_length value="<?=$field_length1[$i]?>" maxlength=3 style="width:95%" class="input"></TD>
					<TD><input type=text name=max_length value="<?=$max_length1[$i]?>" maxlength=3 style="width:95%" class="input"></TD>
					<TD><input type=checkbox name=field_check value="Y" <?php if ($field_check[$i]=="Y") echo "checked"; ?>></TD>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
					<dl class="setup_info">
						<dt>회원가입 선택</dt>
						
					</dl>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>회원가입 선택</span></th>
					<TD class="td_con1"><input type=radio id="idx_shop_mem_type1" name=up_shop_mem_type value="0" <?=$check_shop_mem_type0?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_shop_mem_type1>일반회원</label>  &nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_shop_mem_type2" name=up_shop_mem_type value="1" <?=$check_shop_mem_type1?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_shop_mem_type2>일반회원+기업회원</label></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
					<dl class="setup_info">
						<dt>주민번호 입력 선택기능</dt>
						<dd>
							<ul>
								<li>1) <b>14세 미만</b>의 경우는 보호자의 동의가 필요합니다.</li>
								<li>2) <b>주민번호 미입력</b>으로 인한 모든 법적 책임은 쇼핑몰에 있습니다.</li>
							</ul>
						</dd>
					</dl>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>주민번호 입력여부 선택</span></th>
					<TD class="td_con1"><input type=radio id="idx_resno_type1" name=up_resno_type value="Y" <?=$check_resno_typeY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_resno_type1>회원 가입시 주민등록번호 입력</label>  &nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_resno_type2" name=up_resno_type value="N" <?=$check_resno_typeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_resno_type2>회원 가입시 주민등록번호 미입력</label></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주민번호 수정기능 설정<span>회원가입 후 회원정보 수정에서 주민번호의 변경가능 유무를 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>수정여부 선택</span></th>
					<TD class="td_con1"><input type=radio id="idx_resno_type21" name=up_resno_type2 value="Y" <?=$check_resno_type2Y?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_resno_type21>회원이 주민등록번호 수정가능</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_resno_type22" name=up_resno_type2 value="N" <?=$check_resno_type2N?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_resno_type22>입력된 주민등록번호 수정불가능</label></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
					<dl class="setup_info">
						<dt>회원탈퇴 기능 설정</dt>
						<dd>
							<ul>
								<li>1) 회원이 스스로 회원탈퇴를 할 수 있는 메뉴를 <B>[회원정보수정]</B> 화면에 추가할 수 있습니다.</li>
								<li>2) 탈퇴기능을 사용할 경우 [회원정보수정] 화면에 표시됩니다.</li>
								<li>3) 개별디자인시에는 왼쪽, 상단, 로그인폼 관리에서 해당 링크 추가가 가능합니다.</li>
							</ul>
						</dd>
					</dl>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>회원탈퇴 기능 선택</span></th>
					<TD class="td_con1"><input type=radio id="idx_memberout_type1" name=up_memberout_type value="Y" <?=$check_memberout_typeY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memberout_type1>관리자인증후 회원탈퇴</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_memberout_type2" name=up_memberout_type value="O" <?=$check_memberout_typeO?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memberout_type2>자동 회원 탈퇴</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_memberout_type3" name=up_memberout_type value="N" <?=$check_memberout_typeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memberout_type3>회원 탈퇴메뉴 사용안함</label></TD>
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
							<dt><span>주민번호 미사용 설정 특성</span></dt>
							<dd>- 주민번호 미사용시 <b>[아이디/비빌번호찾기]</b> 는 이름과 가입메일주소로 찾을 수 있습니다.<br>
- 실명인증서비스를 받고 있는 경우라도 실명인증이 연결되지 않습니다.<br>
- 실명인증서비스를 받는 도중 잠시 실명인증을 하지 않거나 실명인증 사용 월정액이 초과되어 추가비용 발생시<br><b>&nbsp;&nbsp;</b>주민번호 미사용으로 설정하면 실명인증서비스 받지 않을 수 있습니다.

							</dd>
						</dl>
						<dl>
							<dt><span>회원탈퇴 처리 특성</span></dt>
							<dd>- 관리자인증 후 탈퇴인 경우 관리자가 탈퇴 인증을 하면 회원리스트에서 즉시 삭제됩니다.<br>
							- 자동 탈퇴일 경우는 회원리스트에서 즉시 삭제됩니다.<br>
							- 삭제된 회원정보는 복구되지 않습니다.<Br>
							- 탈퇴된 회원의 주문리스트는 별도 삭제해주세요.<br>
							- 거래정보 확인을 위해 회원탈퇴 후 거래내역정보 등의 보관 기간을 회원가입 또는 개인정보보호정책에 표명을 권장합니다.<br>
							<b>&nbsp;&nbsp;</b>예) 상법 등 관련법령의 규정에 의하여 다음과 같이 거래 관련 권리 의무 관계의 확인 등을 이유로 일정기간 보유합니다.<br>
							<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 계약 또는 청약철회 등에 관한 기록 : 5년<br>
							<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 대금결제 및 재화등의 공급에 관한 기록 : 5년<br>
							<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 소비자의 불만 또는 분쟁처리에 관한 기록 : 3년
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
