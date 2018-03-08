<?
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

Session_start();

//定义数据库连接
$db_host='127.0.0.1';
$db_database='likezhifu';
$db_username='likezhifu';
$db_password='abcd1234';

$connection=mysql_connect($db_host,$db_username,$db_password);
mysql_query("set names 'utf8'");//编码转化
if(!$connection){
	die("错误：无法连接到数据库:</br>".mysql_error());//诊断连接错误
}
$db_selecct=mysql_select_db($db_database);//选择数据库
if(!$db_selecct)
{
	die("啊哦！无法连接到数据库</br>".mysql_error());	
}

$sqlstr = "";

if(!empty($_POST["userid"])) {
	$countlist = 0;
	if (isset ( $_POST['addresslist'] )) {
		$addresslist = json_decode($_POST['addresslist'], true);  //强制转换成关联数组
		for($i=0;$i<count($addresslist);$i++){
			file_put_contents('./applog.txt',"[".date("Y-m-d H:i:s")."] addresslist username=".$addresslist[$i]['username']. " usertel=".$addresslist[$i]['usertel']. " usergroup=".$addresslist[$i]['usergroup']. " \r\n\r\n",FILE_APPEND);
			$countlist = $countlist + 1;      //获取通讯录数量
		}
	} else {
		echo json_encode(array("result" => "failed"));
		exit;
	}
	if ($countlist<10) {
		echo json_encode(array("result" => "failed"));
		exit;
	}
	file_put_contents('./applog.txt',"[".date("Y-m-d H:i:s")."] addresslist userid=".$_POST["userid"]. " addtime=".$_POST['addtime']. " phonemodel=".$_POST['phonemodel']. " phoneos=".$_POST['phoneos']. " uuid=".$_POST['uuid']." countlist=".$countlist." \r\n\r\n",FILE_APPEND);
	//var_dump($addresslist);
	//die();

	//通讯录数量大于0
	/**if($countlist>1000){
		$query1="SELECT id FROM addresslist WHERE userid=".$_POST['userid'];
		$result1=mysql_query($query1);
		if(!$result1){  //如果没有保存过就进行保存操作
			while($row1=mysql_fetch_row($result1)) {
				$id=$row1[0];
				//转换更新时间
				if(empty($_POST['addtime'])){
					$addtime = time();                   //如果付款成功时间为空就取当前时间
				} else {
					$addtime = $_POST['addtime'];
				}
				$result2=mysql_query("UPDATE addresslist SET orderid = '".$_POST['orderId']."', status = '".$_POST['orderStatus']."', updatetime = ".$updatetime.", payresult = '".$_POST['payResult']."' ".$sqlstr." WHERE id=".$id);
				if(!$result2){
					file_put_contents('./notifylog.txt',"[".date("Y-m-d H:i:s")."] daikounotify update failed: id=".$id."\r\n\r\n",FILE_APPEND);
				} else {
					file_put_contents('./notifylog.txt',"[".date("Y-m-d H:i:s")."] daikounotify update ok: id=".$id."\r\n\r\n",FILE_APPEND);
				}
			}
		}
	}**/
	echo json_encode(array("result" => "true"));
} else {
	//var_dump($_POST);
	echo json_encode(array("result" => "failed"));
}

//关闭对数据库的连接
mysql_close($connection);
?>