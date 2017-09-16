# MockBank
A PHP web application that is created in-accordance with the OWASP Application Security Verification Standards (ASVS), to learn concepts from the same.

This version of the MockBank is created in PHP, using the Netbeans IDE.  The backend database is MySQL.  The application was operated using [XAMPP](https://www.apachefriends.org/index.html).

![Netbeans IDE](/images/netbeans_ide.jpg)

The application allows the following functionalities:
1. Register an account
1. Login a registered user
1. View account details
1. Make a transaction
1. Change account details
1. Change password
1. Logout

**Login Page**
![MockBank Index](/images/mockbank_index.jpg)



**Registration Page**
![Registration Page](/images/registration.jpg)



**Account Page**
![Account Page](/images/account.jpg)


# OWASP Application Security Verification Standards (ASVS)

[OWASP ASVS](https://www.owasp.org/index.php/Category:OWASP_Application_Security_Verification_Standard_Project) can be used during the Development / Testing phase of a project to ensure that the program complies with Secure Code Design and Development Principles.

Detailed below are selected OWASP ASVS Requirements and how MockBank complies with the requirement.

### 2.1 Verify all pages and resources by default require authentication except those specifically intended to be public (Principle of complete mediation).

This is verified by ensuring that the `session.php` file is included in every page that requires authentication prior to access.  The `session.php` file verifies that the current session is a valid and authenticated session which has not been timedout yet.  If it is the contrary, `session.php` redirects the user to the login page.

### 2.4 Verify all authentication controls are enforced on the server side.

Authentication information is sent to the server-side `index.php` file through a POST submit request for verification.  Critical verificaitons are not performed on the client-side.

### 2.9 Verify that the changing password functionality includes the old password, the new password, and a password confirmation.

**Change Password Page**
![Change Password Page](/images/change_password.jpg)


### 2.12 Verify that all authentication decisions can be logged, without storing sensitive session identifiers or passwords. This should include requests with relevant metadata needed for security investigations.

A `security_log()` function is defined for this purpose and it records the necessary information when security relevant events take place.

```php
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
```






