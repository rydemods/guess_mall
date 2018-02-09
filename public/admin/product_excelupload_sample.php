<?php // hspark
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

function getcsvdata($fields = array(), $delimiter = ',', $enclosure = '"') {
	$str = '';
	$escape_char = '\\';
	foreach ($fields as $value) {
		if (strpos($value, $delimiter) !== false ||
		strpos($value, $enclosure) !== false ||
		strpos($value, "\n") !== false ||
		strpos($value, "\r") !== false ||
		strpos($value, "\t") !== false ||
		strpos($value, ' ') !== false) {
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
	}
	$str = rtrim($str,$delimiter);
	$str .= "\n";
	return $str;
}

@set_time_limit(300);

Header("Content-Disposition: attachment; filename=product_".date("Ymd").".csv");
Header("Content-type: application/x-msexcel");

$patten = array ("\r");
$replace = array ("");

$sampleArray = array(
	iconv( 'utf-8', 'euc-kr', "1차 카테고리" ),
	iconv( 'utf-8', 'euc-kr', "2차 카테고리" ),
	iconv( 'utf-8', 'euc-kr', "3차 카테고리" ),
	iconv( 'utf-8', 'euc-kr', "4차 카테고리" ),
	iconv( 'utf-8', 'euc-kr', "상품명" ),
	iconv( 'utf-8', 'euc-kr', "시중가격" ),
	iconv( 'utf-8', 'euc-kr', "단일판매가격" ),
	iconv( 'utf-8', 'euc-kr', "구입원가" ),
	iconv( 'utf-8', 'euc-kr', "제조사" ),
	iconv( 'utf-8', 'euc-kr', "원산지" ),
	iconv( 'utf-8', 'euc-kr', "브랜드" ),
	iconv( 'utf-8', 'euc-kr', "매입처" ),
	iconv( 'utf-8', 'euc-kr', "출시일" ),
	iconv( 'utf-8', 'euc-kr', "적립금 (률)" ),
	iconv( 'utf-8', 'euc-kr', "적립률 YN" ),
	iconv( 'utf-8', 'euc-kr', "재고" ),
	iconv( 'utf-8', 'euc-kr', "상품진열여부" ),
	iconv( 'utf-8', 'euc-kr', "설명" )
);

$sampleArray2 = array(
	iconv( 'utf-8', 'euc-kr', "디지털" ),
	iconv( 'utf-8', 'euc-kr', "컴퓨터" ),
	iconv( 'utf-8', 'euc-kr', "노트북" ),
	iconv( 'utf-8', 'euc-kr', "" ),
	iconv( 'utf-8', 'euc-kr', "상품 1번" ),
	iconv( 'utf-8', 'euc-kr', "1000" ),
	iconv( 'utf-8', 'euc-kr', "1000" ),
	iconv( 'utf-8', 'euc-kr', "100" ),
	iconv( 'utf-8', 'euc-kr', "제조사" ),
	iconv( 'utf-8', 'euc-kr', "원산지" ),
	iconv( 'utf-8', 'euc-kr', "브랜드" ),
	iconv( 'utf-8', 'euc-kr', "매입처" ),
	iconv( 'utf-8', 'euc-kr', "20151025" ),
	iconv( 'utf-8', 'euc-kr', "0" ),
	iconv( 'utf-8', 'euc-kr', "N" ),
	iconv( 'utf-8', 'euc-kr', "0" ),
	iconv( 'utf-8', 'euc-kr', "N" ),
	iconv( 'utf-8', 'euc-kr', "상품 상세설명을 입력하세요" ),
);


echo getcsvdata($sampleArray);

echo getcsvdata($sampleArray2);
flush();

exit;

?>