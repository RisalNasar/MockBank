<?php


    date_default_timezone_set('Asia/Singapore');
    
    //print_r(date("F j, Y, g:i a e O"));
    // May 25, 2017, 7:52 am Asia/Singapore +0800

    //https://stackoverflow.com/questions/7655332/equivalent-of-php-error-log-for-info-logs


    $sourceip = $_SERVER['REMOTE_ADDR'];


    function security_log($userid, $sourceip, $logsource, $logcategory, $eventcategory, $event) {
        
        $logfile = 'C:\logs\mockbank\security_log.txt';

        if (!isset($object)) 
            $object = new stdClass();

        $object->timestamp = date("F j, Y, g:i a e O");
        $object->userid = $userid;
        $object->sourceip = $sourceip;
        $object->logsource = $logsource;
        $object->logcategory = $logcategory;
        $object->eventcategory = $eventcategory;
        $object->event = $event;

        $json_message_string = json_encode($object);
        $json_message_string = $json_message_string . ",\n";


        error_log($json_message_string, 3,  $logfile); 
    }

    function transaction_log($userid, $sourceip, $logsource, $logcategory, $eventcategory, $recepientAccountNumber, $amount) {
        
        $logfile = 'C:\logs\mockbank\transaction_log.txt';

        if (!isset($object)) 
            $object = new stdClass();

        $object->timestamp = date("F j, Y, g:i a e O");
        $object->userid = $userid;
        $object->sourceip = $sourceip;
        $object->logsource = $logsource;
        $object->logcategory = $logcategory;
        $object->eventcategory = $eventcategory;
        $object->recepientAccountNumber = $recepientAccountNumber;
        $object->amount = $amount;

        $json_message_string = json_encode($object);
        $json_message_string = $json_message_string . ",\n";

        error_log($json_message_string, 3,  $logfile); 
    }

    function changedetails_log($userid, $sourceip, $logsource, $logcategory, $eventcategory, $name, $email, $address, $newuserid) {
        
        $logfile = 'C:\logs\mockbank\changedetails_log.txt';

        if (!isset($object)) 
            $object = new stdClass();

        $object->timestamp = date("F j, Y, g:i a e O");
        $object->userid = $userid;
        $object->sourceip = $sourceip;
        $object->logsource = $logsource;
        $object->logcategory = $logcategory;
        $object->eventcategory = $eventcategory;
        $object->name = $name;
        $object->email = $email;
        $object->address = $address;
        $object->newuserid = $newuserid;

        $json_message_string = json_encode($object);
        $json_message_string = $json_message_string . ",\n";

        error_log($json_message_string, 3,  $logfile); 
    }


    // https://stackoverflow.com/questions/1073164/write-to-error-log-with-local-time-information-and-a-line-break-at-the-end

    // Logging: Splunk Best Practices: http://dev.splunk.com/view/logging/SP-CAAAFCK

?>