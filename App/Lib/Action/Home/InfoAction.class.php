<?php
class InfoAction extends CommonAction{
    private $userinfo;
    public  $apiUrl = "https://api.yongxunzhengxin.com/identity/v1/idcard";
    public  $apiUrl2 = 'https://api.yongxunzhengxin.com/identity/v1/bankcard4';
    public $appkey = "1705245261529168";//!!!需自行设定!!!
    public $apiSecret = "l0yWPFFB4HxRjf6Uqhchg1dmXg4KA429";//!!!需自行设定!!!
    public $version = "1.2.0";
    function _initialize(){
        $user = $this->getLoginUser();
        if(!$user){
            $this->redirect('User/login');
        }
        $Userinfo = D("userinfo");
        $info = $Userinfo->where(array('user' => $this->getLoginUser()))->find();
        if(!$info){
            $infoid = $Userinfo->add(array('user' => $this->getLoginUser()));
            $info = array('id' => $infoid,'user' => $this->getLoginUser());
        }
        $this->userinfo = $info;
    }

    public function index(){
        $Userinfo = D("userinfo");
        $xycx = D("xycx");
        $info = $Userinfo->where(array('user' => $this->getLoginUser()))->find();
        $xy = $xycx->where(array('user' => $this->getLoginUser()))->find();
        $arr = array(
            'baseinfo' => 0,
            'unitinfo' => 0,
            'bankinfo' => 0,
            'zhimainfo'=> 0,
            'wechat'   => 0,
            'phoneinfo' => 0,
            'xyc' => 0
        );
        //判断资料完整性（姓名 身份证号 三张照片）
        if($info['name'] && $info['usercard'] && $info['cardphoto_1'] && $info['cardphoto_2'] && $info['cardphoto_3'] ){
            $arr['baseinfo'] = 1;
        }
        if($info['dwname'] && $info['dwaddess_ssq'] && $info['dwaddess_more'] && $info['position'] && $info['workyears'] && $info['addess_ssq'] && $info['addess_more'] && $info['dwysr'] && $info['personname_1'] && $info['personphone_1'] && $info['persongx_1'] && $info['personname_2'] && $info['personphone_2'] && $info['persongx_2']){
            $arr['unitinfo'] = 1;
        }
        if($info['bankcard'] && $info['bankname']){
            $arr['bankinfo'] = 1;
        }
        //芝麻信用分
        if($info['alipay']){
            $arr['zhimainfo'] = 1;
        }
        //微信
        if($info['wechat']){
            $arr['wechat'] = 1;
        }
        if($info['phone']){
            $arr['phoneinfo'] = 1;
        }
        if($xy['mobile']){
            $arr['xyc'] = 1;
        }
        $this->info = $arr;
        $this->display();
    }

    //身份信息
    //姓名、身份证号码、住址
    public function baseinfo(){
        if(IS_POST){
            $data = array('status' => 0,'msg' => '未知错误');
            $Userinfo = D("userinfo");
            $_POST['change_time'] = time();
            if($this->user->is_check!=1){
                $arr = $this->process($_POST['name'],$_POST['usercard']);
                if((isset($arr['data'])) && ($arr['data']['resultCode']=='1')){
                    $_POST['is_check'] = 1;
                }else{
                    $data['msg'] = "身份证信息不一致";
                    exit(json_encode($data));
                }
            }
            $status = $Userinfo->where(array('user' => $this->getLoginUser()))->save($_POST);
            if(!$status){
                $data['msg'] = "操作失败";
            }else{
                $photo1=$_POST['cardphoto_1'];
                $photo2=$_POST['cardphoto_2'];
                $photo3=$_POST['cardphoto_3'];
                $name=$_POST['name'];
                $carid=$_POST['usercard'];
                $base1=$this->base64($photo1);
                $base2=$this->base64($photo2);
                $base3=$this->base64($photo3);
                $renlian=$this->basecurl($base3);
                $sfzzm=$this->sfzzmcurl($base1);
                $sfzfm=$this->sfzfmcurl($base2);
                $user=$this->userinfo['user'];
                $xycx=M('xycx');
                $isuser=$xycx->where('user='.$user)->field('id')->select();
                //print_r($isuser);
                if($isuser){
                    foreach($isuser as $k=>$v){
                        $datarenlian['id']=$v['id'];
                        $datarenlian['renlian']=$renlian;
                        $datarenlian['sfzzm']=$sfzzm;
                        $datarenlian['sfzfm']=$sfzfm;
                        $xycx->save($datarenlian);
                    }
                }else{
                    $dataadd['user']=$user;
                    $dataadd['date']=date("Y-m-d H:i:s");
                    $dataadd['renlian']=$renlian;
                    $dataadd['sfzzm']=$sfzzm;
                    $dataadd['sfzfm']=$sfzfm;
                }
                $data['status'] = 1;
            }
            $data['msg'] = "操作成功";
            exit(json_encode($data));

        }
        $this->assign("userinfo",$this->userinfo);
        $this->display();
    }

    function getParamString($params){
        //计算签名
        $paramsSign = $params;
        //$paramsSign['version'] = $this->version.$this->apiSecret;

        //按照key排序
        ksort($paramsSign);

        //echo(urldecode(http_build_query($paramsSign)))."</br>";
        //加密获取sign
        //$sign=sha1(urldecode(http_build_query($paramsSign)));//对该字符串进行 SHA-1 计算，得到签名，并转换成 16 进制小写编码
        //设置请求参数
        //$params['version'] = $this->version;
       // $params['sign'] = $sign;

        $paramString = http_build_query($params);

        return $paramString;
    }
    function curl($url,$params=false,$ispost=0){
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if( $ispost )
        {
            $time = time();
            curl_setopt( $ch , CURLOPT_POST ,true);
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
            curl_setopt ($ch, CURLOPT_HTTPHEADER, array(
                    'authorization-key: '.'1705245261529168',
                    'authorization-timestamp: '.$time,
                    'authorization-sign: ' .sha1('1705245261529168'.$time.'l0yWPFFB4HxRjf6Uqhchg1dmXg4KA429'),

                )
            );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        curl_close( $ch );
        return $response;
    }
    function process($name,$identityNo){

        $params = array(
            //"method" => 'api.identity.idcheck',
            //"apiKey" => $this->appkey,
            "name" => $name,
            "identityNo" => $identityNo,
        );

        //生成参数字符串
        $paramString = $this->getParamString($params);

        //提交请求
        $content = $this->curl($this->apiUrl,$paramString,1);
        $result = json_decode($content,true);
        return $result;
    }
    function process2($name,$identityNo,$bank,$mobile){

        $params = array(
            "bankCardNo" => $bank,
            "mobile" => $mobile,
            "name" => $name,
            "identityNo" => $identityNo,
        );

        //生成参数字符串
        $paramString = $this->getParamString($params);

        //提交请求
        $content = $this->curl($this->apiUrl2,$paramString,1);
        $result = json_decode($content,true);
        return $result;
    }
    protected function base64($data){//图片转码base64
        $file = $data;
        $search="http://".$_SERVER['HTTP_HOST']."/";
        $file=str_replace($search,"",$file);
        if($fp = fopen($file,"rb", 0))
        {
            $gambar = fread($fp,filesize($file));
            fclose($fp);
            $base64 = chunk_split(base64_encode($gambar));
        }
        return $base64;
    }
    protected function basecurl($base){
        $host = "https://dm-21.data.aliyun.com";
        $path = "/rest/160601/face/detection.json";
        $method = "POST";
        $appcode = "b0e289771ab348678e4fe41c33af4ca0";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
        $querys = "";
        $bodys = '{"inputs":[{"image":{"dataType":50,"dataValue":"'.$base.'"}}]}';
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $result=curl_exec($curl);
        return $result;
    }
    protected function sfzzmcurl($base) {
        //echo $base;
        $host = "https://dm-51.data.aliyun.com";
        $path = "/rest/160601/ocr/ocr_idcard.json";
        $method = "POST";
        $appcode = "b0e289771ab348678e4fe41c33af4ca0";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
        $querys = "";
        $bodys =  "{
					    \"inputs\": [
					        {
					            \"image\": {
					                \"dataType\": 50,
					                \"dataValue\": \"".$base."\"
					            },
					            \"configure\": {
					                \"dataType\": 50,
					                \"dataValue\": \"{\\\"side\\\":\\\"face\\\"}\"
					            }
					        }
					    ]
					}";
        $url = $host . $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER,false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $result=curl_exec($curl);
        return $result;
    }
    protected function sfzfmcurl($base) {
        //echo $base;
        $host = "https://dm-51.data.aliyun.com";
        $path = "/rest/160601/ocr/ocr_idcard.json";
        $method = "POST";
        $appcode = "b0e289771ab348678e4fe41c33af4ca0";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
        $querys = "";
        $bodys =  "{
					    \"inputs\": [
					        {
					            \"image\": {
					                \"dataType\": 50,
					                \"dataValue\": \"".$base."\"
					            },
					            \"configure\": {
					                \"dataType\": 50,
					                \"dataValue\": \"{\\\"side\\\":\\\"back\\\"}\"
					            }
					        }
					    ]
					}";
        $url = $host . $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER,false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $result=curl_exec($curl);
        return $result;
    }

    public function xycx(){

        $this->assign("userinfo",$this->userinfo);
        //print_r($this->userinfo);
        $this->display();
    }

    /**
     * 获取状态
     */
    public function getCommonStatus(){
        if($_POST){
            $apiKey='5498845899219165';
            $token = $_POST['token'];
            $getstatus=array(
                'method'=>'api.common.getStatus',
                'apiKey'=>$apiKey,
                'version'=>'1.2.0',
                'token'=>$token,
                'bizType'=>'mobile',
            );
            $status=$this->paixu($getstatus);
            $resultstatus=$this->curl($status);
            $resultstatus=json_decode($resultstatus,true);
            if($resultstatus['code']=='0000'){
                cookie('token',$resultstatus['token']);
            }
            $this->ajaxReturn($resultstatus);
        }
    }

    /**
     * 短信写入提交
     */
    public function getMobileCode(){
        if($_POST){
            $apiKey='4457446580226125';
            $token = $_POST['token'];
            $input = $_POST['smsCode'];
            $inputarray=array(
                'method'=>'api.common.input',
                'apiKey'=>$apiKey,
                'version'=>'1.2.0',
                'token'=>$token,
                'input'=>$input,
            );
            $status=$this->paixu($inputarray);
            $resultstatus=$this->curl($status);
            $resultstatus=json_decode($resultstatus,true);
            $this->ajaxReturn($resultstatus);
        }
    }

    /**
     * 获取数据
     */
    public function getData(){
        $apiKey='4457446580226125';
        $token = cookie('token');
        $post=array(
            'method'=>'api.common.getResult',
            'apiKey'=>$apiKey,
            'version'=>'1.2.0',
            'token'=>$token,
            'bizType'=>'mobile',
        );
        //print_r($inputarray);
        $inputdata=$this->paixu($post);
        $inputresult=$this->curl($inputdata);
        //echo $inputresult;
        $inputarr=json_decode($inputresult,true);
        if($inputarr['code']=="0000"){
            $xycx=D('xycx');
            $adddata['user']=$this->userinfo['user'];
            $adddata['date']=date("Y-m-d H:i:s");
            $adddata['text']=$inputresult;
            $adddata['mobile']=cookie('userMoblie');
            $xycx->add($adddata);
        }
        $this->ajaxReturn($inputarr);
//            echo "<pre>";
//            print_r($inputarr);
//            echo "</pre>";exit;

    }
    //手机运营商查询提交
    public function xycxpost(){
        if($_POST){
            //print_r($_POST);
            $apiKey='4457446580226125';
            $name=$_POST['name'];
            $card=$_POST['usercard'];
            $mobile=$_POST['mobile'];
            cookie('userMoblie',$mobile);
            $mobilepassword=$_POST['mobilepassword'];
            $user=$_POST['user'];
            $input=$_POST['code'];
            if(!$input){
                $array=array(
                    'method'=>'api.mobile.get',
                    'apiKey'=>$apiKey,
                    'version'=>'1.2.0',
                    'username'=>$mobile,
                    'password'=>base64_encode($mobilepassword),
                );
                $data=$this->paixu($array);
                $result=$this->curl($data);
                //echo $result;
                //exit;
                $result=json_decode($result,true);
                $this->ajaxReturn($result);//exit;
                if($result['code']!="0010"){
                    print_r($result);
                    //echo "错误！查看手机号码或者服务密码是否正确";
                    exit;
                }
                $token=$result['token'];
                cookie("token",$token);
                $getstatus=array(
                    'method'=>'api.common.getStatus',
                    'apiKey'=>$apiKey,
                    'version'=>'1.2.0',
                    'token'=>$token,
                    'bizType'=>'mobile',
                );

                $status=$this->paixu($getstatus);
                $i = 1;
//				while($i<=20){
//					$resultstatus=$this->curl($status);
//					$resultstatus=json_decode($resultstatus,true);
//					if($resultstatus['code']){
//						$i++;
//					}
//				}
//				do{
//					$resultstatus=$this->curl($status);
//					$resultstatus=json_decode($resultstatus,true);
//					if($resultstatus['codde']=='0000'){
//						echo 888;exit;
//						break;
//					}
//					if($conut-- <=0){
//						break;
//					}
////
//				}while(1);

//				//print_r($resultstatus);
                //if($resultstatus['code']!="0000" and $resultstatus['code']=="0006"){
                //exit("888");
                //}
            }

            if($input){
                $inputarray=array(
                    'method'=>'api.common.input',
                    'apiKey'=>$apiKey,
                    'version'=>'1.2.0',
                    'token'=>cookie('token'),
                    'input'=>$input,
                );
                //print_r($inputarray);
                $inputdata=$this->paixu($inputarray);
                $inputresult=$this->curl($inputdata);
                //echo $inputresult;
                $inputarr=json_decode($inputresult,true);
                //echo $inputarr['code'];
                if($inputarr['code']!="0009"){
                    exit("111");
                }

            }
            //echo "ttttttt";
            $get=array(
                'method'=>'api.common.getResult',
                'apiKey'=>$apiKey,
                'version'=>'1.2.0',
                'token'=>cookie('token'),
                'bizType'=>'mobile',
            );
            //print_r($get);
            $mobiledata=$this->paixu($get);
            $resultdata=$this->curl($mobiledata);
            $resultstatus=json_decode($resultdata,true);
            $xycx=D('xycx');
            if($resultstatus['code']=="0000"){
                $adddata['user']=$user;
                $adddata['date']=date("Y-m-d H:i:s");
                $adddata['text']=$resultdata;
                $adddata['mobile']=$mobile;
                $xycx->add($adddata);
                exit("999");
            }
        }
    }
    protected function paixu($array){
        $apiSecret="dVRGoQefnPJAKQJTjFZvzWMDmOVKW0Hn";
        ksort($array);
        $i=0;
        foreach($array as $key=>$value){
            if($i==0){
                $str.=$key."=".$value;
                $i++;
            }else{
                $str.="&".$key."=".$value;
            }
        }
        $sstr=$str.$apiSecret;
        $array['sign']=sha1($sstr);
        $data=http_build_query($array);
        return $data;
    }

    //信用查询回调
    public function xycxcallback(){
        $input = file_get_contents('php://input');
        $path=date("YmdHis");
        file_put_contents($path.'.log', $input);
        file_put_contents('111.log', $_POST);
        file_put_contents('112.log', json_encode($_GET));

    }

    //提交到立木征信
    protected function curl3($data){
        //$url="https://api.limuzhengxin.com/api/gateway";    //生产地址
        $url="https://t.limuzhengxin.cn/api/gateway";          //测试地址
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        //echo curl_error($ch);
        curl_close($ch);
        return $output;
        //print_r($output);
    }

    //单位信息
    public function unitinfo(){
        if(IS_POST){
            $data = array('status' => 0,'msg' => '未知错误');
            $Userinfo = D("userinfo");
            $status = $Userinfo->where(array('user' => $this->getLoginUser()))->save($_POST);
            if(!$status){
                $data['msg'] = "操作失败";
            }else{
                $data['status'] = 1;
            }
            $this->ajaxReturn($data);
            exit;
        }
        $this->assign("userinfo",$this->userinfo);
        $this->display();
    }

    //银行卡信息
    public function bankinfo(){
        if(IS_POST){
            $data = array('status' => 0,'msg' => '未知错误');
            $bankcard = I("bankcard",0,'trim');
            $mobile = $_REQUEST['phone'];
            $userinfo = $this->userinfo;
            if(strlen($bankcard) < 16 || strlen($bankcard) > 20){
                $data['msg'] = "不是有效的银行卡号!";
                $this->ajaxReturn($data);
                exit;
            }else{
                if($mobile){
                    $arr = $this->process2($userinfo['name'],$userinfo['usercard'],$bankcard,$mobile);
                    //var_dump($arr);
                    if((isset($arr['data'])) && ($arr['data']['resultCode']=='1')){
                        $_POST['phone'] = $mobile;
                        $Userinfo = D("userinfo");
                        $_POST['change_time'] = time();
                        $status = $Userinfo->where(array('user' => $this->getLoginUser()))->save($_POST);
                        if(!$status){
                            $data['msg'] = "操作失败";
                            $this->ajaxReturn($data);
                            exit;
                        }else{
                            $data['status'] = 1;
                            $data['msg'] = "操作成功";
                            $this->ajaxReturn($data);
                            exit;
                        }
                    }else{
                        $data['msg'] = "身份证、银行卡信息不一致";
                        unset($_POST['phone']);
                        $this->ajaxReturn($data);
                        exit;
                    }
                }else{
                    $data['msg'] = "手机必须填写";
                    $this->ajaxReturn($data);
                    exit;
                }

            }

        }
        $this->assign("userinfo",$this->userinfo);
        $this->display();
    }

    //芝麻信用授权确认
    public function zhimastepone(){
        $userinfo = $this->userinfo;
        if($userinfo['alipay']){
            $this->redirect('Info/index');
        }
        $this->display();
    }

    //芝麻信用授权
    public function zhimasteptwo(){
        $userinfo = $this->userinfo;
        if($userinfo['alipay']){
           // $this->redirect('Info/index');
        }
        //if(IS_POST){
            ///$Userinfo = D("userinfo");
            //$status = $Userinfo->where(array('user' => $userinfo['user']))->save(array('alipay' => '1'));
            //if($status){
			//	$data['status'] = 1;
				$this->testZhimaAuthInfoAuthorize($userinfo['user']);
            //}else{
            //    $data['msg'] = "授权失败!";
            //}
            //$this->ajaxReturn($data);
            exit;
        //}
        $this->display();
    }

    //芝麻信用网关地址
    public $gatewayUrl = "https://openapi.alipaydev.com/gateway.do";
    //商户私钥文件
    public $privateKeyFile = "d:\\keys\\private_key.pem";
    //芝麻公钥文件
    public $zmPublicKeyFile = "d:\\keys\\public_key.pem";
    //数据编码格式
    public $charset = "UTF-8";
    //芝麻分配给商户的 appId
    public $appId = "2016081500252937";
    //芝麻信用授权获得参数用户在商户端的身份标识ID

    public function testZhimaAuthInfoAuthorize($useriphone){

		require_once("./alipaysdk/AopSdk.php");
		$aop = new AopClient();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = '2017101709348703';
		$aop->rsaPrivateKey = 'MIIEowIBAAKCAQEAncZ4KGldtp9J64k6r4DxFH3bnook43ys7s2W5wnPsN0XJDry5MG5TvLheJBahSb1imuIxfFZUw1V47mNZVQUUVAgvxGEzLlCeUZnjE9LdMsBQr6Mjhqyy8yUTXMDiiPEzJEgkxU/j6zo8MYrozbFWpNaG7hLjYZFDBnCq4wg40g0upIeQI/BhElw4/783lAVJVncKyZvmbztC1ripFu28Db2C44HWwHTUPrkBUmxPDaWZ142nGtsA9j+e51vcHh17RaoOYN7uNqC/jQbPMWA6em2PiMrPTHTKds42NOBkEKR1dR7uR00Wzyq3FzuHT3G1VOPy2azibsH3TIT83BaiwIDAQABAoIBAEkfgPUJ0HshXDsjwOUyV4ltw2m5ENu2HtgWxMeTjoSkE2OhRo2rE115x/H+xVVM9yQOLre+4e1SEuqWRugdjcUZ4/NqBSh0/FAEx10KKyiYJZ9vfOknipJV4K38jjlp1n4RDQ3eHTVTqCpfTj441kLiZDLQuYOAJWQ0VDr5mQx84laJD4OQDxEUXE8ZLL3YOovtEaXC7Fpu7ZStSKvdmqrL2Qa3CP8hTHZx6YYlBl5fBaFuTG3c9+cR8ZSDA/5XyyiAOz4lJO93e7mrR4Ee2qamgeZlhITyNdu4CtjjLrI2ZlFAHFjL4IoFE0NCvVLDb54G9Y/65gvUrxdjGrkj/DECgYEAyvadvvc8U+wufEHJmG3HDoqwJTFh0+Out1XGz2kd5G3OicPMbCaoHOgoY1Y1gRagzJRtvDGaDzKyf2+UT8UxOFhceI4Omh4sW4T5ExfiK0VovxJP9iqOcEhAjzX0Y5E06kaeXYYXzSZbSP5g35/rZwh5rlS01GcI4FK+EvUVIncCgYEAxwD2oW0ZWiHT8bjVmRuNqHWMUhDFuEWCtIHjl8O591IXv8hjS+SmewauMaj44E10t0LZtMo/Z2cOhVSmBRvMH+2IV/h0Mql+X21wfaXFHNtt7gtQTwcnHPpVYKgQKAOhCyJDm0iuCpkC6nyFKaH6cohGHrIK8PataPXcesv3WY0CgYEAuK6jZ4ss1+iHfAWY3Ry3DqJgGdfPfrEk4CorF7w8uQi58V26+4ZJXtRZO9rMIMRoWP+OwroXx1CIX7E2MUfru6ubqQ8Kdm6SSky7IAaRhHJKF+Mf441hwwXbyPR9hsQwdCUCIXPM4J3QXqEpoRKxbzWlE158lPGUgBqKXlGog40CgYAJhcJHdzD/Xx8FMhMGgIsGxJkyVBsh9jGK8awyVutPKl54jt4xg39z453yQmOmWA59tnU0cXkXOZc4ShSnqezamkMr8nec9P9XEaWQXZ5RAy7/dQsivPTiLhhnx6L+Ry4tmjYh+Bhb6XZ6QfsdQuL5GAxC68IaRbOGkj06owfzAQKBgBE3IBz6/D11m+5X7BTKO7GzXN48fYY55MxjAUjiXkaw05UXXnLOiLPEde91iBrIO326CLsXGOTsERlWw6LRccYlWn7+dK7+FryQ6Q51+dc7waZBMtsKpPDwaWBWgX3rsxJdy1Q3y+1pUoDTc+6+j42aPrjOMnIT2xdapCe/VbSa';
		$aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlQbvVF2n4SzcSMDS5+uKUFXVttATDgmNNLslBGZy2MiUAOgHvaJy33KkZgGWElVW3TwvBVdWXlYpd2hTpORApk71N3h6fh2msH+qtJc+RIg4v9xAx4H8bR0dIgS25V8FBlPH9HB/R5qqsSxVm68PxoB2dVt8AmaTQYZ/10fRiwWGnhyAKd4Mp98jE//wf7KGKizo1kJjIuncQLgtBsgEg4PLLkqcQyyEPGyZuT+DTGC7eYW3+uD8F5i/JyzMHakqLWUrKwdd8YR4CnQM8u8USrlhBJyg9Pxhfm9p9u0TRNYyeN5gicSbS+4MVDjWrrUYAsFXITURxeLBW2J6IFWRtwIDAQAB';
		$aop->apiVersion = '1.0';
		$aop->signType = 'RSA2';
		$aop->postCharset='utf-8';
		$aop->format='json';
		$request = new ZhimaCreditScoreBriefGetRequest();
		$request->setBizContent("{" .
		"\"transaction_id\":\"201512100936588040000000465158\"," .
		"\"product_code\":\"w1010100000000002733\"," .
		"\"cert_type\":\"IDENTITY_CARD\"," .
		"\"cert_no\":\"330106197903250417\"," .
		"\"name\":\"李大伟\"," .
		"\"admittance_score\":650" .
		"  }");
		$result = $aop->execute ( $request); 
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			var_dump($result);
			echo "成功";
		} else {
			echo "失败";
		}
    }

    //芝麻信用授权地址解密
    public function getResult($url) {
        //从回调URL中获取params参数，此处为示例值
        $params = 'BFMqwAYz615BnJQIloDJw5h8mfLMTv%2FjvoitHU2PFu7E%2FdO1cTprm0jZ6N6V73BU9KIO5Lc43DrkyEJ9P7%2BDnjUfsFOfbIuV4rSL%2BMe8IEMHtGC3KR6lUn4PZ5qc3VDx5hgdc0D5sCy8v3KgYeEGuXNcNws7F2dL30ze45yps%2FkW1f%2BUbs%2BFcXMYpoZz1dfh7LF78NsjmD1d0D9doM9z8yydgPdZ%2F8kdszCKnLre0iuq%2Bv%2FBHHcDr0NyRvhJQotNJqm%2BA590wUfb%2BpcI168g81av5a9naQHech%2F1z5OF%2BjHADMw%2BSdR6jklASJTCPq0p8rHTLmH0QOnOm7G6ePrG9w%3D%3D';

        //从回调URL中获取sign参数，此处为示例值
        $sign = 'YKbTxhXrEE8VmD7cdpD9FK6Wd00WwkgLn9N2zppfukIOMzQfL4WRsKcCJgHe3YFJRZB%2FVV%2BqGk7chQF5PAaVr1iJyocxGC4cp4UB7HhDnEf01OxGLsjdtqA735Tze3dJv4qzcssBj1edSx1DWECJhthecKaevUxcf2%2BLoe0cRQI%3D';

        //判断串中是否有%，有则需要decode
        $params = strstr ( $params, '%' ) ? urldecode ( $params ) : $params;
        $sign = strstr ( $sign, '%' ) ? urldecode ( $sign ) : $sign;

        $client = new ZmopClient ( $this->gatewayUrl, $this->appId, $this->charset, $this->privateKeyFile, $this->zmPublicKeyFile );
        $result = $client->decryptAndVerifySign ( $params, $sign );
        $this->testZhimaCreditScoreGet($result);
    }

    public function testZhimaCreditScoreGet($result){
        $client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);
        $request = new ZhimaCreditScoreGetRequest();
        $request->setChannel("apppc");
        $request->setPlatform("zmop");
        $request->setTransactionId("201512100936588040000000465158");// 必要参数
        $request->setProductCode("w1010100100000000001");// 必要参数
        $request->setOpenId("2016081500252937");// 必要参数
        $response = $client->execute($request);
        echo json_encode($response);
        $Userinfo = D("userinfo");
        $data = $Userinfo->where(array('user' => '13581918190'))->save(array('zhimaxinyou' => $response));
    }

    public function otherinfo(){
        $Otherinfo = D("otherinfo");
        if(IS_POST){
            $otherinfo = $_POST['otherinfo'];
            if(empty($otherinfo)) $otherinfo = array();
            $str = json_encode($otherinfo);
            $status = $Otherinfo->where(array('user' => $this->getLoginUser()))->find();
            if(!$status){
                $Otherinfo->add(array(
                    'user' => $this->getLoginUser(),
                    'infojson' => $str,
                    'addtime' => time()
                ));
            }else{
                $Otherinfo->where(array('user' => $this->getLoginUser()))->save(array('infojson' => $str));
            }
            exit;
        }
        $tmp = $Otherinfo->where(array('user' => $this->getLoginUser()))->find();
        $tmp = json_decode($tmp['infojson']);
        $data = array();
        for($i=0;$i<count($tmp);$i++){
            $data[$i]['sid'] = $i;
            $data[$i]['imgurl'] = $tmp[$i];
        }
        $this->data = $data;
        $this->display();
    }


    public function wechat(){
        $userinfo = $this->userinfo;
        if($userinfo['alipay']){
            $this->redirect('Info/index');
        }
        $code = I("code",'','trim');
        if($code && substr($code,0,1) == 'a'){
            $Userinfo = D("userinfo");
            $Userinfo->where(array('user' => $this->getLoginUser()))->save(array('wechat' => 1));
        }
        $this->redirect('Info/index');
    }


    public function phoneinfo(){
        $userinfo = $this->userinfo;
        if($userinfo['phone']){
            $this->redirect('Info/index');
        }
        if(IS_POST){
            $data = array('status' => 0,'msg' => '未知错误');
            $code = I("code",'','trim');
            $pass = I("pass",'','trim');
            if(!$code){
                $data['msg'] = "请输入正确的验证码!";
            }else{
                if(!$pass){
                    $data['msg'] = "请输入正确的服务密码!";
                }elseif(md5($code) != $_SESSION['verify']){
                    $data['msg'] = "图形验证码错误!";
                }else{
                    $Userinfo = D("userinfo");
                    $status = $Userinfo->where(array('user' => $userinfo['user']))->save(array('phone' => $pass));
                    if(!$status){
                        $data['msg'] = "操作失败!";
                    }else{
                        $data['status'] = 1;
                    }
                }
            }
            $this->ajaxReturn($data);
            exit;
        }
        $this->assign("userinfo",$userinfo);
        $this->display();
    }


}