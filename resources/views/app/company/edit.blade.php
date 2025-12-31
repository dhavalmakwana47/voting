@extends('app.layout.app')
@section('page_title')
    User Add
@endsection
@section('header-script')
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        Company Details {{ isset($company) ? 'Edit' : 'Add' }}
                    </h3>
                </div>
                {{-- Data insert and update in one file --}}
                <form action="{{ isset($company) ? route('company.update', $company->id) : route('company.store') }}" method="post">
                    @isset($company)
                        @method('PATCH')
                        <input type="hidden" value="{{ $company->id }}" name="id">
                    @endisset
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                        value="{{ isset($company) ? $company->name : old('company_name') }}"
                                        placeholder="Enter name">
                                    @error('company_name')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="company_email">Company Email</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email"
                                        value="{{ isset($company) ? $company->email : old('company_email') }}"
                                        placeholder="Enter email">
                                    @error('company_email')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="company_cin">Company CIN</label>
                                    <input type="text" class="form-control" name="company_cin" placeholder="Company CIN"
                                        id="company_cin"
                                        value="{{isset($company) ? $company->cin : old('company_cin') }}">
                                    @error('company_cin')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="subscriptions_period">Subscriptions Period</label>
                                    <input type="number" class="form-control" name="subscriptions_period"
                                        id="subscriptions_period"
                                        value="{{ isset($company) ? $company->subscriptions_period : old('subscriptions_period') }}"
                                        placeholder="Subscriptions Period">
                                    @error('subscriptions_period')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="resolution_meetingdate">Active Status</label>

                                <div>
                                    <input type="checkbox" data-bootstrap-switch="" name="is_active"
                                        {{ (isset($company) && $company->is_active) ? 'checked' : '' }}>
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
    <script>
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch();
        })
    </script>
@endsection
