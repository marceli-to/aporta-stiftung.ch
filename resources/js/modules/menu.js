(function () {

  const selectors = {
    btn: '[data-sticky-menu-btn]',
    menu: '[data-menu]',
  };

  const treshold = 400;

  const init = () => {
    handleScroll();
    handlePageLoad();
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

  const handlePageLoad = () => {
    // on page load, the the url, if it contains a hash, find the corresponding menu item
    // and add the class 'font-bold' to it
    const url = window.location.href;
    const hash = url.split('#')[1];
    if (!hash) return;

    const menues = document.querySelectorAll(selectors.menu);
    // loop over both menues and find the corresponding menu item
    menues.forEach(menu => {
      // find all menu items
      const menuItems = menu.querySelectorAll('a');
      // loop over all menu items
      menuItems.forEach(item => {
        // if the menu item href contains the hash, add the class 'font-bold'
        if (item.href.includes(hash)) {
          item.classList.add('font-bold');
        }
      });
    });
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