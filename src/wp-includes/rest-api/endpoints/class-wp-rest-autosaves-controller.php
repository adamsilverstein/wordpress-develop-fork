<?php
/**
 * REST API: WP_REST_Autosaves_Controller class.
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.0.0
 */

/**
 * Core class used to access autosaves via the REST API.
 *
 * @since 5.0.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Autosaves_Controller extends WP_REST_Revisions_Controller {

	/**
	 * Parent post type.
	 *
	 * @since 5.0.0
	 * @var string
	 */
	private $parent_post_type;

	/**
	 * Parent controller.
	 *
	 * @since 5.0.0
	 * @var WP_REST_Controller
	 */
	private $parent_controller;

	/**
	 * Parent controller.
	 *
	 * @since 5.0.0
	 * @var WP_REST_Controller
	 */
	private $revision_controller;

	/**
	 * The base of the parent controller's route.
	 *
	 * @since 5.0.0
	 * @var string
	 */
	private $parent_base;

	/**
	 * Constructor.
	 *
	 * @since 5.0.0
	 *
	 * @param string $parent_post_type Post type of the parent.
	 */
	public function __construct( $parent_post_type ) {
		$this->parent_post_type    = $parent_post_type;
		$this->parent_controller   = new WP_REST_Posts_Controller( $parent_post_type );
		$this->revision_controller = new WP_REST_Revisions_Controller( $parent_post_type );
		$this->rest_namespace      = 'wp/v2';
		$this->rest_base           = 'autosaves';
		$post_type_object          = get_post_type_object( $parent_post_type );
		$this->parent_base         = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type_object->name;
	}

	/**o
	 * Registers routes for autosaves.
	 *
	 * @since 5.0.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->rest_namespace, '/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base, array(
				'args'   => array(
					'parent' => array(
						'description' => __( 'The ID for the parent of the object.', 'gutenberg' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this->revision_controller, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this->parent_controller, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->rest_namespace, '/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)', array(
				'args'   => array(
					'parent' => array(
						'description' => __( 'The ID for the parent of the object.', 'gutenberg' ),
						'type'        => 'integer',
					),
					'id'     => array(
						'description' => __( 'Unique identifier for the object.', 'gutenberg' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this->revision_controller, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this->revision_controller, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( 'Required to be true, as autosaves do not support trashing.', 'gutenberg' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this->parent_controller, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	/**
	 * Get the parent post, if the ID is valid.
	 *
	 * @since 4.7.2
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
	 */
	protected function get_parent( $parent ) {
		return $this->revision_controller->get_parent( $parent );
	}
	/**
	 * Creates a single autosave.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {

		// Map new fields onto the existing post data.
		$parent            = $this->get_parent( $request->get_param( 'parent' ) );
		$prepared_post     = $this->parent_controller->prepare_item_for_database( $request );
		$prepared_post->ID = $parent->ID;

		// If the parent post a draft, autosaving updates it and does not create a revision.
		if ( 'draft' === $parent->post_status ) {

			define( 'DOING_AUTOSAVE', true );
			$autosave_id = wp_update_post( (array) $prepared_post, true );

			if ( ! is_wp_error( $autosave_id ) ) {
				$post = get_post( $autosave_id );
			}
		} else {

			// Non-draft posts - update the post, creating an autosave.
			$autosave_id         = $this->create_post_autosave( (array) $prepared_post );
			$post                = get_post( $autosave_id );
		}

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->rest_namespace, $this->rest_base, $autosave_id ) ) );

		return $response;
	}

	/**
	 * Update an autosave, if the ID is valid.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Post|WP_Error Revision post object if ID is valid, WP_Error otherwise.
	 */
	public function update_item( $request ) {
		$parent = $request->get_param( 'parent' );
		$id     = $request->get_param( 'id' );
		$error  = new WP_Error( 'rest_post_invalid_id', __( 'Invalid autosave ID.', 'gutenberg' ), array( 'status' => 404 ) );
		if ( (int) $parent <= 0 ) {
			return $error;
		}

		$prepared_post     = $this->parent_controller->prepare_item_for_database( $request );
		$prepared_post->ID = $id;
		$post_id           = wp_update_post( (array) $prepared_post );
		$post              = get_post( $post_id );
		$fields_update     = $this->update_additional_fields_for_object( $post, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $post, $request );

		return rest_ensure_response( $response );
	}
	/**
	 * Get the autosave, if the ID is valid.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Post|WP_Error Revision post object if ID is valid, WP_Error otherwise.
	 */
	public function get_item( $request ) {
		$parent = $request->get_param( 'parent' );
		$error  = new WP_Error( 'rest_post_invalid_id', __( 'Invalid autosave ID.', 'gutenberg' ), array( 'status' => 404 ) );
		if ( (int) $parent <= 0 ) {
			return $error;
		}
		$autosave = wp_get_post_autosave( (int) $parent );

		if ( empty( $autosave ) || empty( $autosave->ID ) || 'revision' !== $autosave->post_type ) {
			return $error;
		}
		$autosave->post_parent = $parent;
		$response = $this->prepare_item_for_response( $autosave, $request );

		return $response;
	}

	/**
	 * Gets a collection of autosaves using wp_get_post_autosave.
	 *
	 * Contains the user's autosave, for empty if it doesn't exist.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$parent = $this->get_parent( $request->get_param( 'parent' ) );
		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		$autosave = wp_get_post_autosave( $request->get_param( 'parent' ) );

		if ( ! $autosave ) {
			return array();
		}

		$response   = array();
		$data       = $this->prepare_item_for_response( $autosave, $request );
		$response[] = $this->prepare_response_for_collection( $data );

		return rest_ensure_response( $response );
	}


	/**
	 * Retrieves the autosave's schema, conforming to JSON Schema.
	 *
	 * @since 5.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		return $this->revision_controller->get_item_schema();
	}

	/**
	 * Creates autosave data for the specified post from $_POST data.
	 *
	 * From wp-admin/post.php.
	 *
	 * @since 2.6.0
	 *
	 * @param mixed $post_data Associative array containing the post data or int post ID.
	 * @return mixed The autosave revision ID. WP_Error or 0 on error.
	 */
	public function create_post_autosave( $post_data ) {

		$post_id     = (int) $post_data['ID'];
		$post_author = get_current_user_id();

		// Store one autosave per author. If there is already an autosave, overwrite it.
		$old_autosave = wp_get_post_autosave( $post_id, $post_author );
		if ( $old_autosave ) {
			$new_autosave                = _wp_post_revision_data( $post_data, true );
			$new_autosave['ID']          = $old_autosave->ID;
			$new_autosave['post_author'] = $post_author;

			// If the new autosave has the same content as the post, delete the autosave.
			$post                  = get_post( $post_id );
			$autosave_is_different = false;
			foreach ( array_intersect( array_keys( $new_autosave ), array_keys( _wp_post_revision_fields( $post ) ) ) as $field ) {
				if ( normalize_whitespace( $new_autosave[ $field ] ) != normalize_whitespace( $post->$field ) ) {
					$autosave_is_different = true;
					break;
				}
			}

			if ( ! $autosave_is_different ) {
				wp_delete_post_revision( $old_autosave->ID );
				return 0;
			}

			/**
			 * This filter is documented in wp-admin/post.php.
			 */
			do_action( 'wp_creating_autosave', $new_autosave );

			// If the autosave content is significantly different, create a revision.
			$autosave_sizediff        = strlen( $new_autosave['content'] ) - strlen( $old_autosave['content'] );
			$autosave_create_revision = $autosave_sizediff > 250;

			if ( apply_filters( 'wp_create_revision_for_autosave', $autosave_create_revision, $post_data, $autosave_sizediff
			) ) {
				_wp_put_post_revision( $new_autosave );
			}

			return wp_update_post( $new_autosave );
		}

		// _wp_put_post_revision() expects unescaped.
		$post_data = wp_unslash( $post_data );

		// Otherwise create the new autosave as a special post revision.
		return _wp_put_post_revision( $post_data, true );
	}

	/**
	 * Prepares the revision for the REST response.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_Post         $post    Post revision object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $post, $request ) {
		$data = array();
		$response = $this->revision_controller->prepare_item_for_response( $post, $request );

		/**
		 * Filters a revision returned from the API.
		 *
		 * Allows modification of the revision right before it is returned.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Post          $post     The original revision object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_autosave', $response, $post, $request );

	}

}
