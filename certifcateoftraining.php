 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Training Acceptance</title>
    <link rel="stylesheet" href="mystylesheet.css">
</head>
<body>
    <h2>Certificate of Training Acceptance</h2>
<form action="trainingsuccess.php" method="post">
          <input type="hidden" name="orderid" value="<?php echo urldecode($_GET['orderid']); ?>">
          <input type="hidden" name="COTInitiationDate" value="<?php echo urldecode($_GET['COTInitiationDate']); ?>">
          <input type="hidden" name="useremail" value="<?php echo urldecode($_GET['useremail']); ?>">
          <input type="hidden" name="dnotes" value="<?php echo urldecode($_GET['dnotes']); ?>">
          <input type="hidden" name="COTTrainingType" value="<?php echo urldecode($_GET['COTTrainingType']); ?>">
  <div class='field'>
    <label>Dealer Name</label>
    <input name="dealer" value="<?php echo urldecode($_GET['dealer']); ?>" readonly>
  </div>
  <div class='field'>
        <label>Type of Training Delivered</label>
    <input name="trainingtype" value="<?php echo urldecode($_GET['trainingtype']); ?>" readonly>
  </div>
    <div class='field'>
            <label>Method Of Delivery</label>
    <input name="delmethod" value="<?php echo urldecode($_GET['delmethod']); ?>" readonly>
  </div>
    <div class='field'>
            <label>Training complete on:</label>
    <input name="completedon" value="<?php echo urldecode($_GET['completedon']); ?>" readonly>
  </div>
      <div class='field'>
            <label>Consultant Name</label>
    <input name="cname" value="<?php echo urldecode($_GET['cname']); ?>" readonly>
  </div>
        <div class='field'>
            <label>Products Trained On</label>
<textarea id="prodtrain" name="trainedon" readonly>
  <?php
  $products = urldecode($_GET['trainedon']);
  $products = str_replace(',', ','."\r\n"." ", $products);
  echo $products;?>
</textarea>  
</div>
      <div class='field'>
            <label>Notes for Dealer Signatory</label>
            <span name="dnotes" class="text-box" readonly> <?php echo urldecode($_GET['dnotes']); ?></span>
       </div>
      <div class='field'>
            <label>Name of Dealer Signatory</label>
    <input name="dsign" value="<?php echo urldecode($_GET['dsign']); ?>" readonly>
  </div>
    <div class='field'>
            <label>Email of Dealer Signatory</label>
    <input type='email' name="demail" value="<?php echo urldecode($_GET['demail']); ?>">
  </div>
      <div class='field'>
    <input placeholder='Enter your Full Name here and press SUBMIT to acknowledge training completion' name="ack" required="required">
                <label>Acknowledgement Signature</label>
  </div>
  <div class='container'>
    <button type='submit' name='submit' id="button">Submit</button>
  </div>
</form>
</body>
</html>