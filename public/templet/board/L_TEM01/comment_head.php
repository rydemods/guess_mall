<?if($_data->icon_type == 'tem_001'){?>

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
<?}else{?>
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0" bgcolor="#94BEEB" style="table-layout:fixed">
		<tr>
			<td>
			<TABLE width="100%" CELLSPACING="0" cellpadding="0" border="0" bgcolor="#FFFFFF" style="TABLE-LAYOUT:FIXED">
			<TR> 
				<TD style="font-size:11px;letter-spacing:-0.5pt;padding:5px;"><img src="<?=$imgdir?>/board_icon_8a.gif" border="0"> <b>댓글 현재 <b><font color="#FF6600"><?=$this_comment?></font></b>건</TD>
			</TR>
			</TABLE>
<?}?>