@php
    $isUpdateMode = true;
@endphp
@extends('app.member.layout')
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
    <style>
        .checkbox-error {
            outline: 2px solid red;
            border-radius: 6px;
            padding: 5px;
        }
        .checkbox-limit-info {
        font-size: 0.9em;
        color: #888;
    }

    .checkbox-error .checkbox-limit-info {
        color: red;
        font-weight: bold;
    }
    </style>
@endsection
@section('body-content')
    <div class="modal fade" id="add-member-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="form-title">Voting Instruction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="votingInstr">
                        <p>Kindly vote here.</p>
                        <p>Only when the <strong>Yes</strong> , <strong>No</strong>, or
                            <strong>Abstain</strong> option is selected for each Agenda will votes be taken into action.
                        </p>
                        <p>Just before clicking <strong>Submit</strong>," please double-check your votes</p>
                        <p>Your vote will be recorded for each Agenda you have chosen once you click
                            <strong>Submit</strong>and
                            you can modify it by adjusting the Schedule Voting Start and End Date & Time.
                        </p>
                        <p>It is implied that you have not voted for a Agenda when you leave a selection blank.</p>
                        <p>Click the <strong>Agenda File Link</strong>
                            below to view the Agenda file.
                        </p>
                    </div>
                </div>

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title" style="font-size: 21px;">Voter Voting Screen</h3>
                        </div>

                        {{-- Datatable --}}
                        <div class="card-body">

                            <button type="button" class="btn btn-danger" data-toggle="modal"
                                data-target="#add-member-modal">Instruction for Voter !</button>

                        </div>

                        <div class="row">
                            <div class="col-md-3"><strong>Company Name</strong></div>
                            <div class="col-md-3"><strong>Voting No</strong></div>
                            <div class="col-md-3"><strong>Voting Time-Line</strong></div>
                            <div class="col-md-3"><strong>Total Agenda</strong></div>

                            <div class="col-md-3">
                                <p>{{ $resolution->company->name }}</p>
                            </div>
                            <div class="col-md-3">
                                <p>{{ $resolution->id }}</p>
                            </div>
                            <div class="col-md-3">
                                <p>From {{ Carbon\Carbon::parse($resolution->start_date)->format('d-M-Y g:i A') }}
                                    to {{ Carbon\Carbon::parse($resolution->end_date)->format('d-M-Y g:i A') }}</p>
                            </div>
                            <div class="col-md-3"><span
                                    id="totalVotingCount">{{ count($member->option_votes) ? count($member->option_votes) : 0 }}</span>
                                <span>/{{ $resolution->resolution_details->count() }}</span>
                            </div>
                        </div>
                        <form action="{{ route('option_vote.store') }}" method="POST" id="voting_form">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $member_id }}">
                            <div class="table-responsive">
                                <table id="company_list" class="table table-bordered yajra-datatable">
                                    <thead>
                                        <tr>
                                            <th>Item No.</th>
                                            <th>Voting Information</th>
                                            <th>The Option</th>
                                            @if ($resolution->comment_mode)
                                                <th>Comment/Remarks</th>
                                            @endif
                                            <th class="rest_section">Reset
                                                <br><button type="button" class="btn btn-sm btn-primary" id="clear-all">
                                                    <i class="glyphicon glyphicon-refresh"></i> Reset All
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($resolution->resolution_details()->orderBy('index')->get() as $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{!! nl2br(e($row->description)) !!}<br>
                                                    <a href="{{ route('memberresolutiondetails.download', Crypt::encrypt($row->id)) }}"
                                                        class="linkId">View Information</a>
                                                </td>
                                                <td class="voting_input_section">
                                                    @php
                                                        $selectedOptionValue = $row
                                                            ->option_votes()
                                                            ->where('member_id', $member->id)
                                                            ->first()
                                                            ? $row
                                                                ->option_votes()
                                                                ->where('member_id', $member->id)
                                                                ->pluck('option_id')
                                                                ->toArray()
                                                            : [];

                                                        if (count($selectedOptionValue) < 1) {
                                                            $isUpdateMode = false;
                                                        }
                                                        $voteedDetails = $row
                                                            ->option_votes()
                                                            ->where('member_id', $member->id)
                                                            ->first();
                                                    @endphp
                                                    @if ($row->option_type == 'checkbox')
                                                        <div class="checkbox-group"
                                                            id="checkbox-group-{{ $row->id }}">
                                                            <div class="checkbox-limit-info text-muted mb-2">
                                                                <strong>Note:</strong> Select between {{ $row->min }}
                                                                to {{ $row->max }}
                                                                option{{ $row->max > 1 ? 's' : '' }}.
                                                            </div>
                                                    @endif

                                                    @foreach ($row->labels as $label)
                                                        <div class="radio radio-danger block">
                                                            <input type="{{ $row->option_type }}"
                                                                class="voting_input voting_input_{{ $row->id }}"
                                                                name="resolution_choice[{{ $row->id }}]{{ $row->option_type == 'checkbox' ? '[]' : '' }}"
                                                                value="{{ $label->id }}"
                                                                {{ in_array($label->id, $selectedOptionValue) ? 'checked' : '' }}
                                                                data-min="{{ $row->min }}"
                                                                data-max="{{ $row->max }}">
                                                            <label>{{ $label->label }}</label>
                                                        </div>
                                                    @endforeach
                            </div>


                            </td>
                            @if ($resolution->comment_mode)
                                <td>
                                    <textarea class="form-control comment_section" placeholder="Write your comment..."
                                        name="instr_comment[{{ $row->id }}]" cols="10" rows="05">{{ isset($voteedDetails) ? $voteedDetails->instr_comment : '' }}</textarea>
                                </td>
                            @endif
                            <td class="rest_section">
                                <button type="button" class="btn btn-primary"
                                    onclick="resetButton({{ $row->id }})">Reset
                                </button>
                            </td>
                            </tr>
                            @endforeach
                            </tbody>
                            </table>
                            @if ($resolution->is_modifiable || !$isUpdateMode)
                                <div class="text-center" id="backdivId" style="display: none">
                                    <button type="button" id="backId" class="btn btn-primary"><i
                                            class="glyphicon glyphicon-edit"></i> Modify</button>

                                    <button type="submit" id="submitForm" class="btn btn-primary"> <i
                                            class="glyphicon glyphicon-ok"></i> Submit</button>
                                </div>
                                <div class="text-center" id="continuedivId">
                                    <button type="button" id="continueId" class="btn btn-primary"
                                        onclick="continueForModify()">
                                        Submit <i class="glyphicon glyphicon-arrow-right"></i>
                                    </button>
                                </div>
                            @endif

                    </div>
                    </form>
                </div>
            </div>
        </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
@section('footer-script')
    <script>
        let resDetailsArr = {!! json_encode($resolution->resolution_details()->pluck('option_type', 'id')) !!};

        @if ($isUpdateMode)
            $("#continuedivId ").hide();
            $(".choicenav").hide();
            $(".rest_section").hide();
            $("#backdivId").show();
            $('.comment_section').attr('readonly', true)
            $(".voting_input").each(function() {
                if (!$(this).is(":checked")) {
                    $(this).hide();
                    $(this).next().hide();
                    if ($(this).next().next().is("img")) {
                        $(this).next().next().hide();
                    }
                }

            });
        @endif
    </script>
    <script src="{{ asset('custom\member\js\option_voting.js') }}"></script>
@endsection
