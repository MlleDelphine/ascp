<?php

$config = load_config(__DIR__ . '/../apc_reset.ini');

if(!auth($config)) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');

    print_response(array(
        'success' => false,
        'message' => 'Bad credentials'
    ));
}

print_response(clear($config['clearApc'], $config['clearOpcache']));

function print_response($datas) {
    die(json_encode($datas));
}

function load_config($file) {
    $config = array();

    if(file_exists($file)) {
        $parsed_config = parse_ini_file($file);
        $config = $parsed_config ? $parsed_config : array();
    }

    return array_merge(array(
        'clearApc' => true,
        'clearOpcache' => false
    ), $config);
}

function auth($config) {
    $username = null;
    $password = null;

    if(!isset($config['username'])) {
        return true;
    }

    // mod_php
    if (isset($_SERVER['PHP_AUTH_USER'])) {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // most other servers
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {

        if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
            list($username,$password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    }

    if (is_null($username)) {

        return false;

    } else if ($username === $config['username'] && $password === $config['password']) {

        return true;
    }

    return false;
}

function clear($clearApc = true, $clearOpcache = false) {
    $message = '';
    $success = true;

    if($clearApc) {
        if (function_exists('apc_clear_cache') && version_compare(PHP_VERSION, '5.5.0', '>=') && apc_clear_cache()) {
            $message .= ' User Cache: success';
        }
        elseif (function_exists('apc_clear_cache') && version_compare(PHP_VERSION, '5.5.0', '<') && apc_clear_cache('user')) {
            $message .= ' User Cache: success';
        }
        else {
            $success = false;
            $message .= ' User Cache: failure';
        }
    }

    if($clearOpcache) {
        if (function_exists('opcache_reset') && opcache_reset()) {
            $message .= ' Opcode Cache: success';
        }
        elseif (function_exists('apc_clear_cache') && version_compare(PHP_VERSION, '5.5.0', '<') && apc_clear_cache('opcode')) {
            $message .= ' Opcode Cache: success';
        }
        else {
            $success = false;
            $message .= ' Opcode Cache: failure';
        }
    }

    return array(
        'success' => $success,
        'message' => $message
    );
}

