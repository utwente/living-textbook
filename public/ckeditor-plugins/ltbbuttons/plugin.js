/**
 * Based on the basic styles plugin: https://github.com/ckeditor/ckeditor-dev/tree/master/plugins/basicstyles
 */
CKEDITOR.plugins.add( 'ltbbuttons', {
  lang: 'en',
  icons: 'highlight,symbol',
  init: function( editor ) {
    var order = 0;
    // All buttons use the same code to register. So, to avoid
    // duplications, let's use this tool function.
    var addButtonCommand = function( buttonName, buttonLabel, commandName, styleDefiniton ) {
      // Disable the command if no definition is configured.
      if ( !styleDefiniton )
        return;

      var style = new CKEDITOR.style( styleDefiniton ),
          forms = contentForms[ commandName ];

      // Put the style as the most important form.
      forms.unshift( style );

      // Listen to contextual style activation.
      editor.attachStyleStateChange( style, function( state ) {
        !editor.readOnly && editor.getCommand( commandName ).setState( state );
      } );

      // Create the command that can be used to apply the style.
      editor.addCommand( commandName, new CKEDITOR.styleCommand( style, {
        contentForms: forms
      } ) );

      // Register the button, if the button plugin is loaded.
      if ( editor.ui.addButton ) {
        editor.ui.addButton( buttonName, {
          label: buttonLabel,
          command: commandName,
          toolbar: 'ltbbuttons,' + ( order += 10 )
        } );
      }
    };

    var contentForms = {
          highlight: [
            'strong'
          ],

          symbol: [
            'symbol'
          ]
        },
        config = editor.config,
        lang = editor.lang.ltbbuttons;

    addButtonCommand( 'Highlight', lang.highlight, 'highlight', config.coreStyles_highlight );
    addButtonCommand( 'Symbol', lang.symbol, 'symbol', config.coreStyles_symbol );
  }
} );

// Basic Inline Styles.
CKEDITOR.config.coreStyles_highlight = { element: 'strong', overrides: 'b' };
CKEDITOR.config.coreStyles_symbol = { element: 'symbol' };
