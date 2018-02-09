<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("calendar.php");
?>
<?
//exdebug($_POST);
//exit;

//exdebug($_ShopInfo);
$productcode = $_REQUEST['productcode'];
$mode = $_POST['mode'];
if($mode == 'insert'){
	$id = $_POST['user_id'];
	$name = $_POST['user_name'];
    $regdt = str_replace("-", "", $_POST['regdt']).date("His");
	$marks = (int)$_POST['rate'];
	//$best_type = $_POST['best_type'];
	$subject = $_POST['subject'];
	$content = $_POST['content'];
	$date = date('Ymdhis');
    $review_size = $_POST['review_size'];
    $review_foot_width = $_POST['review_foot_width'];
    $review_color = $_POST['review_color'];
    $review_quality = $_POST['review_quality'];
?>
<?
	##############파일처리#################
	$imagepath2=$Dir.DataDir."shopimages/review/";
	$userfile = $_FILES["rfile"];
	$up_rfile = "";

	if ($userfile['tmp_name']) {
		$ext = strtolower(pathinfo($userfile["name"], PATHINFO_EXTENSION));
		$uploadFile = md5($userfile ["name"]).".".$ext;
        $up_rfile = $userfile["name"];
		if(in_array($ext,array('gif','jpg','jpeg','bmp'))) {
			//$uploadFile = time().".".$ext;
			move_uploaded_file($userfile["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/data/shopimages/review/".$uploadFile);
			chmod($imagepath2.$uploadFile,0664);
		} else {
			alert_go("gif와  jpg타입의 이미지만 업로드 가능합니다.", "{$_SERVER['HTTP_REFERER']}");
			$uploadFile = "";
		}

		if ($userfile["size"] > 838860){
		    alert_go('첨부 파일이 800K를 초과합니다.', "{$_SERVER['HTTP_REFERER']}");
		}
		//$uploadFile = $userfile ["name"];
	}##############################################

	$rv_type	= $uploadFile?"1":"0";

	####################리뷰등록하기##################
	$sql .= "INSERT INTO tblproductreview (
		upfile,
        up_rfile, 
		productcode	,
		id		,
		name		,
		marks		,
		date		,
		type		,
		subject		,
		content, 
        size, 
        deli, 
        color, 
        quality		)  ";
	$sql .= " VALUES (
		'{$uploadFile}',
        '{$up_rfile}',
		'{$productcode}',
		'".$id."',
		'{$name}',
		'{$marks}',
		'{$regdt}',
		'{$rv_type}',
		'".$subject."',
		'{$content}', 
         {$review_size}, 
         {$review_foot_width}, 
         {$review_color}, 
         {$review_quality})" ;
	//exdebug($sql);
	if( $result = pmysql_query($sql,get_db_conn())){
		$pr_sql = "UPDATE tblproduct SET review_cnt = review_cnt + 1 WHERE productcode ='".$productcode."'";
		pmysql_query( $pr_sql, get_db_conn() );
		echo "<script>alert('등록되었습니다'); window.close();</script>";	
	}else{
		echo "<script>alert('등록실패! 관리자에게 문의하세요'); window.close();</script>";	
	}
	##############################################
}//mode insert end ㅠㅠ



?>

<html>
<head>
<script src="../js/jquery-1.10.1.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">

function PageResize() {
	var oWidth = 650;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {

    if(document.form1.subject.value == "") {
        alert("제목을 입력해 주십시오.");
        return;
    }

    if(document.form1.content.value == "") {
        alert("내용을 입력해 주십시오.");
        return;
    }

	if (confirm("해당 상품리뷰를 현재 정보로 저장 하시겠습니까?")) {
		document.form1.mode.value ='insert';
		document.form1.submit();
	}
}

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#blah').attr('src', e.target.result);
		}
	reader.readAsDataURL(input.files[0]);
	}
    //alert("1");
    setTimeout(function(){
        PageResize();
    }, 500);
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<title>[상품명]에 대한 리뷰 등록</title>
<div class="pop_top_title"><p>[상품명]에 대한 리뷰 등록</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">
<form name=form1 method=post action='<?$_SEVER['PHP_SELF']?>' enctype = 'multipart/form-data'>
<input type="hidden" name='mode'>
<input type="hidden" name='productcode' value="<?=$productcode?>">
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id="table_body">
<TR>
	<TD background="images/member_zipsearch_bg.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="18"></td>
		<td></td>
		<td width="18" height=10></td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
            <div class="table_style01">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<colgroup>
				<col width=200>
				<col width=>
			</colgroup>
			<TR>
				<th><span>아이디</span></th>
				<TD class="td_con1"><B><input type="text" name="user_id" value="<?=$_ShopInfo->id?>"></B></TD>
			</TR>
			<TR>
				<th><span>이름</span></th>
				<TD class="td_con1"><B><input type="text" name="user_name"></B></TD>
			</TR>
            <TR>
				<th><span>등록일</span></th>
				<TD class="td_con1"><input name="regdt" title="년도를 입력하세요." onclick="Calendar(event)" type="text" size="12" maxlength="12" label="등록년월일" class="input" style="height:25px"></TD>
			</TR>
			<!-- <TR>
				<th><span>평점</span></th>
				<TD class="td_con1"><SPAN class="font_orange"><B>
				
						<select name='rate'>
						<option value='5' >★★★★★</option>
						<option value='4' >★★★★☆</option>
						<option value='3' >★★★☆☆</option>
						<option value='2' >★★☆☆☆</option>
						<option value='1' >★☆☆☆☆</option>
						</select>
			
				</B></SPAN></TD>
			</TR> -->

			<TR>
				<th><span>사이즈</span></th>
				<TD class="td_con1"><SPAN class="font_orange"><B>
	

					<input type="radio" value="1" name="review_size" id="review_size-2">불만
			        <input type="radio" value="2" name="review_size" id="review_size-1">조금 불만
                    <input type="radio" value="3" name="review_size" id="review_size0" checked>보통
                    <input type="radio" value="4" name="review_size" id="review_size1">조금 만족
                    <input type="radio" value="5" name="review_size" id="review_size2">만족
				</B></SPAN></TD>
			</TR>

			<TR>
				<th><span>색상</span></th>
				<TD class="td_con1"><SPAN class="font_orange"><B>
					<input type="radio" value="1" name="review_color" id="color-2">불만
			        <input type="radio" value="2" name="review_color" id="color-1">조금 불만
                    <input type="radio" value="3" name="review_color" id="color0" checked>보통
                    <input type="radio" value="4" name="review_color" id="color1">조금 만족
                    <input type="radio" value="5" name="review_color" id="color2">만족
				</B></SPAN></TD>
			</TR>

			<TR>
				<th><span>배송</span></th>
				<TD class="td_con1"><SPAN class="font_orange"><B>
					<input type="radio" value="1" name="review_foot_width" id="foot_width-2">불만
			        <input type="radio" value="2" name="review_foot_width" id="foot_width-1">조금 불만
                    <input type="radio" value="3" name="review_foot_width" id="foot_width0" checked>보통
                    <input type="radio" value="4" name="review_foot_width" id="foot_width1">조금 만족
                    <input type="radio" value="5" name="review_foot_width" id="foot_width2">만족
				</B></SPAN></TD>
			</TR>


			<TR>
				<th><span>품질/만족도</span></th>
				<TD class="td_con1"><SPAN class="font_orange"><B>
					<input type="radio" value="1" name="review_quality" id="quality-2">불만
			        <input type="radio" value="2" name="review_quality" id="quality-1">조금 불만
                    <input type="radio" value="3" name="review_quality" id="quality0" checked>보통
                    <input type="radio" value="4" name="review_quality" id="quality1">조금 만족
                    <input type="radio" value="5" name="review_quality" id="quality2">만족
				</B></SPAN></TD>
			</TR>

			<!-- <tr>
				<th><span>베스트</span></th>
				<TD class="td_con1">
					<input type="checkbox" value="1" name="best_type" <?if($row->best_type){echo "checked";}?>>선택					
				</td>
			</tr> -->

			<TR>
				<th><span>제목</span></th>
				<TD class="td_con1"><input type="text" name="subject" style="width:90%"></TD>
			</TR>

			<TR>
				<th>이미지 첨부</th>
				<TD>
				<div id="file_area" ondrop="drop(ev);" ondragover="allowDrop(ev);">
					<img id="blah" src="#" alt="your image" onError="this.src='../images/no_img.gif'" width="200px"><br>
					<input type='file' name="rfile" id="img_file" onchange="readURL(this);" />
				</div>
				</TD>
			</TR>
			<TR>
				<th><span>내용</span></th>
				<TD class="td_con1"><textarea name="content" style="width:100%;height:120;word-break:break-all;" class="textarea"><?=$reviewcontent[0]?></textarea></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		</table>
		</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td align="center"><a href="javascript:CheckForm();"><img src="images/btn_save.gif" border="0" vspace="5" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif"  border="0" vspace="5" border=0 hspace="2"></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
</form>
</body>
</html>
