<?

require_once dirname(__FILE__).'/lib/nicepay/web/NicePayWEB.php';
require_once dirname(__FILE__).'/lib/nicepay/core/Constants.php';
require_once dirname(__FILE__).'/lib/nicepay/web/NicePayHttpServletRequestWrapper.php';


/** 1. Request Wrapper Ŭ������ ����Ѵ�.  */ 
$httpRequestWrapper = new NicePayHttpServletRequestWrapper($_REQUEST);
$_REQUEST = $httpRequestWrapper->getHttpRequestMap();

/** 2. ���� ����Ϳ� �����ϴ� Web �������̽� ��ü�� �����Ѵ�.*/
$nicepayWEB = new NicePayWEB();

/** 2-1. �α� ���丮 ���� */
$nicepayWEB->setParam("NICEPAY_LOG_HOME","./log");

/** 2-2. ���ø����̼� �α� ��� ����(0: DISABLE, 1: ENABLE) */
$nicepayWEB->setParam("APP_LOG","1");

/** 2-3. ��ȣȭ�÷��� ����(N: ��, S:��ȣȭ) */
$nicepayWEB->setParam("EncFlag","S");

/** 2-4. ���񽺸�� ����(���� ���� : PY0 , ��� ���� : CL0) */
$nicepayWEB->setParam("SERVICE_MODE", "PY0");

/** 2-5. ��ȭ���� ����(���� KRW(��ȭ) ����)  */
$nicepayWEB->setParam("Currency", "KRW");

/** 2-6. �������� ���� (�ſ�ī����� : CARD, ������ü: BANK, ���������ü : VBANK, �޴������� : CELLPHONE ) */
$payMethod = $_REQUEST['PayMethod'];
$nicepayWEB->setParam("PayMethod",$_REQUEST['PayMethod']);

/** 2-7 ���̼���Ű ���� 
	���� ID�� �´� ����Ű�� �����Ͻʽÿ�.
	*/
$nicepayWEB->setParam("LicenseKey","33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==");

/** 3. ���� ��û */
$responseDTO = $nicepayWEB->doService($_REQUEST);

/** 4. ���� ��� */
$resultCode = $responseDTO->getParameter("ResultCode"); // ����ڵ� (���� :3001 , �� �� ����)
$resultMsg = $responseDTO->getParameter("ResultMsg");   // ����޽���
$authDate = $responseDTO->getParameter("AuthDate");   // �����Ͻ� YYMMDDHH24mmss
$authCode = $responseDTO->getParameter("AuthCode");   // ���ι�ȣ
$buyerName = $responseDTO->getParameter("BuyerName");   // �����ڸ�
$mallUserID = $responseDTO->getParameter("MallUserID");   // ȸ�����ID
$goodsName = $responseDTO->getParameter("GoodsName");   // ��ǰ��
$mallUserID = $responseDTO->getParameter("MallUserID");  // ȸ����ID
$mid = $responseDTO->getParameter("MID");  // ����ID
$tid = $responseDTO->getParameter("TID");  // �ŷ�ID
$moid = $responseDTO->getParameter("Moid");  // �ֹ���ȣ
$amt = $responseDTO->getParameter("Amt");  // �ݾ�

$cardQuota = $responseDTO->getParameter("CardQuota");   // �Һΰ���
$cardCode = $responseDTO->getParameter("CardCode");   // ����ī����ڵ�
$cardName = $responseDTO->getParameter("CardName");   // ����ī����

$bankCode = $responseDTO->getParameter("BankCode");   // �����ڵ�
$bankName = $responseDTO->getParameter("BankName");   // �����
$rcptType = $responseDTO->getParameter("RcptType"); //���� ������ Ÿ�� (0:�����������,1:�ҵ����,2:��������)
$rcptAuthCode = $responseDTO->getParameter("RcptAuthCode");   // ���ݿ����� ���ι�ȣ

$carrier = $responseDTO->getParameter("Carrier");       // ����籸��
$dstAddr = $responseDTO->getParameter("DstAddr");       // �޴�����ȣ

$vbankBankCode = $responseDTO->getParameter("VbankBankCode");   // ������������ڵ�
$vbankBankName = $responseDTO->getParameter("VbankBankName");   // ������������
$vbankNum = $responseDTO->getParameter("VbankNum");   // ������¹�ȣ
$vbankExpDate = $responseDTO->getParameter("VbankExpDate");   // ��������Աݿ�����

$mallReserved = $_REQUEST['MallReserved'];

/** ���� ���� ������ �ܿ��� ���� Header�� ������ ������ Get ���� */

$paySuccess = false;		// ���� ���� ����
if($payMethod == "CARD"){	//�ſ�ī��
	if($resultCode == "3001") $paySuccess = true;	// ����ڵ� (���� :3001 , �� �� ����)
}else if($payMethod == "BANK"){		//������ü
	if($resultCode == "4000") $paySuccess = true;	// ����ڵ� (���� :4000 , �� �� ����)
}else if($payMethod == "CELLPHONE"){			//�޴���
	if($resultCode == "A000") $paySuccess = true;	//����ڵ� (���� : A000, �� �� ������)
}else if($payMethod == "VBANK"){		//�������
	if($resultCode == "4100") $paySuccess = true;	// ����ڵ� (���� :4100 , �� �� ����)
}

if($paySuccess == true){
   // ���� ������ DBó�� �ϼ���.
}else{
   // ���� ���н� DBó�� �ϼ���.
}

?>
<html>
<head><title>NicePAy</title>
<link href="./css/__smart.css" rel="stylesheet" type="text/css"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr;"/>
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
</head>
<body>
        <div class="selectList">
                <ul>
                        <li class="selectBar">
                                <label>�� ��������</label>
                                <span><?=$payMethod?></span>
                        </li>
                        <li class="selectBar">
                                <label>�� ����ID</label>
                                <span><?=$mid ?></span>
                        </li>
                        <li class="selectBar">
                                <label>�� �ݾ�</label>
                                <span><?=$amt ?></span>
                        </li>
                        <li class="selectBar">
                                <label>�� �����ڸ�</label>
                                <span><?=$buyerName ?></span>
                        </li>
                        <li class="selectBar">
                                <label>�� ��ǰ��</label>
                                <span><?=$goodsName ?></span>
                        </li>
                       
                        <li class="selectBar">
                                <label>�� �ŷ���ȣ</label>
                                <span><?=$tid ?></span>
                        </li>
                        <li class="selectBar">
                                <label>�� �ֹ���ȣ</label>
                                <span><?=$moid ?></span>
                        </li>
                        <li class="selectBar">
                                <label>�� ��������</label>
                                <span><?=$authDate ?></span>
                        </li>
                       
                        <li class="selectBar">
                                <label>�� ����ڵ�</label>
                                <span><?=$resultCode ?></span>
                        </li>
                        <li class="selectBar">
                                <label>�� ����޽���</label>
                                <span><?=$resultMsg ?></span>
                        </li>
						
                        <li class="selectBar">
                                <label>�� ���������ȣ</label>
                                <span><?=$mallReserved ?></span>
                        </li>
						<?if($payMethod == "CARD"){?>
							<li class="selectBar">
									<label>�� ����ī����ڵ�</label>
									<span><?=$cardCode ?></span>
							</li>
							<li class="selectBar">
									<label>�� ����ī����</label>
									<span><?=$cardName ?></span>
							</li>
							 <li class="selectBar">
									<label>�� ���ι�ȣ</label>
									<span><?=$authCode ?></span>
							</li>
							<li class="selectBar">
									<label>�� �Һΰ�����</label>
									<span><?=$cardQuota ?></span>
							</li>
						<?}else if($payMethod == "BANK"){?>
							<li class="selectBar">
                                <label>�� �����</label>
                                <span><?=$bankName ?></span>
							</li>
						<?}else if($payMethod == "CELLPHONE"){?>
							<li class="selectBar">
                                <label>�� �޴�����ȣ</label>
                                <span><?=$dstAddr ?></span>
							</li>
						<?}else if($payMethod=="VBANK"){?>
							<li class="selectBar">
                                <label>�� ������¹�ȣ</label>
                                <span><?=$vbankNum ?></span>
							</li>
							<li class="selectBar">
									<label>�� ������������</label>
									<span><?=$vbankBankName ?></span>
							</li>
							
						<?}?>
                       
                       
                      
                </ul>
        </div>
</body>
</html>
