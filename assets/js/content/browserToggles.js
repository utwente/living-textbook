/**
 * Add browser toggle handlers
 *
 * $ has been defined globally in the app.js
 */
(function (btoggles, eDispatch, $) {

  let $containers, $toggles;
  let $conceptToggleContainer, $conceptToggle, $learningPathToggleContainer, $learningPathToggle;
  let conceptState = false, learningPathState = null;
  let initialized = false, replayCalls = [];

  /**
   * Initializer function
   */
  $(function () {
    // Find toggle buttons
    $conceptToggleContainer = $('.concept-browser-toggle');
    $conceptToggle = $conceptToggleContainer.find('input');
    $learningPathToggleContainer = $('.learning-path-browser-toggle');
    $learningPathToggle = $learningPathToggleContainer.find('input');

    $containers = $conceptToggleContainer.add($learningPathToggleContainer);
    $toggles = $conceptToggle.add($learningPathToggle);

    // Make them fully disabled
    setDisabled($containers, $toggles, true);

    // Load change handlers
    $conceptToggle.change(function () {
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
    $learningPathToggle.change(function () {
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

    setDisabled($conceptToggleContainer, $conceptToggle, false);
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
    setToggleValue($conceptToggle, isOpened);
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
      $learningPathToggleContainer.removeClass('d-none');
      setDisabled($learningPathToggleContainer, $learningPathToggle, false);
    }
    learningPathState = isOpened;
    setToggleValue($learningPathToggle, isOpened);
  };

  /**
   * Set disabled state for a toggle
   * @param $containers
   * @param $toggles
   * @param isDisabled
   */
  function setDisabled($containers, $toggles, isDisabled) {
    $containers.tooltip(isDisabled ? 'disable' : 'enable');
    if (isDisabled) {
      $containers.addClass('disabled');
      $containers.find('.btn').addClass('disabled');
    } else {
      $containers.removeClass('disabled');
      $containers.find('.btn').removeClass('disabled');
    }
    $toggles.prop('disabled', isDisabled);
  }

  /**
   * Set the toggle value
   * @param $toggle
   * @param checked
   */
  function setToggleValue($toggle, checked) {
    if ($toggle.prop('checked') !== checked) {
      $toggle.prop('checked', checked).change();
    }
  }

}(window.btoggles = window.btoggles || {}, window.eDispatch, $));
