<?php
require_once ("api_payment/llpay.config.php");
require_once ("api_payment/lib/llpay_apipost_submit.class.php");
require_once ("api_payment/lib/llpay_security.function.php");
class DaikuanAction extends CommonAction{
public $llpay_payment_url3 = 'https://instantpay.lianlianpay.com/paymentapi/payment.htm';


    //借款列表
	public function index(){
	    $yuqi = I('isYuqi',0,'int');
	    if($yuqi){
            $this->title = "预期列表";
            $this->yuqi = $yuqi;
            $where = array('status'=>2);
        }else{
            $this->title = "借款列表";
            $keyword = I("keyword",'','trim');
            $this->keyword = $keyword;
            $where = array();
            if($keyword){
                $where['ordernum'] = $keyword;
                // $where['ordernum|user|money|']=array('like',$keyword);
            }
        }

		$Order = D("order");
		import('ORG.Util.Page');
		$count = $Order->where($where)->count();
		$Page  = new Page($count,25);
		$Page->setConfig('theme','共%totalRow%条记录 | 第 %nowPage% / %totalPage% 页 %upPage%  %linkPage%  %downPage%');
		$show  = $Page->show();
		$list = $Order->where($where)->order('addtime Desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        if($yuqi){
            $i=0;
            foreach ($list as $k=>$v){
                $days = round((time()-$v['updateTime'])/ 86400)-(7*($v['donemonth'] + 1));
                if($days>0){
                    $newList[$i] = $v;
                    $newList[$i]['days']=$days;
                    $newList[$i]['time']=date('Y-m-d',strtotime(date("Y-m-d",strtotime("+".($v['donemonth'] + 1)." week",$v['updateTime']))));
                    $newList[$i]['yuqi']=round($v['days']*$v['monthmoney']*(C('cfg_yuqifuwufei')/100),2);
                }
                $i++;
            }
            $list = array_slice($newList,$Page->firstRow,$Page->listRows);
        }
		$this->list = $list;
		$this->page = $show;
		$this->display();
	}
    public function index1(){
        $yuqi = I('isYuqi',0,'int');
        if($yuqi){
            $this->title = "预期列表";
            $this->yuqi = $yuqi;
            $where = array('status'=>2);
        }else{
            $this->title = "借款列表";
            $keyword = I("keyword",'','trim');
            $this->keyword = $keyword;
            $where = array();
            if($keyword){
                $where['ordernum'] = $keyword;
                // $where['ordernum|user|money|']=array('like',$keyword);
            }
        }

        $Order = D("order");
        import('ORG.Util.Page');
        $count = $Order->where($where)->count();
        $Page  = new Page($count,25);
        $Page->setConfig('theme','共%totalRow%条记录 | 第 %nowPage% / %totalPage% 页 %upPage%  %linkPage%  %downPage%');
        $show  = $Page->show();
        $list = $Order->where($where)->order('addtime Desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        if($yuqi){
            $i=0;
            foreach ($list as $k=>$v){
                $days = round((time()-$v['updateTime'])/ 86400)-(7*($v['donemonth'] + 1));
                if($days>0){
                    $newList[$i] = $v;
                    $newList[$i]['days']=$days;
                    $newList[$i]['time']=date('Y-m-d',strtotime(date("Y-m-d",strtotime("+".($v['donemonth'] + 1)." week",$v['updateTime']))));
                    $newList[$i]['yuqi']=round($days*$v['monthmoney']*(C('cfg_yuqifuwufei')/100),2);
                }
                $i++;
            }
            $list = array_slice($newList,$Page->firstRow,$Page->listRows);
        }
        $this->list = $list;
        $this->page = $show;
        $this->display('index');
    }
	
	//修改订单状态
	public function changestatus(){
		$id = I("id",0,'trim');
		$status = I("status",'','trim');
		$data = array('status' => 0,'msg' => '未知错误');
		if(!$id || $status == ''){
			$data['msg'] = "参数错误!";
		}else{
			$Order = D("order");
			$count = $Order->where(array('id' => $id))->count();
			if(!$count){
				$data['msg'] = "订单不存在!";
			}else{
				$status = $Order->where(array('id' => $id))->save(array('status' => $status,'updateTime'=>time()));
				if(!$status){
					$data['msg'] = "操作失败!";
				}else{
					$data['status'] = 1;
				}
			}
		}
		$this->ajaxReturn($data);
	}

    public function pay($order){
        $out_order_no = $order['ordernum'];//商户网站订单系统中唯一订单号，必填
        $platform = 'wallet.likezhifu.cn';
        $api_version = '1.0';
        //对私标记
        $flag_card = '0';
        $parameter3 = array (
            "oid_partner" => trim('201710120001012537'),
            "sign_type" => trim('RSA'),
            "no_order" => time(),//$out_order_no,
            "dt_order" => date('YmdHis'),
            "money_order" => $order['money'],
            "acct_name" => $order['username'],
            "card_no" => $order['card_no'],
            "info_order" => '测试',
            "flag_card" => $flag_card,
            "notify_url" => $platform,
            "platform" => $platform,
            "api_version" => $api_version
        );
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201306081000001016
        $llpay_config['oid_partner'] = '201710120001012537';

//秘钥格式注意不能修改（左对齐，右边有回车符）  商户私钥，通过openssl工具生成,私钥需要商户自己生成替换，对应的公钥通过商户站上传
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

//连连银通公钥
        $llpay_config['LIANLIAN_PUBLICK_KEY'] ='-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSS/DiwdCf/aZsxxcacDnooGph3d2JOj5GXWi+
q3gznZauZjkNP8SKl3J2liP0O6rU/Y/29+IUe+GTMhMOFJuZm1htAtKiu5ekW0GlBMWxf4FPkYlQ
kPE0FtaoMP3gYfh+OwI+fIRrpW3ySn3mScnc6Z700nU/VYrRkfcSCbSnRwIDAQAB
-----END PUBLIC KEY-----';

//安全检验码，以数字和字母组成的字符
        $llpay_config['key'] = '201408071000001539_sahdisa_20141205';

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


//签名方式 不需修改
        $llpay_config['sign_type'] = strtoupper('RSA');


//字符编码格式 目前支持 gbk 或 utf-8
        $llpay_config['input_charset'] = strtolower('utf-8');
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
        $html_text = json_decode($html_text,true);
        return $html_text;
    }

    //修改订单状态
    public function fangkuan(){
        $id = I("id",0,'trim');
        $order_no = I("order_no");
        $user = I("user");
        //$status = I("status",'','trim');
        $data = array('status' => 0,'msg' => '未知错误');
        if(!$id){
            $data['msg'] = "参数错误!";
        }else{
            $Order = D("order");
            $count = $Order->where(array('id' => $id))->find();
            if(!$count){
                $data['msg'] = "订单不存在!";
            }else{
                $user = M('userinfo')->where(array('user' => $user))->find();
                $order['ordernum'] = time();
                $order['card_no'] = $count['banknum'];
                $order['money'] = $count['money'];
                $order['username'] = $user['name'];
                $arr = $this->pay($order);
                //var_dump($arr);exit;
                if($arr['ret_code']=='0000'){
                    //echo 1;exit;
                    $status = $Order->where(array('id' => $id))->save(array('status' => 2,'updateTime'=>time()));
                    if(!$status){
                        $data['msg'] = "操作失败1!";
                        //错误原因
                        $Order->where(array('id' => $id))->save(array('errcode' => $arr['ret_code'],'errmsg'=>$arr['ret_msg'],'updateTime'=>time()));

                    }else{
                        $data['status'] = 1;
                        $data['msg'] = "ok!";
                    }
                }else{
                    $data['msg'] = "error2!";
                }
            }
        }
        echo 1;exit;
    }
	
	//删除订单
	public function del(){
        $this->title='删除订单';
        $id = I('id',0,'trim');
        if(!$id){
            $this->error("参数有误!");
        }
        $Order = D("order");
        $status = $Order->where(array('id' => $id))->delete();
        if(!$status){
            $this->error("删除失败!");
        }
        $this->success("删除订单成功!");
	}
	
	
	
}
