/*
@license

dhtmlxDiagram v.3.0.2 Professional

This software is covered by DHTMLX Commercial License.
Usage without proper license is prohibited.

(c) XB Software.

*/
if (window.dhx){ window.dhx_legacy = dhx; delete window.dhx; }(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["dhx"] = factory();
	else
		root["dhx"] = factory();
})(window, function() {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/codebase/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 91);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(Promise) {
Object.defineProperty(exports, "__esModule", { value: true });
var dom = __webpack_require__(42);
exports.el = dom.defineElement;
exports.sv = dom.defineSvgElement;
exports.view = dom.defineView;
exports.create = dom.createView;
exports.inject = dom.injectView;
exports.KEYED_LIST = dom.KEYED_LIST;
var svgTagName = ["animate", "animateMotion", "animateTransform", "circle", "clipPath", "color-profile", "defs", "desc", "discard", "ellipse", "feBlend", "feColorMatrix",
    "feComponentTransfer", "feComposite", "feConvolveMatrix", "feDiffuseLighting", "feDisplacementMap", "feDistantLight", "feDropShadow", "feFlood", "feFuncA", "feFuncB", "feFuncG", "feFuncR",
    "feGaussianBlur", "feImage", "feMerge", "feMergeNode", "feMorphology", "feOffset", "fePointLight", "feSpecularLighting", "feSpotLight", "feTile", "feTurbulence", "filter", "foreignObject", "g",
    "hatch", "hatchpath", "image", "line", "linearGradient", "marker", "mask", "mesh", "meshgradient", "meshpatch", "meshrow", "metadata", "mpath", "path", "pattern", "polygon", "polyline",
    "radialGradient", "rect", "script", "set", "solidcolor", "stop", "style", "svg", "switch", "symbol", "text", "textPath", "title", "tspan", "unknown", "use", "view"];
function disableHelp() {
    dom.DEVMODE.mutations = false;
    dom.DEVMODE.warnings = false;
    dom.DEVMODE.verbose = false;
    dom.DEVMODE.UNKEYED_INPUT = false;
}
exports.disableHelp = disableHelp;
function resizer(handler) {
    var resize = window.ResizeObserver;
    var activeHandler = function (node) {
        var height = node.el.offsetHeight;
        var width = node.el.offsetWidth;
        handler(width, height);
    };
    if (resize) {
        return exports.el("div.dhx-resize-observer", {
            _hooks: {
                didInsert: function (node) {
                    new resize(function () { return activeHandler(node); }).observe(node.el);
                },
            },
        });
    }
    return exports.el("iframe.dhx-resize-observer", {
        _hooks: {
            didInsert: function (node) {
                node.el.contentWindow.onresize = function () { return activeHandler(node); };
                activeHandler(node);
            },
        },
    });
}
exports.resizer = resizer;
function xmlToJson(xml) {
    var obj = {};
    if (xml.nodeType === 1) {
        if (xml.attributes.length > 0) {
            obj["@attributes"] = {};
            for (var j = 0; j < xml.attributes.length; j++) {
                var attribute = xml.attributes.item(j);
                obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
            }
        }
    }
    else if (xml.nodeType === 3) {
        obj = xml.nodeValue;
    }
    if (xml.hasChildNodes()) {
        for (var i = 0; i < xml.childNodes.length; i++) {
            var item = xml.childNodes.item(i);
            var nodeName = item.nodeName;
            if (typeof (obj[nodeName]) === "undefined") {
                obj[nodeName] = xmlToJson(item);
            }
            else {
                if (typeof (obj[nodeName].push) === "undefined") {
                    var old = obj[nodeName];
                    obj[nodeName] = [];
                    obj[nodeName].push(old);
                }
                obj[nodeName].push(xmlToJson(item));
            }
        }
    }
    return obj;
}
exports.xmlToJson = xmlToJson;
function jsonToVDOM(json) {
    var _a, _b;
    var tag = Object.keys(json)[0];
    var element = json[tag];
    var children = element["#text"] ? [element["#text"]] : [];
    for (var child in element) {
        if (element.hasOwnProperty(child) && child !== "@attributes" && child !== "#text") {
            if (Array.isArray(element[child])) {
                for (var t in element[child]) {
                    if (element[child].hasOwnProperty(t)) {
                        children.push(jsonToVDOM((_a = {}, _a[child] = element[child][t], _a)));
                    }
                }
            }
            else {
                children.push(jsonToVDOM((_b = {}, _b[child] = element[child], _b)));
            }
        }
    }
    if (svgTagName.indexOf(tag) !== -1) {
        return exports.sv(tag, element["@attributes"] ? element["@attributes"] : {}, children);
    }
    else {
        return exports.el(tag, element["@attributes"] ? element["@attributes"] : {}, children);
    }
}
exports.jsonToVDOM = jsonToVDOM;
function resizeHandler(container, handler) {
    return exports.create({
        render: function () {
            return resizer(handler);
        },
    }).mount(container);
}
exports.resizeHandler = resizeHandler;
function awaitRedraw() {
    return new Promise(function (res) {
        requestAnimationFrame(function () {
            res();
        });
    });
}
exports.awaitRedraw = awaitRedraw;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(8)))

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var html_1 = __webpack_require__(2);
var counter = new Date().valueOf();
function uid() {
    return "u" + counter++;
}
exports.uid = uid;
function extend(target, source, deep) {
    if (deep === void 0) { deep = true; }
    if (source) {
        for (var key in source) {
            var sobj = source[key];
            var tobj = target[key];
            if (sobj === undefined) {
                delete target[key];
            }
            else if (deep &&
                typeof tobj === "object" &&
                !(tobj instanceof Date) &&
                !(tobj instanceof Array)) {
                extend(tobj, sobj);
            }
            else {
                target[key] = sobj;
            }
        }
    }
    return target;
}
exports.extend = extend;
function copy(source, withoutInner) {
    var result = {};
    for (var key in source) {
        if (!withoutInner || !key.startsWith("$")) {
            result[key] = source[key];
        }
    }
    return result;
}
exports.copy = copy;
function naturalSort(arr) {
    return arr.sort(function (a, b) {
        var nn = typeof a === "string" ? a.localeCompare(b) : a - b;
        return nn;
    });
}
exports.naturalSort = naturalSort;
function findIndex(arr, predicate) {
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        if (predicate(arr[i])) {
            return i;
        }
    }
    return -1;
}
exports.findIndex = findIndex;
function isEqualString(from, to) {
    if (from.length > to.length) {
        return false;
    }
    for (var i = 0; i < from.length; i++) {
        if (from[i].toLowerCase() !== to[i].toLowerCase()) {
            return false;
        }
    }
    return true;
}
exports.isEqualString = isEqualString;
function singleOuterClick(fn) {
    var click = function (e) {
        if (fn(e)) {
            document.removeEventListener("click", click);
        }
    };
    document.addEventListener("click", click);
}
exports.singleOuterClick = singleOuterClick;
function detectWidgetClick(widgetId, cb) {
    var click = function (e) { return cb(html_1.locate(e, "dhx_widget_id") === widgetId); };
    document.addEventListener("click", click);
    return function () { return document.removeEventListener("click", click); };
}
exports.detectWidgetClick = detectWidgetClick;
function unwrapBox(box) {
    if (Array.isArray(box)) {
        return box[0];
    }
    return box;
}
exports.unwrapBox = unwrapBox;
function wrapBox(unboxed) {
    if (Array.isArray(unboxed)) {
        return unboxed;
    }
    return [unboxed];
}
exports.wrapBox = wrapBox;
function isDefined(some) {
    return some !== null && some !== undefined;
}
exports.isDefined = isDefined;
function range(from, to) {
    if (from > to) {
        return [];
    }
    var result = [];
    while (from <= to) {
        result.push(from++);
    }
    return result;
}
exports.range = range;
function isNumeric(val) {
    return !isNaN(val - parseFloat(val));
}
exports.isNumeric = isNumeric;
function downloadFile(data, filename, mimeType) {
    if (mimeType === void 0) { mimeType = "text/plain"; }
    var file = new Blob([data], { type: mimeType });
    if (window.navigator.msSaveOrOpenBlob) {
        // IE10+
        window.navigator.msSaveOrOpenBlob(file, filename);
    }
    else {
        var a_1 = document.createElement("a");
        var url_1 = URL.createObjectURL(file);
        a_1.href = url_1;
        a_1.download = filename;
        document.body.appendChild(a_1);
        a_1.click();
        setTimeout(function () {
            document.body.removeChild(a_1);
            window.URL.revokeObjectURL(url_1);
        }, 0);
    }
}
exports.downloadFile = downloadFile;
function debounce(func, wait, immediate) {
    var timeout;
    return function executedFunction() {
        var _this = this;
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var later = function () {
            timeout = null;
            if (!immediate) {
                func.apply(_this, args);
            }
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) {
            func.apply(this, args);
        }
    };
}
exports.debounce = debounce;
function compare(obj1, obj2) {
    for (var p in obj1) {
        if (obj1.hasOwnProperty(p) !== obj2.hasOwnProperty(p)) {
            return false;
        }
        switch (typeof obj1[p]) {
            case "object":
                if (!compare(obj1[p], obj2[p])) {
                    return false;
                }
                break;
            case "function":
                if (typeof obj2[p] === "undefined" ||
                    (p !== "compare" && obj1[p].toString() !== obj2[p].toString())) {
                    return false;
                }
                break;
            default:
                if (obj1[p] !== obj2[p]) {
                    return false;
                }
        }
    }
    for (var p in obj2) {
        if (typeof obj1[p] === "undefined") {
            return false;
        }
    }
    return true;
}
exports.compare = compare;
exports.isType = function (value) {
    var regex = /^\[object (\S+?)\]$/;
    var matches = Object.prototype.toString.call(value).match(regex) || [];
    return (matches[1] || "undefined").toLowerCase();
};
exports.isEmptyObj = function (obj) {
    for (var key in obj) {
        return false;
    }
    return true;
};


/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
function toNode(node) {
    return typeof node === "string"
        ? document.getElementById(node) || document.querySelector(node) || document.body
        : node || document.body;
}
exports.toNode = toNode;
function eventHandler(prepare, hash) {
    var keys = Object.keys(hash);
    return function (ev) {
        var data = prepare(ev);
        var node = ev.target;
        while (node) {
            var cssstring = node.getAttribute ? node.getAttribute("class") || "" : "";
            if (cssstring.length) {
                var css = cssstring.split(" ");
                for (var j = 0; j < keys.length; j++) {
                    if (css.includes(keys[j])) {
                        return hash[keys[j]](ev, data);
                    }
                }
            }
            node = node.parentNode;
        }
        return true;
    };
}
exports.eventHandler = eventHandler;
function locateNode(target, attr, dir) {
    if (attr === void 0) { attr = "dhx_id"; }
    if (dir === void 0) { dir = "target"; }
    if (target instanceof Event) {
        target = target[dir];
    }
    while (target) {
        if (target.getAttribute && target.getAttribute(attr)) {
            return target;
        }
        target = target.parentNode;
    }
}
exports.locateNode = locateNode;
function locate(target, attr) {
    if (attr === void 0) { attr = "dhx_id"; }
    var node = locateNode(target, attr);
    return node ? node.getAttribute(attr) : "";
}
exports.locate = locate;
function locateNodeByClassName(target, className) {
    if (target instanceof Event) {
        target = target.target;
    }
    while (target) {
        if (className) {
            if (target.classList && target.classList.contains(className)) {
                return target;
            }
        }
        else if (target.getAttribute && target.getAttribute("dhx_id")) {
            return target;
        }
        target = target.parentNode;
    }
}
exports.locateNodeByClassName = locateNodeByClassName;
function getBox(elem) {
    var box = elem.getBoundingClientRect();
    var body = document.body;
    var scrollTop = window.pageYOffset || body.scrollTop;
    var scrollLeft = window.pageXOffset || body.scrollLeft;
    var top = box.top + scrollTop;
    var left = box.left + scrollLeft;
    var right = body.offsetWidth - box.right;
    var bottom = body.offsetHeight - box.bottom;
    var width = box.right - box.left;
    var height = box.bottom - box.top;
    return { top: top, left: left, right: right, bottom: bottom, width: width, height: height };
}
exports.getBox = getBox;
var scrollWidth = -1;
function getScrollbarWidth() {
    if (scrollWidth > -1) {
        return scrollWidth;
    }
    var scrollDiv = document.createElement("div");
    document.body.appendChild(scrollDiv);
    scrollDiv.style.cssText = "position: absolute;left: -99999px;overflow:scroll;width: 100px;height: 100px;";
    scrollWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
    document.body.removeChild(scrollDiv);
    return scrollWidth;
}
exports.getScrollbarWidth = getScrollbarWidth;
function isIE() {
    var ua = window.navigator.userAgent;
    return ua.includes("MSIE ") || ua.includes("Trident/");
}
exports.isIE = isIE;
function getRealPosition(node) {
    var rects = node.getBoundingClientRect();
    return {
        left: rects.left + window.pageXOffset,
        right: rects.right + window.pageXOffset,
        top: rects.top + window.pageYOffset,
        bottom: rects.bottom + window.pageYOffset,
    };
}
exports.getRealPosition = getRealPosition;
function getWindowBorders() {
    return {
        rightBorder: window.pageXOffset + window.innerWidth,
        bottomBorder: window.pageYOffset + window.innerHeight,
    };
}
function horizontalCentering(pos, width, rightBorder) {
    var nodeWidth = pos.right - pos.left;
    var diff = (width - nodeWidth) / 2;
    var left = pos.left - diff;
    var right = pos.right + diff;
    if (left >= 0 && right <= rightBorder) {
        return left;
    }
    if (left < 0) {
        return 0;
    }
    return rightBorder - width;
}
function verticalCentering(pos, height, bottomBorder) {
    var nodeHeight = pos.bottom - pos.top;
    var diff = (height - nodeHeight) / 2;
    var top = pos.top - diff;
    var bottom = pos.bottom + diff;
    if (top >= 0 && bottom <= bottomBorder) {
        return top;
    }
    if (top < 0) {
        return 0;
    }
    return bottomBorder - height;
}
function placeBottomOrTop(pos, config) {
    var _a = getWindowBorders(), rightBorder = _a.rightBorder, bottomBorder = _a.bottomBorder;
    var left;
    var top;
    var bottomDiff = bottomBorder - pos.bottom - config.height;
    var topDiff = pos.top - config.height;
    if (config.mode === "bottom") {
        if (bottomDiff >= 0) {
            top = pos.bottom;
        }
        else if (topDiff >= 0) {
            top = topDiff;
        }
    }
    else {
        if (topDiff >= 0) {
            top = topDiff;
        }
        else if (bottomDiff >= 0) {
            top = pos.bottom;
        }
    }
    if (bottomDiff < 0 && topDiff < 0) {
        if (config.auto) {
            // eslint-disable-next-line @typescript-eslint/no-use-before-define
            return placeRightOrLeft(pos, __assign(__assign({}, config), { mode: "right", auto: false }));
        }
        top = bottomDiff > topDiff ? pos.bottom : topDiff;
    }
    if (config.centering) {
        left = horizontalCentering(pos, config.width, rightBorder);
    }
    else {
        var leftDiff = rightBorder - pos.left - config.width;
        var rightDiff = pos.right - config.width;
        if (leftDiff >= 0) {
            left = pos.left;
        }
        else if (rightDiff >= 0) {
            left = rightDiff;
        }
        else {
            left = rightDiff > leftDiff ? pos.left : rightDiff;
        }
    }
    return { left: left, top: top };
}
function placeRightOrLeft(pos, config) {
    var _a = getWindowBorders(), rightBorder = _a.rightBorder, bottomBorder = _a.bottomBorder;
    var left;
    var top;
    var rightDiff = rightBorder - pos.right - config.width;
    var leftDiff = pos.left - config.width;
    if (config.mode === "right") {
        if (rightDiff >= 0) {
            left = pos.right;
        }
        else if (leftDiff >= 0) {
            left = leftDiff;
        }
    }
    else {
        if (leftDiff >= 0) {
            left = leftDiff;
        }
        else if (rightDiff >= 0) {
            left = pos.right;
        }
    }
    if (leftDiff < 0 && rightDiff < 0) {
        if (config.auto) {
            return placeBottomOrTop(pos, __assign(__assign({}, config), { mode: "bottom", auto: false }));
        }
        left = leftDiff > rightDiff ? leftDiff : pos.right;
    }
    if (config.centering) {
        top = verticalCentering(pos, config.height, rightBorder);
    }
    else {
        var bottomDiff = pos.bottom - config.height;
        var topDiff = bottomBorder - pos.top - config.height;
        if (topDiff >= 0) {
            top = pos.top;
        }
        else if (bottomDiff > 0) {
            top = bottomDiff;
        }
        else {
            top = bottomDiff > topDiff ? bottomDiff : pos.top;
        }
    }
    return { left: left, top: top };
}
function calculatePosition(pos, config) {
    var _a = config.mode === "bottom" || config.mode === "top"
        ? placeBottomOrTop(pos, config)
        : placeRightOrLeft(pos, config), left = _a.left, top = _a.top;
    return {
        left: Math.round(left) + "px",
        top: Math.round(top) + "px",
        minWidth: Math.round(config.width) + "px",
        position: "absolute",
    };
}
exports.calculatePosition = calculatePosition;
function fitPosition(node, config) {
    return calculatePosition(getRealPosition(node), config);
}
exports.fitPosition = fitPosition;
function getStrSize(str, textStyle) {
    if (textStyle === void 0) { textStyle = {
        fontSize: "14px",
        fontFamily: "Arial",
        lineHeight: "14px",
        fontWeight: "normal",
        fontStyle: "normal",
    }; }
    var span = document.createElement("span");
    var fontSize = textStyle.fontSize, fontFamily = textStyle.fontFamily, lineHeight = textStyle.lineHeight, fontWeight = textStyle.fontWeight, fontStyle = textStyle.fontStyle;
    span.style.fontSize = fontSize;
    span.style.fontFamily = fontFamily;
    span.style.lineHeight = lineHeight;
    span.style.fontWeight = fontWeight;
    span.style.fontStyle = fontStyle;
    span.style.display = "inline-flex";
    span.innerText = str;
    document.body.appendChild(span);
    var offsetWidth = span.offsetWidth, clientHeight = span.clientHeight;
    document.body.removeChild(span);
    return { width: offsetWidth, height: clientHeight };
}
exports.getStrSize = getStrSize;
var checkCrossLink = function (sheet) { return sheet.href
    && sheet.ownerNode.outerHTML.indexOf(window.location.origin) === -1
    && (sheet.ownerNode.outerHTML.indexOf("http") !== -1
        || sheet.ownerNode.outerHTML.indexOf("https") !== -1
        || sheet.ownerNode.outerHTML.indexOf("href=\"//") !== -1); };
// Dirty (bad decision)
function getPageCss() {
    var css = [];
    for (var sheeti = 0; sheeti < document.styleSheets.length; sheeti++) {
        var sheet = document.styleSheets[sheeti];
        var rules = "cssRules" in sheet ? sheet.cssRules : sheet.rules;
        for (var rulei = 0; rulei < rules.length; rulei++) {
            var rule = rules[rulei];
            if ("cssText" in rule) {
                css.push(rule.cssText);
            }
            else {
                css.push(rule.selectorText + " {\n" + rule.style.cssText + "\n}\n");
            }
        }
    }
    return css.join("\n");
}
exports.getPageCss = getPageCss;


/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
exports.en = {
    applyAll: "Apply all",
    exportData: "Export Data",
    importData: "Import Data",
    resetChanges: "Reset Changes",
    autoLayout: "Auto Layout",
    arrange: "Arrange",
    position: "Position",
    size: "Size",
    color: "Color",
    title: "Title",
    text: "Text",
    image: "Image",
    fill: "Fill",
    textProps: "Text",
    stroke: "Stroke",
    gridStep: "Grid step",
    shapeSections: "Shapes",
    imageUpload: "Click to upload",
    emptyState: "Select a shape or a connector",
};
exports.default = exports.en;


/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var TreeFilterType;
(function (TreeFilterType) {
    TreeFilterType["all"] = "all";
    TreeFilterType["level"] = "level";
    TreeFilterType["leafs"] = "leafs";
})(TreeFilterType = exports.TreeFilterType || (exports.TreeFilterType = {}));
var DropPosition;
(function (DropPosition) {
    DropPosition["top"] = "top";
    DropPosition["bot"] = "bot";
    DropPosition["in"] = "in";
})(DropPosition = exports.DropPosition || (exports.DropPosition = {}));
var DataEvents;
(function (DataEvents) {
    DataEvents["afterAdd"] = "afteradd";
    DataEvents["beforeAdd"] = "beforeadd";
    DataEvents["removeAll"] = "removeall";
    DataEvents["beforeRemove"] = "beforeremove";
    DataEvents["afterRemove"] = "afterremove";
    DataEvents["change"] = "change";
    DataEvents["load"] = "load";
    DataEvents["loadError"] = "loaderror";
    DataEvents["beforeLazyLoad"] = "beforelazyload";
    DataEvents["afterLazyLoad"] = "afterlazyload";
})(DataEvents = exports.DataEvents || (exports.DataEvents = {}));
var DragEvents;
(function (DragEvents) {
    DragEvents["beforeDrag"] = "beforedrag";
    DragEvents["beforeDrop"] = "beforeDrop";
    DragEvents["dragStart"] = "dragstart";
    DragEvents["dragEnd"] = "dragend";
    DragEvents["canDrop"] = "candrop";
    DragEvents["cancelDrop"] = "canceldrop";
    DragEvents["dropComplete"] = "dropcomplete";
    DragEvents["dragOut"] = "dragOut";
    DragEvents["dragIn"] = "dragIn";
    DragEvents["beforeColumnDrag"] = "beforeColumnDrag";
    DragEvents["beforeColumnDrop"] = "beforeColumnDrop";
})(DragEvents = exports.DragEvents || (exports.DragEvents = {}));
var DataDriver;
(function (DataDriver) {
    DataDriver["json"] = "json";
    DataDriver["csv"] = "csv";
    DataDriver["xml"] = "xml";
})(DataDriver = exports.DataDriver || (exports.DataDriver = {}));


/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var dataproxy_1 = __webpack_require__(10);
var drivers_1 = __webpack_require__(27);
function isEqualObj(a, b) {
    for (var key in a) {
        if (a[key] !== b[key]) {
            return false;
        }
    }
    return true;
}
exports.isEqualObj = isEqualObj;
function naturalCompare(a, b) {
    if (isNaN(a) || isNaN(b)) {
        var ax_1 = [];
        var bx_1 = [];
        a.replace(/(\d+)|(\D+)/g, function (_, $1, $2) {
            ax_1.push([$1 || Infinity, $2 || ""]);
        });
        b.replace(/(\d+)|(\D+)/g, function (_, $1, $2) {
            bx_1.push([$1 || Infinity, $2 || ""]);
        });
        while (ax_1.length && bx_1.length) {
            var an = ax_1.shift();
            var bn = bx_1.shift();
            var nn = an[0] - bn[0] || an[1].localeCompare(bn[1]);
            if (nn) {
                return nn;
            }
        }
        return ax_1.length - bx_1.length;
    }
    return a - b;
}
exports.naturalCompare = naturalCompare;
function findByConf(item, conf) {
    if (typeof conf === "function") {
        if (conf.call(this, item)) {
            return item;
        }
    }
    else if (conf.by && conf.match) {
        if (item[conf.by] === conf.match) {
            return item;
        }
    }
}
exports.findByConf = findByConf;
function isDebug() {
    var dhx = window.dhx;
    if (typeof dhx !== "undefined") {
        return typeof dhx.debug !== "undefined" && dhx.debug;
    }
    // return typeof DHX_DEBUG_MODE !== "undefined" && DHX_DEBUG_MODE;
}
exports.isDebug = isDebug;
function dhxWarning(msg) {
    // tslint:disable-next-line:no-console
    console.warn(msg);
}
exports.dhxWarning = dhxWarning;
function dhxError(msg) {
    throw new Error(msg);
}
exports.dhxError = dhxError;
function toProxy(proxy) {
    var type = typeof proxy;
    if (type === "string") {
        return new dataproxy_1.DataProxy(proxy);
    }
    else if (type === "object") {
        return proxy;
    }
}
exports.toProxy = toProxy;
function toDataDriver(driver) {
    if (typeof driver === "string") {
        var dhx = window.dhx;
        var drivers = (dhx && dhx.dataDrivers) || drivers_1.dataDrivers;
        if (drivers[driver]) {
            return new drivers[driver]();
        }
        else {
            // tslint:disable-next-line:no-console
            console.warn("Incorrect data driver type:", driver);
            // tslint:disable-next-line:no-console
            console.warn("Available types:", JSON.stringify(Object.keys(drivers)));
        }
    }
    else if (typeof driver === "object") {
        return driver;
    }
}
exports.toDataDriver = toDataDriver;
function copyWithoutInner(obj, forbidden) {
    var result = {};
    for (var key in obj) {
        if (!key.startsWith("$") && (!forbidden || !forbidden[key])) {
            result[key] = obj[key];
        }
    }
    return result;
}
exports.copyWithoutInner = copyWithoutInner;
function isTreeCollection(obj) {
    // eslint-disable-next-line @typescript-eslint/unbound-method
    return Boolean(obj.getRoot);
}
exports.isTreeCollection = isTreeCollection;
function hasJsonOrArrayStructure(str) {
    if (typeof str === "object") {
        return true;
    }
    if (typeof str !== "string") {
        return false;
    }
    try {
        var result = JSON.parse(str);
        return Object.prototype.toString.call(result) === "[object Object]" || Array.isArray(result);
    }
    catch (err) {
        return false;
    }
}
exports.hasJsonOrArrayStructure = hasJsonOrArrayStructure;


/***/ }),
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
var dom_1 = __webpack_require__(0);
var en_1 = __webpack_require__(3);
exports.meta = {
    grid: {
        id: "gridStep",
        type: "inputsGroup",
        label: en_1.default.gridStep,
        validate: "number",
        options: [{ id: "step", value: 0, icon: function () { return dom_1.el(".dxi.dxi-grid-step"); }, validate: "number" }],
    },
    arrange: {
        id: "arrange",
        type: "inputsGroup",
        label: en_1.default.arrange,
        validate: "number",
        options: [
            { id: "x", value: "1000", label: "x", validate: "number" },
            { id: "y", value: "999", label: "y", validate: "number" },
            { id: "width", value: "1000", label: "w", validate: "number" },
            { id: "height", value: "999", label: "h", validate: "number" },
            {
                id: "angle",
                value: "999",
                label: "y",
                validate: "number",
                icon: function () { return dom_1.el(".dxi.dxi-rotate-right.rotate_icon"); },
            },
        ],
    },
    position: {
        id: "position",
        type: "inputsGroup",
        label: en_1.default.position,
        validate: "number",
        options: [
            { id: "dx", value: "1000", label: "dx", validate: "number" },
            { id: "dy", value: "999", label: "dy", validate: "number" },
        ],
    },
    size: {
        id: "size",
        type: "inputsGroup",
        label: en_1.default.size,
        options: [
            { id: "width", value: "1000", label: "w", validate: "number" },
            { id: "height", value: "999", label: "h", validate: "number" },
        ],
    },
    color: {
        id: "headerColor",
        type: "color",
        label: en_1.default.color,
    },
    title: {
        id: "title",
        type: "textarea",
        label: en_1.default.title,
    },
    text: {
        id: "text",
        type: "textarea",
        label: en_1.default.text,
    },
    img: {
        id: "img",
        type: "image",
        label: en_1.default.image,
    },
    fill: {
        id: "fill",
        type: "color",
        label: en_1.default.fill,
    },
    textProps: {
        id: "textProps",
        type: "textProps",
        label: en_1.default.textProps,
    },
    strokeProps: {
        id: "strokeProps",
        type: "stroke",
        label: en_1.default.stroke,
    },
};
function getMeta(properties) {
    return properties.map(function (config) {
        var type = config.type, label = config.label, property = config.property;
        return __assign(__assign({}, exports.meta[type]), { id: property || exports.meta[type].id, label: label || exports.meta[type].label });
    });
}
exports.getMeta = getMeta;


/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var EventSystem = /** @class */ (function () {
    function EventSystem(context) {
        this.events = {};
        this.context = context || this;
    }
    EventSystem.prototype.on = function (name, callback, context) {
        var event = name.toLowerCase();
        this.events[event] = this.events[event] || [];
        this.events[event].push({ callback: callback, context: context || this.context });
    };
    EventSystem.prototype.detach = function (name, context) {
        var event = name.toLowerCase();
        var eStack = this.events[event];
        if (context && eStack && eStack.length) {
            for (var i = eStack.length - 1; i >= 0; i--) {
                if (eStack[i].context === context) {
                    eStack.splice(i, 1);
                }
            }
        }
        else {
            this.events[event] = [];
        }
    };
    EventSystem.prototype.fire = function (name, args) {
        if (typeof args === "undefined") {
            args = [];
        }
        var event = name.toLowerCase();
        if (this.events[event]) {
            var res = this.events[event].map(function (e) { return e.callback.apply(e.context, args); });
            return !res.includes(false);
        }
        return true;
    };
    EventSystem.prototype.clear = function () {
        this.events = {};
    };
    return EventSystem;
}());
exports.EventSystem = EventSystem;
function EventsMixin(obj) {
    obj = obj || {};
    var eventSystem = new EventSystem(obj);
    obj.detachEvent = eventSystem.detach.bind(eventSystem);
    obj.attachEvent = eventSystem.on.bind(eventSystem);
    obj.callEvent = eventSystem.fire.bind(eventSystem);
}
exports.EventsMixin = EventsMixin;


/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global, setImmediate) {(function () {
  global = this

  var queueId = 1
  var queue = {}
  var isRunningTask = false

  if (!global.setImmediate)
    global.addEventListener('message', function (e) {
      if (e.source == global){
        if (isRunningTask)
          nextTick(queue[e.data])
        else {
          isRunningTask = true
          try {
            queue[e.data]()
          } catch (e) {}

          delete queue[e.data]
          isRunningTask = false
        }
      }
    })

  function nextTick(fn) {
    if (global.setImmediate) setImmediate(fn)
    // if inside of web worker
    else if (global.importScripts) setTimeout(fn)
    else {
      queueId++
      queue[queueId] = fn
      global.postMessage(queueId, '*')
    }
  }

  Deferred.resolve = function (value) {
    if (!(this._d == 1))
      throw TypeError()

    if (value instanceof Deferred)
      return value

    return new Deferred(function (resolve) {
        resolve(value)
    })
  }

  Deferred.reject = function (value) {
    if (!(this._d == 1))
      throw TypeError()

    return new Deferred(function (resolve, reject) {
        reject(value)
    })
  }

  Deferred.all = function (arr) {
    if (!(this._d == 1))
      throw TypeError()

    if (!(arr instanceof Array))
      return Deferred.reject(TypeError())

    var d = new Deferred()

    function done(e, v) {
      if (v)
        return d.resolve(v)

      if (e)
        return d.reject(e)

      var unresolved = arr.reduce(function (cnt, v) {
        if (v && v.then)
          return cnt + 1
        return cnt
      }, 0)

      if(unresolved == 0)
        d.resolve(arr)

      arr.map(function (v, i) {
        if (v && v.then)
          v.then(function (r) {
            arr[i] = r
            done()
            return r
          }, done)
      })
    }

    done()

    return d
  }

  Deferred.race = function (arr) {
    if (!(this._d == 1))
      throw TypeError()

    if (!(arr instanceof Array))
      return Deferred.reject(TypeError())

    if (arr.length == 0)
      return new Deferred()

    var d = new Deferred()

    function done(e, v) {
      if (v)
        return d.resolve(v)

      if (e)
        return d.reject(e)

      var unresolved = arr.reduce(function (cnt, v) {
        if (v && v.then)
          return cnt + 1
        return cnt
      }, 0)

      if(unresolved == 0)
        d.resolve(arr)

      arr.map(function (v, i) {
        if (v && v.then)
          v.then(function (r) {
            done(null, r)
          }, done)
      })
    }

    done()

    return d
  }

  Deferred._d = 1


  /**
   * @constructor
   */
  function Deferred(resolver) {
    'use strict'
    if (typeof resolver != 'function' && resolver != undefined)
      throw TypeError()

    if (typeof this != 'object' || (this && this.then))
      throw TypeError()

    // states
    // 0: pending
    // 1: resolving
    // 2: rejecting
    // 3: resolved
    // 4: rejected
    var self = this,
      state = 0,
      val = 0,
      next = [],
      fn, er;

    self['promise'] = self

    self['resolve'] = function (v) {
      fn = self.fn
      er = self.er
      if (!state) {
        val = v
        state = 1

        nextTick(fire)
      }
      return self
    }

    self['reject'] = function (v) {
      fn = self.fn
      er = self.er
      if (!state) {
        val = v
        state = 2

        nextTick(fire)

      }
      return self
    }

    self['_d'] = 1

    self['then'] = function (_fn, _er) {
      if (!(this._d == 1))
        throw TypeError()

      var d = new Deferred()

      d.fn = _fn
      d.er = _er
      if (state == 3) {
        d.resolve(val)
      }
      else if (state == 4) {
        d.reject(val)
      }
      else {
        next.push(d)
      }

      return d
    }

    self['catch'] = function (_er) {
      return self['then'](null, _er)
    }

    var finish = function (type) {
      state = type || 4
      next.map(function (p) {
        state == 3 && p.resolve(val) || p.reject(val)
      })
    }

    try {
      if (typeof resolver == 'function')
        resolver(self['resolve'], self['reject'])
    } catch (e) {
      self['reject'](e)
    }

    return self

    // ref : reference to 'then' function
    // cb, ec, cn : successCallback, failureCallback, notThennableCallback
    function thennable (ref, cb, ec, cn) {
      // Promises can be rejected with other promises, which should pass through
      if (state == 2) {
        return cn()
      }
      if ((typeof val == 'object' || typeof val == 'function') && typeof ref == 'function') {
        try {

          // cnt protects against abuse calls from spec checker
          var cnt = 0
          ref.call(val, function (v) {
            if (cnt++) return
            val = v
            cb()
          }, function (v) {
            if (cnt++) return
            val = v
            ec()
          })
        } catch (e) {
          val = e
          ec()
        }
      } else {
        cn()
      }
    };

    function fire() {

      // check if it's a thenable
      var ref;
      try {
        ref = val && val.then
      } catch (e) {
        val = e
        state = 2
        return fire()
      }

      thennable(ref, function () {
        state = 1
        fire()
      }, function () {
        state = 2
        fire()
      }, function () {
        try {
          if (state == 1 && typeof fn == 'function') {
            val = fn(val)
          }

          else if (state == 2 && typeof er == 'function') {
            val = er(val)
            state = 1
          }
        } catch (e) {
          val = e
          return finish()
        }

        if (val == self) {
          val = TypeError()
          finish()
        } else thennable(ref, function () {
            finish(3)
          }, finish, function () {
            finish(state == 1 && 3)
          })

      })
    }


  }

  // Export our library object, either for node.js or as a globally scoped variable
  if (true) {
    module['exports'] = Deferred
  } else {}
})()

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(17), __webpack_require__(39).setImmediate))

/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var core_1 = __webpack_require__(1);
var html_1 = __webpack_require__(2);
var View = /** @class */ (function () {
    function View(_container, config) {
        this._uid = core_1.uid();
        this.config = config || {};
    }
    View.prototype.mount = function (container, vnode) {
        if (vnode) {
            this._view = vnode;
        }
        if (container && this._view && this._view.mount) {
            // init view inside of HTML container
            this._container = html_1.toNode(container);
            if (this._container.tagName) {
                this._view.mount(this._container);
            }
            else if (this._container.attach) {
                this._container.attach(this);
            }
        }
    };
    View.prototype.unmount = function () {
        var rootView = this.getRootView();
        if (rootView && rootView.node) {
            rootView.unmount();
            this._view = null;
        }
    };
    View.prototype.getRootView = function () {
        return this._view;
    };
    View.prototype.getRootNode = function () {
        return this._view && this._view.node && this._view.node.el;
    };
    View.prototype.paint = function () {
        if (this._view && // was mounted
            (this._view.node || // already rendered node
                this._container)) {
            // not rendered, but has container
            this._doNotRepaint = false;
            this._view.redraw();
        }
    };
    return View;
}());
exports.View = View;
function toViewLike(view) {
    return {
        getRootView: function () { return view; },
        paint: function () { return view.node && view.redraw(); },
        mount: function (container) { return view.mount(container); },
    };
}
exports.toViewLike = toViewLike;


/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var ajax_1 = __webpack_require__(18);
var DataProxy = /** @class */ (function () {
    function DataProxy(url, config) {
        this.url = this._url = url;
        this.config = config;
    }
    DataProxy.prototype.updateUrl = function (url, params) {
        if (params === void 0) { params = {}; }
        this._url = this.url = url || this._url;
        this.url += "?";
        for (var param in params) {
            this.config[param] = params[param];
            this.url += param + "=" + encodeURIComponent(params[param]) + "&";
        }
        this.url = this.url.slice(0, -1);
    };
    DataProxy.prototype.load = function () {
        return ajax_1.ajax.get(this.url, null, { responseType: "text" });
    };
    DataProxy.prototype.save = function (data, mode) {
        switch (mode) {
            case "delete":
                return ajax_1.ajax.delete(this.url, data);
            case "update":
            case "insert":
            default:
                return ajax_1.ajax.post(this.url, data);
        }
    };
    return DataProxy;
}());
exports.DataProxy = DataProxy;


/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var baseMetaInfo_1 = __webpack_require__(6);
var BaseShape = /** @class */ (function () {
    function BaseShape(config, parameters) {
        this.config = this.setDefaults(config, parameters.defaults);
        this.id = config.id;
        if (config.width) {
            config.width = parseFloat(config.width);
        }
        if (config.height) {
            config.height = parseFloat(config.height);
        }
        if (config.x) {
            config.x = parseFloat(config.x);
        }
        if (config.y) {
            config.y = parseFloat(config.y);
        }
        if (config.strokeWidth) {
            config.strokeWidth = parseFloat(config.strokeWidth);
        }
    }
    BaseShape.prototype.isConnector = function () {
        return false;
    };
    BaseShape.prototype.canResize = function () {
        return true;
    };
    BaseShape.prototype.getCenter = function () {
        var config = this.config;
        return {
            x: Math.abs(config.width / 2),
            y: Math.abs(config.height / 2),
        };
    };
    BaseShape.prototype.getBox = function () {
        var config = this.config;
        var left = config.x + (config.dx || 0);
        var right = left + config.width;
        var top = config.y + (config.dy || 0);
        var bottom = top + config.height;
        return { left: left, right: right, top: top, bottom: bottom };
    };
    BaseShape.prototype.getMetaInfo = function () {
        return [baseMetaInfo_1.meta.text];
    };
    BaseShape.prototype.move = function (x, y) {
        this.update({ x: x, y: y });
    };
    BaseShape.prototype.resize = function (width, height) {
        this.update({ width: width, height: height });
    };
    BaseShape.prototype.rotate = function (angle) {
        this.update({ angle: angle });
    };
    BaseShape.prototype.update = function (config) {
        for (var key in config) {
            this.config[key] = config[key];
            if (this.config.id) {
                this.id = this.config.id;
            }
        }
    };
    BaseShape.prototype.render = function () {
        return "";
    };
    BaseShape.prototype.getPoint = function (x, y) {
        var config = this.config;
        if (config.angle) {
            var cx = config.x + config.width / 2;
            var cy = config.y + config.height / 2;
            var angleRad = config.angle * (Math.PI / 180); // from degrees to radians
            return {
                x: (x - cx) * Math.cos(angleRad) - (y - cy) * Math.sin(angleRad) + cx,
                y: (x - cx) * Math.sin(angleRad) + (y - cy) * Math.cos(angleRad) + cy,
            };
        }
        return { x: x, y: y };
    };
    BaseShape.prototype.setCss = function (value) {
        this.config.css = value;
    };
    BaseShape.prototype.getCss = function () {
        return ((this.config.$selected
            ? "dhx_selected "
            : this.config.$blockSelected
                ? "dhx_blockselected "
                : "") + (this.config.css || ""));
    };
    BaseShape.prototype.setDefaults = function (config, defaults) {
        return config;
    };
    BaseShape.prototype.getCoords = function (config) {
        var x = config.x, y = config.y;
        if (config.dx) {
            x = config.x + config.dx;
        }
        if (config.dy) {
            y = config.y + config.dy;
        }
        return { x: x, y: y };
    };
    return BaseShape;
}());
exports.BaseShape = BaseShape;


/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var types_1 = __webpack_require__(25);
exports.SelectionEvents = types_1.SelectionEvents;
var ts_data_1 = __webpack_require__(15);
exports.DataEvents = ts_data_1.DataEvents;
var DiagramEvents;
(function (DiagramEvents) {
    DiagramEvents["scroll"] = "scroll";
    DiagramEvents["beforeCollapse"] = "beforecollapse";
    DiagramEvents["afterCollapse"] = "aftercollapse";
    DiagramEvents["beforeExpand"] = "beforeexpand";
    DiagramEvents["afterExpand"] = "afterexpand";
    DiagramEvents["shapeMouseDown"] = "shapemousedown";
    DiagramEvents["shapeClick"] = "shapeclick";
    DiagramEvents["shapedDblClick"] = "shapedblclick";
    DiagramEvents["shapeIconClick"] = "shapeiconclick";
    DiagramEvents["beforeRender"] = "beforerender";
    DiagramEvents["shapeHover"] = "shapeHover";
    DiagramEvents["emptyAreaClick"] = "emptyAreaClick";
    DiagramEvents["emptyAreaMouseDown"] = "emptyAreaMouseDown";
    DiagramEvents["lineClick"] = "lineClick";
})(DiagramEvents = exports.DiagramEvents || (exports.DiagramEvents = {}));


/***/ }),
/* 13 */,
/* 14 */,
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

function __export(m) {
    for (var p in m) if (!exports.hasOwnProperty(p)) exports[p] = m[p];
}
Object.defineProperty(exports, "__esModule", { value: true });
__export(__webpack_require__(4));
__export(__webpack_require__(26));
__export(__webpack_require__(50));
__export(__webpack_require__(51));
__export(__webpack_require__(10));
__export(__webpack_require__(53));
__export(__webpack_require__(5));
__export(__webpack_require__(29));
__export(__webpack_require__(28));
__export(__webpack_require__(54));
__export(__webpack_require__(27));
__export(__webpack_require__(18));


/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var dom_1 = __webpack_require__(0);
function getCircleTpl(config) {
    if ((!config.$count && config.open !== false) || !config.$kids) {
        return "";
    }
    var slim = config.dir === "vertical";
    var hide = config.open === false;
    var hW = config.width / 2;
    var hH = config.height / 2;
    var coords = {
        x: slim ? 0 : hW,
        y: slim ? hH : config.height,
    };
    return dom_1.el("div", {
        dhx_diagram: "hide",
        class: hide ? "dhx_expand_icon" : "dhx_hide_icon",
        style: {
            position: "absolute",
            top: coords.y,
            left: coords.x,
        },
    }, [
        dom_1.el("div.dhx_icon-container", {
            style: {
                background: config.$expandColor,
            },
        }, [
            dom_1.el("i.dxi", {
                class: hide ? " dxi-plus" : " dxi-minus",
            }),
        ]),
    ]);
}
exports.getCircleTpl = getCircleTpl;
function getHeaderTpl(config) {
    var width = config.width;
    var height = 4;
    var color = config.headerColor || "#20b6e2";
    return dom_1.el("div", {
        class: "dhx_item_header",
        style: {
            height: height,
            width: width,
            top: 0,
            left: 0,
            position: "absolute",
            background: color,
        },
    });
}
exports.getHeaderTpl = getHeaderTpl;
function getTextTemplate(config, content) {
    var width = config.width, height = config.height;
    if (typeof config.text === "string" || typeof config.title === "string") {
        return dom_1.el("div.shape_content-container", {
            style: {
                width: width,
                height: height,
                top: 0,
                left: 0,
                overflow: "hidden",
                transform: "translate(0 0)",
                position: "absolute",
            },
        }, [
            dom_1.el("div", {
                class: "shape_content",
                style: {
                    width: config.width,
                    height: config.height,
                    wordBreak: "break-word",
                    whiteSpace: "pre-wrap",
                    overflow: "hidden",
                },
            }, content),
        ]);
    }
    return null;
}
exports.getTextTemplate = getTextTemplate;
function getShapeCss(config) {
    var verticalAlign = {
        bottom: "flex-end",
        top: "flex-start",
        center: "center",
    };
    return {
        width: config.width,
        height: config.height,
        display: "flex",
        "flex-direction": "column",
        "justify-content": verticalAlign[config.textVerticalAlign],
        "text-align": config.textAlign,
        "line-height": config.lineHeight,
        "font-size": config.fontSize,
        "font-style": config.fontStyle,
        "font-weight": config.fontWeight,
        color: config.fontColor,
        "word-break": "break-word",
        "white-space": "pre-wrap",
    };
}
exports.getShapeCss = getShapeCss;


/***/ }),
/* 17 */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(Promise) {
Object.defineProperty(exports, "__esModule", { value: true });
var types_1 = __webpack_require__(4);
var helpers_1 = __webpack_require__(5);
function toQueryString(data) {
    return Object.keys(data)
        .reduce(function (entries, key) {
        var value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
        entries.push(key + "=" + encodeURIComponent(value));
        return entries;
    }, [])
        .join("&");
}
function inferResponseType(contentType) {
    if (!contentType) {
        return "text";
    }
    if (contentType.includes("json")) {
        return "json";
    }
    if (contentType.includes("xml")) {
        return "xml";
    }
    return "text";
}
function send(url, data, method, headers, responseType) {
    function parseResponse(responseText, genResponseType) {
        switch (genResponseType) {
            case "json": {
                return JSON.parse(responseText);
            }
            case "text": {
                return responseText;
            }
            case "xml": {
                var driver = helpers_1.toDataDriver(types_1.DataDriver.xml);
                if (driver) {
                    return driver.toJsonObject(responseText);
                }
                else {
                    return { parseError: "Incorrect data driver type: 'xml'" };
                }
            }
            default: {
                return responseText;
            }
        }
    }
    var allHeaders = headers || {};
    if (responseType) {
        allHeaders.Accept = "application/" + responseType;
    }
    if (method !== "GET") {
        allHeaders["Content-Type"] = allHeaders["Content-Type"] || "application/json";
    }
    if (method === "GET") {
        var urlData = data && typeof data === "object"
            ? toQueryString(data)
            : data && typeof data === "string"
                ? data
                : "";
        if (urlData) {
            url += !url.includes("?") ? "?" : "&";
            url += urlData;
        }
        data = null;
    }
    if (!window.fetch) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (responseType === "raw") {
                        resolve({
                            url: xhr.responseURL,
                            headers: xhr
                                .getAllResponseHeaders()
                                .trim()
                                .split(/[\r\n]+/)
                                .reduce(function (acc, cur) {
                                var kv = cur.split(": ");
                                acc[kv[0]] = kv[1];
                                return acc;
                            }, {}),
                            body: xhr.response,
                        });
                    }
                    if (xhr.status === 204) {
                        resolve();
                    }
                    else {
                        resolve(parseResponse(xhr.responseText, responseType || inferResponseType(xhr.getResponseHeader("Content-Type"))));
                    }
                }
                else {
                    reject({
                        status: xhr.status,
                        statusText: xhr.statusText,
                    });
                }
            };
            xhr.onerror = function () {
                reject({
                    status: xhr.status,
                    statusText: xhr.statusText,
                    message: xhr.responseText,
                });
            };
            xhr.open(method, url);
            for (var headerKey in allHeaders) {
                xhr.setRequestHeader(headerKey, allHeaders[headerKey]);
            }
            switch (method) {
                case "POST":
                case "DELETE":
                case "PUT":
                    xhr.send(data !== undefined ? JSON.stringify(data) : "");
                    break;
                case "GET":
                    xhr.send();
                    break;
                default:
                    xhr.send();
                    break;
            }
        });
    }
    else {
        return window
            .fetch(url, {
            method: method,
            body: data ? JSON.stringify(data) : null,
            headers: allHeaders,
        })
            .then(function (response) {
            if (response.ok) {
                var genResponseType = responseType || inferResponseType(response.headers.get("Content-Type"));
                if (genResponseType === "raw") {
                    return {
                        // eslint-disable-next-line @typescript-eslint/ban-ts-ignore
                        // @ts-ignore
                        headers: Object.fromEntries(response.headers.entries()),
                        url: response.url,
                        body: response.body,
                    };
                }
                if (response.status !== 204) {
                    switch (genResponseType) {
                        case "json": {
                            return response.json();
                        }
                        case "xml": {
                            var driver_1 = helpers_1.toDataDriver(types_1.DataDriver.xml);
                            if (driver_1) {
                                return response.text().then(function (xmlData) { return driver_1.toJsonObject(xmlData); });
                            }
                            else {
                                return response.text();
                            }
                        }
                        default:
                            return response.text();
                    }
                }
            }
            else {
                return response.text().then(function (message) {
                    return Promise.reject({
                        status: response.status,
                        statusText: response.statusText,
                        message: message,
                    });
                });
            }
        });
    }
}
exports.ajax = {
    get: function (url, data, config) {
        return send(url, data, "GET", config && config.headers, config !== undefined ? config.responseType : undefined);
    },
    post: function (url, data, config) {
        return send(url, data, "POST", config && config.headers, config !== undefined ? config.responseType : undefined);
    },
    put: function (url, data, config) {
        return send(url, data, "PUT", config && config.headers, config !== undefined ? config.responseType : undefined);
    },
    delete: function (url, data, config) {
        return send(url, data, "DELETE", config && config.headers, config !== undefined ? config.responseType : undefined);
    },
};

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(8)))

/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Graph = /** @class */ (function () {
    function Graph(parent, root, config) {
        this._tbounds = [0, 0];
        if (!parent) {
            this.nodes = [];
            this.hash = {};
        }
        else {
            this.nodes = [root];
            parent.collectSubNodes(root, this.nodes);
            this.hash = parent.hash;
            this.root(root, config);
        }
    }
    Graph.prototype.copy = function () {
        var _this = this;
        var t = new Graph();
        t.nodes = this.nodes.map(function (_a) {
            var id = _a.id, w = _a.w, h = _a.h, x = _a.x, y = _a.y, isn = _a.isn, iss = _a.iss;
            return ({
                id: id,
                w: w,
                h: h,
                x: x,
                y: y,
                kids: [],
                links: [],
                isn: isn,
                iss: iss,
            });
        });
        t.hash = {};
        t.nodes.forEach(function (a) {
            t.hash[a.id] = a;
        });
        t.nodes.forEach(function (a) {
            a.kids = _this.hash[a.id].kids.map(function (b) { return t.hash[b.id]; });
            a.links = _this.hash[a.id].links.map(function (b) { return t.hash[b.id]; });
        });
        t._tbounds = [].concat(this._tbounds);
        t._ybounds = [].concat(this._ybounds);
        t._bounds = this._bounds ? this._bounds.map(function (a) { return [].concat(a); }) : [];
        t.routes = this.routes;
        t._root = this._root ? t.hash[this._root.id] : null;
        return t;
    };
    Graph.prototype.collectSubNodes = function (root, backet) {
        for (var i = 0; i < root.kids.length; i++) {
            var e = root.kids[i];
            backet.push(e);
            if (e.kids)
                this.collectSubNodes(e, backet);
        }
    };
    Graph.prototype.getLevelBounds = function () {
        return this._bounds;
    };
    Graph.prototype.getBounds = function (i, padding, wide) {
        var b;
        if (wide) {
            b = this._tbounds;
        }
        else {
            b = this._bounds[i];
        }
        return [b[0] ? b[0] - padding / 4 : 0, b[1] ? b[1] + padding / 4 : 0];
    };
    Graph.prototype.addEdge = function (from, to) {
        var f = this.hash[from];
        var t = this.hash[to];
        f.links.push(t);
        t.links.push(f);
    };
    Graph.prototype.importNodes = function (node) {
        var _this = this;
        node.forEach(function (a) {
            _this.hash[a.id] = a;
            _this.nodes.push(a);
        });
    };
    Graph.prototype.split = function (filter) {
        var _this = this;
        var next = [];
        this.nodes = this.nodes.filter(function (a) {
            if (!filter(a))
                return true;
            delete _this.hash[a.id];
            next.push(a);
            return false;
        });
        if (!next.length)
            return null;
        var g2 = new Graph();
        g2.importNodes(next);
        return g2;
    };
    Graph.prototype.addNode = function (node) {
        node.kids = [];
        node.links = [];
        if (!this._root)
            this._root = node;
        this.hash[node.id] = node;
        this.nodes.push(node);
    };
    Graph.prototype.getNode = function (id) {
        return this.hash[id];
    };
    Graph.prototype.getRoot = function () {
        return this._root;
    };
    Graph.prototype.getLevels = function () {
        return this._deep;
    };
    Graph.prototype.clean = function () {
        this.nodes.forEach(function (a) {
            if (a.links.length > 1) {
                var were_1 = [];
                a.links = a.links.filter(function (b) {
                    if (were_1.includes(b) || a === b) {
                        return false;
                    }
                    were_1.push(b);
                    return true;
                });
            }
        });
    };
    Graph.prototype.getNodes = function () {
        return this.nodes;
    };
    Graph.prototype.getBox = function () {
        return [this._tbounds, this._ybounds];
    };
    Graph.prototype.setGlobalBox = function () {
        if (!this.nodes.length) {
            return;
        }
        var cmx, cx, cmy, cy;
        cmx = cmy = Infinity;
        cx = cy = -Infinity;
        this.nodes.forEach(function (a) {
            var mx = a.x - a.w / 2;
            var x = a.x + a.w / 2;
            var my = a.y - a.h / 2;
            var y = a.y + a.h / 2;
            if (mx < cmx)
                cmx = mx;
            if (my < cmy)
                cmy = my;
            if (x > cx)
                cx = x;
            if (y > cy)
                cy = y;
        });
        this._tbounds = [cmx, cx];
        this._ybounds = [cmy, cy];
    };
    Graph.prototype.translate = function (p) {
        this.nodes.forEach(function (n) {
            n.x += p.x;
            n.y += p.y;
        });
        this._tbounds[0] += p.x;
        this._tbounds[1] += p.x;
        if (this._bounds)
            for (var i = 0; i < this._bounds.length; i++) {
                this._bounds[i][0] += p.x;
                this._bounds[i][1] += p.x;
            }
        if (this._ybounds) {
            this._ybounds[0] += p.y;
            this._ybounds[1] += p.y;
        }
    };
    Graph.prototype.rotate = function (a) {
        // [x: sin, b: cos ]
        var sin = a.x;
        var cos = a.y;
        this.nodes.forEach(function (n) {
            var x = n.x * cos - n.y * sin;
            var y = n.x * sin + n.y * cos;
            n.x = x;
            n.y = y;
        });
        var _a = this._tbounds, xmin = _a[0], xmax = _a[1];
        var _b = this._ybounds, ymin = _b[0], ymax = _b[1];
        this._tbounds = [xmin * cos - ymin * sin, xmax * cos - ymax * sin].sort();
        this._ybounds = [xmin * sin + ymin * cos, xmax * sin + ymax * cos].sort();
    };
    Graph.prototype.setBox = function () {
        var last = this._deep[this._deep.length - 1];
        var max = -Infinity;
        last.forEach(function (a) {
            var test = a.y + a.h / 2;
            if (test > max)
                max = test;
        });
        this._ybounds = [this._root.y - this._root.h / 2, max];
    };
    Graph.prototype.mirror = function () {
        this.nodes.forEach(function (n) {
            n.x = -n.x;
        });
        for (var i = 0; i < this._bounds.length; i++) {
            var _a = this._bounds[i], l_1 = _a[0], u_1 = _a[1];
            this._bounds[i] = [-u_1, -l_1];
        }
        var _b = this._tbounds, l = _b[0], u = _b[1];
        this._tbounds = [-u, -l];
    };
    Graph.prototype.nonLeaves = function (n) {
        return n.kids.filter(function (a) { return a.kids.length > 0; });
    };
    Graph.prototype.toTree = function (root) {
        if (!root || !this.hash[root.id])
            root = this._detectRoot();
        this.nodes.forEach(function (a) { return (a.kids = []); });
        this.setKids(root);
        return root;
    };
    Graph.prototype.root = function (root, config) {
        this._deep = [[root]];
        var hw = Math.round((config && config.rotate ? root.h : root.w) / 2);
        this._tbounds = [-hw, hw];
        this._bounds = [[-hw, hw]];
        this._leaves = [];
        this._root = root;
        this._setLevels(root, 1);
        this._width = 1;
        for (var i = 0; i < this._deep.length; i++) {
            var n = this._deep[i].length;
            if (n > this._width)
                this._width = n;
        }
    };
    Graph.prototype.setKids = function (root) {
        var _this = this;
        root.links.forEach(function (a) {
            if (!a.kids.length) {
                root.kids.push(a);
                _this.setKids(a);
            }
        });
    };
    Graph.prototype._detectRoot = function () {
        var ext = {};
        var nodes = this.nodes;
        var round = [];
        do {
            round.forEach(function (k) { return (ext[k] = 1); });
            round = [];
            nodes = nodes.filter(function (a) {
                var check = a.links.map(function (a) { return (ext[a.id] ? 0 : 1); }).reduce(function (a, b) { return a + b; }, 0) > 1;
                if (!check)
                    round.push(a.id);
                return check;
            });
        } while (nodes.length > 2 && round.length);
        return nodes[0] || this.nodes[0];
    };
    Graph.prototype._setLevels = function (node, l) {
        var kids = node.kids;
        for (var i = 0; i < kids.length; i++) {
            var n = kids[i];
            var level = this._deep[l];
            if (!level) {
                this._deep[l] = [n];
                this._bounds[l] = [0, 0];
            }
            else
                level.push(n);
            if (n.kids.length)
                this._setLevels(n, l + 1);
            else
                this._leaves.push(n);
        }
    };
    Graph.prototype.getIString = function () {
        var levels = [];
        // mark leaves
        for (var i = 0; i < this._leaves.length; i++) {
            this._leaves[i].isn = 0;
            this._leaves[i].iss = "";
        }
        // for each level
        for (var i = this._deep.length - 2; i >= 0; i--) {
            // for each non leave
            var level = this._deep[i].filter(function (a) { return a.kids.length > 0; });
            for (var j = 0; j < level.length; j++) {
                // create i-string for all non-leaves
                var node = level[j];
                for (var k = 0; k < node.kids.length; k++) {
                    node.iss = node.kids
                        .map(function (n) { return n.isn; })
                        .sort()
                        .join(",");
                }
            }
            // sort non-leaves to form level i-string
            level.sort(function (a, b) { return (a.iss > b.iss ? 1 : -1); });
            levels.push(level.map(function (a) { return a.iss; }).join("|"));
            // // sort all nodes by i-string
            // const all = this._deep[i];
            // all.sort((a,b) => (a.iss > b.iss ? 1 : -1));
            // assign i-numbers based on i-strings
            var code = level[0].iss;
            var num = 1;
            for (var j = 0; j < level.length; j++) {
                if (code !== level[j].iss) {
                    code = level[j].iss;
                    num++;
                }
                level[j].isn = num;
            }
        }
        // return tree i-string
        return levels.join(";");
    };
    return Graph;
}());
exports.default = Graph;


/***/ }),
/* 20 */,
/* 21 */,
/* 22 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
/* eslint-disable @typescript-eslint/no-use-before-define */
var dom_1 = __webpack_require__(0);
var baseMetaInfo_1 = __webpack_require__(6);
var templates_1 = __webpack_require__(16);
var Base_1 = __webpack_require__(11);
var en_1 = __webpack_require__(3);
var DiagramFlowShape = /** @class */ (function (_super) {
    __extends(DiagramFlowShape, _super);
    function DiagramFlowShape(config, parameters) {
        var _this = _super.call(this, config, parameters) || this;
        _this.config = config;
        _this.id = _this.config.id;
        return _this;
    }
    DiagramFlowShape.prototype.getMetaInfo = function () {
        return baseMetaInfo_1.getMeta([
            { type: "grid", label: en_1.default.gridStep },
            { type: "arrange", label: en_1.default.arrange },
            { type: "fill", label: en_1.default.fill },
            { type: "text", label: en_1.default.text },
            { type: "strokeProps", label: en_1.default.stroke },
            { type: "textProps", label: en_1.default.textProps },
        ]);
    };
    DiagramFlowShape.prototype.render = function () {
        if (this.config.strokeType) {
            if (this.config.strokeType === "dash") {
                this.config.strokeDash = "5,5";
            }
            if (this.config.strokeType === "none") {
                this.config.stroke = "none";
            }
        }
        var _a = this.config, id = _a.id, angle = _a.angle, width = _a.width, height = _a.height, strokeWidth = _a.strokeWidth;
        var coords = this.getCoords(this.config);
        var strokeRatio = strokeWidth / 2;
        return dom_1.el("div", {
            _key: id,
            class: "dhx_diagram_flow_item " + this.getCss(),
            dhx_id: id,
            style: {
                transform: "rotate(" + (angle || 0) + "deg)",
                zIndex: this.config.$selected ? 1 : 0,
                position: "absolute",
                height: height,
                width: width,
                top: coords.y,
                left: coords.x,
            },
        }, [
            dom_1.sv("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                height: height,
                width: width,
                viewBox: -strokeRatio + " " + -strokeRatio + " " + (width + strokeRatio) + " " + (height +
                    strokeRatio),
                reserveAspectRatio: "none",
            }, [this._getShapeContour()]),
            templates_1.getTextTemplate(this.config, this.getContent()),
            templates_1.getCircleTpl(this.config),
        ]);
    };
    DiagramFlowShape.prototype.setDefaults = function (config, defaults) {
        var width = config.width, height = config.height, stroke = config.stroke, extraLinesStroke = config.extraLinesStroke, fill = config.fill, strokeWidth = config.strokeWidth, fontColor = config.fontColor, strokeDash = config.strokeDash, textAlign = config.textAlign, lineHeight = config.lineHeight, fontStyle = config.fontStyle, textVerticalAlign = config.textVerticalAlign, type = config.type, fontSize = config.fontSize, text = config.text, preview = config.preview, x = config.x, y = config.y;
        var linesStroke = type === "roll" ? "#DEDEDE" : extraLinesStroke || "#FFF";
        config.extraLinesStroke = defaults.extraLinesStroke || linesStroke;
        var circularShapes = ["circle", "or", "junction"];
        var isCircular = circularShapes.includes(type);
        var widthDefaut = defaults.width ? parseFloat(defaults.width) : isCircular ? 90 : 140;
        var heightDefault = defaults.height ? parseFloat(defaults.height) : 90;
        var lineHeightDefault = defaults.lineHeight ? parseFloat(defaults.lineHeight) : 14;
        var fontSizeDefault = defaults.fontSize ? parseFloat(defaults.fontSize) : 14;
        var strokeWidthDefault = defaults.strokeWidth ? parseFloat(defaults.strokeWidth) : 1;
        config.strokeWidth = strokeWidth || strokeWidthDefault;
        config.width = width || widthDefaut;
        config.height = height || heightDefault;
        config.fontSize = fontSize || fontSizeDefault;
        config.lineHeight = lineHeight || lineHeightDefault;
        config.strokeDash = strokeDash || defaults.strokeDash || "";
        config.fill = fill || defaults.fill || "#DEDEDE";
        config.stroke = stroke || defaults.stroke || "#DEDEDE";
        config.text = typeof text === "string" ? text : defaults.text || "";
        config.textAlign = textAlign || defaults.textAlign || "center";
        config.textVerticalAlign = textVerticalAlign || defaults.textVerticalAlign || "center";
        config.fontStyle = fontStyle || defaults.fontStyle || "normal";
        config.fontColor = fontColor || defaults.fontColor || "#4C4C4C";
        config.preview = preview || defaults.preview;
        config.x = x || 0;
        config.y = y || 0;
        return config;
    };
    DiagramFlowShape.prototype.getContent = function () {
        return [
            dom_1.el("div", {
                class: "shape_content",
                style: templates_1.getShapeCss(this.config),
            }, this.config.text),
        ];
    };
    DiagramFlowShape.prototype._getShapeContour = function () {
        var _a = this.config, width = _a.width, height = _a.height, stroke = _a.stroke, fill = _a.fill, strokeWidth = _a.strokeWidth, strokeDash = _a.strokeDash, extraLinesStroke = _a.extraLinesStroke, type = _a.type;
        var shape = exports.flowShapes[type];
        var part = Math.round(width / 12);
        var pathWidth = width - strokeWidth / 2;
        var pathHeight = height - strokeWidth / 2;
        var path = shape.path(pathWidth, pathHeight, part);
        var additionalPath = shape.additionalPath(pathWidth, pathHeight, part);
        var getPathElement = function (d) {
            return dom_1.sv("path", {
                d: d,
                class: "dhx_diagram_flow_shape dhx_item_shape ",
                stroke: stroke,
                fill: fill,
                "stroke-width": strokeWidth,
                "stroke-dasharray": strokeDash,
            });
        };
        var getAdditionalPathElement = function (d) {
            return dom_1.sv("path", {
                d: d,
                fill: "none",
                stroke: extraLinesStroke,
                class: "dhx_diagram_extra_lines",
            });
        };
        return [getPathElement(path), getAdditionalPathElement(additionalPath)];
    };
    return DiagramFlowShape;
}(Base_1.BaseShape));
exports.DiagramFlowShape = DiagramFlowShape;
exports.flowShapes = {
    circle: {
        path: function (width, height) {
            return "\n\t\t\tM " + width / 2 + " 0 A " + height / 2 + "," + height / 2 + " 0 1 0 " + width / 2 + "," + height + "\n\t\t\tA " + height / 2 + "," + height / 2 + " 0 1 0 " + width / 2 + ",0 Z";
        },
        additionalPath: function () {
            return;
        },
    },
    rectangle: {
        path: function (width, height) {
            return "M 0,0 L 0," + height + " L " + width + "," + height + " L " + width + ",0 Z";
        },
        additionalPath: function () {
            return;
        },
    },
    triangle: {
        path: function (width, height) {
            return "M " + width / 2 + " 0 L" + width + " " + height + " L 0 " + height + " z";
        },
        additionalPath: function () {
            return;
        },
    },
    start: {
        path: function (width, height) {
            return "\n\t\t\tM " + height / 2 + " 0 A " + height / 2 + "," + height / 2 + " 0 1 0 " + height / 2 + "," + height + "\n\t\t\tH " + (width - height / 2) + " A " + height / 2 + "," + height / 2 + " 0 1 0 " + (width - height / 2) + ",0 H " + height / 2 + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    end: {
        path: function (width, height) {
            return "\n\t\t\tM " + height / 2 + " 0 A " + height / 2 + "," + height / 2 + " 0 1 0 " + height / 2 + "," + height + "\n\t\t\tH " + (width - height / 2) + " A " + height / 2 + "," + height / 2 + " 0 1 0 " + (width - height / 2) + ",0 H " + height / 2 + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    process: {
        path: function (width, height) {
            return "M 0,0 L 0," + height + " L " + width + "," + height + " L " + width + ",0 Z";
        },
        additionalPath: function () {
            return;
        },
    },
    output: {
        path: function (width, height, part) {
            return "M " + part * 2 + ",0 " + width + ",0 " + (width - part * 2) + "," + height + " 0," + height + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    decision: {
        path: function (width, height) {
            return "M 0 " + height / 2 + " L " + width / 2 + " 0 L " + width + " " + height / 2 + " L " + width / 2 + " " + height + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    display: {
        path: function (width, height) {
            return "\n\t\t\tM 0 " + height / 2 + " L " + width / 4 + " 0 H " + (width * 3) / 4 + "\n\t\t\tA " + width / 4 + "," + height / 2 + " 0 1 1 " + (width * 3) / 4 + "," + height + "\n\t\t\tH " + width / 4 + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    document: {
        path: function (width, height) {
            height -= 8;
            return "M0 " + height + " C\n\t\t\t" + width / 6 + " " + (height * 10) / 9 + ",\n\t\t\t" + width / 3 + " " + (height * 10) / 9 + ",\n\t\t\t" + width / 2 + " " + height + " S\n\t\t\t" + (width * 5) / 6 + " " + (height * 8) / 9 + ",\n\t\t\t" + width + " " + height + "\n\t\t\tV 0 H 0 Z";
        },
        additionalPath: function () {
            return;
        },
    },
    data: {
        path: function (width, height, part) {
            return "M " + part + " 0 Q\n\t\t\t" + -part + " " + height / 2 + ",\n\t\t\t" + part + " " + height + " H " + width + " Q\n\t\t\t" + (width - part * 2) + " " + height / 2 + ",\n\t\t\t" + width + " 0 Z";
        },
        additionalPath: function () {
            return;
        },
    },
    database: {
        path: function (width, height, part) {
            return "M 0 " + part + " A " + width / 2 + "," + part + " 0 1 0 " + width + "," + part + "\n\t\t\tA " + width / 2 + "," + part + " 0 1 0 0," + part + "\n\t\t\tV " + height + " H " + width + " V " + part;
        },
        additionalPath: function (width, height, part) {
            return "M 0 " + part + " A " + width / 2 + "," + part + " 0 1 0 " + width + "," + part;
        },
    },
    internal: {
        path: function (width, height) {
            return "M 0,0 L 0," + height + " L " + width + "," + height + " L " + width + ",0 Z";
        },
        additionalPath: function (width, height, part) {
            return "M " + part + " 0 V " + height + " M 0 " + part + " H " + width;
        },
    },
    offline: {
        path: function (width, height) {
            return "M 0,0 " + width + ",0 " + width / 2 + "," + height + " Z";
        },
        additionalPath: function (width, height, part) {
            var hypotenuse = Math.sqrt(Math.pow((width / 2), 2) + Math.pow(height, 2));
            var coef = height / hypotenuse;
            var cathet = Math.sqrt(Math.pow((part / coef), 2) - Math.pow(part, 2));
            return "M " + (width / 2 - cathet) + " " + (height - part) + " h " + 2 * cathet;
        },
    },
    delay: {
        path: function (width, height) {
            return "\n\t\t\tM 0 0 H " + (width * 3) / 4 + "\n\t\t\tA " + width / 4 + "," + height / 2 + " 0 1 1 " + (width * 3) / 4 + "," + height + "\n\t\t\tH 0 Z";
        },
        additionalPath: function () {
            return;
        },
    },
    page: {
        path: function (width, height) {
            return "\n\t\t\tM 0,0\n\t\t\t" + width + ",0\n\t\t\t" + width + "," + height / 2 + "\n\t\t\t" + width / 2 + "," + height + "\n\t\t\t0," + height / 2 + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    input: {
        path: function (width, height) {
            return "\n\t\t\tM 0," + height / 3 + "\n\t\t\t" + width + "," + 0 + "\n\t\t\t" + width + "," + height + "\n\t\t\t0," + height + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    operation: {
        path: function (width, height) {
            return "\n\t\t\tM 0,0\n\t\t\t" + width + ",0\n\t\t\t" + (width * 3) / 4 + "," + height + "\n\t\t\t" + width / 4 + "," + height + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    punchcard: {
        path: function (width, height) {
            return "\n\t\t\tM 0," + height / 4 + "\n\t\t\t" + width / 4 + ",0\n\t\t\t" + width + ",0\n\t\t\t" + width + "," + height + "\n\t\t\t0," + height + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    subroutine: {
        path: function (width, height) {
            return "M 0,0 L 0," + height + " L " + width + "," + height + " L " + width + ",0 Z";
        },
        additionalPath: function (width, height, part) {
            return "M " + part + " 0 V " + height + " M " + (width - part) + " 0 V " + height;
        },
    },
    comment: {
        path: function (width, height) {
            var w = 4;
            return "M " + width + " 0 H 0 V " + height + " H" + width + " V" + (height - w) + " H" + w + " V" + w + " H" + width;
        },
        additionalPath: function () {
            return;
        },
    },
    storage: {
        path: function (width, height) {
            return "M 0,0 " + width + ",0 " + width / 2 + "," + height + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    extract: {
        path: function (width, height) {
            return "M 0," + height + " " + width + "," + height + " " + width / 2 + ",0 Z";
        },
        additionalPath: function () {
            return;
        },
    },
    collate: {
        path: function (width, height) {
            return "M " + width / 2 + " " + height / 2 + " L 0 0 H " + width + " L 0 " + height + " H " + width + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    or: {
        path: function (width, height) {
            return "\n\t\t\tM " + width / 2 + " 0 A " + height / 2 + "," + height / 2 + " 0 1 0 " + width / 2 + "," + height + "\n\t\t\tA " + height / 2 + "," + height / 2 + " 0 1 0 " + width / 2 + ",0 Z";
        },
        additionalPath: function (width, height) {
            return "\n\t\t\tM" + (width - height) / 2 + " " + height / 2 + " " + (width - (width - height) / 2) + " " + height / 2 + "\n\t\t\tM" + width / 2 + " " + 0 + " " + width / 2 + " " + height;
        },
    },
    junction: {
        path: function (width, height) {
            return "\n\t\t\tM " + width / 2 + " 0 A " + height / 2 + "," + height / 2 + " 0 1 0 " + width / 2 + "," + height + "\n\t\t\tA " + height / 2 + "," + height / 2 + " 0 1 0 " + width / 2 + ",0 Z";
        },
        additionalPath: function (width, height) {
            return "\n\t\t\tM " + (width / 2 - (height * Math.SQRT2) / 4) + " " + (height / 2 - (height * Math.SQRT2) / 4) + " L " + (width / 2 +
                (height * Math.SQRT2) / 4) + " " + (height / 2 + (height * Math.SQRT2) / 4) + "\n\t\t\tM " + (width / 2 - (height * Math.SQRT2) / 4) + " " + (height / 2 + (height * Math.SQRT2) / 4) + " L " + (width / 2 +
                (height * Math.SQRT2) / 4) + " " + (height / 2 - (height * Math.SQRT2) / 4);
        },
    },
    keyring: {
        path: function (width, height, part) {
            return "\n\t\t\tM " + part + " 0 A " + part + "," + height / 2 + " 0 1 0 " + part + "," + height + "\n\t\t\tH " + (width - part) + " A " + part + "," + height / 2 + " 0 1 0 " + (width - part) + ",0 H " + part + " Z";
        },
        additionalPath: function () {
            return;
        },
    },
    tape: {
        path: function (width, height) {
            return "\n\t\t\tM0 " + (height - 12) + " C\n\t\t\t" + width / 6 + " " + ((height - 8) * 10) / 9 + ",\n\t\t\t" + width / 3 + " " + ((height - 8) * 10) / 9 + ",\n\t\t\t" + width / 2 + " " + (height - 8) + " S\n\t\t\t" + (width * 5) / 6 + " " + ((height - 8) * 8) / 9 + ",\n\t\t\t" + width + " " + height + "\n\t\t\tV 12 C\n\t\t\t" + (width * 5) / 6 + " " + -height / 9 + ",\n\t\t\t" + (width * 2) / 3 + " " + height / 9 / 2 + ",\n\t\t\t" + width / 2 + " 8 S\n\t\t\t" + width / 6 + " " + height / 9 + ",\n\t\t\t0 0 Z";
        },
        additionalPath: function () {
            return;
        },
    },
    preparation: {
        path: function (width, height) {
            var w = 20;
            return "M0 " + height / 2 + "L" + w + " 0H" + (width - w) + "L " + width + " " + height / 2 + "L" + (width -
                w) + " " + height + "H" + w + "L0 " + height / 2 + "Z";
        },
        additionalPath: function () {
            return;
        },
    },
    endpoint: {
        path: function (width, height) {
            return "M0 " + height / 2 + "  L" + width / 2 + " " + 0 + " L " + width / 2 + " " + height + " z";
        },
        additionalPath: function () {
            return;
        },
    },
    roll: {
        path: function (width, height) {
            return "\n\t\t\tM " + width / 2 + " 0 A " + height / 2 + "," + height / 2 + " 0 1 0 " + width / 2 + "," + height + "\n\t\t\tA " + height / 2 + "," + height / 2 + " 0 1 0 " + width / 2 + ",0 Z";
        },
        additionalPath: function (width, height) {
            return "M " + width / 2 + " " + height + " H " + width;
        },
    },
};


/***/ }),
/* 23 */,
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
function getConnectionPoint(shape, gap, side) {
    switch (side) {
        case "top":
            return shape.$shape.getPoint(shape.x + shape.width / 2, shape.y - gap);
        case "bottom":
            return shape.$shape.getPoint(shape.x + shape.width / 2, shape.y + shape.height + gap);
        case "left":
            return shape.$shape.getPoint(shape.x - gap, shape.y + shape.height / 2);
        case "right":
            return shape.$shape.getPoint(shape.x + shape.width + gap, shape.y + shape.height / 2);
        case "center":
            return shape.$shape.getPoint(shape.x + shape.width / 2, shape.y + shape.height / 2);
    }
}
function getLength(from, to) {
    var x = to.x - from.x;
    var y = to.y - from.y;
    return Math.sqrt(x * x + y * y);
}
function getRoundedCorners(na, nb, aa, bb, turn, radius) {
    if (radius === void 0) { radius = 10; }
    if (!turn) {
        var byY = aa.y === bb.y ? +radius : 0;
        var byX = aa.x === bb.x ? +radius : 0;
        return [
            na,
            { x1: aa.x, y1: aa.y, x: aa.x + byY, y: aa.y + byX },
            { x: bb.x - byY, y: bb.y - byX },
            { x1: bb.x, y1: bb.y, x: nb.x, y: nb.y },
        ];
    }
    var bdX = bb.x < turn.x ? -1 : 1;
    var bdY = bb.y < turn.y ? -1 : 1;
    var adX = aa.x > turn.x ? 1 : -1;
    var adY = aa.y > turn.y ? 1 : -1;
    var before = __assign({}, turn);
    var after = __assign({}, bb);
    var curvedTurn = {
        x1: turn.x,
        y1: turn.y,
        x: turn.x,
        y: turn.y + radius * bdY,
    };
    if (aa.x === turn.x) {
        before.y += radius * adY;
        after.x -= radius * bdX;
        curvedTurn = {
            x1: turn.x,
            y1: turn.y,
            x: turn.x + radius * bdX,
            y: turn.y,
        };
    }
    if (aa.y === turn.y) {
        before.x += radius * adX;
        after.y -= radius * bdY;
        curvedTurn = {
            x1: turn.x,
            y1: turn.y,
            x: turn.x,
            y: turn.y + radius * bdY,
        };
    }
    return [na, aa, before, curvedTurn, after, { x1: bb.x, y1: bb.y, x: nb.x, y: nb.y }];
}
function getCurvedLine(na, nb, turn) {
    return [na, { x1: turn.x, y1: turn.y, x: nb.x, y: nb.y }];
}
var SIDES = ["top", "bottom", "right", "left", "center"];
function getRoutePoints(link, from, to, gap, customGap, fromSide, toSide) {
    if (fromSide === void 0) { fromSide = ""; }
    if (toSide === void 0) { toSide = ""; }
    // start points from shape border
    var borderFrom;
    var borderTo;
    // start points with gaps
    var gapFrom;
    var gapTo;
    var toGapValue = customGap && customGap > gap ? customGap : gap || 0;
    if (fromSide === "center" && toSide === "center") {
        return [
            { x: from.x + from.width / 2, y: from.y + from.height / 2 },
            { x: to.x + to.width / 2, y: to.y + to.height / 2 },
        ];
    }
    if (fromSide) {
        borderFrom = getConnectionPoint(from, 0, fromSide);
        gapFrom = getConnectionPoint(from, gap, fromSide);
    }
    if (toSide) {
        borderTo = getConnectionPoint(to, 0, toSide);
        gapTo = getConnectionPoint(to, toGapValue, toSide);
    }
    if (!fromSide || !toSide) {
        var closestPoints = SIDES.map(function (sideFrom) {
            var fromPoint = getConnectionPoint(from, gap, sideFrom);
            return SIDES.map(function (sideTo) {
                var toPoint = getConnectionPoint(to, toGapValue, sideTo);
                var distance = getLength(fromPoint, toPoint);
                return [fromPoint, toPoint, distance, sideFrom, sideTo];
            }).sort(function (l, r) { return l[2] - r[2]; })[0];
        }).sort(function (l, r) { return l[2] - r[2]; })[0];
        gapFrom = closestPoints[0];
        gapTo = closestPoints[1];
        fromSide = closestPoints[3];
        toSide = closestPoints[4];
        link.fromSide = fromSide;
        link.toSide = toSide;
        borderFrom = getConnectionPoint(from, 0, fromSide);
        borderTo = getConnectionPoint(to, 0, toSide);
    }
    var sidesDistance = (fromSide === "bottom" && toSide === "top") || (fromSide === "top" && toSide === "bottom")
        ? Math.abs(borderTo.y - borderFrom.y)
        : 0;
    sidesDistance =
        sidesDistance ||
            ((fromSide === "left" && toSide === "right") || (fromSide === "right" && toSide === "left")
                ? Math.abs(borderTo.x - borderFrom.x)
                : 0);
    // if we reached the gap limit
    if (sidesDistance > 0 && toGapValue + gap >= sidesDistance) {
        toGapValue = sidesDistance - gap;
        gapTo = getConnectionPoint(to, toGapValue, toSide);
    }
    var points = [];
    // preffer simple line form
    if (gapFrom.x === gapTo.x || gapFrom.y === gapTo.y) {
        if ((borderFrom.x === gapFrom.x && gapFrom.x === borderTo.x) ||
            (borderFrom.y === gapFrom.y && gapFrom.y === borderTo.y)) {
            // straight line
            points = [borderFrom, borderTo];
        }
        else {
            // line without central turn point
            points = [borderFrom, gapFrom, gapTo, borderTo];
            if (link.cornersRadius && link.connectType !== "straight") {
                points = getRoundedCorners(borderFrom, borderTo, gapFrom, gapTo, null, link.cornersRadius);
            }
        }
    }
    else {
        // most complex line form, with central turn
        var isLeftCollision = gapFrom.x < borderFrom.x && gapFrom.x < gapTo.x;
        var isBottomCollision = gapFrom.y > borderFrom.y && gapFrom.y > gapTo.y;
        var turn = borderFrom.x !== gapFrom.x && !isLeftCollision
            ? { x: gapTo.x, y: gapFrom.y }
            : { x: gapFrom.x, y: gapTo.y };
        turn = isBottomCollision ? { x: gapTo.x, y: gapFrom.y } : turn;
        if (link.connectType === "curved") {
            points = getCurvedLine(borderFrom, borderTo, turn);
        }
        else if (link.cornersRadius && link.connectType !== "straight") {
            points = getRoundedCorners(borderFrom, borderTo, gapFrom, gapTo, turn, link.cornersRadius);
        }
        else {
            points = [borderFrom, gapFrom, turn, gapTo, borderTo];
        }
    }
    return points;
}
// For Diagram.ts!!!
function nearestLinkPath(link, from, to, config) {
    if (!from || !to) {
        return;
    }
    var points = getRoutePoints(link, from, to, config.lineGap, link.customGap, link.fromSide, link.toSide);
    if (link.connectType === "straight") {
        return (link.points = [points[0], points[points.length - 1]]);
    }
    if (link.points) {
        // without additional points
        if (link.points.length === points.length) {
            link.points = link.points.map(function (p, i) {
                if (p && points[i] && !p.$custom) {
                    return points[i];
                }
                return p;
            });
        }
        else {
            var custom = link.points.filter(function (p) { return p.$custom; });
            link.points = custom.length ? link.points : points;
        }
        if (!link.$move) {
            link.points[0] = points[0];
            link.points[link.points.length - 1] = points[points.length - 1];
        }
    }
    else {
        link.points = points;
    }
}
exports.nearestLinkPath = nearestLinkPath;
// For placement helper
function directLinkPath(link, from, to, config) {
    if (!link) {
        return;
    }
    var x1 = from.x + (from.dx || 0);
    var y1 = from.y + (from.dy || 0);
    var x2 = to.x + (to.dx || 0);
    var y2 = to.y + (to.dy || 0);
    if (from.dir === "vertical") {
        // from right-middle to right middle
        var sx = x1;
        var sy = Math.round(y1 + from.height / 2);
        var ex = x2;
        var ey = Math.round(y2 + to.height / 2);
        var gap = -Math.round(config.margin.itemX / 2) + 0.5;
        link.points = [
            { x: sx, y: sy },
            { x: sx + gap, y: sy },
            { x: sx + gap, y: ey },
            { x: ex, y: ey },
        ];
    }
    else {
        // from bottom-center to top-center
        var sx = Math.round(x1 + from.width / 2);
        var sy = y1 + from.height;
        var ex = Math.round(x2 + to.width / 2);
        var ey = y2;
        var gap = Math.round(config.margin.itemY / 2) + 0.5;
        link.points = [
            { x: sx, y: sy },
            { x: sx, y: sy + gap },
            { x: ex, y: sy + gap },
            { x: ex, y: ey },
        ];
    }
}
exports.directLinkPath = directLinkPath;


/***/ }),
/* 25 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SelectionEvents;
(function (SelectionEvents) {
    SelectionEvents["beforeUnSelect"] = "beforeunselect";
    SelectionEvents["afterUnSelect"] = "afterunselect";
    SelectionEvents["beforeSelect"] = "beforeselect";
    SelectionEvents["afterSelect"] = "afterselect";
})(SelectionEvents = exports.SelectionEvents || (exports.SelectionEvents = {}));


/***/ }),
/* 26 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
var events_1 = __webpack_require__(7);
var loader_1 = __webpack_require__(46);
var sort_1 = __webpack_require__(49);
var dataproxy_1 = __webpack_require__(10);
var helpers_1 = __webpack_require__(5);
var types_1 = __webpack_require__(4);
var core_1 = __webpack_require__(1);
var DataCollection = /** @class */ (function () {
    function DataCollection(config, events) {
        this.config = config || {};
        this._order = [];
        this._pull = {};
        this._changes = { order: [] };
        this._initOrder = null;
        this._sort = new sort_1.Sort();
        this._loader = new loader_1.Loader(this, this._changes);
        this.events = events || new events_1.EventSystem(this);
        this.events.on(types_1.DataEvents.loadError, function (response) {
            if (typeof response !== "string") {
                helpers_1.dhxError(response);
            }
            else {
                helpers_1.dhxWarning(response);
            }
        });
    }
    DataCollection.prototype.add = function (obj, index) {
        var _this = this;
        if (!this.events.fire(types_1.DataEvents.beforeAdd, [obj])) {
            return;
        }
        if (Array.isArray(obj)) {
            return obj.map(function (element, key) {
                if (key !== 0) {
                    index = index + 1;
                }
                return _this._add(element, index);
            });
        }
        else {
            return this._add(obj, index);
        }
    };
    DataCollection.prototype.remove = function (id) {
        var _this = this;
        if (id) {
            if (id instanceof Array) {
                id.map(function (elementId) {
                    _this._remove(elementId);
                });
            }
            else {
                this._remove(id);
            }
        }
    };
    DataCollection.prototype.removeAll = function () {
        this._removeAll();
        this.events.fire(types_1.DataEvents.removeAll);
        this.events.fire(types_1.DataEvents.change);
    };
    DataCollection.prototype.exists = function (id) {
        return !!this._pull[id];
    };
    DataCollection.prototype.getNearId = function (id) {
        var item = this._pull[id];
        if (!item) {
            return this._order[0].id || "";
        }
    };
    DataCollection.prototype.getItem = function (id) {
        return this._pull[id];
    };
    DataCollection.prototype.update = function (id, obj, silent) {
        var item = this.getItem(id);
        if (item) {
            if (helpers_1.isEqualObj(obj, item)) {
                return;
            }
            if (obj.id && id !== obj.id) {
                helpers_1.dhxWarning("this method doesn't allow change id");
                if (helpers_1.isDebug()) {
                    // eslint-disable-next-line no-debugger
                    debugger;
                }
            }
            else {
                core_1.extend(this._pull[id], obj, false);
                if (this.config.update) {
                    this.config.update(this._pull[id]);
                }
                if (!silent) {
                    this._onChange("update", id, this._pull[id]);
                }
            }
        }
        else {
            helpers_1.dhxWarning("item not found");
        }
    };
    DataCollection.prototype.getIndex = function (id) {
        if (!id) {
            return -1;
        }
        var res = core_1.findIndex(this._order, function (item) { return item.id.toString() === id.toString(); });
        if (this._pull[id] && res >= 0) {
            return res;
        }
    };
    DataCollection.prototype.getId = function (index) {
        if (!this._order[index]) {
            return;
        }
        return this._order[index].id;
    };
    DataCollection.prototype.getLength = function () {
        return this._order.length;
    };
    DataCollection.prototype.isDataLoaded = function (from, to) {
        if (from === void 0) { from = 0; }
        if (to === void 0) { to = this._order.length; }
        if (core_1.isNumeric(from) && core_1.isNumeric(to)) {
            return this._order.slice(from, to).filter(function (item) { return item.$empty; }).length === 0;
        }
        return !this.find(function (item) { return item.$empty; });
    };
    DataCollection.prototype.filter = function (rule, config) {
        if (!this.isDataLoaded()) {
            helpers_1.dhxWarning("the method doesn't work with lazyLoad");
            return;
        }
        config = core_1.extend({
            add: false,
            multiple: true,
        }, config);
        if (!config.add) {
            this._order = this._initOrder || this._order;
            this._initOrder = null;
        }
        this._filters = this._filters || {};
        if (!config.multiple || !rule) {
            this._filters = {};
        }
        if (rule) {
            if (typeof rule === "function") {
                var f = "_";
                this._filters[f] = {
                    match: f,
                    compare: rule,
                };
            }
            else {
                if (!rule.match) {
                    delete this._filters[rule.by];
                }
                else {
                    rule.compare = rule.compare || (function (val, match) { return val === match; });
                    this._filters[rule.by] = rule;
                }
            }
            this._applyFilters();
        }
        this.events.fire(types_1.DataEvents.change);
    };
    DataCollection.prototype.find = function (conf) {
        for (var key in this._pull) {
            var res = helpers_1.findByConf(this._pull[key], conf);
            if (res) {
                return res;
            }
        }
        return null;
    };
    DataCollection.prototype.findAll = function (conf) {
        var res = [];
        for (var key in this._pull) {
            var item = helpers_1.findByConf(this._pull[key], conf);
            if (item) {
                res.push(item);
            }
        }
        return res;
    };
    DataCollection.prototype.sort = function (by) {
        if (!this.isDataLoaded()) {
            helpers_1.dhxWarning("the method doesn't work with lazyLoad");
            return;
        }
        if (!by) {
            this._order = [];
            for (var key in this._pull) {
                this._order.push(this._pull[key]);
            }
            this._applyFilters();
        }
        else {
            this._sort.sort(this._order, by);
            if (this._initOrder && this._initOrder.length) {
                this._sort.sort(this._initOrder, by);
            }
        }
        this.events.fire(types_1.DataEvents.change);
    };
    DataCollection.prototype.copy = function (id, index, target, targetId) {
        var _this = this;
        if (id instanceof Array) {
            return id.map(function (elementId, key) {
                return _this._copy(elementId, index, target, targetId, key);
            });
        }
        else {
            return this._copy(id, index, target, targetId);
        }
    };
    DataCollection.prototype.move = function (id, index, target, targetId) {
        var _this = this;
        if (id instanceof Array) {
            return id.map(function (elementId, key) {
                return _this._move(elementId, index, target, targetId, key);
            });
        }
        else {
            return this._move(id, index, target, targetId);
        }
    };
    DataCollection.prototype.forEach = function (cb) {
        for (var i = 0; i < this._order.length; i++) {
            cb.call(this, this._order[i], i, this._order);
        }
    };
    DataCollection.prototype.load = function (url, driver) {
        if (typeof url === "string") {
            this.dataProxy = url = new dataproxy_1.DataProxy(url);
        }
        this.dataProxy = url;
        return this._loader.load(url, driver);
    };
    DataCollection.prototype.parse = function (data, driver) {
        this._removeAll();
        return this._loader.parse(data, driver);
    };
    DataCollection.prototype.$parse = function (data) {
        var apx = this.config.approximate;
        if (apx) {
            data = this._approximate(data, apx.value, apx.maxNum);
        }
        this._parse_data(data);
        this.events.fire(types_1.DataEvents.change, ["load"]);
        this.events.fire(types_1.DataEvents.load);
    };
    DataCollection.prototype.save = function (url) {
        this._loader.save(url);
    };
    DataCollection.prototype.changeId = function (id, newId, silent) {
        if (newId === void 0) { newId = core_1.uid(); }
        if (!silent && !this.isDataLoaded()) {
            helpers_1.dhxWarning("the method doesn't work with lazyLoad");
            return;
        }
        var item = this.getItem(id);
        if (!item) {
            helpers_1.dhxWarning("item not found");
        }
        else {
            item.id = newId;
            core_1.extend(this._pull[id], item);
            this._pull[newId] = this._pull[id];
            if (!silent) {
                this._onChange("update", newId, this._pull[newId]);
            }
            delete this._pull[id];
        }
    };
    // todo: loop through the array and check saved statuses
    DataCollection.prototype.isSaved = function () {
        return !this._changes.order.length; // todo: bad solution, errors and holded elments are missed...
    };
    DataCollection.prototype.map = function (cb) {
        var result = [];
        for (var i = 0; i < this._order.length; i++) {
            result.push(cb.call(this, this._order[i], i, this._order));
        }
        return result;
    };
    DataCollection.prototype.mapRange = function (from, to, cb) {
        if (from < 0) {
            from = 0;
        }
        if (to > this._order.length - 1) {
            to = this._order.length - 1;
        }
        var arr = this._order.slice(from, to);
        var result = [];
        for (var i = from; i <= to; i++) {
            result.push(cb.call(this, this._order[i], i, arr));
        }
        return result;
    };
    DataCollection.prototype.reduce = function (cb, acc) {
        for (var i = 0; i < this._order.length; i++) {
            acc = cb.call(this, acc, this._order[i], i);
        }
        return acc;
    };
    DataCollection.prototype.serialize = function (driver) {
        if (driver === void 0) { driver = types_1.DataDriver.json; }
        // remove $ attrs
        var data = this.map(function (item) {
            var newItem = __assign({}, item);
            Object.keys(newItem).forEach(function (key) {
                if (key.startsWith("$")) {
                    delete newItem[key];
                }
            });
            return newItem;
        });
        var dataDriver = helpers_1.toDataDriver(driver);
        if (dataDriver) {
            return dataDriver.serialize(data);
        }
    };
    DataCollection.prototype.getInitialData = function () {
        return this._initOrder;
    };
    DataCollection.prototype._add = function (obj, index) {
        if (!this.isDataLoaded()) {
            helpers_1.dhxWarning("the method doesn't work with lazyLoad");
            return;
        }
        var id = this._addCore(obj, index);
        this._onChange("add", obj.id, obj);
        this.events.fire(types_1.DataEvents.afterAdd, [obj]);
        return id;
    };
    DataCollection.prototype._remove = function (id) {
        if (!this.isDataLoaded()) {
            helpers_1.dhxWarning("the method doesn't work with lazyLoad");
            return;
        }
        var obj = this._pull[id];
        if (obj) {
            if (!this.events.fire(types_1.DataEvents.beforeRemove, [obj])) {
                return;
            }
            this._removeCore(obj.id);
            this._onChange("remove", id, obj);
        }
        this.events.fire(types_1.DataEvents.afterRemove, [obj]);
    };
    DataCollection.prototype._copy = function (id, index, target, targetId, key) {
        if (!this.isDataLoaded()) {
            helpers_1.dhxWarning("the method doesn't work with lazyLoad");
            return;
        }
        if (!this.exists(id)) {
            return null;
        }
        var newid = core_1.uid();
        if (key) {
            index = index === -1 ? -1 : index + key;
        }
        if (target) {
            if (!(target instanceof DataCollection) && targetId) {
                target.add(helpers_1.copyWithoutInner(this.getItem(id)), index);
                return;
            }
            if (target.exists(id)) {
                target.add(__assign(__assign({}, helpers_1.copyWithoutInner(this.getItem(id))), { id: newid }), index);
                return newid;
            }
            else {
                target.add(helpers_1.copyWithoutInner(this.getItem(id)), index);
                return id;
            }
        }
        this.add(__assign(__assign({}, helpers_1.copyWithoutInner(this.getItem(id))), { id: newid }), index);
        return newid;
    };
    DataCollection.prototype._move = function (id, index, target, targetId, key) {
        if (!this.isDataLoaded()) {
            helpers_1.dhxWarning("the method doesn't work with lazyLoad");
            return;
        }
        if (key) {
            index = index === -1 ? -1 : index + key;
        }
        if (target && target !== this && this.exists(id)) {
            var item = core_1.copy(this.getItem(id), true);
            if (target.exists(id)) {
                item.id = core_1.uid();
            }
            if (targetId) {
                item.parent = targetId;
            }
            target.add(item, index);
            // remove data from original collection
            this.remove(id);
            return item.id;
        }
        if (this.getIndex(id) === index) {
            return null;
        }
        // move other elements
        var spliced = this._order.splice(this.getIndex(id), 1)[0];
        if (index === -1) {
            index = this._order.length;
        }
        this._order.splice(index, 0, spliced);
        this.events.fire(types_1.DataEvents.change);
        return id;
    };
    DataCollection.prototype._removeAll = function () {
        this._pull = {};
        this._order = [];
        this._changes.order = [];
        this._initOrder = null;
    };
    DataCollection.prototype._addCore = function (obj, index) {
        if (this.config.init) {
            obj = this.config.init(obj);
        }
        obj.id = obj.id ? obj.id.toString() : core_1.uid();
        if (this._pull[obj.id]) {
            helpers_1.dhxError("Item already exist");
        }
        // todo: not ideal solution
        if (this._initOrder && this._initOrder.length) {
            this._addToOrder(this._initOrder, obj, index);
        }
        this._addToOrder(this._order, obj, index);
        return obj.id;
    };
    DataCollection.prototype._removeCore = function (id) {
        if (this.getIndex(id) >= 0) {
            this._order = this._order.filter(function (el) { return el.id !== id; });
            delete this._pull[id];
        }
        if (this._initOrder && this._initOrder.length) {
            this._initOrder = this._initOrder.filter(function (el) { return el.id !== id; });
        }
    };
    DataCollection.prototype._parse_data = function (data) {
        var index = this._order.length;
        if (this.config.prep) {
            data = this.config.prep(data);
        }
        for (var _i = 0, data_1 = data; _i < data_1.length; _i++) {
            var obj = data_1[_i];
            if (this.config.init) {
                obj = this.config.init(obj);
            }
            obj.id = obj.id || obj.id === 0 ? obj.id : core_1.uid();
            this._pull[obj.id] = obj;
            this._order[index++] = obj;
        }
    };
    DataCollection.prototype._approximate = function (data, values, maxNum) {
        var len = data.length;
        var vlen = values.length;
        var rlen = Math.floor(len / maxNum);
        var newData = Array(Math.ceil(len / rlen));
        var index = 0;
        for (var i = 0; i < len; i += rlen) {
            var newItem = core_1.copy(data[i]);
            var end = Math.min(len, i + rlen);
            for (var j = 0; j < vlen; j++) {
                var sum = 0;
                for (var z = i; z < end; z++) {
                    sum += data[z][values[j]];
                }
                newItem[values[j]] = sum / (end - i);
            }
            newData[index++] = newItem;
        }
        return newData;
    };
    DataCollection.prototype._onChange = function (status, id, obj) {
        for (var _i = 0, _a = this._changes.order; _i < _a.length; _i++) {
            var item = _a[_i];
            // update pending item if previous state is "saving" or if item not saved yet
            if (item.id === id && !item.saving) {
                // update item
                if (item.error) {
                    item.error = false;
                }
                item = __assign(__assign({}, item), { obj: obj, status: status });
                this.events.fire(types_1.DataEvents.change, [id, status, obj]);
                return;
            }
        }
        this._changes.order.push({ id: id, status: status, obj: __assign({}, obj), saving: false });
        this.events.fire(types_1.DataEvents.change, [id, status, obj]);
    };
    DataCollection.prototype._addToOrder = function (array, obj, index) {
        if (index >= 0 && array[index]) {
            this._pull[obj.id] = obj;
            array.splice(index, 0, obj);
        }
        else {
            this._pull[obj.id] = obj;
            array.push(obj);
        }
    };
    DataCollection.prototype._applyFilters = function () {
        var _this = this;
        if (this._filters && Object.keys(this._filters).length) {
            var fOrder = this._order.filter(function (item) {
                return Object.keys(_this._filters).every(function (key) {
                    return item[key]
                        ? _this._filters[key].compare(item[key], _this._filters[key].match, item)
                        : _this._filters[key].compare(item);
                });
            });
            if (!this._initOrder) {
                this._initOrder = this._order;
            }
            this._order = fOrder;
        }
    };
    return DataCollection;
}());
exports.DataCollection = DataCollection;


/***/ }),
/* 27 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
var JsonDriver_1 = __webpack_require__(28);
var CsvDriver_1 = __webpack_require__(29);
var XMLDriver_1 = __webpack_require__(47);
exports.dataDrivers = {
    json: JsonDriver_1.JsonDriver,
    csv: CsvDriver_1.CsvDriver,
};
exports.dataDriversPro = __assign(__assign({}, exports.dataDrivers), { xml: XMLDriver_1.XMLDriver });


/***/ }),
/* 28 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var JsonDriver = /** @class */ (function () {
    function JsonDriver() {
    }
    JsonDriver.prototype.toJsonArray = function (data) {
        return this.getRows(data);
    };
    JsonDriver.prototype.serialize = function (data) {
        return data;
    };
    JsonDriver.prototype.getFields = function (row) {
        return row;
    };
    JsonDriver.prototype.getRows = function (data) {
        return typeof data === "string" ? JSON.parse(data) : data;
    };
    return JsonDriver;
}());
exports.JsonDriver = JsonDriver;


/***/ }),
/* 29 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
var CsvDriver = /** @class */ (function () {
    function CsvDriver(config) {
        var initConfig = {
            skipHeader: 0,
            nameByHeader: false,
            rowDelimiter: "\n",
            columnDelimiter: ",",
        };
        this.config = __assign(__assign({}, initConfig), config);
        if (this.config.nameByHeader) {
            this.config.skipHeader = 1;
        }
    }
    CsvDriver.prototype.getFields = function (row, headers) {
        var parts = row.trim().split(this.config.columnDelimiter);
        var obj = {};
        for (var i = 0; i < parts.length; i++) {
            obj[headers ? headers[i] : i + 1] = parts[i];
        }
        return obj;
    };
    CsvDriver.prototype.getRows = function (data) {
        return data.trim().split(this.config.rowDelimiter);
    };
    CsvDriver.prototype.toJsonArray = function (data) {
        var _this = this;
        var rows = this.getRows(data);
        var names = this.config.names;
        if (this.config.skipHeader) {
            var top_1 = rows.splice(0, this.config.skipHeader);
            if (this.config.nameByHeader) {
                names = top_1[0].trim().split(this.config.columnDelimiter);
            }
        }
        return rows.map(function (row) { return _this.getFields(row, names); });
    };
    CsvDriver.prototype.serialize = function (data, withoutHeader) {
        var header = data[0]
            ? Object.keys(data[0])
                .filter(function (key) { return !key.startsWith("$"); })
                .join(this.config.columnDelimiter)
            : "";
        var readyData = this._serialize(data);
        if (withoutHeader) {
            return readyData;
        }
        return header + readyData;
    };
    CsvDriver.prototype._serialize = function (data) {
        var _this = this;
        return data.reduce(function (csv, row) {
            var cells = Object.keys(row).reduce(function (total, key, i) {
                if (key.startsWith("$") || key === "items") {
                    return total;
                }
                return "" + total + row[key] + (i === row.length - 1 ? "" : _this.config.columnDelimiter);
            }, "");
            if (row.items) {
                return csv + "\n" + cells + _this._serialize(row.items);
            }
            return "" + csv + _this.config.rowDelimiter + cells;
        }, "");
    };
    return CsvDriver;
}());
exports.CsvDriver = CsvDriver;


/***/ }),
/* 30 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Line_1 = __webpack_require__(55);
var OrgChartCard_1 = __webpack_require__(31);
var OrgChartImgCard_1 = __webpack_require__(56);
var DiagramFlowShape_1 = __webpack_require__(22);
var DiagramTextShape_1 = __webpack_require__(57);
var DiagramCustomShape_1 = __webpack_require__(35);
exports.shapes = {
    line: Line_1.Line,
    dash: Line_1.Line,
    card: OrgChartCard_1.OrgChartCard,
    "img-card": OrgChartImgCard_1.OrgChartImgCard,
    text: DiagramTextShape_1.DiagramTextShape,
};
for (var key in DiagramFlowShape_1.flowShapes) {
    exports.shapes[key] = DiagramFlowShape_1.DiagramFlowShape;
}
function shapesFactory(config, parameters) {
    var component = exports.shapes[config.type];
    if (!component) {
        component = exports.shapes.card;
        if (parameters.shapes[config.type]) {
            return new DiagramCustomShape_1.DiagramCustomShape(config, parameters);
        }
    }
    return new component(config, parameters);
}
exports.shapesFactory = shapesFactory;


/***/ }),
/* 31 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var dom_1 = __webpack_require__(0);
var baseMetaInfo_1 = __webpack_require__(6);
var en_1 = __webpack_require__(3);
var Base_1 = __webpack_require__(11);
var templates_1 = __webpack_require__(16);
var OrgChartCard = /** @class */ (function (_super) {
    __extends(OrgChartCard, _super);
    function OrgChartCard(config, parameters) {
        var _this = _super.call(this, config, parameters) || this;
        _this.config = config;
        _this.id = _this.config.id;
        return _this;
    }
    OrgChartCard.prototype.render = function () {
        var _a = this.config, id = _a.id, angle = _a.angle, width = _a.width, height = _a.height, headerColor = _a.headerColor;
        var borderColor = this.config.$selected ? headerColor : "#E4E4E4";
        var coords = this.getCoords(this.config);
        return dom_1.el("div", {
            _key: id,
            dhx_id: id,
            class: "dhx_diagram_org_item " + this.getCss(),
            style: {
                transform: "rotate(" + (angle || 0) + "deg)",
                position: "absolute",
                top: coords.y,
                left: coords.x,
                zIndex: 0,
            },
        }, [
            dom_1.el("div", {
                class: "dhx_item_shape",
                id: id,
                style: {
                    height: height,
                    width: width,
                    border: "1px solid " + borderColor,
                    borderRadius: "1px",
                },
            }),
            templates_1.getHeaderTpl(this.config),
            templates_1.getTextTemplate(this.config, this.getContent()),
            templates_1.getCircleTpl(this.config),
        ]);
    };
    OrgChartCard.prototype.getMetaInfo = function () {
        return baseMetaInfo_1.getMeta([
            { type: "grid", label: en_1.default.gridStep },
            { type: "color", label: en_1.default.color },
            { type: "position", label: en_1.default.position },
            { type: "size", label: en_1.default.size },
            { type: "text", label: en_1.default.text },
        ]);
    };
    OrgChartCard.prototype.getCss = function () {
        return "dhx_diagram_item " + _super.prototype.getCss.call(this);
    };
    OrgChartCard.prototype.setDefaults = function (config, defaults) {
        var width = config.width, height = config.height, text = config.text, headerColor = config.headerColor;
        var widthDefaut = defaults.width ? parseFloat(defaults.width) : 140;
        var heightDefault = defaults.height ? parseFloat(defaults.height) : 90;
        config.width = width || widthDefaut;
        config.height = height || heightDefault;
        config.text = typeof text === "string" ? text : defaults.text || "";
        config.headerColor = headerColor || defaults.headerColor || "";
        return config;
    };
    OrgChartCard.prototype.getContent = function () {
        return this.config.text;
    };
    return OrgChartCard;
}(Base_1.BaseShape));
exports.OrgChartCard = OrgChartCard;


/***/ }),
/* 32 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var _a;
Object.defineProperty(exports, "__esModule", { value: true });
var LineMode;
(function (LineMode) {
    LineMode[LineMode["Direct"] = 1] = "Direct";
    LineMode[LineMode["Edges"] = 2] = "Edges";
})(LineMode = exports.LineMode || (exports.LineMode = {}));
var Direction;
(function (Direction) {
    Direction[Direction["Top"] = 1] = "Top";
    Direction[Direction["Bottom"] = 2] = "Bottom";
    Direction[Direction["Right"] = 3] = "Right";
    Direction[Direction["Left"] = 4] = "Left";
})(Direction = exports.Direction || (exports.Direction = {}));
exports.DirVectors = (_a = {},
    _a[Direction.Bottom] = { x: 0, y: 1 },
    _a[Direction.Top] = { x: 0, y: -1 },
    _a[Direction.Right] = { x: 1, y: 0 },
    _a[Direction.Left] = { x: -1, y: 0 },
    _a);


/***/ }),
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
exports.sign = function (x) {
    x = +x;
    if (x === 0 || isNaN(x)) {
        return x;
    }
    return x > 0 ? 1 : -1;
};


/***/ }),
/* 34 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var __spreadArrays = (this && this.__spreadArrays) || function () {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
};
Object.defineProperty(exports, "__esModule", { value: true });
/* eslint-disable @typescript-eslint/unbound-method */
var events_1 = __webpack_require__(7);
var core_1 = __webpack_require__(1);
var dom_1 = __webpack_require__(0);
var html_1 = __webpack_require__(2);
var view_1 = __webpack_require__(9);
var Export_1 = __webpack_require__(43);
var linkPaths_1 = __webpack_require__(24);
var placement_1 = __webpack_require__(44);
var Selection_1 = __webpack_require__(45);
var factory_1 = __webpack_require__(30);
var DiagramFlowShape_1 = __webpack_require__(22);
var ShapesCollection_1 = __webpack_require__(36);
var Toolbar_1 = __webpack_require__(58);
var types_1 = __webpack_require__(12);
var Graph_1 = __webpack_require__(19);
var types_2 = __webpack_require__(32);
var Hola_1 = __webpack_require__(59);
var compose_1 = __webpack_require__(63);
var decompose_1 = __webpack_require__(64);
var Diagram = /** @class */ (function (_super) {
    __extends(Diagram, _super);
    function Diagram(container, config) {
        var _this = _super.call(this, container, config) || this;
        _this.version = "3.0.2";
        _this._set_defaults();
        _this._init_events();
        _this._init_struct();
        if (_this.config.toolbar) {
            _this.toolbar = new Toolbar_1.Toolbar(_this.events, _this.config.toolbar);
        }
        var view = dom_1.create({ render: function (vm) { return _this._render(vm); } }, _this);
        _this.mount(container, view);
        return _this;
    }
    Diagram.prototype.addShape = function (type, parameters) {
        var allTypes = __spreadArrays(["line", "dash", "card", "img-card", "text"], Object.keys(this.flowShapes));
        if (allTypes.includes(type)) {
            throw new Error("Cannot reassign existing type");
        }
        if (typeof parameters.template !== "function") {
            throw new Error("The template property must be set as a function");
        }
        if (parameters.defaults) {
            this.config.defaults[type] = __assign({}, parameters.defaults);
        }
        if (parameters.properties) {
            this.config.properties[type] = __spreadArrays(parameters.properties);
        }
        this.flowShapes[type] = parameters.template;
    };
    Diagram.prototype.locate = function (event) {
        var id = html_1.locate(event, "dhx_id");
        var item = this.data.getItem(id);
        return item ? item.$shape : null;
    };
    Diagram.prototype.collapseItem = function (id) {
        if (this.events.fire(types_1.DiagramEvents.beforeCollapse, [id])) {
            this.data.update(id, { open: false });
            this.events.fire(types_1.DiagramEvents.afterCollapse, [id]);
        }
    };
    Diagram.prototype.expandItem = function (id) {
        if (this.events.fire(types_1.DiagramEvents.beforeExpand, [id])) {
            this.data.update(id, { open: true });
            this.events.fire(types_1.DiagramEvents.afterExpand, [id]);
        }
    };
    Diagram.prototype.getScrollState = function () {
        var wrapper = this.getRootView().node.el;
        return {
            x: wrapper.scrollLeft,
            y: wrapper.scrollTop,
        };
    };
    Diagram.prototype.scrollTo = function (x, y) {
        var wrapper = this.getRootView().node.el;
        wrapper.scrollLeft = x;
        wrapper.scrollTop = y;
    };
    Diagram.prototype.showItem = function (id) {
        var wrapper = this.getRootView().node.el;
        var item = this.data.getItem(id);
        if (!item) {
            return;
        }
        var box = item.$shape.getBox();
        var width = wrapper.offsetWidth / 2;
        var height = wrapper.offsetHeight / 2;
        this.scrollTo(box.right + 10 - width, box.bottom + 10 - height);
    };
    Diagram.prototype.autoPlace = function (config) {
        var _this = this;
        var _a = this.config, autoplacement = _a.autoplacement, type = _a.type;
        var mode = (config && config.mode) || autoplacement.mode || "direct";
        var root = (config && config.root) || this.selection.getId();
        if (type === "org") {
            throw new Error("This method does not work with this type of diagram");
        }
        var graph = new Graph_1.default();
        this.data.map(function (shape) {
            if (shape.type !== "line" && shape.type !== "dash") {
                graph.addNode({
                    id: shape.id.toString(),
                    w: shape.width,
                    h: shape.height,
                    x: shape.x,
                    y: shape.y,
                });
            }
        });
        this.data.map(function (shape) {
            if ((shape.type === "line" || shape.type === "dash") && shape.from && shape.to) {
                _this.data.update(shape.id, {
                    fromSide: mode === "direct" ? "center" : undefined,
                    toSide: mode === "direct" ? "center" : undefined,
                });
                graph.addEdge(shape.from.toString(), shape.to.toString());
            }
            else if (shape.type === "line" || shape.type === "dash") {
                _this.data.remove(shape.id);
            }
        });
        graph.clean();
        var layoutTree = new Hola_1.default();
        var composeGraph = compose_1.compose(decompose_1.decompose(graph).map(function (graphItem) {
            graphItem = layoutTree.layout(graphItem, {
                mode: mode,
                root: root,
                dir: (autoplacement && autoplacement.direction) || types_2.Direction.Bottom,
                wide: autoplacement && autoplacement.wide,
                itemPadding: (autoplacement && autoplacement.itemPadding) || 0,
                levelPadding: (autoplacement && autoplacement.levelPadding) || 0,
                full: true,
                preserveLocation: false,
            });
            graphItem.setGlobalBox();
            return graphItem;
        }), {
            padding: typeof autoplacement.graphPadding === "number"
                ? (config && config.graphPadding) || autoplacement.graphPadding
                : 200,
        });
        var bounds = composeGraph.getBox();
        composeGraph.translate({ x: -bounds[0][0], y: -bounds[1][0] });
        composeGraph.getNodes().forEach(function (node) {
            var x = Math.floor(node.x - node.w / 2);
            var y = Math.floor(node.y - node.h / 2);
            _this.data.update(node.id, { x: x, y: y });
        });
    };
    Diagram.prototype.destructor = function () {
        this.events.clear();
        this.unmount();
    };
    Diagram.prototype._render = function (vm) {
        if (this._doNotRepaint && vm.node) {
            return vm.node;
        }
        this._doNotRepaint = true;
        var _a = this._getContent(), size = _a.size, svgContent = _a.svgContent, htmlContent = _a.htmlContent;
        this.events.fire(types_1.DiagramEvents.beforeRender, [size]);
        var width = size.x - size.left + this.config.margin.x * 2;
        var height = size.y - size.top + this.config.margin.y * 2;
        var css = this.config.type === "org" ? "dhx_org_chart" : "dhx_free_diagram";
        size.left -= this.config.margin.x;
        size.top -= this.config.margin.y;
        var topHtmlPosition = size.top + this.config.margin.y < 0 ? Math.abs(size.top) : this.config.margin.y;
        var leftHtmlPosition = size.left + this.config.margin.x < 0 ? Math.abs(size.left) : this.config.margin.x;
        var toolbar = null;
        if (this.toolbar) {
            var id = this.selection.getId();
            if (id) {
                toolbar = this.toolbar.render(this.data.getItem(id), __assign(__assign({}, size), { scale: this.config.scale }));
            }
        }
        var extra = [];
        if (this.config.$svg) {
            extra = this.config.$svg(size);
        }
        return dom_1.el(".dhx_diagram.dhx_widget", __assign({}, this._htmlevents), [
            dom_1.el("div.dhx_diagram__container", {
                class: css,
                style: {
                    height: height * this.config.scale,
                    width: width * this.config.scale,
                },
            }, [
                dom_1.el(".dhx_wrapper", {
                    style: {
                        transform: "scale(" + this.config.scale + ")",
                        position: "absolute",
                        top: 0,
                        left: 0,
                        transformOrigin: "top",
                        zIndex: this.config.type === "org" ? (this._active ? 1 : 0) : 0,
                    },
                }, [
                    dom_1.el("div.dhx_diagram__scale-container", [
                        dom_1.sv("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            width: width,
                            height: height,
                            viewBox: size.left + " " + size.top + " " + width + " " + height,
                        }, [
                            dom_1.sv("defs", [
                                dom_1.sv("filter", {
                                    x: 0,
                                    y: 0,
                                    width: 1,
                                    height: 1,
                                    id: "dhx_text_background",
                                }, [
                                    dom_1.sv("feFlood", { "flood-color": "white" }),
                                    dom_1.sv("feComposite", { in: "SourceGraphic" }),
                                ]),
                            ]),
                            dom_1.sv("g", {
                                "shape-rendering": this.config.scale === 1 && this.config.type === "org"
                                    ? "crispedges"
                                    : "auto",
                            }, svgContent),
                        ]),
                        dom_1.el(".dhx_shape-container", {
                            style: {
                                position: "absolute",
                                top: topHtmlPosition,
                                left: leftHtmlPosition,
                            },
                        }, htmlContent),
                    ]),
                ]),
                extra,
                toolbar,
            ]),
        ]);
    };
    Diagram.prototype._init_events = function () {
        var _this = this;
        this._htmlevents = {
            onscroll: function () {
                _this.events.fire(types_1.DiagramEvents.scroll, [_this.getScrollState()]);
            },
            onmousedown: html_1.eventHandler(function (ev) { return _this.locate(ev); }, {
                dhx_diagram_item: function (ev, item) {
                    _this._active = true;
                    _this.events.fire(types_1.DiagramEvents.shapeMouseDown, [item.id, ev]);
                },
                dhx_diagram_flow_item: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapeMouseDown, [item.id, ev]);
                },
                dhx_diagram_connector: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapeMouseDown, [
                        item.id,
                        ev,
                        _this._getPoint(ev.clientX, ev.clientY),
                    ]);
                },
                dhx_diagram: function (ev) {
                    var t = ev.target;
                    var isContainer = t.getAttribute && (t.getAttribute("class") || "").match(/dhx_diagram\b/);
                    var isSvg = t.tagName === "svg";
                    if (!html_1.locateNodeByClassName(t, "dhx_popup_toolbar") && (isContainer || isSvg)) {
                        _this.events.fire(types_1.DiagramEvents.emptyAreaMouseDown, [ev]);
                    }
                },
            }),
            onmouseout: html_1.eventHandler(function (ev) { return _this.locate(ev); }, {
                dhx_selected: function () {
                    _this._active = false;
                    _this.paint();
                },
            }),
            onmouseover: html_1.eventHandler(function (ev) { return _this.locate(ev); }, {
                dhx_selected: function () {
                    _this._active = true;
                    _this.paint();
                },
                dhx_diagram_item: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapeHover, [item.id, ev]);
                },
                dhx_diagram_flow_item: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapeHover, [item.id, ev]);
                },
                dhx_diagram_connector: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapeHover, [item.id, ev]);
                },
            }),
            onclick: html_1.eventHandler(function (ev) { return _this.locate(ev); }, {
                dhx_expand_icon: function (_ev, item) { return _this.expandItem(item.id); },
                dhx_hide_icon: function (_ev, item) { return _this.collapseItem(item.id); },
                dhx_diagram_connector: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapeClick, [item.id, ev]);
                },
                dhx_diagram_item: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapeClick, [item.id, ev]);
                    if (_this.config.select && !ev.shiftKey && !ev.altKey) {
                        _this.selection.add(item.id || item.config.id);
                    }
                },
                dhx_diagram_flow_item: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapeClick, [item.id, ev]);
                },
                dhx_diagram_line: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.lineClick, [item.id, ev]);
                },
                dhx_diagram: function (ev) {
                    var t = ev.target;
                    var isContainer = t.getAttribute && (t.getAttribute("class") || "").match(/dhx_diagram\b/);
                    var isSvg = t.tagName === "svg";
                    if (isContainer || isSvg) {
                        _this.events.fire(types_1.DiagramEvents.emptyAreaClick, [ev]);
                    }
                },
            }),
            ondblclick: html_1.eventHandler(function (ev) { return _this.locate(ev); }, {
                dhx_diagram_connector: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapedDblClick, [item.id, ev]);
                },
                dhx_diagram_item: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapedDblClick, [item.id, ev]);
                },
                dhx_diagram_flow_item: function (ev, item) {
                    _this.events.fire(types_1.DiagramEvents.shapedDblClick, [item.id, ev]);
                },
            }),
        };
    };
    Diagram.prototype._set_defaults = function () {
        this.config = core_1.extend({
            defaultShapeType: "card",
            defaultLinkType: "line",
            lineGap: 10,
            scale: 1,
            margin: {
                x: 40,
                y: 40,
                itemX: 40,
                itemY: 40,
            },
            gridStep: 10,
            defaults: {},
            properties: {},
            autoplacement: {
                mode: "direct",
                graphPadding: 200,
            },
        }, this.config);
    };
    Diagram.prototype._init_struct = function () {
        var _this = this;
        this.events = new events_1.EventSystem(this);
        this.flowShapes = __assign({}, DiagramFlowShape_1.flowShapes);
        this.data = new ShapesCollection_1.ShapesCollection({
            init: function (item) {
                var defType = "from" in item ? _this.config.defaultLinkType : _this.config.defaultShapeType;
                item.type = item.type || defType;
                if (_this.config.type !== "org" && item.from) {
                    item.stroke = item.stroke || "#2196F3";
                }
                item.$shape = factory_1.shapesFactory(item, {
                    defaults: _this.config.defaults[item.type] || {},
                    properties: _this.config.properties[item.type] || {},
                    shapes: _this.flowShapes,
                });
                return item;
            },
            update: function (item) {
                item.$shape.config = item;
            },
            type: this.config.type,
        }, this.events);
        this.selection = new Selection_1.Selection({}, this.data, this.events);
        this.export = new Export_1.Exporter("diagram", this.version, this);
        this.data.events.on(types_1.DataEvents.change, function () { return _this.paint(); });
        this.events.on(types_1.SelectionEvents.afterSelect, function () { return _this.paint(); });
    };
    Diagram.prototype._getContent = function () {
        var _this = this;
        var placed = false;
        if (this.config.type === "org") {
            placement_1.placeOrgonogram(this.data, this.config);
            placed = true;
        }
        var size = { x: 0, y: 0, left: 0, top: 0, scale: this.config.scale };
        var lines = [];
        var htmlContent = [];
        this.data.mapVisible(function (config) {
            if (!config) {
                return;
            }
            if (config.$shape.isConnector()) {
                if (!placed) {
                    linkPaths_1.nearestLinkPath(config, _this.data.getItem(config.from), _this.data.getItem(config.to), _this.config);
                }
                lines.push(config.$shape.render());
            }
            else {
                htmlContent.push(config.$shape.render());
            }
            var box = config.$shape.getBox();
            if (box.right > size.x) {
                size.x = box.right;
            }
            if (box.left < size.left) {
                size.left = box.left;
            }
            if (box.bottom > size.y) {
                size.y = box.bottom;
            }
            if (box.top < size.top) {
                size.top = box.top;
            }
        });
        return { size: size, svgContent: lines, htmlContent: htmlContent };
    };
    Diagram.prototype._getPoint = function (x, y) {
        var diagramRect = this.getRootView().node.el.getBoundingClientRect();
        return {
            x: x - diagramRect.left - this.config.margin.x,
            y: y - diagramRect.top - this.config.margin.y,
        };
    };
    return Diagram;
}(view_1.View));
exports.Diagram = Diagram;


/***/ }),
/* 35 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var dom_1 = __webpack_require__(0);
var core_1 = __webpack_require__(1);
var baseMetaInfo_1 = __webpack_require__(6);
var templates_1 = __webpack_require__(16);
var Base_1 = __webpack_require__(11);
var en_1 = __webpack_require__(3);
var DiagramCustomShape = /** @class */ (function (_super) {
    __extends(DiagramCustomShape, _super);
    function DiagramCustomShape(config, parameters) {
        var _this = _super.call(this, config, parameters) || this;
        _this.config = config;
        _this.id = _this.config.id;
        _this.shapes = parameters.shapes;
        _this._properties = parameters.properties;
        return _this;
    }
    DiagramCustomShape.prototype.getMetaInfo = function () {
        return this._getMetaInfoStructure(this.config);
    };
    DiagramCustomShape.prototype.render = function () {
        var _a = this.config, id = _a.id, angle = _a.angle, width = _a.width, height = _a.height;
        var coords = this.getCoords(this.config);
        return dom_1.el("div", {
            _key: id,
            class: "dhx_diagram_flow_item " + this.getCss(),
            dhx_id: id,
            style: {
                transform: "rotate(" + (angle || 0) + "deg)",
                zIndex: this.config.$selected ? 1 : 0,
                position: "absolute",
                height: height,
                width: width,
                top: coords.y,
                left: coords.x,
            },
        }, [this._getCustomContent(), templates_1.getCircleTpl(this.config)]);
    };
    DiagramCustomShape.prototype.setDefaults = function (config, defaults) {
        Object.keys(defaults).forEach(function (property) {
            config[property] = config[property] || defaults[property];
        });
        var widthDefaut = defaults.width ? parseFloat(defaults.width) : 140;
        var heightDefault = defaults.height ? parseFloat(defaults.height) : 90;
        config.width = config.width || widthDefaut;
        config.height = config.height || heightDefault;
        config.x = config.x || 0;
        config.y = config.y || 0;
        return config;
    };
    DiagramCustomShape.prototype._getMetaInfoStructure = function (config) {
        return !core_1.isEmptyObj(this._properties)
            ? baseMetaInfo_1.getMeta(this._properties)
            : this._getBaseMetaInfoStructure(config);
    };
    DiagramCustomShape.prototype._getBaseMetaInfoStructure = function (config) {
        var text = config.text, title = config.title, img = config.img, fill = config.fill, stroke = config.stroke, strokeWidth = config.strokeWidth, textAlign = config.textAlign, lineHeight = config.lineHeight, fontStyle = config.fontStyle, textVerticalAlign = config.textVerticalAlign, fontSize = config.fontSize;
        var metaInfo = [];
        if (config.$expandColor) {
            metaInfo = baseMetaInfo_1.getMeta([
                { type: "grid", label: en_1.default.gridStep },
                { type: "position", label: en_1.default.position },
                { type: "size", label: en_1.default.size },
            ]);
        }
        else {
            metaInfo = baseMetaInfo_1.getMeta([
                { type: "grid", label: en_1.default.gridStep },
                { type: "arrange", label: en_1.default.arrange },
            ]);
        }
        if (fill) {
            metaInfo.push(baseMetaInfo_1.getMeta([{ type: "fill", label: en_1.default.fill }])[0]);
        }
        if (title) {
            metaInfo.push(baseMetaInfo_1.getMeta([{ type: "title", label: en_1.default.title }])[0]);
        }
        if (text) {
            metaInfo.push(baseMetaInfo_1.getMeta([{ type: "text", label: en_1.default.text }])[0]);
        }
        if (stroke && strokeWidth) {
            metaInfo.push(baseMetaInfo_1.getMeta([{ type: "strokeProps", label: en_1.default.stroke }])[0]);
        }
        if (textAlign && lineHeight && fontStyle && textVerticalAlign && fontSize) {
            metaInfo.push(baseMetaInfo_1.getMeta([{ type: "textProps", label: en_1.default.textProps }])[0]);
        }
        if (img) {
            metaInfo.push(baseMetaInfo_1.getMeta([{ type: "img", label: en_1.default.image }])[0]);
        }
        return metaInfo;
    };
    DiagramCustomShape.prototype._getCustomContent = function () {
        var _a = this.config, type = _a.type, width = _a.width, height = _a.height;
        var shape = this.shapes[type];
        if (typeof shape !== "function") {
            return;
        }
        return [
            dom_1.el("div", {
                class: "dhx_diagram_flow_shape dhx_item_shape ",
                style: {
                    width: width,
                    height: height,
                },
            }, this._getShapeFromFunction(shape)),
        ];
    };
    DiagramCustomShape.prototype._getShapeFromFunction = function (func) {
        var shape = new DOMParser().parseFromString(func(this.config), "text/xml");
        return [dom_1.jsonToVDOM(dom_1.xmlToJson(shape))];
    };
    return DiagramCustomShape;
}(Base_1.BaseShape));
exports.DiagramCustomShape = DiagramCustomShape;


/***/ }),
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var ts_data_1 = __webpack_require__(15);
var types_1 = __webpack_require__(12);
var ShapesCollection = /** @class */ (function (_super) {
    __extends(ShapesCollection, _super);
    function ShapesCollection(config, events) {
        var _this = _super.call(this, config, events) || this;
        _this._roots = [];
        _this._orgMode = config.type === "org";
        _this.events.on(types_1.DataEvents.change, function (_id, mode, obj) {
            if (mode === "remove") {
                _this._removeNested(obj);
                _this._removeCore(obj.$parent); // [FIXME] multiple parents case is not supported
            }
            if (mode === "add" && obj.parent) {
                _this._addCore({ from: obj.parent, to: obj.id }, -1);
            }
            _this._mark_chains();
        });
        return _this;
    }
    ShapesCollection.prototype.getNearId = function (id) {
        var item = this._pull[id];
        if (!item) {
            if (!this._order.length) {
                return "";
            }
            return this._order[0].id;
        }
        var test = item;
        while (this._orgMode && test.$parent) {
            test = this._pull[this._pull[test.$parent].from];
            if (test.open === false) {
                return test.id;
            }
        }
        return item.id;
    };
    ShapesCollection.prototype.mapVisible = function (handler) {
        var _this = this;
        var result = [];
        if (this._orgMode) {
            var roots = this.getRoots();
            for (var i = 0; i < roots.length; i++) {
                this._eachBranch(this.getItem(roots[i]), handler, result);
            }
        }
        else {
            this.map(function (obj) {
                if (obj.hidden) {
                    return;
                }
                if (obj.$shape.isConnector()) {
                    var from = _this.getItem(obj.from) || {};
                    var to = _this.getItem(obj.to) || {};
                    if (from.hidden || to.hidden) {
                        return;
                    }
                }
                result.push(handler(obj));
            });
        }
        return result;
    };
    ShapesCollection.prototype.getRoots = function () {
        return this._roots;
    };
    ShapesCollection.prototype._removeNested = function (obj) {
        var kids = obj.$kids;
        if (kids) {
            for (var i = 0; i < kids.length; i++) {
                if (this._orgMode) {
                    this._removeNested(this.getItem(kids[i][1]));
                    this._removeCore(kids[i][1]);
                }
                this._removeCore(kids[i][0]);
            }
        }
    };
    ShapesCollection.prototype._eachBranch = function (item, handler, stack) {
        if (item.hidden) {
            return;
        }
        stack.push(handler(item)); // master item
        if (item.open !== false) {
            var kids = item.$kids;
            if (kids) {
                for (var i = 0; i < kids.length; i++) {
                    var child = this.getItem(kids[i][1]);
                    if (!child.hidden) {
                        stack.push(handler(this.getItem(kids[i][0]))); // link
                        this._eachBranch(child, handler, stack);
                    }
                }
            }
        }
    };
    ShapesCollection.prototype._parse_data = function (data) {
        var links = [];
        var linksInData = false;
        for (var i = 0; i < data.length; i++) {
            var obj = data[i];
            if (obj.parent && !linksInData) {
                links.push({ from: obj.parent, to: obj.id });
            }
            if (obj.from) {
                linksInData = true;
            }
        }
        if (links.length && !linksInData) {
            data = data.concat(links);
        }
        _super.prototype._parse_data.call(this, data);
    };
    ShapesCollection.prototype._mark_chains = function () {
        var _this = this;
        this._roots = [];
        var hash = {};
        var parents = {};
        this.map(function (obj) {
            if (obj.$shape.isConnector()) {
                var link = obj;
                parents[link.to] = link.id;
                var kids = (hash[link.from] = hash[link.from] || []);
                kids.push([obj.id, link.to]);
            }
        });
        this.map(function (obj) {
            if (!obj.$shape.isConnector()) {
                obj.$parent = parents[obj.id];
                obj.$kids = hash[obj.id];
                if (!obj.$parent) {
                    _this._roots.push(obj.id);
                }
            }
        });
    };
    return ShapesCollection;
}(ts_data_1.DataCollection));
exports.ShapesCollection = ShapesCollection;


/***/ }),
/* 37 */,
/* 38 */,
/* 39 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var scope = (typeof global !== "undefined" && global) ||
            (typeof self !== "undefined" && self) ||
            window;
var apply = Function.prototype.apply;

// DOM APIs, for completeness

exports.setTimeout = function() {
  return new Timeout(apply.call(setTimeout, scope, arguments), clearTimeout);
};
exports.setInterval = function() {
  return new Timeout(apply.call(setInterval, scope, arguments), clearInterval);
};
exports.clearTimeout =
exports.clearInterval = function(timeout) {
  if (timeout) {
    timeout.close();
  }
};

function Timeout(id, clearFn) {
  this._id = id;
  this._clearFn = clearFn;
}
Timeout.prototype.unref = Timeout.prototype.ref = function() {};
Timeout.prototype.close = function() {
  this._clearFn.call(scope, this._id);
};

// Does not start the time, just sets up the members needed.
exports.enroll = function(item, msecs) {
  clearTimeout(item._idleTimeoutId);
  item._idleTimeout = msecs;
};

exports.unenroll = function(item) {
  clearTimeout(item._idleTimeoutId);
  item._idleTimeout = -1;
};

exports._unrefActive = exports.active = function(item) {
  clearTimeout(item._idleTimeoutId);

  var msecs = item._idleTimeout;
  if (msecs >= 0) {
    item._idleTimeoutId = setTimeout(function onTimeout() {
      if (item._onTimeout)
        item._onTimeout();
    }, msecs);
  }
};

// setimmediate attaches itself to the global object
__webpack_require__(40);
// On some exotic environments, it's not clear which object `setimmediate` was
// able to install onto.  Search each possibility in the same order as the
// `setimmediate` library.
exports.setImmediate = (typeof self !== "undefined" && self.setImmediate) ||
                       (typeof global !== "undefined" && global.setImmediate) ||
                       (this && this.setImmediate);
exports.clearImmediate = (typeof self !== "undefined" && self.clearImmediate) ||
                         (typeof global !== "undefined" && global.clearImmediate) ||
                         (this && this.clearImmediate);

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(17)))

/***/ }),
/* 40 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global, process) {(function (global, undefined) {
    "use strict";

    if (global.setImmediate) {
        return;
    }

    var nextHandle = 1; // Spec says greater than zero
    var tasksByHandle = {};
    var currentlyRunningATask = false;
    var doc = global.document;
    var registerImmediate;

    function setImmediate(callback) {
      // Callback can either be a function or a string
      if (typeof callback !== "function") {
        callback = new Function("" + callback);
      }
      // Copy function arguments
      var args = new Array(arguments.length - 1);
      for (var i = 0; i < args.length; i++) {
          args[i] = arguments[i + 1];
      }
      // Store and register the task
      var task = { callback: callback, args: args };
      tasksByHandle[nextHandle] = task;
      registerImmediate(nextHandle);
      return nextHandle++;
    }

    function clearImmediate(handle) {
        delete tasksByHandle[handle];
    }

    function run(task) {
        var callback = task.callback;
        var args = task.args;
        switch (args.length) {
        case 0:
            callback();
            break;
        case 1:
            callback(args[0]);
            break;
        case 2:
            callback(args[0], args[1]);
            break;
        case 3:
            callback(args[0], args[1], args[2]);
            break;
        default:
            callback.apply(undefined, args);
            break;
        }
    }

    function runIfPresent(handle) {
        // From the spec: "Wait until any invocations of this algorithm started before this one have completed."
        // So if we're currently running a task, we'll need to delay this invocation.
        if (currentlyRunningATask) {
            // Delay by doing a setTimeout. setImmediate was tried instead, but in Firefox 7 it generated a
            // "too much recursion" error.
            setTimeout(runIfPresent, 0, handle);
        } else {
            var task = tasksByHandle[handle];
            if (task) {
                currentlyRunningATask = true;
                try {
                    run(task);
                } finally {
                    clearImmediate(handle);
                    currentlyRunningATask = false;
                }
            }
        }
    }

    function installNextTickImplementation() {
        registerImmediate = function(handle) {
            process.nextTick(function () { runIfPresent(handle); });
        };
    }

    function canUsePostMessage() {
        // The test against `importScripts` prevents this implementation from being installed inside a web worker,
        // where `global.postMessage` means something completely different and can't be used for this purpose.
        if (global.postMessage && !global.importScripts) {
            var postMessageIsAsynchronous = true;
            var oldOnMessage = global.onmessage;
            global.onmessage = function() {
                postMessageIsAsynchronous = false;
            };
            global.postMessage("", "*");
            global.onmessage = oldOnMessage;
            return postMessageIsAsynchronous;
        }
    }

    function installPostMessageImplementation() {
        // Installs an event handler on `global` for the `message` event: see
        // * https://developer.mozilla.org/en/DOM/window.postMessage
        // * http://www.whatwg.org/specs/web-apps/current-work/multipage/comms.html#crossDocumentMessages

        var messagePrefix = "setImmediate$" + Math.random() + "$";
        var onGlobalMessage = function(event) {
            if (event.source === global &&
                typeof event.data === "string" &&
                event.data.indexOf(messagePrefix) === 0) {
                runIfPresent(+event.data.slice(messagePrefix.length));
            }
        };

        if (global.addEventListener) {
            global.addEventListener("message", onGlobalMessage, false);
        } else {
            global.attachEvent("onmessage", onGlobalMessage);
        }

        registerImmediate = function(handle) {
            global.postMessage(messagePrefix + handle, "*");
        };
    }

    function installMessageChannelImplementation() {
        var channel = new MessageChannel();
        channel.port1.onmessage = function(event) {
            var handle = event.data;
            runIfPresent(handle);
        };

        registerImmediate = function(handle) {
            channel.port2.postMessage(handle);
        };
    }

    function installReadyStateChangeImplementation() {
        var html = doc.documentElement;
        registerImmediate = function(handle) {
            // Create a <script> element; its readystatechange event will be fired asynchronously once it is inserted
            // into the document. Do so, thus queuing up the task. Remember to clean up once it's been called.
            var script = doc.createElement("script");
            script.onreadystatechange = function () {
                runIfPresent(handle);
                script.onreadystatechange = null;
                html.removeChild(script);
                script = null;
            };
            html.appendChild(script);
        };
    }

    function installSetTimeoutImplementation() {
        registerImmediate = function(handle) {
            setTimeout(runIfPresent, 0, handle);
        };
    }

    // If supported, we should attach to the prototype of global, since that is where setTimeout et al. live.
    var attachTo = Object.getPrototypeOf && Object.getPrototypeOf(global);
    attachTo = attachTo && attachTo.setTimeout ? attachTo : global;

    // Don't get fooled by e.g. browserify environments.
    if ({}.toString.call(global.process) === "[object process]") {
        // For Node.js before 0.9
        installNextTickImplementation();

    } else if (canUsePostMessage()) {
        // For non-IE10 modern browsers
        installPostMessageImplementation();

    } else if (global.MessageChannel) {
        // For web workers, where supported
        installMessageChannelImplementation();

    } else if (doc && "onreadystatechange" in doc.createElement("script")) {
        // For IE 6–8
        installReadyStateChangeImplementation();

    } else {
        // For older browsers
        installSetTimeoutImplementation();
    }

    attachTo.setImmediate = setImmediate;
    attachTo.clearImmediate = clearImmediate;
}(typeof self === "undefined" ? typeof global === "undefined" ? this : global : self));

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(17), __webpack_require__(41)))

/***/ }),
/* 41 */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),
/* 42 */
/***/ (function(module, exports, __webpack_require__) {

/**
* Copyright (c) 2017, Leon Sorokin
* All rights reserved. (MIT Licensed)
*
* domvm.js (DOM ViewModel)
* A thin, fast, dependency-free vdom view layer
* @preserve https://github.com/leeoniya/domvm (v3.2.6, micro build)
*/

(function (global, factory) {
	 true ? module.exports = factory() :
	undefined;
}(this, (function () { 'use strict';

// NOTE: if adding a new *VNode* type, make it < COMMENT and renumber rest.
// There are some places that test <= COMMENT to assert if node is a VNode

// VNode types
var ELEMENT	= 1;
var TEXT		= 2;
var COMMENT	= 3;

// placeholder types
var VVIEW		= 4;
var VMODEL		= 5;

var ENV_DOM = typeof window !== "undefined";
var win = ENV_DOM ? window : {};
var rAF = win.requestAnimationFrame;

var emptyObj = {};

function noop() {}

var isArr = Array.isArray;

function isSet(val) {
	return val != null;
}

function isPlainObj(val) {
	return val != null && val.constructor === Object;		//  && typeof val === "object"
}

function insertArr(targ, arr, pos, rem) {
	targ.splice.apply(targ, [pos, rem].concat(arr));
}

function isVal(val) {
	var t = typeof val;
	return t === "string" || t === "number";
}

function isFunc(val) {
	return typeof val === "function";
}

function isProm(val) {
	return typeof val === "object" && isFunc(val.then);
}



function assignObj(targ) {
	var args = arguments;

	for (var i = 1; i < args.length; i++)
		{ for (var k in args[i])
			{ targ[k] = args[i][k]; } }

	return targ;
}

// export const defProp = Object.defineProperty;

function deepSet(targ, path, val) {
	var seg;

	while (seg = path.shift()) {
		if (path.length === 0)
			{ targ[seg] = val; }
		else
			{ targ[seg] = targ = targ[seg] || {}; }
	}
}

/*
export function deepUnset(targ, path) {
	var seg;

	while (seg = path.shift()) {
		if (path.length === 0)
			targ[seg] = val;
		else
			targ[seg] = targ = targ[seg] || {};
	}
}
*/

function sliceArgs(args, offs) {
	var arr = [];
	for (var i = offs; i < args.length; i++)
		{ arr.push(args[i]); }
	return arr;
}

function cmpObj(a, b) {
	for (var i in a)
		{ if (a[i] !== b[i])
			{ return false; } }

	return true;
}

function cmpArr(a, b) {
	var alen = a.length;

	if (b.length !== alen)
		{ return false; }

	for (var i = 0; i < alen; i++)
		{ if (a[i] !== b[i])
			{ return false; } }

	return true;
}

// https://github.com/darsain/raft
// rAF throttler, aggregates multiple repeated redraw calls within single animframe
function raft(fn) {
	if (!rAF)
		{ return fn; }

	var id, ctx, args;

	function call() {
		id = 0;
		fn.apply(ctx, args);
	}

	return function() {
		ctx = this;
		args = arguments;
		if (!id) { id = rAF(call); }
	};
}

function curry(fn, args, ctx) {
	return function() {
		return fn.apply(ctx, args);
	};
}

/*
export function prop(val, cb, ctx, args) {
	return function(newVal, execCb) {
		if (newVal !== undefined && newVal !== val) {
			val = newVal;
			execCb !== false && isFunc(cb) && cb.apply(ctx, args);
		}

		return val;
	};
}
*/

/*
// adapted from https://github.com/Olical/binary-search
export function binaryKeySearch(list, item) {
    var min = 0;
    var max = list.length - 1;
    var guess;

	var bitwise = (max <= 2147483647) ? true : false;
	if (bitwise) {
		while (min <= max) {
			guess = (min + max) >> 1;
			if (list[guess].key === item) { return guess; }
			else {
				if (list[guess].key < item) { min = guess + 1; }
				else { max = guess - 1; }
			}
		}
	} else {
		while (min <= max) {
			guess = Math.floor((min + max) / 2);
			if (list[guess].key === item) { return guess; }
			else {
				if (list[guess].key < item) { min = guess + 1; }
				else { max = guess - 1; }
			}
		}
	}

    return -1;
}
*/

// https://en.wikipedia.org/wiki/Longest_increasing_subsequence
// impl borrowed from https://github.com/ivijs/ivi
function longestIncreasingSubsequence(a) {
	var p = a.slice();
	var result = [];
	result.push(0);
	var u;
	var v;

	for (var i = 0, il = a.length; i < il; ++i) {
		var j = result[result.length - 1];
		if (a[j] < a[i]) {
			p[i] = j;
			result.push(i);
			continue;
		}

		u = 0;
		v = result.length - 1;

		while (u < v) {
			var c = ((u + v) / 2) | 0;
			if (a[result[c]] < a[i]) {
				u = c + 1;
			} else {
				v = c;
			}
		}

		if (a[i] < a[result[u]]) {
			if (u > 0) {
				p[i] = result[u - 1];
			}
			result[u] = i;
		}
	}

	u = result.length;
	v = result[u - 1];

	while (u-- > 0) {
		result[u] = v;
		v = p[v];
	}

	return result;
}

// based on https://github.com/Olical/binary-search
function binaryFindLarger(item, list) {
	var min = 0;
	var max = list.length - 1;
	var guess;

	var bitwise = (max <= 2147483647) ? true : false;
	if (bitwise) {
		while (min <= max) {
			guess = (min + max) >> 1;
			if (list[guess] === item) { return guess; }
			else {
				if (list[guess] < item) { min = guess + 1; }
				else { max = guess - 1; }
			}
		}
	} else {
		while (min <= max) {
			guess = Math.floor((min + max) / 2);
			if (list[guess] === item) { return guess; }
			else {
				if (list[guess] < item) { min = guess + 1; }
				else { max = guess - 1; }
			}
		}
	}

	return (min == list.length) ? null : min;

//	return -1;
}

function isEvProp(name) {
	return name[0] === "o" && name[1] === "n";
}

function isSplProp(name) {
	return name[0] === "_";
}

function isStyleProp(name) {
	return name === "style";
}

function repaint(node) {
	node && node.el && node.el.offsetHeight;
}

function isHydrated(vm) {
	return vm.node != null && vm.node.el != null;
}

// tests interactive props where real val should be compared
function isDynProp(tag, attr) {
//	switch (tag) {
//		case "input":
//		case "textarea":
//		case "select":
//		case "option":
			switch (attr) {
				case "value":
				case "checked":
				case "selected":
//				case "selectedIndex":
					return true;
			}
//	}

	return false;
}

function getVm(n) {
	n = n || emptyObj;
	while (n.vm == null && n.parent)
		{ n = n.parent; }
	return n.vm;
}

function VNode() {}

var VNodeProto = VNode.prototype = {
	constructor: VNode,

	type:	null,

	vm:		null,

	// all this stuff can just live in attrs (as defined) just have getters here for it
	key:	null,
	ref:	null,
	data:	null,
	hooks:	null,
	ns:		null,

	el:		null,

	tag:	null,
	attrs:	null,
	body:	null,

	flags:	0,

	_class:	null,
	_diff:	null,

	// pending removal on promise resolution
	_dead:	false,
	// part of longest increasing subsequence?
	_lis:	false,

	idx:	null,
	parent:	null,

	/*
	// break out into optional fluent module
	key:	function(val) { this.key	= val; return this; },
	ref:	function(val) { this.ref	= val; return this; },		// deep refs
	data:	function(val) { this.data	= val; return this; },
	hooks:	function(val) { this.hooks	= val; return this; },		// h("div").hooks()
	html:	function(val) { this.html	= true; return this.body(val); },

	body:	function(val) { this.body	= val; return this; },
	*/
};

function defineText(body) {
	var node = new VNode;
	node.type = TEXT;
	node.body = body;
	return node;
}

// creates a one-shot self-ending stream that redraws target vm
// TODO: if it's already registered by any parent vm, then ignore to avoid simultaneous parent & child refresh

var tagCache = {};

var RE_ATTRS = /\[(\w+)(?:=(\w+))?\]/g;

function cssTag(raw) {
	{
		var cached = tagCache[raw];

		if (cached == null) {
			var tag, id, cls, attr;

			tagCache[raw] = cached = {
				tag:	(tag	= raw.match( /^[-\w]+/))		?	tag[0]						: "div",
				id:		(id		= raw.match( /#([-\w]+)/))		? 	id[1]						: null,
				class:	(cls	= raw.match(/\.([-\w.]+)/))		?	cls[1].replace(/\./g, " ")	: null,
				attrs:	null,
			};

			while (attr = RE_ATTRS.exec(raw)) {
				if (cached.attrs == null)
					{ cached.attrs = {}; }
				cached.attrs[attr[1]] = attr[2] || "";
			}
		}

		return cached;
	}
}

// (de)optimization flags

// forces slow bottom-up removeChild to fire deep willRemove/willUnmount hooks,
var DEEP_REMOVE = 1;
// prevents inserting/removing/reordering of children
var FIXED_BODY = 2;
// enables fast keyed lookup of children via binary search, expects homogeneous keyed body
var KEYED_LIST = 4;
// indicates an vnode match/diff/recycler function for body
var LAZY_LIST = 8;

function initElementNode(tag, attrs, body, flags) {
	var node = new VNode;

	node.type = ELEMENT;

	if (isSet(flags))
		{ node.flags = flags; }

	node.attrs = attrs;

	var parsed = cssTag(tag);

	node.tag = parsed.tag;

	// meh, weak assertion, will fail for id=0, etc.
	if (parsed.id || parsed.class || parsed.attrs) {
		var p = node.attrs || {};

		if (parsed.id && !isSet(p.id))
			{ p.id = parsed.id; }

		if (parsed.class) {
			node._class = parsed.class;		// static class
			p.class = parsed.class + (isSet(p.class) ? (" " + p.class) : "");
		}
		if (parsed.attrs) {
			for (var key in parsed.attrs)
				{ if (!isSet(p[key]))
					{ p[key] = parsed.attrs[key]; } }
		}

//		if (node.attrs !== p)
			node.attrs = p;
	}

	var mergedAttrs = node.attrs;

	if (isSet(mergedAttrs)) {
		if (isSet(mergedAttrs._key))
			{ node.key = mergedAttrs._key; }

		if (isSet(mergedAttrs._ref))
			{ node.ref = mergedAttrs._ref; }

		if (isSet(mergedAttrs._hooks))
			{ node.hooks = mergedAttrs._hooks; }

		if (isSet(mergedAttrs._data))
			{ node.data = mergedAttrs._data; }

		if (isSet(mergedAttrs._flags))
			{ node.flags = mergedAttrs._flags; }

		if (!isSet(node.key)) {
			if (isSet(node.ref))
				{ node.key = node.ref; }
			else if (isSet(mergedAttrs.id))
				{ node.key = mergedAttrs.id; }
			else if (isSet(mergedAttrs.name))
				{ node.key = mergedAttrs.name + (mergedAttrs.type === "radio" || mergedAttrs.type === "checkbox" ? mergedAttrs.value : ""); }
		}
	}

	if (body != null)
		{ node.body = body; }

	return node;
}

function setRef(vm, name, node) {
	var path = ["refs"].concat(name.split("."));
	deepSet(vm, path, node);
}

function setDeepRemove(node) {
	while (node = node.parent)
		{ node.flags |= DEEP_REMOVE; }
}

// vnew, vold
function preProc(vnew, parent, idx, ownVm) {
	if (vnew.type === VMODEL || vnew.type === VVIEW)
		{ return; }

	vnew.parent = parent;
	vnew.idx = idx;
	vnew.vm = ownVm;

	if (vnew.ref != null)
		{ setRef(getVm(vnew), vnew.ref, vnew); }

	var nh = vnew.hooks,
		vh = ownVm && ownVm.hooks;

	if (nh && (nh.willRemove || nh.didRemove) ||
		vh && (vh.willUnmount || vh.didUnmount))
		{ setDeepRemove(vnew); }

	if (isArr(vnew.body))
		{ preProcBody(vnew); }
	else {}
}

function preProcBody(vnew) {
	var body = vnew.body;

	for (var i = 0; i < body.length; i++) {
		var node2 = body[i];

		// remove false/null/undefined
		if (node2 === false || node2 == null)
			{ body.splice(i--, 1); }
		// flatten arrays
		else if (isArr(node2)) {
			insertArr(body, node2, i--, 1);
		}
		else {
			if (node2.type == null)
				{ body[i] = node2 = defineText(""+node2); }

			if (node2.type === TEXT) {
				// remove empty text nodes
				if (node2.body == null || node2.body === "")
					{ body.splice(i--, 1); }
				// merge with previous text node
				else if (i > 0 && body[i-1].type === TEXT) {
					body[i-1].body += node2.body;
					body.splice(i--, 1);
				}
				else
					{ preProc(node2, vnew, i, null); }
			}
			else
				{ preProc(node2, vnew, i, null); }
		}
	}
}

var unitlessProps = {
	animationIterationCount: true,
	boxFlex: true,
	boxFlexGroup: true,
	boxOrdinalGroup: true,
	columnCount: true,
	flex: true,
	flexGrow: true,
	flexPositive: true,
	flexShrink: true,
	flexNegative: true,
	flexOrder: true,
	gridRow: true,
	gridColumn: true,
	order: true,
	lineClamp: true,

	borderImageOutset: true,
	borderImageSlice: true,
	borderImageWidth: true,
	fontWeight: true,
	lineHeight: true,
	opacity: true,
	orphans: true,
	tabSize: true,
	widows: true,
	zIndex: true,
	zoom: true,

	fillOpacity: true,
	floodOpacity: true,
	stopOpacity: true,
	strokeDasharray: true,
	strokeDashoffset: true,
	strokeMiterlimit: true,
	strokeOpacity: true,
	strokeWidth: true
};

function autoPx(name, val) {
	{
		// typeof val === 'number' is faster but fails for numeric strings
		return !isNaN(val) && !unitlessProps[name] ? (val + "px") : val;
	}
}

// assumes if styles exist both are objects or both are strings
function patchStyle(n, o) {
	var ns =     (n.attrs || emptyObj).style;
	var os = o ? (o.attrs || emptyObj).style : null;

	// replace or remove in full
	if (ns == null || isVal(ns))
		{ n.el.style.cssText = ns; }
	else {
		for (var nn in ns) {
			var nv = ns[nn];

			if (os == null || nv != null && nv !== os[nn])
				{ n.el.style[nn] = autoPx(nn, nv); }
		}

		// clean old
		if (os) {
			for (var on in os) {
				if (ns[on] == null)
					{ n.el.style[on] = ""; }
			}
		}
	}
}

var didQueue = [];

function fireHook(hooks, name, o, n, immediate) {
	if (hooks != null) {
		var fn = o.hooks[name];

		if (fn) {
			if (name[0] === "d" && name[1] === "i" && name[2] === "d") {	// did*
				//	console.log(name + " should queue till repaint", o, n);
				immediate ? repaint(o.parent) && fn(o, n) : didQueue.push([fn, o, n]);
			}
			else {		// will*
				//	console.log(name + " may delay by promise", o, n);
				return fn(o, n);		// or pass  done() resolver
			}
		}
	}
}

function drainDidHooks(vm) {
	if (didQueue.length) {
		repaint(vm.node);

		var item;
		while (item = didQueue.shift())
			{ item[0](item[1], item[2]); }
	}
}

var doc = ENV_DOM ? document : null;

function closestVNode(el) {
	while (el._node == null)
		{ el = el.parentNode; }
	return el._node;
}

function createElement(tag, ns) {
	if (ns != null)
		{ return doc.createElementNS(ns, tag); }
	return doc.createElement(tag);
}

function createTextNode(body) {
	return doc.createTextNode(body);
}

function createComment(body) {
	return doc.createComment(body);
}

// ? removes if !recycled
function nextSib(sib) {
	return sib.nextSibling;
}

// ? removes if !recycled
function prevSib(sib) {
	return sib.previousSibling;
}

// TODO: this should collect all deep proms from all hooks and return Promise.all()
function deepNotifyRemove(node) {
	var vm = node.vm;

	var wuRes = vm != null && fireHook(vm.hooks, "willUnmount", vm, vm.data);

	var wrRes = fireHook(node.hooks, "willRemove", node);

	if ((node.flags & DEEP_REMOVE) === DEEP_REMOVE && isArr(node.body)) {
		for (var i = 0; i < node.body.length; i++)
			{ deepNotifyRemove(node.body[i]); }
	}

	return wuRes || wrRes;
}

function _removeChild(parEl, el, immediate) {
	var node = el._node, vm = node.vm;

	if (isArr(node.body)) {
		if ((node.flags & DEEP_REMOVE) === DEEP_REMOVE) {
			for (var i = 0; i < node.body.length; i++)
				{ _removeChild(el, node.body[i].el); }
		}
		else
			{ deepUnref(node); }
	}

	delete el._node;

	parEl.removeChild(el);

	fireHook(node.hooks, "didRemove", node, null, immediate);

	if (vm != null) {
		fireHook(vm.hooks, "didUnmount", vm, vm.data, immediate);
		vm.node = null;
	}
}

// todo: should delay parent unmount() by returning res prom?
function removeChild(parEl, el) {
	var node = el._node;

	// already marked for removal
	if (node._dead) { return; }

	var res = deepNotifyRemove(node);

	if (res != null && isProm(res)) {
		node._dead = true;
		res.then(curry(_removeChild, [parEl, el, true]));
	}
	else
		{ _removeChild(parEl, el); }
}

function deepUnref(node) {
	var obody = node.body;

	for (var i = 0; i < obody.length; i++) {
		var o2 = obody[i];
		delete o2.el._node;

		if (o2.vm != null)
			{ o2.vm.node = null; }

		if (isArr(o2.body))
			{ deepUnref(o2); }
	}
}

function clearChildren(parent) {
	var parEl = parent.el;

	if ((parent.flags & DEEP_REMOVE) === 0) {
		isArr(parent.body) && deepUnref(parent);
		parEl.textContent = null;
	}
	else {
		var el = parEl.firstChild;

		do {
			var next = nextSib(el);
			removeChild(parEl, el);
		} while (el = next);
	}
}

// todo: hooks
function insertBefore(parEl, el, refEl) {
	var node = el._node, inDom = el.parentNode != null;

	// el === refEl is asserted as a no-op insert called to fire hooks
	var vm = (el === refEl || !inDom) ? node.vm : null;

	if (vm != null)
		{ fireHook(vm.hooks, "willMount", vm, vm.data); }

	fireHook(node.hooks, inDom ? "willReinsert" : "willInsert", node);
	parEl.insertBefore(el, refEl);
	fireHook(node.hooks, inDom ? "didReinsert" : "didInsert", node);

	if (vm != null)
		{ fireHook(vm.hooks, "didMount", vm, vm.data); }
}

function insertAfter(parEl, el, refEl) {
	insertBefore(parEl, el, refEl ? nextSib(refEl) : null);
}

var onemit = {};

function emitCfg(cfg) {
	assignObj(onemit, cfg);
}

function emit(evName) {
	var targ = this,
		src = targ;

	var args = sliceArgs(arguments, 1).concat(src, src.data);

	do {
		var evs = targ.onemit;
		var fn = evs ? evs[evName] : null;

		if (fn) {
			fn.apply(targ, args);
			break;
		}
	} while (targ = targ.parent());

	if (onemit[evName])
		{ onemit[evName].apply(targ, args); }
}

var onevent = noop;

function config(newCfg) {
	onevent = newCfg.onevent || onevent;

	{
		if (newCfg.onemit)
			{ emitCfg(newCfg.onemit); }
	}

	
}

function bindEv(el, type, fn) {
	el[type] = fn;
}

function exec(fn, args, e, node, vm) {
	var out = fn.apply(vm, args.concat([e, node, vm, vm.data]));

	// should these respect out === false?
	vm.onevent(e, node, vm, vm.data, args);
	onevent.call(null, e, node, vm, vm.data, args);

	if (out === false) {
		e.preventDefault();
		e.stopPropagation();
	}
}

function handle(e) {
	var node = closestVNode(e.target);
	var vm = getVm(node);

	var evDef = e.currentTarget._node.attrs["on" + e.type], fn, args;

	if (isArr(evDef)) {
		fn = evDef[0];
		args = evDef.slice(1);
		exec(fn, args, e, node, vm);
	}
	else {
		for (var sel in evDef) {
			if (e.target.matches(sel)) {
				var evDef2 = evDef[sel];

				if (isArr(evDef2)) {
					fn = evDef2[0];
					args = evDef2.slice(1);
				}
				else {
					fn = evDef2;
					args = [];
				}

				exec(fn, args, e, node, vm);
			}
		}
	}
}

function patchEvent(node, name, nval, oval) {
	if (nval === oval)
		{ return; }

	var el = node.el;

	if (nval == null || isFunc(nval))
		{ bindEv(el, name, nval); }
	else if (oval == null)
		{ bindEv(el, name, handle); }
}

function remAttr(node, name, asProp) {
	if (name[0] === ".") {
		name = name.substr(1);
		asProp = true;
	}

	if (asProp)
		{ node.el[name] = ""; }
	else
		{ node.el.removeAttribute(name); }
}

// setAttr
// diff, ".", "on*", bool vals, skip _*, value/checked/selected selectedIndex
function setAttr(node, name, val, asProp, initial) {
	var el = node.el;

	if (val == null)
		{ !initial && remAttr(node, name, false); }		// will also removeAttr of style: null
	else if (node.ns != null)
		{ el.setAttribute(name, val); }
	else if (name === "class")
		{ el.className = val; }
	else if (name === "id" || typeof val === "boolean" || asProp)
		{ el[name] = val; }
	else if (name[0] === ".")
		{ el[name.substr(1)] = val; }
	else
		{ el.setAttribute(name, val); }
}

function patchAttrs(vnode, donor, initial) {
	var nattrs = vnode.attrs || emptyObj;
	var oattrs = donor.attrs || emptyObj;

	if (nattrs === oattrs) {
		
	}
	else {
		for (var key in nattrs) {
			var nval = nattrs[key];
			var isDyn = isDynProp(vnode.tag, key);
			var oval = isDyn ? vnode.el[key] : oattrs[key];

			if (nval === oval) {}
			else if (isStyleProp(key))
				{ patchStyle(vnode, donor); }
			else if (isSplProp(key)) {}
			else if (isEvProp(key))
				{ patchEvent(vnode, key, nval, oval); }
			else
				{ setAttr(vnode, key, nval, isDyn, initial); }
		}

		// TODO: bench style.cssText = "" vs removeAttribute("style")
		for (var key in oattrs) {
			!(key in nattrs) &&
			!isSplProp(key) &&
			remAttr(vnode, key, isDynProp(vnode.tag, key) || isEvProp(key));
		}
	}
}

function createView(view, data, key, opts) {
	if (view.type === VVIEW) {
		data	= view.data;
		key		= view.key;
		opts	= view.opts;
		view	= view.view;
	}

	return new ViewModel(view, data, key, opts);
}

//import { XML_NS, XLINK_NS } from './defineSvgElement';
function hydrateBody(vnode) {
	for (var i = 0; i < vnode.body.length; i++) {
		var vnode2 = vnode.body[i];
		var type2 = vnode2.type;

		// ELEMENT,TEXT,COMMENT
		if (type2 <= COMMENT)
			{ insertBefore(vnode.el, hydrate(vnode2)); }		// vnode.el.appendChild(hydrate(vnode2))
		else if (type2 === VVIEW) {
			var vm = createView(vnode2.view, vnode2.data, vnode2.key, vnode2.opts)._redraw(vnode, i, false);		// todo: handle new data updates
			type2 = vm.node.type;
			insertBefore(vnode.el, hydrate(vm.node));
		}
		else if (type2 === VMODEL) {
			var vm = vnode2.vm;
			vm._redraw(vnode, i);					// , false
			type2 = vm.node.type;
			insertBefore(vnode.el, vm.node.el);		// , hydrate(vm.node)
		}
	}
}

//  TODO: DRY this out. reusing normal patch here negatively affects V8's JIT
function hydrate(vnode, withEl) {
	if (vnode.el == null) {
		if (vnode.type === ELEMENT) {
			vnode.el = withEl || createElement(vnode.tag, vnode.ns);

		//	if (vnode.tag === "svg")
		//		vnode.el.setAttributeNS(XML_NS, 'xmlns:xlink', XLINK_NS);

			if (vnode.attrs != null)
				{ patchAttrs(vnode, emptyObj, true); }

			if ((vnode.flags & LAZY_LIST) === LAZY_LIST)	// vnode.body instanceof LazyList
				{ vnode.body.body(vnode); }

			if (isArr(vnode.body))
				{ hydrateBody(vnode); }
			else if (vnode.body != null && vnode.body !== "")
				{ vnode.el.textContent = vnode.body; }
		}
		else if (vnode.type === TEXT)
			{ vnode.el = withEl || createTextNode(vnode.body); }
		else if (vnode.type === COMMENT)
			{ vnode.el = withEl || createComment(vnode.body); }
	}

	vnode.el._node = vnode;

	return vnode.el;
}

// prevent GCC from inlining some large funcs (which negatively affects Chrome's JIT)
//window.syncChildren = syncChildren;
window.lisMove = lisMove;

function nextNode(node, body) {
	return body[node.idx + 1];
}

function prevNode(node, body) {
	return body[node.idx - 1];
}

function parentNode(node) {
	return node.parent;
}

var BREAK = 1;
var BREAK_ALL = 2;

function syncDir(advSib, advNode, insert, sibName, nodeName, invSibName, invNodeName, invInsert) {
	return function(node, parEl, body, state, convTest, lis) {
		var sibNode, tmpSib;

		if (state[sibName] != null) {
			// skip dom elements not created by domvm
			if ((sibNode = state[sibName]._node) == null) {
				state[sibName] = advSib(state[sibName]);
				return;
			}

			if (parentNode(sibNode) !== node) {
				tmpSib = advSib(state[sibName]);
				sibNode.vm != null ? sibNode.vm.unmount(true) : removeChild(parEl, state[sibName]);
				state[sibName] = tmpSib;
				return;
			}
		}

		if (state[nodeName] == convTest)
			{ return BREAK_ALL; }
		else if (state[nodeName].el == null) {
			insert(parEl, hydrate(state[nodeName]), state[sibName]);	// should lis be updated here?
			state[nodeName] = advNode(state[nodeName], body);		// also need to advance sib?
		}
		else if (state[nodeName].el === state[sibName]) {
			state[nodeName] = advNode(state[nodeName], body);
			state[sibName] = advSib(state[sibName]);
		}
		// head->tail or tail->head
		else if (!lis && sibNode === state[invNodeName]) {
			tmpSib = state[sibName];
			state[sibName] = advSib(tmpSib);
			invInsert(parEl, tmpSib, state[invSibName]);
			state[invSibName] = tmpSib;
		}
		else {
			if (lis && state[sibName] != null)
				{ return lisMove(advSib, advNode, insert, sibName, nodeName, parEl, body, sibNode, state); }

			return BREAK;
		}
	};
}

function lisMove(advSib, advNode, insert, sibName, nodeName, parEl, body, sibNode, state) {
	if (sibNode._lis) {
		insert(parEl, state[nodeName].el, state[sibName]);
		state[nodeName] = advNode(state[nodeName], body);
	}
	else {
		// find closest tomb
		var t = binaryFindLarger(sibNode.idx, state.tombs);
		sibNode._lis = true;
		var tmpSib = advSib(state[sibName]);
		insert(parEl, state[sibName], t != null ? body[state.tombs[t]].el : t);

		if (t == null)
			{ state.tombs.push(sibNode.idx); }
		else
			{ state.tombs.splice(t, 0, sibNode.idx); }

		state[sibName] = tmpSib;
	}
}

var syncLft = syncDir(nextSib, nextNode, insertBefore, "lftSib", "lftNode", "rgtSib", "rgtNode", insertAfter);
var syncRgt = syncDir(prevSib, prevNode, insertAfter, "rgtSib", "rgtNode", "lftSib", "lftNode", insertBefore);

function syncChildren(node, donor) {
	var obody	= donor.body,
		parEl	= node.el,
		body	= node.body,
		state = {
			lftNode:	body[0],
			rgtNode:	body[body.length - 1],
			lftSib:		((obody)[0] || emptyObj).el,
			rgtSib:		(obody[obody.length - 1] || emptyObj).el,
		};

	converge:
	while (1) {
//		from_left:
		while (1) {
			var l = syncLft(node, parEl, body, state, null, false);
			if (l === BREAK) { break; }
			if (l === BREAK_ALL) { break converge; }
		}

//		from_right:
		while (1) {
			var r = syncRgt(node, parEl, body, state, state.lftNode, false);
			if (r === BREAK) { break; }
			if (r === BREAK_ALL) { break converge; }
		}

		sortDOM(node, parEl, body, state);
		break;
	}
}

// TODO: also use the state.rgtSib and state.rgtNode bounds, plus reduce LIS range
function sortDOM(node, parEl, body, state) {
	var kids = Array.prototype.slice.call(parEl.childNodes);
	var domIdxs = [];

	for (var k = 0; k < kids.length; k++) {
		var n = kids[k]._node;

		if (n.parent === node)
			{ domIdxs.push(n.idx); }
	}

	// list of non-movable vnode indices (already in correct order in old dom)
	var tombs = longestIncreasingSubsequence(domIdxs).map(function (i) { return domIdxs[i]; });

	for (var i = 0; i < tombs.length; i++)
		{ body[tombs[i]]._lis = true; }

	state.tombs = tombs;

	while (1) {
		var r = syncLft(node, parEl, body, state, null, true);
		if (r === BREAK_ALL) { break; }
	}
}

function alreadyAdopted(vnode) {
	return vnode.el._node.parent !== vnode.parent;
}

function takeSeqIndex(n, obody, fromIdx) {
	return obody[fromIdx];
}

function findSeqThorough(n, obody, fromIdx) {		// pre-tested isView?
	for (; fromIdx < obody.length; fromIdx++) {
		var o = obody[fromIdx];

		if (o.vm != null) {
			// match by key & viewFn || vm
			if (n.type === VVIEW && o.vm.view === n.view && o.vm.key === n.key || n.type === VMODEL && o.vm === n.vm)
				{ return o; }
		}
		else if (!alreadyAdopted(o) && n.tag === o.tag && n.type === o.type && n.key === o.key && (n.flags & ~DEEP_REMOVE) === (o.flags & ~DEEP_REMOVE))
			{ return o; }
	}

	return null;
}

function findHashKeyed(n, obody, fromIdx) {
	return obody[obody._keys[n.key]];
}

/*
// list must be a sorted list of vnodes by key
function findBinKeyed(n, list) {
	var idx = binaryKeySearch(list, n.key);
	return idx > -1 ? list[idx] : null;
}
*/

// have it handle initial hydrate? !donor?
// types (and tags if ELEM) are assumed the same, and donor exists
function patch(vnode, donor) {
	fireHook(donor.hooks, "willRecycle", donor, vnode);

	var el = vnode.el = donor.el;

	var obody = donor.body;
	var nbody = vnode.body;

	el._node = vnode;

	// "" => ""
	if (vnode.type === TEXT && nbody !== obody) {
		el.nodeValue = nbody;
		return;
	}

	if (vnode.attrs != null || donor.attrs != null)
		{ patchAttrs(vnode, donor, false); }

	// patch events

	var oldIsArr = isArr(obody);
	var newIsArr = isArr(nbody);
	var lazyList = (vnode.flags & LAZY_LIST) === LAZY_LIST;

//	var nonEqNewBody = nbody != null && nbody !== obody;

	if (oldIsArr) {
		// [] => []
		if (newIsArr || lazyList)
			{ patchChildren(vnode, donor); }
		// [] => "" | null
		else if (nbody !== obody) {
			if (nbody != null)
				{ el.textContent = nbody; }
			else
				{ clearChildren(donor); }
		}
	}
	else {
		// "" | null => []
		if (newIsArr) {
			clearChildren(donor);
			hydrateBody(vnode);
		}
		// "" | null => "" | null
		else if (nbody !== obody) {
			if (el.firstChild)
				{ el.firstChild.nodeValue = nbody; }
			else
				{ el.textContent = nbody; }
		}
	}

	fireHook(donor.hooks, "didRecycle", donor, vnode);
}

// larger qtys of KEYED_LIST children will use binary search
//const SEQ_FAILS_MAX = 100;

// TODO: modify vtree matcher to work similar to dom reconciler for keyed from left -> from right -> head/tail -> binary
// fall back to binary if after failing nri - nli > SEQ_FAILS_MAX
// while-advance non-keyed fromIdx
// [] => []
function patchChildren(vnode, donor) {
	var nbody		= vnode.body,
		nlen		= nbody.length,
		obody		= donor.body,
		olen		= obody.length,
		isLazy		= (vnode.flags & LAZY_LIST) === LAZY_LIST,
		isFixed		= (vnode.flags & FIXED_BODY) === FIXED_BODY,
		isKeyed		= (vnode.flags & KEYED_LIST) === KEYED_LIST,
		domSync		= !isFixed && vnode.type === ELEMENT,
		doFind		= true,
		find		= (
			isKeyed ? findHashKeyed :				// keyed lists/lazyLists
			isFixed || isLazy ? takeSeqIndex :		// unkeyed lazyLists and FIXED_BODY
			findSeqThorough							// more complex stuff
		);

	if (isKeyed) {
		var keys = {};
		for (var i = 0; i < obody.length; i++)
			{ keys[obody[i].key] = i; }
		obody._keys = keys;
	}

	if (domSync && nlen === 0) {
		clearChildren(donor);
		if (isLazy)
			{ vnode.body = []; }	// nbody.tpl(all);
		return;
	}

	var donor2,
		node2,
		foundIdx,
		patched = 0,
		everNonseq = false,
		fromIdx = 0;		// first unrecycled node (search head)

	if (isLazy) {
		var fnode2 = {key: null};
		var nbodyNew = Array(nlen);
	}

	for (var i = 0; i < nlen; i++) {
		if (isLazy) {
			var remake = false;
			var diffRes = null;

			if (doFind) {
				if (isKeyed)
					{ fnode2.key = nbody.key(i); }

				donor2 = find(fnode2, obody, fromIdx);
			}

			if (donor2 != null) {
                foundIdx = donor2.idx;
				diffRes = nbody.diff(i, donor2);

				// diff returns same, so cheaply adopt vnode without patching
				if (diffRes === true) {
					node2 = donor2;
					node2.parent = vnode;
					node2.idx = i;
					node2._lis = false;
				}
				// diff returns new diffVals, so generate new vnode & patch
				else
					{ remake = true; }
			}
			else
				{ remake = true; }

			if (remake) {
				node2 = nbody.tpl(i);			// what if this is a VVIEW, VMODEL, injected element?
				preProc(node2, vnode, i);

				node2._diff = diffRes != null ? diffRes : nbody.diff(i);

				if (donor2 != null)
					{ patch(node2, donor2); }
			}
			else {
				// TODO: flag tmp FIXED_BODY on unchanged nodes?

				// domSync = true;		if any idx changes or new nodes added/removed
			}

			nbodyNew[i] = node2;
		}
		else {
			var node2 = nbody[i];
			var type2 = node2.type;

			// ELEMENT,TEXT,COMMENT
			if (type2 <= COMMENT) {
				if (donor2 = doFind && find(node2, obody, fromIdx)) {
					patch(node2, donor2);
					foundIdx = donor2.idx;
				}
			}
			else if (type2 === VVIEW) {
				if (donor2 = doFind && find(node2, obody, fromIdx)) {		// update/moveTo
					foundIdx = donor2.idx;
					var vm = donor2.vm._update(node2.data, vnode, i);		// withDOM
				}
				else
					{ var vm = createView(node2.view, node2.data, node2.key, node2.opts)._redraw(vnode, i, false); }	// createView, no dom (will be handled by sync below)

				type2 = vm.node.type;
			}
			else if (type2 === VMODEL) {
				// if the injected vm has never been rendered, this vm._update() serves as the
				// initial vtree creator, but must avoid hydrating (creating .el) because syncChildren()
				// which is responsible for mounting below (and optionally hydrating), tests .el presence
				// to determine if hydration & mounting are needed
				var withDOM = isHydrated(node2.vm);

				var vm = node2.vm._update(node2.data, vnode, i, withDOM);
				type2 = vm.node.type;
			}
		}

		// found donor & during a sequential search ...at search head
		if (!isKeyed && donor2 != null) {
			if (foundIdx === fromIdx) {
				// advance head
				fromIdx++;
				// if all old vnodes adopted and more exist, stop searching
				if (fromIdx === olen && nlen > olen) {
					// short-circuit find, allow loop just create/init rest
					donor2 = null;
					doFind = false;
				}
			}
			else
				{ everNonseq = true; }

			if (olen > 100 && everNonseq && ++patched % 10 === 0)
				{ while (fromIdx < olen && alreadyAdopted(obody[fromIdx]))
					{ fromIdx++; } }
		}
	}

	// replace List w/ new body
	if (isLazy)
		{ vnode.body = nbodyNew; }

	domSync && syncChildren(vnode, donor);
}

// view + key serve as the vm's unique identity
function ViewModel(view, data, key, opts) {
	var vm = this;

	vm.view = view;
	vm.data = data;
	vm.key = key;

	if (opts) {
		vm.opts = opts;
		vm.config(opts);
	}

	var out = isPlainObj(view) ? view : view.call(vm, vm, data, key, opts);

	if (isFunc(out))
		{ vm.render = out; }
	else {
		vm.render = out.render;
		vm.config(out);
	}

	// these must be wrapped here since they're debounced per view
	vm._redrawAsync = raft(function (_) { return vm.redraw(true); });
	vm._updateAsync = raft(function (newData) { return vm.update(newData, true); });

	vm.init && vm.init.call(vm, vm, vm.data, vm.key, opts);
}

var ViewModelProto = ViewModel.prototype = {
	constructor: ViewModel,

	_diff:	null,	// diff cache

	init:	null,
	view:	null,
	key:	null,
	data:	null,
	state:	null,
	api:	null,
	opts:	null,
	node:	null,
	hooks:	null,
	onevent: noop,
	refs:	null,
	render:	null,

	mount: mount,
	unmount: unmount,
	config: function(opts) {
		var t = this;

		if (opts.init)
			{ t.init = opts.init; }
		if (opts.diff)
			{ t.diff = opts.diff; }
		if (opts.onevent)
			{ t.onevent = opts.onevent; }

		// maybe invert assignment order?
		if (opts.hooks)
			{ t.hooks = assignObj(t.hooks || {}, opts.hooks); }

		{
			if (opts.onemit)
				{ t.onemit = assignObj(t.onemit || {}, opts.onemit); }
		}
	},
	parent: function() {
		return getVm(this.node.parent);
	},
	root: function() {
		var p = this.node;

		while (p.parent)
			{ p = p.parent; }

		return p.vm;
	},
	redraw: function(sync) {
		var vm = this;
		sync ? vm._redraw(null, null, isHydrated(vm)) : vm._redrawAsync();
		return vm;
	},
	update: function(newData, sync) {
		var vm = this;
		sync ? vm._update(newData, null, null, isHydrated(vm)) : vm._updateAsync(newData);
		return vm;
	},

	_update: updateSync,
	_redraw: redrawSync,
	_redrawAsync: null,
	_updateAsync: null,
};

function mount(el, isRoot) {
	var vm = this;

	if (isRoot) {
		clearChildren({el: el, flags: 0});

		vm._redraw(null, null, false);

		// if placeholder node doesnt match root tag
		if (el.nodeName.toLowerCase() !== vm.node.tag) {
			hydrate(vm.node);
			insertBefore(el.parentNode, vm.node.el, el);
			el.parentNode.removeChild(el);
		}
		else
			{ insertBefore(el.parentNode, hydrate(vm.node, el), el); }
	}
	else {
		vm._redraw(null, null);

		if (el)
			{ insertBefore(el, vm.node.el); }
	}

	if (el)
		{ drainDidHooks(vm); }

	return vm;
}

// asSub means this was called from a sub-routine, so don't drain did* hook queue
function unmount(asSub) {
	var vm = this;

	var node = vm.node;
	var parEl = node.el.parentNode;

	// edge bug: this could also be willRemove promise-delayed; should .then() or something to make sure hooks fire in order
	removeChild(parEl, node.el);

	if (!asSub)
		{ drainDidHooks(vm); }
}

function reParent(vm, vold, newParent, newIdx) {
	if (newParent != null) {
		newParent.body[newIdx] = vold;
		vold.idx = newIdx;
		vold.parent = newParent;
		vold._lis = false;
	}
	return vm;
}

function redrawSync(newParent, newIdx, withDOM) {
	var isRedrawRoot = newParent == null;
	var vm = this;
	var isMounted = vm.node && vm.node.el && vm.node.el.parentNode;

	var vold = vm.node, oldDiff, newDiff;

	if (vm.diff != null) {
		oldDiff = vm._diff;
		vm._diff = newDiff = vm.diff(vm, vm.data);

		if (vold != null) {
			var cmpFn = isArr(oldDiff) ? cmpArr : cmpObj;
			var isSame = oldDiff === newDiff || cmpFn(oldDiff, newDiff);

			if (isSame)
				{ return reParent(vm, vold, newParent, newIdx); }
		}
	}

	isMounted && fireHook(vm.hooks, "willRedraw", vm, vm.data);

	var vnew = vm.render.call(vm, vm, vm.data, oldDiff, newDiff);

	if (vnew === vold)
		{ return reParent(vm, vold, newParent, newIdx); }

	// todo: test result of willRedraw hooks before clearing refs
	vm.refs = null;

	// always assign vm key to root vnode (this is a de-opt)
	if (vm.key != null && vnew.key !== vm.key)
		{ vnew.key = vm.key; }

	vm.node = vnew;

	if (newParent) {
		preProc(vnew, newParent, newIdx, vm);
		newParent.body[newIdx] = vnew;
	}
	else if (vold && vold.parent) {
		preProc(vnew, vold.parent, vold.idx, vm);
		vold.parent.body[vold.idx] = vnew;
	}
	else
		{ preProc(vnew, null, null, vm); }

	if (withDOM !== false) {
		if (vold) {
			// root node replacement
			if (vold.tag !== vnew.tag || vold.key !== vnew.key) {
				// hack to prevent the replacement from triggering mount/unmount
				vold.vm = vnew.vm = null;

				var parEl = vold.el.parentNode;
				var refEl = nextSib(vold.el);
				removeChild(parEl, vold.el);
				insertBefore(parEl, hydrate(vnew), refEl);

				// another hack that allows any higher-level syncChildren to set
				// reconciliation bounds using a live node
				vold.el = vnew.el;

				// restore
				vnew.vm = vm;
			}
			else
				{ patch(vnew, vold); }
		}
		else
			{ hydrate(vnew); }
	}

	isMounted && fireHook(vm.hooks, "didRedraw", vm, vm.data);

	if (isRedrawRoot && isMounted)
		{ drainDidHooks(vm); }

	return vm;
}

// this also doubles as moveTo
// TODO? @withRedraw (prevent redraw from firing)
function updateSync(newData, newParent, newIdx, withDOM) {
	var vm = this;

	if (newData != null) {
		if (vm.data !== newData) {
			fireHook(vm.hooks, "willUpdate", vm, newData);
			vm.data = newData;

			
		}
	}

	return vm._redraw(newParent, newIdx, withDOM);
}

function defineElement(tag, arg1, arg2, flags) {
	var attrs, body;

	if (arg2 == null) {
		if (isPlainObj(arg1))
			{ attrs = arg1; }
		else
			{ body = arg1; }
	}
	else {
		attrs = arg1;
		body = arg2;
	}

	return initElementNode(tag, attrs, body, flags);
}

//export const XML_NS = "http://www.w3.org/2000/xmlns/";
var SVG_NS = "http://www.w3.org/2000/svg";

function defineSvgElement(tag, arg1, arg2, flags) {
	var n = defineElement(tag, arg1, arg2, flags);
	n.ns = SVG_NS;
	return n;
}

function defineComment(body) {
	var node = new VNode;
	node.type = COMMENT;
	node.body = body;
	return node;
}

// placeholder for declared views
function VView(view, data, key, opts) {
	this.view = view;
	this.data = data;
	this.key = key;
	this.opts = opts;
}

VView.prototype = {
	constructor: VView,

	type: VVIEW,
	view: null,
	data: null,
	key: null,
	opts: null,
};

function defineView(view, data, key, opts) {
	return new VView(view, data, key, opts);
}

// placeholder for injected ViewModels
function VModel(vm) {
	this.vm = vm;
}

VModel.prototype = {
	constructor: VModel,

	type: VMODEL,
	vm: null,
};

function injectView(vm) {
//	if (vm.node == null)
//		vm._redraw(null, null, false);

//	return vm.node;

	return new VModel(vm);
}

function injectElement(el) {
	var node = new VNode;
	node.type = ELEMENT;
	node.el = node.key = el;
	return node;
}

function lazyList(items, cfg) {
	var len = items.length;

	var self = {
		items: items,
		length: len,
		// defaults to returning item identity (or position?)
		key: function(i) {
			return cfg.key(items[i], i);
		},
		// default returns 0?
		diff: function(i, donor) {
			var newVals = cfg.diff(items[i], i);
			if (donor == null)
				{ return newVals; }
			var oldVals = donor._diff;
			var same = newVals === oldVals || isArr(oldVals) ? cmpArr(newVals, oldVals) : cmpObj(newVals, oldVals);
			return same || newVals;
		},
		tpl: function(i) {
			return cfg.tpl(items[i], i);
		},
		map: function(tpl) {
			cfg.tpl = tpl;
			return self;
		},
		body: function(vnode) {
			var nbody = Array(len);

			for (var i = 0; i < len; i++) {
				var vnode2 = self.tpl(i);

			//	if ((vnode.flags & KEYED_LIST) === KEYED_LIST && self. != null)
			//		vnode2.key = getKey(item);

				vnode2._diff = self.diff(i);			// holds oldVals for cmp

				nbody[i] = vnode2;

				// run preproc pass (should this be just preProc in above loop?) bench
				preProc(vnode2, vnode, i);
			}

			// replace List with generated body
			vnode.body = nbody;
		}
	};

	return self;
}

var nano = {
	config: config,

	ViewModel: ViewModel,
	VNode: VNode,

	createView: createView,

	defineElement: defineElement,
	defineSvgElement: defineSvgElement,
	defineText: defineText,
	defineComment: defineComment,
	defineView: defineView,

	injectView: injectView,
	injectElement: injectElement,

	lazyList: lazyList,

	FIXED_BODY: FIXED_BODY,
	DEEP_REMOVE: DEEP_REMOVE,
	KEYED_LIST: KEYED_LIST,
	LAZY_LIST: LAZY_LIST,
};

function protoPatch(n, doRepaint) {
	patch$1(this, n, doRepaint);
}

// newNode can be either {class: style: } or full new VNode
// will/didPatch hooks?
function patch$1(o, n, doRepaint) {
	if (n.type != null) {
		// no full patching of view roots, just use redraw!
		if (o.vm != null)
			{ return; }

		preProc(n, o.parent, o.idx, null);
		o.parent.body[o.idx] = n;
		patch(n, o);
		doRepaint && repaint(n);
		drainDidHooks(getVm(n));
	}
	else {
		// TODO: re-establish refs

		// shallow-clone target
		var donor = Object.create(o);
		// fixate orig attrs
		donor.attrs = assignObj({}, o.attrs);
		// assign new attrs into live targ node
		var oattrs = assignObj(o.attrs, n);
		// prepend any fixed shorthand class
		if (o._class != null) {
			var aclass = oattrs.class;
			oattrs.class = aclass != null && aclass !== "" ? o._class + " " + aclass : o._class;
		}

		patchAttrs(o, donor);

		doRepaint && repaint(o);
	}
}

VNodeProto.patch = protoPatch;

function nextSubVms(n, accum) {
	var body = n.body;

	if (isArr(body)) {
		for (var i = 0; i < body.length; i++) {
			var n2 = body[i];

			if (n2.vm != null)
				{ accum.push(n2.vm); }
			else
				{ nextSubVms(n2, accum); }
		}
	}

	return accum;
}

function defineElementSpread(tag) {
	var args = arguments;
	var len = args.length;
	var body, attrs;

	if (len > 1) {
		var bodyIdx = 1;

		if (isPlainObj(args[1])) {
			attrs = args[1];
			bodyIdx = 2;
		}

		if (len === bodyIdx + 1 && (isVal(args[bodyIdx]) || isArr(args[bodyIdx]) || attrs && (attrs._flags & LAZY_LIST) === LAZY_LIST))
			{ body = args[bodyIdx]; }
		else
			{ body = sliceArgs(args, bodyIdx); }
	}

	return initElementNode(tag, attrs, body);
}

function defineSvgElementSpread() {
	var n = defineElementSpread.apply(null, arguments);
	n.ns = SVG_NS;
	return n;
}

ViewModelProto.emit = emit;
ViewModelProto.onemit = null;

ViewModelProto.body = function() {
	return nextSubVms(this.node, []);
};

nano.defineElementSpread = defineElementSpread;
nano.defineSvgElementSpread = defineSvgElementSpread;

return nano;

})));
//# sourceMappingURL=domvm.micro.js.map


/***/ }),
/* 43 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var html_1 = __webpack_require__(2);
var Exporter = /** @class */ (function () {
    function Exporter(_name, _version, _view) {
        this._name = _name;
        this._version = _version;
        this._view = _view;
    }
    Exporter.prototype.pdf = function (config) {
        this._rawExport(config, "pdf", this._view);
    };
    Exporter.prototype.png = function (config) {
        this._rawExport(config, "png", this._view);
    };
    Exporter.prototype._rawExport = function (config, mode, view) {
        config = config || {};
        config.url = config.url || "https://export.dhtmlx.ru/" + this._name + "/" + mode;
        config.url += "/" + this._version;
        var html = "<head><style>" + html_1.getPageCss() + "</style></head>" + view.getRootView().node.el.parentNode.innerHTML;
        var t = document.createElement("form");
        // t.style.cssText = "position:absolute; left:-1000px; visibility:hidden;";
        t.setAttribute("method", "POST");
        t.setAttribute("action", config.url);
        t.innerHTML = "<input type=\"hidden\" name=\"raw\"><input type=\"hidden\" name=\"config\">";
        t.childNodes[0].value = html;
        t.childNodes[1].value = JSON.stringify(config);
        document.body.appendChild(t);
        t.submit();
        setTimeout(function () {
            t.parentNode.removeChild(t);
        }, 100);
    };
    return Exporter;
}());
exports.Exporter = Exporter;


/***/ }),
/* 44 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var linkPaths_1 = __webpack_require__(24);
var colors = ["#FF9800", "#607D8B", "#00C7B5", "#03A9F4", "#9575CD", "#F06292"];
function placeOrgonogram(data, config) {
    var roots = data.getRoots();
    if (roots.length !== 1) {
        return;
    }
    var root = data.getItem(roots[0]);
    setLocalSizes(data, root, config, 0, 0);
    setGlobalSizes(data, root, 0, 0, config, 0);
}
exports.placeOrgonogram = placeOrgonogram;
function setLocalSizes(data, shape, config, vbranch, extra) {
    var kids = shape.$kids;
    var vertical = shape.dir === "vertical";
    var correction = vertical ? config.margin.itemX / 2 : 0;
    var kidsWidth = 0;
    if (shape.open !== false && kids) {
        var count = 0;
        for (var i = 0; i < kids.length; i++) {
            var sub = data.getItem(kids[i][1]);
            if (!sub.hidden) {
                var dx = setLocalSizes(data, sub, config, vbranch + correction, correction);
                if (vertical) {
                    kidsWidth = Math.max(kidsWidth, dx);
                }
                else {
                    kidsWidth += dx;
                }
                count++;
            }
        }
        if (count && !vertical) {
            kidsWidth += (count - 1) * config.margin.itemX;
        }
        shape.$count = count;
    }
    kidsWidth = Math.max(shape.width, kidsWidth);
    if (!vertical) {
        var gridStep = config.gridStep || 1;
        var width = (kidsWidth - shape.width) / 2 + vbranch;
        shape.x = Math.ceil(width / gridStep) * gridStep;
    }
    else {
        shape.x = vbranch;
    }
    shape.y = 0;
    shape.$width = kidsWidth;
    return kidsWidth + extra;
}
function setGlobalSizes(data, shape, left, top, config, level) {
    var kids = shape.$kids;
    var vertical = shape.dir === "vertical";
    var localtop = 0;
    shape.x += left;
    shape.y += top;
    if (config.gridStep) {
        shape.y = Math.ceil(shape.y / config.gridStep) * config.gridStep;
    }
    top += shape.height + config.margin.itemY;
    if (shape.open !== false && kids) {
        var sub = void 0;
        for (var i = 0; i < kids.length; i++) {
            sub = data.getItem(kids[i][1]);
            if (!sub.hidden) {
                var pos = setGlobalSizes(data, sub, left, top, config, level + 1);
                if (vertical) {
                    top += pos + config.margin.itemY;
                    localtop += pos + config.margin.itemY;
                }
                else {
                    localtop = Math.max(localtop, pos + config.margin.itemY);
                    left += sub.$width + config.margin.itemX;
                }
                linkPaths_1.directLinkPath(data.getItem(kids[i][0]), shape, sub, config);
            }
        }
    }
    if (kids) {
        var firstChildColor = data.getItem(kids[0][1]).headerColor;
        shape.$expandColor = firstChildColor || colors[(level + 1) % colors.length];
    }
    shape.headerColor = shape.headerColor || colors[level % colors.length];
    return shape.height + localtop;
}


/***/ }),
/* 45 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var events_1 = __webpack_require__(7);
var types_1 = __webpack_require__(12);
var Selection = /** @class */ (function () {
    function Selection(_config, data, events) {
        var _this = this;
        this.events = events || new events_1.EventSystem();
        this._data = data;
        this._data.events.on(types_1.DataEvents.removeAll, function () {
            _this._selected = null;
        });
        this._data.events.on(types_1.DataEvents.change, function () {
            if (_this._selected) {
                var near = _this._data.getNearId(_this._selected);
                if (near !== _this._selected) {
                    var old = _this._data.getItem(_this._selected);
                    if (old) {
                        old.$selected = false;
                    }
                    _this._selected = null;
                    if (near) {
                        _this.add(near);
                    }
                }
            }
        });
    }
    Selection.prototype.getId = function () {
        return this._selected;
    };
    Selection.prototype.getItem = function () {
        if (this._selected) {
            return this._data.getItem(this._selected);
        }
        return null;
    };
    Selection.prototype.remove = function (id) {
        id = id || this._selected;
        if (!id) {
            return true;
        }
        if (this.events.fire(types_1.SelectionEvents.beforeUnSelect, [id])) {
            this._data.update(id, { $selected: false }, true);
            this._selected = null;
            this.events.fire(types_1.SelectionEvents.afterUnSelect, [id]);
            return true;
        }
        return false;
    };
    Selection.prototype.add = function (id) {
        if (this._selected === id) {
            return;
        }
        this.remove();
        if (this.events.fire(types_1.SelectionEvents.beforeSelect, [id])) {
            this._selected = id;
            this._data.update(id, { $selected: true }, true);
            this.events.fire(types_1.SelectionEvents.afterSelect, [id]);
        }
    };
    return Selection;
}());
exports.Selection = Selection;


/***/ }),
/* 46 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(Promise) {
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
var helpers_1 = __webpack_require__(5);
var types_1 = __webpack_require__(4);
var Loader = /** @class */ (function () {
    function Loader(parent, changes) {
        this._parent = parent;
        this._changes = changes; // todo: [dirty] mutation
    }
    Loader.prototype.load = function (url, driver) {
        var _this = this;
        // TODO: change way for checking lazyLoad
        if (url.config && !this._parent.events.fire(types_1.DataEvents.beforeLazyLoad, [])) {
            return;
        }
        return (this._parent.loadData = url
            .load()
            .then(function (data) {
            if (data) {
                return _this.parse(data, driver);
            }
            else {
                return [];
            }
        })
            .catch(function (error) {
            _this._parent.events.fire(types_1.DataEvents.loadError, [error]);
        }));
    };
    Loader.prototype.parse = function (data, driver) {
        var _this = this;
        if (driver === void 0) { driver = "json"; }
        if (driver === "json" && !helpers_1.hasJsonOrArrayStructure(data)) {
            this._parent.events.fire(types_1.DataEvents.loadError, ["Uncaught SyntaxError: Unexpected end of input"]);
        }
        driver = helpers_1.toDataDriver(driver);
        data = driver.toJsonArray(data);
        if (!(data instanceof Array)) {
            var totalCount = data.total_count - 1;
            var from_1 = data.from;
            data = data.data;
            if (this._parent.getLength() === 0) {
                var newData = [];
                for (var i = 0, j = 0; i <= totalCount; i++) {
                    if (i >= from_1 && i <= from_1 + data.length - 1) {
                        newData.push(data[j]);
                        j++;
                    }
                    else {
                        newData.push({ $empty: true });
                    }
                }
                data = newData;
            }
            else {
                data.forEach(function (newItem, i) {
                    var index = from_1 + i;
                    var oldId = _this._parent.getId(index);
                    if (oldId) {
                        var emptyItem = _this._parent.getItem(oldId);
                        if (emptyItem && emptyItem.$empty) {
                            _this._parent.changeId(oldId, newItem.id, true);
                            _this._parent.update(newItem.id, __assign(__assign({}, newItem), { $empty: undefined }), true);
                        }
                    }
                    else {
                        helpers_1.dhxWarning("item not found");
                    }
                });
                this._parent.events.fire(types_1.DataEvents.afterLazyLoad, [from_1, data.length]);
                this._parent.events.fire(types_1.DataEvents.change);
                return data;
            }
        }
        if (this._parent.getInitialData()) {
            this._parent.removeAll();
        }
        this._parent.$parse(data);
        return data;
    };
    Loader.prototype.save = function (url) {
        var _this = this;
        var _loop_1 = function (el) {
            if (el.saving || el.pending) {
                helpers_1.dhxWarning("item is saving");
            }
            else {
                var prevEl_1 = this_1._findPrevState(el.id);
                if (prevEl_1 && prevEl_1.saving) {
                    var pending = new Promise(function (res, rej) {
                        prevEl_1.promise
                            .then(function () {
                            el.pending = false;
                            res(_this._setPromise(el, url));
                        })
                            .catch(function (err) {
                            _this._removeFromOrder(prevEl_1);
                            _this._setPromise(el, url);
                            helpers_1.dhxWarning(err);
                            rej(err);
                        });
                    });
                    this_1._addToChain(pending);
                    el.pending = true;
                }
                else {
                    this_1._setPromise(el, url);
                }
            }
        };
        var this_1 = this;
        for (var _i = 0, _a = this._changes.order; _i < _a.length; _i++) {
            var el = _a[_i];
            _loop_1(el);
        }
        this._parent.saveData.then(function () {
            _this._saving = false;
        });
    };
    Loader.prototype._setPromise = function (el, url) {
        var _this = this;
        el.promise = url.save(el.obj, el.status);
        el.promise
            .then(function () {
            _this._removeFromOrder(el);
        })
            .catch(function (err) {
            el.saving = false;
            el.error = true;
            helpers_1.dhxError(err);
        });
        el.saving = true;
        this._saving = true;
        this._addToChain(el.promise);
        return el.promise;
    };
    Loader.prototype._addToChain = function (promise) {
        // eslint-disable-next-line @typescript-eslint/no-misused-promises
        if (this._parent.saveData && this._saving) {
            this._parent.saveData = this._parent.saveData.then(function () { return promise; });
        }
        else {
            this._parent.saveData = promise;
        }
    };
    Loader.prototype._findPrevState = function (id) {
        for (var _i = 0, _a = this._changes.order; _i < _a.length; _i++) {
            var el = _a[_i];
            if (el.id === id) {
                return el;
            }
        }
        return null;
    };
    Loader.prototype._removeFromOrder = function (el) {
        this._changes.order = this._changes.order.filter(function (item) { return !helpers_1.isEqualObj(item, el); });
    };
    return Loader;
}());
exports.Loader = Loader;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(8)))

/***/ }),
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var xml_1 = __webpack_require__(48);
var ARRAY_NAME = "items";
var ITEM_NAME = "item";
// convert xml tag to js object, all subtags and attributes are mapped to the properties of result object
function tagToObject(tag, initialObj) {
    initialObj = initialObj || {};
    // map attributes
    var a = tag.attributes;
    if (a && a.length) {
        for (var i = 0; i < a.length; i++) {
            initialObj[a[i].name] = a[i].value;
        }
    }
    // map subtags
    var b = tag.childNodes;
    for (var i = 0; i < b.length; i++) {
        if (b[i].nodeType === 1) {
            var name_1 = b[i].tagName;
            if (initialObj[name_1]) {
                if (typeof initialObj[name_1].push !== "function") {
                    initialObj[name_1] = [initialObj[name_1]];
                }
                initialObj[name_1].push(tagToObject(b[i], {}));
            }
            else {
                initialObj[name_1] = tagToObject(b[i], {}); // sub-object for complex subtags
            }
        }
    }
    return initialObj;
}
var XMLDriver = /** @class */ (function () {
    function XMLDriver() {
    }
    XMLDriver.prototype.toJsonArray = function (data) {
        return this.getRows(data);
    };
    XMLDriver.prototype.toJsonObject = function (data) {
        var doc;
        if (typeof data === "string") {
            doc = this._fromString(data);
        }
        return tagToObject(doc);
    };
    XMLDriver.prototype.serialize = function (data) {
        return xml_1.jsonToXML(data);
    };
    XMLDriver.prototype.getFields = function (row) {
        return row;
    };
    XMLDriver.prototype.getRows = function (data) {
        if (typeof data === "string") {
            data = this._fromString(data);
        }
        if (data) {
            var childNodes = data.childNodes && data.childNodes[0] && data.childNodes[0].childNodes;
            if (!childNodes || !childNodes.length) {
                return null;
            }
            return this._getRows(childNodes);
        }
        return [];
    };
    XMLDriver.prototype._getRows = function (nodes) {
        var result = [];
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].tagName === ITEM_NAME) {
                result.push(this._nodeToJS(nodes[i]));
            }
        }
        return result;
    };
    XMLDriver.prototype._fromString = function (data) {
        try {
            return new DOMParser().parseFromString(data, "text/xml");
        }
        catch (_a) {
            return null;
        }
    };
    XMLDriver.prototype._nodeToJS = function (node) {
        var result = {};
        if (this._haveAttrs(node)) {
            var attrs = node.attributes;
            for (var i = 0; i < attrs.length; i++) {
                var _a = attrs[i], name_2 = _a.name, value = _a.value;
                result[name_2] = this._toType(value);
            }
        }
        if (node.nodeType === 3) {
            result.value = result.value || this._toType(node.textContent);
            return result;
        }
        var childNodes = node.childNodes;
        if (childNodes) {
            for (var i = 0; i < childNodes.length; i++) {
                var subNode = childNodes[i];
                var tag = subNode.tagName;
                if (!tag) {
                    continue;
                }
                if (tag === ARRAY_NAME && subNode.childNodes) {
                    result[tag] = this._getRows(subNode.childNodes);
                }
                else {
                    if (this._haveAttrs(subNode)) {
                        result[tag] = this._nodeToJS(subNode);
                    }
                    else {
                        result[tag] = this._toType(subNode.textContent);
                    }
                }
            }
        }
        return result;
    };
    XMLDriver.prototype._toType = function (val) {
        if (val === "false" || val === "true") {
            return val === "true";
        }
        if (!isNaN(val)) {
            return Number(val);
        }
        return val;
    };
    XMLDriver.prototype._haveAttrs = function (node) {
        return node.attributes && node.attributes.length;
    };
    return XMLDriver;
}());
exports.XMLDriver = XMLDriver;


/***/ }),
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var INDENT_STEP = 4;
function ws(count) {
    return " ".repeat(count);
}
function itemToXML(item, indent) {
    if (indent === void 0) { indent = INDENT_STEP; }
    var result = ws(indent) + "<item>\n";
    for (var key in item) {
        if (Array.isArray(item[key])) {
            result += ws(indent + INDENT_STEP) + ("<" + key + ">\n");
            result +=
                item[key].map(function (subItem) { return itemToXML(subItem, indent + INDENT_STEP * 2); }).join("\n") +
                    "\n";
            result += ws(indent + INDENT_STEP) + ("</" + key + ">\n");
        }
        else {
            result += ws(indent + INDENT_STEP) + ("<" + key + ">" + item[key] + "</" + key + ">\n");
        }
    }
    result += ws(indent) + "</item>";
    return result;
}
function jsonToXML(data, root) {
    if (root === void 0) { root = "root"; }
    var result = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<" + root + ">";
    for (var i = 0; i < data.length; i++) {
        result += "\n" + itemToXML(data[i]);
    }
    return result + ("\n</" + root + ">");
}
exports.jsonToXML = jsonToXML;


/***/ }),
/* 49 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var helpers_1 = __webpack_require__(5);
var Sort = /** @class */ (function () {
    function Sort() {
    }
    Sort.prototype.sort = function (array, by) {
        var _this = this;
        if (by.rule && typeof by.rule === "function") {
            this._sort(array, by);
        }
        else if (by.by) {
            by.rule = function (a, b) {
                var aa = _this._checkVal(by.as, a[by.by]);
                var bb = _this._checkVal(by.as, b[by.by]);
                return helpers_1.naturalCompare(aa.toString(), bb.toString());
            };
            this._sort(array, by);
        }
    };
    Sort.prototype._checkVal = function (method, val) {
        return method ? method.call(this, val) : val;
    };
    Sort.prototype._sort = function (arr, conf) {
        var _this = this;
        var dir = {
            asc: 1,
            desc: -1,
        };
        return arr.sort(function (a, b) {
            return conf.rule.call(_this, a, b) * (dir[conf.dir] || dir.asc);
        });
    };
    return Sort;
}());
exports.Sort = Sort;


/***/ }),
/* 50 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __spreadArrays = (this && this.__spreadArrays) || function () {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
};
Object.defineProperty(exports, "__esModule", { value: true });
var core_1 = __webpack_require__(1);
var datacollection_1 = __webpack_require__(26);
var dataproxy_1 = __webpack_require__(10);
var helpers_1 = __webpack_require__(5);
var types_1 = __webpack_require__(4);
function addToOrder(store, obj, parent, index) {
    if (index !== undefined && index !== -1 && store[parent] && store[parent][index]) {
        store[parent].splice(index, 0, obj);
    }
    else {
        if (!store[parent]) {
            store[parent] = [];
        }
        store[parent].push(obj);
    }
}
var TreeCollection = /** @class */ (function (_super) {
    __extends(TreeCollection, _super);
    function TreeCollection(config, events) {
        var _a;
        var _this = _super.call(this, config, events) || this;
        var root = (_this._root = "_ROOT_" + core_1.uid());
        _this._childs = (_a = {}, _a[root] = [], _a);
        _this._initChilds = null;
        return _this;
    }
    TreeCollection.prototype.add = function (obj, index, parent) {
        var _this = this;
        if (index === void 0) { index = -1; }
        if (parent === void 0) { parent = this._root; }
        if (typeof obj !== "object") {
            obj = {
                value: obj,
            };
        }
        if (Array.isArray(obj)) {
            return obj.map(function (element, key) {
                return _this._add(element, index, parent, key);
            });
        }
        else {
            return this._add(obj, index, parent);
        }
    };
    TreeCollection.prototype.getRoot = function () {
        return this._root;
    };
    TreeCollection.prototype.getParent = function (id, asObj) {
        if (asObj === void 0) { asObj = false; }
        if (!this._pull[id]) {
            return null;
        }
        var parent = this._pull[id].parent;
        return asObj ? this._pull[parent] : parent;
    };
    TreeCollection.prototype.getItems = function (id) {
        if (this._childs && this._childs[id]) {
            return this._childs[id];
        }
        return [];
    };
    TreeCollection.prototype.getLength = function (id) {
        if (id === void 0) { id = this._root; }
        if (!this._childs[id]) {
            return null;
        }
        return this._childs[id].length;
    };
    TreeCollection.prototype.removeAll = function (id) {
        var _a;
        if (!id) {
            _super.prototype.removeAll.call(this);
            var root = this._root;
            this._initChilds = null;
            this._childs = (_a = {}, _a[root] = [], _a);
        }
        else if (this._childs[id]) {
            var childs = __spreadArrays(this._childs[id]);
            for (var _i = 0, childs_1 = childs; _i < childs_1.length; _i++) {
                var child = childs_1[_i];
                this.remove(child.id);
            }
        }
    };
    TreeCollection.prototype.getIndex = function (id) {
        var parent = this.getParent(id);
        if (!parent || !this._childs[parent]) {
            return -1;
        }
        return core_1.findIndex(this._childs[parent], function (item) { return item.id === id; });
    };
    TreeCollection.prototype.sort = function (by) {
        var _this = this;
        if (!by) {
            this._childs = {};
            // [dirty]
            this._parse_data(Object.keys(this._pull).map(function (key) { return _this._pull[key]; }));
            if (this._filters) {
                for (var key in this._filters) {
                    var filter = this._filters[key];
                    this.filter(filter.rule, filter.config);
                }
            }
        }
        else {
            for (var key in this._childs) {
                this._sort.sort(this._childs[key], by);
            }
            if (this._initChilds && Object.keys(this._initChilds).length) {
                for (var key in this._initChilds) {
                    this._sort.sort(this._initChilds[key], by);
                }
            }
        }
        this.events.fire(types_1.DataEvents.change);
    };
    TreeCollection.prototype.filter = function (rule, config) {
        var _this = this;
        if (config === void 0) { config = {}; }
        if (!rule) {
            this.restoreOrder();
            return;
        }
        if (!this._initChilds) {
            this._initChilds = this._childs;
        }
        config.type = config.type || types_1.TreeFilterType.all;
        // [todo] we can store multiple filter rules, like in datacollection
        this._filters = {};
        this._filters._ = {
            rule: rule,
            config: config,
        };
        var newChilds = {};
        this._recursiveFilter(rule, config, this._root, 0, newChilds);
        Object.keys(newChilds).forEach(function (key) {
            var parentId = _this.getParent(key);
            var current = _this.getItem(key);
            while (parentId) {
                if (!newChilds[parentId]) {
                    newChilds[parentId] = [];
                }
                if (current && !newChilds[parentId].find(function (x) { return x.id === current.id; })) {
                    newChilds[parentId].push(current);
                }
                current = _this.getItem(parentId);
                parentId = _this.getParent(parentId);
            }
        });
        this._childs = newChilds;
        this.events.fire(types_1.DataEvents.change);
    };
    TreeCollection.prototype.restoreOrder = function () {
        if (this._initChilds) {
            this._childs = this._initChilds;
            this._initChilds = null;
        }
        this.events.fire(types_1.DataEvents.change);
    };
    TreeCollection.prototype.copy = function (id, index, target, targetId) {
        var _this = this;
        if (target === void 0) { target = this; }
        if (targetId === void 0) { targetId = this._root; }
        if (id instanceof Array) {
            return id.map(function (elementId, key) {
                return _this._copy(elementId, index, target, targetId, key);
            });
        }
        else {
            return this._copy(id, index, target, targetId);
        }
    };
    TreeCollection.prototype.move = function (id, index, target, targetId) {
        var _this = this;
        if (target === void 0) { target = this; }
        if (targetId === void 0) { targetId = this._root; }
        if (id instanceof Array) {
            return id.map(function (elementId, key) {
                return _this._move(elementId, index, target, targetId, key);
            });
        }
        else {
            return this._move(id, index, target, targetId);
        }
    };
    TreeCollection.prototype.forEach = function (cb, parent, level) {
        if (parent === void 0) { parent = this._root; }
        if (level === void 0) { level = Infinity; }
        if (!this.haveItems(parent) || level < 1) {
            return;
        }
        var array = this._childs[parent];
        for (var i = 0; i < array.length; i++) {
            cb.call(this, array[i], i, array);
            if (this.haveItems(array[i].id)) {
                this.forEach(cb, array[i].id, --level);
            }
        }
    };
    TreeCollection.prototype.eachChild = function (id, cb, direct, checkItem) {
        if (direct === void 0) { direct = true; }
        if (checkItem === void 0) { checkItem = function () { return true; }; }
        if (!this.haveItems(id)) {
            return;
        }
        for (var i = 0; i < this._childs[id].length; i++) {
            cb.call(this, this._childs[id][i], i);
            if (direct && checkItem(this._childs[id][i])) {
                this.eachChild(this._childs[id][i].id, cb, direct, checkItem);
            }
        }
    };
    TreeCollection.prototype.getNearId = function (id) {
        return id; // for selection
    };
    TreeCollection.prototype.loadItems = function (id, driver) {
        var _this = this;
        if (driver === void 0) { driver = "json"; }
        var url = this.config.autoload + "?id=" + id;
        var proxy = new dataproxy_1.DataProxy(url);
        proxy.load().then(function (data) {
            driver = helpers_1.toDataDriver(driver);
            data = driver.toJsonArray(data);
            _this._parse_data(data, id);
            _this.events.fire(types_1.DataEvents.change);
        });
    };
    TreeCollection.prototype.refreshItems = function (id, driver) {
        if (driver === void 0) { driver = "json"; }
        this.removeAll(id);
        this.loadItems(id, driver);
    };
    TreeCollection.prototype.eachParent = function (id, cb, self) {
        if (self === void 0) { self = false; }
        var item = this.getItem(id);
        if (!item) {
            return;
        }
        if (self) {
            cb.call(this, item);
        }
        if (item.parent === this._root) {
            return;
        }
        var parent = this.getItem(item.parent);
        cb.call(this, parent);
        this.eachParent(item.parent, cb);
    };
    TreeCollection.prototype.haveItems = function (id) {
        return id in this._childs;
    };
    TreeCollection.prototype.canCopy = function (id, target) {
        if (id === target) {
            return false;
        }
        var canCopy = true;
        this.eachParent(target, function (item) { return (item.id === id ? (canCopy = false) : null); }); // locate return string
        return canCopy;
    };
    TreeCollection.prototype.serialize = function (driver, checkItem) {
        if (driver === void 0) { driver = types_1.DataDriver.json; }
        var data = this._serialize(this._root, checkItem);
        var dataDriver = helpers_1.toDataDriver(driver);
        if (dataDriver) {
            return dataDriver.serialize(data);
        }
    };
    TreeCollection.prototype.getId = function (index, parent) {
        if (parent === void 0) { parent = this._root; }
        if (!this._childs[parent] || !this._childs[parent][index]) {
            return;
        }
        return this._childs[parent][index].id;
    };
    // Non public API from suite_6.4
    TreeCollection.prototype.map = function (cb, parent, direct) {
        if (parent === void 0) { parent = this._root; }
        if (direct === void 0) { direct = true; }
        var result = [];
        if (!this.haveItems(parent)) {
            return result;
        }
        for (var i = 0; i < this._childs[parent].length; i++) {
            result.push(cb.call(this, this._childs[parent][i], i, this._childs));
            if (direct) {
                var childResult = this.map(cb, this._childs[parent][i].id, direct);
                result = result.concat(childResult);
            }
        }
        return result;
    };
    TreeCollection.prototype._add = function (obj, index, parent, key) {
        if (index === void 0) { index = -1; }
        if (parent === void 0) { parent = this._root; }
        obj.parent = obj.parent ? obj.parent.toString() : parent;
        if (key > 0 && index !== -1) {
            index = index + 1;
        }
        var id = _super.prototype._add.call(this, obj, index);
        if (Array.isArray(obj.items)) {
            for (var _i = 0, _a = obj.items; _i < _a.length; _i++) {
                var item = _a[_i];
                this.add(item, -1, obj.id);
            }
        }
        return id;
    };
    TreeCollection.prototype._copy = function (id, index, target, targetId, key) {
        if (target === void 0) { target = this; }
        if (targetId === void 0) { targetId = this._root; }
        if (!this.exists(id)) {
            return null;
        }
        var currentChilds = this._childs[id];
        if (key) {
            index = index === -1 ? -1 : index + key;
        }
        if (target === this && !this.canCopy(id, targetId)) {
            return null;
        }
        var itemCopy = helpers_1.copyWithoutInner(this.getItem(id), { items: true });
        if (target.exists(id)) {
            itemCopy.id = core_1.uid();
        }
        if (!helpers_1.isTreeCollection(target)) {
            target.add(itemCopy, index);
            return;
        }
        if (this.exists(id)) {
            itemCopy.parent = targetId;
            if (target !== this && targetId === this._root) {
                itemCopy.parent = target.getRoot();
            }
            target.add(itemCopy, index);
            id = itemCopy.id;
        }
        if (currentChilds) {
            for (var _i = 0, currentChilds_1 = currentChilds; _i < currentChilds_1.length; _i++) {
                var child = currentChilds_1[_i];
                var childId = child.id;
                var childIndex = this.getIndex(childId);
                if (typeof id === "string") {
                    this.copy(childId, childIndex, target, id);
                }
            }
        }
        return id;
    };
    TreeCollection.prototype._move = function (id, index, target, targetId, key) {
        if (target === void 0) { target = this; }
        if (targetId === void 0) { targetId = this._root; }
        if (!this.exists(id)) {
            return null;
        }
        if (key) {
            index = index === -1 ? -1 : index + key;
        }
        if (target !== this) {
            if (!helpers_1.isTreeCollection(target)) {
                // move to datacollection
                target.add(helpers_1.copyWithoutInner(this.getItem(id)), index);
                this.remove(id);
                return;
            }
            var returnId = this.copy(id, index, target, targetId);
            this.remove(id);
            return returnId;
        }
        // move inside
        if (!this.canCopy(id, targetId)) {
            return null;
        }
        var parent = this.getParent(id);
        var parentIndex = this.getIndex(id);
        // get item from parent array and move to target array
        var spliced = this._childs[parent].splice(parentIndex, 1)[0];
        spliced.parent = targetId; // need for next moving, ... not best solution, may be full method for get item
        if (!this._childs[parent].length) {
            delete this._childs[parent];
        }
        if (!this.haveItems(targetId)) {
            this._childs[targetId] = [];
        }
        if (index === -1) {
            index = this._childs[targetId].push(spliced);
        }
        else {
            this._childs[targetId].splice(index, 0, spliced);
        }
        this.events.fire(types_1.DataEvents.change);
        return id;
    };
    TreeCollection.prototype._removeAll = function (id) {
        var _a;
        if (id) {
            var childs = __spreadArrays(this._childs[id]);
            for (var _i = 0, childs_2 = childs; _i < childs_2.length; _i++) {
                var child = childs_2[_i];
                this.remove(child.id);
            }
        }
        else {
            _super.prototype._removeAll.call(this);
            var root = this._root;
            this._initChilds = null;
            this._childs = (_a = {}, _a[root] = [], _a);
        }
    };
    TreeCollection.prototype._removeCore = function (id) {
        if (this._pull[id]) {
            var parent_1 = this.getParent(id);
            this._childs[parent_1] = this._childs[parent_1].filter(function (item) { return item.id !== id; });
            if (parent_1 !== this._root && !this._childs[parent_1].length) {
                delete this._childs[parent_1];
            }
            if (this._initChilds && this._initChilds[parent_1]) {
                this._initChilds[parent_1] = this._initChilds[parent_1].filter(function (item) { return item.id !== id; });
                if (parent_1 !== this._root && !this._initChilds[parent_1].length) {
                    delete this._initChilds[parent_1];
                }
            }
            this._fastDeleteChilds(this._childs, id);
            if (this._initChilds) {
                this._fastDeleteChilds(this._initChilds, id);
            }
        }
    };
    TreeCollection.prototype._addToOrder = function (_order, obj, index) {
        var childs = this._childs;
        var initChilds = this._initChilds;
        var parent = obj.parent;
        this._pull[obj.id] = obj;
        addToOrder(childs, obj, parent, index);
        if (initChilds) {
            addToOrder(initChilds, obj, parent, index);
        }
    };
    TreeCollection.prototype._parse_data = function (data, parent) {
        if (parent === void 0) { parent = this._root; }
        for (var _i = 0, data_1 = data; _i < data_1.length; _i++) {
            var obj = data_1[_i];
            if (this.config.init) {
                obj = this.config.init(obj);
            }
            if (typeof obj !== "object") {
                obj = {
                    value: obj,
                };
            }
            obj.id = obj.id ? obj.id.toString() : core_1.uid();
            obj.parent = obj.parent ? obj.parent.toString() : parent;
            this._pull[obj.id] = obj;
            if (!this._childs[obj.parent]) {
                this._childs[obj.parent] = [];
            }
            this._childs[obj.parent].push(obj);
            if (obj.items && obj.items instanceof Object) {
                this._parse_data(obj.items, obj.id);
            }
        }
    };
    TreeCollection.prototype._fastDeleteChilds = function (target, id) {
        if (this._pull[id]) {
            delete this._pull[id];
        }
        if (!target[id]) {
            return;
        }
        for (var i = 0; i < target[id].length; i++) {
            this._fastDeleteChilds(target, target[id][i].id);
        }
        delete target[id];
    };
    TreeCollection.prototype._recursiveFilter = function (rule, config, current, level, newChilds) {
        var _this = this;
        var childs = this._childs[current];
        if (!childs) {
            return;
        }
        var condition = function (item) {
            switch (config.type) {
                case types_1.TreeFilterType.all: {
                    return true;
                }
                case types_1.TreeFilterType.level: {
                    return level === config.level;
                }
                case types_1.TreeFilterType.leafs: {
                    return !_this.haveItems(item.id);
                }
            }
        };
        if (typeof rule === "function") {
            var customRule = function (item) { return condition(item) && rule(item); };
            var filtered = childs.filter(customRule);
            if (filtered.length) {
                newChilds[current] = filtered;
            }
        }
        else if (rule.by && rule.match) {
            var customRule = function (item) {
                return condition(item) &&
                    item[rule.by] &&
                    item[rule.by]
                        .toString()
                        .toLowerCase()
                        .indexOf(rule.match.toString().toLowerCase()) !== -1;
            };
            var filtered = childs.filter(customRule);
            if (filtered.length) {
                newChilds[current] = filtered;
            }
        }
        for (var _i = 0, childs_3 = childs; _i < childs_3.length; _i++) {
            var child = childs_3[_i];
            this._recursiveFilter(rule, config, child.id, level + 1, newChilds);
        }
    };
    TreeCollection.prototype._serialize = function (parent, fn) {
        var _this = this;
        if (parent === void 0) { parent = this._root; }
        return this.map(function (item) {
            var itemCopy = {};
            for (var key in item) {
                if (key === "parent" || key === "items") {
                    continue;
                }
                itemCopy[key] = item[key];
            }
            if (fn) {
                itemCopy = fn(itemCopy);
            }
            if (_this.haveItems(item.id)) {
                itemCopy.items = _this._serialize(item.id, fn);
            }
            return itemCopy;
        }, parent, false);
    };
    return TreeCollection;
}(datacollection_1.DataCollection));
exports.TreeCollection = TreeCollection;


/***/ }),
/* 51 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
var html_1 = __webpack_require__(2);
var CollectionStore_1 = __webpack_require__(52);
var types_1 = __webpack_require__(4);
var helpers_1 = __webpack_require__(5);
function getPosition(e) {
    var y = e.clientY;
    var element = html_1.locateNode(e);
    if (!element) {
        return null;
    }
    var treeLine = element.childNodes[0];
    var _a = treeLine.getBoundingClientRect(), top = _a.top, height = _a.height;
    return (y - top) / height;
}
function dragEventContent(element, elements, exhaustiveList) {
    if (exhaustiveList === void 0) { exhaustiveList = false; }
    var rect = element.getBoundingClientRect();
    var ghost = document.createElement("div");
    var clone = element.cloneNode(true);
    clone.style.width = rect.width + "px";
    clone.style.height = rect.height + "px";
    clone.style.maxHeight = rect.height + "px";
    clone.style.fontSize = window.getComputedStyle(element.parentElement).fontSize;
    clone.style.opacity = "0.8";
    clone.style.fontSize = window.getComputedStyle(element.parentElement).fontSize;
    if (!exhaustiveList || !elements || !elements.length) {
        ghost.appendChild(clone);
    }
    if (elements && elements.length) {
        elements.forEach(function (node, key) {
            var nodeClone = node.cloneNode(true);
            nodeClone.style.width = rect.width + "px";
            nodeClone.style.height = rect.height + "px";
            nodeClone.style.maxHeight = rect.height + "px";
            nodeClone.style.top = (key + 1) * 12 - rect.height - rect.height * key + "px";
            nodeClone.style.left = (key + 1) * 12 + "px";
            nodeClone.style.opacity = "0.6";
            nodeClone.style.zIndex = "" + (-key - 1);
            ghost.appendChild(nodeClone);
        });
    }
    ghost.className = "dhx_drag-ghost";
    return ghost;
}
var DragManager = /** @class */ (function () {
    function DragManager() {
        var _this = this;
        this._transferData = {};
        this._canMove = true;
        this._selectedIds = [];
        this._onMouseMove = function (e) {
            if (!_this._transferData.id) {
                return;
            }
            var pageX = e.pageX, pageY = e.pageY;
            if (!_this._transferData.ghost) {
                if (Math.abs(_this._transferData.x - pageX) < 3 && Math.abs(_this._transferData.y - pageY) < 3) {
                    return;
                }
                else {
                    var ghost = _this._onDragStart(_this._transferData.id, _this._transferData.targetId);
                    if (!ghost) {
                        _this._endDrop();
                        return;
                    }
                    else {
                        _this._transferData.ghost = ghost;
                        document.body.appendChild(_this._transferData.ghost);
                    }
                }
            }
            _this._moveGhost(pageX, pageY);
            _this._onDrag(e);
        };
        this._onMouseUp = function () {
            if (!_this._transferData.x) {
                return;
            }
            if (_this._transferData.ghost) {
                _this._removeGhost();
                _this._onDrop();
            }
            else {
                _this._endDrop();
            }
            document.removeEventListener("mousemove", _this._onMouseMove);
            document.removeEventListener("mouseup", _this._onMouseUp);
        };
    }
    DragManager.prototype.setItem = function (id, item) {
        CollectionStore_1.collectionStore.setItem(id, item);
    };
    DragManager.prototype.onMouseDown = function (e, selectedIds, itemsForGhost) {
        // onmousedown only for target objects
        if (e.which !== 1) {
            return;
        }
        e.preventDefault();
        document.addEventListener("mousemove", this._onMouseMove);
        document.addEventListener("mouseup", this._onMouseUp);
        var item = html_1.locateNode(e, "dhx_id");
        var id = item && item.getAttribute("dhx_id");
        var targetId = html_1.locate(e, "dhx_widget_id");
        if (selectedIds && selectedIds.includes(id) && selectedIds.length > 1) {
            this._selectedIds = selectedIds;
            this._itemsForGhost = itemsForGhost;
        }
        else {
            this._selectedIds = [];
            this._itemsForGhost = null;
        }
        if (id && targetId) {
            var _a = html_1.getBox(item), left = _a.left, top_1 = _a.top;
            this._transferData.initXOffset = e.pageX - left;
            this._transferData.initYOffset = e.pageY - top_1;
            this._transferData.x = e.pageX;
            this._transferData.y = e.pageY;
            this._transferData.targetId = targetId;
            this._transferData.id = id;
            this._transferData.item = item;
        }
    };
    DragManager.prototype._moveGhost = function (x, y) {
        if (this._transferData.ghost) {
            this._transferData.ghost.style.left = x - this._transferData.initXOffset + "px";
            this._transferData.ghost.style.top = y - this._transferData.initYOffset + "px";
        }
    };
    DragManager.prototype._removeGhost = function () {
        document.body.removeChild(this._transferData.ghost);
    };
    DragManager.prototype._onDrop = function () {
        if (!this._canMove) {
            this._endDrop();
            return;
        }
        var target = CollectionStore_1.collectionStore.getItem(this._lastCollectionId);
        var config = target && target.config;
        if (!target || config.dragMode === "source") {
            this._endDrop();
            return;
        }
        if (target.events.fire(types_1.DragEvents.beforeDrop, [
            this._lastId,
            this._transferData.target,
            this._transferData.id,
        ])) {
            var to = {
                id: this._lastId,
                target: target,
            };
            var from = {
                id: this._transferData.id,
                target: this._transferData.target,
            };
            this._move(from, to);
            to.target.events.fire(types_1.DragEvents.dropComplete, [to.id, this._transferData.dropPosition]);
        }
        this._endDrop();
    };
    DragManager.prototype._onDragStart = function (id, targetId) {
        var target = CollectionStore_1.collectionStore.getItem(targetId);
        var config = target.config;
        if (config.dragMode === "target") {
            return null;
        }
        var item = target.data.getItem(id);
        var ghost = dragEventContent(this._transferData.item, this._itemsForGhost, config.dragItem === "column");
        var ans = target.events.fire(types_1.DragEvents.beforeDrag, [item, ghost, id]);
        if (!ans || !id) {
            return null;
        }
        target.events.fire(types_1.DragEvents.dragStart, [id, this._selectedIds]);
        this._toggleTextSelection(true);
        this._transferData.target = target;
        this._transferData.dragConfig = config;
        return ghost;
    };
    DragManager.prototype._onDrag = function (e) {
        var clientX = e.clientX, clientY = e.clientY;
        var element = document.elementFromPoint(clientX, clientY);
        var collectionId = html_1.locate(element, "dhx_widget_id");
        if (!collectionId) {
            if (this._canMove) {
                this._cancelCanDrop();
            }
            return;
        }
        var target = CollectionStore_1.collectionStore.getItem(collectionId);
        var id = html_1.locate(element, "dhx_id");
        if (!id) {
            this._cancelCanDrop();
            this._lastCollectionId = collectionId;
            this._lastId = null;
            this._canDrop();
            return;
        }
        if (target.config.dropBehaviour === "complex") {
            var pos = getPosition(e);
            if (pos <= 0.25) {
                this._transferData.dropPosition = types_1.DropPosition.top;
            }
            else if (pos >= 0.75) {
                this._transferData.dropPosition = types_1.DropPosition.bot;
            }
            else {
                this._transferData.dropPosition = types_1.DropPosition.in;
            }
        }
        else if (this._lastId === id && this._lastCollectionId === collectionId) {
            return;
        }
        var from = {
            id: this._transferData.id,
            target: this._transferData.target,
        };
        if (target.config.dragMode === "source") {
            return;
        }
        from.target.events.fire(types_1.DragEvents.dragOut, [id, target]);
        if (collectionId !== this._transferData.targetId ||
            !helpers_1.isTreeCollection(from.target.data) ||
            (helpers_1.isTreeCollection(from.target.data) && from.target.data.canCopy(from.id, id))) {
            this._cancelCanDrop(); // clear last
            this._lastId = id;
            this._lastCollectionId = collectionId;
            var canMove = from.target.events.fire(types_1.DragEvents.dragIn, [
                id,
                this._transferData.dropPosition,
                CollectionStore_1.collectionStore.getItem(collectionId),
            ]);
            if (canMove) {
                this._canDrop();
            }
        }
        else {
            this._cancelCanDrop();
        }
    };
    DragManager.prototype._move = function (from, to) {
        var fromData = from.target.data;
        var toData = to.target.data;
        var index = 0;
        var targetId = to.id;
        var behaviour = helpers_1.isTreeCollection(toData) ? to.target.config.dropBehaviour : undefined;
        var gridConfig = from.target.config.columns
            ? from.target.config
            : undefined;
        var isColumnDrag = gridConfig &&
            (gridConfig.dragItem === "complex" || gridConfig.dragItem === "column") &&
            gridConfig.columns.map(function (c) { return c.id; }).filter(function (id) { return id === from.id || id === to.id; }).length;
        if (isColumnDrag && from.target === to.target && from.id !== to.id) {
            var grid = from.target;
            var currentCols = grid.config.columns.map(function (c) { return (__assign({}, c)); });
            var sourceIndex = currentCols.findIndex(function (c) { return c.id === from.id; });
            var targetIndex = currentCols.findIndex(function (c) { return c.id === to.id; });
            currentCols.splice(targetIndex, 0, currentCols.splice(sourceIndex, 1)[0]);
            grid.setColumns(currentCols);
            grid.paint();
            return;
        }
        switch (behaviour) {
            case "child":
                break;
            case "sibling":
                targetId = toData.getParent(targetId);
                index = toData.getIndex(to.id) + 1;
                break;
            case "complex": {
                var dropPosition = this._transferData.dropPosition;
                if (dropPosition === types_1.DropPosition.top) {
                    targetId = toData.getParent(targetId);
                    index = toData.getIndex(to.id);
                }
                else if (dropPosition === types_1.DropPosition.bot) {
                    targetId = toData.getParent(targetId);
                    index = toData.getIndex(to.id) + 1;
                }
                break;
            }
            default:
                // list move
                if (!to.id) {
                    index = -1;
                }
                else if (from.target === to.target && toData.getIndex(from.id) < toData.getIndex(to.id)) {
                    index = toData.getIndex(to.id) - 1;
                }
                else {
                    index = toData.getIndex(to.id);
                }
        }
        if (this._transferData.dragConfig.dragCopy) {
            if (this._selectedIds instanceof Array && this._selectedIds.length > 1) {
                this._selectedIds.map(function (selctedId) {
                    fromData.copy(selctedId, index, toData, targetId);
                    if (index > -1) {
                        index++;
                    }
                });
            }
            else {
                fromData.copy(from.id, index, toData, targetId);
            }
        }
        else {
            if (this._selectedIds instanceof Array && this._selectedIds.length > 1) {
                this._selectedIds.map(function (selctedId) {
                    fromData.move(selctedId, index, toData, targetId);
                    if (index > -1) {
                        index++;
                    }
                });
            }
            else {
                fromData.move(from.id, index, toData, targetId); // typescript bug??
            }
        }
    };
    DragManager.prototype._endDrop = function () {
        this._toggleTextSelection(false);
        if (this._transferData.target) {
            this._transferData.target.events.fire(types_1.DragEvents.dragEnd, [
                this._transferData.id,
                this._selectedIds,
            ]);
        }
        this._cancelCanDrop();
        this._canMove = true;
        this._transferData = {};
        this._lastId = null;
        this._lastCollectionId = null;
    };
    DragManager.prototype._cancelCanDrop = function () {
        this._canMove = false;
        var collection = CollectionStore_1.collectionStore.getItem(this._lastCollectionId);
        if (collection && this._lastId) {
            collection.events.fire(types_1.DragEvents.cancelDrop, [this._lastId]);
        }
        this._lastCollectionId = null;
        this._lastId = null;
    };
    DragManager.prototype._canDrop = function () {
        this._canMove = true;
        var target = CollectionStore_1.collectionStore.getItem(this._lastCollectionId);
        if (target && this._lastId) {
            target.events.fire(types_1.DragEvents.canDrop, [this._lastId, this._transferData.dropPosition]);
        }
    };
    DragManager.prototype._toggleTextSelection = function (add) {
        if (add) {
            document.body.classList.add("dhx_no-select");
        }
        else {
            document.body.classList.remove("dhx_no-select");
        }
    };
    return DragManager;
}());
var dhx = (window.dhxHelpers = window.dhxHelpers || {});
dhx.dragManager = dhx.dragManager || new DragManager();
exports.dragManager = dhx.dragManager;


/***/ }),
/* 52 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var CollectionStore = /** @class */ (function () {
    function CollectionStore() {
        this._store = {};
    }
    CollectionStore.prototype.setItem = function (id, target) {
        this._store[id] = target;
    };
    CollectionStore.prototype.getItem = function (id) {
        if (!this._store[id]) {
            return null;
        }
        return this._store[id];
    };
    return CollectionStore;
}());
var dhx = (window.dhxHelpers = window.dhxHelpers || {});
dhx.collectionStore = dhx.collectionStore || new CollectionStore();
exports.collectionStore = dhx.collectionStore;


/***/ }),
/* 53 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(Promise) {
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var dataproxy_1 = __webpack_require__(10);
var core_1 = __webpack_require__(1);
var ajax_1 = __webpack_require__(18);
var LazyDataProxy = /** @class */ (function (_super) {
    __extends(LazyDataProxy, _super);
    function LazyDataProxy(url, config) {
        var _this = _super.call(this, url) || this;
        _this.config = core_1.extend({
            from: 0,
            limit: 50,
            delay: 50,
            prepare: 0,
        }, config);
        _this.updateUrl(url, { from: _this.config.from, limit: _this.config.limit });
        return _this;
    }
    LazyDataProxy.prototype.load = function () {
        var _this = this;
        return new Promise(function (resolve) {
            if (!_this._timeout) {
                ajax_1.ajax.get(_this.url, { responseType: "text" }).then(resolve);
                _this._cooling = true;
                _this._timeout = setTimeout(function () {
                    return;
                });
            }
            else {
                clearTimeout(_this._timeout);
                _this._timeout = setTimeout(function () {
                    ajax_1.ajax.get(_this.url, { responseType: "text" }).then(resolve);
                    _this._cooling = true;
                }, _this.config.delay);
                if (_this._cooling) {
                    resolve(null);
                    _this._cooling = false;
                }
            }
        });
    };
    return LazyDataProxy;
}(dataproxy_1.DataProxy));
exports.LazyDataProxy = LazyDataProxy;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(8)))

/***/ }),
/* 54 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var events_1 = __webpack_require__(7);
var types_1 = __webpack_require__(25);
var types_2 = __webpack_require__(4);
var Selection = /** @class */ (function () {
    function Selection(_config, data, events) {
        var _this = this;
        this.events = events || new events_1.EventSystem(this);
        this._data = data;
        this._data.events.on(types_2.DataEvents.removeAll, function () {
            _this._selected = null;
        });
        this._data.events.on(types_2.DataEvents.change, function () {
            if (_this._selected) {
                var near = _this._data.getNearId(_this._selected);
                if (near !== _this._selected) {
                    _this._selected = null;
                    if (near) {
                        _this.add(near);
                    }
                }
            }
        });
    }
    Selection.prototype.getId = function () {
        return this._selected;
    };
    Selection.prototype.getItem = function () {
        if (this._selected) {
            return this._data.getItem(this._selected);
        }
        return null;
    };
    Selection.prototype.remove = function (id) {
        id = id || this._selected;
        if (!id) {
            return true;
        }
        if (this.events.fire(types_1.SelectionEvents.beforeUnSelect, [id])) {
            this._data.update(id, { $selected: false });
            this._selected = null;
            this.events.fire(types_1.SelectionEvents.afterUnSelect, [id]);
            return true;
        }
        return false;
    };
    Selection.prototype.add = function (id) {
        if (this._selected === id) {
            return;
        }
        this.remove();
        if (this.events.fire(types_1.SelectionEvents.beforeSelect, [id])) {
            this._selected = id;
            this._data.update(id, { $selected: true });
            this.events.fire(types_1.SelectionEvents.afterSelect, [id]);
        }
    };
    return Selection;
}());
exports.Selection = Selection;


/***/ }),
/* 55 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var __spreadArrays = (this && this.__spreadArrays) || function () {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
};
Object.defineProperty(exports, "__esModule", { value: true });
var dom_1 = __webpack_require__(0);
var baseMetaInfo_1 = __webpack_require__(6);
var Base_1 = __webpack_require__(11);
var en_1 = __webpack_require__(3);
var Line = /** @class */ (function (_super) {
    __extends(Line, _super);
    function Line(config, defaults) {
        return _super.call(this, config, defaults) || this;
    }
    Line.prototype.isConnector = function () {
        return true;
    };
    Line.prototype.getMetaInfo = function () {
        var meta = baseMetaInfo_1.getMeta([{ type: "grid", label: en_1.default.gridStep }]);
        meta.push({
            id: "strokeProps",
            type: "stroke",
            label: "Stroke",
            connector: true,
        });
        return meta;
    };
    Line.prototype.setDefaults = function (config) {
        config.connectType = config.connectType || "elbow";
        config.stroke = config.stroke || "#CCC";
        config.strokeWidth = config.strokeWidth || 2;
        config.cornersRadius = config.cornersRadius || 0;
        return config;
    };
    Line.prototype.render = function () {
        var isSelected = this.config.$selected;
        this.id = this.config.id;
        var linePoints = this._getPoints();
        var arrow = this._getArrowLine() || [];
        return dom_1.sv("g", {
            dhx_id: this.config.id || "",
            _key: this.config.id,
            class: "dhx_diagram_connector " + this.getCss(),
        }, __spreadArrays([
            dom_1.sv("path", {
                d: linePoints,
                fill: "none",
                class: "dhx_diagram_line " + (isSelected ? "dhx_diagram_line--selected" : ""),
                "stroke-dasharray": this._getType(),
                "stroke-linejoin": "round",
                stroke: this.config.stroke,
                "stroke-width": this.config.strokeWidth,
            })
        ], arrow));
    };
    Line.prototype.getBox = function () {
        var conf = __assign({}, this.config);
        var max = conf.points.reduce(function (m, p) {
            m.x = Math.max(m.x, p.x);
            m.y = Math.max(m.y, p.y);
            return m;
        }, { x: 0, y: 0 });
        var width = max.x - conf.x;
        var height = max.y - conf.y;
        var left = conf.x;
        var right = left + width;
        var top = conf.y;
        var bottom = top + height;
        return { left: left, right: right, top: top, bottom: bottom };
    };
    Line.prototype._getType = function () {
        if (this.config.strokeType) {
            this.config.type = this.config.strokeType;
        }
        if (this.config.type) {
            switch (this.config.type) {
                case "line":
                    return "";
                case "dash":
                    return "5, 5";
                default:
                    return "";
            }
        }
    };
    Line.prototype._getPoints = function () {
        return this._getStringPoints();
    };
    Line.prototype._getStringPoints = function () {
        this.config.width = Math.abs(this.config.points[this.config.points.length - 1].x - this.config.points[0].x);
        this.config.height = Math.abs(this.config.points[this.config.points.length - 1].y - this.config.points[0].y);
        this.config.x = this.config.points[0].x;
        this.config.y = this.config.points[0].y;
        return ("M " + this.config.x + "," + this.config.y +
            this.config.points
                .map(function (a) {
                if (a.x1 && a.y1) {
                    return "Q" + a.x1 + "," + a.y1 + " " + a.x + "," + a.y;
                }
                else {
                    return "L " + a.x + "," + a.y;
                }
            })
                .join(" "));
    };
    Line.prototype._getArrowLine = function () {
        var p = this.config.points;
        var startArrow = this.config.backArrow;
        var endArrow = this.config.forwardArrow;
        if (startArrow || endArrow) {
            return [
                startArrow
                    ? this.config.connectType === "straight"
                        ? this._angleArrow(p[1], p[0])
                        : this._arrow(p[1], p[0])
                    : null,
                endArrow
                    ? this.config.connectType === "straight"
                        ? this._angleArrow(p[p.length - 2], p[p.length - 1])
                        : this._arrow(p[p.length - 2], p[p.length - 1])
                    : null,
            ];
        }
    };
    Line.prototype._angleArrow = function (from, to) {
        var xCatet = from.x - to.x;
        var yCatet = from.y - to.y;
        var hypo = Math.sqrt(Math.pow(xCatet, 2) + Math.pow(yCatet, 2));
        var ratio = 1 / hypo;
        var xAdd = xCatet * ratio;
        var yAdd = yCatet * ratio;
        var center = {
            x: to.x,
            y: to.y,
        };
        var w = 5;
        var h = 5;
        var r1x = to.x - w;
        var r1y = to.y - h;
        var r2x = to.x + w;
        var r2y = to.y - h;
        var angle = Math.atan((to.x - from.x) / (to.y - from.y)) * (-180 / 3.14);
        if (from.y > to.y) {
            angle += 180;
        }
        return dom_1.sv("path", {
            d: "M" + r1x + "," + r1y + " L" + center.x + "," + center.y + " L" + r2x + "," + r2y + " Z",
            class: "dhx_diagram_arrow",
            "shape-rendering": "auto",
            stroke: this.config.stroke,
            fill: this.config.stroke,
            transform: "translate(" + xAdd + " " + yAdd + ") rotate(" + angle + "," + center.x + "," + center.y + ")",
        });
    };
    Line.prototype._arrow = function (from, to) {
        var vx = from.x !== to.x;
        var rtl = (vx ? from.x < to.x : from.y < to.y) ? 1 : -1;
        var center = {
            x: to.x - (vx ? rtl : 0),
            y: to.y - (vx ? 0 : rtl),
        };
        var w = 7;
        var h = 5;
        var r1x = to.x - (vx ? w * rtl : h * rtl);
        var r1y = to.y - (vx ? h : w * rtl);
        var r2x = to.x + (vx ? -w * rtl : h * rtl);
        var r2y = to.y - (vx ? -h : w * rtl);
        return dom_1.sv("path", {
            d: "M" + r1x + "," + r1y + " L" + center.x + "," + center.y + " L" + r2x + "," + r2y + " Z",
            class: "dhx_diagram_arrow",
            "shape-rendering": "auto",
            stroke: this.config.stroke,
            fill: this.config.stroke,
        });
    };
    return Line;
}(Base_1.BaseShape));
exports.Line = Line;


/***/ }),
/* 56 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var dom_1 = __webpack_require__(0);
var en_1 = __webpack_require__(3);
var baseMetaInfo_1 = __webpack_require__(6);
var OrgChartCard_1 = __webpack_require__(31);
var OrgChartImgCard = /** @class */ (function (_super) {
    __extends(OrgChartImgCard, _super);
    function OrgChartImgCard() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    OrgChartImgCard.prototype.getMetaInfo = function () {
        return baseMetaInfo_1.getMeta([
            { type: "grid", label: en_1.default.gridStep },
            { type: "color", label: en_1.default.color },
            { type: "position", label: en_1.default.position },
            { type: "size", label: en_1.default.size },
            { type: "title", label: en_1.default.title },
            { type: "text", label: en_1.default.text },
            { type: "img", label: en_1.default.image },
        ]);
    };
    OrgChartImgCard.prototype.setDefaults = function (config, defaults) {
        var width = config.width, height = config.height, text = config.text, title = config.title, headerColor = config.headerColor;
        var widthDefaut = defaults.width ? parseFloat(defaults.width) : 210;
        var heightDefault = defaults.height ? parseFloat(defaults.height) : 90;
        config.width = width || widthDefaut;
        config.height = height || heightDefault;
        config.title = typeof title === "string" ? title : defaults.title || "";
        config.text = typeof text === "string" ? text : defaults.text || "";
        config.headerColor = headerColor || defaults.headerColor || "";
        return config;
    };
    OrgChartImgCard.prototype.getCss = function () {
        return "dhx_diagram_image " + _super.prototype.getCss.call(this);
    };
    OrgChartImgCard.prototype.getContent = function () {
        var _a = this.config, img = _a.img, headerColor = _a.headerColor, title = _a.title, text = _a.text, width = _a.width;
        var src = img ? "" + img : null;
        return [
            dom_1.el("img.dhx_orgcard__img", {
                style: {
                    backgroundColor: img ? null : headerColor,
                },
                src: src,
            }),
            dom_1.el("div.dhx_orgcard__title", title),
            dom_1.el("div.dhx_orgcard__text", {
                class: "" + (title ? "" : "dhx_content_text-alone"),
                style: {
                    maxWidth: width - 80,
                    marginLeft: 80,
                },
            }, text),
        ];
    };
    return OrgChartImgCard;
}(OrgChartCard_1.OrgChartCard));
exports.OrgChartImgCard = OrgChartImgCard;


/***/ }),
/* 57 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
var dom_1 = __webpack_require__(0);
var baseMetaInfo_1 = __webpack_require__(6);
var Base_1 = __webpack_require__(11);
var html_1 = __webpack_require__(2);
var templates_1 = __webpack_require__(16);
var en_1 = __webpack_require__(3);
var DiagramTextShape = /** @class */ (function (_super) {
    __extends(DiagramTextShape, _super);
    function DiagramTextShape(config, parameters) {
        var _this = _super.call(this, config, parameters) || this;
        _this.config = config;
        _this.id = _this.config.id;
        return _this;
    }
    DiagramTextShape.prototype.render = function () {
        var coords = this.getCoords(this.config);
        var _a = this.config, id = _a.id, angle = _a.angle, text = _a.text, fontSize = _a.fontSize, lineHeight = _a.lineHeight, fontWeight = _a.fontWeight, fontStyle = _a.fontStyle;
        var textStyle = {
            fontSize: fontSize + "px",
            fontFamily: "Roboto, Arial, Tahoma, Verdana, sans-serif",
            lineHeight: lineHeight + "px",
            fontWeight: fontWeight,
            fontStyle: fontStyle,
        };
        if (this._oldText && this.config.text !== this._oldText) {
            var stringSize = html_1.getStrSize(text, textStyle);
            this.config.width = stringSize.width + 4;
            this.config.height = stringSize.height + 2;
        }
        this._oldText = this.config.text;
        return dom_1.el("div", {
            _key: id,
            class: "dhx_diagram_flow_item " + this.getCss(),
            dhx_id: id,
            zIndex: 2,
            style: __assign({ position: "absolute", top: coords.y, left: coords.x, transform: "rotate(" + (angle || 0) + "deg)" }, templates_1.getShapeCss(this.config)),
        }, [this.getContent()]);
    };
    DiagramTextShape.prototype.getMetaInfo = function () {
        var meta = baseMetaInfo_1.getMeta([
            { type: "grid", label: en_1.default.gridStep },
            { type: "arrange", label: en_1.default.arrange },
            { type: "text", label: en_1.default.text },
        ]);
        meta.push({ id: "textProps", type: "textProps", label: en_1.default.textProps, alignments: false });
        return meta;
    };
    DiagramTextShape.prototype.setDefaults = function (config, defaults) {
        var width = config.width, height = config.height, fontColor = config.fontColor, fontSize = config.fontSize, fontStyle = config.fontStyle, textAlign = config.textAlign, lineHeight = config.lineHeight, textVerticalAlign = config.textVerticalAlign, text = config.text, fontWeight = config.fontWeight, x = config.x, y = config.y;
        var lineHeightDefault = defaults.lineHeight ? parseFloat(defaults.lineHeight) : 14;
        var fontSizeDefault = defaults.fontSize ? parseFloat(defaults.fontSize) : 14;
        var textStyle = {
            fontSize: (fontSize || fontSizeDefault) + "px",
            fontFamily: "Roboto, Arial, Tahoma, Verdana, sans-serif",
            lineHeight: (lineHeight || lineHeightDefault) + "px",
            fontWeight: fontWeight,
            fontStyle: fontStyle,
        };
        var stringSize = html_1.getStrSize(text, textStyle);
        var widthDefault = defaults.width ? parseFloat(defaults.width) : stringSize.width + 4;
        var heightDefault = defaults.height ? parseFloat(defaults.height) : stringSize.height + 2;
        config.width = width || widthDefault;
        config.height = height || heightDefault;
        config.lineHeight = lineHeight || lineHeightDefault;
        config.fontSize = fontSize || fontSizeDefault;
        config.text = text || defaults.text || "";
        config.fontColor = fontColor || defaults.fontColor || "rgba(0,0,0,0.70)";
        config.textAlign = textAlign || defaults.textAlign || "center";
        config.fontStyle = fontStyle || defaults.fontStyle || "normal";
        config.textVerticalAlign = textVerticalAlign || defaults.textVerticalAlign || "center";
        config.x = x || 0;
        config.y = y || 0;
        return config;
    };
    DiagramTextShape.prototype.getContent = function () {
        return dom_1.el("span.dhx_item_shape", this.config.text);
    };
    return DiagramTextShape;
}(Base_1.BaseShape));
exports.DiagramTextShape = DiagramTextShape;


/***/ }),
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var dom_1 = __webpack_require__(0);
var html_1 = __webpack_require__(2);
var types_1 = __webpack_require__(12);
var view_1 = __webpack_require__(9);
var Toolbar = /** @class */ (function (_super) {
    __extends(Toolbar, _super);
    function Toolbar(events, icons) {
        var _this = _super.call(this, null, {
            height: 50,
            iconWidth: 30,
            gap: 16,
            icons: icons,
        }) || this;
        _this.events = events;
        _this._handlers = {
            onclick: html_1.eventHandler(function (ev) { return html_1.locate(ev); }, {
                dhx_icon: function (ev, id) {
                    _this.events.fire(types_1.DiagramEvents.shapeIconClick, [id, ev]);
                },
            }),
        };
        _this.events.on(types_1.DiagramEvents.shapeMouseDown, function (_id, _e, coords) {
            _this._pressCoords = coords;
        });
        _this.events.on(types_1.DiagramEvents.emptyAreaClick, function () {
            _this._pressCoords = null;
        });
        return _this;
    }
    Toolbar.prototype.render = function (item, size) {
        var config = this.config;
        var icons = this._getIcons(item, config.icons);
        var width = config.iconWidth * icons.length + config.gap;
        var pos = this._getCoords(item, width / size.scale, config.height / size.scale);
        return dom_1.el("div", {
            class: "dhx_popup_toolbar",
            style: {
                display: this._hidden ? "none" : "block",
                maxHeight: this.config.height,
                width: width,
                top: (pos.y - size.top) * size.scale,
                left: (pos.x - size.left) * size.scale,
            },
            onclick: this._handlers.onclick,
        }, [
            dom_1.el("div", {
                class: "dhx_item_toolbar",
            }, icons),
        ]);
    };
    Toolbar.prototype.hide = function () {
        this._hidden = true;
        this.paint();
    };
    Toolbar.prototype.show = function () {
        this._hidden = false;
        this.paint();
    };
    Toolbar.prototype._getIcons = function (item, icons) {
        var tags = [];
        for (var i = 0; i < icons.length; i++) {
            var obj = icons[i];
            if (!obj.check || obj.check(item)) {
                var css = obj.css ? obj.css(item) : "";
                var tag = {
                    _key: obj.id,
                    class: "dhx_icon " + css,
                    dhx_id: obj.id,
                };
                var content = typeof obj.content === "function" ? obj.content(item) : obj.content;
                if (typeof content === "string") {
                    tag[".innerHTML"] = content;
                    tags.push(dom_1.el("div", tag));
                }
                else {
                    tags.push(dom_1.el("div", tag, [content]));
                }
            }
        }
        return tags;
    };
    Toolbar.prototype._getCoords = function (target, width, height) {
        if (target.$shape.isConnector()) {
            if (this._pressCoords) {
                return {
                    x: this._pressCoords.x - 50,
                    y: this._pressCoords.y - 50,
                };
            }
            else {
                return {
                    x: target.points[0].x,
                    y: target.points[0].y,
                };
            }
        }
        var box = target.$shape.getBox();
        var center = box.right / 2 + box.left / 2;
        var gap = 8;
        return {
            x: center - width / 2,
            y: box.top - height - gap,
        };
    };
    return Toolbar;
}(view_1.View));
exports.Toolbar = Toolbar;


/***/ }),
/* 59 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SymmetricTree_1 = __webpack_require__(60);
var Routes_1 = __webpack_require__(61);
var split_1 = __webpack_require__(62);
var helpers_1 = __webpack_require__(33);
var Hola = /** @class */ (function () {
    function Hola() {
    }
    Hola.prototype.layout = function (g, config) {
        g.routes = new Routes_1.default();
        if (!config.full) {
            var c_1 = getParams(g, config);
            if (!config.preserveLocation)
                placeAround(c_1.n, c_1.size);
            minStress(c_1.mx, c_1.n, c_1.n, c_1.size);
            fixOverlap(c_1.n);
            g.setGlobalBox();
            return g;
        }
        var subs = split_1.split(g);
        if (subs.length == 1 && subs[0].root !== null) {
            return new SymmetricTree_1.default().layout(subs[0].g, config);
        }
        g = subs[0].g;
        g.routes = new Routes_1.default();
        var c = getParams(g, config);
        if (!config.preserveLocation)
            placeAround(c.n, c.size);
        minStress(c.mx, c.n, c.n, c.size);
        fixOverlap(c.n);
        if (config.full) {
            planarPlacement(g.routes, c.mx, c.n, c.size);
            g = addSubTrees(g, c.mx, subs.slice(1).sort(byTreeLength), c.size);
        }
        g.setGlobalBox();
        return g;
    };
    return Hola;
}());
exports.default = Hola;
function getParams(g, config) {
    var n = g.getNodes();
    var size = 0;
    n.forEach(function (a, i) {
        a.isn = i;
        size += a.w + a.h;
    });
    size = Math.round(size / (2 * n.length));
    if (config.itemPadding)
        size += config.itemPadding;
    else
        size *= 2;
    var mx = getDistMatrix(g);
    return { n: n, mx: mx, size: size };
}
// calculate distance matrix between all nodes
function getDistMatrix(g) {
    var m = [];
    var n = g.getNodes();
    for (var i = 0; i < n.length; i++) {
        var line = (m[i] = []);
        line[n[i].isn] = 0;
        getDistLine(n[i], line, i, 1);
    }
    return m;
}
// calculate distance from node n to all other nodes
function getDistLine(n, line, ii, dist) {
    var now = [n];
    var next = [];
    while (now.length) {
        for (var i = 0; i < now.length; i++) {
            var from = now[i];
            for (var j = 0; j < from.links.length; j++) {
                var to = from.links[j];
                var ti = to.isn;
                if (ti != ii && !line[ti]) {
                    line[ti] = dist;
                    next.push(to);
                }
            }
        }
        dist++;
        now = next;
        next = [];
    }
}
/* create initial node placement as a spaced circle */
function placeAround(n, size) {
    var step = (Math.PI * 2) / n.length;
    var angle = 0;
    var rad = size * 5;
    n.forEach(function (a, i) {
        a.x = Math.round(Math.cos(angle) * rad);
        a.y = Math.round(Math.sin(angle) * rad);
        angle += step;
    });
}
function getStress(mx, n) {
    var nCount = n.length;
    var x = 0, cx, cy;
    // for each node, calculate stress
    for (var i = 0; i < nCount; i++) {
        cx = 0;
        cy = 0;
        var from = n[i];
        var line = mx[from.isn];
        for (var j = 0; j < nCount; j++) {
            // skip self
            if (from.isn == j)
                continue;
            var to = n[j];
            // distance to node
            var dij = line[to.isn];
            var kij = 0.5 / (dij * dij);
            // deltas between nodes
            var dx = from.x - to.x;
            var dy = from.y - to.y;
            var bottom = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2));
            if (bottom) {
                cx += kij * (dx - (dij * dx) / bottom);
                cx += kij * (dy - (dij * dy) / bottom);
            }
        }
        // full stress value for the node
        x += Math.sqrt(Math.pow(cx, 2) + Math.pow(cy, 2));
    }
    return x;
}
/* detects node with maximum stress and calculate the possible improvment */
function getDelta(mx, n, s, size) {
    // max stress node index, max stress
    var di = 0, dm = 0;
    // x-axis max stress
    var cx, cxm = 0;
    // y-axis max stress
    var cy, cym = 0;
    var max = n.length;
    var maxS = s.length;
    // for each node, calculate stress
    for (var i = 0; i < maxS; i++) {
        cx = 0;
        cy = 0;
        var from_1 = s[i];
        var line_1 = mx[from_1.isn];
        for (var j = 0; j < max; j++) {
            // skip self
            if (from_1.isn == j)
                continue;
            var to = n[j];
            // distance to node
            var dij = line_1[to.isn];
            var kij = 0.5 / (dij * dij);
            // deltas between nodes
            var dx = from_1.x - to.x;
            var dy = from_1.y - to.y;
            var bottom = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2));
            if (bottom) {
                cx += kij * (dx - (size * dij * dx) / bottom);
                cy += kij * (dy - (size * dij * dy) / bottom);
            }
        }
        // full stress value for the node
        var cd = Math.sqrt(Math.pow(cx, 2) + Math.pow(cy, 2));
        if (cd > dm) {
            // store max stress node and its stress value
            di = from_1.isn;
            dm = cd;
            cxm = cx;
            cym = cy;
        }
    }
    // now, we know the max stress node
    // lets check how its position can be optimized
    var k1 = 0, k2 = 0, k3 = 0;
    var from = n[di];
    var line = mx[di];
    for (var j = 0; j < max; j++) {
        if (di == j)
            continue;
        var to = n[j];
        var dij = line[to.isn];
        var kij = 0.5 / (dij * dij);
        var dx = from.x - to.x;
        var dy = from.y - to.y;
        var dx2 = dx * dx;
        var dy2 = dy * dy;
        var bottom = Math.sqrt(Math.pow((dx2 + dy2), 3));
        if (bottom) {
            k1 += kij * (1 - (size * dij * dy2) / bottom);
            k2 += (kij * size * dij * dy * dx) / bottom;
            k3 += kij * (1 - (size * dij * dx2) / bottom);
        }
    }
    // k1*gx + k2*gy = -cxm
    // k3*gy + k2*gx = -cym
    // =>
    // gx = (-cxm - k2*gy) / k1
    // gy = (-cym - k2*gx) /  k3
    // =>
    // gx = (-cxm - k2*((-cym - k2*gx) /  k3)) / k1
    // gx = -cxm/k1 - (k2*-cym - k2*k2*gx) /  (k3*k1)
    // gx = -cxm/k1 - (k2*-cym)/(k3*k1) - k2*k2*gx/(k3*k1)
    // gx  + k2*k2*gx/(k3*k1) = -cxm/k1 + (k2*cym)/(k3*k1)
    // gx * (1 + k2*k2/(k3*k1)) = -cxm/k1 + (k2*cym)/(k3*k1)
    // gx = (-cxm/k1 + (k2*cym)/(k3*k1)) / (1 + k2*k2/(k3*k1))
    // recommended node shift for x and y axis
    var gx, gy;
    gx = (-cxm / k1 + (k2 * cym) / (k3 * k1)) / (1 + (k2 * k2) / (k3 * k1));
    gy = (-cym - k2 * gx) / k3;
    // node inde, node stress, x shift, y shift
    return [di, dm, gx, gy];
}
function minStress(mx, n, s, size) {
    var i = 0;
    while (i++ < 100) {
        var _a = getDelta(mx, n, s, size), i_1 = _a[0], m = _a[1], x = _a[2], y = _a[3];
        if (m < 10)
            break;
        n[i_1].x += x;
        n[i_1].y += y;
    }
}
function fixOverlap(n) {
    //const cs = createOverlapConstraints(n);
}
function genEventsList(n) {
    var evs = [];
    for (var i = 0; i < n.length; i++) {
        var el = n[i];
        evs.push([0, i, el.y - el.h / 2]);
        evs.push([1, i, el.y + el.h / 2]);
    }
    evs.sort(function (a, b) { return (a[2] > b[2] ? 1 : a[2] == b[2] ? 0 : -1); });
    return evs;
}
function createOverlapConstraints(n) {
    var scan = {};
    var evs = genEventsList(n);
    var left = [];
    var right = [];
    var res = [];
    var _loop_1 = function (i) {
        var el = evs[i];
        var j = el[1];
        var node = n[j];
        if (el[0] == 0) {
            // open
            scan[j] = node;
            var lb_1 = (left[j] = getLeftBounds(scan, node));
            var rb_1 = (right[j] = getRightBounds(scan, node));
            // remove left duplicates
            for (var z = 0; z < lb_1.length; z++) {
                var el_1 = lb_1[z];
                var cs = right[el_1.isn];
                if (cs) {
                    right[el_1.isn] = cs.filter(function (a) { return !rb_1.includes(a); });
                }
            }
            // remove right duplicates
            for (var z = 0; z < rb_1.length; z++) {
                var el_2 = rb_1[z];
                var cs = left[el_2.isn];
                if (cs) {
                    left[el_2.isn] = cs.filter(function (a) { return !lb_1.includes(a); });
                }
            }
        }
        else {
            //close
            var lb = left[j];
            var rb = right[j];
            for (var z = 0; z < lb.length; z++) {
                var to = lb[z];
                res.push([to, node]);
                right[to.isn] = right[to.isn].filter(function (a) { return a != node; });
            }
            for (var z = 0; z < rb.length; z++) {
                var to = rb[z];
                res.push([node, to]);
                right[to.isn] = right[to.isn].filter(function (a) { return a != node; });
            }
            delete scan[j];
        }
    };
    for (var i = 0; i < evs.length; i++) {
        _loop_1(i);
    }
    return res;
}
function getLeftBounds(scan, v) {
    var left = [];
    var out = [];
    for (var key in scan) {
        var el = scan[key];
        if (el.x < v.x) {
            left.push(el);
        }
    }
    left.sort(function (a, b) { return (a.x > b.x ? -1 : 1); });
    for (var i = 0; i < left.length; i++) {
        var el = left[i];
        if (overlapX(el, v) <= 0) {
            out.push(el);
            break;
        }
        if (overlapX(el, v) <= overlapY(el, v))
            out.push(el);
    }
    return out;
}
function getRightBounds(scan, v) {
    var left = [];
    var out = [];
    for (var key in scan) {
        var el = scan[key];
        if (el.x > v.x) {
            left.push(el);
        }
    }
    left.sort(function (a, b) { return (a.x < b.x ? -1 : 1); });
    for (var i = 0; i < left.length; i++) {
        var el = left[i];
        if (overlapX(el, v) <= 0) {
            out.push(el);
            break;
        }
        if (overlapX(el, v) <= overlapY(el, v))
            out.push(el);
    }
    return out;
}
function overlapX(a, b) {
    return (a.w + b.w) / 2 - Math.abs(a.x - b.x);
}
function overlapY(a, b) {
    return (a.h + b.h) / 2 - Math.abs(a.y - b.y);
}
var piQuarter = Math.PI / 4;
var aligned = {}, alignedBase = 0;
function planarPlacement(r, mx, nodes, step) {
    aligned = {};
    alignedBase = nodes.length;
    nodes.forEach(function (a) { return (a.iss = ""); });
    var byCount = [].concat(nodes);
    var major = byCount
        .filter(function (a) { return a.links.length >= 3; })
        .sort(function (a, b) { return (a.links.length > b.links.length ? -1 : a.links.length == b.links.length ? 0 : 1); });
    var angles = major.map(function (a) {
        var angles = [];
        // select best position for each link
        // include two positions for each node
        a.links.forEach(function (b) {
            var angle = Math.atan2(b.x - a.x, b.y - a.y) + Math.PI;
            var axisA = Math.ceil(angle / (2 * piQuarter)) * 2 * piQuarter;
            var axisB = Math.floor(angle / (2 * piQuarter)) * 2 * piQuarter;
            // related node, axis, angle to axis
            angles.push([b, axisA, Math.abs(angle - axisA)]);
            angles.push([b, axisB, Math.abs(angle - axisB)]);
        });
        // sort by minimal angle
        angles.sort(function (a, b) { return (a[2] > b[2] ? 1 : -1); });
        return [a, angles];
    });
    // cores
    angles.forEach(function (_a) {
        var a = _a[0], angles = _a[1];
        var x = a.x, y = a.y;
        a.x = Math.round(a.x / step) * step;
        a.y = Math.round(a.y / step) * step;
        for (var i = 0; i < angles.length; i++) {
            //best match
            var t = angles[i];
            var b = t[0];
            // console.log(a.id, "->", b.id, a.x, a.y)
            //link already aligned
            if (r.getRoute(a.id, b.id))
                continue;
            var _b = detectDir(a, b, t[1], step, x, y), dx = _b[0], dy = _b[1], vx = _b[2], vy = _b[3];
            if (!r.hasRoute(a.id, vx, vy)) {
                if (r.isAligned(b.id)) {
                    //addBend(a, b, r, dx, dy, vx, vy, step);
                    // TODO - bend point
                }
                else {
                    b.x += dx;
                    b.y += dy;
                    r.addRoute(a.id, b.id, vx, vy);
                }
            }
        }
    });
    minStress(mx, nodes, nodes.filter(function (a) { return !r.isAligned(a.id); }), step);
    // chains
    major.forEach(function (a) {
        if (!r.isAligned(a.id)) {
            // if core node was not aligned, fix it position
            a.x = Math.round(a.x / step) * step;
            a.y = Math.round(a.y / step) * step;
        }
        a.links.forEach(function (b) {
            if (b.links.length == 2) {
                routeLink(a, b, r, step);
            }
        });
    });
    if (major.length === 0) {
        // a single loop
        var a = nodes[0];
        var b = nodes[0].links[0];
        b.x = Math.round(b.x / step) * step;
        b.y = Math.round(b.y / step) * step;
        routeLink(a, b, r, step);
    }
}
function addBend(a, b, r, dx, dy, vx, vy, step) {
    //assume that other node can produce the ortogonal link
    // preffered patterns
    // - ortoganal nearest
    // - reverse + ortogonal
    // - back-reverse + ortogonal
    // - back-ortogonal + reverse +ortogonal
    r.addPath(a.id, b.id, vx, vy, []);
    r.addPath(b.id, a.id, -vx, -vy, []);
}
function routeLink(a, b, r, step) {
    while (true) {
        var next = b.links[0];
        if (next == a)
            next = b.links[1];
        a = b;
        b = next;
        if (b.links.length > 2)
            break;
        var angle = Math.atan2(b.x - a.x, b.y - a.y) + Math.PI;
        var axisA = Math.ceil(angle / (2 * piQuarter)) * 2 * piQuarter;
        var axisB = Math.floor(angle / (2 * piQuarter)) * 2 * piQuarter;
        var deltaA = Math.abs(angle - axisA);
        var deltaB = Math.abs(angle - axisB);
        var angles = deltaA < deltaB ? [axisA, axisB] : [axisB, axisA];
        for (var i = 0; i < angles.length; i++) {
            var _a = detectDir(a, b, angles[i], step, a.x, a.y), dx = _a[0], dy = _a[1], vx = _a[2], vy = _a[3];
            if (!r.hasRoute(a.id, vx, vy)) {
                if (r.isAligned(b.id)) {
                    //addBend(a, b, r, dx, dy, vx, vy, step);
                    return;
                }
                else {
                    b.x += dx;
                    b.y += dy;
                    r.addRoute(a.id, b.id, vx, vy);
                    break;
                }
            }
        }
    }
}
function detectDir(a, b, angle, step, ox, oy) {
    // 0 -> north, conter-clockwise
    if (angle <= piQuarter || angle > piQuarter * 7) {
        // north
        return [a.x - b.x, dd(a.y, b.y, oy, step), 0, -1]; // "n"
    }
    else if (angle <= piQuarter * 3 && angle > piQuarter) {
        // west
        return [dd(a.x, b.x, ox, step), a.y - b.y, -1, 0]; // "w"
    }
    else if (angle <= piQuarter * 5 && angle > piQuarter * 3) {
        // south
        return [a.x - b.x, dd(a.y, b.y, oy, step), 0, 1]; // "s"
    }
    else {
        // if (angle <= piQuarter*7 && angle > piQuarter*5){
        // east
        return [dd(a.x, b.x, ox, step), a.y - b.y, 1, 0]; // "e"
    }
}
function dd(a, b, ao, step) {
    // calculate shift to the best grid position
    var dir = helpers_1.sign(ao - b);
    return a - b - (Math.round((ao - b) / step) || dir) * step;
}
var shortPlacementOrder = [
    [0, 1, 0, 0],
    [1, 0, 0, 0],
    [-1, 0, 0, 0],
    [0, -1, 0, 0],
];
var longPlacementOrder = [
    //bottom-left
    [0, 1, -1, 1],
    [-1, 0, -1, 1],
    //bottom-right
    [0, 1, 1, 1],
    [1, 0, 1, 1],
    //up-right
    [1, 0, 1, -1],
    [0, -1, 1, -1],
    //bottom-left
    [-1, 0, -1, -1],
    [0, -1, -1, -1],
];
var fullPlacementOrder = shortPlacementOrder.concat(longPlacementOrder);
function addSubTrees(m, mx, trees, step) {
    var coreSize = m.getNodes().length;
    trees.forEach(function (_a) {
        var g = _a.g, root = _a.root;
        new SymmetricTree_1.default().layout(g, { root: g.getNodes()[0].id });
        // find the minimal stress position in main 4 directions
        var match = findSubTreePlace(mx, fullPlacementOrder, m, g, root, step, coreSize);
        if (match) {
            m = match.g;
            // add edge to the linked tree
            var to = g.getRoot().id;
            if (!match.dir[2] && !match.dir[3]) {
                m.routes.addRoute(root, g.getRoot().id, match.dir[0], match.dir[1]);
            }
            m.hash[root].links.push(m.hash[to]);
            m.hash[to].links.push(m.hash[root]);
            return;
        }
        console.log("Can't position sub tree");
    });
    return m;
}
function findSubTreePlace(mx, order, m, g, root, step, coreSize) {
    return order
        .map(function (dir) {
        // when placing in ortogonal directions, ignore already routed ones
        if (!dir[2] && !dir[3] && m.routes.hasRoute(root, dir[0], dir[1]))
            return null;
        //set drop orientation
        var gCopy = g.copy();
        var mCopy = m.copy();
        var rNode = gCopy.getRoot();
        var aNode = mCopy.hash[root];
        gCopy.rotate({ x: -dir[0], y: dir[1] });
        var bounds = gCopy.getBox();
        var w = bounds[0][1] - bounds[0][0];
        var h = bounds[1][1] - bounds[1][0];
        var dx = rNode.x - bounds[0][0] - w / 2;
        var dy = rNode.y - bounds[1][0] - h / 2;
        //console.log(aNode.id, dir, rNode.id);
        reserveSpace(mCopy, aNode, w, h, dx, dy, step, dir);
        addSubTree(mCopy, gCopy, aNode, rNode, step, dir);
        var s = getStress(mx, mCopy.getNodes().slice(0, coreSize));
        //console.log(s)
        return { g: mCopy, s: s, dir: dir };
    })
        .reduce(function (acc, val) {
        return !acc || (val && val.s < acc.s) ? val : acc;
    });
}
function reserveSpace(g, n, w, h, dx, dy, step, dir) {
    var _a, _b;
    // shift when placing non-ortogonal
    var nX = n.x + (dir[2] || dir[0]) * step;
    var nY = n.y + (dir[3] || dir[1]) * step;
    // check the node target first
    if (isOccupied(g, nX - step / 2, nX + step / 2, nY - step / 2, nY + step / 2)) {
        // no free space at all, reserve for full graph
        clearSpace(g, nX - (step / 2) * dir[0], nY - (step / 2) * dir[1], w, h, dir, step);
        return;
    }
    var deep = (dir[0] ? w : h) / step - 1;
    if (deep <= 0)
        return;
    // for direction axis add root_size/2 + block size
    // for inderect axis align root of sub-block and root of block
    // add step/2 to disallow nodes without padding
    var xMin, xMax, yMin, yMax;
    var gap = step * 0.45;
    if (dir[0]) {
        // left, right
        xMin = nX + (step / 2) * dir[0];
        xMax = xMin + w * dir[0]; // do not add step, as first node is already placed
        yMin = nY - h / 2 - gap - dy;
        yMax = yMin + h + gap * 2;
    }
    else {
        // top, bottom
        xMin = nX - w / 2 - gap - dx;
        xMax = xMin + w + gap * 2;
        yMin = nY + (step / 2) * dir[1];
        yMax = yMin + h * dir[1];
    }
    if (yMin > yMax)
        _a = [yMax, yMin], yMin = _a[0], yMax = _a[1];
    if (xMin > xMax)
        _b = [xMax, xMin], xMin = _b[0], xMax = _b[1];
    if (isOccupied(g, xMin, xMax, yMin, yMax)) {
        clearSpace(g, nX, nY, w, h, dir, step);
    }
}
function clearSpace(g, nX, nY, w, h, dir, step) {
    // let affected = [];
    // if (dir[1]) {
    // 	console.log(Math.sign(dir[1]) ? ">" : "<", nY, (h + step) * dir[1]);
    // } else {
    // 	console.log(Math.sign(dir[0]) ? ">" : "<", nX, (w + step) * dir[0]);
    // }
    g.getNodes().forEach(function (a) {
        if (dir[1]) {
            if (helpers_1.sign(a.y - nY) == dir[1]) {
                a.y += (h + step / 2) * dir[1];
                // affected.push(a.id);
            }
        }
        else if (dir[0]) {
            if (helpers_1.sign(a.x - nX) == dir[0]) {
                a.x += (w + step / 2) * dir[0];
                // affected.push(a.id);
            }
        }
    });
    // console.log(affected);
}
function isOccupied(g, x1, x2, y1, y2) {
    var nodes = g.getNodes();
    for (var i = 0; i < nodes.length; i++) {
        var a = nodes[i];
        if (a.x >= x1 && a.x <= x2 && a.y >= y1 && a.y <= y2) {
            return true;
        }
    }
    return false;
}
function addSubTree(g, s, n, r, step, dir) {
    var x = n.x + step * (dir[0] || dir[2]) - r.x;
    var y = n.y + step * (dir[1] || dir[3]) - r.y;
    s.translate({ x: x, y: y });
    g.importNodes(s.getNodes());
}
function byTreeLength(a, b) {
    var ax = a.g.getNodes().length;
    var bx = b.g.getNodes().length;
    return ax > bx ? -1 : ax === bx ? 0 : 1;
}


/***/ }),
/* 60 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var types_1 = __webpack_require__(32);
var Graph_1 = __webpack_require__(19);
var SymmetricTree = /** @class */ (function () {
    function SymmetricTree() {
        this._step = 0;
    }
    SymmetricTree.prototype.layout = function (g, config) {
        g.root(g.toTree(g.hash[config.root]));
        var nodes = g.getNodes();
        config.levelPadding = config.levelPadding || this._getStep(nodes);
        config.itemPadding = config.itemPadding || this._getStep(nodes);
        config.dir = config.dir || types_1.Direction.Bottom;
        config.rotate = config.dir === types_1.Direction.Right || config.dir === types_1.Direction.Left;
        var vector = types_1.DirVectors[config.dir];
        this._layout(g, config);
        g.setBox();
        if (config.dir !== types_1.Direction.Bottom) {
            g.rotate(vector);
        }
        var box = g.getBox();
        g.translate({ x: box[0][0] * -1, y: box[1][0] * -1 });
        g.setBox();
        return g;
    };
    SymmetricTree.prototype._layout = function (g, config) {
        var _this = this;
        var root = g.getRoot();
        root.x = root.y = 0;
        if (g.getLevels().length == 1) {
            g._symmetry = true;
            return "0";
        }
        var ctrees = root.kids.map(function (a) { return new Graph_1.default(g, a, config); });
        var itree = {};
        ctrees.forEach(function (c) {
            var code = _this._layout(c, config);
            if (!itree[code]) {
                itree[code] = [c];
            }
            else {
                itree[code].push(c);
            }
        });
        var classes = Object.keys(itree).sort(function (a, b) {
            var ta = itree[a][0];
            var tb = itree[b][0];
            var wa = ta._width;
            var wb = tb._width;
            if (wa > wb)
                return -1;
            if (wa < wb)
                return 1;
            var da = ta.getLevels().length;
            var db = tb.getLevels().length;
            if (da > db)
                return -1;
            if (da < db)
                return 1;
            return a < b ? 1 : -1;
        });
        // get number of non-paired sub-graphs
        var oddNumber = 0;
        var oddClass = 0;
        for (var i = 0; i < classes.length; i++) {
            if (itree[classes[i]].length % 2 === 1) {
                oddNumber++;
                oddClass = i;
            }
        }
        // detect symmetry
        var center = false;
        var symmetry = false;
        if (oddNumber == 1) {
            var oddTree = itree[classes[oddClass]][0];
            // [FIXME] probably any tree with balanced bounds need to be counted as symmetric
            if (oddTree._symmetry) {
                // center symmetric tree
                symmetry = true;
            }
            center = true;
            if (oddClass !== 0) {
                classes.unshift(classes[oddClass]);
                classes.splice(oddClass + 1, 1);
            }
        }
        else if (oddNumber === 0) {
            symmetry = true;
        }
        g._symmetry = symmetry;
        this._layout_place(g, itree, classes, center, config);
        return g.getIString();
    };
    SymmetricTree.prototype._layout_place = function (g, itree, classes, center, config) {
        var dir = { x: 0, y: config.levelPadding };
        var next = true;
        for (var i = 0; i < classes.length; i++) {
            var trees = itree[classes[i]];
            trees.forEach(function (t) {
                if (center) {
                    // only first tree can be placed in the center
                    center = false;
                    //move in position
                    t.translate(dir);
                    // update bounds
                    var sl = 0, su = 0;
                    var tb = t.getLevelBounds();
                    for (var j = 0; j < tb.length; j++) {
                        var _a = tb[j], l = _a[0], u = _a[1];
                        g._bounds[j + 1] = [l, u];
                        if (l < sl)
                            sl = l;
                        if (u > su)
                            su = u;
                    }
                    g._tbounds = [sl, su];
                }
                else {
                    var xu = next ? 1 : 0;
                    var xl = next ? 0 : 1;
                    if (next)
                        t.mirror();
                    var treeMaxBound = void 0, pos = void 0;
                    pos = treeMaxBound = 999999 * (next ? -1 : 1);
                    var padding = config.itemPadding;
                    for (var i_1 = 0; i_1 < t._bounds.length; i_1++) {
                        var test_1 = g.getBounds(i_1 + 1, padding, config.wide)[xu] -
                            t.getBounds(i_1, padding, config.wide)[xl];
                        if ((next && test_1 > pos) || (!next && test_1 < pos)) {
                            pos = test_1;
                        }
                    }
                    // move tree to the calculated position
                    t.translate({ x: pos, y: dir.y });
                    // update bounds
                    for (var i_2 = 0; i_2 < g._bounds.length; i_2++) {
                        var maxBound = void 0;
                        if (i_2 == 0 || i_2 > t._bounds.length) {
                            // level absent in sub-tree
                            maxBound = g._bounds[i_2][xu];
                        }
                        else {
                            // level present in sub-tree
                            maxBound = g._bounds[i_2][xu] = t._bounds[i_2 - 1][xu];
                        }
                        if ((next && maxBound > treeMaxBound) || (!next && maxBound < treeMaxBound)) {
                            treeMaxBound = maxBound;
                        }
                    }
                    g._tbounds[xu] = treeMaxBound;
                    next = !next;
                }
            });
        }
    };
    SymmetricTree.prototype._getStep = function (nodes) {
        if (this._step === 0) {
            var count = nodes.length;
            // 2 x Average Dimension
            var sum_1 = 0;
            nodes.forEach(function (n) { return (sum_1 += n.w + n.h); });
            this._step = sum_1 / count;
        }
        return this._step;
    };
    return SymmetricTree;
}());
exports.default = SymmetricTree;


/***/ }),
/* 61 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var helpers_1 = __webpack_require__(33);
var codes = [0x01, 0x02, 0x04, 0x08, 0x00, 0x10, 0x20, 0x40, 0x80];
var Routes = /** @class */ (function () {
    function Routes() {
        this._map = {};
    }
    Routes.prototype.getRoute = function (a, b) {
        var t = this._map[a];
        if (!t)
            return null;
        return t.map[b] || null;
    };
    Routes.prototype.addRoute = function (a, b, dx, dy) {
        this.addPath(a, b, dx, dy);
        this.addPath(b, a, -dx, -dy);
    };
    Routes.prototype.hasRoute = function (a, dx, dy) {
        var t = this._map[a];
        if (!t)
            return false;
        return (t.dir & this._code(dx, dy)) > 0;
    };
    Routes.prototype.isAligned = function (a) {
        return !!this._map[a];
    };
    Routes.prototype.isAxisAligned = function (a, dx, dy) {
        var t = this._map[a];
        if (!t)
            return null;
        for (var key in t.map) {
            var link = t.map[key];
            if ((dx && link.dy) || (dy && link.dx))
                return false;
        }
        return true;
    };
    Routes.prototype.addPath = function (a, b, dx, dy, points) {
        var t = this._map[a];
        if (!t) {
            t = this._map[a] = { map: {}, dir: 0 };
        }
        t.map[b] = { dx: dx, dy: dy, points: points };
        t.dir = t.dir | this._code(dx, dy);
    };
    Routes.prototype._code = function (dx, dy) {
        return codes[helpers_1.sign(dx) + 1 + (helpers_1.sign(dy) + 1) * 3];
    };
    return Routes;
}());
exports.default = Routes;


/***/ }),
/* 62 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Graph_1 = __webpack_require__(19);
function split(g) {
    var core = g.getNodes();
    if (core.length < 3)
        return [{ g: g }];
    var first = core[0];
    var free = [];
    var count = core.length + 1;
    while (count != core.length) {
        count = core.length;
        core = core.filter(function (a) {
            if (a.links.length == 1) {
                var to = g.hash[a.links[0].id];
                if (to)
                    to.links = to.links.filter(function (b) { return b.id != a.id; });
                free.push(a);
                return false;
            }
            return true;
        });
    }
    //tree graph, no loops
    if (core.length < 2) {
        // restore links
        free.filter(function (a) { return a.links.length > 0; }).forEach(function (a) {
            a.links[0].links.push(a);
        });
        g.root(g.toTree(first));
        return [{ g: g }];
    }
    var freeHash = {};
    free.forEach(function (a) { return (freeHash[a.id] = a); });
    //detect roots
    var roots = free.filter(function (a) { return !freeHash[a.links[0].id]; }).map(function (a) { return [a]; });
    //restore bidirectional links
    free.forEach(function (a) {
        var to = freeHash[a.links[0].id];
        if (to)
            to.links.push(a);
    });
    var trees = roots.map(function (r) {
        var i = 0;
        var next;
        while ((next = r[i++])) {
            var lcount = next.links.length;
            if (lcount > 1)
                for (var i_1 = 1; i_1 < lcount; i_1++) {
                    var to = next.links[i_1];
                    r.push(to);
                }
        }
        var root = r[0].links.splice(0, 1)[0].id;
        return graphFromArray(r, root);
    });
    // core and trees
    return [graphFromArray(core, null)].concat(trees);
}
exports.split = split;
function graphFromArray(data, root) {
    var g = new Graph_1.default();
    g.importNodes(data);
    return { g: g, root: root };
}


/***/ }),
/* 63 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
function compose(many, cfg) {
    if (many.length < 2)
        return many[0] || null;
    var ab = many[0].getBox();
    var dx = cfg.padding;
    var out = many.reduce(function (acc, v) {
        var bb = v.getBox();
        v.translate({ x: ab[0][1] - bb[0][0] + dx, y: ab[1][0] - bb[1][0] });
        acc.importNodes(v.getNodes());
        dx += cfg.padding + bb[0][1] - bb[0][0];
        return acc;
    });
    if (many.length)
        out.setGlobalBox();
    return out;
}
exports.compose = compose;


/***/ }),
/* 64 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
function decompose(g) {
    var out = [];
    var _loop_1 = function () {
        var map = {};
        var n = g.getNodes();
        if (!n.length)
            return { value: out };
        visitAll(n[0], map);
        var sub = g.split(function (a) { return !map[a.id]; });
        out.push(g);
        if (sub) {
            g = sub;
        }
        else {
            return { value: out };
        }
    };
    while (true) {
        var state_1 = _loop_1();
        if (typeof state_1 === "object")
            return state_1.value;
    }
}
exports.decompose = decompose;
function visitAll(n, visited) {
    visited[n.id] = 1;
    n.links.forEach(function (x) {
        if (!visited[x.id]) {
            visitAll(x, visited);
        }
    });
}


/***/ }),
/* 65 */,
/* 66 */,
/* 67 */,
/* 68 */,
/* 69 */,
/* 70 */,
/* 71 */,
/* 72 */,
/* 73 */,
/* 74 */
/***/ (function(module, exports) {

Object.values = Object.values
    ? Object.values
    : function (obj) {
        var allowedTypes = [
            "[object String]",
            "[object Object]",
            "[object Array]",
            "[object Function]",
        ];
        var objType = Object.prototype.toString.call(obj);
        if (obj === null || typeof obj === "undefined") {
            throw new TypeError("Cannot convert undefined or null to object");
        }
        else if (!~allowedTypes.indexOf(objType)) {
            return [];
        }
        else {
            // if ES6 is supported
            if (Object.keys) {
                return Object.keys(obj).map(function (key) {
                    return obj[key];
                });
            }
            var result = [];
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop)) {
                    result.push(obj[prop]);
                }
            }
            return result;
        }
    };


/***/ }),
/* 75 */
/***/ (function(module, exports) {

/* eslint-disable prefer-rest-params */
/* eslint-disable @typescript-eslint/unbound-method */
// eslint-disable-next-line @typescript-eslint/unbound-method
if (!Array.prototype.includes) {
    Object.defineProperty(Array.prototype, "includes", {
        value: function (searchElement, fromIndex) {
            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }
            // 1. Let O be ? ToObject(this value).
            var o = Object(this);
            // 2. Let len be ? ToLength(? Get(O, "length")).
            var len = o.length >>> 0;
            // 3. If len is 0, return false.
            if (len === 0) {
                return false;
            }
            // 4. Let n be ? ToInteger(fromIndex).
            //    (If fromIndex is undefined, this step produces the value 0.)
            var n = fromIndex | 0;
            // 5. If n ≥ 0, then
            //  a. Let k be n.
            // 6. Else n < 0,
            //  a. Let k be len + n.
            //  b. If k < 0, let k be 0.
            var k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);
            function sameValueZero(x, y) {
                return x === y || (typeof x === "number" && typeof y === "number" && isNaN(x) && isNaN(y));
            }
            // 7. Repeat, while k < len
            while (k < len) {
                // a. Let elementK be the result of ? Get(O, ! ToString(k)).
                // b. If SameValueZero(searchElement, elementK) is true, return true.
                if (sameValueZero(o[k], searchElement)) {
                    return true;
                }
                // c. Increase k by 1.
                k++;
            }
            // 8. Return false
            return false;
        },
        configurable: true,
        writable: true,
    });
}
// https://tc39.github.io/ecma262/#sec-array.prototype.find
if (!Array.prototype.find) {
    Object.defineProperty(Array.prototype, "find", {
        value: function (predicate) {
            // 1. Let O be ? ToObject(this value).
            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }
            var o = Object(this);
            // 2. Let len be ? ToLength(? Get(O, "length")).
            var len = o.length >>> 0;
            // 3. If IsCallable(predicate) is false, throw a TypeError exception.
            if (typeof predicate !== "function") {
                throw new TypeError("predicate must be a function");
            }
            // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
            var thisArg = arguments[1];
            // 5. Let k be 0.
            var k = 0;
            // 6. Repeat, while k < len
            while (k < len) {
                // a. Let Pk be ! ToString(k).
                // b. Let kValue be ? Get(O, Pk).
                // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
                // d. If testResult is true, return kValue.
                var kValue = o[k];
                if (predicate.call(thisArg, kValue, k, o)) {
                    return kValue;
                }
                // e. Increase k by 1.
                k++;
            }
            // 7. Return undefined.
            return undefined;
        },
        configurable: true,
        writable: true,
    });
}


/***/ }),
/* 76 */
/***/ (function(module, exports) {

/* eslint-disable @typescript-eslint/unbound-method */
if (!String.prototype.includes) {
    String.prototype.includes = function (search, start) {
        "use strict";
        if (typeof start !== "number") {
            start = 0;
        }
        if (start + search.length > this.length) {
            return false;
        }
        else {
            return this.indexOf(search, start) !== -1;
        }
    };
}
if (!String.prototype.startsWith) {
    Object.defineProperty(String.prototype, "startsWith", {
        enumerable: false,
        configurable: true,
        writable: true,
        value: function (searchString, position) {
            position = position || 0;
            return this.indexOf(searchString, position) === position;
        },
    });
}


/***/ }),
/* 77 */
/***/ (function(module, exports) {

/* eslint-disable @typescript-eslint/no-this-alias */
/* eslint-disable prefer-rest-params */
/* eslint-disable @typescript-eslint/unbound-method */
if (Element && !Element.prototype.matches) {
    var proto = Element.prototype;
    proto.matches =
        proto.matchesSelector ||
            proto.mozMatchesSelector ||
            proto.msMatchesSelector ||
            proto.oMatchesSelector ||
            proto.webkitMatchesSelector;
}
// Source: https://github.com/naminho/svg-classlist-polyfill/blob/master/polyfill.js
if (!("classList" in SVGElement.prototype)) {
    Object.defineProperty(SVGElement.prototype, "classList", {
        get: function get() {
            var _this = this;
            return {
                contains: function contains(className) {
                    return _this.className.baseVal.split(" ").indexOf(className) !== -1;
                },
                add: function add(className) {
                    return _this.setAttribute("class", _this.getAttribute("class") + " " + className);
                },
                remove: function remove(className) {
                    var removedClass = _this
                        .getAttribute("class")
                        .replace(new RegExp("(\\s|^)".concat(className, "(\\s|$)"), "g"), "$2");
                    if (_this.classList.contains(className)) {
                        _this.setAttribute("class", removedClass);
                    }
                },
                toggle: function toggle(className) {
                    if (this.contains(className)) {
                        this.remove(className);
                    }
                    else {
                        this.add(className);
                    }
                },
            };
        },
        configurable: true,
    });
}


/***/ }),
/* 78 */,
/* 79 */,
/* 80 */,
/* 81 */,
/* 82 */,
/* 83 */,
/* 84 */,
/* 85 */,
/* 86 */,
/* 87 */,
/* 88 */,
/* 89 */,
/* 90 */,
/* 91 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(74);
__webpack_require__(75);
__webpack_require__(76);
__webpack_require__(77);
module.exports = __webpack_require__(92);


/***/ }),
/* 92 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
__webpack_require__(93);
var Diagram_1 = __webpack_require__(34);
exports.Diagram = Diagram_1.Diagram;
var dom_1 = __webpack_require__(0);
exports.awaitRedraw = dom_1.awaitRedraw;
var en_1 = __webpack_require__(3);
var w = window;
exports.i18n = w.dhx && w.dhx.i18n ? w.dhx.i18 : {};
exports.i18n.setLocale = function (component, value) {
    var target = exports.i18n[component];
    for (var key in value) {
        target[key] = value[key];
    }
};
exports.i18n.diagram = exports.i18n.diagram || en_1.default;


/***/ }),
/* 93 */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ })
/******/ ]);
});if (window.dhx_legacy) { if (window.dhx){ for (var key in dhx) dhx_legacy[key] = dhx[key]; } window.dhx = dhx_legacy; delete window.dhx_legacy; }