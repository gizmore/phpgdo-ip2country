<?php
namespace GDO\IP2Country\Method;

use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\IP2Country\GDO_IPCountry;
use GDO\User\GDO_User;
use GDO\User\GDO_UserSetting;

/**
 * Detect the country for a user from friend modules.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class DetectUsers extends MethodForm
{

	public function createForm(GDT_Form $form): void
	{
		$form->addField(GDT_AntiCSRF::make());
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form)
	{
		# users without country
		$query = GDO_UserSetting::usersWithQuery('Country', 'country_of_living', null);
		# stats
		$rows = 0;
		$succ = 0;
		$fail = 0;
		# go
		$result = $query->exec();
		$user = $result->getDummy();
		while ($user = $result->fetchInto($user))
		{
			$rows++;
			$s = (int)$this->detectUser($user);
			$succ += $s;
			$fail += 1 - $s;
		}
		return $this->message('msg_ip2country_detection', [
			$rows, $succ, $fail]);
	}

	###########
	### API ###
	###########
	public static function detectUser(GDO_User $user): bool
	{
		if ($ip = self::getIP($user))
		{
			if ($country = GDO_IPCountry::detect($ip))
			{
				$user->saveSettingVar('Country', 'country_of_living', $country->getID());
				return true;
			}
		}
		return false;
	}

	private static function getIP(GDO_User $user): ?string
	{
		# @TODO use DoubleAccount in IP2Country.
		if (module_enabled('DoubleAccounts'))
		{

		}
		if (module_enabled('Register'))
		{
			return $user->settingVar('Register', 'register_ip');
		}
		return null;
	}

}
