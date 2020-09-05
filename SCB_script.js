window.onscroll = function() {
  growShrinkLogo();
};

function growShrinkLogo() {
  var shieldLogo = document.getElementsByClassName("nav-logo")[0];
  var scroll = /*document.body.scrollTop ||*/ document.documentElement.scrollTop;
  shieldLogo.style.height = Math.min(200, Math.max(80, 200 - scroll)) + 'px';
}

setTimeout(growShrinkLogo);
