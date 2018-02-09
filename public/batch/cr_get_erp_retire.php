#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_get_erp_retire.php
# Desc              : 매년 1월1일에 실행되어 ERP로부터 매장정보 가져오기
# Last Updated      : 2018-01-05
# By                : KyungSu,JUng
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_get_erp_retire.sh 
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

@set_time_limit(0);

$conn = GetErpDBConn();

echo "START = ".date("Y-m-d H:i:s")."\r\n";

$sql = "SELECT YYYY,
            EMP_NO,
            RETIRE_DT
			FROM TA_OM022 
			WHERE 1=1
			AND RCV_DATE is NULL
        ";

$smt = oci_parse($conn, $sql);
oci_execute($smt);
echo $sql."\r\n";
//exit;

$cnt = 0;

while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

	echo $cnt."\r\n";

    foreach($data as $k => $v)
    {
        $data[$k] = utf8encode($v);
    }

	list($staff_yn,$id) = pmysql_fetch("SELECT staff_yn,id FROM tblmember WHERE erp_emp_id = '".$data[EMP_NO]."'");


	if($staff_yn == 'Y'){

		$sql = "
					update  tblmember 
					set 	
							staff_yn = 'N',
							erp_emp_id = ''
					where	erp_emp_id = '".$data[EMP_NO]."'
				";
		$ret = pmysql_query($sql, get_db_conn());
		print_r($sql);
		echo "\r\n";
		if($err=pmysql_error()) echo $err."\r\n";		

		$sql = "
				INSERT INTO tblmemberchange_erp (mem_id, emo_no, change_year, change_date)
				values('".$id."','".$data[EMP_NO]."','".$data[YYYY]."', now())
				";
		$ret = pmysql_query($sql, get_db_conn());
		print_r($sql);
		echo "\r\n";
		if($err=pmysql_error()) echo $err."\r\n";

		// 실서버에는 주석풀자..
		$sql = "UPDATE TA_OM022 SET RCV_DATE=SYSDATE WHERE RCV_DATE is NULL AND EMP_NO = '".$data[EMP_NO]."'  ";
		$smt_rec = oci_parse($conn, $sql);
		oci_execute($smt_rec);
		print_r($sql);
		echo "\r\n";
	}

    if( ($cnt%1000) == 0) {
		echo "cnt = ".$cnt."\r\n";
		sleep(10);
	}
}

oci_free_statement($smt);
oci_close($conn);

pmysql_free_result($ret);

echo "END = ".date("Y-m-d H:i:s")."\r\n";


?>