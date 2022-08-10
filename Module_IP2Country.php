<?php
namespace GDO\IP2Country;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Register\GDO_UserActivation;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;

/**
 * IP2Country detection.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 2.0.0
 */
final class Module_IP2Country extends GDO_Module
{
	public int $priority = 80; # Install and load late :)
	
	##############
	### Module ###
	##############
	public function getClasses() : array { return ['GDO\IP2Country\GDO_IPCountry']; }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/ip2country'); }
	public function hrefAdministration() : ?string { return href('IP2Country', 'InstallIP2C'); }
	public function getDependencies() : array
	{
		return [
			'Country',
		];
	}
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return array(
			GDT_Checkbox::make('autodetect_signup')->initial('1'),
			GDT_Link::make('detect_users')->href(href('IP2Country', 'DetectUsers')),
		);
	}
	public function cfgAutodetectSignup() { return $this->getConfigValue('autodetect_signup'); }
	
	#############
	### Hooks ###
	#############
	public function hookUserActivated(GDO_User $user, GDO_UserActivation $activation=null)
	{
		if (module_enabled('Country'))
		{
			if ($this->cfgAutodetectSignup())
			{
				$this->autodetectForUser($user);
			}
		}
	}
	private static function autodetectForUser(GDO_User $user)
	{
		
		
		if (!$user->getCountryISO())
		{
		    $user->saveVar('user_country', GDO_IPCountry::detectISO($user->getRegisterIP()));
		}
	}
}
