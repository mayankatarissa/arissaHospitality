@props(['filters' => []])

@php
    $minimum = (int) ($filters['min_budget'] ?? 0);
    $maximum = (int) ($filters['max_budget'] ?? 1500);
@endphp

<form action="{{ route('rooms.index') }}" method="GET" class="row g-3 align-items-end">
    <div class="col-6 col-lg-3">
        <label class="form-label" for="check_in_date">Check-in date</label>
        <input class="form-control" id="check_in_date" name="check_in_date" type="date" min="{{ now()->toDateString() }}" value="{{ $filters['check_in_date'] ?? '' }}">
    </div>
    <div class="col-6 col-lg-2">
        <label class="form-label" for="check_in_time">Check-in time</label>
        <input class="form-control" id="check_in_time" name="check_in_time" type="time" value="{{ $filters['check_in_time'] ?? '' }}">
    </div>
    <div class="col-6 col-lg-3">
        <label class="form-label" for="check_out_date">Check-out date</label>
        <input class="form-control" id="check_out_date" name="check_out_date" type="date" min="{{ now()->toDateString() }}" value="{{ $filters['check_out_date'] ?? '' }}">
    </div>
    <div class="col-6 col-lg-2">
        <label class="form-label" for="check_out_time">Check-out time</label>
        <input class="form-control" id="check_out_time" name="check_out_time" type="time" value="{{ $filters['check_out_time'] ?? '' }}">
    </div>
    <div class="col-12 col-lg-10">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label mb-1">Budget per night</label>
            <span class="small"><span id="budget-min-label">{{ number_format($minimum) }}</span> – <span id="budget-max-label">{{ number_format($maximum) }}</span></span>
        </div>
        <input type="hidden" name="min_budget" id="min_budget" value="{{ $minimum }}">
        <input type="hidden" name="max_budget" id="max_budget" value="{{ $maximum }}">
        <div class="d-flex gap-3">
            <input class="form-range" id="min-budget-range" type="range" min="0" max="5000" step="25" value="{{ $minimum }}" aria-label="Minimum budget">
            <input class="form-range" id="max-budget-range" type="range" min="0" max="5000" step="25" value="{{ $maximum }}" aria-label="Maximum budget">
        </div>
    </div>
    <div class="col-12 col-lg-2">
        <button class="btn btn-hotel w-100" type="submit">Search rooms</button>
    </div>
</form>

@push('scripts')
    <script>
        (() => {
            const minRange = document.getElementById('min-budget-range');
            const maxRange = document.getElementById('max-budget-range');
            const minInput = document.getElementById('min_budget');
            const maxInput = document.getElementById('max_budget');
            if (!minRange || !maxRange) return;

            const updateBudget = (changed) => {
                let min = Number(minRange.value);
                let max = Number(maxRange.value);
                if (min > max) {
                    if (changed === minRange) max = min;
                    else min = max;
                }
                minRange.value = min;
                maxRange.value = max;
                minInput.value = min;
                maxInput.value = max;
                document.getElementById('budget-min-label').textContent = min.toLocaleString();
                document.getElementById('budget-max-label').textContent = max.toLocaleString();
            };

            minRange.addEventListener('input', () => updateBudget(minRange));
            maxRange.addEventListener('input', () => updateBudget(maxRange));
            updateBudget();
        })();
    </script>
@endpush
