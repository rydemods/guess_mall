<?
include_once('outline/header_m.php');
include_once($Dir."lib/file.class.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl());
	exit;
}

$mode = $_POST["mode"];
$idx = $_POST["idx"] ? $_POST["idx"] : $_GET["idx"];
$email = $_POST["email"];
$ip = $_SERVER['REMOTE_ADDR'];
$subjectl = pg_escape_string($_POST["pop_subject"]);
$today = date("YmdHis");
$content = pg_escape_string($_POST["pop_content"]);
$content = str_replace("\r\n","<br/>",$content); //줄바꿈 처리
// $content = htmlspecialchars($_POST["content"], ENT_QUOTES);  //특수문자를 HTML엔터티로 변환
// $content = str_replace("\r\n","<br/>",$content); //줄바꿈 처리
// $content = str_replace("\u0020","&nbsp;",$content); // 스페이스바 처리
$head_title = $_POST["head_title"];
$hp = $_POST["hp"];
$chk_mail = $_POST["chk_mail"];
$chk_sms = $_POST["chk_sms"];

#파일 업로드
$filepath = $Dir.DataDir."shopimages/personal/";
$up_file = new FILE($filepath);
$file = $up_file->upFiles();
$up_filename = $file["up_filename"][0]["v_file"];
$up_filename = '';
$ori_filename = $_POST["ori_filename"];

$view_sql ="SELECT * FROM tblpersonal WHERE idx = '".$idx."'";
$result = pmysql_query($view_sql, get_db_conn());
$row = pmysql_fetch_object($result);
$data = $row;
if($mode == "insert"){
	$sql = "INSERT INTO tblpersonal (
	id,
	name,
	email,
	ip,
	subject,
	date,
	content,
	head_title,
	\"HP\",
	chk_mail,
	chk_sms,
	up_filename,
	ori_filename
	)values(
	'{$_ShopInfo->getMemid()}',
	'{$_ShopInfo->getMemname()}',
	'{$email}',
	'{$ip}',
	'{$subjectl}',
	'{$today}',
	'{$content}',
	'{$head_title}',
	'{$hp}',
	'{$chk_mail}',
	'{$chk_sms}',
	'{$up_filename}',
	'{$ori_filename}'
	)";
	$result = pmysql_query($sql,get_db_conn());

	if(!pmysql_error()){
		echo  "<script>alert(' 정상적으로 등록되었습니다.'); location.href=\"/m/mypage_personal.php\"</script>";
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}
} else if($mode == "modify"){
	$where[]="head_title='".$head_title."'";
	$where[]="subject='".$subjectl."'";
	$where[]="\"HP\"='".$hp."'";
	$where[]="content='".$content."'";
	$where[]="email='".$email."'";
	$where[]="chk_mail='".$chk_mail."'";
	$where[]="chk_sms='".$chk_sms."'";

	#첨부파일이 변경되면 기존에 있는 파일 삭제 & 새로운 파일 업데이트
	if ($data->ori_filename != $ori_filename) {
		$where[]="up_filename='".$up_filename."'";
		$where[]="ori_filename='".$ori_filename."'";
		$up_file->removeFile($data->up_filename);
	}

	$usql = "UPDATE tblpersonal SET ";
	$usql.= implode(", ",$where);
	$usql.=" WHERE idx = '".$idx."'";

	pmysql_query( $usql, get_db_conn() );

	if(!pmysql_error()){
		echo  "<script>alert('수정이 완료되었습니다.'); location.href=\"/m/mypage_personal.php\"</script>";
	}else{
		echo  "<script>alert('오류가 발생하였습니다.'); location.href=\"/m/front/mypage_personal.php\"</script>";
	}
} else if ($mode == "delete"){
	#파일삭제
	$filepath = $Dir.DataDir."shopimages/personal/";
	$up_file = new FILE($filepath);
	if ($data->up_filename !="") $up_file->removeFile($data->up_filename);
	#데이터삭제
	$dSql = "DELETE FROM tblpersonal WHERE idx = '".$idx."'";

	pmysql_query($dSql, get_db_conn());

	if(!pmysql_error()){
		echo  "<script>alert('삭제가 완료되었습니다..'); location.href=\"/m/mypage_personal.php\"</script>";
	}else{
		//alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		echo  "<script>alert('오류가 발생하였습니다.'); location.href=\"/m/mypage_personal.php\"</script>";
	}
}

?>

