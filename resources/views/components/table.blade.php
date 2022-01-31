@php

    $t = new App\Models\Twitch();

@endphp


<div class="col-12 card">
    <div class="col-form-label-lg">
        {{ $title }}
    </div>
    <div class="card-body">
        <table id="{{ $chart_id ?? "table" }}" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                @foreach($keys as $key)
                    <th>{{ \App\Models\AbstractModel::filterKeys($key) }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
                @foreach($dataSet as $data)
                    <tr>
                        @foreach($keys as $key)
                            <td>{!!  $data->$key !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
            <tr>
                @foreach($keys as $key)
                    <th>{{ \App\Models\AbstractModel::filterKeys($key) }}</th>
                @endforeach
            </tr>
            </tfoot>
        </table>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.bootstrap.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/datatables.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('js/datatables.min.js') }}" ></script>
    <script src="{{ asset('js/jquery.js') }}" ></script>
@endpush
