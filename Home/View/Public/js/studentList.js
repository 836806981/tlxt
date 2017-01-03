var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var name = $('#name').val();
var come = $('#come').val();
var teacher = $('#teacher').val();
var class_name = $('#class_name').val();
var class_number = $('#class_number').val();
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
            come = $('#come').val();
            teacher = $('#teacher').val();
            class_name = $('#class_name').val();
            class_number = $('#class_number').val();
            belong = $('#belong').val();
            //reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;
            //if(!reg.test(status_23) && status_23){
            //    layer.msg('请输入正确回收时间 格式：2016-01-01！');
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
        $.AjaxPost(MODULE+"/Student/getStudentList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,belong:belong,name:name,come:come,teacher:teacher,class_name:class_name,class_number:class_number}, function (backdata) {
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
                        //未接单或者打回才能放回收站
                         var do_str = '';
                         var status_24 = '';
                            if(item.status==1) {
                                if (item.finish_now == 1) {
                                    do_str = '<a do_type="tiqian" s_id="' + item.id + '" s_name="' + item.name + '">提前结业</a>';
                                } else if (item.finish_now == 2) {
                                    do_str = '<a do_type="jieye" s_id="' + item.id + '" s_name="' + item.name + '">结业</a>';
                                } else if (item.finish_now == 3) {
                                    do_str = '<a do_type="yanqi" s_id="' + item.id + '" s_name="' + item.name + '">延期完成</a>';
                                }
                            }
                        if(item.status!=24){
                            status_24 = '<a do_type="taotai" s_id="'+item.id+'" s_name="'+item.name+'">淘汰</a>';
                        }
                        if(item.status==1) {
                            str += '<tr class=" data">\
                            <td width="8%">' + item.number + '</td>\
                            <td width="8%">' + item.name + '</td>\
                            <td width="8%">' + item.status_name + '</td>\
                            <td width="8%">' + item.come + '</td>\
                            <td width="8%">' + item.teacher + '</td>\
                            <td width="8%">' + item.class_name + item.class_number + '期</td>\
                            <td width="8%">' + item.come_time + '</td>\
                            <td width="8%">' + item.finish_time + '</td>\
                            <td width="8%">' + item.study_day + '</td>\
                            <td width="8%">' + item.study_day_true + '</td>\
                            <td width="8%">wu</td>\
                            <td width="15%">' + do_str + '\
                                            <a href="' + MODULE + '/Student/studentInfo/id/' + item.id + '.html">查看</a>\
                                            ' + status_24 + '</td>\
                        </tr>';

                        }else if(item.status==2) {
                            str += '<tr class=" data">\
                            <td width="8%">' + item.number + '</td>\
                            <td width="8%">' + item.name + '</td>\
                            <td width="8%">' + item.status_name + '</td>\
                            <td width="8%">' + item.come + '</td>\
                            <td width="8%">' + item.teacher + '</td>\
                            <td width="8%">' + item.class_name + item.class_number + '期</td>\
                            <td width="8%">' + item.come_time + '</td>\
                            <td width="8%">' + item.finish_time_s + '</td>\
                            <td width="8%">' + item.study_day_true + '</td>\
                            <td width="8%">wu</td>\
                            <td width="15%">' + do_str + '\
                                            <a href="' + MODULE + '/Student/studentInfo_1/id/' + item.id + '.html">查看</a>\
                                            ' + status_24 + '</td>\
                        </tr>';

                        }else if(item.status==3) {
                            str += '<tr class=" data">\
                            <td width="8%">' + item.number + '</td>\
                            <td width="8%">' + item.name + '</td>\
                            <td width="8%">' + item.status_name + '</td>\
                            <td width="8%">' + item.come + '</td>\
                            <td width="8%">' + item.teacher + '</td>\
                            <td width="8%">' + item.class_name + item.class_number + '期</td>\
                            <td width="8%">' + item.come_time + '</td>\
                            <td width="8%">' + item.finish_time_s + '</td>\
                            <td width="8%">' + item.study_day_true + '</td>\
                            <td width="8%">wu</td>\
                            <td width="15%">' + do_str + '\
                                            <a href="' + MODULE + '/Student/studentInfo_1/id/' + item.id + '.html">查看</a>\
                                            ' + status_24 + '</td>\
                        </tr>';

                        }else if(item.status==24) {
                            str += '<tr class=" data">\
                            <td width="8%">' + item.number + '</td>\
                            <td width="8%">' + item.name + '</td>\
                            <td width="8%">' + item.status_name + '</td>\
                            <td width="8%">' + item.come + '</td>\
                            <td width="8%">' + item.teacher + '</td>\
                            <td width="8%">' + item.class_name + item.class_number + '期</td>\
                            <td width="8%">' + item.come_time + '</td>\
                            <td width="8%">' + item.status_24_t + '</td>\
                            <td width="8%">' + item.status_24_r + '</td>\
                            <td width="15%">' + do_str + '\
                                            <a href="' + MODULE + '/Student/studentInfo/id/' + item.id + '.html">查看</a>\
                                            ' + status_24 + '</td>\
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

$('select[status_own=status_own]').live('change',function(){

    $.AjaxPost(MODULE+"/Nurse/changeStatus_own", {nurse_id:$(this).attr('nurse_id'),status_own:$(this).val()}, function (backdata) {

    }, true);
});