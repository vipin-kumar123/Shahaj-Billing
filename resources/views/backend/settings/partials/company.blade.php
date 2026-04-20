<h5 class="fw-bold mb-3">Company Details</h5>

<form method="post" id="companyForm">

    <label class="form-label"> Name</label>
    <input type="text" name="name" class="form-control mb-2" value="{{ old('name') }}" placeholder="Name">

    <label class="form-label">Mobile</label>
    <input type="text" name="mobile" class="form-control mb-2" value="{{ old('mobile') }}" placeholder="Mobile">

    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control mb-2" value="{{ old('email') }}" placeholder="Email">

    <label class="form-label">Tax Number</label>
    <input type="text" name="tax_number" class="form-control mb-2" value="{{ old('tax_number') }}"
        placeholder="Tax Number">

    <label class="form-label">Address</label>
    <textarea name="address" class="form-control mb-2">{{ old('address') }}</textarea>

    <div class="mb-2">
        <label class="form-label">State Name</label>
        <select name="state_id" id="stateSelect" class="form-select select2" data-placeholder="Nathing Select">
            <option value="0">Not Select</option>
            @if ($stateData->isNotEmpty())
                @foreach ($stateData as $state)
                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="mb-2">
        <input type="hidden" id="selectedCity">
        <label class="form-label">City Name</label>
        <select name="city_id" id="citySelect" class="form-select select2" data-placeholder="Nathing Select">
            <option value="0">Not Select</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold">Company Logo</label>

        <div class="d-flex align-items-start gap-4">

            <!-- Preview / Placeholder Box -->
            <div id="logo-box"
                style="width:110px; height:110px; border-radius:8px; background:#f8f9fa; border:1px dashed #c7c7c7;"
                class="d-flex justify-content-center align-items-center overflow-hidden text-muted">

                <span id="logo-placeholder" class="small" style="opacity:0.7;">No Logo</span>

                <img id="company-logo-preview" src=""
                    style="display:none; width:100%; height:100%; object-fit:cover;">
            </div>

            <!-- File Input Column -->
            <div style="flex:1;">
                <label class="form-label">Upload Logo</label>
                <input type="file" name="company_logo" accept="image/*" class="form-control form-control-sm"
                    id="logoFile">

                <small class="text-muted d-block mt-1">
                    Allowed: JPG, PNG, JPEG, GIF | Max size: 1MB
                </small>
            </div>
        </div>
    </div>


    <button type="submit" id="companyBtn" class="mdc-button mdc-button--unelevated px-5 mt-2">Update</button>

</form>
