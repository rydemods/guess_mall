<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");



$ordercode=$_POST["ordercode"];
if (!$ordercode) $ordercode=$_GET["ordercode"];

$fail_msg=$_POST['fail_msg'];
#비즈 스프링 주문 완료 값 초기 셋팅
$gateProductPriceStr = array();
$gateProductNameStr = array();
$gateProductEaStr = array();
$gateProductCodeStr = array();

/*if(substr($ordercode,0,8)<=date("Ymd",strtotime('-3 day'))) {
	echo "<html></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.'); location.href='{$Dir}'\"></body></html>";
	exit;
}*/


$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_ord = $row;
}else if( $fail_msg == 'msg' ){
	echo "<html></head><body onload=\"alert('배송메세지에 사용할 수 없는 문자열이 포함 되어 있습니다'); location.href='/'\"></body></html>";
	exit;
}else {
	echo "<html></head><body onload=\"alert('오류발생,관리자에게 문의해주세요'); location.href='/'\"></body></html>";
	exit;
}

pmysql_free_result($result);

if (strstr("VOQCPM", $_ord->paymethod[0]) && $_ord->deli_gbn=="C") {
	$_ord->pay_data = "결제 중 주문취소";
}
if (strstr("B", $_ord->paymethod[0]) || (strstr("G", $_ord->paymethod[0]) && $_ord->pay_flag =='N') || (strstr("VOQCPMY", $_ord->paymethod[0]) && strcmp($_ord->pay_flag,"0000")==0)){// 주문성공	
} else {//주문실패
	// 주문의 상태값을 변경한다.
	$osu_sql = " UPDATE tblorderinfo SET ";		
	$osu_sql.= "oi_step2 = '54' ";
	$osu_sql.= " WHERE ordercode='".trim($ordercode)."' AND oi_step2 != 54";
	//echo $osu_sql;
	pmysql_query($osu_sql,get_db_conn());

	//주문상품의 상태값을 변경한다.
	$osup_sql = "Update tblorderproduct Set op_step = '54' Where ordercode='".trim($ordercode)."' AND op_step != 54";
	pmysql_query($osup_sql,get_db_conn());
}

# 주문 세팅
$sql = "SELECT op.vender, pr.brand, op.ordercode, op.productcode, op.productname, ";
$sql.= "op.opt1_name, op.opt2_name, op.addcode, op.quantity, ";
$sql.= "op.price, op.reserve, op.date, op.selfcode, ";
$sql.= "op.option_price, op.option_quantity, op.coupon_price, op.deli_price, op.use_point, ";
$sql.= "op.text_opt_subject, op.text_opt_content, op.option_price_text, ";
$sql.= "op.option_type, pr.tinyimage, pr.minimage, pr.consumerprice, ";
$sql.= "vi.deli_mini, op.delivery_type, op.store_code, op.reservation_date, pr.colorcode, pr.prodcode ";
$sql.= "FROM tblorderproduct op LEFT JOIN tblproduct pr ON op.productcode = pr.productcode ";
$sql.= "LEFT JOIN tblvenderinfo vi ON op.vender = vi.vender ";
$sql.= "WHERE op.ordercode = '".$ordercode."' ";
$sql.= "ORDER BY op.vender ASC, op.productcode ASC, op.date DESC ";
$result=pmysql_query($sql,get_db_conn());

while($row=pmysql_fetch_array($result)) {
	$productArr[] = $row;
}
pmysql_free_result( $result );

?>
<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

<TITLE><?=$_data->shoptitle?> - 주문완료</TITLE>


<SCRIPT LANGUAGE="JavaScript">
<!--
	function OrderDetailPrint(ordercode) {
		document.form2.ordercode.value=ordercode;
		document.form2.print.value="OK";
		window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
		document.form2.submit();
	}

	function setPackageShow(packageid) {
		if(packageid.length>0 && document.getElementById(packageid)) {
			if(document.getElementById(packageid).style.display=="none") {
				document.getElementById(packageid).style.display="";
			} else {
				document.getElementById(packageid).style.display="none";
			}
		}
	}
	
	$(document).ready(function(){
		$(".CLS_OrderView").click(function(){
			$("form[name='mypageOrderViewFrm']").submit();
		})
		$(".CLS_GoToMain").click(function(){
			location.replace('/');	//네임 수정되면 같이 수정해야됨
		})
	})
//-->
</SCRIPT>



<?php
foreach( $productArr as $_proData =>$_proObj ){
	//exdebug($_proObj['vender']);
	$brandVenderArr[$_proObj['brand']]	=  $_proObj['vender'];
}

//exdebug($brandVenderArr);

$brandArr = ProductToBrand_Sort( $productArr );

//exdebug($brandArr);

$imgPath = $Dir.DataDir.'shopimages/product/';
$coupon_cnt = 0;
if( strlen( $_ShopInfo->getMemid() ) > 0 ){
	$coupon_cnt = count( MemberCoupon( 1, 'P' ) );
	$memsql = "SELECT reserve FROM tblmember WHERE id = '".$_ShopInfo->getMemid()."'";
	$memres = pmysql_query( $memsql, get_db_conn() );
	$mem_reserve = pmysql_fetch_object( $memres );
	pmysql_free_result( $memres );
}
?>
<?php  include ($Dir.TempletDir."orderend/orderend{$_data->design_order}.php"); ?>

<form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
<input type=hidden name=print>
</form>

<?=$onload?>
<!-- WIDERPLANET PURCHASE SCRIPT START 2017.9.19 -->
<div id="wp_tg_cts" style="display:none;"></div>
<?php
$wptg_arr   = array();
$wptg_items = '';
foreach( $productArr as $prKey=>$prVal ){
    $wptg_arr[] = '{i:"'.$prVal['productcode'].'", t:"'.$prVal['productname'].'", p:"'.$prVal['price'].'", q:"'.$prVal['quantity'].'" }';
}
$wptg_items = implode( ',', $wptg_arr );
?>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
	return {
		wp_hcuid:"",  	
		ti:"37370",
		ty:"PurchaseComplete",
		device:"web"
		,items:[
			 <?=$wptg_items?>
		]
	};
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET PURCHASE SCRIPT END 2017.9.19 -->
<?
/*
list($countOldItem) = pmysql_fetch("SELECT count(basketidx) FROM tblbasket WHERE tempkey = '".$_ShopInfo->getTempkeySelectItem()."'");
if($countOldItem > 0){
	$selectItemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE tempkey='".$_ShopInfo->getTempkeySelectItem()."'";
	pmysql_query($selectItemQuery);
}
*/
?>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>