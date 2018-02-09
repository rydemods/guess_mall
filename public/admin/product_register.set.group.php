<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}

#########################################################
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다

//exdebug($_POST);
//exdebug($_GET);
/**
*
* 함수명 : ProductThumbnail
* 이미지 썸네일 생성
* parameter :
* 	- string prcode : 상품코드 ( 이미지 폴더 )
*   - string fileName : 업로드 이미지명
*   - string upFile : 파일명
*	- makeWidth : 편집될 넓이
*	- makeHeight : 편집될 높이
*   - image_dir : 상품코드 (하단에 소스에서 prcode 입력 변수에 날짜를 포함시키게 변경되어 순수 상품코드가 별도로 필요함)
* 2015 10 30 유동혁
*/
function ProductThumbnail ( $prcode, $fileName, $upFile, $makeWidth, $makeHeight, $imgborder, $setcolor='', $image_dir='' ){
	//$imagepath = DirPath.DataDir."shopimages/product/".$prcode."/";
    $imagepath = DirPath.DataDir."shopimages/product/".$image_dir."/";
	$quality = "90";
	if(ord($setcolor)==0) $setcolor="000000";
	$rcolor=HexDec(substr($setcolor,0,2));
	$gcolor=HexDec(substr($setcolor,2,2));
	$bcolor=HexDec(substr($setcolor,4,2));
	$quality = "90";
	if ( ord($fileName) && file_exists( $imagepath.$upFile ) ) {
		$imgname=$imagepath.$upFile; // 위치 + 파일명
		$size=getimageSize($imgname); //파일 사이즈 array ( 0=>width, 1=>height, 2=>imgtype )
		$width=$size[0];
		$height=$size[1];
		$imgtype=$size[2];

		if ($width >= $makeWidth || $height >= $makeHeight) {
			# 파일 타입별 이미지생성
			if($imgtype==1)      $im = ImageCreateFromGif($imgname);
			else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
			else if($imgtype==3) $im = ImageCreateFromPng($imgname);
			 # 파일의 넓이나 높이가 큰 부분을 기준으로 파일을 자른다
			 $small_width = $makeWidth;
			 $small_height = $makeHeight;

			 # 타입별로 파일 색상, 크기 리사이즈
			if ($imgtype==1) {
				$im2=ImageCreate($small_width,$small_height); // GIF일경우
				$white = ImageColorAllocate($im2, 255,255,255);
				imagefill($im2,1,1,$white);
				$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
				ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
				if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
				imageGIF($im2,$imgname);
			} else if ($imgtype==2) {
				$im2=ImageCreateTrueColor($small_width,$small_height); // JPG일경우
				$white = ImageColorAllocate($im2, 255,255,255);
				imagefill($im2,1,1,$white);
				$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
				imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
				if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
				imageJPEG($im2,$imgname,$quality);
			} else {
				$im2=ImageCreateTrueColor($small_width,$small_height); // PNG일경우
				$white = ImageColorAllocate($im2, 255,255,255);
				imagefill($im2,1,1,$white);
				$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
				imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
				if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
				imagePNG($im2,$imgname);
			}

			ImageDestroy($im);
			ImageDestroy($im2);
			chmod($imgname,0777);
		}
	}
}

#파일 로그 function 추가 2015 10 30 유동혁 
function Product_textLog ( $prcode , &$text, $type ){
	$sql = "SELECT sellprice, consumerprice, buyprice, reserve, reservetype, quantity, min_quantity, max_quantity, option1, option2, supply_subject ";
	$sql.= "FROM tblproduct WHERE productcode = '".$prcode."'";
	$res = pmysql_query( $sql, get_db_conn() );
	$row = pmysql_fetch_object( $res );
	// 상품로그
	$text.= "\n* ".$type." ----------------------------------------------------------\n";
	$text.= " productcode       : ".$prcode."\n";
	$text.= " sellprice         : ".$row->sellprice."\n";
	$text.= " consumerprice     : ".$row->consumerprice."\n";
	$text.= " buyprice          : ".$row->buyprice."\n";
	$text.= " reserve           : ".$row->reserve."\n";
	$text.= " reservetype       : ".$row->reservetype."\n";
	$text.= " quantity          : ".$row->quantity."\n";
	$text.= " min_quantity      : ".$row->min_quantity."\n";
	$text.= " max_quantity      : ".$row->max_quantity."\n";
	$text.= " option1           : ".$row->option1."\n";
	$text.= " option2           : ".$row->option2."\n";
	$text.= " supply_subject    : ".$row->supply_subject."\n";
	pmysql_free_result( $res );
	// 상품옵션 로그
	$sql2 = "SELECT option_code, option_price, option_quantity, option_type, option_use ";
	$sql2.= "FROM tblproduct_option WHERE productcode = '".$prcode."' ORDER BY option_num ASC ";
	$res2 = pmysql_query( $sql2, get_db_conn() );
	$optionCnt = 1;
	while( $row2 = pmysql_fetch_object( $res2 ) ){
		$text.= "\n [ProductOptions] No. ".$optionCnt."\n";
		$text .= " option_code       : ".$row2->option_code."\n";
		$text .= " option_price      : ".$row2->option_price."\n";
		$text .= " option_quantity   : ".$row2->option_quantity."\n";
		$text .= " option_type       : ".$row2->option_type."\n";
		$text .= " option_use        : ".$row2->option_use."\n";
		$optionCnt++;
	}
	$text.= "\n";
	pmysql_free_result( $res2 );

}

function product_related($rmode,$prcode,$prname){ //관련 상품 함수 06 29 원재 ㅠㅠ
	
	if($rmode == "list"){//해당상품의 관련상품리스트 가져오긔
		
		$r_sql =" select pr.productname,pr.productcode,pr.tinyimage 
				from tblproduct pr
				join tblproduct_related r on pr.productcode = r.r_productcode
				where r.productcode='{$prcode}'
		";
		$r_result = pmysql_query($r_sql);
		while( $r_row = pmysql_fetch_object($r_result) ){
			$r_list[] = $r_row;
		}
		return $r_list;
	}

	if($rmode == "update"){
		$r_product = $_POST['relationProduct'];
		
		if(!$r_product && $prcode){
			$r_sql3 = " delete from tblproduct_related where productcode='{$prcode}' ";
			pmysql_query($r_sql3);

		}
		
		if($r_product && $prcode){
			//기존 관련상품 삭제
			$r_sql1 = " delete from tblproduct_related where productcode='{$prcode}' ";
			pmysql_query($r_sql1);

			//새로 등록할 관련상품 동작
			$r_sql2 = " insert into tblproduct_related (productcode,productname,r_productcode,sort) values ";
			foreach($r_product as $r_sort=>$r_val){
				$r_sql_values[] = " ('{$prcode}','{$prname}','{$r_val}',{$r_sort}) ";
			}
			$r_qry = implode(",",$r_sql_values);
			$r_sql2 .= $r_qry;
			pmysql_query($r_sql2);
		}
	}
}

function product_size($mode,$prcode,$use){ //사이즈 조견표 함수 07 05 원재 ㅠㅠ ( 사용안함)
	
	if($use=='N'){//사이즈 조견표 사용 안함시 동작
		$sql = " update tblproduct_size set use='N' where productcode='{$prcode}' ";
		pmysql_query($sql);
	}
		
	if($mode=='up' && $use !='N'){//사이즈 조견표 새로 삽입

		$sizex_sub = $_REQUEST['sizex_subj'];
		$sizey_sub = $_REQUEST['sizey_subj'];
		$size_content = $_REQUEST['size_content'];
		
		if($prcode){//기존에 있던 사이즈 조견표 삭제
			$sql = "delete from tblproduct_size where productcode='{$prcode}' ";
			pmysql_query($sql);
		}

		if($size_content && $sizex_sub && $sizey_sub){
			
			$sql = " insert into tblproduct_size ( productcode,type,rows,cols,text,use) values " ;

			foreach($size_content as $index_1=>$val_1){
				foreach($val_1 as $index_2=>$val_2){
					$content_qry[] = "('{$prcode}','C',{$index_1},{$index_2},'{$val_2}' ,'Y' )";
				}
			}

			foreach($sizex_sub as $index_x=>$xval){
				$content_qry[] = "('{$prcode}','X',{$index_x},0,'{$xval}' ,'Y' )";
			}

			foreach($sizey_sub as $index_y=>$yval){
				$content_qry[] = "('{$prcode}','Y',0,{$index_y},'{$yval}' ,'Y')";
			}

			$qry = implode(",",$content_qry);
			$sql .= $qry;
			pmysql_query($sql);
		}
		
	}

	if($mode=="list"){//사이즈 조견표 가져오기
		$sql = " select * from tblproduct_size where productcode='{$prcode}' ";
		$result = pmysql_query($sql);
		$product_size = array();
		while($row = pmysql_fetch_array($result) ){
			
			if($row['use']=='Y'){
				$product_size['use']['chk']='Y';
			}

			if($row['type']=='X'){
				$product_size['size_x'][] = $row;
			}
			if($row['type']=='Y'){
				$product_size['size_y'][] = $row;
			}
		
			if($row['type']=='C'){
				$product_size['content'] [$row['rows']] [$row['cols']] = $row;
			}
		}
		return $product_size;
	}
}

//exdebug($_POST);
//exdebug($_GET);

$sabangnet_flag=$_REQUEST["sabangnet_flag"];
$sabangnet_prop_option = $_REQUEST['sabangnet_prop_option'];

$category_data=$_REQUEST["category_data"];
$keyword=$_POST["keyword"];
$relation_tag=$_POST["relation_tag"];
$s_check=$_POST["s_check"];
$display=$_POST["display"];
$naver_display=$_POST["naver_display"];
$erp_price_yn = $_POST["erp_price_yn"];
$vip=$_POST["vip"];
$block=$_REQUEST["block"];
$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];
$search_end=$_POST["search_end"];
$search_start=$_POST["search_start"];
$sellprice_min=$_POST["sellprice_min"];
$sellprice_max=$_POST["sellprice_max"];
$code_type=$_POST["code_type"];
$code_area=$_POST["code_area"];
$listnum=$_POST["listnum"];
$hotdealyn=$_POST["hotdealyn"]?$_POST["hotdealyn"]:"N";

$option_cnt = $_POST["toption_qunatity"];

$sabangnet_prop_val=$_POST["sabangnet_prop_val"];
//exdebug($sabangnet_prop_val);
# 리스트에서 넘어온 값
$serial_value=$_POST["serial_value"];
# 제휴몰 리스트와 자사몰 리스트의 주소가 다름
$action_page=$_POST["action_page"];

################################################
$userspec_cnt=5;
//파일이미지 개별 1M 씩 총 3M 로 수정 이전 500K
$maxfilesize="3072000";
$mode=$_POST["mode"];
$category=$_POST["category"];
$position=$_POST["position"];

$checkmaxq=$_POST["checkmaxq"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];

$productcode=$prcode?$prcode:$_POST["productcode"];
    
if ($productcode) {
	//ERP 상품을 쇼핑몰에 업데이트한다.
	getUpErpProductUpdate($productcode);
}

$productname=$_POST["productname"];
$productname_kor=pmysql_escape_string($_POST["productname_kor"]);
$in_vender=$_POST["in_vender"];		// 선택된 벤더
$old_vender=$_POST["old_vender"]; // 현재 등록된 벤더
if( $in_vender == '0' || is_null($in_vender) ) $in_vender = 0;
if($productname){	//2014 12 30 상품이름 DB에 들가는 형태에 맞춤
	$productname = pmysql_escape_string($productname);
}
$vimage=$_POST["vimage"];
$vimage2=$_POST["vimage2"];
$vimage3=$_POST["vimage3"];
$vimage4=$_POST["vimage4"];
$vimage5=$_POST["vimage5"];
$start_no=$_POST["start_no"]?$_POST["start_no"]:"0";
$vip_product=$_POST["vip_product"]?$_POST["vip_product"]:"0";
//$staff_product=$_POST["staff_product"]?$_POST["staff_product"]:"0";
$changecode=$_POST["changecode"];
$code_a=$_REQUEST["code_a"]?$_REQUEST["code_a"]:substr($code,0,3);
$code_b=$_REQUEST["code_b"]?$_REQUEST["code_b"]:substr($code,3,3);
$code_c=$_REQUEST["code_c"]?$_REQUEST["code_c"]:substr($code,6,3);
$code_d=$_REQUEST["code_d"]?$_REQUEST["code_d"]:substr($code,9,3);
### 회원그룹별 할인율
$membergrplevel = $_POST[membergrplevel];
$memergrprt = $_POST[memergrprt];

if(count($memergrprt) > 0){
  for($i=0 ; $i < count($memergrprt) ; $i++){
    $membergrpdc .= $membergrplevel[$i]."-".$memergrprt[$i].";";
  }
}

if($changecode)$typecode=$changecode;
else $typecode=$code;
if(strlen($typecode)==12) {
	$sql = "SELECT type, list_type FROM tblproductcode WHERE code_a='".substr($typecode,0,3)."' ";
	$sql.= "AND code_b='".substr($typecode,3,3)."' ";
	$sql.= "AND code_c='".substr($typecode,6,3)."' AND code_d='".substr($typecode,9,3)."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	//productcode에 code가 없어도 이용가능함 (상품은 link를 이용함)
	//if(!$row) exit;
	//if(strpos($row->type,'X')===FALSE) exit;

	$type = $row->type;
	if ($row->list_type[0]=="B")
		$gongtype="Y";
	else
		$gongtype="N";

	if($gongtype=="Y") {
		$bgcolor="#E9E7F1";
		$list_title = "<B>가격 고정형 공동구매 상품목록</B>";
	} else {
		$bgcolor="#F0F0F0";
		$list_title = "<B>판매상품 목록</B>";
	}

}// else {
//	exit;
//}
$gongtype="N";

if(ord($_POST["setcolor"])==0){
	$setcolor=$_COOKIE["setcolor"];
} else if($_COOKIE["setcolor"]!=$_POST["setcolor"]){
	setcookie("setcolor",$setcolor,0,"/".RootPath.AdminDir);
	$setcolor=$_POST["setcolor"];
} else {
	$setcolor=$_COOKIE["setcolor"];
}

if( strlen( $setcolor ) > 0 ){
	$setcolor = substr( $setcolor, 1 );
}
#상품이미지 태두리 쿠키 설정
if ($_POST["imgborder"]=="Y" && $_COOKIE["imgborder"]!="Y") {
	setcookie("imgborder","Y",0,"/".RootPath.AdminDir);
} else if ($_POST["imgborder"]!="Y" && $_COOKIE["imgborder"]=="Y" && ($mode=="insert" || $mode=="modify")) {
	setcookie("imgborder","",time()-3600,"/".RootPath.AdminDir);
	$imgborder="";
} else {
	$imgborder=$_COOKIE["imgborder"];
}

#상품등록날짜 고정 쿠키 설정
if ($_COOKIE["insertdate_cook"]=="Y" && $insertdate!="Y" && $mode=="modify") {
	setcookie("insertdate_cook","",time()-3600,"/".RootPath.AdminDir);
	$insertdate_cook="";
} else if ($_COOKIE["insertdate_cook"]!="Y" && $insertdate=="Y" && $mode=="modify") {
	setcookie("insertdate_cook","Y",time()+2592000,"/".RootPath.AdminDir);
	$insertdate_cook="Y";
}
$insertdate_cook="Y";

$popup=$_POST["popup"];

#####사이즈 조견표 사용조건
$use_prsize = $_POST['use_prsize'];

##### 옵션 변경 2016-02-27 유동혁
$opt_select    = $_POST['opt_select']; // 옵션사용
$p_goods_code    =  $_POST['p_goods_code']; // 옵션 없을시 자체품목코드(20160610_김재수 추가)
$opt_type      = $_POST['opt_type']; // 옵션 구성방식 0 - 조합형, 1 - 독립형
# 조합형 옵션 2016-02-28유동혁
$opt_num       = $_POST['opt_num']; // 옵션 고유번호
$opt_subject   = $_POST['opt_subject']; // 옵션명
$opt_id        = $_POST['opt_id']; // 옵션코드
$opt_price     = $_POST['opt_price']; // 옵션가
$opt_stock_qty = $_POST['opt_stock_qty']; // 옵션 제고
$opt_noti_qty  = $_POST['opt_noti_qty']; // 옵션 안전제고
$opt_use       = $_POST['opt_use']; //옵션 사용 유무
# 고정옵션 2016-02-28유동혁
$spl_num             = $_POST['spl_num']; // 옵션 고유번호
$spl_option_code     = $_POST['spl_id'];	//옵션코드
$spl_option_price    = $_POST['spl_price'];	//가격
$spl_option_quantity = $_POST['spl_stock_qty'];	//수량
$spl_option_noti_qty = $_POST['spl_noti_qty']; // 안전재고
$spl_option_use      = $_POST['spl_use'];	//사용 유무
$spl_option_subject  = $_POST['spl_subject']; // 옵션명
$spl_option_name     = $_POST['spl'];	//항목명
$spl_tf              = $_POST['spl_tf']; //필수여부
$option1_tf          = $_POST['option1_tf']; // 필수여부
# 추가 옵션 2016-02-28유동혁
$addopt_select  = $_POST['addopt_select']; // 추가옵션 사용 1, null일경우 미사용
$addopt_subject = $_POST['addopt_subject']; // 추가옵션명
$addopt_type    = $_POST['addopt_type']; // 필수항목 선택 T - 필수, F - 선택
$addopt_maxln   = $_POST['addopt_maxln']; // 글자수 제한
# 옵션 자체 코드 2016-05-18
$opt_goods_code = $_POST['opt_goods_code']; //조합형 옵션 자체코드
$spl_goods_code = $_POST['spl_goods_code']; //고정 옵션 자체코드

# 상품 몰 타입 추가 2015 10 30 유동혁
$mall_type = $_POST['mall_type'];
$overseas_type = $_POST['overseas_type'];
$consumerprice=$_POST["consumerprice"];
$buyprice=$_POST["buyprice"];
$sellprice=$_POST["sellprice"];
$assembleuse=$_POST["assembleuse"];
$production=$_POST["production"];
$keyword=$_POST["keyword"];
$quantity=$_POST["quantity"];
if( $quantity == '' ) $quantity = 0;
#품절 추가 2016-03-09 유동혁
$soldout = $_POST['soldout'];
if( $soldout == '' ) $soldout = 'N';
$checkquantity=$_POST["checkquantity"];
$reserve=$_POST["reserve"];
$reservetype=$_POST["reservetype"];
$package_num=$_POST["package_num"];

$optnumvalue=$_POST["optnumvalue"];
$card_benefit = $_POST['card_benefit'];
#배송비 설정 2016-02-17 유동혁
$deli = $_POST["deli"];
$deli_qty = (int)$_POST['deli_qty'];
if( $deli_qty < 0 || is_null($deli_qty) ) $deli_qty = 0;
$deli_select = $_POST['deli_select'];
if( $deli_select < 0 || is_null($deli_select) ) $deli_select = 0;
$deli_price = $_POST['deli_price'];
if( $deli_price < 0 || $deli_price == '' ) $deli_price = 0;
/*
if($deli=="Y"){
	$deli_price=(int)$_POST["deli_price_value2"];
} else if ( $deli=="Z" ) { // 수량별 배송비 추가 2015 12 04 유동혁
	$deli_price=(int)$_POST["deli_price_value3"];
} else {
	$deli_price=(int)$_POST["deli_price_value1"];
}
if($deli=="H" || $deli=="F" || $deli=="G") $deli_price=0;
if($deli!="Y" && $deli!="F" && $deli!="G" && $deli!="Z" ) $deli="N";
*/
# 마진률 추가
$vender_rate = $_POST['vender_rate'];
$rate = $_POST['rate'];
if( $vender_rate == '1' && $in_vender != '0' ) {
    $rate_row = pmysql_fetch_object( pmysql_query( "SELECT rate FROM tblvenderinfo WHERE vender ='".$in_vender."' ") );
    $rate = $rate_row->rate;
    if( $rate == '' ) $rate = 0;
} else {
    if( $rate > 100 ) $rate = 100;
    else if( $rate < 0 || $rate == '' ) $rate = 0;
}
$display=$_POST["display"];
$addcode=$_POST["addcode"];
$option_price=str_replace(" ","",$_POST["option_price"]);
$option_price=rtrim($option_price,',');
$option_ea=str_replace(" ","",$_POST["option_ea"]);
$option_ea=rtrim($option_ea,',');
$option_consumer=str_replace(" ","",$_POST["option_consumer"]);
$option_consumer=rtrim($option_consumer,',');

$optcode=str_replace(" ","",$_POST["optcode"]);
$optcode=rtrim($optcode,',');
$optreserve=str_replace(" ","",$_POST["optreserve"]);
$optreserve=rtrim($optreserve,',');
$madein=$_POST["madein"];
$model=$_POST["model"];
$brandname=$_POST["brandname"];
$brand_idx=$_POST["brand_idx"];

$opendate=$_POST["opendate"];
$selfcode=$_POST["selfcode"];
//$bisinesscode=$_POST["bisinesscode"];
$optiongroup=$_POST["optiongroup"];
$imgcheck=$_POST["imgcheck"];
$bankonly=$_POST["bankonly"];
$deliinfono=$_POST["deliinfono"];
$setquota=$_POST["setquota"];
$miniq=$_POST["miniq"];
$maxq=$_POST["maxq"];
$insertdate=$_POST["insertdate"];
$localsave=$_POST["localsave"];
$content=$_POST["content"];
$content_m=$_POST["content_m"];
$pr_notice=$_POST["pr_notice"];
$pr_sizecon=$_POST["pr_sizecon"];
$pr_sizecon_m=$_POST["pr_sizecon_m"];
$pr_content=$_POST["pr_content"];
$dicker=$_POST["dicker"];
$dicker_text=$_POST["dicker_text"];
$iconvalue=$_POST["iconvalue"];
$userspec=$_POST["userspec"];
$specname=$_POST["specname"];
$specvalue=$_POST["specvalue"];

//$group_check=$_POST["group_check"];
//$group_code=$_POST["group_code"];
#그룹별 판매 없음 2015 12 01 유동혁
$group_check="N";
$delprdtimg=$_POST["delprdtimg"];
$mdcomment = pmysql_escape_string( $_POST["mdcomment"] );
$up_mdcommentcolor = $_POST['mdcommentcolor'];
$smart_search_color = $_POST["smart_search_color"];
$sex = $_POST["sex"]?$_POST["sex"]:"U";

/*
if($group_check=="Y" && count($group_code)>0) {
	$group_check="Y";
} else {
	$group_check="N";
	$group_code="";
}
*/
$specarray='';
if($userspec == "Y") {
	for($i=0; $i<$userspec_cnt; $i++) {
		$specarray[$i]=$specname[$i]."".$specvalue[$i];
	}
	$userspec = implode("=",$specarray);
} else {
	$userspec = "";
}

//if(ord($display)==0) $display='Y';
if(ord($display)==0) $display='N';

if((int)$opendate<1) $opendate="";


$userfile = $_FILES["userfile"];
$userfile2 = $_FILES["userfile2"];
$userfile3 = $_FILES["userfile3"];
$userfile4 = $_FILES["userfile4"];


$use_imgurl=$_POST["use_imgurl"];
$userfile_url=$_POST["userfile_url"];
$userfile2_url=$_POST["userfile2_url"];
$userfile3_url=$_POST["userfile3_url"];
$userfile4_url=$_POST["userfile4_url"];


if($use_imgurl!="Y") {
	$userfile_url="";
	$userfile2_url="";
	$userfile3_url="";
	$userfile4_url="";
}

// ===================================================
// 리뷰 배너 이미지
// ===================================================
$review_banner_img      = $_FILES["review_banner_img"];
$old_review_banner_img  = $_POST["old_review_banner_img"];

$review_banner_image = "";
if ( !empty($review_banner_img['name']) ) {
    $reviewbannerimagepath  = $Dir.DataDir."shopimages/product_review_banner/";

    if ( !empty($old_review_banner_img) && file_exists($reviewbannerimagepath.$old_review_banner_img) ) {
        unlink($reviewbannerimagepath.$old_review_banner_img);
    }

    $ext = strtolower(pathinfo($review_banner_img['name'],PATHINFO_EXTENSION));
    
    if ( !empty($productcode) ) {
        $review_banner_image = "review_banner_{$productcode}_" . date("YmdHis") . ".".$ext;
    } else {
        $review_banner_image = "review_banner_" . date("YmdHis") . ".".$ext;
    }

    $tmp_image  = $review_banner_img['tmp_name'];

    move_uploaded_file($tmp_image,$reviewbannerimagepath.$review_banner_image);
    chmod($reviewbannerimagepath.$review_banner_image, 0604);
}

$maxsize=150;
$makesize=150;

$card_splittype = $_shopdata->card_splittype;
$makesize=$_shopdata->primg_minisize;
//$predit_type=$_shopdata->predit_type;
$maxsize=$makesize+10;
if(strpos(" ".$_shopdata->etctype,"IMGSERO=Y")) {
	$imgsero="Y";
}

if (ord($mode)==0) $maxq="";
$up_bankonly = 'N';
$up_deliinfono = 'N';
$up_setquota = 'N';
$up_miniq = 0;
$up_maxq = 0;

if ($mode=="insert" || $mode=="modify") {
	$etctype = "";
	if ($bankonly=="Y") {
		$etctype .= "BANKONLY";
		$up_bankonly = 'Y';
		
	} else if ($deliinfono=="Y") { 
		$etctype .= "DELIINFONO=Y";
		$up_deliinfono = 'Y';
	} else if ($setquota=="Y") {
		$etctype .= "SETQUOTA";
		$up_setquota = 'Y';
	}

	if (ord(substr($iconvalue,0,3))) {
		$etctype .= "ICON={$iconvalue}";
		$up_icon = $iconvalue;
	}
	if ($dicker=="Y" && ord($dicker_text)) {
		$etctype .= "DICKER={$dicker_text}";
		$up_dicker = $dicker_text;
	}
	if ($miniq>1) {
		$etctype .= "MINIQ={$miniq}";
		$up_miniq = $miniq;
	} else if ($miniq<1){
		alert_go('최소구매수량 수량은 1개 보다 커야 합니다.',-1);
	}
	if ($checkmaxq=="B" && $maxq>=1) {
		$etctype .= "MAXQ={$maxq}";
		$up_maxq = $maxq;
	} else if ($checkmaxq=="B" && $maxq<1){
		alert_go('최대구매수량 수량은 1개 보다 커야 합니다.',-1);
	}

	if ($bankonly=="Y" && $setquota=="Y") {
		alert_go('현금전용결제와 무이자상점부담을 동시에 체크하실 수 없습니다.(결제수단이 서로 틀림)',-1);
	}
} else {
	$bankonly="";
	$deliinfono="";
	$setquota="";
	$miniq="";
	$freedeli="N";
}

$imagepath=$Dir.DataDir."shopimages/product/";

if($mode=="delprdtimg"){
	$delarray = array (&$vimage,&$vimage2,&$vimage3,&$vimage4);
	$delname = array ("maximage","minimage","tinyimage","over_minimage"); //유동혁 수정 준비

	if(ord($delarray[$delprdtimg]) && file_exists($imagepath.$delarray[$delprdtimg])) {
		unlink($imagepath.$delarray[$delprdtimg]);
	}

	$sql = "UPDATE tblproduct SET ".$delname[$delprdtimg]."='' WHERE productcode = '{$prcode}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>alert('해당 상품이미지를 삭제하였습니다.');</script>";
	//$prcode="";
}

//exdebug($prcode);

//앞으로 옵션은 옵션명만 넣는다 구분자 @#
//2016-01-25 유동혁


//$option2 = $opt2_subject;

$optcnt="";
$tempcnt=0;

// 추가옵션의 이름을 합친다 2015 10 28 유동혁

if( count($opt_subject) > 0 && $opt_type == '0' ){
    $tf_arr = array();
	$option1 = implode( '@#', $opt_subject  );
    for( $i=0; $i<count($opt_subject); $i++ ){
        $tf_arr[] = 'T';
    }
    $option1_tf = implode( '@#', $tf_arr );
} else if( count($spl_option_subject) > 0 && $opt_type == '1' ) {
	$option1 = implode( '@#', $spl_option_subject );
} else {
	$option1 = '';
    $option1_tf = '';
}

if ( $addopt_select == '1' && count( $addopt_subject ) > 0 ){
	$option2 = implode( '@#', $addopt_subject );
	$option2_maxlen = implode( '@#', $addopt_maxln );
	$option2_tf = implode( '@#', $addopt_type );
} else {
	$option2 = '';
	$option2_maxlen = '';
	$option2_tf = '';

}


if($mode=="insert") {
	$sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct ";
	$sql.= "WHERE productcode LIKE '{$code}%' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($rows = pmysql_fetch_object($result)) {
		if (strlen($rows->maxproductcode)==18) {
			$productcode = ((int)substr($rows->maxproductcode,12))+1;
			$productcode = sprintf("%06d",$productcode);
		} else if($rows->maxproductcode==NULL){
			$productcode = "000001";
		} else {
			alert_go('상품코드를 생성하는데 실패했습니다. 잠시후 다시 시도하세요.',-1);
		}
		pmysql_free_result($result);
	} else {
		$productcode = "000001";
	}
}

if ($mode=="insert") {
//	$image_name = $code.$productcode;
    $image_dir = $code.$productcode;
	$image_name = $code.$productcode."_".date("YmdHis");	
	$p_goods_code	= $p_goods_code?$p_goods_code:$code.$productcode; // 옵션 없을시 자체품목코드 - 자체품목코드를 입력안했을경우에는 상품코드로 (20160610_김재수 추가)
} elseif ($mode=="modify") {
//	$image_name = $prcode;
    $image_dir = $prcode;
	$image_name = $prcode."_".date("YmdHis");
	$p_goods_code	= $p_goods_code?$p_goods_code:$prcode; // 옵션 없을시 자체품목코드 - 자체품목코드를 입력안했을경우에는 상품코드로 (20160610_김재수 추가)
}

if ($mode=="insert" || $mode=="modify") {
	$p_goods_code  = $opt_select!='1'?$p_goods_code:''; // 옵션 없을시 자체품목코드 있을시에는 공백(20160610_김재수 추가)
	if ($mode=="modify") $pgc_where	= " AND productcode != '".$prcode."' ";
	list($p_goods_code_cnt)=pmysql_fetch("
												select count(*) from ( 
													select '1' as op_type, vender, productcode, self_goods_code, pridx as idx from tblproduct where self_goods_code !='' 
													union select '2' as op_type, tp.vender, tp.productcode, tpo.self_goods_code, tpo.option_num as idx from tblproduct_option as tpo left join tblproduct as tp on tpo.productcode=tp.productcode where tpo.self_goods_code !='' 
												) AS a where vender='{$in_vender}' AND self_goods_code='{$p_goods_code}' {$pgc_where} ");
	if ($p_goods_code_cnt > 0) alert_go('존재하는 자체품목 코드 입니다.',-1);
}

if($use_imgurl!="Y") {
	$file_size = $userfile['size']+$userfile2['size']+$userfile3['size']+$userfile4['size'];
} else {
	$file_size=0;
}

if($file_size < $maxfilesize) {
	if (ord($reserve)==0) {
		$reserve=0;
	}

	if ($reservetype!="N") {
		$reservetype=="Y";
	}

	$curdate = date("YmdHis");

	$productname = str_replace("\\\\'","''",$productname);
	$addcode = str_replace("\\\\'","''",$addcode);
	//$content = str_replace("\\\\'","''",$content);

	$message="";

	if($use_imgurl!="Y") {
		if($imgcheck=="Y") $filename = array (&$userfile['name'],&$userfile['name'],&$userfile['name'],&$userfile['name']);
		else $filename = array (&$userfile['name'],&$userfile2['name'],&$userfile3['name'],&$userfile4['name']);
		$file = array (&$userfile['tmp_name'],&$userfile2['tmp_name'],&$userfile3['tmp_name'],&$userfile4['tmp_name']);
	} else {
		if($imgcheck=="Y") $filename = array (&$userfile_url,&$userfile_url,&$userfile_url,&$userfile_url);
		else $filename = array (&$userfile_url,&$userfile2_url,&$userfile3_url,&$userfile4_url);
		$file = array (&$userfile_url,&$userfile2_url,&$userfile3_url,&$userfile4_url);
	}

	$vimagear = array (&$vimage,&$vimage2,&$vimage3,&$vimage4);
	#DB에 올릴 이미지 경로
	$up_ImagePath = array ( '', '', '', '', '' );
	# 이미지 지정사이즈 ( 유동혁 2015 10 29 );
	$thumbnailArr = array( 
		1=>array('width'=>900,'height'=>900),
		2=>array('width'=>470,'height'=>470), 
		3=>array('width'=>150,'height'=>150),
		//4=>array('width'=>500,'height'=>500)
	);
	if($mode=="insert" || $mode=="modify"){
		# 이미지 폴더 생성
		if( !is_dir( $imagepath.$image_dir ) ){
			mkdir( $imagepath.$image_dir, 0700 );
			chmod( $imagepath.$image_dir, 0777 );
		}
		// 경로지정
		$imagepath2=$Dir.DataDir."shopimages/product/".$image_dir."/";

		# 대이미지 수정일경우 원본이미지를 삭제 후 올림
		/*
		if ($mode=="modify" && ord($vimagear[0]) && ord($filename[0]) && file_exists($imagepath.$vimagear[0]) && file_exists() ) {
			unlink( $Dir.DataDir."shopimages/product/".$vimagear[0] );
		}
		*/

		# 원본 이미지 업로드
		if (ord($filename[0]) && file_exists($file[0])) {
			$ext = strtolower(pathinfo($filename[0],PATHINFO_EXTENSION));
			if(in_array($ext,array('gif','jpg', 'png'))) {
				$image[0] = $image_name.".".$ext;
//				move_uploaded_file($file[0],$imagepath2.$image[0]);
				copy($file[0],$imagepath2.$image[0]);
				chmod($imagepath2.$image[0],0777);
                //msg("upload");
			} else {
				$image[0]="";
			}
		} else {
			$image[0] = $vimagear[0];
		}
		
		#썸네일 생성
		if( $imgcheck=="Y" && ord( $image[0] ) ){
			for( $i = 1; $i < 5; $i++ ){  
                
                if ($mode=="modify" && ord($vimagear[($i-1)]) && file_exists($imagepath.$vimagear[($i-1)])) { 
                    //msg($vimagear[($i-1)]);
                    if($i < 4) { // 4번째는 png 이미지이므로 자동생성되지 않으므로..
                        unlink( $Dir.DataDir."shopimages/product/".$vimagear[($i-1)] );
                    } else {
                        // 4번째 png 이미지가 업로드 되었으면 기존 파일 삭제한다.
                        if( file_exists($file[($i-1)]) ) {
                            unlink( $Dir.DataDir."shopimages/product/".$vimagear[($i-1)] );
                        }
                    }
                }

                if($i == 4) {
                    //msg( strtolower(pathinfo($filename[($i-1)],PATHINFO_EXTENSION)) ); 
                    //msg($filename[($i-1)]);
                }
				if (ord($filename[($i-1)]) && file_exists($file[($i-1)])) {	//사용자 이미지 넣기
					# 기존 이미지가 존재할경우 삭제하고 넣는다
                    if($i == 4) $ext = "png";   // 4번째는 무조건 png 이므로..
                    else $ext = strtolower(pathinfo($filename[($i-1)],PATHINFO_EXTENSION));
                    //$ext = strtolower(pathinfo($filename[($i-1)],PATHINFO_EXTENSION));
					if(in_array($ext,array('gif','jpg', 'png'))) {
                        //msg("1-1");
						$image[$i] = $image_name."_thum".$i."_".$thumbnailArr[$i]['width']."X".$thumbnailArr[$i]['height'].".".$ext;
						move_uploaded_file($file[($i-1)],$imagepath2.$image[$i]);
						chmod($imagepath2.$image[$i],0777);
						$up_ImagePath[$i] = $image_dir."/"; //DB에 업로드한 경로를 같이 넣어준다
					} else {
                        //msg("1-2");
						$image[$i]="";
					}
				} else { // 썸네일 생성
                    //msg("2");
                    if($i < 4) {
                        $image[$i] = $image_name."_thum".$i."_".$thumbnailArr[$i]['width']."X".$thumbnailArr[$i]['height'].".".$ext;
                        copy($imagepath2.$image[0],$imagepath2.$image[$i]);
                        # 썸네일 이미지 크기 리사이징
                        ProductThumbnail ( $image_name, $filename[0], $image[$i], $thumbnailArr[$i]['width'],  $thumbnailArr[$i]['height'], $imgborder, $setcolor, $image_dir );
                        $up_ImagePath[$i] = $image_dir."/"; //DB에 업로드한 경로를 같이 넣어준다
                    } else {
                        // 4번째는 썸네일을 생성하면 안되므로..
                        if( file_exists($imagepath.$vimagear[($i-1)]) ) {
                            $up_ImagePath[$i] = ""; //DB에 업로드한 경로를 같이 넣어준다
                            $image[$i] = $vimagear[($i-1)]; // 업로드된 파일이 없고, 기존에 이미 업로드된 파일이 있으면 기존 이미지명을 저장해야 되므로..
                        } else {
                            $up_ImagePath[$i] = "";
                            $image[$i] = "";
                        }
                    }
				}
			}
		} else { // 개별 업로드
			for( $i = 1; $i < 5; $i++ ){
				if (ord($filename[($i-1)]) && file_exists($file[($i-1)])) {	//사용자 이미지 넣기
					# 기존 이미지가 존재할경우 삭제하고 넣는다
					if ($mode=="modify" && ord($vimagear[($i-1)]) && file_exists($imagepath.$vimagear[($i-1)])) { 
						unlink( $Dir.DataDir."shopimages/product/".$vimagear[($i-1)] );
					}
					$ext = strtolower(pathinfo($filename[($i-1)],PATHINFO_EXTENSION));
					if(in_array($ext,array('gif','jpg','png'))) {
						$image[$i] = $image_name."_thum_".$i.".".$ext;
						move_uploaded_file($file[($i-1)],$imagepath2.$image[$i]);
						chmod($imagepath2.$image[$i],0777);
						$up_ImagePath[$i] = $image_dir."/"; //DB에 업로드한 경로를 같이 넣어준다
					} else {
						$image[$i]="";
					}
				} else {
					$image[$i] = $vimagear[($i-1)];
				}
			}
		}

		# URL 업로드
		if( $use_imgurl == 'Y' ) {
			for( $i = 1; $i < 5; $i++ ){
				if(ord($filename[($i-1)]) && ord($file[($i-1)])) {
					$up_ImagePath[$i]	= $file[($i-1)];
					$image[$i]				= "";
					/*$image_url=str_replace("http://","",$file[($i-1)]);
					$temp=explode("/",$image_url);
					$host=$temp[0];
					$path=str_replace($host,"",$image_url);

					$ext = strtolower(pathinfo($image_url,PATHINFO_EXTENSION));

					$is_upimage=true;
					if(in_array($ext,array('gif','jpg'))) {
						$image[$i] = $image_name."_thum_".$i.".".$ext;
						//$image[$i] = $image_name.$imgnum[$i].".".$ext;
						$fdata=getRemoteImageData($host,$path,$ext);

						if(ord($fdata)) {
							file_put_contents($imagepath2.$image[$i],$fdata);
							chmod($imagepath2.$image[$i],0777);
							$up_ImagePath[$i] = $image_name."/"; //DB에 업로드한 경로를 같이 넣어준다
							$tempsize=@getimagesize($imagepath2.$image[$i]);
							if($tempsize[0]>0 && $tempsize[1]>0 && (strstr("12",$tempsize[2]))) {

							} else {
								@unlink($imagepath2.$image[$i]);
								$is_upimage=false;
							}
						} else {
							$is_upimage=false;
						}
					} else {
						$is_upimage=false;
					}

					if($is_upimage==false) {
						$image[$i]="";
						$filename[($i-1)]="";
					}*/
				} else if($imgcheck=="Y" && ord($filename[($i-1)])) {
					$up_ImagePath[$i]	= $file[0];
					$image[$i]				= "";
					/*$image[$i]=$image_name."_thum_".$i.".".$ext;
					copy($imagepath2.$image[0],$imagepath.$image[$i]);
					chmod($imagepath2.$image[$i],0777);
					$up_ImagePath[$i] = $image_name."/"; //DB에 업로드한 경로를 같이 넣어준다*/
				} else {
					$image[$i] = $vimagear[($i-1)];
				}
			}
		}
		
		if($checkquantity=="F") $quantity=999999999;
		
	} else if($mode=="delete"){
		for($i=0;$i<4;$i++){
			if(ord($vimagear[$i]) && file_exists($imagepath.$vimagear[$i]))
				unlink($imagepath.$vimagear[$i]);
		}
	}

	####################### 타서버 이미지 쇼핑몰에 저장 #############################
	for($k=0;$k < 2;$k++) {
		$arrimgurl='';
		$arrsavefilename=array();
		if ($k == 0) {
			$in_content=$content;
		} else if ($k == 1) {
			$in_content_m=$content_m;
		}
		if($mode=="insert" || $mode=="modify") {
			if($localsave=="Y") {
				$imagesavepath=$Dir.DataDir."design/etc/";
				if(is_dir($imagesavepath)==false) {
					mkdir($imagesavepath);
					chmod($imagesavepath,0755);
				}
				$arrimgurl=array();
				//$cln=explode("\n", $in_content);
				if ($k == 0) {
					$cln=$in_content;
				} else if ($k == 1) {
					$cln=$in_content_m;
				}
				for($i=0;$i<count($cln);$i++) {
					while(preg_match("/[^= \"']*\.(gif|jpg|bmp|png)([\"|\'| |>]){1}/i", $cln[$i], $imgval)){
						$imgval[0] = rtrim($imgval[0],$imgval[2]);
						$arrimgurl[$imgval[0]]=$imgval;
						$cln[$i]=str_replace($imgval,"",$cln[$i]);
					}
				}
				if(count($arrimgurl)>0) {
					while(list($key,$val)=each($arrimgurl)) {
						$file_url=urldecode($val);
						if(substr($file_url,0,7)=="http://") {
							$file_url=str_replace("http://","",$file_url);
							$temp=explode("/",$file_url);
							$host=$temp[0];
							$path=str_replace($host,"",$file_url);

							$filename=basename($file_url);
							$j=0;
							while(file_exists($imagesavepath.$filename)){
								$file_ext = pathinfo($filename,PATHINFO_EXTENSION);
								$file_name = pathinfo($filename,PATHINFO_FILENAME);
								$file_name=substr($file_name, 0, strlen($file_name) - strlen(strrchr($file_name,"[")));

								$filename=$file_name."[{$j}].".$file_ext;
								$j++ ;
							}

							$ext = strtolower(pathinfo($filename,PATHINFO_EXTENSION));

							$fdata=getRemoteImageData($host,$path,$ext);

							if(ord($fdata)) {
								$filepath=$imagesavepath.$filename;
								file_put_contents($filepath,$fdata);
								@chmod($filepath,0604);

								$size=@getimagesize($filepath);

								if($size[0]>0 && $size[1]>0 && (strstr("1236",$size[2]))) {
									$arrsavefilename[]=$filename;
									if ($k == 0) {
										$in_content=@str_replace($val,"/".RootPath.DataDir."design/etc/".$filename,$in_content);
									} else if ($k == 1) {
										$in_content_m=@str_replace($val,"/".RootPath.DataDir."design/etc/".$filename,$in_content_m);
									}
								} else {
									@unlink($filepath);
								}
							}
						}
					}
				}
			}
		}
	}

	if ($mode=="insert") {
		if(strlen($buyprice) < 1 ) $buyprice = 0 ;
		$result = pmysql_query("SELECT COUNT(*) as cnt FROM tblproduct ",get_db_conn());
		if ($row=pmysql_fetch_object($result)) $cnt = $row->cnt;
		else $cnt=0;
		pmysql_free_result($result);

		$date1=date("Ym");
		$date=date("dHis");

		$in=0;
		foreach($category as $k){
			if($in==0){
				$maincate="1";
			}else{
				$maincate="0";
			}
			$query="insert into tblproductlink (c_productcode,c_category,c_maincate,c_date,c_date_1,c_date_2,c_date_3,c_date_4) values ('".$code.$productcode."','".$k."','".$maincate."','".$date1.$date."','".$date1.$date."','".$date1.$date."','".$date1.$date."','".$date1.$date."')";

			pmysql_query($query);
			$in++;
		}

		if($assembleuse=="Y") {
			$sellprice=0;
			$option1="";
			$option2="";
			$option_price="";
			$option_ea="";
			$option_consumer="";
			$optcode="";
			$optreserve="";
			$optcnt="";
			$package_num="0";
		}



		$in_content=pg_escape_string($in_content);
		$in_content_m=pg_escape_string($in_content_m);
		$pr_notice=pg_escape_string($pr_notice);
		$pr_sizecon=pg_escape_string($pr_sizecon);
		$pr_sizecon_m=pg_escape_string($pr_sizecon_m);
        $pr_content=pg_escape_string($pr_content);
		$keyword=pg_escape_string($keyword);
		$relation_tag= pg_escape_string($relation_tag);
		$sql = "INSERT INTO tblproduct(
		productcode	,
		productname	,
		productname_kor	,
		assembleuse	,
		assembleproduct	,
		sellprice	,
		consumerprice	,
		buyprice	,
		reserve		,
		reservetype	,
		production	,
		madein		,
		model		,
		opendate	,
		selfcode	,
		quantity	,
		group_check	,
		keyword		,
		addcode		,
		userspec	,
		maximage	,
		minimage	,
		tinyimage	,
		over_minimage	,
		option_price	,
		option_quantity	,
		option1		,
		option2		,
		sabangnet_flag,
		etctype		,
		deli_price	,
		deli		,
		package_num	,
		display		,
		date		,
		regdate		,
		modifydate	,
		content,
		content_m,
		membergrpdc,
		optcode,
		option_reserve,
		option_consumer,
		start_no,
		vip_product,
		option_ea,
		dctype,
		position,
		mdcomment,
		color_code,
		sabangnet_prop_val,
		sabangnet_prop_option,
		bankonly,
		deliinfono,
		icon,
		dicker,
		min_quantity,
		max_quantity,
		setquota,
		supply_subject,
		mall_type,
		overseas_type,
		card_benefit,
		vender,
		deli_qty,
		deli_select,
		option2_maxlen ,
		option2_tf,
		mdcommentcolor,
		option_type,
		soldout,
        option1_tf,
        /*self_goods_code,*/
        rate,
		pr_sizecon,
		pr_sizecon_m,
		pr_notice,
		relation_tag, 
        sex,
		hotdealyn, 
        pr_content, 
        naver_display, 
        erp_price_yn 
		) VALUES (
		'".$code.$productcode."',
		'{$productname}',
		'{$productname_kor}',
		'{$assembleuse}',
		'',
		{$sellprice},
		{$consumerprice},
		{$buyprice},
		'{$reserve}',
		'{$reservetype}',
		'{$production}',
		'{$madein}',
		'{$model}',
		'{$opendate}',
		'{$selfcode}',
		{$quantity},
		'{$group_check}',
		'{$keyword}',
		'{$addcode}',
		'{$userspec}',
		'".$up_ImagePath[1].$image[1]."',
		'".$up_ImagePath[2].$image[2]."',
		'".$up_ImagePath[3].$image[3]."',
		'".$up_ImagePath[4].$image[4]."',
		'{$option_price}',
		'{$optcnt}',
		'{$option1}',
		'{$option2}',
		'{$sabangnet_flag}',
		'{$etctype}',
		'{$deli_price}',
		'{$deli}',
		'".(int)$package_num."',
		'{$display}',
		'{$curdate}',
		now(),
		now(),
		'{$in_content}',
		'{$in_content_m}',
		'{$membergrpdc}',
		'{$optcode}',
		'{$optreserve}',
		'{$option_consumer}',
		'{$start_no}',
		'{$vip_product}',
		'{$option_ea}',
		'".$_POST['dctype']."',
		'{$position}',
		'{$mdcomment}',
		'{$smart_search_color}',
		'{$sabangnet_prop_val}',
		'{$sabangnet_prop_option}',
		'{$up_bankonly}',
		'{$up_deliinfono}',
		'{$up_icon}',
		'{$up_dicker}',
		'{$up_miniq}',
		'{$up_maxq}',
		'{$up_setquota}',
		'{$supply_subject}',
		'{$mall_type}',
		'{$overseas_type}',
		'{$card_benefit}',
		'{$in_vender}',
		'{$deli_qty}',
		'{$deli_select}',
		'{$option2_maxlen}',
		'{$option2_tf}',
		'{$up_mdcommentcolor}',
		'{$opt_type}',
		'{$soldout}',
        '{$option1_tf}',
        /*'{$p_goods_code}',*/
        '{$rate}',
		'{$pr_sizecon}',
		'{$pr_sizecon_m}',
		'{$pr_notice}',
		'{$relation_tag}', 
        '{$sex}',
		'{$hotdealyn}', 
        '{$pr_content}', 
        '{$naver_display}', 
        '{$erp_price_yn}' 
		)";
		
		if($insert = pmysql_query($sql,get_db_conn())) {
			
			product_related('update',$code.$productcode,$productname); //관련상품이 있을경우 관련상품 등록하여줍니다.
			
			//product_size("up",$code.$productcode,$use_prsize);//사이즈 조견표 등록

			##### 브랜드 관련 처리	
			//---------------------------------------------------//
			// 벤더에 해당하는 브랜드를 등록한다.
			// 그리고 상품별 노출 브랜드에도 등록한다.
			// 2016.01.22 - 김재수

			if ($in_vender) {
				list($bridx)=pmysql_fetch("SELECT bridx FROM tblproductbrand WHERE vender='{$in_vender}'");
				if ($bridx>0) {
					@pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '".$code.$productcode."'",get_db_conn());

					$bpSql = "INSERT INTO tblbrandproduct(productcode, bridx, sort) VALUES ('".$code.$productcode."','".$bridx."','1')";			
					pmysql_query($bpSql,get_db_conn());
				}			
			}

			/*if(ord($brandname)) {
				$result = pmysql_query("SELECT bridx FROM tblproductbrand WHERE brandname = '{$brandname}' ",get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					@pmysql_query("UPDATE tblproduct SET brand = '{$row->bridx}' WHERE productcode = '".$code.$productcode."'",get_db_conn());
				} else {
					$sql = "INSERT INTO tblproductbrand(brandname) VALUES ('{$brandname}') RETURNING bridx";
					if($brandinsert = @pmysql_query($sql,get_db_conn())) {
						$row = pmysql_fetch_array($brandinsert);
						$bridx = $row[0];
						if($bridx>0) {
							@pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '".$code.$productcode."'",get_db_conn());
						}
					}
				}
				pmysql_free_result($result);
			}*/


			$content=$in_content;
			$content_m=$in_content_m;
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";


			
			$onload="<script>alert(\"상품이 등록이 완료되었습니다.$message\");</script>";


			$log_content = "## 상품입력 ## - 코드 $code$productcode - 상품 : $productname 코디/조립 : $assembleuse 패키지그룹 : $package_num 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금: $reserve $display";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		} else {

				$onload="<script>alert(\"상품 등록중 오류가 발생하였습니다.\");</script>";

		}
		$prcode=$code.$productcode;
	} else if ($mode=="delete") {
		$sql = "SELECT vender,display,brand,pridx,assembleuse,assembleproduct FROM tblproduct WHERE productcode = '{$prcode}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		$vender=(int)$row->vender;
		$vdisp=$row->display;
		$brand=$row->brand;
		$vpridx=$row->pridx;
		$vassembleuse=$row->assembleuse;
		$vassembleproduct=$row->assembleproduct;

		#태그관련 지우기
		$sql = "DELETE FROM tbltagproduct WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#리뷰 지우기
		$sql = "DELETE FROM tblproductreview WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#위시리스트 지우기
		$sql = "DELETE FROM tblwishlist WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#관련상품 지우기
		$sql = "DELETE FROM tblcollection WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());
		
		#테마상품 지우기
		$sql = "DELETE FROM tblproducttheme WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#메인상품 진열리스트 삭제 ( 진열상품 )
		//$sql = "DELETE FROM tblmainlist WHERE pridx = '".$vpridx."' ";
		//pmysql_query($sql,get_db_conn());

		#메인 메뉴 상품 진열리스트 삭제 ( 진열상품 )
		//$sql = "DELETE FROM tblmainmenulist WHERE pridx = '".$vpridx."' ";
		//pmysql_query($sql,get_db_conn());
		
		#카테고리별 상품 리스트 삭제 ( 진열상품 )
		//$sql = "DELETE FROM tblrecommendlist WHERE pridx = '".$vpridx."' ";
		//pmysql_query($sql,get_db_conn());
		
		#상품삭제
		$sql = "DELETE FROM tblproduct WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#카테고리 삭제
		$sql = "DELETE FROM tblproductlink WHERE c_productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#옵션 삭제
		$sql = "DELETE FROM tblproduct_option WHERE productcode = '".$prcode."' ";
		pmysql_query( $sql, get_db_conn() );

		#상품접근권한 지우기
		$sql = "DELETE FROM tblproductgroupcode WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#상품별 노출 브랜드 삭제(2016.01.22 - 김재수)
		$sql = "DELETE FROM tblbrandproduct WHERE productcode='".$prcode."'";
		pmysql_query($sql,get_db_conn());	

		if($vender>0) {
			//미니샵 테마코드에 등록된 상품 삭제
			//setVenderThemeDeleteNor($prcode, $vender);
			setVenderCountUpdateMin($vender, $vdisp);

			$tmpcode_a=substr($prcode,0,3);
			$sql = "SELECT COUNT(*) as cnt FROM tblproduct ";
			$sql.= "WHERE productcode LIKE '{$tmpcode_a}%' AND vender='{$vender}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prcnt=$row->cnt;
			pmysql_free_result($result);

			/*if($prcnt==0) {
				setVenderDesignDeleteNor($tmpcode_a, $vender);
				$imagename=$Dir.DataDir."shopimages/vender/{$vender}_CODE10_{$tmpcode_a}.gif";
				@unlink($imagename);
			}*/
		}

		$log_content = "## 상품삭제 ## - 상품코드 $prcode - 상품명 : ".urldecode($productname)." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		delProductMultiImg("prdelete","",$prcode);


		$onload="<script>alert(\"상품 삭제가 완료되었습니다.\");window.close();opener.location.reload()</script>";
		$prcode="";

	} else if ($mode=="modify") {

		#카테고리의 c_date 정보
		$c_date_sql = "SELECT c_category, c_date FROM tblproductlink WHERE c_productcode = '{$prcode}' ";
		$c_date_res = pmysql_query($c_date_sql,get_db_conn());
		while($c_date_row = pmysql_fetch_object($c_date_res)){
			$c_date[$c_data_row->c_category] = $c_date_row->c_date;
		}

		#카테고리 삭제
		$sql = "DELETE FROM tblproductlink WHERE c_productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		$in=0;
		foreach($category as $k){
			if($in==0){
				$maincate="1";
			}else{
				$maincate="0";
			}

			$date1=date("Ym");
			$date=date("dHis");

			//c_date 생성
			$c_date[$k] = ($c_date[$k])?$c_date[$k]:$date1.$date;

			$query="insert into tblproductlink (c_productcode,c_category,c_maincate,c_date,c_date_1,c_date_2,c_date_3,c_date_4) values ('".$prcode."','".$k."','".$maincate."','{$c_date[$k]}','{$c_date[$k]}','{$c_date[$k]}','{$c_date[$k]}','{$c_date[$k]}')";
			pmysql_query($query);
			$in++;
		}

		$sql = "SELECT vender,display,brand,pridx,assembleuse,sellprice,assembleproduct,itemcate, review_banner_img ";
		$sql.= "FROM tblproduct WHERE productcode = '{$prcode}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		if($in_vender) {
			$vender=$in_vender;
		} else {
			$vender=(int)$row->vender;
		}
		$vdisp=$row->display;
		$brand=$row->brand;
		$vassembleuse=$row->assembleuse;
		$vpridx=$row->pridx;
		$vsellprice=$row->sellprice;
		$vassembleproduct=$row->assembleproduct;
		$itemcate = $row->itemcate;

		if(strlen($buyprice) < 1 ) $buyprice = 0 ;
		$in_content=pg_escape_string($in_content);
		$in_content_m=pg_escape_string($in_content_m);
		$pr_notice=pg_escape_string($pr_notice);
		$pr_sizecon=pg_escape_string($pr_sizecon);
		$pr_sizecon_m=pg_escape_string($pr_sizecon_m);
        $pr_content = pg_escape_string($pr_content);
		$keyword=pg_escape_string($keyword);
		$relation_tag=pg_escape_string($relation_tag);

		# 파일 로그 추가 2015 10 30 유동혁
		$logText = "## ".date("Y-m-d H:i:s")." [ 관리자 : ".$_ShopInfo->getId()." ] [ IP : ".$connect_ip." ] ## \n";
		Product_textLog ( $prcode , $logText, '변경전' );
		
		$sql = "UPDATE tblproduct SET ";
		$sql.= "productname		= '{$productname}', ";
		$sql.= "productname_kor = '{$productname_kor}', ";
		$sql.= "consumerprice	= {$consumerprice}, ";
		$sql.= "buyprice		= {$buyprice}, ";
		$sql.= "reserve			= '{$reserve}', ";
		$sql.= "reservetype		= '{$reservetype}', ";
		$sql.= "production		= '{$production}', ";
		$sql.= "madein			= '{$madein}', ";
		$sql.= "model			= '{$model}', ";
		$sql.= "opendate		= '{$opendate}', ";
		$sql.= "selfcode		= '{$selfcode}', ";
		$sql.= "soldout			= '{$soldout}', "; // 품절
		$sql.= "quantity		= {$quantity}, ";
		//$sql.= "group_check		= '{$group_check}', ";
		$sql.= "keyword			= '{$keyword}', ";
		$sql.= "relation_tag			= '{$relation_tag}', ";
		$sql.= "addcode			= '{$addcode}', ";
		$sql.= "userspec		= '{$userspec}', ";
		$sql.= "maximage		= '".$up_ImagePath[1].$image[1]."', ";
		$sql.= "minimage		= '".$up_ImagePath[2].$image[2]."', ";
		$sql.= "tinyimage		= '".$up_ImagePath[3].$image[3]."', ";
		$sql.= "over_minimage		= '".$up_ImagePath[4].$image[4]."', ";

		if ( empty($review_banner_image) && empty($old_review_banner_img) ) {
            // 리뷰 상단 배너 이미지가 삭제된 경우
            $reviewbannerimagepath  = $Dir.DataDir."shopimages/product_review_banner/";
            $old_img = $row->review_banner_img;

            // 실제로 업로드 된 이미지 삭제
            if ( !empty($old_img) && file_exists($reviewbannerimagepath.$old_img) ) {
                unlink($reviewbannerimagepath.$old_img);
            }

            // 필드 업데이드
            $sql.= "review_banner_img   = '', ";
        } elseif ( !empty($review_banner_image) ) {
            $sql.= "review_banner_img   = '".$review_banner_image."', ";
        }

		$sql.= "assembleuse		= '{$assembleuse}', ";
		$sql.= "membergrpdc		= '{$membergrpdc}', ";
		//$sql.= "start_no		= '{$start_no}', ";
		$sql.= "vip_product		= '{$vip_product}', ";
		$sql.= "position		= '{$position}', ";
		$sql.= "dctype			= '".$_POST['dctype']."',";
		$sql.= "sabangnet_prop_val			= '{$sabangnet_prop_val}', ";
		$sql.= "sabangnet_prop_option			= '{$sabangnet_prop_option}', ";

		if($vassembleuse=="Y") {
			if($assembleuse=="Y") {
				$sql.= "assembleproduct	= '', ";
				$sql.= "option_price	= '', ";
				$sql.= "option_ea	= '', ";
				$sql.= "option_consumer	= '', ";
				$sql.= "optcode	= '', ";
				$sql.= "option_reserve	= '', ";
				$sql.= "option_quantity	= '', ";
				$sql.= "option1			= '', ";
				$sql.= "option2			= '', ";
				$sql.= "package_num		= '0', ";
			} else {
				$sql.= "assembleproduct	= '', ";
				$sql.= "sellprice		= {$sellprice}, ";
				$sql.= "option_price	= '{$option_price}', ";
				$sql.= "option_ea	= '{$option_ea}', ";
				$sql.= "option_consumer	= '{$option_consumer}', ";
				$sql.= "optcode	= '{$optcode}', ";
				$sql.= "option_reserve	= '{$optreserve}', ";
				$sql.= "option_quantity	= '{$optcnt}', ";
				$sql.= "option1			= '{$option1}', ";
				$sql.= "option2			= '{$option2}', ";
				$sql.= "supply_subject  = '{$supply_subject}', ";
				$sql.= "package_num		= '".(int)$package_num."', ";
			}
		} else {
			if($assembleuse=="Y") {
				$sql.= "assembleproduct	= '', ";
				$sql.= "sellprice		= 0, ";
				$sql.= "option_price	= '', ";
				$sql.= "option_ea	= '', ";
				$sql.= "option_consumer	= '', ";
				$sql.= "optcode	= '', ";
				$sql.= "option_reserve	= '', ";
				$sql.= "option_quantity	= '', ";
				$sql.= "option1			= '', ";
				$sql.= "option2			= '', ";
				$sql.= "package_num		= '0', ";
			} else {
				$sql.= "sellprice		= {$sellprice}, ";
				$sql.= "option_price	= '{$option_price}', ";
				$sql.= "option_ea	= '{$option_ea}', ";
				$sql.= "option_consumer	= '{$option_consumer}', ";
				$sql.= "optcode	= '{$optcode}', ";
				$sql.= "option1			= '{$option1}', ";
				$sql.= "option2			= '{$option2}', ";
				$sql.= "supply_subject  = '{$supply_subject}', ";
				$sql.= "package_num		= '".(int)$package_num."', ";
			}
		}

		$sql.= "etctype			= '{$etctype}', ";
		# etc type 의 내용을 쪼개서 넣는다 2015 10 28 유동혁
		$sql.= "bankonly	= '{$up_bankonly}', ";
		$sql.= "deliinfono = '{$up_deliinfono}', ";
		$sql.= "icon =  '{$up_icon}', ";
		$sql.= "dicker = '{$up_dicker}', ";
		$sql.= "min_quantity = '{$up_miniq}', ";
		$sql.= "max_quantity = '{$up_maxq}', ";
		$sql.= "setquota = '{$up_setquota}', ";
		# DP 추가
		$sql.= "mall_type = '{$mall_type}', ";
		$sql.= "overseas_type = '{$overseas_type}', ";
		$sql.= "card_benefit = '{$card_benefit}', ";

		$sql.= "deli_price		= '{$deli_price}', ";
		$sql.= "deli			= '{$deli}', ";
		$sql.= "deli_qty		= '{$deli_qty}', ";
		$sql.= "deli_select		= '{$deli_select}', ";
		$sql.= "display			= '{$display}', ";
        $sql.= "naver_display	= '{$naver_display}', ";
        $sql.= "erp_price_yn	= '{$erp_price_yn}', ";
		
		if($insertdate!="Y") {
			$sql.= "date			= '{$curdate}', ";
			$sql.= "regdate		= now(), ";
			$sql.= "modifydate		= now(), ";
		}

		$sql.= "option_type = '".$opt_type."', ";
		$sql.= "option1_tf = '".$option1_tf."', ";
		if( $addopt_select == '1' ){
			$sql.= "option2_tf		= '{$option2_tf}', ";
			$sql.= "option2_maxlen			= '{$option2_maxlen}', ";
		}
		//자체품목코드 추가(20160610_김재수 추가)
		#$sql.= "self_goods_code			= '{$p_goods_code}', ";
        # 2016-03-21 마진률 추가
		$sql.="rate             = '{$rate}', ";
		$sql.= "mdcomment		= '{$mdcomment}',";
		$sql.= "mdcommentcolor	= '{$up_mdcommentcolor}', ";
		$sql.= "color_code		= '{$smart_search_color}',";
		if($in_vender) $sql.= "vender			= '{$in_vender}', ";
		$sql.= "content			= '{$in_content}', ";
		$sql.= "content_m		= '{$in_content_m}', ";
		$sql.= "pr_sizecon		= '{$pr_sizecon}', ";
		$sql.= "pr_sizecon_m	= '{$pr_sizecon_m}', ";
		$sql.= "pr_notice		= '{$pr_notice}', ";
		$sql.= "sex			    = '{$sex}', ";
		$sql.= "hotdealyn		= '{$hotdealyn}',  ";
        $sql.= "pr_content      = '{$pr_content}' ";
		
		$sql.= "WHERE productcode = '{$prcode}' ";
// 		exdebug($sql);
		
		if($update = pmysql_query($sql,get_db_conn())) {

			product_related("update",$prcode);//관련상품 정보 업데이트

			//product_size("up",$prcode,$use_prsize);

			##### 브랜드 관련 처리	
			//---------------------------------------------------//
			// 벤더에 해당하는 브랜드를 등록한다.
			// 그리고 상품별 노출 브랜드에도 등록한다.
			// 2016.01.22 - 김재수

			if ($in_vender && ($in_vender != $old_vender)) {
				list($bridx)=pmysql_fetch("SELECT bridx FROM tblproductbrand WHERE vender='{$in_vender}'");
				if ($bridx>0) {
					@pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '{$prcode}'",get_db_conn());

					@pmysql_query("DELETE FROM tblbrandproduct WHERE productcode='{$prcode}' and bridx='{$bridx}'",get_db_conn());
					@pmysql_query("UPDATE tblbrandproduct SET bridx = '{$bridx}' WHERE productcode = '{$prcode}' and bridx='{$brand_idx}'",get_db_conn());
				}			
			}

			/*if(ord($brandname)) {
				$result = pmysql_query("SELECT bridx FROM tblproductbrand WHERE brandname = '{$brandname}' ",get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					if($brand != $row->bridx) {
						@pmysql_query("UPDATE tblproduct SET brand = '{$row->bridx}' WHERE productcode = '{$prcode}'",get_db_conn());
					}
				} else {
					$sql = "INSERT INTO tblproductbrand(brandname) VALUES ('{$brandname}') RETURNING bridx";
					if($brandinsert = @pmysql_query($sql,get_db_conn())) {
						$row = pmysql_fetch_array($brandinsert);
						$bridx = $row[0];
						if($bridx>0) {
							@pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '{$prcode}'",get_db_conn());
						}
					}
				}
				pmysql_free_result($result);
			} else {
				if($brand>0) {
					@pmysql_query("UPDATE tblproduct SET brand = null WHERE productcode = '{$prcode}'",get_db_conn());
				}
			}*/

			
			if($vassembleuse=="Y") {
				if($assembleuse!="Y") {
					$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
					$sql.= "WHERE productcode = '{$prcode}' ";
					$result = pmysql_query($sql,get_db_conn());
					if($row = @pmysql_fetch_object($result)) {
						$sql = "DELETE FROM tblassembleproduct WHERE productcode = '{$prcode}' ";
						pmysql_query($sql,get_db_conn());

						if(ord(str_replace("","",$row->assemble_pridx))) {
							$sql = "UPDATE tblproduct SET ";
							$sql.= "assembleproduct = REPLACE(assembleproduct,',{$prcode}','') ";
							$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
							$sql.= "AND assembleuse != 'Y' ";
							pmysql_query($sql,get_db_conn());
						}
					}
					pmysql_free_result($result);
				}
			} else {
				if($assembleuse=="Y" || ($assembleuse!="Y" && $vsellprice!=$sellprice)) {
					if(ord($vassembleproduct)) {
						$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode IN ('".str_replace(",","','",$vassembleproduct)."') ";
						$result = pmysql_query($sql,get_db_conn());
						while($row = @pmysql_fetch_object($result)) {
							$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
							$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
							$sql.= "AND display ='Y' ";
							$sql.= "AND assembleuse!='Y' ";
							$result2 = pmysql_query($sql,get_db_conn());
							if($row2 = @pmysql_fetch_object($result2)) {
								$sql = "UPDATE tblproduct SET sellprice='{$row2->sumprice}' ";
								$sql.= "WHERE productcode = '{$row->productcode}' ";
								$sql.= "AND assembleuse='Y' ";
								pmysql_query($sql,get_db_conn());
							}
							pmysql_free_result($result2);
						}
					}

					if($assembleuse=="Y") {
						$sql = "UPDATE tblassembleproduct SET ";
						$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$vpridx}',''), ";
						$sql.= "assemble_list=REPLACE(assemble_list,',{$vpridx}','') ";
						pmysql_query($sql,get_db_conn());
					}
				}
			}

			if($vender>0) {
				if($vdisp!=$display) {
					setVenderCountUpdateRan($vender, $display);
				}
			}
			$content=$in_content;
			$content_m=$in_content_m;
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";

			if($changecode){


				$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}'";
				$result = pmysql_query($sql,get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '{$changecode}%' ";
					$sql.= "ORDER BY productcode DESC LIMIT 1 ";
					$result = pmysql_query($sql,get_db_conn());
					if ($rows = pmysql_fetch_object($result)) {
						$newproductcode = substr($rows->productcode,12)+1;
						$newproductcode = substr("000000".$newproductcode,strlen($newproductcode));
					} else {
						$newproductcode = "000001";
					}
					pmysql_free_result($result);

					$path = $Dir.DataDir."shopimages/product/";
					if (ord($row->maximage)) {
						$ext = strtolower(pathinfo($row->maximage,PATHINFO_EXTENSION));
						$maximage=$changecode.$newproductcode.".".$ext;

						if (file_exists("$path$row->maximage")) {

							rename("$path$row->maximage","$path$maximage");

						}
					} else $maximage="";
					if (ord($row->minimage)) {
						$ext = strtolower(pathinfo($row->minimage,PATHINFO_EXTENSION));
						$minimage=$changecode.$newproductcode."2.".$ext;
						if (file_exists("$path$row->minimage")) {
							rename("$path$row->minimage","$path$minimage");

						}
					} else $minimage="";
					if (ord($row->tinyimage)) {
						$ext = strtolower(pathinfo($row->tinyimage,PATHINFO_EXTENSION));
						$tinyimage=$changecode.$newproductcode."3.".$ext;
						if (file_exists("$path$row->tinyimage")) {
							rename("$path$row->tinyimage","$path$tinyimage");

						}
					} else $tinyimage="";

					if($row->vender>0) {
						$vender_prcodelist[$row->vender]["IN"][]=$changecode.$newproductcode;
					}


					if($row->vender>0) {
						$vender_prcodelist[$row->vender]["OUT"][]=$row->productcode;
					}

					$sql = "UPDATE tblproduct SET productcode = '".$changecode.$newproductcode."' ";
					if($tinyimage)$sql.=", tinyimage='".$tinyimage."'";
					if($minimage)$sql.=", minimage='".$minimage."'";
					if($maximage)$sql.=", maximage='".$maximage."'";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					#태그관련 지우기
					$sql = "UPDATE tbltagproduct SET productcode = '".$changecode.$newproductcode."'";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproductreview SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproducttheme SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblcollection SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblwishlist SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblcollection SET ";
					$sql.= "collection_list = replace(collection_list,'{$prcode}','".$changecode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblspecialcode SET ";
					$sql.= "special_list = replace(special_list,'{$prcode}','".$changecode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblspecialmain SET ";
					$sql.= "special_list = replace(special_list,'{$prcode}','".$changecode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());


					if($row->assembleuse=="Y") { //코디/조립 상품일 경우
						$sql = "UPDATE tblassembleproduct SET productcode = '".$changecode.$newproductcode."' ";
						$sql.= "WHERE productcode='{$prcode}'";
						pmysql_query($sql,get_db_conn());

						$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode = '".$changecode.$newproductcode."' ";
						$result = pmysql_query($sql,get_db_conn());
						if($row = @pmysql_fetch_object($result)) {
							if(ord(str_replace("","",$row->assemble_pridx))) {
								$sql = "UPDATE tblproduct SET ";
								$sql.= "assembleproduct = REPLACE(assembleproduct,',{$prcode}',',".$changecode.$newproductcode."') ";
								$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
								$sql.= "AND assembleuse != 'Y' ";
								pmysql_query($sql,get_db_conn());
							}
						}
						pmysql_free_result($result);
					} else {
						$sql = "UPDATE tblassembleproduct SET ";
						$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$row->pridx}','{$insert_pridx}'), ";
						$sql.= "assemble_list=REPLACE(assemble_list,',{$row->pridx}',',{$insert_pridx}') ";
						pmysql_query($sql,get_db_conn());
					}

					$log_content = "## 상품이동입력 ## - 상품코드 {$prcode} => ".$changecode.$newproductcode." - 상품명 : ".$productname;
					ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

					$prcode=$changecode.$newproductcode;
					$code=$changecode;
					$changecode="";
				}
			}
		}

		#파일 로그 추가 2015 10 30 유동혁
		Product_textLog ( $prcode , $logText, '변경후' );
		$log_folder = DirPath.DataDir."backup/product_logs_".date("Ym");
		if( !is_dir( $log_folder ) ){
			mkdir( $log_folder, 0700 );
			chmod( $log_folder, 0777 );
		}
		$file = $log_folder."/product_log_".date("Ymd").".txt";
		if(!is_file($file)){
			$f = fopen($file,"a+");
			fclose($f);
			chmod($file,0777);
		}
		file_put_contents($file,$logText,FILE_APPEND);

		//$onload="<script>alert(\"상품이 수정되었습니다.$message\");</script>";
        // erp 가격 연동 부분이 있어서, 연동안함에서 연동함으로 바꾸었을 경우 적용된 가격을 보여주려면 다시 상세화면으로 들어와야 되기 때문에 리스트로 돌린다.2016-12-01
        $onload="<script>alert(\"상품이 수정되었습니다.$message\");goBackList();</script>";

		$log_content = "## 상품수정 ## - 코드 $prcode - 상품 : $productname 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금 : $reserve 날짜고정 : ".(($insertdate=="Y")?"Y":"N")." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}
} else {
	alert_go("상품이미지의 총 용량이 ".ceil($file_size/1024).
	"Mbyte로 3M가 넘습니다.\\n\\n한번에 올릴 수 있는 최대 용량은 3M입니다.\\n\\n".
	"이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.",-1);
}

//################# 500K가 넘는 이미지 체크
if ((ord($userfile['name']) && $userfile['size']==0) || (ord($userfile2['name']) && $userfile2['size']==0) || (ord($userfile3['name']) && $userfile3['size']==0)) {
	 $onload="<script>alert(\"상품이미지중 용량이 3M가 넘는 이미지가 있습니다.\\n\\n3M가 넘는 이미지는 등록되지 않습니다..\\n\\n"
		."이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.\");</script>\n";
}
//###############################################



	#### 기타 파일 업로드 STR ####


	for($i=1;$i<=10;$i++){
		$img_new		= "mulimg".sprintf("%02d",$i);
		$img_url_new	= "mulimg".sprintf("%02d",$i)."_url";
		$img_old		= "oldimg".sprintf("%02d",$i);

		${$img_new} = $_FILES["$img_new"];
		${$img_url_new} = $_POST["$img_url_new"];
		${$img_old} = $_POST["$img_old"];
	}
	//$multiimagepath=$Dir.DataDir."shopimages/multi/";
    $multiimagepath=$Dir.DataDir."shopimages/product/";

	if ($_POST[multype]=="delete") {
		if (!$mulimgno) $mulimgno = $_POST[mulimgno];
		//echo $mulimgno;
		//exit;
		if (strlen($productcode) > 0 ) {

			if (ord($mulimgno)) {	//부분삭제
				//proc_matchfiledel($multiimagepath."*{$mulimgno}_{$productcode}*");
				$oldfile = array ("01"=>&$oldimg01,"02"=>&$oldimg02,"03"=>&$oldimg03,"04"=>&$oldimg04,"05"=>&$oldimg05,"06"=>&$oldimg06,"07"=>&$oldimg07,"08"=>&$oldimg08,"09"=>&$oldimg09,"10"=>&$oldimg10);

				$num=0;
				for($i=1;$i<=10;$i++) {
					$gbn=sprintf("%02d",$i);
					if(ord($oldfile[$gbn])) $num++;
				}

				if($num<=0) {
					$sql = "DELETE FROM tblmultiimages WHERE productcode = '{$productcode}' ";
				} else {
					$sql = "SELECT size FROM tblmultiimages WHERE productcode = '{$productcode}' ";
					$result = pmysql_query($sql,get_db_conn());
					if($row = pmysql_fetch_object($result)){
						if(strlen($row->size)!=0){
							$tmpsize=explode("",$row->size);
							$delsize = array("01"=>&$tmpsize[0],"02"=>&$tmpsize[1],"03"=>&$tmpsize[2],"04"=>&$tmpsize[3],"05"=>&$tmpsize[4],"06"=>&$tmpsize[5],"07"=>&$tmpsize[6],"08"=>&$tmpsize[7],"09"=>&$tmpsize[8],"10"=>&$tmpsize[9]);
							for($i=1;$i<=10;$i++){
								$gbn=sprintf("%02d",$i);
								if($gbn==$mulimgno) $delimgsize.="";
								else $delimgsize.="".$delsize[$gbn];
							}
						}
					}
					if(ord($delimgsize)) $delimgsize=",size='".substr($delimgsize,1)."'";
					else $delimgsize=",size=NULL";
					$sql = "UPDATE tblmultiimages SET primg{$mulimgno}='' {$delimgsize} ";
					$sql.= "WHERE productcode = '{$productcode}' ";
				}
				//echo $sql;
				//exit;
			} else {	//전체삭제
				proc_matchfiledel($multiimagepath."*{$productcode}*");
				$sql = "DELETE FROM tblmultiimages WHERE productcode = '{$productcode}' ";
			}
			pmysql_query($sql,get_db_conn());

		}
	}else if ($mode=="insert" || $mode=="modify") {
		if(strlen($productcode) < 18 && $mode=="insert" ) $productcode = $code.$productcode;
		$query = " select count(*) from tblmultiimages where productcode='".$productcode."' ";
		$result = pmysql_query($query,get_db_conn());
		list($multiimage_cnt) = pmysql_fetch_array($result);
		$mode=!$multiimage_cnt?"insert":$mode;

        // 해당 상품코드 밑으로 지정.2016-09-19...일괄등록시는 product/브랜드영문코드명/erp상품코드-쇼핑몰칼라코드/이미지명...tblproduct 이미지정보처럼 tblmultiimages 에도 폴더명/이미지명구조로 변경.
        //$multiimagepath .= $productcode."/";

		$file_url = array ("01"=>&$mulimg01_url,"02"=>&$mulimg02_url,"03"=>&$mulimg03_url,"04"=>&$mulimg04_url,"05"=>&$mulimg05_url,"06"=>&$mulimg06_url,"07"=>&$mulimg07_url,"08"=>&$mulimg08_url,"09"=>&$mulimg09_url,"10"=>&$mulimg10_url);
		$oldfile = array ("01"=>&$oldimg01,"02"=>&$oldimg02,"03"=>&$oldimg03,"04"=>&$oldimg04,"05"=>&$oldimg05,"06"=>&$oldimg06,"07"=>&$oldimg07,"08"=>&$oldimg08,"09"=>&$oldimg09,"10"=>&$oldimg10);
		$filearray = array ("01"=>&$mulimg01["name"],"02"=>&$mulimg02["name"],"03"=>&$mulimg03["name"],"04"=>&$mulimg04["name"],"05"=>&$mulimg05["name"],"06"=>&$mulimg06["name"],"07"=>&$mulimg07["name"],"08"=>&$mulimg08["name"],"09"=>&$mulimg09["name"],"10"=>&$mulimg10["name"]);
		$filen = array ("01"=>&$mulimg01["tmp_name"],"02"=>&$mulimg02["tmp_name"],"03"=>&$mulimg03["tmp_name"],"04"=>&$mulimg04["tmp_name"],"05"=>&$mulimg05["tmp_name"],"06"=>&$mulimg06["tmp_name"],"07"=>&$mulimg07["tmp_name"],"08"=>&$mulimg08["tmp_name"],"09"=>&$mulimg09["tmp_name"],"10"=>&$mulimg10["tmp_name"]);
		if ($mode=="insert") {
			$sql = "INSERT INTO tblmultiimages(productcode) VALUES ('{$productcode}')";
			pmysql_query($sql,get_db_conn());
		} else {
			$sql = "SELECT size FROM tblmultiimages WHERE productcode = '{$productcode}' ";
			$result = pmysql_query($sql,get_db_conn());
			if ($row = pmysql_fetch_object($result)){
				if (strlen($row->size)!=0){
					$tmpsize=explode("",$row->size);
					$delsize = array("01"=>&$tmpsize[0],"02"=>&$tmpsize[1],"03"=>&$tmpsize[2],"04"=>&$tmpsize[3],"05"=>&$tmpsize[4],"06"=>&$tmpsize[5],"07"=>&$tmpsize[6],"08"=>&$tmpsize[7],"09"=>&$tmpsize[8],"10"=>&$tmpsize[9]);
				}
			}
		}
		$sql = "UPDATE tblmultiimages SET ";
		$file_size=0;

		for($i=1;$i<=10;$i++){
			$gbn=sprintf("%02d",$i);
			$image="";
			
			if (ord($file_url[$gbn]) && !ord($filearray[$gbn])) {

                // 업로드 이미지가 없고, url 일 경우.
                $multiimagepath = $Dir.DataDir."shopimages/product/";

				if (ord($file_url[$gbn])) {
					$image=$file_url[$gbn];
                    $image = str_replace($productcode."/", "", $image);
                    //msg("file = ".$image);
				} else if (ord($oldfile[$gbn])) {
					$image=$oldfile[$gbn];
                    //msg("old=".$image);
				}
			} else {

                $multiimagepath = $Dir.DataDir."shopimages/product/".$productcode."/";

				if (ord($filearray[$gbn])) {
					if (ord($filearray[$gbn]) && file_exists($filen[$gbn])) {
						$ext = strtolower(pathinfo($filearray[$gbn],PATHINFO_EXTENSION));
						$image = $gbn."_{$productcode}.".$ext;
						$imgname=$multiimagepath."s".$image;
						$file_size += filesize($filen[$gbn]);
						if($mode=="modify" && ord($oldfile[$gbn])) {
							proc_matchfiledel($multiimagepath."*".$oldfile[$gbn]);
						}
						move_uploaded_file($filen[$gbn],$multiimagepath.$image);
						chmod($multiimagepath.$image, 0604);
						copy($multiimagepath.$image,$imgname);
						chmod($imgname, 0604);
						$size=getimageSize($imgname);
						$width=$size[0];
						$height=$size[1];
						$imgtype=$size[2];
						$maxsize=90;
						if ($width>$maxsize || $height>$maxsize) {
							if ($imgtype==1) $im = ImageCreateFromGif($imgname);
							else if ($imgtype==2) $im = ImageCreateFromJpeg($imgname);
							else if( $imgtype==3) $im = ImageCreateFromPng($imgname);
							if ($width>=$height) {
								$small_width=$maxsize;
								$small_height=($height*$maxsize)/$width;
							} else if ($width<$height) {
								$small_width=($width*$maxsize)/$height;
								$small_height=$maxsize;
							}

							// GIF일경우
							if ($imgtype==1) $im2=ImageCreate($small_width,$small_height);
							// JPG일경우
							else $im2=ImageCreateTrueColor($small_width,$small_height);

							// 홀수픽셀의 경우 검은줄을 흰색으로 바꾸기위해.
							$white = ImageColorAllocate($im2, 255,255,255);
							imagefill($im2,1,1,$white);



							ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);

							if($imgtype==1) imageGIF($im2,$imgname);
							else if($imgtype==2) imageJPEG($im2,$imgname);
							else if($imgtype==3) imagePNG($im2,$imgname);
							ImageDestroy($im);
							ImageDestroy($im2);
						}
					}
				} else if (ord($oldfile[$gbn])) {
					$image=$oldfile[$gbn];
                    //msg("old2 = ".$image);
				}
			}
            //exdebug("full = ".$multiimagepath.$image);
            //exdebug("multiimagepath = ".$multiimagepath);
            //exdebug("image = ".$image);
			if (ord($image) && file_exists($multiimagepath.$image)) {
                //msg($mode);
				if ($mode=="insert") {
					//$sql.= "primg{$gbn} = '{$image}',";
                    $sql.= "primg{$gbn} = '".$productcode."/".$image."',";
					if(ord($file_url[$gbn])) {						
						$imgsize.="";
					} else {
						$imgsize.="{$width}X".$height;
					}
				} else {
                    // 해당 상품코드 폴더로 지정..2016-09-19...일괄등록시는 product/브랜드영문코드명/erp상품코드-쇼핑몰칼라코드/이미지명...tblproduct 이미지정보처럼 tblmultiimages 에도 폴더명/이미지명구조로 변경.
                    /*
                    $tmp = explode("/", $image);
                    //msg(count($tmp));
                    $tmp_img = $tmp[count($tmp)-1];
                    //msg($tmp_img);
                    $sql.= "primg{$gbn} = '".$productcode."/".$tmp_img."',";
                    */
                    if (ord($filearray[$gbn])) {
                        // 파일 업로드일경우
                        $sql.= "primg{$gbn} = '".$productcode."/".$image."',";
                    } else {
                        // url 방식일 경우
                        $sql.= "primg{$gbn} = '".$image."',";
                    }

                    //$sql.= "primg{$gbn} = '".$image."',";
					if(ord($file_url[$gbn])) {						
						$imgsize.="";
					} else {
						if (ord($filearray[$gbn])) $imgsize.="{$width}X".$height;
						else $imgsize.="".$delsize[$gbn];
					}
				}
			} else {
				//$sql.= "primg{$gbn} = '',";
                if(strpos($image, "http://") !== false) {
                    $sql.= "primg{$gbn} = '".$image."',";
                } else {
                    $image = str_replace($productcode."/", "", $image);
                    if(ord($image)) {
                        $sql.= "primg{$gbn} = '".$productcode."/".$image."',";
                    } else {
                        $sql.= "primg{$gbn} = '',";
                    }
                }
				$imgsize.="";
			}
		}
		$imgsize=substr($imgsize,1);
		$sql.= "size = '{$imgsize}' ";
		$sql.= " WHERE productcode = '{$productcode}' ";

		pmysql_query($sql,get_db_conn());
        //exdebug($sql);
	}
	#### 기타 파일 업로드 END ####

	###   옵션 등록 / 수정   ###
	if ( ( $mode=="insert" && $insert ) || ( $mode=="modify" && $update ) ){
		$upOptQty    = 0;
		$optNumArr   = array();
		$opt_success = true;
		$errMsg      = '';
		try {
			BeginTrans();
			if( $opt_select == '1' ){
				if( strlen( $prcode ) == 0 ) $goods_code = $code.$productcode;
				else $goods_code = $prcode;
				
				if( $opt_type == '0' && count( $opt_id ) > 0 ){
					// 조합형
					$update_num           = $opt_num;
					$option_code          = $opt_id;
					$option_price         = $opt_price;
					$option_quantity      = $opt_stock_qty;
					$option_quantity_noti = $opt_noti_qty;
					$option_use           = $opt_use;
					$option_tf            = array();
					$option_type          = $opt_type;
					$self_goods_code      = $opt_goods_code;
				} else if( $opt_type == '1' && count( $spl_option_code ) > 0 ) {
					// 독립형
					$update_num           = $spl_num;
					$option_code          = $spl_option_code;
					$option_price         = $spl_option_price;
					$option_quantity      = $spl_option_quantity;
					$option_quantity_noti = $spl_option_noti_qty;
					$option_use           = $spl_option_use;
					$option_tf            = $spl_tf;
					$option_type          = $opt_type;
					$self_goods_code      = $spl_goods_code;
				} else {
					$opt_success = false;
					throw new Exception( '옵션목록이 존재하지 않습니다. 다시 시도해 주시기 바랍니다.' );
				}
				if( count( $option_code ) > 0 && $opt_success ){
					foreach( $option_code as $optKey=>$optVal ){
						// self_code
						if( $opt_type == '0' ) $option_tf[$optKey] = 'T';
						if( $mode=="modify" && $update_num[$optKey] != '' ) {
							$option_num = $update_num[$optKey];
						} else {
							// 다음 seq의 값을 가져온다
							$seq_sql = "SELECT nextval( 'tblproduct_option_option_num_seq' ) AS seq_num ";
							$seq_res = pmysql_query( $seq_sql, get_db_conn() );
							$seq_row = pmysql_fetch_row( $seq_res );
							pmysql_free_result( $seq_res );
							if( pmysql_error() ){
								$opt_success = false;
								throw new Exception( "옵션목록생성 실패.다시 시도해 주시기 바랍니다." );
								break;
							} else {
								$option_num = $seq_row[0];
							}
						}
						// self_goods_code 확인
						if( strlen( trim( $self_goods_code[$optKey] ) ) > 0 ) {
							$goods_sql = "select count(*) from ( 
													select '1' as op_type, vender, productcode, self_goods_code, pridx as idx from tblproduct where self_goods_code !='' 
													union select '2' as op_type, tp.vender, tp.productcode, tpo.self_goods_code, tpo.option_num as idx from tblproduct_option as tpo left join tblproduct as tp on tpo.productcode=tp.productcode where tpo.self_goods_code !='' 
												) AS a where vender='{$in_vender}' AND self_goods_code='".$self_goods_code[$optKey]."' ";
							if( $update_num[$optKey] != '' ) $goods_sql .= "AND (op_type ='1' OR (op_type ='2' AND idx != '".$option_num."')) ";
							$goods_res = pmysql_query( $goods_sql, get_db_conn() );
							$goods_row = pmysql_fetch_row( $goods_res );
							pmysql_free_result( $goods_res );
							if( $goods_row[0] > 0 ){
								$opt_success = false;
								throw new Exception( "옵션 등록/수정 실패.존재하는 자체품목 코드 입니다." );
								break;
							}
						} else { // 옵션 self코드를 입력 안할시
							$self_goods_code[$optKey] = $goods_code.'_'.$option_num;
						}
						// 옵션가 예외처리
						if( $option_price[$optKey] == "" || is_null($option_price[$optKey]) ) $option_price[$optKey] = 0;
						// 옵션 수량 예외처리
						if( $option_quantity[$optKey] == "" || is_null($option_quantity[$optKey]) ) $option_quantity[$optKey] = 0;
						// 옵션 공지 수량 예외처리
						if( $option_quantity_noti[$splKey] == "" || is_null($option_quantity_noti[$splKey]) ) $option_quantity_noti[$splKey] = 0;

						if ( $mode=="modify" && $update_num[$optKey] != '' ) { // 옵션 내부 값만 수정시
							$optUpdateSql = "UPDATE tblproduct_option SET option_price='".$option_price[$optKey]."',option_quantity='".$option_quantity[$optKey]."', ";
							$optUpdateSql.= "option_quantity_noti='".$option_quantity_noti[$optKey]."',option_use='".$option_use[$optKey]."', ";
							$optUpdateSql.= "option_tf = '".$option_tf[$optKey]."', self_goods_code ='".$self_goods_code[$optKey]."' ";
							$optUpdateSql.= "WHERE option_num='".$update_num[$optKey]."' ";
							$optUpdateSql.= "RETURNING option_num ";
							$returnOption = pmysql_fetch_object( pmysql_query( $optUpdateSql, get_db_conn() ) );
							if( pmysql_error() ){
								$opt_success = false;
								throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
								break;
							} else {
								$optNumArr[] = $returnOption->option_num;
								$upOptQty += $option_quantity[$optKey];
							}
						} else { // 옵션 입력
							$optInsertSql = "INSERT INTO tblproduct_option ";
							$optInsertSql.= "( option_num, option_code, productcode, option_price, ";
							$optInsertSql.= " option_quantity, option_quantity_noti, option_use, option_tf, ";
							$optInsertSql.= " option_type, self_goods_code ) ";
							$optInsertSql.= "VALUES ( '".$option_num."', '".$optVal."', '".$goods_code."', '".$option_price[$optKey]."', ";
							$optInsertSql.= " '".$option_quantity[$optKey]."', '".$option_quantity_noti[$optKey]."', '".$option_use[$optKey]."', '".$option_tf[$optKey]."', ";
							$optInsertSql.= " '".$option_type."', '".$self_goods_code[$optKey]."' ) ";
							pmysql_query($optInsertSql, get_db_conn());
							if( pmysql_error() ){
								$opt_success = false;
								throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
								break;
							} else {
								$optNumArr[] = $option_num;
								$upOptQty += $option_quantity[$optKey];
							}
						}
					}
					// 수정시 옵션 삭제
					if( $mode=="modify" ){
						$tmpNum = implode( ',', $optNumArr );
						$sql = "DELETE FROM tblproduct_option WHERE productcode = '".$goods_code."' ";
						if( $tmpNum != '' ) $sql.= "AND option_num NOT IN ( ".$tmpNum." ) ";
						pmysql_query( $sql, get_db_conn() );
						if( pmysql_error() ){
							$opt_success = false;
							throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
						}
					}
					// 제고 기준을 option quantity에 맞춘다
					if( $checkquantity=="C" && $opt_type == '0' && $opt_success === true ) {
						pmysql_query( "UPDATE tblproduct SET quantity = '".$upOptQty."' WHERE productcode='".$goods_code."'", get_db_conn() );
						if( pmysql_error() ){
							$opt_success = false;
							throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
						}
					}
				} else { // 값이 존재 안함
					$opt_success = false;
					throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
				}
			}
		} catch( Exception $e ) {
			$errMsg .= $e->getMessage();
			RollbackTrans();
		}
		
		if( $opt_success ){
			CommitTrans();
		}

		if( !$opt_success && $errMsg != '' ) {
			$onload = "<script>alert(' ".$errMsg." ');</script>";
		}
	}
	### 옵션 등록 / 수정 END ###
	

	### 회원등급 SELECT ###
	$member_sql = "SELECT group_code, group_name FROM tblmembergroup WHERE group_level != '100' ORDER BY group_code ASC ";
	$member_res = pmysql_query($member_sql,get_db_conn());
	while($member_row = pmysql_fetch_array($member_res)){
		$member_code[] = $member_row;
	}


include("header.php"); 

#---------------------------------------------------------------
# 상품 data 체크
#---------------------------------------------------------------

$selected[dctype][0]="checked";
if (ord($prcode)) {


	$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($_data = pmysql_fetch_object($result)) {
		
		$r_product_list = product_related("list",$prcode);//등록된 관련상품 리스트를 가져옵니다 06 29 원재 ㅠㅠ

		//$product_size = product_size("list",$prcode);
		
		if($r_product_list){ 
			$chk_r2 = " checked ";
		}else{
			$chk_r3 = " style='display:none;' ";
			$chk_r1 =" checked ";
		}

		if($_data->dctype=="1")$selected[dctype][$_data->dctype]="checked";
		$productname = $_data->productname;

		$specname=array();
		$specvalue=array();
		$specarray=array();
		if(ord($_data->userspec)) {
			$userspec = "Y";
			$specarray= explode("=",$_data->userspec);
			for($i=0; $i<$userspec_cnt; $i++) {
				$specarray_exp = explode("", $specarray[$i]);
				$specname[] = $specarray_exp[0];
				$specvalue[] = $specarray_exp[1];
			}

		} else {
			$userspec = "N";
		}

		// 특수옵션값을 체크한다.
		$miniq = 1;          // 최소주문수량 기본값 넣는다.
		$maxq = "";
		$dicker = $dicker_text="";
		if( $_data->bankonly == 'Y' ){
			$bankonly='Y';        // 현금전용
		}
		if( ord( $_data->deliinfono ) ){
			$deliinfono = $_data->deliinfono;  // 배송/교환/환불정보 노출안함 정보
		}
		if( $_data->setquota == 'Y' ){
			$setquota='Y';        // 무이자상품
		}
		if( $_data->min_quantity > 0 ){
			$miniq = $_data->min_quantity;
		}
		if( $_data->max_quantity > 0 ){
			$maxq = $_data->max_quantity;
		}
		if( ord( $_data->icon ) ){
			$iconvalue = $_data->icon;
		}
		if( ord ($_data->dicker ) ){
			$dicker = 'Y';
			$dicker_text = $_data->dicker;
		}
		
		/*
		if (ord($_data->etctype)) {
			$etctemp = explode("",$_data->etctype);
			$miniq = 1;          // 최소주문수량 기본값 넣는다.
			$maxq = "";
			for ($i=0;$i<count($etctemp);$i++) {
				if ($etctemp[$i]=="BANKONLY")                    $bankonly="Y";        // 현금전용
				else if (substr($etctemp[$i],0,11)=="DELIINFONO=")     $deliinfono=substr($etctemp[$i],11);  // 배송/교환/환불정보 노출안함 정보
				else if ($etctemp[$i]=="SETQUOTA")               $setquota="Y";        // 무이자상품
				else if (substr($etctemp[$i],0,6)=="MINIQ=")     $miniq=substr($etctemp[$i],6);  // 최소주문수량
				else if (substr($etctemp[$i],0,5)=="MAXQ=")      $maxq=substr($etctemp[$i],5);  // 최대주문수량
				else if (substr($etctemp[$i],0,5)=="ICON=")      $iconvalue=substr($etctemp[$i],5);  // 최대주문수량
				else if (substr($etctemp[$i],0,9)=="FREEDELI=")  $freedeli=substr($etctemp[$i],9);  // 무료배송상품
				else if (substr($etctemp[$i],0,7)=="DICKER=") {  $dicker=Y; $dicker_text=str_replace("DICKER=","",$etctemp[$i]); }  // 가격대체문구
			}
		}
		*/
		if(ord($iconvalue)) {
			for($i=0;$i<strlen($iconvalue);$i=$i+2) {
				$iconvalue2[substr($iconvalue,$i,2)]="Y";
			}
		}
		if($_data->brand>0) {
			$sql = "SELECT brandname FROM tblproductbrand WHERE bridx = '{$_data->brand}' ";
			$result = pmysql_query($sql,get_db_conn());
			$_data2 = pmysql_fetch_object($result);
			$_data->brandname = $_data2->brandname;
			pmysql_free_result($result);
		}
		

		if(ord($_data->sabangnet_prop_val)) {
			$sabangnet_prop_val = explode("||",$_data->sabangnet_prop_val);
			$prop_type = $sabangnet_prop_val[0];
			/*for ($i=1;$i<count($sabangnet_prop_val);$i++) {
				$prop1 = $sabangnet_prop_val[$i];
			}*/
		}
		if(ord($_data->sabangnet_prop_val)){
			$sabangnet_prop_option = explode("||",$_data->sabangnet_prop_option);
			//$prop_type = $sabangnet_prop_option[0];
		}

		
	} else {
		alert_go('해당 상품이 존재하지 않습니다.');
	}
}else{
	$chk_r3 = " style='display:none;' ";
	$chk_r1 =" checked ";
}

if(preg_match("/^\[OPTG\d{4}\]$/",$_data->option1)){
	$optcode = substr($_data->option1,5,4);
	$_data->option1="";
	$_data->option_price="";
	$_data->option_ea="";
	$_data->option_consumer="";
	$_data->optcode="";
	$_data->option_reserve="";
}
$check[start_no][$_data->start_no]="checked";
$check[vip_product][$_data->vip_product]="checked";


#---------------------------------------------------------------
# 카테고리 리스트 script 작성
#---------------------------------------------------------------

$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblproductcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
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
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";


$codeA_list = "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";

$codeB_list = "<select name=code_b id=code_b style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";

$codeC_list = "<select name=code_c id=code_c style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";

$codeD_list = "<select name=code_d id=code_d style=\"width:150px; height:150px\" {$disabled} Multiple>\n";
$codeD_list.= "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
$codeD_list.= "</select>\n";

$codeSelect = "<span style=\"display:\" name=\"changebutton\"><input type=\"button\" value=\"선택\" style=\"height : 20px;\" onclick=\"javascript:exec_add()\"></span>";

// 스크립트 작성완료

#---------------------------------------------------------------
# 해당 상품의 카테고리 리스트를 가져온다
#---------------------------------------------------------------
$cate_query="select * from tblproductlink where c_productcode='".$prcode."' and c_productcode!=''";
$cate_result=pmysql_query($cate_query);
$i=0;

while($cate_row=pmysql_fetch_array($cate_result)){

	$cate_array[$i]["c_category"]=$cate_row[c_category];
	$cate_cut="";
	$catename="";
	$cate_cut[]=str_pad(substr($cate_row[c_category],0,3), 12, "0");
	if(substr($cate_row[c_category],3,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,6), 12, "0");
	if(substr($cate_row[c_category],6,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,9), 12, "0");
	if(substr($cate_row[c_category],9,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,12), 12, "0");

	foreach($cate_cut as $k){
		$catename_qry="select * from tblproductcode where code_a='".substr($k,0,3)."' and code_b='".substr($k,3,3)."' and code_c='".substr($k,6,3)."' and code_d='".substr($k,9,3)."'";
		$catename_result=pmysql_query($catename_qry);
		$catename_data=pmysql_fetch_array($catename_result);
		$catename[]=$catename_data[code_name];
	}
	$cate_array[$i]["c_codename"]=implode(" > ",$catename);
$i++;
}


#---------------------------------------------------------------
# 해당 상품의 멀티 이미지를 가져온다
#---------------------------------------------------------------
if(strlen($prcode)>0){
	$sql = "SELECT * FROM tblmultiimages ";
	$sql.= "WHERE productcode = '{$prcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)){
		$mulimg_name = array ("01"=>&$row->primg01,"02"=>&$row->primg02,"03"=>&$row->primg03,"04"=>&$row->primg04,"05"=>&$row->primg05,"06"=>&$row->primg06,"07"=>&$row->primg07,"08"=>&$row->primg08,"09"=>&$row->primg09,"10"=>&$row->primg10);
	}
}

#---------------------------------------------------------------
# 해당 상품의 옵션정보를 가져온다
#---------------------------------------------------------------
/*
$option1_Arr=explode(",",$_data->option1);
$option2_Arr=explode(",",$_data->option2);
$opt2_subject = "";
$opt2 = "";
$opt1_subject = "";
$opt1 = "";
# 옵션 arr => "색상,PEWTER,BORDO,BLACK"; 0번째의 위치의 내용은 속성명임 (,로 구분)
if( $option1_Arr ) {
	foreach( $option1_Arr as $opt1_key=>$opt1_val ){
		if( $opt1_key == 0) {
			$opt1_subject.= $opt1_val;
		} else if( count($option1_Arr) - 1 == $opt1_key ) {
			$opt1.= $opt1_val;
		} else {
			$opt1.= $opt1_val.",";
		}
	}
}

# 옵션 arr => "색상,PEWTER,BORDO,BLACK"; 0번째의 위치의 내용은 속성명임 (,로 구분)
if( $option2_Arr ) {
	foreach( $option2_Arr as $opt2_key=>$opt2_val ){
		if( $opt2_key == 0) {
			$opt2_subject.= $opt2_val;
		} else if( count($option2_Arr) - 1 == $opt2_key ) {
			$opt2.= $opt2_val;
		} else {
			$opt2.= $opt2_val.",";
		}
	}
}
*/
#---------------------------------------------------------------
# 해당 상품의 추가 옵션정보를 가져오기위하여 it['td_id'] 에 상품코드를 넣는다
# 추가 옵션이 존재하면 옵션명과 갯수를 가져온다
#---------------------------------------------------------------
$it['it_id'] = $prcode;
if( ord( $_data->option1 ) && $_data->option_type == '1' ){
	$spl_subject = explode( '@#', $_data->option1 );
	$spl_count = count( $spl_subject );
}

#---------------------------------------------------------------
# 해당 상품의 정보고시를 가져온다
#---------------------------------------------------------------

if(ord($_data->sabangnet_prop_val)) {
	$sabangnet_prop_val = explode("||",$_data->sabangnet_prop_val);
	$prop_type = $sabangnet_prop_val[0];
}

#---------------------------------------------------------------
# 해당 상품의 옵션를 가져온다
#---------------------------------------------------------------

$sql2 = "SELECT option_num, option_code, productcode, option_price, option_quantity, option_type, option_use FROM tblproduct_option WHERE productcode = '".$prcode."' AND option_type = '0' ";
$res2 = pmysql_query( $sql2, get_db_conn() );
$opt1_s = array();
$opt2_s = array();
$option_obj = array();
while( $row2 = pmysql_fetch_object( $res2 ) ){

	$option = explode( chr(30), $row2->option_code );
	//$arr_key = array_search( $option[0], $opt1 );
	//exdebug( array_search( $option[0], $opt1_s ) );
	if( array_search( $option[0], $opt1_s ) === false ) $opt1_s[] = $option[0];
	if( array_search( $option[1], $opt2_s ) === false && $option[1] != '' ) $opt2_s[] = $option[1];
	$option_obj[$row2->option_code] = $row2;
}

$option_cnt = $option_obj;

#배송정보를 가져온다
$deli        = $_data->deli;
$deli_price  = $_data->deli_price;
$deli_select = $_data->deli_select;
$deli_qty    = $_data->deli_qty;
# 마진률을 가져온다
if( count( $_data ) > 0 ){
    $up_rate = $_data->rate;
} else {
    $up_rate = 0;
}

#핫딜 체크
$checked["hotdealyn"][$_data->hotdealyn]="checked";

?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('AddFrame')");</script>
<script type="text/javascript" src="<?=$Dir?>js/jscolor.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

function previewImage(targetObj, previewId) {
        var preview = document.getElementById(previewId); //div id
        var ua = window.navigator.userAgent;

        if (ua.indexOf("MSIE") > -1) {//ie일때

            targetObj.select();

            try {

                var src = document.selection.createRange().text; // get file full path
                var ie_preview_error = document
                        .getElementById("ie_preview_error_" + previewId);

                if (ie_preview_error) {
                    preview.removeChild(ie_preview_error); //error가 있으면 delete
                }

                var img = document.getElementById(previewId); //이미지가 뿌려질 곳

               img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ src + "', sizingMethod='scale')"; //이미지 로딩, sizingMethod는 div에 맞춰서 사이즈를 자동조절 하는 역할
            } catch (e) {

                if (!document.getElementById("ie_preview_error_" + previewId)) {
                    var info = document.createElement("<p>");
                    info.id = "ie_preview_error_" + previewId;
                    info.innerHTML = "a";
                    preview.insertBefore(info, null);
                }

            }

        } else { //ie가 아닐때
            var files = targetObj.files;
            for ( var i = 0; i < files.length; i++) {

                var file = files[i];

                var imageType = /image.*/; //이미지 파일일경우만.. 뿌려준다.
                if (!file.type.match(imageType))
                    continue;

                var prevImg = document.getElementById("prev_" + previewId); //이전에 미리보기가 있다면 삭제
                if (prevImg) {
                    preview.removeChild(prevImg);
                }

                var img = document.createElement("img");//크롬은 div에 이미지가 뿌려지지 않는다. 그래서 자식Element를 만든다.
                img.id = "prev_" + previewId;
                img.classList.add("obj");
                img.file = file;
                img.style.width = '50px'; //기본설정된 div의 안에 뿌려지는 효과를 주기 위해서 div크기와 같은 크기를 지정해준다.
                img.style.height = '50px';

                preview.appendChild(img);

                if (window.FileReader) { // FireFox, Chrome, Opera 확인.
                    var reader = new FileReader();
                    reader.onloadend = (function(aImg) {
                        return function(e) {
                            aImg.src = e.target.result;
                        };
                    })(img);
                    reader.readAsDataURL(file);
                } else { // safari is not supported FileReader
                    //alert('not supported FileReader');
                    if (!document.getElementById("sfr_preview_error_"
                            + previewId)) {
                        var info = document.createElement("p");
                        info.id = "sfr_preview_error_" + previewId;
                        info.innerHTML = "not supported FileReader";
                        preview.insertBefore(info, null);
                    }
                }
            }
        }
}

function copy_clipboard(serp) {
	var IE=(document.all)?true:false;
	if (IE) {
		if(confirm("이줄의 ERP코드를 클립보드에 복사하시겠습니까?"))
			window.clipboardData.setData("Text", serp);
		}else{
		temp = prompt("이줄의 ERP코드입니다. Ctrl+C를 눌러 클립보드로 복사하세요", serp);
	}
}

function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function PrdtDelete() {
	if (confirm("해당 상품을 삭제하시겠습니까?")) {
		document.cForm.mode.value="delete";
		document.cForm.submit();
	}
}

function goBackList(){
    var popup = '<?=$popup?>';
    //alert(popup);
    if(popup == "NO") {
    	document.form_register.submit();
    } else {
        //window.close();
        setTimeout (window.close, 1000);
        opener.location.reload();
    }
}

function NewPrdtInsert(){
	document.cForm.prcode.value="";
	document.cForm.submit();
}

function IconMy(){
	window.open("","icon","height=343,width=440,toolbar=no,menubar=no,scrollbars=no,status=no");
	document.icon.submit();
}
function searchProduct(){
	window.open("product_searchproduct.php","searchProduct","height=343,width=540,toolbar=no,menubar=no,scrollbars=no,status=no");
}
function IconList(){
	alert("서비스 준비중 입니다.");
	//window.open("","iconlist","height=343,width=440,toolbar=no,menubar=no,scrollbars=no,status=no");
	//document.iconlist.submit();
}

function DeletePrdtImg(temp){
	if(confirm('해당 이미지를 삭제하시겠습니까?')){
		document.cForm.mode.value="delprdtimg";
		document.cForm.delprdtimg.value=temp-1;
		document.cForm.submit();
	}
}

function DeleteImg(id) {
    $("#" + id).val("");
    $("#" + id + "_del").remove();
    $("#" + id + "_view").css("display", "none");
}
function CheckChoiceIcon(no){
	num = document.form1.iconnum.value;
	iconnum=0;
	for(i=0;i<num;i++){
		if(document.form1.icon[i].checked) iconnum++;
	}
	if(iconnum>3){
		alert('한 상품에 3개까지 아이콘을 사용할 수 있습니다.');
		document.form1.icon[no].checked=false;
	}
}

function PrdtAutoImgMsg(){
	//if(document.form1.imgcheck.checked) alert('상품 중간/작은 이미지가 대 이미지에서 자동 생성됩니다.\n\n대 이미지가 권장 이미지보다 작을경우 이미지 생성이 안됩니다.\n\n기존의 중간/작은 이미지는 삭제됩니다.\n\n 리스트 롤오버 이미지는 적용되지 않습니다. ');
	if(document.form1.imgcheck.checked) alert('상품 중간/작은 이미지가 대 이미지에서 자동 생성됩니다.\n\n대 이미지가 권장 이미지보다 작을경우 이미지 생성이 안됩니다.\n\n기존의 중간/작은 이미지는 삭제됩니다.');
}


function SelectColor(){
	setcolor = document.form1.setcolor.value;
	var newcolor = showModalDialog("select_color.php?color="+setcolor, "oldcolor", "resizable: no; help: no; status: no; scroll: no;");
	if(newcolor){
		document.form1.setcolor.value=newcolor;
		document.all.ColorPreview.style.backgroundColor = '#' + newcolor;
	}
}

function optionhelp(){
	alert("서비스 준비중 입니다.");
}

function DateFixAll(obj) {
	if (obj.checked) {
		document.form1.insertdate.value="Y";
		document.form1.insertdate1.checked=true;
		document.form1.insertdate2.checked=true;
		document.form1.insertdate3.checked=true;
	} else {
		document.form1.insertdate.value="";
		document.form1.insertdate1.checked=false;
		document.form1.insertdate2.checked=false;
		document.form1.insertdate3.checked=false;
	}
}

function change_filetype(obj) {
	if(obj.checked) {	//이미지 링크 방식
		for(var jj=1;jj<=3;jj++) {
			idx=jj;
			if(idx==1) idx="";
			document.form1["userfile"+idx].style.display='none';
			document.form1["userfile"+idx+"_url"].style.display='';
			document.form1["userfile"+idx].disabled=true;
			document.form1["userfile"+idx+"_url"].disabled=false;
		}
		$(".file_type").hide();
		$(".url_type").show();
	} else {				//첨부파일 방식
		for(var jj=1;jj<=3;jj++) {
			idx=jj;
			if(idx==1) idx="";
			document.form1["userfile"+idx].style.display='';
			document.form1["userfile"+idx+"_url"].style.display='none';
			document.form1["userfile"+idx].disabled=false;
			document.form1["userfile"+idx+"_url"].disabled=true;
		}
		$(".url_type").hide();
		$(".file_type").show();
	}
}

function userspec_change(val) {
	if(document.getElementById("userspecidx")) {
		if(val == "Y") {
			document.getElementById("userspecidx").style.display ="";
		} else {
			document.getElementById("userspecidx").style.display ="none";
		}
	}
}
/*
function GroupCode_Change(val) {
	if(document.getElementById("group_checkidx")) {
		if(val == "Y") {
			document.getElementById("group_checkidx").style.display ="";
		} else {
			document.getElementById("group_checkidx").style.display ="none";
		}
	}
}
*/

function GroupCodeAll(checkval,checkcount) {
	for(var i=0; i<checkcount; i++) {
		if(document.getElementById("group_code_idx"+i)) {
			document.getElementById("group_code_idx"+i).checked = checkval;
		}
	}
}

/*################################### 태그관련 시작 #####################################*/
var IE = false ;
if (window.navigator.appName.indexOf("Explorer") !=-1) {
	IE = true;
}

function getXmlHttpRequest() {
	var xmlhttp = false
	if(window.XMLHttpRequest){//Mozila
		xmlhttp = new XMLHttpRequest()
	}else {//IE
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP")
	}
	return xmlhttp;
}
function loadData(path, successFunc, msg){
	var xmlhttp = getXmlHttpRequest();
	xmlhttp.open("GET",path,true);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200) {
				var data = xmlhttp.responseText;
				successFunc(data);
			}else{
				alert(msg);
			}
		}
	}
	xmlhttp.send(null);
	return false;
}


function loadProductTagList (prcode) {
	loadData("product_taglist.xml.php?prcode="+prcode, setProductTagList);
}
function setProductTagList(data) {
  	try {
  		var tagElem = document.getElementById("ProductTagList");
  		if(data=='') {
  			data = "일시적으로 태그 정보를 불러올 수 없습니다.\n\n태그 관리 기능은 잠시 후에 이용해 주십시오. \n\n상품수정은  정상적으로 수정하실  수 있습니다.";
  		}
  		tagElem.innerHTML = data;
		tagElem.style.height = "68";
		tagElem.style.overflowY = "auto";
  	}catch(e) {}
}

function delTagName(prcode,tagname) {
<?php if(_DEMOSHOP=="OK" && $_SERVER['REMOTE_ADDR']!=_ALLOWIP) { ?>
	alert("데모버전에서는 삭제가 불가능 합니다.");
<?php } else { ?>
	if(confirm("\""+tagname+"\" 태그를 삭제하시겠습니까?")) {
		loadData("product_taglist.xml.php?type=del&prcode="+prcode+"&tagname="+tagname, setProductTagList);
	}
<?php } ?>
}

function BrandSelect() {
	window.open("product_brandselect.php","brandselect","height=400,width=420,scrollbars=no,resizable=no");
}


function ItemSelect() {
	window.open("product_itemselect.php","itemselect","height=400,width=420,scrollbars=no,resizable=no");
}

function FiledSelect(pagetype) {
	window.open("product_select.php?type="+pagetype,pagetype,"height=400,width=420,scrollbars=no,resizable=no");
}

/*################################### 태그관련 끝   #####################################*/


function deli_helpshow() {
	if(document.getElementById('deli_helpshow_idx')) {
		if(document.getElementById('deli_helpshow_idx').style.display=="none") {
			document.getElementById('deli_helpshow_idx').style.display="";
		} else {
			document.getElementById('deli_helpshow_idx').style.display="none";
		}
	}
}

function chkFieldMaxLenFunc(thisForm,reserveType) {
	if (reserveType=="Y") { max=5; addtext="/특수문자(소수점)";} else { max=6; }
	if (thisForm.reserve.value.bytes() > max) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "숫자"+addtext+" " + max + "자 이내로 입력이 가능합니다.");
		thisForm.reserve.value = thisForm.reserve.value.cut(max);
		thisForm.reserve.focus();
	}
}

function getSplitCount(objValue,splitStr)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	return split_array.length;
}

function getPointCount(objValue,splitStr,falsecount)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);

	if(split_array.length!=2) {
		if(split_array.length==1) {
			return false;
		} else {
			return true;
		}
	} else {
		if(split_array[1].length>falsecount) {
			return true;
		} else {
			return false;
		}
	}
}

function isDigitSpecial(objValue,specialStr)
{
	if(specialStr.length>0) {
		var specialStr_code = parseInt(specialStr.charCodeAt(i));

		for(var i=0; i<objValue.length; i++) {
			var code = parseInt(objValue.charCodeAt(i));
			var ch = objValue.substr(i,1).toUpperCase();

			if((ch<"0" || ch>"9") && code!=specialStr_code) {
				return true;
				break;
			}
		}
	} else {
		for(var i=0; i<objValue.length; i++) {
			var ch = objValue.substr(i,1).toUpperCase();
			if(ch<"0" || ch>"9") {
				return true;
				break;
			}
		}
	}
}

<?php if($_data->vender<=0) { ?>
function assembleuse_change() {
	
}

<?php } ?>

function catenmchange(prcode){

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
//		$("#catenm").html(msg);

	}else if(msg=='nolowcate'){
		alert("하위카테고리가 존재합니다.");
	//	$("#catenm").html("상품카테고리 선택이 잘못되었습니다.");
	}else{

		var code=document.getElementById("code").value;
		if(code){

			if(sumcode!=code){
				document.form1.changecode.value=sumcode;
			}else{
				document.form1.changecode.value="";
			}
		}else{
			document.form1.code.value=sumcode;
		}
		
		document.form1.submit();
	}
	});


}

function disabledon(){
	document.getElementById("code_a").disabled=false;
	document.getElementById("code_b").disabled=false;
	document.getElementById("code_c").disabled=false;
	document.getElementById("code_d").disabled=false;
}


function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode < 48 || charCode > 57){
		return false;
	}
	// Textbox value

	return true;
}

function isNumberKey2(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	// Textbox value

	var _value = event.srcElement.value;

	// 소수점(.)이 두번 이상 나오지 못하게
	var _pattern0 = /^\d*[.]\d*$/; // 현재 value값에 소수점(.) 이 있으면 . 입력불가
	if (_pattern0.test(_value)) {
		if (charCode == 46) {
		return false;
		}
	}

	// 소수점 둘째자리까지만 입력가능
	var _pattern2 = /^\d*[.]\d{2}$/; // 현재 value값이 소수점 둘째짜리 숫자이면 더이상 입력 불가
		if (_pattern2.test(_value)) {
		alert("소수점 둘째자리까지만 입력가능합니다.");
		return false;
	}

	return true;
}


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
//		$("#catenm").html(msg);

	}else if(msg=='nolowcate'){
		alert("하위카테고리가 존재합니다.");
	//	$("#catenm").html("상품카테고리 선택이 잘못되었습니다.");
	}else{
	document.form1.code.value=sumcode;
	var code_a=document.getElementById("code_a");
	var code_b=document.getElementById("code_b");
	var code_c=document.getElementById("code_c");
	var code_d=document.getElementById("code_d");

	if(code_a.value){
		str[0]=code_a.options[code_a.selectedIndex].text;
	}
	if(code_b.value){
		str[1]=code_b.options[code_b.selectedIndex].text;
	}
	if(code_c.value){
		str[2]=code_c.options[code_c.selectedIndex].text;
	}
	if(code_d.value){
		str[3]=code_d.options[code_d.selectedIndex].text;
	}
	var obj = document.getElementById('Category_table');
	oTr = obj.insertRow();

	oTd = oTr.insertCell(0);
	oTd.id = "cate_name";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell(1);
	oTd.innerHTML = "\
	<input type=text name=category[] value='" + sumcode + "' style='display:none'>\
	";
	oTd = oTr.insertCell(2);
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='img/btn/btn_cate_del01.gif' align=absmiddle></a>";


	}

	});
}

function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('Category_table');
	obj.deleteRow(idx);
}

function mulimgdel(no) {
	if(confirm("해당 이미지를 삭제하시겠습니까?")){
		document.form1.multype.value="delete";
		document.form1.mulimgno.value=no;
		CheckForm('modify')
	}
}

$(document).ready(function(){
	//iframe으로 불러오지 않을경우 왼쪽 메뉴를 hidden 처리한다
	if( window.location != window.parent.location ){
		//console.log( 'iframe' );
	} else {
		hiddenLeft();
		//console.log( 'no iframe' );
	}

	//정보고지 내용을 불러온다
	$(document).on( 'change', '#jungbo_option', function(){
		var jungbo_code = $(this).val();
		var productcode = $("input[name='prcode']").val();
		$.post(
			'ajax_jungbo_option.php',
			{ code : jungbo_code, prcode : productcode }
		).done( function( data ){
			if( data ){
				$("#jungbo_options").html( data );
			} else {
				alert('잘못된 상품군을 선택하셨습니다.');
			}
		});
	});
	//개별 상세정보 별도표기
	$(document).on( 'click', 'input[name="option_chk"]', function( e ){
		var chk_index = $('input[name="option_chk"]').index( e.target );
		if( $(this).prop( 'checked' ) ){
			$('input[name="jungbo_prop_val"]').eq(chk_index).val( '상세정보 별도표기' );
			$('input[name="jungbo_prop_val"]').eq(chk_index).attr('readOnly','true');
		} else {
			$('input[name="jungbo_prop_val"]').eq(chk_index).val( '' );
			$('input[name="jungbo_prop_val"]').eq(chk_index).removeAttr('readOnly');
		}
	});
	//전체 상세정보 별도표기
	$(document).on( 'click', '#jungbo_allchk', function( e ){
		if( $(this).prop( 'checked' ) ){
			$('input[name="option_chk"]').each( function( i, obj ){
				$(this).prop( 'checked', true );
				$('input[name="jungbo_prop_val"]').eq(i).val( '상세정보 별도표기' );
				$('input[name="jungbo_prop_val"]').eq(i).attr('readOnly','true');
			});
		} else {
			$('input[name="option_chk"]').each( function( i, obj ){
				$(this).prop( 'checked', false );
				$('input[name="jungbo_prop_val"]').eq(i).val( '' );
				$('input[name="jungbo_prop_val"]').eq(i).removeAttr('readOnly');
			});
		}
	});

});

//-->
</SCRIPT>


<!-- 하단 스크립트 위치변경 -->
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(mode) {
<?php if ($gongtype=="Y") {?>
	gongname="시작가";
	gongname2="공구가";
<?php } else {?>
     gongname="소비자가격";
     gongname2="판매가격";
<?php }?>
 
	var sHTML = oEditors.getById["ir1"].getIR();
	var sHTML_m = oEditors_m.getById["ir2"].getIR();
	var sHTML_notice = oEditors_pr_notice.getById["ir3"].getIR();
	var sHTML_sizecon = oEditors_pr_sizecon.getById["ir4"].getIR();
	var sHTML_sizecon_m = oEditors_pr_sizecon_m.getById["ir5"].getIR();
	var sHTML_content = oEditors_pr_content.getById["ir6"].getIR();
	document.form1.content.value=sHTML;
	document.form1.content_m.value=sHTML_m;
	document.form1.pr_notice.value=sHTML_notice;
	document.form1.pr_sizecon.value=sHTML_sizecon;
	document.form1.pr_sizecon_m.value=sHTML_sizecon_m;
	document.form1.pr_content.value=sHTML_content;
	if (document.getElementsByName("category[]").length==0) {
		alert("카테고리를 선택하여주세요.");
		document.form1.code_a.focus();
		return;
	}
<?php if(setUseVender()) {?>
	if (document.form1.in_vender.value.length==0) {
		alert("브랜드를 선택하여주세요.");
		document.form1.in_vender.focus();
		return;
	}
<?php }?>
	if (document.form1.productname.value.length==0) {
		alert("상품명을 입력하세요.");
		document.form1.productname.focus();
		return;
	}
	if (CheckLength(document.form1.productname)>200) {
		alert('총 입력가능한 길이가 한글 100자까지입니다. 다시한번 확인하시기 바랍니다.');
		document.form1.productname.focus();
		return;
	}
	if (document.form1.consumerprice.value.length==0) {
		alert(gongname+"을 입력하세요.");
		document.form1.consumerprice.focus();
		return;
	}
	if (isNaN(document.form1.consumerprice.value)) {
		alert(gongname+"을 숫자로만 입력하세요.(콤마제외)");
		document.form1.consumerprice.focus();
		return;
	}
<?php if($_data->vender<=0){?>
	if(document.form1.sellprice.disabled==false) {
		if (document.form1.sellprice.value.length==0) {
			alert(gongname2+"을 입력하세요.");
			document.form1.sellprice.focus();
			return;
		}
		if (isNaN(document.form1.sellprice.value)) {
			alert(gongname2+"을 숫자로만 입력하세요.(콤마제외)");
			document.form1.consumerprice.focus();
			return;
		}
	}
<?php }?>
	if (document.form1.reserve.value.length>0) {
		if(document.form1.reservetype.value=="Y") {
			if(isDigitSpecial(document.form1.reserve.value,".")) {
				alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.reserve.focus();
				return;
			}

			if(getSplitCount(document.form1.reserve.value,".")>2) {
				alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.reserve.focus();
				return;
			}

			if(getPointCount(document.form1.reserve.value,".",2)) {
				alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.reserve.focus();
				return;
			}

			if(Number(document.form1.reserve.value)>100 || Number(document.form1.reserve.value)<0) {
				alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.reserve.focus();
				return;
			}
		} else {
			if(isDigitSpecial(document.form1.reserve.value,"")) {
				alert("적립금은 숫자로만 입력하세요.");
				document.form1.reserve.focus();
				return;
			}
		}
	}

	if( document.form1.checkquantity.value == 'C' ){ //수량 입력
		if (document.form1.quantity.value.length==0) {
			alert("수량을 입력하세요.");
			document.form1.quantity.focus();
			return;
		} else if (isNaN(document.form1.quantity.value)) {
			alert("수량을 숫자로만 입력하세요.");
			document.form1.quantity.focus();
			return;
		} else if (parseInt(document.form1.quantity.value)<0) {
			alert("수량은 0개이상이여야 합니다.");
			document.form1.quantity.focus();
			return;
		}
	}

	miniq_obj=document.form1.miniq;
	maxq_obj=document.form1.maxq;
	if (miniq_obj.value.length>0) {
		if (isNaN(miniq_obj.value)) {
			alert ("최소주문한도는 숫자로만 입력해 주세요.");
			miniq_obj.focus();
			return;
		}
	}
	if (document.form1.checkmaxq[1].checked) {
		if (maxq_obj.value.length==0) {
			alert ("최대주문한도의 수량을 입력해 주세요.");
			maxq_obj.focus();
			return;
		} else if (isNaN(maxq_obj.value)) {
			alert ("최대주문한도의 수량을 숫자로만 입력해 주세요.");
			maxq_obj.focus();
			return;
		}
	}
	if (miniq_obj.value.length>0 && document.form1.checkmaxq[1].checked && maxq_obj.value.length>0) {
		if (parseInt(miniq_obj.value) > parseInt(maxq_obj.value)) {
			alert ("최소주문한도는 최대주문한도 보다 작아야 합니다.");
			miniq_obj.focus();
			return;
		}
	}

	// 배송비 변경
	var deli_err = 0;
	if( $('input[name="deli"]:checked').val() == '2' ){
		$('input[name="deli_select"]').each( function( i, obj ){
			if( $(this).prop('checked') ){
				if( $('input[name=deli_price]').eq(i).val().length == 0 ){
					alert("개별배송비를 입력하세요.");
					$('input[name=deli_price]').eq(i).focus();
					deli_err++;
				} else if( isNaN( $('input[name=deli_price]').eq(i).val() ) ){
					alert("개별배송비는 숫자로만 입력하세요.");
					$('input[name=deli_price]').eq(i).focus();
					deli_err++;
				} else if( parseInt( $('input[name=deli_price]').eq(i).val() ) <= 0){
					alert("개별배송비는 0원 이상 입력하셔야 합니다.");
					$('input[name=deli_price]').eq(i).focus();
					deli_err++;
				}
			}
		});
		if( deli_err > 0 ) return;
	}
	/*
	if(document.form1.deli[3].checked || document.form1.deli[4].checked) {
		if(document.form1.deli[3].checked)
		{
			if (document.form1.deli_price_value1.value.length==0) {
				alert("개별배송비를 입력하세요.");
				document.form1.deli_price_value1.focus();
				return;
			} else if (isNaN(document.form1.deli_price_value1.value)) {
				alert("개별배송비는 숫자로만 입력하세요.");
				document.form1.deli_price_value1.focus();
				return;
			} else if (parseInt(document.form1.deli_price_value1.value)<=0) {
				alert("개별배송비는 0원 이상 입력하셔야 합니다.");
				document.form1.deli_price_value1.focus();
				return;
			}
		}
		else
		{
			if (document.form1.deli_price_value2.value.length==0) {
				alert("개별배송비를 입력하세요.");
				document.form1.deli_price_value2.focus();
				return;
			} else if (isNaN(document.form1.deli_price_value2.value)) {
				alert("개별배송비는 숫자로만 입력하세요.");
				document.form1.deli_price_value2.focus();
				return;
			} else if (parseInt(document.form1.deli_price_value2.value)<=0) {
				alert("개별배송비는 0원 이상 입력하셔야 합니다.");
				document.form1.deli_price_value2.focus();
				return;
			}
		}
	}
	*/

	

	

	if(document.form1.use_imgurl.checked!=true) {
		filesize = Number(document.form1.size_checker.fileSize) + Number(document.form1.size_checker2.fileSize) + Number(document.form1.size_checker3.fileSize) ;
		if(filesize><?=$maxfilesize?>) {
			alert('올리시려고 하는 파일용량이 1M이상입니다.\n파일용량을 체크하신후에 다시 이미지를 올려주세요');
			return;
		}
	}
	tempcontent = document.form1.content.value;
<?php if ($predit_type=="Y"){ ?>
	if(mode=="modify" && tempcontent.length>0 && tempcontent.indexOf("<")==-1 && tempcontent.indexOf(">")==-1 && !confirm("웹편집기 기능추가로 텍스트로만 입력하신 상세설명은\n줄바꾸기가 해제되어 쇼핑몰에서 다르게 보여질 수 있습니다.\n\n재입력하시거나 현재 쇼핑몰에서 해당 상품의 상세설명을\n그대로 마우스로 드래그하여 붙여넣기를 해서 재입력하셔야 합니다.\n\n위와 같이 수정하지 않고 저장하시려면 [확인]을 누르세요.")){
		return;
	}
<?php }?>
	//정보고지
/*
	if(document.form1.prop_type.value != ""){
		var prop_val = document.form1.prop_type.value;
		if(document.form1.prop_type.value == "001"){
			for(var ix=1; ix<13; ix++){
				prop_val += "||" + $('#prop001'+ix).val();
			}
		}
		var prop_opt_val = document.form1.prop_type.value;
		if(document.form1.prop_type.value == "001"){
			for(var ix=1; ix<13; ix++){
				prop_opt_val += "||" + $('#prop_opt001'+ix).val();
			}
		}
		//alert(prop_val);
		document.form1.sabangnet_prop_option.value = prop_opt_val;
		document.form1.sabangnet_prop_val.value = prop_val;
	}
*/
	//상품군에 따른 변경 2016 01 19 유동혁
	if( jQuery.type( $("input[name='jungbo_prop_option']") ) !== "undefined"  ){
		var prop_opt_val = $("#jungbo_option").val();
		$("input[name='jungbo_prop_option']").each(function( i, obj ){
			prop_opt_val += "||" + $(this).val();
		});
		var prop_val = $("#jungbo_option").val();
		$("input[name='jungbo_prop_val']").each(function( i, obj ){
			prop_val += "||" + $(this).val();
		});
		document.form1.sabangnet_prop_option.value = prop_opt_val;
		document.form1.sabangnet_prop_val.value = prop_val;
	}

	//옵션 체크 관련
	if ($("input[name='opt_select']:checked").val() == 1) { // 옵션 사용일 경우
		if ($("input[name='opt_type']:checked").val() == 0) { // 조합형일 경우			
			var count = 0;
			$("input[name^=opt_subject]").each( function( subject_idx ) {
				var content = $("input[name^=opt_content]").eq( subject_idx );
				if( $(this).val().length > 0 && $(content).val().length > 0 ) {
				} else {
					alert("옵션명과 옵션항목을 입력해 주십시오.");
					count++;
					if( $(this).val().length == 0)
					{
						$(this).focus();
					} else {
						if( $(content).val().length == 0) {
							$(content).focus();
						}
					}
					return false;
				}
			});
		} else if ($("input[name='opt_type']:checked").val() == 1) { // 독립형일 경우		
			var count = 0;
			$("input[name='spl_subject[]']").each(function(subject_idx) {
				var content = $("input[name='spl[]']").eq( subject_idx );
				if( $(this).val().length > 0 && $(content).val().length > 0 ) {
				} else {
					alert("옵션명과 옵션항목을 입력해 주십시오.");
					count++;
					if( $(this).val().length == 0)
					{
						$(this).focus();
					} else {
						if( $(content).val().length == 0) {
							$(content).focus();
						}
					}
					return false;
				}
			});
		}
		if (count > 0) return false;
		if (count == 0 && $("input[name='option_create']").val() != 1) {
			alert("옵션목록이 없거나 생성 옵션/항목과 옵션목록이 다릅니다.");
			return false;
		}
	}

    // 독립형 옵션 필수구분
    var tf_text = '';
    var tf_arr = [];
    $('input[name="necessary_tf[]"').each( function( i, obj ) {
        if( $(this).prop( 'checked' )  ){
            tf_arr.push( 'T' );
        } else {
            tf_arr.push( 'F' );
        }
    });
    tf_text = tf_arr.join( '@#' );
    $('#option1_tf').val( tf_text );

	var formCheck = true;
	$(".required").each(function(){
		if(!$(this).val()){
			alert("["+$(this).attr('alt')+"]를(을) 입력 하지 않으셨습니다.");
			$(this).focus();
			formCheck = false;
			return false;
		}
	});

	if(formCheck){
		document.form1.iconvalue.value="";
		num = document.form1.iconnum.value;
		for(i=0;i<num;i++){
			if(document.form1.icon[i].checked) document.form1.iconvalue.value+=document.form1.icon[i].value;
		}
		if (document.form1.insertdate1.checked) document.form1.insertdate.value="Y";
		document.form1.mode.value=mode;
		document.form1.submit();
	}
}

//-->
</SCRIPT>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<style>
	.CLS_sabangnet_tbody tr td input{
		text-align:center;
	}

	.optionStyle caption{
		margin-top: 15px;
		margin-bottom: 10px;
		font-weight: bold;
	}

	.optionStyle fieldset {
		text-align: center;
	}

	.optionStyle fieldset span.frm_info {
		display: block;
	}

	.optionStyle button {
		margin-top: 10px;
	}

	#sit_option_addfrm_btn {
		display:'';
	}
</style>
<?if($popup!="YES"){?>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>기본정보 등록/수정</span></p></div></div>
<?}else{?>
<div class="admin_linemap"><div class="line"></div></div>
<?}?>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
<td valign="top">
<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
<tr>
<td>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<col width=240 id="menu_width"></col>
<col width=10></col>
<col width=></col>
<tr>
	<td valign="top">
	<?php include("menu_product.php"); ?>
	</td>
	<td></td>

	<td>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
	<table cellpadding="0" cellspacing="0" width="100%">


	<input type=hidden name=mode>
	<input type=hidden name=multype>
	<input type=hidden name=mulimgno>
	<input type=hidden name=code id=code value="<?=$code?>">
	<input type=hidden name=changecode value="<?=$changecode?>">
	<input type=hidden name=prcode value="<?=$prcode?>">
	<input type=hidden name=htmlmode value='wysiwyg'>
	<input type=hidden name=delprdtimg>
	<input type=hidden name=option1>
	<input type=hidden name=option2>
	<input type=hidden name=option_price>
	<input type=hidden name=option_ea>
	<input type=hidden name=option_consumer>
	<input type=hidden name=optcode>
	<input type=hidden name=optreserve>
	<input type=hidden name=insertdate>
    <input type='hidden' name='option1_tf' id='option1_tf' value='' >
    <input type='hidden' name='option_create' id='option_create' value="<?=$_data->option_type == '0' || $_data->option_type == '1'?'1':'0'?>" >

	<input type=hidden name=popup value="<?=$popup?>">
	<input type=hidden name=category_data value = "<?=$_POST["category_data"]?>">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">기본정보 등록/수정</div>
				</td>
			</tr>

			<tr><td height="10"></td></tr>
			<tr>
				<td>
					<div class="tab_style1" data-ui="TabMenu">
						<div class="tab-menu clear">
							<a data-content="menu" class="active" title="선택됨">개별상품등록</a>
							<a data-content="menu"  href="product_register.set.group.php">세트상품등록</a>
						</div>
						<!-- 개별상품등록 -->
						<div class="tab-content active" data-content="content">
							개별상품등록 내용 여기에 넣어주세요.
						</div>

						<!-- 세트상품등록 -->
						<div class="tab-content" data-content="content">
							세트상품등록 내용 여기에 넣어주세요.
						</div>
					</div>
				</td>
			</tr>

			<!-- 초기 입력시 자사몰 or 제휴몰 선택-->
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>필수표시 항목</span></div>
				</td>
			</tr>


			<tr><td height=3></td></tr>

			<tr>
				<td>
				<?
					if(!$code && !$changecode){
						 $classname="class=\"graybg_wrap\"";
						 $graybgdisplay="style=\"display:block\"";
					}else{
						$graybgdisplay="style=\"display:none\"";
					}

				?>
				<div <?=$classname?>>
<!--				<div class="graybg" <?=$graybgdisplay?>><div class="ment01">상단의 카테고리를 선택하면 작성하실 수 있습니다.</div></div>-->

				<div class="table_style01">

				<table cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					<th><span>카테고리 선택</span> <font color='#FF0000' > *필수 </font> </th>
					<td colspan="3">
<?php
	//카테고리 SELECT BOX를 불러온다
	echo $codeA_list;
	echo $codeB_list;
	echo $codeC_list;
	echo $codeD_list;
	//카테고리 SELECT 버튼을 불러온다
	echo $codeSelect;
	//카테고리 스크립트 실행
	echo $strcodelist;
	echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";					
?>
					</td>
				</table>
				</div>
				</div><!-- 그레이배경 div -->
				</td>
			</tr>

			</table>

		</td>
	</tr>



	<tr>
		<td>
			<div class="table_style01">

			<table width=100% cellpadding=0 cellspacing=1 border=1 style="border-collapse:collapse">
			<tr>
			<th height=30><span>카테고리</span></th>
			<td>
			<div class="table_none">
			<table width=100% cellpadding=0 cellspacing=1 id=Category_table>
				<col><col width=50 style="padding-right:10"><col width=52 align=right>
<?
	//해당 상품의 카테고리 리스트 가져오기
	if($cate_array){
		foreach($cate_array as $v=>$k){
?>
				<tr>
					<td id=cate_name><?=$k[c_codename]?></td>
					<td>
					<input type=text name=category[] value="<?=$k[c_category]?>" style="display:none">
					</td>
					<td>
					<!--<img src="../img/i_select.gif" border=0 onClick="cate_mod(document.forms[0]['cate[]'][0],this.parentNode.parentNode)" class=hand>-->
					<a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="img/btn/btn_cate_del01.gif" border=0 align=absmiddle></a>
					</td>
				</tr>
<?
		}
	}
?>

			</table>
			</div>
			</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>

	<tr><td height=3></td></tr>
	<tr>
		<td>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="center">

				<table cellpadding="0" cellspacing="0" width="100%">


				<tr>
					<td>
		            <div class="table_style01">
					<table cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">

<?php if(setUseVender()) {?>
					<tr>
						<th><span>브랜드</span> <font color='#FF0000' > *필수</font></th>
						<td class="td_con1" colspan="3">
						<select name="in_vender">
						<option value=''>선택해 주세요</option>
<?php
		$sql = "SELECT vi.vender,vi.id,vi.com_name, pb.brandname FROM tblvenderinfo vi left join tblproductbrand pb on vi.vender=pb.vender order by pb.brandname";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			if ($row->vender == $_data->vender) {
				$vender_popup_btn	= "<A HREF=\"javascript:viewVenderInfo({$row->vender})\"><B>{$row->brandname}</B></A>";
				$chk_opt	=" selected";
			} else {
				if ($vender_popup_btn == "") $vender_popup_btn	= "";
				$chk_opt	="";
			}
			echo "<option value='{$row->vender}'{$chk_opt}>{$row->brandname}</option>";
		}
		pmysql_free_result($result);
?>
						</select> <?=$vender_popup_btn?><input type='hidden' name='old_vender' value='<?=$_data->vender?>'>
						</td>
					</tr>
<?php } ?>
					<tr>
						<th><span>상품명</span> <font color='#FF0000' > *필수</font></th>
						<td class="td_con1" colspan="3"><input name=productname value="<?=str_replace("\"","&quot",$_data->productname)?>" size=80 maxlength=250 onKeyDown="chkFieldMaxLen(250)" class="input" style="width:100%">
						</td>
					</tr>
					<tr>
						<th><span>상품명(한글)</span> <font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3"><input name=productname_kor value="<?=str_replace("\"","&quot",$_data->productname_kor)?>" size=80 maxlength=250 onKeyDown="chkFieldMaxLen(250)" class="input" style="width:100%">
						</td>
					</tr>
					<!-- 상품몰 사용안함 -->
					<tr style='display:none;'>
						<th><span>상품몰 타입</span></th>
						<td>
							<input type='radio' name='mall_type' value='0' <? if( $_data->mall_type == '0' || is_null( $_data->mall_type ) ){ echo 'CHECKED'; } ?> > ALL
							<input type='radio' name='mall_type' value='1' <? if( $_data->mall_type == '1' ){ echo 'CHECKED'; } ?>> 교육할인
							<input type='radio' name='mall_type' value='2' <? if( $_data->mall_type == '2' ){ echo 'CHECKED'; } ?> > 기업
						</td>
					</tr>
					<!-- 해외배송 사용안함 -->
					<tr style='display:none;'>
						<th><span>상품 타입</span></th>
						<td>
							<input type='radio' name='overseas_type' value='0' <? if( $_data->overseas_type == '0' || is_null( $_data->overseas_type ) ){ echo 'CHECKED'; } ?> > 일반상품
							<input type='radio' name='overseas_type' value='1' <? if( $_data->overseas_type == '1' ){ echo 'CHECKED'; } ?>> 해외배송 상품
						</td>
					</tr>
					<tr>
						<th><span>상품진열여부</span> <font color='#FF0000' > *필수</font></th>
						<td class="td_con1">
<?php
	$checked['display'][$_data->display] = "checked";
?>
							<input type=radio id="idx_display1" name=display value="Y" <?=$checked['display']['Y']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_display1>진열함</label> &nbsp;
							<input type=radio id="idx_display2" name=display value="N" <?=$checked['display']['N']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_display2>진열안함</label></td>
					</tr>

					<tr>
						<th><span>네이버지식쇼핑</span> <font color='#FF0000' > *필수</font></th>
						<td class="td_con1">
<?php
	$checked['naver_display'][$_data->naver_display] = "checked";
?>
							<input type=radio id="idx_naver_display1" name=naver_display value="Y" <?=$checked['naver_display']['Y']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_naver_display1>연동함</label> &nbsp;
							<input type=radio id="idx_naver_display2" name=naver_display value="N" <?=$checked['naver_display']['N']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_naver_display2>연동안함</label></td>
					</tr>

<!--
					<tr>
						<th><span>회원할인적용여부</span></th>
						<td class="td_con1">
<?php
	$checked['vip_product'][$_data->vip_product] = "checked";
?>
							<input type=radio id="idx_display3" name=vip_product value="0" <?=$checked['vip_product']['0']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_display3>적용</label> &nbsp;
							<input type=radio id="idx_display4" name=vip_product value="1" <?=$checked['vip_product']['1']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_display4>미적용</label></td>
					</tr>
-->
					<tr>
						<th><span>등록/수정일</span></th>
						<td class="td_con1" colspan="3">
<?php
	if (ord($prcode)==0) {
		echo "자동입력";
	} else {
		if ($_data) {
			echo " ".str_replace("-","/",substr($_data->modifydate,0,16))."\n";
			echo "(상품코드 : <span class=\"font_orange\">{$_data->productcode}</span> / ERP 코드 : <span class=\"font_orange\">{$_data->prodcode}-{$_data->colorcode}</span>)";
			echo "&nbsp;&nbsp;&nbsp;<a href=\"http://{$shopurl}/front/productdetail.php?productcode={$_data->productcode}\" target=_blank><img src=\"images/productregister_goproduct.gif\" align=absmiddle border=0></font></a>";
		}
		echo "<input type=hidden name=productcode value=\"{$_data->productcode}\">\n";
	}
?>
							<input type='hidden' id='it_id' value='<?=$_data->productcode?>' />
						</td>
					</tr>
					<tr>
						<th><span>상품 등록/수정 날짜</span> <font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3"><input type=checkbox id="idx_insertdate10" name=insertdate1 value="Y" onClick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_insertdate10>등록/수정 날짜 고정</label>&nbsp;<span class="font_orange">(* 상품수정시 등록/수정 날짜가 변경되지 않습니다.)</span></td>
					</tr>
					<tr>
						<th><span>핫딜 상품</span> <font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3"><input type=checkbox  name=hotdealyn value="Y" <?=$checked["hotdealyn"]['Y']?>><span class="font_orange">* 선택시 핫딜용 상품으로 구분됩니다. (핫딜 이벤트에서만 노출됩니다.)</span></td>
					</tr>

                    <!-- 2016-11-30 erp 가격연동여부 체크 추가 S -->
					<tr>
						<th><span>ERP 가격연동</span> <font color='#FF0000' > *필수</font></th>
						<td class="td_con1">
<?php
	$checked['erp_price_yn'][$_data->erp_price_yn] = "checked";
?>
							<input type=radio id="idx_erp_price_yn1" name=erp_price_yn value="Y" <?=$checked['erp_price_yn']['Y']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_erp_price_yn1>연동함</label> &nbsp;
							<input type=radio id="idx_erp_price_yn2" name=erp_price_yn value="N" <?=$checked['erp_price_yn']['N']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_erp_price_yn2>연동안함</label></td>
					</tr>
                    <!-- 2016-11-30 erp 가격연동여부 체크 추가 E -->
<?php	
	if($_data->vender>0){ 
?>
					<input type=hidden name="assembleuse" value="N">
					<tr>
						<th><span class="font_orange"><b><?=($gongtype=="Y"?"공구가":"판매가격")?></b></span> <font color='#FF0000' > *필수</font></th>
						<td class="td_con1"><input name=sellprice value="<?=$_data->sellprice?>" size=16 maxlength=10 class="input" style=width:98%></td>
						<th><span class="font_orange"><b><?=($gongtype=="Y"?"시작가":"시중가격")?></b></span><font color='#FF0000' >  *필수</font></th>
						<td class="td_con1"><input name=consumerprice value="<?=(int)(ord($_data->consumerprice)?$_data->consumerprice:"0")?>" size=16 maxlength=10 class="input" style=width:100%><br><span class="font_orange">* <strike>5,000</strike>로 표기됨, 0 입력시 표기안됨&nbsp;</span></td>
					</tr>
<?php	
	} else { 
?>

					<tr>
<?php
		if(ord($prcode)==0) { 
?>
						<th>
		                <div class="table_none">
						<table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td width="100%"><input type="radio" name="assembleuse" value="N" <?=($_data->assembleuse=="Y"?"":"checked")?> id="idx_assembleuseY" style="border:none" onClick="assembleuse_change();"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_assembleuseY"><span class="font_orange"><b><?=($gongtype=="Y"?"단일 공구가":"단일 판매가격")?></b></span> <font color='#FF0000' > *필수</font></label></td>
						</tr>
						<tr style="display: none">
							<td width="100%"><input type="radio" name="assembleuse" value="Y" <?=($_data->assembleuse=="Y"?"checked":"")?> id="idx_assembleuseN" style="border:none" onClick="assembleuse_change();"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_assembleuseN"><span class="font_orange"><b><?=($gongtype=="Y"?"코디/조립 판매가":"코디/조립 판매가")?></b></span></label></td>
						</tr>
						<tr>
							<td width="100%">&nbsp;&nbsp;&nbsp;<span class="font_orange" style="font-size:8pt;">* 한번 등록후 변경불가</span></td>
						</tr>
						</table>
		                </div>

		                </th>
<?php	
		} else { 
?>
						<th>
						<div class="table_none">
		                <table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<input type=hidden name="assembleuse" value="<?=$_data->assembleuse?>">
						<tr>
							<td width="100%" height="30">
<?php
			if($_data->assembleuse=="Y") { 
?>
								<span class="font_orange"><b><?=($gongtype=="Y"?"코디/조립 판매가":"코디/조립 판매가")?></b></span>
<?php 
			} else { 
?>
								<span class="font_orange"><b><?=($gongtype=="Y"?"단일 공구가":"단일 판매가격")?></b></span>
<?php 
			} 
?>
							</td>
						</tr>
						</table>
		                </div>

		                </th>
<?php 
		} 
?>
						<td class="td_con1"><input name=sellprice onkeypress="return isNumberKey(event)" value="<?=$_data->sellprice?>" size=16 maxlength=10 class="input" style=width:98% <?=($_data->assembleuse=="Y"?"disabled style='background:#C0C0C0'":"")?>><br><span class="font_orange" style="font-size:8pt;">* 코디/조립 사용시 판매가격 등록불가<br><b>&nbsp;&nbsp;</b>및 상품옵션, 패키지그룹 사용불가</span></td>
						<th><span class="font_orange"><b><?=($gongtype=="Y"?"시작가":"시중가격")?></b></span><font color='#FF0000' > *필수</font> </th>
						<td class="td_con1"><input name=consumerprice onkeypress="return isNumberKey(event)" value="<?=(int)(ord($_data->consumerprice)?$_data->consumerprice:"0")?>" size=16 maxlength=10 class="input" style=width:100%><br><span class="font_orange">* <strike>5,000</strike>로 표기됨, 0 입력시 표기안됨&nbsp;</span><br><br></td>
					</tr>
<?php
	} 
?>
					<tr>
						<th><span>적립금(률)</span> <font color='#0099BF' > 선택</font></th>
						<td class="td_con1">
                            <input name=reserve value="<?=$_data->reserve?>" size=16 maxlength=6 class="input" style="width:60%" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.reservetype.value);" onkeypress="return isNumberKey2(event)"> 
                            <select name="reservetype" class="select" onChange="chkFieldMaxLenFunc(this.form,this.value);">
                                <option value="Y"<?=($_data->reservetype!="N"?" selected":"")?>>적립률(%)</option>
                                <option value="N"<?=($_data->reservetype!="N"?"":"selected")?>>적립금(￦)</option>
                            </select>
                            <br><span class="font_orange" style="font-size:8pt;letter-spacing:-0.5pt">* 적립률은 소수점 둘째자리까지 입력 가능합니다.<br>* 적립률에 대한 적립 금액 소수점 자리는 반올림.</span>
						</td>
						<th><span>구입원가</span> <font color='#0099BF' > 선택</font></th>
						<td class="td_con1"><input name=buyprice onkeypress="return isNumberKey(event)" value="<?=$_data->buyprice?>" size=16 maxlength=10 class="input" style="width:100%"></td>
					</tr>
					<tr>
						<th><span>제조사</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1"><input name=production value="<?=$_data->production?>" size=23 maxlength=20 onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:FiledSelect('PR');"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></td>
						<th><span>원산지</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1"><input name=madein value="<?=$_data->madein?>" size=23 maxlength=20 onKeyDown="chkFieldMaxLen(30)" class="input"><a href="javascript:FiledSelect('MA');"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></td>
					</tr>
                    <tr style='display:none;'>
                        <th style='display:none;'><span>브랜드</span></th>
						<td style='display:none;' class="td_con1" colspan='3' ><input type=text name=brandname value="<?=$_data->brandname?>" size=23 maxlength=50 onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:BrandSelect();"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a><input type=hidden name=brand_idx value="<?=$_data->brand?>"><br>
						<span class="font_orange">* 브랜드를 직접 입력시에도 등록됩니다.</span></td>
                    </tr>
					<tr>
						<th><span>매입처</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" >
                            <input name=model value="<?=$_data->model?>" size=23 maxlength=40 onKeyDown="chkFieldMaxLen(50)" class="input">
                                <a href="javascript:FiledSelect('MO');">
                                    <img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle">
                                </a>
                        </td>
                        <th><span>마진률 ( % )</span></th>
                        <td>
                            <input type='text' name='rate' id='rate' value='<?=$up_rate?>' >
                            <input type='checkbox' name='vender_rate' id='vender_rate' value ='1' checked >
                            * 브랜드 기본 마진률 사용 
                        </td>
                        <script>
                            $('#vender_rate').click( function( event ) {
                                if( $(this).prop( 'checked' ) ){
                                    $('#rate').attr( 'disabled', 'true' );
                                } else {
                                    $('#rate').removeAttr( 'disabled' );
                                }
                            });
                        </script>
					</tr>
					<!-- 2016-09-09 복구할거면, mdcommentcolor 필드를 따로 만들어야함. color_code 는 이미 쓰고 있음.
                    <tr>
						<th><span>MD 코멘트</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3">
							<input name=mdcomment value="<?=$_data->mdcomment?>" size=35 maxlength=500 onKeyDown="chkFieldMaxLen(500)" class="input" style="width:50%">
<?php
	if( $_data->mdcommentcolor == '' ) $mdcommentcolor = '000000';
	else $mdcommentcolor = $_data->mdcommentcolor;
?>
							<input name="mdcommentcolor" class="jscolor" value='<?=$mdcommentcolor?>' >
						</td>
					</tr> -->

					<tr>
						<th><span>진열코드</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3"><input name=selfcode value="<?=$_data->selfcode?>" size=35 maxlength=20 onKeyDown="chkFieldMaxLen(20)" class="input" style="width:100%"></td>
					</tr>

					<!--관련상품 세팅NEW 06 30 원재-->
					<!-- <script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script> -->
					<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
					<script src="../js/product_related.js"></script>
					<?include("layer_rproduct.php");?>
<!--					
					<TR id='ID_RelationProduct'>
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
									<?foreach($r_product_list as $rval){?>	
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
												<!-- <img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bannerProduct['tinyimage']?>" border="1"/> -->
												<img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir.'shopimages/product/', $rval->tinyimage );?>" border="1"/>
												<input type='hidden' name='relationProduct[]' value='<?=$rval->productcode?>'>
											</td>
											<td style='border:0px' align="left"><?=$rval->productname?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$rval->productcode?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
											</td>
										</tr>
									<?}?>
									</table>
								</div>
						</td>
					</TR>
					  -->
					<!--//관심상품 세팅NEW-->

					<?/*
					<!--관심상품 세팅-->
					<!--
					대코앤이 기존 스타일과 달라 폐기 처분 합니다 06 30 원재 ㅠㅠ
					####################################################################
					상품별 관심 상품 세팅 06 29 원재 ㅠㅠ
					*관심상품은 상품별 최대 10개까지 세팅됩니다.
					*자동노출의 경우 등록된 관심상품은 삭제되며, 카테고리 조건에 맞는 상품이 자동 노출됩니다
					*수동노출을 선택했을 경우에만 세팅된 관심상품이 노출됩니다
					*수동노출 상품이 10개가 되지 않을경우 자동노출상품도 함께 노출됩니다.
					#####################################################################
					-->
					
					<script src="jscript/jquery-ui_won.min.js"></script>
					
					
					<script>
						function view_rproduct(){//관련상품 조회 및 등록 창 열기
							window.open("product_related.php","","width=700,height=700,scrollbars=yes","_blank");
						}

						$(function(){//마우스 드래그로 관련상품 노출 순서를 설정 할 수 있습니다
							$( "#r_product_list" ).sortable({
								scroll: false
							});
							$( "#r_product_list" ).disableSelection();
						});

						$(document).on("click",".del_rproduct",function(){//선택된 관련상품 삭제
							var obj = $(this).parent();
							obj.remove();
						});
						
						$(document).on("change","input[name='r_type']",function(){
							var chk_type = $(this).val();
							
							if(chk_type == "1"){
								if(confirm('자동노출로 전환시 기존에 등록된 관련상품은 삭제됩니다. 자동노출로 전환 하시겠습니까?')){
									$("#r_product_list").children().remove();
									$("#r_product_table").fadeOut('fast');
								}else{
									$(this).next().prop("checked",true);
								}
							}

							if(chk_type=="2"){
								$("#r_product_table").fadeIn('fast');
							}

						});
					</script>
					
					<tr>
						<th><span>관련상품</span><font color='#0099BF' > 선택</font>
						
						</th>
						
						<td class="td_con1" colspan="3">
						<input type="radio" name="r_type"  value="1" <?=$chk_r1?>>자동노출 <input type="radio" name="r_type" value="2" <?=$chk_r2?>>수동노출
							<table width="100%" id="r_product_table" <?=$chk_r3?>>
								<tr>
									<td colspan="2"><a onclick="view_rproduct();"><img src="images/btn_select2.gif" border="0" hspace="2"></a></td>
								</tr>
								<tr>
									<td><a href="javascript:T_layer_open('layer_product_sel','relationProduct');">원재테스트</a></td>
								</tr>
								<tr>
									<td width="30%"><b>관련상품리스트</b><br>마우스 드래그로<br> 순서를 변경 할 수 있습니다</td> 
									<td>
									
										<ul id="r_product_list">
										<?if($r_product_list){?>	
											<?foreach($r_product_list as $r_val){?>
											<li style="height:22px;">
												<input type="hidden" name="r_product[]" value="<?=$r_val->r_productcode?>,<?=$r_val->r_productname?>">
												<?=$r_val->r_productname?>
												<img src='images/icon_del1.gif' border='0' style="vertical-align:middle;" class="del_rproduct">
											</li>
											<?}?>
										<?}?>
										</ul>
										
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<!--//관련상품 세팅-->
					*/?>
					<tr style='display:none;'>
						<th><span>카드혜택</span></th>
						<td class="td_con1" colspan="3"><input name=card_benefit value="<?=$_data->card_benefit?>" size=35 maxlength=200 onKeyDown="chkFieldMaxLen(200)" class="input" style="width:100%"></td>
					</tr>
					<tr>
						<td class="td_con_orange" colspan="4" style="border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid; border-left:1px solid #b9b9b9;">
						<span class="font_orange">* 쇼핑몰에서 자동으로 발급되는 상품코드와는 별개로 운영상 필요한 자체품목코드를 입력해 주세요.<br>
						* 진열코드 관련 설정은 <a href="shop_productshow.php"><span class="font_blue">상점관리 > 쇼핑몰 환경 설정 > 상품 진열 기타 설정</a></span> 변경할 수 있습니다.
						</span></td>
					</tr>
					<tr>
						<th><span>출시일</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3"><input name=opendate value="<?=$_data->opendate?>" size=20 maxlength=8 class="input">&nbsp;&nbsp;예) <?=DATE("Ymd")?>(출시년월일)<br>
						<span class="font_orange">* 가격비교 페이지 등 제휴업체 관련 노출시 사용됩니다.<br>* 잘못된 출시일 지정으로 인한 문제는 상점에서 책임지셔야 됩니다.</span></td>
					</tr>

					<tr>
						<th><span>품절</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3">
							<input type='checkbox' name='soldout' value='Y' <? if( $_data->soldout == 'Y' ){ echo 'checked'; } ?>> 품절
						</td>
					</tr>

					<tr>
						<th><span>수량</span> <font color='#FF0000' > *필수</font></th>
						<td class="td_con1" colspan="3">
							<input type='radio' name='checkquantity' value='F' <? if( $_data->quantity >= 999999999 ){ echo 'checked'; } ?> > 무제한
							<input type='radio' name='checkquantity' value='C' <? if( $_data->quantity < 999999999 ){ echo 'checked'; } ?> > 수량
							<input type='text' name='quantity' size='9' maxlength='9' value='<?=$_data->quantity?>' 
								<? if( $_data->quantity >= 999999999 ) { echo 'readonly style="background : silver; " '; } ?> 
							> 개
							<script>
								//수량처리 script 2016-03-09 유동혁
								$(document).on( 'click', 'input[name="checkquantity"]', function(){
									if( $(this).val() == 'F' ){
										$('input[name="quantity"]').attr( 'readonly', 'true' ).css( 'background', 'silver' );
										
									} else if( $(this).val() == 'C' ){
										$('input[name="quantity"]').removeAttr( 'readonly' ).css( 'background', 'white');
									}
								});
							</script>
						</td>
					</tr>
					
					<tr>
						<th><span>최소구매수량</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1"><input type=text onkeypress="return isNumberKey(event)" name=miniq value="<?=($miniq>0?$miniq:"1")?>" size=5 maxlength=5 class="input"> 개 이상</td>
						<th><span>최대구매수량</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1"><input type=radio onkeypress="return isNumberKey(event)" id="idx_checkmaxq1" name=checkmaxq value="A" <?php if (ord($maxq)==0 || $maxq=="?") echo "checked ";?> onClick="document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_checkmaxq1>무제한</label><br><input type=radio id="idx_checkmaxq2" name=checkmaxq value="B" <?php if ($maxq!="?" && $maxq>0) echo "checked"; ?> onClick="document.form1.maxq.disabled=false;document.form1.maxq.style.background='white';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_checkmaxq2>수량</label> : <input name=maxq size=5 maxlength=5 onkeypress="return isNumberKey(event)" value="<?=$maxq?>" class="input"> 개 이하
						<script>
						if (document.form1.checkmaxq[0].checked) { document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver'; }
						else if (document.form1.checkmaxq[1].checked) { document.form1.maxq.disabled=false;document.form1.maxq.style.background='white'; }
						</script>
						</td>
					</tr>
<!-- 배송비 변경 2016-02-17 유동혁 -->
					<tr>
						<th><span>개별배송비</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3">
							<div class="table_none">
							<table cellSpacing='0' cellPadding='0' width="100%" border='0' >
								<tr>
									<td>
										<input type='radio' name='deli' id='deli0' value='0' <? if( $deli == '0' || is_null($deli) ){ echo 'checked'; } ?> >
										<label for='deli0' >기본 배송비 <b>유지</b></label>
										<input type='radio' name='deli' id='deli1' value='1' <? if( $deli == '1' ){ echo 'checked'; } ?> >
										<label for='deli1' >기본 배송비 <b><font color="#0000FF">무료</font></b></label>
										<input type='radio' name='deli' id='deli2' value='2' <? if( $deli == '2' ){ echo 'checked'; } ?> >
										<label for='deli2' >기본 배송비 <b><font color="#FF0000">유료</font></b></label>
									</td>
								</tr>
								<tr>
									<td height="5"></td>
								</tr>
								<tr id='ID_deli_tr' style='display:none;'>
									<td>
										<table cellSpacing='0' cellPadding='0' width="100%" style='background-color: #f8f8f8;  border: 1px solid #b9b9b9;' >
											<caption>
												<col width='150px;'>
												<col width='*' >
											</caption>
											<tr>
												<td style='padding: 2 2 2 2;' >
													<input type='radio' name='deli_select' id='deliselect0' value='0' <? if( $deli_select == '0' || is_null($deli_select) ){ echo 'checked'; } ?> >
													<label for='deliselect0' >고정배송비</label>
												</td>
												<td style='padding: 2 2 2 2;' >
													배송비
													<input type='text' name='deli_price' value='<?=$deli_price?>' >
													원 ( 수량 / 주문금액에 상관없이 <b><font color="#FF0000">배송비 고정</font></b> )
												</td>
											</tr>
											<tr>
												<td style='padding: 2 2 2 2;'>
													<input type='radio' name='deli_select' id='deliselect1' value='1' <? if( $deli_select == '1' ){ echo 'checked'; } ?> >
													<label for='deliselect1' >수량별 배송비</label>
												</td>
												<td style='padding: 2 2 2 2;'>
													배송비
													<input type='text' name='deli_price' value='<?=$deli_price?>' >
													원 ( 구매수 대비 개별배송비 증가 : <b><font color="#FF0000">상품구매수 X 개별배송비</font></b> )
												</td>
											</tr>
											<tr>
												<td style='padding: 2 2 2 2;'>
													<input type='radio' name='deli_select' id='deliselect2' value='2' <? if( $deli_select == '2'){ echo 'checked'; } ?> >
													<label for='deliselect2' >수량별비례 배송비</label>
												</td>
												<td style='padding: 2 2 2 2;'>
													배송비
													<input type='text' name='deli_price' value='<?=$deli_price?>' > 원
													<input type='text' name='deli_qty' value='<?=$deli_qty?>' style='width:50px;' > 개 마다 기본 배송비 반복 부과 ( <b><font color="#FF0000"> 상품구매수 대비 배송비</font></b> )
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							</div>
						</td>
					</tr>
					<script>
						$(document).ready( function(){
							disabled_deli();
							un_disabled_deli();
							if( $('input[name="deli"]:checked').val() == '2' ){
								$('#ID_deli_tr').show();
							}
						});

						$(document).on( 'click', 'input[name="deli"]', function( event ){
							if( $(this).val() == '2' ){
								$('#ID_deli_tr').show();
								un_disabled_deli();
							} else {
								$('#ID_deli_tr').hide();
								disabled_deli();
							}
						});

						$(document).on( 'click', 'input[name="deli_select"]', function( event ){
							disabled_deli();
							un_disabled_deli();
						});

						function disabled_deli(){
							$('input[name="deli_price"]').each( function(){
								$(this).attr('disabled','true').css( 'background-Color', '#EFEFEF' );
							});
							$('input[name="deli_qty"]').attr('disabled','true').css( 'background-Color', '#EFEFEF' );
						}

						function un_disabled_deli(){
							$('input[name="deli_price"]').each( function( i, obj ){
								if( $('input[name="deli_select"]').eq(i).prop('checked') ){
									$(this).removeAttr('disabled').css( 'background-Color', '' );
								}
							});
							if( $('input[name="deli_select"]').last().prop('checked') ){
								$('input[name="deli_qty"]').removeAttr('disabled').css( 'background-Color', '' );
							}
						}

					</script>
<!-- //배송비 변경 2016-02-17 유동혁 -->
					
					
					<tr>
						<th><span>TAG</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3"><input name=keyword value="<?php if ($_data) echo $_data->keyword; ?>" size=80 maxlength=100 onKeyDown="chkFieldMaxLen(100)" class="input" style=width:100%></td>
					</tr>
					<tr>
						<th><span>관련상품 TAG</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3"><input name=relation_tag value="<?php if ($_data) echo $_data->relation_tag; ?>" size=80 maxlength=100 onKeyDown="chkFieldMaxLen(100)" class="input" style=width:100%></td>
					</tr>
					<tr>
						<th><span>컬러 관리</span></th>
						<td class="td_con1" colspan="3">
<?php
    $checked['color'][$_data->color_code] = "checked";
	$arrDbColorCode = dataColor();
    //exdebug($arrDbColorCode);
?>
								<?foreach($arrDbColorCode as $ck => $cv){?>
                                        <input name="smart_search_color" id="smart_search_color" type="radio" value="<?=$cv->color_name?>" <?=$checked[color][$cv->color_name]?>>
                                        <img src="../../static/img/test/<?=$cv->color_img?>" alt="<?=$cv->color_name?>" title="<?=$cv->color_name?>">
								<?}?>
						</td>
					</tr>
					<tr>
						<th><span>성별</span> <font color='#FF0000' > *필수</font></th>
						<td class="td_con1">
<?php
	$checked['sex'][$_data->sex] = "checked";
?>
							<input type=radio name=sex value="M" <?=$checked['sex']['M']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'">남자</label> &nbsp;
							<input type=radio name=sex value="F" <?=$checked['sex']['F']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'">여자</label> &nbsp;
							<input type=radio name=sex value="U" <?=$checked['sex']['U']?>>
							<label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'">유니섹스</label> &nbsp;
					</tr>

<?php 
	if($gongtype=="N") {
?>
					<tr style="display: none;">
						<th><span>특이사항</span></th>
						<td class="td_con1" colspan="3"><input name=addcode value="<?php if ($_data) echo str_replace("\"","&quot;",$_data->addcode); ?>" size=43 maxlength=200 onKeyDown="chkFieldMaxLen(200)" class="input">&nbsp;<span class="font_orange">* 상품의 특이사항을 입력해 주세요.</span></td>
					</tr>
<?php 
	} else { 
?>
					<tr style="display: none;">
						<th><span>공구 판매수량 표시</span></th>
						<td class="td_con1" colspan="3"><input name=addcode value="<?php if ($_data) echo str_replace("\"","&quot;",$_data->addcode); ?>" size=35 maxlength=200 class="input">&nbsp;<span class="font_orange">(예: 한정판매 : 50개, 판매수량 : 100개)</span></td>
					</tr>
<?php 
	} 
?>
					<tr style="display: none;">
						<th><span>위치정보</span></th>
						<td class="td_con1" colspan="3"><input name=position value="<?=$_data->position?>" size=55 maxlength=200  class="input"></td>
					</tr>
<?php 
	if(strlen($_data->productcode)==18) {
?>
					<tr style="display: none;">
						<th><span>태그 관리</span></th>
						<td class="td_con1" colspan="3">
						<DIV id="ProductTagList" name="ProductTagList" style="padding:5px;width:600px;height:68px;word-spacing:7px;background:#fafafa">
							태그를 불러오고 있습니다.
						</DIV>
						</td>
					</tr>

					<script>loadProductTagList('<?=$_data->productcode?>');</script>
<?php 
	}
?>

					<tr style='display:none;'>
						<td class="td_con_orange" colspan="4" style="border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid; border-left:1px solid #b9b9b9;"><b><span class="font_orange">리뷰 상단 배너 이미지 등록</span></b><br> 
						</td>
					</tr>
					<tr style='display:none;'>
						<th><span>리뷰 상단 배너 이미지</span><font color='#0099BF' > 선택</font></th>
<?php
	$urlpath=$Dir.DataDir."shopimages/product_review_banner/";
?>
		            	<td class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="50%" bgcolor="#F9F9F9">
                            <input type=file name="review_banner_img" style="width:100%">
                            <input type=hidden name="old_review_banner_img" id="old_review_banner_img" value="<?=$_data->review_banner_img?>">
                        </td>
	                    <td>
<?					

if ($_data) {
        if (ord($_data->review_banner_img) && file_exists($urlpath.$_data->review_banner_img)){
            echo "<br><img src='".$urlpath.$_data->review_banner_img."' border=1 style=\"width:100px\" id=\"old_review_banner_img_view\">";
            echo "&nbsp;<a href=\"JavaScript:DeleteImg('old_review_banner_img');\" id=\"old_review_banner_img_del\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
        } elseif(ord($_data->review_banner_img) && file_exists($Dir.$_data->review_banner_img)){
            echo "<br><img src='".$Dir.$_data->review_banner_img."' border=1 style=\"width:100px\" id=\"old_review_banner_img_view\">";
            echo "&nbsp;<a href=\"JavaScript:DeleteImg('old_review_banner_img');\" id=\"old_review_banner_img_del\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
        } else {
            echo "<br><img src=images/space01.gif>";
        }
    }
?>

                        </td>
					</tr>


					<tr>
						<td class="td_con_orange" colspan="4" style="border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid; border-left:1px solid #b9b9b9;"><b><span class="font_orange">상품이미지등록</span></b><br>
						<input type=checkbox id="idx_use_imgurl" name=use_imgurl value="Y" <?=($use_imgurl=="Y"?"checked":"")?> onClick="change_filetype(this)"> <label style='cursor:hand;' onMouseOver="style.textDecoration=''" onMouseOut="style.textDecoration='none'" for=idx_use_imgurl><span class="font_orange"><B>상품이미지 첨부 방식을 URL로 입력합니다.</B> (예 : http://www.abc.com/images/abcd.gif)</font></label>
						</td>
					</tr>
					
					<tr>
						<th><span>대 이미지</span><font color='#FF0000' > * 필수</font></th>
						<td class="td_con1" colspan="3" style="position:relative">
						<div class="file_type">
						<input type=file name="userfile" onchange="document.getElementById('size_checker').src=this.value;" style="WIDTH: 400px"><br>
						<span class="font_orange">(권장이미지 : 600X600) * 권장 사이즈 이상의 이미지를 등록해 주세요.</span><br>
						<input type=hidden name="vimage" value="<?=$_data->maximage?>">
						<input type=checkbox id="idx_imgcheck1" name=imgcheck value="Y"<?php if (ord($_data->minimage) || ord($row->tinyimage)) echo "onclick=PrdtAutoImgMsg()"?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_imgcheck1><font color=#003399>대 이미지로 중, 소 이미지 자동생성(중, 소 권장 사이즈로 생성)</font></label>
<?php
	if ($_data) {
		if (ord($_data->maximage) && file_exists($imagepath.$_data->maximage)) {
			echo "<br><img src='".$imagepath.$_data->maximage.'?v='.date('His')."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->maximage}' style=\"width:100px\">";
			echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('1')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} elseif(ord($_data->maximage) && file_exists($Dir.$_data->maximage)){
			echo "<br><img src='".$Dir.$_data->maximage.'?v='.date('His')."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->maximage}' style=\"width:100px\">";
			echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('1')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} elseif(ord($_data->maximage) && strstr($_data->maximage,'http://')){
			echo "<br><img src='".$_data->maximage.'?v='.date('His')."' border=1 alt='URL : ".$_data->maximage."' style=\"width:100px\">";
		} else {
			echo "<br><img src=\"images/space01.gif\">";
		}
	}
?>
						</div>
						<div class="url_type">
						<input type=text name="userfile_url" value="<?=$_data->maximage?>" style="width:100%;display:none" class="input">
<?php
	if ($_data) {
		if (ord($_data->maximage) && !file_exists($imagepath.$_data->maximage) && !file_exists($Dir.$_data->maximage)) {
			echo "<br><img src='{$_data->maximage}' border=1 alt='URL : {$_data->maximage}' style=\"width:100px\">";
			//echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('1')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} else {
			echo "<br><img src=\"images/space01.gif\">";
		}
	}
?>
						</div>
						</td>
					</tr>
					<tr>
						<th><span>중 이미지</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3" style="position:relative">
						<div class="file_type">
						<input type=file name="userfile2" style="WIDTH: 400px" onChange="document.getElementById('size_checker2').src = this.value;" ><br />
						<span class="font_orange">(권장이미지 : 600X600)</span>
						<input type=hidden name="vimage2" value="<?=$_data->minimage?>">
<?php
	if ($_data) {
		if (ord($_data->minimage) && file_exists($imagepath.$_data->minimage)){
			echo "<br><img src='".$imagepath.$_data->minimage.'?v='.date('His')."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->minimage}' style=\"width:100px\">";
			echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} elseif(ord($_data->minimage) && file_exists($Dir.$_data->minimage)){
			echo "<br><img src='".$Dir.$_data->minimage.'?v='.date('His')."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->minimage}' style=\"width:100px\">";
			echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} elseif(ord($_data->minimage) && strstr($_data->minimage,'http://')){
			echo "<br><img src='".$_data->minimage.'?v='.date('His')."' border=1 alt='URL : ".$_data->minimage."' style=\"width:100px\">";			
		} else {
			echo "<br><img src=images/space01.gif>";
		}
	}
?>
						</div>
						<div class="url_type">
						<input type=text name="userfile2_url" value="<?=$_data->minimage?>" style="width:100%; display:none" class="input">
<?php
	if ($_data) {
		if (ord($_data->minimage) && !file_exists($imagepath.$_data->minimage) && !file_exists($Dir.$_data->minimage)) {
			echo "<br><img src='{$_data->minimage}' border=1 alt='URL : {$_data->minimage}' style=\"width:100px\">";
			//echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} else {
			echo "<br><img src=\"images/space01.gif\">";
		}
	}
?>
						</div>
						</td>
					</tr>

					<tr>
						<th><span>소 이미지</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3" style="position:relative;">
						<div class="file_type">
						<input type=file name="userfile3" style="WIDTH: 400px" onChange="document.getElementById('size_checker3').src = this.value;"><br />
						<span class="font_orange">(권장이미지 : 600X600)</span>

						<!-- <input type=hidden name=setcolor id='setcolor' value="<?=$setcolor?>"> -->
						<input type=hidden name="vimage3" value="<?=$_data->tinyimage?>">
<?php
	if ($_data) {
		if (ord($_data->tinyimage) && file_exists($imagepath.$_data->tinyimage)){
			echo "<br><img src='".$imagepath.$_data->tinyimage.'?v='.date('His')."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->tinyimage}' style=\"width:100px\">";
			echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('3')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} elseif(ord($_data->tinyimage) && file_exists($Dir.$_data->tinyimage)){
			echo "<br><img src='".$Dir.$_data->tinyimage.'?v='.date('His')."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->tinyimage}' style=\"width:100px\">";
			echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('3')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} elseif(ord($_data->tinyimage) && strstr($_data->tinyimage,'http://')){
			echo "<br><img src='".$_data->minimage.'?v='.date('His')."' border=1 alt='URL : ".$_data->tinyimage."' style=\"width:100px\">";	
		} else {
			echo "<br><img src=images/space01.gif>";
		}
	}
?>
						<!-- <input type=checkbox name=imgborder value="Y" <?=(($imgborder)=="Y"?"checked":"")?>>신규 상품등록시 외곽 테두리선 생성 &nbsp;  -->
<?php
	if( strlen( $setcolor ) == 0 ) $setcolor = '#666666';
?>
						<!-- <font class=font_orange>테두리 색상 선택</font> : <input type="text" id="itemColor" class='jscolor' name='setcolor' value="<?=$setcolor?>" > -->
						</div>
						<div class="url_type">
						<input type=text name="userfile3_url" value="<?=$_data->tinyimage?>" style="width:100%; display:none" class="input">
<?php
	if ($_data) {
		if (ord($_data->tinyimage) && !file_exists($imagepath.$_data->tinyimage) && !file_exists($Dir.$_data->tinyimage)) {
			echo "<br><img src='{$_data->tinyimage}' border=1 alt='URL : {$_data->tinyimage}' style=\"width:100px\">";
			//echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} else {
			echo "<br><img src=\"images/space01.gif\">";
		}
	}
?>
						</div>
						</td>
					</tr>

					<tr>
						<th><span>PNG 이미지</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3" style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;position:relative">
						<div class="file_type">
						<input type=file name="userfile4" style="WIDTH: 400px" onChange="document.getElementById('size_checker4').src = this.value;" ><br />
						<span class="font_orange">(권장이미지 : 600X600) * png 파일만 등록할 수 있습니다.</span>
						<input type=hidden name="vimage4" value="<?=$_data->over_minimage?>">
<?php
	if ($_data) {
		if (ord($_data->over_minimage) && file_exists($imagepath.$_data->over_minimage)){
			echo "<br><img src='".$imagepath.$_data->over_minimage.'?v='.date('His')."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->over_minimage}' style=\"width:100px\">";
			echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('4')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} elseif(ord($_data->over_minimage) && file_exists($Dir.$_data->over_minimage)){
			echo "<br><img src='".$Dir.$_data->over_minimage.'?v='.date('His')."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->over_minimage}' style=\"width:100px\">";
			echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('4')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} elseif(ord($_data->over_minimage) && strstr($_data->over_minimage,'http://')){
			echo "<br><img src='".$_data->over_minimage.'?v='.date('His')."' border=1 alt='URL : ".$_data->over_minimage."' style=\"width:100px\">";				
		} else {
			echo "<br><img src=images/space01.gif>";
		}
	}
?>
						</div>
						<div class="url_type">
						<input type=text name="userfile4_url" value="<?=$_data->over_minimage?>" style="width:100%;" class="input">
<?php
	if ($_data) {
		if (ord($_data->over_minimage) && !file_exists($imagepath.$_data->over_minimage) && !file_exists($Dir.$_data->over_minimage)) {
			echo "<br><img src='{$_data->over_minimage}' border=1 alt='URL : {$_data->over_minimage}' style=\"width:100px\">";
			//echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
		} else {
			echo "<br><img src=\"images/space01.gif\">";
		}
	}
?>
						</div>
						</td>
					</tr>

					<tr>
						<th style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;position:relative;"><span>기타 이미지</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1" colspan="3" style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;position:relative;">
						<table width="100%">
<?php
    // 기타 이미지 경로 수정..해당 상품코드폴더 밑으로..쇼핑몰 등록시..2016-09-20..일괄등록시는 product/브랜드영문코드명/erp상품코드-쇼핑몰칼라코드/이미지명...tblproduct 이미지정보처럼 tblmultiimages 에도 폴더명/이미지명구조로 변경.
	//$urlpath=$Dir.DataDir."shopimages/multi/";
    $urlpath=$Dir.DataDir."shopimages/product/";
	for($i=1;$i<=10;$i+=2) {
		$gbn1=sprintf("%02d",$i);
		$gbn2=sprintf("%02d",$i+1);
?>
											<tr bgColor=#f0f0f0>
												<td class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left valign=top width="50%" bgcolor="#F9F9F9">
													<div class="file_type">
													<input type=file name=mulimg<?=$gbn1?> style="width:100%">
<?php 
        //exdebug($urlpath.$mulimg_name[$gbn1]);
		if(ord($mulimg_name[$gbn1])) {
			//if (ord($mulimg_name[$gbn1])&&file_exists($urlpath."s".$mulimg_name[$gbn1])){ // 이미지가 있을 경우
            if (ord($mulimg_name[$gbn1])&&file_exists($urlpath.$mulimg_name[$gbn1])){ // 이미지가 있을 경우
				//echo "<br><img src='".$urlpath."s".$mulimg_name[$gbn1]."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$mulimg_name[$gbn1]."' style=\"width:100px\">";
                echo "<br><img src='".$urlpath.$mulimg_name[$gbn1]."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$mulimg_name[$gbn1]."' style=\"width:100px\">";
				echo "&nbsp;<a href=\"javascript:mulimgdel('".$gbn1."')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
			} else if (ord($mulimg_name[$gbn1])&&strstr($mulimg_name[$gbn1],'http://')){ // URL이 있을경우
				echo "<br><img src='".$mulimg_name[$gbn1]."' border=1 alt='URL : ".$mulimg_name[$gbn1]."' style=\"width:100px\">";
			}
		}
?>
													</div>
													<div class="url_type">
													<input type=text name="mulimg<?=$gbn1?>_url" value="<?=$mulimg_name[$gbn1]?>"style="width:100%" class="input">
<?php
        if(ord($mulimg_name[$gbn1])) {
            //if (ord($mulimg_name[$gbn1]) && !file_exists($urlpath."s".$mulimg_name[$gbn1])) {
            if (ord($mulimg_name[$gbn1]) && !file_exists($urlpath.$mulimg_name[$gbn1])) {
                echo "<br><img src='".$mulimg_name[$gbn1]."' border=1 alt='URL : ".$mulimg_name[$gbn1]."' style=\"width:100px\">";
                //echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
            }
        }
?>
													</div>
													<input type=hidden name=oldimg<?=$gbn1?> value="<?=$mulimg_name[$gbn1]?>">
												</td>
												<td class=line style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left valign=top width="50%" bgcolor="#F9F9F9">
													<div class="file_type">
													<input type=file name=mulimg<?=$gbn2?> style="width:100%">
<?php 
		if(ord($mulimg_name[$gbn2])) {
			//if (ord($mulimg_name[$gbn2])&&file_exists($urlpath."s".$mulimg_name[$gbn2])){ // 이미지가 있을 경우
            if (ord($mulimg_name[$gbn2])&&file_exists($urlpath.$mulimg_name[$gbn2])){ // 이미지가 있을 경우
				//echo "<br><img src='".$urlpath."s".$mulimg_name[$gbn2]."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$mulimg_name[$gbn2]."' style=\"width:100px\">";
                echo "<br><img src='".$urlpath.$mulimg_name[$gbn2]."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$mulimg_name[$gbn2]."' style=\"width:100px\">";
				echo "&nbsp;<a href=\"javascript:mulimgdel('".$gbn2."')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
			} else if (ord($mulimg_name[$gbn2])&&strstr($mulimg_name[$gbn2],'http://')){ // URL이 있을경우
				echo "<br><img src='".$mulimg_name[$gbn2]."' border=1 alt='URL : ".$mulimg_name[$gbn2]."' style=\"width:100px\">";
			}
		}
?>
													</div>
													<div class="url_type">
													<input type=text name="mulimg<?=$gbn2?>_url" value="<?=$mulimg_name[$gbn2]?>" style="width:100%" class="input">
<?php
        if(ord($mulimg_name[$gbn2])) {
            //if (ord($mulimg_name[$gbn2]) && !file_exists($urlpath."s".$mulimg_name[$gbn2])) {
            if (ord($mulimg_name[$gbn2]) && !file_exists($urlpath.$mulimg_name[$gbn2])) {
                echo "<br><img src='".$mulimg_name[$gbn2]."' border=1 alt='URL : ".$mulimg_name[$gbn2]."' style=\"width:100px\">";
                //echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
            }
        }
?>
													</div>
													<input type=hidden name=oldimg<?=$gbn2?> value="<?=$mulimg_name[$gbn2]?>">
												</td>
											</tr>
<?php
	}
?>
						</table>
						<script>change_filetype(document.form1.use_imgurl);</script>
						</td>
					</tr>
					<tr>
						<td class="td_con_orange" colspan="4" style="border-left:1px solid #b9b9b9;">
		                <div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%">
						<col width=160></col>
						<col width=></col>
						<tr>
							<td><B><span class="font_orange">상품 상세내역 입력</span></B></td>
							<td>&nbsp;</td>
						</tr>
						</table>
		                </div>
						</td>
					</tr>
					<tr>
						<th><span>상품 상세내역(MOBILE)</span></th>
						<td colspan="3">
							<textarea wrap=off  id="ir2" style="WIDTH: 100%; HEIGHT: 300px" name=content_m><?=htmlspecialchars($_data->content_m)?></textarea>
						</td>
					</tr>
					<tr>
						<th><span>상품 상세내역(PC)</span></th>
						<td colspan="3">
							<textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name=content><?=htmlspecialchars($_data->content)?></textarea>
						</td>
					</tr>

					<tr>
						<td class="td_con_orange" colspan="4" style="border-left:1px solid #b9b9b9;border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid;position:relative;">
		                <div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%">
						<col width=160></col>
						<col width=></col>
						<tr>
							<td><B><span class="font_orange">상품 사이즈 조견표 입력</span></B></td>
							<td>&nbsp;</td>
						</tr>
						</table>
		                </div>
						</td>
					</tr>
					<tr class="tbody_product_size">
						<th><span>상품 사이즈 조견표(MOBILE)</span></th>
						<td colspan="3">
							<textarea wrap=off  id="ir5" style="WIDTH: 100%; HEIGHT: 300px" name=pr_sizecon_m><?=htmlspecialchars($_data->pr_sizecon_m)?></textarea>
						</td>
					</tr>
					<tr class="tbody_product_size">
						<th><span>상품 사이즈 조견표(PC)</span></th>
						<td colspan="3">
							<textarea wrap=off  id="ir4" style="WIDTH: 100%; HEIGHT: 300px" name=pr_sizecon><?=htmlspecialchars($_data->pr_sizecon)?></textarea>
						</td>
					</tr>

					<tr>
						<td class="td_con_orange" colspan="4" style="border-left:1px solid #b9b9b9;border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid;position:relative;">
		                <div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%">
						<col width=160></col>
						<col width=></col>
						<tr>
							<td><B><span class="font_orange">상품 Detail 간략정보</span></B></td>
							<td>&nbsp;</td>
						</tr>
						</table>
		                </div>
						</td>
					</tr>
					<tr class="tbody_product_detail">
						<th><span>상품 Detail 간략정보(공통)</span></th>
						<td colspan="3">
							<textarea wrap=off  id="ir6" style="WIDTH: 100%; HEIGHT: 300px" name=pr_content><?=htmlspecialchars($_data->pr_content)?></textarea>
						</td>
					</tr>

					<tr  style='display:none;'>
						<td class="td_con_orange" colspan="4" style="border-left:1px solid #b9b9b9;border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid;position:relative;">
						<div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%">
						<col width=160></col>
						<col width=></col>
						<tr>
							<td><B><span class="font_orange">상품 공지사항 입력</span></B></td>
							<td>&nbsp;</td>
						</tr>
						</table>
						</div>
						</td>
					</tr>
					<tr  style='display:none;'>
						<th><span>상품 공지사항</span></th>
						<td colspan="3">
							<textarea wrap=off  id="ir3" style="WIDTH: 100%; HEIGHT: 300px" name=pr_notice><?=htmlspecialchars($_data->pr_notice)?></textarea>
						</td>
					</tr>
					</table>
					<img id="size_checker" style="display:none;"><img id="size_checker2" style="display:none;"><img id="size_checker3" style="display:none;"><img id="size_checker4" style="display:none;">
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<table cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td colspan="2"><input type=checkbox id="idx_insertdate20" name=insertdate2 value="Y" onClick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_insertdate20><span class="font_orange">상품등록날짜 고정</span></label></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<!-- 페이지 타이틀 -->
						<div class="title_depth3">추가정보 등록/수정</div>
					</td>
				</tr>
				<tr>
					<td height=3></td>
				</tr>
				<tr>
					<td>
		            <div class="table_style01 optionStyle" >
					<table cellSpacing=0 cellPadding=0 width="100%" border=0>
					
					<!-- 수정된 옵션 -->
					<tr>
						<th scope="row"><span>옵션사용</span><font color='#0099BF' > 선택</font></th>
						<td colspan="2">
							<? if( trim( $_data->option_type ) == '' ) $_data->option_type = '2'; // 엑셀 업로드에 없는것 확인  ?>
							<input type='radio' name='opt_select' id='opt_select1' value ='1' <?if( $_data->option_type == '0' || $_data->option_type == '1' ){ echo 'CHECKED'; }?> > 사용함
							<input type='radio' name='opt_select' id='opt_select2' value ='0' <?if( $_data->option_type == '2' || $_data->option_type == '' ){ echo 'CHECKED'; }?> > 사용안함
						</td>
					</tr>
					
					<tr id='ID_p_goods_code_area' >
						<th scope="row"><span>자체품목코드</span></th>
						<td colspan="2">
							<input name="p_goods_code" value="<?=$_data->self_goods_code?>" size="50" maxlength="50" class="input" style="width:60%">
						</td>
					</tr>
					
					<tr id='ID_option_type_area' >
						<th scope="row"><span>옵션구성방식</span></th>
						<td colspan="2">
							<input type='radio' name='opt_type' id='opt_type1' value ='0' <?if( strlen( $_data->option1 ) > 0 && $_data->option_type == '0' ){ echo 'CHECKED'; }?> > 조합형
							<input type='radio' name='opt_type' id='opt_type2' value ='1' <?if( strlen( $_data->option1 ) > 0 && $_data->option_type == '1' ){ echo 'CHECKED'; }?> > 독립형
						</td>
					</tr>
					<!-- //수정된 옵션 -->

<!-- 옵션 수정 START -->
					<tr id='ID_opt1_area' >
						<th scope="row"><span>조합형 옵션</span></th>
						<td colspan="2">
							
								<span class="frm_info">
									옵션항목은 '@#' 로 구분하여 여러개를 입력할 수 있습니다. 옷을 예로 들어 [옵션1 : 사이즈 , 옵션1 항목 : XXL@#XL@#L@#M@#S] , [옵션2 : 색상 , 옵션2 항목 : 빨@#파@#노]<br>
									<strong>옵션명과 옵션항목에 '@#'을 연결되어 입력할 수 없습니다.</strong>
								</span>
								<div class="sit_option tbl_frm01" >
								<table border = '0' width='98%'>
									<caption>상품선택옵션 입력</caption>
									<colgroup>
										<col class="grid_4">
										<col>
									</colgroup>
									<tbody id='opt_subject_body' >
<?php
	
	if( strlen( $_data->option1 ) > 0 && $_data->option_type == '0' ){
		$tmp_option_subject = explode( '@#', $_data->option1 );
		$tmp_option_cnt = count( $tmp_option_subject );
		foreach( $tmp_option_subject as $optSubjectKey=>$optSubjectVal ){
			//옵션의 항목을 가져온다
			$opt_content = get_option_code( $_data->productcode,0, $optSubjectKey );
			$opt_str = '';
			foreach( $opt_content as $optConKey=>$optConVal ){
				$opt_str .= $optConKey.'@#';
			}
			if( strlen( $opt_str ) > 0 ) $opt_str = substr( $opt_str, 0, -2 );
?>
									<tr>
										<th scope="row">
											<label for="opt_subject">옵션</label>
											<input type="text" name="opt_subject[]" value="<?=$optSubjectVal?>" class="frm_input" size="10" maxlength="6" onChange="javascript:option_create_reset();">
										</th>
										<td>
											<label for="opt"><b>항목</b></label>
											<input type="text" name="opt_content[]" value="<?=$opt_str?>" class="frm_input" size="50" onChange="javascript:option_create_reset();">
<?php
			if( $optSubjectKey > 0 ){
?>
											<button type="button" class="btn_frmline" name="btn_opt_subject_del" >삭제</button>
<?php
			} // optSubjectKey if
?>

										</td>
									</tr>
<?php
		} // option_subject foreach
	} else { // option1 if
?>
									<tr>
										<th scope="row">
											<label for="opt1_subject">옵션</label>
											<input type="text" name="opt_subject[]" value="" class="frm_input" size="10" maxlength="6" onChange="javascript:option_create_reset();">
										</th>
										<td>
											<label for="opt"><b>항목</b></label>
											<input type="text" name="opt_content[]" value="" class="frm_input" size="50" onChange="javascript:option_create_reset();">
										</td>
									</tr>
<?php
	} // option1 else
?>
									</tbody>
								</table>
								<div class="btn_confirm02 btn_confirm">
									<button type="button" class="btn_frmline" name="btn_opt_subject_add" >추가</button>
									<button type="button" id="option_table_create" class="btn_frmline">옵션목록생성</button>
								</div>
							</div>
							<div id="sit_option_frm">
<?php
	# 옵션이 존재할 경우
	//$optionSql = "SELECT option_num, option_code, option_price, option_quantity, option_quantity_noti, option_use, self_goods_code FROM tblproduct_option WHERE productcode='".$prcode."' AND option_type = 0 ORDER BY option_code ASC ";
    $optionSql = "SELECT option_num, option_code, option_price, option_quantity, option_quantity_noti, option_use, self_goods_code FROM tblproduct_option WHERE productcode='".$prcode."' AND option_type = 0 ORDER BY option_num ASC ";
	$optionRes = pmysql_query( $optionSql, get_db_conn() );
	$optionCnt = pmysql_num_rows( $optionRes );
	if( $optionCnt > 0 ) {
?>
								<div class="sit_option_frm_wrapper">
									<table>
									<caption>옵션 목록</caption>
									<thead>
									<tr>
										<th scope="col">
											<label for="opt_chk_all" class="sound_only">전체 옵션</label>
											<input type="checkbox" name="opt_chk_all" value="1" id="opt_chk_all">
										</th>
										<th scope="col">옵션</th>
										<th scope="col">추가금액</th>
										<th scope="col">재고수량</th>
										<th scope="col">통보수량</th>
										<th scope="col">자체품목코드</th>
										<th scope="col">사용여부</th>
									</tr>
									</thead>
									<tbody>
<?	
	$optionCnt = 0;
	while( $optionRow = pmysql_fetch_array( $optionRes ) ){ 
		$opt_num = $optionRow['option_num'];
		$opt_id = $optionRow['option_code'];
		$opt_val = explode(chr(30), $opt_id);
		$opt_price = $optionRow['option_price'];
		$opt_stock_qty = $optionRow['option_quantity'];
		$opt_noti_qty = $optionRow['option_quantity_noti'];
		$opt_use = $optionRow['option_use'];
		$opt_self_code = $optionRow['self_goods_code'];
?>
										<tr>
											<td class="td_chk">
												<input type="hidden" name="opt_num[]" value="<?=$opt_num?>">
												<input type="hidden" name="opt_id[]" value="<?php echo $opt_id; ?>">
												<label for="opt_chk_<?php echo $optionCnt; ?>" class="sound_only"></label>
												<input type="checkbox" name="opt_chk[]" id="opt_chk_<?php echo $optionCnt; ?>" value="1">
											</td>
											<td class="opt-cell">
<?php
		foreach( $opt_val as $optKey=>$optVal){
			echo $optVal;
			if( count( $opt_val ) > $optKey + 1 ) echo ' <small>&gt;</small> ';
		}
?>
											</td>
											<td class="td_numsmall">
												<label for="opt_price_<?php echo $optionCnt; ?>" class="sound_only"></label>
												<input type="text" name="opt_price[]" value="<?php echo $opt_price; ?>" id="opt_price_<?php echo $optionCnt; ?>" class="frm_input" size="9">
											</td>
											<td class="td_num">
												<label for="opt_stock_qty_<?php echo $optionCnt; ?>" class="sound_only"></label>
												<input type="text" name="opt_stock_qty[]" value="<?php echo $opt_stock_qty; ?>" id="op_stock_qty_<?php echo $optionCnt; ?>" class="frm_input" size="5">
											</td>
										   <td class="td_num">
												<label for="opt_noti_qty_<?php echo $optionCnt; ?>" class="sound_only"></label>
												<input type="text" name="opt_noti_qty[]" value="<?php echo $opt_noti_qty; ?>" id="opt_noti_qty_<?php echo $optionCnt; ?>" class="frm_input" size="5">
											</td>
											<td class="td_num">
												<label for="opt_goods_code_<?php echo $optionCnt; ?>" class="sound_only"></label>
												<input type="text" name="opt_goods_code[]" value="<?=$opt_self_code?>" id="opt_goods_code_<?php echo $optionCnt; ?>" class="frm_input" size="50">
											</td>
											<td class="td_mng">
												<label for="opt_use_<?php echo $optionCnt; ?>" class="sound_only"></label>
												<select name="opt_use[]" id="opt_use_<?php echo $optionCnt; ?>">
													<option value="1" <?php echo get_selected('1', $opt_use); ?>>사용함</option>
													<option value="0" <?php echo get_selected('0', $opt_use); ?>>사용안함</option>
												</select>
											</td>
										</tr>
<?	
		$optionCnt++;
	} 
?>
									</tbody>
									</table>
								</div>

								<div class="btn_list01 btn_list">
									<input type="button" value="선택삭제" id="sel_option_delete">
								</div>

								<fieldset>
									<legend>옵션 일괄 적용</legend>
									<?php echo help('전체 옵션의 추가금액, 재고/통보수량 및 사용여부를 일괄 적용할 수 있습니다. 단, 체크된 수정항목만 일괄 적용됩니다.'); ?>
									<label for="opt_com_price">추가금액</label>
									<label for="opt_com_price_chk" class="sound_only">추가금액일괄수정</label><input type="checkbox" name="opt_com_price_chk" value="1" id="opt_com_price_chk" class="opt_com_chk">
									<input type="text" name="opt_com_price" value="0" id="opt_com_price" class="frm_input" size="5">
									<label for="opt_com_stock">재고수량</label>
									<label for="opt_com_stock_chk" class="sound_only">재고수량일괄수정</label><input type="checkbox" name="opt_com_stock_chk" value="1" id="opt_com_stock_chk" class="opt_com_chk">
									<input type="text" name="opt_com_stock" value="0" id="opt_com_stock" class="frm_input" size="5">
									<label for="opt_com_noti">통보수량</label>
									<label for="opt_com_noti_chk" class="sound_only">통보수량일괄수정</label><input type="checkbox" name="opt_com_noti_chk" value="1" id="opt_com_noti_chk" class="opt_com_chk">
									<input type="text" name="opt_com_noti" value="0" id="opt_com_noti" class="frm_input" size="5">
									<label for="opt_com_use">사용여부</label>
									<label for="opt_com_use_chk" class="sound_only">사용여부일괄수정</label><input type="checkbox" name="opt_com_use_chk" value="1" id="opt_com_use_chk" class="opt_com_chk">
									<select name="opt_com_use" id="opt_com_use">
										<option value="1">사용함</option>
										<option value="0">사용안함</option>
									</select>
									<button type="button" id="opt_value_apply" class="btn_frmline">일괄적용</button>
								</fieldset>
<?	} ?>								
							</div>
						</td>
					</tr>
					
					<!-- 옵션 수정 END -->
					<!-- 추가옵션 2015 10 28 유동혁 -->
					<tr id='ID_supply_area'>
						<th scope="row"><span>독립형 옵션</span></th>
						<td colspan="2">
							<div id="sit_supply_frm" class="sit_option tbl_frm01">
								<?php echo help('옵션항목은 "@#" 로 구분하여 여러개를 입력할 수 있습니다. 스마트폰을 예로 들어 [추가1 : 추가구성상품 , 추가1 항목 : 액정보호필름@#케이스@#충전기]<br><strong>옵션명과 옵션항목에 "@#" 는 입력할 수 없습니다.</strong>'); ?>
								<table>
								<caption>상품추가옵션 입력</caption>
								<colgroup>
									<col class="grid_4">
									<col>
								</colgroup>
								<tbody>
								<?php
								$i = 0;
								do {
									$seq = $i + 1;
								?>
								<tr>
									<th scope="row">
										<label for="spl_subject_<?php echo $seq; ?>">옵션</label>
										<input type="text" name="spl_subject[]" id="spl_subject_<?php echo $seq; ?>" value="<?php echo $spl_subject[$i]; ?>" class="frm_input" size="10" maxlength="6" onChange="javascript:option_create_reset();">
									</th>
									<td>
										<label for="spl_item_<?php echo $seq; ?>"><b>옵션 항목</b></label>
										<input type="text" name="spl[]" id="spl_item_<?php echo $seq; ?>" value="" class="frm_input" size="40" onChange="javascript:option_create_reset();">
										<input type='checkbox' name='necessary_tf[]' value='T' > *필수옵션으로 사용
										<?php
										if($i > 0)
											echo '<button type="button" id="del_supply_row" class="btn_frmline">삭제</button>';
										?>
									</td>
								</tr>
								<?php
									$i++;
								} while($i < $spl_count);
								?>
								</tbody>
								</table>
								<div id="sit_option_addfrm_btn"><button type="button" id="add_supply_row" class="btn_frmline">옵션추가</button></div>
								<div class="btn_confirm02 btn_confirm">
									<button type="button" id="supply_table_create">옵션목록생성</button>
								</div>
							</div>
							
							<div id="sit_option_addfrm"><?php include_once($Dir.'admin/ajax_productoption_plus.php'); ?></div>

						</td>
					</tr>
					<!-- //추가옵션 -->
					<!-- text 옵션 -->
					<tr>
						<th><span>추가옵션</span><font color='#0099BF' > 선택</font></th>
						<td>
							<div>
								<input type='checkbox' name='addopt_select' value ='1' <? if( strlen( $_data->option2 ) > 0 ){ echo 'checked'; } ?> > 사용 <br>
							</div>
							<div id='ID_addopt_content' >
								<ul id='ID_add_content'>
<?php
	if( strlen( $_data->option2 ) > 0 ){
		$tmpAddOpt = explode( '@#', $_data->option2 );
		$tmpAddOpt_tf = explode( '@#', $_data->option2_tf );
		$tmpAddOpt_maxlen = explode( '@#', $_data->option2_maxlen );
		foreach( $tmpAddOpt as $addoptKey=>$addoptVal ){
?>
									<li>
										추가옵션명 <input type='text' name='addopt_subject[<?=$addoptKey?>]' value='<?=$addoptVal?>' size='10' maxlength='5'>
										<input type='radio' name='addopt_type[<?=$addoptKey?>]' value = 'T' <?if( $tmpAddOpt_tf[$addoptKey] == 'T' ){echo'checked';}?> > 필수항목
										<input type='radio' name='addopt_type[<?=$addoptKey?>]' value = 'F' <?if( $tmpAddOpt_tf[$addoptKey] == 'F' ){echo'checked';}?> > 선택항목
										<select name="addopt_maxln[<?=$addoptKey?>]" >
<?php
			for( $i=1; $i<=50; $i++){
?>
											<option value='<?=$i?>' <?if( $tmpAddOpt_maxlen[$addoptKey] == $i ){echo'selected';}?> > <?=$i?>자</option>
<?php
			} // for
?>
								<!-- <option value='100>' > 100자</option>
								<option value='200' > 200자</option> -->
										</select>
										<button type='button' name='addOptDel' >삭제</button>
									</li>
<?php
		} //addopt foreach
	} else {
?>
							
								
									<li>
										추가옵션명 <input type='text' name='addopt_subject[0]' value='' size='10' maxlength='5'>
										<input type='radio' name='addopt_type[0]' value = 'T' checked > 필수항목
										<input type='radio' name='addopt_type[0]' value = 'F' > 선택항목
										<select name="addopt_maxln[0]" >
<?php
		for( $i=1; $i<=50; $i++){
?>
											<option value='<?=$i?>' <? if( $i == 10 ){ echo 'selected'; } ?> > <?=$i?>자</option>
<?php
		} //for
?>
								<!-- <option value='100>' > 100자</option>
								<option value='200' > 200자</option> -->
										</select>
										<button type='button' name='addOptDel' >삭제</button>
									</li>
								
								
<?php
	}// option2 else
?>
								</ul>
								<div class="btn_confirm02 btn_confirm">
										<button type="button" class="btn_frmline" name="btn_addopt_add" >추가</button>
								</div>
							</div>
							
						</td>
					</tr>
					<!-- // text 옵션 -->
					</table>

		            </div>
					


		            <div class="table_style01">
					<table cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<th><span>아이콘 꾸미기</span><font color='#0099BF' > 선택</font></th>
						<td class="td_con1">
		                <div class="table_none">
						<table cellSpacing=0 cellPadding=0 width="100%" border=0>
<?php
	//$iconarray = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28");
	$iconarray = array("01","02","03","04","05","06","07","08","09","10");
	$totaliconnum = 0;
	for($i=0;$i<count($iconarray);$i++) {
		if($i%7==0) echo "<tr height=25>";
		echo "<td width=\"14%\"><input type=checkbox name=icon onclick=CheckChoiceIcon('{$totaliconnum}') value=\"{$iconarray[$i]}\" ";
		if($iconvalue2[$iconarray[$i]]=="Y") echo "checked";
		echo "><img src=\"{$Dir}images/common/icon{$iconarray[$i]}.gif\" border=0 align=absmiddle></td>\n";
		if($i%7==6) echo "</tr>";
		$totaliconnum++;
	}
?>
						<tr>
							<td colSpan=7 height=5></td>
						</tr>
						<tr>
							<td colSpan=7>
							<table cellpadding="1" cellspacing="1" width="100%" bgcolor="#FF9933">
							<tr>
								<td width="585" bgcolor="#FFFCF6">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="167" align=center style="padding-top:5pt; padding-bottom:5pt;"><b><span class="font_orange">내 아이콘</span></b></td>
									<td width="424" style="padding-top:5pt; padding-bottom:5pt;">
									<table cellSpacing=0 cellPadding=0 width="100%" border=0>
<?php
	$iconpath=$Dir.DataDir."shopimages/etc/";
	$usericon = array("U1","U2","U3","U4","U5","U6");
	$cnt=0;
	for($i=0;$i<count($usericon);$i++){
		if(file_exists($iconpath."icon{$usericon[$i]}.gif")){
			$cnt++;
			if($cnt%3==1) echo "<tr height=25>";
			echo "<td width=33%><input type=checkbox name=icon onclick=CheckChoiceIcon('{$totaliconnum}') value=\"{$usericon[$i]}\" ";
			if($iconvalue2[$usericon[$i]]=="Y") echo "checked";
			echo "> <img src=\"{$iconpath}icon{$usericon[$i]}.gif\" border=0 align=absmiddle></td>\n";
			if($cnt%3==0) echo "</tr>";
			$totaliconnum++;
		}
	}
	if($cnt==0) {
		echo "<tr><td align=center><font color=red>등록된 내 아이콘이 없습니다.</font></td></tr>";
	} else {
		for($i=$cnt;$i<6;$i++){
			echo "<td width=33%>&nbsp;</td>";
			if($i%3==2) echo "</tr><tr>";
		}
		if($cnt<6) echo "</tr>";
	}
?>
									</table>
									</td>
								</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#FF9933" style="padding-left:5pt;"><font color="white"><span style="letter-spacing:-0.5;">* 한 상품에 3개까지 아이콘을 사용할 수 있습니다.<br>* <b>아이콘 등록은 6개 까지 등록</b> 가능합니다.</span></font></td>
							</tr>
							<tr>
								<td bgcolor="#FF9933"><A href="JavaScript:IconMy()"><IMG src="images/productregister_iconinsert.gif" align=absMiddle border=0 width="120" height="20"></A><!--&nbsp;<A href="JavaScript:IconList()"><IMG src="images/productregister_icondown.gif" align=absMiddle border=0 width="98" height="20"></A>--></td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
		                </div>
						</td>
					</tr>

					</table>
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<table cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td colspan="2"><input type=checkbox id="idx_insertdate30" name=insertdate3 value="Y" onClick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_insertdate30><span class="font_orange">상품등록날짜 고정</span></label></td>
					</tr>

					</table>
		            </td>
				</tr>
				<tr><td height=10></td></tr>

				</table>

				</td>
			</tr>
			</table>

			<!--사이즈조견표..개발중..-->
			<style>
			.style_none {all: none;}
			.style_none table th{all: none;}
			.style_none table td{all: none;}
			</style>
			<tr style='display:none;'>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tbody id="tbody_chk_prsize">
						<tr>
							<td>
								<div class="title_depth3">사이즈 조견표</div>		
							</td>
						</tr>
						<tr>
							<td>
							<div class="table_style01">
								<table cellspacing="0" cellpadding="0" width="100%" border="0">
									<tbody>
									<tr id="tbody_product_size" <?if(!$product_size || $product_size['use']['chk']=='N') echo "style='display:none;' ";?>>
										<td>
											<table cellspacing="0" cellpadding="0" width="100%" border="0" class="style_none">
												<tbody >
													<tr>
														<td>
														<input type=button value='리스트추가' id='add-row'>
														<input type=button value='항목추가' id='add-col'>
														<input type=button value='리스트삭제' id='del-row'>
														<input type=button value='항목삭제' id='del-col'>
														</td>
													</tr>
													<tr>
														<td>
															<div id="">
																<table border=0>
																	<thead id='stock_thead'>
																		<tr>
																			<td>
																				<!-- <input type=hidden name=it_opty_subj size=10 class=ed value='<?=$it[it_opty_subj]?>'> 
																				<input type=hidden name=it_optx_subj size=4 class=ed value='<?=$it[it_optx_subj]?>'> -->
																				<input type=text readonly value="사이즈/항목" size="10" style="text-align: center">
																			</td>
																		<?if($product_size){?>
																			<?foreach($product_size['size_y'] as $y_index=>$yval){?>
																			 <td>
																				<input type=text name='sizey_subj[]' size=10 class=ed value="<?=$yval['text']?>">
																			</td>
																			<?}?>
																		<?}else{?>
																			<td>
																				<input type=text name='sizey_subj[0]' size=10 class=ed>
																			</td>
																		<?}?>
																		</tr>
																	</thead>
																	<tbody id='stock_tbody'>
																	<?if($product_size){//사이즈 조견표가 있을경우?>
																		<?foreach($product_size['size_x'] as $x_index=>$xval){?>
																		<tr>
																			<td>
																				<input type=text name='sizex_subj[]' size=10 class=ed value="<?=$xval['text']?>">
																			</td>
																			<?for($i=0; $i < count($product_size['size_y']); $i++){?>
																			<td>
																				<input type=text name='size_content[<?=$x_index?>][]' size=10 class=ed value="<?=$product_size['content'][$x_index][$i]['text']?>">
																			</td>
																			<?}?>
																		</tr>
																		<?}?>
																	<?}else{//사이즈 조견표가 없을경우 기본 페이지?>
																		<tr>
																			<td>
																				<input type=text name='sizex_subj[]' size=10 class=ed>
																			</td>
																			<td>
																				<input type=text name='size_content[0][]' size=10 class=ed>
																			</td>
																		</tr>
																	<?}?>
																	</tbody>
																</table>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
							</td>
						</tr>
					</tbody>
					</table>
				</td>
			</tr>
			<tr style="height:20px;">
			</tr>
			<!--//사이즈 조견표-->

			<!-- 정보고시 페이지 수정 2016 01 18 유동혁 -->
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td>
								<div class="title_depth3">정보 고시 등록/수정</div>		
								<input type="hidden" name="sabangnet_prop_val" value="<?=$_data->sabangnet_prop_val?>">
								<input type="hidden" name="sabangnet_prop_option" value="<?=$data->sabangnet_prop_option?>">
								<input type="hidden" name="prop_type" value="001" />
							</td>
						</tr>
						<tr>
							<td>
								<div class="table_style01" id="prop_type001">
									<table cellSpacing=0 cellPadding=0 width="100%" border=0>
										<tr>
											<th>
												<span>상품의 상품군</span> <font color='#0099BF' > 선택</font>
											</th>
											<td>
												<select id='jungbo_option' name='jungbo_option' >
<?php
	echo "<option value='025' selected >INFO</option>";
	/* deco는 info만 볼수엤게 한다
	foreach( $jungbo_code as $codeKey=>$codeVal ){
		$jungbo_selected = '';
		if( $codeKey == $sabangnet_prop_option[0] ) $jungbo_selected = 'selected';
		echo "<option value='".$codeKey."' ".$jungbo_selected." >".$codeVal['title']."</option>";
	}
	*/
?>
												</select>
												<input type='checkbox' id='jungbo_allchk' value='' > <span class='font_orange'>* 모든 상품정보 '상세정보 별도표기' 선택</span>
											</td>
										</tr>
									</table>
								</div>
								<div class="table_style01" id="prop_type001">
									<table cellSpacing=0 cellPadding=0 width="100%" border=0 id='jungbo_options'>
<?php
		//$incode = $jungbo_code[$sabangnet_prop_option[0]];
		# info 만 사용하도록 변경 2016-03-07
		$incode = $jungbo_code["025"];
		$optionKey = 1;
		if( $incode ){
			foreach( $incode['option'] as $inKey=>$inVal ){

?>
										<tr>
											<th>
												<span><?=$inVal?></span>
												<input type='hidden' name='jungbo_prop_option' id='' value='<?=$inVal?>' >
											</th>
											<td>
												<input type='text' name='jungbo_prop_val' id='' value='<?=$sabangnet_prop_val[$optionKey]?>' style="width:450px;" >
												<input type='checkbox' name='option_chk' > 상세정보 별도표기
												<br><span class='font_blue' ><?=$incode['comment'][$inKey]?></span>
											</td>
										</tr>
<?php
				$optionKey++;
			}
		}
?>
									</table>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- //정보고시 페이지 수정 2016 01 18 유동혁 -->


<!-- 2016-06-20 -->
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">

					<tr>
						<td align="center" width="100%">
<?php 
	if (ord($prcode)==0) { 
?>
								<a href="javascript:CheckForm('insert');"><img src="images/btn_new.gif" align=absmiddle border="0" vspace="5"></a>
<?php 
	} else {
?>
								<a href="javascript:CheckForm('modify');"><B><img src="images/btn_infoedit.gif" align=absmiddle border="0" vspace="5"></B></a>
								&nbsp;
								<a href="javascript:PrdtDelete();"><B><img src="images/btn_infodelete.gif" align=absmiddle border="0" vspace="5"></B></a>
                                &nbsp;
                                <a href="http://<?=$shopurl?>/front/productdetail.php?productcode=<?=$_data->productcode?>" target=_blank><img src="images/btn_preview.gif" align=absmiddle border=0 vspace="5"></a>
<?php 
	}
?>
								&nbsp;
								<a href="javascript:goBackList();"><B><img src="img/btn/btn_list.gif" align=absmiddle border="0" vspace="5"></B></a>
							</td>
							<td align="right">
<?php 
	if (ord($prcode)) { 
?>
								<a href="JavaScript:NewPrdtInsert()"  onMouseOver="window.status='신규입력';return true;"><img src="images/product_newregicn.gif" align=absmiddle border="0" vspace="5"></a>
<?php 
	} 
?>
						</td>
					</tr>

					</table>
                </td>
            </tr>

<!-- 2016-06-20 -->


		</td>
	</tr>
	<tr><td height="20"></td></tr>
	<tr>
			<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>메뉴얼</p></div>
					<ul class="help_list">
						<li>가격은 각각의 <b>구성상품의 가격을 합산하여 노출됩니다.</b> 세트 상품의 가격을 변경하시고 싶으실 경우 각각의 구성상품의 가격을 수정하셔야 합니다.</li>
						<li>포인트 적립금은 각각의 <b>구성상품에 설정된 적립금(율)이 합산되어 지급됩니다.</b> 세트 상품에 적립금 지급을 원하지 않을 경우 각각의 구성상품의 적립금(율)을 0원으로 수정하셔야 합니다. </li>
					</ul>
				</div>
			</td>
		</tr>
		<tr><td height=20></td></tr>
			<tr><td height="50"></td></tr>
	<tr><td height=20 colspan=2></td></tr>


	</table>
	<input type=hidden name=iconnum value='<?=$totaliconnum?>'>
	<input type=hidden name=iconvalue>
	<input type=hidden name=optnum1 value=<?=$optnum1?>>
	<input type=hidden name=optnum2 value=<?=$optnum2?>>
	<input type=hidden name=serial_value value=<?=$serial_value?>>
	<input type=hidden name=action_page value=<?=$action_page?>>
	<input type=hidden name=chk_detail value='1'>
</form>
	</td>
</tr>

</table>

</td>
</tr>

</table>
</td>
</tr>
</table>

<!-- $action_page == 제휴몰 리스트와 자사몰 리스트의 주소가 다르기때문에 리스트에서 주소값을 받아옴 -->
<form name=form_register action="<?=$action_page?>" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
<input type=hidden name=category_data value="<?=$category_data?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=keyword value="<?=$keyword?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=display value="<?=$display?>">
<input type=hidden name=naver_display value="<?=$naver_display?>">
<input type=hidden name=erp_price_yn value="<?=$erp_price_yn?>">
<input type=hidden name=vip value="<?=$vip?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=sellprice_min value="<?=$sellprice_min?>">
<input type=hidden name=sellprice_max value="<?=$sellprice_max?>">
<input type=hidden name=code_type value="<?=$code_type?>">
<input type=hidden name=code_area value="<?=$code_area?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=serial_value value=<?=$serial_value?>>
<input type=hidden name=action_page value=<?=$action_page?>>
<input type=hidden name=chk_detail value='1'>
</form>

<form name=cForm action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=popup value="<?=$popup?>">
<input type=hidden name=code value=<?=$code?>>
<input type=hidden name=prcode value=<?=$prcode?>>
<input type=hidden name=delprdtimg>
<input type=hidden name="vimage" value="<?php if ($_data) echo $_data->maximage; ?>">
<input type=hidden name="vimage2" value="<?php if ($_data) echo $_data->minimage; ?>">
<input type=hidden name="vimage3" value="<?php if ($_data) echo $_data->tinyimage; ?>">
<input type=hidden name="vimage4" value="<?php if ($_data) echo $_data->over_minimage; ?>">
</form>
<form name=icon action="product_iconmy.php" method=post target=icon>
</form>
<form name=iconlist action="product_iconlist.php" method=post target=iconlist>
</form>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>

<?=$onload?>

<script type="text/javascript">
var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

var oEditors_m = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors_m,
	elPlaceHolder: "ir2",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

var oEditors_pr_notice = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors_pr_notice,
	elPlaceHolder: "ir3",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

var oEditors_pr_sizecon = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors_pr_sizecon,
	elPlaceHolder: "ir4",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

var oEditors_pr_sizecon_m = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors_pr_sizecon_m,
	elPlaceHolder: "ir5",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

var oEditors_pr_content = [];
nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors_pr_content,
	elPlaceHolder: "ir6",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});
</script>
<!-- 옵션 스크립트 추가 2015 10 23 유동혁 -->
<script>
$(function() {

	$(document).ready(function(){
		$('input[name="opt_select"]:checked').trigger('click');

		$('input[name="addopt_select"]').prop( 'checked', function( i, v ){
			if( v ) $('#ID_addopt_content').show();
			else $('#ID_addopt_content').hide();
		});

	});

	//리스트 롤오버 이미지 파일 체크
	$('input[name="userfile4"]').change( function(){
		 if( $("input[name=userfile4]").val() != "" ){
		     var ext = $("input[name=userfile4]").val().split(".").pop().toLowerCase();
		     if($.inArray(ext, ['png']) == -1) {
		        alert('png 파일만 등록할 수 있습니다.');
		        $("input[name=userfile4]").val("");
		        return;
		      }
		 }
	});

	//추가옵션 사용 click
	$('input[name="addopt_select"]').click( function(){
		if( $(this).prop( 'checked' ) ){
			$('#ID_addopt_content').show();
		} else {
			$('#ID_addopt_content').hide();
		}
	});


	$('button[name="btn_addopt_add"]').click( function(){
		//$('#ID_add_content > li');
		var idx = $('#ID_add_content > li').length;
		var addoptHtml = "";

		addoptHtml += "<li>";
		addoptHtml += "	추가옵션명 <input type='text' name='addopt_subject[" + idx + "]' value='' size='10' maxlength='5'>";
		addoptHtml += "	<input type='radio' name='addopt_type[" + idx + "]' value = 'T' checked > 필수항목";
		addoptHtml += "	<input type='radio' name='addopt_type[" + idx + "]' value = 'F' > 선택항목";
		addoptHtml += "	<select name='addopt_maxln[" + idx + "]' >";
		for( var i = 1; i <= 50; i++ ){
			if( i == 10) addoptHtml += "		<option value='" + i + "' selected > " + i + "자</option>";
			else addoptHtml += "		<option value='" + i + "'> " + i + "자</option>";
		}
		addoptHtml += "	</select>";
		addoptHtml += "	<button type='button' name='addOptDel' >삭제</button>";
		addoptHtml += "</li>";

		$('#ID_add_content').append( addoptHtml );

	});

	$(document).on( 'click', 'button[name="addOptDel"]', function(){
		var content_element = $(this).parent();
		var content_element_leng = $('#ID_add_content > li').length;
		var this_idx = $('button[name="addOptDel"]').index( $(this) ) + 1;

		for( var i = ( this_idx + 1 ); i < content_element_leng; i++ ){
			//console.log( $(content_element).eq(i) );
			$('input[name="addopt_subject[' + i + ']"]').attr( 'name', 'addopt_subject[' + ( i - 1 ) + ']' );
			$('input[name="addopt_type[' + i + ']"]').each( function(){
				$(this).attr( 'name', 'addopt_type[' + ( i - 1 ) + ']' );
			});
			$('select[name="addopt_maxln[' + i + ']"]').attr( 'name', 'addopt_maxln[' + ( i - 1 ) + ']' );
		}
		
		$(this).parent().remove();
	});

	//옵션사용 click
	$(document).on( 'click', 'input[name="opt_select"]', function(){
		if( $(this).val() == 1 ){
			$('#ID_p_goods_code_area').hide();
			$('#ID_option_type_area').show();
			$('input[name="opt_type"]:checked').trigger('click');
		} else {
			if( $('input[name="spl_subject[]"').eq(0).val().length > 0 || $('input[name="opt_subject[]"').eq(0).val().length > 0 ){
				if( !confirm("옵션이 존재합니다.\n사용하지 않을 경우 기존 내용은 삭제됩니다.") ){
					$(this).prop( 'checked', false );
					$('input[name="opt_select"]').eq(0).prop( 'checked', true );
					return;
				}  else {
					$("input[name='option_create']").val("0");
				}
			}
			if($("input[name='option_create']").val() == 0) {
				$("input[name^=opt_subject]").val("");
				$("input[name^=opt_content]").val("");
				$("input[name^=spl_subject]").val("");
				$("input[name^=spl]").val("");
				$("input[name^=necessary_tf]").prop( 'checked', false );
				$("#sit_option_frm").empty();
				$("#sit_option_addfrm").empty();

				$('#ID_p_goods_code_area').show();
				$('#ID_option_type_area').hide();
				$('#ID_opt1_area').hide();
				$('#ID_supply_area').hide();
			}
		}
		
	});

	//옵션 구성방식 click
	$(document).on( 'click', 'input[name="opt_type"]', function(){
		if( $(this).val() == 0 ){
			if( $('input[name="spl_subject[]"').eq(0).val().length > 0 ){
				if( !confirm("독립형 옵션이 존재합니다.\n옵션을 변경할경우 기존 내용은 삭제됩니다.") ){
					$(this).prop( 'checked', false );
					$('input[name="opt_type"]').eq(1).prop( 'checked', true );
					return;
				} else {
					$("input[name='option_create']").val("0");
				}
			}
			$('#ID_opt1_area').show();
			$('#ID_supply_area').hide();
		} else {
			if( $('input[name="opt_subject[]"').eq(0).val().length > 0 ){
				if( !confirm("조합형 옵션이 존재합니다.\n옵션을 변경할경우 기존 내용은 삭제됩니다.") ){
					$(this).prop( 'checked', false );
					$('input[name="opt_type"]').eq(0).prop( 'checked', true );
					return;
				} else {
					$("input[name='option_create']").val("0");
				}
			}
			$('#ID_opt1_area').hide();
			$('#ID_supply_area').show();
		}
		if($("input[name='option_create']").val() == 0) {
			$("input[name^=opt_subject]").val("");
			$("input[name^=opt_content]").val("");
			$("input[name^=spl_subject]").val("");
			$("input[name^=spl]").val("");
			$("input[name^=necessary_tf]").prop( 'checked', false );
			$("#sit_option_frm").empty();
			$("#sit_option_addfrm").empty();
		}
	});

	$(document).on( 'click', 'button[name="btn_opt_subject_add"]' , function() {
		var html = '';
		var element = $("input[name^=opt_subject]");
		var element_cnt = $(element).length;
		var $option_table = $("#sit_option_frm");
		
		html += '<tr>';
		html +=	'	<th scope="row">';
		html +=	'		<label for="opt_subject">옵션</label>';
		html +=	'		<input type="text" name="opt_subject[] " value="" class="frm_input" size="10" maxlength="6" onChange="javascript:option_create_reset();">';
		html +=	'	</th>';
		html +=	'	<td>';
		html +=	'		<label for="opt"><b>항목</b></label>';
		html +=	'		<input type="text" name="opt_content[]" value="" class="frm_input" size="50" onChange="javascript:option_create_reset();">';
		html +=	'		<button type="button" class="btn_frmline" name="btn_opt_subject_del" >삭제</button>';
		//html +=	'		<button type="button" class="btn_frmline" name="btn_opt_subject_add" >추가</button>';
		html +=	'	</td>';
		html += '</tr>';

		//$(this).remove();
		option_create_reset();
		$option_table.empty();
		$('#opt_subject_body').append( html );

	});
	//옵션 삭제
	$(document).on( 'click', 'button[name="btn_opt_subject_del"]', function(){
		var $option_table = $("#sit_option_frm");
		$(this).parent().parent().remove();
		/*
		if( $("input[name^=opt_content]").last().parent().find('button[name="btn_opt_subject_add"]').length == 0 ){
			$("input[name^=opt_content]").last().parent().append( ' <button type="button" class="btn_frmline" name="btn_opt_subject_add" >추가</button>' );
		}
		*/
		option_create_reset();
		$option_table.empty();
	});
	
	// 옵션목록생성
	$("#option_table_create").click(function() {
		//var it_id = $.trim($("input[name=it_id]").val()); // 수정
		var it_id = $.trim($("#it_id").val()); // 수정
		var opt_subject = [];
		var opt_content = [];
		var $option_table = $("#sit_option_frm");

		$("input[name^=opt_subject]").each( function( subject_idx, subject_obj ) {
			var content = $("input[name^=opt_content]").eq( subject_idx );
			if( $(this).val().length > 0 && $(content).val().length > 0 ) {
				opt_subject.push( $.trim( $(this).val() ) );
				opt_content.push( $.trim( $(content).val() ) );
			} else {
				alert("옵션명과 옵션항목을 입력해 주십시오.");
				count++;
				if( $(this).val().length == 0)
				{
					$(this).focus();
				} else {
					if( $(content).val().length == 0) {
						$(content).focus();
					}
				}
				return false;
			}
		});
/*
		$("input[name^=opt_content]").each( function( content_idx, content_obj ) {
			if( $.type( opt_subject[content_idx] ) != 'undefined' && $(this).val().length > 0 ) {
				opt_content.push( $.trim( $(this).val() ) );
			}
		});
*/
		/*if( ( opt_subject[0].length == 0 ) || ( opt_content[0].length == 0 ) ) {
			alert("옵션명과 옵션항목을 입력해 주십시오.");
			return false;
		}*/

		$.post(
			"ajax_productoption_new.php",
			{ it_id: it_id, w: "u", opt_subject: opt_subject, opt_content: opt_content },
			function(data) {
				$("input[name='option_create']").val('1');
				$option_table.empty().html(data);
				//console.log( data );
			}
		);
	});

	// 모두선택
	$(document).on("click", "input[name=opt_chk_all]", function() {
		if($(this).is(":checked")) {
			$("input[name='opt_chk[]']").attr("checked", true);
		} else {
			$("input[name='opt_chk[]']").attr("checked", false);
		}
	});

	// 선택삭제
	$(document).on("click", "#sel_option_delete", function() {
		var $el = $("input[name='opt_chk[]']:checked");
		if($el.size() < 1) {
			alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
			return false;
		}

		$el.closest("tr").remove();
	});

	// 일괄적용
	$(document).on("click", "#opt_value_apply", function() {
		if($(".opt_com_chk:checked").size() < 1) {
			alert("일괄 수정할 항목을 하나이상 체크해 주십시오.");
			return false;
		}

		var opt_price = $.trim($("#opt_com_price").val());
		var opt_stock = $.trim($("#opt_com_stock").val());
		var opt_noti = $.trim($("#opt_com_noti").val());
		var opt_use = $("#opt_com_use").val();
		var $el = $("input[name='opt_chk[]']:checked");

		// 체크된 옵션이 있으면 체크된 것만 적용
		if($el.size() > 0) {
			var $tr;
			$el.each(function() {
				$tr = $(this).closest("tr");

				if($("#opt_com_price_chk").is(":checked"))
					$tr.find("input[name='opt_price[]']").val(opt_price);

				if($("#opt_com_stock_chk").is(":checked"))
					$tr.find("input[name='opt_stock_qty[]']").val(opt_stock);

				if($("#opt_com_noti_chk").is(":checked"))
					$tr.find("input[name='opt_noti_qty[]']").val(opt_noti);

				if($("#opt_com_use_chk").is(":checked"))
					$tr.find("select[name='opt_use[]']").val(opt_use);
			});
		} else {
			if($("#opt_com_price_chk").is(":checked"))
				$("input[name='opt_price[]']").val(opt_price);

			if($("#opt_com_stock_chk").is(":checked"))
				$("input[name='opt_stock_qty[]']").val(opt_stock);

			if($("#opt_com_noti_chk").is(":checked"))
				$("input[name='opt_noti_qty[]']").val(opt_noti);

			if($("#opt_com_use_chk").is(":checked"))
				$("select[name='opt_use[]']").val(opt_use);
		}
	});
});

function option_create_reset() {// 옵션/항목변경시 0으로 변경
	$("input[name='option_create']").val('0');
}
</script>

<!-- 독립형 옵션 2015 10 28 유동혁 -->
<script>
$(function() {
	<?php if($it['it_id'] && $ps_run) { ?>
	// 추가옵션의 항목 설정
	var arr_subj = new Array();
	var subj, spl;
	var tf_type = '';

	$("input[name='spl_subject[]']").each(function() {
		subj = $.trim($(this).val());
		if(subj && $.inArray(subj, arr_subj) == -1)
			arr_subj.push(subj);
	});

	for(i=0; i<arr_subj.length; i++) {
		var arr_spl = new Array();
		$(".spl-subject-cell").each(function(index) {
			subj = $(this).text();
			if(subj == arr_subj[i]) {
				spl = $(".spl-cell:eq("+index+")").text();
				arr_spl.push(spl);
				necessary_tf = $("input[name='spl_tf[]']:eq("+index+")").val();
			}
		});
		
		$("input[name='spl[]']:eq("+i+")").val(arr_spl.join('@#'));
		if( necessary_tf == 'T' ) $("input[name='necessary_tf[]']:eq(" + i + ")").prop( 'checked', true );
	}
	//console.log( subj ); console.log( spl );
	<?php } ?>
	// 입력필드추가
	$("#add_supply_row").click(function() {
		var $el = $("#sit_supply_frm tr:last");
		var fld = "<tr>\n";
		fld += "<th scope=\"row\">\n";
		fld += "<label for=\"\">옵션</label>\n";
		fld += "<input type=\"text\" name=\"spl_subject[]\" value=\"\" class=\"frm_input\" size=\"10\" maxlength=\"6\" onChange=\"javascript:option_create_reset();\">\n";
		fld += "</th>\n";
		fld += "<td>\n";
		fld += "<label for=\"\"><b>항목</b></label>\n";
		fld += "<input type=\"text\" name=\"spl[]\" value=\"\" class=\"frm_input\" size=\"40\" onChange=\"javascript:option_create_reset();\">\n";
		fld += "<input type=\"checkbox\" name=\"necessary_tf[]\" value=\"T\" > *필수옵션으로 사용\n";
		fld += "<button type=\"button\" id=\"del_supply_row\" class=\"btn_frmline\">삭제</button>\n";
		fld += "</td>\n";
		fld += "</tr>";

		$el.after(fld);
		option_create_reset();
		$supply_table.empty();
		supply_sequence();
	});

	// 입력필드삭제
	$(document).on("click", "#del_supply_row", function() {
		$(this).closest("tr").remove();
		option_create_reset();
		supply_sequence();
	});

	// 옵션목록생성
	$("#supply_table_create").click(function() {
		var it_id = $.trim($("#it_id").val());
		//var it_id = $.trim($("input[name=it_id]").val());
		var subject = new Array();
		var supply = new Array();
		var necessary_type = new Array();
		var subj, spl, necessary_tf;
		var count = 0;
		var $el_subj = $("input[name='spl_subject[]']");
		var $el_spl = $("input[name='spl[]']");
		var $el_necessary_tf = $("input[name='necessary_tf[]']");
		var $supply_table = $("#sit_option_addfrm");

		$el_subj.each(function(index) {
			subj = $.trim($(this).val());
			spl = $.trim($el_spl.eq(index).val());
			if( $el_necessary_tf.eq(index).prop( 'checked' ) ) {
				necessary_tf = 'T';
			} else {
				necessary_tf = 'F';
			}
			
			if(subj && spl) {
				subject.push(subj);
				supply.push(spl);
				necessary_type.push(necessary_tf);
			} else {
				alert("옵션명과 옵션항목을 입력해 주십시오.");
				count++;
				if( $(this).val().length == 0)
				{
					$(this).focus();
				} else {
					if( $el_spl.eq(index).val().length == 0) {
						$el_spl.eq(index).focus();
					}
				}
				return false;
			}
		});

		/*if(!count) {
			alert("추가옵션명과 추가옵션항목을 입력해 주십시오.");
			return false;
		}*/
		if(count == 0) {		
			$.post(
				"ajax_productoption_plus.php",
				{ it_id: it_id, w: "u", 'subject[]': subject, 'supply[]': supply, 'necessary_tf[]': necessary_type },
				function(data) {
					$("input[name='option_create']").val('1');
					$supply_table.empty().html(data);
				}
			);
		}
	});

	// 모두선택
	$(document).on("click", "input[name=spl_chk_all]", function() {
		if($(this).is(":checked")) {
			$("input[name='spl_chk[]']").attr("checked", true);
		} else {
			$("input[name='spl_chk[]']").attr("checked", false);
		}
	});

	// 선택삭제
	$(document).on("click", "#sel_supply_delete", function() {
		var $el = $("input[name='spl_chk[]']:checked");
		if($el.size() < 1) {
			alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
			return false;
		}

		$el.closest("tr").remove();
	});

	// 일괄적용
	$(document).on("click", "#spl_value_apply", function() {
		if($(".spl_com_chk:checked").size() < 1) {
			alert("일괄 수정할 항목을 하나이상 체크해 주십시오.");
			return false;
		}

		var spl_price = $.trim($("#spl_com_price").val());
		var spl_stock = $.trim($("#spl_com_stock").val());
		var spl_noti = $.trim($("#spl_com_noti").val());
		var spl_use = $("#spl_com_use").val();
		var $el = $("input[name='spl_chk[]']:checked");

		// 체크된 옵션이 있으면 체크된 것만 적용
		if($el.size() > 0) {
			var $tr;
			$el.each(function() {
				$tr = $(this).closest("tr");

				if($("#spl_com_price_chk").is(":checked"))
					$tr.find("input[name='spl_price[]']").val(spl_price);

				if($("#spl_com_stock_chk").is(":checked"))
					$tr.find("input[name='spl_stock_qty[]']").val(spl_stock);

				if($("#spl_com_noti_chk").is(":checked"))
					$tr.find("input[name='spl_noti_qty[]']").val(spl_noti);

				if($("#spl_com_use_chk").is(":checked"))
					$tr.find("select[name='spl_use[]']").val(spl_use);
			});
		} else {
			if($("#spl_com_price_chk").is(":checked"))
				$("input[name='spl_price[]']").val(spl_price);

			if($("#spl_com_stock_chk").is(":checked"))
				$("input[name='spl_stock_qty[]']").val(spl_stock);

			if($("#spl_com_noti_chk").is(":checked"))
				$("input[name='spl_noti_qty[]']").val(spl_noti);

			if($("#spl_com_use_chk").is(":checked"))
				$("select[name='spl_use[]']").val(spl_use);
		}
	});
    //필수옵션 체크
    $(document).on( 'click', 'input[name="necessary_tf[]"]', function( event ) {
        var this_index = $('input[name="necessary_tf[]"]').index( $(this) );
        var this_object = $(this);
        if( this_index > 0 && $(this).prop('checked') ) {
            $('input[name="necessary_tf[]"]').each( function( i, obj ) { // 체크할 경우
                if( i < this_index && !$(this).prop('checked') ){
                    alert('필수 옵션을 위부터 체크 해주세요.');
                    $(this_object).prop( 'checked', false );
                    return false;
                }
            });
        } else if( !$(this).prop('checked') ){ // 해제할 경우
            for( var i = $('input[name="necessary_tf[]"]').length - 1; i >= 0; i-- ){
                if( i > this_index && $('input[name="necessary_tf[]"]').eq( i ).prop('checked') ){
                    alert('필수 옵션을 아래부터 해제 해주세요.');
                    $(this_object).prop( 'checked', true );
                    return false;
                }
            }
        }
    });

});

function supply_sequence()
{
	var $tr = $("#sit_supply_frm tr");
	var seq;
	var th_label, td_label;

	$tr.each(function(index) {
		seq = index + 1;
		$(this).find("th label").attr("for", "spl_subject_"+seq).text("옵션");
		$(this).find("th input").attr("id", "spl_subject_"+seq);
		$(this).find("td label").attr("for", "spl_item_"+seq);
		$(this).find("td label b").text("항목");
		$(this).find("td input").attr("id", "spl_item_"+seq);
	});
}

<?php if(strpos($_data->maximage, "http://") !== false) { ?>
	document.form1.use_imgurl.checked = true;
	for(var jj=1;jj<=3;jj++) {
		idx=jj;
		if(idx==1) idx="";
		document.form1["userfile"+idx].style.display='none';
		document.form1["userfile"+idx+"_url"].style.display='';
		document.form1["userfile"+idx].disabled=true;
		document.form1["userfile"+idx+"_url"].disabled=false;
	}
	$(".file_type").hide();
	$(".url_type").show();
<?php } ?>
</script>
<!-- //독립형 옵션 -->

<?php
include("copyright.php");
?>
</body>
</html>
