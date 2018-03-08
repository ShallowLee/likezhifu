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
$llpay_config['oid_partner'] = '201710120001012537';

//秘钥格式注意不能修改（左对齐，右边有回车符）
$llpay_config['RSA_PRIVATE_KEY'] ='-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDP7OqQr/7Jvg87rIfBic//iK4/VOcPa16wxSrbT/NFfMYQzwIU
tf4qvyuFQq5dTUiD/Z2unit+N+8ju80INsXWZ6dtAFqOoyzFmMuX06DXSz2fcJT+
BLEm84mWRcdoPc2MO/hGJOVci3gvKLQ9yKKH4mPJ/j5LE8P8l3PS+bshUQIDAQAB
AoGAU4NyN4kpCjj3f11t7ZN/4sAwVKmyYOQcVV3sN8hmCsvx9gBfcpgirWK5hT3i
MQGAldtBAUjwaTLoL28YDCuLzDevz8aIGR+32D7Dpel7gRSEtjj25tBRqb23DJh3
c/pQSlizQDw9tU78j1ZkXWAHnYiUTEVeGUZ2vDVv+v53lN0CQQDxkaGTmw9/tN3n
Drhw0ZR/0wktmpyf57A/3faOjolOSQwyRllfv9qBDTTUqBVRL86tSzEAUBNLr1qJ
QNb/q0YDAkEA3FjEvXqxeAzH5XBO5TjpVzrUDz9utxt3lUpeFYm5E8Bm2+v53YdK
dp+Mw13X6uNRYzcbgljcE26xXIjuvvmVGwJBAM4Rr0XdRrFoNst+MTR8dDM+cVvn
wqhd2moBDOy7BsIzaiYRAPi/DsR74Y9u+xBQufv2YoyjwnIT2iWvnDhpgMUCQQC6
VcMCLQh46e39Q80kIM2Ku6/quQyqgerNb9dCRXYiktko71QclzVMPT5vVCOsedEw
osB7qSNqt3f7Nb0X+L2zAkAFwno2kw/fR1itt2ks7yZCmvo65yXoA0UylNfXjtLK
IcfN3m0FdlTKlgs8fDAIPhQ2kqzg0fOPZYj/3rIhGoze
-----END RSA PRIVATE KEY-----';


//安全检验码，以数字和字母组成的字符
$llpay_config['key'] = '201408071000001539_sahdisa_20141205';

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

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