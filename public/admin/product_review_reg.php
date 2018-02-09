<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$category_data=$_REQUEST["category_data"];
if($category_data){
	$arrCategoryData = explode("|", $_REQUEST['category_data']);

	$_REQUEST["code_a"] = $arrCategoryData[0];
	$_REQUEST["code_b"] = $arrCategoryData[1];
	$_REQUEST["code_c"] = $arrCategoryData[2];
	$_REQUEST["code_d"] = $arrCategoryData[3];
}
$s_keyword=trim($_REQUEST["s_keyword"]);
$s_check=$_REQUEST["s_check"];
$display_yn=$_REQUEST["display_yn"];
$vip=$_REQUEST["vip"];
$staff=$_REQUEST["staff"];
$vperiod=$_REQUEST["vperiod"];
$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];
$search_end=$_REQUEST["search_end"];
$search_start=$_REQUEST["search_start"];
$sellprice_min=$_REQUEST["sellprice_min"];
$sellprice_max=$_REQUEST["sellprice_max"];
$sel_vender = $_REQUEST["sel_vender"];
$code_type=$_REQUEST["code_type"];
$code_area=$_REQUEST["code_area"];
if($code_area){
	$s_keyword="";
}
$listnum=(int)$_REQUEST["listnum"];
if(!$listnum){
	//$listnum = (int)$_REQUEST["listnum_select"];
	$listnum = 20;
}
//$gotopage = $_REQUEST["gotopage"];
$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$likecodeExchange = $code_a."|".$code_b."|".$code_c."|".$code_d;

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

if($display_yn==""){
	$display_yn = "all";
}
if($vip==""){
	$vip = "all";
}
if($s_check==""){
	$s_check = "all";
}
if($staff==""){
	$staff = "all";
}
$checked["display_yn"][$display_yn] = "checked";
$checked["vip"][$vip] = "checked";
$checked["staff"][$staff] = "checked";
$checked["s_check"][$s_check] = "checked";
$imagepath=$Dir.DataDir."shopimages/product/";

#리뷰 지우기
//$sql = "DELETE FROM tblproductreview WHERE productcode IN ({$chkPrcode})";
//pmysql_query($sql,get_db_conn());
?>

<?php include("header.php"); ?>

<!--#####################스크립트 영역########################-->
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

$(function(){

	$(".search_list").click(function(){
		var obj =$(this).next().children();
		var chk_display = $(this).next().children().css('display');
		var productcode = $(this).attr('prcode');
		var productname = $(this).attr('prname');
		if(chk_display == 'none'){
			obj.fadeIn();
			$(this).children().css("border-bottom","0");
			$.post('product_review_ajax.php',{productcode:productcode,productname:productname}, function(data){
				if(data){
					obj.html(data);
				}
			});
		}else{
			obj.fadeOut();
			$(this).children().css("border-bottom","1px solid #cbcbcb");
		}
	});

}); //$(function) end 시무룩..

function more(offset,productcode,productname){
	$.post('product_review_ajax.php',{offset:offset,productcode:productcode,productname:productname}, function(data){
		if(data){
			$("#"+productcode).html(data);
		}
	});
}

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function reg_review(prcode){
	window.open('product_review_reg_form.php?productcode='+prcode ,'_blank',"width=400,height=700scrollbars=yes");
}

function ReviewReply(date,prcode) {
	window.open("about:blank","reply","width=400,height=500,scrollbars=no");
	document.replyform.target="reply";
	document.replyform.date.value=date;
	document.replyform.productcode.value=prcode;
	document.replyform.submit();
}

function go_product(productcode){
	window.open('../front/productdetail.php?productcode='+productcode,'_blank');
}

</script>
<!--###########################################################-->

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>상품관리 리스트</span></p></div></div>

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
			<?php include("menu_community.php"); ?>
			</td>
			<td></td>
			<td valign="top">
		<form name=form1 id="frm1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=prcode>
			<input type=hidden name=copy_type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=listnum value="<?=$listnum?>">
			<input type="hidden" name="selectChk" id="selectChk"/>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td>
				<div class="title_depth3">상품별 리뷰 등록</div>

				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>상품검색</span></th>
							<td><input class="w200" type="text" name="s_keyword" value="<?=$s_keyword?>"></td>
						</tr>
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
								$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
								$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
								$i=0;
								$ii=0;
								$iii=0;
								$iiii=0;
								$strcodelist = "";
								$strcodelist.= "<script>\n";
								$result = pmysql_query($sql,get_db_conn());
								$selcode_name="";

								while($row=pmysql_fetch_object($result)) {
									$strcodelist.= "var clist=new CodeList();\n";
									$strcodelist.= "clist.code_a='{$row->code_a}';\n";
									$strcodelist.= "clist.code_b='{$row->code_b}';\n";
									$strcodelist.= "clist.code_c='{$row->code_c}';\n";
									$strcodelist.= "clist.code_d='{$row->code_d}';\n";
									$strcodelist.= "clist.type='{$row->type}';\n";
									$strcodelist.= "clist.code_name='{$row->code_name}';\n";
									if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
										$strcodelist.= "lista[{$i}]=clist;\n";
										$i++;
									}
									if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
										if ($row->code_c=="000" && $row->code_d=="000") {
											$strcodelist.= "listb[{$ii}]=clist;\n";
											$ii++;
										} else if ($row->code_d=="000") {
											$strcodelist.= "listc[{$iii}]=clist;\n";
											$iii++;
										} else if ($row->code_d!="000") {
											$strcodelist.= "listd[{$iiii}]=clist;\n";
											$iiii++;
										}
									}
									$strcodelist.= "clist=null;\n\n";
								}
								pmysql_free_result($result);
								$strcodelist.= "CodeInit();\n";
								$strcodelist.= "</script>\n";

								echo $strcodelist;


								echo "<select name=code_a style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
								echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_b style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
								echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_c style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
								echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_d style=\"width:170px;\">\n";
								echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
				?>
							</td>
						</tr>
						<tr>
							<th><span>등록일</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
						</tr>
						<tr>
							<th><span>상품금액별 검색</span></th>
							<td><input class="input_bd_st01" type="text" name="sellprice_min" value="<?=$sellprice_min?>"/> 원 ~ <input class="input_bd_st01" type="text" name="sellprice_max" value="<?=$sellprice_max?>"/> 원</td>
						</tr>
						<tr>
							<th><span>품절 유무</span></th>
							<td><input type="radio" name="s_check" value="all" <?=$checked["s_check"]['all']?>/>전체 <input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>판매중 <input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절</td>
						</tr>
						<tr>
							<th><span>진열 유무</span></th>
							<td><input type="radio" name="display_yn" value="all" <?=$checked["display_yn"]['all']?>/>전체
							<input type="radio" name="display_yn" value="Y" <?=$checked["display_yn"]['Y']?>/> 진열&nbsp;&nbsp;
							<input type="radio" name="display_yn" value="N" <?=$checked["display_yn"]['N']?>/> 미진열</td>
						</tr>
                       </table>
					<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a></p>
				</div>

				<div class="table_style02">
					<table width=100% cellpadding=0 cellspacing=0 border=0>
						<colgroup>
							
						</colgroup>
						<div class="btn_right">
							<select name="listnum_select" onchange="javascript:listnumSet(this)">
								<option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
								<option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
								<option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
								<option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
								<option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
							</select>
						</div>
						<tr>
							<th width="50px">No</th>
							<th width="100px">이미지</th>
							<th width="150px">상품이름</th>
						</tr>
		<?php
						$page_numberic_type=1;

						if($likecode) $qry= "AND b.c_category LIKE '{$likecode}%' ";
						if($s_keyword) $qry.= "AND lower(productname || productcode) LIKE lower('%{$s_keyword}%') ";
						if($s_check==1)	$qry.="AND quantity > 0 ";
						elseif($s_check==2){
							$qry.=" AND quantity <= 0 ";
						}
						if($display_yn=="Y")	$qry.="AND a.display='Y' ";
						elseif($display_yn=="N")	$qry.="AND a.display='N' ";

						if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','') ";
						//if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}' ";
                        if($sellprice_min) $qry.="AND sellprice >= '{$sellprice_min}' ";
                        if($sellprice_max) $qry.="AND sellprice <= '{$sellprice_max}' ";
                        if($sel_vender) $qry.="AND a.vender = '{$sel_vender}' ";

                        ## jhjeong 2015-06-11
						$sql = "select distinct on (productcode,regdate, pridx) * 
								from
								(
									SELECT	productcode,productname,sellprice,consumerprice, 
                                            buyprice,quantity,reserve,reservetype,addcode,
                                            display,a.vender,c.com_name, minimage, date, modifydate, a.regdate, pridx
									FROM	tblproduct a 
									left join tblproductlink b on (a.productcode=b.c_productcode) 
                                    left join tblvenderinfo c on (a.vender = c.vender)
									WHERE 1=1 
									".$qry."
								) v
								";
             
						$sql0 = "SELECT COUNT(*) as t_count FROM (".$sql.") a  WHERE 1=1 ";
						if(!$listnum){
							$listnum = 20;
						}
                      
						$paging = new newPaging($sql0,10,$listnum);
						$t_count = $paging->t_count;
						$gotopage = $paging->gotopage;

						$sql.= "ORDER BY v.regdate DESC, pridx ASC ";
						$sql = $paging->getSql($sql);
						$result = pmysql_query($sql,get_db_conn());
						$cnt=0;
						while($row=pmysql_fetch_object($result)) {
						$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
						?>
						<tr style="cursor:pointer;" class="search_list" prcode='<?=$row->productcode?>' prname='<?=$row->productname?>'>
						<td><?=$number?></td>
						<!--이미지-->
						<td>
                            <?	if (ord($row->minimage) && file_exists($imagepath.$row->minimage)){ ?>
								<img src="<?=$imagepath.$row->minimage."?v".date("His")?>" style="width:100px" border=1>
                            <?} else if(ord($row->minimage) && file_exists($Dir.$row->minimage)) { ?>
								<img src="<?=$Dir.$row->minimage."?v".date("His")?>" style="width:100px" border=1>
                            <?} else { ?>
								<img src='images/space01.gif' style="width:100px" border=1>
                            <?} ?>
						</td>
						<td>
							<?=$row->productname?>
						</td>
						</tr>
						
						<tr>
							<td style="display:none;" class="search_detail" colspan=3 id="<?=$row->productcode?>">
							</td>
						</tr>
						<?
						$cnt++;
						} //while end ㅇㅇ

						if ($cnt==0) {
							$colspan='16';
							$page_numberic_type="";
							echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
						}?>
				</table>
			</div>

			<!--페이징-->
			<div id="page_navi01" style="height:'40px'">
				<div class="page_navi">
				<?if($page_numberic_type){?>
					<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
				<?}?>
				</div>
			</div>

			<!--하단 버튼-->
			<div class="btn_right">
				
			</div>


        	<table height="20"><tr><td> </td></tr></table>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>내용을 기입하세요</span></dt>
						<dd>
						
						</dd>
					</dl>
				</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<form name=replyform action="product_reviewreply.php" method=post>
<input type=hidden name=date>
<input type=hidden name=productcode>
</form>
<?php
include("copyright.php");
?>
<?=$onload?>