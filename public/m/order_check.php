<?php

$product = new PRODUCT();

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

$ip = $_SERVER['REMOTE_ADDR'];

$receipt_yn = $paymethod!="B"?"Y":$receipt_yn;

$sslchecktype="";
if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
	$sslchecktype="ssl";
}
if($sslchecktype=="ssl") {
	$secure_data=getSecureKeyData($_POST["sessid"]);
	if(!is_array($secure_data)) {
		alert_go('보안인증 정보가 잘못되었습니다.',-2);
	}
	foreach($secure_data as $key=>$val) {
		${$key}=$val;
	}
} else {
	foreach($_POST as $key=>$val) {
		${$key}=$val;
	}
}
//주문실패시 refferer 확인
$sendReferer = parse_url( $_SERVER['HTTP_REFERER'] );

$sender_name=str_replace(" ","",$sender_name);
$sender_email=str_replace("'","",$sender_email);
$receiver_name=str_replace(" ","",$receiver_name);
$order_msg=str_replace("'","",$order_msg);
$sender_tel=str_replace("'","",$sender_tel);
$receiver_tel1=str_replace("'","",$receiver_tel1);
$receiver_tel2=str_replace("'","",$receiver_tel2);
$receiver_addr=str_replace("'","",$receiver_addr);
$rpost=$rpost1.$rpost2;
$overseas_code = strip_tags( $overseas_code );

$deli_type = $_POST["deli_type"];

$gift_sel=$_POST['gift_sel'];

//$loc=substr($raddr1,0,4);
$loc = mb_substr($raddr1,0,4,'utf-8');

if (ord($paymethod)==0) {
	echo "<html></head><body onload=\"alert('결제방법이 선택되지 않았습니다.');parent.document.form1.process.value='N';parent.ProcessWait('hidden');\"></body></html>";
	exit;
}

if (ord($usereserve)>0 && !IsNumeric($usereserve)) {
	echo "<html></head><body onload=\"alert('적립금은 숫자만 입력하시기 바랍니다.');parent.document.form1.process.value='N';parent.ProcessWait('hidden');\"></body></html>";
	exit;
}

if(ord($_data->escrow_id)==0 && $paymethod=="Q") {
	echo "<html></head><body onload=\"alert('에스크로 정보가 존재하지 않습니다.');parent.document.form1.process.value='N';parent.ProcessWait('hidden');\"></body></html>";
	exit;
}

$escrow_info = GetEscrowType($_data->escrow_info);
if(ord($_data->escrow_id)>0 && ($escrow_info["escrowcash"]=="Y" || $escrow_info["escrowcash"]=="A")) {
	$escrowok="Y";
} else {
	$escrowok="N";
	$escrow_info["escrowcash"]="";
	if($escrow_info["onlycash"]!="Y" && (ord($escrow_info["onlycard"])==0 && ord($escrow_info["nopayment"])==0)) $escrow_info["onlycash"]="Y";
}

$pg_type="";
switch ($paymethod) {
	case "B":
		break;
	case "V":
		$pgid_info=GetEscrowType($_data->trans_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "O":
		$pgid_info=GetEscrowType($_data->virtual_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "Q":
		$pgid_info=GetEscrowType($_data->escrow_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "C":
		$pgid_info=GetEscrowType($_data->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "P":
		$pgid_info=GetEscrowType($_data->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "M":
		$pgid_info=GetEscrowType($_data->mobile_id);
		$pg_type=$pgid_info["PG"];
		break;
}
$pg_type=trim($pg_type);

$pmethod=$paymethod.$pg_type;

if ($paymethod!="B" && ord($pg_type)==0) {
	echo "<html></head><body onload=\"alert('선택하신 결제방법은 이용하실 수 없습니다.');parent.document.form1.process.value='N';parent.ProcessWait('hidden');\"></body></html>";
	exit;
}

$card_splittype=$_data->card_splittype;
$card_splitmonth=$_data->card_splitmonth;
$card_splitprice=$_data->card_splitprice;

$coupon_ok=$_data->coupon_ok;
$card_miniprice=$_data->card_miniprice;
$reserve_limit=$_data->reserve_limit;
$reserve_maxprice=$_data->reserve_maxprice;
if($reserve_limit==0) $reserve_limit=1000000000000;
if($_data->rcall_type=="Y") {
	$rcall_type = $_data->rcall_type;
	$bankreserve="Y";
} else if($_data->rcall_type=="N") {
	$rcall_type = $_data->rcall_type;
	$bankreserve="Y";
} else if($_data->rcall_type=="M") {
	$rcall_type="Y";
	$bankreserve="N";
} else {
	$rcall_type="N";
	$bankreserve="N";
}

if($_data->reserve_useadd==-1) $reserve_useadd="N";
else if($_data->reserve_useadd==-2) $reserve_useadd="U";
else $reserve_useadd=$_data->reserve_useadd;

$etcmessage=explode("=",$_data->order_msg);

#적립금이 현금결제시에만 사용가능하고 현금결제를 선택안했을때
if($bankreserve=="N" && !strstr("BVOQ",$paymethod)) {
	$usereserve=0;
}

$user_reserve=0;
$reserve_type="N";
if( strlen( $ordercode ) == 0 || is_null( $ordercode ) ){
	if(ord($_ShopInfo->getMemid())>0) {
		$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$ordercode=unique_id();
			$user_reserve = $row->reserve;
			$group_code=$row->group_code;
			pmysql_free_result($result);
			
			if(ord($group_code)>0 && $group_code!=NULL) {
				$sql = "SELECT * FROM tblmembergroup WHERE group_code='{$group_code}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$group_code=$row->group_code;
					$group_level=$row->group_level;
					$group_deli_free=$row->group_deli_free;
					$group_name=$row->group_name;
					$group_type=substr($row->group_code,0,2);
					$group_usemoney=$row->group_usemoney;
					$group_addmoney=$row->group_addmoney;
					$group_payment=$row->group_payment;
				}
				pmysql_free_result($result);
			}
		} else {
			$_ShopInfo->SetMemNULL();
			//guest
			$ordercode=unique_id()."X";
			$id="X".date("iHs").$sender_name;
		}
	} else {
		//guest
		$ordercode=unique_id()."X";
		$id="X".date("iHs").$sender_name;
	}
}


$basketsql2 = "SELECT a.productcode,a.package_idx,a.quantity,c.package_list,c.package_title,c.package_price ";
$basketsql2.= "FROM tblbasket AS a, tblproduct AS b, tblproductpackage AS c ";
$basketsql2.= "WHERE a.productcode=b.productcode ";
$basketsql2.= "AND b.package_num=c.num ";
$basketsql2.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
$basketsql2.= "AND a.package_idx>0 ";
$basketsql2.= "AND b.display = 'Y' ";

$basketresult2 = pmysql_query($basketsql2,get_db_conn());
while($basketrow2=@pmysql_fetch_object($basketresult2)) {
	if(ord($basketrow2->package_title)>0 && ord($basketrow2->package_idx)>0 && $basketrow2->package_idx>0) {
		$package_title_exp = explode("",$basketrow2->package_title);
		$package_price_exp = explode("",$basketrow2->package_price);
		$package_list_exp = explode("", $basketrow2->package_list);
			
		$title_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx] = $package_title_exp[$basketrow2->package_idx];
	
		if(ord($package_list_exp[$basketrow2->package_idx])>1) {
			$basketsql3 = "SELECT productcode,quantity,productname,tinyimage,sellprice FROM tblproduct ";
			$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",ltrim($package_list_exp[$basketrow2->package_idx],','))."') ";
			$basketsql3.= "AND display = 'Y' ";

			$basketresult3 = pmysql_query($basketsql3,get_db_conn());
			$sellprice_package_listtmp=0;
			while($basketrow3=@pmysql_fetch_object($basketresult3)) {
				$assemble_proquantity[$basketrow3->productcode]+=$basketrow2->quantity;
				$productcode_package_listtmp[] = $basketrow3->productcode;
				$quantity_package_listtmp[] = $basketrow3->quantity;
				$productname_package_listtmp[] = $basketrow3->productname;
				$tinyimage_package_listtmp[] = $basketrow3->tinyimage;
				//달러 환율적용
				//$sellprice_package_listtmp+= exchageRate($basketrow3->sellprice);
				$sellprice_package_listtmp+= $basketrow3->sellprice;
			}
			@pmysql_free_result($basketresult3);
			
			if(count($productcode_package_listtmp)>0) {  //장바구니 패키지 상품 정보 출력시 필요한 정보
				$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=0;
				if((int)$sellprice_package_listtmp>0) {
					$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=(int)$sellprice_package_listtmp;
					if(ord($package_price_exp[$basketrow2->package_idx])>0) {
						$package_price_expexp = explode(",",$package_price_exp[$basketrow2->package_idx]);
						if(ord($package_price_expexp[0])>0 && $package_price_expexp[0]>0) {
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
									} else if($package_price_expexp[4]=="R") {
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
				$tinyimage_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $tinyimage_package_listtmp;
			}
			
			$productcode_package_listtmp=array();
			$quantity_package_listtmp=array();
			$productname_package_listtmp=array();
		}
	}
}
@pmysql_free_result($basketresult2);

//재고수량 파악
$errmsg="";
$sql = "SELECT a.quantity as sumquantity, a.optionarr, a.quantityarr, a.op_type ,b.productcode, ";
$sql.= "b.productname,b.display,b.quantity,b.group_check, b.staff_product, b.vip_product, ";
$sql.= "b.option_ea,b.etctype,b.assembleuse,a.assemble_list AS basketassemble_list, ";
$sql.= "c.assemble_list,a.package_idx ";
$sql.= "FROM tblbasket a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
$sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
$sql.= "AND a.productcode=b.productcode ";
$result=pmysql_query($sql,get_db_conn());
$assemble_proquantity_cnt=0;
while($row=pmysql_fetch_object($result)) {

	if($row->display!="Y") {
		if($row->vip_product != "1" and $row->staff_product != "1"  ){
			$errmsg="[".str_replace("'","",$row->productname)."]상품은 판매가 되지 않는 상품입니다.\\n";
		}
	}
	if($row->group_check!="N") {
		if(ord($_ShopInfo->getMemid())>0) {
			$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
			$sqlgc.= "WHERE productcode='{$row->productcode}' ";
			$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
			$resultgc=pmysql_query($sqlgc,get_db_conn());
			if($rowgc=@pmysql_fetch_object($resultgc)) {
				if($rowgc->groupcheck_count<1) {
					$errmsg="[".str_replace("'","",$row->productname)."]상품은 지정 등급 전용 상품입니다.\\n";
				}
				@pmysql_free_result($resultgc);
			} else {
				$errmsg="[".str_replace("'","",$row->productname)."]상품은 지정 등급 전용 상품입니다.\\n";
			}
		} else {
			$errmsg="[".str_replace("'","",$row->productname)."]상품은 회원 전용 상품입니다.\\n";
		}
	}
	$assemble_list_exp = array();
	if(ord($errmsg)==0 && $row->assembleuse=="Y") { // 조립/코디 상품 등록에 따른 구성상품 체크
		if(ord($row->assemble_list)==0) {
			$errmsg="[".str_replace("'","",$row->productname)."]상품은 구성상품이 미등록된 상품입니다. 다른 상품을 주문해 주세요.\\n";
		} else {
			$assemble_list_exp = explode("",$row->basketassemble_list);
		}
	}
	if(ord($errmsg)==0) {
		$miniq=1;
		$maxq="?";
		if(ord($row->etctype)>0) {
			$etctemp = explode("",$row->etctype);
			for($i=0;$i<count($etctemp);$i++) {
				if(strpos($etctemp[$i],"MINIQ=")===0)     $miniq=substr($etctemp[$i],6);
				if(strpos($etctemp[$i],"MAXQ=")===0)      $maxq=substr($etctemp[$i],5);
			}
		}

		if(ord(dickerview($row->etctype,0,1))>0) {
			$errmsg="[".str_replace("'","",$row->productname)."]상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\\n";
		}
	}

	$package_productcode_tmp = array();
	$package_quantity_tmp = array();
	$package_productname_tmp = array();
	if(ord($errmsg)==0 && $row->package_idx>0) { // 패키지 상품 등록에 따른 구성상품 체크
		if(count($productcode_package_list[$row->productcode][$row->package_idx])>0) {
			$package_productcode_tmp = $productcode_package_list[$row->productcode][$row->package_idx];
			$package_quantity_tmp = $quantity_package_list[$row->productcode][$row->package_idx];
			$package_productname_tmp = $productname_package_list[$row->productcode][$row->package_idx];
		}
	}

	if(ord($errmsg)==0) {

		

		if ($miniq!=1 && $miniq>1 && $row->sumquantity<$miniq) 
			$errmsg.="[".str_replace("'","",$row->productname)."]상품은 최소 {$miniq}개 이상 주문하셔야 합니다.\\n";

		if ($maxq!="?" && $maxq>0 && $row->sumquantity>$maxq)
			$errmsg.="[".str_replace("'","",$row->productname)."]상품은 최대 {$maxq}개 이하로 주문하셔야 합니다.\\n";

		if(ord($row->quantity)>0) {
			if ($row->sumquantity>$row->quantity) {
				if ($row->quantity>0)
					$errmsg.="[".str_replace("'","",$row->productname)."]상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\\n";
				else
					$errmsg.= "[".str_replace("'","",$row->productname)."]상품의 재고가 다른고객 주문등의 이유로 장바구니 수량보다 작습니다.\\n";
			}
		}


		if($assemble_proquantity_cnt==0) { //일반 및 구성상품들의 재고량 가져오기
			///////////////////////////////// 코디/조립 기능으로 인한 재고량 체크 ///////////////////////////////////////////////
			$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket ";
			$basketsql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$basketresult = pmysql_query($basketsql,get_db_conn());
			while($basketrow=@pmysql_fetch_object($basketresult)) {
				if($basketrow->assemble_idx>0) {
					if(ord($basketrow->assemble_list)>0) {
						$assembleprolistexp = explode("",$basketrow->assemble_list);
						for($i=0; $i<count($assembleprolistexp); $i++) {
							if(ord($assembleprolistexp[$i])>0) {
								$assemble_proquantity[$assembleprolistexp[$i]]+=$basketrow->quantity;
							}
						}
					}
				} else {
					$assemble_proquantity[$basketrow->productcode]+=$basketrow->quantity;
				}
			}
			@pmysql_free_result($basketresult);
			$assemble_proquantity_cnt++;
		}		
		if(count($assemble_list_exp)>0) { // 구성상품의 재고 체크
			$assemprosql = "SELECT productcode,quantity,productname FROM tblproduct ";
			$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
			$assemprosql.= "AND display = 'Y' ";
			$assemproresult=pmysql_query($assemprosql,get_db_conn());
			while($assemprorow=@pmysql_fetch_object($assemproresult)) {
				if(ord($assemprorow->quantity)>0) {
					if($assemble_proquantity[$assemprorow->productcode]>$assemprorow->quantity) {
						if($assemprorow->quantity>0) {
							$errmsg.="[".str_replace("'","",$row->productname)."]상품의 구성상품 [".str_replace("'","",$assemprorow->productname)."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$assemprorow->quantity} 개 입니다.")."\\n";
						} else {
							$errmsg.="[".str_replace("'","",$row->productname)."]상품의 구성상품 [".str_replace("'","",$assemprorow->productname)."] 다른 고객의 주문으로 품절되었습니다.\\n";
						}
					}
				}
			}
		} else if(count($package_productcode_tmp)>0) { // 패키지 구성상품의 재고 체크
			//$package_productcode_tmpexp = explode("",$package_productcode_tmp);
			//$package_quantity_tmpexp = explode("",$package_quantity_tmp);
			//$package_productname_tmpexp = explode("",$package_productname_tmp);
			$package_productcode_tmpexp = $package_productcode_tmp;
			$package_quantity_tmpexp = $package_quantity_tmp;
			$package_productname_tmpexp = $package_productname_tmp;
			for($i=0; $i<count($package_productcode_tmpexp); $i++) {
				if(ord($package_productcode_tmpexp[$i])>0) { 
					if(ord($package_quantity_tmpexp[$i])>0) {
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
		} else { // 일반상품의 재고 체크
			if(ord($row->quantity)>0) {
				if($assemble_proquantity[$assemprorow->productcode]>$row->quantity) {
					if ($row->quantity>0) {
						$errmsg.="[".str_replace("'","",$row->productname)."]상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\\n";
					} else {
						$errmsg.= "[".str_replace("'","",$row->productname)."]상품의 재고가 다른고객 주문등의 이유로 장바구니 수량보다 작습니다.\\n";
					}
				}
			}
		}

		//옵션 수량 체크 2015 11 10 유동혁 
		if( strlen( trim($row->optionarr) ) > 0 ) {
			$optQtySql = "SELECT option_quantity FROM tblproduct_option WHERE productcode = '".$row->productcode."' AND option_code = '".trim($row->optionarr)."' ";
			$optQtyRes = pmysql_query( $optQtySql, get_db_conn() );
			$optQtyRow = pmysql_fetch_row( $optQtyRes );
			if( $optQtyRow[0] <= 0 ) {
				$errmsg.="해당 상품의 선택된 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
			} else if( $optQtyRow[0] < $row->quantityarr ) {
				$errmsg.="해당 상품의 선택된 옵션의 재고가 부족합니다. \\n";
			}
			pmysql_free_result( $optQtyRes );
		}
	}
}
pmysql_free_result($result);


if(ord($errmsg)>0) {
	echo "<html></head><body onload=\"alert('{$errmsg}');parent.location.href='basket.php'\"></body></html>";
	exit;
}

$sql = "SELECT b.vender FROM tblbasket a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
$res=pmysql_query($sql,get_db_conn());

$sumprice=0;
$reserve=0;
$deli_price=0;

$optcnt=0;
$count=0;
$setquotacnt = 0;
$basketcnt=array();
$prcode=array();
$arrvender=array();
$prprice=array();
$prname=array();
$orderpatten = array("'","\\\\");
$orderreplace = array("","");
$goodname="";
$allprname="";
$arr_deliprice=array();
$arr_delimsg=array();
$arr_delisubj=array();
// tblorderproduct.deli_price 값
$prDeliPrcie = array();

$address=" ".$raddr1;
$address=str_replace("'","",strip_tags($address));
$address=" ".$address;	//지역별 배송료 구하기위해....
$aryRealPrice = array();
$tmp_use_card = array();
while($vgrp=pmysql_fetch_object($res)) {
	//1. vender가 0이 아니면 해당 입점업체의 배송비 추가 설정값을 가져온다.
	$_vender=null;
	if($vgrp->vender>0) {
		$sql = "SELECT deli_price,deli_pricetype,deli_mini,deli_area,deli_limit,deli_area_limit FROM tblvenderinfo WHERE vender='{$vgrp->vender}' ";
		$res2=pmysql_query($sql,get_db_conn());
		if($_vender=pmysql_fetch_object($res2)) {
			if($_vender->deli_price==-9) {
				$_vender->deli_price=0;
				$_vender->deli_after="Y";
			}
			if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;
		}
		pmysql_free_result($res2);
		
	}
	
	$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.optionarr,a.quantityarr,a.pricearr, ";
	$sql.= "a.op_type, a.quantity, a.date, ";
	$sql.= "b.productcode, b.productname, b.membergrpdc, b.option_reserve, ";
	$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option_ea,b.option1,b.option2, ";
	$sql.= "b.etctype,b.deli_price,b.deli, b.selfcode, b.bisinesscode,a.assemble_list,a.assemble_idx,a.package_idx, ";
	$sql.= "b.sellprice, b.sellprice*a.quantity as realprice, b.deli_qty ";
	$sql.= "FROM tblbasket a
			left join tblproduct b on a.productcode = b.productcode
			";
	$sql.= "WHERE b.vender='{$vgrp->vender}' ";
	$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "ORDER BY a.date DESC ";
	
	$result=pmysql_query($sql,get_db_conn());
	
	$vender_sumprice = 0;	//해당 입점업체의 총 구매액
	$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
	$vender_deliprice = 0;
	$deli_productprice=0;
	$deli_productname1="";
	$deli_productname2="";
	$deli_init = false;
	$aryProductName = array();
	$inProducts = '';
	while($row = pmysql_fetch_object($result)) {
		$tmpPrDeliPrice = 0;
		
		if(ord($prcode[0])>0) {
			if(substr($row->productcode,0,12)==substr($prcode[0],0,12)) $prcode[0]=substr($prcode[0],0,12);
			else if(substr($row->productcode,0,9)==substr($prcode[0],0,9)) $prcode[0]=substr($prcode[0],0,9);
			else if(substr($row->productcode,0,6)==substr($prcode[0],0,6)) $prcode[0]=substr($prcode[0],0,6);
			else if(substr($row->productcode,0,3)==substr($prcode[0],0,3)) $prcode[0]=substr($prcode[0],0,3);
			else $prcode[0]="";
		}
		if((int)$basketcnt[0]==0) $prcode[0]=$row->productcode;

		if($vgrp->vender>0) {
			if(ord($prcode[$vgrp->vender])>0) {
				if(substr($row->productcode,0,12)==substr($prcode[$vgrp->vender],0,12)) $prcode[$vgrp->vender]=substr($prcode[$vgrp->vender],0,12);
				else if(substr($row->productcode,0,9)==substr($prcode[$vgrp->vender],0,9)) $prcode[$vgrp->vender]=substr($prcode[$vgrp->vender],0,9);
				else if(substr($row->productcode,0,6)==substr($prcode[$vgrp->vender],0,6)) $prcode[$vgrp->vender]=substr($prcode[$vgrp->vender],0,6);
				else if(substr($row->productcode,0,3)==substr($prcode[$vgrp->vender],0,3)) $prcode[$vgrp->vender]=substr($prcode[$vgrp->vender],0,3);
				else $prcode[$vgrp->vender]="";
			}
			if((int)$basketcnt[$vgrp->vender]==0) $prcode[$vgrp->vender]=$row->productcode;
		}

		$productcode[$count]=$row->productcode;
		$option_quantity[$productcode[$count]]=trim($row->option_quantity);
		$option_ea[$productcode[$count]]=$row->option_ea;
		$option1num[$count]=$row->opt1_idx;
		$option2num[$count]=($row->opt2_idx!=''?$row->opt2_idx:'');
		$productname[$count]=str_replace($orderpatten,$orderreplace,$row->productname);
		$addcode[$count]=str_replace($orderpatten,$orderreplace,$row->addcode);
		$quantity[$count]=$row->quantity;
		$vender[$count]=$vgrp->vender;
		$selfcode[$count]=$row->selfcode;
		$bisinesscode[$count]=$row->bisinesscode;
		$assemble_idx[$count]=$row->assemble_idx;
		$assemble_info[$count]="";
		$assemble_productcode[$count]="";
		$package_idx[$count]=$row->package_idx;
		$package_info[$count]="";
		$package_productcode[$count]="";
		// 옵션가격, 옵션 주문수량, 옵션 type 추가
		// 2015 11 10 유동혁
		$optPrice[$count] = 0;
		$optQuantity[$count] = trim($row->quantityarr);
		//옵션 type - 0 상품 1 옵션
		$optType[$count] = 0;
		$option1[$count] = '';
		$option2[$count] = '';
		$optType[$count] = $row->op_type;

		if(ord($row->bisinesscode)>0) {
			$bisinessvalue[$row->bisinesscode]=$row->bisinesscode;
		}

		if($msg_type=="2") {
			$ordermessage[$count]=${"order_prmsg".$count};
		} else {
			$ordermessage[$count]=$order_prmsg;
		}
		$ordermessage[$count]=str_replace("'","",$ordermessage[$count]);
		//$ordermessage[$count] = enc_euckr($ordermessage[$count]);

		
		//######### 옵션에 따른 가격 변동 체크 ###############
		//$optionPrice = 0;
		$total_opotion_price = 0;
		//$optionarr = explode(chr(30), $row->optionarr);
		if ( strlen( trim($row->optionarr) ) == 0) {
			$price = $row->sellprice;
			$tempreserve = getReserveConversion($row->reserve,$row->reservetype, ( $price * $row->quantity ) ,"N");
			$optQuantity[$count] = $row->quantity; // 옵션이 존재 안할경우 옵션 수량은 상품 수량에 맞춰짐
		} else if ( strlen( trim($row->optionarr) ) > 0) {
			$opPriceSql = "SELECT option_price FROM tblproduct_option WHERE productcode='".$row->productcode."' AND option_code ='".trim($row->optionarr)."' ";
			$opPriceRes = pmysql_query( $opPriceSql, get_db_conn() );
			if( $opPriceRow = pmysql_fetch_object( $opPriceRes ) ){
				$total_opotion_price = $opPriceRow->option_price * $row->quantityarr;
				//$optionPrice = $opPriceRow->option_price * $row->quantityarr;
				$optPrice[$count] = $opPriceRow->option_price;
				$price =  $row->sellprice;
				// 추가 옵션은 적립금이 % 일 경우에만 적립이 가능함
				if( $row->op_type == '0' || $row->reservetype == 'Y') {
					//$tempreserve = getReserveConversion($row->reserve,$row->reservetype,( $price * $row->quantity ) + $total_opotion_price,"N");
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,( $price + $opPriceRow->option_price ) * $row->quantityarr,"N");
				} else {
					$tempreserve = 0;
				}
			}
			// tblorderproduct.opt1_name , tblorderproduct.opt2_name 값 세팅
			$tmpOptArr = explode( chr(30), $row->optionarr );
			$tmpOpt1Arr = explode( ',', $row->option1 );
			$tmpOpt2Arr = explode( ',', $row->option2 );
			//필수 옵션이 존재할 경우
			if( $row->op_type == '0' && strlen( trim( $tmpOptArr[0] ) )  > 0  ) {
				$option1[$count] = $tmpOpt1Arr[0].'::'.$tmpOptArr[0];
				if( strlen( trim( $tmpOptArr[1] ) ) > 0 ){
					$option2[$count] = $tmpOpt2Arr[0].'::'.$tmpOptArr[1];
				} else {
					$option2[$count] = '';
				}
			//추가 옵션이 존재할 경우
			} else if( $row->op_type == '1' && strlen( trim( $tmpOptArr[0] ) )  > 0 ) {
				$option1[$count] = $tmpOptArr[0].'::'.$tmpOptArr[1];
				$option2[$count] = '';
			//상품만 있을경우
			} else {
				$option1[$count] = '';
				$option2[$count] = '';
			}
			if(ord($optvalue2[$count])>0) $option1[$count]=$optvalue2[$count];
			unset( $tmpOptArr );
			unset( $tmpOpt1Arr );
			unset( $tmpOpt2Arr );
		}		

		$realreserve[$count]=$tempreserve;

		if (ord($goodname)>0) $goodname = $row->productname." 등.."; else $goodname = $row->productname;

		//######### 상품 특별할인률 적용 ############
		$dc_data = $product->getProductDcRate($row->productcode);
		$salemoney = getProductDcPrice($price,$dc_data[price]);
		$salereserve = getProductDcPrice($price,$dc_data[reserve]);
		
		//추가 적립금 적용
		$tempreserve+=$salereserve;
		$realreserve[$count] = $tempreserve;

		//회원 할인율 적용
		$bf_price = $price;
		$price = $price - $salemoney;
		$tot_salemoney+=$salemoney*$row->quantity;

		//######### 옵션에 따른 가격 변동 체크 끝 ############
		$bf_sumprice += ($bf_price*$row->quantity)+$total_opotion_price;  //할인전 총 금액(총구매금액대별 할인률을 구하기 위해)
		$sumprice += ($price*$row->quantity)+$total_opotion_price;
		$vender_sumprice +=  ($price * $row->quantity ) + $total_opotion_price;
		$reserve += $tempreserve*$row->quantity;

		$arrvender[0]["sumprice"]+=$bf_price*$row->quantity;
		if($vgrp->vender>0) {
			$arrvender[$vgrp->vender]["sumprice"]+=$bf_price*$row->quantity;
		}

		$date[$count]=substr($row->date, 0, 8);
//		$realprice[$count]=$price;
		# 추가옵션일경우 가격이 0원임
		if( $row->op_type == '0' ){
			$realprice[$count]=$bf_price;
		} else {
			$realprice[$count]=0;
		}
		// 쿠폰가를 구하기 위한 array
		$aryRealPrice[$row->productcode] += ( $price * $row->quantity ) + $total_opotion_price;		

		########### 쿠폰 관련 ###############
		$prprice[0][$row->productcode]=($price*$row->quantity)+$total_opotion_price;
		$prprice[0][substr($row->productcode,0,3)]+=($price*$row->quantity)+$total_opotion_price;
		$prprice[0][substr($row->productcode,0,6)]+=($price*$row->quantity)+$total_opotion_price;
		$prprice[0][substr($row->productcode,0,9)]+=($price*$row->quantity)+$total_opotion_price;
		$prprice[0][substr($row->productcode,0,12)]+=($price*$row->quantity)+$total_opotion_price;

		$prname[0][$row->productcode]=$row->productname.", ";
		$prname[0][substr($row->productcode,0,3)].=$row->productname.", ";
		$prname[0][substr($row->productcode,0,6)].=$row->productname.", ";
		$prname[0][substr($row->productcode,0,9)].=$row->productname.", ";
		$prname[0][substr($row->productcode,0,12)].=$row->productname.", ";
		if($vgrp->vender>0) {
			$prprice[$vgrp->vender][$row->productcode]=($price*$row->quantity)+$total_opotion_price;
			$prprice[$vgrp->vender][substr($row->productcode,0,3)]+=($price*$row->quantity)+$total_opotion_price;
			$prprice[$vgrp->vender][substr($row->productcode,0,6)]+=($price*$row->quantity)+$total_opotion_price;
			$prprice[$vgrp->vender][substr($row->productcode,0,9)]+=($price*$row->quantity)+$total_opotion_price;
			$prprice[$vgrp->vender][substr($row->productcode,0,12)]+=($price*$row->quantity)+$total_opotion_price;

			$prname[$vgrp->vender][$row->productcode]=$row->productname.", ";
			$prname[$vgrp->vender][substr($row->productcode,0,3)].=$row->productname.", ";
			$prname[$vgrp->vender][substr($row->productcode,0,6)].=$row->productname.", ";
			$prname[$vgrp->vender][substr($row->productcode,0,9)].=$row->productname.", ";
			$prname[$vgrp->vender][substr($row->productcode,0,12)].=$row->productname.", ";
		}


		$allprname.=$row->productname.", ";

		//######## 특수값체크 : 현금결제상품//무이자상품 #####
		
		if( $row->bankonly == 'Y' ){
			$bankonly = "Y";
		}
		if( $row->setquota == 'Y' ){
			if ($card_splittype=="O" && $sumprice>=$card_splitprice) {
				$setquotacnt++;
			}
		}



		//################ 개별 배송비 체크 #################
		

		if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
			if( $row->op_type == '0' && strpos( $inProducts, $row->productcode ) === false ){ //추가옵션은 개별배송비를 먹이지 않는다
				if($row->deli=="Y" ) { 
					$tmpPrDeliPrice = $row->deli_price*$row->quantity;
					$deli_productprice += $row->deli_price*$row->quantity;
					$deli_productname2.=$row->productname.", ";
				} else {
					$tmpPrDeliPrice = $row->deli_price;
					$deli_productprice += $row->deli_price;
					$deli_productname2.=$row->productname.", ";
					$inProducts.= $row->productcode.','; //상품별 개별배송비 확인을 위한 부분
				}
			}
		} else if($row->deli=="F" || $row->deli=="G" ) {
			if( $row->op_type == '0' && strpos( $inProducts, $row->productcode ) === false ){ //추가옵션은 개별배송비를 먹지 않는다.
				$deli_productprice += 0;
				if($row->deli=="F") {
					$deli_productname2.=$row->productname.", ";
				} else {
					$deli_productname2.=$row->productname.", ";
				}
			}
		} else if( $row->deli=="Z" && strpos( $inProducts, $row->productcode ) === false ) {
			$tempDeilPrice_Z = 0;
			$tempDeilQty_1 = $row->quantity % $row->deli_qty;
			$tempDeliQty_2 = floor( $row->quantity / $row->deli_qty );
			$tempDeilPrice_Z = $row->deli_price * ( $tempDeilQty_1 + $tempDeliQty_2 );
			$deli_productprice += $tempDeilPrice_Z;
			$tmpPrDeliPrice = $tempDeilPrice_Z;
			$deli_productname2.=$row->productname.", ";
		}else {
			$deli_init=true;
			$vender_delisumprice += ($price*$row->quantity)+$total_opotion_price;
		}
		
		$deli_productname1.=$row->productname.", ";

		$basketcnt[0]++;
		if($vgrp->vender>0) $basketcnt[$vgrp->vender]++;
		$count++;
		$prDeliPrcie[$vgrp->vender][$row->productcode] += $tmpPrDeliPrice;
	}
	pmysql_free_result($result);
	$deli_area="";
	$deli_productname="";
	//상품 개별 배송비
	$vender_deliprice=$deli_productprice;
	
	if($deli_productprice>0) {
		$deli_productname=$deli_productname2;
	}
	$vender_deliarealimit_init=false;
	if($_vender) {
		$arr_delisubj[$vgrp->vender]="";
		if(ord($_vender->deli_area_limit)>0) {
			if($_vender->deli_pricetype=="Y") {
				$vender_delisumprice = $vender_sumprice;
			}
			$vender_deliarealimit = "";
			$vender_deliarealimit_exp = "";
			$deli_area_limit_exp = "";
			$deli_area_limit_exp1 = "";
			$deli_area_limit_exp2 = "";
			
			$deli_area_limit_exp = explode(":",$_vender->deli_area_limit);
			for($i=0; $i<count($deli_area_limit_exp); $i++) {
				$deli_area_limit_exp1=explode("=",$deli_area_limit_exp[$i]);
				
				$deli_area_limit_exp2=explode(",",$deli_area_limit_exp1[0]);
				for($jj=0;$jj<count($deli_area_limit_exp2);$jj++){
					if(ord(trim($deli_area_limit_exp2[$jj]))>0 && strpos($address,$deli_area_limit_exp2[$jj])>0) {
						$vender_deliarealimit = setDeliLimit($vender_delisumprice,@implode("=", @array_slice($deli_area_limit_exp1, 1)),"Y");
						if(ord($vender_deliarealimit)>0) {
							$vender_deliarealimit_exp = explode("", $vender_deliarealimit);
							$vender_deliarealimit_init=true;
							$vender_deliprice+=$vender_deliarealimit_exp[0];
							$arr_delisubj[$vgrp->vender]="해당 배송지 {$deli_area_limit_exp2[$jj]}이고 상품 구매합계가 {$vender_deliarealimit_exp[1]}인 경우";
							break;
						}
					}
				}
				if(ord($vender_deliarealimit_exp[0])>0) {
					break;
				}
			}
		}
		
		if($vender_deliarealimit_init==false){
			if($_vender->deli_price>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_vender->deli_mini && $deli_init) {
					$prDeliPrcie[$vgrp->vender]['deli'] += $_vender->deli_price;
					$vender_deliprice+=$_vender->deli_price;
					$deli_productname=$deli_productname1;

					if($_vender->deli_mini<1000000000) {
						$arr_delisubj[$vgrp->vender]="해당 상품 구매합계가 ".number_format($_vender->deli_mini)."원 미만인 경우";
					} else {
						$arr_delisubj[$vgrp->vender]="해당 상품 구매시 무조건 청구";
					}
				}
			// 차등 구매금액별 배송료
			} else if(ord($_vender->deli_limit)>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}
				if($deli_init) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_vender->deli_limit,"Y");
					$delilmitprice_exp = explode("", $delilmitprice);
					$vender_deliprice+=$delilmitprice_exp[0];
					$deli_productname=$deli_productname1;
					
					$arr_delisubj[$vgrp->vender]="해당 상품 구매합계가 {$delilmitprice_exp[1]}인 경우";
				}
			}
		}
		$deli_area=$_vender->deli_area;
	} else {
		$arr_delisubj[$vgrp->vender]="";
		if(ord($_data->deli_area_limit)>0) {
			if($_data->deli_basefeetype=="Y") {
				$vender_delisumprice = $vender_sumprice;
			}
			
			$vender_deliarealimit = "";
			$vender_deliarealimit_exp = "";
			$deli_area_limit_exp = "";
			$deli_area_limit_exp1 = "";
			$deli_area_limit_exp2 = "";
			
			$deli_area_limit_exp = explode(":",$_data->deli_area_limit);
			for($i=0; $i<count($deli_area_limit_exp); $i++) {
				$deli_area_limit_exp1=explode("=",$deli_area_limit_exp[$i]);
				
				$deli_area_limit_exp2=explode(",",$deli_area_limit_exp1[0]);
				for($jj=0;$jj<count($deli_area_limit_exp2);$jj++){
					if(ord(trim($deli_area_limit_exp2[$jj]))>0 && strpos($address,$deli_area_limit_exp2[$jj])>0) {
						$vender_deliarealimit = setDeliLimit($vender_delisumprice,@implode("=", @array_slice($deli_area_limit_exp1, 1)),"Y");
						
						if(ord($vender_deliarealimit)>0) {
							$vender_deliarealimit_exp = explode("", $vender_deliarealimit);
							$vender_deliarealimit_init=true;
							$vender_deliprice+=$vender_deliarealimit_exp[0];
							$arr_delisubj[$vgrp->vender]="해당 배송지 {$deli_area_limit_exp2[$jj]}이고 상품 구매합계가 {$vender_deliarealimit_exp[1]}인 경우";
							break;
						}
					}
				}
				if(ord($vender_deliarealimit_exp[0])>0) {
					break;
				}
			}
		}

		if($vender_deliarealimit_init==false){
			if($_data->deli_basefee>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_data->deli_miniprice && $deli_init) {
					$prDeliPrcie[$vgrp->vender]['deli'] += $_data->deli_basefee;
					$vender_deliprice+=$_data->deli_basefee;
					$deli_productname=$deli_productname1;

					if($_data->deli_miniprice<1000000000) {
						$arr_delisubj[$vgrp->vender]="해당 상품 구매합계가 ".number_format($_data->deli_miniprice)."원 미만인 경우";
					} else {
						$arr_delisubj[$vgrp->vender]="해당 상품 구매시 무조건 청구";
					}
				}
			// 차등 구매금액별 배송료
			} else if(ord($_data->deli_limit)>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if($deli_init) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit,"Y");
					$delilmitprice_exp = explode("", $delilmitprice);
					$vender_deliprice+=$delilmitprice_exp[0];
					$deli_productname=$deli_productname1;

					$arr_delisubj[$vgrp->vender]="해당 상품 구매합계가 {$delilmitprice_exp[1]}인 경우";
				}
			}
		}
		$deli_area=$_data->deli_area;
	}
	
	if($deli_productprice>0) {
		if(ord($arr_delisubj[$vgrp->vender])>0) {
			$arr_delisubj[$vgrp->vender].=", 상품 개별배송비 포함";
		} else {
			$arr_delisubj[$vgrp->vender].="상품 개별배송비 포함";
		}
	}

	//지역별 배송료를 계산한다.
	$area_price=0;
	$array_deli = explode("|",$deli_area);
	$cnt2= floor(count($array_deli)/2);
	for($kk=0;$kk<$cnt2;$kk++){
		$subdeli=explode(",",$array_deli[$kk*2]);
		for($jj=0;$jj<count($subdeli);$jj++){
			if(ord(trim($subdeli[$jj]))>0 && strpos($address,$subdeli[$jj])>0) {
				$area_price=$array_deli[$kk*2+1];
			}
		}
	}

	if($area_price>0) {
		if(ord($arr_delisubj[$vgrp->vender])>0) {
			$arr_delisubj[$vgrp->vender].=", 해당 배송지 추가배송료";
		} else {
			$arr_delisubj[$vgrp->vender].="해당 배송지 추가배송료";
		}
	}

	$vender_deliprice+=$area_price;
	if($vender_deliprice>0) {
		
		//그룹배송비 무료처리
		if($group_deli_free!='1'){
			$arr_deliprice[$vgrp->vender]=$vender_deliprice;
		}else{
	
			$arr_deliprice[$vgrp->vender]=0;
		}
		$arr_delimsg[$vgrp->vender]=substr($deli_productname,0,-2);
	}
	$deli_price+=$vender_deliprice;
}
pmysql_free_result($res);

//그룹배송비 무료처리
if($group_deli_free=='1'){
	
	$deli_price=0;
}

if(count($bisinessvalue)>0) {
	$bisinessvalue_imp = implode("','", $bisinessvalue);
	$bisql = "SELECT companyviewval, companycode ";
	$bisql.= "FROM tblproductbisiness ";
	$bisql.= "WHERE companycode IN ('{$bisinessvalue_imp}') ";
	$biresult=pmysql_query($bisql,get_db_conn());

	while($birow = pmysql_fetch_object($biresult)) {
		$companyviewval[$birow->companycode] = str_replace($orderpatten,$orderreplace,$birow->companyviewval);
	}
}
// 현금결제상품이 있는데 카드결제선택시
if ($bankonly=="Y" && !strstr("BVOQ",$paymethod)) {
	echo "<html></head><body onload=\"alert('현금결제 상품이 있기 때문에 무통장 입금 결제만 선택하실 수 있습니다.');parent.document.form1.process.value='N';parent.ProcessWait('hidden');\"></body></html>";
	exit;
}

// 전체상품(basketcnt)과 무이자셋팅상품(setquotacnt)이 같고 무이자적용이개별상품으로 선택되어 있으면
if ($basketcnt[0]==$setquotacnt && $setquotacnt>0 && $card_splittype=="O") $card_splittype="Y";

if($reserve_limit<0) $reserve_limit=(int)($sumprice*abs($reserve_limit)/100);

$usereserve = str_replace(",","",$usereserve);

if ($usereserve>0) {
	if($reserve_maxprice>$sumprice)
		$usereserve=0;
	else if($user_reserve>=$_data->reserve_maxuse && $usereserve<=$reserve_limit && $usereserve<=$user_reserve) {
		$reserve_type="Y";
	} else $usereserve=0;
} else $usereserve=0;

if($_data->coupon_ok=="Y" && strlen($coupon_code)==8 && $rcall_type=="N" && $usereserve>0) {
	$usereserve=0;
}

if($sumprice<$_data->bank_miniprice) {
	echo "<html></head><body onload=\"alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');parent.location.href='basket.php'\"></body></html>";
	exit;
} else if($sumprice<=0) {
	echo "<html></head><body onload=\"alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다..');parent.location.href='basket.php'\"></body></html>";
	exit;
}

if(strstr("CP", $paymethod)) {
	if($_data->card_miniprice>$sumprice) {
		echo "<html></head><body onload=\"alert('카드결제 최소 주문금액보다 결제금액이 작습니다.');parent.location.href='basket.php'\"></body></html>";
		exit;
	}
} else if(strstr("BVOQ",$paymethod) && $sumprice<$_data->bank_miniprice) {
	echo "<html></head><body onload=\"alert('최소 주문금액보다 결제금액이 작습니다.');parent.location.href='basket.php'\"></body></html>";
	exit;
}

if( ( $sumprice + $deli_price ) != $total_sum ){
	echo "<html></head><body onload=\"alert('결제금액이 일치하지 않습니다.');parent.location.href='basket.php'\"></body></html>";
	exit;
}

if ($reserve_type=="N") $usereserve=0;

?>