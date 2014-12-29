<?php
/**
* @package vegetarianusreceptek
*/
/*
Plugin Name: Vegetáriánus Receptek
Plugin URI: http://wordpress.org/plugins/vegetarianus-receptek/
Version: 1.0.5
Description: Tartalom-érzékeny, automatikusan frissülő ajánlások a <strong><a href="http://vegetarianusreceptek.hu" target="_blank">vegetáriánusreceptek.hu</a></strong> receptjeiből, híreiből. A receptek az Ön oldalának tartalmához illeszkedve jelennek meg, további ajánlatokat adva látogatóinak.
Author: e-presence, Bliszkó Viktor
Author URI: http://www.e-presence.hu
*/

class vegetarianusreceptek_widget extends WP_Widget {

	const VR_TYPE_KERESES = 1;
	const VR_TYPE_AJANLAS = 2;
	const VR_TYPE_MINDKETTO = 3;
	const VR_WIDTH_MIN = 152;
	const VR_WIDTH_MAX = false;
	const VR_HEIGHT_KERESES = 117;
	const VR_HEIGHT_AJANLAS = 200;
	const VR_HEIGHT_MINDKETTO = 200;

	private $default_type;
	private $default_width;
	private $default_height;
	private $default_width_max;

	public function __construct() {
		parent::__construct(
			'vegetarianusreceptek',
			'Vegetáriánus Receptek',
			array('description' => 'Tartalom-érzékeny, automatikusan frissülő ajánlások a vegetáriánusreceptek.hu receptjeiből, híreiből.')
		);
		$this->default_type = self::VR_TYPE_AJANLAS;
		$this->default_width = self::VR_WIDTH_MIN;
		$this->default_height = self::VR_HEIGHT_AJANLAS;
		$this->default_width_max = self::VR_WIDTH_MAX;
	}

	private function filter_type($type) {
		$type = (int)$type;
		if (!in_array($type, array(self::VR_TYPE_AJANLAS, self::VR_TYPE_KERESES, self::VR_TYPE_MINDKETTO))) {
			$type = self::VR_TYPE_AJANLAS;
		}
		return $type;
	}

	private function filter_width($width) {
		$width = (int)$width;
		if ($width < self::VR_WIDTH_MIN) {
			$width = self::VR_WIDTH_MIN;
		}
		return $width;
	}

	private function filter_width_max($width_max) {
		$width_max = (boolean)$width_max;
		return $width_max;
	}

	private function filter_height($height, $type) {
		$height = (int)$height;
		$default_height = array(
			self::VR_TYPE_KERESES 	=> self::VR_HEIGHT_KERESES,
			self::VR_TYPE_AJANLAS 	=> self::VR_HEIGHT_AJANLAS,
			self::VR_TYPE_MINDKETTO	=> self::VR_HEIGHT_MINDKETTO);
		if ($height < $default_height[$type]) {
			$height = $default_height[$type];
		}
		return $height;
	}

	public function widget($args, $instance) {

		$vr_type = $this->filter_type($instance['vr_type']);
		$vr_width = $this->filter_width($instance['vr_width']);
		$vr_width_max = $this->filter_width_max($instance['vr_width_max']);
		if ($vr_width_max) {
			$vr_width = 'max';
		}
		$vr_height = $this->filter_height($instance['vr_height'], $vr_type);

		echo $args['before_widget'];

		echo "<script src=\"http://vegetarianusreceptek.hu/vegetarianusreceptek.js\" type=\"text/javascript\"></script>";
		echo "<script type=\"text/javascript\">try{vegetarianusreceptek.keret({t:'" . $vr_type . "',w:'" . $vr_width . "',h:'" . $vr_height . "'});}catch(evt){alert(evt.message)}</script>";

		echo $args['after_widget'];
	}

	public function form($instance) {

		$vr_type = $instance['vr_type'];
		$vr_width = $instance['vr_width'];
		$vr_width_max = $instance['vr_width_max'];
		$vr_height = $instance['vr_height'];
		$checked = '';
		$disabled = '';
		if ((boolean)$vr_width_max) {
			$checked = ' checked="checked" ';
			$disabled = ' disabled="disabled" ';
			$vr_width = '100%';
		}

		echo "<p>";
		echo "<label for=\"" , $this->get_field_id('vr_type'), "\">Tartalom: </label>";
		echo "<select name=\"", $this->get_field_name('vr_type'), "\" id=\"", $this->get_field_id('vr_type'), "\" class=\"widefat\" style=\"width:auto\">";
		echo "<option value=\"" . self::VR_TYPE_AJANLAS . "\"";
		if (esc_attr($vr_type) == self::VR_TYPE_AJANLAS) {
			echo " selected=\"selected\"";
		}
		echo ">Ajánlás</option>";

		echo "<option value=\"" . self::VR_TYPE_KERESES . "\"";
		if (esc_attr($vr_type) == self::VR_TYPE_KERESES) {
			echo " selected=\"selected\"";
		}
		echo ">Keresés</option>";

		echo "<option value=\"" . self::VR_TYPE_MINDKETTO . "\"";
		if (esc_attr($vr_type) == self::VR_TYPE_MINDKETTO) {
			echo " selected=\"selected\"";
		}
		echo ">Mindkettő</option>";

		echo "</select>";
		echo "</p>";

		echo "<p>";
		echo "<label for=\"", $this->get_field_id('vr_width'), "\">Szélesség: </label>";
		echo "<input class=\"widefat\" id=\"", $this->get_field_id('vr_width'), "\" name=\"", $this->get_field_name('vr_width'), "\" type=\"text\" value=\"", esc_attr($vr_width), "\" $disabled />";
		echo "<input id=\"", $this->get_field_id('vr_width_max'), "\" name=\"", $this->get_field_name('vr_width_max'), "\" value=\"1\" type=\"checkbox\" $checked onclick=\"document.getElementById('" . $this->get_field_id('vr_width') . "').disabled=this.checked\"/>&nbsp;";
		echo "<label for=\"", $this->get_field_id('vr_width_max'), "\">teljes szélesség</label>";
		echo "</p>";

		echo "<p>";
		echo "<label for=\"", $this->get_field_id('vr_height'), "\">Magasság: </label>";
		echo "<input class=\"widefat\" id=\"", $this->get_field_id('vr_height'), "\" name=\"", $this->get_field_name('vr_height'), "\" type=\"text\" value=\"", esc_attr($vr_height), "\" />";
		echo "</p>";

		echo "<p><input type=\"checkbox\" checked=\"checked\" disabled=\"disabled\">&nbsp;A widget külső hivatkozásokat használ, amihez a widget használatával hozzájárulok.</p>";

	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		if (!isset($instance['vr_type'])) {
			$instance['vr_type'] = $this->default_type;
		} else {
			$instance['vr_type'] = $new_instance['vr_type'];
		}
		$instance['vr_type'] = $this->filter_type($instance['vr_type']);

		if (!isset($instance['vr_width'])) {
			$instance['vr_width'] = $this->default_width;
		} else {
			$instance['vr_width'] = $new_instance['vr_width'];
		}
		$instance['vr_width'] = $this->filter_width($instance['vr_width']);

		if (!isset($instance['vr_width_max'])) {
			$instance['vr_width_max'] = $this->default_width_max;
		} else {
			$instance['vr_width_max'] = $new_instance['vr_width_max'];
		}
		$instance['vr_width_max'] = $this->filter_width_max($instance['vr_width_max']);

		if (!isset($instance['vr_height'])) {
			$instance['vr_height'] = $this->default_height;
		} else {
			$instance['vr_height'] = $new_instance['vr_height'];
		}
		$instance['vr_height'] = $this->filter_height($instance['vr_height'], $instance['vr_type']);

		return $instance;
	}

}

function vegetarianusreceptek_load_widget() {
	register_widget('vegetarianusreceptek_widget');
}

add_action('widgets_init', 'vegetarianusreceptek_load_widget');
