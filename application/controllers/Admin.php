<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Admin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if ($this->ion_auth->logged_in() === false) {
			exit();
		}

		$this->load->helper('url');
		$this->load->model('admin_model');
		$this->load->model('user_model');
		$this->load->model('store_model');
		$this->load->model('logs_model');
		$this->load->model('notification_model');
		$this->load->model('report_model');
	}

	public function products()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$products = $this->admin_model->getProducts();

				$response = array(
					"message" => "Successfully products",
					"data" => $products,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function setting_shop_product_type()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$product_types = $this->admin_model->getProductTypes();

				$response = array(
					"message" => "Successfully product types",
					"data" => $product_types,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function setting_delete_shop_product()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'DELETE':
				$product_id = $this->input->get('id');

				$this->admin_model->removeShopProduct($product_id);
				$this->admin_model->removeShopProductCategory($product_id);
				$this->admin_model->removeShopProductRegionDaLogs($product_id);
				$this->admin_model->removeProductWithAddons($product_id);
				$this->admin_model->removeCateringProductAddons($product_id);

				$product_variants = $this->admin_model->getProductVariants($product_id);

				foreach ($product_variants as $product_variant) {

					$product_variant_options = $this->admin_model->getProductVariantOptions($product_variant->id);
					$this->admin_model->removeProductVariant($product_variant->id);

					foreach ($product_variant_options as $product_variant_option) {

						$product_variant_option_combinations = $this->admin_model->getProductVariantOptionCombinations($product_variant_option->id);
						$this->admin_model->removeProductVariantOption($product_variant_option->id);

						foreach ($product_variant_option_combinations as $product_variant_option_combination) {

							$this->admin_model->removeProductSku($product_variant_option_combination->sku_id);
							$this->admin_model->removeProductVariantOptionCombination($product_variant_option_combination->id);
						}
					}
				}


				$response = array(
					"message" =>  'Successfully delete product'
				);
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function setting_edit_shop_product()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$product_image_name = str_replace(' ', '-', strtolower($this->input->post('name'))) . '-' . time() . '.jpg';
				$product_id = $this->input->post('id');

				$product = $this->admin_model->getShopProduct($product_id);

				if (isset($_FILES['image500x500']['tmp_name']) && is_uploaded_file($_FILES['image500x500']['tmp_name'])) {
					$image500x500_error = upload('image500x500', './assets/images/shared/products/500', $product_image_name, 'jpg');
					if ($image500x500_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image500x500_error));
						return;
					}
				} else {
					if ($product->product_image !== $product_image_name) {
						rename('./assets/images/shared/products/500/' . $product->product_image, './assets/images/shared/products/500/' . $product_image_name);
					}
				}

				if (isset($_FILES['image250x250']['tmp_name']) && is_uploaded_file($_FILES['image250x250']['tmp_name'])) {
					$image250x250_error = upload('image250x250', './assets/images/shared/products/250', $product_image_name, 'jpg');
					if ($image250x250_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image250x250_error));
						return;
					}
				} else {
					if ($product->product_image !== $product_image_name) {
						rename('./assets/images/shared/products/250/' . $product->product_image, './assets/images/shared/products/250/' . $product_image_name);
					}
				}

				if (isset($_FILES['image150x150']['tmp_name']) && is_uploaded_file($_FILES['image150x150']['tmp_name'])) {
					$image150x150_error = upload('image150x150', './assets/images/shared/products/150', $product_image_name, 'jpg');
					if ($image150x150_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image150x150_error));
						return;
					}
				} else {
					if ($product->product_image !== $product_image_name) {
						rename('./assets/images/shared/products/150/' . $product->product_image, './assets/images/shared/products/150/' . $product_image_name);
					}
				}

				if (isset($_FILES['image75x75']['tmp_name']) && is_uploaded_file($_FILES['image75x75']['tmp_name'])) {
					$image75x75_error = upload('image75x75', './assets/images/shared/products/75', $product_image_name, 'jpg');
					if ($image75x75_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image75x75_error));
						return;
					}
				} else {
					if ($product->product_image !== $product_image_name) {
						rename('./assets/images/shared/products/75/' . $product->product_image, './assets/images/shared/products/75/' . $product_image_name);
					}
				}


				switch ($this->input->post('productType')) {
					case "1":
						$data = array(
							"name" => $this->input->post('name'),
							"product_image" => $product_image_name,
							"description" => $this->input->post('description'),
							"delivery_details" => $this->input->post('deliveryDetails'),
							"price" => $this->input->post('price'),
							"uom" => $this->input->post('uom'),
							"add_details" => $this->input->post('addDetails'),
							"category" => $this->input->post('category'),
							"num_flavor" => $this->input->post('numFlavor'),
							"product_type_id" => $this->input->pose('productType'),
						);

						$this->admin_model->updateShopProduct($product_id, $data);

						$product_category = array(
							"product_id" => $product_id,
							"category_id" => $this->input->post('category'),
						);

						$this->admin_model->updateShopProductCategory($product_id, $product_category);


						$stores = json_decode($this->input->post('stores'), true);

						foreach ($stores as $store) {
							$data = array(
								'region_id' => $store['region_store_id'],
								'store_id' => $store['store_id'],
								'product_id' => $product_id,
								'status' => 1,
							);
							$region_da_logs[] = $data;
						}

						$this->admin_model->removeShopProductRegionDaLogs($product_id);
						$this->admin_model->insertShopProductRegionDaLogs($region_da_logs);

						$variants = $this->input->post('variants') ? json_decode($this->input->post('variants'), true) : array();

						$product_variants = $this->admin_model->getProductVariants($product_id);

						foreach ($product_variants as $product_variant) {

							$product_variant_options = $this->admin_model->getProductVariantOptions($product_variant->id);
							$this->admin_model->removeProductVariant($product_variant->id);

							foreach ($product_variant_options as $product_variant_option) {

								$product_variant_option_combinations = $this->admin_model->getProductVariantOptionCombinations($product_variant_option->id);
								$this->admin_model->removeProductVariantOption($product_variant_option->id);

								foreach ($product_variant_option_combinations as $product_variant_option_combination) {

									$this->admin_model->removeProductSku($product_variant_option_combination->sku_id);
									$this->admin_model->removeProductVariantOptionCombination($product_variant_option_combination->id);
								}
							}
						}

						foreach ($variants as $variant) {
							$data = array(
								'product_id' => $product_id,
								'name' => $variant['name'],
								'status' => 1,
							);

							$variant_id = $this->admin_model->insertShopProductVariant($data);

							$options = $variant['options'];

							foreach ($options as $option) {
								$product_variant_option = array(
									"product_variant_id" => $variant_id,
									"name" => $option['name'],
									"status" => 1,
								);

								$product_variant_option_id = $this->admin_model->insertShopProductVariantOption($product_variant_option);

								if (isset($option['price']) && isset($option['sku'])) {
									$product_sku = array(
										"product_id" => $product_id,
										"sku" => $option['sku'],
										"price" => $option['price']
									);

									$sku_id = $this->admin_model->insertShopProductSku($product_sku);

									$product_variant_option_combination = array(
										"product_variant_option_id" => $product_variant_option_id,
										"sku_id" => $sku_id,
									);

									$this->admin_model->insertShopProductVariantOptionCombination($product_variant_option_combination);
								}
							}
						}


						$this->admin_model->removeProductWithAddons($product_id);
						$this->admin_model->removeCateringProductAddons($product_id);

						break;
					case "2":
						$data = array(
							"name" => $this->input->post('name'),
							"product_image" => $product_image_name,
							"description" => $this->input->post('description'),
							"delivery_details" => $this->input->post('deliveryDetails'),
							"price" => $this->input->post('price'),
							"product_type_id" => $this->input->post('productType'),
							"uom" => $this->input->post('uom'),
							"add_details" => $this->input->post('addDetails'),
							"num_flavor" => $this->input->post('numFlavor'),
							"category" => ''
						);

						$this->admin_model->updateShopProduct($product_id, $data);

						$products = json_decode($this->input->post('products'), true);

						if ($products) {

							$product_with_addons = array();

							foreach ($products as $value) {
								$data = array(
									'product_id' => $value['id'],
									'addon_product_id' => $product_id,
								);
								$product_with_addons[] = $data;
							}

							$this->admin_model->removeProductWithAddons($product_id);
							$this->admin_model->insertProductWithAddons($product_with_addons);
						}


						$stores = json_decode($this->input->post('stores'), true);

						foreach ($stores as $store) {
							$data = array(
								'region_id' => $store['region_store_id'],
								'store_id' => $store['store_id'],
								'product_id' => $product_id,
								'status' => 1,
							);
							$catering_product_addons[] = $data;
						}


						$this->admin_model->removeCateringProductAddons($product_id);
						$this->admin_model->insertCaterProductAddonsRegionDaLogs($catering_product_addons);

						$this->admin_model->removeShopProductCategory($product_id);
						$this->admin_model->removeShopProductRegionDaLogs($product_id);

						$product_variants = $this->admin_model->getProductVariants($product_id);

						foreach ($product_variants as $product_variant) {

							$product_variant_options = $this->admin_model->getProductVariantOptions($product_variant->id);
							$this->admin_model->removeProductVariant($product_variant->id);

							foreach ($product_variant_options as $product_variant_option) {

								$product_variant_option_combinations = $this->admin_model->getProductVariantOptionCombinations($product_variant_option->id);
								$this->admin_model->removeProductVariantOption($product_variant_option->id);

								foreach ($product_variant_option_combinations as $product_variant_option_combination) {

									$this->admin_model->removeProductSku($product_variant_option_combination->sku_id);
									$this->admin_model->removeProductVariantOptionCombination($product_variant_option_combination->id);
								}
							}
						}

						break;
				}




				$response = array(
					"message" =>  'Successfully edit product'
				);
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function setting_shop_product()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$product_id = $this->input->get('product-id');

				$product = $this->admin_model->getShopProduct($product_id);

				switch ($product->product_type_id) {
					case 1:
						$product_variants = $this->admin_model->getShopProductVariants($product_id);

						foreach ($product_variants as $product_variant) {
							$variants = array(
								"name" => $product_variant->name,
							);

							$variants['options'] = $this->admin_model->getShopProductVariantOptions($product_variant->id);

							$product->variants[] = $variants;
						}

						$product->stores = $this->admin_model->getShopProductStores($product_id);

						break;
					case 2:

						$product->products = $this->admin_model->getProductWithAddons($product_id);
						$product->stores = $this->admin_model->getCateringAddonProductStores($product_id);

						break;
				}

				$response = array(
					"message" =>  'Successfully fetch product',
					"data" => $product,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'POST':
				if (
					is_uploaded_file($_FILES['image500x500']['tmp_name']) &&
					is_uploaded_file($_FILES['image250x250']['tmp_name']) &&
					is_uploaded_file($_FILES['image150x150']['tmp_name']) &&
					is_uploaded_file($_FILES['image75x75']['tmp_name'])
				) {
					$product_image_name = str_replace(' ', '-', strtolower($this->input->post('name'))) . '-' . time() . '.jpg';

					$image500x500_error = upload('image500x500', './assets/images/shared/products/500', $product_image_name, 'jpg');
					if ($image500x500_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image500x500_error));
						return;
					}

					$image250x250_error = upload('image250x250', './assets/images/shared/products/250', $product_image_name, 'jpg');
					if ($image250x250_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image250x250_error));
						return;
					}

					$image150x150_error = upload('image150x150', './assets/images/shared/products/150', $product_image_name, 'jpg');
					if ($image150x150_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image150x150_error));
						return;
					}

					$image75x75_error = upload('image75x75', './assets/images/shared/products/75', $product_image_name, 'jpg');
					if ($image75x75_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image75x75_error));
						return;
					}


					switch ($this->input->post('productType')) {
						case "1": // Main
							$product_hash = substr(md5(uniqid(mt_rand(), true)), 0, 20);

							$data = array(
								"name" => $this->input->post('name'),
								"product_image" => $product_image_name,
								"description" => $this->input->post('description'),
								"delivery_details" => $this->input->post('deliveryDetails'),
								"price" => $this->input->post('price'),
								"category" => $this->input->post('category'),
								"uom" => $this->input->post('uom'),
								"add_details" => $this->input->post('addDetails'),
								"status" => 1,
								"product_type_id" => $this->input->post('productType'),
								"num_flavor" => $this->input->post('numFlavor'),
								'product_hash' => $product_hash,
							);

							$product_id = $this->admin_model->insertShopProduct($data);

							$product_category = array(
								"product_id" => $product_id,
								"category_id" => $this->input->post('category'),
							);

							$this->admin_model->insertShopProductCategory($product_category);

							$stores = json_decode($this->input->post('stores'), true);

							foreach ($stores as $store) {
								$data = array(
									'region_id' => $store['region_store_id'],
									'store_id' => $store['store_id'],
									'product_id' => $product_id,
									'status' => 1,
								);
								$region_da_logs[] = $data;
							}

							$this->admin_model->insertShopProductRegionDaLogs($region_da_logs);

							$variants = $this->input->post('variants') ? json_decode($this->input->post('variants'), true) : array();

							foreach ($variants as $variant) {
								$data = array(
									'product_id' => $product_id,
									'name' => $variant['name'],
									'status' => 1,
								);

								$variant_id = $this->admin_model->insertShopProductVariant($data);

								$options = $variant['options'];
								foreach ($options as $option) {
									$product_variant_option = array(
										"product_variant_id" => $variant_id,
										"name" => $option['name'],
										"status" => 1,
									);

									$product_variant_option_id = $this->admin_model->insertShopProductVariantOption($product_variant_option);

									if (isset($option['price']) && isset($option['sku'])) {
										$product_sku = array(
											"product_id" => $product_id,
											"sku" => $option['sku'],
											"price" => $option['price']
										);

										$sku_id = $this->admin_model->insertShopProductSku($product_sku);

										$product_variant_option_combination = array(
											"product_variant_option_id" => $product_variant_option_id,
											"sku_id" => $sku_id,
										);

										$this->admin_model->insertShopProductVariantOptionCombination($product_variant_option_combination);
									}
								}
							}

							break;
						case "2": // Addons


							$product_hash = substr(md5(uniqid(mt_rand(), true)), 0, 20);

							$data = array(
								"name" => $this->input->post('name'),
								"product_image" => $product_image_name,
								"description" => $this->input->post('description'),
								"delivery_details" => $this->input->post('deliveryDetails'),
								"price" => $this->input->post('price'),
								"uom" => $this->input->post('uom'),
								"add_details" => $this->input->post('addDetails'),
								"status" => 1,
								"product_type_id" => $this->input->post('productType'),
								"num_flavor" => $this->input->post('numFlavor'),
								'product_hash' => $product_hash,
							);

							$product_id = $this->admin_model->insertShopProduct($data);

							$products = json_decode($this->input->post('products'), true);

							foreach ($products as $product) {
								$data = array(
									'product_id' => $product['id'],
									'addon_product_id' => $product_id,
								);
								$product_with_addons[] = $data;
							}

							$this->admin_model->insertProductWithAddons($product_with_addons);

							$stores = json_decode($this->input->post('stores'), true);

							foreach ($stores as $store) {
								$data = array(
									'region_id' => $store['region_store_id'],
									'store_id' => $store['store_id'],
									'product_id' => $product_id,
									'status' => 1,
								);
								$catering_product_addons[] = $data;
							}

							$this->admin_model->insertCaterProductAddonsRegionDaLogs($catering_product_addons);

							break;
					}
				}

				$response = array(
					"message" =>  'Successfully add product'
				);
				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);

				$this->admin_model->updateShopProductStatus($put['product_id'], $put['status']);

				$response = array(
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function setting_shop_products()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$status = $this->input->get('status') ?? null;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$shop_products_count = $this->admin_model->getShopProductsCount($status, $search);
				$shop_products = $this->admin_model->getShopProducts($page_no, $per_page, $status, $order_by, $order, $search);

				$pagination = array(
					"total_rows" => $shop_products_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch survey verification',
					"data" => array(
						"pagination" => $pagination,
						"shop_products" => $shop_products
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function survey_verification($survey_id)
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$survey = $this->admin_model->getSurvey($survey_id);
				$response = array(
					"data" => $survey,
					"message" => "Succesfully fetch survey verification"
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function survey_verifications()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$status = $this->input->get('status') ?? null;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$surveys_count = $this->admin_model->getSurveysCount($status, $search);
				$surveys = $this->admin_model->getSurveys($page_no, $per_page, $status, $order_by, $order, $search);

				$pagination = array(
					"total_rows" => $surveys_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch survey verification',
					"data" => array(
						"pagination" => $pagination,
						"surveys" => $surveys
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function survey_verification_change_status()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);

				$survey_verification_id = $this->input->post('surveyverificationId');
				$status = $this->input->post('status');

				$this->admin_model->changeStatusSurveyVerification($survey_verification_id, $status);

				$response = array(
					"message" => 'Successfully update survey verification status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function report_transaction($startDate, $endDate)
	{

		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$start = date("Y-m-d", strtotime($startDate)) . " 00:00:00";
				$end = date("Y-m-d", strtotime($endDate)) . " 23:59:59";
				$data = $this->report_model->getReportTransaction($start, $end);
				header("Content-Type: application/vnd.ms-excel");
				header("Content-disposition: attachment; filename=transaction_" . $startDate . "_" . $endDate . "_" . date('Y-m-d H:i:s') . ".xls");

				$payment_options = array(
					"' - '",
					"BPI",
					"BDO",
					"CASH",
					"GCASH",
					"PAYMAYA",
					"ROBINSONS-BANK",
					"CHINABANK",
				);

				$order_status = array(
					"Incomplete Transaction",
					"New",
					"Paid",
					"Confirmed",
					"Declined",
					"Cancelled",
					"Completed",
					"Rejected",
					"Preparing",
					"For Dispatch",
					"Error Transaction",
				);


				$mode_of_handling = array(
					"",
					"Pickup",
					"Delivery",
				);


				$flag = false;
				foreach ($data as $row) {
					if (!$flag) {
						echo implode("\t", array_keys((array)$row)) . "\r\n";
						$flag = true;
					}

					$line = "";

					foreach ((array)$row as $key => $val) {

						switch ($key) {
							case 'PAYMENT OPTION':
								$line .= $payment_options[$val] . "\t";
								break;
							case 'MODE OF HANDLING':
								$line .= $mode_of_handling[$val] . "\t";
								break;
							case 'STATUS':
								$line .= $order_status[$val] . "\t";
								break;
							case 'DISTANCE':
								$line .= $val . " km\t";
								break;
								// case 'REMARKS':
								//   $line .= $this->report_remarks($val) . "\t";
								//   break;
							default:
								$line .= $val . "\t";
								break;
						}
					}

					echo $line . "\r\n";
				}

				break;
		}
	}

	public function report_pmix($startDate, $endDate)
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$start = date("Y-m-d", strtotime($startDate)) . " 00:00:00";
				$end = date("Y-m-d", strtotime($endDate)) . " 23:59:59";
				$data = $this->report_model->getReportPmix($start, $end);
				header("Content-Type: application/vnd.ms-excel");

				header("Content-disposition: attachment; filename=PMIX_" . $startDate . "_" . $endDate . "_" . date('Y-m-d H:i:s') . ".xls");

				$flag = false;
				foreach ($data as $row) {
					if (!$flag) {
						echo implode("\t", array_keys((array)$row)) . "\r\n";
						$flag = true;
					}

					$line = "";
					foreach ((array)$row as $key => $val) {
						// if ($key == 'REMARKS') 
						//   $line .= $this->report_remarks($val) . "\t";
						// else 
						$line .= $val . "\t";
					}

					echo $line . "\r\n";
				}

				break;
		}
	}

	public function notification_seen($notification_id)
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$date_now = date('Y-m-d H:i:s');
				$this->notification_model->seenNotification($notification_id, $date_now);

				$response = array(
					"message" => "Succesfully seen notification"
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function notifications()
	{

		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$user_id = $this->session->admin['user_id'];

				$response = array(
					"data" => array(
						"all" => array(
							'notifications' => $this->notification_model->getNotifications($user_id, null, false, 'admin'),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, null, true, 'admin'),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, null, 'admin'),
						),
						"snackshop_order" => array(
							'notifications' => $this->notification_model->getNotifications($user_id, 1, false, 'admin'),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, 1, true, 'admin'),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, 1, 'admin'),
						),
						"catering_booking" => array(
							'notifications' => $this->notification_model->getNotifications($user_id, 2, false, 'admin'),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, 2, true, 'admin'),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, 2, 'admin'),
						),
						"popclub_redeem" => array(
							'notifications' => $this->notification_model->getNotifications($user_id, 3, false, 'admin'),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, 3, true, 'admin'),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, 3, 'admin'),
						),
					),
					"message" => "Succesfully fetch notification"
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function catering_transaction_logs($reference_id)
	{

		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$transaction_logs = $this->logs_model->getCateringTransactionLogs($reference_id);

				$response = array(
					"message" => 'Successfully fetch audit',
					"data" => $transaction_logs,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function snackshop_transaction_logs($reference_id)
	{

		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$transaction_logs = $this->logs_model->getTransactionLogs($reference_id);

				$response = array(
					"message" => 'Successfully fetch audit',
					"data" => $transaction_logs,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function store_operating_hours()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);
				$store_id = $put['store_id'];
				$available_start_time =  $put['available_start_time'];
				$available_end_time =  $put['available_end_time'];

				$this->admin_model->updateSettingStoreOperatingHours(
					$store_id,
					$available_start_time,
					$available_end_time,
				);

				$response = array(
					"message" => 'Successfully update operating hours',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function store()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$store_id = $this->input->get('store_id');

				$store = $this->admin_model->getStore($store_id);

				$response = array(
					"data" => $store,
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);

				$this->admin_model->updateSettingStore($put['store_id'], $put['name_of_field_status'], $put['status']);

				$response = array(
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function setting_stores()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'store_id';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$store_id_array = array();
				$store_id = $this->user_model->get_store_group_order($this->ion_auth->user()->row()->id);
				foreach ($store_id as $value) $store_id_array[] = $value->store_id;

				if (empty($store_id_array) && !$this->ion_auth->in_group(1) && !$this->ion_auth->in_group(10)) {
					$stores_count = 0;
					$stores = array();
				} else {
					$stores_count = $this->admin_model->getSettingStoresCount($search, $store_id_array);
					$stores = $this->admin_model->getSettingStores($page_no, $per_page, $order_by, $order, $search, $store_id_array);
				}

				$pagination = array(
					"total_rows" => $stores_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch stores',
					"data" => array(
						"pagination" => $pagination,
						"stores" => $stores
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);

				$this->admin_model->updateSettingStore($put['id'], $put['name_of_field_status'], $put['status']);

				$response = array(
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}



	public function deal_availability()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$store_id = $this->input->get('store_id');
				$category_id = $this->input->get('category_id');
				$status = $this->input->get('status') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'id';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$deals_count = $this->admin_model->getStoreDealsCount($store_id, $category_id, $status, $search);
				$deals = $this->admin_model->getStoreDeals($page_no, $per_page, $store_id, $category_id, $status, $order_by, $order, $search);

				$pagination = array(
					"total_rows" => $deals_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch deals',
					"data" => array(
						"pagination" => $pagination,
						"deals" => $deals
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;

			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);

				$this->admin_model->updateStoreDeal($put['id'], $put['status']);

				$response = array(
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function product_availability()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$store_id = $this->input->get('store_id');
				$category_id = $this->input->get('category_id');
				$status = $this->input->get('status') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'id';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$products_count = $this->admin_model->getStoreProductCount($store_id, $category_id, $status, $search);
				$products = $this->admin_model->getStoreProducts($page_no, $per_page, $store_id, $category_id, $status, $order_by, $order, $search);

				$pagination = array(
					"total_rows" => $products_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch products',
					"data" => array(
						"pagination" => $pagination,
						"products" => $products
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;

			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);

				$this->admin_model->updateStoreProduct($put['id'], $put['status']);

				$response = array(
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}
	//TODO KARL
	public function getCaters_package()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$search = $this->input->get('search');
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'id';

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$data = $this->admin_model->getAllCatersPackage($page_no, $per_page, $order_by, $order, $search);

				$caters_packages_count = $data['TotalPackage'];

				$pagination = array(
					"total_rows" => $caters_packages_count,
					"per_page" => $per_page,
				);

				unset($data['TotalPackage']);

				$response = array(
					'data' => $data['results'],
					'message' => "sucess",
					'pagination' => $pagination,
					'DynamicPrices' => $data['DynamicPrices'],
				);
				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function createCaters_package()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':

				$_POST['dateadded'] = date('d-m-y h:i:s');

				if ($_POST['package_type'] == 0) $_POST['add_remarks'] = 1;

				//! below here will be change
				$_POST['to_gc_value'] = null;
				if ($_POST['note'] == '') $_POST['note'] = null;
				if ($_POST['tags'] == '') $_POST['tags'] = null;
				if ($_POST['product_code'] == '') $_POST['product_code'] = '0000';
				if ($_POST['status'] == '') $_POST['status'] = 1;
				if ($_POST['report_status'] == '') $_POST['report_status'] = 1;
				if ($_POST['free_threshold'] == '') $_POST['free_threshold'] = 0;
				$_POST['product_hash'] = substr(md5(uniqid(mt_rand(), true)), 0, 20);


				$variantData = array();
				$tempVariantVar = json_decode($_POST['variants']);

				for ($i = 0; $i < count($tempVariantVar); $i++) {
					array_push($variantData, (array) $tempVariantVar[$i]);
				}
				// $this->output->set_status_header('401');
				// echo json_encode(array("message" => $tempVariantVar));
				// return;

				//? If there is an value in array format
				$dynamicPriceData = array();
				$tempVar = json_decode($_POST['dynamic_price']);
				for ($i = 0; $i < count($tempVar); $i++) {
					array_push($dynamicPriceData, (array) $tempVar[$i]);
				}



				unset($_POST['dynamic_price']);
				unset($_POST['variants']);

				$insert = $this->admin_model->createNewCatersPackage($_POST, $dynamicPriceData);

				foreach ($variantData as $variant) {

					$variantId = $this->admin_model->addNewVariantCatersPackage($insert, $variant);
					$option = (array)$variant['variantOption'];
					foreach ($option as $value) {
						$tempVal = (array) $value;
						$data = array(
							'id' => "",
							'product_variant_id' => $variantId,
							'name' => $tempVal['name'],
							'status' => 1,
						);

						$response = array(
							'option' => $data,
						);
						$this->admin_model->addNewVariantOptionCatersPackage($data);
					}
				}


				if ($insert >= 0) {
					$filepath75 = './assets/images/shared/products/75';
					$filepath150 = './assets/images/shared/products/150';
					$filepath250 = './assets/images/shared/products/250';
					$filepath500 = './assets/images/shared/products/500';
					//! Unique Folder

					// if (!is_dir('./assets/images/shared/products/' . $insert)) {
					// 	mkdir('./assets/images/shared/products/' . $insert);
					// }
					// $filepath75 = './assets/images/shared/products/CatersPackage-'.$insert;
					// $filepath150 = './assets/images/shared/products/CatersPackage-'.$insert;
					// $filepath250 = './assets/images/shared/products/CatersPackage-'.$insert;
					// $filepath500 = './assets/images/shared/products/CatersPackage-'.$insert;
					//! Unique Folder

					$image75x75_error = upload('product_image75x75', $filepath75, $_POST['product_image'], 'jpg');
					if ($image75x75_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image75x75_error));
						return;
					}

					$image150x150_error = upload('product_image150x150', $filepath150, $_POST['product_image'], 'jpg');
					if ($image150x150_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image150x150_error));
						return;
					}

					$image250x250_error = upload('product_image250x250', $filepath250, $_POST['product_image'], 'jpg');
					if ($image250x250_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image250x250_error));
						return;
					}

					$image500x500_error = upload('product_image500x500', $filepath500, $_POST['product_image'], 'jpg');
					if ($image500x500_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image500x500_error));
						return;
					}
				} else {
					$response = array(
						'message' => $insert
					);
					header('content-type: application/json');
					echo json_encode(array("message" => $response));
				}

				return;
		}
	}

	public function updateCaters_package()
	{

		switch ($this->input->server('REQUEST_METHOD')) {

			case 'POST':

				$data = $this->admin_model->getCaterPackage($_POST["id"]);

				if ($data['product_image'] != $_POST["product_image"]) {
					$filepath75 = './assets/images/shared/products/75';
					$filepath150 = './assets/images/shared/products/150';
					$filepath250 = './assets/images/shared/products/250';
					$filepath500 = './assets/images/shared/products/500';
					$image75x75_error = upload('product_image75x75', $filepath75, $_POST['product_image'], 'jpg');
					if ($image75x75_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image75x75_error));
						return;
					}

					$image150x150_error = upload('product_image150x150', $filepath150, $_POST['product_image'], 'jpg');
					if ($image150x150_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image150x150_error));
						return;
					}

					$image250x250_error = upload('product_image250x250', $filepath250, $_POST['product_image'], 'jpg');
					if ($image250x250_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image250x250_error));
						return;
					}

					$image500x500_error = upload('product_image500x500', $filepath500, $_POST['product_image'], 'jpg');
					if ($image500x500_error) {
						$this->output->set_status_header('401');
						echo json_encode(array("message" => $image500x500_error));
						return;
					}
				}
				unset($_POST['product_image75x75']);
				unset($_POST['product_image150x150']);
				unset($_POST['product_image250x250']);
				unset($_POST['product_image500x500']);



				//? If there is an value in array format
				$dynamicPriceData = array();
				$tempVar = json_decode($_POST['dynamic_price']);
				for ($i = 0; $i < count($tempVar); $i++) {
					array_push($dynamicPriceData, (array) $tempVar[$i]);
				}
				unset($_POST['dynamic_price']);

				$this->admin_model->updateCatersPackage($_POST, $dynamicPriceData);

				$response = array(
					'id' => $_POST["id"],
					'ids' => $dynamicPriceData
				);
				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}
	public function deleteCaters_package($id)
	{

		switch ($this->input->server('REQUEST_METHOD')) {

			case 'DELETE':
				$this->admin_model->removeCatersPackage($id);
				$response = array(
					"id" =>  $id
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}
	public function caters_package_availability()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$store_id = $this->input->get('store_id');
				$category_id = $this->input->get('category_id');
				$status = $this->input->get('status') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'id';
				$search = $this->input->get('search');


				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$caters_packages_count = $this->admin_model->getStoreCatersPackageCount($store_id, $category_id, $status, $search);
				$caters_packages = $this->admin_model->getStoreCatersPackages($page_no, $per_page, $store_id, $category_id, $status, $order_by, $order, $search);

				$pagination = array(
					"total_rows" => $caters_packages_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch packages',
					"data" => array(
						"pagination" => $pagination,
						"caters_packages" => $caters_packages
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;

			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);

				$this->admin_model->updateStoreCatersPackage($put['id'], $put['status']);

				$response = array(
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function caters_product_addon_availability()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$store_id = $this->input->get('store_id');
				$status = $this->input->get('status') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'id';
				$search = $this->input->get('search');


				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$caters_product_addons_count = $this->admin_model->getStoreCatersProductAddonsCount($store_id,  $status, $search);
				$caters_product_addons = $this->admin_model->getStoreCatersProductAddons($page_no, $per_page, $store_id, $status, $order_by, $order, $search);

				$pagination = array(
					"total_rows" => $caters_product_addons_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch catering product addons',
					"data" => array(
						"pagination" => $pagination,
						"caters_product_addons" => $caters_product_addons
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;

			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);

				$this->admin_model->updateStoreCatersProductAddon($put['id'], $put['status']);

				$response = array(
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function caters_package_addon_availability()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$store_id = $this->input->get('store_id');
				$status = $this->input->get('status') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'id';
				$search = $this->input->get('search');


				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$caters_package_addons_count = $this->admin_model->getStoreCatersPackageAddonsCount($store_id,  $status, $search);
				$caters_package_addons = $this->admin_model->getStoreCatersPackageAddons($page_no, $per_page, $store_id, $status, $order_by, $order, $search);

				$pagination = array(
					"total_rows" => $caters_package_addons_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch catering package addons',
					"data" => array(
						"pagination" => $pagination,
						"caters_package_addons" => $caters_package_addons
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;

			case 'PUT':
				$put = json_decode(file_get_contents("php://input"), true);

				$this->admin_model->updateStoreCatersPackageAddon($put['id'], $put['status']);

				$response = array(
					"message" => 'Successfully update status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function admin_catering_privilege()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);

				$fb_user_id = $this->input->post('fb_user_id');
				$mobile_user_id = $this->input->post('mobile_user_id');

				$password = $this->input->post('password');

				$transaction_id = $this->input->post('transactionId');

				$from_store_id = $this->input->post('fromStoreId');
				$to_store_id = $this->input->post('toStoreId');

				$from_status_id = $this->input->post('fromStatusId');
				$to_status_id = $this->input->post('toStatusId');

				$request = isset($to_store_id) ? 'store_transfer' : (isset($to_status_id) ? 'change_status' : null);

				$user_id = $this->session->admin['user_id'];

				$from_store = $this->store_model->get_store_info($from_store_id);
				$to_store = $this->store_model->get_store_info($to_store_id);

				if ($from_status_id == 1) $from_status = "Waiting for booking confirmation";
				elseif ($from_status_id == 2) $from_status = "Booking Confirmed";
				elseif ($from_status_id == 3) $from_status = "Contract Uploaded";
				elseif ($from_status_id == 4) $from_status = "Contract Verified";
				elseif ($from_status_id == 5) $from_status = "Initial Payment Uploaded";
				elseif ($from_status_id == 6) $from_status = "Initial Payment Verified";
				elseif ($from_status_id == 7) $from_status = "Final Payment Uploaded";
				elseif ($from_status_id == 8) $from_status = "Final payment verified";
				elseif ($from_status_id == 20) $from_status = "Booking denied";
				elseif ($from_status_id == 21) $from_status = "Contract denied";
				elseif ($from_status_id == 22) $from_status = "Initial Payment denied";
				elseif ($from_status_id == 23) $from_status = "Final Payment denied";

				if ($to_status_id == 1) $to_status = "Waiting for booking confirmation";
				elseif ($to_status_id == 2) $to_status = "Booking Confirmed";
				elseif ($to_status_id == 3) $to_status = "Contract Uploaded";
				elseif ($to_status_id == 4) $to_status = "Contract Verified";
				elseif ($to_status_id == 5) $to_status = "Initial Payment Uploaded";
				elseif ($to_status_id == 6) $to_status = "Initial Payment Verified";
				elseif ($to_status_id == 7) $to_status = "Final Payment Uploaded";
				elseif ($to_status_id == 8) $to_status = "Final payment verified";
				elseif ($to_status_id == 20) $to_status = "Booking denied";
				elseif ($to_status_id == 21) $to_status = "Contract denied";
				elseif ($to_status_id == 22) $to_status = "Initial Payment denied";
				elseif ($to_status_id == 23) $to_status = "Final Payment denied";



				$fetch_data = $this->admin_model->updateStoreOrStatusCateringTransaction(
					$request,
					$password,
					$transaction_id,
					$to_store_id,
					$to_status_id
				);

				if ($fetch_data == 1) {

					if ($request == "store_transfer") {
						$this->logs_model->insertCateringTransactionLogs($user_id, 1, $transaction_id, 'Transfer booking from ' . $from_store->name . ' to ' . $to_store->name);

						$real_time_notification = array(
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"message" => 'Your booking has been transfered from ' . $from_store->name . ' to ' . $to_store->name,
						);

						notify('user-catering', 'catering-booking-changed', $real_time_notification);
					} elseif ($request == "change_status") {
						$this->logs_model->insertCateringTransactionLogs($user_id, 1, $transaction_id, 'Change booking status from ' . $from_status . ' to ' . $to_status);

						$real_time_notification = array(
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"message" => 'Your order status changed ' . $from_status . ' to ' . $to_status,
						);

						notify('user-catering', 'catering-booking-changed', $real_time_notification);
					}

					header('content-type: application/json');
					echo json_encode(array("message" => "Update success!"));
				} else {
					if ($request == "store_transfer")
						$this->logs_model->insertCateringTransactionLogs($user_id, 3, $transaction_id, 'Transferring booking Failed');
					elseif ($request == "change_status")
						$this->logs_model->insertCateringTransactionLogs($user_id, 3, $transaction_id, 'Changing booking status Failed');

					$this->output->set_status_header('401');
					echo json_encode(array("message" => $fetch_data));
				}

				return;
		}
	}

	public function admin_privilege()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);

				$fb_user_id = $this->input->post('fb_user_id');
				$mobile_user_id = $this->input->post('mobile_user_id');

				$password = $this->input->post('password');

				$transaction_id = $this->input->post('transactionId');

				$from_store_id = $this->input->post('fromStoreId');
				$to_store_id = $this->input->post('toStoreId');

				$from_status_id = $this->input->post('fromStatusId');
				$to_status_id = $this->input->post('toStatusId');

				$request = isset($to_store_id) ? 'store_transfer' : (isset($to_status_id) ? 'change_status' : null);

				$user_id = $this->session->admin['user_id'];

				$from_store = $this->store_model->get_store_info($from_store_id);
				$to_store = $this->store_model->get_store_info($to_store_id);

				if ($from_status_id == 1) $from_status = "New";
				elseif ($from_status_id == 2) $from_status = "Paid";
				elseif ($from_status_id == 3) $from_status = "Confirmed";
				elseif ($from_status_id == 4) $from_status = "Declined";
				elseif ($from_status_id == 5) $from_status = "Cancelled";
				elseif ($from_status_id == 6) $from_status = "Completed";
				elseif ($from_status_id == 7) $from_status = "Rejected";
				elseif ($from_status_id == 8) $from_status = "Preparing";
				elseif ($from_status_id == 9) $from_status = "For Dispatch";

				if ($to_status_id == 1) $to_status = "New";
				elseif ($to_status_id == 2) $to_status = "Paid";
				elseif ($to_status_id == 3) $to_status = "Confirmed";
				elseif ($to_status_id == 4) $to_status = "Declined";
				elseif ($to_status_id == 5) $to_status = "Cancelled";
				elseif ($to_status_id == 6) $to_status = "Completed";
				elseif ($to_status_id == 7) $to_status = "Rejected";
				elseif ($to_status_id == 8) $to_status = "Preparing";
				elseif ($to_status_id == 9) $to_status = "For Dispatch";



				$fetch_data = $this->admin_model->updateStoreOrStatusSnackshopTransaction(
					$request,
					$password,
					$transaction_id,
					$to_store_id,
					$to_status_id
				);

				if ($fetch_data == 1) {

					if ($request == "store_transfer") {
						$this->logs_model->insertTransactionLogs($user_id, 1, $transaction_id, 'Transfer order from ' . $from_store->name . ' to ' . $to_store->name);

						$real_time_notification = array(
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"message" => 'Your order has been transfered from ' . $from_store->name . ' to ' . $to_store->name,
						);

						notify('user-snackshop', 'snackshop-order-changed', $real_time_notification);
					} elseif ($request == "change_status") {
						$this->logs_model->insertTransactionLogs($user_id, 1, $transaction_id, 'Change order status from ' . $from_status . ' to ' . $to_status);

						$real_time_notification = array(
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"message" => 'Your order status changed ' . $from_status . ' to ' . $to_status,
						);

						notify('user-snackshop', 'snackshop-order-changed', $real_time_notification);
					}

					header('content-type: application/json');
					echo json_encode(array("message" => "Update success!"));
				} else {
					if ($request == "store_transfer")
						$this->logs_model->insertTransactionLogs($user_id, 3, $transaction_id, 'Transferring order Failed');
					elseif ($request == "change_status")
						$this->logs_model->insertTransactionLogs($user_id, 3, $transaction_id, 'Changing order status Failed');

					$this->output->set_status_header('401');
					echo json_encode(array("message" => $fetch_data));
				}

				return;
		}
	}

	public function catering_update_status()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);
				$trans_id = (int) $this->input->post('transactionId');
				$status = $this->input->post('status');
				$fetch_data = $this->admin_model->update_catering_status($trans_id, $status);
				$user_id = $this->session->admin['user_id'];
				$fb_user_id = $this->input->post('fbUserId');
				$mobile_user_id = $this->input->post('mobileUserId');

				$update_on_click = $this->admin_model->update_catering_on_click($trans_id, $status);
				if ($status == 2) $generate_invoice = $this->admin_model->generate_catering_invoice_num($trans_id);

				if ($status == 2) $tagname = "Confirm";
				elseif ($status == 4) $tagname = "Contract Verified";
				elseif ($status == 6) $tagname = "Initial Payment Verified";
				elseif ($status == 8) $tagname = "Final Payment Verified";
				elseif ($status == 9) $tagname = "Catering booking completed";

				if ($fetch_data == 1) {
					$this->logs_model->insertCateringTransactionLogs($user_id, 1, $trans_id, '' . $tagname . ' ' . 'Booking Success');

					$real_time_notification = array(
						"fb_user_id" => (int) $fb_user_id,
						"mobile_user_id" => (int) $mobile_user_id,
						"message" => $tagname,
					);

					notify('user-catering', 'catering-booking-updated', $real_time_notification);
					header('content-type: application/json');
					echo json_encode(array("message" => 'Successfully update status!'));
				} else {
					$this->logs_model->insertTransactionLogs($user_id, 3, $trans_id, '' . $tagname . ' ' . 'Booking Success');
					$this->output->set_status_header('401');
					echo json_encode(array("message" => 'Failed update status!'));
				}

				return;
		}
	}

	public function shop_update_status()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);
				$trans_id = (int) $this->input->post('transactionId');
				$user_id = $this->session->admin['user_id'];
				$status = $this->input->post('status');
				$fetch_data = $this->admin_model->update_shop_status($trans_id, $status);
				$fb_user_id = $this->input->post('fbUserId');
				$mobile_user_id = $this->input->post('mobileUserId');

				$update_on_click = $this->admin_model->update_shop_on_click($trans_id, $_POST['status']);
				if ($status == 3) $generate_invoice = $this->admin_model->generate_shop_invoice_num($trans_id);

				if ($status == 3) $tagname = "Confirm";
				elseif ($status == 4) $tagname = "Declined";
				elseif ($status == 6) $tagname = "Complete";
				elseif ($status == 7) $tagname = "Reject";
				elseif ($status == 8) $tagname = "Prepare";
				elseif ($status == 9) $tagname = "Dispatched";

				if ($fetch_data == 1) {
					$this->logs_model->insertTransactionLogs($user_id, 1, $trans_id, '' . $tagname . ' ' . 'Order Success');
					$this->status_notification($trans_id, 9, $user_id);

					$real_time_notification = array(
						"fb_user_id" => (int) $fb_user_id,
						"mobile_user_id" => (int) $mobile_user_id,
						"status" => $status,
					);

					notify('user-snackshop', 'snackshop-order-update', $real_time_notification);


					header('content-type: application/json');
					echo json_encode(array("message" => 'Successfully update status!'));
				} else {
					$this->logs_model->insertTransactionLogs($user_id, 3, $trans_id, '' . $tagname . ' ' . 'Order Success');
					$this->output->set_status_header('401');
					echo json_encode(array("message" => 'Failed update status!'));
				}
				return;
		}
	}

	public function reference_num()
	{

		$_POST =  json_decode(file_get_contents("php://input"), true);

		$user_id = $this->session->admin['user_id'];
		$trans_id = $this->input->post('transactionId');
		$ref_num = $this->input->post('referenceNumber');
		$fetch_data = $this->admin_model->validate_ref_num($trans_id, $ref_num);

		if ($fetch_data == 1) {

			$this->logs_model->insertTransactionLogs($user_id, 1, $trans_id, 'Payment Validation Success');
			header('content-type: application/json');
			echo json_encode(array("message" => 'Validation successful'));
		} else {

			$this->logs_model->insertTransactionLogs($user_id, 1, $trans_id, 'Payment Validation Failed');
			$this->output->set_status_header('401');
			echo json_encode(array("message" => 'Invalid Reference number'));
		}
	}

	public function deal_categories()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$deal_categories = $this->admin_model->get_deal_categories();

				$response = array(
					"message" => 'Successfully fetch user stores',
					"data" => $deal_categories,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function caters_package_categories()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$package_categories = $this->admin_model->get_caters_package_categories();

				$response = array(
					"message" => 'Successfully fetch user stores',
					"data" => $package_categories,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function product_categories()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$product_categories = $this->admin_model->getProductCategories();

				$response = array(
					"message" => 'Successfully fetch user stores',
					"data" => $product_categories,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function stores()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$user_id = $this->input->get('user_id');

				if ($user_id) {
					$stores =  $this->user_model->get_store_group_order_set($user_id);
				} else {
					$stores = $this->store_model->getAllStores();
				}

				$response = array(
					"message" => 'Successfully fetch user stores',
					"data" => $stores,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;

			case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);
				$stores = $this->input->post('stores');

				$this->user_model->add_store_group($this->input->post('userId'), $stores);

				$response = array(
					"message" => 'Successfully update user stores',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function setting_groups()
	{

		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$groups =  $this->admin_model->getGroups();

				$response = array(
					"message" => 'Successfully fetch snackshop user',
					"data" => $groups,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function setting_user($user_id)
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$user =  $this->admin_model->getUser($user_id);
				$user->groups = $this->admin_model->getUserGroups($user->id);

				$response = array(
					"message" => 'Successfully fetch snackshop user',
					"data" => $user,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function setting_users()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$order = $this->input->get('order') ?? 'asc';
				$order_by = $this->input->get('order_by') ?? 'id';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$users_count =  $this->admin_model->getUsersCount($search);
				$users = $this->admin_model->getUsers($page_no, $per_page, $order_by, $order, $search);

				foreach ($users as $user) {
					$user->groups = $this->admin_model->getUserGroups($user->id);
				}

				$pagination = array(
					"total_rows" => $users_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch snackshop orders',
					"data" => array(
						"pagination" => $pagination,
						"users" => $users
					),
				);
				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function shop_order($trackingNo)
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$order = $this->admin_model->getSnackshopOrder($trackingNo);
				$order->items = $this->admin_model->getSnackshopOrderItems($order->id);

				$response = array(
					"message" => 'Successfully fetch snackshop order',
					"data" => $order,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function shop()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$status = $this->input->get('status') ?? null;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$store_id_array = array();
				$store_id = $this->user_model->get_store_group_order($this->ion_auth->user()->row()->id);
				foreach ($store_id as $value) $store_id_array[] = $value->store_id;

				if (empty($store_id_array) && !$this->ion_auth->in_group(1) && !$this->ion_auth->in_group(10)) {
					$orders_count = 0;
					$orders = array();
				} else {
					$orders_count = $this->admin_model->getSnackshopOrdersCount($status, $search, $store_id_array);
					$orders = $this->admin_model->getSnackshopOrders($page_no, $per_page, $status, $order_by, $order, $search, $store_id_array);
				}

				$pagination = array(
					"total_rows" => $orders_count,
					"per_page" => $per_page,
					"test" => $store_id_array,
				);

				$response = array(
					"message" => 'Successfully fetch snackshop orders',
					"data" => array(
						"pagination" => $pagination,
						"orders" => $orders
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function catering_order($trackingNo)
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$order = $this->admin_model->getCateringBooking($trackingNo);
				$order->items = $this->admin_model->getCateringBookingItems($order->id);


				if ($order->logon_type == 'facebook') {
					$account_info = $this->admin_model->get_fname_lname_email($order->fb_user_id);
					$order->account_name = $account_info->first_name . " " . $account_info->last_name;
					$order->account_email = $account_info->email;
				} else {
					$account_info = $this->admin_model->get_fname_lname_email_mobile($order->mobile_user_id);
					$order->account_name = $account_info->first_name . " " . $account_info->last_name;
					$order->account_email = $account_info->email;
				}

				$response = array(
					"message" => 'Successfully fetch snackshop order',
					"data" => $order,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function catering()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$status = $this->input->get('status') ?? null;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$store_id_array = array();
				$store_id = $this->user_model->get_store_group_order($this->ion_auth->user()->row()->id);
				foreach ($store_id as $value) $store_id_array[] = $value->store_id;

				if (empty($store_id_array) && !$this->ion_auth->in_group(1) && !$this->ion_auth->in_group(10)) {
					$bookings_count = 0;
					$bookings = array();
				} else {
					$bookings_count = $this->admin_model->getCateringBookingsCount($status, $search, $store_id_array);
					$bookings = $this->admin_model->getCateringBookings($page_no, $per_page, $status, $order_by, $order, $search,  $store_id_array);
				}

				$pagination = array(
					"total_rows" => $bookings_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch snackshop bookings',
					"data" => array(
						"pagination" => $pagination,
						"bookings" => $bookings
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function popclub_decline_redeem()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);

				$redeem_id = $this->input->post('redeemId');
				$fb_user_id = $this->input->post('fbUserId');
				$mobile_user_id = $this->input->post('mobileUserId');

				$this->admin_model->declineRedeem($redeem_id);

				$real_time_notification = array(
					"fb_user_id" => (int) $fb_user_id,
					"mobile_user_id" => (int) $mobile_user_id,
					"message" => "Your redeem declined, Thank you."
				);

				notify('user-popclub', 'popclub-redeem-declined', $real_time_notification);

				$response = array(
					"message" => 'Successfully declined the redeem',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function user_discount_change_status()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);

				$discount_users_id = $this->input->post('discountUserId');
				$status = $this->input->post('status');

				$this->admin_model->changeStatusUserDiscount($discount_users_id, $status);

				$response = array(
					"message" => 'Successfully update user discount status',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function popclub_complete_redeem()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);

				$redeem_id = $this->input->post('redeemId');
				$fb_user_id = $this->input->post('fbUserId');
				$mobile_user_id = $this->input->post('mobileUserId');

				$this->admin_model->completeRedeem($redeem_id);

				$real_time_notification = array(
					"fb_user_id" => (int) $fb_user_id,
					"mobile_user_id" => (int) $mobile_user_id,
					"message" => "You completed your redeem, Thank you."
				);

				notify('user-popclub', 'popclub-redeem-completed', $real_time_notification);


				$response = array(
					"message" => 'Successfully completed the redeem',
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function popclub_redeem($redeemCode)
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$redeem = $this->admin_model->getPopclubRedeem($redeemCode);
				$redeem->items = $this->admin_model->getPopclubRedeemItems($redeem->id);

				$response = array(
					"message" => 'Successfully fetch popclub redeem',
					"data" => $redeem,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function popclub()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$status = $this->input->get('status') ?? null;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$store_id_array = array();
				$store_id = $this->user_model->get_store_group_order($this->ion_auth->user()->row()->id);
				foreach ($store_id as $value) $store_id_array[] = $value->store_id;

				if (empty($store_id_array) && !$this->ion_auth->in_group(1) && !$this->ion_auth->in_group(10)) {
					$redeems_count = 0;
					$redeems = array();
				} else {
					$redeems_count = $this->admin_model->getPopclubRedeemsCount($status, $search, $store_id_array);
					$redeems = $this->admin_model->getPopclubRedeems($page_no, $per_page, $status, $order_by, $order, $search, $store_id_array);
				}

				$pagination = array(
					"total_rows" => $redeems_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch popclub redeems',
					"data" => array(
						"pagination" => $pagination,
						"redeems" => $redeems
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function discount($discount_user_id)
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$discount = $this->admin_model->getDiscount($discount_user_id);

				$response = array(
					"message" => 'Successfully fetch user discount request',
					"data" => $discount,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function discounts()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$status = $this->input->get('status') ?? null;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');

				if ($page_no != 0) {
					$page_no = ($page_no - 1) * $per_page;
				}

				$discounts_count = $this->admin_model->getDiscountsCount($status, $search);
				$discounts = $this->admin_model->getDiscounts($page_no, $per_page, $status, $order_by, $order, $search);

				$pagination = array(
					"total_rows" => $discounts_count,
					"per_page" => $per_page,
				);

				$response = array(
					"message" => 'Successfully fetch user discounts',
					"data" => array(
						"pagination" => $pagination,
						"discounts" => $discounts
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function session()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'GET':

				$data = array(
					"admin" => array(
						"identity" => $this->session->admin['identity'],
						"email" => $this->session->admin['email'],
						"user_id" => $this->session->admin['user_id'],
						"old_last_login" => $this->session->admin['old_last_login'],
						"last_check" => $this->session->admin['last_check'],
						"is_admin" => $this->ion_auth->in_group(1),
						"is_csr_admin" => $this->ion_auth->in_group(10),
						"is_catering_admin" => $this->ion_auth->in_group(14),
					)
				);

				$data["admin"]['user_details'] = $this->admin_model->getUser($this->session->admin['user_id']);
				$data["admin"]['user_details']->groups = $this->admin_model->getUserGroups($this->session->admin['user_id']);

				if ($this->ion_auth->in_group(1) || $this->ion_auth->in_group(10)) {
					$data["admin"]['user_details']->stores = $this->user_model->get_all_store();
				} else {
					$data["admin"]['user_details']->stores = $this->user_model->get_store_group_order($this->session->admin['user_id']);
				}

				$response = array(
					"message" => 'Successfully fetch admin session',
					"data" => $data,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	// TO BE IMPROVED ( V2 Backend )
	public function print_asdoc($id, $isCatering)
	{

		if ($isCatering == "true") {
			$query_result = $this->admin_model->getCateringBooking($id);
			$query_result->items = $this->admin_model->getCateringBookingItems($query_result->id);

			$data['info'] = $query_result;
			$data['orders'] =  $query_result->items;

			$print = $this->load->view('/report/catering_invoice_print', $data, TRUE);
		} else {
			$query_result = $this->admin_model->get_order_summary($id);
			$data['info'] = $query_result['clients_info'];
			$data['orders'] = $query_result['order_details'];

			/** Downlaod as word-doc */
			$print = $this->load->view('/report/invoice_print', $data, TRUE);
		}

		header("Content-Type: application/vnd.ms-word");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=Report.doc");

		echo $print;
	}

	// TO BE IMPROVED ( V2 Backend )
	public function print_view($id, $isCatering)
	{
		if ($isCatering == "true") {
			$query_result = $this->admin_model->getCateringBooking($id);
			$query_result->items = $this->admin_model->getCateringBookingItems($query_result->id);

			$data['info'] = $query_result;
			$data['orders'] =  $query_result->items;

			return $this->load->view('/report/catering_invoice_print', $data);
		} else {
			$query_result = $this->admin_model->get_order_summary($id);
			$data['info'] = $query_result['clients_info'];
			$data['orders'] = $query_result['order_details'];

			return $this->load->view('/report/invoice_print', $data);
		}
	}

	// TO BE IMPROVED ( V2 Backend ) *** will create a helper with this
	private function send_sms($to, $text)
	{

		$dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
		$dotenv->load();

		$api_key = $_ENV['SMS_API_KEY'];
		$api_sec =  $_ENV['SMS_API_SEC'];
		$sender_name = $_ENV['SMS_SENDER_NAME'];
		$new_text = urlencode($text);

		$url = 'https://rest-portal.promotexter.com/sms/send?apiKey=' . $api_key . '&apiSecret=' . $api_sec . '&from=' . $sender_name . '&to=' . $to . '&text=' . $new_text;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		// curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			$msg = "Error: " . curl_error($ch);
			$status = FALSE;
		} else {
			$jsonArrayResponse = json_decode($result, TRUE);
			curl_close($ch);
			$status = ($jsonArrayResponse['status'] == 'ok') ? TRUE : FALSE;
			$msg = ($status) ? "Sending Successful" : "Sending Failed";
		}

		header('content-type: application/json');
		echo json_encode(array("status" => $status, 'message' => $msg));

		return $status;
	}

	// WILL BE DEPRECATED ( V2 Backend )
	public function status_notification($transaction_id = '', $status = '', $user_id)
	{
		$query_result = $this->admin_model->get_order_summary($transaction_id);
		$info = $query_result['clients_info'];

		switch ($status) {
				// PAID
			case '2':
				break;

				// CONFIRMED
			case '3':
				break;

				// DECLINE
			case '4':
				break;

				// CANCELLED
			case '5':
				break;

				// COMPLETE
			case '6':
				break;

				// REJECTED
			case '7':
				break;

				// DISPATCH
			case '9':
				// SMS message
				$sms_msg = 'Hey! Your product is now ready. Our team will get in touch with you shortly regarding the order logistics.';

				$email_stat = TRUE;
				$sms_stat = TRUE;
				if ($info->table_number != null) {
					if ($this->send_sms($info->contact_number, $sms_msg)) {
						$sms_stat = TRUE;
						$this->logs_model->insertTransactionLogs($user_id, 1, $transaction_id, 'Dispatched-sms-sent');
					} else {
						$sms_stat = FALSE;
						$this->logs_model->insertTransactionLogs($user_id, 1, $transaction_id, 'Dispatched-sms-not-sent');
					}
				}

				return array('email_status' => $email_stat, 'sms_status' => $sms_stat);
				break;
		}
	}


	public function payment()
	{
		switch ($this->input->server('REQUEST_METHOD')) {
			case 'POST':

				$config['upload_path']          = './assets/upload/proof_payment/';
				$config['allowed_types']        = 'jpeg|jpg|png';
				$config['max_size']             = 2000;
				$config['max_width']            = 0;
				$config['max_height']           = 0;
				$config['encrypt_name']         = TRUE;

				$this->load->library('upload', $config);

				$trans_id = $_POST['trans_id'];

				if (!$this->upload->do_upload('payment_file')) { // Upload validation
					// Failed-Upload
					$error = $this->upload->display_errors();
					$this->output->set_status_header('401');
					echo json_encode(array("message" => $error));
				} else {
					// File-Uploaded-Successfull
					$data = $this->upload->data(); // Get file details
					$file_name = $data['file_name'];

					$this->admin_model->uploadPayment($trans_id, $data, $file_name);

					header('content-type: application/json');
					echo json_encode(array("message" => 'Succesfully upload payment'));
				}

				break;
		}
	}
	function test()
	{
		header('content-type: application/json');
		echo json_encode(array("message" => 'Succesfully upload payment'));
	}
}