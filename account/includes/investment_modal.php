<!-- Investment Modal -->
<div class="modal fade" id="investmentModal" tabindex="-1" role="dialog" aria-labelledby="investmentModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="process_investment.php">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="investmentModalLabel">Invest in a Plan</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="planSelect">Select Investment Plan</label>
            <select id="planSelect" name="plan_id" class="form-control" required>
              <option value="" disabled selected>-- Select Plan --</option>
              <?php
                $conn = $pdo->open();
                try {
                  $stmt = $conn->prepare("SELECT * FROM investment_plans");
                  $stmt->execute();
                  foreach ($stmt as $row) {
                    echo "<option value='".$row['id']."'>".$row['name']." - ".$row['rate']."% for ".$row['duration']."</option>";
                  }
                } catch (PDOException $e) {
                  echo "<option disabled>Error loading plans</option>";
                }
                $pdo->close();
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="amount">Amount to Invest</label>
            <input type="number" class="form-control" name="amount" id="amount" required min="1">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-flat" name="invest">Confirm Investment</button>
        </div>
      </form>
    </div>
  </div>
</div>
