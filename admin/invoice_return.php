<?php 
ob_start();
session_start();
if($_SESSION['name']!='www.somrat.info')
{
  header('location: index.php');
}
include("head.php"); 
include("../connection.php");
$memo_no = $_GET['memo_no'];

if(isset($_POST['invoice']))
{
  try {


    for($i=0;$i<count($_POST['itemNo']);$i++)
    {

  
        $itemNo['itemNo']      = $_POST['itemNo'][$i];
        $itemName['itemName']     = $_POST['itemName'][$i];
        $price['price']        = $_POST['price'][$i];
        $quantity['quantity']     = $_POST['quantity'][$i];
        $total['total']        = $_POST['total'][$i];

        $statement = $db->prepare("SELECT memo_item.memo_id , memo_item.memo_no , memo_item.item_quantity, memo_item.item_total , table_products.quantityInStock , table_products.productCode FROM `memo_item` INNER JOIN `table_products` ON `memo_item`.memo_no = ? AND `memo_item`.item_id = ? AND `memo_item`.item_id = `table_products`.productCode");
        $statement->execute(array($memo_no,$itemNo['itemNo']));
        if($result = $statement->fetchAll(PDO::FETCH_ASSOC)){
        
        foreach ($result as $row) {
          $row['item_quantity'] = $row['item_quantity']-$quantity['quantity'];
          $row['item_total']    = $row['item_total']-$total['total'];
          $row['quantityInStock'] = $row['quantityInStock']+$quantity['quantity'];
          $memo_id  = $row['memo_id'];

        $statement1 = $db->prepare("UPDATE memo_item SET item_quantity=?,item_total=? WHERE memo_id = ?");
        $statement1->execute(array($row['item_quantity'],$row['item_total'],$memo_id));

        $statement2 = $db->prepare("UPDATE table_products SET quantityInStock=? WHERE productCode = ?");
        $statement2->execute(array($row['quantityInStock'],$row['productCode']));

        }
        }
   
      }


    echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.alert('Return Item Inserted successfully')
    window.location.href='memo.php';
    </SCRIPT>");
    
  
  }
  
  catch(Exception $e) { 
    $error_message = $e->getMessage();
  }
}
?>

<div class="content-wrapper">
  <section class="content">
	<div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div align="center" class="box-header with-border">
                  <h3 class="box-title">Return An Item</h3>
                </div><!-- /.box-header -->
                <form class="" method="post" action="" enctype="multipart/form-data">
		
                <div class="box-body">
                 <div class="table-responsive">  
                 
                  <table id="invoice_bill" class="table table-bordered table-hover">
                    <thead>
                        <tr>
							<th width="2%"><input id="check_all" class="formcontrol" type="checkbox"/></th>
							<th width="13%">Item ID</th>
							<th width="33%">Item Name</th>
							<th width="13%">Available</th>
							<th width="13%">Price</th>
							<th width="13%">Quantity</th>
							<th width="13%">Total</th>
						</tr>
                    </thead>

                    <tbody>
                    	<tr>
							<td><input class="case" type="checkbox"/></td>
							<td><input type="text" data-type="productCode" name="itemNo[]" id="itemNo_1" class="form-control autocomplete_txt" autocomplete="off"></td>
							<td><input type="text" data-type="productName" name="itemName[]" id="itemName_1" class="form-control autocomplete_txt" autocomplete="off"></td>
							<td><input type="text" data-type="productAvailable" name="itemAvailable[]" id="itemAvailable_1" class="form-control autocomplete_txt" autocomplete="off"></td>
							<td><input type="number" name="price[]" id="price_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>
							<td><input type="number" name="quantity[]" id="quantity_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>
							<td><input type="number" name="total[]" id="total_1" class="form-control totalLinePrice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>
						</tr>
                    </tbody>
                  </table>
                </div>
             <div class='col-xs-12 col-sm-3 col-md-3 col-lg-3'>
      			<button class="btn btn-danger delete" type="button">- Delete</button>
      			<button class="btn btn-success addmore" type="button">+ Add More</button>
      		</div>

				
			<div class="row col-md-6 pull-right">
			

          <div class="form-group">       	
			<label class="col-sm-4"></label>	
			<div class="input-group col-sm-6">
			<input type=submit name="invoice" value="Save" class="btn btn-primary btn-lg btn-block">
			</div>
     
          </div>

		
		</div>


      	</div> <!--box body -->
      	</form>
        </div>
     </div>
    </div>




  </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php include ("footer_invoice.php"); ?>