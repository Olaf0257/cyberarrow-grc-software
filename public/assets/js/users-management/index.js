 // users role select2-multiple init
 var userRolesSelect2 = $('#user-roles-select2').select2();

 userRolesSelect2.on('select2:select select2:unselect', function (e) {
     let selectedItems = $(this).val();
     if(selectedItems.includes('Global Admin')){
         selectedItems.forEach(function(selectedItem) {
             if(selectedItem != 'Global Admin'){
                 // set the select with value A to unselected
                 userRolesSelect2.find(`option[value='${selectedItem}']`).prop("selected",false);
                 userRolesSelect2.trigger("change");
             }
         })
     }         
 });