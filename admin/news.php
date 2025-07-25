<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        News
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">News</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <th>Title</th>
                    <th>Photo</th>
                    <th>Details</th>
                    <th>Date Posted</th>
                    <th>Actions</th>
                  </thead>
                  <tbody>
                    <?php
                      $conn = $pdo->open();

                      try{
                        $stmt = $conn->prepare("SELECT * FROM news");
                        $stmt->execute();
                        foreach($stmt as $row){
                          $image = (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg';
                          echo "
                            <tr>
                              <td>".$row['title']."</td>
                              <td>
                                <img src='".$image."' height='30px' width='30px'>
                                <span class='pull-right'><a href='#edit_photo' class='photo' data-toggle='modal' data-id='".$row['id']."'><i class='fa fa-edit'></i></a></span>
                              </td>
                              <td><a href='#details' data-toggle='modal' class='btn btn-info btn-sm btn-flat desc' data-id='".$row['id']."'><i class='fa fa-search'></i> View</a></td>
                              <td>".$row['posted']."</td>
                              <td>
                                <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['id']."'><i class='fa fa-edit'></i> Edit</button>
                                <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."'><i class='fa fa-trash'></i> Delete</button>
                              </td>
                            </tr>
                          ";
                        }
                      }
                      catch(PDOException $e){
                        echo $e->getMessage();
                      }

                      $pdo->close();
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
     
  </div>
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/news_modal.php'; ?>
  <?php include 'includes/news_modal2.php'; ?>

</div>
<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.photo', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.desc', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  
  $('#addreview').click(function(e){
    e.preventDefault();
    getCategory();
    getBrand();
  });

  $("#addnew").on("hidden.bs.modal", function () {
      $('.append_items').remove();
  });

  $("#edit").on("hidden.bs.modal", function () {
      $('.append_items').remove();
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'news_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('#desc').html(response.details);
      $('.title').html(response.ntitle);
      $('.nid').val(response.nid);
      $('#edit_title').val(response.ntitle);
      $('#edit_posted').val(response.posted);
      $('#edit_short_title').val(response.short_title);
      $('#edit_short_details').val(response.short_details);
      CKEDITOR.instances["editor2"].setData(response.details);
    }
  });
}
</script>
<style>
.table-responsive {
  overflow-x: auto;
  width: 100%;
}

.table-responsive table {
  min-width: 800px; /* Adjust based on your table's content width */
}
</style>
</body>
</html>
