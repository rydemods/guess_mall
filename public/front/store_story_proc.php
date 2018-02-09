<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보 
$ap_board_cnt			= $pointSet['board']['count'];		// 게시글 지급 횟수제한
$ap_board_point			= $pointSet['board']['point'];		// 게시글 지급 포인트

//exdebug($_POST);
//exit;
$mode				= $_POST['mode'];
$sno					= $_POST['sno'];
if ($mode == 'write' || $mode == 'modify') { // 입력, 수정일 경우
	$store_code		= $_POST['store_code'];
	$v_up_filename	= $_POST['v_up_filename'];
	$file_exist			= $_POST['file_exist'];
	$title					= str_replace("'", "''", $_POST['title']);
	$content				= str_replace("'", "''", $_POST['content']);

	$imagepath          = $Dir.DataDir."shopimages/store_story/";
	$upload_add_file        = new FILE($imagepath);
	$upload_add_img         = $upload_add_file->upFiles();

	$file_exists_count  = 0;
	$chkExtArr          = array('jpg','gif','jpeg');
	$file_size_mb       = 3;
	$file_size_max      = $file_size_mb * 1024 * 1024;           // 파일 하나당 최대 용량

	$loop_idx           = 0;
	$b_check_upload_img = true;
	$fail_msg           = "";

	$arrVFile           = array();
	$arrRFile           = array();

	foreach ( $upload_add_img['up_filename'] as $arrUploadAddImg ) {
		if ( $arrUploadAddImg['error'] === false ) {
			// 파일이 존재

			$file_ext_org = pathinfo($arrUploadAddImg['r_file'], PATHINFO_EXTENSION);
			$file_ext = strtolower($file_ext_org);
			if ( !in_array($file_ext, $chkExtArr) ) {
				// 잘못된 확장자
				$b_check_upload_img = false;
				$fail_msg = $arrUploadAddImg['r_file'] . " 파일의 확장자(" . $file_ext_org . ")는 업로드 불가합니다.";
				break;
			}

			if ( $arrUploadAddImg['size'] > $file_size_max ) {
				// 용량이 초과한 경우
				$b_check_upload_img = false;
				$fail_msg = $arrUploadAddImg['r_file'] . " 파일 용량이 " . $file_size_mb . "MB를 초과할 수 없습니다.";
				break;
			}


			$arrVFile[$loop_idx] = $arrUploadAddImg['v_file'];
			$arrRFile[$loop_idx] = $arrUploadAddImg['r_file'];

			$file_exists_count++;
		}

		$loop_idx++;
	}

	if ( $b_check_upload_img === false ) {
		// 이미지 확장자가 다르거나 용량이 기준을 초과한 경우
		echo "FAIL||{$fail_msg}";
		exit;
	}

	// 번호가 넘어온 경우는 기존 업로드된 파일들을 셋팅한다.
	if ( $sno > 0 && $mode =='modify') {
		$sql  = "SELECT * FROM tblstorestory WHERE sno = {$sno} ";
		$row  = pmysql_fetch_object(pmysql_query($sql));

		for ( $i = 0; $i < $loop_idx; $i++ ) {
			if($upload_add_img["up_filename"][$i]["v_file"] || $v_up_filename[$i] == ''){
				if ( $i == 0 ) {
					$up_rFile = $row->filename;
				} else {
					$fieldName = "filename" . ($i+1);
					$up_rFile = $row->$fieldName;
				}

				if ( $up_rFile !="" || $v_up_filename[$i] =='' ) {
					$upload_add_file->removeFile($up_rFile);
					$up_vfilename    = "";   // 실제 업로드 되는 파일명
					$up_rfilename   = "";   // 원본 파일명
				}
				if($upload_add_img["up_filename"][$i]["v_file"]) $up_vfilename=$upload_add_img["up_filename"][$i]["v_file"];
				if($upload_add_img["up_filename"][$i]["r_file"]) $up_rfilename=$upload_add_img["up_filename"][$i]["r_file"];

				$arrVFile[$i] = $up_vfilename;
				$arrRFile[$i] = $up_rfilename;
			}

			if ( !isset($arrVFile[$i]) ) { 
				if ( $i == 0 ) { 
					$arrVFile[$i] = $row->filename; 
				} else {
					$varName = "filename" . ($i+1);
					$arrVFile[$i] = $row->{$varName}; 
				}
			}
			if ( !isset($arrRFile[$i]) ) { 
				if ( $i == 0 ) {
					$arrRFile[$i] = $row->vfilename;     
				} else {
					$varName = "vfilename" . ($i+1);
					$arrRFile[$i] = $row->{$varName};     
				}

				if ( !empty($arrRFile[$i]) ) {
					$file_exists_count++;
				}
			}
		}
	}

	BeginTrans();

	$flagResult = "SUCCESS";

	try {

		if ( $sno > 0 && $mode =='modify') {
			$sql  = "UPDATE tblstorestory SET ";
			$sql .= "store_code = '{$store_code}', ";
			$sql .= "filename = '{$arrVFile[0]}', ";
			$sql .= "vfilename = '{$arrRFile[0]}', ";
			$sql .= "title = '{$title}', ";
			$sql .= "content = '{$content}' ";
			$sql .= "WHERE sno = {$sno} ";
			$result = pmysql_query($sql, get_db_conn());
			if ( empty($result) ) {
				throw new Exception('Insert Fail');
			}

		} else {

			$sql  = "INSERT INTO tblstorestory ( ";
			$sql .= "   mem_id, ";
			$sql .= "   store_code, ";
			$sql .= "   filename, ";
			$sql .= "   vfilename, ";
			$sql .= "   title, ";
			$sql .= "   content, ";
			$sql .= "   regdt ";
			$sql .= ") VALUES ( ";
			$sql .= "   '" . $_ShopInfo->getMemid() ."', ";
			$sql .= "   '{$store_code}', ";
			$sql .= "   '{$arrVFile[0]}', ";
			$sql .= "   '{$arrRFile[0]}', ";
			$sql .= "   '{$title}', ";
			$sql .= "   '{$content}', ";
			$sql .= "   '".date("YmdHis")."' ";
			$sql .= ") ";

			$result = pmysql_query($sql, get_db_conn());
			if ( empty($result) ) {
				throw new Exception('Insert Fail');
			} else {
				/****************게시글 작성 포인트 지급****************************/
				// 오늘 게시글 작성시 적립받은 갯수를 체크한다.
				list($bp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) bp_cnt from tblpoint_act WHERE rel_flag='@board_in_point' and regdt >= '".date("Ymd")."000000' AND regdt <= '".date("Ymd")."999999' AND mem_id = '".$_ShopInfo->getMemid()."' "));
				if ($bp_cnt < $ap_board_cnt) { // 게시글 작성시 적립받은 갯수가 설정수보다 작으면
					insert_point_act($_ShopInfo->getMemid(), $ap_board_point, '게시글 작성 포인트', '@board_in_point', "admin_".date("YmdHis"), date("YmdHis"), 0);
				}
			}
		}

	} catch (Exception $e) {
		$flagResult = "FAIL";
		RollbackTrans();
	}
	CommitTrans();
} else if ($mode == 'delete') {

	$imagepath			= $Dir.DataDir."shopimages/store_story/";
	$upload_del_file     = new FILE($imagepath);

	$sql  = "SELECT * FROM tblstorestory WHERE sno = {$sno} ";
	$row  = pmysql_fetch_object(pmysql_query($sql));

	for ( $i = 0; $i < 1; $i++ ) {
		if ( $i == 0 ) {
			$up_rFile = $row->filename;
		} else {
			$fieldName = "filename" . ($i+1);
			$up_rFile = $row->$fieldName;
		}

		if ( $up_rFile !="") {
			$upload_del_file->removeFile($up_rFile);
		}
	}

	BeginTrans();

	$flagResult = "SUCCESS";

	try {
			$sql  = "DELETE FROM tblstorestory WHERE sno = {$sno} ";
			$result = pmysql_query($sql, get_db_conn());
			if ( empty($result) ) {
				throw new Exception('Delete Fail');
			}

			$sql  = "DELETE FROM tblstorestory_comment WHERE sno = {$sno} ";
			$result = pmysql_query($sql, get_db_conn());
			if ( empty($result) ) {
				throw new Exception('Delete Fail');
			} else {
				/****************게시글 작성 포인트 지급 환원****************************/
				insert_point_act($_ShopInfo->getMemid(), $ap_board_point * -1, '게시글 삭제 포인트 환원', '@board_del_point', "admin_".date("YmdHis"), $sno."_".date("YmdHis"), 0);
			}

	} catch (Exception $e) {
		$flagResult = "FAIL";
		RollbackTrans();
	}
	CommitTrans();

}

echo $flagResult;
?>
