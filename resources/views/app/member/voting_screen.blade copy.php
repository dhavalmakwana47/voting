@extends('app.member.layout')
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
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
                                    id="totalVotingCount">{{ isset($voteArr) ? count($voteArr) : 0 }}</span>
                                <span>/{{ $resolution->resolution_details->count() }}</span>
                            </div>
                        </div>
                        <form action="{{ route('vote.store') }}" method="POST" id="voting_form">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $member_id }}">
                            <div class="table-responsive">
                                <table id="company_list" class="table table-bordered yajra-datatable">
                                    <thead>
                                        <tr>
                                            <th>Item No.</th>
                                            <th>Voting Information</th>
                                            <th>The Option
                                                <div class="choicenav"
                                                    @if($vote_count) style="display: none" @endif>
                                                    <div class="radio radio-danger selectAllYes" id="slectAllYesId">
                                                        <input type="radio" name="radiogroupAll" id="radio1"
                                                            value="YES" onclick="totalVotingCountallYesOrNo('yes')">
                                                        <label for="radio1">
                                                            All Agree (Yes)
                                                        </label>
                                                    </div>
                                                    <div class="radio radio-danger selectAllNo" id="slectAllNoId">
                                                        <input type="radio" name="radiogroupAll" id="radio2"
                                                            value="NO" onclick="totalVotingCountallYesOrNo('no')">
                                                        <label for="radio2">
                                                            All disagree (No)
                                                        </label>
                                                    </div>
                                                    <div class="radio radio-danger selectabstain" id="slectAllAbstainId">
                                                        <input type="radio" name="radiogroupAll" id="radio3"
                                                            value="ABSTAIN" onclick="totalVotingCountallYesOrNo('abstain')">
                                                        <label for="radio3">
                                                            All Abstain
                                                        </label>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="rest_section"
                                                @if($vote_count) style="display: none" @endif>Reset
                                                <br><button type="button" class="btn btn-sm btn-primary" id="clear-all">
                                                    <i class="glyphicon glyphicon-refresh"></i> Reset All
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($resolution->resolution_details()->orderBy('index')->get() as $row)
                                            @php
                                                $vote = $row->votes->where('member_id', $member_id)->first();
                                            @endphp
                                            @if (isset($vote))

                                                <input type="hidden" name="vote_id[]" value="{{ $vote->id }}">
                                            @else
                                                <input type="hidden" name="vote_id[]" value="0">
                                            @endisset

                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{!! nl2br(e($row->description)) !!} <br>
                                                    <a href="{{ route('memberresolutiondetails.download', Crypt::encrypt($row->id)) }}"
                                                        class="linkId">View Information</a>
                                                </td>
                                                <td class="voting_input_section">
                                                    <input type="hidden" class="evsnidischecked" name="evsnidischecked"
                                                        id="evsnischecked_{{ $row->id }}"
                                                        @if (isset($vote)) value="Y" @else value="N" @endif>

                                                    <div class="choiceque" id="choiceQueId">

                                                        <div class="radio radio-danger block"
                                                            id="radioYesdiv_{{ $row->id }}">
                                                            <input type="radio"
                                                                class="voting_input selectyes  resolution_choice{{ $row->id }}"
                                                                name="resolution_choice[{{ $row->id }}]"
                                                                id="radioYes_{{ $row->id }}" value="YES"
                                                                onclick="selectAllYesNo({{ $row->id }})"
                                                                {{ isset($vote) && $vote->resolution_choice == 'YES' ? 'checked' : '' }}>
                                                            <label for="radioYes_{{ $row->id }}">
                                                                I agree to the Agenda (Yes)
                                                            </label>
                                                        </div>


                                                        <div class="radio radio-danger block"
                                                            id="radioNodiv_{{ $row->id }}">
                                                            <input type="radio"
                                                                class="voting_input selectno  resolution_choice{{ $row->id }}"
                                                                name="resolution_choice[{{ $row->id }}]"
                                                                id="radioNo_{{ $row->id }}" value="NO"
                                                                onclick="selectAllYesNo({{ $row->id }})"
                                                                {{ isset($vote) && $vote->resolution_choice == 'No' ? 'checked' : '' }}>
                                                            <label for="radioNo_{{ $row->id }}">
                                                                I disagree to the Agenda (No)
                                                            </label>
                                                        </div>


                                                        <!-- Add by Keyur
                                                                                    date:15-04-2019
                                                                                    #81 They should also be given option else than yes, no and Abstain for voting -->&nbsp;
                                                        <div class="radio radio-danger block"
                                                            id="radioAbstaindiv_{{ $row->id }}">
                                                            <input type="radio"
                                                                class="voting_input selectabstain resolution_choice{{ $row->id }}"
                                                                name="resolution_choice[{{ $row->id }}]"
                                                                id="radioAbstain_{{ $row->id }}" value="ABSTAIN"
                                                                onclick="selectAllYesNo({{ $row->id }})"
                                                                {{ isset($vote) && $vote->resolution_choice == 'ABSTAIN' ? 'checked' : '' }}>
                                                            <label for="radioAbstain_{{ $row->id }}">
                                                                I Abstain to the Agenda
                                                            </label>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="rest_section"
                                                    @if ($vote_count) style="display: none" @endif>
                                                    <button type="button" class="btn btn-primary"
                                                        id="reset_{{ $row->id }}"
                                                        onclick="resetButton({{ $row->id }})">Reset
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                </tbody>
                            </table>
                            @if ($resolution->is_modifiable || !$vote_count)
                            <div class="text-center" id="backdivId"
                                @if (!$vote_count) style="display: none" @endif>
                                <button type="button" id="backId" class="btn btn-primary"><i
                                        class="glyphicon glyphicon-edit"></i> Modify</button>

                                <button type="submit" id="submitForm" class="btn btn-primary"> <i
                                        class="glyphicon glyphicon-ok"></i> Submit</button>
                            </div>
                            <div class="text-center" id="continuedivId"
                                @if ($vote_count) style="display: none" @endif>
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
<script src="{{ asset('custom\member\js\voting_screen.js') }}"></script>
@if($vote_count)
    <script>
        $(".voting_input").each(function() {
            if (!$(this).is(":checked")) {
                $(this).hide();
                $(this).next().hide();
            }
        });
    </script>
@endif
@endsection
