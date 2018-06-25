<?php
add_action( 'wp_enqueue_scripts', 'scp_scripts', 100 );

add_filter( 'the_content', 'scp_content_filter' );
add_filter( 'mce_external_plugins', 'scp_add_tinymce_plugins' );
add_filter( 'mce_buttons_3', 'scp_register_tinymce_buttons' );

add_shortcode( 'scp_icon_bullet_text', 'scp_icon_bullet_text_shortcode' );
add_shortcode( 'scp_icon', 'scp_icon_shortcode' );
add_shortcode( 'scp_separator', 'scp_separator_shortcode' );
add_shortcode( 'scp_block_quote_alt', 'scp_block_quote_alt_shortcode' );
add_shortcode( 'scp_table', 'scp_table_shortcode' );
add_shortcode( 'scp_table_row', 'scp_table_row_shortcode' );

// For fa_icon shortcode
function scp_scripts() {
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', false );
	wp_enqueue_style( 'sp-style', SCP_PLUGIN_URL . 'public/css/style.css', false );
}


function scp_content_filter( $content ) {
	// array of custom shortcodes requiring the fix
	$block = join( '|', array(
		'scp_icon_bullet_text',
		'scp_icon',
		'scp_separator',
		'scp_block_quote_alt',
		'scp_table',
		'scp_table_row_shortcode',
	) );

	// opening tag
	$rep = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );

	// closing tag
	$rep = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep );

	return $rep;
}

function scp_add_tinymce_plugins( $plugin_array ) {
	$plugin_array['scp_mce_shortcodes'] = SCP_PLUGIN_URL . '/public/js/tinymce/customcodes.js';

	return $plugin_array;
}

function scp_register_tinymce_buttons( $buttons ) {
	array_push( $buttons, 'scp_mce_shortcodes' );

	return $buttons;
}

function scp_icon_bullet_text_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'icon'              => 'fa fa-clock-o',
		'icon_font_size'    => '14',
		'icon_color'        => '#000',
		'title'             => 'The Title',
		'title_tag'         => 'h3',
		'title_color'       => '#000',
		'subtitle_tag'      => 'h3',
		'subtitle'          => 'The Subtitle',
		'subtitle_color'    => '#000',
		'description_tag'   => 'p',
		'description_color' => '#000',
		'float'             => 'left',
		'padding'           => '0px',
		'margin'            => '0px',
		'text_block_margin' => '0px',
		'subtitle_is_above' => '0',
	), $atts ) );

	$uid = uniqid( 'scp_icon_bullet_text' );

	$out      = '';
	$iconSize = (int) $icon_font_size;

	if ( $float == 'right' ) {
		$float = 'pull-right';
	} elseif ( $float == 'left' ) {
		$float = 'pull-left';
	} else {
		$float = '';
	}


	/**
	 * Main Wrapper Style
	 */
	$mainWrapperStyle = '';

	$mainWrapperStyle .= 'style="';
	$mainWrapperStyle .= 'padding:' . $padding . ';';
	$mainWrapperStyle .= 'margin:' . $margin . ';';
	$mainWrapperStyle .= '"';

	/**
	 * Icon Wrapper Style
	 */
	$iconWrapperStyle = '';

	$iconWrapperStyle .= 'style="';
	$iconWrapperStyle .= 'color:' . $icon_color . ';';
	$iconWrapperStyle .= 'font-size:' . $icon_font_size . ';';
	$iconWrapperStyle .= '"';

	/**
	 * Title Style
	 */
	$titleStyle = '';

	$titleStyle .= 'style="';
	$titleStyle .= 'color:' . $title_color . ';';
	$titleStyle .= '"';

	/**
	 * Title Block Style
	 */
	$titleBlockStyle = '';

	$titleBlockStyle .= 'style="';
	$titleBlockStyle .= 'margin:' . $text_block_margin . ';';
	$titleBlockStyle .= '"';

	/**
	 * Subtitle Style
	 */
	$subtitleStyle = '';

	$subtitleStyle .= 'style="';
	$subtitleStyle .= 'color:' . $subtitle_color . ';';
	$subtitleStyle .= '"';

	/**
	 * Description Style
	 */
	$descriptionStyle = '';

	$descriptionStyle .= 'style="';
	$descriptionStyle .= 'color:' . $description_color . ';';
	$descriptionStyle .= '"';


	$titleTagOpen        = ! empty( $title_tag ) ? '<' . $title_tag . ' class="title" ' . $titleStyle . '>' : '';
	$titleTagClose       = ! empty( $title_tag ) ? '</' . $title_tag . '>' : '';
	$subtitleTagOpen     = ! empty( $subtitle_tag ) ? '<' . $subtitle_tag . ' class="subtitle" ' . $subtitleStyle . '>' : '';
	$subtitleTagClose    = ! empty( $subtitle_tag ) ? '</' . $subtitle_tag . '>' : '';
	$descriptionTagOpen  = ! empty( $description_tag ) ? '<' . $description_tag . ' class="description" ' . $descriptionStyle . '>' : '';
	$descriptionTagClose = ! empty( $description_tag ) ? '</' . $description_tag . '>' : '';


	$out .= '<div class="scp-shortcode scp-icon-bullet-text ' . $float . '" ' . $mainWrapperStyle . '>';
	$out .= '<div class="align-center scp-icon-bullet-text-icon" ' . $iconWrapperStyle . '>';
	$out .= '<i class="' . $icon . '"></i>';
	$out .= '</div>';
	$out .= '<div class="scp-icon-bullet-text-text pad-left" ' . $titleBlockStyle . '>';

	if ( (int) $subtitle_is_above ) {
		$out .= $subtitle ? $subtitleTagOpen . html_entity_decode( $subtitle ) . $subtitleTagClose : '';
		$out .= $title ? $titleTagOpen . html_entity_decode( $title ) . $titleTagClose : '';
	} else {
		$out .= $title ? $titleTagOpen . html_entity_decode( $title ) . $titleTagClose : '';
		$out .= $subtitle ? $subtitleTagOpen . html_entity_decode( $subtitle ) . $subtitleTagClose : '';
	}

	$out .= $content ? $descriptionTagOpen . html_entity_decode( $content ) . $descriptionTagClose : '';
	$out .= '</div>';
	$out .= '</div>';

	return $out;
}

/**
 *  [scp_icon icon="fa-twitter" link="absolute url" size="20px" color="#fff" hover_color="#fff" float="right" margin="0 5px"]
 */
function scp_icon_shortcode( $atts ) {

	extract( shortcode_atts( array(
		'link'        => '#',
		'icon'        => '',
		'size'        => '24px',
		'color'       => '#fff',
		'hover_color' => '#fff',
		'float'       => 'right',
		'margin'      => '0 5px',
		'line_height' => '30px',
		'bg_color'    => '',
		'bg_width'    => '',
	), $atts ) );

	$uid = uniqid( 'fa_icon' );

	/**
	 * Background Style
	 */
	$bg_style = '';

	$bg_style .= 'style="';
	if ( $bg_color ) {
		$bg_style .= 'background-color:' . $bg_color . ';';
	}
	if ( $bg_width ) {
		$bg_style .= 'width:' . $bg_width . ';';
	}
	if ( $margin ) {
		$bg_style .= 'margin:' . $margin . ';';
	}
	$bg_style .= 'text-align:center;';
	$bg_style .= '"';

	/**
	 * Icon Style
	 */
	$style = '';

	$style .= 'style="';
	$style .= 'color:' . $color . ';';
	$style .= 'font-size:' . $size . ';';
	$style .= 'line-height:' . $line_height . ';';
	$style .= '"';

	$hover = 'onMouseOver="this.style.color=\'' . $hover_color . '\'"';
	$hover .= ' onMouseOut="this.style.color=\'' . $color . '\'"';

	if ( $float == 'right' ) {
		$float = 'pull-right';
	} elseif ( $float == 'left' ) {
		$float = 'pull-left';
	} else {
		$float = '';
	}

	$out = '<span class="scp-icon scp-icon-background ' . $float . '" ' . $bg_style . '>';
	if ( $link ) {
		$out .= '<a href="' . $link . '" target="_blank">';
	}

	$out .= '<i class="fa ' . $icon . '" ' . $style . ' ' . $hover . '></i>';
	if ( $link ) {
		$out .= '</a> ';
	}
	$out .= '</span>';

	return $out;
}

/**
 *  [scp_separator type="vertical"]
 */
function scp_separator_shortcode( $atts ) {

	extract( shortcode_atts( array(
		'type'           => 'horizontal',
		'width'          => '1px',
		'height'         => '50px',
		'color'          => '#000',
		'margin'         => '20px',
		'float'          => 'left',
		'show_on_mobile' => 'no',
	), $atts ) );

	$style = '';

	$style .= 'style="';
	$style .= 'width:' . $width . ';';
	$style .= 'height:' . $height . ';';
	$style .= 'background-color:' . $color . ';';
	$style .= 'margin:' . $margin . ';';
	$style .= 'float:' . $float . ';';
	$style .= '"';

	$class = 'scp-shortcode-separator';
	if ( $show_on_mobile == 'no' ) {
		$class .= ' hide-on-mobile hide-on-small-tablet';
	}

	$out = '<div class="' . $class . '" ' . $style . '></div>';


	return $out;
}

/**
 *  [scp_block_quote_alt width="50%" float="right"][/scp_block_quote_alt]
 */
function scp_block_quote_alt_shortcode( $atts, $content ) {

	extract( shortcode_atts( array(
		'width' => '50%',
		'float' => 'right',
	), $atts ) );

	$float = $float == 'right' ? 'right' : 'left';

	$style = '';
	$style .= 'style="';
	$style .= 'width:' . $width . ';';
	$style .= 'float:' . $float . ';';
	$style .= '"';


	$class = 'scp-block-quote-alt ' . $float;

	$out = '<div class="' . $class . '" ' . $style . '>' . do_shortcode( $content ) . '</div>';

	return $out;
}

function scp_table_row_shortcode( $atts ) {

	extract( shortcode_atts( array(
		'labels' => false,
		'class'  => '',
	), $atts ) );

	if ( ! $labels ) {
		return;
	}

	if ( $class ) {
		$class = " class=\"{$class}\"";
	}

	global $scp_table_shortcode_columns;
	global $scp_table_shortcode_column_highlight;
	$parts = explode( '|', $labels );
	?>
	<tr<?php echo $class; ?>>
		<?php foreach ( $parts as $key => $part ) : ?>
			<?php $column_class = $key == $scp_table_shortcode_column_highlight - 1 ? 'class="column-highlight"' : ''; ?>
			<td <?php echo $column_class; ?> data-title="<?php echo isset( $scp_table_shortcode_columns[ $key ] ) ? trim( $scp_table_shortcode_columns[ $key ] ) : ''; ?>"><?php echo trim( $part ); ?></td>
		<?php endforeach; ?>
	</tr>
<?php
}

$scp_table_shortcode_columns          = array();
$scp_table_shortcode_column_highlight = false;

function scp_table_shortcode( $atts, $content ) {

	extract( shortcode_atts( array(
		'columns'   => false,
		'highlight' => false,
	), $atts ) );

	if ( ! $columns ) {
		return;
	}
	global $scp_table_shortcode_columns;
	global $scp_table_shortcode_column_highlight;
	$scp_table_shortcode_columns          = explode( '|', $columns );
	$scp_table_shortcode_column_highlight = (int) $highlight;

	?>
	<div class='rg-container'>
		<div class='rg-content'>
			<table class='rg-table'>
				<thead>
				<?php foreach ( $scp_table_shortcode_columns as $scp_table_shortcode_column ) : ?>
					<th><?php echo trim( $scp_table_shortcode_column ); ?></th>
				<?php endforeach; ?>
				</thead>
				<tbody>
				<?php echo do_shortcode( $content ); ?>
				</tbody>
			</table>
		</div>
	</div>
<?php
}
