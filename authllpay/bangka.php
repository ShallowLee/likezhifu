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
	var useSign = true;
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
    var name = "李大伟";
    var bind_mob = "13581918190";
    var card_no = "6226620205533417";
    var id_no = "330106197903250417";

	var jsonstr = '{"acct_name":"['+name+']","api_version":"1.0","bind_mob":"['+bind_mob+']","card_no":"['+card_no+']","dt_order":"20170401191638","flag_chnl":"3","flag_pay_product":"1","id_no":"['+id_no+']","id_type":"0","no_order":"20170401191638","oid_partner":"201710120001012537","risk_item":"{user_info_bind_phone:['+bind_mob+'],user_info_dt_register:20131030122130,risk_state:1,frms_ware_category:1009}","sign":"MIICXQIBAAKBgQCuSGeNqwbwTj1CW5TijRkIDSCU1VRu+iFTxqb1vkGlFkX5+NqGmybbDH0BBhQAFg+uitAvxYhwGRFHsGzr6EBc2Qlbg9NYCPch3aQS0eQTw5snc65ZCVglbFeb3RUfO+Zk6UhijWCK7viQP4PA8u0ho2KY6EPI7P4XUn0EZU1LcwIDAQABAoGBAIdZwdpbFaNxD9BGMaWUcPk4wLH1z4H0jgdzAt1c6bxdRPEym/vn6NA7raUq5EOA3qLuOWqwXRq5zRrA4IaBs+FfsqUdKC4leSfmcy71I1NqCADgOKLhw5HVT2cPo/rAYIMcCuC6QwhZvFJf+3Ei7Gt6uOmfwCMxXDhramnF6x3xAkEA020SgB6psH2kwdtE9pG+UNRkZrIa2kDQPGvNVCW9CX8nkw97RpOgQb4e+UWxr15fHVERQ9Hhs26955CLJ2C76wJBANMGqmwC98MGCyvVff9dDfGCY0NCSrx5wJF6LrgJ5LzWEWtMDEE0GrO5LpVL0gos36FXyfYmT6fGb7dphxjj9JkCQDIRgnAU0tqY5ouCAbLikM9TfPNU+kb1Ulr8rINg7INzOUv6PpdYHntzKqKdfpapIp0utYf6DBkSAKb18/4/SkECQHI32WY89P2J9+Hx3BCrvTBK8o8lAm6hN6mGCGN0d2n+M89vifKA0TMVuDD2qOj5ANHDQn0In3Sf4zfS1lyShYECQQCVm70BwmNsl3h1ufQmIwzJ4qIAaz02bn05rBzNCW1t4JVoQsnpeKkGZR7yOJOzJ66h469eo59IDLnfGFcAs7f9","sign_type":"RSA","time_stamp":"20170401191638","user_id":"LLUser"}';
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

