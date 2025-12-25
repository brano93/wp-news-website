<?php if ( is_active_sidebar( 'above-footer-widgets-area' ) ) : ?>
	<div class="above-footer-widget-section">
		<div class="theme-wrapper">
			<div class="above-footer-widget-section-wrap">
				<?php dynamic_sidebar( 'above-footer-widgets-area' ); ?>
			</div>
		</div>
	</div>
<?php endif; ?>
