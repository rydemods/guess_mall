<?php
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

$maxcnt=5;

$type=$_POST["type"];
$mode=$_POST["mode"];
$num=$_POST["num"];
$used=$_POST["used"];
$banner_type=$_POST["banner_type"];
$banner_target=$_POST["banner_target"];
$banner_url=$_POST["banner_url"];
$title=$_POST["title"];
$banner_html=$_POST["banner_html"];
if(ord($used)==0) $used="N";

$imagepath=$Dir.DataDir."shopimages/banner/";
$filename=date("YmdHis").".gif";

if($type=="insert") {
	$sql = "SELECT COUNT(*) as cnt FROM tblaffiliatebanner ";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($row->cnt<$maxcnt) {
		if($banner_type=="Y") {	//이미지 등록방식
			$banner_image=$_FILES["banner_image"];
			if ($banner_image["size"]>153600) {
				alert_go('배너 이미지 용량은 150KB를 넘을 수 없습니다.');
			}
			$ext = strtolower(pathinfo($banner_image['name'],PATHINFO_EXTENSION));
			if (ord($banner_image['name']) && $banner_image["size"]>0 && in_array($ext,array('gif','jpg'))) {
				$banner_image['name']=$filename;
				move_uploaded_file($banner_image['tmp_name'],$imagepath.$banner_image['name']);
				chmod($imagepath.$banner_image['name'],0664);
			}

			$content="Y={$banner_target}={$banner_url}=".$banner_image['name'];
		} else {				//html 편집방식
			$content="N=".$banner_html;
		}
		$sql = "INSERT INTO tblaffiliatebanner(used,reg_date,title,content) VALUES (
		'N', 
		'".date("YmdHis")."', 
		'{$title}', 
		'{$content}')"; 
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('배너 등록이 완료되었습니다.'); }</script>";
		$type=''; $used=''; $banner_type=''; $banner_target=''; $banner_url='';
		$banner_html=''; $title=''; $content='';
	} else {
		$onload="<script>window.onload=function(){ alert('배너 등록은 최대 {$maxcnt}개 까지 등록 가능합니다.'); }</script>";
	}
} else if (($type=="modify_result" || $type=="modify") && ord($num)) {
	$sql = "SELECT * FROM tblaffiliatebanner WHERE num = '{$num}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		pmysql_free_result($result);
		$tempcontent=explode("=",$row->content);
		$temptype=$tempcontent[0];

		if($type=="modify") {
			$used=$row->used;
			$title=$row->title;
			$tempcontent=explode("=",$row->content);
			$banner_type=$tempcontent[0];
			if($banner_type=="Y") {
				$banner_target=$tempcontent[1];
				$banner_url=$tempcontent[2];
				$banner_image=$tempcontent[3];
				$banner_html="";
			} else if($banner_type=="N") {
				$banner_html=$tempcontent[1];
				$banner_target="";
				$banner_url="";
				$banner_image="";
			}
		} else if($type=="modify_result") {
			if($temptype=="Y") {
				$old_image=$tempcontent[3];
			} else if($temptype=="N") {
				$old_image="";
			}
			if($banner_type=="Y") {	//이미지 등록방식
				$banner_image=$_FILES["banner_image"];
				if ($banner_image["size"]>153600) {
					alert_go('배너 이미지 용량은 150KB를 넘을 수 없습니다.');
				}
				$ext = strtolower(pathinfo($banner_image['name'],PATHINFO_EXTENSION));
				if (ord($banner_image['name']) && $banner_image["size"]>0 && in_array($ext,array('gif','jpg'))) {
					$banner_image['name']=$filename;
					move_uploaded_file($banner_image['tmp_name'],$imagepath.$banner_image['name']);
					chmod($imagepath.$banner_image['name'],0664);
				}
				if(ord($banner_image['name']) && $banner_image["size"]>0) {
					$content="Y={$banner_target}={$banner_url}=".$banner_image['name'];
					if(ord($old_image)) {
						if(file_exists($imagepath.$old_image)) {
							unlink($imagepath.$old_image);
						}
					}
				} else {
					$content="Y={$banner_target}={$banner_url}=".$old_image;
				}
			} else {				//html 편집방식
				$content="N=".$banner_html;
				if(ord($old_image)) {
					if(file_exists($imagepath.$old_image)) {
						unlink($imagepath.$old_image);
					}
				}
			}
			$sql = "UPDATE tblaffiliatebanner SET ";
			$sql.= "used		= '{$used}', ";
			$sql.= "title		= '{$title}', ";
			$sql.= "content		= '{$content}' ";
			$sql.= "WHERE num = '{$num}' ";
			
			pmysql_query($sql,get_db_conn());
			$onload="<script>window.onload=function(){ alert('배너 수정이 완료되었습니다.'); }</script>";
			$type=''; $used=''; $banner_type=''; $banner_target=''; $banner_url='';
			$banner_html=''; $num=''; $title=''; $content='';
		}
	} else {
		pmysql_free_result($result);
		$onload="<script>window.onload=function(){ alert('수정하려는 배너 정보가 존재하지 않습니다.'); }</script>";
	}
} else if ($type=="delete" && ord($num)) {
	$sql = "SELECT * FROM tblaffiliatebanner WHERE num = '{$num}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$tempcontent=explode("=",$row->content);
		$temptype=$tempcontent[0];
		if($temptype=="Y") {
			$old_image=$tempcontent[3];
		} else if($temptype=="N") {
			$old_image="";
		}
		if(ord($old_image)) {
			if(file_exists($imagepath.$old_image)) {
				unlink($imagepath.$old_image);
			}
		}

		$sql = "DELETE FROM tblaffiliatebanner WHERE num = '{$num}' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('해당 배너를 삭제하였습니다.'); }</script>";
		$type=''; $used=''; $banner_type=''; $banner_target=''; $banner_url='';
		$banner_html=''; $num=''; $title=''; $content='';
	}
	pmysql_free_result($result);
}

if(ord($type)==0) $type="insert";
$type_name="images/botteon_save.gif";
if($type=="modify" || $type=="modify_result") $type_name="images/btn_edit2.gif";

if($type=="insert") $used_disabled="disabled";
include("header.php"); 
?>
<script>try {parent.topframe.ChangeMenuImg(7);}catch(e){}</script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">

function CheckForm(type) {
	if(document.form1.title.value.length==0) {
		alert("배너 제목을 입력하세요.");
		document.form1.title.focus();
		return;
	}
	temptype="";
	for(i=0;i<document.form1.banner_type.length;i++) {
		if(document.form1.banner_type[i].checked) {
			temptype=document.form1.banner_type[i].value;
			break;
		}
	}
	if(temptype.length==0 || (temptype!="Y" && temptype!="N")) {
		alert("배너 등록 형태를 선택하세요.");
		document.form1.banner_type[0].focus();
		return;
	}
	if(temptype=="Y") {
		if(document.form1.banner_image.value.length==0) {
			if(type=="modify" || type=="modify_result") {
				if(document.form1.tempbannerimg.value.length==0) {
					alert("배너 이미지를 등록하세요.");
					document.form1.banner_image.focus();
					return;
				}
			} else {
				alert("배너 이미지를 등록하세요.");
				document.form1.banner_image.focus();
				return;
			}
		}
		if(document.form1.banner_url.length==0) {
			alert("배너 연결URL을 입력하세요.");
			document.form1.banner_url.focus();
			return;
		}
	} else if(temptype=="N") {
		if(document.form1.banner_html.length==0) {
			alert("배너 내용을 입력하세요.");
			document.form1.banner_html.focus();
			return;
		}
	}
	if(type=="modify" || type=="modify_result") {
		if(!confirm("해당 배너를 수정하시겠습니까?")) {
			return;
		}
		document.form1.type.value="modify_result";
	} else {
		document.form1.type.value="insert";
	}
	document.form1.submit();
}

function ModeSend(type,num) {
	if(type=="delete") {
		if(!confirm("해당 배너를 삭제하시겠습니까?")) {
			return;
		}
	}
	document.form1.type.value=type;
	document.form1.num.value=num;
	document.form1.submit();
}


function ChangeType(type){
	if(type=="Y") {
		document.form1.banner_image.disabled=false;
		document.form1.banner_url.disabled=false;
		document.form1.banner_target.disabled=false;
		document.form1.banner_html.disabled=true;
	} else if(type=="N") {
		document.form1.banner_image.disabled=true;
		document.form1.banner_url.disabled=true;
		document.form1.banner_target.disabled=true;
		document.form1.banner_html.disabled=false;
	}
}

function BannerImageMouseOver() {
	obj = event.srcElement;
	WinObj=eval("document.all.bannerimg");
	obj._tid = setTimeout("BannerImageView(WinObj)",200);
}
function BannerImageView(WinObj) {
	WinObj.style.visibility = "visible";
}
function BannerImageMouseOut() {
	obj = event.srcElement;
	WinObj=eval("document.all.bannerimg");
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 마케팅지원 &gt;<span>Affiliate 배너관리</span></p></div></div>
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
			<input type=hidden name=num value="<?=$num?>">
			<input type=hidden name=htmlmode value='wysiwyg'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">Affiliate배너관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>로그인 페이지 우측에 등록될 배너를 관리하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">Affiliate 배너 목록</div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=30></col>
				<col width=55></col>
				<col width=></col>
				<col width=65></col>
				<col width=80></col>
				<col width=60></col>
				<col width=60></col>
				<TR>
					<th>No</th>
					<th>사용여부</th>
					<th>배너제목</th>
					<th>배너타입</th>
					<th>등록일</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan=7;
				$sql = "SELECT num, used, reg_date, title, content FROM tblaffiliatebanner ORDER BY num DESC ";
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$cnt++;
					$reg_date = substr($row->reg_date,0,4).".".substr($row->reg_date,4,2).".".substr($row->reg_date,6,2);
					if($row->used=="Y")	$used_name = "사용중";
					else if($row->used=="N")	$used_name = "사용안함";
					$temptype=$row->content[0];
					if($temptype=="Y") $typename="이미지형";
					else if($temptype=="N") $typename="HTML편집형";
					echo "<tr align=\"center\">\n";
					echo "	<td>{$cnt}</td>\n";
					echo "	<td>{$used_name}</td>\n";
					echo "	<td><div class=\"ta_l\">{$row->title}</div></td>\n";
					echo "	<td>{$typename}</td>\n";
					echo "	<td>{$reg_date}</td>\n";
					echo "	<td><a href=\"javascript:ModeSend('modify','{$row->num}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></td>\n";
					echo "	<td><a href=\"javascript:ModeSend('delete','{$row->num}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></td>\n";
					echo "</tr>\n";
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center>등록된 배너가 없습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">Affiliate 배너 등록/수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>사용여부</span></th>
					<TD class="td_con1"><input type=checkbox id="idx_used" name=used value="Y" <?php if($used=="Y")echo"checked";?> <?=$used_disabled?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_used>사용함</label><br>* 사용함으로 되어 있는 경우에만 표시됩니다. (로그인 페이지에 표시됨)<br><span class="font_orange">* 사용함 기능은 등록후에 변경할 수 있습니다.&nbsp;</span></TD>
				</TR>
				<tr>
					<th><span>배너 제목</span></th>
					<TD class="td_con1"><INPUT style="WIDTH:100%" name=title value="<?=$title?>" class="input"><br><span class="font_orange">＊관리목록에서만 사용합니다. 간단히 입력해 주세요.</span></TD>
				</tr>
				</table>
				</div>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<TD colspan="2" width="760" class="table_cell" style="border-left:1px solid #b9b9b9"><input type=radio id="idx_bannertypeY" name="banner_type" value="Y" onclick="ChangeType('Y')"<?=($banner_type=="Y"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bannertypeY><span class="font_orange">이미지로 배너등록</span></label></td>
				</tr>
				<tr>
					<TD colspan="2" style="border-left:1px solid #b9b9b9">

					<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>노출정보 이미지 선택</span></th>
						<TD>
                        <div class="table_none">						
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td>
							<input type=file name=banner_image size="50">
							<!--
                            <input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly">
                            <div class="file_input_div">
                            <input type="button" value="찾아보기" class="file_input_button" />
                            <input type=file name=banner_image value="" style="width:100%;" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ></div>
                            -->
                            
<?php

							if ($banner_type=="Y") {
								if(ord($banner_image) && file_exists($imagepath.$banner_image)) {
									echo "<input type=hidden name=tempbannerimg value=\"{$banner_image}\">\n";
									echo "<A style=\"cursor:hand;\" onMouseOver=\"BannerImageMouseOver()\" onMouseOut=\"BannerImageMouseOut();\"><B>[이미지 확인]</B></A>";	
									echo "<div id=bannerimg style=\"position:absolute; z-index:100; left:500px; top:200px; visibility:hidden;\">\n";
									echo "<table border=0 cellpadding=0 cellspacing=0>\n";
									echo "<tr><td style=\"border:1px #000000 solid\"><img src=\"".$imagepath.$banner_image."\" border=0></td></tr>\n";
									echo "</table>\n";
									echo "</div>";
								}
							}
?>
							<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 이미지는 150KB 이하의 GIF, JPG만 가능</span></td>
						</tr>
						</table>
                        </div>
						</TD>
					</TR>
					<TR>
						<th><span>연결 URL 입력</span></th>
						<TD class="td_con1"><input type=text name=banner_url value="<?=$banner_url?>" size=50 disabled class="input"> <select name=banner_target disabled class="select">
						<option value="_blank"<?php if($banner_target=="_blank")echo" selected";?>>_blank</option>
						<option value="_top"<?php if($banner_target=="_top")echo" selected";?>>_top</option>
						<option value="_parent"<?php if($banner_target=="_parent")echo" selected";?>>_parent</option>
						<option value="_self"<?php if($banner_target=="_self")echo" selected";?>>_self</option>
						</select>
						</TD>
					</TR>
					</TABLE>
					</div>

					</TD>
				</tr>
				<tr>
					<TD colspan="2" class="table_cell" style="border-left:1px solid #b9b9b9"><input type=radio id="idx_bannertypeN" name="banner_type" value="N" onclick="ChangeType('N')"<?=($banner_type=="N"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bannertypeN><span class="font_orange">HTML로 배너편집</span></label></td>
				</tr>
				<tr>
					<TD colspan="2" style="border-left:1px solid #b9b9b9">
					<div class="table_none">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<col width=140></col>
					<col width=></col>
					<TR>
						<TD bgcolor="white" ><img src="images/icon_point2.gif" border="0">배너 내용입력</TD>
						<TD class="td_con1"><TEXTAREA name=banner_html style="width:100%;height:255" class="textarea" disabled><?=$banner_html?></textarea></TD>
					</tr>
					</table>
					</div>
				</tr>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td HEIGHT=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="<?=$type_name?>" border="0"></a></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>Affiliate 배너관리</span></dt>
							<dd>
								- Affiliate 배너는 최대 5개까지만 등록 가능합니다.<br>
								- Affiliate 배너 사용여부는 수정시에만 선택할 수 있습니다.<br>
								- 등록된 Affiliate 배너는 로그인 페이지 본문 우측에 랜덤하게 1개만 선택하여 출력시킵니다.<br>
						<b>&nbsp;&nbsp;</b>로그인 페이지 템플릿 디자인 관리는 <a href="javascript:parent.topframe.GoMenu(2,'design_login.php');"><span class="font_blue">디자인관리 > 템플릿-페이지 본문 > 로그인 관련 화면 템플릿</span></a><br>
						<b>&nbsp;&nbsp;</b>로그인 페이지 개별 디자인 관리는 <a href="javascript:parent.topframe.GoMenu(2,'design_eachlogin.php');"><span class="font_blue">디자인관리 > 개별디자인-페이지 본문 > 로그인 화면 꾸미기</span></a><br>
								- Affiliate 배너에 관련한 통계자료는 제공되지 않습니다.<br>
								- 사용하지 않는 배너는 되도록 삭제 하세요.

							</dd>
							
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="update">
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
<script>
ChangeType("<?=$banner_type?>");
//editor_generate('content');
</script>
<?=$onload?>
<?php 
include("copyright.php");
