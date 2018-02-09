<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=($_POST["mode"])?$_POST["mode"]:$_GET["mode"];
$mode_result=$_POST["mode_result"];

echo $mode;

$code=($_POST["code"])?$_POST["code"]:$_GET["code"];
$parentcode=$_POST["parentcode"];

$up_code_name=$_POST["up_code_name"];
$up_type1=$_POST["up_type1"];
$up_type2=$_POST["up_type2"];
$up_group_code=$_POST["up_group_code"];
$up_sort=$_POST["up_sort"];
$up_list_type=$_POST["up_list_type"];
$up_detail_type=$_POST["up_detail_type"];
$up_special=$_POST["up_special"];
$up_islist=$_POST["up_islist"];
$up_code_hide=$_POST["up_code_hide"];

$up_special_1_cols=(int)$_POST["up_special_1_cols"];
$up_special_1_rows=(int)$_POST["up_special_1_rows"];
$up_special_2_cols=(int)$_POST["up_special_2_cols"];
$up_special_2_rows=(int)$_POST["up_special_2_rows"];
$up_special_3_cols=(int)$_POST["up_special_3_cols"];
$up_special_3_rows=(int)$_POST["up_special_3_rows"];

$up_special_1_type=$_POST["up_special_1_type"];
$up_special_2_type=$_POST["up_special_2_type"];
$up_special_3_type=$_POST["up_special_3_type"];

$is_gcode=$_POST["is_gcode"];
$is_sort=$_POST["is_sort"];
$is_design=$_POST["is_design"];
$is_special=$_POST["is_special"];

if ($mode=="insert" && ord($up_code_name)) {
	if(strlen($parentcode)==12) {	//하위카테고리 추가
		$in_code_a=substr($parentcode,0,3);
		$in_code_b=substr($parentcode,3,3);
		$in_code_c=substr($parentcode,6,3);
		$in_code_d=substr($parentcode,9,3);

		$sql = "SELECT * FROM tblproductcode WHERE code_a='{$in_code_a}' AND code_b='{$in_code_b}' ";
		$sql.= "AND code_c='{$in_code_c}' AND \"code_d\"='{$in_code_d}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		if($row) {
			if(strstr($row->type,"X")) {
				alert_go('상위카테고리 선택이 잘못되었습니다.');
			}
		} else {
			alert_go('상위카테고리 선택이 잘못되었습니다.',$_SERVER['PHP_SELF'],'parent.HiddenFrame');			
		}
		$type=$row->type;
		if(!strstr($type,"M")) $type.="M";

		$sql = "SELECT MAX(code_b) as maxcode_b, MAX(code_c) as maxcode_c, MAX(code_d) as maxcode_d ";
		$sql.= "FROM tblproductcode WHERE code_a='{$in_code_a}' ";
		if($in_code_b!="000") {
			$sql.= "AND code_b='{$in_code_b}' ";
		}
		if($in_code_c!="000") {
			$sql.= "AND code_c='{$in_code_c}' ";
		}
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);

		if($in_code_b=="000" && $in_code_c=="000" && $in_code_d=="000") {
			$in_code_b=(int)$row->maxcode_b+1;
			$in_code_b=sprintf("%03d",$in_code_b);
		} else if($in_code_c=="000" && $in_code_d=="000") {
			$in_code_c=(int)$row->maxcode_c+1;
			$in_code_c=sprintf("%03d",$in_code_c);
		} else if($in_code_d=="000") {
			$in_code_d=(int)$row->maxcode_d+1;
			$in_code_d=sprintf("%03d",$in_code_d);
		}
		if (ord($up_type2)==0 || $up_type2=="1" || $in_code_d!="000") {
			$type.="X";
		}

	} else {	//최상위 카테고리 신규추가
		$sql = "SELECT MAX(code_a) as maxcode FROM tblproductcode WHERE type IN ('L','T','LX','TX') ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
		$maxcode=(int)$row->maxcode+1;
		$maxcode=sprintf("%03d",$maxcode);
		$type=$up_type1;
		if ($up_type2=="1") {	//중카테고리 없음
			$type.="X";
		}
		$in_code_a=$maxcode;
		$in_code_b="000";
		$in_code_c="000";
		$in_code_d="000";
	}
	if ($up_code_hide=="NO") {
		$up_group_code = "NO";
	}
	if(ord($up_islist)==0) $up_islist="N";
	$in_special="";
	if(ord($old_special) && ord($up_special)) {
		$arr_sp=explode(",",$old_special);
		for($i=0;$i<count($arr_sp);$i++) {
			if(stristr($up_special,$arr_sp[$i])) {
				$in_special.=$arr_sp[$i].",";
			}
		}
		$in_special=rtrim($in_special,',');
	} else $in_special=$up_special;

	$in_special_cnt="";
	if(strstr($in_special,"1")) {
		if($up_special_1_cols<=0) $up_special_1_cols=5;
		if($up_special_1_rows<=0) $up_special_1_rows=1;
		if(ord($up_special_1_type)==0) $up_special_1_type="I";
		$in_special_cnt.="1:{$up_special_1_cols}X{$up_special_1_rows}X{$up_special_1_type},";
	}
	if(strstr($in_special,"2")) {
		if($up_special_2_cols<=0) $up_special_2_cols=5;
		if($up_special_2_rows<=0) $up_special_2_rows=1;
		if(ord($up_special_2_type)==0) $up_special_2_type="I";
		$in_special_cnt.="2:{$up_special_2_cols}X{$up_special_2_rows}X{$up_special_2_type},";
	}
	if(strstr($in_special,"3")) {
		if($up_special_3_cols<=0) $up_special_3_cols=5;
		if($up_special_3_rows<=0) $up_special_3_rows=1;
		if(ord($up_special_3_type)==0) $up_special_3_type="I";
		$in_special_cnt.="3:{$up_special_3_cols}X{$up_special_3_rows}X{$up_special_3_type},";
	}
	if(ord($in_special_cnt)) $in_special_cnt=rtrim($in_special_cnt,',');

	$sql = "INSERT INTO tblproductcode(
	code_a		,
	code_b		,
	code_c		,
	code_d		,
	type		,
	code_name	,
	list_type	,
	detail_type	,
	sort		,
	group_code	,
	special		,
	special_cnt	,
	islist) VALUES (
	'{$in_code_a}', 
	'{$in_code_b}', 
	'{$in_code_c}', 
	'{$in_code_d}', 
	'{$type}', 
	'{$up_code_name}', 
	'{$up_list_type}', 
	'{$up_detail_type}', 
	'{$up_sort}', 
	'{$up_group_code}', 
	'{$in_special}', 
	'{$in_special_cnt}', 
	'{$up_islist}')";
	$insert = pmysql_query($sql,get_db_conn());
	if ($insert) {
		$log_content = "## 카테고리입력 ## - 코드 ".$in_code_a.$in_code_b.$in_code_c.$in_code_d." - 코드명 : {$up_code_name}";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		$onload="<script>parent.NewCodeResult('".$in_code_a.$in_code_b.$in_code_c.$in_code_d."','{$type}','{$up_code_name}','{$up_list_type}','{$up_detail_type}','{$up_sort}','{$up_group_code}');parent.HiddenFrame.alert('상품카테고리 등록이 완료되었습니다.');</script>";
	} else {
		$onload="<script>parent.HiddenFrame.alert('상품카테고리 등록중 오류가 발생하였습니다.');</script>";
	}
} else if($mode=="modify" && strlen($code)==12) {

	$code_a=substr($code,0,3);
	$code_b=substr($code,3,3);
	$code_c=substr($code,6,3);
	$code_d=substr($code,9,3);

	$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
	$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);

	pmysql_free_result($result);
	if(!$row) {
		echo "<script>parent.HiddenFrame.alert('해당 상품카테고리 정보가 존재하지 않습니다.');parent.location.reload();</script>";
		exit;
	}
	$type=$row->type;

	if ($mode_result=="result" && $up_code_name) {	//수정내역 업데이트
		if ($up_code_hide=="NO") {
			$up_group_code = "NO";
		}
		if(ord($up_islist)==0) $up_islist="N";
		$in_special="";
		if(ord($old_special) && ord($up_special)) {
			$arr_sp=explode(",",$old_special);
			for($i=0;$i<count($arr_sp);$i++) {
				if(stristr($up_special,$arr_sp[$i])) {
					$in_special.=$arr_sp[$i].",";
				}
			}
			$in_special=rtrim($in_special,',');
		} else $in_special=$up_special;

		$in_special_cnt="";
		if(strstr($in_special,"1")) {
			if($up_special_1_cols<=0) $up_special_1_cols=5;
			if($up_special_1_rows<=0) $up_special_1_rows=1;
			if(ord($up_special_1_type)==0) $up_special_1_type="I";
			$in_special_cnt.="1:{$up_special_1_cols}X{$up_special_1_rows}X{$up_special_1_type},";
		}
		if(strstr($in_special,"2")) {
			if($up_special_2_cols<=0) $up_special_2_cols=5;
			if($up_special_2_rows<=0) $up_special_2_rows=1;
			if(ord($up_special_2_type)==0) $up_special_2_type="I";
			$in_special_cnt.="2:{$up_special_2_cols}X{$up_special_2_rows}X{$up_special_2_type},";
		}
		if(strstr($in_special,"3")) {
			if($up_special_3_cols<=0) $up_special_3_cols=5;
			if($up_special_3_rows<=0) $up_special_3_rows=1;
			if(ord($up_special_3_type)==0) $up_special_3_type="I";
			$in_special_cnt.="3:{$up_special_3_cols}X{$up_special_3_rows}X{$up_special_3_type},";
		}
		if(ord($in_special_cnt)) $in_special_cnt=rtrim($in_special_cnt,',');

		$up_code_name = str_replace(";","",$up_code_name);
		$sql = "UPDATE tblproductcode SET 
		code_name		= '{$up_code_name}', 
		list_type		= '{$up_list_type}', 
		detail_type		= '{$up_detail_type}', 
		group_code		= '{$up_group_code}', 
		sort			= '{$up_sort}', 
		special			= '{$in_special}', 
		special_cnt		= '{$in_special_cnt}', 
		islist			= '{$up_islist}' 
		WHERE code_a = '{$code_a}' AND code_b = '{$code_b}' 
		AND code_c = '{$code_c}' AND code_d = '{$code_d}' ";
		$update = pmysql_query($sql,get_db_conn());
		if ($update) {
			if(($is_gcode==1 || $is_sort==1 || $is_design==1 || $is_special==1) && !strstr($type,"X")) {
				$sql = "UPDATE tblproductcode SET ";
				if($is_gcode==1) $sql.= "group_code = '{$up_group_code}',";
				if($is_sort==1) $sql.= "sort = '{$up_sort}',";
				if($is_design==1) {
					$sql.= "list_type = '{$up_list_type}',";
					$sql.= "detail_type = '{$up_detail_type}',";
				}
				if($is_special==1) {
					$sql.= "special		= '{$in_special}',";
					$sql.= "special_cnt	= '{$in_special_cnt}',";
					$sql.= "islist		= '{$up_islist}',";
				}
				$sql = rtrim($sql,',');
				$sql.= " WHERE code_a='{$code_a}' ";
				if($code_b!="000") {
					$sql.= "AND code_b='{$code_b}' ";
					if($code_c!="000") {
						$sql.= "AND code_c='{$code_c}' ";
					}
				}
				pmysql_query($sql,get_db_conn());
			}
			$onload="<script>parent.ModifyCodeResult('".$code_a.$code_b.$code_c.$code_d."','{$type}','{$up_code_name}','{$up_list_type}','{$up_detail_type}','{$up_sort}','{$up_group_code}','{$is_gcode}','{$is_sort}','{$is_design}');parent.HiddenFrame.alert('상품카테고리 정보 수정이 완료되었습니다.');</script>";
		} else {
			$onload="<script>parent.HiddenFrame.alert('상품카테고리 정보 수정중 오류가 발생하였습니다.');</script>";
		}

		$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
		$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
	}
	$type=$row->type;
	$code_name=$row->code_name;
	$list_type=$row->list_type;
	$detail_type=$row->detail_type;
	$group_code=$row->group_code;
	$sort=$row->sort;
	$special=$row->special;
	$special_cnt=$row->special_cnt;
	$islist=$row->islist;
	$arr_special=explode(",",$special);
	$old_special=$special;
	$special=array();
	for($i=0;$i<count($arr_special);$i++) {
		$special[$arr_special[$i]]="Y";
	}

	if(ord($old_special)==0) {
		$old_special="1,2,3";
	} else {
		if(!strstr($old_special,"1")) {
			$old_special.=",1";
		}
		if(!strstr($old_special,"2")) {
			$old_special.=",2";
		}
		if(!strstr($old_special,"3")) {
			$old_special.=",3";
		}
	}

	$arrspecialcnt=explode(",",$special_cnt);
	for ($i=0;$i<count($arrspecialcnt);$i++) {
		if (substr($arrspecialcnt[$i],0,2)=="1:") {
			$tmpsp1=substr($arrspecialcnt[$i],2);
		} else if (substr($arrspecialcnt[$i],0,2)=="2:") {
			$tmpsp2=substr($arrspecialcnt[$i],2);
		} else if (substr($arrspecialcnt[$i],0,2)=="3:") {
			$tmpsp3=substr($arrspecialcnt[$i],2);
		}
	}
	if(ord($tmpsp1)) {
		$special_1=explode("X",$tmpsp1);
		$special_1_cols=(int)$special_1[0];
		$special_1_rows=(int)$special_1[1];
		$special_1_type=$special_1[2];
	}
	if(ord($tmpsp2)) {
		$special_2=explode("X",$tmpsp2);
		$special_2_cols=(int)$special_2[0];
		$special_2_rows=(int)$special_2[1];
		$special_2_type=$special_2[2];
	}
	if(ord($tmpsp3)) {
		$special_3=explode("X",$tmpsp3);
		$special_3_cols=(int)$special_3[0];
		$special_3_rows=(int)$special_3[1];
		$special_3_type=$special_3[2];
	}

	if($special_1_cols<=0) $special_1_cols=5;
	if($special_1_rows<=0) $special_1_rows=1;
	if(ord($special_1_type)==0) $special_1_type="I";
	if($special_2_cols<=0) $special_2_cols=5;
	if($special_2_rows<=0) $special_2_rows=1;
	if(ord($special_2_type)==0) $special_2_type="I";
	if($special_3_cols<=0) $special_3_cols=5;
	if($special_3_rows<=0) $special_3_rows=1;
	if(ord($special_3_type)==0) $special_3_type="I";

	$type1=$type[0];
	if (strstr($type,"X")) {
		$type2="1";	//하위카테고리 없음
	} else {
		$type2="0";	//하위카테고리 있음
	}

	$gong="N";
	if ($row->list_type[0]=="B") {
		$gong="Y";
	}	
	$code_loc = getCodeLoc($code);
} else {
	$mode="insert";
	$islist="Y";
	if(ord($old_special)==0) $old_special="1,2,3";
	$special_cnt=4;

	$special_1_type="I";
	$special_1_cols=5;
	$special_1_rows=1;
	$special_2_type="I";
	$special_2_cols=5;
	$special_2_rows=1;
	$special_3_cols=5;
	$special_3_type="I";
	$special_3_rows=1;
}

if(ord($code)==0 && ord($parentcode)==0) {
	$code_loc = "최상위 카테고리";
} else if(strlen($parentcode)==12) {
	if(substr($parentcode,9,3)!="000") {
		alert_go('상위카테고리 선택이 잘못되었습니다.',$_SERVER['PHP_SELF'],'parent.HiddenFrame');
	} else {
		$sql = "SELECT type FROM tblproductcode ";
		$sql.= "WHERE code_a='".substr($parentcode,0,3)."' ";
		$sql.= "AND code_b='".substr($parentcode,3,3)."' ";
		$sql.= "AND code_c='".substr($parentcode,6,3)."' ";
		$sql.= "AND code_d='".substr($parentcode,9,3)."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if(strstr($row->type,"X")) {
				alert_go('상위카테고리 선택이 잘못되었습니다.',$_SERVER['PHP_SELF'],'parent.HiddenFrame');
			}
		} else {
			alert_go('상위카테고리 선택이 잘못되었습니다.',$_SERVER['PHP_SELF'],'parent.HiddenFrame');
		}
		pmysql_free_result($result);
	}
	$code_loc = "";
	$sql = "SELECT code_name,type FROM tblproductcode WHERE code_a='".substr($parentcode,0,3)."' ";
	if(substr($parentcode,3,3)!="000") {
		$sql.= "AND (code_b='".substr($parentcode,3,3)."' OR code_b='000') ";
		if(substr($parentcode,6,3)!="000") {
			$sql.= "AND (code_c='".substr($parentcode,6,3)."' OR code_c='000') ";
		} else {
			$sql.= "AND code_c='000' ";
		}
	} else {
		$sql.= "AND code_b='000' AND code_c='000' ";
	}
	$sql.= "AND code_d='000' ";
	$sql.= "ORDER BY code_a,code_b,code_c,code_d ASC ";
	$result=pmysql_query($sql,get_db_conn());
	$_=array();
	while($row=pmysql_fetch_object($result)) {
		$_[] = $row->code_name;
		$type1=$row->type[0];
	}
	$code_loc = implode(" >> ",$_);
	pmysql_free_result($result);

	if(substr($parentcode,6,3)!="000") {
		$type2=1;
	}
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('PropertyFrame')");</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
function DesignList(idx) {
	document.form1.gong[idx].checked=true;
	if(document.form1.gong[0].checked) gong="N";
	else gong="Y";
	up_list_type=document.form1.up_list_type.value;
	window.open("design_productlist.php?code="+up_list_type+"&gong="+gong,"design","height=450,width=380,scrollbars=yes");
}

function DesignDetail(idx) {
	document.form1.gong[idx].checked=true;
	if(document.form1.gong[0].checked) gong="N";
	else gong="Y";
	up_detail_type=document.form1.up_detail_type.value;
	window.open("design_productdetail.php?code="+up_detail_type+"&gong="+gong,"design2","height=450,width=380,scrollbars=yes");
}

function ChangeSequence() {
	txt=document.form1.fcode.options[document.form1.fcode.selectedIndex].text;
	if((num=txt.indexOf("(가상대카테고리)"))>0) document.form1.selectedfcodename.value=txt.substr(0,num);
	else document.form1.selectedfcodename.value = txt;
}

function GroupCheck(checked){
	if (checked) {
		alert('카테고리를 숨길경우 메인에 표시된 상품은 그대로 표시됩니다.\n확인후 메인상품의 경우는 직접 메인에서 삭제를 해주셔야 합니다.');
		document.form1.up_group_code.disabled=true;
	} else {
		document.form1.up_group_code.disabled=false;
	}
}

function Save() {
	mode = document.form1.mode.value;
	if (document.form1.up_code_name.value.length==0) {
		document.form1.up_code_name.focus();
		alert("카테고리명을 입력하세요.");
		return;
	}
	if (CheckLength(document.form1.up_code_name)>100) {
		alert('총 입력가능한 길이가 한글 50자까지입니다. 다시한번 확인하시기 바랍니다.');
		document.form1.up_code_name.focus();
		return;
	}
	if (mode=="insert") {
		if(typeof(document.form1.up_type1)=="object") {
			if (document.form1.up_type1[0].checked==false && document.form1.up_type1[1].checked==false) {
				alert("카테고리 타입을 선택하세요.");
				return;
			}
		}
		if(typeof(document.form1.up_type2)=="object") {
			if (document.form1.up_type2[0].checked==false && document.form1.up_type2[1].checked==false) {
				alert("하위카테고리 유무를 선택하세요.");
				return;
			}
		}
	}
	if (document.form1.up_list_type.value.length==0) {
		alert("상품진열 디자인을 선택하세요.");
		if(document.form1.gong[0].checked) DesignList(0);
		else DesignList(1);
		return;
	} else {
		list_type=document.form1.up_list_type.value.substring(0,1);
		if(document.form1.gong[0].checked) {
			if(list_type!="A") {
				alert("상품진열 디자인을 선택하세요.");
				DesignList(0);
				return;
			}
		} else {
			if(list_type!="B") {
				alert("상품진열 디자인을 선택하세요.");
				DesignList(1);
				return;
			}
		}
	}
	if (document.form1.up_detail_type.value.length==0) {
		alert("상품상세 디자인을 선택하세요.");
		if(document.form1.gong[0].checked) DesignDetail(0);
		else DesignDetail(1);
		return;
	} else {
		detail_type=document.form1.up_detail_type.value.substring(0,1);
		if(document.form1.gong[0].checked) {
			if(detail_type!="A") {
				alert("상품상세 디자인을 선택하세요.");
				DesignDetail(0);
				return;
			}
		} else {
			if(detail_type!="B") {
				alert("상품상세 디자인을 선택하세요.");
				DesignDetail(1);
				return;
			}
		}
	}

	if (document.form1.up_sort.selectedIndex<=0) {
		alert("상품 정렬 방법을 선택하세요.");
		return;
	}
	up_special="";
	for(i=0;i<document.form1.tmp_special.length;i++) {
		if(document.form1.tmp_special[i].checked) {
			up_special+=","+document.form1.tmp_special[i].value;
		}
	}
	if(up_special.length>0) {
		up_special=up_special.substring(1,up_special.length);
	}
	document.form1.up_special.value=up_special;
	document.form1.submit();
}

function DesignMsg(type){
	if (type==0 && confirm("일반쇼핑몰타입으로 상품이 진열되는 방식입니다!\n상품진열선택과 상품상세선택을 셋팅해 주세요!")) {
		document.form1.gong[0].checked=true;
	} else if(type==0) {
		document.form1.gong[1].checked=true;
	} else if (type==1 && confirm("공동구매타입으로 상품이 진열되는 방식입니다!\n공구상품진열선택과 공구상품상세선택을 셋팅해 주세요!")) {
		document.form1.gong[1].checked=true;
	} else if(type==1) {
		document.form1.gong[0].checked=true;
	}
}

function CodeDelete() {
	submit=true;
	con = "삭제하시겠습니까?\n하위카테고리 및 상품이 모두 지워집니다.";
	con2= "카테고리삭제는 하위카테고리 및 상품이 삭제되오니 신중히 하시기 바랍니다.\n\n최종확인을 합니다."
	if (confirm(con)) {
		if (!confirm(con2)) submit=false;
	} else submit=false;
	if (submit) {
		parent.CodeDelete2(document.form1.code.value);
	}
}

var clickgbn=false;
function ChildCodeClick() {
	WinObj=eval("document.all.child_layer");
	if(clickgbn==false) {
		WinObj.style.visibility = "visible";
		clickgbn=true;
	} else if (clickgbn) {
		WinObj.style.visibility = "hidden";
		clickgbn=false;
	}
}

//-->
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">

<table cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr>
	<td width="100%" bgcolor="#FFFFFF">

		<!-- 소제목 -->
		<div class="title_depth3_sub">카테고리 속성<br /><span>카테고리 추가, 수정, 삭제가 가능하며 카테고리별 템플릿을 선택할 수 있습니다.</span></div>

	</td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<col width=141></col>
	<col width=""></col>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post onsubmit="return false">
	<input type=hidden name=mode value="<?=$mode?>">
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=parentcode value="<?=$parentcode?>">
	<input type=hidden name=mode_result value="result">
	<input type=hidden name=up_list_type value="<?=$list_type?>">
	<input type=hidden name=up_detail_type value="<?=$detail_type?>">
	<input type=hidden name=old_special value="<?=$old_special?>">
	<input type=hidden name=up_special>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<?php if($mode=="modify"){?>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>카테고리 코드</b></TD>
		<TD class="td_con1"><B><?=$code?></B></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<?php }?>

	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>카테고리명</b></TD>
		<TD class="td_con1"><input type=text name=up_code_name size=38 maxlength=100 value="<?=htmlspecialchars($code_name)?>" class="input_selected" style=width:100%></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">카테고리위치</TD>
		<TD class="td_con1"><?=$code_loc?></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">하위 카테고리명</TD>
		<TD class="td_con1"><input type="text" name="" id="" /></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">카테고리타입</TD>
		<TD class="td_con1">
<?php
	if ($mode=="modify" || (ord($parentcode))==12 && strlen($type1)) {
		if ($type1=="L") echo "기본 카테고리";
		else if ($type1=="T") echo "가상 카테고리";
	} else {
		echo "<input type=radio id=\"idx_type1_1\" name=up_type1 value=\"L\" checked style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_type1_1>기본 카테고리</label> <input type=radio id=\"idx_type1_2\" name=up_type1 value=\"T\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_type1_2>가상 카테고리</label>";
	}
?>
		</TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell">
			<img src="images/icon_point2.gif" width="8" height="11" border="0">하위카테고리유무
		</TD>
		<TD class="td_con1">
<?php
		if ($mode=="modify" || (strlen($parentcode)==12 && $type2==1)) {
			if ($type2=="0") echo "하위카테고리 있음";
			else echo "하위카테고리 없음";
		} else {
			echo "<input type=radio id=\"idx_type2_1\" name=up_type2 value=\"0\" checked style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" onclick=\"javascript:alert('카테고리 등록시 한번 설정한 하위카테고리유무는 변경이 불가능 하므로 신중히 선택해 주세요.');\" for=idx_type2_1>하위카테고리 있음</label> <input id=\"idx_type2_2\" type=radio name=up_type2 value=\"1\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" onclick=\"javascript:alert('카테고리 등록시 한번 설정한 하위카테고리유무는 변경이 불가능 하므로 신중히 선택해 주세요.');\" for=idx_type2_2>하위카테고리 없음</labal>";
		}
?>
		</TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">하위 카테고리</TD>
		<TD class="td_con1"><input type="checkbox" name="" id="" /> 하위분류에도 위에서 설정한 내용들을 동일하게 적용합니다.</TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">접근가능 회원등급</TD>
		<TD class="td_con1">
		<select name=up_group_code style="width:100%" <?php if($group_code=="NO") echo "disabled";?> class="select">
<?php  
		$gcode_array = array("","ALL");
		$gname_array = array("모든사람 접근가능","쇼핑몰 회원만 접근가능");
		$sql = "SELECT group_code,group_name FROM tblmembergroup ";
		$result = pmysql_query($sql,get_db_conn());
		$num=2;
		while($row = pmysql_fetch_object($result)){
			$gcode_array[$num]=$row->group_code;
			$gname_array[$num++]=$row->group_name;
		}
		pmysql_free_result($result);
		for($i=0;$i<$num;$i++){
			echo "<option value=\"{$gcode_array[$i]}\"";
			if($group_code==$gcode_array[$i]) echo " selected";
			echo ">{$gname_array[$i]}</option>\n";
		}
?> 
		</select>
		</TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품정렬</TD>
		<TD class="td_con1"><select name=up_sort style="width:100%" class="select">
			<option value="date">선택하세요.</option>
			<option <?php if ($sort=="date") echo "selected "; ?> value="date">상품 등록/수정날짜 순서</option>
			<option <?php if ($sort=="date2") echo "selected "; ?> value="date2">상품 등록/수정날짜 순서 + 품절상품 뒤로</option>
			<option <?php if ($sort=="productname") echo "selected "; ?> value="productname">상품명 가나다 순서</option>
			<option <?php if ($sort=="production") echo "selected "; ?> value="production">제조사 가나다 순서</option>
			<option <?php if ($sort=="price") echo "selected "; ?> value="price">상품 판매가격 순서</option>
			</select>
		</TD>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">카테고리 상품진열</TD>
		<TD class="td_con1"><input type=checkbox id="idx_special1" name=tmp_special value="1" <?php if($special["1"]=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special1>신규상품</label>
		- <FONT COLOR="red">진열타입 선택 :</FONT>
		<select name=up_special_1_type class="select">
			<option value="I" <?php if($special_1_type=="I")echo"selected";?>>이미지 A형</option>
			<option value="D" <?php if($special_1_type=="D")echo"selected";?>>이미지 B형</option>
			<option value="L" <?php if($special_1_type=="L")echo"selected";?>>리스트형</option>
		</select>
		<br><img width=0 height=2><br>
		<img width=87 height=0><FONT COLOR="red">라인별 상품수 :</FONT>
		<select name=up_special_1_cols class="select">
			<option value="1" <?php if($special_1_cols==1)echo"selected";?>>1</option>
			<option value="2" <?php if($special_1_cols==2)echo"selected";?>>2</option>
			<option value="3" <?php if($special_1_cols==3)echo"selected";?>>3</option>
			<option value="4" <?php if($special_1_cols==4)echo"selected";?>>4</option>
			<option value="5" <?php if($special_1_cols==5)echo"selected";?>>5</option>
			<option value="6" <?php if($special_1_cols==6)echo"selected";?>>6</option>
			<option value="7" <?php if($special_1_cols==7)echo"selected";?>>7</option>
			<option value="8" <?php if($special_1_cols==8)echo"selected";?>>8</option>
		</select>&nbsp;
		<FONT COLOR="red">줄수 :</FONT>
		<select name=up_special_1_rows class="select">
			<option value="1" <?php if($special_1_rows==1)echo"selected";?>>1</option>
			<option value="2" <?php if($special_1_rows==2)echo"selected";?>>2</option>
			<option value="3" <?php if($special_1_rows==3)echo"selected";?>>3</option>
			<option value="4" <?php if($special_1_rows==4)echo"selected";?>>4</option>
			<option value="5" <?php if($special_1_rows==5)echo"selected";?>>5</option>
		</select>
		<br><img width=0 height=7><br>
		<input type=checkbox id="idx_special0" name=tmp_special value="2" <?php if($special["2"]=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special0>인기상품</label>
		- <FONT COLOR="red">진열타입 선택 :</FONT>
		<select name=up_special_2_type class="select">
			<option value="I" <?php if($special_2_type=="I")echo"selected";?>>이미지 A형</option>
			<option value="D" <?php if($special_2_type=="D")echo"selected";?>>이미지 B형</option>
			<option value="L" <?php if($special_2_type=="L")echo"selected";?>>리스트형</option>
		</select>
		<br><img width=0 height=2><br>
		<img width=87 height=0><FONT COLOR="red">라인별 상품수 :</FONT>
		<select name=up_special_2_cols class="select">
			<option value="1" <?php if($special_2_cols==1)echo"selected";?>>1</option>
			<option value="2" <?php if($special_2_cols==2)echo"selected";?>>2</option>
			<option value="3" <?php if($special_2_cols==3)echo"selected";?>>3</option>
			<option value="4" <?php if($special_2_cols==4)echo"selected";?>>4</option>
			<option value="5" <?php if($special_2_cols==5)echo"selected";?>>5</option>
			<option value="6" <?php if($special_2_cols==6)echo"selected";?>>6</option>
			<option value="7" <?php if($special_2_cols==7)echo"selected";?>>7</option>
			<option value="8" <?php if($special_2_cols==8)echo"selected";?>>8</option>
		</select>&nbsp;
		<FONT COLOR="red">줄수 :</FONT>
		<select name=up_special_2_rows class="select">
			<option value="1" <?php if($special_2_rows==1)echo"selected";?>>1</option>
			<option value="2" <?php if($special_2_rows==2)echo"selected";?>>2</option>
			<option value="3" <?php if($special_2_rows==3)echo"selected";?>>3</option>
			<option value="4" <?php if($special_2_rows==4)echo"selected";?>>4</option>
			<option value="5" <?php if($special_2_rows==5)echo"selected";?>>5</option>
		</select>
		<br><img width=0 height=2><br>
		<input type=checkbox id="idx_special2" name=tmp_special value="3" <?php if($special["3"]=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special2>추천상품</label>
		- <FONT COLOR="red">진열타입 선택 :</FONT>
		<select name=up_special_3_type class="select">
			<option value="I" <?php if($special_3_type=="I")echo"selected";?>>이미지 A형</option>
			<option value="D" <?php if($special_3_type=="D")echo"selected";?>>이미지 B형</option>
			<option value="L" <?php if($special_3_type=="L")echo"selected";?>>리스트형</option>
		</select>
		<br><img width=0 height=2><br>
		<img width=87 height=0><FONT COLOR="red">라인별 상품수 :</FONT>
		<select name=up_special_3_cols class="select">
			<option value="1" <?php if($special_3_cols==1)echo"selected";?>>1</option>
			<option value="2" <?php if($special_3_cols==2)echo"selected";?>>2</option>
			<option value="3" <?php if($special_3_cols==3)echo"selected";?>>3</option>
			<option value="4" <?php if($special_3_cols==4)echo"selected";?>>4</option>
			<option value="5" <?php if($special_3_cols==5)echo"selected";?>>5</option>
			<option value="6" <?php if($special_3_cols==6)echo"selected";?>>6</option>
			<option value="7" <?php if($special_3_cols==7)echo"selected";?>>7</option>
			<option value="8" <?php if($special_3_cols==8)echo"selected";?>>8</option>
		</select>&nbsp;
		<FONT COLOR="red">줄수 :</FONT>
		<select name=up_special_3_rows class="select">
			<option value="1" <?php if($special_3_rows==1)echo"selected";?>>1</option>
			<option value="2" <?php if($special_3_rows==2)echo"selected";?>>2</option>
			<option value="3" <?php if($special_3_rows==3)echo"selected";?>>3</option>
			<option value="4" <?php if($special_3_rows==4)echo"selected";?>>4</option>
			<option value="5" <?php if($special_3_rows==5)echo"selected";?>>5</option>
		</select>
		<br>
		<input type=checkbox id="idx_islist" name=up_islist value="Y" <?php if($islist=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_islist>카테고리상품목록</label><br>
		<span class="font_orange" style="letter-spacing:-0.5pt;FONT-SIZE:11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 진열수 : <a href="shop_mainproduct.php" target="_parent"><span class="font_blue" style="letter-spacing:-0.5pt;FONT-SIZE:11px;">상점관리 > 쇼핑몰 환경 설정 > 상품 진열수/화면설정</span></a>.</span>
		</TD>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">카테고리 숨김여부</TD>
		<TD class="td_con1"><input type=checkbox id="idx_code_hide1" name=up_code_hide value="NO" <?php if($group_code=="NO") echo "checked";?> onclick="GroupCheck(this.checked)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_code_hide1>이 상품카테고리(카테고리) 숨기기</label></TD>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품진열 템플릿 선택</TD>
		<TD class="td_con1">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="397"><input type=radio id="idx_gong1" name=gong value="N" <?php if($gong=="N" || ord($gong)==0) echo " checked"?> onclick="DesignMsg(0)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gong1>상품 진열 및 상품상세 디자인(일반형)</label></td>
		</tr>
		<tr>
			<td width="100%" style="padding-left:13pt;">
			<table cellpadding="0" cellspacing="0" width="97%">
			<col width=50%></col>
			<col width=50%></col>
			<tr>
				<td><a href="javascript:DesignList(0);"><img src="images/product_displaylist1.gif" width="158" height="16" border="0"></a></td>
				<td><a href="javascript:DesignDetail(0);"><img src="images/product_displaydetail1.gif" width="158" height="16" border="0"></a></td>
			</tr>
			<?php if($gong == "N" && $list_type!="" && $detail_type!="") {?>
			<tr>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignList(0);"><img src="images/product/<?=$list_type?>.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignDetail(0);"><img src="images/product/<?=$detail_type?>.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignList(0);"><img src="images/ex1.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignDetail(0);"><img src="images/ex2.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
			</tr>
			<?php } ?>
			</table>
			</td>
		</tr>
		<tr>
			<td height=10></td>
		</tr>
		<tr>
			<td width="100%"><input type=radio id="idx_gong2" name=gong value="Y" <?php if($gong=="Y") echo " checked"?> onclick="DesignMsg(1)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gong2>가격고정형 공동구매 디자인(공구형)</label></td>
		</tr>
		<tr>
			<td width="100%" style="padding-left:13pt;">
			<table cellpadding="0" cellspacing="0" width="97%">
			<col width=50%></col>
			<col width=50%></col>
			<tr>
				<td><a href="javascript:DesignList(1);"><img src="images/product_displaylist2.gif" width="158" height="16" border="0"></a></td>
				<td><a href="javascript:DesignDetail(1);"><img src="images/product_displaydetail2.gif" width="158" height="16" border="0"></a></td>
			</tr>
			<?php if($gong == "Y" && $list_type!="" && $detail_type!="") {?>
			<tr>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignList(1);"><img src="images/product/<?=$list_type?>.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignDetail(1);"><img src="images/product/<?=$detail_type?>.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignList(1);"><img src="images/ex3.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignDetail(1);"><img src="images/ex4.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
			</tr>
			<?php } ?>
			</table>
			</td>
		</tr>
		</table>
		</TD>
	</tr>
	<?php if($mode=="modify"){?>
	<tr>
		<TD align="center" colspan="2">
		<div id=child_layer style="position:absolute;z-index:100;left:0;bottom:45;width:270px;visibility:hidden;">
		<table border=0 cellspacing=1 cellpadding=0 width=270 bgcolor=#000000>
		<tr>
			<td bgcolor=#FFFFFF>
			<table border=0 cellpadding=3 width=100%>
			<col width=50%></col>
			<col width=50%></col>
			<tr>
				<td valign="top"><input type=checkbox id="idx_isgcode" name="is_gcode" value="1" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_isgcode>접근가능 회원등급</label><br><input type=checkbox id="idx_issort" name="is_sort" value="1" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_issort>상품정렬</label></td>
				<td valign="top"><input type=checkbox id="idx_isdesign" name="is_design" value="1" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_isdesign>상품진열 디자인</label><br><input type=checkbox id="idx_isspecial" name="is_special" value="1" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_isspecial>카테고리 진열상품</label></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</div>
		</TD>
	</tr>
	<?php }?>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>

	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">텍스트 꾸밈</TD>
		<TD class="td_con1">
			<ul>
				<li>
					보통일 때
					<select name="" id="">
						<option value="">==폰트==</option>
					</select>
				</li>
				<li>
					마우스오버일 때
					<select name="" id="">
						<option value="">==폰트==</option>
					</select>
				</li>
			</ul>
			
			
		</TD>
	</TR>

	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>

		<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">이미지 꾸밈</TD>
		<TD class="td_con1">
			<ul>
				<li>
					보통일 때
					<input type="button" value="찾아보기" />
				</li>
				<li>
					마우스오버일 때
					<input type="button" value="찾아보기" />
				</li>
			</ul>
			
			
		</TD>
	</TR>

	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	<?php if($mode=="insert"){?>
	<TR>
		<TD colspan="2" align="center"><a href="javascript:Save()"><img src="images/botteon_add.gif"  border="0" hspace="0"></a></TD>
	</TR>
	<?php }else if($mode=="modify"){?>
	<TR>
		<TD colspan="2" align="center">

		<?php/* if(!strstr($type,"X")){?>
		<a href="javascript:ChildCodeClick();"><img src="images/botteon_downallapply.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
		<a href="javascript:parent.NewCode();"><img src="images/botteon_newadd.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
		<?php }*/?>
		<a href="javascript:Save();"><img src="images/botteon_catemodify.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
		<a href="javascript:CodeDelete();"><img src="images/botteon_catedelete.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
		</TD>
	</TR>
	<?php }?>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	</TABLE>
	</td>
</tr>
</form>
<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
</form>
</table>
<?=$onload?>
</body>
</html>
