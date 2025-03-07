/*
 *  jQuery Date Dropdowns - v1.0.0
 *  A simple, customisable date select plugin
 *  https://github.com/IckleChris/jquery-date-dropdowns
 *  Made by Chris Brown
 *  Under MIT License
 */
! function(a, b, c, d) {
    "use strict";

    function e(b, c) {
        return this.element = b, this.$element = a(b), this.config = a.extend({}, g, c), this.internals = {
            objectRefs: {}
        }, this.init(), this
    }
    var f = "dateDropdowns",
        g = {
            defaultDate: null,
            defaultDateFormat: "yyyy-mm-dd",
            displayFormat: "dmy",
            submitFormat: "yyyy-mm-dd",
            minAge: 0,
            maxAge: 120,
            minYear: null,
            maxYear: 2050,
            submitFieldName: "date",
            wrapperClass: "date-dropdowns",
            dropdownClass: null,
            daySuffixes: !0,
            monthSuffixes: !0,
            //monthFormat: "long",
            monthFormat: "numeric",
            required: !1,
            dayLabel: "Day",
            monthLabel: "Month",
            yearLabel: "Year",
            monthLongValues: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            //monthLongValues: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            monthShortValues: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            initialDayMonthYearValues: ["Day", "Month", "Year"],
            daySuffixValues: ["st", "nd", "rd", "th"]
        };
    a.extend(e.prototype, {
        init: function() {
            this.checkForDuplicateElement(), this.setInternalVariables(), this.setupMarkup(), this.buildDropdowns(), this.attachDropdowns(), this.bindChangeEvent(), this.config.defaultDate && this.populateDefaultDate()
        },
        checkForDuplicateElement: function() {
            return !a('input[name="' + this.config.submitFieldName + '"]').length || (a.error("Duplicate element found"), !1)
        },
        setInternalVariables: function() {
            var a = new Date;
            this.internals.currentDay = a.getDate(), this.internals.currentMonth = a.getMonth() + 1, this.internals.currentYear = a.getFullYear()
        },
        setupMarkup: function() {
            var b, c;
            if ("input" === this.element.tagName.toLowerCase()) {
                this.config.defaultDate || (this.config.defaultDate = this.element.value), c = this.$element.attr("type", "hidden").wrap('<div class="' + this.config.wrapperClass + '"></div>');
                var d = this.config.submitFieldName !== g.submitFieldName,
                    e = this.element.hasAttribute("name");
                e || d ? d && this.$element.attr("name", this.config.submitFieldName) : this.$element.attr("name", g.submitFieldName), b = this.$element.parent()
            } else c = a("<input/>", {
                type: "hidden",
                name: this.config.submitFieldName
            }), this.$element.append(c).addClass(this.config.wrapperClass), b = this.$element;
            return this.internals.objectRefs.pluginWrapper = b, this.internals.objectRefs.hiddenField = c, !0
        },
        buildDropdowns: function() {
            var a, b, c;
            return e.message = {
                day: this.config.initialDayMonthYearValues[0],
                month: this.config.initialDayMonthYearValues[1],
                year: this.config.initialDayMonthYearValues[2]
            }, a = this.buildDayDropdown(), this.internals.objectRefs.dayDropdown = a, b = this.buildMonthDropdown(), this.internals.objectRefs.monthDropdown = b, c = this.buildYearDropdown(), this.internals.objectRefs.yearDropdown = c, !0
        },
        attachDropdowns: function() {
            var a = this.internals.objectRefs.pluginWrapper,
                b = this.internals.objectRefs.dayDropdown,
                c = this.internals.objectRefs.monthDropdown,
                d = this.internals.objectRefs.yearDropdown;
            switch (this.config.displayFormat) {
                case "mdy":
                    a.append(c, b, d);
                    break;
                case "ymd":
                    a.append(d, c, b);
                    break;
                case "dmy":
                default:
                    a.append(b, c, d)
            }
            return !0
        },
        bindChangeEvent: function() {
            var a = this.internals.objectRefs.dayDropdown,
                b = this.internals.objectRefs.monthDropdown,
                c = this.internals.objectRefs.yearDropdown,
                d = this,
                e = this.internals.objectRefs;
            e.pluginWrapper.on("change", "select", function() {
                var f, g, h = a.val(),
                    i = b.val(),
                    j = c.val();
                return (f = d.checkDate(h, i, j)) ? (e.dayDropdown.addClass("invalid"), !1) : ("00" !== e.dayDropdown.val() && e.dayDropdown.removeClass("invalid"), e.hiddenField.val(""), f || h * i * j === 0 || (g = d.formatSubmitDate(h, i, j), e.hiddenField.val(g)), void e.hiddenField.change())
            })
        },
        populateDefaultDate: function() {
            var a = this.config.defaultDate,
                b = [],
                c = "",
                d = "",
                e = "";
            switch (this.config.defaultDateFormat) {
                case "yyyy-mm-dd":
                default:
                    b = a.split("-"), c = b[2], d = b[1], e = b[0];
                    break;
                case "dd/mm/yyyy":
                    b = a.split("/"), c = b[0], d = b[1], e = b[2];
                    break;
                case "mm/dd/yyyy":
                    b = a.split("/"), c = b[1], d = b[0], e = b[2];
                    break;
                case "unix":
                    b = new Date, b.setTime(1e3 * a), c = b.getDate() + "", d = b.getMonth() + 1 + "", e = b.getFullYear(), c.length < 2 && (c = "0" + c), d.length < 2 && (d = "0" + d)
            }
            return this.internals.objectRefs.dayDropdown.val(c), this.internals.objectRefs.monthDropdown.val(d), this.internals.objectRefs.yearDropdown.val(e), this.internals.objectRefs.hiddenField.val(a), !0 === this.checkDate(c, d, e) && this.internals.objectRefs.dayDropdown.addClass("invalid"), !0
        },
        buildBaseDropdown: function(b) {
            var c = b;
            return this.config.dropdownClass && (c += " " + this.config.dropdownClass), a("<select></select>", {
                class: c,
                name: this.config.submitFieldName + "_[" + b + "]",
                required: this.config.required
            })
        },
        buildDayDropdown: function() {
            var a, b = this.buildBaseDropdown("day"),
                d = c.createElement("option");
            d.setAttribute("value", ""), d.appendChild(c.createTextNode(this.config.dayLabel)), b.append(d);
            for (var e = 1; e < 10; e++) a = this.config.daySuffixes ? e : "0" + e, d = c.createElement("option"), d.setAttribute("value", "0" + e), d.appendChild(c.createTextNode(a)), b.append(d);
            for (var f = 10; f <= 31; f++) a = f, this.config.daySuffixes && (a = f), d = c.createElement("option"), d.setAttribute("value", f), d.appendChild(c.createTextNode(a)), b.append(d);
            return b
        },
        buildMonthDropdown: function() {
            var a = this.buildBaseDropdown("month"),
                b = c.createElement("option");
            b.setAttribute("value", ""), b.appendChild(c.createTextNode(this.config.monthLabel)), a.append(b);
            for (var d = 1; d <= 12; d++) {
                var e;
                switch (this.config.monthFormat) {
                    case "short":
                        e = this.config.monthShortValues[d - 1];
                        break;
                    case "long":
                        e = this.config.monthLongValues[d - 1];
                        break;
                    case "numeric":
                        e = d, this.config.monthSuffixes;
                }
                d < 10 && (d = "0" + d), b = c.createElement("option"), b.setAttribute("value", d), b.appendChild(c.createTextNode(e)), a.append(b)
            }
            return a
        },
        buildYearDropdown: function() {
            var a = this.config.minYear,
                b = this.config.maxYear,
                d = this.buildBaseDropdown("year"),
                e = c.createElement("option");
            e.setAttribute("value", ""), e.appendChild(c.createTextNode(this.config.yearLabel)), d.append(e), a || (a = this.internals.currentYear - (this.config.maxAge + 1)), b || (b = this.internals.currentYear - this.config.minAge);
            for (var f = b; f >= a; f--) e = c.createElement("option"), e.setAttribute("value", f), e.appendChild(c.createTextNode(f)), d.append(e);
            return d
        },
        getSuffix: function(a) {
            var b = "",
                c = this.config.daySuffixValues[0],
                d = this.config.daySuffixValues[1],
                e = this.config.daySuffixValues[2],
                f = this.config.daySuffixValues[3];
            switch (a % 10) {
                case 1:
                    b = a % 100 === 11 ? f : c;
                    break;
                case 2:
                    b = a % 100 === 12 ? f : d;
                    break;
                case 3:
                    b = a % 100 === 13 ? f : e;
                    break;
                default:
                    b = "th"
            }
            return b
        },
        checkDate: function(a, b, c) {
            var d;
            if ("00" !== b) {
                var e = new Date(c, b, 0).getDate(),
                    f = parseInt(a, 10);
                d = this.updateDayOptions(e, f), d && this.internals.objectRefs.hiddenField.val("")
            }
            return d
        },
        updateDayOptions: function(a, b) {
            var d = parseInt(this.internals.objectRefs.dayDropdown.children(":last").val(), 10),
                e = "",
                f = "",
                g = !1;
            if (d > a) {
                for (; d > a;) this.internals.objectRefs.dayDropdown.children(":last").remove(), d--;
                b > a && (g = !0)
            } else if (d < a)
                for (; d < a;) {
                    e = ++d, f = e, this.config.daySuffixes;
                    var h = c.createElement("option");
                    h.setAttribute("value", e), h.appendChild(c.createTextNode(f)), this.internals.objectRefs.dayDropdown.append(h)
                }
            return g
        },
        formatSubmitDate: function(a, b, c) {
            var d, e;
            switch (this.config.submitFormat) {
                case "unix":
                    e = new Date, e.setDate(a), e.setMonth(b - 1), e.setYear(c), d = Math.round(e.getTime() / 1e3);
                    break;
                default:
                    d = this.config.submitFormat.replace("dd", a).replace("mm", b).replace("yyyy", c)
            }
            return d
        },
        destroy: function() {
            var a = this.config.wrapperClass;
            if (this.$element.hasClass(a)) this.$element.empty();
            else {
                var b = this.$element.parent(),
                    c = b.find("select");
                this.$element.unwrap(), c.remove()
            }
        }
    }), a.fn[f] = function(b) {
        return this.each(function() {
            if ("string" == typeof b) {
                var c = Array.prototype.slice.call(arguments, 1),
                    d = a.data(this, "plugin_" + f);
                if ("undefined" == typeof d) return a.error("Please initialize the plugin before calling this method."), !1;
                d[b].apply(d, c)
            } else a.data(this, "plugin_" + f) || a.data(this, "plugin_" + f, new e(this, b))
        }), this
    }
}(jQuery, window, document);