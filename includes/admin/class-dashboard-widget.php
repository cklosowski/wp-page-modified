<?php

/**
 * Class Page_Modified_Dashboard_Widget
 *
 * @since 1.0
 */
class Page_Modified_Dashboard_Widget {
	private static $instance;

	private function __construct() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->hooks();
	}

	static public function instance() {
		if ( !self::$instance ) {
			self::$instance = new Page_Modified_Dashboard_Widget();
		}

		return self::$instance;
	}

	/**
	 * Register any hooks we need into WordPress.
	 *
	 * @since 1.0
	 */
	private function hooks() {
		add_action( 'wp_dashboard_setup', array( $this, 'register_widget' ) );
	}

	/**
	 * Register the Dashboard widget
	 *
	 * @since 1.0
	 */
	public function register_widget() {
		wp_add_dashboard_widget(
			'page_modified_dashboard_widget',
			__( 'Page Modified &mdash; Last Crawl', 'wp-page-modified' ),
			array( $this, 'render_widget' )
		);
	}

	/**
	 * Render the dashboard widget
	 *
	 * @since 1.0
	 */
	public function render_widget() {
		$last_crawl = wp_page_modified()->active_domain->last_crawl;
		?>
		<canvas id="page-modified-last-crawl" width="400" height="200"></canvas>
		<script>
		jQuery(document).ready(function() {
			var ctx  = document.getElementById("page-modified-last-crawl").getContext("2d");
			var data = {
				datasets: [{
					data: [
						<?php echo $last_crawl->history->status_200; ?>,
						<?php echo $last_crawl->history->status_300; ?>,
						<?php echo $last_crawl->history->status_400; ?>,
						<?php echo $last_crawl->history->status_500; ?>,
					],

					backgroundColor: [
						'#3D9970',
						'#FFDC00',
						'#FF851B',
						'#FF4136',
					],

					label: 'HTTP Reesponse Codes',
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: [
					'2xx',
					'3xx',
					'4xx',
					'5xx',
				],

			};

			var options = {
				legend: {
					position: 'right',
				}
			};

			var wppm_dashboard_widget = new Chart(ctx, {
				type   : 'doughnut',
				data   : data,
				options: options
			});
		});
		</script>
		<div class="wppm-dashboard-widget">
			<div class="table table_left table_urls">
				<table>
					<thead>
					<tr>
						<td colspan="2"><?php _e( 'URLs', 'wp-page-modified' ); ?></td>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="first t total"><?php _e( 'Crawled', 'wp-page-modified' ); ?></td>
						<td class="b b-total" style="font-weight: normal;"><?php echo number_format_i18n( $last_crawl->history->pages_crawled ); ?></td>
					</tr>
					<tr>
						<td class="first t internal"><?php _e( 'Internal Links', 'wp-page-modified' ); ?></td>
						<td class="b b-internal" style="font-weight: normal;"><?php echo number_format_i18n( $last_crawl->history->internal_links ); ?></td>
					</tr>
					<tr>
						<td class="first t external"><?php _e( 'External Links', 'wp-page-modified' ); ?></td>
						<td class="b b-external" style="font-weight: normal;"><?php echo number_format_i18n( $last_crawl->history->external_links ); ?></td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="table table_right table_today">
				<table>
					<thead>
					<tr>
						<td colspan="2">
							<?php _e( 'Response Times', 'wp-page-modified' ); ?>
							<small><em><?php _e( 'in milliseconds', 'wp-page-modified' ); ?></em></small>
						</td>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="t min-response"><?php _e( 'Min', 'wp-page-modified' ); ?></td>
						<td class="last b b-min-response" style="font-weight: normal;"><?php echo number_format_i18n( $last_crawl->{'url-times'}->min, 2 ); ?></td>
					</tr>
					<tr>
						<td class="t max-response"><?php _e( 'Max', 'wp-page-modified' ); ?></td>
						<td class="last b b-max-response" style="font-weight: normal;"><?php echo number_format_i18n( $last_crawl->{'url-times'}->max, 2 ); ?></td>
					</tr>
					<tr>
						<td class="t max-response"><?php _e( 'Average', 'wp-page-modified' ); ?></td>
						<td class="last b b-max-response" style="font-weight: normal;"><?php echo number_format_i18n( $last_crawl->{'url-times'}->avg, 2 ); ?></td>
					</tr>
					<tr>
						<td class="t max-response"><?php _e( 'Standard Deviation', 'wp-page-modified' ); ?></td>
						<td class="last b b-max-response" style="font-weight: normal;"><?php echo number_format_i18n( $last_crawl->{'url-times'}->stddev, 2 ); ?></td>
					</tr>
					</tbody>
				</table>
			</div>

			<div style="clear: both"></div>
			<div>
				<?php $details_url = sprintf( 'https://app.pagemodified.com/domain/%d/histories/%d', $last_crawl->history->domain_id, $last_crawl->history->id ); ?>
				<a class="button secondary" target="_blank" href="<?php echo $details_url; ?>"><?php _e( 'View in Page Modified', 'wp-page-modified' ); ?></a>
				<span class="completed-time">
					<?php printf(
						__( 'Completed on %s at %s', 'wp-page-modified' ),
						date_i18n( get_option( 'date_format' ), strtotime( $last_crawl->history->updated_at ) ),
						date_i18n( get_option( 'time_format' ), strtotime( $last_crawl->history->updated_at ) )
					); ?>
				</span>
			</div>
		</div>
		<?php
	}
}
Page_Modified_Dashboard_Widget::instance();
