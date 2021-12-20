@extends('layouts.app')
@section('content')
    @include('partials.alert')
    <div class="card">
        <div class="card-header">
            <h4>Base Stations</h4>
        </div>
        <div class="card-body">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Tags</th>
                    <th scope="col">Name</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($bases as $key => $base)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $base->id.':'.$base->tag }}</td>
                        <td>{{ $base->name }}</td>
                        <td>
                            @if(in_array($base->id, config('basestations.allow')))
                                <a href="{{ route('dashboard', [$base->id, $base->tag]) }}" class="btn btn-sm btn-info">Dashboard</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $bases->links() }}
        </div>
    </div>
@endsection