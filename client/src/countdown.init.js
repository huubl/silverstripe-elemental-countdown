;(function ($) {
    $.fn.extend({
        ssCountDownBlockOptions: function (opts) {
            return $(this).each(function () {
                var config = $.extend(opts || {}, $(this).data(), $(this).data('countdownconfig'), {});

                $(this).each(function () {
                    if ($(this).hasClass('cd-applied')) return true;

                    $(this).countdown(config.end, {
                        elapse: config.elapse
                    }).on('update.countdown', function (event) {

                        var months = $(this).find('.months');
                        var daysOffset = event.offset.totalDays;

                        if (months.length) {
                            daysOffset = event.offset.daysToMonth;
                        }

                        months.html(event.offset.months);
                        $(this).find('.days').html(daysOffset);
                        $(this).find('.hours').html(event.offset.hours);
                        $(this).find('.minutes').html(event.offset.minutes);
                        $(this).find('.seconds').html(event.offset.seconds);
                    });
                })
            });
        }
    });

    $(window).on('load', function () {
        $('.countdown').ssCountDownBlockOptions();
    });
}(jQuery));
