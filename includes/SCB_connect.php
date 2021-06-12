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
    "Anaheim Ducks"=>"ANA",
    "Arizona Coyotes"=>"ARI",
    "Boston Bruins"=>"BOS",
    "Buffalo Sabres"=>"BUF",
    "Calgary Flames"=>"CGY",
    "Carolina Hurricanes"=>"CAR",
    "Chicago Blackhawks"=>"CHI",
    "Colorado Avalanche"=>"COL",
    "Columbus Blue Jackets"=>"CBJ",
    "Dallas Stars"=>"DAL",
    "Detroit Red Wings"=>"DET",
    "Edmonton Oilers"=>"EDM",
    "Florida Panthers"=>"FLA",
    "Los Angeles Kings"=>"LAK",
    "Minnesota Wild"=>"MIN",
    "Montreal Canadiens"=>"MTL",
    "Nashville Predators"=>"NSH",
    "New Jersey Devils"=>"NJD",
    "New York Islanders"=>"NYI",
    "New York Rangers"=>"NYR",
    "Ottawa Senators"=>"OTT",
    "Philadelphia Flyers"=>"PHI",
    "Pittsburgh Penguins"=>"PIT",
    "San Jose Sharks"=>"SJS",
    "Seattle Kraken"=>"SEA",
    "St. Louis Blues"=>"STL",
    "Tampa Bay Lightning"=>"TBL",
    "Toronto Maple Leafs"=>"TOR",
    "Vancouver Canucks"=>"VAN",
    "Vegas Golden Knights"=>"VGK",
    "Washington Capitals"=>"WSH",
    "Winnipeg Jets"=>"WPG",
  );
?>
