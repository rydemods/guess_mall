<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$_ShopData=new ShopData($_ShopInfo);
$_ShopData=$_ShopData->shopdata;
$regdate = $_ShopData->regdate;

$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

$orderby=$_POST["orderby"];
if(strlen($orderby)==0) $orderby="DESC";

$paystate=$_POST["paystate"];
$sel_code = $_POST["sel_code"];
$pickup_type=$_POST["pickup_type"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];

$search_start=$search_start?$search_start:$period[2];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$qry = "WHERE toc.pickup_state in ('R','Y') AND toc.restore ='N' ";
if ($search_s != "" || $search_e != "") { 
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND toc.regdt LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND toc.regdt>='{$search_s}' AND toc.regdt <='{$search_e}' ";
	}
}

if(ord($sel_code)) $qry.= "AND toc.code='{$sel_code}' ";

//처리여부
if(ord($pickup_type)) {
	if ($pickup_type == "N") $qry.= "AND coalesce(toc.pickup_date,'')='' ";
	if ($pickup_type == "Y") $qry.= "AND coalesce(toc.pickup_date,'')!='' ";
}
// 검색어
if(strlen($search)>0) {
	if($s_check=="cd") $qry.= "AND a.ordercode='".$search."' ";
	else if($s_check=="pn") $qry.= "AND b.productname LIKE '".$search."%' ";
	else if($s_check=="mn") $qry.= "AND a.sender_name='".$search."' ";
	else if($s_check=="mi") $qry.= "AND a.id='".$search."' ";
	else if($s_check=="cn") $qry.= "AND a.id='".$search."X' ";
}

$qry_from  = " tblorder_cancel toc ";
$qry_from .= "join tblorderinfo a on toc.ordercode = a.ordercode ";
$qry_from .= "join (Select p.oc_no, pb.brandname, p.vender from tblorderproduct p left join tblvenderinfo v on p.vender = v.vender left join tblproductbrand pb on p.vender=pb.vender where p.oc_no > 0 and p.redelivery_type = 'G' and p.op_step != '40' group by p.oc_no, p.vender, pb.brandname) b on toc.oc_no=b.oc_no ";
$qry_from .= "AND b.vender='".$_VenderInfo->getVidx()."' ";


$setup[page_num] = 10;
$setup[list_num] = 10;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (empty($gotopage)) {
	$gotopage = 1;
}

$sql = "SELECT COUNT(*) as t_count FROM {$qry_from} {$qry} ";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$t_count = $row->t_count;
pmysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function OnChangePeriod(val) {
	var pForm = document.sForm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function searchForm() {
	document.sForm.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","vorderdetail","scrollbars=yes,width=1000,height=600");
	document.detailform.submit();
}

function searchSender(name) {
	document.sForm.s_check.value="mn";
	document.sForm.search.value=name;
	document.sForm.submit();
}

function searchId(id) {
	document.sForm.s_check.value="mi";
	document.sForm.search.value=id;
	document.sForm.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chk_oc_no[i].checked=chkval;
   }
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function GoOrderby(orderby) {
	document.pageForm.block.value = "";
	document.pageForm.gotopage.value = "";
	document.pageForm.orderby.value = orderby;
	document.pageForm.submit();
}

function OrderCheckRechange() {
	document.exeform.oc_no.value="";
	document.exeform.ordercode.value="";
	document.exeform.idxs.value="";
	var k = 0;
	for(i=1;i<document.form2.chk_oc_no.length;i++) {
		if(document.form2.chk_oc_no[i].checked) {
			if (k == 0)
			{
				document.exeform.oc_no.value+=document.form2.chk_oc_no[i].value;
				document.exeform.ordercode.value+=document.form2.ordercode[i].value;
				document.exeform.idxs.value+=document.form2.idxs[i].value;
			} else {
				document.exeform.oc_no.value+=","+document.form2.chk_oc_no[i].value;
				document.exeform.ordercode.value+=","+document.form2.ordercode[i].value;
				document.exeform.idxs.value+=","+document.form2.idxs[i].value;
			}
			k++;
		}
	}

	if(document.exeform.oc_no.value.length==0) {
		alert("선택하신 교환건이 없습니다.");
		return;
	}
	//alert(document.exeform.oc_no.value);
	//alert(document.exeform.ordercode.value);
	//alert(document.exeform.idxs.value);
	//return;
	if(confirm("교환처리 하시겠습니까?")) {
		document.exeform.mode.value="rechange";
		document.exeform.target="processFrame";
		document.exeform.submit();
	}
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
					<FONT COLOR="#ffffff"><B>주문교환</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>주문교환</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사에서 등록한 상품만 교환관리 할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 주문일자 클릭시 상품에 대한 모든 정보를 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 본사 쇼핑몰에서  상태변경시 입점사 주문조회 상태값이 자동 연동됩니다.</td>
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
						<tr>
							<td>
							&nbsp;<U>기간선택</U>&nbsp; <input type=text name=search_start value="<?=$search_start?>" size=13 onclick=Calendar(event) style="text-align:center;font-size:8pt"> ~ <input type=text name=search_end value="<?=$search_end?>" size=13 onclick=Calendar(event) style="text-align:center;font-size:8pt">
							&nbsp;
							<img src=images/btn_dayall.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							&nbsp;&nbsp;&nbsp;
							<U>교환사유</U>&nbsp;
							<select name="sel_code" style="font-size:8pt">
							<option value="">전체</option>
<?
							foreach($oc_code as $key => $val) {
								echo "<option value=\"{$key}\"";
								if($sel_code==$key) echo " selected";
								echo ">{$val}</option>\n";
							}
?>
							</select>
							</td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td>
							&nbsp;<U>처리여부</U>&nbsp;
							<select name="pickup_type"  style="font-size:8pt">
                              <option value="" <?if($pickup_type=='')echo"selected";?>>전체</option>
                                <option value="N" <?if($pickup_type=='N')echo"selected";?>>대기</option>
                                <option value="Y" <?if($pickup_type=='Y')echo"selected";?>>완료</option>
							</select>
							&nbsp;&nbsp;&nbsp;
							<U>검색어</U>&nbsp;&nbsp;&nbsp;&nbsp;
							<select name=s_check style="font-size:8pt;width:94px">
							<option value="cd" <?if($s_check=="cd")echo"selected";?>>주문코드</option>
							<option value="pn" <?if($s_check=="pn")echo"selected";?>>상품명</option>
							<option value="mn" <?if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:183">
							&nbsp;
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
				<col width=130></col>
				<col width=200></col>
				<col width=></col>
				<tr><td colspan=3 height=20></td></tr>
				<tr>
					<td>
					<A HREF="javascript:OrderCheckRechange()"><img src=images/btn_rechange.gif border=0 align=absmiddle style='padding-bottom:4px'></A>
					</td>
					<td valign=bottom style="padding-left:20">
					<B>정렬 :</B> 
					<?if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');" style="color:blue"><B>요청일자순<FONT COLOR="red">↑</FONT></B></A>
					<?}else{?>
					<A HREF="javascript:GoOrderby('DESC');" style="color:blue"><B>요청일자순<FONT COLOR="red">↓</FONT></B></A>
					<?}?>
					</td>
					<td align=right valign=bottom>
					총 주문수 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;
					현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></B> 페이지
					</td>
				</tr>
				<tr><td colspan=3 height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<!-- 선택,번호,상품명,결제일,주문자,ID/주문번호,수량,판매금액,결제상태,처리여부 -->
				<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<col width=25></col>
				<col width=70></col>
				<col width=70></col>
				<col width=120></col>
				<col width=></col>
				<tr height=32 align=center bgcolor=F5F5F5>
					<input type=hidden name=chk_oc_no>
					<input type=hidden name=ordercode>
					<input type=hidden name=idxs>
					<td><!-- input type=checkbox name=allcheck onclick="CheckAll()" --></td>
					<td><B>요청일자</B></td>
					<td><B>주문일자</B></td>
					<td><B>주문자 정보</B></td><!-- 주문자, ID, 주문번호 -->
					<td><B>교환사유</B></td>
				</tr>
<?
				$colspan=5;

				$sql = "SELECT  toc.*, a.id, a.sender_name, a.paymethod ";
				$sql.= "FROM {$qry_from} {$qry} ";
				$sql.= "ORDER BY toc.oc_no {$orderby} ";
				$sql.= "LIMIT " . $setup[list_num]." OFFSET ".($setup[list_num] * ($gotopage - 1));
				//echo $sql;
				$result=pmysql_query($sql,get_db_conn());
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
					$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
					$regdate = substr($row->regdt,0,4)."/".substr($row->regdt,4,2)."/".substr($row->regdt,6,2)." (".substr($row->regdt,8,2).":".substr($row->regdt,10,2).")";
					$name=$row->sender_name;
					$stridX='';
					$stridM='';
					if($row->ordercode[20]=="X") {	//비회원
						$stridX = substr($row->id,1,6);
					} else {	//회원
						$stridM = "<A HREF=\"javascript:searchId('".$row->id."');\"><FONT COLOR=\"blue\">".$row->id."</FONT></A>";
					}

					if ($row->pickup_date) {
						 $chk_dis	= " disabled=true"; 
						 $pickup_status	= "완료"; 
					} else {
						 $chk_dis	= ""; 
						 $pickup_status	= "대기"; 
					}

					echo "<tr bgcolor=#FFFFFF>\n";
					echo "	<td align=center rowspan=2 valign=top><input type=checkbox name=chk_oc_no value=\"".$row->oc_no."\"".$chk_dis."><input type=hidden name=\"ordercode\" value=\"".$row->ordercode."\"></td>\n";
					echo "	<td align=center style=\"padding:3;line-height:11pt\">".$regdate."</td>\n";
					echo "	<td align=center style=\"padding:3;line-height:11pt\"><A HREF=\"javascript:OrderDetailView('".$row->ordercode."')\">".$date."</A></td>\n";
					echo "	<td style=\"padding:3;line-height:11pt\">\n";
					echo "	주문자 : <A HREF=\"javascript:searchSender('".$name."');\"><FONT COLOR=\"blue\">".$name."</font></A>";
					if(strlen($stridX)>0) {
						echo "<br> 주문번호 : ".$stridX;
					} else if(strlen($stridM)>0) {
						echo "<br> 아이디 : ".$stridM;
					}
					echo "	</td>\n";
					echo "	<td align=center style=\"padding:3;line-height:12pt\">";
					echo $oc_code[$row->code];
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr bgcolor=#FFFFFF>\n";
					echo "	<td colspan=4>\n";
					echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% hieght=100% style=\"table-layout:fixed\">\n";
					echo "	<col width=></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=203></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=35></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=60></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=90></col>\n";
					$sql = "SELECT * FROM tblorderproduct  WHERE oc_no='{$row->oc_no}' AND ordercode='".$row->ordercode."' order by vender, idx";
					$result2=pmysql_query($sql,get_db_conn());
					$jj=0;
					$idxs	= "";
					while($row2=pmysql_fetch_object($result2)) {
						if ($idxs == "") {
							$idxs	.= $row2->idx;
						} else {
							$idxs	.= "|".$row2->idx;
						}

						$oc_opt_name	= "";
						if( strlen( trim( $row2->opt1_name ) ) > 0 ) {
							
							$oc_opt1_name_arr	= explode("@#", $row2->opt1_name);
							$oc_opt2_name_arr	= explode(chr(30), $row2->opt2_name);
							$s_cnt	= 0;
							for($s=0;$s < sizeof($oc_opt1_name_arr);$s++) {
								if ($oc_opt2_name_arr[$s]) {
									if ($s_cnt > 0) $oc_opt_name	.= " / ";
									$oc_opt_name	.= $oc_opt1_name_arr[$s].' : '.$oc_opt2_name_arr[$s];
									$s_cnt++;
								}
							}
						}

						if( strlen( trim( $row2->text_opt_subject ) ) > 0 ) {
							$oc_text_opt_subject_arr	= explode("@#", $row2->text_opt_subject);
							$oc_text_opt_content_arr	= explode("@#", $row2->text_opt_content);

							for($s=0;$s < sizeof($oc_text_opt_subject_arr);$s++) {
								if ($oc_text_opt_content_arr[$s]) {
									if ($oc_opt_name != '') $oc_opt_name	.= " / ";
									$oc_opt_name	.= $oc_text_opt_subject_arr[$s].' : '.$oc_text_opt_content_arr[$s];
								}
							}
						}


						$oc_opt_name_chn	= "";

						if( strlen( $row2->opt1_change ) > 0 ) {
							$oc_optc1_name_arr	= explode("@#", $row2->opt1_change);
							$oc_optc2_name_arr	= explode(chr(30), $row2->opt2_change);
							for($ss=0;$ss < sizeof($oc_optc1_name_arr);$ss++) {
								if ($ss > 0) $oc_opt_name_chn	.= " / ";
								$oc_opt_name_chn	.= $oc_optc1_name_arr[$ss].' : '.$oc_optc2_name_arr[$ss];
							}
						}	

						if( strlen( trim( $row2->text_opt_subject_change ) ) > 0 ) {
							$oc_text_opt_subject_arr	= explode("@#", $row2->text_opt_subject_change);
							$oc_text_opt_content_arr	= explode("@#", $row2->text_opt_content_change);

							for($s=0;$s < sizeof($oc_text_opt_subject_arr);$s++) {
								if ($oc_text_opt_content_arr[$s]) {
									if ($oc_opt_name_chn != '') $oc_opt_name_chn	.= " / ";
									$oc_opt_name_chn	.= $oc_text_opt_subject_arr[$s].' : '.$oc_text_opt_content_arr[$s];
								}
							}
						}
						if ($oc_opt_name_chn) {
							$oc_opt_name_chn	= "<font color='#0074BA'>".$oc_opt_name_chn."</font>";	
						}
						
						$opt_price_chn	= 0;
						$tot_opt_price_chn_text	= "";
						$tot_price_chn_text	= "";

						if ($row2->option_price_text_change) {
							$optc_arr	= explode("||", $row2->option_price_text_change);
							for($i-0;$i < count($optc_arr);$i++) {
								$opt_price_chn = $opt_price_chn + $optc_arr[$i];
							}

							$tot_opt_price_chn	= $opt_price_chn * $row2->option_quantity;

							//if ($tot_opt_price_chn > 0 && $tot_opt_price_chn != $row2->sum_opt_price) {
							//}
						}

						$tot_opt_price_chn_text		= "<font color='#0074BA'>".number_format($tot_opt_price_chn)."</font>)";
						$tot_price_chn_text				= "<font color='#0074BA'>".number_format((($row2->price + $opt_price_chn) * $row2->option_quantity) + $row2->deli_price)."</font>";

						if($tot_opt_price_chn_text =='') $tot_opt_price_chn_text = "<center>-</center>";
						if($tot_price_chn_text =='') $tot_price_chn_text = "<center>-</center>";
						if($oc_opt_name =='') $oc_opt_name = "<center>-</center>";
						if($oc_opt_name_chn =='') $oc_opt_name_chn = "<center>-</center>";

						if($jj>0) echo "<tr><td colspan=9 height=1 bgcolor=#E7E7E7></tr>";
						echo "<tr>\n";
						echo "	<td style=\"padding:3;line-height:11pt\" rowspan=3>".$row2->productname."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=left>".$oc_opt_name."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=center>".$row2->option_quantity."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=right style=\"padding:3\">".number_format((($row2->price+$row2->option_price)*$row2->option_quantity)+$row2->deli_price)."&nbsp;</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=center style=\"padding:3\" rowspan=3>";
						//echo $op_step[$row2->op_step];
						echo $pickup_status;
						echo "	</td>\n";
						echo "</tr>\n";
						echo "<tr><td colspan=7 height=1 bgcolor=#E7E7E7></tr>";
						echo "<tr>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=left>".$oc_opt_name_chn."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=center>".$row2->option_quantity."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=right style=\"padding:3\">".$tot_price_chn_text."&nbsp;</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "</tr>\n";
						$jj++;
					}
					echo "	<input type=hidden name=idxs value='".$idxs."'>";
					pmysql_free_result($result2);
					echo "	</table>\n";
					echo "	</td>\n";
					echo "</tr>\n";
					$i++;
				}
				pmysql_free_result($result);
				$cnt=$i;
				if($i==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
				} else if($i>0) {
					$total_block = intval($pagecount / $setup[page_num]);
					if (($pagecount % $setup[page_num]) > 0) {
						$total_block = $total_block + 1;
					}
					$total_block = $total_block - 1;
					if (ceil($t_count/$setup[list_num]) > 0) {
						// 이전	x개 출력하는 부분-시작
						$a_first_block = "";
						if ($nowblock > 0) {
							$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
							$prev_page_exists = true;
						}
						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

							$a_prev_page = $a_first_block.$a_prev_page;
						}
						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
								if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						} else {
							if (($pagecount % $setup[page_num]) == 0) {
								$lastpage = $setup[page_num];
							} else {
								$lastpage = $pagecount % $setup[page_num];
							}
							for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
								if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						}
						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
							$last_gotopage = ceil($t_count/$setup[list_num]);
							$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
							$next_page_exists = true;
						}
						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>1</B>";
					}
					$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
				}
?>
				<input type=hidden name=tot value="<?=$cnt?>">
				</form>

				<form name=detailform method="post" action="order_detail.php" target="vorderdetail">
				<input type=hidden name=ordercode>
				</form>

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
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=paystate value="<?=$paystate?>">
<input type=hidden name=sel_code value="<?=$sel_code?>">
<input type=hidden name=pickup_type value="<?=$pickup_type?>">
<input type=hidden name=orderby value="<?=$orderby?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

<form name=exeform action="order_list.exe.php" method=post>
<input type=hidden name=mode>
<input type=hidden name=oc_no>
<input type=hidden name=ordercode>
<input type=hidden name=idxs>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<?php include("copyright.php"); ?>
