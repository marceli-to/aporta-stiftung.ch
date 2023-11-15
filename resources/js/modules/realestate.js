(function () {

  const selectors = {
    images: {
      wrapper: '[data-images]',
      stage: '[data-image-stage]',
      source: '[data-image-source]',
    },

    map: {
      item: '[data-map-item]',
    },

    collapsible: {
      item: '[data-collapsible-item]',
    }

  };

  const init = () => {
    handleImageInteraction();
    handleMapInteraction()
  };

  const handleMapInteraction = () => {
    // on map item click get the value of data-map-item
    // find the corresponding collapsible item and set the state to open
    document.querySelectorAll(selectors.map.item).forEach((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        const target = item.getAttribute('data-map-item');
        const collapsibleItem = document.querySelector(`[data-collapsible-item="${target}"]`);
        
        // set the open state of all other collapsible items to false
        document.querySelectorAll(selectors.collapsible.item).forEach((item) => {
          if (item !== collapsibleItem) {
            item.open = false;
          }
        });
        collapsibleItem.open = true;

        // wait 25ms and scroll to the element
        setTimeout(() => {
          // scroll into view
          collapsibleItem.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
          });
        }, 25);
      });
    });
  };

  const handleImageInteraction = () => {
    document.querySelectorAll(selectors.images.source).forEach((source) => {
      source.addEventListener('click', (e) => {
        e.preventDefault();
        const wrapper = source.closest(selectors.images.wrapper);
        const stage = wrapper.querySelector(selectors.images.stage);
        const href = source.getAttribute('href');
        stage.setAttribute('src', href);

        // replace title
        const title = source.getAttribute('title');
        const caption = wrapper.querySelector('figcaption');
        caption.innerText = title;

        // remove active class from all other sources
        wrapper.querySelectorAll(selectors.images.source).forEach((source) => {
          source.classList.remove('is-active');
        });
        
        // add active class to clicked source
        source.classList.add('is-active');
        
      });
    });
  };

  init();
  
})();