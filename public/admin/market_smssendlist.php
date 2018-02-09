<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
//$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
//$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
//$period[3] = date("Y-m-d",strtotime('-1 month'));

$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$search_start   = $search_start?$search_start:$period[0];
$search_end     = $search_end?$search_end:$period[0];
$search_s       = $search_start?$search_start:"";
$search_e       = $search_end?$search_end:"";


$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
}
pmysql_free_result($result);

if(ord($sms_id)==0 || ord($sms_authkey)==0) {
	alert_go('SMS 기본환경 설정에서 SMS 아이디 및 인증키를 입력하시기 바랍니다.','market_smsconfig.php');
}

$type=$_POST["type"];
$status=$_POST["status"];
if(ord($status)==0) $status="A";


$t_count=0;
$smslistdata=array();
$duoSmsCheck = duo_smsAuthCheck();
# SMS 발송내역 및 아이디확인 추가 2015 07 08 유동혁 [뿌리오]
if($duoSmsCheck[result]=="true"){
	$qry = "";
	switch($status){
		case 'A':
			$qry = "";
			break;
		case 'Y':
			$qry = "AND status = 'Y' ";
			break;
		case 'N':
			$qry = "AND status = 'N' ";
			break;
		case 'M':
			$qry = "AND status = 'M' ";
			break;
		default :
			$qry = "";
			break;
	}

    // 기간선택 조건
    if ($search_s != "" || $search_e != "") { 
        if(substr($search_s,0,10)==substr($search_e,0,10)) {
            $qry.= "AND substr(send_date::varchar, 1, 10) LIKE '".substr($search_s,0,10)."%' ";
        } else {
            $qry.= "AND substr(send_date::varchar, 1, 10) >= '{$search_s}' AND substr(send_date::varchar, 1, 10) <= '{$search_e}' ";
        }
    }

	# count
	$countSql = "SELECT COUNT(*) AS cnt FROM tblsmslog ";
	$countSql.= "WHERE 1=1 ";
	$countSql.= $qry;
	$countResult = pmysql_query($countSql,get_db_conn());
	$selectRow = pmysql_fetch_object($countResult);
	$t_count = (int)$selectRow->cnt;
	$paging = new Paging($t_count,10,20);
	$gotopage = $paging->gotopage;
	pmysql_free_result($countResult);
	
	# select
	$selectSql = "SELECT msg,send_date,to_tel_no,status,etc_msg,res_msg FROM tblsmslog ";
	$selectSql.= "WHERE 1=1 ";
	$selectSql.= $qry;
    $selectSql.= " order by idx desc ";
	$selectSql = $paging->getSql($selectSql);
	//exdebug($selectSql);
	$selectResult = pmysql_query($selectSql,get_db_conn());
	
	while($selectRow = pmysql_fetch_object($selectResult)){
		$smslistdata[] = $selectRow;
	}
	pmysql_free_result($selectResult);
}else if($duoSmsCheck[result]=="false"){
	$onload="<script>alert('SMS 회원가입 및 충전 후 이용하시기 바랍니다.');</script>";
}else{
	$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
}
// 인커뮤 발생내역 주석처리 2015 07 08 유동혁 
#########################################################
#														#
#			SMS서버와 통신 루틴 추가 (완료)				#
#														#
#########################################################
/*$query="block={$block}&gotopage={$gotopage}&type={$type}&year={$year}&month={$month}&day={$day}&status=".$status;
$resdata=getSmssendlist($sms_id,$sms_authkey,$query);
if(substr($resdata,0,2)=="OK") {
	$tempdata=explode("=",$resdata);
	$t_count=$tempdata[1];
	$smslistdata=unserialize($tempdata[2]);
} else if(substr($resdata,0,2)=="NO") {
	$tempdata=explode("=",$resdata);
	$onload="<script>alert('{$tempdata[1]}');</script>";
} else {
	$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
}*/

include("header.php"); 
echo $onload;
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function sendsms(msg,tel){
	tmre = /RN_/g
	sqre = /sQ_/g
	dqre = /dQ_/g
	msg = msg.replace(tmre,"\r\n");
	msg = msg.replace(sqre,"'");
	msg = msg.replace(dqre,'"');
	window.open("about:blank","sms","width=200,height=200");
	document.smsform.number.value=tel;
	document.smsform.message.value=msg;
	document.smsform.type.value="sendfail";
	document.smsform.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; SMS 발송/관리 &gt;<span>SMS 발송내역 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr><td height="8"></td></tr>
			
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">SMS발송내역 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>SMS 발송에 대한 상세내역을 확인할 수 있습니다.</span></div>
				</td>
			</tr>
            
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC" style="padding:6pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%" height="35" background="images/blueline_bg.gif" align="center"><b><font color="#0099CC">SMS 발송내역 조회하기</font></b></TD>
						</TR>
						<TR>
							<TD width="100%">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">조회기간</TD>
								<TD class="td_con1" width="500">
                                    <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								</TD>
							</TR>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"></TD>
							</TR>
							<tr>
								<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">처리상태</TD>
								<TD class="td_con1" width="500">
<?php
								echo "<select size=1 name=status class=\"select\">\n";
								$arstatus = array ("전체보기","발송완료","발송실패","발송예정");
								$statusvalue = array ("A","Y","N","M");
								for($i=0;$i<4;$i++){
									echo "<option value=\"{$statusvalue[$i]}\"";
									if($status==$statusvalue[$i]) echo " selected";
									echo ">{$arstatus[$i]}</option>\n";
								}
								echo "</select>\n";
?>
								<a href="javascript:document.form1.submit();"><img src="images/btn_search2.gif" border="0" align=absmiddle></a>
								</TD>
							</tr>
							</TABLE>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>

			</form>

			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">

				<?$colspan=5; ?>

				<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">SMS발송내역 검색 결과</div>
					<!-- 소제목 -->
					
				</td>
			</tr>
				<tr>
					<td width="100%" height=3 style="padding-bottom:3pt;" align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 SMS 발송건수 : <B><?= $t_count ?></B>건 <img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				<tr>
					<td width="100%">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD background="images/table_top_line.gif" colspan=5></TD>
					</TR>
					<TR>
						<TD class="table_cell" width="20" align="center">No</TD>
						<TD class="table_cell1" align="center">전송완료시간</TD>
						<TD class="table_cell1" align="center">수신번호</TD>
						<TD class="table_cell1" width="50%" align="center">메세지</TD>
						<TD class="table_cell1" align="center">처리상태</TD>
					</TR>
					<TR>
						<TD colspan="5" background="images/table_con_line.gif"></TD>
					</TR>
<?php
					$cnt=0;
					if ($t_count>0) {
						$rsltmsg=array("1"=>"TIMEOUT","A"=>"휴대폰 호 처리중","B"=>"음영지역","C"=>"Power Off",
									"D"=>"메세지 저장 개수 초과","2"=>"잘못된 전화번호","a"=>"일시 서비스 정지",
									"b"=>"기타 단말기 문제","c"=>"착신 거절","d"=>"기타","e"=>"이통사 SMC 형식 오류",
									"f"=>"IB 자체 형식 오류","g"=>"SMS 서비스 불가 단말기","h"=>"휴대폰 호 불가 상태",
									"i"=>"SMC 운영자가 메시지 삭제","j"=>"이통사 내부 메시지 Que Full");

						$patten =array("\r","'","\"");
						$replace=array("RN_","sQ_","dQ_");
						$cnt=0;
						//exdebug($smslistdata);
						for($i=0;$i<count($smslistdata);$i++) {
							
							$row=$smslistdata[$i];
							$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
							$date = substr($row->send_date,0,16);
							$date = substr($date,0,4)."/".substr($date,5,2)."/".substr($date,8,2)." (".substr($date,11,2).":".substr($date,14,2).")";
							echo "<TR>\n";
							echo "	<TD class=\"td_con2\" width=\"34\" align=\"center\">{$number}</TD>\n";
							echo "	<TD class=\"td_con1\" align=\"center\">{$date}</TD>\n";
							echo "	<TD class=\"td_con1\" align=\"center\"><B>{$row->to_tel_no}</B></TD>\n";
							echo "	<TD class=\"td_con1\" width=\"40%\">&nbsp;{$row->msg}</TD>\n";// 전체적인 수정이 있을때 까지는 사용 못함<a href=\"JavaScript:sendsms('".str_replace($patten,$replace,$row->msg)."','{$row->to_tel_no}');\"></a>
							echo "	<TD class=\"td_con1\" align=\"center\"><B>";
							if($row->status=="Y") echo "발송 완료";
							else if($row->tran_rslt=="M") echo "<font color=#0072BC>발송 예정</font>";
							else echo "<font color=#FF0000><u>발송 실패</u></font>";
							echo "	</B></TD>\n";
							echo "</TR>\n";
							echo "<TR>\n";
							echo "	<TD colspan={$colspan} background=\"images/table_con_line.gif\"></TD>\n";
							echo "</TR>\n";
							$cnt++;
						}
					}

					if ($cnt==0) {
						echo "<tr><td class=td_con2 colspan={$colspan} align=center>조건에 맞는 발송내역이 존재하지 않습니다.</td></tr>";
					}
?>
					<TR>
						<TD background="images/table_top_line.gif" colspan=5></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td width="100%" height=10></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" class="font_size" align="center">
						<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="25">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>SMS 발송내역 관리</span></dt>
							<dd>
							  - SMS 발송내역은 전송스케쥴 및 네트워크 상태에 따라 수분의 시간이 지연될수 있습니다. <br>
							  - SMS 발송 실패된 건에 대해서는 2일 후 일괄적으로 재충전 해드립니다. <br>
							  - SMS 발송 실패된 건의 [발송실패]를 누르시면 발송 실패한 결과를 확인할 수 있습니다. <br>
							  - SMS 발송 실패된 건의 재전송을 원하시면 실패건에 대해서 재전송이 가능합니다. <br>
							  - "전송완료시간"은 통신사(SKT, KTF, LGT)에서 알려준 시간으로서 문자메세지가 휴대폰에 도착한 시간입니다.
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

	<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
    <input type=hidden name=search_start value="<?=$search_start?>">
	<input type=hidden name=search_end value="<?=$search_end?>">
	<input type=hidden name=status value="<?=$status?>">
	</form>

	<form name=smsform method=post action="sendsms.php" target=sms>
	<input type=hidden name=type>
	<input type=hidden name=number>
	<input type=hidden name=message>
	</form>

	</table>
	</td>
</tr>
</table>

<?php 
include("copyright.php");
