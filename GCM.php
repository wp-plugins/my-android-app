<?php
 
class GCM {
 
    //put your code here
    // constructor
    function __construct() {
         
    }
 
    /**
     * Sending Push Notification
     */
    public function send_notification($registration_ids, $message) {
        // get API key from options
        $options = get_option('myandroidapp_plugin_options');
        define("GOOGLE_API_KEY", $options['gcmkey']);
 
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
        
        $groups = array_chunk($registration_ids, 1000);
        $success = 0;
        $failure = 0;
        
        foreach($groups as $group) {
        $fields = array(
            'registration_ids' => $group,
            'data' => $message
        );
 
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        
        $data = json_decode($result, true);
            
            $success = $success + $data["success"];
            $failure = $failure + $data["failure"];
            $total = count($registration_ids);
 
        // Close connection
        curl_close($ch);
        
    }
    
    return "<b>Total Devices : </b> $total <br /> <b>Success :</b> $success <br /> <b>Failure :</b> $failure<br />";
    }
 
}
?>