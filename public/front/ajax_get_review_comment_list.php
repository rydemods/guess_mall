<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");

$cate_code      = $_GET['cate_code'];
$listnum        = $_GET['listnum'];
$page           = $_GET['commentgotopage'];
$review_num     = $_GET['review_num'];

// 댓글 리스트 조회
$sql  = "SELECT * FROM tblproductreview_comment ";
$sql .= "WHERE pnum = " . $review_num . " ";
$sql .= "ORDER BY no desc ";

$paging = new amg_Paging2($sql, 10, $listnum, 'GoPageAjax2', $review_num); 
//$gotopage = $paging->gotopage;
$gotopage = $page;

$sql = $paging->getSql($sql); 
$htmlResult = '';
$result = pmysql_query($sql);
while ($row = pmysql_fetch_array($result)) {
    $reg_date = $row['regdt'];
//    $reg_date = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2) . " " . substr($reg_date, 8, 2) . ":" . substr($reg_date, 10, 2) . ":" . substr($reg_date, 12, 2);
    $reg_date = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2);

    $htmlResult .= '<div class="answer">
    							<span class="name">' . setIDEncryp($row['id']) . ' (' . substr($row['regdt'],0,4)."-".substr($row['regdt'],4,2)."-".substr($row['regdt'],6,2) . ')</span>';
    if ( $row['id'] == $_ShopInfo->getMemid() ) {
    	$htmlResult .= '<div class="btn_delete"> <a class="btn-delete" href="javascript:;" onClick="javascript:delete_review_comment(this);" ids="' . $row['no'] . '" ids2="' . $row['pnum'] . '">삭제</a></div>';
    }
    $htmlResult .= '<p>' . $row['content'] . '</p>';
    $htmlResult .= '<div class="btn-feeling mt-5">
    							<a class="btn-good-feeling" href="javascript:select_feeling(\''.$row['no'].'\' ,\'product_review_comment\',\'good\',\''.$_ShopInfo->getMemid().'\');" id="feeling_good_comment_'.$row['no'].'">'.totalFeeling($row['no'], 'product_review_comment', 'good').'</a>
    							<a class="btn-good-feeling" href="javascript:select_feeling(\''.$row['no'].'\' ,\'product_review_comment\',\'bad\',\''.$_ShopInfo->getMemid().'\');" id="feeling_good_comment_'.$row['no'].'">'.totalFeeling($row['no'], 'product_review_comment', 'bad').'</a>
    						</div>';
    $htmlResult .= '</div>';

}
$htmlResult .= '<div class="list-paginate-wrap mb-30">';
$htmlResult .= '<div class="list-paginate" id="paging_' .$review_num. '">';

$htmlResult .= "|||" . $paging->a_prev_page . $paging->print_page . $paging->a_next_page;
$htmlResult .= '</div>';
$htmlResult .= '</div>';

echo $htmlResult;
?>
