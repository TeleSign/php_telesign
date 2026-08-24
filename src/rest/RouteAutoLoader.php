<?php
namespace telesign\sdk\rest;

trait RouteAutoLoader
{
    protected function loadAutomatedRoutes()
    {
        $parts = explode('\\', get_class($this));

        array_pop($parts);

        $module = end($parts);
        $namespace = implode('\\', $parts);

        $className = str_replace(' ','', ucwords(str_replace(array('_', '-'), ' ', $module))). 'Routes';

        $routeClass = $namespace . '\\' . $className;

        $routeMap = array();

        if (class_exists($routeClass)) {
            $routeMap = $routeClass::$map;
        }

        if (strpos($routeClass, 'telesign\\enterprise\\sdk\\') !== 0) {
            return $routeMap;
        }

        $selfServiceRouteClass = str_replace('telesign\\enterprise\\sdk\\','telesign\\sdk\\', $routeClass);

        if (!class_exists($selfServiceRouteClass)) {
            return $routeMap;
        }

        return array_merge(
            $selfServiceRouteClass::$map,
            $routeMap
        );
    }
}