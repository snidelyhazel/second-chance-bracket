<?php
include("../includes/SCB_connect.php");

if ($db === FALSE)
{
    echo "<p>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_errno() . ": " . mysqli_error() . "</p>";
}
else
{
  $authenticated_successfully = false;

  $table = "users";

  $query_result = mysqli_query($db, "SHOW TABLES LIKE '$table'");

  if (mysqli_num_rows($query_result) == 0)
  {
    error_log($table . " table does not exist.");
  }
  else
  {
    $username_or_email = $_POST["username_or_email"];
    $password = $_POST["password"];

    $query_result = mysqli_query($db, "SELECT `id`, `username`, `email`, `password` FROM " . $table . " WHERE `username` = '" . mysqli_real_escape_string($db, $username_or_email) . "' OR `email` = '" . mysqli_real_escape_string($db, $username_or_email) . "';");

    if (mysqli_num_rows($query_result) != 0)
    {
      $row = $query_result->fetch_assoc();

      $hashedPassword = $row["password"];
      if (password_verify($password, $hashedPassword))
      {
        $_SESSION['user_id'] = $row["id"];
        $_SESSION['username'] = $row["username"];
        $_SESSION['email'] = $row["email"];
        $authenticated_successfully = true;
      }
    }
  }

  $response_data = array("authenticated_successfully"=>$authenticated_successfully);
  echo json_encode($response_data);
}
?>
