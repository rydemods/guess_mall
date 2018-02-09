<?
// 전체매장 가져오기
$arrStoreList = array();
$sql  = "SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
$result = pmysql_query($sql);
while ($row = pmysql_fetch_object($result)) {
	$arrStoreList[] = $row;
}
pmysql_free_result($result);

// 매장직원정보 가져오기
list($staff_store_code, $staff_level) = pmysql_fetch(pmysql_query("SELECT store_code, level FROM tblstore_staff WHERE mem_id = '{$mem_id}' "));

// 환불계좌
list($rBankCode, $rAccountNum) = pmysql_fetch(pmysql_query("SELECT bank_code, account_num FROM tblmember WHERE id = '{$mem_id}' "));

################## 회원 그룹 쿼리 ################
$groupname='';

$group_qry="select group_name,group_code from tblmembergroup order by group_level";
$group_result=pmysql_query($group_qry);

################## 20170830 제휴사 쿼리 ################
$c_qry="select group_name,group_code from tblcompanygroup ";
$c_result=pmysql_query($c_qry);

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
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
			data: "name=" + u_name_val + "&mobile=" + u_mobile_val + "&mode=erp_mem_chk&access_type=mobile&mem_id=<?=$mem_id?>",
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
	var val	= $("input[name=email]").val();

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
				data: "email=" + val + "&mode=email&access_type=mobile&mem_id=<?=$mem_id?>",
				dataType:"json", 
				success: function(data) {
					$("#email_checked").val(data.code);
					if (data.code == 0) {
						alert(data.msg);
						$("input[name=email]").focus();
						return;
					} else {
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
			data: "name=" + u_name_val + "&emp_id=" + val + "&mode=erp_emp_chk&access_type=mobile&mem_id=<?=$mem_id?>",
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
	
	$("#emp_checked").val("1");
	CheckFormSubmit();
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

	$("input[name=name_checked]").val('0');
	$("input[name=birth_checked]").val('0');
	$("input[name=home_addr_checked]").val('0');
	$("input[name=email_checked]").val('0');
	$("input[name=mobile_checked]").val('0');
	$("input[name=emp_checked]").val('0');
	$("input[name=cooper_checked]").val('0');
	ValidFormName();
}

function CheckFormSubmit(){
	form=document.mem_frm;

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
		if(confirm("<?=$mem_id?> 회원님의 개인정보를 수정하시겠습니까?"))
			form.submit();
		else
			return;
	} else {
		return;
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

			<div class="contentsBody">
				
                <form name="mem_frm" method=post action="crm_view_mem_indb.php">
                <input type="hidden" name="mode" value="update">
                <input type="hidden" name="id" value="<?=$mem_id?>">
				<input type=hidden name=id_checked id=id_checked value="1">
				<input type=hidden name=passwd_checked id=passwd_checked value="1">
				<input type=hidden name=name_checked id=name_checked value="0">
				<input type=hidden name=birth_checked id=birth_checked value="0">
				<input type=hidden name=home_addr_checked id=home_addr_checked value="0">
				<input type=hidden name=email_checked id=email_checked value="0">
				<input type=hidden name=mobile_checked id=mobile_checked value="0">
				<input type=hidden name=emp_checked id=emp_checked value="0">
				<input type=hidden name=cooper_checked id=cooper_checked value="0">
				<input type=hidden name=erp_member_id id=erp_member_id value="">
				<h3 class="table-title">회원상세정보</h3>
				<p class="dot-title">기본정보</p>
				<table class="th-left" >
					<caption>회원 기본정보 출력</caption>
					<colgroup>
						<col style="width:18%"><col style="width:82%">
					</colgroup>
					<tbody>

						<tr>
							<th scope="col">구분</th>
							<td>
								<input type="radio" name="mem_join_type" id="idx_mem_join_type1" value="1"<?=$mem_join_type==""||$mem_join_type=="1"?" checked":""?> onClick="javascript:mem_join_type_chk(this.value);"/> <label for="idx_mem_join_type1">일반</label>
								<input type="radio" name="mem_join_type" id="idx_mem_join_type2" value="2"<?=$mem_join_type=="2"?" checked":""?> onClick="javascript:mem_join_type_chk(this.value);"/> <label for="idx_mem_join_type2">임직원</label>
								<input type="radio" name="mem_join_type" id="idx_mem_join_type3" value="3"<?=$mem_join_type=="3"?" checked":""?> onClick="javascript:mem_join_type_chk(this.value);"/> <label for="idx_mem_join_type3">제휴사</label>
							</td>
						</tr>

						<tr>
							<th scope="col">아이디</th>
							<td><?=$mem_id?></td>
						</tr>

						<tr>
							<th scope="col"><label for="inp-pwd01">비밀번호</label></th>
							<td><a href="javascript:LostPass('<?=$mem_id?>');" class="btn-line">변경</a></td>
						</tr>

						<tr>
							<th scope="col">등급</th>
							<td>
								<select name=group_code>
                                <?while($group_data=pmysql_fetch_object($group_result)){?>
                                    <option value="<?=$group_data->group_code?>" <?=$selected[group][$group_data->group_code]?>><?=$group_data->group_name?></option>
                                <?}?>
                                </select>
							</td>
						</tr>

						<tr>
							<th scope="col">이름</th>
							<td><input type="text" name="name" id="name" value="<?=$mem_name?>" title="이름을 입력하세요."></td>
						</tr>

						<tr>
							<th scope="col">생년월일</th>
							<td>
                                <input name="birth1" title="생년월일을 입력하세요." type="text" size="8" maxlength="8" value="<?=$mem_birth?>" label="생년월일" class="w80">&nbsp;&nbsp;&nbsp
								<input type="radio" name="lunar" id="idx_lunar1" value="1"<?=$mem_lunar==""||$mem_lunar=="1"?" checked":""?>   /> <label for="idx_lunar1">양력</label>
								<input type="radio" name="lunar" id="idx_lunar2" value="0"<?=$mem_lunar=="0"?" checked":""?>   /> <label for="idx_lunar2">음력</label>
							</td>
						</tr>

						<tr>
							<th scope="col">성별</th>
							<td>
								<input type="radio" name="gender" id="idx_gender1" value="1"<?=$mem_gender==""||$mem_gender=="1"?" checked":""?>   /> <label for="idx_gender1">남자</label>
								<input type="radio" name="gender" id="idx_gender2" value="2"<?=$mem_gender=="0"?" checked":""?>   /> <label for="idx_gender2">여자</label>
							</td>
						</tr>
						<tr>
							<th scope="col"><label for="inp-address01">주소</label></th>
							<td>
								<div><input type="text" title="우편번호 자리" name="home_zonecode" id="home_zonecode" value="<?=$mem_home_post?>"> <a href="javascript:openDaumPostcode();" class="btn-line">우편번호 검색</a></div>
								<div class="mt-5"><input class="w100-per" type="text" title="주소를 입력해 주세요." name="home_addr1" id="home_addr1" value="<?=$home_addr1?>"> </div>
								<div class="mt-5"><input class="w100-per" type="text" title="주소를 입력해 주세요." name="home_addr2" id="home_addr2" value="<?=$home_addr2?>"> </div>
							</td>
						</tr>
						<tr>
							<th scope="col">전화번호</th>
							<td>
                                <select name="home_tel[]" id="home_tel1" class="w50" >
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
							- <input type="text" name="home_tel[]" id="home_tel2" maxlength="4" value="<?=$home_tel[1]?>" size="6" style="ime-mode:disabled;" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="w50"/>
							- <input type="text" name="home_tel[]" id="home_tel3" maxlength="4" value="<?=$home_tel[2]?>" size="6" style="ime-mode:disabled;" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="w50" title="전화번호를 입력해 주세요."/>
							</td>
						</tr>
						<tr>
							<th scope="col">휴대폰번호</th>
							<td>
                                <select name="mobile[]" id="mobile1" class="w50" >
                                    <option value="010" <?=$selected[mobile]["010"]?>>010&nbsp;&nbsp;&nbsp;</option>
                                    <option value="011" <?=$selected[mobile]["011"]?>>011</option>
                                    <option value="016" <?=$selected[mobile]["016"]?>>016</option>
                                    <option value="017" <?=$selected[mobile]["017"]?>>017</option>
                                    <option value="018" <?=$selected[mobile]["018"]?>>018</option>
                                    <option value="019" <?=$selected[mobile]["019"]?>>019</option>
                                </select>
							- <input type="text" name="mobile[]" id="mobile2" maxlength="4" value="<?=$mobile[1]?>" size="6" style="ime-mode:disabled;" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="w50"/>
							- <input type="text" name="mobile[]" id="mobile3" maxlength="4" value="<?=$mobile[2]?>" size="6" style="ime-mode:disabled;" onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="w50" title="휴대폰번호를 입력해 주세요."/>
							</td>
						</tr>
						<tr>
							<th scope="col">이메일</th>
							<td><input type="text" title="이메일" class="w300" name="email" value="<?=$mem_email?>" title="이메일을 입력해 주세요."></td>
						</tr>

						<tr>
							<th scope="col">추가정보</th>
							<td>
								키(cm) <input type="text" name="height" class="w60" value="<?=$mem_height?>" title="키" maxlength="3" > / 
								몸무게(kg) <input type="text" name="weigh" class="w60" value="<?=$mem_weigh?>" title="몸무게" maxlength="3">
							</td>
						</tr>
						<tr>
							<th scope="col">직업</th>
							<td>
								<select name="job_code" class="w100">
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
							<th scope="col">사번</th>
							<td>
                                <input type="text" name="emp_id" title="사번을 입력하세요." class="w200 ta-l" value="<?=$mem_emp_id?>">
							</td>
						</tr>
						<tr class="emp_tr" style="<?if ($mem_join_type!="2"){?>display:none;<?}?>">
							<th scope="col">임직원적립금</th>
							<td>
								<input type="text" name="staff_reserve" title="임직원적립금" class="w100 ta-r" value="<?=$staff_reserve?>"><input type="hidden" name="staff_reserve_ori" value="<?=$staff_reserve?>"> 원
							</td>
						</tr>
<!-- 20170825 수정 --->
						<!--<tr class="cooper_tr" style="<?if ($mem_join_type!="3"){?>display:none;<?}?>">
							<th scope="col">제휴사적립금</th>
							<td>
								<input type="text" name="cooper_reserve" title="제휴사적립금" class="w100 ta-r" value="<?=$cooper_reserve?>"><input type="hidden" name="cooper_reserve_ori" value="<?=$cooper_reserve?>"> 원
							</td>
						</tr>-->
						<tr class="cooper_tr" style="<?if ($mem_join_type!="3"){?>display:none;<?}?>">
							<th scope="col">제휴업체명</th>
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
							<th scope="col">수신여부</th>
							<td>
                                <input type="checkbox" name="news_sms_yn" id="idx_news_sms_yn" value="Y" <?if($news_sms_yn=="Y")echo"checked";?>  /> <label for="idx_news_sms_yn">이메일 수신</label>
                                <input type="checkbox"  name="news_mail_yn" id="idx_news_mail_yn" value="Y"  <?if($news_mail_yn=="Y")echo"checked";?>/> <label for="idx_news_mail_yn">SMS 수신</label>
                                <input type="checkbox"  name="news_kko_yn" id="idx_news_kko_yn" value="Y"  <?if($news_kko_yn=="Y")echo"checked";?>/> <label for="idx_news_kko_yn">카카오톡 수신</label>
							</td>
						</tr>
						<tr>
							<th scope="col">환불계좌</th>
							<td><?=$rAccountNum?$oc_bankcode[$rBankCode]." ".$rAccountNum:"-"?></td>
						</tr>
					</tbody>
				</table>

                <div class="btn-place"><button class="btn-line big" type="button" onClick="javascript:chk_writeForm();"><span>수정</span></button></div>
                </form>



				<dl class="help-attention mt-50">
					<dt>도움말</dt>
					<!-- <dd>1. 비회원인 경우는 어쩌고 저쩌고</dd>
					<dd>2. 회원인 경우는 어쩌고 저쩌고</dd> -->
				</dl>


			</div><!-- //.contentsBody -->