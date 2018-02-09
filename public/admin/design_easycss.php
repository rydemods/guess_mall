<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-7";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$array_text[0]['idx']="family";
$array_text[0]['name']="글꼴";
$array_text[0]['value']="굴림=굴림,바탕=바탕,돋움=돋움,궁서=궁서,명조=명조,verdana=verdana,Arial=Arial,Wingdings=Wingdings,Tahoma=Tahoma,System=System,Arial Black=Arial Black,Arial Narrow=Arial Narrow,Comic Sans MS=Comic Sans MS,Courier New=Courier New,Georgia=Georgia";
$array_text[1]['idx']="size";
$array_text[1]['name']="크기";
$array_text[1]['value']="7pt=7,8pt=8,9pt=9,10pt=10,11pt=11,12pt=12,13pt=13,14pt=14,15pt=15,16pt=16";
$array_text[2]['idx']="weight";
$array_text[2]['name']="두께";
$array_text[2]['value']="normal=보통,bold=두껍게";
$array_text[3]['idx']="decoration";
$array_text[3]['name']="옵션";
$array_text[3]['value']="=없음,underline=밑줄,line-through=가운데줄";
$array_text[4]['idx']="color";
$array_text[4]['name']="색상";
$array_text[4]['value']="";

$array_menu[0]=array("상품카테고리","커뮤니티","고객상담");
$array_menu[1]=array("신규/인기/추천 상품명","신규/인기/추천 가격","특별상품명","특별상품 가격","메인공지사항","메인컨텐츠","메인투표","메인게시판","메인시중가격","메인적립금","메인태그","메인제조사","메인진열코드");
$array_menu[2]=array("분류그룹 상단카테고리명","분류그룹 상위카테고리명","분류그룹 하위카테고리명","상품명","상품가격","원산지","제조사","시중가격","적립금","태그","특이사항","상품정렬방법","현재 상품정렬방법","상품목록 페이지 숫자","현재페이지 숫자","진열코드");

$css="";

$type=$_POST["type"];
if($type=="update") {
	$up_family=(array)$_POST["up_family"];
	$up_size=(array)$_POST["up_size"];
	$up_weight=(array)$_POST["up_weight"];
	$up_decoration=(array)$_POST["up_decoration"];
	$up_color=(array)$_POST["up_color"];

	$k=0;
	$css0 = array();
	$css1 = array();
	for($i=0;$i<count($array_menu[$k]);$i++) {
		$up_color[$k][$i]=str_replace(",","",$up_color[$k][$i]);
		$up_color[$k][$i]=str_replace("","",$up_color[$k][$i]);
		$css0[]=$up_family[$k][$i];
		$css0[]=$up_size[$k][$i];
		$css0[]=$up_weight[$k][$i];
		$css0[]=$up_decoration[$k][$i];
		$css0[]=$up_color[$k][$i];
	}
	$css1[]=implode(',',$css0);

	$k=1;
	$css0 = array();
	for($i=0;$i<count($array_menu[$k]);$i++) {
		$up_color[$k][$i]=str_replace(",","",$up_color[$k][$i]);
		$up_color[$k][$i]=str_replace("","",$up_color[$k][$i]);
		$css0[]=$up_family[$k][$i];
		$css0[]=$up_size[$k][$i];
		$css0[]=$up_weight[$k][$i];
		$css0[]=$up_decoration[$k][$i];
		$css0[]=$up_color[$k][$i];
	}
	$css1[]=implode(',',$css0);

	$k=2;
	$css0 = array();
	for($i=0;$i<count($array_menu[$k]);$i++) {
		$up_color[$k][$i]=str_replace(",","",$up_color[$k][$i]);
		$up_color[$k][$i]=str_replace("","",$up_color[$k][$i]);
		$css0[]=$up_family[$k][$i];
		$css0[]=$up_size[$k][$i];
		$css0[]=$up_weight[$k][$i];
		$css0[]=$up_decoration[$k][$i];
		$css0[]=$up_color[$k][$i];
	}
	$css1[]=implode(',',$css0);
	
	$css = implode('',$css1);
	$sql = "UPDATE tblshopinfo SET css='{$css}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");

	$_shopdata->css=$css;

	$onload="<script>window.onload=function(){ alert(\"쇼핑몰 텍스트 속성 변경이 완료되었습니다.\"); }</script>";
} elseif($type=="clear") {
	$sql = "UPDATE tblshopinfo SET css='' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");

	$_shopdata->css="";

	$onload="<script>window.onload=function(){ alert(\"쇼핑몰 텍스트 속성이 기본값으로 복원되었습니다.\"); }</script>";
}

if(ord($_shopdata->css)==0) {
	$sql = "SELECT * FROM tbltempletinfo WHERE icon_type='{$_shopdata->icon_type}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	
	$_shopdata->css=$row->default_css;
	pmysql_free_result($result);
}

if(ord($_shopdata->css)==0) {
	for($i=0;$i<count($array_menu[0]);$i++) {
		$_shopdata->css.="굴림,";
		$_shopdata->css.="9pt,";
		$_shopdata->css.="normal,";
		$_shopdata->css.=",";
		$_shopdata->css.=",";
	}
	$_shopdata->css.="";
	for($i=0;$i<count($array_menu[1]);$i++) {
		$_shopdata->css.="굴림,";
		$_shopdata->css.="9pt,";
		$_shopdata->css.="normal,";
		$_shopdata->css.=",";
		$_shopdata->css.=",";
	}
	$_shopdata->css.="";
	for($i=0;$i<count($array_menu[2]);$i++) {
		$_shopdata->css.="굴림,";
		$_shopdata->css.="9pt,";
		$_shopdata->css.="normal,";
		$_shopdata->css.=",";
		$_shopdata->css.=",";
	}
}
$array_val=explode("",$_shopdata->css);
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="clear") {
		if(!confirm("기본 속성값으로 변경하시겠습니까?")) {
			return;
		}
	}
	document.form1.type.value=type;
	document.form1.submit();
}

function selcolor(obj){
	fontcolor = obj.value.substring(1);
	var newcolor = showModalDialog("color.php?color="+fontcolor, "oldcolor", "resizable: no; help: no; status: no; scroll: no;");
	if(newcolor){
		obj.value=newcolor;
	}
}

</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; Easy디자인 관리 &gt;<span>Easy 텍스트 속성 변경</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" >
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 타이틀 -->
					<div class="title_depth3">Easy 텍스트 속성 변경</div>
					<div class="title_depth3_sub"><span>메인페이지, 상품카테고리, 검색화면에서 보여지는 텍스트들의 속성을 간단하게 변경하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">메인화면 왼쪽메뉴</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th class="top" style="border-left:1px solid #b9b9b9">메뉴명</th>
					<th class="top">글꼴</th>
					<th class="top">크기</th>
					<th class="top">굵기</th>
					<th class="top">밑줄처리</th>
					<th class="top">색상</th>
				</TR>
<?php
			$z=0;
			$k=0;
			$m=0;
			$value=explode(",",$array_val[$z]);
			for($i=0;$i<count($array_menu[$z]);$i++) {
				echo "<TR>\n";
				echo "	<th><span>{$array_menu[$z][$i]}</span></th>\n";
				for($j=0;$j<count($array_text);$j++) {
					echo "	<TD class=\"td_con1\" align=center>";
					if($array_text[$j]['idx']=="color") {
						//$array_text[$j]['name']." : ";
						echo " <div class=\"table_none\">\n";
						echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"140\" align=\"center\">\n";
						echo "<tr>\n";
						echo "	<td>#</td>\n";
						echo "	<td width=\"34\"><input type=text name=\"up_{$array_text[$j]['idx']}[{$z}][]\" value=\"{$value[$k]}\" size=8 maxlength=6 class=\"input\"></td>\n";
						echo "	<td width=\"34\"><font color=\"{$value[$k]}\"><span style=\"font-size:20pt;\">■</span></font></td>\n";
						echo "	<td>&nbsp;<a href=\"javascript:selcolor(document.form1['up_{$array_text[$j]['idx']}[{$z}][]'][{$m}])\"><IMG src=\"images/icon_color.gif\" border=0 width=\"55\" height=\"18\"></a></td>\n";
						echo "</tr>\n";
						echo "</table>\n";
						echo "</div>\n";
						$m++;
					} else {
						//echo "&nbsp;{$array_text[$j]['name']} : ";
						echo "<select name=\"up_{$array_text[$j]['idx']}[{$z}][]\" class=\"select\">\n";
						$tmparr=explode(",",$array_text[$j]['value']);
						for($y=0;$y<count($tmparr);$y++) {
							$tmp=explode("=",$tmparr[$y]);
							echo "<option value=\"{$tmp[0]}\" ";
							if($value[$k]==$tmp[0]) echo " selected";
							echo ">{$tmp[1]}</option>\n";
						}
						echo "</select>\n";
					}
					$k++;
					echo "	</td>\n";
				}
				echo "</TR>\n";
			}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">메인본문 메뉴</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th class="top" style="border-left:1px solid #b9b9b9">메뉴명</div></th>
					<th class="top">글꼴</th>
					<th class="top">크기</th>
					<th class="top">굵기</th>
					<th class="top">밑줄처리</th>
					<th class="top">색상</th>
				</TR>
<?php
			$z=1;
			$k=0;
			$m=0;
			$value=explode(",",$array_val[$z]);
			for($i=0;$i<count($array_menu[$z]);$i++) {
				echo "<tr>\n";
				echo "	<th><span>{$array_menu[$z][$i]}</span></th>\n";
				for($j=0;$j<count($array_text);$j++) {
					echo "	<TD class=\"td_con1\" align=center>";
					if($array_text[$j]['idx']=="color") {
						//echo "&nbsp;{$array_text[$j]['name']} : ";
						echo " <div class=\"table_none\">\n";
						echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"140\" align=\"center\">\n";
						echo "<tr>\n";
						echo "	<td>#</td>\n";
						echo "	<td width=\"34\"><input type=text name=\"up_{$array_text[$j]['idx']}[{$z}][]\" value=\"{$value[$k]}\" size=8 maxlength=6 class=\"input\"></td>\n";
						echo "	<td width=\"34\"><font color=\"{$value[$k]}\"><span style=\"font-size:20pt;\">■</span></font></p></td>\n";
						echo "	<td>&nbsp;<a href=\"javascript:selcolor(document.form1['up_{$array_text[$j]['idx']}[{$z}][]'][{$m}])\"><IMG src=\"images/icon_color.gif\" border=0 width=\"55\" height=\"18\"></a></td>\n";
						echo "</tr>\n";
						echo "</table>\n";
						echo "</div>\n";
						$m++;
					} else {
						//echo "&nbsp;{$array_text[$j]['name']} : ";
						echo "<select name=\"up_{$array_text[$j]['idx']}[{$z}][]\" class=\"select\">\n";
						$tmparr=explode(",",$array_text[$j]['value']);
						for($y=0;$y<count($tmparr);$y++) {
							$tmp=explode("=",$tmparr[$y]);
							echo "<option value=\"{$tmp[0]}\" ";
							if($value[$k]==$tmp[0]) echo " selected";
							echo ">{$tmp[1]}</option>\n";
						}
						echo "</select>\n";
					}
					echo "	</td>\n";
					$k++;
				}
				echo "</tr>\n";
			}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품 분류/검색 페이지</div>
				</td>
			</tr>
			<tr>
				<td align="center">
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th class="top" style="border-left:1px solid #b9b9b9">메뉴명</div></th>
					<th class="top">글꼴</th>
					<th class="top">크기</th>
					<th class="top">굵기</th>
					<th class="top">밑줄처리</th>
					<th class="top">색상</th>
				</TR>
<?php
			$z=2;
			$k=0;
			$m=0;
			$value=explode(",",$array_val[$z]);
			for($i=0;$i<count($array_menu[$z]);$i++) {
				echo "<tr>\n";
				echo "	<th><span>{$array_menu[$z][$i]}</span></th>\n";
				for($j=0;$j<count($array_text);$j++) {
					echo "	<TD class=\"td_con1\" align=center>";
					if($array_text[$j]['idx']=="color") {
						//echo "&nbsp;{$array_text[$j]['name']} : ";
						echo " <div class=\"table_none\">\n";
						echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"140\" align=\"center\">\n";
						echo "<tr>\n";
						echo "	<td>#</td>\n";
						echo "	<td width=\"34\"><input type=text name=\"up_{$array_text[$j]['idx']}[{$z}][]\" value=\"{$value[$k]}\" size=8 maxlength=6 class=\"input\"></td>\n";
						echo "	<td width=\"34\"><font color=\"{$value[$k]}\"><span style=\"font-size:20pt;\">■</span></font></td>\n";
						echo "	<td>&nbsp;<a href=\"javascript:selcolor(document.form1['up_{$array_text[$j]['idx']}[{$z}][]'][{$m}])\"><IMG src=\"images/icon_color.gif\" border=0 width=\"55\" height=\"18\"></a></td>\n";
						echo "</tr>\n";
						echo "</table>\n";
						echo "</div>\n";
						$m++;
					} else {
						//echo "&nbsp;{$array_text[$j]['name']} : ";
						echo "<select name=\"up_{$array_text[$j]['idx']}[{$z}][]\" class=\"select\">\n";
						$tmparr=explode(",",$array_text[$j]['value']);
						for($y=0;$y<count($tmparr);$y++) {
							$tmp=explode("=",$tmparr[$y]);
							echo "<option value=\"{$tmp[0]}\" ";
							if($value[$k]==$tmp[0]) echo " selected";
							echo ">{$tmp[1]}</option>\n";
						}
						echo "</select>\n";
					}
					$k++;
					echo "	</td>\n";
				}
				echo "</tr>\n";
			}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" border="0" hspace="2"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>공동구매와 경매는 설정이 적용되지 않습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>메인게시판은 [메인본문 개별 디자인]할 경우에만 쇼핑몰에 반영됩니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>개별디자인시 매크로명령어로 불러오는 일반 게시판의 텍스트 속성을 의미합니다.</dt>
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