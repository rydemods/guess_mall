<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include_once($Dir."lib/file.class.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-1";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$up_file=new FILE("../data/shopimages/board/notice/");

$type=$_POST["type"];
$mode=$_POST["mode"];
$date=$_POST["date"];
$up_subject=$_POST["up_subject"];
$up_content=$_POST["up_content"];
$up_newdate=$_POST["up_newdate"];
$vdate = date("YmdHis");

if($_POST["date"] || $up_subject){

	$up_filename=$up_file->upFiles();

	$r_file=$up_filename['up_filename'][0]['r_file'];
	$v_file=$up_filename['up_filename'][0]['v_file'];

}

if(ord($up_subject) && $type=="insert") {

	$sql = "INSERT INTO tblnotice(date,access,subject,content,r_file,v_file) VALUES (
	'{$vdate}', 
	0, 
	'{$up_subject}', 
	'{$up_content}',
	'".$r_file."',
	'".$v_file."'
	)";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('공지사항 등록이 완료되었습니다.'); }</script>\n";
} else if (ord($date) && $type=="modify") {
	if ($mode=="result") {

		if($v_file || $_POST['file_del']=='1' ){
			list($del_filename)=pmysql_fetch("select v_file from tblnotice WHERE date = '{$date}'");
			$up_file->removeFile($del_filename);

			$add_qry=" r_file='".$r_file."', v_file ='".$v_file."', ";
		}

		$sql = "UPDATE tblnotice SET ".$add_qry." subject = '{$up_subject}', content = '{$up_content}' ";
		if($up_newdate=="Y") $sql.= ", date = '{$vdate}' ";
		$sql.= "WHERE date = '{$date}' ";

		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('공지사항 수정이 완료되었습니다.'); }</script>\n";
		$type='';
		$mode='';
		$date='';
	} else {
		$sql = "SELECT * FROM tblnotice WHERE date = '{$date}' ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
		if ($row) {
			$subject = str_replace("\"","&quot;",$row->subject);
			$content = str_replace("\"","&quot;",$row->content);
			$r_filename = $row->r_file;
		} else {
			$onload="<script>window.onload=function(){ alert('수정하려는 공지사항이 존재하지 않습니다.'); }<script>";
			$type='';
			$date='';
		}
	}
} else if (ord($date) && $type=="delete") {

	list($del_filename)=pmysql_fetch("select v_file from tblnotice WHERE date = '{$date}'");
	$up_file->removeFile($del_filename);

	$sql = "DELETE FROM tblnotice WHERE date = '{$date}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('공지사항 삭제가 완료되었습니다.'); }</script>\n";
	$type='';
	$date='';
}



if (ord($type)==0) $type="insert";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(document.form1.up_subject.value.length==0) {
		document.form1.up_subject.focus();
		alert("공지사항 제목을 입력하세요");
		return;
	}

	var sHTML = oEditors.getById["ir1"].getIR();
	form1.up_content.value=sHTML;

	if(document.form1.up_content.value.length==0) {
		document.form1.up_content.focus();
		alert("공지사항 내용을 입력하세요");
		return;
	}
	if(type=="modify") {
		if(!confirm("해당 공지사항을 수정하시겠습니까?")) {
			return;
		}
		document.form1.mode.value="result";
	}
	document.form1.type.value=type;
	document.form1.submit();
}
function NoticeSend(type,date) {
	if(type=="delete") {
		if(!confirm("해당 공지사항을 삭제하시겠습니까?")) return;
	}
	document.form1.type.value=type;
	document.form1.date.value=date;
	document.form1.submit();
}
function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 마케팅지원 &gt;<span>공지사항 관리</span></p></div></div>

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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype='multipart/form-data'>
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=date value="<?=$date?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">공지사항 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>공지사항 관리메뉴 게시물 등록/수정/삭제 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">등록된 공지사항 리스트</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=160></col>
				<col width=></col>
				<col width=50></col>
				<col width=60></col>
				<col width=60></col>
				<TR align=center>
					<th>등록일자</th>
					<th>제목</th>
					<th>조회</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan=5;
				$sql = "SELECT COUNT(*) as t_count FROM tblnotice ";
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;	
				$gotopage = $paging->gotopage;				

				$sql = "SELECT * FROM tblnotice ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." ".substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
					echo "<TR>\n";
					echo "	<TD>{$str_date}</TD>\n";
					echo "	<TD><div class=\"ta_l\">{$row->subject}</div></TD>\n";
					echo "	<TD>{$row->access}</td>\n";
					echo "	<TD><a href=\"javascript:NoticeSend('modify','{$row->date}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:NoticeSend('delete','{$row->date}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
				pmysql_free_result($result);

				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan={$colspan} align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
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
					<div class="title_depth3_sub">공지사항 등록/수정</div>
				</td>
			</tr>
			<tr>
				<td>
				
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<TR>
					<th><span>공지사항 글제목</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_subject class="input" value="<?=$subject?>"></TD>
				</TR>
				<TR>
					<th><span>공지사항 내용</span></th>
					<TD><TEXTAREA id="ir1" style="WIDTH: 100%; HEIGHT: 200px" name=up_content ><?php echo $content ?></TEXTAREA></TD>
				</TR>
				<TR>
					<th><span>첨부파일</span></th>
					<TD>
					<!--
						<input type="text" id="fileName1" class="file_input_textbox w400" readonly="readonly"> 
						<div class="file_input_div">
						<input type="button" value="찾아보기" class="file_input_button" /> 
						<input type=file name="up_filename[]" onChange="document.getElementById('fileName1').value = this.value;" style="WIDTH: 400px" class="file_input_hidden" ><br />
						</div>
					-->
						<input type=file name="up_filename[]" size=50><br>
						<?php if($type=="modify" && $r_filename){?>
						<?=$r_filename?> <input type="checkbox" name="file_del" value='1'>삭제
						<?}?>
					</TD>
				</TR>
				<?php if($type=="modify"){?>
				<TR>
					<th><span>등록일 변경여부</span>
					<TD><INPUT id=idx_newdate type=checkbox CHECKED value=Y name=up_newdate>해당 공지사항 등록일을 현재시간으로 변경합니다. (최근 공지로 변경)</LABEL></TD>
				</TR>
				<?php }?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>공지사항 관리</span></dt>
							<dd>
								- 공지사항 신규등록 또는 수정시 "등록일 변경여부"를 선택한 글은 공지사항 출력시 최상단에 위치합니다.<br>
								- 공지사항 등록 및 수정은 관리자페이지에서만 가능하며 사용자페이지에서는 불가능합니다.
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</form>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
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
