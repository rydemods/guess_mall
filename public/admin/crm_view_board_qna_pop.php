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
$mode = $_POST["mode"];
$re_content = $_POST["re_content"];

$sql = "SELECT  a.pridx, a.title, a.filename, to_char(to_timestamp(a.writetime), 'YYYYMMDDHH24MISS') as regdt, a.total_comment, a.content, a.mem_id, b.email, b.name 
        FROM    tblboard a 
        JOIN    tblmember b ON a.mem_id = b.id 
        WHERE   a.board = 'qna' 
        AND     a.num = ".$idx." ";
//print_r($sql);
$result=pmysql_query($sql,get_db_conn());
$data=pmysql_fetch_object($result);
pmysql_free_result($result);
if(!$data) {
	echo "<script>alert(\"해당 게시물이 존재하지 않습니다.\");window.close();</script>";
	exit;
}

list($productname)=pmysql_fetch("SELECT productname FROM tblproduct WHERE pridx = '".$data->pridx."'");

if(ord($data->email)==0) $data->email="메일 입력이 안되었습니다.";
if($data->total_comment > 0) $data->reply="<img src=\"images/icon_finish.gif\" border=\"0\">";
else $data->reply="<img src=\"images/icon_nofinish.gif\" border=\"0\">";

if($mode=="update" && ord($re_content)) {

    $sql = "INSERT INTO tblboardcomment DEFAULT VALUES RETURNING num";
	$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
	$sql  = "UPDATE tblboardcomment SET ";
	$sql.= "board		= 'qna', ";
	$sql.= "parent		= '{$idx}', ";
	$sql.= "name		= '관리자', ";
	$sql.= "passwd		= '{$setup['passwd']}', ";
	$sql.= "ip			= '{$_SERVER['REMOTE_ADDR']}', ";
	$sql.= "writetime	= '".time()."', ";
	$sql.= "comment		= '{$re_content}' WHERE num={$row2[0]}";
	$insert = pmysql_query($sql,get_db_conn());

	// 코멘트 갯수를 구해서 정리
	$total=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) FROM tblboardcomment WHERE board='qna' AND parent='{$idx}'",get_db_conn()));
	pmysql_query("UPDATE tblboard SET total_comment='{$total[0]}' WHERE board='qna' AND num='{$idx}'",get_db_conn());

    // ================================================================================================================
    // 상품문의에 답변이 달린 경우, 메일 발송
    // ================================================================================================================
    SendQnaMail($_data->shopname, $_ShopInfo->getShopurl(), $_data->design_mail, $_data->info_email, 'qna', $idx);

	if(ord($data->HP) && $data->chk_sms == 'Y') {
		$sqlSms = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
		$resultSms=pmysql_query($sqlSms,get_db_conn());
		if($rowSms=pmysql_fetch_object($resultSms)){
			$return_tel = explode("-",$rowSms->return_tel);
			$sms_id=$rowSms->id;
			$sms_authkey=$rowSms->authkey;
		}
		pmysql_free_result($result);


		#$cnt=count(explode(",",$tel_list))<=$maxcount;
		/*
			SendSMS($shopid, $authkey, $totellist, $tonamelist, $fromtel, $date, $msg, $etcmsg) {
			SendSMS(smsID, sms인증키, 받는사람핸드폰, 받는사람명, 보내는사람(회신전화번호), 발송일, 메세지, etc메세지(예:개별 메세지 전송))
		*/
		#if($cnt <=$maxcount){
		$etcmsg="상품 Q&A 답변 메세지 전송";
		$temp=SendSMS($sms_id, $sms_authkey, $data->HP, "", $_shopdata->info_tel, 0, "문의 하신 상품 Q&A에 대한 답변이 등록되었습니다.", $etcmsg); 
		#$resmsg=explode("[SMS]",$temp);
		#echo "<script>alert('{$resmsg[1]}');</script>";
		#}else{
		#	echo "<script>alert('SMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.');</script>";
		#}
	}

	echo "<script>alert(\"해당 게시글에 대한 답변이 완료되었습니다.\");opener.location.reload();window.close();</script>";
	exit;

} elseif ($mode=="delete") {

	$sql = "DELETE FROM tblboard WHERE board = 'qna' and num = '{$idx}' ";
	pmysql_query($sql,get_db_conn());
    
    $sql = "DELETE FROM tblboardcomment where board = 'qna' and parent = '{$idx}' ";
    pmysql_query($sql,get_db_conn());

	echo "<script>alert(\"해당 게시글을 삭제하였습니다.\");opener.location.reload();window.close();</script>";
	exit;
}
?><html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>상품 Q&A 게시판</title>
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
	if(form.re_content.length==0) {
		alert("답변 내용을 입력하세요.");
		form.re_content.focus();
		return;
	}
	form.mode.value="update";
	form.submit();
}

function CheckDelete() {
	if(confirm("해당 게시글을 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.submit();
	}
}
//-->
</SCRIPT>
</head>

<div class="pop_top_title"><p>상품 Q&A 게시판</p></div>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">

<TABLE WIDTH="328" BORDER=0 CELLPADDING=0 CELLSPACING=0>
<TR>
	<TD style="padding:6pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=idx value="<?=$idx?>">
	<tr>
		<td width="100%">
        <div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="584" border=0>
		<col width = '20%'><col width = '*%'>
		<TR>
			<th><span>회원명</span></th>
			<TD class="td_con1"><B><span class="font_blue"><?=$data->name?></B>(<?=$data->mem_id?>)</span></TD>
		</TR>
		<TR>
			<th><span>제목</span></th>
			<TD class="td_con1"><?=$data->title?></TD>
		</TR>
		<?if($productname){?>
		<TR>
			<th><span>문의상품</span></th>
			<TD class="td_con1"><?=$productname?></TD>
		</TR>
		<?}?>
		<TR>
			<th><span>메일</span></th>
			<TD class="td_con1"><a href="mailto:<?=$data->email?>"><?=$data->email?></a></TD>
		</TR>
		<tr>
			<th><span>답변여부</span></th>
			<TD class="td_con1"><?=$data->reply?></TD>
		</tr>
		<tr>
			<th><span>내용</span></th>
			<TD class="td_con1"><?=nl2br($data->content)?></TD>
		</tr>
		<!-- <tr>
			<th><span>답변 제목</span></th>
			<TD class="td_con1">
				<p align="left"><INPUT maxLength=200 size=70 name=re_subject value="<?=$data->re_subject?>" style="width:100%" class="input">
			</TD>
		</tr> -->
<?
if($data->total_comment > 0) {
    $repl_sql = "select comment from tblboardcomment where board = 'qna' and parent = ".$idx." ";
    $repl_ret = pmysql_query($repl_sql);
    $i = 0;
    while($repl_row = pmysql_fetch_object($repl_ret)) {
        $i++;
?>
		<tr>
			<th><span>답변 내용 <?=$i?></span></th>
			<TD class="td_con1"><TEXTAREA style="width:95%;height:100" name=re_content class="textarea"><?=$repl_row->comment?></TEXTAREA></TD>
		</tr>
<?
    }
} else {
?>
		<tr>
			<th><span>답변 내용</span></th>
			<TD class="td_con1"><TEXTAREA style="width:95%;height:205" name=re_content class="textarea"><?=$data->re_content?></TEXTAREA></TD>
		</tr>
<?
}
?>
		<!-- <tr>
			<th><span>첨부파일</span></th>
			<TD class="td_con1">
			<?
			if ($data->up_filename) {
				echo "<img src='".$Dir.DataDir."shopimages/personal/".$data->up_filename."' style='max-width:430px;'>";
			} else {
				echo "첨부파일 없음";
			}

			?>
			</TD>
		</tr> -->
		</TABLE>
        </div>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center">
<?
if($data->total_comment == 0) {
?>
		<a href="javascript:CheckForm(document.form1);"><img src="images/btn_write1.gif" border="0" vspace="10" border=0></a>
<?
}
?>
		<a href="javascript:CheckDelete();"><img src="images/btn_dela.gif"  border="0" vspace="10" border=0 hspace="2"></a>
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