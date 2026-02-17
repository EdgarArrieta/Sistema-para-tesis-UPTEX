@extends('layouts.app')

@section('title', 'Ficha Técnica #' . $ticket->id_ticket)

@section('content')
<style>
    .figma-bg { background-color: #f8f9fa; padding: 30px; font-family: 'Inter', sans-serif; }
    .ticket-box { 
        background: #D9D9D9; /* El gris de tu Figma */
        border: 2px solid #000; 
        border-radius: 40px; 
        padding: 40px; 
        margin: 0 auto;
        max-width: 950px;
    }
    .label-text { font-weight: 800; color: #000; text-transform: uppercase; margin-right: 10px; }
    .white-pill { 
        background: white; 
        border: 1px solid #000; 
        border-radius: 25px; 
        padding: 6px 20px; 
        display: inline-block; 
        min-width: 180px;
        font-weight: 600;
    }
    .badge-large {
        border-radius: 20px; 
        padding: 10px 30px; 
        color: white; 
        font-weight: 900; 
        border: 2px solid #000;
        display: inline-block;
    }
    .desc-box { 
        background: white; 
        border: 2px solid #000; 
        border-radius: 25px; 
        padding: 20px; 
        margin-top: 25px;
        min-height: 100px;
    }
    .comments-area { margin-top: 30px; border-top: 2px dashed #000; padding-top: 20px; }
    .bubble { background: white; border: 1px solid #000; border-radius: 15px; padding: 12px; margin-bottom: 10px; }
    @media print { .no-print { display: none; } }
</style>

<div class="figma-bg">
    <div class="d-flex justify-content-between mb-4 no-print">
        <h3>UPTEX <span class="text-primary">TICKETS</span></h3>
        <div class="white-pill">{{ session('usuario_nombre') }} (Técnico)</div>
    </div>

    <div class="ticket-box shadow">
        <div class="row">
            <div class="col-md-7">
                <div class="mb-3"><span class="label-text">CREADO POR:</span> <div class="white-pill">{{ $ticket->usuario->nombre_completo }}</div></div>
                <div class="mb-3"><span class="label-text">MATRICULA:</span> <div class="white-pill">{{ $ticket->usuario->matricula ?? 'N/A' }}</div></div>
                <div class="mb-3"><span class="label-text">AREA:</span> <div class="white-pill">{{ $ticket->area->nombre }}</div></div>
            </div>
            <div class="col-md-5 text-end">
                <div class="mb-3">
                    <span class="label-text d-block">PRIORIDAD:</span>
                    <div class="badge-large" style="background: {{ $ticket->prioridad->nivel >= 3 ? 'red' : 'blue' }}">{{ $ticket->prioridad->nombre }}</div>
                </div>
                <div>
                    <span class="label-text d-block">ESTADO:</span>
                    <div class="badge-large" style="background: #00FF00; color: black;">{{ $ticket->estado->nombre }}</div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h5 class="text-center label-text">DESCRIPCIÓN:</h5>
            <div class="desc-box">{{ $ticket->descripcion }}</div>
        </div>

        <div class="comments-area">
            <h5 class="label-text mb-3">Historial de Comentarios:</h5>
            @forelse($ticket->comentarios as $com)
                <div class="bubble">
                    <strong>{{ $com->usuario->nombre_completo }}</strong> <small class="text-muted">({{ $com->created_at->diffForHumans() }})</small><br>
                    {{ $com->contenido }}
                </div>
            @empty
                <p class="text-center">No hay comentarios aún.</p>
            @endforelse
        </div>
    </div>

    <div class="text-center mt-5 no-print">
        <button onclick="window.print()" class="btn btn-dark px-5">Imprimir Ficha</button>
        <a href="{{ route('tickets.asignados') }}" class="btn btn-secondary px-5">Volver</a>
    </div>
</div>
@endsection