var button = document.getElementById("drag_button");
var leftFrame = document.getElementById("container_left");
var rightFrame = document.getElementById("container_right");
var invisibleFrame = document.getElementById("invisible_frame");
var lastX = document.body.clientWidth / 2;

button.doResize = function (x) {
  invisibleFrame.style.zIndex = 15;
  var cWidth = document.body.clientWidth;
  var minWidth = 0.25 * cWidth;
  var lWidth = x - 9;
  lWidth = Math.min(lWidth, cWidth - minWidth);
  lWidth = Math.max(lWidth, minWidth);
  var rWidth = cWidth - lWidth - 18;
  rWidth = Math.min(rWidth, cWidth - minWidth);
  rWidth = Math.max(rWidth, minWidth);

  leftFrame.style.width = lWidth + 'px';
  rightFrame.style.width = rWidth + 'px';
  lastX = x;
};
button.doDrag = function (e) {
  if (e.which !== 1) {
    button.stopDrag(e);
    return;
  }

  button.doResize(e.pageX)
};
button.stopDrag = function (e) {
  invisibleFrame.style.zIndex = -1;
  button.removeEventListener('mousemove', button.doDrag);
  button.removeEventListener('mouseup', button.stopDrag);
};
button.onmousedown = function (e) {
  try {
    document.documentElement.addEventListener('mousemove', button.doDrag);
    document.documentElement.addEventListener('mouseup', button.stopDrag);
  } catch (e) {
    console.error("Column resize not available");
  }
};

window.onresize = function () {
  button.doResize(lastX);
}