
$(document).on('click', ".disable-user", function(event) {
            event.preventDefault()

            let userProjectAssignmentsUrl = this.dataset.userProjectAssignmentsUrl
            const userId = this.dataset.userId
            const userTransferAssignmentUrl = this.dataset.transferAssignmentsUrl
            const disableUserUrl = this.href
            const assignmentTransferableUserUrl = this.dataset.assignmentTransferableUserUrl

            $.get( userProjectAssignmentsUrl )
                .done(function( res ) {
                    if(res.data > 0){
                        var selectOptions = { }

                        // requesting for assignment transferable user
                        $.get(assignmentTransferableUserUrl,
                        function (res) {
                            if(res.success){
                                let users = res.data;
                                selectOptions[''] = 'select';


                               Object.keys(users).forEach((index) => {
                                    selectOptions[users[index].id] = `${users[index].full_name} - ${users[index].email}`;
                                });

                                Swal.fire({
                                    title: 'Select&nbsp;a&nbsp;user&nbsp;to&nbsp;transfer&nbsp;responsibility&nbsp;to:',
                                    input: 'select',
                                    inputOptions: selectOptions,
                                    showCloseButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: '#b2dd4c',
                                    imageUrl: '{{ asset('assets/images/info1.png') }}',
                                    imageWidth: 120,
                                    onBeforeOpen: function () {
                                        $('.swal2-select').select2({
                                            width: '100%',
                                            language: "it"
                                        });
                                    }
                                })
                                .then(res => {
                                        if(!res.dismiss && res.value){
                                            // transfering assignments
                                            $.post(userTransferAssignmentUrl, {
                                                    '_token' : '{!! csrf_token() !!}',
                                                    transfer_to: res.value
                                                })
                                                .done(function(response) {
                                                    if(response.success){
                                                        // disabling user
                                                        disableUser()
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
                        // disabling user
                        disableUser()
                    }
                });

            // disabling user
            function disableUser(){
                // request to disabling user
                $.get(disableUserUrl,  // url
                function (response) {

                    if(response.success){
                        if(typeof usersListDatatable !== 'undefined' ){
                            usersListDatatable.ajax.reload(null, false);
                        } else {
                            $('#page-wrapper').load(document.URL +  ' #page-wrapper > *', function(){
                                validateUserInfoUpdateForm();
                                $(".select2-multiple").select2()
                            })
                        }

                        Swal.fire({
                            text: 'User disabled successfully',
                            confirmButtonColor: '#b2dd4c',
                            imageUrl: '{{ asset('assets/images/success.png') }}',
                            imageWidth: 120
                        })
                    }
                });
            }
        }) // disable user
