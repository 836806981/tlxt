var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var name = $('#name').val();
var age1 = $('#age1').val();
var age2 = $('#age2').val();
var study_time = $('#study_time').val();
var practical = $('#practical').val();


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
            var reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;

            name = $('#name').val();
             age1 = $('#age1').val();
             age2 = $('#age2').val();
             study_time = $('#study_time').val();
             practical = $('#practical').val();
            var reg_number = /^[1-9]+[0-9]*]*$/;

            if(!reg_number.test(age1) && age1!=''){
                layer.msg('请输入正确的年龄！');
                return false;
            }
            if(!reg.test(study_time) && study_time!=''){
                layer.msg('请输入正确的入学日期！');
                return false;
            }
            if(!reg_number.test(age2) && age2!=''){
                layer.msg('请输入正确的年龄！');
                return false;
            }
            if(age2&&age1&&age2<age1){
                layer.msg('请输入正确的年龄！');
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
        $.AjaxPost(MODULE+"/Student/getStudentList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,name:name,age1:age1,age2:age2,study_time:study_time,practical:practical}, function (backdata) {
            if (backdata.code == 1000) {
                List_zcj = backdata.data.list;
                Num_zcj = backdata.data.num;
                $.ProductPageShow(Num_zcj,pagenum_zcj,"b");
                $.CurrentPageShow(currentpage_zcj);
                $('#show_number_str').text(Num_zcj);
				var str='';
				var $dom=$(".last_tr");
                $(".data").remove();
				if(List_zcj!=null){
                    $.each(List_zcj,function(i,item){

                        var dell = '<a href="'+MODULE+'/Student/studentInfo/id/'+item.id+'.html">查看</a>';

                        str +='<tr class="data">\
                            <td>'+item.number+'</td>\
                            <td>'+item.name+'</td>\
                            <td>'+item.age+'</td>\
                            <td>'+item.id_card+'</td>\
                            <td>'+item.phone+'</td>\
                            <td>'+item.study_time+'</td>\
                            <td>'+item.practical_name+'</td>\
                            <td>'+dell+'</td>\
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