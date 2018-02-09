<?
	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=euc-kr");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$mode = $_POST['mode'];
	$productcode = $_POST['productcode'];
	$coupon_code = $_POST['coupon_code'];
	if($mode=="coupon" && strlen($coupon_code)==8 && strlen($productcode)==18) {	//쿠폰 발급
		if(strlen($_ShopInfo->getMemid())==0) {	//비회원
			$msg = '로그인 후 쿠폰 다운로드가 가능합니다.';
			$msgType = 0;
		} else {
			
			$goods_cate_sql = "SELECT * FROM tblproductlink WHERE c_productcode = '".$productcode."'";
			$goods_cate_result = pmysql_query($goods_cate_sql,get_db_conn());
			$categorycode = array();
			while($goods_cate_row=pmysql_fetch_object($goods_cate_result)) {
				list($cate_a, $cate_b, $cate_c, $cate_d) = sscanf($goods_cate_row->c_category,'%3s%3s%3s%3s');
				$categorycode[] = $cate_a;
				$categorycode[] = $cate_a.$cate_b;
				$categorycode[] = $cate_a.$cate_b.$cate_c;
				$categorycode[] = $cate_a.$cate_b.$cate_c.$cate_d;
			}
			if(count($categorycode) > 0){											
				$addCategoryQuery = "('".implode("', '", $categorycode)."')";
			}else{
				$addCategoryQuery = "('')";
			}

			$sql = "SELECT a.* FROM tblcouponinfo a ";
			$sql .= "LEFT JOIN tblcouponproduct c on a.coupon_code=c.coupon_code ";
			$sql .= "LEFT JOIN tblcouponcategory d on a.coupon_code=d.coupon_code ";
			if($_pdata->vender>0) {
				$sql .= "WHERE (a.vender='0' OR a.vender='{$_pdata->vender}') ";
			} else {
				$sql .= "WHERE a.vender='0' ";
			}
			$sql .= "AND a.coupon_code='{$coupon_code}' ";
			$sql .= "AND a.display='Y' AND a.issue_type='Y' AND a.detail_auto='Y' ";
			$sql .= "AND (a.date_end>'".date("YmdH")."' OR a.date_end='') ";
			$sql .= "AND ((a.use_con_type2='Y' AND a.productcode = 'ALL') OR ((a.use_con_type2='Y' AND a.productcode != 'ALL') AND (c.productcode = '".$productcode."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y')))) ";
			$sql .= "AND mod(a.sale_type::int , 2) = '0' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$issue_member_no = $row->issue_member_no;
				if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
					$msg = '모든 쿠폰이 발급되었습니다.';
					$msgType =1;
				} else {
					$date=date("YmdHis");
					if($row->date_start>0) {
						$date_start=$row->date_start;
						$date_end=$row->date_end;
					} else {
						$date_start = substr($date,0,10);
						$date_end = date("Ymd23",strtotime("+".abs($row->date_start)." day"));
					}
					$sql = "INSERT INTO tblcouponissue 
									(coupon_code, id, date_start, date_end, date) 
								VALUES 
									('{$coupon_code}', '".$_ShopInfo->getMemid()."', '{$date_start}', '{$date_end}', '{$date}')";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_errno()) {
						$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
						$sql.= "WHERE coupon_code = '{$coupon_code}'";
						pmysql_query($sql,get_db_conn());

						$msg = '해당 쿠폰 발급이 완료되었습니다.';
						$msgType = 1;
					} else {
						//동일인 재발급이 가능하다면,,,,
						if($row->repeat_id=="Y") {
							$sqlIssue = "SELECT id, coupon_code, issue_member_no, used FROM tblcouponissue WHERE coupon_code='{$coupon_code}' AND id='".$_ShopInfo->getMemid()."'";
							$resultIssue=pmysql_query($sqlIssue,get_db_conn());
							$rowIssue=pmysql_fetch_object($resultIssue);
							if($row->issue_member_no < $rowIssue->issue_member_no+1){
								$msg = '모든 쿠폰이 발급되었습니다.';
								$msgType = 1;
							}else if($row->issue_member_no > $rowIssue->issue_member_no && $rowIssue->used == 'N'){
								$msg = '사용하지 않은 동일한 쿠폰이 있습니다.';
								$msgType = 1;
							}else{
								$sql = "UPDATE tblcouponissue SET ";
								if($row->date_start<=0) {
									$sql.= "date_start	= '{$date_start}', ";
									$sql.= "date_end	= '{$date_end}', ";
								}
								$sql.= "used		= 'N', ";
								$sql.= "issue_member_no		= issue_member_no+1 ";
								$sql.= "WHERE coupon_code='{$coupon_code}' ";
								$sql.= "AND id='".$_ShopInfo->getMemid()."' ";
								pmysql_query($sql,get_db_conn());

								$msg = '해당 쿠폰 발급이 완료되었습니다.!';
								$msgType = 1;
							}
						} else {
							$msg = '이미 쿠폰을 발급받으셨습니다.';
							$msgType = 1;
						}
					}
				}
			} else {
				$msg = '해당 쿠폰은 사용 가능한 쿠폰이 아닙니다.';
				$msgType = 1;
			}
			pmysql_free_result($result);
		}
	}
	$msgTmp = mb_convert_encoding($msg,"UTF-8","EUC-KR");
	$onMsg = array("msgType"=>$msgType,"msg"=>$msgTmp);
	echo json_encode($onMsg);
	
?>