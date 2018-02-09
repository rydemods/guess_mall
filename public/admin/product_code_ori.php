<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="<?=$code?>";
var allopen=false;
var movecode=false;

function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";
		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}
	SetButton();
}

function ViewProperty() {
	if(selcode.length==0 || selcode=="000000000000") {
		return;
	}
	document.form1.code.value=selcode;
	document.form1.parentcode.value="";
	document.form1.mode.value="modify";
	document.form1.action="product_code.property.php";
	document.form1.target="PropertyFrame";
	document.form1.submit();
}

function NewCode() {
	if((selcode.length==0 || (selcode.length==12 && selcode.substring(9,12)=="000")) && (seltype.indexOf("X")==-1)) {
		document.form1.parentcode.value=selcode;
	} else {
		document.form1.parentcode.value="";
	}
	document.form1.code.value="";
	document.form1.action="product_code.property.php";
	document.form1.target="PropertyFrame";
	document.form1.submit();
}

function NewCodeResult(_code,type,code_name,list_type,detail_type,sort,group_code) {
	code_a=_code.substring(0,3);
	code_b=_code.substring(3,6);
	code_c=_code.substring(6,9);
	code_d=_code.substring(9,12);
	if((type=="L" || type=="T" || type=="LX" || type=="TX") && code_b=="000") {	//1차카테고리 추가완료
		var calist = new CodeAList();
		calist=AddObj(calist,_code,type,code_name,list_type,detail_type,all_list.length,sort,group_code);
		calist.display="show";
		all_list[all_list.length] = calist;
		calist=null;
		BodyInit(''); SetButton();
		return;
	} else {
		for(i=0;i<all_list.length;i++) {
			if(code_a==all_list[i].code_a) {
				if((type=="LM" || type=="TM" || type=="LMX" || type=="TMX") && code_c=="000") {	//2차
					var cblist = new CodeBList();
					cblist=AddObj(cblist,_code,type,code_name,list_type,detail_type,all_list[i].ArrCodeB.length,sort,group_code);
					if(all_list[i].open=="open") {
						cblist.display="show";
					}
					all_list[i].ArrCodeB[all_list[i].ArrCodeB.length] = cblist;
					cblist=null;
					BodyInit(''); SetButton();
					return;
				} else {
					for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
						if(code_b==all_list[i].ArrCodeB[ii].code_b) {
							
							if((type=="LM" || type=="TM" || type=="LMX" || type=="TMX") && code_d=="000") {
								var cclist = new CodeCList();
								cclist=AddObj(cclist,_code,type,code_name,list_type,detail_type,all_list[i].ArrCodeB[ii].ArrCodeC.length,sort,group_code);
								if(all_list[i].ArrCodeB[ii].open=="open") {
									cclist.display="show";
								}
								all_list[i].ArrCodeB[ii].ArrCodeC[all_list[i].ArrCodeB[ii].ArrCodeC.length] = cclist;
								cclist=null;
								BodyInit(''); SetButton();
								return;
							} else if((type=="LM" || type=="TM" || type=="LMX" || type=="TMX") && code_d!="000") {
								for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
									if(code_c==all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_c) {
										var cdlist = new CodeDList();
										cdlist=AddObj(cdlist,_code,type,code_name,list_type,detail_type,all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length,sort,group_code);

										if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].open=="open") {
											cdlist.display="show";
										}
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length] = cdlist;
										cdlist=null;
										BodyInit(''); SetButton();
										return;
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function AddObj(obj,_code,type,code_name,list_type,detail_type,sequence,sort,group_code) {
	obj.code=_code;
	obj.code_a=_code.substring(0,3);
	obj.code_b=_code.substring(3,6);
	obj.code_c=_code.substring(6,9);
	obj.code_d=_code.substring(9,12);
	obj.type=type;
	obj.code_name=code_name;
	obj.list_type=list_type;
	obj.detail_type=detail_type;
	obj.sequence=sequence;
	obj.sort=sort;
	obj.group_code=group_code;
	obj.selected=false;
	return obj;
}

function ModifyCodeResult(_code,type,code_name,list_type,detail_type,sort,group_code,is_gcode,is_sort,is_design) {
	code_a=_code.substring(0,3);
	code_b=_code.substring(3,6);
	code_c=_code.substring(6,9);
	code_d=_code.substring(9,12);
	for(i=0;i<all_list.length;i++) {
		if(code_a!="000" && code_b=="000" && code_c=="000" && code_d=="000") {
			if(all_list[i].code==_code) {
				all_list[i].code_name=code_name;
				all_list[i].list_type=list_type;
				all_list[i].detail_type=detail_type;
				all_list[i].sort=sort;
				all_list[i].group_code=group_code;
				if(is_gcode==1 || is_sort==1 || is_design==1) {
					for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
						if(is_gcode==1) all_list[i].ArrCodeB[ii].group_code=group_code;
						if(is_sort==1) all_list[i].ArrCodeB[ii].sort=sort;
						if(is_design==1) {
							all_list[i].ArrCodeB[ii].list_type=list_type;
							all_list[i].ArrCodeB[ii].detail_type=detail_type;
						}
						for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
							if(is_gcode==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].group_code=group_code;
							if(is_sort==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].sort=sort;
							if(is_design==1) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
							}
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								if(is_gcode==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].group_code=group_code;
								if(is_sort==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].sort=sort;
								if(is_design==1) {
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
								}
							}
						}
					}
				}
				BodyInit(''); SetButton();
				return;
			}
		} else {
			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
				if(code_a!="000" && code_b!="000" && code_c=="000" && code_d=="000") {
					if(all_list[i].ArrCodeB[ii].code==_code) {
						all_list[i].ArrCodeB[ii].code_name=code_name;
						all_list[i].ArrCodeB[ii].list_type=list_type;
						all_list[i].ArrCodeB[ii].detail_type=detail_type;
						all_list[i].ArrCodeB[ii].sort=sort;
						all_list[i].ArrCodeB[ii].group_code=group_code;
						if(is_gcode==1 || is_sort==1 || is_design==1) {
							for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
								if(is_gcode==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].group_code=group_code;
								if(is_sort==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].sort=sort;
								if(is_design==1) {
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
								}
								for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
									if(is_gcode==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].group_code=group_code;
									if(is_sort==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].sort=sort;
									if(is_design==1) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
									}
								}
							}
						}
						BodyInit(''); SetButton();
						return;
					}
				} else {
					for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
						if(code_a!="000" && code_b!="000" && code_c!="000" && code_d=="000") {
							if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code==_code) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_name=code_name;
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].sort=sort;
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].group_code=group_code;
								if(is_gcode==1 || is_sort==1 || is_design==1) {
									for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
										if(is_gcode==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].group_code=group_code;
										if(is_sort==1) all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].sort=sort;
										if(is_design==1) {
											all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
											all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
										}
									}
								}
								BodyInit(''); SetButton();
								return;
							}
						} else {
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								if(code_a!="000" && code_b!="000" && code_c!="000" && code_d!="000") {
									if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code==_code) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code_name=code_name;
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].sort=sort;
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].group_code=group_code;
										BodyInit(''); SetButton();
										return;
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function AllOpen() {
	display="show";
	open1="open";
	if(allopen) {
		display="none";
		open1="close";
		allopen=false;
	} else {
		allopen=true;
	}
	for(i=0;i<all_list.length;i++) {
		if(display=="none" && all_list[i].code_a==selcode.substring(0,3)) {
			all_list[i].selected=true;
			selcode=all_list[i].code_a+all_list[i].code_b+all_list[i].code_c+all_list[i].code_d;
			seltype=all_list[i].type;
		}
		all_list[i].display=display;
		all_list[i].open=open1;
		for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
			if(display=="none") {
				all_list[i].ArrCodeB[ii].selected=false;
			}
			all_list[i].ArrCodeB[ii].display=display;
			all_list[i].ArrCodeB[ii].open=open1;
			for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
				if(display=="none") {
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].selected=false;
				}
				all_list[i].ArrCodeB[ii].ArrCodeC[iii].display=display;
				all_list[i].ArrCodeB[ii].ArrCodeC[iii].open=open1;
				for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
					if(display=="none") {
						all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].selected=false;
					}
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].display=display;
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].open=open1;
				}
			}
		}
	}
	BodyInit('');
}

function SetButton() {
	document.all["btn_property"].disabled=false;
	document.all["btn_property"].className="btn";
	document.all["btn_codeadd"].disabled=false;
	document.all["btn_codeadd"].className="btn";
	document.all["btn_codedel"].disabled=false;
	document.all["btn_codedel"].className="btn";
	document.all["btn_moveup"].disabled=false;
	document.all["btn_moveup"].className="btn";
	document.all["btn_movedown"].disabled=false;
	document.all["btn_movedown"].className="btn";
	if(selcode.length==0 || selcode=="000000000000") {
		document.all["btn_property"].disabled=true;
		document.all["btn_property"].className="btnNA";
		document.all["btn_moveup"].disabled=true;
		document.all["btn_moveup"].className="btnNA";
		document.all["btn_movedown"].disabled=true;
		document.all["btn_movedown"].className="btnNA";
	}
	if((seltype.indexOf("X")!=-1)) {
		document.all["btn_codeadd"].disabled=true;
		document.all["btn_codeadd"].className="btnNA";
	} else {
		document.all["btn_codedel"].disabled=true;
		document.all["btn_codedel"].className="btnNA";
	}
	if(movecode==false) {
		document.all["btn_movesave"].disabled=true;
		document.all["btn_movesave"].className="btnNA";
		document.all["btn_movecancel"].disabled=true;
		document.all["btn_movecancel"].className="btnNA";
	} else {
		document.all["btn_movesave"].disabled=false;
		document.all["btn_movesave"].className="btn";
		document.all["btn_movecancel"].disabled=false;
		document.all["btn_movecancel"].className="btn";
	}
}

function CodeMove(tmp) {
	//## up / down ##//
	if(selcode.length==0 || selcode=="000000000000" || seltype.length==0) {
		alert("이동할 카테고리 선택이 안되었습니다.");
		return;
	}
	_code_a=selcode.substring(0,3);
	_code_b=selcode.substring(3,6);
	_code_c=selcode.substring(6,9);
	_code_d=selcode.substring(9,12);

	//카테고리이동 처리
	tmp_array=new Array();
	for(i=0;i<all_list.length;i++) {
		if(_code_b=="000" && _code_c=="000" && _code_d=="000" && (seltype=="L" || seltype=="T" || seltype=="LX" || seltype=="TX")) {	//1차 대카테고리
			if(all_list[i].code==selcode) {
				if(tmp=="up") {
					if(i==0) {
						alert("현재 카테고리의 \"최상위\" 입니다. 더 이상 이동할 수 없습니다.");
						return;
					}
				} else if(tmp=="down") {
					if(i==(all_list.length-1)) {
						alert("현재 카테고리의 \"최하위\" 입니다. 더 이상 이동할 수 없습니다.");
						return;
					}
				}
				for(j=0;j<all_list.length;j++) {
					if(tmp=="up") {
						if(j<(i-1) || j>i) {
							tmp_array[j]=all_list[j];
						} else if (j==(i-1)) {
							tmp_array[j]=all_list[j+1];
						} else if (j==i) {
							tmp_array[j]=all_list[j-1];
						}
					} else if(tmp=="down") {
						if(j<i || j>(i+1)) {
							tmp_array[j]=all_list[j];
						} else if(j==(i+1)) {
							tmp_array[j]=all_list[j-1];
						} else if(j==i) {
							tmp_array[j]=all_list[j+1];
						}
					}
				}
				all_list = new Array();
				all_list = tmp_array;
				movecode=true;
				BodyInit('');
				SetButton();
				return;
			}
		} else {
			if(all_list[i].code_a==_code_a) {
				for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
					if(_code_a!="000" && _code_b!="000" && _code_c=="000" && _code_d=="000") {	//2차카테고리
						if(all_list[i].ArrCodeB[ii].code==selcode) {
							if(tmp=="up") {
								if(ii==0) {
									alert("현재 카테고리의 \"최상위\" 입니다. 더 이상 이동할 수 없습니다.");
									return;
								}
							} else if(tmp=="down") {
								if(ii==(all_list[i].ArrCodeB.length-1)) {
									alert("현재 카테고리의 \"최하위\" 입니다. 더 이상 이동할 수 없습니다.");
									return;
								}
							}
							for(j=0;j<all_list[i].ArrCodeB.length;j++) {
								if(tmp=="up") {
									if(j<(ii-1) || j>ii) {
										tmp_array[j]=all_list[i].ArrCodeB[j];
									} else if (j==(ii-1)) {
										tmp_array[j]=all_list[i].ArrCodeB[j+1];
									} else if (j==ii) {
										tmp_array[j]=all_list[i].ArrCodeB[j-1];
									}
								} else if(tmp=="down") {
									if(j<ii || j>(ii+1)) {
										tmp_array[j]=all_list[i].ArrCodeB[j];
									} else if(j==(ii+1)) {
										tmp_array[j]=all_list[i].ArrCodeB[j-1];
									} else if(j==ii) {
										tmp_array[j]=all_list[i].ArrCodeB[j+1];
									}
								}
							}
							all_list[i].ArrCodeB = new Array();
							all_list[i].ArrCodeB = tmp_array;
							movecode=true;
							BodyInit('');
							SetButton();
							return;
						}
					} else {	//3,4차 카테고리
						if(all_list[i].ArrCodeB[ii].code_a==_code_a && all_list[i].ArrCodeB[ii].code_b==_code_b) {
							for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
								if(_code_a!="000" && _code_b!="000" && _code_c!="000" && _code_d=="000") {	//3차카테고리
									if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code==selcode) {
										if(tmp=="up") {
											if(iii==0) {
												alert("현재 카테고리의 \"최상위\" 입니다. 더 이상 이동할 수 없습니다.");
												return;
											}
										} else if(tmp=="down") {
											if(iii==(all_list[i].ArrCodeB[ii].ArrCodeC.length-1)) {
												alert("현재 카테고리의 \"최하위\" 입니다. 더 이상 이동할 수 없습니다.");
												return;
											}
										}
										for(j=0;j<all_list[i].ArrCodeB[ii].ArrCodeC.length;j++) {
											if(tmp=="up") {
												if(j<(iii-1) || j>iii) {
													tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[j];
												} else if (j==(iii-1)) {
													tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[j+1];
												} else if (j==iii) {
													tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[j-1];
												}
											} else if(tmp=="down") {
												if(j<iii || j>(iii+1)) {
													tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[j];
												} else if(j==(iii+1)) {
													tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[j-1];
												} else if(j==iii) {
													tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[j+1];
												}
											}
										}
										all_list[i].ArrCodeB[ii].ArrCodeC = new Array();
										all_list[i].ArrCodeB[ii].ArrCodeC = tmp_array;
										movecode=true;
										BodyInit('');
										SetButton();
										return;
									}
								} else {	//4차카테고리
									if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_a==_code_a && all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_b==_code_b && all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_c==_code_c) {
										for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
											if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code==selcode) {
												if(tmp=="up") {
													if(iiii==0) {
														alert("현재 카테고리의 \"최상위\" 입니다. 더 이상 이동할 수 없습니다.");
														return;
													}
												} else if(tmp=="down") {
													if(iiii==(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length-1)) {
														alert("현재 카테고리의 \"최하위\" 입니다. 더 이상 이동할 수 없습니다.");
														return;
													}
												}
												for(j=0;j<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;j++) {
													if(tmp=="up") {
														if(j<(iiii-1) || j>iiii) {
															tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[j];
														} else if (j==(iiii-1)) {
															tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[j+1];
														} else if (j==iiii) {
															tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[j-1];
														}
													} else if(tmp=="down") {
														if(j<iiii || j>(iiii+1)) {
															tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[j];
														} else if(j==(iiii+1)) {
															tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[j-1];
														} else if(j==iiii) {
															tmp_array[j]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[j+1];
														}
													}
												}
												all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD = new Array();
												all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD = tmp_array;
												movecode=true;
												BodyInit('');
												SetButton();
												return;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

//카테고리순서 DB에 저장
function MoveSave() {
	if(confirm("수정된 카테고리순서를 저장 하시겠습니까?")) {
		codes="";
		for(i=0;i<all_list.length;i++) {
			codes+="!"+all_list[i].code;
			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
				codes+="@"+all_list[i].ArrCodeB[ii].code;
				for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
					codes+="#"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].code;
					for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
						codes+="$"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code;
					}
				}
			}
		}
		document.form1.codes.value=codes.substring(1);
		document.form1.mode.value="movesave";
		document.form1.action="product_code.process.php";
		document.form1.target="HiddenFrame";
		document.form1.submit();
	}
}
//이동된 카테고리 되돌리기
function MoveCancel() {
	arrall=new Array();
	for(i=0;i<all_list.length;i++) {
		arr_b=new Array();
		for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
			arr_c=new Array();
			for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
				arr_d=new Array();
				for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
					arr_d[all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].sequence]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii];
				}
				all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD=new Array();
				all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD=arr_d;

				arr_c[all_list[i].ArrCodeB[ii].ArrCodeC[iii].sequence]=all_list[i].ArrCodeB[ii].ArrCodeC[iii];
			}
			all_list[i].ArrCodeB[ii].ArrCodeC=new Array();
			all_list[i].ArrCodeB[ii].ArrCodeC=arr_c;

			arr_b[all_list[i].ArrCodeB[ii].sequence]=all_list[i].ArrCodeB[ii];
		}
		all_list[i].ArrCodeB=new Array();
		all_list[i].ArrCodeB=arr_b;
		arrall[all_list[i].sequence]=all_list[i];
	}
	all_list=new Array();
	all_list=arrall;
	movecode=false;
	BodyInit('');
	SetButton();
	alert("이동된 카테고리를 초기값으로 되돌렸습니다.");
	return;
}

function CodeDelete() {
	if(selcode.length==12 && selcode!="000000000000" && (seltype.indexOf("X")!=-1)) {
		if(confirm("선택된 카테고리를 삭제 하시겠습니까?")) {
			document.form1.code.value=selcode;
			document.form1.mode.value="delete";
			document.form1.action="product_code.process.php";
			document.form1.target="HiddenFrame";
			document.form1.submit();
		}
	}
}

function CodeDelete2(_code) {
	if(selcode.length==12 && selcode!="000000000000") {
		document.form1.code.value=_code;
		document.form1.mode.value="delete";
		document.form1.action="product_code.process.php";
		document.form1.target="HiddenFrame";
		document.form1.submit();
	}
}

function CodeMoveResult() {
	movecode=false;
	SetButton();
}

function CodeDeleteResult(_code) {
	//document.location.reload();

	code_a=_code.substring(0,3);
	code_b=_code.substring(3,6);
	code_c=_code.substring(6,9);
	code_d=_code.substring(9,12);
	arrall=new Array();
	j=0;
	for(i=0;i<all_list.length;i++) {
		if(code_a!="000" && code_b=="000" && code_c=="000" && code_d=="000") {	//대카테고리를 삭제하였다.
			if(all_list[i].code!=_code) {
				all_list[i].sequence=j;
				arrall[j]=all_list[i];
				j++;
			} else {
				if(selcode==_code) {
					selcode="";
					seltype="";
				}
			}
		} else {
			all_list[i].sequence=j;
			arrall[j]=all_list[i];
			arr_b=new Array();
			jj=0;
			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
				if(code_a!="000" && code_b!="000" && code_c=="000" && code_d=="000") {
					if(all_list[i].ArrCodeB[ii].code!=_code) {
						all_list[i].ArrCodeB[ii].sequence=jj;
						arr_b[jj]=all_list[i].ArrCodeB[ii];
						jj++;
					} else {
						if(selcode==_code) {
							arrall[j].selected=true;
							arrall[j].open="open";
							selcode=all_list[i].code;
							seltype=all_list[i].type;
						}
					}
				} else {
					all_list[i].ArrCodeB[ii].sequence=jj;
					arr_b[jj]=all_list[i].ArrCodeB[ii];
					arr_c=new Array();
					jjj=0;
					for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
						if(code_a!="000" && code_b!="000" && code_c!="000" && code_d=="000") {
							if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code!=_code) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].sequence=jjj;
								arr_c[jjj]=all_list[i].ArrCodeB[ii].ArrCodeC[iii];
								jjj++;
							} else {
								if(selcode==_code) {
									arr_b[jj].selected=true;
									arr_b[jj].open="open";
									selcode=all_list[i].ArrCodeB[ii].code;
									seltype=all_list[i].ArrCodeB[ii].type;
								}
							}
						} else {
							all_list[i].ArrCodeB[ii].ArrCodeC[iii].sequence=jjj;
							arr_c[jjj]=all_list[i].ArrCodeB[ii].ArrCodeC[iii];
							arr_d=new Array();
							jjjj=0;
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								if(code_a!="000" && code_b!="000" && code_c!="000" && code_d!="000") {
									if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code!=_code) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].sequence=jjjj;
										arr_d[jjjj]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii];
										jjjj++;
									} else {
										if(selcode==_code) {
											arr_c[jjj].selected=true;
											arr_c[jjj].open="open";
											selcode=all_list[i].ArrCodeB[ii].ArrCodeC[iii].code;
											seltype=all_list[i].ArrCodeB[ii].ArrCodeC[iii].type;
										}
									}
								} else {
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].sequence=jjjj;
									arr_d[jjjj]=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii];
									jjjj++;
								}
							}
							arrall[j].ArrCodeB[jj].ArrCodeC[jjj].ArrCodeD=new Array();
							arrall[j].ArrCodeB[jj].ArrCodeC[jjj].ArrCodeD=arr_d;

							jjj++;
						}
					}
					arrall[j].ArrCodeB[jj].ArrCodeC=new Array();
					arrall[j].ArrCodeB[jj].ArrCodeC=arr_c;

					jj++;
				}
			}
			arrall[j].ArrCodeB=new Array();
			arrall[j].ArrCodeB=arr_b;

			j++;
		}
	}
	all_list=new Array();
	all_list=arrall;

	document.form1.code.value="";
	document.form1.parentcode.value="";
	document.form1.action="product_code.property.php";
	document.form1.target="PropertyFrame";
	document.form1.submit();

	BodyInit('');
	SetButton();
}

var divLeft=0;
var defaultLeft=0;
var timeOffset=0;
var setTObj;
var divName="";
var zValue=0;

function divMove()
{
	divLeft+=timeOffset;
		
	if(divLeft >= defaultLeft)
	{
		divLeft=defaultLeft;
		divName.style.left=divLeft;
		divName.style.zIndex = zValue;
		clearTimeout(setTObj);
		setTObj="";
	}
	else
	{
		timeOffset+=20;
		divName.style.left=divLeft;
		setTObj=setTimeout('divMove();',5);
	}
}

function divAction(arg1,arg2)
{
	if(zValue != arg2 && !setTObj)
	{
		defaultLeft = arg1.offsetLeft;
		divLeft = defaultLeft;
		zValue = arg2;
		divName = arg1;
		timeOffset = -70;
		divMove();
	}
}
</script>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 220;HEIGHT: 320;}
</STYLE>

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>카테고리 관리</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">

			
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=code>
			<input type=hidden name=codes>
			<input type=hidden name=parentcode>
			<tr>
				<td>
				
					
					<div class="product_setup_wrap"><!-- 카테고리관리 -->
					<table width="100%" cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td valign=top>

							<!-- 카테고리 트리 -->

							<div class="title_depth3">
								카테고리관리
								<div class="btn_function"><a href="#"><img src="/admin/img/btn/btn_cate_reg.gif" alt="등록" /></a><a href="#"><img src="/admin/img/btn/btn_cate_del.gif" alt="삭제" /></a></div>
							</div>

							<div class="cate_tree_wrap">
									<table cellpadding="0" cellspacing="0" width="100%" height="800">
									<tr>
										<td width="100%" height="100%" valign="top">

										<table cellpadding="0" cellspacing="0" width="100%" height="100%">
											<tr>
												<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;">

												<DIV class=MsgrScroller id=contentDiv style="width:99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
													<DIV id=bodyList>

														<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
															<tr>
																<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
															</tr>
															<tr>
																<!-- 상품카테고리 목록 -->
																<td id="code_list" nowrap valign=top></td>
																<!-- 상품카테고리 목록 끝 -->
															</tr>
														</table>

													</DIV>
												</DIV>

												</td>
											</tr>
										</table>

										</td>
									</tr>
									</table>

							</div>

						</td>
						<!-- 카테고리 트리 -->

						<!-- 설정영역 -->
						<td align=left style="padding-left:30px;">
							<DIV style="width:100%;height:100%;bgcolor:#FFFFFF;"><IFRAME name="PropertyFrame" src="product_code.property.php" width=100% height=840 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></div>
							<!-- 메뉴얼 -->
							<div class="sub_manual_wrap">
								<div class="title"><p>메뉴얼</p></div>
								<dl>
									<dt><span>카테고리생성시 주의사항</span></dt>
									<dd>
										  - 카테고리명은 최대 한글 50자, 영문 100자 이내로 제한되어 있으며, 특수문자는 삼가해 주세요.  <br />
										  - 최상위카테고리 생성시 하위카테고리 유무를 확인 후 등록해 주세요. <br />
										  - "하위카테고리 없음" 선택시 해당 카테고리에서 바로 상품을 등록할 수 있습니다. <br />
									</dd>
								</dl>
								<dl>
									<dt><span>상품정렬</span></dt>
									<dd>
										  - [상품 등록/수정날짜 순서] : 정렬순서는 최근 수정된 상품이 먼저 출력되며 수정안된 상품은 등록순으로 출력됩니다.   <br />
										    &nbsp;&nbsp;<a href="javascript:parent.topframe.GoMenu(4,'product_sort.php');">상품관리 > 카테고리/상품관리 > 상품 진열순서 설정</a> 에서 진열 순서 조절이 가능합니다. <br />
										  - [상품 등록/수정날짜 순서 + 품절상품 뒤로] : 재고있는 상품과 품절된 상품 순으로 구분되어 출력되며 정렬순서는 최근 수정된 상품이 먼저 출력되며 수정안된 상품은 등록순으로 출력됩니다<br />
										     &nbsp;&nbsp;<a href="javascript:parent.topframe.GoMenu(4,'product_sort.php');">상품관리 > 카테고리/상품관리 > 상품 진열순서 설정</a> 에서 진열 순서 조절이 가능합니다.<br />
										  - [상품명 가나다 순서] : 정렬순서는 상품 이름순으로 출력됩니다. <br />
										  - [제조사 가나다 순서] : 정렬순서는 제조사 이름순으로 출력됩니다. <br />
										  - [상품 판매가격 순서] : 정렬순서는 상품 판매가격 낮은 순으로 출력됩니다.

									</dd>
								</dl>
								<dl>
									<dt><span>가상카테고리 생성</span></dt>
									<dd>
										- 카테고리 타입은 일반과 가상으로 구분되며 "가상카테고리"는 일반상품 중에서 수집하여 상품을 진열합니다. <br />
										- 가상카테고리에 상품은 수집된 상품이므로 본래의 일반상품의 수정 또는 재고관리 함께 연동됩니다. 

									</dd>
								</dl>
								<dl>
									<dt><span>상품진열 타입선택</span></dt>
									<dd>
										 - 카테고리 상품진열에서 인기/신상품/추천상품 진열여부와 진열타입, 진열수를 선택할 수 있습니다. <br />
										 - 메인본문 상품진열은 <a href="javascript:parent.topframe.GoMenu(1,'shop_mainproduct.php');">상점관리 > 쇼핑몰 환경 설정 > 상품 진열수/화면설정</a> 에서 선택할 수 있습니다. 

									</dd>
								</dl>
							</div>

						</td>
						<!-- 설정영역 -->

						</tr>
					</table>
					</div><!-- 카테고리관리 -->

					<!-- 페이지 타이틀 -->
					
				</td>
			</tr>
			
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>
			<tr><td height="20"></td></tr>
			
<?/*?>			
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=code>
			<input type=hidden name=codes>
			<input type=hidden name=parentcode>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%" height="910">
				<tr>
					<td valign="top">
					<DIV onmouseover="divAction(this,2);" id="cateidx" style="position:absolute;z-index:0;width:242px;bgcolor:#FFFFFF; "> <!-------------------->
					<table cellpadding="0" cellspacing="0" width="100%" height="870">
					<tr>
						<td width="100%" height="100%" valign="top" background="images/category_boxbg.gif">
						<table cellpadding="0" cellspacing="0" width="100%" height="100%">
						<tr>
							<td bgcolor="#FFFFFF"><IMG SRC="images/product_totoacategory_title.gif" WIDTH=85 HEIGHT=24 ALT=""></td>
						</tr>
						<tr>
							<td><IMG SRC="images/category_box1.gif" border="0"></td>
						</tr>
						<tr>
							<td bgcolor="#0F8FCB" style="padding-top:4pt; padding-bottom:6pt;">
							<table align="center" cellpadding="0" cellspacing="0" width="230">
							<tr>
								<td width="24"><button title="전체 트리확장" id="btn_treeall" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="AllOpen();"><IMG SRC="images/category_btn1.gif" WIDTH=22 HEIGHT=23 border="0"></button></td>
								<td width="24"><button title="선택된 카테고리속성 보기" id="btn_property" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="ViewProperty();"><IMG SRC="images/category_btn2.gif" WIDTH=22 HEIGHT=23 border="0"></button></td>
								<td width="24"><button title="선택된 카테고리에 하위카테고리 추가" id="btn_codeadd" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="NewCode();"><IMG SRC="images/category_btn3.gif" WIDTH=22 HEIGHT=23 border="0"></button></td>
								<td width="24"><button title="선택된 카테고리 삭제" id="btn_codedel" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="CodeDelete();"><IMG SRC="images/category_btn4.gif" WIDTH=22 HEIGHT=23 border="0"></button></td>
								<td width="24"><button title="선택된 카테고리 위로 이동" id="btn_moveup" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="CodeMove('up');"><IMG SRC="images/category_btn5.gif" WIDTH=22 HEIGHT=23 border="0"></button></td>
								<td width="24"><button title="선택된 카테고리 아래로 이동" id="btn_movedown" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="CodeMove('down');"><IMG SRC="images/category_btn6.gif" WIDTH=22 HEIGHT=23 border="0"></button></td>
								<td width="24"><button title="이동된 카테고리 저장" id="btn_movesave" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="MoveSave();"><IMG SRC="images/category_btn7.gif" WIDTH=22 HEIGHT=23 border="0"></button></td>
								<td width="24"><button title="이동된 카테고리 되돌리기" id="btn_movecancel" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="MoveCancel();"><IMG SRC="images/category_btn8.gif" WIDTH=23 HEIGHT=23 border="0"></button></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td><IMG SRC="images/category_box2.gif" border="0"></td>
						</tr>
						<tr>
							<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;">
							<DIV class=MsgrScroller id=contentDiv style="width:99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
							<DIV id=bodyList>
							<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
							<tr>
								<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
							</tr>
							<tr>
								<!-- 상품카테고리 목록 -->
								<td id="code_list" nowrap valign=top></td>
								<!-- 상품카테고리 목록 끝 -->
							</tr>
							</table>
							</DIV>
							</DIV>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td><IMG SRC="images/category_boxdown.gif" border="0"></td>
					</tr>
					</table>
					</div>
					</td>
					<td style="padding-left:84px;"></td>
					<td width="100%" valign="top" height="100%" onmouseover="divAction(document.getElementById('cateidx'),0);"><DIV style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;"><IFRAME name="PropertyFrame" src="product_code.property.php" width=100% height=840 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></div></td>
				</tr>
				<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
				</table>
				</td>
			</tr>
			</form>
<?*/?>			
			<tr><td height=20></td></tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php
$sql = "SELECT * FROM tblproductcode ORDER BY sequence DESC ";
include("codeinit.php");
include("copyright.php");
