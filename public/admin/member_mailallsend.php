<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

########################### TEST 쇼핑몰 확인 ##########################
DemoShopCheck("데모버전에서는 접근이 불가능 합니다.", "history.go(-1)");
#######################################################################

####################### 페이지 접근권한 check ###############
$PageCode = "me-3";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$shopemail=$_shopdata->info_email;
$shopname=$_shopdata->shopname;

$from=$_POST["from"];
$rname=$_POST["rname"];
$group_code=$_POST["group_code"];
$subject=$_POST["subject"];
$body=$_POST["body"];


if (ord($subject) && ord($body)) {
	$qry = "WHERE (news_yn='Y' OR news_yn='M') ";
	$qry.=$_POST[logincnt_s]?" and logincnt >= {$_POST[logincnt_s]} ":"";
	$qry.=$_POST[logincnt_e]?" and logincnt <= {$_POST[logincnt_e]} ":"";
	$qry.=$_POST[search_start]?" and logindate > '".str_replace("-", "", $_POST[search_start])."' ":"";
	$qry.=$_POST[search_end]?" and logindate > '".str_replace("-", "", $_POST[search_end])."' ":"";

	if ($group_code!="ALL") $qry.= "AND group_code = '{$group_code}' ";

	$sql = "SELECT COUNT(*) as cnt FROM tblmember ";
	$sql.= $qry;
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	$cnt = $row->cnt;
	pmysql_free_result($result);

	$sql = "SELECT email, name, date, id FROM tblmember ";
	$sql.= $qry;

	$result = pmysql_query($sql,get_db_conn());

	$maildate = date("YmdHis");
	$filename = $maildate.".php";
	if ($cnt>0) $fp=fopen($Dir.DataDir."groupmail/".$filename,"w");

	$count=0;
	while ($row=pmysql_fetch_object($result)) {
		if (strpos($row->email,"@")!==false && strpos($row->email,".")!==false && strpos($row->email,"'")===false) {
			fputs($fp,"<?{$row->email},{$row->name},{$row->date},{$row->id}?>\n");
			$count++;
		}
	}
	pmysql_free_result($result);
	if ($cnt>0) fclose($fp);

	if ($count==0) {
		alert_go('메일을 보낼 회원이 없습니다.',-1);
	} else {
		$html="Y";
		$body = str_replace("[NOMAIL]","<a href=http://{$shopurl}[NOMAIL]>수신거부</a>",$body);

		$sql = "INSERT INTO tblgroupmail(
		date		,
		issend		,
		html		,
		fromemail	,
		shopname	,
		filename	,
		subject		,
		body) VALUES (
		'{$maildate}', 
		'N', 
		'{$html}', 
		'{$from}', 
		'{$rname}', 
		'{$filename}', 
		'{$subject}', 
		'{$body}')";
		pmysql_query($sql,get_db_conn());

		#발송 프로세서를 호출해야할까??? 아니면 [단체메일 발송내역 관리]에서 일괄 발송할 수 있게 해줄까???

		echo "<script>alert('단체메일 발송준비가 완료되었습니다.\\n\\n네트워크 부하가 적은 새벽시간대에 발송하시기 바랍니다.\\n\\n########## [단체메일 발송내역 관리]에서 발송 ##########');</script>";
	}
}

if (ord($shopemail)==0) {
	echo "<script>alert(\"[상점관리]=>[기본정보관리]에서 관리자 이메일을 입력하셔야 합니다.\");parent.topframe.location.href=\"JavaScript:GoMenu(1,'shop_basicinfo.php')\";</script>";
	exit;
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}


_editor_url = "htmlarea/";

function ChangeEditer(mode,obj){
	if (mode==form1.htmlmode.value) {
		return;
	} else {
		obj.checked=true;
		editor_setmode('body',mode);
	}
	form1.htmlmode.value=mode;
}

function CheckForm() {
	if(document.form1.from.value.length==0) {
		alert("보내는 사람 이메일을 입력하세요.");
		document.form1.from.focus();
		return;
	}
	if(document.form1.subject.value.length==0) {
		alert("메일 제목을 입력하세요.");
		document.form1.subject.focus();
		return;
	}
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.body.value=sHTML;
	if(document.form1.body.value.length==0) {
		alert("메일 본문을 입력하세요.");
		document.form1.body.focus();
		return;
	}
	if (document.form1.sendyn.value=="N") {
		if (confirm("메일을 보내시겠습니까?")) {
			document.form1.body.value='<style>\n'
			+ 'body { background-color: #FFFFFF; font-family: "굴림"; font-size: x-small; } \n'
			+ '</style>\n'+document.form1.body.value;
			document.form1.sendyn.value="Y";
			document.form1.submit();
		} else return;
	} else {
		alert("이미 메일을 보냈거나 발송중입니다.");
	}
}

function MailPreview() {
	if (document.form1.body.value.length==0) {
		alert("내용을 입력하세요.");return;
	}
	var p = window.open("about:blank","pop","height=550,width=750,scrollbars=yes");
	p.document.write('<title>단체메일 미리보기</title>');
	p.document.write('<style>\n');
	p.document.write('body { background-color: #FFFFFF; font-family: "굴림"; font-size: x-small; } \n');
	p.document.write('P {margin-top:2px;margin-bottom:2px;}\n');
	p.document.write('</style>\n');
	p.document.write(document.form1.body.value);
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원관리 부가기능 &gt;<span>단체메일 발송</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">단체메일 발송</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 전체회원 또는 그룹회원에게 메일을 발송할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data" >
			<input type=hidden name=htmlmode value='wysiwyg'>
			<input type=hidden name=sendyn value="N">
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>보내는 사람 이메일</span></th>
					<TD><input name=from size=50 value="<?=$shopemail?>" onfocus="this.blur();alert('관리자 메일은 [상점관리]=>[기본정보관리]의 쇼핑몰 정보설정에서 변경이 가능합니다.');" class="input">&nbsp;<span class="font_orange">＊필수입력</span></TD>
				</TR>
				<TR>
					<th><span>보내는 사람 이름</span></th>
					<TD><input name=rname size=50 value="<?=$shopname?>" class="input"></TD>
				</TR>
				<TR>
					<th><span>그룹 선택</span></th>
					<TD>
						<select name=group_code style="width:273" class="select">
						<option value="ALL">전체 메일 보내기
<?php
						$sql = "SELECT group_code,group_name FROM tblmembergroup order by group_level ";
						$result = pmysql_query($sql,get_db_conn());
						$count = 0;
						while ($row=pmysql_fetch_object($result)) {
							echo "<option value='{$row->group_code}'";
							if ($group_code==$row->group_code) {
								echo " selected";
							}
							echo ">{$row->group_name}</option>";
						}
?>
						</select>
					</TD>
				</TR>
				</tr>
					<th><b>방문 횟수 : </b></th>
					<td>
						<input type="text" name="logincnt_s" size="5" value="<?=$_REQUEST[logincnt_s]?>"> ~ <input type="text" name="logincnt_e" size="5" value="<?=$_REQUEST[logincnt_e]?>">
					</td>
				</tr>
				</tr>
					<th><b>방문 일자 : </b></th>
					<td>
						<input class="input_bd_st01" type="text" size="10" name="search_start" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end"  size="10" value="<?=$search_end?>"/>
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
					</td>
				</tr>
				<tr>
					<th><span>제 목</span></th>
					<TD>
					<div class="table_none">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="290"><input name=subject size=80 class="input"></td>
						<td width="290"><span class="font_orange">＊필수입력</span></td>
					</tr>
					</table>
					</div>
					</TD>
				</tr>
				<!--
				<tr>
					<th><span>편집방법 선택</span></th>
					<TD><input type=radio name=chk_webedit checked onclick="JavaScript:ChangeEditer('wysiwyg',this)" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;">웹편집기로 입력하기(권장) <input type=radio name=chk_webedit onclick="JavaScript:ChangeEditer('textedit',this);" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;">직접 HTML로 입력하기</TD>
				</tr>
				-->
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#E0DFE3" style="padding:3"><textarea id="ir1" name=body rows=20 wrap=off style="WIDTH: 100%; HEIGHT: 300px"></TEXTAREA></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/btn_mailsend.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:MailPreview();"><img src="images/btn_view.gif" border="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>메일발송은 받는 메일서버와 네트워크의 상태, 부정확한 메일주소에 따라서 발송이 지연 또는 전달되지 않을 수 있습니다.</span></dt>

	
						</dl>
						<dl>
							<dt><span>[NAME], [DATE], [NOMAIL]의 태그는 메일 발송시 변환되어 발송되며, 미리보기에서는 그대로 보여집니다.</span></dt>
						</dl>
						<dl>
							<dt><span>메일 제목에 고객의 이름 넣는 방법</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">입력방법</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%"><FONT color=#ff4800><B>[NAME]</B></FONT> 고객님께 2주간 최고 20% 할인 쿠폰 가전 초특가 타임을 드립니다.</TD>
								</TR>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">보낸사례</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%"><FONT color=#ff4800><B>홍길동</B></FONT> 고객님께 2주간 최고 20% 할인 쿠폰 가전 초특가 타임을 드립니다.</TD>
								</TR>
								</TABLE>		
							</dd>

						</dl>

						<dl>
							<dt><span>메일 본문에 고객의 이름 넣는 방법</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">입력방법</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%"><FONT color=#ff4800><B>[NAME]</B></FONT> 고객님 안녕하세요~ <BR>이번 저희 쇼핑몰에서 가전제품 초특가 할인 이벤트를 실시합니다.</TD>
								</TR>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">보낸사례</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%"><FONT color=#ff4800><B>홍길동</B></FONT> 고객님 안녕하세요~ <BR>이번 저희 쇼핑몰에서 가전제품 초특가 할인 이벤트를 실시합니다.</TD>
								</TR>
								</TABLE>
							</dd>

						</dl>

						<dl>
							<dt><span>메일을 보내시는 경우에 꼭 <b><font color="black">고객의 동의확인 및 수신거부 메세지</font></b>를 넣어 주세요!</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">입력방법</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%"><FONT color=#ff4800><B>[NAME]</B></FONT> 고객님께서는 <FONT color=#ff4800><B>[DATE]</B></FONT>에 OOO쇼핑몰의 메일 발송에 동의하셨습니다. <BR>저희 OOO쇼핑몰의 메일을 더이상 받기를 원하지 않으면, <FONT color=#ff4800><B>[NOMAIL]</B></FONT>를 해주시기 바랍니다.</TD>
								</TR>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">보낸사례</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%"><FONT color=#ff4800><B>홍길동</B></FONT> 고객님께서는 <FONT color=#ff4800><B>2006년04월13일 (08:30)</B></FONT>에 OOO쇼핑몰의 메일 발송에 동의하셨습니다. 저희 OOO쇼핑몰의 메일을 더이상 받기를 원하지 않으면, <FONT color=#ff4800><B>수신거부</B></FONT>를 해주시기 바랍니다.</TD>
								</TR>
								</TABLE>
							</dd>

						</dl>

						<dl>
							<dt><span>단체메일발송 입력폼</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%">고객 이름을 대체하는 태그입니다. [제목과 본문내용에 사용 가능합니다]</TD>
								</TR>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[DATE]</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">회원가입일을 대체하는 태그입니다. [본문내용에만 사용 가능합니다]</TD>
								</TR>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NOMAIL]</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%">수신거부 링크를 대체하는 태그입니다. [본문내용에만 사용 가능합니다]</TD>
								</TR>
								</TABLE>
							</dd>

						</dl>


					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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

<SCRIPT LANGUAGE="JavaScript">
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		}, 
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>
<?=$onload?>
<?php 
include("copyright.php");
