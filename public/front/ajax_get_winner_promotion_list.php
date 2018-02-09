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
$sql .= "WHERE display_type in ('A', 'M') and winner_list_content <> '' ";   // '전시상태'가 모두 or 모바일인 경우만
$sql .= "ORDER BY rdate desc, idx desc ";

$paging = new New_Templet_mobile_paging($sql, 5, $listnum, 'GoPageAjax_winner_list_promotion', true); 
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql); 
$review_result = pmysql_query($sql);

$htmlResult = '';
$cnt = 1;
while ( $row = pmysql_fetch_array($review_result) ) { 
    $num = $cnt + ( ( $page - 1 ) * $listnum );

    $htmlResult .= '
        <tr>
            <td>' . $num . '</td>
            <td class="subject"><a href="/m/promotion_detail.php?idx=' . $row['idx'] . '">' . $row['title'] . '</a></td>
            <td>' . $row['rdate'] . '</td>
        </tr>';

    $cnt++;
} 
pmysql_free_result($result);

$htmlResult .= "|||" . $paging->a_prev_page . $paging->print_page . $paging->a_next_page . "|||" . number_format($paging->t_count);

echo $htmlResult;
?>
