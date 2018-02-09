<?		
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath=$Dir.DataDir."shopimages/multi/";

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
if($quantity==0) $quantity=1;
$errmsg="";
$assemble_type=$_POST["assemble_type"];
$assemble_list=@str_replace("|","",$_POST["assemble_list"]);
$assembleuse=$_POST["assembleuse"];
$assemble_idx=(int)$_POST["assemble_idx"];

$package_idx=(int)$_POST["package_idx"];

//장바구니 인증키 확인
if(ord($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}

//장바구니담기, 바로구매
if(ord($quantity) && strlen($productcode)==18) {//장바구니 담기
	$rowcnt=$quantity;

	$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
	$sql.= "WHERE productcode='{$productcode}' ";

	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->display!="Y") {
			$errmsg="해당 상품은 판매가 되지 않는 상품입니다.\\n";
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

			if(empty($option1) && ord($row->option1))  $option1=1;
			if(empty($option2) && ord($row->option2))  $option2=1;
			if(ord($row->quantity)) {
				if ($rowcnt>$row->quantity) {
					if ($row->quantity>0)
						$errmsg.="해당 상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\\n";
					else
						$errmsg.= "해당 상품이 다른 고객의 주문으로 품절되었습니다.\\n";
				}
			}
			if(ord($row->option_quantity)) {
				$optioncnt = explode(",",ltrim($row->option_quantity,','));
				if($option2==0) $tmoption2=1;
				else $tmoption2=$option2;
				$optionvalue=$optioncnt[(($tmoption2-1)*10)+($option1-1)];
				if($optionvalue<=0 && $optionvalue!="")
					$errmsg.="해당 상품의 선택된 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
				else if($optionvalue<$quantity && $optionvalue!="")
					$errmsg.="해당 상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
			}
		}
	} else {
		$errmsg="해당 상품이 존재하지 않습니다.\\n";
	}
	pmysql_free_result($result);
} else {
	$errmsg = "구매수량이 잘못되었습니다.";
}

if(!$errmsg)
{
	
	// 이미 장바구니에 담긴 상품인지 검사하여 있으면 카운트만 증가.
	if (empty($option1))  $option1=0;
	if (empty($option2))  $option2=0;
	if (empty($opts))  $opts="0";
	if (empty($assemble_idx))  $assemble_idx=0;

	if($proassembleuse=="Y") {
		$assemaxsql = "SELECT MAX(assemble_idx) AS assemble_idx_max FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
		$assemaxsql.= "AND productcode='{$productcode}' ";
		$assemaxsql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
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
	} else {
		if (strlen($productcode)==18) {
			$vdate = date("YmdHis");
			$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$result = pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($row->cnt>=200) {
				$msg= "1. 장바구니에는 총 200개 까지만 담을 수 있습니다.<br>2. 새 상품을 담기 위해서는 기존 장바구니 상품을 삭제 후 담을 수 있습니다.<br><br>                    장바구니 페이지로 이동 하겠습니까?";
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
						'{$vdate}')";
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
						date,id) VALUES (
						'".$_ShopInfo->getTempkey()."',
						'{$productcode}',
						'{$option1}',
						'{$option2}',
						'{$opts}',
						'{$quantity}',
						'{$package_idx}',
						'{$assemble_idx_max}',
						'{$assemble_list}',
						'{$vdate}','".$_ShopInfo->getMemid()."')";
						pmysql_query($sql,get_db_conn());
				}
				$msg = "장바구니에 해당 상품을 등록하였습니다.";
			}
		}
	}
}
if($errmsg){
	echo $errmsg;
}else{
	echo $msg;
}
?>