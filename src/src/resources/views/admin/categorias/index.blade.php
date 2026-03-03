@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Categorías</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.categorias.create') }}" class="btn btn-sm btn-primary">
            + Nueva Categoría
        </a>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Productos</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categorias as $categoria)
            <tr>
                <td>{{ $categoria->id }}</td>
                <td>{{ $categoria->nombre }}</td>
                <td><code>{{ $categoria->slug }}</code></td>
                <td>{{ $categoria->productos_count }}</td>
                <td>{{ $categoria->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('admin.categorias.edit', $categoria) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" class="d-inline" data-confirm="Estas seguro de eliminar esta categoria?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No hay categorías registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $categorias->links() }}

@endsection
