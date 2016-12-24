var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var order_number = $('#order_number').val();
var nurse_number = $('#nurse_number').val();
var nurse_name = $('#nurse_name').val();
var is_statement = $('#is_statement').val();
var belong = $('#belong').val();


$(function () {
        $.regEvent();
        $.initinfo();
    
});


$.extend({
    regEvent: function () {
        //页码点击效果
        $(".pagenum").live("click", function () {
            if($(this).attr('read')=='readonly'){
                return false;
            }else{
                currentpage_zcj = $(this).text();
                $.getNewsListByAllTerm();
            }
        });
        //上一页
        $(".prev_page").live("click", function () {
            var current = $("#pagenum").find("a.current").text();
            if (current != 1) {
                currentpage_zcj = parseInt(current) - 1;
				$.getNewsListByAllTerm();
            }
        });
        //下一页
        $(".next_page").live("click", function () {
            var current = $("#pagenum").find("a.current").text();
            var pages = $("#pagenum").find("a.pagenum");
            if (current != $(pages).length) {
                currentpage_zcj = parseInt(current) + 1;
                $.getNewsListByAllTerm();
            }
        });


        //页码点击效果
        $("#priority").live("change", function () {
            priority = $("#priority").val();
            currentpage_zcj = 1;
            $.getNewsListByAllTerm();
        });

        //搜索
        $("#search").live("click", function () {
            order_number = $('#order_number').val();
            nurse_number = $('#nurse_number').val();
            nurse_name = $('#nurse_name').val();
            is_statement = $('#is_statement').val();
            belong = $('#belong').val();
            //reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;
            //if(!reg.test(status_6) && status_6){
            //    layer.msg('请输入正确的签单时间 格式：2016-01-01！');
            //    return false;
            //}
            currentpage_zcj = 1;
            $.getNewsListByAllTerm();
        });
    },
    initinfo: function () {
        //根据页码获取数据
        $.getNewsListByAllTerm();
		
    },
	//根据所有条件查询新闻列表
    getNewsListByAllTerm: function () {
        //异步提交数据,参数：currentpage,要查询的页码;pagenum，每页的条数
        $.AjaxPost(MODULE+"/Money/getStatementList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,order_number:order_number,nurse_number:nurse_number,nurse_name:nurse_name,is_statement:is_statement,belong:belong}, function (backdata) {
            if (backdata.code == 1000) {
                List_zcj = backdata.data.list;
                Num_zcj = backdata.data.num;
                $.ProductPageShow(Num_zcj,pagenum_zcj,"b");
                $.CurrentPageShow(currentpage_zcj);
                $('#show_number_str').text(Num_zcj);
				var str='';
				var $dom=$(".last_tr");
                $(".data").remove();
                var back = '';
                if(List_zcj!=null){
                    $.each(List_zcj,function(i,item){
                        //未接单或者打回才能放回收站
                        var price_add = '';
                        if (item.price_add == 1) {
                            price_add = '+' + item.add_nurse_price;
                        }

                        var reward_price = '';
                        if (item.reward_price!=0) {
                            reward_price = '+' + item.reward_price;
                        }
                        var do_str = ' nurse_name="' + item.nurse_name + '" order_number="' + item.order_number + '" nurse_number="' + item.nurse_number + '" product_price="' + item.product_price + '" nurse_pay_do="' + item.nurse_pay_do + '" service_day="' + item.service_day + '"' +
                            ' true_b_time="' + item.true_b_time + '" true_s_time="' + item.true_s_time + '" status_name="' + item.status_name + '" price_add="' + price_add + '" reward_price="'+reward_price+'"  order_nurse_id="' + item.id + '" ';

                        if(belong==1) {
                           var do_statement = '';


                            if (item.is_statement == 1) {
                                do_statement = '<a class="do_statement" ' + do_str + '>待结算</a>';
                            } else if (item.is_statement == 2) {
                                do_statement = '待审核';
                            } else if (item.is_statement == 3) {
                                do_statement = '已结算';
                            }

                            str += '<tr class="data">\
                        <td width="8%">' + item.order_number + '</td>\
                        <td width="8%">' + item.product + '</td>\
                        <td width="8%">' + item.product_price + '</td>\
                        <td width="8%">' + item.nurse_pay_do + price_add+reward_price + '</td>\
                        <td width="8%">' + item.nurse_number + '</td>\
                        <td width="8%">' + item.nurse_name + '</td>\
                        <td width="8%">' + item.come + '</td>\
                        <td width="8%">' + item.level_name + '</td>\
                        <td width="15%">' + do_statement+ '</td>\
                        </tr>';
                        }else if(belong==2){
                            var finance = '';
                            if(item.is_through==0){
                                finance = '<a class="finance" '+do_str+'>审核通过</a>'
                            }else  if(item.is_through==1){
                                finance = '待复核';
                            }else if(item.is_through==2){
                                finance = '<a class="finance_re" '+do_str+' title="'+item.no_through_reason+'">重新审核</a>'
                            }
                            str += '<tr class="data">\
                            <td width="8%">' + item.order_number + '</td>\
                            <td width="8%">' + item.product + '</td>\
                            <td width="8%">' + item.product_price + '</td>\
                            <td width="8%">' + item.nurse_pay_do + price_add+reward_price + '</td>\
                            <td width="8%">' + item.nurse_number + '</td>\
                            <td width="8%">' + item.nurse_name + '</td>\
                            <td width="8%">' + item.come + '</td>\
                            <td width="8%">' + item.level_name + '</td>\
                            <td width="15%">'+ finance + '</td>\
                        </tr>';

                        }else if(belong==3){
                            var boss = '<a class="boss3" '+do_str+'>审核通过</a> <a class="boss2" '+do_str+'>审核不通过</a>';
                            str += '<tr class="data">\
                                <td width="8%">' + item.order_number + '</td>\
                                <td width="8%">' + item.product + '</td>\
                                <td width="8%">' + item.product_price + '</td>\
                                <td width="8%">' + item.nurse_pay_do + price_add+reward_price + '</td>\
                                <td width="8%">' + item.nurse_number + '</td>\
                                <td width="8%">' + item.nurse_name + '</td>\
                                <td width="8%">' + item.come + '</td>\
                                <td width="8%">' + item.level_name + '</td>\
                                <td width="15%">'+ boss + '</td>\
                            </tr>';

                        }else if(belong==4){
                            if(item.pay_status==1){
                                var give = '已发：'+item.nurse_pay_true+'';
                            }else{
                                var give = '<a class="give" '+do_str+'>发放工资</a> ';
                            }

                            str += '<tr class="data">\
                                <td width="8%">' + item.order_number + '</td>\
                                <td width="8%">' + item.product + '</td>\
                                <td width="8%">' + item.product_price + '</td>\
                                <td width="8%">' + item.nurse_pay_do + price_add+reward_price + '</td>\
                                <td width="8%">' + item.nurse_number + '</td>\
                                <td width="8%">' + item.nurse_name + '</td>\
                                <td width="8%">' + item.come + '</td>\
                                <td width="8%">' + item.level_name + '</td>\
                                <td width="15%">'+ give + '</td>\
                            </tr>';

                        }
                    });
                    $dom.before(str);
                    $(".data").attr('onmouseover',"this.style.backgroundColor='#eeeeee';");
                    $(".data").attr('onmouseout',"this.style.backgroundColor='#ffffff';");

				}else{
                    str ='<div style="color: #c3c3c3; width: 80px; display: block; margin: 50px auto;" id="zanwushuju">暂无数据</div>';
                    $dom.before(str);
                }
            } else {

            }
        }, true);
    }
    
});