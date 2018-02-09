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
$id=$_GET[id];
$recommand_type=$_shopdata->recom_ok;
$member_addform=$_shopdata->member_addform;
$exe_mode	= 'insert';
$exe_mode_text	= '추가';
if ($id) {
	$exe_mode	= 'update';
	$exe_mode_text	= '수정';
	### 회원 정보쿼리 ####
	$qry="Select	a.*, b.group_name
        From	tblmember a 
        Join	tblmembergroup b on a.group_code = b.group_code 
        Where	1=1 and id='{$id}'";
	$result=pmysql_query($qry);
	$data=pmysql_fetch_object($result);
	
	$mem_join_type	= '1';
	if ($data->staff_yn == 'Y') $mem_join_type	= '2';
	if ($data->cooper_yn == 'Y') $mem_join_type	= '3';

	$home_tel=explode("-",$data->home_tel);
	$mobile=explode("-",$data->mobile);
	$office_tel=explode("-",$data->office_tel);
	$home_zonecode=$data->home_zonecode;
	$home_post=$data->home_post;
	$home_post1=substr($data->home_post,0,3);
	$home_post2=substr($data->home_post,3,3);
	$home_addr=$data->home_addr;
	$home_addr_temp=explode("↑=↑",$home_addr);
	$home_addr1=$home_addr_temp[0];
	$home_addr2=$home_addr_temp[1];
	$office_post1=substr($data->office_post,0,3);
	$office_post2=substr($data->office_post,3,3);
	$office_addr=$data->office_addr;
	$office_addr_temp=explode("tblpoint",$office_addr);
	$office_addr1=$office_addr_temp[0];
	$office_addr2=$office_addr_temp[1];
	$etc=explode("tblpoint",$data->etcdata);
	$gdn_mobile=explode("-",$data->gdn_mobile);
	$email_arr=explode("@",$data->email);

	$email_domain_arr	= array("naver.com","daum.net","gmail.com","nate.com","yahoo.co.kr","lycos.co.kr","empas.com","hotmail.com","msn.com","hanmir.com","chol.net","korea.com","netsgo.com","dreamwiz.com","hanafos.com","freechal.com","hitel.net");

	if (in_array($email_arr[1], $email_domain_arr)) {
		$email_com	= $email_arr[1];
	} else {
		$email_com	= $email_arr[1]?"custom":"";
	}

	$selected[group][$data->group_code]="selected";
	$selected[home_tel][$home_tel[0]]="selected";
	$selected[mobile][$mobile[0]]="selected";
	$selected[job_code][$data->job_code]="selected";

	// 20170825 수정
	$selected[office][$data->company_code]="selected";


	if($data->news_yn=="Y") {
		$news_mail_yn="Y";
		$news_sms_yn="Y";
	} else if($data->news_yn=="M") {
		$news_mail_yn="Y";
		$news_sms_yn="N";
	} else if($data->news_yn=="S") {
		$news_mail_yn="N";
		$news_sms_yn="Y";
	} else if($data->news_yn=="N") {
		$news_mail_yn="N";
		$news_sms_yn="N";
	}

	$news_kko_yn=$data->kko_yn;
}


################## 회원 그룹 쿼리 ################
$groupname='';

$group_qry="select group_name,group_code from tblmembergroup order by group_level";
$group_result=pmysql_query($group_qry);

################## 20170830 제휴사 쿼리 ################
$c_qry="select group_name,group_code from tblcompanygroup ";
$c_result=pmysql_query($c_qry);

################### 추가정보 ###################
$straddform='';
$scriptform='';
$stretc='';
if(ord($member_addform)) {


	$fieldarray=explode("tblpoint",$member_addform);
	$num=sizeof($fieldarray)/3;
	for($i=0;$i<$num;$i++) {
		if (substr($fieldarray[$i*3],-1,1)=="^") {
			$fieldarray[$i*3]="<img src=\"images/icon_point2.gif\" border=\"0\">".substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1);
			$field_check[$i]="OK";
		}

		$stretc.="<tr>\n";
		$stretc.="	<th style=\"width:140px\"><span>".$fieldarray[$i*3]."</span></th>\n";

		$etcfield[$i]="<input type=text name=\"etc[{$i}]\" value=\"{$etc[$i]}\" size=\"".$fieldarray[$i*3+1]."\" maxlength=\"".$fieldarray[$i*3+2]."\" id=\"etc_{$i}\">";

		$stretc.="	<TD  class=\"td_con1\" align=\"center\"><p align=\"left\">{$etcfield[$i]}</TD>\n";
		$stretc.="</tr>\n";


	}
	$straddform.=$stretc;
}


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
		if (val.match(/[^a-zA-Z0-9]/)!=null) {
			alert("아이디는 숫자와 영문만 입력할 수 있습니다.");			
			$("input[name=id]").focus();
			return;			
		}else if (val.length < 4 || val.length > 20) {
			alert("아이디는 4자 이상, 20자 이하여야 합니다.");
			$("input[name=id]").focus();
			return;		
		} else {
			$.ajax({ 
				type: "GET", 
				url: "../front/iddup.proc.php", 
				data: "id=" + val + "&mode=id",
				dataType:"json", 
				success: function(data) {
					$("#id_checked").val(data.code);
					if (data.code == 0) {
						alert(data.msg);
						$("input[name=id]").focus();
						return;
					} else {
						ValidFormPassword();
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다."); 
					$("input[name=id]").focus();
				}
			}); 
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
		if (!(new RegExp(/^.*(?=.{8,20})(?=.*[a-zA-Z])(?=.*[0-9]).*$/)).test(val)) {
			alert('"8~20자 이내 영문, 숫자 조합으로 이루어져야 합니다.');	
			$("input[name=passwd]").focus();
			return;
		} else {			
			$("#passwd_checked").val("1");
			ValidFormName();
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
			ValidFormBirth();
		}
	}
}

function ValidFormBirth() { //생년월일 유효성 체크
	var val1			= $("input[name=birth1]").val();

	if (val1 == '') {
		alert($("input[name=birth1]").attr("title"));
		$("input[name=birth1]").focus();
		return;
	} else {
		if (val1.length < 8) {
			alert($("input[name=birth1]").attr("title"));
			$("input[name=birth1]").focus();
			return;
		} else {
			$("#birth_checked").val("1");
			ValidFormAddr();
		}
	}
}

function ValidFormAddr(){ // 주소 유효성 체크
	var home_zonecode	= $("input[name=home_zonecode]").val();
	var home_post1			= $("input[name=home_post1]").val();
	var home_post2			= $("input[name=home_post2]").val();
	var home_addr1			= $("input[name=home_addr1]").val();
	var home_addr2			= $("input[name=home_addr2]").val();

	if (home_zonecode != '' || home_addr1 != '' || home_addr2 != '') {
		if (home_zonecode.length > 5) {
			alert("신주소를 입력해 주세요.");
			return;
		} else {
			if (home_addr1 == '' || home_addr2 == '') {
				alert("주소를 입력해 주세요.");
				return;
			} else {
				$("#home_addr_checked").val("1");
				ValidFormMobile();
				return;
			}
		}
	} else {
		$("#home_addr_checked").val("1");
		ValidFormMobile();
		return;
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
		var u_name_val	= $("input[name=name]").val();
		var u_mobile_val	= $("#mobile1 option:selected").val()+$("#mobile2").val()+$("#mobile3").val();
		$.ajax({
			type: "GET",
			url: "<?=$Dir.FrontDir?>iddup.proc.php",
			data: "name=" + u_name_val + "&mobile=" + u_mobile_val + "&mode=erp_mem_chk&access_type=mobile&mem_id=<?=$id?>",
			dataType:"json",
			success: function(data) {
				if (data.code == 0) {
					//alert(data.msg.eshop_id);
					if (data.msg.eshop_id =='') {
						if (confirm("오프라인 매장 회원이십니다. 계속 진행하시겠습니까?")) {	
							$("#mobile_checked").val("1");
							$("form[name=form1]").find("input[name=erp_member_id]").val(data.msg.member_id);	
							$("#mobile_checked").val("1");
							ValidFormEmail();
						} else {
							return;
						}
					} else {					
						alert("통합 회원이십니다. 다른 휴대폰번호로 가입해 주시기 바랍니다.");
						return;
					}
				} else {				
					$("#mobile_checked").val("1");
					ValidFormEmail();
					return;
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다.");
				$("input[name=mobile1]").focus();
				return;
			}
		});
	}
}

function ValidFormEmail() {//이메일 유효성 체크
	var val1	= $("input[name=email1]").val();
	var val2	= $("input[name=email2]").val();
	var mem_join_type	= $("input[name=mem_join_type]:checked").val();
	
	if (mem_join_type=='1') {
		$("#emp_checked").val("1");
		$("#cooper_checked").val("1");
		CheckFormSubmit();
	} else if (mem_join_type=='2') {
		$("#cooper_checked").val("1");
		ValidFormEmp();
	} else if (mem_join_type=='3') {
		$("#emp_checked").val("1");
		ValidFormCooper();
	}

	if (val1 == '' || val2 == '') {
		alert($("input[name=email1]").attr("title"));
		$("input[name=email1]").focus();
		return;
	} else {
		var val = val1 + '@' + val2;
		if (!(new RegExp(/^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$/)).test(val)) {
			alert("잘못된 이메일 형식입니다.");
			$("input[name=email1]").focus();
			return;
		} else {
			$.ajax({ 
				type: "GET", 
				url: "<?=$Dir.FrontDir?>iddup.proc.php", 
				data: "email=" + val + "&mode=email&access_type=mobile&mem_id=<?=$id?>",
				dataType:"json", 
				success: function(data) {
					$("#email_checked").val(data.code);
					if (data.code == 0) {
						alert(data.msg);
						$("input[name=email1]").focus();
						return;
					} else {
						
						if (mem_join_type=='1') {
							$("#emp_checked").val("1");
							$("#cooper_checked").val("1");
							CheckFormSubmit();
						} else if (mem_join_type=='2') {
							$("#cooper_checked").val("1");
							ValidFormEmp();
						} else if (mem_join_type=='3') {
							$("#emp_checked").val("1");
							ValidFormCooper();
						}
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다."); 
					$("input[name=email1]").focus();
					return;
				}
			}); 
		}
	}
}

function ValidFormEmp() { //임직원 유효성 체크
	var val			= $("input[name=emp_id]").val();

	if (val == '') {
		alert($("input[name=emp_id]").attr("title"));
		$("input[name=emp_id]").focus();
		return;
	} else {
		var u_name_val	= $("input[name=name]").val();
		$.ajax({
			type: "GET",
			url: "<?=$Dir.FrontDir?>iddup.proc.php",
			data: "name=" + u_name_val + "&emp_id=" + val + "&mode=erp_emp_chk&access_type=mobile&mem_id=<?=$id?>",
			dataType:"json",
			success: function(data) {
				if (data.code == 0) {
					$("#emp_checked").val("1");
					CheckFormSubmit();
				} else if (data.code == '-1') {			
					alert("등록하신 "+u_name_val+"님의 사번 "+val+" 는(은) 존재하지 않습니다.");
					$("input[name=emp_id]").focus();
					return;
				} else if (data.code == '-2') {			
					alert("등록하신 "+u_name_val+"님의 사번 "+val+" 는(은) 이미 가입된 사번입니다.");
					$("input[name=emp_id]").focus();
					return;
				}
				return;
			},
			error: function(result) {
				alert("에러가 발생하였습니다.");
				$("input[name=emp_id]").focus();
				return;
			}
		});
	}
}

function ValidFormCooper() { //협력업체 유효성 체크
	$("#cooper_checked").val("1");
	CheckFormSubmit();
/*
	var val			= $("input[name=office_name]").val();

	if (val == '') {
		alert($("input[name=office_name]").attr("title"));
		$("input[name=office_name]").focus();
		return;
	} else {
		$("#cooper_checked").val("1");
		CheckFormSubmit();
	}
*/
}

function chk_writeForm() {

	$("input[name=id_checked]").val("<?=$exe_mode=='update'?'1':'0'?>");
	$("input[name=passwd_checked]").val(<?=$exe_mode=='update'?'1':'0'?>);
	$("input[name=name_checked]").val('0');
	$("input[name=birth_checked]").val('0');
	$("input[name=home_addr_checked]").val('0');
	$("input[name=email_checked]").val('0');
	$("input[name=mobile_checked]").val('0');
	$("input[name=emp_checked]").val('0');
	$("input[name=cooper_checked]").val('0');
	<? if( $exe_mode=='update') { ?>
	ValidFormName();
	<? } else { ?>
	ValidFormId();
	<? } ?>
}

function CheckFormSubmit(){
	form=document.form1;

	var id_checked				= $("input[name=id_checked]").val();
	var passwd_checked		= $("input[name=passwd_checked]").val();
	var name_checked			= $("input[name=name_checked]").val();
	var birth_checked			= $("input[name=birth_checked]").val();
	var home_addr_checked	= $("input[name=home_addr_checked]").val();
	var email_checked			= $("input[name=email_checked]").val();
	var mobile_checked		= $("input[name=mobile_checked]").val();
	var emp_checked			= $("input[name=emp_checked]").val();
	var cooper_checked		= $("input[name=cooper_checked]").val();

	/*alert(
		id_checked+"\n"+
		passwd_checked+"\n"+
		name_checked+"\n"+
		birth_checked+"\n"+
		home_addr_checked+"\n"+
		email_checked+"\n"+
		mobile_checked+"\n"+
		emp_checked+"\n"+
		cooper_checked
	);return;*/
	if (id_checked == '1' && passwd_checked == '1' && name_checked == '1' && birth_checked == '1' && home_addr_checked == '1' && email_checked == '1' && mobile_checked == '1' && emp_checked == '1' && cooper_checked == '1')
	{
		if(confirm("<?=$id?> 회원님의 개인정보를 <?=$exe_mode_text?>하시겠습니까?"))
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

function LostPass(id) {
	window.open("about:blank","lostpasspop","width=350,height=200,scrollbars=no");
	document.form3.target="lostpasspop";
	document.form3.id.value=id;
	document.form3.action="member_lostpasspop_new.php";
	document.form3.submit();
}

function customChk(val){

	if((val=='custom')){
		$('#email2').show();
		$('#email2').val('');	
		$('#email2').focus();	
	}else{
		$('#email2').hide();
		$('#email2').val(val);	
	}	
}

function mem_join_type_chk(val) {
	$('.emp_tr').hide();
	$('.cooper_tr').hide();
	if (val == '2') {
		$('.emp_tr').show();
	} else if (val == '3') {
		$('.cooper_tr').show();
	}
}

// -->
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript" src="<?=$Dir.BoardDir?>chk_form.js.php"></SCRIPT>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>

<script type="text/javascript" src="lib.js.php"></script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원정보 관리 &gt;<span>회원정보 수정</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<form name=form1 method=post action="member_write_indb.php">

<input type="hidden" name="mode" value="<?=$exe_mode?>">
<input type="hidden" name="mem_type" value="<?=$exe_mode=='update'?$data->mem_type:'0'?>">
<input type=hidden name=id_checked id=id_checked value="<?=$exe_mode=='update'?'1':'0'?>">
<input type=hidden name=passwd_checked id=passwd_checked value="<?=$exe_mode=='update'?'1':'0'?>">
<input type=hidden name=name_checked id=name_checked value="0">
<input type=hidden name=birth_checked id=birth_checked value="0">
<input type=hidden name=home_addr_checked id=home_addr_checked value="0">
<input type=hidden name=email_checked id=email_checked value="0">
<input type=hidden name=mobile_checked id=mobile_checked value="0">
<input type=hidden name=emp_checked id=emp_checked value="0">
<input type=hidden name=cooper_checked id=cooper_checked value="0">
<input type=hidden name=erp_member_id id=erp_member_id value="">
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
					<div class="title_depth3_sub"><span>회원정보를 <?=$exe_mode_text?>할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr><td><div class="title_depth3_sub">기본정보 <?=$exe_mode_text?></div></td></tr>
			<tr>
				<td>

					<div class="table_style01">
					<table cellSpacing=0 cellPadding=0 width="100%" border=0>
					
					<tr>
						<th><span>구분</span></th>
						<td class="td_con1">
							<p align="left">							
							<input type="radio"  name="mem_join_type" id="idx_mem_join_type1" value="1"  <?=trim($mem_join_type)==''||$mem_join_type=='1'?' checked':''?> onClick="javascript:mem_join_type_chk(this.value);"/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_mem_join_type1">일반</LABEL>
							<input type="radio"  name="mem_join_type" id="idx_mem_join_type2" value="2"  <?=$mem_join_type=='2'?' checked':''?> onClick="javascript:mem_join_type_chk(this.value);"/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_mem_join_type2">임직원</LABEL>
							<input type="radio"  name="mem_join_type" id="idx_mem_join_type3" value="3"  <?=$mem_join_type=='3'?' checked':''?> onClick="javascript:mem_join_type_chk(this.value);"/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_mem_join_type3">협력업체</LABEL>
						</td>
					</tr>

					<tr>
						<th><span>아이디</span></th>
						<td class="td_con1" align="center">
							<p align="left">
							<?if ($exe_mode=='update') {?>
							<?=$id?>
							<input type="hidden" name="id" value="<?=$id?>" title='아이디를 입력해주세요.'>
							<?} else {?>
							<input type=text name=id class="input" style="width:300px;height:25px" onkeydown="fn_press_han(this);"  onkeyup="fn_press_han(this);"  title='아이디를 입력해주세요.'>
							<?}?>
							</p>
						</td>
					</tr>

					<tr>
						<th style="width:140px"><span>비밀번호</span></th>
						<td class="td_con1" align="center">
							<p align="left">
							<?if ($exe_mode=='update') {?>
							<a href="javascript:LostPass('<?=$data->id?>');"><img src="images/btn_edit4.gif" border="0"></a>
							<?} else {?>
							<input type=text name=passwd class="input" style="width:300px;height:25px" onkeydown="fn_press_han(this);"  onkeyup="fn_press_han(this);" title="비밀번호를 입력하세요.">
							<?}?>
							</p>
						</td>
					</tr>

					<tr style="<?if ($exe_mode!='update'){?>display:none;<?}?>">
						<th style="width:140px;"><span>등급</span></th>
						<td class="td_con1" align="center">
							<p align="left">
							<select name=group_code class="input" style="height:25px">
							<?while($group_data=pmysql_fetch_object($group_result)){?>
								<option value="<?=$group_data->group_code?>" <?=$selected[group][$group_data->group_code]?>><?=$group_data->group_name?></option>
							<?}?>
							</select>
							</p>
						</td>
					</tr>

					<tr>
						<th><span>이름</span></th>
						<td class="td_con1" align="center">
							<p align="left">
							<input type=text name=name value="<?=$data->name?>" class="input" style="height:25px" title="이름을 입력하세요.">
							</p>
						</td>
					</tr>
					
					<tr>
						<th><span>생년월일</span></th>
						<td class="td_con1">
							<p align="left">
							<input name="birth1" title="생년월일을 입력하세요." type="text" size="8" maxlength="8" value="<?=$data->birth?>" label="생년월일" class="input" style="height:25px">				
							<input type="radio"  name="lunar" id="idx_lunar1" value="1"  <?=trim($data->lunar)==''||$data->lunar=='1'?' checked':''?>/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_gender1">양력</LABEL>
							<input type="radio"  name="lunar" id="idx_lunar2" value="0"  <?=$data->lunar=='0'?' checked':''?>/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_gender2">음력</LABEL>
						</td>
					</tr>
					
					<tr>
						<th><span>성별</span></th>
						<td class="td_con1">
							<p align="left">							
							<input type="radio"  name="gender" id="idx_gender1" value="1"  <?=trim($data->gender)==''||$data->gender=='1'?' checked':''?>/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_gender1">남자</LABEL>
							<input type="radio"  name="gender" id="idx_gender2" value="2"  <?=$data->gender=='0'?' checked':''?>/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_gender2">여자</LABEL>
						</td>
					</tr>

					<tr>
						<th><span>주소</span></th>
						<td class="td_con1" align="center">
						<div>
						<p align="left" style="margin:0 0;padding:2px 0px;">
							<input type=text name="home_zonecode" id="home_zonecode" value="<?=$home_post?>" readOnly style="WIDTH:60px;height:25px" class="input">
							<input type=hidden name="home_post1" id="home_post1" value="<?=$home_post1?>" ><input type=hidden name="home_post2" id="home_post2" value="<?=$home_post2?>">
                            <input type='button' onClick="javascript:openDaumPostcode();" value="주소찾기" style="background:#f0f0f0; border:1px solid #aeaeae; padding:4px 20px 5px;vertical-align: top;">
						</p>
						</div>
						<div>
						<p align="left" style="margin:0 0;padding:2px 0px;">
							<input type=text name="home_addr1" id="home_addr1" value="<?=$home_addr1?>" maxLength="100" readOnly style="WIDTH:300px;height:25px" class="input" title="주소를 입력해 주세요.">
							<input type=text name="home_addr2" id="home_addr2" value="<?=$home_addr2?>" maxLength="100" style="WIDTH:500px;height:25px" class="input" title="주소를 입력해 주세요.">
						</p>
						</div>
						</td>
					</tr>
					<th><span>전화번호</span></th>
						<TD class="td_con1" align="center">
							<p align="left">

							<select name="home_tel[]" id="home_tel1" class="input" style="height:25px">
								<option value="02" <?=$selected[home_tel]["02"]?>>02&nbsp;&nbsp;&nbsp;&nbsp;</option>
								<option value="031" <?=$selected[home_tel]["031"]?>>031</option>
								<option value="032" <?=$selected[home_tel]["032"]?>>032</option>
								<option value="033" <?=$selected[home_tel]["033"]?>>033</option>
								<option value="041" <?=$selected[home_tel]["041"]?>>041</option>
								<option value="042" <?=$selected[home_tel]["042"]?>>042</option>
								<option value="043" <?=$selected[home_tel]["043"]?>>043</option>
								<option value="044" <?=$selected[home_tel]["044"]?>>044</option>
								<option value="051" <?=$selected[home_tel]["051"]?>>051</option>
								<option value="052" <?=$selected[home_tel]["052"]?>>052</option>
								<option value="053" <?=$selected[home_tel]["053"]?>>053</option>
								<option value="054" <?=$selected[home_tel]["054"]?>>054</option>
								<option value="055" <?=$selected[home_tel]["055"]?>>055</option>
								<option value="061" <?=$selected[home_tel]["061"]?>>061</option>
								<option value="062" <?=$selected[home_tel]["062"]?>>062</option>
								<option value="063" <?=$selected[home_tel]["063"]?>>063</option>
								<option value="064" <?=$selected[home_tel]["064"]?>>064</option>
							</select>
							- <input type="text" name="home_tel[]" id="home_tel2" maxlength="4" value="<?=$home_tel[1]?>"size="10" style="ime-mode:disabled;height:25px" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" class="input"/>
							- <input type="text" name="home_tel[]" id="home_tel3" maxlength="4" value="<?=$home_tel[2]?>"size="10" style="ime-mode:disabled;height:25px" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" class="input" title="전화번호를 입력해 주세요."/>
							</p>
						</TD>
					</tr>

					<tr>
						<th><span>휴대폰번호</span></th>
						<td class="td_con1" align="center">
							<p align="left">
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
							</p>
						</td>
					</tr>

					<tr>
						<th><span>이메일</span></th>
						<td class="td_con1" align="center">
							<p align="left">
							<input type="text" name="email1" id="email1" value="<?=$email_arr[0]?>" style="height:25px" class="input" title="이메일을 입력해 주세요."/>
							@
							<input type="text" name="email2" id="email2" value="<?=$email_arr[1]?>" style="height:25px;<?if($email_com!="custom"){?>display: none;<?}?>" class="input"/>
							<select  id="email_com" class="input" style="height:25px" onchange="customChk(this.value);">
								<option value="">선택</option>
								<option value="custom" <?=$selected[email_com]["custom"]?>>직접입력</option>
								<option value="naver.com" <?=$selected[email_com]["naver.com"]?>>naver.com</option>
								<option value="daum.net" <?=$selected[email_com]["daum.net"]?>>daum.net</option>
								<option value="gmail.com" <?=$selected[email_com]["gmail.com"]?>>gmail.com</option>
								<option value="nate.com" <?=$selected[email_com]["nate.com"]?>>nate.com</option>
								<option value="yahoo.co.kr" <?=$selected[email_com]["yahoo.co.kr"]?>>yahoo.co.kr</option>
								<option value="lycos.co.kr" <?=$selected[email_com]["lycos.co.kr"]?>>lycos.co.kr</option>
								<option value="empas.com" <?=$selected[email_com]["empas.com"]?>>empas.com</option>
								<option value="hotmail.com" <?=$selected[email_com]["hotmail.com"]?>>hotmail.com</option>
								<option value="msn.com" <?=$selected[email_com]["msn.com"]?>>msn.com</option>
								<option value="hanmir.com" <?=$selected[email_com]["hanmir.com"]?>>hanmir.com</option>
								<option value="chol.net" <?=$selected[email_com]["chol.net"]?>>chol.net</option>
								<option value="korea.com" <?=$selected[email_com]["korea.com"]?>>korea.com</option>
								<option value="netsgo.com" <?=$selected[email_com]["netsgo.com"]?>>netsgo.com</option>
								<option value="dreamwiz.com" <?=$selected[email_com]["dreamwiz.com"]?>>dreamwiz.com</option>
								<option value="hanafos.com" <?=$selected[email_com]["hanafos.com"]?>>hanafos.com</option>
								<option value="freechal.com" <?=$selected[email_com]["freechal.com"]?>>freechal.com</option>
								<option value="hitel.net" <?=$selected[email_com]["hitel.net"]?>>hitel.net</option>
							</select>
							</p>
						</td>
					</tr>

					<tr>
						<th><span>추가정보</span></th>
						<td>
							키(cm) <input type="text" name="height" class="input" style="width:60px;height:25px" value="<?=$data->height?>" title="키" maxlength="3" > / 
							몸무게(kg) <input type="text" name="weigh" class="input" style="width:60px;height:25px" value="<?=$data->weigh?>" title="몸무게" maxlength="3">
						</td>
					</tr>
					<tr>
						<th><span>직업</span></th>
						<td>
							<select name="job_code" class="input" style="height:25px">
								<option value="">선택</option>
								<option value="01" <?=$selected[job_code]["01"]?>>주부</option>
								<option value="02" <?=$selected[job_code]["02"]?>>자영업</option>
								<option value="03" <?=$selected[job_code]["03"]?>>사무직</option>
								<option value="04" <?=$selected[job_code]["04"]?>>생산/기술직</option>
								<option value="05" <?=$selected[job_code]["05"]?>>판매직</option>
								<option value="06" <?=$selected[job_code]["06"]?>>보험업</option>
								<option value="07" <?=$selected[job_code]["07"]?>>은행/증권업</option>
								<option value="08" <?=$selected[job_code]["08"]?>>전문직</option>
								<option value="09" <?=$selected[job_code]["09"]?>>공무원</option>
								<option value="10" <?=$selected[job_code]["10"]?>>농축산업</option>
								<option value="11" <?=$selected[job_code]["11"]?>>학생</option>
								<option value="12" <?=$selected[job_code]["12"]?>>기타</option>
							</select>
						</td>
					</tr>

					<tr class="emp_tr" style="<?if ($mem_join_type!="2"){?>display:none;<?}?>">
						<th><span>사번</span></th>
						<td class="td_con1" align="center">
							<p align="left">
							<input type=text name="emp_id" value="<?=$data->erp_emp_id?>" class="input" style="height:25px" title="사번을 입력하세요.">
							</p>
						</td>
					</tr>
					<tr class="<?if($exe_mode=='update'){?>emp_tr<?}?>" style="<?if ($mem_join_type!="2"){?>display:none;<?}?>">
						<th><span>임직원적립금</span></th>
						<td class="td_con1" align="center">
							<p align="left">
							<input type="text" name="staff_reserve" title="임직원적립금" class="w100 ta-r" value="<?=$data->staff_reserve?>"><input type="hidden" name="staff_reserve_ori" value="<?=$data->staff_reserve?>"> 원
							</p>
						</td>
					</tr>
<!-- 20170825 수정 --->
					<!--<tr class="<?if($exe_mode=='update'){?>emp_tr<?}?>" style="<?if ($mem_join_type!="3"){?>display:none;<?}?>">
						<th><span>제휴사적립금</span></th>
						<td class="td_con1" align="center">
							<p align="left">
							<input type="text" name="cooper_reserve" title="제휴사적립금" class="w100 ta-r" value="<?=$data->cooper_reserve?>"><input type="hidden" name="cooper_reserve_ori" value="<?=$data->cooper_reserve?>"> 원
							</p>
						</td>
					</tr>-->
					<tr class="cooper_tr" style="<?if ($mem_join_type!="3"){?>display:none;<?}?>">
						<th><span>제휴사명</span></th>
							<td>
								<select name=office_code>
                                <?while($c_data=pmysql_fetch_object($c_result)){?>
                                    <option value="<?=$c_data->group_code?>" <?=$selected[office][$c_data->group_code]?>><?=$c_data->group_name?></option>
                                <?}?>
                                </select>
							</td>
					</tr>

<!-- 20170825 수정 --->
					<tr>
						<th><span>수신여부</span></th>
						<TD class="td_con1" align="center">
							<p align="left">
							<input type="checkbox"  name="news_mail_yn" id="idx_news_mail_yn" value="Y"  <?if($news_mail_yn=="Y")echo"checked";?>/>
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn">이메일 수신</LABEL>
							<input type="checkbox" name="news_sms_yn" id="idx_news_sms_yn" value="Y" <?if($news_sms_yn=="Y")echo"checked";?>  />
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn">SMS 수신</LABEL>
							<input type="checkbox" name="news_kko_yn" id="idx_news_kko_yn" value="Y" 
							<?if($news_kko_yn=="Y")echo"checked";?>  />
							<LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_kko_yn">카카오톡 수신</LABEL>
							</p>
						</TD>
					</tr>

					</TABLE>
					</div>
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
							<dt><span>회원정보 관리</span></dt>
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
