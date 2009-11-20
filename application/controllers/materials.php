<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Materials extends MY_Controller
{

    public $groupList = array(18, 754, /* 886,*/ 334, 873, 913, 427, 428, 429, 465, 423);
    
    /**
     * Loads the invTypes for $groupID
     *
     * @param int
     * @returns array
     **/
    private function _get_invgroup($groupID)
    {
        $q = $this->db->query('
            SELECT 
                * 
            FROM 
                invTypes,
                invGroups,
                eveGraphics
            WHERE 
                invTypes.graphicID=eveGraphics.graphicID AND
                invTypes.marketGroupID IS NOT NULL AND
                invTypes.groupID=invGroups.groupID AND 
                invTypes.groupID = ?', $groupID);
        return ($q->result_array());
    }
    
    /**
     * materials loader
     *
     * Loads the materials from db for our json request
     *
     * @param   int
     *
     * @todo this has gotten quite messy, and should be "reviewed"
         */
    public function load($groupID)
    {
        $character = $this->character;

        $regionID = !get_user_config($this->Auth['user_id'], 'market_region') ? 10000067 : get_user_config($this->Auth['user_id'], 'market_region');
        $custom_prices = $this->input->post('custom_prices') ? True : False;
        $data['custom_prices'] = $custom_prices;


        // Step 1: Pull all the player owned assets from the db for $groupID
        $assets = AssetList::getAssetsFromDB($this->chars[$character]['charid'], array("invGroups.groupID" => $groupID));

        $materials = array();        
        foreach ($assets as $loc)
        {
            foreach ($loc as $asset)
            {
                if ($asset['groupID'] == $groupID) 
                {
                    if (!isset($data['data'][$asset['typeID']]))
                    {
                        $materials[$asset['typeID']] = array_merge($asset ,array('quantity' => 0));
                    }
                    $materials[$asset['typeID']]['quantity'] += $asset['quantity'];
                }
                if (isset($asset['contents']))
                {
                    foreach ($asset['contents'] as $content)
                    {
                        if ($content['groupID'] == $groupID) 
                        {
                            if (!isset($data['data'][$content['typeID']]))
                            {
                                $materials[$content['typeID']] = array_merge($content ,array('quantity' => 0));
                            }
                            $materials[$content['typeID']]['quantity'] += $content['quantity'];
                        }
                    }
                }
            }
        }

        // Step 2: Pull all invTypes from $groupID and merge with the quantities from Step 1
        $invtypes = $this->_get_invgroup($groupID);
        $typeids = array();
        foreach ($invtypes as $v)
        {
            if (!empty($materials[$v['typeID']]))
            {
                $data['data'][] = array_merge($v, array('quantity' => $materials[$v['typeID']]['quantity']  )); 
            }
            else
            {
                $data['data'][] = array_merge($v, array('quantity' => 0));
            }
            $typeids[] = $v['typeID'];
        }
                        
        /**
         * Step 3: Did the user alter any quantity and posted? If so, overwrite quantity pulled from Step 2
         * @todo Do we want to save this into the database?
         **/
        if (is_numeric($this->input->post('content')))
        {
            //$to_update = $ordered[$this->input->post('n')];
            $data['data'][$this->input->post('n')]['quantity'] = $this->input->post('content');
        }

        // Step 4: Pull the prices for all of $groupID
        $data['sums']['volume'] = $data['sums']['sellprice'] = $data['sums']['buyprice'] = 0;
        $data['prices'] = $this->evecentral->getPrices($typeids, $regionID, $custom_prices);

        // Step 5: And finally add all the quantites up to totals
        foreach ($data['data'] as $v)
        {
            $data['sums']['volume'] += $v['volume']*$v['quantity'];
            $data['sums']['sellprice'] += $v['quantity']*$data['prices'][$v['typeID']]['sell']['median'];
            $data['sums']['buyprice'] += $v['quantity']*$data['prices'][$v['typeID']]['buy']['median'];
        }        

        // Step 6: Profit!
        echo json_encode($data);
        exit;
    }
    
    /**
     * materials
     *
     * Display a Table with the Materials, Amounts and Values defined by ?groupID=
     *
     * @param   int
     */
    public function index($groupID = 18)
    {
        $regionID = !get_user_config($this->Auth['user_id'], 'market_region') ? 10000067 : get_user_config($this->Auth['user_id'], 'market_region');
        
        //FIXME: Make it possible again to check for categoryID's
        $sID = 'groupID'; // What ID to search for
        
        if ($this->input->post('groupID'))
        {
            $groupID = $this->input->post('groupID');
            redirect(site_url("materials/index/".$groupID));
            exit;
        }
        $custom_prices = $this->input->post('custom_prices') ? True : False;
        $data['custom_prices'] = $custom_prices;
        
        $data['groupID'] = $groupID;

        $groupIDList = array();
        $q = $this->db->query('SELECT groupID,groupName FROM invGroups;');
        foreach ($q->result() as $row)
        {
            if ($row->groupID == $groupID)
            {
                $data['caption'] = $row->groupName;
                $data['caption'] .= ' - Prices from the "'.regionid_to_name($regionID).'" region';
            }
            if (in_array($row->groupID, $this->groupList))
            {
                $groupIDList[$row->groupID] = $row->groupName;
            }
  
        }
        $data['groupIDList'] = $groupIDList;
        $data['caption'] = 'Materials - Prices from the "'.regionid_to_name($regionID).'" region';
        
        $data['types'] = $this->_get_invgroup($groupID);

        foreach ($data['types'] as $r)
        {
            $typeIDList[$r['typeID']] = $r['typeName'];
        }
        $data['prices'] = $this->evecentral->getPrices(array_keys($typeIDList), $regionID, $custom_prices);

        $template['content'] = $this->load->view('materials', $data, True);
        $this->load->view('maintemplate', $template);
    }
}
?>
