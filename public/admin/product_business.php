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

$type=$_POST["type"];
$mode=$_POST["mode"];

$companycode=$_POST["companycode"];
$up_companyname=$_POST["up_companyname"];
$up_companycharge=$_POST["up_companycharge"];
$up_companychargeposition=$_POST["up_companychargeposition"];
$up_companytel=$_POST["up_companytel"];
$up_companyhp=$_POST["up_companyhp"];
$up_companyemail=$_POST["up_companyemail"];
$up_companynum=$_POST["up_companynum"];
$up_companytype=$_POST["up_companytype"];
$up_companybiz=$_POST["up_companybiz"];
$up_companyitem=$_POST["up_companyitem"];
$up_companyowner=$_POST["up_companyowner"];
$up_companyfax=$_POST["up_companyfax"];
$up_companypost1=$_POST["up_companypost1"];
$up_companypost2=$_POST["up_companypost2"];
$up_companyaddr=$_POST["up_companyaddr"];
$up_companyurl=$_POST["up_companyurl"];
$up_companybank=$_POST["up_companybank"];
$up_companybanknum=$_POST["up_companybanknum"];
$up_companymemo=$_POST["up_companymemo"];

$up_companyview1=$_POST["up_companyview1"];
$up_companyview2=$_POST["up_companyview2"];
$up_companyview3=$_POST["up_companyview3"];

$up_companyviewval = $up_companyname;
if($up_companyview1!="Y") { 
	$up_companyview1 = "N";
} else {
	$up_companyviewval.=", {$up_companycharge} ".$up_companychargeposition;
}

if($up_companyview2!="Y") { 
	$up_companyview2 = "N";
} else {
	$up_companyviewval.=", ".$up_companytel;
}

if($up_companyview3!="Y") { 
	$up_companyview3 = "N";
} else {
	$up_companyviewval.=", ".$up_companyhp;
}

$up_companyview = $up_companyview1.$up_companyview2.$up_companyview3;

if($up_companyview=="NNN") {
	$up_companyview="";
}

if(ord($up_companyname) && $type=="insert") {
	$sql = "INSERT INTO tblproductbisiness(
	companyname		,
	companycharge		,
	companychargeposition	,
	companytel		,
	companyhp		,
	companyfax		,
	companyemail		,
	companynum		,
	companytype		,
	companybiz		,
	companyitem		,
	companyowner		,
	companypost		,
	companyaddr		,
	companyurl		,
	companybank		,
	companybanknum		,
	companymemo		,
	companyview		,
	companyviewval) VALUES (
	'{$up_companyname}', 
	'{$up_companycharge}', 
	'{$up_companychargeposition}', 
	'{$up_companytel}', 
	'{$up_companyhp}', 
	'{$up_companyfax}', 
	'{$up_companyemail}', 
	'{$up_companynum}', 
	'{$up_companytype}', 
	'{$up_companybiz}', 
	'{$up_companyitem}', 
	'{$up_companyowner}', 
	'".$up_companypost1.$up_companypost2."', 
	'{$up_companyaddr}', 
	'".str_replace("http://", "", $up_companyurl)."', 
	'{$up_companybank}', 
	'{$up_companybanknum}', 
	'{$up_companymemo}', 
	'{$up_companyview}', 
	'{$up_companyviewval}')";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert('거래업체 등록이 완료되었습니다.');}</script>\n";
} else if (ord($companycode) && $type=="modify") {
	if ($mode=="result") {
		$sql = "UPDATE tblproductbisiness SET ";
		$sql.= "companyname				= '{$up_companyname}', ";
		$sql.= "companycharge			= '{$up_companycharge}', ";
		$sql.= "companychargeposition	= '{$up_companychargeposition}', ";
		$sql.= "companytel				= '{$up_companytel}', ";
		$sql.= "companyhp				= '{$up_companyhp}', ";
		$sql.= "companyfax				= '{$up_companyfax}', ";
		$sql.= "companyemail			= '{$up_companyemail}', ";
		$sql.= "companynum				= '{$up_companynum}', ";
		$sql.= "companytype				= '{$up_companytype}', ";
		$sql.= "companybiz				= '{$up_companybiz}', ";
		$sql.= "companyitem				= '{$up_companyitem}', ";
		$sql.= "companyowner			= '{$up_companyowner}', ";
		$sql.= "companypost				= '".$up_companypost1.$up_companypost2."', ";
		$sql.= "companyaddr				= '{$up_companyaddr}', ";
		$sql.= "companyurl				= '".str_replace("http://", "", $up_companyurl)."', ";
		$sql.= "companybank				= '{$up_companybank}', ";
		$sql.= "companybanknum			= '{$up_companybanknum}', ";
		$sql.= "companymemo				= '{$up_companymemo}', ";
		$sql.= "companyview				= '{$up_companyview}', ";
		$sql.= "companyviewval			= '{$up_companyviewval}' ";
		$sql.= "WHERE companycode = '{$companycode}' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){alert('거래업체 수정이 완료되었습니다.');}</script>\n";
		$type='';
		$mode='';
		$companycode='';
	} else {
		$sql = "SELECT * FROM tblproductbisiness WHERE companycode = '{$companycode}' ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
		if ($row) {
			$companycode = $row->companycode;
			$companyname = $row->companyname;
			$companynum = $row->companynum;
			$companyowner = $row->companyowner;
			$companypost1 = @substr($row->companypost,0,3);
			$companypost2 = @substr($row->companypost,3);
			$companyaddr = $row->companyaddr;
			$companybiz = $row->companybiz;
			$companyitem = $row->companyitem;
			$companytype = $row->companytype;
			$companycharge = $row->companycharge;
			$companychargeposition = $row->companychargeposition;
			$companyemail = $row->companyemail;
			$companytel = $row->companytel;
			$companyhp = $row->companyhp;
			$companyfax = $row->companyfax;
			$companybank = $row->companybank;
			$companybanknum = $row->companybanknum;
			$companyurl = $row->companyurl;
			$companymemo = $row->companymemo;
			$companyview = $row->companyview;
			
			$companyview_checked = array();
			if(ord($companyview)) {
				for($i=0; $i<strlen($companyview); $i++) {
					if(substr($companyview,$i,1)=="Y") {
						$companyview_checked[$i] = "checked";
					}
				}
			}
		} else {
			$onload="<script>window.onload=function(){alert('수정하려는 거래업체가 존재하지 않습니다.');}<script>";
			$type='';
			$mode='';
			$companycode='';
		}
	}
} else if (ord($companycode) && $type=="delete") {
	$sql = "DELETE FROM tblproductbisiness WHERE companycode = '{$companycode}' ";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblproduct SET bisinesscode='' WHERE bisinesscode = '{$companycode}' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){alert('거래업체 삭제가 완료되었습니다.');}</script>\n";
	}
	$type='';
	$mode='';
	$companycode='';
} 

if (ord($type)==0) $type="insert";
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(document.form1.up_companyname.value.length==0) {
		document.form1.up_companyname.focus();
		alert("상호(회사명)을 입력하세요");
		return;
	}
	if(document.form1.up_companycharge.value.length==0) {
		document.form1.up_companycharge.focus();
		alert("담당자 성명을 입력하세요");
		return;
	}
	if(document.form1.up_companytel.value.length==0) {
		document.form1.up_companytel.focus();
		alert("전화번호를 입력하세요");
		return;
	}
	if(document.form1.up_companyhp.value.length==0) {
		document.form1.up_companyhp.focus();
		alert("휴대폰번호를 입력하세요");
		return;
	}
	if(document.form1.up_companyemail.value.length==0) {
		document.form1.up_companyemail.focus();
		alert("이메일을 입력하세요");
		return;
	}
	if(type=="modify") {
		if(!confirm("수정할 경우 상품쪽 거래업체 정보도 동일하게 수정됩니다.\n\n거래업체 정보를 정말 수정하시겠습니까?")) {
			return;
		}
		document.form1.mode.value="result";
	}
	if(type=="insert") {
		if(!confirm("해당 거래업체 정보를 등록하시겠습니까?")) {
			return;
		}
		document.form1.mode.value="result";
	}
	document.form1.type.value=type;
	document.form1.submit();
}
function ContentSend(type,companycode) {
	if(type=="delete") {
		if(!confirm("삭제할 경우 상품쪽 거래업체 정보도 동일하게 삭제됩니다.\n\n거래업체 정보를 정말 삭제하시겠습니까?")) return;
	}
	document.form1.type.value=type;
	document.form1.companycode.value=companycode;
	document.form1.submit();
}
function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
function f_addr_search(form,post,addr,gbn) {
	window.open("../front/addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");		
}
function BusinessSMS(number) {
	document.smsform.number.value=number;
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.submit();
}
function BusinessMail(mail){
	document.mailform.rmail.value=mail;
	document.mailform.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 카테고리/상품관리 &gt;<span>상품 거래처 관리</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=companycode value="<?=$companycode?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 거래처 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품 거래처의 등록/수정/삭제를 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품 거래처 목록</div>
				</td>
			</tr>
			<tr>
				<td>
<?php
				$colspan=9;
?>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=></col>
				<col width=80></col>
				<col width=60></col>
				<col width=60></col>
				<col width=60></col>
				<col width=68></col>
				<col width=68></col>
				<col width=60></col>
				<col width=60></col>
				<TR align=center>
					<th>업체명</th>
					<th>담당자</th>
					<th>구분</th>
					<th>연락처</th>
					<th>메일</th>
					<th>주소</th>
					<th>메모</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$sql = "SELECT COUNT(*) as t_count FROM tblproductbisiness ";
				$paging = new Paging($sql,10,10);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT * FROM tblproductbisiness ORDER BY companycode DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					if(ord($row->companyhp)) {
						$row->companyhp=str_replace("-","",$row->companyhp);
					}

					echo "<TR align=center>\n";
					echo "	<TD><div class=\"ta_l\">{$row->companyname}&nbsp;</div></TD>\n";
					echo "	<TD>{$row->companycharge} {$row->companychargeposition}&nbsp;</TD>\n";
					echo "	<TD>{$row->companytype}&nbsp;</TD>\n";
					echo "	<TD><a href=\"javascript:alert('   전화번호    : ".addslashes($row->companytel)."      \\n   휴대폰번호 : ".addslashes($row->companyhp)."         \\n   팩스번호    : ".addslashes($row->companyfax)."      ');\"><img src=\"images/member_tel.gif\" border=\"0\"></a>".(ord($row->companyhp)?"<img width=2 height=0><a href=\"javascript:BusinessSMS('".addslashes($row->companyhp)."');\"><img src=\"images/member_mobile.gif\" border=\"0\"></a>":"")."</TD>\n";
					echo "	<TD><a href=\"javascript:BusinessMail('".addslashes($row->companyemail)."');\"><img src=\"images/icon_mail.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:alert('   사업장 주소 : ".addslashes(substr($row->companypost,0,3)."-".substr($row->companypost,3)." ".$row->companyaddr)."      ');\"><img src=\"images/addr_home.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD>".(ord($row->companymemo)?"<img src=\"images/ordtll_icnmemo.gif\" border=\"0\" alt=\"".htmlspecialchars($row->companymemo)."\">":"&nbsp;")."</TD>\n";
					echo "	<TD><a href=\"javascript:ContentSend('modify','{$row->companycode}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:ContentSend('delete','{$row->companycode}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
				pmysql_free_result($result);

				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align=center class="font_size">
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품거래처 등록/수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span><font color="#FF4C00">상호(회사명)</font></span></Th>
					<TD width="35%"><INPUT style="WIDTH: 100%" name=up_companyname class="input" value="<?=$companyname?>" maxlength="30" onKeyDown="chkFieldMaxLen(30)"></TD>
					<th><span><font color="#FF4C00">담당자 성명</font></span></th>
					<TD><INPUT style="WIDTH: 50%" name=up_companycharge class="input" value="<?=$companycharge?>" onKeyDown="chkFieldMaxLen(20)" maxlength="20"> 직위 : <INPUT style="WIDTH: 30%" name=up_companychargeposition class="input" value="<?=$companychargeposition?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
				</TR>
				<TR>
					<th><span><font color="#FF4C00">전화번호</font></span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companytel class="input" value="<?=$companytel?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
					<th><span><font color="#FF4C00">휴대폰번호</font></span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companyhp class="input" value="<?=$companyhp?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
				</TR>
				<TR>
					<th><span><font color="#FF4C00">이메일</font></span></th>
					<TD colspan="3"><INPUT style="WIDTH: 100%" name=up_companyemail class="input" value="<?=$companyemail?>" maxlength="70" onKeyDown="chkFieldMaxLen(70)"></TD>
				</TR>
				<TR>
					<th><span>사업자등록번호</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companynum class="input" value="<?=$companynum?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
					<th><span>업체구분</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companytype class="input" value="<?=$companytype?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
				</TR>
				<TR>
					<th><span>사업자 업태</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companybiz class="input" value="<?=$companybiz?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
					<th><span>사업자 종목</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companyitem class="input" value="<?=$companyitem?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
				</TR>
				<TR>
					<th><span>대표자 성명</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companyowner class="input" value="<?=$companyowner?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
					<th><span>팩스번호</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companyfax class="input" value="<?=$companyfax?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
				</TR>
				<tr>
					<th><span>사업장 주소</span></th>
					<td colspan="3" bgcolor="#FFFFFF">
                    <div class="table_none">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td width="80" nowrap><input type=text name="up_companypost1" value="<?=$companypost1?>" size="3" maxlength="3" class="input" onKeyDown="chkFieldMaxLen(20)"> - <input type=text name="up_companypost2" value="<?=$companypost2?>" size="3" maxlength="3" class="input" onKeyDown="chkFieldMaxLen(20)"></td>
						<td width="100%"><A href="javascript:f_addr_search('form1','up_companypost','up_companyaddr',2);" onfocus="this.blur();" style="selector-dummy: true" class="board_list hideFocus"><img src="images/icon_addr.gif" border="0"></A></td>
					</tr>
					<tr>
						<td colspan="2"><input style="WIDTH: 100%" type=text name="up_companyaddr" value="<?=$companyaddr?>" maxlength="150" class="input" onKeyDown="chkFieldMaxLen(150)"></td>
					</tr>
					</table>
                    </div>
					</td>
				</tr>
				<TR>
					<th><span>홈페이지</span></th>
					<TD colspan="3">
                    <div class="table_none">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<col width="40"></col>
					<col width=""></col>
					<tr>
						<td>http://&nbsp;</td>
						<td><INPUT style="WIDTH: 100%" name=up_companyurl class="input" value="<?=$companyurl?>" maxlength="70" onKeyDown="chkFieldMaxLen(70)"></TD>
					</tr>
					</table>
                    </div>
					</td>
				</TR>
				<TR>
					<th><span>거래은행</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_companybank class="input" value="<?=$companybank?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
					<th><span>계좌번호</span></th>
					<TD class="td_con1"><INPUT style="WIDTH: 100%" name=up_companybanknum class="input" value="<?=$companybanknum?>" maxlength="20" onKeyDown="chkFieldMaxLen(20)"></TD>
				</TR>
				<TR>
					<th><span>메모</span></th>
					<TD colspan="3"><TEXTAREA style="WIDTH: 100%; HEIGHT: 100px" name=up_companymemo class="textarea"><?=htmlspecialchars($companymemo)?></TEXTAREA></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td><b>노출항목 : </b><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;"><b><input type=checkbox checked disabled>업체명(기본)&nbsp;
			<input type=checkbox name=up_companyview1 id="idx_view1" value="Y" <?=$companyview_checked[0]?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_view1">담당자성명,직위</label>&nbsp;
			<input type=checkbox name=up_companyview2 id="idx_view2" value="Y" <?=$companyview_checked[1]?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_view2">전화번호</label>&nbsp;
			<input type=checkbox name=up_companyview3 id="idx_view3" value="Y" <?=$companyview_checked[2]?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_view3">휴대폰번호</label>
			</b></span></td></tr>
			<tr>
				<td><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 노출항목이란 상품 등록/수정 또는 주문서 엑셀 다운로드할 경우 노출되는 거래처 정보입니다.</span></td>
			</tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품 거래처 관리</span></dt>
							<dd>
							- 상품 등록/수정시 거래처 선택 항목이 출력됩니다.<br>
							- 상품 거래처 수정/삭제할 경우 상품 거래처에도 동일 반영됩니다.<br>
							- 노출항목은 상품 등록/수정할 경우 거래처 선택 항목에 노출되는 정보입니다.
							</dd>
	
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			</form>
			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=number>
			</form>
			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
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
include("copyright.php");
