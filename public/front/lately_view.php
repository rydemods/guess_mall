<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$listnum = $_GET['listnum'] ?: 10;

include ($Dir.MainDir.$_data->menu_type.".php") 
?>

<SCRIPT LANGUAGE="JavaScript">
<!--

// 전체상품 삭제 
function AllDelete() {
    
	var sa = true;
	var form = document.form1;
	document.getElementById('all_check').checked = true;

	for (var i=0;i<form.elements.length;i++) {
		var e = form.elements[i];
		if(e.type.toUpperCase()=="CHECKBOX" && e.name=="idx[]") {
			e.checked = true;
		}
	}
	form.flag.value = 0;

    GoDelete();
}

function CheckBoxAll() {
	var sa = true;
	var form = document.form1;

	if(form.flag.value==1) sa = false;

	for (var i=0;i<form.elements.length;i++) {
		var e = form.elements[i];
		if(e.type.toUpperCase()=="CHECKBOX" && e.name=="idx[]") {
			if(sa)
				e.checked = false;
			else
				e.checked = true;
		}
	}

	if(form.flag.value == 1) {
		form.flag.value = 0;
	} else{
		form.flag.value = 1;
	}
}

function GoCart() {

	chk_ok=1;

	if($("input[name='idx[]']:checked").length>0){

		$("input[name='idx[]']:checked").each(function(){
			
			if($(this).attr('class')!='cart_ok'){
				$(this).prop('checked',false);
				chk_ok=0;
			}
		});

		if(chk_ok==0){
			alert('패키지, 코디 상품은 각 상품 상세에서 구성상품을 선택 후 장바구니에 담아주세요.');
		}

		document.form1.action="confirm_cart_wishlist.php";
		document.form1.submit();
	}else{
		alert("선택된 상품이 없습니다.");
	}
}

function GoDelete() {
	var form = document.form1;
	var issel=false;
	for (var i=0;i<form.elements.length;i++) {
		var e = form.elements[i];
		if(e.type.toUpperCase()=="CHECKBOX" && e.name=="idx[]") {
			if(e.checked) {
				issel=true;
				break;
			}
		}
	}
	if(!issel) {
		alert("삭제할 상품을 선택하세요.");
		return;
	}
	if(confirm("삭제하시겠습니까?")) {
		form.mode.value="delete";
		form.submit();
	}
}

function delete_view(idx) {
	var form = document.form1;

	if(confirm("삭제하시겠습니까?")) {
        form.del_item.value = idx;
		form.mode.value="delete";
		form.submit();
	}
}

function SaveMemo(idx) {
	try {
		document.memoform.mode.value="memo";
		document.memoform.wish_idx.value=idx;
		document.memoform.up_marks.value=document.form1["up_marks_"+idx].value;
		document.memoform.up_memo.value=document.form1["up_memo_"+idx].value;
		document.memoform.submit();
	} catch(e) {}
}

function ChangeSort(val) {
	document.form3.block.value="";
	document.form3.gotopage.value="";
	document.form3.sort.value=val;
	document.form3.submit();
}

function ChangeListnum(val) {
	document.form3.block.value="";
	document.form3.gotopage.value="";
	document.form3.listnum.value=val;
	document.form3.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
//-->
</SCRIPT>

<?
include ($Dir.TempletDir."mypage/lately_view{$_data->design_mypage}.php");
?>

<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=listnum value="<?=$listnum?>">
</form>

<form name=form3 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
