<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");

$JSON_USE=true;
$AJAX_USE=true;

include_once("shopdata.inc.php");

// JSON 리턴 객체
$return=array(
        "total" => 0,
        "list" => array(),
        );

$page=$_REQUEST["page"];
if (!$page || !is_numeric($page) || $page < 1) {
    $page = 1;
}
$limit=20;
$offset=($page-1) * $limit;

switch (strtoupper($_REQUEST["action_mode"])) {
	// 카테고리 상품 목록 및 상품검색 목록
	case "GET_LIST":
		$code=$_REQUEST["code"];
		$search=data_convert(trim($_REQUEST["search"]));
		$sort = $_REQUEST["sort"];
		if($code=="000000") $code="";

		if(mb_strlen($search, "EUC-KR")==1) {
			response(false, "검색어는 2글자 이상 가능합니다.");
		}
		if(strlen($code)==0 && strlen($search)==0) {
			response(false, "잘못된 요청입니다.");
		}

        if(strlen($code)>0 && strlen($search)==0) {
			$codeA=substr($code,0,3);
			$codeB=substr($code,3,3);
			$codeC=substr($code,6,3);
			$codeD=substr($code,9,3);
			if(strlen($codeA)!=3) $codeA="000";
			if(strlen($codeB)!=3) $codeB="000";
			if(strlen($codeC)!=3) $codeC="000";
			if(strlen($codeD)!=3) $codeD="000";
			$code=$codeA.$codeB.$codeC.$codeD;

			$likecode=$codeA;
			if($codeB!="000") $likecode.=$codeB;
			if($codeC!="000") $likecode.=$codeC;
			if($codeD!="000") $likecode.=$codeD;

			$_cdata="";
			$sql = "SELECT * FROM tblproductcode WHERE code_a='".$codeA."' AND code_b='".$codeB."' ";
			$sql.= "AND code_c='".$codeC."' AND code_d='".$codeD."' ";
			$result=pmysql_query($sql,get_mdb_conn());
			if($row=pmysql_fetch_object($result)) {
				//접근가능권한그룹 체크
				if($row->group_code=="NO") {
					response(false, "해당 카테고리가 존재하지 않습니다.");
					exit;
				}
				if(strlen($_MShopInfo->getMemid())==0) {
					if(strlen($row->group_code)>0) {
						response(false, "로그인 후 이용이 가능합니다.");
						exit;
					}
				} else {
					if($row->group_code!="ALL" && strlen($row->group_code)>0 && $row->group_code!=$_MShopInfo->getMemgroup()) {
						response(false, "해당 카테고리 접근권한이 없습니다.");
						exit;
					}
				}
				$_cdata=$row;
				$code_name=strip_tags($row->code_name);
			} else {
				response(false, "해당 카테고리가 존재하지 않습니다.");
				exit;
			}
			pmysql_free_result($result);

			$sql = "SELECT code_a, code_b, code_c, code_d FROM tblproductcode ";
			if(strlen($_MShopInfo->getMemid())==0) {
				$sql.= "WHERE group_code!='' ";
			} else {
				$sql.= "WHERE group_code!='".$_MShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
			}
			$result=pmysql_query($sql,get_mdb_conn());
			$not_qry="";
			while($row=pmysql_fetch_object($result)) {
				$tmpcode=$row->code_a;
				if($row->code_b!="000") $tmpcode.=$row->code_b;
				if($row->code_c!="000") $tmpcode.=$row->code_c;
				if($row->code_d!="000") $tmpcode.=$row->code_d;
				$not_qry.= "AND a.productcode NOT LIKE '".$tmpcode."%' ";
			}
			pmysql_free_result($result);

			$qry = "WHERE 1=1 ";
			if(preg_match("/T/",$_cdata->type)) {	//가상분류
				$sql = "SELECT productcode FROM tblproducttheme WHERE code LIKE '".$likecode."%' ";
				if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
					$sql.= "ORDER BY date DESC ";
				}
				$result=pmysql_query($sql,get_mdb_conn());
				$t_prcode="";
				while($row=pmysql_fetch_object($result)) {
					$t_prcode.=$row->productcode.",";
					$i++;
				}
				pmysql_free_result($result);
				$t_prcode=substr($t_prcode,0,-1);
				$t_prcode=ereg_replace(',','\',\'',$t_prcode);
				$qry.= "AND a.productcode IN ('".$t_prcode."') ";

				$add_query="&code=".$code;
			} else {	//일반분류
				$qry.= "AND a.productcode LIKE '".$likecode."%' ";
			}
			$qry.="AND a.display='Y' ";

			$sql = "SELECT COUNT(*) as t_count FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= $qry." ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
			if(strlen($not_qry)>0) {
				$sql.= $not_qry." ";
			}
			$result=pmysql_query($sql,get_mdb_conn());
			$row=pmysql_fetch_object($result);
			$t_count = (int)$row->t_count;
			pmysql_free_result($result);

			$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
			if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
			$sql.= "a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= $qry." ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
			if(strlen($not_qry)>0) {
				$sql.= $not_qry." ";
			}
			if($sort=="new") $sql.= "ORDER BY a.date DESC ";
			else if($sort=="low_price") $sql.= "ORDER BY a.sellprice ASC ";
			else if($sort=="high_price") $sql.= "ORDER BY a.sellprice DESC ";
			else if($sort=="product_name") $sql.= "ORDER BY a.productname ASC ";
			else {
				if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
					if(preg_match("/T/",$_cdata->type) && strlen($t_prcode)>0) {
						$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
					} else {
						$sql.= "ORDER BY a.date DESC ";
					}
				} else {
					$sql.= "ORDER BY a.date DESC ";
				}
			}
			$sql.= "LIMIT {$limit} OFFSET {$offset}";

        } else if (mb_strlen($search, "EUC-KR") > 1) {
			$qry="WHERE 1=1 ";
			$skeys = explode(" ",$search);
			for($j=0;$j<count($skeys);$j++) {
				if(strlen($skeys[$j])>0) {
					$qry.= "AND (a.productname LIKE '%".$skeys[$j]."%' OR a.keyword LIKE '%".$skeys[$j]."%') ";
				}
			}
			$qry.= "AND a.display!='N' ";
			if(strlen($code)>0) $qry.="AND a.productcode LIKE '".$code."%' ";

			$sql = "SELECT COUNT(*) as t_count ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= $qry;
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
			$result=pmysql_query($sql,get_mdb_conn());
			$row=pmysql_fetch_object($result);
			$t_count = (int)$row->t_count;
			pmysql_free_result($result);

			$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
			if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
			$sql.= "a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= $qry." ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
			if(strlen($not_qry)>0) {
				$sql.= $not_qry." ";
			}
			if($sort=="new") $sql.= "ORDER BY a.date DESC ";
			else if($sort=="low_price") $sql.= "ORDER BY a.sellprice ASC ";
			else if($sort=="high_price") $sql.= "ORDER BY a.sellprice DESC ";
			else if($sort=="product_name") $sql.= "ORDER BY a.productname ASC ";
			else $sql.= "ORDER BY a.date DESC ";

			$sql.= "LIMIT {$limit} OFFSET {$offset}";
        }

        $return["total"]=$t_count;
        if ($return["total"]<=0) {
            response(true, "", $return);
        }

		$result=pmysql_query($sql,get_mdb_conn());
        while ($row = pmysql_fetch_object($result)) {
			if (!$row->tinyimage) {
				$row->tinyimage = $Dir."images/acimage.gif";
			} else {
				$row->tinyimage = $imagepath.$row->tinyimage;
			}
            $row->productname = rawurlencode(ajax_convert(strip_tags($row->productname)));
			if($row->consumerprice<=0 || $row->sellprice>=$row->consumerprice) {
				$row->consumerprice = 0;
			}
            $row->sellprice = number_format($row->sellprice);
			$row->consumerprice = number_format($row->consumerprice);

			$row->reserve=number_format(getReserveConvert($row->reserve,$row->reservetype,$row->sellprice,"Y"));

			$r_cnt=0;
			$r_marks=0;
			$r_totscore=0;
			if($_data->review_type=="Y" || $_data->review_type=="A") {
				$sql = "SELECT COUNT(*) as r_cnt, SUM(marks) as r_marks FROM tblproductreview ";
				$sql.= "WHERE productcode='".$row->productcode."' ";
				if($_data->review_type=="A") $sql.= "AND display='Y' ";
				$sql.= "GROUP BY productcode ";
				$result2=pmysql_query($sql,get_mdb_conn());
				$row2=pmysql_fetch_object($result2);
				pmysql_free_result($result2);
				
				$r_cnt=(int)$row2->r_cnt;
				$r_marks=(int)$row2->r_marks;
				$r_totscore=0;

				if($r_cnt>0) {
					$r_totscore=ceil(($r_marks*20)/$r_cnt);
				}
			}
			$row->r_cnt=$r_cnt;
			$row->r_marks=$r_marks;
			$row->r_totscore=$r_totscore;

            $return["list"][]=$row;
        }
		pmysql_free_result($result);

        response(true, "", $return);
        break;
}

response(false, "잘못된 요청입니다.");
?>