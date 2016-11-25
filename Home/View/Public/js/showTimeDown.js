/**
 * Created by 伟 on 2016/11/25.
 */

    var showTimeDown = function () {
        var timer = setInterval(function () {
            var show = $('span[name=show]');
            var do_interval = 0;
            show.each(function () {
                var time = $(this).attr('val');
                if (time > 0) {
                    $(this).attr('val', time - 1);
                    $(this).text(Math.floor(time / 60) + ':' + time % 60);
                    do_interval = 1;
                } else if (time == 0) {
                    $(this).text(0);
                }
            });
            if (do_interval == 0) {
                clearInterval(timer);
            }
        }, 1000);
    }