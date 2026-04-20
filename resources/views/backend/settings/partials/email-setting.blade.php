<h5 class="fw-bold mb-3">SMTP Settings</h5>

<form method="post" id="hostForm" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf

    <!-- OTHER PROFILE FIELDS -->
    <label class="form-label">Host</label>
    <input type="text" name="host" class="form-control mb-2" value="{{ old('host', $user->host ?? '') }}">

    <label class="form-label">Port</label>
    <input type="text" name="port" class="form-control mb-2" value="{{ old('port', $user->port ?? '') }}">

    <label class="form-label">Username</label>
    <input type="text" name="username" class="form-control mb-2"
        value="{{ old('username', $user->username ?? '') }}">

    <label class="form-label">Password</label>
    <input type="text" name="password" class="form-control mb-2"
        value="{{ old('password', $user->password ?? '') }}">

    <label class="form-label">Encryption</label>
    <input type="text" name="encryption" class="form-control mb-2"
        value="{{ old('encryption', $user->encryption ?? '') }}">

    <label class="form-label">Status</label>
    <select name="footer_text" class="form-select mb-2">
        <option value="1">Enable</option>
        <option value="0">Disable</option>
    </select>


    <button type="submit" id="emailSettingBtn" class="mdc-button mdc-button--unelevated px-5 mt-2">Save</button>
</form>
