<?php
/**
 * DokuWiki Plugin conceptbrowserlinker (Syntax Component)
 *
 * @author  B. van de Vijver <bob@drenso.nl>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_conceptbrowserlinker extends DokuWiki_Syntax_Plugin
{
  /**
   * @return string Syntax mode type
   */
  public function getType()
  {
    return 'substition';
  }

  /**
   * @return string Paragraph type
   */
  public function getPType()
  {
    return 'normal';
  }

  /**
   * @return int Sort order - Low numbers go before high numbers
   */
  public function getSort()
  {
    return 35;
  }

  /**
   * Connect lookup pattern to lexer.
   *
   * @param string $mode Parser mode
   */
  public function connectTo($mode)
  {
    $this->Lexer->addEntryPattern('<cm.*?>(?=.*?</cm>)', $mode, 'plugin_conceptbrowserlinker');
    $this->Lexer->addEntryPattern('<cb.*?>(?=.*?</cb>)', $mode, 'plugin_conceptbrowserlinker');
  }

  public function postConnect()
  {
    $this->Lexer->addExitPattern('</cm>', 'plugin_conceptbrowserlinker');
    $this->Lexer->addExitPattern('</cb>', 'plugin_conceptbrowserlinker');
  }

  /**
   * Handle matches of the conceptbrowserlinker syntax
   *
   * @param string       $match   The match of the syntax
   * @param int          $state   The state of the handler
   * @param int          $pos     The position in the document
   * @param Doku_Handler $handler The handler
   *
   * @return array Data for the renderer
   */
  public function handle($match, $state, $pos, Doku_Handler $handler)
  {
    switch ($state) {
      case DOKU_LEXER_ENTER:
        list($link) = preg_split("/\//u", substr($match, 3, -1), 2);
        return array($state, hsc(trim($link)));
      case DOKU_LEXER_UNMATCHED:
        return array($state, $match);
      case DOKU_LEXER_EXIT:
        return array($state, '');
    }

    return array();
  }

  /**
   * Render xhtml output or metadata
   *
   * @param string        $mode     Renderer mode (supported modes: xhtml)
   * @param Doku_Renderer $renderer The renderer
   * @param array         $data     The data from the handler() function
   *
   * @return bool If rendering was successful.
   */
  public function render($mode, Doku_Renderer $renderer, $data)
  {
    if ($mode != 'xhtml') return false;

    list($state, $match) = $data;
    switch ($state) {
      case DOKU_LEXER_ENTER:
        $renderer->doc .= '<span class="concept-browser-link" data-cb-link="' . $match . '">';
        break;
      case DOKU_LEXER_UNMATCHED:
        $renderer->doc .= $renderer->_xmlEntities($match);
        break;
      case DOKU_LEXER_EXIT:
        $renderer->doc .= "</span>";
        break;
    }

    return true;
  }
}

// vim:ts=4:sw=4:et:
