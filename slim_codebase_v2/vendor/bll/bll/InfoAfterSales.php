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
class InfoAfterSales extends \BLL\BLLSlim{
    
    /**
     * constructor
     */
    public function __construct() {
        //parent::__construct();
    }
    
    
    /**
     * get aftersales alış faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalari($params);
    }
    
    /**
     * get aftersales alış faturaları  monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariAylik($params);
    }
    
    /**
     * get aftersales alış faturaları  yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariYillik($params);
    }
    
    /**
     * get aftersales işemri faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalari($params);
    }
    
    /**
     * get aftersales işemri faturaları monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariAylik($params);
    }
    
    /**
     * get aftersales işemri faturaları yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariYillik($params);
    }
    
     /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalari($params);
    }
    
    /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariAylik($params);
    }
    
    /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariYillik($params);
    }
    
    /**
     * get aftersales icmal faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalari($params);
    }
    
    /**
     * get aftersales icmal faturaları monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariAylik($params);
    }
    
    /**
     * get aftersales icmal faturaları yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariYillik($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapanan($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapanan($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs(per month)
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananAylik($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs(per year)
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananYillik($params);
    }
    
    /**
     * get aftersales ciro data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiro($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayCiro($params);
    }
    
    /**
     * get aftersales ciro monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayCiroAylik($params);
    }
    
    /**
     * get aftersales ciro yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayCiroYillik($params);
    }
    
    /**
     * get aftersales bayi stocks for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayBayiStok($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayBayiStok($params);
    }
    
    /**
     * get aftersales last is emirleri data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardIsEmriLastDataMusteri($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardIsEmriLastDataMusteri($params);
    }
    
    /**
     * get aftersales is emirleri summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardIsEmirData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardIsEmirData($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardFaturaData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardFaturaData($params);
    }
    
    /**
     * get aftersales ciro , yedek parca, müşteri sayısı data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardCiroYedekParcaData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardCiroYedekParcaData($params);
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

