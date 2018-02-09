<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");

$cate_code      = $_GET['cate_code'];
$listnum        = $_GET['listnum'];
$page           = $_GET['gotopage'];
$mnum     = $_GET['mnum'];

// 댓글 리스트 조회
$sql  = "SELECT * FROM tblmagazine_comment ";
$sql .= "WHERE mnum = " . $mnum . " ";
$sql .= "ORDER BY no desc ";

$paging = new amg_Paging($sql, 10, $listnum, 'GoPageAjax2', $mnum); 
$gotopage = $paging->gotopage;
$count = $paging->t_count;
$sql = $paging->getSql($sql); 

$htmlResult = '';
$result = pmysql_query($sql);
while ($row = pmysql_fetch_array($result)) {
    $reg_date = $row['regdt'];
    $reg_date = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2) . " " . substr($reg_date, 8, 2) . ":" . substr($reg_date, 10, 2);
//     $reg_date = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2);

    $htmlResult .= '<div class="view_reply">
    							<div class="inner">
    							<p class="info">' . $row['id'] . ' (' . $reg_date . ')</p>';
    $htmlResult .= '		<p class="con">' . $row['content'] . '</p>';
    $htmlResult .= '			<div class="clear">
    									<div class="btn-feeling">
    										<a class="btn-good-feeling" href="javascript:select_feeling(\''.$row['no'].'\' ,\'magazine_comment\',\'good\',\''.$_ShopInfo->getMemid().'\');" id="feeling_good_magazine_comment_'.$row['no'].'">'.totalFeeling($row['no'], 'magazine_comment', 'good').'</a>
    										<a class="btn-good-feeling" href="javascript:select_feeling(\''.$row['no'].'\' ,\'magazine_comment\',\'bad\',\''.$_ShopInfo->getMemid().'\');" id="feeling_bad_magazine_comment_'.$row['no'].'">'.totalFeeling($row['no'], 'magazine_comment', 'bad').'</a>
    									</div>';
    if ( $row['id'] == $_ShopInfo->getMemid() ) {
    	$htmlResult .= '<div class="btn-set"> <a href="javascript:;" onclick="delete_review_comment(this);" ids="' . $row['no'] . '" ids2="' . $row['mnum'] . '" class="btn-function">삭제</a></div>';
    }
    $htmlResult .= '			</div>
    							</div>
    						</div>';

}

$htmlResult .= "|||" . $paging->a_prev_page . $paging->print_page . $paging->a_next_page;
$htmlResult .= "|||" .$count ;

echo $htmlResult;
?>
