// var dokuwikiUrl = 'https://itc-giscience.utwente.nl/doku.php';
var dokuwikiUrl = 'https://living-textbook.drenso.nl:60130/doku/doku.php';

/**
 * Start executing after DOM load
 */
$(function () {

  /**
   * Update the dokuwiki frame (left) with the new link
   * @param params
   */
  function updateDokuwikiFrame(params) {
    var url = dokuwikiUrl;
    if (params !== null && params !== '') {
      url += '?' + params;
    }
    document.getElementById('iframe_left').src = url;
  }

  /**
   * Retrieve parameter by name from the given url
   * @param name
   * @param url
   * @returns {*}
   */
  function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  }

  /**
   * Get the url parameters from the given message.
   * @param message
   * @returns {*}
   */
  function getUrlParams(message) {
    var indexOf = message.data.indexOf('?');
    if (indexOf === -1) return '';
    return message.data.substring(indexOf + 1);
  }

  /**
   * Update the url of the top level page
   * @param params
   */
  function updatePageUrl(params) {
    var url = pageUrl;
    if (params !== null && params !== '') {
      url += '?doku=' + encodeURIComponent(params);
    }
    window.history.replaceState({}, '', url);
  }

  // Get the page url
  var pageUrl = window.location.href;
  var pageUrlLoc = pageUrl.indexOf('?');
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
    if (event.data.type === 'wiki_update') {
      if (message.data.startsWith(dokuwikiUrl)) {
        var params = getUrlParams(message);
        updateDokuwikiFrame(params);
      } else {
        document.getElementById('iframe_left').src = message.data;
      }
    }

    // Forward to other frames
    document.getElementById('iframe_left').contentWindow.postMessage(message, '*');
    document.getElementById('iframe_right').contentWindow.postMessage(message, '*');
  });
});
