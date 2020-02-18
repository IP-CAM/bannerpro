<?php
class ModelExtensionModuleBannerPro extends Model
{

    public function install()
    {
        try {

            $db = $this->db;

            $sql = "DROP TABLE IF EXISTS `oc_bannerpro`;";
            $db->query($sql);

            $sql = "CREATE TABLE `oc_bannerpro` (
			  `banner_id` int(11) NOT NULL AUTO_INCREMENT,
              `module_id` int(11) NOT NULL,
			  `name` varchar(64) NOT NULL,
              `layout` varchar(100) NOT NULL,
              `width` varchar(50) NOT NULL,
              `height` varchar(50) NOT NULL,
			  `status` tinyint(1) NOT NULL,
			  PRIMARY KEY (`banner_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
            $db->query($sql);

            $sql = "DROP TABLE IF EXISTS `oc_bannerpro_image`;";
            $db->query($sql);

            $sql = "CREATE TABLE `oc_bannerpro_image` (
		  `banner_image_id` int(11) NOT NULL AUTO_INCREMENT,
		  `banner_id` int(11) NOT NULL,
		  `language_id` int(11) NOT NULL,
		  `title` varchar(64) NOT NULL,
		  `subtitle` varchar(255) NOT NULL,
          `description` varchar(255) NOT NULL,
		  `link1` varchar(255) NOT NULL,
          `label1` varchar(255) NOT NULL,
          `link2` varchar(255) NOT NULL,
          `label2` varchar(255) NOT NULL,
		  `image` varchar(255) NOT NULL,
          `video` varchar(255) NOT NULL,
		  `sort_order` int(3) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`banner_image_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
            $db->query($sql);
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    public function uninstall()
    {
        try {
            $db = $this->db;

            $sql = "DROP TABLE IF EXISTS `oc_bannerpro`;";
            $db->query($sql);


            $sql = "DROP TABLE IF EXISTS `oc_bannerpro_image`;";
            $db->query($sql);
        } catch (\Throwable $th) {
            echo $th;
        }
    }


    public function addBanner($data)
    {
        $this->db->query(
            "INSERT INTO " . DB_PREFIX . "bannerpro 
        SET name = '" . $this->db->escape($data['name']) . "', 
        status = '" . (int) $data['status'] . "' , 
        module_id = '" . (int) $data['module_id'] . "', 
        layout = '" . $data['layout'] . "',
        width = '" . $data['width'] . "',
        height = '" . $data['height'] . "'"
        
        );

        $banner_id = $this->db->getLastId();

        if (isset($data['banner_image'])) {
            foreach ($data['banner_image'] as $language_id => $value) {
                foreach ($value as $banner_image) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "bannerpro_image 
                    SET banner_id = '" . (int) $banner_id . "',
                     language_id = '" . (int) $language_id . "', 
                     title = '" .  $this->db->escape($banner_image['title']) . "', 
                     subtitle = '" .  $this->db->escape($banner_image['subtitle']) . "', 
                     description = '" .  $this->db->escape($banner_image['description']) . "', 
                     link1 = '" .  $this->db->escape($banner_image['link1']) . "', 
                     label1 = '" .  $this->db->escape($banner_image['label1']) . "', 
                     link2 = '" .  $this->db->escape($banner_image['link2']) . "', 
                     label2 = '" .  $this->db->escape($banner_image['label2']) . "', 
                     image = '" .  $this->db->escape($banner_image['image']) . "', 
                     video = '" .  $this->db->escape($banner_image['video']) . "', 
                     sort_order = '" .  (int) $banner_image['sort_order'] . "'");
                }
            }
        }

        return $banner_id;
    }

    public function editBanner($banner_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "bannerpro 
        SET name = '" . $this->db->escape($data['name']) . "', 
        status = '" . (int) $data['status'] . "' , 
        module_id = '" . (int) $data['module_id'] . "', 
        layout = '" . $data['layout'] . "',
        width = '" . $data['width'] . "',
        height = '" . $data['height'] . "'
             WHERE banner_id = '" . (int) $banner_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "bannerpro_image 
        WHERE banner_id = '" . (int) $banner_id . "'");

        if (isset($data['banner_image'])) {
            foreach ($data['banner_image'] as $language_id => $value) {
                foreach ($value as $banner_image) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "bannerpro_image 
                    SET banner_id = '" . (int) $banner_id . "', 
                    language_id = '" . (int) $language_id . "', 
                    title = '" .  $this->db->escape($banner_image['title']) . "', 
                    subtitle = '" .  $this->db->escape($banner_image['subtitle']) . "', 
                    description = '" .  $this->db->escape($banner_image['description']) . "', 
                    link1 = '" .  $this->db->escape($banner_image['link1']) . "', 
                    label1 = '" .  $this->db->escape($banner_image['label1']) . "', 
                    link2 = '" .  $this->db->escape($banner_image['link2']) . "', 
                    label2 = '" .  $this->db->escape($banner_image['label2']) . "', 
                    image = '" .  $this->db->escape($banner_image['image']) . "', 
                    video = '" .  $this->db->escape($banner_image['video']) . "', 
                    sort_order = '" .  (int) $banner_image['sort_order'] . "'");
                }
            }
        }
    }

    public function deleteBanner($banner_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "bannerpro WHERE banner_id = '" . (int) $banner_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bannerpro_image WHERE banner_id = '" . (int) $banner_id . "'");
    }

    public function getBanner($banner_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "bannerpro 
        WHERE banner_id = '" . (int) $banner_id . "'");

        return $query->row;
    }


    public function getBannerImages($banner_id)
    {
        $banner_image_data = array();

        $banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bannerpro_image WHERE banner_id = '" . (int) $banner_id . "' ORDER BY sort_order ASC");

        foreach ($banner_image_query->rows as $banner_image) {
            $banner_image_data[$banner_image['language_id']][] = array(
                'title'      => $banner_image['title'],
                'subtitle'      => $banner_image['subtitle'],
                'description'      => $banner_image['description'],
                'link1'       => $banner_image['link1'],
                'label1'      => $banner_image['label1'],
                'link2'      => $banner_image['link2'],
                'label2'       => $banner_image['label2'],
                'image'      => $banner_image['image'],
                'video'      => $banner_image['video'],
                'sort_order' => $banner_image['sort_order']
            );
        }

        return $banner_image_data;
    }

    public function getTotalBanners()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "bannerpro");

        return $query->row['total'];
    }

    public function getBannerIdFromModuleId($module_id)
    {
        if ($module_id) {
            $query = $this->db->query("SELECT banner_id FROM " . DB_PREFIX . "bannerpro WHERE module_id= '" . (int) $module_id . "'");
            return (count($query->row)>0)  ? $query->row['banner_id'] : null;
        } else {
            return null;
        }
    }
}
