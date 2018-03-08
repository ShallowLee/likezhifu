<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>连连支付wap交易接口</title>
</head>
<?php


/* *
 * 功能：连连支付wap交易接口接入页
 * 版本：1.2
 * 修改日期：2014-06-13
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
require_once ("llpay.config.php");
require_once ("lib/llpay_submit.class.php");

/**************************请求参数**************************/

//商户用户唯一编号
$user_id = $_POST['user_id'];

//支付类型
$busi_partner = '101001';

//商户订单号
$no_order = $_POST['no_order'];
//商户网站订单系统中唯一订单号，必填

//付款金额
$money_order = $_POST['money_order'];
//必填

//商品名称
$name_goods = $_POST['name_goods'];

//订单描述
$info_order = $_POST['info_order'];

//卡号
$card_no = '6212264301012423982';

//姓名
$acct_name = '宋宇辉';

//身份证号
$id_no = '410381199203286531';

//协议号
$no_agree = $_POST['no_agree'];

//风险控制参数
$risk_item = '{\"frms_ware_category\":\"2009\",\"user_info_mercht_userno\":\"123456\",\"user_info_dt_register\":\"20141015165530\",\"user_info_full_name\":\"张三\",\"user_info_id_no\":\"3306821990012121221\",\"user_info_identify_type\":\"1\",\"user_info_identify_state\":\"1\"}';

//订单有效期
$valid_order = $_POST['valid_order'];

//服务器异步通知页面路径
$notify_url = "http://10.10.110.246/llpay/notify_url.php";
//需http://格式的完整路径，不能加?id=123这类自定义参数

//页面跳转同步通知页面路径
$return_url = "http://10.10.110.246/llpay/return_url.php";
//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array (
    "oid_partner" => trim($llpay_config['oid_partner']),
    "app_request" => 3,
    "sign_type" => trim($llpay_config['sign_type']),
    "valid_order" => trim($llpay_config['valid_order']),
    "user_id" => $user_id,
    "busi_partner" => $busi_partner,
    "no_order" => time(),
    "dt_order" => date('YmdHis'),
    "name_goods" => 'ceshi',
    "info_order" => 'ceshi',
    "money_order" => 0.01,
    "notify_url" => $notify_url,
    "url_return" => $return_url,
    "card_no" => '6212264301012423982',
    "acct_name" => '宋宇辉',
    "id_no" => '410381199203286531',
    "risk_item" => ($risk_item),
);

//建立请求
$llpaySubmit = new LLpaySubmit($llpay_config);
$html_text = $llpaySubmit->buildRequestForm2($parameter, "post", "确认");
echo $html_text;
?>
</body>
</html>