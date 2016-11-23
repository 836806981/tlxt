var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var name = $('#name').val();
var b_time = $('#b_time').val();
var id_card = $('#id_card').val();
var safe_time = $('#safe_time').val();
var is_safe = $('#is_safe').val();


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
             b_time = $('#b_time').val();
             id_card = $('#id_card').val();
             safe_time = $('#safe_time').val();
             is_safe = $('#is_safe').val();
             var reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;
            var reg_id_card = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if(!reg.test(b_time) && b_time){
                layer.msg('请输入正确派单时间 格式：2016-01-01！');
                return false;
            }
            if(!reg_id_card.test(id_card) && b_time){
                layer.msg('请输入正确身份证号！');
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
        $.AjaxPost(MODULE+"/Insurance/getInsuranceList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,name:name,b_time:b_time,id_card:id_card,safe_time:safe_time,is_safe:is_safe}, function (backdata) {
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
                var buy_confirm = "javascript:return window.confirm('确定已购买么？')";
                var no_buy_confirm = "javascript:return window.confirm('确定忽略么？')";
				if(List_zcj!=null){
                    $.each(List_zcj,function(i,item){
                        if(item.is_safe == 1){
                            del_str = '<a  onclick="'+buy_confirm+'" href="'+MODULE+'/Insurance/buy/id/'+item.id+'" >购买</a> | <a onclick="'+no_buy_confirm+'"  href="'+MODULE+'/Insurance/no_buy/id/'+item.id+'">忽略</a>';
                        }else if(item.is_safe == 2){
                            del_str = '已购买'
                        }else if(item.is_safe == 3){
                            del_str = '已忽略'
                        }
                        str +='<tr class="data">\
                        <td>'+item.nurse_name+'</td>\
                        <td>'+item.nurse_id_card+'</td>\
                        <td>'+item.b_time+'('+item.true_b_time+')'+'</td>\
                        <td>'+item.safe_time+'</td>\
                        <td>'+item.is_safe_name+'</td>\
                        <td>'+del_str+'</td>\
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