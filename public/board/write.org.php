<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include ("head.php");
include_once("../lib/file.class.php");

$exec=$_REQUEST["exec"];
$mypageid=$_REQUEST["mypageid"];

if(strlen($pridx)>0) {
	$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
	if($prqnaboard!=$board) $pridx="";
}

if(strlen($mode)>0 && ($mode != "reWrite")) {
	//본문내용 필터링
	if (strlen($setup['filter'])>0) {
		if (isFilter($setup['filter'],$up_memo,$findFilter)) {
			echo "
				<script>
					window.alert('사용 제한된 불량단어를 사용하셨습니다.\\n\\n다시 확인 하십시오.');
				</script>
			";
			reWriteForm();
			exit;
		}
	}

	//관리자 사칭방지 이름 필터링
	if(strlen($setup['admin_name'])>0) {
		if(($_POST["up_name"]==$setup['admin_name']) && ($setup['passwd']!=$_POST["up_passwd"])) {
			echo "
				<script>
					window.alert('게시판 관리자 사칭 글쓰기 방지를 위하여 [{$_POST['up_name']}] 이름으로 등록이 불가능합니다.\\n\\n해당 이름으로 게시물 등록을 하실려면 게시판 관리자 비밀번호를 입력하세요.');
				</script>
			";
			reWriteForm();
			exit;
		}
	}
}

if($setup['use_html']=="N") {
	$hide_html_start="<!--";
	$hide_html_end="-->";
}

$imagepath = $Dir.DataDir."shopimages/board/".$board."/";

$banner_file = new FILE($imagepath);


if ($exec == "write") {	//글쓰기
	//글쓰기 권한 체크
	if($member['grant_write']!="Y") {
		if($setup['grant_write']=="A") {	// 관리자
			$errmsg="게시판 관리자 로그인 후 글쓰기가 가능합니다.";
			alert_go($errmsg,-1);
		} else if($setup['grant_write']=="Y") {
			if(strlen($setup['group_code'])==4) {
				$errmsg="특정 회원그룹만 글쓰기가 가능합니다.";
				alert_go($errmsg,-1);
			} else {
				$errmsg="회원 로그인 후 글쓰기가 가능합니다.";
				alert_go($errmsg,-1);
			}
		}
	}

	if(($_POST['mode']=="up_result") && ($_POST['ins4e'][mode]=="up_result") && ($_POST['up_subject']!="") && ($_POST['ins4e'][up_subject]!="")) {
		if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
			$errmsg="잘못된 경로로 접근하셨습니다.";
			alert_go($errmsg,-1);
		}

		//$thread = $setup['thread_no'] - 1;
		//if ($thread<=0) {
			$que2 = "SELECT MIN(thread) FROM tblboard ";
			$result = pmysql_query($que2,get_db_conn());
			$row = pmysql_fetch_array($result);
			
			if ($row[0]<=0) {
				$thread = 999999999;
			} else {
				$thread = $row[0] - 1;
			}
			pmysql_free_result($result);
		//}

		//해당 쇼핑몰 모든 게시판 thread값 동일하게 업데이트 (통합되어 보여질 때 유일thread값을 갖게하기 위하여)
		@pmysql_query("UPDATE tblboardadmin SET thread_no='".$thread."' ",get_db_conn());

		if($setup['use_html']=="N") $up_html="";

		//메일용 변수
		$send_email = $up_email;
		$send_name = $up_name;
		$send_subject = $up_subject;
		$send_memo = stripslashes($up_memo);
		$send_filename= $up_filename;

		if (!$up_html) {
			$send_memo = nl2br(stripslashes($up_memo));
		}
		$send_date = date("Y-m-d H:i:s");

		$up_name = pg_escape_string($up_name);
		$up_subject = str_replace("<!","&lt;!",$up_subject);
		$up_subject = pg_escape_string($up_subject);
		$up_memo = str_replace("<!","&lt;!",$up_memo);
		$up_memo = pg_escape_string($up_memo);

		if (!$up_is_secret) $up_is_secret = 0;

		$next_no = $setup['max_num'];

		if (!$next_no) {
			$que3 = "SELECT MAX(num) FROM tblboard WHERE board='".$board."' AND pos=0 AND deleted!='1'";
			$result3 = pmysql_query($que3,get_db_conn());
			$row3 = pmysql_fetch_array($result3);
			@pmysql_free_result($result3);
			$next_no = $row3[0];

			if (!$next_no) $next_no = 0;
		}
		
		if($_data->icon_type == 'tem_001'){
			
			$banner_img=$banner_file->upFiles();
			
		
			if($banner_img["up_filename"][0]["v_file"]){
				//$banner_file->removeFile($select_data->banner_img);
				$up_filename=$banner_img["up_filename"][0]["v_file"];
			}
		
		}else{
			if(ProcessBoardFileIn($board,$up_filename)!="SUCCESS") {
				$up_filename="";
			}	
		}

		
		$sql = "INSERT INTO tblboard DEFAULT VALUES RETURNING num";
		$row = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
		$up_memo=pg_escape_string($up_memo);
		$sql  = "UPDATE tblboard SET ";
		$sql .= "board				= '".$board."', ";
		$sql .= "thread				= '".$thread."', ";
		$sql .= "pos				= '0', ";
		$sql .= "depth				= '0', ";
		$sql .= "prev_no			= '0', ";
		if(strlen($pridx)>0) {
			$sql.= "pridx			= '".$pridx."', ";
		}
		$sql .= "next_no			= '".$next_no."', ";
		$sql .= "name				= '".$up_name."', ";
		$sql .= "passwd				= '".$up_passwd."', ";
		$sql .= "email				= '".$up_email."', ";
		$sql .= "is_secret			= '".$up_is_secret."', ";
		$sql .= "use_html			= '".$up_html."', ";
		$sql .= "title				= '".$up_subject."', ";
		$sql .= "filename			= '".$up_filename."', ";
		$sql .= "vfilename			= '".$up_filename."', ";
		$sql .= "writetime			= '".time()."', ";
		$sql .= "ip					= '".$_SERVER['REMOTE_ADDR']."', ";
		$sql .= "access				= '0', ";
		$sql .= "total_comment		= '0', ";
		$sql .= "content			= '".$up_memo."', ";
		$sql .= "notice				= '0', ";
		if($_ShopInfo->memid)$sql .= "mem_id				= '".$_ShopInfo->memid."', ";
		$sql .= "deleted			= '0' WHERE num={$row[0]}";
		
		$insert = pmysql_query($sql,get_db_conn());
		
		if($row[0]>0) {
			$thisNum = $row[0];

			if ($next_no) {
				$qry9 = "SELECT thread FROM tblboard WHERE board='$board' AND num='$next_no' ";
				$res9 = pmysql_query($qry9,get_db_conn());
				$next_thread = pmysql_fetch_row($res9);
				@pmysql_free_result($res9);
				pmysql_query("UPDATE tblboard SET prev_no='{$thisNum}' WHERE board='{$board}' AND thread = '{$next_thread[0]}'",get_db_conn());

				pmysql_query("UPDATE tblboard SET prev_no='{$thisNum}' WHERE board='{$board}' AND num = '{$next_no}'",get_db_conn());
			}

			// ===== 관리테이블의 게시글수 update =====
			$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1, max_num='$thisNum' ";
			$sql3.= "WHERE board='$board' ";
			$update = pmysql_query($sql3,get_db_conn());

			if (($setup['use_admin_mail']=="Y") && $setup['admin_mail']) {
				include("SendForm.inc.php");

				$title = $send_subject;
				$message = GetHeader() . GetContent($send_name, $send_email, $send_subject, $send_memo,$send_date,$send_filename,$setup['board_name']) . GetFooter();

				$tmp_admin_mail_list = explode(",",$setup['admin_mail']);

				sendMailForm($send_name,$send_email,$message,null,$bodytext,$mailheaders);
				
				for($jj=0;$jj<count($tmp_admin_mail_list);$jj++) {
					if (ismail($tmp_admin_mail_list[$jj])) {
						mail($tmp_admin_mail_list[$jj], $title, $bodytext, $mailheaders);
					}
				}
			}

			//게시판 글등록 SMS발송
			$sqlsms = "SELECT * FROM tblsmsinfo WHERE admin_board='Y' ";
			$resultsms= pmysql_query($sqlsms,get_db_conn());
			if($rowsms=pmysql_fetch_object($resultsms)){
				$sms_id=$rowsms->id;
				$sms_authkey=$rowsms->authkey;

				$totellist=$rowsms->admin_tel;
				if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
				if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
				if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
				$fromtel=$rowsms->return_tel;
				
				$smsboardname=str_replace("\\n"," ",str_replace("\\r","",strip_tags($setup['board_name'])));
				$smsboardsubject=str_replace("\\n"," ",str_replace("\\r","",strip_tags(str_replace("&lt;!","<!",stripslashes($up_subject)))));
				
				$smsmsg="에는 신규글이 ".getStringCut($smsboardsubject,20)."으로 등록되었습니다.";
				$smsmsg=getStringCut($setup['board_name'],80-strlen($smsmsg)).$smsmsg;

				$etcmsg="게시판 글등록 메세지(관리자)";
				if($rowsms->sleep_time1!=$rowsms->sleep_time2){
					$date="0";
					$time = date("Hi");
					if($rowsms->sleep_time2<"12" && $time<=sprintf("%02d59",$rowsms->sleep_time2)) $time+=2400;
					if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

					if($time<sprintf("%02d00",$rowsms->sleep_time1) || $time>=sprintf("%02d59",$rowsms->sleep_time2)){
						if($time<sprintf("%02d00",$rowsms->sleep_time1)) $day = 0;
						else $day=1;
						
						$date = date("Y-m-d",strtotime("+{$day} day")).sprintf(" %02d:00:00",$rowsms->sleep_time1);
					}
				}
				$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
				pmysql_free_result($resultsms);
			}
			
			if($mypageid){
				echo("<meta http-equiv='Refresh' content='0; URL=board.php?pagetype=list&board=$board&mypageid=$mypageid'>");
			}else{
				echo("<meta http-equiv='Refresh' content='0; URL=board.php?pagetype=list&board=$board'>");	
			}
			
			exit;
		} else {
			echo "
				<script>
				window.alert('글쓰기 입력중 오류가 발생하였습니다.');
				</script>
			";
			reWriteForm();
			exit;
		}
	} else {
		
		if ($mode == "reWrite") {
			$thisBoard=$_REQUEST["thisBoard"];
			$thisBoard['content']  = stripslashes(urldecode($thisBoard['content']));
			$thisBoard['title']  = stripslashes(urldecode($thisBoard['title']));
			$thisBoard['name']  = stripslashes(urldecode($thisBoard['name']));
		} else if (!$_REQUEST["mode"]) {

			if($member['nickname']){
				$thisBoard['name'] = $member['nickname'];
			}else{
				$thisBoard['name'] = $member['name'];
			}
			$thisBoard['email'] = $member['email'];
			$thisBoard['pridx'] = $pridx;
		}

		include ("top.php");

		if(strlen($pridx)>0 && $_data->icon_type != 'tem_001') {
			$sql = "SELECT a.productcode,a.productname,a.etctype,a.sellprice,a.quantity,a.tinyimage ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE pridx='".$pridx."' ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			$result=pmysql_query($sql,get_db_conn());
			if($_pdata=pmysql_fetch_object($result)) {
				include("prqna_top.php");
			} else {
				$pridx="";
			}
			pmysql_free_result($result);
		}

		include ($dir."/write.php");

		include ("bottom.php");
	}
} else if ($exec == "modify") {	//수정

	//게시글 수정 권한 체크
	if($setup['use_article_care']=="Y" && $member['admin']!="SU") {
		$errmsg="해당 게시판은 게시글 보호 기능을 사용중이므로 수정이 불가능합니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.";
		alert_go($errmsg,-1);
	}

	$sql2 = "SELECT * FROM tblboard WHERE board='$board' AND num = $num ";
	$result2 = pmysql_query($sql2,get_db_conn());
	$id_data=pmysql_fetch_object($result1);
	$edit_ok = pmysql_num_rows($result2);

	if ((!$edit_ok) || ($edit_ok == -1)) {
		$errmsg="수정할 게시글이 없습니다.";
		alert_go($errmsg,-1);
	}

	$row2 = pmysql_fetch_array($result2);

	if(($_POST['mode']=="up_result") && ($_POST['ins4e'][mode]=="up_result") && ($_POST['up_subject']!="") && ($_POST['ins4e'][up_subject]!="")) {
		if($member['admin']!="SU" && $id_data->mem_id!=$_ShopInfo->memid && !$mypageid) {
			if (strlen($_POST["up_passwd"])==0) {
				$errmsg="잘못된 경로로 접근하셨습니다.";
				
				if($mypageid){
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&mypageid=$mypageid');\"></body></html>";	
				}else{
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";	
				}
				exit;
			}

			if (($row2['passwd']!=$_POST["up_passwd"]) && ($setup['passwd']!=$_POST["up_passwd"])) {
				$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
				alert_go($errmsg,-1);
			}
		}

		$up_name = pg_escape_string($up_name);
		$up_subject = str_replace("<!","&lt;!",$up_subject);
		$up_subject = pg_escape_string($up_subject);
		$up_memo = str_replace("<!","&lt;!",$up_memo);
		$up_memo = pg_escape_string($up_memo);

		if (!$up_is_secret) $up_is_secret = 0;

		if($setup['use_html']=="N") $up_html="";

		$sql  = "UPDATE tblboard SET ";
		$sql .= "name			= '".$up_name."', ";
		$sql .= "email			= '".$up_email."', ";
		$sql .= "is_secret		= '".$up_is_secret."', ";
		$sql .= "use_html		= '".$up_html."', ";
		$sql .= "title			= '".$up_subject."', ";
		
		if($_data->icon_type == 'tem_001'){
			
			$banner_img=$banner_file->upFiles();
			
		
			if($banner_img["up_filename"][0]["v_file"]){
				$select_qry="select * from tblboard where board='".$board."' AND num = $num ";
				$select_result=pmysql_query($select_qry);
				$select_num=pmysql_num_rows($select_result);
				$select_data=pmysql_fetch_object($select_result);
				$banner_file->removeFile($select_data->filename);
				$up_filename=$banner_img["up_filename"][0]["v_file"];
				$sql .= "filename	= '".$banner_img["up_filename"][0]["v_file"]."', ";
				$sql .= "vfilename	= '".$banner_img["up_filename"][0]["v_file"]."', ";
			}
		
		}else{
			if ($up_filename) {
				if(ProcessBoardFileModify($board,$up_filename,$row2['filename'])=="SUCCESS") {
					$sql .= "filename	= '".$up_filename."', ";
				}
			}
		}
		
		
		$sql .= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
		$sql .= "content		= '".$up_memo."' ";
		$sql .= "WHERE board='".$board."' AND num = $num ";
		$insert = pmysql_query($sql,get_db_conn());

		if($insert) {
			
			if($mypageid){
			echo("<meta http-equiv='Refresh' content='0; URL=board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&mypageid=$mypageid'>");	
			}else{
			echo("<meta http-equiv='Refresh' content='0; URL=board.php?pagetype=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage'>");	
			}
			
			exit;
		} else {
			echo "
				<script>
				window.alert('글수정 중 오류가 발생했습니다.');
				</script>
			";
			reWriteForm();
			exit;
		}
	} else {
		if($member['admin']!="SU" && $id_data->mem_id!=$_ShopInfo->memid && !$mypageid) {
			if (strlen($_POST["up_passwd"])==0) {
				$errmsg="잘못된 경로로 접근하셨습니다.";
				if($mypageid){
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&mypageid=$mypageid');\"></body></html>";	
				}else{
				echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage');\"></body></html>";	
				}
				
				exit;
			}
			
			if(strlen($row2['passwd'])==16) {
				$sql9 = "SELECT PASSWORD('".$_POST["up_passwd"]."') AS new_passwd";
				$result9 = pmysql_query($sql9,get_db_conn());
				$row9=@pmysql_fetch_object($result9);
				$new_passwd = $row9->new_passwd;
				@pmysql_free_result($result);
			}

			if ($row2['passwd']!=$_POST["up_passwd"] && $setup['passwd']!=$_POST["up_passwd"]) {
				if(strlen($row2['passwd'])!=16 || (strlen($row2['passwd'])==16 && $row2['passwd']!=$new_passwd)) {
					$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
					alert_go($errmsg,-1);
				}
			}
			
			if(strlen($row2['passwd'])==16 && $row2['passwd']==$new_passwd) {
				@pmysql_query("UPDATE tblboard SET passwd='".$_POST["up_passwd"]."' WHERE board='".$row2['board']."' AND num='".$row2['num']."' ",get_db_conn());
				$row2['passwd']=$_POST["up_passwd"];
			}
		}

		if ($row2['filename']) {
			$thisBoard['filename'] = "기존파일을 사용하려면 파일첨부 하지 마세요.";
		}

		if ($mode == "reWrite") {
			$thisBoard['content']  = stripslashes(urldecode($thisBoard['content']));
			$thisBoard['title']  = stripslashes(urldecode($thisBoard['title']));
			$thisBoard['name']  = stripslashes(urldecode($thisBoard['name']));
		} else if (!$mode) {
			$thisBoard['pos'] = $row2['pos'];
			$thisBoard['is_secret'] = $row2['is_secret'];
			$thisBoard['name'] = stripslashes($row2['name']);
			$thisBoard['passwd'] = $row2['passwd'];
			$thisBoard['email'] = $row2['email'];
			$thisBoard['title'] = stripslashes($row2['title']);
			$thisBoard['content'] = stripslashes($row2['content']);

			if ($row2['use_html'] == "1") $thisBoard['use_html'] = "checked";
		}

		include ("top.php");

		if(strlen($row2['pridx'])>0 && $row2['pridx']>0 && $_data->icon_type != 'tem_001') {
			$sql = "SELECT a.productcode,a.productname,a.etctype,a.sellprice,a.quantity,a.tinyimage ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE pridx='".$row2['pridx']."' ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			$result=pmysql_query($sql,get_db_conn());
			if($_pdata=pmysql_fetch_object($result)) {
				include("prqna_top.php");
			} else {
				$pridx="";
			}
			pmysql_free_result($result);
		}

		include ($dir."/write.php");

		include ("bottom.php");
	}
} else if ($exec == "reply") {	//답변
	//웹진형과 앨범형 게시판은 답변쓰기가 안된다.
	if($setup['btype']!="L") {
		$errmsg="본 게시판은 답변쓰기 기능이 지원되지 않습니다.";
		alert_go($errmsg,-1);
	}

	//답변 권한 체크
	if($member['grant_reply']!="Y") {
		if($setup['grant_reply']=="Y") {
			$errmsg="게시판 회원 로그인 후 답변쓰기가 가능합니다.";
			alert_go($errmsg,-1);
		} else if($setup['grant_reply']=="A") {
			$errmsg="게시판 관리자 로그인 후 답변쓰기가 가능합니다.";
			alert_go($errmsg,-1);
		}
	}

	$sql2 = "SELECT * FROM tblboard WHERE board='$board' AND num = $num ";
	$result2 = pmysql_query($sql2,get_db_conn());
	$re_ok = pmysql_num_rows($result2);

	if ((!$re_ok) || ($re_ok == -1)) {
		$errmsg="답변할 게시글이 없습니다.";
		alert_go($errmsg,-1);
	}

	$row2 = pmysql_fetch_array($result2);

	if(($_POST['mode'] == "up_result") && ($_POST['ins4e'][mode] == "up_result") && ($_POST['up_subject'] != "") && ($_POST['ins4e'][up_subject] != "")) {		
		// ======== thread, pos, depth 정의 ========
		$sql = "UPDATE tblboard SET pos = pos+1 WHERE board='$board' AND thread={$row2['thread']} AND pos>{$row2['pos']} ";
		$update = pmysql_query($sql,get_db_conn());

		if($setup['use_html']=="N") $up_html="";

		//메일용 변수
		$send_email = $up_email;
		$send_name = $up_name;
		$send_subject = $up_subject;
		$send_memo = stripslashes($up_memo);
		$send_filename= $up_filename;
		if (!$up_html) {
			$send_memo = nl2br(stripslashes($up_memo));
		}
		
		
		$send_date = date("Y-m-d H:i:s");

		$up_name = pg_escape_string($up_name);
		$up_subject = str_replace("<!","&lt;!",$up_subject);
		$up_subject = pg_escape_string($up_subject);
		$up_memo = str_replace("<!","&lt;!",$up_memo);
		$up_memo = pg_escape_string($up_memo);

		if (!$up_is_secret) $up_is_secret = 0;

		if(ProcessBoardFileIn($board,$up_filename)!="SUCCESS") {
			$up_filename="";
		}

		$sql = "INSERT INTO tblboard DEFAULT VALUES RETURNING num";
		$row = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
		
		$sql  = "UPDATE tblboard SET ";
		$sql .= "board				= '".$board."', ";
		$sql .= "thread				= '".$row2['thread']."', ";
		$sql .= "pos				= {$row2['pos']}+1, ";
		$sql .= "depth				= {$row2['depth']}+1, ";
		$sql .= "prev_no			= '".$row2['prev_no']."', ";
		$sql .= "next_no			= '".$row2['next_no']."', ";
		if($row2['pridx'])$sql .= "pridx				= '".$row2['pridx']."', ";
		$sql .= "name				= '".$up_name."', ";
		$sql .= "passwd				= '".$up_passwd."', ";
		$sql .= "email				= '".$up_email."', ";
		$sql .= "is_secret			= '".$up_is_secret."', ";
		$sql .= "use_html			= '".$up_html."', ";
		$sql .= "title				= '".$up_subject."', ";
		$sql .= "filename			= '".$up_filename."', ";
		$sql .= "writetime			= '".time()."', ";
		$sql .= "ip					= '".$_SERVER['REMOTE_ADDR']."', ";
		$sql .= "access				= '0', ";
		$sql .= "total_comment		= '0', ";
		$sql .= "content			= '".$up_memo."', ";
		$sql .= "notice				= '0', ";
		if($_ShopInfo->memid)$sql .= "mem_id				= '".$_ShopInfo->memid."', ";
		$sql .= "deleted			= '0' WHERE num={$row[0]}";
		$insert = pmysql_query($sql,get_db_conn());

		if($row[0]>0) {
			$thisNum = $row[0];

			// ===== 관리테이블의 게시글수 update =====
			$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1 WHERE board='".$board."' ";
			$update = pmysql_query($sql3,get_db_conn());

			if (strlen($row2['email'])>0) {
				include("SendForm.inc.php");

				$title = $send_subject;
				$message = GetHeader() . GetContent($send_name, $send_email, $send_subject, $send_memo,$send_date,$send_filename,$setup['board_name']) . GetFooter();

				if($setup['admin_mail'])$tmp_admin_mail_list = split(",",$setup['admin_mail']);

				sendMailForm($send_name,$send_email,$message,null,$bodytext,$mailheaders);

				if (ismail($row2['email'])) {
					mail($row2['email'], $title, $bodytext, $mailheaders);
				}
			}

			//게시판 글등록 SMS발송
			$sqlsms = "SELECT * FROM tblsmsinfo WHERE admin_board='Y' ";
			$resultsms= pmysql_query($sqlsms,get_db_conn());
			if($rowsms=@pmysql_fetch_object($resultsms)){
				$sms_id=$rowsms->id;
				$sms_authkey=$rowsms->authkey;

				$totellist=$rowsms->admin_tel;
				if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
				if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
				if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
				$fromtel=$rowsms->return_tel;
				
				$smsboardname=str_replace("\\n"," ",str_replace("\\r","",strip_tags($setup['board_name'])));
				$smsboardsubject=str_replace("\\n"," ",str_replace("\\r","",strip_tags(str_replace("&lt;!","<!",stripslashes($up_subject)))));
				
				$smsmsg="에는 신규글이 ".getStringCut($smsboardsubject,20)."으로 등록되었습니다.";
				$smsmsg=getStringCut($setup['board_name'],80-strlen($smsmsg)).$smsmsg;

				$etcmsg="게시판 글등록 메세지(관리자)";
				if($rowsms->sleep_time1!=$rowsms->sleep_time2){
					$date="0";
					$time = date("Hi");

					if($rowsms->sleep_time2<"12" && $time<=sprintf("%02d59",$rowsms->sleep_time2)) $time+=2400;
					if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

					if($time<sprintf("%02d00",$rowsms->sleep_time1) || $time>=sprintf("%02d59",$rowsms->sleep_time2)){
						if($time<sprintf("%02d00",$rowsms->sleep_time1)) $day = 0;
						else $day=1;
						
						$date = date("Y-m-d",strtotime("+{$day} day")).sprintf(" %02d:00:00",$rowsms->sleep_time1);
					}
				}
				$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
				pmysql_free_result($resultsms);
			}
			
			if($mypageid){
			echo("<meta http-equiv='Refresh' content='0; URL=board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&mypageid=$mypageid'>");	
			}else{
			echo("<meta http-equiv='Refresh' content='0; URL=board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage'>");	
			}
			
			exit;
		} else {
			echo "
				<script>
				window.alert('글답변 중 오류가 발생했습니다.');
				</script>
			";
			reWriteForm();
			exit;
		}
	} else {

		if ($mode == "reWrite") {
			$thisBoard['content']  = stripslashes(urldecode($thisBoard['content']));
			if ($setup['use_related'] == "1") {
				$thisBoard['content'] = str_replace("cid:",$attachFileUrl,$thisBoard['content']);
			}
			$thisBoard['title']  = stripslashes(urldecode($thisBoard['title']));
			$thisBoard['summary']  = stripslashes(urldecode($thisBoard['summary']));
			$thisBoard['name']  = stripslashes(urldecode($thisBoard['name']));
		} else if (!$mode) {
			$thisBoard['pos'] = $row2['pos'];
			$thisBoard['is_secret'] = $row2['is_secret'];
			$thisBoard['use_anonymouse'] = $row2['use_anonymouse'];
			$thisBoard['sitelink1'] = $row2['sitelink1'];
			$thisBoard['sitelink2'] = $row2['sitelink2'];
			$thisBoard['name'] = $member['name'];
			$thisBoard['email'] = $member['email'];
			$thisBoard['category'] = $row2['category'];

			$thisBoard['title'] = stripslashes($row2['title']);
			$thisBoard['summary'] = stripslashes($row2['summary']);

			$thisBoard['content'] = stripslashes($row2['content']);

			$thisBoard['title']    = "[답변]" . $thisBoard['title'];

			if ($setup['use_related'] == "1") {
				$thisBoard['content'] = str_replace("cid:",$attachFileUrl,$thisBoard['content']);
				if ($wtype == "special" && $row2['use_html'] != "1") {
					$thisBoard['content'] = nl2br($thisBoard['content']);
				}

				if ($wtype == "special" || $row2['use_related'] == "1") {
					$tmp_content = $thisBoard['content'];
					$thisBoard['content'] = "<br><br><blockquote type=cite style=\"border-left: #000000 solid 2px; margin-left: 5px; padding-left: 5px\">";
					$thisBoard['content'] .= "<div style=\"font: 10pt arial\">-------- \n\n\n'".stripslashes($row2['name'])."'님이 쓰신글 --------\n";
					$thisBoard['content'] .= "<div><br></div>\n";
					$thisBoard['content'] .= $tmp_content."</blockquote>";
				} else {
					$thisBoard['content']  = "\n\n\n'".stripslashes($row2['name'])."'님이 쓰신글\n";
					$thisBoard['content'] .= "------------------------------------\n";
					$thisBoard['content'] .= ">" . str_replace(chr(10), chr(10).">", $row2['content']) . "\n";
					$thisBoard['content'] .= "------------------------------------\n";
				}
			} else {
				$thisBoard['content']  = "\n\n\n'".stripslashes($row2['name'])."'님이 쓰신글\n";
				$thisBoard['content'] .= "------------------------------------\n";
				$thisBoard['content'] .= ">" . str_replace(chr(10), chr(10).">", $row2['content']) . "\n";
				$thisBoard['content'] .= "------------------------------------\n";
			}
		}

		include ("top.php");

		if(strlen($row2['pridx'])>0 && $row2['pridx']>0 && $_data->icon_type != 'tem_001') {
			$sql = "SELECT a.productcode,a.productname,a.etctype,a.sellprice,a.quantity,a.tinyimage ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE pridx='".$row2['pridx']."' ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			$result=pmysql_query($sql,get_db_conn());
			if($_pdata=pmysql_fetch_object($result)) {
				include("prqna_top.php");
			} else {
				$pridx="";
			}
			pmysql_free_result($result);
		}

		include ($dir."/write.php");

		include ("bottom.php");
	}
} else {
	$errmsg="잘못된 페이지입니다.";
	alert_go($errmsg,-1);
}
