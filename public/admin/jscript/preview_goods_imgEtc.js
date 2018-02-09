// 이미지 미리보기
var objPreloadImg;
var previewWidth = 150;
var previewHeight = 150;

var previewDefault = '/images/blank.gif';

var nowPreviewUrl;

function previewImgEtc(value) {
	var arrExt = new Array('jpg', 'gif');

	nowPreviewUrl = "";

	if (value.stripspace() != "") {
		var fidx = value.lastIndexOf("\\")+1;
		var filename = value.substr(fidx, value.length);
		var eidx = value.lastIndexOf(".")+1;
		var ext = value.substr(eidx, value.length).toLowerCase();

		// 파일명 확인
		if (!strEngCheck(filename)) {
			alert("파일명을 반드시 영문 또는 숫자로 해주세요.");
			return false;
		}

		// 파일확장자 확인
		var chkExt = false;
		for (var i=0; i<arrExt.length; i++) {
			if (arrExt[i] == ext) chkExt = true;
		}
		if (!chkExt) {
			alert("이미지(jpg, gif) 파일만 선택해 주세요.");
			return false;
		}
	}
	else {
		value = previewDefault;
	}

	objPreloadImg = new Image();
	objPreloadImg.src = value;

	execPreviewImgEtc();
}

function execPreviewImgEtc() {
	if (!objPreloadImg.complete) {
		setTimeout("execPreviewImgEtc()", "100");
		return;
	}

	var orgWidth = objPreloadImg.width;
	var orgHeight = objPreloadImg.height;

	var rateX = orgWidth / previewWidth;
	var rateY = orgHeight / previewHeight;

	var rate = (rateX > rateY) ? rateX : rateY;
	if (rate < 1) rate = 1;

	var width = parseInt(orgWidth / rate, 10);
	var height = parseInt(orgHeight / rate, 10);

	var objPreview = document.getElementById("imgEtcPreview");
	objPreview.style.width = width+'px';
	objPreview.style.height = height+'px';
	objPreview.innerHTML = '<img src="'+objPreloadImg.src+'" width="'+width+'" height="'+height+'" onError="this.src=\''+previewDefault+'\'">';

	nowPreviewUrl = objPreloadImg.src;
}

function openPreviewZoom() {
	if (nowPreviewUrl)
		openPopup("/common/pop_orgImageView.asp?path="+escape(nowPreviewUrl), "PreviewZoom", 100, 100, "status=yes, resizable=yes");
}