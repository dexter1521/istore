@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Configuración</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Información del Negocio</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nombre_negocio" class="form-label">Nombre del Negocio *</label>
                            <input type="text" class="form-control @error('nombre_negocio') is-invalid @enderror"
                                id="nombre_negocio" name="nombre_negocio"
                                value="{{ old('nombre_negocio', $settings['nombre_negocio'] ?? '') }}" required>
                            @error('nombre_negocio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="moneda" class="form-label">Moneda *</label>
                            <select class="form-select @error('moneda') is-invalid @enderror" id="moneda" name="moneda"
                                required>
                                <option value="USD" {{ ($settings['moneda'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)
                                </option>
                                <option value="MXN" {{ ($settings['moneda'] ?? '') == 'MXN' ? 'selected' : '' }}>MXN ($)
                                </option>
                                <option value="EUR" {{ ($settings['moneda'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)
                                </option>
                            </select>
                            @error('moneda')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">WhatsApp</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="whatsapp_numero" class="form-label">Número de WhatsApp *</label>
                            <input type="text" class="form-control @error('whatsapp_numero') is-invalid @enderror"
                                id="whatsapp_numero" name="whatsapp_numero"
                                value="{{ old('whatsapp_numero', $settings['whatsapp_numero'] ?? '') }}"
                                placeholder="521234567890" required>
                            @error('whatsapp_numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Formato: código de país + número (ej: 521234567890)</small>
                        </div>

                        <div class="mb-3">
                            <label for="whatsapp_mensaje" class="form-label">Mensaje Predeterminado</label>
                            <textarea class="form-control @error('whatsapp_mensaje') is-invalid @enderror"
                                id="whatsapp_mensaje" name="whatsapp_mensaje"
                                rows="3">{{ old('whatsapp_mensaje', $settings['whatsapp_mensaje'] ?? '') }}</textarea>
                            @error('whatsapp_mensaje')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mensaje que se enviará al cliente por WhatsApp</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                </div>
            </form>
        </div>
    </div>

@endsection