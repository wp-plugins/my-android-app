<div class="wrap">
	<div id="icon-edit" class="icon32"></div><h2><?php _e('My Android App'); ?></h2>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
	<div id="side-info-column" class="inner-sidebar">
	
	<div class="postbox">
		<h3>My Android App</h3>
                <div class="inside">We provide you with inexpensive android app for your wordpress blog for one time charge of <strong>$25</strong> only. There are no recurring monthly or annual charges.<br /><a href="http://myandroidapp.ahsan.pk">Click here</a> for more details.
			<form action="https://secure.payza.com/checkout" method="post"><input name="ap_productid" type="hidden" value="SsWeiG4T6deNPmQ73eI2dQ==" />
<input name="ap_quantity" type="hidden" value="1" />
<input name="ap_image" src="https://secure.payza.com/PayNow/D3982AD2F2BD4DF58B28AF37815E3CDAb0en.gif" type="image" /></form>
</div></div> 
	

<div class="postbox">
		<h3>Support</h3>
		<div class="inside">Need any help? <br /> Want a new feature? <br /> Please feel free to contact on 
			alpha.dev@hotmail.com </div></div>
</div>


		<div id="post-body-content">

<div class="postbox">
		<h3>Settings</h3>
		<div class="inside">


<form action="options.php" method="post">

<?php settings_fields( 'myandroidapp_options' ); ?>

<?php $options = get_option('myandroidapp_plugin_options'); ?>

<table width="600" style="margin:20px">

  <tr>

    <td align="left" valign="top" width="150" >
      <p style="font-size:10pt;">
          <b> <?php _e('Secret Key'); ?></b>
      </p>
   </td>
    <td><input type="text" name="myandroidapp_plugin_options[secretkey]" <?php if ($options['secretkey']) echo 'readonly="readonly"'; ?>  value="<?php echo $options['secretkey']; ?>" size="50" pattern=".{10,}"   required title="10 characters minimum" /> <p style="font-size:8pt;">
        <?php _e('Enter a random secret key. 10 characters minimum. This will be used to validate data sent from app. Once set it cannot be changed.'); ?>
      </p></td>

  </tr>
  <tr>

    <td align="left" valign="top" width="150" >
      <p style="font-size:10pt;">
        <?php _e('GCM Server Key'); ?>
      </p></td>

    <td><input type="text" name="myandroidapp_plugin_options[gcmkey]"  value="<?php echo $options['gcmkey']; ?>" size="50" /></td>

  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td><input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	  </td>
  </tr>
</table>
    <?php
    global $wpdb;
    $devices_table = $wpdb->prefix . "devices";
    
    if (isset($_POST['message'])){
        
         $msg= trim($_POST['message']);
       
        if ($options['gcmkey']==''){
            $result = "Please set GCM Server Key first.";
        }
        elseif ($msg == '') {
            $result= "You cannot send an empty message.";
        }
        else
        {
             include_once 'GCM.php';
             $url=get_site_url();
             $registatration_ids = array();
             
            $ids = $wpdb->get_results( "SELECT device_id FROM $devices_table" );
            if ($ids == NULL){
                $result = 'No Registered devices';
            }
            else
            {
                foreach ($ids as $reg_id){
                  //  $ids =$id['device_id'];
                    $registration_ids [] = $reg_id->device_id;
                }
                $gcm = new GCM();
             $message = array( "url" => $url,
                    "message" => $msg );
             $result = $gcm->send_notification($registration_ids, $message);
            }
        }
        
    
    }
        
    
    ?>
</form>
                    <hr> <br>
                    <form method="POST">
                        
                        <table >
                            
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td><?php if ($result) echo "<div class='update-nag'>$result</div>"; ?></td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top" width="150"><b>Send Push Notification to all devices</b></td>
                                    <td><textarea name="message" rows="4" cols="50"></textarea></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input class="button-primary" type="submit" value="Send" /></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        
                    </form>
<hr>
<?php

$total_devices = $wpdb->get_var( "SELECT COUNT(*) FROM $devices_table" );
echo "<b>Total Devices:</b> $total_devices";
?>

</div> </div>
	