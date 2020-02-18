<?php
class ModelExtensionModuleBannerPro extends Model {
	public function getBanner($banner_id) {
		//var_dump("SELECT * FROM " . DB_PREFIX . "bannerpro b LEFT JOIN " . DB_PREFIX . "bannerpro_image bi ON (b.banner_id = bi.banner_id) WHERE b.banner_id = '" . (int)$banner_id . "' AND b.status = '1' AND bi.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY bi.sort_order ASC");
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bannerpro b LEFT JOIN " . DB_PREFIX . "bannerpro_image bi ON (b.banner_id = bi.banner_id) WHERE b.banner_id = '" . (int)$banner_id . "' AND b.status = '1' AND bi.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY bi.sort_order ASC");
		return $query->rows;
	}
}
