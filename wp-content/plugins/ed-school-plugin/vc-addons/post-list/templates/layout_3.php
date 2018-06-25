
<div class="item">

			<div class="meta-data">
				<div class="date">
					<!-- <i class="icon-edtime3"></i> -->
					<?php if ( $post_date_format ) : ?>
						<?php echo date_i18n( $post_date_format, strtotime( $post->post_date ) ); ?>
					<?php else: ?>
						<div class="month">
							<?php echo date_i18n( 'M', strtotime( $post->post_date ) ); ?>
						</div>
						<div class="day">
							<?php echo date_i18n( 'd', strtotime( $post->post_date ) ); ?>
						</div>
					<?php endif; ?>
				</div>
				
			</div>
			<h3>
				<a title="<?php echo $post->post_title; ?>"
				   href="<?php echo get_permalink( $post->ID ); ?>">
					<?php echo $post->post_title; ?>
				</a>
			</h3>



</div>