<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");
if(!$product) $product = new PRODUCT();

$mode=$_REQUEST["mode"];
$code=$_POST["code"];
$ordertype=$_POST["ordertype"];	//바로구매 구분 (바로구매시 => ordernow)
$opts=$_POST["opts"];	//옵션그룹 선택된 항목 (예:1,1,2,)
$option1=$_POST["option1"];	//옵션1
$option2=$_POST["option2"];	//옵션2
$quantity=(int)$_REQUEST["quantity"];	//구매수량
//error_log($_REQUEST["quantity"]);
if($quantity==0) $quantity=1;
$productcode=$_REQUEST["productcode"];
$vip_type=$_REQUEST["vip_type"];
$staff_type=$_REQUEST["staff_type"];

$orgquantity=$_POST["orgquantity"];
$orgoption1=$_POST["orgoption1"];
$orgoption2=$_POST["orgoption2"];

$assemble_type=$_POST["assemble_type"];
$assemble_list=@str_replace("|","",$_POST["assemble_list"]);
$assembleuse=$_POST["assembleuse"];
$assemble_idx=(int)$_POST["assemble_idx"];

$optionArr = $_POST["optionArr"];
$priceArr = $_POST["priceArr"];
$quantityArr = $_POST["quantityArr"];

#옵션 변환
$option_type = $_POST['option_type'];
$option_code = $_POST['option_code'];
$option_value = $_POST['option_value'];
$option_quantity = $_POST['option_quantity'];

$package_idx=(int)$_POST["package_idx"];
$multiOrderCount = 0;
$jsmultiOrderCount = 0;
if($assemble_idx==0) {
	if($assembleuse=="Y") {
		$assemble_idx="-9999";
	}
} else {
	$assembleuse="Y";
}


//장바구니 인증키 확인
if(ord($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
	$_ShopInfo->setTempkeySelectItem($_data->ETCTYPE["BASKETTIME"]);
}

list($countOldItem) = pmysql_fetch("SELECT count(basketidx) FROM tblbasket WHERE tempkey = '".$_ShopInfo->getTempkeySelectItem()."'");
if($countOldItem > 0){
	$selectItemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE tempkey='".$_ShopInfo->getTempkeySelectItem()."'";
	pmysql_query($selectItemQuery);
}

if($_ShopInfo->getMemid()){

	list($countOldItem2) = pmysql_fetch("SELECT count(basketidx) FROM tblbasket WHERE id = '".$_ShopInfo->getMemid()."'");
	if($countOldItem2 > 0){
		$selectItemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE id = '".$_ShopInfo->getMemid()."'";
		pmysql_query($selectItemQuery);
	}

}

//회원정보

$group_code=$_ShopInfo->memgroup;

if(ord($group_code) && $group_code!=NULL) {
	$sql = "SELECT * FROM tblmembergroup WHERE group_code='{$group_code}' AND SUBSTR(group_code,1,1)!='M' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)){
		$group_type=substr($row->group_code,0,2);
		$group_level=$row->group_level;
		$group_addmoney=$row->group_addmoney;
		$group_usemoney=$row->group_usemoney;
	}
	pmysql_free_result($result);
}

//비즈스프링 배열 초기화
//echo "<script>var bizAllClearArray = new Array();</script>";

if($mode!="del" && $mode!="clear" && strlen($productcode)==18) {
	if(ord($code)==0) {
		$code=substr($productcode,0,12);
	}
	list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
	if(strlen($code_a)!=3) $code_a="000";
	if(strlen($code_b)!=3) $code_b="000";
	if(strlen($code_c)!=3) $code_c="000";
	if(strlen($code_d)!=3) $code_d="000";
	
	//2015 02 09 유동혁 상품코드를 링크에서 가져옴
	//$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	//$result=pmysql_query($sql,get_db_conn());
	
	$sql = "
		SELECT 
		a.*,b.c_maincate 
		FROM tblproductcode a 
		,tblproductlink b
		WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
		AND group_code = ''
		AND c_productcode = '{$productcode}'
	";
	
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		if($row->c_maincate == 1){
			$mainCate = $row;
		}
		$cateProduct[] = $row;
	}
	
	if($cateProduct) {
	/*
	if($row=pmysql_fetch_object($result)) {  
		if($row->group_code=="NO") {	//숨김 분류
			alert_go('판매가 종료된 상품입니다.',$Dir.FrontDir."basket.php");		
		} elseif($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			alert_go('로그인 하셔야 장바구니에 담으실 수 있습니다.',$Dir.FrontDir."basket.php");
		} elseif(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			alert_go('해당 분류의 접근 권한이 없습니다.',$Dir.FrontDir."basket.php");
		}
	*/
		if(count($cateProduct)==0 || !$cateProduct){
			$group_sql = "
				SELECT 
				a.group_code
				FROM tblproductcode a 
				,tblproductlink b
				WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
				AND group_code != ''
				AND c_productcode = '{$productcode}'
				GROUP BY a.group_code
			";
			$gruop_res = pmysql_query($group_sql,get_db_conn());
			while($gruop_row = pmysql_fetch_object($gruop_res)){
				if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
					Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
					exit;
				}else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
					alert_go('해당 분류의 접근 권한이 없습니다.',-1);
				}
			}
			alert_go('판매가 종료된 상품입니다.',$Dir.MainDir."main.php");
		}
		//Wishlist 담기
		if($mode=="wishlist") {
			if(strlen($_ShopInfo->getMemid())==0) {	//비회원
				alert_go('로그인을 하셔야 본 서비스를 이용하실 수 있습니다.',$Dir.FrontDir."login.php?chUrl=".getUrl());
			}
			$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$result2=pmysql_query($sql,get_db_conn());
			$row2=pmysql_fetch_object($result2);
			$totcnt=$row2->totcnt;
			pmysql_free_result($result2);
			$maxcnt=20;
			if($totcnt>=$maxcnt) {
				$sql = "SELECT b.productcode FROM tblwishlist a, tblproduct b ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
				$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
				if($vip_type != "1" or $staff_type !="1"){
					$sql.= "AND b.display='Y' ";
				}
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
					$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode NOT IN ({$wishprcode}) ";
					pmysql_query($sql,get_db_conn());
				}
			}
			if($totcnt<$maxcnt) {
				$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='{$productcode}' ";
				$result2=pmysql_query($sql,get_db_conn());
				$row2=pmysql_fetch_object($result2);
				$cnt=$row2->cnt;
				pmysql_free_result($result2);
				if($cnt>0) {
					$sql = "UPDATE tblwishlist SET date='".date("YmdHis")."' ";
					$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
					$sql.= "AND productcode='{$productcode}' ";
					$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
					pmysql_query($sql,get_db_conn());

					alert_go('WishList에 이미 등록된 상품입니다.',-1);
				} else {
					$sql = "INSERT INTO tblwishlist(id,productcode) VALUES (
					'".$_ShopInfo->getMemid()."',
					'{$productcode}')";
					pmysql_query($sql,get_db_conn());
					alert_go('WishList에 해당 상품을 등록하였습니다.',-1);
				}
			} else {
				alert_go("WishList에는 {$maxcnt}개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.",-1);
			}
		}
	} else {
		alert_go('해당 분류가 존재하지 않습니다.',$Dir.FrontDir."basket.php");
	}
	pmysql_free_result($result);
} elseif($mode=="clear") {	//장바구니 비우기
	if($_ShopInfo->getMemid() != null || $_ShopInfo->getMemid() != ""){
		$sql = "DELETE FROM tblbasket WHERE id='".$_ShopInfo->getMemid()."' ";
		pmysql_query($sql,get_db_conn());
	}
	$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";

	# 장바구니 비울 시 비즈스프링에도 제거 상품 정보 전송
	if($biz[bizNumber]){
		if($_ShopInfo->getMemid() == null || $_ShopInfo->getMemid() == ""){
			$sqlBiz = "SELECT b.productname FROM tblbasket a LEFT JOIN tblproduct b ON a.productcode = b.productcode WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
		}else{
			$sqlBiz = "SELECT b.productname FROM tblbasket a LEFT JOIN tblproduct b ON a.productcode = b.productcode WHERE a.id='".$_ShopInfo->getMemid()."' ";
		}
		$resultBiz = pmysql_query($sqlBiz, get_db_conn());

		while($rowBiz=pmysql_fetch_object($resultBiz)) {
			echo "<script>bizAllClearArray.push('".$rowBiz->productname."');</script>";
		}
	}

	pmysql_query($sql,get_db_conn());
}

$basketsql2 = "SELECT a.productcode,a.package_idx,a.quantity,c.package_list,c.package_title,c.package_price ";
$basketsql2.= "FROM tblbasket AS a, tblproduct AS b, tblproductpackage AS c ";
$basketsql2.= "WHERE a.productcode=b.productcode ";
$basketsql2.= "AND b.package_num=c.num ";

if($_ShopInfo->getMemid() == null || $_ShopInfo->getMemid() == ""){
	$basketsql2.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
}else{
	$basketsql2.= "AND a.id='".$_ShopInfo->getMemid()."' ";
}


$basketsql2.= "AND a.package_idx>0 ";
if($vip_type != "1" or $staff_type="1"){
	$basketsql2.= "AND b.display = 'Y' ";
}

$basketresult2 = pmysql_query($basketsql2,get_db_conn());
while($basketrow2=@pmysql_fetch_object($basketresult2)) {
	if(ord($basketrow2->package_title) && ord($basketrow2->package_idx) && $basketrow2->package_idx>0) {
		$package_title_exp = explode("",$basketrow2->package_title);
		$package_price_exp = explode("",$basketrow2->package_price);
		$package_list_exp = explode("", $basketrow2->package_list);

		$title_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx] = $package_title_exp[$basketrow2->package_idx];

		if(strlen($package_list_exp[$basketrow2->package_idx])>1) {
			$basketsql3 = "SELECT productcode,quantity,productname,sellprice FROM tblproduct ";
			$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",ltrim($package_list_exp[$basketrow2->package_idx],','))."') ";
			if($vip_type != "1" or $staff_type!="1"){
				$basketsql3.= "AND display = 'Y' ";
			}
			$basketresult3 = pmysql_query($basketsql3,get_db_conn());
			$sellprice_package_listtmp=0;
			while($basketrow3=@pmysql_fetch_object($basketresult3)) {
				$assemble_proquantity[$basketrow3->productcode]+=$basketrow2->quantity;
				$productcode_package_listtmp[] = $basketrow3->productcode;
				$quantity_package_listtmp[] = $basketrow3->quantity;
				$productname_package_listtmp[] = $basketrow3->productname;
				$sellprice_package_listtmp+= $basketrow3->sellprice;
			}
			
			@pmysql_free_result($basketresult3);

			if(count($productcode_package_listtmp)>0) {  //장바구니 패키지 상품 정보 출력시 필요한 정보
				$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=0;
				if((int)$sellprice_package_listtmp>0) {
					$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=(int)$sellprice_package_listtmp;
					if(ord($package_price_exp[$basketrow2->package_idx])) {
						$package_price_expexp = explode(",",$package_price_exp[$basketrow2->package_idx]);
						if(ord($package_price_expexp[0]) && $package_price_expexp[0]>0) {
							$sumsellpricecal=0;
							if($package_price_expexp[1]=="Y") {
								$sumsellpricecal = ((int)$sellprice_package_listtmp*$package_price_expexp[0])/100;
							} else {
								$sumsellpricecal = $package_price_expexp[0];
							}
							if($sumsellpricecal>0) {
								if($package_price_expexp[2]=="Y") {
									$sumsellpricecal = $sellprice_package_listtmp-$sumsellpricecal;
								} else {
									$sumsellpricecal = $sellprice_package_listtmp+$sumsellpricecal;
								}
								if($sumsellpricecal>0) {
									if($package_price_expexp[4]=="F") {
										$sumsellpricecal = floor($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
									} elseif($package_price_expexp[4]=="R") {
										$sumsellpricecal = round($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
									} else {
										$sumsellpricecal = ceil($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
									}
									$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=$sumsellpricecal;
								}
							}
						}
					}
				}

				$productcode_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productcode_package_listtmp;
				$productname_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productname_package_listtmp;
			}

			if(ord($package_productcode_tmp)==0) { // 수량 및 옵션을 변경항 상품의 패키지 구성 상품 정보(재고 체크에서 필요)
				if($mode!="clear" && $mode!="wishlist" && $mode!="del" && ord($quantity) && strlen($productcode)==18) {
					if(count($productcode_package_listtmp)>0 && $basketrow2->productcode==$productcode && $basketrow2->package_idx==$package_idx && ord($package_idx) && (int)$package_idx>0) {
						$package_productcode_tmp = implode("",$productcode_package_listtmp);
						$package_quantity_tmp = implode("",$quantity_package_listtmp);
						$package_productname_tmp = implode("",$productname_package_listtmp);
					}
				}
			}
			$productcode_package_listtmp=array();
			$quantity_package_listtmp=array();
			$productname_package_listtmp=array();
		}
	}
}
@pmysql_free_result($basketresult2);

$errmsg="";

//선택상품 삭제
if($mode=='del_chk'){

		$del_idx = substr($_GET['del_idx'],0,-1);
		$del_idx = str_replace("|","','",$del_idx);
		$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND basketidx in('".$del_idx."')";
		pmysql_query($sql,get_db_conn());

}else if($mode=='wish_chk'){

}

if($mode!="clear" && $mode!="wishlist" && strlen($productcode)==18) {
	//해당상품삭제, 장바구니담기, 바로구매, 수량 업데이트, 원샷구매시에...
	if($mode!="del" && ord($quantity) && ($quantity<=0 || $quantity>9999) && strlen($productcode)==18) {
		alert_go('구매수량이 잘못되었습니다.',-1); 
	}

	//장바구니 담기 또는 수량/옵션 업데이트
	if($mode!="del" && ord($quantity) && strlen($productcode)==18) {
		$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,option_ea,etctype,group_check,assembleuse,package_num FROM tblproduct ";
		$sql.= "WHERE productcode='{$productcode}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($vip_type != "1" and $staff_type!="1"){
				if($row->display!="Y") {
					$errmsg="해당 상품은 판매가 되지 않는 상품입니다.\\n";
				}
			}

			$proassembleuse = $row->assembleuse;

			if($mode=="upd") {

				$sql2 = "SELECT SUM(quantity) as quantity FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
				$sql2.= "AND productcode='{$productcode}' ";
				$sql2.= "GROUP BY productcode ";
				$result2 = pmysql_query($sql2,get_db_conn());
				if($row2 = pmysql_fetch_object($result2)) {
					$rowcnt=$row2->quantity;
				} else {
					$rowcnt=0;
				}


				pmysql_free_result($result2);

				$charge_quantity = -($orgquantity-$quantity);
				$rowcnt=$rowcnt+$charge_quantity;

				if($proassembleuse=="Y") { // 조립/코디 상품 등록에 따른 구성상품 체크
					$assemsql = "SELECT * FROM tblassembleproduct ";
					$assemsql.= "WHERE productcode='{$productcode}' ";
					$assemresult=pmysql_query($assemsql,get_db_conn());
					if(!$assemrow=@pmysql_fetch_object($assemresult)) {
						$errmsg="현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.\\n";
					} else {
						$assemble_type_exp = explode("",$assemrow->assemble_type);
						$assemble_list_exp = explode("",$assemble_list);
					}
				}
			} else {

				$rowcnt=$quantity;
				$charge_quantity=$quantity;

				if($proassembleuse=="Y") { // 조립/코디 상품 등록에 따른 구성상품 체크
					if(ord($assemble_type)) {
						$assemsql = "SELECT * FROM tblassembleproduct ";
						$assemsql.= "WHERE productcode='{$productcode}' ";
						$assemresult=pmysql_query($assemsql,get_db_conn());
						if(!$assemrow=@pmysql_fetch_object($assemresult)) {
							alert_go('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.',-1);
						} else {
							$assemble_type_exp = explode("",$assemrow->assemble_type);
							$assemble_list_exp = explode("",$assemble_list);
							$assemble_list_count=0;
							for($i=1; $i<count($assemble_type_exp); $i++) {
								if(ord($assemble_list_exp[$i])) {
									$assemble_list_count++;
								} else {
									if($assemble_type_exp[$i]=="Y") {
										alert_go('필수 구성 상품을 선택해 주세요.',-1);
									}
								}
							}

							if($assemble_list_count>0) {
								$assemprosql = "SELECT COUNT(productcode) AS productcode_cnt FROM tblproduct ";
								$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
								if($vip_type != "1" or $staff_type!="1"){
									$assemprosql.= "AND display = 'Y' ";
								}
								$assemproresult=pmysql_query($assemprosql,get_db_conn());
								if($assemprorow=@pmysql_fetch_object($assemproresult)) {
									if($assemble_list_count!=$assemprorow->productcode_cnt) {
										alert_go('선택하신 구성 상품중 판매가 되지 않는 상품이 있습니다. 새로고침 후 다시 등록해 주세요.',-1);
									}
								}
								@pmysql_free_result($assemproresult);
							} else {
								alert_go('구성 상품을 하나 이상은 선택해야만 구매할 수 있습니다.',-1);
							}
						}
					} else {
						alert_go('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.',-1);
					}
				}
//				if(isdev()){
//					print_r($_REQUEST);

//					echo $row->option_ea;
					if($row->option_ea && !$row->option_quantity){
						$tmp_option_ea = explode(",",$row->option_ea);
						$rowcnt = $tmp_option_ea[($option1-1)] * $rowcnt;
					}
//				}
				if(ord($package_productcode_tmp)==0 && ord($package_idx) && (int)$package_idx>0) { // 장바구니 담길 상품의 패키지 정보
					$basketsql2 = "SELECT b.package_list,b.package_title,b.package_price ";
					$basketsql2.= "FROM tblproduct AS a, tblproductpackage AS b ";
					$basketsql2.= "WHERE a.package_num=b.num ";
					$basketsql2.= "AND a.productcode='{$productcode}' ";
					if($vip_type != "1" or $staff_type!="1"){
						$basketsql2.= "AND a.display = 'Y' ";
					}
					$basketresult2 = pmysql_query($basketsql2,get_db_conn());
					if($basketrow2=@pmysql_fetch_object($basketresult2)) {
						if(ord($basketrow2->package_title) && ord($package_idx) && $package_idx>0) {
							$package_title_exp = explode("",$basketrow2->package_title);
							$package_price_exp = explode("",$basketrow2->package_price);
							$package_list_exp = explode("", $basketrow2->package_list);

							$title_package_listtmp[$productcode][$package_idx] = $package_title_exp[$package_idx];

							if(strlen($package_list_exp[$package_idx])>1) {
								$basketsql3 = "SELECT productcode,quantity,productname,sellprice FROM tblproduct ";
								$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",ltrim($package_list_exp[$package_idx],','))."') ";
								if($vip_type != "1" or $staff_type!="1"){
									$basketsql3.= "AND display = 'Y' ";
								}

								$basketresult3 = pmysql_query($basketsql3,get_db_conn());
								$sellprice_package_listtmp=0;
								while($basketrow3=@pmysql_fetch_object($basketresult3)) {
									$assemble_proquantity[$basketrow3->productcode]+=$basketrow2->quantity;
									$productcode_package_listtmp[] = $basketrow3->productcode;
									$quantity_package_listtmp[] = $basketrow3->quantity;
									$productname_package_listtmp[] = $basketrow3->productname;
									$sellprice_package_listtmp+= $basketrow3->sellprice;
								}
								@pmysql_free_result($basketresult3);

								if(count($productcode_package_listtmp)>0) {  //장바구니 패키지 상품 정보 출력시 필요한 정보
									$price_package_listtmp[$productcode][$package_idx]=0;
									if((int)$sellprice_package_listtmp>0) {
										$price_package_listtmp[$productcode][$package_idx]=(int)$sellprice_package_listtmp;
										if(ord($package_price_exp[$package_idx])) {
											$package_price_expexp = explode(",",$package_price_exp[$package_idx]);
											if(ord($package_price_expexp[0]) && $package_price_expexp[0]>0) {
												$sumsellpricecal=0;
												if($package_price_expexp[1]=="Y") {
													$sumsellpricecal = ((int)$sellprice_package_listtmp*$package_price_expexp[0])/100;
												} else {
													$sumsellpricecal = $package_price_expexp[0];
												}
												if($sumsellpricecal>0) {
													if($package_price_expexp[2]=="Y") {
														$sumsellpricecal = $sellprice_package_listtmp-$sumsellpricecal;
													} else {
														$sumsellpricecal = $sellprice_package_listtmp+$sumsellpricecal;
													}
													if($sumsellpricecal>0) {
														if($package_price_expexp[4]=="F") {
															$sumsellpricecal = floor($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
														} elseif($package_price_expexp[4]=="R") {
															$sumsellpricecal = round($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
														} else {
															$sumsellpricecal = ceil($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
														}
														$price_package_listtmp[$productcode][$package_idx]=$sumsellpricecal;
													}
												}
											}
										}
									}

									$productcode_package_list[$productcode][$package_idx] = $productcode_package_listtmp;
									$productname_package_list[$productcode][$package_idx] = $productname_package_listtmp;

									$package_productcode_tmp = implode("",$productcode_package_listtmp);
									$package_quantity_tmp = implode("",$quantity_package_listtmp);
									$package_productname_tmp = implode("",$productname_package_listtmp);

									$productcode_package_listtmp=array();
									$quantity_package_listtmp=array();
									$productname_package_listtmp=array();
								}
							}
						}
					}
					@pmysql_free_result($basketresult2);
				}
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

				if(strlen(dickerview($row->etctype,0,1))>0) {
					$errmsg="해당 상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\\n";
				}
			}
			if(ord($errmsg)==0) {
				$temPquantityArr = explode("||",trim($quantityArr));
				if (sizeof($temPquantityArr) > 0) {
					for ($s=0; $s<sizeof($temPquantityArr); $s++) {
						$rowcnt = $rowcnt + $temPquantityArr[$s];
					}
				}
				if ($miniq!=1 && $miniq>1 && $rowcnt<$miniq)
					$errmsg="해당 상품은 최소 {$miniq}개 이상 주문하셔야 합니다.\\n";
				if ($maxq!="?" && $maxq>0 && $rowcnt>$maxq)
					$errmsg.="해당 상품은 최대 {$maxq}개 이하로 주문하셔야 합니다.\\n";

				if(empty($option1) && ord($row->option1))  $option1=1;
				if(empty($option2) && ord($row->option2))  $option2=1;
				if(ord($row->quantity)) {
					if ($rowcnt>$row->quantity) {
						if ($row->quantity>0){
							$errmsg="해당 상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\\n";
						}else
							$errmsg= "해당 상품이 다른 고객의 주문으로 품절되었습니다.\\n";
					}
				}

				///////////////////////////////// 코디/조립 기능으로 인한 재고량 체크 ///////////////////////////////////////////////
				$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket ";
				$basketsql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
				$basketresult = pmysql_query($basketsql,get_db_conn());
				while($basketrow=@pmysql_fetch_object($basketresult)) {
					if($basketrow->assemble_idx>0) {
						if(ord($basketrow->assemble_list)) {
							$assembleprolistexp = explode("",$basketrow->assemble_list);
							for($i=0; $i<count($assembleprolistexp); $i++) {
								if(ord($assembleprolistexp[$i])) {
									$assemble_proquantity[$assembleprolistexp[$i]]+=$basketrow->quantity;
								}
							}
						}
					} else {
						$assemble_proquantity[$basketrow->productcode]+=$basketrow->quantity;
					}
				}
				@pmysql_free_result($basketresult);

				if(count($assemble_list_exp)>0) {
					for($i=0; $i<count($assemble_list_exp); $i++) {
						if(ord($assemble_list_exp[$i])) {
							$assemble_proquantity[$assemble_list_exp[$i]]+=$charge_quantity;
						}
					}
					$assemprosql = "SELECT productcode,quantity,productname FROM tblproduct ";
					$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
					if($vip_type != "1" or $staff_type!="1"){
						$assemprosql.= "AND display = 'Y' ";
					}
					$assemproresult=pmysql_query($assemprosql,get_db_conn());
					while($assemprorow=@pmysql_fetch_object($assemproresult)) {
						if(ord($assemprorow->quantity)) {
							if($assemble_proquantity[$assemprorow->productcode] > $assemprorow->quantity) {
								if($assemprorow->quantity>0) {
									$errmsg.="해당 상품의 구성상품 [".str_replace("'","",$assemprorow->productname)."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$assemprorow->quantity} 개 입니다.")."\\n";
								} else {
									$errmsg="해당 상품의 구성상품 [".str_replace("'","",$assemprorow->productname)."] 다른 고객의 주문으로 품절되었습니다.\\n";
								}
							}
						}
					}
					@pmysql_free_result($assemproresult);
				} elseif(ord($package_productcode_tmp)) {
					$errmsg = '';
					$assemble_proquantity[$productcode]+=$charge_quantity;
					$package_productcode_tmpexp = explode("",$package_productcode_tmp);
					$package_quantity_tmpexp = explode("",$package_quantity_tmp);
					$package_productname_tmpexp = explode("",$package_productname_tmp);
					for($i=0; $i<count($package_productcode_tmpexp); $i++) {
						if(ord($package_productcode_tmpexp[$i])) {
							$assemble_proquantity[$package_productcode_tmpexp[$i]]+=$charge_quantity;

							if(ord($package_quantity_tmpexp[$i])) {
								if($assemble_proquantity[$package_productcode_tmpexp[$i]] > $package_quantity_tmpexp[$i]) {
									if($package_quantity_tmpexp[$i]>0) {
										$errmsg.="해당 상품의 패키지 [".str_replace("'","",$package_productname_tmpexp[$i])."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$package_quantity_tmpexp[$i]} 개 입니다.")."\\n";
									} else {
										$errmsg.="해당 상품의 패키지 [".str_replace("'","",$package_productname_tmpexp[$i])."] 다른 고객의 주문으로 품절되었습니다.\\n";
									}
								}
							}
						}
					}
				} else {
					$assemble_proquantity[$productcode]+=$charge_quantity;
					if(ord($row->quantity)) {
						if ($assemble_proquantity[$productcode] > $row->quantity) {
							if ($row->quantity>0)
								$errmsg="해당 상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\\n";
							else
								$errmsg= "해당 상품이 다른 고객의 주문으로 품절되었습니다.\\n";
						}
					}
				}

				# 수정되어야 함
				/*
				$option_type = $_POST['option_type'];
				$option_code = $_POST['option_code'];
				$option_value = $_POST['option_value'];
				$option_quantity = $_POST['option_quantity'];
				*/
				if( count($option_code) > 0 ){
					foreach( $option_code[$productcode] as $opKey=>$opVal ){
						$opQuantitySql = "SELECT option_quantity FROM tblproduct_option WHERE option_code = '".$opVal."' ";
						$opQuantityRes = pmysql_query( $opQuantitySql, get_db_conn() );
						if ( $opQuantityRow = pmysql_fetch_object( $opQuantityRes ) ) {
							if ( $opQuantityRow->option_quantity <= 0 ) {
								$errmsg.="해당 상품의 선택된 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
							} else if ( $opQuantityRow->option_quantity < $option_quantity[$productcode][$opKey] ) {
								$errmsg.="해당 상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.": $opQuantityRow->option_quantity." 개 입니다.")."\\n";
							}
						} else {
							$errmsg.="잘못된 옵션입니다.\\n";
						}
						pmysql_free_result( $opQuantityRes );
					}
				}
				/*
				if(ord($row->option_quantity)) {
					$optioncnt = explode(",",ltrim($row->option_quantity,','));
					if($option2==0) $tmoption2=1;
					else $tmoption2=$option2;
					$optionvalue=$optioncnt[(($tmoption2-1)*10)+($option1-1)];
					if($optionvalue<=0 && $optionvalue!="") {
						$errmsg.="해당 상품의 선택된 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
					} elseif($optionvalue<$quantity && $optionvalue!="") {
						$errmsg.="해당 상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
					} else {
						if($mode=="upd") {
							if (empty($option1))  $option1=0;
							if (empty($option2))  $option2=0;
							if (empty($opts))  $opts="0";
							if (empty($assemble_idx))  $assemble_idx=0;

							$samesql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
							$samesql.= "AND productcode='{$productcode}' ";
							$samesql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
							$samesql.= "AND assemble_idx = '{$assemble_idx}' ";
							$sameresult = pmysql_query($samesql,get_db_conn());
							$samerow=pmysql_fetch_object($sameresult);
							pmysql_free_result($sameresult);
							if($samerow && ($option1!=$orgoption1 || $option2!=$orgoption2)) {
								if($optionvalue<($samerow->quantity + $quantity) && $optionvalue!="") {
									$errmsg.="해당 상품의 선택된 옵션과 중복상품의 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
								}
							}
						}
					}
				}
				*/
			}
		} else {
			$errmsg="해당 상품이 존재하지 않습니다!.\\n";
		}
		pmysql_free_result($result);

		if(ord($errmsg)) {
			alert_go($errmsg,$Dir.FrontDir."basket.php");
		}
	}

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

	$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND productcode='{$productcode}' ";
	$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
	$sql.= "AND assemble_idx = '{$assemble_idx}' ";
	$sql.= "AND package_idx = '{$package_idx}' ";
//	echo $sql;
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
/*
	if($row){
		alert_go('장바구니에 존재하는 상품입니다.',$Dir.FrontDir."basket.php");			
	}
*/	
	if ($mode=="del") {

		$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='{$productcode}' ";
		$sql.= "AND opt1_idx='{$orgoption1}' AND opt2_idx='{$orgoption2}' AND optidxs='{$opts}' ";
		$sql.= "AND assemble_idx = '{$assemble_idx}' ";
		$sql.= "AND package_idx = '{$package_idx}' ";
		pmysql_query($sql,get_db_conn());

	} elseif ($mode=="upd") {
		if (($option1==$orgoption1 && $option2==$orgoption2) || !($row)) {
			$sql = "UPDATE tblbasket SET ";
			$sql.= "quantity		= '{$quantity}', ";
			$sql.= "opt1_idx		= '{$option1}', ";
			$sql.= "opt2_idx		= '{$option2}', ";
			$sql.= "quantityarr		= '{$quantity}' ";
			$sql.= "WHERE tempkey	='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode	='{$productcode}' AND opt1_idx='{$orgoption1}' ";
			$sql.= "AND opt2_idx	='{$orgoption2}' AND optidxs='{$opts}' ";
			$sql.= "AND assemble_idx = '{$assemble_idx}' ";
			$sql.= "AND package_idx = '{$package_idx}' ";
			pmysql_query($sql,get_db_conn());
		} else {
			$c = $row->quantity + $quantity;
			$sql = "UPDATE tblbasket SET quantity='{$c}', opt1_idx='{$option1}' ";
			$sql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode='{$productcode}' AND opt1_idx='{$option1}' ";
			$sql.= "AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
			$sql.= "AND assemble_idx = '{$assemble_idx}' ";
			$sql.= "AND package_idx = '{$package_idx}' ";
			pmysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='{$productcode}' ";
			$sql.= "AND opt1_idx='{$orgoption1}' AND opt2_idx='{$orgoption2}' AND optidxs='{$opts}' ";
			$sql.= "AND assemble_idx = '{$assemble_idx}' ";
			$sql.= "AND package_idx = '{$package_idx}' ";
			pmysql_query($sql,get_db_conn());
		}
	} elseif ($row) {	// tblbasket 에 값이 존재할 경우
		$onload="<script>alert('이미 장바구니에 상품이 담겨있습니다. 수량을 조절하시려면 수량입력후 수정하세요.');</script>";
	} else {  
		if (strlen($productcode)==18) {
			$vdate = date("YmdHis");
			$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$result = pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($row->cnt>=200) {
				echo "<script>alert('장바구니에는 총 200개까지만 담을수 있습니다.');</script>";
			} else {
				if ($optionArr != "" && $priceArr != "" && $quantityArr != "" ) {
					$optionArrTmp = explode("||",trim($optionArr));
					$priceArrTmp = explode("||",trim($priceArr));
					$quantityArrTmp = explode("||",trim($quantityArr));
					for ($i = 0; $i < sizeof($optionArrTmp); $i++) {
						$multiOrderCount++;
						//$ex_option = explode("_",$optionArrTmp[$i]);
						$ex_option = explode( chr(30), $optionArrTmp[$i] );
						$option1 = $ex_option[0];
						$option2 = $ex_option[1];
						$quantity = $quantityArrTmp[$i];
						$opTypeSql = "SELECT option_type FROM tblproduct_option WHERE productcode = '".$productcode."' AND option_code = '".$optionArrTmp[$i]."'";
						$opTypeRes = pmysql_query( $opTypeSql );
						$opTypeRow = pmysql_fetch_array( $opTypeRes );
						$opType = $opTypeRow['option_type'];
						pmysql_free_result( $opTypeRes );
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
								'{$quantity}',
								'{$package_idx}',
								'{$assemble_idx_max}',
								'{$assemble_list}',
								'{$optionArrTmp[$i]}',
								'{$quantity}',
								'{$priceArrTmp[$i]}',
								'{$vdate}',
								'{$opType}' )";	
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
								'{$quantity}',
								'{$package_idx}',
								'{$assemble_idx_max}',
								'{$assemble_list}',
								'{$optionArrTmp[$i]}',
								'{$quantity}',
								'{$priceArrTmp[$i]}',
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
							optionarr		,
							quantityarr		,
							pricearr		,
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
							'{$optionArr}'		,
							'{$quantityArr}'	,
							'{$priceArr}'	,
							'{$vdate}','".$_ShopInfo->getMemid()."')";
							pmysql_query($sql,get_db_conn());	
					}
				}
				

				

			}
		}
	}
}

if($mode == 'upd'){
	## POST 로 넘기면 재전송 새로고침시 재전송여부를 확인 하기 때문에 location.replce 시킴.
	go($Dir.FrontDir."basket.php");
}
list($grp_name)=pmysql_fetch("select group_name from tblmembergroup where group_code='".$_ShopInfo->memgroup."'");
list($reserve)=pmysql_fetch("select reserve from tblmember where id='".$_ShopInfo->memid."' ");
list($cnt_coupon)=pmysql_fetch("select count(*) from tblcouponissue where id='".$_ShopInfo->memid."' AND used != 'Y'");

?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="">
<meta name="Author" content="">
<meta name="Keywords" content="<?=$_data->shopkeyword?>">
<meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?=$_data->shoptitle?></title>
<link href="../css/common.css" rel="stylesheet" type="text/css" />
<link href="../css/main.css" rel="stylesheet" type="text/css" />
<link href="../css/sub.css" rel="stylesheet" type="text/css" />
<script src="../js/jquery-1.10.1.min.js" type="text/javascript"></script>
<script src="../js/gnb_nav.js" type="text/javascript"></script>
<script src="../js/common.js" type="text/javascript"></script>
<script src="../js/select.js" type="text/javascript"></script>

<script src="../js/main/main_visual.js" type="text/javascript"></script>
<script src="../js/main/news.js" type="text/javascript"></script>
<script src="../js/main/main_nav.js" type="text/javascript"></script>

</head>
<body>

<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>

<style>
	.checkProduct{
		border:0px solid red;
	}
	.allCheck{
		border:0px solid red;
	}
</style>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>

<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
/** 장바구니 list에 적용되는 함수 **/
function CheckForm(mode,idx) {
	if(mode=="wish_chk") {

		if($(".checkProduct:checked").length > 0){

			wish_idx='';
			$(".checkProduct:checked").each(function(){

				wish_idx+=$(this).val()+'|';
			});

			document.wishform.basket_idx.value=wish_idx;
			window.open("about:blank","confirmwishlist","width=500,height=300,scrollbars=no");
			document.wishform.submit();
		}else{
			alert('상품을 선택해주세요.');
		}

	}else if(mode=="del_chk") {
		if(idx=="") {
			if($(".checkProduct:checked").length > 0){
				if(confirm("해당 상품을 장바구니에서 삭제하시겠습니까?")) {
					del_idx='';
					$(".checkProduct:checked").each(function(){
						del_idx+=$(this).val()+'|';
					});
					document.location.href="basket.php?mode="+mode+"&del_idx="+del_idx;
					//document["form_"+idx].mode.value=mode;
					//document["form_"+idx].submit();
				}
			}else{
				alert('상품을 선택해주세요.');
			}
		}else{
			if(confirm("해당 상품을 장바구니에서 삭제하시겠습니까?")) {
				document.location.href="basket.php?mode="+mode+"&del_idx="+idx+"|";
			}
		}
	} else if(mode=="upd") {
		if(document["form_"+idx].quantity.value.length==0 || document["form_"+idx].quantity.value==0) {
			alert("수량을 입력하세요.x");
			//document["form_"+idx].quantity.focus();
			return;
		}
		if(!IsNumeric(document["form_"+idx].quantity.value)) {
			alert("수량은 숫자만 입력하세요.");
			//document["form_"+idx].quantity.focus();
			return;
		}
		if(0+document["form_"+idx].quantity.value>9999) {
			//alert(document["form_"+idx].quantity.value);
			alert("수량은 최대 9999까지 입력가능합니다.");
			//document["form_"+idx].quantity.focus();
			return;
		}

		document["form_"+idx].mode.value=mode;
		document["form_"+idx].submit();
	}
	//////// 추가  ( 장바구니 )
	else if ( mode == '')
	{
		document.form1.action="../front/confirm_basket.php";
		document.form1.target="confirmbasketlist";		
		document.form1.productcode.value= idx;
		window.open("about:blank","confirmbasketlist","width=401,height=309,scrollbars=no,resizable=no, status=no,");
		document.form1.submit();
	}
}

function change_quantity(gbn,idx) {
	tmp=document["form_"+idx].quantity.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	document["form_"+idx].quantity.value=tmp;


	tmp=document["form_v_"+idx].quantity_v.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	document["form_v_"+idx].quantity_v.value=tmp;
}

function basket_clear() {
	if(confirm("장바구니를 비우시겠습니까?")) {
		document.delform.mode.value="clear";
		//AEC_D_A();
		document.delform.submit();
	}
}
function check_login() {
	if(confirm("로그인이 필요한 서비스입니다. 로그인을 하시겠습니까?")) {
		document.location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
	}
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

<?php if($_data->oneshot_ok=="Y" || $_data->design_basket=="U") {?>
var imagepath="<?=$Dir.DataDir?>shopimages/product/";
var default_primage="oneshot_primage<?=$_data->design_basket?>.gif";
var prall=new Array();
function pralllist() {
	var argv = pralllist.arguments;
	var argc = pralllist.arguments.length;

	//Property 선언
	this.classname		= "pralllist"								//classname
	this.debug			= false;									//디버깅여부.
	this.productcode	= new String((argc > 0) ? argv[0] : "");
	this.tinyimage		= new String((argc > 1) ? argv[1] : "");
	this.option1		= ToInt((argc > 2) ? argv[2] : 1);
	this.option2		= ToInt((argc > 3) ? argv[3] : 1);
	this.quantity		= ToInt((argc > 4) ? argv[4] : 1);
	this.miniq			= ToInt((argc > 5) ? argv[5] : 1);
	this.assembleuse	= new String((argc > 6) ? argv[6] : "N");
	this.package_num	= new String((argc > 7) ? argv[7] : "");
}

function CheckCode() {
	form=document.form1;
	if(form.code_a.value.length==3 && form.code_b.value.length==3 && form.code_c.value.length==3 && form.code_d.value.length==3) {
		form.submit();
	} else {
		form.tmpprcode.options.length=1;
		var d = new Option("상품 선택");
		form.tmpprcode.options[0] = d;
		form.tmpprcode.options[0].value = "";

		document.all["oneshot_primage"].src="<?=$Dir?>images/common/basket/"+default_primage;
		form.productcode.value="";
		form.quantity.value="";
		form.option1.value="";
		form.option2.value="";
	}
}

function CheckProduct() {
	form=document.form1;
	if(form.tmpprcode.value.length==0) {
		document.all["oneshot_primage"].src="<?=$Dir?>images/common/basket/"+default_primage;
		form.productcode.value="";
		form.quantity.value="";
		form.option1.value="";
		form.option2.value="";
		form.assembleuse.value="";
		form.package_num.value="";
	} else {
		productcode=prall[form.tmpprcode.value].productcode;
		tinyimage=prall[form.tmpprcode.value].tinyimage;
		option1=prall[form.tmpprcode.value].option1;
		option2=prall[form.tmpprcode.value].option2;
		quantity=prall[form.tmpprcode.value].miniq;
		assembleuse=prall[form.tmpprcode.value].assembleuse;
		package_num=prall[form.tmpprcode.value].package_num;
		if(tinyimage.length>0) {
			document.all["oneshot_primage"].src=imagepath+tinyimage;
		} else {
			document.all["oneshot_primage"].src="<?=$Dir?>images/common/basket/"+default_primage;
		}
		form.productcode.value=productcode;
		form.quantity.value=quantity;
		form.option1.value=option1;
		form.option2.value=option2;
		form.assembleuse.value=assembleuse;
		form.package_num.value=package_num;
	}
}

function OneshotBasketIn() {
	if(document.form1.productcode.value.length!=18) {
		alert("상품을 선택하세요.");
		document.form1.tmpprcode.focus();
		return;
	}
	if(document.form1.assembleuse.value=="Y") {
		if(confirm("해당 상품은 구성상품을 구성해야만 구매가 가능한 상품입니다.\n\n         상품 상세페이지에서 구성를 하겠습니까?")) {
			location.href="<?=$Dir.FrontDir?>productdetail.php?productcode="+document.form1.productcode.value;
		}
	} else if(document.form1.package_num.value.length>0) {
		if(confirm("해당 상품은 패키지 선택 상품으로써 상품상세페이지에서 패키지 정보를 확인 해 주세요.\n\n                              상품상세페이지로 이동 하겠습니까?")) {
			location.href="<?=$Dir.FrontDir?>productdetail.php?productcode="+document.form1.productcode.value;
		}
	} else {
		document.form1.submit();
	}
}
<?php }?>
//-->
</SCRIPT>

<?
$subTop_flag = 3;
//include ($Dir.MainDir."sub_top.php");
?>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<table border="0" cellpadding="0" cellspacing="0" width="100%" >
<?php
$leftmenu="Y";
if($_data->design_basket=="U") {
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='basket'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	pmysql_free_result($result);
}

if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/basket_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/basket_title.gif\" border=\"0\" alt=\"장바구니\"></td>\n";
	} else if($_data->design_basket=="001" || $_data->design_basket=="002" || $_data->design_basket=="003"){
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/basket_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/basket_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/basket_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";


 include ($Dir.TempletDir."basket/basket_iframe_{$_data->design_basket}.php");
 

echo "	</td>\n";
echo "</tr>\n";

$sql = "update tblbasket set ord_state=false where tempkey = '".$_ShopInfo->getTempkey()."' ";
pmysql_query($sql,get_db_conn());

if($ordertype=="ordernow") {  //바로구매
	if($sumprice>=$_data->bank_miniprice) {
		if ($multiOrderCount>0) {
		//if ($multiOrderCount < 0) {
			$jsmultiOrderCount = $multiOrderCount;
		} else {			
		// if조건이 이상해서.   update 조건이 먹히지 않음   // optionarr quantity arr 값을 가져와서 update를 해야하나 안
		/*
			$sql = "update tblbasket set tempkey='".$_ShopInfo->getTempkeySelectItem()."' where tempkey = '".$_ShopInfo->getTempkey()."'";
			pmysql_query($sql,get_db_conn());

			$sql = "update tblbasket set tempkey='".$_ShopInfo->getTempkey()."', quantity='".$quantity."' where tempkey = '".$_ShopInfo->getTempkeySelectItem()."' and productcode='{$productcode}' and opt1_idx='{$option1}' and opt2_idx='{$option2}' and optidxs='{$opts}' and assemble_idx='{$assemble_idx}' and package_idx='{$package_idx}' and assemble_list='{$assemble_list}' ";
			
			pmysql_query($sql,get_db_conn());  
		*/	  
			echo "<script>location.href='".$Dir.FrontDir."login.php?buy=1&chUrl=".urlencode($Dir.FrontDir."order.php?productcode=".$productcode)."';</script>";
			exit;
		}
	} else {
		$onload="<script>alert('".number_format($_data->bank_miniprice)."원 이상 구매가 가능합니다.');</script>";
	}
}
?>
<form name=wishform method=post action="<?=$Dir.FrontDir?>confirm_wishlist_cart.php" target="confirmwishlist">
<input type=hidden name=basket_idx>
</form>
<form name=delform method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=productcode>
</form>
</table>

<?
	if($biz[bizNumber]){
?>
<script>
	if(bizAllClearArray){
		for(var h=0; h < bizAllClearArray.length; h++){
			_trk_clickTrace('SCO', "'"+bizAllClearArray[h]+"'");
		}
	}
</script>
<?
	}
?>


<?=$onload?>



<script type="text/javascript">



	function multiOrder(count_){
		<?
			if($ordertype=="ordernow") {	
		?>
		if (count_ < 1){
			return;
		}
		var strBasket = "";
		$(".checkProduct").each(function(index){
			if (index < count_){
				strBasket += $(this).val() + "|";
			}
		})
		var orderHref = "/front/order.php?"+"&selectItem="+strBasket;
		location.href = orderHref;
		<?
			}	
		?>
	}
	//allCheck
	//
	$(document).ready(function(){
		multiOrder('<?=$multiOrderCount?>');
		$(document).on("click",".allCheck",function(){
			$(".checkProduct").prop('checked',$(this).is(":checked"));
		});
		$(document).on("click",".allCheckButton",function(){
			$(".checkProduct").prop('checked', true);
			$(".allCheck").prop('checked', true);
		});
		$(document).on("click",".allUnCheckButton",function(){
			$(".checkProduct").prop('checked', false);
			$(".allCheck").prop('checked', false);
		});
		
		$(".layer_goods_icon").on("click",function(e){
	    	var target = e.target
	    	if($(target).attr("class") == "cart" || $(target).attr("class") == "view" ) return; 
	    	location.href = $(this).attr("link_url");
	    });
	    
	    $(".cart").on("click",function(e){
	    	var chkOption = $(this).attr("option_chk");
	    	var chkLink = $(this).attr("cart_chk");
	    	if(chkOption == 1){
				CheckForm('',chkLink);
			}else if(chkOption == 3){
		    	$("#productlist_basket").attr("action","../front/productlist_basket.php");
		    	$("#productlist_basket").attr("target","basketOpen");
		    	$("#productcode2").val(chkLink);
				window.open("","basketOpen","width=440,height=420,scrollbars=no,resizable=no, status=no,");
				$("#productlist_basket").submit();
			} 
	    });
	    
	    $(".view").on("click",function(){
	    	location.href = $(this).attr("link_url");
	    });
	});

	/*function CheckForm(gbn,temp2) {
		var itemCount = 0;

		if(gbn=="ordernow") {
			document.form1.ordertype.value="ordernow";
		}

		if (gbn != "ordernow"){
			document.form1.action="../front/confirm_basket.php";
			document.form1.target="confirmbasketlist";
			document.form1.productcode.value= temp2;
			window.open("about:blank","confirmbasketlist","width=401,height=309,scrollbars=no,resizable=no, status=no,");
			document.form1.submit();
		}

	}*/

//////////////////
		$(".estimate_sheet").click(function(){
			if($(".checkProduct:checked").length > 0){
				var strBasket = "";
				$(".checkProduct").each(function(){
					if($(this).is(":checked")){
						strBasket += $(this).val() + "|";						
						/**
						strBasket += $(this).prev().val() + "|";
						strBasket += $(this).prev().prev().prev().prev().prev().val() + "|";	
						if( $(this).prev().prev().prev().prev().prev().prev().val() != ""){
							
							strBasket += $(this).prev().prev().prev().prev().prev().prev().val() + "|";	
							
						}else{
							
							strBasket += "|";	
						}
						strBasket += $(this).prev().prev().prev().prev().prev().prev().prev().val() + "|";	
						**/
					}
				})
				
				new_win=window.open("about:blank","test_pop","scrollbars=yes,width=800,height=600,resizable=yes");
				//alert(strBasket);
				document.estimate_sheet_form.target="test_pop";
				document.estimate_sheet_form.strBasket.value =strBasket;
				document.estimate_sheet_form.action="estimate_sheet_form1.php";
				document.estimate_sheet_form.submit();
				new_win.focus();

				//location.href = orderHref;
			}else{
				alert("적어도 하나 이상의 상품을 선택 해 주세요.");
			}
		});

$("a.busket").mouseenter(function(){
	$(this).css('border-color','#ffa93a');
	$(this).css('color','#ffa93a');
	
})
$("a.busket").mouseleave(function(){
	$(this).css('border-color','');
	$(this).css('color','#ccc');
	
})

$("a.btn").mouseenter(function(){
	$(this).css('border-color','#ffa93a');
	$(this).css('color','#ffa93a');
	
})
$("a.btn").mouseleave(function(){
	$(this).css('border-color','');
	$(this).css('color','#ccc');
	
})
/////////////////
		$(".selectProduct").click(function(){
			if($(".checkProduct:checked").length > 0){
				var strBasket = "";
				$(".checkProduct").each(function(){
					if($(this).is(":checked")){
						strBasket += $(this).val() + "|";
					}
				})

				var orderHref = $(this).prev().attr("href")+"&selectItem="+strBasket;
				//alert(orderHref);
				location.href = orderHref;
			}else{
				alert("적어도 하나 이상의 상품을 선택 해 주세요.");
			}
		});

		$(".allBuyProduct").click(function(){
			$(".checkProduct").prop('checked', true);
			$(".allCheck").prop('checked', true);
			if($(".checkProduct:checked").length > 0){
				var strBasket = "";
				$(".checkProduct").each(function(){
					if($(this).is(":checked")){
						strBasket += $(this).val() + "|";
					}
				})

				//var orderHref = $(this).prev().prev().attr("href")+"?selectItem="+strBasket;
				var orderHref = $(this).prev().prev().attr("href");
				location.href = orderHref;
			}else{
				alert("적어도 하나 이상의 상품을 선택 해 주세요.");
			}
		});


		$(".estimate").click(function(){
			if($(".checkProduct:checked").length > 0){
				var strEst = "";
				$(".checkProduct").each(function(){
					if($(this).is(":checked")){
						strEst += $(this).val() + "|";
					}
				})

				var estimateHref = "estimate_print.php?printval="+strEst;
				//location.href = estimateHref;
				window.open(estimateHref,"est_pop","width=600px;");
			}else{
				alert("적어도 하나 이상의 상품을 선택 해 주세요.");
			}
		});

		$(".allProduct").click(function(){
			$(".checkProduct").attr("checked", true);
			if($(".checkProduct:checked").length > 0){

				var orderHref = $(this).prev().attr("href")+"?allcheck=1";
				location.href = orderHref;
			}else{
				alert("장바구니에 상품이 존재하지 않습니다.");
			}
		});

		$(".CLS_DirectBuyBtn").click(function(){
			$(".checkProduct").prop('checked', false);
			$(".allCheck").prop('checked', false);
			$(this).parent().parent().children().find('.checkProduct').prop('checked', true);
			$('.selectProduct').trigger('click');
		});

		$(".CLS_WishlistBtn").click(function(){
			var wishProductcode = $(this).parent().parent().children().find("input[name='productcode']").val();
			var wishOption1 = $(this).parent().parent().children().find("input[name='option1']").val();
			var wishOption2 = $(this).parent().parent().children().find("input[name='option2']").val();
			var wishOpts = $(this).parent().parent().children().find("input[name='opts']").val();
			$.ajax({
				type: "POST",
				url: "../front/confirm_wishlist.php",
				data: "productcode="+wishProductcode+"&opts="+wishOpts+"&option1="+wishOption1+"&option2="+wishOption2+"&check_falg=ajax"
			}).done(function ( msg ) {
				alert(msg);
			});
		})

		$(".CLS_basketTotalCount").html("("+$(".checkProduct").length+"개)");

	//});

	$(function(){
		//중간 상품 레이어
		var middle_goods = $('li div.goods_img');
		var middle_goods_layer = $('div.layer_goods_info');
		middle_goods.mouseenter(function(e){
			//var ii = $(this).offset();
			var ii = $(this).position();
					
 
			$(this).find(middle_goods_layer).css({'top':ii.top });
			$(this).find(middle_goods_layer).css({'left':ii.left});
			$(this).find(middle_goods_layer).show();
			
		});
		middle_goods_layer.mouseleave(function(){
			middle_goods_layer.hide();		
		});	

	})

</script>


<div id="overDiv" style="position:absolute;top:0px;left:0px;z-index:100;display:none;" class="alpha_b60" ></div>
<div class="popup_preview_warp" style="margin-left: 50%;left: -459px;display:none;" ></div>
</BODY>
</HTML>
