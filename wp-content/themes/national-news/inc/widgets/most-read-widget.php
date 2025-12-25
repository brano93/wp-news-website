<?php
if ( ! class_exists( 'National_News_Most_Read_Widget' ) ) {
	/**
	 * Adds National News Most Read Widget.
	 */
	class National_News_Most_Read_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			$most_read_widget = array(
				'classname'   => 'widget adore-widget most-read-widget',
				'description' => __( 'Retrive Most Read Widgets', 'national-news' ),
			);
			parent::__construct(
				'national_news_most_read_widget',
				__( 'Adore Widget: Most Read Widget', 'national-news' ),
				$most_read_widget
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}
			$most_read_title       = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
			$most_read_title       = apply_filters( 'widget_title', $most_read_title, $instance, $this->id_base );
			$viewall_button_label  = ( ! empty( $instance['view_all_button_label'] ) ) ? $instance['view_all_button_label'] : '';
			$most_read_post_count  = isset( $instance['number'] ) ? absint( $instance['number'] ) : 6;
			$most_read_post_offset = isset( $instance['offset'] ) ? absint( $instance['offset'] ) : '';
			$most_read_category    = isset( $instance['category'] ) ? absint( $instance['category'] ) : '';
			$viewall_button_link   = ( ! empty( $instance['view_all_button_link'] ) ) ? $instance['view_all_button_link'] : esc_url( get_category_link( $most_read_category ) );

			echo $args['before_widget'];
			if ( !empty( $most_read_title || $viewall_button_label ) ) {
				?>
				<div class="widget-header">
					<?php
					echo $args['before_title'] . esc_html( $most_read_title ) . $args['after_title'];
					if ( ! empty( $viewall_button_label ) ) {
						?>
						<a href="<?php echo esc_url( $viewall_button_link ); ?>"><?php echo esc_html( $viewall_button_label ); ?></a>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="adore-widget-body">
				<div class="most-read-widget-wrapper">
					<?php
					$most_read_widgets_args = array(
						'post_type'      => 'post',
						'posts_per_page' => absint( $most_read_post_count ),
						'offset'         => absint( $most_read_post_offset ),
						'cat'            => absint( $most_read_category ),
					);
					$query                  = new WP_Query( $most_read_widgets_args );
					if ( $query->have_posts() ) :
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<div class="post-item post-list">
								<div class="post-item-image">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail(); ?>							
									</a>
								</div>
								<div class="post-item-content">
									<h3 class="entry-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>  
									<ul class="entry-meta">
										<li class="post-author"> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><span class="far fa-user"></span><?php echo esc_html( get_the_author() ); ?></a></li>
										<li class="post-date"> <span class="far fa-calendar-alt"></span><?php echo esc_html( get_the_date() ); ?></li>
										<li class="post-comment"> <span class="far fa-comment"></span><?php echo absint( get_comments_number( get_the_ID() ) ); ?></li>
									</ul>
								</div>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
			</div>
			<?php
			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$most_read_title       = isset( $instance['title'] ) ? $instance['title'] : '';
			$viewall_button_label  = isset( $instance['view_all_button_label'] ) ? $instance['view_all_button_label'] : '';
			$viewall_button_link   = isset( $instance['view_all_button_link'] ) ? $instance['view_all_button_link'] : '';
			$most_read_post_count  = isset( $instance['number'] ) ? absint( $instance['number'] ) : 6;
			$most_read_post_offset = isset( $instance['offset'] ) ? absint( $instance['offset'] ) : '';
			$most_read_category    = isset( $instance['category'] ) ? absint( $instance['category'] ) : '';
			$most_read_orderby     = isset( $instance['orderby'] ) && in_array( $instance['orderby'], array( 'title', 'date' ) ) ? $instance['orderby'] : 'date';
			$most_read_order       = isset( $instance['order'] ) && in_array( $instance['order'], array( 'asc', 'desc' ) ) ? $instance['order'] : 'desc';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Section Title:', 'national-news' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $most_read_title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'view_all_button_label' ) ); ?>"><?php esc_html_e( 'View All Button:', 'national-news' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'view_all_button_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'view_all_button_label' ) ); ?>" type="text" value="<?php echo esc_attr( $viewall_button_label ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'view_all_button_link' ) ); ?>"><?php esc_html_e( 'View All Button URL:', 'national-news' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'view_all_button_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'view_all_button_link' ) ); ?>" type="text" value="<?php echo esc_attr( $viewall_button_link ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show(Min:1 | Max:8):', 'national-news' ); ?></label>
				<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" max="8" value="<?php echo absint( $most_read_post_count ); ?>" size="3" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>"><?php esc_html_e( 'Number of posts to displace or pass over:', 'national-news' ); ?></label>
				<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="number" step="1" min="0" value="<?php echo absint( $most_read_post_offset ); ?>" size="3" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Select the category to show posts:', 'national-news' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" class="widefat" style="width:100%;">
					<?php
					$categories = national_news_get_post_cat_choices();
					foreach ( $categories as $category => $value ) {
						?>
						<option value="<?php echo absint( $category ); ?>" <?php selected( $most_read_category, $category ); ?>><?php echo esc_html( $value ); ?></option>
					<?php } ?>      
				</select>
			</p>
			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                          = $old_instance;
			$instance['title']                 = sanitize_text_field( $new_instance['title'] );
			$instance['view_all_button_label'] = sanitize_text_field( $new_instance['view_all_button_label'] );
			$instance['view_all_button_link']  = esc_url_raw( $new_instance['view_all_button_link'] );
			$instance['number']                = (int) $new_instance['number'];
			$instance['offset']                = (int) $new_instance['offset'];
			$instance['category']              = (int) $new_instance['category'];
			return $instance;
		}

	}
}
