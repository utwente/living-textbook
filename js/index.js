var dokuwikiUrl = 'https://itc-giscience.utwente.nl/doku.php';
crosstab.PING_TIMEOUT = 10000;

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

  // Install handler to update src
  crosstab.on('wiki_update', function (message) {
    if (message.data.startsWith(dokuwikiUrl)) {
      var params = getUrlParams(message);
      updateDokuwikiFrame(params);
    } else {
      document.getElementById("iframe_left").src = message.data;
    }
  });

  // Whenever a wiki page is loaded, update our url
  crosstab.on('wiki_load', function (message) {
    updatePageUrl(getUrlParams(message));
  });
});


$(window).on('unload', function() {
  window.localStorage.removeItem('crosstab.MESSAGE_KEY');
  window.localStorage.removeItem('crosstab.TABS_KEY');
  window.localStorage.removeItem('crosstab.SUPPORTED');
  window.localStorage.removeItem('crosstab.FROZEN_TAB_ENVIRONMENT');
});
