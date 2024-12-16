<?php

namespace Aimocs\Iis\Flat\Router;

class RouteParser
{
    /**
     * Parses a route template and a given URL, normalizing the URL and extracting arguments.
     *
     * @param string $template The route template (e.g., "/user/{id}/profile/{section}").
     * @param string $url The actual URL (e.g., "/user/121/profile/settings").
     * @return array|null Returns an array with `normalized` and `arguments`, or null if no match.
     */
    public function parse(string $template, string $url): ?array
    {
        $pattern = preg_replace_callback(
            '/\{(\w+)\}/',
            function ($matches) {
                return '(?P<' . $matches[1] . '>[^/]+)';
            },
            $template
        );

        // Match the URL against the regex pattern
        $regex = '~^' . $pattern . '$~';
        if (preg_match($regex, $url, $matches)) {
            // Extract named arguments
            $arguments = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            return [
                'normalized' => $template,
                'arguments' => $arguments,
            ];
        }

        return null;
    }
}
