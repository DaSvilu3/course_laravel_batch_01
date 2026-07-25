{{-- Renders any of our status enums: <x-status-badge :status="$order->status" /> --}}
@props(['status'])

<span class="badge {{ $status->color() }}">{{ $status->label() }}</span>
