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

switch (strtoupper($_REQUEST["action_mode"])) {

	case "CART_ADD":
		//장바구니 인증키 확인
		if(ord($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
			$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
			$_ShopInfo->setTempkeySelectItem($_data->ETCTYPE["BASKETTIME"]);
		}

		//옵션 넣어야함
		$productcode=$_POST['productcode'];
		$quantity=$_POST['quantity'];
		$optidxs = $_POST['optidxs'];

			$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode='{$productcode}' ";

			$result = pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);

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
						'0',
						'0',
						'{$optidxs}',
						'{$quantity}',
						'0',
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
						'0',
						'0',
						'{$optidxs}',
						'{$quantity}',
						'0',
						'0',
						'0',
						'{$vdate}','".$_ShopInfo->getMemid()."')";
						pmysql_query($sql,get_db_conn());
					}
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

		$opts="0";
		$option1=0;
		$option2=0;

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
	
	case "CHECK_EA":
		$productcode=$_POST['productcode'];
		$optidxs=$_POST["optidxs"];
		$opt_explode=explode(",",$optidxs);
		
		$qry="select option_quantity from tblproduct where productcode='".$productcode."'";
		$result=pmysql_query($qry);
		$data=pmysql_fetch_object($result);
		
		$option_quantity=explode(",",$data->option_quantity);
		
		if(count($opt_explode)>2){
			$opt_ea=$option_quantity[(($opt_explode[1]-1)*10+$opt_explode[0])];
		}else{
			$opt_ea=$option_quantity[$opt_explode[0]];
		}
		if($opt_ea=="0"){
			response(true, '해당 상품의 옵션은 품절되었습니다. 다른 상품을 선택하세요');	
		}else{
			response(false, $opt_ea);	
		}
		
		break;
}

response(false, "잘못된 요청입니다.");
?>