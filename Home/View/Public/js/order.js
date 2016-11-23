var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var number = $('#number').val();
var name = $('#name').val();
var phone = $('#phone').val();
var employee_id = $('#employee_id').val();
var add_time = $('#add_time').val();


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
             number = $('#number').val();
             name = $('#name').val();
             phone = $('#phone').val();
             employee_id = $('#employee_id').val();
             add_time = $('#add_time').val();
            var reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;
            if(!reg.test(add_time) && add_time){
                layer.msg('请输入正确派单时间 格式：2016-01-01！');
                return false;
            }
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
        $.AjaxPost(MODULE+"/Order/getOrderList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,number:number,name:name,phone:phone,employee_id:employee_id,add_time:add_time}, function (backdata) {
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
                var dell = '';
                if(List_zcj!=null){
                    $.each(List_zcj,function(i,item){
                        if(backdata.permission == 11){
                            del_str = '<a id="del_this" order_id="'+item.id+'">放入回收站</a>';
                        }
                        dell='';
                        if(item.is_read !=1) {
                            dell = '<a href="' + MODULE + '/Order/changeOrder/id/' + item.id + '.html">编辑</a>';
                        }

                        str +='<tr class="data">\
                        <td>'+item.number+'</td>\
                        <td>'+item.name+'</td>\
                        <td>'+item.phone+'</td>\
                        <td>'+item.other+'</td>\
                        <td>'+item.employee_name+'</td>\
                        <td>'+item.ask+'</td>\
                        <td>'+item.add_time+'</td>\
                        <td>'+dell+del_str+'</td>\
                        </tr>';
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