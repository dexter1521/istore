@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Categoría</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('admin.categorias.update', $categoria) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre"
                        value="{{ old('nombre', $categoria->nombre) }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion"
                        name="descripcion" rows="3">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Opcional</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Información</label>
                    <p class="text-muted mb-0">
                        <strong>Slug:</strong> <code>{{ $categoria->slug }}</code><br>
                        <strong>Productos asociados:</strong> {{ $categoria->productos()->count() }}
                    </p>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

@endsection