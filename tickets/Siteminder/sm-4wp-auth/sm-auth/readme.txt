Notes for enabling SiteMinder authentication for WordPress
----------------------------------------------------------


1.  The SiteMinder agent should be installed on your server.  Please contact Enterpriseauth@jhmi.edu if you need assistance with this.  You should request that the JHED email address, JHED givenname (i.e. first name), and JHED sn (i.e. last name) be provided to your server as available attributes when people login.


2.  Please also configure the SiteMinder agent so that a LogoffUri value is defined (e.g. "/bin/logout").


3.  Copy the "logout" script to a location on your server that does not require SiteMinder authentication ... and which is accessible via the path defined above (e.g. "http://myserver.jhu.edu/bin/logout").  Please note that you may need to have perl and the CGI module on your server in order for this script to work.


4.  Copy the wp-logout.php script to a location on your server that does not require SiteMinder authentication.  You may place this script in the same folder as "logout" ... in which case the URL would likely be http://myserver.jhu.edu/bin/wp-logout.php.


5.  Copy the wp-login.php script to a location on your server that *does* require SiteMinder authentication (which was hopefully defined as part of step 1 above).  For example, if the "/protected" path is configured to require SiteMinder authentication, you might place the script so that it is available as http://myserver.jhu.edu/protected/wp-login.php.


6.  During SiteMinder authentication of a protected location on your server (e.g. wp-login.php hopefully), environment variables are set with useful information such as the JHED id of the person who has logged in.  If the environment variables use different names other than SM_USER for the JHED id, JHED_mail for the email address, JHED_givenname for the first name, and JHED_sn for the last name -- then you should update wp-login.php accordingly with the correct values to use for your server.


7.  Please make sure that you have a user account in your WordPress site that corresponds to your JHED id -- and which has administrator level access to the site.


8.  Install the http-authentication plugin in your WordPress installation.  For a multi-site installation, this plugin should be enabled separately for each site using it (i.e. please do not network-enable the plugin).


8.  Under Settings->HTTP Authentication, please enter the appropriate values for the Login URI and for the Logout URI.  Using our values mentioned above, we might then have:

Login URI:   http://myserver.jhu.edu/protected/wp-login.php?dest=%redirect_encoded%
Logout URI:  http://myserver.jhu.edu/bin/wp-logout.php?dest=%redirect_encoded%



9.  Once the settings are saved, and if everything is configured correctly, JHED authentication should then be enabled for your WordPress site.


