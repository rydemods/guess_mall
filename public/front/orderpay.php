<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

$script="window.close();opener.ProcessWait('hidden');";

$ip = $_SERVER['REMOTE_ADDR'];

$sslchecktype="";
if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
	$sslchecktype="ssl";
}
if($sslchecktype=="ssl") {
	$secure_data=getSecureKeyData($_POST["sessid"]);
	if(!is_array($secure_data)) {
		echo "<html><head><title></title></head><body onload=\"alert('보안인증 정보가 잘못되었습니다.');opener.document.form1.process.value='N';{$script}\"></body></html>";
		exit;
	}
	foreach($secure_data as $key=>$val) {
		${$key}=$val;
	}
} else {
	$coupon_code=$_POST["coupon_code"];
	$usereserve=$_POST["usereserve"];
	$email=$_POST["email"];
	$mobile_num1=$_POST["mobile_num1"];
	$mobile_num=$_POST["mobile_num"];
	$address=$_POST["address"];
}
$email=str_replace("'","",strip_tags($email));
$address=str_replace("'","",strip_tags($address));

$address=" ".$address;	//지역별 배송료 구하기위해....

if (ord($usereserve) && !IsNumeric($usereserve)) {
	echo "<html></head><body onload=\"alert('적립금은 숫자만 입력하시기 바랍니다.');opener.document.form1.process.value='N';{$script}\"></body></html>";
	exit;
}

$card_splittype=$_data->card_splittype;
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

if($bankreserve=="N" && $usereserve>0) {
	$bankonly="R";
}

$paylistnum="0,1,2,3,4,5";
//0:무통장, 1:신용카드, 2:실시간계좌이체, 3:가상계좌, 4:에스크로, 5:핸드폰

$user_reserve=0;
$reserve_type="N";
if(strlen($_ShopInfo->getMemid())>0) {
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$user_reserve = $row->reserve;
		$group_code=$row->group_code;
		pmysql_free_result($result);

		if(ord($group_code) && $group_code!=NULL) {
			$sql = "SELECT * FROM tblmembergroup WHERE group_code='{$group_code}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$group_code=$row->group_code;
				$group_name=$row->group_name;
				$group_type=substr($row->group_code,0,2);
				$group_usemoney=$row->group_usemoney;
				$group_addmoney=$row->group_addmoney;
				$group_payment=$row->group_payment;
			}
			pmysql_free_result($result);
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 회원 아이디가 존재하지 않습니다.');opener.document.form1.process.value='N';{$script}\"></body></html>";
		exit;
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
	if(ord($basketrow2->package_title) && ord($basketrow2->package_idx) && $basketrow2->package_idx>0) {
		$package_title_exp = explode("",$basketrow2->package_title);
		$package_price_exp = explode("",$basketrow2->package_price);
		$package_list_exp = explode("", $basketrow2->package_list);

		$title_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx] = $package_title_exp[$basketrow2->package_idx];

		if(strlen($package_list_exp[$basketrow2->package_idx])>1) {
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
$sql = "SELECT a.quantity as sumquantity,b.productcode,b.productname,b.display,b.quantity,b.group_check, ";
$sql.= "b.option_quantity,b.etctype,b.assembleuse,a.assemble_list AS basketassemble_list ";
$sql.= ", c.assemble_list,a.package_idx ";
$sql.= "FROM tblbasket a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
$sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
$sql.= "AND a.productcode=b.productcode ";
$result=pmysql_query($sql,get_db_conn());
$assemble_proquantity_cnt=0;
while($row=pmysql_fetch_object($result)) {
	if($row->display!="Y") {
		$errmsg="[".str_replace("'","",$row->productname)."]상품은 판매가 되지 않는 상품입니다.\\n";
	}
	if($row->group_check!="N") {
		if(strlen($_ShopInfo->getMemid())>0) {
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
			$errmsg="[".str_replace("'","",$row->productname)."]상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\\n";
		}
	}

	if(ord($errmsg)==0) {
		if ($miniq!=1 && $miniq>1 && $row->sumquantity<$miniq)
			$errmsg.="[".str_replace("'","",$row->productname)."]상품은 최소 {$miniq}개 이상 주문하셔야 합니다.\\n";

		if ($maxq!="?" && $maxq>0 && $row->sumquantity>$maxq)
			$errmsg.="[".str_replace("'","",$row->productname)."]상품은 최대 {$maxq}개 이하로 주문하셔야 합니다.\\n";

		if(ord($row->quantity)) {
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
			$assemble_proquantity_cnt++;
		}
		if(count($assemble_list_exp)>0) { // 구성상품의 재고 체크
			$assemprosql = "SELECT productcode,quantity,productname FROM tblproduct ";
			$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
			$assemprosql.= "AND display = 'Y' ";
			$assemproresult=pmysql_query($assemprosql,get_db_conn());
			while($assemprorow=@pmysql_fetch_object($assemproresult)) {
				if(ord($assemprorow->quantity)) {
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
				if(ord($package_productcode_tmpexp[$i])) {
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
		} else { // 일반상품의 재고 체크
			if(ord($row->quantity)) {
				if($assemble_proquantity[$assemprorow->productcode]>$row->quantity) {
					if ($row->quantity>0) {
						$errmsg.="[".str_replace("'","",$row->productname)."]상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$row->quantity} 개 입니다.")."\\n";
					} else {
						$errmsg.= "[".str_replace("'","",$row->productname)."]상품의 재고가 다른고객 주문등의 이유로 장바구니 수량보다 작습니다.\\n";
					}
				}
			}
		}
		if(ord($row->option_quantity)) {
			$sql = "SELECT opt1_idx, opt2_idx, quantity FROM tblbasket ";
			$sql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode='{$row->productcode}' ";
			$result2=pmysql_query($sql,get_db_conn());
			while($row2=pmysql_fetch_object($result2)) {
				$optioncnt = explode(",",ltrim($row->option_quantity,','));
				$optionvalue=$optioncnt[($row2->opt2_idx==0?0:($row2->opt2_idx-1))*10+($row2->opt1_idx-1)];

				if($optionvalue<=0 && $optionvalue!="") {
					$errmsg.="[".str_replace("'","",$row->productname)."]상품의 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
				} else if($optionvalue<$row2->quantity && $optionvalue!="") {
					$errmsg.="[".str_replace("'","",$row->productname)."]상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
				}
			}
			pmysql_free_result($result2);
		}
	}
}
pmysql_free_result($result);

if(ord($errmsg)) {
	echo "<html></head><body onload=\"alert('{$errmsg}');{$script}\"></body></html>";
	exit;
}
//재고수량 파악 끝

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
$orderpatten = array("'","\\\\");
$orderreplace = array("","");
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

	$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,a.date,b.productcode,b.productname,b.sellprice, ";
	$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
	$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice,a.assemble_list,a.assemble_idx,a.package_idx ";
	$sql.= "FROM tblbasket a, tblproduct b ";
	$sql.= "WHERE b.vender='{$vgrp->vender}' ";
	$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode ";
	$result=pmysql_query($sql,get_db_conn());

	$vender_sumprice = 0;	//해당 입점업체의 총 구매액
	$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
	$vender_deliprice = 0;
	$deli_productprice=0;
	$deli_init = false;
	while($row = pmysql_fetch_object($result)) {
		if(ord($prcode[0])) {
			if(substr($row->productcode,0,12)==substr($prcode[0],0,12)) $prcode[0]=substr($prcode[0],0,12);
			else if(substr($row->productcode,0,9)==substr($prcode[0],0,9)) $prcode[0]=substr($prcode[0],0,9);
			else if(substr($row->productcode,0,6)==substr($prcode[0],0,6)) $prcode[0]=substr($prcode[0],0,6);
			else if(substr($row->productcode,0,3)==substr($prcode[0],0,3)) $prcode[0]=substr($prcode[0],0,3);
			else $prcode[0]="";
		}
		if((int)$basketcnt[0]==0) $prcode[0]=$row->productcode;

		if($vgrp->vender>0) {
			if(ord($prcode[$vgrp->vender])) {
				if(substr($row->productcode,0,12)==substr($prcode[$vgrp->vender],0,12)) $prcode[$vgrp->vender]=substr($prcode[$vgrp->vender],0,12);
				else if(substr($row->productcode,0,9)==substr($prcode[$vgrp->vender],0,9)) $prcode[$vgrp->vender]=substr($prcode[$vgrp->vender],0,9);
				else if(substr($row->productcode,0,6)==substr($prcode[$vgrp->vender],0,6)) $prcode[$vgrp->vender]=substr($prcode[$vgrp->vender],0,6);
				else if(substr($row->productcode,0,3)==substr($prcode[$vgrp->vender],0,3)) $prcode[$vgrp->vender]=substr($prcode[$vgrp->vender],0,3);
				else $prcode[$vgrp->vender]="";
			}
			if((int)$basketcnt[$vgrp->vender]==0) $prcode[$vgrp->vender]=$row->productcode;
		}

		$optvalue2[$count]="";
		if(preg_match("/^\[OPTG\d{4}\]$/",$row->option1)) {
			$optioncode = substr($row->option1,5,4);
			$row->option_price="";
			if($row->optidxs!="") {
				$tempoptcode = substr($row->optidxs,0,-1);
				$exoptcode = explode(",",$tempoptcode);
				$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='{$optioncode}' ";
				$resultopt = pmysql_query($sqlopt,get_db_conn());
				if($rowopt = pmysql_fetch_object($resultopt)){
					$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
					$opti=0;
					$optvalue[$count]="";
					while(ord($optionadd[$opti])) {
						if($exoptcode[$opti]>0) {
							$opval = explode("",str_replace('"','',$optionadd[$opti]));
							$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
							$optvalue[$count].= ", {$opval[0]} : ";
							if ($exop[1]>0) $optvalue[$count].=$exop[0]."(<font color=#FF3C00>+{$exop[1]}원</font>)";
							else if($exop[1]==0) $optvalue[$count].=$exop[0];
							else $optvalue[$count].=$exop[0]."(<font color=#FF3C00>{$exop[1]}원</font>)";
							$row->sellprice+=$exop[1];
						}
						$opti++;
					}
					$optvalue[$count] = ltrim($optvalue[$count],',');
					$optcnt++;

					$optvalue2[$count] = sprintf("[OPTG%03d]",$optcnt);
				}
			}
		}

		$productcode[$count]=$row->productcode;
		$option_quantity[$productcode[$count]]=$row->option_quantity;
		$option1num[$count]=$row->opt1_idx;
		$option2num[$count]=($row->opt2_idx>0?$row->opt2_idx:1);
		$productname[$count]=str_replace($orderpatten,$orderreplace,$row->productname);
		$addcode[$count]=str_replace($orderpatten,$orderreplace,$row->addcode);
		$quantity[$count]=$row->quantity;

		if($row->assemble_idx>0 && strlen(str_replace("","",$row->assemble_list))>0) {
			$assemble_list_proexp = explode("",$row->assemble_list);
			$alprosql = "SELECT SUM(sellprice) AS assemble_sellprice FROM tblproduct ";
			$alprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_proexp)."') ";
			$alprosql.= "AND display = 'Y' ";
			$alproresult=pmysql_query($alprosql,get_db_conn());
			$alprorow=@pmysql_fetch_object($alproresult);

			//######### 코디/조립에 따른 가격 변동 체크 ###############
			$price = $alprorow->assemble_sellprice;
			$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$alprorow->assemble_sellprice,"N");
		} else if($row->package_idx>0 && ord($row->package_idx)) {
			//######### 옵션에 따른 가격 변동 체크 ###############
			if (ord($row->option_price)==0) {
				$price = $row->sellprice+$price_package_listtmp[$row->productcode][$row->package_idx];
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$price,"N");
			} else if (ord($row->opt1_idx)) {
				$option_price = $row->option_price;
				$pricetok=explode(",",$option_price);
				$priceindex = count($pricetok);
				$price = $pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$price,"N");
			}
		} else {
			//######### 옵션에 따른 가격 변동 체크 ###############
			if (ord($row->option_price)==0) {
				$price = $row->sellprice;
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
			} else if (ord($row->opt1_idx)) {
				$option_price = $row->option_price;
				$pricetok=explode(",",$option_price);
				$priceindex = count($pricetok);
				$price = $pricetok[$row->opt1_idx-1];
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
			}
		}
		$realreserve[$count]=$tempreserve;
		//######### 옵션에 따른 가격 변동 체크 끝 ############
		$sumprice += $price*$row->quantity;
		$vender_sumprice += $price*$row->quantity;
		$reserve += $tempreserve*$row->quantity;

		$arrvender[0]["sumprice"]+=$price*$row->quantity;
		if($vgrp->vender>0) {
			$arrvender[$vgrp->vender]["sumprice"]+=$price*$row->quantity;
		}

		if ($row->opt1_idx>0) {
			$temp = $row->option1;
			$tok = explode(",",$temp);
			$option1[$count]=$tok[0]." : ".$tok[$row->opt1_idx];
			$option1[$count]=str_replace("'","",$option1[$count]);
		}  // if
		if ($row->opt2_idx>0) {
			$temp = $row->option2;
			$tok = explode(",",$temp);
			$option2[$count]=$tok[0]." : ".$tok[$row->opt2_idx];
			$option2[$count]=str_replace("'","",$option2[$count]);
		}  // if
		if(ord($optvalue2[$count])) $option1[$count]=$optvalue2[$count];
		$date[$count]=$row->date;
		$realprice[$count]=$price;

		########### 쿠폰 관련 ###############
		$prprice[0][$row->productcode]=$price*$row->quantity;
		$prprice[0][substr($row->productcode,0,3)]+=$price*$row->quantity;
		$prprice[0][substr($row->productcode,0,6)]+=$price*$row->quantity;
		$prprice[0][substr($row->productcode,0,9)]+=$price*$row->quantity;
		$prprice[0][substr($row->productcode,0,12)]+=$price*$row->quantity;
		if($vgrp->vender>0) {
			$prprice[$vgrp->vender][$row->productcode]=$price*$row->quantity;
			$prprice[$vgrp->vender][substr($row->productcode,0,3)]+=$price*$row->quantity;
			$prprice[$vgrp->vender][substr($row->productcode,0,6)]+=$price*$row->quantity;
			$prprice[$vgrp->vender][substr($row->productcode,0,9)]+=$price*$row->quantity;
			$prprice[$vgrp->vender][substr($row->productcode,0,12)]+=$price*$row->quantity;
		}

		//######## 특수값체크 : 현금결제상품//무이자상품 #####
		if (ord($row->etctype)) {
			$etctemp = explode("|",$row->etctype);
			for ($i=0;$i<count($etctemp);$i++) {
				switch ($etctemp[$i]) {
					case "BANKONLY":
						$bankonly = "Y";
						break;
					case "SETQUOTA":
						if ($card_splittype=="O" && $sumprice>=$card_splitprice) {
							$setquotacnt++;
						}
						break;
				}
			}
		}

		//################ 개별 배송비 체크 #################
		if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
			if($row->deli=="Y") {
				$deli_productprice += $row->deli_price*$row->quantity;
			} else {
				$deli_productprice += $row->deli_price;
			}
		} else if($row->deli=="F" || $row->deli=="G") {
			$deli_productprice += 0;
		} else {
			$deli_init=true;
			$vender_delisumprice += $price*$row->quantity;
		}

		$basketcnt[0]++;
		if($vgrp->vender>0) $basketcnt[$vgrp->vender]++;

		$count++;
	}
	pmysql_free_result($result);

	$deli_area="";
	$vender_deliprice=$deli_productprice;
	$vender_deliarealimit_init=false;
	if($_vender) {
		if(ord($_vender->deli_area_limit)) {
			if($_vender->deli_pricetype=="Y") {
				$vender_delisumprice = $vender_sumprice;
			}

			$vender_deliarealimit = "";
			$deli_area_limit_exp = "";
			$deli_area_limit_exp1 = "";
			$deli_area_limit_exp2 = "";

			$deli_area_limit_exp = explode(":",$_vender->deli_area_limit);
			for($i=0; $i<count($deli_area_limit_exp); $i++) {
				$deli_area_limit_exp1=explode("=",$deli_area_limit_exp[$i]);

				$deli_area_limit_exp2=explode(",",$deli_area_limit_exp1[0]);
				for($jj=0;$jj<count($deli_area_limit_exp2);$jj++){
					if(strlen(trim($deli_area_limit_exp2[$jj]))>0 && strpos($address,$deli_area_limit_exp2[$jj])>0) {
						$vender_deliarealimit = setDeliLimit($vender_delisumprice,@implode("=", @array_slice($deli_area_limit_exp1, 1)));

						if(ord($vender_deliarealimit)) {
							$vender_deliarealimit_init=true;
							$vender_deliprice+=$vender_deliarealimit;
							break;
						}
					}
				}
				if(ord($vender_deliarealimit)) {
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
					$vender_deliprice+=$_vender->deli_price;
				}
			} else if(ord($_vender->deli_limit)) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}
				if($deli_init) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_vender->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		}
		$deli_area=$_vender->deli_area;
	} else {
		if(ord($_data->deli_area_limit)) {
			if($_data->deli_basefeetype=="Y") {
				$vender_delisumprice = $vender_sumprice;
			}

			$vender_deliarealimit = "";
			$deli_area_limit_exp = "";
			$deli_area_limit_exp1 = "";
			$deli_area_limit_exp2 = "";

			$deli_area_limit_exp = explode(":",$_data->deli_area_limit);
			for($i=0; $i<count($deli_area_limit_exp); $i++) {
				$deli_area_limit_exp1=explode("=",$deli_area_limit_exp[$i]);

				$deli_area_limit_exp2=explode(",",$deli_area_limit_exp1[0]);
				for($jj=0;$jj<count($deli_area_limit_exp2);$jj++){
					if(strlen(trim($deli_area_limit_exp2[$jj]))>0 && strpos($address,$deli_area_limit_exp2[$jj])>0) {
						$vender_deliarealimit = setDeliLimit($vender_delisumprice,@implode("=", @array_slice($deli_area_limit_exp1, 1)));

						if(ord($vender_deliarealimit)) {
							$vender_deliarealimit_init=true;
							$vender_deliprice+=$vender_deliarealimit;
							break;
						}
					}
				}
				if(ord($vender_deliarealimit)) {
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
					$vender_deliprice+=$_data->deli_basefee;
				}
			} else if(ord($_data->deli_limit)) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if($deli_init) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		}
		$deli_area=$_data->deli_area;
	}

	//지역별 배송료를 계산한다.
	$area_price=0;
	$array_deli = explode("|",$deli_area);
	$cnt2= floor(count($array_deli)/2);
	for($kk=0;$kk<$cnt2;$kk++){
		$subdeli=explode(",",$array_deli[$kk*2]);
		for($jj=0;$jj<count($subdeli);$jj++){
			if(strlen(trim($subdeli[$jj]))>0 && strpos($address,$subdeli[$jj])>0) {
				$area_price=$array_deli[$kk*2+1];
			}
		}
	}

	$vender_deliprice+=$area_price;
	$deli_price+=$vender_deliprice;
}
pmysql_free_result($res);

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
	echo "<html></head><body onload=\"alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');{$script}\"></body></html>";
	exit;
} else if($sumprice<=0) {
	echo "<html></head><body onload=\"alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다.');{$script}\"></body></html>";
	exit;
}
if($_data->ETCTYPE["VATUSE"]=="Y") {
	$sumpricevat = return_vat($sumprice);
}
$banksumprice=$cardsumprice=$essumprice=$sumprice;
if($sumprice>0) {
	$salemoney=0;
	$salereserve=0;
	if(ord($group_type) && $group_type!=NULL && $sumprice>=$group_usemoney) {	//그룹 할인 관련
		if($group_type=="SW" || $group_type=="SP") {
			if($group_type=="SW") {
				$salemoney=$group_addmoney;
			} else if($group_type=="SP") {
				$salemoney=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00";
			}
			if(strstr("BN",$group_payment)) $bankdc_price="-".$salemoney;
			if(strstr("CN",$group_payment)) $carddc_price="-".$salemoney;
			if(strstr("N",$group_payment)) $esdc_price="-".$salemoney;
		}
	}

	if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && strlen($coupon_code)==8) {
		$date = date("YmdH");
		$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode, a.amount_floor,";
		$sql.= "a.mini_price,a.use_con_type1,a.use_con_type2,a.use_point,a.vender, b.date_start, b.date_end ";
		$sql.= "FROM tblcouponinfo a, tblcouponissue b ";
		$sql.= "WHERE b.id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND a.coupon_code=b.coupon_code AND b.date_start<='{$date}' ";
		$sql.= "AND (b.date_end>='{$date}' OR b.date_end='') ";
		$sql.= "AND a.coupon_code='{$coupon_code}' AND b.used='N' ";
		$resultcou = pmysql_query($sql,get_db_conn());
		if($rowcou=pmysql_fetch_object($resultcou)) {
			$code_a=substr($rowcou->productcode,0,3);
			$code_b=substr($rowcou->productcode,3,3);
			$code_c=substr($rowcou->productcode,6,3);
			$code_d=substr($rowcou->productcode,9,3);

			$likecode=$code_a;
			if($code_b!="000") $likecode.=$code_b;
			if($code_c!="000") $likecode.=$code_c;
			if($code_d!="000") $likecode.=$code_d;

			if($prcode[$rowcou->vender]=="") $prcode[$rowcou->vender]="ALL";  // 전체상품
			else {
				$prleng=strlen($rowcou->productcode);

				if($prleng==18) $tempprcode=$rowcou->productcode;
				else $tempprcode=$likecode;

				$num = strlen($tempprcode);
				$prcode[$rowcou->vender] = substr($prcode[$rowcou->vender],0,$num);
			}

			$rowcou->productcode=$likecode;

			if(($rowcou->mini_price==0 || $rowcou->mini_price<=$arrvender[$rowcou->vender]["sumprice"])  // 총구매금액이 일정금액보다 큰지 검사
			&& ($rowcou->use_con_type2=="Y" && ($rowcou->productcode==$prcode[$rowcou->vender] || $rowcou->productcode=="ALL" || ($rowcou->use_con_type1=="Y" && $rowcou->productcode!="ALL"))
			|| ($rowcou->use_con_type2=="N" && (($rowcou->use_con_type1=="Y" && $arrvender[$rowcou->vender]["sumprice"]-$prprice[$rowcou->vender][$rowcou->productcode]>0) || ($rowcou->use_con_type1=="N" && ord($prprice[$rowcou->vender][$rowcou->productcode])==0))))
			){
				if($rowcou->productcode=="ALL") $couponmoney = $arrvender[$rowcou->vender]["sumprice"];
				else if($rowcou->use_con_type2=="N")  $couponmoney = $arrvender[$rowcou->vender]["sumprice"]-$prprice[$rowcou->vender][$rowcou->productcode];
				else $couponmoney = $prprice[$rowcou->vender][$rowcou->productcode];

				$tempcoumoney = floor(($rowcou->sale_type<=2?($couponmoney/100*$rowcou->sale_money):($couponmoney>0?$rowcou->sale_money:0))/pow(10,$rowcou->amount_floor))*pow(10,$rowcou->amount_floor);
				if($rowcou->sale_type%2==0) {      // 주문금액할인
					$coumoney = -$tempcoumoney;
					$coureserve=0;
					if($rowcou->bank_only=="Y") {
						$banksumprice=$banksumprice + $coumoney;
					} else {
						$banksumprice=$banksumprice + $coumoney;
						$cardsumprice=$cardsumprice + $coumoney;
						$essumprice=$essumprice + $coumoney;
					}
				}
				// 그룹할인허용여부
				if($rowcou->use_point!="Y") {
					$bankdc_price=$carddc_price=$esdc_price="";
				}
			}
		}
		pmysql_free_result($resultcou);
	}
}

//결제 총 금액
if(ord($carddc_price)) $cardsumprice= $cardsumprice - $salemoney;
if(ord($bankdc_price)) $banksumprice= $banksumprice - $salemoney;
if(ord($esdc_price)) $essumprice= $essumprice - $salemoney;

$cardsumprice+=$deli_price;
$banksumprice+=$deli_price;
$essumprice+=$deli_price;

if($_data->ETCTYPE["VATUSE"]=="Y") {
	$banksumprice+=$sumpricevat;
	$cardsumprice+=$sumpricevat;
	$essumprice+=$sumpricevat;
}

if ($_data->card_payfee>0) {  // 카드결제시 추가 수수료 적용
	$cardsumprice = ((int) ($cardsumprice * (1+$_data->card_payfee/100) /100)) * 100;
} else if ($_data->card_payfee<0 && $sumprice>$usereserve) {
	if($_data->card_payfee<-50){
		$_data->card_payfee+=50;
		$saletype="Y";
	}
	$_data->card_payfee=abs($_data->card_payfee);
	$dctemp = floor(($banksumprice-$deli_price)/100*$_data->card_payfee/100)*100;
	if($saletype!="Y") $banksumprice=$banksumprice-$dctemp;
	else if($saletype=="Y") {
		$bankremess="▒ <font color=red><u>회원에 한해 현금 결제시 결제금액의 {$_data->card_payfee}%인 <b>".number_format($dctemp)."원</b>을 추가적립해 드립니다.</u></font>";
		$bankremess1="<br>".$bankremess;
		$bankremess2=$bankremess."<br>";
		$_data->card_payfee=0;
	}
}

$banklast_price = $banksumprice - $usereserve;
$cardlast_price = $cardsumprice - $usereserve;
$eslast_price = $essumprice - $usereserve;

if ($banklast_price<0) $banklast_price=0;
if ($cardlast_price<0) $cardlast_price=0;
if ($eslast_price<0) $eslast_price=0;

$escrow_info = GetEscrowType($_data->escrow_info);
if(ord($escrow_info["escrow_limit"])==0) $escrow_info["escrow_limit"]=100000;
if(ord($_data->escrow_id) && ($escrow_info["escrowcash"]=="Y" || $escrow_info["escrowcash"]=="A")) {
	$escrowok="Y";
} else {
	$escrowok="N";
	$escrow_info["escrowcash"]="";
	if($escrow_info["onlycash"]!="Y" && (ord($escrow_info["onlycard"])==0 && ord($escrow_info["nopayment"])==0)) $escrow_info["onlycash"]="Y";
}

if ($escrow_info["nopayment"]=="Y" && ord($_data->card_id)==0 && (int)$banklast_price>=$escrow_info["escrow_limit"]) {
	echo "<html></head><body onload=\"alert('".number_format($escrow_info["escrow_limit"])."원 이상 결제가 지원되지 않습니다.\\n\\n결제금액을 조절하여 주문하시기 바랍니다.');opener.document.form1.process.value='N';{$script}\"></body></html>";
	exit;
}
//esbank => Y:무통장 입금과 동일한 할인/적립을 적용하지 않음.
if ($escrow_info["esbank"]!="Y") $eslast_price=$banklast_price;

$smsok="N";
$sql="SELECT COUNT(*) as cnt FROM tblsmsinfo WHERE mem_bank='Y'";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
if($row->cnt==1){
	$smsok="Y";
}
pmysql_free_result($result);

/*
echo "sumprice => $sumprice<br>";
echo "banksumprice => $banksumprice<br>";
echo "cardsumprice => $cardsumprice<br>";
echo "essumprice => $essumprice<br><br>";

echo "banklast_price => $banklast_price<br>";
echo "cardlast_price => $cardlast_price<br>";
echo "eslast_price => $eslast_price<br><br>";

exit;
*/
?>

<html>
<head>
<title>결제</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
window.resizeTo(594,600);
function CheckForm() {
	try {
		if (document.form1.paymethod.length==null) {
			if(document.form1.paymethod.checked==false) {
				alert("결제방법을 선택하세요.");
				opener.document.form1.paymethod.value="";
				return;
			}
			opener.document.form1.paymethod.value=document.form1.paymethod.value;
		} else {
			var ispaymentcheck=false;
			for(i=0;i<document.form1.paymethod.length;i++) {
				if(document.form1.paymethod[i].checked) {
					opener.document.form1.paymethod.value=document.form1.paymethod[i].value;
					ispaymentcheck=true;
					break;
				}
			}
			if(ispaymentcheck==false) {
				alert("결제방법을 선택하세요.");
				opener.document.form1.paymethod.value="";
				return;
			}
		}
	} catch(e) {
		return;
	}

	if(opener.document.form1.paymethod.value=="B" && document.form1.pay_data1.selectedIndex!=0) {
		opener.document.form1.pay_data1.value=document.form1.pay_data1.options[document.form1.pay_data1.selectedIndex].value;
	} else if(opener.document.form1.paymethod.value=="B" && document.form1.pay_data1.selectedIndex<=0) {
		alert("입금계좌를 선택하세요.");
		opener.document.form1.paymethod.value="";
		document.form1.pay_data1.focus();
		return;
	}

	<?php  if($bankonly=="R") { ?>
	if(opener.document.form1.paymethod.value=="C" || opener.document.form1.paymethod.value=="M"){
		if(!confirm("현금입금시에만 적립금 사용이 가능합니다.\n\n결제를 계속 진행하시겠습니까?")) {
			return;
		}
	}
	<?php  } ?>

	
	if(opener.document.form1.paymethod.value=='B' && document.form1.bank_sender.value==''){
		alert("입금자명을 입력하세요.");
		return;
	}else{
		opener.document.form1.bank_sender.value=document.form1.bank_sender.value;
	}
	

	if(opener.document.form1.paymethod.value=="V") {
		if(typeof(document.form1.sender_resno1)=="object" && typeof(document.form1.sender_resno2)=="object") {
			if (document.form1.sender_resno1.value.length==0) {
				alert("계좌소유주 주민등록번호를 입력하세요.");
				document.form1.sender_resno1.focus();
				return;
			}
			if (document.form1.sender_resno2.value.length==0) {
				alert("계좌소유주 주민등록번호를 입력하세요.");
				document.form1.sender_resno2.focus();
				return;
			}

			var bb;
			bb = chkResNo(document.form1.sender_resno1.value+"-"+document.form1.sender_resno2.value);

			if (!bb) {
				alert("계좌소유주의 주민등록번호가 잘못되었습니다.\n\n다시 입력하세요");
				document.form1.sender_resno1.focus();
				return;
			}
			opener.document.form1.sender_resno.value=document.form1.sender_resno1.value+""+document.form1.sender_resno2.value
		}
	}

	opener.document.form1.pay_data2.value;

	opener.CheckForm();

	window.close();
}

function CheckPayment(val) {
	if(val=="V") {
		try {
			document.all["idx_trans"].style.display="";
		} catch (e) {}
	} else {
		try {
			document.all["idx_trans"].style.display="none";
		} catch (e) {}
	}
}

function bank_selected() {
	if (document.form1.paymethod.length==null) document.form1.paymethod.checked=true;
	else if (document.form1.paymethod[0].value=="B") document.form1.paymethod[0].checked=true;
}

function notuse(card_miniprice) {
	if (document.form1.paymethod.length==null)
		document.form1.paymethod.checked=false;
	else {
		for (i=0;i<document.form1.paymethod.length;i++) document.form1.paymethod[i].checked=false;
		if (document.form1.paymethod[0].value=="B") document.form1.paymethod[0].checked=true;
	}
	alert("주문금액이 "+card_miniprice+"원 이상이여야 결제가 가능합니다.");
}

function ordercancel(gbn) {
	window.close();
	try {
		opener.ordercancel(gbn);
	} catch (e) {}
}

function strnumkeyup2(field) {
	if (!isNumber(field.value)) {
		alert("숫자만 입력하세요.");
		field.value=strLenCnt(field.value,field.value.length - 1);
		field.focus();
		return;
	}
	if (field.name == "sender_resno1") {
		if (field.value.length == 6) {
			form1.sender_resno2.focus();
		}
	}
}

//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post>
<tr>
	<td><IMG  src="<?=$Dir?>images/common/orderpay_title.gif" border="0"></td>
</tr>
<tr>
	<td style="padding:10px;">
	<table cellpadding="0" cellspacing="0" width="100%">
<?php
$arrpayinfo=explode("=",$_data->bank_account);
$arrcardcom=array("A"=>"[<font color=red>KCP.CO.KR</font>]","B"=>"[<font color=red>dacompay.net (데이콤전자상거래)</font>]","C"=>"[<font color=red>allthegate.com (올더게이트)</font>]","D"=>"[<font color=red>inicis.com (이니시스)</font>]");
$cardid_info=GetEscrowType($_data->card_id);

//무통장
if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<$escrow_info["escrow_limit"]) {
	if(strstr("YN", $_data->payment_type)) {//결제방법이 모든결제 OR 온라인결제가 선택되었을 경우
		$pmethodlist[0].= "<tr>\n";
		$pmethodlist[0].= "	<td>\n";
		$pmethodlist[0].= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$pmethodlist[0].= "	<tr>\n";
		$pmethodlist[0].= "		<td>\n";
		$pmethodlist[0].= "		<input type=radio name=\"paymethod\" value=\"B\" onclick=\"CheckPayment(this.value)\"><img src=\"{$Dir}images/common/orderpay_icon1.gif\" border=\"0\" align=\"absmiddle\"> <font color=\"#F02800\"><b>결제금액 : ".number_format($banklast_price)."원</b></font>\n";
		$pmethodlist[0].= "		<div>입금자명 : <input type='text' name='bank_sender' value='".$_ShopInfo->memname."' size = '4'></div>";
		$pmethodlist[0].= "		<div>입금계좌 : <select name=\"pay_data1\" onchange=\"bank_selected()\" style=\"font-size:11px;font-family:'돋움,굴림';background-color:#404040;letter-spacing:-0.5pt;\">\n";
		if(ord($arrpayinfo[1])==0) $pmethodlist[0].= "			<option value=\"\" style=\"color:#ffffff;\">입금 계좌번호 선택 (반드시 주문자 성함으로 입금)</option>\n";
		else $pmethodlist[0].= "			<option value=\"\" style=\"color:#ffffff;\">{$arrpayinfo[1]}</option>\n";
		$count=0;
		if (ord($arrpayinfo[0])) {
			$tok = strtok($arrpayinfo[0],",");
			$count = 0;
			while ($tok) {
				$pmethodlist[0].="			<option value=\"{$tok}\" style=\"color:#ffffff;\">{$tok}</option>\n";
				$tok = strtok(",");
				$count++;
			}
		}
		$pmethodlist[0].= "		</select></div>";
		$pmethodlist[0].= "		</td>\n";
		$pmethodlist[0].= "	</tr>\n";
		$pmethodlist[0].= "	<tr>\n";
		$pmethodlist[0].= "		<td style=\"padding-left:21px;\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><font color=\"#999999\">";
		$pmethodlist[0].= "		".$bankremess2;
		$pmethodlist[0].= "		- <font color=\"#FF0000\">입금확인후</font>, 안전하고 빠르게 상품을 배송합니다.";
		if($smsok=="Y") {
			$pmethodlist[0].="<br>- 입금 계좌번호 및 금액을 고객의 핸드폰으로 발송해 드립니다!";
		}
		$pmethodlist[0].= "		</font></td>";
		$pmethodlist[0].= "	</tr>\n";
		$pmethodlist[0].= "	</table>\n";
		$pmethodlist[0].= "	</td>\n";
		$pmethodlist[0].= "</tr>\n";
	}
}

//2:신용카드
if(strstr("YC", $_data->payment_type) && ord($_data->card_id)) {
	$pmethodlist[1].= "<tr>\n";
	$pmethodlist[1].= "	<td>\n";
	$pmethodlist[1].= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	$pmethodlist[0].= "	<tr>\n";
	$pmethodlist[1].= "		<td>\n";
	if($_data->card_miniprice!=0 && $_data->card_miniprice>$cardlast_price) {
		$pmethodlist[1].= "		<input type=radio name=\"paymethod\" value=\"C\" onclick=\"CheckPayment(this.value);notuse('".number_format($_data->card_miniprice)."')\"><img src=\"{$Dir}images/common/orderpay_icon2.gif\" border=\"0\" align=\"absmiddle\"> - <font color=\"#F02800\"><b>카드결제 최소 주문 금액 : ".number_format($_data->card_miniprice)."원</b></font>\n";
	} else {
		$pmethodlist[1].= "		<input type=radio name=\"paymethod\" value=\"C\"".(($_data->payment_type=="C" && $bankonly!="Y")?"checked":"").($bankonly=="Y"?"onclick=\"this.checked=false;CheckPayment(this.value);alert('현금결제 상품이 있기 때문에 무통장 입금 결제만 선택하실 수 있습니다.');\"":"onclick=\"CheckPayment(this.value)\"")."><img src=\"{$Dir}images/common/orderpay_icon2.gif\" border=\"0\" align=\"absmiddle\"> \n";
		if($_data->card_payfee>0) {
			$pmethodlist[1].="<font color=\"#F02800\"><b>".(ord($arrpayinfo[2])==0?"비할인판매가":$arrpayinfo[2])." : ".number_format($cardlast_price)."원</b></font><font style=\"font-size:11px;letter-spacing:-0.5pt;\"> → 카드결제시, [현금할인가] 적용이 안됩니다.</font>";
		} else {
			$pmethodlist[1].="<font color=\"#F02800\"><b>결제금액 : ".number_format($cardlast_price)."원</b></font>";
		}
	}
	$pmethodlist[1].= "		</td>\n";
	$pmethodlist[1].= "	</tr>\n";
	$pmethodlist[1].= "	<tr>\n";
	$pmethodlist[1].= "		<td style=\"padding-left:21px;\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><font color=\"#999999\">";
	$pmethodlist[1].= "		- 신용카드 정보가 상점에 남지 않으며, <font color=\"#FF0000\">128bit SSL</font>로 암호화된 결제창이 새로 뜹니다!";
	$pmethodlist[1].= "		<br>- 결제후, 카드명세서에 {$arrcardcom[$cardid_info["PG"]]}로 표시됩니다!";
	$pmethodlist[1].= "		</font></td>";
	$pmethodlist[1].= "	</tr>\n";
	$pmethodlist[1].= "	<input type=hidden name=\"pay_data2\" value=\"신용카드결제 - 카드작성중\">\n";
	$pmethodlist[1].= "	</table>\n";
	$pmethodlist[1].= "	</td>\n";
	$pmethodlist[1].= "</tr>\n";
}

//2:실시간계좌이체
if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<$escrow_info["escrow_limit"]) {
	if(ord($_data->trans_id)) {
		$pmethodlist[2].= "<tr>\n";
		$pmethodlist[2].= "	<td>\n";
		$pmethodlist[2].= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$pmethodlist[2].= "	<tr>\n";
		$pmethodlist[2].= "		<td>\n";
		$pmethodlist[2].= "		<input type=radio name=\"paymethod\" value=\"V\" ".($cardid_info["PG"]=="C"?"":" onclick=\"CheckPayment(this.value)\"")."><img src=\"{$Dir}images/common/orderpay_icon3.gif\" border=\"0\" align=\"absmiddle\"> <font color=\"#F02800\"><b>결제금액 : ".number_format($banklast_price)."원</b></font>\n";
		$pmethodlist[2].= "		</td>\n";
		$pmethodlist[2].= "	</tr>\n";
		if($cardid_info["PG"]!="C") {
		$pmethodlist[2].= "	<tr id=\"idx_trans\" style=\"display:none\">\n";
		$pmethodlist[2].= "		<td style=\"padding-left:29\"><font color=\"#F02800\"><B>계좌소유주 주민번호 :</B></font> <input type=text name=sender_resno1 size=6 class=input onkeyup=\"strnumkeyup2(this)\">-<input type=text name=sender_resno2 size=7 class=input onkeyup=\"strnumkeyup2(this)\"></td>\n";
		$pmethodlist[2].= "	</tr>\n";
		}
		$pmethodlist[2].= "	<tr>\n";
		$pmethodlist[2].= "		<td style=\"padding-left:21px;\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><font color=\"#999999\">";
		$pmethodlist[2].= "		".$bankremess2;
		$pmethodlist[2].= "		- 본인 계좌 정보입력으로 결제금액이 이체되는 서비스입니다.<br>";
		$pmethodlist[2].= "		- 인터넷뱅킹과 동일한 보안방식을 적용하므로 안전하며, 상점에 정보가 남지 않습니다.";
		$pmethodlist[2].= "		</font></td>";
		$pmethodlist[2].= "	</tr>\n";
		$pmethodlist[2].= "	</table>\n";
		$pmethodlist[2].= "	</td>\n";
		$pmethodlist[2].= "</tr>\n";
	}
}

//3:가상계좌
if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<$escrow_info["escrow_limit"]) {
	if(ord($_data->virtual_id)) {
		$pmethodlist[3].= "<tr>\n";
		$pmethodlist[3].= "	<td>\n";
		$pmethodlist[3].= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$pmethodlist[3].= "	<tr>\n";
		$pmethodlist[3].= "		<td>\n";
		$pmethodlist[3].= "		<input type=radio name=\"paymethod\" value=\"O\" onclick=\"CheckPayment(this.value)\"><img src=\"{$Dir}images/common/orderpay_icon4.gif\" border=\"0\" align=\"absmiddle\"> <font color=\"#F02800\"><b>결제금액 : ".number_format($banklast_price)."원</b></font>\n";
		$pmethodlist[3].= "		</td>\n";
		$pmethodlist[3].= "	</tr>\n";
		$pmethodlist[3].= "	<tr>\n";
		$pmethodlist[3].= "		<td style=\"padding-left:21px;\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><font color=\"#999999\">";
		$pmethodlist[3].= "		".$bankremess2;
		$pmethodlist[3].= "		- <font color=\"#FF0000\">주의!</font> 1회용 계좌(가상계좌) 입금시, <font color=\"#FF0000\">이름/금액이</font> 꼭 일치해야 입금확인됩니다!";
		$pmethodlist[3].= "		</font></td>";
		$pmethodlist[3].= "	</tr>\n";
		$pmethodlist[3].= "	</table>\n";
		$pmethodlist[3].= "	</td>\n";
		$pmethodlist[3].= "</tr>\n";
	}
}

//4:에스크로
if(($escrow_info["escrowcash"]=="A" || ($escrow_info["escrowcash"]=="Y" && (int)$banklast_price>=$escrow_info["escrow_limit"])) && ord($_data->escrow_id)) {
	if ($escrow_info["percent"]>0) {  // 에스크로 결제시 추가 수수료
		$estemplast_price = ((int) ($eslast_price * ($escrow_info["percent"]/100) /10)) * 10;
		if($estemplast_price<300) $estemplast_price=300;
		$eslast_price+=$estemplast_price;
	}
	$pgid_info="";
	$pg_type="";
	$pgid_info=GetEscrowType($_data->escrow_id);
	$pg_type=trim($pgid_info["PG"]);

	if(strstr("ABCD",$pg_type)) {
		//KCP/데이콤/올더게이트/이니시스 가상계좌 에스크로 코딩
		$pmethodlist[4].= "<tr>\n";
		$pmethodlist[4].= "	<td>\n";
		$pmethodlist[4].= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$pmethodlist[4].= "	<tr>\n";
		$pmethodlist[4].= "		<td>\n";
		$pmethodlist[4].= "		<input type=radio name=\"paymethod\" value=\"Q\" onclick=\"CheckPayment(this.value)\"><img src=\"{$Dir}images/common/orderpay_icon6.gif\" border=\"0\" align=\"absmiddle\"> ";
		if ($escrow_info["percent"]>0){
			$pmethodlist[4].="<font color=\"#F02800\"><b>수수료 포함가 : ".number_format($eslast_price)."원</b></font></font>";
		} else {
			$pmethodlist[4].="<font color=\"#F02800\"><b>결제금액 : ".number_format($eslast_price)."원</b></font>";
		}
		$pmethodlist[4].= "		</td>\n";
		$pmethodlist[4].= "	</tr>\n";
		$pmethodlist[4].= "	<tr>\n";
		$pmethodlist[4].= "		<td style=\"padding-left:21px;\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><font color=\"#999999\">";
		$pmethodlist[4].= "		".$bankremess2;
		$pmethodlist[4].= "		- <font color=\"#FF0000\">주의!</font> 1회용 계좌(가상계좌) 입금시, <font color=\"#FF0000\">이름/금액이</font> 꼭 일치해야 입금확인됩니다!<br>";
		$pmethodlist[4].= "		- 에스크로를 통해서 구매결정을 하실 수 있는 결제방식입니다.\n";
		$pmethodlist[4].= "		</font></td>";
		$pmethodlist[4].= "	</tr>\n";
		$pmethodlist[4].= "	</table>\n";
		$pmethodlist[4].= "	</td>\n";
		$pmethodlist[4].= "</tr>\n";
	}
}

//5:핸드폰
if(ord($_data->mobile_id)) {
	$pmethodlist[5].= "<tr>\n";
	$pmethodlist[5].= "	<td>\n";
	$pmethodlist[5].= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	$pmethodlist[5].= "	<tr>\n";
	$pmethodlist[5].= "		<td>\n";
	$pmethodlist[5].= "		<input type=radio name=\"paymethod\" value=\"M\" ".($bankonly=="Y"?"onclick=\"this.checked=false;CheckPayment(this.value);alert('현금결제 상품이 있기 때문에 무통장 입금 결제만 선택하실 수 있습니다.');\"":"onclick=\"CheckPayment(this.value)\"")." ><img src=\"{$Dir}images/common/orderpay_icon5.gif\" border=\"0\" align=\"absmiddle\"> \n";
	if($_data->card_payfee>0) {
		$pmethodlist[5].="<font color=\"#F02800\"><b>".(ord($arrpayinfo[2])==0?"비할인판매가":$arrpayinfo[2])." : ".number_format($cardlast_price)."원</b></font><font style=\"font-size:11px;letter-spacing:-0.5pt;\"> → 핸드폰결제시, [현금할인가] 적용이 안됩니다.</font>";
	} else {
		$pmethodlist[5].="<font color=\"#F02800\"><b>결제금액 : ".number_format($cardlast_price)."원</b></font>";
	}
	$pmethodlist[5].= "		</td>\n";
	$pmethodlist[5].= "	</tr>\n";
	$pmethodlist[5].= "	<tr>\n";
	$pmethodlist[5].= "		<td style=\"padding-left:21px;\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><font color=\"#999999\">";
	$pmethodlist[5].= "		- 결제정보가 상점에 남지 않으며, 보안 적용된 결제창이 새로 뜹니다!<br>";
	$pmethodlist[5].= "		- 결제후, 핸드폰 요금청구서에 ";
	if(strstr("A",$pg_type)) {
		$pmethodlist[5].= "'소액결제'";
	} else if(strstr("C",$pg_type)) {
		$pmethodlist[5].= "'(주)다날'";
	} else if(strstr("D",$pg_type)) {
		$pmethodlist[5].= "'인포허브/모빌리언스'";
	} else {
		$pmethodlist[5].= "'(주)인포허브'";
	}
	$pmethodlist[5].= "		로 표시됩니다!";
	$pmethodlist[5].= "		</font></td>";
	$pmethodlist[5].= "	</tr>\n";
	$pmethodlist[5].= "	<input type=hidden name=\"pay_data2\" value=\"핸드폰결제 - 작성중\">\n";
	$pmethodlist[5].= "	</table>\n";
	$pmethodlist[5].= "	</td>\n";
	$pmethodlist[5].= "</tr>\n";
}

	$arlist = explode(",",$paylistnum);
	$arcnt = count($arlist);
	$cnt=0;
	for($i=0;$i<$arcnt;$i++) {
		if(ord($pmethodlist[$arlist[$i]])) {
			echo $pmethodlist[$arlist[$i]];
			echo "<tr>\n";
			echo "	<td height=\"25\"><hr size=\"1\" noshade color=\"#F3F3F3\" width=\"100%\"></td>\n";
			echo "</tr>\n";

			$cnt++;
		}
	}
?>
	<tr>
		<td valign="bottom" align="center"><a href="javascript:CheckForm()" onMouseOver="window.status='주문완료';return true;"><img src="<?=$Dir?>images/common/orderpay_btnok.gif" border="0"></a><a href="javascript:ordercancel('cancel')" onMouseOver="window.status='주문취소';return true;"><img src="<?=$Dir?>images/common/orderpay_btncancel.gif" border="0" hspace="5"></a></td>
	</tr>
	</table>
	</td>
</tr>
</form>
</table>
</body>
</html>
