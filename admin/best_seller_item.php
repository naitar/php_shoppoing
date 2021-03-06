<?php
  session_start();
  require '../Config/config.php';
  require '../Config/common.php';

  if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in']))
  {
    header('location:login.php');
  }

  if(isset($_SESSION['role']))
  {
    if($_SESSION['role'] == 0)
    {    
      echo  "<script>alert('You are not admin');window.location.href='login.php'</script>";
    }    
  }

  require('header.php');

  $currentDate = date('Y-m-d');
  $fromDate = date('Y-m-d',strtotime($currentDate. '+1 day'));
  $toDate = date('Y-m-d',strtotime($currentDate. '-1 months'));

//   echo $fromDate."<br>";
//   echo $toDate;
//   exit();

//   2021-05-22
//   2021-04-21

  
    $stmt = $pdo->prepare("select product_id,sum(quantity) as qty,order_date from sale_order_detail 
                           group by product_id  
                           having order_date < :fromDate AND order_date >= :toDate AND sum(quantity) >= 7");                         
                         
    $stmt->execute([':fromDate'=>$fromDate, ':toDate'=>$toDate]);
    $result = $stmt->fetchAll();

//   echo "<pre>";
//   print_r($result);
//   echo "</pre>";
//   exit();
?>

<!-- Header -->
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <!-- table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Best Seller Item (1 Month Sell product_item > 7) </h3>
            </div>

            <!-- /.card-header -->
            <div class="card-body">

                <table class="table table-bordered" id="d-table">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Order Date</th>                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if ($result)
                    {
                        $i = 1; 
                        foreach ($result as $value) 
                        { 
                            $proStmt = $pdo->prepare("SELECT * FROM products WHERE id=".$value['product_id']);
                            $proStmt->execute();
                            $ProResult = $proStmt->fetchAll();
                            // echo "<pre>";
                            // print_r($ProResult);
                            // echo "</pre>";
                            // exit();
                    
                    ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo escape($ProResult[0]['name']); ?></td>
                            <td> <?php echo escape(number_format($value['qty'])); ?></td>
                            <td><?php echo escape(date("Y-m-d",strtotime($value['order_date']))); ?> </td>                            
                        </tr>   
                        <?php 
                        $i++;
                        }
                    }   
                    ?>    
                    </tbody>
                </table>

            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
        <!-- table -->

    </div>
</div>

</div>
<!-- /.content-wrapper -->



<!-- Footer -->
<?php include('footer.html'); ?>
<!-- DataTable -->
<script>
    $(document).ready(function() {
        $('#d-table').DataTable();
    } );
</script>