(function() {
    window.modal = function(a, customParams) {
        
        var $a = $(a),
            id = $a.data('modal-id'),
            params = $.extend({}, {
                replaceEntireModal: true
            }, customParams),
            $modal;
        
        if(!id) {
            // create modal id
            id = 'modal-' + Math.round(Math.random() * (new Date()).getTime());
            $a.attr('data-target', '#' + id);
            
            // create modal window
            $modal = $('<div class="modal fade" role="dialog" aria-hidden="true" />')
                .attr('id', id)
                .attr('aria-labelledby', id)
                .appendTo(document.body);
        
            if(!params.replaceEntireModal) {
                $modal.append(
                    $('<div class="modal-dialog"/>').append(
                        $('<div class="modal-content">').append([
                            // header
                            $('<div/>').addClass('modal-header').append([
                                $('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'),
                                $('<h4 class="modal-title">' + ($a.attr('title') || '&nbsp;') + '</h4>')
                            ]),

                            // body
                            $('<div/>').addClass('modal-body'),

                            //footer
                            $('<div/>').addClass('modal-footer')
                        ]))
                    )
                    ;
                
            }
            
            // apply config
            if(typeof params === 'object') {
                for(var name in params) {
                    switch(name) {
                        case 'hide':
                        case 'hidden':
                        case 'show':
                        case 'shown':
                            $modal.on( name, params[name]);
                            break;
                    }
                }
            }
        } else {
            // already created modal
            $modal = $('#' + id);
        }

        // launch modal
        $.get($a.attr('href'), function(response) {
            if(params.replaceEntireModal) {
                $modal.modal({remote: $a.attr('href')});
            }
            else {
                $modal.find('.modal-body').html(response);
                $modal.modal();
            }
        });
    };
    
})();