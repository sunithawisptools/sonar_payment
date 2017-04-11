<?php

require 'vendor/autoload.php';
require 'config.php';

$sum_array=array();
$date=date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date .' -1 day'));
$from_date=$prev_date.' 00:00:00';
$to_date=$prev_date.' 23:59:59';
$trans_data=pagination($from_date,$to_date);
foreach($trans_data as $temp)
  {
    if($temp)
      {
        $sum_array[$temp->type]=0;
      }
  }
foreach($trans_data as $temp)
  {
    if($temp)
      {
        echo $temp->amount;echo "</br>";
        $sum_array[$temp->type]=$sum_array[$temp->type]+$temp->amount; 
      }
  }
echo "</br>";
foreach ($sum_array as $key => $value) 
  {
    echo $key."_SUM: ".$value;
  }
        
function pagination($from_date,$to_date)
   {
     $trans_array=array();
     $size=1;
     $page=1;
     $data=complexSearch($size,$page,$from_date,$to_date);
     $data_values=$data->results;
     $page_values=$data->paginator;
     $total_page=$page_values->total_pages;
     foreach($data_values as $temp)
        {
            $trans_array[]=$temp;
        }
     if($total_page>1)
        {
            for($i=2;$i<=$total_page;$i++)
            {
                $data=complexSearch($size,$i,$from_date,$to_date);
                $data_values=$data->results;
                foreach($data_values as $temp)
                 {
                   $trans_array[]=$temp;
                 }
            }
        }
        
        return $trans_array;
 }
 function complexSearch($size,$page,$from_date,$to_date)
 {
     $url = $config['url'];
     $username = $config['username'];
     $password = $config['password'];
     $client = new GuzzleHttp\Client();
     $response = $client->request('POST', $url, array(
       'auth' => array($username, $password),
       'json' => array(
         'size' => $size,
         'page' => $page,
         'search'=> array(
           "date_limits" => array(
  	     array(
	       "field" => "datetime",
               "from" => $from_date,
               "to" => $to_date
             )
           )
      	 )
       )   
     ));

   $body = json_decode($response->getBody());
   return $body->data;
}

?>
