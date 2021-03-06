require('../css/content/content.scss');

global.inDoubleColumn = false;
export const inDoubleColumnChecksum = Math.random().toString(36);

export function setDoubleColumnDetected(checksum) {
  if (checksum === inDoubleColumnChecksum) {
    console.info('DoubleColumn context detected!');
    $('#no-browser-warning').slideUp();
    global.inDoubleColumn = true;
    window.dispatchEvent(new CustomEvent('double_column_detected'));
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

String.prototype.trunc = String.prototype.trunc ||
    function (n) {
      return (this.length > n) ? this.substr(0, n) + '...' : this;
    };

$(function () {
  require('./content/eventHandler');
  require('./content/eventDispatcher');
  require('./content/annotations');
  require('./content/customTags');
  require('./content/search');
  require('./content/sortableLearningPaths');
  require('./content/browserToggles');
  require('./content/tags');

  eDispatch.checkForDoubleColumn(inDoubleColumnChecksum);

  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});

  // Bind to all links
  $(document.body).on('click', function (e) {
    // Disable this behavior if the browser has not been found
    if (!inDoubleColumn) return;

    e = e || event;

    let from = findParent('a', e.target || e.srcElement);
    if (from) {
      // Retrieve url
      let $from = $(from);
      let url = $from.attr('href');
      if (typeof url === 'undefined') return;

      // Exclude _blank target links
      if ($from.attr('target') === '_blank') {
        eDispatch.blankPageLoad(url);
        return;
      }

      // Exclude links which are not within our domain, and open those in a new page
      if (window.location.hostname !== from.hostname && from.hostname.length > 0) {
        e.preventDefault();
        window.open(url, '_blank');
        eDispatch.blankPageLoad(url);
        return;
      }

      // Exclude 'no-link' class from handler
      if ($from.hasClass('no-block')) return;

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
  let $loadedForms = $('form:not(".ignore-submit")');

  // Send handler to form buttons to determine clicked button
  let $submitButtons = $loadedForms.find(':input[type=submit]');
  $submitButtons.click(function () {
    $submitButtons.removeAttr('clicked');
    $(this).attr('clicked', 'true');
  });

  // Disable submit buttons on submit
  $loadedForms.submit(function () {
    let $form = $(this);
    let target = $form.attr('target');
    if (target === '_blank' || target === '_parent') return;

    // Find buttons and disable them by binding a new handler
    // If this is not done, the default submit action wouldn't be executed
    $form.find(':input[type=submit]')
        .addClass('disabled')
        .on('click', function (e) {
          e.preventDefault();
          return false;
        });

    let method = $form.attr('method');
    if (typeof method !== 'string') {
      method = 'POST';
    } else {
      method = method.toUpperCase();
    }

    if (inDoubleColumn && method === 'POST') {
      // Make sure we have the updated data from ckeditor
      if (typeof CKEDITOR !== 'undefined') {
        for (let instanceName in CKEDITOR.instances) {
          if (!CKEDITOR.instances.hasOwnProperty(instanceName)) continue;
          CKEDITOR.instances[instanceName].updateElement();
        }
      }

      // Retrieve action
      let action = $form.attr('action');
      if (typeof action !== 'string') {
        action = window.location.href;
      }

      let formData = new FormData($form[0]);

      // Retrieve clicked button and put it in the form data
      let clickedButton = $(':input[type=submit][clicked=true]');
      formData.append(clickedButton.attr('name'), '');

      // Send actual POST request
      $.ajax({
        type: method,
        url: action,
        data: formData,
        processData: false,
        contentType: false,
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
        if (data.hasOwnProperty('responseText')) {
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

  // Fix file selector label text
  $loadedForms.find('.custom-file-input').on('change', function () {
    let files = $(this)[0].files;
    let $fileLabel = $('label[for="' + $(this).attr('id') + '"].custom-file-label');
    if (files.length > 0) {
      $fileLabel.text($(this)[0].files[0].name);
    } else {
      $fileLabel.text('');
    }
  });

  // Auto focus first form field
  $loadedForms.find('input').first().focus();

  // Select2 auto search select fix, caused by jQuery 3.6
  // See https://github.com/select2/select2/issues/5993
  $(document).on('select2:open', () => {
    const allFound = document.querySelectorAll('.select2-container--open .select2-search__field');
    allFound[allFound.length - 1].focus();
  });

  // Page loaded event
  eDispatch.pageLoaded();

  // Check in double column has been detected (after timeout)
  setTimeout(() => {
    if (!inDoubleColumn) {
      $('#no-browser-warning').slideDown({
        done: function () {
          $(window).trigger('resize');
        }
      });
    }
  }, 5000);
});
