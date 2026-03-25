@extends('layouts.admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Usuarios</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus mr-1"></i>
        Nuevo usuario
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-1">Rol</label>
                <select class="form-control" id="roleFilter">
                    <option value="">Todos</option>
                    @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ ($role === $r->name) ? 'selected' : '' }}>
                        {{ $r->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="button" id="applyFilter" class="btn btn-primary">Filtrar</button>
                <button type="button" id="clearFilter" class="btn btn-outline-secondary">Limpiar</button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Listado de usuarios</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="usersTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha</th>
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
        const table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.users.data') }}',
                data: function(d) {
                    d.role = $('#roleFilter').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombre', name: 'nombre' },
                { data: 'email', name: 'email' },
                { data: 'rol', name: 'rol', orderable: false },
                { data: 'fecha', name: 'fecha' },
                {
                    data: 'id',
                    name: 'acciones',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        const editUrl = '{{ url('admin/users') }}/' + data + '/edit';
                        const deleteUrl = '{{ url('admin/users') }}/' + data;
                        const resetUrl = '{{ url('admin/users') }}/' + data + '/reset-password';
                        return `
                            <a href="${editUrl}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form action="${resetUrl}" method="POST" class="d-inline" data-confirm="Resetear password y generar uno temporal?">
                                <input type="hidden" name="_token" value="${csrf}">
                                <button type="submit" class="btn btn-sm btn-outline-warning">Reset</button>
                            </form>
                            <form action="${deleteUrl}" method="POST" class="d-inline" data-confirm="Eliminar este usuario?">
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

        $('#applyFilter').on('click', function() {
            table.ajax.reload();
        });

        $('#clearFilter').on('click', function() {
            $('#roleFilter').val('');
            table.ajax.reload();
        });
    });
</script>
@endpush
