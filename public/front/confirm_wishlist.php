<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$check_falg=$_POST["check_falg"];

if(strlen($_ShopInfo->getMemid())==0) {
	if(!$check_falg){
		echo "<html></head><body onload=\"window.close()\"></body></html>";exit;
	}else{
		echo mb_convert_encoding("로그인이 필요한 서비스입니다. ", 'UTF-8', 'EUC-KR');exit;
	}
}

$productcode=$_POST["productcode"];
$code = $_POST['code'];
$opts=$_POST["opts"];

#옵션 2015 11 10 유동혁
$io_type = $_REQUEST["io_type"][$productcode]; // 옵션 종류 ( 0 필수옵션, 1 추가옵션 ) tblproduct_option.option_type
$io_id = $_REQUEST["io_id"][$productcode]; // 옵션 코드 tblproduct_option.option_code
$ct_qty = $_REQUEST["ct_qty"][$productcode];// 옵션 구매 수량 tblproduct_option.option_quantity
$io_price = $_REQUEST['io_price'][$productcode];// 옵션 가격 tblproduct_option.option_price
$io_value = $_REQUEST["io_value"][$productcode];// 옵션 값
$optionCtn = count($io_id);


$ajaxMsg = "";

if (empty($opts))  $opts="0";
//if (empty($option1))  $option1=0;
//if (empty($option2))  $option2=0;

if(strlen($productcode) > 0 ) {
	list($code_a,$code_b,$code_c,$code_d) = sscanf($productcode,'%3s%3s%3s%3s');

///////////////////////////////////
	$sql = "select count(*) as wishcnt from tblwishlist WHERE productcode='{$productcode}' ";

	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->wishcnt > 0){		
		//$errmsg = '이미 wishlist에 존재하는 상품입니다.';
		$ajaxMsg = '이미 wishlist에 존재하는 상품입니다.';
	}
///////////////////////////////////
	/*$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
	$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	$result=pmysql_query($sql,get_db_conn());
	*/
	$sql = "
		SELECT
		a.*,b.c_maincate,b.c_category
		FROM tblproductcode a
		,tblproductlink b
		WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
		AND group_code = ''
		AND c_productcode = '{$productcode}'
	";
	//exdebug($sql);
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		if($row->c_maincate == 1){
			$mainCate = $row;
		}
		$cateProduct[] = $row;
	}	
	//if($row=pmysql_fetch_object($result)) {
	if($cateProduct[0]){
		if($cateProduct[0]->group_code=="NO") {	//숨김 분류
			//alert_go('판매가 종료된 상품입니다.','c');
			$errmsg = "판매가 종료된 상품입니다.";
			$ajaxMsg = "판매가 종료된 상품입니다.";
		} else if(ord($cateProduct[0]->group_code) && $cateProduct[0]->group_code!="ALL" && $cateProduct[0]->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			//alert_go('해당 분류의 접근 권한이 없습니다.','c');
			$errmsg = "해당 분류의 접근 권한이 없습니다.";
			$ajaxMsg = "해당 분류의 접근 권한이 없습니다.";
		}
	} else {
		//alert_go('해당 분류가 존재하지 않습니다.','c');
		$errmsg = "해당 분류가 존재하지 않습니다."; 
		$ajaxMsg = "해당 분류가 존재하지 않습니다.";
	}
	pmysql_free_result($result);

	$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
	$sql.= "WHERE productcode='{$productcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->display!="Y") {
			$errmsg="해당 상품은 판매가 되지 않는 상품입니다.\\n";
			$ajaxMsg = "해당 상품은 판매가 되지 않는 상품입니다.";
		}
		if($row->group_check!="N") {
			if(strlen($_ShopInfo->getMemid())>0) {
				$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
				$sqlgc.= "WHERE productcode='{$productcode}' ";
				$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
				$resultgc=pmysql_query($sqlgc,get_db_conn());
				if($rowgc=@pmysql_fetch_object($resultgc)) {
					if($rowgc->groupcheck_count<1) {
						$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
						$ajaxMsg = "해당 상품은 지정 등급 전용 상품입니다.";
					}
					@pmysql_free_result($resultgc);
				} else {
					$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
					$ajaxMsg = "해당 상품은 지정 등급 전용 상품입니다.";
				}
			} else {
				$errmsg="해당 상품은 회원 전용 상품입니다.\\n";
				$ajaxMsg = "해당 상품은 회원 전용 상품입니다.";
			}
		}
		if(ord($errmsg)==0) {
			if(strlen(dickerview($row->etctype,0,1))>0) {
				$errmsg="해당 상품은 판매가 되지 않습니다.\\n";
				$ajaxMsg = "해당 상품은 판매가 되지 않습니다.";
			}
		}
		if(empty($option1) && ord($row->option1))  $option1=1;
		if(empty($option2) && ord($row->option2))  $option2=1;
	} else {
		$errmsg="해당 상품이 존재하지 않습니다.\\n";
		$ajaxMsg = "해당 상품이 존재하지 않습니다.";
	}
	pmysql_free_result($result);
	
	if(ord($errmsg) && !$check_falg) {
		echo "<html></head><body onload=\"alert('{$errmsg}');window.close()\"></body></html>";
		exit;
	}

	$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
	$result2=pmysql_query($sql,get_db_conn());
	$row2=pmysql_fetch_object($result2);
	$totcnt=$row2->totcnt;
	pmysql_free_result($result2);
	$maxcnt=100;  
	if($totcnt>=$maxcnt) {
		$sql = "SELECT b.productcode FROM tblwishlist a, tblproduct b ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
		$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
		$sql.= "AND b.display='Y' ";
		$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "GROUP BY b.productcode ";
		$result2=pmysql_query($sql,get_db_conn());
		$i=0;
		$wishprcode="";
		while($row2=pmysql_fetch_object($result2)) {
			$wishprcode.="'{$row2->productcode}',";
			$i++;
		}
		pmysql_free_result($result2);
		$totcnt=$i;
		$wishprcode=rtrim($wishprcode,',');
		if(ord($wishprcode)) {
			$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$sql.= "AND productcode NOT IN ({$wishprcode}) ";
			pmysql_query($sql,get_db_conn());
			if(!$check_falg) echo "<html></head><body onload=\"if(typeof(opener.setFollowFunc)!='undefined') { opener.setFollowFunc('Wishlist','selectmenu'); }\"></body></html>";
		}
	}
		if($totcnt<$maxcnt) {
			if ( $optionCtn > 0 ) {
				for ($i = 0; $i < $optionCtn; $i++) {
					$multiOrderCount++;
					$ex_option = explode( chr(30) , trim( $io_id[$i] ) );
					$option1 = $ex_option[0];
					$option2 = $ex_option[1];
					$opType = $io_type[$i];
					pmysql_free_result( $opTypeRes );
					if(strlen($_ShopInfo->getMemid())>0) {
						$sql = "INSERT INTO tblwishlist (
						id			,
						productcode	,
						opt1_idx	,
						opt2_idx	,
						optidxs		,
						date		,
						op_type		) VALUES (
						'".$_ShopInfo->getMemid()."', 
						'{$productcode}', 
						'{$option1}', 
						'{$option2}', 
						'{$opts}', 
						'".date("YmdHis")."',
						'{$opType}'		)"; 
						pmysql_query($sql,get_db_conn());
					}
				}
			}else{
				if(strlen($_ShopInfo->getMemid())>0) {
					$sql = "INSERT INTO tblwishlist (
					id			,
					productcode	,
					opt1_idx	,
					opt2_idx	,
					optidxs		,
					date		
					) VALUES (
					'".$_ShopInfo->getMemid()."', 
					'{$productcode}', 
					'{$option1}', 
					'{$option2}', 
					'{$opts}', 
					'".date("YmdHis")."'
					)"; 
					pmysql_query($sql,get_db_conn());
				}
			}
		if(!$check_falg) echo "<html></head><body onload=\"if(typeof(opener.setFollowFunc)!='undefined') { opener.setFollowFunc('Wishlist','selectmenu'); }\"></body></html>";
	} else {
		if(!$check_falg) alert_go("WishList에는 {$maxcnt}개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.",'c');
		$ajaxMsg = "WishList에는 {$maxcnt}개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.";
	}
} else {
	if(!$check_falg){
		echo "<html></head><body onload=\"window.close()\"></body></html>";exit;
	}
	$ajaxMsg = "상품 코드가 존재하지 않습니다.";
}
if($check_falg){
	if($ajaxMsg){
		echo $ajaxMsg;
	}else{
		echo "해당 상품이 위시리스트 상품목록에 등록되었습니다.";
	}
	exit;
}
?>
<html>
<head>
<title>위시리스트 상품추가</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<script type="text/javascript">
<!--
window.moveTo(10,10);
/*window.resizeTo(392,265);*/

function go_wishlist() {
	if(typeof(opener)=="object") {
		opener.location.href="<?=$Dir.FrontDir?>wishlist.php";
		window.close();
	} else {
		window.open("<?=$Dir.FrontDir?>wishlist.php");
		window.close();
	}
}
//-->
</SCRIPT>
</head>
<link rel="stylesheet" href="../css/oryany.css" />
<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">

<div class="popup_def_wrap">
	<div class="title_wrap">
		<p class="title">위시리스트 상품 추가</p>
		<a href="javascript:window.close();" class="btn_close"></a>
	</div>

	<div class="popup_cart_go">
		<img src="../img/icon/icon_pop_wishlist.gif" alt="" />
		<p class="txt">
			해당 상품이 위시리스트 상품목록에 등록되었습니다. <br />
			지금 등록된 목록에서 확인하시겠습니까?
		</p>
	</div>

	

<?if(!$ajaxMsg){?>
	<div class="btn_area">
		<a href="javascript:go_wishlist()" class="go_cart">지금확인 이동</a>
		<a href="javascript:window.close();" class="gray">닫기</a>
	</div>
<?}else{?>
	<div class="btn_area">
		<p  class="txt"><?=substr($ajaxMsg,0,strlen($ajaxMsg)-1)?></p>
		<br>
		<a href="javascript:window.close();" class="gray">닫기</a>
	</div>
<?}?>
<!--
	<div class="btn_area">
		<a href="javascript:go_wishlist()" class="go_cart">지금확인</a>
		<a href="javascript:window.close();" class="gray">아니오</a>
	</div>
-->
</div>

<!-- <table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td><IMG src="<?=$Dir?>images/common/confirm_wishlist_title.gif" border="0"></td>
</tr>
<tr>
	<td align="center" style="padding-top:10px;padding-bottom:10px;"><img src="<?=$Dir?>images/common/confirm_wishlist_text01.gif" border="0"></td>
</tr>
<tr>
	<td><hr size="1" noshade color="#F3F3F3"></td>
</tr>
<tr>
	<td align="center"><A HREF="javascript:go_wishlist()"><img src="<?=$Dir?>images/common/confirm_wishlist_btn1.gif" border="0" vspace="5"></a><A HREF="javascript:window.close();"><img src="<?=$Dir?>images/common/confirm_wishlist_btn2.gif" border="0" hspace="5" vspace="5"></a></td>
</tr>
</table> -->
</body>
</html>
