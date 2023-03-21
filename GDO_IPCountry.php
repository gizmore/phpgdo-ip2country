<?php
namespace GDO\IP2Country;

use GDO\Core\GDO;
use GDO\Core\GDT_Index;
use GDO\Core\GDT_UInt;
use GDO\Country\GDO_Country;
use GDO\Country\GDT_Country;

/**
 * IP2Country GDO table.
 *
 * @version 6.05
 * @since 3.0
 * @author gizmore
 * @see GDO_Country
 */
final class GDO_IPCountry extends GDO
{

	###########
	### GDO ###
	###########
	/**
	 *
	 * @param string $ip
	 *
	 * @return GDO_Country
	 */
	public static function detect($ip)
	{
		if ($iso = self::detectISO($ip))
		{
			return GDO_Country::getById($iso);
		}
	}

	/**
	 * Detect a country by IP. Return it's ISO2 code.
	 *
	 * @param string $ip
	 *
	 * @return string country iso
	 */
	public static function detectISO($ip)
	{
		if ($ip = ip2long($ip))
		{
			return self::table()->select('ip_country')->where("ipc_lo <= $ip AND ipc_hi >= $ip")->limit(1)->exec()->fetchValue();
		}
	}

	public function gdoEngine(): string { return self::MYISAM; }

	###########
	### API ###
	###########

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_UInt::make('ipc_lo')->notNull(),
			GDT_UInt::make('ipc_hi')->notNull(),
			GDT_Country::make('ip_country')->notNull(),
			GDT_Index::make()->indexColumns('ipc_lo', 'ipc_hi'),
		];
	}

}
