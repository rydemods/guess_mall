<?php
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
} 
?>
function clearRow(){
  var length = tbList.rows.length;
  for(var i=length; i>1; i--){
    tbList.deleteRow(i-1);
  }
}

function addRow(prdid, prdnm){
  var oRow = tbList.insertRow();
  oRow.style.backgroundColor = "FFFFFF";
  oRow.style.height = "28";
  oRow.align = "center";
  oRow.onmouseover=function(){ tbList.clickedRowIndex=this.rowIndex;};
  
  idx = tbList.rows.length - 1;
  var oCell1 = oRow.insertCell();
  var oCell2 = oRow.insertCell();
  var oCell3 = oRow.insertCell();
  
  oCell1.innerHTML= "<input type=\"checkbox\" name=\"valid_flag\" value =\""+String(idx-1)+"\"> " +
  					"<input type=\"hidden\" name=\"upthemecode[]\"> ";
  oCell2.innerHTML= "<input type=\"text\" name=\"upprdtcode[]\" value=\""+prdid+"\" size=\"18\" readonly> ";
  oCell3.innerHTML= prdnm;
}

function checkDup(prdId){
 var length = tbList.rows.length;
 if(length == 1) return true;
 
 for(var i=1; i<length; i++){
  if(tbList.rows(i).cells(1).children[0].value == prdId){
  	alert("선택하신 상품은 이미 해당카테고리에 들어가 있습니다.");
    return false;
  }
 }
  return true;
}

function checkedAll(){
  var flag = iForm.allChk.checked;
  for(var i=0; i<tbList.rows.length; i++){
    if(tbList.rows(i).cells(0).children[0].type == 'checkbox'){
      tbList.rows(i).cells(0).children[0].checked = flag;
    }
  }
}

function saveData(){

  var selfMCtgrLen = parent.ThemeBCodeCtgr.iForm.theme_sectcode.length;  
  var selfLCtgr = parent.ThemePrdtListFrm.ThemeACodeCtgr.options[parent.ThemePrdtListFrm.ThemeACodeCtgr.selectedIndex].value;
  var selfMCtgr = parent.ThemeBCodeCtgr.iForm.theme_sectcode.options[parent.ThemeBCodeCtgr.iForm.theme_sectcode.selectedIndex].value;

  if(tbList.rows.length == 1){alert("저장할 데이터가 없습니다."); return;}
  if(!parent.checkCondition()) return;
  if(selfMCtgrLen == 1){
    iForm.theme_sectcode.value = selfLCtgr
  }else{
    iForm.theme_sectcode.value = selfMCtgr
  }
  if(confirm("해당 카테고리에 등록하시겠습니까?")){
  	  iForm.themeGoodNm.value = parent.ThemePrdtListFrm.themeGoodNm.value;
	  iForm.mode.value="update";
	  iForm.target = "processFrame";
	  iForm.submit();
  }
}


function deleteData(){
  var cnt = 0;

  var selfMCtgrLen = parent.ThemeBCodeCtgr.iForm.theme_sectcode.length;  
  var selfLCtgr = parent.ThemePrdtListFrm.ThemeACodeCtgr.options[parent.ThemePrdtListFrm.ThemeACodeCtgr.selectedIndex].value;
  var selfMCtgr = parent.ThemeBCodeCtgr.iForm.theme_sectcode.options[parent.ThemeBCodeCtgr.iForm.theme_sectcode.selectedIndex].value;
  if(tbList.rows.length == 1) return;
  if(selfMCtgrLen == 1 || selfMCtgr=="0"){
    iForm.theme_sectcode.value = selfLCtgr
  }else{
    iForm.theme_sectcode.value = selfMCtgr
  }
  
  for(var i=0; i<tbList.rows.length; i++){
    if(tbList.rows(i).cells(0).children[0].type == 'checkbox'){
      if(tbList.rows(i).cells(0).children[0].checked){
      	cnt++;
      	break;
      }
    }
  }

  if(cnt == 0){alert("삭제할 데이터를 선택하세요.");return;}
  if(confirm("해당 카테고리에서 삭제하시겠습니까?")){
	  iForm.themeGoodNm.value = parent.ThemePrdtListFrm.themeGoodNm.value;  
	  iForm.mode.value="delete";
	  iForm.target = "processFrame";
	  iForm.submit();
  }
}

function calculageHeightSize(){
  if(document.all.ifr.offsetHeight != 0) {
    try {
      if ( document.all.ifr.offsetHeight < 460 ) {
        parent.document.all.ThemePrdtListIfrm.style.height = 10 + document.all.ifr.offsetHeight;
      } else {
        parent.document.all.ThemePrdtListIfrm.style.height = document.all.ifr.offsetHeight;
      }
    } catch(e) {
      self.resizeTo(685 , 460);
    }
  }
}
