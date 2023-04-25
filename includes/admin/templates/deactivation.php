<?php
/**
 * Bulk_Price_Update deactivation Content.
 *
 * @package Bulk_Price_Update
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bpu_deactivation_nonce = wp_create_nonce( 'bpu_deactivation_nonce' );
?>

<div class="bpu-popup-overlay">
	<div class="bpu-serveypanel">
		<form action="#" method="post" id="bpu-deactivate-form">
			<div class="bpu-popup-header">
				<h2>
					<?php
						/* translators: %s: Plugin Name */
						sprintf( esc_html__( 'Quick feedback about %s', 'bulk-price-update' ), BPU_NAME );
					?>
				</h2>
			</div>
			<div class="bpu-popup-body">
				<h3><?php esc_html_e( 'If you have a moment, please let us know why you are deactivating:', 'bulk-price-update' ); ?></h3>
				<input type="hidden" class="bpu_deactivation_nonce" name="bpu_deactivation_nonce" value="<?php echo esc_attr( $bpu_deactivation_nonce ); ?>">
				<ul id="bpu-reason-list">
					<li class="bpu-reason" data-input-type="" data-input-placeholder="">
						<label>
							<span>
								<input type="radio" name="bpu-selected-reason" value="1">
							</span>
							<span class="reason_text"><?php esc_html_e( 'I only needed the plugin for a short period', 'bulk-price-update' ); ?></span>
						</label>
						<div class="bpu-internal-message"></div>
					</li>
					<li class="bpu-reason has-input" data-input-type="textfield">
						<label>
							<span>
								<input type="radio" name="bpu-selected-reason" value="2">
							</span>
							<span class="reason_text"><?php esc_html_e( 'I found a better plugin', 'bulk-price-update' ); ?></span>
						</label>
						<div class="bpu-internal-message"></div>
						<div class="bpu-reason-input"><span class="message error-message "><?php esc_html_e( 'Kindly tell us the Plugin name.', 'bulk-price-update' ); ?></span><input type="text" name="better_plugin" placeholder="What's the plugin's name?"></div>
					</li>
					<li class="bpu-reason" data-input-type="" data-input-placeholder="">
						<label>
							<span>
								<input type="radio" name="bpu-selected-reason" value="3">
							</span>
							<span class="reason_text"><?php esc_html_e( 'The plugin broke my site', 'bulk-price-update' ); ?></span>
						</label>
						<div class="bpu-internal-message"></div>
					</li>
					<li class="bpu-reason" data-input-type="" data-input-placeholder="">
						<label>
							<span>
								<input type="radio" name="bpu-selected-reason" value="4">
							</span>
							<span class="reason_text"><?php esc_html_e( 'The plugin suddenly stopped working', 'bulk-price-update' ); ?></span>
						</label>
						<div class="bpu-internal-message"></div>
					</li>
					<li class="bpu-reason" data-input-type="" data-input-placeholder="">
						<label>
							<span>
							<input type="radio" name="bpu-selected-reason" value="5">
							</span>
							<span class="reason_text"><?php esc_html_e( 'I no longer need the plugin', 'bulk-price-update' ); ?></span>
						</label>
						<div class="bpu-internal-message"></div>
					</li>
					<li class="bpu-reason" data-input-type="" data-input-placeholder="">
						<label>
							<span>
								<input type="radio" name="bpu-selected-reason" value="6">
							</span>
							<span class="reason_text"><?php esc_html_e( "It's a temporary deactivation. I'm just debugging an issue.", 'bulk-price-update' ); ?></span>
						</label>
						<div class="bpu-internal-message"></div>
					</li>
					<li class="bpu-reason has-input" data-input-type="textfield" >
						<label>
							<span>
								<input type="radio" name="bpu-selected-reason" value="7">
							</span>
							<span class="reason_text"><?php esc_html_e( 'Other', 'bulk-price-update' ); ?></span>
						</label>
						<div class="bpu-internal-message"></div>
						<div class="bpu-reason-input"><span class="message error-message "><?php esc_html_e( 'Kindly tell us the reason so we can improve.', 'bulk-price-update' ); ?></span><input type="text" name="other_reason" placeholder="Kindly tell us the reason so we can improve."></div>
					</li>
				</ul>
			</div>
			<div class="bpu-popup-footer">
				<label class="bpu-anonymous"><input type="checkbox" /><?php esc_html_e( 'Anonymous feedback', 'bulk-price-update' ); ?></label>
				<input type="button" class="button button-secondary button-skip bpu-popup-skip-feedback" value="<?php esc_html_e( 'Skip & Deactivate', 'bulk-price-update' ); ?>" >
				<div class="action-btns">
					<span class="bpu-spinner"><img src="<?php echo esc_url( admin_url( '/images/spinner.gif' ) ); ?>" alt=""></span>
					<input type="submit" class="button button-secondary button-deactivate bpu-popup-allow-deactivate" value="<?php esc_html_e( 'Submit & Deactivate', 'bulk-price-update' ); ?>" disabled="disabled">
					<a href="#" class="button button-primary bpu-popup-button-close"><?php esc_html_e( 'Cancel', 'bulk-price-update' ); ?></a>
				</div>
			</div>
		</form>
	</div>
</div>
