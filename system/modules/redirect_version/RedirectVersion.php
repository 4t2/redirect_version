<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Class RedirectVersion
 *
 * @copyright  Lingo4you 2012
 * @author     Mario Müller <http://www.lingo4u.de/>
 * @package    RedirectVersion
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
class RedirectVersion extends Controller
{

	public function getPageIdFromUrl($arrFragments)
	{
		$this->import('Database');

		$fragments = $arrFragments;

		$alias = array_shift($fragments);

		$objPages = $this->Database->prepare('SELECT COUNT(*) AS `total` FROM `tl_page` WHERE `alias`=?')->limit(1)->executeUncached($alias);

		if ($objPages->total < 1)
		{
			$requestAlias = $this->getRequestAlias();

			$objVersions = $this->Database->execute("SELECT `pid`,`data` FROM `tl_version` WHERE `fromTable`='tl_page'");

			while ($objVersions->next())
			{
				$rowPage = unserialize($objVersions->data);
				
				if ($rowPage['alias'] == $alias || ($requestAlias && $rowPage['alias'] == $requestAlias))
				{
					$objPage = $this->Database->prepare("SELECT * FROM `tl_page` WHERE `id`=?")->limit(1)->executeUncached($objVersions->pid);
					$this->redirect($this->generateFrontendUrl($objPage->fetchAssoc(), '/'.implode('/', $fragments), $objPage->language), 301);
				}
			}
		}
	
	    return $arrFragments;
	}


	protected function getRequestAlias()
	{
		if ($this->Environment->request != '')
		{
			$strRequest = preg_replace(array('/^index.php\/?/', '/\?.*$/'), '', $this->Environment->request);
	
			// Remove the URL suffix if not just a language root (e.g. en/) is requested
			if ($strRequest != '' && (!$GLOBALS['TL_CONFIG']['addLanguageToUrl'] || !preg_match('@^[a-z]{2}/$@', $strRequest)))
			{
				$intSuffixLength = strlen($GLOBALS['TL_CONFIG']['urlSuffix']);
	
				// Return false if the URL suffix does not match (see #2864)
				if ($intSuffixLength > 0)
				{
					if (substr($strRequest, -$intSuffixLength) != $GLOBALS['TL_CONFIG']['urlSuffix'])
					{
						return false;
					}
	
					$strRequest = substr($strRequest, 0, -$intSuffixLength);
				}
			}
			
			return $strRequest;
		}
		else
		{
			return false;
		}
	}

}

?>