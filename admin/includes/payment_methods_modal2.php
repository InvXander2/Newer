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
          <input type="hidden" class="pmid" name="id">
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Edit Payment Method</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="payment_methods_edit.php" enctype="multipart/form-data">
          <input type="hidden" class="pmid" name="id">
          
          <div class="form-group">
            <label for="edit_name" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_wallet_address" class="col-sm-2 control-label">Wallet Address</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="edit_wallet_address" name="wallet_address" required>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_details" class="col-sm-2 control-label">Details</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="edit_details" name="details" rows="3" required></textarea>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_photo" class="col-sm-2 control-label">Photo</label>
            <div class="col-sm-10">
              <input type="file" id="edit_photo" name="photo">
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
