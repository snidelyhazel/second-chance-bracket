<?php
  $CURRENT_PAGE = "Rankings";
  $PAGE_TITLE = "Second-Chance Bracket | Rankings";
  include("../includes/SCB_connect.php");
?>

<!DOCTYPE html>
<html>
<head>
  <?php include("../includes/SCB_head.php");?>
</head>
<body>
  <?php include("../includes/SCB_nav.php");?>
  <main>
    <h1>The Second-Chance Bracket Challenge Rankings!</h1>
    <h3></h3>

    <table class="score-table">
      <tr>
        <th>
          Ranking
        </th>
        <th>
          User
        </th>
        <th>
          Total
        </th>
        <th>
          Champ
        </th>
      </tr>
      <?php
        $year = date("Y");

        $previous_year = $year - 1;
        $season = $previous_year . $year;

        // Update these dates for each season.
        $playoff_start_and_end_dates = "&startDate=2021-05-16&endDate=2021-09-01";
        // The URL should be something like: https://statsapi.web.nhl.com/api/v1/schedule?season=20202021&gameType=P&startDate=2021-05-16&endDate=2021-09-01

        $result = file_get_contents("http://statsapi.web.nhl.com/api/v1/schedule?season=" . $season . "&gameType=P" . $playoff_start_and_end_dates);
        $json = json_decode($result);
        $teams = array();

        function addOutcome($team_a_name, $team_b_name, $team_a_score, $team_b_score, &$teams, $team_name_to_key)
        {
          if (!array_key_exists($team_a_name, $team_name_to_key))
          {
            return;
          }
          $teamAKey = $team_name_to_key[$team_a_name];
          if (!array_key_exists($teamAKey, $teams))
          {
            $teams[$teamAKey] = array("name"=>$team_a_name, "opponents"=>[], "opponents_defeated"=>0);
          }
          $opponent = null;
          for ($i=0, $len=count($teams[$teamAKey]["opponents"]); $i<$len; $i++) {
            if ($teams[$teamAKey]["opponents"][$i]["name"] == $team_b_name)
            {
              $opponent = $i;
            }
          }
          if (is_null($opponent))
          {
            $opponent = count($teams[$teamAKey]["opponents"]);
            $teams[$teamAKey]["opponents"][] = array("name"=>$team_b_name, "wins_against"=>0, "losses_against"=>0, "games_played"=>0, "goals_scored"=>0, "final"=>false);
          }
          $teams[$teamAKey]["opponents"][$opponent]["goals_scored"] += $team_a_score + $team_b_score;
          if ($team_a_score >  0 || $team_b_score > 0)
          {
            $teams[$teamAKey]["opponents"][$opponent]["games_played"]++;
          }
          if ($team_a_score > $team_b_score)
          {
            $teams[$teamAKey]["opponents"][$opponent]["wins_against"]++;
          }
          else if ($team_a_score < $team_b_score)
          {
            $teams[$teamAKey]["opponents"][$opponent]["losses_against"]++;
          }

          $opponents_defeated = 0;
          for ($i=0, $len=count($teams[$teamAKey]["opponents"]); $i<$len; $i++) {
            if ($teams[$teamAKey]["opponents"][$i]["wins_against"] >= 4)
            {
              $opponents_defeated++;
            }
            if ($teams[$teamAKey]["opponents"][$i]["wins_against"] >= 4 || $teams[$teamAKey]["opponents"][$i]["losses_against"] >= 4)
            {
              $teams[$teamAKey]["opponents"][$i]["final"] = true;
            }
          }
          $teams[$teamAKey]["opponents_defeated"] = $opponents_defeated;
        }

        foreach ($json->dates as $date)
        {
          foreach ($date->games as $game)
          {
            addOutcome($game->teams->away->team->name, $game->teams->home->team->name, $game->teams->away->score, $game->teams->home->score, $teams, $team_name_to_key);
            addOutcome($game->teams->home->team->name, $game->teams->away->team->name, $game->teams->home->score, $game->teams->away->score, $teams, $team_name_to_key);
          }
        }

        $scf_participant = null;
        foreach ($teams as $team_key => $team) {
          if ($team["opponents_defeated"] >= 3)
          {
            $scf_participant = $team_key;
          }
        }

        $query_result = mysqli_query($db, "SELECT username, wcf_champ, ecf_champ, scf_champ, wcf_games, ecf_games, scf_goals FROM brackets LEFT JOIN users ON brackets.user_id = users.id WHERE year = " . $year . ";");
        $rankings = [];
        while($row = $query_result->fetch_assoc())
        {
          $total = 0;
          $wcf_champ = $row["wcf_champ"];
          $ecf_champ = $row["ecf_champ"];
          $scf_champ = $row["scf_champ"];
          $wcf_games = $row["wcf_games"];
          $ecf_games = $row["ecf_games"];
          $scf_goals = $row["scf_goals"];

          // echo("wcf_champ: " . $wcf_champ . ", wcf_games: " . $wcf_games . ", opponents_defeated: " . $teams[$wcf_champ]["opponents_defeated"] . ", final: " . $teams[$wcf_champ]["opponents"][2]["final"] . ", games_played: " . $teams[$wcf_champ]["opponents"][2]["games_played"] . ", goals_scored: " . $teams[$wcf_champ]["opponents"][2]["goals_scored"] . "<br/>");
          // echo("ecf_champ: " . $ecf_champ . ", ecf_games: " . $ecf_games . ", opponents_defeated: " . $teams[$ecf_champ]["opponents_defeated"] . ", final: " . $teams[$ecf_champ]["opponents"][2]["final"] . ", games_played: " . $teams[$ecf_champ]["opponents"][2]["games_played"] . ", goals_scored: " . $teams[$ecf_champ]["opponents"][2]["goals_scored"] . "<br/>");
          // echo("scf_champ: " . $scf_champ . ", scf_goals: " . $scf_goals . ", scf_participant: " . $scf_participant . ", opponents_defeated: " . $teams[$scf_participant]["opponents_defeated"] . ", final: " . $teams[$scf_participant]["opponents"][3]["final"] . ", games_played: " . $teams[$scf_participant]["opponents"][3]["games_played"] . ", goals_scored: " . $teams[$scf_participant]["opponents"][3]["goals_scored"] . "<br/>");
          if (array_key_exists($wcf_champ, $teams) && $teams[$wcf_champ]["opponents_defeated"] >= 3)
          {
            $total += 450;
          }
          if (array_key_exists($ecf_champ, $teams) && $teams[$ecf_champ]["opponents_defeated"] >= 3)
          {
            $total += 450;
          }
          if (array_key_exists($scf_champ, $teams) && $teams[$scf_champ]["opponents_defeated"] >= 4)
          {
            $total += 1000;
          }
          if (array_key_exists($wcf_champ, $teams)
              && count($teams[$wcf_champ]["opponents"]) >= 3
              && $teams[$wcf_champ]["opponents"][2]["final"]
              && $teams[$wcf_champ]["opponents"][2]["games_played"] == $wcf_games)
          {
            $total += 50;
          }
          if (array_key_exists($ecf_champ, $teams)
              && count($teams[$ecf_champ]["opponents"]) >= 3
              && $teams[$ecf_champ]["opponents"][2]["final"]
              && $teams[$ecf_champ]["opponents"][2]["games_played"] == $ecf_games)
          {
            $total += 50;
          }
          if ($scf_participant != null
              && count($teams[$scf_participant]["opponents"]) >= 4
              && $teams[$scf_participant]["opponents"][3]["final"])
          {
            $final_goals_scored = $teams[$scf_participant]["opponents"][3]["goals_scored"];

            $difference = abs($scf_goals - $final_goals_scored);
            $bonus_awarded = 20 - $difference;
            if ($bonus_awarded < 0)
            {
              $bonus_awarded = 0;
            }
            $total += $bonus_awarded;
          }

          $rankings[] = array("rank"=>0, "username"=>$row["username"], "total"=>$total, "scf_champ"=>$scf_champ);
        }

        function compare_rankings($a, $b)
        {
          return $b["total"] - $a["total"];
        }
        usort($rankings, "compare_rankings");

        $prev_rank = 0;
        $prev_score = 100000;
        for ($i=0, $len=count($rankings); $i<$len; $i++) {
          // echo($prev_score . " " . $ranking["total"] . " " . ($prev_score > $ranking["total"]) . " " . $prev_rank . "</br>");
          if ($prev_score > $rankings[$i]["total"])
          {
            $prev_score = $rankings[$i]["total"];
            $prev_rank++;
          }
          $rankings[$i]["rank"] = $prev_rank;
        }

        foreach ($rankings as $ranking)
        {
          ?>
          <tr>
            <td>
              <?= $ranking["rank"] ?>
            </td>
            <td>
              <?= $ranking["username"] ?>
            </td>
            <td>
              <?= $ranking["total"] ?>
            </td>
            <td>
              <img class="logo-small" src="/logos/<?= $ranking["scf_champ"] ?>.svg"/>
            </td>
          </tr>
          <?php
          //echo($row["user_id"] . " ");
        }
      ?>
    </table>
  </main>
  <?php include("../includes/SCB_footer.php");?>
</body>
</html>
