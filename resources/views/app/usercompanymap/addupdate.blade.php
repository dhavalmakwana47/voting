@extends('app.layout.app')
@section('page_title')
    Admin-Assign
@endsection
@section('header-script')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        Assignee
                    </h3>
                </div>
                {{-- Data insert and update in one file --}}
                <form action="{{ route('usercompanymap.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="company_list">Company</label>
                                    <select name="company" class="form-control select2" id="company_list"
                                        data-placeholder="Select a Company">
                                        <option value=""></option>
                                        @foreach ($companyArr as $company)
                                            <option value="{{ $company->id }}"
                                                {{ old('company') == $company->id ? 'selected' : '' }}>{{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>New Scrutinizer </label>
                                    <select class="form-control select2bs4" multiple="multiple" name="scrutinizer[]"
                                        data-placeholder="Select a Scrutinizer" style="width: 100%;" id="scrutinizer">
                                        @foreach ($scrutinizerArr as $scrutinizer)
                                            <option value="{{ $scrutinizer->id }}"
                                                {{ is_array(old('scrutinizer')) && in_array($scrutinizer->id, old('scrutinizer')) ? 'selected' : '' }}>
                                                {{ $scrutinizer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('scrutinizer')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>New AR </label>
                                    <select class="select2bs4" multiple="multiple" name="ar[]"
                                        data-placeholder="Select a AR" style="width: 100%;" id="ar">
                                        @foreach ($authorizedPersonArr as $authorizedPerson)
                                            <option value="{{ $authorizedPerson->id }}"
                                                {{ is_array(old('ar')) && in_array($authorizedPerson->id, old('ar')) ? 'selected' : '' }}>
                                                {{ $authorizedPerson->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ar')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" name="submit" class="btn btn-primary">
                            Save
                        </button>
                        &nbsp;
                    </div>
                </form>

            </div>
        </div>
    </div>
    </div>
@endsection
@section('footer-script')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $('.select2').select2()
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
        $('#company_list').on('change', function() {
            var id = $(this).val();
            if (id != "") {
                $.ajax({
                    type: 'post',
                    url: "{{ route('usercompanymap.assign_users') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "company_id": id
                    },
                    success: function(res) {
                        var users = res['users'];
                        $('#scrutinizer').val([]).trigger('change');
                        $('#scrutinizer').val(users).trigger('change');

                        $('#ar').val([]).trigger('change');
                        $('#ar').val(users).trigger('change');

                    }
                });
            }
        })
    </script>
@endsection
