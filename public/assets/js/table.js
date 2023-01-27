$(document).ready(function(){
    $('.dataTable.with-no-export').DataTable({
        "ordering": false
    }); 
    $(".dataTable.with-export").DataTable({
        dom: "Bfrtip",
        buttons: [
            { extend: "excel", text: "EXCEL", className: "btn btn-default", exportOptions: { columns: "th:not(:last-child)" } },
            { extend: "pdf", text: "PDF", className: "btn btn-default", exportOptions: { columns: "th:not(:last-child)" } },
        ],
    });
});