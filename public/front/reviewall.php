<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


if ($_data->ETCTYPE["REVIEW"]!="Y") {
	alert_go('사용후기 모음 게시판을 이용할 수 없습니다.',"{$Dir}main/main.php");
}

$imagepath=$Dir.DataDir."shopimages/etc/main_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/main_logo.swf";

if (file_exists($imagepath)) {
	$mainimg="<img src=\"".$imagepath."\" border=\"0\" align=\"absmiddle\">";
} else {
	$mainimg="";
}
if (file_exists($flashpath)) {
	if (preg_match("/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/",$_data->shop_intro,$match)) {
		$width=$match[1];
		$height=$match[2];
	}
	$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
} else {
	$mainflash="";
}
$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/");
$replace=array($Dir,$mainimg,$mainflash);
$shop_intro=preg_replace($pattern,$replace,$_data->shop_intro);


$mb_qry="select * from tblmainbannerimg order by banner_sort";


if (stripos($shop_intro,"<table")!==false || strlen($mainflash)>0)
	$main_banner=$shop_intro;
else
	$main_banner=nl2br($shop_intro);

$likecode = $_POST["code"];
$search_word = $_POST["search_word"];
$search_select = $_POST["search_select"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 사용후기</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function select_code(code1) {
	var code2 = code1
	document.form1.code.value="00400"+code2;
	if(code2==null){
		document.form1.code.value="004";
	}
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
//-->
</SCRIPT>

</HEAD>

<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?	$board = "review";
	$lnb_flag = 5;
	$class_on['reviewall'] ="class='on'";
?>
<?   $qry = " WHERE 1=1 ";
					if(ord($likecode)) {
						$qry.= " AND a.productcode = c.c_productcode AND c.c_category LIKE '{$likecode}%' ";
					}
					$qry.= "AND a.productcode=b.productcode ";
					if($_data->review_type=="A") $qry.= "AND a.display='Y' ";
					$qry.= "AND b.display='Y' ";
				
					$sql = "SELECT a.upfile,a.num,a.id,a.name,a.marks,a.date,a.content,a.subject, b.productcode,b.productname,b.quantity,b.selfcode,b.minimage ";
					$sql.= "FROM tblproductreview a, tblproduct b";
					if(ord($likecode)) {
						$sql.= ", tblproductlink c ";
					}
					$sql.= $qry;
					if($search_select!=""){
						if($search_select==0){
							$sql.="AND b.productname LIKE '%".$search_word."%' ";
						}else if($search_select==1){
							$sql.="AND a.name LIKE '%".$search_word."%' ";
						}
					}
					if($sort==0) $sql.= "ORDER BY a.date DESC ";
					else if($sort==1) $sql.= "ORDER BY marks DESC ";
					$paging = new Tem001_saveheels_Paging($sql,10,8,'GoPage',true);
					$t_count = $paging->t_count;
					$gotopage = $paging->gotopage;
					$sql = $paging->getSql($sql);
					$result=pmysql_query($sql);
					//exdebug($sql);
				?>
<div class="containerBody sub_skin">
	<? include ($Dir.MainDir."lnb.php"); ?>
	<div class="right_section">
		
	
	<h3 class="title">
		REVIEW
		<p class="line_map"><a>홈</a> &gt; <a>Community</a> &gt; <a>REVIEW</a></p>
	</h3>

	<div class="table_wrap mt_20">
		TOTAL(<?=$t_count?>)
		<div class="right_area">
			<div class="search">
					<select class="type01" name="search_select">
						<option value="" <?if($search_select==""){ echo "selected=\"selected\"";$search_word="";}?>>선택하세요</option>
						<option value="0" <?if($search_select==0) echo "selected=\"selected\"";?>>상품명</option>
						<option value="1" <?if($search_select==1) echo "selected=\"selected\"";?>>등록자</option>
					</select>
					<input class="type01" type="text" name="search_word" value="<?=$search_word?>"/>
					<a href="javascript:document.form1.submit()"><img src="../img/button/customer_notice_list_search_btn.gif" alt="" /></a>
				</div>
		</div>		
	</div>

	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>	
		<!-- 리뷰리스트 -->
		<div class="brand_review_list">
			<table class="th_top" width="100%">
				<colgroup>
					<col style="width:50px" /><col style="width:140px" /><col style="width:auto" /><col style="width:120px" /><col style="width:120px" />
				</colgroup>
				<tr>
					<th>번호</th>
					<th>사진</th>
					<th>제목</th>
					<th>작성자</th>
					<th>작성일</th>
				</tr>
				<?php
					$i=0;
					while($row=pmysql_fetch_object($result)) {
					if($row){						
					$date=substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				?>
				<tr>
					<td><?=$number?></td>
					<td class="review_img">
						<? 	if(ord($brow->upfile) && file_exists($Dir.DataDir."shopimages/board/reviewbbs/".$brow->upfile)) {
								echo "<a href=\"review_view.php?num=<?=$row->num?>\">";
								echo "<img src=\"".$Dir.DataDir."shopimages/board/reviewbbs/{$brow->upfile}\" border=0 \">";
								echo "</a>";
								}
							else {
								echo "<img src=\"{$Dir}images/no_img.gif\" border=0 width=$imgwidth>";
								} ?>
					</td>
					<td class="ta_l"><a href="review_view.php?num=<?=$row->num?>"><?=$row->subject?></a></td>
					<td><?=substr($row->id,0,2)?>***</td>
					<td><?=$date?></td>
				</tr>	
			<?
				}
				$i++;
			}?>
			</table>
			<div class="paging bottom"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></div>
		</div><!-- //리뷰리스트 -->
		
	<input type=hidden name=code value="<?=$likecode?>">
	</form>
	</table>
	</div>
</div>

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=code value="<?=$likecode?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>
<script>
$(function() {
	var brandcode = "logo0"+document.form1.code.value.substr(5,1);
	document.getElementById(brandcode).className = document.getElementById(brandcode).className+" on";
});
</script>
</BODY>
</HTML>
