<?php
/*
 * @package    Joomla.DaData - IPLocate
 * @version    __DEPLOYMENT_VERSION__
 * @author     Artem Vasilev - webmasterskaya.xyz
 * @copyright  Copyright (c) 2018 - 2021 Webmasterskaya. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://webmasterskaya.xyz/
 */

class dadata_iplocateInstallerScript
{

	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var  string
	 *
	 * @since __DEPLOYMENT_VERSION__
	 */
	protected $minimumPhp = '7.1';

	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var string
	 *
	 * @since __DEPLOYMENT_VERSION__
	 */
	protected $minimumJoomla = '3.9.0';

	/**
	 * Method to check compatible
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since  __DEPLOYMENT_VERSION__
	 */
	function preflight()
	{
		// Check old Joomla!
		if (!class_exists('Joomla\CMS\Version'))
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'LIB_DADATA_IPLOCATE_ERROR_COMPATIBLE_JOOMLA',
					$this->minimumJoomla
				),
				'error'
			);

			return false;
		}

		$app      = Joomla\CMS\Factory::getApplication();
		$jversion = new Joomla\CMS\Version();

		// Check PHP
		if (!(version_compare(PHP_VERSION, $this->minimumPhp) >= 0))
		{
			$app->enqueueMessage(
				Joomla\CMS\Language\Text::sprintf(
					'LIB_DADATA_IPLOCATE_ERROR_COMPATIBLE_PHP',
					$this->minimumPhp
				),
				'error'
			);

			return false;
		}

		// Check Joomla version
		if (!$jversion->isCompatible($this->minimumJoomla))
		{
			$app->enqueueMessage(
				Joomla\CMS\Language\Text::sprintf(
					'LIB_DADATA_IPLOCATE_ERROR_COMPATIBLE_JOOMLA',
					$this->minimumJoomla
				),
				'error'
			);

			return false;
		}

		return true;
	}

}
