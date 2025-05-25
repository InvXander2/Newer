<!-- Add -->
<div class="modal fade" id="addnew">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="payment_methods_add.php">
        <div class="modal-header">
          <h4 class="modal-title">Add New Payment Method</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Name (e.g. Bitcoin)</label>
            <input type="text" class="form-control" name="name" required>
            <label>Wallet Address</label>
            <input type="text" class="form-control" name="wallet" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" name="add">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit -->
<div class="modal fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="payment_methods_edit.php">
        <div class="modal-header">
          <h4 class="modal-title">Edit Payment Method</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" class="payid" name="id">
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

<!-- Delete -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="payment_methods_delete.php">
        <div class="modal-header">
          <h4 class="modal-title">Delete Payment Method</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" class="payid" name="id">
          <p>Are you sure you want to delete <strong class="del_name"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger" name="delete">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
