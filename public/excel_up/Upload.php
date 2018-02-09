<?	

	require_once("../config/db_connect.php");
	require_once("../config/function.php");



?>
<html>
<head>
<title>주문 업로드</title>

<script type="text/JavaScript">



function go_submit(){
	//alert(document.excelform.mallName.value);
	
	document.excelform.action="view_qnaup.php";
	document.excelform.submit();
}

function go_submit2(){
	//alert(document.excelform.mallName.value);
	
	document.excelform.action="view_qnaup2.php";
	document.excelform.submit();
}

function go_submit3(){
	//alert(document.excelform.mallName.value);
	
	document.excelform.action="view_qnaup3.php";
	document.excelform.submit();
}

function go_submit4(){
	//alert(document.excelform.mallName.value);
	
	document.excelform.action="view_qnaup4.php";
	document.excelform.submit();
}

function go_submit5(){
	//alert(document.excelform.mallName.value);
	
	document.excelform.action="view_qnaup5.php";
	document.excelform.submit();
}

function go_submit6(){
	//alert(document.excelform.mallName.value);
	
	document.excelform.action="view_qnaup6.php";
	document.excelform.submit();
}
function go_submit7(){
	//alert(document.excelform.mallName.value);
	
	document.excelform.action="../goods/socialExcel.php";
	document.excelform.submit();
}
function go_submitst(){
	document.stockform.action="/WDERP/sales/stockIns.php";
	document.stockform.submit();
}

function go_submitce(){
	document.cancleform.action="/WDERP/sales/cancleIns.php";
	document.cancleform.submit();
}

function go_del(str){

	if(confirm('삭제하시겠습니까')){

	document.location.href="/WDERP/sales/cancleDel.php?mall="+str;

	}
}


function go_submitbad(){
	document.stockform1.action="/WDERP/sales/stockbadIns.php";
	document.stockform1.submit();
}



function enter() {
 if(event.keyCode==13){
	 return alert('1');
 }
}

function go_orderinSearch(){

	window.open('orderinSearch.php','popup','fullscreen=no,menubar=no,status=no,toolbar=no,titlebar=no,location=no,scrollbars=yes,top=250,left=600,width=1100,height=700');
}
</script>
<script language="JavaScript" src="/WDERP/js/selectbox.js"></script>
</head>

<body onLoad=>




<form name="excelform" method="post" enctype="multipart/form-data">
<table>
<?/*?>
<tr>
신규상품 DB업로드	<input type="file" name="excelfile">
<!--	<input type="submit" value="업로드">-->
	<input type="button" value="업로드" onclick="javascript:go_submit()">
</tr>

<tr>
신규상품 추가업로드	<input type="file" name="excelfile">
<!--	<input type="submit" value="업로드">-->
	<input type="button" value="업로드" onclick="javascript:go_submit2()">


</tr>

<tr>
레더맨 상품명 업데이트 <input type="file" name="excelfile">
<!--	<input type="submit" value="업로드">-->
	<input type="button" value="업로드" onclick="javascript:go_submit3()">


</tr>
<?*/?>
<tr>
모델명 업로드 <input type="file" name="excelfile">
<!--	<input type="submit" value="업로드">-->
	<input type="button" value="업로드" onclick="javascript:go_submit4()">


</tr>
<?/*?>
<tr>
주문내역 삽입 <input type="file" name="excelfile">
<!--	<input type="submit" value="업로드">-->
	<input type="button" value="업로드" onclick="javascript:go_submit5()">


</tr>

주문내역 업데이트2 <input type="file" name="excelfile">
<!--	<input type="submit" value="업로드">-->
	<input type="button" value="업로드" onclick="javascript:go_submit6()">


</tr>


</tr>

엑셀 임시다운
<!--	<input type="submit" value="업로드">-->
	<input type="button" value="업로드" onclick="javascript:go_submit7()">


</tr>
<?*/?>
</table>
</form>



</body>
</html>
