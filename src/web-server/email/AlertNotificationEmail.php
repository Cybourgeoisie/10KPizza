<?php

declare(strict_types=1);

namespace Email;

class AlertNotificationEmail extends \Scrollio\Email\AbstractAWSSESManager
{
	protected $default_email = EMAIL_ALERT_ADDRESS;
	protected $default_sender_name = EMAIL_ALERT_SENDER;

	public function sendAlertEmail(string $notification_desc)
	{
		// Construct the subject and body
		$this->setTemplateLocation(dirname(realpath(__FILE__)) . '/template.html');
		$this->setTemplateKey('EMAIL_TITLE', 'Alert Notification');
		$this->setTemplateKey('SITE_NAME', SITE_NAME);
		$this->setTemplateKey('SITE_ADDRESS', SITE_ADDRESS);
		$this->setTemplateKey('SITE_ADDRESS_CLEAN', SITE_ADDRESS_CLEAN);
		$this->setSubject($this->getAlertSubject($notification_desc));
		$this->setBody($this->getAlertBody($notification_desc));

		// Send!
		$this->send();
	}

	private function getAlertSubject(string $notification_desc)
	{
		return SITE_NAME . ' Alert: ' . $notification_desc;
	}

	private function getAlertBody(string $notification_desc)
	{
		return '<p>The following alert has been triggered:</p>' .
			'<p><center><h3>' . $notification_desc . '</h3></center></p>' .
			'<p><a href="' . SITE_ADDRESS . '">Visit the website</a> to view the current value.</p>' .
			'<p>Thank you for using ' . SITE_NAME . ' for your alerts!</p>';
	}
}
