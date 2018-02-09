<?php
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$textDir = $_SERVER[DOCUMENT_ROOT].'/batch/member_change/';
/*
$f = fopen($textDir."Member_Change_".date("Ymd").".txt","a+");
fwrite($f,"########################################## START ".date("Y-m-d H:i:s")."\r\n");
*/

## 멤버 등급 정보 갱신
$change_query = "	Update	tblmember 
			Set		group_code = '0001'	";
//pmysql_query( $change_query, get_db_conn() );

$from_dt = date("Ym",strtotime("-6 month"))."01000000";
$to_dt = date("Ym",strtotime("-1 month")).date("t",mktime(0,0,1,date("m",strtotime("-1 month")),1,date("Y",strtotime("-1 month"))))."235959";
//$to_dt = "20160331235959";

# 그룹 조회쿼리 기본등급은 제외하고 돌린다. (전체 기본등급으로 변경된이후에 각 구매금액별로 등급을 업데이트한다)
$sql = "SELECT	group_code, group_name, group_level, group_orderprice_s, group_orderprice_e, group_ordercnt_s, group_ordercnt_e
		FROM 	tblmembergroup where group_code!='0001'
		ORDER BY group_code ";
$smt = pmysql_query( $sql, get_db_conn() );

//fwrite($f,"sql = ".$sql."\r\n");
//fwrite($f,"##########################################"."\r\n\r\n");

while($row=pmysql_fetch_object($smt)) {
//	fwrite($f,"GROUP_CODE = ".$row[group_code]."\r\n");
	//fwrite($f,"GROUP_LEVEL = ".$row[group_level]."\r\n");
	if($row->group_orderprice_e == 0)
	{
		$having_sql = "HAVING  sum(coalesce(iprice,0)) >= $row->group_ordercnt_s ";
	}
	else
	{
		//$having_sql = "HAVING  sum(coalesce(iprice,0)) >= $row->group_orderprice_s AND sum(coalesce(iprice,0)) <= $row->group_orderprice_e and sum(coalesce(OC,0)) >= $row->group_ordercnt_s and sum(coalesce(OC,0)) <= $row->group_ordercnt_e ";
		$having_sql = "HAVING  sum(coalesce(iprice,0)) >= $row->group_orderprice_s AND sum(coalesce(iprice,0)) <= $row->group_orderprice_e ";
	}
	
	# 구매 금액 조회 쿼리
	$query="select id, name, sum(coalesce(iprice,0) - coalesce(coupon,0) - coalesce(pointprice,0)) M, count(id) OC  from 	
			(
				select min(m.id) as id,
				min(m.name) as name,
				min(m.email) as email,
				sum(op.price * op.quantity) as iprice,
				sum(coalesce(op.coupon_price, 0)) as coupon,
				sum(coalesce(op.use_point, 0)) as pointprice
				from 
					tblorderinfo o 
					join tblorderproduct op on o.ordercode=op.ordercode
					join tblmember m on o.id=m.id	
				where oi_step1='4' and oi_step2='0'
				and coalesce(o.bank_date,substr(o.ordercode,0,15)) >= '$from_dt' 
				and	coalesce(o.bank_date,substr(o.ordercode,0,15)) <= '$to_dt'
				
				group by o.ordercode
				union all
				select min(m.id) as id,
				min(m.name) as name,
				min(m.email) as email,
				sum(op.price * op.quantity)*-1 as iprice,
				sum(coalesce(op.coupon_price, 0))*-1 as coupon,
				sum(coalesce(op.use_point, 0))*-1 as pointprice
				from 
					tblorderinfo o 
					join tblorderproduct op on o.ordercode=op.ordercode
					join tblmember m on o.id=m.id	
					join tblorder_cancel oc on o.ordercode=oc.ordercode and op.oc_no=oc.oc_no
				where oi_step1='4' and oi_step2='0'
				and coalesce(coalesce(oc.rfindt,oc.cfindt),oc.regdt) >= '$from_dt' 
				and	coalesce(coalesce(oc.rfindt,oc.cfindt),oc.regdt) <= '$to_dt'
				
				and op.op_step = 44
				group by o.ordercode

			) V group by name, id
			".$having_sql." 
			order by M desc
			";
	//--and o.order_conf='1'
	//$result = pmysql_query( $query, get_db_conn() );
	exdebug($query);
	
	//			fwrite($f,"sql = ".$sql."\r\n");

	while($data=pmysql_fetch_object($result)) {

		#그룹 변경 히스토리 저장
		$h_query="insert into tblmember_grp_history (group_code, apply_yyyymm, regdt, id) values ('".$row->group_code."','".date('Ym')."', now(), '".$data->id."')";
		//pmysql_query( $h_query, get_db_conn() );
		echo $h_query;
		echo "<br>";
		//fwrite($f,"sql = ".$h_query."\r\n");

		## 멤버 등급 정보 갱신
		$u_query="update tblmember set group_code = '".$row->group_code."' where id='".$data->id."'";
		//pmysql_query( $u_query, get_db_conn() );
		//fwrite($f,"sql = ".$u_query."\r\n");
		//fwrite($f,"##########################################"."\r\n\r\n");
		
	}
}


/*
fwrite($f,"########################################## END ".date("Y-m-d H:i:s")."\r\n\r\n");
fclose($f);
chmod($textDir."Member_Change_".date("Ymd").".txt",0777);
*/
?>
