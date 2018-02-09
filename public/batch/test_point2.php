<?
$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


//exdebug($_ShopInfo);
//exdebug($_data);
//exdebug($_ShopInfo->id);
exdebug($_data->reserve_term);
/**
tblshopinfo.reserve_maxuse : 5000 , 사용가능 최소 포인트, 적립금 사용안함으로 체크시 -1로 저장됨. 0보다 크거나 같은면 포인트 사용여부가 사용함임. 
tblshopinfo.reserve_maxprice : 50,000, 5만원 이상 상품 구매시 포인트 사용가능.
tblshopinfo.reserve_join : 가입축하포인트, 적립금 사용안함으로 체크시 0로 저장됨
tblshopinfo.reserve_limit = '-100' : 상품구매액의 100%까지 사용 가능.
tblshopinfo.reserve_useadd=-2 : 사용한 적립금을 제외한 구매금액 대비 적립(구매금액-사용적립금)
tblshopinfo.rcall_type = Y : 적립금/쿠폰 동시 적용 가능

select mb_point from tblmember where mem_id = 'ikazeus';
select * from tblpoint where mem_id = 'ikazeus';
**/

//$_data->reserve_term = 365; // 포인트 유효기간

/**
 * 로그인시 포인트 체크
**/
// 포인트 체크
echo "<hr>로그인시 포인트 체크<br>";
if($_data->reserve_maxuse >= 0) {
    $sum_point = get_point_sum($_ShopInfo->id);

    $sql= " update tblmember set reserve = '$sum_point' where id = '$_ShopInfo->id' ";
    pmysql_query($sql);
    echo "sql1 = ".$sql."<br>";
}


/**
 * 첫 로그인 포인트 지급
**/
// 첫 로그인 포인트 지급
echo "<hr>첫 로그인 포인트 지급<br>";
insert_point($_ShopInfo->id, 100, date("Ymd").' 첫로그인', '@login', $_ShopInfo->id, date("Ymd"));


/**
 * 관리자 > 포인트 증감
**/
echo "<hr>관리자 > 포인트 증감<br>";
$po_expire_term = '';
if($_data->reserve_term > 0) {
    $po_expire_term = $_data->reserve_term;
}
echo "po_expire_term = ".$po_expire_term."<br>";

$expire = preg_replace('/[^0-9]/', '', $_POST['po_expire_term']);
echo "expire = ".$expire."<br>";
//insert_point($mem_id, $point, $body, '@admin', $mem_id, $member['mem_id'].'-'.uniqid(''), $expire);
//insert_point($_ShopInfo->id, 3000, date("Ymd")." 관리자지급", '@admin', $_ShopInfo->id, $_ShopInfo->id.'-'.uniqid(''), 1);

/**
 * 사용자 > 주문시 포인트 사용
**/
// 회원이면서 포인트를 사용했다면 포인트 테이블에 사용을 추가
echo "<hr>사용자 > 주문시 포인트 사용<br>";
$od_receipt_point = 9000;
$od_id = "test_orderid_".date("YmdHis");
//insert_point($_ShopInfo->id, (-1) * $od_receipt_point, "주문번호 $od_id 결제", '@order', $_ShopInfo->id, $_ShopInfo->id.'-'.uniqid(''));


/**
 * 사용자 > 주문취소시 사용포인트 반환
**/
// 주문취소 회원의 포인트를 되돌려 줌
echo "<hr>사용자 > 주문취소시 사용포인트 반환<br>";
//if ($od_receipt_point > 0) insert_point($_ShopInfo->id, $od_receipt_point, "주문번호 $od_id 본인 취소", '@order',$_ShopInfo->id, $_ShopInfo->id.'-'.uniqid(''), '7');


/**
 * 프로모션 포인트 지급
**/
// 첫 로그인 포인트 지급
echo "<hr>프로모션 포인트 지급(유효기간 지정)<br>";
//insert_point($_ShopInfo->id, 1100, date("Ymd").' 프로모션 지급', '@event', $_ShopInfo->id, date("Ymd"), '7');

/*
// 포인트 부여
function insert_point($mem_id, $point, $body='', $rel_flag='', $rel_mem_id='', $rel_job='', $expire=0)
{
    global $_data;
    global $config;
    //global $is_admin;

    // 포인트 사용을 하지 않는다면 return
    if ($_data->reserve_maxuse < 0) { return 0; }

    // 포인트가 없다면 업데이트 할 필요 없음
    if ($point == 0) { return 0; }

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mem_id == '') { return 0; }
    $mb = pmysql_fetch(" select id from tblmember where id = '$mem_id' ");
    echo " select id from tblmember where id = '$mem_id' "."<br>";
    if (!$mb['id']) { return 0; }

    // 회원포인트
    $mb_point = get_point_sum($mem_id);
    echo "mb_point = ".$mb_point."<br>";

    // 이미 등록된 내역이라면 건너뜀
    if ($rel_flag || $rel_mem_id || $rel_job)
    {
        $sql = " select count(*) as cnt from tblpoint
                  where mem_id = '$mem_id'
                    and rel_flag = '$rel_flag'
                    and rel_mem_id = '$rel_mem_id'
                    and rel_job = '$rel_job' ";
        $row = pmysql_fetch($sql);
        echo "sql2 = ".$sql."<br>";
        if ($row['cnt'])
            return -1;
    }

    // 포인트 건별 생성
    // expire : 1 => 만료 
    $expire_date = '99991231';
    if($_data->reserve_term > 0) {
        if($expire > 0)
            $expire_date = date('Ymd', strtotime('+'.($expire - 1).' days', time()));
        else
            $expire_date = date('Ymd', strtotime('+'.($_data->reserve_term - 1).' days', time()));
    }

    $expire_chk = 0;
    if($point < 0) {
        $expire_chk = 1;
        $expire_date = date("Ymd");
    }
    $tot_point = $mb_point + $point;

    $sql = "insert into tblpoint (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            values 
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job') 
            ";
    pmysql_query($sql);
    echo "sql3 = ".$sql."<br>";

    // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
    if($point < 0) {
        insert_use_point($mem_id, $point);
    }

    // 포인트 UPDATE
    $sql = " update tblmember set reserve = '$tot_point' where id = '$mem_id' ";
    pmysql_query($sql);
    echo "sql4 = ".$sql."<br>";

    return 1;
}

// 사용포인트 입력
function insert_use_point($mem_id, $point, $pid=0)
{
    global $_data;

    if($_data->reserve_term)
        $sql_order = " order by expire_date asc, pid asc ";
    else
        $sql_order = " order by pid asc ";

    $point1 = abs($point);
    $sql = " select pid, point, use_point
                from tblpoint
                where mem_id = '$mem_id'
                  and pid <> '$pid'
                  and expire_chk = '0'
                  and point > use_point
                $sql_order ";
    $result = pmysql_query($sql);
    echo "sql5 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['point'];
        $point3 = $row['use_point'];
        echo "point1 = ".$point1."<br>";
        echo "point2 = ".$point2."<br>";
        echo "point3 = ".$point3."<br>";

        if(($point2 - $point3) > $point1) {
            $sql = " update tblpoint
                        set use_point = use_point + '$point1'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql6 = ".$sql."<br>";
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " update tblpoint
                        set use_point = use_point + '$point4',
                            expire_chk = '99'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql7 = ".$sql."<br>";
            $point1 -= $point4;
        }
    }
}
*/
/*
// 사용포인트 삭제
function delete_use_point($mem_id, $point)
{
    global $_data;

    if($_data->reserve_term)
        $sql_order = " order by expire_date desc, pid desc ";
    else
        $sql_order = " order by pid desc ";

    $point1 = abs($point);
    $sql = " select pid, use_point, expire_chk, expire_date
                from tblpoint
                where mem_id = '$mem_id'
                  and expire_chk <> '1'
                  and use_point > 0
                $sql_order ";
    $result = pmysql_query($sql);
    echo "sql8 = ".$sql."<br>";
    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['use_point'];

        $expire_chk = $row['expire_chk'];
        if($row['expire_chk'] == 99 && ($row['expire_date'] == '99991231' || $row['expire_date'] >= date("Ymd")))
            $expire_chk = 0;

        if($point2 > $point1) {
            $sql = " update tblpoint
                        set use_point = use_point - '$point1',
                            expire_chk = '$expire_chk'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql9 = ".$sql."<br>";
            break;
        } else {
            $sql = " update tblpoint
                        set use_point = '0',
                            expire_chk = '$expire_chk'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql10 = ".$sql."<br>";
            $point1 -= $point2;
        }
    }
}
*/
/*
// 소멸포인트 삭제
function delete_expire_point($mem_id, $point)
{
    global $_data;

    $point1 = abs($point);
    $sql = " select pid, use_point, expire_chk, expire_date
                from tblpoint
                where mem_id = '$mem_id'
                  and expire_chk = '1'
                  and point >= 0
                  and use_point > 0
                order by expire_date desc, pid desc ";
    $result = pmysql_query($sql);
    echo "sql11 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['use_point'];
        $expire_chk = '0';
        $expire_date = '99991231';
        if($_data->reserve_term > 0)
            $expire_date = date('Ymd', strtotime('+'.($_data->reserve_term - 1).' days', time()));

        if($point2 > $point1) {
            $sql = " update tblpoint
                        set use_point = use_point - '$point1',
                            expire_chk = '$expire_chk',
                            expire_date = '$expire_date'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql12 = ".$sql."<br>";
            break;
        } else {
            $sql = " update tblpoint
                        set use_point = '0',
                            expire_chk = '$expire_chk',
                            expire_date = '$expire_date'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql13 = ".$sql."<br>";
            $point1 -= $point2;
        }
    }
}
*/
/*
// 포인트 내역 합계
function get_point_sum($mem_id)
{
    global $_data;

    if($_data->reserve_term > 0) {
        // 소멸포인트가 있으면 내역 추가
        $expire_point = get_expire_point($mem_id);
        echo "expire_point = ".$expire_point."<br>";
        if($expire_point > 0) {
            $mb = get_member($mem_id, 'reserve');
            $body = '포인트 소멸';
            $rel_flag = '@expire';
            $rel_mem_id = $mem_id;
            $rel_job = 'expire'.'-'.uniqid('');
            $point = $expire_point * (-1);
            $tot_point = $mb['reserve'] + $point;
            $expire_date = date("Ymd");
            $expire_chk = 1;

            $sql = "insert into tblpoint (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            values 
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job') 
            ";
            pmysql_query($sql);
            echo "sql14 = ".$sql."<br>";

            // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
            if($point < 0) {
                insert_use_point($mem_id, $point);
            }
        }

        // 유효기간이 있을 때 기간이 지난 포인트 => expired 체크
        $sql = " update tblpoint
                    set expire_chk = '1'
                    where mem_id = '$mem_id'
                      and expire_chk <> '1'
                      and expire_date <> '99991231'
                      and expire_date < '".date("Ymd")."' ";
        pmysql_query($sql);
        echo "sql15 = ".$sql."<br>";
    }

    // 포인트합
    $sql = " select COALESCE(sum(point),0) as sum_point
                from tblpoint
                where mem_id = '$mem_id' ";
    $row = pmysql_fetch($sql);
    echo "sql16 = ".$sql."<br>";

    return $row['sum_point'];
}

// 소멸 포인트
function get_expire_point($mem_id)
{
    global $_data;

    if($_data->reserve_term == 0)
        return 0;

    $sql = " select COALESCE(sum(point - use_point),0) as sum_point
                from tblpoint
                where mem_id = '$mem_id'
                  and expire_chk = '0'
                  and expire_date <> '99991231'
                  and expire_date < '".date("Ymd")."' ";
    $row = pmysql_fetch($sql);
    echo "sql17 = ".$sql."<br>";

    return $row['sum_point'];
}
*/
/*
// 포인트 삭제
function delete_point($mem_id, $rel_flag, $rel_mem_id, $rel_job)
{
    //global $g5;

    $result = false;
    if ($rel_flag || $rel_mem_id || $rel_job)
    {
        // 포인트 내역정보
        $sql = " select * from tblpoint
                    where mem_id = '$mem_id'
                      and rel_flag = '$rel_flag'
                      and rel_mem_id = '$rel_mem_id'
                      and rel_job = '$rel_job' ";
        $row = pmysql_fetch($sql);
        echo "sql18 = ".$sql."<br>";

        if($row['point'] < 0) {
            $mem_id = $row['mem_id'];
            $point = abs($row['point']);

            delete_use_point($mem_id, $point);
        } else {
            if($row['use_point'] > 0) {
                insert_use_point($row['mem_id'], $row['use_point'], $row['pid']);
            }
        }

        $result = pmysql_query(" delete from tblpoint
                     where mem_id = '$mem_id'
                       and rel_flag = '$rel_flag'
                       and rel_mem_id = '$rel_mem_id'
                       and rel_job = '$rel_job' ", false);

        // tot_point에 반영
        $sql = " update tblpoint
                    set tot_point = tot_point - '{$row['point']}'
                    where mem_id = '$mem_id'
                      and pid > '{$row['pid']}' ";
        pmysql_query($sql);
        echo "sql19 = ".$sql."<br>";

        // 포인트 내역의 합을 구하고
        $sum_point = get_point_sum($mem_id);

        // 포인트 UPDATE
        $sql = " update tblmember set mb_point = '$sum_point' where mem_id = '$mem_id' ";
        $result = pmysql_query($sql);
        echo "sql20 = ".$sql."<br>";
    }

    return $result;
}
*/
/*
// 회원 정보를 얻는다.
function get_member($mem_id, $fields='*', $emailOpt='1')
{
    //global $g5;

	if($emailOpt == 1)
	    return pmysql_fetch(" select $fields from tblmember where id = TRIM('$mem_id') ");
	else if($emailOpt == 2)
	    return pmysql_fetch(" select $fields from tblmember where email = TRIM('$mem_id') ");
}
*/
?>