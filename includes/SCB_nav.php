<header>
  <img src="/scbshield.png" class="nav-logo" alt="Second-Chance Bracket"/>
  <nav>
    <div class="topnav" id="SCB-nav">
      <a href="/" <?php if($CURRENT_PAGE=="Home") echo "class='active'"; ?>>Home</a>
      <?php
        if(!isset($_SESSION['username']))
        {
          // logged out!
          ?>
          <a href="/join/" <?php if($CURRENT_PAGE=="Join") echo "class='active'"; ?>>Join</a>
          <a href="/login/" <?php if($CURRENT_PAGE=="Login") echo "class='active'"; ?>>Login</a>
          <?php
        }
        else
        {
          // logged in!
          ?>
          <a href="/create/" <?php if($CURRENT_PAGE=="Create") echo "class='active'"; ?>>Bracket</a>
          <?php
        }
      ?>
      <a href="/rankings/" <?php if($CURRENT_PAGE=="Rankings") echo "class='active'"; ?>>Rankings</a>
      <a href="/about/" <?php if($CURRENT_PAGE=="About") echo "class='active'"; ?>>About</a>
      <?php
        if(isset($_SESSION['username']))
        {
          // logged in!
          ?>
          <a href="javascript:void(0);" onclick="logout()">Logout</a>
          <?php
        }
      ?>
    </div>
    <a href="javascript:void(0);" class="icon" onclick="toggleNav()">
      <span>☰</span>
    </a>
  </nav>
</header>

<!-- Responsive navigation -->

<script>
  function toggleNav()
  {
    var item = document.getElementById("SCB-nav");
    if (item.className === "topnav")
    {
      item.className += " responsive";
    }
    else
    {
      item.className = "topnav";
    }
  }

  function logout()
  {

    var request = new XMLHttpRequest();
    request.open('POST', '/logout.php');
    // Define what happens on successful data submission
    request.addEventListener("load", function(event)
    {
      window.location = "/";
    })
    request.send();
  }

</script>
