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
     * Function to fill text on user interface layer
     * @param array $params
     * @return array
     */
    public function fillServicesDdlist($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
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
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalari($params);
    }
    
    /**
     * get aftersales alış faturaları data for detailed graphs weekly with services
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariWeeklyWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariWeeklyWithServices($params);
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
     * get aftersales alış faturaları  monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariAylikWithServices($params);
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
     * get aftersales alış faturaları  yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAlisFaturalariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAlisFaturalariYillikWithServices($params);
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
     * get aftersales işemri faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariWeeklyWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariWeeklyWithServices($params);
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
     * get aftersales işemri faturaları monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariAylikWithServices($params);
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
     * get aftersales işemri faturaları yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsemriFaturalariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsemriFaturalariYillikWithServices($params);
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
    public function getAfterSalesDetaySatisFaturalariWeeklyWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariWeeklyWithServices($params);
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
    public function getAfterSalesDetaySatisFaturalariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariAylikWithServices($params);
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
     * get aftersales satiş faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetaySatisFaturalariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetaySatisFaturalariYillikWithServices($params);
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
     * get aftersales icmal faturaları data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariWeeklyWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariWeeklyWithServices($params);
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
     * get aftersales icmal faturaları monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariAylikWithServices($params);
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
     * get aftersales icmal faturaları yearly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIcmalFaturalariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIcmalFaturalariYillikWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikWithoutServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikWithoutServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikAylik($params);
    }
    
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikAylikWithServices($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikYillik($params);
    }
    
    /**
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcikYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcikYillikWithServices($params);
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
     * get aftersales last is emirleri açık/kapalı data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananWithServices($params);
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
     * get aftersales last is emirleri açık/kapalı data for detailed graphs(per month)
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananAylikWithServices($params);
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
     * get aftersales last is emirleri açık/kapalı data for detailed graphs(per year)
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayIsEmriAcilanKapananYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayIsEmriAcilanKapananYillikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardAracGirisSayilari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardAracGirisSayilari($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilari($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilari($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAracGirisSayilariYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAracGirisSayilariYillikWithServices($params);
    }
    
    /**
     * get aftersales downtime summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardDowntime($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardDowntime($params);
    }
    
    /**
     * get aftersales downtime summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardDowntimeWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardDowntimeWithServices($params);
    }
    
    /**
     * get aftersales downtime summary data for datagrid
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridDowntime($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGridDowntime($params);
    }
    
    /**
     * get aftersales downtime summary data for datagrid
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridDowntimeWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGridDowntimeWithServices($params);
    }
    
    /**
     * get aftersales verimlilik  summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardVerimlilik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardVerimlilik($params);
    }
    
    /**
     * get aftersales verimlilik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardVerimlilikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardVerimlilikWithServices($params);
    }
    
    /**
     * get aftersales verimlilik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayVerimlilikYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayVerimlilikYillik($params);
    }
    
    /**
     * get aftersales verimlilik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayVerimlilikYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayVerimlilikYillikWithServices($params);
    }
    
    /**
     * get aftersales kapasite  summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardKapasite($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardKapasite($params);
    }
    
    /**
     * get aftersales kapasite summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardKapasiteWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardKapasiteWithServices($params);
    }
    
    /**
     * get aftersales kapasite summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayKapasiteYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayKapasiteYillik($params);
    }
    
    /**
     * get aftersales verimlilik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayKapasiteYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayKapasiteYillikWithServices($params);
    }
    
    /**
     * get aftersales etkinlik  summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardEtkinlik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardEtkinlik($params);
    }
    
    /**
     * get aftersales etkinlik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardEtkinlikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardEtkinlikWithServices($params);
    }
    
    /**
     * get aftersales etkinlik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayEtkinlikYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayEtkinlikYillik($params);
    }
    
    /**
     * get aftersales etkinlik summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayEtkinlikYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayEtkinlikYillikWithServices($params);
    }
    
    /**
     * get aftersales yedek parca toplam satış summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardYedekParcaTS($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardYedekParcaTS($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTS($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTS($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaTSYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaTSYillikWithServices($params);
    }
    
    /**
     * get aftersales yedek parca yag satış summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardYedekParcaYS($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardYedekParcaYS($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYS($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYS($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayYedekParcaYSYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayYedekParcaYSYillikWithServices($params);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * get aftersales atolye cirosu summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardAtolyeCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardAtolyeCirosu($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosu($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayAtolyeCirosuYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayAtolyeCirosuYillikWithServices($params);
    }
    
    
    
    
    
    
   
    
    
    /**
     * get aftersales atolye cirosu summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardGarantiCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardGarantiCirosu($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosu($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosu($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuAylik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuAylik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuAylikWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGarantiCirosuYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGarantiCirosuYillikWithServices($params);
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
     * get aftersales ciro data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayCiroWithServices($params);
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
     * get aftersales ciro monthly data for detailed graphs
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroAylikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayCiroAylikWithServices($params);
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
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayCiroYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayCiroYillikWithServices($params);
    }
    
    /**
     * get aftersales CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardMMCSI($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardMMCSI($params);
    }
    
    /**
     * get aftersales CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardMMCSIWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardMMCSIWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayMMCSIYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayMMCSIYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayMMCSIYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayMMCSIYillikWithServices($params);
    }
    
    /**
     * get aftersales imüşteri memnuniyet CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridMMCSI($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGridMMCSI($params);
    }
    
    /**
     * get aftersales imüşteri memnuniyet CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridMMCSIWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGridMMCSIWithServices($params);
    }
    
    /**
     * get aftersales CXI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardMMCXI($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardMMCXI($params);
    }
    
    /**
     * get aftersales CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardMMCXIWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardMMCXIWithServices($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayMMCXIYillik($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayMMCXIYillik($params);
    }
    
    /**
     * get aftersales invoice summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayMMCXIYillikWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayMMCXIYillikWithServices($params);
    }
    
    /**
     * get aftersales imüşteri memnuniyet CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridMMCXI($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGridMMCXI($params);
    }
    
    /**
     * get aftersales imüşteri memnuniyet CSI summary data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDetayGridMMCXIWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDetayGridMMCXIWithServices($params);
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
    
    /**
     * get aftersales stok data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardStoklar($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardStoklar($params);
    }
    
    /**
     * get aftersales stok data for dashboard
     * @param array | null $params
     * @return array
     * @author Mustafa Zeynel Dağlı
     */
    public function getAfterSalesDashboardStoklarWithServices($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        return $DAL->getAfterSalesDashboardStoklarWithServices($params);
    }
    
    /**
     * Function to fill stoklar datagrid on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDetayStoklarGrid ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
        $resultSet = $DAL->getAfterSalesDetayStoklarGrid($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function to fill stoklar datagrid on user interface layer
     * @param array | null $params
     * @return array
     */
    public function getAfterSalesDetayStoklarGridWithServices ($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoAfterSalesOraclePDO');
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

}

