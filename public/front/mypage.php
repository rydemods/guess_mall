<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
} else {
	$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
	/*if ($mem_auth_type == 'sns') {
		Header("Location:".$Dir.FrontDir."lately_view.php");
		exit;
	}*/
}

function dateDiff($nowDate, $oldDate) { 
	$nowDate = date_parse($nowDate); 
	$oldDate = date_parse($oldDate); 
	return ((gmmktime(0, 0, 0, $nowDate['month'], $nowDate['day'], $nowDate['year']) - gmmktime(0, 0, 0, $oldDate['month'], $oldDate['day'], $oldDate['year']))/3600/24); 
}


$sql = "SELECT a.*, b.group_level, b.group_name, b.group_code, b.group_orderprice_s, b.group_orderprice_e, b.group_ordercnt_s, b.group_ordercnt_e FROM tblmember a left join tblmembergroup b on a.group_code = b.group_code WHERE a.id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
	}
}
$staff_type = $row->staff_type;
pmysql_free_result($result);

// 사용가능 쿠폰수
$cdate = date("YmdH");
$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='{$cdate}' OR date_end='') ";
//echo "sql = ".$sql."<br>";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$coupon_cnt = $row->cnt;
pmysql_free_result($result);

//1:1문의 수
$sql = "SELECT COUNT(*) as cnt FROM tblpersonal  WHERE id='".$_ShopInfo->getMemid()."'";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$personal_cnt = $row->cnt;
pmysql_free_result($result);

// 현재 AP포인트
$now_ap_point		= $_mdata->act_point;

// 다음등급 AP포인트
list($next_ap_point)=pmysql_fetch_array(pmysql_query("select group_ap_s from tblmembergroup WHERE group_level > '{$_mdata->group_level}' order by group_level asc limit 1"));

// 다음등급까지 남은 AP 포인트
$left_ap_point=($now_ap_point >= $next_ap_point)?'0':($next_ap_point-$now_ap_point);

// 주문상태별 수
$sql = "select 
			id, 
			SUM(step0) as step0, 
			SUM(step1) as step1, 
			SUM(step2) as step2, 
			SUM(step3) as step3, 
			SUM(step4) as step4, 
			SUM(step5) as step5, 
			SUM(step6) as step6, 
			SUM(step7) as step7 
			from (
			select 
			id,
			ordercode, 
			CASE WHEN oi_step1=0 and oi_step2=0 THEN 1 ELSE 0 END as step0, 
			CASE WHEN oi_step1=1 and oi_step2=0 THEN 1 ELSE 0 END as step1, 
			CASE WHEN oi_step1=2 and oi_step2=0 THEN 1 ELSE 0 END as step2, 
			CASE WHEN oi_step1=3 and oi_step2=0 THEN 1 ELSE 0 END as step3, 
			CASE WHEN oi_step1=4 and oi_step2=0 THEN 1 ELSE 0 END as step4,  
			0 as step5,  
			0 as step6, 
			0 as step7
			from tblorderinfo 
			where oi_step1 in ('0','1','2','3','4') and oi_step2 in ('0')
			union all
			select oi.id,
			op.ordercode,
			0 as step0, 
			0 as step1, 
			0 as step2, 
			0 as step3, 
			0 as step4,  
			1 as step5,  
			0 as step6, 
			0 as step7 
			from tblorderproduct op left join tblorderinfo oi on op.ordercode=oi.ordercode 
			where op.redelivery_type NOT IN ('G','Y') and op.op_step in ('40','41','42','44') group by op.ordercode, oi.id
			union all
			select oi.id,
			op.ordercode,
			0 as step0, 
			0 as step1, 
			0 as step2, 
			0 as step3, 
			0 as step4,  
			0 as step5, 
			CASE WHEN op.redelivery_type = 'Y' THEN 1 ELSE 0 END as step6, 
			CASE WHEN op.redelivery_type = 'G' THEN 1 ELSE 0 END as step7 
			from tblorderproduct op left join tblorderinfo oi on op.ordercode=oi.ordercode 
			where op.redelivery_type IN ('G','Y') and op.op_step in ('40','41','42','44') group by op.ordercode, oi.id, op.redelivery_type
			) as foo where  id='".$_ShopInfo->getMemid()."' group by id
";
$result = pmysql_query($sql,get_db_conn());
$osc_data = pmysql_fetch_object($result);
pmysql_free_result($result);

//최근 6개월 누적금
/*$CurrentTime = time();
$trimestertime=date("Ym01",strtotime('-5 month'));
$today_time=date("Ymd",$CurrentTime);

$group_now_code	= $_mdata->group_code; // 현재 등급 코드
$group_next_code	= $group_now_code + 1; // 다음 등급 코드

list($id, $ord_cnt, $tot_ord_price)=pmysql_fetch_array(pmysql_query("select id, count(oi.ordercode) as ord_cnt, sum(op.tot_price) as tot_price from tblorderinfo oi left join (select ordercode, sum((price+option_price)*option_quantity) tot_price from tblorderproduct where op_step < 40 group by ordercode) op on oi.ordercode=op.ordercode where oi.ordercode between '".$trimestertime."' and '".$today_time."' and oi.id='".$_ShopInfo->getMemid()."' and oi.oi_step1 > 0 and oi_step2 < 40 group by id"));

if ($group_next_code == 6) {
	$n_group_name=$_mdata->group_name;
	$n_group_orderprice_s=$_mdata->group_orderprice_s;
	$n_group_orderprice_e=$_mdata->group_orderprice_e;
	$n_group_ordercnt_s=$_mdata->group_ordercnt_s;
	$n_group_ordercnt_e=$_mdata->group_ordercnt_e;
	$n_group_status	= "유지";
} else {
	$sql = "SELECT * FROM tblmembergroup WHERE group_code = '000{$group_next_code}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$n_group_name=$row->group_name;
		$n_group_orderprice_s=$row->group_orderprice_s;
		$n_group_orderprice_e=$row->group_orderprice_e;
		$n_group_ordercnt_s=$row->group_ordercnt_s;
		$n_group_ordercnt_e=$row->group_ordercnt_e;
		$n_group_status	= "상향조정";
	}
	pmysql_free_result($result);
}

//필요 구매 금액
$tot_need_price	= $n_group_orderprice_s - $tot_ord_price;
if ($tot_need_price < 0) $tot_need_price = 0;

//필요 주문 건수
$tot_need_cnt	= $n_group_ordercnt_s - $ord_cnt;
if ($tot_need_cnt < 0) $tot_need_cnt = 0;
*/

# 개별디자인 사용하지 않음 2016 01 05 유동혁
/*
$leftmenu="Y";
if($_data->design_mypage=="U") {
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='mypage'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	pmysql_free_result($result);
}
*/



##### 최근 본 상품

$_prdt_list=trim($_COOKIE['ViewProduct'],',');	//(,상품코드1,상품코드2,상품코드3,) 형식으로


$prdt_list=explode(",",$_prdt_list);
$prdt_no=count($prdt_list);
if(ord($prdt_no)==0||!$_prdt_list) {
	$prdt_no=0;
}

$tmp_product="";
for($i=0;$i<$prdt_no;$i++){
	$prdt_listArr = explode("||", $prdt_list[$i]);
	$tmp_product.="'{$prdt_listArr[0]}',";
}
$tmp_product = rtrim($tmp_product,',');
$tmp_productArr = explode(",", $tmp_product);
$tmp_productArr = array_unique($tmp_productArr);
$tmp_product = implode(",",$tmp_productArr);

$productall = array();
if($tmp_product){
	
	
	$sql_recent = "SELECT productcode,productname,tinyimage,quantity,consumerprice,sellprice , production FROM tblproduct ";
	$sql_recent.= "WHERE productcode IN ({$tmp_product}) ";
	$sql_recent.= "ORDER BY FIELD(productcode,{$tmp_product}) limit 4";

	$res_recent=pmysql_query($sql_recent,get_db_conn());
}



?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
	//좋아요
   
});	
<!--
function OrderDetailPop(ordercode) {
	document.form2.ordercode.value=ordercode;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}
function DeliSearch(deli_url){
	window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
}
function DeliveryPop(ordercode) {
	document.form3.ordercode.value=ordercode;
	window.open("about:blank","delipop","width=600,height=370,scrollbars=no");
	document.form3.submit();
}
function ViewPersonal(idx) {
	window.open("about:blank","mypersonalview","width=600,height=450,scrollbars=yes");
	document.form4.idx.value=idx;
	document.form4.submit();
}
function OrderDetail(ordercode) {
	document.detailform.ordercode.value=ordercode;
	document.detailform.submit();
}
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}

//좋아요..
function SaveLike(like_code, like_type) {
	
    $.ajax({
        type: "POST",
        url: "ajax_hott_like_ok.php",
        data: "hott_code="+like_code+"&section=product&like_type="+like_type, 
        //data: param,
        dataType: "JSON", 
        async: false, 
        cache: false, 
        success: function(data) {
            alert(data[0]['msg']);
            //alert(data[0]['cnt_my']);
            //alert(data[0]['div']);
            $(".like_"+like_code).html(data[0]['div']);
        }, 
        error: function(result) {
            //alert(result.status + " : " + result.description);
            alert("오류 발생!! 조금 있다가 다시 해주시기 바랍니다.");
        }
    });
}


//-->
</SCRIPT>

<?php
include ($Dir.TempletDir."mypage/mypage{$_data->design_mypage}.php");
?>
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>
<form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
</form>
<form name=form3 method=post action="<?=$Dir.FrontDir?>deliverypop.php" target="delipop">
<input type=hidden name=ordercode>
</form>
<form name=form4 action="<?=$Dir.FrontDir?>mypage_personalview.php" method=post target="mypersonalview">
<input type=hidden name=idx>
</form>
<form name=detailform method=GET action="<?=$Dir.FrontDir?>mypage_orderlist_view.php">
<input type=hidden name=ordercode>
</form>

<?=$onload?>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
