<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$regdate=substr($_shopdata->regdate,0,8);

$today = date("Ymd");
$year=date("Y");
$month=date("m");
$day=date("d");

$sql = "SELECT * FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$shopname = $row->shopname;
	$companyname = $row->companyname;
	$companynum = $row->companynum;
	$companyowner = $row->companyowner;
	$companypost = $row->companypost;
	$companyaddr = $row->companyaddr;
	$companybiz = $row->companybiz;
	$companyitem = $row->companyitem;
	$reportnum = $row->reportnum;
	$info_email = $row->info_email;
	$info_tel  = $row->info_tel;
	$info_addr = $row->info_addr;
	$privercyname = $row->privercyname;
	$privercyemail = $row->privercyemail;
}


include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.form.js"></script>
<script language="JavaScript">
	function f_addr_search(form,post,addr,gbn) {
		window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
	}

	$(document).ready(function(){
		$('.checkCusType').click(function(){
			//ssnDefault
			if($(this).val() == 'C'){
				$('#cusTypeName').html('회사명');
				$('#cusTypeSsn').html('사업자등록번호');
				$('input[name=ssn]').val($('#ssnDefault').val());
			}else{
				$('#cusTypeName').html('고객명');
				$('#cusTypeSsn').html('주민등록번호');
				$('input[name=ssn]').val('');
			}
		})

		$('#password, #passwordConfirm').keyup(function(){
			if($('#password').val() && $('#passwordConfirm').val()){
				if($('#password').val() == $('#passwordConfirm').val()){
					$('#confirmPass').css('color', 'green');
					$('#confirmPass').html('비밀번호가 일치합니다.');
					$('input[name=passwordSameCheck]').val('1');
				}else{
					$('#confirmPass').css('color', 'red');
					$('#confirmPass').html('비밀번호가 일치하지 않습니다.');
					$('input[name=passwordSameCheck]').val('');
				}
			}
		})
		/*
		var option = {
			success : function(data) {
				if(data == 'Err'){
					alert("등록에 실패 하셨습니다.");
				}else{
					$('#formBizOuter').html($('#formBiz').html());
					$('#formBizOuter').submit();
				}
			}
		};
		$('#formBiz').ajaxForm(option);
		*/
		$('.onSubmitBtn').click(function(){
			if($('input[name=cusId]').val().slice(0, 3) == 'dm_'){
				$.ajax({
					type: "POST",
					url: "counter_biz_indb.php",
					data: $('#formBiz').serialize(),
					beforeSend: function () {}
				}).done(function ( data ) {
					if(data == 'Err'){
						alert("등록에 실패 하셨습니다.");
					}else{
						$('#formBiz').attr('action', 'http://logger.co.kr/register/registerPs.tsp');
						$('#formBiz').submit();
						return false;
					}
				});
			}else{
				alert('회원아이디는 dm_로 시작 하셔야 합니다.');
				$('input[name=cusId]').focus();
				return false;
			}
		})
	})
</script>


<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 스마트MD &gt;<span> 스마트MD 통계</span></p></div></div>
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
			<?php include("menu_counter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">스마트MD 신청</div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>(* 필수입력 사항)</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<form name = 'formBiz' id = 'formBiz' action = "counter_biz_indb.php" method='POST'>
					<input type = 'hidden' name = 'partnerName' value = '(주)듀오메티스'>
					<input type = 'hidden' name = 'partnerNum' value = '32148'>
					<input type = 'hidden' name = 'level' value = '4'>
					<input type = 'hidden' name = 'returnURL' value = 'http://<?=$_SERVER[HTTP_HOST]?>/admin/counter_biz_result.php'>
					<input type = 'hidden' name = 'errorURL' value = 'http://<?=$_SERVER[HTTP_HOST]?>/admin/counter_biz_error.php'>
					<input type = 'hidden' name = 'passwordSameCheck'>
					
					<div class="table_style01">
					<table width="100%" cellpadding="0" cellspacing="0">
						<col width="140"></col><col></col>
						<tr>
							<th><span>구분</span></th>
							<td class="td_con1">
								<input type = 'radio' name = 'cusType' value = 'C' class = 'checkCusType' checked> 회사
								<input type = 'radio' name = 'cusType' value = 'P' class = 'checkCusType'> 개인
							</td>
						</tr>
						<tr>
							<th><span>회원아이디[임시 입력](*)</span></th>
							<td class="td_con1">
								<input type="text" name="cusId" value="dm_" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input" required>
							</td>
						</tr>
						<tr>
							<th><span>비밀번호(*)</span></th>
							<td class="td_con1">
								<input type="password" name="password" id="password" size="30" class="input" required>
							</td>
						</tr>
						<tr>
							<th><span>비밀번호 확인(*)</span></th>
							<td class="td_con1">
								<input type="password" name="passwordConfirm" id="passwordConfirm" size="30" class="input" required>&nbsp;<span id = 'confirmPass'></span>
							</td>
						</tr>
						<tr>
							<th><span id = 'cusTypeSsn'>사업자등록번호</span>(*)</th>
							<td class="td_con1">
								<input type="hidden" id="ssnDefault" value="<?=$companynum?>">
								<input type="text" name="ssn" value="<?=$companynum?>" size="20" maxlength="13" class="input" required>
							</td>
						</tr>

						<!--위에 입력한거에 따라 변화 예정-->
						<tr>
							<th><span id = 'cusTypeName'>회사명</span>(*)</th>
							<td class="td_con1"><input type="text" name="name" value="<?=$companyname?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input" required></td>
						</tr>
						<tr>
							<th><span>쇼핑몰 이메일(*)</span></th>
							<td class="td_con1"><input type=text name="email" value="<?=$info_email?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input" required></td>
						</tr>
						<tr>
							<th><span>도메인명(*)</span></th>
							<td class="td_con1"><input type=text name="domain" value="http://<?=$_SERVER[HTTP_HOST]?>" size="60" class="input" required></td>
						</tr>
						<tr>
							<th><span>웹사이트명(*)</span></th>
							<td class="td_con1"><input type=text name="siteName" value="<?=$shopname?>" size="60" class="input" required></td>
						</tr>
						<tr>
							<th><span>담당자 성명(*)</span></th>
							<td class="td_con1"><input type=text name="contactName" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input" required></td>
						</tr>
						<tr>
							<th><span>담당자 직책</span></th>
							<td class="td_con1"><input type=text name="contactRole" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input"></td>
						</tr>
						<tr>
							<th><span>담당자 전화번호</span></th>
							<td class="td_con1"><input type=text name="phone" size="60" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input"></td>
						</tr>
						<tr>
							<th><span>담당자 팩스번호</span></th>
							<td class="td_con1"><input type=text name="fax" size="60" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input"></td>
						</tr>
						<tr>
							<th><span>담당자 핸드폰번호</span></th>
							<td class="td_con1"><input type=text name="mobile" size="60" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input"></td>
						</tr>
						<tr>
							<th><span>사업장 주소</span></th>
							<td colspan="3" bgcolor="#FFFFFF" class="td_con1">
							
							<div class="table_none">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="80" nowrap>
										<input type=text name="zipCode" value="<?=substr($companypost,0,3)?>-<?=substr($companypost,3,3)?>" size="7" maxlength="7" class="input" readonly required>
									</td>
									<td width="100%">
										<A href="javascript:f_addr_search('formBiz','zipCode','addr1',1);" onfocus="this.blur();" style="selector-dummy: true" class="board_list hideFocus">
											<img src="images/icon_addr.gif" border="0">
										</A>
									</td>
								</tr>
								<tr>
									<td colspan="2"><input type=text name="addr1" value="<?=$companyaddr?>" size="60" maxlength="150" onKeyDown="chkFieldMaxLen(150)" class="input" readonly required></td>
								</tr>
								<tr>
									<td colspan="2"><input type=text name="addr2" value="" size="60" maxlength="150" onKeyDown="chkFieldMaxLen(150)" class="input" required></td>
								</tr>
							</table>
							</div>

							</td>
						</tr>

						<tr>
							<th><span>사업자 업태(*)</span></th>
							<td class="td_con1"><input type="text" name="bizType" value="<?=$companybiz?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input" required></td>
						</tr>
						<tr>
							<th><span>사업자 종목(*)</span></th>
							<td class="td_con1"><input type=text name="bizName" value="<?=$companyitem?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input" required></td>
						</tr>
						<tr>
							<th><span>대표자 성명(*)</span></th>
							<td class="td_con1"><input type="text" name="ceoName" value="<?=$companyowner?>" size="20" maxlength="12" onKeyDown="chkFieldMaxLen(12)" class="input" required></td>
						</tr>
					</table>
					</div>
					<p align="center"><input  type = 'button' value = '' class = 'onSubmitBtn' style="width:111px; height:38px; border:0px; background:url(../admin/img/btn/btn_input02.gif) no-repeat; ;"></p>
					</form>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
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
