<?php
  $CURRENT_PAGE = "Login";
  $PAGE_TITLE = "Second-Chance Bracket | Login";
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
    <h1>Login to the Second-Chance Bracket Challenge!</h1>
    <h3>Welcome back to your Second-Chance Bracket!</h3>
    <p>Let's make sure you are who you say you are.</p>

    <form id="loginForm">
      <table>
        <tr>
          <td>
            <label for="username_or_email">Enter username or email:</label>
          </td>
          <td>
            <input type="text" name="username_or_email" id="username_or_email" placeholder="username or email@domain.com" style="margin: 3px;">
          </td>
        </tr>
        <tr>
          <td>
            <label for="password">Enter password:</label>
          </td>
          <td>
            <input type="password" name="password" id="password" placeholder="password" style="margin: 3px;">
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
    <p>Did you forget your password? Retrieval coming soon!</p>

    <div id="warning" style="color: red;"></div>

    <script>
      var loginForm = document.getElementById("loginForm");
      loginForm.addEventListener("submit", function()
      {
        event.preventDefault();
        var request = new XMLHttpRequest();
        request.open('POST', '/login/authenticate.php');
        // Define what happens on successful data submission
        request.addEventListener("load", function(event)
        {
          var responseData = JSON.parse(request.responseText);
          var warning = document.getElementById("warning");
          warning.innerHTML = "";

          if (responseData.authenticated_successfully)
          {
            window.location = "/home/";
          }
          else
          {
            var passwordInput = document.getElementById("password");
            passwordInput.value = "";

            passwordInput.focus();

            warning.innerHTML += "The combination of login information provided is incorrect. <br/>If you do not already have a Second-Chance Bracket account, please <a href='/join/'>create an account</a>.";
          }
        });

        request.addEventListener("error", function(event)
        {
          alert("I'm sorry, the server might be down.");
          console.log('HTTP error', request.status, request.statusText);
        });
        // start request
        request.send(new FormData(loginForm));
      });
    </script>

  </main>

  <?php include("../includes/SCB_footer.php");?>
</body>
</html>
