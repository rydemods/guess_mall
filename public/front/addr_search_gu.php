<?
$noDemoMsg = 1;
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
header("Content-Type: text/html; charset=EUC-KR");

$location= $_REQUEST['location']; 
//msg($_GET['sigungu']);
//msg($_SERVER[REQUEST_URI]);

$sigungu = $_REQUEST['sigungu'];

$wh = "sido=''";
switch($location){
	case "SEOUL" :
		$wh = "sido='서울특별시'";
		break;
	case "BUSAN" :
		$wh = "sido='부산광역시'";
		break;
	case "INCHEON" :
		$wh = "sido='인천광역시'";
		break;
	case "DAEGU" :
		$wh = "sido='대구광역시'";
		break;
	case "DAEJEON" :
		$wh = "sido='대전광역시'";
		break;
	case "GWANGJU" :
		$wh = "sido='광주광역시'";
		break;
	case "ULSAN" :
		$wh = "sido='울산광역시'";
		break;
	case "SEJONG" :
		$wh = "sido='세종특별자치시'";
		break;
	case "JEJU" :
		$wh = "sido='제주특별자치도'";
		break;
	case "GYEONGGI" :
		$wh = "sido='경기도'";
		break;
	case "GANGWON" :
		$wh = "sido='강원도'";
		break;
	case "CHUNGBOOK" :
		$wh = "sido='충청북도'";
		break;
	case "CHUNGNAM" :
		$wh = "sido='충청남도'";
		break;
	case "GYEONGBOOK" :
		$wh = "sido='경상북도'";
		break;
	case "GYEONGNAM" :
		$wh = "sido='경상남도'";
		break;
	case "JEONBOOK" :
		$wh = "sido='전라북도'";
		break;
	case "JEONNAM" :
		$wh = "sido='전라남도'";
		break;
}

$query = "SELECT sigungu FROM GD_ZIPCODE_DORO_SIDO where ".$wh."";
//exdebug($query);

//echo $sigungu;
$result = pmysql_query($query);
	echo "<select name='sigungu' id='sigungu' class='ng' style='width:116px;'>";
	echo "<option value=''>선택해주세요</option>";

while($data = pmysql_fetch_array($result))
{
	$selected = $data[sigungu]==$sigungu?"selected":"";
	echo "<option value='".$data[sigungu]."' ".$selected." >".$data[sigungu]."</option>";
}
	echo "</select>";

?>
