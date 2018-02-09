<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging.php");

$listnum        = $_GET['listnum'];
$page           = $_GET['gotopage'];

$htmlResult     = "";

$sql  = "SELECT * FROM tblpromo ";
$sql .= "WHERE event_type <> '4' AND display_type in ('A', 'M') AND hidden = 1 AND ( current_date >= start_date AND current_date <= end_date ) ";

// ============================================================
// 프로모션 시작일이 오래된 순으로 정렬
// 시작일이 같은 경우 등록순으로 정렬
// by 최문성 ( 요청 : 조경복과장님 )
// date : 2016-05-04
// 2016-05-10 : 시작일 가장 최근것부터 나오게 수정 요청하여 재수정 (요청 : 조경복) by JeongHo, Jeong
// ============================================================
//$sql .= "ORDER BY start_date asc, idx::integer desc ";
$sql .= "ORDER BY start_date desc, idx::integer desc ";  

$paging = new New_Templet_mobile_paging($sql, 5, $listnum, 'GoPageAjax_running_promotion', true); 
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql); 
$review_result = pmysql_query($sql);

$htmlResult = '';
while ( $row = pmysql_fetch_object($review_result) ) { 
    $start_date = str_replace("-", ".", $row->start_date);
    $end_date = str_replace("-", ".", $row->end_date);
    $thumbImg = getProductImage($Dir.DataDir.'shopimages/timesale/', $row->thumb_img_m, true);

    $htmlResult .= '
        <li>
            <a href="promotion_detail.php?idx=' . $row->idx . '&event_type=' . $row->event_type . '">
                <figure>
                    <img src="' . $thumbImg . '" alt="">
                    <figcaption>
                        <span class="title">' . $row->title . '</span>
                        <span class="date">' . $start_date . '~' . $end_date . '</span>
                    </figcaption>
                </figure>
            </a>
        </li>';
    
} 
pmysql_free_result($result);

$htmlResult .= "|||" . $paging->a_prev_page . $paging->print_page . $paging->a_next_page . "|||" . number_format($paging->t_count);

echo $htmlResult;
?>
