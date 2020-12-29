 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Success Form</title>
    <link rel="stylesheet" href="mystylesheet.css">
</head>
<body>
<div class="success-msg">
  <i class="fa fa-check"></i>
  Your Response has been Submitted
</div>
</body>
</html>

<?php

define("CLIENT_ID", "Please fill your CLIENT_ID HERE");
define("CLIENT_SECRET", "Please fill your CLIENT_SECRET HERE");
define("LOGIN_URI", "https://ap2.salesforce.com");
define("USER_URI", "https://{YOUR_ADDRESS}.my.salesforce.com");
define("SF_USER", "Please fill your USERNAME HERE");
define("SF_PWD", "Please fill your PASSWORD HERE");
define("SECURITY_TOKEN", "Please fill your SECURITY_TOKEN HERE");  

$loginurl = LOGIN_URI."/services/oauth2/token";

$params = "grant_type=password"
. "&client_id=" . CLIENT_ID
. "&client_secret=" . CLIENT_SECRET
. "&username=" . SF_USER
. "&password=" . SF_PWD . SECURITY_TOKEN;

$curl = curl_init($loginurl);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

$json_response = curl_exec($curl);
$token_request_data = json_decode($json_response, true);

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
}

if (empty($token_request_data))
    die("Couldn't decode '$token_request_data' as a JSON object");

if (!isset($token_request_data['access_token'])||
    !isset($token_request_data['instance_url']))
{
    sendfailuremail($order_uuid,$dealer);
    die("Missing expected data from ".print_r($token_request_data, true));
  }

curl_close($curl);


$order_uuid = $_POST['orderid'];
$dealer = $_POST['dealer'];

$initated_COT_date = $_POST['COTInitiationDate'];
$COTTrainingType = $_POST['COTTrainingType'];
$products = $_POST['trainedon'];
$products = str_replace(',', ','."\r\n"." ", $products);


function sendfailuremail($order_uuid,$dealer){
$to = $useremail;;
$subject = 'COT updation failed in salesforce for OrderID : '.$order_uuid;
$body = 'COT updation failed for dealer --> '.$dealer.' and order -->'.$order_uuid.'  .... Please check in salesforce ';
$headers = array('Content-Type: text/html; charset=UTF-8');
 
wp_mail( $to, $subject, $body, $headers );
echo "Update failed on Server due to Technical Error, Please contact Support TEAM...";
}

function sendsuccessemail($order_uuid,$dealer,$products,$useremail){
$to = $useremail;
$subject = 'COT Submitted for OrderID : '.$order_uuid;
$body = 'COT Submitted for dealer --> '.$dealer.' , order -->'.$order_uuid.' for products -->'.$products;
$headers = array('Content-Type: text/html; charset=UTF-8'); 
wp_mail( $to, $subject, $body, $headers );
}
// $query = "SELECT Id FROM Order WHERE OrderNumber='" . $orderid . "'";
// $queryurl = USER_URI."/services/data/v49.0/query?q=". urlencode($query);
// $curl = curl_init($queryurl);
//     curl_setopt($curl, CURLOPT_HEADER, false);
//     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($curl, CURLOPT_HTTPHEADER,
//             array("Authorization: Bearer ".$token_request_data['access_token']));

//     $json_response = curl_exec($curl);
//     $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//     if ( $status != 200 ) {
//       die("<br />Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
//     }

//     curl_close($curl);

//     $response = json_decode($json_response, true);

//     $total_size = $response['totalSize'];
    
//     if ($total_size > 1) {
//       foreach ((array) $response['records'] as $record) {
//          echo $record['Id'] . "<br/>";
//       }
//     }
//     elseif ($total_size == 1) {
//         $order_uuid = $response['records'][0]['Id'];
//     }

$orderurl = USER_URI."/services/data/v49.0/sobjects/Order/".$order_uuid."/OrderItems";
$curl = curl_init($orderurl);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: Bearer ".$token_request_data['access_token'],
                "charset: UTF-8",
                  "Content-Type: application/json"));

    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ( $status != 200 ) {
      sendfailuremail($order_uuid,$dealer);
      die("<br />Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }

    curl_close($curl);
    
    $itemsIdList = array();
    $response = json_decode($json_response, true);
      foreach ((array) $response['records'] as $record) {
            if($record['COT_Initiated__c'] == true and $record['Activate__c'] == false and $record['COT_Initiation_Date__c'] == $initated_COT_date){
            $id = $record['Id'];
            array_push($itemsIdList, $id);
      }  
         
  }

  $traindate = date('Y-m-d'); 
  $signedemail = $_POST['demail']; 
  $dealernotes = $_POST['dnotes']; 
  $useremail = $_POST['useremail']; 


  $updatedata = array(
    'COT_Signed_Date__c' => $traindate,
    'COT_Signed_By__c' => $signedemail,
    'COT_Notes_for_Customer__c' => $dealernotes,
    'Training_Type__c' => $COTTrainingType

);
$updatepayload = json_encode($updatedata);

foreach ( $itemsIdList as $items) {
$updateItemsurl = USER_URI."/services/data/v49.0/sobjects/OrderItem/".$items;
$curl = curl_init($updateItemsurl);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $updatepayload);

    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: Bearer ".$token_request_data['access_token'],
                "charset: UTF-8",
                  "Content-Type: application/json"));

    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
     if ( $status != 204 ) {
      sendfailuremail($order_uuid,$dealer);
      die("<br />Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }
    curl_close($curl);
}
sendsuccessemail($order_uuid,$dealer,$products,$useremail);