var bannerEditor = function()
{
    
};

bannerEditor.prototype.i18n = new i18n();

bannerEditor.prototype.parametersForm = null;

bannerEditor.prototype.initParametersForm = function()
{    
    // form handler
    this.parametersForm = $('#parameters form').form({
        onSuccess: function(response) {
            
            var isNew = parseInt($('#parameters').data('new-banner'));
            
            if(isNew) {
                $('#parameters').attr('data-new-banner', 0);
                // set id
                $('#parameters input[name=id]').val(response.bannerId);
                // change url
                if(history.pushState) {
                    history.pushState(null, null, '/banners/edit/id/' + response.bannerId);
                }
                // activate disabled tabs
                $('#bannerEditorTabs').find('li.disabled').each(function(i, li) {
                    var $li = $(li);
                    $li.removeClass('disabled');
                    $li.find('a').attr('data-toggle', 'tab');
                });
            }
        }
    });
};

bannerEditor.prototype.initDeliveryOptionsForm = function()
{
    // form
    $('#frmDeliveryOptions').form({
        onBeforeSubmit: function() {
            $('#frmDeliveryOptions input[name=id]').val($('#parameters input[name=id]').val());
            $('#frmDeliveryOptions div.deleted').remove();
        }
    });
    
    // add new option
    $('#cmdAddNewOption').click(function(e) {
        e.preventDefault();
        
        var $cmd = $(this),
            optionType = $('#comboOptionTypes').val();
        
        $.get('/banners/deliveryOption?type=' + optionType, function(response) {
            $cmd.closest('.form-group').after(response);
        });
    });
    
    // delete buttons
    $('#frmDeliveryOptions').on('click', '.delete', function(e) {
           e.preventDefault();
           var $a = $(this);
           var $icon = $a.find('.glyphicon');

           if($icon.hasClass('glyphicon-trash')) {
              // change icon
              $icon.removeClass('glyphicon-trash').addClass('glyphicon-refresh');
              // mark all deleted
              $a.closest('.deliveryOption').addClass('deleted');
           }
           else {
              // change icon
              $icon.removeClass('glyphicon-refresh').addClass('glyphicon-trash');
              // mark all un-deleted
              $a.closest('.deliveryOption').removeClass('deleted');
           }
    });
};

bannerEditor.prototype.initBannerZonesForm = function()
{
    $('#frmBannerZones').form({
        onBeforeSubmit: function() {
            $('#frmBannerZones input[name=id]').val($('#parameters input[name=id]').val());
        }
    });
};