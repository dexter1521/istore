@extends('layouts.admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Productos</h1>
    <div class="btn-toolbar">
        <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#importModal">
            <i class="fas fa-file-import mr-1"></i>
            Importar Excel
        </button>
        <a href="{{ route('admin.productos.template') }}" class="btn btn-sm btn-outline-secondary mr-2">
            <i class="fas fa-file-download mr-1"></i>
            Plantilla CSV
        </a>
        <a href="{{ route('admin.productos.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nuevo producto
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Listado de productos</h6>
        <span class="text-muted small">{{ $productos->total() }} registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-sm mb-0">
                <thead class="thead-light">
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
                        <td>{{ currency_symbol() }}{{ number_format($producto->precio, 2) }}</td>
                        <td>{{ $producto->categoria ? $producto->categoria->nombre : '-' }}</td>
                        <td>
                            @if($producto->activo)
                            <span class="badge badge-success">Si</span>
                            @else
                            <span class="badge badge-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.productos.edit', $producto) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form action="{{ route('admin.productos.destroy', $producto) }}" method="POST" class="d-inline" data-confirm="Estas seguro?">
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
        </div>
    </div>
</div>

<div class="d-flex justify-content-end">
    {{ $productos->links() }}
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importar productos desde Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.productos.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Archivo Excel</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Formatos aceptados: XLSX, XLS, CSV</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>Formato esperado:</strong><br>
                        Columnas: <code>sku</code>, <code>nombre</code>, <code>descripcion</code>, <code>precio</code>,
                        <code>categoria</code>, <code>activo</code>
                    </div>
                    <div class="small text-muted">Tip: usa la plantilla para evitar errores de formato.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
