<?php

namespace App\Http\Controllers;

use App\Models\ContactQuery;
use App\Models\EventEnquiry;
use App\Models\HotelSetting;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminOperationsController extends Controller
{
    public function users(): View
    {
        return view('admin.operations.users', [
            'users' => User::query()->where('role', 'user')->latest()->paginate(20),
        ]);
    }

    public function managers(): View
    {
        return view('admin.operations.managers', [
            'managers' => User::query()->where('role', 'manager')->latest()->paginate(20),
        ]);
    }

    public function administrators(): View
    {
        return view('admin.operations.administrators', [
            'administrators' => User::query()->where('role', 'admin')->latest()->paginate(20),
        ]);
    }

    public function storeAdministrator(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $administrator = new User;
        $administrator->name = $data['name'];
        $administrator->email = $data['email'];
        $administrator->phone = $data['phone'] ?? null;
        $administrator->password = Str::random(64);
        $administrator->role = 'admin';
        $administrator->save();

        $status = Password::sendResetLink(['email' => $administrator->email]);

        return back()->with('status', $status === Password::RESET_LINK_SENT
            ? 'Administrator created. A password setup link was sent.'
            : 'Administrator created. Ask them to use Forgot password to set their password.');
    }

    public function updateAdministrator(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'admin', 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->fill($data)->save();

        return back()->with('status', 'Administrator details updated.');
    }

    public function administratorStatus(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'admin', 404);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $isActive = (bool) $data['is_active'];

        if (! $isActive && $user->is_active) {
            if ($user->id === $request->user()->id) {
                return back()->withErrors(['administrator' => 'You cannot deactivate your own administrator account.']);
            }

            if (User::query()->where('role', 'admin')->where('is_active', true)->count() <= 1) {
                return back()->withErrors(['administrator' => 'At least one active administrator must remain.']);
            }
        }

        $user->update(['is_active' => $isActive]);

        return back()->with('status', $isActive ? 'Administrator account activated.' : 'Administrator account deactivated.');
    }

    public function storeManager(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $manager = new User;
        $manager->name = $data['name'];
        $manager->email = $data['email'];
        $manager->phone = $data['phone'] ?? null;
        $manager->password = Str::random(64);
        $manager->role = 'manager';
        $manager->save();

        $status = Password::sendResetLink(['email' => $manager->email]);

        return back()->with('status', $status === Password::RESET_LINK_SENT
            ? 'Manager created. A password setup link was sent.'
            : 'Manager created. Ask them to use Forgot password to set their password.');
    }

    public function updateManager(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'manager', 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->fill($data)->save();

        return back()->with('status', 'Manager details updated.');
    }

    public function managerStatus(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'manager', 404);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $user->update(['is_active' => $data['is_active']]);

        return back()->with('status', $user->is_active ? 'Manager account activated.' : 'Manager account deactivated.');
    }

    public function reviews(): View
    {
        return view('admin.operations.reviews', [
            'reviews' => Review::query()->with('user')->latest()->paginate(20),
        ]);
    }

    public function reviewStatus(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:approved,rejected']]);
        $review->update(['status' => $data['status']]);

        return back()->with('status', 'Review moderation status updated.');
    }

    public function queries(): View
    {
        return view('admin.operations.queries', [
            'queries' => ContactQuery::query()->latest()->paginate(20),
        ]);
    }

    public function queryStatus(Request $request, ContactQuery $query): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:pending,resolved']]);
        $query->update([
            'status' => $data['status'],
            'resolved_at' => $data['status'] === 'resolved' ? now() : null,
        ]);

        return back()->with('status', 'Contact query status updated.');
    }

    public function deleteQuery(ContactQuery $query): RedirectResponse
    {
        $query->delete();

        return back()->with('status', 'Contact query deleted.');
    }

    public function eventEnquiries(): View
    {
        return view('admin.operations.event-enquiries', [
            'enquiries' => EventEnquiry::query()->latest()->paginate(20),
        ]);
    }

    public function eventEnquiryStatus(Request $request, EventEnquiry $eventEnquiry): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:pending,resolved']]);
        $eventEnquiry->update([
            'status' => $data['status'],
            'resolved_at' => $data['status'] === 'resolved' ? now() : null,
        ]);

        return back()->with('status', 'Event enquiry status updated.');
    }

    public function deleteEventEnquiry(EventEnquiry $eventEnquiry): RedirectResponse
    {
        $eventEnquiry->delete();

        return back()->with('status', 'Event enquiry deleted.');
    }

    public function content(): View
    {
        return view('admin.operations.content', [
            'about' => HotelSetting::query()->where('key', 'about')->first()?->value ?? [],
            'resort' => HotelSetting::query()->where('key', 'resort')->first()?->value ?? [],
        ]);
    }

    public function updateContent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'about.title' => ['required', 'string', 'max:255'],
            'about.body' => ['required', 'string', 'max:5000'],
            'about.mission' => ['required', 'string', 'max:2000'],
            'about.vision' => ['required', 'string', 'max:2000'],
            'about.contact_address' => ['nullable', 'string', 'max:500'],
            'about.contact_phone' => ['nullable', 'string', 'max:30'],
            'about.contact_email' => ['nullable', 'email', 'max:255'],
            'resort.title' => ['required', 'string', 'max:255'],
            'resort.body' => ['required', 'string', 'max:5000'],
            'resort.experiences' => ['required', 'string', 'max:3000'],
        ]);

        foreach (['about', 'resort'] as $key) {
            HotelSetting::query()->updateOrCreate(['key' => $key], ['value' => $data[$key]]);
        }

        return back()->with('status', 'Website content updated.');
    }
}
