<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr = array();

$code = 0;
$message = "success";

$mode   = $_POST['mode'];   //QR: 상품문의답변, RC: 리뷰댓글, RL: 리뷰좋아요
$type   = $_POST['type'];   //R: 등록, M: 수정, D: 삭제
$idx    = $_POST['idx'];    //원본글 idx
$sub_idx    = $_POST['sub_idx'];    //수정글 idx(댓글 또는 답글의 idx)
$content =$_POST['content'];    //내용
$user_id =$_POST['user_id'];    //작성자고유번호
$user_name =$_POST['user_name'];    //작성자고유번호

$json_arrOrder=json_encode($_POST);
$Encrypt = new Encrypt();
$Encrypt->log( "[Synccommerce_jsonPostData] {$json_arrOrder}\r\n" );

if ( empty($idx) ) {
    $code       = 1;
    $message    = "원본글 idx가 없습니다.";
} elseif ( empty($sub_idx) && $type != 'R') {
    $code       = 1;
    $message    = "수정글 idx가 없습니다.";
} else {
    if($mode=='RC'){
        list($comment_no) = pmysql_fetch(pmysql_query("SELECT no FROM tblproductreview_comment WHERE pnum = '{$idx}' and id='{$user_id}'"));
        if($type=='R'){
            if(!$comment_no) {
                $comm_qry = "INSERT INTO tblproductreview_comment ( ";
                $comm_qry .= "   id,name,content,regdt,pnum ";
                $comm_qry .= ") VALUES ( ";
                $comm_qry .= "   '{$user_id}','{$user_name}','" . $content . "','" . date("YmdHis") . "', {$idx}";
                $comm_qry .= ") RETURNING no";
                $result = pmysql_query($comm_qry, get_db_conn());
                $row = pmysql_fetch_object( $result );

                $r_sub_idx=$row->no;    //등록고유번호
            }else{
                $code       = 1;
                $message    = "이미 등록된 매장입니다.";
            }
        }else if($type=='M'){
            if(!$comment_no) {
                $code       = 1;
                $message    = "해당글이 없습니다.";
            }else{
                $comm_qry="UPDATE tblproductreview_comment set content='".$content."' WHERE no={$sub_idx}";
                $result = pmysql_query($comm_qry, get_db_conn());
                $row = pmysql_fetch_object( $result );
            }
        }else if($type=='D'){
            if(!$comment_no) {
                $code       = 1;
                $message    = "해당글이 없습니다.";
            }else{
                $comm_qry="DELETE FROM tblproductreview_comment WHERE no={$sub_idx}";
                $result = pmysql_query($comm_qry, get_db_conn());
                $row = pmysql_fetch_object( $result );
            }
        }else{
            $code       = 1;
            $message    = "type 이 없습니다.";
        }
    }else if($mode=='RL'){
        list($comment_no) = pmysql_fetch(pmysql_query("SELECT no FROM tblproductreview_like WHERE pnum = '{$idx}' and id='{$user_id}'"));
        if($type=='R'){
            if(!$comment_no) {
                $like_qry = "INSERT INTO tblproductreview_like ( ";
                $like_qry .= "   id,name,regdt,pnum ";
                $like_qry .= ") VALUES ( ";
                $like_qry .= "   '{$user_id}','{$user_name}','" . date("YmdHis") . "', {$idx}";
                $like_qry .= ") RETURNING no";
                $result = pmysql_query($like_qry, get_db_conn());
                $row = pmysql_fetch_object( $result );

                $r_sub_idx=$row->no;    //등록고유번호
            }else{
                $code       = 1;
                $message    = "이미 등록된 매장입니다.";
            }
        }else if($type=='D'){
            if(!$comment_no) {
                $code       = 1;
                $message    = "해당글이 없습니다.";
            }else{
                $like_qry="DELETE FROM tblproductreview_like WHERE no={$sub_idx}";
                $result = pmysql_query($like_qry, get_db_conn());
                $row = pmysql_fetch_object( $result );
            }
        }else{
            $code       = 1;
            $message    = "type 이 없습니다.";
        }
    }else if($mode=='QR'){

        list($comment_no,$tcontent) = pmysql_fetch(pmysql_query("SELECT num,comment FROM tblboardcomment WHERE parent = '{$idx}'"));
        if($type=='R'){
            if(!$comment_no || strlen($tcontent) == 0){
                if(!$comment_no) {

                    $comm_qry = "INSERT INTO tblboardcomment ( ";
                    $comm_qry .= "   board,c_mem_id,name,comment,writetime,parent ";
                    $comm_qry .= ") VALUES ( ";
                    $comm_qry .= "   'qna','{$user_id}','{$user_name}','" . $content . "','" . time() . "', {$idx}";
                    $comm_qry .= ") RETURNING num";
                    $result = pmysql_query($comm_qry, get_db_conn());
                    $rrow = pmysql_fetch_object($result);

                    $r_sub_idx = $rrow->num;    //등록고유번호

                }else if(strlen($tcontent) == 0 && strlen($content) > 0){
                    $comm_qry="UPDATE tblboardcomment set comment='".$content."' WHERE num={$comment_no}";
                    $result = pmysql_query($comm_qry, get_db_conn());
                    $r_sub_idx = $comment_no;    //등록고유번호
                }

                if(strlen($content) > 0) {

                    $sql = "SELECT * FROM tblboard WHERE num = {$idx} ";
                    $result = pmysql_query($sql, get_db_conn());

                    $row = pmysql_fetch_object($result);
                    $total = pmysql_fetch_array(pmysql_query("SELECT COUNT(*) FROM tblboardcomment WHERE board='qna' AND parent='{$idx}'", get_db_conn()));
                    pmysql_query("UPDATE tblboard SET total_comment='{$total[0]}' WHERE num='{$idx}'", get_db_conn());

                    // ================================================================================================================
                    // 상품문의에 답변이 달린 경우, 메일 발송
                    // ================================================================================================================
                    SendQnaMail($_data->shopname, $_ShopInfo->getShopurl(), $_data->design_mail, $_data->info_email, 'qna', $idx);

                    //SMS 발송
                    sms_autosend('mem_qna', $row->mem_id, $idx, '');
                    //SMS 관리자 발송
                    sms_autosend('admin_qna', $row->mem_id, $idx, '');
                }
            }else{
                $code       = 1;
                $message    = "이미 등록된 답변입니다.";
            }
        }else if($type=='M'){
            if(!$comment_no) {
                $code       = 1;
                $message    = "해당글이 없습니다.";
            }else{
                $comm_qry="UPDATE tblboardcomment set comment='".$content."' WHERE num={$sub_idx}";
                $result = pmysql_query($comm_qry, get_db_conn());
                $row = pmysql_fetch_object( $result );
            }
        }else if($type=='D'){
            if(!$comment_no) {
                $code       = 1;
                $message    = "해당글이 없습니다.";
            }else{
                $like_qry="DELETE FROM tblboardcomment WHERE num={$sub_idx}";
                $result = pmysql_query($comm_qry, get_db_conn());
                $row = pmysql_fetch_object( $result );
            }
        }else{
            $code       = 1;
            $message    = "type 이 없습니다.";
        }
    }
}

$resultTotArr["result"]    = $resultArr;
$resultTotArr["code"]      = $code;
$resultTotArr["message"]   = $message;
$resultTotArr["r_sub_idx"]   = $r_sub_idx;

echo json_encode($resultTotArr);
?>
