 <h5 class="fw-bold mb-3">General</h5>

 <form method="post" id="GeneralForm" enctype="multipart/form-data">
     @csrf

     <!-- OTHER PROFILE FIELDS -->
     <label class="form-label">Application Name</label>
     <input type="text" name="app_name" class="form-control mb-2" value="{{ old('app_name') }}"
         placeholder="Application Name">

     <label class="form-label">Footer Text</label>
     <input type="text" name="footer_text" class="form-control mb-2" value="{{ old('footer_text') }}"
         placeholder="Footer Text">

     <div class="mb-2">
         <label class="form-label">Language</label>
         <select name="language" class="form-select select2">
             <option value="english">English</option>
             <option value="hindi">Hindi</option>
         </select>
     </div>

     <div class="mb-2">
         <label class="form-label">Timezone</label>
         <select name="timezone" class="form-select select2">
             <option value="GMT">(GMT/UTC 00:00)GMT</option>
         </select>
     </div>

     <div class="mb-2">
         <label class="form-label">Date Format</label>
         <select name="date_format" class="form-select select2">
             <option value="Y-m-d">Y-m-d</option>
             <option value="d/m/Y">d/m/Y</option>
             <option value="d-m-Y">d-m-Y</option>
         </select>
     </div>

     <div class="mb-2">
         <label class="form-label">Time Format</label>
         <select name="time_format" class="form-select select2">
             <option value="12">12 Hours</option>
             <option value="24">24 Hours</option>
         </select>
     </div>


     <button type="submit" id="generalBtn" class="mdc-button mdc-button--unelevated px-5 mt-2">Save</button>
 </form>
