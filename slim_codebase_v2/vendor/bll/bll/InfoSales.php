<?php
/**
 *  Framework 
 *
 * @link       
 * @copyright Copyright (c) 2017
 * @license   
 */

namespace BLL\BLL;

/**
 * Business Layer class for after sales dashboard data
 */
class InfoSales extends \BLL\BLLSlim{
    
    /**
     * constructor
     */
    public function __construct() {
        //parent::__construct();
    }
    
    /**
     * get kamyon sales detail data
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getKamyonSales($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoSalesOraclePDO');
        return $DAL->getKamyonSales($params);
    }
    
    /**
     * get dealer invoice data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getDealerInvoice($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoSalesOraclePDO');
        return $DAL->getDealerInvoice($params);
    }
    
    /**
     * get sales  summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getSalesDashboardData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoSalesOraclePDO');
        return $DAL->getSalesDashboardData($params);
    }
    
    /**
     * get funnel hassas olcum detail data
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getFunnelOlcumData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoSalesOraclePDO');
        return $DAL->getFunnelOlcumData($params);
    }
    
    /**
     * get funnel basic olcum detail data
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getFunnelBasicData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoSalesOraclePDO');
        return $DAL->getFunnelBasicData($params);
    }

    public function delete($params = array()) {
        
    }

    public function getAll($params = array()) {
        
    }

    public function insert($params = array()) {
        
    }

    public function update($params = array()) {
        
    }

}

