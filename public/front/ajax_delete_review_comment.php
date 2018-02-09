<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");
include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보
$ap_comment_cnt		= $pointSet['comment']['count'];	// 댓글 지급 횟수제한
$ap_comment_point	= $pointSet['comment']['point'];	// 댓글 지급 포인트

$review_comment_num = $_GET['review_comment_num'];
$review_num = $_GET['review_num'];

BeginTrans();

$flagResult = "SUCCESS";

try {
	// 작성한 댓글수를 체크한다.
	list($cm_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cm_cnt from tblproductreview_comment WHERE pnum IN (select pnum from tblproductreview_comment where pnum='".$review_num."') AND id = '".$_ShopInfo->getMemid()."' "));
    $sql  = "DELETE FROM tblproductreview_comment ";
    $sql .= "WHERE no = " . $review_comment_num . " ";
    $result = pmysql_query($sql, get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Delete Fail');
    }else{
    	/****************댓글 작성 포인트 지급 환원****************************/
    	if ($cm_cnt == "1") {
    		insert_point_act($_ShopInfo->getMemid(), $ap_comment_point * -1, '댓글 삭제 포인트 환원', '@comment_del_point', "admin_".date("YmdHis"), "rc_".$review_comment_num."_".date("YmdHis"), 0);
    	}
    }
} catch (Exception $e) {
    $flagResult = "FAIL";
    RollbackTrans();
}
CommitTrans();

echo $flagResult;
?>
