<?php
/* *
 * 配置文件
 * 版本：1.2
 * 日期：2014-06-13
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201408071000001543
$llpay_config['oid_partner'] = '201408071000001543';

//秘钥格式注意不能修改（左对齐，右边有回车符）
$llpay_config['RSA_PRIVATE_KEY'] ='-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCuSGeNqwbwTj1CW5TijRkIDSCU1VRu+iFTxqb1vkGlFkX5+NqG
mybbDH0BBhQAFg+uitAvxYhwGRFHsGzr6EBc2Qlbg9NYCPch3aQS0eQTw5snc65Z
CVglbFeb3RUfO+Zk6UhijWCK7viQP4PA8u0ho2KY6EPI7P4XUn0EZU1LcwIDAQAB
AoGBAIdZwdpbFaNxD9BGMaWUcPk4wLH1z4H0jgdzAt1c6bxdRPEym/vn6NA7raUq
5EOA3qLuOWqwXRq5zRrA4IaBs+FfsqUdKC4leSfmcy71I1NqCADgOKLhw5HVT2cP
o/rAYIMcCuC6QwhZvFJf+3Ei7Gt6uOmfwCMxXDhramnF6x3xAkEA020SgB6psH2k
wdtE9pG+UNRkZrIa2kDQPGvNVCW9CX8nkw97RpOgQb4e+UWxr15fHVERQ9Hhs269
55CLJ2C76wJBANMGqmwC98MGCyvVff9dDfGCY0NCSrx5wJF6LrgJ5LzWEWtMDEE0
GrO5LpVL0gos36FXyfYmT6fGb7dphxjj9JkCQDIRgnAU0tqY5ouCAbLikM9TfPNU
+kb1Ulr8rINg7INzOUv6PpdYHntzKqKdfpapIp0utYf6DBkSAKb18/4/SkECQHI3
2WY89P2J9+Hx3BCrvTBK8o8lAm6hN6mGCGN0d2n+M89vifKA0TMVuDD2qOj5ANHD
Qn0In3Sf4zfS1lyShYECQQCVm70BwmNsl3h1ufQmIwzJ4qIAaz02bn05rBzNCW1t
4JVoQsnpeKkGZR7yOJOzJ66h469eo59IDLnfGFcAs7f9
-----END RSA PRIVATE KEY-----';	

//安全检验码，以数字和字母组成的字符
$llpay_config['key'] = '201408071000001543test_2014082';

//版本号
$llpay_config['version'] = '1.2';

//请求应用标识 为wap版本，不需修改
$llpay_config['app_request'] = '3';

//签名方式 不需修改
$llpay_config['sign_type'] = strtoupper('RSA');

//订单有效时间  分钟为单位，默认为10080分钟（7天） 
$llpay_config['valid_order'] ="10080";

//字符编码格式 目前支持 gbk 或 utf-8
$llpay_config['input_charset'] = strtolower('utf-8');

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$llpay_config['transport'] = 'http';
?>