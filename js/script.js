(function ($) {
  $(document).on("click", ".download-csv", function (e) {
    e.preventDefault();
    button = $(this);
    schoolID = button.attr("school-id");
    leadsTable = button.attr("leads-t");
    $.ajax({
      url: dcms_vars.ajaxurl,
      type: "post",
      data: {
        action: "dcms_ajax_readmore",
        school_id: schoolID,
        leads_table: leadsTable,
      },
      success: function (resultado) {
        var hiddenElement = document.createElement("a");
        hiddenElement.href =
          "data:text/csv;charset=utf-8," + encodeURI(resultado);
        hiddenElement.download = "Leads.csv";
        hiddenElement.click();
      },
    });
  });
})(jQuery);
