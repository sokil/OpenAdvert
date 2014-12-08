function merge()
{
    var merged = {}, key;

    for(var i = 0; i < arguments.length; i++) {
        for(key in arguments[i]) {
            merged[key] = arguments[i][key];
        }
    }

    return merged;
}
    
function extend(childConstructor, parentConstructor)
{
    var F = function() { };
    F.prototype = merge(parentConstructor.prototype, childConstructor.prototype);
    childConstructor.prototype = new F();
    childConstructor.prototype.constructor = childConstructor;
    childConstructor.parent = parentConstructor.prototype;
}

/**
 * Localization
 */
var i18n = function(messages) {
    this._messages = messages || {};
    this.addMessages = function(messages) {
        this._messages = $.extend({}, this._messages, messages);
    };
    this.getMessage = function(code) {
        return this._messages[code] || code;
    };
};

/**
 * Template Engine
 */
var TemplateEngine = function() {
    this.compile = function(template) {
        return {
            render: function(scope) {
                var rendered = template;
                for(var key in scope) {
                    rendered = rendered.replace('{{' + key + '}}', scope[key]);
                }
                return rendered;
            }
        };
    };
};

/**
 * Init grid buttons
 */
$.fn.tableButtons = function(customOptions) {
    
    var $table = $(this),
        options = $.extend({}, {
            deleteSignature: 'delete',
            activateSignature: 'activate',
        }, customOptions || {});
    
    var local = new i18n(options.messages);
    
    // status
    $table.on('click', '.status', function(e) {
        e.preventDefault();
        
        var $a = $(this);

        $a.find('.glyphicon').removeClass('glyphicon-play glyphicon-pause').addClass('glyphicon-refresh');
        
        $.get($a.attr('href'), function(response) {
            if(1 === response.error) {
                alert(response.errorMessage);
                return;
            }
            
            if($a.hasClass('active')) {
                $a.attr('href', $a.attr('href').replace('suspend', 'activate'));
                $a.removeClass('active').addClass('suspended');
                $a.find('.glyphicon').removeClass('glyphicon-refresh').addClass('glyphicon-pause');
            }
            else {
                $a.attr('href', $a.attr('href').replace('activate', 'suspend'));
                $a.removeClass('suspended').addClass('active');
                $a.find('.glyphicon').removeClass('glyphicon-refresh').addClass('glyphicon-play');
            }
        });
    });
    
    // delete
    $table.on('click', '.delete', function(e) {
        e.preventDefault();
        
        var $a = $(this),
            $tr = $a.closest('TR');
            
            // delete
            if(!$tr.hasClass('deleted')) {
                $.get($a.attr('href'), function(response) {
                    if(1 === response.error) {
                        alert(response.errorMessage);
                        return;
                    }
                    
                    // change url
                    $a.attr('href', $a.attr('href').replace(options.deleteSignature, options.activateSignature));
                    
                    // trash icon to reload
                    $a.find('.glyphicon').removeClass('glyphicon-trash').addClass('glyphicon-refresh');
                    
                    // tr
                    $tr.addClass('deleted');
                });
            }
            // rollback
            else {
                $.get($a.attr('href'), function(response) {
                    if(1 === response.error) {
                        alert(response.errorMessage);
                        return;
                    }
                    
                    // change url
                    $a.attr('href', $a.attr('href').replace(options.activateSignature, options.deleteSignature));
                    
                    // trash icon to reload
                    $a.find('.glyphicon').removeClass('glyphicon-refresh').addClass('glyphicon-trash');
                    
                    // tr
                    $tr.removeClass('deleted');
                });
            }
    });
};

/*
 * Url class
 */
(function() {
    
    // global factory
    window.Url = function(url) {
        return new urlConstructor(url);
    };
 
    // consctructor
    var urlConstructor = function(url) {
        this._url = url;
    };
 
    urlConstructor.prototype = {
        _url: null,
 
         _merge: function()
        {
           var merged = {}, key;
 
           for(var i = 0; i < arguments.length; i++) {
               for(key in arguments[i]) {
                   if(merged[key] && typeof arguments[i][key] === 'object') {
                       merged[key] = this._merge(merged[key], arguments[i][key]);
                   }
                   else {
                       merged[key] = arguments[i][key];
                   }
               }
           }
 
           return merged;
        },
        
        _queryStringToArray: function(queryString) {
            var queryArray = {},
                queryPairs = queryString.split('&'),
                numericIndexCounter = {};
 
            for(var i in queryPairs) {
                var queryPair = queryPairs[i].split('='),
                    queryPairKey = queryPair[0],
                    queryPairValue = queryPair[1];
            
                var arrayKey = queryPairKey.match(/([^=&]+?)\[(.*)\]/);
                
                // param[]=value
                if(arrayKey) {
                    var arrayKeyName = arrayKey[1],
                        arrayKeyIndexList = arrayKey[2].split("][");
                
                    if(!queryArray[arrayKeyName]) {
                        queryArray[arrayKeyName] = {};
                        numericIndexCounter[arrayKeyName] = {};
                    }
                    
                    var pointer = queryArray[arrayKeyName];
                
                    for(var j = 0; j < arrayKeyIndexList.length; j++) {
                        // get index
                        var arrayKeyIndex = arrayKeyIndexList[j];
                        if(!arrayKeyIndex) {
                            if(j === 0) {
                                if(!numericIndexCounter[arrayKeyName][j]) {
                                    numericIndexCounter[arrayKeyName][j] = 0;
                                }
                                arrayKeyIndex = numericIndexCounter[arrayKeyName][j]++;
                            } else {
                                arrayKeyIndex = 0;
                            }
                        }
                        
                        // set pointer
                        if(j === arrayKeyIndexList.length - 1) {
                            pointer[arrayKeyIndex] = queryPairValue;
                        } else {
                            if(!pointer[arrayKeyIndex]) {
                                pointer[arrayKeyIndex] = {};
                            }
                            pointer = pointer[arrayKeyIndex];
                        }
                    }
                }
                // param=value
                else {
                    queryArray[queryPairKey] = queryPairValue;
                }
            }
 
            return queryArray;
        },
        
        _queryArrayToString: function(array, keyPrefix) {
            var pairs = [];
            for(var key in array) {
                var value = array[key];
                
                // get full key
                var fullKey;
                if(keyPrefix) {
                    fullKey = keyPrefix + '[' + key + ']';
                }
                else {
                    fullKey = key;
                }
                
                if(typeof value === 'object') {
                    pairs.push(this._queryArrayToString(value, fullKey));
                } else {
                    pairs.push(fullKey + '=' + value);
                }
            }
            
            return pairs.join('&');
        },
 
        merge: function(params) {
            if(!params) {
                return this;
            }
 
            var hostAndPath,
                queryArray,
                queryMarkPos = this._url.indexOf('?');
 
            if(queryMarkPos > 0) {
                queryArray = this._queryStringToArray(this._url.substr(queryMarkPos + 1));
                hostAndPath = this._url.substr(0, queryMarkPos);   
            } else {
                queryArray = {};
                hostAndPath = this._url;
            }
 
            if("object" !== typeof params) {
                params = this._queryStringToArray(params);
            }
 
            // merge
            queryArray = this._merge(queryArray, params);
 
            // update url
            this._url = hostAndPath + '?' + this._queryArrayToString(queryArray);
 
            return this;
        },
 
        toString: function() {
            return this._url; 
        }
    };
})();