<?php

spl_autoload_register(function($class) {
		if ( 0 == strpos($class, 'PFDB') ) {
			$filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
			if ( is_file($filename) ) {
				include $filename;
			}
		}
	});