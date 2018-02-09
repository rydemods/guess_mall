<?
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
var prdListWin;
var hot_templt_img = new Array();
var hot_prd_count = new Array();
var new_templt_img = new Array();
var new_prd_count = new Array();
var maxRow;


function  changeHotTempltIdx() {
	iForm.hot_templt_img.src = hot_templt_img[iForm.hot_templt_dispseq.value];
	iForm.hot_prddispcnt.value = hot_prd_count[iForm.hot_templt_dispseq.value];
	resetHotPrdList();
}

function  changeNewTempltIdx() {
	iForm.new_templt_img.src = new_templt_img[iForm.new_templt_dispseq.value];
}

function openCbmPrdInfoWindow(){
	return windowOpenScroll("product_poplist.php", "cbmPrdInfo", 620, 680);
}

function openPrdList() {
	if ( iForm.hot_sect_prdlink[1].checked ) {
		if ( countPrdList() < iForm.hot_prddispcnt.value) {    
			prdListWin = openCbmPrdInfoWindow();
		} else {
			alert("이미 상품이 모두 등록되었습니다.");
		}
	}
}

function countPrdList() {
	for ( var i = 0; i < iForm.hot_prddispcnt.value; i++ ) {
		if ( eval("iForm.hot_prdcode" + i +".value") == 0 ) {
			return i;
		}
	}
	return iForm.hot_prddispcnt.value;
}

function deletePrdList() {
	if ( iForm.hot_sect_prdlink[1].checked ) {
		for(var i = 0; i < iForm.hot_prddispcnt.value; i++) {
			if ( eval("iForm.selectBoxPrd[" + i + "].checked")) {
				eval("hot_prdname" + i +".innerText = ''; ");
				eval("iForm.hot_prdcode" + i +".value = ''; ");
				eval("iForm.hot_prdprice" + i +".value = ''; ");
				eval("iForm.hot_prddpflag" + i +".value = ''; ");
				eval("iForm.selectBoxPrd[" + i + "].checked = false;");
			}
		}
	}
}

function resetHotPrdList() {
	for(var i = 0; i < maxRow; i++) {
		if ( i < iForm.hot_prddispcnt.value ) {
			if ( iForm.hot_sect_prdlink[0].checked ) {      
				document.all['autoHeader'].style.display='';
				document.all['autoPrd'+i].style.display='';
				document.all['hotHeader'].style.display='none';
				document.all['hotPrd'+i].style.display='none';
			} else {
				document.all['autoHeader'].style.display='none';
				document.all['autoPrd'+i].style.display='none';
				document.all['hotHeader'].style.display='';
				document.all['hotPrd'+i].style.display='';
			}
		} else {
			document.all['autoPrd'+i].style.display='none';
			document.all['hotPrd'+i].style.display='none';
		}
	}
	return true;
}

function setCbmPrdInfo(arg) {
	if ( iForm.hot_sect_prdlink[1].checked) {
		for ( var i = 0; i < maxRow; i++ ) {
			if ( eval("iForm.hot_prdcode" + i +".value") == "") {
				for ( var j = 0; j < maxRow; j++ ) {
					if ( eval("iForm.hot_prdcode" + j +".value") == arg[0]) {
						return;
					}
				}
				eval("hot_prdname" + i +".innerText = '" + arg[1] + "'; ");
				eval("iForm.hot_prdcode" + i +".value = '" + arg[0] + "'; ");
				eval("iForm.hot_prdprice" + i +".value = '" + toMoney(arg[2]) + "'; ");
				eval("iForm.hot_prddpflag" + i +".value = 'Y'; ");
				break;
			}
			if (iForm.hot_prddispcnt.value <= (i + 1) ) {
				prdListWin.close();
				return;
			}
		}
	} else {
		prdListWin.close();
	}

	if ( countPrdList() == iForm.hot_prddispcnt.value ) {
		prdListWin.close();
	}
}

function goUp(index) {
	if ( index > 0 ) {
		var prd_id = eval("iForm.hot_prdcode" + (index - 1) +".value");
		var prd_nm = eval("hot_prdname" + (index - 1) +".innerText");
		var prd_price = eval("iForm.hot_prdprice" + (index - 1) +".value");
		var prd_dpflag = eval("iForm.hot_prddpflag" + (index - 1) +".value");

		eval("iForm.hot_prdcode" + (index - 1) +".value = iForm.hot_prdcode" + index +".value;");
		eval("hot_prdname" + (index - 1) +".innerText = hot_prdname" + index +".innerText;");
		eval("iForm.hot_prdprice" + (index - 1) +".value = iForm.hot_prdprice" + index +".value;");
		eval("iForm.hot_prddpflag" + (index - 1) +".value = iForm.hot_prddpflag" + index +".value;");

		eval("iForm.hot_prdcode" + index +".value = '" + prd_id +"';");
		eval("hot_prdname" + index +".innerText = '" + prd_nm +"';");
		eval("iForm.hot_prdprice" + index +".value = '" + prd_price +"';");
		eval("iForm.hot_prddpflag" + index +".value = '" + prd_dpflag +"';");
	}
}

function goDown(index) {
	if ( index < maxRow - 1) {
		var prd_id = eval("iForm.hot_prdcode" + index +".value");
		var prd_nm = eval("hot_prdname" + index +".innerText");
		var prd_price = eval("iForm.hot_prdprice" + index +".value");
		var prd_dpflag = eval("iForm.hot_prddpflag" + index +".value");

		eval("iForm.hot_prdcode" + index +".value = iForm.hot_prdcode" + (index + 1) +".value;");
		eval("hot_prdname" + index +".innerText = hot_prdname" + (index + 1) +".innerText;");
		eval("iForm.hot_prdprice" + index +".value = iForm.hot_prdprice" + (index + 1) +".value;");
		eval("iForm.hot_prddpflag" + index +".value = iForm.hot_prddpflag" + (index + 1) +".value;");

		eval("iForm.hot_prdcode" + (index + 1) +".value = '" + prd_id + "';");
		eval("hot_prdname" + (index + 1) +".innerText = '" + prd_nm + "';");
		eval("iForm.hot_prdprice" + (index + 1) +".value = '" + prd_price + "';");
		eval("iForm.hot_prddpflag" + (index + 1) +".value = '" + prd_dpflag +"';");
	}
}

function changeSect(sectCD) {
	var selSect;
	var changOk = false;
	var message = "변경하신 내용이 있으면 저장 후 상점 대분류를 변경하세요.\n새로운 대분류 정보을 조회하시겠습니까?";
  
	if ( confirm(message) ) {
		if (sectCD == '20'){
			iForm.cbm_tgbn[1].checked = true;
		}else{
			iForm.cbm_tgbn[0].checked = true;	
		}
		iForm.mode.value="";
		iForm.target = "_self";
		iForm.submit();
	} else {
		if(iForm.select_tgbn.value=="10") {
			iForm.cbm_tgbn[0].checked = true;
			for ( var i = 0; i < iForm.cbm_sectcode.options.length; i++ ) {
				if ( iForm.select_code.value ==  iForm.cbm_sectcode.options[i].value ) {
					iForm.cbm_sectcode.options.selectedIndex = i;
				}
			}
		} else if(iForm.select_tgbn.value=="20") {
			iForm.cbm_tgbn[1].checked = true;
			for ( var i = 0; i < iForm.cbm_themesectcode.options.length; i++ ) {
				if ( iForm.select_code.value ==  iForm.cbm_themesectcode.options[i].value ) {
					iForm.cbm_themesectcode.options.selectedIndex = i;
				}
			}
		}	
	}
}

function formSubmit() {
	if ( iForm.hot_used_flag_radio[0].checked ) {
		iForm.hot_used_flag.value = "1";
	} else {
		iForm.hot_used_flag.value = "0";
	}

	if ( iForm.hot_sect_prdlink[0].checked ) {
		iForm.hot_prdlinktype.value = "1";
	} else {
		iForm.hot_prdlinktype.value = "2";
	}

	if ( iForm.new_used_flag_radio[0].checked ) {
		iForm.new_used_flag.value = "1";
	} else {
		iForm.new_used_flag.value = "0";
	}
	iForm.mode.value="update";
  
	iForm.target = "processFrame";
	iForm.submit();
}

function formGSubmit() {
	if ( iForm.hot_used_flag_radio[0].checked && iForm.hot_sect_prdlink[1].checked) {
		if ( countPrdList() < iForm.hot_prddispcnt.value ) {
			alert("추천 상품을 선택하세요...");
			prdListWin = openCbmPrdInfoWindow();
			return;
		}
	}
	  
	if ( iForm.hot_used_flag_radio[0].checked ) {
		iForm.hot_used_flag.value = "1";
	} else {
		iForm.hot_used_flag.value = "0";
	}

	if ( iForm.hot_sect_prdlink[0].checked ) {
		iForm.hot_prdlinktype.value = "1";
	} else {
		iForm.hot_prdlinktype.value = "2";
	}
	iForm.mode.value="update";

	iForm.target = "processFrame";
	iForm.submit();
}

function formEventSubmit(proc_type) {
	if(typeof(iForm.select_code)=="object") {
		if(iForm.select_code.value.length==0) {
			alert("대분류를 선택하세요...");
			return;
		}
	}
	if(iForm.toptype[1].checked) {
		if(iForm.upfileimage.value.length==0) {
			alert("이미지를 선택하세요.");
			return;
		}
		iForm.image_path.value=iForm.upfileimage.value;
	} else if(iForm.toptype[2].checked) {
		if(iForm.topdesign.value.length==0) {
			alert("내용을 입력하세요.");
			iForm.topdesign.focus();
			return;
		}
	}
	if (proc_type=="preview") {
		mallWin = windowOpenScroll("", "MinishopPreview", 920, 500);
		mallWin.focus();

		iForm.target = "MinishopPreview";
		iForm.action = "preview.minishop.php";
		iForm.submit();
		iForm.action = "";
	} else {
		if (confirm("변경하신 내용을 저장하시겠습니까?")) {
			iForm.mode.value="update";
			iForm.target = "processFrame";
			iForm.submit();
		}
	}
}
