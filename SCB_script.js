
function growShrinkLogo()
{
  var shieldLogo = document.getElementsByClassName("nav-logo")[0];
  if (window.matchMedia("(max-width: 600px)").matches)
  {
    shieldLogo.style.height = '80px';
  }
  else
  {
    var scroll = /*document.body.scrollTop ||*/ document.documentElement.scrollTop;
    shieldLogo.style.height = Math.min(200, Math.max(80, 200 - scroll)) + 'px';
  }
}

window.addEventListener("scroll", growShrinkLogo);
window.addEventListener('resize', growShrinkLogo);
setTimeout(growShrinkLogo);
