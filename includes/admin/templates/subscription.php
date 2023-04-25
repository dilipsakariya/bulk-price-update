<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bpu-subscription-callout-wrapper">
	<div class="bpu-subscription-callout">
		<div class="bpu-subscription-callout-main">
			<h3><?php esc_html_e( 'Subscribe to our Newsletter', 'bulk-price-update' ); ?></h3>
			<p>
				<?php
				/* translators: %1$s: Plugins & Snippets Website URL. */
				printf(
					__( 'Receive updates from <a href="%s" target="_blank">Plugins & Snippets</a> with respect to WordPress plugins aimed to enhance the conversion rates of your web stores.', 'bulk-price-update' ),
					esc_url( 'https://www.pluginsandsnippets.com' )
				);
				?>
			</p>

			<div class="bpu-subscription-error" style="display: none;"><?php esc_html_e( 'There was an error in processing your request, please try again.', 'bulk-price-update' ); ?></div>

			<form method="POST" class="bpu-subscription-form">
				<input type="email" required value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>">

				<?php wp_nonce_field( 'bpu_subscribe' ); ?>

				<div class="bpu-subscription-actions">
					<button class="button-primary"><?php esc_html_e( 'Subscribe', 'bulk-price-update' ); ?></button>
				</div>
			</form>
		</div>
		<p class="bpu-subscription-callout-thanks" style="display: none;"><?php esc_html_e( 'Thank you for signing up to our Newsletter!', 'bulk-price-update' ); ?></p>
	</div>
</div>
