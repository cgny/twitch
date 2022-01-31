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
                <th>#</th>
                @foreach($keys as $key)
                    <th>{{ \App\Models\AbstractModel::filterKeys($key) }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
                @foreach($dataSet as $counter => $data)
                    <tr>
                        <td>{{ ($counter+1) }}</td>
                        @foreach($keys as $key)
                            <td>{!!  $data->$key !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>#</th>
                @foreach($keys as $key)
                    <th>{{ \App\Models\AbstractModel::filterKeys($key) }}</th>
                @endforeach
            </tr>
            </tfoot>
        </table>
    </div>
</div>
