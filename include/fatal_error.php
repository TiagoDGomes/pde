<?php

isset($PDE) or die('Nope');

function format_error( $errno, $errstr, $errfile, $errline ) {
    $trace = print_r( debug_backtrace( false ), true );

    $content = "
    <table>
        <thead><th>Item</th><th>Description</th></thead>
        <tbody>
            <tr>
                <th>Error</th>
                <td><pre>$errstr</pre></td>
            </tr>
            <tr>
                <th>Errno</th>
                <td><pre>$errno</pre></td>
            </tr>
            <tr>
                <th>File</th>
                <td>$errfile</td>
            </tr>
            <tr>
                <th>Line</th>
                <td>$errline</td>
            </tr>
            <tr>
                <th>Trace</th>
                <td><pre>$trace</pre></td>
            </tr>
        </tbody>
    </table>";
    return $content;
}
function fatal_handler() {

    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if($error !== NULL) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];        
    }
    exit(format_error( $errno, $errstr, $errfile, $errline));
}

register_shutdown_function( "fatal_handler");

