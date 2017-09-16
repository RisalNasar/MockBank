# MockBank
A PHP web application that is created in-accordance with the OWASP Application Security Verification Standards (ASVS), to learn concepts from the same.

This version of the MockBank is created in PHP, using the Netbeans IDE.  

![Netbeans IDE](/images/netbeans_ide.jpg)

The backend database is MySQL and the webserver is Apache HTTPD.  The application is operated using [XAMPP](https://www.apachefriends.org/index.html).

![XAMPP](/images/xampp.jpg)


The application allows the following functionalities:
<b>
1. Register an account
1. Login a registered user
1. View account details
1. Make a transaction
1. Change account details
1. Change password
1. Logout
</b>

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

This is implemented by ensuring that HTTPS mode is supported by the Apache HTTPD webserver.  Even if a user tries to access the webpage over HTTP, the web server is configured to redirect the user to the HTTPS webpage.

`.htaccess` file at the Web Root folder should contain the below contents, to redirect HTTP traffic to their HTTPS equivalent:
```
RewriteEngine On
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 2.18 Verify that information enumeration is not possible via login, password reset, or forgot account functionality.

`register.php` is the main page susceptible to a user enumeration attack.  A brute force attack is thwarted by ensuring that a Captcha input is required at the page.


### 2.20 Verify that anti-automation is in place to prevent breached credential testing, brute forcing, and account lockout attacks.

`register.php` is the main page susceptible to a user enumeration attack.  A brute force attack is thwarted by ensuring that a Captcha input is required at the page.


### 3.1 Verify that there is no custom session manager, or that the custom session manager is resistant against all common session management attacks.

PHP's session management is used.  Session management functions are defined in `session.php`.

Following specific secure configurations where set in `php.ini`.

```
; Whether to use cookies.
; http://php.net/session.use-cookies
session.use_cookies=1

; http://php.net/session.cookie-secure
session.cookie_secure=1

; This option forces PHP to fetch and use a cookie for storing and maintaining
; the session id. We encourage this operation as it's very helpful in combating
; session hijacking when not specifying and managing your own session id. It is
; not the be-all and end-all of session hijacking defense, but it's a good start.
; http://php.net/session.use-only-cookies
session.use_only_cookies=1

; Name of the session (used as cookie name).
; http://php.net/session.name
session.name=SESSID

; Initialize session on request startup.
; http://php.net/session.auto-start
session.auto_start=0

; Whether or not to add the httpOnly flag to the cookie, which makes it inaccessible to browser scripting languages such as JavaScript.
; http://php.net/session.cookie-httponly
session.cookie_httponly=1

; Select a hash function for use in generating session ids.
; Possible Values
;   0  (MD5 128 bits)
;   1  (SHA-1 160 bits)
; This option may also be set to the name of any hash function supported by
; the hash extension. A list of available hashes is returned by the hash_algos()
; function.
; http://php.net/session.hash-function
session.hash_function=12

; Define how many bits are stored in each character when converting
; the binary hash data to something readable.
; Possible values:
;   4  (4 bits: 0-9, a-f)
;   5  (5 bits: 0-9, a-v)
;   6  (6 bits: 0-9, a-z, A-Z, "-", ",")
; Default Value: 4
; Development Value: 5
; Production Value: 5
; http://php.net/session.hash-bits-per-character
session.hash_bits_per_character=5

```

### 3.2 Verify that sessions are invalidated when the user logs out.

Achieved through `logout.php` and through inactive time based logout, defined in `session.php`.

### 3.3 Verify that sessions timeout after a specified period of inactivity.

Inactive time out is defined in `session.php`.

### 3.4 Verify that sessions timeout after an administratively-configurable maximum time period regardless of activity (an absolute timeout).

An abolute timeout is defined in `session.php`.

### 3.5 Verify that all pages that require authentication have easy and visible access to logout functionality.

`logout.php` is readily accessible from all pages of the web application.

### 3.6 Verify that the session id is never disclosed in URLs, error messages, or logs. This includes verifying that the application does not support URL rewriting of session cookies.

All data transmitted is through the POST format (as opposed to GET).  Session IDs are not included in URLs.

### 3.7 Verify that all successful authentication and re-authentication generates a new session and session id.

Authentication and Re-authentication results in a new Session ID.  Defined in `session.php`.

### 3.10 Verify that only session ids generated by the application framework are recognized as active by the application.

`php.ini` file is located at `C:\xampp\php\php.ini`.

Within `php.ini`, enable strict mode for sessions:

```
; Whether to use strict session mode.
; Strict session mode does not accept uninitialized session ID and regenerate
; session ID if browser sends uninitialized session ID. Strict mode protects
; applications from session fixation via session adoption vulnerability. It is
; disabled by default for maximum compatibility, but enabling it is encouraged.
; https://wiki.php.net/rfc/strict_sessions
session.use_strict_mode=1
```








