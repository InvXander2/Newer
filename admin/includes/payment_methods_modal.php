<!-- Edit Modal -->
<div class="modal fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="payment_methods_edit.php">
        <div class="modal-header">
          <h4 class="modal-title"><b>Edit Payment Method</b></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" class="methodid" name="id">
          <div class="form-group">
            <label for="edit_name">Payment Method</label>
            <input type="text" class="form-control" id="edit_name" name="name" required>
          </div>
          <div class="form-group">
            <label for="edit_wallet">Wallet Address</label>
            <input type="text" class="form-control" id="edit_wallet" name="wallet_address" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="edit" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
