<?php
  $CURRENT_PAGE = "Create";
  $PAGE_TITLE = "Second-Chance Bracket | Create";
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



<?php
  if(isset($_SESSION['username']))
  {
    // logged in!
    $username = $_SESSION['username'];
    ?>

    <h1>Let's Make Your Second-Chance Bracket!</h1>
    <h3></h3>
    <p>Welcome <?= $username ?>! Next, let's fill in your bracket!</p>

    <form id="bracketForm">
      <div class="bracket">
        <div id="wcfDiv">
          <div>
            <img class="logo" src="/logos/NHL_Western_Conference.svg"/>
          </div>
          <div>
            Western Conference Final
          </div>
          <label>
            <input type="radio" name="wcf_champ" value="VGK" required/>
            <img class="logo" src="/logos/VGK.svg"/>
          </label>
          <label>
            <input type="radio" name="wcf_champ" value="DAL" required/>
            <img class="logo" src="/logos/DAL.svg"/><!-- or COL? -->
          </label>
          <div>
            <select name="wcf_games" id="wcf_games" required>
              <option> </option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
            </select>
            <label for="wcf_games">Games</label>
          </div>
        </div>
        <div id="ecfDiv">
          <div>
            <img class="logo" src="/logos/NHL_Eastern_Conference.svg"/>
          </div>
          <div>
            Eastern Conference Final
          </div>
          <!-- <label>
            <input type="radio" name="ecf_champ" value="PHI"/>
            <img class="logo" src="/logos/PHI.svg"/>
          </label> -->
          <label>
            <input type="radio" name="ecf_champ" value="TBL" required/>
            <img class="logo" src="/logos/TBL.svg"/>
          </label>
          <label>
            <input type="radio" name="ecf_champ" value="NYI" required/>
            <img class="logo" src="/logos/NYI.svg"/>
          </label>
          <div>
            <select name="ecf_games" id="ecf_games" required>
              <option> </option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
            </select>
            <label for="ecf_games">Games</label>
          </div>
        </div>
        <div id="scfDiv">
          <div>
            <img style="height: 100px;" src="/logos/section-prizing-nhl-game.png"/>
          </div>
          <div>
            Stanley Cup Final
          </div>
          <label>
            <input id="scfw_champ_button" type="radio" name="scf_champ" required disabled/>
            <img id="scfw_champ_logo" class="logo" src="/logos/Question_mark_grey.svg"/>
          </label>
          <label>
            <input id="scfe_champ_button" type="radio" name="scf_champ" required disabled/>
            <img id="scfe_champ_logo" class="logo" src="/logos/Question_mark_grey.svg"/>
          </label>
          <div>
            <input type="number" name="scf_goals" id="scf_goals" style="width: 30px;" required>
            <label for="scf_goals">Goals Scored</label>
          </div>
          <div id="outcomeMessage">

          </div>
        </div>
      </div>

      <div style="text-align: center;">
        <input type="submit" value="Submit"/>
      </div>
    </form>

    <div id="warning" style="color: red;"></div>

    <script>
      var bracketForm = document.getElementById("bracketForm");
      var wcfDiv = document.getElementById("wcfDiv");
      var ecfDiv = document.getElementById("ecfDiv");
      var scfw_champ_button = document.getElementById("scfw_champ_button");
      var scfe_champ_button = document.getElementById("scfe_champ_button");
      var scfw_champ_logo = document.getElementById("scfw_champ_logo");
      var scfe_champ_logo = document.getElementById("scfe_champ_logo");
      var outcomeMessage = document.getElementById("outcomeMessage");

      var madeAnyChanges = false;
      bracketForm.addEventListener("change", function(event)
      {
        madeAnyChanges = true;
      });
      window.addEventListener('beforeunload', function (e) {
        if (madeAnyChanges)
        {
          e.preventDefault();
          e.returnValue = '';
        }
      });

      wcfDiv.addEventListener("change", updateForm);
      ecfDiv.addEventListener("change", updateForm);
      scfDiv.addEventListener("change", updateForm);

      function updateForm()
      {
        var wcfChamp = bracketForm.wcf_champ.value;
        var ecfChamp = bracketForm.ecf_champ.value;
        if (wcfChamp != "")
        {
          scfw_champ_button.value = wcfChamp;
          scfw_champ_logo.src = "/logos/" + wcfChamp + ".svg";
          scfw_champ_button.removeAttribute("disabled");
        }
        if (ecfChamp != "")
        {
          scfe_champ_button.value = ecfChamp;
          scfe_champ_logo.src = "/logos/" + ecfChamp + ".svg";
          scfe_champ_button.removeAttribute("disabled");
        }

        var champ = bracketForm.scf_champ.value;
        if (champ == "") return;
        outcomeMessage.textContent = "The " +
        {
          VGK: "Vegas Golden Knights",
          DAL: "Dallas Stars",
          TBL: "Tampa Bay Lightning",
          NYI: "New York Islanders",
        }[champ] + " are your 2020 Stanley Cup Champions!";
      }

      bracketForm.addEventListener("submit", function()
      {
        event.preventDefault();
        var request = new XMLHttpRequest();
        request.open('POST', '/create/submit_bracket.php');
        // Define what happens on successful data submission
        request.addEventListener("load", function(event)
        {
          var responseData = JSON.parse(request.responseText);

          madeAnyChanges = false;

          warning.textContent = "";
          if (responseData.bracket_locked)
          {
            warning.textContent += "Failed to save bracket. Brackets locked after conference finals start. ";
          }
          else
          {
            window.location = "/home/";
          }
        });

        request.addEventListener("error", function(event)
        {
          alert("I'm sorry, the server might be down.");
          console.log('HTTP error', request.status, request.statusText);
        });

        // start request
        request.send(new FormData(bracketForm));
      });


      var savedBracket = <?php

        $user_id = $_SESSION['user_id'];
        $year = date("Y");
        $query_result = mysqli_query($db, "SELECT `wcf_champ`, `ecf_champ`, `scf_champ`, `wcf_games`, `ecf_games`, `scf_goals`  FROM brackets WHERE `user_id` = " . $user_id . " AND `year` = " . $year . ";");
        $saved_bracket = null;
        if (mysqli_num_rows($query_result) != 0)
        {
          echo(json_encode($query_result->fetch_assoc()));
        }
        else
        {
          echo("{}");
        }

      ?>;

      for (var key in savedBracket)
      {
        bracketForm[key].value = savedBracket[key];
        updateForm();
      }

    </script>

    <?php
  }
  else
  {
    // Not logged in!
    ?>
    <h1>Get (Your Info) In the Box!</h1>
    <p>That is, <a href="/join/">join</a> or <a href="/login/">login!</a></p>
    <?php
  }
?>

  </main>

  <?php include("../includes/SCB_footer.php");?>
</body>
</html>
