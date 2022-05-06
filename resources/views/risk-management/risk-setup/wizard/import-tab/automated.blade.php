<p class="text-center">
Choose the corresponding compliance project to generate your risks. 
</p>
<form action="{{ route('risks.wizard.automated-risk-setup') }}" method="Post" id="risk-wizard-automated-import-form" class="w-100">
    @csrf
    <div class="row mb-3">
        <label for="inputPassword" class="col-lg-1 col-md-1 col-sm-1 offset-lg-4 offset-md-4 offset-sm-3 col-form-label">Project</label>
        <div class="col-lg-3 col-md-3 col-sm-4">
            <select class="form-control select2-inputs" name="project">
                <option value="">Select Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
                //getting the name of the standard selected by the user 
                <input type="hidden" name="riskSetUpStandard" value = "{{ $riskSetUpStandard }}"  />
              </select>
            <div class="invalid-feedback d-block" id="automated-setup-error">
                @if ($errors->has('project'))
                    {{ $errors->first('project') }}
                @endif
            </div>
        </div>
    </div>

    <div class="import-sec-btn d-flex">
        <button type="submit" class="btn btn-primary btn-generate-risk ms-auto generate-risk-register">Generate Risk Register</button>
    </div>
</form>

