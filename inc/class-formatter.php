<?php

namespace runcommand\Profile;

class Formatter {

	private $formatter;

	private $args;

	public function __construct( &$assoc_args, $fields = null, $prefix = false ) {
		$format_args = array(
			'format' => 'table',
			'fields' => $fields,
			'field' => null
		);

		foreach ( array( 'format', 'fields', 'field' ) as $key ) {
			if ( isset( $assoc_args[ $key ] ) ) {
				$format_args[ $key ] = $assoc_args[ $key ];
			}
		}

		if ( ! is_array( $format_args['fields'] ) ) {
			$format_args['fields'] = explode( ',', $format_args['fields'] );
		}

		$format_args['fields'] = array_map( 'trim', $format_args['fields'] );

		$this->args = $format_args;
		$this->formatter = new \WP_CLI\Formatter( $assoc_args, $fields, $prefix );
	}

	/**
	 * Display multiple items according to the output arguments.
	 *
	 * @param array $items
	 */
	public function display_items( $items ) {
		if ( 'table' === $this->args['format'] && empty( $this->args['field'] ) ) {
			self::show_table( $items, $this->args['fields'] );
		} else {
			$this->formatter->display_items( $items );
		}
	}

	/**
	 * Show items in a \cli\Table.
	 *
	 * @param array $items
	 * @param array $fields
	 */
	private static function show_table( $items, $fields ) {
		$table = new \cli\Table();

		$enabled = \cli\Colors::shouldColorize();
		if ( $enabled ) {
			\cli\Colors::disable( true );
		}

		$table->setHeaders( $fields );

		$totals = array(
			'total',
		);
		foreach ( $items as $item ) {
			$values = array_values( \WP_CLI\Utils\pick_fields( $item, $fields ) );
			foreach( $values as $i => $value ) {
				if ( 0 === $i ) {
					continue;
				}
				if ( ! isset( $totals[ $i ] ) ) {
					if ( is_array( $value ) ) {
						$totals[ $i ] = array();
					} else {
						$totals[ $i ] = 0;
					}
				}
				if ( is_array( $value ) ) {
					$new_value = '';
					foreach( $value as $k => $j ) {
						if ( ! isset( $totals[ $i ][ $k ] ) ) {
							$totals[ $i ][ $k ] = 0;
						}
						$totals[ $i ][ $k ] += $j;
						if ( 'time' === $k ) {
							$j = round( $j, 4 ) . 's';
						}
						$new_value .= "{$j} / ";
					}
					$values[ $i ] = rtrim( $new_value, '/ ' );
				} else {
					$totals[ $i ] += $value;
					if ( stripos( $fields[ $i ], '_time' ) || 'time' === $fields[ $i ] ) {
						$values[ $i ] = round( $value, 4 ) . 's';
					}
				}
			}
			$table->addRow( $values );
		}
		foreach( $totals as $i => $value ) {
			if ( stripos( $fields[ $i ], '_time' ) || 'time' === $fields[ $i ] ) {
				$totals[ $i ] = round( $value, 4 ) . 's';
			}
			if ( is_array( $value ) ) {
				$new_value = '';
				foreach( $value as $k => $j ) {
					if ( 'time' === $k ) {
						$j = round( $j, 4 ) . 's';
					}
					$new_value .= "{$j} / ";
				}
				$totals[ $i ] = rtrim( $new_value, '/ ' );
			}
		}
		$table->setFooters( $totals );

		foreach( $table->getDisplayLines() as $line ) {
			\WP_CLI::line( $line );
		}

		if ( $enabled ) {
			\cli\Colors::enable( true );
		}
	}
}
