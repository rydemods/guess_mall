<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-4";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

include("header.php");

//print_r($_POST);
//print_r($_GET);
//echo $_SERVER['REQUEST_URI'];

if($_GET[no]) {
    $_POST['menu_type'] = $mSelect['banner_no'] = $_GET[no];
}

$search_hidden      = $_REQUEST['search_hidden'];

# POST 기본값 세팅
$menu_type = $_POST['menu_type'];
$mode = $_POST['mode'];

$idx            = $_POST['idx'];
$visible_mode   = $_POST['visible_mode'];

if ( count($idx) >= 1 ) {
    $whereIdx = implode(",", $idx);
    $sql  = "UPDATE tblmainbannerimg SET banner_hidden = {$visible_mode} WHERE no in ( " . $whereIdx . " ) ";
    pmysql_query($sql, get_db_conn());
}

//배너 타입
$in_type = $_POST['in_type'];
$v_in_type = $_POST['v_in_type'];
//배너 이미지(PC)
$v_banner_img = $_POST['v_banner_img'];
//배너 이미지(MOBILE)
$v_banner_img_m = $_POST['v_banner_img_m'];
//배너 노출순서
$banner_sort=$_POST["banner_sort"];
//배너 링크
$banner_link=$_POST["banner_link"];
//배너 타이틀
$banner_title=$_POST["banner_title"];
//배너 타이틀 링크
$banner_t_link=$_POST["banner_t_link"];
//배너 노출
$banner_hidden=$_POST["banner_hidden"];
if( $banner_hidden == '' || is_null( $banner_hidden ) ){
	$banner_hidden = '0';
}

//기본 텍스트
$banner_name=$_POST["banner_name"];
//선택 텍스트
$banner_subname=$_POST["banner_subname"];
//선택 텍스트
$banner_subname2=$_POST["banner_subname2"];
//배너 텍스트 링크
$banner_n_link=$_POST["banner_n_link"];

//배너 타이틀 색상
$banner_title_color=$_POST["banner_title_color"];
//기본 텍스트 색상
$banner_name_color=$_POST["banner_name_color"];
//선택 텍스트 색상
$banner_subname_color=$_POST["banner_subname_color"];
//선택 텍스트2 색상
$banner_subname_color2=$_POST["banner_subname_color2"];

//관련상품
$relationProduct = $_POST['relationProduct'];

# open 위치 추가
$banner_target = $_POST['banner_target'];
# 카테고리 추가
$code_a = $_POST['code_a'];
$code_b = $_POST['code_b'];
$code_c = $_POST['code_c'];
$code_d = $_POST['code_d'];
if( $code_a ){
	if( $code_b=="" || is_null( $code_b ) ) $code_b = '000';
	if( $code_c=="" || is_null( $code_c ) ) $code_c = '000';
	if( $code_d=="" || is_null( $code_d ) ) $code_d = '000';
}

$promo_idx = $_POST['promo_idx'];

$cate_number = $code_a.$code_b.$code_c.$code_d;
# 이미지 경로
$imagepath = $Dir.DataDir."shopimages/mainbanner/";

#배너 입력 수정 삭제
// 이미지 파일
$banner_file = new FILE($imagepath);
// 배너 어드민 정보
if( strlen( $mode ) > 0 && ( strlen( $in_type ) > 0 || strlen( $v_in_type ) > 0 )){
	if( strlen( $in_type ) > 0 ){
		$bAdminNo = $in_type;
	} else {
		$bAdminNo = $v_in_type;
	}
	$bAdminSql = "SELECT mb.no, mb.title, mb.img_number, ";
	$bAdminSql.= "mb.titlename, mb.cate_use_yn, mbi.banner_cnt, mbi.banner_maxnum ";
	$bAdminSql.= "FROM tblmainbanner mb ";
	$bAdminSql.= "LEFT JOIN (SELECT banner_no, COUNT( banner_number ) as banner_cnt, MAX( banner_number ) as banner_maxnum FROM tblmainbannerimg WHERE banner_no = ".$bAdminNo." GROUP BY banner_no ) mbi ";
	$bAdminSql.= "ON mbi.banner_no = mb.no ";
	$bAdminSql.= "WHERE no = '".$bAdminNo."' ";
	$bAdminRes = pmysql_query( $bAdminSql, get_db_conn() );
	$bAdminRow = pmysql_fetch_object( $bAdminRes);
	if( $bAdminRow->banner_maxnum == '' || is_null( $bAdminRow->banner_maxnum ) ) {
		$bAdminRow->banner_maxnum = 0;
	}
	$bAdmin = $bAdminRow;
	pmysql_free_result( $bAdminRes );
}
if($mode=="delete") {
	# 배너 넘버
	$img_no = $_POST['img_no'];
	
	$relationProduct_del = "DELETE FROM tblmainbannerimg_product WHERE tblmainbannerimg_no = '".$img_no."' ";
	pmysql_query($relationProduct_del,get_db_conn());

	if( !pmysql_error() ){
		if( strlen( $v_banner_img ) > 0 && is_file( $imagepath.$v_banner_img ) ){
			$banner_file->removeFile( $v_banner_img );
		}
		if( strlen( $v_banner_img_m ) > 0 && is_file( $imagepath.$v_banner_img_m ) ){
			$banner_file->removeFile( $v_banner_img_m );
		}
		$qry = "DELETE FROM tblmainbannerimg WHERE no='".$img_no."' ";
		pmysql_query( $qry, get_db_conn() );
	}
	$qry = '';
//	alert_go('삭제가 완료되었습니다.', $_SERVER['REQUEST_URI']);
	alert_go('삭제가 완료되었습니다.', $_SERVER['PHP_SELF'] . "?no=" . $mSelect['banner_no']  . "&search_cate=" . $search_cate . "&search_hidden=" . $search_hidden . "&search_up_title=" . $search_up_title);

} else if($mode=="modify") {
	//exdebug($_POST);
	//exit;
	# 배너 넘버
	$img_no = $_POST['img_no'];
	$banner_img=$banner_file->upFiles();
	$where="";

	if( $bAdmin->cate_use_yn == 'Y' ){
		$where[]="banner_category='".$cate_number."'";
	}	

	if( strlen( $banner_img["banner_img"][0]["v_file"] ) > 0 ){
		if( is_file( $imagepath.$v_banner_img ) > 0 ){
			$banner_file->removeFile( $v_banner_img );
		}
		$where[]="banner_img='".$banner_img["banner_img"][0]["v_file"]."'";
	}

	if( strlen( $banner_img["banner_img"][1]["v_file"] ) > 0 ){
		if( is_file( $imagepath.$v_banner_img_m ) > 0 ){
			$banner_file->removeFile( $v_banner_img_m );
		}
		$where[]="banner_img_m='".$banner_img["banner_img"][1]["v_file"]."'";
	}
	
	$where[]="banner_sort='".$banner_sort."'";
	$where[]="banner_title='".$banner_title."'";
	$where[]="banner_title_color='".$banner_title_color."'";
	$where[]="banner_link='".$banner_link."'";
	$where[]="banner_name='".$banner_name."'";
	$where[]="banner_name_color='".$banner_name_color."'";
	$where[]="banner_subname='".$banner_subname."'";
	$where[]="banner_subname_color='".$banner_subname_color."'";
	$where[]="banner_subname2='".$banner_subname2."'";
	$where[]="banner_subname_color2='".$banner_subname_color2."'";
	$where[]="banner_n_link='".$banner_n_link."'";
	$where[]="banner_hidden='".$banner_hidden."'";
	$where[]="banner_t_link='".$banner_t_link."'";
	$where[]="banner_target='".$banner_target."'";
	$where[]="promo_idx=".$promo_idx;
	
	$qry="UPDATE tblmainbannerimg SET ";
	$qry.=implode(", ",$where);
	$qry.=" WHERE no='".$img_no."' ";	
	
	pmysql_query($qry,get_db_conn());
	if(!pmysql_error()){
		if($relationProduct){
			$relationProduct_del = "DELETE FROM tblmainbannerimg_product WHERE tblmainbannerimg_no = '".$img_no."' ";
			pmysql_query($relationProduct_del,get_db_conn());
			for($i=0;$i<count($relationProduct);$i++){
				$relationProduct_sql = "INSERT INTO tblmainbannerimg_product ";
				$relationProduct_sql.= "(tblmainbannerimg_no, productcode, date) ";
				$relationProduct_sql.= "VALUES (".$img_no.", '".$relationProduct[$i]."', '".date("YmdHis")."') ";
				pmysql_query($relationProduct_sql,get_db_conn());
			}
		}
		// 타이틀을 모두 업데이트를 한다.
		$titleUp_sql	= "UPDATE tblmainbannerimg SET banner_title='".$banner_title."', banner_title_color='".$banner_title_color."', banner_t_link='".$banner_t_link."' WHERE banner_no='".$v_in_type."'";
		pmysql_query($titleUp_sql,get_db_conn());	
		
//		alert_go('수정이 완료되었습니다.', $_SERVER['REQUEST_URI']);
		alert_go('수정이 완료되었습니다.', $_SERVER['PHP_SELF'] . "?no=" . $mSelect['banner_no']  . "&search_cate=" . $search_cate . "&search_hidden=" . $search_hidden . "&search_up_title=" . $search_up_title);
	}else{	
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	} 
	$qry = '';

} else if( $mode == 'insert' ){
	$banner_img=$banner_file->upFiles();
	//exdebug( $banner_img );
	if( $bAdmin->cate_use_yn == 'N' ){
		$cate_number = '';
	}
	$qry="insert into tblmainbannerimg (
		banner_no, 
		banner_img, 
		banner_img_m, 
		banner_sort, 
		banner_date, 
		banner_title, 
		banner_title_color, 
		banner_link, 
		banner_hidden,
		banner_number,
		banner_name,
		banner_name_color,
		banner_subname,
		banner_subname_color,
		banner_subname2,
		banner_subname_color2,
		banner_n_link, 
		banner_category,
		banner_t_link,
		banner_target,
        promo_idx
		)values(
		'".$v_in_type."',
		'".$banner_img["banner_img"][0]["v_file"]."',
		'".$banner_img["banner_img"][1]["v_file"]."',
		'".$banner_sort."',
		'now()',
		'".$banner_title."',
		'".$banner_title_color."',
		'".$banner_link."',
		'".$banner_hidden."',
		'".( $bAdmin->banner_maxnum + 1 )."',
		'".$banner_name."',
		'".$banner_name_color."',
		'".$banner_subname."',
		'".$banner_subname_color."',
		'".$banner_subname2."',
		'".$banner_subname_color2."',
		'".$banner_n_link."',
		'".$cate_number."',
		'".$banner_t_link."',
		'".$banner_target."',
		".$promo_idx."
	) ";
	
	$qry.= "RETURNING no ";
	
	$result = pmysql_query($qry,get_db_conn());
	
	if($row = pmysql_fetch_object($result)){
		if($relationProduct){
			for($i=0;$i<count($relationProduct);$i++){
				$relationProduct_sql = "INSERT INTO tblmainbannerimg_product ";
				$relationProduct_sql.= "(tblmainbannerimg_no, productcode, date) ";
				$relationProduct_sql.= "VALUES (".$row->no.", '".$relationProduct[$i]."', '".date("YmdHis")."') ";
				pmysql_query($relationProduct_sql,get_db_conn());
			}
		}
		
		// 타이틀을 모두 업데이트를 한다.
		$titleUp_sql	= "UPDATE tblmainbannerimg SET banner_title='".$banner_title."', banner_title_color='".$banner_title_color."', banner_t_link='".$banner_t_link."' WHERE banner_no='".$v_in_type."'";
		pmysql_query($titleUp_sql,get_db_conn());

//		alert_go('등록이 완료되었습니다.', $_SERVER['REQUEST_URI']);
		alert_go('등록이 완료되었습니다.', $_SERVER['PHP_SELF'] . "?no=" . $mSelect['banner_no']  . "&search_cate=" . $search_cate . "&search_hidden=" . $search_hidden . "&search_up_title=" . $search_up_title);
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}
	$qry = '';
	pmysql_free_result($result);
}


#배너 기본 세팅
$display['0'] = '비노출';
$display['1'] = '노출';

$prCateSql = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d AS cate_code, code_name FROM tblproductcode ORDER BY cate_code ASC ";
$prCateRes = pmysql_query( $prCateSql, get_db_conn() );
while( $prRow = pmysql_fetch_array( $prCateRes ) ){
	$prCate[ $prRow['cate_code'] ] = $prRow;
}
pmysql_free_result( $prCateRes );

# 배너 메뉴 불러오기

$menuRes = pmysql_query( "SELECT no, title, titlename, sort, cate_use_yn FROM tblmainbanner ORDER BY sort ", get_db_conn() );
while( $menuRow = pmysql_fetch_array( $menuRes) ){
	$menuCate[] = $menuRow;
	$menuArr[$menuRow['no']] = $menuRow['titlename'];
}
pmysql_free_result( $menuRes );

$bannerQry = "";

# 메뉴타입별 불러오기
if( !is_null($menu_type) && $menu_type!='' && strlen( $menu_type ) > 0 ){
	$bannerQry .= " AND banner_no = '".$menu_type."'";
}

if( !is_null($search_hidden) && $search_hidden != "" ) {
    $bannerQry .= " AND banner_hidden = ${search_hidden} ";
}

# 배너 페이징
$page_sql = "SELECT COUNT(*) FROM tblmainbannerimg WHERE 1=1 {$bannerQry} ";
$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

# 배너 리스트 불러오기

$bannerSql = "SELECT * FROM tblmainbannerimg WHERE 1=1 ";
$bannerSql.= $bannerQry;
$bannerSql.= "ORDER BY banner_no, banner_sort ";
$sql = $paging->getSql( $bannerSql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$bannerList[] = $row;
}

#---------------------------------------------------------------
# 카테고리 리스트 script 작성
#---------------------------------------------------------------

$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblproductcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
$i=0;
$ii=0;
$iii=0;
$iiii=0;
$strcodelist = "";
$strcodelist.= "<script>\n";
$result = pmysql_query($sql,get_db_conn());
$selcode_name="";

while($row=pmysql_fetch_object($result)) {
	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.code_a='{$row->code_a}';\n";
	$strcodelist.= "clist.code_b='{$row->code_b}';\n";
	$strcodelist.= "clist.code_c='{$row->code_c}';\n";
	$strcodelist.= "clist.code_d='{$row->code_d}';\n";
	$strcodelist.= "clist.type='{$row->type}';\n";
	$strcodelist.= "clist.code_name='{$row->code_name}';\n";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
		$strcodelist.= "lista[{$i}]=clist;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
		if ($row->code_c=="000" && $row->code_d=="000") {
			$strcodelist.= "listb[{$ii}]=clist;\n";
			$ii++;
		} else if ($row->code_d=="000") {
			$strcodelist.= "listc[{$iii}]=clist;\n";
			$iii++;
		} else if ($row->code_d!="000") {
			$strcodelist.= "listd[{$iiii}]=clist;\n";
			$iiii++;
		}
	}
	$strcodelist.= "clist=null;\n\n";
}
pmysql_free_result($result);
$display_type = "display:none;";
//$display_type = '';
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";


$codeA_list = "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다 display:none;
$codeB_list = "<select name=code_b id=code_b style=\"width:150px; height:150px; ".$display_type."\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다display:none;
$codeC_list = "<select name=code_c id=code_c style=\"width:150px; height:150px; ".$display_type."\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다display:none;
$codeD_list = "<select name=code_d id=code_d style=\"width:150px; height:150px; display:none;\" {$disabled} Multiple>\n";
$codeD_list.= "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
$codeD_list.= "</select>\n";

$codeSelect = "<span style=\"display:\" name=\"changebutton\"><input type=\"button\" value=\"선택\" style=\"height : 20px;\" onclick=\"javascript:exec_add()\"></span>";


# 수정할 배너 불러오기
if( $mode == 'modfiy_select' ){
	$img_no = $_POST['img_no'];
	$bSelectSql = "SELECT * FROM tblmainbannerimg WHERE no ='".trim($img_no)."' ";
	$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
	$bSelectRow = pmysql_fetch_array( $bSelectRes );
	$mSelect = $bSelectRow;
	pmysql_free_result( $bSelectRes );
	//수정
	$qType = '1';
	if( $mSelect['banner_category'] != '' || is_null( $mSelect['banner_category'] ) ){
		list($code_a,$code_b,$code_c,$code_d) = sscanf($mSelect['banner_category'],'%3s%3s%3s%3s');
		if(strlen($code_a)!=3) $code_a="000";
		if(strlen($code_b)!=3) $code_b="000";
		if(strlen($code_c)!=3) $code_c="000";
		if(strlen($code_d)!=3) $code_d="000";
	}
	
	$bProductSql = "SELECT a.productcode,b.productname,b.sellprice,b.tinyimage ";
	$bProductSql.= "FROM tblmainbannerimg_product a ";
	$bProductSql.= "JOIN tblproduct b ON a.productcode=b.productcode ";
	$bProductSql.= "WHERE a.tblmainbannerimg_no= ".trim($img_no);
	$bProductResult = pmysql_query($bProductSql,get_db_conn());
	while($bProductRow = pmysql_fetch_array($bProductResult)){
		$thisBannerProduct[] = $bProductRow;
	}
	pmysql_free_result( $bProductResult );
}

# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
	$bSelectSql = "SELECT * FROM tblmainbannerimg where banner_no = '".$menu_type."' limit 1";
	$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
	$bSelectRow = pmysql_fetch_array( $bSelectRes );
	$mSelect['banner_title']	= $bSelectRow['banner_title'];
	//echo "banner_title : ".$mSelect['banner_title'];
	pmysql_free_result( $bSelectRes );
}

# banner target array
$baerrTargetText = array(
	'_blank'=>'새창',
	'_self'=>'현재위치'
);

//네비게이션을 위해 불러온다(2016.01.12 - 김재수)
include("menu_design_navi.php"); 

if ($qType == '0') $mode_Text	= "등록";
if ($qType == '1') $mode_Text	= "수정";

if ($menu_type =='77' || 
	$menu_type == '85' || 
	$menu_type == '90' || 
	$menu_type == '93' || 
	$menu_type == '94' || 
	$menu_type == '99' || 
	$menu_type == '100' || 
	$menu_type == '101' || 
	$menu_type == '102' || 
	$menu_type == '105' || 
	$menu_type == '106' || 
	$menu_type == '103' ||  
	$menu_type == '107' ||
    $menu_type == '108' ) { // 이미지형 배너
	
	$form_listing_type	= "bannerImgCate";
	if ($menu_type =='90') {
		$form_listing_cate_type	= "on";
	}

} else if ($menu_type =='78' || 
	$menu_type == '79' || 
	$menu_type == '95' || 
	$menu_type == '96' || 
	$menu_type == '97' || 
	$menu_type == '98' ||
    $menu_type == '104' ) { // 상품+텍스트형 배너
	
	$form_listing_type	= "bannerProductTextCate";

} else if ($menu_type =='80') { // 이미지+상품형 배너
	
	$form_listing_type	= "bannerImgProductCate";

} else if ($menu_type =='87') { // 이미지+상품+텍스트형1 배너
	
	$form_listing_type	= "bannerImgProductTextCate1";
	$form_listing_cate_type	= "on";

} else if ($menu_type == '88' || 
	$menu_type == '89' || 
	$menu_type == '91' || 
	$menu_type == '92') { // 이미지+상품+텍스트형2 배너
	
	$form_listing_type	= "bannerImgProductTextCate2";
	if ($menu_type !='88') {
		$form_listing_cate_type	= "on";
	}

}

?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jscolor.min.js"></script>

<SCRIPT LANGUAGE="JavaScript">
var bannerImgCate = ['77', '85', '90', '93', '94', '99', '100', '101', '102', '103', '105', '106', '107', '108']; // 이미지형 배너
var bannerProductTextCate = ['78', '79', '95', '96', '97', '98', '104']; // 상품+텍스트형 배너
var bannerImgProductCate = ['80']; // 이미지+상품형 배너
var bannerImgProductTextCate1 = ['87']; // 이미지+상품+텍스트형1 배너
var bannerImgProductTextCate2 = ['88', '89', '91', '92']; // 이미지+상품+텍스트형2 배너

function GoPage(block,gotopage) {
	document.insertForm.mode.value = "";
	document.insertForm.block.value = block;
	document.insertForm.gotopage.value = gotopage;
	document.insertForm.submit();
}

//배너 카테고리
function exec_add()
{

	var ret;
	var str = new Array();
	var code_a=document.form1.code_a.value;
	var code_b=document.form1.code_b.value;
	var code_c=document.form1.code_c.value;
	var code_d=document.form1.code_d.value;

	if(!code_a) code_a="000";
	if(!code_b) code_b="000";
	if(!code_c) code_c="000";
	if(!code_d) code_d="000";
	sumcode=code_a+code_b+code_c+code_d;
	$.ajax({
		type: "POST",
		url: "product_register.ajax.php",
		data: "code_a="+code_a+"&code_b="+code_b+"&code_c="+code_c+"&code_d="+code_d
	}).done(function(msg) {
		if(msg=='nocate'){
			alert("상품카테고리 선택이 잘못되었습니다.");

		}else if(msg=='nolowcate'){
			alert("하위카테고리가 존재합니다.");
		}else{
			document.form1.code.value=sumcode;
			var code_a=document.getElementById("code_a");
			var code_b=document.getElementById("code_b");
			var code_c=document.getElementById("code_c");
			var code_d=document.getElementById("code_d");
		}

	});
}
// 수정 / 삭제 
function changeAction( mode , num ){
	//mode 0 -> insert, 1 -> modify, 2 -> modfiy_select, 3 -> delete
	
	if( mode == '0' ){
		if( confirm('등록하시겠습니까?') ){
			if( $('#banner_sort').val() == '' || $('#banner_sort').val() < 0 ){
				alert('노출순서를 입력해야 합니다.');
                $('#banner_sort').focus();
				return;
			}
        
            if( $("#promo_idx").val() == '' ) {
                alert('기획전을 선택해 주세요.');
                $("#promo_idx").focus();
                return;
            }

			$("#mode").val( 'insert' );
		} else {
			return;
		}
	} else if ( mode == '1' ) {
		/*$("input[name='relationProduct[]']").each(function(){
			alert ($(this).val());
		});
		return;*/

		if( confirm('수정하시겠습니까?') ){
			if( $('#banner_sort').val() == '' || $('#banner_sort').val() < 0 ){
				alert('노출순서를 입력해야 합니다.');
                $('#banner_sort').focus();
				return;
			}

            if( $("#promo_idx").val() == '' ) {
                alert('기획전을 선택해 주세요.');
                $("#promo_idx").focus();
                return;
            }

			$("#mode").val( 'modify' );
		} else {
			return;
		}
	} else if ( mode == '2' ) {
		$('#img_no').val( num );
		$("#mode").val( 'modfiy_select' );
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			$('#img_no').val( num );
			$("#mode").val( 'delete' );
		} else {
			return;
		}
	} else {
		alert('잘못된 입력입니다.');
		return;
	}

	if( checkForm() ){
		$("#insertForm").submit();
	}
	
}

function changeVisible(val) {
    // val : 1 => 노출, 0 => 비노출

    if ( $("input[name='idx[]']:checked").length == 0 ) {
        alert('하나 이상을 선택해 주세요.');
    } else {
        if ( val == "1" ) {
            msg = "노출 설정 하시겠습니까?";
        } else {
            msg = "비노출 설정 하시겠습니까?";
        }

        if ( confirm(msg) ) {
            document.insertForm.visible_mode.value = val;
            document.insertForm.submit();
        }
    }
}

// submit 하기전 값을 체크한다
function checkForm( mode ){
	var returnVal = true;
	return returnVal;
}

function typeChange(){
	var type = $("#in_type").val();
	var alt = '';
	$.each( $("#in_type").find('option') , function( index, obj ) {
		if( $(this).val() == type ){
			alt = $(this).attr('alt');
		}
	});

	if( alt == 'Y' ){
		$("#ID_trCate").show();
	} else {
		$("#ID_trCate").hide();
	}

	if( bannerImgCate.indexOf( type ) > -1 ){
		$("#ID_trImg").show();
		$("#ID_trImg_m").show();
		$("#ID_trLink").show();
		$("#ID_trTitle").hide();
		$("#ID_trT_Link").hide();
		$("#ID_RelationProduct").hide();
		$("#ID_trName").hide();
		$("#ID_trSubName").hide();
		$("#ID_trN_Link").hide();
	} else if( bannerProductTextCate.indexOf( type ) > -1 ) {
		$("#ID_trTitle").show();
		$("#ID_trImg").hide();
		$("#ID_trImg_m").hide();
		$("#ID_trLink").hide();
		$("#ID_RelationProduct").show();
		$("#ID_trName").hide();
		$("#ID_trSubName").hide();
		$("#ID_trN_Link").hide();
	}  else if( bannerImgProductCate.indexOf( type ) > -1 ) {
		$("#ID_trTitle").hide();
		$("#ID_trT_Link").hide();
		$("#ID_trImg").show();
		$("#ID_trImg_m").show();
		$("#ID_trLink").show();
		$("#ID_RelationProduct").show();
		$("#ID_trName").hide();
		$("#ID_trSubName").hide();
		$("#ID_trN_Link").hide();
	}  else if( bannerImgProductTextCate1.indexOf( type ) > -1 ) {
		$("#ID_trTitle").show();
		$("#ID_trT_Link").hide();
		$("#ID_trImg").show();
		$("#ID_trImg_m").show();
		$("#ID_trLink").show();
		$("#ID_RelationProduct").show();
		$("#ID_trName").hide();
		$("#ID_trSubName").hide();
		$("#ID_trN_Link").hide();
	}  else if( bannerImgProductTextCate2.indexOf( type ) > -1 ) {
		$("#ID_trTitle").show();
		$("#ID_trT_Link").show();
		$("#ID_trImg").show();
		$("#ID_trImg_m").show();
		$("#ID_trLink").show();
		$("#ID_RelationProduct").show();
		$("#ID_trName").show();
		$("#ID_trSubName").show();
		$("#ID_trSubName2").show();
		$("#ID_trN_Link").show();
	} else {
		$("#ID_trImg").show();
		$("#ID_trImg_m").show();
		$("#ID_trLink").show();
		$("#ID_trTitle").hide();
		$("#ID_trT_Link").hide();
		$("#ID_RelationProduct").hide();
		$("#ID_trName").hide();
		$("#ID_trSubName").hide();
		$("#ID_trN_Link").hide();
	}
}

function goList() {
	$('#banner_no').val('');
	$('#img_no').val('');
	$("#insertForm").submit();
}

// 배너 타입에 따른 이벤트 처리
$(document).ready(function(){
	typeChange();

	$("#in_type").on('change', function(){
		typeChange();
	});
});
</SCRIPT>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; <?=$subPage_dept2_title?> &gt;<span><?=$subPage_dept3_title?> 관리</span></p></div></div>

<form name='insertForm' id='insertForm' method='POST' enctype="multipart/form-data">
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<input type='hidden' name='mode' id='mode' value='' >
		<input type='hidden' name='banner_no' id='banner_no' value='' >
		<input type='hidden' name='img_no' id='img_no' value='<?=$mSelect['no']?>' >
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">		
        <input type="hidden" name="visible_mode" value="" />
        <input type="hidden" name="search_cate" value="<?=$search_cate?>" />
        <input type="hidden" name="search_hidden" value="<?=$search_hidden?>" />
        <input type="hidden" name="search_up_title" value="<?=$search_up_title?>" />
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3"><?=$subPage_dept3_title?> 관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span><?=$subPage_dept3_title?> 등록/수정/삭제 처리를 할 수 있습니다.</span></div>
				</td>
            </tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배너 <?=$mode_Text?></div>
				</td>
			</tr>
			<tr>
				<td>
				<?include("layer_prlistPop.php");?>
				<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					
						<col width=140></col>
						<col width=></col>
						<tr style='display: none;' >
							<th><span>배너타입</span></th>
							<TD>
								<select id="in_type" name="in_type" <? if( $mSelect['banner_no'] ) { echo 'disabled'; }  ?> >
									<option value="">=====선택해 주세요====</option>
<?php
	foreach( $menuCate as $mKey=>$mVal ){
?>
									<option value="<?=$mVal['no']?>" <? if( $mVal['no'] == $mSelect['banner_no'] ){ echo 'SELECTED'; } ?> alt='<?=$mVal['cate_use_yn']?>' ><?=$mVal['titlename']?></option>
<?php
	}
?>								
								</select>
								<input type='hidden' name='v_in_type' value='<?=$mSelect['banner_no']?>'>
							</TD>
						</tr>
						
						<tr id='ID_trCate' style='display: none;' >
							<th><span>배너 카테고리</span></th>
							<td>
<?php
	//카테고리 SELECT BOX를 불러온다
	echo $codeA_list;
	echo $codeB_list;
	echo $codeC_list;
	echo $codeD_list;
	//카테고리 SELECT 버튼을 불러온다
	//echo $codeSelect;
	//카테고리 스크립트 실행
	echo $strcodelist;
	echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";					
?>
							</td>
						</tr>

						<tr id='ID_trTitle' style='display: none;' >
							<th><span>타이틀</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_title' name='banner_title' value="<?=$mSelect['banner_title']?>"><input name = 'banner_title_color' class="jscolor" value="<?if ($mSelect['banner_title_color']) { echo $mSelect['banner_title_color']; } else { echo '000000'; }?>" size=7></TD>
						</tr>
						<tr id='ID_trT_Link' style='display: none;' >
							<th><span>타이틀 링크</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_t_link' name='banner_t_link' value="<?=$mSelect['banner_t_link']?>" ></TD>
						</tr>
						<tr id='ID_trImg' style='display: none;' >
							<th><span>배너 이미지(PC)</span></th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="banner_img[]" style="WIDTH: 400px"><br>
								<!--<span class="font_orange">(권장이미지 : )</span>-->
								<input type=hidden name="v_banner_img" value="<?=$mSelect['banner_img']?>" >
								<div style='margin-top:5px' >
<?	if( is_file($imagepath.$mSelect['banner_img']) ){ ?>
									<img src='<?=$imagepath.$mSelect['banner_img']?>' style='max-width: 125px;' />
<?	} ?>
								</div>
							</td>
						</tr>


						<tr id='ID_trImg_m' style='display: none;' >
							<th><span>배너 이미지(MOBILE)</span></th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="banner_img[]" style="WIDTH: 400px"><br>
								<!--<span class="font_orange">(권장이미지 : )</span>-->
								<input type=hidden name="v_banner_img_m" value="<?=$mSelect['banner_img_m']?>" >
								<div style='margin-top:5px' >
<?	if( is_file($imagepath.$mSelect['banner_img_m']) ){ ?>
									<img src='<?=$imagepath.$mSelect['banner_img_m']?>' style='max-width: 125px;' />
<?	} ?>
								</div>
							</td>
						</tr>
						<TR id='ID_RelationProduct' style='display: none;'>
							<th><span>관련상품</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','relationProduct');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
							<td align="left">
									<div style="margin-top:0px; margin-bottom: 0px;">							
										<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_relationProduct">	
										<input type="hidden" name="limit_relationProduct" id="limit_relationProduct" value=""/>								
											<colgroup>
												<col width=20></col>
												<col width=50></col>
												<col width=></col>
											</colgroup>
										<?foreach($thisBannerProduct as $bannerProductKey=>$bannerProduct){?>	
											<tr align="center">
												<td style='border:0px'>
													<a name="pro_upChange" style="cursor: hand;">
														<img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
													</a>
													<br>
													<a name="pro_downChange" style="cursor: hand;">
														<img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
													</a>
												</td>
												<td style='border:0px'>
													<img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bannerProduct['tinyimage']?>" border="1"/>
													<input type='hidden' name='relationProduct[]' value='<?=$bannerProduct[productcode]?>'>
												</td>
												<td style='border:0px' align="left"><?=$bannerProduct[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bannerProduct[productcode]?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
												</td>
											</tr>
										<?}?>
										</table>
									</div>
							</td>
						</TR>
						<tr id='ID_trLink' style='display: none;' >
							<th><span>링크</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_link' name='banner_link' value="<?=$mSelect['banner_link']?>" ></TD>
						</tr>
						<tr id='ID_trName' style='display: none;' >
							<th><span>기본 텍스트</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_name' name='banner_name' value="<?=$mSelect['banner_name']?>"><input name = 'banner_name_color' class="jscolor" value="<?if ($mSelect['banner_name_color']) { echo $mSelect['banner_name_color']; } else { echo '000000'; }?>" size=7></TD>
						</tr>
						<tr id='ID_trSubName' style='display: none;' >
							<th><span>선택 텍스트</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_subname' name='banner_subname' value="<?=$mSelect['banner_subname']?>"><input name = 'banner_subname_color' class="jscolor" value="<?if ($mSelect['banner_subname_color']) { echo $mSelect['banner_subname_color']; } else { echo '666666'; }?>" size=7></TD>
						</tr>
						<tr id='ID_trSubName2' style='display: none;' >
							<th><span>선택 텍스트2</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_subname2' name='banner_subname2' value="<?=$mSelect['banner_subname2']?>"><input name = 'banner_subname_color2' class="jscolor" value="<?if ($mSelect['banner_subname_color2']) { echo $mSelect['banner_subname_color2']; } else { echo '666666'; }?>" size=7></TD>
						</tr>
						<tr id='ID_trN_Link' style='display: none;' >
							<th><span>텍스트 링크</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_n_link' name='banner_n_link' value="<?=$mSelect['banner_n_link']?>" ></TD>
						</tr>
						<tr>
							<th><span>위치</span></th>
							<td>
								<select name='banner_target' >
									<option value='_blank' <? if( $mSelect['banner_target'] == '_blank' ) { echo 'SELECTED'; } ?> > 새창 </option>
									<option value='_self' <? if( $mSelect['banner_target'] == '_self' ) { echo 'SELECTED'; } ?> >현재위치</option>
								</select>
							</td>
						</tr>
						<tr>
							<th><span>기획전 선택</span></th>
                            <td>
                                <select name="promo_idx" id="promo_idx">
<?
    $sql  = "SELECT * FROM tblpromo ";
    $sql .= "WHERE display_type in ('A', 'P') AND event_type = '1' ";
    //$sql .= "AND to_date(to_char(now(), 'YYYY-MM-DD'), 'YYYY-MM-DD') between start_date and end_date ";
    $sql .= "AND to_date(to_char(now(), 'YYYY-MM-DD'), 'YYYY-MM-DD') <= end_date "; // 진행중이거나 진행예정 리스트 보이기 위해.2016-03-11 jhjeong
    $sql .= "ORDER BY rdate desc ";

    $result = pmysql_query($sql);

    $cnt = 0;
    while ($row = pmysql_fetch_object($result)) {
        // 맨 상단
        if ( $cnt == 0 ) { 
            echo "<option value=''>=========================================</option>";
        }

        $selected = "";
        if ( $row->idx === $mSelect['promo_idx'] ) {
            $selected = "selected";
        }
        echo "<option value='" . $row->idx . "' {$selected}>" . $row->title . "</option>";

        $cnt++;
    }
    pmysql_free_result($result);
?>
                                </select>
                            </td>
						</tr>
						<tr>
							<th><span>노출순서</span></th>
							<TD><INPUT maxLength=10 size=10 id='banner_sort' name='banner_sort' value="<?=$mSelect['banner_sort']?>" ></TD>
						</tr>
						<tr>
							<th><span>노출</span></th>
							<TD><INPUT type='checkbox' id='banner_hidden' name='banner_hidden' value="1" <? if( $mSelect['banner_hidden'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
						</tr>
					</table>
					
				</div>
				</td>
			</tr>
			<tr>
				<td colspan="8" align="center">
<?php
	if( $qType == '0' ){
?>
					<a href="javascript:changeAction('<?=$qType?>', '<?=$mSelect['banner_no']?>' );">
						<img src="images/btn_confirm_com.gif">	
					</a>
<?php
	} else {
?>
					<a href="javascript:javascript:changeAction('<?=$qType?>', '<?=$mSelect['banner_no']?>' );">
						<img src="images/btn_edit2.gif">
					</a>
					<a href="javascript:javascript:goList();">
						<img src="img/btn/btn_list.gif" >
					</a>
<?php
	}
?>
				</td>
			</tr>
			<tr>
				<td>
                    <table cellpadding="0" cellspacing="0" width="100%" border="0">
                    <col width="10%"></col>
                    <col width=></col>
                    <tr>
                        <td>
                            <!-- 소제목 -->
                            <div class="title_depth3_sub">검색된 목록</div>
                        </td>
                        <td align="right">
                            <div style="margin:20px 0 5px; align: left;">
                            사용 :
                            <select name="search_hidden" onChange="javascript:changeSelectHidden(this);">
                                <option value=""  <?php if ($search_hidden == "") echo "selected"; ?>>========전체=======</option>
                                <option value="1" <?php if ($search_hidden == "1") echo "selected"; ?>>노출</option>
                                <option value="0" <?php if ($search_hidden == "0") echo "selected"; ?>>비노출</option>
                            </select>

                            </div>
                        </td>
                    </tr>
                    </table>
				</td>
			</tr>
			
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
					<col width='50'>
					<col width='50'>
				<?
					if ($form_listing_type == "bannerImgCate") { // 이미지형 배너
						$list_cols='9';
						if ($form_listing_cate_type == "on") {
				?>
					<col width='150'>
					<?}?>
					<col width='150'>
					<col width='150'>
					<col width='*'>
				<?
					} else if ($form_listing_type == "bannerProductTextCate") { // 상품+텍스트형 배너
						$list_cols='9';
				?>
					<col width='200'>
					<col width='*'>
					<col width='150'>
				<?
					} else if ($form_listing_type == "bannerImgProductCate") { // 이미지+상품형 배너
						$list_cols='10';
				?>
					<col width='150'>
					<col width='150'>
					<col width='150'>
					<col width='*'>
				<?
					} else if ($form_listing_type == "bannerImgProductTextCate1") { // 이미지+상품+텍스트형1 배너
						$list_cols='11';
						if ($form_listing_cate_type == "on") {
				?>
					<col width='150'>
					<?}?>
					<col width='*'>
					<col width='150'>
					<col width='150'>
					<col width='100'>
					<col width='200'>
				<?
					} else if ($form_listing_type == "bannerImgProductTextCate2") { // 이미지+상품+텍스트형2 배너
						$list_cols='12';
						if ($form_listing_cate_type == "on") {
				?>
					<col width='150'>
					<?}?>
					<col width='*'>
					<col width='150'>
					<col width='150'>
					<col width='100'>
					<col width='200'>
					<col width='200'>
				<?}?>
					<col width='55'>
					<col width='60'>
					<col width='60'>
					<col width='60'>
					<col width='60'>
				</colgroup>
				<?
				if ($form_listing_cate_type == "on") {
					$list_cols = $list_cols+1;
				}
                $list_cols++;   // 맨 앞에 checkbox가 있어야 하므로 1증가
				?>
				<TR>
                    <th><input type="checkbox" id="allCheck" onClick="CheckAll()";></th>
					<th>번호</th>
				<?if ($form_listing_type == "bannerImgCate") { // 이미지형 배너?>
				<?		if ($form_listing_cate_type == "on") { ?>
					<th>배너 카테고리</th>
				<?		}?>
					<th>PC 이미지</th>
					<th>MOBILE 이미지</th>
					<th>링크</th>
				<?} else if ($form_listing_type == "bannerProductTextCate") { // 상품+텍스트형 배너?>
					<th>배너 카테고리</th>
					<th>타이틀</th>
					<th>관련상품수</th>
				<?} else if ($form_listing_type == "bannerImgProductCate") { // 이미지+상품형 배너?>
					<th>PC 이미지</th>
					<th>MOBILE 이미지</th>
					<th>관련상품수</th>
					<th>링크</th>
				<?} else if ($form_listing_type == "bannerImgProductTextCate1") { // 이미지+상품+텍스트형1 배너?>
				<?		if ($form_listing_cate_type == "on") { ?>
					<th>배너 카테고리</th>
				<?		}?>
					<th>타이틀</th>
					<th>PC 이미지</th>
					<th>MOBILE 이미지</th>
					<th>관련상품수</th>
					<th>링크</th>
				<?} else if ($form_listing_type == "bannerImgProductTextCate2") { // 이미지+상품+텍스트형2 배너?>
				<?		if ($form_listing_cate_type == "on") { ?>
					<th>배너 카테고리</th>
				<?		}?>
					<th>타이틀</th>
					<th>PC 이미지</th>
					<th>MOBILE 이미지</th>
					<th>관련상품수</th>
					<th>텍스트</th>
					<th>링크</th>
				<?}?>
					<th>위치</th>
					<th>노출순서</th>
					<th>사용</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
	if( count( $bannerList ) > 0 ) {
		$cnt=0;
		foreach( $bannerList as $bCnt=>$bVal ){
			$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );
			list($product_cnt) = pmysql_fetch("SELECT count(*) as product_cnt  FROM tblmainbannerimg_product WHERE tblmainbannerimg_no = '".$bVal['no']."'");
?>
				<TR>
                    <td><input type="checkbox" name="idx[]" value="<?=$bVal['no']?>"></td>
					<!-- 번호 -->
					<td>
						<?=$number?>
					</td>
	<?if ($form_listing_type == "bannerProductTextCate" || $form_listing_cate_type == "on") { ?>
					<!-- 메뉴 카테고리 -->
					<td>
<?php
			if( $bVal['banner_category'] ){
				echo $prCate[$bVal['banner_category']]['code_name'];
				//echo '<br>'.$prCate[$bVal['banner_category']]['cate_code'];

			} else {
				echo '-';
			}
?>
					</td>
	<?}?>
	<?if ($form_listing_type == "bannerProductTextCate" || $form_listing_type == "bannerImgProductTextCate1" || $form_listing_type == "bannerImgProductTextCate2") { ?>
					<!-- 타이틀 -->
					<td>
<?php
			if( strlen( $bVal['banner_title'] ) > 0 ) echo "<font color='#".$bVal['banner_title_color']."'>".$bVal['banner_title']."</font>";
			else echo '-';
?>
					</td>
	<?}?>
	<?if ($form_listing_type != "bannerProductTextCate") { ?>
					<!-- 이미지 -->
					<td>
						<div id='img_display' >
<?php
			if( is_file($imagepath.$bVal['banner_img']) ){
?>
							<img src='<?=$imagepath.$bVal['banner_img']?>' style='max-width : 125px;' >
<?php
			} else {
				echo '-';
			}
?>	
						</div>
					</td>
					<!-- 이미지 -->
					<td>
						<div id='img_display2' >
<?php
			if( is_file($imagepath.$bVal['banner_img_m']) ){
?>
							<img src='<?=$imagepath.$bVal['banner_img_m']?>' style='max-width : 125px;' >
<?php
			} else {
				echo '-';
			}
?>	
						</div>
					</td>
	<?}?>
	<?if ($form_listing_type != "bannerImgCate") { ?>
					<!-- 상품 갯수 -->
					<td>
						<?=$product_cnt?>개
					</td>
	<?}?>
	<?if ($form_listing_type == "bannerImgProductTextCate2") { ?>
					<!-- link -->
					<td>
						<? if ($bVal['banner_name']) echo "기본 텍스트 : <font color='#".$bVal['banner_name_color']."'>".$bVal['banner_name']."</font>";?>
						<? if ($bVal['banner_name'] && $bVal['banner_subname']) echo "<br>";?>
						<? if ($bVal['banner_subname']) echo "선택 텍스트 : <font color='#".$bVal['banner_subname_color']."'>".$bVal['banner_subname']."</font>";?>
					</td>
	<?}?>
	<?if ($form_listing_type != "bannerProductTextCate") { ?>
					<!-- link -->
					<td>
						<? if ($bVal['banner_link']) echo "배너 링크 : ".$bVal['banner_link'];?>
						<? if ($bVal['banner_link'] && $bVal['banner_t_link']) echo "<br>";?>
						<? if ($bVal['banner_t_link']) echo "타이틀 링크 : ".$bVal['banner_t_link'];?>
						<? if ($bVal['banner_t_link'] && $bVal['banner_n_link']) echo "<br>";?>
						<? if ($bVal['banner_n_link']) echo "텍스트 링크 : ".$bVal['banner_n_link'];?>
					</td>
	<?}?>
					<!-- 위치 -->
					<td>
						<?=$baerrTargetText[$bVal['banner_target']]?>
					</td>
					<!-- 노출 순서 -->
					<td>
						<?=$bVal['banner_sort']?>
					</td>
					<!-- 노출 / 비노출 -->
					<td>
						<?=$display[$bVal['banner_hidden']]?>
					</td>
					<!-- 수정 -->
					<td>
						<a href="javascript:changeAction( '2' ,'<?=$bVal["no"]?>' );"><img src="images/btn_edit.gif"></a>
					</td>
					<!-- 삭제 -->
					<td>
						<a href="javascript:changeAction('3', '<?=$bVal["no"]?>' );"><img src="images/btn_del.gif"></a>
					</td>
				</TR>
<?php
			$cnt++;
		}
	} else {
?>
				<TR>
					<td colspan='<?=$list_cols?>' > 목록이 존재하지 않습니다.</td>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
<?	if( count( $bannerList ) > 0 ) { ?>
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
<?	} ?>
					</div>
				</div>

				</td>
			</tr>

            <tr>
                <td height="20">&nbsp;</td>
            </tr>

            <tr>
                <td colspan="8" align="center">
                    <a href="javascript:changeVisible('1');">
                        <img src="images/btn_visible_set.png">
                    </a>
                    <a href="javascript:changeVisible('0');">
                        <img src="images/btn_visible_unset.png">
                    </a>
                </td>
            </tr>

            <tr>
                <td height="20">&nbsp;</td>
            </tr>
			
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>  정보</span></dt>
							<dd>
							- <b>번호</b> : <span style="letter-spacing:-0.5pt;">.</span><br>
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</form>

<script type="text/javascript">
    function changeSelectHidden(obj) {
        var hiddenVal = $(obj).children("option:selected").val();

        document.insertForm.search_hidden.value = hiddenVal;
        document.insertForm.submit();

/*
        location.href = "<?=$_SERVER['PHP_SELF']?>" + 
            "?no=<?=$mSelect['banner_no']?>" + 
            "&search_hidden=" + hiddenVal;
*/
    }

    function CheckAll() {
        if($("#allCheck").prop("checked")) {
            $("input[name='idx[]']").prop("checked",true);
        } else {
            $("input[name='idx[]']").prop("checked",false);
        }
    }

</script>

<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<?=$onload?>
<?php
include("copyright.php");
