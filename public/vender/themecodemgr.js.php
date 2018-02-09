<?php
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
} 
?>
var DELCtgr = new Array();
var delIdx = 0;

function f_setEdit(){
	var selectedIdx = form1.code.selectedIndex;  
	if(selectedIdx >=0){
		parent.ctgrEdit.value = form1.code[selectedIdx].text;
	}
}

function f_editChange(){
	if(parent.ctgrEdit.value == '') return;
	var selectedIdx = form1.code.selectedIndex;  
	if(selectedIdx > -1){
		form1.code[selectedIdx].text = parent.ctgrEdit.value;
	}
}

function moveTop(){
	var selectedIdx = form1.code.selectedIndex;

	if(selectedIdx >=0){
		var oldID = form1.code[selectedIdx].value;
		var oldTxt = form1.code[selectedIdx].text;
		form1.code.options[selectedIdx] = null;
		form1.code.options.add(new Option(oldTxt,oldID), 0);
		form1.code[0].selected = true;
		f_setEdit();
	}else{
		alert("선택된 항목이 없습니다.");
	}
}

function moveBottom(){
	var selectedIdx = form1.code.selectedIndex;

	if(selectedIdx >=0){  
		var oldID = form1.code[selectedIdx].value;
		var oldTxt = form1.code[selectedIdx].text;

		form1.code.options[selectedIdx] = null;
		form1.code.options[form1.code.options.length] = new Option(oldTxt, oldID);
		form1.code[form1.code.options.length-1].selected = true;
		f_setEdit();
	}else{
		alert("선택된 항목이 없습니다.");
	}
}

function moveUp(){
	var selectedIdx = form1.code.selectedIndex;
	if(selectedIdx == 0) return;

	if(selectedIdx > 0){  
		var oldID = form1.code[selectedIdx-1].value;
		var oldTxt = form1.code[selectedIdx-1].text;

		form1.code[selectedIdx-1].value = form1.code[selectedIdx].value;
		form1.code[selectedIdx-1].text  = form1.code[selectedIdx].text;	  
		form1.code[selectedIdx].value   = oldID;
		form1.code[selectedIdx].text    = oldTxt;
		form1.code[selectedIdx-1].selected = true;
		f_setEdit();
	}else{
		alert("선택된 항목이 없습니다.");
	}
}

function moveDown(){
	var selectedIdx = form1.code.selectedIndex;
	if(selectedIdx == form1.code.options.length-1) return;

	if(selectedIdx >= 0){  
		var oldID = form1.code[selectedIdx+1].value;
		var oldTxt = form1.code[selectedIdx+1].text;

		form1.code[selectedIdx+1].value = form1.code[selectedIdx].value;
		form1.code[selectedIdx+1].text  = form1.code[selectedIdx].text;
		form1.code[selectedIdx].value   = oldID;
		form1.code[selectedIdx].text    = oldTxt;
		form1.code[selectedIdx+1].selected = true;
		f_setEdit();
	}else{
		alert("선택된 항목이 없습니다.");
	}
}


function addRow(){
	var length = form1.code.options.length;
	if(form1.code_a.value != '' && length == 0){
		if(confirm("중분류를 생성하시면 대분류에 진열된 상품이 있을 경우,\n상품진열이 모두 해제됩니다.중분류를 생성하시겠습니까?") == false){
			return;
		}
	}

	form1.code.options[length] = new Option("카테고리"+ (length+1), "");
	form1.code[length].selected = true;
	f_setEdit();
}

function delRow(){
	var length = form1.code.options.length;
	var selectedIdx = form1.code.selectedIndex;
	var cbm_self_id = "";

	if(selectedIdx < 0){
		alert("선택된 항목이 없습니다.");
		return;
	}


	if(selectedIdx > -1 && confirm("카테고리를 삭제하시면 해당카테고리에 진열한 상품이 있을 경우 \n상품 설정도 동시에 해제됩니다. 카테고리를 삭제하시겠습니까?")){
		cbm_self_id = form1.code.options[selectedIdx].value;
		form1.code.options[selectedIdx] = null;

		if(form1.code.options.length > 0) {
			if(selectedIdx <= 1){
				form1.code[0].selected = true;
				f_setEdit();
			}else{
				form1.code[selectedIdx-1].selected = true;
				f_setEdit();
			}
		}
		if(cbm_self_id != ""){
			DELCtgr[delIdx] = cbm_self_id;
			delIdx++;
		}
	}
}

function applyRow(){

	var msg = "수정하신 항목이 모두 적용됩니다. \n저장 하시겠습니까?";  
	if(confirm(msg)){
		createDelete();
		modifiedData();
		if(Number(form1.delCnt.value) + Number(form1.code_cnt.value) == 0){
			alert("처리할 데이터가 없습니다");
			return;
		}
		form1.mode.value="update";
		form1.target = "processFrame";
		form1.submit();
	}
}

function createDelete(){	
	var length = DELCtgr.length;
	form1.delCnt.value = length;
	delcodes="";
	for(var idx=0; idx<length; idx++){
		if(idx>0) delcodes+="=";
		delcodes+=DELCtgr[idx];
		//oData.insertAdjacentHTML("afterEnd", "<input type=hidden name=delCtgr"+idx+" value=" +DELCtgr[idx]+ ">");
	}
	oData.insertAdjacentHTML("afterEnd", "<input type=hidden name=delcodes value=" +delcodes+ ">");
}

function modifiedData(){
	var length = form1.code.options.length;
	form1.code_cnt.value = length;
	codes="";
	for(var idx=0; idx<length; idx++){
		if(idx>0) codes+="=";
		codes+=form1.code[idx].value+""+replaceDQuot(form1.code[idx].text);
		//oData.insertAdjacentHTML("afterEnd", "<input type=hidden name=codes"+idx+" value=\"" +form1.code[idx].value+ "\" >");
		//oData.insertAdjacentHTML("afterEnd", "<input type=hidden name=codes_name"+idx+" value=\"" +replaceDQuot(form1.code[idx].text)+ "\" >");
	}
	oData.insertAdjacentHTML("afterEnd", "<input type=hidden name=savecodes value=\"" +codes+ "\" >");
}

function replaceDQuot(str) {
	var temp = str; 
	var oldArr = new Array("\"");	
	var newArr = new Array("'");
	
	for(var i=0; i<oldArr.length; i++){
		var oldStr = oldArr[i];
		var newStr = newArr[i];
		while (temp.indexOf(oldStr)>-1) {
			pos= temp.indexOf(oldStr);
			temp = "" + (temp.substring(0, pos) + newStr + temp.substring((pos + oldStr.length), temp.length));
		}		
	}
	return temp;
}


function SaveCodeDispType(){
	var strSelectedVal = "";
	for(var i=0; i<parent.code_disptype.length; i++){
		if(parent.code_disptype[i].checked){
			strSelectedVal = parent.code_disptype[i].value;
		}
	}
	if(strSelectedVal.length!=2) {
		alert("카테고리 노출 설정값을 선택하세요.");
		parent.code_disptype[0].focus();
		return;

	}
	if(!confirm("카테고리 노출 설정을 하시겠습니까?")) return;
	if(typeof(form1.code_disptype)!="object") {
		oData.insertAdjacentHTML("afterEnd", "<input type=hidden name=code_disptype value=\"" + strSelectedVal + "\" >");
	} else {
		form1.code_disptype.value=strSelectedVal;
	}
	form1.mode.value="disptypeupdate";
	form1.target = "processFrame";
	form1.submit();
}
