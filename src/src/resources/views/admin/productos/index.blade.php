@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Productos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <span data-feather="upload"></span>
            Importar Excel
        </button>
        <a href="{{ route('admin.productos.template') }}" class="btn btn-sm btn-outline-secondary me-2">
            <span data-feather="download"></span>
            Descargar Plantilla CSV
        </a>
        <a href="{{ route('admin.productos.create') }}" class="btn btn-sm btn-outline-primary">
            <span data-feather="plus"></span>
            Nuevo Producto
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importar Productos desde Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.productos.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Archivo Excel</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Formatos aceptados: XLSX, XLS, CSV</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>Formato esperado:</strong><br>
                        Columnas: <code>sku</code>, <code>nombre</code>, <code>descripcion</code>, <code>precio</code>,
                        <code>categoria</code>, <code>activo</code>
                    </div>
                    <div class="small text-muted">
                        Tip: usa la plantilla para evitar errores de formato.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">SKU</th>
                <th scope="col">Nombre</th>
                <th scope="col">Precio</th>
                <th scope="col">Categoría</th>
                <th scope="col">Activo</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $producto)
            <tr>
                <td>{{ $producto->id }}</td>
                <td>{{ $producto->sku }}</td>
                <td>{{ $producto->nombre }}</td>
                <td>${{ number_format($producto->precio, 2) }}</td>
                <td>{{ $producto->categoria ? $producto->categoria->nombre : '-' }}</td>
                <td>
                    @if($producto->activo)
                    <span class="badge bg-success">Si</span>
                    @else
                    <span class="badge bg-secondary">No</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.productos.edit', $producto) }}"
                        class="btn btn-sm btn-outline-secondary">Editar</a>
                    <form action="{{ route('admin.productos.destroy', $producto) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Estás seguro?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No hay productos registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $productos->links() }}
</div>
@endsection
