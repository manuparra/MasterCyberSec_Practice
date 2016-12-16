<?php

  if (isset($_POST['user']) && isset($_POST['password'])) {
        
        //LDAP USER LOGIN DATA
        $user=$_POST['user'];
        $password=$_POST['password'];

        //LDAP SETUP CONFIGURATION
        $host= '192.168.10.30';
        $port= 14XXX; //LDAP port // SSL: 698
        $dn="cn=admin,dc=ugr,dc=es"; //change your DN
        $pwd_admin="password"; // your admin password

        $conn = ldap_connect( "ldap://".$host,$port) ;


        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);        

        //Matching DN admin and password
        $bind = ldap_bind( $conn, $dn, $pwd_admin);
        

        if ($bind){
            $newbind = ldap_bind( $conn, "cn=".$user.",ou=Users,dc=ugr,dc=es", $password );
            if ($newbind) {
               echo "USER: ".$user." Authenticated";
            }
            else {
               echo "USER: ".$user." NOT Authenticated";
            }
        }



}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Login</title>

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->    
  </head>

  <body>


    <div style="width:40%" class="container">
      <H1> CONTAINER AUTH LDAP </H1>
      <form class="form-signin" method="post" action="./authentication.php">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">User</label>
        <input type="input" name="user" id="inputEmail" class="form-control" placeholder="User" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->    
  </body>
</html>
