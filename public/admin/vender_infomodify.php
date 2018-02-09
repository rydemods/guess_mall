<?php
/********************************************************************* 
// 파 일 명		: vender_infomodify.php 
// 설     명		: 입점업체 수정
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 입점업체를 수정
// 작 성 자		: hspark
// 수 정 자		: 2015.10.23 - 김재수
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

##################### 페이지 접근권한 check #####################
	$PageCode = "vd-1";
	$MenuCode = "vender";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#########################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$vender=$_POST["vender"];

	$sql = "SELECT a.*, b.brand_name FROM tblvenderinfo a, tblvenderstore b ";
	$sql.= "WHERE a.vender='{$vender}' AND a.delflag='N' AND a.vender=b.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vdata=pmysql_fetch_object($result)) {
		alert_go('해당 업체 정보가 존재하지 않습니다.',-1);
	}
	pmysql_free_result($result);

	$com_tel=explode("-",$_vdata->com_tel);
	$com_fax=explode("-",$_vdata->com_fax);
	$p_mobile=explode("-",$_vdata->p_mobile);
	$bank_account=explode("=",$_vdata->bank_account);

	$type=$_POST["type"];

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	if($type=="update") {				// DB를 수정한다.
		$up_disabled=$_POST["up_disabled"];
		$up_passwd=$_POST["up_passwd"];
		$up_com_name=$_POST["up_com_name"];
		$up_com_num=$_POST["up_com_num"];
		$up_brand_name=$_POST["up_brand_name"];
		$up_com_owner=$_POST["up_com_owner"];
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

		$up_com_post="";
		if(strlen($up_com_post1)==3 && strlen($up_com_post2)==3) {
			$up_com_post=$up_com_post1.$up_com_post2;
		}

		$up_com_tel="";
		$up_com_fax="";
		$up_p_mobile="";
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
		if(ord($up_com_name)==0) {
			$error="회사명을 입력하세요.";
		} else if(ord($up_com_num)==0) {
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
		}

		if(ord($error)==0) {
			$sql = "SELECT brand_name FROM tblvenderstore WHERE vender!='{$vender}' AND brand_name='{$up_brand_name}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$error="미니샵명이 중복되었습니다.";
			}
			pmysql_free_result($result);

			if(ord($error)==0) {
				$sql = "UPDATE tblvenderinfo SET ";
				if(ord($up_passwd)) {
					$up_passwd = "*".strtoupper(SHA1(unhex(SHA1($up_passwd))));
					$sql.= "passwd			= '".$up_passwd."', ";
				}
				$sql.= "grant_product	= '{$grant_product}', ";
				$sql.= "product_max		= '{$up_product_max}', ";
				$sql.= "rate			= '{$up_rate}', ";
				$sql.= "bank_account	= '{$bank_account}', ";
				$sql.= "account_date	= '{$up_account_date}', ";
				$sql.= "com_name		= '{$up_com_name}', ";
				$sql.= "com_num			= '{$up_com_num}', ";
				$sql.= "com_owner		= '{$up_com_owner}', ";
				$sql.= "com_post		= '{$up_com_post}', ";
				$sql.= "com_addr		= '{$up_com_addr}', ";
				$sql.= "com_biz			= '{$up_com_biz}', ";
				$sql.= "com_item		= '{$up_com_item}', ";
				$sql.= "com_tel			= '{$up_com_tel}', ";
				$sql.= "com_fax			= '{$up_com_fax}', ";
				$sql.= "com_homepage	= '{$up_com_homepage}', ";
				$sql.= "p_name			= '{$up_p_name}', ";
				$sql.= "p_mobile		= '{$up_p_mobile}', ";
				$sql.= "p_email			= '{$up_p_email}', ";
				$sql.= "p_buseo			= '{$up_p_buseo}', ";
				$sql.= "p_level			= '{$up_p_level}', ";
				$sql.= "regdate			= '".date("YmdHis")."', ";
				$sql.= "disabled		= '{$up_disabled}' ";
				$sql.= "WHERE vender='{$vender}' ";
				if(pmysql_query($sql,get_db_conn())) {
					if($_vdata->brand_name!=$up_brand_name) {
						$sql = "UPDATE tblvenderstore SET ";
						$sql.= "brand_name	= '{$up_brand_name}' ";
						$sql.= "WHERE vender='{$vender}' ";
						pmysql_query($sql,get_db_conn());
					}

					$log_content = "## 입점업체 정보 수정 ## - 업체ID : ".$_vdata->id;
					ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

					echo "<html></head><body onload=\"alert('업체정보 수정이 완료되었습니다.');parent.document.form3.submit();\"></body></html>";exit;
				} else {
					$error="입점업체 등록중 오류가 발생하였습니다.";
				}
			}
		}
		if(ord($error)) {
			echo "<html></head><body onload=\"alert('{$error}');\"></body></html>";exit;
		}
	} else if($type=="delete" && ($_POST["delete_gbn"]=="Y" || $_POST["delete_gbn"]=="N")) {				// DB를 삭제한다.
		$delete_gbn=$_POST["delete_gbn"];
		$sql = "SELECT COUNT(*) as cnt FROM tblorderproduct WHERE vender='{$vender}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		$cnt=$row->cnt;

		$sql="UPDATE tblshopcount SET vendercnt=vendercnt-1 ";
		pmysql_query($sql,get_db_conn());

		if($cnt<=0) {
			pmysql_query("DELETE FROM tblvenderinfo WHERE vender='{$vender}'",get_db_conn());
		} else {
			$sql = "UPDATE tblvenderinfo SET delflag='Y' ";
			$sql.= "WHERE vender='{$vender}' ";
			pmysql_query($sql,get_db_conn());
		}
		pmysql_query("DELETE FROM  tblvenderinfo_add WHERE vender='{$vender}'",get_db_conn());
		pmysql_query("DELETE FROM  tblproductbrand WHERE vender='{$vender}'",get_db_conn());
		pmysql_query("DELETE FROM tblvenderstore WHERE vender='{$vender}'",get_db_conn());
		pmysql_query("DELETE FROM tblvenderstorecount WHERE vender='{$vender}'",get_db_conn());
		pmysql_query("DELETE FROM tblvenderlog WHERE vender='{$vender}'",get_db_conn());

		/**************************** 필요없는 쿼리 삭제 (김재수 - 2015.10.23) ****************************/
		//pmysql_query("DELETE FROM tblvenderstorevisit WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvendercodedesign WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblregiststore WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvenderthemecode vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvenderthemeproduct WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvenderspecialmain WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvenderspecialcode WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvendernotice WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvenderadminnotice WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvenderadminqna WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblvenderaccount WHERE vender='{$vender}'",get_db_conn());
		//pmysql_query("DELETE FROM tblregiststore WHERE vender='{$vender}'",get_db_conn());		
		/***********************************************************************************************/

		pmysql_query("optimize table tblvenderstorevisit");

		/**************************** 필요없는 쿼리 삭제 (김재수 - 2015.10.23) ****************************/
		//pmysql_query("optimize table tblvenderlog");
		//pmysql_query("optimize table tblregiststore");
		/***********************************************************************************************/

		//이미지 파일 삭제
		$storeimagepath=$Dir.DataDir."shopimages/vender/";
		proc_matchfiledel($storeimagepath."MAIN_{$vender}.*");
		proc_matchfiledel($storeimagepath."logo_{$vender}.*");
		proc_matchfiledel($storeimagepath.$vender."*");
		proc_matchfiledel($storeimagepath."aboutdeliinfo_{$vender}*");

		if($delete_gbn=="Y") {			//업체 상품 완전 삭제
			$sql = "SELECT productcode FROM tblproduct WHERE vender='{$vender}' ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$prcode=$row->productcode;
				#태그관련 지우기
				$sql1 = "DELETE FROM tbltagproduct WHERE productcode = '{$prcode}'";
				pmysql_query($sql,get_db_conn());

				#리뷰 지우기
				$sql1 = "DELETE FROM tblproductreview WHERE productcode = '{$prcode}'";
				pmysql_query($sql,get_db_conn());

				#위시리스트 지우기
				$sql1 = "DELETE FROM tblwishlist WHERE productcode = '{$prcode}'";
				pmysql_query($sql,get_db_conn());

				#관련상품 지우기
				$sql1 = "DELETE FROM tblcollection WHERE productcode = '{$prcode}'";
				pmysql_query($sql,get_db_conn());

				$sql1 = "DELETE FROM tblproducttheme WHERE productcode = '{$prcode}'";
				pmysql_query($sql,get_db_conn());

				$sql1 = "DELETE FROM tblproduct WHERE productcode = '{$prcode}'";
				pmysql_query($sql,get_db_conn());

				$sql1 = "DELETE FROM tblproduct_option WHERE productcode = '{$prcode}'";
				pmysql_query($sql,get_db_conn());

				$sql1 = "DELETE FROM tblproductgroupcode WHERE productcode = '{$prcode}'";
				pmysql_query($sql,get_db_conn());

				$delshopimage = $Dir.DataDir."shopimages/product/{$prcode}*";
				proc_matchfiledel($delshopimage);

				delProductMultiImg("prdelete","",$prcode);
			}
			pmysql_free_result($result);

			$log_content = "## 입점업체 삭제 ## - 업체ID : {$_vdata->id} , [업체상품 삭제]";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		} else if($delete_gbn=="N") {	//업체 상품 쇼핑몰 본사 상품으로 변경
			$sql = "UPDATE tblproduct SET vender=0 ";
			$sql.= "WHERE vender='{$vender}' ";
			pmysql_query($sql,get_db_conn());

			$log_content = "## 입점업체 삭제 ## - 업체ID : {$_vdata->id} , [업체상품 본사상품으로 변경]";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		}

		echo "<html></head><body onload=\"alert('해당 입점업체 정보가 완전히 삭제되었습니다.');parent.document.form3.submit();\"></body></html>";exit;
	}

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$disabled=$_POST["disabled"];
	$s_check=$_POST["s_check"];
	$search=$_POST["search"];
	$block=$_POST["block"];
	$gotopage=$_POST["gotopage"];

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	form=document.form1;
	if(form.up_disabled[0].checked!=true && form.up_disabled[1].checked!=true) {
		alert("업체 승인여부를 선택하세요.");
		form.up_disabled[0].focus();
		return;
	}
	if(form.up_passwd.value.length>0) {
		if(form.up_passwd.value!=form.up_passwd2.value) {
			alert("변경할 비밀번호가 일치하지 않습니다."); form.up_passwd2.focus(); return;
		}
	}
	if(form.up_com_name.value.length==0) {
		alert("회사명을 입력하세요."); form.up_com_name.focus(); return;
	}
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
	if(confirm("입점업체 정보를 수정하시겠습니까?")) {
		document.form1.type.value="update";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function GoReturn() {
	document.form3.submit();
}

function CheckDelete() {
	if(confirm("해당 업체를 정말 삭제하시겠습니까?")) {
		if(confirm("해당 업체의 상품도 같이 삭제하시겠습니까?\n\n업체 상품을 같이 삭제할 경우 [확인]\n\n업체 상품을 쇼핑몰 본사 상품으로 변경하시려면 [취소] 버튼을 누르세요.")) {
			if(confirm("정말 해당 업체와 상품을 모두 삭제하시겠습니까?")) {
				document.form1.delete_gbn.value="Y";
				document.form1.type.value="delete";
				document.form1.target="processFrame";
				document.form1.submit();
			}
		} else {
			if(confirm("정말 해당 업체 삭제 후 업체 상품을 쇼핑몰 본사 상품으로 변경하시겠습니까?")) {
				document.form1.delete_gbn.value="N";
				document.form1.type.value="delete";
				document.form1.target="processFrame";
				document.form1.submit();
			}
		}
	}
}

function branddup(vender) {
	brand=document.form1.up_brand_name;
	if(brand.value.length==0) {
		alert("미니샵명을 입력하세요.");
		brand.focus();
		return;
	}
	window.open("vender_branddup.php?vender="+vender+"&brand_name="+brand.value,"","height=100,width=300,toolbar=no,menubar=no,scrollbars=no,status=no");
}

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");		
}

</script>

<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리 &gt; <span class="2depth_select">입점업체 정보 수정/삭제</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_vender.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=delete_gbn>
			<input type=hidden name=vender value="<?=$vender?>">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_new_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif">&nbsp;</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">입점업체의 정보를 수정/삭제 하실 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_reg_stitle1.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">업체 승인</TD>
					<TD class="td_con1">
					<input type=radio name=up_disabled id=up_disabled0 value="0" <?php if($_vdata->disabled=="0")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled0>승인</label>
					<img width=20 height=0>
					<input type=radio name=up_disabled id=up_disabled1 value="1" <?php if($_vdata->disabled=="1" || ord($_vdata->disabled)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled1>보류</label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">업체 ID</TD>
					<TD class="td_con1"><B><?=$_vdata->id?></B></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">패스워드 변경</TD>
					<TD class="td_con1">
					<input type=password name=up_passwd value="" size=20 maxlength=12 class=input>
					&nbsp;&nbsp;
					<FONT class=font_orange>* 영문, 숫자를 혼용하여 사용(4자 ~ 12자)</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">패스워드 확인</TD>
					<TD class="td_con1">
					<input type=password name=up_passwd2 value="" size=20 maxlength=12 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>			
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_reg_stitle2.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">상호 (회사명)</TD>
					<TD class="td_con1">
					<input type=text name=up_com_name value="<?=$_vdata->com_name?>" size=20 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">사업자등록번호</TD>
					<TD class="td_con1">
					<input type=text name=up_com_num value="<?=$_vdata->com_num?>" size=20 maxlength=20 onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">미니샵명</TD>
					<TD class="td_con1">
					<input type=text name=up_brand_name value="<?=$_vdata->brand_name?>" size=20 maxlength=30 class=input>
					<A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:branddup();"><IMG src="images/duple_check_img.gif" border=0 align="absmiddle"></A>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">대표자 성명</TD>
					<TD class="td_con1">
					<input name=up_com_owner value="<?=$_vdata->com_owner?>" size=20 maxlength="12" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">회사 주소</TD>
					<TD class="td_con1">
					<input type=text name="up_com_post1" value="<?=substr($_vdata->com_post,0,3)?>" size="3" maxlength="3" readonly class=input> - <input type=text name="up_com_post2" value="<?=substr($_vdata->com_post,3,3)?>" size="3" maxlength="3" readonly class=input> <A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:f_addr_search('form1','up_com_post','up_com_addr',2);"><IMG src="images/order_no_uimg.gif" border=0 align="absmiddle"></A><br>
					<input type=text name="up_com_addr" value="<?=$_vdata->com_addr?>" size=50 maxlength=150 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">사업자 업태</TD>
					<TD class="td_con1">
					<input type="text" name=up_com_biz value="<?=$_vdata->com_biz?>" size=30 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">사업자 종목</TD>
					<TD class="td_con1">
					<input type=text name=up_com_item value="<?=$_vdata->com_item?>" size=30 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">회사 대표전화</TD>
					<TD class="td_con1">
					<input type=text name=up_com_tel1 value="<?=$com_tel[0]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_tel2 value="<?=$com_tel[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_tel3 value="<?=$com_tel[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">회사 팩스번호</TD>
					<TD class="td_con1">
					<input type=text name=up_com_fax1 value="<?=$com_fax[0]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_fax2 value="<?=$com_fax[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_fax3 value="<?=$com_fax[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">회사 홈페이지</TD>
					<TD class="td_con1">
					http://<input type=text name=up_com_homepage value="<?=$_vdata->com_homepage?>" size=30 maxlength=50 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>			
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_reg_stitle3.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 이름</TD>
					<TD class="td_con1">
					<input type=text name=up_p_name value="<?=$_vdata->p_name?>" size=20 maxlength=20 class=input> &nbsp; <FONT class=font_orange>* 입점 담당자 이름을 정확히 입력하세요.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 휴대전화</TD>
					<TD class="td_con1">
					<input type=text name=up_p_mobile1 value="<?=$p_mobile[0]?>" size=4 maxlength=3 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_p_mobile2 value="<?=$p_mobile[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_p_mobile3 value="<?=$p_mobile[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 이메일</TD>
					<TD class="td_con1">
					<input type=text name=up_p_email value="<?=$_vdata->p_email?>" size=30 maxlength=50 class=input> &nbsp; <FONT class=font_orange>* 주문확인시 담당자 이메일로 통보됩니다.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">담당자 부서명</TD>
					<TD class="td_con1">
					<input type=text name=up_p_buseo value="<?=$_vdata->p_buseo?>" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">담당자 직위</TD>
					<TD class="td_con1">
					<input type=text name=up_p_level value="<?=$_vdata->p_level?>" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>			
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_reg_stitle4.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 처리 권한</TD>
					<TD class="td_con1">
					<input type=checkbox name=chk_prdt1 id=idx_chk_prdt1 value="Y" <?php if($_vdata->grant_product[0]=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt1>등록</label>
					<img width=20 height=0>
					<input type=checkbox name=chk_prdt2 id=idx_chk_prdt2 value="Y" <?php if($_vdata->grant_product[1]=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt2>수정</label>
					<img width=20 height=0>
					<input type=checkbox name=chk_prdt3 id=idx_chk_prdt3 value="Y" <?php if($_vdata->grant_product[2]=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt3>삭제</label>
					<img width=50 height=0>
					<input type=checkbox name=chk_prdt4 id=idx_chk_prdt4 value="Y" <?php if($_vdata->grant_product[3]=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt4>등록/수정시, 관리자 인증</label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">입점 상품수 제한(<?=$_vdata->product_max?>)</TD>
					<TD class="td_con1">
						<select name=up_product_max class="select">
						<option value="0" <?php if($_vdata->product_max==0)echo"selected";?>>무제한</option>
						<option value="50" <?php if($_vdata->product_max==50)echo"selected";?>>50</option>
						<option value="100" <?php if($_vdata->product_max==100)echo"selected";?>>100</option>
						<option value="150" <?php if($_vdata->product_max==150)echo"selected";?>>150</option>
						<option value="200" <?php if($_vdata->product_max==200)echo"selected";?>>200</option>
						<option value="250" <?php if($_vdata->product_max==250)echo"selected";?>>250</option>
						<option value="300" <?php if($_vdata->product_max==300)echo"selected";?>>300</option>
						</select> 개 까지 상품등록 가능
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">판매 수수료</TD>
					<TD class="td_con1">
						<input type=text name=up_rate value="<?=$_vdata->rate?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
						&nbsp;&nbsp;&nbsp;&nbsp; <FONT class=font_orange>* 쇼핑몰 본사에서 받는 상품판매 수수료를 입력하세요.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">정산 계좌정보</TD>
					<TD class="td_con1">
						은행 <input type=text name=up_bank1 value="<?=$bank_account[0]?>" size=10 class=input>
						<img width=20 height=0>
						계좌번호 <input type=text name=up_bank2 value="<?=$bank_account[1]?>" size=20 class=input>
						<img width=20 height=0>
						예금주 <input type=text name=up_bank3 value="<?=$bank_account[2]?>" size=15 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">정산일(매월)</TD>
					<TD class="td_con1">
						<input type=text name=up_account_date value="<?=$_vdata->account_date?>" size=10 class=input>일 
						&nbsp;&nbsp;&nbsp;&nbsp; <FONT class=font_orange>* (복수기입시 10,20,30 과 같이 기입요망)</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td align="center">
					<a href="javascript:CheckForm();"><img src="images/btn_edit2.gif" width="113" height="38" border="0"></a>
					&nbsp;
					<a href="javascript:CheckDelete();"><img src="images/btn_infodelete.gif" width="113" height="38" border="0"></a>
				</td>
			</tr>
			</form>
			<form name="form3" method="post" action="vender_management.php">
			<input type=hidden name='vender' value="<?=$value?>">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">입점업체 정보 수정/삭제</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top" style="letter-spacing:-0.5pt;">- 등록된 입점업체 리스트와 기본적인 정보사항을 수정/삭제 할 수 있습니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
