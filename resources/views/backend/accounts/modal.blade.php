 <link rel="stylesheet" href="{{ asset('assets/backend/css/demo/custom.css') }}">

 {{-- add account --}}
 <div class="modal fade pop-out" id="addAccountModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content border-0 shadow-sm rounded-3">

             <!-- HEADER -->
             <div class="modal-header border-bottom">
                 <h5 class="modal-title fw-semibold mb-0">Add Account</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body p-4">
                 <form id="addAccountForm" method="POST">
                     @csrf

                     <div class="row g-3">

                         <!-- Account Name -->
                         <div class="col-md-12">
                             <label class="form-label fw-semibold">
                                 Account Name <span class="text-danger">*</span>
                             </label>
                             <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                 placeholder="e.g. Cash in Hand / Bank / Sales A/c">

                         </div>

                         <!-- Account Type -->
                         <div class="col-md-6">
                             <label class="form-label fw-semibold">
                                 Account Type <span class="text-danger">*</span>
                             </label>
                             <select name="type" class="form-select">
                                 <option value="">Select Type</option>
                                 <option value="asset" {{ old('type') == 'asset' ? 'selected' : '' }}>Asset</option>
                                 <option value="liability" {{ old('type') == 'liability' ? 'selected' : '' }}>Liability
                                 </option>
                                 <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                                 <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense
                                 </option>
                             </select>

                         </div>

                         <!-- Parent Account -->
                         <div class="col-md-6">
                             <label class="form-label fw-semibold">Parent Account</label>
                             <select name="parent_id" class="form-select select2">
                                 <option value="">Select Parent</option>
                                 @foreach ($accounts ?? [] as $acc)
                                     <option value="{{ $acc->id }}"
                                         {{ old('parent_id') == $acc->id ? 'selected' : '' }}>
                                         {{ ucfirst($acc->type) }} - {{ $acc->name }}
                                     </option>
                                 @endforeach
                             </select>

                         </div>

                     </div>

                     <!-- FOOTER BUTTONS -->
                     <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-3">
                         <button type="button" class="mdc-button mdc-button--light" data-bs-dismiss="modal">
                             Cancel
                         </button>
                         <button type="submit" id="addBtn" class="mdc-button mdc-button--unelevated">
                             Save Account
                         </button>
                     </div>
                 </form>
             </div>

         </div>
     </div>
 </div>


 {{-- edit account --}}
 <div class="modal fade pop-out" id="editAccountModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content border-0 shadow-sm rounded-3">

             <!-- HEADER -->
             <div class="modal-header border-bottom">
                 <h5 class="modal-title fw-semibold mb-0">Edit Account</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <!-- BODY -->
             <div class="modal-body p-4">
                 <form id="updateAccountForm" method="POST">
                     @csrf
                     <input type="hidden" name="account_id" id="edit_account_id">
                     <div class="row g-3">
                         <!-- Account Name -->
                         <div class="col-md-12">
                             <label class="form-label fw-semibold">
                                 Account Name <span class="text-danger">*</span>
                             </label>
                             <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                 placeholder="e.g. Cash in Hand / Bank / Sales A/c">

                         </div>

                         <!-- Account Type -->
                         <div class="col-md-6">
                             <label class="form-label fw-semibold">
                                 Account Type <span class="text-danger">*</span>
                             </label>
                             <select name="type" class="form-select">
                                 <option value="">Select Type</option>
                                 <option value="asset" {{ old('type') == 'asset' ? 'selected' : '' }}>Asset</option>
                                 <option value="liability" {{ old('type') == 'liability' ? 'selected' : '' }}>Liability
                                 </option>
                                 <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                                 <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense
                                 </option>
                             </select>

                         </div>

                         <!-- Parent Account -->
                         <div class="col-md-6">
                             <label class="form-label fw-semibold">Parent Account</label>
                             <select name="parent_id" class="form-select select2">
                                 <option value="">Select Parent</option>
                                 @foreach ($accounts ?? [] as $acc)
                                     <option value="{{ $acc->id }}"
                                         {{ old('parent_id') == $acc->id ? 'selected' : '' }}>
                                         {{ ucfirst($acc->type) }} - {{ $acc->name }}
                                     </option>
                                 @endforeach
                             </select>

                         </div>

                     </div>

                     <!-- FOOTER BUTTONS -->
                     <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-3">
                         <button type="button" class="mdc-button mdc-button--light" data-bs-dismiss="modal">
                             Cancel
                         </button>
                         <button type="submit" id="updateBtn" class="mdc-button mdc-button--unelevated">
                             Update Account
                         </button>
                     </div>
                 </form>
             </div>

         </div>
     </div>
 </div>
