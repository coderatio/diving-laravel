@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
                <form action="{{ route('links.update', $link->id) }}" method="post">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control @if($errors->first('title')) is-invalid @endif" name="title" value="{{ old('title', $link->title) }}">
                        @if ($errors->first('title'))
                            <span class="invalid-feedback">{{ $errors->first('title') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="url">Url</label>
                        <input type="text" class="form-control @if($errors->first('url')) is-invalid @endif" name="url" value="{{ old('url', $link->url) }}">
                        @if ($errors->first('url'))
                            <span class="invalid-feedback">{{ $errors->first('url') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="" cols="3" rows="3" class="form-control @if($errors->first('description')) is-invalid @endif">{{ old('description', $link->description) }}</textarea>
                        @if ($errors->first('description'))
                            <span class="invalid-feedback">{{ $errors->first('description') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                        <a href="{{ route('links.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
