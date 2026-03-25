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
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="categoriasTable" width="100%">
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
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $('#categoriasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.categorias.data') }}'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                {
                    data: 'slug',
                    name: 'slug',
                    orderable: false,
                    render: function(data) {
                        return '<code>' + data + '</code>';
                    }
                },
                { data: 'productos', name: 'productos' },
                { data: 'fecha', name: 'fecha' },
                {
                    data: 'id',
                    name: 'acciones',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        const editUrl = '{{ url('admin/categorias') }}/' + data + '/edit';
                        const deleteUrl = '{{ url('admin/categorias') }}/' + data;
                        return `
                            <a href="${editUrl}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form action="${deleteUrl}" method="POST" class="d-inline" data-confirm="Estas seguro de eliminar esta categoria?">
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
