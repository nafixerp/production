@extends('layouts.app')
@section('title', $title . ' Detail')
@section('page-title', $title . ' Detail')
@section('content')
<div class="card-gold" style="max-width:700px">
    <div class="card-header-gold">
        <h5>◇ {{ $title }} — #{{ $row->id ?? '' }}</h5>
        <div class="d-flex gap-2">
            <a href="{{ route($slug.'.edit', $row->id) }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-pencil"></i> Edit</a>
            <a href="{{ route($slug.'.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>
    @if($row)
    <table class="table-gold w-100">
        @foreach((array)$row as $key => $val)
        <tr>
            <td style="width:35%;color:rgba(212,175,55,0.7);font-size:0.78rem;text-transform:uppercase;letter-spacing:1px">{{ str_replace('_',' ',$key) }}</td>
            <td>{{ $val ?? '—' }}</td>
        </tr>
        @endforeach
    </table>
    @endif
</div>
@endsection
