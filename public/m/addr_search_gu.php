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
		$wh = "sido='����Ư����'";
		break;
	case "BUSAN" :
		$wh = "sido='�λ걤����'";
		break;
	case "INCHEON" :
		$wh = "sido='��õ������'";
		break;
	case "DAEGU" :
		$wh = "sido='�뱸������'";
		break;
	case "DAEJEON" :
		$wh = "sido='����������'";
		break;
	case "GWANGJU" :
		$wh = "sido='���ֱ�����'";
		break;
	case "ULSAN" :
		$wh = "sido='��걤����'";
		break;
	case "SEJONG" :
		$wh = "sido='����Ư����ġ��'";
		break;
	case "JEJU" :
		$wh = "sido='����Ư����ġ��'";
		break;
	case "GYEONGGI" :
		$wh = "sido='��⵵'";
		break;
	case "GANGWON" :
		$wh = "sido='������'";
		break;
	case "CHUNGBOOK" :
		$wh = "sido='��û�ϵ�'";
		break;
	case "CHUNGNAM" :
		$wh = "sido='��û����'";
		break;
	case "GYEONGBOOK" :
		$wh = "sido='���ϵ�'";
		break;
	case "GYEONGNAM" :
		$wh = "sido='��󳲵�'";
		break;
	case "JEONBOOK" :
		$wh = "sido='����ϵ�'";
		break;
	case "JEONNAM" :
		$wh = "sido='���󳲵�'";
		break;
}

$query = "SELECT sigungu FROM GD_ZIPCODE_DORO_SIDO where ".$wh."";
//exdebug($query);

//echo $sigungu;
$result = pmysql_query($query);
	echo "<select name='sigungu' id='sigungu' class='ng' style='width:116px;'>";
	echo "<option value=''>�������ּ���</option>";

while($data = pmysql_fetch_array($result))
{
	$selected = $data[sigungu]==$sigungu?"selected":"";
	echo "<option value='".$data[sigungu]."' ".$selected." >".$data[sigungu]."</option>";
}
	echo "</select>";
	echo "<p>����Ư����ġ�ô� ���û����� �����ϴ�.</p>";

?>
