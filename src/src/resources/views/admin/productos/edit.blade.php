@extends('layouts.admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Editar producto</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Información del producto</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" class="form-control" id="sku" value="{{ $producto->sku }}" readonly disabled>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
            </div>
            <div class="form-group">
                <label for="precio1">Precio base (precio 1)</label>
                <input type="number" class="form-control" id="precio1" name="precio1" step="0.01" value="{{ old('precio1', $producto->precio1 ?? $producto->precio) }}" required>
            </div>
            <div class="form-group">
                <label for="categoria_id">Categoría</label>
                <select class="form-control" id="categoria_id" name="categoria_id" required>
                    @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>
            <div class="form-group">
                <label for="unidad_medida">Unidad de medida</label>
                <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" maxlength="50" value="{{ old('unidad_medida', $producto->unidad_medida) }}" placeholder="Ej. pieza, kg, caja">
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
                                <td><input type="number" class="form-control" name="cantidad2" min="1" value="{{ old('cantidad2', $producto->cantidad2) }}"></td>
                                <td><input type="number" class="form-control" name="precio2" step="0.01" value="{{ old('precio2', $producto->precio2) }}"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="cantidad3" min="1" value="{{ old('cantidad3', $producto->cantidad3) }}"></td>
                                <td><input type="number" class="form-control" name="precio3" step="0.01" value="{{ old('precio3', $producto->precio3) }}"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="cantidad4" min="1" value="{{ old('cantidad4', $producto->cantidad4) }}"></td>
                                <td><input type="number" class="form-control" name="precio4" step="0.01" value="{{ old('precio4', $producto->precio4) }}"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="cantidad5" min="1" value="{{ old('cantidad5', $producto->cantidad5) }}"></td>
                                <td><input type="number" class="form-control" name="precio5" step="0.01" value="{{ old('precio5', $producto->precio5) }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Las cantidades deben ser ascendentes. Si defines una cantidad, debes definir su precio.</small>
            </div>

            @if($producto->imagenes->count() > 0)
            <div class="form-group">
                <label>Imágenes actuales</label>
                <div class="row">
                    @foreach($producto->imagenes as $imagen)
                    <div class="col-md-3 mb-2 text-center">
                        <img src="{{ route('media.show', ['path' => $imagen->path]) }}" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;" alt="Imagen producto">
                        <form action="{{ route('admin.productos.imagenes.destroy', [$producto, $imagen]) }}" method="POST" class="mt-2" data-confirm="Eliminar esta imagen?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="form-group">
                <label for="imagenes">Agregar nuevas imágenes</label>
                <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*">
                <small class="text-muted">Puedes seleccionar múltiples imágenes (JPEG, PNG, WebP, máximo 2MB c/u)</small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{ $producto->activo ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Activo</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
