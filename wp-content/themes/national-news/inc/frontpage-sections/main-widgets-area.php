<?php
$full_width = ! is_active_sidebar( 'secondary-widgets-area' ) ? 'full-width' : '';
if ( is_active_sidebar( 'primary-widgets-area' ) || is_active_sidebar( 'secondary-widgets-area' ) ) :
	?>
	<div class="main-widget-section">
		<div class="theme-wrapper">
			<div class="main-widget-section-wrap secondary-left-position <?php echo esc_attr( $full_width ); ?>">
				<div class="primary-widgets-area">
					<?php dynamic_sidebar( 'primary-widgets-area' ); ?>
				</div>
				<div class="secondary-widgets-area">
					<?php dynamic_sidebar( 'secondary-widgets-area' ); ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
