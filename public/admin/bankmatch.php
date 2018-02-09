<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."bankda/lib/bankda.class.php");

include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-5";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################


$bankda = new BANKDA();
$bankda->bankListUpdate();
//$bankda->list_size=2;
//$bankda->page_size=3;
$data_list = $bankda->getBankList();

extract($_REQUEST);
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//print_r($_shopdata);




include("header.php");

/*
$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count FROM {$qry_from} {$qry} ";
$paging = new Paging($sql,10,10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
*/

?>
<!-- <script type="text/javascript" src="lib.js.php"></script> -->
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function searchForm() {
	document.form1.action="<?=$_SERVER[PHP_SELF]?>";
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function GoPage(gotopage) {
	document.form1.page_no.value=gotopage;
	document.form1.submit();
}

function GoOrderby(orderby) {
	document.form1.action="<?=$_SERVER[PHP_SELF]?>";
	document.form1.orderby.value = orderby;
	document.form1.submit();
}

function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.member_form.search.value=id;
	document.member_form.submit();
}

function SenderSearch(sender) {
	document.sender_form.search.value=sender;
	document.sender_form.submit();
}

function ReserveInOut(id){
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.id.value=id;
	document.reserveform.type.value="reserve";
	document.reserveform.submit();
}

var clickno=0;
function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

function AddressPrint() {
	document.form1.action="order_address_excel.php";
	document.form1.submit();
	document.form1.action="<?=$_SERVER[PHP_SELF]?>";
}

function OrderExcel() {
	document.form1.action="bankmatch_excel.php";
	document.form1.submit();
	document.form1.action="<?=$_SERVER[PHP_SELF]?>";
}

function OrderDelete(ordercode) {
	if(confirm("해당 주문서를 삭제하시겠습니까?")) {
		document.idxform.type.value="delete";
		document.idxform.ordercodes.value=ordercode+",";
		document.idxform.submit();
	}
}

function OrderDeliPrint() {
	alert("운송장 출력은 준비중에 있습니다.");
}

function OrderCheckPrint() {
	document.printform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.printform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.printform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("소비자용 주문서로 출력하시겠습니까?")) {
		document.printform.gbn.value="N";
	} else {
		document.printform.gbn.value="Y";
	}
	document.printform.target="hiddenframe";
	document.printform.submit();
}

function OrderCheckExcel() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_excel.php";
	document.checkexcelform.submit();
}

function OrderCheckDelete() {
	document.idxform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			if(document.form2.chkordercode[i].value.substring(0,1)=="N") {
				alert("삭제가 불가능한 주문서가 포함되어있습니다.");
				return;
			} else {
				document.idxform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
			}
		}
	}
	if(document.idxform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("선택하신 주문서를 삭제하시겠습니까? ")) {
		document.idxform.type.value="delete";
		document.idxform.submit();
	}
}

function ProductInfo(code,prcode,popup) {
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}

function bankSync(){
	if(confirm("실시간 입금확인을 실행하시겠습니까?")){
		document.ifrmHidden.location.href="/bankda/sync.php?mode=M";
	}
}

function bankSyncComp(){
	alert("완료하였습니다.");
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 자동입금 확인 &gt;<span>입금 조회/확인</span></p></div></div>
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
			<?php include("menu_order.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">입금 조회/확인</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>통장에 입금된 내역을 실시간으로 조회하며, 입금된 내역을 실시간으로 입금확인처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td class="pt_30">
					<!-- 소제목 -->
					<div class="title_depth3_sub">입금현황 조회</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=get>
			<input type="hidden" name="page_no" value="1">
			<input type=hidden name=orderby value="<?=$_REQUEST[orderby]?>">
			<tr>
				<td>

					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>검색어</span></td>
							<td class="input_bd_st01">
							<select name="sfield">
								<option value="">통합검색</option>
								<option value="b.bkjukyo" <?=$_REQUEST[sfield]=="b.bkjukyo"?"selected":""?>>입금자명</option>
								<option value="b.bkname" <?=$_REQUEST[sfield]=="b.bkname"?"selected":""?>>입금은행</option>
								<option value="b.bkinput" <?=$_REQUEST[sfield]=="b.bkinput"?"selected":""?>>임금예정 금액</option>
								<option value="o.ordercode" <?=$_REQUEST[sfield]=="o.ordercode"?"selected":""?>>주문번호</option>
							</select>
							<input type="text" name="svalue" value="<?=$_REQUEST[svalue]?>">
							</td>
						</tr>
						<TR>
							<th><span>입금완료일</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
						</TR>
						<tr>
							<th><span>현재상태</span></td>
							<td class="input_bd_st01">
							<select name="sstatus">
								<option value="">전체 보기</option>
								<option value="0" <?=$_REQUEST[sstatus]=="0"?"selected":""?>>매칭성공 : 시스템</option>
								<option value="1" <?=$_REQUEST[sstatus]=="1"?"selected":""?>>매칭성공 : 관리자</option>
								<option value="2" <?=$_REQUEST[sstatus]=="2"?"selected":""?>>매칭실패 : 데이타 불일치</option>
								<option value="3" <?=$_REQUEST[sstatus]=="3"?"selected":""?>>매칭실패 : 동명이인</option>
							</select>
							</td>
						</tr>
						</TABLE>
						</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right">
				<input type="image" src="images/bank_search_button.gif"  border="0"> 
				<img src="images/bank_sync_button.gif"  border="0" onclick="bankSync()">&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a>
				
				</td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post onsubmit="return checkFrom()">
			<tr>
				<td style="padding-bottom:3pt;">
<?php
                /*
				$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

				$sql = "SELECT a.* FROM {$qry_from} {$qry} ";
				$sql.= "ORDER BY a.ordercode {$orderby} ";
				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());
                //exdebug($sql);
                */

				$colspan=10;
				if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif"  border="0"><B>정렬 :
					<?php if(!$orderby || $orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>입금완료일순↓</FONT></B></A>
					<?php }else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>입금완료일순↑</FONT></B></A>
					<?php }?>
					</td>
					<td width="" align="right"><img src="images/icon_8a.gif"  border="0">총 : <B><?=number_format($bankda->list_total)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif"  border="0">현재 <b><?=$bankda->page_no?>/<?=$bankda->page_total?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>


				<div class="table_style02">
				<?
				/*
					$sql = " select b.*, o.bank_date as match_date from tblbank b ";
					$sql.= " left join tblorderinfo o on b.ordercode = o.ordercode ";

					$rs = pmysql_query($sql,get_db_conn());
					while($row = pmysql_fetch_object($rs)){
						$row->match_date = $row->match_date?date("Y-m-d", strtotime($row->match_date)):"-";
						$row->bkdate = $row->bkdate?date("Y-m-d", strtotime($row->bkdate)):"-";
						$row->status = trim($row->status);
						switch($row->status){
							case "0":
								$row->status_tag = "매칭성공 (시스템)";
							break;
							case "1":
								$row->status_tag = "매칭성공 (관리자)";
							break;
							case "2":
								$row->status_tag = "매칭실패 (데이터 불일치)";
							break;
							case "3":
								$row->status_tag = "매칭실패 (동명 이인)";
							break;
						}

						$data_list[] = $row;
					}
				*/
				?>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<th>번호</th>
						<th>입금완료일</th>
						<th>계좌번호</th>
						<th>은행명</th>
						<th>입금금액</th>
						<th>입금자명</th>
						<th>현재상태</th>
						<th>최종 매칭일</th>
						<th>주문번호</th>
					</tr>
					<?if(is_array($data_list)){ 
                        $num = $bankda->list_start;
                        foreach($data_list as $data){
                            $num++;
                    ?>
					<tr <?=!$data->ordercode?"bgcolor=#d17171":""?>>
						<td><?=$num?></td>
						<td><?=$data->bkdate?></td>
						<td><?=$data->bkacctno?></td>
						<td><?=$data->bkname?></td>
						<td><?=number_format($data->bkinput)?></td>
						<td><?=$data->bkjukyo?></td>
						<td>
							<?if($data->status > 1){?>
								<select name="status[]" class="frmStatus">
									<option value="2" <?=$data->status=="2"?"selected":""?>>매칭실패 : 데이터 불일치</option>
									<option value="3" <?=$data->status=="3"?"selected":""?>>매칭실패 : 동명이인</option>
									<option value="1">관리자 입금확인</option>
								</select>
							<?}else{?>
								<?=$data->status_tag?>
							<?}?>
						
						</td>
						<td><?=$data->match_date?></td>
						<td>
						<?if($data->ordercode){?>
							<a href="JavaScript:OrderDetailView('<?=$data->ordercode?>')"><?=$data->ordercode?></a>
						<?}else{?>
							<input type="hidden" name="Bkcode[]" class="frmBkcode" value="<?=$data->bkcode?>">
							<input type="text" name="ordercode[]" class="frmOrdercode" value="">
						<?}?>
						</td>
					</tr>
					<?}}?>
				</table>
				</div>
				<script>
				function checkFrom(){
					for(var i=0; i<$(".frmBkcode").length; i++){
						
						if($(".frmStatus:eq("+i+")").val()==1 && !$(".frmOrdercode:eq("+i+")").val()){
							alert("주문번호를 입력하지 않았습니다.");
							return false;
						}
					}
				}
				</script>

				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;">
				<input type="image" src="/admin/images/botteon_save.gif">
				<!--a href="javascript:OrderDeliPrint();"><img src="images/btn_print.gif"  border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif"  border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel1.gif"  border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckDelete();"><img src="images/btn_judel.gif"  border="0"></a>--></td>
			</tr>
			<tr>
				<td align="center">				
                    <div id="page_navi01" style="height:'40px'">
                        <div class="page_navi">
                            <ul>
                                <?=$bankda->getPageNavi();?>
                            </ul>
                        </div>
                    </div>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>

			<form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=reserveform action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=checkexcelform action="order_excel.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>

			<form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>일자별 주문조회/배송</span></dt>
							<dd>
								- 일자별 쇼핑몰의 모든 주문현황 및 주문내역을 확인/처리하실 수 있습니다.<Br>
								- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br>
								- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<Br>
								- 카드실패 주문건은 2시간후에 삭제가 가능합니다.<br>
							</dd>
						</dl>
						<dl>
							<dt><span>일괄 처리 부가 기능</span></dt>
							<dd>
								- 운송장출력 : 체크된 주문건의 운송장을 일괄 출력합니다.(현재 서비스 준비중에 있습니다.)<br>
								- 주문서출력 : 체크된 주문건을 소비자용 주문서로 일괄 출력합니다.<Br>
								- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;엑셀 주문서 항목 조절은 <a href="javascript:parent.topframe.GoMenu(5,'order_excelinfo.php');"><span class="font_blue">주문/매출 > 주문조회 및 배송관리 > 주문리스트 엑셀파일 관리</span></a> 에서 가능합니다.<br>
								- 주문서삭제 : 체크된 주문건을 일괄 삭제 합니다.
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
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
<?php if(isdev()){ ?>
<iframe name="ifrmHidden" id="ifrmHidden" src="" style="width:1200px;height:2400px"></iframe>
<?php }else{ ?>
<iframe name="ifrmHidden" id="ifrmHidden" src="" style="display:none;"></iframe>
<?php } ?>
<?=$onload?>
<?php
include("copyright.php");
