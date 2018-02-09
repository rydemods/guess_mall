<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다

@set_time_limit(0);

//정보고시 구하기
function getJungboInfo($jungbo_cd, $mixrate, $colorcd, $size) {
	global $jungbo_code;
	
	$incode = $jungbo_code[$jungbo_cd];

	$jungboinfo_val_arr	= array();
	$jungboinfo_val_arr[0]	= $mixrate;
	$jungboinfo_val_arr[1]	= $colorcd;
	$jungboinfo_val_arr[2]	= str_replace("@#","/", $size);
	$jungboinfo_val_arr[3]	= $incode['comment'][3];
	$jungboinfo_val_arr[4]	= $incode['comment'][4];
	$jungboinfo_val_arr[5]	= $incode['comment'][5];
	$jungboinfo_val_arr[6]	= $incode['comment'][6];
	$jungboinfo_val_arr[7]	= $incode['comment'][7];
	$jungboinfo_val_arr[8]	= $incode['comment'][8];

	$jungboinfo_option	= $jungbo_cd;
	$jungboinfo_val		= $jungbo_cd;
	foreach( $incode['option'] as $inKey=>$inVal ){
		$jungboinfo_option .= "||".$inVal;
		$jungboinfo_val .= "||".$jungboinfo_val_arr[$inKey];
	}
	$jungboinfo				= array();
	$jungboinfo['option']	= $jungboinfo_option;
	$jungboinfo['val']		= $jungboinfo_val;

	return $jungboinfo;
}

$cnt = 0;
$sql = "SELECT  * FROM tblproduct WHERE join_yn = 'N' ORDER BY pridx ASC
		"; // LIMIT 100
$result = pmysql_query($sql, get_db_conn());
echo $sql."\r\n";
while($row = pmysql_fetch_array($result)) {
	$productcode	= $row['productcode'];
	$mixrate		= $row['mixrate'];
	$colorcode		= $row['colorcode'];
	$sizecd			= $row['sizecd'];

	$sabangnet_prop			= getJungboInfo("001", $mixrate, $colorcode, $sizecd);
	$sabangnet_prop_option	= $sabangnet_prop['option'];
	$sabangnet_prop_val		= $sabangnet_prop['val'];

	$up_sql = "UPDATE tblproduct SET 
					sabangnet_prop_option='".$sabangnet_prop_option."',
					sabangnet_prop_val='".$sabangnet_prop_val."'
					";
	$up_sql.= "WHERE productcode='{$productcode}'";
	echo "sql=>".$up_sql."\r\n";
	pmysql_query($up_sql,get_db_conn());

    $cnt++;
	echo "cnt = ".$cnt."\r\n";
    if( ($cnt%1000) == 0) {
		sleep(5);
	}
}
exdebug('end');
?>