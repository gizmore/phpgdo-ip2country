<?php
namespace GDO\IP2Country;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\IP2Country\Method\DetectUsers;
use GDO\Register\GDO_UserActivation;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;

/**
 * IP2Country detection.
 *
 * @version 7.0.1
 * @since 2.0.0
 * @author gizmore
 */
final class Module_IP2Country extends GDO_Module
{

	public int $priority = 80; # Install and load late :)

	##############
	### Module ###
	##############
	public function getClasses(): array { return ['GDO\IP2Country\GDO_IPCountry']; }

	public function onLoadLanguage(): void { $this->loadLanguage('lang/ip2country'); }

	public function href_administrate_module(): ?string { return href('IP2Country', 'InstallIP2C'); }

	public function getDependencies(): array
	{
		return [
			'Country',
		];
	}

	public function getFriendencies(): array
	{
		return [
			'DoubleAccounts',
			'Register',
		];
	}

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			GDT_Checkbox::make('autodetect_signup')->initial('1'),
			GDT_Link::make('detect_users')->href(href('IP2Country', 'DetectUsers')),
		];
	}

	public function getPrivacyRelatedFields(): array
	{
		return [
			GDT_Divider::make()->label('info_privacy_related_module', [$this->gdoHumanName()]),
			$this->getConfigColumn('autodetect_signup'),
		];
	}

	public function hookUserActivated(GDO_User $user, GDO_UserActivation $activation = null)
	{
		if (module_enabled('Country'))
		{
			if ($this->cfgAutodetectSignup())
			{
				$this->autodetectForUser($user);
			}
		}
	}

	#############
	### Hooks ###
	#############

	public function cfgAutodetectSignup(): bool { return $this->getConfigValue('autodetect_signup'); }

	private static function autodetectForUser(GDO_User $user)
	{
		if (!$user->getCountryISO())
		{
			DetectUsers::detectUser($user);
		}
	}

}
