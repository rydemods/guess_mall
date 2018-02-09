<?php
/********************************************************************* 
// 파 일 명     : coupon.class.php
// 설     명    : 쿠폰 관련 함수
// 상세설명     : 쿠폰 불러오는 내용 총합
// 작 성 자     : 2016-05-19 유동혁
// 수 정 자     : 
// 
*********************************************************************/ 
?>
<?php
/*********************************************************************
* tblcouponinfo 내용
=> + 추가, # 고정
    coupon_code                 // 쿠폰 코드
    coupon_name                 // 쿠폰 명
    date_start                  // 사용 시작일 ( 가능한 날짜 )
    date_end                    // 사용 종료일 ( 가능한 날짜 )
    sale_money                  // 쿠폰 할인 금액/할인률 
    amount_floor                // 금액 절삭 ( 1, 10 ,100 - 단위 )
    mini_price                  // 쿠폰사용제한 구매 금액
    productcode                 // 제외 카테고리/상품 선택 구분 - ALL : 전체 / CATEGORY : 제외 카테고리 / GOODS : 제외 상품 ( * 사용안함)
    issue_type                  // 쿠폰 발급조건 - 회원가입시 자동발급 : M / 자동발급 : A
    description                 // 쿠폰 설명
    member                      // 회원 발급용 ( ALL - 전체, NULL - X , tblmembergroup.group_code - 특정 등급 )
    display                     // 쿠폰 발급용 쿠폰 (issue_type=N 일경우, Y 아니면 N)
    issue_no                    // 발급수량 ( * 사용안함 default 0 )
    date                        // 입력일
    vender                      // 벤더코드 tblvenderinfo.vender
    coupon_use_type             // 쿠폰 사용 방법
    coupon_type                 // 쿠폰 발급 구분
    sale_max_money              // 할인 상한 금액
    coupon_is_mobile            // 쿠폰 사용 범위
    time_type                   // 유효 기간 설정 선택 ( D : 기간 / P : 일 )
+    issue_code                  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
+    not_productcode             // 제외 상품 - GOODS / 제외 카테고리 - CATEGORY
+    issue_start                 // 발급 시점 설정 - 시작 +추가
+    issue_end                   // 발급 시점 설정 - 종료 +추가
+    order_accept_quantity       // 발급 충족 구매 수량 +추가
+    order_accept_price          // 발급 충족 구매 금액 (추가필드) +추가
+    one_issue_limit             // 1회 발급 수량 (1 : 1장 발급, 2 : 설정수량 발급) +추가
+    one_issue_quantity          // 1회 발급 설정수량 +추가
+    mini_type                   // 쿠폰사용제한 선택(P : 구매 금액 기준, Q : 구매 상품 수량 기준) +추가
+    mini_quantity               // 쿠폰사용제한 상품 수량 +추가
+    join_rote                   // 경로 +추가
+    issue_status                // 쿠폰발행 상태 (대기, 발급중, 일시중지)

#    sale_type                   // 쿠폰 종류 - 할인쿠폰으로 #고정 
#    bank_only                   // 쿠폰사용가능 결제방법 - 제한 없음으로 #고정
#    use_con_type1               // 다른쿠폰과 같이사용 유무 - 같이 사용가능으로 #고정
#    use_con_type2               // 카테고리/상품 포함/제외 여부 - 제외로 #고정
#    detail_auto                 // 제품 상세 쿠폰 노출 설정 - 노출안함으로 #고정 * 확인필요
#    issue_tot_no                // 총 발행 쿠폰 수 - 무제한으로 #고정 
#    repeat_id                   // 동일인 재발급 가능여부 - 가능으로 #고정
#    use_point                   // 쿠폰과 등급회원 할인/적립 혜택 동시적용 유무 - 동시적용으로 #고정
#    delivery_type               // 쿠폰 사용시 배송비 포함 유무 - 미포함으로 #고정
#    issue_member_no             // 보유가능 쿠폰 수 - NULL로 #고정
#    use_card                    // 사용카드에 따른 유무 - NULL로 고정( 카드쿠폰 -> KCP 카드코드 ) #고정

* tblcouponissue
coupon_code                      // 쿠폰 코드
id                               // 회원 아이디
date_start                       // 사용가능 시작일
date_end                         // 사용가능 종료일
used                             // 사용유무
date                             // 등록일
issue_member_no                  // 
issue_recovery_no                // 회원이 받은 수량
ci_no                            // 일련번호

*********************************************************************/

?>
<?php

class CouponInfo {
    # 쿠폰 기본 설정기능
    public $coupon     = array();
    # 발급 대상 쿠폰
    public $infoData   = array();
    # insert할 쿠폰 정보
    public $issueData  = array();
    # issue data가 있는지 check
    public $issue_type = false;
    # 회원 쿠폰
    public $mem_coupon = array();
    # 쿠폰 사용가능 true false ( 발급부분 X )
    public $coupon_yn  = false;
    # 발급 구분값
    public $type       = '';
    # 발급 구분별 type => 쿠폰 조회시 사용
    public $coupon_type = array(
        # default // 회원 쿠폰만 검색이 가능
        '0'=>array(
            'coupon_type'  => '0',  // 쿠폰 발급 구분
            'issue_code'   => '0',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 즉시발급 (일반)
        '1'=>array(
            'coupon_type'  => '1',  // 쿠폰 발급 구분
            'issue_code'   => '1',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 다운로드 (일반)
        '6'=>array(
            'coupon_type'  => '6',  // 쿠폰 발급 구분
            'issue_code'   => '0',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 페이퍼 (일반)
        '7'=>array(
            'coupon_type'  => '7',  // 쿠폰 발급 구분
            'issue_code'   => '0',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 무료배송 (일반)
        '9'=>array(
            'coupon_type'  => '9',  // 쿠폰 발급 구분
            'issue_code'   => '1',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 회원가입 (자동)
        '2'=>array(
            'coupon_type'  => '2',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 기념일 (자동)
        '3'=>array(
            'coupon_type'  => '3',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 첫구매 (자동)
        '4'=>array(
            'coupon_type'  => '4',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 생일 (자동)
        '10'=>array(
            'coupon_type'  => '10',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 상품구매후기 (자동)
        '11'=>array(
            'coupon_type'  => '11',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 구매 수량 충족 (자동)
        '12'=>array(
            'coupon_type'  => '12',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 구매 금액 충족 (자동)
        '13'=>array(
            'coupon_type'  => '13',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 주말 출석 (자동)
        '14'=>array(
            'coupon_type'  => '14',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 회원 등급별 (자동)
        '15'=>array(
            'coupon_type'  => '15',  // 쿠폰 발급 구분
            'issue_code'   => '2',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        ),
        # 일반 발급용 (일반)
        '16'=>array(
            'coupon_type'  => '16',  // 쿠폰 발급 구분
            'issue_code'   => '0',  // 발급구분 ( 0 - 일반, 1 - 지정, 2 - 자동 )
            'issue_status' => 'Y'   // 쿠폰발행 상태 (대기, 발급중, 일시중지)
        )

    );

    public function CouponInfo( $type = 0 )
    {
        $this->set_coupon( $type );   //쿠폰 기본 설정
    }

    # 쿠폰 기본 설정
    public function set_coupon( $type = 0 )
    {
        //global $_ShopData;

        unset( $this->coupon );
        unset( $this->infoData );
        unset( $this->issueData );
        unset( $this->mem_coupon );
        $this->issue_type = false;
        $rcall_type = 'N';
        $coupon_ok  = 'N';

        $sql    = "SELECT num, amount_floor, useand_pc_yn ";
        $sql   .= "FROM tblcoupon ";
        $sql   .= "ORDER BY num DESC LIMIT 1 ";
        $result = pmysql_query( $sql, get_db_conn() );
        $row    = pmysql_fetch_object( $result );
        pmysql_free_result;

        list( $rcall_type, $coupon_ok ) = pmysql_fetch( " SELECT rcall_type, coupon_ok FROM tblshopinfo LIMIT 1 " );

        $this->coupon = array(
            'amount_floor' => $row->amount_floor,               // 쿠폰 금액 절삭
            'useand_pc_yn' => $row->useand_pc_yn,               // 상품 / 장바구니 동시사용 여부
            'all_type'     => $rcall_type                       // 적립금, 쿠폰 동시사용
        );

        // 쿠폰 사용여부
        if( $coupon_ok == 'Y' ) $this->coupon_yn = true;
        else $this->coupon_yn = false;
        // 쿠폰 type
        $this->type                  = $type;

    }
    # 쿠폰 발급
    public function insert_couponissue( $papercode  = '' )
    {
        // 발급 쿠폰의 정보가 없거나 쿠폰을 사용안함으로 할 경우 발급 X
        if( !$this->issue_type || !$this->coupon_yn ) return 1;
        $insertArr = array();
        $returnArr = array();
        $err       = 0;
        if( count( $this->issueData ) > 0 ){
            BeginTrans();
            $sql = " INSERT INTO tblcouponissue ( coupon_code, id, date_start, date_end, date, ordercode, op_idx ) VALUES  ";
            foreach( $this->issueData as $coupon_code => $issue ){
                foreach( $issue as $v ){
                    // 중복발행 확인
                    if( $v['one_issue_limit'] == 1 || $v['one_issue_limit'] == 0 ){ // 중복발행 N
                        $insertArr[] = "( '".$v['coupon_code']."', '".$v['id']."', '".$v['date_start']."', '".$v['date_end']."', '".date("YmdHis")."', '".$v['ordercode']."', '".$v['op_idx']."' )";
                    } else { // 중복발행 Y
                        for( $i = 0; $i < $v['one_issue_quantity']; $i++ ){ // one_issue_quantity 수 만큼 발행
                            $insertArr[] = "( '".$v['coupon_code']."', '".$v['id']."', '".$v['date_start']."', '".$v['date_end']."', '".date("YmdHis")."', '".$v['ordercode']."', '".$v['op_idx']."' )";
                        }
                    }
                }
            }
            
            $insert_text = implode( ',', $insertArr );
            $sql.= $insert_text;
            $sql.= " RETURNING ci_no, id, coupon_code ";
            if( $result      = pmysql_query( $sql, get_db_conn() ) ){
                $this->insert_coupon_log( $sql );
                // inster된 쿠폰 정보
                $updateArr = array();
                while( $row = pmysql_fetch_object( $result ) ) {
                    $updateArr[] = $row->coupon_code; // ci_no -> coupon_code 변경
                    if( $this->type == '1' || $this->type == '9' ) {
                        $cis_sql = "
                            UPDATE tblcouponissue_standby SET used = 'Y' WHERE id = '".$row->id."' AND coupon_code = '".$row->coupon_code."' AND used = 'N' 
                        ";
                        pmysql_query( $cis_sql, get_db_conn() );
                        if( pmysql_error() ) {
                            $err++;
                        }
                    }
                }
                pmysql_free_result( $result );
                // 쿠폰 발행수량 update
                $up_sql = "
                    UPDATE tblcouponinfo AS ci
                    SET issue_no = p.issue_no_plus
                    FROM (
                        SELECT coupon_code, COUNT( coupon_code ) AS issue_no_plus 
                        FROM tblcouponissue
                        WHERE coupon_code IN ( '".implode( "','", $updateArr )."' )
                        GROUP BY coupon_code
                    ) AS p 
                    WHERE ci.coupon_code = p.coupon_code 
                ";
                pmysql_query( $up_sql, get_db_conn() );
                if( pmysql_error() ) {
                    $err++;
                    RollbackTrans();
                }
                // 페이퍼 쿠폰
                if( $this->type == '7' && strlen( $papercode ) > 0 ){
                    $sqlUsed = "
                        UPDATE tblcouponpaper SET used = 'Y' WHERE papercode='".$papercode."'
                    ";
                    pmysql_query( $sqlUsed, get_db_conn() );
                    if( pmysql_error() ) {
                        $err++;
                        RollbackTrans();
                    }
                }
            } else {
                RollbackTrans();
                $err++;
            }
            
            if( $err === 0 ) {
                CommitTrans();
            }
        } else {
            $err = 1;
        }
        $returnArr = array( $err, $updateArr );
        return $returnArr;
    }

    # 쿠폰 복구 / 관리자 발급 ( 복구 및 관리자 발급은 조건 X )
    public function insert_couponissue_admin( $coupon_code, $id, $admin_type = 0 )
    {
        
        // 쿠폰을 사용안함으로 할 경우 발급 X
        if( !$this->coupon_yn ) return 1;
        $err = 0;
        $admin_sql = "
            SELECT time_type, date_start, date_end 
            FROM  tblcouponinfo 
            WHERE coupon_code = '".$coupon_code."'
        ";
        list( $admin_coupon ) = pmysql_fetch_array( pmysql_query( $admin_sql, get_db_conn() ) );
        $couponDate = $this->get_coupon_date( $admin_coupon['time_type'], $admin_coupon['date_start'], $admin_coupon['date_end'] );
        $sql = " INSERT INTO tblcouponissue ( coupon_code, id, date_start, date_end, date ) VALUES  ";
        $sql.= "( '".$coupon_code."', '".$id."', '".$couponDate->date_start."', '".$couponDate->date_end."', '".date("YmdHis")."' )";
        $sql.= " RETURNING ci_no ";
        BeginTrans();
        if( $result      = pmysql_query( $sql, get_db_conn() ) ){
            $this->insert_coupon_log( $sql );
            $result_rows = pmysql_num_rows( $result );
            pmysql_free_result( $result );
            $up_sql = "UPDATE tblcouponinfo SET issue_no = issue_no + ".$result_rows." WHERE coupon_code = '".$coupon_code."' ";
            pmysql_query( $up_sql, get_db_conn() );
             if( pmysql_error() ) {
                $err++;
                RollbackTrans();
            }
        } else {
            RollbackTrans();
            $err++;
        }

        if( $err === 0 ) CommitTrans();

        return $err;
    }

    # 발급 정보
    public function set_couponissue( $id = '', $skey = '', $ekey = '' )
    {   
        global $_ShopInfo;

        // 쿠폰을 사용안함으로 할 경우 발급 X
        if( !$this->coupon_yn ) return false;
        
        unset( $this->issueData );
        $this->issue_type = false;
        # 쿠폰별 회원 정보 setting
        if( count( $this->infoData ) > 0 ){
            foreach( $this->infoData as $k => $v ){
                # 날짜 변환
                if( $skey != '' && $ekey != '' ){
                    $issueDate  = (object) array( 'date_start' => $skey, 'date_end' => $ekey );
                } else {
                    if( $this->type == '14' ){ // 주말 출석
                        $date_num = date("w");
                        $limit_day	= '0';
                        $limit_day	= $week_num=='6'?'2':$limit_day;
                        $limit_day	= $week_num=='0'?'1':$limit_day;
                        $date_start = date( "Ymd" )."00";
                        $date_end   = date( "Ymd", mktime( 0, 0, 0, date( "m" ), date( "d" ) + (int)$limit_day, date( "y" ) ) )."23";
                        $issueDate  = (object) array( 'date_start' => $date_start, 'date_end' => $date_end );
                    } else if( $this->type == '15' ){ // 회원 등급
                        $date_start = date( "Ymd" )."00";
                        $date_end	= date("Ym").date("t")."23";
                        $issueDate  = (object) array( 'date_start' => $date_start, 'date_end' => $date_end );
                    } else if( $this->type == '10' ){ // 생일쿠폰
                        $date_start = date( "Ymd" )."00";
                        //$date_end	= date("Ym").date("t")."23";
						$date_end=date("Ymd", @mktime(0,0,0,date("m")+1,1,date("Y"))-1)."23"; //해당월의 마지막 날까져오기
                        $issueDate  = (object) array( 'date_start' => $date_start, 'date_end' => $date_end );
                    }else { // 일반
                        $issueDate = $this->get_coupon_date( $v->time_type, $v->date_start, $v->date_end );
                    }
                }
                // id 확인
                if( ord( $v->id ) ) $memid = $v->id;
                else if ( strlen( $id ) > 0 ) $memid = $id;
                // 주문코드 확인
                if( $v->ordercode != '' ) $ordercode = $v->ordercode;
                else $ordercode = '';
                // 주문 상품 idx 확인
                if( $v->op_idx > 0 ) $op_idx = $v->op_idx;
                else $op_idx = 0;

                if( strlen( $memid ) > 0 ) { 

                    $this->issueData[$v->coupon_code][] = array(
                        'coupon_code'        => $v->coupon_code,         // 쿠폰 코드
                        'id'                 => $memid,                  // 회원 id
                        'date_start'         => $issueDate->date_start,  // 사용가능 시작일
                        'date_end'           => $issueDate->date_end,    // 사용가능 종료일
                        'one_issue_limit'    => $v->one_issue_limit,     // 증복발급 type
                        'one_issue_quantity' => $v->one_issue_quantity,  // 중복발급 수량
                        'ordercode'          => $ordercode,              //주문 코드
                        'op_idx'             => $op_idx                  // 주문 상품 idx
                    );

                }
            }
            
            $this->issue_type = true;
        }

        return $this->issue_type;

    }

    # type별 발급 가능 쿠폰 목록
    /*
    * $key : ID 또는 ordercode
    * =>   : 첫구매, 수량충족, 금액충족 ( tblorderinfo.ordercode ) / 구매후기 tblorderproduct.idx
    */
    public function search_coupon( $coupon_code = '', $key = '', $general_type = '' )
    {
        // 발급 쿠폰의 정보가 없거나 쿠폰을 사용안함으로 할 경우 발급 X
        if( !$this->coupon_yn ) return false;

        unset( $this->infoData );
        $coupon = $this->coupon_type[$this->type];

        $sql   = "
            SELECT 
                info.coupon_code, info.coupon_name, info.date_start, info.date_end, info.sale_type, info.sale_money, 
                info.time_type, info.use_con_type2, info.productcode, info.not_productcode, info.display_img_type, info.display_img, 
                info.description, info.date, info.one_issue_limit, info.one_issue_quantity, info.member, info.sel_group, info.issue_days_ago, 
                info.sel_gubun, info.order_accept_quantity, info.order_accept_price, info.join_rote 
        ";
        /*
        if( $this->type == '10' ){ // 생일 
            $sql   .= "
                ,m.id
            ";
        }
        */
        # 첫 구매
        if( $this->type == '4' ){
            $sql .= "
                ,oc.ordercode ,oc.oc_cnt , oc.id , oc.is_mobile 
            ";
        }
        # 상품 구매 후기
        else if( $this->type == '11' ){
            $sql   .= "
                ,'".$key."'::int AS op_idx
            ";
        }
        # 수량 충족, 금액 충족
        else if( $this->type == '12' || $this->type == '13' ){
            $sql   .= "
                ,oc.ordercode, oc.total_pr_price, oc.total_quantity, oc.id, oc.is_mobile 
            ";
        }
        # 기본 couponinfo table
        $sql   .= "
            FROM ( 
                SELECT  
                    coupon_code, coupon_name, date_start, date_end, sale_type, sale_money,  
                    time_type, use_con_type2, productcode, not_productcode, display_img_type, display_img, 
                    description, join_rote, date, one_issue_limit, one_issue_quantity, member, sel_group, issue_days_ago, 
                    sel_gubun, order_accept_quantity, order_accept_price
                FROM 
                    tblcouponinfo  
                WHERE 
                    coupon_type = '".$coupon['coupon_type']."' AND issue_status = '".$coupon['issue_status']."' 
                AND 
                    issue_code = '".$coupon['issue_code']."' 
                AND 
                    date_start < to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )
				AND 
                    date_end > to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )
            ) AS info 
        ";
        # 첫구매 order table
        if( $this->type == '4' ){
            $sql   .= "
                ,(
                    SELECT 
                            a.ordercode, a.oc_cnt, b.id, b.is_mobile
                    FROM
                        ( 
                            SELECT ordercode, count( ordercode ) as oc_cnt
                            FROM tblorderproduct
                            WHERE ordercode = '".$key."' 
                            AND op_step = 4
                            GROUP BY ordercode
                        ) AS a
                    JOIN 
                        (
                            SELECT ordercode, id, is_mobile
                            FROM tblorderinfo 
                            WHERE ordercode = '".$key."' 
                            AND id != ''
                        ) AS b ON a.ordercode = b.ordercode 
                ) AS oc
            ";
        }
        # 생일 / 기념일 회원 member talbe
        else if( $this->type == '10' || $this->type == '3' ){ 
            $sql   .= "
                ,(
                    SELECT
                        id, birth, married_date 
                    FROM
                        tblmember 
                    WHERE
                        id = '".$key."'
                    AND
                        birth != ''
                ) AS m
            ";
        }
        # 수량 충족, 금액 충족 order table
        else if( ( $this->type == '12' || $this->type == '13' ) ){
            $sql   .= "
                ,(
                    SELECT
                        op.ordercode, op.total_pr_price, op.total_quantity, oi.id, oi.is_mobile
                    FROM
                        (
                            SELECT 
                                ordercode, SUM( ( price + option_price ) * quantity - coupon_price - use_point ) AS total_pr_price, 
                                SUM( quantity ) AS total_quantity
                            FROM 
                                tblorderproduct 
                            WHERE 
                                op_step = 4
                            GROUP BY ordercode
                        ) AS op
                    JOIN
                        (
                            SELECT
                                ordercode, id, is_mobile 
                            FROM
                                tblorderinfo 
                            WHERE
                                ordercode = '".$key."'
                        ) AS oi ON ( op.ordercode = oi.ordercode )
                ) AS oc
            ";
        }
        $sql   .= "
            WHERE 1 = 1 
        ";
        //exdebug( $sql );
        # 즉시발급 / 무료배송 ( 등급별 발급일 경우 )
        if( $this->type == '1' || $this->type == '9' ){
            $sql   .=  "
                AND 
                    ( 
                        info.sel_gubun = 'A' 
                        OR ( 
                            ( 
                                info.sel_gubun = 'M' OR info.sel_gubun = 'E' ) 
                                AND ( info.coupon_code IN ( SELECT coupon_code FROM tblcouponissue_standby WHERE id = '".$key."' AND used = 'N' ) 
                            ) 
                        )
                        OR ( info.sel_gubun = 'G' AND ( info.sel_group = ( SELECT group_code FROM tblmember WHERE id = '".$key."' ) ) )
                    )
            ";
        }
       
        # 첫구매
        else if( $this->type == '4' ){
            $sql  .= "
                AND
                    oc.oc_cnt = 1
            ";
        }
        # 생일
        else if( $this->type == '10' ){
			/*
            $sql .= "
                AND ( date_part( 'month', current_date + info.issue_days_ago ) = date_part( 'month', CAST( NULLIF( TRIM( m.birth ), '' ) AS DATE ) ) ) 
                AND ( date_part( 'day',  current_date + info.issue_days_ago ) = date_part( 'day', CAST( NULLIF( TRIM( m.birth ), '' ) AS DATE ) ) )
                AND ( info.coupon_code NOT IN 
                    ( 
                        SELECT 
                            coupon_code 
                        FROM 
                            tblcouponissue 
                        WHERE id = m.id 
                        AND info.coupon_code = coupon_code AND date_part( 'year',  current_date )::text = substr( date, 1, 4 ) 
                    ) 
                )
            ";*/
			$sql .= "
                AND ( date_part( 'month', current_date ) = date_part( 'month', CAST( NULLIF( TRIM( m.birth ), '' ) AS DATE ) ) ) 
                AND ( info.coupon_code NOT IN 
                    ( 
                        SELECT 
                            coupon_code 
                        FROM 
                            tblcouponissue 
                        WHERE id = m.id 
                        AND info.coupon_code = coupon_code AND date_part( 'year',  current_date )::text = substr( date, 1, 4 ) 
                    ) 
                )
            ";
        }
        # 상품 구매 후기
        else if( $this->type == '11' ){
            $sql   .= "
                AND 
                    0 = ( SELECT COUNT( coupon_code ) FROM tblcouponissue WHERE op_idx = '".$key."' )
                AND
                    1 = ( SELECT COUNT( productorder_idx ) FROM tblproductreview WHERE productorder_idx = '".$key."' )
            ";
        }
        # 수량 충족
        else if( $this->type == '12'  ){
            $sql   .= "
                AND
                    ( info.order_accept_quantity <= oc.total_quantity )
                AND
                    info.coupon_code NOT IN ( SELECT coupon_code FROM tblcouponissue WHERE ordercode = oc.ordercode )
            "; // id = '".$id."' AND 
        }
        # 금액 충족
        else if( $this->type == '13' ){
            $sql   .= "
                AND
                    ( info.order_accept_quantity <= oc.total_pr_price )
                AND
                    info.coupon_code NOT IN ( SELECT coupon_code FROM tblcouponissue WHERE ordercode = oc.ordercode )
            "; // id = '".$id."' AND 
        }
        # 주말 출석
        else if( $this->type  == '14' ){ // || $general_type == '14' 
            $sql   .= "
                AND
                    ( TO_CHAR( current_date, 'D') = '7' OR TO_CHAR( current_date, 'D') = '1' )
                AND
                    0 = (
                        SELECT 
                            COUNT( coupon_code  )
                        FROM 
                            tblcouponissue 
                        WHERE
                            id = '".$key."'
                        AND
                            coupon_code = info.coupon_code
                        AND
                            date_part( 'year', current_date ) = date_part( 'year', CAST( NULLIF( TRIM( substr( date, 1, 8 ) ), '' ) AS DATE ) )
                        AND
                            TO_CHAR( current_date,'IW' ) = TO_CHAR( CAST( NULLIF( TRIM( substr( date, 1, 8 ) ), '' ) AS DATE ), 'IW' )
                    )
            ";
        }
        # 등급별 자동
        else if( $this->type == '15' ){
			/*
            $sql   .= "
                AND
                    info.sel_group = ( SELECT group_code FROM tblmember WHERE id = '".$key."' )
                AND
                    info.coupon_code NOT IN ( 
                        SELECT 
                            coupon_code 
                        FROM 
                            tblcouponissue 
                        WHERE
                            id = '".$key."'
                        AND
                            date_part( 'year', current_date ) = date_part( 'year', CAST( NULLIF( TRIM( substr( date, 1, 8 ) ), '' ) AS DATE ) )
                        AND 
                            date_part( 'month', current_date ) = date_part( 'month', CAST( NULLIF( TRIM( substr( date, 1, 8 ) ), '' ) AS DATE ) )
                    )
                AND
                    info.coupon_code IN (
                        SELECT 
                            regexp_split_to_table( group_couponcode, '\^' )::character varying(10) AS coupon_code
                        FROM 
                            tblmembergroup
                        WHERE
                            group_code = ( SELECT group_code FROM tblmember WHERE id = '".$key."' )
                    )
            ";*/
			$sql   .= "
                AND
                    info.sel_group = ( SELECT group_code FROM tblmember WHERE id = '".$key."' )
                AND
                    info.coupon_code NOT IN ( 
                        SELECT 
                            coupon_code 
                        FROM 
                            tblcouponissue 
                        WHERE
                            id = '".$key."'
                        AND
                            date_part( 'year', current_date ) = date_part( 'year', CAST( NULLIF( TRIM( substr( date, 1, 8 ) ), '' ) AS DATE ) )
                        AND 
                            date_part( 'month', current_date ) = date_part( 'month', CAST( NULLIF( TRIM( substr( date, 1, 8 ) ), '' ) AS DATE ) )
                    )
            ";
        }

        # 접속 경로
        // 즉시발급, 회원가입, 다운로드, 페이퍼
        // 무료배송, 구매후기, 일반
        if( 
            $this->type == '1' || $this->type == '2' || $this->type == '6' || $this->type == '7' || 
            $this->type == '9' || $this->type == '11' || $this->type == '16' 
        ){ // $coupon['issue_code'] != '2' 자동쿠폰은 접속경로가 무의미함
            $sql   .= "
                AND 
                    ( info.join_rote = '".get_join_rote()."' OR info.join_rote = 'A' )
            ";
        }
        # 첫구매 / 수량별 / 금액별 주문 접속경로
        if( $this->type == '4' || $this->type == '12' || $this->type == '13' ){
            $sql   .= "
                AND
                    ( 
                        info.join_rote = 'A' 
                        OR ( info.join_rote = 'P' AND oc.is_mobile = '0' )
                        OR ( info.join_rote = 'M' AND oc.is_mobile = '1' )
                        OR ( info.join_rote = 'T' AND oc.is_mobile = '2' )
                    )
                AND
                    (
                        1 = ( SELECT COUNT( id ) FROM tblmember WHERE id = oc.id )
                    )
                AND
                    (
                        1 = ( SELECT COUNT( ordercode) FROM tblorderinfo WHERE id = oc.id AND oi_step1 = 4 AND oi_step2 = 0 )
                    )
                AND
                    (
                        0 = ( SELECT COUNT( coupon_code ) FROM tblcouponissue WHERE id = oc.id AND coupon_code = info.coupon_code )
                    )
            ";
        }
        # 쿠폰 검색
        if( strlen( trim( $coupon_code ) ) > 0 ) {
            $sql   .= "
                AND
                    info.coupon_code = '".$coupon_code."'
            ";
        }
        
        # 기존에 받은 쿠폰 제외 ( 즉시발급, 다운로드, 무료배송비, 일반쿠폰 )
        if( strlen( $key ) > 0 && strlen( $coupon_code ) > 0 && ( $this->type == '1' || $this->type == '6' || $this->type == '9' || ( $this->type == '16' && $general_type == '' ) ) ){
            $sql .= "
                AND 
                    ( 0 = ( SELECT COUNT( * ) FROM tblcouponissue WHERE coupon_code = '".$coupon_code."' AND id = '".$key."' ) )
            ";
        # 회원만 검색시 기존에 받은 쿠폰 제외 ( 즉시발급, 다운로드, 무료배송비 )
        } else if( 
                strlen( $key ) > 0 
                && ( $this->type == '1' || $this->type == '6' || $this->type == '9' || $this->type == '2' ) 
            ) {
            $sql .= "
                AND 
                    ( info.coupon_code NOT IN (SELECT coupon_code FROM tblcouponissue WHERE id = '".$key."' ) )
            ";
        }
        $sql   .= "
            ORDER BY info.date
        ";
        //exdebug( $sql );
        $result = pmysql_query( $sql, get_db_conn() );
        while( $row = pmysql_fetch_object( $result ) ){
            $this->infoData[] = $row;
        }
        pmysql_free_result( $result );
        if( strlen( $coupon_code ) > 0 && strlen( $key ) ){
            $chk_return = $this->check_coupon( $coupon_code, $key );
            if( count( $this->infoData ) == 0 && $chk_return == '1' ){ // 쿠폰 코드가 없거나 기한이 지났거나 제외 조건에 붙은 상태
                return '0'; // 발급 불가능한 쿠폰
            } else if( count( $this->infoData ) == 0  && ( $chk_return == '4' || $chk_return == '5' ) ){
                return $chk_return;
            } else if( count( $this->infoData ) > 0 ) {
                return '1';
            } else {
                return '0';
            }
        } else {
            if( count( $this->infoData ) > 0 ) return true;
            else return false;
        }
    }
    
    # 페이퍼 쿠폰 정보
    public function search_paper_coupon( $papercode, $id )
    {
        $msg    = "0";
        $coupon = $this->coupon_type[$this->type];
        list( $coupon_code, $used ) = pmysql_fetch( "SELECT coupon_code, used FROM tblcouponpaper WHERE papercode = '".$papercode."' " );
        if( $coupon_code && $used == 'N' ){
            $sql = "
                SELECT 
                    coupon_code, coupon_name, date_start, date_end, sale_type, sale_money,  
                    time_type, use_con_type2, productcode, not_productcode, display_img_type, display_img, 
                    description, join_rote, date, one_issue_limit, one_issue_quantity, member, sel_group, issue_days_ago, 
                    sel_gubun, order_accept_quantity, order_accept_price
                FROM
                    tblcouponinfo
                WHERE 
                    coupon_type = '".$coupon['coupon_type']."' AND issue_status = '".$coupon['issue_status']."' 
                AND 
                    issue_code = '".$coupon['issue_code']."' 
                AND 
                    date_end > to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )
                AND
                    coupon_code = '".$coupon_code."'
                AND
                    (join_rote = '".get_join_rote()."' OR join_rote = 'A')
                ORDER BY date DESC
            ";
            //exdebug( $sql );
            $result = pmysql_query( $sql,  get_db_conn() );
            if( $row = pmysql_fetch_object( $result ) ){
                /*
                $date = date("YmdHis");
                if( $row->date_start > 0 ) {
                    $date_start = $row->date_start;
                    $date_end   = $row->date_end;
                } else {
                    $date_start = substr( $date, 0, 10 );
                    $date_end   = date( "Ymd23", strtotime( "+".abs($row->date_start)." day" ) );
                }
                */
                $chk_sql = "SELECT id, used FROM tblcouponissue WHERE id='".$id."' AND coupon_code = '".$coupon_code."'";
                list( $chkId, $chkUsed ) = pmysql_fetch( $chk_sql );
                if( !$chkId ) { // 정상발급
                    $this->infoData[] = $row;
                    $msg              = "1";
                } else if($chkUsed == 'Y') { // 사용한 쿠폰
                    /*
                    $sqlGive = "UPDATE tblcouponissue SET used = 'N', date_start = '{$date_start}', date_end = '{$date_end}', date = '{$date}' WHERE coupon_code = '".$row->coupon_code."'";
                    pmysql_query($sqlGive,get_db_conn());

                    $sqlUsed = "UPDATE tblcouponpaper SET used = 'Y' WHERE papercode='".$_POST['papercode']."'";
                    pmysql_query($sqlUsed, get_db_conn());
                    */
                    #사용 쿠폰 날짜 갱신 => 사용한 쿠폰 보유
                    $msg = "4";
                } else if($chkUsed == 'N'){ // 사용안한 쿠폰 보유
                    $msg = "5";
                }
            }
        } else if ($coupon_code && $used == 'Y') {
            $msg = "2";
        }else if(!$coupon_code){
            $msg = "3";
        }

        return $msg;
    }
    # 발급 정보 확인
    public function check_coupon( $coupon_code, $id )
    {
        //$coupon = $this->coupon_type[$this->type];
        $msg = '0';
        $issue_sql = "SELECT coupon_code, used FROM tblcouponissue WHERE coupon_code = '".$coupon_code."' AND id = '".$id."' ";
        list( $coupon_code, $used ) = pmysql_fetch( $issue_sql );
        if( !$coupon_code ){
            $msg = '1'; // 발급 가능
        } else {
            if( $used == 'Y' ) $msg = '4';
            else $msg = '5';
        }

        return $msg;
    }

    #회원 쿠폰정보
    public function search_member_coupon( $id, $type = 0, $is_mobile = 0 )
    {
        $rote             = '';
        $coupon_is_mobile = '';
        $member_coupon    = array();
        $now_date         = date("YmdH");

        $sql = "
            SELECT 
                issue.coupon_code, issue.id, issue.date_start, issue.date_end, 
                issue.used, issue.issue_member_no, issue.issue_recovery_no, issue.ci_no, 
                info.coupon_name, info.sale_type, info.sale_money, info.amount_floor, 
                info.productcode, info.use_con_Type1, info.use_con_type2, info.description, 
                info.use_point, info.vender, info.delivery_type, info.coupon_use_type, 
                info.coupon_type, info.sale_max_money, info.coupon_is_mobile , info.mini_quantity, 
                info.mini_type 
            FROM 
                tblcouponissue issue 
            JOIN 
                tblcouponinfo info ON ( info.coupon_code = issue.coupon_code ) 
            WHERE 
                issue.id = '".$id."' 
        ";
        // 접속경로 구분 
        $rote = get_join_rote();
        if( $rote == 'T' &&  $is_mobile == 1 ){
            #'A' -- 전체 'D' -- 모바일 웹 + 모바일 APP 'C' -- PC + 모바일 APP 'T' -- 모바일 APP
            $sql.= " 
                AND 
                    ( 
                        info.coupon_is_mobile = 'A' 
                        OR info.coupon_is_mobile = 'D' 
                        OR info.coupon_is_mobile = 'C' 
                        OR info.coupon_is_mobile = 'T' 
                    )
            ";
        } else if( $rote == 'M' &&  $is_mobile == 1 ){
            #'A' -- 전체 'D' -- 모바일 웹 + 모바일 APP 'B' -- PC + 모바일 웹 'M' -- 모바일 웹
            $sql.= " 
                AND 
                    ( 
                        info.coupon_is_mobile = 'A' 
                        OR info.coupon_is_mobile = 'D' 
                        OR info.coupon_is_mobile = 'B' 
                        OR info.coupon_is_mobile = 'M' 
                    )
            ";
        } else if( $rote == 'P' &&  $is_mobile == 1 ) {
            #'A' -- 전체 'C' -- PC + 모바일 APP 'B' -- PC + 모바일 웹 'P' -- PC
            $sql.= " 
                AND 
                    ( 
                        info.coupon_is_mobile = 'A'
                        OR info.coupon_is_mobile = 'C' 
                        OR info.coupon_is_mobile = 'B' 
                        OR info.coupon_is_mobile = 'P' 
                    )
            ";
        } else {
            // 전체
            //$sql.= "AND info.coupon_is_mobile = 'A' -- 전체 ";
        }
        // 사용가능한 쿠폰
        if( $type == 1 ){
            $sql.= "
                AND issue.used = 'N' 
                AND ( issue.date_start <= '".$now_date."' AND issue.date_end >= '".$now_date."' ) 
                AND ( issue.date_end <= info.date_end ) 
            ";
        }
        $sql.="
            ORDER BY issue.date DESC 
        ";
        //exdebug( $sql );
        $result = pmysql_query( $sql, get_db_conn() );
        while( $row = pmysql_fetch_object( $result ) ){
            $member_coupon[] = $row;
        }
        pmysql_free_result( $result );

        $this->mem_coupon = $member_coupon;

        return $member_coupon;


    }

    # 일반쿠폰 set
    public function general_coupon_set( $coupon_code, $id, $skey = '', $ekey = '', $general_type = '' )
    {
        $this->search_coupon( $coupon_code, $id, $general_type );
        $this->set_couponissue( $id, $skey, $ekey );
    }

    # 쿠폰 상품 OR 장바구니 제외 / 포함 확인
    # 쿠폰 상품 / 카테고리 유형별 유효성 체크
    public function check_coupon_product ( $productcode, $type = 0, $couponArr = array() )
    {
        $is_unset     = false;
        $arrGoods     = array();
        $infoData     = array();

        if( count( $this->infoData ) > 0 && $type === 0 ){
            $infoData     = $this->infoData;
        } else if( count( $this->mem_coupon ) > 0 && $type === 1 ) {
            $infoData     = $this->mem_coupon;
        } else if( count( $couponArr ) > 0 && $type === 2 ){
            $infoData[]   = $couponArr;
        }

        if( count( $infoData ) > 0 ){
            # 사용 가능한 쿠폰 목록을 불러옴
            foreach( $infoData as $infoKey => $infoVal ){
                $arrGoods[$infoKey] = $infoVal->coupon_code;
            }
            # 쿠폰 목록 중 해당 상품이 사용 가능한지 확인함
            $sql ="
                SELECT
                    cc.coupon_code, cc.use_con_type2, cc.productcode, cc.not_productcode, COUNT( pl.maincate ) AS cate_cnt, COUNT( cp.cp_code ) AS goods_cnt, count( cb_coupon_code ) as brand_cnt
                FROM
                    (
                        SELECT ci.coupon_code, ci.use_con_type2, ci.productcode, ci.not_productcode, cc.categorycode
                        FROM tblcouponinfo AS ci
                        LEFT JOIN tblcouponcategory AS cc ON ( ci.coupon_code = cc.coupon_code )
                        WHERE ci.coupon_code IN ( '".implode( "','", $arrGoods )."' )
                    ) AS cc
                LEFT JOIN
                    (
                        SELECT c_category AS maincate
                        FROM tblproductlink 
                        WHERE c_productcode = '".$productcode."'
                        AND c_maincate = '1'
                        LIMIT 1
                    ) AS pl ON( pl.maincate LIKE cc.categorycode||'%'  )
                LEFT JOIN
                    (
                        SELECT coupon_code, productcode AS cp_code
                        FROM tblcouponproduct
                        WHERE productcode = '".$productcode."'
                        GROUP BY coupon_code, productcode
                    ) AS cp ON ( cc.coupon_code = cp.coupon_code )
                LEFT JOIN
                    (
                        SELECT a.coupon_code as cb_coupon_code
                        FROM tblcouponbrandseason  as a
						join tblproduct as p on 
							( 
								a.bridx = p.brand 
								and ( 
									( a.season_year = p.season_year and a.season = p.season ) 
									or  ( a.season_year = '' and a.season = '' ) 
								)
							)
                        WHERE productcode = '".$productcode."'
                        GROUP BY coupon_code
                    ) AS cb ON ( cc.coupon_code = cb.cb_coupon_code )
                GROUP BY 
                    cc.coupon_code, use_con_type2, productcode, not_productcode
                ";
				//exdebug($sql);
            $result = pmysql_query( $sql, get_db_conn() );
            while( $row = pmysql_fetch_object( $result ) ){
                
                if( $row->use_con_type2 == 'Y' ){ // 상품 포함
                    # 카테고리 또는 상품이 없으면
                    if( 
                        ( $row->productcode == 'CATEGORY' && $row->cate_cnt == 0 ) ||
                        ( $row->productcode == 'GOODS'    && $row->goods_cnt == 0 ) ||
                        ( $row->productcode == 'BRANDSEASONS' && $row->brand_cnt == 0 )
                    ){ //카테고리
                       $arrKey = array_search( $row->coupon_code, $arrGoods );
                       unset( $infoData[$arrKey] ); //해당 상품을 목록에서 지운다
                       $is_unset = true;
                    }
                } else {
                    # 카테고리 또는 상품에 포함되면
                    if( 
                        ( $row->not_productcode == 'CATEGORY' && $row->cate_cnt > 0 ) || 
                        ( $row->not_productcode == 'GOODS'    && $row->goods_cnt > 0 ) ||
                        ( $row->productcode == 'BRANDSEASONS' && $row->brand_cnt > 0 )
                    ){ //카테고리
                        $arrKey = array_search( $row->coupon_code, $arrGoods );
                        unset( $infoData[$arrKey] ); //해당 상품을 목록에서 지운다
                        $is_unset = true;
                    }
                }
            }
            pmysql_free_result( $result );
        }
        
        if( $is_unset && $type === 0 ) {
            $this->infoData   = $infoData;
        } else if( $is_unset && $type === 1 ){
            $this->mem_coupon = $infoData;
        } else if( $type === 2 ) {
            if( $infoData ) return true;
            else return false;
        } 

        return 1;

    }

    # 쿠폰 date_start와 date_end를 반환한다
    public function get_coupon_date( $time_type, $date_start, $date_end )
    {
        $returnDate = array();          // array( 'date_start' => '', 'date_end' => '' );
        $nowDateTime = date("YmdH");    // 현제 년 월 일 시
        $tmp_start = '';                // temp 시작일시
        $tmp_end   = '';                // temp 종료일시
        if( $time_type == 'P' ){        // 발급일 기준
            
            if( $nowDateTime < $date_end ){
                
                $tmp_date = date("Ymd", strtotime( "+".abs( $date_start )." day", time() ) )."23"; // 시작일
                
                if( $tmp_date < $date_end ){
                    $tmp_start = $nowDateTime;
                    $tmp_end   = $tmp_date;
                } else {
                    $tmp_start = $nowDateTime;
                    $tmp_end   = $date_end;
                }

            }

        } else {                        // 기간

            $tmp_start = $date_start;
            $tmp_end   = $date_end;

        }

        $returnDate = (object) array( 'date_start' => $tmp_start, 'date_end' => $tmp_end );

        return $returnDate;
    }

    
    # 기념일 / 생일 회원 조회
    public function search_anniversary_member()
    {
        $memberData = array();
        foreach( $this->infoData as $k => $v ){
            $sql    = "
                SELECT 
                    m.id
                FROM 
                    ( SELECT ( current_date + ".$v->issue_days_ago." ) AS event_day ) AS e,
                    tblmember m
                WHERE 
                    date_part( 'month', e.event_day ) = date_part( 'month', CAST( NULLIF( TRIM( m.birth ), '' ) AS DATE ) )
                AND
                    date_part( 'day', e.event_day ) = date_part( 'day', CAST( NULLIF( TRIM( m.birth ), '' ) AS DATE ) )
            ";
            $result = pmysql_query( $sql, get_db_conn() );
            while( $row = pmysql_fetch_object( $result ) ){
                $memberData[] = $row;
            }
            pmysql_free_result( $result );
        }
        return $memberData;
    }

    # 즉시발급 / 무료배송 standby talbe 조회
    public function check_standby_member( $id, $coupon_code )
    {
        $returnData = '';
        $sql    = "
            SELECT
                cis_no, coupon_code, id
            FROM
                tblcouponissue_standby
            WHERE
                coupon_code = '".$coupon_code."'
            AND
                id = '".$id."'
            AND
                used = 'N'
        ";
        $result = pmysql_query( $sql, get_db_conn() );
        $row = pmysql_fetch_object( $result );
        pmysql_free_result( $result );
        if( $row->cis_no > 0 ) $returnData = $row->cis_no;

        return $returnData;
    }

    # 쿠폰 보유 확인
    public function search_possesion_check( $coupon_code, $id )
    {
        $sql    = "
            SELECT 
                COUNT( * ) AS coupon_cnt
            FROM
                tblcouponissue 
            WHERE 
                coupon_code = '".$coupon_code."' AND id = '".$id."'
        ";
        $result = pmysql_query( $sql, get_db_conn() );
        $row    = pmysql_fetch_row( $result );
        pmysql_free_result( $result );

        if( $row[0] > 0 ) return false;
        else return true;
    }

    # 상품가격에 쿠폰 할인가를 적용
    public function discountPrice( $sellprice = 0, $ci_no = 0 )
    {
        global $_ShopInfo;
        $reserve_type = true;

        $sql = "
            SELECT 
                info.coupon_code, info.coupon_type, info.mini_price,
                info.sale_type, info.sale_money, issue.ci_no,
                info.sale_max_money 
            FROM 
                tblcouponinfo AS info
            JOIN 
                ( SELECT coupon_code, ci_no FROM tblcouponissue WHERE ci_no = ".$ci_no." AND id = '".$_ShopInfo->getMemid()."' ) AS issue 
                ON ( info.coupon_code = issue.coupon_code  )
        ";
        $result = pmysql_query( $sql, get_db_conn() );
        $row    = pmysql_fetch_object( $result );
        pmysql_free_result( $result );

        $ci_no        = $row->ci_no;
        $coupon_code  = $row->coupon_code;
        $dc_price     = 0;

        switch( $row->sale_type ){
            case '1' : // 적립
            case '2' : // 할인
                $dc_price = ( ( $sellprice * $row->sale_money ) / 100 );
                $dc_price = AmountFloor( $this->coupon['amount_floor'], $dc_price );
                break;
            case '3' : // 적립
            case '4' : //할인
                //$dc_price =  $row->sale_money;
                if($row->sale_money > $sellprice){
                    $dc_price = $sellprice;
                 }else{
                    $dc_price =  $row->sale_money;
                 }
                break;
            default :
                break;
        }

        if( $row->sale_max_money > 0 && $row->sale_max_money < $dc_price ) {
            $dc_price = $row->sale_max_money;
        }

        $arr_dc = array(
            $ci_no,            'ci_no'       => $ci_no, 
            $reserve_type,     'type'        => $reserve_type, 
            $sellprice,        'sellprice'   => $sellprice, 
            $dc_price,         'dc'          => $dc_price,
            $coupon_code,      'coupon_code' => $coupon_code,
            $row->coupon_type, 'coupon_type' => $row->coupon_type,
            $row->mini_price,  'mini_price'  => $row->mini_price
        );

         return $arr_dc;
    }
    # 쿠폰 insert log
    protected function insert_coupon_log( $sql )
    {
        $logText = "=======".date("Y-m-d H:i:s")."-------".PHP_EOL;
        $logText.= $sql.PHP_EOL;
        $log_folder = DirPath.DataDir."backup/insert_coupon_log_".date("Ym");
        if( !is_dir( $log_folder ) ){
            mkdir( $log_folder, 0700 );
            chmod( $log_folder, 0777 );
        }
        $file = $log_folder."/coupon_insert_".date("Ymd").".txt";
        if( !is_file( $file ) ){
            $f = fopen( $file, "a+" );
            fclose( $f );
            chmod( $file, 0777 );
        }
        file_put_contents( $file, $logText, FILE_APPEND );
    }

}

?>
