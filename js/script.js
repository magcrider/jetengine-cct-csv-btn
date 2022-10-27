(function ($) {
    $(document).on('click', '.download-csv', function (e) {
        e.preventDefault();
        $.ajax({
            url: dcms_vars.ajaxurl,
            type: 'post',
            data: {
                action: 'dcms_ajax_readmore',
            },
            success: function (resultado) {
                var hiddenElement = document.createElement('a');
                hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(resultado);
                hiddenElement.download = 'Leads.csv';
                hiddenElement.click();
            }
        });
    });
})(jQuery);