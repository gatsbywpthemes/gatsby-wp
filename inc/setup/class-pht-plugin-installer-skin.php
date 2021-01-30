<?php
class Quiet_Upgrader_Skin extends WP_Upgrader_Skin {
				/*
				 * Suppress normal upgrader feedback / output
				 */
	public function feedback( $string, ...$args ) {
		/* no output */
	}
	public function header() {
		if ( $this->done_header ) {
			return;
		}
		$this->done_header = true;

	}

	/**
	 */
	public function footer() {
		if ( $this->done_footer ) {
			return;
		}

	}
}
