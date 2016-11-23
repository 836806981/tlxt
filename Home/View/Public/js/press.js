var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var order_name = $('#order_name').val();
var nurse_name = $('#nurse_name').val();
var press_status = $('#press_status').val();
var belong = $('#belong').val();
var student_name = $('#student_name').val();


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
             order_name = $('#order_name').val();
             nurse_name = $('#nurse_name').val();
             press_status = $('#press_status').val();
            student_name = $('#student_name').val();
            reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;
            var reg_id_card = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;

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
        $.AjaxPost(MODULE+"/Finance/getFinanceList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,order_name:order_name,nurse_name:nurse_name,press_status:press_status,belong:belong,student_name:student_name}, function (backdata) {
            if (backdata.code == 1000) {
                List_zcj = backdata.data.list;
                Num_zcj = backdata.data.num;
                $.ProductPageShow(Num_zcj,pagenum_zcj,"b");
                $.CurrentPageShow(currentpage_zcj);
                $('#show_number_str').text(Num_zcj);
				var str='';
				var $dom=$(".last_tr");
                var del_str = '';
                $(".data").remove();

				if(List_zcj!=null){
                    if(belong == 1){
                        $.each(List_zcj,function(i,item){
                            var b_time = item.true_b_time? item.true_b_time+'(<span title="实际上户">实</span>)':item.b_time+'(<span title="预计上户">预</span>)';
                            var s_time = item.true_s_time? item.true_s_time+'(<span title="实际下户">实</span>)':item.s_time+'(<span title="预计下户">预</span>)';
                            str +='<tr class="data">\
                            <td>'+item.name+'</td>\
                            <td>'+item.nurse_name+'</td>\
                            <td>'+item.contract_money+'</td>\
                            <td>'+b_time+'</td>\
                            <td>'+s_time+'</td>\
                            <td>'+item.press_status_name+'</td>\
                            <td>'+item.get_money+'</td>\
                            <td><a class="add_record" order_id="'+item.id+'">新增催款记录</a><a href="'+MODULE+'/Finance/info/id/'+item.id+'.html">查看</a></td>\
                            </tr>';
                        });
                    }else  if(belong == 2){
                        $.each(List_zcj,function(i,item){
                            var b_time = item.true_b_time? item.true_b_time+'(<span title="实际上户">实</span>)':item.b_time+'(<span title="预计上户">预</span>)';
                            var s_time = item.true_s_time? item.true_s_time+'(<span title="实际下户">实</span>)':item.s_time+'(<span title="预计下户">预</span>)';
                            str +='<tr class="data">\
                            <td>'+item.name+'</td>\
                            <td>'+item.nurse_name+'</td>\
                            <td>'+item.contract_money+'</td>\
                            <td>'+b_time+'</td>\
                            <td>'+s_time+'</td>\
                            <td>'+item.press_status_name+'</td>\
                            <td>'+item.get_money+'</td>\
                            <td><a class="add_get" order_id="'+item.id+'" order_name="'+item.name+'">新增收款记录</a><a href="'+MODULE+'/Finance/info/id/'+item.id+'.html">查看</a></td>\
                            </tr>';
                        });
                    }else  if(belong == 3){
                        $.each(List_zcj,function(i,item){
                            var b_time = item.true_b_time? item.true_b_time+'(<span title="实际上户">实</span>)':item.b_time+'(<span title="预计上户">预</span>)';
                            var s_time = item.true_s_time? item.true_s_time+'(<span title="实际下户">实</span>)':item.s_time+'(<span title="预计下户">预</span>)';
                            var  through = '';
                            if(item.is_through == 2 && item.pay_status ==0){
                                through=  '<a class="pay_status" id="'+item.id+'" order_name="'+item.order_name+'" nurse_name="'+item.nurse_name+'" nurse_pay_true="'+item.nurse_pay_true+'" remark="'+item.remark+'" bank_card="'+item.bank_card+'">已发</a>';
                            }
                            str +='<tr class="data">\
                            <td>'+item.order_name+'</td>\
                            <td>'+item.nurse_name+'</td>\
                            <td>'+item.contract_money+'</td>\
                            <td>'+b_time+'</td>\
                            <td>'+s_time+'</td>\
                            <td>'+item.do_time+'</td>\
                            <td>'+item.nurse_pay+'</td>\
                            <td>'+item.nurse_pay_true+'</td>\
                            <td>'+item.is_through_name+'</td>\
                            <td>'+item.pay_status_name+'</td>\
                            <td>'+through+'<a href="'+MODULE+'/Finance/info/id/'+item.order_id+'.html">查看</a></td>\
                            </tr>';
                        });
                    }else  if(belong == 4){
                        $.each(List_zcj,function(i,item){
                            var b_time = item.true_b_time? item.true_b_time+'(<span title="实际上户">实</span>)':item.b_time+'(<span title="预计上户">预</span>)';
                            var s_time = item.true_s_time? item.true_s_time+'(<span title="实际下户">实</span>)':item.s_time+'(<span title="预计下户">预</span>)';
                            var  through = '';
                            if(item.is_through ==1){
                                through=  '<a class="do_through" id="'+item.id+'" order_name="'+item.order_name+'" nurse_name="'+item.nurse_name+'" nurse_pay_true="'+item.nurse_pay_true+'" remark="'+item.remark+'" do_time="'+item.do_time+'" nurse_pay="'+item.nurse_pay+'">审核</a>';
                            }
                            str +='<tr class="data">\
                            <td>'+item.order_name+'</td>\
                            <td>'+item.nurse_name+'</td>\
                            <td>'+item.contract_money+'</td>\
                            <td>'+b_time+'</td>\
                            <td>'+s_time+'</td>\
                            <td>'+item.do_time+'</td>\
                            <td>'+item.nurse_pay+'</td>\
                            <td>'+item.nurse_pay_true+'</td>\
                            <td>'+item.is_through_name+'</td>\
                            <td>'+item.pay_status_name+'</td>\
                            <td>'+through+'<a href="'+MODULE+'/Finance/info/id/'+item.order_id+'.html">查看</a></td>\
                            </tr>';
                        });
                    }else  if(belong == 5){
                        $.each(List_zcj,function(i,item){
                            var  through = '';
                            if(item.is_through == 1 && item.pay_status ==0){
                                through=  '<a class="student_pay_status" id="'+item.id+'" student_name="'+item.student_name+'" student_pay_true="'+item.student_pay_true+'" do_time="'+item.do_time+'" student_pay="'+item.student_pay+'" true_b_time="'+item.true_b_time+'"  true_s_time="'+item.true_s_time+'" bank_card="'+item.bank_card+'">已发</a>';
                            }
                            str +='<tr class="data">\
                            <td>'+item.student_name+'</td>\
                            <td>'+item.true_b_time+'</td>\
                            <td>'+item.true_s_time+'</td>\
                            <td>'+item.do_time+'</td>\
                            <td>'+item.student_pay+'</td>\
                            <td>'+item.student_pay_true+'</td>\
                            <td>'+item.is_through_name+'</td>\
                            <td>'+item.pay_status_name+'</td>\
                            <td>'+through+'<a href="'+MODULE+'/Student/studentInfo/id/'+item.student_id+'.html">查看</a></td>\
                            </tr>';
                        });
                    }else  if(belong == 6){
                        $.each(List_zcj,function(i,item){
                            var  through = '';
                            if(item.is_through ==0){
                                through=  '<a class="student_do_through" id="'+item.id+'" student_name="'+item.student_name+'" student_pay_true="'+item.student_pay_true+'" do_time="'+item.do_time+'" student_pay="'+item.student_pay+'" true_b_time="'+item.true_b_time+'"  true_s_time="'+item.true_s_time+'">审核</a>';
                            }
                            str +='<tr class="data">\
                            <td>'+item.student_name+'</td>\
                            <td>'+item.true_b_time+'</td>\
                            <td>'+item.true_s_time+'</td>\
                            <td>'+item.do_time+'</td>\
                            <td>'+item.student_pay+'</td>\
                            <td>'+item.student_pay_true+'</td>\
                            <td>'+item.is_through_name+'</td>\
                            <td>'+item.pay_status_name+'</td>\
                            <td>'+through+'<a href="'+MODULE+'/Student/studentInfo/id/'+item.student_id+'.html">查看</a></td>\
                            </tr>';
                        });
                    }

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