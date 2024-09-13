@extends('layouts.app')

@section('title', 'Lista de Pedidos')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Filtrar Pedidos</h2>
        <form method="GET" action="{{ route('pedidos.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <label for="cliente">Cliente:</label>
                    <input type="text" class="form-control" id="cliente" name="cliente" value="{{ request('cliente') }}">
                </div>
                <div class="col-md-3">
                    <label for="status">Status:</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="processando" {{ request('status') == 'processando' ? 'selected' : '' }}>Processando</option>
                        <option value="erro" {{ request('status') == 'erro' ? 'selected' : '' }}>Erro</option>
                        <option value="aguardando pagamento" {{ request('status') == 'aguardando_pagamento' ? 'selected' : '' }}>Aguardando Pagamento</option>
                        <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                        <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="data_inicial">Data Inicial:</label>
                    <input type="date" class="form-control" id="data_inicial" name="data_inicial" value="{{ request('data_inicial') }}">
                </div>
                <div class="col-md-2">
                    <label for="data_final">Data Final:</label>
                    <input type="date" class="form-control" id="data_final" name="data_final" value="{{ request('data_final') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h2>Lista de Pedidos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Notas</th> <!-- Coluna adicional de notas -->
                </tr>
            </thead>
            <tbody>
                @forelse ($pedidos as $pedido)
                    <tr>
                        <td>{{ $pedido->cliente->nome }}</td>
                        <td>{{ $pedido->total }}</td>
                        <td>{{ ucfirst($pedido->status) }}</td>
                        <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                        <td>{{ $pedido->nota }}</td> 
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Nenhum pedido encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $pedidos->links() }}
        </div>
    </div>
</div>
@endsection
