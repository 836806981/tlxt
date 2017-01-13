var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var order_number = $('#order_number').val();
var order_name = $('#order_name').val();
var nurse_number = $('#nurse_number').val();
var nurse_name = $('#nurse_name').val();


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
            order_name = $('#order_name').val();
            nurse_number = $('#nurse_number').val();
            nurse_name = $('#nurse_name').val();
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
        $.AjaxPost(MODULE+"/Safe/getSafeList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,order_number:order_number,order_name:order_name,nurse_number:nurse_number,nurse_name:nurse_name}, function (backdata) {
            if (backdata.code == 1000) {
                List_zcj = backdata.data.list;
                Num_zcj = backdata.data.num;
                $.ProductPageShow(Num_zcj,pagenum_zcj,"b");
                $.CurrentPageShow(currentpage_zcj);
                $('#show_number_str').text(Num_zcj);
				var str='';
				var $dom=$(".last_tr");
                $(".data").remove();
                var str_do = "javascript:return window.confirm('确认操作？')";
                var back = '';
                if(List_zcj!=null){
                    $.each(List_zcj,function(i,item){
                        //未接单或者打回才能放回收站
                        var Y_m = '';
                        var buy_safe = '';
                        if(item.type!='') {
                            buy_safe = '<a class="nurse_safe" safe_id="' + item.id + '" nurse_name="'+item.nurse_name+'">购买</a> |';
                        }
                        if(item.type==''){
                            Y_m = '<br/> <a href="'+MODULE+'/Safe/nurseBuyType/id/'+item.id+'/type/1.html"  onclick="'+str_do+'">包年</a> | <a href="'+MODULE+'/Safe/nurseBuyType/id/'+item.id+'/type/2.html"  onclick="'+str_do+'">包月</a>';
                        }

                        str +='	<tr class="data">\
                        <td>'+item.order_number+'</td>\
                        <td>'+item.order_name+'</td>\
                        <td>'+item.nurse_number+'</td>\
                        <td>'+item.nurse_name+'</td>\
                        <td>'+item.id_card+'</td>\
                        <td>'+item.status_6_str+'</td>\
                        <td>'+item.status_name+'</td>\
                        <td>'+item.time+'</td>\
                        <td>'+item.type+'</td>\
                        <td>'+buy_safe+' <a class="no_safe" safe_id="' + item.id + '">不需要购买</a> | <a href="'+MODULE+'/Safe/nurseBuy/id/'+item.nurse_id+'.html">查看记录</a> '+Y_m+'</td>\
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