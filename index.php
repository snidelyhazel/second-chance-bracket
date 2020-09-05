<?php
  $CURRENT_PAGE = "Home";
  $PAGE_TITLE = "Second-Chance Bracket | Home";

  include("includes/SCB_connect.php");

?>

<!DOCTYPE html>
<html>
<head>
  <?php include("includes/SCB_head.php");?>
</head>
<body>
  <?php include("includes/SCB_nav.php");?>
  <main>
    <?php
      if(isset($_SESSION['username']))
      {
        // logged in!
        $username = $_SESSION['username'];
        ?><h1>Welcome back to the Second-Chance Bracket Challenge!</h1>
        <h3>Hello <?= $username ?>, here are today's games:</h3><?php

      }
      else
      {
        ?><h1>Welcome to the Second-Chance Bracket Challenge!</h1>

        <p>If your bracket looks anything like mine, it's totally busted! So, I've decided to give you a second chance!</p>

        <h3><a href="/join/">Join today!</a></h3><?php

      }
      ?>

      <table class="score-table">
      <?php
      $result = file_get_contents("https://statsapi.web.nhl.com/api/v1/schedule?expand=schedule.linescore");
      $json = json_decode($result);
      foreach ($json->dates as $date) {
        foreach ($date->games as $game) {
          $state = $game->status->detailedState;
          $away_team = $game->teams->away;
          $home_team = $game->teams->home;
          $away_score = $away_team->score;
          $home_score = $home_team->score;
          $away_name = $away_team->team->name;
          $home_name = $home_team->team->name;
          $away_logo = "/logos/" . $team_name_to_key[$away_name] . ".svg";
          $home_logo = "/logos/" . $team_name_to_key[$home_name] . ".svg";
          $period = "";
          $time_remaining = "";
          if (property_exists($game, "linescore") && $game->linescore->currentPeriod > 3)
          {
            $period = "/" . $game->linescore->currentPeriodOrdinal;
            $time_remaining = $game->linescore->currentPeriodTimeRemaining;
          }

          ?>
          <tr>
            <td class="padding-top">
              <img class="logo-small" src="<?= $away_logo ?>"/>
            </td>
            <td class="padding-top">
              <?= $away_name ?>
            </td>
            <td class="padding-top">
              <?= $away_score ?>
            </td>
            <td rowspan="2" class="border-bottom padding-top">
              <?= $state ?><?= $period ?>
            </td>
          </tr>
          <tr>
            <td class="border-bottom">
              <img class="logo-small" src="<?= $home_logo ?>"/>
            </td>
            <td class="border-bottom">
              <?= $home_name ?>
            </td>
            <td class="border-bottom">
              <?= $home_score ?>
            </td>
          </tr>
          <?php

        }
      }
      ?>
      </table>
  </main>

  <?php include("includes/SCB_footer.php");?>
</body>
</html>
