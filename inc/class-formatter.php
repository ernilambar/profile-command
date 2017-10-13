<?php

namespace runcommand\Profile;

class Formatter {

	private $formatter;

	private $args;

	private $total_cell_index;

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

		if ( 'time' !== $fields[0] ) {
			$this->total_cell_index = array_search( $fields[0], $format_args['fields'] );
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
	public function display_items( $items, $include_total = true, $order, $orderby ) {
		if ( 'table' === $this->args['format'] && empty( $this->args['field'] ) ) {
			$this->show_table( $order, $orderby, $items, $this->args['fields'], $include_total );
		} else {
			$this->formatter->display_items( $items );
		}
	}

	/**
	 * Function to compare floats.
	 *
	 * @param double $a Floating number.
	 * @param double $b Floating number.
	 */
	private function compare_float( $a, $b ) {
		$a = number_format( $a, 4 );
		$b = number_format( $b, 4 );
		if ( 0 === $a - $b ) {
			return 0;
		} else if ( $a - $b < 0 ) {
			return -1;
		} else {
			return 1;
		}
	}

	/**
	 * Show items in a \cli\Table.
	 *
	 * @param array $items
	 * @param array $fields
	 */
	private function show_table( $order, $orderby, $items, $fields, $include_total ) {
		$table = new \cli\Table();

		$enabled = \cli\Colors::shouldColorize();
		if ( $enabled ) {
			\cli\Colors::disable( true );
		}

		$table->setHeaders( $fields );

		$totals = array_fill( 0, count( $fields ), null );
		if ( ! is_null( $this->total_cell_index ) ) {
			$totals[ $this->total_cell_index ] = 'total (' . count( $items ) . ')';
		}

		if ( $orderby ) {
			usort( $items, function( $a, $b ) use ( $order, $orderby ) {
				if ( 'ASC' === $order ) {
					if ( is_numeric( $a->$orderby ) && is_numeric( $b->$orderby ) ) {
						return $this->compare_float( $a->$orderby, $b->$orderby );
					} else {
						return strcmp( $a->$orderby, $b->$orderby );
					}
				} elseif ( 'DESC' === $order ) {
					if ( is_numeric( $b->$orderby ) && is_numeric( $a->$orderby ) ) {
						return $this->compare_float( $b->$orderby, $a->$orderby );
					} else {
						return strcmp( $b->$orderby, $a->$orderby );
					}
				}
			});
		}

		$location_index = array_search( 'location', $fields );
		foreach ( $items as $item ) {
			$values = array_values( \WP_CLI\Utils\pick_fields( $item, $fields ) );
			foreach( $values as $i => $value ) {
				if ( ! is_null( $this->total_cell_index ) && $this->total_cell_index === $i ) {
					continue;
				}

				// Ignore 'location' for hook profiling
				if ( false !== $location_index && $location_index === $i ) {
					continue;
				}

				if ( null === $totals[ $i ] ) {
					if ( stripos( $fields[ $i ], '_ratio' ) ) {
						$totals[ $i ] = array();
					} else {
						$totals[ $i ] = 0;
					}
				}
				if ( stripos( $fields[ $i ], '_ratio' ) ) {
					if ( ! is_null( $value ) ) {
						$totals[ $i ][] = $value;
					}
				} else {
					$totals[ $i ] += $value;
				}
				if ( stripos( $fields[ $i ], '_time' ) || 'time' === $fields[ $i ] ) {
					$values[ $i ] = round( $value, 4 ) . 's';
				}
			}
			$table->addRow( $values );
		}
		if ( $include_total ) {
			foreach( $totals as $i => $value ) {
				if ( null === $value ) {
					continue;
				}
				if ( stripos( $fields[ $i ], '_time' ) || 'time' === $fields[ $i ] ) {
					$totals[ $i ] = round( $value, 4 ) . 's';
				}
				if ( is_array( $value ) ) {
					if ( ! empty( $value ) ) {
						$totals[ $i ] = round( ( array_sum( $value ) / count( $value ) ), 2 ) . '%';
					} else {
						$totals[ $i ] = null;
					}
				}
			}
			$table->setFooters( $totals );
		}

		foreach( $table->getDisplayLines() as $line ) {
			\WP_CLI::line( $line );
		}

		if ( $enabled ) {
			\cli\Colors::enable( true );
		}
	}
}
