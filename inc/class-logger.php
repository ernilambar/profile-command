<?php

namespace runcommand\Profile;

class Logger {

	public $execution_time = 0;
	public $query_count = 0;
	public $query_time = 0;
	public $hook_count = 0;
	public $hook_time = 0;

	private $start_time = null;
	private $query_offset = null;
	private $hook_start_time = null;

	public static $active_loggers = array();

	public function __construct( $type, $name ) {
		$this->$type = $name;
	}

	/**
	 * Start this logger
	 */
	public function start() {
		global $wpdb;
		$this->start_time = microtime( true );
		$this->query_offset = ! empty( $wpdb->queries ) ? count( $wpdb->queries ) : 0;
		if ( false === ( $key = array_search( $this, self::$active_loggers ) ) ) {
			self::$active_loggers[] = $this;
		}
	}

	/**
	 * Stop this logger
	 */
	public function stop() {
		global $wpdb;

		if ( ! is_null( $this->start_time ) ) {
			$this->execution_time += microtime( true ) - $this->start_time;
		}
		if ( ! is_null( $this->query_offset ) ) {
			for ( $i = $this->query_offset; $i < count( $wpdb->queries ); $i++ ) {
				$this->query_time += $wpdb->queries[ $i ][1];
				$this->query_count++;
			}
		}

		$this->start_time = null;
		$this->query_offset = null;
		if ( false !== ( $key = array_search( $this, self::$active_loggers ) ) ) {
			unset( self::$active_loggers[ $key ] );
		}
	}

	/**
	 * Start this logger's hook timer
	 */
	public function start_hook_timer() {
		$this->hook_count++;
		$this->hook_start_time = microtime( true );
	}

	/**
	 * Stop this logger's hook timer
	 */
	public function stop_hook_timer() {
		if ( ! is_null( $this->hook_start_time ) ) {
			$this->hook_time += microtime( true ) - $this->hook_start_time;
		}
		$this->hook_start_time = null;
	}

}
