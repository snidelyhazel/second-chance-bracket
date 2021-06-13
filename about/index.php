<?php
  $CURRENT_PAGE = "About";
  $PAGE_TITLE = "Second-Chance Bracket | About";
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
    <h1>About the Second-Chance Bracket Challenge!</h1>
    <h3>The Second-Chance Bracket Challenge is brought to you by <a href="http://ashleyzeldin.com">Ashley Zeldin</a> and <a href="http://johnnesky.com">John Nesky</a>.</h3>
    <p>She likes hockey, he doesn't.</p>
    <p>But his bracket somehow always outperforms hers in the NHL Bracket Challenge, so she decided she needed a redo.</p>
    <p>Enter the Second-Chance Bracket Challenge, for those of us who got it wrong!</p>
    <br/>
    <p>Fun fact! In the 2020 Stanley Cup Playoffs, all the teams with C names, whether their city (Calgary, Carolina, Chicago, Colorado, Columbus) or their collective (Canadiens, Canucks, Capitals, Coyotes), were out of contention as of the 2nd round!</p>
  </main>
  <?php include("../includes/SCB_footer.php");?>
</body>
</html>
