<?php
if ( ! class_exists( 'wvhg_show_hooks' ) ) {

	class wvhg_show_hooks {
		private static $instance;
		/*Regex pattern to check if hook has prefix woocommerce_*/
		protected $ajax_reffer_prefix = 'woo_visual_';
		Protected $condtions = array(
			'is_shop',
			'is_product_category',
			'is_product_tag',
			'is_product',
			'is_cart',
			'is_checkout',
			'is_account_page',
		);

		public function __construct() {
			add_action( 'admin_bar_menu', array( $this, 'tool_bar_link' ), 100 );
			add_action( 'get_header', array( $this, 'check_active_args_page' ) );
			add_action( 'wp_ajax_show_hooks', array( $this, 'list_hook_detais' ) );
			add_action( "wp_ajax_nopriv_show_hooks", array( $this, 'list_hook_detais' ) );
		}

		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}


		public function check_active_args_page() {

			if ( ! 'show' == isset( $_GET['wvhg_hooks'] ) ) {
				return; // Just bail
			}
			if ( ! is_user_logged_in() ) {
				return;  // Just bail
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'wvhg_style' ) );
			$this->hook_html_structure( $this->list_woocommerce_template_hooks() );

		}


		public function list_woocommerce_template_hooks() {
			require 'hook-library.php';

			return $hooks;
		}

		public function hook_html_structure( $hooks ) {
			ob_start();

			?>

            <div id="load-model" class="mfp-hide white-popup">
                <div class="wrap">
                    <div id="wvhg-loader" style="display:none;">
                        <div class="cssload-loader">
                            <div class="cssload-inner cssload-one"></div>
                            <div class="cssload-inner cssload-two"></div>
                            <div class="cssload-inner cssload-three"></div>
                        </div>
                    </div>
                    <div id="show-hooks-result">

                    </div>
                </div>
            </div>
			<?php
			foreach ( $hooks as $hook ) :
				add_action( $hook, function () use ( $hook ) {
					$nonce = wp_create_nonce( $this->ajax_reffer_prefix . $hook );
					?>
                    <div class="<?php echo $hook ?> wvhg_action show_hooks">
                        <a href="#load-model" class="open-hook" href="javascript:void(0)"
                           data-checkreffer="<?php echo $nonce ?>"
                           data-hookname="<?php echo $hook; ?>"><?php echo $hook ?></a>
                    </div>
					<?php
				}, 99 );

			endforeach;
			echo ob_get_clean();

		}

		public function tool_bar_link() {
			global $wp_admin_bar;

			$wp_admin_bar->add_menu( array(
				'id'       => 'wvhg',
				'title'    => __( 'Woo Visual Hook Guide', 'woo-visual-hook-guide' ),
				'href'     => '',
				'position' => 0,
			) );
			if ( ! isset( $_GET['wvhg_hooks'] )  ) {
				$wp_admin_bar->add_menu( array(
					'id'       => 'wvhg_action',
					'parent'   => 'wvhg',
					'title'    => __( 'Show Action Hooks', 'woo-visual-hook-guide' ),
					'href'     => add_query_arg( 'wvhg_hooks', 'show' ),
					'position' => 10,

				) );
			} else {
				$current_url = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				$wp_admin_bar->add_menu( array(
					'id'       => 'wvhg_action',
					'parent'   => 'wvhg',
					'title'    => __( 'Remove Visual Hooks', 'woo-visual-hook-guide' ),
					'href'     => remove_query_arg( array(
						'wvhg_hooks',
					) ),
					'position' => 10,

				) );
			}
		}

		public function wvhg_style() {
			wp_enqueue_style( 'wvhg-style', plugins_url( 'css/wvhg-style.css', WVHG_BASE_PATH ) );
			wp_enqueue_style( 'magnific-css', plugins_url( 'css/magnific-popup.css', WVHG_BASE_PATH ) );
			wp_enqueue_script( 'magnific-js', plugins_url( 'js/jquery.magnific-popup.min.js', WVHG_BASE_PATH ) );

			wp_enqueue_script( 'wvhg-script', plugins_url( 'js/woo-hook-guide.js', WVHG_BASE_PATH ) );
			wp_localize_script( 'wvhg-script', 'wvhg_ajax', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			) );
		}

		public function list_hook_detais() {
			$hook_name = $_POST['hook_name'];
			check_ajax_referer( $this->ajax_reffer_prefix . $hook_name, 'security' );
			$this->get_hooks_params( $hook_name );
			die;

		}

		private function get_hooks_params( $hook_name ) {
			ob_start();
			?>
            <table id="list-hooks" class="wp-list-table widefat striped">
                <tr>
                    <td colspan="5">
                        <h1>Hook Name: <?php echo $hook_name ?></h1>
                    </td>
                </tr>
                <tr>
                    <th>SN#</th>
                    <th>Function Name</th>
                    <th>Priority</th>
                    <th>Arguments Accepted</th>
                </tr>
				<?php
				$count = 1;
				global $wp_filter;
				$action = $wp_filter[ $hook_name ];
				foreach ( $action as $priority => $callbacks ) {
					foreach ( $callbacks as $callback ) {
						$callback = QM_Util::populate_callback( $callback );
						if ( isset( $callback['component'] ) ) {
							$components[ $callback['component']->name ] = $callback['component']->name;
						}
						?>
                        <tr class="list">
                            <td><?php echo $count; ?></td>
                            <td class="name"><?php echo $callback['name']; ?></td>
                            <td><?php echo $priority; ?></td>
                            <td>
                                Accepted Arguments : <?php echo $callback['accepted_args']; ?><br/>
                                File: <?php echo $callback['file']; ?><br/>
                                Line No: <?php echo $callback['line']; ?><br/>

                            </td>
                        </tr>
						<?php
						$count ++;
					}
				}
				?>
            </table>
			<?php
			echo ob_get_clean();
		}


	}

	add_action( 'plugins_loaded', array( 'wvhg_show_hooks', 'get_instance' ) );
}

