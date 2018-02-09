<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");
include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보
$ap_comment_cnt		= $pointSet['comment']['count'];	// 댓글 지급 횟수제한
$ap_comment_point	= $pointSet['comment']['point'];	// 댓글 지급 포인트

$comment_num = $_GET['comment_num'];
$mnum = $_GET['mnum'];

BeginTrans();

$flagResult = "SUCCESS";

try {
	// 작성한 댓글수를 체크한다.
	list($cm_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cm_cnt from tblmagazine_comment WHERE mnum IN (select mnum from tblmagazine_comment where mnum='".$mnum."') AND id = '".$_ShopInfo->getMemid()."' "));
    $sql  = "DELETE FROM tblmagazine_comment ";
    $sql .= "WHERE no = " . $comment_num . " ";
    $result = pmysql_query($sql, get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Delete Fail');
    }else{
    	/****************댓글 작성 포인트 지급 환원****************************/
    	if ($cm_cnt == "1") {
    		insert_point_act($_ShopInfo->getMemid(), $ap_comment_point * -1, '댓글 삭제 포인트 환원', '@comment_del_m_point', "admin_".date("YmdHis"), "mc_".$cno."_".date("YmdHis"), 0);
    	}
    }
} catch (Exception $e) {
    $flagResult = "FAIL";
    RollbackTrans();
}
CommitTrans();

echo $flagResult;
?>
