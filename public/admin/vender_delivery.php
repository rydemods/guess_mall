<?php
/********************************************************************* 
// 파 일 명		: vender_delivery.php 
// 설     명		: 입점업체 배송관련 기능설정
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 배송관련 기능설정
// 작 성 자		: 2016.03.18 - 김재수
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
	# 파일 클래스 추가
	include_once($Dir."lib/file.class.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "me-4";
	$MenuCode = "member";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$vender=$_POST["vender"];

	$sql = "SELECT a.*, b.brand_name FROM tblvenderinfo a, tblvenderstore b ";
	$sql.= "WHERE a.vender='{$vender}' AND a.delflag='N' AND a.vender=b.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vdata=pmysql_fetch_object($result)) {
		alert_go('해당 업체 정보가 존재하지 않습니다.',-1);
	}
	pmysql_free_result($result);	

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

$mode=$_POST["mode"];

if($mode=="update") {

	#배송비 선정방법 변경 2016-02-16 유동혁
	$basefeetype_select = $_POST['basefeetype_select']; // 배송료 선택 0 - 무료 / 1 - 유료
	if( $basefeetype_select == '1' ){
		$deli_select = $_POST['deli_select']; // 지불방법 0 - 선불, 1 - 착불, 2 - 구매자(선불/착불) 선택
		$basefee_select = $_POST['basefee_select']; // 배송료
		$minprice_select = $_POST['minprice_select']; // 배송료 지불 기준값 ( 미만 )
	} else {
		$deli_select = 0; // 지불방법 0 - 선불, 1 - 착불, 2 - 구매자(선불/착불) 선택
		$basefee_select = 0; // 배송료
		$minprice_select = 0; // 배송료 지불 기준값 ( 미만 )
	}

	$up_deli_price     = $basefee_select; //배송료
	$up_deli_pricetype = $basefeetype_select; // 배송료 선택
	$up_deli_mini      = $minprice_select; // 배송료 지불 기준값
	$up_deli_select    = $deli_select; // 지불방법

	$sql = "UPDATE tblvenderinfo SET ";
	$sql.= "deli_price		= '".$up_deli_price."', ";
	$sql.= "deli_pricetype	= '".$up_deli_pricetype."', ";
	$sql.= "deli_mini		= '".$up_deli_mini."', ";
	$sql.= "deli_select		= '".$up_deli_select."' ";
	$sql.= "WHERE vender='{$vender}' ";

	if(pmysql_query($sql,get_db_conn())) {

		$log_content = "## 입점업체 배송료 수정 ## - 벤더 : ".$vender." - 배송료 선택 : ".$up_deli_pricetype." - 지불방법 : ".$up_deli_select." - 배송료 : ".$basefee_select." - 배송료 지불 기준값 : ".$up_deli_mini;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		echo "<html></head><body onload=\"alert('배송료 설정이 완료되었습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('배송료 설정중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}

$deli_basefee     = $_vdata->deli_price; //배송료
$deli_basefeetype = $_vdata->deli_pricetype;; //배송료 선택
$deli_miniprice   = $_vdata->deli_mini; // 배송료 지불 기준값
$deli_select      = $_vdata->deli_select; // 지불방법

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$disabled=$_POST["disabled"];
	$s_check=$_POST["s_check"];
	$search=$_POST["search"];
	$block=$_POST["block"];
	$gotopage=$_POST["gotopage"];

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;

	//배송료 책정방법 변경 2016-02-16 유동혁
	if( $('input[name="basefeetype_select"]:checked').val() == '1' ){

		if( $('input[name="basefee_select"]').val().length == 0 ){
			alert("배송료를 입력하세요.");
			$('input[name="basefee_select"]').focus();
			return;
		} else if( isNaN( $('input[name="basefee_select"]').val() ) ){
			alert("배송료는 숫자만 입력 가능합니다.");
			$('input[name="basefee_select"]').focus();
			return;
		} else if( parseInt( $('input[name="basefee_select"]').val() ) <= 0 ){
			alert("배송료는 0원 이상 입력하셔야 합니다.");
			$('input[name="basefee_select"]').focus();
			return;
		}

		if( isNaN( $('input[name="minprice_select"]').val() ) ){
			$('input[name="minprice_select"]').val( 0 );
		} else if( parseInt( $('input[name="minprice_select"]').val() ) < 0 ){
			$('input[name="minprice_select"]').val( 0 );
		}
	}

	if(confirm("변경하신 내용을 저장하시겠습니까?")) {
		form.mode.value="update";
		form.target="processFrame";
		form.submit();
	}
}

function GoReturn() {
	document.form3.submit();
}


function goBackList(){
	location.href="vender_management2.php";
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 입점업체 정보관리 &gt;<span>배송관련 기능설정</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">배송관련 기능설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>입점업체의 배송료 조건을 설정하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr><td height=15></td></tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>업체 ID</span></th>
					<td><B><?=$_vdata->id?></B></td>
				</tr>
				<tr>
					<th><span>상호 (회사명)</span></th>
					<td><?=$_vdata->com_name?></td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<input type=hidden name=vender value="<?=$vender?>">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배송료 설정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<th><span>배송료 선택</span></th>
					<td>
						<input type='radio' name='basefeetype_select' id='basefeetype_0' value='0' <? if( $deli_basefeetype == '0' || is_null($deli_basefeetype) ) { echo 'checked'; } ?> >
						<label for='basefeetype_0'>배송료 <font color='#0000FF'><b>무료</b></font></label>
						<input type='radio' name='basefeetype_select' id='basefeetype_1' value='1' <? if( $deli_basefeetype == '1' ) { echo 'checked'; } ?> >
						<label for='basefeetype_1' >배송료 <font color='#FF0000'><b>유료</b></font></label>
					</td>
				</tr>
				<tr>
					<th><span>지불방법</span></th>
					<td>
						<input type='radio' name='deli_select' id='deli_0' value='0' <? if( $deli_select == '0' || is_null($deli_select) ) { echo 'checked'; } ?> >
						<label for='deli_0' >배송료 <font color='#CC3D3D'><b>선불</b></font></label>
						<input type='radio' name='deli_select' id='deli_1' value='1' <? if( $deli_select == '1' ) { echo 'checked'; } ?> >
						<label for='deli_1' >배송료 <font color='#47C83E'><b>착불</b></font></label>
						<input type='radio' name='deli_select' id='deli_2' value='2' <? if( $deli_select == '2' ) { echo 'checked'; } ?> >
						<label for='deli_2' >배송료 <font color='#4374D9'><b>구매자( 선불/착불 ) 선택</b></font></label>
					</td>
				</tr>
				<tr>
					<th><span>배송료</span></th>
					<td>
						배송료 <input type='text' name='basefee_select' value='<?=$deli_basefee?>' style='text-align: right;'> 원
						<div style='margin-top : 3px; padding : 5px 5px 0px 0px;' >
						<TABLE cellSpacing='0' cellPadding='0' width="100%" border='0' >
							<tr>
								<td align="center" style="border : 3px #57B54A solid; padding : 5px; ">
									구매금액 <input type='text' name='minprice_select' size='10' maxlength='10' value="<?=$deli_miniprice?>" 
										class="input" style="text-align:right;">
									원 미만일 경우 배송비가 청구됩니다.<br>
									<span style="font-size:8pt;color:#2A97A7;line-height:11pt;letter-spacing:-0.5pt;">
										* 구매금액 0 원 입력시 모든 금액에 배송비가 부과됩니다.
									</span>
								</td>
							</tr>
						</table>
						</div>
					</td>
				</tr>
				</table>
				<script>
					//배송료 변환 script
					$(document).ready( function(){
						var basefeetype = $('input[name="basefeetype_select"]:checked');
						if( basefeetype.val() == '0'){
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).attr( 'disabled', 'true' );
							});
							$('input[name="basefee_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
							$('input[name="minprice_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
						} else {
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).removeAttr( 'disabled' );
							});
							$('input[name="basefee_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
							$('input[name="minprice_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
						}
					});

					$(document).on( 'click', 'input[name="basefeetype_select"]', function( event ){
						if( $(this).val() == '0' ){
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).attr( 'disabled', 'true' );
							});
							$('input[name="basefee_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
							$('input[name="minprice_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
						} else {
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).removeAttr( 'disabled' );
							});
							$('input[name="basefee_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
							$('input[name="minprice_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
						}
					});
				</script>
				</div>
				</td>
			</tr>			
			<tr><td height=20></td></tr>	
			<tr>
				<td colspan=8 align=center>
					<a href="javascript:CheckForm();"><img src="images/btn_edit2.gif" width="113" height="38" border="0"></a>
					&nbsp;
					<a href="javascript:goBackList();"><img src="img/btn/btn_list.gif"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>입점업체 정보 수정/삭제</span></dt>
							<dd>- 등록된 입점업체 리스트와 기본적인 정보사항을 수정/삭제 할 수 있습니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			<form name="form3" method="post" action="vender_management2.php">
			<input type=hidden name='vender' value="<?=$value?>">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>
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
<?=$onload?>
<?php 
include("copyright.php");
