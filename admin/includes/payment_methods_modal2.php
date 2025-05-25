<!-- Delete Payment Method Modal -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <form class="form-horizontal" method="POST" action="payment_methods_delete.php">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>Deleting Payment Method...</b></h4>
        </div>
        <div class="modal-body">
          <input type="hidden" class="pmid" name="id">
          <div class="text-center">
            <p>Are you sure you want to delete this payment method?</p>
            <h2 class="bold name"></h2>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
            <i class="fa fa-close"></i> Close
          </button>
          <button type="submit" class="btn btn-danger btn-flat" name="delete">
            <i class="fa fa-trash"></i> Delete
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add/Edit Payment Method Modal -->
<div class="modal fade" id="edit">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form class="form-horizontal" method="POST" action="payment_methods_edit.php">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>Add / Edit Payment Method</b></h4>
        </div>
        <div class="modal-body">
          <input type="hidden" class="pmid" name="id">

          <div class="form-group">
            <label for="edit_name" class="col-sm-2 control-label">Payment Method Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="edit_name" name="name" placeholder="e.g. Bitcoin" required>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_wallet_address" class="col-sm-2 control-label">Wallet Address</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="edit_wallet_address" name="wallet" placeholder="Enter wallet address" required>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_status" class="col-sm-2 control-label">Status</label>
            <div class="col-sm-10">
              <select class="form-control" id="edit_status" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
            <i class="fa fa-close"></i> Close
          </button>
          <button type="submit" class="btn btn-success btn-flat" name="edit">
            <i class="fa fa-check-square-o"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
