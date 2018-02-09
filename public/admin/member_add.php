<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$selected[group]['0002']="selected";
################## 회원 그룹 쿼리 ################
$groupname='';

$group_qry="select group_name,group_code from tblmembergroup order by group_level";
$group_result=pmysql_query($group_qry);

################## 가입경로 쿼리 ################
$referer1 = '';
$referer2 = '';
$ref_qry="select idx,name from tblaffiliatesinfo order by name";
$ref1_result=pmysql_query($ref_qry);
$ref2_result=pmysql_query($ref_qry);

include("header.php");
?>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
 function fn_press_han(obj)
{
	//좌우 방향키, 백스페이스, 딜리트, 탭키에 대한 예외
	if(event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39	|| event.keyCode == 46 ) return;
	obj.value = obj.value.replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g, '');
}

function ValidFormId() { //아이디 유효성 체크
	var val	= $("input[name=id]").val();
	if (val == '') {
		alert($("input[name=id]").attr("title"));
		$("input[name=id]").focus();
		return;
	} else {
		if (!(new RegExp(/^[a-zA-Z0-9]{5,16}$/)).test(val)) {
			alert("5~16자 이내 영문과 숫자만 사용 가능합니다.");
			$("input[name=id]").focus();
			return;
		} else {
			$.ajax({ 
				type: "GET", 
				url: "<?=$Dir.FrontDir?>iddup.proc.php", 
				data: "id=" + val + "&mode=id",
				dataType:"json", 
				success: function(data) {
					$("#id_checked").val(data.code);
					if (data.code == 0) {
						alert(data.msg);
						$("input[name=id]").focus();
						return;
					} else {
						ValidFormName();
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다."); 
					$("input[name=id]").focus();
					return;
				}
			}); 
		}
	}
}

function ValidFormName(){ //이름 유효성 체크
	var val			= $("input[name=name]").val();

	if (val == '') {
		alert($("input[name=name]").attr("title"));
		$("input[name=name]").focus();
		return;
	} else {

		// 한글 이름 2~4자 이내
		// 영문 이름 2~10자 이내 : 띄어쓰기(\s)가 들어가며 First, Last Name 형식
		// 한글 또는 영문 사용하기(혼용X)

		if (!(new RegExp(/^[가-힣]{2,4}|[a-zA-Z]{2,10}\s[a-zA-Z]{2,10}$/)).test(val)) {
			alert("한글(2~4자 이내) 또는 영문(2~10자 이내)으로 사용 가능합니다.");
			$("input[name=name]").focus();
			return;
		} else {
			$("#name_checked").val("1");
			ValidFormPassword();
		}
	}
}

function ValidFormPassword(){//비밀번호 유효성 체크
	var val	= $("input[name=passwd]").val();
	if (val == '') {
		alert($("input[name=passwd]").attr("title"));
		$("input[name=passwd]").focus();
		return;
	} else {
		if (!(new RegExp(/^.*(?=.{8,20})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).*$/)).test(val)) {
			alert("8~20자 이내 영문, 숫자, 특수문자(!@#$%^&amp;*) 3가지 조합으로 이루어져야 합니다.");
			$("input[name=passwd]").focus();
			return;
		} else {
			$("#passwd1_checked").val("1");
			ValidFormAddr();
		}
	}
}

function ValidFormAddr(){//주소 유효성 체크
	var home_zonecode	= $("input[name=home_zonecode]").val();
	var home_post1			= $("input[name=home_post1]").val();
	var home_post2			= $("input[name=home_post2]").val();
	var home_addr1			= $("input[name=home_addr1]").val();
	var home_addr2			= $("input[name=home_addr2]").val();
	if (home_zonecode == '' || home_addr1 == '' || home_addr2 == '') {
		alert($("input[name=home_addr2]").attr("title"));
		$("input[name=home_addr2]").focus();
		return;
	} else {
		$("#home_addr_checked").val("1");
		ValidFormMobile();
	}
}

function ValidFormMobile() {//휴대폰번호 체크
	var mobile2			= $("#mobile2").val();
	var mobile3			= $("#mobile3").val();

	if (mobile2 == '' || mobile3 == '') {
		alert($("#mobile3").attr("title"));
		if (mobile2 == '') {
			$("#mobile2").focus();
			return;
		} else if (mobile3 == '') {
			$("#mobile3").focus();
			return;
		}
	} else {
		$("#mobile_checked").val("1");
		ValidFormEmail();
	}
}

function ValidFormEmail() {//이메일 유효성 체크
	var val			= $("input[name=email]").val();

	if (val == '') {
		alert($("input[name=email]").attr("title"));
		$("input[name=email]").focus();
		return;
	} else {
		if (!(new RegExp(/^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$/)).test(val)) {
			alert("잘못된 이메일 형식입니다.");
			$("input[name=email]").focus();
			return;
		} else {
			$.ajax({ 
				type: "GET", 
				url: "<?=$Dir.FrontDir?>iddup.proc.php", 
				data: "email=" + val + "&mode=email",
				dataType:"json", 
				success: function(data) {
					$("#email_checked").val(data.code);
					if (data.code == 0) {
						alert(data.msg);
						$("input[name=email]").focus();
						return;
					} else {
						CheckFormSubmit();
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다."); 
					$("input[name=email]").focus();
					return;
				}
			}); 
		}
	}
}

function chk_writeForm() {
	form=document.form1;
	form.id.value = form.id.value.replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g, '');
	ValidFormId();
}

function CheckFormSubmit(){
	form=document.form1;

	var id_checked				= $("input[name=id_checked]").val();
	var passwd1_checked		= $("input[name=passwd1_checked]").val();
	var name_checked			= $("input[name=name_checked]").val();
	var home_addr_checked	= $("input[name=home_addr_checked]").val();
	var email_checked			= $("input[name=email_checked]").val();
	var mobile_checked		= $("input[name=mobile_checked]").val();
	var id								= $("input[name=id]").val();

	//alert(id_checked+"\n"+passwd1_checked+"\n"+name_checked+"\n"+home_addr_checked+"\n"+email_checked+"\n"+mobile_checked);return;
	if (id_checked == '1' && passwd1_checked == '1' && name_checked == '1' && home_addr_checked == '1' && email_checked == '1' && mobile_checked == '1')
	{
		if(confirm(id+" 회원을 추가하시겠습니까?"))
			form.submit();
		else
			return;
	} else {
		return;
	}
}

function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			document.getElementById('home_zonecode').value = data.zonecode;
			document.getElementById('home_post1').value = data.postcode1;
			document.getElementById('home_post2').value = data.postcode2;
			document.getElementById('home_addr1').value = data.address;
			document.getElementById('home_addr2').value = '';
			document.getElementById('home_addr2').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;

			
		}
	}).open();
}

// -->
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript" src="<?=$Dir.BoardDir?>chk_form.js.php"></SCRIPT>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>

<script type="text/javascript" src="lib.js.php"></script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원정보 관리 &gt;<span>회원정보 추가</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<form name=form1 method=post action="member_write_indb.php">
<input type="hidden" name="mode" value="insert">
<input type=hidden name=id_checked id=id_checked value="0">
<input type=hidden name=passwd1_checked id=passwd1_checked value="0">
<input type=hidden name=name_checked id=name_checked value="0">
<input type=hidden name=home_addr_checked id=home_addr_checked value="0">
<input type=hidden name=email_checked id=email_checked value="0">
<input type=hidden name=mobile_checked id=mobile_checked value="0">
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">회원 정보 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>회원정보를 추가할 수 있습니다.</span></div>
				</td>
			</tr>
			<TR><td><div class="title_depth3_sub">회원정보 추가</div></td></tr>
			<tr>
				<td>

					<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

					<TR>
						<th style="width:140px"><span>아이디</span></th>
						<TD class="td_con1" align="center" style="width:400px"><p align="left"><input type=text name=id class="input" style="height:25px" onkeydown="fn_press_han(this);"  onkeyup="fn_press_han(this);"  title='아이디를 입력해주세요.'></TD>
						<th style="width:140px"><span>그룹</span></th>
						<TD class="td_con1" align="center"><p align="left">
							<select name=group_code class="input" style="height:25px">
							<?while($group_data=pmysql_fetch_object($group_result)){?>
								<option value="<?=$group_data->group_code?>" <?=$selected[group][$group_data->group_code]?>><?=$group_data->group_name?></option>
							<?}?>
							</select>
						</TD>
					</tr>

					<TR>
						<th style="width:140px"><span>이름</span></th>
						<TD class="td_con1" align="center"><p align="left"><input type=text name=name value="<?=$data->name?>" class="input" style="height:25px" title="이름을 입력하세요."></TD>
						<th style="width:140px"><span>비밀번호</span></th>
						<TD class="td_con1" align="center"><p align="left">
							<input type=text name=passwd class="input" style="height:25px" title="비밀번호를 입력하세요.">
						</TD>
					</tr>
					<TR>
						<th style="width:140px"><span>주소</span></th>
						<TD class="td_con1" align="center" colspan=4><p align="left">
							<INPUT type=text name="home_zonecode" id="home_zonecode" value="<?=$home_zonecode?>" readOnly style="WIDTH:40px;" class="input">
							<INPUT type=hidden name="home_post1" id="home_post1" value="<?=$home_post1?>" readOnly style="WIDTH:40px;" class="input"><INPUT type=hidden name="home_post2" id="home_post2" value="<?=$home_post2?>" readOnly style="WIDTH:40px;" class="input">
                            <A href="javascript:openDaumPostcode();" onfocus="this.blur();" style="selector-dummy: true" class="board_list hideFocus">
							<img src="images/icon_addra.gif" border="0" align="absmiddle" hspace="3"></a><br>
							<INPUT type=text name="home_addr1" id="home_addr1" value="<?=$home_addr1?>" maxLength="100" readOnly style="WIDTH:300px;height:25px" class="input" title="주소를 입력해 주세요.">
							<INPUT type=text name="home_addr2" id="home_addr2" value="<?=$home_addr2?>" maxLength="100" style="WIDTH:500px;height:25px" class="input" title="주소를 입력해 주세요.">
						</TD>
					</tr>
					<TR>
						<th style="width:140px"><span>휴대폰번호</span></th>
						<TD class="td_con1" align="center" style="width:400px"><p align="left">

							<select name="mobile[]" id="mobile1" class="input" style="height:25px">
								<option value="010" <?=$selected[mobile]["010"]?>>010&nbsp;&nbsp;&nbsp;</option>
								<option value="011" <?=$selected[mobile]["011"]?>>011</option>
								<option value="016" <?=$selected[mobile]["016"]?>>016</option>
								<option value="017" <?=$selected[mobile]["017"]?>>017</option>
								<option value="018" <?=$selected[mobile]["018"]?>>018</option>
								<option value="019" <?=$selected[mobile]["019"]?>>019</option>

							</select>
							- <input type="text" name="mobile[]" id="mobile2" maxlength="4" value="<?=$mobile[1]?>" size="10" style="ime-mode:disabled;height:25px" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="input"/>
							- <input type="text" name="mobile[]" id="mobile3" maxlength="4" value="<?=$mobile[2]?>" size="10" style="ime-mode:disabled;height:25px" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="input" title="휴대폰번호를 입력해 주세요."/>
							<input type="checkbox" name="news_sms_yn" id="idx_news_sms_yn" value="Y" <?if($news_sms_yn=="Y")echo"checked";?>  />
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn">SMS수신</LABEL>
						</TD>
						<th style="width:140px"><span>이메일</span></th>
						<TD class="td_con1" align="center"><p align="left">
							<input type=text name=email value="<?=$data->email?>" style="width:250px;height:25px;" class="input" title="이메일을 입력해 주세요.">
							<input type="checkbox"  name="news_mail_yn" id="idx_news_mail_yn" value="Y"  <?if($news_mail_yn=="Y")echo"checked";?>/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn">메일정보 수신</LABEL>

						</TD>
					</tr>
					<TR>
						<th style="width:140px"><span>생년월일</span></th>
						<TD class="td_con1">
							<input name="birth" title="년도를 입력하세요." onclick="Calendar(event)" type="text" size="12" maxlength="12" value="<?=$data->birth?>" label="생년월일" class="input" style="height:25px">
						</TD>
						<th><span>기념일</span></th>
						<td style="border-left: 1px solid #ededed;">
							<input name="married_date" class="w100" required="" onclick="Calendar(event)" type="text" size="12" maxlength="12" readonly="" value="<?=$data->married_date?>" label="기념일">
						</td>
					</tr>
					</TABLE>
					</div>

				</td>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align=center>
					<a href="javascript:chk_writeForm();"><img src="/admin/images/botteon_save.gif"></a>
					<a href="../admin/member_list.php"><img src="img/btn/btn_list.gif"></a>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>회원정보 추가</span></dt>
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
	</form>
	</table>

<form name=form3 method=post>
<input type=hidden name=id>
</form>
<?=$onload?>
<?php
include("copyright.php");
?>
