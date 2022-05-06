<ol class="dd-list ">
    @foreach($departments as $department)
    <li class="dd-item" data-id="{{$department->id}}">

            <div class="dd-handle">
                {{ decodeHTMLEntity($department->name) }}
            </div>
            <div class="dd-handle-actions">
                <a href="{{ route('global-settings.organizations.departments.store', $organization->id) }}"
                    data-department-id="{{$department->id}}"
                    data-get-departments-link="{{ route('global-settings.organizations.departments', $organization->id) }}"
                    class="btn btn-primary btn-xs waves-effect waves-light add-department-link">
                    <i class="fe-plus"></i>
                </a>

                <a href="{{ route('global-settings.organizations.departments.update', [$organization->id, $department->id]) }}"
                    title="Edit Information"
                    data-parent-id="{{$department->parent_id}}"
                    class="btn btn-info btn-xs waves-effect waves-light edit-department-action"
                    data-toggle='tooltip'
                    data-edit-url="{{ route('global-settings.organizations.departments.edit', [$organization->id, $department->id]) }}"
                    data-get-departments-link="{{ route('global-settings.organizations.departments', $organization->id) }}"
                    data-original-title='Edit'>
                    <i class='fe-edit'></i>
                </a>

                <a  href="{{ route('global-settings.organizations.departments.delete', [$organization->id, $department->id]) }}"
                     data-department-id="{{$department->id}}"
                     data-organization-id="{{$organization->id}}"
                     data-department-transferable-user-count="{{route('global-settings.organizations.departments.department-transferable-user-count', [$organization->id, $department->id])}}"
                     data-transferable-department-url="{{route('global-settings.organizations.departments.department-transferable', [$organization->id, $department->id])}}"
                     data-user-department-assignments-url="{{route('global-settings.organizations.departments.department-transferable-user', [$organization->id, $department->id])}}"
                     title='Delete'
                    class='btn btn-danger btn-xs waves-effect waves-light department-delete'
                    data-animation='blur'

                    data-overlayColor='#38414a'>
                    <i class='fe-trash-2'></i>
                </a>
            </div>

        @if(count($department->departments) > 0 )
            @include('administration.global-settings.organization-settings.departments', [ 'departments' => $department->departments])
        @endif
    </li>
    @endforeach
</ol>

