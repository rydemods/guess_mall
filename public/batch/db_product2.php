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
        FROM 	deconc_product p 
        JOIN	deconc_product_detail pd ON p.p1 = pd.p1 
        JOIN	deconc_product_detail_shopinfo pds ON p.p1 = pds.pds2 
        WHERE	p.p28 = 'F' 
        ORDER BY p.p1 ASC 
        "; 
//        And     p.p1 = 2210 
$result = pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";

$i = 0;
while($row = pmysql_fetch_array($result)) {
    $i++;
    if( ($i % 10) == 0) echo $i."<br>";
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
    $maximage       = "http://deconc.cafe24.com/web/product/big/".$row[maximage];
    $minimage       = "http://deconc.cafe24.com/web/product/medium/".$row[minimage];
    $tinyimage      = "http://deconc.cafe24.com/web/product/small/".$row[tinyimage];
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

    list($exist_pcode) = pmysql_fetch("Select count(*) as cnt From tblproduct Where productcode = '$productcode'");
    if($exist_pcode > 0) {
        echo "기존재 / productcode = ".$productcode."<br>";
        echo "<hr>";
        continue;
    }

    $vender         = GetVender($row[vendor_code]);
    $brand          = GetBrandIdx($vender);

    $option1 = $option1_tf = $option2 = $option2_tf = $option2_len = "";
    if($option_yn == "T") {
        $option1        = GetOpt1Name($row[goodsno], $op_type, $op_type_code);
        $option1_tf     = GetOpt1TF($row[goodsno], $op_type, $op_type_code);
    } else {
        $op_type = "2";
    }

    if($opt2_yn == "T") {
        $option2        = str_replace("#$%", "@#", $opt2_name);
        $option2_tf     = str_replace("/", "@#", $opt2_tf);
        $option2_len    = str_replace("/", "@#", $opt2_maxlen);
    }


    //echo "vender = ".$vender."<br>";
    //echo "brand = ".$brand."<br>";
    //echo "option1 = ".$option1."<br>";
    //echo "option2 = ".$option2."<br>";
    //echo "option2_tf = ".$option2_tf."<br>";

    $pridx = DbInsertProduct($goodsno, $productcode, $productname, $sellprice, $consumerprice, $buyprice, $reserve, $reservetype, $production, $madein, $selfcode, 
                            $keyword, $maximage, $minimage, $tinyimage, $deli_price, $display, $regdate, $modifydate, $content, $min_quantity, $op_type, $op_type_code, 
                            $opt2_yn, $opt2_name, $opt2_tf, $vender, $brand, $option1, $option2, $option2_tf, $option2_len, $option1_tf);

    if($option_yn == "T") {
        // tblproduct_option
        DbInsertProductOption($goodsno, $productcode, $op_type, $op_type_code);
    } else {

        // 옵션 사용안함..재고 무제한으로 처리하자..
        $noOptQty = 999999999; // 무제한
        $sql = "UPDATE tblproduct SET quantity = ".$noOptQty." WHERE productcode = '".$productcode."'";
        echo "sql = ".$sql."<br>";
        pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            echo "sql = ".$sql."<br>";
            echo $err."<br>";
            exit;
        }
    }

    // tblbrandproduct 
    DbInsertBrandProduct($goodsno, $productcode, $brand);

    // tblmultiimage
    DbInsertProductMultiImages($goodsno, $productcode);

    // tblproductlink
    //DbInsertProductLink($row[it_id], $row[ca_id]);

    echo "pridx = ".$pridx." / productcode = ".$productcode."<br>";
    echo "<hr>";
}

function GetVender($vender_code) {
    $sql = "Select vender From tblvenderinfo Where vendor_code = '$vender_code'";
    //echo "sql = ".$sql."<br>";
    list($vender) = pmysql_fetch($sql);
 
    return $vender;
}

function GetBrandIdx($vender) {
    $qry = "select bridx from tblproductbrand where vender = $vender ";
    //echo "sql = ".$qry."<br>";
    list($bridx) = pmysql_fetch($qry);

    return $bridx;
}

function GetOpt1Name($goodsno, $op_type, $op_type_code = '') {

    $opt1_name = "";
    if($op_type == "0") {   // 조합형
        $qry = "select p5 from deconc_product_shopinfo where p3 = $goodsno And p14 = 'F' Group by p5";
        list($opt1_name) = pmysql_fetch($qry);

        $opt1_name = str_replace("#$%", "@#", $opt1_name);

    } else {    // 독립형
        $qry = "SELECT	o7 FROM deconc_product_optioninfo_link WHERE o1 = '$op_type_code' AND o4 = 'T' GROUP BY o7 ORDER BY max(o10) ASC";
        $ret = pmysql_query($qry);
        while($row = pmysql_fetch_object($ret)) {
            $opt1_name .= $row->o7."@#";
        }
        $opt1_name = substr($opt1_name, 0, -2);
    }

    return $opt1_name;
}

function GetOpt1TF($goodsno, $op_type, $op_type_code = '') {

    $opt1_name = "";
    $opt1_tf = "";
    if($op_type == "0") {   // 조합형은 무조건 필수
        $qry = "select p5 from deconc_product_shopinfo where p3 = $goodsno And p14 = 'F' Group by p5";
        list($opt1_name) = pmysql_fetch($qry);

        $opt1_name = str_replace("#$%", "@#", $opt1_name);
        $opt1_tf_tmp = explode("@#", $opt1_name);
        for($i=0; $i < count($opt1_tf_tmp); $i++) {
            $opt1_tf .= "T"."@#";
        }
        $opt1_tf = substr($opt1_tf, 0, -2);

    } else {    // 독립형
        $qry = "SELECT	o7, MAX(o11) AS o11 FROM deconc_product_optioninfo_link WHERE o1 = '$op_type_code' AND o4 = 'T' GROUP BY o7 ORDER BY max(o10) ASC";
        $ret = pmysql_query($qry);
        while($row = pmysql_fetch_object($ret)) {
            $opt1_tf .= $row->o11."@#";
        }
        $opt1_tf = substr($opt1_tf, 0, -2);
    }

    return $opt1_tf;
}


function DbInsertProduct($goodsno, $productcode, $productname, $sellprice, $consumerprice, $buyprice, $reserve, $reservetype, $production, $madein, $selfcode, 
                        $keyword, $maximage, $minimage, $tinyimage, $deli_price, $display, $regdate, $modifydate, $content, $min_quantity, $op_type, $op_type_code, 
                        $opt2_yn, $opt2_name, $opt2_tf, $vender, $brand, $option1, $option2, $option2_tf, $option2_len, $option1_tf) {

	if( is_null($brand) || $brand=='' ) $brand = 0;

    $sql = "INSERT INTO tblproduct(
            productcode,
            productname,
            sellprice,
            consumerprice,
            buyprice,
            reserve, 
            reservetype, 
            production,
            madein,
            brand,
            selfcode,
            keyword, 
            maximage,
            minimage,
            tinyimage,
            deli_price, 
            display,
            vender, 
            regdate,
            modifydate,
            option1,
            option1_tf, 
            option2, 
            option2_tf, 
            option2_maxlen, 
            option_type, 
            content, 
            min_quantity
            ) VALUES (
            '$productcode',
            '$productname',
            $sellprice,
            $consumerprice,
            $buyprice,
            '$reserve', 
            '$reservetype', 
            '$production',
            '$madein',
            $brand,
            '$selfcode',
            '$keyword', 
            '$maximage',
            '$minimage',
            '$tinyimage',
            $deli_price, 
            '$display',
            $vender, 
            '$regdate',
            '$modifydate',
            '$option1',
            '$option1_tf', 
			'$option2', 
            '$option2_tf', 
            '$option2_len', 
            '$op_type', 
            '$content', 
            $min_quantity 
            )
        ";
    $insert = pmysql_query($sql,get_db_conn());
    if($err=pmysql_error()) {
        echo "sql = ".$sql."<br>";
        echo $err."<br>";
        exit;
    }

    list($pridx) = pmysql_fetch("SELECT CURRVAL(pg_get_serial_sequence('tblproduct','pridx'))");
    return $pridx;
}



function DbInsertProductOption($goodsno, $productcode, $op_type, $op_type_code='') {

    if($op_type == "0") {   // 조합형
        $sql = "SELECT  a.p6 AS option_code, b.p11 AS product_code, a.p9 AS option_price, b.p3 AS option_quantity, 
                        b.p5 AS option_quantity_noti, 0 AS option_type, (CASE WHEN a.p7 = 'T' THEN 1 ELSE 0 END) AS option_use, 
                        b.p12 AS qty_tf 
                FROM    deconc_product_shopinfo a 
                JOIN    deconc_product_option b ON a.p3 = b.p1 
                WHERE   a.p3 = $goodsno 
                AND     a.p14 = 'F'
                AND     a.p2 = b.p10 
                ORDER BY a.p6
                ";
    } else {    // 독립형 (독립형은 재고 체크 안하므로, qty_tf 값을 무조건 'F' 로 세팅함.)
        $sql = "SELECT  o7, o8, '$productcode' product_code, o13 AS option_price, 0 AS option_quantity, 0 AS option_quantity_noti, 1 AS option_type, (CASE WHEN o4 = 'T' THEN 1 ELSE 0 END) AS option_use, 
                        'F' AS qty_tf 
                FROM    deconc_product_optioninfo_link 
                WHERE   o1 = '$op_type_code' 
                ORDER BY o10 ASC, o12 ASC 
                ";
    }
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    $upOptQty = 0;
    $opt_qry_tf = "";   // 옵션 재고 체크 사용 유무..(아자샵은 이 기능이 없음=>상품의 수량에 무제한 수량을 넣어서 대체하자..옵션 중 하나라도 'T'이면 무제한으로 처리)
    while($row = pmysql_fetch_array($result)) {

        if($op_type == "0") $option_code = str_replace("#$%", chr(30), $row[option_code]);
        else $option_code = $row[o7].chr(30).$row[o8];

        $opt_qry_tf .= $row[qty_tf]."/";

        $optInsertSql = "INSERT INTO tblproduct_option ";
        $optInsertSql.= "( option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use ) ";
        $optInsertSql.= "VALUES ( '".$option_code."', '".$row[product_code]."', ".$row[option_price].", ".$row[option_quantity].", ".$row[option_quantity_noti].", ".$row[option_type].", ".$row[option_use]." ) ";
        pmysql_query($optInsertSql, get_db_conn());
        if($err=pmysql_error()) {
            echo "sql = ".$optInsertSql."<br>";
            echo $err."<br>";
            exit;
        }

        if($row[op_type] == "0") $upOptQty += $row[option_quantity];
    }
    echo "opt_qry_tf = ".$opt_qry_tf."<br>";
    if(strpos($opt_qry_tf, "F") !== false) $upOptQty = 999999999; // 무제한

    $sql = "UPDATE tblproduct SET quantity = ".$upOptQty." WHERE productcode = '".$productcode."'";
    echo "sql = ".$sql."<br>";
    pmysql_query($sql, get_db_conn());
    if($err=pmysql_error()) {
        echo "sql = ".$sql."<br>";
        echo $err."<br>";
        exit;
    }
}

function DbInsertBrandProduct($goodsno, $productcode, $brand) {

    $sql = "Insert into tblbrandproduct 
            (productcode, bridx) 
            Values 
            ('$productcode', $brand) 
            ";
    pmysql_query($sql, get_db_conn());
    if($err=pmysql_error()) {
        echo "sql = ".$sql."<br>";
        echo $err."<br>";
        exit;
    }
}

function DbInsertProductMultiImages($goodsno, $productcode) {

    $cnt = 0;
    $sql = "SELECT * FROM deconc_product_etc_img WHERE pe1 = $goodsno ORDER BY pe2";
    $ret_img = pmysql_query($sql, get_db_conn());
    $cnt = pmysql_num_rows($ret_img);

    if($cnt > 0) {
        $primg1 = $primg2 = $primg3 = $primg4 = $primg5 = $primg6 = $primg7 = $primg8 = $primg9 = $primg10 = "";
        $i = 1;
        while($row_img = pmysql_fetch_array($ret_img)) {

            $primg = "primg".$i;
            $$primg = $row_img[pe3];
            $i++;
        }

        $sql = "Insert into tblmultiimages 
                (productcode, primg01, primg02, primg03, primg04, primg05, primg06, primg07, primg08, primg09, primg10) 
                Values 
                ('".$productcode."', '".$primg1."', '".$primg2."', '".$primg3."', '".$primg4."', '".$primg5."', '".$primg6."', '".$primg7."', '".$primg8."', '".$primg9."', '".$primg10."') 
                ";
        pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            echo "sql = ".$sql."<br>";
            echo $err."<br>";
            exit;
        }
    }
}

function DbInsertProductLink($pcode, $p_cate) {
    $cate = getCategory($p_cate);

    $sql = "Insert into tblproductlink 
            (c_productcode, c_category, c_maincate, c_date, c_date_1, c_date_2, c_date_3, c_date_4) 
            Values 
            ('".$pcode."', '".$cate."', '1', '".date("YmdHis")."', '".date("YmdHis")."', '".date("YmdHis")."', '".date("YmdHis")."', '".date("YmdHis")."') 
            ";
    pmysql_query($sql, get_db_conn());
    //exdebug($sql);
}



echo "End ".date("Y-m-d H:i:s")."<br>";
?>
