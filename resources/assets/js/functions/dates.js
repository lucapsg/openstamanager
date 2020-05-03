import 'bootstrap-daterangepicker';
import moment from 'moment';
import 'tempusdominus-bootstrap-4';

export function start_datepickers() {
    var icons = {
        time: 'fa fa-clock-o',
        date: 'fa fa-calendar',
        up: 'fa fa-chevron-up',
        down: 'fa fa-chevron-down',
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-street-view',
        clear: 'fa fa-trash',
        close: 'fa fa-times'
    };

    var date_format = dateFormatMoment(globals.date_format);
    var timestamp_format = dateFormatMoment(globals.timestamp_format);
    var time_format = dateFormatMoment(globals.time_format);

    $('.timestamp-picker').each(function () {
        var $this = $(this);
        $this.datetimepicker({
            format: timestamp_format,
            locale: globals.locale,
            icons: icons,
            collapse: false,
            sideBySide: true,
            useCurrent: false,
            stepping: 5,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            },
            minDate: moment($this.attr('min-date')).isValid() ? $this.attr('min-date') : false,
            maxDate: moment($this.attr('max-date')).isValid() ? $this.attr('max-date') : false,
        });
    });

    //fix per timestamp-picker non visibile con la classe table-responsive
    $('.timestamp-picker').each(function () {
        var $this = $(this);
        $this.on("dp.show", function (e) {
            $('#tecnici > div').removeClass('table-responsive');
        });
        $this.on("dp.hide", function (e) {
            $('#tecnici > div').addClass('table-responsive');
        })
    });

    $('.datepicker').each(function () {
        var $this = $(this);
        $this.datetimepicker({
            format: date_format,
            locale: globals.locale,
            icons: icons,
            useCurrent: false,
            minDate: moment($this.attr('min-date')).isValid() ? $this.attr('min-date') : false,
            maxDate: moment($this.attr('max-date')).isValid() ? $this.attr('max-date') : false,
        });
    });

    $('.timepicker').each(function () {
        var $this = $(this);
        $this.datetimepicker({
            format: time_format,
            locale: globals.locale,
            icons: icons,
            useCurrent: false,
            stepping: 5,
            minDate: moment($this.attr('min-date')).isValid() ? $this.attr('min-date') : false,
            maxDate: moment($this.attr('max-date')).isValid() ? $this.attr('max-date') : false,
        });
    });
}

export function start_complete_calendar(id, callback) {
    var ranges = {};
    ranges[globals.translations.today] = [moment(), moment()];
    ranges[globals.translations.firstThreemester] = [moment("01", "MM"), moment("03", "MM").endOf('month')];
    ranges[globals.translations.secondThreemester] = [moment("04", "MM"), moment("06", "MM").endOf('month')];
    ranges[globals.translations.thirdThreemester] = [moment("07", "MM"), moment("09", "MM").endOf('month')];
    ranges[globals.translations.fourthThreemester] = [moment("10", "MM"), moment("12", "MM").endOf('month')];
    ranges[globals.translations.firstSemester] = [moment("01", "MM"), moment("06", "MM").endOf('month')];
    ranges[globals.translations.secondSemester] = [moment("06", "MM"), moment("12", "MM").endOf('month')];
    ranges[globals.translations.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    ranges[globals.translations.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
    ranges[globals.translations.thisYear] = [moment().startOf('year'), moment().endOf('year')];
    ranges[globals.translations.lastYear] = [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')];

    var format = dateFormatMoment(globals.date_format);
    $(id).daterangepicker({
            locale: {
                format: format,
                customRangeLabel: globals.translations.custom,
                applyLabel: globals.translations.apply,
                cancelLabel: globals.translations.cancel,
                fromLabel: globals.translations.from,
                toLabel: globals.translations.to,
            },
            ranges: ranges,
            startDate: globals.start_date_formatted,
            endDate: globals.end_date_formatted,
            applyClass: 'btn btn-success btn-sm',
            cancelClass: 'btn btn-danger btn-sm',
            linkedCalendars: false
        },
        callback
    );
}

export function dateFormatMoment(format) {
    /*
     * PHP => moment.js
     * Will take a php date format and convert it into a JS format for moment
     * http://www.php.net/manual/en/function.date.php
     * http://momentjs.com/docs/#/displaying/format/
     */
    var formatMap = {
        d: 'DD',
        D: 'ddd',
        j: 'D',
        l: 'dddd',
        N: 'E',
        S: function () {
            return '[' + moment().format('Do').replace(/\d*/g, '') + ']';
        },
        w: 'd',
        z: function () {
            return moment().format('DDD') - 1;
        },
        W: 'W',
        F: 'MMMM',
        m: 'MM',
        M: 'MMM',
        n: 'M',
        t: function () {
            return moment().daysInMonth();
        },
        L: function () {
            return moment().isLeapYear() ? 1 : 0;
        },
        o: 'GGGG',
        Y: 'YYYY',
        y: 'YY',
        a: 'a',
        A: 'A',
        B: function () {
            var thisUTC = moment().clone().utc(),
                // Shamelessly stolen from http://javascript.about.com/library/blswatch.htm
                swatch = ((thisUTC.hours() + 1) % 24) + (thisUTC.minutes() / 60) + (thisUTC.seconds() / 3600);
            return Math.floor(swatch * 1000 / 24);
        },
        g: 'h',
        G: 'H',
        h: 'hh',
        H: 'HH',
        i: 'mm',
        s: 'ss',
        u: '[u]', // not sure if moment has this
        e: '[e]', // moment does not have this
        I: function () {
            return moment().isDST() ? 1 : 0;
        },
        O: 'ZZ',
        P: 'Z',
        T: '[T]', // deprecated in moment
        Z: function () {
            return parseInt(moment().format('ZZ'), 10) * 36;
        },
        c: 'YYYY-MM-DD[T]HH:mm:ssZ',
        r: 'ddd, DD MMM YYYY HH:mm:ss ZZ',
        U: 'X'
    };
    var formatEx = /[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]/g;

    return format.replace(formatEx, function (phpStr) {
        return typeof formatMap[phpStr] === 'function' ? formatMap[phpStr].call(that) : formatMap[phpStr];
    })
}
