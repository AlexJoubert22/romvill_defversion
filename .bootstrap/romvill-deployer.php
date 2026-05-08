<?php
/**
 * Plugin Name: Romvill Deployer
 * Description: One-time deploy bootstrap for Romvill theme.
 * Version: 1.0
 * Author: Romvill
 */
add_action('rest_api_init', function() {
    register_rest_route('romvill/v1', '/deploy', [
        'methods'             => 'POST',
        'callback'            => 'romvill_plugin_deploy',
        'permission_callback' => function() { return current_user_can('manage_options'); },
    ]);
});
function romvill_plugin_deploy(WP_REST_Request $request) {
    $files = $request->get_file_params();
    if (empty($files['themezip']['tmp_name'])) {
        return new WP_Error('no_file', 'Missing themezip', ['status' => 400]);
    }
    $zip = new ZipArchive();
    if ($zip->open($files['themezip']['tmp_name']) !== true) {
        return new WP_Error('zip_error', 'Cannot open ZIP', ['status' => 400]);
    }
    $theme_dir = trailingslashit(get_theme_root()) . 'romvill-theme/';
    $n = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        $rel  = preg_replace('#^[^/]+/#', '', $name);
        if ($rel === '' || substr($rel, -1) === '/') continue;
        $dest = $theme_dir . $rel;
        wp_mkdir_p(dirname($dest));
        file_put_contents($dest, $zip->getFromIndex($i));
        $n++;
    }
    $zip->close();
    return ['success' => true, 'files' => $n];
}
