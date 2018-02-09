<?php include_once('outline/header_m.php'); ?>
<?php

$productcode     = $_GET['productcode'];   // 상품 productcode
$pridx      = $_GET['pridx'];   // 상품 pridx
$qna_num    = $_GET['qna_num'];     // 상품 Q&A 글번호

$_subject   = "";
$_content   = "";
$_oldpass   = "";
$_is_secret = "";
$_hp ="";
$_email ="";
$_hp_check ="";
$_email_check ="";

if ( !empty($qna_num) ) {
    $sql    = "SELECT title, content, passwd, is_secret, hp, email, sms_send, email_send FROM tblboard WHERE num = {$qna_num} LIMIT 1";
    list($_subject, $_content, $_oldpass, $_is_secret, $_hp, $_email, $_hp_check, $_email_check) = pmysql_fetch($sql);
}

$_is_secret = $_is_secret ? $_is_secret : "0";

?>

<!-- <div class="sub-title">
    <h2>상품 Q&#38;A</h2>
    <a class="btn-prev" href="product_qna.php?productcode=<?=$productcode?>&pridx=<?=$pridx?>"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
</div>

<p class="goods-qna-note">
    고객님의 문의에 최대한 빨리 답변 드리도록 하겠습니다.<br>
    질문에 대한 답변은 마이페이지에서도 확인 하실 수 있습니다.
</p> -->

<!-- Q&A 글쓰기 -->
<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>상품문의</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="mypage_sub write_qna">
	<div class="order_table">
		<table class="my-th-left form_table">
			<colgroup>
				<col style="width:35%;">
				<col style="width:65%;">
			</colgroup>
			<tbody>
				<tr>
					<th>제목<span class="required">*</span></th>
					<td><input type="text" id="qna-subject" title="제목" value="<?=$_subject?>"></td>
				</tr>
				<tr>
					<th>내용<span class="required">*</span></th>
					<td><textarea id="qna-content" title="내용" value="<?=$_content ?>"><?=$_content ?></textarea></td>
				</tr>
				<tr>
					<th><label for="phone_chk">휴대폰 번호</label><!-- <input id="phone_chk"  type="checkbox" class="chk_agree checkbox-def ml-5">--></th>
					<td><input type="tel" id="hp" name="hp" title="휴대폰 번호" value="<?=$_hp ?>" placeholder="하이픈(-) 없이 입력"></td>
				</tr>
				<tr>
					<th><label for="email_chk">이메일</label><!--<input id="email_chk" type="checkbox" class="chk_agree checkbox-def ml-5">--></th>
					<td><input type="email"  id="email" name="email" title="이메일 아이디 입력자리" value="<?=$_email ?>"></td>
				</tr>
				<tr>
					<th>공개여부</th>
					<td>
						<!-- <label><input name="view-type" id="view" value='0' class="radio-def" type="radio" <?php if ( $_is_secret == "0" || empty($_is_secret) ) { echo "checked"; } ?>><span>공개</span></label>
						<label><input name="view-type" id="no-view" value='1' class="radio-def" type="radio" <?php if ( $_is_secret == "1" ) { echo "checked"; } ?> ><span>비공개</span></label> -->

						<label class="check_round private">
							<?if($_is_secret == "0"){ ?>
							<input type="checkbox" class="CLS_view-type" checked>
							
							<?}else{ ?>
							<input type="checkbox" class="CLS_view-type">
							<?} ?>
						</label>
					</td>
				</tr>
				<tr>
					<th>비밀번호<span class="required">*</span></th>
					<td>
						<input type="password" id="qna-pwd">
						<input type=hidden name=oldpass id=oldpass value="<?=$_oldpass?>" >
					</td>
				</tr>
			</tbody>
		</table>
	</div>

    <!-- <section class="write-open">
        <h3>공개여부</h3>
        <label><input name="view-type" id="view" value='0' class="radio-def" type="radio" <?php if ( $_is_secret == "0" || empty($_is_secret) ) { echo "checked"; } ?>><span>공개</span></label>
        <label><input name="view-type" id="no-view" value='1' class="radio-def" type="radio" <?php if ( $_is_secret == "1" ) { echo "checked"; } ?> ><span>비공개</span></label>
    </section>
    <section class="write-pw">
        <h3>비밀번호</h3>
        <input type="password" id="qna-pwd">
        <input type=hidden name=oldpass id=oldpass value="<?=$_oldpass?>" >
    </section>
    <section class="write-title">
        <h3>제목</h3>
        <input type="text" id="qna-subject" placeholder="제목을 입력하세요" title="제목" value="<?=$_subject?>" >
    </section>
    <section class="write-content">
        <h3>내용</h3>
        <textarea id="qna-content" placeholder="내용을 입력하세요" title="내용"><?=$_content?></textarea>
    </section>
    <p class="write-note">
        상품에 관한문의만 작성해주세요<br>
        배송,결제,교환/반품에 대한 문의는 1:1문의를 이용해주세요
    </p> -->

	<a class="btn-point" href="javascript:;" onclick='javascript:QnAController();'>문의하기</a>
	<!-- <a class="btn-def" href="product_qna.php?productcode=<?=$productcode?>&pridx=<?=$pridx?>">목록</a> -->

    <input type="hidden" id="up_name" name="up_name" value="<?=$_ShopInfo->getmemname()?>">
    <input type="hidden" id="pridx" value="<?=$pridx?>" >
    <input type="hidden" id="qna-num" value="<?=$qna_num?>" >
    <input type='hidden' name='chk_sms' value="<?=$_hp_check?>">
	<input type='hidden' name='chk_mail' value="<?=$_email_check?>">
	<input type='hidden' name='view-type' value="<?=$_is_secret?>">
</div>
<!-- // Q&A 글쓰기 -->

<script type="text/javascript">
$(document).ready(function (){
	var sms_send = $("input[name=chk_sms]").val();
	var email_send = $("input[name=chk_mail]").val();

/*	
	if(sms_send == "1"){
		$("#phone_chk").attr("checked", true);
		$("input[name=chk_sms]").val("1");
		$("#hp").attr("readonly", false);
    	$("#hp").attr("class","chk_only_number required_value");
    	$("#phone_chk").after("<span class='required' id='phoneChk'></span>");
	}else{
		$("#phone_chk").attr("checked", false);
		$("input[name=chk_sms]").val("0");
		$("#hp").attr("readonly", true);
    	$("#hp").attr("class","chk_only_number");
    	$("#phoneChk").remove();
	}

	if(email_send == "1"){
		$("#email_chk").attr("checked", true);
		$("input[name=chk_mail]").val("1");
		$("#email").attr("readonly", false);
    	$("#email").attr("class","required_value");
    	$("#email_chk").after("<span class='required' id='emailChk'></span>");
	}else{
		$("#email_chk").attr("checked", false);
		$("input[name=chk_mail]").val("0");
		$("#email").attr("readonly", true);
    	$("#email").attr("class","chk_only_number");
    	$("#emailChk").remove();
	}

	
	//휴대폰 답변 체크값 설정
	$("#phone_chk").change(function(){
        if($("#phone_chk").is(":checked")){
        	$("input[name=chk_sms]").val("1");
        	$("#hp").attr("readonly", false);
        	$("#hp").attr("class","chk_only_number required_value");
        	$("#phone_chk").after("<span class='required' id='phoneChk'></span>");
        }else{
        	$("input[name=chk_sms]").val("0");
        	$("#hp").attr("readonly", true);
        	$("#hp").attr("class","chk_only_number");
        	$("#phoneChk").remove();
        }

	});

	//이메일 답변 체크값 설정
	$("#email_chk").change(function(){
        if($("#email_chk").is(":checked")){
        	$("input[name=chk_mail]").val("1");
        	$("#email").attr("readonly", false);
        	$("#email").attr("class","required_value");
        	$("#email_chk").after("<span class='required' id='emailChk'></span>");
        }else{
        	$("input[name=chk_mail]").val("0");
        	$("#email").attr("readonly", true);
        	$("#email").attr("class","chk_only_number");
        	$("#emailChk").remove();
        }
    });

*/	
	//공개여부
    $(".CLS_view-type").change(function() {
    	if($(".CLS_view-type").is(":checked")){
        	//공개
    		$("input[name=view-type]").val("0");
    	}else{
        	//비공개
    		$("input[name=view-type]").val("1");
    	}	
    });    

});    
    // qna 게시판 입력 컨트롤변수 true - insert / false - modify

    <?php if ( empty($qna_num) ) { ?>
        var _QnAController = true;
    <?php } else { ?>
        var _QnAController = false;
    <?php } ?>



    // QnA insert / modify
    function QnAController(){
        if( _QnAController ) InsertAjaxQna();
        else ModifyAjaxQna();
    }
    // QnA 입력
    function InsertAjaxQna(){

        if(  QnaFormCheck() ) return; //넘어가는 폼 체크
		var email = $("#email").val();  //이메일
		var hp = $("#hp").val();  //휴대폰 번호
		if(hp !=""){
			$("input[name=chk_sms]").val("1");
		}else{
			$("input[name=chk_sms]").val("0");
		}	

		if(email != ""){
			$("input[name=chk_mail]").val("1");
		}else{
			$("input[name=chk_mail]").val("0");
		}	
        var pridx        = $('#pridx').val(); //상품 idx
        var up_subject   = $('#qna-subject').val(); // 제목
        var up_memo      = $('#qna-content').val(); // 내용
        var up_is_secret = $('input[name="view-type"]').val(); //공개여부
        var up_passwd    = $('#qna-pwd').val(); // 비밀번호
        var up_name      = $('#up_name').val(); // 닉네임
		var hp_chk =  $("input[name=chk_sms]").val();  //휴대폰 번호 체크
		var email_chk =  $("input[name=chk_mail]").val();  //이메일 번호 체크


        $.ajax({
            type: "POST",
            url: "../board/board.php",
            data: {
                'mode' : 'up_result',
                'ins4e[mode]' : 'up_result',
                'ins4e[up_subject]' : 'up_result',
                'pagetype' : 'write',
                'exec' : 'write',
                'board' : 'qna',
                pridx : pridx,
                up_subject : up_subject,
                up_memo : up_memo,
                up_is_secret : up_is_secret,
                up_passwd : up_passwd,
                up_name : up_name,
				hp : hp,
				email : email,
				'sms-send' : hp_chk,
				'email-send' : email_chk,
            }
        }).done( function( data ){
            alert("등록되었습니다.");
            location.href="mypage_qna.php";
          //  location.href='product_qna.php?productcode=<?=$productcode?>&pridx=<?=$pridx?>';
        });
    }
    // QnA 내용 확인
    function QnaFormCheck(){

        if( $('#qna-subject').val().length <= 0 ){
            alert('제목을 입력해주세요');
            $('#qna-subject').focus();
            return true;
        }

        if( $('#qna-content').val().length <= 0 ){
            alert('내용을 입력해주세요');
            $('#qna-content').focus();
            return true;
        }

        if( $('#qna-pwd').val().length <= 0 ){
            alert('비밀번호를 입력해주세요');
            $('#qna-pwd').focus();
            return true;
        }

        return false;

    }
    // QnA 삭제
    function DeleteAjaxQna( obj, num ){
        if( confirm('삭제하시겠습니까?') ){
            //삭제시 비밀번호 입력 창 필요
            //up_passwd
            var passwd  = $(obj).next().next().next().next().val();
            $.ajax({
                type: "POST",
                url: "../board/board.php",
                data: {
                    'pagetype' : 'delete',
                    'exec' : 'delete',
                    'board' : 'qna',
                    mode : 'delete_ajax',
                    up_passwd : passwd,
                    num : num
                }
            }).done( function( data ){
                location.reload();
                //console.log( data );
            });
            /*$.ajax({
                type: "POST",
                url: "../board/board.php",
                data: {
                    'pagetype' : 'delete',
                    'exec' : 'delete',
                    'board' : 'qna',
                    num : num
                }
            }).done( function( data ){
                //location.reload();
                console.log( data );
            });*/
        }
    }
    // QnA 수정
    function ModifyAjaxQna(){
        if(  QnaFormCheck() ) return; //넘어가는 폼 체크

        
		var hp = $("#hp").val();  //휴대폰 번호
		var email = $("#email").val();  //이메일
		if(hp !=""){
			$("input[name=chk_sms]").val("1");
		}else{
			$("input[name=chk_sms]").val("0");
		}	

		if(email != ""){
			$("input[name=chk_mail]").val("1");
		}else{
			$("input[name=chk_mail]").val("0");
		}	
        var up_subject   = $('#qna-subject').val(); // 제목
        var up_content      = $('#qna-content').val(); // 내용
        var up_is_secret = $('input[name="view-type"]').val(); //공개여부
        var up_passwd    = $('#qna-pwd').val(); // 비밀번호
        var up_name      = $('#up_name').val(); // 닉네임
        var num          = $('#qna-num').val(); //게시판 번호
		var up_sms_send =  $("input[name=chk_sms]").val();  //휴대폰 번호 체크
		var up_email_send =  $("input[name=chk_mail]").val();  //휴대폰 번호 체크

        if( $('#qna-pwd').val().length <= 0 ){
            alert('비밀번호를 입력해주세요');
            $('#qna-pwd').focus();
            return true;
        } else {
            if ($('#oldpass').val() != $('#qna-pwd').val())
            {
            alert('비밀번호가 다릅니다.');
            $('#qna-pwd').val("").focus();
            return true;
            }
        }

        if( confirm('수정하시겠습니까?') ){
            $.ajax({
                type: "POST",
                url: "../front/ajax_modify_qna.php",
                data: {
                    num : num,
                    up_subject : up_subject,
					up_content : up_content,
                    up_is_secret : up_is_secret,
                    up_passwd : up_passwd,
                    up_name : up_name,
					up_hp : hp,
					up_sms_send : up_sms_send,
					up_email : email,
					up_email_send : up_email_send,
                }
            }).done( function( data ){
                alert("수정되었습니다.");
                location.href="mypage_qna.php";
			    //location.href='product_qna.php?productcode=<?=$productcode?>&pridx=<?=$pridx?>';
            });
        }
    }

</script>

<?php include_once('outline/footer_m.php'); ?>

