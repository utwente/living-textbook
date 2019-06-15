/**
 * Add browser toggle handlers
 *
 * $ has been defined globally in the app.js
 */
(function (btoggles, eDispatch, $) {

  let $containers, $toggles;
  let $conceptToggleContainers, $conceptToggles, $learningPathToggleContainers, $learningPathToggles;
  let conceptState = false, learningPathState = null;
  let initialized = false, replayCalls = [];

  /**
   * Initializer function
   */
  $(function () {
    // Find toggle buttons
    $conceptToggleContainers = $('.concept-browser-toggle');
    $conceptToggles = $conceptToggleContainers.find('input');
    $learningPathToggleContainers = $('.learning-path-browser-toggle');
    $learningPathToggles = $learningPathToggleContainers.find('input');

    $containers = $conceptToggleContainers.add($learningPathToggleContainers);
    $toggles = $conceptToggles.add($learningPathToggles);

    // Make them fully disabled
    setDisabled($containers, $toggles, true);

    // Load change handlers
    $conceptToggles.change(function () {
      let newValue = $(this).prop('checked');
      if (newValue === conceptState) {
        return;
      }
      conceptState = newValue;

      if (conceptState) {
        eDispatch.openConceptBrowser();
      } else {
        eDispatch.closeConceptBrowser();
      }
    });
    $learningPathToggles.change(function () {
      let newValue = $(this).prop('checked');
      if (newValue === learningPathState) {
        return;
      }
      learningPathState = newValue;

      if (learningPathState) {
        eDispatch.openLearningPathBrowser();
      } else {
        eDispatch.closeLearningPathBrowser();
      }
    });

    initialized = true;
    for (let i = 0; i < replayCalls.length; i++) {
      replayCalls[i]();
    }
    replayCalls = [];
  });

  /**
   * Load the state
   * @param state
   */
  btoggles.loadState = function (state) {
    if (!initialized) {
      replayCalls.push(function () {
        btoggles.loadState(state);
      });
      return;
    }

    setDisabled($conceptToggleContainers, $conceptToggles, false);
    this.loadConceptState(state.concept);

    if (state.learningPath != null) {
      this.loadLearningPathState(state.learningPath);
    }
  };

  /**
   * Load concept state
   * @param isOpened
   */
  btoggles.loadConceptState = function (isOpened) {
    if (!initialized) {
      replayCalls.push(function () {
        btoggles.loadConceptState(isOpened);
      });
      return;
    }

    conceptState = isOpened;
    setToggleValue($conceptToggles, isOpened);
  };

  /**
   * Load learning path state
   * @param isOpened
   */
  btoggles.loadLearningPathState = function (isOpened) {
    if (!initialized) {
      replayCalls.push(function () {
        btoggles.loadLearningPathState(isOpened);
      });
      return;
    }

    if (learningPathState === null) {
      $learningPathToggleContainers.removeClass('d-none');
      setDisabled($learningPathToggleContainers, $learningPathToggles, false);
    }
    learningPathState = isOpened;
    setToggleValue($learningPathToggles, isOpened);
  };

  /**
   * Set disabled state for a toggle
   * @param $containers
   * @param $toggles
   * @param isDisabled
   */
  function setDisabled($containers, $toggles, isDisabled) {
    $containers.each(function () {
      const $container = $(this);
      $container.tooltip(isDisabled ? 'disable' : 'enable');
      if (isDisabled) {
        $container.addClass('disabled');
        $container.find('.btn').addClass('disabled');
      } else {
        $container.removeClass('disabled');
        $container.find('.btn').removeClass('disabled');
      }
    });

    $toggles.each(function () {
      $(this).prop('disabled', isDisabled);
    });
  }

  /**
   * Set the toggle value
   * @param $toggles
   * @param checked
   */
  function setToggleValue($toggles, checked) {
    $toggles.each(function () {
      const $toggle = $(this);
      if ($toggle.prop('checked') !== checked) {
        $toggle.prop('checked', checked).change();
      }
    });
  }

}(window.btoggles = window.btoggles || {}, window.eDispatch, $));
