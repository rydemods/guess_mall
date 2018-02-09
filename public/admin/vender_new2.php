<?php
/********************************************************************* 
// 파 일 명		: vender_new2.php 
// 설     명		: 입점업체 신규등록
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 입점업체 신규로 등록
// 작 성 자		: 2015.11.16 - 김재수 (vender_new.php 복사)
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	# 파일 클래스 추가
	include_once($Dir."lib/file.class.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "br-1";
	$MenuCode = "brand";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$type=$_POST["type"];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/brand/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
	if($type=="insert") {						// DB에 등록한다.

		$up_disabled=$_POST["up_disabled"];
		$up_id=$_POST["up_id"];
		$up_passwd=$_POST["up_passwd"];
		$up_com_name = trim($_POST["up_com_name"]);
		$up_com_num=$_POST["up_com_num"];
		$up_brand_name=$_POST["up_brand_name"];
		$up_com_owner=$_POST["up_com_owner"];
		$up_com_zonecode=$_POST["up_com_zonecode"];
		$up_com_post1=$_POST["up_com_post1"];
		$up_com_post2=$_POST["up_com_post2"];
		$up_com_addr=$_POST["up_com_addr"];
		$up_com_biz=$_POST["up_com_biz"];
		$up_com_item=$_POST["up_com_item"];
		$up_com_tel1=$_POST["up_com_tel1"];
		$up_com_tel2=$_POST["up_com_tel2"];
		$up_com_tel3=$_POST["up_com_tel3"];
		$up_com_fax1=$_POST["up_com_fax1"];
		$up_com_fax2=$_POST["up_com_fax2"];
		$up_com_fax3=$_POST["up_com_fax3"];
		$up_com_homepage=strtolower($_POST["up_com_homepage"]);

		$up_p_name=$_POST["up_p_name"];
		$up_p_mobile1=$_POST["up_p_mobile1"];
		$up_p_mobile2=$_POST["up_p_mobile2"];
		$up_p_mobile3=$_POST["up_p_mobile3"];
		$up_p_email=$_POST["up_p_email"];
		$up_cs_tel1=$_POST["up_cs_tel1"];
		$up_cs_tel2=$_POST["up_cs_tel2"];
		$up_cs_tel3=$_POST["up_cs_tel3"];
		$up_p_buseo=$_POST["up_p_buseo"];
		$up_p_level=$_POST["up_p_level"];

		$chk_prdt1=$_POST["chk_prdt1"];
		$chk_prdt2=$_POST["chk_prdt2"];
		$chk_prdt3=$_POST["chk_prdt3"];
		$chk_prdt4=$_POST["chk_prdt4"];
		$up_product_max=$_POST["up_product_max"];
		$up_rate=$_POST["up_rate"];
		$up_bank1=$_POST["up_bank1"];
		$up_bank2=$_POST["up_bank2"];
		$up_bank3=$_POST["up_bank3"];
		$up_account_date=$_POST["up_account_date"];
		$v_up_imagefile=$_POST["v_up_imagefile"];
		$up_venbrand_name = $_POST["up_venbrand_name"] ? trim($_POST["up_venbrand_name"]) : trim($_POST["up_com_name"]);
		$up_venbrand_name2 = trim($_POST["up_venbrand_name2"]);
		$up_venbrand_tag=$_POST["up_venbrand_tag"];
		$productcode_a=$_POST["productcode_a"];

		if ($up_rate == '') $up_rate = 0;
		$up_brand_name = $up_id;

		$up_grade		= $_POST["up_grade"];
		$up_staff_rate	= $_POST["up_staff_rate"];
		if ($up_staff_rate == '') $up_staff_rate = 0;

		$up_com_post="";
		if(strlen($up_com_post1)==3 && strlen($up_com_post2)==3) {
			$up_com_post=$up_com_post1.$up_com_post2;
		}

		$brand_cate_no=$_POST["brand_cate_no"];

		$up_com_tel="";
		$up_com_fax="";
		$up_p_mobile="";
		$up_cs_tel="";
		if(ord($up_com_tel1) && ord($up_com_tel2) && ord($up_com_tel3)) {
			if(IsNumeric($up_com_tel1) && IsNumeric($up_com_tel2) && IsNumeric($up_com_tel3)) {
				$up_com_tel=$up_com_tel1."-{$up_com_tel2}-".$up_com_tel3;
			}
		}
		if(ord($up_com_fax1) && ord($up_com_fax2) && ord($up_com_fax3)) {
			if(IsNumeric($up_com_fax1) && IsNumeric($up_com_fax2) && IsNumeric($up_com_fax3)) {
				$up_com_fax=$up_com_fax1."-{$up_com_fax2}-".$up_com_fax3;
			}
		}
		if(ord($up_p_mobile1) && ord($up_p_mobile2) && ord($up_p_mobile3)) {
			if(IsNumeric($up_p_mobile1) && IsNumeric($up_p_mobile2) && IsNumeric($up_p_mobile3)) {
				$up_p_mobile=$up_p_mobile1."-{$up_p_mobile2}-".$up_p_mobile3;
			}
		}
		if(ord($up_cs_tel1) && ord($up_cs_tel2) && ord($up_cs_tel3)) {
			if(IsNumeric($up_cs_tel1) && IsNumeric($up_cs_tel2) && IsNumeric($up_cs_tel3)) {
				$up_cs_tel=$up_cs_tel1."-{$up_cs_tel2}-".$up_cs_tel3;
			}
		}
		if(!ismail($up_p_email)) {
			$up_p_email="";
		}
		$up_com_homepage=str_replace("http://","",$up_com_homepage);

		if($chk_prdt1!="Y") $chk_prdt1="N";
		if($chk_prdt2!="Y") $chk_prdt2="N";
		if($chk_prdt3!="Y") $chk_prdt3="N";
		if($chk_prdt4!="Y") $chk_prdt4="N";
		$grant_product=$chk_prdt1.$chk_prdt2.$chk_prdt3.$chk_prdt4;

		$bank_account="";
		if(ord($up_bank1) && ord($up_bank2) && ord($up_bank3)) {
			$bank_account=$up_bank1."={$up_bank2}=".$up_bank3;
		}

		$error="";
		if(strlen($up_id)<4 || strlen($up_id)>20) {
			$error="업체 아이디는 4자 이상 20자 이하로 입력하셔야 합니다.";
		//} else if(IsAlphaNumeric($up_id)==false) {
		//	$error="업체 아이디는 영문, 숫자를 조합하여 4~12자 이내로 등록이 가능합니다.";
		} else if(ord($up_passwd)==0) {
			$error="비밀번호를 입력하세요.";
		} else if(ord($up_com_name)==0) {
			$error="회사명을 입력하세요.";
		} /*else if(ord($up_com_num)==0) {
			$error="사업자등록번호를 입력하세요.";
		} else if(ord($up_brand_name)==0) {
			$error="미니샵명을 입력하세요.";
		} else if(chkBizNo($up_com_num)==false) {
			$error="사업자등록번호를 정확히 입력하세요.";
		} else if(ord($up_com_tel)==0) {
			$error="회사 대표전화를 정확히 입력하세요.";
		} else if(ord($up_p_name)==0) {
			$error="담당자 이름을 입력하세요.";
		} else if(ord($up_p_mobile)==0) {
			$error="담당자 휴대전화를 정확히 입력하세요.";
		} else if(ord($up_p_email)==0) {
			$error="담당자 이메일을 입력하세요.";
		} else if(ismail($up_p_email)==false) {
			$error="담당자 이메일을 정확히 입력하세요.";
		}*/
		
		if(ord($error)==0) {
			$sql = "SELECT id FROM tblvenderinfo WHERE id='{$up_id}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$error="업체 아이디가 중복되었습니다.";
			}
			pmysql_free_result($result);

			if(ord($error)==0) {
				$sql = "SELECT brand_name FROM tblvenderstore WHERE brand_name='{$up_brand_name}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$error="미니샵명이 중복되었습니다.";
				}
				pmysql_free_result($result);
			}
			
			//같은 브랜드가 있는지 체크한다.
			if(ord($error)==0) {
				$sql = "SELECT brandname FROM tblproductbrand WHERE brandname='{$up_venbrand_name}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$error="브랜드명이 중복되었습니다.";
				}
				pmysql_free_result($result);
			}

			
			$up_passwd = "*".strtoupper(SHA1(unhex(SHA1($up_passwd))));
	
			$up_imagefile=$imagefile->upFiles();

			if(ord($error)==0) {
				$sql = "INSERT INTO tblvenderinfo(
				id		,
				passwd		,
				grant_product	,
				product_max	,
				rate		,
				bank_account	,
				account_date	,
				com_name	,
				com_num		,
				com_owner	,
				com_zonecode	,
				com_post	,
				com_addr	,
				com_biz		,
				com_item	,
				com_tel		,
				com_fax		,
				com_homepage	,
				p_name		,
				p_mobile	,
				p_email		,
				cs_tel		,
				p_buseo		,
				p_level		,
				regdate		,
				disabled) VALUES (
				'{$up_id}', 
				'".$up_passwd."', 
				'{$grant_product}', 
				'{$up_product_max}', 
				'{$up_rate}', 
				'{$bank_account}', 
				'{$up_account_date}', 
				'{$up_com_name}', 
				'{$up_com_num}', 
				'{$up_com_owner}', 
				'{$up_com_zonecode}', 
				'{$up_com_post}', 
				'{$up_com_addr}', 
				'{$up_com_biz}', 
				'{$up_com_item}', 
				'{$up_com_tel}', 
				'{$up_com_fax}', 
				'{$up_com_homepage}', 
				'{$up_p_name}', 
				'{$up_p_mobile}', 
				'{$up_p_email}', 
				'{$up_cs_tel}', 
				'{$up_p_buseo}', 
				'{$up_p_level}', 
				'".date("YmdHis")."', 
				'{$up_disabled}')";

				if(pmysql_query($sql,get_db_conn())) {
					$sql = "SELECT currval('tblvenderinfo_vender_seq') ";
					$res = pmysql_fetch_row(pmysql_query($sql,get_db_conn()));
					$vender = $res[0];

					$sql = "INSERT INTO tblvenderstore(
					vender		,
					id		,
					brand_name	,
					skin) VALUES (
					'{$vender}', 
					'{$up_id}', 
					'{$up_brand_name}', 
					'1,1,1')";
					pmysql_query($sql,get_db_conn());

					$sql = "INSERT INTO tblvenderstorecount(vender) VALUES('{$vender}')";
					pmysql_query($sql,get_db_conn());

					$sql="UPDATE tblshopcount SET vendercnt=vendercnt+1 ";
					pmysql_query($sql,get_db_conn());

					//상품 브랜드 페이지에 등록한다(2016.01.13 - 김재수 추가)
					$sql = "INSERT INTO tblproductbrand( brandname, logo_img, display_yn, productcode_a, vender, brandname2, brandtag, grade,staff_rate ) VALUES ('{$up_venbrand_name}', '".$up_imagefile['up_imagefile'][0]['v_file']."', '1', '{$productcode_a}', '{$vender}', '{$up_venbrand_name2}', '{$up_venbrand_tag}', '{$up_grade}', '{$up_staff_rate}') RETURNING bridx";
					$bridx = pmysql_fetch_object(pmysql_query( $sql, get_db_conn() ));
					//pmysql_query($sql,get_db_conn());					
					DeleteCache("tblproductbrand.cache");
					
					//브랜드별 카테고리코드 추가
					if($bridx->bridx){
						$brand_cate_no_array=explode(",",$brand_cate_no);
						if(count($brand_cate_no_array)){
							foreach($brand_cate_no_array as $bcn=>$bcnv){
								pmysql_query("insert into tblproductbrand_cate (bridx, cate_code) values ('".$bridx->bridx."','".$bcnv."')");
							}
						}
					}
					

					$log_content = "## Vender 신규등록 ## - 업체ID : ".$up_id;
					ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
					alert_go('업체 등록이 완료되었습니다.','vender_management2.php');
				} else {
					$error="Vender 등록중 오류가 발생하였습니다.";
				}
			}
		}
		if(ord($error)) {
			$onload="<script>alert('{$error}')</script>";
		}
	}

	//코드에 해당하는 상품 카테고리를 가져와서 뿌려준다.
	$cateListA_sql = "
	SELECT code_a,code_name,idx
	FROM tblproductcode
	WHERE code_b = '000'
	AND group_code !='NO' AND display_list is NULL
	ORDER BY code_a,code_b,code_c,code_d ASC , cate_sort ASC";
	$cateListA_res = pmysql_query($cateListA_sql,get_db_conn());

?>

<?php include("header.php"); // 상단부분을 불러온다. ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	form=document.form1;
	if(form.up_disabled[0].checked!=true && form.up_disabled[1].checked!=true) {
		alert("업체 승인여부를 선택하세요.");
		form.up_disabled[0].focus();
		return;
	}
	if(form.up_id.value.length==0) {
		alert("업체 아이디를 입력하세요."); form.up_id.focus(); return;
	}
	if(form.up_id.value.length<4) {
		alert("업체 아이디는 4자 이상 20자 이하로 입력하셔야 합니다."); form.up_id.focus(); return;
	}
    /*
	if (IsAlphaNumeric(form.up_id.value)==false) {
   		alert("업체 아이디는 영문, 숫자를 조합하여 4~12자 이내로 등록이 가능합니다."); form.up_id.focus(); return;			
   	}
    */
	if(form.up_passwd.value.length==0) {
		alert("비밀번호를 입력하세요."); form.up_passwd.focus(); return;
	}
	if(form.up_passwd.value!=form.up_passwd2.value) {
		alert("비밀번호가 일치하지 않습니다."); form.up_passwd2.focus(); return;
	}
	if(form.up_com_name.value.length==0) {
		alert("회사명을 입력하세요."); form.up_com_name.focus(); return;
	}
    /*
	if(form.up_com_num.value.length==0) {
		alert("사업자등록번호를 입력하세요."); form.up_com_num.focus(); return;
	}
	if(chkBizNo(form.up_com_num.value)==false) {
		alert("사업자등록번호가 잘못되었습니다."); form.up_com_num.focus(); return;
	}
	if(form.up_com_tel1.value.length==0 || form.up_com_tel2.value.length==0 || form.up_com_tel3.value.length==0) {
		alert("회사 대표전화를 정확히 입력하세요."); form.up_com_tel1.focus(); return;
	}
	if(form.up_p_name.value.length==0) {
		alert("담당자 이름을 입력하세요."); form.up_p_name.focus(); return;
	}
	if(form.up_p_mobile1.value.length==0 || form.up_p_mobile2.value.length==0 || form.up_p_mobile3.value.length==0) {
		alert("담당자 휴대전화를 정확히 입력하세요."); form.up_p_mobile1.focus(); return;
	}
	if(form.up_p_email.value.length==0) {
		alert("담당자 이메일을 입력하세요."); form.up_p_email.focus(); return;
	}
	if(IsMailCheck(form.up_p_email.value)==false) {
		alert("담당자 이메일을 정확히 입력하세요."); form.up_p_email.focus(); return;
	}
    */
	if(form.up_venbrand_name.value.length==0) {
		alert("브랜드명을 입력하세요."); form.up_venbrand_name.focus(); return;
	}
	/*
	if(form.up_venbrand_name2.value.length==0) {
		alert("브랜드명2을 입력하세요."); form.up_venbrand_name2.focus(); return;
	}
	*/
	if(confirm("Vender를 등록하시겠습니까?")) {
		document.form1.type.value="insert";
		document.form1.submit();
	}
}

function iddup() {
	id=document.form1.up_id;
	if(id.value.length==0) {
		alert("업체 아이디를 입력하세요.");
		id.focus();
		return;
	}
	window.open("vender_iddup.php?id="+id.value,"","height=100,width=300,toolbar=no,menubar=no,scrollbars=no,status=no");
}

/*function branddup() {
	brand=document.form1.up_brand_name;
	if(brand.value.length==0) {
		alert("미니샵명을 입력하세요.");
		brand.focus();
		return;
	}
	window.open("vender_branddup.php?brand_name="+brand.value,"","height=100,width=300,toolbar=no,menubar=no,scrollbars=no,status=no");
}*/

function venbranddup() {
	var brand=document.form1.up_venbrand_name;
	if(brand.value.length==0) {
		alert("브랜드명을 입력하세요.");
		brand.focus();
		return;
	}
	window.open("vender_brand_check.php?brand_name="+brand.value,"","height=100,width=300,toolbar=no,menubar=no,scrollbars=no,status=no");
}

</script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script><!-- 다음 우편번호 api -->
<script>
// 다음 우편번호 팝얻창 불러오기
function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			document.getElementById('up_com_zonecode').value = data.zonecode;
			document.getElementById('up_com_post1').value = data.postcode1;
			document.getElementById('up_com_post2').value = data.postcode2;
			document.getElementById('up_com_addr').value = data.address;
			document.getElementById('up_com_addr').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;

			
		}
	}).open();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 브랜드 관리 &gt; 브랜드 관리 &gt; <span>브랜드 신규등록</span></p></div></div>
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
			<?php include("menu_brand.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">브랜드 신규등록</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰에 입점할 업체를 신규로 등록하실 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name="brand_cate_no" id="brand_cate_no">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">업체 회사정보</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<th><span>업체 승인</span></th>
					<td>
					<input type=radio name=up_disabled id=up_disabled0 value="0" <?php if($up_disabled=="0")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled0>승인</label>
					<img width=20 height=0>
					<input type=radio name=up_disabled id=up_disabled1 value="1" <?php if($up_disabled=="1" || ord($up_disabled)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled1>보류</label></td>
				</tr>
				<tr>
					<th><span>업체 ID</span></th>
					<td>
					<input type=text name=up_id value="<?=$up_id?>" size=20 maxlength=20 class=input>
					<A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:iddup();"><IMG src="images/duple_check_img.gif" border=0 align="absmiddle"></A>
					&nbsp;&nbsp; <font class=font_orange>* 영문, 숫자를 혼용하여 사용(4자 ~ 20자)</font></td>
				</tr>
				<tr>
					<th><span>패스워드</span></th>
					<td>
					<input type=password name=up_passwd value="" size=20 maxlength=12 class=input>
					&nbsp;&nbsp;
					<font class=font_orange>* 영문, 숫자를 혼용하여 사용(4자 ~ 12자)</font></td>
				</tr>
				<tr>
					<th><span>패스워드 확인</span></th>
					<td>
					<input type=password name=up_passwd2 value="" size=20 maxlength=12 class=input></td>
				</tr>

				<tr>
					<th><span>상호 (회사명)</span></th>
					<td>
					<input type=text name=up_com_name value="<?=$up_com_name?>" size=20 maxlength=30 class=input>
					</td>
				</tr>
				<tr>
					<th><span>사업자등록번호</span></th>
					<td>
					<input type=text name=up_com_num value="<?=$up_com_num?>" size=20 maxlength=20 onkeyup="strnumkeyup(this)" class=input>
					</td>
				</tr>
				<tr style="display:none;">
					<th><span>미니샵명</span></th>
					<td>
					<input type=text name=up_brand_name value="<?=$up_brand_name?>" size=20 maxlength=30 class=input>
					<A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:branddup();"><IMG src="images/duple_check_img.gif" border=0 align="absmiddle"></A>
					</td>
				</tr>
				<tr>
					<th><span>대표자 성명</span></th>
					<td>
					<input name=up_com_owner value="<?=$up_com_owner?>" size=20 maxlength="12" class=input>
					</td>
				</tr>
				<tr>
					<th><span>회사 주소</span></th>
					<td>
					<input type=text name="up_com_zonecode" id="up_com_zonecode" value="<?=$up_com_zonecode?>" size="5" maxlength="5" readonly class=input>
					<input type=hidden name="up_com_post1" id="up_com_post1" value="<?=$up_com_post1?>"><input type=hidden name="up_com_post2" id="up_com_post2" value="<?=$up_com_post2?>"> <A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:openDaumPostcode();"><IMG src="images/order_no_uimg.gif" border=0 align="absmiddle"></A><br>
					<input type=text name="up_com_addr" id="up_com_addr" value="<?=$up_com_addr?>" size=100 maxlength=150 class=input>
					</td>
				</tr>
				<tr>
					<th><span>사업자 업태</span></th>
					<td>
					<input type="text" name=up_com_biz value="<?=$up_com_biz?>" size=30 maxlength=30 class=input>
					</td>
				</tr>
				<tr>
					<th><span>사업자 종목</span></th>
					<td>
					<input type=text name=up_com_item value="<?=$up_com_item?>" size=30 maxlength=30 class=input>
					</td>
				</tr>
				<tr>
					<th><span>회사 대표전화</span></th>
					<td>
					<input type=text name=up_com_tel1 value="<?=$up_com_tel1?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_tel2 value="<?=$up_com_tel2?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_tel3 value="<?=$up_com_tel3?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>
					</td>
				</tr>
				<tr>
					<th><span>회사 팩스번호</span></th>
					<td>
					<input type=text name=up_com_fax1 value="<?=$up_com_fax1?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_fax2 value="<?=$up_com_fax2?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_fax3 value="<?=$up_com_fax3?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>
					</td>
				</tr>
				<tr>
					<th><span>회사 홈페이지</span></th>
					<td>
					http://<input type=text name=up_com_homepage value="<?=$up_com_homepage?>" size=30 maxlength=50 class=input>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">업체 담당자정보</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>담당자 이름</span></th>
					<td>
					<input type=text name=up_p_name value="<?=$up_p_name?>" size=20 maxlength=20 class=input> &nbsp; <font class=font_orange>* 입점 담당자 이름을 정확히 입력하세요.</font>
					</td>
				</tr>
				<tr>
					<th><span>담당자 휴대전화</span></th>
					<td>
					<input type=text name=up_p_mobile1 value="<?=$up_p_mobile1?>" size=4 maxlength=3 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_p_mobile2 value="<?=$up_p_mobile2?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_p_mobile3 value="<?=$up_p_mobile3?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input></td>
				</tr>
				<tr>
					<th><span>담당자 이메일</span></th>
					<td>
					<input type=text name=up_p_email value="<?=$up_p_email?>" size=30 maxlength=50 class=input> &nbsp; <font class=font_orange>* 주문확인시 담당자 이메일로 통보됩니다.</font>
					</td>
				</tr>
				<tr>
					<th><span>CS 연락처</span></th>
					<td>
					<input type=text name=up_cs_tel1 value="<?=$up_cs_tel1?>" size=4 maxlength=3 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_cs_tel2 value="<?=$up_cs_tel2?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_cs_tel3 value="<?=$up_cs_tel3?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input></td>
				</tr>
				<tr>
					<th><span>담당자 부서명</span></th>
					<td>
					<input type=text name=up_p_buseo value="<?=$up_p_buseo?>" size=20 maxlength=20 class=input>
					</td>
				</tr>
				<tr>
					<th><span>담당자 직위</span></th>
					<td>
					<input type=text name=up_p_level value="<?=$up_p_level?>" size=20 maxlength=20 class=input>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">업체 관리정보</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>상품 처리 권한</span></th>
					<td>
					<input type=checkbox name=chk_prdt1 id=idx_chk_prdt1 value="Y" <?php if($chk_prdt1=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt1>등록</label>
					<img width=20 height=0>
					<input type=checkbox name=chk_prdt2 id=idx_chk_prdt2 value="Y" <?php if($chk_prdt2=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt2>수정</label>
					<img width=20 height=0>
					<input type=checkbox name=chk_prdt3 id=idx_chk_prdt3 value="Y" <?php if($chk_prdt3=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt3>삭제</label>
					<img width=50 height=0>
					<input type=checkbox name=chk_prdt4 id=idx_chk_prdt4 value="Y" <?php if($chk_prdt4=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt4>등록/수정시, 관리자 인증</label>
					</td>
				</tr>
				<tr>
					<th><span>입점 상품수 제한</span></th>
					<td>
						<?php if(ord($up_product_max)==0)$up_product_max="0";?>
						<select name=up_product_max class="select">
						<option value="0" <?php if($up_product_max==0)echo"selected";?>>무제한</option>
						<option value="50" <?php if($up_product_max==50)echo"selected";?>>50</option>
						<option value="100" <?php if($up_product_max==100)echo"selected";?>>100</option>
						<option value="150" <?php if($up_product_max==150)echo"selected";?>>150</option>
						<option value="200" <?php if($up_product_max==200)echo"selected";?>>200</option>
						<option value="250" <?php if($up_product_max==250)echo"selected";?>>250</option>
						<option value="300" <?php if($up_product_max==300)echo"selected";?>>300</option>
						</select> 개 까지 상품등록 가능
					</td>
				</tr>
				<tr>
					<th><span>브랜드명</span></th>
					<td>
					<input type=text name=up_venbrand_name value="<?=$up_venbrand_name?>" size=20 maxlength=30 class=input>
					<A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:venbranddup();"><IMG src="images/duple_check_img.gif" border=0 align="absmiddle"></A>
					</td>
				</tr>
				<tr>
					<th><span>한글 브랜드명</span></th>
					<td>
					<input type=text name=up_venbrand_name2 value="<?=$up_venbrand_name2?>" size=20 maxlength=30 class=input>
					</td>
				</tr>
				<tr>
					<th><span>태그</span></th>
					<td>
					<input type=text name=up_venbrand_tag value="<?=$up_venbrand_tag?>" size=100 maxlength=100 class=input> &nbsp; <font class=font_orange>* 여러태그 입력시 구분자(,)를 입력해 주세요.</font>
					</td>
				</tr>
				<tr>
					<th><span>대표 카테고리</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<select name='productcode_a'>
						<?while($cateListA_row = pmysql_fetch_object($cateListA_res)){?>
						<option value='<?=$cateListA_row->code_a?>'><?=$cateListA_row->code_name?></option>
						<?}?>
						</select>
					</td>
				</tr>				
				<tr>
					<th><span>등급</span></th>
					<td>
					<input type=radio name=up_grade id=up_grade0 value="N" <?php if($up_grade=="N" || ord($up_grade)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_grade0>일반</label>
					<img width=20 height=0>
					<input type=radio name=up_grade id=up_grade1 value="P" <?php if($up_grade=="P")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_grade1>프리미엄</label></td>
				</tr>
				<tr>
					<th><span>로고 투명이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile" value="" >
					</td>
				</tr>
				<tr>
					<th><span>수수료율</span></th>
					<td>
						<input type=text name=up_rate value="<?=$up_rate?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
					</td>
				</tr>
				<tr>
					<th><span>임직원 할인율</span></th>
					<td>
						<input type=text name=up_staff_rate value="<?=$up_staff_rate?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
					</td>
				</tr>
				<tr style="display:;">
					<th><span>정산 계좌정보</span></th>
					<td>
						은행 <input type=text name=up_bank1 value="<?=$up_bank1?>" size=10 class=input>
						<img width=20 height=0>
						계좌번호 <input type=text name=up_bank2 value="<?=$up_bank2?>" size=20 class=input>
						<img width=20 height=0>
						예금주 <input type=text name=up_bank3 value="<?=$up_bank3?>" size=15 class=input>
					</td>
				</tr>
				<tr style="display:none;">
					<th><span>정산일(매월)</span></th>
					<td>
						<input type=text name=up_account_date value="<?=$up_account_date?>" size=10 class=input>일 
						&nbsp;&nbsp;&nbsp;&nbsp; <font class=font_orange>* (복수기입시 10,20,30 과 같이 기입요망)</font>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">브랜드 카테고리 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="table_style01">				
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<col width=140></col>
							<col width=></col>
							<tr>
								<th><span>카테고리 선택</span></th>
								<td>
									<link rel="stylesheet" href="/js/dist/themes/default/style.min.css?v=221" />
									<div id="jstree">
									<!-- in this example the tree is populated from inline HTML -->
									<ul>
										<?foreach(Category_list() as $cl=>$clv){?>
										<li id="<?=$clv->code_a?>"><?=$clv->code_name?>
											<ul>
											<?foreach(Category_list($clv->code_a) as $cl2=>$clv2){?>
											<li id="<?=$clv->code_a.$clv2->code_b?>"><?=$clv2->code_name?>
												<ul>
												<?foreach(Category_list($clv->code_a,$clv2->code_b) as $cl3=>$clv3){?>
													<li id="<?=$clv->code_a.$clv2->code_b.$clv3->code_c?>"><?=$clv3->code_name?>
														<ul>
														<?foreach(Category_list($clv->code_a,$clv2->code_b,$clv3->code_c) as $cl4=>$clv4){?>
															<li id="<?=$clv->code_a.$clv2->code_b.$clv3->code_c.$clv4->code_d?>"><?=$clv4->code_name?></li>
														<?}?>
														</ul>
													</li>
												<?}?>
												</ul>
											</li>
											<?}?>
											</ul>
										</li>
										<?}?>
									</ul>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
<!-- 4 include the jQuery library -->
<script src="/js/dist/jquery.min.js"></script>
<!-- 5 include the minified jstree source -->
<script src="/js/dist/jstree.min.js"></script>
<script>
$(function () {
	// 6 create an instance when the DOM is ready
	
	$('#jstree').jstree({
		"checkbox" : {	"keep_selected_style" : false },
		"plugins" : [ "checkbox" ]
	});
	
	// 7 bind to events triggered on the tree
	$('#jstree').on("changed.jstree", function (e, data) {
		//console.log(data.selected);
		$("#brand_cate_no").val(data.selected);
	});
	$("#jstree").jstree("open_all");
	//$("#jstree").jstree("check_all");
	//$("#jstree").jstree("hide_dots");
	//$("#jstree").jstree("close_all");
	
	

	
});
</script>


			<tr><td height=20></td></tr>	
			<tr>
				<td colspan=8 align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>	
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>브랜드 신규등록</span></dt>
							<dd>- 신규 브랜드 등록페이지 입니다.<br>
							- 브랜드 등록이후 미니샵 관리자페이지 내에서 상품관리를 진행할 수 있습니다.<br>
							- 등록된 입점사 관리는 <a href="javascript:parent.topframe.GoMenu(11,'vender_management2.php');"><span class="font_blue">브랜드 관리 > 브랜드 관리 > 브랜드 정보관리</span></a> 페이지에서 관리합니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
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
include("copyright.php"); // 하단부분을 불러온다. 
