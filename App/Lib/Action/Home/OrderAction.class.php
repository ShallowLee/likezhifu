<?php
class OrderAction extends CommonAction{
	
	public function checkorder(){
		$data = array('status' => 0,'msg' => '未知错误!');
		if(!$this->getLoginUser()){
			$data['status'] = 1;
		}else{
			$day = $this->yesdaikuan($this->getLoginUser());
			if(!$day){
				$data['status'] = 1;
			}else{
                $data['status'] = 1;
				// $data['msg'] = "您最近一次订单审核失败,请".$day."天后再次提交!";
			}
		}
		$this->ajaxReturn($data);
	}
	
	//返回拒绝提交等待天数
	private function yesdaikuan($user){
		//先查找最近一次失败订单
		$Order = D("order");
		$info = $Order->where(array('user' => $this->getLoginUser()))->order("addtime Desc")->find();
		if(!$info){
			return 0;
		}
		if($info['status'] != '-1'){
			return 0;
		}
		$tmptime = $info['addtime'];
		$tmptime = time()-$tmptime;
		$tmptime = $tmptime/(24*60*60);
		//如果未拒绝提交等待天数 则默认为等待30天
		$disdkdleyday = C("cfg_disdkdleyday");
		if(!$disdkdleyday) $disdkdleyday = 30;
		if($tmptime > $disdkdleyday){
			return 0;
		}
		return ceil($disdkdleyday-$tmptime);
	}
	
	//贷款主方法
	public function daikuan(){
		//判断用户是否登录以及关键信息是否完整
		if(!$this->getLoginUser()){
			$this->redirect('User/login');
		}
		$Userinfo = D("userinfo");
		$info = $Userinfo->where(array('user' => $this->getLoginUser()))->find();
		if(!$info){
			$this->redirect('Info/index');
		}
		if($info['personname_1']==''){
			$this->redirect('Info/index');
		}
		if($info['bankcard']==''){
			$this->redirect('Info/index');
		}
		//判断用户最近一次失败订单是否超过逾期时间
		// $yesdaikuan = $this->yesdaikuan($this->getLoginUser());
		// if($yesdaikuan){
		// 	$this->redirect('Index/index');
		// }
		$money = I("money",0,'trim');
		$money = (float)$money;
		$month = I("month",0,'trim');      //借款天数  这里沿用之前的month变量 便于将来做成按月分期
		$month = (int)$month;
		$dismonths = C("cfg_dkmonths");   //允许选择的期限  这里暂时无用但保留
		$dismonths = explode(",",$dismonths);
		if($money > C('cfg_maxmoney') || $money < C('cfg_minmoney')){
			//借款金额不允许
			$this->redirect('Index/index');
		}
		//if(!in_array($month,$dismonths)){   //不允许的借款期限
		//	$this->redirect('Index/index');
		//}

		//$fuwufei = explode(",",$fuwufei);
		//日息=后台服务费率设置里逗号的第[借款时间-1]项 除以30天 这里因为按周结算就改成除以7
		//$rixi = round($fuwufei[$month-1] / 30,2);
		//总的日息=后台服务费率设置里逗号的第[借款时间-1]项 乘以 借款金额 除以100 
		//$fuwufei = $fuwufei[$month-1] * $money / 100;

		//以上屏蔽的都是老算法 全部作废
		$fuwufei = round($money * C('cfg_fuwufei'),2);
		$shenhefei = round($money * C('cfg_shenhefei'),2);
		$rixi = $money * C('cfg_rixi') * $month;
		$lixi = round($rixi,2);

		$order = array(
			'money'   => $money,
			'fee'     => (float)($money-$fuwufei-$shenhefei-$lixi), //实际放款金额
			'month'   => $month,                                    //借款天数  这里沿用之前的month变量 便于将来做成按月分期
			'huankuan'=> (float)($money+$fuwufei+$shenhefei+$lixi), //还款金额
			'bank'	  => $info['bankname'],
			'banknum' => $info['bankcard'],
			'lixi'	  => $lixi
		);

		//提交获取支付订单号
		$addorder = I("get.trueorder",0,'trim');
		if($addorder){
			$data = array('status' => 0,'msg' => '未知错误','payurl' => '');
			//创建订单 生成订单号
			$ordernum = neworderNum();
			//插入服务费和审核费的交易记录
			$arr = array(
				'ordernum' => $ordernum,
				'type'	   => '各种服务费',
				'money'	   => $shenhefei+$fuwufei,
				'addtime'  => time(),
				'status'   => 0,
				'user'	   => $this->getLoginUser(),
				'remark'   => "服务费:".$fuwufei."+审核费:".$shenhefei
			);
			$Payorder = D("payorder");
			$pid = $Payorder->add($arr);   //插入数据表成功后应会返回该条记录的id
			if(!$pid){
				$data['msg'] = '创建订单失败!';
			}else{
				//插入订单表
				$Order = D("order");
				$arr = array(
					'user' => $this->getLoginUser(),
					'money' => $money,
					'huankuan' => (float)$order['huankuan'],
					'months' => (int)$order['month'],
					'monthmoney' => (float)$order['huankuan']/(int)$order['month'],    //每期还款额
					'donemonth' => 0,
					'addtime' => time(),
					'status' => 1,                      //订单状态:正在审核
					'pid' => $pid,
					'bank' => $info['bankname'],
					'banknum' => $info['bankcard'],
					'ordernum' => $ordernum             //订单号
				);
				$pid = $Order->add($arr);   //插入数据表成功后应会返回该条记录的id
				if(!$pid){
					$data['msg'] = '创建订单失败!';
				}else{
					$data['status'] = 1;
                    $data['msg'] = "订单提交成功";
					$data['payurl'] = U('Order/lists');
				}
			}
			$this->ajaxReturn($data);
			exit;
		}else{
			$this->order = $order;
			$this->display();
		}
	}
	
	//获取订单列表
	public function lists(){
		$Order = D("order");
		$user = $this->getLoginUser();
		if(!$user){
			$this->redirect('User/login');
		}
		$this->data = $Order->where(array('user' => $user))->order("addtime Desc")->select();
		$this->display();
	}
	
	//获取订单详情
	public function info(){
		$oid = I("oid",0,"trim");
		if(!$oid){
			$this->redirect('Order/lists');
		}
		$user = $this->getLoginUser();
		if(!$user){
			$this->redirect('User/login');
		}
		$Order = D("order");
		$order = $Order->where(array('id' => $oid,'user' => $user))->find();
		if(!$order){
			$this->redirect('Order/lists');
		}
		$this->data = $order;
		$this->yuqirx = C('cfg_yuqirixi')*100;       //显示逾期日息
		$this->yuqifwf = C('cfg_yuqifuwufei');     //显示逾期服务费
		$this->display();
	}
	
	//我的还款
	public function bills(){
		$user = $this->getLoginUser();
		if(!$user){
			$this->redirect('User/login');
		}
		$hkr = C("cfg_huankuanri");
		if(!$hkr) $hkr = 10;           //如果没有设置还款日，就默认每月10号还款，暂时无用的参数
		$data = array();

		//遍历已借款订单
		$Order = D("order");
        $tmp = $Order->where(array('user' => $user,'status' => 2))->select();
		for($i=0;$i<count($tmp);$i++){
			//判断是否已还清
			if($tmp[$i]['months'] > $tmp[$i]['donemonth']){
				$tmp_ordernum = $tmp[$i]['ordernum'];
				//从还款记录查找本期/本月是否已还款
				$Bills = D("bills");
                $data[] = array(
                    'ordernum' => $tmp_ordernum,
                    'money'    => $tmp[$i]['monthmoney'],
                    'days'	   => round((time()-$tmp[$i]['updateTime'])/ 86400)-(7*($tmp[$i]['donemonth'] + 1)),  //判断逾期天数
                    'qishu'	   => $tmp[$i]['donemonth'] + 1,
                    'time'     => date('Y-m-d',strtotime(date("Y-m-d",strtotime("+".($tmp[$i]['donemonth'] + 1)." week",$tmp[$i]['updateTime']))))
                );
                if($data[$i]['days']>0){
                    //计算逾期金额
                    $data[$i]['yuqimouney'] =  round($data[$i]['days']*$tmp[$i]['monthmoney']*C('cfg_yuqirixi'),2) + C('cfg_yuqifuwufei');
                }
			}
		}
		$this->data = $data;
		$this->display();
	}

	//还款
	public function billinfo(){
		$user = $this->getLoginUser();
		if(!$user){
			$this->redirect('User/login');
		}
		$ordernum = I("ordernum",'','trim');
		if(!$ordernum){
			$this->redirect('Order/bills');
		}
		$Order = D("order");
		$order = $Order->where(array('ordernum' => $ordernum,'user' => $user))->find();
		//判断是否已还完
        //var_dump($order);exit;
		if($order['months'] == $order['donemonth']){
			$this->redirect('Order/bills');
		}
		//判断是否逾期
        $days = round((time()-$order['updateTime'])/ 86400)-(7*($order['donemonth'] + 1));
        if($days>0){
            $order['monthmoney'] = $order['monthmoney'] + round($days*$order['monthmoney']*C('cfg_yuqirixi'),2) + C('cfg_yuqifuwufei');
        }

		//创建支付订单
		$payordernum = neworderNum();
		$arr = array(
			'ordernum' => $payordernum,
			'user'     => $user,
			'type'	   => "还款",
			'money'	   => $order['monthmoney'],
			'addtime'  => time(),
			'status'   => 0,
			'jkorder'  => $ordernum
		);
		$Payorder = D("payorder");
		$status = $Payorder->add($arr);
		if(!$status){
			$this->redirect('Order/bills');
		}
		//跳转支付
		$this->redirect('Pay/Index/index',array('ordernum' => $payordernum));
	}
	
}
