<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");

$mode = $_POST["mode"];
$idx = $_POST["idx"] ? $_POST["idx"] : $_GET["idx"];
$email = $_POST["email"];
$ip = $_SERVER['REMOTE_ADDR'];
$subjectl = $_POST["subject"];
$today = date("Y-m-d");
$content = $_POST["content"];
// $content = htmlspecialchars($_POST["content"], ENT_QUOTES);  //특수문자를 HTML엔터티로 변환
// $content = str_replace("\r\n","<br/>",$content); //줄바꿈 처리
// $content = str_replace("\u0020","&nbsp;",$content); // 스페이스바 처리
$head_title = $_POST["head_title"];
$hp = $_POST["hp"];
$chk_mail = $_POST["chk_mail"];
$chk_sms = $_POST["chk_sms"];

#파일 업로드
$filepath = $Dir.DataDir."shopimages/personal/";
$up_file = new FILE($filepath);
$file = $up_file->upFiles();
$up_filename = $file["up_filename"][0]["v_file"];
$ori_filename = $_POST["ori_filename"];

$view_sql ="SELECT * FROM tblpersonal WHERE idx = '".$idx."'";
$result = pmysql_query($view_sql, get_db_conn());
$row = pmysql_fetch_object($result);
$data = $row;

if($mode == "insert"){
	$sql = "INSERT INTO tblpersonal (
				id,
				name,
				email,
				ip,
				subject,
				date,
				content,
				head_title,
				\"HP\",
				chk_mail,
				chk_sms,
				up_filename,
				ori_filename
				)values(
				'{$_ShopInfo->getMemid()}',
				'{$_ShopInfo->getMemname()}',
				'{$email}',
				'{$ip}',
				'{$subjectl}',
				'{$today}',
				'{$content}',
				'{$head_title}',
				'{$hp}',
				'{$chk_mail}',
				'{$chk_sms}',
				'{$up_filename}',
				'{$ori_filename}'
		)";
	$result = pmysql_query($sql,get_db_conn());
	
	if(!pmysql_error()){
		echo  "<script>alert(' 정상적으로 등록되었습니다.'); location.href=\"/front/myqna_list.php\"</script>";
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}
}else if($mode == "modify"){
	$where[]="head_title='".$head_title."'";
	$where[]="subject='".$subjectl."'";
	$where[]="\"HP\"='".$hp."'";
	$where[]="content='".$content."'";
	$where[]="email='".$email."'";
	$where[]="chk_mail='".$chk_mail."'";
	$where[]="chk_sms='".$chk_sms."'";
	
	#첨부파일이 변경되면 기존에 있는 파일 삭제 & 새로운 파일 업데이트
	if ($data->ori_filename != $ori_filename) {
		$where[]="up_filename='".$up_filename."'";
		$where[]="ori_filename='".$ori_filename."'";
		$up_file->removeFile($data->up_filename);
	}
	
	$usql = "UPDATE tblpersonal SET ";
	$usql.= implode(", ",$where);
	$usql.=" WHERE idx = '".$idx."'";
	
	pmysql_query( $usql, get_db_conn() );
	
	if(!pmysql_error()){
		echo  "<script>alert('수정이 완료되었습니다.'); location.href=\"/front/myqna_list.php\"</script>";
	}else{
		echo  "<script>alert('오류가 발생하였습니다.'); location.href=\"/front/front/myqna_list.php\"</script>";
	}
}

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<div id="contents">
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->
			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->
			<article class="mypage_content">
				<section class="mypage_main">
					<div class="title_box_border">
						<h3>1:1 문의</h3>
					</div>
					<!-- 게시판 쓰기/수정 -->
					<div class="myboard">
						<form name='write_form' id='write_form' action="<?=$_SERVER['PHP_SELF']?>" method='POST' enctype="multipart/form-data">
						<input type='hidden' id='mode' name='mode' value="<?=$_GET["mode"] ?>">
						<input type='hidden' id='idx' name='idx' value="<?=$_GET["idx"] ?>">
						<input type='hidden' name='chk_mail' value="N">
						<input type='hidden' name='chk_sms' value="N">
						<input type='hidden' name='ori_filename' value="<?=$data->ori_filename ?>">
						<table class="th_left border_none">
							<caption>1:1 문의 작성</caption>
							<colgroup>
								<col style="width:160px">
								<col style="width:auto">
								<col style="width:160px">
								<col style="width:160px">
							</colgroup>
							<tbody>
								<tr>
									<th><label for="select_type">상담유형</label><span class="required">*</span></th>
									<td colspan="3">
										<div class="my-comp-select">
											<select class="required_value" id="head_title" name="head_title" value="<?=$data->head_title ?>"  label="상담유형" >
												<option value="">선택</option>
												<option value=1 <?=$data->head_title  == '1' ? ' selected="selected"' : '';?>>로그인</option>
												<option value=2 <?=$data->head_title  == '2' ? ' selected="selected"' : '';?>>회원가입</option>
												<option value=3 <?=$data->head_title  == '3' ? ' selected="selected"' : '';?>>구매관련</option>
												<option value=4 <?=$data->head_title  == '4' ? ' selected="selected"' : '';?>>배송관련</option>
												<option value=5 <?=$data->head_title  == '5' ? ' selected="selected"' : '';?>>결제관련</option>
												<option value=6 <?=$data->head_title  == '6' ? ' selected="selected"' : '';?>>매장관련</option>
												<option value=7 <?=$data->head_title  == '7' ? ' selected="selected"' : '';?>>기타</option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="inp_writer">제목</label><span class="required">*</span></th>
									<td colspan="3">
										<input type="text" class="required_value" id="subject" name="subject" value="<?=$data->subject ?>" title="제목 입력자리"  label="제목" style="width:100%;">
									</td>
								</tr>
								<tr>
									<th><label for="inp_content">문의내용</label><span class="required">*</span></th>
									<td colspan="3">
										<textarea class="required_value" id="content" name="content" cols="30" rows="10" label="문의내용"  style="width:100%"><?=$data->content?></textarea>
									</td>
								</tr>
								<tr>
									<th><label for="phone_chk">휴대폰 답변</label><input id="phone_chk" type="checkbox" class="chk_agree checkbox-def ml-5"></th>
									<td>
										<input type="text" class="chk_only_number" id="hp" name="hp" value="<?=$data->HP ?>" placeholder="하이픈(-) 없이 입력" title="휴대폰 번호" maxlength="15" style="width:288px; ime-mode:disabled;" readonly>
									</td>
									<th><label for="email_chk">이메일 답변</label><input id="email_chk" type="checkbox" class="chk_agree checkbox-def ml-5"></th>
									<td>
										<input type="text" id="email" name="email" value="<?=$data->email ?>" title="이메일 입력자리" style="width:288px" readonly>
									</td>
								</tr>
								<tr>
									<th>첨부</th>
									<td colspan="3" class="imageAdd">
										<input type="file" name="up_filename[]" id="up_filename">
										<div class="txt-box"><?=$data->ori_filename ?></div> <!-- 파일 업로드시 파일 주소 출력 -->
										<label for="up_filename">찾기</label>
									</td>
								</tr>
							</tbody>
						</table>
						</form>
						<?if($idx) {?>
						<div class="btn_wrap mt-30"><a href="javascript:;" class="btn-type1" id="btnSubmit">수정</a></div>
						<?}else{ ?>
						<div class="btn_wrap mt-30"><a href="javascript:;" class="btn-type1" id="btnSubmit">문의하기</a></div>			
						<?} ?>
					</div>
					<!-- // 게시판 쓰기/수정 -->
				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->

<script Language="JavaScript">
$(document).ready(function (){

	if("<?=$data->chk_sms ?>" == "Y"){
		$("#phone_chk").attr("checked", true);
		$("input[name=chk_sms]").val("Y");
		$("#hp").attr("readonly", false);
	}else{
		$("#phone_chk").attr("checked", false);
		$("input[name=chk_sms]").val("N");
		$("#hp").attr("readonly", true);
	}

	if("<?=$data->chk_mail ?>" == "Y"){
		$("#email_chk").attr("checked", true);
		$("input[name=chk_mail]").val("Y");
		$("#email").attr("readonly", false);
	}else{
		$("#email_chk").attr("checked", false);
		$("input[name=chk_mail]").val("N");
		$("#email").attr("readonly", true);
	}
	
	$("#btnSubmit").click(function(){
		if(check_form()){
			if($("#mode").val() == ""){
				$("#mode").val("insert");
			}
			$("#write_form").submit();
		}
	});

	//휴대폰 답변 체크값 설정
	$("#phone_chk, #email_chk").change(function(){
        if($("#phone_chk").is(":checked")){
        	$("input[name=chk_sms]").val("Y");
        	$("#hp").attr("readonly", false);
        }else{
        	$("input[name=chk_sms]").val("N");
        	$("#hp").attr("readonly", true);
        }
        if($("#email_chk").is(":checked")){
        	$("input[name=chk_mail]").val("Y");
        	$("#email").attr("readonly", false);
        }else{
        	$("input[name=chk_mail]").val("N");
        	$("#email").attr("readonly", true);
        }       
    });

	


	//파일첨부 파일명 설정
    $("#up_filename").change(function(){
        var filename = $("#up_filename").val();
		$(".txt-box").text(filename);
		$("input[name=ori_filename]").val(getFilename(filename));
    });  

});

//파일명 추출
function getFilename(filename) {

	var fileValue = filename.split("\\");
	var fileName = fileValue[fileValue.length-1]; // 파일명

	return fileName;
}

function check_form() {
	var procSubmit = true;

	$(".required_value").each(function(){
		if(!$(this).val()){
			if($(this).attr('label') == "상담유형"){
				alert($(this).attr('label')+"을 선택해 주세요");
			}else if($(this).attr('label') == "상담유형"){
				alert($(this).attr('label')+"을 입력해 주세요");
			}else{
				alert($(this).attr('label')+"를 정확히 입력해 주세요");
			}	
			$(this).focus();
			procSubmit = false;
			return false;
		}
	})

	if(procSubmit){
		return true;
	}else{
		return false;
	}
}



</script>

<?php  include ($Dir."lib/bottom.php") ?>
