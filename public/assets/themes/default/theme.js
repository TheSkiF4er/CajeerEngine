(function(){
  var btn = document.querySelector('[data-ce="burger"]');
  var mobile = document.querySelector('[data-ce="mobile"]');
  if(!btn || !mobile) return;
  btn.addEventListener('click', function(){
    mobile.classList.toggle('is-open');
  });
})();