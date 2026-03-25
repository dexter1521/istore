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
        <span class="text-muted small">Usa búsqueda y filtros de la tabla</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="productosTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Precio base</th>
                        <th>Unidad</th>
                        <th>Categoría</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
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
                        Columnas: <code>sku</code>, <code>nombre</code>, <code>descripcion</code>, <code>precio1</code>,
                        <code>precio2</code>, <code>precio3</code>, <code>precio4</code>, <code>precio5</code>,
                        <code>cantidad2</code>, <code>cantidad3</code>, <code>cantidad4</code>, <code>cantidad5</code>,
                        <code>unidad_medida</code>, <code>categoria</code>, <code>activo</code>
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


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $('#productosTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.productos.data') }}'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'sku', name: 'sku' },
                { data: 'nombre', name: 'nombre' },
                {
                    data: 'precio',
                    name: 'precio',
                    render: function(data) {
                        const val = parseFloat(data || 0).toFixed(2);
                        return '{{ currency_symbol() }}' + val;
                    }
                },
                {
                    data: 'unidad',
                    name: 'unidad',
                    render: function(data) {
                        return data ? data : '-';
                    }
                },
                { data: 'categoria', name: 'categoria' },
                {
                    data: 'activo',
                    name: 'activo',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data ? '<span class="badge badge-success">Si</span>' : '<span class="badge badge-secondary">No</span>';
                    }
                },
                {
                    data: 'id',
                    name: 'acciones',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        const editUrl = '{{ url('admin/productos') }}/' + data + '/edit';
                        const deleteUrl = '{{ url('admin/productos') }}/' + data;
                        return `
                            <a href="${editUrl}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form action="${deleteUrl}" method="POST" class="d-inline" data-confirm="Estas seguro?">
                                <input type="hidden" name="_token" value="${csrf}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
            }
        });
    });
</script>
@endpush
