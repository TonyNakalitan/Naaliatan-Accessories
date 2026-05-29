<?php
// Patches vendor/knpuniversity/oauth2-client-bundle to remove deprecated Request::get() usage
// See: https://github.com/knpuniversity/oauth2-client-bundle (v2.20.2 still uses deprecated call)

$file = __DIR__ . '/../vendor/knpuniversity/oauth2-client-bundle/src/Client/OAuth2Client.php';

if (!file_exists($file)) {
    echo "Patch target not found, skipping.\n";
    exit(0);
}

$content = file_get_contents($file);

$old = 'return $request->query->has($key) ? $request->query->get($key) : $request->request->get($key);';
$new = 'return $request->query->has($key) ? $request->query->get($key) : ($request->request->has($key) ? $request->request->get($key) : null);';

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "Patched OAuth2Client.php: removed deprecated Request::get() usage.\n";
} else {
    echo "OAuth2Client.php already patched or changed upstream, skipping.\n";
}
