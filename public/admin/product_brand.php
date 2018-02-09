<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$brUseChk = 1;
extract($_REQUEST);

$sql = "SELECT etctype FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
$etctype= "";
$branduse="";
$brandleft="";
$brandlefty="";
$brandleftl="";
$brandpro="";
$brandmap="";
$brandmapt="";
if($row=pmysql_fetch_object($result)) {
	if (ord($row->etctype)) {
		$etctemp = @explode("",$row->etctype);
		
		for($i=0; $i<count($etctemp); $i++) {
			if (ord($etctemp[$i])) {
				if(ord($etctemp[$i][9]) && substr($etctemp[$i],0,9) == "BRANDUSE=") {
					$branduse=$etctemp[$i][9];
				} elseif(ord($etctemp[$i][10]) && substr($etctemp[$i],0,10) == "BRANDLEFT=") {
					$brandleft=$etctemp[$i][10];
				} elseif(ord(substr($etctemp[$i],11,3)) && substr($etctemp[$i],0,11) == "BRANDLEFTY=") {
					$brandlefty=substr($etctemp[$i],11,3);
				} elseif(ord($etctemp[$i][11]) && substr($etctemp[$i],0,11) == "BRANDLEFTL=") {
					$brandleftl=$etctemp[$i][11];
				} elseif(ord($etctemp[$i][9]) && substr($etctemp[$i],0,9) == "BRANDPRO=") {
					$brandpro=$etctemp[$i][9];
				} elseif(ord($etctemp[$i][9]) && substr($etctemp[$i],0,9) == "BRANDMAP=") {
					$brandmap=$etctemp[$i][9];
				} elseif(ord($etctemp[$i][10]) && substr($etctemp[$i],0,10) == "BRANDMAPT=") {
					$brandmapt=$etctemp[$i][10];
				} else {
					$etctempvalue[] = $etctemp[$i];
				}
			} else {
				$etctempvalue[] = "";
			}
		}

		$etctype = @implode("",$etctempvalue);
	}
}
pmysql_free_result($result);

$type=$_POST["type"];
$up_branduse=$_POST["up_branduse"];
$up_brandleft=$_POST["up_brandleft"];
$up_brandlefty=(int)$_POST["up_brandlefty"];
$up_brandleftl=$_POST["up_brandleftl"];
$up_brandpro=$_POST["up_brandpro"];
$up_brandmap=$_POST["up_brandmap"];
$up_brandmapt=$_POST["up_brandmapt"];
$up_display_yn = $_POST['display_yn'];
$logo_text = $_POST['logo_text'];
$up_bridx = $_POST['up_bridx'];

$imagepath = $Dir.DataDir."shopimages/brand/";
$brand_file = new FILE($imagepath); //파일 클래스 사용

if($type=="up") {
	$branduse="N";
	$brandleft="N";
	$brandlefty="";
	$brandleftl="N";
	$brandpro="N";
	$brandmap="N";
	$brandmapt="N";
	if(ord($up_branduse) && $up_branduse=="Y") { 
		$etctype.="BRANDUSE=Y";
		$branduse="Y";
		if(ord($up_brandleft) && $up_brandleft=="Y") {
			$etctype.="BRANDLEFT=Y";
			$brandleft="Y";

			if($up_brandlefty>0) {
				$etctype.="BRANDLEFTY={$up_brandlefty}";
				$brandlefty=$up_brandlefty;
			}
			if(ord($up_brandleftl) && ($up_brandleftl=="Y" || $up_brandleftl=="B" || $up_brandleftl=="A")) {
				$etctype.="BRANDLEFTL={$up_brandleftl}";
				$brandleftl=$up_brandleftl;
			}
		}
		if(ord($up_brandpro) && $up_brandpro=="Y") {
			$etctype.="BRANDPRO=Y";
			$brandpro="Y";
		}
		if(ord($up_brandmap) && $up_brandmap=="Y") {
			$etctype.="BRANDMAP=Y";
			$brandmap="Y";

			if(ord($up_brandmapt) && $up_brandmapt=="Y") {
				$etctype.="BRANDMAPT=Y";
				$brandmapt="Y";
			}
		}
	}

	$sql="UPDATE tblshopinfo SET etctype='{$etctype}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('브랜드 관련 페이지 설정이 완료되었습니다.');}</script>";
} else if($type=="save") {
	if($edittype == "insert") {
		if(ord($up_brandname)) {
			$brand_file = $brand_file->upFiles(); // 파일 가져오기
			$sql = "INSERT INTO tblproductbrand( brandname, logo_img, display_yn ) VALUES ('{$up_brandname}', '".$brand_file['logo_img'][0]['v_file']."', '{$up_display_yn}')";
			if(pmysql_query($sql,get_db_conn())) {
				$onload="<script>window.onload=function(){ alert('브랜드 등록이 정상 완료되었습니다.');}</script>";
				DeleteCache("tblproductbrand.cache");
			} else {
				alert_go('동일명이 존재합니다. 다른 브랜드명을 입력해 주세요.',-1);
			}
		} else {
			alert_go('추가할 브랜드명을 입력해 주세요.',-1);
		}
	} else if($edittype == "update") {
		if(ord($up_brandname) && (int)$up_brandlist>0) {

			$up_brand_file = $brand_file->upFiles(); // 파일 가져오기

			$imgDelRes = pmysql_query( "SELECT logo_img FROM tblproductbrand WHERE bridx = '".$up_brandlist."'", get_db_conn() );
			$imgDelRow = pmysql_fetch_object( $imgDelRes );
			if( $imgDelRow->logo_img && $up_brand_file['logo_img'][0]['error'] === false ) {
				$brand_file->removeFile( $imgDelRow->logo_img );
			}
			pmysql_free_result( $imgDelRes );

			if( $up_brand_file['logo_img'][0]['error'] === false ){
				$up_logo_file = $up_brand_file['logo_img'][0]['v_file'];
			} else {
				$up_logo_file = $logo_text;
			}

			$sql = "UPDATE tblproductbrand SET ";
			$sql.= "brandname	= '{$up_brandname}', ";
			$sql.= "logo_img    = '".$up_logo_file."', ";
			$sql.= "display_yn  = '{$up_display_yn}' ";
			$sql.= "WHERE bridx = '{$up_brandlist}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$onload="<script>window.onload=function(){ alert('브랜드 수정이 정상 완료되었습니다.');}</script>";
				DeleteCache("tblproductbrand.cache");
			} else {
				alert_go('동일명이 존재합니다. 다른 브랜드명을 입력해 주세요.',-1);
			}

		} else if((int)$up_brandlist<1) {
			alert_go('수정할 브랜드를 선택해 주세요.',-1);
		} else {
			alert_go('추가할 브랜드명을 입력해 주세요.',-1);
		}
	} else if($edittype == "delete") {
		if((int)$up_brandlist>0) {
			$imgDelRes = pmysql_query( "SELECT logo_img FROM tblproductbrand WHERE bridx = '".$up_brandlist."'", get_db_conn() );
			$imgDelRow = pmysql_fetch_object( $imgDelRes );
			if( $imgDelRow->logo_img ) {
				$brand_file->removeFile( $imgDelRow->logo_img );
			}
			pmysql_free_result( $imgDelRes );
			$sql = "DELETE FROM tblproductbrand ";
			$sql.= "WHERE bridx = '{$up_brandlist}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblproduct ";
				$sql.= "SET brand = null ";
				$sql.= "WHERE brand = '{$up_brandlist}' ";
				pmysql_query($sql,get_db_conn());
				$onload="<script>window.onload=function(){ alert('브랜드 삭제가 정상 완료되었습니다.');}</script>";
				DeleteCache("tblproductbrand.cache");
			}
		} else {
			alert_go('삭제할 브랜드를 선택해 주세요.',-1);
		}
	}
}

if($branduse != "Y") {
	$branddisabled="disabled";
	$brandleftdisabled="disabled";
	$brandmapdisabled="disabled";
} else if($brandleft != "Y") {
	$brandleftdisabled="disabled";
} else if($brandmap != "Y") {
	$brandmapdisabled="disabled";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(typeval) {
	form = document.form1;
	var submit_val = "";
	
	if(typeval == "up") {
		var brandleftyval = document.form1.up_brandlefty.value;
		if(document.form1.up_brandlefty.disabled == false && (!brandleftyval || isNaN(brandleftyval) || parseInt(brandleftyval)<1 || brandleftyval != parseInt(brandleftyval))) {
			alert('브랜드 목록 높이는 0보다 큰 숫자를 입력해 주세요.');
			form.up_brandname.focus();
			submit_val = "no";
		} else if(confirm("브랜드 페이지 설정을 적용하겠습니까?")){
			submit_val = "ok";
		} else {
			submit_val = "no";
		}
	} else if(typeval == "save") {
		if(form.edittype.value == "update" || form.edittype.value == "insert") {
			if(!form.up_brandname.value) {
				alert('브랜드 명을 입력해 주세요.');
				form.up_brandname.focus();
				submit_val = "no";
			}
			if( $("#chk_bridx").val().length <= 0 ){ 
				for(var i=0; i<form.up_brandlist.options.length; i++) {
					if(form.up_brandname.value == form.up_brandlist.options[i].text) {
						alert('현재 동일 브랜드 명이 존재합니다. 다른 브랜드명을 입력 해 주세요.');
						form.up_brandname.focus();
						submit_val = "no";
						break;
					}
				}
			}
		}
		
		if(!submit_val) {
			if(form.edittype.value == "update" && confirm("해당 브랜드가 입력된 상품의 브랜드도 같이 변경됩니다.\n\n선택된 브랜드명을 정말 변경하겠습니까?")) {
				submit_val = "ok";
			} else if(form.edittype.value == "insert" && confirm("신규로 브랜드를 추가하겠습니까?")) {
				submit_val = "ok";
			} else if(form.edittype.value == "delete") {
				if(confirm("해당 브랜드가 입력된 상품의 브랜드도 같이 삭제됩니다.\n\n선택된 브랜드를 정말 삭제하겠습니까?")) {
					submit_val = "ok";
				} else {
					edittype_select("insert");
				}
			}
		}
	}
	
	if(submit_val == "ok") {
		form.type.value=typeval;
		form.submit();
	}
}

function brandleft_change(form) {
	if(form.up_branduse[0].checked == true && form.up_brandleft[0].checked == true) {
		form.up_brandlefty.disabled = false;
		form.up_brandleftl[0].disabled = false;
		form.up_brandleftl[1].disabled = false;
		form.up_brandleftl[2].disabled = false;
	} else {
		form.up_brandlefty.disabled = true;
		form.up_brandleftl[0].disabled = true;
		form.up_brandleftl[1].disabled = true;
		form.up_brandleftl[2].disabled = true;
	}
}

function brandmap_change(form) {
	if(form.up_branduse[0].checked == true && form.up_brandmap[0].checked == true) {
		form.up_brandmapt[0].disabled = false;
		form.up_brandmapt[1].disabled = false;
	} else {
		form.up_brandmapt[0].disabled = true;
		form.up_brandmapt[1].disabled = true;
	}
}

function branduse_change(form) {
	if(form.up_branduse[0].checked == true) {
		form.up_brandleft[0].disabled = false;
		form.up_brandpro[0].disabled = false;
		form.up_brandmap[0].disabled = false;
		form.up_brandleft[1].disabled = false;
		form.up_brandpro[1].disabled = false;
		form.up_brandmap[1].disabled = false;
	} else {
		form.up_brandleft[0].disabled = true;
		form.up_brandpro[0].disabled = true;
		form.up_brandmap[0].disabled = true;
		form.up_brandleft[1].disabled = true;
		form.up_brandpro[1].disabled = true;
		form.up_brandmap[1].disabled = true;
	}
	brandleft_change(form);
	brandmap_change(form);
}

function edittype_select(edittypeval) {
	$("input[input[name='logo_text']").val('');
	$("#chk_bridx").val('');
	form = document.form1;
	if((edittypeval == "update" || edittypeval == "delete") && form.up_brandlist.selectedIndex<0) {
		alert('변경할 브랜드를 선택해 주세요.');
		form.up_brandlist.focus();
	} else {
	
		form.edittype.value="";

		if(edittypeval == "update") {
			/*
			document.getElementById("update").style.backgroundColor = "#FF4C00";
			document.getElementById("insert").style.backgroundColor = "#FFFFFF";
			document.getElementById("delete").style.backgroundColor = "#FFFFFF";
			form.edittype.value = "update";
			form.up_brandname.value = form.up_brandlist.options[form.up_brandlist.selectedIndex].text;
			*/
			// 브랜드 정보를 가져오기위한 ajax
			var bridx = form.up_brandlist.options[form.up_brandlist.selectedIndex].value;
			$.post(
				"product_brand_ajax.php",
				{
					bridx : bridx
				},
				function( data ) {
					if( data ){
						var logoHtml = ""; //로고 이미지 html
						
						$("#chk_bridx").val( data.bridx );
						if( data.logo_img.length > 0 ){
							var logoSrc = '../data/shopimages/brand/' + data.logo_img;
							logoHtml = "<img src='" + logoSrc + "' style='max-width: 120px;' >";
							$("#logo_display").html( logoHtml );
							$("input[name='logo_text']").val( data.logo_img );
						} else {
							$("#logo_display").html("");
							$("input[input[name='logo_text']").val('');
						}

						$("input[name='display_yn']").each(function( index, obj ){
							if( parseInt( $(this).val() ) == parseInt( data.display_yn ) ){
								$(this).prop('checked', true );
							} else {
								$(this).prop('checked', false );
							}
						});

						$("input[name='edittype']").val( "update" );
						$("input[name='up_brandname']").val( data.brandname );
						$("#update").css('background-color','#FF4C00');
						$("#insert").css('background-color','#FFFFFF');
						$("#delete").css('background-color','#FFFFFF');

					} else {
						alert('잘못된 브랜드 선택 입니다.');
					}
				},
				"json"
			);

		} else if(edittypeval == "insert") {
			document.getElementById("update").style.backgroundColor = "#FFFFFF";
			document.getElementById("insert").style.backgroundColor = "#FF4C00";
			document.getElementById("delete").style.backgroundColor = "#FFFFFF";
			form.edittype.value = "insert";
			form.up_brandname.value = "";
		} else if(edittypeval == "delete") {
			document.getElementById("update").style.backgroundColor = "#FFFFFF";
			document.getElementById("insert").style.backgroundColor = "#FFFFFF";
			document.getElementById("delete").style.backgroundColor = "#FF4C00";
			form.edittype.value = "delete";
			CheckForm('save');
		}
	}
}

function brandlist_change() {
	form = document.form1;
	if(form.edittype.value == "update") {
		form.up_brandname.value = form.up_brandlist.options[form.up_brandlist.selectedIndex].text;
	}
}

function defaultreset() {
	branduse_change(document.form1);
	if(document.form1.edittype.value == "update") {
		edittype_select("update");
	} else {
		edittype_select("insert");
	}
}

function SearchSubmit(seachIdxval) {
	form = document.form1;
	form.type.value="";
	form.edittype.value="";
	form.seachIdx.value = seachIdxval;
	form.submit();
}
</script>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 315;}
</STYLE>
<body onLoad="defaultreset();">
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 카테고리/상품관리 &gt;<span>상품 브랜드 관리</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>

	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 브랜드 설정 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>브랜드 추가,수정,삭제가 가능하며 브랜드 관련 페이지의 출력 설정을 할 수 있습니다.</span></div>
				</td>
			</tr>
			
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=edittype value="insert">
			<input type=hidden name=seachIdx value="<?=$seachIdx?>">
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품 브랜드 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
					<tr>
						<td>
                       	<div class="table_style01">
                            <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
							<col width="200"></col>
							<col></col>
							<tr>
								<th><span>상품 브랜드 목록</span></th>
								<td class="td_con1">
								<div class="table_none">
                                <table border=0 cellpadding=0 cellspacing=0 width="100%">
								<tr>
									<td style="padding:5px;padding-left:2px;padding-right:2px;letter-spacing:1.5pt;"><b><a href="javascript:SearchSubmit('A');"><span id="A">A</span></a> 
									<a href="javascript:SearchSubmit('B');"><span id="B">B</span></a> 
									<a href="javascript:SearchSubmit('C');"><span id="C">C</span></a> 
									<a href="javascript:SearchSubmit('D');"><span id="D">D</span></a> 
									<a href="javascript:SearchSubmit('E');"><span id="E">E</span></a> 
									<a href="javascript:SearchSubmit('F');"><span id="F">F</span></a> 
									<a href="javascript:SearchSubmit('G');"><span id="G">G</span></a> 
									<a href="javascript:SearchSubmit('H');"><span id="H">H</span></a> 
									<a href="javascript:SearchSubmit('I');"><span id="I">I</span></a> 
									<a href="javascript:SearchSubmit('J');"><span id="J">J</span></a> 
									<a href="javascript:SearchSubmit('K');"><span id="K">K</span></a> 
									<a href="javascript:SearchSubmit('L');"><span id="L">L</span></a> 
									<a href="javascript:SearchSubmit('M');"><span id="M">M</span></a> 
									<a href="javascript:SearchSubmit('N');"><span id="N">N</span></a> 
									<a href="javascript:SearchSubmit('O');"><span id="O">O</span></a> 
									<a href="javascript:SearchSubmit('P');"><span id="P">P</span></a> 
									<a href="javascript:SearchSubmit('Q');"><span id="Q">Q</span></a> 
									<a href="javascript:SearchSubmit('R');"><span id="R">R</span></a> 
									<a href="javascript:SearchSubmit('S');"><span id="S">S</span></a> 
									<a href="javascript:SearchSubmit('T');"><span id="T">T</span></a> 
									<a href="javascript:SearchSubmit('U');"><span id="U">U</span></a> 
									<a href="javascript:SearchSubmit('V');"><span id="V">V</span></a> 
									<a href="javascript:SearchSubmit('W');"><span id="W">W</span></a> 
									<a href="javascript:SearchSubmit('X');"><span id="X">X</span></a> 
									<a href="javascript:SearchSubmit('Y');"><span id="Y">Y</span></a> 
									<a href="javascript:SearchSubmit('Z');"><span id="Z">Z</span></a></b></td>
									<td width="50" align="center" nowrap><b><a href="javascript:SearchSubmit('전체');"><span id="전체">전체</span></a></b></td>
								</tr>
								<tr>
									<td>
									<select name="up_brandlist" size="20" style="width:100%;" onChange="brandlist_change();">
<?php
$sql = "SELECT * FROM tblproductbrand ";

if(preg_match("/^[A-Z]/", $seachIdx)) {
	$sql.= "WHERE brandname LIKE '{$seachIdx}%' OR brandname LIKE '".strtolower($seachIdx)."%' ";	
	$sql.= "ORDER BY brandname ";
} else if(preg_match("/^[ㄱ-ㅎ]/", $seachIdx)) {
	if($seachIdx == "ㄱ") $sql.= "WHERE (brandname >= 'ㄱ' AND brandname < 'ㄴ') OR (brandname >= '가' AND brandname < '나') ";
	if($seachIdx == "ㄴ") $sql.= "WHERE (brandname >= 'ㄴ' AND brandname < 'ㄷ') OR (brandname >= '나' AND brandname < '다') ";
	if($seachIdx == "ㄷ") $sql.= "WHERE (brandname >= 'ㄷ' AND brandname < 'ㄹ') OR (brandname >= '다' AND brandname < '라') ";
	if($seachIdx == "ㄹ") $sql.= "WHERE (brandname >= 'ㄹ' AND brandname < 'ㅁ') OR (brandname >= '라' AND brandname < '마') ";
	if($seachIdx == "ㅁ") $sql.= "WHERE (brandname >= 'ㅁ' AND brandname < 'ㅂ') OR (brandname >= '마' AND brandname < '바') ";
	if($seachIdx == "ㅂ") $sql.= "WHERE (brandname >= 'ㅂ' AND brandname < 'ㅅ') OR (brandname >= '바' AND brandname < '사') ";
	if($seachIdx == "ㅅ") $sql.= "WHERE (brandname >= 'ㅅ' AND brandname < 'ㅇ') OR (brandname >= '사' AND brandname < '아') ";
	if($seachIdx == "ㅇ") $sql.= "WHERE (brandname >= 'ㅇ' AND brandname < 'ㅈ') OR (brandname >= '아' AND brandname < '자') ";
	if($seachIdx == "ㅈ") $sql.= "WHERE (brandname >= 'ㅈ' AND brandname < 'ㅊ') OR (brandname >= '자' AND brandname < '차') ";
	if($seachIdx == "ㅊ") $sql.= "WHERE (brandname >= 'ㅊ' AND brandname < 'ㅋ') OR (brandname >= '차' AND brandname < '카') ";
	if($seachIdx == "ㅋ") $sql.= "WHERE (brandname >= 'ㅋ' AND brandname < 'ㅌ') OR (brandname >= '카' AND brandname < '타') ";
	if($seachIdx == "ㅌ") $sql.= "WHERE (brandname >= 'ㅌ' AND brandname < 'ㅍ') OR (brandname >= '타' AND brandname < '파') ";
	if($seachIdx == "ㅍ") $sql.= "WHERE (brandname >= 'ㅍ' AND brandname < 'ㅎ') OR (brandname >= '파' AND brandname < '하') ";
	if($seachIdx == "ㅎ") $sql.= "WHERE (brandname >= 'ㅎ' AND brandname < 'ㅏ') OR (brandname >= '하' AND brandname < '') ";
	$sql.= "ORDER BY brandname ";
} else if($seachIdx == "기타") {
	$sql.= "WHERE (brandname < 'ㄱ' OR brandname >= 'ㅏ') AND (brandname < '가' OR brandname >= '') AND (brandname < 'a' OR brandname >= '{') AND (brandname < 'A' OR brandname >= '[') ";
	$sql.= "ORDER BY brandname ";
} else {
	$sql.= "ORDER BY brandname ";
}

$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	echo "<option value=\"{$row->bridx}\">{$row->brandname}</option>";
}
?>
									</select></td>
									<td width="50" align="center" nowrap style="line-height:21px;" valign="top"><b><a href="javascript:SearchSubmit('ㄱ');"><span id="ㄱ">ㄱ</span></a><br>
									<a href="javascript:SearchSubmit('ㄴ');"><span id="ㄴ">ㄴ</span></a><br>
									<a href="javascript:SearchSubmit('ㄷ');"><span id="ㄷ">ㄷ</span></a><br>
									<a href="javascript:SearchSubmit('ㄹ');"><span id="ㄹ">ㄹ</span></a><br>
									<a href="javascript:SearchSubmit('ㅁ');"><span id="ㅁ">ㅁ</span></a><br>
									<a href="javascript:SearchSubmit('ㅂ');"><span id="ㅂ">ㅂ</span></a><br>
									<a href="javascript:SearchSubmit('ㅅ');"><span id="ㅅ">ㅅ</span></a><br>
									<a href="javascript:SearchSubmit('ㅇ');"><span id="ㅇ">ㅇ</span></a><br>
									<a href="javascript:SearchSubmit('ㅈ');"><span id="ㅈ">ㅈ</span></a><br>
									<a href="javascript:SearchSubmit('ㅊ');"><span id="ㅊ">ㅊ</span></a><br>
									<a href="javascript:SearchSubmit('ㅋ');"><span id="ㅋ">ㅋ</span></a><br>
									<a href="javascript:SearchSubmit('ㅌ');"><span id="ㅌ">ㅌ</span></a><br>
									<a href="javascript:SearchSubmit('ㅍ');"><span id="ㅍ">ㅍ</span></a><br>
									<a href="javascript:SearchSubmit('ㅎ');"><span id="ㅎ">ㅎ</span></a><br>
									<a href="javascript:SearchSubmit('기타');"><span id="기타">기타</span></a></b></td>
								</tr>
								</table>
                                </div>
								</td>
							</tr>
							<tr>
								<th><span>편집 모드 선택</span></th>
								<td class="td_con1" align="center">
                                <div class="table_none">
								<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
								<tr>
									<td id="insert" style="background-color:#FF4C00;padding:5px;"><div style="padding:5px;background-color:'#FFFFFF';"><img src="images/btn_add2.gif" border="0" style="cursor:hand;" onClick="edittype_select('insert');"></div></td>
									<td style="padding-left:20px;padding-right:20px;">
									<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
									<tr>
										<td id="update" style="padding:5px;"><div style="padding:5px;background-color:'#FFFFFF';"><img src="images/btn_edit.gif" border="0" style="cursor:hand;" onClick="edittype_select('update');"></div></td>
									</tr>
									</table>
									</td>
									<td id="delete" style="padding:5px;"><div style="padding:5px;background-color:'#FFFFFF';"><img src="images/btn_del.gif" border="0" style="cursor:hand;" onClick="edittype_select('delete');"></div></td>
								</tr>
								</table>
                                </div>
								</td>
							</tr>
							<tr>
								<th><span>상품 브랜드명</span></th>
								<td style="padding:5px;" class="td_con1">
									<input type='hidden' id='chk_bridx' value='' >
									<input type=text name="up_brandname" value="" size="50" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input">
									<a href="javascript:CheckForm('save');">
										<img src="images/btn_save.gif" border="0" hspace="5" align="absmiddle">
									</a>
								</td>
							</tr>
							<tr>
								<th><span>브랜드 로고 이미지</span></th>
								<td style="padding:5px;" class="td_con1">
									<input type='file' name='logo_img[]' />
									<input type='hidden' name='logo_text' />
									<div id='logo_display' >
									</div>
								</td>
							</tr>
							<tr>
								<th><span>브랜드 노출</span></th>
								<td style="padding:5px;" class="td_con1">
									<input type='radio' name='display_yn' value='1' <? if($brUseChk) { echo "checked"; } ?>> 노출
									<input type='radio' name='display_yn' value='0'> 비노출
								</td>
							</tr>
							</table>
                        </div>
						</td>
					</tr>
				</form>
				<tr><td height=20></td></tr>
				<tr>
					<td colspan="2">
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>브랜드 페이지 설정</span></dt>
							<dd>- 브랜드 페이지 사용함으로 설정시에만 각각의 세부 페이지 사용여부를 설정할 수 있습니다.</dd>
	
						</dl>
						<dl>
							<dt><span>상품 브랜드 관리</span></dt>
							<dd>
							- 편집 모드에 따라 브랜드를 등록/수정/삭제가 가능합니다.<br>
							- <font color="#FF4C00">편집 모드에 따라 변경된 내용은 해당 브랜드가 입력된 상품에도 동일하게 적용됩니다.</font><br>
							- 등록된 브랜드는 상품등록/수정시 브랜드를 선택할 수 있습니다.<br>
							- 상품등록/수정시 직접입력한 브랜드는 브랜드 목록에 자동 등록됩니다.
							</dd>

						</dl>

									
					</div>
					</td>
				</tr>
				<tr>
					<td height="50" colspan="2"></td>
				</tr>
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
	</td>
</tr>
</table>
<script language="javascript">
<!--
<?php
	if(ord($seachIdx)) {
		echo "document.getElementById(\"$seachIdx\").style.color=\"#FF4C00\";";
	} else {
		echo "document.getElementById(\"전체\").style.color=\"#FF4C00\";";
	}
?>
//-->
</script>
<?=$onload?>
<?php 
include("copyright.php");
