<?php

/*
 *
 */

namespace Search\Controllers;

use SolrIndex;
use BaseController;
use Response;

class SearchesController extends BaseController
{

  public function index()
  {
    define('SOLR_HOST_URL', $_REQUEST['solr-host-url']);

    $solr = new SolrIndex($_REQUEST['solr-core']);
    $matches = $solr->rawQuery($_REQUEST['web-name'], $_REQUEST['query'], [], [], [], true);
    $html = array_map(function($item) {
        $result = [];

        if (! empty($item->highlight)) {
          $result[] = '<div class="search_results">';
          $result[] = sprintf('<div class="results_information"><a class="chapter_title" href="%s">%s</a></div>',
                              $item->url,
                              $item->title_en[0]);
          $result[] = '<div class="results_chapter_snippets">';

          foreach ($item->highlight as $snippet) {
            $result[] = sprintf('<div class="text_snippets">%s</div>',
                                $snippet);
          }

          $result[] = '</div>';
          $result[] = '</div>';
        }

        return join('', $result);
      }, $matches);

    $result = [
               'r' => $_REQUEST,
               'p' => $matches,
               'f' => $solr->getOutput(),
               'html' => join("\n", $html),
               ];

    return Response::json($result);
  }
}
