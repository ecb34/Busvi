@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h3>{{ $post->title }}</h3>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body" style="padding: 20px 20px 15px 20px;">
                {!! $post->body !!}
            </div>
        </div>
    </div>
@endsection