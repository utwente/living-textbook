/**
 * Register dw (draggable window) namespace for usage
 */
(function (dw, $, undefined) {

  var openWidth = 760;
  var opened = false;
  var openedX = 0;
  var fullScreen = false;
  var fullScreenX = 0;
  var openButton = $('#open_button');
  var closeButton = $('#close_button');
  var fullScreenButton = $('#full_screen_button');
  var dragButton = $('#drag_button');
  var leftFrame = $('#container_left');
  var rightFrame = $('#container_right');
  var rightButtonContainer = $('#right_button_container');
  var moveContainersInner = $('#move_containers_inner');
  var animation = 0;
  var invisibleFrame = $('#invisible_frame');
  var lastX = document.body.clientWidth / 2;
  var firstOpen = true;

  ////////////////////////////////
  // Layout handlers
  ////////////////////////////////

  /**
   * Handler for the open cm window action
   */
  openButton.openWindow = function (readyHandler) {
    // Check if already opened
    if (opened) {
      readyHandler();
      return;
    }

    // Setup variables
    opened = true;
    readyHandler = typeof readyHandler === 'function' ? readyHandler : function () {
      if (!firstOpen) return;
      document.getElementById("iframe_right").contentWindow.postMessage({
        'type': 'cb_opened'
      }, '*');
    };

    // Check for full screen, and adjust resize accordingly
    if (fullScreen) {
      dragButton.doResize(-moveContainersInner.innerWidth(), -moveContainersInner.innerWidth(), undefined, true, function () {
        readyHandler();
        firstOpen = false;
      });
      moveContainersInner.fadeOut();
    } else {
      dragButton.doResize(openedX !== 0 ? openedX : (openWidth + ($('#move_containers_inner').innerWidth() / 2) - 1), undefined, undefined, true, function () {
        readyHandler();
        firstOpen = false;
      });
    }

    // Fade out the open button
    openButton.fadeOut();
  };

  /**
   * Handler for the close window button
   */
  closeButton.closeWindow = function () {
    if (!opened) return;
    opened = false;
    openedX = lastX;

    // Resize the window in order to close it
    dragButton.doResize($(window).width() + (moveContainersInner.innerWidth() / 2) - 1, 0, $('body').width(), true, function () {
      openButton.fadeIn();
    });
    moveContainersInner.fadeIn();
  };

  /**
   * Handler for the full screen button
   */
  fullScreenButton.fullScreenWindow = function () {
    if (fullScreen) {
      // Close full screen
      dragButton.doResize(fullScreenX, 0, undefined, true, function () {
        $('#full_screen_button').find('span').html("Open full screen");
      });
      moveContainersInner.fadeIn();
    } else {
      // Open full screen
      fullScreenX = lastX;
      dragButton.doResize(-moveContainersInner.innerWidth(), -moveContainersInner.innerWidth(), undefined, true, function () {
        $('#full_screen_button').find('span').html("Close full screen");
      });
      moveContainersInner.fadeOut();
    }

    // Toggle state
    fullScreen = !fullScreen;
  };

  // Bind the click handler to the button
  openButton.click(openButton.openWindow);
  closeButton.click(closeButton.closeWindow);
  fullScreenButton.click(fullScreenButton.fullScreenWindow);

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
    var centerWidth = $('#move_containers_inner').innerWidth();
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
      animation = 3;
      leftFrame.animate({
        width: leftWidth
      }, 1000, function () {
        animation--;
        if (animation === 0) {
          callback();
        }
      });
      rightFrame.add(rightButtonContainer).animate({
        width: rightWidth
      }, 1000, function () {
        animation--;
        if (animation === 0) {
          callback();
        }
      });
    } else {
      leftFrame.width(leftWidth);
      rightFrame.width(rightWidth);
      rightButtonContainer.width(rightWidth);
      callback();
    }
  };

  /**
   * Handler that registers the drag event.
   * @param e
   */
  dragButton.doDrag = function (e) {
    if (animation > 0) return;
    if (fullScreen) return;
    if (e.which !== 1) {
      dragButton.stopDrag(e);
      return;
    }

    invisibleFrame.css('z-index', 15);
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

  /**
   * Open the concept browser on cb link
   */
  window.addEventListener('message', function (event) {
    var message = event.data;
    if (message.type === 'cb_update') {
      openButton.openWindow(function () {
        document.getElementById("iframe_right").contentWindow.postMessage({
          'type': 'cb_update_opened',
          'data': message.data
        }, '*');
      });
    }
  });
}(window.dw = window.dw || {}, jQuery));
