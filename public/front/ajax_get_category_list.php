<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$depth          = $_GET['depth'];
$categoryCode   = $_GET['cate_code'];

$code_a = substr($categoryCode, 0, 3);
$code_b = substr($categoryCode, 3, 3);
$code_c = substr($categoryCode, 6, 3);
$code_d = substr($categoryCode, 9, 3);

$sql  = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx ";
$sql .= "FROM tblproductcode ";

if ( $depth == 1 ) {
    $sql .= "WHERE code_a = '{$code_a}' AND code_b <> '000' AND code_c = '000' ";
} else if ( $depth == 2 ) {
    $sql .= "WHERE code_a = '{$code_a}' AND code_b = '{$code_b}' AND code_c <> '000' AND code_d = '000' ";
} else if ( $depth == 3 ) {
    $sql .= "WHERE code_a = '{$code_a}' AND code_b = '{$code_b}' AND code_c = '{$code_c}' AND code_d <> '000' ";
} else if ( $depth == 4 ) {
    $sql .= "WHERE code_a = '{$code_a}' AND code_b = '{$code_b}' AND code_c = '{$code_c}' AND code_d = '{$code_d}' ";
}

$sql .= "AND group_code !='NO' AND display_list is NULL ";
$sql .= "ORDER BY code_a,code_b,code_c,code_d ASC, cate_sort ASC";

$result = pmysql_query($sql);
$htmlResult = "";

$bAllList = false;
$selectedName = "ALL";

$arrList = array();
while ($row = pmysql_fetch_object($result)) {

    if ( $depth == 1 ) {
        if ( $row->code_a == $code_a && $row->code_b == $code_b ) {
            $selectedName = $row->code_name;
        }
    } else if ( $depth == 2 ) { 
        if ( $row->code_a == $code_a && $row->code_b == $code_b && $row->code_c == $code_c ) {
            $selectedName = $row->code_name;
        }
    } else if ( $depth == 3 ) {
        if ( $row->code_a == $code_a && $row->code_b == $code_b && $row->code_c == $code_c && $row->code_d == $code_d ) {
            $selectedName = $row->code_name;
        }
    }

    if ( $bAllList === false && $selectedName != "ALL" ) {
        $allCateCode = "";
        if ( $depth == 1 ) {
            $allCateCode = $row->code_a.'000'.'000'.'000';
        } else if ( $depth == 2 ) { 
            $allCateCode = $row->code_a.$row->code_b.'000'.'000';
        } else if ( $depth == 3 ) {
            $allCateCode = $row->code_a.$row->code_b.$row->code_c.'000';
        }

        if ( $allCateCode != "" ) {
            $htmlResult .= "<li><a href=\"javascript:;\" onClick=\"javascript:selectCategory('" . $allCateCode . "', '" . ($depth+1) . "', true);\">ALL</a></li>";
        }
        $bAllList = true;
    }

//  $htmlResult .= "<li><a href=\"javascript:;\" onClick=\"javascript:selectCategory('" . $row->cate_code . "', '" . ($depth+1) . "', true);\">" . $row->code_name . "</a></li>";

    array_push($arrList, "<li><a href=\"javascript:;\" onClick=\"javascript:selectCategory('" . $row->cate_code . "', '" . ($depth+1) . "', true);\">" . $row->code_name . "</a></li>");
}
pmysql_free_result($result);

foreach ( $arrList as $listItem ) {
    $htmlResult .= $listItem;
}

echo $selectedName . "||" . $htmlResult;
//echo $htmlResult;
?>
