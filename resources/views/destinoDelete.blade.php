@extends('layouts.plantilla')
@section('contenido')

    <h1>Baja de una región</h1>
{{--
@if ( $cantidad > 0)
    <div class="alert text-danger bg-light p-4 col-8 mx-auto shadow">
        No se puede eliminar la región
        <span class="fs-4">{{ $region->regNombre }}</span>
        porque tiene destinos relacionados.
        <a href="/regiones" class="btn btn-outline-secondary">
            Volver a panel de regiones
        </a>
    </div>
@else--}}

    <div class="alert text-danger bg-light p-4 col-8 mx-auto shadow">
        Se eliminará el destino
        <span class="fs-4">{{ $destino->destNombre }}</span>.
        <br>
        {{ $destino->regNombre }}
        <form action="/destino/destroy" method="post">
            @csrf
            <input type="hidden" name="destNombre"
                   value="{{ $destino->destNombre }}">
            <input type="hidden" name="idDestino"
                   value="{{ $destino->idDestino }}">
            <button class="btn btn-danger btn-block my-3">Confirmar baja</button>
            <a href="/destinos" class="btn btn-outline-secondary btn-block my-2">
                volver a panel
            </a>
        </form>

    </div>



{{--@endif--}}

@endsection
