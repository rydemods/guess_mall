<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$btype=$_POST["btype"];
if(ord($btype) && $btype!="L" && $btype!="W" && $btype!="I" && $btype!="B") {
	$onload="<script>window.onload=function(){alert(\"게시판 형태 선택이 잘못되었습니다.\");}</script>";
	$btype="";
}

$prqnaboardname="";
if($btype=="L") {
	$etcfield=$_shopdata->etcfield;
	$prqnaboard=getEtcfield($etcfield,"PRQNA");
	if(ord($prqnaboard)) {
		$sql = "SELECT board_name FROM tblboardadmin ";
		$sql.= "WHERE board='{$prqnaboard}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$prqnaboardname=str_replace("<script>","",str_replace("</script>","",$row->board_name));
		} else {
			$etcfield=setEtcfield($etcfield,"PRQNA","");
			$prqnaboard="";
		}
		pmysql_free_result($result);
	}
	$_shopdata->etcfield=$etcfield;
}

$board=$_POST["board"];
$board_name=$_POST["board_name"];
$board_skin=$_POST["board_skin"];
$board_width=$_POST["board_width"];
$list_num=$_POST["list_num"];
$page_num=$_POST["page_num"];
$max_filesize=$_POST["max_filesize"];
$use_imgresize="N";
$img_maxwidth=$_POST["img_maxwidth"];
$img_align=$_POST["img_align"];
$passwd=$_POST["passwd"];
$grant_write=$_POST["grant_write"];
$grant_view=$_POST["grant_view"];
$group_code=$_POST["group_code"];
$use_reply=$_POST["use_reply"];
$grant_reply=$_POST["grant_reply"];
$use_comment=$_POST["use_comment"];
$grant_comment=$_POST["grant_comment"];
$use_lock=$_POST["use_lock"];
$writer_gbn=(int)$_POST["writer_gbn"];
$prqna_gbn=$_POST["prqna_gbn"];

if($mode=="insert" && ord($btype)) {
	$date=date("YmdHis");
	if(ord($board)==0) {
		alert_go('게시판 코드를 입력하세요.',-1);
	}
	if(ord($board_name)==0) {
		alert_go('게시판 제목을 입력하세요.',-1);
	}
	if(ord($board_width)==0) $board_width=690;
	if(ord($list_num)==0) $list_num=20;
	if(ord($page_num)==0) $page_num=10;
	if(ord($max_filesize)==0) $max_filesize=2;
	if(ord($img_maxwidth)==0) $img_maxwidth=650;
	if(ord($use_lock)==0) $use_lock="N";
	if(ord($use_reply)==0) $use_reply="Y";
	if(ord($use_comment)==0) $use_comment="Y";
	if(ord($use_imgresize)==0) $use_imgresize="Y";
	if(ord($img_align)==0) $img_align="center";
	if(ord($use_lock)==0) $use_lock="N";
	if(ord($use_reply)==0) $use_reply="Y";
	if(ord($use_comment)==0) $use_comment="Y";
	if(ord($use_imgresize)==0) $use_imgresize="Y";
	if(ord($grant_reply)==0) $grant_reply="N";

	//게시판 코드 중복 체크 및 코드 문자 체크

	$sql = "SELECT thread_no FROM tblboardadmin WHERE board='qna' ";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	$thread_no=$row->thread_no;

	$sql = "INSERT INTO tblboardadmin(board) VALUES ('{$board}')";
	$res = pmysql_query($sql,get_db_conn());
	$sql = "UPDATE tblboardadmin SET 
	board			= '{$board}', 
	board_name		= '{$board_name}', 
	passwd			= '{$passwd}', 
	thread_no		= '{$thread_no}', 
	board_skin		= '{$board_skin}', 
	board_width		= '{$board_width}', 
	list_num		= '{$list_num}', 
	page_num		= '{$page_num}', 
	writer_gbn		= '{$writer_gbn}', 
	max_filesize	= '{$max_filesize}', 
	img_maxwidth	= '{$img_maxwidth}', 
	img_align		= '{$img_align}', 
	date			= '{$date}', 
	use_lock		= '{$use_lock}', 
	use_reply		= '{$use_reply}', 
	use_comment		= '{$use_comment}', 
	use_imgresize	= '{$use_imgresize}', 
	group_code		= '{$group_code}', 
	grant_write		= '{$grant_write}', 
	grant_view		= '{$grant_view}', 
	grant_reply		= '{$grant_reply}', 
	grant_comment	= '{$grant_reply}' WHERE board='{$board}'";
	$insert= $res && pmysql_query($sql,get_db_conn());
	if (pmysql_errno()!=0) $onload="<script>window.onload=function(){alert(\"게시판 코드가 중복되어 오류가 발생하였습니다.\");}</script>";
	else {
		if($btype=="L") {
			if($prqna_gbn=="Y") {
				$etcfield=setEtcfield($etcfield,"PRQNA",$board);
				$_shopdata->etcfield=$etcfield;
			}
		}
		$onload="<script>window.onload=function(){alert(\"게시판 생성이 완료되었습니다.\");}</script>";
		include($Dir.BoardDir."file.inc.php");
		ProcessBoardDir($board,"create");
	}
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
var skin_cnt = 0;

<?php if($btype=="L"){?>
function check_qnaboard(gbn) {
<?php if(ord($prqnaboard)){?>
	if(confirm("현재 \"<?=$prqnaboardname?>\" 게시판을 상품QNA로 사용중입니다.\n\n현재 게시판을 상품QNA로 설정하시겠습니까?")) {
		document.form1.prqna_gbn[0].checked=true;
	} else {
		document.form1.prqna_gbn[1].checked=true;
	}
<?php }?>
}
<?php }?>

function CheckForm(form) {
	if(skin_cnt>1) {
		try {
			selskin=false;
			for(i=0;i<form.board_skin.length;i++) {
				if(form.board_skin[i].checked) {
					selskin=true;
					break;
				}
			}
			if(selskin==false) {
				alert("게시판 디자인을 선택하세요.");
				return;
			}
		} catch (e) {}
	}
	if(form.board.value.length==0) {
		alert("게시판 코드를 입력하세요.");
		form.board.focus();
		return;
	}
	if(form.board_name.value.length==0) {
		alert("게시판 제목을 입력하세요.");
		form.board_name.focus();
		return;
	}
	if(form.board_width.value.length==0) {
		alert("게시판 넓이를 입력하세요.");
		form.board_width.focus();
		return;
	}
	if(!IsNumeric(form.board_width.value)) {
		alert("게시판 넓이는 숫자만 입력 가능합니다.");
		form.board_width.focus();
		return;
	}
	if(form.list_num.value.length==0) {
		alert("게시글 목록수를 입력하세요.");
		form.list_num.focus();
		return;
	}
	if(!IsNumeric(form.list_num.value)) {
		alert("게시판 목록수는 숫자만 입력 가능합니다.");
		form.list_num.focus();
		return;
	}
	if(form.page_num.value==0) {
		alert("페이지 목록수를 입력하세요.");
		form.page_num.focus();
		return;
	}
	if(!IsNumeric(form.page_num.value)) {
		alert("페이지 목록수는 숫자만 입력 가능합니다.");
		form.page_num.focus();
		return;
	}
	if(form.passwd.value.length==0) {
		alert("게시판 관리 비밀번호를 입력하세요.");
		form.passwd.focus();
		return;
	}
	form.mode.value="insert";
	form.submit();
}

function ChoiceType(type) {
	document.form1.btype.value=type;
	document.form1.submit();
}

function ChangeDesign(tmp) {
	tmp=tmp + skin_cnt;
	document.form1["board_skin"][tmp].checked=true;
}

function big_view(bigimg) {
	window.open("community_register_bigpop.php?bigimg="+bigimg,"bigpop","left=50, height=100,width=100");
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>게시판 신규 생성</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">

	<tr>
		<td>
		<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<input type=hidden name=mode>
		<input type=hidden name=btype value="<?=$btype?>">
		<tr>
			<td valign="top">
			<?php include("menu_community.php"); ?>
			</td>

			<td></td>

			<td valign="top">

			<?php if(ord($btype)==0){?>

			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">게시판 신규 생성</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>커뮤니티 성격에 맞는 다양한 게시판을 생성할 수 있습니다.</span></div>
					<!-- 소제목 -->
					<div class="title_depth3_sub">게시판 타입 설정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=220></col>
				<col width=></col>
				<TR>
					<th class="table_cell"><A href="javascript:big_view('community_btypeimg_L1.gif');"><IMG src="images/community_btypeimg_L.gif" border=0></A></th>
					<TD align=center>
						<div class="table_none">
						<table cellpadding="0" cellspacing="0" width="95%">
						<col width=></col>
						<col width=117></col>
						<tr>
							<td><b>일반형 게시판</b><br>가장 많이 사용되는 일반적인 리스트 형태의 게시판입니다.<br></td>
							<td align=right rowspan="2"><a href="javascript:ChoiceType('L');"><img src="images/botteon_save.gif" border="0"></a></td>
						</tr>
						</table>
						</div>
					</TD>
				</TR>
				<TR>
					<th class="table_cell"><A href="javascript:big_view('community_btypeimg_W1.gif');"><IMG src="images/community_btypeimg_W.gif" border=0></A></th>
					<TD align=center>
						<div class="table_none">
						<table cellpadding="0" cellspacing="0" width="95%">
						<col width=></col>
						<col width=117></col>
						<tr>
							<td><b>웹진형 게시판</b><br>컨텐츠 형태의 게시판으로서 정보성 게시판 성격에 어울리는 게시판입니다.</td>
							<td align=right rowspan="2"><a href="javascript:ChoiceType('W');"><img src="images/botteon_save.gif" border="0"></a></td>
						</tr>
						</table>
						</div>
					</TD>
				</TR>
				<TR>
					<th class="table_cell"><A href="javascript:big_view('community_btypeimg_I1.gif');"><IMG src="images/community_btypeimg_I.gif" border=0></A></th>
					<TD align=center>
						<div class="table_none">
						<table cellpadding="0" cellspacing="0" width="95%">
						<col width=></col>
						<col width=117></col>
						<tr>
							<td><b>앨범형 게시판</b><br>앨범을 보듯 이미지를 앨범형태로 나열되는 형태의 게시판입니다.</td>
							<td align=right rowspan="2"><a href="javascript:ChoiceType('I');"><img src="images/botteon_save.gif" border="0"></a></td>
						</tr>
						</table>
						</div>
					</TD>
				</TR>
				<TR>
					<th class="table_cell"><A href="javascript:big_view('community_btypeimg_B1.gif');"><IMG src="images/community_btypeimg_B.gif" border=0></A></th>
					<TD align=center>
						<div class="table_none">
						<table cellpadding="0" cellspacing="0" width="95%">
						<col width=></col>
						<col width=117></col>
						<tr>
							<td><b>블로그형 게시판</b><br>요즘 많이 사용되고 있는 블로그 형태의 게시판입니다.</td>
							<td align=right rowspan="2"><a href="javascript:ChoiceType('B');"><img src="images/botteon_save.gif" border="0"></a></td>
						</tr>
						</table>
						</div>
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=30></td></tr>

			<?php }else{?>
<?php
			$sql = "SELECT * FROM tblboardskin WHERE board_skin LIKE '{$btype}%' ORDER BY board_skin ASC";
			$result=pmysql_query($sql,get_db_conn());
			$rows=pmysql_num_rows($result);
			if(!$rows) {
				alert_go("해당 게시판 형태의 스킨 등록이 안되어 게시판 추가가 불가합니다.");
			}
?>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">게시판 신규 생성</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>커뮤니티 성격에 맞는 다양한 게시판을 생성하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
<?php
			if($rows==1) {
				$row=pmysql_fetch_object($result);
				pmysql_free_result($result);
				echo "<input type=hidden name=\"board_skin\" value=\"{$row->board_skin}\">\n";
			} elseif($rows>1) {
				echo "<script>skin_cnt={$rows};</script>\n";
?>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><div class="point_title"><span>등록된 게시판 스킨 선택하기</span></div></td>
				</tr>
				<tr>
					<td class="bd_editer" width="100%" >
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%" style="padding:10pt;">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
								<TABLE cellSpacing=0 cellPadding="4" width="100%" border=0 align="center">
								<col width=31></col>
								<col width=></col>
								<col width=31></col>
								<TR>
									<TD height="160" align=right valign="middle"><img src="images/btn_back.gif" border="0" onMouseover='moveright()' onMouseout='clearTimeout(righttime)' style="cursor:hand;"></TD>
									<TD height="160" valign="top" align="center">
									<table width="100%" cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td id="temp" style="visibility:hidden;position:absolute;top:0;left:0">
<?php
										echo "<script>";
										$jj=0;
										$menucontents = "";
										$menucontents .= "<table border=0 cellpadding=0 cellspacing=0><tr>";
										$i=0;
										while($row=pmysql_fetch_object($result)) {
											echo "thisSel = 'dotted #FFFFFF';";
											$menucontents .= "<td width=160 align=center><img src='images/sample/board_{$row->board_skin}.gif' border=0 style='border-width:1pt; border-color:#FFFFFF; border-style:solid;' hspace='5' onMouseOver='changeMouseOver(this);' onMouseOut='changeMouseOut(this,thisSel);' style='cursor:hand;' onclick='ChangeDesign({$i});'>";
											$menucontents .= "<br><input type=radio name='board_skin' value='{$row->board_skin}'";
											if($data->board_skin==$row->board_skin) $menucontents .= " checked";
											$menucontents .= "></td>";
											$jj++;
											$i++;
										}
										pmysql_free_result($result);
										$menucontents .= "</tr></table>";
										echo "</script>";
?>  

										<script language="JavaScript1.2">
										<!--
										function changeMouseOver(img) {
											 img.style.border='1 dotted #999999';
										}
										function changeMouseOut(img,dot) {
											 img.style.border="1 "+dot;
										}

										var menuwidth=650
										var menuheight=220
										var scrollspeed=10
										var menucontents="<nobr><?=$menucontents?></nobr>";
										
										var iedom=document.all||document.getElementById
										if (iedom)
											document.write(menucontents)
										var actualwidth=''
										var cross_scroll, ns_scroll
										var loadedyes=0
										function fillup(){
											if (iedom){
												cross_scroll=document.getElementById? document.getElementById("test2") : document.all.test2
												cross_scroll.innerHTML=menucontents
												actualwidth=document.all? cross_scroll.offsetWidth : document.getElementById("temp").offsetWidth
											}
											else if (document.layers){
												ns_scroll=document.ns_scrollmenu.document.ns_scrollmenu2
												ns_scroll.document.write(menucontents)
												ns_scroll.document.close()
												actualwidth=ns_scroll.document.width
											}
											loadedyes=1
										}
										window.onload=fillup
										
										function moveleft(){
											if (loadedyes){
												if (iedom&&parseInt(cross_scroll.style.left)>(menuwidth-actualwidth)){
													cross_scroll.style.left=parseInt(cross_scroll.style.left)-scrollspeed
												}
												else if (document.layers&&ns_scroll.left>(menuwidth-actualwidth))
													ns_scroll.left-=scrollspeed
											}
											lefttime=setTimeout("moveleft()",50)
										}
										
										function moveright(){
											if (loadedyes){
												if (iedom&&parseInt(cross_scroll.style.left)<0)
													cross_scroll.style.left=parseInt(cross_scroll.style.left)+scrollspeed
												else if (document.layers&&ns_scroll.left<0)
													ns_scroll.left+=scrollspeed
											}
											righttime=setTimeout("moveright()",50)
										}
										
										if (iedom||document.layers){
											with (document){
												write('<td valign=top>')
												if (iedom){
													write('<div style="position:relative;width:'+menuwidth+';">');
													write('<div style="position:absolute;width:'+menuwidth+';height:'+menuheight+';overflow:hidden;">');
													write('<div id="test2" style="position:absolute;left:0">');
													write('</div></div></div>');
												}
												else if (document.layers){
													write('<ilayer width='+menuwidth+' height='+menuheight+' name="ns_scrollmenu">')
													write('<layer name="ns_scrollmenu2" left=0 top=0></layer></ilayer>')
												}
												write('</td>')
											}
										}
										//-->
										</script>
										</td>
									</tr>
									</table>
									</TD>
									<TD height="160"><img src="images/btn_next.gif" border="0" onMouseover='moveleft()' onMouseout='clearTimeout(lefttime)' style="cursor:hand;"></TD>
								</TR>
								</TABLE>
								</td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
<?php
			}
?>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">게시판 기본기능 설정 <span>게시판의 기본기능을 설정 할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<TR>
					<th><span>게시판 코드</span></th>
					<TD><INPUT maxLength=20 size=20 name=board class="input_selected"> <span class="font_orange">* 게시판 유일 코드를 입력하세요. (예:qna2)</span>
					</TD>
				</TR>
				<TR>
					<th><span>게시판 제목</span></th>
					<TD><INPUT maxLength=200 size=60 name=board_name class="input_selected"> <span class="font_orange">* HTML로 작성 가능</span>
					</TD>
				</TR>
				<TR>
					<th><span>게시판 넓이</span></th>
					<TD><INPUT onkeyup="return strnumkeyup(this);" style="TEXT-ALIGN: right" maxLength="10" size="10" value=690 name=board_width class="input"> <span class="font_blue">(권장 : 690픽셀, 게시판 가로크기 100이하이면 %로 설정됩니다.)</span></TD>
				</TR>
				<TR>
					<th><span>게시글 목록수</span></th>
					<TD><INPUT onkeyup="return strnumkeyup(this);" style="TEXT-ALIGN: right" maxLength="10" size="10" value=20 name=list_num class="input"> <span class="font_blue">(권장 : 20)</span></TD>
				</TR>
				<TR>
					<th><span>페이지 목록수</span></th>
					<TD><INPUT onkeyup="return strnumkeyup(this);" style="TEXT-ALIGN: right" maxLength="10" size="10" value=10 name=page_num class="input"> <span class="font_blue">(권장 : 10)</span></TD>
				</TR>
				<tr>
					<th><span>게시판 첨부파일</span></th>
					<TD>
					
					<SELECT name=max_filesize class="select" onchange="if(this.value>10) alert('호스팅사에서 제공된 업로드 가능 용량을 확인 후 설정하시기 바랍니다.');">
					<OPTION value=1>100KB</OPTION>
					<OPTION value=2 selected>200KB</OPTION>
					<OPTION value=3>300KB</OPTION>
					<OPTION value=4>400KB</OPTION>
					<OPTION value=5>500KB</OPTION>
					<OPTION value=6>600KB</OPTION>
					<OPTION value=7>700KB</OPTION>
					<OPTION value=8>800KB</OPTION>
					<OPTION value=9>900KB</OPTION>
					<OPTION value=10>&nbsp;&nbsp;&nbsp;1MB</OPTION>
					<OPTION value=20>&nbsp;&nbsp;&nbsp;2MB</OPTION>
					</SELECT>
					 (권장 : 200KB)
					</TD>
				</tr>
				<tr>
					<th><span>게시판 이미지 설정</span></th>
					<TD>이미지 최대 사이즈 : <INPUT onkeyup="return strnumkeyup(this);" size="10" value=650 name=img_maxwidth class="input" style="text-align:right;">픽셀 / 이미지 정렬 : <INPUT id=idx_img_align0 type=radio CHECKED value=center name=img_align><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_img_align0>가운데 정렬</LABEL> <INPUT id=idx_img_align1 type=radio value=left name=img_align><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_img_align1>왼쪽 정렬</LABEL><br><span class="font_blue"> * 이미지 최대 사이즈는 게시판의 넓이보다 작은 사이즈로 설정하시기 바랍니다.</span></TD>
				</tr>
				<tr>
					<th><span>게시판 비밀번호</span></th>
					<TD><INPUT name=passwd class="input"> <span class="font_blue"> * 비밀번호가 유출되지 않도록 주의하시기 바랍니다.</span></TD>
				</tr>
				<tr>
					<th><span>게시판 접근권한</span></th>
					<TD>
					<div class="table_none">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						 게시물 쓰기 : 
						<SELECT name=grant_write class="select"  style="width:130px">
						<OPTION value=N selected>회원/비회원</OPTION>
						<OPTION value=Y>회원전용</OPTION>
						<OPTION value=A>관리자전용</OPTION>
						</SELECT>
						
						</td>
					</tr>
					<tr>
						<td>
						 게시물 보기 : 
						<SELECT  style="width:130px" name=grant_view class="select">
						<OPTION value=N selected>회원/비회원</OPTION>
						<OPTION value=U>비회원목록조회</OPTION>
						<OPTION value=Y>비회원조회불가</OPTION>
						</SELECT>
						<br>
						</td>
					</tr>
					<tr>
						<td>
						 특정등급만 읽고 쓰기 : 
						<SELECT style="WIDTH: 307px" name=group_code class="select">
						<option value="">해당 등급을 선택하세요.</option>
<?php
						$sql = "SELECT group_code,group_name FROM tblmembergroup ";
						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)) {
							echo "<option value=\"{$row->group_code}\">{$row->group_name}</option>";
						}
						pmysql_free_result($result);
?>
						</SELECT>
						<br>
						</td>
					</tr>
					<tr>
						<td style="padding-top:8pt;" class="font_blue">
						 * [게시물 보기]의 <B>비회원목록조회</B> 선택시 비회원은 목록조회는 가능하나 내용보기가<span style="letter-spacing:-0.5pt;"> 안됩니다.(로그인 후가능)</span>
						<br>
						 * [특정등급만 읽고 쓰기]에서 회원등급이 선택된 경우 해당 회원등급의 회원만 쓰기 및 보기가 가능합니다.<br>&nbsp;&nbsp;(다만 [게시물 쓰기]의 권한이 관리자전용으로 선택시 해당 회원등급은 조회 및 내용보기만 가능합니다.)
						</td>
					</tr>
					</table>
					</div>
					</TD>
				</tr>
				<?php if($btype=="L"){?>
				<tr>
					<th><span>게시판 답변기능</span></th>
					<TD>
					<INPUT id=idx_use_reply0 onclick=this.form.grant_reply.disabled=false; type=radio CHECKED value=Y name=use_reply><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_use_reply0>사용함</LABEL>(답변 쓰기 :
					<SELECT name=grant_reply class="select">
					<OPTION value=N selected>회원/비회원</OPTION>
					<OPTION value=Y>회원전용</OPTION>
					<OPTION value=A>관리자전용</OPTION>
					</SELECT>
					)
					<INPUT id=idx_use_reply1 onclick=this.form.grant_reply.disabled=true; type=radio value=N name=use_reply><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_use_reply1>사용안함</LABEL><br>
					</TD>
				</tr>
				<?php }else{?>
				<input type=hidden name=use_replay value="N">
				<input type=hidden name=grant_reply value="N">
				<?php }?>
				<tr>
					<th><span>게시판 댓글기능</span></th>
					<TD>
					<INPUT id=idx_use_comment0 onclick=this.form.grant_comment.disabled=false; type=radio CHECKED value=Y name=use_comment><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_use_comment0>사용함</LABEL>(댓글 쓰기 :
					<SELECT name=grant_comment class="select">
					<OPTION value=N selected>회원/비회원</OPTION>
					<OPTION value=Y>회원전용</OPTION>
					<OPTION value=A>관리자전용</OPTION>
					</SELECT>
					)
					<INPUT id=idx_use_comment1 onclick=this.form.grant_comment.disabled=true; type=radio value=N name=use_comment><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_use_comment1>사용안함</LABEL>
					<br>
					
					</TD>
				</TR>
				<tr>
					<th><span>비밀글 기능</span></th>
					<TD><INPUT id=idx_use_lock0 type=radio value=A name=use_lock><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_use_lock0>고객 의무사용</LABEL>  &nbsp;&nbsp;<INPUT id=idx_use_lock1 type=radio value=Y name=use_lock><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_use_lock1>고객 선택사용</LABEL>  &nbsp;<INPUT id=idx_use_lock2 type=radio CHECKED value=N name=use_lock><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_use_lock2>사용하지 않음</LABEL><br>&nbsp;<span class="font_blue"> * 게시물 작성시 작성자 본인과 관리자만 볼 수 있도록 하는 기능입니다.</span></TD>
				</tr>
				<tr>
					<th><span>작성자 표기</span></th>
					<TD><INPUT id=idx_writer_gbn0 type=radio CHECKED value=0 name=writer_gbn><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_writer_gbn0>회원 이름</LABEL>  &nbsp;&nbsp;&nbsp;<INPUT id=idx_writer_gbn1 type=radio value=1 name=writer_gbn><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_writer_gbn1>회원 아이디</LABEL><br>&nbsp;<span class="font_blue"> * 게시판에 출력되는 회원의 작성자 표기 방식을 선택할 수 있습니다.</span></TD>
				</tr>
				<?php if($btype=="L"){?>
				<tr>
					<th><span>상품 Q&A 기능</span></th>
					<TD>
					<INPUT id=idx_prqna_gbn0 type=radio value="Y" name=prqna_gbn <?php if($prqna_gbn=="Y")echo"checked";?> onclick="check_qnaboard('Y')"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_prqna_gbn0>현재 게시판을 상품QNA로 사용함</LABEL>
					
					&nbsp;&nbsp;&nbsp;<INPUT id=idx_prqna_gbn1 type=radio name=prqna_gbn value="" <?php if($prqna_gbn!="Y")echo"checked";?> onclick="check_qnaboard('')"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_prqna_gbn1>사용안함</LABEL>
					<br>&nbsp;<span class="font_blue"> * 상품상세페이지의 상품Q&A 게시판으로 사용될 게시판을 선택할 수 있습니다.</span></TD>
				</tr>
				<?php }?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm(document.form1);"><img src="images/botteon_newboard.gif"  border="0"></a></td>
			</tr>

			<?php }?>

			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>게시판 타입 설정</span></dt>
							<dd>- 한번 생성된 게시판은 다른 타입으로 변경이 불가 합니다.<br>
								- 등록된 게시판의 설정 변경은 <a href="javascript:parent.topframe.GoMenu(8,'community_list.php');"><span class="font_blue">커뮤니티 > 커뮤니티 관리 > 게시판 리스트 관리</a></span> 메뉴를 사용하시기 바랍니다.<br>

						</dd>	
						</dl>
						<dl>
							<dt><span>게시판 기본 기능설정</span></dt>
						<dd>
							- 해당 게시판 성격에 맞춰 기본기능을 설정하시기 바랍니다.<br>
							- 등록된 게시판의 설정 변경은 <a href="javascript:parent.topframe.GoMenu(8,'community_list.php');"><span class="font_blue">커뮤니티 > 커뮤니티 관리 > 게시판 리스트 관리</a></span> 메뉴를 사용하시기 바랍니다.
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
		</form>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
