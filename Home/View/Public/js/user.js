var currentpage_zcj = 1;
var pagenum_zcj =20;
var List_zcj=[];
var Num_zcj=[];
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

        $('#keyword').bind('keypress',function(event){
            if(event.keyCode == "13")
            {
                $("#keyword").blur();
                keyword = $("#keyword").val();
                if(keyword==''||!keyword){
                    alert('请输入');
                    return false;
                }else{
                    currentpage_zcj = 1;
                    $.getNewsListByAllTerm();
                }
            }
        });

        //页码点击效果
        $("#priority").live("change", function () {
            priority = $("#priority").val();
            currentpage_zcj = 1;
            $.getNewsListByAllTerm();
        });

        //搜索
        $("#find").live("click", function () {

            keyword = $("#keyword").val();
            if(keyword==''||!keyword){
                alert('请输入');
                return false;
            }else{
                currentpage_zcj = 1;
                $.getNewsListByAllTerm();
            }

        });
    },
    initinfo: function () {
        //根据页码获取数据
        $.getNewsListByAllTerm();
		
    },
	//根据所有条件查询新闻列表
    getNewsListByAllTerm: function () {
        //异步提交数据,参数：currentpage,要查询的页码;pagenum，每页的条数
        $.AjaxPost(MODULE+"/User/getUserList", {currentpage:currentpage_zcj, pagenum:pagenum_zcj}, function (backdata) {
            if (backdata.code == 1000) {
                List_zcj = backdata.data.list;
                Num_zcj = backdata.data.num;
                $.ProductPageShow(Num_zcj,pagenum_zcj,"b");
                $.CurrentPageShow(currentpage_zcj);
				var str='';
				var $dom=$(".last_tr");
                $(".data").remove();
                var confirm1 = "javascript:return window.confirm('确定停用么？')";
                var confirm2 = "javascript:return window.confirm('确定启用么？')";
                var confirm3 = "javascript:return window.confirm('确定删除么？')";
                var confirm4 = "javascript:return window.confirm('确定重置密码么？')";

				if(List_zcj!=null){
                    $.each(List_zcj,function(i,item){
                        if(backdata.session_id == item.id||backdata.session_per==24){
                            var change_info = '<a href="'+MODULE+'/User/changeInfo/id/'+item.id+'.html">修改</a>';
                        }else{
                            var change_info = '';
                        }
                        var set_status = '';
                        if(item.status==1&&backdata.session_per==24&&item.permission!=24){
                            var set_status = '<a onclick="'+confirm1+'" href="'+MODULE+'/User/set_status_0/id/'+item.id+'.html">停用</a>';
                        }else if(item.status==0&&backdata.session_per==24&&item.permission!=24){
                            var set_status = '<a onclick="'+confirm2+'" href="'+MODULE+'/User/set_status_1/id/'+item.id+'.html">启用</a>';
                        }
                        if(backdata.session_per==11&&item.permission!=24){
                            var del_employee = '<a onclick="'+confirm3+'" href="'+MODULE+'/User/del_user/id/'+item.id+'.html">删除</a>\
                                <a onclick="'+confirm4+'" href="'+MODULE+'/User/resetPwd/id/'+item.id+'.html">重置密码</a>';
                        }else{
                            var del_employee = '';
                        }



                        str +='<tr class="data">\
                        <td>'+item.id+'</td>\
                        <td>'+item.username+'</td>\
                        <td>'+item.real_name+'</td>\
                        <td>\
                        <span class="spa_cek">'+item.permission_name+'</span>\
                        </td>\
                        <td>'+item.status_name+'</td>\
                        <td>'+change_info+'<a href="'+MODULE+'/User/userInfo/id/'+item.id+'.html">查看</a>\
                            '+set_status+del_employee+'</td>\
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