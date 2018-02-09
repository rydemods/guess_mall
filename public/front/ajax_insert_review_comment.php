<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보
$ap_comment_cnt		= $pointSet['comment']['count'];	// 댓글 지급 횟수제한
$ap_comment_point	= $pointSet['comment']['point'];	// 댓글 지급 포인트

$return       = $_POST['return'];
$pnum       = $_POST['pnum'];
$content    = str_replace("'", "''", trim($_POST['review_comment']));

BeginTrans();

$flagResult = "SUCCESS";

try {
    $sql  = "INSERT INTO tblproductreview_comment ( ";
    $sql .= "   id, ";
    $sql .= "   name, ";
    $sql .= "   content, ";
    $sql .= "   regdt, ";
    $sql .= "   pnum ";
    $sql .= ") VALUES ( ";
    $sql .= "   '" . $_ShopInfo->getMemid() ."', ";
    $sql .= "   '" . $_ShopInfo->memname . "', ";
    $sql .= "   '{$content}', ";
    $sql .= "   '".date("YmdHis")."', ";
    $sql .= "   {$pnum} ";
	if ($return == 'OK') {
		$sql .= ") RETURNING no";
	} else {
		$sql .= ") ";
	}
	
    $result = pmysql_query($sql, get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Insert Fail');
    }else{
    	
    	/****************댓글 작성 포인트 지급****************************/
    	// 작성한 댓글수를 체크한다.
    	list($cm_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cm_cnt from tblproductreview_comment WHERE pnum='".$pnum."' AND id = '".$_ShopInfo->getMemid()."' "));
    	
    	// 오늘 댓글 작성시 적립받은 갯수를 체크한다.
    	list($cp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cp_cnt from tblpoint_act WHERE rel_flag='@comment_in_point' and regdt >= '".date("Ymd")."000000' AND regdt <= '".date("Ymd")."999999' AND mem_id = '".$_ShopInfo->getMemid()."' "));
    	if ($cp_cnt < $ap_comment_cnt && $cm_cnt == 1) { // 댓글 작성시 적립받은 갯수가 설정수보다 작으면
    		insert_point_act($_ShopInfo->getMemid(), $ap_comment_point, '댓글 작성 포인트', '@comment_in_point', "admin_".date("YmdHis"), date("YmdHis"), 0);
    	}
    }
	
	if ($return == 'OK') {
		$row2 = pmysql_fetch_array($result);
		$rno = $row2[0];
		$flagResult = "SUCCESS|".$rno;
	}

} catch (Exception $e) {
	if ($return == 'OK') {
		$flagResult = "FAIL|0";
	} else {
		$flagResult = "FAIL";
	}
    RollbackTrans();
}
CommitTrans();

echo $flagResult;
?>
