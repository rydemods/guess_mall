<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$category_data=$_REQUEST["category_data"];
if($category_data){
	$arrCategoryData = explode("|", $_REQUEST['category_data']);

	$_REQUEST["code_a"] = $arrCategoryData[0];
	$_REQUEST["code_b"] = $arrCategoryData[1];
	$_REQUEST["code_c"] = $arrCategoryData[2];
	$_REQUEST["code_d"] = $arrCategoryData[3];
}
$copy_type=$_REQUEST["copy_type"];
$mode=$_REQUEST["mode"];
$s_keyword=trim($_REQUEST["s_keyword"]);
$s_check=$_REQUEST["s_check"];
$display_yn=$_REQUEST["display_yn"];
$vip=$_REQUEST["vip"];
$vperiod=$_REQUEST["vperiod"];
$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];
$search_end=$_REQUEST["search_end"];
$search_start=$_REQUEST["search_start"];
$sellprice_min=$_REQUEST["sellprice_min"];
$sellprice_max=$_REQUEST["sellprice_max"];
$code_type=$_REQUEST["code_type"];
$code_area=$_REQUEST["code_area"];
if($code_area){
	$s_keyword="";
}
$listnum=(int)$_REQUEST["listnum"];
if(!$listnum){
	$listnum = (int)$_REQUEST["listnum_select"];
}
$gotopage = $_REQUEST["gotopage"];

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$likecodeExchange = $code_a."|".$code_b."|".$code_c."|".$code_d;

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

if($display_yn==""){
	$display_yn = "all";
}
if($vip==""){
	$vip = "all";
}
if($s_check==""){
	$s_check = "all";
}
$checked["display_yn"][$display_yn] = "checked";
$checked["vip"][$vip] = "checked";
$checked["s_check"][$s_check] = "checked";
//$checked["check_vperiod"][$vperiod] = "checked";

$imagepath=$Dir.DataDir."shopimages/product/";
if($mode=="delete"){
	$prcode=$_REQUEST["prcode"];

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

	$sql = "DELETE FROM tblproducttheme WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproduct WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproductgroupcode WHERE productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	#카테고리 삭제
	$sql = "DELETE FROM tblproductlink WHERE c_productcode = '{$prcode}'";
	pmysql_query($sql,get_db_conn());

	if($vassembleuse=="Y") {
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
	} else {
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

		$sql = "UPDATE tblassembleproduct SET ";
		$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$vpridx}',''), ";
		$sql.= "assemble_list=REPLACE(assemble_list,',{$vpridx}','') ";
		pmysql_query($sql,get_db_conn());
	}

	if($vender>0) {
		//미니샵 테마코드에 등록된 상품 삭제
		setVenderThemeDeleteNor($prcode, $vender);
		setVenderCountUpdateMin($vender, $vdisp);

		$tmpcode_a=substr($prcode,0,3);
		$sql = "SELECT COUNT(*) as cnt FROM tblproduct ";
		$sql.= "WHERE productcode LIKE '{$tmpcode_a}%' AND vender='{$vender}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$prcnt=$row->cnt;
		pmysql_free_result($result);

		if($prcnt==0) {
			setVenderDesignDeleteNor($tmpcode_a, $vender);
			$imagename=$Dir.DataDir."shopimages/vender/{$vender}_CODE10_{$tmpcode_a}.gif";
			@unlink($imagename);
		}
	}

	$log_content = "## 상품삭제 ## - 상품코드 $prcode - 상품명 : ".urldecode($productname)." $display_yn";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$delshopimage=$Dir.DataDir."shopimages/product/{$prcode}*";
	proc_matchfiledel($delshopimage);

	delProductMultiImg("prdelete","",$prcode);

	$onload="<script>window.onload=function(){ alert(\"상품 삭제가 완료되었습니다.\");}</script>";
	$prcode="";

}

if($mode=="modify"){
	$modcode = $_REQUEST["modcode"];
	$modsellprice = $_REQUEST["modsellprice"];
	$modquantity = $_REQUEST["modquantity"];
	$moddisplay = $_REQUEST["moddisplay"];
	$mc = explode(",", $modcode);
	$ms = explode(",", $modsellprice);
	$mq = explode(",", $modquantity);
	$md = explode(",", $moddisplay);
	$temp_quantity = "";
	for($aa=0;count($mc)>$aa;$aa++){
		$temp_quantity = $mq[$aa];
		if($mq[$aa] == '품절'){
			list($temp_quantity)=pmysql_fetch("SELECT quantity FROM tblproduct WHERE productcode='".$mc[$aa]."'");
			if(!$temp_quantity) $temp_quantity = 'NULL';
		}
		$usql = "UPDATE tblproduct ";
		$usql.= "SET sellprice = {$ms[$aa]} ";
		$usql.= ", quantity = ".$temp_quantity." ";
		$usql.= ", display = '".$md[$aa]."' ";
		$usql.= "WHERE productcode = '".$mc[$aa]."' ";
		pmysql_query($usql,get_db_conn());
	}
}

else if($mode=="copy"){
	$prcode=$_REQUEST["prcode"];
	$vender_prcodelist=array();
	if (strlen($prcode)==18) {
		$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}'";
		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {

			$copycode=substr($prcode,0,12);

			$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '{$copycode}%' ";
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
				$maximage=$copycode.$newproductcode.".".$ext;
				if (file_exists("$path$row->maximage")) {
					copy("$path$row->maximage","$path$maximage");
				}
			} else $maximage="";
			if (ord($row->minimage)) {
				$ext = strtolower(pathinfo($row->minimage,PATHINFO_EXTENSION));
				$minimage=$copycode.$newproductcode."2.".$ext;
				if (file_exists("$path$row->minimage")) {
					copy("$path$row->minimage","$path$minimage");
				}
			} else $minimage="";
			if (ord($row->tinyimage)) {
				$ext = strtolower(pathinfo($row->tinyimage,PATHINFO_EXTENSION));
				$tinyimage=$copycode.$newproductcode."3.".$ext;
				if (file_exists("$path$row->tinyimage")) {
					copy("$path$row->tinyimage","$path$tinyimage");
				}
			} else $tinyimage="";
			if (ord($row->quantity)==0) $quantity="NULL";
			else $quantity=$row->quantity;

			if(ord($row->brand)==0) $row->brand = 'NULL';
			$productname = pmysql_escape_string($row->productname);
			$production = pmysql_escape_string($row->production);
			$madein = pmysql_escape_string($row->madein);
			$model = pmysql_escape_string($row->model);
			$tempkeyword = pmysql_escape_string($row->keyword);
			$addcode = pmysql_escape_string($row->addcode);
			$userspec = pmysql_escape_string($row->userspec);
			$option1 = pmysql_escape_string($row->option1);
			$option2 = pmysql_escape_string($row->option2);
			$content = pmysql_escape_string($row->content);
			$selfcode = pmysql_escape_string($row->selfcode);
			$assembleproduct = pmysql_escape_string($row->assembleproduct);

			$sql = "INSERT INTO tblproduct(
			productcode	,
			productname	,
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
			brand		,
			opendate	,
			selfcode	,
			bisinesscode	,
			quantity	,
			group_check	,
			keyword		,
			addcode		,
			userspec	,
			maximage	,
			minimage	,
			tinyimage	,
			option_price	,
			option_quantity	,
			option1		,
			option2		,
			sabangnet_flag,
			etctype		,
			deli		,
			package_num	,
			display		,
			date		,
			vender		,
			regdate		,
			modifydate	,
			content,
			sewon_option_no,
			sewon_option_code1,
			sewon_option_code2) VALUES (
			'".$copycode.$newproductcode."',
			'{$productname}',
			'{$row->assembleuse}',
			'{$row->assembleproduct}',
			{$row->sellprice},
			{$row->consumerprice},
			{$row->buyprice},
			'{$row->reserve}',
			'{$row->reservetype}',
			'{$production}',
			'{$madein}',
			'{$model}',
			{$row->brand},
			'{$row->opendate}',
			'{$row->selfcode}',
			'{$row->bisinesscode}',
			{$quantity},
			'{$row->group_check}',
			'{$tempkeyword}',
			'{$addcode}',
			'{$userspec}',
			'{$maximage}',
			'{$minimage}',
			'{$tinyimage}',
			'{$row->option_price}',
			'{$row->option_quantity}',
			'{$option1}',
			'{$option2}',
			'{$copy_type}',
			'{$row->etctype}',
			'{$row->deli}',
			'".(int)$row->package_num."',
			'N',
			'".(($newtime=="Y")?date("YmdHis"):$row->date)."',
			'{$row->vender}',
			now(),
			now(),
			'{$content}',
			'{$row->sewon_option_no}',
			'{$row->sewon_option_code1}',
			'{$row->sewon_option_code2}') RETURNING pridx";
			$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));

			$fromproductcodes.="|".$prcode;
			$copyproductcodes.="|".$copycode.$newproductcode;

			if($row->vender>0) {
				$vender_prcodelist[$row->vender]["IN"][]=$copycode.$newproductcode;
			}

			$copy_cate_query="select * from tblproductlink where c_productcode='".$prcode."' and c_productcode!=''";
			$copy_cate_result=pmysql_query($copy_cate_query);
			while($copy_cate_row=pmysql_fetch_object($copy_cate_result)){
				if(!$copy_cate_row->c_maincate){
					$c_maincate = 0;
				}else{
					$c_maincate  = $copy_cate_row->c_maincate;
				}
				$addCopyCol = $addCopyVal = '';
				if($copy_cate_row->goodsno){
					$addCopyCol = ", goodsno";
					$addCopyVal = ", ".$copy_cate_row->goodsno."";
				}
				$copy_cate_insert_sql = "INSERT INTO tblproductlink 
							(c_productcode, c_category, c_maincate, c_date, chk, godo_cate) 
							VALUES 
							('".$copycode.$newproductcode."', '".$copy_cate_row->c_category."', ".$c_maincate.", '".$copy_cate_row->c_date."', '".$copy_cate_row->chk."', '".$copy_cate_row->godo_cate."')";
				pmysql_query($copy_cate_insert_sql,get_db_conn());
			}

			if($row->group_check=="Y") {
				$sql = "INSERT INTO tblproductgroupcode SELECT '".$copycode.$newproductcode."', group_code FROM tblproductgroupcode WHERE productcode = '{$prcode}' ";
				pmysql_query($sql,get_db_conn());
			}
			if($row->assembleuse=="Y") { //코디/조립 상품일 경우
				$sql = "INSERT INTO tblassembleproduct ";
				$sql.= "SELECT '".$copycode.$newproductcode."', assemble_type, assemble_title, assemble_pridx, assemble_list FROM tblassembleproduct ";
				$sql.= "WHERE productcode='{$prcode}' ";
				pmysql_query($sql,get_db_conn());

				$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode = '{$prcode}' ";

				$result = pmysql_query($sql,get_db_conn());
				if($row = @pmysql_fetch_object($result)) {
					if(ord(str_replace("","",$row->assemble_pridx))) {
						$sql = "UPDATE tblproduct SET ";
						$sql.= "assembleproduct = assembleproduct||',".$copycode.$newproductcode."' ";
						$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
						$sql.= "AND assembleuse != 'Y' ";
						pmysql_query($sql,get_db_conn());
					}
				}
				pmysql_free_result($result);
			} else {
				$sql = "UPDATE tblproduct SET assembleproduct = '' ";
				$sql.= "WHERE productcode='".$copycode.$newproductcode."'";
				pmysql_query($sql,get_db_conn());
			}

			$log_content = "## 상품복사입력 ## - 상품코드 {$prcode} => ".$copycode.$newproductcode." - 상품명 : ".$productname;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		}
		//입점업체 상품 관련 처리
		if(count($vender_prcodelist)>0) {
			$tmpvender=$vender_prcodelist;
			while(list($vender,$prarr)=each($tmpvender)) {
				$tmpcode_a=array();
				for($kk=0;$kk<count($prarr["IN"]);$kk++) {
					//insert 처리
					setVenderDesignInsert($vender, $prarr["IN"][$kk]);

				}
				//미니샵 상품수 업데이트 (진열된 상품만)
				$sql="SELECT COUNT(*) as prdt_allcnt,COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
				$sql.="WHERE vender='{$vender}' ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				$prdt_allcnt=(int)$row->prdt_allcnt;
				$prdt_cnt=(int)$row->prdt_cnt;
				pmysql_free_result($result);

				setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $vender);

				if(count($tmpcode_a)>0) {
					$sql = "SELECT SUBSTR(productcode,1,3) as code_a FROM tblproduct ";
					$sql.= "WHERE ( ";
					$arr_code_a=$tmpcode_a;
					$_=array();
					while(list($key,$val)=each($arr_code_a)) {
						if(strlen($key)==3) {
							$_[] = "productcode LIKE '{$key}%' ";
						}
					}
					$sql.= implode("OR ",$_);
					$sql.= ") ";
					$sql.= "AND vender='{$vender}' ";
					$sql.= "GROUP BY code_a ";
					$result=pmysql_query($sql,get_db_conn());
					while($row=pmysql_fetch_object($result)) {
						unset($tmpcode_a[$row->code_a]);
					}
					pmysql_free_result($result);

					if(count($tmpcode_a)>0) {
						while(list($key,$val)=each($tmpcode_a)) {
							$imagename = $Dir.DataDir."shopimages/vender/{$vender}_CODE10_{$key}.gif";
							@unlink($imagename);
						}
						$str_code_a = implode(',',array_keys($tmpcode_a));
						$str_code_a=str_replace(',','\',\'',$str_code_a);
						setVenderDesignDelete($str_code_a, $vender);
					}
				}
			}
		}

		delProductMultiImg($mode,substr($fromproductcodes,1),substr($copyproductcodes,1));

		$onload="<script>window.onload=function(){ alert(\"상품 복사가 완료되었습니다.\");}</script>";
		$prcode="";
	}
}


$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function ProductInfo(prcode,popuptype,category_data) {
	code=prcode.substring(0,12);
	popup=popuptype;
	document.form_register.code.value=code;
	document.form_register.prcode.value=prcode;
	document.form_register.popup.value=popup;
	document.form_register.category_data.value=category_data;
	if (popup=="YES") {
		document.form_register.action="product_register.add.php";
		document.form_register.target="register";
		window.open("about:blank","register","width=1000,height=700,scrollbars=yes,status=no");
	} else {
		document.form_register.action="product_register.set.php";
		document.form_register.target="";
	}
	document.form_register.submit();
}

function ProductDel(prcode){
	if(confirm("선택하신 상품을 정말로 삭제하시겠습니까?")){
		document.form1.mode.value="delete";
		document.form1.prcode.value=prcode;
		document.form1.submit();
	}
}

function Productcopy(prcode,copy_type){
	if(confirm("선택하신 상품을 동일하게 한개 더 생성하시겠습니까?")){
		document.form1.mode.value="copy";
		document.form1.prcode.value=prcode;
		document.form1.copy_type.value=copy_type;
		document.form1.submit();
	}
}

function registeradd(){
	document.form_register.code.value='';
	document.form_register.prcode.value='';
	document.form_register.popup.value="NO";
	//document.form_register.code.value="004002000000";
	document.form_register.action="product_register.set.php";
	document.form_register.target="";
	document.form_register.submit();
}

function listnumSet(listnum){
	document.form1.listnum.value=listnum.value;
	document.form1.submit();
}

$(document).ready(function(){
	$('.check-all').click(function(){
		$('.code_check').prop('checked', this.checked);
	});

	$('.th_sellprice').keyup(function(){
		var nocomma = document.form1.th_sellprice.value.replace(/,/gi,'');
		var b = '';
		var i = 0;
		for (var k=(nocomma.length-1);k>=0; k--){
			var a = nocomma.charAt(k);
			if (k == 0 && a == 0) {
			document.form1.th_sellprice.value = '';
			return;
			}else {
				if (i != 0 && i % 3 == 0) {
				b = a + "," + b ;
				}else {
				b = a + b;
				}
			i++;
			}
		}
		document.form1.th_sellprice.value = b;
		return;
	});

	$('.edit').click(function(){
		if (confirm("선택한 항목을 수정하시겠습니까?") == true){
			var modfrm = document.form_modify;
			var edfrm = document.form1;

			for (var i=0; i<edfrm.code_check.length; i++) {
				if (edfrm.code_check[i].checked) {
					modfrm.modcode.value = edfrm.product_code[i].value+","+modfrm.modcode.value;

					if(edfrm.th_sellprice.value!=""){
						var sellprice = edfrm.th_sellprice.value;
						sellprice = sellprice.replace(/,/gi, '');
						modfrm.modsellprice.value = sellprice+","+modfrm.modsellprice.value;
					}else{
						var sellprice = edfrm.sellprice[i].value;
						sellprice = sellprice.replace(/,/gi, '');
						modfrm.modsellprice.value = sellprice+","+modfrm.modsellprice.value;
					}

					if(edfrm.th_quantity.value!=""){
						modfrm.modquantity.value = edfrm.th_quantity.value+","+modfrm.modquantity.value;
					}else{
						modfrm.modquantity.value = edfrm.quantity[i].value+","+modfrm.modquantity.value;
					}

					if(edfrm.th_display.value!=""){
						modfrm.moddisplay.value = edfrm.th_display.value+","+modfrm.moddisplay.value;
					}else{
						modfrm.moddisplay.value = edfrm.display_select[i].value+","+modfrm.moddisplay.value;
					}
				}
			}
			modfrm.mode.value = "modify";
			modfrm.modcode.value=modfrm.modcode.value.slice(0, -1);
			modfrm.modsellprice.value=modfrm.modsellprice.value.slice(0, -1);
			modfrm.modquantity.value=modfrm.modquantity.value.slice(0, -1);
			modfrm.moddisplay.value=modfrm.moddisplay.value.slice(0, -1);
			modfrm.submit();
		}else{
			return;
		}

	});
});
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>상품관리 리스트</span></p></div></div>

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
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=prcode>
			<input type=hidden name=copy_type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value=>
			<input type=hidden name=listnum value="<?=$listnum?>">
			<tr>
				<td>
				<div class="title_depth3">상품관리</div>

				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>상품검색</span></th>
							<td><input class="w200" type="text" name="s_keyword" value="<?=$s_keyword?>"></td>
							<th style="text-align:center; width:250;" rowspan="2">빠른조회<br/>
							<select name="code_type"><option value="1"  <?if($code_type=="1"){ echo "selected";}?>>상품코드</option>
														<option value="2"  <?if($code_type=="2"){ echo "selected";}?>>세원ERP코드</option></select></th>
						</tr>
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
								$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
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
								$strcodelist.= "CodeInit();\n";
								$strcodelist.= "</script>\n";

								echo $strcodelist;


								echo "<select name=code_a style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
								echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_b style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
								echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_c style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
								echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_d style=\"width:170px;\">\n";
								echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
				?>
							</td>
						</tr>
						<tr>
							<th><span>등록일</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
							<td rowspan="4"><textarea style="width:230; height:100;" name="code_area"><?=$code_area?></textarea> </td>
						</tr>
						<tr>
							<th><span>상품금액별 검색</span></th>
							<td><input class="input_bd_st01" type="text" name="sellprice_min" value="<?=$sellprice_min?>"/> 원 ~ <input class="input_bd_st01" type="text" name="sellprice_max" value="<?=$sellprice_max?>"/> 원</td>
						</tr>
						<tr>
							<th><span>품절 유무</span></th>
							<td><input type="radio" name="s_check" value="all" <?=$checked["s_check"]['all']?>/>전체 <input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>판매중 <input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절</td>
						</tr>
						<tr>
							<th><span>진열 유무</span></th>
							<td><input type="radio" name="display_yn" value="all" <?=$checked["display_yn"]['all']?>/>전체 
							<input type="radio" name="display_yn" value="Y" <?=$checked["display_yn"]['Y']?>/> 진열&nbsp;&nbsp; 
							<input type="radio" name="display_yn" value="N" <?=$checked["display_yn"]['N']?>/> 미진열</td>
						</tr>
						<tr>
							<th><span>VIP 상품 보기</span></th>
							<td><input type="radio" name="vip" value="all" <?=$checked["vip"]['all']?>/>전체
							<input type="radio" name="vip" value="1" <?=$checked["vip"]['1']?>/> vip 전용&nbsp;&nbsp;
							<input type="radio" name="vip" value="2" <?=$checked["vip"]['2']?>/> vip 제외</td>
						</tr>
					</table>
					<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a></p>
				</div>

				<div class="table_style02">
					<table width=100% cellpadding=0 cellspacing=0>
						<colgroup>
							<col width="50" />
							<col width="50" />
							<col width="80" />
							<col width="120" />
							<col width="120" />
							<col width="" />
							<col width="60" />
							<col width="80" />
							<col width="80" />
							<col width="40" />
							<col width="40" />
							<col width="60" />
							<col width="60" />
							<col width="40" />
						</colgroup>
						<div class="btn_right">
							<select name="listnum_select" onchange="javascript:listnumSet(this)">
								<option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
								<option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
								<option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
								<option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
								<option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
							</select>
						</div>
						<tr>
							<th><input type="checkbox" name="check-all" class="check-all"></th>
							<th>No</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>세원ERP코드</th>
							<th width="300">상품명</th>
							<th>등록일</th>
							<th>시중가</th>
							<th>판매가<br/><input type="text" style="width:60" name="th_sellprice" class="th_sellprice"></th>
							<th>적립금</th>
							<th>상태</th>
							<th>재고<br/><input type="text" style="width:40" name="th_quantity"></th>
							<th>옵션</th>
							<th>진열유무<br/><select name="th_display"><option value="">선택하세요</option><option value="Y">판매중</option>
																		<option value="N">보류중</option></select></th>
							<th>복사</th>
							<th>수정</th>
							<th>삭제</th>
						</tr>
		<?php
						$page_numberic_type=1;

						if ($likecode){
						//$qry= "AND b.c_category LIKE '{$likecode}%' ";
						$link_qry="select c_productcode from tblproductlink where c_category like '{$likecode}%' group by c_productcode";
						$link_result=pmysql_query($link_qry);
						while($link_data=pmysql_fetch_object($link_result)){
							$linkcode[]=$link_data->c_productcode;
						}

						$qry.= "AND a.productcode in ('".implode("','",$linkcode)."') ";

						}
						if ($s_keyword) $qry.= "AND (productname || productcode)LIKE '%{$s_keyword}%' ";
						if($s_check==1)	$qry.="AND (quantity is NULL OR quantity > 0) ";
						elseif($s_check==2){
							$qry.=" AND ( quantity <= 0 or option_quantity like '%,0,%' )";
//							$qry.=" AND option_quantity like '%,0,%' ";
						}
						if($display_yn=="Y")	$qry.="AND a.display='Y' ";
						elseif($display_yn=="N")	$qry.="AND a.display='N'";

						$qry.=" AND sabangnet_flag= 'N' ";

						if($vip==1)	$qry.=" AND vip_product= 1 ";
						elseif($vip==2)	$qry.=" AND vip_product= 0 ";
						//if($search_start && $search_end) $qry.="AND SUBSTRING(date from 1 for 8) between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
						if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
						if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}'";

						/*빠른조회*/
						if($code_area){
							$area_code = explode("\r\n",$code_area);

							if($code_type=="1"){
								$qry = "AND productcode in ('".implode("','",$area_code)."') ";
							}else{
								$qry = "AND sewon_option_no in ('".implode("','",$area_code)."') ";
							}
						}

						$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a  WHERE 1=1 ";
						$sql0.= $qry;
						if(!$listnum){
							$listnum = 20;
						}
						$paging = new newPaging($sql0,10,$listnum);
						$t_count = $paging->t_count;
						$gotopage = $paging->gotopage;

						$sql = "SELECT * FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode and c_maincate=1) WHERE 1=1 ";
						$sql.= $qry." ";

						$sql.= "ORDER BY regdate DESC ";

						$sql = $paging->getSql($sql);
						$result = pmysql_query($sql,get_db_conn());
						$cnt=0;
						while($row=pmysql_fetch_object($result)) {

						$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
						?>

						<tr>
						<td><input type="checkbox" name="code_check" class="code_check"></td>
						<td><?=$number?></td>

						<!--이미지-->
						<td>
						<?	if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){ ?>
						<a href="/front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank">
						<img src="<?=$imagepath.$row->tinyimage?>" style="width:100px" border=1></a>
						<?} else { ?>
						<img src=images/space01.gif>
						<?} ?>
						</td>
						<!--상품코드-->
						<td class="product_code"><?=$row->productcode?></td>
						
						<input type="hidden" name="product_code" value="<?=$row->productcode?>">
						<!--세원ERP코드-->
						<td>
							<?
								if($row->sewon_option_no){
									$arrOptionNameCnt = count(explode(",", $row->option1))-1;
									$arrSewonOptionCnt = count(array_filter(explode(",", $row->sewon_option_no)));
									if($arrOptionNameCnt == $arrSewonOptionCnt){
										$sewon = explode(',',$row->sewon_option_no); echo $sewon[0];
									}else{
										echo "없음";
									}
								}else{
									list($chkSabangnet, $temp_sewon_option_no)=pmysql_fetch("SELECT count(no), MAX(sewon_option_no) FROM tblproduct_sabangnet WHERE productcode = '".$row->productcode."' AND sewon_option_no != ''");
									list($sewoncodeCount)=pmysql_fetch("SELECT count(no) FROM tblproduct_sabangnet WHERE productcode = '".$row->productcode."' AND sewon_option_no = ''");
									if($chkSabangnet && !$sewoncodeCount){
										echo $temp_sewon_option_no;
									}else{
										echo "없음";
									}
								}
							?>
						</td>
						<!--상품명-->
						<td height="50"><p class="ta_l" style="text-align:center">
						<?if($row->vip_product == 1){ ?>
						<img src="img/icon/icon_vip.gif" border="0" style="margin-right:2px;">
						<?}?>
						<a href="javascript:ProductInfo('<?=$row->productcode?>','YES', '<?=$likecodeExchange?>');"> <!--YES를 NO로 바꾸면 팝업안됨-->
						<?=$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")?>
						</a></p></td>
						<!--등록일-->
						<td><?=substr($row->modifydate,0,10)?></td>
						<!--시중가-->
						<td style="text-align:right; padding-right:10px">
						<img src="images/won_icon.gif" border="0" style="margin-right:2px;">
						<span class="font_orange"><?=number_format($row->consumerprice)?></span></td>
						<!--판매가-->
						<td style="text-align:right; padding-right:10px"><img src="images/won_icon.gif" border="0" style="margin-right:2px;">
						<input type="text" name="sellprice" style="width:50" value="<?=number_format($row->sellprice)?>"></td>
						<!--적립율-->
						<td><img src="images/reserve_icon.gif" border="0" style="margin-right:2px;">
						<?=($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")?></td>
						<!--상태-->
						<td><?=($row->quantity=="0"?"품절":"재고")?></td>
						<!--재고-->
						<TD>
						<input type="text" name="quantity" style="width:40" value="<?if ($row->quantity<=0){ echo "품절";
																}else echo $row->quantity; ?>" />
						</td>
						<!--옵션재고-->
						<TD>
						<? 
						$option_quantity = explode(",",$row->option_quantity);
						$option1_arr = explode(",",$row->option1);
						$option_price_arr = explode(",",$row->option_price);
						$option_consumer_arr = explode(",",$row->option_consumer);
						$sewon_option_no_arr = explode(",",$row->sewon_option_no);
						$sewon_option_code1_arr = explode(",",$row->sewon_option_code1);
						$sewon_option_code2_arr = explode(",",$row->sewon_option_code2);
						
						for($cnt1=1;$cnt1<count($option1_arr);$cnt1++){
						?>
							옵션1 <input type="text" value="<?=$option1_arr[$cnt1]?>">
							재고 <input type="text" value="<?=$option_quantity[$cnt1]?>">
							시중가격 <input type="text" value="<?=$option_price_arr[$cnt1-1]?>">
							가격 <input type="text" value="<?=$option_consumer_arr[$cnt1-1]?>">
							세원ERP <input type="text" value="<?=$sewon_option_no_arr[$cnt1-1]?>">
							색상 <input type="text" value="<?=$sewon_option_code1_arr[$cnt1-1]?>">
							사이즈 <input type="text" value="<?=$sewon_option_code2_arr[$cnt1-1]?>">							
						<?}?>
					
						</td>
						
						<!--진열유무-->
						<td>
							<select name="display_select">
							<option value="Y" <?if($row->display=="Y") echo "selected";?>>판매중</option>
							<option value="N" <?if($row->display=="N") echo "selected";?>>보류중</option>
							</select>
						</td>
						<!--복사-->
						<td>
							<a href="javascript:Productcopy('<?=$row->productcode?>','H')"><img src="img/btn/btn_cate_copy1.gif" alt="제휴몰복사" /></a>
							<a href="javascript:Productcopy('<?=$row->productcode?>','N')"><img src="img/btn/btn_cate_copy2.gif" alt="자사몰복사" /></a>							
						</td>
						<!--수정-->
						<td><a href="javascript:ProductInfo('<?=$row->productcode?>','NO', '<?=$likecodeExchange?>');"><img src="img/btn/btn_cate_modify.gif" alt="수정" /></a></td>
						<!--삭제-->
						<td>
						<?
						if($row->sabangnet_flag == 'N'){
						?>
						<a href="javascript:ProductDel('<?=$row->productcode?>')"><img src="img/btn/btn_cate_del01.gif" alt="삭제" /></a>
						<?
						}else{
							echo "-";
						}
						?>
						</td>
						</tr>
						<?
						$cnt++;
						}
						if ($cnt==0) {
							$colspan='16';
							$page_numberic_type="";
							echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
						}?>
				</table>
			</div>

			<!--페이징-->
			<div id="page_navi01" style="height:'40px'">
				<div class="page_navi">
				<?if($page_numberic_type){?>
					<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
				<?}?>
				</div>
			</div>

			<!--하단 버튼-->
			<div class="btn_right">
			<a href="#"></a><img src="img/btn/btn_product_reg_all.gif" alt="일괄수정" class="edit"/></<a>
			<a href="javascript:registeradd();"><img src="img/btn/btn_product_reg.gif" alt="상품등록" /></a></div>


        	<table height="20"><tr><td> </td></tr></table>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span></span></dt>
						<dd>

						</dd>
					</dl>
				</div>
				</td>
			</tr>
			</form>
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

<form name=form_register action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
<input type=hidden name=category_data>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=s_keyword value="<?=$s_keyword?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=display_yn value="<?=$display_yn?>">
<input type=hidden name=vip value="<?=$vip?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=sellprice_min value="<?=$sellprice_min?>">
<input type=hidden name=sellprice_max value="<?=$sellprice_max?>">
<input type=hidden name=code_type value="<?=$code_type?>">
<input type=hidden name=code_area value="<?=$code_area?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<form name=form_modify action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=modcode>
<input type=hidden name=modsellprice>
<input type=hidden name=modquantity>
<input type=hidden name=moddisplay>

<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
<input type=hidden name=category_data>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=s_keyword value="<?=$s_keyword?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=display_yn value="<?=$display_yn?>">
<input type=hidden name=vip value="<?=$vip?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=sellprice_min value="<?=$sellprice_min?>">
<input type=hidden name=sellprice_max value="<?=$sellprice_max?>">
<input type=hidden name=code_type value="<?=$code_type?>">
<input type=hidden name=code_area value="<?=$code_area?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=mode2 value="N">
</form>

<script>

</script>
<?php
include("copyright.php");
?>
<?=$onload?>