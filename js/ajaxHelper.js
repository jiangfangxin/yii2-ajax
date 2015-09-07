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
            var method = $(elem).attr("method") ? $(elem).attr("method") : "POST";
        } else {
            var method = $(elem).attr("ajax-method") ? $(elem).attr("ajax-method") : "GET";
        }
        return method;
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
        var tag = elem.tagName.toLowerCase();
        return tag == "form" ? false : null;
    },
    getContentType: function(elem) {
        var tag = elem.tagName.toLowerCase();
        return tag == "form" ? false : null;
    },
    filter: function(json) {
        for(var i in json) {
            //Delete elements whose value is null.
            if(json[i] === null) delete json[i];
        }
    }
};