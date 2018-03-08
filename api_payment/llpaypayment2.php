<?php


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
$llpay_payment_url = 'https://fourelementapi.lianlianpay.com/paycreatebill';
$llpay_payment_url2 = 'https://fourelementapi.lianlianpay.com/signapply';
$llpay_payment_url3 = 'https://instantpay.lianlianpay.com/paymentapi/payment.htm';

//需http://格式的完整路径，不能加?id=123这类自定义参数

/************************************************************/
$time = time();
//构造要请求的参数数组，无需改动
$parameter = array (
	"oid_partner" => trim($llpay_config['oid_partner']),
	"sign_type" => trim($llpay_config['sign_type']),
    'time_stamp'=> date('YmdHis'),
	"user_id" => $time,
    'no_order'=>$time,
	"dt_order" => $dt_order,
	"risk_item" => '{"frms_ware_category":"2010","user_info_mercht_userno":"'.$time.'",
	"user_info_bind_phone":"17712899672","user_info_dt_register":"20141015165530","user_info_id_no":"410381199203286531",
	"user_info_identify_type":"1","user_info_identify_state":"1"}',
	"flag_pay_product" => 1,
	"flag_chnl" => 3,
	"id_type" => 0,
    'bind_mob'=>'17712899672',
	"id_no" => '410381199203286531',
	"acct_name" => '宋宇辉',
	"card_no" => '6212264301012423982',
    'notify_url'=>$notify_url,
    'money_order'=>1000.00,
	"api_version" => $api_version
);
//建立请求
$llpaySubmit = new LLpaySubmit($llpay_config);
//对参数排序加签名
$sortPara = $llpaySubmit->buildRequestPara($parameter);
//传json字符串
$json = json_encode($sortPara,true);
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>LianLianPay Wap Demo</title>
    <link rel="stylesheet" type="text/css" href="https://wap.lianlianpay.com/css/style.css">
    <style>
        .info {
            padding: 15px;
            background: #fff url(https://wap.lianlianpay.com/images/info_bg.png) left bottom repeat-x;
        }

        .table_ui {
            width: 100%;
            margin: 0 auto;
        }

        .table_ui td {
            line-height: 1.5em;
            padding-bottom: 10px;
            vertical-align: top;
        }

        .ft_gray {
            color: #999;
        }

        .slogan {
            overflow: hidden;
            width: 100%;
            height: 19px;
            position: relative;
            margin: 20px 0 5px 0;
        }

        .slogan h3 {
            font-size: 18px;
            line-height: 19px;
            padding-left: 1%;
            color: #4d4d4d;
            position: absolute;
            background: #f2f2f2;
            z-index: 100;
            padding: 0 0.215em;
            font-weight: normal;
            font-family: "微软雅黑";
        }

        .slogan span {
            height: 9px;
            border-bottom: 1px solid #cacaca;
            width: 100%;
            position: absolute
        }

        .warp {
            width: 95%;
            margin: 0 auto;
        }

        .footer {
            text-align: center;
            color: #999;
            padding: 2em 0 1em 0;
        }

        .footer img {
            height: 15px;
            vertical-align: middle;
        }

        .footer span {
            height: 15px;
            font-size: 0.8em;
            line-height: 0.8em;
        }
    </style>

    <script src="https://wap.lianlianpay.com/lib/jquery-1.7.1.min.js"></script>
    <script>
        var jsonobj = {};
        //选择签约还是支付
        var useSign = false;
        //签约模拟创单
        var signCreateUrl = 'https://fourelementapi.lianlianpay.com/repay/signcreatebill';
        //支付模拟创单
        var payCreateUrl = 'https://fourelementapi.lianlianpay.com/repay/paycreatebill';

        jsonobj.api_version = '1.0';
        jsonobj.no_order = <?php echo time();?>;
        jsonobj.oid_partner = '201710120001012537';
        jsonobj.time_stamp = <?php echo date('YmdHis')?>;
        jsonobj.user_id = '<?php echo date('YmdHis')?>';
        jsonobj.flag_chnl = '3';
        var moneyOrder = '0.01';
        var actionURL = useSign?signCreateUrl:payCreateUrl;
        if (!useSign) {
            jsonobj.money_order = moneyOrder;
        }
        $(document).ready(function(){
            //四要素

            var jsonstr = '<?php echo $json;?>';
            var payOrSign = useSign?"sign":"pay";
            console.log(payOrSign+"CreateBillRequest:" + jsonstr);

            $.ajax({
                type: 'POST',
                url: actionURL,
                contentType: "application/json",
                dataType: 'json',
                data:jsonstr,
                success: function(data){
                    console.log(payOrSign+"CreateBillReturned:"+JSON.stringify(data));
                    if(data.ret_code=="0000"){
                        token = data.token;
                        jsonobj.token = data.token;
                    }else{
                        alert(payOrSign + "CreateBillFailedWithCode[" + data.ret_code + "]" + data.ret_msg);
                    }
                },
                error:function(){
                    alert(payOrSign+'CreateBillError');
                }
            })

        });
    </script>
</head>
<body>
<div class="header">
    <a href="javascript:history.go(-1);" class="back">返回</a>
    <h1 class="logo">银行卡支付</h1>
    <a href="/llpayh5/about.html" class="about">关于</a>
</div>
<section class="info">
    <table border="0" cellspacing="0" cellpadding="0" class="table_ui">
        <tr>
            <td width="200"><span class="ft_gray"></span></td>
            <td style="text-align: right"></td>
        </tr>
        <tr>
            <td><span class="ft_gray">房型：</span></td>
            <td style="text-align: right">高级套房</td>
        </tr>
        <tr>
            <td width="100"><span class="ft_gray">房间面积：</span></td>
            <td style="text-align: right">50</td>
        </tr>
        <tr>
            <td width="100"><span class="ft_gray">楼层：</span></td>
            <td style="text-align: right">2-5</td>
        </tr>
        <tr>
            <td width="100"><span class="ft_gray">早餐：</span></td>
            <td style="text-align: right">含双早</td>
        </tr>
        <tr>
            <td width="100"><span class="ft_gray">宽带：</span></td>
            <td style="text-align: right">免费</td>
        </tr>
        <tr>
            <td width="100"><span class="ft_gray">到店时间：</span></td>
            <td style="text-align: right">2013-07-25</td>
        </tr>
    </table>
</section>
<section class="slogan">
    <h3>
        请仔细阅读 <a href="javascript:void(0)">《网上订购须知》</a>
    </h3>
    <span class="line"></span>
</section>
<section>
    <div class="form_warp">
        <ul>
            <li><button class="btn" type="button" id="sign_btn">LianLianPay 签约</button></li>
        </ul>
    </div>
    <div class="form_warp">
        <ul>
            <li><button class="btn" type="button" id="pay_btn">LianLianPay 支付</button></li>
        </ul>
    </div>
</section>
<footer class="warp footer">
    <img src="images/logo.png" /> 连连支付版权所有 2004-2017 浙B2-20080148
</footer>
<script type="text/javascript" src="https://wap.lianlianpay.com/lib/llpay.min.js" charset="utf-8"></script>
<script>
    $("#sign_btn").click(function(){
        console.log("SignInitRequestData:"+JSON.stringify(jsonobj));
        new LLPay().setData(JSON.stringify(jsonobj)).signReq(function(data){
            console.log("SignReturnedData:"+JSON.stringify(data));
            alert("SignReturnedData:"+JSON.stringify(data));
        });
    })

    $("#pay_btn").click(function(){
        console.log("PayInitRequestData:"+JSON.stringify(jsonobj));
        new LLPay().setData(JSON.stringify(jsonobj)).payReq(function(data){
            console.log("PayInitRequestData:"+JSON.stringify(data));
            alert("PayInitRequestData:"+JSON.stringify(data));
        });
    })
</script>
</body>
</html>



