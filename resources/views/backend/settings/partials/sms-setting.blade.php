<h5 class="fw-bold mb-3">SMS Settings</h5>

<form method="post" id="hostForm" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf

    <!-- OTHER PROFILE FIELDS -->
    <label class="form-label">Twilio</label>
    <input type="text" name="host" class="form-control mb-2">

    <label class="form-label">Vonage</label>
    <input type="text" name="port" class="form-control mb-2">

    <button type="submit" id="smsBtn" class="mdc-button mdc-button--unelevated px-5 mt-2">Save</button>
</form>
