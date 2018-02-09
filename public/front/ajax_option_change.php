<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

// 옵션의 재고 (창고재고수량 - 주문대기수량)
function get_option_stock_qty($it_id, $io_id, $type)
{

    $sql = " SELECT option_quantity
                FROM tblproduct_option
                WHERE productcode = '".$it_id."' AND option_code = '".$io_id."' AND option_type = '".$type."' AND option_use = '1' ";
	
    $row = pmysql_fetch($sql);
    $jaego = (int)$row['option_quantity'];

    // 재고에서 빼지 않았고 주문인것만
    $sql = " SELECT SUM(quantity) AS sum_qty
               FROM tblorderproduct
              WHERE productcode = '".$it_id."'
                AND opt1_name||opt2_name = '".$io_id."'
                AND deli_gbn in ('N', 'H', 'S') ";
    $row = pmysql_fetch($sql);
    $daegi = (int)$row['sum_qty'];

    return $jaego - $daegi;
}

$it_id = $_POST['it_id'];
$opt_id = $_POST['opt_id'];
$idx = $_POST['idx'];
$sel_count = $_POST['sel_count'];

$sql = " SELECT * FROM tblproduct_option
                WHERE option_type = '0'
                  AND productcode = '".$it_id."'
                  AND option_use = '1'
                  AND option_code like '".$opt_id."%'
                ORDER BY option_num asc ";
$result = pmysql_query($sql);

$str = '<option value="">선택</option>';
$opt = array();
//echo $sql;
for($i=0; $row=pmysql_fetch_array($result); $i++) {
    $val = explode(chr(30), $row['option_code']);
    $key = $idx + 1;

    if(!strlen($val[$key]))
        continue;

    $continue = false;
    foreach($opt as $v) {
        if(strval($v) === strval($val[$key])) {
            $continue = true;
            break;
        }
    }
    if($continue)
        continue;

    $opt[] = strval($val[$key]);

    if($key + 1 < $sel_count) {
        $str .= PHP_EOL.'<option value="'.$val[$key].'">'.$val[$key].'</option>';
    } else {
        if($row['option_price'] >= 0)
            $price = '&nbsp;&nbsp;+ '.number_format($row['option_price']).'원';
        else
            $price = '&nbsp;&nbsp; '.number_format($row['option_price']).'원';

        $io_stock_qty = get_option_stock_qty($it_id, $row['option_code'], $row['option_type']);

        if($io_stock_qty < 1)
            $soldout = '&nbsp;&nbsp;[품절]';
        else
            $soldout = '';

        $str .= PHP_EOL.'<option value="'.$val[$key].','.$row['option_price'].','.$io_stock_qty.'">'.$val[$key].$price.$soldout.'</option>';
    }
}

echo $str;

?>