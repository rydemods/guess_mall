<?
class MEMBER{
	function MEMBER(){
	}
	

	function setSearch(){
		if(is_array($param)){foreach($param as $f=>$v){
			$this->$f = $v;
		}}
	}
	#### member ####
	function getMemberList(){
		$field[] = "*";
		$table[] = "tblmember";	
		$query = "select * from tblmember ";
	}

	function getMemberInfo(){
		
		global $_ShopInfo;

		$id = $_ShopInfo->memid;
		$temkey = $_ShopInfo->tempkey;
		$group_code = $_ShopInfo->memgroup;

		if(!$temkey) $temkey = $_ShopInfo->getTempkey();
		if(!$temkeySelect) $temkeySelect = $_ShopInfo->getTempkeySelectItem();	
		if($_ShopInfo->memid){
			//회원정보
			$where=" where id='".$id."' ";
			$query = "select id,name,reserve from tblmember ".$where." ";
			$result = pmysql_query($query);

			while($row = pmysql_fetch_array($result)){
				$data=$row;
			}
			
			//보유쿠폰수량
			list($coupon_cnt) =pmysql_fetch("SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$id."' AND used='N' AND (date_end>=cast(current_date as varchar) OR date_end='')");
			$data[coupon_cnt]=$coupon_cnt;
			//장바구니수량
			if($temkey && $temkeySelect){
				$cart_qry="select COUNT(*) as cnt from tblbasket where tempkey in ('".$temkey."', '".$temkeySelect."')";
			}else if($temkey && !$temkeySelect){
				$cart_qry="select COUNT(*) as cnt from tblbasket where tempkey in ('".$temkey."')";
			}else{
				$cart_qry="select COUNT(*) as cnt from tblbasket where id='".$id."'";
			}
			list($cart_cnt) =pmysql_fetch($cart_qry);
			$data[cart_cnt]=$cart_cnt;
			//위시리스트 수량
			list($wish_cnt) =pmysql_fetch("select COUNT(*) as cnt from tblwishlist where id='duo135'");
			$data[wish_cnt]=$wish_cnt;
			//그룹정보
			$query = "select * from tblmembergroup where group_code='".$group_code."' order by group_level asc ";
			$result = pmysql_query($query, get_db_conn());
			while($row = pmysql_fetch_array($result)){
				$data[group] = $row;
			}

		}else{
			$data[coupon_cnt]=0;
			$data[cart_cnt]=0;
			$data[wish_cnt]=0;
		}
		return $data;
	}


	function getMemberGroupList(){
		$query = "select * from tblmembergroup order by group_level asc ";
		$result = pmysql_query($query, get_db_conn());
		while($row = pmysql_fetch_array($result)){
			$data[] = $row;
		}
		return $data;
	}

	function getMemberGroupInfo(){
		$query = "select * from tblmembergroup order by group_level asc ";
		$result = pmysql_query($query, get_db_conn());
		while($row = pmysql_fetch_array($result)){
			$data[$row[group_code]] = $row;
		}
		return $data;
	}
	#### member group ####
}
?>