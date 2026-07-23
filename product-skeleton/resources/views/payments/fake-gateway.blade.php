<x-guest-layout>
    <h1 class="text-lg font-bold text-gray-900">{{ __('payments.fake_title') }}</h1>

    <p class="mt-2 rounded-lg bg-amber-50 p-3 text-xs leading-relaxed text-amber-800">
        {{ __('payments.fake_notice') }}
    </p>

    <dl class="mt-6 space-y-2 text-sm">
        <div class="flex justify-between">
            <dt class="text-gray-500">{{ __('shop.order_number') }}</dt>
            <dd class="font-mono">{{ $payment->order->number }}</dd>
        </div>
        <div class="flex justify-between">
            <dt class="text-gray-500">{{ __('payments.amount_due') }}</dt>
            <dd class="font-bold text-brand-700">{{ $payment->formattedAmount() }}</dd>
        </div>
    </dl>

    <form method="POST" action="{{ route('fake-gateway.pay') }}" class="mt-6">
        @csrf
        <input type="hidden" name="session" value="{{ $payment->session_id }}">
        <input type="hidden" name="success_url" value="{{ $successUrl }}">
        <input type="hidden" name="cancel_url" value="{{ $cancelUrl }}">
        <button type="submit" class="btn-primary w-full">{{ __('payments.fake_pay') }}</button>
    </form>

    <form method="POST" action="{{ route('fake-gateway.cancel') }}" class="mt-2">
        @csrf
        <input type="hidden" name="session" value="{{ $payment->session_id }}">
        <input type="hidden" name="success_url" value="{{ $successUrl }}">
        <input type="hidden" name="cancel_url" value="{{ $cancelUrl }}">
        <button type="submit" class="btn-secondary w-full">{{ __('payments.fake_cancel') }}</button>
    </form>
</x-guest-layout>
