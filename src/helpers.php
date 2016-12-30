<?php
if (!function_exists('bigquery')) {
    function bigquery()
    {
        return app('bigQuery');
    }
}

if (!function_exists('datastore')) {
    function datastore()
    {
        return app('datastore');
    }
}

if (!function_exists('logging')) {
    function logging()
    {
        return app('logging');
    }
}

if (!function_exists('naturalLanguage')) {
    function naturallanguage()
    {
        return app('naturalLanguage');
    }
}

if (!function_exists('pubsub')) {
    function pubsub()
    {
        return app('pubsub');
    }
}

if (!function_exists('speech')) {
    function speech()
    {
        return app('speech');
    }
}

if (!function_exists('storage')) {
    function storage()
    {
        return app('storage');
    }
}

if (!function_exists('vision')) {
    function vision()
    {
        return app('vision');
    }
}

if (!function_exists('translate')) {
    function translate()
    {
        return app('translate');
    }
}

