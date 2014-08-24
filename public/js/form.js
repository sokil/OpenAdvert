(function()
{
    var Form = function($frm, options) 
    {
        var self = this;
        
        this.form = $frm;

        // events
        this.events = {
            beforeSubmit: [],
            success: []
        };
        
        /**
         * Set default options
         */
        if(!options) options = {};
        this.options = $.extend({}, {
            statusSelector: '.status',
            showErrorDescription: true,
            errorDescriptionMode: 'text',
            preloaderCssClass: 'prel',
            modelToFormFieldName: function(modelFieldName) {
                return modelFieldName;
            },
            onBeforeSubmit: null,
            onSuccess: null
        }, options);
        
        if(options.onBeforeSubmit) {
            this.onBeforeSubmit(options.onBeforeSubmit);
        }
        
        if(options.onSuccess) {
            this.onSuccess(options.onSuccess);
        }
        
        /**
         * Submit handler
         */
        $frm.submit(function(e)
        {
            e.preventDefault();

            // remove error messages
            $('.form-group.has-error', $frm).removeClass('has-error');
            $('.form-group .text-danger', $frm).remove();

            // trigger beforesubmit event
            if(!self.trigger('beforeSubmit')) {
                return;
            }
            
            // send message
            $(self.options.statusSelector, $frm).html('&nbsp;').addClass(self.options.preloaderCssClass);
            
            $.post($frm.attr('action'), $frm.serialize(), function(response) 
            {
                $(self.options.statusSelector, $frm).removeClass(self.options.preloaderCssClass);
                
                if(1 === response.error) {
                    self.setStatus(response.errorMessage, null, 'alert alert-danger');

                    // maybe invalidated
                    var $error, errors;

                    if(response.invalidated) {
                        for(var field in response.invalidated)
                        {
                            errors  = response.invalidated[field];
                            
                            var $input = $('*[name=' + self.options.modelToFormFieldName(field) + ']', $frm),
                                $error = [];

                            for(var rule in errors) {
                                $error.push(errors[rule]);
                            }

                            $input.closest('.form-group').addClass('has-error');
                            
                            if(self.options.showErrorDescription) {
                                
                                // mode: toolbox
                                if(self.options.errorDescription === 'toolbox') {
                                    $input.tooltip({
                                            title: $error.join(', '),
                                            placement: 'top',
                                            trigger: 'manual'
                                        })
                                        .tooltip('show')
                                        .keypress(function() {
                                            $input.tooltip('destroy');
                                            $input.closest('.form-group').removeClass('has-error');
                                        });
                                }
                                // mode: text
                                else {
                                    $('<div class="text-danger"></div>')
                                        .text($error.join(', '))
                                        .insertAfter($input);
                                
                                    $input.keypress(function() {
                                        $input.next('.text-danger').remove();
                                        $input.closest('.form-group').removeClass('has-error');
                                    });
                                }
                                    
                            }
                        }
                    }

                    return;
                }

                if('successMessage' in response) {
                    self.setStatus(response.successMessage, 5, 'alert alert-success');
                }
                
                self.trigger('success', $frm, response);

            }, 'json');
        });
    };
    
    Form.prototype = 
    {
        trigger: function(event, context, params)
        {
            
            var self = this,
                context = context || self;
            
            if(self.events[event].length === 0)
                return true;
            
            var success = true;
            for(var i = 0; i < self.events[event].length; i++) {
                if(false === self.events[event][i].call(context, params)) {
                    success = false;
                }
            }
            
            return success;
        },
                
        onBeforeSubmit: function(handler)
        {
            if("function" === typeof handler) {
                this.events.beforeSubmit.push(handler);
            }
        },
        
        onSuccess: function(handler)
        {
            if("function" === typeof handler) {
                this.events.success.push(handler);
            }
        },
        
        setStatus: function(text, hideAfterSecs, cssClass)
        {            
            var $status = $(this.options.statusSelector, this.form);
            if(0 === $status.length) {
                $status = $('<span class="status"></span>').insertAfter(this.form.find('input[type=submit]'));
            }
                
            $status
                .html(text)
                .show()
                .removeClass().addClass('status');;
        
            if(cssClass) {
                $status.addClass(cssClass);
            }
            
            if(!hideAfterSecs)
                return this;
            
            setTimeout(function() {
                $status.fadeOut();
            }, 
            hideAfterSecs * 1000);
            
            return this;
        }
    };
    
    $.fn.form = function()
    {
        var $frm = this;
        
        if(arguments.length === 0 || typeof arguments[0] === "object") {
            var formHandlerInstance = new Form($frm, arguments.length === 0 ? null : arguments[0]);
            $frm.data('instance', formHandlerInstance);
        }
        else {
            var formHandlerInstance = $frm.data('instance');
            
            // check if handler initialized on form
            if(arguments[0] === 'isInitialized') {
                return !!$frm.data('instance');
            }
            
            // call method
            var methodName = arguments[0],
                formHandlerInstance = $frm.data('instance'),
                formHandlerMethod = formHandlerInstance[methodName];
            
            if(arguments.length === 1) {
                formHandlerMethod.call(formHandlerInstance);
            }
            else {
                formHandlerMethod.apply(formHandlerInstance, Array.prototype.slice.call(arguments, 1));
            }
        }
        
        return this;
        
    };
    
})();