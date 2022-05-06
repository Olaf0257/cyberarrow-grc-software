<div class="tab-pane department {{ ($activeTab == 'organization_settings') ? 'show active' : ''}}" id="organization-tab">
    <div class="row">
        <div class="col clearfix">
            <!-- Button trigger modal -->
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div id="accordion" class="mb-3">
                @if(count($organizations) > 0)
                    @foreach($organizations as $organization)
                        <div class="card mb-1">
                            <div class="card-header organization-card-header" id="heading_{{$organization->id}}">
                                <h5 class="position-relative m-0">
                                    <a class="text-dark organization-collapse-el" data-toggle="collapse" href="#collapse_{{$organization->id}}" aria-expanded="true">
                                        <i class="mdi mdi-office-building me-1 secondary-text-color"></i>
                                        {{ decodeHTMLEntity($organization->name)}}
                                    </a>


                                    <div class="organization-actions">
                                        <a  href="{{ route('global-settings.organizations.departments.store', $organization->id) }}" data-get-departments-link="{{ route('global-settings.organizations.departments', $organization->id) }}" class="btn btn-primary btn-xs waves-effect waves-light text-white add-department-link">
                                            <i class="fe-plus"></i>
                                        </a>

                                        <a href="{{ route('global-settings.organizations.update',$organization->id) }}" data-organization-name="{{ $organization->name}}" class="btn btn-info edit-organizations-action btn-xs waves-effect waves-light text-white me-1">
                                            <i class="fe-edit"></i>
                                        </a>
                                    </div>
                                </h5>
                            </div>


                            <div id="collapse_{{$organization->id}}" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                @if($organization->departments->count() > 0)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="custom-dd dd nestable_list-wp" id="nestable-list_{{ $organization->id }}" data-organization-id="{{ $organization->id }}">
                                                @include('administration.global-settings.organization-settings.departments', [ 'departments' => $organization->departments])
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col-md-12">
                                            <form action="{{ route('global-settings.organizations.departments.save-nested-departments', $organization->id) }}" method="post">
                                                @csrf
                                                <textarea style="display: none;" name="nested_department_array" id="nestable-output_{{$organization->id}}"></textarea>
                                                <button type="submit" class="btn btn-primary float-end" style="margin-top: 15px;" >Save changes</button>
                                            </form>
                                        </div>
                                        <!-- end col -->
                                    </div> <!-- end row -->
                                @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                {{-- cart for icon of add organization --}}
                <p class="sub-header text-center">
                    <strong>Organization</strong> must be added before proceeding further.
                </p>
                <div class="row">
                    <div class="col-lg-4 col-sm-6 offset-lg-4 offset-sm-3" data-toggle="modal" data-target="#add-organization-modal">
                        <a href="" data-toggle="modal" data-target="#add-campaign-modal">
                            <div class="card">
                                <div class="card-body project-box project-div d-flex justify-content-center align-items-center" style="min-height: 15.5rem; font-size: 4rem; color: #323b43;">
                                    <i class="mdi mdi-plus"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                @endif
            </div> <!-- end #accordions-->
        </div> <!-- end col -->
    </div>
</div>

<!-- Edit Organization Modal -->
<div id="edit-organization-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" >
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">
                        Edit Organization
                    </h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="field-1" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"  placeholder="Add organization" value="" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->

<!-- Add Organization Modal -->
<div id="add-organization-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('global-settings.organizations.store') }}" id="add-organization-form"   >
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">
                        Add Organization
                    </h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="field-1" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"  placeholder="Add organization"  >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit"  class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->

<!-- Add Department Modal -->
<div id="add-department-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form action="" method="POST" id="add-department-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Department</h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="department-name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" id="department-name" placeholder="Department Name" required >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-0">
                                <label for="field-3" class="form-label">Parent department</label>
                                <select name="parent_id" class="form-control parent-department">
                                    <option value="">No parent</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div><!-- /.modal -->
