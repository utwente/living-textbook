require('../css/content/content.scss');

global.inDoubleColumn = false;
export const inDoubleColumnChecksum = Math.random().toString(36);

export function setDoubleColumnDetected(checksum) {
  if (checksum === inDoubleColumnChecksum) {
    console.info("DoubleColumn context detected!");
    $('#no-browser-warning').slideUp();
    global.inDoubleColumn = true;
  }
}

function findParent(tag, el) {
  while (el) {
    if ((el.nodeName || el.tagName).toLowerCase() === tag.toLowerCase()) {
      return el;
    }
    el = el.parentNode;
  }
  return null;
}

$(function () {
  require('./content/eventHandler');
  require('./content/eventDispatcher');
  require('./content/customTags');
  require('./content/search');
  require('./content/sortableLearningPaths');

  eDispatch.checkForDoubleColumn(inDoubleColumnChecksum);

  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});

  // Bind to all links
  $(document.body).on('click', function (e) {
    // Disable this behavior if the browser has not been found
    if (!inDoubleColumn) return;

    e = e || event;

    let from = findParent('a', e.target || e.srcElement);
    if (from) {
      let $from = $(from);

      // Exclude _blank target links
      if ($from.attr('target') === '_blank') return;

      // Exclude 'no-link' class from handler
      if ($from.hasClass('no-block')) return;

      // Retrieve url
      let url = $from.attr('href');
      if (typeof url === 'undefined') return;

      // Exclude hash urls
      if (url.startsWith('#')) return;

      // Exclude javascript urls
      if (url.startsWith('javascript')) return;

      // Exclude mailto urls
      if (url.startsWith('mailto:')) return;

      // Build options
      let options = {
        topLevel: $from.hasClass('top-level')
      };

      // Load the new page
      e.preventDefault();
      eDispatch.pageLoad(url, options);
    }
  });

  // Forms on the page to parse
  let $loadedForms = $('form');

  // Send handler to form buttons to determine clicked button
  let $submitButtons = $loadedForms.find(':input[type=submit]');
  $submitButtons.click(function () {
    $submitButtons.removeAttr('clicked');
    $(this).attr('clicked', 'true');
  });

  // Disable submit buttons on submit
  $loadedForms.submit(function () {
    let $form = $(this);
    if ($form.attr('target') === '_blank') return;

    // Find buttons and disable them by binding a new handler
    // If this is not done, the default submit action wouldn't be executed
    $form.find(':input[type=submit]')
        .addClass('disabled')
        .on('click', function (e) {
          e.preventDefault();
          return false;
        });

    let method = $form.attr('method').toUpperCase();
    if (method === undefined) {
      method = 'POST';
    }

    if (inDoubleColumn && method === 'POST') {
      // Make sure we have the updated data from ckeditor
      for (let instanceName in CKEDITOR.instances) {
        if (!CKEDITOR.instances.hasOwnProperty(instanceName)) continue;
        CKEDITOR.instances[instanceName].updateElement();
      }

      // Retrieve action
      let action = $form.attr('action');
      if (action === undefined) {
        action = window.location.href;
      }

      // Serialize form data
      let formData = $form.serialize();

      // Retrieve clicked button data
      formData += '&' + encodeURIComponent($(':input[type=submit][clicked=true]').attr('name')) + '=';

      // Send actual POST request
      $.ajax({
        type: method,
        url: action,
        data: formData
      }).always(function (data) {
        // Before loading the new data, make sure to unload ckeditor instances
        for (let instanceName in CKEDITOR.instances) {
          if (!CKEDITOR.instances.hasOwnProperty(instanceName)) continue;
          CKEDITOR.instances[instanceName].destroy();
        }
        // noinspection JSUndeclaredVariable
        CKEDITOR = undefined;

        // Replace content
        document.open();
        if (data.hasOwnProperty("responseText")) {
          document.write(data.responseText);
        } else {
          document.write(data);
        }
        document.close();
      });

      return false;
    }

    return true;
  });

  // Auto focus first form field
  $loadedForms.find('input').first().focus();

  // Page loaded event
  eDispatch.pageLoaded();

  // Check in double column has been detected (after timeout)
  setTimeout(() => {
    if (!inDoubleColumn) {
      $('#no-browser-warning').slideDown();
    }
  }, 5000)
});
