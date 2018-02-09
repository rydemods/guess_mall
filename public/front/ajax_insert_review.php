<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.point.php");

$v_up_filename      = $_POST['v_up_filename'];
$productcode        = $_POST['productcode'];
$ordercode          = $_POST['ordercode'];
$productorder_idx   = $_POST['productorder_idx'];
$review_num         = $_POST['review_num'];
$review_vote        = $_POST['review_vote'];

echo $v_up_filename."<br>".$productcode."<br>".$ordercode."<br>".$review_num."<br>".$review_vote;

$review_title       = str_replace("'", "''", trim($_POST['review_title']));
$review_content     = str_replace("'", "''", $_POST['review_content']);
$review_type        = "0";  // 0 : 텍스트, 1 : 포토

$imagepath          = $Dir.DataDir."shopimages/review/";
$banner_file        = new FILE($imagepath);
$banner_img         = $banner_file->upFiles();

$file_exists_count  = 0;
$chkExtArr          = array('jpg','gif','jpeg');
$file_size_mb       = 3;
$file_size_max      = $file_size_mb * 1024 * 1024;           // 파일 하나당 최대 용량

$loop_idx           = 0;
$b_check_upload_img = true;
$fail_msg           = "";

$arrVFile           = array();
$arrRFile           = array();

foreach ( $banner_img['up_filename'] as $arrBannerImg ) {
    if ( $arrBannerImg['error'] === false ) {
        // 파일이 존재

        $file_ext_org = pathinfo($arrBannerImg['r_file'], PATHINFO_EXTENSION);
        $file_ext = strtolower($file_ext_org);
        if ( !in_array($file_ext, $chkExtArr) ) {
            // 잘못된 확장자
            $b_check_upload_img = false;
            $fail_msg = $arrBannerImg['r_file'] . " 파일의 확장자(" . $file_ext_org . ")는 업로드 불가합니다.";
            break;
        }

        if ( $arrBannerImg['size'] > $file_size_max ) {
            // 용량이 초과한 경우
            $b_check_upload_img = false;
            $fail_msg = $arrBannerImg['r_file'] . " 파일 용량이 " . $file_size_mb . "MB를 초과할 수 없습니다.";
            break;
        }


        $arrVFile[$loop_idx] = $arrBannerImg['v_file'];
        $arrRFile[$loop_idx] = $arrBannerImg['r_file'];

        $file_exists_count++;
    }

    $loop_idx++;
}

if ( $b_check_upload_img === false ) {
    // 이미지 확장자가 다르거나 용량이 기준을 초과한 경우
    echo "FAIL||{$fail_msg}";
    exit;
}

// review번호가 넘어온 경우는 기존 업로드된 파일들을 셋팅한다.
if ( $review_num > 0 ) {
    $sql  = "SELECT * FROM tblproductreview WHERE num = {$review_num} ";
    $row  = pmysql_fetch_object(pmysql_query($sql));

    for ( $i = 0; $i < $loop_idx; $i++ ) {
        if($banner_img["up_filename"][$i]["v_file"] || $v_up_filename[$i] == ''){
            if ( $i == 0 ) {
                $up_rFile = $row->upfile;
            } else {
                $fieldName = "upfile" . ($i+1);
                $up_rFile = $row->$fieldName;
            }

            if ( $up_rFile !="" || $v_up_filename[$i] =='' ) {
                $banner_file->removeFile($up_rFile);
                $up_vfilename    = "";   // 실제 업로드 되는 파일명
                $up_rfilename   = "";   // 원본 파일명
            }
            if($banner_img["up_filename"][$i]["v_file"]) $up_vfilename=$banner_img["up_filename"][$i]["v_file"];
            if($banner_img["up_filename"][$i]["r_file"]) $up_rfilename=$banner_img["up_filename"][$i]["r_file"];

            $arrVFile[$i] = $up_vfilename;
            $arrRFile[$i] = $up_rfilename;
        }

        if ( !isset($arrVFile[$i]) ) { 
            if ( $i == 0 ) { 
                $arrVFile[$i] = $row->upfile; 
            } else {
                $varName = "upfile" . ($i+1);
                $arrVFile[$i] = $row->{$varName}; 
            }
        }
        if ( !isset($arrRFile[$i]) ) { 
            if ( $i == 0 ) {
                $arrRFile[$i] = $row->up_rfile;     
            } else {
                $varName = "up_rfile" . ($i+1);
                $arrRFile[$i] = $row->{$varName};     
            }

            if ( !empty($arrRFile[$i]) ) {
                $file_exists_count++;
            }
        }
    }
}

// 업로드한 파일이 하나 이상이면 '포토리뷰'
if ( $file_exists_count >= 1 ) {
    $review_type    = "1";
}

BeginTrans();

$flagResult = "SUCCESS";

try {

    if ( $review_num != "0" ) {
        $sql  = "UPDATE tblproductreview SET ";
        $sql .= "marks = '{$review_vote}', ";
        $sql .= "subject = '{$review_title}', ";
        $sql .= "content = '{$review_content}', ";
        $sql .= "type  = '{$review_type}', ";
        $sql .= "upfile = '{$arrVFile[0]}', ";
        $sql .= "up_rfile = '{$arrRFile[0]}', ";
        $sql .= "upfile2 = '{$arrVFile[1]}', ";
        $sql .= "up_rfile2 = '{$arrRFile[1]}', ";
        $sql .= "upfile3 = '{$arrVFile[2]}', ";
        $sql .= "up_rfile3 = '{$arrRFile[2]}', ";
        $sql .= "upfile4 = '{$arrVFile[3]}', ";
        $sql .= "up_rfile4 = '{$arrRFile[3]}' ";
        $sql .= "WHERE num = {$review_num} ";
        $result = pmysql_query($sql, get_db_conn());
        if ( empty($result) ) {
            throw new Exception('Insert Fail');
        }

    } else {

        $sql  = "INSERT INTO tblproductreview ( ";
        $sql .= "   productcode, ";
        if ($ordercode) $sql .= "   ordercode, ";
        if ($productorder_idx) $sql .= "   productorder_idx, ";
        $sql .= "   id, ";
        $sql .= "   name, ";
        $sql .= "   marks, ";
        $sql .= "   date, ";
        $sql .= "   subject, ";
        $sql .= "   content, ";
        $sql .= "   type, ";
        $sql .= "   upfile, ";
        $sql .= "   up_rfile, ";
        $sql .= "   upfile2, ";
        $sql .= "   up_rfile2, ";
        $sql .= "   upfile3, ";
        $sql .= "   up_rfile3, ";
        $sql .= "   upfile4, ";
        $sql .= "   up_rfile4 ";
        $sql .= ") VALUES ( ";
        $sql .= "   '{$productcode}', ";
        if ($ordercode) $sql .= "   '{$ordercode}', ";
        if ($productorder_idx) $sql .= "   {$productorder_idx}, ";
        $sql .= "   '" . $_ShopInfo->getMemid() ."', ";
        $sql .= "   '" . $_ShopInfo->memname . "', ";
        $sql .= "   '{$review_vote}', ";
        $sql .= "   '".date("YmdHis")."', ";
        $sql .= "   '{$review_title}', ";
        $sql .= "   '{$review_content}', ";
        $sql .= "   '{$review_type}', ";
        $sql .= "   '{$arrVFile[0]}', ";
        $sql .= "   '{$arrRFile[0]}', ";
        $sql .= "   '{$arrVFile[1]}', ";
        $sql .= "   '{$arrRFile[1]}', ";
        $sql .= "   '{$arrVFile[2]}', ";
        $sql .= "   '{$arrRFile[2]}', ";
        $sql .= "   '{$arrVFile[3]}', ";
        $sql .= "   '{$arrRFile[3]}' ";
        $sql .= ") ";

        $result = pmysql_query($sql, get_db_conn());
        if ( empty($result) ) {
            throw new Exception('Insert Fail');
        }

        $sql  = "UPDATE tblproduct ";
        $sql .= "SET review_cnt = review_cnt + 1 ";
        $sql .= "WHERE productcode ='".$productcode."'";
        $result = pmysql_query($sql, get_db_conn());
        if ( empty($result) ) {
            throw new Exception('Update Fail');
        }

        // =======================================================
        // mypage_orderlist.ajax.php 코드를 그대로 옮김
        // =======================================================
		if ($ordercode) { // 주문코드가 있을 경우에만 처리 (2016-03-02 - 김재수) // 리뷰쓰기 권한에 있는사람은 주문코드가 없으수 있어서...
			$idx = $productorder_idx;

			//실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
			if ($_ShopInfo->getMemname()) {
				$reg_name	= $_ShopInfo->getMemname();
			} else {
				list($reg_name)=pmysql_fetch_array(pmysql_query("select sender_name from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));
			}
			$exe_id		= $_ShopInfo->getMemid()."|".$reg_name."|user";	// 실행자 아이디|이름|타입

            // 아직 구매확정이 안된 경우에만 아래 내용을 실행
            $sql  = "SELECT count(*) ";
            $sql .= "FROM tblorderproduct ";
            $sql .= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' AND receive_ok = '1' AND deli_gbn='F' AND order_conf_date <> '' ";
            list($row_cnt) = pmysql_fetch($sql);

            if ( $row_cnt == 0 ) {
                list($deli_reserve)=pmysql_fetch_array(pmysql_query("select reserve from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx IN ('".str_replace("|", "','", $idx)."')"));

                $sql = "UPDATE tblorderproduct SET receive_ok = '1' ,deli_gbn='F', order_conf_date = '" . date('YmdHis') . "' ";
                $sql.= "WHERE ordercode='{$ordercode}' AND idx='{$idx}' ";
                $sql.= "AND op_step < 40 ";

                pmysql_query($sql,get_db_conn());
                if( !pmysql_error() ){

                    // 신규상태 변경 추가 - (2016.02.18 - 김재수 추가)
                    orderProductStepUpdate($exe_id, $ordercode, $idx, '4'); // 배송완료
                                
                    //적립 예정 적립금을 지급한다.
                    if ($deli_reserve != 0) insert_point_act($_ShopInfo->getMemid(), $deli_reserve, "주문 ".$ordercode." 배송완료(".count($idx)."건)에 의한 적립금 지급", '','',"admin-".uniqid(''), $return_point_term);

                    //주문중 배송완료, 취소완료상태가 아닌경우
                    list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $idx)."') AND (op_step != '4' AND op_step != '44')"));

                    if ($op_idx_cnt == 0) {
                        $sql = "UPDATE tblorderinfo SET receive_ok = '1', deli_gbn = 'F', order_conf_date = '" . date('YmdHis') . "' ";
                        $sql.= "WHERE ordercode='{$ordercode}' ";
                        pmysql_query($sql,get_db_conn());
                    }
                    $msg	= "구매확정 되었습니다.";
                    $msgType = "1";
                } else {
                    $msg = "구매확정 실패. 관리자에게 문의해주세요.";
                    $msgType = "0";

                    throw new Exception('구매확정 실패');
                }
            }
		}

        // ================================================================================================
        // 포인트 지급은 실제로 해당 상품을 구입한 경우에만 지급
        // ================================================================================================
        if ( !empty($ordercode) && !empty($productorder_idx) ) {
            // 현재 상품에 대한 리뷰가 한개인 경우에만 포인트 지급
            /*
            $sql  = "SELECT num FROM tblproductreview ";
            $sql .= "WHERE productcode = '{$productcode}' AND id = '" . $_ShopInfo->getMemid() . "' ";
            */
    
            // 현재 상품을 구입하고 리뷰를 처음 작성한 경우에만 포인트 지급
            $sql  = "SELECT tpr.num ";
            $sql .= "FROM ";
            $sql .= "   tblproductreview tpr LEFT JOIN tblorderproduct top ON tpr.productcode = top.productcode AND tpr.productorder_idx = top.idx ";
            $sql .= "WHERE ";
            $sql .= "   tpr.productcode = '{$productcode}' AND tpr.id = '" . $_ShopInfo->getMemid() . "' AND top.idx='{$productorder_idx}' ";

            $row_count = pmysql_num_rows(pmysql_query($sql));
            list($review_num) = pmysql_fetch($sql);

            if ( $row_count == 1 ) {
                if ( $review_type == "1" ) {
                    // 포토리뷰
                    $title = "포토리뷰 작성보상";
                    $point = $pointSet['photo']['point'];
                } else {
                    // 텍스트리뷰
                    $title = "텍스트리뷰 작성보상";
                    $point = $pointSet['textr']['point'];
                }

                $result = insert_point_act($_ShopInfo->getMemid(), $point, $title, "@event", "admin", $review_num);
            }
        }
    }

} catch (Exception $e) {
    $flagResult = "FAIL";
    RollbackTrans();
}
CommitTrans();

echo $flagResult;
?>
