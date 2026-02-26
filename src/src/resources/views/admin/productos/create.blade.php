@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Crear Producto</h1>
</div>

<form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" class="form-control" id="sku" name="sku" required>
    </div>
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>
    <div class="mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
    </div>
    <div class="mb-3">
        <label for="categoria_id" class="form-label">Categoría</label>
        <select class="form-select" id="categoria_id" name="categoria_id" required>
            @foreach($categorias as $categoria)
            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label for="imagenes" class="form-label">Imágenes</label>
        <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*">
        <small class="text-muted">Puedes seleccionar múltiples imágenes (JPEG, PNG, WebP, máx. 2MB c/u)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" checked>
        <label class="form-check-label" for="activo">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
