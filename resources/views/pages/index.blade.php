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
                                <form method="post" action="{{ route('dashboard', $base->id) }}">
                                    @csrf
{{--                                    <input type="hidden" id="termial_id" name="id" value={{ $base->id }}>--}}
                                    <input type="hidden" id="user_name" name="user_name" value="demo@ontoto.com">
                                    <input type="hidden" id="api_token" name="api_key"
                                           value="s4B96hTKaZ53nvQ3OS8xz5sGU2wjSynh6twikj30QuQs0RbYgOHcNTrcjFz0">
                                    <input type="hidden" id="site_id" name="site_id" value="27">
                                    <button type="submit" class="btn btn-sm btn-info"> Dashboard</button>
                                </form>
                                {{--                                <a href="{{ route('dashboard', ['id' => $base->id, 'api_key' => \Illuminate\Support\Facades\Crypt::encrypt('s4B96hTKaZ53nvQ3OS8xz5sGU2wjSynh6twikj30QuQs0RbYgOHcNTrcjFz0')]) }}"--}}
                                {{--                                   class="btn btn-sm btn-info">Dashboard</a>--}}
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
