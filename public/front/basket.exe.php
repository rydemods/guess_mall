<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."lib/schedule.class.php");
	
	$mode=trim($_POST[mode]);


		if(is_array($_POST[idx])) $idx = $_POST[idx];
		else $idx[] = $_POST[idx]; 
		
		//print_r($_POST);

		foreach($idx as $i){

			$opts=$_POST["opts"][$i];	//옵션그룹 선택된 항목 (예:1,1,2,)
			$option1=(int)$_POST["option1"][$i];	//옵션1
			$option2=(int)$_POST["option2"][$i];	//옵션2
			$quantity=(int)$_REQUEST["quantity"][$i];	//구매수량
			if($quantity==0) $quantity=1;
			$productcode=$_REQUEST["productcode"][$i];

			$orgquantity=$_POST["orgquantity"][$i];
			$orgoption1=$_POST["orgoption1"][$i];
			$orgoption2=$_POST["orgoption2"][$i];

			$assemble_type=$_POST["assemble_type"][$i];
			$assemble_list=@str_replace("|","",$_POST["assemble_list"][$i]);
			$assembleuse=$_POST["assembleuse"][$i];
			$assemble_idx=(int)$_POST["assemble_idx"][$i];
			$assemble_idx_max=(int)$_POST["assemble_idx_max"][$i];

			$package_idx=(int)$_POST["package_idx"][$i];

			if($assemble_idx==0) {
				if($assembleuse=="Y") {
					$assemble_idx="-9999";
				}
			} else {
				$assembleuse="Y";
			}

			### 주문 상품의 basketidx 집계 하단에 서 삭제 처리 후 order.php로 이동 ####
			$basketidx[] = $_POST[basketidx][$i];

			$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode='{$productcode}' ";
			$sql.= "AND opt1_idx='{$option1}' AND opt2_idx='{$option2}' AND optidxs='{$opts}' ";
			$sql.= "AND assemble_idx = '{$assemble_idx}' ";
			$sql.= "AND package_idx = '{$package_idx}' ";

			$result = pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);


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
					$sql.= "opt2_idx		= '{$option2}' ";
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
			} elseif ($row) {
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
					}
				}
				$_POST[returnUrl]="/front/basket.php";
			}
		}//End foreach
		
		if($mode=="ord"){
			$sql = "update tblbasket set ord_state=false where tempkey = '".$_ShopInfo->getTempkey()."' ";
			pmysql_query($sql,get_db_conn());

			$sql = "update tblbasket set ord_state=true WHERE tempkey='".$_ShopInfo->getTempkey()."' AND basketidx in ('".implode("','", $basketidx)."') ";
			pmysql_query($sql,get_db_conn());
			$_POST[returnUrl] = "order.php";
		}
		if($_POST[frame]){ 
			alert_go('',$_POST[returnUrl],$_POST[frame]);
		}else{
			alert_go('',$_POST[returnUrl]);
		}
?>