<?php
class ControllerExtensionModuleBannerPro extends Controller
{

	private $error = array();

	public function index()
	{
		$this->load->language('extension/module/banner_pro');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/banner_pro');

		$banner_id = null;
		if (isset($this->request->get['module_id'])) $banner_id = $this->model_extension_module_banner_pro->getBannerIdFromModuleId($this->request->get['module_id']);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->load->model('setting/module');
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('banner_pro', $this->request->post);
				$models = $this->model_setting_module->getModulesByCode('banner_pro');
				$module_id = null;
				foreach ($models as $key => $value) {
					$data = json_decode($value['setting']);

					if ($data->name === $this->request->post['name']) {
						$module_id = $value['module_id']; //FIX UNIQUE NAME FOR BANNERS
						break;
					}
				}
				$this->request->post['module_id'] = $module_id;
				$banner_id = $this->model_extension_module_banner_pro->addBanner($this->request->post);
				$this->request->post['banner_id'] = $banner_id;
				$this->model_setting_module->editModule($module_id, $this->request->post);
			} else {
				$this->request->post['module_id'] = $this->request->get['module_id'];
				$banner_id = $this->model_extension_module_banner_pro->getBannerIdFromModuleId($this->request->post['module_id']);
				$this->request->post['banner_id'] = $banner_id;
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
				$this->model_extension_module_banner_pro->editBanner($banner_id, $this->request->post);
			}


			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm($banner_id);
	}

	public function install()
	{
		$this->load->model('extension/module/banner_pro');

		$this->model_extension_module_banner_pro->install();
	}

	public function uninstall()
	{
		$this->load->model('extension/module/banner_pro');

		$this->model_extension_module_banner_pro->uninstall();
	}

	protected function setValueData($name, $module_info, $initialVal = '')
	{
		$value = $initialVal;
		if (isset($this->request->post[$name])) {
			$value = $this->request->post[$name];
		} elseif (!empty($module_info) && isset($module_info[$name])) {
			$value = $module_info[$name];
		}

		return $value;
	}

	protected function setBreadcrumbs()
	{
		$breadcrumbs = array();

		$breadcrumbs[] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$breadcrumbs[] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$breadcrumbs[] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/banner_pro', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$breadcrumbs[] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/banner_pro', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}
		return $breadcrumbs;
	}



	public function delete()
	{

		$this->load->language('extension/module/banner_pro');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/banner_pro');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $banner_id) {
				$this->model_extension_module_banner_pro->deleteBanner($banner_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			//$this->response->redirect($this->url->link('extension/module/banner_pro', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}



	protected function getForm($banner_id = null)
	{


		$data['text_form'] = !isset($banner_id) ? $this->language->get('text_add') : $this->language->get('text_edit');


		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
		$data['error_banner_image'] = isset($this->error['banner_image']) ? $this->error['banner_image'] : array();


		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = $this->setBreadcrumbs();

		if (!isset($banner_id)) {
			$data['action'] = $this->url->link('extension/module/banner_pro', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/module/banner_pro', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$banner_info = null;
		if (isset($banner_id) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$banner_info = $this->model_extension_module_banner_pro->getBanner($banner_id);
		}


		$data['user_token'] = $this->session->data['user_token'];

		$data['name'] = $this->setValueData('name', $banner_info);
		$data['status'] = $this->setValueData('status', $banner_info, true);
		$data['layout'] = $this->setValueData('layout', $banner_info, 'banner_pro');
		$data['width'] = $this->setValueData('width', $banner_info);
		$data['height'] = $this->setValueData('height', $banner_info);


		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');

		if (isset($this->request->post['banner_image'])) {
			$banner_images = $this->request->post['banner_image'];
		} elseif (isset($banner_id)) {
			$banner_images = $this->model_extension_module_banner_pro->getBannerImages($banner_id);
		} else {
			$banner_images = array();
		}

		$data['banner_images'] = array();

		foreach ($banner_images as $key => $value) {
			foreach ($value as $banner_image) {
				if (is_file(DIR_IMAGE . $banner_image['image'])) {
					$image = $banner_image['image'];
					$thumb = $banner_image['image'];
				} else {
					$image = '';
					$thumb = 'no_image.png';
				}

				$data['banner_images'][$key][] = array(
					'title'      => $banner_image['title'],
					'subtitle'      => $banner_image['subtitle'],
					'description'      => $banner_image['description'],
					'link1'       => $banner_image['link1'],
					'label1'      => $banner_image['label1'],
					'link2'      => $banner_image['link2'],
					'label2'       => $banner_image['label2'],
					'video'      => $banner_image['video'],
					'image'      => $image,
					'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
					'sort_order' => $banner_image['sort_order']
				);
			}
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/banner_pro', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/banner_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (isset($this->request->post['banner_image'])) {
			foreach ($this->request->post['banner_image'] as $language_id => $value) {
				foreach ($value as $banner_image_id => $banner_image) {
					if ((utf8_strlen($banner_image['title']) < 2) || (utf8_strlen($banner_image['title']) > 64)) {
						$this->error['banner_image'][$language_id][$banner_image_id] = $this->language->get('error_title');
					}
				}
			}
		}

		return !$this->error;
	}

	protected function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/banner_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
