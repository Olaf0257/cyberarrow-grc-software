$(document).on('click', ".department-delete", function(event) {
    event.preventDefault()

    let departmentTransferableUserCount = this.dataset.departmentTransferableUserCount

    let userDepartmentAssignmentsUrl = this.dataset.userDepartmentAssignmentsUrl

    const departmentId = this.dataset.departmentId

    const organizationId = this.dataset.organizationId
    let deleteUrl = $(this).attr('href')

    const transferableDepartmentUrl = this.dataset.transferableDepartmentUrl

    //getting user count involved with the  department
    $.get(departmentTransferableUserCount)
        .done(function(res) {
            if (res.data > 0) {
                var selectOptions = {}

                // getting all other department expect the deleting one
                $.get(userDepartmentAssignmentsUrl,
                    function(res) {
                        if (res.success) {

                            let department = res.data;
                            selectOptions[0] = 'Select';
                            Object.keys(department).forEach((index) => {
                                selectOptions[department[index].id] = `${department[index].name}`;
                            });


                            Swal.fire({
                                    title: 'Select&nbsp;a&nbsp;department&nbsp;to&nbsp;transfer&nbsp;user(s)&nbsp;to:',
                                    input: 'select',
                                    inputOptions: selectOptions,
                                    showCloseButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: '#b2dd4c',
                                    imageUrl: '{{ asset('assets/images/info1.png') }}',
                                    imageWidth: 120,
                                    onBeforeOpen: function() {
                                        $('.swal2-select').select2({
                                            width: '150%',
                                            language: "it"
                                        });
                                        $('select option[value="0"]').prop('disabled',true);
                                    }
                                })
                                .then(res => {
                                    if (!res.dismiss && res.value) {
                                        // transfering old department users to newly selected department
                                        $.post(transferableDepartmentUrl, {
                                                '_token': '{!! csrf_token() !!}',
                                                transfer_to: res.value
                                            })
                                            .done(function(response) {
                                                if (response.success) {

                                                    //delete department
                                                    deleteDepartment()
                                                } else {
                                                    Swal.fire({
                                                        title: 'Oops...',
                                                        text: response.message,
                                                        imageUrl: '{{asset('assets/images/error.png')}}',
                                                        imageWidth: 100,
                                                        confirmButtonColor: '#ff0000'
                                                    })
                                                }
                                            });
                                    }
                                })
                        }
                    });
            } else {
                //delete department
                // Are you sure
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this",
                        showCancelButton: true,
                        confirmButtonColor: '#ff0000',
                        confirmButtonText: 'Yes, delete it!',
                        closeOnConfirm: false,
                        imageUrl: '{{asset('assets/images/warning.png')}}',
                        imageWidth: 120

                    })
                    .then(confirmed => {
                        if (confirmed.value && confirmed.value == true) {
                            deleteDepartment()
                        }
                    });

            }
        });

    // delete department

    function deleteDepartment() {
        //delete the department
        $.get(deleteUrl).done(function(response) {

            console.log(response);
            if (response.success) {
                Swal.fire({
                    title: 'Department deleted successfully',
                    confirmButtonColor: '#b2dd4c',
                    imageUrl: '{{asset('assets/images/success.png') }}',
                    imageWidth: 120
                })
                //delete after ajax call
                $(".dd-item[data-id='" + departmentId + "']").remove();
            }

        });

    }

})
