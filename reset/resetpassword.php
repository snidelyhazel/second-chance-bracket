<?php
include("../includes/SCB_connect.php");

if ($db === FALSE)
{
    echo "<p>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_errno() . ": " . mysqli_error() . "</p>";
}
else
{
  $reset_successfully = false;

  $key = $_POST["key"];
  $email = $_POST["email"];
  $new_password = $_POST["password"];

  $curDate = date("Y-m-d H:i:s");
  $query_result = mysqli_query($db, "SELECT * FROM `password_reset_temp` WHERE `key`='" . mysqli_real_escape_string($db, $key) . "' and `email`='" . mysqli_real_escape_string($db, $email) . "';");
  if (mysqli_num_rows($query_result) != 0)
  {
    $row = $query_result->fetch_assoc();

    $expDate = $row['expDate'];
    if ($expDate >= $curDate)
    {
      $password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
      mysqli_query($db, "UPDATE `users` SET `password`='" . $password_hashed . "' WHERE `email`='" . mysqli_real_escape_string($db, $email) . "';");

      $query_result = mysqli_query($db, "SELECT `id`, `username`, `email` FROM users WHERE `email` = '" . mysqli_real_escape_string($db, $email) . "';");
      if (mysqli_num_rows($query_result) != 0)
      {
        $row = $query_result->fetch_assoc();

        $_SESSION['user_id'] = $row["id"];
        $_SESSION['username'] = $row["username"];
        $_SESSION['email'] = $row["email"];
        $reset_successfully = true;
      }
    }

    mysqli_query($db,"DELETE FROM `password_reset_temp` WHERE `email`='" . $email . "';");
  }

  $response_data = array("reset_successfully"=>$reset_successfully);
  echo json_encode($response_data);
}
?>
