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

$code=($_POST["code"])?$_POST["code"]:$_GET["code"];
$parentcode=$_POST["parentcode"];
$add_sub_cate=$_POST['add_sub_cate'];
$copy_cate_info=$_POST['copy_cate_info'];

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
		//최상위 카테고리 신규추가
		$sql = "SELECT MAX(code_a) as maxcode FROM tblrecipecode WHERE type IN ('L','T','LX','TX') ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
		$maxcode=(int)$row->maxcode+1;
		$maxcode=sprintf("%03d",$maxcode);
		$type=$up_type1;
		$type.="X";

		$in_code_a=$maxcode;
		$in_code_b="000";
		$in_code_c="000";
		$in_code_d="000";
	


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

	$sql = "INSERT INTO tblrecipecode(
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

		//카테고리 수정
		echo "<script>parent.document.form1.action='recipe_code_indb.php';parent.document.forms[0].submit();</script>";

		//$onload="<script>parent.NewCodeResult('".$in_code_a.$in_code_b.$in_code_c.$in_code_d."','{$type}','{$up_code_name}','{$up_list_type}','{$up_detail_type}','{$up_sort}','{$up_group_code}');parent.HiddenFrame.alert('상품카테고리 등록이 완료되었습니다.');</script>";
	} else {
		$onload="<script>parent.HiddenFrame.alert('상품카테고리 등록중 오류가 발생하였습니다.');</script>";
	}
} else if($mode=="modify" && strlen($code)==12) {

	$code_a=substr($code,0,3);
	$code_b=substr($code,3,3);
	$code_c=substr($code,6,3);
	$code_d=substr($code,9,3);

	$sql = "SELECT * FROM tblrecipecode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
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
		$sql = "UPDATE tblrecipecode SET 
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

		//하위 카테고리 추가
		if($add_sub_cate) {	//하위카테고리 추가

			
			$in_code_a=substr($code,0,3);
			$in_code_b=substr($code,3,3);
			$in_code_c=substr($code,6,3);
			$in_code_d=substr($code,9,3);
			
			$chk_d=$in_code_d;
			$sql = "SELECT * FROM tblrecipecode WHERE code_a='{$in_code_a}' AND code_b='{$in_code_b}' ";
			$sql.= "AND code_c='{$in_code_c}' AND \"code_d\"='{$in_code_d}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($row) {
				if(strstr($row->type,"X")) {
					$retype=str_replace("X","",$row->type);
				}else{
					$retype=$row->type;
				}
			} else {
				alert_go('상위카테고리 선택이 잘못되었습니다.',$_SERVER['PHP_SELF'],'parent.HiddenFrame');			
			}
			
			if(!strstr($retype,"M")) $retype.="M";

			$sql = "SELECT MAX(code_b) as maxcode_b, MAX(code_c) as maxcode_c, MAX(code_d) as maxcode_d ";
			$sql.= "FROM tblrecipecode WHERE code_a='{$in_code_a}' ";
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
			
			$retype.="X";
			
			if($copy_cate_info=='y'){
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
			}

			if($chk_d=='000'){
				$sql = "INSERT INTO tblrecipecode(
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
				'{$retype}', 
				'{$add_sub_cate}', 
				'{$up_list_type}', 
				'{$up_detail_type}', 
				'{$up_sort}', 
				'NO', 
				'{$in_special}', 
				'{$in_special_cnt}', 
				'{$up_islist}')";
				$insert = pmysql_query($sql,get_db_conn());
			}
		}
		
		if($insert){
			if($add_sub_cate){
				$type=str_replace('X','',$type);
				$sql = "UPDATE tblrecipecode SET type='".$type."' where code_a='{$code_a}' and code_b='{$code_b}' and code_c='{$code_c}' and code_d='{$code_d}'";
				pmysql_query($sql,get_db_conn());
			}
		}

		if ($update) {
			if(($is_gcode==1 || $is_sort==1 || $is_design==1 || $is_special==1) && !strstr($type,"X")) {
				$sql = "UPDATE tblrecipecode SET ";
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
			//카테고리 수정
			
			echo "<script>parent.document.form1.action='recipe_code_indb.php'; parent.document.form1.submit();</script>";
			
			//$onload="<script>parent.ModifyCodeResult('".$code_a.$code_b.$code_c.$code_d."','{$type}','{$up_code_name}','{$up_list_type}','{$up_detail_type}','{$up_sort}','{$up_group_code}','{$is_gcode}','{$is_sort}','{$is_design}');parent.HiddenFrame.alert('상품카테고리 정보 수정이 완료되었습니다.');</script>";
		} else {
			$onload="<script>parent.HiddenFrame.alert('상품카테고리 정보 수정중 오류가 발생하였습니다.');</script>";
		}

		$sql = "SELECT * FROM tblrecipecode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
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
		$sql = "SELECT type FROM tblrecipecode ";
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
	$sql = "SELECT code_name,type FROM tblrecipecode WHERE code_a='".substr($parentcode,0,3)."' ";
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
	
	/*
	if (mode=="insert") {
		if(typeof(document.form1.up_type1)=="object") {
			if (document.form1.up_type1[0].checked==false && document.form1.up_type1[1].checked==false) {
				alert("카테고리 타입을 선택하세요.");
				return;
			}
		}
	}
	*/

	up_special="";
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
		//CodeDeleteProperty(document.form1.code.value);
		parent.CodeDelete2(document.form1.code.value);
	}
}
/*
function CodeDeleteProperty(_code) {
	if(_code.length==12 && _code!="000000000000") {
		$("input[name='code']", parent.document).val(_code);
		$("input[name='mode']", parent.document).val('delete');
		$("form[name='form1']", parent.document).attr('action', 'product_code.process.php');
		$("form[name='form1']", parent.document).attr('target', 'HiddenFrame');
		$("form[name='form1']", parent.document).submit();
	}else{
		alert('삭제하실 카테고리를 선택해주세요.');
	}
}
*/
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
		<div class="title_depth3_sub">레시피 카테고리 속성<span>카테고리 추가, 수정, 삭제가 가능하며 카테고리별 템플릿을 선택할 수 있습니다.</span></div>

	</td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top">
    <div class="table_style01">
	<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="return false">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<input type=hidden name=mode value="<?=$mode?>">
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=parentcode value="<?=$parentcode?>">
	<input type=hidden name=mode_result value="result">
	<input type=hidden name=up_list_type value="<?=$list_type?>">
	<input type=hidden name=up_detail_type value="<?=$detail_type?>">
	<input type=hidden name=old_special value="<?=$old_special?>">
	<input type=hidden name=up_special>
	<?php if($mode=="modify"){?>
	<TR>
		<th><span>카테고리 코드</span></th>
		<TD class="td_con1"><B><?=$code?></B></TD>
	</TR>

	<?php }?>
	
	<TR>
		<th><span>카테고리명</span></th>
		<TD class="td_con1"><input type=text name=up_code_name size=38 maxlength=100 value="<?=htmlspecialchars($code_name)?>" class="input_selected" style="width:100%"></TD>
	</TR>
	<TR>
		<th><span>카테고리위치</span></th>
		<TD class="td_con1"><?=$code_loc?></TD>
	</TR>
	<?php if($mode=="modify" && $code_d=='000'){?>
	<TR>
		<th><span>하위 카테고리 생성</span></th>
		<TD class="td_con1"><input type="text" name="add_sub_cate" /></TD>
	</TR>
	<?php }?>
	<tr>
		<th><span>카테고리 숨김여부</span></th>
		<TD class="td_con1"><input type=checkbox id="idx_code_hide1" name=up_code_hide value="NO" <?php if($group_code=="NO") echo "checked";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_code_hide1>이 상품카테고리(카테고리) 숨기기</label></TD>
	</tr>
	<input type="hidden" name=up_type1 value="L">
<!--
	<TR>
		<th><span>카테고리타입</span></th>
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
-->
	<?php if($mode=="modify"){?>
	<TR>
		<th><span>하위 카테고리</span></th>
		<TD class="td_con1"><input type="checkbox" name="copy_cate_info" value="y" checked/> 하위분류에도 위에서 설정한 내용들을 동일하게 적용합니다.</TD>
	</TR>
<? } ?>

    </table>
    </div>
    <div class="table_none">
    <table width="100%">
	<?php if($mode=="insert"){?>
	<TR>
		<TD colspan="2" align="center" style="border:none"><a href="javascript:Save()"><img src="images/botteon_add.gif"  border="0" hspace="0"></a></TD>
	</TR>
	<?php }else if($mode=="modify"){?>
	<TR>
		<TD colspan="2" align="center">

		<?php/* if(!strstr($type,"X")){?>
		<a href="javascript:ChildCodeClick();"><img src="images/botteon_downallapply.gif" border="0" hspace="0"></a>&nbsp;
		<a href="javascript:parent.NewCode();"><img src="images/botteon_newadd.gif" border="0" hspace="0"></a>&nbsp;
		<?php }*/?>
		<a href="javascript:Save();"><img src="images/botteon_catemodify.gif" border="0" hspace="0"></a>&nbsp;
		<a href="javascript:CodeDelete();"><img src="images/botteon_catedelete.gif" border="0" hspace="0"></a>&nbsp;
		</TD>
	</TR>
	<?php }?>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
    </TABLE>
	</form>
    </div>
	</td>
</tr>
<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
</form>
</table>
<?=$onload?>
</body>
</html>