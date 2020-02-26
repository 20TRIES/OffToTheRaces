@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Dashboard
                    <button type="button" v-on:click="createRace" style="float: right;">Create Race</button>
                    <button type="button" v-on:click="progressTime" style="float: right;">Progress</button>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Welcome to the races!

                    <div>
                        <button v-on:click="tab = 'active'" v-bind:style="tab === 'active' ? 'background: blue;' : ''">Active Races</button>
                        <button v-on:click="tab = 'recent'" v-bind:style="tab === 'recent' ? 'background: blue;' : ''">Recent Finished Races</button>
                        <button v-on:click="tab = 'top-performances'" v-bind:style="tab === 'top-performances' ? 'background: blue;' : ''">Top Performances</button>
                    </div>

                    <div v-if="tab === 'active'">
                        <pre v-for="race in races.active">
                            @{{race}}
                        </pre>
                    </div>
                    <div v-if="tab === 'recent'">
                        <pre v-for="race in races.recent">
                            @{{race}}
                        </pre>
                    </div>
                    <div v-if="tab === 'top-performances'">
                        <pre v-for="race in races.topPerformances">
                            @{{race}}
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
