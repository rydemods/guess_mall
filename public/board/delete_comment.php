<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include "head.php";

if ($setup['use_comment'] != "Y") {
	$errmsg="해당 게시판은 댓글 기능을 지원하지 않습니다.";
	alert_go($errmsg,-1);
}

if ($member['grant_comment']!="Y") {
	$errmsg="이용권한이 없습니다.";
	alert_go($errmsg,-1);
}

$qry  = "SELECT * FROM tblboardcomment WHERE board='".$board."' AND parent='".$num."' AND num='".$c_num."' ";
$result1 = pmysql_query($qry,get_db_conn());

$ok_result = pmysql_num_rows($result1);

if ((!$ok_result) || ($ok_result == -1)) {
	$errmsg="삭제할 댓글이 없습니다.\\n\\n다시 확인하시기 바랍니다.";
	
	if($mypageid){
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&mypageid=$mypageid');\"></body></html>";
		
	}else{
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";
	}
	exit;
} else {
	$row1 = pmysql_fetch_array($result1);
}


if ($_POST["mode"] == "delete") {
	
	
	if($member['admin']!="SU" && $row1[c_mem_id]!=$_ShopInfo->memid && !$mypageid) {
		if (strlen($_POST["up_passwd"])==0) {
			$errmsg="잘못된 경로로 접근하셨습니다.";
			alert_go($errmsg,-1);
		}
		if (($row1['passwd']!= substr(md5($_POST["up_passwd"]), 0, 16)) && ($setup['passwd']!=$_POST["up_passwd"])) {
			
			$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
			
			if($mypageid){
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&mypageid=$mypageid');\"></body></html>";
			}else{
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";	
			}
			exit;
		}
	}
	$del_sql = "DELETE FROM tblboardcomment WHERE board='".$board."' AND parent='".$num."' AND num = '".$_POST["c_num"]."'";
	$delete = pmysql_query($del_sql,get_db_conn());

	if ($delete) {
		@pmysql_query("UPDATE tblboard SET total_comment = total_comment - 1 WHERE board='".$board."' AND num='".$num."'",get_db_conn());
	}
	if($mypageid){
		header("Location:board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&mypageid=$mypageid");
	}else{
		header("Location:board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage");	
	}
	
} else {
	
	$info_msg="댓글 입력시 등록한 비밀번호를 입력하세요.";
	
	if($member['admin']=="SU" || ($row1[c_mem_id]==$_ShopInfo->memid && $mypageid)) {
		
		$admin_hide_start = "정말 삭제하시겠습니까?<!--";
		$admin_hide_end = "-->";
		$info_msg="";
	}
}


@include ("top.php");
?>

<script>
function check_submit() {
	try {
		if (!pwForm.up_passwd.value) {
			alert("비밀번호를 입력하여 주세요");
			pwForm.up_passwd.focus();
			return false;
		}
	} catch (e) {}
}
</script>

<form method=post action="<?=$PHP_SELF?>" onsubmit="return check_submit();" name=pwForm>
<input type=hidden name=pagetype value="delete_comment">
<input type=hidden name=num value=<?=$num?>>
<input type=hidden name=board value=<?=$board?>>
<input type=hidden name=s_check value=<?=$s_check?>>
<input type=hidden name=search value=<?=$search?>>
<input type=hidden name=block value=<?=$block?>>
<input type=hidden name=gotopage value=<?=$gotopage?>>
<input type=hidden name=c_num value=<?=$c_num?>>
<input type=hidden name=mode value="delete">
<?if($mypageid){?><input type=hidden name=mypageid value="<?=$mypageid?>"><?}?>
<?
	include ($dir."/passwd_confirm.php");
?>

</form>

<?
	@include ("bottom.php");
?>

