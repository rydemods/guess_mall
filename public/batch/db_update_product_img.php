<?php
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/shopdata2.php");

//exit;

echo "Start ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";


$sql = "select  pridx, productcode, maximage, minimage, tinyimage, content, 
                replace(maximage, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/'), 
                replace(minimage, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') , 
                replace(tinyimage, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') , 
                replace(content, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
        from tblproduct 
        where 1=1 
        and strpos(maximage, 'http://deconc.cafe24.com/web/') > 0 
        order by pridx 
        "; 
//        and	productcode = 'P000000V' 
$result = pmysql_query($sql, get_db_conn());
echo "sql = ".$sql."<br>";
echo "<hr>";

$i = 0;
while($row = pmysql_fetch_array($result)) {
    $i++;
    if( ($i % 10) == 0) echo $i."<br>";
    //exdebug($row);
    
    $pridx          = $row[pridx];
    $productcode    = $row[productcode];
    $maximage       = $row[maximage];
    $minimage       = $row[minimage];
    $tinyimage      = $row[tinyimage];
    $content        = pg_escape_string($row[content]);

    echo "pridx = ".$pridx." / productcode = ".$productcode."<br><br>";

    // tblproduct
    DbUpdateProductImages($pridx, $productcode, $maximage, $minimage, $tinyimage, $content);

    // tblmultiimage
    //DbUpdateProductMultiImages($pridx, $productcode);

    echo "<hr>";
}


function DbUpdateProductImages($pridx, $productcode, $maximage, $minimage, $tinyimage, $content) {

    $search = "deconc.cafe24.com/web/";

    if(strpos($maximage, $search) > 0) {

        $sql = "UPDATE  tblproduct SET 
                        maximage = replace(maximage, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
                where   productcode = '".$productcode."' 
                ";
        echo "sql = ".$sql."<br>";
        pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            echo "sql = ".$sql."<br>";
            echo $err."<br>";
            exit;
        }
    }

    if(strpos($minimage, $search) > 0) {

        $sql = "UPDATE  tblproduct SET 
                        minimage = replace(minimage, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
                where   productcode = '".$productcode."' 
                ";
        echo "sql = ".$sql."<br>";
        pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            echo "sql = ".$sql."<br>";
            echo $err."<br>";
            exit;
        }
    }

    if(strpos($tinyimage, $search) > 0) {

        $sql = "UPDATE  tblproduct SET 
                        tinyimage = replace(tinyimage, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
                where   productcode = '".$productcode."' 
                ";
        echo "sql = ".$sql."<br>";
        pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            echo "sql = ".$sql."<br>";
            echo $err."<br>";
            exit;
        }
    }

    if(strpos($content, $search) > 0) {

        $sql = "UPDATE  tblproduct SET 
                        content = replace(content, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
                where   productcode = '".$productcode."' 
                ";
        echo "sql = ".$sql."<br>";
        pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            echo "sql = ".$sql."<br>";
            echo $err."<br>";
            exit;
        }
    }
}

function DbUpdateProductMultiImages($pridx, $productcode) {

    $cnt = 0;
    $sql = "SELECT * FROM tblmultiimages WHERE pe1 = $goodsno ORDER BY pe2";
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
        //pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            echo "sql = ".$sql."<br>";
            echo $err."<br>";
            exit;
        }
    }
}




echo "End ".date("Y-m-d H:i:s")."<br>";
?>
