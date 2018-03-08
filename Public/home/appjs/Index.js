var config={}
function getYuegong (str){
	// 重新获取money存储设置其他值
	var money = parseInt(str);
	var benjin=0;
	xianshi();
}
function xianshi () {
    $.each($(".timeBtn"), function(index, val) {
    	var spanval = $("#timeSpanVal_"+index).html();
    	if(spanval == definamonth){
    		$('#'+index+'m').addClass('act').siblings('.timeBtn').removeClass('act');
    	}
    });
}
function changeslider(isPlus) {  //添加.s-money或.s-time以区分是申请金额还是借款期限的改变 by mm
    if (isPlus) {
        //加
        scale = parseInt($('.s-money .jslider-pointer-to').css('left')) + STEP;
        scale = (scale >= 100) ? '100' : parseInt(scale);
        scale= scale+'%'
        $('.s-money .jslider div:nth-child(3)').css('left', scale);
        $('#tap1 i:nth-child(3)').css('width', scale);
        SliderSingle1.slider('value', MINMONEY, (parseInt(nowmoney.money) + 100))
        reset();
    } else {
        //减
        scale = parseInt($('.s-money .jslider-pointer-to').css('left')) - STEP;
        scale = (scale <= 0) ? '0' : parseInt(scale);
        scale= scale+'%'
        $('.s-money .jslider div:nth-child(3)').css('left', scale);
        $('#tap1 i:nth-child(3)').css('width', scale);
        SliderSingle1.slider('value', MINMONEY, (parseInt(nowmoney.money) - 100))
        reset()
    }
}
function changeTimeSlider(isPlus) {
    if (isPlus) {
        //加
        scale = parseInt($('.s-time .jslider-pointer-to').css('left')) + STEP;
        scale = (scale >= 100) ? '100' : parseInt(scale);
        scale= scale+'%'
        $('.s-time .jslider div:nth-child(3)').css('left', scale);
        $('#tap2 i:nth-child(3)').css('width', scale);
        SliderSingle2.slider('value', MINTIME, (parseInt(nowmoney.days) + 1))
        reset();
    } else {
        //减
        scale = parseInt($('.s-time .jslider-pointer-to').css('left')) - STEP;
        scale = (scale <= 0) ? '0' : parseInt(scale);
        scale= scale+'%'
        $('.s-time .jslider div:nth-child(3)').css('left', scale);
        $('#tap2 i:nth-child(3)').css('width', scale);
        SliderSingle2.slider('value', MINTIME, (parseInt(nowmoney.days) - 1))
        reset()
    }
}
function reset() {
	//借款金额  无需保留两位小数
	var money = $('#money_str').html();
	money = new Number(money).toFixed(0);
	//借款期限 这里显示的是月数 实际一周就是1  两周就是2
	///var month = parseInt($('.timeBtn.act span').html());

    //借款期限  以天计//by mm
    var days = parseInt($('#time_str').html()); // by mm end

	//借款服务费=借款金额乘以后台设置里的服务费率
	var jkfuwufei = money * fuwufei;
	jkfuwufei = new Number(jkfuwufei).toFixed(2);
	//审核费=借款金额乘以后台设置里的审核费率
	var jkshenhefei = money * shenhefei;
	jkshenhefei = new Number(jkshenhefei).toFixed(2);
	//利息总计=后台设置里的日息（0.008即千分之八）乘以借款金额再乘以借款期限 因为是按周，所以还要乘以7天
	// var jklixi = rixi * money * month * 7;//注释掉 by lmm
    //jklixi 以天计//by lmm
    var jklixi = rixi * money * days;//by lmm end

	jklixi = new Number(jklixi).toFixed(2);
	$("#jklixi").html(jklixi);
	//服务费总计=借款服务费+审核费
	var allfuwufei = parseFloat(jkfuwufei) + parseFloat(jkshenhefei);
	//allfuwufei = allfuwufei.toFixed(2);
	$("#allfuwufei").html(allfuwufei);

	//总计还款=借款金额+借款服务费+审核费+利息总计
	var totalfee = parseFloat(money) + parseFloat(allfuwufei) + parseFloat(jklixi);
	totalfee = new Number(totalfee).toFixed(2);
	$("#total").html(totalfee);

	//实际放款金额 = 借款金额-审核费-借款服务费-利息总计
	var fee = parseFloat(money-allfuwufei-jklixi);
    //fee = new Number(fee).toFixed(2);
	//把实际放款金额显示在前台页面jkfee的地方
    $('#jkfee').html(fee);

	//本金=借款金额
	var benjin = money;
	benjin = new Number(benjin).toFixed(2);

	//所谓的月供=本金=借款金额
	var yuegong = new Number(benjin);
	yuegong = new Number(yuegong).toFixed(2);

    nowmoney['money'] = money;
    $("#order_money").val(money);
    //nowmoney['month'] = days;
    //$("#order_month").val(days);
    nowmoney['allfuwufei'] = allfuwufei;
    nowmoney['benjin'] = benjin;
    nowmoney['yuegong'] = yuegong;
    nowmoney['total'] = totalfee;
	nowmoney['days'] = days;
	$("#order_days").val(days);
}
$(function () {
        SliderSingle1.slider({
            from: MINMONEY,
            to: MAXMONEY,
            step: 100,
            round: 0,
            dimension: '',
            skin: "round",
            onstatechange: function (a) {
                // console.log(a);
                var t = a.split(';');
                t[1] = parseInt(t[1]).toFixed(2);
                $('#money_str').html(t[1]);
                getYuegong(t[1]);
                reset();
            },
            callback: function (a) {
                if (num % 2 == 0) {
                    $(".s-money .jslider-pointer").css({
                        animation: 'myfirst .5s',
                        '-webkit-animation': 'myfirst .5s'
                    });
                } else {
                    $(".s-money .jslider-pointer").css({
                        animation: 'mysecond .5s',
                        '-webkit-animation': 'mysecond .5s'
                    })
                }
                num++
            }
        });
        var flag = null;
        //减按钮
        $('#subtract').click(function () {
            if (!flag) {
                if (parseInt(nowmoney.money) > MINMONEY) {
                    changeslider();
                } else {
                    $(".subtractmore").fadeIn('slow')
                    flag = setTimeout(showTime, 2000);
                    function showTime() {
                        $(".subtractmore").fadeOut('slow')
                        flag = null;
                    }
                }
            }
        });
        //加按钮
        $('#plus').click(function () {
            if (!flag) {
                if (parseInt(nowmoney.money) < MAXMONEY) {
                    changeslider(true);
                } else {
                    $(".plusmore").fadeIn('slow')
                    flag = setTimeout(showTime, 2000);
                    function showTime() {
                        $(".plusmore").fadeOut('slow')
                        flag = null;
                    }
                }
            }

        });
        //随机生成 jslider-pointer 样式
        function pointer() {
            var random = Math.ceil(Math.random() * 3);
            switch (random) {
                case 1:
                    $(".jslider-pointer").css('background-image', "url("+PublicUrl+"/home/imgs/coin.png)");
                    break;
                case 2:
                    $(".jslider-pointer").css('background-image', "url("+PublicUrl+"/home/imgs/coin.png)");
                    break;
            }
        }
        pointer();

    	middle33();
	    function middle33(){
	        var h = $('#deowin33').height();
	        var t = -h/2 + "px";
	        $('#deowin33').css('marginTop',t);
	    }
    	$('#winbtn4').click(function(){
        	$('#deowin4').hide();
        	$('#mask3').hide();
        	$('#deowin4 iframe').attr('src',''); 
      	});
    	middle1();
    	function middle1(){
        	var h = $('#deowin4').height();
        	var t = -h/2 + "px";
        	$('#deowin4').css('marginTop',t);
    	}
    	$('#winbtn5').click(function(){
        	$('#deowin5').hide();
        	$('#mask3').hide();
        	$('#deowin5 iframe').attr('src',''); 
    	});
    	middle2();
    	function middle2(){
        	var h = $('#deowin5').height();
        	var t = -h/2 + "px";
        	$('#deowin5').css('marginTop',t);
    	}
    	//提示关闭
        $("#winbtn3").click(function() {
            $('#deowin31').hide();
            $('#mask3').hide();
        });
        middle();
        function middle() {
            var w = $('#deowin3').width();
            var h = w / 0.64;
            $('.deocon11').css('height', h);
            var t = -h / 2 + "px";
            $('#deowin3').css('marginTop', t);
        }


        // 时间slider by lmm
        SliderSingle2.slider({
            from: MINTIME,
            to: MAXTIME,
            step: 1,
            round: 0,
            dimension: '',
            skin: "round",
            onstatechange: function (a) {
                //console.log(a);
                var t = a.split(';');
                t[1] = parseInt(t[1]);
                $('#time_str').html(t[1]);
                reset();
            },
            callback: function (a) {
                if (num2 % 2 == 0) {
                    $(".s-time .jslider-pointer").css({
                        animation: 'myfirst .5s',
                        '-webkit-animation': 'myfirst .5s'
                    });
                } else {
                    $(".s-time .jslider-pointer").css({
                        animation: 'mysecond .5s',
                        '-webkit-animation': 'mysecond .5s'
                    })
                }
                num2++
            }
        });
        var timeFlag = null;
        //加时间按钮
        $('#plusTime').click(function () {
            if (!timeFlag) {
                if (parseInt(nowmoney.days) < MAXTIME) {
                    changeTimeSlider(true);
                } else {
                    $(".plusmore").fadeIn('slow')
                    timeFlag = setTimeout(showTime, 2000);
                    function showTime() {
                        $(".plusmore").fadeOut('slow')
                        timeFlag = null;
                    }
                }
            }

        });
        //减时间按钮
        $('#subtractTime').click(function () {
            if (!timeFlag) {
                if (parseInt(nowmoney.days) > MINTIME) {
                    changeTimeSlider();
                } else {
                    $(".subtractmore").fadeIn('slow')
                    timeFlag = setTimeout(showTime, 2000);
                    function showTime() {
                        $(".subtractmore").fadeOut('slow')
                        timeFlag = null;
                    }
                }
            }
        });
});