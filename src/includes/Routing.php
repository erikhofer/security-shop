<?php

class Routing
{
    /**
     * Return an absolute path to given site name inside /pages
     * This does not check, if the file actually exists
     *
     * @param string $site Site name without file extension
     * @return string
     */
    public static function getPagesPath($site)
    {
        // sanitize site name so no files outside of /pages are included
        // this disallows everything except A-z0-9_- characters as filename
        $site = preg_replace('[^\w\-]+', '', $site);
        return __DIR__ . '/../pages/' . $site . '.php';
    }

    /**
     * Include the site from /pages given in $site or 404 if the site does not exist
     * in /pages directory
     *
     * @param string $site
     */
    public static function includeSite($site = 'home')
    {
        $path = static::getPagesPath($site);
        if (!file_exists($path) || !is_file($path)) {
            http_response_code(404);
            $path = static::getPagesPath('404');
        }
        include $path;
    }

    /**
     * Returns the site that is given in the request
     *
     * @param array $server The $_SERVER global variable
     * @param string $defaultSite A default site, if no site is set in the request
     * @return string
     */
    public static function getSiteFromRequest($server, $defaultSite = 'home')
    {
        if (!isset($server['QUERY_STRING'])) {
            return $defaultSite;
        }

        parse_str($server['QUERY_STRING'], $query);
        if (isset($query['site'])) {
            return $query['site'];
        } else {
            return $defaultSite;
        }
    }

    /**
     * Get an URL to the given site from pages
     *
     * @param string $site The site from pages to link to
     * @param array $additionalParams Any additional parameters, that should be added to the query
     * @return string
     */
    public static function getUrlToSite($site, $additionalParams = [])
    {
        $params = ['site' => $site] + $additionalParams;
        $query = http_build_query($params);
        return 'index.php?' . $query;
    }

    /**
     * Redirect user to $site
     *
     * @param string $site
     */
    public static function redirect($site)
    {
        $url = self::getUrlToSite($site);
        header('Location: ' . $url, true, 301);
    }
}