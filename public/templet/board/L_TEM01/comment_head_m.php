<script LANGUAGE="JavaScript">
<!--

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
//-->
</script>
