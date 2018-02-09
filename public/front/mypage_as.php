<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once("../lib/file.class.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

include($Dir."admin/calendar_join.php");
$class_on['mypage_cs'] = " class='on'";

#####날짜 셋팅 부분
$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

// 상단 top 메뉴 추가
$type_menu=$_POST["type_menu"];
$idx = $_POST["idx"] ? $_POST["idx"] : $_GET["idx"];

$day_division = $_POST['day_division'];

$limitpage = $_POST['limitpage'];

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$etime=strtotime("$e_year-$e_month-$e_day");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$formatDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day"));
$formatDate2 = date("Y-m-d",$etime);

$strDate1 = date("YmdHis",strtotime("$s_year-$s_month-$s_day"));
$strDate2 = date("Ymd235959",$etime);
$today = time();

#파일여부경로
$filepath = $Dir.DataDir."shopimages/personal/";

// $while = "";
// if($type_menu == 'waite' || $type_menu ==''){
// 	$while = " AND re_id = '' ";
// } else {
// 	$while .= " AND re_id != '' ";
// }
// as 문의 리스트
$list_sql ="SELECT * FROM tblasinfo WHERE id = '".$_ShopInfo->getMemid()."'".$while;
$list_sql .=" AND to_char(date, 'yyyymmddhhmmss') >= '".$strDate1."' AND to_char(date, 'yyyymmddhhmmss') <= '".$strDate2."'";
$list_sql .=" AND status != 2 ORDER BY idx DESC";

// echo $list_sql;
// exit();
# 페이징
$paging = new New_Templet_paging($list_sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql( $list_sql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$list[] = $row;
}

//$product_select = "SELECT DISTINCT  T2.productname,T2.productcode FROM tblorderinfo T1 JOIN tblorderproduct T2 ON T1.ordercode = T2.ordercode AND T1.id ='".$_ShopInfo->getMemid()."'";
// $product_select = "SELECT DISTINCT  T2.productname,T2.productcode FROM tblorderinfo T1 JOIN tblorderproduct T2 ON T1.ordercode = T2.ordercode AND T1.id ='jkm9424@naver.com'";
$product_select = "SELECT DISTINCT  c.productname,c.productcode FROM tblorderinfo a, tblorderproduct b, tblproduct c WHERE a.ordercode = b.ordercode AND b.productcode = c.productcode and a.id = '".$_ShopInfo->getMemid()."'";
$product_result = pmysql_query( $product_select, get_db_conn() );
while( $product_row = pmysql_fetch_array( $product_result ) ){
	$product_list[] = $product_row;
}
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>

<!-- 20170405 AS 접수 -->
<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">AS 접수</h2>

		<div class="inner-align page-frm clear">

		<!-- LNB -->
		<? include  "mypage_TEM01_left.php";  ?>
		<!-- //LNB -->
		<article class="my-content">
				
				<div class="as-flow clear">
					<div class="inner">
						<div class="visual"><img src="../static/img/icon/icon_as01.png" alt="AS접수"><span>AS 접수</span></div>
						<div class="comment" style="font-size: 11px;">
							<dl>
								<!-- <dt>AS 접수</dt> -->
								<dd>- 온라인 AS 게시판 접수</dd>
								<dd>- 온라인 콜센터 접수</dd>
							</dl>
						</div>
					</div><!-- //.inner -->
					<div class="inner">
						<div class="visual"><img src="../static/img/icon/icon_as02.png" alt="고객만족실"><span>고객상담실(1661-2585)</span></div>
						<div class="comment" style="font-size: 11px;">
							<p>- 상품 인수 수선 또는 외부 심의 의뢰<br>
							- 고객에게 유선 통화</p>
						</div>
					</div><!-- //.inner -->
					<div class="inner">
						<div class="visual"><img src="../static/img/icon/icon_as03.png" alt="수선진행"><span>수선진행</span></div>
						<div class="comment" style="font-size: 11px;">
						<!--  
							<p>- 자체 수선실 및 협력업체에서<br><span style="padding-left:7px"></span>수선 지행</p>
						-->
							<p >고객과실<br>
							- 수선가능 : 수선 (고객 수선비 부담)<br>
							- 수선불가 : 회송</p>
							<p>제품하자<br>
							- 수선가능 : 수선 (신원몰 수선비 부담)<br>
							- 수선불가 : 교환 / 환불 진행</p>
						</div>
					</div><!-- //.inner -->
					<div class="inner">
						<div class="visual"><img src="../static/img/icon/icon_as04.png" alt="수선완료 후 발송"><span>수선완료 후 발송</span></div>
						<div class="comment" style="font-size: 11px;">
							<p>- 수선 완료후 발송 혹은 환불</p>
						</div>
					</div><!-- //.inner -->
				</div>
				<div class="as-tel">
					<p><strong>AS 접수 및 문의 (1661-2585)</strong></p>
					<p>주소 : 서울 중랑구 신내로1길 20 신원몰CS CJ대한통운택배중랑대리점</p>
				</div>
				<section class="mt-55">
				<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
					<header class="my-title">
						<h3 class="fz-0">AS</h3>
						<div class="count">전체 <strong><?=$t_count?></strong></div>
						<div class="date-sort clear">
							<div class="type month">
								<p class="title">기간별 조회</p>
							<?php 
								// 기간별 조회 20170414
								if(!$day_division) $day_division = '1MONTH';
								foreach($arrSearchDate as $kk => $vv){
									$dayClassName = "";
									if($day_division != $kk){
										$dayClassName = '';
									}else{
										$dayClassName = 'on';
									}
									//echo '<button type="button" class="'.$dayClassName.'" onClick = "GoSearch2("'.$kk.'", this)"><span>'.$vv.'</span></button>';
							?>
									<button type="button" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><span><?=$vv?></span></button>
							<?php
									
								}
							?>	
							
								<!-- <button type="button" class="on"><span>1개월</span></button>
								<button type="button"><span>3개월</span></button>
								<button type="button"><span>6개월</span></button>
								<button type="button"><span>12개월</span></button> -->
							</div>
							<div class="type calendar">
								<p class="title">일자별 조회</p>
								<div class="box">
									<div><input type="text" title="일자별 시작날짜" name="date1" id="" value="<?=$formatDate1?>" readonly></div>
									<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
								</div>
								<span class="dash"></span>
								<div class="box">
									<div><input type="text" title="일자별 시작날짜" name="date2" id="" value="<?=$formatDate2?>" readonly></div>
									<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
								</div>
							</div>
							<button type="button" class="btn-point" onClick="javascript:CheckForm();"><span>검색</span></button>
						</div>
					</header>
				</form>
					<div>
						<table class="th-top table-toggle">
							<caption>AS 리스트</caption>
							<colgroup>
								<col style="width:100px">
								<col style="width:auto">
								<col style="width:100px">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">작성일</th>
									<th scope="col">제목</th>
									<th scope="col">상태</th>
								</tr>
							</thead>
							<tbody class="tbody_subject">
					<?php
						if( count($list) > 0 ) {
							$cnt=0;
							foreach( $list as $key=>$val ){
								$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt++ );
								echo '<tr>';
								echo '<td class="txt-toneB">'.date_format(date_create($val['date']), "Y.m.d").'</td>';
								echo '<td class="ta-l pl-50"><a href="javascript:;" class="menu fw-bold txt-toneA">'.$val['subject'].'</a></td>';
								echo '<td class="txt-toneA">'.$val['type_mode'].'</td>';
								echo '</tr>';
								echo '<tr class="hide">';
								echo '<td class="reset" colspan="3">';
								echo '<div class="board-answer editor-output">';
								echo '<div class="btn">';
								echo '<button class="btn-basic h-small w50" onclick="editForm('.$val['idx'].',\'tblasinfo\');"><span>수정</span></button>';
								echo '<button class="btn-line h-small w50" onclick="deleteForm('.$val['idx'].');"><span>삭제</span></button>';
								echo '</div>';
								/* echo '<p><strong>주문상품 : </strong> </p>'; */
								echo $val['content'];
								if($val['up_filename'] != ''){
									echo '<p><img src="'.$filepath.$val['up_filename'].'" alt=""></p>';
								}
								if ($val['status'] == 1 && $val['re_id'] != '') {
									echo '<div class="answer editor-output">';
									echo '<div class="answer-user"><span>'.$val['re_id'].' <em>|</em>'.date("Y.m.d", strtotime( $val['re_date']) ).'</span></div>';
									echo '<p>'.$val['re_content'].'</p>';
									echo '<p>'.$val['re_subject'].'</p>';
									echo '</div>';
								}
								echo '</div>';
								echo '</td>';
								echo '</tr>';
							}
						} else {
							echo '<tr><td colspan="3">등록된 문의가 없습니다.</td></tr>';
						}
					?>
							<!-- 
								<tr>
									<td class="txt-toneB">2017.02.02</td>
									<td class="ta-l pl-50">
										<a href="javascript:;" class="menu fw-bold txt-toneA">수선요청합니다</a>
									</td>
									<td class="txt-toneA">AS 접수</td>
								</tr>
								<tr class="hide">
									<td class="reset" colspan="3">
										<div class="board-answer editor-output">
											<div class="btn">
												<button class="btn-basic h-small w50"><span>수정</span></button>
												<button class="btn-line h-small w50"><span>삭제</span></button>
											</div>
											<p><strong>주문상품 : </strong> 솔리드 심플 벨티드 쟈켓</p>
											<div class="mt-10"><strong>수선의뢰 내용</strong></div>
											<p>사이즈가 잘 맞지 않아 입기가 힘듭니다.</p>
											<p>32사이즈인데 28사이즈 인듯 합니다.</p>
											<p>허리사이즈 늘려주시라요</p>
											<p></p>
											<p><img src="../static/img/test/@loobook_thumb02.jpg" alt=""></p>
										</div>
									</td>
								</tr>
								<tr>
									<td class="txt-toneB">2017.02.02</td>
									<td class="ta-l pl-50">
										<a href="javascript:;" class="menu fw-bold txt-toneA">수선 요청 한다요</a>
									</td>
									<td class="point-color">심의중</td> 
								</tr>
								<tr class="hide">
									<td class="reset" colspan="3">
										<div class="board-answer editor-output">
											<div class="btn">
												<button class="btn-basic h-small w50"><span>수정</span></button>
												<button class="btn-line h-small w50"><span>삭제</span></button>
											</div>
											<p><strong>주문상품 : </strong> 솔리드 심플 벨티드 쟈켓</p>
											<div class="mt-10"><strong>수선의뢰 내용</strong></div>
											<p>사이즈가 잘 맞지 않아 입기가 힘듭니다.</p>
											<p>32사이즈인데 28사이즈 인듯 합니다.</p>
											<p>허리사이즈 늘려주시라요</p>
										</div>
									</td>
								</tr>
							 -->
							</tbody>
						</table>
						<div class="btn-withPainate">
							<div class="list-paginate mt-20">
								<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
							</div>
						<?php 
							if(count($product_list) > 0 ){
								echo "<button class=\"btn-point h-large w150 btn-as-order\" type=\"button\"><span>AS 접수하기</span></button>";
							} elseif (count($product_list) == 0 ) {
								echo "<button class=\"btn-point h-large w150 \" type=\"button\" onclick=\"javascritp:alert('구매완료된 제품이 없습니다.');\"><span>AS 접수하기</span></button>";
							}
						?>
							<!-- <button class="btn-point h-large w150 btn-as-order" type="button"><span>AS 접수하기</span></button> -->
						</div>
					</div>
					<ul class="as-attention">
						<li><strong>신원몰 A/S 업무</strong></li>
						<li> - 상품상의 하자로 인한 수선에 대해서는 무상으로 처리해 드리고 있으나, 고객 취급상의 과실로 인한 파손 또는 과다 착용으로 인한  수선은 수선가능과 <br><span style="padding-left:12px"></span>불가능 및 유상수선과 무상수선으로 나뉘어 진행하고 있습니다.</li>
						<li><strong>신원몰 A/S 의뢰방법 - 택배접수</strong></li>
						<li> - A/S 제품과 수선의뢰 내용 및 고객 성명,연락처를 메모하여 함께 동봉하여 보내주시면 제품 수령후 담당자가 고객님께 연락을 드립니다.</li>
						<li style="font-size: 11px;">* 주소지 : 서울시 중랑구 신내동 495-42 CJ택배 서울 상봉대리점 ㈜신원</li>
						<li><strong>신원몰 A/S 수선기준</strong></li>
						<li> - 구입일로 부터 1년이내 수선가능 (수선가능과 불가능 및 유상/무상수선으로 나뉘어 집니다.)</li>
						<li>   *수선불가 항목</li>
						<li>    소매기장 / 총장 / 밑단 수선등<br>
						    디자인 변경불가 (리폼불가)</li>
					</ul>
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<!-- 상세 > AS 접수-->
<div class="layer-dimm-wrap pop-asReg">
	<div class="layer-inner">
		<h2 class="layer-title">AS 접수</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			<form name='write_form' id='write_form' action="mypage_asinfowrite.php" method='POST' enctype="multipart/form-data">
			<input type='hidden' id='mode' name='mode' value="<?=$_GET["mode"] ?>">
			<input type='hidden' id='idx' name='idx' value="<?=$_GET["idx"] ?>">
			<input type='hidden' name='chk_mail' value="N">
			<input type='hidden' name='chk_sms' value="N">
			<input type='hidden' name='ori_filename' value="<?=$list_sql->ori_filename ?>">
			<input type='hidden' id="ph" name='hp' value="">
			<input type='hidden' id="email" name='email' value="">
			<table class="th-left">
				<caption>AS 접수</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="as_goods" class="essential">상품선택</label></th>
						<td>
							<div class="input-cover">
								<div class="select">
									<select style="width:320px" title="상품 선택" id="product_code" name="product_code">
									<?php 
										foreach( $product_list as $key=>$val ){
											echo "<option value=\"{$val['productcode']}\">{$val['productname']}</option>\n";
										}
									?>
									</select>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="as_title" class="essential">제목</label></th>
						<td><div class="input-cover"><input type="text" class="w100-per required_value" title="제목 입력" label="제목" name="pop_subject" id="pop_subject"></div></td>
					</tr>
					<tr>
						<th scope="row"><label for="pop_content" class="essential">내용</label></th>
						<td><textarea class="w100-per required_value" style="height:272px" label="문의내용" name="pop_content" id="pop_content"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="as_email">이메일</label></th>
						<td>
							<div class="input-cover">
							<!--  
								<input type="text"  style="width:190px" title="이메일 입력" id="contact_email" name="contact_email">
								<span class="txt">@</span>
							-->
								<input type="text" id="email1" name="email1" value="" style="width:150px" title="이메일 입력" tabindex="14">
								<span class="txt">@</span>
								<input type="text" id="email2" name="email2" value="" title="도메인 직접 입력" class="ml-10" style="width:150px; display: none;" >
								&nbsp;
								<div class="select">
								<!--  <select style="width:170px" title="이메일 도메인 선택" onchange="EmailChange()" id="email_select"> -->
										<!-- <option value="@naver.com">naver.com</option>
										<option value="@daum.net">daum.net</option>
										<option value="@gmail.com">gmail.com</option>
										<option value="@nate.com">nate.com</option>
										<option value="@yahoo.co.kr">yahoo.co.kr</option>
										<option value="@lycos.co.kr">lycos.co.kr</option>
										<option value="@empas.com">empas.com</option>
										<option value="@hotmail.com">hotmail.com</option>
										<option value="@msn.com">msn.com</option>
										<option value="@hanmir.com">hanmir.com</option>
										<option value="@chol.net">chol.net</option>
										<option value="@korea.com">korea.com</option>
										<option value="@netsgo.com">netsgo.com</option>
										<option value="@dreamwiz.com">dreamwiz.com</option>
										<option value="@hanafos.com">hanafos.com</option>
										<option value="@freechal.com">freechal.com</option>
										<option value="@hitel.net">hitel.net</option>
										<option value="">직접입력</option> -->
									<select style="width:170px" title="이메일 도메인 선택" id="email_select" onchange="customChk(this.value);">
										<option value="">선택</option>
										<option value="custom">직접입력</option>
										<option value="naver.com">naver.com</option>
										<option value="daum.net">daum.net</option>
										<option value="gmail.com">gmail.com</option>
										<option value="nate.com">nate.com</option>
										<option value="yahoo.co.kr">yahoo.co.kr</option>
										<option value="lycos.co.kr">lycos.co.kr</option>
										<option value="empas.com">empas.com</option>
										<option value="hotmail.com">hotmail.com</option>
										<option value="msn.com">msn.com</option>
										<option value="hanmir.com">hanmir.com</option>
										<option value="chol.net">chol.net</option>
										<option value="korea.com">korea.com</option>
										<option value="netsgo.com">netsgo.com</option>
										<option value="dreamwiz.com">dreamwiz.com</option>
										<option value="hanafos.com">hanafos.com</option>
										<option value="freechal.com">freechal.com</option>
										<option value="hitel.net">hitel.net</option>
									</select>
								</div>
								<!--  
								<input type="text" title="도메인 직접 입력" class="ml-10" style="width:164px" id="email2" readonly> <!-- [D] 직접입력시 인풋박스 출력 -->
								-->
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>휴대폰 번호</label></th>
						<td>
							<div class="input-cover">
								<div class="select">
									<select style="width:110px" title="휴대폰 앞자리 선택" id="phone_num1">
										<option value="010">010</option>
										<option value="011">011</option>
										<option value="016">016</option>
										<option value="017">017</option>
										<option value="018">018</option>
										<option value="019">019</option>
									</select>
								</div>
								<span class="txt">-</span>
								<input type="text" title="휴대폰 가운데 번호 입력" style="width:110px" id="phone_num2">
								<span class="txt">-</span>
								<input type="text" title="휴대폰 마지막 번호 입력" style="width:110px" id="phone_num3">
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>파일첨부</label></th>
						<td>
							<div class="filebox no-photo">
								<input class="upload-nm h-medium" value="파일선택" disabled="disabled">
								<label for="up_filename" class="btn-basic ">찾기</label> 
								<input type="file" id="up_filename" name="up_filename[]" class="upload-hidden"> 
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
			<p class="att pt-10"><span class="point-color">*</span> 표시는 필수항목입니다.</p>
			<div class="btnPlace mt-20 mb-40">
				<button class="btn-line  h-large" type="button" id="btnCancel"><span>취소</span></button>
				<button class="btn-point h-large" type="submit" id="btnSubmit"><span>등록</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > AS 접수 -->

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">
<input type=hidden name=type_menu value="<?=$type_menu ?>">
</form>

<script Language="JavaScript">
var NowYear=parseInt(<?=date('Y')?>);
var NowMonth=parseInt(<?=date('m')?>);
var NowDay=parseInt(<?=date('d')?>);
var NowTime=parseInt(<?=time()?>);

$(document).ready(function(){

	var up_email = '';
	var up_storetel = '';
	
// 	$("#subject").click(function(){
// 		var url = "../front/myqna_view.php";
// 		$(location).attr('href',url);
// 	});

	$("input[name='date1'], input[name='date2']").click(function(){
		Calendar(event);
	});
	$(".CLS_cal_btn").click(function(){
		$(this).prev().find("input[type='text']").focus();
		$(this).prev().find("input[type='text']").trigger('click');
	});
	
	$("#btnSubmit").click(function(){
		if(check_form()){
			if($("#mode").val() == ""){
				$("#mode").val("insert");
			}
			//EmailChange ();
			document.getElementById("email").value = $('#email1').val() +'@'+ $('#email2').val();
			document.getElementById("ph").value = document.getElementById("phone_num1").value +"-"+document.getElementById("phone_num2").value +"-"+document.getElementById("phone_num3").value;
			$("#write_form").submit();
		}
	});
	$("#btnCancel").click(function(){
		window.location.reload();
	});
	$(".btn-close").click(function(){
		window.location.reload();
	});

	//파일첨부 파일명 설정
    $("#up_filename").change(function(){
        var filename = $("#up_filename").val();
		$(".txt-box").text(filename);
		$("input[name=ori_filename]").val(getFilename(filename));
    });
	// 20170404 1:1 벨리데이션 체크 끝   
	 
});

//상세페이지
// function go_view(idx){
// 	var url = "../front/mypage_personalview.php?idx="+idx;
// 	$(location).attr('href',url);
// }

function getMonthDays(sYear,sMonth) {
	var Months_day = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31)
	var intThisYear = new Number(), intThisMonth = new Number();
	datToday = new Date();													// 현재 날자 설정
	
	intThisYear = parseInt(sYear);
	intThisMonth = parseInt(sMonth);


	
	if (intThisYear == 0) intThisYear = datToday.getFullYear();				// 값이 없을 경우
	if (intThisMonth == 0) intThisMonth = parseInt(datToday.getMonth())+1;	// 월 값은 실제값 보다 -1 한 값이 돼돌려 진다.
	

	if ((intThisYear % 4)==0) {													// 4년마다 1번이면 (사로나누어 떨어지면)
		if ((intThisYear % 100) == 0) {
			if ((intThisYear % 400) == 0) {
				Months_day[2] = 29;
			}
		} else {
			Months_day[2] = 29;
		}
	}
	intLastDay = Months_day[intThisMonth];										// 마지막 일자 구함
	return intLastDay;
}

function ChangeDate(gbn) {
	year=document.form1[gbn+"_year"].value;
	month=document.form1[gbn+"_month"].value;
	totdays=getMonthDays(year,month);

	MakeDaySelect(gbn,1,totdays);
}

function MakeDaySelect(gbn,intday,totdays) {
	document.form1[gbn+"_day"].options.length=totdays;
	for(i=1;i<=totdays;i++) {
		var d = new Option(i);
		document.form1[gbn+"_day"].options[i] = d;
		document.form1[gbn+"_day"].options[i].value = i;
	}
	document.form1[gbn+"_day"].selectedIndex=intday;
}

function GoSearch2(gbn, obj) {
	var s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "TODAY":
			break;
		case "1WEEK":
			s_date.setDate(s_date.getDate()-7);
			break;
		case "15DAY":
			s_date.setDate(s_date.getDate()-15);
			break;
		case "1MONTH":
			s_date.setMonth(s_date.getMonth()-1);
			break;
		case "3MONTH":
			s_date.setMonth(s_date.getMonth()-3);
			break;
		case "6MONTH":
			s_date.setMonth(s_date.getMonth()-6);
			break;
		case "9MONTH":
			s_date.setMonth(s_date.getMonth()-9);
			break;
		case "12MONTH":
			s_date.setFullYear(s_date.getFullYear()-1);
			break;
		default :
			break;
	}
	e_date = new Date(NowTime*1000);

	//======== 시작 날짜 셋팅 =========//
	var s_month_str = str_pad_right(parseInt(s_date.getMonth())+1);
	var s_date_str = str_pad_right(parseInt(s_date.getDate()));
	
	// 폼에 셋팅
	document.form2.s_year.value = s_date.getFullYear();
	document.form2.s_month.value = s_month_str;
	document.form2.s_day.value = s_date_str;
	//날짜 칸에 셋팅
	var s_date_full = s_date.getFullYear()+"-"+s_month_str+"-"+s_date_str;
	document.form1.date1.value=s_date_full;
	//======== //시작 날짜 셋팅 =========//
	
	//======== 끝 날짜 셋팅 =========//
	var e_month_str = str_pad_right(parseInt(e_date.getMonth())+1);
	var e_date_str = str_pad_right(parseInt(e_date.getDate()));

	// 폼에 셋팅
	document.form2.e_year.value = e_date.getFullYear();
	document.form2.e_month.value = e_month_str;
	document.form2.e_day.value = e_date_str;

	document.form2.day_division.value = gbn;
	
	//날짜 칸에 셋팅
	var e_date_full = e_date.getFullYear()+"-"+e_month_str+"-"+e_date_str;
	document.form1.date2.value=e_date_full;
	//======== //끝 날짜 셋팅 =========//
	
	/*
	document.form1.s_year.value=parseInt(s_date.getFullYear());
	document.form1.s_month.value=parseInt(s_date.getMonth());
	document.form1.e_year.value=NowYear;
	document.form1.e_month.value=NowMonth;
	totdays=getMonthDays(parseInt(s_date.getFullYear()),parseInt(s_date.getMonth()));
	MakeDaySelect("s",parseInt(s_date.getDate()),totdays);
	totdays=getMonthDays(NowYear,NowMonth);
	MakeDaySelect("e",NowDay,totdays);
	document.form1.submit();
	*/
}

function str_pad_right(num){
	
	var str = "";
	if(num<10){
		str = "0"+num;
	}else{
		str = num;
	}
	return str;
}

function isNull(obj){
	return (typeof obj !="undefined" && obj != "")?false:true;
}

// top 메뉴 추가 
function CheckTopMenuForm(obj) {
	document.form2.type_menu.value = obj; 
	document.form2.gotopage.value = 1; 
	CheckForm();
}


function CheckForm() {

	//##### 시작날짜 셋팅
	var sdatearr = "";
	var str_sdate = document.form1.date1.value;
	if(!isNull(document.form1.date1.value)){
		sdatearr = str_sdate.split("-");
		if(sdatearr.length==3){
		// 폼에 셋팅
			document.form2.s_year.value = sdatearr[0];
			document.form2.s_month.value = sdatearr[1];
			document.form2.s_day.value = sdatearr[2];
		}
	}
	var s_date = new Date(parseInt(sdatearr[0]),parseInt(sdatearr[1]),parseInt(sdatearr[2]));
	
	//##### 끝 날짜 셋팅
	var edatearr = "";
	var str_edate = document.form1.date2.value;
	if(!isNull(document.form1.date2.value)){
		edatearr = str_edate.split("-");
		if(edatearr.length==3){
		// 폼에 셋팅
			document.form2.e_year.value = edatearr[0];
			document.form2.e_month.value = edatearr[1];
			document.form2.e_day.value = edatearr[2];
		}
	}
	var e_date = new Date(parseInt(edatearr[0]),parseInt(edatearr[1]),parseInt(edatearr[2]));

	if(s_date>e_date) {
		alert("조회 기간이 잘못 설정되었습니다. 기간을 다시 설정해서 조회하시기 바랍니다.");
		return;
	}
	
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}


// 20170404 1:1 입력 팝업창 
//파일명 추출
function getFilename(filename) {

	var fileValue = filename.split("\\");
	var fileName = fileValue[fileValue.length-1]; // 파일명

	return fileName;
}

function check_form() {
	var procSubmit = true;

	$(".required_value").each(function(){
		if(!$(this).val()){
			if($(this).attr('label') == "상담유형"){
				alert($(this).attr('label')+"을 선택해 주세요");
			}else if($(this).attr('label') == "상담유형"){
				alert($(this).attr('label')+"을 입력해 주세요");
			}else{
				alert($(this).attr('label')+"를 정확히 입력해 주세요");
			}	
			$(this).focus();
			procSubmit = false;
			return false;
		}
	})
	
	if(procSubmit){
		return true;
	}else{
		return false;
	}
}
// 20170404 팝업창 끝

// 벨리데이션 체크 [직접입력] 시 입력가능
// function EmailChange (){
// 	var email_select = document.getElementById("email_select").value;
// 	if(email_select == ''){
// 		document.getElementById("email").value = document.getElementById("contact_email").value +"@"+document.getElementById("email2").value;
// 		document.getElementById("email2").readOnly = false;
// 	} else {
// 		document.getElementById("email").value = document.getElementById("contact_email").value +document.getElementById("email_select").value;
// 		document.getElementById("email2").readOnly = true;
// 	}
// }

// 20170404 삭제폼
function deleteForm(obj){
	var con_test = confirm("삭제 하시겠습니까?");
	if(con_test == true){ 
		$("#mode").val("delete");
		$("#idx").val(obj);
		$("#write_form").submit();
	}
}

// 수정폼
function editForm(idx,table) {

	$("#mode").val("modify");
	$("#idx").val(idx);
	
	$.ajax({
		type: "POST",
		url: "ajax_common_change.php",
		data: "idx="+idx+"&table="+table,
		dataType:"JSON"
	}).done(function(data){

		// 데이터는 비동식으로 전부 받아옴 
		// product_code
		$("#product_code").val(data[0]['productcode']).attr("selected", "selected");
		$("#pop_subject").val(data[0]['subject']);
		var content = data[0]['content'].replace(/<br[^>]*>/gi,"\n");	// 줄바꿈처리
		$("#pop_content").val(content);
		var emails = data[0]['email'].split("@");
		if(emails[0] != null){
// 	 		$("#contact_email").val(emails[0]);
	 		$("#email1").val(emails[0]);
	 	}
	 	if(emails[1] != null){
	 		//$("#email2").val(emails[1]);
	 		var temp_mail = '';
			var arr_mail = new Array( 'naver.com', 'daum.net', 'gmail.com','nate.com', 'yahoo.co.kr', 'lycos.co.kr','empas.com', 'hotmail.com', 'gmail.com','hanmir.com','chol.net','korea.com','netsgo.com','dreamwiz.com','hanafos.com','freechal.com' );
			for ( var i = 0; i < arr_mail.length; i++ ) {
		        if(arr_mail[i] === emails[1]){
			        temp_mail = emails[1];
		        } 
	      	}
			
	      	if(temp_mail == ''){
	      		$('#email2').val(emails[1]);
	    		$('#email2').show();
	      		$("#email_select").val("custom").attr("selected", "selected");
	      	} else {
				$("#email_select").val(temp_mail).attr("selected", "selected");
	      	}
	 	}
	 	var phones = data[0]['phone'].split("-");
	 	if(phones[0] != null){
	 		var res = phones[0].substr(0, 3);
	 		$("#phone_num1").val(res).attr("selected", "selected");
	 	}
	 	if(phones[1] != null){
	 		$("#phone_num2").val(phones[1]);
	 	}
	 	if(phones[2] != null){
	 		$("#phone_num3").val(phones[2]);
	 	}
	});
 	$('#btnSubmit').text('수정');
 	$('.pop-asReg').show();
}

function customChk(str){
	
	$('#email2').val(str);
	
	if(str=='custom'){
		$('#email2').val('');
		$('#email2').show();
	}
}

</script>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>