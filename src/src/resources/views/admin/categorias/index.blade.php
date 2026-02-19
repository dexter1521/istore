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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                            <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
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