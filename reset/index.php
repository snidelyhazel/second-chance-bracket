<?php
  $CURRENT_PAGE = "Reset";
  $PAGE_TITLE = "Second-Chance Bracket | Reset";
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
    <h1>Reset your password</h1>
    <?php

      if (isset($_GET["key"]) && isset($_GET["email"]))
      {
        $key = $_GET["key"];
        $email = $_GET["email"];

        ?>
        <p>Please choose a new password.</p>

        <form id="resetForm">
          <input type="hidden" name="key" value="<?= $key ?>">
          <input type="hidden" name="email" value="<?= $email ?>">
          <table>
            <tr>
              <td>
                <label for="password">New password:</label>
              </td>
              <td>
                <input type="password" name="password" id="password" placeholder="password" style="margin: 3px;" required>
              </td>
            </tr>
            <tr>
              <td>
              </td>
              <td>
                <input type="submit" style="margin: 3px;">
              </td>
            </tr>
          </table>
        </form>

        <div id="warning" style="color: red;"></div>

        <script>
          var resetForm = document.getElementById("resetForm");
          resetForm.addEventListener("submit", function()
          {
            event.preventDefault();
            var request = new XMLHttpRequest();
            request.open('POST', '/reset/resetpassword.php');
            // Define what happens on successful data submission
            request.addEventListener("load", function(event)
            {
              var responseData = JSON.parse(request.responseText);
              var warning = document.getElementById("warning");
              warning.innerHTML = "";

              if (responseData.reset_successfully)
              {
                window.location = "/";
              }
              else
              {
                warning.innerHTML += "Failed to update password. Make sure you used a recent password reset link!";
              }
            });

            request.addEventListener("error", function(event)
            {
              alert("I'm sorry, the server might be down.");
              console.log('HTTP error', request.status, request.statusText);
            });
            // start request
            request.send(new FormData(resetForm));
          });
        </script>

        <?php
      }
      else
      {
        echo("<p>Please use the full password reset link from your email.</p>");
      }
    ?>
  </main>

  <?php include("../includes/SCB_footer.php");?>
</body>
</html>
