@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Link</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @foreach($links as $link)
                                <tr>
                                    <td>{{ $link->id }}</td>
                                    <td>{{ $link->title }}</td>
                                    <td>{{ $link->url }}</td>
                                    <td>{{ $link->created_at->diffForHumans() }}</td>
                                    <td>
                                        <form action="{{ route('links.delete') }}" method="post">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="linkID" value="{{ $link->id }}">
                                            <a href="{{ route('links.edit', $link->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $links->links() }}
            </div>
        </div>
    </div>
@stop
