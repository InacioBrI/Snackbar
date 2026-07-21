@extends('layouts.admin')

@section('title', 'Configurações')
@section('heading', 'Configurações da loja')

@section('content')
    <form action="{{ route('admin.configuracoes.update') }}" method="POST" enctype="multipart/form-data" class="grid max-w-4xl gap-5 lg:grid-cols-3">
        @csrf @method('PUT')

        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 lg:col-span-2">
            <h2 class="font-bold text-stone-900">Informações gerais</h2>
            <div>
                <label for="name" class="text-sm font-medium text-stone-700">Nome da lanchonete *</label>
                <input id="name" name="name" value="{{ old('name', $storeSettings['name']) }}" required
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="about" class="text-sm font-medium text-stone-700">Sobre</label>
                <textarea id="about" name="about" rows="3"
                          class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('about', $storeSettings['about']) }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="phone" class="text-sm font-medium text-stone-700">Telefone</label>
                    <input id="phone" name="phone" value="{{ old('phone', $storeSettings['phone']) }}"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label for="hours" class="text-sm font-medium text-stone-700">Horário de funcionamento</label>
                    <input id="hours" name="hours" value="{{ old('hours', $storeSettings['hours']) }}"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="text-sm font-medium text-stone-700">Endereço / Localização</label>
                    <input id="address" name="address" value="{{ old('address', $storeSettings['address']) }}"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <h2 class="border-t border-stone-100 pt-4 font-bold text-stone-900">Pedidos e pagamentos</h2>
            <div>
                <label for="service_fee_percent" class="text-sm font-medium text-stone-700">Taxa de serviço (%)</label>
                <input id="service_fee_percent" name="service_fee_percent" type="number" step="0.01" min="0" max="100"
                       value="{{ old('service_fee_percent', $storeSettings['service_fee_percent']) }}"
                       class="mt-1 w-32 rounded-xl border border-stone-300 px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <p class="text-sm font-medium text-stone-700">Formas de pagamento aceitas *</p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach(['pix' => 'PIX', 'credit' => 'Crédito', 'debit' => 'Débito'] as $key => $label)
                        <label class="flex items-center gap-2 rounded-xl border border-stone-200 px-4 py-2 text-sm">
                            <input type="checkbox" name="payment_methods[]" value="{{ $key }}"
                                   @checked(in_array($key, old('payment_methods', $storeSettings['payment_methods'])))
                                   class="h-4 w-4 rounded border-stone-300 text-amber-500 focus:ring-amber-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-stone-200 bg-white p-6">
                <p class="text-sm font-semibold text-stone-900">Logo</p>
                @if($storeSettings['logo'])
                    <img src="{{ asset('storage/'.$storeSettings['logo']) }}" class="mt-3 h-24 w-24 rounded-xl object-cover" alt="Logo">
                @endif
                <input type="file" name="logo" accept="image/*" class="mt-3 w-full text-sm text-stone-600 file:mr-3 file:rounded-full file:border-0 file:bg-amber-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-700 hover:file:bg-amber-100">
                <p class="mt-2 text-xs text-stone-400">PNG/JPG até 2MB.</p>
            </div>
            <button class="w-full rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">Salvar configurações</button>
        </div>
    </form>
@endsection
