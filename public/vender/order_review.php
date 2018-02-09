<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$code=$_POST["code"];
$s_check=$_POST["s_check"];
$s_type=$_POST["s_type"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$qry = "WHERE a.productcode=b.productcode AND b.productcode=c.c_productcode AND b.vender='".$_VenderInfo->getVidx()."' ";
if(strlen($code)>=3) {
	$qry.= "AND c.c_category LIKE '".$code."%' ";
}
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "AND a.date LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "AND a.date>='".$search_s."' AND a.date <='".$search_e."' ";
}
//$qry.= "AND a.display='Y' ";
$qry.= "AND b.display='Y' ";
if(strlen($search)>0) {
	if($s_check=="t") $qry.= "AND (a.subject LIKE '%".$search."%' OR a.content LIKE '%".$search."%') ";
	else if($s_check=="n") $qry.= "AND a.name='".$search."' ";
}

if (ord($s_type)) {
	$qry.= "AND a.type='".$s_type."' ";
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "order.ctgr.php?code="+code;
	BCodeCtgr.location.href = murl;
}

function OnChangePeriod(val) {
	var pForm = document.sForm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function searchForm() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

var old_menu="";
function view(submenu){
	if(old_menu!=submenu) {
		if(old_menu!="") document.getElementById(old_menu).style.display = 'none';
		document.getElementById(submenu).style.display="";
		old_menu=submenu;
	} else {
		document.getElementById(submenu).style.display="none";
		old_menu="";
	}
}

function formSubmit(form) {
	form.mode.value="update";
	form.target="processFrame";
	form.submit();
}
</script>

<!-- <table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed"> -->
<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<!-- <col width=740></col> -->
<col width=1300></col>
<!-- <col width=80></col> -->
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>상품 리뷰 관리</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>상품 리뷰 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사가 등록한 상품에 대해서만 리뷰 게시물을 확인할 수 있습니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15">

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<form name=sForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=code value="<?=$code?>">
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<col width=></col>
						<col width=130></col>
						<tr>
							<td>
							<U>접수일</U>&nbsp; <input type=text name=search_start value="<?=$search_start?>" size=10 OnClick="Calendar(event)" style="text-align:center;font-size:8pt"> ~ <input type=text name=search_end value="<?=$search_end?>" size=10 OnClick="Calendar(event)" style="text-align:center;font-size:8pt">
							&nbsp;
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							&nbsp;&nbsp;&nbsp;&nbsp;
							<U>분류</U>&nbsp;
							<select name="code1" style=width:130; onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
							<option value="">--- 선택하세요 ---</option>
<?php
							$sql = "SELECT SUBSTR(b.c_category,1,3) as prcode FROM tblproduct a left join tblproductlink b on a.productcode=b.c_productcode ";
							$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
							$sql.= "GROUP BY prcode ";
							$result=pmysql_query($sql,get_db_conn());
							$codes="";
							while($row=pmysql_fetch_object($result)) {
								$codes.=$row->prcode.",";
							}
							pmysql_free_result($result);
							if(strlen($codes)>0) {
								$codes=rtrim($codes,',');
								$prcodelist=str_replace(',','\',\'',$codes);
							}
							if(strlen($prcodelist)>0) {
								$sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode ";
								$sql.= "WHERE code_a IN ('".$prcodelist."') AND code_b='000' AND code_c='000' ";
								$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
								$result=pmysql_query($sql,get_db_conn());
								while($row=pmysql_fetch_object($result)) {
									echo "<option value=\"".$row->code_a."\"";
									if($row->code_a==substr($code,0,3)) echo " selected";
									echo ">".$row->code_name."</option>\n";
								}
								pmysql_free_result($result);
							}
?>
							</select>
							</td>
							<td><iframe name="BCodeCtgr" src="order.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>" width="130" height="21" scrolling=no frameborder=no></iframe></td>
						</tr>
						<tr><td colspan=2 height=8></td></tr>
						<tr>
							<td colspan=2>
							<U>리뷰타입</U>&nbsp;
							<select name=s_type style="font-size:8pt">
							<option value="" <?if($s_type=="")echo"selected";?>>전체</option>
							<option value="0" <?if($s_type=="0")echo"selected";?>>텍스트리뷰</option>
							<option value="1" <?if($s_type=="1")echo"selected";?>>포토리뷰</option>
							</select>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<U>검색어</U>&nbsp;
							<select name=s_check style="font-size:8pt">
							<option value="n" <?if($s_check=="n")echo"selected";?>>작성자</option>
							<option value="t" <?if($s_check=="t")echo"selected";?>>제목+내용</option>
							</select>
							<input type=text name=search value="<?=$search?>" size=30>
							<A HREF="javascript:searchForm()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</form>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=20></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<col width=70></col>
				<col width=110></col>
				<col width=70></col>
				<col width=></col>
				<col width=70></col>
				<col width=75></col>
				<tr height=28 align=center bgcolor=F5F5F5>
					<td><B>타입</B></td>
					<td><B>상품명</B></td>
					<td><B>별점</B></td>
					<td><B>상품평</B></td>
					<td><B>작성자</B></td>
					<td><B>등록일</B></td>
				</tr>
<?php
				$sql = "SELECT COUNT(*) as t_count FROM tblproductreview a, tblproduct b, (select * from tblproductlink where c_maincate = '1') c ".$qry." ";
				echo $sql;
				$paging = new Paging($sql,10,10);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT a.*, b.productname,b.selfcode FROM tblproductreview a, tblproduct b, (select * from tblproductlink where c_maincate = '1') c ".$qry." ";
				$sql.= "ORDER BY a.date DESC ";
				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());
				$k=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
					$date=substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);
					$contents=explode("=",$row->content);
					//별점
					$marks="";
					for($i=0;$i<$row->marks;$i++){
						$marks.="<FONT color=#000000>★</FONT>";
					}
					for($i=$row->marks;$i<5;$i++){
						$marks.="<FONT color=#DEDEDE>★</FONT>";
					}

					// 업로드 이미지 정보
					$arrUpFile = array();

					if ( !empty($row->upfile) ) { array_push($arrUpFile, $row->upfile); }
					if ( !empty($row->upfile2) ) { array_push($arrUpFile, $row->upfile2); }
					if ( !empty($row->upfile3) ) { array_push($arrUpFile, $row->upfile3); }
					$add_img	= "";
					foreach ( $arrUpFile as $key => $val ) {
						$add_img	.= "<br/><img src='" . $Dir.DataDir."shopimages/review/" . $val . "' style='max-width:320px'/>";
					}

					echo "<tr align=center valign=top bgcolor=FFFFFF>\n";
					echo "	<td width=100% nowrap style=padding:3;font-size:8pt; align=center valign=middle>\n";
                    if ( $row->type == '1' ) {
                        echo "  포토리뷰";
                    } else {
                        echo "  텍스트리뷰";
                    }
					echo "	</td>\n";
					echo "	<td  align=left style=padding:3;>\n";
					echo "	<a href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" target=_blank>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</a>\n";
					echo "	</td>\n";
					echo "	<td  align=center style=padding:3; valign=middle>".$marks."</td>\n";
					echo "	<td  align=left style=padding:3; valign=middle><a href='javascript:;' onClick=\"javascript:view('sub".$k."');\"><b>".titleCut(38,$row->subject)."</b></a></td>\n";
					echo "	<td  align=center style=padding:3; valign=middle>".$row->name."</td>\n";
					echo "	<td  align=center style=padding:3; valign=middle>".$date."</td>\n";
					echo "</tr>\n";
					echo "<tr align=center valign=top bgcolor=FFFFFF id=sub".$k." style=display:none>\n";
					echo "	<td colspan=6 align=left style=padding:10;>".nl2br($contents[0])."<br>".$add_img."<br></td>\n";
					echo "</tr>\n";
					$k++;
				}
				pmysql_free_result($result);
				if($i==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=6 align=center>조회된 내용이 없습니다.</td></tr>\n";
				} else if($i>0) {
					$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				}
?>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
				</tr>
				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>

<form name=pageForm method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=s_type value="<?=$s_type?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<?php include("copyright.php"); ?>
