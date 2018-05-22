<?php

/**
 *  Framework 
 *
 * @link       
 * @copyright Copyright (c) 2017
 * @license   
 */

namespace DAL\PDO\Oracle;

/**
 * example DAL layer class for test purposes
 * @author Okan CIRAN
 */
class InfoSales extends \DAL\DalSlim {

    
     /**
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function getKamyonSales($args = array()) {
        //$today = date('d/m/Y');
        //$dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    select * from mobilkart@crm.oracle order by sira 
                    ";
             
            $statement = $pdo->prepare($sql);  
            //print_r($sql);
            
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }
    
    
     /**
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function getDealerInvoice($args = array()) {
        //$today = date('d/m/Y');
        //$dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    select count(fb.kay_id) adet,u.name
                        from fatura_bas@crm.oracle fb, users@crm.oracle u
                        where to_char(fb.fatura_tarihi,'YYYY')=to_char(sysdate,'YYYY')
                        and fb.ownergroup=u.user_id
                        group by u.name
                        order by adet desc
                    ";
             
            $statement = $pdo->prepare($sql);  
            //print_r($sql);
            
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }
    
    
    
    
     /**
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function getSalesDashboardData($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    select count(ac.activity_id) a,
                            cast( 'Bayi Aktiviteler(Bugün)' AS varchar2(300)) as  aciklama,
                            6 as controler
                             from activity@crm.oracle  ac
                             where  
                                activity_r_date between to_date('27/04/2018', 'dd/mm/yyyy') AND to_date('28/04/2018', 'dd/mm/yyyy') 
                             and product=1 
                    UNION
                    select count(opportunity_id) a,
                           cast( 'Bayi Fırsatlar' AS varchar2(300)) as  aciklama,
                            5 as controler
                           from opportunity@crm.oracle
                             where 
                         creation_date between to_date('21/04/2018', 'dd/mm/yyyy') AND to_date('28/04/2018', 'dd/mm/yyyy')
                             and product=1
                    UNION
                    select count(f.fatura_no) a,
                           cast( 'Bayi Faturalar' AS varchar2(300)) as  aciklama,
                            4 as controler
                           from fatura_bas@crm.oracle f
                            where fatura_tarihi BETWEEN to_date('01/01/2018', 'dd/mm/yyyy') AND to_date('28/04/2018', 'dd/mm/yyyy')
                    UNION
                    select  NVL(sum(Toplam_fatura),0) /*|| cast(NVL(sum(toplam_stok),0) as varchar2(300))*/ a , 
                            cast( 'MTBTR Satış' AS varchar2(300)) as  aciklama,
                            1 as controler
                            from lkw_kontrol_toplam@CRM.ORACLE
                            where product=1
                    UNION
                    select  NVL(sum(toplam_stok),0)  a , 
                            cast( 'MTBTR Stok' AS varchar2(300)) as  aciklama,
                            2 as controler
                            from lkw_kontrol_toplam@CRM.ORACLE
                            where product=1
                    UNION
                    SELECT 
                    count(stok_id) a,
                    cast( 'Bayi Stokları' AS varchar2(300)) as  aciklama,
                    3 as controler
                        FROM STOK_MASTER@crm.oracle sm,
                        users@crm.oracle u 
                        WHERE sm.bayi=u.user_id
                        and u.user_id not in (117,120,234,113,230,0,112)
                        AND sm.bayi is not null
                        and ((sm.tahsisat_durumu=4 AND sm.DURUM<>6) OR (sm.tahsisat_durumu=3 AND sm.DURUM=5)) 
                        and u.product=1 and u.aktifmi=1
                    ";
             
            $statement = $pdo->prepare($sql);  
            //print_r($sql);
            
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }
    
     /**
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function getFunnelOlcumData($args = array()) {
        //$today = date('d/m/Y');
        //$dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    select 
                            round(((select y_retail_satis_h from app_sales_funnel@crm.oracle ) /PBAA)) KGTPA,
                            round((((select y_retail_satis_h from app_sales_funnel@crm.oracle) /PBAA) /PKO)) TEGTTA,
                            round(((((select y_retail_satis_h from app_sales_funnel@crm.oracle) /PBAA) /PKO)/OTO))  YGTPA,
                            round((((((select y_retail_satis_h from app_sales_funnel@crm.oracle) /PBAA) /PKO)/OTO)/POO))TGTAA,
                            ownergroup
                        from funnel_kritik_olcum@crm.oracle
                        where PBAA<>0 and ownergroup in (0)
                    ";
             
            $statement = $pdo->prepare($sql);  
            //print_r($sql);
            
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }
    
    /**
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function getFunnelBasicData($args = array()) {
        //$today = date('d/m/Y');
        //$dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    select * from app_sales_funnel@crm.oracle 
                    where year between 2017 and 2018
                    ";
             
            $statement = $pdo->prepare($sql);  
            //print_r($sql);
            
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
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
