<?
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
}

function custRegistMinishop() {
	if(document.custregminiform.memberlogin.value!="Y") {
		alert("로그인 후 이용이 가능합니다.");
		return;
	}
	owin=window.open("about:blank","miniregpop","width=100,height=100,scrollbars=no");
	owin.focus();
	document.custregminiform.target="miniregpop";
	document.custregminiform.action="minishop.regist.pop.php";
	document.custregminiform.submit();
}

function GoItem(productcode) {
	document.location.href="productdetail.php?productcode="+productcode;
}

function GoSection(sellvidx,tgbn,code) {
	//tgbn : 10=>일반카테고리, 20=>테마카테고리
	//code : 6자리
	if(tgbn.length>0) {
		document.location.href="minishop.productlist.php?sellvidx="+sellvidx+"&tgbn="+tgbn+"&code="+code;
	} else {
		document.location.href="minishop.php?sellvidx="+sellvidx+"&code="+code;
	}
}

function GoNoticeList(sellvidx,block,gotopage) {
	url="minishop.notice.php?sellvidx="+sellvidx;
	if(typeof block!="undefined") url+="&block="+block;
	if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
	document.location.href=url;
}

function GoNoticeView(sellvidx,artid,block,gotopage) {
	url="minishop.notice.php?type=view&sellvidx="+sellvidx+"&artid="+artid;
	if(typeof block!="undefined") url+="&block="+block;
	if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
	document.location.href=url;
}
