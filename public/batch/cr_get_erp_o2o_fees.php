#!/usr/local/php/bin/php
<?php
//exit;
#######################################################################################
# FileName          : cr_get_erp_o2o_fees.php
# Desc              : 매일 자정에 실행되어 ERP로부터 O2O 수수료율 정보 가져오기
# Last Updated      : 2016-09-01
# By                : Peter,Kim
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_get_erp_o2o_fees.sh 
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
@set_time_limit(0);

//$sql = "DELETE FROM tblstore_o2o_fees";
//pmysql_query($sql,get_db_conn());

 $conn = oci_connect("swonline", "commercelab", "125.128.119.220/SWERP", "US7ASCII");

echo "START = ".date("Y-m-d H:i:s")."\r\n";

$sql = "SELECT PART_DIV,
            PART_NO,
            BRANDCD,
			O2O_GB,
			APPLY_FDATE,
			APPLY_TDATE,
			FEE_RATE 
			FROM (
			SELECT PART_DIV,
						PART_NO,
						BRAND AS BRANDCD,
						'1' AS O2O_GB,
						APPLY_FDATE,
						APPLY_TDATE,
						FEE_RATE,
						RCV_DATE
						FROM TA_OM003 
			UNION ALL
			SELECT PART_DIV,
						PART_NO,
						BRAND AS BRANDCD,
						'2' AS O2O_GB,
						APPLY_FDATE,
						APPLY_TDATE,
						FEE_RATE,
						RCV_DATE
						FROM TA_OM003 
			UNION ALL
			SELECT PART_DIV,
						PART_NO,
						BRAND AS BRANDCD,
						'3' AS O2O_GB,
						APPLY_FDATE,
						APPLY_TDATE,
						FEE_RATE,
						RCV_DATE
						FROM TA_OM003 
			) A 
			WHERE 1=1
			AND RCV_DATE is NULL
			ORDER BY PART_DIV, PART_NO, BRANDCD, O2O_GB

        ";
$smt = oci_parse($conn, $sql);
oci_execute($smt);
//echo $sql."\r\n";
//exit;

$cnt = 0;
while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

    foreach($data as $k => $v)
    {
        $data[$k] = utf8encode($v);
    }

	$store_code	= $data[PART_DIV].$data[PART_NO].$data[BRANDCD];
	$o2o_gb			= $data[O2O_GB];
	$regdt			= date("YmdHis");

    echo "part_div = ".$data[PART_DIV]." / part_no = ".$data[PART_NO]." / brand = ".$data[BRANDCD]."\r\n";

	$sql = "
			WITH upsert as (
				update  tblstore_o2o_fees 
				set  fee_rate      = '".$data[FEE_RATE]."',
				apply_tdate = '".$data[APPLY_TDATE]."'
				where store_code      = '".$store_code."' AND part_div = '".$data[PART_DIV]."' AND part_no = '".$data[PART_NO]."' AND brandcd = '".$data[BRANDCD]."' AND apply_fdate = '".$data[APPLY_FDATE]."' AND o2o_gb = '".$data[O2O_GB]."' 
				RETURNING * 
			)
			insert into tblstore_o2o_fees 
			(store_code, part_div, part_no, brandcd, o2o_gb, apply_fdate, apply_tdate, fee_rate, regdt  )
			Select  '".$store_code."','".$data[PART_DIV]."','".$data[PART_NO]."','".$data[BRANDCD]."', '".$data[O2O_GB]."', '".$data[APPLY_FDATE]."', '".$data[APPLY_TDATE]."', '".$data[FEE_RATE]."','".$regdt."'
			WHERE NOT EXISTS ( SELECT * FROM upsert )
			";
	$ret = pmysql_query($sql, get_db_conn());
	print_r($sql);
	if($err=pmysql_error()) echo $err."\r\n";

	if($o2o_gb!='3') {
		$up_sql1  = "UPDATE tblstore SET ";
		if($o2o_gb=='1') $up_sql1 .= "pickup_yn='Y' ";
		if($o2o_gb=='2') $up_sql1 .= "delivery_yn='Y' ";
		$up_sql1 .= "WHERE store_code='{$store_code}' ";
		$ret2 = pmysql_query($up_sql1,get_db_conn());
		print_r($up_sql1);
		if($err=pmysql_error()) echo $err."\r\n";
	}

	// 실서버에는 주석풀자..
	$sql = "update TA_OM003 set RCV_DATE=SYSDATE where RCV_DATE is NULL and PART_DIV = '".$data[PART_DIV]."' AND PART_NO = '".$data[PART_NO]."' AND BRAND = '".$data[BRANDCD]."' AND APPLY_FDATE = '".$data[APPLY_FDATE]."' AND APPLY_TDATE = '".$data[APPLY_TDATE]."' ";
	$smt_rec = oci_parse($conn, $sql);
	oci_execute($smt_rec);

    $cnt++;

    if( ($cnt%1000) == 0) echo "cnt = ".$cnt."\r\n";
	/*if ($cnt==20) {
		oci_free_statement($smt);
		oci_close($conn);

		echo "END = ".date("Y-m-d H:i:s")."\r\n";
		exit;
	}*/
}

$nowdt			= date("Ymd");

$up_sql = "
update tblstore set pickup_yn='N', delivery_yn='N', day_delivery_yn='N' where store_code in (
    SELECT store_code FROM tblstore 
    where store_code not in (
        select store_code from tblstore_o2o_fees 
        where apply_fdate <= '{$nowdt}' and apply_tdate >= '{$nowdt}' group by store_code
    ) 
    and (pickup_yn='Y' or delivery_yn='Y') 
    and store_code!='A1801B'
)";
pmysql_query($up_sql,get_db_conn());
print_r($up_sql);
if($err=pmysql_error()) echo $err."\r\n";

oci_free_statement($smt);
oci_close($conn);

echo "END = ".date("Y-m-d H:i:s")."\r\n";
?>
