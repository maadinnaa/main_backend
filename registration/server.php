<?php
session_start();

// initializing variables
$username    = "";
$email       = "";
$first_name  = "";
$second_name = "";
$errors = array(); 

// connect to the database
$db = oci_pconnect("ecoeco", "qwerty123", "//localhost/xe");

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password_1 = $_POST['password_1'];
  $password_2 = $_POST['password_2'];
  $first_name = $_POST['first_name'];
  $second_name = $_POST['second_name'];

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT DISTINCT username, email FROM users WHERE username='$username' OR email='$email'";
  //echo $user_check_query;
  $result = oci_parse($db, $user_check_query);
  oci_execute($result);
  oci_fetch_all($result, $user);

  var_dump($user);

  if ($user) { // if user exists
    if ($user['USERNAME'][0] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['EMAIL'][0] === $email) {
      array_push($errors, "email already exists");
    }
  }

  echo 'hi';
  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database
    echo 'inserting for you a';
  	$query = "INSERT INTO users (id, username, first_name, second_name, email, password)
                VALUES(user_seq.NEXTVAL, '$username', '$first_name', '$second_name', '$email', '$password')";

    $result = oci_parse($db, $query);
    oci_execute($result, OCI_DEFAULT);

    $cquery = "SELECT * FROM users";
    echo $cquery;
    $result = oci_parse($db, $cquery);
    oci_execute($result);
    oci_fetch_all($result, $cq);

    //var_dump($cq);

  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
  else{
    echo 'sxff';
  }
}

// ... 
if (isset($_POST['login_user'])) {
    $username =  $_POST['username'];
    $password =  $_POST['password'];
  
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $password = md5($password);
        $query = "SELECT COUNT(*) FROM users WHERE username='$username' AND password='$password'";
        
        $results = oci_parse($db, $query);
        oci_execute($results);
        oci_fetch_all($results, $results);

        //var_dump($results);
        echo $results['COUNT(*)'][0];

        if ($results['COUNT(*)'][0] == 1) {
          $_SESSION['username'] = $username;
          $_SESSION['success'] = "You are now logged in";
          header('location: index.php');
        }else {
            array_push($errors, "Wrong username/password combination");
        }
    }
  }
  oci_close($db);
  ?>