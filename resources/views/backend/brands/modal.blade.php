 <link rel="stylesheet" href="{{ asset('assets/backend/css/demo/custom.css') }}">


 <div class="modal fade pop-out" id="addBrandModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Add Brand</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body">
                 <form id="addBrandForm" method="post">
                     @csrf
                     <!-- Name -->
                     <div class="mb-3">
                         <label class="form-label fw-semibold">
                             Brand Name <span class="text-danger">*</span>
                         </label>
                         <input type="text" name="name" class="form-control" placeholder="Enter brand name">
                     </div>

                     <!-- Description -->
                     <div class="mb-3">
                         <label class="form-label fw-semibold">Description</label>
                         <textarea name="description" class="form-control" rows="3" placeholder="Optional description"></textarea>

                     </div>

                     <!-- FOOTER BUTTONS -->
                     <div class="d-flex justify-content-end gap-2 mt-4">
                         <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                             Cancel
                         </button>

                         <button type="submit" id="addBrandBtn" class="mdc-button mdc-button--unelevated px-4">
                             <span class="btn-text">Save Brand</span>
                         </button>
                     </div>
                 </form>
             </div>

         </div>
     </div>
 </div>

 {{-- edit category  --}}
 <div class="modal fade pop-out" id="editBrandModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Edit Brand</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body">
                 <form id="editBrandForm" method="post">
                     @csrf
                     <!-- Name -->
                     <div class="mb-3">
                         <label class="form-label fw-semibold">
                             Brand Name <span class="text-danger">*</span>
                         </label>
                         <input type="hidden" name="brand_id">
                         <input type="text" name="name" id="name" class="form-control"
                             placeholder="Enter Brand name">

                     </div>

                     <!-- Description -->
                     <div class="mb-3">
                         <label class="form-label fw-semibold">Description</label>
                         <textarea name="description" id="description" class="form-control" rows="3" placeholder="Optional description"></textarea>

                     </div>

                     <!-- FOOTER BUTTONS -->
                     <div class="d-flex justify-content-end gap-2 mt-4">
                         <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                             Cancel
                         </button>

                         <button type="submit" id="editBrandBtn" class="mdc-button mdc-button--unelevated px-4">
                             <span class="btn-text">Update Brand</span>
                         </button>
                     </div>
                 </form>
             </div>

         </div>
     </div>
 </div>


 {{-- show category  --}}
 <div class="modal fade pop-out" id="showBrand" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Show Brand Details</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body">

             </div>

         </div>
     </div>
 </div>
