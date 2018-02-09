<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$login_yn = "N";
if(strlen($_ShopInfo->getMemid())>0){
	$login_yn = "Y";
}

$curdate = date('Ymd');
$sql = "SELECT id FROM tblroulette WHERE id='{$_ShopInfo->getMemid()}' AND date = '{$curdate}'";
$res = pmysql_query($sql);
$row = pmysql_num_rows($res);
$chance = 1;
if($row>=1){
	$chance = 0;
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";


	/* 	룰렛 게임에 사용
		페이지 로드 시 호출
		플래시에서 사용하는 요소들에 대한 초기 값 세팅*/


echo "<data>";
	
	/*로그인 판단 여부로 Y 일 경우 게임 가능, N 이면 자바스크립트 goLogin 호출 */
echo "	<loginYN><![CDATA[".$login_yn."]]></loginYN>";
	
	/*게임 참여 기회로 0보다 크면 게임 가능, 1보다 작으면 자바스크립트 noChange 호출*/ 
echo "	<chanceNum><![CDATA[".$chance."]]></chanceNum>";
	
	
	/*룰렛 내용 관련 이미지 경로, 당첨 결과 값 순서대로 content 노드 나열 ( 맨 위에 부터 0 번 )
	slot 은 룰렛 판 이미지 경로, popup 은 당첨 결과 팝업 이미지 경로*/
	
echo "	<roulette>
		
		<content>
			<slot><![CDATA[image/board_slot1.png]]></slot>
			<popup><![CDATA[image/popup_content1.png]]></popup>
		</content>
		<content>
			<slot><![CDATA[image/board_slot2.png]]></slot>
			<popup><![CDATA[image/popup_content2.png]]></popup>
		</content>
		<content>
			<slot><![CDATA[image/board_slot3.png]]></slot>
			<popup><![CDATA[image/popup_content3.png]]></popup>
		</content>
		<content>
			<slot><![CDATA[image/board_slot4.png]]></slot>
			<popup><![CDATA[image/popup_content4.png]]></popup>
		</content>
		<content>
			<slot><![CDATA[image/board_slot5.png]]></slot>
			<popup><![CDATA[image/popup_content5.png]]></popup>
		</content>
		<content>
			<slot><![CDATA[image/board_slot6.png]]></slot>
			<popup><![CDATA[image/popup_content6.png]]></popup>
		</content>
		<content>
			<slot><![CDATA[image/board_slot7.png]]></slot>
			<popup><![CDATA[image/popup_content7.png]]></popup>
		</content>
		
	</roulette>
	
</data>";