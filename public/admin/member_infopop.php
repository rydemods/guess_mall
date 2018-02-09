<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$id=$_POST["id"];
$mode=$_POST["mode"];

if(ord($_ShopInfo->getId())==0 || ord($id)==0){
	echo "<script>window.close();</script>";  
	exit;
}
$recommand_type=$_shopdata->recom_ok;
$member_addform=$_shopdata->member_addform;

$sql = "SELECT * FROM tblmember WHERE id='{$id}' "; 
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) { 
	if($row->member_out=="Y") {
		echo "<script>window.close();</script>";
		exit;
	}
	################## 회원 그룹 쿼리 ################
	$groupname='';

	$group_qry="select group_name,group_code from tblmembergroup where group_code='{$row->group_code}'";
	$group_result=pmysql_query($group_qry);
	$group_data=pmysql_fetch_object($group_result);
	###################################################

	################## 총 구매금액 쿼리(도매) ################
	$sum_sql = "SELECT sum(price) as sumprice FROM sales.tblorderinfo ";
	$sum_sql.= "WHERE id = '{$id}' AND deli_gbn = 'Y'";
	$sum_result = pmysql_query($sum_sql,get_db_conn());
	$sum_data=pmysql_fetch_object($sum_result);
	###################################################

	################## 총 구매금액 쿼리(소매) ################
	$sum_sql2 = "SELECT sum(price) as sumprice FROM tblorderinfo ";
	$sum_sql2.= "WHERE id = '{$id}' AND deli_gbn = 'Y'";
	$sum_result2 = pmysql_query($sum_sql2,get_db_conn());
	$sum_data2=pmysql_fetch_object($sum_result2);
	###################################################


	$sumprice=$sum_data->sumprice+$sum_data2->sumprice;
	$group_name=$group_data->group_name;

	$name=$row->name;
	$nickname=$row->nickname;
	$birth=$row->birth;
	if($_shopdata->resno_type!="N") {
		$resno1=substr($row->resno,0,6);
		$resno2=substr($row->resno,6,7);
	}
	$email=$row->email;
	$home_tel=$row->home_tel;
	if (strlen($row->home_post) > 5) {
		$home_post=substr($row->home_post,0,3)."-".substr($row->home_post,3,3);
	} else {
		$home_post=$row->home_post;
	}
	$home_addr=$row->home_addr;
	$home_addr_temp=explode("↑=↑",$home_addr);
	$home_addr1=$home_addr_temp[0];
	$home_addr2=$home_addr_temp[1];
	$mobile=$row->mobile;
	$office_post1=substr($row->office_post,0,3);
	$office_post2=substr($row->office_post,3,3);
	$office_addr=$row->office_addr;
	$office_addr_temp=explode("↑=↑",$office_addr);
	$office_addr1=$office_addr_temp[0];
	$office_addr2=$office_addr_temp[1];
	$etc=explode("↑=↑",$row->etcdata);
	$mem_type=$row->mem_type;
	$office_name=$row->office_name;
	$office_no=$row->office_no;
	$office_representative=$row->office_representative;
	$office_tel=$row->office_tel;
	$reserve=$row->reserve;
	$sumsale=$row->sumprice;
	$reg_date=substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2)." (".substr($row->date,8,2).":".substr($row->date,10,2).")";
	$logindate=substr($row->logindate,0,4)."-".substr($row->logindate,4,2)."-".substr($row->logindate,6,2)." (".substr($row->logindate,8,2).":".substr($row->logindate,10,2).":".substr($row->logindate,12,2).")";

	$joinip=$row->joinip;
	$ip=$row->ip;

	$logincnt=$row->logincnt;


	if($row->news_yn=="Y") {
		$news_mail_yn="Y";
		$news_sms_yn="Y";
	} else if($row->news_yn=="M") {
		$news_mail_yn="Y";
		$news_sms_yn="N";
	} else if($row->news_yn=="S") {
		$news_mail_yn="N";
		$news_sms_yn="Y";
	} else if($row->news_yn=="N") {
		$news_mail_yn="N";
		$news_sms_yn="N";
	}

	$rec_id=$row->rec_id;
	if(ord($rec_id)==0) {
		$str_rec="추천인 없음";
	} else {
		$str_rec=$rec_id;
	}
	if($recommand_type=="Y") {
		$sql = "SELECT rec_cnt FROM tblrecommendmanager ";
		$sql.= "WHERE rec_id='{$id}' ";
		$result2= pmysql_query($sql,get_db_conn());
		if($row2=pmysql_fetch_object($result2)) {
			$str_rec.=" <b><font color=#3A3A3A> {$row2->rec_cnt}명이 당신을 추천하셨습니다.</font></b>";
		}
		pmysql_free_result($result2);
	}
} else {
	echo "<script>window.close();</script>";
	exit;
}
pmysql_free_result($result);

$straddform='';
$scriptform='';
$stretc='';
if(ord($member_addform)) {
	$straddform.="<tr>\n";
	$straddform.="	<TD height=\"30\" colspan=4 align=center><B>추가정보</B></td>\n";
	$straddform.="</tr>\n";

	$fieldarray=explode("↑=↑",$member_addform);
	$num=sizeof($fieldarray)/3;
	for($i=0;$i<$num;$i++) {
		if (substr($fieldarray[$i*3],-1,1)=="^") {
			$fieldarray[$i*3]="<img src=\"images/icon_point2.gif\" border=\"0\">".substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1);
			$field_check[$i]="OK";
		}

		$stretc.="<tr>\n";
		$stretc.="	<TD class=\"table_cell\">".$fieldarray[$i*3]."</td>\n";

		$etcfield[$i]="{$etc[$i]}";

		$stretc.="	<TD class=\"td_con1\" colspan=\"3\">{$etcfield[$i]}</TD>\n";
		$stretc.="</tr>\n";


	}
	$straddform.=$stretc;
}


?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>회원정보</title>
<link rel="stylesheet" href="style.css" type="text/css">
<style>
	.hideData{
		display:none;
	}
	.hideData td{
		border:1px solid #EDEDED;
	}
	.viewData{
		cursor:pointer;
	}
</style>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

function PageResize() {
	var oWidth = 750;
	var oHeight = 530;

	window.resizeTo(oWidth,oHeight);
}

function ReserveInfo(id) {
	window.open("about:blank","reserve_info","height=400,width=400,scrollbars=yes");
	document.reserve_pop.id.value=id;
	document.reserve_pop.submit();
}

function GroupInfo(id) {
	window.open("about:blank","group_info","height=400,width=400,scrollbars=yes");
	document.group_form.id.value=id;
	document.group_form.submit();
}

function OrderInfo(id) {
	window.open("about:blank","orderinfo","width=414,height=320,scrollbars=yes");
	document.form3.target="orderinfo";
	document.form3.id.value=id;
	document.form3.action="orderinfopop_new.php";
	document.form3.submit();
}

function CouponInfo(id) {
	window.open("about:blank","couponinfo","width=600,height=400,scrollbars=yes");
	document.form3.target="couponinfo";
	document.form3.id.value=id;
	document.form3.action="coupon_listpop_new.php";
	document.form3.submit();
}

function SendSMS(tel1,tel2,tel3) {
	//number=tel1+"|"+tel2+"|"+tel3;
	number=tel1;
	document.smsform.number.value=number;
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.submit();
}

function SendMail(mail) {
	try {
		opener.parent.topframe.ChangeMenuImg(3);
		opener.document.mailform.rmail.value=mail;
		opener.document.mailform.submit();
	} catch(e) {}
}






function div_in(thisID){
	$("#question_mode").val("write");
	var obj = document.getElementById(thisID);
	if( obj.style.display == 'block' ) obj.style.display = 'none';
	else obj.style.display = 'block';
}
function div_out(thisID){
	document.Crm_writeForm.reset();
	document.getElementById(thisID).style.display = 'none';
}

function popupJquery(message, w, h) {
	$('#dialog-box').html("<div class='dialog-content'><div id='dialog-message'></div></div>");
	var maskHeight = $(document).height();
	var maskWidth = $(window).width();

	// 원본 : var dialogTop =  (maskHeight/4) - ($('#dialog-box').height());
	var dialogTop =  $(window).scrollTop() + ($(window).height()/2) - (h/2);
	//var dialogTop =  (maskHeight/1.5) - (h);
	var dialogLeft = (maskWidth/2) - (w/2);

	$('#dialog-overlay').css({height:maskHeight, width:maskWidth}).show();
	$('#dialog-box').css({top:dialogTop, left:dialogLeft}).show();

	var ifrm = document.createElement("iframe");
	with (ifrm.style){
		width = w;
		height = h;
	}
	ifrm.id = 'createIfrm';
	ifrm.width = w;
	ifrm.height = h;
	ifrm.frameBorder = 0;

	$('#dialog-message').css({width:w, height:h, border:"1px solid #000000"});
	$('#dialog-message').append(ifrm);
	ifrm.src = message;
}


$(document).ready(function () {
	$('#dataInnerHtml').load('./member_question_list.php?id='+$("#thisMemberid").val());

	$(document).on("click", ".viewData", function(){
		$(this).next().toggle();
	});
});


function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	$('#dataInnerHtml').load('./member_question_list.php?id='+$("#thisMemberid").val()+"&block="+block+"&gotopage="+gotopage);
}

function viewAllQna(){
	opener.parent.topframe.GoMenu(8,'community_list.php');
	opener.document.location.href = "./community_personal.php";
	window.close();
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div class="pop_top_title"><p></p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">
<!--form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>"-->
	<input type=hidden name=mode>
	<input type=hidden name=id id = 'thisMemberid' value="<?=$id?>">
	<div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<tr>
			<TD height="30" width="100%" colspan=4><B>회원정보</B><span style="padding-left:220px"><a href="javascript:member_mod('<?=$id?>');"><!--img src="images/btn_modify_g01.gif" border=0 style='vertical-align:middle'--></a></span></td>
		</tr>
		<TR>
			<th style="width:100px;font-size:11px"><span>이름</span></th>
			<TD class="td_con1"><b><span class="font_orange" style="font-size:11px"><?=$name?></span></b></TD>
			<th style="width:100px;font-size:11px"><span>아이디</span></th>
			<TD class="td_con1"><b style="font-size:11px"><?=$id?></b></TD>

		</TR>
		<TR>
			<th style="width:100px;font-size:11px"><span>닉네임</span></th>
			<TD class="td_con1"><b><span style="font-size:11px"><?=$nickname?></span></b></TD>
			<th style="width:100px;font-size:11px"><span>생년월일</span></th>
			<TD class="td_con1"><b style="font-size:11px"></b><?=$birth?></TD>

		</TR>
		<TR>

		</TR>
		<?php if($_shopdata->resno_type!="N"){?>
		<TR>
			<th style="width:100px;font-size:11px"><span>주민등록번호</span></th>
			<TD class="td_con1" style="font-size:11px" colspan=3><?=$resno1?> - <?=str_repeat("*",strlen($resno2))?></TD>
		</TR>
		<?php }?>
		<TR>
			<th style="width:100px;font-size:11px"><span>이메일</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$email?></TD>
			<th style="width:100px;font-size:11px"><span>그룹</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$group_name?> <!-- <A HREF="javascript:GroupInfo('<?=$id?>');"><img src="/admin/images/btn_s_detailview.gif" style='vertical-align:middle'></a> --></TD>
		</TR>
		<TR>
			<th style="width:100px;font-size:11px"><span>E-mail 수신</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$news_mail_yn?> <a href="javascript:SendMail('<?=$email?>')"><img src="images/btn_s_send.gif" border=0 align=absmiddle></a></TD>
			<th style="width:100px;font-size:11px"><span>SMS 수신</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$news_sms_yn?> <a href="javascript:SendSMS('<?=$mobile?>')"><img src="images/btn_s_send.gif" border=0 align=absmiddle alt='sms보내기'></a></TD>
		</TR>
		<tr>
			<th style="width:100px;font-size:11px"><span>집전화</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$home_tel?></TD>
			<th style="width:100px;font-size:11px"><span>휴대폰</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$mobile?></TD>
		</tr>
		<tr>
			<th style="width:100px;font-size:11px"><span>우편번호</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$home_post?></td>
			<th style="width:100px;font-size:11px"><span>주소</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$home_addr1?> <?=$home_addr2?></td>

		</tr>

		<tr>
			<th style="width:100px;font-size:11px"><span>회원가입일</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$reg_date?></td>
			<th style="width:100px;font-size:11px"><span>최근로그인</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$logindate?></td>

		</tr>
		<tr>
			<th style="width:100px;font-size:11px"><span>가입IP</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$joinip?></td>
			<th style="width:100px;font-size:11px"><span>최근로그인IP</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$ip?></td>

		</tr>
		<tr>
			<th style="width:100px;font-size:11px"><span>구매금액</span></th>
			<TD class="td_con1" style="font-size:11px;"><?if($sumsale!='0'){?>(<?=number_format($sumsale)?> + )<br><?}?><?=number_format($sumprice)?> 원 <a href="javascript:OrderInfo('<?=$id?>');"><img src="/admin/images/btn_s_detailview.gif" style='vertical-align:middle'></a></td>
			<th style="width:100px;font-size:11px"><span>적립금</span></th>
			<TD class="td_con1" style="font-size:11px"><?=number_format($reserve)?> <A HREF="javascript:ReserveInfo('<?=$id?>');"><img src="/admin/images/btn_s_detailview.gif" style='vertical-align:middle'></a></td>

		</tr>
		<tr>

			<th style="width:100px;font-size:11px"><span>쿠폰</span></th>
			<TD class="td_con1" style="font-size:11px"><a href="javascript:CouponInfo('<?=$id?>');"><img src="/admin/images/btn_s_detailview.gif"></a></td>
			<th style="width:100px;font-size:11px"><span>로그인횟수</span></th>
			<TD class="td_con1" style="font-size:11px"><?=number_format($logincnt)?> 회</td>

		</tr>

		<?php if($recommand_type=="Y") {?>
		<tr>
			<th style="width:100px;font-size:11px"><span>추천회원ID</span></th>
			<TD class="td_con1" style="font-size:11px" colspan=3><?=$str_rec?></TD>
		</tr>
		<?php }?>
	<?if($mem_type){?>
		<tr>
			<TD height="30" colspan=4 align=center><B>사업자정보</B></td>
		</tr>
		<tr>
			<th style="width:100px;font-size:11px"><span>회사명</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$office_name?></td>
			<th style="width:100px;font-size:11px"><span>사업자번호</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$office_no?></td>
		</tr>
		<tr>
			<th style="width:100px;font-size:11px"><span>대표자명</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$office_representative?></td>
			<th style="width:100px;font-size:11px"><span>회사전화</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$office_tel?></td>
		</tr>
		<tr>
			<th style="width:100px;font-size:11px"><span>우편번호</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$office_post1?> - <?=$office_post2?></td>
			<th style="width:100px;font-size:11px"><span>주소</span></th>
			<TD class="td_con1" style="font-size:11px"><?=$office_addr1?> <?=$office_addr2?></td>
		</tr>
	<?}?>
	<?php
        /*
		if(ord($straddform)) {
			echo $straddform;
		}
        */
	?>
		</TABLE>
	</div>
<!--/form-->

<div class="table_style01">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border:0px;">
	<tr>
		<td height="30" width="100%" colspan=4 align=center>
			<b style="padding-left:270px">상담내역</b>
			<span style="padding-left:220px"><a href="javascript:popupJquery('./member_question_reg.php?id=<?=$id?>', 370, 316);">상담등록</a></span>
		</td>
	</tr>
	<tr>
		<th colspan = '4'>
			<table border="0" cellspacing="0" cellpadding="0" style = "border:0px solid #ffffff;">
				<tr align="center">
				<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="41" height='30'><font class=small1 color=444444><b>No</b></td>
				<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="101"><font class=small1 color=444444><b>상담일</b></td>
				<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="81"><font class=small1 color=444444><b>처리자</b></td>
				<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="408"><font class=small1 color=444444><b>내용</b></td>
				<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="80"><font class=small1 color=444444><b>상담수단</b></td>
				</tr>
			</table>
			<div id = 'dataInnerHtml'></div>
		</th>
	</tr>
	</TABLE>
</div>
<div class="table_style01">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border:0px;">
	<tr>
		<TD height="30" colspan=4 align=center>
			<b style="padding-left:270px">1:1 문의내역</b>
			<span style="padding-left:110px">(최근 3건의 문의만 출력)  <a href="javascript:viewAllQna();">문의전체보기</a></span>
		</td>
	</tr>
	<tr>
		<th colspan = '4'>
			<?
				$sql = "SELECT
								idx, id, name, email, ip, subject, date, re_date, head_title, re_subject, re_id, re_date, content, re_content
							FROM
								tblpersonal
							WHERE
								id = '".$id."'
							ORDER BY
								idx
							DESC LIMIT 3 OFFSET 0";
				$result = pmysql_query($sql);
			?>
			<table border="0" cellspacing="0" cellpadding="0" style = "border:0px solid #ffffff;">
				<tr align="center">
					<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="60" height='30'><font class=small1 color=444444><b>No</b></td>
					<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="410"><font class=small1 color=444444><b>제목</b></td>
					<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="80"><font class=small1 color=444444><b>질문유형</b></td>
					<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="100"><font class=small1 color=444444><b>작성자</b></td>
					<td style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;" width="80"><font class=small1 color=444444><b>작성일</b></td>
				</tr>
			<?
				$countIdx = 3;
				$countIdxData = 0;
				while($row=pmysql_fetch_object($result)) {
					$date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."(".substr($row->date,8,2).":".substr($row->date,10,2).")";
					$redate = substr($row->re_date,0,4)."/".substr($row->re_date,4,2)."/".substr($row->re_date,6,2)."(".substr($row->re_date,8,2).":".substr($row->re_date,10,2).")";
			?>
				<tr style="background:#FFFFFF;" class = "viewData">
					<td width="60" height='30' align = 'center' style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;"><font class=small1 color=444444><?=$countIdx--?></td>
					<td width="410" style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;padding-left:10px;"><font class=small1 color=444444><?=strip_tags($row->subject)?></td>
					<td width="80" align = 'center' style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;">
						<font class=small1 color=444444><?=$arrayCustomerHeadTitle[$row->head_title]?>
					</td>
					<td width="100" align = 'center' style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;"><font class=small1 color=444444><?=$row->id?></td>
					<td width="80" align = 'center' style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;"><font class=small1 color=444444><?=$date?></td>
				</tr>
				<tr class = 'hideData'>
					<td colspan = '9' style = 'padding-left:30px;'><?=nl2br($row->content)?></td>
				</tr>
				<?if(strlen($row->re_date)==14) {?>
				<tr style="background:#FFFFFF;" class = "viewData">
					<td width="60" height='30' align = 'center' style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;"><font class=small1 color=444444>-</td>
					<td width="410" style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;padding-left:10px;">
						<img src = "./img/btn/btn_reply.gif" align = 'absmiddle'><font class=small1 color=444444><?=strip_tags($row->re_subject)?>
					</td>
					<td width="80" align = 'center' style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;">
						<font class=small1 color=444444><?=$arrayCustomerHeadTitle[$row->head_title]?>
					</td>
					<td width="100" align = 'center' style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;"><font class=small1 color=444444><?=$row->re_id?></td>
					<td width="80" align = 'center' style="font-size:11px;font-family:돋움, 굴림;border:0px solid #ffffff;"><font class=small1 color=444444><?=$redate?></td>
				</tr>
				<tr class = 'hideData'>
					<td colspan = '9' style = 'padding-left:30px;'><?=nl2br($row->re_content)?></td>
				</tr>
				<?}?>
			<?
					$countIdxData++;
				}
				if ($countIdxData==0) {
					echo "<tr style='background:#FFFFFF;'><td colspan=9 align=center height=30 style='border:0px solid #ffffff;'>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
			?>
			</table>
		</th>
	</tr>
	</TABLE>
</div>


<form name=reserve_pop action="member_reservelist_new.php" method=post target=reserve_info>
<input type=hidden name=id>
<input type=hidden name=type>
</form>
<form name=form3 method=post>
<input type=hidden name=id>
</form>
<form name=smsform action="sendsms.php" method=post target="sendsmspop">
<input type=hidden name=number>
</form>

<form name=group_form action="groupchangepop.php" method=post target=group_info>
<input type=hidden name=id>
<input type=hidden name=type>
</form>

<div id="dialog-overlay"></div>
<div id="dialog-box">
	<div class="dialog-content">
		<div id="dialog-message">
		</div>
	</div>
</div>

<?=$onload?>
</body>
</html>
