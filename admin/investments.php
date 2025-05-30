<div class="box-body">
  <div class="table-responsive">
    <table id="example1" class="table table-bordered">
      <thead>
        <th>Username</th>
        <th>Plan</th>
        <th>Capital</th>
        <th>Return</th>
        <th>Current Compound</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Status</th>
        <th>Edit Status</th>
      </thead>
      <tbody>
        <?php
          $conn = $pdo->open();

          try{
            $stmt = $conn->prepare("SELECT *, investment.status AS invest_status FROM investment LEFT JOIN investment_plans ON investment_plans.id=investment.invest_plan_id LEFT JOIN users ON users.id=investment.user_id order by invest_id desc");
            $stmt->execute();

            $now = date('Y-m-d H:i:s');

            foreach($stmt as $row){ ?>
                <tr>
                  <td><?php echo $row['uname']; ?></td>
                  <td><?php echo $row['name']; ?></td>
                  <td>$ <?php echo number_format($row['capital'], 2); ?></td>
                  <td>$ <?php echo number_format($row['returns'], 2); ?></td>
                  <td>
                    <?php
                      $date_ivstart = strtotime($row['start_date']);
                      $date_ivend = strtotime($row['end_date']);
                      $date_now = strtotime($now);

                      $secs = $date_now - $date_ivstart;// == <seconds between the two times>
                      $day = $secs / 86400;

                      $total_days = ($date_ivend - $date_ivstart) / 86400;

                      $current_cpd = $row['capital'] + ($row['capital'] * $row['rate'] / $total_days * $day / 100);

                      if ($row['invest_status'] == 'in progress') {
                        $query = $conn->prepare("UPDATE investment SET current=:current_cpd WHERE invest_id=:cpd_id");
                        $query->execute(['current_cpd'=>$current_cpd, 'cpd_id'=>$row['invest_id']]);

                        echo number_format($current_cpd, 2);

                      }else{
                        echo number_format($row['current'], 2);}
                    ?>
                  </td>
                  <td><?php echo $row['start_date']; ?></td>
                  <td><?php echo $row['end_date']; ?></td>
                  <td>
                    <?php 
                      $iv_end = strtotime($row['end_date']);
                      $t_day = strtotime($now);
                      if ($t_day >= $iv_end) {
                        $stmt = $conn->prepare("UPDATE investment SET status=:c_status WHERE invest_id=:c_id");
                        $stmt->execute(['c_status'=>'completed', 'c_id'=>$row['invest_id']]);

                        echo 'completed';

                      }else{
                        echo $row['invest_status'];}
                    ?>
                  </td>
                  <td>
                    <button class="btn btn-primary btn-sm edit btn-flat" data-id="<?php echo $row['invest_id']; ?>"><i class="fa fa-edit"></i> Status</button>
                  </td>
                </tr>
          <?php  
            } 
          } 

          catch(PDOException $e){
            echo $e->getMessage();
          }
          $pdo->close(); ?>
      </tbody>
    </table>
  </div>
</div>
