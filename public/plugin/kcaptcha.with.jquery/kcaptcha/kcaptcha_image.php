<?php
include_once('kcaptcha.php');
session_start();
$captcha = new KCAPTCHA();
$captcha->setKeyString($_SESSION['captcha_keystring']);
$captcha->getKeyString();
$captcha->image();
?>