<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sales_price_order_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


//$s_check    = $_POST["s_check"];
$search     = trim($_POST["search"]);
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$o2o_type		= $_POST["o2o_type"];

$search_start = $search_start?$search_start:date("Ym")."01";
$search_end = $search_end?$search_end:date("Ymd");
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

// 검색어
if(ord($search)) {
	$qry.= "AND ordercode like '%{$search}%' ";
    //$qry.="and	o.ordercode in ('2016032212123826082A', '2016032212210188149A') ";
}

// o2o구분
if(ord($o2o_type)) {
	$qry.= "AND delivery_type = '{$o2o_type}' ";
}
//echo "qry = ".$qry."<br>";

$t_price=0;

$subquery = "SELECT 	* 
                FROM
                (
            SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.bank_date) as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, op.delivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            WHERE   1=1 
            AND	    o.bank_date >= '{$search_s}' and o.bank_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('0')
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017081823011211354A','2017082911094138546A','2017120714063451079A')
			AND op.idx not in('18770','33418','33427','33428','33429','33627','33628','33689','33954','34043','34113','34214','34246','34262','34491','34538','34720','34741','34752','34821','34822','34888','34928','34395','35463','35474','35475','35058','35549','33100','33139','35749','50387','57757')
		   GROUP BY op.idx, o.ordercode
            UNION ALL 
           SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                     min(o.bank_date) as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    o.bank_date >= '{$search_s}' and o.bank_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND op.delivery_type IN ('2') 
            AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017081823011211354A','2017082911094138546A')
			AND op.idx not in('47616','47984','50387','57685')
			GROUP BY op.idx, o.ordercode
            UNION ALL 
           SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(op.deli_date) as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, op.delivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            WHERE   1=1 
            AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('1','2','3')
             AND op.deli_date IS NOT NULL
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017081823011211354A','2017082911094138546A')
           GROUP BY op.idx, o.ordercode
            UNION ALL 
           SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(op.deli_date) as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, op.delivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            WHERE   1=1 
            AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('0')
             AND op.deli_date IS NOT NULL
             AND o.ordercode = '2017071817141751724A'
             AND op.idx='18770'
           GROUP BY op.idx, o.ordercode
			UNION ALL 
			SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                     min(op.deli_date) as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND op.delivery_type IN ('0') 
			AND o.ordercode not in ('2017112917371324603A','2017121313333944418A','2017121615565474628A','2017121621464957404A','2017120517484927256A')
			AND op.idx not in('44020','50387','50420','50421','50395','48926','53070','54082','54071','54074','54075','54076','54078','54079','54080','54081','54883','54880','54879','57474','57570','57673','57714','55872','55892','59710','58984')
			GROUP BY op.idx, o.ordercode
   			UNION ALL
			SELECT 'refund' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, op.delivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
            WHERE   1=1 
            AND	    oc.cfindt >= '{$search_s}' and oc.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND	    op.op_step = 44 
             AND op.delivery_type IN ('0')
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017081823011211354A','2017082911094138546A')
			AND op.idx not in('18771')
			
			GROUP BY op.idx, o.ordercode, oc.cfindt
            UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND op.delivery_type IN ('2') 
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017081823011211354A','2017082911094138546A')
			AND op.idx not in('18771','47984','50387')
			GROUP BY op.idx, o.ordercode, cp.cfindt
            UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, op.delivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
            WHERE   1=1 
            AND	    oc.cfindt >= '{$search_s}' and oc.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND	    op.op_step = 44 
             AND op.delivery_type IN ('1','2','3')
             AND op.deli_date IS NOT NULL
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017071420243042524AX','2017081823011211354A','2017082911094138546A')
			AND op.idx not in('18771','57685')
            GROUP BY op.idx, o.ordercode, oc.cfindt
            UNION ALL 
			SELECT 'refund' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, max(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND op.delivery_type IN ('0')
			AND o.ordercode not in ('2017112917371324603A','2017120714063451079A','2017121313333944418A','2017121615565474628A')
			AND op.idx not in('31971','50387','52074','52069','54082','54071','54074','54075','54076','54078','54079','54080','54081','57474','57714','59710','58984')
			GROUP BY op.idx, o.ordercode, cp.cfindt
            UNION ALL 
			SELECT 'sale' as saletype, o.pg_ordercode as ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.regdt) as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, op.delivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            WHERE   1=1 
            AND	    o.regdt >= '{$search_s}' and o.regdt <= '{$search_e}' 
			AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND o.pg_ordercode in ('2017083111174852543A','2017083111223506903A','2017083111321061616A','2017083111331067302A','2017083111335751622A','2017083111394507861A','2017083111443924329A','2017083111445983565A','2017083111512002245A','2017083111515122264A','2017083112033721283A','2017083112034578475A','2017083112150427444A','2017083112391624208A','2017083112391624208A','2017083113015588821A','2017083113023006455A','2017083113054869828A','2017083113080585586A','2017083113081749203A'
			)
		   GROUP BY op.idx, o.pg_ordercode
            UNION ALL 
           SELECT 'sale' as saletype, o.pg_ordercode as ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201709011211000' as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, op.delivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            WHERE   1=1 
            AND	    '201709011211000' >= '{$search_s}' and '201709011211000' <= '{$search_e}' 
			AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('2')
             AND op.deli_date IS NOT NULL
			AND o.pg_ordercode in ('2017083111321061616A','2017083111335751622A')
           GROUP BY op.idx, o.pg_ordercode
			UNION ALL 
            SELECT 'refund' as saletype, o.pg_ordercode as ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.pg_ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND o.pg_ordercode in ('2017083111321061616A','2017083111335751622A','2017083111515122264A','2017083112033721283A','2017083113081749203A','2017083111174852543A','2017083111331067302A','2017083111223506903A','2017083111394507861A','2017083111443924329A','2017083111445983565A','2017083111512002245A','2017083112033721283A','2017083112034578475A','2017083112150427444A','2017083112391624208A','2017083113015588821A','2017083113023006455A','2017083113054869828A','2017083113080585586A','2017111914370326830A')
			GROUP BY op.idx, o.pg_ordercode, cp.cfindt
			UNION ALL 
            SELECT 'refund' as saletype, o.pg_ordercode as ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, max(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.pg_ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND o.pg_ordercode in ('2017083111321061616A','2017083111335751622A')
			GROUP BY op.idx, o.pg_ordercode, cp.cfindt
			UNION ALL 
			select 'sale' as saletype,a.ordercode as ordercode,op.idx as idx,o.id as id,
				o.sender_name as sender_name,o.paymethod as paymethod,o.oldordno as oldordno,o.is_mobile as is_mobile,
				a.regdt as cdt,
				op.productname as productname, 1 as cnt_prod,
				(op.price+op.option_price) * op.option_quantity as op_ordprice, op.coupon_price as op_coupon, 
				op.use_point as op_usepoint, op.use_epoint as op_useepoint, o.deli_price as o_deli_price, op.deli_price as op_deli_price, op.delivery_type 
				from 
				tblorderproduct_store_code a 
				LEFT join tblorderinfo o on a.ordercode=o.ordercode
				LEFT join tblorderproduct op on a.ordercode=op.ordercode
				WHERE 1=1
				AND	    a.regdt >= '{$search_s}' and a.regdt <= '{$search_e}' 
				AND a.regdt > '20171001000000'
				AND a.store_code='A1801B' 
				AND a.old_store_code=''
				AND o.ordercode not in('2017112917371324603A','2017120813325138412A','2017120813314141785A','2017121313333944418A','2017121615565474628A')
				AND op.idx not in('52074','52069','57474','57714','59710')
			UNION ALL 
		   SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(op.deli_date) as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, op.delivery_type 
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            WHERE   1=1 
            AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('3','4') 
            AND 	(op.op_step >= 0 and op.op_step < 45) 
             AND op.delivery_type IN ('0')
             AND op.deli_date IS NOT NULL
			AND op.idx in ('33418','33418','33427','33428','33429','33627','33628','33689','33954','34043','34113','34214','34246','34262','34491','34538','34720','34741','34752','34821','34822','34888','34928','34395','35463','35474','35475','35058','35549','33100','33139','35749')
		   GROUP BY op.idx, o.ordercode
            UNION ALL 
           SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201712192032000' as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    '201712192032000' >= '{$search_s}' and '201712192032000' <= '{$search_e}' 
			AND op.idx in('57685','57757')
			GROUP BY op.idx, o.ordercode
            UNION ALL
            SELECT 'refund' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201712202110000' as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    '201712202110000' >= '{$search_s}' and '201712202110000' <= '{$search_e}' 
			AND op.idx in ('57757')
			GROUP BY op.idx, o.ordercode, cp.cfindt
			UNION ALL 
           SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201712212110000' as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    '201712212110000' >= '{$search_s}' and '201712212110000' <= '{$search_e}' 
			AND op.idx in ('57685','57757')
		   GROUP BY op.idx, o.ordercode
            UNION ALL
            SELECT 'refund' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201712212110000' as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    '201712212110000' >= '{$search_s}' and '201712212110000' <= '{$search_e}' 
			AND op.idx in ('57685','57757')
			GROUP BY op.idx, o.ordercode, cp.cfindt
			UNION ALL 
           SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '20171222093800' as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    '20171222093800' >= '{$search_s}' and '20171222093800' <= '{$search_e}' 
			AND op.idx in ('57685','57757')
		   GROUP BY op.idx, o.ordercode
            UNION ALL
            SELECT 'refund' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '20171222094000' as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    '20171222094000' >= '{$search_s}' and '20171222094000' <= '{$search_e}' 
            AND op.delivery_type IN ('2') 
			AND op.idx in ('57685')
			GROUP BY op.idx, o.ordercode, cp.cfindt
			UNION ALL 
           SELECT 'sale' as saletype, o.ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '20171222184000' as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    '20171222184000' >= '{$search_s}' and '20171222184000' <= '{$search_e}' 
			AND op.idx in ('57757')
		   GROUP BY op.idx, o.ordercode
			UNION ALL
			SELECT 'refund' as saletype, o.ordercode as ordercode, op.idx, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt, 
                    min(op.productname) as productname, count(op.productname) as cnt_prod,
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(op.use_epoint) as op_useepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, '0' as delivery_type
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
            AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND o.ordercode in ('2017102610562741209A','2017112010593347698A')
			GROUP BY op.idx, o.ordercode, cp.cfindt
				) a 
				WHERE 1=1
				".$qry."
                 ORDER BY ordercode asc, idx asc, cdt asc, saletype desc
        ";
//            AND	    o.id = 'ikazeus'
// 2016-04-18 jhjeong 환불완료시의 수수료는 환불쪽의 쿠폰금액에 포함시켜달라고 함. by 조세진
// sum(op.coupon_price) as op_coupon ==> sum(op.coupon_price + oc.rfee) as op_coupon
?>

<?php
		$sql = $subquery;
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

        
        $sales = array();           // 전체 배열
        $tot_sale_ordprice = 0;     // 전체 결제 주문금액
        $tot_sale_ordqty = 0;     // 전체 결제 주문수량
        $tot_sale_coupon = 0;       // 전체 결제 쿠폰 사용 금액
        $tot_sale_usepoint = 0;     // 전체 결제 적립금 사용 금액
		$tot_sale_useepoint = 0;     // 전체 결제 E포인트 사용 금액
        $tot_sale_deliprice = 0;    // 전체 결제 배송비 금액
        $tot_sale_realprice = 0;    // 전체 결제 실결제 금액
        $tot_sale_realprice2 = 0;    // 전체 결제 실결제 금액(배송비제외)
        $tot_refund_ordprice = 0;   // 전체 환불 주문금액
        $tot_refund_ordqty = 0;   // 전체 환불 주문수량
        $tot_refund_coupon = 0;     // 전체 환불 쿠폰 사용 금액
        $tot_refund_usepoint = 0;   // 전체 환불 적립금 사용 금액
		$tot_refund_useepoint = 0;   // 전체 환불 E포인트 사용 금액
        $tot_refund_deliprice = 0;  // 전체 환불 배송비 금액
        $tot_refund_realprice = 0;  // 전체 결제 실결제 금액
        $tot_refund_realprice2 = 0;  // 전체 결제 실결제 금액(배송비제외)

		while($row=pmysql_fetch_object($result)) {

            $cdt = $row->cdt;
            $saletype = $row->saletype;
            $ordercode = $row->ordercode;
            $idx = $row->idx;
            $sender_name = $row->sender_name;
            $productname = $row->productname;
            $cnt_prod = $row->cnt_prod;
            if($cnt_prod > 1) $productname = $productname." 외 ".($cnt_prod-1)."건";
            $delivery_type = $arrChainCode[$row->delivery_type];

            $paymethod = $row->paymethod;
            $ordprice = $row->op_ordprice;
            $coupon = $row->op_coupon;
            $usepoint = $row->op_usepoint;
			$useepoint = $row->op_useepoint;
            $op_deliprice = $row->op_deli_price;
            $real_price = $ordprice - $coupon - $usepoint - $useepoint + $op_deliprice;
            $real_price2 = $ordprice - $coupon - $usepoint - $useepoint;

			
			$ordqty='1';
            if($saletype == "sale") {
                $tot_sale_ordprice += $ordprice;
                $tot_sale_ordqty += $ordqty;
                $tot_sale_coupon += $coupon;
                $tot_sale_usepoint += $usepoint;
				$tot_sale_useepoint += $useepoint;
                $tot_sale_deliprice += $op_deliprice;
                $tot_sale_realprice += $real_price;;
                $tot_sale_realprice2 += $real_price2;

            } else if($saletype == "refund") {
                $tot_refund_ordprice += $ordprice;
                $tot_refund_ordqty += $ordqty;
                $tot_refund_coupon += $coupon;
                $tot_refund_usepoint += $usepoint;
				$tot_refund_useepoint += $useepoint;
                $tot_refund_deliprice += $op_deliprice;
                $tot_refund_realprice += $real_price;;
                $tot_refund_realprice2 += $real_price2;
            }

            $sales[$cnt][$cdt][$ordercode][$idx]['sender_name'] = $sender_name;
            $sales[$cnt][$cdt][$ordercode][$idx]['productname'] = $productname;
            $sales[$cnt][$cdt][$ordercode][$idx]['delivery_type'] = $delivery_type;
            $sales[$cnt][$cdt][$ordercode][$idx][$saletype]['paymethod'] = $paymethod;
            $sales[$cnt][$cdt][$ordercode][$idx][$saletype]['ordqty'] = $ordqty;
            $sales[$cnt][$cdt][$ordercode][$idx][$saletype]['ordprice'] = $ordprice;
            $sales[$cnt][$cdt][$ordercode][$idx][$saletype]['coupon'] = $coupon;
            $sales[$cnt][$cdt][$ordercode][$idx][$saletype]['usepoint'] = $usepoint;
			$sales[$cnt][$cdt][$ordercode][$idx][$saletype]['useepoint'] = $useepoint;
            $sales[$cnt][$cdt][$ordercode][$idx][$saletype]['op_deliprice'] = $op_deliprice;
            $sales[$cnt][$cdt][$ordercode][$idx][$saletype]['real_price'] = $real_price;
            $sales[$cnt][$cdt][$ordercode][$idx][$saletype]['real_price2'] = $real_price2;

			$cnt++;
		}

		pmysql_free_result($result);

        //$t_count = count($sales);
        $t_count = $cnt;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=1 cellpadding=0 cellspacing=0 width=100%>
				<col width=50></col>
				<col width=120></col>
				<col width=140></col>
				<col width=120></col>
				<col width=250></col>
				<col width=120></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<input type=hidden name=chkordercode>
			
				<TR bgcolor="#d1d1d1">
					<td rowspan=2><b>번호<b></td>
                    <td rowspan=2><b>일자<b></td>
                    <td rowspan=2><b>주문번호<b></td>
                    <td rowspan=2><b>주문자<b></td>
                    <td rowspan=2><b>상품명<b></td>
                    <td rowspan=2><b>O2O구분<b></td>
                    <td colspan=8><b>구매<b></td>
                    <td colspan=8><b>환불<b></td>
                    <td colspan=7><b>순매출<b></td>
				</TR>
                <TR bgcolor="#d1d1d1">
                    <td><b>결제수단<b></td>
                    <td><b>수량<b></td>
                    <td><b>금액<b></td>
                    <td><b>쿠폰할인<b></td>
                    <td><b>포인트<b></td>
					<td><b>E포인트<b></td>
                    <td><b>실결제금액<br>(배송비포함)<b></td>
                    <td><b>실결제금액<br>(배송비제외)<b></td>

                    <td><b>환불수단<b></td>
                    <td><b>수량<b></td>
                    <td><b>금액<b></td>
                    <td><b>쿠폰할인<b></td>
                    <td><b>포인트<b></td>
					<td><b>E포인트<b></td>
                    <td><b>실결제금액<br>(배송비포함)<b></td>
                    <td><b>실결제금액<br>(배송비제외)<b></td>

                    <td><b>수량<b></td>
                    <td><b>금액<b></td>
                    <td><b>쿠폰할인<b></td>
                    <td><b>포인트<b></td>
					<td><b>E포인트<b></td>
                    <td><b>실결제금액<br>(배송비포함)<b></td>
                    <td><b>실결제금액<br>(배송비제외)<b></td>
                </TR>
<?
		$colspan=22;
        $i = 0;
        $arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰",/*"R"=>"적립금",*/"Y"=>"PAYCO");
        foreach($sales as $k => $v) {

            foreach($v as $d => $v) {
				foreach($v as $ordercode => $v) {

					foreach($v as $idx => $v) {

					if($i%2) $thiscolor="#ffeeff";
					else $thiscolor="#FFFFFF";

					$i++;

					$date = substr($d, 0, 4)."/".substr($d, 4, 2)."/".substr($d, 6, 2)." (".substr($d, 8, 2).":".substr($d, 10, 2).")";
					//exdebug($v);
?>

			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=$i?></td>
                    <td><?=$date?></td>
                    <td><?=$ordercode?> (<?=$idx?>)</td>
                    <td><?=$v['sender_name']?></td>
                    <td><?=$v['productname']?></td>
                    <td><?=$v['delivery_type']?></td>
                    <td><?=$arpm[$v['sale']['paymethod'][0]]?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordqty'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['sale']['useepoint'])?></td>
                    <!-- <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice'])?></td> -->
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price2'])?></td>
                    <td><?=$v['refund']['paymethod'][0]=="V"?"무통장":$arpm[$v['refund']['paymethod'][0]]?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['ordqty'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['refund']['useepoint'])?></td>
                    <!-- <td style="text-align:right;"><?=number_format($v['refund']['op_deliprice'])?></td> -->
                    <td style="text-align:right;"><?=number_format($v['refund']['real_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['real_price2'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordqty']-$v['refund']['ordqty'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice']-$v['refund']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['coupon']-$v['refund']['coupon'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['usepoint']-$v['refund']['usepoint'])?></td>
					<td style="text-align:right;"><?=number_format($v['sale']['useepoint']-$v['refund']['useepoint'])?></td>
                    <!-- <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice']-$v['refund']['op_deliprice'])?></td> -->
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price']-$v['refund']['real_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['real_price2']-$v['refund']['real_price2'])?></td>
                </tr>
<?
					}
				}
            }
        }
?>
			    <tr bgcolor="#d1d1d1" onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='#d1d1d1'">
                    <td><b>합계</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordqty)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_sale_useepoint)?></b></td>
                    <!-- <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice)?></b></td> -->
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice2)?></b></td>

                    <td></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_ordqty)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_refund_useepoint)?></b></td>
                    <!-- <td style="text-align:right;"><b><?=number_format($tot_refund_deliprice)?></b></td> -->
                    <td style="text-align:right;"><b><?=number_format($tot_refund_realprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_realprice2)?></b></td>

                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordqty - $tot_refund_ordqty)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice - $tot_refund_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_coupon - $tot_refund_coupon)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_usepoint - $tot_refund_usepoint)?></b></td>
					<td style="text-align:right;"><b><?=number_format($tot_sale_useepoint - $tot_refund_useepoint)?></b></td>
                    <!-- <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice - $tot_refund_deliprice)?></b></td> -->
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice - $tot_refund_realprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_realprice2 - $tot_refund_realprice2)?></b></td>
                </tr>
				</TABLE>
</body>
</html>