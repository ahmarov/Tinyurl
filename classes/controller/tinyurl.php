<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Controller_Tinyurl
 *
 * @author     Dmitri Ahmarov
 * @copyright  (c) 2011 Dmitri Ahmarov
 * @license    MIT
 */
class Controller_Tinyurl extends Kohana_Controller_Template {

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = FALSE;

	public function action_index()
	{
		$key = trim($this->request->param('key'));

		if (NULL !== $key && strlen($key) > 0)
		{
			if (NULL !== ($redirect_url = Tinyurl::get_key_data($key)))
			{
				$this->request->redirect($redirect_url);
			}
		}

		$this->request->redirect('/');
	}

} // EOF Controller_Tinyurl