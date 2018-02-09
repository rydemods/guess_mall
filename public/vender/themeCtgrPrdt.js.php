<?php
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
} 
?>
var bodyOnLoad = 0;
var loadedNum = 0;


function ACodeSendIt(code) {
  murl = "product_code.ctgr.php?code="+code;
  bodyOnLoad = 1;
  loadedNum = 0;
  DCodeCtgr.iForm.code.value = '';
  CCodeCtgr.iForm.code.value = '';
  BCodeCtgr.iForm.code.value = '';
    
  surl = "product_code.ctgr.php";
  durl = "product_code.ctgr.php";
  BCodeCtgr.location.href = murl;
  CCodeCtgr.location.href = surl;
  DCodeCtgr.location.href = durl;
}


function ThemeACodeIt(f,obj) {
  ThemeBCodeCtgr.location.href = "product_themecode.ctgr.php?code=" + obj.value;
}


function f_getData(){

  var dCtgr = DCodeCtgr.iForm.code.options[DCodeCtgr.iForm.code.selectedIndex].value;
  var sCtgr = CCodeCtgr.iForm.code.options[CCodeCtgr.iForm.code.selectedIndex].value;
  var mCtgr = BCodeCtgr.iForm.code.options[BCodeCtgr.iForm.code.selectedIndex].value;
  var lCtgr = prdListFrm.code.options[prdListFrm.code.selectedIndex].value;
  
  if(dCtgr == ''){  
    if(sCtgr == ''){
      if(mCtgr == ''){
        if(lCtgr == ''){
          alert('분류를 선택하세요.');
          prdListFrm.code.focus();
          return false;
        }else{
          prdListFrm.sectcode.value = lCtgr;
        }
      }else{
        prdListFrm.sectcode.value = mCtgr;
      }
    }else{
      prdListFrm.sectcode.value = sCtgr;
    }
  }else{
    prdListFrm.sectcode.value = dCtgr;
  }

  prdListFrm.target="PrdtListIfrm";
  prdListFrm.action = "product_prlist.select.php";
  prdListFrm.submit();
}


function checkCondition(){
  var selfMCtgrLen = ThemeBCodeCtgr.iForm.theme_sectcode.length;  
  var selfLCtgr = ThemePrdtListFrm.ThemeACodeCtgr.options[ThemePrdtListFrm.ThemeACodeCtgr.selectedIndex].value;
  var selfMCtgr = ThemeBCodeCtgr.iForm.theme_sectcode.options[ThemeBCodeCtgr.iForm.theme_sectcode.selectedIndex].value;
  if( selfLCtgr == "0"){
    alert("대분류를 선택하세요.");
    ThemePrdtListFrm.ThemeACodeCtgr.focus();
    return false;
  } 
  if(selfMCtgrLen > 1 && selfMCtgr=="0"){
    alert("중분류를 선택하세요.");
    ThemeBCodeCtgr.iForm.theme_sectcode.focus();
    return false;
  }
  return true;
}

function ThemeSelCtgrPrdtList(){
  var selfMCtgrLen = ThemeBCodeCtgr.iForm.theme_sectcode.length;  
  var selfLCtgr = ThemePrdtListFrm.ThemeACodeCtgr.options[ThemePrdtListFrm.ThemeACodeCtgr.selectedIndex].value;
  var selfMCtgr = ThemeBCodeCtgr.iForm.theme_sectcode.options[ThemeBCodeCtgr.iForm.theme_sectcode.selectedIndex].value;
  
  if(!checkCondition()) return false;
  if(selfMCtgrLen == 1){
    ThemePrdtListFrm.theme_sectcode.value = selfLCtgr
  }else{
    ThemePrdtListFrm.theme_sectcode.value = selfMCtgr
  }
  
  ThemePrdtListFrm.target = "ThemePrdtListIfrm";
  ThemePrdtListFrm.action = "product_themeprlist.select.php";
  ThemePrdtListFrm.submit();
}

function SelCtgrPrdtList(){
  var selfLCtgr = ThemePrdtListFrm.ThemeACodeCtgr.options[ThemePrdtListFrm.ThemeACodeCtgr.selectedIndex].value;
  ThemePrdtListFrm.theme_sectcode.value = selfLCtgr;

  ThemePrdtListFrm.target = "ThemePrdtListIfrm";
  ThemePrdtListFrm.action = "product_themeprlist.select.php";
  ThemePrdtListFrm.submit();

}

function copyPrdInfo(){
  var cnt = PrdtListIfrm.frmList.selectPrdList.selectedIndex;
  var index = 0;
  var obj = PrdtListIfrm.frmList.selectPrdList;

  if(cnt < 0) {
    alert("현재 복사할 상품이 선택되지 않았습니다");
    PrdtListIfrm.frmList.selectPrdList.focus();
    return;
  }else{
  	if(!checkCondition()) return false;
    for(var i=0; i<obj.options.length; i++){
      if(obj.options[i].selected && ThemePrdtListIfrm.checkDup(obj[i].value)){
        ThemePrdtListIfrm.addRow(obj[i].value, replaceTag(obj[i].text));
      }
    }
  }
  ThemePrdtListIfrm.calculageHeightSize();
}
