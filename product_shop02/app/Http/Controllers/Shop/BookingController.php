<?php

namespace App\Http\Controllers\Shop;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        return view('shop.bookings.index', [
            'bookings' => $request->user()
                ->bookings()
                ->with('service')
                ->orderByRaw('starts_at is null desc')
                ->orderBy('starts_at')
                ->paginate(10),
        ]);
    }
    public function analysis(Request $request): View
    {
        
    }

    /** Pick (or change) the appointment slot for a paid service. */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);

        $data = $request->validate([
            'starts_at' => ['required', 'date', 'after:now'],
        ]);

        $startsAt = Carbon::parse($data['starts_at']);
        $duration = $booking->service?->duration_minutes;

        $booking->update([
            'starts_at' => $startsAt,
            'ends_at' => $duration ? $startsAt->copy()->addMinutes($duration) : null,
            'status' => BookingStatus::Pending,
        ]);

        return back()->with('status', __('shop.booking_updated'));
    }
}
