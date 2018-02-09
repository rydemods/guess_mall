var isIE = (window.navigator.appName.indexOf("Explorer") != -1) ? true : false; // ie 여부

var ezPopLayerID = 'divEzPopUser';
var ezPopUserID = '';
var ezPopIsFunc = 0;

function ezUserInfo() {
	if (ezPopUserID == GUEST_ID) {
		alert("비회원 고객은 회원정보를 제공하지 않습니다.");
	}
	else {
		if (ezPopIsFunc) {
			userDetail(ezPopUserID);
		}
		else {
			window.open(ADMIN_PATH+"/member/pop_member_detail.asp?userid="+ezPopUserID, "EzSendEmail", "width=840, height=570, scrollbars=yes");
		}
	}
}

function ezSendEmail() {
	if (ezPopUserID == GUEST_ID) {
		alert("비회원 고객은 메일발송을 할 수 없습니다.");
	}
	else {
		window.open(ADMIN_PATH+"/member/pop_send_email.asp?userid="+ezPopUserID, "EzSendEmail", "width=740, height=570");
	}
}

function ezGetOffset(obj) {
	var objOffset = { left : 0, top : 0 };
	var objOffsetParent = obj.offsetParent;

	objOffset.left = obj.offsetLeft;
	objOffset.top = obj.offsetTop;

	while (objOffsetParent) {
		objOffset.left += objOffsetParent.offsetLeft;
		objOffset.top += objOffsetParent.offsetTop;

		objOffsetParent = objOffsetParent.offsetParent;
	}

	return objOffset;
}

function ezRegistEvent() {
	if (isIE) {
		document.attachEvent('onclick', ezHidePop);
	}
	else if (document.addEventListener) {
		document.addEventListener('click', ezHidePop, false);
	}
	else if (document.attachEvent) {
		document.attachEvent('click', ezHidePop);
	}
}

function ezUnregistEvent() {
	if (isIE) {
		document.detachEvent('onclick', ezHidePop);
	}
	else if (document.removeEventListener) {
		document.removeEventListener('click', ezHidePop, false);
	}
	else if (document.detachEvent) {
		document.detachEvent('click', ezHidePop);
	}
}

function ezPop(e, userid, isFunc) {
	var e = window.event || e;

	ezPopUserID = userid;
	ezPopIsFunc = (isFunc) ? 1 : 0;

	var objElement = (e.srcElement) ? e.srcElement : e.target;

	setTimeout(function () { ezShowPop(objElement) }, 300);
}

function ezShowPop(objElement) {
	if (!document.getElementById(ezPopLayerID)) ezPopMake();

	var objPos = ezGetOffset(objElement);

	var objEzPop = document.getElementById(ezPopLayerID);
	objEzPop.style.left = objPos.left+"px";
	objEzPop.style.top = objPos.top + objElement.offsetHeight+"px";
	objEzPop.style.display = "";

	ezRegistEvent();
}

function ezHidePop() {
	ezUnregistEvent();

	var objEzPop = document.getElementById(ezPopLayerID);
	if (objEzPop) objEzPop.style.display = "none";
}

function ezPopOver(objElement) {
	objElement.style.backgroundColor = "#E2F4DE";
}

function ezPopOut(objElement) {
	objElement.style.backgroundColor = "";
}

function ezPopMake() {
	var objDiv = document.createElement('DIV');
	objDiv.id = ezPopLayerID;
	objDiv.style.position = "absolute";
	objDiv.style.left = objDiv.style.top = "0px";
	objDiv.style.zIndex = 100;
	objDiv.style.display = "none";
	objDiv.innerHTML = ezPopGetTable();
	document.body.appendChild(objDiv);
}

function ezPopGetTable() {
	return "\
<table width='80' border='0' cellpadding='1' cellspacing='2' bgcolor='#CCCCCC'><tr><td bgcolor='#FFFFFF'>\
<table width='100%' border='0' cellpadding='3' cellspacing='0'>\
<tr height='18'><td onClick=\"ezUserInfo()\" onMouseOver='ezPopOver(this)' onMouseOut='ezPopOut(this)' style='cursor:pointer;'>회원정보</a></td></tr>\
<tr height='18'><td onClick=\"ezSendEmail()\" onMouseOver='ezPopOver(this)' onMouseOut='ezPopOut(this)' style='cursor:pointer;'>메일보내기</a></td></tr>\
</table>\
</td></tr></table>";
}
