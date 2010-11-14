<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


define('ALE_CONFIG_DIR', APPPATH.'config');

require_once(BASEPATH.'../ale/factory.php');

class Eveapi
{

    /**
    * Ale Api Object
    **/
	public $api = Null;

	/**
	* userid + key from characters.ini
	**/
	private $api_credentials = Null;

	/**
	* All Characters loaded 
	**/
	public $characters = array();

    /**
    * Array with characters to be skipped, loaded from characters.ini
    **/
    public $skip_characters = array();

	/**
	* Constructor, initialized Ali api, and loads configuration
	*
	* @access public
	**/
	public function __construct()
	{
		$this->api = AleFactory::get('eveapi'); 
		
		if (!is_readable(APPPATH.'config/characters.ini'))
		{
			die("Unable to read config/characters.ini");
		}
		$this->api_credentials = parse_ini_file(APPPATH.'config/characters.ini');

        if (isset($this->api_credentials['skip']))
        {
            $this->skip_characters = explode(':', $this->api_credentials['skip']);
        }
	}

	/**
	* Convert an ale simplexml object t oan array
	*
	* @access public
	* @param object Ale XML Object
	* @param string
	* @param array An Array to merge with each dataset
	**/
	public static function from_xml($xml, $type, $to_merge = array())
	{
		$output = array();

		foreach ($xml->result->$type as $row)
		{
			$index = count($output);
			foreach ($row->attributes() as $name => $value)
			{
				$output[$index][(string) $name] = (string) $value;
				if (in_array((string) $name, array('date', 'transactionDateTime', 'sentDate'))) 
				{
					$output[$index]['unix'.$name] = strtotime((string) $value);
				}
			}
			$output[$index] = array_merge($output[$index], (array) $to_merge);
		}
		
		return ($output);
	}

	/**
	* Load Characters from all accounts (skipping the ignored ones)
	*
	**/
	public function load_characters()
	{
		foreach ($this->api_credentials['apiuser'] as $k => $v)
		{
			if (!empty($this->api_credentials['apikey'][$k]))
			{
				$this->api->setCredentials($v, $this->api_credentials['apikey'][$k]);
			}
			else
			{
				throw new LogicException(sprintf("ApiUser [%s] doesn't have a valid ApiKey set.", $v));
			}
			
			try
			{
				$account = $this->api->account->Characters();
			}
			catch (Exception $e)
			{
				// FIXME: Ignore Characters that are unreadable (for now)
				unset($this->api_credentials['apiuser'][$k]);
				unset($this->api_credentials['apikey'][$k]);
				continue;	
			}
			
			foreach ($account->result->characters as $character)
			{
                if (in_array((string) $character->name, $this->skip_characters))
                {
                    continue;
                }

				$this->characters[(string) $character->name] = (object) array(
					'name' => (string) $character->name,
					'apiUser' => (int) $v,
					'apiKey' => (string) $this->api_credentials['apikey'][$k],
					'characterID' => (int) $character->characterID,
					'corporationName' => (string) $character->corporationName,
					'corporationID' => (int) $character->corporationID,
					);
			}
		}
		ksort($this->characters);			
		return (array_keys($this->characters));
	}


    /** 
    * Load assets from api for charaters, and merge them with inftypes, then cache
    *
    * @params object $characters Characters to pull assets fo
    * @params book $with_contents Wether to load container contents or not
    **/
	public function load_assets($characters, $with_contents = True)
	{
	    $CI =& get_instance();
	    $cache_key = 'evetool_'.md5(implode(':', $characters)).'_'.$with_contents;
	    
		if ( ($assets = $CI->cache->get($cache_key)) !== False )
		{
		    return($assets);
	    }

        $assets = $typeidlist = array();
		foreach ($this->characters as $char)
		{
		    if (!in_array($char->name, $characters))
		    {
		        continue;
	        }
			$CI->eveapi->api->setCredentials($char->apiUser, $char->apiKey, $char->characterID);
            $_assets = $this->api->char->AssetList();

            foreach ($_assets->result->assets as $asset)
            {
                $assets[(int) $asset->itemID] = array(
                    'itemID' => (int) $asset->itemID,
                    'locationID' => (int) $asset->locationID,
                    'typeID' => (int) $asset->typeID,
                    'quantity' => (int) $asset->quantity,
                    'flag' => (int) $asset->flag,
                    'singleton' => (int) $asset->singleton,
                    'containerID' => Null,
                    'owner' => $char,
                    'contents' => array(),
                    );
                $typeidlist[] = (int) $asset->typeID;
                
                $container = (int) $asset->itemID;
                
                if (isset($asset->contents) )
                {
        		    foreach ($asset->contents as $content)
        		    {
                        $assets[$container]['contents'][] = (int) $content->itemID;

                        if ($with_contents)
                        {
                            $assets[(int) $content->itemID] = array(
                                'itemID' => (int) $content->itemID,
                                'typeID' => (int) $content->typeID,
                                'quantity' => (int) $content->quantity,
                                'flag' => (int) $content->flag,
                                'singleton' => (int) $content->singleton,
                                'containerID' => $container,
                                'locationID' => $assets[$container]['locationID'],
                                'owner' => $char,
                                );
                            $typeidlist[] = (int) $content->typeID;
                        }
        		    }
                }
            }
        }
        sort($typeidlist);
        $typeidlist = array_unique($typeidlist);
        $q = $CI->db->query("
            SELECT 
                invTypes.typeName,
                invTypes.typeID,
                invGroups.groupName,
                invTypes.groupID,
                invTypes.description,
                invTypes.volume,
                invTypes.mass,
                invCategories.categoryName,
		        (SELECT iconFile FROM eveIcons WHERE iconID=invTypes.iconID ) AS iconFile,
		        invGroups.categoryID,
			    (SELECT valueInt FROM dgmTypeAttributes WHERE typeID=invTypes.typeID AND attributeID=422) AS techlevel
            FROM 
                invTypes,
                invGroups,
                invCategories
            WHERE 
                invTypes.typeID IN (".implode(',', $typeidlist).") AND 
                invTypes.groupID=invGroups.groupID AND 
                invCategories.categoryID=invGroups.categoryID;");
        $types = array();
        
        foreach ($q->result() as $row)
        {
            $types[$row->typeID] = (array) $row;
        }

        foreach ($assets as $k => $v)
        {
            $assets[$k] = array_merge($v, $types[$v['typeID']]);

        }
		$CI->cache->set($cache_key, $assets);
        return ($assets);
	}

	/**
	* Load reftypes from eveapi and cache them
	*
	**/
	public function get_reftypes()
	{
        $CI =& get_instance();
        
		if ( ($reftypes = $CI->cache->get('evetool_reftypes')) === False )
		{
		    $_reftypes = eveapi::from_xml($this->api->eve->RefTypes(), 'refTypes');
		
		    $reftypes = array();
		
		    foreach ($_reftypes as $reftype)
		    {
			    $reftypes[$reftype['refTypeID']] = $reftype['refTypeName'];
		    }
        }
        
		$CI->cache->set('evetool_reftypes', $reftypes);
		
		return ($reftypes);
	}
	
	public function get_skilltree()
	{
        $CI =& get_instance();
        
		if ( ($skilltree = $CI->cache->get('evetool_skilltree')) === False )
		{
		    $_skilltree = $this->api->eve->SkillTree();
		    $skilltree = array();
		    
		    foreach ($_skilltree->result->skillGroups as $group)
		    {
		        foreach ($group->skills as $skill)
		        {
		            $skilltree[(string) $skill->typeID]['groupName'] = (string) $group->groupName;
				    foreach (array('typeName', 'description', 'groupID', 'rank') as $field)
				    {
					    $skilltree[(string) $skill->typeID][$field] = (string) $skill->$field;
				    }
		        }
		    }
        }
        $CI->cache->set('evetool_skilltree', $skilltree);
	
		return ($skilltree);
	}

	public function get_stationlist()
    {
        $CI =& get_instance();

        $api = $CI->eveapi->api;
        $stationlist = array();

		if ( ($stationlist = $CI->cache->get('evetool_stationlist')) === False )
		{
            $_stationlist = $api->eve->ConquerableStationList();
            foreach ($_stationlist->result->outposts as $station)
            {
                foreach (array('stationName', 'stationTypeID', 'solarSystemID', 'corporationID', 'corporationName') as $field)
                {
                    $stationlist[(int) $station->stationID][$field] = (string) $station->$field;
                }
            }
        }
        $CI->cache->set('evetool_stationlist', $stationlist);

        return ($stationlist);
    }
	
	public static function charsheet_extra_info($charsheet)
	{
        $data['skillsTotal'] = $data['skillPointsTotal'] = 0;
        $data['skillsAtLevel'] = array_fill(0, 6, 0);
        
		$skillTree = array();        
		
        //print '<pre>';
		foreach ($charsheet->result->skills as $_skill)
		{
			$skill = $_skill->attributes();
			//print_r($skill);
            $data['skillPointsTotal'] += (int) $skill['skillpoints'];
			/*            
            $s = $this->eveapi->skilltree[$skill['typeID']];
            if (!isset($skillTree[$s['groupID']]))
            {
                $skillTree[$s['groupID']] = array('groupSP' => 0, 'skillCount' => 0);
            }

            $skillTree[$s['groupID']]['skills'][$skill['typeID']] = array(
                'typeID' => $skill['typeID'],
                'skillpoints' => $skill['skillpoints'],
                'rank' => $s['rank'],
                'typeName' => $s['typeName'],
                'description' => $s['description'],
                'level' => $skill['level']);
            $skillTree[$s['groupID']]['groupSP'] += $skill['skillpoints'];
            $skillTree[$s['groupID']]['skillCount'] ++;
            */
            $data['skillsTotal'] ++;
          	$data['skillsAtLevel'][(int) $skill['level']] ++;
		}
		
		//print_r($data);
        
        //die();
		return ($data);
	}
}



?>
