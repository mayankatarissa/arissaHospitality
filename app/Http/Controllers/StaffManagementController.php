<?php

namespace App\Http\Controllers;

use App\Models\StaffDetail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffManagementController extends Controller
{
    public function index(Request $request): View
    {
        $staff = StaffDetail::query()->with('manager')->latest();

        if ($request->user()->role === 'manager') {
            $staff->where('manager_id', $request->user()->id);
        }

        return view('management.staff.index', [
            'staff' => $staff->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('management.staff.form', [
            'record' => new StaffDetail,
            'managers' => User::query()->where('role', 'manager')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['manager_id'] = $request->user()->role === 'manager'
            ? $request->user()->id
            : ($data['manager_id'] ?? null);
        $data['created_by'] = $request->user()->id;
        StaffDetail::query()->create($data);

        return redirect()->route('management.staff.index')->with('status', 'Staff record created.');
    }

    public function edit(Request $request, StaffDetail $staff): View
    {
        $this->ensureCanManage($request, $staff);

        return view('management.staff.form', [
            'record' => $staff,
            'managers' => User::query()->where('role', 'manager')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, StaffDetail $staff): RedirectResponse
    {
        $this->ensureCanManage($request, $staff);
        $data = $this->validated($request);

        if ($request->user()->role === 'manager') {
            unset($data['manager_id']);
        }

        $staff->update($data);

        return redirect()->route('management.staff.index')->with('status', 'Staff record updated.');
    }

    public function destroy(Request $request, StaffDetail $staff): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $staff->delete();

        return back()->with('status', 'Staff record deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['required', 'string', 'max:150'],
            'department' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ];

        if ($request->user()->role === 'admin') {
            $rules['manager_id'] = ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'manager')];
        }

        return $request->validate($rules);
    }

    private function ensureCanManage(Request $request, StaffDetail $staff): void
    {
        abort_unless(
            $request->user()->role === 'admin' || $staff->manager_id === $request->user()->id,
            404,
        );
    }
}
