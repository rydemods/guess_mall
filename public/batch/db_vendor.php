<?php
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

echo "Start ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";

//exit;
/**
 *  0. 구분을 위해 mb_nick_date 에 '2016-02-23' 값을 넣었음.
 *  1. mysql db에 엑셀의 값을 csv로 insert한 후 table 의 값 sql 형태로 dump 떠서 deco.deco_vendor 에 insert 한다.(수작업)
 *  2. deco.deco_vendor 에서 select 해서 deco.tblvenderinfo 에 insert 한다.(아래 batch)
 *  3. deco.deco_vendor 에서 select 해서 tblproductbrand table 에 insert 한다.(아래 batch)
 *  4. tblproductbrand 의 brandname 이 unique 제약조건이 걸려있어서 오류발생 => 제약조건 삭제 처리함.
 *  5. 벤더 관리자 로긴 비번은 아이디 연속 2번입력하면 되게 처리.
**/

// 2.
$sql = "
        SELECT 	a.p1 AS id, a.p3 as brandname, 'YYYN' AS grant_product, a.p10::float AS rate, 
                (case when substr(a.p15,1,4) = 'bank' then a.p15 else '' end) as bank_name,  
                a.p16 as bank_acct, a.p17 as bank_accname,
                a.p12 as account_date, a.p27 as com_name, a.p23 as com_num, a.p22 as com_owner, 
                (case when a.p24 != '' then replace(a.p24, '-', '') else '' end) AS com_post, (case when a.p25 != '' then a.p25||' '||a.p26 else ' ' end) as com_addr, 
                a.p28 as com_biz, a.p29 as com_item, a.p4 as p_name, a.p5 as p_mobile, a.p6 as p_email, to_char(now(), 'YYYYMMDDHH24MISS') as regdate, 
                a.p2 AS vendor_code 
        FROM 	deco_vendor a 
        WHERE	a.p1 != '' 
        order by a.p1
        ";
// WHERE	a.p1 = 'apron' 
$result = pmysql_query($sql, get_db_conn());
$i = 0;
while($row = pmysql_fetch_object($result)) {
    $i++;
    if( ($i % 10) == 0) echo $i."<br>";

    $id = trim($row->id);
    $passwd = "*".strtoupper(SHA1(unhex(SHA1($id.$id))));
    $brandname = trim($row->brandname);
    $grant_product = trim($row->grant_product);
    $rate = trim($row->rate);
    $bank_name = DbGetBankName(trim($row->bank_name));
    $bank_acct = trim($row->bank_acct);
    $bank_accname = trim($row->bank_accname);
    if($bank_name) $bank_account = $bank_name."=".$bank_acct."=".$bank_accname;
    $account_date = trim($row->account_date);
    $com_name = trim($row->com_name);
    $com_num = trim($row->com_num);
    $com_owner = trim($row->com_owner);
    $com_post = trim($row->com_post);
    $com_addr = trim($row->com_addr);
    $com_biz = trim($row->com_biz);
    $com_item = trim($row->com_item);
    $p_name = trim($row->p_name);
    $p_mobile = trim($row->p_mobile);
    $p_email = trim($row->p_email);
    $regdate = trim($row->regdate);
    $vendor_code = trim($row->vendor_code);

    ###### tblvenderinfo
    $sql = "insert into tblvenderinfo 
            (id, passwd, grant_product, rate, bank_account, account_date, com_name, com_num, com_owner, com_post, com_addr, com_biz, com_item, com_tel, 
             p_name, p_mobile, p_email, regdate, disabled, vendor_code 
            ) 
            Values 
            ('$id', '$passwd', '$grant_product', '$rate', '$bank_account', '$account_date', '$com_name', '$com_num', '$com_owner', '$com_post', '$com_addr', '$com_biz', '$com_item', '$p_mobile',
             '$p_name', '$p_mobile', '$p_email', '$regdate', 0, '$vendor_code'
            )";
    pmysql_query($sql, get_db_conn());
    echo "sql = ".$sql."<br>";
    echo "<hr>";
    if($err=pmysql_error()) {
        echo $err."<br>";
        exit;
    }

    list($vender) = pmysql_fetch("SELECT CURRVAL(pg_get_serial_sequence('tblvenderinfo','vender'))");
    echo "vender = ".$vender."<br>";

    ###### tblvenderstore
    $sql = "INSERT INTO tblvenderstore (vender, id, brand_name, skin) VALUES ($vender, '$id', '$brandname', '1,1,1')";
	pmysql_query($sql,get_db_conn());
    echo "sql = ".$sql."<br>";
    echo "<hr>";
    if($err=pmysql_error()) {
        echo $err."<br>";
        exit;
    }

    ###### tblvenderstorecount
    $sql = "INSERT INTO tblvenderstorecount (vender) VALUES ($vender)";
    pmysql_query($sql,get_db_conn());
    echo "sql = ".$sql."<br>";
    echo "<hr>";
    if($err=pmysql_error()) {
        echo $err."<br>";
        exit;
    }

    ###### tblshopcount
    $sql="UPDATE tblshopcount SET vendercnt = vendercnt + 1 ";
    pmysql_query($sql,get_db_conn());
    echo "sql = ".$sql."<br>";
    echo "<hr>";
    if($err=pmysql_error()) {
        echo $err."<br>";
        exit;
    }

    ###### tblproductbrand
    $sql = "Insert into tblproductbrand (brandname, display_yn, vender) Values ('$brandname', 1, $vender) ";
    pmysql_query($sql, get_db_conn());
    echo "sql = ".$sql."<br>";
    echo "<hr>";
    if($err=pmysql_error()) {
        echo $err."<br>";
        exit;
    }
}



function DbGetBankName($bank) {

    $ret_bank = "";
    if($bank == "bank_82") $ret_bank = "농협회원조합";
    if($bank == "bank_03") $ret_bank = "기업은행";
    if($bank == "bank_04") $ret_bank = "국민은행";
    if($bank == "bank_05") $ret_bank = "외환은행";
    if($bank == "bank_07") $ret_bank = "수협중앙회";
    if($bank == "bank_11") $ret_bank = "농협중앙회";
    if($bank == "bank_20") $ret_bank = "우리은행";
    if($bank == "bank_21") $ret_bank = "조흥은행";
    if($bank == "bank_26") $ret_bank = "신한은행";
    if($bank == "bank_27") $ret_bank = "한미은행";
    if($bank == "bank_31") $ret_bank = "대구은행";
    if($bank == "bank_32") $ret_bank = "부산은행";
    if($bank == "bank_34") $ret_bank = "광주은행";
    if($bank == "bank_35") $ret_bank = "제주은행";
    if($bank == "bank_37") $ret_bank = "전북은행";
    if($bank == "bank_39") $ret_bank = "경남은행";
    if($bank == "bank_53") $ret_bank = "씨티은행";
    if($bank == "bank_02") $ret_bank = "산업은행";
    if($bank == "bank_71") $ret_bank = "우체국";
    if($bank == "bank_81") $ret_bank = "하나은행";
    if($bank == "bank_83") $ret_bank = "도이치은행";
    if($bank == "bank_84") $ret_bank = "상호저축은행";
    if($bank == "bank_85") $ret_bank = "새마을금고";
    if($bank == "bank_86") $ret_bank = "수출입은행";
    if($bank == "bank_87") $ret_bank = "신용협동조합";
    if($bank == "bank_12") $ret_bank = "농협개인";
    if($bank == "bank_89") $ret_bank = "홍콩상하이은행(HSBC)";
    if($bank == "bank_90") $ret_bank = "에이비엔암로은행";
    if($bank == "bank_52") $ret_bank = "모건스탠리은행";
    if($bank == "bank_57") $ret_bank = "유에프제이은행";
    if($bank == "bank_58") $ret_bank = "미즈호코퍼레이트은행";
    if($bank == "bank_59") $ret_bank = "미쓰비시도쿄은행";
    if($bank == "bank_60") $ret_bank = "뱅크오브아메리카";
    if($bank == "bank_209") $ret_bank = "동양종합금융증권";
    if($bank == "bank_218") $ret_bank = "현대증권";
    if($bank == "bank_230") $ret_bank = "미래에셋증권";
    if($bank == "bank_238") $ret_bank = "대우증권";
    if($bank == "bank_240") $ret_bank = "삼성증권";
    if($bank == "bank_243") $ret_bank = "한국투자증권";
    if($bank == "bank_247") $ret_bank = "우리투자증권";
    if($bank == "bank_261") $ret_bank = "교보증권";
    if($bank == "bank_262") $ret_bank = "하이투자증권";
    if($bank == "bank_263") $ret_bank = "투자증권HMC";
    if($bank == "bank_266") $ret_bank = "SK증권";
    if($bank == "bank_267") $ret_bank = "대신증권";
    if($bank == "bank_269") $ret_bank = "한화증권";
    if($bank == "bank_270") $ret_bank = "하나대투증권";
    if($bank == "bank_278") $ret_bank = "신한금융투자";
    if($bank == "bank_279") $ret_bank = "동부증권";
    if($bank == "bank_280") $ret_bank = "유진투자증권";
    if($bank == "bank_287") $ret_bank = "메리츠증권";
    if($bank == "bank_289") $ret_bank = "NH투자증권";
    if($bank == "bank_291") $ret_bank = "신영증권";
    if($bank == "bank_23") $ret_bank = "(구 SC제일은행)스탠다드차타드은행";
    if($bank == "bank_13") $ret_bank = "농협";
    if($bank == "bank_91") $ret_bank = "산림조합";

    return $ret_bank;
}


echo "End ".date("Y-m-d H:i:s")."<br>";
?>
