(function() {
    
    /**
     * Init
     */
    window.statApp = {
        
        _options: null,
        
        init: function(options) {
            
            this._options = options;
            
            var self = this;
            
            // selector
            $('#menu a').click(function(e) {
                e.preventDefault();
                
                self.handleMainMenuClick($(this));
            });

            // subselector
            $('#subMenu').on('click', 'a', function(e) {
                e.preventDefault();
                self.handleSubMenuClick($(this));
            });
            
            // handle clicks on links in container
            $('#container').on('click', 'a[href!="#"]', function(e) {
                e.preventDefault();
                var $a = $(this);
                self._loadPage($a.attr('href').substr(1));
            });
            
            // init calendar
            this.calendar.init();
            
            // subscribe calendar change to page load
            this.calendar.subscribe(function() {
                self._loadPage(location.hash.substr(1));
            });

            // load first page
            this.handleMainMenuClick($('#menu a:first'));
        },

        handleMainMenuClick: function($a) {
            $('#menu a').removeClass('active');
            $a.addClass('active');

            this.subMenu.clear();
            this._loadPage($a.attr('href').substr(1));
        },
        
        handleSubMenuClick: function($a) {                
            $('#subMenu li').removeClass('active');
            $a.closest('LI').addClass('active');

            this._loadPage($a.attr('href').substr(1));
        },
        
        _loadPage: function(url) {    
            
            // change hash
            location.hash = url;
            url = '/' + url;
            
            // show preloader
            $('#container').html('<img src="/images/prel.gif" style="margin: 10px;" />');
            
            // load page
            $.get(Url(url).merge(this.calendar.getValue()).toString(), function(response) {
                // load page content
                $('#container').html(response);
            }, 'html');
        }
    };
    
    /**
     * Calendar
     */
    window.statApp.calendar = {
        
        _subscribers: [],
        
        init: function() {
            var self = this;
            
            // date-from datepicker
            $('#dateRange').pickmeup({
                mode            : 'range',
                calendars       : 3,
                format          : 'Y-m-d',
                hide_on_select  : true,
                locale          : statApp._options.calendar.locale,
                separator       : ' - ',
                change          : function(value) {
                    
                    $('#dateRange')
                        .text(value[0] + ' - ' + value[1])
                        .data('query', 'dateFrom=' + value[0] + '&dateTo=' + value[1]);
                    
                    self._subscribers.map(function(subscriber) {
                        if(typeof subscriber === 'function') {
                            subscriber.call(this);
                        }
                    });
                }
            });
            
            // set default value
            var value = $('#dateRange').text().split(' - ');
            $('#dateRange').data('query', 'dateFrom=' + value[0] + '&dateTo=' + value[1]);
        },
        
        getValue: function() {
            return $('#dateRange').data('query');
        },
        
        subscribe: function(handler) {
            this._subscribers.push(handler);
        }
    };
        
    /**
     * Submenu
     */
    window.statApp.subMenu = {
        clear: function() {
            $('#subMenu').empty();
        },
        add: function(url, caption) {
            $('<LI>')
                .append(
                    $('<A>').attr('href', url).text(caption)
                )
                .appendTo($('#subMenu'));
        },
        load: function(url) {
            var self = this;
            $.get(url, function(response) {
                for(var i in response.subMenu) {
                    var item = response.subMenu[i];
                    self.add(item.url, item.caption);
                }

                // select forst as active
                window.statApp.handleSubMenuClick($('#subMenu a:first'));
            }, 'json');
        }
    };
    
})();