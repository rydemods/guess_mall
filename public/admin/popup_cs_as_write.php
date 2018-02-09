<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>게시판 기본기능 설정</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="styleSheet" href="/css/common.css" type="text/css">
<link rel="stylesheet" href="/admin/static/css/crm.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<div class="pop_top_title"><p>엑셀 다운로드</p></div>

<section class="online-as">
	<div class="title">
		<h3><span class="point-txt">C5555546452</span>온라인 AS 요청정보</h3>
		<p><a href="#" class="btn-type">온라인 AS 요청서 출력</a></p>
	</div>
	<div class="clear">
		<div class="content-l">
			<table class="table-th-left">
				<caption></caption>
				<colgroup>
					<col style="width:120px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">접수번호</th>
						<td>B0000001235</td>
					</tr>
					<tr>
						<th scope="row">접수일</th>
						<td>2016-09-29 10:10:10</td>
					</tr>
					<tr>
						<th scope="row">구분</th>
						<td>온라인 브랜드 A/S</td>
					</tr>
					<tr>
						<th scope="row">주문채널/<br>대표주문번호</th>
						<td><strong>[PC] <span class="point-txt">G16846695669</span></strong></td>
					</tr>
					<tr>
						<th scope="row">PG사 주문번호</th>
						<td>201609168794949658</td>
					</tr>
					<tr>
						<th scope="row">주문일</th>
						<td>2016-09-29 10:10:10</td>
					</tr>
					<tr>
						<th scope="row">AS 요청자</th>
						<td>홍길동(honghong)</td>
					</tr>
					<tr>
						<th scope="row">결제</th>
						<td>
							<div>
								<p>- 실시간계좌이체</p>
								<p>- 실결제금액 : <strong class="point-txt">660,000원</strong></p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="content-r">
			<div class="cont-box">
				<table class="table-th-top">
				<caption></caption>
				<thead>
					<tr class="bg">
						<th scope="col"><strong>처리이력</strong></th>
						<th scope="col" class="ta_r">온라인 AS 요청상품 :<span class="point-txt">G16846695669</span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							<div class="scroll">
								<div> <!-- [D] 리스트 반복 -->
									<strong>상태</strong>
									<p class="name">AS접수 홍길동(honghong)</p>
									<div class="date-sort clear">
										<div class="type calendar">
											<div class="box">
												<input type="text" title="일자별 시작날짜" value="2016-06-21">
												<button type="button">달력 열기</button>
											</div>
											<select name="" class="ml_5 select">
												<option value="">00시</option>
												<option value="">01시</option>
											</select>
											<span>시</span>
											<select name="" class="select">
												<option value="">00분</option>
												<option value="">01분</option>
											</select>
											<span>분</span>
											<select name="" class="select">
												<option value="">00초</option>
												<option value="">01초</option>
											</select>
											<span>초</span>
										</div>
									</div>
								</div><!-- // [D] 리스트 반복 -->
								<div class="btn-bottom"><a href="#" class="btn-type c1">처리이력 저장</a></div>
							</div>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="mt_40 btn-set">
		<a href="#" class="btn-type c1">저장</a>
		<a href="#" class="btn-type c2">닫기</a>
	</div>

	<div class="order-info">
		<h3>주문자 정보</h3>
		<table class="table-th-left">
			<caption>주문자 정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">주문자이름</th>
					<td><input type="text" name="" style="width:125px" class="input" value="홍길동" title="주문자 이름"></td>
				</tr>
				<tr>
					<th scope="row">전화번호</th>
					<td><input type="text" name="" style="width:125px" class="input" value="041-999-5555" title="전화번호"></td>
				</tr>
				<tr>
					<th scope="row">휴대전화</th>
					<td><input type="text" name="" style="width:125px" class="input" value="010-1111-1111" title="휴대전화"></td>
				</tr>
				<tr>
					<th scope="row">이메일</th>
					<td><input type="text" name="" style="width:200px" class="input" value="hong@naver.com" title="주소"></td>
				</tr>
				<tr>
					<th scope="row">주소</th>
					<td>
						<div>
							<input type="text" name="" id="" title="우편번호 앞자리" value="111" style="width:40px;">
							<span class="dash">-</span>
							<input type="text" name="" id="" title="우편번호 뒷자리" value="111" style="width:40px;">
						</div>
						<div class="input-wrap">
							<input type="text" name="" id="" title="주소" value="서울시 강남구 논현동" style="width:350px;">
							<input type="text" name="" id="" title="상세주소"  value="123-45번지" style="width:350px;">
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<table class="table-th-top">
			<caption>상담 가능한 연락처</caption>
			<colgroup>
				<col style="width:210px">
				<col style="width:auto">
			</colgroup>
			<thead>
				<tr>
					<th scope="col" colspan="2">상담 가능한 연락처 <span class="btn-small"><a href="#" class="btn-type c2">수정</a></span></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						· 이름:
						<input type="text" name="" id="" title="우편번호 뒷자리" value="111" style="width:125px;">
					</td>
					<td>
						· 전화번호:
						<select name="s_check" class="select" style="width:40px;">
							<option value="">02</option>
							<option value="">031</option>
						</select>
						<span class="dash">-</span>
						<input type="text" name="" id="" title="우편번호 뒷자리" value="111" style="width:40px;">
						<span class="dash">-</span>
						<input type="text" name="" id="" title="우편번호 뒷자리" value="111" style="width:40px;">
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<table class="table-th-top">
			<caption>교환신청 상품 수령지</caption>
			<thead>
				<tr>
					<th scope="col" colspan="2">교환신청 상품 수령지 <span class="btn-small"><a href="#" class="btn-type c2">수령지 변경</a></span></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<p>[받는 분] 홍길동 / 041-555-5555 / 010-5555-5555</p>
						<p>대전광역시 중구 선화동 165-446 15</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="mt_40">
		<h3>
			A/S 진행 상태
			<div class="btn-wrap">
				<input type="radio" name="" value="" checked="">
				<span class="btn-small"><a href="#" class="btn-type c2">제품도착</a></span>
				<span>
					<a href="#" class="btn-line02 open">모든 상태 열림</a>
					<a href="#" class="btn-line02 close">모든 상태 닫힘</a>
				</span>
			</div>
		</h3>
		<table class="table-th-left">
			<caption>A/S 진행 상태</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">기본</th>
					<td>
						<div class="radio-set">
							<input id="radio01" type="radio" name="" value="" checked="">
							<label for="radio01" class="point-txt bold">AS접수</label>
							<input id="radio02" type="radio" name="" value="">
							<label for="radio02">제품도착</label>
							<input id="radio03" type="radio" name="" value="">
							<label for="radio03">수선처 발송</label>
							<input id="radio04" type="radio" name="" value="">
							<label for="radio04">회송</label>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">수선</th>
					<td>
						<div class="radio-set">
							<input id="radio05" type="radio" name="" value="" checked="">
							<label for="radio05">수선중</label>
							<input id="radio06" type="radio" name="" value="">
							<label for="radio06">수선완료</label>
							<input id="radio07" type="radio" name="" value="">
							<label for="radio07">고객발송</label>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">심의</th>
					<td>
						<div class="radio-set">
							<input id="radio05" type="radio" name="" value="" checked="">
							<label for="radio05">심의중</label>
							<input id="radio06" type="radio" name="" value="">
							<label for="radio06">A/S반품</label>
							<input id="radio07" type="radio" name="" value="">
							<label for="radio07">교환처리</label>
							<input id="radio08" type="radio" name="" value="">
							<label for="radio08">반품처리</label>
							<input id="radio09" type="radio" name="" value="">
							<label for="radio09">심의회송</label>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">외부심의</th>
					<td>
						<div class="radio-set">
							<input id="radio10" type="radio" name="" value="" checked="">
							<label for="radio10">외부심의중</label>
							<input id="radio11" type="radio" name="" value="">
							<label for="radio11">외부심의반품</label>
							<input id="radio12" type="radio" name="" value="">
							<label for="radio12">반품처리</label>
							<input id="radio13" type="radio" name="" value="">
							<label for="radio13">반품등록</label>
							<input id="radio14" type="radio" name="" value="">
							<label for="radio14">로케이션이동</label>
							<input id="radio15" type="radio" name="" value="">
							<label for="radio15">외부심의회송</label>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>A/S 신청 상품</h3>
		<table class="table-th-top02">
			<caption>A/S 신청 상품</caption>
			<colgroup>
				<col style="width:8%">
				<col style="width:19%">
				<col style="width:8%">
				<col style="width:10%">
				<col style="width:10%">
				<col style="width:5%">
				<col style="width:10%">
				<col style="width:8%">
				<col style="width:8%">
				<col style="width:auto">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">상품<br>주문번호</th>
					<th scope="col">상품명</th>
					<th scope="col">옵션<br>(사이즈)</th>
					<th scope="col">정상가</th>
					<th scope="col">판매가</th>
					<th scope="col">주문<br>수량</th>
					<th scope="col">총금액</th>
					<th scope="col">상태</th>
					<th scope="col">중고매장</th>
					<th scope="col">배송정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>20116-09-07<br><ins>C4664646486</ins></td>
					<td>
						<div class="product-info clear">
							<img src="../admin/static/img/test/@test_img.gif" alt="">
							<div class="pro-title">
								<strong>NIKE</strong>
								<p>우먼스나이키 에어팩스
								인비고 [76646-400]</p>
							</div>
						</div>
					</td>
					<td>235</td>
					<td>1,000,000원</td>
					<td>1,000,000원</td>
					<td>13</td>
					<td>1,000,000원</td>
					<td>A/S접수</td>
					<td>B2C온라인직영</td>
					<td>
						<strong>현대택배</strong>
						<p>66464634679864</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>처리결과</h3>
		<table class="table-th-left">
			<caption>처리결과</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">현재 주문</th>
					<td>
						<select name="s_check" class="select" style="width:143px;">
							<option value="">선택하세요</option>
							<option value="">옵션</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>A/S 접수 정보</h3>
		<table class="table-th-left">
			<caption>A/S 접수 정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">접수매장</th>
					<td>
						<select name="s_check" class="select" style="width:143px;">
							<option value="">B2C온라인직영</option>
							<option value="">옵션</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">접수유형</th>
					<td>
						<div class="radio-set">
							<input id="radio-a01" type="radio" name="" value="" checked="">
							<label for="radio-a01">유상수선</label>
							<input id="radio-a02" type="radio" name="" value="">
							<label for="radio-a02">무상수선</label>
							<input id="radio-a03" type="radio" name="" value="">
							<label for="radio-a03">수선재접수</label>
							<input id="radio-a04" type="radio" name="" value="">
							<label for="radio-a04">심의</label>
							<input id="radio-a05" type="radio" name="" value="">
							<label for="radio-a05">심의재접수</label>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">감가적용</th>
					<td>
						<div class="radio-set">
							<input id="radio-a06" type="radio" name="" value="" checked="">
							<label for="radio-a06">받음</label>
							<input id="radio-a07" type="radio" name="" value="">
							<label for="radio-a07">안받음</label>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">요쳥사항</th>
					<td>
						<textarea style="width:100%; height:80px"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>AS 처리 정보</h3>
		<table class="table-th-left">
			<caption>AS 처리 정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:360px">
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">접수유형</th>
					<td>무상수선</td>
					<th scope="row">수선비</th>
					<td><input type="text" name="" id="" title="수선비" value="" style="width:100px;"> 원</td>
				</tr>
				<tr>
					<th scope="row">처리내용</th>
					<td colspan="3">
						<div class="radio-set">
							<input id="radio-b01" type="radio" name="" value="" checked="">
							<label for="radio-b01">유상수선</label>
							<input id="radio-b02" type="radio" name="" value="">
							<label for="radio-b02">무상수선</label>
							<input id="radio-b03" type="radio" name="" value="">
							<label for="radio-b03">A/S반품</label>
							<input id="radio-b04" type="radio" name="" value="">
							<label for="radio-b04">벤더반품</label>
							<input id="radio-b05" type="radio" name="" value="">
							<label for="radio-b05">외부심의반품</label>
							<input id="radio-b06" type="radio" name="" value="">
							<label for="radio-b06">외부심의회송</label>
							<input id="radio-b07" type="radio" name="" value="">
							<label for="radio-b07">회송</label>
							<input id="radio-b08" type="radio" name="" value="">
							<label for="radio-b08">기타</label>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">업체명 (밴더사)</th>
					<td colspan="3"><input type="text" name="" id="" title="수선비" value="" style="width:510px;"></td>
				</tr>
				<tr>
					<th scope="row">발송 운송장</th>
					<td colspan="3">
						<select name="s_check" class="select" style="width:113px;">
							<option value="">택배사선택</option>
							<option value="">옵션</option>
						</select>
						<input type="text" name="" id="" title="수선비" value="" style="width:393px;">
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>택배비</h3>
		<table class="table-th-left">
			<caption>택배비</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:360px">
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">고객부담 택배비</th>
					<td><input type="text" name="" id="" title="고객부담 택배비" value="" style="width:100px;"></td>
					<th scope="row">택배비 수령</th>
					<td><input type="text" name="" id="" title="택배비 수령" value="" style="width:100px;"></td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- [D] 20161006 AS 처리 상세 -->
	<div class="mt_40 as-detail">
		<h3>AS 처리 상세</h3>
		<table class="table-th-left">
			<colgroup>
				<col width="100px">
				<col width="*">
			</colgroup>
			<tbody>
			<tr>
				<th scope="row">접착수선</th>
				<td>
					<input type="checkbox" name="adhesion_repair_cd" id="adhesion_repair_cd_100" value="100"><label for="adhesion_repair_cd_100">아웃솔접착</label>&nbsp;&nbsp;
					<input type="checkbox" name="adhesion_repair_cd" id="adhesion_repair_cd_110" value="110"><label for="adhesion_repair_cd_110">스트랩접착</label>&nbsp;&nbsp;
					<input type="checkbox" name="adhesion_repair_cd" id="adhesion_repair_cd_120" value="120"><label for="adhesion_repair_cd_120">로고접착</label>&nbsp;&nbsp;
					<input type="checkbox" name="adhesion_repair_cd" id="adhesion_repair_cd_130" value="130"><label for="adhesion_repair_cd_130">갑피접착</label>&nbsp;&nbsp;
					<input type="checkbox" name="adhesion_repair_cd" id="adhesion_repair_cd_140" value="140"><label for="adhesion_repair_cd_140">액세서리 접착</label>&nbsp;&nbsp;
					<input type="checkbox" name="adhesion_repair_cd" id="adhesion_repair_cd_150" value="150"><label for="adhesion_repair_cd_150">기타</label>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<th scope="row"><p>재봉수선</p></th>
				<td>
					<input type="checkbox" name="sewing_repair_cd" id="sewing_repair_cd_100" value="100"><label for="sewing_repair_cd_100">갑피 재봉</label>&nbsp;&nbsp;
					<input type="checkbox" name="sewing_repair_cd" id="sewing_repair_cd_110" value="110"><label for="sewing_repair_cd_110">뒤축 재봉</label>&nbsp;&nbsp;
					<input type="checkbox" name="sewing_repair_cd" id="sewing_repair_cd_120" value="120"><label for="sewing_repair_cd_120">벨크로 재봉</label>&nbsp;&nbsp;
					<input type="checkbox" name="sewing_repair_cd" id="sewing_repair_cd_130" value="130"><label for="sewing_repair_cd_130">설포 재봉</label>&nbsp;&nbsp;
					<input type="checkbox" name="sewing_repair_cd" id="sewing_repair_cd_140" value="140"><label for="sewing_repair_cd_140">발볼 재봉</label>&nbsp;&nbsp;
					<input type="checkbox" name="sewing_repair_cd" id="sewing_repair_cd_150" value="150"><label for="sewing_repair_cd_150">로고 재봉</label>&nbsp;&nbsp;
					<input type="checkbox" name="sewing_repair_cd" id="sewing_repair_cd_160" value="160"><label for="sewing_repair_cd_160">기타</label>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<th scope="row">덧댐수선</th>
				<td>
					<input type="checkbox" name="add_repair_cd" id="add_repair_cd_100" value="100"><label for="add_repair_cd_100">갑보</label>&nbsp;&nbsp;
					<input type="checkbox" name="add_repair_cd" id="add_repair_cd_110" value="110"><label for="add_repair_cd_110">밑창 덧댐</label>&nbsp;&nbsp;
					<input type="checkbox" name="add_repair_cd" id="add_repair_cd_120" value="120"><label for="add_repair_cd_120">갑피 덧댐</label>&nbsp;&nbsp;
					<input type="checkbox" name="add_repair_cd" id="add_repair_cd_130" value="130"><label for="add_repair_cd_130">도리 덧댐</label>&nbsp;&nbsp;
					<input type="checkbox" name="add_repair_cd" id="add_repair_cd_140" value="140"><label for="add_repair_cd_140">기타</label>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<th scope="row">작업성수선</th>
				<td>
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_100" value="100"><label for="work_repair_cd_100">무두질</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_110" value="110"><label for="work_repair_cd_110">볼늘림</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_120" value="120"><label for="work_repair_cd_120">보풀제거</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_130" value="130"><label for="work_repair_cd_130">뒤축 보강</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_140" value="140"><label for="work_repair_cd_140">전창갈이</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_150" value="150"><label for="work_repair_cd_150">염색</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_160" value="160"><label for="work_repair_cd_160">세탁</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_170" value="170"><label for="work_repair_cd_170">인솔제작</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_180" value="180"><label for="work_repair_cd_180">아일렛 교체</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_190" value="190"><label for="work_repair_cd_190">지퍼 교체</label>&nbsp;&nbsp;<br><br>
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_200" value="200"><label for="work_repair_cd_200">벨크로교체</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_210" value="210"><label for="work_repair_cd_210">장식교체</label>&nbsp;&nbsp;
					<input type="checkbox" name="work_repair_cd" id="work_repair_cd_220" value="220"><label for="work_repair_cd_220">기타</label>&nbsp;&nbsp;
				</td>
			</tr>
		</tbody>
		</table>



		<table cellspacing="0" cellpadding="0" class="table-th-left" style="border-top:0;">
			<colgroup>
				<col width="100px">
				<col>
				<col width="80px">
				<col width="100px">
				<col>
				<col width="80px">
				<col width="100px">
				<col>
				<col width="80px">
			</colgroup>
			<tbody>
			<tr>
				<th rowspan="6" scope="row">덧댐수선</th>
				<td><label style="float:left" for="ascode_A10"><input name="ascode[]" id="ascode_A10" value="A10" type="checkbox">갑보(일반)</label></td>
				<td><input type="text" name="asval_A10" id="asval_A10" value="10,000" class="input" style="width:60px"></td>

				<th rowspan="4" scope="row">접착수선</th>
				<td><label style="float:left" for="ascode_F10"><input name="ascode[]" id="ascode_F10" value="F10" type="checkbox">접착(일반)</label></td>
				<td><input type="text" name="asval_F10" id="asval_F10" value="10,000" class="input" style="width:60px"></td>

				<th rowspan="2" scope="row"><p>벨크로,밴드수선</p></th>
				<td><label style="float:left" for="ascode_K10"><input name="ascode[]" id="ascode_K10" value="K10" type="checkbox">밴드교체 </label></td>
				<td><input type="text" name="asval_K10" id="asval_K10" value="10,000" class="input" style="width:60px"></td>

			</tr>
			<tr>
				<td><label style="float:left" for="ascode_A11"><input name="ascode[]" id="ascode_A11" value="A11" type="checkbox">갑보(우라_반)</label></td>
				<td><input type="text" name="asval_A11" id="asval_A11" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_F11"><input name="ascode[]" id="ascode_F11" value="F11" type="checkbox">창접착(일반)</label></td>
				<td><input type="text" name="asval_F11" id="asval_F11" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_K11"><input name="ascode[]" id="ascode_K11" value="K11" type="checkbox">벨크로교체 </label></td>
				<td><input type="text" name="asval_K11" id="asval_K11" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_A12"><input name="ascode[]" id="ascode_A12" value="A12" type="checkbox">갑보(우라_전체)</label></td>
				<td><input type="text" name="asval_A12" id="asval_A12" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_F12"><input name="ascode[]" id="ascode_F12" value="F12" type="checkbox">창접착(부분)</label></td>
				<td><input type="text" name="asval_F12" id="asval_F12" value="10,000" class="input" style="width:60px"></td>
				<th rowspan="5" scope="row">지퍼수선</th>

				<td><label style="float:left" for="ascode_L10"><input name="ascode[]" id="ascode_L10" value="L10" type="checkbox">지퍼교체</label></td>
				<td><input type="text" name="asval_L10" id="asval_L10" value="10,000" class="input" style="width:60px"></td>
			</tr>

			<tr>
				<td><label style="float:left" for="ascode_A13"><input name="ascode[]" id="ascode_A13" value="A13" type="checkbox">도리(일반) </label></td>
				<td><input type="text" name="asval_A13" id="asval_A13" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_F13"><input name="ascode[]" id="ascode_F13" value="F13" type="checkbox">창접착(전체)</label></td>
				<td><input type="text" name="asval_F13" id="asval_F13" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_L11"><input name="ascode[]" id="ascode_L11" value="L11" type="checkbox">지퍼고리 제작 </label></td>
				<td><input type="text" name="asval_L11" id="asval_L11" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_A14"><input name="ascode[]" id="ascode_A14" value="A14" type="checkbox">도리(우라_반)</label></td>
				<td><input type="text" name="asval_A14" id="asval_A14" value="10,000" class="input" style="width:60px"></td>

				<th rowspan="7" scope="row">재봉수선</th>
				<td><label style="float:left" for="ascode_G10"><input name="ascode[]" id="ascode_G10" value="G10" type="checkbox">미싱/일반(1EA) </label></td>
				<td><input type="text" name="asval_G10" id="asval_G10" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_L12"><input name="ascode[]" id="ascode_L12" value="L12" type="checkbox">지퍼교체(앵클) </label></td>
				<td><input type="text" name="asval_L12" id="asval_L12" value="10,000" class="input" style="width:60px"></td>

			</tr>
			<tr>
				<td><label style="float:left" for="ascode_A15"><input name="ascode[]" id="ascode_A15" value="A15" type="checkbox">도리(우라_전체)</label></td>
				<td><input type="text" name="asval_A15" id="asval_A15" value="15,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_G11"><input name="ascode[]" id="ascode_G11" value="G11" type="checkbox">미싱/일반(2EA 이상)</label></td>
				<td><input type="text" name="asval_G11" id="asval_G11" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_L13"><input name="ascode[]" id="ascode_L13" value="L13" type="checkbox">지퍼교체(하프)</label></td>
				<td><input type="text" name="asval_L13" id="asval_L13" value="10,000" class="input" style="width:60px"></td>

			</tr>
			<tr>
				<th rowspan="6" scope="row"><p>아웃솔  수선</p></th>
				<td><label style="float:left" for="ascode_B10"><input name="ascode[]" id="ascode_B10" value="B10" type="checkbox">가공T/L </label></td>
				<td><input type="text" name="asval_B10" id="asval_B10" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_G12"><input name="ascode[]" id="ascode_G12" value="G12" type="checkbox">미싱(우라)</label></td>
				<td><input type="text" name="asval_G12" id="asval_G12" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_L14"><input name="ascode[]" id="ascode_L14" value="L14" type="checkbox">지퍼교체(롱)</label></td>
				<td><input type="text" name="asval_L14" id="asval_L14" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_B11"><input name="ascode[]" id="ascode_B11" value="B11" type="checkbox">가공T/L<br>  (스펀지추가삽입) </label></td>
				<td><input type="text" name="asval_B11" id="asval_B11" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_G13"><input name="ascode[]" id="ascode_G13" value="G13" type="checkbox">미싱(골프)</label></td>
				<td><input type="text" name="asval_G13" id="asval_G13" value="10,000" class="input" style="width:60px"></td>
				<th rowspan="2" scope="row"><p>부자재수선</p></th>

				<td><label style="float:left" for="ascode_M10"><input name="ascode[]" id="ascode_M10" value="M10" type="checkbox">아일렛(1EA) </label></td>
				<td><input type="text" name="asval_M10" id="asval_M10" value="10,000" class="input" style="width:60px"></td>

			</tr>
			<tr>
				<td><label style="float:left" for="ascode_B12"><input name="ascode[]" id="ascode_B12" value="B12" type="checkbox">굽갈이(여성화)</label></td>
				<td><input type="text" name="asval_B12" id="asval_B12" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_G14"><input name="ascode[]" id="ascode_G14" value="G14" type="checkbox">미싱(골프_전체)</label></td>
				<td><input type="text" name="asval_G14" id="asval_G14" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_M11"><input name="ascode[]" id="ascode_M11" value="M11" type="checkbox">장식(1EA)</label></td>
				<td><input type="text" name="asval_M11" id="asval_M11" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_B13"><input name="ascode[]" id="ascode_B13" value="B13" type="checkbox">전창갈이</label></td>
				<td><input type="text" name="asval_B13" id="asval_B13" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_G15"><input name="ascode[]" id="ascode_G15" value="G15" type="checkbox">설포고정</label></td>
				<td><input type="text" name="asval_G15" id="asval_G15" value="10,000" class="input" style="width:60px"></td>

				<th rowspan="2" scope="row"><p>시즌제품 창기모</p></th>
				<td><label style="float:left" for="ascode_N10"><input name="ascode[]" id="ascode_N10" value="N10" type="checkbox">쪼리 창기모(외발) </label></td>
				<td><input type="text" name="asval_N10" id="asval_N10" value="10,000" class="input" style="width:60px"></td>

			</tr>
			<tr>
				<td><label style="float:left" for="ascode_B14"><input name="ascode[]" id="ascode_B14" value="B14" type="checkbox">반창(앞부분)</label></td>
				<td><input type="text" name="asval_B14" id="asval_B14" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_G16"><input name="ascode[]" id="ascode_G16" value="G16" type="checkbox">중창재봉</label></td>
				<td><input type="text" name="asval_G16" id="asval_G16" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_N11"><input name="ascode[]" id="ascode_N11" value="N11" type="checkbox">쪼리 창기모(양발) </label></td>
				<td><input type="text" name="asval_N11" id="asval_N11" value="10,000" class="input" style="width:60px"></td>

			</tr>
			<tr>
				<td><label style="float:left" for="ascode_B15"><input name="ascode[]" id="ascode_B15" value="B15" type="checkbox">밑창 전체덧댐</label></td>
				<td><input type="text" name="asval_B15" id="asval_B15" value="10,000" class="input" style="width:60px"></td>

				<th rowspan="8" scope="row"><p>작업성수선</p></th>
				<td><label style="float:left" for="ascode_H10"><input name="ascode[]" id="ascode_H10" value="H10" type="checkbox">무두질 </label></td>
				<td><input type="text" name="asval_H10" id="asval_H10" value="10,000" class="input" style="width:60px"></td>

				<th rowspan="7" scope="row"><p>부자재수선</p></th>
				<td><label style="float:left" for="ascode_P10"><input name="ascode[]" id="ascode_P10" value="P10" type="checkbox">탄성끈 교체 </label></td>
				<td><input type="text" name="asval_P10" id="asval_P10" value="10,000" class="input" style="width:60px"></td>

			</tr>
			<tr>
				<th scope="row">뒤축수선</th>
				<td><label style="float:left" for="ascode_C10"><input name="ascode[]" id="ascode_C10" value="C10" type="checkbox">월형보강</label></td>
				<td><input type="text" name="asval_C10" id="asval_C10" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_H11"><input name="ascode[]" id="ascode_H11" value="H11" type="checkbox">볼늘림</label></td>
				<td><input type="text" name="asval_H11" id="asval_H11" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_P11"><input name="ascode[]" id="ascode_P11" value="P11" type="checkbox">보풀제거 </label></td>
				<td><input type="text" name="asval_P11" id="asval_P11" value="10,000" class="input" style="width:60px"></td>

			</tr>
			<tr>
				<th rowspan="6" scope="row">갑피수선</th>
				<td><label style="float:left" for="ascode_D10"><input name="ascode[]" id="ascode_D10" value="D10" type="checkbox">갑피기모(1EA)</label></td>
				<td><input type="text" name="asval_D10" id="asval_D10" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_H12"><input name="ascode[]" id="ascode_H12" value="H12" type="checkbox">보풀제거</label></td>
				<td><input type="text" name="asval_H12" id="asval_H12" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_P12"><input name="ascode[]" id="ascode_P12" value="P12" type="checkbox">스토퍼 교체 </label></td>
				<td><input type="text" name="asval_P12" id="asval_P12" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_D11"><input name="ascode[]" id="ascode_D11" value="D11" type="checkbox">갑피기모(2EA 이상)</label></td>
				<td><input type="text" name="asval_D11" id="asval_D11" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_H13"><input name="ascode[]" id="ascode_H13" value="H13" type="checkbox">인솔제작(스펀지) </label></td>
				<td><input type="text" name="asval_H13" id="asval_H13" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_P13"><input name="ascode[]" id="ascode_P13" value="P13" type="checkbox">ANNA 리벳 교체 </label></td>
				<td><input type="text" name="asval_P13" id="asval_P13" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_D12"><input name="ascode[]" id="ascode_D12" value="D12" type="checkbox">창기모(1EA)</label></td>
				<td><input type="text" name="asval_D12" id="asval_D12" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_H14"><input name="ascode[]" id="ascode_H14" value="H14" type="checkbox">인솔제작(합피)</label></td>
				<td><input type="text" name="asval_H14" id="asval_H14" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_P14"><input name="ascode[]" id="ascode_P14" value="P14" type="checkbox">14019버클교체</label></td>
				<td><input type="text" name="asval_P14" id="asval_P14" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_D13"><input name="ascode[]" id="ascode_D13" value="D13" type="checkbox">창기모(2EA 이상)</label></td>
				<td><input type="text" name="asval_D13" id="asval_D13" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_H15"><input name="ascode[]" id="ascode_H15" value="H15" type="checkbox">인솔제작(가죽)</label></td>
				<td><input type="text" name="asval_H15" id="asval_H15" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_P15"><input name="ascode[]" id="ascode_P15" value="P15" type="checkbox">샌들 삼각고리  <br> 교체(1EA) </label></td>
				<td><input type="text" name="asval_P15" id="asval_P15" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_D14"><input name="ascode[]" id="ascode_D14" value="D14" type="checkbox">본드,오염제거</label></td>
				<td><input type="text" name="asval_D14" id="asval_D14" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_H16"><input name="ascode[]" id="ascode_H16" value="H16" type="checkbox">스트랩제작</label></td>
				<td><input type="text" name="asval_H16" id="asval_H16" value="10,000" class="input" style="width:60px"></td>

				<td><label style="float:left" for="ascode_P16"><input name="ascode[]" id="ascode_P16" value="P16" type="checkbox">샌들 삼각고리<br>  교체 (2EA 이상)</label></td>
				<td><input type="text" name="asval_P16" id="asval_P16" value="10,000" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<td><label style="float:left" for="ascode_D15"><input name="ascode[]" id="ascode_D15" value="D15" type="checkbox">이염제거</label></td>
				<td><input type="text" name="asval_D15" id="asval_D15" value="10,000" class="input" style="width:60px"></td>
				<td><label style="float:left" for="ascode_H17"><input name="ascode[]" id="ascode_H17" value="H17" type="checkbox">열처리보정</label></td>
				<td><input type="text" name="asval_H17" id="asval_H17" value="10,000" class="input" style="width:60px"></td>
				<th scope="row">기타</th>


				<td><input type="text" name="etc_name" id="etc_name" class="input" style="width:60px"></td>
				<td><input type="text" name="etc_val" id="etc_val" class="input" style="width:60px"></td>
			</tr>
			<tr>
				<th scope="row">작업성 수선</th>
				<td><label style="float:left" for="ascode_E10"><input name="ascode[]" id="ascode_E10" value="E10" type="checkbox">펀칭</label></td>
				<td><input type="text" name="asval_E10" id="asval_E10" value="10,000" class="input" style="width:60px"></td>

				<th rowspan="2" scope="row">세탁,염색</th>

				<td><label style="float:left" for="ascode_J10"><input name="ascode[]" id="ascode_J10" value="J10" type="checkbox">세탁(털부츠)</label></td>
				<td><input type="text" name="asval_J10" id="asval_J10" value="10,000" class="input" style="width:60px"></td>
				<th scope="row">&nbsp;</th>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th scope="row">&nbsp;</th>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><label style="float:left" for="ascode_J11"><input name="ascode[]" id="ascode_J11" value="J11" type="checkbox">염색</label></td>
				<td><input type="text" name="asval_J11" id="asval_J11" value="10,000" class="input" style="width:60px"></td>
				<th scope="row">&nbsp;</th>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
		</table>
		<p>&nbsp;</p>
	</div>
	</div>
	<!-- // [D] 20161006 AS 처리 상세 -->

	<div class="mt_40">
		<h3>기타</h3>
		<table class="table-th-left">
			<caption>기타</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">CS메모</th>
					<td>
						<textarea style="width:100%; height:300px"></textarea>
						<div class="add-file-cover">
							<div>E:\you\2016\[아자샵] 세정\PSD\PC\03_마이페이지(공통)</div> <!-- 파일 업로드시 파일 주소 출력 -->
							<input type="file" id="add_file">
						</div>
						<div class="btn-wrap1"><span><a href="#" class="btn-type1">이미지추가</a></span></div>
						<div class="txt-box">
							<h4>[AS접수] <strong>홍길동(honghong)</strong> 2016-09-29 10:10;10</h4>
							<div class="cont">
								<p>수선기간 : 양방동봉 안내</p>
								<p>A/S 주소지 문자전송</p>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40 btn-set">
		<a href="#" class="btn-type c1">저장</a>
		<a href="#" class="btn-type c2">닫기</a>
	</div>

</section> <!-- // .online-as -->





<?=$onload?>
</body>
</html>