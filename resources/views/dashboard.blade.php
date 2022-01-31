<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {!! session('info') !!}

                    <div class="card">
                        <div class="col-form-label-lg">
                            Average Total Number of Viewers {{ $total_avg_viewers }}
                        </div>
                        <div class="card-body">

                        </div>
                    </div>

                    <!-- Top 1000 Streams -->
                    @include('components.table',['title' => 'Top 1000  Streams', 'keys' => $top_1000_keys, 'dataSet' => $top_1000_data, 'chart_id' => 'top_1000'])

                    @include('components.table',['title' => 'Top 100  Streams', 'keys' => $top_100_keys, 'dataSet' => $top_100_data, 'chart_id' => 'top_100'])

                    @include('components.table',['title' => 'Streams By Start Hour', 'keys' => $streams_start_keys, 'dataSet' => $streams_start_data, 'chart_id' => 'streams_by_hr'])




                        <div class="col-12 card">
                            <div class="col-form-label-lg">
                                Follow Streams with Tags
                            </div>
                            <div class="card-body">
                                <table id="followed_tags" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Viewers</th>
                                        <th>Needed for 1000</th>
                                        <th>Tags</th>
                                        <th>Shared Tags</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($user_follow_data as $user_follow_datum)
                                        <tr>
                                            <td>{{ $user_follow_datum['title'] }} </td>
                                            <td>{{ $user_follow_datum['viewers'] }} </td>
                                            <td>{{ $user_follow_datum['needed_1000'] }} </td>
                                            <td>

                                               @foreach($user_follow_datum['tags'] as $tags)

                                                  @foreach($tags as $tag)
                                                        {{ $tag->localization_descriptions->{'en-us'} }}<br>
                                                    @endforeach
                                               @endforeach
                                            </td>
                                            <td>

                                               @foreach($user_follow_datum['shared_tags'] as $tag)

                                                    {{ $tag->localization_descriptions->{'en-us'} }}<br>
                                               @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Title</th>
                                        <th>Viewers</th>
                                        <th>Needed for 1000</th>
                                        <th>Tags</th>
                                        <th>Shared Tags</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#top_1000, #top_100').DataTable( );
                $('#streams_by_hr').DataTable();
            } );
        </script>

    @endpush
</x-app-layout>
