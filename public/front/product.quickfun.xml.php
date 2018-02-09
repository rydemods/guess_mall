<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

header("Cache-Control: no-cache, must-revalidate"); 
header("Content-Type: text/xml; charset=EUC-KR");

$imagepath=$Dir.DataDir."shopimages/multi/";

$productcode=$_REQUEST["productcode"];
$qftype=$_GET["qftype"];
$bttype=$_GET["bttype"];
$opts=$_GET["opts"];
$option1=$_GET["option1"];
$option2=$_GET["option2"];
$mode=$_GET["mode"];
$code=$_GET["code"];
$ordertype=$_GET["ordertype"];	//바로구매 구분 (바로구매시 => ordernow)
$quantity=(int)$_REQUEST["quantity"];	//구매수량
if($quantity==0) $quantity=1;

$errmsg="";
if(strlen($productcode)==18) {
	list($code_a,$code_b,$code_c,$code_d) = sscanf($productcode,'%3s%3s%3s%3s');

	$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_cdata=$row;
		if($row->group_code=="NO") {	//숨김 분류
			$errmsg="판매가 종료된 상품입니다.";
		} else if($row->group_code=="ALL" && ord($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			$errmsg="회원전용 상품입니다.\\n\\n로그인 후 이용하시기 바랍니다.";
		} else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			$errmsg="해당 분류의 접근 권한이 없습니다.";
		} else {
			if (empty($option1))  $option1=0;
			if (empty($option2))  $option2=0;
			if (empty($opts))  $opts="0";

			//Wishlist 담기
			if($mode=="wishlist") {
				if(ord($_ShopInfo->getMemid())==0) {	//비회원
					$errmsg="로그인을 하셔야 본 서비스를 이용하실 수 있습니다.";
				} else {
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
							if(ord(dickerview($row->etctype,0,1))>0) {
								$errmsg="해당 상품은 판매가 되지 않습니다.\\n";
							}
						}
						if(empty($option1) && ord($row->option1))  $option1=1;
						if(empty($option2) && ord($row->option2))  $option2=1;
					} else {
						$errmsg="해당 상품이 존재하지 않습니다.\\n";
					}
					pmysql_free_result($result);
					
					if(!$errmsg)
					{
						$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
						$result2=pmysql_query($sql,get_db_conn());
						$row2=pmysql_fetch_object($result2);
						$totcnt=$row2->totcnt;
						pmysql_free_result($result2);
						$maxcnt=100;
						if($totcnt>=$maxcnt) {
							$sql = "SELECT b.productcode ";
							$sql.= "FROM tblwishlist a, tblproduct b ";
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
							}
						}
						if($totcnt<$maxcnt) {
							$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
							$sql.= "AND productcode='{$productcode}' AND opt1_idx='{$option1}' ";
							$sql.= "AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
							$result2=pmysql_query($sql,get_db_conn());
							$row2=pmysql_fetch_object($result2);
							$cnt=$row2->cnt;
							pmysql_free_result($result2);
							if($cnt<=0) {
								$sql = "INSERT INTO tblwishlist (
								id			,
								productcode	,
								opt1_idx	,
								opt2_idx	,
								optidxs		,
								date		) VALUES (
								'".$_ShopInfo->getMemid()."', 
								'{$productcode}', 
								'{$option1}', 
								'{$option2}', 
								'{$opts}', 
								'".date("YmdHis")."')";
								pmysql_query($sql,get_db_conn());
							} else {
								$sql = "UPDATE tblwishlist SET date='".date("YmdHis")."' ";
								$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
								$sql.= "AND productcode='{$productcode}' ";
								$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
								pmysql_query($sql,get_db_conn());
							}

							echo "<script type=\"text/javascript\">
							if(confirm('WishList에 해당 상품을 등록하였습니다.\\n\\n  WishList 페이지로 이동 하겠습니까?')) { location.href='".$Dir.FrontDir."wishlist.php'; }
							".(ord($bttype)?"else { if(typeof(setFollowFunc)!='undefined') { setFollowFunc('Today','noselectmenu'); setFollowFunc('Wishlist','noselectmenu'); } }":"else { if(typeof(setFollowFunc)!='undefined') { setFollowFunc('Today','noselectmenu'); setFollowFunc('Wishlist','selectmenu'); } }")."
							</script>"; exit;
						} else {
							echo "<script type=\"text/javascript\">
							if(confirm('1. WishList에는 {$maxcnt}개 까지만 등록이 가능합니다.\\n2. 새 상품을 담기 위해서는 기존 WishList 상품을 삭제 후 등록할 수 있습니다.\\n\\n                    WishList 페이지로 이동 하겠습니까?'))
								location.href='".$Dir.FrontDir."wishlist.php';
							</script>"; exit;
						}
					}
				}
			}
			else if($mode=="basket_insert") {//장바구니 담기
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
					$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='{$productcode}' ";
					$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
					$result = pmysql_query($sql,get_db_conn());
					$row=pmysql_fetch_object($result);
					pmysql_free_result($result);
					if ($row) {
						echo "<script type=\"text/javascript\">
							if(confirm('이미 장바구니에 상품이 담겨있습니다. 수량은 장바구니 가셔서 조절해 주세요.\\n\\n                    장바구니 페이지로 이동 하겠습니까?'))
								location.href='".$Dir.FrontDir."basket.php';
						</script>"; exit;
					} else {
						if (strlen($productcode)==18) {
							$vdate = date("YmdHis");
							$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
							$result = pmysql_query($sql,get_db_conn());
							$row = pmysql_fetch_object($result);
							pmysql_free_result($result);
							if($row->cnt>=200) {
								echo "<script type=\"text/javascript\">
									if(confirm('1. 장바구니에는 총 200개 까지만 담을 수 있습니다.\\n2. 새 상품을 담기 위해서는 기존 장바구니 상품을 삭제 후 담을 수 있습니다.\\n\\n                    장바구니 페이지로 이동 하겠습니까?'))
										location.href='".$Dir.FrontDir."basket.php';
									</script>"; exit;
							} else {
								$sql = "INSERT INTO tblbasket ( 
								tempkey		,
								productcode	,
								opt1_idx	,
								opt2_idx	,
								optidxs		,
								quantity	,
								date,id		) VALUES (
								'".$_ShopInfo->getTempkey()."', 
								'{$productcode}', 
								'{$option1}', 
								'{$option2}', 
								'{$opts}', 
								'{$quantity}', 
								'{$vdate}','".$_ShopInfo->getMemid()."')";
								pmysql_query($sql,get_db_conn());
								
								

								$sql = "update tblbasket set ord_state=false where tempkey = '".$_ShopInfo->getTempkey()."' ";
								pmysql_query($sql,get_db_conn());

								if($ordertype=="ordernow") {	//바로구매
								
									$sql = "update tblbasket set tempkey='".$_ShopInfo->getTempkeySelectItem()."' where tempkey = '".$_ShopInfo->getTempkey()."'";
									pmysql_query($sql,get_db_conn());

									$sql = "update tblbasket set tempkey='".$_ShopInfo->getTempkey()."' where tempkey = '".$_ShopInfo->getTempkeySelectItem()."' and productcode='{$productcode}' and opt1_idx='{$option1}' and opt2_idx='{$option2}' and optidxs='{$opts}' and assemble_idx='0' and package_idx='0' and assemble_list='' ";
									pmysql_query($sql,get_db_conn());
																		
									//$sql = "update tblbasket set ord_state=true where tempkey = '".$_ShopInfo->getTempkey()."' and productcode='{$productcode}' and opt1_idx='{$option1}' and opt2_idx='{$option2}' and optidxs='{$opts}' and assemble_idx='0' and package_idx='0' and assemble_list='' ";
									
									
									//pmysql_query($sql,get_db_conn());
									
									echo "<script type=\"text/javascript\">location.href='".$Dir.FrontDir."login.php?chUrl=".urlencode($Dir.FrontDir."order.php")."';</script>";
									exit;
								} else {
									echo "
									<script type=\"text/javascript\">
										if(confirm('장바구니에 해당 상품을 등록하였습니다.\\n\\n장바구니 페이지로 이동 하겠습니까?')) { location.href='".$Dir.FrontDir."basket.php'; } 
										".(ord($bttype)?"else { if(typeof(setFollowFunc)!='undefined') { setFollowFunc('Today','noselectmenu'); setFollowFunc('Basket','noselectmenu'); } }":"else { if(typeof(setFollowFunc)!='undefined') { setFollowFunc('Today','noselectmenu');  setFollowFunc('Basket','selectmenu'); } }")."
									</script>"; exit;
								}
							}
						}
					}
				}
			}
			else
			{
				$sql = "SELECT a.* ";
				$sql.= "FROM tblproduct AS a ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
				$sql.= "WHERE a.productcode='{$productcode}' AND a.display='Y' ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$_pdata=$row;
				} else {
					$errmsg="해당 상품 정보가 존재하지 않습니다.";
				}
				pmysql_free_result($result);
			}
		}
	} else {
		$errmsg="해당 분류가 존재하지 않습니다.";
	}
} else {
	$errmsg="해당 상품이 존재하지 않습니다.";
}

if(ord($errmsg)) {
	echo "<script type=\"text/javascript\">alert('{$errmsg}'); ajaShop('create_openwin').setStyle('display','none');</script>"; exit;
}

if($_pdata->assembleuse=="Y" && $qftype != 1) {
	echo "<script type=\"text/javascript\">if(confirm('해당 상품은 상품상세페이지에서 구성상품을 선택 후에만 구매가 가능합니다.\\n\\n                     상품상세페이지로 이동 하겠습니까?')) {location.href='".$Dir.FrontDir."productdetail.php?productcode={$productcode}';} ajaShop('create_openwin').setStyle('display','none');</script>"; exit;
}

if((int)$_pdata->package_num>0 && $qftype != 1) {
	echo "<script type=\"text/javascript\">if(confirm('해당 상품은 패키지 선택 상품으로써 상품상세페이지에서 패키지 정보를 확인 해 주세요.\\n\\n                              상품상세페이지로 이동 하겠습니까?')) {location.href='".$Dir.FrontDir."productdetail.php?productcode={$productcode}';} ajaShop('create_openwin').setStyle('display','none');</script>"; exit;
}

$ref=$_REQUEST["ref"];
if (ord($ref)==0) {
	$ref=strtolower(str_replace("http://","",$_SERVER['HTTP_REFERER']));
	if(strpos($ref,"/") !== false) $ref=substr($ref,0,strpos($ref,"/"));
}

if(ord($ref) && ord($_ShopInfo->getRefurl())==0) {
	$sql2="SELECT * FROM tblpartner WHERE url LIKE '%{$ref}%' ";
	$result2 = pmysql_query($sql2,get_db_conn());
	if ($row2=pmysql_fetch_object($result2)) {
		pmysql_query("UPDATE tblpartner SET hit_cnt = hit_cnt+1 WHERE url = '{$row2->url}'",get_db_conn());
		$_ShopInfo->setRefurl($row2->id);
		$_ShopInfo->Save();
	}
	pmysql_free_result($result2);
}

if(strlen($productcode)==18) {
	if(ord($bttype)) {
		$viewproduct=$_COOKIE["ViewProduct"];
		if(ord($viewproduct)==0 || strpos($viewproduct,",{$productcode},")===FALSE) {
			if(ord($viewproduct)==0) {
				$viewproduct=",{$productcode},";
			} else {
				$viewproduct=",".$productcode.$viewproduct;
			}
			$viewproduct=substr($viewproduct,0,172);
			setcookie("ViewProduct",$viewproduct,0,"/");
		}
	} else {
		$viewproduct=$_COOKIE["ViewProduct"];
		if(ord($viewproduct)==0 || strpos($viewproduct,",{$productcode},")===FALSE) {
			if(ord($viewproduct)==0) {
				$viewproduct=",{$productcode},";
			} else {
				$viewproduct=",".$productcode.$viewproduct;
			}
		} else {
			$viewproduct=str_replace(",{$productcode}","",$viewproduct);
			$viewproduct=",".$productcode.$viewproduct;
		}
		$viewproduct=substr($viewproduct,0,172);
		setcookie("ViewProduct",$viewproduct,0,"/");
	}
}

//상품 상세정보 노출정보
if(ord($_data->exposed_list)==0) {
	$_data->exposed_list=",0,2,3,4,5,6,7,";
}
$arexcel = explode(",",trim($_data->exposed_list,','));
$prcnt = count($arexcel);
$arproduct=array(&$prproduction,&$prmadein,&$prconsumerprice,&$prsellprice,&$prreserve,&$praddcode,&$prquantity,&$proption,&$prproductname,&$prdollarprice);

if(preg_match("/^\[OPTG\d{4}\]$/",$_pdata->option1)){
	$optcode = substr($_pdata->option1,5,4);
	$_pdata->option1="";
	$_pdata->option_price="";
}

$miniq = 1; 
if (ord($_pdata->etctype)) {
	$etctemp = explode("",$_pdata->etctype);
	for ($i=0;$i<count($etctemp);$i++) {
		if (strpos($etctemp[$i],"MINIQ=")===0)			$miniq=substr($etctemp[$i],6);
	}
}

//입점업체 정보 관련
if($_pdata->vender>0) {
	$sql = "SELECT a.vender, a.id, a.brand_name, a.deli_info, b.prdt_cnt ";
	$sql.= "FROM tblvenderstore a, tblvenderstorecount b ";
	$sql.= "WHERE a.vender='{$_pdata->vender}' AND a.vender=b.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vdata=pmysql_fetch_object($result)) {
		$_pdata->vender=0;
	}
	pmysql_free_result($result);
}
?>


<form name="quickfun_form1" method="post" action="<?=$Dir.FrontDir?>basket.php">
<?php 
	$prproductname="";
	
	if(ord($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0) {
		$prsellprice=$dicker;
		$prdollarprice="";
		$priceindex=0;
	} else if(ord($optcode)==0 && ord($_pdata->option_price)) { 
		$option_price = $_pdata->option_price;
		$pricetok=explode(",",$option_price);
		$priceindex = count($pricetok);
		for($tmp=0;$tmp<=$priceindex;$tmp++) {
			$pricetok[$tmp]=number_format($pricetok[$tmp]);
		}
		$prsellprice.="<td align=\"right\" style=\"word-break:break-all;\"><IMG SRC=\"{$Dir}images/common/icon_line_point.gif\" border=\"0\" align=\"absmiddle\">판매가격</td>\n";
		$prsellprice.="<td></td>";
		$prsellprice.="<td align=\"left\"><img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</FONT></b></td>";
		$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
		$prdollarprice ="";
	} else if(ord($optcode)) {
		$prsellprice.="<td align=\"right\" style=\"word-break:break-all;\"><IMG SRC=\"{$Dir}images/common/icon_line_point.gif\" border=\"0\" align=\"absmiddle\">판매가격</td>\n";
		$prsellprice.="<td></td>";
		$prsellprice.="<td align=\"left\"><img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</FONT></b></td>";
		$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
		$prdollarprice ="";
	} else if(ord($_pdata->option_price)==0) {
		$prsellprice.="<td align=\"right\" style=\"word-break:break-all;\"><IMG SRC=\"{$Dir}images/common/icon_line_point.gif\" border=\"0\" align=\"absmiddle\">판매가격</td>\n";
		$prsellprice.="<td></td>";
		$prsellprice.="<td align=\"left\"><img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</FONT></b></td>";
		$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
		$prdollarprice ="";
		$priceindex=0;
	}
	
	if($qftype == 1)
		$prquantity ="<input type=hidden name=\"quantity\" value=\"1\">\n";
	else
	{
		$prquantity.="<td align=\"right\" style=\"word-break:break-all;\"><IMG SRC=\"{$Dir}images/common/icon_line_point.gif\" border=\"0\" align=\"absmiddle\">구매수량</td>\n";
		$prquantity.="<td></td>";
		$prquantity.="<td align=\"left\">\n";
		$prquantity.="<table cellpadding=\"1\" cellspacing=\"0\" width=\"60\">\n";
		$prquantity.="<tr>\n";
		$prquantity.="	<td width=\"33\"><input type=text name=\"quantity\" value=\"".($miniq>1?$miniq:"1")."\" size=\"4\" style=\"font-size:11px;BORDER:#DFDFDF 1px solid;HEIGHT:18px;BACKGROUND-COLOR:#F7F7F7;padding-top:2pt;padding-bottom:1pt;\" onkeyup=\"strnumkeyup(this)\"></td>\n";
		$prquantity.="	<td width=\"33\">\n";
		$prquantity.="	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$prquantity.="	<tr>\n";
		$prquantity.="		<td width=\"5\" height=\"7\"><a href=\"javascript:quickfun_change_quantity('up')\"><img src=\"{$Dir}images/common/btn_plus.gif\" border=\"0\"></a></td>\n";
		$prquantity.="	</tr>\n";
		$prquantity.="	<tr>\n";
		$prquantity.="		<td width=\"5\" height=\"7\"><a href=\"javascript:quickfun_change_quantity('dn')\"><img src=\"{$Dir}images/common/btn_minus.gif\" border=\"0\"></a></td>\n";
		$prquantity.="	</tr>\n";
		$prquantity.="	</table>\n";
		$prquantity.="	</td>\n";
		$prquantity.="	<td width=\"33\">EA</td>\n";
		$prquantity.="</tr>\n";
		$prquantity.="</table>\n";
		$prquantity.="</td>\n";
	}

	$proption1="";
	if(ord($_pdata->option1)) {
		$temp = $_pdata->option1;
		$tok = explode(",",$temp);
		$count=count($tok);
		$proption1.="<tr height=\"22\">\n";
		$proption1.="	<td align=\"right\" style=\"word-break:break-all;\"><IMG SRC=\"{$Dir}images/common/icon_line_point.gif\" border=\"0\" align=\"absmiddle\">{$tok[0]}</td>\n";
		$proption1.="	<td></td>\n";
		$proption1.="	<td align=\"left\">";
		if ($priceindex!=0) {
			$proption1.="<select name=\"option1\" size=\"1\" style=\"width:98%;font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" ";
			$proption1.="onchange=\"quickfun_change_price(1,document.quickfun_form1.option1.selectedIndex-1,";
			if(ord($_pdata->option2)) $proption1.="document.quickfun_form1.option2.selectedIndex-1";
			else $proption1.="''";
			$proption1.=")\">\n";
		} else {
			$proption1.="<select name=\"option1\" size=\"1\" style=\"width:98%;font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" ";
			$proption1.="onchange=\"quickfun_change_price(0,document.quickfun_form1.option1.selectedIndex-1,";
			if(ord($_pdata->option2)) $proption1.="document.quickfun_form1.option2.selectedIndex-1";
			else $proption1.="''";
			$proption1.=")\">\n";
		}

		$optioncnt = explode(",",ltrim($_pdata->option_quantity,','));
		$proption1.="<option value=\"\" style=\"color:#ffffff;\">옵션을 선택하세요\n";
		$proption1.="<option value=\"\" style=\"color:#ffffff;\">-----------------\n";
		for($i=1;$i<$count;$i++) {
			if(ord($tok[$i])) $proption1.="<option value=\"$i\" style=\"color:#ffffff;\">$tok[$i]\n";
			if(ord($_pdata->option2)==0 && $optioncnt[$i-1]=="0") $proption1.=" (품절)";
		}
		$proption1.="</select>";
	} else {
		//$proption1.="<input type=hidden name=option1>";
	}

	$proption2="";
	if(ord($_pdata->option2)) {
		$temp = $_pdata->option2;
		$tok = explode(",",$temp);
		$count2=count($tok);
		
		$proption2.="<tr height=\"22\">\n";
		$proption2.="	<td align=\"right\" style=\"word-break:break-all;\"><IMG SRC=\"{$Dir}images/common/icon_line_point.gif\" border=\"0\" align=\"absmiddle\">{$tok[0]}</td>\n";
		$proption2.="	<td></td>\n";
		$proption2.="	<td align=\"left\">";
		$proption2.="<select name=\"option2\" size=\"1\" style=\"width:98%;font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" ";
		$proption2.="onchange=\"quickfun_change_price(0,";
		if(ord($_pdata->option1)) $proption2.="document.quickfun_form1.option1.selectedIndex-1";
		else $proption2.="''";
		$proption2.=",document.quickfun_form1.option2.selectedIndex-1)\">\n";
		$proption2.="<option value=\"\" style=\"color:#ffffff;\">옵션을 선택하세요\n";
		$proption2.="<option value=\"\" style=\"color:#ffffff;\">-----------------\n";
		for($i=1;$i<$count2;$i++) if(ord($tok[$i])) $proption2.="<option value=\"$i\" style=\"color:#ffffff;\">$tok[$i]\n";
		$proption2.="</select>";
		$proption2.="	</td>\n";
		$proption2.="</tr>\n";
		
	} else {
		if(ord($_pdata->option1)) {
		$proption1.="	</td>\n";
		$proption1.="</tr>\n";
		}
	}

	if(ord($optcode)) {
		$sql = "SELECT * FROM tblproductoption WHERE option_code='{$optcode}' ";
		$result = pmysql_query($sql,get_db_conn());
		if($row = pmysql_fetch_object($result)) {
			$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
			$opti=0;
			$option_choice = $row->option_choice;
			$exoption_choice = explode("",$option_choice);
			while(ord($optionadd[$opti])) {
				$proption3.="[OPT]";
				$proption3.="<select name=\"mulopt\" size=1 style=\"width:98%;font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" onchange=\"quickfun_chopprice('$opti')\"";
				$proption3.=">";
				$opval = str_replace('"','',explode("",$optionadd[$opti]));
				$proption3.="<option value=\"0,0\" style=\"color:#ffffff;\">--- ".$opval[0].($exoption_choice[$opti]==1?"(필수)":"(선택)")." ---";
				$opcnt=count($opval);
				for($j=1;$j<$opcnt;$j++) {
					$exop = str_replace('"','',explode(",",$opval[$j]));
					$proption3.="<option value=\"{$opval[$j]}\" style=\"color:#ffffff;\">";
					if($exop[1]>0) $proption3.=$exop[0]."(+{$exop[1]}원)";
					else if($exop[1]==0) $proption3.=$exop[0];
					else $proption3.=$exop[0]."({$exop[1]}원)";
				}
				$proption3.="</select><input type=hidden name=\"opttype\" value=\"0\"><input type=hidden name=\"optselect\" value=\"{$exoption_choice[$opti]}\">[OPTEND]";
				$opti++;
			}
			$proption3.="<input type=hidden name=\"mulopt\"><input type=hidden name=\"opttype\"><input type=hidden name=\"optselect\">";
		}
		pmysql_free_result($result);
	}
?>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=productcode value="<?=$productcode?>">
<input type=hidden name=bttype value="<?=$bttype?>">
<input type=hidden name=ordertype>
<input type=hidden name=opts>
<tr>
	<td>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td id=layerbox-top style="cursor:move; float:left;">
		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
		<col width=10></col>
		<col width=></col>
		<col width=10></col>
		<tr>
			<td style="width:10px;height:25px;background: url(/<?=RootPath?>images/common/layeropenbg_top_left.gif) no-repeat 0 0;"></td>
			<td style="height:25px;background: url(/<?=RootPath?>images/common/layeropenbg_top_center.gif)">
			<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<col width=></col>
			<col width=50></col>
			<tr>
				<td style="padding:5,0,0,0; font-size:11px;color:#FEEACB;"><B><?php if($qftype == 1) echo "WishList 담기"; else if($qftype == 2) echo "장바구니 담기"; else echo "바로구매";?></B></td>
				<td align=right style="padding-top:2;"><a style="cursor:hand" onclick="PrdtQuickCls.openwinClose()"><FONT style="font-size:11px;color:#FEEACB;">close</FONT> <img src="/<?=RootPath?>images/common/layeropen_btn_close.gif" border=0 align=absmiddle></a></td>
			</tr>
			</table>
			</td>
			<td style="width:10px;height:25px;background: url(/<?=RootPath?>images/common/layeropenbg_top_right.gif) no-repeat 0 0;"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td id=layerbox-content>
		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
		<col width=10></col>
		<col width=></col>
		<col width=10></col>
		<tr>
			<td style="width:10px;background: url(/<?=RootPath?>images/common/layeropenbg_middle_left.gif) repeat-y;"></td>
			<td style="background: url(/<?=RootPath?>images/common/layeropenbg_middle_center.gif);">
			<div style="margin: 15px 0 0 3px;">
			<!-- 내용 시작 -->
			<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<tr>
				<td style="word-break:break-all;">
				<?php if(ord($_pdata->addcode)) echo "<font style=\"color:#FF7900;font-size:12px\"><B>[{$_pdata->addcode}]</B></font> ";
				echo "		<FONT style=\"color:#000000;font-size:12px\"><B>".viewproductname($_pdata->productname,$_pdata->etctype,$_pdata->selfcode)."</B></FONT>";?>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			<tr><td height=1 bgcolor=#fafafa></td></tr>
			<tr><td height=1 bgcolor=#efefef></td></tr>
			<tr><td height=1 bgcolor=#fafafa></td></tr>
			<tr><td height=5></td></tr>
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td valign=top>
					<!-- 상품 상세내용 출력 시작 -->
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width="100"></col>
						<col width="10"></col>
						<col width=></col>
<?php 
						for($i=0;$i<$prcnt;$i++) {
							if($arexcel[$i][0]=="O") {	//공백
								echo "<tr><td colspan=\"4\" height=\"5\" bgcolor=\"#FFFFFF\"></td></tr>\n";
							} else if ($arexcel[$i]=="7") {	//옵션
								if(ord($proption1) || ord($proption2) || ord($proption3)) {
									
									if(ord($proption1)) {
										$proption.=$proption1;
									}
									if(ord($proption2)) {
										$proption.=$proption2;
									}
									if(ord($proption3)) {
										$add_proption ="<tr height=\"22\">\n";
										$add_proption.="	<td align=\"right\" style=\"word-break:break-all;\"><IMG SRC=\"{$Dir}images/common/icon_line_point.gif\" border=\"0\" align=\"absmiddle\">상품옵션</td>\n";
										$add_proption.="	<td></td>\n";
										$add_proption.="	<td align=\"left\">\n";
										$add_proption.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
										$add_proption2 ="	</table>\n";
										$add_proption2.="	</td>\n";
										$add_proption2.="</tr>\n";

										$pattern=array("[OPT]","[OPTEND]");
										$replace=array("<tr><td>","</td></tr>");
										$proption.=$add_proption.str_replace($pattern,$replace,$proption3).$add_proption2;
									}

									echo $arproduct[$arexcel[$i]];
								} else {
									$proption ="<input type=hidden name=\"option1\">\n";
									$proption.="<input type=hidden name=\"option2\">\n";
								}
							} else if(ord($arproduct[$arexcel[$i]])) {	//
								echo "<tr height=\"22\">{$arproduct[$arexcel[$i]]}</tr>\n";
								if($arexcel[$i]=="9") $dollarok="Y";
							}
						}
						
?>
						</table>
						</td>
					</tr>
					</table>
					<!-- 상품 상세내용 출력 끝   -->
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			<tr><td height=1 bgcolor=#fafafa></td></tr>
			<tr><td height=1 bgcolor=#efefef></td></tr>
			<tr><td height=1 bgcolor=#fafafa></td></tr>
			<tr><td height=15></td></tr>
			<tr>
				
<?php 
	if(ord($dicker)==0) {
		if($qftype == 1)
		{
			if (ord($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
				echo "<td align=center><a href=\"javascript:quickfun_CheckForm('wishlist','{$opti}')\"><IMG SRC=\"{$Dir}images/common/btn_wishlist.gif\" border=0 align=absmiddle></a></td>\n";
			} else {
				echo "<td align=center><a href=\"javascript:quickfun_check_login();\"><IMG SRC=\"{$Dir}images/common/btn_wishlist.gif\" border=0 align=absmiddle></a></td>\n";
			}
		}
		else
		{
			if(ord($_pdata->quantity) && $_pdata->quantity<=0)
			{
				if($qftype == 3)
					echo "<td align=center><a href=\"javascript:alert('품절 상태이므로 바로구매할 수 없습니다.');\" onMouseOver=\"window.status='바로구매';return true;\"><IMG SRC=\"{$Dir}images/common/btn_directbuy.gif\" border=0 align=middle></a></td>\n";
				else if($qftype == 2)
					echo "<td align=center><a href=\"javascript:alert('품절 상태이므로 장바구니 담을 수 없습니다.');\" onMouseOver=\"window.status='장바구니담기';return true;\"><IMG SRC=\"{$Dir}images/common/btn_basket.gif\" border=\"0\" align=middle></a></td>\n";
			}
			else
			{
				if($qftype == 3)
					echo "<td align=center><a href=\"javascript:quickfun_CheckForm('ordernow','{$opti}')\" onMouseOver=\"window.status='바로구매';return true;\"><IMG SRC=\"{$Dir}images/common/btn_directbuy.gif\" border=0 align=middle></a></td>\n";
				else if($qftype == 2)
					echo "<td align=center><a href=\"javascript:quickfun_CheckForm('','{$opti}')\" onMouseOver=\"window.status='장바구니담기';return true;\"><IMG SRC=\"{$Dir}images/common/btn_basket.gif\" border=\"0\" align=middle onMouseDown=\"eval('try{ _trk_clickTrace( \'SCI\', \'".$_pdata->productname."\' ); }catch(_e){ }');\"></a></td>\n";
			}
		}
	}

?>
			</tr>
			</table>
			<!-- 내용   끝 -->
			</div>
			</td>
			<td style="width:10px;background: url(/<?=RootPath?>images/common/layeropenbg_middle_right.gif) repeat-y;"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td id=layerbox-bottom>
		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
		<col width=10></col>
		<col width=></col>
		<col width=10></col>
		<tr>
			<td style="width:10px;height:18px;background: url(/<?=RootPath?>images/common/layeropenbg_bottom_left.gif) no-repeat 0 0;"></td>
			<td style="height:18px;background: url(/<?=RootPath?>images/common/layeropenbg_bottom_center.gif)" align=right></td>
			<td style="width:10px;height:18px;background: url(/<?=RootPath?>images/common/layeropenbg_bottom_right.gif) no-repeat 0 0;"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</form>
<?php 
if(ord($optcode)==0) {
	$maxnum=($count2-1)*10;
	if($optioncnt>0) {
		for($i=0;$i<$maxnum;$i++) { 
			if ($i!=0) $quickfun_num .= ",";
			if(ord($optioncnt[$i])==0) $quickfun_num .= "100000";
			else $quickfun_num .= $optioncnt[$i]; 
		} 
	}

	if($priceindex>0) $quickfun_price = number_format($_pdata->sellprice)."|".number_format($_pdata->sellprice)."|";
	for($i=0; $i<$priceindex; $i++) { 
		if($i!=0)
			$quickfun_price .= "|";
		$quickfun_price .= $pricetok[$i]; 
	}
}
?>
<form name="quickfun_setform">
<input type=hidden name=quickfun_miniq value="<?=($miniq>1?$miniq:1)?>">
<input type=hidden name=quickfun_num value="<?=$quickfun_num?>">
<input type=hidden name=quickfun_dicker value="<?=(int)@strlen($dicker)?>">
<input type=hidden name=quickfun_price value="<?=$quickfun_price?>">
<input type=hidden name=quickfun_priceindex value="<?=$priceindex?>">
<input type=hidden name=quickfun_login value="<?=$Dir.FrontDir?>login.php?chUrl=">
<input type=hidden name=quickfun_login2 value="<?=urlencode("?".$_SERVER['QUERY_STRING'])?>">
</form>

<script type="text/javascript">
Drag.init(ajaShop("layerbox-top"),ajaShop("create_openwin"));
</script>
