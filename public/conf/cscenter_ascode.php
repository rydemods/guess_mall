<?

#################온라인 as등록###################

#as구분
$as_gubun=array("1"=>"온라인 EB A/S", "2"=>"온라인 브랜드 A/S");

#as접수유형
$as_receipt=array("1"=>"유상수선","2"=>"무상수선","3"=>"수선재접수","4"=>"심의","5"=>"심의재접수");
$as_receipt_class=array("1"=>"rowon", "2"=>"rowoff", "3"=>"rowoff", "4"=>"rowoff", "5"=>"rowoff");
#as유상수선비
$as_repair=array("F"=>"선불","L"=>"후불");

#as감가적용
$as_depreciation=array("Y"=>"받음","N"=>"안받음");

#as현금영수증
$as_cash=array("Y"=>"Y","N"=>"N");
$as_cash_class=array("Y"=>"cashon","N"=>"cashoff");



#################온라인 as요청서#####################

#as 진행상태
$as_progress=array(
			"progress_a01"=>"AS접수","progress_a02"=>"제품도착","progress_a03"=>"수선처발송","progress_a04"=>"회송",
			"progress_b01"=>"수선중","progress_b02"=>"수선완료","progress_b03"=>"고객발송",
			"progress_c01"=>"심의중","progress_c02"=>"A/S반품","progress_c03"=>"교환처리","progress_c04"=>"반품처리","progress_c05"=>"심의회송",
			"progress_d01"=>"외부심의중","progress_d02"=>"외부심의반품","progress_d03"=>"반품처리","progress_d04"=>"반품등록","progress_d05"=>"로케이션이동","progress_d06"=>"외부심의회송"
			);

#as 진행상태 노출순서
$as_progress_sort=array(
			"기본"=>array("progress_a01","progress_a02","progress_a03","progress_a04"),
			"수선"=>array("progress_b01","progress_b02","progress_b03"),
			"심의"=>array("progress_c01","progress_c02","progress_c03","progress_c04","progress_c05"),
			"외부심의"=>array("progress_d01","progress_d02","progress_d03","progress_d04","progress_d05","progress_d06")
			);

#as 상태별 노출 클레스 설정
$as_progress_class=array(
			"progress_a04"=>"return",
			"progress_b01"=>"repair",
			"progress_b02"=>"repair",
			"progress_b03"=>"repair",
			"progress_c02"=>"returngoods",
			"progress_c04"=>"returngoods",
			"progress_c05"=>"reviewreturn",
			"progress_d02"=>"outreviewgoods",
			"progress_d03"=>"outreviewgoods",
			"progress_d04"=>"outreviewgoods",
			"progress_d05"=>"outreviewgoods",
			"progress_d06"=>"outreviewreturn"
			);

#처리결과
$as_result=array("1"=>"반품요청","2"=>"반품완료","3"=>"교환완료");

#as 회송
$as_return=array("1"=>"고객과실제품훼손","2"=>"수선불가","3"=>"수선거부","4"=>"이상없음","5"=>"오배송","6"=>"기타");

#as 심의회송
$as_reviewreturn=array("1"=>"고객과실제품훼손","2"=>"수선불가","3"=>"수선거부","4"=>"이상없음","5"=>"오배송","6"=>"기타");

#as 외부심의회송
$as_outreviewreturn=array("outreviewreturn_a01"=>"세탁한자","outreviewreturn_a02"=>"소비자","outreviewreturn_a03"=>"수선가능","outreviewreturn_a04"=>"이상없음");

#as 반품처리내용
$as_returngoods=array("returngoods_a01"=>"갑피불량","returngoods_a02"=>"벨크로(밴드) 불량","returngoods_a03"=>"뒷축불량","returngoods_a04"=>"접착불량","returngoods_a05"=>"아웃솔불량","returngoods_a06"=>"앞코불량","returngoods_a07"=>"이염관련불량","returngoods_a08"=>"사이즈(짝발) 불량","returngoods_a09"=>"인솔불량","returngoods_a10"=>"끈(장식) 불량","returngoods_a11"=>"기타");

#as 반품처리
$as_returngoods2=array("1"=>"기부","2"=>"사판","3"=>"폐기","4"=>"벤더","5"=>"기타");

#as 외부심의 반품 처리내용
##구분
//$as_outreviewgoods_1=array("outreviewgoods_a01"=>"갑피불량","outreviewgoods_a02"=>"벨크로(밴드) 불량","outreviewgoods_a03"=>"뒷축불량");
$as_outreviewgoods_1=array("outreviewgoods_a01"=>"제조,판매업자","outreviewgoods_a02"=>"공동","outreviewgoods_a03"=>"보류 및 심의요");
##상세
$as_outreviewgoods_2=array("outreviewgoods_b01"=>"갑피불량","outreviewgoods_b02"=>"벨크로(밴드) 불량","outreviewgoods_b03"=>"뒷축불량","outreviewgoods_b04"=>"접착불량","outreviewgoods_b05"=>"아웃솔불량","outreviewgoods_b06"=>"앞코불량","outreviewgoods_b07"=>"이염관련불량","outreviewgoods_b08"=>"사이즈(짝발) 불량","outreviewgoods_b09"=>"인솔불량","outreviewgoods_b10"=>"끈(장식) 불량","outreviewgoods_b11"=>"형태불량","outreviewgoods_b12"=>"기타");



#######################as 처리상세###########################
//접착수선
$as_process_1=array("process_a01"=>"아웃솔접착","process_a02"=>"스트랩접착","process_a03"=>"로고접착","process_a04"=>"갑피접착","process_a05"=>"액세서리 접착","process_a06"=>"기타");
//재봉수선
$as_process_2=array("process_b01"=>"갑피 재봉","process_b02"=>"뒤축 재봉","process_b03"=>"벨크로 재봉","process_b04"=>"설포 재봉","process_b05"=>"발볼 재봉","process_b06"=>"로고 재봉","process_b07"=>"기타");
//덧댐수선
$as_process_3=array("process_c01"=>"갑보","process_c02"=>"밑창 덧댐","process_c03"=>"갑피 덧댐","process_c04"=>"도리 덧댐","process_c05"=>"기타");
//작업성수선
$as_process_4=array("process_d01"=>"무두질","process_d02"=>"볼늘림","process_d03"=>"보풀제거","process_d04"=>"뒤축 보강","process_d05"=>"전창갈이","process_d06"=>"염색","process_d07"=>"세탁","process_d08"=>"인솔제작","process_d09"=>"아일렛 교체","process_d10"=>"지퍼 교체","process_d11"=>"벨크로교체","process_d12"=>"장식교체","process_d13"=>"기타");
//덧댐수선
$as_process_5=array("process_e01"=>"갑보(일반)","process_e02"=>"갑보(우라_반)","process_e03"=>"갑보(우라_전체)","process_e04"=>"도리(일반)","process_e05"=>"도리(우라_반)","process_e06"=>"도리(우라_전체)");
$as_process_5_price=array("process_e01"=>"10000","process_e02"=>"12000","process_e03"=>"15000","process_e04"=>"10000","process_e05"=>"12000","process_e06"=>"15000");
//아웃솔 수선
$as_process_6=array("process_f01"=>"가공T/L","process_f02"=>"가공T/L<br>(스펀지추가삽입)","process_f03"=>"굽갈이(여성화)","process_f04"=>"전창갈이","process_f05"=>"반창(앞부분)","process_f06"=>"밑창 전체덧댐");
$as_process_6_price=array("process_f01"=>"8000","process_f02"=>"15000","process_f03"=>"3000","process_f04"=>"50000","process_f05"=>"15000","process_f06"=>"25000");
//뒤축수선
$as_process_7=array("process_g01"=>"월형보강");
$as_process_7_price=array("process_g01"=>"20000");
//갑피수선
$as_process_8=array("process_h01"=>"갑피기모(1EA)","process_h02"=>"갑피기모(2EA 이상)","process_h03"=>"창기모(1EA)","process_h04"=>"창기모(2EA 이상)","process_h05"=>"본드,오염제거","process_h06"=>"이염제거");
$as_process_8_price=array("process_h01"=>"7000","process_h02"=>"14000","process_h03"=>"15000","process_h04"=>"20000","process_h05"=>"15000","process_h06"=>"40000");
//작업성 수선
$as_process_9=array("process_i01"=>"펀칭");
$as_process_9_price=array("process_i01"=>"3000");
//접착수선
$as_process_10=array("process_j01"=>"접착(일반)","process_j02"=>"청접착(일반)","process_j03"=>"청접착(부분)","process_j04"=>"청접착(전체)");
$as_process_10_price=array("process_j01"=>"5000","process_j02"=>"5000","process_j03"=>"10000","process_j04"=>"15000");
//재봉수선
$as_process_11=array("process_k01"=>"미싱/일반(1EA)","process_k02"=>"미싱/일반(2EA 이상)","process_k03"=>"미싱(우라)","process_k04"=>"미싱(골프)","process_k05"=>"미싱(골프_전체)","process_k06"=>"설포고정","process_k07"=>"중창재봉");
$as_process_11_price=array("process_k01"=>"5000","process_k02"=>"10000","process_k03"=>"10000","process_k04"=>"10000","process_k05"=>"20000","process_k06"=>"5000","process_k07"=>"5000");
//작업성수선
$as_process_12=array("process_l01"=>"무두질","process_l02"=>"볼늘림","process_l03"=>"보풀제거","process_l04"=>"인솔제작(스펀지)","process_l05"=>"인솔제작(합피)","process_l06"=>"인솔제작(가죽)","process_l07"=>"스트랩제작","process_l08"=>"열처리보정");
$as_process_12_price=array("process_l01"=>"3000","process_l02"=>"3000","process_l03"=>"3000","process_l04"=>"7000","process_l05"=>"15000","process_l06"=>"20000","process_l07"=>"25000","process_l08"=>"8000");
//세탁,염색
$as_process_13=array("process_m01"=>"세탁(털부츠)","process_m02"=>"염색");
$as_process_13_price=array("process_m01"=>"","process_m02"=>"");
//세탁,염색
$as_process_14=array("process_n01"=>"밴드교체","process_n02"=>"벨크로교체");
$as_process_14_price=array("process_n01"=>"8000","process_n02"=>"8000");
//지퍼수선
$as_process_15=array("process_o01"=>"지퍼교체","process_o02"=>"지퍼고리 제작","process_o03"=>"지퍼교체(앵클)","process_o04"=>"지퍼교체(하프)","process_o05"=>"지퍼교체(롱)");
$as_process_15_price=array("process_o01"=>"15000","process_o02"=>"10000","process_o03"=>"15000","process_o04"=>"20000","process_o05"=>"30000");
//부자재수선
$as_process_16=array("process_p01"=>"아울렛(1EA)","process_p02"=>"장식(1EA)");
$as_process_16_price=array("process_p01"=>"3000","process_p02"=>"5000");
//시즌제품 창기모
$as_process_17=array("process_q01"=>"쪼리 창기모(외발)","process_q02"=>"쪼리 창기모(양발)");
$as_process_17_price=array("process_q01"=>"10000","process_q02"=>"20000");
//부자재수선
$as_process_18=array("process_r01"=>"탄성끈 교체","process_r02"=>"보풀제거","process_r03"=>"스토퍼 교체","process_r04"=>"ANNA 리벳 교체","process_r05"=>"14019버클교체","process_r06"=>"샌들 감각고리교체(1EA)","process_r07"=>"샌들 삼각고리 교체 (2EA 이상)");
$as_process_18_price=array("process_r01"=>"10000","process_r02"=>"3000","process_r03"=>"5000","process_r04"=>"3000","process_r05"=>"3000","process_r06"=>"5000","process_r07"=>"10000");

###############################################################################


#as 처리정보
$as_complete=array("1"=>"유상수선","2"=>"무상수선","3"=>"A/S반품","4"=>"벤더반품","5"=>"외부심의반품","6"=>"외부심의회송","7"=>"회송","8"=>"기타");
$as_complete_class=array("1"=>"completeoff","2"=>"completeoff","3"=>"completeoff","4"=>"completeoff","5"=>"completeoff","6"=>"completeoff","7"=>"completeoff","8"=>"completeon");

#as 기타 상세처리
$as_complete_detail=array("1"=>"아울렛","2"=>"사내판매","3"=>"기부","4"=>"폐기","5"=>"기타");
/*
exdebug($as_progress);

echo "<table>";
foreach ($as_progress_sort as $k=>$v){
	echo "<tr>";
	echo "<th>".$k."</th>";
	echo "<td >";
	foreach ($v as $lk){
		echo "<input type='radio' ".$checked[$lk]." class='".$as_progress_class[$lk]."'>".$as_progress[$lk]."</input>";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
*/
?>