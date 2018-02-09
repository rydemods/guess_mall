<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";

if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

function getcsvdata($fields = array(), $delimiter = ',', $enclosure = '"', $pos = false) {
	$str = '';
	$escape_char = '\\';
	$count = 0;
	//exdebug($pos);
	foreach ($fields as $value) {
		
		if (strpos($value, $delimiter) !== false ||
		strpos($value, $enclosure) !== false ||
		strpos($value, "\n") !== false ||
		strpos($value, "\r") !== false ||
		strpos($value, "\t") !== false ||
		strpos($value, ' ') !== false ) {
			$str2 = $enclosure;
			$escaped = 0;
			$len = strlen($value);
			for ($i=0;$i<$len;$i++) {
				if ($value[$i] == $escape_char) {
					$escaped = 1;
				} else if (!$escaped && $value[$i] == $enclosure) {
					$str2 .= $enclosure;
				} else {
					$escaped = 0;
				}
				$str2 .= $value[$i];
			}
			$str2 .= $enclosure;
			$str .= $str2.$delimiter;
		} else {
			$str .= $value.$delimiter;
		}
		/*if ($count == 21){
			exdebug($count);
			exdebug($str);
		}*/
		$count++;
	}
	$str = rtrim($str,$delimiter);
	$str .= "\n";
	return $str;
}

@set_time_limit(300);

$mode   = $_POST["mode"];
$no     = $_POST["no"];

if($mode=="download") {
	Header("Content-Disposition: attachment; filename=event_comment_".date("Ymd").".csv");
	Header("Content-type: application/x-msexcel;");
	header("Content-Description: PHP4 Generated Data" );

	$patten = array ("\r");
	$replace = array ("");

	$field=array(
		iconv( 'utf-8', 'euc-kr', "NO" ),
		iconv( 'utf-8', 'euc-kr', "아이디" ),
		iconv( 'utf-8', 'euc-kr', "내용" ),
		iconv( 'utf-8', 'euc-kr', "날짜" ),
	);

	echo getcsvdata($field);

    $sql  = "SELECT * FROM tblboardcomment_promo ";
    $sql .= "WHERE parent = {$no} ";
    $sql .= "ORDER BY num desc ";

    $result = pmysql_query($sql);

    $num = 1;
	while ($row=pmysql_fetch_object($result)) {
		$field=array();

		$field[]=iconv( 'utf-8', 'euc-kr', $num);
		$field[]=iconv( 'utf-8', 'euc-kr', $row->c_mem_id);
		$field[]=iconv( 'utf-8', 'euc-kr', trim($row->comment));
		$field[]=date("Y-m-d H:i:s", $row->writetime);
		
		echo(getcsvdata($field,',','"',true));
		flush();

        $num++;
	}
    
	pmysql_free_result($result);
}
?>
