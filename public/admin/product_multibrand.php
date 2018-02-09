<?php
/********************************************************************* 
// 파 일 명		: product_multibrand.php
// 설     명		: 상품별 다중 브랜드 관리
// 상세설명	: 상품별 다중 브랜드 추가, 이동, 삭제
// 작 성 자		: 2016.01.21 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	include("calendar.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "pr-4";
	$MenuCode = "product";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################
//exdebug($_POST);
#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode=$_POST["mode"];
	$gotopage = $_POST["gotopage"];
	$keyword = trim($_POST["keyword"]);
	$code_a=$_REQUEST["code_a"];
	$code_b=$_REQUEST["code_b"];
	$code_c=$_REQUEST["code_c"];
	$code_d=$_REQUEST["code_d"];
	$vender = $_REQUEST["vender"];
    $listnum    = $_REQUEST["listnum"] ?: "20";

	if($keyword=="상품명 상품코드") $keyword="";

	$likecode="";
	if($code_a!="000") $likecode.=$code_a;
	if($code_b!="000") $likecode.=$code_b;
	if($code_c!="000") $likecode.=$code_c;
	if($code_d!="000") $likecode.=$code_d;

	$imagepath=$Dir.DataDir."shopimages/product/";

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	if($mode=="insert" || $mode=="move" || $mode=="del") {				// DB를 수정한다.

		$code_check	= $_POST["code_check"];
		$sel_brand	= $_POST["sel_brand"];
		$sel_mode	= $_POST["sel_mode"];
		//echo count($code_check);

		for($u=0;$u<count($code_check);$u++) {
			
			if ($mode=="insert") {
				list($tot_bp)=pmysql_fetch("SELECT count(*) as tot_bp FROM tblbrandproduct WHERE productcode='".$code_check[$u]."' and bridx='".$sel_brand."'");
				list($sort_max)=pmysql_fetch("SELECT MAX(sort) as sort_max FROM tblbrandproduct WHERE productcode='".$code_check[$u]."' group by productcode");
				if (!$sort_max) $sort_max = 0;
				$sort_max++;
				if ($tot_bp == 0) {
					$sql = "INSERT INTO tblbrandproduct(productcode, bridx, sort) VALUES ('".$code_check[$u]."','".$sel_brand."','".$sort_max."')";			
					pmysql_query($sql,get_db_conn());
				}	
			} else if ($mode=="move") {
				@pmysql_query("UPDATE tblbrandproduct SET bridx = '".$sel_brand."' WHERE productcode = '".$code_check[$u]."' AND sort = 1",get_db_conn());
				@pmysql_query("DELETE FROM tblbrandproduct WHERE productcode='".$code_check[$u]."' and bridx='".$sel_brand."' AND sort > 1",get_db_conn());
			} else if ($mode=='del') {
				$sql = "DELETE FROM tblbrandproduct WHERE productcode='".$code_check[$u]."' and bridx='".$sel_brand."'";
				pmysql_query($sql,get_db_conn());	
			}
		}

		if($mode=="insert") $load_text	= "추가가";
		if($mode=="move") $load_text	= "이동이";
		if($mode=="del") $load_text	= "삭제가";
		echo "<html></head><body onload=\"alert('{$load_text} 완료되었습니다.');parent.location.reload();\"></body></html>";
		exit;
	}



$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
    $sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
            ORDER BY b.brandname ASC
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$brandlist=array();
$sql = "SELECT bridx, brandname FROM tblproductbrand ORDER BY brandname ASC ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$brandlist[]=$row;
}
pmysql_free_result($result);

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

$(document).ready(function(){
	$('.check-all').click(function(){
		$('.code_check').prop('checked', this.checked);
	});
});

<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function CheckForm() {

	if($("input[name=code_check[]]:checked").length<1){
		alert("적용할 상품을 선택해 주시기 바랍니다.");
		return;
	}

	if (document.form1.sel_brand.value == '') {
		alert("적용할 브랜드를 선택해 주시기 바랍니다.");
		return;
	}

	if (document.form1.sel_mode.value == '') {
		alert("적용할 상태를 선택해 주시기 바랍니다.");
		return;
	}

	if(confirm("적용 하시겠습니까?")) {
		document.form1.mode.value=document.form1.sel_mode.value;
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";
	
	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}


function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function listnumSet(listnum){
	document.form1.submit();
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;상품 일괄관리 &gt; <span>상품 노출 브랜드 설정 관리</span></p></div></div>
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
					<div class="title_depth3">상품 노출 브랜드 설정 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰에 등록된 상품이 노출될 브랜드를 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td>
				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>상품검색</span></th>
							<td><input class="input_bd_st01" type="text" name="keyword" onfocus="this.value=''; this.style.color='#000000'; this.style.textAlign='left';" <?=$keyword?"value=".$keyword:"style=\"color:'#bdbdbd';text-align:center;\" value=\"상품명 상품코드\""?>></td>
						</tr>
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
								$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
								$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
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
                            <th><span>브랜드 검색</span></th>
                            <td><select name=vender class="select">
                                <option value="">==== 전체 ====</option>
        <?php
                        foreach($venderlist as $key => $val) {
                            echo "<option value=\"{$val->bridx}\"";
                            if($vender==$val->bridx) echo " selected";
                            echo ">{$val->brandname}</option>\n";
                        }

        ?>
                                </select> 
                            </td>
                        </tr>
					</table>
					<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a></p>
				</div>
				</td>
			</tr>
<?
						$page_numberic_type=1;
						
						if ($likecode){
							$link_qry="select c_productcode from tblproductlink where c_category like '{$likecode}%' group by c_productcode";
							$link_result=pmysql_query($link_qry);
							while($link_data=pmysql_fetch_object($link_result)){
								$linkcode[]=$link_data->c_productcode;
							}
							$qry.= "AND a.productcode in ('".implode("','",$linkcode)."') ";						
						}
						if ($keyword) $qry.= "AND UPPER(productname || productcode) LIKE UPPER('%{$keyword}%') ";		
                        if($vender) $qry.="AND a.brand = '{$vender}' ";				
						
						$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a  WHERE 1=1 ";
						$sql0.= $qry;
						$paging = new newPaging($sql0,10,$listnum);
						$t_count = $paging->t_count;
						$gotopage = $paging->gotopage;
?>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right">
                        <!-- <img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지 -->
                        <select name="listnum" onchange="javascript:listnumSet(this);">
                            <option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
                            <option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
                            <option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
                            <option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
                            <option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
                            <option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
                            <option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
                            <option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
                            <option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
                            <option value="100000" <?if($listnum==100000)echo "selected";?>>전체</option>
                        </select>
                    </td>
				</tr>
				</table>
				</td>
			</tr>

			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
					<div class="table_style02">
					<table width=100% cellpadding=0 cellspacing=0 style='position:relative'>
						<colgroup>
							<?php
							$colspan=5;
							//if($vendercnt>0) $colspan++;
							?>
							<col width=40></col>
							<?php if($vendercnt>0){?>
							<!-- col width=80></col -->
							<?php }?>
							<col width=180></col>
							<col width=40></col>
							<col width=></col>
							<col width=400></col>
						</colgroup>
<?php
						$sql = "SELECT a.option_price, a.productcode, a.productname, a.production, a.sellprice, a.consumerprice, ";
						$sql.= "a.buyprice, a.quantity, a.reserve, a.reservetype, a.addcode, a.display, a.vender, a.tinyimage, a.minimage, a.assembleuse, a.assembleproduct, a.date, a.brand, b.brandname ";
						$sql.= "FROM tblproduct a left join tblproductbrand b on a.vender=b.vender WHERE 1=1 ";
						$sql.= $qry." ";
						$sql.= "ORDER BY date DESC ";
						$sql = $paging->getSql($sql);
						$result = pmysql_query($sql,get_db_conn());
						$cnt=0;
?>
						<tr>
							<th><input type="checkbox" name="check-all" class="check-all"></th>
							<?php if($vendercnt>0){?>
							<!-- th>Vender</th -->
							<?php }?>
							<th>브랜드</th>
							<th colspan=2>상품명</th>
							<th>노출 브랜드</th>							
						</tr>

<?
						while($row=pmysql_fetch_object($result)) {
							$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);


		?>
						<tr>
							<td><input type="checkbox" name="code_check[]"  value="<?=$row->productcode?>" class="code_check"></td>
		<?php
							if($vendercnt>0) {
								//echo "	<td align=\"center\" style=\"font-size:8pt\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a>":"-")."</B></td>\n";
							}					
							echo "<td align=\"center\" style=\"font-size:8pt;padding:2\">".$row->brandname."</td>";
							echo "	<TD>";
							
                            $tinyimage = getProductImage($imagepath, $row->tinyimage );
                            $minimage = getProductImage($imagepath, $row->minimage );
							if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
								echo "<img src='".$imagepath.$row->tinyimage."' style=\"width:50px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							} else if($tinyimage){
								echo "<img src='".$tinyimage."' style=\"width:50px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							} else {
								echo "$row->tinyimage<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							}
							echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
							echo "		<tr bgcolor=\"#FFFFFF\">\n";
							if (ord($row->minimage) && file_exists($imagepath.$row->minimage)){
								echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->minimage."\" border=\"0\"></td>\n";
							} else if($minimage){
								echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$minimage."\" border=\"0\"></td>\n";
							} else {
								echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
							}
							echo "		</tr>\n";
							echo "		</table>\n";
							echo "		</div>\n";
							echo "	</td>\n";
							
							//echo "		<TD style=\"word-break:break-all; text-align:left; padding-left:10px\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
							echo "		<TD style=\"word-break:break-all; text-align:left; padding-left:10px\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
		?>		
							<td style="font-size:8pt;padding:2;text-align:left">
		<?
							$bnSql = "SELECT b.brandname FROM tblbrandproduct a left join tblproductbrand b on a.bridx=b.bridx where a.productcode='".$row->productcode."' ORDER BY b.brandname ASC ";
							$bnRes=pmysql_query($bnSql,get_db_conn());
							$bnCnt	= 1;
							while($bnRow=pmysql_fetch_object($bnRes)) {
								if ($bnCnt > 1) $bnbr	= "<br>";
								else $bnbr	= "";
								echo $bnbr.$bnCnt.". ".$bnRow->brandname;
								$bnCnt++;
							}
							pmysql_free_result($bnRes);
		?>					
							</td>						
		<?
						$cnt++;	
						}
						if ($cnt==0) {
							$page_numberic_type="";
							echo "<tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
						} else {
			
						}
		?>
					</table>
					</div>
					
		<?
					if($page_numberic_type) {
							echo "<div id=\"page_navi01\">";
							echo "<div class=\"page_navi\">";
							echo "<ul>";
							echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
							echo "</ul>";
							echo "</div>";
							echo "</div>";
					}
		?>		
					</td>
				</tr>
		
				</table>
				</td>
			</tr>
			<?if($page_numberic_type) {?>
			<tr>
				<td background="images/counter_blackline_bg.gif"  class="font_white" align="center" height='40'>
				선택한 상품을 브랜드
				<select name=sel_brand class="select">
					<option value="">===========선택===========</option>
<?php
			foreach($brandlist as $key => $val) {
				echo "<option value=\"{$val->bridx}\">{$val->brandname}</option>\n";
			}
?>
				</select> 로 
				<select name=sel_mode class="select">
				<option value="">=선택=</option>							
				<option value="insert">추가</option>						
				<option value="move">이동</option>		
				<option value="del">삭제</option>				
				</select> 합니다.&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm();" style='font-weight:bold;color: #FFDB1A;'>[ 적용하기 ]</a></td>
			</tr>
			<?}?>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</form>
			
			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>

			
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품 노출 브랜드 설정 관리</span></dt>
							<dd>
							- 쇼핑몰에 등록된 상품이 노출될 브랜드를 설정할 수 있습니다.
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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?php
include("copyright.php");
?>
<?=$onload?>