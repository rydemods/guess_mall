<?php
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
var all_list2 = new Array();
var t_lista=new Array();
var t_listb=new Array();
var t_listc=new Array();
var t_listd=new Array();

function T_DeleteFrontZero(str){
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
function T_ToInt(val){
	val = T_DeleteFrontZero(val);
	return parseInt(val);
}

function T_CodeList() {
	var t_argv = T_CodeList.arguments;   
	var t_argc = T_CodeList.arguments.length;
	
	//Property 선언
	this.classname		= "T_CodeList"								//classname
	this.debug			= false;									//디버깅여부.
	this.t_code_a			= new String((t_argc > 0) ? t_argv[0] : "000");
	this.t_code_b			= new String((t_argc > 1) ? t_argv[1] : "000");
	this.t_code_c			= new String((t_argc > 2) ? t_argv[2] : "000");
	this.t_code_d			= new String((t_argc > 3) ? t_argv[3] : "000");
	this.type			= new String((t_argc > 4) ? t_argv[4] : "");
	this.code_name		= new String((t_argc > 5) ? t_argv[5] : "");
}

function T_CodeAList() {
	var t_argv = T_CodeAList.arguments;   
	var t_argc = T_CodeAList.arguments.length;  

	//Property 선언
	this.classname	= "T_CodeAList"							//classname
	this.debug		= false;									//디버깅여부.
	this.CodeA	= new String((t_argc > 0) ? t_argv[0] : "000");
	this.CodeA_Name	= new String((t_argc > 1) ? t_argv[1] : "");
	this.CodeA_Type	= new String((t_argc > 2) ? t_argv[2] : "");
	this.CodeB	= new Array();
}

function T_CodeBList() {
	var t_argv = T_CodeBList.arguments;   
	var t_argc = T_CodeBList.arguments.length;  

	//Property 선언
	this.classname	= "T_CodeBList"							//classname
	this.debug		= false;									//디버깅여부.
	this.CodeA	= new String((t_argc > 0) ? t_argv[0] : "000");
	this.CodeB	= new String((t_argc > 1) ? t_argv[1] : "000");
	this.CodeB_Name	= new String((t_argc > 2) ? t_argv[2] : "");
	this.CodeB_Type	= new String((t_argc > 3) ? t_argv[3] : "");
	this.CodeC	= new Array();
}

function T_CodeCList() {
	var t_argv = T_CodeCList.arguments;   
	var t_argc = T_CodeCList.arguments.length;  

	//Property 선언
	this.classname	= "T_CodeCList"							//classname
	this.debug		= false;									//디버깅여부.
	this.CodeA	= new String((t_argc > 0) ? t_argv[0] : "000");
	this.CodeB	= new String((t_argc > 1) ? t_argv[1] : "000");
	this.CodeC	= new String((t_argc > 2) ? t_argv[2] : "000");
	this.CodeC_Name	= new String((t_argc > 3) ? t_argv[3] : "");
	this.CodeC_Type	= new String((t_argc > 4) ? t_argv[4] : "");
	this.CodeD	= new Array();
}

function T_CodeDList() {
	var t_argv = T_CodeDList.arguments;   
	var t_argc = T_CodeDList.arguments.length;  

	//Property 선언
	this.classname	= "T_CodeDList"							//classname
	this.debug		= false;									//디버깅여부.
	this.CodeA	= new String((t_argc > 0) ? t_argv[0] : "000");
	this.CodeB	= new String((t_argc > 1) ? t_argv[1] : "000");
	this.CodeC	= new String((t_argc > 2) ? t_argv[2] : "000");
	this.CodeD	= new String((t_argc > 3) ? t_argv[3] : "000");
	this.CodeD_Name	= new String((t_argc > 4) ? t_argv[4] : "");
	this.CodeD_Type	= new String((t_argc > 5) ? t_argv[5] : "");
}


function T_SearchCodeInit(t_code_a, t_code_b, t_code_c, t_code_d) {

	if(typeof(document.forms[0].t_code_a)!="object") return;

	var d = new Option("〓〓 1차 카테고리 〓〓");
	document.forms[0].t_code_a.options[0] = d;
	document.forms[0].t_code_a.options[0].value = "";
	for(var i=0;i<all_list2.length;i++) {
		var plus = "";
		if (all_list2[i].CodeA_Type=="LX" || all_list2[i].CodeA_Type=="TX") {
			<!--plus = " (단일)";-->
		}
		var d = new Option(all_list2[i].CodeA_Name+plus);
		document.forms[0].t_code_a.options[i+1] = d;
		document.forms[0].t_code_a.options[i+1].value = all_list2[i].CodeA;

		if (all_list2[i].CodeA == t_code_a) {
			document.forms[0].t_code_a.selectedIndex = i+1;
			if(typeof(document.forms[0].t_code_b)!="object") return;
			try {
				if(all_list2[i].CodeA_Type=="LX" || all_list2[i].CodeA_Type=="TX") {
					var d = new Option("〓〓 카테고리 〓〓");
					document.forms[0].t_code_b.options[0] = d;
					document.forms[0].t_code_b.options[0].value = "000";

					var d = new Option("〓〓 카테고리 〓〓");
					document.forms[0].t_code_c.options[0] = d;
					document.forms[0].t_code_c.options[0].value = "000";

					var d = new Option("〓〓 카테고리 〓〓");
					document.forms[0].t_code_d.options[0] = d;
					document.forms[0].t_code_d.options[0].value = "000";
				} else {
					var d = new Option("〓〓 2차 카테고리 〓〓");
					document.forms[0].t_code_b.options[0] = d;
					document.forms[0].t_code_b.options[0].value = "";
					for(var j=0;j<all_list2[i].CodeB.length;j++) {
						plus = "";
						if (all_list2[i].CodeB[j].CodeB_Type=="LMX" || all_list2[i].CodeB[j].CodeB_Type=="TMX") {
							<!--plus = " (단일)";-->
						}
						var d = new Option(all_list2[i].CodeB[j].CodeB_Name+plus);
						document.forms[0].t_code_b.options[j+1] = d;
						document.forms[0].t_code_b.options[j+1].value = all_list2[i].CodeB[j].CodeB;
						if (all_list2[i].CodeB[j].CodeB == t_code_b) {
							document.forms[0].t_code_b.selectedIndex = j+1;
							if(typeof(document.forms[0].t_code_c)!="object") return;
							try {
								if(all_list2[i].CodeB[j].CodeB_Type=="LMX" || all_list2[i].CodeB[j].CodeB=="TMX") {
									var d = new Option("〓〓 카테고리 〓〓");
									document.forms[0].t_code_c.options[0] = d;
									document.forms[0].t_code_c.options[0].value = "000";

									var d = new Option("〓〓 카테고리 〓〓");
									document.forms[0].t_code_d.options[0] = d;
									document.forms[0].t_code_d.options[0].value = "000";
								} else {
									var d = new Option("〓〓 3차 카테고리 〓〓");
									document.forms[0].t_code_c.options[0] = d;
									document.forms[0].t_code_c.options[0].value = "";
									for(var y=0;y<all_list2[i].CodeB[j].CodeC.length;y++) {
										plus = "";
										if (all_list2[i].CodeB[j].CodeC[y].CodeC_Type=="LMX" || all_list2[i].CodeB[j].CodeC[y].CodeC_Type=="TMX") {
											<!--plus = " (단일)";-->
										}
										var d = new Option(all_list2[i].CodeB[j].CodeC[y].CodeC_Name+plus);
										document.forms[0].t_code_c.options[y+1] = d;
										document.forms[0].t_code_c.options[y+1].value = all_list2[i].CodeB[j].CodeC[y].CodeC;
										if (all_list2[i].CodeB[j].CodeC[y].CodeC == t_code_c) {
											document.forms[0].t_code_c.selectedIndex = y+1;
											if(typeof(document.forms[0].t_code_d)!="object") return;
											try {
												if(all_list2[i].CodeB[j].CodeC[y].CodeC_Type=="LMX" || all_list2[i].CodeB[j].CodeC[y].CodeC_Type=="TMX") {
													var d = new Option("〓〓 카테고리 〓〓");
													document.forms[0].t_code_d.options[0] = d;
													document.forms[0].t_code_d.options[0].value = "000";
												} else {
													var d = new Option("〓〓 4차 카테고리 〓〓");
													document.forms[0].t_code_d.options[0] = d;
													document.forms[0].t_code_d.options[0].value = "";
													for(var z=0;z<all_list2[i].CodeB[j].CodeC[y].CodeD.length;z++) {
														var d = new Option(all_list2[i].CodeB[j].CodeC[y].CodeD[z].CodeD_Name);
														document.forms[0].t_code_d.options[z+1] = d;
														document.forms[0].t_code_d.options[z+1].value = all_list2[i].CodeB[j].CodeC[y].CodeD[z].CodeD;
														if (all_list2[i].CodeB[j].CodeC[y].CodeD[z].CodeD == t_code_d) {
															document.forms[0].t_code_d.selectedIndex = z+1;
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


function T_SearchChangeCate(sel, gbn) {
	if (gbn == 1) {
		if(typeof(document.forms[0].t_code_a)!="object") return;

		if(typeof(document.forms[0].t_code_b)=="object") {
			document.forms[0].t_code_b.length = 0;
			var d = new Option("〓〓 2차 카테고리 〓〓");
			document.forms[0].t_code_b.options[0] = d;
			document.forms[0].t_code_b.options[0].value = "";
		}

		if(typeof(document.forms[0].t_code_c)=="object") {
			document.forms[0].t_code_c.length = 0;
			var d = new Option("〓〓 3차 카테고리 〓〓");
			document.forms[0].t_code_c.options[0] = d;
			document.forms[0].t_code_c.options[0].value = "";
		}

		if(typeof(document.forms[0].t_code_d)=="object") {
			document.forms[0].t_code_d.length = 0;
			var d = new Option("〓〓 4차 카테고리 〓〓");
			document.forms[0].t_code_d.options[0] = d;
			document.forms[0].t_code_d.options[0].value = "";
		}

		for(var i=0;i<all_list2.length;i++) {
			if (all_list2[i].CodeA == sel.value) {
				if(typeof(document.forms[0].t_code_b)!="object") return;
				try {
					if(all_list2[i].CodeA_Type=="LX" || all_list2[i].CodeA_Type=="TX") {
						var d = new Option("〓〓 카테고리 〓〓");
						document.forms[0].t_code_b.options[0] = d;
						document.forms[0].t_code_b.options[0].value = "000";

						var d = new Option("〓〓 카테고리 〓〓");
						document.forms[0].t_code_c.options[0] = d;
						document.forms[0].t_code_c.options[0].value = "000";

						var d = new Option("〓〓 카테고리 〓〓");
						document.forms[0].t_code_d.options[0] = d;
						document.forms[0].t_code_d.options[0].value = "000";
					} else {
						for(var j=0;j<all_list2[i].CodeB.length;j++) {
							var plus = "";
							if (all_list2[i].CodeB[j].CodeB_Type=="LMX" || all_list2[i].CodeB[j].CodeB_Type=="TMX") {
								<!--plus = " (단일)";-->
							}
							var d = new Option(all_list2[i].CodeB[j].CodeB_Name+plus);
							document.forms[0].t_code_b.options[j+1] = d;
							document.forms[0].t_code_b.options[j+1].value = all_list2[i].CodeB[j].CodeB;
						}
					}
				} catch (e) {}
				break;
			}
		}
	} else if (gbn == 2) {
		if(typeof(document.forms[0].t_code_b)!="object") return;

		if(typeof(document.forms[0].t_code_c)=="object") {
			document.forms[0].t_code_c.length = 0;
			var d = new Option("〓〓 3차 카테고리 〓〓");
			document.forms[0].t_code_c.options[0] = d;
			document.forms[0].t_code_c.options[0].value = "";
		}

		if(typeof(document.forms[0].t_code_d)=="object") {
			document.forms[0].t_code_d.length = 0;
			var d = new Option("〓〓 4차 카테고리 〓〓");
			document.forms[0].t_code_d.options[0] = d;
			document.forms[0].t_code_d.options[0].value = "";
		}
		var t_code_a=document.forms[0].t_code_a.value;
		for(var i=0;i<all_list2.length;i++) {
			if (all_list2[i].CodeA == t_code_a) {
				try {
					for(var j=0;j<all_list2[i].CodeB.length;j++) {
						if (all_list2[i].CodeB[j].CodeB == sel.value) {
							try {
								if(all_list2[i].CodeB[j].CodeB_Type=="LMX" || all_list2[i].CodeB[j].CodeB_Type=="TMX") {
									var d = new Option("〓〓 카테고리 〓〓");
									document.forms[0].t_code_c.options[0] = d;
									document.forms[0].t_code_c.options[0].value = "000";

									var d = new Option("〓〓 카테고리 〓〓");
									document.forms[0].t_code_d.options[0] = d;
									document.forms[0].t_code_d.options[0].value = "000";
								} else {
									for(var y=0;y<all_list2[i].CodeB[j].CodeC.length;y++) {
										var plus = "";
										if (all_list2[i].CodeB[j].CodeC[y].CodeC_Type=="LMX" || all_list2[i].CodeB[j].CodeC[y].CodeC_Type=="TMX") {
											<!--plus = " (단일)";-->
										}
										var d = new Option(all_list2[i].CodeB[j].CodeC[y].CodeC_Name+plus);
										document.forms[0].t_code_c.options[y+1] = d;
										document.forms[0].t_code_c.options[y+1].value = all_list2[i].CodeB[j].CodeC[y].CodeC;
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
		if(typeof(document.forms[0].t_code_c)!="object") return;

		if(typeof(document.forms[0].t_code_d)=="object") {
			document.forms[0].t_code_d.length = 0;
			var d = new Option("〓〓 4차 카테고리 〓〓");
			document.forms[0].t_code_d.options[0] = d;
			document.forms[0].t_code_d.options[0].value = "";
		}

		var t_code_a = document.forms[0].t_code_a.value;
		var t_code_b = document.forms[0].t_code_b.value;
		for(var i=0;i<all_list2.length;i++) {
			if (all_list2[i].CodeA == t_code_a) {
				try {
					for(var j=0;j<all_list2[i].CodeB.length;j++) {
						if (all_list2[i].CodeB[j].CodeB == t_code_b) {
							try {
								for(var y=0;y<all_list2[i].CodeB[j].CodeC.length;y++) {
									if (all_list2[i].CodeB[j].CodeC[y].CodeC == sel.value) {
										try {
											if (all_list2[i].CodeB[j].CodeC[y].CodeC_Type=="LMX" || all_list2[i].CodeB[j].CodeC[y].CodeC_Type=="TMX") {
												var d = new Option("〓〓 카테고리 〓〓");
												document.forms[0].t_code_d.options[0] = d;
												document.forms[0].t_code_d.options[0].value = "000";
											} else {
												for(var z=0;z<all_list2[i].CodeB[j].CodeC[y].CodeD.length;z++) {
													var d = new Option(all_list2[i].CodeB[j].CodeC[y].CodeD[z].CodeD_Name);
													document.forms[0].t_code_d.options[z+1] = d;
													document.forms[0].t_code_d.options[z+1].value = all_list2[i].CodeB[j].CodeC[y].CodeD[z].CodeD;
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






function T_CodeInit() {
	j=0;
	for(i=0;i<t_lista.length;i++) {
		if(t_lista[i].type=="L" || t_lista[i].type=="T" || t_lista[i].type=="LX" || t_lista[i].type=="TX") {//대카테고리 뽑기
			var calist=new T_CodeAList();
			calist.CodeA=t_lista[i].t_code_a;
			calist.CodeA_Name=t_lista[i].code_name;
			calist.CodeA_Type=t_lista[i].type;
			jj=0;
			for(ii=0;ii<t_listb.length;ii++) {
				if(t_lista[i].t_code_a==t_listb[ii].t_code_a) {
					var cblist=new T_CodeBList();
					cblist.CodeA=t_listb[ii].t_code_a;
					cblist.CodeB=t_listb[ii].t_code_b;
					cblist.CodeB_Name=t_listb[ii].code_name;
					cblist.CodeB_Type=t_listb[ii].type;
					jjj=0;
					for(iii=0;iii<t_listc.length;iii++) {
						if(t_listb[ii].t_code_a==t_listc[iii].t_code_a && t_listb[ii].t_code_b==t_listc[iii].t_code_b) {
							var cclist=new T_CodeCList();
							cclist.CodeA=t_listc[iii].t_code_a;
							cclist.CodeB=t_listc[iii].t_code_b;
							cclist.CodeC=t_listc[iii].t_code_c;
							cclist.CodeC_Name=t_listc[iii].code_name;
							cclist.CodeC_Type=t_listc[iii].type;
							jjjj=0;
							for(iiii=0;iiii<t_listd.length;iiii++) {
								if(t_listc[iii].t_code_a==t_listd[iiii].t_code_a && t_listc[iii].t_code_b==t_listd[iiii].t_code_b && t_listc[iii].t_code_c==t_listd[iiii].t_code_c) {
									var cdlist=new T_CodeDList();
									cdlist.CodeA=t_listd[iiii].t_code_a;
									cdlist.CodeB=t_listd[iiii].t_code_b;
									cdlist.CodeC=t_listd[iiii].t_code_c;
									cdlist.CodeD=t_listd[iiii].t_code_d;
									cdlist.CodeD_Name=t_listd[iiii].code_name;
									cdlist.CodeD_Type=t_listd[iiii].type;

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
			all_list2[i] = calist;
			calist=null;
			j++;
		}
	}
}
