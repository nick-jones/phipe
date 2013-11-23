<?php

namespace Phipe;

spl_autoload_register(function($className) {
	if (strpos($className, 'Phipe\\') === 0) {
		require strtr($className, '\\', DIRECTORY_SEPARATOR) . '.php';
	}
});