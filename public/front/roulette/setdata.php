<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

/*각 등수의 확률? 넣음 이건 나중에 */
$n1 = rand(1,100); 
$n2 = rand(1,100); 
$n3 = rand(1,100); 
$n4 = rand(1,100); 
$n5 = rand(1,100); 
$n6 = rand(1,100); 
$result_n = 7;

$sql = "SELECT count(*),num FROM tblroulette GROUP BY num ORDER BY num";
$res = pmysql_query($sql);
while($row = pmysql_fetch_object($res)){
	$rdata[] = $row->count;
}

for($i=6;$i>0;$i--){
	switch($i){		/*당첨인원수 설정*/
		case 1 : $ii = 1; break;
		case 2 : $ii = 10; break;
		case 3 : $ii = 10; break;
		case 4 : $ii = 25; break;
		case 5 : $ii = 50; break;
		case 6 : $ii = 100; break;		
	}
	
	if($rdata[$i] < $ii){
		$random_n = ${"n".$i};
		if($random_n == 1){
		$result_n = $i;
		}
	}
}

$curdate = date('Ymd');
$sql2 = "INSERT INTO tblroulette (id, num, date) values('{$_ShopInfo->getMemid()}', '{$result_n}', '{$curdate}' )";
//exdebug($rdata);
pmysql_query($sql2);

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	/*룰렛 게임에 사용
	 플래시에서 START 클릭 시 호출
	 당첨 결과 값 세팅*/
echo "<data>";	
	/* 당첨 결과 값으로 1 ~ 7 까지 총 8개, 1 ~ 7 이 아닐 경우 오류로 자바스크립트 resultError 호출 */
echo "	<resultNum><![CDATA[".$result_n."]]></resultNum>";	
echo "</data>";