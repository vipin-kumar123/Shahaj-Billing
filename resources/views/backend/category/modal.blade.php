 <link rel="stylesheet" href="{{ asset('assets/backend/css/demo/custom.css') }}">


 <div class="modal fade pop-out" id="addCategory" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Add Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body">
                 <form id="addcatForm" method="post">
                     @csrf

                     <!-- Name -->
                     <div class="mb-3">
                         <label class="form-label fw-semibold">
                             Category Name <span class="text-danger">*</span>
                         </label>
                         <input type="text" name="name" class="form-control" placeholder="Enter category name">

                     </div>

                     <!-- Slug -->
                     <div class="mb-3">
                         <label class="form-label fw-semibold">
                             Slug <span class="text-danger">*</span>
                         </label>
                         <input type="text" name="slug" class="form-control" placeholder="slug">

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

                         <button type="submit" id="addcatBtn" class="mdc-button mdc-button--unelevated px-4">
                             <span class="btn-text">Save Category</span>
                         </button>
                     </div>
                 </form>
             </div>

         </div>
     </div>
 </div>

 {{-- edit category  --}}
 <div class="modal fade pop-out" id="editCategory" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Edit Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body">
                 <form id="editCatForm" method="post">
                     @csrf
                     <!-- Name -->
                     <div class="mb-3">
                         <label class="form-label fw-semibold">
                             Category Name <span class="text-danger">*</span>
                         </label>
                         <input type="hidden" name="catid">
                         <input type="text" name="name" id="name" class="form-control"
                             placeholder="Enter category name">

                     </div>

                     <!-- Slug -->
                     <div class="mb-3">
                         <label class="form-label fw-semibold">
                             Slug <span class="text-danger">*</span>
                         </label>
                         <input type="text" name="slug" id="slug" class="form-control" placeholder="slug">

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

                         <button type="submit" id="editcatBtn" class="mdc-button mdc-button--unelevated px-4">
                             <span class="btn-text">Save Category</span>
                         </button>
                     </div>
                 </form>
             </div>

         </div>
     </div>
 </div>


 {{-- show category  --}}
 <div class="modal fade pop-out" id="showCategory" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Show Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body" id="editCategoryBody">

             </div>

         </div>
     </div>
 </div>
