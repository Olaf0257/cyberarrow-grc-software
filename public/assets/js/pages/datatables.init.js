$(document).ready(function(){
    $("#basic-datatable").DataTable({        
        lengthChange: false,
        searching: false,
        ordering: false,
        language:{
            paginate:{
                previous:"<i class='mdi mdi-chevron-left'>",
                next:"<i class='mdi mdi-chevron-right'>"
            }
        },

        drawCallback:function(){
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
        }
    });    

});