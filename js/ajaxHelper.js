/**
 * Author: Fangxin Jiang
 * Date: 2015-8-27
 */
var ajaxHelper = {
    getUrl: function(elem) {
        var tag = elem.tagName.toLowerCase();
        switch(tag) {
            case "a":
                var url = $(elem).attr("href") ? $(elem).attr("href") : null;
                break;
            case "form":
                var url = $(elem).attr("action") ? $(elem).attr("action") : null;
                break;
            default :
                var url = $(elem).attr("ajax-url") ? $(elem).attr("ajax-url") : null;
                break;
        }
        return url;
    },
    getMethod: function(elem) {
        var tag = elem.tagName.toLowerCase();
        if(tag == "form") {
            var method = $(elem).attr("method") ? $(elem).attr("method") : null;
        } else {
            var method = $(elem).attr("ajax-method") ? $(elem).attr("ajax-method") : null;
        }
        return method;
    },
    getMethod_default: function(elem) {
        var tag = elem.tagName.toLowerCase();
        return tag == "form" ? "POST" : "GET";
    },
    getData: function(elem) {
        var tag = elem.tagName.toLowerCase();
        if(tag == "form") {
            var data = new FormData(elem);
        } else {
            var data = $(elem).attr("ajax-data") ? $(elem).attr("ajax-data") : null;
        }
        return data;
    },
    getDataType: function(elem) {
        return $(elem).attr("ajax-dataType") ? $(elem).attr("ajax-dataType") : null;
    },
    getProcessData: function(elem) {
        return $(elem).attr("ajax-processData") ? eval("(false || " + $(elem).attr("ajax-processData") + ")") : null;
    },
    getProcessData_default: function(elem) {
        var tag = elem.tagName.toLowerCase();
        return tag == "form" ? false : null;
    },
    getContentType: function(elem) {
        var str = $(elem).attr("ajax-contentType");
        if(!str) return null;
        var arr = ["true","false","1","0"];
        return arr.indexOf(str) == -1 ? str : eval("(false || " + str + ")");
    },
    getContentType_default: function(elem) {
        var tag = elem.tagName.toLowerCase();
        return tag == "form" ? false : null;
    },
    getSuccess: function(elem) {
        return $(elem).attr("ajax-success") ? eval("(false || " + $(elem).attr("ajax-success") + ")") : null;
    },
    getError: function(elem) {
        return $(elem).attr("ajax-error") ? eval("(false || " + $(elem).attr("ajax-error") + ")") : null;
    },
    getBeforeSend: function(elem) {
        return $(elem).attr("ajax-beforeSend") ? eval("(false || " + $(elem).attr("ajax-beforeSend") + ")") : null;
    },
    getComplete: function(elem) {
        return $(elem).attr("ajax-complete") ? eval("(false || " + $(elem).attr("ajax-complete") + ")") : null;
    },
    getCache: function(elem) {
        return $(elem).attr("ajax-cache") ? eval("(false || " + $(elem).attr("ajax-cache") + ")") : null;
    },
    getTimeout: function(elem) {
        return $(elem).attr("ajax-timeout") ? eval("(false || " + $(elem).attr("ajax-timeout") + ")") : null;
    },
    priority: function() {
        for(var i=0; i<arguments.length; i++) {
            if(arguments[i] !== null) return arguments[i];
        }
        return null;
    },
    filter: function(json) {
        var result = {};
        for(var i in json) {
            if(json[i] !== null && json.hasOwnProperty(i)) result[i] = json[i];
        }
        return result;
    }
};