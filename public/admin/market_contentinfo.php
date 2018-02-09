<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-1";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/etc/";

$mode=$_POST["mode"];
$type=$_POST["type"];
$date=$_POST["date"];
$old_image=$_POST["old_image"];
$up_subject=$_POST["up_subject"];
$up_content=$_POST["up_content"];
$up_image=$_FILES["up_image"];
$up_image_align=$_POST["up_image_align"];
$up_newdate=$_POST["up_newdate"];
$vdate = date("YmdHis");

if(ord($up_subject) && $type=="insert") {
	$ext = strtolower(pathinfo($up_image['name'],PATHINFO_EXTENSION));
	if (ord($up_image["name"]) && in_array($ext,array('gif','jpg'))) {
		if ($up_image["size"]<=153600) {
			$up_image["name"] = "cinfo".$up_image["name"];
			move_uploaded_file($up_image['tmp_name'],$imagepath.$up_image["name"]);
			chmod($imagepath.$up_image["name"],0606);
		} else {
			$up_image["name"] = "";
		}
	}  else {
		$up_image["name"] = "";
	}
	$sql = "INSERT INTO tblcontentinfo(
	date		,
	subject		,
	image_name	,
	image_align	,
	access		,
	content) VALUES (
	'{$vdate}', 
	'{$up_subject}', 
	'{$up_image[name]}', 
	'{$up_image_align}', 
	0, 
	'{$up_content}')";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('정보(information) 등록이 완료되었습니다.'); }</script>\n";
} elseif (ord($date) && $type=="modify") {
	if ($mode=="result") {
		$ext = strtolower(pathinfo($up_image['name'],PATHINFO_EXTENSION));
		if (ord($up_image["name"]) && in_array($ext,array('gif','jpg'))) {
			if ($up_image["size"]<=153600) {
				$up_image["name"] = "cinfo".$up_image["name"];
				if(ord($old_image) && file_exists($imagepath.$old_image)) unlink($imagepath.$old_image);
				move_uploaded_file($up_image['tmp_name'],$imagepath.$up_image["name"]);
				chmod($imagepath.$up_image["name"],0606);
			} else {
				$up_image["name"] = $old_image;
			}
		} else {
			$up_image["name"] = $old_image;
		}
		$sql = "UPDATE tblcontentinfo SET 
		image_name	= '{$up_image[name]}', 
		image_align	= '{$up_image_align}', 
		subject		= '{$up_subject}', 
		content		= '{$up_content}' ";
		if($up_newdate=="Y") $sql.= ", date = '{$vdate}' ";
		$sql.= "WHERE date = '{$date}' ";

		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('정보(information) 수정이 완료되었습니다.'); }</script>\n";
		$mode='';
	} //else {

		if($up_newdate=="Y"){
			$qry_date=$vdate;
		}else{
			
			$qry_date=$date;
		}
		$sql = "SELECT * FROM tblcontentinfo WHERE date = '{$qry_date}' ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);

		pmysql_free_result($result);
		if ($row) {
			$subject = str_replace("\"","&quot;",$row->subject);
			$content = str_replace("\"","&quot;",$row->content);
			$image_name = $row->image_name;
			$image_align = $row->image_align;
		} else {
			$onload="<script>window.onload=function(){ alert('정보(information)가 존재하지 않습니다.'); }<script>";
			$type='';
			$date='';
		}
	//}
} elseif (ord($date) && $type=="delete") {
	$sql = "SELECT * FROM tblcontentinfo WHERE date = '{$date}' ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(ord($row->image_name)) {
		if(file_exists($imagepath.$row->image_name)) unlink($imagepath.$row->image_name);
	}
	$sql = "DELETE FROM tblcontentinfo WHERE date = '{$date}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('정보(information) 삭제가 완료되었습니다.'); }</script>\n";
	$type='';
	$date='';
} elseif (ord($date) && $type=="imgdel") {
	$sql = "SELECT * FROM tblcontentinfo WHERE date = '{$date}' ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(ord($row->image_name)) {
		if(file_exists($imagepath.$row->image_name)) unlink($imagepath.$row->image_name);
		pmysql_query("UPDATE tblcontentinfo SET image_name=NULL,image_align=NULL WHERE date='{$date}'",get_db_conn());
	}
	$onload="<script>window.onload=function(){ alert('이미지 삭제가 완료되었습니다.'); }</script>\n";
	$type='';
	$date='';
}

if (ord($type)==0) $type="insert";
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type,date) {
	if(document.form1.up_subject.value.length==0) {
		document.form1.up_subject.focus();
		alert("정보(information) 제목을 입력하세요");
		return;
	}
	if(document.form1.up_content.value.length==0) {
		document.form1.up_content.focus();
		alert("정보(information) 내용을 입력하세요");
		return;
	}
	if(type=="modify") {
		if(!confirm("해당 정보(information)를 수정하시겠습니까?")) {
			return;
		}
		document.form1.mode.value="result";
	}
	document.form1.type.value=type;
	document.form1.date.value=date;
	document.form1.submit();
}
function ContentSend(type,date) {
	if(type=="delete") {
		if(!confirm("해당 정보(information)를 삭제하시겠습니까?")) return;
	}
	if(type=="imgdel") {
		if(!confirm("해당 정보(information)의 이미지를 삭제하시겠습니까?")) return;
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 마케팅지원 &gt;<span>정보(information) 관리</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=date value="<?=$date?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">정보(information)관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>정보(information)를 등록/수정삭제 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->	
					<div class="title_depth3_sub">등록된 정보(information) 리스트</div>
				</td>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=160></col>
				<col width=></col>
				<col width=50></col>
				<col width=60></col>
				<col width=60></col>
				<TR>
					<th>등록일자</th>
					<th>제목</th>
					<th>조회</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan=5;
				$sql = "SELECT COUNT(*) as t_count FROM tblcontentinfo ";
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;	
				$gotopage = $paging->gotopage;
				
				$sql = "SELECT * FROM tblcontentinfo ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." ".substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
					echo "<TR align=center>\n";
					echo "	<TD>{$str_date}</TD>\n";
					echo "	<TD><div class=\"ta_l\">{$row->subject}</div></TD>\n";
					echo "	<TD>{$row->access}</TD>\n";
					echo "	<TD><a href=\"javascript:ContentSend('modify','{$row->date}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:ContentSend('delete','{$row->date}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
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
					<div class="title_depth3_sub">정보(information) 등록/수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>글제목</span></th>
					<TD><INPUT style="WIDTH: 100%" name=up_subject class="input" value="<?=$subject?>"></TD>
				</TR>
				<TR>
					<th><span>내용</span></th>
					<TD><TEXTAREA style="WIDTH: 100%; HEIGHT: 200px" name=up_content class="textarea"><?php echo $content ?></TEXTAREA></TD>
				</TR>
				<TR>
					<th><span>이미지</span></th>
					<TD>
					<SELECT name=up_image_align class="select">
					<option value="left" <?php if ($image_align=="left") echo "selected" ?>>왼쪽정렬
					<option value="right" <?php if ($image_align=="right") echo "selected" ?>>오른쪽정렬
					<option value="top" <?php if ($image_align=="top") echo "selected" ?>>위로정렬
					<option value="bottom" <?php if ($image_align=="bottom") echo "selected" ?>>아래로정렬
					</SELECT>
                                      
					<br>
					<input type=file name=up_image size="50">
					<!--
                    <input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly">
					<div class="file_input_div">
					<input type="button" value="찾아보기" class="file_input_button" />
					<input type=file name=up_image value="" style="width:100%;" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ></div>                   
					-->
                    
					<?php if(ord($image_name)){?>
					<a href="javascript:ContentSend('imgdel','<?=$date?>');"><img src="images/myicon_upload_del.gif" border="0"></a><input type=hidden name=old_image value="<?=$image_name?>">
					<?php }?>
				<?php
				if (ord($image_name)) {
					if (file_exists($imagepath.$image_name)) {
						$width = getimagesize($imagepath.$image_name);
						if ($width[0]>=450) $width=" width=450 ";
					}
				?>
					<br><img src="<?=$imagepath.$image_name?>" <?=$width?>>
				<?php }?>
				<br><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 이미지는 150KB 이하의 GIF, JPG만 가능</span>
					</TD>
				</TR>

				<?php if($type=="modify"){?>
				<TR>
					<th><span>등록일 변경여부</span></th>
					<TD><INPUT id=idx_newdate type=checkbox CHECKED value=Y name=up_newdate>해당 공지사항 등록일을 현재시간으로 변경합니다. (최근 공지로 변경)</LABEL></TD>
				</TR>
				<?php }?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>','<?=$date?>');"><img src="images/botteon_save.gif" border="0"></a></td>
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
							<dt><span>정보(information)관리</span></dt>
							<dd>
								- 정보(information) 메뉴는 쇼핑몰 정보 또는 커뮤니티 기사를 제공하는 메뉴 입니다.<br>
								- 정보(information)는 메인화면 템플릿에서 메인 우측에 기본으로 출력되게 설정돼 있습니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_main.php');"><span class="font_blue">디자인관리 > 템플릿-메인 및 카테고리 > 메인화면 템플릿</span></a><br>
								- 정보(information) 신규등록 또는 수정시 "등록일 변경여부"를 선택한 글은 정보(information) 출력시 최상단에 위치합니다.
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
