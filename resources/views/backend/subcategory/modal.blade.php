 <link rel="stylesheet" href="{{ asset('assets/backend/css/demo/custom.css') }}">


 <div class="modal fade pop-out" id="addSubCategoryModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Add Sub Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <!-- BODY -->
             <div class="modal-body">
                 <form id="addsubcatForm" method="post">
                     @csrf

                     <div class="mb-3">
                         <label class="form-label fw-semibold">
                             Category <span class="text-danger">*</span>
                         </label>
                         <select name="category_id" class="form-control select2" data-placeholder="Nothing Select">
                             <option value="">Nothing Select</option>
                             @forelse ($categories as $c)
                                 <option value="{{ $c->id }}">{{ $c->name }}</option>
                             @empty
                                 <option value="">No Categories Available</option>
                             @endforelse
                         </select>
                     </div>

                     <div id="subContainer">
                         <div class="row row-item mb-3">
                             <div class="col-md-10">
                                 <label class="form-label fw-semibold">Sub Category</label>
                                 <input type="text" name="name[]" class="form-control sub_name" placeholder="Name">
                             </div>

                             <input type="hidden" name="slug[]" class="form-control sub_slug">

                             <div class="col-md-2 d-flex align-items-center mt-2">
                                 <button type="button" class="btn btn-danger btn-sm removeRow mt-4">X</button>
                             </div>

                             <div class="col-md-12 mt-2">
                                 <label class="form-label fw-semibold">Description</label>
                                 <textarea name="description[]" class="form-control" rows="2" placeholder="Optional"></textarea>
                             </div>

                         </div>

                     </div>


                     <button type="button" id="addMoreSub" class="mdc-button mdc-button--success px-4">
                         + Add More
                     </button>


                     <div class="d-flex justify-content-end gap-2 mt-4">
                         <button type="button" class="btn btn-light btn-sm mb-3" data-bs-dismiss="modal">
                             Cancel
                         </button>

                         <button type="submit" id="addsubcatBtn" class="mdc-button mdc-button--unelevated px-4">
                             Save All
                         </button>
                     </div>

                 </form>
             </div>
         </div>
     </div>
 </div>

 {{-- edit category  --}}
 <div class="modal fade pop-out" id="editSubCategoryModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Edit Sub Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <!-- BODY -->
             <div class="modal-body">
                 <form id="editsubcatForm" method="post">
                     @csrf
                     <div class="mb-3">
                         <label class="form-label fw-semibold">
                             Category <span class="text-danger">*</span>
                         </label>
                         <select name="category_id" class="form-control select2" data-placeholder="Nothing Select">
                             <option value="">Nothing Select</option>
                             @forelse ($categories as $c)
                                 <option value="{{ $c->id }}">{{ $c->name }}</option>
                             @empty
                                 <option value="">No Categories Available</option>
                             @endforelse
                         </select>
                     </div>

                     <div id="subContainer">
                         <div class="row row-item mb-3">
                             <div class="col-md-12">
                                 <label class="form-label fw-semibold">Sub Category</label>
                                 <input type="hidden" name="subcatid">
                                 <input type="text" name="name" class="form-control sub_name" placeholder="Name">
                             </div>

                             <input type="hidden" name="slug" class="form-control sub_slug">

                             <div class="col-md-12 mt-2">
                                 <label class="form-label fw-semibold">Description</label>
                                 <textarea name="description" class="form-control" rows="2" placeholder="Optional"></textarea>
                             </div>

                         </div>

                     </div>


                     <div class="d-flex justify-content-end gap-2 mt-4">
                         <button type="button" class="btn btn-light btn-sm mb-3" data-bs-dismiss="modal">
                             Cancel
                         </button>

                         <button type="submit" id="editSubcatBtn" class="mdc-button mdc-button--unelevated px-4">
                             Update
                         </button>
                     </div>

                 </form>
             </div>

         </div>
     </div>
 </div>


 {{-- show category  --}}
 <div class="modal fade pop-out" id="showSubCategoryModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
         <div class="modal-content border-0 shadow-sm">

             <!-- HEADER -->
             <div class="modal-header">
                 <h5 class="modal-title fw-semibold">Show Category</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body">

             </div>

         </div>
     </div>
 </div>
