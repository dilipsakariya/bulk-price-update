<?php
/**
 * Settings Page
 *
 * @package     Bulk_Price_Update\Settings
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

/*if ( isset( $_POST['bpu_save_settings'] ) ) {
	update_option( 'bpu_settings', $_POST['bpu'], false );
	$message = __( 'Settings have been successfully updated.', 'bulk-price-update' );
	bpu_show_message( $message );
}*/

$settings = bpu_get_settings();
$categories = get_terms('product_cat', array('post_type' => array('product'),'hide_empty' => true,'orderby' => 'name','order' => 'ASC'));
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Settings', 'bulk-price-update' ); ?></h1>
	<?php do_action( 'bpu_after_settings_title' ); ?>
	<h2><?php esc_html_e( 'Sub Heading Goes Here', 'bulk-price-update' ); ?></h2>
	<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'bulk-price-update' ); ?></p>
	<form method="post" action="" id="bpu_setting_form">
		<div id="ps_plugin_template_settings_tabs">
			<div id="ps_plugin_template_settings_tabs_header">
				<a href="#bpu_settings_tab_1" class="bpu-tab-active"><?php esc_html_e( 'General', 'bulk-price-update' ); ?></a>
			</div>

			<div id="bpu_settings_tab_1" class="bpu-tab-content bpu-tab-active">

				<h2 style="margin:0;"><?php esc_html_e( 'General Settings', 'bulk-price-update' ); ?></h2>
				<hr />
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label><?php esc_html_e( 'Price Change Type', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<label>
									<input type="radio" name="bpu[bpu_price_type_by_change]" <?php checked( $settings['bpu_price_type_by_change'], 'by_percent' ); ?> value="by_percent">
									<span><?php esc_html_e( 'Percentage', 'bulk-price-update' ); ?></span>
								</label>

								<label>
									<input type="radio" name="bpu[bpu_price_type_by_change]" <?php checked( $settings['bpu_price_type_by_change'], 'by_fixed' ); ?> value="by_fixed">
									<span><?php esc_html_e( 'Fixed', 'bulk-price-update' ); ?></span>
								</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="bpu_percentage"><?php esc_html_e( 'Amount:', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<input type="number" id="bpu_percentage" name="bpu[bpu_percentage]" value="<?php echo esc_attr( $settings['bpu_percentage'] ); ?>" class="regular-text" min="0" max="100" />
								<p class="description"><?php esc_html_e( '(Enter pricing percentage)', 'bulk-price-update' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php esc_html_e( 'Please select between following methods', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<label>
									<input type="radio" name="bpu[bpu_price_change_method]" <?php checked( $settings['bpu_price_change_method'], 'by_categories' ); ?> value="by_categories">
									<span><?php esc_html_e( 'Categories', 'bulk-price-update' ); ?></span>
								</label>

								<label>
									<input type="radio" name="bpu[bpu_price_change_method]" <?php checked( $settings['bpu_price_change_method'], 'by_products' ); ?> value="by_products">
									<span><?php esc_html_e( 'Specific Products', 'bulk-price-update' ); ?></span>
								</label>
							</td>
						</tr>
						<tr valign="top" id="bpu_method_by_categories" class="bpu_method_aria_tc">
							<th scope="row">
								<label for="bpu_categories"><?php esc_html_e( 'Please select categories', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<select id="bpu_categories" name="bpu[bpu_categories][]" multiple class="regular-text bpu-multi-select">

									<?php
										foreach ($categories as $key => $cat) 
										{
										    echo '<option value="'.$cat->term_id.'" '. ( isset( $settings['bpu_categories'] ) && in_array( $cat->term_id, $settings['bpu_categories'] ) ? 'selected' : '' ) .'>'.$cat->name.'</option>';
										}
									?>

								</select>
								<p class="description"></p>
							</td>
						</tr>
						<tr valign="top" id="bpu_method_by_products" class="bpu_method_aria_tc">
							<th scope="row">
								<label for="bpu_products"><?php esc_html_e( 'Please select products', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<select id="bpu_products" name="bpu[bpu_products][]" multiple class="regular-text bpu-multi-select-product">
								</select>
								<p class="description"></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="bpu_price_rounds_point"><?php esc_html_e( 'Round Up Prices.', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="bpu_price_rounds_point" name="bpu[bpu_price_rounds_point]" value="1" <?php checked( $settings['bpu_price_rounds_point'], '1' ); ?> class="regular-text" />
								<span><?php esc_html_e( '( $5.2 => $5 or $5.9 => $6 )', 'bulk-price-update' ); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php esc_html_e( 'Increase Prices', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<label>
									<input type="radio" name="bpu[bpu_price_change_type]" <?php checked( $settings['bpu_price_change_type'], 'increase-percentge' ); ?> value="increase-percentge">
									<span><?php esc_html_e( '(Regular price and sale price)', 'bulk-price-update' ); ?></span>
								</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php esc_html_e( 'Decrease Prices', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<label>
									<input type="radio" name="bpu[bpu_price_change_type]" <?php checked( $settings['bpu_price_change_type'], 'discount-percentge' ); ?> value="discount-percentge">
									<span><?php esc_html_e( '(Regular price and sale price)', 'bulk-price-update' ); ?></span>
								</label>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label><?php esc_html_e( 'Sale price dates', 'bulk-price-update' ); ?></label>
							</th>
							<td class="bpu_sale_price_dates_fields" >
								<input type="text" id="bpu_sale_price_dates_from" name="bpu[bpu_sale_price_dates_from]" value="<?php echo esc_attr( $settings['bpu_sale_price_dates_from'] ); ?>" class="regular-text" placeholder="From… YYYY-MM-DD" /><br><br><input type="text" id="bpu_sale_price_dates_to" name="bpu[bpu_sale_price_dates_to]" value="<?php echo esc_attr( $settings['bpu_sale_price_dates_to'] ); ?>" class="regular-text" placeholder="To… YYYY-MM-DD" />
								<p class="description"><?php esc_html_e( '(Select sale price dates start to end)', 'bulk-price-update' ); ?></p>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row">
								<label for="bpu_dry_run"><?php esc_html_e( 'Run as dry run?', 'bulk-price-update' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="bpu_dry_run" name="bpu[bpu_dry_run]" value="1" <?php checked( $settings['bpu_dry_run'], '1' ); ?> />
								<p class="description"><?php esc_html_e( 'If checked, no changes will be made to the database, allowing you to check the results beforehand.', 'bulk-price-update' ); ?></p>
							</td>
						</tr>

					</tbody>
				</table>

			</div>

		</div>                
		<div style="display: flex; margin-top: 1.5em; height: 2em; align-items: center;">
			<?php wp_nonce_field( 'bpu_product_ids_nonce', 'bpu_product_ids_nonce' );?>
			<?php wp_nonce_field( 'bpu_product_update_nonce', 'bpu_product_update_nonce' );?>
			<input type="submit" name="bpu_save_settings" id="bpu_save_settings" class="button button-primary" value="Submit">
		</div>
		<br />
		<div style="display:none;" id="bpu_loader"><progress class="bpu-progress" max="100" value="0"></progress></div>
		<div style="display:none;" id="bpu_update_product_results">
	        <table class="widefat striped">
		        <thead>
		        	<tr>
		        		<td><?php esc_html_e( 'No.', 'bulk-price-update' );?></td>
		        		<td><?php esc_html_e( 'Thumb', 'bulk-price-update' );?></td>
		        		<td><?php esc_html_e( 'Product ID', 'bulk-price-update' );?></td>
		        		<td><?php esc_html_e( 'Product Name', 'bulk-price-update' );?></td>
		        		<td><?php esc_html_e( 'Product Type', 'bulk-price-update' );?></td>
		        		<td><?php esc_html_e( 'Old Price', 'bulk-price-update' );?> <span class="dashicons dashicons-arrow-right-alt"></span><?php esc_html_e( 'New Price', 'bulk-price-update' );?></td>
		        	</tr>
		        </thead>
		        <tbody id="bpu_update_product_results_body"></tbody>
			</table>
		</div>
	</form>

	<?php if ( isset( $promos ) && ! empty( $promos ) ) : ?>
		<div class="bpu-other-plugins">
			<?php foreach ( $promos as $promo ) : ?>
				<div class="bpu-other-plugin">
					<div class="bpu-other-plugin-title">
						<a href="<?php echo esc_url( $promo['url'] ); ?>" target="_blank"><?php echo esc_html( $promo['title'] ); ?></a>
					</div>
					<div class="bpu-other-plugin-links">
						<div><a href="<?php echo esc_url( $promo['url'] ); ?>" target="_blank"><?php esc_html_e( 'View', 'bulk-price-update' ); ?></a></div>
						<?php if ( isset( $promo['documentation'] ) ) : ?>
							<div><a href="<?php echo esc_url( $promo['documentation'] ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'bulk-price-update' ); ?></a></div>
						<?php endif; ?>
						<?php if ( isset( $promo['support'] ) ) : ?>
							<div><a href="<?php echo esc_url( $promo['support'] ); ?>" target="_blank"><?php esc_html_e( 'Support', 'bulk-price-update' ); ?></a></div>
						<?php endif; ?>
					</div>
					<div class="bpu-other-plugin-image"><a href="<?php echo esc_url( $promo['url'] ); ?>" target="_blank"><img src="<?php echo esc_url( $promo['image'] ); ?>" /></a></div>
					<div class="bpu-other-plugin-desc">
						<?php if ( $promo['initial_link'] ) : ?>
							<a href="<?php echo esc_url( $promo['url'] ); ?>" target="_blank"><?php echo esc_html( $promo['title'] ); ?></a> 
						<?php endif; ?>

						<?php echo $promo['description']; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div>
