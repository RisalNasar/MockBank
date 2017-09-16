# MockBank
A PHP web application that is created in-accordance with the OWASP Application Security Verification Standards (ASVS), to learn concepts from the same.

This version of the MockBank is created in PHP, using the Netbeans IDE.  

![Netbeans IDE](/images/netbeans_ide.jpg)

The backend database is MySQL and the webserver is Apache HTTPD.  The application was operated using [XAMPP](https://www.apachefriends.org/index.html).

![XAMPP](/images/xampp.jpg)


The application allows the following functionalities:
**
1. Register an account
1. Login a registered user
1. View account details
1. Make a transaction
1. Change account details
1. Change password
1. Logout
**

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

`security_log(), transaction_log() and changedetails_log()` functions are defined within `logging.php` for this purpose and it records the necessary information when security/transaction/change-in-account-details relevant events take place.

### 2.13 Verify that account passwords are one way hashed with a salt, and there is sufficient work factor to defeat

A hash is generated from the password that was input by the user using BCrypt algorithm.  The hash includes a randomly generated salt as well.
```php
$hash = password_hash($password, PASSWORD_BCRYPT);
```

The password hash is verified by calling the below function:
```php
password_verify($password, $hash)
```

### 2.16 Verify that credentials are transported using a suitable encrypted link and that all pages/functions that require a user to enter credentials are done so using an encrypted link.

This is implemented by ensuring that HTTPS mode is supported by the Apache HTTPD


