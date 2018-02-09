<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$code=$_POST["code"];
$body=$_POST["body"];
$added=$_POST["added"];

if((int)$code>0) {
	$sql = "SELECT * FROM tblproductbrand WHERE bridx='{$code}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$row) {
		$onload="<script>alert(\"브랜드 선택이 잘못 되었습니다.\");</script>";
	}
}

if(ord($onload)==0) {
	if(ord($mode)) {
		if(strlen($code)) {
			if(($code == "전체" || $code == "ALL")) {
				$code = "ALL";
			}
		} else {
			$onload="<script>alert(\"브랜드 선택이 잘못 되었습니다.\");</script>";
		}
	}
	
	if(ord($onload)==0) {
		if($mode == "update" && ord($body)) {
			if($added=="Y") {
				$leftmenu="Y";
			} else {
				$leftmenu="N";
			}
			
			$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='brlist' AND code='{$code}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			if($row->cnt==0) {
				$sql = "INSERT INTO tbldesignnewpage( 
				type,		
				subject,
				leftmenu,	
				body,		
				code) VALUES (		
				'brlist', 
				'상품 브랜드', 
				'{$leftmenu}', 
				'{$body}', 
				'{$code}')";
				pmysql_query($sql,get_db_conn());
			} else {
				$sql = "UPDATE tbldesignnewpage SET 
				leftmenu	= '{$leftmenu}', 
				body		= '{$body}' 
				WHERE type='brlist' AND code='{$code}' ";
				pmysql_query($sql,get_db_conn());
			}
			pmysql_free_result($result);

			$sql="";
			if($leftmenu=="Y") {	//브랜드 화면 개별디자인 적용
				$sql = "UPDATE tblproductbrand SET list_type=SUBSTR(list_type,1,4)||'U' ";
			} else if($leftmenu=="N") {	//브랜드화면 템플릿 적용
				$sql = "UPDATE tblproductbrand SET list_type=SUBSTR(list_type,1,4) ";
			}
			if(ord($sql)) {
				if((int)$code>0) {
					$sql.= "WHERE bridx='{$code}' ";
					$msg="해당 상품 브랜드 디자인이 ";
				} else {
					$msg="모든 상품 브랜드 디자인이 ";
				}
				pmysql_query($sql,get_db_conn());

				if($leftmenu=="Y") {
					$msg.="개별디자인으로 변경되었습니다.";
				} else if($leftmenu=="N") {
					$msg.="기본으로 제공되는 템플릿 디자인으로 변경되었습니다.";
				}
			}
			if((int)$code>0) {
				$onload="<script>alert(\"{$msg}\");</script>";
			} else {
				echo "<script>alert(\"{$msg}\");parent.location.reload();</script>";
				exit;
			}
		} else if($mode=="delete") {
			$sql = "DELETE FROM tbldesignnewpage WHERE type='brlist' AND code='{$code}' ";
			pmysql_query($sql,get_db_conn());
			
			$sql = "UPDATE tblproductbrand SET list_type=SUBSTR(list_type,1,4) ";
			if((int)$code>0) {
				$sql.= "WHERE bridx='{$code}' ";
				$msg="해당 상품 브랜드 디자인이 기본으로 제공되는 템플릿 디자인으로 변경되었습니다.";
			} else {
				$msg="모든 상품 브랜드 개별디자인이 삭제되었습니다.\\n해당 상품 브랜드의 개별디자인은 삭제되지 않았습니다.";
			}
			pmysql_query($sql,get_db_conn());

			if((int)$code>0) {
				$onload="<script>alert(\"{$msg}\");</script>";
			} else {
				echo "<script>alert(\"{$msg}\");parent.location.reload();</script>";
				exit;
			}
		} else if($mode=="clear") {
			$body="";
			$sql = "SELECT body FROM tbldesigndefault WHERE type='brlist' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$body=$row->body;
			}
			pmysql_free_result($result);
		}
	}
}

if($mode!="clear") {
	$body="";
	$added="";
	if(ord($code)) {
		if(($code == "전체" || $code == "ALL")) {
			$code = "ALL";
		}
	}
	$sql = "SELECT leftmenu, body FROM tbldesignnewpage WHERE type='brlist' AND code='{$code}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$added=$row->leftmenu;

		if($added=="Y" && $code == "ALL") {
			$design_default = "LU";
		}
	} else {
		$added="Y";
	}
	pmysql_free_result($result);
}

if((int)$code>0) {
	$sql = "SELECT * FROM tblproductbrand WHERE bridx='{$code}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	
	if(strlen($row->list_type)==5) {
		$design_default = "LU";
	} else {
		$design_default = $row->list_type;
	}
}
?>

<?php include("header.php"); ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<!--
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('MainPrdtFrame')");</script>
-->

<script type="text/javascript">
 function init(){
  var doc= document.getElementById("divName");
  if(doc.offsetHeight!=0){
   pageheight = doc.offsetHeight;
   parent.document.getElementById("MainPrdtFrame").height=pageheight+"px";
   }
 }

 window.onload=function(){
  init();
 }
</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(mode) {
	if(document.form1.code.value.length==0) {
		alert("상품 브랜드를 선택해 주세요.");
		parent.document.form1.up_brandlist.focus();
		return;
	}

	if(mode=="update") {
		if(document.form1.body.value.length==0) {
			alert("상품 브랜드 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		if(document.form1.code.value=="ALL") {
			msg="모든 ";
		} else {
			msg="해당 ";
		}
		if(document.form1.added.checked) {
			if(!confirm(msg+"상품 브랜드 디자인을 개별디자인으로 변경하시겠습니까?")) {
				return;
			}
		} else {
			if(!confirm(msg+"상품 브랜드 디자인을 기본으로 제공되는 템플릿 디자인으로 변경하시겠습니까?\n\n입력하신 개별디자인 소스는 저장됩니다.")) {
				return;
			}
		}
		document.form1.mode.value=mode;
		document.form1.submit();
	} else if(mode=="delete") {
		if(confirm("상품 브랜드 개별디자인을 삭제하시겠습니까?")) {
			document.form1.mode.value=mode;
			document.form1.submit();
		}
	} else if(mode=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.mode.value=mode;
		document.form1.submit();
	}
}

<?php
echo "parent.document.all[\"preview_img\"].style.display=\"none\";";
if(ord($design_default)) {
	echo "parent.document.all[\"preview_img\"].src=\"images/sample/brand{$design_default}.gif\";\n";
	echo "parent.document.all[\"preview_img\"].style.display=\"\";";
}
?>
//-->
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css">

<div id="divName">
<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<tr bgcolor="white">
	<td>
    	<div class="title_depth3_sub">상품 브랜드별 등록/수정</div>
    </td>
</tr>
<tr>
	<td>
    	<div class="help_info01_wrap">
			<ul>
				<li>1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.</li>
				<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.</li>
				<li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)<br>
		&nbsp;<b>&nbsp;&nbsp;</b>※ 상품 브랜드 페이지 노출 설정은 <a href="javascript:parent.topframe.GoMenu(4,'product_brand.php');"><span class="font_blue"><b>상품관리 > 카테고리/상품관리 > 상품 브랜드 설정 관리</span></b></a> 에서 설정해 주세요.</p></li>
			</ul>
		</div>
	</td>
</tr>
<tr>
	<td><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea><br><input type=checkbox name=added value="Y" <?php if($added=="Y")echo"checked";?>> <b><span class="font_orange">적용하기 체크</span>(체크해야만 디자인이 적용됩니다. 미체크시 소스만 보관되고 적용은 되지 않습니다.)</b></td>
</tr>
<tr>
	<td height=10></td>
</tr>
<tr>
	<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="0"></a></td>
</tr>
<tr>
	<td height=20></td>
</tr>
</form>
</table>
</div>
<?=$onload?>

</body>
</html>
