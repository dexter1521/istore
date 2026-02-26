@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Producto</h1>
</div>

<form action="{{ route('admin.productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" class="form-control" id="sku" value="{{ $producto->sku }}" readonly disabled>
    </div>
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}"
            required>
    </div>
    <div class="mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input type="number" class="form-control" id="precio" name="precio" step="0.01"
            value="{{ old('precio', $producto->precio) }}" required>
    </div>
    <div class="mb-3">
        <label for="categoria_id" class="form-label">Categorí­a</label>
        <select class="form-select" id="categoria_id" name="categoria_id" required>
            @foreach($categorias as $categoria)
            <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                {{ $categoria->nombre }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion"
            rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
    </div>

    @if($producto->imagenes->count() > 0)
    <div class="mb-3">
        <label class="form-label">Imágenes Actuales</label>
        <div class="row">
            @foreach($producto->imagenes as $imagen)
            <div class="col-md-3 mb-2">
                <img src="{{ route('media.show', ['path' => $imagen->path]) }}" class="img-thumbnail" alt="Imagen producto">
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="mb-3">
        <label for="imagenes" class="form-label">Agregar Nuevas Imágenes</label>
        <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*">
        <small class="text-muted">Puedes seleccionar múltiples imágenes (JPEG, PNG, WebP, máximo 2MB c/u)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{ $producto->activo ? 'checked' : '' }}>
        <label class="form-check-label" for="activo">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
