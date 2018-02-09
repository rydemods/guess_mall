<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");

$JSON_USE=true;
$AJAX_USE=true;

include_once("shopdata.inc.php");

function get_count($sql) {
	$result=pmysql_query($sql,get_mdb_conn());
	if (!$result) {
		response(false, "데이터베이스 오류가 발생하였습니다.");
	}
	if (!pmysql_num_rows($result)) {
		return 0;
	}
	return pmysql_result($result, 0);
}

function get_review_total_count($pridx, $productcode, $review_type) {
	$qry = "WHERE productcode='".$productcode."' ";
	if($review_type=="A") $qry.= "AND display='Y' ";
	$sql = "SELECT COUNT(*) as t_count FROM tblproductreview ";
	$sql.= $qry;

	return get_count($sql);
}

function get_review_list($productcode, $offset, $limit) {
	global $_data;

	$qry = "WHERE productcode='".$productcode."' ";
	if($_data->review_type=="A") $qry.= "AND display='Y' ";

	$sql = "SELECT * FROM tblproductreview ";
	$sql.= $qry;
	$sql.= "ORDER BY num DESC ";
	$sql.= "LIMIT {$limit} OFFSET {$offset} ";
	$res = pmysql_query($sql,get_mdb_conn());
	$review_list_array = array();
	while ($row_review = @pmysql_fetch_object($res)) {
		$review_list_array[] = $row_review;
		if (strlen($row_review->id) > 0) {
			$id_list[$row_review->id] = $row_review->id;
		}
	}
	pmysql_free_result($res);

	$i=0;
	$review_list=array();
	foreach ($review_list_array as $row_review) {
		$review=array();

		$review["mark_icon_html"]="";
		for($ii=0;$ii<$row_review->marks;$ii++) {
			$review["mark_icon_html"].="<span style=\"font-size:10px; color:#abc511;\">★</span>";
		}
		for($ii=$row_review->marks;$ii<5;$ii++) {
			$review["mark_icon_html"].="<span style=\"font-size:10px; color:#dedede;\">★</span>";
		}
		$reviewcontent=explode("=",$row_review->content);

		$i++;

		if(strlen($row_review->id) > 10 && $row_review->name == substr($row_review->id,0,10)) {
			$review["name"]=$row_review->id;
		} else {
			$review["name"]=$row_review->name;
		}
		$review["name"]=$review["name"];

		$review["date"]=substr($row_review->date,0,4)."/".substr($row_review->date,4,2)."/".substr($row_review->date,6,2);
		$review["subject"]=trim(strip_tags(str_replace("\n", " ", $reviewcontent[0])));
		$review["subject"]=titleCut(40,$review["subject"]);
		$review["content"]=nl2br($reviewcontent[0]);
		$review["reply"]=nl2br($reviewcontent[1]);
		$review["name"]=rawurlencode(ajax_convert($review["name"]));
		$review["subject"]=rawurlencode(ajax_convert($review["subject"]));
		$review["content"]=rawurlencode(ajax_convert($review["content"]));
		$review["reply"]=rawurlencode(ajax_convert($review["reply"]));
		$review["mark_icon_html"]=rawurlencode(ajax_convert($review["mark_icon_html"]));
		$review_list[] = $review;
	}

	return array(true, "", $review_list);
}
//action_mode:'cart_add', productcode: '016001000000000001', quantity: 0,priceArr:8000||10000||8000    ,optionArr:1_2||3_1||1_5 ,quantityArr:3||2||5
//$_REQUEST["action_mode"] = "CART_ADD";
switch (strtoupper($_REQUEST["action_mode"])) {

	case "CART_ADD":
		//장바구니 인증키 확인
		if(ord($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
			$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
			$_ShopInfo->setTempkeySelectItem($_data->ETCTYPE["BASKETTIME"]);
		}

		//옵션 넣어야함
		//packagenum priceArr optionArr 
		$productcode=$_POST['productcode'];
		$quantity=$_POST['quantity'];
		$package=$_POST['packagenum']?$_POST['packagenum']:"0";
		//$optidxs = $_POST['optidxs'];
		//$opt_explode=explode(",",$optidxs);
		$optionArr = $_POST['optionArr'];
		$quantityArr = $_POST['quantityArr'];
		$ex_optionArr = explode("||",$optionArr);
		$ex_quantityArr = explode("||",$quantityArr);
		$opt1="0";
		$opt2="0";
		$where="";
		//if($opt_explode[0])$opt1=$opt_explode[0];
		//if($opt_explode[1])$opt2=$opt_explode[1];
		// 다중 옵션 기능 추가로 멀티 insert 반복문으로 구현 start
		for ($s=0; $s<sizeof($ex_optionArr); $s++) {
			$ex_ex_optionArr = explode("_",$ex_optionArr[$s]);
			$opt1 = $ex_ex_optionArr[0];
			$opt2 = $ex_ex_optionArr[1];
			$quantity = $ex_quantityArr[$s];
			if(strlen($_MShopInfo->getMemid())==0) $where="tempkey='".$_ShopInfo->getTempkey()."'";
			else $where="id='".$_MShopInfo->getMemid()."'";
			
			$sql = "SELECT * FROM tblbasket WHERE {$where} ";
			$sql.= "AND productcode='{$productcode}' ";
			$sql.= "AND opt1_idx='{$opt1}' AND opt2_idx='{$opt2}' AND optidxs='0' ";
			$sql.= "AND assemble_idx = '0' ";
			$sql.= "AND package_idx = '{$package}' ";
			$sql.= "AND tempkey = '{$_ShopInfo->getTempkey()}' ";
		//	echo $sql;
			$result = pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			
			//패키지상품 재고검사
			
			if($_POST['packagenum']!=''){
				$packagesql = "SELECT b.package_list,b.package_title,b.package_price ";
				$packagesql.= "FROM tblproduct AS a, tblproductpackage AS b ";
				$packagesql.= "WHERE a.productcode='{$productcode}' ";
				$packagesql.= "AND a.package_num=b.num ";
				$packagesql.= "AND a.display = 'Y' ";
				$packageresult = pmysql_query($packagesql,get_db_conn());
				$packagerow=@pmysql_fetch_object($packageresult);
							
				$package_list_exp = explode("", $packagerow->package_list);
				
				$packagesq2 = "SELECT productcode,quantity,productname,sellprice FROM tblproduct ";
				$packagesq2.= "WHERE pridx IN ('".str_replace(",","','",ltrim($package_list_exp[$package],','))."') ";
				$packagesq2.= "AND display = 'Y' ";

				$packageresult2 = pmysql_query($packagesq2,get_db_conn());
				$sellprice_package_listtmp=0;
				while($packagerow2=@pmysql_fetch_object($packageresult2)) {
					$productcode_package_listtmp[] = $packagerow2->productcode;
					$quantity_package_listtmp[] = $packagerow2->quantity;
					$productname_package_listtmp[] = $packagerow2->productname;
				}
							
				if(count($productcode_package_listtmp)) {
					$errmsg = '';
						for($i=0; $i<count($productcode_package_listtmp); $i++) {
						if(ord($productcode_package_listtmp[$i])) {
							if(ord($quantity_package_listtmp[$i])) {
								if($quantity_package_listtmp[$i]>0) {
									if($quantity_package_listtmp[$i]<$quantity){
										$errmsg="해당 상품의 패키지 [".str_replace("'","",$productname_package_listtmp[$i])."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$quantity_package_listtmp[$i]} 개 입니다.");
									}
								} else {
									$errmsg="해당 상품의 패키지 [".str_replace("'","",$productname_package_listtmp[$i])."] 다른 고객의 주문으로 품절되었습니다.";
								}
							}
						}
					}
				}	
			}
			
			if($errmsg!=''){
				response(false,$errmsg,'package');
			}else if($row){
				response(false);
			}else{
				if (strlen($productcode)==18) {
					$vdate = date("YmdHis");
					$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
					$result = pmysql_query($sql,get_db_conn());
					$row = pmysql_fetch_object($result);
					pmysql_free_result($result);
					if($row->cnt>=200) {
						echo "<script>alert('장바구니에는 총 200개까지만 담을수 있습니다.');</script>";
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
							'{$opt1}',
							'{$opt2}',
							'0',
							'{$quantity}',
							'{$package}',
							'0',
							'0',
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
							'{$opt1}',
							'{$opt2}',
							'0',
							'{$quantity}',
							'{$package}',
							'0',
							'0',
							'{$vdate}','".$_ShopInfo->getMemid()."')";
							pmysql_query($sql,get_db_conn());
						}
					}
				}
				
			}
			
		}
		response(true);
		break;
	case "ORDER_ADD":
		//장바구니 인증키 확인
		if(ord($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
			$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
			$_ShopInfo->setTempkeySelectItem($_data->ETCTYPE["BASKETTIME"]);
		}
		
		list($countOldItem) = pmysql_fetch("SELECT count(basketidx) FROM tblbasket WHERE tempkey = '".$_ShopInfo->getTempkeySelectItem()."'");
		if($countOldItem > 0){
			$selectItemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE tempkey='".$_ShopInfo->getTempkeySelectItem()."'";
			//$selectItemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkeySelectItem()."' WHERE tempkey='".$_ShopInfo->getTempkey()."'";
			pmysql_query($selectItemQuery);
		}

		if($_MShopInfo->getMemid()){
			
			list($countOldItem2) = pmysql_fetch("SELECT count(basketidx) FROM tblbasket WHERE id = '".$_MShopInfo->getMemid()."'");
			if($countOldItem2 > 0){
				$selectItemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE id = '".$_MShopInfo->getMemid()."'";
				//$selectItemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkeySelectItem()."' WHERE id = '".$_MShopInfo->getMemid()."'";
				pmysql_query($selectItemQuery);
			}
		}
		

		//옵션 넣어야함
		$productcode=$_POST['productcode'];
		$quantity=$_POST['quantity'];
		$package=$_POST['packagenum']?$_POST['packagenum']:"0";
		$optionArr = $_POST['optionArr'];
		$quantityArr = $_POST['quantityArr'];
		$ex_optionArr = explode("||",$optionArr);
		$ex_quantityArr = explode("||",$quantityArr);
		$opt1="0";
		$opt2="0";
		$where="";
		
		/*
		$productcode=$_POST['productcode'];
		$quantity=$_POST['quantity'];
		$package=$_POST['packagenum']?$_POST['packagenum']:"0";
		$optidxs = $_POST['optidxs'];
		$opt_explode=explode(",",$optidxs);
		$opt1="0";
		$opt2="0";
		$where="";
		if($opt_explode[0])$opt1=$opt_explode[0];
		if($opt_explode[1])$opt2=$opt_explode[1];
		*/
		for ($s=0; $s<sizeof($ex_optionArr); $s++) {
			$ex_ex_optionArr = explode("_",$ex_optionArr[$s]);
			$opt1 = $ex_ex_optionArr[0];
			$opt2 = $ex_ex_optionArr[1];
			$quantity = $ex_quantityArr[$s];
			if(strlen($_MShopInfo->getMemid())==0) $where="tempkey='".$_ShopInfo->getTempkey()."'";
			else $where="id='".$_MShopInfo->getMemid()."'";
			
			$sql = "SELECT * FROM tblbasket WHERE {$where} ";
			$sql.= "AND productcode='{$productcode}' ";
			$sql.= "AND opt1_idx='{$opt1}' AND opt2_idx='{$opt2}' AND optidxs='0' ";
			$sql.= "AND assemble_idx = '0' ";
			$sql.= "AND package_idx = '{$package}' ";
			$sql.= "AND tempkey = '{$_ShopInfo->getTempkey()}' ";
		//	echo $sql;
			$result = pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			
			//패키지상품 재고검사
			if($_POST['packagenum']!=''){
				$packagesql = "SELECT b.package_list,b.package_title,b.package_price ";
				$packagesql.= "FROM tblproduct AS a, tblproductpackage AS b ";
				$packagesql.= "WHERE a.productcode='{$productcode}' ";
				$packagesql.= "AND a.package_num=b.num ";
				$packagesql.= "AND a.display = 'Y' ";
				$packageresult = pmysql_query($packagesql,get_db_conn());
				$packagerow=@pmysql_fetch_object($packageresult);
							
				$package_list_exp = explode("", $packagerow->package_list);
				
				$packagesq2 = "SELECT productcode,quantity,productname,sellprice FROM tblproduct ";
				$packagesq2.= "WHERE pridx IN ('".str_replace(",","','",ltrim($package_list_exp[$package],','))."') ";
				$packagesq2.= "AND display = 'Y' ";

				$packageresult2 = pmysql_query($packagesq2,get_db_conn());
				$sellprice_package_listtmp=0;
				while($packagerow2=@pmysql_fetch_object($packageresult2)) {
					$productcode_package_listtmp[] = $packagerow2->productcode;
					$quantity_package_listtmp[] = $packagerow2->quantity;
					$productname_package_listtmp[] = $packagerow2->productname;
				}
							
				if(count($productcode_package_listtmp)) {
					$errmsg = '';
						for($i=0; $i<count($productcode_package_listtmp); $i++) {
						if(ord($productcode_package_listtmp[$i])) {
							if(ord($quantity_package_listtmp[$i])) {
								if($quantity_package_listtmp[$i]>0) {
									if($quantity_package_listtmp[$i]<$quantity){
										$errmsg="해당 상품의 패키지 [".str_replace("'","",$productname_package_listtmp[$i])."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 {$quantity_package_listtmp[$i]} 개 입니다.");
									}
								} else {
									$errmsg="해당 상품의 패키지 [".str_replace("'","",$productname_package_listtmp[$i])."] 다른 고객의 주문으로 품절되었습니다.";
								}
							}
						}
					}
				}	
			}
			
			if($errmsg!=''){
				response(false,$errmsg,'package');
			}else{
				if (strlen($productcode)==18) {
					$vdate = date("YmdHis");
					$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
					$result = pmysql_query($sql,get_db_conn());
					$row = pmysql_fetch_object($result);
					pmysql_free_result($result);
					if($row->cnt>=200) {
						echo "<script>alert('장바구니에는 총 200개까지만 담을수 있습니다.');</script>";
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
							'{$opt1}',
							'{$opt2}',
							'0',
							'{$quantity}',
							'{$package}',
							'0',
							'0',
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
							'{$opt1}',
							'{$opt2}',
							'0',
							'{$quantity}',
							'{$package}',
							'0',
							'0',
							'{$vdate}','".$_ShopInfo->getMemid()."')";
							pmysql_query($sql,get_db_conn());
						}
					}
				}
				
				$sql2 = "update tblbasket set ord_state=false where tempkey = '".$_ShopInfo->getTempkey()."' ";
				pmysql_query($sql2,get_db_conn());
						
				
					
				$sql2 = "update tblbasket set tempkey='".$_ShopInfo->getTempkeySelectItem()."' where tempkey = '".$_ShopInfo->getTempkey()."'";
				pmysql_query($sql2,get_db_conn());

				$sql2 = "update tblbasket set tempkey='".$_ShopInfo->getTempkey()."', quantity='".$quantity."' where tempkey = '".$_ShopInfo->getTempkeySelectItem()."' and productcode='{$productcode}' and opt1_idx='{$opt1}' and opt2_idx='{$opt2}' and optidxs='0' and assemble_idx='0' and package_idx='{$package}' and assemble_list='0' ";
				pmysql_query($sql2,get_db_conn());
				
				//response(false,$_ShopInfo->getTempkey().' '.$_ShopInfo->getTempkeySelectItem(),'package');
			}
			
		}
		response(true);
		break;

    // 관심상품 등록
    case "WISHLIST_ADD":
        $pridx=$_POST["pridx"];
        if(strlen($_MShopInfo->getMemid())==0) {
            response(false, "로그인이 필요한 서비스입니다.", 'LOGIN');
        }
        if (!$pridx || !is_numeric($pridx)) {
            response(false, "상품정보가 존재하지 않습니다.");
        }
		$option1=0;
		$option2=0;

		$opts="0";
		$optidxs = $_POST['optidxs'];
		$opt_explode=explode(",",$optidxs);
		$where="";
		if($opt_explode[0]) $option1=$opt_explode[0];
		if($opt_explode[1]) $option2=$opt_explode[1];

		$sql = "SELECT productcode,display,option1,option2,etctype,group_check FROM tblproduct ";
		$sql.= "WHERE pridx='".$pridx."' ";
		$result=pmysql_query($sql,get_mdb_conn());
		if(!$result) {
			response(false, "데이터베이스 오류가 발생하였습니다.");
		}
		if($row=@pmysql_fetch_object($result)) {
			$productcode=$row->productcode;

			if($row->display!="Y") {
				response(false, "해당 상품은 판매가 되지 않는 상품입니다.");
			}
			if($row->group_check!="N") {
				if(strlen($_MShopInfo->getMemid())>0) {
					$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
					$sqlgc.= "WHERE productcode='".$productcode."' ";
					$sqlgc.= "AND group_code='".$_MShopInfo->getMemgroup()."' ";
					$resultgc=pmysql_query($sqlgc,get_mdb_conn());
					if($rowgc=@pmysql_fetch_object($resultgc)) {
						if($rowgc->groupcheck_count<1) {
							response(false, "해당 상품은 지정 등급 전용 상품입니다.");
						}
						@pmysql_free_result($resultgc);
					} else {
						response(false, "해당 상품은 지정 등급 전용 상품입니다.");
					}
				} else {
					response(false, "해당 상품은 회원 전용 상품입니다.");
				}
			}
			if(strlen($errmsg)==0) {
				if(strlen(dickerview($row->etctype,0,1))>0) {
					response(false, "해당 상품은 판매가 되지 않습니다.");
				}
			}
			if(empty($option1) && strlen($row->option1)>0)  $option1=1;
			if(empty($option2) && strlen($row->option2)>0)  $option2=1;
		} else {
			response(false, "해당 상품이 존재하지 않습니다.");
		}
		pmysql_free_result($result);
		
		/*
		$sql = "REPLACE INTO tblwishlist SET ";
		$sql.= "id			= '".$_MShopInfo->getMemid()."', ";
		$sql.= "productcode	= '".$productcode."', ";
		$sql.= "opt1_idx	= '".$option1."', ";
		$sql.= "opt2_idx	= '".$option2."', ";
		$sql.= "optidxs		= '".$opts."', ";
		$sql.= "date		= '".date("YmdHis")."' ";
		*/
		
		$sql = "insert INTO tblwishlist (id, productcode, opt1_idx, opt2_idx, optidxs, date)values";
		$sql.= "('".$_MShopInfo->getMemid()."', '".$productcode."', '".$option1."', '".$option2."', '".$opts."', '".date("YmdHis")."')";

		$res=pmysql_query($sql,get_mdb_conn());

        if (!$res) {
			
			$sql = "update tblwishlist set date='".date("YmdHis")."' 
			where id='".$_MShopInfo->getMemid()."' 
			and productcode='".$productcode."' 
			and opt1_idx='".$option1."'
			and opt2_idx='".$option2."'
			and optidxs='".$opts."'
			";

			$res=pmysql_query($sql,get_mdb_conn());

			if (!$res) {
	            response(false, "데이터베이스 오류가 발생하였습니다.");
			}
        }

        response(true);
        break;

    // 관심상품 삭제
    case "WISHLIST_DEL":
        $wishidxs=$_POST["wishidx"];
        if (!$wishidxs || !is_array($wishidxs)) {
            response(false, "선택된 상품이 없습니다.");
        }
        $cnt=0;
		$sql;
        foreach ($wishidxs AS $wishidx) {
            if (is_numeric($wishidx) === true) {
				$sql = "DELETE FROM tblwishlist WHERE id='".$_MShopInfo->getMemid()."' AND wish_idx='".$wishidx."' ";
				$res=pmysql_query($sql,get_mdb_conn());
                if ($res !== false ) {
                    $cnt++;
                }
            }
        }
        if ($cnt > 0) {
            response(true, $cnt);
        } else {
            response(false, "관심상품을 삭제하지 못하였습니다.");
        }
        break;

    // 상품 상세정보
    case "GET_DETAIL":
        $pridx=$_POST["pridx"];
		$sql = "SELECT content FROM tblproduct WHERE pridx='".$pridx."' ";
        $res = pmysql_query($sql,get_mdb_conn());
        if (!$res) {
            response(false, "데이터베이스 오류가 발생하였습니다.");
        }
        if(pmysql_num_rows($res)) {
            $content = pmysql_result($res, 0);
            $content = replace_content($_data->filter, $content);

			$content = strip_tags($content, "<br><p><img>");
            $content = rawurlencode(ajax_convert($content));
        }

        response(true, "", $content);
        break;
}

response(false, "잘못된 요청입니다.");
?>