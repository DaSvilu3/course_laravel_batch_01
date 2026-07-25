<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with('service', 'user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByRaw('starts_at is null desc')
            ->orderBy('starts_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', ['bookings' => $bookings]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_column(BookingStatus::cases(), 'value'))],
            'starts_at' => ['nullable', 'date'],
        ]);

        $booking->update(array_filter($data, fn ($v) => $v !== null));

        return back()->with('status', __('admin.saved'));
    }
}
