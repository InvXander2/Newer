<!-- Delete Modal -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="payment_methods_delete.php">
        <div class="modal-header">
          <h4 class="modal-title"><b>Deleting <span class="name"></span></b></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" class="methodid" name="id">
          <p>Are you sure you want to delete this payment method?</p>
        </div>
        <div class="modal-footer">
          <button type="submit" name="delete" class="btn btn-danger">Yes, Delete</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
