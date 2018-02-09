<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	include_once($Dir."conf/cscenter_ascode.php");

	header("Content-type: application/vnd.ms-excel");
	Header("Content-Disposition: attachment; filename=onlineList.xls"); 
	Header("Pragma: no-cache"); 
	Header("Expires: 0");
	Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	Header("Content-Description: PHP4 Generated Data");

	$mode=$_POST["mode"];
	$excel_sql=$_POST["excel_sql"];
	$excel_sql_orderby=$_POST["excel_sql_orderby"];

	$sql=$excel_sql.$excel_sql_orderby;
	$result=pmysql_query($sql);
	$sqlnum=pmysql_num_rows($result);

	$thstyle="style='background-color:#f0f0f0; text-align:center;'";
	$tdstyle_center="style='text-align:center'";

	#매장정보 가져오기
	$store_sql="select store_code, name from tblstore ";
	$store_result=pmysql_query($store_sql);
	while($store_data=pmysql_fetch_object($store_result)){
		$store_name[$store_data->store_code]=$store_data->name;
	}

	#브랜드정보 가져오기
	$brand_sql="select bridx, brandname from tblproductbrand ";
	$brand_result=pmysql_query($brand_sql);
	while($brand_data=pmysql_fetch_object($brand_result)){
		$brand_name[$brand_data->bridx]=$brand_data->brandname;
	}

	$colspannum=35+count($as_progress);

	//수선 항목 전부 배열에 저장
	for($i=1;$i<="18";$i++){
		$process_check=${"as_process_".$i};
		foreach($process_check as $rc=>$rcv){
			$process_array[$rc]=$rcv;
		}
	}

	//외부심의 반품 배열에 저장
	for($i=1;$i<="2";$i++){
		$outreviewgoods_check=${"as_outreviewgoods_".$i};
		foreach($outreviewgoods_check as $orc=>$orcv){
			$outrevie_array[$orc]=$orcv;
		}
	}

	
?>
	<table>
		<Tr style="font-size:30px;">
			<th colspan=<?=$colspannum?>>[온라인 AS] CS등록 리스트</th>
		</tr>
		<tr>
			<td>등록건수 : <?=$sqlnum?>건</td>
		</tr>
	</table>
	<table border=1>
		<tr>
			<th <?=$thstyle?>>구분</th>
			<th <?=$thstyle?>>구입일</th>
			<th <?=$thstyle?>>접수일</th>
			<th <?=$thstyle?>>접수번호</th>
			<th <?=$thstyle?>>주문번호</th>
			<th <?=$thstyle?>>결제방식</th>
			<th <?=$thstyle?>>접수매장</th>
			<th <?=$thstyle?>>구입매장</th>
			<th <?=$thstyle?>>고객명</th>
			<th <?=$thstyle?>>연락처</th>
			<th <?=$thstyle?>>수령지주소</th>
			<th <?=$thstyle?>>브랜드</th>
			<th <?=$thstyle?>>상품정보</th>
			<th <?=$thstyle?>>스타일코드</th>
			<th <?=$thstyle?>>컬러코드</th>
			<th <?=$thstyle?>>사이즈</th>
			<th <?=$thstyle?>>수량</th>
			<th <?=$thstyle?>>접수유형</th>
			<th <?=$thstyle?>>구입금액</th>
			<th <?=$thstyle?>>감가적용</th>
			<th <?=$thstyle?>>요청사항</th>
			<th <?=$thstyle?>>송장번호</th>
			<th <?=$thstyle?>>고객부담 택배비</th>
			<th <?=$thstyle?>>택배비 수령</th>
			<th <?=$thstyle?>>처리상태</th>
			<th <?=$thstyle?>>처리내용</th>
			<?foreach($as_progress as $ak => $akv){?>
				<th <?=$thstyle?>><?=$akv?></th>
			<?}?>
			<th <?=$thstyle?>>상세현황</th>
			<th <?=$thstyle?>>상세처리</th>
			<th <?=$thstyle?>>수선비용</th>
			<th <?=$thstyle?>>유상수선비 가격</th>
			<th <?=$thstyle?>>유상수선비 선불여부</th>
			<th <?=$thstyle?>>현금영수증 발행</th>
			<th <?=$thstyle?>>현금영수증 발행정보1</th>
			<th <?=$thstyle?>>현금영수증 발행정보2</th>
			<th <?=$thstyle?>>업체명</th>
		</tr>
		
<?while($data=pmysql_fetch_object($result)){
	$st_cost="0";
	$cash_name="";
	$step_detail="";
	#구입일
	$order_date=substr($data->ordercode,'0','4').'-'.substr($data->ordercode,'4','2').'-'.substr($data->ordercode,'6','2');
	#접수일
	$receipt_date=substr($data->regdt,'0','4').'-'.substr($data->regdt,'4','2').'-'.substr($data->regdt,'6','2');
	#수선비 분리
	//if($data->complete_type=="1") $st_cost=$data->complete_cost;
	#현금영수증 발행정보
	if($data->cash_detail_type=="1") $cash_name="소득공제용";
	else if ($data->cash_detail_type=="2") $cash_name="지출증빙용(사업자등록번호)";

	//상세현황 

	if($as_progress_class[$data->step_code]){
		$progress_qry="select * from tblcsasreceiptdetail where receipt_no='".$data->no."' and process_name='".$as_progress_class[$data->step_code]."' order by no ";
		$progress_result=pmysql_query($progress_qry);
		while($progress_data=pmysql_fetch_array($progress_result)){
			$progress_name_array[$progress_data["process_name"]][$progress_data["no"]]=$progress_data;
		}
		if($as_progress_class[$data->step_code]=="return"){
			$step_detail="[".$as_return[$data->c_return]."]";
		}else if($as_progress_class[$data->step_code]=="repair"){
			if(count($progress_name_array[$as_progress_class[$data->step_code]])){
				foreach($progress_name_array[$as_progress_class[$data->step_code]] as $ar=>$arv){
					
					if($arv["as_code"]=="process_text"){
						$repair_array[]=$arv["process_title"];
					}else{
						$repair_array[]=$process_array[$arv["as_code"]];
					}
					$st_cost+=$arv["process_price"];
				}
			}
			$step_detail="[".implode("] [",$repair_array)."]";
		}else if($as_progress_class[$data->step_code]=="returngoods"){
			if(count($progress_name_array[$as_progress_class[$data->step_code]])){
				foreach($progress_name_array[$as_progress_class[$data->step_code]] as $ar=>$arv){
					$returngoods_array[]=$as_returngoods[$arv["as_code"]];
				}
			}
			$step_detail="[".implode("] [",$returngoods_array)."]";

		}else if($as_progress_class[$data->step_code]=="reviewreturn"){
			$step_detail="[".$as_reviewreturn[$data->c_reviewreturn]."]";
		}else if($as_progress_class[$data->step_code]=="outreviewgoods"){
			
			if(count($progress_name_array[$as_progress_class[$data->step_code]])){
				foreach($progress_name_array[$as_progress_class[$data->step_code]] as $ar=>$arv){
					$outreviewgoods_array[]=$outrevie_array[$arv["as_code"]];
				}
			}
			$step_detail="[".implode("] [",$outreviewgoods_array)."]";

		
		}else if($as_progress_class[$data->step_code]=="outreviewreturn"){

			if(count($progress_name_array[$as_progress_class[$data->step_code]])){
				foreach($progress_name_array[$as_progress_class[$data->step_code]] as $ar=>$arv){
					$outreviewreturn_array[]=$as_outreviewreturn[$arv["as_code"]];
				}
			}
			$step_detail="[".implode("] [",$outreviewreturn_array)."]";
		}

	}


	//수령 지주소

	if($data->place_type=="1"){
		$address = $data->place_addr;
		$zonecode	= $data->place_zipcode;
	}else{
		$address = str_replace("\n"," ",trim($data->receiver_addr));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$post = str_replace("우편번호 : ","",$post);
		$arpost = explode("-",$post);
		$zonecode	= $post;
	}

	?>
		<tr>
			<td <?=$tdstyle_center?>><?=$as_gubun[$data->as_type]?></td>
			<td <?=$tdstyle_center?>><?=$order_date?></td>
			<td <?=$tdstyle_center?>><?=$receipt_date?></td>
			<td <?=$tdstyle_center?>><?=$data->as_code?></td>
			<td <?=$tdstyle_center?>><?=$data->ordercode?></td>
			<td <?=$tdstyle_center?>><?=$arpm[$data->paymethod[0]]?></td>
			<td <?=$tdstyle_center?>><?=$data->storename?></td>
			<td <?=$tdstyle_center?>><?=$store_name[$data->store_code]?></td>
			<td <?=$tdstyle_center?>><?=$data->sender_name?></td>
			<td <?=$tdstyle_center?>><?=$data->sender_tel?></td>
			<td>[<?=$zonecode?>] <?=$address?></td>
			<td <?=$tdstyle_center?>><?=$brand_name[$data->brand]?></td>
			<td><?=$data->productname?></td>
			<td><?=$data->prodcode?></td>
			<td><?=$data->colorcode?></td>
			<td><?=$data->opt2_name?></td>
			<td <?=$tdstyle_center?>><?=$data->quantity?></td>
			<td <?=$tdstyle_center?>><?=$as_receipt[$data->receipt_type]?></td>
			<td><?=number_format($data->price-$data->coupon_price)?></td>
			<td <?=$tdstyle_center?>><?=$as_depreciation[$data->depreciation_type]?></td>
			<td><?=$data->requests_text?></td>
			<td <?=$tdstyle_center?>><?=$data->complete_delinumber?></td>
			<td><?=number_format($data->delivery_cost)?></td>
			<td <?=$tdstyle_center?>><?=$data->delivery_receipt?></td>
			<td <?=$tdstyle_center?>><?=$as_progress[$data->step_code]?></td>
			<td <?=$tdstyle_center?>><?=$as_complete[$data->complete_type]?></td>
			<?foreach($as_progress as $ak => $akv){
				$receipt_date="";
				list($time_check)=pmysql_fetch("select regdt from tblcsaslog where receipt_no='".$data->no."' and step_code='".$ak."'");
				#날짜변경
				if($time_check) $receipt_date=substr($time_check,'0','4').'-'.substr($time_check,'4','2').'-'.substr($time_check,'6','2');
			?>
				<td <?=$tdstyle_center?>><?=$receipt_date?></td>
			<?}?>
			<td><?=$step_detail?></td>
			<td <?=$tdstyle_center?>><?if($as_complete_class[$data->complete_type]=="completeon"){ echo $as_complete_detail[$data->complete_detail]; } else { echo "-"; }?></td>
			<td><?=number_format($st_cost)?></td>
			<td><?=number_format($data->complete_cost)?></td>
			<td <?=$tdstyle_center?>><?if($as_receipt_class[$data->receipt_type]=="rowon"){ echo $as_repair[$data->repairs_type]; } else { echo "-"; }?></td>
			<td <?=$tdstyle_center?>><?if($as_receipt_class[$data->receipt_type]=="rowon"){ echo $as_cash[$data->cash_type]; } else { echo "-"; }?></td>
			<td <?=$tdstyle_center?>><?if($as_cash_class[$data->cash_type]=="cashon" && $as_receipt_class[$data->receipt_type]=="rowon"){ echo $cash_name; } else { echo "-"; }?></td>
			<td <?=$tdstyle_center?>><?if($as_cash_class[$data->cash_type]=="cashon" && $as_receipt_class[$data->receipt_type]=="rowon"){ echo $data->cash_detail_num; } else { echo "-"; }?></td>
			<td><?=$data->complete_store?></td>
		
		</tr>
<?}?>

</table>