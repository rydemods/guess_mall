<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.ap_point.php");

$review_num = $_GET['review_num'];

BeginTrans();

$flagResult = "SUCCESS";

try {
    $sql  = "SELECT * FROM tblproductreview WHERE num = {$review_num}";
    $row  = pmysql_fetch_object(pmysql_query($sql));
	
    // 업로드된 이미지들 삭제하는 부분
    $imagepath  = $Dir.DataDir."shopimages/review/";

    if ( !empty($row->upfile) && file_exists($imagepath.$row->upfile) ) {
        unlink($imagepath.$row->upfile);
    }
    if ( !empty($row->upfile2) && file_exists($imagepath.$row->upfile2) ) {
        unlink($imagepath.$row->upfile2);
    }
    if ( !empty($row->upfile3) && file_exists($imagepath.$row->upfile3) ) {
        unlink($imagepath.$row->upfile3);
    }
    if ( !empty($row->upfile4) && file_exists($imagepath.$row->upfile4) ) {
        unlink($imagepath.$row->upfile4);
    }

    $sql  = "DELETE FROM tblproductreview WHERE num = {$review_num}";
    $result = pmysql_query($sql, get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Delete Fail');
    }
    $comment_sql  = "DELETE FROM tblproductreview_comment WHERE pnum = {$review_num}";
    $comment_result = pmysql_query($comment_sql, get_db_conn());
    if ( empty($comment_result) ) {
    	throw new Exception('Delete Fail');
    }
    $sql    = "UPDATE tblproduct SET review_cnt = review_cnt - 1 WHERE productcode ='". $row->productcode ."'";
    $result = pmysql_query($sql, get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Update Fail');
    }

	//포인트 반환
	list($del_point, $rel_mem_id, $body)=pmysql_fetch("select point, rel_mem_id, body from tblpoint_act where rel_flag='@review' and point>0 and rel_job='".$review_num."' and mem_id='".$_ShopInfo->getMemid()."'");
	//insert_point_act($_ShopInfo->getMemid(), "-".$del_point, "리뷰 삭제 포인트 반환", "@review", $rel_mem_id, $review_num);

	//3회이상 포인트반환 
	list($prodel_point)=pmysql_fetch("select point from tblpoint_act where rel_flag='@review' and point>0 and rel_job='".$review_num."_proreview_point"."' and mem_id='".$_ShopInfo->getMemid()."'");
	//insert_point_act($_ShopInfo->getMemid(), "-".$del_point, "리뷰 삭제 포인트 반환", "@review", $rel_mem_id, $review_num);

	
	/*
    // 해당 리뷰 타입에 따라서 포인트를 차감해 준다.
    if ( $row->type == "1" ) {
        // 포토리뷰
        $title = "포토리뷰 작성보상 취소";
        //$point = "-".$pointSet['photo']['point'];
    } else {
        // 텍스트리뷰
        $title = "텍스트리뷰 작성보상 취소";
        //$point = "-".$pointSet['textr']['point'];
    }*/
	$title = $body." 취소";
	$point = "-".$del_point;
	$prpoint = "-".$prodel_point;

    // 해당 리뷰 작성해서 받은 포인트가 있는지부터 체크
    $sql  = "SELECT COUNT(*) FROM tblpoint_act ";
    $sql .= "WHERE mem_id = '" . $_ShopInfo->getMemid() . "' AND rel_job = '" . $review_num . "'";
    list($row_count) = pmysql_fetch($sql);

    if ( $row_count == 1 ) {
        // 해당 내역이 있으면 포인트 차감
        $result = insert_point_act($_ShopInfo->getMemid(), $point, "리뷰 삭제 포인트 반환", "@review_del", "admin_".date("YmdHis"), $review_num);
    }

	// 해당 리뷰 작성해서 받은 포인트가 있는지부터 체크
    $sql  = "SELECT COUNT(*) FROM tblpoint_act ";
    $sql .= "WHERE mem_id = '" . $_ShopInfo->getMemid() . "' AND rel_job = '" . $review_num."_proreview_point" . "'";
    list($row_count) = pmysql_fetch($sql);

    if ( $row_count == 1 ) {
        // 해당 내역이 있으면 포인트 차감
        $result = insert_point_act($_ShopInfo->getMemid(), $prpoint, "리뷰 삭제 포인트 반환(3번째 이내 상품평 작성)", "@review_del", "admin_".date("YmdHis"), $review_num."_proreview_point");
    }

} catch (Exception $e) {
    $flagResult = "FAIL";
    RollbackTrans();
}
CommitTrans();

echo $flagResult;
?>
