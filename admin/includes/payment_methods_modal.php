<!-- Add Payment Method Modal -->
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
