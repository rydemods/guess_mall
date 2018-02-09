<?

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/category.class.php");
//include("access.php");


$cate =new CATEGORYLIST();


?>
<html>

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>Destroydrop &raquo; Javascripts &raquo; Tree</title>
	<link rel="stylesheet" type="text/css" href="DynamicTree.css">
	<script src="DynamicTree.js"></script>
	<script src="DynamicTreeSorting.js"></script>
<script>

/*** 분류트리 하부노드 로딩 ***/
function openTree(obj, chkable)
{
	alert(chkable);
	
	tree.sorting.ready(obj);
}
</script>
</head>

<body>

<h2>Sitemap</h2>

<div class="DynamicTree">
<div class="wrap" id="tree">
<!--
	<div class=folder>폴더1
		<div class=folder>폴더
			<div class=folder>폴더
				<div class=doc>자바스크립트</div>
			    <div class=doc>자바스크립</div>
			</div>
        </div>
	    <div class=doc>자바스크립트</div>
	</div>
	<div class=doc>동영상</div>
	<div class=doc>계정</div>
	<div class=folder>폴더
		<div class=doc><a href="javascript:alert('클릭');">게시판</a></div>
		<div class=doc><a href="javascript:alert('클릭');">기타</a></div>
	</div>	
-->
	<?echo $cate->getDesignCateTree()?>


</div>
</div>

<script type="text/javascript">
var tree = new DynamicTree("tree");
tree.init();
tree.Sorting();
</script>

</body>

</html>

