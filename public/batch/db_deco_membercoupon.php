<?php
exit;
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

echo "Start ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";
// insert할 쿠폰 번호 * 필수! 없으며 err
$coupon_code = '29173313';
if( $coupon_code == '' ) exit;
// 해당 쿠폰을 member에 insert 한다
$couponInsert_qry = "

INSERT INTO tblcouponissue ( coupon_code, id, date_start, date_end, date ) 
(
  SELECT coupon.coupon_code, in_member.id, coupon.date_start, coupon.date_end, to_char( now() , 'YYYYMMDDHH24' ) AS date
  FROM
  (
    SELECT
      id
    FROM 
      tblmember
    WHERE
      id NOT IN (
        SELECT m.id
        FROM tblmember m
        JOIN tblcouponissue cs ON cs.id = m.id
        WHERE m.mb_nick_date != '' 
        AND cs.coupon_code = '".$coupon_code."'
        GROUP BY m.id
      )
    AND
      mb_nick_date != '' 
  ) AS in_member,    
  (
    SELECT coupon_code, 
      (
        CASE 
          WHEN time_type = 'D' THEN date_start
          WHEN time_type = 'P' THEN to_char( now() , 'YYYYMMDDHH24' )
        END
      ) AS date_start,
      (
        CASE 
          WHEN time_type = 'D' THEN date_end
          WHEN time_type = 'P' THEN 
          (
            CASE
              WHEN to_char( ( now()::date + abs( date_start::int ) + 1 ) - interval '1 hour' , 'YYYYMMDDHH24' ) < date_end 
                THEN to_char( ( now()::date + abs( date_start::int ) + 1 ) - interval '1 hour' , 'YYYYMMDDHH24' )
              ELSE date_end
            END
          )
          END
      ) AS date_end
    FROM tblcouponinfo info
    WHERE coupon_code = '".$coupon_code."' 
  ) AS coupon
) 
RETURNING coupon_code, id, date_start, date_end, date 

";
$couponInsert_res = pmysql_query($couponInsert_qry, get_db_conn());
echo "sql = <br>";
exdebug( $couponInsert_qry );
echo "<br>";
echo "<hr>";
if( $err = pmysql_error() ) {
    echo $err."<br>";
    exit;
}
echo "<hr>";

echo "Start 2"."<br>";
echo "<hr>";

$coupon_rows = pmysql_num_rows( $couponInsert_res );
echo "rows = ".$coupon_rows."<br>";
if( $coupon_rows > 0 ){
    # insert된 쿠폰수만큼 issue_no를 update 해준다
    $update_coupon_qry = "UPDATE tblcouponinfo SET issue_no = issue_no + ".$coupon_rows." WHERE coupon_code = '".$coupon_code."' ";
    pmysql_query($update_coupon_qry, get_db_conn());
    echo "sql = ".$update_coupon_qry."<br>";
    echo "<hr>";

    $batch_text = "## coupon_insert === Coupon Code : ".$coupon_code." === [ Date : ".date('Y-m-d H:i:s')." ] , ".$coupon_rows." ROW ## \n\n";
    while( $insert_row = pmysql_fetch_object( $couponInsert_res ) ){
        //coupon_code, id, date_start, date_end, date
        $batch_text.=" Coupon Code : ".$insert_row->coupon_code." \n";
        $batch_text.=" ID          : ".$insert_row->id." \n";
        $batch_text.=" Date Start  : ".$insert_row->date_start." \n";
        $batch_text.=" Date End    : ".$insert_row->date_end." \n";
        $batch_text.=" Insert Dat  : ".$insert_row->date." \n";
        $batch_text.="\n";
    }
    pmysql_free_result( $couponInsert_res );
    $batch_text.= "## ////////////////////////////////////////////////////////////////////////////////////////// ##\n\n";
    echo "INSERT DATA "."<br>";   
    echo "<hr>";
    exdebug( $batch_text);
    echo "<br>";
    echo "<hr>";
    # 파일 로그를 남긴다
    $log_folder = DirPath.DataDir."backup/deco_membercoupon_".date("Ym");
    if( !is_dir( $log_folder ) ){
        mkdir( $log_folder, 0700 );
        chmod( $log_folder, 0777 );
    }
    $file = $log_folder."/deco_membercoupon_".date("Ymd").".txt";
    if( !is_file( $file ) ){
        $f = fopen( $file, "a+" );
        fclose( $f );
        chmod( $file, 0777 );
    }
    file_put_contents( $file, $batch_text, FILE_APPEND );

}
echo "End ".date("Y-m-d H:i:s")."<br>";
exit;
?>