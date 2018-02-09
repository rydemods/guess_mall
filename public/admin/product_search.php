<?header("Content-type: text/html; charset=utf-8");?>
<?$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("../conf/config.php");
include("access.php");

//<link rel="styleSheet" href="/css/admin.css" type="text/css">

$pdtname=$_GET[pdtname];
$productcode=$_GET[pdtcode];

$imagepath=$cfg_img_path['product'];

$productHTML.="<div class=\"table_style01\">";

if($pdtname || $productcode){
	

$query="select * from view_tblproduct ";
//$query= "SELECT a.* FROM view_tblproduct AS a  ";
//$query.= "JOIN (select c_productcode as c_productcode from tblproductlink group by c_productcode) AS link ON a.productcode=link.c_productcode ";

if($productcode){
	$where[]="productcode like '".$productcode."%' ";
}

if($pdtname){
	$where[]="productname like '%".$pdtname."%' ";
}

//제휴몰 여부 확인(제휴몰 아닌것들만)
//$where[]="sabangnet_flag = 'N'";

if(count($where)){
	$query.="where ".implode(" and ",$where);
}

$query.="order by productcode";
$result=pmysql_query($query);
$cnt=0;

while($data=pmysql_fetch_object($result)){

	$productHTML.="<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border:0px\">";
	$productHTML.="<tr class='pdt_search_result'>";
	$productHTML.="<th style=\"width:100px;text-align:center\" >";
	$productHTML.="<input type=hidden class='pdt_code_val' value='{$data->productcode}'>";
	$productHTML.="<span class='img_span'>";
	if (ord($data->tinyimage) && file_exists($imagepath.$data->tinyimage)){
	$productHTML.="<img src='".$imagepath.$data->tinyimage."' widht=50 height=50>";
	}elseif(ord($data->tinyimage) && file_exists($Dir.$data->tinyimage)){
	$productHTML.="<img src='".$Dir.$data->tinyimage."' widht=50 height=50>";
	}else{
	$productHTML.="<img src=\"../images/product_noimg.gif\" widht=50 height=50>";	
	}
	$productHTML.="</span>";
	$productHTML.="</th>";
	$productHTML.="<td>";
	$productHTML.="상품명:<span class='name_span'>".$data->productname."</span>";

	$productHTML.="<br>가격:<span class='price_span'>".number_format($data->sellprice)."</span>원";
	$productHTML.="</td>";
	$productHTML.="</tr>";
	$productHTML.="</table>";
	$cnt++;
}

if(!$cnt){
	$productHTML.="카테고리에 상품이 존재하지 않습니다.";
}

}else{
	$productHTML.="상품 검색을 해주십시요.";
}
$productHTML.="</div>";


#echo utf8encode($productHTML);
echo $productHTML;
?>
