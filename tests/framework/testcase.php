<?php
namespace Pods_Unit_Tests;

require dirname( __FILE__ ) . '/factory.php';

// Components
require PODS_PLUGIN_DIR . '/components/Migrate-Packages/Migrate-Packages.php';
require PODS_PLUGIN_DIR . '/components/Advanced-Content-Types.php';
require PODS_PLUGIN_DIR . '/components/Table-Storage.php';

require PODS_PLUGIN_DIR . '/classes/fields/pick.php';

class Pods_UnitTestCase extends \WP_UnitTestCase {
	/**
	 * A collection of supported pod types
	 * 
	 * @var    array
	 * @static
	 */
	public static $supported_types = array(
		'post_type' => array(
			'object' => array(
				'%d',
			    'post',
			    'page',
			    'nav_menu_item'
			),
		    // @todo Figure out how to split test meta/table for existing objects
			'storage' => array(
				'meta',
			    'table'
			)
		),
	    'taxonomy' => array(
		    'object' => array(
			    '%d',
		        'category',
		        'post_tag',
		        'nav_menu'
		    ),
		    // @todo Figure out how to split test meta/table for existing objects
			'storage' => array(
			    'table',
			    'none'
			)
	    ),
	    'user' => array(
		    // @todo Figure out how to split test meta/table for existing objects
			'storage' => array(
			    'meta',
		        'table'
			),
	        'fields' => array(
		        array(
			        'name' => 'avatar',
			        'type' => 'avatar'
		        )
	        ),
	        'data' => array(
				'display_name' => 'User %s',
				'user_login' => 'User-%s',
				'user_email' => '%s@user.com',
			    'user_pass' => '%s'
	        )
	    ),
	    /*'media' => array(
		    // @todo Figure out how to split test meta/table for existing objects
			'storage' => array(
			    'meta',
		        'table'
			),
	        'data' => array(
		        'guid' => 'http://f.cl.ly/items/1f1e0d0c0D310X1z0m3C/Screen%%20Shot%%202014-11-07%%20at%%201.06.32%%20AM.png'
	        )
	    ),*/
	    'comment' => array(
		    // @todo Figure out how to split test meta/table for existing objects
			'storage' => array(
			    'meta',
		        'table'
			),
	        'data' => array(
				'comment_author' => 'Comment %s',
				'comment_author_email' => '%s@comment.com',
				'comment_author_url' => 'http://comment.com',
				'comment_content' => '%s',
				'comment_post_ID' => 1,
				'comment_type' => 'comment',
			    'post_status' => 'publish',
			    'comment_date' => '2014-11-11 00:00:00'
	        )
	    ),
	    'pod' => array(
		    'object' => array(
			    '%d'
		    ),
			'storage' => array(
		        'table'
			),
	        'fields' => array(
		        array(
			        'name' => 'name',
			        'type' => 'text'
		        ),
		        array(
			        'name' => 'author',
			        'type' => 'pick',
				    'pick_object' => 'user',
				    'pick_val' => 'user',
				    'pick_format_type' => 'single'
		        )
	        )
	    )
	);
	
	/**
	 * A collection of supported field definitions
	 * 
	 * @var    array
	 * @static
	 */
	public static $supported_fields = array(
	    array(
			'name' => 'test_rel_user',
			'type' => 'pick',
		    'pick_object' => 'user',
		    'pick_val' => 'user',
		    'pick_format_type' => 'single'
		),
		array(
			'name' => 'test_rel_post',
			'type' => 'pick',
		    'pick_object' => 'post_type',
		    'pick_val' => 'post',
		    'pick_format_type' => 'single'
		),
	    array(
			'name' => 'test_rel_pages',
			'type' => 'pick',
		    'pick_object' => 'post_type',
		    'pick_val' => 'page',
		    'pick_format_type' => 'multi'
		),
	    array(
			'name' => 'test_rel_tag',
			'type' => 'pick',
		    'pick_object' => 'taxonomy',
		    'pick_val' => 'post_tag',
		    'pick_format_type' => 'single'
		),
	    /*array(
			'name' => 'test_rel_media',
			'type' => 'pick',
		    'pick_object' => 'media',
		    'pick_val' => '',
		    'pick_format_type' => 'single'
		),*/
	    array(
			'name' => 'test_rel_comment',
			'type' => 'pick',
		    'pick_object' => 'comment',
		    'pick_val' => 'comment',
		    'pick_format_type' => 'single'
		),
	    array(
			'name' => 'test_text_field',
			'type' => 'text'
		)
	);

	/**
	 * A collection of preset related field configurations
	 * 
	 * @var    array
	 * @static
	 */
	public static $related_items = array(
	    'test_rel_user' => array(
			'pod' => 'user',
		    'id' => 0,
		    'field_index' => 'display_name',
		    'field_id' => 'ID',
			'data' => array(
				'display_name' => 'Related user',
				'user_login' => 'related-user',
				'user_email' => 'related@user.com',
			    'user_pass' => 'changeme'
			)
		),
		'test_rel_post' => array(
			'pod' => 'post',
		    'id' => 0,
		    'field_index' => 'post_title',
		    'field_id' => 'ID',
		    'field_author' => 'post_author',
			'data' => array(
				'post_title' => 'Related post',
				'post_content' => '%s',
			    'post_status' => 'publish'
			)
		),
	    'test_rel_page' => array(
			'pod' => 'page',
		    'id' => 0,
		    'ids' => array(),
		    'field_index' => 'post_title',
		    'field_id' => 'ID',
		    'field_author' => 'post_author',
		    'limit' => 2,
			'data' => array(
				'post_title' => 'Related page',
				'post_content' => '%s',
			    'post_status' => 'publish'
			)
		),
	    'test_rel_tag' => array(
			'pod' => 'post_tag',
		    'id' => 0,
		    'field_index' => 'name',
		    'field_id' => 'term_id',
		    'field_author' => false,
			'data' => array(
				'name' => 'Related post tag',
				'description' => '%s'
			)
		),
	    /*'test_rel_media' => array(
			'pod' => 'media',
		    'id' => 0,
		    'field_index' => 'post_title',
		    'field_id' => 'ID',
		    'field_author' => 'post_author',
			'data' => array(
				'post_title' => 'Related media',
				'post_content' => '%s',
			    'post_status' => 'publish'
			)
		),*/
	    'test_rel_comment' => array(
			'pod' => 'comment',
		    'id' => 0,
		    'field_index' => 'comment_date',
		    'field_id' => 'comment_ID',
		    'field_author' => 'user_id',
			'data' => array(
				'comment_author' => 'Related comment',
				'comment_author_email' => 'related@comment.com',
				'comment_author_url' => 'http://comment.com',
				'comment_content' => '%s',
				'comment_post_ID' => 1,
				'comment_type' => 'comment',
			    'post_status' => 'publish',
			    'comment_date' => '2014-11-11 00:00:00'
			)
		),
	    '%s' => array(
			'pod' => '%s',
		    'id' => 0,
		    'field_index' => '',
		    'field_id' => '',
		    'field_author' => false,
			'data' => array(
				'index' => 'Testing %s',
				'test_text_field' => 'Testing %s'
			)
		)
	);

	/**
	 * A collection of pre-built pod objects
	 * 
	 * @var    array
	 * @static
	 */
	public static $builds = array(
		/*
		 * 'pod_type' => array(
		 *      'object_name' => array(
		 *          'meta' => array(
		 *              // pod array of info
		 *          ),
		 *          'table' => array(
		 *              // pod array of info
		 *          ),
		 *          'none' => array(
		 *              // pod array of info
		 *          )
		 *      )
		 * )
		 */
	);
	
	public function setUp() {
		parent::setUp();
		$this->factory = new Pods_UnitTest_Factory;

		pods_require_component( 'table-storage' );
		pods_require_component( 'advanced-relationships' );
		pods_require_component( 'migrate-packages' );
		pods_require_component( 'advanced-content-types' );
	}

	public function clean_up_global_scope() {
		parent::clean_up_global_scope();
	}

	public function assertPreConditions() {
		parent::assertPreConditions();
	}

	public function go_to( $url ) {
		$GLOBALS['_SERVER']['REQUEST_URI'] = $url = str_replace( network_home_url(), '', $url );

		$_GET = $_POST = array();

		foreach ( array( 'query_string', 'id', 'postdata', 'authordata', 'day', 'currentmonth', 'page', 'pages', 'multipage', 'more', 'numpages', 'pagenow' ) as $v ) {
			if ( isset( $GLOBALS[ $v ] ) ) unset( $GLOBALS[ $v ] );
		}

		$parts = parse_url($url);

		if ( isset( $parts['scheme'] ) ) {
			$req = $parts['path'];
			if ( isset( $parts['query'] ) ) {
				$req .= '?' . $parts['query'];
				parse_str( $parts['query'], $_GET );
			}
		} else {
			$req = $url;
		}

		if ( ! isset( $parts['query'] ) ) {
			$parts['query'] = '';
		}

		// Scheme
		if ( 0 === strpos( $req, '/wp-admin' ) && force_ssl_admin() ) {
			$_SERVER['HTTPS'] = 'on';
		} else {
			unset( $_SERVER['HTTPS'] );
		}

		$_SERVER['REQUEST_URI'] = $req;
		unset($_SERVER['PATH_INFO']);

		$this->flush_cache();

		unset($GLOBALS['wp_query'], $GLOBALS['wp_the_query']);

		$GLOBALS['wp_the_query'] = new WP_Query();
		$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
		$GLOBALS['wp'] = new WP();

		foreach ( $GLOBALS['wp']->public_query_vars as $v ) {
			unset( $GLOBALS[ $v ] );
		}
		foreach ( $GLOBALS['wp']->private_query_vars as $v ) {
			unset( $GLOBALS[ $v ] );
		}

		$GLOBALS['wp']->main( $parts['query'] );
	}

	public function set_current_user( $user_id ) {
		wp_set_current_user( $user_id );
	}

	public function getReflectionPropertyValue( $class, $property )	{
		$reflection = new \ReflectionProperty( $class, $property );
		$reflection->setAccessible( true );
		return $reflection->getValue( $class );
	}

	public function setReflectionPropertyValue( $class, $property, $value )	{
		$reflection = new \ReflectionProperty( $class, $property );
		$reflection->setAccessible( true );
		return $reflection->setValue( $class, $value );
	}

	public function reflectionMethodInvoke( $class, $method ) {
		$reflection = new \ReflectionMethod( $class, $method );
		$reflection->setAccessible( true );
		return $reflection->invoke( $class );
	}

	public function reflectionMethodInvokeArgs( $class, $method, $args ) {
		$reflection = new \ReflectionMethod( $class, $method );
		$reflection->setAccessible( true );
		return $reflection->invokeArgs( $class, $args );
	}

	/**
	 * Create a full working set of pods
	 */
	public static function _initialize_config() {

		$api = pods_api();

		$test_pod = 1;

		// Loop through supported types and fields and setup test builds
		foreach ( self::$supported_types as $pod_type => $options ) {
			$main_pod = array(
				'name'    => '',
				'type'    => $pod_type,
				'storage' => '',
				'fields'  => array(),
				// Hack for 2.x
				// @todo Remove for 3.x
				'options' => array()
			);

			if ( 'pod' == $pod_type ) {
				$main_pod[ 'options' ][ 'pod_index' ] = 'name';
			}

			$objects = array();

			if ( ! isset( $options[ 'object' ] ) ) {
				$objects[ ] = $pod_type;
			} else {
				$objects = (array) $options[ 'object' ];
			}

			foreach ( $objects as $object ) {
				$object_pod = $main_pod;

				$pod_object = $object;

				if ( '%d' == $pod_object ) {
					$pod_object = 'test_' . substr( $pod_type, 0, 4 );

					$object_pod[ 'object' ] = '';
				} else {
					$object_pod[ 'object' ] = $pod_object;
				}

				$object_pod[ 'name' ] = $pod_object;

				foreach ( $options[ 'storage' ] as $storage_type ) {
					$pod = $object_pod;

					if ( empty( $pod[ 'object' ] ) ) {
						$pod[ 'name' ] = $pod_object . '_' . substr( $storage_type, 0, 3 ) . '_' . $test_pod;
					}

					$pod[ 'storage' ] = $storage_type;

					if ( 'none' != $storage_type ) {
						$pod[ 'fields' ] = self::$supported_fields;

						if ( isset( $options[ 'fields' ] ) ) {
							foreach ( $options[ 'fields' ] as $field ) {
								if ( isset( $field[ 'id' ] ) ) {
									unset( $field[ 'id' ] );
								}

								// Hack for 2.x
								// @todo Remove for 3.x
								$field[ 'options' ] = $field;

								$pod[ 'fields' ][ ] = $field;
							}
						}
					}

					if ( ! isset( self::$builds[ $pod_type ] ) ) {
						self::$builds[ $pod_type ] = array();
					}

					if ( ! isset( self::$builds[ $pod_type ][ $object ] ) ) {
						self::$builds[ $pod_type ][ $object ] = array();
					}

					if ( isset( self::$builds[ $pod_type ][ $object ][ $storage_type ] ) ) {
						continue;
					}

					self::$builds[ $pod_type ][ $object ][ $storage_type ]             = $pod;
					self::$builds[ $pod_type ][ $object ][ $storage_type ][ 'fields' ] = array();

					foreach ( $pod[ 'fields' ] as $field ) {
						self::$builds[ $pod_type ][ $object ][ $storage_type ][ 'fields' ][ $field[ 'name' ] ] = $field;
					}

					$id = $api->save_pod( $pod );

					//$this->assertGreaterThan( 0, $id, 'Pod not added' );

					self::$builds[ $pod_type ][ $object ][ $storage_type ][ 'id' ] = $id;

					$test_pod++;

					// @todo Figure out how to split test meta/table for existing objects
					// If object set, we can't create multiple Pods to test, use first storage provided
					if ( ! empty( $pod[ 'object' ] ) ) {
						break;
					}
				}
			}
		}

		global $pods_init;

        $pods_init->setup_content_types( true );

	}
	
	/**
	 * Add items to the pods
	 */
	public static function _initialize_data() {

		// Insert initial data
		$related_items = self::$related_items;

		$related_author = 0;

		foreach ( $related_items as $item => $item_data ) {
			if ( '%s' != $item ) {
				foreach ( $item_data[ 'data' ] as $k => $v ) {
					$item_data[ 'data' ][ $k ] = sprintf( $v, wp_generate_password( 4, false ) );
				}

				$p = pods( $item_data[ 'pod' ] );

				$item_data[ 'field_id' ] = $p->pod_data[ 'field_id' ];
				$item_data[ 'field_index' ] = $p->pod_data[ 'field_index' ];

				$id = $p->add( $item_data[ 'data' ] );

				if ( ! empty( $item_data[ 'limit' ] ) ) {
					$ids = array();

					$ids[] = $id;

					for ( $x = 1; $x < $item_data[ 'limit' ]; $x++ ) {
						$sub_item_data = $item_data[ 'data' ];
						$sub_item_data[ $item_data[ 'field_index' ] ] .= ' (' . $x . ')';

						$id = $p->add( $sub_item_data );

						$ids[] = $id;
					}

					$id = $ids;
				}
				elseif ( 'test_rel_user' == $item ) {
					$related_author = $id;
				}

				$item_data[ 'id' ] = $id;

				self::$related_items[ $item ] = $related_items[ $item ] = $item_data;
			}
			else {
				foreach ( self::$builds as $pod_type => $objects ) {
					foreach ( $objects as $object => $storage_types ) {
						foreach ( $storage_types as $storage_type => $pod ) {
							$pod_item_data = $item_data;

							if ( ! empty( self::$supported_types[ $pod_type ][ 'data' ] ) ) {
								foreach ( self::$supported_types[ $pod_type ][ 'data' ] as $k => $v ) {
									$pod_item_data[ 'data' ][ $k ] = $v;
								}
							}

							foreach ( $pod_item_data[ 'data' ] as $k => $v ) {
								$pod_item_data[ 'data' ][ $k ] = sprintf( $v, wp_generate_password( 4, false ) );
							}

							foreach ( self::$supported_fields as $field ) {
								if ( 'pick' == $field[ 'type' ] && isset( self::$related_items[ $field[ 'name' ] ] ) ) {
									$pod_item_data[ 'data' ][ $field[ 'name' ] ] = self::$related_items[ $field[ 'name' ] ][ 'id' ];
								}
							}

							$pod_item_data[ 'pod' ] = $pod[ 'name' ];

							$p = pods( $pod_item_data[ 'pod' ] );

							$pod_item_data[ 'field_index' ] = $p->pod_data[ 'field_index' ];
						    $pod_item_data[ 'field_id' ] = $p->pod_data[ 'field_id' ];

							$index = $pod_item_data[ 'data' ][ 'index' ];

							unset( $pod_item_data[ 'data' ][ 'index' ] );

							if ( empty( $pod_item_data[ 'data' ][ $pod_item_data[ 'field_index' ] ] ) ) {
								$pod_item_data[ 'data' ][ $pod_item_data[ 'field_index' ] ] = $index;
							}

							if ( in_array( $pod_type, array( 'post_type', 'media' ) ) ) {
								$pod_item_data[ 'data' ][ 'post_author' ] = $related_author;
							}
							elseif ( 'user' == $pod_type ) {
								$pod_item_data[ 'data' ][ 'post_author' ] = $related_author;
							}
							elseif ( 'comment' == $pod_type ) {
								$pod_item_data[ 'data' ][ 'user_id' ] = $related_author;
							}
							elseif ( 'pod' == $pod_type ) {
								$pod_item_data[ 'data' ][ 'author' ] = $related_author;
							}

							$id = $p->add( $pod_item_data[ 'data' ] );

							self::$related_items[ $pod_item_data[ 'pod' ] ] = $pod_item_data;
							self::$related_items[ $pod_item_data[ 'pod' ] ][ 'id' ] = $id;
							self::$related_items[ $pod_item_data[ 'pod' ] ][ 'is_build' ] = true;
						}
					}
				}
			}
		}

	}
}

Pods_UnitTestCase::_initialize_config();
Pods_UnitTestCase::_initialize_data();
