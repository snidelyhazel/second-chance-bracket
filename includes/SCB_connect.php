<?php
  if (is_string(getenv("CLEARDB_DATABASE_URL")))
  {
    $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
    $server = $url["host"];
    $username = $url["user"];
    $password = $url["pass"];
    $database = substr($url["path"], 1);
  }
  else
  {
    // Access to test locally
    include("SCB_admininfo.php");
  }

  // $db = new mysqli($server, $username, $password, $database);

  // Create connection
  $db = new mysqli($server, $username, $password);
  // Check connection
  if ($db->connect_error)
  {
    die("Connection failed: " . $db->connect_error);
  }

  if (!$db->select_db($database))
  {
      if (!$db->query("CREATE DATABASE IF NOT EXISTS " . $database . ";"))
      {
          echo "Couldn't create database: " . $db->error;
      }
      $db->select_db($database);
  }

  session_start();

  $team_name_to_key = array(
    "Tampa Bay Lightning"=>"TBL",
    "Vegas Golden Knights"=>"VGK",
    "Vancouver Canucks"=>"VAN",
    "New York Islanders"=>"NYI",
    "Philadelphia Flyers"=>"PHI",
    "Dallas Stars"=>"DAL",
    "Colorado Avalanche"=>"COL",
  );
?>
