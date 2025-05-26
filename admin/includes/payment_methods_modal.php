<!-- Description -->
<div class="modal fade" id="description">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><span class="name"></span></b></h4>
            </div>
            <div class="modal-body">
                <p id="desc"></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
                <i class="fa fa-close"></i> Close
              </button>
            </div>
        </div>
    </div>
</div>

<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Add New Payment Method</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="payment_methods_add.php" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" name="name" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="wallet_address" class="col-sm-2 control-label">Wallet Address</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="wallet_address" name="wallet_address" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="details" class="col-sm-2 control-label">Details</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" id="details" name="details" rows="3" placeholder="Additional details (optional)"></textarea>
                  </div>
                </div>

                <div class="form-group">
                  <label for="photo" class="col-sm-2 control-label">Photo</label>
                  <div class="col-sm-10">
                    <input type="file" id="photo" name="photo" accept="image/*">
                    <p class="help-block">Upload an image for this payment method (optional).</p>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
                <i class="fa fa-close"></i> Close
              </button>
              <button type="submit" class="btn btn-primary btn-flat" name="add">
                <i class="fa fa-save"></i> Save
              </button>
              </form>
            </div>
        </div>
    </div>
</div>
