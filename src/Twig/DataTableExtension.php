<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class DataTableExtension
 *
 * This extension ensures that the correct options are loaded for
 * the DataTable extension
 *
 * @author BobV
 */
class DataTableExtension extends AbstractExtension
{
  /**
   * @var TranslatorInterface
   */
  private $translator;

  /**
   * DataTableExtension constructor.
   *
   * @param TranslatorInterface $translator
   */
  public function __construct(TranslatorInterface $translator)
  {
    $this->translator = $translator;
  }

  /**
   * @return array
   */
  public function getFunctions()
  {
    return [
        new TwigFunction('dataTable', [$this, 'dataTable'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * @param       $tableId
   * @param array $options
   *
   * @return string
   */
  public function dataTable($tableId, $options = [])
  {
    // Merge options with default ones
    $options = array_merge($this->getDefaultDataTableOptions(), $options);

    // Load translations
    $options = array_merge($this->getDutchDataTableTranslation(), $options);

    // Generate JS with token
    $return = '<script type="text/javascript">';
    $return .= '$(\'#' . $tableId . '\').DataTable(' . json_encode($options) . ');';
    $return .= '</script>';

    // Return JS
    return trim(preg_replace('/\s+/', ' ', $return));
  }

  private function getDefaultDataTableOptions()
  {
    return [
        'buttons'    => [],
        'lengthMenu' => [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, $this->translator->trans('datatable.all')],
        ],
        'pageLength' => 25,
        'responsive' => true,
    ];
  }

  private function getDutchDataTableTranslation()
  {
    $translations = [
        'language' => [
            'sProcessing'     => 'Bezig...',
            'sLengthMenu'     => '_MENU_ resultaten weergeven',
            'sZeroRecords'    => 'Geen resultaten gevonden',
            'sInfo'           => '_START_ tot _END_ van _TOTAL_ resultaten',
            'sInfoEmpty'      => 'Geen resultaten om weer te geven',
            'sInfoFiltered'   => ' (gefilterd uit _MAX_ resultaten)',
            'sInfoPostFix'    => NULL,
            'sSearch'         => 'Zoeken:',
            'sEmptyTable'     => 'Geen resultaten aanwezig in de tabel',
            'sInfoThousands'  => '.',
            'sLoadingRecords' => 'Een moment geduld aub - bezig met laden...',
            'oPaginate'       => [
                'sFirst'    => 'Eerste',
                'sLast'     => 'Laatste',
                'sNext'     => 'Volgende',
                'sPrevious' => 'Vorige',
            ],
            'oAria'           => [
                'sSortAscending'  => ': activeer om kolom oplopend te sorteren',
                'sSortDescending' => ': activeer om kolom aflopend te sorteren',
            ],
        ],
    ];

    foreach ($translations['language'] as $key => $value) {
      if (is_array($translations['language'][$key])) {
        foreach ($translations['language'][$key] as $key2 => $value2) {
          $translations['language'][$key][$key2] = $value2 !== NULL
              ? $this->translator->trans('datatable.' . $key . '.' . $key2)
              : '';
        }
      } else {
        $translations['language'][$key] = $value !== NULL
            ? $this->translator->trans('datatable.' . $key)
            : '';
      }
    }

    return $translations;
  }
}
