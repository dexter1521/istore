@extends('layouts.admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Crear producto</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Información del producto</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" class="form-control" id="sku" name="sku" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="precio1">Precio base (precio 1)</label>
                <input type="number" class="form-control" id="precio1" name="precio1" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="categoria_id">Categoría</label>
                <select class="form-control" id="categoria_id" name="categoria_id" required>
                    @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="unidad_medida">Unidad de medida</label>
                <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" maxlength="50" placeholder="Ej. pieza, kg, caja">
            </div>

            <div class="form-group">
                <label>Niveles de precio</label>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Desde cantidad</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" class="form-control" name="cantidad2" min="1" placeholder="Cantidad 2"></td>
                                <td><input type="number" class="form-control" name="precio2" step="0.01" placeholder="Precio 2"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="cantidad3" min="1" placeholder="Cantidad 3"></td>
                                <td><input type="number" class="form-control" name="precio3" step="0.01" placeholder="Precio 3"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="cantidad4" min="1" placeholder="Cantidad 4"></td>
                                <td><input type="number" class="form-control" name="precio4" step="0.01" placeholder="Precio 4"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="cantidad5" min="1" placeholder="Cantidad 5"></td>
                                <td><input type="number" class="form-control" name="precio5" step="0.01" placeholder="Precio 5"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Las cantidades deben ser ascendentes. Si defines una cantidad, debes definir su precio.</small>
            </div>
            <div class="form-group">
                <label for="imagenes">Imágenes</label>
                <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*">
                <small class="text-muted">Puedes seleccionar múltiples imágenes (JPEG, PNG, WebP, máx. 2MB c/u)</small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" checked>
                <label class="form-check-label" for="activo">Activo</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
