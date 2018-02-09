<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr = array();

$code = 0;
$message = "success";

$company_code   = $_POST['company_code'];
$business_part  = $_POST['business_part'];
$brand_code		= $_POST['brand_code'];
$type           = $_POST["type"]?$_POST["type"]:0;
$order          = $_POST["order"]?$_POST["order"]:0;
$page_num       = $_POST["page_num"]?$_POST["page_num"]:1;
$list_num       = $_POST["list_num"]?$_POST["list_num"]:10;

if ( empty($company_code) ) {
    $code       = 1;
    $message    = "회사코드가 없습니다.";
} elseif ( empty($business_part) ) {
    $code       = 1;
    $message    = "사업부코드가 없습니다.";
} else {
    $sql  = "SELECT a.subject, a.content, a.hit, ";
    $sql .= "(select count(*) from tblproductreview_comment WHERE pnum = a.num ) as comment_cnt, ";
    //$sql .= "a.marks, a.id, a.date, a.upfile, a.upfile2, a.upfile3, a.upfile4, a.upfile5, a.fit, a.color, ";
	$sql .= "a.marks, a.id, a.date, a.upfile, a.upfile2, a.upfile3, a.upfile4, a.upfile5, a.color, ";
    //$sql .= "(";
    //$sql .= "   SELECT code_name ";
    //$sql .= "   FROM on_common_code ";
    //$sql .= "   WHERE company_code = b.company_code AND code_division = '06' AND code1 = '1' AND code2 = b.color AND use_type = 'Y' ";
    //$sql .= ") prod_color, ";
	
	$sql .= "b.colorcode as prod_color, ";
    $sql .= "b.standard as prod_standard, ";
    $sql .= "b.prodcode||b.colorcode AS prod_code, ";
    $sql .= "b.prodcode AS prod_style ";
	$sql .= "FROM tblproductreview a LEFT JOIN tblproduct b ON a.productcode = b.productcode ";
    //$sql .= "WHERE b.company_code = '{$company_code}' AND b.business_part = '{$business_part}' ";
	$sql .= "WHERE b.brandcd='".$brand_code."' ";
    
    if ( $type == 1 ) {         // 텍스트 리뷰
        $sql .= "AND type = '0' ";
    } else if ( $type == 2 ) {  // 포토 리뷰
        $sql .= "AND type = '1' ";
    }

    if ( $order == 0 ) {        // 최신순
        $sql .= "ORDER BY date desc ";
    } else if ( $order == 1 ) { // 조회순
        $sql .= "ORDER BY hit desc ";
    } else if ( $order == 2 ) { // 댓글순
        $sql .= "ORDER BY comment_cnt desc ";
    }

    $paging = new New_Templet_paging($sql,10,$list_num);    // 페이징

    $t_count = $paging->t_count;                // 전체 건수
    $t_page_count = floor($paging->pagecount);  // 전체 페이지 수   
    $paging->gotopage = $page_num;              // 페이지 지정

    $sql = $paging->getSql($sql);   

    $resultArr["review_cnt"] = $t_count;
    $resultArr["total_page_cnt"] = $t_page_count;
    $resultArr["page_num"] = $page_num;
    $resultArr["list_num"] = $list_num;
    $resultArr["review_list"] = array();

    $result = pmysql_query($sql);

    $cnt = 0;
    while ( $row = pmysql_fetch_object($result) ) {
        // 몸에 맞는지 여부
        $fitStr = "";
        if ( isset($arrSejungReviewFit[$row->fit]) ) {
            $fitStr = $arrSejungReviewFit[$row->fit];
        }

        // 색상 여부
        $colorStr = "";
        if ( isset($arrSejungReviewColor[$row->color]) ) {
            $colorStr = $arrSejungReviewColor[$row->color];
        }

        // 업로드한 파일1
        $arrUpFile = array();

        for ( $i = 1; $i <= 5; $i++ ) {
            $arrUpFile[$i-1] = "";  // init

            if ( $i == 1 ) {
                if ( $row->upfile ) {
                    $arrUpFile[$i-1] = "http://" . $_SERVER['HTTP_HOST'] . "/data/shopimages/review/" . $row->upfile;
                }
            } else {
                $fieldName = "upfile" . $i;
                if ( $row->{$fieldName} ) {
                    $arrUpFile[$i-1] = "http://" . $_SERVER['HTTP_HOST'] . "/data/shopimages/review/" . $row->{$fieldName};
                }
            }
        }

        $resultArr["review_list"][$cnt] = array(
            "subject"       => $row->subject,
            "content"       => $row->content,
            "productcode"   => $row->prod_code, // 세정품목코드(스타일+색상)
            "hit"           => $row->hit,
            "comment_cnt"   => $row->comment_cnt,
            "marks"         => $row->marks,
            "id"            => $row->id,
            "reg_date"      => $row->date,
            "upfile1"       => $arrUpFile[0],
            "upfile2"       => $arrUpFile[1],
            "upfile3"       => $arrUpFile[2],
            "upfile4"       => $arrUpFile[3],
            "upfile5"       => $arrUpFile[4],
            "fit"           => $fitStr,
            "color"         => $colorStr,
            "prod_color"    => $row->prod_color,
            "prod_standard" => $row->prod_standard,
            "prod_style"    => $row->prod_style
        );

        $cnt++;
    }
}

$resultTotArr["result"]    = $resultArr;
$resultTotArr["code"]      = $code;
$resultTotArr["message"]   = $message;

echo json_encode($resultTotArr);
?>
