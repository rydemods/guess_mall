<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."lib/recipe.class.php");

$recipe = new RECIPE();

$no=$_REQUEST[no];
$data = $recipe->getRecipeDetail($no);
$product_list = $recipe->getRecipeProductList($no);

$param[code] = $_REQUEST[code];
$param[search_field] = $_REQUEST[search_field];
$param[search_word] = $_REQUEST[search_word];
$recipe->setSearch($param);
$other = $recipe->getRecipeOtherList($no);


//상품QNA 게시판 존재여부 확인 및 설정정보 확인
$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
if(ord($prqnaboard)) {
	$sql = "SELECT * FROM tblboardadmin WHERE board='{$prqnaboard}' ";
	$result=pmysql_query($sql,get_db_conn());
	$qnasetup=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($qnasetup->use_hidden=="Y") $qnasetup=null;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shopname." [{$data[subject]}]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<link rel="stylesheet" type="text/css" href="../../css/tem_001.css" media="all" />
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/drag.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.Tem001.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
}

function ChangeSort(val,type) {
	
	if(type)document.form2.listnum.value=document.getElementById("listnum").value;
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	document.form2.submit();
}

function ChangeListnum(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.listnum.value=val;
	document.form2.submit();
}

function GoPage(page_no) {
	document.list.page_no.value=page_no;
	document.list.submit();
}

function cate_change(cate){
	code_a="";
	code_b="";
	code_c="";
	code_d="";
	if(cate=="a"){
		code_a=document.getElementById("code_a").value;
	}else if(cate=="b"){
		code_a=document.getElementById("code_a").value;
		code_b=document.getElementById("code_b").value;
	}else if(cate=="c"){
		code_a=document.getElementById("code_a").value;
		code_b=document.getElementById("code_b").value;
		code_c=document.getElementById("code_c").value;
	}else if(cate=="d"){
		code_a=document.getElementById("code_a").value;
		code_b=document.getElementById("code_b").value;
		code_c=document.getElementById("code_c").value;
		code_d=document.getElementById("code_d").value;
	}
	
		
	location.href="productlist.php?code="+code_a+code_b+code_c+code_d;
}

function list_change(listsort){
	listnum=document.getElementById("listnum").value;
	code_a=document.getElementById("code_a").value;
	code_b=document.getElementById("code_b").value;
	code_c=document.getElementById("code_c").value;
	code_d=document.getElementById("code_d").value;
	
	location.href="productlist.php?code="+code_a+code_b+code_c+code_d+"&listnum="+listnum;
	
}


var view_qnano="";
function view_qnacontent(idx) {
	if (idx=="W") {	//쓰기권한 없음
		alert("상품Q&A 게시판 문의 권한이 없습니다.");
	} else if(idx=="N") {	//일기권한 없음
		alert("해당 Q&A게시판 게시글을 보실 수 없습니다.");
	} else if(idx=="S") {	//잠금기능 설정된 글
		if(view_qnano.length>0 && view_qnano!=idx) {
			document.all["qnacontent"+view_qnano].style.display="none";
		}
		alert("해당 문의 글은 잠금기능이 설정된 게시글로\n\n직접 게시판에 가셔서 확인하셔야 합니다.");
	} else if(idx=="D") {
		if(view_qnano.length>0 && view_qnano!=idx) {
			document.all["qnacontent"+view_qnano].style.display="none";
		}
		alert("작성자가 삭제한 게시글입니다.");
	} else {
		try {
			if(document.all["qnacontent"+idx].style.display=="none") {
				view_qnano=idx;
				document.all["qnacontent"+idx].style.display="";
			} else {
				document.all["qnacontent"+idx].style.display="none";
			}
		} catch (e) {
			alert("오류로 인하여 게시내용을 보실 수 없습니다.");
		}
	}
}

//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<?
$lnb_flag = 2;
include ($Dir.MainDir."lnb.php");
?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>
<?php 
//	$param[page_no] = $_REQUEST[page_no];
//	$param[list_size] = 15;
//	$recipe->setSearch($param);
	include $Dir.TempletDir."recipe/view.php";

?>
	</td>
</tr>
<form name="list" method=get action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="no" value="<?=$_REQUEST[no]?>">
	<input type="hidden" name="page_no" value="">
	<input type="hidden" name="listUrl" value="<?=$_REQUEST[listUrl]?>">
	<input type="hidden" name="code" value="<?=$_REQUEST[code]?>">
	<input type="hidden" name="search_word" value="<?=$_REQUEST[search_word]?>">
	<?if(is_array($_REQUEST[search_field])){foreach($_REQUEST[search_field]as $v){?>
	<input type="hidden" name="search_field[]" value="<?=$v?>">
	<?}}?>
</form>
</table>


<?
	if($biz[bizNumber]){
?>
<script type="text/javascript">
	_TRK_PI = "PLV"; 
</script>
<?
	}
?>


<div id="create_openwin" style="display:none"></div>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
<?php  if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>
