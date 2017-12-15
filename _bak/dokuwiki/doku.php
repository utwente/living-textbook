<?php
/**
 * The script forwards the user that uses the old url to the new url page,
 * by retrieving the url params and reformatting the accordingly
 */

function basePageUrl()
{
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") {
    $pageURL .= "s";
  }
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
  } else {
    $pageURL .= $_SERVER["SERVER_NAME"];
  }

  return $pageURL;
}

function forwardToNewUrl($dokuId = "")
{
  $baseUrl = basePageUrl();
  $url     = $dokuId == "" ? $baseUrl : $baseUrl . '/?doku=' . urlencode($dokuId);
  header("HTTP/1.1 301 Moved Permanently");
  header(sprintf("Location: %s", $url));
  exit();
}

forwardToNewUrl($_SERVER['QUERY_STRING']);
exit();
