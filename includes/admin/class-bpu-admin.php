<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main BPU_Admin class
 *
 * @since 1.0.0
 */
class BPU_Admin {
	
	public function __construct() {
		$this->hooks();
		$this->review_notice_callout();
	}

	/**
	 * Run action and filter hooks
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function hooks() {

		add_action( 'admin_menu', array( $this, 'create_setting_menu' ) );
		add_action( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'add_deactive_modal' ) );
		add_action( 'wp_ajax_bpu_deactivation', array( $this, 'deactivation_popup' ) );
		add_action( 'plugin_action_links', array( $this, 'action_links' ), 10, 2 );
		add_action( 'in_plugin_update_message-' . plugin_basename( BPU_FILE ), array( $this, 'in_plugin_update_message' ), 10, 2 );

		add_action( 'bpu_after_settings_title', array( $this, 'subscription_callout' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		add_action( 'admin_footer', array( $this, 'subscription_popup' ) );
		add_action( 'wp_ajax_bpu_handle_subscription_request', array( $this, 'process_subscription' ) );
		add_action( 'wp_ajax_bpu_subscription_popup_shown', array( $this, 'subscription_shown' ) );
		add_action('wp_ajax_bpu_get_products', array($this,'bpu_get_products_callback'));
		add_action('wp_ajax_bpu_change_price_product_ids', array($this,'bpu_change_price_product_ids_callback'));
		add_action('wp_ajax_bpu_change_price_percentge', array($this,'bpu_change_price_percentge_callback'));
	}

	/**
	 * Admin Enqueue style and scripts
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function enqueue_admin_scripts() {
		if ( BPU_LOAD_NON_MIN_SCRIPTS ) {
			$suffix = '';
		} else {
			$suffix = '.min';
		}

		if ( bpu_is_admin_page( 'admin.php' ) || bpu_is_admin_page( 'plugins.php' ) ) {
			wp_enqueue_style( 'bpu_admin_style', BPU_URL . 'assets/admin/css/admin' . $suffix . '.css', array(), BPU_VER );
			wp_enqueue_script( 'bpu_admin_script', BPU_URL . 'assets/admin/js/admin' . $suffix . '.js', array(), BPU_VER, true );
		}


		$main_page = bpu_get_admin_page_by_name();

		if ( bpu_is_admin_page( 'admin.php', $main_page['slug'] ) ) {

			wp_enqueue_media(); // load media scripts

			wp_enqueue_style(
				'select2',
				BPU_URL . 'assets/vendor/select2/css/select2.min.css',
				array(),
				BPU_VER
			);

			wp_enqueue_style(
				'jquery-ui-style',
				BPU_URL . 'assets/vendor/jquery-ui/jquery-ui.min.css',
				array(),
				BPU_VER
			);

			wp_enqueue_style(
				'bootstrap',
				BPU_URL . 'assets/vendor/bootstrap/bootstrap-3.3.2.min.css',
				array(),
				BPU_VER
			);

			wp_enqueue_style(
				'multiselect',
				BPU_URL . 'assets/vendor/multiselect/bootstrap-multiselect.css',
				array(),
				BPU_VER
			);

			wp_enqueue_style(
				'bpu_settings_style',
				BPU_URL . 'assets/admin/css/settings' . $suffix . '.css',
				array(),
				BPU_VER
			);

			wp_enqueue_script(
				'select2',
				BPU_URL . 'assets/vendor/select2/js/select2.min.js',
				array( 'jquery' ),
				BPU_VER,
				true
			);

			wp_enqueue_script(
				'bootstrap',
				BPU_URL . 'assets/vendor/bootstrap/bootstrap-3.3.2.min.js',
				array( 'jquery' ),
				BPU_VER,
				true
			);

			wp_enqueue_script(
				'multiselect',
				BPU_URL . 'assets/vendor/multiselect/bootstrap-multiselect.js',
				array( 'jquery' ),
				BPU_VER,
				true
			);
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script(
				'bpu_settings_script',
				BPU_URL . 'assets/admin/js/settings' . $suffix . '.js',
				array( 'jquery', 'jquery-ui-datepicker' ),
				BPU_VER,
				true
			);
		}

		if ( bpu_is_admin_page( 'plugins.php' ) ) {
			wp_enqueue_style(
				'bpu_deactivation_style',
				BPU_URL . 'assets/admin/css/deactivation' . $suffix . '.css',
				array(),
				BPU_VER
			);

			wp_enqueue_script(
				'bpu_deactivation_script',
				BPU_URL . 'assets/admin/js/deactivation' . $suffix . '.js',
				array( 'jquery' ),
				BPU_VER,
				true
			);
		}

		if ( 'y' !== get_option( 'bpu_subscription_shown' ) ) {
			wp_enqueue_style(
				'bpu_subscription_style',
				BPU_URL . 'assets/admin/css/subscription' . $suffix . '.css',
				array(),
				BPU_VER
			);

			wp_enqueue_script(
				'bpu_subscription_script',
				BPU_URL . 'assets/admin/js/subscription' . $suffix . '.js',
				array( 'jquery' ),
				BPU_VER,
				true
			);
		}
	}

	/**
	 * Get Products
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	function bpu_get_products_callback()
	{
		$return = array();
		$search_results = new WP_Query(array('post_type' => 'product', 's'=> sanitize_text_field( $_REQUEST['s'] ),'paged'=> sanitize_text_field( $_REQUEST['page'] ),'posts_per_page' => 50));
		if( $search_results->have_posts() ) :
			while( $search_results->have_posts() ) : $search_results->the_post();	
				$return[] = array('id'=>get_the_ID(), 'text'=>get_the_title());
			endwhile;
		endif;
		echo json_encode(array('results'=>$return,'count_filtered'=>$search_results->found_posts,'page'=> sanitize_text_field( $_REQUEST['page'] ),'pagination' => array( "more"=> true )));
		exit();
	}

	/**
	 * Get Change Price Products ids by Categories
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	function bpu_change_price_product_ids_callback()
	{
		if(isset($_POST["cat_ids"]) && $_POST["cat_ids"]!='' && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'bpu_product_ids_nonce'))
		{
			$posts_array = get_posts(array('fields'=> 'ids','numberposts' => -1,'post_type' => 'product','status'=>'publish','order' => 'ASC','tax_query' => array(array('taxonomy' => 'product_cat','field' => 'term_id','terms' => array_map( 'sanitize_text_field', $_POST["cat_ids"] ) ) ) ) );
			echo json_encode($posts_array);
		}
		exit();
	}

	/**
	 * Change Price Products
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	function bpu_change_price_percentge_callback() 
	{	
		if(isset($_POST["product_id"]) && !empty($_POST["product_id"]) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'bpu_product_update_nonce'))
		{
			$product_count = sanitize_text_field( $_POST['bpu_req_count'] );
			$product_count = $product_count+1;
			$product_count = 5 * $product_count;
			$temp_i=4;
			$product_ids = array_map( 'sanitize_text_field', $_POST["product_id"]);
			
			foreach ($product_ids as $key => $product_id) 
			{				
				$res=array(); 
				$opration_type= sanitize_text_field(trim($_POST["opration_type"]));
				$price_type_by_change = sanitize_text_field(trim($_POST["price_type_by_change"]));
			    $percentage  = trim($_POST["percentage"]);
				$price_rounds_point = sanitize_text_field(trim($_POST["price_rounds_point"]));
				$bpu_dry_run = sanitize_text_field(trim($_POST["bpu_dry_run"]));
				$bpu_sale_price_dates_from = '';
				$bpu_sale_price_dates_to = '';

				if ( !empty( $_POST["bpu_sale_price_dates_from"] ) && !empty( $_POST["bpu_sale_price_dates_to"] ) ) {
					
					$bpu_sale_price_dates_from = strtotime( sanitize_text_field(trim($_POST["bpu_sale_price_dates_from"])) );
					$bpu_sale_price_dates_to = strtotime( sanitize_text_field(trim($_POST["bpu_sale_price_dates_to"])) );
				}
				$product = wc_get_product(intval(trim($product_id)));
				$product_id = $product->get_id();			
				$currency = get_woocommerce_currency_symbol();
		        $thumbnail = wp_get_attachment_image($product->get_image_id(), array(50,50));
		        $html = '<td>'.(($thumbnail) ? $thumbnail : wc_placeholder_img(array(50,50))).'</td>';
		        $html .= '<td>'.$product_id.'</td>';
		        $html .= '<td>'.$product->get_name().'</td>';
		        $html .= '<td>'.$product->get_type().'</td>';
		        $html .= '<td><table><tbody>';
				
				if(!$product->is_type('variable')) 
				{
					$res['is_type'] = 'simple';
					$product_prc = get_post_meta( $product->get_id(), '_price', true);
					$sale_price = get_post_meta( $product->get_id(), '_sale_price', true);
					$regular_price = get_post_meta( $product->get_id(), '_regular_price', true);		    			
					$res['old_price'] = $product_prc;
					
					if (!empty($sale_price))
					{
						
						if($price_type_by_change=='by_percent'){
							$sale_price_update = (float) $sale_price  * ( $percentage / 100 );
							$regular_price_update = (float) $regular_price  * ( $percentage / 100 );
						} elseif ( $price_type_by_change=='by_fixed' ) {
							$sale_price_update = $percentage;
							$regular_price_update = $percentage;
						}
						
						if($opration_type=="increase-percentge")
						{
							$sale_product_prc=$sale_price+$sale_price_update;
							$regular_product_prc=$regular_price+$regular_price_update;				
						} elseif ($opration_type=="discount-percentge") {
							$sale_product_prc=$sale_price-$sale_price_update;
							$regular_product_prc=$regular_price-$regular_price_update;	
						}
						
						if($price_rounds_point == 'true'){
							$sale_product_prc = round($sale_product_prc);
							$regular_product_prc = round($regular_product_prc);
						}
						$sale_product_prc = round($sale_product_prc, 2);
						$regular_product_prc = round($regular_product_prc, 2);
						
						if($bpu_dry_run == 'false'){
							update_post_meta( $product->get_id(), '_sale_price', $sale_product_prc);
							update_post_meta( $product->get_id(), '_regular_price', $regular_product_prc);
							update_post_meta( $product->get_id(), '_price', $sale_product_prc );
							
							if ( !empty( $bpu_sale_price_dates_from ) && !empty( $bpu_sale_price_dates_to ) ) {
								update_post_meta( $product->get_id(), '_sale_price_dates_from', $bpu_sale_price_dates_from );
								update_post_meta( $product->get_id(), '_sale_price_dates_to', $bpu_sale_price_dates_to );
							}
						}
						$res['new_price'] = $sale_product_prc;
					}
					else
					{
						if($price_type_by_change=='by_percent'){
							$regular_price_update = (float) $regular_price  * ( $percentage / 100 );
						}elseif ( $price_type_by_change=='by_fixed' ){
							$regular_price_update = (float) $percentage;
						}					
						
						if($opration_type=="increase-percentge")
						{
							$regular_product_prc= (float) $regular_price+$regular_price_update;				
						}
						
						if($opration_type=="discount-percentge")
						{
							$regular_product_prc= (float) $regular_price-$regular_price_update;	
						}					
						
						if($price_rounds_point == 'true'){
							$regular_product_prc = round($regular_product_prc);
						}
						$regular_product_prc = round($regular_product_prc, 2);					
						
						if($bpu_dry_run == 'false'){
							update_post_meta( $product->get_id(), '_regular_price', $regular_product_prc);
							update_post_meta( $product->get_id(), '_price', $regular_product_prc );

							if ( !empty( $bpu_sale_price_dates_from ) && !empty( $bpu_sale_price_dates_to ) ) {
								update_post_meta( $product->get_id(), '_sale_price_dates_from', $bpu_sale_price_dates_from );
								update_post_meta( $product->get_id(), '_sale_price_dates_to', $bpu_sale_price_dates_to );
							}
						}
						$res['new_price']= $regular_product_prc;
					} 
					$html .= '<tr><td><strong>'.esc_html__( 'Old Price:', 'bulk-price-update' ).'</strong></td><td><code>'.$currency.' '.$res['old_price'].'</code></td></tr><tr><td><strong>'.esc_html__( 'New Price:', 'bulk-price-update' ).'</strong></td><td><code>'.$currency.' '.$res['new_price'].'</code></td></tr>';
			 	} else {	
					$res['is_type']= 'variable';
					$var_new_price=array();
					$variation_count=0;
			 		
			 		foreach ( $product->get_children() as $child_id ) 
			 		{	 			
						$variation_res=array(); 
			 			$variation_count++;
			    		$product_prc = get_post_meta( $child_id, '_price', true);
						$variation_res['old_price']= $product_prc;
			    		$sale_price = get_post_meta( $child_id, '_sale_price', true);	
						$regular_price = get_post_meta( $child_id, '_regular_price', true);
			    		
			    		if (!empty($sale_price))
			    		{
							
							if($price_type_by_change=='by_percent'){
				    			$sale_price_update = (float) $sale_price  * ( $percentage / 100 );
								$regular_price_update = (float) $regular_price  * ( $percentage / 100 );
							}
							elseif ($price_type_by_change=='by_fixed')
							{
			    				$sale_price_update = (float) $percentage;
								$regular_price_update = (float) $percentage;
							}

							if($opration_type=="increase-percentge")
							{
								$sale_product_prc= (float) $sale_price+$sale_price_update;
								$regular_product_prc= (float) $regular_price+$regular_price_update;				
							}

							if($opration_type=="discount-percentge")
							{
								$sale_product_prc= (float) $sale_price-$sale_price_update;
								$regular_product_prc= (float) $regular_price-$regular_price_update;	
							}

							if($price_rounds_point == 'true'){
								$sale_product_prc = round($sale_product_prc);
								$regular_product_prc = round($regular_product_prc);
							}							
							$sale_product_prc = round($sale_product_prc, 2);
							$regular_product_prc = round($regular_product_prc, 2);

							if($bpu_dry_run == 'false'){
								update_post_meta( $child_id, '_sale_price', $sale_product_prc);
								update_post_meta( $child_id, '_regular_price', $regular_product_prc);
								update_post_meta( $child_id, '_price', $sale_product_prc );
								
								if ( !empty( $bpu_sale_price_dates_from ) && !empty( $bpu_sale_price_dates_to ) ) {
									update_post_meta( $child_id, '_sale_price_dates_from', $bpu_sale_price_dates_from );
									update_post_meta( $child_id, '_sale_price_dates_to', $bpu_sale_price_dates_to );
								}
								$var_new_price[]=$sale_product_prc;
							}
							$variation_res['new_price']= $sale_product_prc;
			    		}
			    		else
			    		{

							if($price_type_by_change=='by_percent'){
			    				$regular_price_update = (float) $regular_price  * ( $percentage / 100 );
							}elseif($price_type_by_change=='by_fixed'){
								$regular_price_update = (float) $percentage;
							}
							if($opration_type=="increase-percentge"){
								$regular_product_prc= (float) $regular_price+$regular_price_update;				
							}elseif($opration_type=="discount-percentge"){
								$regular_product_prc= (float) $regular_price-$regular_price_update;	
							}						
							if($price_rounds_point == 'true'){
								$regular_product_prc = round($regular_product_prc);
							}
							$regular_product_prc = round($regular_product_prc, 2);

							if($bpu_dry_run == 'false'){
								update_post_meta( $child_id, '_regular_price', $regular_product_prc);
								update_post_meta( $child_id, '_price', $regular_product_prc );
								
								if ( !empty( $bpu_sale_price_dates_from ) && !empty( $bpu_sale_price_dates_to ) ) {
									update_post_meta( $child_id, '_sale_price_dates_from', $bpu_sale_price_dates_from );
									update_post_meta( $child_id, '_sale_price_dates_to', $bpu_sale_price_dates_to );
								}
								$var_new_price[]=$regular_product_prc;
							}
							$variation_res['new_price']= $regular_product_prc;
			    		}
			    		$res['variation_'.$variation_count] = $variation_res;
					}

					foreach ($res as $key => $value) 
		        	{

		        		if($key !='is_type')
		        		{
		        			$html .= '<tr><td><strong>'.esc_html__( 'Old Price:', 'bulk-price-update' ).'</strong></td><td><code>'.$currency.' '.$value['old_price'].'</code></td></tr><tr><td><strong>'.esc_html__( 'New Price:', 'bulk-price-update' ).'</strong></td><td><code>'.$currency.' '.$value['new_price'].'</code></td></tr>';
		        		}
		        	}

		        	if($bpu_dry_run == 'false'){
		        		update_post_meta( $product->get_id(), '_price', min($var_new_price));
		        	}
				}
		        $html .= '</tbody></table></td>';

		        if($bpu_dry_run == 'false'){
					$product->save();
		        }

				$product_count_1 = $product_count - $temp_i;
				echo '<tr><td>'.$product_count_1.'</td>'.$html.'</tr>';
				$temp_i--;
			}	        
		}
	    exit();
	}

	/**
	 * Add support link
	 *
	 * @since 1.0.0
	 * @param array  $plugin_meta
	 * @param string $plugin_file
	 */
	public function add_plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( plugin_basename( BPU_FILE ) === $plugin_file ) {
			
			array_push( $plugin_meta, '<a href="' . BPU_DOCUMENTATION_URL . '" target="_blank">' . __( 'Documentation', 'bulk-price-update' ) . '</a>' );

			array_push( $plugin_meta, '<a href="' . BPU_OPEN_TICKET_URL . '" target="_blank">' . __( 'Open Support Ticket', 'bulk-price-update' ) . '</a>' );

			array_push( $plugin_meta, '<a href="' . BPU_REVIEW_URL . '" target="_blank">' . __( 'Post Review', 'bulk-price-update' ) . '</a>' );
		}

		return $plugin_meta;
	}

	/**
	 * Show Subscription Modal, if not shown already
	 *
	 * @since 1.0.2
	 * @access public
	 * @return void
	 */
	public function subscription_callout() {
		require BPU_DIR . 'includes/admin/templates/subscription.php';
	}

	/**
	 * Show Subscription Modal, if not shown already
	 *
	 * @since 1.0.2
	 * @access public
	 * @return void
	 */
	public function subscription_popup() {

		// To enable subscription popup, remove following return
		return false;

		if ( 'y' !== get_option( 'bpu_subscription_shown' ) ) {
			require BPU_DIR . 'includes/admin/templates/subscription-popup.php';
		}
	}

	/**
	 * Processes the subscription request
	 *
	 * @since 1.0.2
	 * @access public
	 * @return void
	 */
	public function process_subscription() {

		check_ajax_referer( 'bpu_subscribe', 'security' );

		if ( isset( $_POST['email'] ) && filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
			$email = sanitize_email( $_POST['email'] );
		} else {
			$email = get_option( 'admin_email' );
		}

		wp_remote_post(
			BPU_SUBSCRIBE_URL,
			array(
				'body' => array(
					'email'       => $email,
					'plugin_name' => BPU_NAME,
				),
			)
		);

		if ( ! isset( $_POST['from_callout'] ) ) {
			update_option( 'bpu_subscription_shown', 'y', false );
		}

		wp_send_json(
			array(
				'processed' => 1,
			)
		);
	}

	/**
	 * Update the flag for susbscription popup
	 *
	 * @since 1.0.2
	 * @access public
	 * @return void
	 */
	public function subscription_shown() {

		update_option( 'bpu_subscription_shown', 'y', false );

		wp_send_json(
			array(
				'processed' => 1,
			)
		);
	}

	/**
	 * Add page to admin menu
	 *
	 * @since 1.0.2
	 * @access public
	 * @return void
	 */
	public function create_setting_menu() {

		$pages = bpu_get_admin_pages();

		$menu = add_menu_page(
			$pages['main']['title'],
			$pages['main']['title'],
			'manage_options',
			$pages['main']['slug'],
			array( $this, 'load_main_page' ),
			BPU_URL . 'assets/admin/images/' . $pages['main']['icon']
		);

		$menu = add_submenu_page(
			$pages['main']['slug'],
			$pages['main']['title'],
			$pages['main']['sub_title'],
			'manage_options',
			$pages['main']['slug'],
			array( $this, 'load_main_page' )
		);

		// Can add more Submenu Pages here
	}

	public function load_main_page() {
		require_once BPU_DIR . 'includes/admin/settings/promos.php';
		require_once BPU_DIR . 'includes/admin/settings/settings.php';
	}

	public function load_general_page() {
		require_once BPU_DIR . 'includes/admin/templates/page.php';
	}

	private function review_notice_callout() {
		// AJAX action hook to disable the 'review request' notice.
		add_action( 'wp_ajax_bpu_review_notice', array( $this, 'dismiss_review_notice' ) );

		if ( ! get_option( 'bpu_review_time' ) ) {
			$review_time = time() + 7 * DAY_IN_SECONDS;
			add_option( 'bpu_review_time', $review_time, '', false );
		}

		if (
			is_admin() &&
			get_option( 'bpu_review_time' ) &&
			get_option( 'bpu_review_time' ) < time() &&
			! get_option( 'bpu_dismiss_review_notice' )
		) {
			add_action( 'admin_notices', array( $this, 'notice_review' ) );
			add_action( 'admin_footer', array( $this, 'notice_review_script' ), 5 );
		}
	}

	/**
	 * Disables the notice about leaving a review.
	 */
	public function dismiss_review_notice() {
		update_option( 'bpu_dismiss_review_notice', true, false );
		wp_die();
	}

	/**
	 * Ask the user to leave a review for the plugin.
	 */
	public function notice_review() {
		global $current_user;

		wp_get_current_user();

		// Set the user name
		$user_name = '';
		
		// Check if the display name is not empty
		if ( ! empty( $current_user->display_name ) ) {
			// Set the user name
			$user_name = ' ' . $current_user->display_name;
		}

		echo "<div id='bulk-price-update-review' class='notice notice-info is-dismissible'><p>" .

		sprintf( __( 'Hi %1$s, Thank you for using <b>%2$s</b>. Please don\'t forget to rate our plugin. We sincerely appreciate your feedback.', 'bulk-price-update' ), esc_attr( $user_name ), esc_attr( BPU_NAME ) )
		.
		'<br><a target="_blank" href="' . BPU_REVIEW_URL . '" class="button-secondary">' . esc_html__( 'Post Review', 'bulk-price-update' ) . '</a>' .
		'</p></div>';
	}

	/**
	 * Loads the inline script to dismiss the review notice.
	 */
	public function notice_review_script() {

		wp_enqueue_script( 'bpu_admin_review_notice', BPU_URL . 'assets/admin/js/review.min.js', array( 'jquery' ), BPU_VER, true );
	}

	/**
	 * Add deactivate modal layout.
	 */
	public function add_deactive_modal() {
		global $pagenow;

		if ( 'plugins.php' !== $pagenow ) {
			return;
		}

		include BPU_DIR . 'includes/admin/templates/deactivation.php';
	}

	/**
	 * Called after the user has submitted his reason for deactivating the plugin.
	 *
	 * @since  1.0.0
	 */
	public function deactivation_popup() {

		wp_verify_nonce( $_REQUEST['bpu_deactivation_nonce'], 'bpu_deactivation_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		if ( empty( $_POST['reason'] ) ) {
			wp_die();
		}

		$reason_id = intval( sanitize_text_field( wp_unslash( $_POST['reason'] ) ) );

		$reason_info = ! empty( $_POST['reason_detail'] ) ? sanitize_text_field( wp_unslash( $_POST['reason_detail'] ) ) : '';

		if ( 1 === $reason_id ) {
			$reason_text = __( 'I only needed the plugin for a short period', 'bulk-price-update' );
		} elseif ( 2 === $reason_id ) {
			$reason_text = __( 'I found a better plugin', 'bulk-price-update' );
		} elseif ( 3 === $reason_id ) {
			$reason_text = __( 'The plugin broke my site', 'bulk-price-update' );
		} elseif ( 4 === $reason_id ) {
			$reason_text = __( 'The plugin suddenly stopped working', 'bulk-price-update' );
		} elseif ( 5 === $reason_id ) {
			$reason_text = __( 'I no longer need the plugin', 'bulk-price-update' );
		} elseif ( 6 === $reason_id ) {
			$reason_text = __( 'It\'s a temporary deactivation. I\'m just debugging an issue.', 'bulk-price-update' );
		} elseif ( 7 === $reason_id ) {
			$reason_text = __( 'Other', 'bulk-price-update' );
		}

		$current_user = wp_get_current_user();

		$to      = 'info@pluginsandsnippets.com';
		$subject = 'Plugin Uninstallation';

		$body  = '<p>Plugin Name: ' . BPU_NAME . '</p>';
		$body .= '<p>Plugin Version: ' . BPU_VER . '</p>';
		$body .= '<p>Reason: ' . $reason_text . '</p>';
		$body .= '<p>Reason Info: ' . $reason_info . '</p>';
		$body .= '<p>Admin Name: ' . $current_user->display_name . '</p>';
		$body .= '<p>Admin Email: ' . get_option( 'admin_email' ) . '</p>';
		$body .= '<p>Website: ' . get_site_url() . '</p>';
		$body .= '<p>Website Language: ' . get_bloginfo( 'language' ) . '</p>';
		$body .= '<p>WordPress Version: ' . get_bloginfo( 'version' ) . '</p>';
		$body .= '<p>PHP Version: ' . PHP_VERSION . '</p>';

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail( $to, $subject, $body, $headers );
		wp_die();
	}

	/**
	 * Add a link to the settings page to the plugins list
	 *
	 * @since  1.0.0
	 */
	public function action_links( $links, $file ) {

		static $this_plugin;

		if ( empty( $this_plugin ) ) {
			$this_plugin = 'bulk-price-update/bulk-price-update.php';
		}

		if ( $file === $this_plugin ) {
			$main_page     = bpu_get_admin_page_by_name();
			$settings_link = sprintf( esc_html__( '%1$s Settings %2$s', 'bulk-price-update' ), '<a href="' . admin_url( 'admin.php?page=' . $main_page['slug'] ) . '">', '</a>' );

			array_unshift( $links, $settings_link );

		}

		// Return the links
		return $links;
	}

	/**
	 * Shows an additional update message.
	 */
	public function in_plugin_update_message( $plugin_data, $response ) {
		// Check if the user can update plugins and if the response package is empty
		if ( current_user_can( 'update_plugins' ) && empty( $response->package ) ) {
			
		}
	}
}
