<?php
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
} 
?>
var bodyOnLoad = 0;
var loadedNum = 0;

function ACodeSendIt(code) {
	bodyOnLoad = 1;
	loadedNum = 0;

	document.sForm.code.value=code;
	document.sForm.prcode.value="";
	murl = "sellstat_sale.ctgr.php?code="+code+"&depth=2";
	surl = "sellstat_sale.ctgr.php?depth=3";
	durl = "sellstat_sale.ctgr.php?depth=4";
	BCodeCtgr.location.href = murl;
	CCodeCtgr.location.href = surl;
	DCodeCtgr.location.href = durl;
}

function f_getData() {
	var dCtgr = DCodeCtgr.iForm.code.options[DCodeCtgr.iForm.code.selectedIndex].value;
	var sCtgr = CCodeCtgr.iForm.code.options[CCodeCtgr.iForm.code.selectedIndex].value;
	var mCtgr = BCodeCtgr.iForm.code.options[BCodeCtgr.iForm.code.selectedIndex].value;
	var lCtgr = sForm.code1.options[sForm.code1.selectedIndex].value;
  
	if(dCtgr == '') {  
		if(sCtgr == '') {
			if(mCtgr == '') {
				if(lCtgr == '') {
					//alert('분류를 선택하세요.');
					//sForm.code1.focus();
					//return false;
				} else {
					sForm.code.value = lCtgr;
				}
			} else {
				sForm.code.value = mCtgr;
			}
		} else {
			sForm.code.value = sCtgr;
		}
	} else {
		sForm.code.value = dCtgr;
	}

	sForm.target="PrdtListIfrm";
	sForm.action = "sellstat_sale.prlist.php";
	sForm.submit();
}

function SellStat() {
	if(document.sForm.code.value.length==0) {
		alert("분류를 선택하세요.");
		document.sForm.code1.focus();
		return;
	}
	if(!IsNumeric(document.form1.age1.value) || !IsNumeric(document.form1.age2.value)) {
		alert("연령 입력은 숫자만 입력하셔야 합니다.");
		return;
	}
	age1=0;
	age2=0;
	if(document.form1.age1.value.length>0 && document.form1.age2.value.length>0) {
		age1=document.form1.age1.value;
		age2=document.form1.age2.value;
		if(age1==0 || age2==0 || age1>age2) {
			age1=0;
			age2=0;
		}
	}
	if((age1>0 || document.form1.sex.value!="ALL") && document.form1.member.value!="Y") {
		document.form1.member.options[1].selected=true;
	}
	document.form1.code.value=document.sForm.code.value;
	document.form1.prcode.value=document.sForm.prcode.value;
	document.form1.age1.value=age1;
	document.form1.age2.value=age2;
	document.form1.target="StatIfrm";
	document.form1.action="sellstat_sale.result.php";
	document.form1.submit();
}
