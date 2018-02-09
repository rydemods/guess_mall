<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

//exdebug($_POST);
//exdebug($_GET);
//exit;

$idx = $_POST["idx"];
$counsel_type = $_POST["counsel_type"];
$mem_id = $_POST["id"];
$mode = $_POST["mode"];

if($mem_id) {
    list($mem_name) = pmysql_fetch("Select name From tblmember where id = '".$mem_id."'");
}

if($mode == "write") {

    $Query = "  INSERT INTO tblmember_question
                (
                 id, counsel_id, contents, regdt, counsel_type
                )
                VALUES
                (
                 '$mem_id', '$_POST[counsel_id]', '$_POST[contents]', '$_POST[regdt]', '$_POST[counsel_type]'
                )
		";
    if($insert = pmysql_query($Query)){
        echo "<script>alert(\"등록이 완료되었습니다.\");opener.location.reload();window.close();</script>";
        exit;
    }else{
        echo "<script>alert('등록이 실패 되었습니다.')</script>";
    }
}


if($mode=="update") {

    $Query = "  UPDATE tblmember_question SET
                id = '$_POST[id]', 
                counsel_id = '$_POST[counsel_id]', 
                contents = '$_POST[contents]', 
                regdt = '$_POST[regdt]', 
                counsel_type = '$_POST[counsel_type]'
                WHERE sno = '".$_POST[idx]."'
		";
    //exdebug($Query);
    if($insert = pmysql_query($Query)){
        echo "<script>alert(\"수정이 완료되었습니다.\");opener.location.reload();window.close();</script>";
        exit;
    }else{
        echo "<script>alert('수정이 실패 되었습니다.')</script>";
    }
} 


if($mode=="delete") {

    $Query = "  DELETE FROM tblmember_question WHERE sno = '".$_POST[idx]."'";
    //exdebug($Query);
    if($insert = pmysql_query($Query)){
        echo "<script>alert(\"삭제가 완료되었습니다.\");opener.location.reload();window.close();</script>";
        exit;
    }else{
        echo "<script>alert('삭제가 실패 되었습니다.')</script>";
    }
} 

##### 해당 메모 가져오기
if($idx) {
    $sql = "SELECT  sno, id, counsel_id, contents, regdt, counsel_type 
            FROM    tblmember_question  
            WHERE   counsel_type = '".$counsel_type."' 
            AND     sno = ".$idx." 
            ";
    //print_r($sql);
    $result=pmysql_query($sql,get_db_conn());
    $data=pmysql_fetch($result);
    if(!$data) {
        echo "<script>alert(\"해당 게시물이 존재하지 않습니다.\");window.close();</script>";
        exit;
    }

    $mem_id = $data['id'];
    $counsel_id = $data['counsel_id'];
	$contents = $data['contents'];
	$regdt = $data['regdt'];
	$counsel_type = $data['counsel_type'];

	$mode = "update";

    pmysql_free_result($result);
} else {

    $mem_id = $_POST["id"];
	$counsel_id = $_ShopInfo->id;
	$regdt = date('Y-m-d H:i:s');

	$mode = "write";
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>전화상담 메모</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = 600;
	var oHeight = 570;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm(form) {
	if(form.contents.length==0) {
		alert("내용을 입력하세요.");
		form.contents.focus();
		return;
	}
	form.submit();
}

function CheckDelete(form) {
	if(confirm("해당 메모를 삭제하시겠습니까?")) {
		form.mode.value="delete";
		form.submit();
	}
}

//-->
</SCRIPT>
</head>

<div class="pop_top_title"><p>전화상담 메모</p></div>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">

<TABLE WIDTH="328" BORDER=0 CELLPADDING=0 CELLSPACING=0>
<TR>
	<TD style="padding:6pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode value="<?=$mode?>">
	<input type=hidden name=idx value="<?=$idx?>">
    <input type=hidden name=counsel_type value="<?=$counsel_type?>">
    <input type=hidden name=id value="<?=$mem_id?>">
    <input type=hidden name=counsel_id value="<?=$counsel_id?>">
    <input type=hidden name=regdt value="<?=$regdt?>">
	<tr>
		<td width="100%">
        <div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="584" border=0>
		<col width = '20%'><col width = '*%'>
		<TR>
			<th><span>회원명</span></th>
			<TD class="td_con1"><B><span class="font_blue"><?=$mem_name?></B>(<?=$mem_id?>)</span></TD>
		</TR>
		<TR>
			<th><span>처리자 ID</span></th>
			<TD class="td_con1"><?=$counsel_id?></TD>
		</TR>
		<TR>
			<th><span>상담시간</span></th>
			<TD class="td_con1"><?=$regdt?></TD>
		</TR>
		<tr>
			<th><span>내용</span></th>
			<TD class="td_con1"><TEXTAREA style="width:95%;height:100" name=contents class="textarea"><?=$contents?></TEXTAREA></TD>
		</tr>
		</TABLE>
        </div>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center">
		<a href="javascript:CheckForm(document.form1);"><img src="images/btn_write1.gif" border="0" vspace="10" border=0></a>
		<a href="javascript:CheckDelete(document.form1);"><img src="images/btn_dela.gif"  border="0" vspace="10" border=0 hspace="2"></a>
		<a href="javascript:window.close()"><img src="images/btn_closea.gif" border="0" vspace="10" border=0 hspace="0"></a>
		</td>
	</tr>
	</form>
	</table>
	</TD>
</TR>
</TABLE>
</body>
</html>