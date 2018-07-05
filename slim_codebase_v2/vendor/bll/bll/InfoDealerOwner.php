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
class InfoDealerOwner extends \BLL\BLLSlim{
    
    /**
     * constructor
     */
    public function __construct() {
        //parent::__construct();
    }
    
    /**
     * Function to fill text on user interface layer
     * @param array $params
     * @return array
     */
    public function fillServicesDdlist($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->fillServicesDdlist($params);
        return $resultSet['resultSet'];
    }
    
    
    /**
     * get aftersales alış faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalari($params);
    }
    
    /**
     * get aftersales alış faturaları data for detailed graphs weekly with services
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariWeeklyWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariWeeklyWithServices($params);
    }
    
    
    /**
     * get aftersales alış faturaları  monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariAylik($params);
    }
    
    /**
     * get aftersales alış faturaları  monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariAylikWithServices($params);
    }
    
    /**
     * get aftersales alış faturaları  yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariYillik($params);
    }
    
    /**
     * get aftersales alış faturaları  yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariYillikWithServices($params);
    }
    
    /**
     * get aftersales işemri faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalari($params);
    }
    
    /**
     * get aftersales işemri faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariWeeklyWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariWeeklyWithServices($params);
    }
    
    /**
     * get aftersales işemri faturaları monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariAylik($params);
    }
    
    /**
     * get aftersales işemri faturaları monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariAylikWithServices($params);
    }
    
    /**
     * get aftersales işemri faturaları yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariYillik($params);
    }
    
     /**
     * get aftersales işemri faturaları yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariYillikWithServices($params);
    }
    
     /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalari($params);
    }
    
    /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalariWeeklyWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariWeeklyWithServices($params);
    }
    
    /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariAylik($params);
    }
    
    /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariAylikWithServices($params);
    }
    
    /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariYillik($params);
    }
    
    /**
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariYillikWithServices($params);
    }
    
    /**
     * get aftersales icmal faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalari($params);
    }
    
    /**
     * get aftersales icmal faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariWeeklyWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariWeeklyWithServices($params);
    }
    
    /**
     * get aftersales icmal faturaları monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariAylik($params);
    }
    
    /**
     * get aftersales icmal faturaları monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariAylikWithServices($params);
    }
    
    /**
     * get aftersales icmal faturaları yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariYillik($params);
    }
    
    /**
     * get aftersales icmal faturaları yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariYillikWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikWithoutServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikWithoutServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikAylik($params);
    }
    
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikAylikWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikYillik($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikYillikWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapanan($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapanan($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs(per month)
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananAylik($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs(per month)
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananAylikWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs(per year)
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananYillik($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs(per year)
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananYillikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardAracGirisSayilari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardAracGirisSayilari($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardAracGirisSayilariWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardAracGirisSayilariWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilari($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariYillikWithServices($params);
    }
    
    /**
     * get aftersales downtime summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardDowntime($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardDowntime($params);
    }
    
    /**
     * get aftersales downtime summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardDowntimeWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardDowntimeWithServices($params);
    }
    
    /**
     * get aftersales downtime summary data for datagrid
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridDowntime($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGridDowntime($params);
    }
    
    /**
     * get aftersales downtime summary data for datagrid
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridDowntimeWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGridDowntimeWithServices($params);
    }
    
    /**
     * get aftersales verimlilik  summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardVerimlilik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardVerimlilik($params);
    }
    
    /**
     * get aftersales verimlilik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardVerimlilikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardVerimlilikWithServices($params);
    }
    
    /**
     * get aftersales verimlilik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayVerimlilikYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayVerimlilikYillik($params);
    }
    
    /**
     * get aftersales verimlilik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayVerimlilikYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayVerimlilikYillikWithServices($params);
    }
    
    /**
     * get aftersales kapasite  summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardKapasite($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardKapasite($params);
    }
    
    /**
     * get aftersales kapasite summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardKapasiteWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardKapasiteWithServices($params);
    }
    
    /**
     * get aftersales kapasite summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayKapasiteYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayKapasiteYillik($params);
    }
    
    /**
     * get aftersales verimlilik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayKapasiteYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayKapasiteYillikWithServices($params);
    }
    
    /**
     * get aftersales etkinlik  summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardEtkinlik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardEtkinlik($params);
    }
    
    /**
     * get aftersales etkinlik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardEtkinlikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardEtkinlikWithServices($params);
    }
    
    /**
     * get aftersales etkinlik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayEtkinlikYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayEtkinlikYillik($params);
    }
    
    /**
     * get aftersales etkinlik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayEtkinlikYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayEtkinlikYillikWithServices($params);
    }
    
    /**
     * get aftersales yedek parca toplam satış summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardYedekParcaTS($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardYedekParcaTS($params);
    }
    
    /**
     * get aftersales yedek parca toplam satış summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardYedekParcaTSWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardYedekParcaTSWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTS($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTS($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSYillikWithServices($params);
    }
    
    /**
     * get aftersales yedek parca yag satış summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardYedekParcaYS($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardYedekParcaYS($params);
    }
    
    /**
     * get aftersales yedek parca yag satış summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardYedekParcaYSWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardYedekParcaYSWithServices($params);
    }
    
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYS($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYS($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSYillikWithServices($params);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * get aftersales atolye cirosu summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardAtolyeCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardAtolyeCirosu($params);
    }
    
    /**
     * get aftersales atolye cirosu summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardAtolyeCirosuWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardAtolyeCirosuWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosu($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuYillikWithServices($params);
    }
    
    
    
    
    
    
   
    
    
    /**
     * get aftersales atolye cirosu summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardGarantiCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardGarantiCirosu($params);
    }
    
    /**
     * get aftersales atolye cirosu summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardGarantiCirosuWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardGarantiCirosuWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosu($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuYillikWithServices($params);
    }
    
    
    
    
    
    
    
    
   
    
    
    
    /**
     * get aftersales atolye cirosu summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardDirekSatisCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardDirekSatisCirosu($params);
    }
    
    
    /**
     * get aftersales atolye cirosu summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardDirekSatisCirosuWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardDirekSatisCirosuWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayDirekSatisCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayDirekSatisCirosu($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayDirekSatisCirosuWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayDirekSatisCirosuWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayDirekSatisCirosuAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayDirekSatisCirosuAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayDirekSatisCirosuAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayDirekSatisCirosuAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayDirekSatisCirosuYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayDirekSatisCirosuYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayDirekSatisCirosuYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayDirekSatisCirosuYillikWithServices($params);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * get aftersales ciro data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiro($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayCiro($params);
    }
    
    /**
     * get aftersales ciro data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayCiroWithServices($params);
    }
    
    /**
     * get aftersales ciro monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayCiroAylik($params);
    }
    
    /**
     * get aftersales ciro monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayCiroAylikWithServices($params);
    }
    
    /**
     * get aftersales ciro yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayCiroYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayCiroYillikWithServices($params);
    }
    
    /**
     * get aftersales CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardMMCSI($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardMMCSI($params);
    }
    
    /**
     * get aftersales CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardMMCSIWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardMMCSIWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayMMCSIYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayMMCSIYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayMMCSIYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayMMCSIYillikWithServices($params);
    }
    
    /**
     * get aftersales imüşteri memnuniyet CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridMMCSI($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGridMMCSI($params);
    }
    
    /**
     * get aftersales imüşteri memnuniyet CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridMMCSIWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGridMMCSIWithServices($params);
    }
    
    /**
     * get aftersales CXI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardMMCXI($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardMMCXI($params);
    }
    
    /**
     * get aftersales CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardMMCXIWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardMMCXIWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayMMCXIYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayMMCXIYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayMMCXIYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayMMCXIYillikWithServices($params);
    }
    
    /**
     * get aftersales imüşteri memnuniyet CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridMMCXI($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGridMMCXI($params);
    }
    
    /**
     * get aftersales imüşteri memnuniyet CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridMMCXIWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayGridMMCXIWithServices($params);
    }
    
    /**
     * get aftersales bayi stocks for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayBayiStok($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDetayBayiStok($params);
    }
    
    /**
     * get aftersales last is emirleri data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardIsEmriLastDataMusteri($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardIsEmriLastDataMusteri($params);
    }
    
    /**
     * get aftersales is emirleri summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardIsEmirData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardIsEmirData($params);
    }
    
    /**
     * get aftersales is emirleri summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardIsEmirDataWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardIsEmirDataWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardFaturaData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardFaturaData($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardFaturaDataWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardFaturaDataWithServices($params);
    }
    
    /**
     * get aftersales ciro , yedek parca, müşteri sayısı data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardCiroYedekParcaData($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardCiroYedekParcaData($params);
    }
    
    /**
     * get aftersales stok data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardStoklar($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardStoklar($params);
    }
    
    /**
     * get aftersales stok data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardStoklarWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        return $DAL->getAfterSalesDashboardStoklarWithServices($params);
    }
    
    /**
     * Function to fill stoklar datagrid on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDetayStoklarGrid ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDetayStoklarGrid($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function to fill stoklar datagrid on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDetayStoklarGridWithServices ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDetayStoklarGridWithServices($params);  
        return $resultSet['resultSet'];
    }

    public function delete($params = array()) {
        
    }

    public function getAll($params = array()) {
        
    }

    public function insert($params = array()) {
        
    }

    public function update($params = array()) {
        
    }
    
    
    //detay yedek parça sayfası fonk. baş
    /**
     * Function yedek parça toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDashboardFaalYedekParca ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDashboardFaalYedekParca($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDetayFaalYedekParca ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDetayFaalYedekParca($params);  
        return $resultSet['resultSet'];
    }
    
      /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDashboardFaalYedekParcaWithServices ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDashboardFaalYedekParcaWithServices($params);  
        return $resultSet['resultSet'];
    }
    
    
         /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDashboardFaalYedekParcaServisDisiWithServices ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDashboardFaalYedekParcaServisDisiWithServices($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDashboardFaalYagToplam ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDashboardFaalYagToplam($params);  
        return $resultSet['resultSet'];
    }
    
     
       /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDashboardFaalYagToplamWithServices ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDashboardFaalYagToplamWithServices($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDashboardFaalStokToplam ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDashboardFaalStokToplam($params);  
        return $resultSet['resultSet'];
    }

    
      /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDashboardFaalStokToplamWithServices ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesDashboardFaalStokToplamWithServices($params);  
        return $resultSet['resultSet'];
    }
    //detay yedek parça sayfası fonk. son
    
    
    
    
    //detay yedek parça hedef fonk. baş
    
    /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesYedekParcaHedefServissiz ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesYedekParcaHedefServissiz($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesYedekParcaHedefServisli ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesYedekParcaHedefServisli($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesYedekParcaPDFServissiz ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesYedekParcaPDFServissiz($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function yağ toplam on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesYedekParcaPDFServisli ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoDealerOwnerOraclePDO');
        $resultSet = $DAL->getAfterSalesYedekParcaPDFServisli($params);  
        return $resultSet['resultSet'];
    }
    
    
    //detay yedek parça hedef fonk. son
}

