/**
 * Register dw namespace in the browser, for usage of the draggable window object
 *
 * $ has been defined globally in the app.js
 */
(function (dw, $, undefined) {

  let openWidth = 760;
  let opened = false;
  let openedX = 0;
  let fullScreen = false;
  let fullScreenX = 0;
  let closeButton = $('#close-button');
  let fullScreenButton = $('#fullscreen-button');
  let dragButton = $('#drag-button');
  let leftFrame = $('#left-container');
  let rightFrame = $('#right-container');
  let moveContainersInner = $('#draggable-bar-inner');
  let animationCount = 0;
  let invisibleFrame = $('#invisible-frame');
  let lastX = -1;
  let firstOpen = true;

  /**
   * @param {boolean} value
   */
  function setOpened(value) {
    opened = value;
    cb.setOpenedState(opened);
  }

  /**
   * Verify whether the window is opened
   * @returns {boolean}
   */
  dw.isOpened = function () {
    return opened;
  };

  ////////////////////////////////
  // Layout handlers
  ////////////////////////////////

  /**
   * Handler for the open cm window action
   */
  dw.openWindow = function (readyHandler) {
    // Check if already opened
    if (opened) {
      if (typeof readyHandler === 'function') readyHandler();

      return;
    }

    // Setup variables
    setOpened(true);
    readyHandler = typeof readyHandler === 'function' ? readyHandler : function () {
      if (!firstOpen) return;

      // When the map is opened for the first time, make sure to center the view
      cb.centerView(500);
    };

    // Check for full screen, and adjust resize accordingly
    if (fullScreen) {
      dragButton.doResize(-moveContainersInner.innerWidth(), -moveContainersInner.innerWidth(), undefined, true, function () {
        readyHandler();
        firstOpen = false;
      });
      moveContainersInner.fadeOut();
    } else {
      dragButton.doResize(openedX !== 0 ? openedX : (openWidth + ($('#draggable-bar-inner').innerWidth() / 2) - 1), undefined, undefined, true, function () {
        readyHandler();
        firstOpen = false;
      });
    }

    // Sent event
    window.eDispatch.openedConceptBrowser();
  };

  /**
   * Handler for the close window button
   */
  dw.closeWindow = function () {
    if (!opened) return;
    setOpened(false);
    openedX = lastX;

    // Resize the window in order to close it
    dragButton.doResize($(window).width() + (moveContainersInner.innerWidth() / 2) - 1, 0, $('body').width(), true);
    moveContainersInner.fadeIn();

    // Sent event
    window.eDispatch.closedConceptBrowser();
  };

  /**
   * Handler for toggle window action
   */
  dw.toggleWindow = function () {
    opened ? dw.closeWindow() : dw.openWindow();
  };

  /**
   * Handler for the full screen button
   */
  dw.fullScreenWindow = function () {
    if (fullScreen) {
      // Close full screen
      dragButton.doResize(fullScreenX, 0, undefined, true, function () {
        $('#full-screen-button').find('span').html('Open full screen');
      });
      moveContainersInner.fadeIn();
    } else {
      // Open full screen
      fullScreenX = lastX;
      dragButton.doResize(-moveContainersInner.innerWidth(), -moveContainersInner.innerWidth(), undefined, true, function () {
        $('#full-screen-button').find('span').html('Close full screen');
      });
      moveContainersInner.fadeOut();
    }

    // Toggle state
    fullScreen = !fullScreen;
  };

  // Bind the click handler to the button
  closeButton.click(dw.closeWindow);
  fullScreenButton.click(dw.fullScreenWindow);

  ////////////////////////////////
  // Drag button handlers
  ////////////////////////////////

  /**
   * Handler executed to resize the iframes
   * @param x
   * @param minWidth
   * @param maxWidth
   * @param animate
   * @param callback
   * @param force
   */
  dragButton.doResize = function (x, minWidth, maxWidth, animate, callback, force) {
    // Check for double call
    if (force !== true && x === lastX) return;

    // Get information
    let centerWidth = $('#draggable-bar-inner').innerWidth();
    let clientWidth = $('body').width();

    // Check input
    minWidth = typeof minWidth !== 'undefined' ? minWidth : 0.25 * clientWidth;
    maxWidth = typeof maxWidth !== 'undefined' ? maxWidth : 0.75 * clientWidth;
    animate = typeof animate !== 'undefined' ? animate : false;
    callback = typeof callback !== 'undefined' ? callback : function () {
    };

    // Set the last x value
    lastX = x - (centerWidth / 2) < minWidth ? minWidth : x;

    // Calculate new widths
    let leftWidth = x - (centerWidth / 2);
    leftWidth = Math.min(leftWidth, maxWidth);
    leftWidth = Math.max(leftWidth, minWidth);
    let rightWidth = clientWidth - leftWidth - centerWidth;

    // Move the window (with or without animation)
    if (animate) {
      animationCount = 3;
      let animationDuration = 1000;
      let completeFunction = function () {
        animationCount--;
        if (animationCount === 0) {
          callback();
          cb.resizeCanvas();
          iframeLoaderUpdate();
        }
      };
      leftFrame.animate({
        width: leftWidth
      }, {duration: animationDuration, complete: completeFunction});

      rightFrame.animate({
        width: rightWidth
      }, {duration: animationDuration, complete: completeFunction});
      rightFrame.find('.animation-opacity-container').animate({
        opacity: rightWidth <= 0 ? 0 : 1
      }, {duration: animationDuration, complete: completeFunction});

      // Prerender the canvas
      if (rightWidth > 0) {
        cb.requestResizeCanvasWithSizes(rightWidth);
      }
    } else {
      leftFrame.width(leftWidth);
      rightFrame.width(rightWidth);
      rightFrame.find('.animation-opacity-container').css('opacity', rightWidth <= 0 ? 0 : 1);
      callback();
      cb.requestResizeCanvas();
    }

    iframeLoaderUpdate();
  };

  /**
   * Handler that registers the drag event.
   * @param e
   */
  dragButton.doDrag = function (e) {
    if (animationCount > 0 && !_isDotronStudyArea) return;
    if (fullScreen) return;


    // For touch event, replace the jQuery event with the actual touch event
    // Otherwise, e.pageX would be undefined
    if (e.type === 'touchmove') {
      e = e.originalEvent.touches[0];
    } else if (e.which !== 1) {
      dragButton.stopDrag(e);
      return;
    }

    invisibleFrame.css('z-index', 100);
    dragButton.doResize(e.pageX);
  };

  /**
   * Handler that registers the stop drag event / touch start
   */
  dragButton.stopDrag = function () {
    invisibleFrame.css('z-index', -1);

    // Remove event listeners
    $(document).off('mousemove touchmove', dragButton.doDrag);
    $(document).off('mouseup touchend', dragButton.stopDrag);
  };

  /**
   * Handler that registers the drag and stop drag handles on mouse click
   */
  dragButton.on('mousedown touchstart', function () {
    try {
      // Add event listener at document level, to register the drag correctly
      $(document).on('mousemove touchmove', dragButton.doDrag);
      $(document).on('mouseup touchend', dragButton.stopDrag);
    } catch (e) {
      console.error('Column resize not available');
    }
  });

  ////////////////////////////////
  // Window handlers
  ////////////////////////////////

  /**
   * Also resize the view after window resize
   */
  window.onresize = function () {
    if (opened) {
      dragButton.doResize(lastX, undefined, undefined, undefined, undefined, true);
    } else {
      dragButton.doResize($(window).width() + (moveContainersInner.innerWidth() / 2) - 1, 0, $('body').width(), undefined, undefined, true);
    }
  };

  ////////////////////////////////
  // iFrame handlers
  ////////////////////////////////

  /**
   * Load a new source in the iframe
   * By removing the iframe before changing it's url, we do not influence the browser history stack
   * @param url
   */
  dw.iframeLoad = function (url) {
    dw.iframeLoader(true);
    const $iframe = $('#data-iframe');
    const $container = $iframe.parent();
    $iframe.remove();
    $iframe.attr('src', url);
    $container.append($iframe);
  };

  dw.iframeLoader = function (loading) {
    iframeLoaderUpdate();
    const $loader = $('#left-container-loader');
    if (loading) {
      $loader.fadeIn(0);
    } else {
      $loader.fadeOut();
    }
  };

  function iframeLoaderUpdate() {
    const $loader = $('#left-container-loader');
    const $iframe = $('#data-iframe');

    $loader.height($iframe.height());
    $loader.width($iframe.width());
  }

}(window.dw = window.dw || {}, $));
