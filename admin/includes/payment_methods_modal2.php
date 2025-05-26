<!-- Delete -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Deleting...</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="payment_methods_delete.php">
          <input type="hidden" class="methodid" name="id">
          <div class="text-center">
            <p>DELETE Payment Method</p>
            <h2 class="bold name"></h2>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit -->
<div class="modal fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Edit Payment Method</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="payment_methods_edit.php">
          <input type="hidden" class="methodid" name="id">
          <div class="form-group">
            <label for="edit_name" class="col-sm-3 control-label">Method Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_wallet" class="col-sm-3 control-label">Wallet Address</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_wallet" name="wallet" required>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
