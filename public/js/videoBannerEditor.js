var videoBannerEditor = function() {
    videoBannerEditor.parent.constructor.apply(this, arguments);    
};

videoBannerEditor.prototype = {
    
    initParametersForm: function()
    {
        videoBannerEditor.parent.initParametersForm.apply(this, arguments);
        
        var self = this,
            $form = this.parametersForm;

        // add new event block
        $('#cmdAddEvent').click(function() {
           var event = $('#selAvailableEventList').val();
           self.addEvent(event);        
        });

        // mark event as deleted
        $('#frmVideoBannerEvents').on('click', '.trash', function(e) {
           e.preventDefault();
           var $a = $(this);
           var $icon = $a.find('.glyphicon');

           if($icon.hasClass('glyphicon-trash')) {
              // change icon
              $icon.removeClass('glyphicon-trash').addClass('glyphicon-refresh');
              // mark all deleted
              $a.closest('.input-group').addClass('deleted');
           }
           else {
              // change icon
              $icon.removeClass('glyphicon-refresh').addClass('glyphicon-trash');
              // mark all un-deleted
              $a.closest('.input-group').removeClass('deleted');
           }
        });
       
        // add beforeSubmit event to form
        this.parametersForm.form('onBeforeSubmit', function() {
            if($('#mediaFileList tbody tr').length === 0) {
                alert(self.i18n.getMessage("You need to attach media file"));
                return false;
            }
            
            // remove deleted events
            $('#frmVideoBannerEvents .event.deleted').remove();
        });
       
        // uploader button
        $('#cmdUpload input[type=file]').uploader({
            uploadHandlerUrl: '/banners/uploadmediafile',
            uploadHandlerParams: function() {
                return {
                    id: $form.find('input[name=id]').val(),
                    campaign: $form.find('input[name=campaign]').val()
                };
            },
            onbeforeupload: function() {
                // create tr
                this.$tr = $('<TR>').append(
                    $('<TD colspan="5">').append(
                        $('<div class="progress" style="margin: 0;">').append(
                            $('<div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="text-align: center;">0%</div>')
                        )
                    )
                );
        
                $('#mediaFileList TBODY').prepend(this.$tr);
            },
            onprogress: function(loaded, total) {
                var persents = Math.ceil(loaded / total * 100);
                var $progress = this.$tr.find('.progress');

                $progress.find('.progress-bar')
                    .css({width: persents + "%"})
                    .text(persents + '%');

                if (100 === persents) {
                    setTimeout(function() {
                        $progress.hide();
                        $progress.find('.progress-bar')
                            .css({width: "0%"})
                            .text('0%');
                    }, 800);
                }
            },
            oninvalidfile: function(code) 
            {
                this.$tr.find('.progress').closest('TR').remove();
                switch(code) {
                    case this.VALIDATE_ERROR_FORMAT:
                        alert(self.i18n.getMessage('Wrong file format'));
                        break;
                    case this.VALIDATE_ERROR_SIZE:
                        alert(self.i18n.getMessage('Wrong file size'));
                        break;
                };
            },
            onerror: function(message) {
                this.$tr.find('.progress').closest('TR').remove();
                alert(message);
            },
            onafterupload: function() {
                this.$tr.find('.progress').closest('TR').remove();
            },
            onsuccess: function(response) {
                $form.find('input[name=id]').val(response.bannerId);
                self.addMediaFile(response.url, response.delivery, response.id, response.size, response.type);
            },
            supportedFormats: ['flv', 'mp4'],
            maxSize: 10000000
        });
        
        // delete video handler
        $('#mediaFileList').on('click', '.delete-media', function(e) {
            e.preventDefault();
            var $a = $(this);
            $.get($a.attr('href'), function(response) {
                if(response.error == 1) {
                    alert(response.errorMessage);
                }
                
                $a.closest('TR').fadeOut(function() {
                    $(this).remove();
                });
            });
        });
    },
    
    /**
     * function for add media file to dom
     */
    addMediaFile: function(url, delivery, id, size, type) {
        if(!url) {
            url = '';
        }
        
        if(!delivery) {
            delivery = 'progressive';
        }
        
        if(!id) {
            id = '';
        }

        // add new media file
        $('#mediaFileList TBODY').prepend($('<tr/>').append([
            $('<td><a href="' + url + '">' + url + '</a></td>'),
            $('<td>' + delivery + '</td>'),
            $('<td>' + size + '</td>'),
            $('<td>' + type + '</td>'),
            $('<td>').append([
                $('<a href="/banners/deletemediafile?id=' + id + '" class="delete-media">')
                    .append('<span class="glyphicon glyphicon-trash"></span>')
            ])
        ]));
    },
    
    addEvent: function(event, url) {
        if(!event) {
            throw Error('Event must be specified');
        }

        if(!url) {
            url = '';
        }

        return $('<div class="event input-group bottom-space"/>')
            .append([
                $('<span class="input-group-addon" style="width: 150px;"/>').text(event),
                $('<input type="text" name="events[' + event + '][]" class="form-control" value="' + url + '" />'),
                $('<span class="input-group-addon"/>')
                    .append(
                        $('<a href="javascript:void(0);" class="trash" />')
                            .append('<span class="glyphicon glyphicon-trash"/>')
                    )
            ])
            .insertAfter($('#cmdAddEvent').closest('.input-group'));
    },

    /**
     * add existed media files to list
     */
    addMediaFileList: function(mediaFileList)
    {
        if(!mediaFileList.length) {
            return;
        }
        // add to dom
        for(var i in mediaFileList) {
            this.addMediaFile(
                mediaFileList[i].url,
                mediaFileList[i].delivery,
                mediaFileList[i].id,
                mediaFileList[i].size,
                mediaFileList[i].type
            );
        }
    },

    /*
     * Add existed events
     */ 
    addEventsList: function(eventsList)
    {
        if(!eventsList) {
            return;
        }

        for(var eventName in eventsList) {
            for(var i in eventsList[eventName]) {
                this.addEvent(eventName, eventsList[eventName][i]);
            }
        }    
    }
};

extend(videoBannerEditor, bannerEditor);