@extends('app')
@section('title', 'Status')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{$header}}</div>

                <div class="card-body text-center">
                    <h5 class="text-success">{{$message}}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection