<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/venderlib.php");


include("access.php");
$mode=$_POST["mode"];
$prcodes=$_POST["prcodes"];
$display=$_POST["display"];

$code=$_POST["code"];
$listnum=$_POST["listnum"]?$_POST["listnum"]:'10';
$disptype=$_POST["disptype"];
$s_check=$_POST["s_check"];
if(strlen($s_check)==0) $s_check="name";
$search=ltrim($_POST["search"]);
$sort=$_POST["sort"];
if($sort!="order by productname asc" && $sort!="order by productname desc" && $sort!="order by productcode asc" && $sort!="order by productcode desc" && $sort!="order by sellprice asc" && $sort!="order by sellprice desc" && $sort!="order by regdate asc" && $sort!="order by regdate desc") {
	$sort="order by regdate desc";
}


$qry = "WHERE 1=1 ";
if(strlen($code)>=3) {
	$qry.= "AND c_category LIKE '".$code."%' ";
}

$qry.= "AND vender='".$_VenderInfo->getVidx()."' ";

if($disptype=="Y") $qry.= "AND display='Y' ";
else if($disptype=="N") $qry.= "AND display='N' ";
if(strlen($search)>0) {
	if($s_check=="name") $qry.= "AND UPPER( productname ) LIKE UPPER( '%".$search."%' ) ";
	else if($s_check=="code") $qry.= "AND productcode='".$search."' ";
}

$sql = "SELECT COUNT(*) as t_count FROM (select a.*, b.c_category FROM tblproduct a left join tblproductlink b on ( a.productcode=b.c_productcode  AND b.c_maincate = '1' ) ) tpd ".$qry." ";
//echo $sql;

$paging = new Paging($sql,10,$listnum);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
//exdebug( $_REQUEST );
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script src="/js/jquery-1.12.1.min.js"></script>
<script language="JavaScript">
$(document).ready(function(){
	
	$("#loginfo").css("display","none");
	
	$("#chk_confirm").click(function(){
		var input_r_prodcut = "";
		var new_count=0;
		var current_count = $("#r_product_list",opener.document).children().length;

		$("input[name='productcode']").each(function(i){
			;
			if($(this).prop('checked')){
				console.log("ok2");
				var arrThisValue = $(this).val().split("||||");
				console.log(arrThisValue);
				var inProduct	= 0;

				input_r_prodcut += "<li style='height:22px;'><input type='hidden' name='r_product[]' value='"+arrThisValue+"'>"+arrThisValue[1]+"<img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' class='del_rproduct'></li>";
				$(this).removeAttr('checked');
				new_count = i+1;
			}
		})
			
		
		if( (new_count+current_count) >10 ){
			alert("관련상품은 10개까지만 등록 가능 합니다(현재 선택된 관련상품갯수:"+current_count);
		}else{
			$("#r_product_list", opener.document).append(input_r_prodcut);
			window.close();
		}
		
	});

	$(".CLS_allCheck").click(function(){
		if($(this).prop("checked")){
			$("input[name='productcode']").prop("checked", true);
		}else{
			$("input[name='productcode']").prop("checked", false);
		}
	});

});

function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "product_myprd.ctgr.php?code="+code+"&depth=2";
	surl = "product_myprd.ctgr.php?depth=3";
	durl = "product_myprd.ctgr.php?depth=4";
	BCodeCtgr.location.href = murl;
	CCodeCtgr.location.href = surl;
	DCodeCtgr.location.href = durl;
}

function SearchPrd() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function OrderSort(sort) {
	document.pageForm.block.value="";
	document.pageForm.gotopage.value="";
	document.pageForm.sort.value=sort;
	document.pageForm.submit();
}

function GoPrdinfo(prcode,target) {
	document.form3.target="";
	document.form3.prcode.value=prcode;
	if(target.length>0) {
		document.form3.target=target;
	}
	document.form3.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=0;i<cnt;i++){
      document.form2.productcode[i].checked=chkval;
   }
}

function listnumSet(listnum){
	document.sForm.listnum.value=listnum.value;
	document.sForm.submit();
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
	<td width=175 valign=top nowrap></td>
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
					<FONT COLOR="#ffffff"><B>관련상품 선택<B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>관련상품 선택</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 카테고리 분류/상품명 검색으로 관련상품을 선택하여 진열 합니다.</td>
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
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<form name="sForm" method="post">
						<input type="hidden" name="code" value="<?=$code?>">
						<input type="hidden" name="listnum" value="<?=$listnum?>">
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<!-- <col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col> -->
							<tr>
								<td style="width:170px;">
								<select name="code1" style=width:155 onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
								<option value="">------ 대 분 류 ------</option>
<?
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
									echo $sql;
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
								
								<td style="width:170px;">
								<iframe name="BCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>&depth=2" width="155" height="21" scrolling=no frameborder=no></iframe>
								</td>
								
								<td style="width:170px;"><iframe name="CCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,6)?>&select_code=<?=$code?>&depth=3" width="155" height="21" scrolling=no frameborder=no></iframe></td>
								
								<td style="width:170px;"><iframe name="DCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,9)?>&select_code=<?=$code?>&depth=4" width="155" height="21" scrolling=no frameborder=no></iframe></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<!-- <col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col> -->
							<tr>
								<td style="width:170px;">
								<select name=disptype  style=width:155 >
								<option value="">진열/대기상품 전체</option>
								<option value="Y" <?if($disptype=="Y")echo"selected";?>>진열상품만 검색</option>
								<option value="N" <?if($disptype=="N")echo"selected";?>>대기상품만 검색</option>
								</select>
								</td>

							

								<td style="width:170px;">
								<select name="s_check"  style=width:155 >
								<option value="name" <?if($s_check=="name")echo"selected";?>>상품명으로 검색</option>
								<option value="code" <?if($s_check=="code")echo"selected";?>>상품코드로 검색</option>
								</select>
								</td>

							

								<td style="width:170px;"><input type=text name=search value="<?=$search?>"  style=width:155 ></td>

								

								<td style="width:170px;"><A HREF="javascript:SearchPrd()"><img src=images/btn_inquery03.gif border=0></A></td>
							</tr>
							</table>
							</td>
						</tr>

						</form>

						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<!--
				<tr><td height=20></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<col width=150></col>
					<col width=></col>
					<col width=100></col>
					<tr style="display:none;">
						<td valign=top><img src=images/btn_exceldown.gif border=0 style="cursor:hand" onclick="excelDown()"></td>
						<td align=right valign=top>
						<?if($_venderdata->grant_product[1]=="Y" && $_venderdata->grant_product[3]=="N") {?>
						<img src=images/btn_prddispon.gif border=0 style="cursor:hand" onclick="setPrdDisplaytype('','Y')">
						<img src=images/btn_prddispoff.gif border=0 style="cursor:hand" onclick="setPrdDisplaytype('','N')">
						<?}?>
						<?if($_venderdata->grant_product[2]=="Y") {?>
						<img src=images/btn_prddel.gif border=0 style="cursor:hand" onclick="DeletePrd('')">
						<?}?>
						</td>
						<td align=right valign=top>
						<select name="listnum_select" onchange="javascript:listnumSet(this)">
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
				-->
				<tr><td height=3></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td bgcolor=E7E7E7>
					<table width=100% border=0 cellspacing=1 cellpadding=0 style="table-layout:fixed">
					<col width=30></col>
					<col width=40></col>
					<col width=120></col>
					<col width=></col>
					<col width=60></col>
					<col width=70></col>
                    <!-- <col width=60></col> -->
					<col width=60></col>

					<form name=form2 method=post>
					<input type=hidden name=chkprcode>

					<tr height=35 align=center bgcolor=F5F5F5>
						<td align=center><input type=checkbox name=allcheck class="CLS_allCheck"></td>
						<td align=center><B>번호</B></td>
						<td align=center><a href="javascript:OrderSort('<?=($sort=="order by productcode asc"?"order by productcode desc":"order by productcode asc")?>')"; onMouseover="self.status=''; return true; "><B>상품코드</B></a></td>
						<td align=center><a href="javascript:OrderSort('<?=($sort=="order by productname asc"?"order by productname desc":"order by productname asc")?>')"; onMouseover="self.status=''; return true; "><B>상품명</B></a></td>
						<td align=center><a href="javascript:OrderSort('<?=($sort=="order by sellprice asc"?"order by sellprice desc":"order by sellprice asc")?>')"; onMouseover="self.status=''; return true; "><B>가격</B></a></td>
						<td align=center><a href="javascript:OrderSort('<?=($sort=="order by regdate asc"?"order by regdate desc":"order by regdate asc")?>')"; onMouseover="self.status=''; return true; "><B>등록일</B></a></td>
                        <!-- <td align=center><B>비고</B></td> -->
						<td align=center><B>상품진열</B></td>
					</tr>
<?php
					$colspan=7;
					$cnt=0;
					if($t_count>0) {
						$sql = "SELECT productcode,productname,sellprice,regdate,display,selfcode FROM (select a.*, b.c_category FROM tblproduct a left join tblproductlink b on ( a.productcode=b.c_productcode  AND b.c_maincate = '1' )  ) tpd ".$qry." ".$sort." ";
						$sql = $paging->getSql($sql);
						//exdebug($sql);
						$result=pmysql_query($sql,get_db_conn());
						$i=0;
						while($row=pmysql_fetch_object($result)) {
							$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
							echo "<tr height=30 bgcolor=#FFFFFF>\n";
							echo "	<td align=center><input type = 'checkbox' name = 'productcode' value = '".$row->productcode."||||".$row->productname."'></td>\n";
							echo "	<td align=center style=\"font-size:8pt\">".$number."</td>\n";
							echo "	<td align=center style=\"font-size:8pt\"><a href=\"/front/productdetail.php?productcode=".$row->productcode."\" target=\"_blank\">".$row->productcode."</a></td>\n";
							echo "	<td style='font-size:8pt;line-height:11pt;padding-left:5;padding-right:5'><A HREF=\"javascript:GoPrdinfo('".$row->productcode."','')\">".titleCut(45,$row->productname.($row->selfcode?"-".$row->selfcode:""))."</A> <A HREF=\"javascript:GoPrdinfo('".$row->productcode."','_blank')\"><img src=images/newwindow.gif border=0 align=absmiddle></A></td>\n";
							echo "	<td align=right style=font-size:8pt;padding-right:5>".number_format($row->sellprice)."</td>\n";
							echo "	<td align=center style=\"font-size:8pt\">".substr($row->regdate,0,10)."</td>\n";
                            //echo "  <td align=center><a href=\"javascript:go_copy('".$row->productcode."');\"><img src='images/btn_cate_copy.gif'></a></td>\n";
							echo "	<td align=center>";
							if($_venderdata->grant_product[1]=="Y" && $_venderdata->grant_product[3]=="N") {
								if($row->display=="Y") {
									echo "<img src=images/icon_on.gif border=0 style=\"cursor:hand\" onclick=\"setPrdDisplaytype('".$row->productcode."','N')\">";
								} else {
									echo "<img src=images/icon_off.gif border=0 style=\"cursor:hand\" onclick=\"setPrdDisplaytype('".$row->productcode."','Y')\">";
								}
							} else {
								if($row->display=="Y") {
									echo "<img src=images/icon_on.gif border=0>";
								} else {
									echo "<img src=images/icon_off.gif border=0>";
								}
							}
							echo "	</td>\n";
                           
							echo "</tr>\n";
							$i++;
						}
						pmysql_free_result($result);
						$cnt=$i;
						if($i>0) {
							$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
						}
					} else {
						echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
					}
?>
					<input type=hidden name=tot value="<?=$cnt?>">
					</form>

					</table>
					</td>
				</tr>
				<tr><td height=10></td></tr>
				<tr>
					<td align=center>
					<form name="pageForm" method="post">
					<input type=hidden name='code' value='<?=$code?>'>
					<input type=hidden name='listnum' value='<?=$listnum?>'>
					<input type=hidden name='disptype' value='<?=$disptype?>'>
					<input type=hidden name='s_check' value='<?=$s_check?>'>
					<input type=hidden name='search' value='<?=$search?>'>
					<input type=hidden name='sort' value='<?=$sort?>'>
					<input type=hidden name='block' value='<?=$block?>'>
					<input type=hidden name='gotopage' value='<?=$gotopage?>'>
					</form>

					<?=$pageing?>

					</td>
				</tr>
				<tr>
					<td height=10></td>
				</tr>
				<tr>
					<td align=center><img src="images/btn_registered.gif" id="chk_confirm"></td>
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


<form name=etcform method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=prcodes>
<input type=hidden name=display>
</form>

<form name=form3 method=post action="product_prdmodify.php">
<input type=hidden name=prcode>

<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=code1 value="<?=$code1?>">
<input type=hidden name=disptype value="<?=$disptype?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=sort value="<?=$sort?>">
</form>

<form name='copy_form' method='POST' action='<?=$_SERVER[PHP_SELF]?>' >
<input type='hidden' name='prcode' value='' >
<input type='hidden' name='mode' value='' >
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<?php include("copyright.php"); ?>
