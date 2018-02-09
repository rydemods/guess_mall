<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$total_check=$_POST['total_check'];
$num=$_POST['num'];

foreach($total_check as $k=>$v){
	$postX_arr[]=$_POST['postX'.$v];
	$postY_arr[]=$_POST['postY'.$v];
	$p_number_arr[]=$_POST['p_number'.$v];
	$left_right_arr[]=$_POST['left_right'.$v];
	$up_down_arr[]=$_POST['up_down'.$v];
	$img_color_arr[]=$_POST['img_color'.$v];
}
$postX_implode=implode("|",$postX_arr);
$postY_implode=implode("|",$postY_arr);
$p_numbe_implode=implode("|",$p_number_arr);
$left_right_implode=implode("|",$left_right_arr);
$up_down_implode=implode("|",$up_down_arr);
$img_color_implode=implode("|",$img_color_arr);

$query="update tbllookbook_content set lr_coordinates='".$postX_implode."', ud_coordinates='".$postY_implode."', view_lr='".$left_right_implode."', productcodes='".$p_numbe_implode."', total_num='".count($total_check)."', view_ud='".$up_down_implode."', view_img='".$img_color_implode."' where no='".$num."'";
pmysql_query($query, get_db_conn() );

echo "<script>alert('적용되었습니다.'); window.close();</script>";
//msg("적용되었습니다.","mobileShop_set.php");

?>