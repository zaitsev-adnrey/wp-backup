<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Создание бэкапов в заданом каталоге 
 *
 * @when before_wp_load
 */
$backup_command = function($args) {
					$catalog = $args[0];
	                $find_options = array(
                      'return'     => true,   // Return 'STDOUT'; use 'all' for full object.
                      'parse'      => 'json', // Parse captured STDOUT to JSON array.
                      'launch'     => false,  // Reuse the current process.
                      'exit_error' => true,   // Halt script execution on error.
                    );
	$paths = WP_CLI::runcommand('find '. $catalog .'  --field=wp_path --format=json', $find_options);
	$options = array('return'     => true,'exit_error' => false, );
	$progress = \WP_CLI\Utils\make_progress_bar( 'Creating backup', count($paths) );
	
	foreach ($paths as $key => $path) {
			//$multisite = WP_CLI::runcommand('config get MULTISITE --path=' . $path .' ',$options);
		WP_CLI::line( WP_CLI::colorize("%c$path%n" .'%y a backup creating%n'));	
		WP_CLI::runcommand('db export --path=' . $path .' ',array('return'     => false,));		
		WP_CLI::line( WP_CLI::colorize("%c$path%n" .' a backup copy was created'));
		$progress->tick();	
	}
	$progress->finish();
	
};
WP_CLI::add_command( 'backup', $backup_command  );
