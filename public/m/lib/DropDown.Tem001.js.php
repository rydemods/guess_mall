<?php
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
var all_list1 = new Array();
var lista=new Array();
var listb=new Array();
var listc=new Array();
var listd=new Array();

function DeleteFrontZero(str){
	val = new String(str)
	do {
		if (val.length==1) 
			break;
		if (val.substr(0,1)=='0')
			val = val.substr(1, val.length - 1);
		else
			break;
	} while (true);
	return val
}

///Int 형으로 변환한다.
function ToInt(val){
	val = DeleteFrontZero(val);
	return parseInt(val);
}

function CodeList() {
	var argv = CodeList.arguments;   
	var argc = CodeList.arguments.length;
	
	//Property 선언
	this.classname		= "CodeList"								//classname
	this.debug			= false;									//디버깅여부.
	this.code_a			= new String((argc > 0) ? argv[0] : "000");
	this.code_b			= new String((argc > 1) ? argv[1] : "000");
	this.code_c			= new String((argc > 2) ? argv[2] : "000");
	this.code_d			= new String((argc > 3) ? argv[3] : "000");
	this.type			= new String((argc > 4) ? argv[4] : "");
	this.code_name		= new String((argc > 5) ? argv[5] : "");
}

function CodeAList() {
	var argv = CodeAList.arguments;   
	var argc = CodeAList.arguments.length;  

	//Property 선언
	this.classname	= "CodeAList"							//classname
	this.debug		= false;									//디버깅여부.
	this.CodeA	= new String((argc > 0) ? argv[0] : "000");
	this.CodeA_Name	= new String((argc > 1) ? argv[1] : "");
	this.CodeA_Type	= new String((argc > 2) ? argv[2] : "");
	this.CodeB	= new Array();
}

function CodeBList() {
	var argv = CodeBList.arguments;   
	var argc = CodeBList.arguments.length;  

	//Property 선언
	this.classname	= "CodeBList"							//classname
	this.debug		= false;									//디버깅여부.
	this.CodeA	= new String((argc > 0) ? argv[0] : "000");
	this.CodeB	= new String((argc > 1) ? argv[1] : "000");
	this.CodeB_Name	= new String((argc > 2) ? argv[2] : "");
	this.CodeB_Type	= new String((argc > 3) ? argv[3] : "");
	this.CodeC	= new Array();
}

function CodeCList() {
	var argv = CodeCList.arguments;   
	var argc = CodeCList.arguments.length;  

	//Property 선언
	this.classname	= "CodeCList"							//classname
	this.debug		= false;									//디버깅여부.
	this.CodeA	= new String((argc > 0) ? argv[0] : "000");
	this.CodeB	= new String((argc > 1) ? argv[1] : "000");
	this.CodeC	= new String((argc > 2) ? argv[2] : "000");
	this.CodeC_Name	= new String((argc > 3) ? argv[3] : "");
	this.CodeC_Type	= new String((argc > 4) ? argv[4] : "");
	this.CodeD	= new Array();
}

function CodeDList() {
	var argv = CodeDList.arguments;   
	var argc = CodeDList.arguments.length;  

	//Property 선언
	this.classname	= "CodeDList"							//classname
	this.debug		= false;									//디버깅여부.
	this.CodeA	= new String((argc > 0) ? argv[0] : "000");
	this.CodeB	= new String((argc > 1) ? argv[1] : "000");
	this.CodeC	= new String((argc > 2) ? argv[2] : "000");
	this.CodeD	= new String((argc > 3) ? argv[3] : "000");
	this.CodeD_Name	= new String((argc > 4) ? argv[4] : "");
	this.CodeD_Type	= new String((argc > 5) ? argv[5] : "");
}


function SearchCodeInit(code_a, code_b, code_c, code_d) {
	if(typeof(document.form1.code_a)!="object") return;

	var d = new Option("1차 카테고리");
	document.form1.code_a.options[0] = d;
	document.form1.code_a.options[0].value = "";
	for(var i=0;i<all_list1.length;i++) {
		var plus = "";
		if (all_list1[i].CodeA_Type=="LX" || all_list1[i].CodeA_Type=="TX") {
			plus = " (단일)";
		}
		var d = new Option(all_list1[i].CodeA_Name+plus);
		document.form1.code_a.options[i+1] = d;
		document.form1.code_a.options[i+1].value = all_list1[i].CodeA;

		if (all_list1[i].CodeA == code_a) {
			document.form1.code_a.selectedIndex = i+1;
			if(typeof(document.form1.code_b)!="object") return;
			try {
				if(all_list1[i].CodeA_Type=="LX" || all_list1[i].CodeA_Type=="TX") {
					var d = new Option("단일 카테고리");
					document.form1.code_b.options[0] = d;
					document.form1.code_b.options[0].value = "000";

					var d = new Option("단일 카테고리");
					document.form1.code_c.options[0] = d;
					document.form1.code_c.options[0].value = "000";

					var d = new Option("단일 카테고리");
					document.form1.code_d.options[0] = d;
					document.form1.code_d.options[0].value = "000";
				} else {
					var d = new Option("2차 카테고리");
					document.form1.code_b.options[0] = d;
					document.form1.code_b.options[0].value = "";
					for(var j=0;j<all_list1[i].CodeB.length;j++) {
						plus = "";
						if (all_list1[i].CodeB[j].CodeB_Type=="LMX" || all_list1[i].CodeB[j].CodeB_Type=="TMX") {
							plus = " (단일)";
						}
						var d = new Option(all_list1[i].CodeB[j].CodeB_Name+plus);
						document.form1.code_b.options[j+1] = d;
						document.form1.code_b.options[j+1].value = all_list1[i].CodeB[j].CodeB;
						if (all_list1[i].CodeB[j].CodeB == code_b) {
							document.form1.code_b.selectedIndex = j+1;
							if(typeof(document.form1.code_c)!="object") return;
							try {
								if(all_list1[i].CodeB[j].CodeB_Type=="LMX" || all_list1[i].CodeB[j].CodeB=="TMX") {
									var d = new Option("단일 카테고리");
									document.form1.code_c.options[0] = d;
									document.form1.code_c.options[0].value = "000";

									var d = new Option("단일 카테고리");
									document.form1.code_d.options[0] = d;
									document.form1.code_d.options[0].value = "000";
								} else {
									var d = new Option("3차 카테고리");
									document.form1.code_c.options[0] = d;
									document.form1.code_c.options[0].value = "";
									for(var y=0;y<all_list1[i].CodeB[j].CodeC.length;y++) {
										plus = "";
										if (all_list1[i].CodeB[j].CodeC[y].CodeC_Type=="LMX" || all_list1[i].CodeB[j].CodeC[y].CodeC_Type=="TMX") {
											plus = " (단일)";
										}
										var d = new Option(all_list1[i].CodeB[j].CodeC[y].CodeC_Name+plus);
										document.form1.code_c.options[y+1] = d;
										document.form1.code_c.options[y+1].value = all_list1[i].CodeB[j].CodeC[y].CodeC;
										if (all_list1[i].CodeB[j].CodeC[y].CodeC == code_c) {
											document.form1.code_c.selectedIndex = y+1;
											if(typeof(document.form1.code_d)!="object") return;
											try {
												if(all_list1[i].CodeB[j].CodeC[y].CodeC_Type=="LMX" || all_list1[i].CodeB[j].CodeC[y].CodeC_Type=="TMX") {
													var d = new Option("단일 카테고리");
													document.form1.code_d.options[0] = d;
													document.form1.code_d.options[0].value = "000";
												} else {
													var d = new Option("4차 카테고리");
													document.form1.code_d.options[0] = d;
													document.form1.code_d.options[0].value = "";
													for(var z=0;z<all_list1[i].CodeB[j].CodeC[y].CodeD.length;z++) {
														var d = new Option(all_list1[i].CodeB[j].CodeC[y].CodeD[z].CodeD_Name);
														document.form1.code_d.options[z+1] = d;
														document.form1.code_d.options[z+1].value = all_list1[i].CodeB[j].CodeC[y].CodeD[z].CodeD;
														if (all_list1[i].CodeB[j].CodeC[y].CodeD[z].CodeD == code_d) {
															document.form1.code_d.selectedIndex = z+1;
														}
													}
												}
											} catch (e) {}
										}
									}
								}
							} catch (e) {}
						}
					}
				}
			} catch (e) {}
		}
	}
}


function SearchChangeCate(sel, gbn) {
	if (gbn == 1) {
		if(typeof(document.form1.code_a)!="object") return;

		if(typeof(document.form1.code_b)=="object") {
			document.form1.code_b.length = 0;
			var d = new Option("2차 카테고리");
			document.form1.code_b.options[0] = d;
			document.form1.code_b.options[0].value = "";
		}

		if(typeof(document.form1.code_c)=="object") {
			document.form1.code_c.length = 0;
			var d = new Option("3차 카테고리");
			document.form1.code_c.options[0] = d;
			document.form1.code_c.options[0].value = "";
		}

		if(typeof(document.form1.code_d)=="object") {
			document.form1.code_d.length = 0;
			var d = new Option("4차 카테고리");
			document.form1.code_d.options[0] = d;
			document.form1.code_d.options[0].value = "";
		}

		for(var i=0;i<all_list1.length;i++) {
			if (all_list1[i].CodeA == sel.value) {
				if(typeof(document.form1.code_b)!="object") return;
				try {
					if(all_list1[i].CodeA_Type=="LX" || all_list1[i].CodeA_Type=="TX") {
						var d = new Option("단일 카테고리");
						document.form1.code_b.options[0] = d;
						document.form1.code_b.options[0].value = "000";

						var d = new Option("단일 카테고리");
						document.form1.code_c.options[0] = d;
						document.form1.code_c.options[0].value = "000";

						var d = new Option("단일 카테고리");
						document.form1.code_d.options[0] = d;
						document.form1.code_d.options[0].value = "000";
					} else {
						for(var j=0;j<all_list1[i].CodeB.length;j++) {
							var plus = "";
							if (all_list1[i].CodeB[j].CodeB_Type=="LMX" || all_list1[i].CodeB[j].CodeB_Type=="TMX") {
								plus = " (단일)";
							}
							var d = new Option(all_list1[i].CodeB[j].CodeB_Name+plus);
							document.form1.code_b.options[j+1] = d;
							document.form1.code_b.options[j+1].value = all_list1[i].CodeB[j].CodeB;
						}
					}
				} catch (e) {}
				break;
			}
		}
	} else if (gbn == 2) {
		if(typeof(document.form1.code_b)!="object") return;

		if(typeof(document.form1.code_c)=="object") {
			document.form1.code_c.length = 0;
			var d = new Option("3차 카테고리");
			document.form1.code_c.options[0] = d;
			document.form1.code_c.options[0].value = "";
		}

		if(typeof(document.form1.code_d)=="object") {
			document.form1.code_d.length = 0;
			var d = new Option("4차 카테고리");
			document.form1.code_d.options[0] = d;
			document.form1.code_d.options[0].value = "";
		}
		var code_a=document.form1.code_a.value;
		for(var i=0;i<all_list1.length;i++) {
			if (all_list1[i].CodeA == code_a) {
				try {
					for(var j=0;j<all_list1[i].CodeB.length;j++) {
						if (all_list1[i].CodeB[j].CodeB == sel.value) {
							try {
								if(all_list1[i].CodeB[j].CodeB_Type=="LMX" || all_list1[i].CodeB[j].CodeB_Type=="TMX") {
									var d = new Option("단일 카테고리");
									document.form1.code_c.options[0] = d;
									document.form1.code_c.options[0].value = "000";

									var d = new Option("단일 카테고리");
									document.form1.code_d.options[0] = d;
									document.form1.code_d.options[0].value = "000";
								} else {
									for(var y=0;y<all_list1[i].CodeB[j].CodeC.length;y++) {
										var plus = "";
										if (all_list1[i].CodeB[j].CodeC[y].CodeC_Type=="LMX" || all_list1[i].CodeB[j].CodeC[y].CodeC_Type=="TMX") {
											plus = " (단일)";
										}
										var d = new Option(all_list1[i].CodeB[j].CodeC[y].CodeC_Name+plus);
										document.form1.code_c.options[y+1] = d;
										document.form1.code_c.options[y+1].value = all_list1[i].CodeB[j].CodeC[y].CodeC;
									}
								}
							} catch (e) {}
							break;
						}
					}	
				} catch (e) {}
				break;
			}
		}
	}  else if (gbn == 3) {
		if(typeof(document.form1.code_c)!="object") return;

		if(typeof(document.form1.code_d)=="object") {
			document.form1.code_d.length = 0;
			var d = new Option("4차 카테고리");
			document.form1.code_d.options[0] = d;
			document.form1.code_d.options[0].value = "";
		}

		var code_a = document.form1.code_a.value;
		var code_b = document.form1.code_b.value;
		for(var i=0;i<all_list1.length;i++) {
			if (all_list1[i].CodeA == code_a) {
				try {
					for(var j=0;j<all_list1[i].CodeB.length;j++) {
						if (all_list1[i].CodeB[j].CodeB == code_b) {
							try {
								for(var y=0;y<all_list1[i].CodeB[j].CodeC.length;y++) {
									if (all_list1[i].CodeB[j].CodeC[y].CodeC == sel.value) {
										try {
											if (all_list1[i].CodeB[j].CodeC[y].CodeC_Type=="LMX" || all_list1[i].CodeB[j].CodeC[y].CodeC_Type=="TMX") {
												var d = new Option("단일 카테고리");
												document.form1.code_d.options[0] = d;
												document.form1.code_d.options[0].value = "000";
											} else {
												for(var z=0;z<all_list1[i].CodeB[j].CodeC[y].CodeD.length;z++) {
													var d = new Option(all_list1[i].CodeB[j].CodeC[y].CodeD[z].CodeD_Name);
													document.form1.code_d.options[z+1] = d;
													document.form1.code_d.options[z+1].value = all_list1[i].CodeB[j].CodeC[y].CodeD[z].CodeD;
												}
											}
										} catch (e) {}
										break;
									}
								}	
							} catch (e) {}
							break;
						}
					}	
				} catch (e) {}
				break;
			}
		}
	}
}






function CodeInit() {
	j=0;
	for(i=0;i<lista.length;i++) {
		if(lista[i].type=="L" || lista[i].type=="T" || lista[i].type=="LX" || lista[i].type=="TX") {//대분류 뽑기
			var calist=new CodeAList();
			calist.CodeA=lista[i].code_a;
			calist.CodeA_Name=lista[i].code_name;
			calist.CodeA_Type=lista[i].type;
			jj=0;
			for(ii=0;ii<listb.length;ii++) {
				if(lista[i].code_a==listb[ii].code_a) {
					var cblist=new CodeBList();
					cblist.CodeA=listb[ii].code_a;
					cblist.CodeB=listb[ii].code_b;
					cblist.CodeB_Name=listb[ii].code_name;
					cblist.CodeB_Type=listb[ii].type;
					jjj=0;
					for(iii=0;iii<listc.length;iii++) {
						if(listb[ii].code_a==listc[iii].code_a && listb[ii].code_b==listc[iii].code_b) {
							var cclist=new CodeCList();
							cclist.CodeA=listc[iii].code_a;
							cclist.CodeB=listc[iii].code_b;
							cclist.CodeC=listc[iii].code_c;
							cclist.CodeC_Name=listc[iii].code_name;
							cclist.CodeC_Type=listc[iii].type;
							jjjj=0;
							for(iiii=0;iiii<listd.length;iiii++) {
								if(listc[iii].code_a==listd[iiii].code_a && listc[iii].code_b==listd[iiii].code_b && listc[iii].code_c==listd[iiii].code_c) {
									var cdlist=new CodeDList();
									cdlist.CodeA=listd[iiii].code_a;
									cdlist.CodeB=listd[iiii].code_b;
									cdlist.CodeC=listd[iiii].code_c;
									cdlist.CodeD=listd[iiii].code_d;
									cdlist.CodeD_Name=listd[iiii].code_name;
									cdlist.CodeD_Type=listd[iiii].type;

									cclist.CodeD[jjjj]=cdlist;
									cdlist=null;
									jjjj++;
								}
							}
							cblist.CodeC[jjj]=cclist;
							cclist=null;
							jjj++;
						}
					}
					calist.CodeB[jj]=cblist;
					cblist=null;
					jj++;
				}
			}
			all_list1[i] = calist;
			calist=null;
			j++;
		}
	}
}
