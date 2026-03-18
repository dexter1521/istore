@extends('layouts.admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Categorías</h1>
    <a href="{{ route('admin.categorias.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus mr-1"></i>
        Nueva categoría
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Listado de categorías</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Productos</th>
                        <th>Fecha creación</th>
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
                            <a href="{{ route('admin.categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" class="d-inline" data-confirm="Estas seguro de eliminar esta categoria?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
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
    </div>
</div>

<div class="d-flex justify-content-end">
    {{ $categorias->links() }}
</div>
@endsection
