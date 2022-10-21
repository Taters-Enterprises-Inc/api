<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bsc_auth
{
	protected $status;

	public $_extra_where = [];

	public $_extra_set = [];

	public $_cache_user_in_group;

	public function __construct()
	{
		$this->check_compatibility();

		$this->config->load('bsc_auth', TRUE);
		$this->load->library(['email']);
		$this->lang->load('ion_auth');
		$this->load->helper(['cookie', 'language','url']);

		$this->load->library('session');

		$this->load->model('bsc_auth_model');

		$this->_cache_user_in_group =& $this->bsc_auth_model->_cache_user_in_group;
	
		$email_config = $this->config->item('email_config', 'ion_auth');

		if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config))
		{
			$this->email->initialize($email_config);
		}

		$this->bsc_auth_model->trigger_events('library_constructor');
	}

	public function __call($method, $arguments)
	{
		if($method == 'create_user')
		{
			return call_user_func_array([$this, 'register'], $arguments);
		}
		if($method=='update_user')
		{
			return call_user_func_array([$this, 'update'], $arguments);
		}
		if (!method_exists( $this->bsc_auth_model, $method) )
		{
			throw new Exception('Undefined method Ion_auth::' . $method . '() called');
		}
		return call_user_func_array( [$this->bsc_auth_model, $method], $arguments);
	}

	public function __get($var)
	{
		return get_instance()->$var;
	}

	public function forgotten_password($identity)
	{
		$user = $this->where($this->bsc_auth_model->identity_column, $identity)
					 ->where('active', 1)
					 ->users()->row();

		if ($user)
		{
			$code = $this->bsc_auth_model->forgotten_password($identity);

			if ($code)
			{
				$data = [
					'identity' => $identity,
					'forgotten_password_code' => $code
				];

				if (!$this->config->item('use_ci_email', 'bsc_auth'))
				{
					$this->set_message('forgot_password_successful');
					return $data;
				}
				else
				{
					$message = $this->load->view($this->config->item('email_templates', 'bsc_auth') . $this->config->item('email_forgot_password', 'bsc_auth'), $data, TRUE);
					$this->email->clear();
					$this->email->from($this->config->item('admin_email', 'bsc_auth'), $this->config->item('site_title', 'bsc_auth'));
					$this->email->to($user->email);
					$this->email->subject($this->config->item('site_title', 'bsc_auth') . ' - ' . $this->lang->line('email_forgotten_password_subject'));
					$this->email->message($message);

					if ($this->email->send())
					{
						$this->set_message('forgot_password_successful');
						return TRUE;
					}
				}
			}
		}

		$this->set_error('forgot_password_unsuccessful');
		return FALSE;
	}

	/**
	 * forgotten_password_check
	 *
	 * @param string $code
	 *
	 * @return object|bool
	 * @author Michael
	 */
	public function forgotten_password_check($code)
	{
		$user = $this->bsc_auth_model->get_user_by_forgotten_password_code($code);

		if (!is_object($user))
		{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}
		else
		{
			if ($this->config->item('forgot_password_expiration', 'bsc_auth') > 0)
			{
				//Make sure it isn't expired
				$expiration = $this->config->item('forgot_password_expiration', 'bsc_auth');
				if (time() - $user->forgotten_password_time > $expiration)
				{
					//it has expired
					$identity = $user->{$this->config->item('identity', 'bsc_auth')};
					$this->bsc_auth_model->clear_forgotten_password_code($identity);
					$this->set_error('password_change_unsuccessful');
					return FALSE;
				}
			}
			return $user;
		}
	}

	public function register($identity, $password, $email, $additional_data = [], $group_ids = [])
	{
		$this->bsc_auth_model->trigger_events('pre_account_creation');

		$email_activation = $this->config->item('email_activation', 'bsc_auth');

		$id = $this->bsc_auth_model->register($identity, $password, $email, $additional_data, $group_ids);

		if (!$email_activation)
		{
			if ($id !== FALSE)
			{
				$this->set_message('account_creation_successful');
				$this->bsc_auth_model->trigger_events(['post_account_creation', 'post_account_creation_successful']);
				return $id;
			}
			else
			{
				$this->set_error('account_creation_unsuccessful');
				$this->bsc_auth_model->trigger_events(['post_account_creation', 'post_account_creation_unsuccessful']);
				return FALSE;
			}
		}
		else
		{
			if (!$id)
			{
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}

			$deactivate = $this->deactivate($id);

			$this->ion_auth_model->clear_messages();


			if (!$deactivate)
			{
				$this->set_error('deactivate_unsuccessful');
				$this->bsc_auth_model->trigger_events(['post_account_creation', 'post_account_creation_unsuccessful']);
				return FALSE;
			}

			$activation_code = $this->bsc_auth_model->activation_code;
			$identity        = $this->config->item('identity', 'bsc_auth');
			$user            = $this->bsc_auth_model->user($id)->row();

			$data = [
				'identity'   => $user->{$identity},
				'id'         => $user->id,
				'email'      => $email,
				'activation' => $activation_code,
			];
			if(!$this->config->item('use_ci_email', 'bsc_auth'))
			{
				$this->bsc_auth_model->trigger_events(['post_account_creation', 'post_account_creation_successful', 'activation_email_successful']);
				$this->set_message('activation_email_successful');
				return $data;
			}
			else
			{
				$message = $this->load->view($this->config->item('email_templates', 'bsc_auth').$this->config->item('email_activate', 'bsc_auth'), $data, true);

				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'bsc_auth'), $this->config->item('site_title', 'bsc_auth'));
				$this->email->to($email);
				$this->email->subject($this->config->item('site_title', 'bsc_auth') . ' - ' . $this->lang->line('email_activation_subject'));
				$this->email->message($message);

				if ($this->email->send() === TRUE)
				{
					$this->bsc_auth_model->trigger_events(['post_account_creation', 'post_account_creation_successful', 'activation_email_successful']);
					$this->set_message('activation_email_successful');
					return $id;
				}

			}

			$this->bsc_auth_model->trigger_events(['post_account_creation', 'post_account_creation_unsuccessful', 'activation_email_unsuccessful']);
			$this->set_error('activation_email_unsuccessful');
			return FALSE;
		}
	}

	public function logout()
	{
		$this->bsc_auth_model->trigger_events('logout');

		$identity = $this->config->item('identity', 'bsc_auth');

		$this->session->unset_userdata('bsc');

		// delete the remember me cookies if they exist
		delete_cookie($this->config->item('remember_cookie_name', 'bsc_auth'));

		// Clear all codes
		$this->bsc_auth_model->clear_forgotten_password_code($identity);
		$this->bsc_auth_model->clear_remember_code($identity);

		// Destroy the session
		$this->session->sess_destroy();

		$this->set_message('logout_successful');
		return TRUE;
	}

	public function logged_in()
	{
		$this->bsc_auth_model->trigger_events('logged_in');

		$recheck = $this->bsc_auth_model->recheck_session();

		if (!$recheck && get_cookie($this->config->item('remember_cookie_name', 'bsc_auth')))
		{
			$recheck = $this->bsc_auth_model->login_remembered_user();
		}

		return $recheck;
	}

	public function get_user_id()
	{
		$user_id = $this->session->userdata('user_id');
		if (!empty($user_id))
		{
			return $user_id;
		}
		return NULL;
	}

	public function is_admin($id = FALSE)
	{
		$this->bsc_auth_model->trigger_events('is_admin');

		$admin_group = $this->config->item('admin_group', 'bsc_auth');

		return $this->bsc_auth_model->in_group($admin_group, $id);
	}

	/**
	 * Check the compatibility with the server
	 *
	 * Script will die in case of error
	 */
	protected function check_compatibility()
	{
		// PHP password_* function sanity check
		if (!function_exists('password_hash') || !function_exists('password_verify'))
		{
			show_error("PHP function password_hash or password_verify not found. " .
				"Are you using CI 2 and PHP < 5.5? " .
				"Please upgrade to CI 3, or PHP >= 5.5 " .
				"or use password_compat (https://github.com/ircmaxell/password_compat).");
		}

		// Sanity check for CI2
		if (substr(CI_VERSION, 0, 1) === '2')
		{
			show_error("Ion Auth 3 requires CodeIgniter 3. Update to CI 3 or downgrade to Ion Auth 2.");
		}

		// Compatibility check for CSPRNG
		// See functions used in Ion_auth_model::_random_token()
		if (!function_exists('random_bytes') && !function_exists('mcrypt_create_iv') && !function_exists('openssl_random_pseudo_bytes'))
		{
			show_error("No CSPRNG functions to generate random enough token. " .
				"Please update to PHP 7 or use random_compat (https://github.com/paragonie/random_compat).");
		}
	}

	public function deactivate($id = NULL)
	{
		$this->trigger_events('deactivate');

		if (!isset($id))
		{
			$this->set_error('deactivate_unsuccessful');
			return FALSE;
		}
		else if ($this->logged_in() && $this->user()->row()->id == $id)
		{
			$this->set_error('deactivate_current_user_unsuccessful');
			return FALSE;
		}

		return $this->bsc_auth_model->deactivate($id);
	}

}
