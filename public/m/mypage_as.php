<?php
include_once('outline/header_m.php');
include_once("../lib/file.class.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl());
	exit;
}

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

# 페이징
$paging = new New_Templet_paging($list_sql, 5,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql( $list_sql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$list[] = $row;
}

// jkm9424@naver.com
$product_select = "SELECT DISTINCT  
					c.productname,
					c.productcode 
						FROM tblorderinfo a, tblorderproduct b, tblproduct c 
							WHERE a.ordercode = b.ordercode 
								AND b.productcode = c.productcode and a.id = '".$_ShopInfo->getMemid()."'";
$product_result = pmysql_query( $product_select, get_db_conn() );
while( $product_row = pmysql_fetch_array( $product_result ) ){
	$product_list[] = $product_row;
}
?>

<!-- 내용 -->
<main id="content" class="subpage">
	<!-- AS 접수 팝업 -->
	<section class="pop_layer layer_as_write">
		<div class="inner">
			<h3 class="title">AS 접수<button type="button" class="btn_close">닫기</button></h3>
			<div class="board_type_write">
				<form name='write_form' id='write_form' action="mypage_asinfowrite.php" method='POST' enctype="multipart/form-data">
				<input type='hidden' id='mode' name='mode' value="<?=$_GET["mode"] ?>">
				<input type='hidden' id='idx' name='idx' value="<?=$_GET["idx"] ?>">
				<input type='hidden' name='chk_mail' value="N">
				<input type='hidden' name='chk_sms' value="N">
				<input type='hidden' name='ori_filename' value="<?=$list_sql->ori_filename ?>">
				<input type='hidden' id="ph" name='hp' value="">
				<input type='hidden' id="email" name='email' value="">
				<dl>
					<dt>상품선택</dt>
					<dd>
						<select class="select_line w100-per" id="product_code" name="product_code"><!-- [D] 배송중, 배송완료 상태인 상품 노출(같은 이름의 상품은 1개만 노출) -->
							<?php 
								foreach( $product_list as $key=>$val ){
									echo "<option value=\"{$val['productcode']}\">{$val['productname']}</option>\n";
								}
							?>
						</select>
					</dd>
				</dl>
				<dl>
					<dt>제목</dt>
					<dd>
						<input type="text" class="w100-per required_value" placeholder="제목 입력(필수)" label="제목" name="pop_subject" id="pop_subject">
					</dd>
				</dl>
				<dl>
					<dt>내용</dt>
					<dd>
						<textarea class="w100-per required_value" rows="6" placeholder="내용 입력(필수)" name="pop_content" id="pop_content"></textarea>
					</dd>
				</dl>
				<dl>
					<dt>답변받을 이메일</dt>
					<dd>
						<div class="input_mail">
							<input type="text" class="" id="contact_email" name="contact_email">
							<span class="at">&#64;</span>
							<select class="select_line" onchange="EmailChange()" id="email_select">
								<option value="@naver.com">naver.com</option>
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
								<option value="">직접입력</option>
							</select>
						</div>
						<input type="text" class="w100-per mt-5" placeholder="직접입력" id="email2" readonly>
					</dd>
				</dl>
				<dl>
					<dt>휴대폰 번호</dt>
					<dd>
						<div class="input_tel">
							<select class="select_line" id="phone_num1">
								<option value="010">010</option>
								<option value="011">011</option>
								<option value="016">016</option>
								<option value="017">017</option>
								<option value="018">018</option>
								<option value="019">019</option>
							</select>
							<span class="dash"></span>
							<input type="tel" maxlength="4" id="phone_num2">
							<span class="dash"></span>
							<input type="tel" maxlength="4" id="phone_num3">
						</div>
					</dd>
				</dl>
				<dl>
					<dt>파일첨부</dt>
					<dd>
						<div class="input_file">
							<input class="upload_name" disabled>
							<label for="up_filename" class="btn-basic h-input">찾기</label>
							<input type="file" id="up_filename" name="up_filename[]" class="upload_hidden">
						</div>
					</dd>
				</dl>

				<div class="btn_area">
					<ul class="ea2">
						<li><a href="javascript:;" class="btn-line h-large" id="btnCancel">취소</a></li>
						<li><a href="javascript:;" class="btn-point h-large" id="btnSubmit">등록</a></li>
					</ul>
				</div>
				</form>
			</div>
		</div>
	</section>
	<!-- //AS 접수 팝업 -->

	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>AS 접수</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="wrap_as sub_bdtop">
		<div class="step_as">
			<dl>
				<dt>
					<div class="icon"><img src="../static/img/icon/icon_step_as01.png" alt="AS 접수"></div>
					<p>AS 접수</p>
				</dt>
				<dd>
					<!-- <p class="tit">- 온라인 AS 게시판 접수</p> -->
					<ul class="list">
						<li>온라인 AS 게시판 접수</li>
						<li>온라인 콜센터 접수</li>
					</ul>
					<!-- <p class="tit mt-5">신원몰 구매 고객</p>
					<ul class="list">
						<li>온라인 AS 게시판 접수</li>
						<li>온라인 콜센터 접수</li>
					</ul> -->
				</dd>
			</dl>
			<dl>
				<dt>
					<div class="icon"><img src="../static/img/icon/icon_step_as02.png" alt="고객만족실"></div>
					<p>고객상담실(1661-2585)</p>
				</dt>
				<dd>
					<ul class="list">
						<li>상품 인수 수선 또는 외부 심의 의뢰</li>
						<li>고객에게 유선 통화</li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>
					<div class="icon"><img src="../static/img/icon/icon_step_as03.png" alt="수선진행"></div>
					<p>수선진행</p>
				</dt>
				<dd>
					<ul class="list">
						<li>고객과실</li>
						<li>수선가능 : 수선 (고객 수선비 부담)</li>
						<li>수선불가 : 회송</li>
						<li>제품하자</li>
						<li>수선가능 : 수선 (신원몰 수선비 부담)</li>
						<li>수선불가 : 교환 / 환불 진행</li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>
					<div class="icon"><img src="../static/img/icon/icon_step_as04.png" alt="수선완료 후 발송"></div>
					<p>수선완료 후 발송</p>
				</dt>
				<dd>
					<ul class="list">
						<li>수선 완료후 발송 혹은 환불</li>
					</ul>
				</dd>
			</dl>
		</div><!-- //.step_as -->

		<div class="as_contact">
			<h3>AS 접수 및 문의 (1661-2585)</h3>
			<dl>
				<dt>주소 : </dt>
				<dd>서울 중랑구 신내로1길 20 신원몰CS<br> CJ대한통운택배중랑대리점</dd>
			</dl>
		</div><!-- //.as_contact -->

		<div class="as_reception">
		<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="date1">
			<input type="hidden" name="date2">
			<div class="check_period">
				<ul>
				<?php 
					if(!$day_division) $day_division = '1MONTH';
					foreach($arrSearchDate as $kk => $vv){
						$dayClassName = "";
						if($day_division != $kk){
							$dayClassName = '';
						}else{
							$dayClassName = 'class="on"';
						}
						//echo "<li ".$dayClassName."><a href=\"javascript:GoSearch2('".$kk."', this);\" >".$vv."</a></li>\n";
				?>
						<li <?=$dayClassName?>><a href="javascript:GoSearch2('<?=$kk?>', this);" ><?=$vv?></a></li>
						<!-- <button type="button" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><span><?=$vv?></span></button> -->
				<?php
					}
				?>
				</ul>
			</div><!-- //.check_period -->
			</form>
			<div class="pt-10 pr-10 pl-10">
			
			<?php 
				if(count($product_list) > 0 ){
					echo "<a href=\"javascript:;\" class=\"btn_as_write btn-line w100-per h-input\">AS 접수하기</a>";
				} else {
					echo "<a href=\"javascript:alert('구매완료된 제품이 없습니다.');\" class=\"btn-line w100-per h-input\">AS 접수하기</a>";
				}
			?>
				
			</div>

				<div class="inquiry_list mt-10">
					<ul class="accordion_list">
					<?php 
						if( count($list) > 0 ) {
							foreach( $list as $key=>$val ){
								echo "<li>";
								echo "	<div class=\"my_inquiry\">";
								echo "		<p class=\"info\"><span class=\"date\">".date_format(date_create($val['date']), "Y.m.d")."</span><strong>".$val['type_mode']."</strong></p>";
								echo "		<p class=\"tit accordion_btn\">".$val['subject']."</p>";
								echo "		<div class=\"btns\">";
								echo "			<a href=\"javascript:;\" class=\"btn_as_write btn-line\" onclick='editForm(".$val['idx'].",\"tblasinfo\");'>수정</a>";
								echo "			<a href=\"javascript:;\" class=\"btn-basic\" onclick='deleteForm(".$val['idx'].");'>삭제</a>";
								echo "		</div>";
								echo "	</div>";
								echo "	<div class=\"qna_con accordion_con\">";
								echo "		<div class=\"question\">";
								echo 			$val['content'];
								
								if($val['up_filename'] != ''){
									echo '<p><img src="'.$filepath.$val['up_filename'].'" alt=""></p>';
								}
								
// 								echo "			<p><strong>주문상품 :</strong> <span>솔리드 심플 벨티드 쟈켓</span></p>";
// 								echo "			<strong>수선의뢰 내용</strong>";
// 								echo "			<p>사이즈가 잘 맞지 않아 입기가 힘듭니다. <br>32사이즈인데 28사이즈 인 듯 합니다.<br>허리사이즈를 늘려주세요.</p>";
								echo "		</div>";
								
								if ($val['status'] == 1 && $val['re_id'] != '') {
									echo "		<div class=\"answer\">";
									echo "			<p class=\"writer\"><span>".$val['re_id']."</span><span class=\"a_date\">".date("Y.m.d", strtotime( $val['re_date']) )."</span></p>";
									echo "			<p class=\"txt\">".$val['re_subject']."</p>";
									echo "			<p class=\"txt\">".$val['re_content']."</p>";
									echo "		</div>";
								}
								
								echo "	</div>";
								echo "</li>";
							}
						} else {
							echo "<li>";
							echo "	<div class=\"my_inquiry\">";
							echo "		<p class=\"tit accordion_btn\">내용이 없습니다.</p>";
							echo "	</div>";
							echo "</li>";
						}
					?>
					<!-- 
						<li>
							<div class="my_inquiry">
								<p class="info"><span class="date">2017.01.20</span><strong>AS 접수</strong></p>
								<p class="tit accordion_btn">궁금하고 또 궁금해서 물어보는데요. 꼭 답변주실 수 있으신거죠? 네? 네?</p>
							</div>
							<div class="qna_con accordion_con">
								<div class="question">
									<p><strong>주문상품 :</strong> <span>솔리드 심플 벨티드 쟈켓</span></p>
									<strong>수선의뢰 내용</strong>
									<p>사이즈가 잘 맞지 않아 입기가 힘듭니다. <br>32사이즈인데 28사이즈 인 듯 합니다.<br>허리사이즈를 늘려주세요.</p>
								</div>
								<div class="answer">
									<p class="writer"><span>관리자</span><span class="a_date">2017.01.14</span></p>
									<p class="txt">- 안녕하세요. 고객님 3월 24일 AS접수가 완료되었습니다.</p>
									<p class="txt">- 3월 27일 수선이 완료되어 발송처리 하였습니다.</p>
								</div>
							</div>
						</li>
						
						<li>
							<div class="my_inquiry">
								<p class="info"><span class="date">2017.01.20</span><strong class="point-color">심의중</strong></p>
								<p class="tit accordion_btn">궁금하고 또 궁금해서 물어보는데요.</p>
								<div class="btns">
									<a href="javascript:;" class="btn_as_write btn-line">수정</a>
									<a href="javascript:;" class="btn-basic">삭제</a>
								</div>
							</div>
							<div class="qna_con accordion_con">
								<div class="question">
									<p><strong>주문상품 :</strong> <span>솔리드 심플 벨티드 쟈켓</span></p>
									<strong>수선의뢰 내용</strong>
									<p>사이즈가 잘 맞지 않아 입기가 힘듭니다. <br>32사이즈인데 28사이즈 인 듯 합니다.<br>허리사이즈를 늘려주세요.</p>
								</div>
							</div>
						</li>
					 -->
						
					</ul>
				</div><!-- //.inquiry_list -->
				
			<div class="list-paginate mt-15">
				<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
			</div><!-- //.list-paginate -->
		</div><!-- //.as_reception -->

	</section><!-- //.wrap_as -->

</main>
<!-- //내용 -->

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
	
	$("#btnSubmit").click(function(){
		if(check_form()){
			if($("#mode").val() == ""){
				$("#mode").val("insert");
			}
			EmailChange ();
			document.getElementById("ph").value = document.getElementById("phone_num1").value +"-"+document.getElementById("phone_num2").value +"-"+document.getElementById("phone_num3").value;
			$("#write_form").submit();
		}
	});
	$("#btnCancel").click(function(){
		window.location.reload();
	});
	$(".btn_close").click(function(){
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
	CheckForm();
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
function EmailChange (){
	var email_select = document.getElementById("email_select").value;
	if(email_select == ''){
		document.getElementById("email").value = document.getElementById("contact_email").value +"@"+document.getElementById("email2").value;
		document.getElementById("email2").readOnly = false;
	} else {
		document.getElementById("email").value = document.getElementById("contact_email").value +document.getElementById("email_select").value;
		document.getElementById("email2").readOnly = true;
	}
}

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
		url: "../front/ajax_common_change.php",
		data: "idx="+idx+"&table="+table,
		dataType:"JSON"
	}).done(function(data){

		// 데이터는 비동식으로 전부 받아옴 
		$("#product_code").val(data[0]['productcode']).attr("selected", "selected");
		$("#pop_subject").val(data[0]['subject']);
		var content = data[0]['content'].replace(/<br[^>]*>/gi,"\n");	// 줄바꿈처리
		$("#pop_content").val(content);
		var emails = data[0]['email'].split("@");
		if(emails[0] != null){
	 		$("#contact_email").val(emails[0]);
	 	}
	 	if(emails[1] != null){
	 		var temp_mail = '';
			var arr_mail = new Array( 'naver.com', 'daum.net', 'gmail.com','nate.com', 'yahoo.co.kr', 'lycos.co.kr','empas.com', 'hotmail.com', 'gmail.com','hanmir.com','chol.net','korea.com','netsgo.com','dreamwiz.com','hanafos.com','freechal.com','msn.com' );
			for ( var i = 0; i < arr_mail.length; i++ ) {
		        if(arr_mail[i] === emails[1]){
			        temp_mail = emails[1];
		        } 
	      	}
			
	      	if(temp_mail == ''){
	      		$("#email_select").val(temp_mail).attr("selected", "selected");
	      		$("#email2").val(temp_mail);
	      		document.getElementById("email2").readOnly = false;
	      	} else {
				$("#email_select").val("@"+temp_mail).attr("selected", "selected");
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

	 	$('#btnSubmit').text('수정');
	 	$('.pop-asReg').show();
	});
}

</script>

<?php
include_once('outline/footer_m.php');
?>