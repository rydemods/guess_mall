<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("calendar.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//exdebug($_POST);
//exdebug($_GET);

$curDate = date("Ymd");
$curMon = date("Ym");
//$curDate = "20160513";
//$curMon = "201605";
//exdebug("curDate = ".$curDate);

$yesterday = date("Ymd", strtotime($curDate." -1 days"));
//exdebug("yesterday = ".$yesterday);

$nowWeek = date('w', strtotime($curDate));
//exdebug("week no = ".$nowWeek);

$weekday = date("Ymd", strtotime($curDate."-".$nowWeek." days"));
//exdebug("weekday = ".$weekday);

$weekstart = $weekday."000000";
$weekend = $curDate."235959";
//exdebug("weekstart = ".$weekstart);
//exdebug("weekend = ".$weekend);

if($_GET['point'] == "y") {
    ##### 매출현황(총주문금액 : 적립금 포함)
    $sql = "SELECT  COUNT(CASE WHEN (ordercode LIKE '".$curDate."%') THEN 1 ELSE NULL END) as totordcnt_today, 
                    SUM(CASE WHEN (ordercode LIKE '".$curDate."%') THEN price-dc_price::int+deli_price ELSE 0 END) as totordprice_today,
                    COUNT(CASE WHEN (ordercode LIKE '".$yesterday."%') THEN 1 ELSE NULL END) as totordcnt_yesterday, 
                    SUM(CASE WHEN (ordercode LIKE '".$yesterday."%') THEN price-dc_price::int+deli_price ELSE 0 END) as totordprice_yesterday, 
                    COUNT(CASE WHEN (ordercode >= '".$weekstart."' and ordercode <= '".$weekend."') THEN 1 ELSE NULL END) as totordcnt_week, 
                    SUM(CASE WHEN (ordercode >= '".$weekstart."' and ordercode <= '".$weekend."') THEN price-dc_price::int+deli_price ELSE 0 END) as totordprice_week, 
                    COUNT(CASE WHEN (ordercode LIKE '".$curMon."%') THEN 1 ELSE NULL END) as totordcnt_mon, 
                    SUM(CASE WHEN (ordercode LIKE '".$curMon."%') THEN price-dc_price::int+deli_price ELSE 0 END) as totordprice_mon, 

                    COUNT(CASE WHEN (bank_date LIKE '".$curDate."%') THEN 1 ELSE NULL END) as totordcnt_act_today, 
                    SUM(CASE WHEN (bank_date LIKE '".$curDate."%') THEN price-dc_price::int+deli_price ELSE 0 END) as totordprice_act_today, 
                    COUNT(CASE WHEN (bank_date LIKE '".$yesterday."%') THEN 1 ELSE NULL END) as totordcnt_act_yesterday, 
                    SUM(CASE WHEN (bank_date LIKE '".$yesterday."%') THEN price-dc_price::int+deli_price ELSE 0 END) as totordprice_act_yesterday,
                    COUNT(CASE WHEN (bank_date >= '".$weekstart."' and bank_date <= '".$weekend."') THEN 1 ELSE NULL END) as totordcnt_act_week, 
                    SUM(CASE WHEN (bank_date >= '".$weekstart."' and bank_date <= '".$weekend."') THEN price-dc_price::int+deli_price ELSE 0 END) as totordprice_act_week, 
                    COUNT(CASE WHEN (bank_date LIKE '".$curMon."%') THEN 1 ELSE NULL END) as totordcnt_act_mon, 
                    SUM(CASE WHEN (bank_date LIKE '".$curMon."%') THEN price-dc_price::int+deli_price ELSE 0 END) as totordprice_act_mon 

            FROM tblorderinfo 
            WHERE 1=1 
            ";
    //exdebug($sql);
    $res = pmysql_query($sql);
    $row = pmysql_fetch_object($res);

    $totordcnt_today = (int)$row->totordcnt_today;			    //오늘 주문건수
    $totordprice_today = (int)$row->totordprice_today;		    //오늘 주문금액
    $totordcnt_yesterday = (int)$row->totordcnt_yesterday;		//어제 주문건수
    $totordprice_yesterday = (int)$row->totordprice_yesterday;	//어제 주문금액
    $totordcnt_week = (int)$row->totordcnt_week;			    //이번주 주문건수
    $totordprice_week = (int)$row->totordprice_week;		    //이번주 주문금액
    $totordcnt_mon = (int)$row->totordcnt_mon;		            //이달의 주문건수
    $totordprice_mon = (int)$row->totordprice_mon;	            //이달의 주문금액

    $totordcnt_act_today = (int)$row->totordcnt_act_today;			    //오늘 실결제 주문건수
    $totordprice_act_today = (int)$row->totordprice_act_today;		    //오늘 실결제 주문금액
    $totordcnt_act_yesterday = (int)$row->totordcnt_act_yesterday;		//어제 실결제 주문건수
    $totordprice_act_yesterday = (int)$row->totordprice_act_yesterday;	//어제 실결제 주문금액
    $totordcnt_act_week = (int)$row->totordcnt_act_week;			    //이번주 실결제 주문건수
    $totordprice_act_week = (int)$row->totordprice_act_week;		    //이번주 실결제 주문금액
    $totordcnt_act_mon = (int)$row->totordcnt_act_mon;		            //이달의 실결제 주문건수
    $totordprice_act_mon = (int)$row->totordprice_act_mon;	            //이달의 실결제 주문금액

    pmysql_free_result($row);
    ########################################
    ##### 매출현황(총환불금액 : 적립금 포함)
    $sql = "SELECT 	SUM(CASE WHEN (cdt LIKE '".$curDate."%') THEN cnt_ord ELSE 0 END) as totordcnt_repay_today, 
                    SUM(CASE WHEN (cdt LIKE '".$curDate."%') THEN ordprice-coupon+op_deli_price ELSE 0 END) as totordprice_repay_today, 

                    SUM(CASE WHEN (cdt LIKE '".$yesterday."%') THEN cnt_ord ELSE NULL END) as totordcnt_repay_yesterday, 
                    SUM(CASE WHEN (cdt LIKE '".$yesterday."%') THEN ordprice-coupon+op_deli_price ELSE 0 END) as totordprice_repay_yesterday,

                    SUM(CASE WHEN (cdt >= '".$weekstart."' and cdt <= '".$weekend."') THEN cnt_ord ELSE NULL END) as totordcnt_repay_week, 
                    SUM(CASE WHEN (cdt >= '".$weekstart."' and cdt <= '".$weekend."') THEN ordprice-coupon+op_deli_price ELSE 0 END) as totordprice_repay_week, 

                    SUM(CASE WHEN (cdt LIKE '".$curMon."%') THEN cnt_ord ELSE NULL END) as totordcnt_repay_mon, 
                    SUM(CASE WHEN (cdt LIKE '".$curMon."%') THEN ordprice-coupon+op_deli_price ELSE 0 END) as totordprice_repay_mon 
            FROM 
            (
                SELECT 	substring(cdt, 1, 8) as cdt, count(a.ordercode) as cnt_ord,  
                        sum(op_ordprice) as ordprice, sum(op_coupon) as coupon, sum(op_usepoint) as usepoint, sum(op_deli_price) as op_deli_price 
                FROM (
                    SELECT  substring(oc.cfindt, 1, 8) as cdt, o.ordercode, 
                        sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon,  
                        sum(op.use_point) as op_usepoint, sum(op.deli_price) as op_deli_price
                    FROM    tblorderinfo o 
                    JOIN    tblorderproduct op on o.ordercode = op.ordercode 
                    JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
                    JOIN    tblproductbrand v on op.vender = v.vender 
                    JOIN    tblproduct p on op.productcode = p.productcode 
                    WHERE   1=1 
                    AND	    o.oi_step1 in ('1', '2', '3', '4') 
                    AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
                    AND	    op.op_step = 44 
                    GROUP BY substring(oc.cfindt, 1, 8), o.ordercode 
                ) a 
                GROUP BY substring(cdt, 1, 8) 
            ) z
            WHERE 1=1
            ";
    //exdebug($sql);
    $res = pmysql_query($sql);
    $row = pmysql_fetch_object($res);

    $totordcnt_repay_today = (int)$row->totordcnt_repay_today;			    //오늘 환불 주문건수
    $totordprice_repay_today = (int)$row->totordprice_repay_today;		    //오늘 환불 주문금액
    $totordcnt_repay_yesterday = (int)$row->totordcnt_repay_yesterday;		//어제 환불 주문건수
    $totordprice_repay_yesterday = (int)$row->totordprice_repay_yesterday;	//어제 환불 주문금액
    $totordcnt_repay_week = (int)$row->totordcnt_repay_week;			    //이번주 환불 주문건수
    $totordprice_repay_week = (int)$row->totordprice_repay_week;		    //이번주 환불 주문금액
    $totordcnt_repay_mon = (int)$row->totordcnt_repay_mon;		            //이달의 환불 주문건수
    $totordprice_repay_mon = (int)$row->totordprice_repay_mon;	            //이달의 환불 주문금액

    pmysql_free_result($row);
    ########################################
}

if($_GET['point'] == "n") {
    ##### 매출현황(총주문금액 : 적립금 제외)
    $sql = "SELECT  COUNT(CASE WHEN (ordercode LIKE '".$curDate."%') THEN 1 ELSE NULL END) as totordcnt_today, 
                    SUM(CASE WHEN (ordercode LIKE '".$curDate."%') THEN price-dc_price::int-reserve+deli_price ELSE 0 END) as totordprice_today,
                    COUNT(CASE WHEN (ordercode LIKE '".$yesterday."%') THEN 1 ELSE NULL END) as totordcnt_yesterday, 
                    SUM(CASE WHEN (ordercode LIKE '".$yesterday."%') THEN price-dc_price::int-reserve+deli_price ELSE 0 END) as totordprice_yesterday, 
                    COUNT(CASE WHEN (ordercode >= '".$weekstart."' and ordercode <= '".$weekend."') THEN 1 ELSE NULL END) as totordcnt_week, 
                    SUM(CASE WHEN (ordercode >= '".$weekstart."' and ordercode <= '".$weekend."') THEN price-dc_price::int-reserve+deli_price ELSE 0 END) as totordprice_week, 
                    COUNT(CASE WHEN (ordercode LIKE '".$curMon."%') THEN 1 ELSE NULL END) as totordcnt_mon, 
                    SUM(CASE WHEN (ordercode LIKE '".$curMon."%') THEN price-dc_price::int-reserve+deli_price ELSE 0 END) as totordprice_mon, 

                    COUNT(CASE WHEN (bank_date LIKE '".$curDate."%') THEN 1 ELSE NULL END) as totordcnt_act_today, 
                    SUM(CASE WHEN (bank_date LIKE '".$curDate."%') THEN price-dc_price::int-reserve+deli_price ELSE 0 END) as totordprice_act_today, 
                    COUNT(CASE WHEN (bank_date LIKE '".$yesterday."%') THEN 1 ELSE NULL END) as totordcnt_act_yesterday, 
                    SUM(CASE WHEN (bank_date LIKE '".$yesterday."%') THEN price-dc_price::int-reserve+deli_price ELSE 0 END) as totordprice_act_yesterday,
                    COUNT(CASE WHEN (bank_date >= '".$weekstart."' and bank_date <= '".$weekend."') THEN 1 ELSE NULL END) as totordcnt_act_week, 
                    SUM(CASE WHEN (bank_date >= '".$weekstart."' and bank_date <= '".$weekend."') THEN price-dc_price::int-reserve+deli_price ELSE 0 END) as totordprice_act_week, 
                    COUNT(CASE WHEN (bank_date LIKE '".$curMon."%') THEN 1 ELSE NULL END) as totordcnt_act_mon, 
                    SUM(CASE WHEN (bank_date LIKE '".$curMon."%') THEN price-dc_price::int-reserve+deli_price ELSE 0 END) as totordprice_act_mon 

            FROM tblorderinfo 
            WHERE 1=1 
            ";
    //exdebug($sql);
    $res = pmysql_query($sql);
    $row = pmysql_fetch_object($res);

    $totordcnt_today = (int)$row->totordcnt_today;			    //오늘 주문건수
    $totordprice_today = (int)$row->totordprice_today;		    //오늘 주문금액
    $totordcnt_yesterday = (int)$row->totordcnt_yesterday;		//어제 주문건수
    $totordprice_yesterday = (int)$row->totordprice_yesterday;	//어제 주문금액
    $totordcnt_week = (int)$row->totordcnt_week;			    //이번주 주문건수
    $totordprice_week = (int)$row->totordprice_week;		    //이번주 주문금액
    $totordcnt_mon = (int)$row->totordcnt_mon;		            //이달의 주문건수
    $totordprice_mon = (int)$row->totordprice_mon;	            //이달의 주문금액

    $totordcnt_act_today = (int)$row->totordcnt_act_today;			    //오늘 실결제 주문건수
    $totordprice_act_today = (int)$row->totordprice_act_today;		    //오늘 실결제 주문금액
    $totordcnt_act_yesterday = (int)$row->totordcnt_act_yesterday;		//어제 실결제 주문건수
    $totordprice_act_yesterday = (int)$row->totordprice_act_yesterday;	//어제 실결제 주문금액
    $totordcnt_act_week = (int)$row->totordcnt_act_week;			    //이번주 실결제 주문건수
    $totordprice_act_week = (int)$row->totordprice_act_week;		    //이번주 실결제 주문금액
    $totordcnt_act_mon = (int)$row->totordcnt_act_mon;		            //이달의 실결제 주문건수
    $totordprice_act_mon = (int)$row->totordprice_act_mon;	            //이달의 실결제 주문금액

    pmysql_free_result($row);
    ########################################
    ##### 매출현황(총환불금액 : 적립금 제외)
    $sql = "SELECT 	SUM(CASE WHEN (cdt LIKE '".$curDate."%') THEN cnt_ord ELSE 0 END) as totordcnt_repay_today, 
                    SUM(CASE WHEN (cdt LIKE '".$curDate."%') THEN ordprice-coupon-usepoint+op_deli_price ELSE 0 END) as totordprice_repay_today, 

                    SUM(CASE WHEN (cdt LIKE '".$yesterday."%') THEN cnt_ord ELSE NULL END) as totordcnt_repay_yesterday, 
                    SUM(CASE WHEN (cdt LIKE '".$yesterday."%') THEN ordprice-coupon-usepoint+op_deli_price ELSE 0 END) as totordprice_repay_yesterday,

                    SUM(CASE WHEN (cdt >= '".$weekstart."' and cdt <= '".$weekend."') THEN cnt_ord ELSE NULL END) as totordcnt_repay_week, 
                    SUM(CASE WHEN (cdt >= '".$weekstart."' and cdt <= '".$weekend."') THEN ordprice-coupon-usepoint+op_deli_price ELSE 0 END) as totordprice_repay_week, 

                    SUM(CASE WHEN (cdt LIKE '".$curMon."%') THEN cnt_ord ELSE NULL END) as totordcnt_repay_mon, 
                    SUM(CASE WHEN (cdt LIKE '".$curMon."%') THEN ordprice-coupon-usepoint+op_deli_price ELSE 0 END) as totordprice_repay_mon 
            FROM 
            (
                SELECT 	substring(cdt, 1, 8) as cdt, count(a.ordercode) as cnt_ord,  
                        sum(op_ordprice) as ordprice, sum(op_coupon) as coupon, sum(op_usepoint) as usepoint, sum(op_deli_price) as op_deli_price 
                FROM (
                    SELECT  substring(oc.cfindt, 1, 8) as cdt, o.ordercode, 
                        sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon,  
                        sum(op.use_point) as op_usepoint, sum(op.deli_price) as op_deli_price
                    FROM    tblorderinfo o 
                    JOIN    tblorderproduct op on o.ordercode = op.ordercode 
                    JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
                    JOIN    tblproductbrand v on op.vender = v.vender 
                    JOIN    tblproduct p on op.productcode = p.productcode 
                    WHERE   1=1 
                    AND	    o.oi_step1 in ('1', '2', '3', '4') 
                    AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
                    AND	    op.op_step = 44 
                    GROUP BY substring(oc.cfindt, 1, 8), o.ordercode 
                ) a 
                GROUP BY substring(cdt, 1, 8) 
            ) z
            WHERE 1=1
            ";
    //exdebug($sql);
    $res = pmysql_query($sql);
    $row = pmysql_fetch_object($res);

    $totordcnt_repay_today = (int)$row->totordcnt_repay_today;			    //오늘 환불 주문건수
    $totordprice_repay_today = (int)$row->totordprice_repay_today;		    //오늘 환불 주문금액
    $totordcnt_repay_yesterday = (int)$row->totordcnt_repay_yesterday;		//어제 환불 주문건수
    $totordprice_repay_yesterday = (int)$row->totordprice_repay_yesterday;	//어제 환불 주문금액
    $totordcnt_repay_week = (int)$row->totordcnt_repay_week;			    //이번주 환불 주문건수
    $totordprice_repay_week = (int)$row->totordprice_repay_week;		    //이번주 환불 주문금액
    $totordcnt_repay_mon = (int)$row->totordcnt_repay_mon;		            //이달의 환불 주문건수
    $totordprice_repay_mon = (int)$row->totordprice_repay_mon;	            //이달의 환불 주문금액

    pmysql_free_result($row);
    ########################################
}

if($_GET['coupon'] == "y") {
     ##### 매출현황(총주문금액 : 쿠폰 포함)
    $sql = "SELECT  COUNT(CASE WHEN (ordercode LIKE '".$curDate."%') THEN 1 ELSE NULL END) as totordcnt_today, 
                    SUM(CASE WHEN (ordercode LIKE '".$curDate."%') THEN price-reserve+deli_price ELSE 0 END) as totordprice_today,
                    COUNT(CASE WHEN (ordercode LIKE '".$yesterday."%') THEN 1 ELSE NULL END) as totordcnt_yesterday, 
                    SUM(CASE WHEN (ordercode LIKE '".$yesterday."%') THEN price-reserve+deli_price ELSE 0 END) as totordprice_yesterday, 
                    COUNT(CASE WHEN (ordercode >= '".$weekstart."' and ordercode <= '".$weekend."') THEN 1 ELSE NULL END) as totordcnt_week, 
                    SUM(CASE WHEN (ordercode >= '".$weekstart."' and ordercode <= '".$weekend."') THEN price-reserve+deli_price ELSE 0 END) as totordprice_week, 
                    COUNT(CASE WHEN (ordercode LIKE '".$curMon."%') THEN 1 ELSE NULL END) as totordcnt_mon, 
                    SUM(CASE WHEN (ordercode LIKE '".$curMon."%') THEN price-reserve+deli_price ELSE 0 END) as totordprice_mon, 

                    COUNT(CASE WHEN (bank_date LIKE '".$curDate."%') THEN 1 ELSE NULL END) as totordcnt_act_today, 
                    SUM(CASE WHEN (bank_date LIKE '".$curDate."%') THEN price-reserve+deli_price ELSE 0 END) as totordprice_act_today, 
                    COUNT(CASE WHEN (bank_date LIKE '".$yesterday."%') THEN 1 ELSE NULL END) as totordcnt_act_yesterday, 
                    SUM(CASE WHEN (bank_date LIKE '".$yesterday."%') THEN price-reserve+deli_price ELSE 0 END) as totordprice_act_yesterday,
                    COUNT(CASE WHEN (bank_date >= '".$weekstart."' and bank_date <= '".$weekend."') THEN 1 ELSE NULL END) as totordcnt_act_week, 
                    SUM(CASE WHEN (bank_date >= '".$weekstart."' and bank_date <= '".$weekend."') THEN price-reserve+deli_price ELSE 0 END) as totordprice_act_week, 
                    COUNT(CASE WHEN (bank_date LIKE '".$curMon."%') THEN 1 ELSE NULL END) as totordcnt_act_mon, 
                    SUM(CASE WHEN (bank_date LIKE '".$curMon."%') THEN price-reserve+deli_price ELSE 0 END) as totordprice_act_mon 

            FROM tblorderinfo 
            WHERE 1=1 
            ";
    //exdebug($sql);
    $res = pmysql_query($sql);
    $row = pmysql_fetch_object($res);

    $totordcnt_today = (int)$row->totordcnt_today;			    //오늘 주문건수
    $totordprice_today = (int)$row->totordprice_today;		    //오늘 주문금액
    $totordcnt_yesterday = (int)$row->totordcnt_yesterday;		//어제 주문건수
    $totordprice_yesterday = (int)$row->totordprice_yesterday;	//어제 주문금액
    $totordcnt_week = (int)$row->totordcnt_week;			    //이번주 주문건수
    $totordprice_week = (int)$row->totordprice_week;		    //이번주 주문금액
    $totordcnt_mon = (int)$row->totordcnt_mon;		            //이달의 주문건수
    $totordprice_mon = (int)$row->totordprice_mon;	            //이달의 주문금액

    $totordcnt_act_today = (int)$row->totordcnt_act_today;			    //오늘 실결제 주문건수
    $totordprice_act_today = (int)$row->totordprice_act_today;		    //오늘 실결제 주문금액
    $totordcnt_act_yesterday = (int)$row->totordcnt_act_yesterday;		//어제 실결제 주문건수
    $totordprice_act_yesterday = (int)$row->totordprice_act_yesterday;	//어제 실결제 주문금액
    $totordcnt_act_week = (int)$row->totordcnt_act_week;			    //이번주 실결제 주문건수
    $totordprice_act_week = (int)$row->totordprice_act_week;		    //이번주 실결제 주문금액
    $totordcnt_act_mon = (int)$row->totordcnt_act_mon;		            //이달의 실결제 주문건수
    $totordprice_act_mon = (int)$row->totordprice_act_mon;	            //이달의 실결제 주문금액

    pmysql_free_result($row);
    ########################################
    ##### 매출현황(총환불금액 : 쿠폰 포함)
    $sql = "SELECT 	SUM(CASE WHEN (cdt LIKE '".$curDate."%') THEN cnt_ord ELSE 0 END) as totordcnt_repay_today, 
                    SUM(CASE WHEN (cdt LIKE '".$curDate."%') THEN ordprice-usepoint+op_deli_price ELSE 0 END) as totordprice_repay_today, 

                    SUM(CASE WHEN (cdt LIKE '".$yesterday."%') THEN cnt_ord ELSE NULL END) as totordcnt_repay_yesterday, 
                    SUM(CASE WHEN (cdt LIKE '".$yesterday."%') THEN ordprice-usepoint+op_deli_price ELSE 0 END) as totordprice_repay_yesterday,

                    SUM(CASE WHEN (cdt >= '".$weekstart."' and cdt <= '".$weekend."') THEN cnt_ord ELSE NULL END) as totordcnt_repay_week, 
                    SUM(CASE WHEN (cdt >= '".$weekstart."' and cdt <= '".$weekend."') THEN ordprice-usepoint+op_deli_price ELSE 0 END) as totordprice_repay_week, 

                    SUM(CASE WHEN (cdt LIKE '".$curMon."%') THEN cnt_ord ELSE NULL END) as totordcnt_repay_mon, 
                    SUM(CASE WHEN (cdt LIKE '".$curMon."%') THEN ordprice-usepoint+op_deli_price ELSE 0 END) as totordprice_repay_mon 
            FROM 
            (
                SELECT 	substring(cdt, 1, 8) as cdt, count(a.ordercode) as cnt_ord,  
                        sum(op_ordprice) as ordprice, sum(op_coupon) as coupon, sum(op_usepoint) as usepoint, sum(op_deli_price) as op_deli_price 
                FROM (
                    SELECT  substring(oc.cfindt, 1, 8) as cdt, o.ordercode, 
                        sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon,  
                        sum(op.use_point) as op_usepoint, sum(op.deli_price) as op_deli_price
                    FROM    tblorderinfo o 
                    JOIN    tblorderproduct op on o.ordercode = op.ordercode 
                    JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
                    JOIN    tblproductbrand v on op.vender = v.vender 
                    JOIN    tblproduct p on op.productcode = p.productcode 
                    WHERE   1=1 
                    AND	    o.oi_step1 in ('1', '2', '3', '4') 
                    AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
                    AND	    op.op_step = 44 
                    GROUP BY substring(oc.cfindt, 1, 8), o.ordercode 
                ) a 
                GROUP BY substring(cdt, 1, 8) 
            ) z
            WHERE 1=1
            ";
    //exdebug($sql);
    $res = pmysql_query($sql);
    $row = pmysql_fetch_object($res);

    $totordcnt_repay_today = (int)$row->totordcnt_repay_today;			    //오늘 환불 주문건수
    $totordprice_repay_today = (int)$row->totordprice_repay_today;		    //오늘 환불 주문금액
    $totordcnt_repay_yesterday = (int)$row->totordcnt_repay_yesterday;		//어제 환불 주문건수
    $totordprice_repay_yesterday = (int)$row->totordprice_repay_yesterday;	//어제 환불 주문금액
    $totordcnt_repay_week = (int)$row->totordcnt_repay_week;			    //이번주 환불 주문건수
    $totordprice_repay_week = (int)$row->totordprice_repay_week;		    //이번주 환불 주문금액
    $totordcnt_repay_mon = (int)$row->totordcnt_repay_mon;		            //이달의 환불 주문건수
    $totordprice_repay_mon = (int)$row->totordprice_repay_mon;	            //이달의 환불 주문금액

    pmysql_free_result($row);
    ########################################
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>

<?
// 총 주문 금액 링크
$link_totordprice_today = "./order_list_all_order.php?s_check=al&s_date=ordercode&search_start=".$curDate."&search_end=".$curDate."&s_prod=pn&search_prod=&paystate=A&ord_flag_all=4";
$link_totordprice_yesterday = "./order_list_all_order.php?s_check=al&s_date=ordercode&search_start=".$yesterday."&search_end=".$yesterday."&s_prod=pn&search_prod=&paystate=A&ord_flag_all=4";
$link_totordprice_week = "./order_list_all_order.php?s_check=al&s_date=ordercode&search_start=".$weekday."&search_end=".$curDate."&s_prod=pn&search_prod=&paystate=A&ord_flag_all=4";
$link_totordprice_mon = "./order_list_all_order.php?s_check=al&s_date=ordercode&search_start=".date("Ym")."01"."&search_end=".$curDate."&s_prod=pn&search_prod=&paystate=A&ord_flag_all=4";

// 총 실 결제금액 링크
$link_totordprice_act_today = "./order_list_all_order.php?s_check=al&s_date=bank_date&search_start=".$curDate."&search_end=".$curDate."&s_prod=pn&search_prod=&paystate=A&ord_flag_all=4";
$link_totordprice_act_yesterday = "./order_list_all_order.php?s_check=al&s_date=bank_date&search_start=".$yesterday."&search_end=".$yesterday."&s_prod=pn&search_prod=&paystate=A&ord_flag_all=4";
$link_totordprice_act_week = "./order_list_all_order.php?s_check=al&s_date=bank_date&search_start=".$weekday."&search_end=".$curDate."&s_prod=pn&search_prod=&paystate=A&ord_flag_all=4";
$link_totordprice_act_mon = "./order_list_all_order.php?s_check=al&s_date=bank_date&search_start=".date("Ym")."01"."&search_end=".$curDate."&s_prod=pn&search_prod=&paystate=A&ord_flag_all=4";

// 총 환불 금액 링크 ( %5B%5D ==> [] )
$link_totordprice_repay_today = "./order_list_cancel_steprefund.php?tab=step44&oistep=1234&s_check=oc&search_fin_start=".$curDate."&search_fin_end=".$curDate."&paymethod[]=B&paymethod[]=CA&paymethod[]=VA&paymethod[]=OA&paymethod[]=ME&paymethod[]=QA&paymethod[]=YF&ord_flag=AA";
$link_totordprice_repay_yesterday = "./order_list_cancel_steprefund.php?tab=step44&oistep=1234&s_check=oc&search_fin_start=".$yesterday."&search_fin_end=".$yesterday."&paymethod[]=B&paymethod[]=CA&paymethod[]=VA&paymethod[]=OA&paymethod[]=ME&paymethod[]=QA&paymethod[]=YF&ord_flag=AA";
$link_totordprice_repay_week = "./order_list_cancel_steprefund.php?tab=step44&oistep=1234&s_check=oc&search_fin_start=".$weekday."&search_fin_end=".$curDate."&paymethod[]=B&paymethod[]=CA&paymethod[]=VA&paymethod[]=OA&paymethod[]=ME&paymethod[]=QA&paymethod[]=YF&ord_flag=AA";
$link_totordprice_repay_mon = "./order_list_cancel_steprefund.php?tab=step44&oistep=1234&s_check=oc&search_fin_start=".date("Ym")."01"."&search_fin_end=".$curDate."&paymethod[]=B&paymethod[]=CA&paymethod[]=VA&paymethod[]=OA&paymethod[]=ME&paymethod[]=QA&paymethod[]=YF&ord_flag=AA";

//if($_GET['point'] == "y") {
?>
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						    <div class="table_style01">
                            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                                <TR height='40' style="background-color:#f8f8f8;">
                                    <th style="text-align:center;">구분</th>
                                    <th style="text-align:center">오늘</th>
                                    <th style="text-align:center">어제</th>
                                    <th style="text-align:center">이번주</th>
                                    <th style="text-align:center">이번달</th>
                                </TR>
                                <TR height='40'>
                                    <th><span>총 주문 금액(건수)</span></th>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_today?>" target="_blank"><b><font size=4 color=orange><?=number_format($totordprice_today)."</font> 원 (".number_format($totordcnt_today)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_yesterday?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_yesterday)."</font> 원 (".number_format($totordcnt_yesterday)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_week?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_week)."</font> 원 (".number_format($totordcnt_week)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_mon?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_mon)."</font> 원 (".number_format($totordcnt_mon)." 건)"?></b></a></TD>
                                </TR>
                                <TR height='40'>
                                    <th><span>총 실 결제금액(건수)</span></th>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_act_today?>" target="_blank"><b><font size=4 color=orange><?=number_format($totordprice_act_today)."</font> 원 (".number_format($totordcnt_act_today)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_act_yesterday?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_act_yesterday)."</font> 원 (".number_format($totordcnt_act_yesterday)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_act_week?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_act_week)."</font> 원 (".number_format($totordcnt_act_week)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_act_mon?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_act_mon)."</font> 원 (".number_format($totordcnt_act_mon)." 건)"?></b></a></TD>
                                </TR>
                                <TR height='40'>
                                    <th><span>총 환불 금액(건수)</span></th>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_repay_today?>" target="_blank"><b><font size=4 color=orange><?=number_format($totordprice_repay_today)."</font> 원 (".number_format($totordcnt_repay_today)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_repay_yesterday?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_repay_yesterday)."</font> 원 (".number_format($totordcnt_repay_yesterday)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_repay_week?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_repay_week)."</font> 원 (".number_format($totordcnt_repay_week)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_repay_mon?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_repay_mon)."</font> 원 (".number_format($totordcnt_repay_mon)." 건)"?></b></a></TD>
                                </TR>
                                <TR height='2'>
                                    <th colspan=5></th>
                                </TR>
                                <TR height='40'>
                                    <th><span>총 매출</span></th>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size=4 color=orange><?=number_format($totordprice_act_today-$totordprice_repay_today)?></font> 원</b></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size=4 color=blue><?=number_format($totordprice_act_yesterday-$totordprice_repay_yesterday)?></font> 원</b></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size=4 color=blue><?=number_format($totordprice_act_week-$totordprice_repay_week)?></font> 원</b></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size=4 color=blue><?=number_format($totordprice_act_mon-$totordprice_repay_mon)?></font> 원</b></TD>
                                </TR>
                            </TABLE>
						    </div>
						</td>
					</tr>					
				    </table>
<?
//} 
/*
if($_GET['point'] == "n") {
?>
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						    <div class="table_style01">
                            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                                <TR height='40' style="background-color:#f8f8f8;">
                                    <th style="text-align:center;">구분</th>
                                    <th style="text-align:center">오늘</th>
                                    <th style="text-align:center">어제</th>
                                    <th style="text-align:center">이번주</th>
                                    <th style="text-align:center">이번달</th>
                                </TR>
                                <TR height='40'>
                                    <th><span>총 주문 금액(건수)</span></th>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_today?>" target="_blank"><b><font size=4 color=orange><?=number_format($totordprice_today)."</font> 원 (".number_format($totordcnt_today)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_yesterday?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_yesterday)."</font> 원 (".number_format($totordcnt_yesterday)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_week?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_week)."</font> 원 (".number_format($totordcnt_week)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_mon?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_mon)."</font> 원 (".number_format($totordcnt_mon)." 건)"?></b></a></TD>
                                </TR>
                                <TR height='40'>
                                    <th><span>총 실 결제금액(건수)</span></th>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_act_today?>" target="_blank"><b><font size=4 color=orange><?=number_format($totordprice_act_today)."</font> 원 (".number_format($totordcnt_act_today)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_act_yesterday?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_act_yesterday)."</font> 원 (".number_format($totordcnt_act_yesterday)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_act_week?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_act_week)."</font> 원 (".number_format($totordcnt_act_week)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_act_mon?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_act_mon)."</font> 원 (".number_format($totordcnt_act_mon)." 건)"?></b></a></TD>
                                </TR>
                                <TR height='40'>
                                    <th><span>총 환불 금액(건수)</span></th>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_repay_today?>" target="_blank"><b><font size=4 color=orange><?=number_format($totordprice_repay_today)."</font> 원 (".number_format($totordcnt_repay_today)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_repay_yesterday?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_repay_yesterday)."</font> 원 (".number_format($totordcnt_repay_yesterday)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_repay_week?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_repay_week)."</font> 원 (".number_format($totordcnt_repay_week)." 건)"?></b></a></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$link_totordprice_repay_mon?>" target="_blank"><b><font size=4 color=blue><?=number_format($totordprice_repay_mon)."</font> 원 (".number_format($totordcnt_repay_mon)." 건)"?></b></a></TD>
                                </TR>
                                <TR height='2'>
                                    <th colspan=5></th>
                                </TR>
                                <TR height='40'>
                                    <th><span>총 매출</span></th>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size=4 color=orange><?=number_format($totordprice_act_today-$totordprice_repay_today)?></font> 원</b></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size=4 color=blue><?=number_format($totordprice_act_yesterday-$totordprice_repay_yesterday)?></font> 원</b></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size=4 color=blue><?=number_format($totordprice_act_week-$totordprice_repay_week)?></font> 원</b></TD>
                                    <TD class="td_con1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size=4 color=blue><?=number_format($totordprice_act_mon-$totordprice_repay_mon)?></font> 원</b></TD>
                                </TR>
                            </TABLE>
						    </div>
						</td>
					</tr>					
				    </table>
<?
}
*/
?>
<?=$onload?>
</body>
</html>