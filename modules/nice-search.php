<?php
/**
 * Redirects search results from /?s=query to /search/query/, converts %20 to +
 *
 * @link http://txfx.net/wordpress-plugins/nice-search/
 *
 * You can enable/disable this feature in functions.php (or lib/config.php if you're using Roots):
 * add_theme_support('soil-nice-search');
 */
function soil_nice_search_redirect() {
  global $wp_rewrite;
  if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks()) {
    return;
  }

  $search_base = $wp_rewrite->search_base;
  if (is_search() && !is_admin() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false) {

    // Get all query vars
    global $wp_query;
    $query_vars = $wp_query->query;

    // Handle main search query string
    $search_string = urlencode($query_vars['s']);
    unset($query_vars['s']);

    // Prepare for extra query vars
    if (count($query_vars) > 0) {
      $search_string .= '?';
    }

    // Append extra query vars
    foreach ($query_vars as $key => $value) {
      if ($key != 's') {
        $search_string .= urlencode($key) . '=' . urlencode($value) . '&';
      }
    }

    // Trim trailing ampersand
    $search_string = rtrim($search_string, "&");

    // Redirect
    wp_redirect(home_url("/{$search_base}/" . $search_string));

    exit();
  }
}
add_action('template_redirect', 'soil_nice_search_redirect');
