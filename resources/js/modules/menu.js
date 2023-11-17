(function () {

  const selectors = {
    btn: '[data-sticky-menu-btn]',
  };

  const treshold = 400;

  const init = () => {
    handleScroll();
    window.addEventListener('scroll', debounce(handleScroll, 100));
  };
  
  const handleScroll = () => {
    const btn = document.querySelector(selectors.btn);

    if (window.scrollY > treshold) {
      btn.classList.remove('hidden');
      btn.classList.add('flex');
    } else {
      btn.classList.add('hidden');
      btn.classList.remove('flex');
    }
  };

  const debounce = (func, wait, immediate) => {
    let timeout;
    return function () {
      const context = this,
        args = arguments;
      const later = function () {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      const callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow)
        func.apply(context, args);
    };
  }

  init();
  
})();