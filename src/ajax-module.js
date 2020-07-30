
export const AjaxQuery = (function() {
    var running = false; // Ajax Running
    return {
        "sendAjax" : function(config, wait = true) {
            if(!running && wait) {
                running = true; // Set running jadi true
                function j() {
                    return $.ajax(config);
                }
                $.when(j()).done(function() {
                    running = false;
                })
            }
            else {
                $.ajax(config);
            }
        }
    };
})();

export const SetAjaxConfig = function(...args) {
    var ajax = {
        "url": "",
        "data": {},
        "processData": false,
        "cache": false,
        "dataType": "JSON",
        "type": "POST"
    };
    var redirect = "";
    var config = arguments;
    var data = Object.create(null);

    const getConfig = function() {
        return ajax;
    }

    const setConfig = function(...args) {
        if(arguments.length > 0) {
            if(arguments[0] instanceof Object) {
                ajax = arguments[0];
            }
            else {
                if(arguments[0] != "" && arguments[1] != "") { 
                    console.log(arguments[1]);
                    ajax[arguments[0]] = arguments[1];
                }
            }
        }
    }

    const setData = function(data) {
        data = data;
    }

    const setSuccess = function(fn, show = true, redirect = "") {
        ajax.redirect = redirect;
        if(show && fn instanceof Function) {
            ajax.success = fn;
        }
        else {
            ajax.success = function() {
                if(redirect != "") {
                    location.href = redirect;
                }
            }
        }
    }

    const setError = function(fn, show = true) {
        if(show && fn instanceof Function) {
            ajax.error = fn;
        }
        else {
            ajax.error = function() {};
        }
    }

    const setBeforeSend = function(fn) {
        if(fn instanceof Function) {
            ajax.beforeSend = fn;
        }
    }

    const setComplete = function(fn) {
        if(fn instanceof Function) {
            ajax.complete = fn;
        }
    }

    const setRedirect = function(path) {
        redirect = path;
    }

    return {
        "getConfig": getConfig,
        "setConfig": setConfig,
        "setData": setData,
        "setSuccess": setSuccess,
        "setError": setError,
        "setRedirect": setRedirect,
        "setBeforeSend": setBeforeSend,
        "setComplete": setComplete
    }
}

