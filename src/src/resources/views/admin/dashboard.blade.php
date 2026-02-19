@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Pedidos Nuevos</div>
            <div class="card-body">
                <h5 class="card-title">0</h5>
                <p class="card-text">Pedidos pendientes de procesar.</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Productos Activos</div>
            <div class="card-body">
                <h5 class="card-title">0</h5>
                <p class="card-text">Total de productos en catálogo.</p>
            </div>
        </div>
    </div>
</div>
@endsection
