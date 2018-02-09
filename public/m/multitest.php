<?php


$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");
include_once($Dir."lib/product.class.php");
$product = new PRODUCT();
function merror($msg) {
    $msg = str_replace("'", "\'", $msg);
    header("Content-Type: text/html; charset=euc-kr");
    echo <<<HTML
        <script type="text/javascript">
        alert('{$msg}');
        history.back(-1);
        </script>
HTML;
    exit;
}

$pridx=$_REQUEST["pridx"];
$mode=$_POST["mode"];
/*
if (!$pridx || !is_numeric($pridx)) {
    header("Location:index.php");
    exit;
}
*/
$sql = "SELECT a.* ";
//$sql.= "FROM tblproduct AS a ";
$sql.= "FROM view_tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.= "WHERE a.pridx='".$pridx."' AND a.display='Y' ";
$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
$result=pmysql_query($sql,get_mdb_conn());
if (!$result) {
    merror("시스템 오류가 발생했습니다. 잠시 후 다시 시도해주세요.");
    exit;
}
$_pdata=pmysql_fetch_object($result);
pmysql_free_result($result);
//exdebug($_pdata);
if (!$_pdata) {
    merror("상품이 삭제되었거나 진열중인 상품이 아닙니다.");
    exit;
}

$productcode=$_pdata->productcode;
$groupPriceList = $product->getProductGroupPrice($productcode);
if ($groupPriceList) { // 일반 및 도매회원 금액 세팅시 로그인 되어잇는 user 등급에 따라 판매 금액 적용
	$_pdata->sellprice = $groupPriceList[sellprice];
	$_pdata->consumerprice = $groupPriceList[consumerprice];
	$_pdata->consumer_reserve = $groupPriceList[consumer_reserve];
}

//환율적용
$_pdata->sellprice = exchageRate($_pdata->sellprice);
$_pdata->consumerprice = exchageRate($_pdata->consumerprice);

$option1Arr;$option2Arr;$optionState = "N";
$staticDeliType = "0";
$deliState_ = $product->getDeliState($_pdata);

$item_deli_price = $_pdata->deli_price; // 아이템별 배송비 금액
$shop_deli_price = $_data->deli_basefee; // 전체 배송비 설정 금액
$shop_constant_deli_price = $_data->deli_miniprice; // 얼마 이상 무료 기준 금액
$deli_state = $deliState_[itemState];
//$deli_tpye = $_pdata->deli.$_data_deli;

$deli_tpye_common = "";
$deli_tpye_item = "";
$deli_price = 0;
switch ($deli_state) {
	case "1" : $deli_price = $item_deli_price;
		break;
	case "2" : $deli_price = $item_deli_price;
		break;
	case "3" : $deli_price = 0;
		break;
	case "4" : $deli_price = 0;
		break;
	case "5" : $deli_price = 0;
		break;
	case "6" : $deli_price = 0;
		break;
	case "7" : $deli_price = $shop_deli_price;
		break;
	case "8" : $deli_price = $shop_deli_price;
		break;
	default : $deli_price = 0;
}

list($review_ordercode_cnt)=pmysql_fetch("select count(*) from tblorderproduct a JOIN tblorderinfo b on a.ordercode = b.ordercode where a.productcode='".$productcode."' AND b.id = '".$_ShopInfo->memid."'");
if(!$_ShopInfo->memid) $review_ordercode_cnt = 0;

$code=substr($productcode,0,12);

$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$likecode=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;

//$sql = "SELECT * FROM tblproductcode WHERE code_a='{$codeA}' AND code_b='{$codeB}' AND code_c='{$codeC}' AND code_d='{$codeD}' ";
//$result=pmysql_query($sql,get_mdb_conn());

$sql = "
	SELECT
	a.*,b.c_maincate
	FROM tblproductcode a
	,tblproductlink b
	WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
	AND group_code = ''
	AND c_productcode = '{$productcode}'
";
//exdebug($sql);
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)){
	if($row->c_maincate == 1){
		$mainCate = $row;
	}
	$cateProduct[] = $row;
}

//if($row=pmysql_fetch_object($result)) {
	//$_cdata=$row;
if($cateProduct) {
		if($mainCate) $_cdata=$mainCate;
		else $_cdata=$cateProduct[0];
/*
	if($row->group_code=="NO") {	//숨김 분류
		merror("판매가 종료된 상품입니다.");
		exit;
	} else if($row->group_code=="ALL" && strlen($_MShopInfo->getMemid())==0) {	//회원만 접근가능
		Header("Location:login.php?chUrl=".getUrl());
		exit;
	} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_MShopInfo->getMemgroup()) {	//그룹회원만 접근
		merror("해당 분류의 접근 권한이 없습니다.");
		exit;
	}
*/
	if(count($cateProduct)==0 || !$cateProduct){
		$group_sql = "
			SELECT
			a.group_code
			FROM tblproductcode a
			,tblproductlink b
			WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
			AND group_code != ''
			AND c_productcode = '{$productcode}'
			GROUP BY a.group_code
		";
		$gruop_res = pmysql_query($group_sql,get_db_conn());
		while($gruop_row = pmysql_fetch_object($gruop_res)){
			if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
				Header("Location:"."login.php?chUrl=".getUrl());
				exit;
			}else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
				alert_go('해당 분류의 접근 권한이 없습니다.',-1);
			}
		}
		alert_go('판매가 종료된 상품입니다.',"index.php");
	}
} else {
	merror("해당 분류가 존재하지 않습니다.");
	exit;
}
pmysql_free_result($result);


$_pdata->reserve=getReserveConvert($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");

if(preg_match("/^\[OPTG\d{4}\]$/",$_pdata->option1)){
	$optcode = substr($_pdata->option1,5,4);
	$_pdata->option1="";
	$_pdata->option_price="";
}

$opt_list=array();
if(strlen($_pdata->option1)>0) {
	$values = explode(",", $_pdata->option1);
	$opt_price=explode(",", $_pdata->option_price);
	$opt_c_price=explode(",", $_pdata->option_c_price);
	$option["name"] = array_shift($values);
	$option["values"] = $values;
	$option["price"]=$opt_price;
	$option["consumer"]=$opt_c_price;

	$opt_list[] = $option;

	if(strlen($_pdata->option2)>0) {
		$values = explode(",", $_pdata->option2);
		$option["name"] = array_shift($values);
		$option["values"] = $values;
		$opt_list[] = $option;
	}

} else if(strlen($optcode)>0) {
	$sql = "SELECT * FROM tblproductoption WHERE option_code='".$optcode."' ";
	$result = pmysql_query($sql,get_mdb_conn());
	if($row = pmysql_fetch_object($result)) {
		$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
		$opti=0;
		$option_choice = $row->option_choice;
		$exoption_choice = explode("",$option_choice);
		while(strlen($optionadd[$opti])>0) {
			$option=array();
			$opval = str_replace('"','',explode("",$optionadd[$opti]));
			$option["values"][]="--- ".$opval[0].($exoption_choice[$opti]==1?"(필수)":"(선택)")." ---";
			$opcnt=count($opval);
/*			for($j=1;$j<$opcnt;$j++) {
				$exop = str_replace('"','',explode(",",$opval[$j]));
				if($exop[1]>0) $option["values"][]=$exop[0]."(+".$exop[1]."원)";
				else if($exop[1]==0) $option["values"][]=$exop[0];
				else $option["values"][]=$exop[0]."(".$exop[1]."원)";
			}*/
			for($j=1;$j<$opcnt;$j++) {
				$exop = str_replace('"','',explode(",",$opval[$j]));
				if($exop[1]>0){ $option["values"][]=$exop[0]."(+".$exop[1]."원)"; $option["p"][]=$exop[1];}
				else if($exop[1]==0){ $option["values"][]=$exop[0]; $option["p"][]=$exop[0];}
				else {$option["values"][]=$exop[0]."(".$exop[1]."원)"; $option["p"][]=$exop[1];}
			}
			$opti++;

			$opt_list[] = $option;
		}
	}
	pmysql_free_result($result);
}

//리뷰관련 환경 설정
$reviewlist=$_data->ETCTYPE["REVIEWLIST"];
$reviewdate=$_data->ETCTYPE["REVIEWDATE"];
if(ord($reviewlist)==0) $reviewlist="N";

//리뷰등록
if($mode=="review_write") {
	function ReviewFilter($filter,$memo,&$findFilter) {
		$use_filter = explode(",",$filter);
		$isFilter = false;
		for($i=0;$i<count($use_filter);$i++) {
			if (preg_match("/{$use_filter[$i]}/i",$memo)) {
				$findFilter = $use_filter[$i];
				$isFilter = true;
				break;
			}
		}
		return $isFilter;
	}

	$rname=$_POST["rname"];
	$rcontent=$_POST["rcontent"];
	$rmarks=$_POST["rmarks"];
	$rblog=$_POST["rblog"];
	$rsubject=$_POST["rsubject"];
	$review_ordercode=$_POST["review_ordercode"];

	list($review_cnt)=pmysql_fetch("select count(*) from TBLPRODUCTREVIEW where id = '".$_ShopInfo->getMemid()."' AND productcode='".$productcode."'");
	if($review_cnt > 0){
		alert_go('이미 후기를 작성하였습니다.', "{$_SERVER['HTTP_REFERER']}");
	}

	$imagepath=$Dir.DataDir."shopimages/board/reviewbbs/";
	$userfile = $_FILES["rfile"];
	if ($userfile['tmp_name']) {
		$ext = strtolower(pathinfo($userfile[name], PATHINFO_EXTENSION));
		if(in_array($ext,array('gif','jpg','jpeg'))) {
			$uploadFile = time().".".$ext;
			move_uploaded_file($userfile['tmp_name'], $imagepath.$uploadFile);
			chmod($imagepath.$uploadFile,0664);
		} else {
			alert_go("gif와  jpg타입의 이미지만 업로드 가능합니다.", "{$_SERVER['HTTP_REFERER']}");
			$uploadFile = "";
		}
		$addFileColumn = ", upfile";
		$addFileData = ", '".$uploadFile."'";
	}
	if ($_FILES['rfile']["size"] > 838860){
		alert_go('해당 파일은 800K를 초과합니다.', "{$_SERVER['HTTP_REFERER']}");
	}

	if((strlen($_ShopInfo->getMemid())==0) && $_data->review_memtype=="Y") {
		alert_go('로그인을 하셔야 사용후기 등록이 가능합니다.',"login.php?chUrl=".getUrl());
	}
	if(ord($review_filter)) {
		if(ReviewFilter($review_filter,$rcontent,$findFilter)) {
			alert_go("사용하실 수 없는 단어를 입력하셨습니다.({$findFilter})\\n\\n다시 입력하시기 바랍니다.", "{$_SERVER['HTTP_REFERER']}");
		}
	}

	$sql = "
				INSERT INTO TBLPRODUCTREVIEW
				(
					productcode ,
					id ,
					name ,
					marks ,
					date ,
					content ,
					ordercode,
					blog_url,
					subject ".$addFileColumn."
				) VALUES (
					'{$productcode}',
					'".$_ShopInfo->getMemid()."',
					'{$rname}',
					'{$rmarks}',
					'".date("YmdHis")."',
					'{$rcontent}',
					'".$review_ordercode."',
					'".$rblog."',
					'".$rsubject."' ".$addFileData."
				)";
	pmysql_query($sql,get_db_conn());
	if($_data->review_type=="A") $msg="관리자 인증후 등록됩니다.";
	else $msg="리뷰가 등록되었습니다.";
	alert_go($msg,"{$_SERVER["HTTP_REFERER"]}");
}

//Q&A등록
if($mode == 'qna_write'){
	$board = 'qna';
	$up_name = $_POST['up_name'];
	$up_passwd = $_POST['up_passwd'];
	$up_email = $_POST['up_email'];
	$up_subject = $_POST['up_subject'];
	$up_memo = $_POST['up_memo'];

	$up_name = addslashes($up_name);
	$up_subject = str_replace("<!","&lt;!",$up_subject);
	$up_subject = addslashes($up_subject);
	$up_memo = str_replace("<!","&lt;!",$up_memo);
	$up_memo = addslashes($up_memo);

	$que2 = "SELECT MIN(thread) FROM tblboard ";
	$result = pmysql_query($que2,get_db_conn());
	$row = pmysql_fetch_array($result);
	if ($row[0]<=0) {
		$thread = 999999999;
	} else {
		$thread = $row[0] - 1;
	}
	pmysql_free_result($result);

	//해당 쇼핑몰 모든 게시판 thread값 동일하게 업데이트 (통합되어 보여질 때 유일thread값을 갖게하기 위하여)
	@pmysql_query("UPDATE tblboardadmin SET thread_no='".$thread."' ",get_db_conn());

	$que3 = "SELECT MAX(num) FROM tblboard WHERE board='".$board."' AND pos=0 AND deleted!='1'";
	$result3 = pmysql_query($que3,get_db_conn());
	$row3 = pmysql_fetch_array($result3);
	@pmysql_free_result($result3);
	$next_no = $row3[0];

	if (!$next_no) $next_no = 0;

/*	if(ProcessBoardFileIn($board,$up_filename)!="SUCCESS") {
		$up_filename="";
	}
*/
	$sql = "INSERT INTO tblboard DEFAULT VALUES RETURNING num";
	$row = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));

	$sql  = "UPDATE tblboard SET ";
	$sql .= "board				= '".$board."', ";
	$sql .= "thread				= '".$thread."', ";
	$sql .= "pos				= '0', ";
	$sql .= "depth				= '0', ";
	$sql .= "prev_no			= '0', ";
	if(strlen($pridx)>0) {
		$sql.= "pridx			= '".$pridx."', ";
	}
	$sql .= "next_no			= '".$next_no."', ";
	$sql .= "name				= '".$up_name."', ";
	$sql .= "passwd				= '".$up_passwd."', ";
	$sql .= "email				= '".$up_email."', ";
	$sql .= "is_secret			= '".$up_is_secret."', ";
	$sql .= "use_html			= '".$up_html."', ";
	$sql .= "title				= '".$up_subject."', ";
	$sql .= "filename			= '".$up_filename."', ";
	$sql .= "writetime			= '".time()."', ";
	$sql .= "ip					= '".$_SERVER['REMOTE_ADDR']."', ";
	$sql .= "access				= '0', ";
	$sql .= "total_comment		= '0', ";
	$sql .= "content			= '".$up_memo."', ";
	$sql .= "notice				= '0', ";
	if($_ShopInfo->memid)$sql .= "mem_id				= '".$_ShopInfo->memid."', ";
	$sql .= "deleted			= '0' WHERE num={$row[0]}";
	$insert = pmysql_query($sql,get_db_conn());

	if($row[0]>0) {
		$thisNum = $row[0];

		if ($next_no) {
			$qry9 = "SELECT thread FROM tblboard WHERE board='$board' AND num='$next_no' ";
			$res9 = pmysql_query($qry9,get_db_conn());
			$next_thread = pmysql_fetch_row($res9);
			@pmysql_free_result($res9);
			pmysql_query("UPDATE tblboard SET prev_no='{$thisNum}' WHERE board='{$board}' AND thread = '{$next_thread[0]}'",get_db_conn());

			pmysql_query("UPDATE tblboard SET prev_no='{$thisNum}' WHERE board='{$board}' AND num = '{$next_no}'",get_db_conn());
		}

		// ===== 관리테이블의 게시글수 update =====
		$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1, max_num='$thisNum' ";
		$sql3.= "WHERE board='$board' ";
		$update = pmysql_query($sql3,get_db_conn());
	}


	$msg="Q&A가 등록되었습니다.";
	alert_go($msg,"{$_SERVER["HTTP_REFERER"]}");
}

if($data->review_type !='N'){
	//리뷰카운트
	/*
	$reviewsql = "SELECT * FROM tblproductreview WHERE productcode = '".$_pdata->productcode."' ORDER BY num desc ";
	$reviewres = pmysql_query($reviewsql,get_mdb_conn());
	$reviewcount = pmysql_num_rows($reviewres );
	*/

	list($reviewcount, $totmarks)=pmysql_fetch("SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview WHERE productcode='".$_pdata->productcode."'");
	if($reviewcount > 0){
		$marks = ceil($totmarks/$reviewcount);
	}else{
		$marks = 0;
	}
	//리뷰리스트
	$reviewNum_sql = "SELECT COUNT(*) AS cnt FROM tblproductreview WHERE productcode = '".$_pdata->productcode."'";
	$reviewNum_res = pmysql_query($reviewNum_sql,get_mdb_conn());
	$reviewNum_row = pmysql_fetch_object($reviewNum_res);
	$reviewlist_num = 5;
	$reviewtotalpage = ceil($reviewcount / $reviewlist_num);
	$reviewsql = "SELECT * FROM tblproductreview WHERE productcode = '".$_pdata->productcode."' ORDER BY num desc limit $reviewlist_num";
	$reviewres = pmysql_query($reviewsql,get_mdb_conn());
	while($reviewrow = pmysql_fetch_object($reviewres)){
		/*
		$regdt = substr($reviewrow->date,0,4).".";
		$regdt .= substr($reviewrow->date,4,2).".";
		$regdt .= substr($reviewrow->date,6,2);
		$reviewrow->date = $regdt;
		*/
		$reviewloop[] = $reviewrow;
	}
}else{
	$reviewcount = 0;
}

//Q&A카운트
$qnasql = "SELECT * FROM tblboard WHERE board = 'qna' and pridx = '".$pridx."' and pos = '0' and depth = '0' ORDER BY num desc ";
$qnares = pmysql_query($qnasql,get_mdb_conn());
$qnacount = pmysql_num_rows($qnares );

//Q&A리스크
$qnalist_num = 5;
$qnatotalpage = ceil($qnacount / $qnalist_num);
$qnasql = "SELECT * FROM tblboard WHERE board = 'qna' and pridx = '".$pridx."' and pos = '0' and depth = '0' ORDER BY num desc limit $qnalist_num ";
$qnares = pmysql_query($qnasql,get_mdb_conn());
while($qnarow = pmysql_fetch_object($qnares)){
	$writetime = date("Y-m-d", $qnarow->writetime);
	$qnarow->writetime = $writetime;
	$qnaloop[] = $qnarow;
}


//입점업체 정보 관련
if($_pdata->vender>0) {
	$sql = "SELECT a.vender, a.id, a.brand_name, a.deli_info, b.prdt_cnt ";
	$sql.= "FROM tblvenderstore a, tblvenderstorecount b ";
	$sql.= "WHERE a.vender='{$_pdata->vender}' AND a.vender=b.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vdata=pmysql_fetch_object($result)) {
		$_pdata->vender=0;
	}
	pmysql_free_result($result);
}

//배송/교환/환불정보 노출
$deli_info="";
if($deliinfono!="Y") {	//개별상품별 배송/교환/환불정보 노출일 경우
	$deli_info_data="";
	if($_pdata->vender>0) {	//입점업체 상품이면 입점업체 배송/교환/환불정보 누출
		$deli_info_data=$_vdata->deli_info;
		$aboutdeliinfofile=$Dir.DataDir."shopimages/vender/aboutdeliinfo_{$_vdata->vender}.gif";
	} else {
		$deli_info_data=$_data->deli_info;
		$aboutdeliinfofile=$Dir.DataDir."shopimages/etc/aboutdeliinfo.gif";
	}
	if(ord($deli_info_data)) {
		$tempdeli_info=explode("=", stripslashes($deli_info_data));
		if($tempdeli_info[0]=="Y") {
			if($tempdeli_info[1]=="TEXT") {			//텍스트형
				$allowedTags = "<h1><b><i><a><ul><li><pre><hr><blockquote><u><img><br><font>";

				if(ord($tempdeli_info[2]) || ord($tempdeli_info[3])) {
					if(ord($tempdeli_info[2])) {	//배송정보 텍스트
						$deli_info.= "	<dl class='delivery_info'><dd>".nl2br(strip_tags($tempdeli_info[2],$allowedTags))."</dd></dl>\n";
					}
					if(ord($tempdeli_info[3])) {	//교환/환불정보 텍스트
						$deli_info.= "	<dl class='delivery_info'><dd>".nl2br(strip_tags($tempdeli_info[3],$allowedTags))."</dd></dl>\n";
					}
				}
			} else if($tempdeli_info[1]=="IMAGE") {	//이미지형
				if(file_exists($aboutdeliinfofile)) {
					$deli_info = "<img src=\"{$aboutdeliinfofile}\" align=absmiddle border=0>\n";
				}
			} else if($tempdeli_info[1]=="HTML") {	//HTML로 입력
				if(ord($tempdeli_info[2])) {
					$deli_info = "{$tempdeli_info[2]}\n";
				}
			}
		}
	}
}

$mi_sql="SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
$mi_result=pmysql_query($mi_sql,get_db_conn());
$mi_row=pmysql_fetch_object($mi_result);
exdebug($mi_row);
if(!$mi_row->primg04) echo "데이터없음";
$mi_imgs=array(&$mi_row->primg01,&$mi_row->primg02,&$mi_row->primg03,&$mi_row->primg04,&$mi_row->primg05,&$mi_row->primg06,&$mi_row->primg07,&$mi_row->primg08,&$mi_row->primg09,&$mi_row->primg10);




//상품다중이미지 확인
$multi_img="N";
$sql2 ="SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
$result2=pmysql_query($sql2,get_db_conn());
if($row2=pmysql_fetch_object($result2)) {

	if($_data->multi_distype=="0") {
		$multi_img="I";
	} else if($_data->multi_distype=="1") {
		$multi_img="Y";
		$multi_imgs=array(&$row2->primg01,&$row2->primg02,&$row2->primg03,&$row2->primg04,&$row2->primg05,&$row2->primg06,&$row2->primg07,&$row2->primg08,&$row2->primg09,&$row2->primg10);
		$thumbcnt=0;
		for($j=0;$j<10;$j++) {
			if(ord($multi_imgs[$j])) {
				$thumbcnt++;
			}
		}
		$multi_height=430;
		$thumbtype=1;
		if($thumbcnt>5) {
			$multi_height=490;
			$thumbtype=2;
		}
	}
}

pmysql_free_result($result2);

/*if($multi_img=="Y") {

	$imagepath=$Dir.DataDir."shopimages/multi/";
	//$dispos=$row->multi_dispos;
	$changetype=$_data->multi_changetype;
	$bgcolor=$_data->multi_bgcolor;

	$sql = "SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$multi_imgs=array(&$row->primg01,&$row->primg02,&$row->primg03,&$row->primg04,&$row->primg05,&$row->primg06,&$row->primg07,&$row->primg08,&$row->primg09,&$row->primg10);

		$tmpsize=explode("",$row->size);
		$insize="";
		$updategbn="N";

		$y=0;
		for($i=0;$i<10;$i++) {
			if(ord($multi_imgs[$i])) {
				$yesimage[$y]=$multi_imgs[$i];
				if(ord($tmpsize[$i])==0) {
					$size=getimagesize($Dir.DataDir."shopimages/multi/".$multi_imgs[$i]);
					$xsize[$y]=$size[0];
					$ysize[$y]=$size[1];
					$insize.="{$size[0]}X".$size[1];
					$updategbn="Y";
				} else {
					$insize.="".$tmpsize[$i];
					$tmp=explode("X",$tmpsize[$i]);
					$xsize[$y]=$tmp[0];
					$ysize[$y]=$tmp[1];
				}
				$y++;
			} else {
				$insize.="";
			}
		}

		$makesize=$maxsize;
		for($i=0;$i<$y;$i++){
			if($xsize[$i]>$makesize || $ysize[$i]>$makesize) {
				if($xsize[$i]>=$ysize[$i]) {
					$tempxsize=$makesize;
					$tempysize=($ysize[$i]*$makesize)/$xsize[$i];
				} else {
					$tempxsize=($xsize[$i]*$makesize)/$ysize[$i];
					$tempysize=$makesize;
				}
				$xsize[$i]=$tempxsize;
				$ysize[$i]=$tempysize;
			}
		}

		pmysql_free_result($result);
	}
}*/

if(strlen($productcode)==18) {
	$viewproduct=$_COOKIE["ViewProduct"];
	if(ord($viewproduct)==0 || strpos($viewproduct,",{$productcode},")===FALSE) {
		if(ord($viewproduct)==0) {
			$viewproduct=",{$productcode},";
		} else {
			$viewproduct=",".$productcode.$viewproduct;
		}
	} else {
		$viewproduct=str_replace(",{$productcode}","",$viewproduct);
		$viewproduct=",".$productcode.$viewproduct;
	}
	$viewproduct=substr($viewproduct,0,571);
	setcookie("ViewProduct",$viewproduct,0,"/".RootPath);
}

// 패키지 선택 출력
$arrpackage_title=array();
$arrpackage_list=array();
$arrpackage_price=array();
$arrpackage_pricevalue=array();
if((int)$_pdata->package_num>0) {
	$sql = "SELECT * FROM tblproductpackage WHERE num='".(int)$_pdata->package_num."' ";
	$result = pmysql_query($sql,get_db_conn());
	$package_count=0;
	if($row = @pmysql_fetch_object($result)) {
		$package_type=$row->package_type;
		pmysql_free_result($result);
		if(strlen($row->package_title)>0) {
			$arrpackage_title = explode("",$row->package_title);
			$arrpackage_list = explode("",$row->package_list);
			$arrpackage_price = explode("",$row->package_price);

			$package_listrep = str_replace("","",$row->package_list);

			if(strlen($package_listrep)>0) {
				$sql = "SELECT pridx,productcode,productname,sellprice,maximage,quantity,etctype FROM tblproduct ";
				$sql.= "WHERE pridx IN ('".str_replace(",","','",trim($package_listrep,','))."') ";
				$sql.= "AND assembleuse!='Y' ";
				$sql.= "AND display='Y' ";
				$result2 = pmysql_query($sql,get_db_conn());
				while($row2 = @pmysql_fetch_object($result2)) {
					$arrpackage_proinfo[productcode][$row2->pridx] = $row2->productcode;
					$arrpackage_proinfo[productname][$row2->pridx] = $row2->productname;
					$arrpackage_proinfo[sellprice][$row2->pridx] = $row2->sellprice;
					$arrpackage_proinfo[maximage][$row2->pridx] = $row2->maximage;
					$arrpackage_proinfo[quantity][$row2->pridx] = $row2->quantity;
					$arrpackage_proinfo[etctype][$row2->pridx] = $row2->etctype;
				}
				@pmysql_free_result($result2);
			}

			for($t=1; $t<count($arrpackage_list); $t++) {
				$arrpackage_pricevalue[0]=0;
				$arrpackage_pricevalue[$t]=0;
				if(strlen($arrpackage_list[$t])>0) {
					$arrpackage_list_exp = explode(",",$arrpackage_list[$t]);
					$sumsellprice=0;
					for($tt=0; $tt<count($arrpackage_list_exp); $tt++) {
						$sumsellprice += (int)$arrpackage_proinfo[sellprice][$arrpackage_list_exp[$tt]];
					}

					if((int)$sumsellprice>0) {
						$arrpackage_pricevalue[$t]=(int)$sumsellprice;
						if(strlen($arrpackage_price[$t])>0) {
							$arrpackage_price_exp = explode(",",$arrpackage_price[$t]);
							if(strlen($arrpackage_price_exp[0])>0 && $arrpackage_price_exp[0]>0) {
								$sumsellpricecal=0;
								if($arrpackage_price_exp[1]=="Y") {
									$sumsellpricecal = ((int)$sumsellprice*$arrpackage_price_exp[0])/100;
								} else {
									$sumsellpricecal = $arrpackage_price_exp[0];
								}
								if($sumsellpricecal>0) {
									if($arrpackage_price_exp[2]=="Y") {
										$sumsellpricecal = $sumsellprice-$sumsellpricecal;
									} else {
										$sumsellpricecal = $sumsellprice+$sumsellpricecal;
									}
									if($sumsellpricecal>0) {
										if($arrpackage_price_exp[4]=="F") {
											$sumsellpricecal = floor($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										} else if($arrpackage_price_exp[4]=="R") {
											$sumsellpricecal = round($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										} else {
											$sumsellpricecal = ceil($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										}
										$arrpackage_pricevalue[$t]=$sumsellpricecal;
									}
								}
							}
						}
					}
				}
				$propackage_option.= "<option value=\"".$t."\">".$arrpackage_title[$t]."</option>\n";
				$package_count++;
			}
		}
	}
}

$miniq = 1;
if (ord($_pdata->etctype)) {
	$etctemp = explode("",$_pdata->etctype);
	for ($i=0;$i<count($etctemp);$i++) {

		if (strpos($etctemp[$i],"MINIQ=")===0)			$miniq=substr($etctemp[$i],6);
		if (strpos($etctemp[$i],"MAXQ=")===0)			$maxq=substr($etctemp[$i],5);
		if (strpos($etctemp[$i],"DELIINFONO=")===0)	$deliinfono=substr($etctemp[$i],11);
	}
}

//[saveheels] [FITFLOP] 핏플랍 14AW/ 팝 발레리나_퓨터 http://www.saveheels.com/m/goods/view.php?goodsno=1834
//http://twitter.com/home?status=%5Bsaveheels%5D+%5BFITFLOP%5D+%ED%95%8F%ED%94%8C%EB%9E%8D+13AW%2F%EB%AC%B5%EB%A3%A9+%EB%AA%A9+%EB%A0%88%EB%8D%94_%ED%84%B0%ED%8B%80%EA%B7%B8%EB%A6%B0%0Dhttp%3A%2F%2Fnexolve.ajashop.co.kr%2Fm%2Fproductdetail.php%3Fpridx%3D14627
/*
$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', "[saveheels] [FITFLOP] 핏플랍 14AW/ 팝 발레리나_퓨터 http://www.saveheels.com/m/goods/view.php?goodsno=1834"));
$twitterurl = 'http://twitter.com/home?status='.$encodedMsg;
*/


if($_REQUEST["pridx"]){
	list($sns_productname, $sns_maximage)=pmysql_fetch("SELECT productname, maximage FROM tblproduct WHERE pridx='".$_REQUEST["pridx"]."'");


	if($multi_img=="Y") {

		$facebook_imagepath = 'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/multi/";

		$facebook_sql = "SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
		$facebook_result=pmysql_query($facebook_sql,get_db_conn());
		if($facebook_row=pmysql_fetch_object($facebook_result)) {
			$facebook_multi_imgs=array(&$facebook_row->primg01,&$facebook_row->primg02,&$facebook_row->primg03,&$facebook_row->primg04,&$facebook_row->primg05,&$facebook_row->primg06,&$facebook_row->primg07,&$facebook_row->primg08,&$facebook_row->primg09,&$facebook_row->primg10);
			$facebook_y=0;
			$facebook_insize="";
			for($facebook_i=0;$facebook_i<10;$facebook_i++) {
				if(ord($facebook_multi_imgs[$facebook_i])) {
					$facebook_image[$facebook_y] = $facebook_imagepath.$facebook_multi_imgs[$facebook_i];
					$facebook_y++;
				} else {
					$facebook_insize.="";
				}
			}
			pmysql_free_result($facebook_result);
		}
	}else{
		$facebook_image[0] = 'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/product/".$sns_maximage;
	}
	$facebook_msg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER[REQUEST_URI];
	$facebookurl = 'http://www.facebook.com/sharer.php?u='.urlencode($facebook_msg.'&time='.time());
	$facebook_meta1 = "<meta property='og:title' content='[".$_data->shopname."] ".$sns_productname."]'/>";
	$facebook_meta2 = "<meta property='og:description' content='[".$_data->shopname."] ".$sns_productname."]'/>";
	$facebook_meta3 = "<meta property='og:image' content='".$facebook_image[0]."' />";


	$tw_msg = "[".$_data->shopname."] {productname} ".'http://'.$_SERVER['HTTP_HOST'].$_SERVER[REQUEST_URI];
	$tw_goodsnm = $sns_productname;
	if ($tw_msg_length <= 140) $tw_goodsnm = mb_substr($_pdata->productname, 0, 140 - $tw_msg_length);
	$tw_msg = preg_replace('/{productname}/i', $tw_goodsnm, $tw_msg);
	$tw_encodedMsg = urlencode(iconv('EUC-KR', 'UTF-8//IGNORE', $tw_msg));
	$tw_twitterurl = 'http://twitter.com/home?status='.$tw_encodedMsg;
}

# 카카오
$Port = ($_SERVER[SERVER_PORT] == 80)? "":$_SERVER[SERVER_PORT];
if (strlen($Port)>0) $Port = ":".$Port;
$linkURL = 'http://'.$_SERVER[HTTP_HOST].$Port.$_SERVER[REQUEST_URI];
$msg_kakao1 = $_pdata->productname;
$msg_kakao2 = $linkURL;
$msg_kakao3 = 'fitflop';
$server_host = $_SERVER[HTTP_HOST];


# 페이스북
$facebookButtonUrl = $facebookurl;


# 트위터
$twitterButtobUrl = $tw_twitterurl;


$ardollar=explode(",",$_data->ETCTYPE["DOLLAR"]);

if(ord($ardollar[1])==0 || $ardollar[1]<=0) $ardollar[1]=1;






# 쿠폰 다운로드 최근 날짜 1장 노출
$couponDownLoadFlag = false;
$goods_sale_type = "";
$goods_sale_money = "";
$goods_amount_floor = "";
$goods_sale_max_money = "";
if($_data->coupon_ok=="Y") {
	$goods_cate_sql = "SELECT * FROM tblproductlink WHERE c_productcode = '".$productcode."'";
	$goods_cate_result = pmysql_query($goods_cate_sql,get_db_conn());
	$categorycode = array();
	while($goods_cate_row=pmysql_fetch_object($goods_cate_result)) {
		list($cate_a, $cate_b, $cate_c, $cate_d) = sscanf($goods_cate_row->c_category,'%3s%3s%3s%3s');
		$categorycode[] = $cate_a;
		$categorycode[] = $cate_a.$cate_b;
		$categorycode[] = $cate_a.$cate_b.$cate_c;
		$categorycode[] = $cate_a.$cate_b.$cate_c.$cate_d;
	}
	if(count($categorycode) > 0){
		$addCategoryQuery = "('".implode("', '", $categorycode)."')";
	}else{
		$addCategoryQuery = "('')";
	}

	$sql = "SELECT a.* FROM tblcouponinfo a ";
	$sql .= "LEFT JOIN tblcouponproduct c on a.coupon_code=c.coupon_code ";
	$sql .= "LEFT JOIN tblcouponcategory d on a.coupon_code=d.coupon_code ";
	if($_pdata->vender>0) {
		$sql .= "WHERE (a.vender='0' OR a.vender='{$_pdata->vender}') ";
	} else {
		$sql .= "WHERE a.vender='0' ";
	}
	$sql .= "AND a.display='Y' AND a.issue_type='Y' AND a.detail_auto='Y' AND a.coupon_type='1' ";
	$sql .= "AND (a.date_end>'".date("YmdH")."' OR a.date_end='') ";
	$sql .= "AND ((a.use_con_type2='Y' AND a.productcode = 'ALL') OR ((a.use_con_type2='Y' AND a.productcode != 'ALL') AND (c.productcode = '".$productcode."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y')))) ";
	$sql .= "AND mod(sale_type::int , 2) = '0' ";
	$sql .= "ORDER BY date DESC ";
	$sql .= "LIMIT 1 OFFSET 0";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$goods_sale_type = $row->sale_type;
		$goods_sale_money = $row->sale_money;
		$goods_amount_floor = $row->amount_floor;
		$goods_sale_max_money = $row->sale_max_money;
		$goods_coupon_code = $row->coupon_code;

		$couponDownLoadFlag = true;
	}
	pmysql_free_result($result);
}

$mainbanner = mainBannerList();

include_once('outline/header_m.php');
?>

<!-- 내용 -->
<main id="content" class="subpage">

	<h2 class="blind">상품상세보기</h2>

	<!-- 공유 -->
	<p class="view_thumb">
		<? if(strlen($_pdata->maximage)>0 && file_exists("../data/shopimages/product/".$_pdata->maximage)) {?>
			<img src="<?="../data/shopimages/product/".$_pdata->maximage?>" border="0">
		<?	} else {?>
			<img src="<?=$Dir?>images/acimage.gif" border="0">
		<?	}?>
	</p>
<form name=frmView method=post onsubmit="return false"><!-- 상품 정보 form -->
	<div class="detail_spec">
	<input type="hidden" name="miniq" value="<?=$miniq?$miniq:"1";?>">
	<input type="hidden" name="maxq" value="<?=$maxq?>">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="package_count" value="<?=$package_count?>">
	<input type="hidden" name="consumerprice" value="<?=$_pdata->consumerprice?>">
	<input type="hidden" name="dan_price" id="dan_price" value="<?=$_pdata->sellprice?>">
	<input type="hidden" name="ea" id="ea" value="<?=$_pdata->quantity?>">
	<input type="hidden" name="productcode" value="<?=$productcode?>">
	<input type="hidden" name="returnUrl" value="<?=$_SERVER["REQUEST_URI"]?>">
	<input type="hidden" name="package_type" value="<?=$package_type?>">
		<dl>
			<dt><?=$_pdata->productname?></dt>
			<dd class="no">(모델번호:)</dd>
			<dd class="price">
			<?
				$SellpriceValue=0;
				//if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0){
				if(false){
			?>
					<?if($_pdata->consumerprice){?>
						<del><?=number_format($_pdata->consumerprice)?></del>
						<span><?=$dicker?></span>
					<?}else{?>
						<span><?=$dicker?></span>
					<?}?>
			<?
					$prdollarprice="";
					$priceindex=0;
				//} else if(strlen($optcode)==0 && strlen($_pdata->option_price)>0) {
				} else if(false) {
					$option_price = $_pdata->option_price;
					$option_consumer = $_pdata->option_consumer;
					$option_reserve = $_pdata->option_reserve;
					$pricetok=explode(",",$option_price);
					$consumertok=explode(",",$option_consumer);
					$reservetok=explode(",",$option_reserve);
					$priceindex = count($pricetok);
					for($tmp=0;$tmp<=$priceindex;$tmp++) {
						$pricetokdo[$tmp]=number_format($pricetok[$tmp]/$ardollar[1],2);
						$spricetok[$tmp]=number_format($pricetok[$tmp]);
						$pricetok[$tmp]=number_format(getProductSalePrice($pricetok[$tmp], $dc_data[price]));
						if($consumertok[$tmp]) $consumertok[$tmp]=number_format($consumertok[$tmp]);
						$reservetok[$tmp]=number_format($reservetok[$tmp]);
					}
			?>
					<?if($_pdata->consumerprice){?>
						<del><?=number_format($_pdata->consumerprice)?></del>
						<span><?=number_format(str_replace(",","",$pricetok[0]))?></span>
					<?}else{?>
						<span><?=number_format(str_replace(",","",$pricetok[0]))?></span>
					<?}?>
					<input type=hidden name=price value="<?=number_format($_pdata->sellprice)?>">
					<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
					<input type=hidden name=consumer value="<?=number_format($_pdata->consumerprice)?>">
					<input type=hidden name=o_reserve value="<?=number_format($_pdata->option_reserve)?>">
					<input type=hidden name=ID_sellprice id="ID_sellprice" value="<?=$_pdata->sellprice?>">
			<?
					$SellpriceValue=str_replace(",","",$pricetok[0]);
				//} else if(strlen($optcode)>0) {
				} else if(false) {
			?>
					<?if($_pdata->consumerprice){?>
						<del><?=number_format($_pdata->consumerprice)?></del>
						<span><?=number_format($_pdata->sellprice)?></span>
					<?}else{?>
						<span><?=number_format($_pdata->sellprice)?></span>
					<?}?>
					<input type=hidden name=price value="<?=number_format($_pdata->sellprice)?>">
					<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
					<input type=hidden name=consumer value="<?=number_format($_pdata->consumerprice)?>">
					<input type=hidden name=o_reserve value="<?=number_format($_pdata->option_reserve)?>">
					<input type=hidden name=ID_sellprice id="ID_sellprice" value="<?=$_pdata->sellprice?>">
			<?
					$SellpriceValue=$_pdata->sellprice;
				//} else if(strlen($_pdata->option_price)==0) {
				} else if(true) {
			?>
					<?if($_pdata->consumerprice){?>
						<del><?=number_format($_pdata->consumerprice)?></del>
						<span><?=number_format($_pdata->sellprice)?></span>
					<?}else{?>
						<span><?=number_format($_pdata->sellprice)?></span>
					<?}?>
					<input type=hidden name=ID_sellprice id="ID_sellprice" value="<?=$_pdata->sellprice?>">
			<?
					$SellpriceValue=$_pdata->sellprice;
					$priceindex=0;
				}
			?>

			<?
				$couponLimitStr = '';
				## 쿠폰적용가
				if($couponDownLoadFlag){
					if($goods_sale_type <= 2){
						$couponDcPrice = ($SellpriceValue*$goods_sale_money)*0.01;
						$couponDcPrice = ($couponDcPrice / pow(10, $goods_amount_floor)) * pow(10, $goods_amount_floor);
						#$goods_dc_coupong = number_format($goods_sale_money)."%";
						$goods_dc_coupongStr[0] = number_format($goods_sale_money);
						$goods_dc_coupongStr[1] = "%";
					}else{
						$couponDcPrice = $goods_sale_money;
						#$goods_dc_coupong = number_format($goods_sale_money)."원";
						$goods_dc_coupongStr[0] = number_format($goods_sale_money);
						$goods_dc_coupongStr[1] = "원";
					}
					if($goods_sale_max_money && $goods_sale_max_money < $couponDcPrice){
						$couponDcPrice = $goods_sale_max_money;
						$couponLimitStr = "<br>최대할인금액 : ".number_format($goods_sale_max_money)."원";
					}
					$coumoney = $couponDcPrice;
					##쿠폰적용가 출력 위치
				}
			?>
			</dd>
		</dl>
		<a href="javascript:;" class="btn_wishlist favorite" title="찜하기"></a>
	</div>

	<!-- 상품상세 하단 고정 -->

	<div class="detailoption" id="goods_bottom">
		<div class="optionwrap"><!-- 수량선택시 opt_choice 클래스 추가 -->
			<!--
			(D) 옵션을 펼쳐볼 때 .optionwrap 에 class="on" 을 추가하고, a.btn_arrow 에 title="숨기기" 로 변경합니다.
			옵션이 펼쳐진 상태에서 수량을 선택하면 optionwrap 와 openbox 클래스에 opt_choice 클래스를 추가하고, ul.opt_choice 에 있는 hide클래스를 제거합니다.
			-->
			<a class="btn_arrow" href="#" onclick="detail_bottom_toggle();return false;" title="펼쳐보기"><img src="images/detail_bottom_btn_arrow.png" alt="옵션보기" /></a>
			<div class="closebox">
				<div class="buttonbox">
					<div class="box"><a class="btn_buy" href="#" onclick="detail_bottom_toggle();return false;">바로구매</a></div>
				</div>
			</div>
			<div class="openbox"><!-- 수량선택시 opt_choice 클래스 추가 -->
				<?
				if(strlen($_pdata->option1)>0) {
					$temp = $_pdata->option1;
					$option1Arr = explode(",",$temp);
					$tok = explode(",",$temp);
					$optprice = explode(",", $_pdata->option_price);

					$optcode = "";
					if($_pdata->optcode){
						$optcode = explode(",", $_pdata->optcode);
					}
					if (sizeof($optprice)!= sizeof($option1Arr) ) {
						for($i=0; $i<sizeof($option1Arr); $i++){
							$optprice[$i] = $optprice[$i]=="" ? "0":$optprice[$i];
						}
					}
					$optionState = "Y";
					$count=count($tok);
				?>
				<ul>
					<li>
						<!--<span class="optStyle">-->
						<!-- name="optidxs[]" onchange="javascript:option_change(this,'0')" -->
							<select name="option1" id="option1" alt='<?=$tok[0]?>' class="" style="margin-top: 5px;" >
								<option value=""><?=$tok[0]?></option>
								<?for($i=1;$i<$count;$i++) {?>
									<?if(strlen($tok[$i]) > 0) {?>
									<option value="<?=$i?>">
									<?
										$tempopt = $optprice[$i-1] == "" ? "0": $optprice[$i-1];
									?>
										<span><?=$tok[$i]?></span>&nbsp;(<?=number_format($tempopt)?>원)
									<?}?>
									</option>
								<?}?>
							</select>
						<!--</span>-->
					</li>
				</ul>
				<? } ?>
				<?
				if(strlen($_pdata->option2)>0) {
					$temp = $_pdata->option2;
					$option2Arr = explode(",",$temp);
					$tok = explode(",",$temp);
					$count2=count($tok);
					$optionState = "Y";
				?>
				<ul>
					<li>
						<!--<span class="optStyle">-->
						<!-- name="optidxs[]" onchange="javascript:option_change(this,'0')" -->
							<select name="option2" id="option2" alt='<?=$tok[0]?>' class="" style="margin-top: 5px;" >
								<option value=""><?=$tok[0]?></option>
								<?for($i=1;$i<$count2;$i++) {?>
									<?if(strlen($tok[$i]) > 0) {?>
									<option value="<?=$i?>">
									<?
										$tempopt = $optprice[$i-1] == "" ? "0": $optprice[$i-1];
									?>
										<span><?=$tok[$i]?></span>
									<?}?>
									</option>
								<?}?>
							</select>
						<!--</span>-->
					</li>
				</ul>
				<? }?>
				<?if(strlen($_pdata->option1)<1) {?>
				<select id="quantity" name="quantity" onchange="javascript:change_quantity();">
					<option value="0">수량</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
				<?}?>
				<style type="text/css">
					ul.opt_list {}
					ul.opt_list li {height: 50px;}
					ul.opt_list li div.item_info_area {width: 72%; margin: 0 0 0 0; float: left;}
					ul.opt_list li div.item_editer_area {float: left;  width: 28%;}
					ul.opt_list li div.item_editer_area div {float: left;}
					ul.opt_list li div.item_editer_area div {float: left;}
					ul.opt_list li div.item_editer_area div input.amount2 {float: left; margin-top: 10px;}
					ul.opt_list li div.item_editer_area div span {float: left;}
				</style>
				<ul class="opt_choice opt_list" style="display:none;">

				</ul>
				<ul class="opt_choice hide" id="onChoice"><!-- 수량선택시 hide 클래스 제거 -->
					<li><?=$_pdata->productname?></li>
					<li><span>총주문금액: <strong id="result_total_price">0원</strong></span></li>
					<input type = 'hidden' value = '<?=$SellpriceValue?>' id = 'ID_goodsprice' name="ID_goodsprice">
				</ul>
				<div class="buttonbox">
					<div class="box"><a class="btn_buy buy" href="javascript:;">바로구매</a></div>
					<div class="box half"><a class="btn_cart cart" href="javascript:;">장바구니</a></div>
				</div>
			</div>
		</div>
		<script src="js2/detailBottom.js"></script>
	</div>
	<!-- 상품상세 하단 고정 -->
	<?if( strlen($_pdata->option1) > 0 ){?>
	<input type="hidden" readonly="true" name="quantity" id="quantity" value="1">
	<?}?>
	<input type="hidden" name="constant_quantity" id="constant_quantity" value="<?=$_pdata->quantity?>" />
	<input type=hidden name="optionArr" id="optionArr" value="">
	<input type=hidden name="priceArr" id="priceArr" value="">
	<input type=hidden name="quantityArr" id="quantityArr" value="">
	<input type=hidden name="optionState" id="optionState" value="<?=$optionState?>">
</form><!-- //상품 정보 form -->

<script>
var optpriceArr_;
var option1Arr_;
var option2Arr_;
var quantity_ = "<?=$_pdata->option_quantity?>";
quantity_ = quantity_.split(',');

var quantityArr_ = new Array();
quantityArr_[0] = new Array(10);
quantityArr_[1] = new Array(10);
quantityArr_[2] = new Array(10);
quantityArr_[3] = new Array(10);
quantityArr_[4] = new Array(10);

var onOptWarp = 0;
var changeOptWarp = 0;
<?php

	$str1 = "0";
	if (sizeof($optprice)>0){
		$str1 = "[";
		for ($i=0 ;$i<sizeof($optprice) ;$i++ ){
			if ($optprice[$i] == ""){ $optprice[$i] = "0";}
			$str1 .= "'".$optprice[$i]."',";
		}
		$str1 .= "]";
	}

	$str2 = "0";
	if (sizeof($option1Arr)>0){
		$str2 = "[";
		for ($i=0 ;$i<sizeof($option1Arr) ;$i++ ){
			if($i == 0){continue;}
			$str2 .= "'".$option1Arr[$i]."',";
		}
		$str2 .= "]";
	}
	$str3 = "0";
	if (sizeof($option2Arr)>0){
		$str3 = "[";
		for ($i=0 ;$i<sizeof($option2Arr) ;$i++ ){
			if($i == 0){continue;}
			$str3 .= "'".$option2Arr[$i]."',";
		}
		$str3 .= "]";
	}
?>
var optpriceArr_ = <?=trim($str1)?>;
var option1Arr_ = <?=trim($str2)?>;
var option2Arr_ = <?=trim($str3)?>;
var sellprice = <?=$_pdata->sellprice?>;

$(document).ready(function(){

	onOptWarp = $(".optionwrap").height;

	<?php
		if (sizeof($option1Arr)>0){
	?>
			setTotalPrice();
	<?
		} else {
	?>
		setDeliPrice(sellprice,"1");
	<?
		}
	?>
	var d1 = 0;
	var d2 = 0;

	for (var i=0;i<quantity_.length ;i++ ){
		if (i>=1 && i<=10){
			quantityArr_[0][d2] = quantity_[i] == "" ? "0":quantity_[i];
			d2++;
			if (d2>9){ d2=0; }
		}else if (i>=11 && i<=20){
			quantityArr_[1][d2] = quantity_[i] == "" ? "0":quantity_[i];
			d2++;
			if (d2>9){ d2=0; }
		}else if (i>=21 && i<=30){
			quantityArr_[2][d2] = quantity_[i] == "" ? "0":quantity_[i];
			d2++;
			if (d2>9){ d2=0; }
		}else if (i>=31 && i<=40){
			quantityArr_[3][d2] = quantity_[i] == "" ? "0":quantity_[i];
			d2++;
			if (d2>9){ d2=0; }
		}else if (i>=41 && i<=50){
			quantityArr_[4][d2] = quantity_[i] == "" ? "0":quantity_[i];
			d2++;
			if (d2>9){ d2=0; }
		}
	}

});
function quantityCheck(compareVal,d1,d2){

	var constantVal = 0;
	if (d2 == "0"){
		constantVal = quantityArr_[0][d1-1];
	} else {
		constantVal = quantityArr_[d2-1][d1-1];
	}

	if (Number(constantVal) >= Number(compareVal) ){
		return true;
	} else {
		return false;
	}
}

function item_ea_up(conIdx,idx){
	var total_quantity = 0;
	var constant_quantity = $("#constant_quantity").val();
	$(".opt_list li").each(function(){
		var id = $(this).attr('id');
		var ex_id = id.split('-');
		total_quantity = Number($("#quantityea-"+ex_id[1]).val())+total_quantity;
	});
	if (constant_quantity != "" && constant_quantity <= total_quantity){
		alert('상품 재고 수량을 초과 하셨습니다.');
		return;
	}
	var goodsprice = $("#ID_goodsprice").val();
	var count_ = $("#quantityea-"+conIdx).val();
	var itemPrice = $("#itemTotalPrice-"+conIdx).val();
	count_ = Number(count_)+1;
	if (count_ < 1){
		count_ = 1;
	}
	var ex_conIdx = conIdx.split('_');
	if (!quantityCheck(count_,ex_conIdx[0],ex_conIdx[1])){
		alert('옵션 재고 수량 초과 입니다.');
		return;
	}
	$("#quantityea-"+conIdx).val(count_);
	$("#itemPrice-"+conIdx).html(jsSetComa(itemPrice*count_)+"원");
	change_quantityOpt("up");
	setTotalPrice();
}
function item_ea_dn(conIdx,idx){
	var goodsprice = $("#ID_goodsprice").val();
	var count_ = $("#quantityea-"+conIdx).val();
	var itemPrice = $("#itemTotalPrice-"+conIdx).val();
	count_ = Number(count_)-1;
	if (count_ < 1){
		count_ = 1;
	}
	$("#quantityea-"+conIdx).val(count_);
	$("#itemPrice-"+conIdx).html(jsSetComa(itemPrice*count_)+"원");
	change_quantityOpt("dn");
	setTotalPrice();
}
function items_del(conIdx,idx){
	//적립금
	var itemQuantity = $("#quantityea-"+conIdx).val();

	//document.form1.quantity.value -= itemQuantity;
	$("#quantity").val($("#quantity").val()-itemQuantity);
	//if(document.form1.quantity.value < 0) document.form1.quantity.value = 0;
	if($("#quantity").val() < 0) $("#quantity").val(0);
	$("#items-"+conIdx).remove();
	//빼줘야함
	if(changeOptWarp > 1){
		$(".optionwrap").height($(".optionwrap").height()-50);
		changeOptWarp--;
	}else{
		$(".optionwrap").height($(".optionwrap").height()-50);
	}
	setTotalPrice();
}

$(function(){
	$("#option1").change(function(){
		if (option2Arr_.length >0){
			return;
		}
		var appendHtml = "";
		var minea = 1; // 최소 구매 수량
		var constant_quantity = $("#constant_quantity").val(); // 재고량
		var val1 = $("#option1 option:selected").val();
		if(val1 == "")	return;
		var op1Title = $("#option1").attr("alt");
		var goodsprice = $("#ID_goodsprice").val();
		var controlIdx_ = val1+"_0";
		var total_quantity = 0;
		var validationControler = true;
		$(".opt_list li").each(function(){
			var id = $(this).attr('id');
			var ex_id = id.split('-');
			if (ex_id[1] == controlIdx_){
				validationControler = false;
			}
			total_quantity = Number($("#quantityea-"+ex_id[1]).val())+total_quantity;
		});
		if (constant_quantity != "" && constant_quantity <= total_quantity){
			alert('상품 재고 수량을 초과 하셨습니다.');
			return;
		}
		if (!validationControler){
			alert('이미 추가되어 있는 옵션입니다.');
			return;
		}
		if (!quantityCheck('1',val1,'0')){
			alert('옵션 재고 수량 초과 입니다.');
			return;
		}
		/*if (val1 == ""){
			alert(op1Title+' 을 선택 하셔야 합니다.');
			$("#option1").focus();
			return;
		}*/
		val1 = Number(val1)-1;
		var itemTotalPrice = Number(sellprice)+Number(optpriceArr_[val1]);

		appendHtml += "<li class='select_list' id='items-"+controlIdx_+"'>";
		appendHtml += "<div class='item_info_area'>";
		appendHtml += "	<span class='opt_name'>-<?=$_pdata->productname?>,"+option1Arr_[val1]+"</span> <span class='price' id='itemPrice-"+controlIdx_+"' alt="+itemTotalPrice+">"+jsSetComa(itemTotalPrice)+"원</span>";
		appendHtml += "</div>";
		appendHtml += "<div class='item_editer_area'>";
		appendHtml += "		<div style='float:left;'>";
		appendHtml += "			<input type=text id='quantityea-"+controlIdx_+"' value='1' class='amount2' size = '2' readonly>";
		appendHtml += "			<input type=hidden id='itemTotalPrice-"+controlIdx_+"' class='itemPrice' value='"+itemTotalPrice+"' >";
		appendHtml += "			<span class='item_ea_up' onclick='javascript:item_ea_up(\""+controlIdx_+"\",\""+val1+"\");'><img src='/image/cart/c_plus_btn.jpg'></span>";
		appendHtml += "			<span class='item_ea_dn' onclick='javascript:item_ea_dn(\""+controlIdx_+"\",\""+val1+"\");'><img src='/image/cart/c_minus_btn.jpg'></span>";
		appendHtml += "		</div>";
		appendHtml += "		<div style='float:right;'><span><img src='/image/cart/c_x_btn.gif' alt='삭제'  onclick='javascript:items_del(\""+controlIdx_+"\",\""+val1+"\");' class='item_del' id='item_del' /></span></div>";
		appendHtml += "	</div>";
		appendHtml += "</li>";

		goodsprice = Number(goodsprice)+Number(itemTotalPrice);
		if($(".opt_list").find("li").length > 0){
			change_quantityOpt("up");
		}
		$(".opt_list").append(appendHtml);
		//크기 올려주야 함
		if(changeOptWarp == 0){
			$(".optionwrap").height($(".optionwrap").height() + 70);
			changeOptWarp++;
		}else{
			$(".optionwrap").height($(".optionwrap").height()+ 50);
			changeOptWarp++;
		}
		$(".opt_list").show();
		setTotalPrice();
	});

	$("#option2").change(function(){
	//$('#option2 option').bind('click', function(){
		var minea = 1; // 최소 구매 수량
		var constant_quantity = $("#constant_quantity").val(); // 재고량
		var appendHtml = "";
		var val1 = $("#option1 option:selected").val();
		var op1Title = $("#option1").attr("alt");
		var val2 = $("#option2 option:selected").val();
		var op2Title = $("#option2").attr("alt");
		var goodsprice = $("#ID_goodsprice").val();
		var controlIdx_ = val1+"_"+val2;
		var total_quantity = 0;
		var validationControler = true;

		$(".opt_list li").each(function(){
			var id = $(this).attr('id');
			var ex_id = id.split('-');
			if (ex_id[1] == controlIdx_){
				validationControler = false;
			}
			total_quantity = Number($("#quantityea-"+ex_id[1]).val())+total_quantity;
		});

		if (constant_quantity != "" && constant_quantity <= total_quantity){
			alert('상품 재고 수량을 초과 하셨습니다.');
			return;
		}

		if (!validationControler){
			alert('이미 추가되어 있는 옵션입니다.');
			return;
		}
		if (!quantityCheck('1',val1,val2)){
			alert('옵션 재고 수량 초과 입니다.');
			return;
		}
		if (val1 == ""){
			alert(op1Title+' 을 선택 하셔야 합니다.');
			$("#option1").focus();
			return;
		}
		if (val2 == ""){
			alert(op2Title+' 을 선택 하셔야 합니다.');
			$("#option2").focus();
			return;
		}
		val1 = Number(val1)-1;
		val2 = Number(val2)-1;

		var itemTotalPrice = Number(sellprice)+Number(optpriceArr_[val1]);
		appendHtml += "<li class='select_list' id='items-"+controlIdx_+"'>";
		appendHtml += "<div class='item_info_area'>";
		appendHtml += "	<span class='opt_name'>-<?=$_pdata->productname?>,"+option1Arr_[val1]+","+option2Arr_[val2]+"</span> <span class='price' id='itemPrice-"+controlIdx_+"' alt="+itemTotalPrice+">"+jsSetComa(itemTotalPrice)+"원</span>";
		appendHtml += "</div>";
		appendHtml += "<div class='item_editer_area'>";
		appendHtml += "		<div style='float:left;'>";
		appendHtml += "			<input type=text id='quantityea-"+controlIdx_+"' value='1' class='amount2' size = '2' readonly>";
		appendHtml += "			<input type=hidden id='itemTotalPrice-"+controlIdx_+"' class='itemPrice' value='"+itemTotalPrice+"' >";
		appendHtml += "			<span class='item_ea_up' onclick='javascript:item_ea_up(\""+controlIdx_+"\",\""+val1+"\");'><img src='/image/cart/c_plus_btn.jpg'></span>";
		appendHtml += "			<span class='item_ea_dn' onclick='javascript:item_ea_dn(\""+controlIdx_+"\",\""+val1+"\");'><img src='/image/cart/c_minus_btn.jpg'></span>";
		appendHtml += "		</div>";
		appendHtml += "		<div style='float:right;'><span><img src='/image/cart/c_x_btn.gif' alt='삭제'  onclick='javascript:items_del(\""+controlIdx_+"\",\""+val1+"\");' class='item_del' id='item_del' /></span></div>";
		appendHtml += "	</div>";
		appendHtml += "</li>";
		goodsprice = Number(goodsprice)+Number(itemTotalPrice);
		if($(".opt_list").find("li").length > 0){
			change_quantityOpt("up");
		}
		$(".opt_list").append(appendHtml);
		//크기 올려줘야 함
		if(changeOptWarp == 0){
			$(".optionwrap").height($(".optionwrap").height() + 70);
			changeOptWarp++;
		}else{
			$(".optionwrap").height($(".optionwrap").height()+ 50);
			changeOptWarp++;
		}
		$(".opt_list").show();

		setTotalPrice();
	});
});
function setTotalPrice(){
	var totaltemp = 0;
	var totalea = 0;
	$(".amount2").each(function(index){
		var itemea = $(this).val();
		var id = $(this).attr('id');
		var ex_id = id.split('-');
		var itemIdx = ex_id[1];
		var itemPrice = $("#itemTotalPrice-"+itemIdx).val();
		totaltemp = totaltemp+(itemPrice*itemea);
		totalea = Number(totalea) + Number(itemea);
	});
	if (totaltemp == ""){
		$(".opt_list").hide();
		totaltemp = 0;
	}
	$("#result_total_price").html(jsSetComa(totaltemp)+"원");
	$("#ID_goodsprice").val(totaltemp);
	$("#option2 option:eq(0)").attr("selected","true");
	setDeliPrice(totaltemp,totalea);
}

function setDeliPrice(totalPrice,totalea){
var deli_type = $("#deli_type").val();
var deliprice = $('#deli_price').val();


if (deli_type == "1"){
	$('#deli_price_result').html(jsSetComa(deliprice*totalea)+"원"); // 구매수 대비 증가
	return;
} else if (deli_type == "2") {
	$('#deli_price_result').html(jsSetComa(deliprice)+"원");
	return;
} else if (deli_type == "3") {
	$('#deli_price_result').html("0원");
	return;
} else if (deli_type == "4") {
	$('#deli_price_result').html("착불");
	return;
} else if (deli_type == "5") {
	$('#deli_price_result').html("착불");
	return;
} else if (deli_type == "6") {
	$('#deli_price_result').html("0원");
	return;
} else if (deli_type == "7") {
	if (totalPrice >= $("#deli_miniprice").val() ) {
		deliprice = 0;
	}
	deliprice = deliprice ;
	$('#deli_price_result').html(jsSetComa(deliprice)+"원");
	return;
} else if (deli_type == "8") {
	if (totalPrice >= $("#deli_miniprice").val() ) {
		deliprice = 0;
	}
	$('#deli_price_result').html(jsSetComa(deliprice)+"원");
	return;
} else {
	return;
}
return;
}

/*function newOptionPrice(tmp_price,count_){
	var itemPrice = 0;
	if(s_cnt > 0){
		$.each(price,function(index,item){
			if(Number(s_min[index]) <= Number(count_) && Number(s_max[index]) >= Number(count_)) tmp_price = item;
		});
		//if(tmp_price <= 0) tmp_price = itemPrice;
		//itemPrice = Number(tmp_price)+ Number($("#optionPrice-"+conIdx).val());
		return tmp_price;
	}
	return tmp_price;
}*/
</script>
	<nav class="share">
		<ul>
			<li><a href="https://www.facebook.com/OryanyKorea"><img src="images/btn_share_facebook.png" alt="페이스북으로 공유"></a></li>
			<li><a href="https://instagram.com/oryanykorea/"><img src="images/btn_share_insta.png" alt="인스타그램 공유"></a></li>
			<!--li><a href="#"><img src="images/btn_share_kakaostory.png" alt="카카오스토리로 공유"></a></li>-->
			<!--<li><a href="#"><img src="images/btn_share_kakaotalk.png" alt="카카오톡으로 공유"></a></li>
		</ul>-->
	</nav>

	<div class="detail_banner"><a href="#"><img src="images/sample/s_banner01.jpg" alt="" /></a></div>
	<!-- // 공유 -->



	<!-- 상세 내용 탭 -->
	<nav class="detailtab">
		<!--
			(D) 선택된 li 에 class="on" title="선택됨" 을 추가합니다.
			a 의 href 는 "#list_content + 숫자" 조합으로 차례로 넣어줍니다.
		-->
		<ul>
			<li class="on" title="선택됨"><a href="#list_content1">상세보기</a></li>
			<li><a href="#list_content2">상품리뷰</a></li>
			<li><a href="#list_content3">상품Q&amp;A</a></li>
			<li><a href="#list_content4">쇼핑안내</a></li>
		</ul>
		<script src="js2/detailTab.js"></script>
	</nav>
	<!-- // 상세 내용 탭 -->

	<!-- (D) section 에 .detailtab 의 href 와 연결되도록 id 를 차례로 넣어줍니다. -->
	<!-- 상품설명 -->
	<section id="list_content1" class="section manual">
		<h3 class="blind">상품설명</h3>
		<div class="imgbox">
			<!-- (D) 상품설명 이미지는 이 곳에 넣어주세요 -->
			<?=stripslashes($_pdata->content)?>
			<!--<img src="images/sample/goods_detail.jpg" alt="">-->
		</div>
	</section>
	<!-- // 상품설명 -->

	<!-- 상품리뷰 -->
	<section id="list_content2" class="section review">
		<h3 class="blind">상품리뷰</h3>
		<ul class="detail_review_write">
		<form id="frmReview" class="form_wrap" method="POST">
		<input type="hidden" name="productcode" value="<?=$_pdata->productcode?>"  />
		<input type="hidden" name="mode" value="review_write" />
		<input type='hidden' name='rname' id='rname' value="<?=$_ShopInfo->memname?>">
			<li><input type="text" id="rsubject" name="rsubject" placeholder="제목을 입력해주세요." /></li>
			<li>
				<select name = 'rmarks' >
					<option value="5">★★★★★</option>
					<option value="4">★★★★☆</option>
					<option value="3">★★★☆☆</option>
					<option value="2">★★☆☆☆</option>
					<option value="1">★☆☆☆☆</option>
				</select>
			</li>
			<li class="txtarea"><div class="txtarea_wrap"><textarea id="review_content" name="rcontent" cols="30" rows="10" placeholder="내용을 입력해주세요"></textarea></div></li>
			<li><input type="text" type="text" name="rblog" id="rblog" placeholder="블로그 URL을 입력해주세요" /></li>
			<li><input type="file" name = 'rfile' /><p>(※ 한글,영문,숫자 / 800K이하 / 파일명:GIF,JPG,JPEG)</p></li>
			<li><a href="javascript:saveReview('<?=$review_ordercode_cnt?>');" class="btn">등록하기</a></li>
		</form>
		</ul>

		<? if($reviewcount>0) { ?>
		<div class="topbox">
			<span>고객님이 작성해 주신 상품리뷰 <strong>총 <?=$reviewNum_row->cnt?>개</strong></span>
		</div>
		<div class="CLS_detail_review">
		<!--
			(D) 별점은 style 로 width 를 직접 설정하고, 내용에 "총점 5점만점에 n점" 을 넣어줍니다.
			선택된 li 에 class="on" 을 추가하고, a.title 에 title="숨기기" 로 변경합니다.
		-->
		<ul class="review">
			<?foreach($reviewloop as $review){?>
			<?
				$colorStar = 0;
				for($i=0;$i<$review->marks;$i++) {
					$colorStar += 20;
				}
				$noColorStar = "";
				/*for($i=$review->marks;$i<5;$i++) {
					$noColorStar .= "★";
				}*/
				if($review->id){
					$reviewWriter = $review->id;
				}else{
					$reviewWriter = "비회원";
				}
				$reviewDate=substr($review->date,0,4)."-".substr($review->date,4,2)."-".substr($review->date,6,2);
			?>
			<li>
				<a class="title" href="#" title="펼쳐보기">
					<span class="id"><?=$reviewWriter?> (<?=$reviewDate?>)</span>
					<div class="title"><?=$review->subject?></div>
					<div class="starbox"><span style="width:<?=$colorStar?>%"></span></div>
				</a>
				<div class="content">
					<p>
						<?=nl2br($review->content)?>
					</p>
				</div>
			</li>
			<?}?>
		</ul>

		<div class="page_num">
			<!--<span>1/4</span>-->
			<!--<a href="#" class="btn prev">&lt; 이전</a>
			<a href="#" class="btn next">다음 &gt;</a>-->
			<span>1/<?=$reviewtotalpage?></span> <? if($reviewcount>5) : ?><a class="btn next" href="javascript:reviewPage('2','<?=$_pdata->productcode?>');" class="next">다음</a><? endif;?>
		</div>
		</div>
		<? } ?>


	</section>
	<!-- // 상품리뷰 -->

	<!-- 상품Q&A -->
	<section id="list_content3" class="section review">
		<h3 class="blind">상품Q&A</h3>
		<?
			$passwd_style = "
				background: #fff;
				border: 1px solid #b2b4b2;
				width: 100%;
				line-height: 28px;
				text-indent: 10px;
			";
		?>
		<ul class="detail_review_write">
		<form id="frmqna" class="form_wrap" method="POST">
			<input type="hidden" name="mode" value="qna_write" />
			<li><input type="text" id="qna_name" name="up_name" placeholder="이름을 입력해주세요"/></li>
			<li><input style="<?=$passwd_style?>" type="password" id="qna_pw" name="up_passwd" placeholder="비밀번호을 입력해주세요." /></li>
			<li><input type="text" id="qna_email" name="up_email" placeholder="이메일을 입력해주세요." /></li>
			<li><input type="text"id="qna_title" name="up_subject" placeholder="제목을 입력해주세요." /></li>
			<li class="txtarea"><div class="txtarea_wrap"><textarea id="qna_content" name="up_memo" cols="30" rows="10" placeholder="내용을 입력해주세요"></textarea></div></li>
			<li><a href="javascript:saveqna();" class="btn">등록하기</a></li>
		</form>
		</ul>

		<? if($qnacount>0) { ?>
		<div class="topbox">
			<span>고객님이 작성해 주신 상품문의<strong>총 <?=$qnacount?>개</strong></span>
		</div>
		<!--
		선택된 li 에 class="on" 을 추가하고, a.title 에 title="숨기기" 로 변경합니다.
		답변이 등록된경우 process 에 hide를 삭제해줍니다.
		-->
		<div class="CLS_detail_qna">
		<ul class="review">
			<?foreach($qnaloop as $qna){?>
				<?
					list($qnaCount)=pmysql_fetch("SELECT count(num) FROM tblboardcomment WHERE board = 'qna' and parent = '".$qna->num."'");
				?>
			<li>
				<a class="title" href="#" title="펼쳐보기">
					<span class="id"><?=$qna->name?> (<?=$qna->writetime?>)</span>
					<div class="title"><?=$qna->title?></div>
					<div class="process">답변</div>
				</a>
				<div class="content">
					<p class="ques">
						<?=nl2br($qna->content)?>
					</p>
					<?
						$qna_reply_sql = "SELECT * FROM tblboardcomment WHERE board = 'qna' and parent = '".$qna->num."' order by num desc";
						$qna_reply_res = pmysql_query($qna_reply_sql,get_mdb_conn());
						while($qna_reply_row = pmysql_fetch_object($qna_reply_res)){
					?>
					<p class="answer">
						<span class="date"><strong>답변</strong> (<?=date("Y/m/d",$qna_reply_row->writetime)?>)</span>
						<?=nl2br($qna_reply_row->comment)?>
					</p>
					<?
						}
					?>
				</div>
			</li>
			<?}?>
		</ul>

		<div class="page_num">
			<span>1 / <?=$qnatotalpage?></span> <? if($qnacount>5) : ?><a  href="javascript:qnaPage('2');"  class="btn next">다음&gt;</a><? endif;?>
			<!--<span>1/4</span>
			<a href="#" class="btn prev">&lt; 이전</a>
			<a href="#" class="btn next">다음 &gt;</a>-->
		</div>
		</div>
	<? } ?>
		<script src="js2/detailReview.js"></script>
		<script>

			//리뷰페이징
			function reviewPage(page,productcode){
				$.get("productdetail_ajax_review.php?page="+page+"&productcode="+productcode,function(data){
					$(".CLS_detail_review").html(data);
				});
			}
			//리뷰 저장
			function saveReview(cnt){
				if($("#rsubject").val() == ""){
					alert("제목을 입력해주세요.");
					return false;
				}
				if($("#review_content").val() == ""){
					alert("내용을 입력해주세요.");
					return false;
				}
				if(cnt == 0){
					alert("상품을 주문하신후에 후기 등록이 가능합니다.");
					return false;
				}
				$("#frmReview").submit();
			}
			//Q&A등록
			function saveqna(){
				if($("#qna_name").val() == ""){
					alert("이름을 입력해주세요.");
					return false;
				}
				if($("#qna_pw").val() == ""){
					alert("비밀번호를 입력해주세요.");
					return false;
				}
				if($("#qna_title").val() == ""){
					alert("제목을 입력해주세요.");
					return false;
				}
				if($("#qna_content").val() == ""){
					alert("내용을 입력해주세요.");
					return false;
				}
				$("#frmqna").submit();
			}
			//Q&A페이징
			function qnaPage(page){
				$.get("productdetail_ajax_qna.php?page="+page+"&pridx="+<?=$pridx?>,function(data){
					$(".CLS_detail_qna").html(data);
				});
			}

			//상품 증가값
			function change_quantityOpt(gbn) {
				tmp=$('#ea').val();
				if(gbn=="up") {
					tmp++;
				} else if(gbn=="dn") {
					if(tmp>1) tmp--;
				}
				var cons_qu = $("#constant_quantity").val();
				if (cons_qu != "" && cons_qu != "0"){
					if (cons_qu<tmp){
						alert('재고량이 부족 합니다.');
						return;
					}
				} else if(cons_qu == "0") {
					alert('품절 입니다.');
					return;
				}
				<?php  if($_pdata->assembleuse=="Y") { ?>
					if(getQuantityCheck(tmp)) {
						if(document.form1.assemblequantity) {
							document.form1.assemblequantity.value=tmp;
						}
						$('#ea').val(tmp);
						setTotalPrice(tmp);
					} else {
						alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
						return;
					}
				<?php  } else { ?>
					//var tmp_price = $("#ID_goodsprice").val();
					//tmp_price = Number(tmp_price)*Number(tmp);
					//setDeliPrice(tmp_price,tmp);
					//$("#result_total_price").html(jsSetComa(tmp_price));
					$('#ea').val(tmp);
				<?php  } ?>
			}
			// 장바구니 담기
			$('.cart').click(function(){
				var itemCount = 0;

				$(".select_list").each(function(){
					var id = $(this).attr("id");
					var ex_id = id.split("-");
					var optionArr_temp = $("#optionArr").val() == "" ? ex_id[1] : $("#optionArr").val()+"||"+ex_id[1];
					var quantityArr_temp = $("#quantityArr").val() == "" ? $("#quantityea-"+ex_id[1]).val(): $("#quantityArr").val()+"||"+$("#quantityea-"+ex_id[1]).val();
					var priceArr = $("#priceArr").val() == "" ? $("#itemPrice-"+ex_id[1]).attr("alt"): $("#priceArr").val()+"||"+$("#itemPrice-"+ex_id[1]).attr("alt");
					$("#optionArr").val(optionArr_temp);
					$("#quantityArr").val(quantityArr_temp);
					$("#priceArr").val(priceArr);
					itemCount++;
				});

				var optionState = $("#optionState").val();
				var optionArr_ = $("#optionArr").val();
				var quantity = $("#quantity").val();;
				var priceArr_ = $("#priceArr").val();
				var quantityArr_ = $("#quantityArr").val();

				if (optionState == "Y"){
					if (itemCount<1){
						if ($("#option2").attr("alt") != ""){
							alert($("#option1").attr("alt")+','+$("#option2").attr("alt")+'은 필수 선택입니다.');
							return;
						} else {
							alert($("#option1").attr("alt")+'은 필수 선택입니다.');
							return;
						}
						$("#option1").focus();
					}
				} else if(optionState == "N") {
					if ($("#quantity").val() < 1){
						alert('수량을 입력 하세요.');
						$("#quantity").focus();
						return;
					} else {
						quantityArr_ =$("#quantity").val();
						optionArr_ = "0_0";
					}
				} else {
					alert('errer');
					return;
				}

				//초기화
				$("#optionArr").val('');
				$("#priceArr").val('');
				$("#quantityArr").val('');

				//return;
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: 'productdetail.process.php',
					data: { action_mode: 'cart_add', productcode: '<?=$_pdata->productcode?>', quantity: quantity,priceArr:priceArr_,optionArr:optionArr_,quantityArr:quantityArr_ },
					success: function(response){
						if (response.success){
							if(confirm("장바구니를 확인하시겠습니까?")){
								location.replace('basket.php');
							}
						}else{
							if(response.data=="package"){
								alert(decodeURIComponent(response.msg));
							}else{
								if(confirm("이미 장바구니에 상품이 담겨있습니다. 장바구니를 확인하시겠습니까?")){
									location.replace('basket.php');
								}
							}

						}
						return;
					}
				});
				return;
			});


			//바로구매
			$('.buy').click(function(){
				var itemCount = 0;

				$(".select_list").each(function(){
					var id = $(this).attr("id");
					var ex_id = id.split("-");
					var optionArr_temp = $("#optionArr").val() == "" ? ex_id[1] : $("#optionArr").val()+"||"+ex_id[1];
					var quantityArr_temp = $("#quantityArr").val() == "" ? $("#quantityea-"+ex_id[1]).val(): $("#quantityArr").val()+"||"+$("#quantityea-"+ex_id[1]).val();
					var priceArr = $("#priceArr").val() == "" ? $("#itemPrice-"+ex_id[1]).attr("alt"): $("#priceArr").val()+"||"+$("#itemPrice-"+ex_id[1]).attr("alt");
					$("#optionArr").val(optionArr_temp);
					$("#quantityArr").val(quantityArr_temp);
					$("#priceArr").val(priceArr);
					itemCount++;
				});

				var optionState = $("#optionState").val();
				var optionArr_ = $("#optionArr").val();
				var quantity = $("#quantity").val();
				var priceArr_ = $("#priceArr").val();
				var quantityArr_ = $("#quantityArr").val();

				if (optionState == "Y"){
					if (itemCount<1){
						if ($("#option2").attr("alt") != ""){
							alert($("#option1").attr("alt")+','+$("#option2").attr("alt")+'은 필수 선택입니다.');
							return;
						} else {
							alert($("#option1").attr("alt")+'은 필수 선택입니다.');
							return;
						}
						$("#option1").focus();
					}
				} else if(optionState == "N") {
					if ($("#quantity").val() < 1){
						alert('수량을 입력 하세요.');
						$("#quantity").focus();
						return;
					} else {
						quantityArr_ =$("#quantity").val();
						optionArr_ = "0_0";
					}
				} else {
					alert('errer');
					return;
				}

				//초기화
				$("#optionArr").val('');
				$("#priceArr").val('');
				$("#quantityArr").val('');

				//return;
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: 'productdetail.process.php',
					data: { action_mode: 'order_add', productcode: '<?=$_pdata->productcode?>', quantity: quantity,priceArr:priceArr_,optionArr:optionArr_,quantityArr:quantityArr_ },
					success: function(response){
						if (response.success){

							//location.replace('order.php');
							//location.replace('login.php?chUrl=order.php?productcode=<?=$productcode?>');
							location.replace('login.php?chUrl=order.php');

						}else{
							if(response.data=="package"){
								alert(decodeURIComponent(response.msg));
							}else{
								if(confirm("이미 장바구니에 상품이 담겨있습니다. 장바구니를 확인하시겠습니까?")){
									location.replace('basket.php');
								}
							}

						}
						return;
					}
				});
				return;
			});


			// 관심 상품 담기
			$('.favorite').click(function() {
				var optidx = document.getElementsByName('optidxs[]');
				var optidxs = '';

				for (i=0;i<optidx.length;i++){
					if(optidx[i].value==''){
						alert('옵션을 선택해주세요.');
						return;
					}else{
						optidxs += optidx[i].value+",";
					}
				}

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: 'productdetail.process.php',
					data: { action_mode: 'wishlist_add', pridx: '<?=$_pdata->pridx?>', optidxs: optidxs },
					success: function(response) {
						if (response.success) {
							if(confirm('관심상품 등록 하였습니다. \n이동하시겠습니까?')){
								location.replace('wishlist.php');
							}
						} else if (response.data == 'LOGIN') {
							alert(decodeURIComponent(response.msg));
							location.replace('login.php?chUrl=<?=getUrl()?>');
						} else {
							alert(decodeURIComponent(response.msg));
							$("#qtest").val(decodeURIComponent(response.msg));

						}
						return false;
					}
				});
				return false;
			});

			function change_quantity() {
				tmp = $("#quantity").val();

				var cons_qu = $("#constant_quantity").val();
				if (cons_qu != "" && cons_qu != "0"){
					if (cons_qu<tmp){
						alert('재고량이 부족 합니다.');
						return;
					}
				} else if(cons_qu == "0") {
					alert('품절 입니다.');
					return;
				}
				<?php  if($_pdata->assembleuse=="Y") { ?>
					if(getQuantityCheck(tmp)) {
						if(document.form1.assemblequantity) {
							document.form1.assemblequantity.value=tmp;
						}
						$("#quantity").val(tmp);
						setTotalPrice(tmp);
					} else {
						alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
						return;
					}
				<?php  } else { ?>
					//var tmp_price = $("#ID_goodsprice").val();
					var tmp_price = $("#ID_sellprice").val();

					$("#quantity").val(tmp);
					//tmp_price = tmp_price == newOptionPrice(tmp_price,tmp) ? tmp_price : newOptionPrice(tmp_price,tmp);
					tmp_price = Number(tmp_price)*Number(tmp);
					setDeliPrice(tmp_price,tmp);
					setTotalPrice();
					$("#ID_goodsprice").val(tmp_price);
					$("#result_total_price").html(jsSetComa(tmp_price)+"원");
					if(changeOptWarp == 0){
						$(".optionwrap").height($(".optionwrap").height()+65);
						changeOptWarp = 1 ;
					}
					$("#onChoice").removeClass("hide");
				<?php  } ?>
			}
			$(document).ready(function(){
					OptWarp = $(".optionwrap").height();

					$("a.btn_arrow").click(function(){

					<?php if(strlen($_pdata->option2) > 0){ ?>
						if($(".optionwrap").attr("class").length >= 13){
							//$(".optionwrap").css("height",110);
							$(".optionwrap").css("height",110+(($(".opt_list").find("li").length * 50)+20));
						}/*else if($(".opt_list").find("li").length > 0 && $(".optionwrap").attr("class").length >= 13){
						}*/else{
							$(".optionwrap").css("height",52);
						}
					<?php }else{ ?>

						if($(".optionwrap").height() > 90) $(".optionwrap").height(OptWarp);
						else if(changeOptWarp == 1) $(".optionwrap").height($(".optionwrap").height()+103);

					<?php } ?>
					});
			});
		</script>
	</section>
	<!-- // 상품Q&A -->

	<!-- 쇼핑안내 -->
	<section id="list_content4" class="section guide">
		<h3 class="blind">쇼핑안내</h3>
		<article>
			<dl>
				<dt>구매전 필독사항</dt>
				<dd>구매전에 꼭 읽어주세요</dd>
				<dt>배송비</dt>
				<dd>
					무료배송<br />
					무료배송 단 50,000원 이상 구매 시 무료배송이며, 50,000원 미만 시 2.500원의 배송비가 지불됩니다.<br />
					또한 이벤트 상품 중 배송비 적용 및 상품페이지에 단품구매 시 배송비 책정 상품의 경우 배송비가 적용될 수 있습니다. <br />
					(타 쇼핑몰과 달리 도서, 도서산간지역도 추가 배송비가 없습니다.)<br />
					배송비는 한번에 결제하신 동일 주문번호, 동일 배송지 기준으로 부과됩니다 반품시에는 배송비가 환불되지 않습니다.
				</dd>
				<dt>배송기간</dt>
				<dd>
					평일 오전 9시 이전 입금 확인분에 한해 당일 출고를 원칙으로 합니다. <br />
					입금 확인 후 2~3일 이내 배송( 토, 일, 공휴일 제외), 도서 산간지역은 7일 이내 배송됩니다. <br />
					단, 물류 사정에 따라 다소 차이가 날 수 있습니다.
				</dd>
			</dl>
		</article>
	</section>
	<!-- // 쇼핑안내 -->


</main>
<!-- // 내용 -->

<?php
include_once('outline/footer_m.php')
?>