@extends('app.layout.app')
@section('page_title')
    Home Page
@endsection

@section('content-body')
    <div class="row">
        @if (auth()->user()->type == '0' && isset($userCount))
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $userCount }}</h3>

                        <p>Users</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endif

        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $companyCount }}</h3>

                    <p>Company</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>

                @if (auth()->user()->type == '0')
                    <a href="{{ route('company.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                @endif
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $votingCount }}</h3>
                    <p>Votings</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="{{ route('voting.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>
    <!-- /.row -->
    <!-- Main row -->
@endsection
