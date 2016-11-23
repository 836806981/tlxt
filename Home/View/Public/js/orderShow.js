var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var name = $('#name').val();
var employee_id = $('#employee_id').val();
var other = $('#other').val();
var phone = $('#phone').val();
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
             name = $('#name').val();
             employee_id = $('#employee_id').val();
             other = $('#other').val();
             phone = $('#phone').val();
             reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;
            //if(!reg.test(add_time) && add_time){
            //    layer.msg('请输入正确派单时间 格式：2016-01-01！');
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
        $.AjaxPost(MODULE+"/OrderShow/getOrderList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,name:name,employee_id:employee_id,other:other,phone:phone,belong:belong}, function (backdata) {
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
                    $.each(List_zcj,function(i,item){
                    var b_time = item.true_b_time? item.true_b_time+'(<span title="实际上户">实</span>)':item.b_time+'(<span title="预计上户">预</span>)'
                    var s_time = item.true_s_time? item.true_s_time+'(<span title="实际下户">实</span>)':item.s_time+'(<span title="预计下户">预</span>)'
                        if(belong==1){
                            str +='<tr class="data">\
                                <td>'+item.name+'</td>\
                                <td>'+item.other+'</td>\
                                <td>'+item.phone+'</td>\
                                <td>'+item.nurse_name+'</td>\
                                <td>'+item.employee_name+'</td>\
                                <td>'+b_time+'</td>\
                                <td>'+s_time+'</td>\
                                <td>'+item.status_name+'</td>\
                                <td>'+item.contract_money+'</td>\
                                <td>'+item.nurse_pay+'</td>\
                                <td><a href="'+MODULE+'/OrderShow/OrderInfo/id/'+item.id+'.html">查看</a></td>\
                                </tr>';
                        }else if(belong==2){
                            str +='<tr class="data">\
                                <td>'+item.name+'</td>\
                                <td>'+item.other+'</td>\
                                <td>'+item.phone+'</td>\
                                <td>'+item.nurse_name+'</td>\
                                <td>'+item.employee_name+'</td>\
                                <td>'+b_time+'</td>\
                                <td>'+s_time+'</td>\
                                <td>'+item.contract_money+'</td>\
                                <td>'+item.nurse_pay+'</td>\
                                <td><a href="'+MODULE+'/OrderShow/OrderInfo/id/'+item.id+'.html">查看</a></td>\
                                </tr>';
                        }else if(belong==3){
                            str +='<tr class="data">\
                                <td>'+item.name+'</td>\
                                <td>'+item.other+'</td>\
                                <td>'+item.phone+'</td>\
                                <td>'+item.nurse_name+'</td>\
                                <td>'+item.employee_name+'</td>\
                                <td>'+b_time+'</td>\
                                <td>'+s_time+'</td>\
                                <td>'+item.contract_money+'</td>\
                                <td>'+item.nurse_pay+'</td>\
                                <td><a href="'+MODULE+'/OrderShow/OrderInfo/id/'+item.id+'.html">查看</a></td>\
                                </tr>';
                        }else if(belong==4){
                            str +='<tr class="data">\
                                <td>'+item.name+'</td>\
                                <td>'+item.other+'</td>\
                                <td>'+item.phone+'</td>\
                                <td>'+item.nurse_name+'</td>\
                                <td>'+item.employee_name+'</td>\
                                <td>'+b_time+'</td>\
                                <td>'+s_time+'</td>\
                                <td>'+item.contract_money+'</td>\
                                <td>'+item.nurse_pay+'</td>\
                                <td><a href="'+MODULE+'/OrderShow/OrderInfo/id/'+item.id+'.html">查看</a></td>\
                                </tr>';
                        }else if(belong==5){
                            str +='<tr class="data">\
                                <td>'+item.name+'</td>\
                                <td>'+item.other+'</td>\
                                <td>'+item.phone+'</td>\
                                <td>'+item.nurse_name+'</td>\
                                <td>'+item.employee_name+'</td>\
                                <td>'+b_time+'</td>\
                                <td>'+s_time+'</td>\
                                <td>'+item.contract_money+'</td>\
                                <td>'+item.nurse_pay+'</td>\
                                <td><a href="'+MODULE+'/OrderShow/OrderInfo/id/'+item.id+'.html">查看</a></td>\
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