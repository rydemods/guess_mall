<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr = array();

$code = 0;
$message = "success";

$company_code   = $_POST['company_code'];
$business_part  = $_POST['business_part'];
$brand_code  = $_POST['brand_code'];
$page_num       = $_POST["page_num"]?$_POST["page_num"]:1;
$list_num       = $_POST["list_num"]?$_POST["list_num"]:10;

if ( empty($company_code) ) {
    $code       = 1;
    $message    = "회사코드가 없습니다.";
} elseif ( empty($business_part) ) {
    $code       = 1;
    $message    = "사업부코드가 없습니다.";
} else {

    $sql  = "SELECT a.* ";
    $sql .= "FROM tblboard a left join tblproduct b ON a.pridx = b.pridx ";
    //$sql .= "WHERE a.board = 'qna' and ( b.company_code = '{$company_code}' AND b.business_part = '{$business_part}' ) ";
	$sql .= "WHERE a.board = 'qna' and b.brandcd='".$brand_code."' ";
    $sql .= "ORDER BY a.writetime desc ";

    $paging = new New_Templet_paging($sql,10,$list_num);    // 페이징

    $t_count = $paging->t_count;                // 전체 건수
    $t_page_count = floor($paging->pagecount);  // 전체 페이지 수   
    $paging->gotopage = $page_num;              // 페이지 지정

    $sql = $paging->getSql($sql);   

    $resultArr["cnt"] = $t_count;
    $resultArr["total_page_cnt"] = $t_page_count;
    $resultArr["page_num"] = $page_num;
    $resultArr["list_num"] = $list_num;
    $resultArr["list"] = array();

    $result = pmysql_query($sql);

    $cnt = 0;
    while ( $row = pmysql_fetch_object($result) ) {

        list($goods_number,$style,$color,$size) = pmysql_fetch("SELECT prodcode||colorcode||standard AS goods_number, prodcode, colorcode, sizecd FROM tblproduct WHERE pridx = '".$row->pridx."'");            // 품목코드
        list($qnaCount)     = pmysql_fetch("SELECT count(num) FROM tblboardcomment WHERE board = 'qna' and parent = '".$row->num."'");  // 답변여부

/*
        if($qnaCount > 0){
            $a_status   = "답변완료";
        } else {
            $a_status   = "답변 전";
        }

        if($row->is_secret == "0") $is_secret   = "공개";
        if($row->is_secret == "1") $is_secret   = "비공개";
*/

        $qna_reply_sql  = "SELECT name, writetime, comment ";
        $qna_reply_sql .= "FROM tblboardcomment ";
        $qna_reply_sql .= "WHERE board = 'qna' and parent = '".$row->num."' ";
        $qna_reply_sql .= "order by num desc";

        $qna_reply_res = pmysql_query($qna_reply_sql,get_db_conn());

        $reply_date     = "";
        $arrQnaReply    = array();
        $reply_cnt      = 0;
        while( $qna_reply_row = pmysql_fetch_object( $qna_reply_res ) ) {
            if ( $reply_cnt == 0 ) {
                $reply_date = date( "Y-m-d", $qna_reply_row->writetime);
            }

            array_push($arrQnaReply, array(
                    "name"      => $qna_reply_row->name, 
                    "date"      => date( "Y-m-d", $qna_reply_row->writetime),
                    "content"   => nl2br(trim($qna_reply_row->comment)),
            ));

            $reply_cnt++;
        } // qna_reply_row while
        pmysql_free_result( $qna_reply_res );

        $resultArr["list"][$cnt] = array(
            "id"            => $row->mem_id,
            "name"          => $row->name,
            "subject"       => $row->title,
            "content"       => $row->content,
            "reg_date"      => date("Y-m-d", $row->writetime),
            "reply_date"    => $reply_date,
            "goods_number"  => $goods_number,
            "style"         => $style,
            "color"         => $color,
            "size"          => $size,
            "reply_list"    => $arrQnaReply,
        );

        $cnt++;
    }
}

$resultTotArr["result"]    = $resultArr;
$resultTotArr["code"]      = $code;
$resultTotArr["message"]   = $message;

echo json_encode($resultTotArr);
?>
