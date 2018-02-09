<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$imagepath=$Dir.DataDir."shopimages/multi/";


$vip_type=$_REQUEST["vip_type"];
$staff_type=$_REQUEST["staff_type"];
$productcode=$_REQUEST["productcode"];
$qftype=$_REQUEST["qftype"];
$bttype=$_REQUEST["bttype"];
$opts=(int)$_REQUEST["opts"];
$option1=(int)$_REQUEST["option1"];
$option2=(int)$_REQUEST["option2"];
$mode=$_REQUEST["mode"];
$code=$_REQUEST["code"];
$ordertype=$_REQUEST["ordertype"];	//바로구매 구분 (바로구매시 => ordernow)
$quantity=(int)$_REQUEST["quantity"];	//구매수량
$constant_quantity = (int) $_REQUEST['constant_quantity']; //상품 수량 ( 무제한 참고용 ) 무제한 : 999999999
if($quantity==0) $quantity=1;
$errmsg="";
$assemble_type=$_POST["assemble_type"];
$assemble_list=@str_replace("|","",$_POST["assemble_list"]);
$assembleuse=$_POST["assembleuse"];
$assemble_idx=(int)$_POST["assemble_idx"];

$package_idx=(int)$_POST["package_idx"];
$buy_type = $_REQUEST["buy_type"];

$optionArr = $_REQUEST["optionArr"];
$priceArr = $_REQUEST["priceArr"];
//exdebug($priceArr);
$quantityArr = $_REQUEST["quantityArr"];

#옵션 2015 11 10 유동혁
$io_type = $_REQUEST["io_type"][$productcode]; // 옵션 종류 ( 0 필수옵션, 1 추가옵션 ) tblproduct_option.option_type
$io_id = $_REQUEST["io_id"][$productcode]; // 옵션 코드 tblproduct_option.option_code
$ct_qty = $_REQUEST["ct_qty"][$productcode];// 옵션 구매 수량 tblproduct_option.option_quantity
$io_price = $_REQUEST['io_price'][$productcode];// 옵션 가격 tblproduct_option.option_price
$io_value = $_REQUEST["io_value"][$productcode];// 옵션 값
$optionCtn = count($io_id);

//장바구니 인증키 확인
if(ord($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}
//장바구니담기, 바로구매
if(ord($quantity) ) {//장바구니 담기
	$rowcnt=$quantity;

	////////////////

	//$code=substr($productcode,0,12);
	//list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
	$code_a = substr($code,0,3);
	$code_b = substr($code,3,3);
	$code_c = substr($code,6,3);
	$code_d = substr($code,9,3);

	if(strlen($code_a)!=3) $code_a="000";
	if(strlen($code_b)!=3) $code_b="000";
	if(strlen($code_c)!=3) $code_c="000";
	if(strlen($code_d)!=3) $code_d="000";

	$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->group_code=="NO") {	//숨김 분류
			$errmsg='판매가 종료된 상품입니다.';
		} elseif($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			$errmsg='로그인 하셔야 장바구니에 담으실 수 있습니다.';
		} elseif(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			$errmsg='해당 분류의 접근 권한이 없습니다.';
		}
	}
	//////////////////
	$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check,staff_product,vip_product FROM tblproduct ";
	$sql.= "WHERE productcode='{$productcode}' ";

	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->vip_product != "1" and $row->staff_product!="1"){
			if($row->display!="Y") {
				$errmsg="해당 상품은 판매가 되지 않는 상품입니다!.\\n";
			}
		}

		if($row->group_check!="N") {
			if(ord($_ShopInfo->getMemid())>0) {
				$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
				$sqlgc.= "WHERE productcode='{$productcode}' ";
				$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
				$resultgc=pmysql_query($sqlgc,get_db_conn());
				if($rowgc=@pmysql_fetch_object($resultgc)) {
					if($rowgc->groupcheck_count<1) {
						$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
					}
					@pmysql_free_result($resultgc);
				} else {
					$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
				}
			} else {
				$errmsg="해당 상품은 회원 전용 상품입니다.\\n";
			}
		}
		if(ord($errmsg)==0) {
			$miniq=1;
			$maxq="?";
			if(ord($row->etctype)) {
				$etctemp = explode("",$row->etctype);
				for($i=0;$i<count($etctemp);$i++) {
					if(strpos($etctemp[$i],"MINIQ=")===0)     $miniq=substr($etctemp[$i],6);
					if(strpos($etctemp[$i],"MAXQ=")===0)      $maxq=substr($etctemp[$i],5);
				}
			}

			if(ord(dickerview($row->etctype,0,1))>0) {
				$errmsg="해당 상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\\n";
			}
		}
		if(ord($errmsg)==0) {
			if ($miniq!=1 && $miniq>1 && $rowcnt<$miniq)
				$errmsg="해당 상품은 최소 {$miniq}개 이상 주문하셔야 합니다.\\n";
			if ($maxq!="?" && $maxq>0 && $rowcnt>$maxq)
				$errmsg.="해당 상품은 최대 {$maxq}개 이하로 주문하셔야 합니다.\\n";

			if(ord($row->quantity)) {
				if ($rowcnt>$row->quantity) {
					if ($row->quantity>0)
						$errmsg.="해당 상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\\n";
					else
						$errmsg.= "해당 상품이 다른 고객의 주문으로 품절되었습니다.\\n";
				}
			}
			if( count($io_id) > 0 ) {
				#option quantity 추가
				for( $opi=0; $opi < $optionCtn; $opi++ ){
					$optQtySql = "SELECT option_quantity FROM tblproduct_option WHERE option_code = '".$io_id[$opi]."' ";
					$optQtyRes = pmysql_query( $optQtySql, get_db_conn() );
					$optQtyRow = pmysql_fetch_row( $optQtyRes );
					if( $optQtyRow[0] < $ct_qty[$opi] ){
						$errmsg.="해당 ".$io_value[$opi]."의 재고가 부족합니다. \\n";
					}
					pmysql_free_result( $optQtyRes );
				}
			}
		}
	} else {
		$errmsg="해당 상품이 존재하지 않습니다!!!.\\n";
	}
	pmysql_free_result($result);
} else {
	$errmsg = "구매수량이 잘못되었습니다.";
}

if(!$errmsg)
{

	// 이미 장바구니에 담긴 상품인지 검사하여 있으면 카운트만 증가.
	if (empty($opts))  $opts="0";
	if (empty($assemble_idx))  $assemble_idx=0;

	if($proassembleuse=="Y") {
		$assemaxsql = "SELECT MAX(assemble_idx) AS assemble_idx_max FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
		$assemaxsql.= "AND productcode='{$productcode}' ";
		//$assemaxsql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
		$assemaxsql.= "AND assemble_idx > 0 ";
		$assemaxresult = pmysql_query($assemaxsql,get_db_conn());
		$assemaxrow=@pmysql_fetch_object($assemaxresult);
		@pmysql_free_result($assemaxresult);
		$assemble_idx_max = $assemaxrow->assemble_idx_max+1;
	} else {
		$assemble_idx_max = 0;
	}


	$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='{$productcode}' ";
	$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
	$sql.= "AND assemble_idx = '{$assemble_idx}' ";
	$sql.= "AND package_idx = '{$package_idx}' ";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	
	pmysql_free_result($result);
	if ($row) {
		$msg = "이미 장바구니에 상품이 담겨있습니다.<br> 수량은 장바구니 가셔서 조절해 주세요.<br>                    장바구니 페이지로 이동 하겠습니까?";
		$msgList = "이미 장바구니에 상품이 담겨있습니다.\r\n수량은 장바구니 가셔서 조절해 주세요.\r\n장바구니 페이지로 이동 하겠습니까?";
		///////////////////////////////////////////  개행 \n 먹히도록
		$errmsg = "이미 장바구니에 상품이 담겨있습니다.";
	} else {
		if (strlen($productcode) > 0 ) {
			$vdate = date("YmdHis");
			$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$result = pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($row->cnt>=200) {
				$msg= "1. 장바구니에는 총 200개 까지만 담을 수 있습니다.<br>2. 새 상품을 담기 위해서는 기존 장바구니 상품을 삭제 후 담을 수 있습니다.<br><br>                    장바구니 페이지로 이동 하겠습니까?";
				$msgList = "1. 장바구니에는 총 200개 까지만 담을 수 있습니다.\r\n2. 새 상품을 담기 위해서는 기존 장바구니 상품을 삭제 후 담을 수 있습니다.\r\n장바구니 페이지로 이동 하겠습니까?";
			} else {
				if ( $optionCtn > 0 ) {
					for ($i = 0; $i < $optionCtn; $i++) {
						$multiOrderCount++;
						$ex_option = explode( chr(30) ,$io_id[$i]);
						$option1 = $ex_option[0];
						$option2 = $ex_option[1];
						$optionArrTmp = $io_id[$i];
						$quantityArrTmp = $ct_qty[$i];
						$priceArrTmp = $io_price[$i] * $ct_qty[$i];
						$opType = $io_type[$i];
						//상품수량 ( 추가상품일때는 상품 수량이 안들어간다 )
						if( $opType == '1' ){
							$quantityTmp = 0;
							// 상품을 따로 넣는다
							$sql = "INSERT INTO tblbasket(
								tempkey			,
								productcode		,
								optidxs			,
								quantity		,
								package_idx		,
								assemble_idx	,
								assemble_list	, ";
							// 회원일때 id 추가
							if( strlen($_ShopInfo->getMemid()) > 0 ) {
								$sql.= "id				, ";
							}

							$sql.= "
								date			
								) VALUES (
								'".$_ShopInfo->getTempkey()."',
								'{$productcode}',
								'{$opts}',
								'{$quantity}',
								'{$package_idx}',
								'{$assemble_idx_max}',
								'{$assemble_list}', ";
							// 회원일때 id 추가
							if( strlen($_ShopInfo->getMemid()) > 0 ) {
								$sql.= "'".$_ShopInfo->getMemid()."', ";
							}

							$sql.= "
								'{$vdate}'
								) ";
								pmysql_query($sql,get_db_conn());
						} else {
							$quantityTmp = $quantity;
						}
						if(strlen($_ShopInfo->getMemid())==0) {
								$sql = "INSERT INTO tblbasket(
								tempkey			,
								productcode		,
								opt1_idx		,
								opt2_idx		,
								optidxs			,
								quantity		,
								package_idx		,
								assemble_idx	,
								assemble_list	,
								optionarr		,
								quantityarr		,
								pricearr		,
								date			,
								op_type			) VALUES (
								'".$_ShopInfo->getTempkey()."',
								'{$productcode}',
								'{$option1}',
								'{$option2}',
								'{$opts}',
								'{$quantityTmp}',
								'{$package_idx}',
								'{$assemble_idx_max}',
								'{$assemble_list}',
								'{$optionArrTmp}',
								'{$quantityArrTmp}',
								'{$priceArrTmp}',
								'{$vdate}',
								'{$opType}' ) ";
								pmysql_query($sql,get_db_conn());
						}else{
								$sql = "INSERT INTO tblbasket(
								tempkey			,
								productcode		,
								opt1_idx		,
								opt2_idx		,
								optidxs			,
								quantity		,
								package_idx		,
								assemble_idx	,
								assemble_list	,
								optionarr		,
								quantityarr		,
								pricearr		,
								date			,
								id				,
								op_type			) VALUES (
								'".$_ShopInfo->getTempkey()."',
								'{$productcode}',
								'{$option1}',
								'{$option2}',
								'{$opts}',
								'{$quantityTmp}',
								'{$package_idx}',
								'{$assemble_idx_max}',
								'{$assemble_list}',
								'{$optionArrTmp}',
								'{$quantityArrTmp}',
								'{$priceArrTmp}',
								'{$vdate}',
								'".$_ShopInfo->getMemid()."',
								'{$opType}' )";
								pmysql_query($sql,get_db_conn());
						}


					}
				} else {
					if(strlen($_ShopInfo->getMemid())==0) {
							$sql = "INSERT INTO tblbasket(
							tempkey			,
							productcode		,
							opt1_idx		,
							opt2_idx		,
							optidxs			,
							quantity		,
							package_idx		,
							assemble_idx	,
							assemble_list	,
							optionarr		,
							quantityarr		,
							pricearr		,
							date			) VALUES (
							'".$_ShopInfo->getTempkey()."',
							'{$productcode}',
							'{$option1}',
							'{$option2}',
							'{$opts}',
							'{$quantity}',
							'{$package_idx}',
							'{$assemble_idx_max}',
							'{$assemble_list}',
							'{$optionArr}'		,
							'{$quantityArr}'	,
							'{$priceArr}'	,
							'{$vdate}' )";
							pmysql_query($sql,get_db_conn());
					}else{
							$sql = "INSERT INTO tblbasket(
							tempkey			,
							productcode		,
							opt1_idx		,
							opt2_idx		,
							optidxs			,
							quantity		,
							package_idx		,
							assemble_idx	,
							assemble_list	,
							optionarr		,
							quantityarr		,
							pricearr		,
							date			,
							id				) VALUES (
							'".$_ShopInfo->getTempkey()."',
							'{$productcode}',
							'{$option1}',
							'{$option2}',
							'{$opts}',
							'{$quantity}',
							'{$package_idx}',
							'{$assemble_idx_max}',
							'{$assemble_list}',
							'{$optionArr}'		,
							'{$quantityArr}'	,
							'{$priceArr}'	,
							'{$vdate}',
							'".$_ShopInfo->getMemid()."' ) ";
							pmysql_query($sql,get_db_conn());
					}
				}
				$msg = "장바구니에 해당 상품을 등록하였습니다.<br>장바구니 페이지로 이동 하겠습니까?";
				$msgList = "장바구니에 해당 상품을 등록하였습니다.\r\n장바구니 페이지로 이동 하겠습니까?";
			}
		}
	}
}

?>
<?if($buy_type == 'list'){?>
	<?=mb_convert_encoding($msgList, 'UTF-8', 'EUC-KR')?>
<?}else{?>
<html>
<head>
<title>장바구니 상품추가</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=UTF-8">
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
//window.moveTo(10,10);
//window.resizeTo(392,265);

function go_basket() {

	if(typeof(opener)=="object" && opener!=null) {
		opener.location.href="<?=$Dir.FrontDir?>basket.php";
		window.close();
	} else {
		window.open("<?=$Dir.FrontDir?>basket.php");
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
		<p class="title">장바구니 담기</p>
		<a href="javascript:window.close();" class="btn_close"></a>
	</div>

	<div class="popup_cart_go">
		<img src="../img/icon/icon_pop_cart.gif" alt="" />
		<p class="txt">
			장바구니에 해당 상품을 등록하였습니다. <br />
			장바구니 페이지로 이동 하겠습니까?
		</p>
	</div>
<?if(!$errmsg){?>
	<div class="btn_area">
		<a href="javascript:go_basket()" class="go_cart">장바구니로 이동</a>
		<a href="javascript:window.close();" class="gray">계속 쇼핑하기</a>
	</div>
<?}else{?>
	<div class="btn_area">
		<p  class="txt"><?=substr($errmsg,0,strlen($errmsg)-1)?></p>
		<a href="javascript:window.close();" class="gray">계속 쇼핑하기</a>
	</div>
<?}?>
</div>
<!-- 전환페이지 설정 -->
 <script type="text/javascript" src="http://wcs.naver.net/wcslog.js"> </script> 
 <script type="text/javascript">
var _nasa={};
 _nasa["cnv"] = wcs.cnv("3","<?=$productcode?>"); // 전환유형, 전환가치 설정해야함. 설치매뉴얼 참고
</script>

</body>
</html>
<?}?>