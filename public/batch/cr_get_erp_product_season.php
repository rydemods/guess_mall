<?php
#######################################################################################
# FileName          : cr_get_erp_product_season.php
# Desc              : 시즌정보 가져오기
# Desc2             : 
# Last Updated      : 2017-04-06
# By                : jae su
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_get_erp_product_season.sh 
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

@set_time_limit(0);

//$conn = oci_connect("swonline", "commercelab", "125.128.119.220/SWERP", "US7ASCII");
$conn = GetErpDBConn();

echo "START = ".date("Y-m-d H:i:s")."\r\n";

$sql = "SELECT * 
			FROM TA_CC030 
			WHERE SEASON_YEAR > '2013' 
			AND SEASON_YEAR < '9999' 
			ORDER BY SEASON_YEAR, SEASON_GB
        ";

$smt = oci_parse($conn, $sql);
oci_execute($smt);
echo $sql."\r\n";

$cnt = 0;
while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

    foreach($data as $k => $v)
    {
        $data[$k] = utf8encode($v);
    }

    echo "season_year = ".$data[SEASON_YEAR]." / season = ".$data[SEASON]." / season_kor_name = ".$data[SEASON_KOR_NAME]." / season_eng_name = ".$data[SEASON_ENG_NAME]." / season_gb = ".$data[SEASON_GB]." / use_yn = ".$data[USE_YN]."\r\n";

    $sql = "
            WITH upsert as (
                update  tblproductseason 
                set 	season_kor_name = '".$data[SEASON_KOR_NAME]."',
                        season_eng_name = '".$data[SEASON_ENG_NAME]."',
                        season_gb = '".$data[SEASON_GB]."',
                        use_yn = '".$data[USE_YN]."'
                where	season_year = '".$data[SEASON_YEAR]."' 
				and season = '".$data[SEASON]."' 
                RETURNING * 
            )
            insert into tblproductseason 
            (
			season_year, 
			season, 
			season_kor_name, 
			season_eng_name, 
			season_gb, 
			use_yn
			)
            Select  
			'".$data[SEASON_YEAR]."', 
			'".$data[SEASON]."', 
			'".$data[SEASON_KOR_NAME]."', 
			'".$data[SEASON_ENG_NAME]."', 
			'".$data[SEASON_GB]."', 
			'".$data[USE_YN]."'
            WHERE NOT EXISTS ( select * from upsert ) 
            ";
    $ret = pmysql_query($sql, get_db_conn());
    print_r($sql);
    if($err=pmysql_error()) echo $err."\r\n";

    $cnt++;
    if( ($cnt%1000) == 0) {
		echo "cnt = ".$cnt."\r\n";
		sleep(10);
	}
	//exit;
}

oci_free_statement($smt);
oci_close($conn);

pmysql_free_result($ret);

echo "END = ".date("Y-m-d H:i:s")."\r\n";

?>
