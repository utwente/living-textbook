/**
 * Register dw namespace in the browser, for usage of the draggable window object
 *
 * $ has been defined globally in the app.js
 */
(function (dw, $, undefined) {

  var openWidth = 760;
  var opened = false;
  var openedX = 0;
  var fullScreen = false;
  var fullScreenX = 0;
  var closeButton = $('#close-button');
  var fullScreenButton = $('#fullscreen-button');
  var dragButton = $('#drag-button');
  var leftFrame = $('#left-container');
  var rightFrame = $('#right-container');
  var moveContainersInner = $('#draggable-bar-inner');
  var animationCount = 0;
  var invisibleFrame = $('#invisible-frame');
  var lastX = document.body.clientWidth / 2;
  var firstOpen = true;

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
    opened = true;
    readyHandler = typeof readyHandler === 'function' ? readyHandler : function () {
      if (!firstOpen) return;

      // When the map is opened, make sure to center the view
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
  };

  /**
   * Handler for the close window button
   */
  dw.closeWindow = function () {
    if (!opened) return;
    opened = false;
    openedX = lastX;

    // Resize the window in order to close it
    dragButton.doResize($(window).width() + (moveContainersInner.innerWidth() / 2) - 1, 0, $('body').width(), true);
    moveContainersInner.fadeIn();
  };

  /**
   * Handler for toggle window action
   */
  dw.toggleWindow = function() {
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
    var centerWidth = $('#draggable-bar-inner').innerWidth();
    var clientWidth = $('body').width();

    // Check input
    minWidth = typeof minWidth !== 'undefined' ? minWidth : 0.25 * clientWidth;
    maxWidth = typeof maxWidth !== 'undefined' ? maxWidth : 0.75 * clientWidth;
    animate = typeof animate !== 'undefined' ? animate : false;
    callback = typeof callback !== 'undefined' ? callback : function () {
    };

    // Set the last x value
    lastX = x - (centerWidth / 2) < minWidth ? minWidth : x;

    // Calculate new widths
    var leftWidth = x - (centerWidth / 2);
    leftWidth = Math.min(leftWidth, maxWidth);
    leftWidth = Math.max(leftWidth, minWidth);
    var rightWidth = clientWidth - leftWidth - centerWidth;

    // Move the window (with or without animation)
    if (animate) {
      animationCount = 3;
      var animationDuration = 1000;
      var completeFunction = function () {
        animationCount--;
        if (animationCount === 0) {
          callback();
          cb.resizeCanvas();
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
        cb.resizeCanvasWithSizes(rightWidth)
      }
    } else {
      leftFrame.width(leftWidth);
      rightFrame.width(rightWidth);
      rightFrame.find('.animation-opacity-container').css('opacity', rightWidth <= 0 ? 0 : 1);
      callback();
      cb.resizeCanvas();
    }
  };

  /**
   * Handler that registers the drag event.
   * @param e
   */
  dragButton.doDrag = function (e) {
    if (animationCount > 0) return;
    if (fullScreen) return;
    if (e.which !== 1) {
      dragButton.stopDrag(e);
      return;
    }

    invisibleFrame.css('z-index', 100);
    dragButton.doResize(e.pageX);
  };

  /**
   * Handler that registers the stop drag event
   */
  dragButton.stopDrag = function () {
    invisibleFrame.css('z-index', -1);

    // Remove event listeners
    document.documentElement.removeEventListener('mousemove', dragButton.doDrag);
    document.documentElement.removeEventListener('mouseup', dragButton.stopDrag);
  };

  /**
   * Handler that registers the drag and stop drag handles on mouse click
   */
  dragButton.on('mousedown', function () {
    try {
      // Add event listener at document level, to register the drag correctly
      document.documentElement.addEventListener('mousemove', dragButton.doDrag);
      document.documentElement.addEventListener('mouseup', dragButton.stopDrag);
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
}(window.dw = window.dw || {}, $));
