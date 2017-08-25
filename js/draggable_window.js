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

////////////////////////////////
// Layout handlers
////////////////////////////////

/**
 * Handler for the open cm window action
 */
openButton.openWindow = function () {
  if (opened) return;
  opened = true;
  dragButton.doResize(openedX !== 0 ? openedX : (768 + ($('#move_containers_inner').innerWidth() / 2) - 1), 0, true, function () {
  });
  openButton.fadeOut();
};

/**
 * Handler for the close window action
 */
closeButton.closeWindow = function () {
  if (!opened) return;
  opened = false;
  fullScreen = false;
  openedX = lastX;

  dragButton.doResize($(window).width() + (moveContainersInner.innerWidth() / 2) - 1, 0, true, function () {
    openButton.fadeIn();
    $('#full_screen_button').find('span').html("Open full screen");
  });
  moveContainersInner.fadeIn();
};

fullScreenButton.fullScreenWindow = function () {
  if (fullScreen) {
    // Close full screen
    dragButton.doResize(fullScreenX, 0, true, function () {
      $('#full_screen_button').find('span').html("Open full screen");
    });
    moveContainersInner.fadeIn();
  } else {
    // Open full screen
    fullScreenX = lastX;
    dragButton.doResize(-moveContainersInner.innerWidth(), -moveContainersInner.innerWidth(), true, function () {
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
 * @param animate
 * @param callback
 */
dragButton.doResize = function (x, minWidth, animate, callback) {
  // Check for double call
  if (x === lastX) return;

  // Get information
  var centerWidth = $('#move_containers_inner').innerWidth();
  var clientWidth = $('body').width();

  // Check input
  minWidth = typeof minWidth !== 'undefined' ? minWidth : 0.25 * clientWidth;
  animate = typeof animate !== 'undefined' ? animate : false;
  callback = typeof callback !== 'undefined' ? callback : function () {
  };

  // Calculate new widths
  var leftWidth = x - (centerWidth / 2);
  leftWidth = Math.min(leftWidth, clientWidth - minWidth);
  leftWidth = Math.max(leftWidth, minWidth);
  var rightWidth = clientWidth - leftWidth - centerWidth;

  // Set the last x value
  lastX = x;

  // Move the window (with or without animation
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
  dragButton.doResize(e.pageX)
};
/**
 * Handler that registers the stop drag event
 */
dragButton.stopDrag = function () {
  console.log("stop drag");
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
  dragButton.doResize(lastX);
};