<?php

declare(strict_types=1);

namespace Email;

class RegistrationEmail extends \Scrollio\Email\AbstractAWSSESManager
{
	protected $default_email = EMAIL_DEFAULT_ADDRESS;
	protected $default_sender_name = EMAIL_DEFAULT_SENDER;

	public function sendRegistrationEmail(string $verification_code, string $email_address)
	{
		// Construct the subject and body
		$this->setTemplateLocation(dirname(realpath(__FILE__)) . '/template.html');
		$this->setTemplateKey('EMAIL_TITLE', 'New Account');
		$this->setTemplateKey('SITE_NAME', SITE_NAME);
		$this->setTemplateKey('SITE_ADDRESS', SITE_ADDRESS);
		$this->setTemplateKey('SITE_ADDRESS_CLEAN', SITE_ADDRESS_CLEAN);
		$this->setSubject($this->getRegistrationSubject());
		$this->setBody($this->getRegistrationBody($verification_code, $email_address));

		// Send!
		$this->send();
	}

	private function getRegistrationSubject()
	{
		return 'Welcome to ' . SITE_NAME;
	}

	private function getRegistrationBody(string $verification_code, string $email_address)
	{
		// Validate the verification code - it will only ever have alphanumeric characters
		if (preg_match('/[^a-zA-Z0-9]/', $verification_code))
		{
			throw new \Exception('Verification code is not valid.');
		}

		$verification_link = VERIFICATION_ADDRESS . $verification_code;

		return '<p>Hello!</p><p>Welcome to ' . SITE_NAME . '. Your account under the email address ' . 
			$email_address . ' has been created. In order to verify your account, ' .
			'please click on the following link:</p>' . 
			'<center><p><table cellpadding="0" cellspacing="0" border="0" style="line-height: 1.6em; width: auto !important; margin: 0 0 10px; padding: 0;">
				<tr style="line-height: 1.6em; margin: 0; padding: 0;">
					<td style="line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background-color: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
					  <a href="' . $verification_link . '" style="line-height: 2; color: #fff; border-radius: 25px; display: inline-block; cursor: pointer; font-weight: bold; text-decoration: none; background-color: #348eda; margin: 0; padding: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">
					  	Verify My Account
					  </a>
					</td>
				</tr>
			</table></p></center>' .
			'<p>If you have any questions or concerns, feel free to <a href="' . EMAIL_CONTACT_ADDRESS .
			'">contact us</a>' . ' and we\'ll get back to you shortly.</p>' .
			'<p>Thank you for joining, and we hope you enjoy using ' . SITE_NAME . '!</p>';
	}
}
