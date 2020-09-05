<?php
include("../includes/SCB_connect.php");

if ($db === FALSE)
{
    echo "<p>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_errno() . ": " . mysqli_error() . "</p>";
}
else
{
  if(isset($_SESSION['user_id']))
  {
    // logged in!
    $user_id = $_SESSION['user_id'];


    $table = "brackets";

    $query_result = mysqli_query($db, "SHOW TABLES LIKE '$table'");

    if (mysqli_num_rows($query_result) == 0)
    {
      $SQLstring = "CREATE TABLE IF NOT EXISTS $table (`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT, `user_id` INT NOT NULL, `year` INT NOT NULL, `wcf_champ` VARCHAR(3) NOT NULL, `ecf_champ` VARCHAR(3) NOT NULL, `scf_champ` VARCHAR(3) NOT NULL, `scf_goals` INT NOT NULL, `wcf_games` INT NOT NULL, `ecf_games` INT NOT NULL)";
      $query_result = mysqli_query($db, $SQLstring);
      if ($query_result === FALSE)
      {
        error_log("<p>Unable to create the table.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
      }
      else
      {
        $query_result = mysqli_query($db, "CREATE UNIQUE INDEX `brackets_by_user_id_and_year` ON " . $table . "(`user_id`, `year`);");
        if ($query_result === FALSE)
        {
          error_log("<p>Unable to create the index.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
        }
      }
    }

    $year = date("Y");
    $wcf_champ = $_POST["wcf_champ"];
    $ecf_champ = $_POST["ecf_champ"];
    $scf_champ = $_POST["scf_champ"];
    $wcf_games = $_POST["wcf_games"];
    $ecf_games = $_POST["ecf_games"];
    $scf_goals = $_POST["scf_goals"];

    $query_result = mysqli_query($db, "SELECT `id` FROM " . $table . " WHERE `user_id` = " . $user_id . " AND `year` = " . $year . ";");

    if (mysqli_num_rows($query_result) != 0)
    {
      $row = $query_result->fetch_assoc();
      $bracket_id = $row["id"];

      $query_result = mysqli_query($db, "UPDATE " . $table . " SET `wcf_champ` = '" . $wcf_champ . "', `ecf_champ` = '" . $ecf_champ . "', `scf_champ` = '" . $scf_champ . "', `wcf_games` = '" . $wcf_games . "', `ecf_games` = '" . $ecf_games . "', `scf_goals` = '" . $scf_goals . "' WHERE id = " . $bracket_id . ";");
      if ($query_result === FALSE)
      {
        error_log("<p>Unable to update the bracket.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
      }
    }
    else
    {
      $query_result = mysqli_query($db, "INSERT INTO $table (user_id, year, wcf_champ, ecf_champ, scf_champ, wcf_games, ecf_games, scf_goals) VALUES (" . $user_id . ", " . $year . ", '" . mysqli_real_escape_string($db, $wcf_champ) . "', '" . mysqli_real_escape_string($db, $ecf_champ) . "', '" . mysqli_real_escape_string($db, $scf_champ) . "', '" . mysqli_real_escape_string($db, $wcf_games) . "', '" . mysqli_real_escape_string($db, $ecf_games) . "', '" . mysqli_real_escape_string($db, $scf_goals) . "');");
      if ($query_result === FALSE)
      {
        error_log("<p>Unable to insert the bracket.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
      }
    }

    $response_data = array("bracket_locked"=>false);
    echo json_encode($response_data);
  }
  else
  {
    // Not logged in.
  }
}
?>
