<?php
/**
 * Better Category Selector for WooCommerce Settings Class
 */

/**
 * Settings class, based on class from Theme.fm (see link)
 * @package better-category-selector-for-woocommerce
 * @subpackage includes
 * @link http://theme.fm/2011/10/how-to-create-tabs-with-the-settings-api-in-wordpress-2590/
 */
class HD_BCS_Settings {

	/**
	 * @var string
	 */
	protected $general_key = '';

	/**
	 * @var string
	 */
	protected $plugin_key = 'bcs-settings';

	/**
	 * @var array
	 */
	protected $plugin_tabs = array();

	/**
	 * @var array
	 */
	protected $general_settings = array();

	/**
	 * @var string
	 */
	protected $current_tab = '';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'load_settings' ) );
		add_action( 'admin_init', array( $this, 'register_general_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
	}

	public function load_settings() {
		$this->general_settings = HD_BCS_Lib::get_settings();
		$this->general_key      = HD_BCS_Lib::$settings_key;
	}

	public function section_general_desc() {
	}

	public function register_general_settings() {
		$this->plugin_tabs[ $this->general_key ] = esc_attr__( 'Better Category Selector for WooCommerce Settings', HD_BCS_Lib::DOMAIN );
		$this->current_tab = isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : $this->general_key;

		register_setting( $this->general_key, $this->general_key, array( $this, 'validate_input' ) );

		add_settings_section( 'section_general', esc_attr__( 'Better Category Selector for WooCommerce Settings', HD_BCS_Lib::DOMAIN ), array( $this, 'section_general_desc' ), $this->general_key );
		add_settings_field(
			'show_number',
			esc_attr__( 'Add Widget Number Classes', HD_BCS_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key' => 'show_number',
			)
		);
		add_settings_field(
			'show_location',
			esc_attr__( 'Add First/Last Classes', HD_BCS_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key' => 'show_location',
			)
		);
		add_settings_field(
			'show_evenodd',
			esc_attr__( 'Add Even/Odd Classes', HD_BCS_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key' => 'show_evenodd',
			)
		);
		add_settings_field(
			'show_id',
			esc_attr__( 'Show Additional Field for ID', HD_BCS_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key' => 'show_id',
			)
		);
		add_settings_field(
			'type',
			esc_attr__( 'Class Field Type', HD_BCS_Lib::DOMAIN ),
			array( $this, 'type_option' ),
			$this->general_key,
			'section_general'
		);
		add_settings_field(
			'defined_classes',
			esc_attr__( 'Predefined Classes', HD_BCS_Lib::DOMAIN ),
			array( $this, 'defined_classes_option' ),
			$this->general_key,
			'section_general'
		);
		add_settings_field(
			'fix_widget_params',
			esc_attr__( 'Fix widget parameters', HD_BCS_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key'  => 'fix_widget_params',
				'desc' => esc_html__( 'Wrap widget in a <div> element if the parameters are invalid.', HD_BCS_Lib::DOMAIN ),
			)
		);
		add_settings_field(
			'filter_unique',
			esc_attr__( 'Remove duplicate classes', HD_BCS_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key'  => 'filter_unique',
				'desc' => esc_html__( 'Plugins that run after this plugin could still add duplicates.', HD_BCS_Lib::DOMAIN ),
			)
		);
		add_settings_field(
			'translate_classes',
			esc_attr__( 'Translate classes', HD_BCS_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key'  => 'translate_classes',
				'desc' => esc_html__( 'Translate classes like `widget-first` and `widget-even`.', HD_BCS_Lib::DOMAIN )
					// Translators: %s stands for a link to translate.wordpress.org.
					. ' ' . sprintf( esc_html__( 'Translations are taken from %s', HD_BCS_Lib::DOMAIN ), '<a href="https://translate.wordpress.org/projects/wp-plugins/better-category-selector-for-woocommerce" target="_blank">translate.wordpress.org</a>' ),
			)
		);
		do_action( 'widget_css_classes_settings' );
	}

	public function show_yes_no_option( $args ) {
		if ( ! $args['key'] ) return;
		$key = esc_attr( $args['key'] );
		?>
		<label><input type="radio" name="<?php echo esc_attr( $this->general_key . '[' . $key . ']' ); ?>" value="1" <?php checked( $this->general_settings[ $key ], true ); ?> /> <?php esc_attr_e( 'Yes', HD_BCS_Lib::DOMAIN ); ?></label> &nbsp;
		<label><input type="radio" name="<?php echo esc_attr( $this->general_key . '[' . $key . ']' ); ?>" value="0" <?php checked( $this->general_settings[ $key ], false ); ?> /> <?php esc_attr_e( 'No', HD_BCS_Lib::DOMAIN ); ?></label>
		<?php
		if ( ! empty( $args['desc'] ) ) {
			// I need to delete this    echo HD_BCS::do_description( $args['desc'] ); // @codingStandardsIgnoreLine >> no valid esc function.
		}
	}

	public function type_option() {
		?>
		<label><input type="radio" class="hd_bcs_type" name="<?php echo esc_attr( $this->general_key ) . '[type]'; ?>" value="1" <?php checked( $this->general_settings['type'], 1 ); ?> /> <?php esc_attr_e( 'Text', HD_BCS_Lib::DOMAIN ); ?></label> &nbsp;
		<label><input type="radio" class="hd_bcs_type" name="<?php echo esc_attr( $this->general_key ) . '[type]'; ?>" value="2" <?php checked( $this->general_settings['type'], 2 ); ?> /> <?php esc_attr_e( 'Predefined', HD_BCS_Lib::DOMAIN ); ?></label> &nbsp;
		<label><input type="radio" class="hd_bcs_type" name="<?php echo esc_attr( $this->general_key ) . '[type]'; ?>" value="3" <?php checked( $this->general_settings['type'], 3 ); ?> /> <?php esc_attr_e( 'Both', HD_BCS_Lib::DOMAIN ); ?></label> &nbsp;
		<label><input type="radio" class="hd_bcs_type" name="<?php echo esc_attr( $this->general_key ) . '[type]'; ?>" value="0" <?php checked( $this->general_settings['type'], 0 ); ?> /> <?php esc_attr_e( 'None', HD_BCS_Lib::DOMAIN ); ?></label>
		<?php
	}

	public function defined_classes_option() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		$presets = $this->general_settings['defined_classes'];
		?>
		<div class="hd_bcs_sortable">
		<?php
		if ( count( $presets ) > 1 ) {
			foreach ( $presets as $key => $preset ) {
			?>
				<p class="hd_bcs_defined_classes">
					<a class="hd_bcs_sort" href="#"><span class="dashicons dashicons-sort"></span></a>
					<input type="text" name="<?php echo esc_attr( $this->general_key ) . '[defined_classes][' . esc_attr( $key ) . ']'; ?>" value="<?php echo esc_attr( $preset ); ?>" />
					<a class="hd_bcs_remove" href="#"><span class="dashicons dashicons-dismiss"></span></a>
				</p>
			<?php
			}
			?>
			<p class="hd_bcs_defined_classes hd_bcs_sort_fixed">
				<a class="hd_bcs_sort" href="#"><span class="dashicons dashicons-sort"></span></a>
				<input type="text" name="<?php echo esc_attr( $this->general_key ) . '[defined_classes][]'; ?>" value="" />
				<a href="#" class="hd_bcs_copy" rel=".hd_bcs_defined_classes"><span class="dashicons dashicons-plus-alt"></span></a>
				<a class="hd_bcs_remove" href="#"><span class="dashicons dashicons-dismiss"></span></a>
			</p>
		<?php
		} else {
			$value = ( ! empty( $this->general_settings['defined_classes'][0] ) ) ? $this->general_settings['defined_classes'][0] : '';
			?>
			<p class="hd_bcs_defined_classes hd_bcs_sort_fixed">
				<a class="hd_bcs_sort" href="#"><span class="dashicons dashicons-sort"></span></a>
				<input type="text" name="<?php echo esc_attr( $this->general_key ) . '[defined_classes][]'; ?>" value="<?php echo esc_attr( $value ); ?>" />
				<a href="#" class="hd_bcs_copy" rel=".hd_bcs_defined_classes"><span class="dashicons dashicons-plus-alt"></span></a>
				<a class="hd_bcs_remove" href="#"><span class="dashicons dashicons-dismiss"></span></a>
			</p>
		<?php
		}
		?>
		</div>
		<?php
	}

	/**
	 * @param  array $input
	 * @return array
	 */
	public function validate_input( $input ) {
		HD_BCS_Lib::set_settings( $input );
		return HD_BCS_Lib::get_settings();
	}

	public function add_admin_menus() {
		add_options_page( esc_attr__( 'Better Category Selector for WooCommerce', HD_BCS_Lib::DOMAIN ), esc_attr__( 'Better Category Selector for WooCommerce', HD_BCS_Lib::DOMAIN ), 'manage_options', 'bcs-settings', array( $this, 'plugin_options_page' ) );
	}

	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	public function plugin_options_page() {
		$tab = $this->current_tab;
		?>
	<div class="wrap">
    <h1>We aren't cool enough to need settings.  But we will soon.  :-)</h1>
		<?php /* I need to delete this $this->plugin_options_tabs(); */ ?>
		<?php /* <form method="post" action="options.php" enctype="multipart/form-data">
			<?php settings_fields( $tab ); ?>
			<?php do_settings_sections( $tab ); ?>
			<?php if ( 'importexport' === $tab ) $this->importexport_fields(); ?>
			<?php if ( 'importexport' !== $tab ) submit_button(); ?>
		</form> */ ?>
	</div>
	<?php
		add_action( 'in_admin_footer', array( 'HD_BCS_Lib', 'admin_footer' ) );
	}

	/*     /* I need to delete this
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.

	public function plugin_options_tabs() {

		echo '<h1 class="nav-tab-wrapper">';
		foreach ( $this->plugin_tabs as $tab_key => $tab_caption ) {
			$active = $this->current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr( $active ) . '" href="?page=' . esc_attr( $this->plugin_key ) . '&amp;tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption ) . '</a>';
		}
		echo '</h1>';
	}

	public function importexport_fields() {
	?>
		<h3><?php esc_html_e( 'Import/Export Settings', HD_BCS_Lib::DOMAIN ); ?></h3>

		<p><a class="submit button" href="?bcs-settings-export"><?php esc_attr_e( 'Export Settings', HD_BCS_Lib::DOMAIN ); ?></a></p>

		<p>
			<input type="hidden" name="bcs-settings-import" id="bcs-settings-import" value="true" />
			<?php submit_button( esc_attr__( 'Import Settings', HD_BCS_Lib::DOMAIN ), 'button', 'bcs-settings-submit', false ); ?>
			<input type="file" name="bcs-settings-import-file" id="bcs-settings-import-file" />
		</p>
	<?php
	}
	 */
}
