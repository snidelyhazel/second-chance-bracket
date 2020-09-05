<?php
  $CURRENT_PAGE = "Join";
  $PAGE_TITLE = "Second-Chance Bracket | Join";
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
    <h1>Join the Second-Chance Bracket Challenge!</h1>
    <h3>Thanks for deciding to make your Second-Chance Bracket!</h3>
    <p>First, we'll need to learn a little bit about you.</p>

    <form id="joinForm">
      <table>
        <tr>
          <td>
            <label for="username">Enter username:</label>
          </td>
          <td>
            <input type="text" name="username" id="username" placeholder="username" style="margin: 3px;" required title="Please avoid spaces or special characters in your username." pattern='^[^\s<>,/?;:"\[\]\\{}+=()`~!@#$%^&amp;*]+$'/>
          </td>
        </tr>
        <tr>
          <td>
            <label for="password">Enter password:</label>
          </td>
          <td>
            <input type="password" name="password" id="password" placeholder="password" style="margin: 3px;" required/>
          </td>
        </tr>
        <tr>
          <td>
            <label for="email">Enter email:</label>
          </td>
          <td>
            <input type="email" name="email" id="email" placeholder="email@domain.com" style="margin: 3px;" required/>
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
      var joinForm = document.getElementById("joinForm");
      joinForm.addEventListener("submit", function()
      {
        event.preventDefault();
        var request = new XMLHttpRequest();
        request.open('POST', '/join/make_account.php');
        // Define what happens on successful data submission
        request.addEventListener("load", function(event)
        {
          var responseData = JSON.parse(request.responseText);
          var warning = document.getElementById("warning");
          warning.textContent = "";

          if (responseData.username_taken)
          {
            var usernameInput = document.getElementById("username");
            usernameInput.value = "";

            usernameInput.focus();

            warning.textContent += "That username is already registered. ";
          }
          if (responseData.email_taken)
          {
            var emailInput = document.getElementById("email");
            emailInput.value = "";

            if (!responseData.username_taken)
            {
              emailInput.focus();
            }

            warning.textContent += "That email is already registered. ";
          }

          if (responseData.username_taken || responseData.email_taken)
          {
            warning.innerHTML += "Would you like to <a href='/login/'>login</a>?";
          }
          else
          {
            window.location = "/create/";
          }
        });

        request.addEventListener("error", function(event)
        {
          alert("I'm sorry, the server might be down.");
          console.log('HTTP error', request.status, request.statusText);
        });
        // start request
        request.send(new FormData(joinForm));
      });
    </script>

  </main>

  <?php include("../includes/SCB_footer.php");?>
</body>
</html>
