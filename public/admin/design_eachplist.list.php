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
$list_type=$_POST["list_type"];

if(ord($code)==0) $code="ALL";

$body=$_POST["body"];
$added=$_POST["added"];


if($mode=="update" && ord($body)) {
	if($added=="Y") {
		$leftmenu="Y";
	} else {
		$leftmenu="N";
	}

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='prlist' AND code='{$code}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,leftmenu,body,code) VALUES (
		'prlist', 
		'상품 카테고리', 
		'{$leftmenu}', 
		'{$body}',
		'{$code}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		leftmenu	= '{$leftmenu}', 
		body		= '{$body}' 
		WHERE type='prlist' AND code='{$code}' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);

	$sql="";
	if($leftmenu=="Y") {	//카테고리화면 개별디자인 적용
		$sql = "UPDATE tblproductcode SET list_type=SUBSTR(list_type,1,5)||'U' ";
	} else if($leftmenu=="N") {	//카테고리화면 템플릿 적용
		$sql = "UPDATE tblproductcode SET list_type=SUBSTR(list_type,1,5) ";
	}
	if(ord($sql)) {
		$sql.= "WHERE 1=1 ";
		if(strlen($code)==12) {
			$code_a=substr($code,0,3);
			$code_b=substr($code,3,3);
			$code_c=substr($code,6,3);
			$code_d=substr($code,9,3);
			$sql.= "AND code_a='{$code_a}' AND code_b='{$code_b}' ";
			$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
			$msg="해당 상품 카테고리 디자인이 ";
			if($leftmenu=="Y") {
				$script_p="parent.ModifyCodeDesign('{$code}','".substr($list_type,0,5)."U"."','0');";
			} else if($leftmenu=="N") {
				$script_p="parent.ModifyCodeDesign('{$code}','".substr($list_type,0,5)."','0');";
			}
		} else {
			$msg="모든 상품 카테고리 디자인이 ";
		}
		pmysql_query($sql,get_db_conn());

		if($leftmenu=="Y") {
			$msg.="개별디자인으로 변경되었습니다.";
		} else if($leftmenu=="N") {
			$msg.="기본으로 제공되는 템플릿 디자인으로 변경되었습니다.";
		}
	}
	if(strlen($code)==12) {
		$onload="<script>{$script_p} alert(\"{$msg}\");</script>";
	} else {
		echo "<script>alert(\"{$msg}\");parent.location.reload();</script>";
		exit;
	}
} elseif($mode=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='prlist' AND code='{$code}' ";
	pmysql_query($sql,get_db_conn());

	
	$sql = "UPDATE tblproductcode SET list_type=SUBSTR(list_type,1,5) ";
	$sql.= "WHERE 1=1 ";
	if(strlen($code)==12) {
		$code_a=substr($code,0,3);
		$code_b=substr($code,3,3);
		$code_c=substr($code,6,3);
		$code_d=substr($code,9,3);
		$sql.= "AND code_a='{$code_a}' AND code_b='{$code_b}' ";
		$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
		$msg="해당 상품 카테고리 디자인이 기본으로 제공되는 템플릿 디자인으로 변경되었습니다.";
		$script_p="parent.ModifyCodeDesign('{$code}','".substr($list_type,0,5)."','0');";
	} else {
		$msg="모든 상품 카테고리 개별디자인이 삭제되었습니다.\\n해당 상품 카테고리의 개별디자인은 삭제되지 않았습니다.";
	}
	pmysql_query($sql,get_db_conn());

	if(strlen($code)==12) {
		$onload="<script>{$script_p} alert(\"{$msg}\");</script>";
	} else {
		echo "<script>alert(\"{$msg}\");parent.location.reload();</script>";
		exit;
	}
} elseif($mode=="clear") {
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='prlist' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
	}
	pmysql_free_result($result);
}

if($mode!="clear") {
	$body="";
	$added="";
	$sql = "SELECT leftmenu,body FROM tbldesignnewpage WHERE type='prlist' AND code='{$code}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$added=$row->leftmenu;
	} else {
		$added="Y";
	}
	pmysql_free_result($result);
}
include("header.php"); 
?>
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
	if(mode=="update") {
		if(document.form1.body.value.length==0) {
			alert("상품 카테고리 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		if(document.form1.code.value.length==12) {
			msg="해당 ";
		} else {
			msg="모든 ";
		}
		if(document.form1.added.checked) {
			if(!confirm(msg+"상품 카테고리 디자인을 개별디자인으로 변경하시겠습니까?")) {
				return;
			}
		} else {
			if(!confirm(msg+"상품 카테고리 디자인을 기본으로 제공되는 템플릿 디자인으로 변경하시겠습니까?\n\n입력하신 개별디자인 소스는 저장됩니다.")) {
				return;
			}
		}
		document.form1.mode.value=mode;
		document.form1.submit();
	} else if(mode=="delete") {
		if(confirm("상품 카테고리 개별디자인을 삭제하시겠습니까?")) {
			document.form1.mode.value=mode;
			document.form1.submit();
		}
	} else if(mode=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.mode.value=mode;
		document.form1.submit();
	}
}
//-->
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css">
<div id="divName">
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=list_type value="<?=$list_type?>">
<tr bgcolor="white">
	<td>
		<!-- 소제목 -->
		<div class="title_depth3_sub">상품 카테고리별 등록/수정</div>
	</td>
</tr>
<tr>
	<td>
        <div class="help_info01_wrap"> 
            <ul>
				<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
				<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.</li>
				<li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</li>
			</ul>
        </div>
    </td>
</tr>
<tr>
	<td><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea><br><input type=checkbox name=added value="Y" <?php if($added=="Y")echo"checked";?>> <b><span class="font_orange">적용하기 체크</span>(체크해야만 디자인이 적용됩니다. 미체크시 소스만 보관되고 적용은 되지 않습니다.)</b></td>
</tr>
<tr>
	<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="0"></a></td>
</tr>
</form>
<tr>
				<td height=20></td>
</tr>
<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>상품 카테고리 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></dt>
                      <dd>
                        <table border=0 cellpadding=0 cellspacing=0 width=100%>
                        <col width=150></col>
                        <col width=></col>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[CODENAME]</td>
                            <td class=td_con1 style="padding-left:5;">
                            현재 카테고리명
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[CODENAVI??????_??????]</td>
                            <td class=td_con1 style="padding-left:5;">
                            카테고리 네비게이션 
                                    <br><img width=10 height=0>
                                    <FONT class=font_orange>앞?????? : 현재 카테고리가 속한 카테고리 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
                                    <br><img width=10 height=0>
                                    <FONT class=font_orange>뒤?????? : 현재 카테고리의 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
                                    <br>
                                    <FONT class=font_blue>예) [CODENAVI] or [CODENAVI000000_FF0000]</FONT>
                            </td>
                        </tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[CLIPCOPY]</td>
                            <td class=td_con1 style="padding-left:5;">
                            현재주소 복사 버튼 <FONT class=font_blue>(예:&lt;a href=[CLIPCOPY]>주소복사&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[CODEEVENT]</td>
                            <td class=td_con1 style="padding-left:5;">
                            카테고리별 이벤트 이미지/html
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[CODEGROUP]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품 카테고리 그룹
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right bgcolor=#E9A74E style="padding-right:15">상품 카테고리 그룹 관련 스타일 정의</td>
                            <td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;">
                                        <img width=10 height=0>
                                        <FONT class=font_orange>#group1_td - 상위카테고리 TD 스타일 정의 (사이즈 및 백그라운드컬러)</FONT>
                                        <br><img width=100 height=0>
                                        <FONT class=font_blue>예) #group1_td { background-color:#E6E6E6;width:25%; }</FONT>
                                        <br><img width=0 height=7><br><img width=10 height=0>
                                        <FONT class=font_orange>#group2_td - 하위카테고리 TD 스타일 정의 (사이즈 및 백그라운드컬러)</FONT>
                                        <br><img width=100 height=0>
                                        <FONT class=font_blue>예) #group2_td { background-color:#EFEFEF; }</FONT>
                                        <br><img width=0 height=7><br><img width=10 height=0>
                                        <FONT class=font_orange>#group_line - 상위그룹과 상위그룹 사이의 가로라인 셀 스타일 정의</FONT>
                                        <br><img width=100 height=0>
                                        <FONT class=font_blue>예) #group_line { background-color:#FFFFFF;height:1px; }</FONT>
                            <pre style="line-height:15px">
            <B>[사용 예]</B> - 내용 본문에 아래와 같이 정의하시면 됩니다.
            
            <FONT class=font_blue>&lt;style>
              #group1_td { background-color:#E6E6E6;width:25%; }
              #group2_td { background-color:#EFEFEF; }
              #group_line { background-color:#FFFFFF;height:1px; }
            &lt;/style></FONT></pre>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[NEWITEM1??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션신규상품 - 이미지A형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[NEWITEM2??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션신규상품 - 이미지B형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[NEWITEM????????_??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션신규상품 - 이미지A형/이미지B형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 위에 제공된 신규상품 형태 (1:이미지A형, 2:이미지B형)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 사이의 가로라인 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 시중가격 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 적립금 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>_?? : 신규상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [NEWITEM142NNYN2_10], [NEWITEM222LYYY2_5]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[NEWITEM3??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션신규상품 - 리스트형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>?? : 신규상품 진열갯수 (01~20)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[NEWITEM3??????]</td>
                            <td class=td_con1 style="padding-left:5;">
                            신규상품 - 리스트형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>?? : 신규상품 진열갯수 (01~20)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 제조사 표시여부 (Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 시중가격 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 적립금 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 신규상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [NEWITEM304YYY4]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[BESTITEM1??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션인기상품 - 이미지A형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[BESTITEM2??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션인기상품 - 이미지B형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[BESTITEM????????_??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션인기상품 - 이미지A형/이미지B형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 위에 제공된 인기상품 형태 (1:이미지A형, 2:이미지B형)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 사이의 가로라인 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 시중가격 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 적립금 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>_?? : 인기상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [BESTITEM142NNYN2_10], [BESTITEM222LYYY2_5]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[BESTITEM3??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션인기상품 - 리스트형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>?? : 인기상품 진열갯수 (01~20)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[BESTITEM3??????]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션인기상품 - 리스트형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>?? : 인기상품 진열갯수 (01~20)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 제조사 표시여부 (Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 시중가격 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 적립금 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 인기상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [BESTITEM304YYY4]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[HOTITEM1??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션추천상품 - 이미지A형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[HOTITEM2??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션추천상품 - 이미지B형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[HOTITEM????????_??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션추천상품 - 이미지A형/이미지B형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 위에 제공된 추천상품 형태 (1:이미지A형, 2:이미지B형)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 사이의 가로라인 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 시중가격 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 적립금 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>_?? : 추천상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [HOTITEM142NNYN2_10], [HOTITEM222LYYY2_5]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[HOTITEM3??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션추천상품 - 리스트형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>?? : 추천상품 진열갯수 (01~20)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[HOTITEM3??????]</td>
                            <td class=td_con1 style="padding-left:5;">
                            섹션추천상품 - 리스트형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>?? : 추천상품 진열갯수 (01~20)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 제조사 표시여부 (Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 시중가격 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 적립금 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 추천상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [HOTITEM304YYY4]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[PRLIST1??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품목록 - 이미지A형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[PRLIST2??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품목록 - 이미지B형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[PRLIST????????_??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품목록 - 이미지A형/이미지B형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 위에 제공된 상품목록 형태 (1:이미지A형, 2:이미지B형)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 사이의 가로라인 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 시중가격 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 적립금 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>_?? : 상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [PRLIST142NNYN2_10], [PRLIST222LYYY2_5]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[PRLIST3??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품목록 - 리스트형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>?? : 상품목록 진열갯수 (01~20)</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[PRLIST3???????]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품목록 - 리스트형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>?? : 상품 진열갯수 (01~20)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 이미지 표시여부 (Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 제조사 표시여부 (Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 시중가격 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 적립금 표시여부(Y/N)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [PRLIST304NYYY4]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[PRLIST4??_??]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품목록 - 공동구매형
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 라인별 상품갯수(2~4)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1~8)</FONT>
                                        <br><img width=10 height=0>
                                        <FONT class=font_orange>_?? : 상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                        <br>
                                        <FONT class=font_blue>예) [PRLIST423_5]</FONT>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right bgcolor=#E9A74E style="padding-right:15">진열상품(신규/인기/추천/상품목록) 스타일 정의</td>
                            <td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;">
                                        <img width=10 height=0>
                                        <FONT class=font_orange>#prlist_colline - 이미지/리스트형의 가로라인 셀 스타일 정의</FONT>
                                        <br><img width=100 height=0>
                                        <FONT class=font_blue>예) #prlist_colline { background-color:#f4f4f4;height:1px; }</FONT>
                                        <br><img width=0 height=7><br><img width=10 height=0>
                                        <FONT class=font_orange>#prlist_colline - 이미지/리스트형의 가로라인 셀 스타일 정의</FONT>
                                        <br><img width=100 height=0>
                                        <FONT class=font_blue>예) #prlist_rowline { background-color:#f4f4f4;width:1px; }</FONT>
                            <pre style="line-height:15px">
            <B>[사용 예]</B> - 내용 본문에 아래와 같이 정의하시면 됩니다.
            
            <FONT class=font_blue>&lt;style>
              #prlist_colline { background-color:#f4f4f4;height:1px; }
              #prlist_rowline { background-color:#f4f4f4;width:1px; }
            &lt;/style></FONT></pre>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[TOTAL]</td>
                            <td class=td_con1 style="padding-left:5;">
                            총 상품수 <FONT class=font_blue>(예:[TOTAL]건)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[SORTPRODUCTUP]</td>
                            <td class=td_con1 style="padding-left:5;">
                            제조사 ㄱㄴㄷ순 정렬  <FONT class=font_blue>(예:&lt;a href=[SORTPRODUCTUP]>제조사순▲&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[SORTPRODUCTDN]</td>
                            <td class=td_con1 style="padding-left:5;">
                            제조사 ㄷㄴㄱ순 정렬 <FONT class=font_blue>(예:&lt;a href=[SORTPRODUCTDN]>제조사순▼&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[SORTNAMEUP]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품명 ㄱㄴㄷ순 정렬 <FONT class=font_blue>(예:&lt;a href=[SORTNAMEUP]>상품명순▲&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[SORTNAMEDN]</td>
                            <td class=td_con1 style="padding-left:5;">
                            상품명 ㄷㄴㄱ순 정렬 <FONT class=font_blue>(예:&lt;a href=[SORTNAMEDN]>상품명순▼&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[SORTPRICEUP]</td>
                            <td class=td_con1 style="padding-left:5;">
                            낮은 상품가격순 <FONT class=font_blue>(예:&lt;a href=[SORTPRICEUP]>가격순▲&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[SORTPRICEDN]</td>
                            <td class=td_con1 style="padding-left:5;">
                            높은 상품가격순 <FONT class=font_blue>(예:&lt;a href=[SORTPRICEDN]>가격순▼&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[SORTRESERVEUP]</td>
                            <td class=td_con1 style="padding-left:5;">
                            낮은 적립금순 <FONT class=font_blue>(예:&lt;a href=[SORTRESERVEUP]>적립금순▲&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[SORTRESERVEDN]</td>
                            <td class=td_con1 style="padding-left:5;">
                            높은 적립금순 <FONT class=font_blue>(예:&lt;a href=[SORTRESERVEDN]>적립금순▼&lt;/a>)</font>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
                            <td class=table_cell align=right style="padding-right:15">[PAGE]</td>
                            <td class=td_con1 style="padding-left:5;">
                            페이지 표시
                            </td>
                        </tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        </table>
                        </dd>
                    </dl>
                    <dl>
                    	<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
                    </dl>
                </div>				
                </td>
			</tr>
			<tr>
	<td height="50"></td>
</tr>
</table>
</div>
<?=$onload?>
</body>
</html>
