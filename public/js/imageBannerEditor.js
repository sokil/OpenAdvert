var imageBannerEditor = function() {
    imageBannerEditor.parent.constructor.apply(this, arguments);    
};

imageBannerEditor.prototype = {
    
    initParametersForm: function()
    {
        imageBannerEditor.parent.initParametersForm.apply(this, arguments);
        
        var $form = this.parametersForm;
        
        // banner form
        this.parametersForm.form('onSuccess', function() {
            $('#bannerImage').show().find('img').attr('src', $form.find('input[name=imageUrl]').val());
        });
        
        // upload image form
        $('#cmdUploadBannerImage').uploader({
            uploadHandlerUrl: '/banners/uploadimage',
            uploadHandlerParams: function() {
                return {
                    id          : $form.find('input[name=id]').val(),
                    campaign    : $form.find('input[name=campaign]').val()
                };
            },
            onprogress: function(loaded, total) {
                var persents = Math.ceil(loaded / total * 100);
                var $progress = $('#cmdUploadBannerImage')
                    .closest('.form-group')
                    .find('.progress')
                    .show();
            
                $progress.find('.progress-bar')
                    .css({width: persents + "%"})
                    .text(persents + '%');
            
                if(100 === persents) {
                    setTimeout(function() {
                        $progress.hide();
                        $progress.find('.progress-bar')
                            .css({width: "0%"})
                            .text('0%');
                    }, 800);
                }
            },
            onsuccess: function(response) {
                $('input[name=id]', $form).val(response.id);
                $form.find('input[name=imageUrl]').val(response.imageUrl);
                
                $('#bannerImage').show().find('img').attr('src', response.imageUrl + '?' + Math.random());
                
            }
        });
    }
};
    
extend(imageBannerEditor, bannerEditor);