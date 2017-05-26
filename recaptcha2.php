<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.environment.browser');

/**
 * RecaptchaV2 Plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 * @since       3.1
 */
class plgCaptchaRecaptcha2 extends JPlugin
{
    const RECAPTCHA_SCRIPT = 'https://www.google.com/recaptcha/api.js?onload=JoomlaInitReCaptcha2_0&render=explicit';
    const RECAPTCHA_VERIFY = 'https://www.google.com/recaptcha/api/siteverify';
    const RECAPTCHA_CALLBACK = '/plugins/captcha/recaptcha2/js/script_2.0.js';

    private $publicKey, $privateKey, $response, $userIP, $doc, $typeMethod;

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->doc = JFactory::getDocument();
		$this->doc->addScript(self::RECAPTCHA_SCRIPT);
        $this->doc->addScript(self::RECAPTCHA_CALLBACK);

        $this->publicKey = $this->params->get('public_key', '');
        $this->privateKey = $this->params->get('private_key', '');

        $this->initTypeCaptcha();
	}

	private function initTypeCaptcha() {
        if ($this->params->get('recaptcha_type') == 0) {
            $this->typeMethod = 'captchaMethod2_0';
        } else {
            $this->typeMethod = 'captchaMethodInvisible';
        }
	}

	public function onInit()
	{
        $this->checkPublicKey();
        $this->checkPrivateKey();
        $this->checkIP();

		return true;
	}

    private function checkPublicKey() {
        if ($this->publicKey == null || $this->publicKey == '') {
            throw new Exception(JText::_('PLG_RECAPTCHA_ERROR_NO_PUBLIC_KEY'));
        }
    }

	private function checkPrivateKey() {
        if ($this->privateKey == null || $this->privateKey == '') {
            throw new Exception(JText::_('PLG_RECAPTCHA_ERROR_NO_PRIVATE_KEY'));
        }
	}

    private function checkIP() {
        $this->userIP = $this->getUserIP();
        if (!$this->userIP) {
            throw new Exception(JText::_('PLG_RECAPTCHA_ERROR_NO_IP'));
        }
    }

    private function getUserIP() {
        return $_SERVER['REMOTE_ADDR'];
    }

	public function onDisplay($id = 'dynamic_recaptcha_1')
	{
	    return $this->{$this->typeMethod}($id);
	}

	private function captchaMethod2_0($id) {
        return '<div id="' . $id . '" class="g-recaptcha" data-sitekey="' . $this->publicKey . '"></div>';
	}

	private function captchaMethodInvisible($id) {
        $this->doc->addScript('/plugins/captcha/recaptcha2/js/script.js');
        return '<div class="g-recaptcha" data-sitekey="' . $this->publicKey . '" data-callback="onSubmit" data-size="invisible"></div>';
	}

	public function onCheckAnswer()
	{
        $this->_recaptcha_http_post();

        if ($this->response->success === false) {
            $this->_subject->setError(JText::_('PLG_RECAPTCHA_ERROR_EMPTY_SOLUTION'));
            return false;
        }
        return true;
	}

    private function _recaptcha_http_post()
    {
        $curlInit = curl_init();
        curl_setopt_array($curlInit, array(
            CURLOPT_URL => self::RECAPTCHA_VERIFY,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'secret' => $this->privateKey,
                'response' => $this->getGRecaptchaResponse(),
                'remoteip' => $this->userIP
            ))
        ));
        $response = curl_exec($curlInit);
        curl_close($curlInit);
        $this->response = json_decode($response);
    }

    private function getGRecaptchaResponse () {
        return JRequest::getString('g-recaptcha-response');
    }
}
