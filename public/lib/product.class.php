<?
include_once dirname(__FILE__)."/page.class.php";

class PRODUCT extends PAGE{

	function PRODUCT(){

	}

	function setSearch($param){
		if(is_array($param)){foreach($param as $f=>$v){
			$this->$f = $v;
		}}
	}

	function getProductList(){
		$field[] = "*";
		if($this->productname) $where[] = "productname like '%".$this->productname."%' ";
		
		$table[]="tblproduct";
		$this->field = $field;
		$this->table = $table;
		$this->where = $where;
		
		$this->setQuery();

		$result = pmysql_query($this->query, get_db_conn());
		while($row = pmysql_fetch_array($result)){

			$row[option][option1_data] = explode(",",$row[option1]);
			$row[option][option2_data] = explode(",",$row[option2]);
			$row[option][option_price_data] = explode(",",$row[option_price]);
			$row[timg_src]="/data/shopimages/product/".$row[minimage];
			$data[] = $row;
		}
		return $data;
	}

	function getOptionForm($data,$idx){
		if(count($data[option1_data])>1){
			$str = "<select name='option[".$idx."]' class='option'>";
			for($i=1; $i<count($data[option1_data]); $i++){

				if(count($data[option2_data])>1){
					for($j=1; $j< count($data[option2_data]); $j++){
						$str.="<option value='".$data[option1_data][$i].",".$data[option2_data][$j]."'>".$data[option1_data][$i].",".$data[option2_data][$j]." ( ".number_format($data[option_price_data][($i-1)])."￦ )</option>";
					}
				}else{
					$str.="<option value='".$data[option1_data][$i]."'>".$data[option1_data][$i]." ( ".number_format($data[option_price_data][($i-1)])."￦ )</option>";
				}
			}
			$str.="</select>";
		}else{
			$str = "<input type='text' name='option[".$idx."]' value=''>";
		}
		return $str;
	}
	function isProductReview($productcode, $memid, $ordercode=''){
		$cnt = 0;
		$query = "select count(*) from tblproductreview where productcode = '".$productcode."' and id='".$memid."' ";
		if($ordercode){
		$query .="and ordercode='".$ordercode."'";
		}
		$result = pmysql_query($query, get_db_conn());
		list($cnt) = pmysql_fetch_array($result);
		return $cnt;
	}

	function getMemberDcRate(){
		global $_ShopInfo, $paymethod, $receipt_yn;

		$group_code=$_ShopInfo->memgroup;
		if(ord($group_code) && $group_code!=NULL) {
			$sql = "SELECT * FROM tblmembergroup WHERE group_code='{$group_code}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)){
//				$data[type] = substr($row->group_code,0,2);
				$data[price] = $row->group_addmoney;
				$data[reserve] = $row->group_addreserve;
			}
			pmysql_free_result($result);
		}
		return $data;
	}
/*
	function getProductDcRate_old($productcode){
		global $_ShopInfo, $paymethod, $receipt_yn;

		if($_ShopInfo->memid){
			$group_code=$_ShopInfo->memgroup;
			$query = "select * from tblproduct where productcode='".$productcode."'";
			$row = pmysql_fetch_object(pmysql_query($query,get_db_conn()));
			$grpdc_ex=explode(";",$row->membergrpdc);
			foreach($grpdc_ex as $v){
				$grpdc_data=explode("-",$v);
				$grpdc_arr[$grpdc_data[0]]=$grpdc_data[1];
			}
			$dc_per=0;
			$dc_per=$grpdc_arr['lv'.$_ShopInfo->memlevel];
			
			$data = $this->getMemberDcRate();

			if($dc_per){
				$data[price]=$dc_per."%";
			}

			$etc = strstr($data[price],"%");
			if(strlen($paymethod)>0 && $_ShopInfo->wsmember=="Y" && ( $paymethod!="B" || ( $paymethod=="B" && $receipt_yn!="N") ) && $etc){
				$data[price] = ($data[price]-7).$etc; ### 도매회원 현금결제가 아니거나 영수증 신청여부에 따라 회원할인율 -7% 처리함
			}
		}

		return $data;
	}
*/

	function getProductDcRate($productcode){
		global $_ShopInfo, $paymethod, $receipt_yn;
		if($_ShopInfo->memid){
			$group_code=$_ShopInfo->memgroup;
			$query = "select * from tblproduct where productcode='".$productcode."'";
			$row = pmysql_fetch_object(pmysql_query($query,get_db_conn()));
			$grpdc_ex=explode(";",$row->membergrpdc);
			foreach($grpdc_ex as $v){
				$grpdc_data=explode("-",$v);
				$grpdc_arr[$grpdc_data[0]]=$grpdc_data[1];
			}
			$dc_per=0;
			$dc_per=$grpdc_arr['lv'.$_ShopInfo->memlevel];
			
			$data = $this->getMemberDcRate();
			
			//설정이 없으면 0을 넣어줌.
			if($dc_per<0 || $dc_per=='') $dc_per=0;
			
			//개별옵션을 선택한 상품
			if($row->dctype=='1'){
				$data[price]=$dc_per."%";
			}

			$etc = strstr($data[price],"%");
			if(strlen($paymethod)>0 && $_ShopInfo->wsmember=="Y" && ( $paymethod!="B" || ( $paymethod=="B" && $receipt_yn!="N") ) && $etc){

				$wsdc=$data[price]-7;
				if($wsdc<0) $wsdc=0;

				$data[price] = ($wsdc).$etc; ### 도매회원 현금결제가 아니거나 영수증 신청여부에 따라 회원할인율 -7% 처리함
			}
		}

		return $data;
	}
	/*	테이블 사용안함 2015 11 04 유동혁
	function getProductGroupPrice($productcode){
		
		global $_ShopInfo, $paymethod, $receipt_yn;
		$resultData;
		$selectQueryBody = "
			SELECT 
				a.productcode, 
				a.group_code, 
				a.consumerprice, 
				a.consumer_cost, 
				a.consumer_vat, 
				a.consumer_reserve, 
				a.consumer_reservetype, 
				a.sellprice, 
				a.sell_cost, 
				a.sell_vat, 
				a.sell_reserve, 
				a.sell_reservetype
			FROM tblmembergroup_price a
			WHERE a.productcode = '{$productcode}'
		";
		$selectQueryAnd = "";
		if ($_ShopInfo->memgroup != "") { 
			$selectQueryAnd = " AND a.group_code = '{$_ShopInfo->memgroup}' ";
		} else { // default
			return null;
		}
		$result = pmysql_query($selectQueryBody.$selectQueryAnd,get_db_conn());
		while($row=pmysql_fetch_array($result)){
			$data = $row;
		}		
		return $data;
	}
	*/
	/*
		상품의 배송비 정보를 리턴 합니다.
	*/
	function getDeliState($_pdata){
		global $_ShopInfo, $_data;
		$resultData = Array();

		$itemState = "0";
		$msg = "";
		if (($_pdata->deli=="Y" || $_pdata->deli=="N") && $_pdata->deli_price>0) {
			if($_pdata->deli=="Y") {
				$msg = "개별배송비 상품당 :".number_format($_pdata->deli_price)."원";
				$itemState = "1";
				
			} else {
				$deli_productprice += $_pdata->deli_price;
				$msg = "개별배송비 총 :".number_format($_pdata->deli_price)."원";
				$itemState = "2";
			}
		} else if($_pdata->deli=="F" || $_pdata->deli=="G") {
			if($_pdata->deli=="F") {
				$msg = "무료";
				$itemState = "3";
			} else {
				$msg = "착불";
				$itemState = "4";
			}
		} else {
			$msg = '';
			
			if ($_data->deli_type == "T" && $_data->deli_basefeetype == "Y" && $_data->deli_basefee < 1) {//무료
				if ($_data->deli_after == "Y") {
					$msg .= " 착불";
					$itemState = "5";
				} else {
					$msg .= " 무료";
					$itemState = "6";
				}
			} else if ($_data->deli_type == "T" && $_data->deli_basefee > 0) { // 유료
				if ($_data->deli_basefeetype == "N") {
					$msg .= "";
					$itemState = "7";
				} else { // 단일 유료 배송료  
					if( $_pdata->sellprice >= $_data->deli_miniprice  ){
						$msg .= "무료";
					} else {
						$msg .= "";
					}
					$itemState = "8";
				}
			} else {
				$msg .= " 에러";
				$itemState = "0";
			}
		}
		$res = array("itemState"=>$itemState, "msg" => $msg );
		$resultData = array_merge($res, $resultData); 
		return $resultData;
	}
}

?>