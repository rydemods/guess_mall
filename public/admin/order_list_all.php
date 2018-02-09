<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

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

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));
$period[4] = date("Y-m-d",strtotime('-1 year'));

################## 배송업체 쿼리 ################
$dvcode = '';
$ref_qry="select code, company_name from tbldelicompany order by company_name";
$ref1_result=pmysql_query($ref_qry);
#########################################################


$orderby    = $_GET["orderby"];
if(ord($orderby)==0) $orderby = "DESC";

$s_check    = $_GET["s_check"];
$search     = trim($_GET["search"]);
$s_date     = $_GET["s_date"];
if(ord($s_date)==0) $s_date = "ordercode";
if(!preg_match("/^(bank_date|deli_date|ordercode|delivery_end_date)$/", $s_date)) {
	$s_date = "ordercode";
}
$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];
$s_prod         = $_GET["s_prod"];
$search_prod    = $_GET["search_prod"];
$staff_order    = $_GET["staff_order"]?$_GET["staff_order"]:"A"; //스테프관련 추가 (2016.05.11 - 김재수)
$dvcode         = $_GET["dvcode"];
$oistep1        = $_GET["oistep1"];
$oi_type        = $_GET["oi_type"];
$paystate       = $_GET["paystate"]?$_GET["paystate"]:"A";
$paymethod      = $_GET["paymethod"];
$delivery_type      = $_GET["delivery_type"];
$selected[delivery_type]      = $delivery_type;
$ord_flag       = $_GET["ord_flag"]; // 유입경로

// 상품구분
$pr_select = $_GET["pr_select"];

// 경매 주문 구분
$auction_chk = $_GET["auction_chk"];

if ($oistep1[0] == '' && $oi_type[0] == '') {
	$oistep1_def=array("0","1","2","3","4");
	foreach($oistep1_def as $k => $v) $oistep1[$k] = $v;
	$oi_type_def=array("44","67","61","62","68","63","64","65","66");
	foreach($oi_type_def as $k => $v) $oi_type[$k] = $v;
}

if ($paymethod[0] == '') {
	foreach(array_keys($arpm) as $k => $v) {
		$paymethod[$k] = $v;
	}
}

if ($ord_flag[0] == '') {
	$ord_flag_def=array("PC","MO","AP");
	foreach($ord_flag_def as $k => $v) $ord_flag[$k] = $v;
}

if(is_array($oistep1)) $oistep1 = implode(",",$oistep1);
if(is_array($oi_type)) $oi_type = implode(",",$oi_type);
if(is_array($paymethod)) $paymethod = implode("','",$paymethod);
if(is_array($ord_flag)) $ord_flag = implode("','",$ord_flag);

$oistep1_arr   = explode(",",$oistep1);
$oi_type_arr   = explode(",",$oi_type);
$paymethod_arr = explode("','",$paymethod);
$ord_flag_arr  = explode("','",$ord_flag);

$sel_vender    = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$sel_vender2    = $_GET["sel_vender2"];  // 벤더 선택값으로 검색
$brandname     = $_GET["brandname"];  // 벤더이름 검색


//foreach($oistep1 as $k => $v) $oistep1[$k] = (int)$v;
//foreach($oi_type as $k => $v) $oi_type[$k] = (int)$v;

$selected[s_check][$s_check]    = 'selected';
$selected[s_date][$s_date]      = 'selected';
$selected[s_prod][$s_prod]      = 'selected';
$selected[staff_order][$staff_order] = 'checked'; //스테프관련 추가 (2016.05.11 - 김재수)
$selected[dvcode][$dvcode]      = 'selected';
$selected[paystate][$paystate]  = 'checked';

/*
$type = $_GET["type"];
$ordercodes = $_GET["ordercodes"];
$deli_gbn = $_GET["deli_gbn"];
*/

$search_start=$search_start?$search_start:$period[1];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

/*$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}*/

// 기본 검색 조건
$qry_from = "(
					SELECT op.*, p.prodcode, p.colorcode, p.season FROM tblorderproduct op LEFT JOIN tblproduct p 
					ON op.productcode=p.productcode
					) a ";
$qry_from.= " join tblorderinfo b on a.ordercode = b.ordercode ";
$qry.= "WHERE 1=1 ";
// 기간선택 조건
if ($search_s != "" || $search_e != "") {
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND b.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND b.{$s_date}>='{$search_s}' AND b.{$s_date} <='{$search_e}' ";
	}
}

if( $s_date == 'delivery_end_date' ){
    $qry_from .= "
        left join ( select idx, step_next, regdt as logdt,  row_number() over( partition by idx order by opl_no desc ) as rn from tblorderproduct_log ) as log
            on ( a.idx = log.idx and log.rn = 1 and a.op_step = log.step_next and log.step_next = 4 )
    ";
}

// 기본옵션만 검색 (2016-03-08 김재수 막음 - 추가옵션도 있어서..)
//$qry.= "AND a.option_type = 0 ";

// 검색어
if(ord($search)) {
	/*
	 if($s_check=="oc") $qry.= "AND a.ordercode like '%{$search}%' ";
	else if($s_check=="dv") $qry.= "AND a.deli_num = '{$search}' ";
	else if($s_check=="on") $qry.= "AND b.sender_name = '{$search}' ";
	else if($s_check=="oi") $qry.= "AND b.id = '{$search}' ";
	else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
	else if($s_check=="op") $qry.= "AND b.ip = '{$search}' ";
	else if($s_check=="sn") $qry.= "AND b.bank_sender = '{$search}' ";
	else if($s_check=="rn") $qry.= "AND b.receiver_name = '{$search}' ";
	else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
	else if($s_check=="ra") $qry.= "AND b.receiver_addr like '%{$search}%' ";
	else if($s_check=="nm") $qry.= "AND (b.sender_name = '{$search}' OR b.bank_sender = '{$search}' OR b.receiver_name = '{$search}') ";
	else if($s_check=="al") {
        $or_qry[] = " a.ordercode like '%{$search}%' ";
        //$or_qry[] = " a.deli_num = '{$search}' ";
        $or_qry[] = " b.sender_name = '{$search}' ";
        $or_qry[] = " b.id = '{$search}' ";
        $or_qry[] = " replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
        $or_qry[] = " b.ip = '{$search}' ";
        $or_qry[] = " b.bank_sender = '{$search}' ";
        $or_qry[] = " b.receiver_name = '{$search}' ";
        $or_qry[] = " replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
        $or_qry[] = " b.receiver_addr like '%{$search}%' ";
        $qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
    }
	*/ 
	$search = trim($search);
	$temp_search = explode("\r\n", $search);
	$cnt = count($temp_search);
	
	$search_arr = array();
	for($i = 0 ; $i < $cnt ; $i++){
		array_push($search_arr, "'%".$temp_search[$i]."%'");
	}

	if($s_check=="oc") $qry.= "AND a.ordercode LIKE any ( array[".implode(",", $search_arr)."] ) ";
	else if($s_check=="dv") $qry.= "AND a.deli_num LIKE any ( array[".implode(",", $search_arr)."] ) ";
	else if($s_check=="on") $qry.= "AND b.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
	else if($s_check=="oi") $qry.= "AND b.id LIKE any ( array[".implode(",", $search_arr)."] ) ";
	else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
	else if($s_check=="op") $qry.= "AND b.ip LIKE any ( array[".implode(",", $search_arr)."] ) ";
	else if($s_check=="sn") $qry.= "AND b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] ) ";
	else if($s_check=="rn") $qry.= "AND b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
	else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
	else if($s_check=="ra") $qry.= "AND b.receiver_addr LIKE any ( array[".implode(",", $search_arr)."] ) ";
	else if($s_check=="nm") $qry.= "AND (b.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) OR b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] ) OR b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] )) ";
	else if($s_check=="al") {
		$or_qry[] = " a.ordercode LIKE any ( array[".implode(",", $search_arr)."] )  ";
		//$or_qry[] = " a.deli_num = '{$search}' ";
		$or_qry[] = " b.sender_name LIKE any ( array[".implode(",", $search_arr)."] )  ";
		$or_qry[] = " b.id LIKE any ( array[".implode(",", $search_arr)."] )  ";
		$or_qry[] = " replace(b.sender_tel, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
		$or_qry[] = " b.ip LIKE any ( array[".implode(",", $search_arr)."] )  ";
		$or_qry[] = " b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] )  ";
		$or_qry[] = " b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] )  ";
		$or_qry[] = " replace(b.receiver_tel2, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
		$or_qry[] = " b.receiver_addr LIKE any ( array[".implode(",", $search_arr)."] )  ";
		$qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
	}
}

// 상품 조건
if(ord($search_prod)) {
	if($s_prod=="pn") $qry.= "AND upper(a.productname) like upper('%{$search_prod}%') ";
	else if($s_prod=="pc") $qry.= "AND upper(a.productcode) like upper('%{$search_prod}%') ";
	else if($s_prod=="sc") $qry.= "AND upper(a.selfcode) like upper('%{$search_prod}%') ";
	else if($s_prod=="st") $qry.= "AND upper(a.style) like upper('%{$search_prod}%') ";
}

// 주문구분 조건 (2016.05.11 - 김재수)
if(ord($staff_order)){
	if($staff_order != "A"){
		if($staff_order == "M"){ // 20170825 제휴사 추가
			$qry.= " AND b.cooper_order = 'Y'";
		}else{
			$qry.= " AND b.staff_order = '{$staff_order}' ";
		}
	}
}

// 배송업체 조건
if(ord($dvcode)) $qry.= "AND a.deli_com = '{$dvcode}' ";

// 발생구분 조건
if(ord($delivery_type)) $qry.= "AND a.delivery_type = '{$delivery_type}' ";

// 결제상태 조건
if(ord($paystate)) {
	if($paystate == "N") $qry.=" AND b.oi_step1 < 1 ";
	else if($paystate == "Y") $qry.=" AND b.oi_step1 > 0 ";
}

// 칸그림 관리자는 다른 벤더를 확인 못하게 막는다 2017-02-17 유동혁
/*
if( kanVenderCheck() ){
	$vender = "'" . implode( "','", $kannNotVender ) . "'";
	$qry .= " AND a.vender not in ( " . $vender . " ) and a.company_code = '99' ";
}*/

// 주문상태별 조건
if( $oistep1_arr[0] == '' ) $oistep1_arr = array();
//exdebug(count($oistep1_arr));
//exdebug($oistep1);
//exdebug(count($oi_type));
if( count($oistep1_arr) || count($oi_type_arr) ) {
	$subWhere = array();

	if(count($oistep1_arr)) {
		//$subWhere[] = " (b.oi_step1 in (".implode(", ", $oistep1_arr).") And b.oi_step2 = 0) ";
		//$subWhere[] = " (b.oi_step1 in (".$oistep1.") And b.oi_step2 = 0) ";
		$subWhere[] = " a.op_step in (".$oistep1.") ";
	}

	if(count($oi_type_arr)) {
		foreach($oi_type_arr as $k => $v) {
			switch($v) {
				case 44 : $subWhere[] = " (b.oi_step1 = 0 And b.oi_step2 = 44) "; break;    //입금전취소완료
				//case 61 : $subWhere[] = " (b.oi_step1 in (3,4) And (coalesce(a.opt1_change, '') != '' OR coalesce(a.opt2_change, '') != '') And a.op_step = 41) "; break;   //교환접수
				//case 62 : $subWhere[] = " (b.oi_step1 in (3,4) And (coalesce(a.opt1_change, '') != '' OR coalesce(a.opt2_change, '') != '') And a.op_step = 44) "; break;   //교환완료
				// 2016-02-12 jhjeong redelivery_type = 'G' 추가..옵션없는 상품의 교환일 경우 구분할수 있는 값이 없어서 추가함.
				case 67 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 40) "; break;   //교환신청
				case 61 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 41) "; break;   //교환접수
				case 62 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 44) "; break;   //교환완료
				case 68 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 40) "; break;    //반품신청
				case 63 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 41) "; break;    //반품접수
				case 64 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And a.op_step = 42) "; break;   //반품완료(배송중 이상이면서 환불접수단계)
				case 65 : $subWhere[] = " (a.redelivery_type != 'G' and b.bank_date is not null And ((b.oi_step1 in (1,2) and a.op_step = 41) OR a.op_step = 42) And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '')))"; break;  //환불접수
				case 66 : $subWhere[] = " (a.redelivery_type != 'G' and b.oi_step1 > 0 And a.op_step = 44 And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = ''))) "; break;  //환불완료
			}
		}
	}

	//exdebug($subWhere);
	if(count($subWhere)) {
		$sub = " (".implode(" OR ", $subWhere)." ) ";
	}
}
//exdebug($sub);
if($sub) $qry.= " AND ".$sub;


// 결제타입 조건
if(ord($paymethod))	$qry.= " AND SUBSTRING(b.paymethod,1,1) in ('".$paymethod."') ";

// 유입경로 조건
if(ord($ord_flag)) {
	$chk_mb = array();
	if(count($ord_flag_arr)) {
		foreach($ord_flag_arr as $k => $v) {
			switch($v) {
				case "PC" : $chk_mb[] = "0"; break;
				case "MO" : $chk_mb[] = "1"; break;
				case "AP" : $chk_mb[] = "2"; break;
			}
		}
	}
	if(count($subWhere)) {
		$qry.= " AND b.is_mobile in ('".implode("','",$chk_mb)."') ";
	}
}

// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $subqry = " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

    //$qry_from.= " join tblvenderinfo v on a.vender = v.vender ".$subqry."";
    $qry_from.= " join tblproductbrand v on a.vender = v.vender ".$subqry."";
} else {
    //$qry_from.= " join tblvenderinfo v on a.vender = v.vender ";
    $qry_from.= " join tblproductbrand v on a.vender = v.vender ";
}


if($type=="delete" && ord($ordercodes)) { //주문서 삭제
	$ordercode=str_replace(",","','",$ordercodes);
	pmysql_query("INSERT INTO tblorderinfotemp SELECT * FROM tblorderinfo WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	pmysql_query("INSERT INTO tblorderproducttemp SELECT * FROM tblorderproduct WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	pmysql_query("INSERT INTO tblorderoptiontemp SELECT * FROM tblorderoption WHERE ordercode IN ('{$ordercode}')",get_db_conn());

	pmysql_query("DELETE FROM tblorderinfo WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	pmysql_query("DELETE FROM tblorderproduct WHERE ordercode IN ('{$ordercode}')",get_db_conn());
	pmysql_query("DELETE FROM tblorderoption WHERE ordercode IN ('{$ordercode}')",get_db_conn());

	$log_content = "## 주문내역 삭제 ## - 주문번호 : ".$ordercodes;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	$onload="<script>window.onload=function(){ alert('선택하신 주문내역을 삭제하였습니다.'); }</script>";
}

$t_price=0;

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
	// 칸그림 관리자는 다른 벤더를 확인 못하게 막는다 2017-02-17 유동혁
	/*
	if( kanVenderCheck() ){
		$vender = "'" . implode( "','", $kannNotVender ) . "'";
		$vQry = " AND a.vender not in ( " . $vender . " ) ";
	}*/
    $sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname
            FROM    tblvenderinfo a
            JOIN    tblproductbrand b on a.vender = b.vender
            WHERE a.delflag='N'/* and a.vender_code not in ('01','02','03')*/
			" . $vQry . "
            ORDER BY lower(b.brandname) ASC
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

include("header.php");

$sql = "SELECT COUNT(a.ordercode) as t_count FROM {$qry_from} {$qry} {$subqry} ";
//$paging = new Paging($sql,10,20);
$paging = new newPaging($sql,10,20,'GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sum_p="select sum(a.coupon_price) as coupon_price, sum(a.deli_price) as deli_price, sum(a.use_point) as use_point, sum(a.use_epoint) as use_epoint, sum(((a.price+a.option_price)*quantity)) as tot_prod_price, sum(((a.price+a.option_price)*quantity)-a.coupon_price-use_point-use_epoint) as tot_sum_price, sum(((a.price+a.option_price)*quantity)+a.deli_price-a.coupon_price-use_point-use_epoint) as tot_real_price FROM {$qry_from} {$qry} {$subqry}";
$sum_presult=pmysql_query($sum_p);
$sum_pdata=pmysql_fetch_object($sum_presult);
$order_tot_prod_price		= $sum_pdata->tot_prod_price;
$order_tot_sum_price		= $sum_pdata->tot_sum_price;
$order_tot_real_price		= $sum_pdata->tot_real_price;
pmysql_free_result($sum_presult);

$excel_sql = "SELECT  a.vender, v.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, 
                    (a.price+a.option_price) as price, a.option_quantity, 
                    a.deli_com, a.deli_num, a.deli_date, a.deli_price, 
					a.coupon_price, a.use_point, a.use_epoint, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, 
					b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2, b.is_mobile, 
                    b.sender_tel, b.sender_email, a.self_goods_code, a.delivery_type, a.store_code, a.reservation_date, a.prodcode, a.colorcode, (a.ori_price+a.ori_option_price) as ori_price, a.staff_price, a.cooper_price, a.staff_order, a.cooper_order,a.redelivery_type
                FROM {$qry_from} {$qry} ";
$excel_sql_orderby = "
                ORDER BY a.ordercode {$orderby}, a.vender DESC 
                ";

if ($search_s != "" || $search_e != "") {
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$check_qry .= "AND {$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$check_qry .= "AND {$s_date}>='{$search_s}' AND {$s_date} <='{$search_e}' ";
	}
}

for($k=0;$k < sizeof($check_qry_arr);$k++) {
	$_count[]=pmysql_num_rows(pmysql_query("SELECT * FROM tblorderinfo WHERE sabangnet_idx ='' ".$check_qry_arr[$k]." {$check_qry}"));
}

?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".chk_all").click(function() {
		var chk_cn = $(this).attr('chk');
		 if($(this).prop("checked")){
			$("."+chk_cn).attr("checked", true);
		 } else {
			$("."+chk_cn).attr("checked", false);
		 }
	});
});

<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function searchForm() {
	document.form1.action="order_list_all.php";
	document.form1.method="GET";
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600,resizable=yes");
	document.detailform.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
	document.idxform.submit();
}

function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.member_form.search.value=id;
	document.member_form.submit();
}

function SenderSearch(sender) {
	//document.sender_form.search.value=sender;
	//document.sender_form.submit();
	document.form1.search_start.value="";
	document.form1.search_end.value="";
	document.form1.s_check.value="on";
	document.form1.search.value=sender;
	document.form1.action="order_list_all.php";
	document.form1.submit();
}

/*
function ReserveInOut(id){
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.id.value=id;
	document.reserveform.type.value="reserve";
	document.reserveform.submit();
}

var clickno=0;
function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}
*/

function CheckAll(){
	chkval=document.form2.allcheck.checked;
	cnt=document.form2.tot.value;
	for(i=1;i<=cnt;i++){
		document.form2.chkordercode[i].checked=chkval;
	}
}
/*
function AddressPrint() {
	document.form1.action="order_address_excel.php";
	document.form1.submit();
	document.form1.action="";
}
*/

function OrderExcel() {
	document.downexcelform.idxs.value="";
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

/*
function OrderExcel() {
	//alert("excel");
	document.form1.action="order_excel_all_sejung.php";
	document.form1.method="POST";
	document.form1.submit();
	document.form1.action="";
}
*/
/*
function OrderDelete(ordercode) {
	//alert(ordercode);
	if(confirm("해당 주문서를 삭제하시겠습니까?")) {
		document.idxform.type.value="delete";
		document.idxform.ordercodes.value=ordercode+",";
		document.idxform.submit();
	}
}
*/
/*
function OrderDeliPrint() {
	alert("운송장 출력은 준비중에 있습니다.");
}
*/
function OrderCheckPrint() {
	document.printform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.printform.ordercodes.value+=document.form2.chkordercode[i].value.substring(0)+",";
		}
	}
	if(document.printform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("소비자용 주문서로 출력하시겠습니까?")) {
		document.printform.gbn.value="N";
	} else {
		document.printform.gbn.value="Y";
	}
	document.printform.target="hiddenframe";
	document.printform.submit();
}

function OrderDeliveryAll() {
	//sendErporderShopChange($ordercode, $idxs) // 매장변경시 전송
	if(confirm("선택한 주문건을 매장입찰 하시겠습니까?")){
		for(i=1;i<document.form2.chkordercode.length;i++) {
			if(document.form2.chkordercode[i].checked) {
				document.form2.queryIdx.value+=document.form2.chkordercode[i].value.substring(0)+",";
			}
		}
		if(document.form2.queryIdx.value.length==0) {
			alert("선택하신 주문서가 없습니다.");
			return;
		}else{
			$("#frmList").attr('method', 'POST');
			$("#frmList").attr('action', '/admin/order_check_deli_flag.php');
			$("#frmList").submit();
		}
	}
}

function OrderCheckExcel() {
	/*
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(0)+",";
		}
	}*/
	document.downexcelform.idxs.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			if(document.downexcelform.idxs.value!='') document.downexcelform.idxs.value +=",";
			document.downexcelform.idxs.value+=document.form2.chkordercode[i].value;
		}
	}
	if(document.downexcelform.idxs.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	/*
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}*/
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
	/*
	//document.checkexcelform.target="_blank";
	document.checkexcelform.action="order_excel_all_sejung.php";
	document.checkexcelform.submit();
	*/
}
/*
function OrderSendSMS() {
	document.smsform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.smsform.ordercodes.value+="'"+document.form2.chkordercode[i].value.substring(1)+"',";
		}
	}
	if(document.smsform.ordercodes.value.length==0) {
		alert("SMS를 발송할 주문서를 선택하세요.");
		return;
	}
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.type.value="order";
	document.smsform.submit();
}

function ProductInfo(code,prcode,popup) {
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}

function pop_search(paystate, deli_gbn, redelivery_type) {
	window.open("?search_start=<?=$search_start?>&search_end=<?=$search_end?>&paystate="+paystate+"&deli_gbn="+deli_gbn+"&redelivery_type="+redelivery_type+"&popup=on", "stepPopup","");
}
*/

function CheckOrder() {
	document.stepform.idx.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			//alert(document.form2.chkordercode[i].value);
			document.stepform.idx.value+=document.form2.chkordercode[i].value+",";
		}
	}
	if(document.stepform.idx.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	//alert(document.form2.sel_mode.value);
	if (document.form2.sel_mode.value == '') {
		alert("적용할 상태를 선택해 주시기 바랍니다.");
		return;
	}
	if(document.form2.sel_mode.value == "2") {
		alert("배송준비중 처리는 반드시 입금 처리후 진행하셔야 됩니다.");
	}
	if(confirm("적용 하시겠습니까?")) {
		document.stepform.mode.value=document.form2.sel_mode.value;
		document.stepform.target = "HiddenFrame";
		//document.stepform.target = "_blank";
		document.stepform.submit();
	}
}

function store_pass(idx){
	if(confirm("해당 주문건을 매장입찰 하시겠습니까?")){
		document.form2.queryIdx.value=idx+",";
		
		$("#frmList").attr('method', 'POST');
		$("#frmList").attr('action', '/admin/order_check_deli_flag.php');
		$("#frmList").submit();
		
	}
}

function OrderShopStock(prodcd, colorcd, sizecd, delivery_type) {
	window.open("order_shop_stock.php?prodcd="+prodcd+"&colorcd="+colorcd+"&sizecd="+sizecd+"&delivery_type="+delivery_type,"stockpop","width=400,height=500");
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>전체 주문 조회</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_order.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">전체 주문 조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</span></div>
				</td>
			</tr>
			<tr>
				<td>

					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1">
								<select name="s_check" class="select" style="width:80px;height:32px;vertical-align:middle;">
									<option value="al" <?=$selected[s_check]["al"]?>>전체</option>
                                    <option value="oc" <?=$selected[s_check]["oc"]?>>주문코드</option>
                                    <!-- <option value="dv" <?=$selected[s_check]["dv"]?>>송장번호</option> -->
                                    <option value="">----------------------</option>
                                    <option value="on" <?=$selected[s_check]["on"]?>>주문자명</option>
                                    <option value="oi" <?=$selected[s_check]["oi"]?>>주문자ID</option>
                                    <option value="oh" <?=$selected[s_check]["oh"]?>>주문자HP</option>
                                    <option value="op" <?=$selected[s_check]["op"]?>>주문자IP</option>
                                    <option value="">----------------------</option>
                                    <option value="sn" <?=$selected[s_check]["sn"]?>>입금자명</option>
                                    <option value="rn" <?=$selected[s_check]["rn"]?>>수령자명</option>
                                    <option value="rh" <?=$selected[s_check]["rh"]?>>수령자HP</option>
                                    <option value="ra" <?=$selected[s_check]["ra"]?>>배송지주소</option>
                                    <option value="">----------------------</option>
                                    <option value="nm" <?=$selected[s_check]["nm"]?>>주문자명,입금자명,수령자명</option>
								</select>
								<!-- 
								<input type=text name=search value="<?=$search?>" style="width:197" class="input">
								 -->
								<textarea rows="2" cols="10" class="w200" name="search" id="search" style="resize:none;vertical-align:middle;"><?=$search?></textarea>
							</TD>
						</tr>

						<TR>
							<th><span>기간선택</span></th>
							<td>
								<select name="s_date" class="select">
									<option value="ordercode" <?=$selected[s_date]["ordercode"]?>>주문일</option>
									<!-- <option value="deli_date" <?=$selected[s_date]["deli_date"]?>>배송일</option> -->
									<option value="bank_date" <?=$selected[s_date]["bank_date"]?>>입금일</option>
                                    <!--<option value="delivery_end_date" <?=$selected[s_date]["delivery_end_date"]?> >배송완료일</option>-->
								</select>
								<input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
						</TR>

						<tr>
							<th><span>상품</span></th>
							<TD class="td_con1">
								<select name="s_prod" class="select">
									<!--<option value="st" <?=$selected[s_prod]["st"]?>>스타일</option>-->
									<option value="pn" <?=$selected[s_prod]["pn"]?>>상품명</option>
									<option value="pc" <?=$selected[s_prod]["pc"]?>>상품코드</option>
									<option value="sc" <?=$selected[s_prod]["sc"]?>>진열코드</option>
								</select>
								<input type=text name=search_prod value="<?=$search_prod?>" style="width:197" class="input">
							</TD>
						</tr>
<? /*
						<TR>
							<th><span>배송업체</span></th>
							<TD>
								<select name=dvcode class="select">
									<option value="">==== 전체 ====</option>
<? while($ref1_data=pmysql_fetch_object($ref1_result)){ ?>
									<option value="<?=$ref1_data->code?>" <?=$selected[dvcode][$ref1_data->code]?>><?=$ref1_data->company_name?></option>
<? } ?>
								</select>&nbsp;
							</TD>
						</TR>
*/ ?>

						<TR>
							<th><span>주문구분</span></th>
							<TD class="td_con1">
								<input type="radio" name="staff_order" value="A" <?=$selected[staff_order]["A"]?>>전체</input>
								<input type="radio" name="staff_order" value="N" <?=$selected[staff_order]["N"]?>>일반</input>
								<input type="radio" name="staff_order" value="Y" <?=$selected[staff_order]["Y"]?>>임직원</input>
<!-- 20170825 제휴사 추가 -->
								<input type="radio" name="staff_order" value="M" <?=$selected[staff_order]["M"]?>>제휴</input>
<!----------------------->
							</TD>
						</TR>

						<TR>
							<th><span>주문상태</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_oistep' name="oistep_all" value="<?=$k?>" <?if(count($oistep1_arr) == 5 && count($oi_type_arr) == 9) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
<?
		foreach ($oi_step1 as $k=>$v){
?>
								<input type="checkbox" class='chk_oistep' name="oistep1[]" value="<?=$k?>" <?=( in_array($k, $oistep1_arr )?'checked':'')?>><?=$v?></input>
<?
		}
?>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="44" <?=(in_array(44,$oi_type_arr)?'checked':'')?>>입금전취소완료</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="67" <?=(in_array(67,$oi_type_arr)?'checked':'')?>>교환신청</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="61" <?=(in_array(61,$oi_type_arr)?'checked':'')?>>교환접수</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="62" <?=(in_array(62,$oi_type_arr)?'checked':'')?>>교환완료</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="68" <?=(in_array(68,$oi_type_arr)?'checked':'')?>>반품신청</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="63" <?=(in_array(63,$oi_type_arr)?'checked':'')?>>반품접수</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="64" <?=(in_array(64,$oi_type_arr)?'checked':'')?>>반품완료</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="65" <?=(in_array(65,$oi_type_arr)?'checked':'')?>>환불접수</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="66" <?=(in_array(66,$oi_type_arr)?'checked':'')?>>환불완료</input>
							</TD>
						</TR>

						<TR>
							<th><span>발생구분</span></th>
							<TD class="td_con1">
								<input type="radio" name="delivery_type" value="" <?if($selected[delivery_type]==''){?>checked<?}?>>전체
								<?foreach ($arrChainCode as $k=>$v){?>
								<input type="radio" name="delivery_type" value="<?=$k?>" <?if($selected[delivery_type]."|"==$k."|"){?>checked<?}?>><?=$v?>
								<?}?>
							</TD>
						</TR>

						<TR style="display:none">
							<th><span>결제상태</span></th>
							<TD class="td_con1">
								<input type="radio" name="paystate" value="A" <?=$selected[paystate]["A"]?>>전체</input>
								<input type="radio" name="paystate" value="N" <?=$selected[paystate]["N"]?>>입금전</input>
								<input type="radio" name="paystate" value="Y" <?=$selected[paystate]["Y"]?>>입금완료(결제완료)</input>
							</TD>
						</TR>

						<TR>
							<th><span>결제타입</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_paymethod' name="paymethod_all" value="<?=$k?>" <?if(count($paymethod_arr) == 6) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
<?php
							foreach($arpm as $k => $v) {
								$selPaymethod='';
								if(in_array($k,$paymethod_arr)>0)$selPaymethod="checked";
?>
								<input type="checkbox" class='chk_paymethod' name="paymethod[]" value="<?=$k?>" <?=$selPaymethod?>><?=$v?>
<?
							}
?>
							</TD>
						</TR>

						<TR>
							<th><span>유입경로</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_ord_flag' name="ord_flag_all" value="<?=$k?>" <?if(count($ord_flag_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
								<input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="PC" <?=(in_array('PC',$ord_flag_arr)?'checked':'')?>>PC</input>
								<input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="MO" <?=(in_array('MO',$ord_flag_arr)?'checked':'')?>>MOBILE</input>
								<input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="AP" <?=(in_array('AP',$ord_flag_arr)?'checked':'')?>>APP</input>
							</TD>
						</TR>

<?
	if($vendercnt > 0) {
?>
						<TR>
							<th><span>브랜드</span></th>
							<td><select name=sel_vender class="select">
									<option value="">==== 전체 ====</option>
									<?php
									foreach($venderlist as $key => $val) {
										echo "<option value=\"{$val->vender}\"";
										if($sel_vender==$val->vender) echo " selected";
										echo ">{$val->brandname}</option>\n";
									}
									?>
								</select>
								
							</td>
						</TR>

                        <TR style="display:none">
                            <th><span>상품구분</span></th>
                            <td>
                                <select name='pr_select' class='select' >
                                    <option value="" <?if( $pr_select == '' ){ echo 'selected'; }?> >=== 전체 ===</option>
                                    <option value="SJ" <?if( $pr_select == 'SJ' ){ echo 'selected'; }?> >세정상품</option>
                                    <option value="VN" <?if( $pr_select == 'VN' ){ echo 'selected'; }?> >입점상품</option>
                                </select>
                            </td>
                        </TR>

                        <TR style="display:none">
                            <th><span>옥션주문</span></th>
                            <td>
                                <select name='auction_chk' class='select' >
                                    <option value="" <?if( $auction_chk == '' ){ echo 'selected'; }?> >=== 전체 ===</option>
                                    <option value="N" <?if( $auction_chk == 'N' ){ echo 'selected'; }?> >일반주문</option>
                                    <option value="Y" <?if( $auction_chk == 'Y' ){ echo 'selected'; }?> >경매주문</option>
                                </select>
                            </td>
                        </TR>
<?
	}
?>
						</TABLE>
						</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="#;" onclick="OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></td>
			</tr>
			</table>
			</form>

			<form name=form2 id = 'frmList' action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type = 'hidden' id = 'queryString' name = 'query_string' value = '<?=$_SERVER['QUERY_STRING']?>'>
			<input type = 'hidden' id = 'queryIdx' name = 'query_idx' value = ''>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		//$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰","Y"=>"PAYCO");

		$sql = "
				SELECT
						a.vender, v.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, a.price, a.colorcode,
						a.option_price, a.deli_com, a.deli_num, a.deli_date, a.deli_price,
						a.coupon_price, a.use_point, a.use_epoint, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx,
						b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2, a.redelivery_type, b.is_mobile, a.delivery_type, a.reservation_date, a.store_code, b.pg_ordercode,
						b.deli_price as sumdeli_price, b.price as sumprice, b.reserve as sumreserve, b.point as sumpoint, b.dc_price as sumdc, a.staff_order, a.cooper_order, a.season, a.deli_closed
				FROM {$qry_from} {$qry} {$subqry}
				ORDER BY a.ordercode {$orderby}, a.vender DESC
				";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
// 		echo "sql = ".$sql."<br>";
// 		//exit();
// 		exdebug($sql);

		$colspan=10;

		if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif" border="0"><B>정렬 :
					<?php if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>주문일자순↑</FONT></B></A>
					<?php }else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>주문일자순↓</FONT></B></A>
					<?php }?>
					&nbsp;주문번호(붉은색) : <B><FONT class=font_orange>PG 주문번호</FONT></B>
					</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0"><B>총 상품금액 : <FONT class=font_orange><?=number_format($order_tot_prod_price)?></FONT>원</B>&nbsp;&nbsp;<img src="images/icon_8a.gif" border="0"><B>총 상품개별금액 : <FONT class=font_orange><?=number_format($order_tot_sum_price)?></FONT>원</B>&nbsp;&nbsp;<img src="images/icon_8a.gif" border="0"><B>총 실결제금액 : <FONT class=font_orange><?=number_format($order_tot_real_price)?></FONT>원</B>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=140></col>
				<col width=130></col>
				<col width=30></col>
				<?php if($vendercnt>0){?>
				<col width=80></col>
				<?php }?>
				<col width=*></col>
				<col width=100></col>
				<col width=60></col>
				<col width=80></col>
				<col width=40></col>
				<col width=60></col>
				<col width=80></col>
				<col width=60></col>
				<col width=80></col>
				<col width=60></col>
				<col width=80></col>
				<col width=100></col>
				<col width=60></col>
				<col width=50></col>
				<input type=hidden name=chkordercode>

				<TR>
					<th>주문일자</th>
					<th>주문자 정보</th>
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<?php if($vendercnt>0){?>
					<th>브랜드</th>
					<?php }?>
					<th>상품명</th>
					<th>O2O구분</th>
					<th>결제방법</th>
					<th>상품금액</th>
					<th>수량</th>
					<th>쿠폰할인</th>
					<th>통합포인트</th>
					<th>E포인트</th>
					<th>상품개별금액</th>
					<th>배송비</th>					
					<th>실결제금액</th>
					<th>배송주체</th>
					<th>처리단계</th>
					<th>비고</th>
				</TR>

<?php
	$colspan=16;
	if($vendercnt>0) $colspan++;

	$curdate = date("YmdHi",strtotime('-2 hour'));
	$curdate5 = date("Ymd",strtotime('-5 day'));
	$cnt=0;
	$thisordcd="";
	$thiscolor="#FFFFFF";
	$arrListOrder = $arrListOrderCount = array();

	while($row=pmysql_fetch_object($result)) {
		$thisordcd = $row->ordercode;
		if($thisordcd == $row->ordercode) {
		}
		$arrListOrderCount[$row->ordercode] += 1;
		$arrListOrder[] = $row;
	}

	foreach($arrListOrder as $key => $row) {
		$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
		$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")<br>".$row->ordercode."<br>";
		$name = $row->sender_name;
		//$stridX='';
		$stridM='';
		$member_text="";
		if(substr($row->ordercode,20)=="X") { //비회원
			$member_text=" (비회원)";
			$stridM = "주문번호: ".substr($row->id,1,6);
		} else { //회원
			//$stridM = "ID: <A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
			$stridM = "ID: <A HREF=\"javascript:CrmView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
		}
		if($thisordcd!=$row->ordercode) {
			$thisordcd=$row->ordercode;
			if($thiscolor=="#FFFFFF") {
				//$thiscolor="#FEF8ED";
				$thiscolor="#ffeeff";
			} else {
				$thiscolor="#FFFFFF";
			}
		}
		if($row->auction_seq > 0){
			$thiscolor="#FBF8DE";
		}
		//list($style, $color)=pmysql_fetch("SELECT style, color FROM tblproduct WHERE productcode='".$row->productcode."'");

		$storeData = getStoreData($row->store_code);

		//$status = $o_step[$row->oi_step1][$row->oi_step2];
		$status = $op_step[$row->op_step];
		if($row->redelivery_type == "G" && $row->op_step == "41") $status = "교환접수";
		if($row->redelivery_type == "G" && $row->op_step == "44") $status = "교환완료";
		
		$opt_name="";
		if($row->colorcode){
			$opt_name.="COLOR : ".$row->colorcode." / ";
		}
		if( strlen( $row->opt1_name ) > 0  || strlen( $row->text_opt_subject ) > 0 ){
			$tmp_opt_subject = explode( '@#', $row->opt1_name );
			$tmp_opt_content = explode( chr(30), $row->opt2_name );
			foreach( $tmp_opt_subject as $subjectKey=>$subjectVal ){
				$opt_name.=$subjectVal.' : '.$tmp_opt_content[$subjectKey];
			} // opt_subject foreach

			if( strlen( $row->text_opt_subject ) > 0 ){
				$tmp_text_opt_subject = explode( '@#', $row->text_opt_subject );
				$tmp_text_opt_content = explode( '@#', $row->text_opt_content );
				foreach( $tmp_text_opt_subject as $subjectKey=>$subjectVal ){
					$opt_name.=' [ '.$subjectVal.' : '.$tmp_text_opt_content[$subjectKey];
				} // opt_subject foreach
			}
		} else {
			$opt_name.="-";
		}
?>
				 <!-- onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'" -->
				<tr bgcolor=<?=$thiscolor?>>
					<?if($thisordcd2!=$row->ordercode) {?>
						<?$thisordcd2=$row->ordercode;?>
						<td align="center" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>">
							<A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$date?></A><br><FONT class=font_orange><?=$row->pg_ordercode ?></font>
						</td>

						<td style="font-size:8pt;padding:3;line-height:11pt" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>">
							주문자: <A HREF="javascript:SenderSearch('<?=$name?>');"><FONT COLOR="blue"><?=$name.$member_text?></font></A>
							<br> <?=$stridM?>
<!----------- 20170822 제휴사 ---------------->

							<?list($group_name)=pmysql_fetch("SELECT a.group_name FROM tblcompanygroup a LEFT JOIN tblmember p on a.group_no = p.company_group WHERE 1=1 and p.id='".$row->id."'");?>
							<br> 구매자 : <FONT class=font_orange><?if($row->staff_order=="Y"){echo "임직원";}else if($row->cooper_order=="Y"){echo "제휴(".$group_name.")";}else{echo "일반";}?></font>							

<!-------------- 20170822 ------------------->
						</td>
					<?}?>

					<td align="center">
						<input type=checkbox name=chkordercode value="<?=$row->idx?>"><br>
						<!-- <?=$number?> -->
					</td>
<?
			if($vendercnt>0){
?>
					<td style='text-align:left'>
						<a href="javascript:viewVenderInfo(<?=$row->vender?>)"><?=$row->brandname?></a>
					</td>
<?
			}
?>
					<td style='text-align:left'><a href="JavaScript:OrderDetailView('<?=$row->ordercode?>')">
						<?=$row->productname?><img src="images/newwindow.gif" border=0 align=absmiddle></a>
						<br><?=$opt_name?>
<?
						list($prodcode)=pmysql_fetch("select prodcode from tblproduct where productcode='".$row->productcode."'");
?>
						&nbsp;<input type="button" value="재고확인" class="btn_blue" style="font-size:11px" onclick="javascript:OrderShopStock('<?=$prodcode?>', '<?=$row->colorcode?>', '<?=$row->opt2_name?>', '<?=$row->delivery_code?>')" >
						
					</td>
					<td align=center style="font-size:8pt;padding:3;line-height:12pt">
						<div>
							<?
								echo '<strong>'.$arrChainCode[$row->delivery_type].'</strong>';
								if( $row->reservation_date ){
									echo '<br>'.substr($row->reservation_date, 0, 4).".".substr($row->reservation_date, 5, 2).".".substr($row->reservation_date, 8, 2);
								}
								if($row->store_code){
									echo '<br>'.$storeData["name"];
									echo '<br>'.$row->store_code;
								}
							?>
						</div>
					</td>
					<td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->price+$row->option_price)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->quantity)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->coupon_price)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->use_point)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format($row->use_epoint)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format((($row->price+$row->option_price)*$row->quantity)-$row->coupon_price-$row->use_point-$row->use_epoint)?></td>
					<?if($thisordcd4!=$row->ordercode) {?>
						<?$thisordcd4=$row->ordercode;?>
						<td align=right style="font-size:8pt;padding:3" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>"><?=number_format($row->sumdeli_price)?></td>
					<?}?>
					<?if($thisordcd5!=$row->ordercode) {?>
						<?$thisordcd5=$row->ordercode;?>
						<td align=right style="font-size:8pt;padding:3" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>"><?=number_format($row->sumprice-$row->sumdc-$row->sumreserve-$row->sumpoint+$row->sumdeli_price)?></td>
					<?}?>


					<td align=center style="font-size:8pt;padding:3">
						<?if($row->delivery_type==0 || $row->delivery_type==3){    //택배발송 주문의 경우 배송주체 나오도록 작업
							
							list($prodcode)=pmysql_fetch("select prodcode from tblproduct where productcode='".$row->productcode."'");
//							$rtn=getErpPriceNStock($prodcode, $row->colorcode, $row->opt2_name, $sync_bon_code);

//							if(!$rtn[sumqty]){
//								$rtn[sumqty]=0;
//							}
// 20170824 매장재고 없을시 매장입찰 생성 안되게 수정
							$prodcd				= $prodcode;
							$colorcd			= $row->colorcode;
							$sizecd				= $row->opt2_name;

							$res = getErpProdShopStock($prodcd, $colorcd, $sizecd, $row->delivery_type);
//							$res = getErpProdShopStock_New($prodcd, $colorcd, $sizecd, $row->delivery_type);

							$sum = 0;
							$sum_a = 0;
							if ($res) {
								foreach($res["shopnm"] as $key => $val) {
									if ($res["availqty"][$key] > 0) {
										if($res["shopcd"][$key] != "A1801B"){
											$sum += $res["availqty"][$key];
										}else{
											$sum_a += $res["availqty"][$key];
										}
									}
								}
							}

							?>
							<span class="page_screen">
								<?if($row->delivery_type==0){?>
									본사발송<br>(재고:<?=$sum_a?>)
								<?}else if($row->delivery_type==3){?>
									매장발송
								<?}?>
								<?//if(($row->staff_order=="N" && $row->delivery_type==0 && ($row->op_step == 1 || $row->op_step == 2)) && $row->season!="K" && $sum > 0){?>
								<?if(($row->delivery_type==0 && ($row->op_step == 1 || $row->op_step == 2)) && $row->season!="K" && $sum > 0){?>
								<br><input type="button" value="매장입찰" class="btn_blue" style="padding:2px 5px 1px" onclick="javascript:store_pass('<?=$row->idx?>')">
								<?}?>
								
							</span>
                       
						<?}else{?>
								-
						<?}?>								

					</td>
					
					<!--<td align=center style="font-size:8pt;padding:3"><?=$row->oi_step1." / ".$row->oi_step2." / ".$row->op_step?><br><?=$status?></td>-->
					<td align=center style="font-size:8pt;padding:3"><?=$row->oi_step1." / ".$row->oi_step2." / ".$row->op_step?><br>
						<?if($row->op_step < "40"){
							if($row->op_step=="3" && $row->deli_closed){
								echo "CJ배송완료";
							}else{
								echo GetStatusOrder("p", $row->oi_step1, $row->oi_step2, $row->op_step, $row->redelivery_type, $row->order_conf);
							}
						}else{
							$sql="SELECT * FROM tblorder_cancel WHERE oc_no='{$row->oc_no}'";
							$result=pmysql_query($sql,get_db_conn());
							$_oci=pmysql_fetch_object($result);
							echo orderCancelStatusStep($row->redelivery_type, $_oci->oc_step, $_oci->hold_oc_step);
						}
						?>
					</td>
					<?if($thisordcd3!=$row->ordercode) {?>
						<?$thisordcd3=$row->ordercode;?>
						<td align=right style="font-size:8pt;padding:3" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>"><?=$arr_mobile[$row->is_mobile]?></td>
					<?}?>

<?
		$cnt++;
	}
	pmysql_free_result($result);
	if($cnt==0) {
		echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
	}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;">
					<!--<a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" border="0" hspace="0"></a>&nbsp;-->
					<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a>&nbsp;
					<a href="javascript:OrderDeliveryAll();"><img src="images/market_send.gif" border="0" hspace="1"></a>&nbsp;
					<!--<a href="javascript:OrderDeliveryAll();"><img src="images/btn_order_delivery_all.gif" border="0" hspace="2"></a>-->
				</td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
							<div id="page_navi01" style="height:'40px'">
								<div class="page_navi">
									<ul>
										<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
									</ul>
								</div>
							</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">

			<tr style='display:none;'>
				<td background="images/counter_blackline_bg.gif"  class="font_white" align="center" height='40'>
				선택한 주문건을
				<select name=sel_mode class="select">
					<option value="">=======주문상태변경=======</option>
					<!--<option value="1">입금확인 처리</option>-->
					<option value="2">배송준비중 처리</option>
					<!-- <option value="3">배송중(발송완료) 처리</option> -->
					<!-- <option value="4">배송완료 처리</option> -->
				</select> 로
				&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckOrder();" style='font-weight:bold;color: #FFDB1A;'>[ 적용하기 ]</a></td>
			</tr>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>배송/입금일별 주문조회</span></dt>
							<dd>
								- 입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.<br>
								- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br>
								- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<br>
								- 카드실패 주문건은 2시간후에 삭제가 가능합니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 부가기능</span></dt>
							<dd>
								- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 주의사항</span></dt>
							<dd>- 배송/입금별 주문조회 기간은 1달을 초과할 수 없습니다.</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</form>

			<!-- <form name=detailform method="post" action="order_detail_v2.php" target="orderdetail"> -->
			<form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=staff_order value="<?=$staff_order?>"> <!-- 스테프관련 추가 (2016.05.11 - 김재수) -->
			<input type=hidden name=dvcode value="<?=$dvcode?>">
			<input type=hidden name=oistep1 value="<?=$oistep1?>">
			<input type=hidden name=oi_type value="<?=$oi_type?>">
			<input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<!-- <input type=hidden name=deli_gbn value="<?=$deli_gbn?>"> -->
			<input type=hidden name=s_date value="<?=$s_date?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=sel_vender2 value="<?=$sel_vender2?>">
			<input type=hidden name=brandname value="<?=$brandname?>">
			<!-- <input type=hidden name=redelivery_type value="<?=$redelivery_type?>"> -->
			<input type=hidden name=ord_flag value="<?=$ord_flag?>">
            <input type=hidden name=delivery_type value="<?=$delivery_type?>">
            <input type=hidden name=s_prod value="<?=$s_prod?>">
            <input type=hidden name=search_prod value="<?=$search_prod?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<!-- <form name=reserveform action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			</form> -->

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=checkexcelform action="order_excel_new.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
			<input type=hidden name="item_type" value="order_product_all">
			<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
			<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			<input type=hidden name="idxs">
			</form>

<? /*
			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>

			<form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			</form>
*/ ?>

			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>

			<form name=stepform action="order_state_indb.php" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=idx>
			<input type=hidden name=ordercodes>
			</form>

			<form name=crmview method="post" action="crm_view.php">
			<input type=hidden name=id>
			</form>

			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php
include("copyright.php");
?>
