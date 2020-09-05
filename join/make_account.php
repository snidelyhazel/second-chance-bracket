<?php
include("../includes/SCB_connect.php");

if ($db === FALSE)
{
    echo "<p>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_errno() . ": " . mysqli_error() . "</p>";
}
else
{
  $table = "users";

  $query_result = mysqli_query($db, "SHOW TABLES LIKE '$table'");

  if (mysqli_num_rows($query_result) == 0)
  {
    $SQLstring = "CREATE TABLE IF NOT EXISTS $table (`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT, `username` VARCHAR(50) NOT NULL, `display_name` VARCHAR(50), `email` VARCHAR(50) NOT NULL, `password` VARCHAR(255) NOT NULL)";
    $query_result = mysqli_query($db, $SQLstring);
    if ($query_result === FALSE)
    {
      error_log("<p>Unable to create the table.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
    }
    else
    {
      $query_result = mysqli_query($db, "CREATE UNIQUE INDEX `users_by_username` ON " . $table . "(`username`);");
      if ($query_result === FALSE)
      {
        error_log("<p>Unable to create the index.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
      }
      $query_result = mysqli_query($db, "CREATE UNIQUE INDEX `users_by_email` ON " . $table . "(`email`);");
      if ($query_result === FALSE)
      {
        error_log("<p>Unable to create the index.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
      }
    }
  }

  $username = $_POST["username"];
  $password = $_POST["password"];
  $email = $_POST["email"];
  $password_hashed = password_hash($password, PASSWORD_DEFAULT);

  $query_result = mysqli_query($db, "SELECT `username` FROM " . $table . " WHERE `username` = '" . mysqli_real_escape_string($db, $username) . "';");
  $username_taken = (mysqli_num_rows($query_result) != 0);

  $query_result = mysqli_query($db, "SELECT `email` FROM " . $table . " WHERE `email` = '" . mysqli_real_escape_string($db, $email) . "';");
  $email_taken = (mysqli_num_rows($query_result) != 0);

  // if ($username_taken)
  // {
  //   echo "<p>Username already exists.</p>";
  // }
  // if ($email_taken)
  // {
  //   echo "<p>Email already exists.</p>";
  // }

  $account_created = false;

  if (!$username_taken && !$email_taken)
  {
    $query_result = mysqli_query($db, "INSERT INTO $table (username, email, password) VALUES ('" . mysqli_real_escape_string($db, $username) . "', '" . mysqli_real_escape_string($db, $email) . "', '" . mysqli_real_escape_string($db, $password_hashed) . "');");

    if ($query_result === FALSE)
    {
      error_log("<p>Unable to create account.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
    }
    else
    {
      $account_created = true;

      $_SESSION['user_id'] = mysqli_insert_id($db);
      $_SESSION['username'] = $username;
      $_SESSION['email'] = $email;
    }
  }

  $response_data = array("username_taken"=>$username_taken, "email_taken"=>$email_taken, "account_created"=>$account_created);

  echo json_encode($response_data);


}
?>
