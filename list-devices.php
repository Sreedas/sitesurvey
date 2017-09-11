<?php


include("includes/sql-config.inc.php");
$params=array();

$query = "SELECT device_id,hostname FROM devices order by device_id";

$all_devices = dbFetchRows($query, $params);

$new_hostname_array = array();


if(count($all_devices)>0){

   foreach($all_devices as $val){


      $host_name = $val['hostname'];

      $host_name_data = explode('-',$host_name);

      $hostname_part1 = $host_name_data[0];

      $hostname_part2 = $host_name_data[1];

      $hostname_part3 = $host_name_data[2];




      if((strstr($hostname_part3,$hostname_part1)!="") && !check_word($host_name)){
        $new_hostname_array[] = $val['device_id'];
      }


      if((substr($host_name,0,6)=='sector')){
        $new_hostname_array[] = $val['device_id'];
      }

      if((strstr($host_name,'bh.gp')) && !check_word($host_name)){
	$new_hostname_array[] = $val['device_id'];
      }

   }

   $display_data = '';

   $display_data .= '<table border="1" align="center">';
   $display_data .= '<tr><th></th><th>Site name</th><th>Province</th><th>Name</th><th>Tx/Rx</th></tr>';



   if(count($new_hostname_array)>0){


       foreach($new_hostname_array as $key=>$device_id){

           $site_name = '';

           $province = '';

           $name = '';

           $tx_rx = '';

           $query = "SELECT hostname FROM devices where device_id=".$device_id;

           $device_data = dbFetchRows($query, $params);

           //echo '<pre>'.print_r($device_data);

           $host_name = $device_data[0]['hostname'];

           $site_name = $device_data[0]['hostname'];

           $name = $device_data[0]['hostname'];


           if(strpos($name,'.gp.')){
               $province = 'Gauteng';
               $site_name_data = explode('.gp.', $site_name);

           }
           else if(strpos($name,'.nw.')){
               $province = 'North West';
               $site_name_data = explode('.nw.', $site_name);
           }
           else if(strpos($name,'.kzn.')){
               $province = 'Kwazul Natal';
               $site_name_data = explode('.kzn.', $site_name);
           }
           else if(strpos($name,'.wp.')){
               $province = 'Westrn Cape';
               $site_name_data = explode('.wp.', $site_name);
           }
           else if(strpos($name,'.pe.')){
               $province = 'Port Elizabeth';
               $site_name_data = explode('.pe.', $site_name);
           }
          //  else if(strpos($name,'.mp.')){
          //      $province = 'Mpumlanga';
          //      $site_name_data = explode('.mp.', $site_name);
          //  }

           $first_data = $site_name_data[0];
           $site_data = explode('.', $first_data);
           $site_name = end($site_data);

           $sitename_change_array = array('bh','vx','mt','mf','tr','arc','cmh','eoh','mfn','cs','lc','zn','sc','ld','pcm','ft','cw','ic','ucs');
           if(in_array($site_name,$sitename_change_array)){

               $host_name_data = explode('-',$host_name);
               $site_name = $host_name_data[0];
           }

          //  $sitename_change_arraytwo = array('mono');
          //  if(in_array($site_name,$sitename_change_arraytwo)){
           //
          //      $host_name_data = explode('.',$host_name);
          //      $site_name = $host_name_data[1];
          //  }


           $query = "SELECT radio_rx_freq,radio_tx_freq FROM p2p_radios where device_id=".$device_id;

           $radio_data = dbFetchRows($query, $params);

           if(!empty($radio_data[0]['radio_tx_freq']) && !empty($radio_data[0]['radio_rx_freq'])){

           $tx_rx = $radio_data[0]['radio_tx_freq'].'/'.$radio_data[0]['radio_rx_freq'];

           }

           $display_data .= '<tr>';

           $display_data .= '<td>'.($key+1).'</td>';

           $display_data .= '<td>'.$site_name.'</td>';

           $display_data .= '<td>'.$province.'</td>';

           $display_data .= '<td>'.$name.'</td>';

           $display_data .= '<td>'.$tx_rx.'</td>';

           $display_data .= '</tr>';
       }
   }

   $display_data .= '</table>';

   echo $display_data;

}

function check_word($string){

    $unwanted_hostnames = array('mtgpo','rnbb','vtb','mtbb','ce.');
    foreach ($unwanted_hostnames as $item) {
      if(strpos($string, $item)===0) {
         return true;
      }
   }

   return false;

}




// EOF
