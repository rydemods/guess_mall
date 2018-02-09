<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/libsms.php");
include_once($Dir."lib/lib.php");
include("access.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$idx=$_POST["idx"];
$mode=$_POST["mode"];
$re_content=$_POST["re_content"];
$re_subject=$_POST["re_subject"];
$type_mode=$_POST["type_mode"];
$re_id = $_ShopInfo->id;

$sql = "SELECT * FROM tblasinfo WHERE idx='{$idx}' ";
$result=pmysql_query($sql,get_db_conn());
$data=pmysql_fetch_object($result);
pmysql_free_result($result);
if(!$data) {
	echo "<script>alert(\"해당 게시물이 존재하지 않습니다.\");window.close();</script>";
	exit;
}

list($productname)=pmysql_fetch("SELECT productname FROM tblproduct WHERE productcode = '".$data->productcode."'");

if(ord($data->email)==0) $data->email="메일 입력이 안되었습니다.";
if(strlen($data->re_date)==14) $data->reply="<img src=\"images/icon_finish.gif\" border=\"0\">";
else $data->reply="<img src=\"images/icon_nofinish.gif\" border=\"0\">";

if($mode=="update" && ord($re_content)) {

    $re_content = pg_escape_string($re_content);
	$sql = "UPDATE tblasinfo SET ";
	$sql.= "re_date			= now(), ";
	$sql.= "re_subject		= '{$re_subject}', ";
	$sql.= "re_id				= '{$re_id}', ";
	$sql.= "re_content	= '{$re_content}', ";
	$sql.= "type_mode	= '{$type_mode}', ";
	$sql.= "re_name		= '{$_ShopInfo->name}', ";
	$sql.= "status		= 1 ";						// 답변완료 0: 최초 [as 접수] 1: 답변완료 2 : 관리자 삭제 
	$sql.= "WHERE idx='{$idx}' ";
	
// 	echo $sql;
// 	exit();
	
	pmysql_query($sql,get_db_conn());
//     echo "sql = ".$sql."<br>";

	if(ord($data->email) && $data->chk_mail == 'Y') {
		$info_email=$_shopdata->info_email;
		$shopname=$_shopdata->shopname;

		#$content = include("../templet/mail/personal001.php");
			/*
		$content = "##### {$data->name}님 {$shopname} 1:1 고객 문의에 대한 답변입니다. #####";
		$content.= "<br>\n\n";
		$content.= "보낸이 - {$shopname} (<a href=\"mailto:{$info_email}\">{$info_email}</a>)<br>\n";
		$content.= "#####################################################################\n";
		$content.= "<pre>{$re_content}</pre>\n";
		*/

		//오늘 날짜
		$curdate = date( "Y.m.d" );
		$curdate2 = date( "Y-m-d H:i:s" );
		
		//작성일
		$personaldate = substr($data->date,0,4).".".substr($data->date,4,2).".".substr($data->date,6,2);

		$shopurl=$_ShopInfo->getShopurl();


		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/personalTEM_001.php")) {
			$buffer = file_get_contents(DirPath.TempletDir."mail/personalTEM_001.php");
			$body=$buffer;
		}
		$pattern = array ("[SUBJECT]","[NAME]","[HP]","[EMAIL]","[TYPE]","[CONTENTS]","[RE_SUBJECT]","[RE_CONTENTS]","[CURDATE]","[CURDATE2]",'[PERSONALDATE]',"[SHOPURL]");
		$replace = array ($data->subject, $data->name, $data->HP, $data->email, $arrayCustomerHeadTitle[$data->head_title], nl2br($data->content), $re_subject, nl2br($re_content), $curdate,$curdate2,$personaldate,$shopurl);
		$body	 = str_replace($pattern,$replace,$body);
		$header="From: 핫티 <{$info_email}>\r\nContent-Type: text/html; charset=utf-8\r\n";
		sendmail($data->email, '=?utf-8?B?'.base64_encode("A/S 고객 문의에 대한 답변입니다.").'?=', $body, $header);

	}
	if(ord($data->HP) && $data->chk_sms == 'Y') {
		//SMS 발송
		sms_autosend( 'mem_personal', $data->id, $idx, $data->HP );
		//SMS 관리자 발송
		sms_autosend( 'admin_personal', $data->id, $idx, $data->HP );
		/*
		$sqlSms = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
		$resultSms=pmysql_query($sqlSms,get_db_conn());
		if($rowSms=pmysql_fetch_object($resultSms)){
			$return_tel = explode("-",$rowSms->return_tel);
			$sms_id=$rowSms->id;
			$sms_authkey=$rowSms->authkey;
		}
		pmysql_free_result($result);
		*/
		#$cnt=count(explode(",",$tel_list))<=$maxcount;
		/*
			SendSMS($shopid, $authkey, $totellist, $tonamelist, $fromtel, $date, $msg, $etcmsg) {
			SendSMS(smsID, sms인증키, 받는사람핸드폰, 받는사람명, 보내는사람(회신전화번호), 발송일, 메세지, etc메세지(예:개별 메세지 전송))
		*/
		#if($cnt <=$maxcount){
		//$etcmsg="1:1문의 답변 메세지 전송";
		//$temp=SendSMS($sms_id, $sms_authkey, $data->HP, "", $_shopdata->info_tel, 0, "문의 하신 1:1문의에 대한 답변이 등록되었습니다.", $etcmsg); 
		#$resmsg=explode("[SMS]",$temp);
		#echo "<script>alert('{$resmsg[1]}');</script>";
		#}else{
		#	echo "<script>alert('SMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.');</script>";
		#}
	}
	if( !pmysql_error() ){
		echo "<script>alert(\"해당 게시글에 대한 답변이 완료되었습니다.\");opener.location.reload();window.close();</script>";
		exit;
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}
} elseif ($mode=="delete") {
	$sql = "DELETE FROM tblasinfo WHERE idx='{$idx}' ";
	pmysql_query($sql,get_db_conn());
	echo "<script>alert(\"해당 게시글을 삭제하였습니다.\");opener.location.reload();window.close();</script>";
	exit;
}
?><html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>A/S 고객 게시판</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="../static/js/jquery-1.12.0.min.js"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">

document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
var oEditors = [];
$(document).ready( function() {
	nhn.husky.EZCreator.createInIFrame({
	    oAppRef: oEditors,
	    elPlaceHolder: "ir1",
	    sSkinURI: "../SE2/SmartEditor2Skin.html",
	    htParams : {
	        bUseToolbar : true,             // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
	        bUseVerticalResizer : true,     // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
	        bUseModeChanger : true,         // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
	        //aAdditionalFontList : aAdditionalFontSet,     // 추가 글꼴 목록
	        fOnBeforeUnload : function(){
	        }
	    },
	    fOnAppLoad : function(){
	    },
	    fCreator: "createSEditor2"
	        
	});

	$("#smart_editor2").css({
	     "min-width": "100px", 
	 });

});
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = 820;
	var oHeight = 680;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm(form) {
	if(form.re_content.length==0) {
		alert("답변 내용을 입력하세요.");
		form.re_content.focus();
		return;
	}
    var sHTML = oEditors.getById["ir1"].getIR();
    form1.re_content.value=sHTML;
	form.mode.value="update";
	form.submit();
}

function CheckDelete() {
	if(confirm("해당 게시글을 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.submit();
	}
}

</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>A/S 고객 게시판 문의내용 및 답변하기</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>A/S 고객 게시판 문의내용 및 답변하기</p></div>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">

<TABLE>
<TR>
	<TD style="padding:6pt;">
	<table cellpadding="0" cellspacing="0" width="920px">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=idx value="<?=$idx?>">
	<tr>
		<td width="100%">
        <div class="table_style01">
		<TABLE cellpadding="0" cellspacing="0" width="">
		<col width = '20%'><col width = '*%'>
		<TR>
			<th><span>회원명</span></th>
			<TD class="td_con1"><B><span class="font_blue"><a href="javascript:;" onClick="javascript:CrmView('<?=$data->id?>');"><?=$data->name?></B>(<?=$data->id?>)</span></a></TD>
		</TR>
		<TR>
			<th><span>제목</span></th>
			<TD class="td_con1"><?=$data->subject?></TD>
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
			<th><span>답변타입</span></th>
			<TD class="td_con1">휴대폰 : <?=$data->chk_sms?>  이메일 : <?=$data->chk_mail?></TD>
		</tr>
		<!-- 
		<tr>
			<th><span>답변여부</span></th>
			<TD class="td_con1"><?=$data->reply?>
        <?
        if(strlen($data->re_date)==14) {
        ?>
                <br> <span>답변자 : <?=$data->re_id." (".$data->re_writer.")"?></span>
        <?
        }
        ?>
            </TD>
		</tr>
		 -->
		 <tr>
		 	<th><span>상태변경</span></th>
		 	<TD class="td_con1">
		 		<input type="radio" id="review_class1"  name="type_mode" value="AS접수" <? if ( $data->type_mode == "AS접수" ) { echo "checked"; } ?>/> <label for=review_class1>AS접수</label>&nbsp;
                <input type="radio" id="review_class2"  name="type_mode" value="제품도착" <? if ( $data->type_mode == "제품도착" ) { echo "checked"; } ?> /> <label for=review_class2>제품도착</label>&nbsp;
                <input type="radio" id="review_class3"  name="type_mode" value="심의중" <? if ( $data->type_mode == "심의중" ) { echo "checked"; } ?> /> <label for=review_class3>심의중</label>&nbsp;
                <input type="radio" id="review_class4"  name="type_mode" value="수선중" <? if ( $data->type_mode == "수선중" ) { echo "checked"; } ?> /> <label for=review_class4>수선중</label>&nbsp;
                <input type="radio" id="review_class5"  name="type_mode" value="수선완료" <? if ( $data->type_mode == "수선완료" ) { echo "checked"; } ?> /> <label for=review_class5>수선완료</label>&nbsp;
                <input type="radio" id="review_class6"  name="type_mode" value="고객발송" <? if ( $data->type_mode == "고객발송" ) { echo "checked"; } ?> /> <label for=review_class6>고객발송</label>&nbsp;
                <input type="radio" id="review_class7"  name="type_mode" value="AS반품처리" <? if ( $data->type_mode == "AS반품처리" ) { echo "checked"; } ?> /> <label for=review_class7>AS반품처리</label>&nbsp;
               	<input type="radio" id="review_class8"  name="type_mode" value="반품처리" <? if ( $data->type_mode == "반품처리" ) { echo "checked"; } ?> /> <label for=review_class8>반품처리</label>&nbsp;
               	<input type="radio" id="review_class9"  name="type_mode" value="교환처리" <? if ( $data->type_mode == "교환처리" ) { echo "checked"; } ?> /> <label for=review_class9>교환처리</label>&nbsp;
		 	</TD>
		 </tr>
		<tr>
			<th><span>내용</span></th>
			<TD class="td_con1"><?=nl2br($data->content)?></TD>
		</tr>
		<tr>
			<th><span>답변 제목</span></th>
			<TD class="td_con1">
				<p align="left"><INPUT wrap=off  maxLength=200 size=70 name=re_subject value="<?=$data->re_subject?>" style="width:100%" class="input">
			</TD>
		</tr>
		<tr>
			<th><span>답변 내용</span></th>
			<TD class="td_con1"><TEXTAREA style="width:95%;height:205" id="ir1" name=re_content class="textarea"><?=$data->re_content?></TEXTAREA></TD>
		</tr>
		<tr>
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
		</tr>
		</TABLE>
        </div>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center">
		<a href="javascript:CheckForm(document.form1);"><img src="images/btn_write1.gif" border="0" vspace="10" border=0></a>
		<a href="javascript:CheckDelete();"><img src="images/btn_dela.gif"  border="0" vspace="10" border=0 hspace="2"></a>
		<a href="javascript:window.close()"><img src="images/btn_closea.gif" border="0" vspace="10" border=0 hspace="0"></a>
		</td>
	</tr>
	</form>
	</table>
	</TD>
</TR>
</TABLE>

<form name=crmview method="post" action="crm_view.php">
<input type=hidden name=id>
</form>

</body>
</html>
