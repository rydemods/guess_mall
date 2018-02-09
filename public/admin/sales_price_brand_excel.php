<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sales_price_brand_excel_".date("Ymd",$CurrentTime).".xls"); 
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
//$search     = trim($_POST["search"]);
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$s_prod         = $_POST["s_prod"];
$search_prod    = $_POST["search_prod"];
$sel_vender     = $_POST["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_POST["brandname"];  // 벤더이름 검색

$search_start = $search_start?$search_start:date("Ym")."01";
$search_end = $search_end?$search_end:date("Ymd");
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $qry.= " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $qry.= " and v.vender = ".$sel_vender."";
}
//echo "qry = ".$qry."<br>";

$t_price=0;

function option_slice2( $content, $option_type = '0' ){
    $tmp_content = '';
    if( $option_type == '0' ) {
        $tmp_content = explode( chr(30), $content );
    } else {
        $tmp_content = explode( '@#', $content );
    }
    
    return $tmp_content;

}

/*
$subquery = "
            SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.bank_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname, min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            WHERE   1=1 
            AND	    o.bank_date >= '{$search_s}' and o.bank_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            ".$qry." 
            GROUP BY o.ordercode, op.productcode, op.idx
            UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname, min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            WHERE   1=1 
            AND	    oc.cfindt >= '{$search_s}' and oc.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND	    op.op_step = 44 
            ".$qry." 
            GROUP BY o.ordercode, op.productcode, op.idx, oc.cfindt
        ";*/
		$subquery = "SELECT 	* 
                FROM
                (
            SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.bank_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            WHERE   1=1 
			AND	    o.bank_date >= '{$search_s}' and o.bank_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('0')
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017081823011211354A','2017082911094138546A','2017120714063451079A')
			AND op.idx not in('18770','33418','33427','33428','33429','33627','33628','33689','33954','34043','34113','34214','34246','34262','34491','34538','34720','34741','34752','34821','34822','34888','34928','34395','35463','35474','35475','35058','35549','33100','33139','35749','50387','57757')
		   GROUP BY op.idx, o.ordercode
            UNION ALL 
			   SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.bank_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
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
            SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.deli_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            WHERE   1=1 
			AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('1','2','3')
             AND op.deli_date IS NOT NULL
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017081823011211354A','2017082911094138546A')
		   GROUP BY op.idx, o.ordercode
            UNION ALL 
            SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(op.deli_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
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
            SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(op.deli_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
			JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
			JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            WHERE   1=1 
			AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('0')
			AND o.ordercode not in ('2017112917371324603A','2017121313333944418A','2017121615565474628A','2017121621464957404A','2017120517484927256A')
			AND op.idx not in('44020','50387','50420','50421','50395','48926','53070','54082','54071','54074','54075','54076','54078','54079','54080','54081','54883','54880','54879','57474','57570','57673','57714','55872','55892','59710')
		   GROUP BY op.idx, o.ordercode
			UNION ALL 
            SELECT 'sale' as saletype, o.pg_ordercode as ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.regdt) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            WHERE   1=1 
            AND	    o.regdt >= '{$search_s}' and o.regdt <= '{$search_e}' 
			AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND op.idx not in('31971')
			AND o.pg_ordercode in ('2017083111174852543A','2017083111223506903A','2017083111321061616A','2017083111331067302A','2017083111335751622A','2017083111394507861A','2017083111443924329A','2017083111445983565A','2017083111512002245A','2017083111515122264A','2017083112033721283A','2017083112034578475A','2017083112150427444A','2017083112391624208A','2017083112391624208A','2017083113015588821A','2017083113023006455A','2017083113054869828A','2017083113080585586A','2017083113081749203A'
			)
		   GROUP BY op.idx, o.pg_ordercode
			UNION ALL 
            SELECT 'sale' as saletype, o.pg_ordercode as ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                   '201709011211000' as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
			WHERE   1=1 
            AND	    '201709011211000' >= '{$search_s}' and '201709011211000' <= '{$search_e}' 
			AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('2')
             AND op.deli_date IS NOT NULL
			AND o.pg_ordercode in ('2017083111321061616A','2017083111335751622A')
		   GROUP BY op.idx, o.pg_ordercode
			UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname, count(op.productname) as cnt_prod, min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
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
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
             WHERE   1=1 
			AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND op.delivery_type IN ('2') 
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A','2017081823011211354A','2017082911094138546A')
			AND op.idx not in('18771','47984','50387')
			GROUP BY op.idx, o.ordercode, cp.cfindt
			UNION ALL
			SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    oc.cfindt as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname, count(op.productname) as cnt_prod, min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + oc.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblorder_cancel oc on o.ordercode = oc.ordercode and op.oc_no = oc.oc_no 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
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
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
             WHERE   1=1 
			AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
            AND op.delivery_type IN ('0')
			AND o.ordercode not in ('2017112917371324603A','2017120714063451079A','2017121313333944418A','2017121615565474628A')
			AND op.idx not in('31971','50387','52074','52069','54082','54071','54074','54075','54076','54078','54079','54080','54081','57474','57714','59710')
			GROUP BY op.idx, o.ordercode, cp.cfindt
			UNION ALL 
            SELECT 'refund' as saletype, o.pg_ordercode as ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
			JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.pg_ordercode = cp.ordercode and op.idx = cp.idx 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
	    WHERE   1=1 
			AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND o.pg_ordercode in ('2017083111321061616A','2017083111335751622A','2017083111515122264A','2017083112033721283A','2017083113081749203A','2017083111174852543A','2017083111331067302A','2017083111223506903A','2017083111394507861A','2017083111443924329A','2017083111445983565A','2017083111512002245A','2017083112033721283A','2017083112034578475A','2017083112150427444A','2017083112391624208A','2017083113015588821A','2017083113023006455A','2017083113054869828A','2017083113080585586A','2017111914370326830A')
			GROUP BY op.idx, o.pg_ordercode, cp.cfindt
			UNION ALL 
			SELECT 'refund' as saletype, o.pg_ordercode as ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice,sum(op.coupon_price + cp.rfee) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
			JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
			JOIN    (select ordercode, idx, max(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.pg_ordercode = cp.ordercode and op.idx = cp.idx
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
	    WHERE   1=1 
			AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND o.pg_ordercode in ('2017083111321061616A','2017083111335751622A')
			GROUP BY op.idx, o.pg_ordercode, cp.cfindt
			UNION ALL 
            SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    min(o.deli_date) as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            WHERE   1=1 
            AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('3','4') 
            AND 	(op.op_step >= 0 and op.op_step < 45) 
             AND op.delivery_type IN ('0')
             AND op.deli_date IS NOT NULL
			AND op.idx in ('33418','33418','33427','33428','33429','33627','33628','33689','33954','34043','34113','34214','34246','34262','34491','34538','34720','34741','34752','34821','34822','34888','34928','34395','35463','35474','35475','35058','35549','33100','33139','35749')
		   GROUP BY op.idx, o.ordercode
			UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
             WHERE   1=1 
            AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND o.ordercode in ('2017102610562741209A','2017112010593347698A')
			GROUP BY op.idx, o.ordercode, cp.cfindt
			UNION ALL 
            SELECT 'sale' as saletype, a.ordercode as ordercode, o.id as id, o.sender_name as sender_name, o.paymethod as paymethod, 
                    o.oldordno as oldordno, is_mobile as is_mobile, 
                    a.regdt as cdt, o.oi_step1 as oi_step1, o.oi_step2 as oi_step2, op.op_step as op_step, 
                    op.productcode, op.productname as productname,1 as cnt_prod,  op.opt1_name as opt1_name, op.opt2_name as opt2_name, 
                    op.text_opt_subject as text_opt_subject, op.text_opt_content as text_opt_content, op.option_price_text as option_price_text, 
                    op.vender as vender, v.brandname as brandname, 
                    op.price as price, op.option_price as option_price, op.option_quantity as option_quantity, 
                    (op.price+op.option_price) * op.option_quantity as op_ordprice, op.coupon_price as op_coupon, 
                    op.use_point as op_usepoint, o.deli_price as o_deli_price, op.deli_price as op_deli_price, 
                    op.rate as rate, op.idx, op.option_type as option_type, 
                    vi.rate as v_rate, ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) as supply_price, 
                    case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                     as v_supply_price
				from 
				tblorderproduct_store_code a 
				LEFT join tblorderinfo o on a.ordercode=o.ordercode
				LEFT join tblorderproduct op on a.ordercode=op.ordercode
            LEFT JOIN    tblproductbrand v on op.vender = v.vender 
            LEFT JOIN    tblvenderinfo vi on op.vender = vi.vender 
            WHERE   1=1 
				AND	    a.regdt >= '{$search_s}' and a.regdt <= '{$search_e}' 
				AND a.regdt > '20171001000000'
				AND a.store_code='A1801B' 
				AND a.old_store_code=''
				AND o.ordercode not in('2017112917371324603A','2017120813325138412A','2017120813314141785A','2017121313333944418A','2017121615565474628A')
				AND op.idx not in('52074','52069','57474','57714','59710')
            UNION ALL 
			   SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201712192032000' as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
			WHERE   1=1 
            AND	    '201712192032000' >= '{$search_s}' and '201712192032000' <= '{$search_e}' 
			AND op.idx in('57685','57757')
			GROUP BY op.idx, o.ordercode
			UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201712202110000' as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
             WHERE   1=1 
            AND	    '201712202110000' >= '{$search_s}' and '201712202110000' <= '{$search_e}' 
			AND op.idx in ('57757')
			GROUP BY op.idx, o.ordercode, cp.cfindt
            UNION ALL 
			   SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201712212110000' as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
			WHERE   1=1 
            AND	    '201712212110000' >= '{$search_s}' and '201712212110000' <= '{$search_e}' 
			AND op.idx in ('57685','57757')
		   GROUP BY op.idx, o.ordercode
			UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '201712212110000' as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
             WHERE   1=1 
            AND	    '201712212110000' >= '{$search_s}' and '201712212110000' <= '{$search_e}' 
			AND op.idx in ('57685','57757')
			GROUP BY op.idx, o.ordercode, cp.cfindt
            UNION ALL 
			   SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '20171222093800' as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
			WHERE   1=1 
            AND	    '20171222093800' >= '{$search_s}' and '20171222093800' <= '{$search_e}' 
			AND op.idx in ('57685','57757')
		   GROUP BY op.idx, o.ordercode
			UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '20171222094000' as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
             WHERE   1=1 
            AND	    '20171222094000' >= '{$search_s}' and '20171222094000' <= '{$search_e}' 
            AND op.delivery_type IN ('2') 
			AND op.idx in ('57685')
			GROUP BY op.idx, o.ordercode, cp.cfindt
            UNION ALL 
			   SELECT 'sale' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    '20171222184000' as cdt, min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
			FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
			WHERE   1=1 
            AND	    '20171222184000' >= '{$search_s}' and '20171222184000' <= '{$search_e}' 
			AND op.idx in ('57757')
		   GROUP BY op.idx, o.ordercode
			UNION ALL 
            SELECT 'refund' as saletype, o.ordercode, min(o.id) as id, min(o.sender_name) as sender_name, min(o.paymethod) as paymethod, 
                    min(o.oldordno) as oldordno, min(is_mobile) as is_mobile, 
                    cp.cfindt as cdt,  min(o.oi_step1) as oi_step1, min(o.oi_step2) as oi_step2, min(op.op_step) as op_step, 
                    op.productcode, min(op.productname) as productname,count(op.productname) as cnt_prod,  min(op.opt1_name) as opt1_name, min(op.opt2_name) as opt2_name, 
                    min(op.text_opt_subject) as text_opt_subject, min(op.text_opt_content) as text_opt_content, min(op.option_price_text) as option_price_text, 
                    min(op.vender) as vender, min(v.brandname) as brandname, 
                    min(op.price) as price, min(op.option_price) as option_price, min(op.option_quantity) as option_quantity, 
                    sum( (op.price+op.option_price) * op.option_quantity) as op_ordprice, sum(op.coupon_price) as op_coupon, 
                    sum(op.use_point) as op_usepoint, sum(o.deli_price) as o_deli_price, sum(op.deli_price) as op_deli_price, 
                    min(op.rate) as rate, op.idx, min(op.option_type) as option_type, 
                    min(vi.rate) as v_rate, sum( ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) ) as supply_price, 
                    sum(case when op.rate = 0   then ((op.price+op.option_price) * op.option_quantity) * (1 - (vi.rate/100::float)) 
                                                else ((op.price+op.option_price) * op.option_quantity) * (1 - (op.rate/100::float)) end
                    ) as v_supply_price
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            JOIN    (select ordercode, idx, min(regdt) as cfindt, 0 as rfee from tblorderproduct_store_change group by ordercode, idx) cp on o.ordercode = cp.ordercode and op.idx = cp.idx 
            JOIN    tblvenderinfo vi on op.vender = vi.vender 
             WHERE   1=1 
            AND	    cp.cfindt >= '{$search_s}' and cp.cfindt <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
			AND o.ordercode in ('2017102610562741209A','2017112010593347698A')
			GROUP BY op.idx, o.ordercode, cp.cfindt
				) c
				WHERE 1=1
				".$qry."
                 ORDER BY ordercode asc, idx asc, cdt asc
        ";
//            AND	    o.id = 'ikazeus'
// 2016-04-18 jhjeong 환불완료시의 수수료는 환불쪽의 쿠폰금액에 포함시켜달라고 함. by 조세진
// sum(op.coupon_price) as op_coupon ==> sum(op.coupon_price + oc.rfee) as op_coupon
?>

<?php
		$sql = "SELECT 	brandname, saletype, count(ordercode) as cnt_ord, sum(cnt_prod) as cnt_prod, 
                        sum(ordprice) as ordprice, sum(coupon) as coupon, sum(usepoint) as usepoint, sum(o_deli_price) as o_deliprice, sum(op_deli_price) as op_deliprice, 
                        sum(supply_price) as supply_price, sum(v_supply_price) as v_supply_price 
                FROM
                    (
                        SELECT 	brandname, saletype, min(a.ordercode) as ordercode, count(a.productcode) as cnt_prod, 
                                sum(op_ordprice) as ordprice, sum(op_coupon) as coupon, sum(op_usepoint) as usepoint, sum(o_deli_price) as o_deli_price, sum(op_deli_price) as op_deli_price, 
                                sum(supply_price) as supply_price, sum(v_supply_price) as v_supply_price 
                        FROM (
                                ".$subquery."
                        ) a 
                        GROUP BY brandname, saletype, ordercode 
                ) b 
                GROUP BY brandname, saletype 
                ORDER BY brandname asc, saletype asc  
                ";
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

        $sales = array();           // 전체 배열
        $tot_sale_cnt_ord = 0;      // 전체 결제 주문 수량
        $tot_sale_cnt_prod = 0;     // 전체 결제 상품 수량
        $tot_sale_ordprice = 0;     // 전체 결제 주문금액
        $tot_sale_supply_price = 0; // 전체 결제 공급가금액
        $tot_sale_deliprice = 0;    // 전체 결제 배송비 금액

        $tot_refund_cnt_ord = 0;    // 전체 환불 주문 수량
        $tot_refund_cnt_prod = 0;   // 전체 환불 상품 수량
        $tot_refund_ordprice = 0;   // 전체 환불 주문금액
        $tot_refund_supply_price = 0;   // 전체 환불 공급가금액
        $tot_refund_deliprice = 0;  // 전체 환불 배송비 금액

		while($row=pmysql_fetch_object($result)) {

            //if($row->saletype == "refund") $minus = -1;
            //else $minus = 1;

            $brandname = $row->brandname;
            $saletype = $row->saletype;
            $cnt_ord = $row->cnt_ord;
            $cnt_prod = $row->cnt_prod;
            $ordprice = $row->ordprice;
            $supply_price = $row->v_supply_price;
            $op_deliprice = $row->op_deliprice;

            if($saletype == "sale") {
                $tot_sale_cnt_ord += $cnt_ord;
                $tot_sale_cnt_prod += $cnt_prod;
                $tot_sale_ordprice += $ordprice;
                $tot_sale_supply_price += $supply_price;
                $tot_sale_deliprice += $op_deliprice;

            } else if($saletype == "refund") {
                $tot_refund_cnt_ord += $cnt_ord;
                $tot_refund_cnt_prod += $cnt_prod;
                $tot_refund_ordprice += $ordprice;
                $tot_refund_supply_price += $supply_price;
                $tot_refund_deliprice += $op_deliprice;
            }

            $sales[$brandname][$saletype]['cnt_ord'] = $cnt_ord;
            $sales[$brandname][$saletype]['cnt_prod'] = $cnt_prod;
            $sales[$brandname][$saletype]['ordprice'] = $ordprice;
            $sales[$brandname][$saletype]['supply_price'] = $supply_price;
            $sales[$brandname][$saletype]['op_deliprice'] = $op_deliprice;

			$cnt++;
		}

		pmysql_free_result($result);

        $t_count = count($sales);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=1 cellpadding=0 cellspacing=0 width=100%>
				<col width=200></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<input type=hidden name=chkordercode>
			
				<TR align=center>
					<td rowspan=2><b>브랜드별<b></td>
                    <td rowspan=2><b>주문건수<b></td>
                    <td rowspan=2><b>주문품목수<b></td>
                    <td colspan=3><b>판매<b></td>
                    <td rowspan=2><b>환불건수<b></td>
                    <td rowspan=2><b>환불품목수<b></td>
                    <td colspan=3><b>환불<b></td>
                    <td colspan=3><b>순매출<b></td>
                    <td rowspan=2><b>정산금액<b></td>
				</TR>
                <TR align=center>
                    <td><b>상품구매금액<b></td>
                    <td><b>공급가<b></td>
                    <td><b>배송비<b></td>

                    <td><b>상품환불금액<b></td>
                    <td><b>공급가<b></td>
                    <td><b>배송비<b></td>

                    <td><b>상품구매금액<b></td>
                    <td><b>마진제외<b></td>
                    <td><b>배송비<b></td>
                </TR>
<?
		$colspan=20;
        $i = 0;
        foreach($sales as $k => $v) {

            if($i%2) $thiscolor="#ffeeff";
            else $thiscolor="#FFFFFF";
?>

			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=$k?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['cnt_ord'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['cnt_prod'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['supply_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice'])?></td>

                    <td style="text-align:right;"><?=number_format($v['refund']['cnt_ord'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['cnt_prod'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['supply_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['refund']['op_deliprice'])?></td>

                    <td style="text-align:right;"><?=number_format($v['sale']['ordprice']-$v['refund']['ordprice'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['supply_price']-$v['refund']['supply_price'])?></td>
                    <td style="text-align:right;"><?=number_format($v['sale']['op_deliprice']-$v['refund']['op_deliprice'])?></td>

                    <td style="text-align:right;"><?=number_format($v['sale']['supply_price']-$v['refund']['supply_price'] + $v['sale']['op_deliprice']-$v['refund']['op_deliprice'])?></td>
                </tr>
<?
            $i++;
        }
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><b>합계</b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_cnt_ord)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_cnt_prod)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_supply_price)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice)?></b></td>

                    <td style="text-align:right;"><b><?=number_format($tot_refund_cnt_ord)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_cnt_prod)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_supply_price)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_refund_deliprice)?></b></td>

                    <td style="text-align:right;"><b><?=number_format($tot_sale_ordprice - $tot_refund_ordprice)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_supply_price - $tot_refund_supply_price)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($tot_sale_deliprice - $tot_refund_deliprice)?></b></td>

                    <td style="text-align:right;"><b><?=number_format($tot_sale_supply_price - $tot_refund_supply_price + $tot_sale_deliprice - $tot_refund_deliprice)?></b></td>
                </tr>
				</TABLE>
</body>
</html>