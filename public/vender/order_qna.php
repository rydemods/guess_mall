<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

//상품QNA 게시판 존재여부 확인 및 설정정보 확인
$prqnaboard=getEtcfield($_venderdata->etcfield,"PRQNA");
if(strlen($prqnaboard)>0) {
	$sql = "SELECT * FROM tblboardadmin WHERE board='".$prqnaboard."' ";
	$result=pmysql_query($sql,get_db_conn());
	$qnasetup=pmysql_fetch_object($result);
	pmysql_free_result($result);

	$qnasetup->btype=$qnasetup->board_skin[0];
	$qnasetup->max_filesize=$qnasetup->max_filesize*(1024*100);
	if($qnasetup->use_hidden=="Y") $qnasetup=NULL;
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$code=$_POST["code"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[1];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?$search_start." 00:00:00":$period[0]." 00:00:00";
$search_e=$search_end?$search_end." 23:59:59":date("Ymd",$CurrentTime)." 23:59:59";

$search_s=strtotime($search_s);
$search_e=strtotime($search_e);

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$qry = "WHERE a.board='".$qnasetup->board."' ";
$qry.= "AND a.pridx=b.pridx ";
$qry.= "AND b.vender='".$_VenderInfo->getVidx()."' ";
if(strlen($code)>=3) {
	$qry.= "AND b.c_category LIKE '".$code."%' ";
}
if(date("Ymd",$search_s)==date("Ymd",$search_e)) {
	$qry.= "AND to_char(to_timestamp(a.writetime),'YYYY-MM-DD') = '".date("Y-m-d",$search_s)."' ";
} else {
	$qry.= "AND a.writetime>='".$search_s."' AND a.writetime <='".$search_e."' ";
}
if(strlen($search)>0) {
	if($s_check=="t") $qry.= "AND (a.title LIKE '%".$search."%' OR a.content LIKE '%".$search."%') ";
	else if($s_check=="n") $qry.= "AND a.name='".$search."' ";
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

function viewArticle(num) {
	//view, modify, write
	document.procForm.num.value=num;
	document.procForm.action="order_qnaview.php";
	document.procForm.submit();
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
					<FONT COLOR="#ffffff"><B>상품 Q&A 관리</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>상품 Q&A 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사에서 등록한 상품에 대해서만 Q&A 게시물을 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사는 등록된 Q&A 게시물의 관리[답변/수정/삭제]를 할 수 있습니다.</td>
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
							<U>검색어</U>&nbsp;
							<select name=s_check>
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
				<col width=35></col>
				<col width=></col>
				<col width=200></col>
				<col width=75></col>
				<col width=75></col>
				<tr height=28 align=center bgcolor=F5F5F5>
					<td><B>번호</B></td>
					<td><B>제목</B></td>
					<td><B>상품명</B></td>
					<td><B>답변</B></td>
					<td><B>등록일</B></td>
				</tr>
<?
				$colspan=5;
				if(strlen($qnasetup->board)>0) {
					$sql = "SELECT COUNT(*) as t_count FROM tblboard a, (select a.*, c.c_category FROM tblproduct a left join tblproductlink c on a.productcode=c.c_productcode and c.c_category='1') b ".$qry." ";
					echo $sql;
					$paging = new Paging($sql,10,10);
					$t_count = $paging->t_count;
					$gotopage = $paging->gotopage;

					$sql = "SELECT a.*, b.productcode,b.productname,b.selfcode FROM tblboard a, (select a.*, c.c_category FROM tblproduct a left join tblproductlink c on a.productcode=c.c_productcode and c.c_category='1') b ".$qry." ";
					$sql.= "ORDER BY a.thread, a.pos ";

					//echo $sql;
					$sql = $paging->getSql($sql);
					$result=pmysql_query($sql,get_db_conn());
					$i=0;
					while($row=pmysql_fetch_object($result)) {
						$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);

						list($qnaCount)=pmysql_fetch("SELECT count(num) FROM tblboardcomment WHERE board = 'qna' and parent = '".$row->num."'");
						
						$a_status	= "<font color='blue'>대기</font>";
						if ($qnaCount > 0)$a_status	= "<font color='red'>완료</font>";

						$subject='';
						$depth=$row->depth;
						$wid=1;
						if ($depth > 0) {
							if ($depth == 1) {
								$wid = 6;
							} else {
								$wid = (6 * $depth) + (4 * ($depth-1));
							}
							$subject .= "<img src=images/x.gif width=".$wid." height=2 border=0>";
							$subject .= "<img src=images/re_mark.gif border=0>";
						}
						$subject .= strip_tags($row->title);

						echo "<tr height=28 bgcolor=#FFFFFF>\n";
						echo "	<td align=center>".$number."</td>\n";
						echo "	<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>";
						echo "	<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>\n";
						echo "	<A HREF=\"javascript:viewArticle(".$row->num.")\">".$subject."</A>\n";
						echo "	</span>\n";
						echo "	</td>\n";
						echo "	<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>";
						echo "	<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>\n";
						echo "	<a href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" target=_blank>".titleCut(22,$row->productname.($row->selfcode?"-".$row->selfcode:""))."</a>\n";
						echo "	</span>\n";
						echo "	</td>\n";
						echo "	<td align=center>".$a_status."</td>\n";
						echo "	<td align=center>".date("Y-m-d",$row->writetime)."</td>\n";
						echo "</tr>\n";
						$i++;
					}
					pmysql_free_result($result);
					if($i==0) {
						echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
					} else if($i>0) {
						$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
					}
				} else {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
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
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<form name=procForm method=get>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=num>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<?php include("copyright.php"); ?>
