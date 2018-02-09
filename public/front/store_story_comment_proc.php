<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보 
$ap_comment_cnt		= $pointSet['comment']['count'];	// 댓글 지급 횟수제한
$ap_comment_point	= $pointSet['comment']['point'];	// 댓글 지급 포인트


//exdebug($_POST);
//exit;
$mode				= $_POST['mode'];

if ($mode == 'write') { // 입력일 경우
	$sno					= $_POST['sno'];
	$comment			= str_replace("'", "''", $_POST['comment']);

	BeginTrans();

	$flagResult = "SUCCESS";

	try {
		$sql  = "INSERT INTO tblstorestory_comment ( ";
		$sql .= "   sno, ";
		$sql .= "   mem_id, ";
		$sql .= "   comment, ";
		$sql .= "   regdt ";
		$sql .= ") VALUES ( ";
		$sql .= "   '{$sno}', ";
		$sql .= "   '" . $_ShopInfo->getMemid() ."', ";
		$sql .= "   '{$comment}', ";
		$sql .= "   '".date("YmdHis")."' ";
		$sql .= ") ";

		$result = pmysql_query($sql, get_db_conn());
		if ( empty($result) ) {
			throw new Exception('Insert Fail');
		} else {
			/****************댓글 작성 포인트 지급****************************/
			// 작성한 댓글수를 체크한다.
			list($cm_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cm_cnt from tblstorestory_comment WHERE sno='".$sno."' AND mem_id = '".$_ShopInfo->getMemid()."' "));

			// 오늘 댓글 작성시 적립받은 갯수를 체크한다.
			list($cp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cp_cnt from tblpoint_act WHERE rel_flag='@comment_in_point' and regdt >= '".date("Ymd")."000000' AND regdt <= '".date("Ymd")."999999' AND mem_id = '".$_ShopInfo->getMemid()."' "));
			if ($cp_cnt < $ap_comment_cnt && $cm_cnt == 1) { // 댓글 작성시 적립받은 갯수가 설정수보다 작으면
				insert_point_act($_ShopInfo->getMemid(), $ap_comment_point, '댓글 작성 포인트', '@comment_in_point', "admin_".date("YmdHis"), date("YmdHis"), 0);
			}
		}
	} catch (Exception $e) {
		$flagResult = "FAIL";
		RollbackTrans();
	}
	CommitTrans();
} else if ($mode == 'delete') {
	
	$cno					= $_POST['cno'];

	BeginTrans();

	$flagResult = "SUCCESS";

	try {
			// 작성한 댓글수를 체크한다.
			list($cm_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cm_cnt from tblstorestory_comment WHERE sno IN (select sno from tblstorestory_comment where cno='".$cno."') AND mem_id = '".$_ShopInfo->getMemid()."' "));

			$sql  = "DELETE FROM tblstorestory_comment WHERE cno = {$cno} ";
			$result = pmysql_query($sql, get_db_conn());
			if ( empty($result) ) {
				throw new Exception('Delete Fail');
			} else {
				/****************댓글 작성 포인트 지급 환원****************************/
				if ($cm_cnt == 1) {
					insert_point_act($_ShopInfo->getMemid(), $ap_comment_point * -1, '댓글 삭제 포인트 환원', '@comment_del_point', "admin_".date("YmdHis"), "sc_".$cno."_".date("YmdHis"), 0);
				}
			}

	} catch (Exception $e) {
		$flagResult = "FAIL";
		RollbackTrans();
	}
	CommitTrans();

}

echo $flagResult;
?>
