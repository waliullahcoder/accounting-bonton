$(function () {
    "use strict";
    function onClicks() {
        $('.brand-toggle').on('click', function () {
            if ($('.site-wrapper').hasClass("aside-minimize")) {
                $('.site-wrapper').removeClass('aside-minimize');
            } else {
                $('.site-wrapper').toggleClass('aside-minimize');
            }
            $('.site-wrapper').toggleClass('session-sidebar');
            $(this).toggleClass('active');
        });

        $('.aside-menu-wrapper').on('mouseenter', function () {
            $('.site-wrapper').addClass('aside-minimize-hover');
        });

        $('.aside-menu-wrapper').on('mouseleave', function () {
            $('.site-wrapper').removeClass('aside-minimize-hover');
        });

        $('.has-submenu > a').on('click', function () {
            $(this).closest('.has-submenu').siblings().find('.menu-submenu').slideUp("50");
            if ($(this).closest('.has-submenu').hasClass('root-menu') && $(this).closest('.has-submenu').find('.menu-submenu .menu-submenu:visible')) {
                $(this).closest('.has-submenu').find('.menu-submenu .menu-submenu').slideUp('fast');
            }
            $(this).closest('.has-submenu').siblings().removeClass('menu-item-active');
            $(this).closest('.has-submenu').find(' >.menu-submenu').slideToggle("fast");
            $(this).closest('.has-submenu').addClass('menu-item-active');
            return false;
        });

        $('.password-toggler').on('click', function () {
            if ($(this).closest('.input-group').find('input').attr('type') === 'password') {
                $(this).closest('.input-group').find('input').attr('type', 'text');
                $(this).find('i').removeClass('fa-eye-slash');
                $(this).find('i').addClass('fa-eye');
            } else {
                $(this).closest('.input-group').find('input').attr('type', 'password');
                $(this).find('i').addClass('fa-eye-slash');
                $(this).find('i').removeClass('fa-eye');
            }
        });

        if ($('.short_description').length) {
            $('.short_description').summernote({
                placeholder: 'Summary',
                height: 100,
                tabsize: 1,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                ],
            });
        }

        if ($('.description').length) {
            $('.description').summernote({
                placeholder: 'Write here..',
                height: 300,
                styleTags: [
                    'p',
                    {
                        title: 'Blockquote',
                        tag: 'blockquote',
                        className: 'blockquote',
                        value: 'blockquote'
                    },
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
                ],
                prettifyHtml: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'add-text-tags', 'highlight', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'videoAttributes']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                ],
                imageAttributes: {
                    icon: '<i class="note-icon-pencil"/>',
                    figureClass: 'figureClass',
                    figcaptionClass: 'captionClass',
                    captionText: 'Caption Goes Here.',
                    manageAspectRatio: true // true = Lock the Image Width/Height, Default to true
                },
                lang: 'en-US',
                popover: {
                    image: [
                        ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                        ['float', ['floatLeft', 'floatRight', 'floatNone']],
                        ['remove', ['removeMedia']],
                        ['custom', ['imageAttributes']],
                    ],
                },
            });
        }
    }

    function daterange() {
        $(".date-range").each(function () {
            var $this = $(this);
            var today = moment().startOf("day");
            var value = $this.val();
            var startDate = false;
            var minDate = false;
            var maxDate = false;
            var advncdRange = false;
            var ranges = {
                Today: [moment(), moment()],
                Yesterday: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
            };

            var single = $this.data("single");
            var monthYearDrop = $this.data("show-dropdown");
            var format = $this.data("format");
            var separator = $this.data("separator");
            var pastDisable = $this.data("past-disable");
            var futureDisable = $this.data("future-disable");
            var timePicker = $this.data("time-picker");
            var timePickerIncrement = $this.data("time-gap");
            var advncdRange = $this.data("advanced-range");

            single = !single ? false : single;
            monthYearDrop = !monthYearDrop ? false : monthYearDrop;
            format = !format ? "YYYY-MM-DD" : format;
            separator = !separator ? " / " : separator;
            minDate = !pastDisable ? minDate : today;
            maxDate = !futureDisable ? maxDate : today;
            timePicker = !timePicker ? false : timePicker;
            timePickerIncrement = !timePickerIncrement ? 1 : timePickerIncrement;
            ranges = !advncdRange ? "" : ranges;

            $this.daterangepicker({
                singleDatePicker: single,
                showDropdowns: monthYearDrop,
                minDate: minDate,
                maxDate: maxDate,
                timePickerIncrement: timePickerIncrement,
                autoUpdateInput: false,
                ranges: ranges,
                locale: {
                    format: format,
                    separator: separator,
                    applyLabel: "Select",
                    cancelLabel: "Clear",
                },
            });
            if (single) {
                $this.on("apply.daterangepicker", function (ev, picker) {
                    $this.val(picker.startDate.format(format));
                });
            } else {
                $this.on("apply.daterangepicker", function (ev, picker) {
                    $this.val(
                        picker.startDate.format(format) +
                        separator +
                        picker.endDate.format(format)
                    );
                });
            }

            $this.on("cancel.daterangepicker", function (ev, picker) {
                $this.val("");
            });
        });
    }

    function select() {
        if ($('.select').length) {
            $('.select').select2({
                allowClear: true,
            });
        }
    }
    select();
    onClicks();
    daterange();

    $(".date_picker").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
    })
});
