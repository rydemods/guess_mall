<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>" >
    <input type=hidden name=idx value="<?=$idx?>">
    <input type=hidden name=listnum value="<?=$listnum?>">
    <input type=hidden name=block value="<?=$block?>">
    <input type=hidden name=gotopage value="<?=$gotopage?>">
    <input type=hidden name=view_mode value="<?=$view_mode?>">
    <input type=hidden name=view_type value="<?=$view_type?>">
    <input type=hidden name=event_type value="<?=$event_type?>">
</form>

<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type="text/javascript">

function chk_from() {
    if ( $("#keyword").val().trim() == "" ) {
        // 검색어가 없는 경우
        alert("검색어를 입력해 주세요.");
        $("#search_word").val("").focus();
        return false;
    }

    document.form2.block.value = "";
    document.form2.gotopage.value = "";
}

function GoPage(block,gotopage) {
    document.form2.block.value=block;
    document.form2.gotopage.value=gotopage;
    document.form2.submit();
}

function putSubject(subject) {
	document.writeForm.up_subject.value = subject;
}

function FileUp() {
	fileupwin = window.open("","fileupwin","width=50,height=50,toolbars=no,menubar=no,scrollbars=no,status=no");
	while (!fileupwin);
	document.fileform.action = "<?=$Dir.BoardDir?>ProcessBoardFileUpload.php"
	document.fileform.target = "fileupwin";
	document.fileform.submit();
	fileupwin.focus();
}

function chkLoginCommentForm() {
    var mem_id = '<?=$_ShopInfo->getMemid()?>';

    if ( mem_id === "" ) {
        alert("로그인이 필요합니다.");
<? if ( $isMobile ) { ?>
        location.href = '/m/login.php?chUrl=<?=urlencode($_SERVER[REQUEST_URI])?>';
<? } else { ?>
        location.href = '/front/login.php?chUrl=<?=urlencode($_SERVER[REQUEST_URI])?>';
<? } ?>
        return false;
    } else {
        return true;
    }
}

function chkCommentForm() {

/*
    if (!comment_form.up_name.value) {
        alert('이름을 입력 하세요.');
        comment_form.up_name.focus();
        return false;
    }
    if (!comment_form.up_passwd.value) {
        alert('패스워드를 입력 하세요.');
        comment_form.up_passwd.focus();
        return false;
    }
*/

    // 로그인이 되어 있는지 체크
    if ( chkLoginCommentForm() === true ) {
        if ( $("#up_comment").val().trim() === "" ) {
            alert("내용을 입력하세요.");
            $("#up_comment").val("").focus();
            return false;
        } else if ( parseInt($("#messagebyte").text()) < 20 ) {
            alert("20자 이상 입력하셔야 합니다.");
            $("#up_comment").focus();
            return false;
        }
    } else {
        return false;
    }
}

var clearChk=true;
var limitByte = 300; //바이트의 최대크기, limitByte 를 초과할 수 없슴

// textarea에 마우스가 클릭되었을때 초기 메시지를 클리어
function clearMessage(frm){
    // 로그인 여부 체크
    chkLoginCommentForm();

    if(clearChk){
        $("#up_comment").val("");
        clearChk=false;
    }
}

// textarea에 입력된 문자의 바이트 수를 체크
function checkByte(frm) {

    var totalByte = 0;
    var message = $("#up_comment").val();

    for(var i =0; i < message.length; i++) {
        var currentByte = message.charCodeAt(i);
        if(currentByte > 128) totalByte += 2;
        else totalByte++;
    }

    $("#messagebyte").text(totalByte);

    if(totalByte > limitByte) {
        alert( limitByte+"바이트까지 전송가능합니다.");
        $("#up_comment").val(message.substring(0,limitByte));
    }
}

// 아래 부분은 필요할까 싶다....
/*
function chkCommentForm2(frm) {

    if (!frm.up_name_two.value) {
        alert('이름을 입력 하세요.');
        frm.up_name_two.focus();
        return false;
    }
    if (!frm.up_passwd_two.value) {
        alert('패스워드를 입력 하세요.');
        frm.up_passwd_two.focus();
        return false;
    }

    if (!frm.up_comment_two.value) {
        alert('내용을 입력 하세요.');
        frm.up_comment_two.focus();
        return false;
    }
}

$(document).ready(function(){
    $('.comment_reply_btn').click( function( e ) {
        $_this = e.target;
        $($_this).parent().next().toggle();
        $($_this).next().toggle();
        $($_this).toggle();
    });

    $('.cancle').click( function( e ) {
        $_this = e.target;
        $($_this).parent().next().toggle();
        $($_this).prev().toggle();
        $($_this).toggle();
    });

});
*/

$("#list_btn").on("click", function() {
    location.href = "/front/promotion.php?view_mode=<?=$view_mode?>&view_type=<?=$view_type?>";
});

function chk_writeForm(form) {

	if (typeof(form.tmp_is_secret) == "object") {
		form.up_is_secret.value = form.tmp_is_secret.options[form.tmp_is_secret.selectedIndex].value;
	}

/*
	if (!form.up_name.value) {
		alert('닉네임을 입력하십시오.');
		form.up_name.focus();
		return;
	}

	if (!form.up_passwd.value) {
		alert('비밀번호를 입력하십시오.');
		form.up_passwd.focus();
		return;
	}
*/

	if (!form.up_subject.value) {
		alert('제목을 입력하십시오.');
		form.up_subject.focus();
		return;
	}

    try {
        var sHTML = oEditors.getById["ir1"].getIR();
        form.up_memo.value=sHTML;
    } catch(err) {
        // do nothing
    }

	if (!form.up_memo.value) {
		alert('내용을 입력하십시오.');
		form.up_memo.focus();
		return;
	}

    if ( form.file_exist1.value == "N" ) {
        alert("첨부1은 필수입니다.");
        return;
    }

	form.mode.value = "up_result";
	reWriteName(form);
	form.submit();
}

$("#photo_list_btn").on("click", function() {
//    location.href = "/front/promotion_detail.php?idx=<?=$idx?>&view_mode=<?=$view_mode?>&view_type=<?=$view_type?>&event_type=<?=$event_type?>";
    location.href = "?idx=<?=$idx?>&view_mode=<?=$view_mode?>&view_type=<?=$view_type?>&event_type=<?=$event_type?>";
});

function goLogin() {
    <?php
//        $url = $Dir.FrontDir."login.php?chUrl=";
        $url = "login.php?chUrl=";
    ?>
    if ( confirm("로그인이 필요합니다.") ) {
        location.href = "<?=$url?>" + encodeURIComponent('<?=$_SERVER['REQUEST_URI']?>');
    }
}

var oEditors = [];
var flagMakeEditor = false;

// 실제 레이어를 띄울 때 생성하게 처리(물론 한번만)
$(".photo-event-write").on("click", function() {

    <?php if(strlen($_ShopInfo->getMemid())==0) {
        $url = $Dir.FrontDir."login.php?chUrl=";
    ?>
        if ( confirm("로그인이 필요합니다.") ) {
            location.href = "<?=$url?>" + encodeURIComponent('<?=$_SERVER['REQUEST_URI']?>');
        }
    <?php } else { ?>

    if ( flagMakeEditor == false ) {
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: "ir1",
            sSkinURI: "../SE2/SmartEditor2Skin.html",
            htParams : {
                bUseToolbar : true,             // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseVerticalResizer : false,     // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseModeChanger : true,         // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                //aAdditionalFontList : aAdditionalFontSet,     // 추가 글꼴 목록
                fOnBeforeUnload : function(){
                }
            },
            fOnAppLoad : function(){
            },
            fCreator: "createSEditor2"
        });

        flagMakeEditor = true;
    }

    <?php } ?>
});

// 댓글이벤트의 댓글 삭제
function delete_comment(board, num) {
    if ( confirm("삭제하시겠습니까?") ) {
        var params = {
            board : board,
            num : num
        };

        $.ajax({
            type: "get",
            url: "/front/ajax_delete_promo_comment.php",
            data: params
        }).success(function ( result ) {
            if ( result == "SUCCESS" ) {
                alert('삭제되었습니다.');
                location.reload();
            } else {
                alert('다시 시도해 주세요.');
            }
        }).fail(function () {
            alert('다시 시도해 주세요.');
        });
    }
}

/*$(document).ready(function() {
    Kakao.init('f98b9dc26d0fb025add7216b13cc2496');

    Kakao.Link.createTalkLinkButton({
      container: '#kakao-link-btn',
      label: '<?=$sns_text?>',
      image: {
        src: '<?=$sns_thumb_img?>',
        width: '300',
        height: '200'
      },
      webButton: {
        text: '<?=$_data->shoptitle?>',
        url: 'http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>' // 앱 설정의 웹 플랫폼에 등록한 도메인의 URL이어야 합니다.
      }
    });
});*/

function sns(select, text){

	var Link_url = "http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>";
	var sns_url = "";

	if(select =='facebook'){//페이스북
		sns_url = "http://www.facebook.com/sharer.php?u="+encodeURIComponent(Link_url);
	}
	if(select =='twitter'){//트위터
		sns_url = "http://twitter.com/intent/tweet?text="+encodeURIComponent(text)+"&url="+ Link_url + "&img" ;
	}
	if( select == 'kakao' ){

		Kakao.Story.share({
          url: Link_url,
          text: text
        });

	}
	if(select == "band"){
		sns_url = 'http://band.us/plugin/share?body=' + encodeURIComponent(text) + '&route=' + encodeURIComponent(Link_url);
		console.log(sns_url);
	} 

	var popup= window.open(sns_url,"_snsPopupWindow", "width=500, height=500");
	popup.focus();

}


</script>
