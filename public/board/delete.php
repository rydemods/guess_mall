<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include ("head.php");


if($setup['use_article_care']=="Y" && $member['admin']!="SU") {
	$errmsg="해당 게시판은 게시글 보호 기능을 사용중이므로 삭제가 불가능합니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.";
	alert_go($errmsg,-1);
}

$qry  = "SELECT * FROM tblboard WHERE board='".$board."' AND num='".$num."' ";
$del_result = pmysql_query($qry,get_db_conn());
$id_data=pmysql_fetch_object($result1);
$del_ok = pmysql_num_rows($del_result);



if ((!$del_ok) || ($del_ok == -1)) {
	$errmsg="삭제할 글이 없습니다.\\n\\n다시 확인 하십시오.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";exit;
} else {
	$del_row = pmysql_fetch_array($del_result);

	if ($mode == "delete" || $_POST['mode']=="delete_ajax") {
		if($member['admin']!="SU" && $id_data->mem_id!=$_ShopInfo->memid && !$mypageid) {
			if (strlen($_POST["up_passwd"])==0) {
				$errmsg="잘못된 경로로 접근하셨습니다.1";
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";exit;
			}

			if ($_POST['mode']=="delete_ajax") {
				$_POST["up_passwd"] = crypt($_POST["up_passwd"],"passwd");
			}

			if ((crypt($del_row['passwd'],"passwd") != $_POST["up_passwd"]) && crypt($setup['passwd'],"passwd") != $_POST["up_passwd"]) {
				$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";exit;
			}
		}

		if ($del_row['pos'] <> 0) {
			// 게시물을 삭제하자
			$sql  = "DELETE FROM tblboard WHERE board='".$board."' AND num=".$num." ";
			$isUpdate = true;
		} else {
			$sql2  = "SELECT COUNT(*) FROM tblboard WHERE board='".$board."' AND thread=".$del_row['thread']." ";
			$result2 = pmysql_query($sql2,get_db_conn());
			$deleteTotal = pmysql_result($result2,0,0);
			pmysql_free_result($result2);

			if ($deleteTotal == 1) {
				$sql  = "DELETE FROM tblboard WHERE board='".$board."' AND num = ".$num." ";
				$isUpdate = true;
			} else {
				$delMsg = "관리자 또는 작성자에 의해 삭제되었습니다.";
				$sql  = "UPDATE tblboard SET ";
				$sql .= "prev_no = 0, ";
				$sql .= "next_no = 0, ";
				$sql .= "use_html = '0', ";
				$sql .= "title = '".$delMsg."', ";
				$sql .= "filename = '', ";
				$sql .= "total_comment = 0, ";
				$sql .= "content = '".$delMsg."', ";
				$sql .= "notice = '0', ";
				$sql .= "deleted = '1' ";
				$sql .= "WHERE board='".$board."' AND num=".$num." ";
			}
		}
		$delete = pmysql_query($sql,get_db_conn());

		if($delete) {
			if($del_row['prev_no']) pmysql_query("UPDATE tblboard SET next_no='".$del_row['next_no']."' WHERE board='".$board."' AND next_no='".$del_row['num']."'",get_db_conn());
			if($del_row['next_no']) pmysql_query("UPDATE tblboard SET prev_no='".$del_row['prev_no']."' WHERE board='".$board."' AND prev_no='".$del_row['num']."'",get_db_conn());

			// ===== 관리테이블의 게시글수 update =====
			$in_max_qry='';
			$in_total_qry='';
			if ($del_row['pos'] == 0) {
				if ($del_row['prev_no'] == 0) {
					$in_max_qry = "max_num = '{$del_row['next_no']}' ";
				}
			}
			if ($isUpdate) {
				$in_total_qry = "total_article = total_article - 1 ";
			}

			$sql3 = "UPDATE tblboardadmin SET ";
			if ($in_max_qry) $sql3.= $in_max_qry;
			if ($in_max_qry && $in_total_qry) $sql3.= ",".$in_total_qry;
			else if (!$in_max_qry && $in_total_qry) $sql3.= $in_total_qry;
			$sql3.= "WHERE board='".$board."' ";

			if ($in_max_qry || $in_total_qry) $update = pmysql_query($sql3,get_db_conn());

			if ($setup['use_comment']=="Y" && $del_row['total_comment'] > 0) {
				@pmysql_query("DELETE FROM tblboardcomment WHERE board='".$board."' AND parent = '".$del_row['num']."'",get_db_conn());
			}

			if($del_row['filename']) {
				$filedel=ProcessBoardFileDel($board,$del_row['filename']);
			}
			
			if ($_POST['mode']=="delete_ajax") {
				echo 'true';
			} else {
				if($mypageid){
					echo("<meta http-equiv='Refresh' content='0; URL=board.php?pagetype=list&board=$board&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&mypageid=$mypageid'>");	
				}else{
					echo("<meta http-equiv='Refresh' content='0; URL=board.php?pagetype=list&board=$board&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check'>");
				}
			}
			
			exit;
		} else {
			$errmsg="글삭제 중 오류가 발생했습니다.";
			
			if ($_POST['mode']=="delete_ajax") {
				echo 'false';
			} else {			
				if($mypageid){
					echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&mypageid=$mypageid');\"></body></html>";
				}else{
					echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";	
				}
			}
			exit;
		}

	} else {
		if ($member['admin']!="SU" && $id_data->mem_id!=$_ShopInfo->memid && !$mypageid) {
			if (strlen($_POST["up_passwd"])==0) {
				$errmsg="잘못된 경로로 접근하셨습니다.2";
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";exit;
			}
			
			if(strlen($del_row['passwd'])==16) {
				$sql9 = "SELECT PASSWORD('".$_POST["up_passwd"]."') AS new_passwd";
				$result9 = pmysql_query($sql9,get_db_conn());
				$row9=@pmysql_fetch_object($result9);
				$new_passwd = $row9->new_passwd;
				@pmysql_free_result($result);
			}

			if ($del_row['passwd']!=$_POST["up_passwd"] && $setup['passwd']!=$_POST["up_passwd"]) {
				if(strlen($del_row['passwd'])!=16 || (strlen($del_row['passwd'])==16 && $del_row['passwd']!=$new_passwd)) {
					$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
					echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";exit;
				}
			}
			
			if(strlen($del_row['passwd'])==16 && $del_row['passwd']==$new_passwd) {
				@pmysql_query("UPDATE tblboard SET passwd='".$_POST["up_passwd"]."' WHERE board='".$del_row['board']."' AND num='".$del_row['num']."' ",get_db_conn());
				$del_row['passwd']=$_POST["up_passwd"];
			}
		}

		$thisBoard['name'] = stripslashes($del_row['name']);
		$thisBoard['email'] = $del_row['email'];
		$thisBoard['title'] = stripslashes($del_row['title']);

		include ("top.php");

		include ($dir."/delete.php");

		include ("bottom.php");
	}
}

