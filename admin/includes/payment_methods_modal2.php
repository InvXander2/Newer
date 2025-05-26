<!-- Edit Payment Method Modal -->
<div class="modal fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="payment_methods_edit.php">
        <div class="modal-header">
          <h4 class="modal-title">Edit Payment Method</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" class="methodid" name="id">
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" id="edit_name" name="name" required>
            <label>Wallet Address</label>
            <input type="text" class="form-control" id="edit_wallet" name="wallet" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" name="edit">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Payment Method Modal -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="payment_methods_delete.php">
        <div class="modal-header">
          <h4 class="modal-title">Delete Payment Method</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" class="methodid" name="id">
          <p>Are you sure you want to delete <strong class="name"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger" name="delete">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
