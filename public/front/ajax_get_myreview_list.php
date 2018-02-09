<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");

$listnum        = $_GET['listnum'];
$page           = $_GET['gotopage'];
$mode           = $_GET['mode'];
$whereDate1     = $_GET['start_date'];
$whereDate2     = $_GET['end_date'];
$bMobile        = $_GET['isMobile'];    // 모바일 여부(PC버젼은 빈값) 

$htmlResult     = "";

if ( $mode == "1" ) {

    $sql  = "SELECT tblResult.* ";
    $sql .= "FROM ";
    $sql .= "   ( ";
    $sql .= "       SELECT a.*, b.regdt  ";
    $sql .= "       FROM tblorderproduct a LEFT JOIN tblorderinfo b ON a.ordercode = b.ordercode ";
    $sql .= "       WHERE b.id = '" . $_ShopInfo->getMemid()  . "' ";
	$sql .= "       AND     (a.op_step = 4 and a.order_conf = '1') ";

    if ( !empty($whereDate1) && !empty($whereDate2) ) {
        $sql .= "       AND ( b.regdt >= '{$whereDate1}' AND b.regdt <= '{$whereDate2}' ) ";
    }

    $sql .= "       ORDER BY a.idx DESC ";
    $sql .= "   ) AS tblResult LEFT ";
    $sql .= "   OUTER JOIN tblproductreview tpr ON tblResult.productcode = tpr.productcode and tblResult.ordercode = tpr.ordercode and tblResult.idx = tpr.productorder_idx ";
    $sql .= "WHERE tpr.productcode is null ";
    $sql .= "ORDER BY tblResult.idx desc ";

    if ( !empty($bMobile) ) {
        $paging = new New_Templet_mobile_paging($sql, 3, $listnum, 'GoPageAjax1', true);
    } else {
        $paging = new amg_Paging($sql, 10, $listnum, 'GoPageAjax', "{$mode}||{$whereDate1}||{$whereDate2}"); 
    }
    $gotopage = $paging->gotopage;

    $sql = $paging->getSql($sql); 
    $review_result = pmysql_query($sql);

    while ( $row = pmysql_fetch_object($review_result) ) { 
        // 상품 정보
        $sub_sql  = "SELECT *, b.brandname "; 
        $sub_sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";
        $sub_sql .= "WHERE a.productcode = '" . $row->productcode . "' ";
        $sub_row = pmysql_fetch_object(pmysql_query($sub_sql));

        // 옵션 정보
        $arrOptions = array();

        // 옵션명 리스트
        $arrOpt1 = array();
        if ( !empty($row->opt1_name) ) {
            $arrOpt1 = explode("@#", $row->opt1_name);
        }

        // 옵션값 리스트
        $arrOpt2 = array();
        if ( !empty($row->opt2_name) ) {
            $arrOpt2 = explode(chr(30), $row->opt2_name);
        }

        // 옵션 정보
        $arrOptions = array();
        for ( $i = 0; $i < count($arrOpt1); $i++ ) {
            if ( $arrOpt1[$i] && $arrOpt2[$i] ) {
                array_push($arrOptions, $arrOpt1[$i] . " : " . $arrOpt2[$i]);
            }
        }

        // 수량
        if ( !empty($row->quantity) ) {
            array_push($arrOptions, "수량 : " . number_format($row->quantity) . "개");
        }

        // 주문일
        $order_date = substr($row->regdt, 0, 3) . "-" . substr($row->regdt, 4, 2) . "-" . substr($row->regdt, 6, 2);
        if ( empty($row->regdt) ) {
            $order_date = substr($row->ordercode, 0, 3) . "-" . substr($row->ordercode, 4, 2) . "-" . substr($row->ordercode, 6, 2);
        }

        $imgUrl = getProductImage($Dir.DataDir.'shopimages/product/',$sub_row->tinyimage);

        if ( !empty($bMobile) ) {
        	//모바일
            $consumer_class = "";
            if ( $sub_row->consumerprice <= 0 || $sub_row->consumerprice == $sub_row->sellprice ){
                $consumer_class = "hide";
            }

        	$htmlResult .= '<p class="date_order">주문날짜 : '.$order_date.'</p>
        							<div class="box_mylist">
        							<div class="content">
        								<a href="../m/productdetail.php?productcode='.$row->productcode.'">
        									<figure class="mypage_goods">
        										<div class="img"><img src="'.getProductImage($Dir.DataDir.'shopimages/product/',$sub_row->tinyimage).'" alt="주문상품 이미지"></div>
        										<figcaption>
        										<p class="brand">['.$sub_row->brandname.']</p>
												<p class="name">'.$sub_row->productname.'</p>';
        	if($sub_row->consumerprice != $sub_row->sellprice){
        		$htmlResult.= '<p class="price"><del>'.number_format($sub_row->consumerprice).'</del>  <span class="point-color">'.number_format($sub_row->sellprice).'</span></p>';
        	}else{
        		$htmlResult.= '<p class="price"> <span class="point-color">'.number_format($sub_row->consumerprice).'</span></p>';
        	}
        	$htmlResult .= '			</figcaption>
        								</figure>
        							</a>
        							<div class="btnwrap">
										<ul class="ea1">
											<li><button type="button" class="btn-def light" onclick="javascript:send_review_write_page(\''.$row->productcode.'\', \''.$row->ordercode.'\', \''.$row->idx.'\',\''.$row->num.'\'	);">리뷰작성</button></li>
										</ul>
									</div>
        						</div>
        					</div>';
        	
        } else {
        	//PC
            $htmlResult .= '
                <tr>
                    <td>' . $order_date . '</td>
                    <td><img class="img-size-mypage" src="' . $imgUrl . '"></td>
                    <td class="ta-l">
                        <span class="brand-color">' . $sub_row->brandname  . '</span><br>
                        <span>' . $sub_row->productname  . '</span><br>
                        <span>' . implode(" / ", $arrOptions) . '</span>
                    </td>
                    <td><a href="javascript:;" onClick="javascript:send_review_write_page(\'' . $row->productcode . '\', \'' . $row->ordercode . '\', \'' . $row->idx . '\');" class="btn-dib-line"><span>리뷰쓰기</span></a></td>
                </tr>';
        }

    } // end of while
    pmysql_free_result($result);

} else {

    // 작성한 리뷰 리스트

    $sql  = "SELECT *  ";
    $sql .= "FROM tblproductreview tpr ";
    $sql .= "WHERE tpr.id = '" . $_ShopInfo->getMemid() . "' ";

    if ( !empty($whereDate1) && !empty($whereDate2) ) {
        $sql .= "AND ( tpr.date >= '{$whereDate1}' AND tpr.date <= '{$whereDate2}' ) ";
    }

    $sql .= "ORDER BY tpr.num desc ";

    if ( !empty($bMobile) ) {
        $paging = new New_Templet_mobile_paging($sql, 3, $listnum, 'GoPageAjax2', true);
    } else {
        $paging = new amg_Paging($sql, 10, $listnum, 'GoPageAjax', "{$mode}||{$whereDate1}||{$whereDate2}");
    }

    $t_count = $paging->t_count;
    $gotopage = $paging->gotopage;

    $sql = $paging->getSql($sql);
    $result = pmysql_query($sql,get_db_conn());

    while($row=pmysql_fetch_object($result)) { 

        // 상품 정보
        $sub_sql  = "SELECT *, b.brandname "; 
        $sub_sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";
        $sub_sql .= "WHERE a.productcode = '" . $row->productcode . "' ";
        $sub_row = pmysql_fetch_object(pmysql_query($sub_sql));

        // 옵션 정보
        $arrOptions = array();

        // 옵션1
        if ( !empty($sub_row->option1) && !empty($row->opt1_name) ) {
            array_push($arrOptions, $sub_row->option1 . " : " . $row->opt1_name);
        }

        // 옵션2
        if ( !empty($sub_row->option2) && !empty($row->opt2_name) ) {
            array_push($arrOptions, $sub_row->option2 . " : " . $row->opt2_name);
        }

        // 수량
        if ( !empty($row->quantity) ) {
            array_push($arrOptions, "수량 : " . number_format($row->quantity) . "개");
        }

        // 별점 
        $marks = '';
        //for ($i = 0; $i < $row->marks; $i++) {
        for ($i = 0; $i < $row->quality+3; $i++) {
//            $marks .= '★';
            $marks .= '<img src="/static/img/common/ico_star.png" />';
        }

        // 주문일
        $order_date = $row->regdt;
        if ( empty($order_date) ) {
            $order_date = substr($row->ordercode, 0, 3) . "-" . substr($row->ordercode, 4, 2) . "-" . substr($row->ordercode, 6, 2);
        }

        // 작성일
        $write_date = substr($row->date, 0, 4) . "-" . substr($row->date, 4, 2) . "-" . substr($row->date, 6, 2);

        // 업로드 이미지 정보
        $arrUpFile = array();

        if ( !empty($row->upfile) ) { array_push($arrUpFile, $row->upfile); }
        if ( !empty($row->upfile2) ) { array_push($arrUpFile, $row->upfile2); }
        if ( !empty($row->upfile3) ) { array_push($arrUpFile, $row->upfile3); }
        if ( !empty($row->upfile4) ) { array_push($arrUpFile, $row->upfile4); }

        // 리뷰 제목/내용
       	$review_title   = $row->subject;
        $review_file   = $row->upfile;
        $review_content = nl2br($row->content);
        //$review_mark = $row->marks * 20;
        $review_mark = ($row->quality+3) * 20;
        $review_best = $row->best_type;
        #파일여부경로
        $filepath = $Dir.DataDir."shopimages/review/";

        $imgUrl = getProductImage($Dir.DataDir.'shopimages/product/',$sub_row->tinyimage);

        if ( !empty($bMobile) ) {
        	//모바일
        	$htmlResult .= '<p class="date_order">주문날짜 : '.$order_date.'</p>
        							<div class="box_mylist">
        							<div class="content">
        								<a href="../m/productdetail.php?productcode='.$row->productcode.'">
        									<figure class="mypage_goods">
        										<div class="img"><img src="'.getProductImage($Dir.DataDir.'shopimages/product/',$sub_row->tinyimage).'" alt="주문상품 이미지"></div>
        										<figcaption>
        										<p class="brand">['.$sub_row->brandname.']</p>
												<p class="name">'.$sub_row->productname.'</p>';
        	if($sub_row->consumerprice != $sub_row->sellprice){
        		$htmlResult.= '<p class="price"><del>'.number_format($sub_row->consumerprice).'</del>  <span class="point-color">'.number_format($sub_row->sellprice).'</span></p>';
        	}else{
        		$htmlResult.= '<p class="price"> <span class="point-color">'.number_format($sub_row->consumerprice).'</span></p>';
        	}
        	$htmlResult .= '			</figcaption>
        								</figure>
        							</a>
        						</div>
        					</div>';
        	 
        	$htmlResult .= '<div class="review_read">
        								<div class="title">
        									<span class="comp-star star-score"><strong style="width:'.$review_mark.'%;">5점만점에 5점</strong></span>
        									<p class="subject">'.$review_title.'</p>';
        									if( is_file($filepath.$review_file) ){
        	$htmlResult .= '			<img src="static/img/icon/icon_photo.png" class="icon_photo" alt="photo">';
        									}
        									if($review_best == "1"){
        	$htmlResult .='			<img src="static/img/icon/icon_best.png" class="icon_best" alt="best">';
        									}
        	$htmlResult .='			</div>
        								<div class="content"> '.$review_content;
        		
        	foreach ( $arrUpFile as $key => $val ) {
        		$htmlResult .= '<img src="' . $Dir.DataDir . 'shopimages/review/' . $val . '" /> <br/>';
        	}
        	$htmlResult .= '<br/>';
        	$htmlResult .='
        				<div class="btn_area">
                        	<button class="btn-function" type="button" onClick="javascript:send_review_write_page(\'' . $row->productcode . '\', \'' . $row->ordercode . '\', \'' . $row->idx . '\', \'' . $row->num . '\');"><span>수정</span></button>
                        	<button class="btn-function" type="button" onClick="javascript:delete_review(\'' . $row->num . '\');"><span>삭제</span></button>
                        </div>';
        	
        	$htmlResult.='</div>
        					</div>';
        	
        } else {
        	//PC
            $htmlResult .= '
                        <tr class="my-write-review" ids="' . $row->num . '">
                            <td>' . $order_date . '</td>
                            <td><img class="img-size-mypage" src="' . $imgUrl . '"></td>
                            <td class="ta-l">
                                <span class="brand-color">' . $sub_row->brandname . '</span><br>
                                <span>' . $sub_row->productname . '</span><br>
                                <span>' . implode(" / ", $arrOptions) . '</span>
                            </td>
                            <td>' . $write_date . '</td>
                            <td>' . $marks . '</td>
                        </tr>
                        <tr class="open-content" style="display:none;">
                            <td colspan="5">
                                <div class="list-tr-open">
                                    <div class="review_contents my-wirte">' 
                                          . $review_title . ' <br/><br/>' 
                                          . $review_content . '<br/><br/>';

                                            foreach ( $arrUpFile as $key => $val ) {
                                                $htmlResult .= "<img src='" . $Dir.DataDir."shopimages/review/" . $val . "' /> <br/>";
                                            }
                                            $htmlResult .= "<br/>";

            $htmlResult .= '		<div class="btn-place">';

            $htmlResult .= '
                <button class="btn-dib-line " type="button" onClick="javascript:send_review_write_page(\'' . $row->productcode . '\', \'' . $row->ordercode . '\', \'' . $row->idx . '\', \'' . $row->num . '\');"><span>수정</span></button>
                <button class="btn-dib-line " type="button" onClick="javascript:delete_review(\'' . $row->num . '\');"><span>삭제</span></button>';


            $htmlResult .= '
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>';
        }
    } 
} 
$htmlResult .= "|||" . $paging->a_prev_page . $paging->print_page . $paging->a_next_page . "|||" . number_format($paging->t_count);

echo $htmlResult;
?>
