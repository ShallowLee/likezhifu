<?php
ini_set('date.timezone','Asia/Shanghai');

/* *
 * 功能：连连支付实时付款交易接口
 * 版本：1.0
 * 修改日期：2016-11-28
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
require_once ("llpay.config.php");
require_once ("lib/llpay_apipost_submit.class.php");
require_once ("lib/llpay_security.function.php");


/**************************请求参数**************************/

//商户付款流水号
$no_order = '20161128165916';

//商户时间
$dt_order = '20161128165916';

//金额
$money_order = '0.01';

//收款人姓名
$acct_name = '李三';

//银行账号
$card_no = '6216261000000000018';

//订单描述
$info_order = 'test测试';

//对私标记
$flag_card = '0';

//服务器异步通知地址
$notify_url = 'wallet.likezhifu.cn';

//平台来源
$platform = 'wallet.likezhifu.cn';

//版本号
$api_version = '1.0';


//实时付款交易接口地址
$llpay_payment_url3 = 'https://instantpay.lianlianpay.com/paymentapi/payment.htm';

//需http://格式的完整路径，不能加?id=123这类自定义参数

/************************************************************/
$time = time();

$parameter3 = array (
    "oid_partner" => trim($llpay_config['oid_partner']),
    "sign_type" => trim($llpay_config['sign_type']),
    "no_order" => rand(10000, 99999).$time.rand(10000, 99999),
    "dt_order" => local_date('YmdHis', time()),
    "money_order" => 1,
    "acct_name" => '宋宇辉',
    "card_no" => '6212264301012423982',
    "info_order" => $info_order,
    "flag_card" => $flag_card,
    "notify_url" => $notify_url,
    "platform" => $platform,
    "api_version" => $api_version
);
//建立请求
$llpaySubmit = new LLpaySubmit($llpay_config);
//对参数排序加签名
$sortPara = $llpaySubmit->buildRequestPara($parameter3);
//传json字符串
$json = json_encode($sortPara);
$parameterRequest = array (
    "oid_partner" => trim($llpay_config['oid_partner']),
    "pay_load" => ll_encrypt($json,$llpay_config['LIANLIAN_PUBLICK_KEY']) //请求参数加密
);
$html_text = $llpaySubmit->buildRequestJSON($parameterRequest,$llpay_payment_url3);
var_dump(json_decode($html_text,true));
$html_text = json_decode($html_text,true);

echo $html_text['ret_code'].date('YmdHis');
