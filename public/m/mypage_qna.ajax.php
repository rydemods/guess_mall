<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");

	$mode	= $_POST['mode'];
	$num		= $_POST['num']; //게시판 번호
	if($mode=="modify_exe") {
		$up_subject		= $_POST['up_subject']; // 제목
		$up_memo			= $_POST['up_memo']; // 내용
		$up_is_secret		= $_POST['up_is_secret']; // 1 - 비밀글 / 0 - 일반글
		$up_passwd		= $_POST['up_passwd']; // 비밀번호
		//exdebug($_POST);
		//exdebug($_FILES);
		//exdebug($_pdata);
		//exit;

		list($passwd)=pmysql_fetch("SELECT passwd FROM tblboard WHERE board = 'qna' AND num='{$num}'");
		if($passwd) {
			if ($passwd != $up_passwd) {
				echo "<html></head><body onload=\"alert('비밀번호가 다릅니다.');\"></body></html>";exit;
			}
		} else {
			echo "<html></head><body onload=\"alert('해당 문의내역이 없습니다.');\"></body></html>";exit;
		}

		$sql = "UPDATE tblboard SET ";
		$sql.= "title = '".$up_subject."', ";
		$sql.= "content = '".pmysql_escape_string( $up_memo )."', ";
		$sql.= "passwd = '".$up_passwd."', ";
		$sql.= "is_secret = '".$up_is_secret."' ";
		$sql.= "WHERE num = '".$num."' ";

		if(pmysql_query($sql,get_db_conn())) {
			echo "<html></head><body onload=\"alert('정상적으로 수정되었습니다.');parent.location.href='/m/mypage_qna.php';\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('오류가 발생하였습니다.');\"></body></html>";exit;
		}
	}/* else if ($mode == 'del_exe'){
		$sql = "SELECT * FROM tblboard WHERE board = 'qna' AND num='{$num}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$_pdata=$row;
		} else {
			echo "<html></head><body onload=\"alert('해당 문의내역이 없습니다.');\"></body></html>";exit;
		}

		$sql = "DELETE FROM tblboard WHERE board = 'qna' AND num='{$num}' ";

		if(pmysql_query($sql,get_db_conn())) {
			echo "<html></head><body onload=\"alert('정상적으로 삭제되었습니다.');parent.location.reload();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('오류가 발생하였습니다.');\"></body></html>";exit;
		}
	}*/
?>