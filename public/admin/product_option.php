<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-6";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$mode=$_POST["mode"];
$option_code=$_POST["option_code"];
$description=$_POST["description"];
$option_choice=$_POST["option_choice"];
$option_list=$_POST["option_list"];
if ($type=="delete") {
	if (ord($option_code)) {
		$sql = "DELETE FROM tblproductoption WHERE option_code = '{$option_code}' ";
		$delete = pmysql_query($sql,get_db_conn());
		if ($delete) {
			$onload="<script>window.onload=function(){alert('해당 옵션그룹 삭제가 완료되었습니다.');}</script>";
		}
	}
	$type="insert";
} else if ($type=="modify") {
	if (ord($option_code)) {
		if ($mode=="result") {
			$arroptval = explode("↕",$option_list);
			$sql = "UPDATE tblproductoption SET ";
			$sql.= "description		= '{$description}', ";
			for($i=0;$i<10;$i++) {
				$tmp = sprintf("%02d",$i+1);
				if (ord($arroptval[$i])) {
					$sql.= "option_value{$tmp} = '{$arroptval[$i]}', ";
				} else {
					$sql.= "option_value{$tmp} = NULL, ";
				}
			}
			$sql.= "option_choice	= '{$option_choice}' ";
			$sql.= "WHERE option_code = '{$option_code}' ";
			$update = pmysql_query($sql,get_db_conn());
			if ($update) {
				$onload="<script>window.onload=function(){alert('해당 옵션그룹 정보 수정이 완료되었습니다.');}</script>";
			}
		}
		$sql = "SELECT * FROM tblproductoption WHERE option_code = '{$option_code}' ";
		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {
			$description = $row->description;
			$option_choice = $row->option_choice;
			$option_value01 = $row->option_value01;
			$option_value02 = $row->option_value02;
			$option_value03 = $row->option_value03;
			$option_value04 = $row->option_value04;
			$option_value05 = $row->option_value05;
			$option_value06 = $row->option_value06;
			$option_value07 = $row->option_value07;
			$option_value08 = $row->option_value08;
			$option_value09 = $row->option_value09;
			$option_value10 = $row->option_value10;
		} else {
			$type="insert";
		}
		pmysql_free_result($result);
	}
} elseif ($type=="insert" && $mode=="result") {
	$sql = "SELECT MAX(option_code) as maxcode FROM tblproductoption ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_object($result)) {
		if($row->maxcode==NULL) $option_code=1000;
		else $option_code=$row->maxcode+10;
	} else {
		$option_code=1000;
	}
	pmysql_free_result($result);
	$arroptval = explode("↕",$option_list);
	$sql = "INSERT INTO tblproductoption(option_code,description,";
	for($i=0;$i<count($arroptval);$i++) {
		$tmp = sprintf("%02d",$i+1);
		$sql.= "option_value{$tmp}, ";
	}
	$sql .="option_choice) VALUES (
	'{$option_code}', 
	'{$description}',";
	for($i=0;$i<count($arroptval);$i++) {
		$sql.= "'{$arroptval[$i]}', ";
	}
	$sql.= "'{$option_choice}')";
	$insert = pmysql_query($sql,get_db_conn());
	if ($insert) {
		$onload="<script>window.onload=function(){alert('옵션그룹 등록이 완료되었습니다.');}</script>";
	}
} else {
	$type="insert";
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	var option_choice = "";
	var option_list = "";
	if (form.description.value.length==0) {
		form.description.focus();
		alert("옵션그룹명을 입력하세요.");
		return;
	}
	var yy=0;
	for(var i=0;i<10;i++) {
		if (form["option_value"+i].value.length>0) {
			if (form["option_name"+i].value.length==0) {
				form["option_name"+i].focus();
				alert("옵션명을 입력하세요.");
				return;
			}
			tmpline = form["option_value"+i].value.split("\r\n");
			if (tmpline.length>0) {
				if (yy>0) {
					option_list+="↕";
				}
				option_list+=form["option_name"+i].value;
				for(var j=0;j<tmpline.length;j++) {
					tmp = tmpline[j].split(",");

					if (tmp.length>1) {
						if (isNaN(tmp[1])) {
							form["option_value"+i].focus();
							alert("해당 속성의 속성가격은 숫자만 입력 가능합니다.");
							return;
						}
						option_list+=""+tmp[0]+","+tmp[1];
					} else {
						if (tmp.length>0) {
							option_list+=""+tmp[0];
						}
					}
				}
				if (form["option_choice"+i][0].checked == true) {
					no=0;
				} else {
					no=1;
				}
				option_choice+=""+no;
				yy++;
			}
		}
	}

	if (option_choice.length > 0) {
		option_choice = option_choice.substring(1,option_choice.length);
	} else {
		alert("옵션이 하나 이상 추가되지 않았습니다.");
		return;
	}
	form.option_choice.value=option_choice;
	form.option_list.value=option_list;
	form.submit();
}

function SendMode(type,code) {
	if (type=="delete") {
		if (!confirm("해당 옵션그룹을 삭제하시겠습니까?")) {
			return;
		}
	}
	document.form2.type.value=type;
	document.form2.option_code.value=code;
	document.form2.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 옵션그룹 등록 관리 &gt;<span>옵션그룹 등록 관리</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">옵션그룹 등록 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품에 등록/수정시 지정할 옵션그룹을 등록할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">등록된 옵션그룹</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <col width=40 />
                <col width= />
                <col width=60 />
                <col width=60 />
				<TR align=center>
					<th>No</th>
					<th>옵션그룹명</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php

				$sql = "SELECT COUNT(*) as t_count FROM tblproductoption ";
				$paging = new Paging($sql,10,10);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;
				
				$sql = "SELECT * FROM tblproductoption ";
				$sql.= "ORDER BY option_code DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
					echo "<tr>\n";
					echo "	<TD>{$number}</td>\n";
					echo "	<TD><div class=\"ta_l\">{$row->description}</div></td>\n";
					echo "	<TD><A HREF=\"javascript:SendMode('modify','{$row->option_code}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></A></td>\n";
					echo "	<TD><A HREF=\"javascript:SendMode('delete','{$row->option_code}');\"><img src=\"images/btn_del.gif\" border=\"0\"></A></td>\n";
					echo "</tr>\n";
					$i++;
				}
				pmysql_free_result($result);

				if ($i==0) {
					echo "<tr><td colspan=\"4\" align=\"center\">등록된 옵션그룹 정보가 없습니다.</td></tr>\n";
				}
?>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" align=center class="font_size">
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">옵션그룹 등록/수</div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=type value="<?=$type?>">
			<input type=hidden name=mode value="result">
			<input type=hidden name=option_code value="<?=$option_code?>">
			<input type=hidden name=option_choice>
			<input type=hidden name=option_list>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th width="200"><span>옵션그룹명</span></th>
					<TD class="td_con1" width="85%"><input type=text name="description" value="<?=$description?>" size=100 maxlength=200 onKeyDown="chkFieldMaxLen(200);" style="width:100%" class="input"></TD>
				</TR>
				<tr>
					<TD colspan="2" style="border-left:1px solid #b9b9b9;">
                    <div class="table_none">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
<?php
					$option_choice = explode("",$option_choice);
					$arrsample1 = array("색상","사이즈","스타일","색상","사이즈","스타일","색상","사이즈","스타일","색상");
					$arrsample2 = array("파랑,1000","XL,0 또는 XL","남녀커플반지(14K),10000","빨강,0 또는 빨강","XXL,1000","남반지(14K),5000","흰색,0 또는 흰색","M,0 또는 M","남녀커플반지(18K),20000","노랑,0 또는 노랑");
					for($i=0;$i<10;$i++) {
						if ($i!=0 && $i%2==0) {
							echo "</tr><TD colSpan=2 height=20></TD></tr><tr>\n";
						}
						echo "<TD width=\"50%\">\n";

						$cnt = sprintf("%02d",$i+1);
						$options = explode("",${"option_value".$cnt});

						$check_choice0='';
						$check_choice1='';
						if ($option_choice[$i]==1)	$check_choice1 = "checked";
						else						$check_choice0 = "checked";
?>
						<TABLE cellSpacing="1" cellPadding="1" width="100%" border=0 bgcolor="#EBEBEB">
						<tr>
							<TD class=lineleft align=middle bgColor=#f0f0f0 colSpan=2><B>옵션<?=($i+1)?></B></td>
						</tr>
						<tr>
							<TD class=lineleft style="PADDING-RIGHT: 5px" noWrap align=right width=120 bgcolor="white">옵션필수여부</td>
							<TD class=line style="PADDING-LEFT: 5px" width="100%" bgcolor="white"><input type=radio id="idx_option_choice1<?=$i?>" name="option_choice<?=$i?>" value=0 <?=$check_choice0?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_option_choice1<?=$i?>>미필수</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_option_choice2<?=$i?>" name="option_choice<?=$i?>" value=1 <?=$check_choice1?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_option_choice2<?=$i?>>필수</label></td>
						</tr>
						<tr>
							<TD class=lineleft style="PADDING-RIGHT: 5px" noWrap align=right width=120 bgcolor="white">속성명</td>
							<TD class=line style="PADDING-LEFT: 5px; PADDING-BOTTOM: 3px; LINE-HEIGHT: 15px; PADDING-TOP: 3px" width="100%" bgcolor="white"><input type=text name="option_name<?=$i?>" value="<?=$options[0]?>" style="width:98%" class="input"><BR><FONT class=font_orange>예) <?=$arrsample1[$i]?></font></td>
						</tr>
						<tr>
							<TD class=linebottomleft style="PADDING-RIGHT: 5px" noWrap align=right width=120 bgcolor="white">속성,속성가격</td>
							<TD style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 3px; PADDING-TOP: 3px" width="100%" bgcolor="white">
<?php
							$option_value='';
							$yy=0;
							for($y=1;$y<count($options);$y++) {
								if (ord(trim($options[$y]))) {
									if ($yy>0) $option_value.= "\r\n";
									$option_value.= trim($options[$y]);
									$yy++;
								}
							}
?>
							<textarea name="option_value<?=$i?>" style="width:100%;height:71" class="textarea"><?=$option_value?></textarea><br>* 한줄에 하나의 속성과 속성가격 입력<BR>* <FONT class=font_orange>예) <?=$arrsample2[$i]?></FONT>
							</td>
						</tr>
						</table>
<?php
						echo "</td>\n";
						if ($i%2==0) {
							echo "<TD noWrap width=20></TD>\n";
						}
					}
?>
					
                    </TR>
                    </TABLE>
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=type>
			<input type=hidden name=option_code>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>옵션그룹 등록 관리</span></dt>
							<dd>
							- 옵션그룹은 무제한 등록이 가능합니다.<br>
							- 옵션그룹을 수정/삭제시 해당 옵션그룹을 등록한 상품은 바로 적용됩니다.
							- 옵션 출력방식은 <a href="javascript:parent.topframe.GoMenu(1,'shop_mainproduct.php');"><span class="font_blue">상점관리 > 쇼핑몰 환경 설정 > 상품 진열 기타 설정</span></a> 에서 설정할 수 있습니다.
							</dd>
						</dl>
						<dl>
							<dt><span>옵션그룹 등록 방법</span></dt>
							<dd>
							① 옵션그룹명을 입력합니다.<br>
							② 옵션필수여부를 선택해 주세요.<br>
							③ 속성명을 입력해 주세요.&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">예)사이즈, 색상, 용량, 용도 등</span><br>
							④ 속성,속성가격을 입력해 주세요. 속성가격은 선택사항입니다.(한줄에 하나씩 속성,속성가격 입력)<br>
													&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">예)속성명이 색상일 경우<span class="font_blue">(속성가격 입력)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;예)속성명이 사이즈일 경우<span class="font_blue">(속성가격 미입력)</span><br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;블루,25600&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L(100~105)<br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;레드,26600&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;XL(105~110)<br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;아이보리,25600&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;XXL(110)<br>
													</span>
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
<?=$onload?>
<?php 
include("copyright.php");
