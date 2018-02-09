<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/config.ap_point.php");

$hott_code = $_POST['hott_code'];
$section = $_POST['section'];
$like_type = $_POST['like_type'];

$like_id = $_ShopInfo->getMemid();
$regdt = date("YmdHis");
$like_point  = $pointSet['like']['point'];

$section_arr = array("instagram", "product", "storestory", "magazine", "lookbook", "forum_list_mypage", "forum_list");  // section 유형정의 2016-10-25
if(in_array($section, $section_arr)) $chk_section = 1;
else $chk_section = 0;

if($like_type == "unlike") {

    if($chk_section) {

        $sql = "Delete From tblhott_like Where like_id = '".$like_id."' and section = '".$section."' and hott_code = '".$hott_code."' ";
        pmysql_query($sql, get_db_conn());

        $msg = "좋아요 취소되었습니다.";
        $div = '<div class="user_like_none"><a href="javascript:SaveLike(\''.$hott_code.'\', \'like\')">좋아요</a></div>';
        $div2 = '<p class="user_like_none"><a href="javascript:product_like(\''.$hott_code.'\', \'like\')">좋아요</a></p>'; // 장바구니용
        //insert_point_act($_ShopInfo->getMemid(), -$like_point, "좋아요 취소 포인트", "like_minus_point", $regdt, 0);
        insert_point_act($_ShopInfo->getMemid(), -$like_point, "좋아요 취소 포인트", "like_minus_point_".$section, $regdt, $hott_code);
    }
    
} elseif($like_type == "like") {

    // 자신이 좋아요 했는지 카운트..
    $sql = "Select count(*) as cnt from tblhott_like Where like_id = '".$like_id."' and section = '".$section."' and hott_code = '".$hott_code."' ";
    list($cnt) = pmysql_fetch($sql, get_db_conn());


    if($cnt > 0) {
        $msg = "이미 좋아요 하셨습니다.";

    } else {

        if($chk_section) {

            $sql = "insert into tblhott_like 
                    (like_id, section, hott_code, regdt) 
                    Values 
                    ('".$like_id."', '".$section."', '".$hott_code."', '".$regdt."')
                    ";
            pmysql_query($sql, get_db_conn());

            $msg = "좋아요 등록되었습니다.";
            $div = '<div class="user_like"><a href="javascript:SaveLike(\''.$hott_code.'\', \'unlike\')">좋아요</a></div>';
            $div2 = '<p class="user_like"><a href="javascript:product_like(\''.$hott_code.'\', \'unlike\')">좋아요</a></p>';    // 장바구니용
            //insert_point_act($_ShopInfo->getMemid(), $like_point, "좋아요 포인트", "like_plus_point", $date, 0);
            insert_point_act($_ShopInfo->getMemid(), $like_point, "좋아요 포인트", "like_plus_point_".$section, $regdt, $hott_code);
        }
    }
}

// 해당 컨텐츠 좋아요 카운트 수..
$sql = "Select count(*) as cnt from tblhott_like Where section = '".$section."' and hott_code = '".$hott_code."' ";
list($cnt_all) = pmysql_fetch($sql, get_db_conn());

$sql = "Select count(*) as cnt from tblhott_like Where like_id = '".$like_id."' and section = '".$section."' and hott_code = '".$hott_code."' ";
list($cnt_my) = pmysql_fetch($sql, get_db_conn());

$response = array();
$response[] = array(
        'cnt_all' => $cnt_all, 
        'cnt_my' => $cnt_my, 
        'msg' => $msg, 
        'div' => $div, 
        'div2' => $div2 
);

echo json_encode($response);
?>