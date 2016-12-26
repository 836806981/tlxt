var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];


var name = $('#name').val();
var status_sh = $('#status_sh').val();
var status_own = $('#status_own').val();
var agreement_type = $('#agreement_type').val();


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
            status_sh = $('#status_sh').val();
            status_own = $('#status_own').val();
            agreement_type = $('#agreement_type').val();
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
        $.AjaxPost(MODULE+"/Nurse/getNurseList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj,agreement_type:agreement_type,name:name,status_sh:status_sh,status_own:status_own}, function (backdata) {
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
                        var status_own_str = '';
                        var status_24 = '';
                        var agreement_type_1 = '';
                        var is_type1 = '';
                        if(item.status_own==1){
                            status_own_str = '<select class="form-control" nurse_id="'+item.id+'" status_own="status_own" >\
                            <option value="1" selected>暂不接单</option>\
                            <option value="2">等单中</option>\
                            <option value="3">私签中</option>\
                            <option value="4">外单中</option>\
                        </select>';
                        }else if(item.status_own==2){
                            status_own_str = '<select class="form-control" nurse_id="'+item.id+'" status_own="status_own" >\
                            <option value="1">暂不接单</option>\
                            <option value="2" selected>等单中</option>\
                            <option value="3">私签中</option>\
                            <option value="4">外单中</option>\
                        </select>';
                        }else if(item.status_own==3){
                            status_own_str = '<select class="form-control" nurse_id="'+item.id+'" status_own="status_own" >\
                            <option value="1">暂不接单</option>\
                            <option value="2">等单中</option>\
                            <option value="3" selected>私签中</option>\
                            <option value="4">外单中</option>\
                        </select>';
                        }else if(item.status_own==4){
                            status_own_str = '<select class="form-control" nurse_id="'+item.id+'" status_own="status_own"  >\
                            <option value="1">暂不接单</option>\
                            <option value="2">等单中</option>\
                            <option value="3">私签中</option>\
                            <option value="4" selected>外单中</option>\
                        </select>';
                        }

                        if(item.status!=24){
                            status_24 = '<a do_type="taotai" s_id="'+item.id+'" s_name="'+item.name+'">淘汰</a>';
                        }
                        if(item.agreement_type==3 && item.status!=24){
                            agreement_type_1 = '<a href="'+MODULE+'/Nurse/changeNurse/id/'+item.id+'.html">转正</a>';
                        }
                        if(item.agreement_type==1){
                            var data_type1 = 'name="'+item.name+'"  price_name="'+item.price_name+'" nurse_id="'+item.id+'" ';
                            is_type1 = '<a class="level_up"  '+data_type1+'>升级</a><a class="level_up" '+data_type1+'>降级</a>';
                        }


                        if(item.status==24){
                            str += '<tr class="data">\
                                <td>' + item.number + '</td>\
                                <td>' + item.name + '</td>\
                                <td>' + item.is_student_str + '</td>\
                                <td>' + item.work_time + '年</td>\
                                <td>' + item.status_24_t + '</td>\
                                <td>' + item.status_24_r + '</td>\
                                <td>' + status_24 + agreement_type_1 + '<a href="' + MODULE + '/Nurse/nurseInfo/id/' + item.id + '.html">查看</a></td>\
                                </tr>';
                        }else {
                            str += '<tr class="data">\
                                <td>' + item.number + '</td>\
                                <td>' + item.status_sh_name + '</td>\
                                <td>' + status_own_str + '</td>\
                                <td>' + item.name + '</td>\
                                <td>' + item.is_student_str + '</td>\
                                <td>' + item.work_time + '年</td>\
                                <td><a class="search" type="1" nurse_name="'+item.name+'" nurse_id="'+item.id+'" number="'+ item.count1+'">' + item.count1+ '</a></td>\
                                <td>' + item.b_time + '</td>\
                                <td><a class="search" type="2" nurse_name="'+item.name+'" nurse_id="'+item.id+'" number="'+ item.count2+'">' + item.count2+ '</a></td>\
                                <td><a class="search" type="3" nurse_name="'+item.name+'" nurse_id="'+item.id+'" number="'+ item.count3+'">' + item.count3+ '</a></td>\
                                <td>' +is_type1+ status_24 + agreement_type_1 + '<a href="' + MODULE + '/Nurse/nurseInfo/id/' + item.id + '.html">查看</a></td>\
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