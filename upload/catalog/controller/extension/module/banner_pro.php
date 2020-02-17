<?php
class ControllerExtensionModuleBannerPro extends Controller {
        public function index($setting) {
                $this->load->language('extension/module/banner_pro');

                $data['heading_title'] = $this->language->get('heading_title');

                return $this->load->view('extension/module/banner_pro', $data);

        }
}
