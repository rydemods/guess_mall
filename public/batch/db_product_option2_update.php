<?php
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/shopdata2.php");

exit;

echo "Start ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";

/*
--delete from tblproduct;
--delete from tblproduct_option;
--delete from tblbrandproduct;
--delete from tblmultiimages;




-- 4304 까지 밀어넣었음.
-- 4047(옵셥사용X), 2210(조합독립형), 4046(연동형:아자샵의 추가옵셥처럼 1*N 구조이지만 필수여부가 있다.), 3717(추가옵션..이니셜등)
-- p83 C , S, 
SELECT * FROM deco_product WHERE p28 = 'F' AND p1 = 4046; -- p76 값이 있으면 연동형옵션 table 참조, p22 가 F 이면 옵션 사용 안함.
SELECT * FROM deco_product_detail WHERE p1 = 4046;
SELECT * FROM deco_product_detail_shopinfo WHERE pds43 = 'F';
SELECT * FROM deco_product_etc_img WHERE pe1 = 4046 ORDER BY pe2;
SELECT * FROM deco_product_optioninfo WHERE p10 = 'F' AND p2 = 4046;-- p4 값이 있으면 연동형옵션 table 참조

SELECT * FROM deco_product_option WHERE p1 = 4046; -- p12 값이 'F' 이면 재고 무제한처리.(품절기능 사용 안함)
SELECT * FROM deco_product_shopinfo WHERE p3 = 4046 AND p14 = 'F'; 
-- 연동형일경우..
SELECT * FROM deco_product_optioninfo_link WHERE o1 = 'S000000A' ORDER BY o10 ASC, o12 ASC;
SELECT o7, o8, '' product_code, o13 AS option_price, 0 AS option_quantity, 0 AS option_quantity_noti, 1 AS option_type, (CASE WHEN o4 = 'T' THEN 1 ELSE 0 END) AS option_use
FROM deco_product_optioninfo_link 
WHERE o1 = 'S000000A' 
ORDER BY o10 ASC, o12 ASC;

SELECT	o7, MAX(o10) FROM deco_product_optioninfo_link WHERE o1 = 'S000000A' AND o4 = 'T' GROUP BY o7 ;


-- 옵션추가가격,재고 등..(조합독립형)
SELECT a.p6 AS option_code, b.p11 AS product_code, a.p9 AS option_price, b.p3 AS option_quantity, 
	b.p5 AS option_quantity_noti, 0 AS option_type, (CASE WHEN a.p7 = 'T' THEN 1 ELSE 0 END) AS option_use, 
	(SELECT p4 FROM deco_product_optioninfo WHERE p2 = a.p3 AND p10 = 'F') AS connect, b.p12 AS qty_tf 
FROM deco_product_shopinfo a 
JOIN deco_product_option b ON a.p3 = b.p1 
WHERE a.p3 = 2210 
AND a.p14 = 'F'
AND a.p2 = b.p10 
ORDER BY a.p6
;

-- 추가옵션
SELECT * FROM deco_product_detail_shopinfo WHERE pds43 = 'F' AND pds11 = 'T';



-- 변수초기화 안해서 옵션값이 들어간 목록들 일괄 update
--update tblproduct set option1 = '', option2 = '', option2_tf = '', option2_maxlen = '' 
where productcode in 
(select b.productcode 
from deco_product p
join tblproduct b on p.p2 = b.productcode 
where p.p28 = 'F' 
and p.p22 = 'F'
and	b.option1 != '' 
);
*/
$sql = "SELECT	p.p1 as goodsno, p.p2 AS productcode, p.p3 AS productname, 
                p.p4::float AS sellprice, p.p6::float AS consumerprice, p.p5::float AS buyprice, 
                p.p17::float AS reserve, (CASE WHEN p.p16 = 'P' THEN 'Y' ELSE 'N' END) AS reservetype, p.p10 AS production, 
                p.p11 AS madein, p.p25 AS vendor_code, p.p36 AS selfcode, pds.pds20 AS keyword, 
                p.p21 AS maximage, p.p20 AS minimage, p.p19 AS tinyimage, 
                p.p40::float AS deli_price, (CASE WHEN p.p12 = 'T' THEN 'Y' ELSE 'N' END) AS display, 
                p.p8 AS regdate, p.p9 AS modifydate, pd.p2 AS content, p.p35 AS min_quantity, 
                p.p22 as option_yn, (CASE WHEN p.p76 != '' THEN 1 ELSE 0 END) AS op_type, p.p76 AS op_type_code, 
                pds.pds11 AS opt2_yn, pds.pds12 AS opt2_name, pds.pds13 AS opt2_tf, pds.pds55 as opt2_maxlen 
        FROM 	deco_product p 
        JOIN	deco_product_detail pd ON p.p1 = pd.p1 
        JOIN	deco_product_detail_shopinfo pds ON p.p1 = pds.pds2 
        WHERE	p.p28 = 'F' 
        ORDER BY p.p1 ASC 
        "; 
//        And     p.p1 = 2210 
$result = pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";

$i = 0;
while($row = pmysql_fetch_array($result)) {
    //exdebug($row);
    
    $goodsno        = $row[goodsno];
    $productcode    = $row[productcode];
    $productname    = pg_escape_string($row[productname]);
    $sellprice      = $row[sellprice];
    $consumerprice  = $row[consumerprice];
    $buyprice       = $row[buyprice];
    $reserve        = $row[reserve];
    $reservetype    = $row[reservetype];
    $production     = $row[production];
    $madein         = $row[madein];
    $selfcode       = $row[selfcode];
    $keyword        = $row[keyword];
    $maximage       = "http://cash-stores.com/web/product/big/".$row[maximage];
    $minimage       = "http://cash-stores.com/web/product/medium/".$row[minimage];
    $tinyimage      = "http://cash-stores.com/web/product/small/".$row[tinyimage];
    $deli_price     = $row[deli_price];
    $display        = $row[display];
    $regdate        = $row[regdate];
    $modifydate     = $row[modifydate];
    $content        = pg_escape_string($row[content]);
    $min_quantity   = $row[min_quantity];
    $option_yn      = $row[option_yn];      // 옵션유무(F 이면 옵션 없는 상품)
    $op_type        = $row[op_type];        // 옵션유형(0:조합형, 1:독립형, 2:옵션없음)
    $op_type_code   = $row[op_type_code];   // 독립형일 경우, 연결 코드
    $opt2_yn        = $row[opt2_yn];        // 추가문구옵션 사용여부 (T : 사용, F : 미사용)
    $opt2_name      = $row[opt2_name];      // 추가문구옵션명
    $opt2_tf        = $row[opt2_tf];        // 추가문구옵션 필수여부 (T/F/T 형식)
    $opt2_maxlen    = $row[opt2_maxlen];    // 추가문구옵션의 글자수제한(최대자리수)

    // 기존에 작업한거 추가문구옵션 데이타 제대로 안들어갔음. 새로 배치하나 만들어서 upate 처리하자..2016-03-07 
    if($opt2_yn == "T") {

        $i++;
        echo $i."<br>";

        $option2        = str_replace("#$%", "@#", $opt2_name);
        $option2_tf     = str_replace("/", "@#", $opt2_tf);
        $option2_len    = str_replace("/", "@#", $opt2_maxlen);


        //echo "opt2_yn = ".$opt2_yn."<br>";
        //echo "opt2_name = ".$opt2_name."<br>";
        //echo "opt2_tf = ".$opt2_tf."<br>";
        //echo "opt2_maxlen = ".$opt2_maxlen."<br>";

        echo "option2 = ".$option2."<br>";
        echo "option2_tf = ".$option2_tf."<br>";
        echo "option2_len = ".$option2_len."<br>";

        DbUpdateProductOpt2($goodsno, $productcode, $productname, $option2, $option2_tf, $option2_len);

        echo "productcode = ".$productcode."<br>";
        echo "<hr>";
    }
}


function DbUpdateProductOpt2($goodsno, $productcode, $productname, $option2, $option2_tf, $option2_len) {

    $sql = "Update  tblproduct SET 
                    option2 = '$option2', 
                    option2_tf = '$option2_tf', 
                    option2_maxlen = '$option2_len' 
            Where   productcode = '$productcode' 
        ";
    $insert = pmysql_query($sql,get_db_conn());
    echo "sql = ".$sql."<br>";
    if($err=pmysql_error()) {
        echo "sql = ".$sql."<br>";
        echo $err."<br>";
        exit;
    }
}

echo "End ".date("Y-m-d H:i:s")."<br>";
?>
