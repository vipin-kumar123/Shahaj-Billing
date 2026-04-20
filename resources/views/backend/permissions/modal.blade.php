 <link rel="stylesheet" href="{{ asset('assets/backend/css/demo/custom.css') }}">


 <div class="modal fade pop-out" id="permission" tabindex="-1">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Create Permission</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <div class="modal-body">
                 <form id="permissionForm" method="post">
                     @csrf
                     <div class="mb-3">
                         <label class="form-label">Name</label>
                         <input type="text" name="name" class="form-control" placeholder="Enter name">
                     </div>

                     <button type="submit" id="permissionBtn" class="mdc-button mdc-button--unelevated">Submit</button>
                 </form>

             </div>
         </div>
     </div>
 </div>
