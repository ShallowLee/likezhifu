<?php
require_once ("api_payment/llpay.config.php");
require_once ("api_payment/lib/llpay_security.function.php");
require_once ("authllpay_php/authllpay/lib/llpay_submit.class.php");
class PayAction extends CommonAction{
public $llpay_payment_url3 = 'https://instantpay.lianlianpay.com/paymentapi/payment.htm';
    public $notify_url = "/index.php/Pay/notify_url/";
    //页面跳转同步通知页面路径
    public $return_url = "index.php/Pa//return_url";
    public function index(){
		$ordernum = I("ordernum",'','trim');
		if(!$ordernum){
			$this->redirect('Index/index');
		}
		$Payorder = D("payorder");
		$orderinfo = $Payorder->where(array('ordernum' => $ordernum))->find();
		if(!$orderinfo){
			$this->redirect('Index/index');
		}
		//require_once(BASE_PATH."/shanpay/lib/shanpayfunction.php");
		$out_order_no = $ordernum;//商户网站订单系统中唯一订单号，必填
        $platform = 'wallet.likezhifu.cn';
        $api_version = '1.0';
        //对私标记
        $flag_card = '0';
        $parameter3 = array (
            "oid_partner" => trim($llpay_config['oid_partner']),
            "sign_type" => trim($llpay_config['sign_type']),
            "no_order" => $out_order_no,
            "dt_order" => date('YmdHis'),
            "money_order" => $orderinfo['money'],
            "acct_name" => $_SESSION['username'],
            "card_no" => $_SESSION['card_no'],
            "info_order" => '测试',
            "flag_card" => $flag_card,
            "notify_url" => $platform,
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
        $html_text = $llpaySubmit->buildRequestJSON($parameterRequest,$this->llpay_payment_url3);
        echo $html_text;
	}

    //还款支付
    public function turnBack(){
        $risk_item = '{\"frms_ware_category\":\"2009\",\"user_info_mercht_userno\":\"123456\",\"user_info_dt_register\":\"20141015165530\",\"user_info_full_name\":\"张三\",\"user_info_id_no\":\"3306821990012121221\",\"user_info_identify_type\":\"1\",\"user_info_identify_state\":\"1\"}';
        $Payorder = D("payorder");
        $ordernum = I("ordernum",'','trim');
        $orderinfo = $Payorder->where(array('ordernum' => $ordernum))->find();
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
        //构造要请求的参数数组，无需改动
        $parameter = array (
            "oid_partner" => trim($llpay_config['oid_partner']),
            "app_request" => trim($llpay_config['app_request']),
            "sign_type" => trim($llpay_config['sign_type']),
            "valid_order" => trim($llpay_config['valid_order']),
            "user_id" => time(),
            "busi_partner" => '101001',
            "no_order" => $ordernum,
            "dt_order" => local_date('YmdHis', time()),
            "name_goods" => '测试',
            "info_order" => 'ceshi',
            "money_order" => $orderinfo['money'],
            "notify_url" => C('cfg_siteurl').$this->notify_url,
            "url_return" => C('cfg_siteurl').$this->return_url,
            "card_no" => '6212264301012423982',
            "acct_name" => '宋宇辉',
            "id_no" => '410381199203286531',
            "no_agree" => '',
            "risk_item" => $risk_item,
            "valid_order" => 10080,
        );
        //建立请求
        $llpaySubmit = new LLpaySubmit($llpay_config);
        $html_text = $llpaySubmit->buildRequestForm2($parameter, "post", "确认");
        echo $html_text;
        exit;
    }
    //还款异步通知
    public function notify_url(){
        require_once ("/authllpay_php/authllpay/llpay.config.php");
        require_once ("/authllpay_php/authllpay/lib/llpay_notify.class.php");

        //计算得出通知验证结果
        $llpayNotify = new LLpayNotify($llpay_config);
        $llpayNotify->verifyNotify();
        if ($llpayNotify->result) { //验证成功
            //获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $no_order = $llpayNotify->notifyResp['no_order'];//商户订单号
            $oid_paybill = $llpayNotify->notifyResp['oid_paybill'];//连连支付单号
            $result_pay = $llpayNotify->notifyResp['result_pay'];//支付结果，SUCCESS：为支付成功
            $money_order = $llpayNotify->notifyResp['money_order'];// 支付金额
            if($result_pay == "SUCCESS"){
                $this->paydo2(true,$no_order,$money_order);
            }
            file_put_contents("log.txt", "异步通知 验证成功\n", FILE_APPEND);
            die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            file_put_contents("log.txt", "异步通知 验证失败\n", FILE_APPEND);
            //验证失败
            die("{'ret_code':'9999','ret_msg':'验签失败'}");
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
    //还款同步通知
    public function return_url(){
        require_once("/authllpay_php/authllpay/llpay.config.php");
        require_once("/authllpay_php/authllpay/lib/llpay_notify.class.php");
        include_once ('/authllpay_php/authllpay/lib/llpay_cls_json.php');
        //计算得出通知验证结果
        $llpayNotify = new LLpayNotify($llpay_config);
        $verify_result = $llpayNotify->verifyReturn();
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取连连支付的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            $json = new JSON;
            $res_data = $_POST["res_data"];


            //商户编号
            $oid_partner = $json->decode($res_data)-> {'oid_partner' };

            //商户订单号
            $no_order = $json->decode($res_data)-> {'no_order' };

            //支付结果
            $result_pay =  $json->decode($res_data)-> {'result_pay' };

            if($result_pay == 'SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（no_order）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //$this->paydo2(true,$no_order,$money_order);
            }
            else {
                echo "result_pay=".$result_pay;
            }

            echo "验证成功<br />";

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            //如要调试，请看llpay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
    }
	//同步通知
	public function returnurl(){
		$shanNotify = md5VerifyShan($_REQUEST['out_order_no'],$_REQUEST['total_fee'],$_REQUEST['trade_status'],$_REQUEST['sign'],C('cfg_paykey'),C('cfg_paypartner'));
		if(!$shanNotify){
			//验证失败
			$this->redirect('Index/index');
		}else{
			if($_REQUEST['trade_status']!='TRADE_SUCCESS'){
				//支付失败
				$this->redirect('Index/index');
			}else{
				$this->paydo();
			}
		}
	}
	
	//异步通知
	public function notifyurl(){
		$shanNotify = md5VerifyShan($_REQUEST['out_order_no'],$_REQUEST['total_fee'],$_REQUEST['trade_status'],$_REQUEST['sign'],C('cfg_paykey'),C('cfg_paypartner'));
		if(!$shanNotify){
			echo "fail";
		}else{
			if($_REQUEST['trade_status']=='TRADE_SUCCESS'){
				$this->paydo(false);
			}
			echo 'success';
		}
	}
	
	
	//支付成功处理
	function paydo($jump = true){
		$out_trade_no = $_REQUEST['out_order_no'];
		$money = $_REQUEST['total_fee'];
		$Payorder = D("payorder");
		$info = $Payorder->where(array('ordernum' => $out_trade_no))->find();
		if(!$info){
			//订单不存在
			if($jump) $this->redirect('Index/index');
		}else{
			if($info['status'] == 1){
				//已经处理，跳过
				if($jump) $this->redirect('Index/index');
			}
			$Payorder->where(array('ordernum' => $out_trade_no))->save(array('status' => 1));
			if($info['type'] == "审核费"){
				$Order = D("order");
				$order = $Order->where(array('pid' => $info['id']))->find();
				//将借款订单设置为已支付
				if($order && $order['status'] == 0){
					$Order->where(array('pid' => $info['id']))->save(array('status' => 1));
				}
				if($jump) $this->redirect('Order/info',array('oid' => $order['id']));
			}elseif($info['type'] == "还款费"){
				//写入还款记录
				$Bills = D("bills");
				$arr = array(
					'user'     => $info['user'],
					'addtime'  => time(),
					'money'    => $money,
					'ordernum' => $out_trade_no
				);
				$Bills->add($arr);
				//订单信息更改已还款期数
				$Order = D("order");
				$Order->where(array('ordernum' => $info['jkorder'],'user' => $info['user']))->setInc('donemonth',1);
				if($jump) $this->redirect('Order/bills');
			}else{
				//未知类型支付
				if($jump) $this->redirect('Index/index');
			}
		}
	}
    //支付成功处理
    function paydo2($jump = true,$out_trade_no,$money){
        $Payorder = D("payorder");
        $info = $Payorder->where(array('ordernum' => $out_trade_no))->find();
        if(!$info){
            //订单不存在
            if($jump) $this->redirect('Index/index');
        }else{
            if($info['status'] == 1){
                //已经处理，跳过
                if($jump) $this->redirect('Index/index');
            }
            $Payorder->where(array('ordernum' => $out_trade_no))->save(array('status' => 1));
            if($info['type'] == "审核费"){
                $Order = D("order");
                $order = $Order->where(array('pid' => $info['id']))->find();
                //将借款订单设置为已支付
                if($order && $order['status'] == 0){
                    $Order->where(array('pid' => $info['id']))->save(array('status' => 1));
                }
                if($jump) $this->redirect('Order/info',array('oid' => $order['id']));
            }elseif($info['type'] == "还款费"){
                //写入还款记录
                $Bills = D("bills");
                $arr = array(
                    'user'     => $info['user'],
                    'addtime'  => time(),
                    'money'    => $money,
                    'ordernum' => $out_trade_no
                );
                $Bills->add($arr);
                //订单信息更改已还款期数
                $Order = D("order");
                $Order->where(array('ordernum' => $info['jkorder'],'user' => $info['user']))->setInc('donemonth',1);
                if($jump) $this->redirect('Order/bills');
            }else{
                //未知类型支付
                if($jump) $this->redirect('Index/index');
            }
        }
    }
}
