<?
$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


//exdebug($_ShopInfo);
//exdebug($_data);
//exdebug($_ShopInfo->id);
//exdebug($_data->reserve_maxuse);
/**
tblshopinfo.reserve_maxuse : 5000 , 사용가능 최소 포인트, 적립금 사용안함으로 체크시 -1로 저장됨. 0보다 크거나 같은면 포인트 사용여부가 사용함임. 
tblshopinfo.reserve_maxprice : 50,000, 5만원 이상 상품 구매시 포인트 사용가능.
tblshopinfo.reserve_join : 가입축하포인트, 적립금 사용안함으로 체크시 0로 저장됨
tblshopinfo.reserve_limit = '-100' : 상품구매액의 100%까지 사용 가능.
tblshopinfo.reserve_useadd=-2 : 사용한 적립금을 제외한 구매금액 대비 적립(구매금액-사용적립금)
tblshopinfo.rcall_type = Y : 적립금/쿠폰 동시 적용 가능

select mb_point from g5_member where mb_id = 'ikazeus';
select * from g5_point where mb_id = 'ikazeus';
**/

$config['cf_point_term'] = 365; // 포인트 유효기간

/**
 * 로그인시 포인트 체크
**/
// 포인트 체크
echo "<hr>로그인시 포인트 체크<br>";
if($_data->reserve_maxuse >= 0) {
    $sum_point = get_point_sum($_ShopInfo->id);

    $sql= " update g5_member set mb_point = '$sum_point' where mb_id = '$_ShopInfo->id' ";
    pmysql_query($sql);
    echo "sql1 = ".$sql."<br>";
}


/**
 * 첫 로그인 포인트 지급
**/
// 첫 로그인 포인트 지급
echo "<hr>첫 로그인 포인트 지급<br>";
insert_point($_ShopInfo->id, 100, date("Y-m-d").' 첫로그인', '@login', $_ShopInfo->id, date("Y-m-d"));


/**
 * 관리자 > 포인트 증감
**/
echo "<hr>관리자 > 포인트 증감<br>";
$po_expire_term = '';
if($config['cf_point_term'] > 0) {
    $po_expire_term = $config['cf_point_term'];
}
echo "po_expire_term = ".$po_expire_term."<br>";

$expire = preg_replace('/[^0-9]/', '', $_POST['po_expire_term']);
echo "expire = ".$expire."<br>";
//insert_point($mb_id, $po_point, $po_content, '@passive', $mb_id, $member['mb_id'].'-'.uniqid(''), $expire);
//insert_point($_ShopInfo->id, 1000, date("Y-m-d")." 관리자지급", '@passive', $_ShopInfo->id, $_ShopInfo->id.'-'.uniqid(''), 1);

/**
 * 사용자 > 주문시 포인트 사용
**/
// 회원이면서 포인트를 사용했다면 포인트 테이블에 사용을 추가
echo "<hr>사용자 > 주문시 포인트 사용<br>";
$od_receipt_point = 5000;
$od_id = "test_orderid_".date("YmdHis");
//insert_point($_ShopInfo->id, (-1) * $od_receipt_point, "주문번호 $od_id 결제");


/**
 * 사용자 > 주문취소시 사용포인트 반환
**/
// 주문취소 회원의 포인트를 되돌려 줌
echo "<hr>사용자 > 주문취소시 사용포인트 반환<br>";
//if ($od_receipt_point > 0) insert_point($_ShopInfo->id, $od_receipt_point, "주문번호 $od_id 본인 취소");


/**
 * 프로모션 포인트 지급
**/
// 첫 로그인 포인트 지급
echo "<hr>프로모션 포인트 지급(유효기간 지정)<br>";
insert_point($_ShopInfo->id, 100, date("Y-m-d").' 프로모션 지급', '@event', $_ShopInfo->id, date("Y-m-d"), '7');


// 포인트 부여
function insert_point($mb_id, $point, $content='', $rel_table='', $rel_id='', $rel_action='', $expire=0)
{
    global $_data;
    global $config;
    //global $is_admin;

    // 포인트 사용을 하지 않는다면 return
    if ($_data->reserve_maxuse < 0) { return 0; }

    // 포인트가 없다면 업데이트 할 필요 없음
    if ($point == 0) { return 0; }

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mb_id == '') { return 0; }
    $mb = pmysql_fetch(" select mb_id from g5_member where mb_id = '$mb_id' ");
    if (!$mb['mb_id']) { return 0; }

    // 회원포인트
    $mb_point = get_point_sum($mb_id);
    echo "mb_point = ".$mb_point."<br>";

    // 이미 등록된 내역이라면 건너뜀
    if ($rel_table || $rel_id || $rel_action)
    {
        $sql = " select count(*) as cnt from g5_point
                  where mb_id = '$mb_id'
                    and po_rel_table = '$rel_table'
                    and po_rel_id = '$rel_id'
                    and po_rel_action = '$rel_action' ";
        $row = pmysql_fetch($sql);
        echo "sql2 = ".$sql."<br>";
        if ($row['cnt'])
            return -1;
    }

    // 포인트 건별 생성
    // expire : 1 => 만료 
    $po_expire_date = '9999-12-31';
    if($config['cf_point_term'] > 0) {
        if($expire > 0)
            $po_expire_date = date('Y-m-d', strtotime('+'.($expire - 1).' days', time()));
        else
            $po_expire_date = date('Y-m-d', strtotime('+'.($config['cf_point_term'] - 1).' days', time()));
    }

    $po_expired = 0;
    if($point < 0) {
        $po_expired = 1;
        $po_expire_date = date("Y-m-d");
    }
    $po_mb_point = $mb_point + $point;

    $sql = "insert into g5_point (mb_id, po_datetime, po_content, po_point, po_use_point, po_mb_point, po_expired, po_expire_date, po_rel_table ,po_rel_id, po_rel_action)
            values 
            ('$mb_id', '".date("Y-m-d H:i:s")."', '".addslashes($content)."', '$point', '0', '$po_mb_point', '$po_expired', '$po_expire_date', '$rel_table', '$rel_id', '$rel_action') 
            ";
    pmysql_query($sql);
    echo "sql3 = ".$sql."<br>";

    // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
    if($point < 0) {
        insert_use_point($mb_id, $point);
    }

    // 포인트 UPDATE
    $sql = " update g5_member set mb_point = '$po_mb_point' where mb_id = '$mb_id' ";
    pmysql_query($sql);
    echo "sql4 = ".$sql."<br>";

    return 1;
}

// 사용포인트 입력
function insert_use_point($mb_id, $point, $po_id=0)
{
    global $config;

    if($config['cf_point_term'])
        $sql_order = " order by po_expire_date asc, po_id asc ";
    else
        $sql_order = " order by po_id asc ";

    $point1 = abs($point);
    $sql = " select po_id, po_point, po_use_point
                from g5_point
                where mb_id = '$mb_id'
                  and po_id <> '$po_id'
                  and po_expired = '0'
                  and po_point > po_use_point
                $sql_order ";
    $result = pmysql_query($sql);
    echo "sql5 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['po_point'];
        $point3 = $row['po_use_point'];
        echo "point1 = ".$point1."<br>";
        echo "point2 = ".$point2."<br>";
        echo "point3 = ".$point3."<br>";

        if(($point2 - $point3) > $point1) {
            $sql = " update g5_point
                        set po_use_point = po_use_point + '$point1'
                        where po_id = '{$row['po_id']}' ";
            pmysql_query($sql);
            echo "sql6 = ".$sql."<br>";
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " update g5_point
                        set po_use_point = po_use_point + '$point4',
                            po_expired = '100'
                        where po_id = '{$row['po_id']}' ";
            pmysql_query($sql);
            echo "sql7 = ".$sql."<br>";
            $point1 -= $point4;
        }
    }
}

/*
// 사용포인트 삭제
function delete_use_point($mb_id, $point)
{
    global $config;

    if($config['cf_point_term'])
        $sql_order = " order by po_expire_date desc, po_id desc ";
    else
        $sql_order = " order by po_id desc ";

    $point1 = abs($point);
    $sql = " select po_id, po_use_point, po_expired, po_expire_date
                from g5_point
                where mb_id = '$mb_id'
                  and po_expired <> '1'
                  and po_use_point > 0
                $sql_order ";
    $result = pmysql_query($sql);
    echo "sql8 = ".$sql."<br>";
    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['po_use_point'];

        $po_expired = $row['po_expired'];
        if($row['po_expired'] == 100 && ($row['po_expire_date'] == '9999-12-31' || $row['po_expire_date'] >= date("Y-m-d")))
            $po_expired = 0;

        if($point2 > $point1) {
            $sql = " update g5_point
                        set po_use_point = po_use_point - '$point1',
                            po_expired = '$po_expired'
                        where po_id = '{$row['po_id']}' ";
            pmysql_query($sql);
            echo "sql9 = ".$sql."<br>";
            break;
        } else {
            $sql = " update g5_point
                        set po_use_point = '0',
                            po_expired = '$po_expired'
                        where po_id = '{$row['po_id']}' ";
            pmysql_query($sql);
            echo "sql10 = ".$sql."<br>";
            $point1 -= $point2;
        }
    }
}
*/
/*
// 소멸포인트 삭제
function delete_expire_point($mb_id, $point)
{
    global $config;

    $point1 = abs($point);
    $sql = " select po_id, po_use_point, po_expired, po_expire_date
                from g5_point
                where mb_id = '$mb_id'
                  and po_expired = '1'
                  and po_point >= 0
                  and po_use_point > 0
                order by po_expire_date desc, po_id desc ";
    $result = pmysql_query($sql);
    echo "sql11 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['po_use_point'];
        $po_expired = '0';
        $po_expire_date = '9999-12-31';
        if($config['cf_point_term'] > 0)
            $po_expire_date = date('Y-m-d', strtotime('+'.($config['cf_point_term'] - 1).' days', time()));

        if($point2 > $point1) {
            $sql = " update g5_point
                        set po_use_point = po_use_point - '$point1',
                            po_expired = '$po_expired',
                            po_expire_date = '$po_expire_date'
                        where po_id = '{$row['po_id']}' ";
            pmysql_query($sql);
            echo "sql12 = ".$sql."<br>";
            break;
        } else {
            $sql = " update g5_point
                        set po_use_point = '0',
                            po_expired = '$po_expired',
                            po_expire_date = '$po_expire_date'
                        where po_id = '{$row['po_id']}' ";
            pmysql_query($sql);
            echo "sql13 = ".$sql."<br>";
            $point1 -= $point2;
        }
    }
}
*/

// 포인트 내역 합계
function get_point_sum($mb_id)
{
    global $config;

    if($config['cf_point_term'] > 0) {
        // 소멸포인트가 있으면 내역 추가
        $expire_point = get_expire_point($mb_id);
        echo "expire_point = ".$expire_point."<br>";
        if($expire_point > 0) {
            $mb = get_member($mb_id, 'mb_point');
            $content = '포인트 소멸';
            $rel_table = '@expire';
            $rel_id = $mb_id;
            $rel_action = 'expire'.'-'.uniqid('');
            $point = $expire_point * (-1);
            $po_mb_point = $mb['mb_point'] + $point;
            $po_expire_date = date("Y-m-d");
            $po_expired = 1;

            $sql = "insert into g5_point (mb_id, po_datetime, po_content, po_point, po_use_point, po_mb_point, po_expired, po_expire_date, po_rel_table ,po_rel_id, po_rel_action)
            values 
            ('$mb_id', '".date("Y-m-d H:i:s")."', '".addslashes($content)."', '$point', '0', '$po_mb_point', '$po_expired', '$po_expire_date', '$rel_table', '$rel_id', '$rel_action') 
            ";
            pmysql_query($sql);
            echo "sql14 = ".$sql."<br>";

            // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
            if($point < 0) {
                insert_use_point($mb_id, $point);
            }
        }

        // 유효기간이 있을 때 기간이 지난 포인트 => expired 체크
        $sql = " update g5_point
                    set po_expired = '1'
                    where mb_id = '$mb_id'
                      and po_expired <> '1'
                      and po_expire_date <> '9999-12-31'
                      and po_expire_date < '".date("Y-m-d")."' ";
        pmysql_query($sql);
        echo "sql15 = ".$sql."<br>";
    }

    // 포인트합
    $sql = " select sum(po_point) as sum_po_point
                from g5_point
                where mb_id = '$mb_id' ";
    $row = pmysql_fetch($sql);
    echo "sql16 = ".$sql."<br>";

    return $row['sum_po_point'];
}

// 소멸 포인트
function get_expire_point($mb_id)
{
    global $config;

    if($config['cf_point_term'] == 0)
        return 0;

    $sql = " select sum(po_point - po_use_point) as sum_point
                from g5_point
                where mb_id = '$mb_id'
                  and po_expired = '0'
                  and po_expire_date <> '9999-12-31'
                  and po_expire_date < '".date("Y-m-d")."' ";
    $row = pmysql_fetch($sql);
    echo "sql17 = ".$sql."<br>";

    return $row['sum_point'];
}

/*
// 포인트 삭제
function delete_point($mb_id, $rel_table, $rel_id, $rel_action)
{
    //global $g5;

    $result = false;
    if ($rel_table || $rel_id || $rel_action)
    {
        // 포인트 내역정보
        $sql = " select * from g5_point
                    where mb_id = '$mb_id'
                      and po_rel_table = '$rel_table'
                      and po_rel_id = '$rel_id'
                      and po_rel_action = '$rel_action' ";
        $row = pmysql_fetch($sql);
        echo "sql18 = ".$sql."<br>";

        if($row['po_point'] < 0) {
            $mb_id = $row['mb_id'];
            $po_point = abs($row['po_point']);

            delete_use_point($mb_id, $po_point);
        } else {
            if($row['po_use_point'] > 0) {
                insert_use_point($row['mb_id'], $row['po_use_point'], $row['po_id']);
            }
        }

        $result = pmysql_query(" delete from g5_point
                     where mb_id = '$mb_id'
                       and po_rel_table = '$rel_table'
                       and po_rel_id = '$rel_id'
                       and po_rel_action = '$rel_action' ", false);

        // po_mb_point에 반영
        $sql = " update g5_point
                    set po_mb_point = po_mb_point - '{$row['po_point']}'
                    where mb_id = '$mb_id'
                      and po_id > '{$row['po_id']}' ";
        pmysql_query($sql);
        echo "sql19 = ".$sql."<br>";

        // 포인트 내역의 합을 구하고
        $sum_point = get_point_sum($mb_id);

        // 포인트 UPDATE
        $sql = " update g5_member set mb_point = '$sum_point' where mb_id = '$mb_id' ";
        $result = pmysql_query($sql);
        echo "sql20 = ".$sql."<br>";
    }

    return $result;
}
*/

// 회원 정보를 얻는다.
function get_member($mb_id, $fields='*', $emailOpt='1')
{
    //global $g5;

	if($emailOpt == 1)
	    return pmysql_fetch(" select $fields from g5_member where mb_id = TRIM('$mb_id') ");
	else if($emailOpt == 2)
	    return pmysql_fetch(" select $fields from g5_member where mb_email = TRIM('$mb_id') ");
}
?>