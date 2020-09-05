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
    <h3>Coming Soon!</h3>

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

        $result = file_get_contents("http://statsapi.web.nhl.com/api/v1/schedule?gameType=P&season=" . $season);
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
            $teams[$teamAKey] = array("name"=>$team_a_name, "opponents"=>[]);
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
            $teams[$teamAKey]["opponents"][] = array("name"=>$team_b_name, "winsAgainst"=>0, "lossesAgainst"=>0);
          }
          if ($team_a_score > $team_b_score)
          {
            $teams[$teamAKey]["opponents"][$opponent]["winsAgainst"]++;
          }
          else if ($team_a_score < $team_b_score)
          {
            $teams[$teamAKey]["opponents"][$opponent]["lossesAgainst"]++;
          }
        }

        foreach ($json->dates as $date) {
          foreach ($date->games as $game) {
            addOutcome($game->teams->away->team->name, $game->teams->home->team->name, $game->teams->away->score, $game->teams->home->score, $teams, $team_name_to_key);
            addOutcome($game->teams->home->team->name, $game->teams->away->team->name, $game->teams->home->score, $game->teams->away->score, $teams, $team_name_to_key);
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
