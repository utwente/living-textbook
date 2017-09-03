// var dokuwikiUrl = 'https://itc-giscience.utwente.nl/doku.php';
var dokuwikiUrl = 'https://living-textbook.drenso.nl:60130/doku/doku.php';

$(function () {

  function updateDokuwikiFrame(params) {
    document.getElementById("iframe_left").src = dokuwikiUrl + '?' + params;
  }

  function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

  function getUrlParams(message) {
    return message.data.substring(message.data.indexOf('?') + 1);
  }

  function updatePageUrl(params) {
    window.history.replaceState({}, "", pageUrl + '?doku=' + encodeURIComponent(params));
  }

  // Get page url
  var pageUrl = window.location.href;
  var pageUrlLoc = pageUrl.indexOf("?");
  if (pageUrlLoc > 0) {
    pageUrl = pageUrl.substr(0, pageUrlLoc);
  }

  // Open the requested wiki page
  updateDokuwikiFrame(getParameterByName('doku'));

  // Install message handler
  window.addEventListener('message', function (event) {
    var message = event.data;
    console.log(message);

    // Update url handler
    if (event.data.type === 'wiki_load') {
      updatePageUrl(getUrlParams(event.data));
      return;
    }

    // Update dokuwiki frame src handler
    if (event.data.type === 'wiki_update'){
      if (message.data.startsWith(dokuwikiUrl)) {
        var params = getUrlParams(message);
        updateDokuwikiFrame(params);
      } else {
        document.getElementById("iframe_left").src = message.data;
      }
    }

    // Forward to other frames
    document.getElementById("iframe_left").contentWindow.postMessage(message, '*');
    document.getElementById("iframe_right").contentWindow.postMessage(message, '*');
  });
});
