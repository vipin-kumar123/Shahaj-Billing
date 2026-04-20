 <h5 class="fw-bold mb-3">Application Logo</h5>

 <form method="post" id="appLogoForm">

     <div class="mb-4">
         <label class="form-label fw-semibold">Image Logo</label>
         <div class="d-flex align-items-center gap-4">

             <!-- Image Box -->
             <div style="width:100px; height:100px; border-radius:6px; background:#f1f1f1;"
                 class="d-flex justify-content-center align-items-center overflow-hidden">

                 <img id="previewLogo" data-default="{{ asset('assets/backend/images/faces/default.png') }}"
                     src="" style="width:100%; height:100%; object-fit:contain;">
             </div>

             <!-- Buttons -->
             <div>
                 <div class="d-flex gap-2">
                     <label class="btn btn-outline-primary btn-sm m-0">
                         Browse
                         <input type="file" id="logoInput" name="logo" accept="image/*" class="d-none">
                     </label>

                     <button type="button" class="btn btn-outline-secondary btn-sm" id="resetLogo">Reset</button>
                 </div>
                 <small class="text-muted mt-1 d-block">Allowed JPG, GIF, PNG. Max size 1MB.</small>
             </div>

         </div>
     </div>



     <div class="mb-4">
         <label class="form-label fw-semibold">Favicon</label>

         <div class="d-flex align-items-center gap-4">

             <!-- Image Box -->
             <div style="width:100px; height:100px; border-radius:6px; background:#f1f1f1;"
                 class="d-flex justify-content-center align-items-center overflow-hidden">

                 <img id="previewFavicon" data-default="{{ asset('assets/backend/images/faces/default.png') }}"
                     src="" style="width:100%; height:100%; object-fit:contain;">
             </div>

             <!-- Buttons -->
             <div>
                 <div class="d-flex gap-2">
                     <label class="btn btn-outline-primary btn-sm m-0">
                         Browse
                         <input type="file" id="faviconInput" name="favicon" accept="image/*" class="d-none">
                     </label>

                     <button type="button" class="btn btn-outline-secondary btn-sm" id="resetFavicon">Reset</button>
                 </div>
                 <small class="text-muted mt-1 d-block">Allowed JPG, GIF, PNG. Max size 1MB.</small>
             </div>

         </div>
     </div>


     <button type="submit" id="logoBtn" class="mdc-button mdc-button--unelevated px-5 mt-2">Update</button>

 </form>
