<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

include("header.php");

$requestData = $_POST ? $_POST : $_GET;

$type = $requestData['type'];

// DB 기본키 ( 수정 누를때때 있는값 )
$no = $requestData['banner_no'];

//debug($request);

$arrPageText = array("intro" => "인트로", "main" => "전면 팝업", "quit" => "종료 팝업");


// 검색 조건 쿼리
$bannerQry = "";

# APP 배너 페이징
# APP 배너 테이블 없음
$page_sql = "SELECT COUNT(*) FROM 테이블명 WHERE 1=1 {$bannerQry} ";
$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

# APP 배너 리스트 불러오기
$bannerSql = "SELECT * FROM 테이블명 WHERE 1=1 ";
$bannerSql.= $bannerQry;
$bannerSql.= "ORDER BY no";
$sql = $paging->getSql( $bannerSql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$bannerList[] = $row;
}
?>

<SCRIPT LANGUAGE="JavaScript">


function changeAction( mode , num ){
	//mode ins -> DB 입력, upd -> 수정데이터 셋팅, upd_db -> DB에 수정 하기, del -> 삭제
	
	if( mode == 'ins' ){


		if( confirm('등록 하시겠습니까?') ){
			$("#mode").val( 'insert' );
			$("#insertForm").attr('action', './app_manage_image_indb.php');
		} else {
			return;
		}


	} else if ( mode == 'upd_db' ) {


		if( confirm('수정 하시겠습니까?') ){
			$("#mode").val( 'modify' );
			$("#insertForm").attr('action', './app_manage_image_indb.php');
		} else {
			return;
		}


	} else if ( mode == 'upd' ) {


		$('#banner_no').val( num );
		$("#mode").val( 'modfiy_select' );
		$("#insertForm").attr('action', './app_manage_image.php?type='+$("input[name='type']").val());


	} else if ( mode == 'del' ) {


		if( confirm('삭제 하시겠습니까?') ){
			$("#mode").val( 'delete' );
			$("#insertForm").attr('action', './app_manage_image_indb.php');
		} else {
			return;
		}


	} else {


		alert('잘못된 입력입니다.');
		return;


	}

	$("#insertForm").submit();
}



// 배너 타입에 따른 이벤트 처리
$(document).ready(function(){
});
</SCRIPT>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; APP 이미지 관리 &gt;<span> <?=$arrPageText[$type]?> 관리</span></p></div></div>

<form name='insertForm' id='insertForm' method='POST' enctype="multipart/form-data">
	<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
		<tr>
			<td valign="top">
				<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
								<input type='hidden' name='type' value='<?=$type?>' >
								<input type='hidden' name='mode' id='mode' value='<?=$mode?>' >
								<input type='hidden' name='banner_no' id='banner_no' value='' >
								<input type='hidden' name='block' value="<?=$block?>">
								<input type='hidden' name='gotopage' value="<?=$gotopage?>">
								<col width=240 id="menu_width"></col><col width=10></col><col width=></col>
								<tr>
									<td valign="top">
										<?php include("menu_design.php"); ?>
									</td>

									<td></td>

									<td valign="top">
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
											<tr><td height="8"></td></tr>
											<tr>
												<td>
													<div class="title_depth3"><?=$arrPageText[$type]?> 관리</div>
													<div class="title_depth3_sub"><span><?=$arrPageText[$type]?> 등록/수정/삭제 처리를 할 수 있습니다.</span></div>
												</td>
											</tr>

											<tr>
												<td>
													<div class="title_depth3_sub">배너 등록</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="table_style01">
														<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>													
															<col width=140></col><col width=></col>
															<tr>
																<th><span>인트로 이미지</span></th>
																<td class="td_con1" colspan="3" style="position:relative">
																	<input type='file' name="banner_img[]" style="WIDTH: 400px">
																</td>
															</tr>

															<?if($type == "main"){?>
															<tr>
																<th><span>팝업링크</span></th>
																<TD><INPUT name='app_popup_link' maxLength='80' size='80'></TD>
															</tr>
															<tr>
																<th><span>노출순서</span></th>
																<TD><INPUT name='app_view_sort' maxLength='4' size='4'></TD>
															</tr>
															<tr>
																<th><span>노출</span></th>
																<TD><INPUT type='checkbox' name='app_view_flag' value="1"> * 체크시 노출됩니다. </TD>
															</tr>
															<tr>
																<th><span>노출</span></th>
																<TD>
																	<INPUT type='radio' name='app_view_type' value="1"> 하루동안 열리지 않음
																	<INPUT type='radio' name='app_view_type' value="2"> 다시 열지 않음
																</TD>
															</tr>
															<?}?>
														</table>
													</div>
												</td>
											</tr>
											<tr>
												<td colspan="8" align="center">
													<?if($no){?>
														<a href="javascript:changeAction('upd_db', '기본키 들어갈 자리' );">
															<img src="images/btn_edit2.gif">
														</a>
														<a href="javascript:goList();">
															<img src="img/btn/btn_list.gif" >
														</a>
													<?}else{?>
														<a href="changeAction('ins', '' );">
															<img src="images/btn_confirm_com.gif">	
														</a>
													<?}?>
												</td>
											</tr>	
											<tr><td colspan="8" align="center">&nbsp;</td></tr>		
											<tr>
												<td>
													<div class="table_style02">
														<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
															<colgroup>
																<col width='50'><col width='*'>
																<?if($type == "main"){?>
																<col width='400'><col width='50'><col width='50'>
																<?}?>
																<col width='100'><col width='100'>
															</colgroup>
															<TR>
																<th>번호</th><th>이미지</th>
																<?if($type == "main"){?>
																<th>링크</th><th>순서</th><th>사용</th>
																<?}?>
																<th>수정</th><th>삭제</th>
															</TR>
															<?if( count( $bannerList ) > 0 ){?>
															<TR>
																<td>
																	1
																</td>
																<td>
																	이미지
																</td>
																<?if($type == "main"){?>
																<td>
																	링크
																</td>
																<td>
																	순서
																</td>
																<td>
																	사용
																</td>
																<?}?>
																<td>
																	<a href="javascript:changeAction( 'upd', '기본키 들어갈 자리' );"><img src="images/btn_edit.gif"></a>
																</td>
																<td>
																	<a href="javascript:changeAction('del', '기본키 들어갈 자리' );"><img src="images/btn_del.gif"></a>
																</td>
															</TR>
															<?}else{?>
															<TR>
																<td colspan='<?=$type=="main"?7:4?>' > 목록이 존재하지 않습니다.</td>
															</TR>
															<?}?>
														</TABLE>
													</div>
												</td>
											</tr>
											<tr>
												<td height="20">&nbsp;</td>
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
</form>










<script type="text/javascript">
</script>

<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<?=$onload?>
<?php
include("copyright.php");
