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
class InfoAfterSales extends \DAL\DalSlim {

    
     /**  
     * @author Mustafa Zeynel Dağlı
     * @ ddslick doldurmak için satış sonrası servisler tablosundan danısman kayıtları döndürür !!
     * @version 30/05/2018
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillServicesDdlist($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');   
            $sql ="
               /*  select SERVISID ID, 
                       ISORTAKAD AD 
                from vt_servisler where 
                    DURUMID = 1 AND 
                    dilkod = 'Turkish' 
                    order by id      */ 
                    
                   SELECT
                        vtsxy.SERVISID ID,  
                        vtsxy.GIZLIAD AD
                    FROM SASON.PERFORMANSSERVISLER vtsxy
                    WHERE vtsxy.active =0 
                    order by id


                                 ";
             $statement = $pdo->prepare( $sql);
          //  echo debugPDO($sql, $params);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {      
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }
    
    
    
    /**
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function getAfterSalesDetayAlisFaturalari($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select 
            to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') as TARIH,
                    --sum(a.toplam) toplam,
                    --TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')) as FATURATUTAR
                    --TRIM(TO_CHAR(sum(a.NETTUTAR) , '999,999,999,999,999'))
                    nvl(REPLACE(to_char(sum(a.NETTUTAR)),',',','),0) FATURATUTAR
                    /*CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                FROM faturalar a
                WHERE /*a.servisid  and*/ 
                a.ISLEMTARIHI between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                and a.faturaturid=4 
                GROUP BY to_char(a.ISLEMTARIHI, 'dd/mm/yyyy')
            ORDER BY to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') asc
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
    public function getAfterSalesDetayAlisFaturalariWeeklyWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' vv.servisid in ('.$_GET['src'].') and ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
             SELECT  vv.servisid , /*(Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */
                                   (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
            tarihicin.tar tarih ,
            nvl(data1.FATURATUTAR,0) FATURATUTAR 
             
              from vt_servisler vv  
             left join (
               select distinct 
                   to_date(x.kayittarih,'dd/mm/yyyy') tar   
               from servisisemirler x WHERE                   
                 x.kayittarih between  to_date('".$weekBefore."','dd/mm/yyyy')  and  to_date('".$today."','dd/mm/yyyy')  
                --x.kayittarih between  to_date('21/05/2018','dd/mm/yyyy')  and  to_date('28/05/2018','dd/mm/yyyy')
                                      
             ) tarihicin on 1=1
             LEFT JOIN (
             select    a.servisid ,  
                  --  to_char(a.ISLEMTARIHI, 'dd/mm/yyyy')  TARIH,
                  to_date(a.ISLEMTARIHI, 'dd/mm/yyyy') Tarih,
                  REPLACE(to_char(sum(a.NETTUTAR)),',',',') FATURATUTAR
                    /*CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                
                FROM faturalar a
                WHERE ".$servicesQuery." 
                --a.servisid in (94, 96) and 
                a.ISLEMTARIHI between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')
                --a.ISLEMTARIHI    between  to_date('21.05.2018', 'dd/mm/yyyy') AND to_date('28.05.2018', 'dd/mm/yyyy')
                and a.faturaturid=4 
                GROUP BY a.servisid, to_date(a.ISLEMTARIHI, 'dd/mm/yyyy') --  to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') 

             
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar
             WHERE 
                 -- vv.servisid not in (1,134,136)
                 ".$servicesQuery2." 
                 --vv.servisid in (94, 96) and 
                 vv.dilkod ='Turkish' 
             ORDER BY vv.servisid, tarih asc
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
    public function getAfterSalesDetayAlisFaturalariAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select * from (
                select 
                      to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                 --     to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') as TARIH,
                      to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) as tarih ,  
                        --sum(a.toplam) toplam,
                        --TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')) as FATURATUTAR
                        --TRIM(TO_CHAR(sum(a.NETTUTAR) , '999,999,999,999,999'))
                        nvl(REPLACE(to_char(sum(a.NETTUTAR)),',',','),0) FATURATUTAR
                        /*CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE /*a.servisid  and*/ 
                        a.ISLEMTARIHI between to_date('".$treeMonthsBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                    and a.faturaturid=4 
                    GROUP BY -- to_char(a.ISLEMTARIHI, 'dd/mm/yyyy'), 
                    to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) , to_number(to_char(a.ISLEMTARIHI,'yyyy'))
                --ORDER BY to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') asc
                order by yil , tarih desc
            
            ) test WHERE rownum<5
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
    public function getAfterSalesDetayAlisFaturalariAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' vv.servisid in ('.$_GET['src'].') and ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad,  */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
            tarihicin.tar as tarih ,
            tarihicin.yil as yil ,
            
            nvl(data1.FATURATUTAR,0) FATURATUTAR 
             
              from vt_servisler vv  
             left join (
               select distinct 
                   to_number(to_char(to_date(x.kayittarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.kayittarih,'yyyy')) yil  
               from servisisemirler x WHERE                   
                     to_number(to_char(to_date(x.kayittarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date('21.05.2018', 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date('21.05.2018', 'dd/mm/yyyy'), 'ww'))               
             ) tarihicin on 1=1
             LEFT JOIN (
             select    a.servisid ,  
                  to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil,                 
                  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) Tarih,
                  REPLACE(to_char(sum(a.NETTUTAR)),',',',') FATURATUTAR
                  /*CASE 
                        WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')))= 3 THEN '1'
                        ELSE TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')) END as FATURATUTAR*/ 
                
                FROM faturalar a
                WHERE ".$servicesQuery."
                a.servisid not in  (1,134,136) and 
                to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date('21.05.2018', 'dd/mm/yyyy'), 'ww'))
                AND a.faturaturid=4 
                AND to_number(to_char(a.ISLEMTARIHI,'yyyy'))  = to_number(to_char(to_date('".$today."','dd/mm/yyyy'),'yyyy')) 
                GROUP BY a.servisid,  
                 to_number(to_char(a.ISLEMTARIHI,'yyyy')),    
                   to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))
             
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and 
                 ".$servicesQuery2." 
                  vv.dilkod ='Turkish' 
             
             ORDER BY vv.servisid, tarih asc 
            ";
            //print_r($sql);
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
    public function getAfterSalesDetayAlisFaturalariYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select * FROM(
                select 
                      to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                      to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,
                 --     to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') as TARIH, 
                        --sum(a.toplam) toplam,
                        --TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')) as FATURATUTAR
                        --TRIM(TO_CHAR(sum(a.NETTUTAR) , '999,999,999,999,999'))
                        --REPLACE(to_char(sum(a.toplam)),',','') FATURATUTAR
                        sum(a.NETTUTAR) as tt,
                        nvl(REPLACE(to_char(sum(a.NETTUTAR)),',',','),0) FATURATUTAR
                        /*CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE 
                        a.ISLEMTARIHI between to_date('".$yearAgoToday."', 'dd/mm/yyyy') AND to_date('".$lastDay."', 'dd/mm/yyyy')
                    and a.faturaturid=4 
                    GROUP BY -- to_char(a.ISLEMTARIHI, 'dd/mm/yyyy'), 
                   to_number(to_char(a.ISLEMTARIHI,'MM')), to_number(to_char(a.ISLEMTARIHI,'yyyy'))
                --ORDER BY to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') asc
                order by yil ,ay asc  
            ) test2 WHERE rownum<13
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
    public function getAfterSalesDetayAlisFaturalariYillikWithServices($args = array()) {
        $today = date('d/m/Y');
        /*$date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day*/
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and a.servisid IN(94, 96)
            $servicesQuery = ' and a.servisid in ('.$_GET['src'].')  ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as ay,
                tarihicin.yil as yil,
                nvl(data1.FATURATUTAR,0) FATURATUTAR
                --TO_CHAR(sum(a.NETTUTAR)) as FATURATUTAR
              from vt_servisler vv  
             left join (
               select distinct 
                   -- to_date(x.tarih, 'dd/mm/yyyy') tar  ,
                   -- to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) week  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x WHERE                   
                  to_date(x.tarih,'dd/mm/yyyy')  between   to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')
                                                            
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select  a.servisid, 
                      to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                      to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,
                      --REPLACE('JACK and JUE','J','BL')
                      REPLACE(to_char(sum(a.toplam)),',','') FATURATUTAR
                     /* CASE 
                        WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                        ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE 
                        to_date(a.ISLEMTARIHI,'dd/mm/yyyy')  between  to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')
                        --to_number(to_char(a.ISLEMTARIHI,'yyyy'))  =  to_number(to_char(to_date('21.05.2018', 'dd/mm/yyyy'),'yyyy'))
                    and a.faturaturid=3
                    ".$servicesQuery."                   
                    GROUP BY 
                    a.servisid, 
                     to_number(to_char(a.ISLEMTARIHI,'MM')),
                    to_number(to_char(a.ISLEMTARIHI,'yyyy'))
                    
       
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish' 
             
             ORDER BY vv.servisid, yil, ay 

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
    public function getAfterSalesDetayIsemriFaturalari($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select 
            to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') AS TARIH,
            --sum(a.toplam)  as FATURATUTAR
            nvl(REPLACE(to_char(sum(a.toplam)),',',','),0) FATURATUTAR
            --TRIM(TO_CHAR(ROUND(sum(a.toplam),0), '999,999,999,999,999')) as FATURATUTAR
            /*CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                FROM faturalar a
                WHERE /*a.servisid  and*/ 
                a.ISLEMTARIHI between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                and a.faturaturid=1 
                GROUP BY to_char(a.ISLEMTARIHI, 'dd/mm/yyyy')
            ORDER BY to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') asc
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
    public function getAfterSalesDetayIsemriFaturalariWeeklyWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and a.servisid IN(94, 96)
            $servicesQuery = '  a.servisid in ('.$_GET['src'].') and ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = '  vv.servisid in ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid ,/*  (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad,  */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
            tarihicin.tar tarih ,
            nvl(data1.FATURATUTAR,0) FATURATUTAR 
             
              from vt_servisler vv  
             left join (
               select distinct 
                   to_date(x.tarih,'dd/mm/yyyy') tar   
               -- from servisisemirler x WHERE
               from sason.tarihler x where                    
                 x.tarih between  to_date('".$weekBefore."','dd/mm/yyyy')  and  to_date('".$today."','dd/mm/yyyy')  
                --x.tarih between  to_date('21/05/2018','dd/mm/yyyy')  and  to_date('28/05/2018','dd/mm/yyyy') -1
                                      
             ) tarihicin on 1=1
             LEFT JOIN (
            select   a.servisid,  
            to_date(a.ISLEMTARIHI, 'dd/mm/yyyy') AS TARIH,  
            REPLACE(to_char(sum(a.toplam)),',','') FATURATUTAR
            /*CASE 
                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                FROM faturalar a
                WHERE ".$servicesQuery."
                 a.ISLEMTARIHI between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')
                 --a.ISLEMTARIHI between   to_date('21/05/2018','dd/mm/yyyy')  and  to_date('28/05/2018','dd/mm/yyyy')
                and a.faturaturid=3 
                GROUP BY  a.servisid , to_date(a.ISLEMTARIHI, 'dd/mm/yyyy') 
             
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar
             WHERE 
                 -- vv.servisid not in (1,134,136)      
                 ".$servicesQuery2."
                  vv.dilkod ='Turkish' 
             
             ORDER BY vv.servisid, tarih asc 
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
    public function getAfterSalesDetayIsemriFaturalariAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select * from (
                select  
                -- to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') AS TARIH,
                  to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) tarih ,
                --sum(a.toplam)  as FATURATUTAR
                --TRIM(TO_CHAR(ROUND(sum(a.toplam),0), '999,999,999,999,999')) as FATURATUTAR
                nvl(REPLACE(to_char(sum(a.toplam)),',',','),0) FATURATUTAR
                /*CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE /*a.servisid  and*/ 
                    a.ISLEMTARIHI between to_date('".$treeMonthsBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                    and a.faturaturid=1 
                    GROUP BY  to_number(to_char(a.ISLEMTARIHI,'yyyy')) ,to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))
                ORDER BY yil desc , tarih desc 
             
            ) test WHERE rownum<5
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
    public function getAfterSalesDetayIsemriFaturalariAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        //$dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //$treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = " 
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.FATURATUTAR,0) FATURATUTAR
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE                   
                
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww'))
                      and to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'),'yyyy'))                                      
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select   a.servisid,  
                  to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) tarih ,
                  REPLACE(to_char(sum(a.toplam)),',',',') FATURATUTAR
                /*CASE 
                    WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                    ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a 
                WHERE ".$servicesQuery."
                a.servisid not in  (1,134,136) and 
                to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww'))
                AND a.faturaturid=1 
                AND to_number(to_char(a.ISLEMTARIHI,'yyyy'))  = to_number(to_char(to_date('".$today."','dd/mm/yyyy'),'yyyy'))  
                GROUP BY a.servisid,  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))
       
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish' 
             
             ORDER BY vv.servisid, tarih asc
            ";
            //print_r($sql);
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
    public function getAfterSalesDetayIsemriFaturalariYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select * FROM(
                select  
                -- to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') AS TARIH,
                  to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                  to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,

                --sum(a.toplam)  as FATURATUTAR
                --TRIM(TO_CHAR(ROUND(sum(a.toplam),0), '999,999,999,999,999')) as FATURATUTAR
                nvl(REPLACE(to_char(sum(a.toplam)),',',','),0) FATURATUTAR
                /*CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE /*a.servisid  and*/ 
                    a.ISLEMTARIHI between to_date('".$yearAgoToday."', 'dd/mm/yyyy') AND to_date('".$lastDay."', 'dd/mm/yyyy')
                    and a.faturaturid=1 
                    GROUP BY  to_number(to_char(a.ISLEMTARIHI,'yyyy')) , to_number(to_char(a.ISLEMTARIHI,'MM')) 
                ORDER BY yil asc , ay asc
            ) test2 WHERE rownum<13
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
    public function getAfterSalesDetayIsemriFaturalariYillikWithServices($args = array()) {
        //$today = date('d/m/Y');
        /*$date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day*/
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and a.servisid IN(94, 96)
            $servicesQuery = ' and a.servisid in ('.$_GET['src'].')  ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as ay,
                tarihicin.yil as yil,            
                nvl(data1.FATURATUTAR,0) FATURATUTAR
              from vt_servisler vv  
             left join (
               select distinct 
                   -- to_date(x.tarih, 'dd/mm/yyyy') tar  ,
                   -- to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) week  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x WHERE    
                     to_date(x.tarih,'dd/mm/yyyy')  between   to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')                                     
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select  a.servisid, 
                      to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                      to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,
                      REPLACE(to_char(sum(a.toplam)),',',',') FATURATUTAR
                      /*CASE 
                        WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                        ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE 
                        to_date(a.ISLEMTARIHI,'dd/mm/yyyy')  between  to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')
                    and a.faturaturid=1
                    ".$servicesQuery."                   
                    GROUP BY 
                    a.servisid,to_number(to_char(a.ISLEMTARIHI,'yyyy')),
                     to_number(to_char(a.ISLEMTARIHI,'MM')) 
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish' 
             ORDER BY vv.servisid, yil, ay   
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
    public function getAfterSalesDetaySatisFaturalari($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
             select 
                    to_char(a.islemtarihi, 'dd/mm/yyyy') as TARIH,
                    --sum(a.toplam) FATURATUTAR
                    --TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) as FATURATUTAR
                    nvl(REPLACE(to_char(sum(a.toplam)),',',','),0) FATURATUTAR
                    /*CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                FROM faturalar a
                WHERE 
                a.islemtarihi between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                and a.faturaturid=3 
                GROUP BY to_char(a.islemtarihi, 'dd/mm/yyyy')
                ORDER BY to_char(a.islemtarihi, 'dd/mm/yyyy') asc
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
    public function getAfterSalesDetaySatisFaturalariWeeklyWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and a.servisid IN(94, 96)
            $servicesQuery = '  a.servisid in ('.$_GET['src'].') and ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = '  vv.servisid in ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad,  */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
            tarihicin.tar tarih ,
            nvl(data1.FATURATUTAR,0) FATURATUTAR 
             
              from vt_servisler vv  
             left join (
               select distinct 
                   to_date(x.tarih,'dd/mm/yyyy') tar   
               -- from servisisemirler x WHERE
               from sason.tarihler x where                    
                 x.tarih between  to_date('".$weekBefore."','dd/mm/yyyy')  and  to_date('".$today."','dd/mm/yyyy')  
                --x.tarih between  to_date('21/05/2018','dd/mm/yyyy')  and  to_date('28/05/2018','dd/mm/yyyy') -1
                                      
             ) tarihicin on 1=1
             LEFT JOIN (
            select   a.servisid,  
            to_date(a.ISLEMTARIHI, 'dd/mm/yyyy') AS TARIH,  
            REPLACE(to_char(sum(a.toplam)),',','') FATURATUTAR
            /*CASE 
                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                FROM faturalar a
                WHERE ".$servicesQuery."
                 a.ISLEMTARIHI between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')
                 --a.ISLEMTARIHI between   to_date('21/05/2018','dd/mm/yyyy')  and  to_date('28/05/2018','dd/mm/yyyy')
                and a.faturaturid=3 
                GROUP BY  a.servisid , to_date(a.ISLEMTARIHI, 'dd/mm/yyyy') 
             
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar
             WHERE 
                 -- vv.servisid not in (1,134,136)      
                 ".$servicesQuery2."
                  vv.dilkod ='Turkish' 
             
             ORDER BY vv.servisid, tarih asc 
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
    public function getAfterSalesDetaySatisFaturalariAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select * from (
                select 
                    to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                    to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) tarih ,
                       --sum(a.toplam) FATURATUTAR
                       --TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) as FATURATUTAR
                       nvl(REPLACE(to_char(sum(a.toplam)),',',','),0) FATURATUTAR
                       /*CASE 
                               WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                               ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                   FROM faturalar a
                   WHERE 
                   a.islemtarihi between to_date('".$treeMonthsBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                   and a.faturaturid=3 
                   GROUP BY  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))    
                   ORDER BY yil desc , tarih desc 
              ) test WHERE rownum<5
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
    public function getAfterSalesDetaySatisFaturalariAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        //$dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //$treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid , /*  (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                      (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.FATURATUTAR,0) FATURATUTAR
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE                   
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww'))
                      and to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'),'yyyy'))                                      
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select   a.servisid,  
                  to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) tarih ,
                  REPLACE(to_char(sum(a.toplam)),',',',') FATURATUTAR
                /*CASE 
                    WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                    ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a 
                WHERE ".$servicesQuery."
                a.servisid not in  (1,134,136) and 
                to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww'))
                AND a.faturaturid=3 
                AND to_number(to_char(a.ISLEMTARIHI,'yyyy'))  = to_number(to_char(to_date('".$today."','dd/mm/yyyy'),'yyyy'))  
                GROUP BY a.servisid,  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))
       
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish' 
             ORDER BY vv.servisid, tarih asc
            ";
            //print_r($sql);
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
    public function getAfterSalesDetaySatisFaturalariYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            Select * FROM(
                select 
                      to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                      to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,
                       --sum(a.toplam) FATURATUTAR
                       --TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) as FATURATUTAR
                       nvl(REPLACE(to_char(sum(a.toplam)),',',','),0) FATURATUTAR
                       /*CASE 
                               WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                               ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                   FROM faturalar a
                   WHERE 
                   a.islemtarihi between to_date('".$yearAgoToday."', 'dd/mm/yyyy') AND to_date('".$lastDay."', 'dd/mm/yyyy')
                   and a.faturaturid=3 
                   GROUP BY  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(a.ISLEMTARIHI,'MM'))   
                   ORDER BY yil asc , ay asc
            ) test2 WHERE rownum<13
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
    public function getAfterSalesDetaySatisFaturalariYillikWithServices($args = array()) {
        //$today = date('d/m/Y');
        /*$date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day*/
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and a.servisid IN(94, 96)
            $servicesQuery = ' and a.servisid in ('.$_GET['src'].')  ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                     (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as ay,
                tarihicin.yil as yil,            
                nvl(data1.FATURATUTAR,0) FATURATUTAR
              from vt_servisler vv  
             left join (
               select distinct 
                   -- to_date(x.tarih, 'dd/mm/yyyy') tar  ,
                   -- to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) week  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x WHERE                   
                    to_date(x.tarih,'dd/mm/yyyy')  between   to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')                                     
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select  a.servisid, 
                      to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                      to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,
                      REPLACE(to_char(sum(a.toplam)),',',',') FATURATUTAR
                      /*CASE 
                        WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                        ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE 
                        to_date(a.ISLEMTARIHI,'dd/mm/yyyy')  between  to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')
                    and a.faturaturid=3
                    ".$servicesQuery."                    
                    GROUP BY 
                    a.servisid, 
                     to_number(to_char(a.ISLEMTARIHI,'MM')),
                    to_number(to_char(a.ISLEMTARIHI,'yyyy'))
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish' 
             ORDER BY vv.servisid, yil, ay
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
    public function getAfterSalesDetayIcmalFaturalari($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select 
            to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') as TARIH,
            --sum(a.toplam) FATURATUTAR
            --TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) as FATURATUTAR
            nvl(REPLACE(to_char(sum(a.toplam)),',',''),0) FATURATUTAR
            /*CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                FROM faturalar a
                WHERE /*a.servisid  and*/ 
                a.ISLEMTARIHI between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                and a.faturaturid=2
                GROUP BY to_char(a.ISLEMTARIHI, 'dd/mm/yyyy')
                ORDER BY to_char(a.ISLEMTARIHI, 'dd/mm/yyyy') asc
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
    public function getAfterSalesDetayIcmalFaturalariWeeklyWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and a.servisid IN(94, 96)
            $servicesQuery = '  a.servisid in ('.$_GET['src'].') and ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and  vv.servisid in ('.$_GET['src'].')   ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = " 
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad,  */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
            tarihicin.tar tarih ,
            nvl(data1.FATURATUTAR,0) FATURATUTAR  
              from vt_servisler vv  
             left join (
               select distinct 
                   to_date(x.tarih,'dd/mm/yyyy') tar   
               -- from servisisemirler x WHERE
               from sason.tarihler x where                    
                 x.tarih between  to_date('".$weekBefore."','dd/mm/yyyy')  and  to_date('".$today."','dd/mm/yyyy')  
                 --x.tarih between  to_date('21/05/2018','dd/mm/yyyy')  and  to_date('28/05/2018','dd/mm/yyyy') -1
                                      
             ) tarihicin on 1=1
             LEFT JOIN (
            select   a.servisid,  
            to_date(a.ISLEMTARIHI, 'dd/mm/yyyy') AS TARIH,
            REPLACE(to_char(sum(a.toplam)),',','') FATURATUTAR
            /*CASE 
                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                FROM faturalar a
                WHERE ".$servicesQuery."
                 a.ISLEMTARIHI between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')
                 --a.ISLEMTARIHI between   to_date('21/05/2018','dd/mm/yyyy')  and  to_date('28/05/2018','dd/mm/yyyy')
                and a.faturaturid=2 
                GROUP BY  a.servisid , to_date(a.ISLEMTARIHI, 'dd/mm/yyyy') 
             
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar
             WHERE 
                  vv.servisid not in (1,134,136)      
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish' 
             ORDER BY vv.servisid, tarih asc 
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
    public function getAfterSalesDetayIcmalFaturalariAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
             select * from (
                select 
                   to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                   to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) tarih ,
                --sum(a.toplam) FATURATUTAR
                --TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) as FATURATUTAR
                nvl(REPLACE(to_char(sum(a.toplam)),',',''),0) FATURATUTAR
                /*CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE /*a.servisid  and*/ 
                    a.ISLEMTARIHI between to_date('".$treeMonthsBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                    and a.faturaturid=2
                    GROUP BY  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))    
                    ORDER BY yil desc , tarih desc
                ) test WHERE rownum<5
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
    public function getAfterSalesDetayIcmalFaturalariAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        //$dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //$treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.FATURATUTAR,0) FATURATUTAR
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE                   
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww'))
                      and to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'),'yyyy'))                                      
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select   a.servisid,  
                  to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) tarih ,
                  REPLACE(to_char(sum(a.toplam)),',',',') FATURATUTAR
                /*CASE 
                    WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                    ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a 
                WHERE ".$servicesQuery."
                a.servisid not in  (1,134,136) and 
                to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'), 'ww'))
                AND a.faturaturid=3 
                AND to_number(to_char(a.ISLEMTARIHI,'yyyy'))  = to_number(to_char(to_date('".$today."','dd/mm/yyyy'),'yyyy'))  
                GROUP BY a.servisid,  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))
       
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish' 
             ORDER BY vv.servisid, tarih asc
            ";
            //print_r($sql);
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
    public function getAfterSalesDetayIcmalFaturalariYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            Select * From(
                select 
                    to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                       to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,
                --sum(a.toplam) FATURATUTAR
                --TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) as FATURATUTAR
                nvl(REPLACE(to_char(sum(a.toplam)),',',''),0) FATURATUTAR
                /*CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE /*a.servisid  and*/ 
                    a.ISLEMTARIHI between to_date('".$yearAgoToday."', 'dd/mm/yyyy') AND to_date('".$lastDay."', 'dd/mm/yyyy')
                    and a.faturaturid=2
                     GROUP BY  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(a.ISLEMTARIHI,'MM'))   
                    ORDER BY yil asc , ay asc
            ) test2 WHERE rownum<13
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
    public function getAfterSalesDetayIcmalFaturalariYillikWithServices($args = array()) {
        //$today = date('d/m/Y');
        /*$date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day*/
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and a.servisid IN(94, 96)
            $servicesQuery = ' and a.servisid in ('.$_GET['src'].')  ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                
            SELECT  vv.servisid , /*  (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                     (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as ay,
                tarihicin.yil as yil,            
                nvl(data1.FATURATUTAR,0) FATURATUTAR
              from vt_servisler vv  
             left join (
               select distinct 
                   -- to_date(x.tarih, 'dd/mm/yyyy') tar  ,
                   -- to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) week  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x WHERE
                     to_date(x.tarih,'dd/mm/yyyy')  between   to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')                                     
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select  a.servisid, 
                      to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                      to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,
                      REPLACE(to_char(sum(a.toplam)),',',',') FATURATUTAR
                      /*CASE 
                        WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                        ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR*/
                    FROM faturalar a
                    WHERE 
                        to_date(a.ISLEMTARIHI,'dd/mm/yyyy')  between  to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')                        
                    and a.faturaturid=2
                    ".$servicesQuery."                     
                    GROUP BY 
                    a.servisid, 
                     to_number(to_char(a.ISLEMTARIHI,'MM')),
                    to_number(to_char(a.ISLEMTARIHI,'yyyy'))
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish' 
             ORDER BY vv.servisid, yil, ay  
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
    public function getAfterSalesDetayIsEmriAcikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //si.servisid in (94) and 
            $servicesQuery = ' si.servisid in ('.$_GET['src'].') and ';
            // and vv.servisid IN (94, 96, 113)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT   vv.servisid ,  /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad,  */ 
                                       (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
            tarihicin.tar tarih ,
            nvl(data1.OGUN_KAPATILMAYAN_EMIRLER,0) OGUN_KAPATILMAYAN_EMIRLER  
              from vt_servisler vv  
             left join (
               select distinct 
                   to_date(x.tarih,'dd/mm/yyyy') tar 
               from sason.tarihler x where   
                     x.tarih between  to_date('".$weekBefore."','dd/mm/yyyy')  and  to_date('".$today."','dd/mm/yyyy')  
             ) tarihicin on 1=1
             LEFT JOIN (
                select  
                    distinct si.servisid,  
                    to_date(si.kayittarih, 'dd/mm/yyyy') AS TARIH,
                    count(si.id) as OGUN_KAPATILMAYAN_EMIRLER
                from servisisemirler  si
                 where to_date(SI.KAYITTARIH,'dd/mm/yyyy') = to_date(si.KAYITTARIH,'dd/mm/yyyy') AND 
                       si.durumid =1 AND  
                        si.teknikolaraktamamla = 0 and si.tamamlanmatarih is null AND
                       ".$servicesQuery."
                       si.kayittarih between to_date('".$weekBefore."','dd/mm/yyyy')  and  to_date('".$today."','dd/mm/yyyy')
                group by servisid, to_date(si.kayittarih, 'dd/mm/yyyy')
                )  data1 on data1.servisid = vv.servisid 
                and data1.TARIH = tarihicin.tar
             WHERE 
                 vv.servisid not in (1,134,136)      
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish' 
             ORDER BY vv.servisid, tarih asc  
            ";
            $statement = $pdo->prepare($sql);  
           // print_r($sql);
            
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
    public function getAfterSalesDetayIsEmriAcikWithoutServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                select to_date(tarihicin.tar, 'dd/mm/yyyy') as TARIH,
                count(z.servisid) as MIKTAR from  servisisemirler  z 
                left join(
                    select distinct 
                           to_date(x.kayittarih,'dd/mm/yyyy') tar   
                       from servisisemirler x 
                       WHERE                   
                         x.kayittarih between  to_date('".$weekBefore."','dd/mm/yyyy')  and  to_date('".$dayAfter."','dd/mm/yyyy')  
                            -- x.kayittarih between  to_date('21/05/2018','dd/mm/yyyy')  and  to_date('30/05/2018','dd/mm/yyyy')   
                             --and x.servisid =  129                       
                     ) tarihicin on 1=1
                where
                        /*to_date(z.kayittarih,'dd/mm/yyyy') <> to_date(z.tamamlanmatarih,'dd/mm/yyyy') AND */ 
                        to_date(z.kayittarih,'dd/mm/yyyy') <= to_date(tarihicin.tar,'dd/mm/yyyy') AND  
                        z.teknikolaraktamamla = 0 and z.tamamlanmatarih is null 
                        --and z.servisid = vv.servisid
                        GROUP BY to_date(tarihicin.tar, 'dd/mm/yyyy')
                        ORDER BY to_date(tarihicin.tar, 'dd/mm/yyyy') asc

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
    public function getAfterSalesDetayIsEmriAcikAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "   
            SELECT   
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.MIKTAR,0) MIKTAR
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE     
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww')) AND 
                     to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'),'yyyy'))                                      
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select  
                  to_number(to_char(a.kayittarih,'yyyy')) yil,
                  to_number(to_char(to_date(a.kayittarih, 'dd/mm/yyyy'), 'ww')) tarih,  
                count(a.servisid) as MIKTAR
                FROM servisisemirler a
                WHERE  
                  to_number(to_char(to_date(a.kayittarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww')) AND  
                  a.teknikolaraktamamla = 0 and a.tamamlanmatarih is null 
                GROUP BY  to_number(to_char(a.kayittarih,'yyyy')), to_number(to_char(to_date(a.kayittarih, 'dd/mm/yyyy'), 'ww'))
             ) data1 on data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid   in (1) and
                 vv.dilkod ='Turkish'  
             ORDER BY  tarih desc
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
    public function getAfterSalesDetayIsEmriAcikAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        //$dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //$treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and  
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and ';
            // and vv.servisid in (94, 96)
            $servicesQuery2 = ' and vv.servisid in ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid , /*  (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                      (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.MIKTAR,0) MIKTAR
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE     
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww'))
                      and to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date('".$today."', 'dd/mm/yyyy'),'yyyy'))                                      
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select   
                  a.servisid,  
                  to_number(to_char(a.kayittarih,'yyyy')) yil,
                  to_number(to_char(to_date(a.kayittarih, 'dd/mm/yyyy'), 'ww')) tarih,  
                count(a.servisid) as MIKTAR
                FROM servisisemirler a
                WHERE 
                  ".$servicesQuery."
                  to_number(to_char(to_date(a.kayittarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww')) AND  
                  a.teknikolaraktamamla = 0 and a.tamamlanmatarih is null 
                GROUP BY  a.servisid ,  to_number(to_char(a.kayittarih,'yyyy')), to_number(to_char(to_date(a.kayittarih, 'dd/mm/yyyy'), 'ww'))
       
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
             ORDER BY vv.servisid, tarih asc
            ";
            //print_r($sql);
            $statement = $pdo->prepare($sql);  
            
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
    public function getAfterSalesDetayIsEmriAcikYillik($args = array()) {
        //$today = date('d/m/Y');
        /*$date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day*/

        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
            SELECT  
                tarihicin.ay as ay,
                tarihicin.yil as yil,            
                nvl(data1.MIKTAR,0) oay_kapatilmayan_emirler  
              from vt_servisler vv  
             left join (
               select distinct  
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x WHERE
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
             ) tarihicin on 1=1
             LEFT JOIN (
                select    
                    to_number(to_char(a.kayittarih,'yyyy')) yil ,
                    to_number(to_char(a.kayittarih,'MM')) ay ,
                    count( a.servisid) as MIKTAR
                FROM servisisemirler a
                WHERE  
                 a.kayittarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                 AND a.teknikolaraktamamla = 0 and a.tamamlanmatarih is null 
                GROUP BY   to_number(to_char(a.kayittarih,'yyyy')), to_number(to_char(a.kayittarih,'MM')) 
             ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid in (1) and  
                 vv.dilkod ='Turkish'  
             ORDER BY yil, ay  
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
    public function getAfterSalesDetayIsEmriAcikYillikWithServices($args = array()) {
        //$today = date('d/m/Y');
        /*$date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day*/
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // a.servisid IN(94, 96) and
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // and vv.servisid in (94) 
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = " 
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as ay,
                tarihicin.yil as yil,            
                nvl(data1.MIKTAR,0) oay_kapatilmayan_emirler  
              from vt_servisler vv  
             left join (
               select distinct  
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x WHERE
                 --    to_date(x.tarih,'dd/mm/yyyy')  between   to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy')
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                            
             ) tarihicin on 1=1
             LEFT JOIN (
                select   
                    a.servisid,  
                    to_number(to_char(a.kayittarih,'yyyy')) yil ,
                    to_number(to_char(a.kayittarih,'MM')) ay ,
                    count( a.servisid) as MIKTAR
                FROM servisisemirler a
                WHERE 
                ".$servicesQuery."  
                  a.kayittarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                 AND a.teknikolaraktamamla = 0 and a.tamamlanmatarih is null 
                GROUP BY  a.servisid , to_number(to_char(a.kayittarih,'yyyy')), to_number(to_char(a.kayittarih,'MM')) 
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
             ORDER BY vv.servisid, yil, ay     
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
    public function getAfterSalesDetayIsEmriAcilanKapanan($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select  null as kapanan_is_emri,
                     count(si.id) as acilan_is_emri, 
                     to_char(si.KAYITTARIH,'dd/mm/yyyy') as tarih
                       from servisisemirler  si
                       where   SI.KAYITTARIH   BETWEEN to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                       group by  to_char(SI.KAYITTARIH,'dd/mm/yyyy')
             UNION        
             select  count(si.id) as kapanan_is_emri,
                   null acilan_is_emri, 
                   to_char(SI.TAMAMLANMATARIH,'dd/mm/yyyy') as tarih
                       from servisisemirler  si
                       where  si.teknikolaraktamamla = 1
                       and SI.TAMAMLANMATARIH   BETWEEN to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                       group by  to_char(SI.TAMAMLANMATARIH,'dd/mm/yyyy')
                       order by tarih asc
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
    public function getAfterSalesDetayIsEmriAcilanKapananWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // a.servisid in (94, 96, 98) and
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // and vv.servisid in (94, 96, 98)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad,  */ 
                                     (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.tar tarih ,
                nvl(data1.acilan_is_emri,0) acilan_is_emri ,
                nvl(data1.kapanan_is_emri,0) kapanan_is_emri 
                  from vt_servisler vv  
                 left join (
                   select distinct 
                       to_date(x.tarih,'dd/mm/yyyy') tar 
                   from sason.tarihler x where   
                         x.tarih between  to_date(to_char(sysdate, 'dd/mm/yyyy'),'dd/mm/yyyy')-7  and 
                         to_date(to_char(sysdate, 'dd/mm/yyyy'),'dd/mm/yyyy') -1 
                 ) tarihicin on 1=1
                 LEFT JOIN (
                    select  distinct a.servisid,  
                        to_date(a.kayittarih, 'dd/mm/yyyy') AS TARIH,
                        (select  
                         count(si.id) as acilan_is_emri
                           from servisisemirler  si
                           where  to_date(SI.KAYITTARIH,'dd/mm/yyyy') = to_date(a.KAYITTARIH,'dd/mm/yyyy') AND 
                           si.durumid =1 AND 
                           si.servisid = a.servisid
                            ) acilan_is_emri , 
                          ( select  count(sid.id)                        
                                from servisisemirler  sid
                               where  sid.teknikolaraktamamla = 1 AND  
                                    sid.durumid =1 AND 
                                    sid.servisid = a.servisid AND 
                                    to_date(SID.TAMAMLANMATARIH,'dd/mm/yyyy')   =  to_date(a.KAYITTARIH,'dd/mm/yyyy') ) kapanan_is_emri
                    FROM servisisemirler a
                    WHERE 
                    --a.servisid in (94, 96, 98) and 
                    ".$servicesQuery."
                     a.kayittarih between   to_date(to_char(sysdate, 'dd/mm/yyyy'),'dd/mm/yyyy')-7  and  to_date(to_char(sysdate, 'dd/mm/yyyy'),'dd/mm/yyyy')

                    ) 

                  data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar
            WHERE 
                vv.servisid not in (1,134,136)      
                --and vv.servisid in (94, 96, 98) 
                ".$servicesQuery2."
                and vv.dilkod ='Turkish' 

             ORDER BY vv.servisid, tarih desc
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
    public function getAfterSalesDetayIsEmriAcilanKapananAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
        select * from (
                select 
                   yil,
                   sum(kapanan_is_emri) kapanan_is_emri,
                   sum(acilan_is_emri) acilan_is_emri,
                   tarih       
                 from ( 
                  select asd.yil,asd.ay,
                   sum(nvl(kapanan_is_emri,0)) as kapanan_is_emri,
                   sum(nvl(acilan_is_emri,0)) as acilan_is_emri,
                   tarih
                   from (
                  select  null as kapanan_is_emri,
                         to_number(to_char(si.KAYITTARIH,'yyyy')) yil ,
                         to_number(to_char(si.KAYITTARIH,'mm')) ay,
                         to_char(to_date(si.KAYITTARIH, 'dd/mm/yyyy'), 'ww') as tarih ,  
                         count(si.id) as acilan_is_emri 
                              --to_date (to_char(si.KAYITTARIH,'dd/mm/yyyy'))  as tarih
                          from servisisemirler si
                          where SI.KAYITTARIH BETWEEN to_date('".$treeMonthsBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                          group by to_number(to_char(si.KAYITTARIH,'yyyy')) ,
                          to_number(to_char(si.KAYITTARIH,'mm')),  
                          to_char(to_date(si.KAYITTARIH, 'dd/mm/yyyy'), 'ww') 
                    UNION        
                    select  count(si.id) as kapanan_is_emri,
                            to_number(to_char(SI.TAMAMLANMATARIH,'yyyy')) yil ,
                            to_number(to_char(SI.TAMAMLANMATARIH,'mm')) ay,
                            to_char(to_date(si.TAMAMLANMATARIH, 'dd/mm/yyyy'), 'ww') as tarih ,
                            null acilan_is_emri
                            --  to_date(to_char(SI.TAMAMLANMATARIH,'dd/mm/yyyy')) as tarih
                            from servisisemirler  si
                            where  si.teknikolaraktamamla = 1 
                            and SI.TAMAMLANMATARIH   BETWEEN to_date('".$treeMonthsBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                            group by  to_number(to_char(SI.TAMAMLANMATARIH,'yyyy')) , 
                            to_number(to_char(SI.TAMAMLANMATARIH,'mm')), 
                            to_char(to_date(si.TAMAMLANMATARIH, 'dd/mm/yyyy'), 'ww') 
                            ) asd 
                  group by acilan_is_emri,kapanan_is_emri , yil,ay,tarih
                    ) ddd     
                    group by yil , tarih
                  order by yil desc ,  tarih desc

                  ) test WHERE rownum<5
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
    public function getAfterSalesDetayIsEmriAcilanKapananAylikWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // a.servisid in (94,96) and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // and vv.servisid in (94,96) 
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT  vv.servisid , /*  (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                      (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.acilan_is_emri,0) acilan_is_emri ,
                nvl(data1.kapanan_is_emri,0) kapanan_is_emri 
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE     
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww'))
                      and to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))                                      
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select  distinct  a.servisid,  
                  to_number(to_char(a.kayittarih,'yyyy')) yil ,
                  to_number(to_char(to_date(a.kayittarih, 'dd/mm/yyyy'), 'ww')) tarih ,
                  
                    (select  
                     count(si.id) as acilan_is_emri
                       from servisisemirler  si
                       where 
                            to_number(to_char(to_date(SI.KAYITTARIH, 'dd/mm/yyyy'), 'ww'))  = to_number(to_char(to_date(a.KAYITTARIH, 'dd/mm/yyyy'), 'ww')) AND 
                            to_number(to_char(SI.KAYITTARIH,'yyyy'))  = to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'),'dd/mm/yyyy'),'yyyy')) AND
                            si.durumid =1 AND 
                            si.servisid = a.servisid
                        ) acilan_is_emri , 
                      ( select  count(sid.id)                        
                            from servisisemirler  sid
                           where  sid.teknikolaraktamamla = 1 AND  
                                sid.durumid =1 AND 
                                sid.servisid = a.servisid AND  
                                to_number(to_char(to_date(SID.TAMAMLANMATARIH, 'dd/mm/yyyy'), 'ww'))  =  to_number(to_char(to_date(a.KAYITTARIH, 'dd/mm/yyyy'), 'ww')) AND
                                to_number(to_char(SID.TAMAMLANMATARIH,'yyyy'))  = to_number(to_char(to_date(a.KAYITTARIH,'dd/mm/yyyy'),'yyyy'))  
                                 ) kapanan_is_emri
                FROM servisisemirler a
                WHERE 
                --a.servisid in (94,96) and 
                ".$servicesQuery."
                 to_number(to_char(to_date(a.kayittarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) AND
                 to_number(to_char(a.kayittarih,'yyyy'))  = to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'),'dd/mm/yyyy'),'yyyy'))  
             
       
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 --and vv.servisid in (94,96)
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish'  
             ORDER BY vv.servisid, yil, tarih desc
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
    public function getAfterSalesDetayIsEmriAcilanKapananYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select * from (
                select asd.yil,asd.ay,
                           sum(nvl(kapanan_is_emri,0)) as kapanan_is_emri,
                           sum(nvl(acilan_is_emri,0)) as acilan_is_emri  
                           from ( 
                          select  null as kapanan_is_emri,
                                      to_number(to_char(si.KAYITTARIH,'yyyy')) yil ,
                                      to_number(to_char(si.KAYITTARIH,'mm')) ay,
                                      --null as ay,
                                      count(si.id) as acilan_is_emri 
                                      --to_date (to_char(si.KAYITTARIH,'dd/mm/yyyy'))  as tarih
                                      from servisisemirler  si
                                      where   SI.KAYITTARIH   BETWEEN to_date('".$yearAgoToday."', 'dd/mm/yyyy') AND to_date('".$lastDay."', 'dd/mm/yyyy')
                                      group by    to_number(to_char(si.KAYITTARIH,'yyyy')) ,to_number(to_char(si.KAYITTARIH,'mm'))
                            UNION        
                            select  count(si.id) as kapanan_is_emri,
                                      to_number(to_char(SI.TAMAMLANMATARIH,'yyyy')) yil ,
                                      to_number(to_char(SI.TAMAMLANMATARIH,'mm')) ay,
                                      null acilan_is_emri
                                    --  to_date(to_char(SI.TAMAMLANMATARIH,'dd/mm/yyyy')) as tarih
                                      from servisisemirler  si
                                      where  si.teknikolaraktamamla = 1
                                      and SI.TAMAMLANMATARIH   BETWEEN to_date('".$yearAgoToday."', 'dd/mm/yyyy') AND to_date('".$lastDay."', 'dd/mm/yyyy')
                                      group by  to_number(to_char(SI.TAMAMLANMATARIH,'yyyy')) , to_number(to_char(SI.TAMAMLANMATARIH,'mm'))
                                      ) asd 
                          group by acilan_is_emri,kapanan_is_emri , yil,ay
                          order by yil desc ,ay desc 
          )  test2 
          WHERE rownum<25
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
    public function getAfterSalesDetayIsEmriAcilanKapananYillikWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        $servicesQuery3 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // a.servisid in (94,96) and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // and vv.servisid in (94,96) 
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
            // and si.servisid IN (94,96,98) 
            $servicesQuery3 = ' and si.servisid in  ('.$_GET['src'].')  ';
        } 
        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT  vv.servisid ,  /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                      (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad,    
                tarihicin.ay as ay,
                tarihicin.yil as yil,            
                tarihicin.ay as tarih, 
                nvl(data1.acilan_is_emri,0) acilan_is_emri ,
                nvl(data1.kapanan_is_emri,0) kapanan_is_emri 
              from vt_servisler vv  
             left join (
               select distinct
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x WHERE
                     to_date(x.tarih,'dd/mm/yyyy')  between   to_date(to_date(sysdate, 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(sysdate, 'dd/mm/yyyy') 
             ) tarihicin on 1=1
             LEFT JOIN ( 
             
             select servisid ,   yil, ay, sum(kapanan_is_emri) kapanan_is_emri,  sum (acilan_is_emri) acilan_is_emri from (
                select  asd.servisid ,   asd.yil,asd.ay,
                           sum(nvl(kapanan_is_emri,0)) as kapanan_is_emri,
                           sum(nvl(acilan_is_emri,0)) as acilan_is_emri  
                           from ( 
                          select      si.servisid ,  
                                      null as kapanan_is_emri,
                                      to_number(to_char(si.KAYITTARIH,'yyyy')) yil ,
                                      to_number(to_char(si.KAYITTARIH,'mm')) ay, 
                                      count(si.id) as acilan_is_emri  
                                      from servisisemirler  si
                                      where   SI.KAYITTARIH   BETWEEN to_date(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                      --and si.servisid IN (94, 96, 98)
                                      ".$servicesQuery3."
                                      group by   si.servisid ,    to_number(to_char(si.KAYITTARIH,'yyyy')) ,to_number(to_char(si.KAYITTARIH,'mm'))
                            UNION        
                            select     si.servisid ,  
                                      count(si.id) as kapanan_is_emri,
                                      to_number(to_char(SI.TAMAMLANMATARIH,'yyyy')) yil ,
                                      to_number(to_char(SI.TAMAMLANMATARIH,'mm')) ay,
                                      null acilan_is_emri 
                                      from servisisemirler  si
                                      where  si.teknikolaraktamamla = 1 
                                      --and si.servisid IN (94,96,98) 
                                       ".$servicesQuery3."
                                      and SI.TAMAMLANMATARIH   BETWEEN to_date(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') -365 , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                      group by  si.servisid ,   to_number(to_char(SI.TAMAMLANMATARIH,'yyyy')) , to_number(to_char(SI.TAMAMLANMATARIH,'mm'))
                                      ) asd 
                          group by  servisid ,  acilan_is_emri,kapanan_is_emri , yil,ay
                       
          )  test2 
          group by servisid ,  yil, ay  
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 --and vv.servisid in (94,96,98) 
                 ".$servicesQuery2."
                 and vv.dilkod ='Turkish'  
             ORDER BY vv.servisid, yil, ay  
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
    public function getAfterSalesDashboardStoklar($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        //print_r($dayBefore);
        //print_r($dayBeforeTwo);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                SELECT
                    sum(asd.stoktutar)  A,
                    'Inventory' ACIKLAMA
                    from (
                   SELECT
                        p.HSERVISID SERVISID,
                        p.stokmiktar * p.ortalamamaliyet stoktutar              
                    FROM(SELECT servisstokturid,
                               a.id,
                               a.servisid hservisid, 
                               C.STOKMIKTAR, 
                               kurlar_pkg.ORTALAMAMALIYET(a.id) ortalamamaliyet
                          FROM(SELECT DISTINCT servisstokid
                                  FROM sason.servisstokhareketdetaylar) h,
                               sason.servisstoklar a, 
                                (
                                        SELECT CASE
                                                 WHEN servisstokid IS NULL THEN 0 - ambarstokmiktar
                                                 ELSE stokmiktar
                                              END
                                                 stokmiktar,
                                              CASE
                                                 WHEN servisstokid IS NULL THEN ambarstokid
                                                 ELSE servisstokid
                                              END
                                                 servisstokid,
                                              servisid
                                         FROM (SELECT a.stokmiktar - NVL (b.stokmiktar, 0) stokmiktar,
                                                      a.servisstokid,
                                                      b.servisstokid ambarstokid,
                                                      b.stokmiktar ambarstokmiktar,
                                                      a.servisid
                                                 FROM (  SELECT SUM (stokmiktar) STOKMIKTAR,
                                                                servisid,
                                                                servisstokid
                                                           FROM(SELECT servisid,
                                                                        servisstokid,
                                                                        amiktar * stokislemtipdeger STOKMIKTAR
                                                                   FROM servisstokhareketdetaylar s,
                                                                        servisstokhareketler h
                                                                  WHERE     h.id = S.SERVISSTOKHAREKETID
                                                                        AND s.servisdepoid NOT IN(21, 22) 
                                                                        )
                                                       GROUP BY servisid, servisstokid) a
                                                      FULL OUTER JOIN
                                                      (SELECT SUM (a.miktar) stokmiktar,
                                                                a.servisstokid,
                                                                c.servisid
                                                           FROM servisismislemmalzemeler a,
                                                                servisisemirislemler b,
                                                                servisisemirler c
                                                          WHERE c.id = b.servisisemirid
                                                                AND b.id = A.SERVISISEMIRISLEMID
                                                                AND a.durumid = 1
                                                                AND c.teknikolaraktamamla = 0  
                                                       GROUP BY servisstokid, servisid) b
                                                 ON(a.servisstokid = b.servisstokid))
                                            ) c 
                            WHERE     h.servisstokid = a.id
                                  AND A.ID = C.SERVISSTOKID
                                  AND C.STOKMIKTAR <> 0
                                  AND a.servisid = c.servisid  
                             ) p,
                          servisstokturler a
                    WHERE p.servisstokturid = a.id 
                       AND hservisid  not in (1,134,136)
                   ) asd 
    
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardStoklarWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        $servicesQuery3 = '';
        $servicesQuery4 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // and  servisid in (94,96)
            $servicesQuery = ' and  servisid in ('.$_GET['src'].')   ';
            // and  c.servisid in (94,96) 
            $servicesQuery2 = ' and c.servisid in   ('.$_GET['src'].')  ';
            // AND a.servisid in (94,96)
            $servicesQuery3 = ' AND a.servisid in  ('.$_GET['src'].')  ';
            // AND hservisid  in (94,96)
            $servicesQuery4 = ' AND hservisid  in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                SELECT              
                     
                   --TO_CHAR(sum(stoktutar), '999,999,999,999,999') A from (
                    sum(asd.stoktutar)  A from (
                   SELECT
                            p.HSERVISID SERVISID, 
                            /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = p.HSERVISID   )  as servisad,  */
                            (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = p.HSERVISID) as servisad, 
                           p.stokmiktar * p.ortalamamaliyet stoktutar              
                      FROM(SELECT servisstokturid,
                               a.id,
                               a.servisid hservisid, 
                               C.STOKMIKTAR, 
                               kurlar_pkg.ORTALAMAMALIYET(a.id) ortalamamaliyet
                        
                            FROM(SELECT DISTINCT servisstokid
                                  FROM sason.servisstokhareketdetaylar) h,
                                    sason.servisstoklar a, 
                                     (
                                        SELECT CASE
                                                 WHEN servisstokid IS NULL THEN 0 - ambarstokmiktar
                                                 ELSE stokmiktar
                                              END
                                                 stokmiktar,
                                              CASE
                                                 WHEN servisstokid IS NULL THEN ambarstokid
                                                 ELSE servisstokid
                                              END
                                                 servisstokid,
                                              servisid
                                         FROM (SELECT a.stokmiktar - NVL (b.stokmiktar, 0) stokmiktar,
                                                      a.servisstokid,
                                                      b.servisstokid ambarstokid,
                                                      b.stokmiktar ambarstokmiktar,
                                                      a.servisid
                                                 FROM (  SELECT SUM (stokmiktar) STOKMIKTAR,
                                                                servisid,
                                                                servisstokid
                                                           FROM(SELECT servisid,
                                                                        servisstokid,
                                                                        amiktar * stokislemtipdeger STOKMIKTAR
                                                                   FROM servisstokhareketdetaylar s,
                                                                        servisstokhareketler h
                                                                  WHERE     h.id = S.SERVISSTOKHAREKETID
                                                                        AND s.servisdepoid NOT IN(21, 22)
                                                                        /*and  servisid in (94,96) ------------------************************************/
                                                                        ".$servicesQuery."
                                                                        )
                                                       GROUP BY servisid, servisstokid) a
                                                      FULL OUTER JOIN
                                                      (SELECT SUM (a.miktar) stokmiktar,
                                                                a.servisstokid,
                                                                c.servisid
                                                           FROM servisismislemmalzemeler a,
                                                                servisisemirislemler b,
                                                                servisisemirler c
                                                          WHERE c.id = b.servisisemirid
                                                                AND b.id = A.SERVISISEMIRISLEMID
                                                                AND a.durumid = 1
                                                                AND c.teknikolaraktamamla = 0
                                                                /*and  c.servisid in (94,96) ------------------************************************/
                                                                ".$servicesQuery2."
                                                       GROUP BY servisstokid, servisid) b
                                                 ON(a.servisstokid = b.servisstokid))
                                            ) c 
                                WHERE     h.servisstokid = a.id
                               AND A.ID = C.SERVISSTOKID
                               AND C.STOKMIKTAR <> 0
                               AND a.servisid = c.servisid
                              /*AND a.servisid in (94,96) ------------------*************************************/
                              ".$servicesQuery3."
                            
                          ) p,
                       servisstokturler a
                 WHERE p.servisstokturid = a.id 
                 /*AND hservisid  in (94,96)  ------------------************************************/
                 ".$servicesQuery4."
                ) asd  
             
                
                order by  asd.SERVISID
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayStoklarGrid($args = array()) {
         $servicesQuery = '';
        $servicesQuery2 = '';
        $servicesQuery3 = '';
        $servicesQuery4 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // and  servisid in (94,96)
            $servicesQuery = ' and  servisid in ('.$_GET['src'].')   ';
            // and  c.servisid in (94,96) 
            $servicesQuery2 = ' and c.servisid in   ('.$_GET['src'].')  ';
            // AND a.servisid in (94,96)
            $servicesQuery3 = ' AND a.servisid in  ('.$_GET['src'].')  ';
            // AND hservisid  in (94,96)
            $servicesQuery4 = ' AND hservisid  in  ('.$_GET['src'].')  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                SELECT              
                    asd.SERVISID , 
                    servisad,
                    sum(asd.stoktutar)  stoktutar from (
                   SELECT
                            p.HSERVISID SERVISID, 
                            /*(Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = p.HSERVISID   )  as servisad,  */
                            (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = p.HSERVISID) as servisad, 
                           p.stokmiktar * p.ortalamamaliyet stoktutar              
                      FROM(SELECT servisstokturid,
                               a.id,
                               a.servisid hservisid, 
                               C.STOKMIKTAR, 
                               kurlar_pkg.ORTALAMAMALIYET(a.id) ortalamamaliyet
                        
                            FROM(SELECT DISTINCT servisstokid
                                  FROM sason.servisstokhareketdetaylar) h,
                                    sason.servisstoklar a, 
                                     (
                                        SELECT CASE
                                                 WHEN servisstokid IS NULL THEN 0 - ambarstokmiktar
                                                 ELSE stokmiktar
                                              END
                                                 stokmiktar,
                                              CASE
                                                 WHEN servisstokid IS NULL THEN ambarstokid
                                                 ELSE servisstokid
                                              END
                                                 servisstokid,
                                              servisid
                                         FROM (SELECT a.stokmiktar - NVL (b.stokmiktar, 0) stokmiktar,
                                                      a.servisstokid,
                                                      b.servisstokid ambarstokid,
                                                      b.stokmiktar ambarstokmiktar,
                                                      a.servisid
                                                 FROM (  SELECT SUM (stokmiktar) STOKMIKTAR,
                                                                servisid,
                                                                servisstokid
                                                           FROM(SELECT servisid,
                                                                        servisstokid,
                                                                        amiktar * stokislemtipdeger STOKMIKTAR
                                                                   FROM servisstokhareketdetaylar s,
                                                                        servisstokhareketler h
                                                                  WHERE     h.id = S.SERVISSTOKHAREKETID
                                                                        AND s.servisdepoid NOT IN(21, 22) 
                                                                         and servisid not IN (1,134,136) 
                                                                        )
                                                       GROUP BY servisid, servisstokid) a
                                                      FULL OUTER JOIN
                                                      (SELECT SUM (a.miktar) stokmiktar,
                                                                a.servisstokid,
                                                                c.servisid
                                                           FROM servisismislemmalzemeler a,
                                                                servisisemirislemler b,
                                                                servisisemirler c
                                                          WHERE c.id = b.servisisemirid
                                                                AND b.id = A.SERVISISEMIRISLEMID
                                                                AND a.durumid = 1
                                                                AND c.teknikolaraktamamla = 0 
                                                       GROUP BY servisstokid, servisid) b
                                                 ON(a.servisstokid = b.servisstokid))
                                            ) c 
                                WHERE     h.servisstokid = a.id
                               AND A.ID = C.SERVISSTOKID
                               AND C.STOKMIKTAR <> 0
                               AND a.servisid = c.servisid 
                          ) p,
                       servisstokturler a
                 WHERE p.servisstokturid = a.id   
                ) asd  
             
                group by asd.servisid,servisad 
                order by  asd.SERVISID
                    ";
            //print_r($sql);
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayStoklarGridWithServices($args = array()) {
         $servicesQuery = '';
        $servicesQuery2 = '';
        $servicesQuery3 = '';
        $servicesQuery4 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // and  servisid in (94,96)
            $servicesQuery = ' and  servisid in ('.$_GET['src'].')   ';
            // and  c.servisid in (94,96) 
            $servicesQuery2 = ' and c.servisid in   ('.$_GET['src'].')  ';
            // AND a.servisid in (94,96)
            $servicesQuery3 = ' AND a.servisid in  ('.$_GET['src'].')  ';
            // AND hservisid  in (94,96)
            $servicesQuery4 = ' AND hservisid  in  ('.$_GET['src'].')  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                SELECT              
                    asd.SERVISID , 
                    servisad,
                    sum(asd.stoktutar)  stoktutar from (
                   SELECT
                            p.HSERVISID SERVISID, 
                            /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = p.HSERVISID   )  as servisad,  */ 
                            (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = p.HSERVISID) as servisad, 
                           p.stokmiktar * p.ortalamamaliyet stoktutar              
                      FROM(SELECT servisstokturid,
                               a.id,
                               a.servisid hservisid, 
                               C.STOKMIKTAR, 
                               kurlar_pkg.ORTALAMAMALIYET(a.id) ortalamamaliyet
                        
                            FROM(SELECT DISTINCT servisstokid
                                  FROM sason.servisstokhareketdetaylar) h,
                                    sason.servisstoklar a, 
                                     (
                                        SELECT CASE
                                                 WHEN servisstokid IS NULL THEN 0 - ambarstokmiktar
                                                 ELSE stokmiktar
                                              END
                                                 stokmiktar,
                                              CASE
                                                 WHEN servisstokid IS NULL THEN ambarstokid
                                                 ELSE servisstokid
                                              END
                                                 servisstokid,
                                              servisid
                                         FROM (SELECT a.stokmiktar - NVL (b.stokmiktar, 0) stokmiktar,
                                                      a.servisstokid,
                                                      b.servisstokid ambarstokid,
                                                      b.stokmiktar ambarstokmiktar,
                                                      a.servisid
                                                 FROM (  SELECT SUM (stokmiktar) STOKMIKTAR,
                                                                servisid,
                                                                servisstokid
                                                           FROM(SELECT servisid,
                                                                        servisstokid,
                                                                        amiktar * stokislemtipdeger STOKMIKTAR
                                                                   FROM servisstokhareketdetaylar s,
                                                                        servisstokhareketler h
                                                                  WHERE     h.id = S.SERVISSTOKHAREKETID
                                                                        AND s.servisdepoid NOT IN(21, 22)
                                                                        /*and  servisid in (94,96) ------------------************************************/
                                                                        ".$servicesQuery."
                                                                        )
                                                       GROUP BY servisid, servisstokid) a
                                                      FULL OUTER JOIN
                                                      (SELECT SUM (a.miktar) stokmiktar,
                                                                a.servisstokid,
                                                                c.servisid
                                                           FROM servisismislemmalzemeler a,
                                                                servisisemirislemler b,
                                                                servisisemirler c
                                                          WHERE c.id = b.servisisemirid
                                                                AND b.id = A.SERVISISEMIRISLEMID
                                                                AND a.durumid = 1
                                                                AND c.teknikolaraktamamla = 0
                                                                /*and  c.servisid in (94,96) ------------------************************************/
                                                                ".$servicesQuery2."
                                                       GROUP BY servisstokid, servisid) b
                                                 ON(a.servisstokid = b.servisstokid))
                                            ) c 
                                WHERE     h.servisstokid = a.id
                               AND A.ID = C.SERVISSTOKID
                               AND C.STOKMIKTAR <> 0
                               AND a.servisid = c.servisid
                              /*AND a.servisid in (94,96) ------------------*************************************/
                              ".$servicesQuery3."
                            
                          ) p,
                       servisstokturler a
                 WHERE p.servisstokturid = a.id 
                 /*AND hservisid  in (94,96)  ------------------************************************/
                 ".$servicesQuery4."
                ) asd  
             
                group by asd.servisid,servisad 
                order by  asd.SERVISID
                    ";
            //print_r($sql);
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardAracGirisSayilari($args = array()) {
        
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //print_r($today);
        //print_r($dayAfter);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT
                        /*tarihler.yil, 
                        tarihler.ay,*/ 
                        --servis.servisid, 
                        /*servis.isortakad SERVISISORTAKAD, 
                        servis.VARLIKAD SERVISVARLIKAD, */
                        /*ags.isemir_acilan, 
                        ags.isemir_kapanan*/
                        tarihler.tarih, 
                        sum(ags.arac_giris) as A
                    FROM 
                        (SELECT TARIH, YIL, AY FROM TARIHLER WHERE TARIH BETWEEN  to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')) tarihler
                    left join vt_servisler servis on --servis.servisid =94 and 
                                servis.dilkod = 'Turkish'
                    left join mobilags ags on AGS.SERVIS = servis.servisid and 
                            AGS.TARIH = tarihler.tarih
                    GROUP BY tarihler.tarih
                    order by tarihler.tarih--, servis.servisid 
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardAracGirisSayilariWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        $servicesQuery3 = '';
        $servicesQuery4 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // servis.servisid =96 and 
            $servicesQuery = '   servis.servisid in ('.$_GET['src'].') and  ';
            // and  c.servisid in (94,96) 
            $servicesQuery2 = ' and c.servisid in   ('.$_GET['src'].')  ';
            // AND a.servisid in (94,96)
            $servicesQuery3 = ' AND a.servisid in  ('.$_GET['src'].')  ';
            // AND hservisid  in (94,96)
            $servicesQuery4 = ' AND hservisid  in  ('.$_GET['src'].')  ';
        } 
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //print_r($today);
        //print_r($dayAfter);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT
               
                         sum(ags.arac_giris) as A
                     
                    
                    FROM 
                     
                          (SELECT TARIH, YIL, AY FROM TARIHLER WHERE TARIH BETWEEN  to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')) tarihler
                    left join vt_servisler servis on 
                           -- servis.servisid =96 and 
                           ".$servicesQuery."
                                servis.dilkod = 'Turkish'
                    left join mobilags ags on AGS.SERVIS = servis.servisid and 
                            AGS.TARIH = tarihler.tarih 
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayAracGirisSayilari($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "   
            SELECT
                sum(ags.arac_giris) as ARAC_GIRIS_SAYISI,
                tarihler.tarih 
            FROM 
                (SELECT TARIH, YIL, AY FROM TARIHLER WHERE TARIH BETWEEN  to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')) tarihler
            left join vt_servisler servis on 
                        servis.dilkod = 'Turkish'
            left join mobilags ags on AGS.SERVIS = servis.servisid and 
                    AGS.TARIH = tarihler.tarih
            GROUP BY tarihler.tarih
            order by tarihler.tarih asc 
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
    public function getAfterSalesDetayAracGirisSayilariWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //servis.servisid =94 and 
            $servicesQuery = ' servis.servisid in ('.$_GET['src'].') and  ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT
                        /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = servis.servisid) as servisad, */ 
                        (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = servis.servisid) as servisad, 
                        --sum(ags.arac_giris) as ARAC_GIRIS_SAYISI
                        servis.servisid,
                        tarihler.tarih, 
                        ags.arac_giris
                    FROM 
                        (SELECT TARIH, YIL, AY FROM TARIHLER WHERE TARIH BETWEEN  to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')) tarihler
                    left join vt_servisler servis on 
                    --servis.servisid =94 and 
                                ".$servicesQuery."
                                servis.dilkod = 'Turkish'
                    left join mobilags ags on AGS.SERVIS = servis.servisid and 
                            AGS.TARIH = tarihler.tarih
                    --GROUP BY tarihler.tarih
                    order by servis.servisid ,tarihler.tarih asc 
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
    public function getAfterSalesDetayAracGirisSayilariAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try     {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT 
                yil, tarih,                   
                sum(arac_giris) arac_giris 
         FROM ( 
        SELECT  vv.servisid ,  
                      tarihicin.tar as tarih,
                      tarihicin.yil as yil,            
                      nvl(data1.arac_giris,0) arac_giris
                    from vt_servisler vv  
                   left join (
                     select distinct 
                          to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                          to_number(to_char(x.tarih,'yyyy')) yil  
                     from sason.tarihler x WHERE     
                           to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(to_char(sysdate,'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(to_char(sysdate,'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww'))
                            and to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date(to_char(sysdate,'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))                                      
                   ) tarihicin on 1=1
                   LEFT JOIN ( 
                      select  distinct  zx.servis servisid,  
                            to_number(to_char(zx.tarih,'yyyy')) yil ,
                            to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww')) tarih ,
                            sum(zx.arac_giris) arac_giris     
                      FROM mobilags zx 
                      WHERE 
                              to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(to_char(sysdate,'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(to_char(sysdate,'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww'))
                              and to_number(to_char(zx.tarih,'yyyy'))  =  to_number(to_char(to_date(to_char(sysdate,'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                      group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww'))
                   ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                   WHERE 
                       vv.servisid not in (1,134,136) and  
                       vv.dilkod ='Turkish'  
                         ) asd
              group by yil,tarih             
              ORDER BY  yil, tarih

            ";
             
            $statement = $pdo->prepare($sql);  
           // print_r($sql);
            
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
    public function getAfterSalesDetayAracGirisSayilariAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //zx.servis =94 AND 
            $servicesQuery = ' zx.servis in ('.$_GET['src'].') and  ';
            // and vv.servisid in
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                    (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad,  
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.arac_giris,0) arac_giris  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE     
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww'))
                      and to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))                                      
             ) tarihicin on 1=1
             LEFT JOIN ( 
                  
                select  distinct  zx.servis servisid,  
                      to_number(to_char(zx.tarih,'yyyy')) yil ,
                      to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww')) tarih ,
                      sum(zx.arac_giris) arac_giris     
                FROM mobilags zx 
                WHERE
                        ".$servicesQuery."   
                        to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww'))
                        and to_number(to_char(zx.tarih,'yyyy'))  =  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))   
                
                group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww'))
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
             ORDER BY vv.servisid, yil, tarih desc

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
    public function getAfterSalesDetayAracGirisSayilariYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT 
            yil, tarih,                   
            sum(arac_giris) arac_giris 
            FROM ( 

               SELECT  vv.servisid , 
                  /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                  tarihicin.ay as tarih,
                  tarihicin.yil as yil,            
                  nvl(data1.arac_giris,0) arac_giris  
                from vt_servisler vv  
               left join (
                 select distinct 
                      to_number(to_char(x.tarih,'yyyy')) yil  ,
                      to_number(to_char(x.tarih,'MM')) ay 
                 from sason.tarihler x 
                 WHERE     
                       x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
               ) tarihicin on 1=1
               LEFT JOIN ( 

                  select  distinct  zx.servis servisid,  
                        to_number(to_char(zx.tarih,'yyyy')) yil ,
                        to_number(to_char(zx.tarih,'MM')) ay ,
                        sum(zx.arac_giris) arac_giris     
                  FROM mobilags zx 
                  WHERE                  
                       zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                  group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
               ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and
                  vv.dilkod ='Turkish' 
                   ) asd
              group by yil,tarih             
              ORDER BY  yil, tarih  
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
    public function getAfterSalesDetayAracGirisSayilariYillikWithServices($args = array()) {
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' zx.servis  in ('.$_GET['src'].') and  ';
            // vv.servisid in (94)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
            SELECT  vv.servisid , 
                /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                   (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.arac_giris,0) arac_giris  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE     
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
                  
                select  distinct  zx.servis servisid,  
                      to_number(to_char(zx.tarih,'yyyy')) yil ,
                      to_number(to_char(zx.tarih,'MM')) ay ,
                      sum(zx.arac_giris) arac_giris     
                FROM mobilags zx 
                WHERE
                        ".$servicesQuery."  
                        zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                
                group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih  
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
    public function getAfterSalesDashboardDowntime($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        //print_r($dayBefore);
        //print_r($dayBeforeTwo);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                SELECT  -1 , 'Türkiye Geneli'  as servisad,
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.DOWNTIME,0) DOWNTIME  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE 
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
          
                    SELECT    to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                      to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                        trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                            (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                            trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME , 
                        -1 servisid,         
                        'Türkiye Geneli' isortakad,                 
                        'R001' partnercode                      
                    FROM 
                        servisisemirler ie,
                        vt_servisler vtsrv,
                        aracturler ar 
                    WHERE   
                          (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                        and vtsrv.dilkod(+) = 'Turkish' 
                        and ar.kod =  ie.aractipad and ar.durumid =1 
                        AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                        AND ie.servisid not in (1,134,136)
                     GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM'))
             
               
             ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE   
                   vv.servisid in (1)
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih 
    
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardDowntimeWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and ie.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                            (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                        tarihicin.ay as tarih,
                        tarihicin.yil as yil,            
                        nvl(data1.DOWNTIME,0) DOWNTIME  

                      from vt_servisler vv  
                     left join (
                       select distinct 
                            to_number(to_char(x.tarih,'yyyy')) yil  ,
                            to_number(to_char(x.tarih,'MM')) ay 
                       from sason.tarihler x 
                       WHERE     
                             x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                     ) tarihicin on 1=1
                     LEFT JOIN ( 

                       SELECT   to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                                to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                                trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                                    (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                                    trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME ,
                                ie.servisid,
                                vtsrv.isortakad,  
                                vtsrv.partnercode                      
                            FROM 
                                servisisemirler ie,
                                vt_servisler vtsrv,
                                aracturler ar 
                            WHERE -- ie.tamamlanmatarih between   to_date('01/05/2018') and  to_date('30/05/2018') and
                                ie.teknikolaraktamamla=1 
                                ".$servicesQuery."
                                and (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                                and vtsrv.dilkod(+) = 'Turkish' and vtsrv.servisid(+)=ie.servisid
                                and ar.kod =  ie.aractipad and ar.durumid =1  
                                AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                        GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM')), ie.servisid, vtsrv.isortakad, vtsrv.partnercode 
                     ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                     WHERE 
                         vv.servisid not in (1,134,136)  
                         ".$servicesQuery2." 
                         and vv.dilkod ='Turkish'  
                      ORDER BY vv.servisid, yil, tarih 
    
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayGridDowntime($args = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  vv.servisid , 
                    /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                    (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                    tarihicin.ay as tarih,
                    tarihicin.yil as yil,            
                    nvl(data1.DOWNTIME,0) DOWNTIME  

                  from vt_servisler vv  
                 left join (
                   select distinct 
                        to_number(to_char(x.tarih,'yyyy')) yil  ,
                        to_number(to_char(x.tarih,'MM')) ay 
                   from sason.tarihler x 
                   WHERE     
                         x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                 ) tarihicin on 1=1
                 LEFT JOIN ( 

                   SELECT   to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                            to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                            trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                                (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                                trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME ,
                            ie.servisid,
                            vtsrv.isortakad,  
                            vtsrv.partnercode                      
                        FROM 
                            servisisemirler ie,
                            vt_servisler vtsrv,
                            aracturler ar 
                        WHERE -- ie.tamamlanmatarih between   to_date('01/05/2018') and  to_date('30/05/2018') and
                            ie.teknikolaraktamamla=1 

                            and (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                            and vtsrv.dilkod(+) = 'Turkish' and vtsrv.servisid(+)=ie.servisid
                            and ar.kod =  ie.aractipad and ar.durumid =1  
                            AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                    GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM')), ie.servisid, vtsrv.isortakad, vtsrv.partnercode 
                 ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                 WHERE 
                     vv.servisid not in (1,134,136)
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayGridDowntimeWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and ie.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  vv.servisid , 
                        /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                        (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                        tarihicin.ay as tarih,
                        tarihicin.yil as yil,            
                        nvl(data1.DOWNTIME,0) DOWNTIME  

                      from vt_servisler vv  
                     left join (
                       select distinct 
                            to_number(to_char(x.tarih,'yyyy')) yil  ,
                            to_number(to_char(x.tarih,'MM')) ay 
                       from sason.tarihler x 
                       WHERE     
                             x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                     ) tarihicin on 1=1
                     LEFT JOIN ( 

                       SELECT   to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                                to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                                trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                                    (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                                    trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME ,
                                ie.servisid,
                                vtsrv.isortakad,  
                                vtsrv.partnercode                      
                            FROM 
                                servisisemirler ie,
                                vt_servisler vtsrv,
                                aracturler ar 
                            WHERE -- ie.tamamlanmatarih between   to_date('01/05/2018') and  to_date('30/05/2018') and
                                ie.teknikolaraktamamla=1 
                                ".$servicesQuery."
                                and (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                                and vtsrv.dilkod(+) = 'Turkish' and vtsrv.servisid(+)=ie.servisid
                                and ar.kod =  ie.aractipad and ar.durumid =1  
                                AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                        GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM')), ie.servisid, vtsrv.isortakad, vtsrv.partnercode 
                     ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                     WHERE 
                         vv.servisid not in (1,134,136)  
                         ".$servicesQuery2." 
                         and vv.dilkod ='Turkish'  
                      ORDER BY vv.servisid, yil, tarih 
    
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardVerimlilik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        //print_r($dayBefore);
        //print_r($dayBeforeTwo);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                SELECT  -1 , 'Türkiye Geneli'  as servisad,
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.DOWNTIME,0) DOWNTIME  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE 
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
          
                    SELECT    to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                      to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                        trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                            (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                            trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME , 
                        -1 servisid,         
                        'Türkiye Geneli' isortakad,                 
                        'R001' partnercode                      
                    FROM 
                        servisisemirler ie,
                        vt_servisler vtsrv,
                        aracturler ar 
                    WHERE   
                          (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                        and vtsrv.dilkod(+) = 'Turkish' 
                        and ar.kod =  ie.aractipad and ar.durumid =1 
                        AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                        AND ie.servisid not in (1,134,136)
                     GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM'))
             
               
             ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE   
                   vv.servisid in (1)
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih 
    
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardVerimlilikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and ie.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                             (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                        tarihicin.ay as tarih,
                        tarihicin.yil as yil,            
                        nvl(data1.DOWNTIME,0) DOWNTIME  

                      from vt_servisler vv  
                     left join (
                       select distinct 
                            to_number(to_char(x.tarih,'yyyy')) yil  ,
                            to_number(to_char(x.tarih,'MM')) ay 
                       from sason.tarihler x 
                       WHERE     
                             x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                     ) tarihicin on 1=1
                     LEFT JOIN ( 

                       SELECT   to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                                to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                                trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                                    (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                                    trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME ,
                                ie.servisid,
                                vtsrv.isortakad,  
                                vtsrv.partnercode                      
                            FROM 
                                servisisemirler ie,
                                vt_servisler vtsrv,
                                aracturler ar 
                            WHERE -- ie.tamamlanmatarih between   to_date('01/05/2018') and  to_date('30/05/2018') and
                                ie.teknikolaraktamamla=1 
                                ".$servicesQuery."
                                and (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                                and vtsrv.dilkod(+) = 'Turkish' and vtsrv.servisid(+)=ie.servisid
                                and ar.kod =  ie.aractipad and ar.durumid =1  
                                AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                        GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM')), ie.servisid, vtsrv.isortakad, vtsrv.partnercode 
                     ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                     WHERE 
                         vv.servisid not in (1,134,136)  
                         ".$servicesQuery2." 
                         and vv.dilkod ='Turkish'  
                      ORDER BY vv.servisid, yil, tarih 
    
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayVerimlilikYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT 
            yil, tarih,                   
            sum(arac_giris) arac_giris 
            FROM ( 

               SELECT  vv.servisid , 
                  /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                     (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                  tarihicin.ay as tarih,
                  tarihicin.yil as yil,            
                  nvl(data1.arac_giris,0) arac_giris  
                from vt_servisler vv  
               left join (
                 select distinct 
                      to_number(to_char(x.tarih,'yyyy')) yil  ,
                      to_number(to_char(x.tarih,'MM')) ay 
                 from sason.tarihler x 
                 WHERE     
                       x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
               ) tarihicin on 1=1
               LEFT JOIN ( 

                  select  distinct  zx.servis servisid,  
                        to_number(to_char(zx.tarih,'yyyy')) yil ,
                        to_number(to_char(zx.tarih,'MM')) ay ,
                        sum(zx.arac_giris) arac_giris     
                  FROM mobilags zx 
                  WHERE                  
                       zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                  group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
               ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and
                  vv.dilkod ='Turkish' 
                   ) asd
              group by yil,tarih             
              ORDER BY  yil, tarih  
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
    public function getAfterSalesDetayVerimlilikYillikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' zx.servis  in ('.$_GET['src'].') and  ';
            // vv.servisid in (94)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
            SELECT  vv.servisid , 
                /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.arac_giris,0) arac_giris  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE     
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
                  
                select  distinct  zx.servis servisid,  
                      to_number(to_char(zx.tarih,'yyyy')) yil ,
                      to_number(to_char(zx.tarih,'MM')) ay ,
                      sum(zx.arac_giris) arac_giris     
                FROM mobilags zx 
                WHERE
                        ".$servicesQuery."  
                        zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                
                group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih  
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
    public function getAfterSalesDashboardKapasite($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        //print_r($dayBefore);
        //print_r($dayBeforeTwo);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                SELECT  -1 , 'Türkiye Geneli'  as servisad,
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.DOWNTIME,0) DOWNTIME  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE 
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
          
                    SELECT    to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                      to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                        trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                            (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                            trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME , 
                        -1 servisid,         
                        'Türkiye Geneli' isortakad,                 
                        'R001' partnercode                      
                    FROM 
                        servisisemirler ie,
                        vt_servisler vtsrv,
                        aracturler ar 
                    WHERE   
                          (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                        and vtsrv.dilkod(+) = 'Turkish' 
                        and ar.kod =  ie.aractipad and ar.durumid =1 
                        AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                        AND ie.servisid not in (1,134,136)
                     GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM'))
             
               
             ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE   
                   vv.servisid in (1)
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih 
    
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardKapasiteWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and ie.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                             (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                        tarihicin.ay as tarih,
                        tarihicin.yil as yil,            
                        nvl(data1.DOWNTIME,0) DOWNTIME  

                      from vt_servisler vv  
                     left join (
                       select distinct 
                            to_number(to_char(x.tarih,'yyyy')) yil  ,
                            to_number(to_char(x.tarih,'MM')) ay 
                       from sason.tarihler x 
                       WHERE     
                             x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                     ) tarihicin on 1=1
                     LEFT JOIN ( 

                       SELECT   to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                                to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                                trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                                    (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                                    trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME ,
                                ie.servisid,
                                vtsrv.isortakad,  
                                vtsrv.partnercode                      
                            FROM 
                                servisisemirler ie,
                                vt_servisler vtsrv,
                                aracturler ar 
                            WHERE -- ie.tamamlanmatarih between   to_date('01/05/2018') and  to_date('30/05/2018') and
                                ie.teknikolaraktamamla=1 
                                ".$servicesQuery."
                                and (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                                and vtsrv.dilkod(+) = 'Turkish' and vtsrv.servisid(+)=ie.servisid
                                and ar.kod =  ie.aractipad and ar.durumid =1  
                                AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                        GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM')), ie.servisid, vtsrv.isortakad, vtsrv.partnercode 
                     ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                     WHERE 
                         vv.servisid not in (1,134,136)  
                         ".$servicesQuery2." 
                         and vv.dilkod ='Turkish'  
                      ORDER BY vv.servisid, yil, tarih 
    
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayKapasiteYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT 
            yil, tarih,                   
            sum(arac_giris) arac_giris 
            FROM ( 

               SELECT  vv.servisid , 
                  /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                     (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                  tarihicin.ay as tarih,
                  tarihicin.yil as yil,            
                  nvl(data1.arac_giris,0) arac_giris  
                from vt_servisler vv  
               left join (
                 select distinct 
                      to_number(to_char(x.tarih,'yyyy')) yil  ,
                      to_number(to_char(x.tarih,'MM')) ay 
                 from sason.tarihler x 
                 WHERE     
                       x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
               ) tarihicin on 1=1
               LEFT JOIN ( 

                  select  distinct  zx.servis servisid,  
                        to_number(to_char(zx.tarih,'yyyy')) yil ,
                        to_number(to_char(zx.tarih,'MM')) ay ,
                        sum(zx.arac_giris) arac_giris     
                  FROM mobilags zx 
                  WHERE                  
                       zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                  group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
               ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and
                  vv.dilkod ='Turkish' 
                   ) asd
              group by yil,tarih             
              ORDER BY  yil, tarih  
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
    public function getAfterSalesDetayKapasiteYillikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' zx.servis  in ('.$_GET['src'].') and  ';
            // vv.servisid in (94)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
            SELECT  vv.servisid , 
                /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                   (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.arac_giris,0) arac_giris  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE     
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
                  
                select  distinct  zx.servis servisid,  
                      to_number(to_char(zx.tarih,'yyyy')) yil ,
                      to_number(to_char(zx.tarih,'MM')) ay ,
                      sum(zx.arac_giris) arac_giris     
                FROM mobilags zx 
                WHERE
                        ".$servicesQuery."  
                        zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                
                group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih  
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
    public function getAfterSalesDashboardEtkinlik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        //print_r($dayBefore);
        //print_r($dayBeforeTwo);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                SELECT  -1 , 'Türkiye Geneli'  as servisad,
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.DOWNTIME,0) DOWNTIME  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE 
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
          
                    SELECT    to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                      to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                        trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                            (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                            trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME , 
                        -1 servisid,         
                        'Türkiye Geneli' isortakad,                 
                        'R001' partnercode                      
                    FROM 
                        servisisemirler ie,
                        vt_servisler vtsrv,
                        aracturler ar 
                    WHERE   
                          (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                        and vtsrv.dilkod(+) = 'Turkish' 
                        and ar.kod =  ie.aractipad and ar.durumid =1 
                        AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                        AND ie.servisid not in (1,134,136)
                     GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM'))
             
               
             ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE   
                   vv.servisid in (1)
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih 
    
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardEtkinlikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and ie.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                             (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                        tarihicin.ay as tarih,
                        tarihicin.yil as yil,            
                        nvl(data1.DOWNTIME,0) DOWNTIME  

                      from vt_servisler vv  
                     left join (
                       select distinct 
                            to_number(to_char(x.tarih,'yyyy')) yil  ,
                            to_number(to_char(x.tarih,'MM')) ay 
                       from sason.tarihler x 
                       WHERE     
                             x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                     ) tarihicin on 1=1
                     LEFT JOIN ( 

                       SELECT   to_number(to_char(ie.tamamlanmatarih,'yyyy')) yil ,
                                to_number(to_char(ie.tamamlanmatarih,'MM')) ay ,
                                trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)) ||','||
                                    (trunc((round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)-
                                    trunc(round( sum((case when ie.araccikiszamani is not null then ie.araccikiszamani else ie.tamamlanmatarih end ) - ie.kayittarih)/count(ie.id),2)))*24))||'' DOWNTIME ,
                                ie.servisid,
                                vtsrv.isortakad,  
                                vtsrv.partnercode                      
                            FROM 
                                servisisemirler ie,
                                vt_servisler vtsrv,
                                aracturler ar 
                            WHERE -- ie.tamamlanmatarih between   to_date('01/05/2018') and  to_date('30/05/2018') and
                                ie.teknikolaraktamamla=1 
                                ".$servicesQuery."
                                and (ie.arackazali <> 1 or ie.arackazaaciklama is null or ie.arackazaaciklama = '')
                                and vtsrv.dilkod(+) = 'Turkish' and vtsrv.servisid(+)=ie.servisid
                                and ar.kod =  ie.aractipad and ar.durumid =1  
                                AND ie.tamamlanmatarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                        GROUP BY  to_number(to_char(ie.tamamlanmatarih,'yyyy')),  to_number(to_char(ie.tamamlanmatarih,'MM')), ie.servisid, vtsrv.isortakad, vtsrv.partnercode 
                     ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                     WHERE 
                         vv.servisid not in (1,134,136)  
                         ".$servicesQuery2." 
                         and vv.dilkod ='Turkish'  
                      ORDER BY vv.servisid, yil, tarih 
    
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayEtkinlikYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT 
            yil, tarih,                   
            sum(arac_giris) arac_giris 
            FROM ( 

               SELECT  vv.servisid , 
                  /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */
                     (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                  tarihicin.ay as tarih,
                  tarihicin.yil as yil,            
                  nvl(data1.arac_giris,0) arac_giris  
                from vt_servisler vv  
               left join (
                 select distinct 
                      to_number(to_char(x.tarih,'yyyy')) yil  ,
                      to_number(to_char(x.tarih,'MM')) ay 
                 from sason.tarihler x 
                 WHERE     
                       x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
               ) tarihicin on 1=1
               LEFT JOIN ( 

                  select  distinct  zx.servis servisid,  
                        to_number(to_char(zx.tarih,'yyyy')) yil ,
                        to_number(to_char(zx.tarih,'MM')) ay ,
                        sum(zx.arac_giris) arac_giris     
                  FROM mobilags zx 
                  WHERE                  
                       zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                  group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
               ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and
                  vv.dilkod ='Turkish' 
                   ) asd
              group by yil,tarih             
              ORDER BY  yil, tarih  
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
    public function getAfterSalesDetayEtkinlikYillikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' zx.servis  in ('.$_GET['src'].') and  ';
            // vv.servisid in (94)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
            SELECT  vv.servisid , /*(Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */
                                    (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.arac_giris,0) arac_giris  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE     
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
                  
                select  distinct  zx.servis servisid,  
                      to_number(to_char(zx.tarih,'yyyy')) yil ,
                      to_number(to_char(zx.tarih,'MM')) ay ,
                      sum(zx.arac_giris) arac_giris     
                FROM mobilags zx 
                WHERE
                        ".$servicesQuery."  
                        zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                
                group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih  
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
    public function getAfterSalesDashboardYedekParcaTS($args = array()) {
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                select  
                TO_CHAR(ROUND(sum(nvl(yagsatistutar,0)), 0), '999,999,999,999,999') yagsatistutar,
                TO_CHAR(ROUND(sum(nvl(yedekparcatoplamsatis,0)), 0), '999,999,999,999,999') yedekparcatoplamsatis 

                from ( 
                select  -- siparisservisid ,

                  CASE
                         WHEN SERVISSTOKTURID = 6
                         THEN
                           sum (tutar)
                      END  yagsatistutar,


                      CASE
                         WHEN SERVISSTOKTURID <> 6
                         THEN
                           sum (tutar)
                      END  yedekparcatoplamsatis 



                from sason.ypdata
                where  YEDEKPARCARAPORTARIHI > --to_date('01/01/2016')
                      to_date(to_char(to_date( '01.01.2018','dd/mm/yyyy') , 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                group  by  SERVISSTOKTURID
                ) asd 
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardYedekParcaTSWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid   in (94 )   and 
            $servicesQuery = ' a.servisid   in  ('.$_GET['src'].') and  ';
            // and vv.servisid   in (94 ) 
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')   ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT 
            TO_CHAR(sum(yedekparcatoplamsatis), '999,999,999,999,999') yedekparcatoplamsatis
            --sum(yedekparcatoplamsatis)     
        --    yedekparcatoplamsatis
         FROM (  
         SELECT   
                     to_char(tarihicin.tar) tarih ,
                     nvl(data1.yedekparcatoplamsatis,0) yedekparcatoplamsatis   
                       from vt_servisler vv  
                      left join (
                        select distinct 
                            to_date(x.tarih,'dd/mm/yyyy') tar   
                        from sason.tarihler x where
                              x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                      ) tarihicin on 1=1
                      LEFT JOIN (
                       select  distinct
                            a.servisid , 
                             to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') AS TARIH,
                             /*  CASE
                                  WHEN SERVISSTOKTURID = 6
                                  THEN
                                    sum (tutar)
                               END  yagsatistutar 
                              */
                               CASE
                                  WHEN SERVISSTOKTURID <> 6
                                  THEN
                                    sum (tutar)
                               END  yedekparcatoplamsatis    
                         from sason.ypdata  a
                         WHERE 
                             --a.servisid   in (94 )   and 
                               ".$servicesQuery."
                             a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                         GROUP BY to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') ,SERVISSTOKTURID ,servisid  
                      ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar 
                      WHERE 
                           vv.servisid  not in (1 , 134 , 136 )   
                            --and vv.servisid   in (94 )
                            ".$servicesQuery2."
                            AND vv.dilkod ='Turkish'
                   ) asd
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayYedekParcaTS($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "   
            SELECT 
            tarih ,
            sum(yedekparcatoplamsatis)     yedekparcatoplamsatis
         FROM (  
         SELECT   
                     to_char(tarihicin.tar) tarih ,
                     nvl(data1.yedekparcatoplamsatis,0) yedekparcatoplamsatis   
                       from vt_servisler vv  
                      left join (
                        select distinct 
                            to_date(x.tarih,'dd/mm/yyyy') tar   
                        from sason.tarihler x where
                              x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                      ) tarihicin on 1=1
                      LEFT JOIN (
                       select  distinct
                          --   count( a.servisid) as MIKTAR,
                             to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') AS TARIH,
                             /*  CASE
                                  WHEN SERVISSTOKTURID = 6
                                  THEN
                                    sum (tutar)
                               END  yagsatistutar 
                              */
                               CASE
                                  WHEN SERVISSTOKTURID <> 6
                                  THEN
                                    sum (tutar)
                               END  yedekparcatoplamsatis    
                         from sason.ypdata  a
                         WHERE
                             a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                         GROUP BY to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') ,SERVISSTOKTURID 
                      ) data1 on data1.TARIH = tarihicin.tar
                      WHERE 
                            vv.servisid   in (1 )  
                            AND vv.dilkod ='Turkish'
                   ) asd
                   group  by tarih   
             ORDER BY  tarih asc  
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
    public function getAfterSalesDetayYedekParcaTSWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //servis.servisid =94 and 
            $servicesQuery = ' servis.servisid in ('.$_GET['src'].') and  ';
            // vv.servisid   in (94, 96, 98 )  AND
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT servisid , 
                --(Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = asd.servisid) as servisad, 
                (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
                   tarih ,
                            sum(yedekparcatoplamsatis)   yedekparcatoplamsatis 
                     FROM (  
                               SELECT   vv.servisid,
                                 to_char(tarihicin.tar) tarih ,
                                 nvl(data1.yedekparcatoplamsatis,0) yedekparcatoplamsatis   
                               from vt_servisler vv  
                               left join (
                                    select distinct 
                                        to_date(x.tarih,'dd/mm/yyyy') tar   
                                    from sason.tarihler x where
                                          x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                ) tarihicin on 1=1
                                LEFT JOIN (
                                   select  distinct a.SIPARISSERVISID servisid,  
                                         to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') AS TARIH,
                                       /*    CASE
                                              WHEN SERVISSTOKTURID = 6
                                              THEN
                                                sum (tutar)
                                           END  yagsatistutar */

                                           CASE
                                              WHEN SERVISSTOKTURID <> 6
                                              THEN
                                                sum (tutar)
                                           END  yedekparcatoplamsatis    
                                     from sason.ypdata  a
                                     WHERE
                                         a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                     GROUP BY to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') ,a.SERVISSTOKTURID ,a.SIPARISSERVISID
                                  ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar 
                                  WHERE 
                                         vv.servisid not in (1,134,136) and 
                                         --vv.servisid   in (94, 96, 98 )  AND
                                         ".$servicesQuery2."
                                          vv.dilkod ='Turkish'
                             ) asd
                 group  by servisid,tarih   
                 ORDER BY  servisid ,tarih  asc
                
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
    public function getAfterSalesDetayYedekParcaTSAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try     {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT
                    tarih,
                    yil,            
                    sum(yedekparcatoplamsatis) yedekparcatoplamsatis
                  FROM (   
                  SELECT   distinct
                                tarihicin.tar as tarih,
                                tarihicin.yil as yil,            
                                nvl(data1.yedekparcatoplamsatis,0) yedekparcatoplamsatis
                              from vt_servisler vv  
                             left join (
                               select distinct 
                                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                    to_number(to_char(x.tarih,'yyyy')) yil  
                               from sason.tarihler x WHERE 
                                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                     x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                             ) tarihicin on 1=1             
                             LEFT JOIN (
                              select  distinct
                                  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')) yil,  
                                  to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww')) tarih,  
                                      CASE
                                         WHEN SERVISSTOKTURID <> 6
                                         THEN
                                           sum (tutar)
                                      END  yedekparcatoplamsatis 
                                from sason.ypdata  a
                                WHERE 
                                   to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                                   a.YEDEKPARCARAPORTARIHI  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                GROUP BY   to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')), to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww')) ,SERVISSTOKTURID 
                             ) data1 on data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                             WHERE 
                                 vv.servisid   in (1) and
                                 vv.dilkod ='Turkish'  
                             ORDER BY  tarih desc 
             ) ASD 
             group by tarih, yil 
             order by tarih asc, yil asc 
            ";
             
            $statement = $pdo->prepare($sql);  
           // print_r($sql);
            
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
    public function getAfterSalesDetayYedekParcaTSAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //zx.servis =94 AND 
            $servicesQuery = ' zx.servis in ('.$_GET['src'].') and  ';
            // vv.servisid   in (94, 96, 98) and
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT servisid , 
                    --(Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = asd.servisid) as servisad,
                    (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
                    tarih,
                    yil,            
                    sum(yedekparcatoplamsatis) yedekparcatoplamsatis
                  FROM ( 

                  SELECT   distinct vv.servisid,
                                tarihicin.tar as tarih,
                                tarihicin.yil as yil,            
                                nvl(data1.yedekparcatoplamsatis,0) yedekparcatoplamsatis
                              from vt_servisler vv  
                             left join (
                               select distinct 
                                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                    to_number(to_char(x.tarih,'yyyy')) yil  
                               from sason.tarihler x WHERE 
                                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                     x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                             ) tarihicin on 1=1             
                             LEFT JOIN (
                              select  distinct a.SIPARISSERVISID servisid, 
                                  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')) yil,  
                                  to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww')) tarih,  
                                  /*    CASE
                                         WHEN SERVISSTOKTURID = 6
                                         THEN
                                           sum (tutar)
                                      END  yagsatistutar */

                                      CASE
                                         WHEN SERVISSTOKTURID <> 6
                                         THEN
                                           sum (tutar)
                                      END  yedekparcatoplamsatis 
                                from sason.ypdata  a
                                WHERE 
                                   to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                                   a.YEDEKPARCARAPORTARIHI  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                GROUP BY   to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')), to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww')) ,SERVISSTOKTURID ,a.SIPARISSERVISID
                             ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                             WHERE 
                                vv.servisid not in (1,134,136) and 
                                --vv.servisid   in (94, 96, 98) and
                                ".$servicesQuery2."
                                vv.dilkod ='Turkish'  
                             ORDER BY  tarih desc 
                             ) ASD 
             group by servisid,tarih, yil 
             order by  servisid,tarih asc, yil asc

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
    public function getAfterSalesDetayYedekParcaTSYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                  SELECT
    ay,
    yil,            
    sum(yedekparcatoplamsatis) yedekparcatoplamsatis
  FROM ( 
  
   
   SELECT  
                tarihicin.ay as ay,
                tarihicin.yil as yil,            
                nvl(data1.yedekparcatoplamsatis,0) yedekparcatoplamsatis  
              from vt_servisler vv  
             left join (
               select distinct  
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x WHERE  
                     x.tarih  between  to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                      
             ) tarihicin on 1=1
             LEFT JOIN (
              select  distinct
                  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')) yil,  
                  to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'MM')) ay, 
                     
                      CASE
                         WHEN SERVISSTOKTURID <> 6
                         THEN
                           sum (tutar)
                      END  yedekparcatoplamsatis 
                from sason.ypdata  a
                WHERE  
                  a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                GROUP BY  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')), to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'MM')) ,SERVISSTOKTURID 
              
             ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid in (1) and  
                 vv.dilkod ='Turkish'  
             ORDER BY yil, ay  
             
                          ) ASD 
             group by yil, ay     
             order by    yil, ay  ";
             
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
    public function getAfterSalesDetayYedekParcaTSYillikWithServices($args = array()) {
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' zx.servis  in ('.$_GET['src'].') and  ';
            // vv.servisid in (94) and
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].')  and ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
                   SELECT servisid  ,  
                    --(Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad,
                    (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
                    ay,
                    yil,            
                    sum(yedekparcatoplamsatis) yedekparcatoplamsatis
                  FROM (     
                        SELECT  vv.servisid,
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.yedekparcatoplamsatis,0) yedekparcatoplamsatis  
                        from vt_servisler vv  
                         left join (
                               select distinct 
                                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                                    to_number(to_char(x.tarih,'MM')) ay 
                               from sason.tarihler x WHERE  
                                     x.tarih  between  to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                             ) tarihicin on 1=1
                             LEFT JOIN (
                              select  distinct a.SIPARISSERVISID servisid, 
                                  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')) yil,  
                                  to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'MM')) ay,  
                                  /*    CASE
                                         WHEN SERVISSTOKTURID = 6
                                         THEN
                                           sum (tutar)
                                      END  yagsatistutar 
                                      ,*/
                                      CASE
                                         WHEN SERVISSTOKTURID <> 6
                                         THEN
                                           sum (tutar)
                                      END  yedekparcatoplamsatis 
                                from sason.ypdata  a
                                WHERE  
                                  a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                GROUP BY  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')), to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'MM')) ,SERVISSTOKTURID  ,a.SIPARISSERVISID

                             ) data1 on  data1.servisid = vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                             WHERE 
                                  vv.servisid not in (1,134,136) and 
                                --vv.servisid   in (94, 96, 98) and
                                ".$servicesQuery2."
                                 vv.dilkod ='Turkish' 
                             ORDER BY servisid,yil, ay     
            ) ASD 
             group by servisid, yil, ay 
             order by servisid, yil, ay 
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
    public function getAfterSalesDashboardYedekParcaYS($args = array()) {
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                select  
                    TO_CHAR(ROUND(sum(nvl(yagsatistutar,0)), 0), '999,999,999,999,999') yagsatistutar,
                    TO_CHAR(ROUND(sum(nvl(yedekparcatoplamsatis,0)), 0), '999,999,999,999,999') yedekparcatoplamsatis 
                 --ROUND(sum(nvl(yagsatistutar,0)), 0) yagsatistutar,
                 --ROUND(sum(nvl(yedekparcatoplamsatis,0)), 0) yedekparcatoplamsatis
                from ( 
                select  -- siparisservisid ,

                  CASE
                         WHEN SERVISSTOKTURID = 6
                         THEN
                           sum (tutar)
                      END  yagsatistutar,


                      CASE
                         WHEN SERVISSTOKTURID <> 6
                         THEN
                           sum (tutar)
                      END  yedekparcatoplamsatis 



                from sason.ypdata
                where  YEDEKPARCARAPORTARIHI > --to_date('01/01/2016')
                      to_date(to_char(to_date( '01.01.2018','dd/mm/yyyy') , 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                group  by  SERVISSTOKTURID
                ) asd 
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardYedekParcaYSWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid   in (94 )   and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // and  vv.servisid   in (94 )  
            $servicesQuery2 = ' and  vv.servisid in  ('.$_GET['src'].')   ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT 
        TO_CHAR(sum(yagsatistutar), '999,999,999,999,999') yagsatistutar
            --sum(yagsatistutar)     yagsatistutar
         FROM (  
         SELECT   
                     to_char(tarihicin.tar) tarih ,
                     nvl(data1.yagsatistutar,0) yagsatistutar   
                       from vt_servisler vv  
                      left join (
                        select distinct 
                            to_date(x.tarih,'dd/mm/yyyy') tar   
                        from sason.tarihler x where
                              x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                      ) tarihicin on 1=1
                      LEFT JOIN (
                       select  distinct
                            a.servisid , 
                             to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') AS TARIH,
                        CASE
                                  WHEN SERVISSTOKTURID = 6
                                  THEN
                                    sum (tutar)
                               END  yagsatistutar 
                             
                            /*   CASE
                                  WHEN SERVISSTOKTURID <> 6
                                  THEN
                                    sum (tutar)
                               END  yedekparcatoplamsatis    
                               */
                         from sason.ypdata  a
                         WHERE 
                            -- a.servisid   in (94 )   and 
                            ".$servicesQuery."
                             a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                         GROUP BY to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') ,SERVISSTOKTURID ,servisid  
                      ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar 
                      WHERE 
                           vv.servisid  not in (1 , 134 , 136 )   
                           -- and  vv.servisid   in (94 ) 
                           ".$servicesQuery2."
                            AND vv.dilkod ='Turkish'
                   ) asd
              
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayYedekParcaYS($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "   
                

            SELECT 
               tarih ,
               sum(yagsatistutar) yagsatistutar  
            FROM (  
                SELECT   
                to_char(tarihicin.tar) tarih ,
                nvl(data1.yagsatistutar,0) yagsatistutar   
                  from vt_servisler vv  
                 left join (
                   select distinct 
                       to_date(x.tarih,'dd/mm/yyyy') tar   
                   from sason.tarihler x where
                         x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                 ) tarihicin on 1=1
                 LEFT JOIN (
                  select  distinct
                     --   count( a.servisid) as MIKTAR,
                        to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') AS TARIH,
                          CASE
                             WHEN SERVISSTOKTURID = 6
                             THEN
                               sum (tutar)
                          END  yagsatistutar 
                          /*,
                          CASE
                             WHEN SERVISSTOKTURID <> 6
                             THEN
                               sum (tutar)
                          END  yedekparcatoplamsatis */   
                    from sason.ypdata  a
                    WHERE
                        a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                    GROUP BY to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') ,SERVISSTOKTURID 
                 ) data1 on data1.TARIH = tarihicin.tar
                 WHERE 
                       vv.servisid   in (1 )  
                       AND vv.dilkod ='Turkish'
                       ) asd
                   group  by tarih   
             ORDER BY  tarih  asc
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
    public function getAfterSalesDetayYedekParcaYSWithServices($args = array()) {
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //servis.servisid =94 and 
            $servicesQuery = ' servis.servisid in ('.$_GET['src'].') and  ';
            // vv.servisid   in (94, 96, 98 )  AND
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT servisid , 
                      /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = asd.servisid) as servisad,  */ 
                         (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
                    tarih ,
                    sum(yagsatistutar)   yagsatistutar 
                 FROM (  
                           SELECT   vv.servisid,
                             to_char(tarihicin.tar) tarih ,
                             nvl(data1.yagsatistutar,0) yagsatistutar   
                           from vt_servisler vv  
                           left join (
                                select distinct 
                                    to_date(x.tarih,'dd/mm/yyyy') tar   
                                from sason.tarihler x where
                                      x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                            ) tarihicin on 1=1
                            LEFT JOIN (
                               select  distinct a.SIPARISSERVISID servisid,  
                                     to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') AS TARIH,
                                       CASE
                                          WHEN SERVISSTOKTURID = 6
                                          THEN
                                            sum (tutar)
                                       END  yagsatistutar 
                                    /*
                                       CASE
                                          WHEN SERVISSTOKTURID <> 6
                                          THEN
                                            sum (tutar)
                                       END  yedekparcatoplamsatis    */
                                 from sason.ypdata  a
                                 WHERE
                                     a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                 GROUP BY to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy') ,a.SERVISSTOKTURID ,a.SIPARISSERVISID
                              ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar 
                              WHERE 
                                    --vv.servisid   in (94, 96, 98 )  AND
                                    ".$servicesQuery2."
                                     vv.dilkod ='Turkish'
                         ) asd
                      group  by servisid,tarih   
                      ORDER BY  servisid,tarih  asc 
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
    public function getAfterSalesDetayYedekParcaYSAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try     {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT
                tarih,
                yil,            
                sum(yagsatistutar) yagsatistutar
              FROM ( 

              SELECT   distinct
                            tarihicin.tar as tarih,
                            tarihicin.yil as yil,            
                            nvl(data1.yagsatistutar,0) yagsatistutar
                          from vt_servisler vv  
                         left join (
                           select distinct 
                                to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                to_number(to_char(x.tarih,'yyyy')) yil  
                           from sason.tarihler x WHERE 
                                 to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                 x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                         ) tarihicin on 1=1             
                         LEFT JOIN (
                          select  distinct
                              to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')) yil,  
                              to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww')) tarih,  
                                  CASE
                                     WHEN SERVISSTOKTURID = 6
                                     THEN
                                       sum (tutar)
                                  END  yagsatistutar 
                                  /*,
                                  CASE
                                     WHEN SERVISSTOKTURID <> 6
                                     THEN
                                       sum (tutar)
                                  END  yedekparcatoplamsatis */   
                            from sason.ypdata  a
                            WHERE 
                               to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                               a.YEDEKPARCARAPORTARIHI  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                            GROUP BY   to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')), to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww')) ,SERVISSTOKTURID 
                         ) data1 on data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                         WHERE 
                             vv.servisid   in (1) and
                             vv.dilkod ='Turkish'  
                         ORDER BY  tarih desc 
                         ) ASD 
                group by tarih , yil 
                ORDER BY tarih asc, yil asc   

            ";
             
            $statement = $pdo->prepare($sql);  
           // print_r($sql);
            
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
    public function getAfterSalesDetayYedekParcaYSAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //zx.servis =94 AND 
            $servicesQuery = ' zx.servis in ('.$_GET['src'].') and  ';
            // vv.servisid   in (94, 96, 98) and 
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT servisid , 
                /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = asd.servisid) as servisad,  */ 
                   (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
                tarih,
                yil,            
                sum(yagsatistutar) yagsatistutar
              FROM ( 

              SELECT   distinct vv.servisid,
                            tarihicin.tar as tarih,
                            tarihicin.yil as yil,            
                            nvl(data1.yagsatistutar,0) yagsatistutar
                          from vt_servisler vv  
                         left join (
                           select distinct 
                                to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                to_number(to_char(x.tarih,'yyyy')) yil  
                           from sason.tarihler x WHERE 
                                 to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                 x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                         ) tarihicin on 1=1             
                         LEFT JOIN (
                          select  distinct a.SIPARISSERVISID servisid, 
                              to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')) yil,  
                              to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww')) tarih,  
                                  CASE
                                     WHEN SERVISSTOKTURID = 6
                                     THEN
                                       sum (tutar)
                                  END  yagsatistutar 
                                  /*,
                                  CASE
                                     WHEN SERVISSTOKTURID <> 6
                                     THEN
                                       sum (tutar)
                                  END  yedekparcatoplamsatis */   
                            from sason.ypdata  a
                            WHERE 
                               to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                               a.YEDEKPARCARAPORTARIHI  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                            GROUP BY   to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')), to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'ww')) ,SERVISSTOKTURID ,a.SIPARISSERVISID
                         ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                         WHERE 
                            vv.servisid not in (1,134,136) and 
                            --vv.servisid   in (94, 96, 98) and 
                            ".$servicesQuery2."
                            vv.dilkod ='Turkish'  
                         ORDER BY  tarih desc 
                         ) ASD 
             group by servisid,tarih, yil 
             ORDER BY  servisid,tarih asc, yil  asc   

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
    public function getAfterSalesDetayYedekParcaYSYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT
            ay,
            yil,            
            sum(yagsatistutar) yagsatistutar
                FROM ( 
                 SELECT  
                              tarihicin.ay as ay,
                              tarihicin.yil as yil,            
                              nvl(data1.yagsatistutar,0) yagsatistutar  
                            from vt_servisler vv  
                           left join (
                             select distinct  
                                  to_number(to_char(x.tarih,'yyyy')) yil  ,
                                  to_number(to_char(x.tarih,'MM')) ay 
                             from sason.tarihler x WHERE  
                                   x.tarih  between  to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')

                           ) tarihicin on 1=1
                           LEFT JOIN (
                            select  distinct
                                to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')) yil,  
                                to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'MM')) ay,  
                                    CASE
                                       WHEN SERVISSTOKTURID = 6
                                       THEN
                                         sum (tutar)
                                    END  yagsatistutar 
                                    /*,
                                    CASE
                                       WHEN SERVISSTOKTURID <> 6
                                       THEN
                                         sum (tutar)
                                    END  yedekparcatoplamsatis */   
                              from sason.ypdata  a
                              WHERE  
                                a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                              GROUP BY  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')), to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'MM')) ,SERVISSTOKTURID 

                           ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                     WHERE 
                         vv.servisid in (1) and  
                         vv.dilkod ='Turkish'  
                     ORDER BY yil, ay  
             
                          ) ASD 
             group by yil, ay     
             order by    yil, ay
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
    public function getAfterSalesDetayYedekParcaYSYillikWithServices($args = array()) {
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' zx.servis  in ('.$_GET['src'].') and  ';
            //vv.servisid   in (94, 96, 98) and
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
                SELECT servisid , 
                --(Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = asd.servisid) as servisad, 
                (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
                    ay,
                    yil,            
                    sum(yagsatistutar) yagsatistutar
                  FROM (     
                        SELECT  vv.servisid,
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.yagsatistutar,0) yagsatistutar  
                        from vt_servisler vv  
                         left join (
                               select distinct 
                                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                                    to_number(to_char(x.tarih,'MM')) ay 
                               from sason.tarihler x WHERE  
                                     x.tarih  between  to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                             ) tarihicin on 1=1
                             LEFT JOIN (
                              select  distinct a.SIPARISSERVISID servisid, 
                                  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')) yil,  
                                  to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'MM')) ay,  
                                      CASE
                                         WHEN SERVISSTOKTURID = 6
                                         THEN
                                           sum (tutar)
                                      END  yagsatistutar 
                                      /*,
                                      CASE
                                         WHEN SERVISSTOKTURID <> 6
                                         THEN
                                           sum (tutar)
                                      END  yedekparcatoplamsatis */
                                from sason.ypdata  a
                                WHERE  
                                  a.YEDEKPARCARAPORTARIHI BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                GROUP BY  to_number(to_char(a.YEDEKPARCARAPORTARIHI,'yyyy')), to_number(to_char(to_date(a.YEDEKPARCARAPORTARIHI, 'dd/mm/yyyy'), 'MM')) ,SERVISSTOKTURID  ,a.SIPARISSERVISID

                             ) data1 on  data1.servisid = vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                             WHERE 
                                  vv.servisid not in (1,134,136) and 
                                --vv.servisid   in (94, 96, 98) and
                                ".$servicesQuery2."
                                 vv.dilkod ='Turkish' 
                             ORDER BY servisid,yil, ay     
            ) ASD 
             group by servisid, yil, ay 
             order by servisid, yil, ay    
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
    public function getAfterSalesDashboardAtolyeCirosu($args = array()) {
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select 
 
                    TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari

                    from ( 
                    select  
                    /*
                    1 - isemri 
                    2 - icmal
                    3 - satis
                    */  

                      CASE
                             WHEN faturaturid= 3
                             THEN
                               sum (toplam)
                          END  direksatistutar,

                          CASE
                             WHEN faturaturid = 1
                             THEN
                               sum (toplam)
                          END  atolyecirosucari  , 
                          CASE
                             WHEN faturaturid = 2
                             THEN
                               sum (toplam)
                          END  atolyecirosugaranti  

                    from faturalar 
                    where  
                        durumid=1 and faturaturid in(1,2,3) and
                        islemtarihi   < to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 


                    group  by  faturaturid 
                    ) asd  
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardAtolyeCirosuWithServices($args = array()) {
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // and servisid in (94,96)
            $servicesQuery = ' and servisid in ('.$_GET['src'].')   ';
            // vv.servisid   in (94, 96, 98 )  AND
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            select TO_CHAR(ROUND(sum(de.atolyecirosucari2),0), '999,999,999,999,999') atolyecirosucari from(
                    select  servisid ,  
                    (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad,  
                    TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari, 
                    sum(nvl(atolyecirosucari,0)) atolyecirosucari2
                    
                    from ( 
                    select    
                    /*
                    1 - isemri 
                    2 - icmal
                    3 - satis
                    */  
                    servisid  , 
                      CASE
                             WHEN faturaturid= 3
                             THEN
                               sum (toplam)
                          END  direksatistutar,

                          CASE
                             WHEN faturaturid = 1
                             THEN
                               sum (toplam)
                          END  atolyecirosucari  , 
                          CASE
                             WHEN faturaturid = 2
                             THEN
                               sum (toplam)
                          END  atolyecirosugaranti  

                    from faturalar 
                    where  
                        durumid=1 and faturaturid in(1,2,3) and
                        islemtarihi   < to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                        --and servisid in (94,96)  
                        ".$servicesQuery."

                    group  by faturaturid , servisid 
                    ) asd 
                    group by  servisid  ) de
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayAtolyeCirosu($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "   
                SELECT  
                    tarih,        
                     --sum(nvl(atolyecirosucari,0)) atolyecirosucari
                     TO_CHAR(sum(nvl(atolyecirosucari,0)), '999,999,999,999,999') atolyecirosucari
                 FROM (  
                           SELECT   vv.servisid,
                             to_char(tarihicin.tar) tarih ,
                             nvl(data1.direksatistutar,0) direksatistutar,
                              nvl(data1.atolyecirosucari,0) atolyecirosucari,   
                               nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti      
                           from vt_servisler vv  
                           left join (
                                select distinct 
                                    to_date(x.tarih,'dd/mm/yyyy') tar   
                                from sason.tarihler x where
                                      x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                            ) tarihicin on 1=1
                            LEFT JOIN (
                               select  distinct    
                                     tarihh AS TARIH,                
                                     ROUND(sum(nvl(direksatistutar,0)), 0) direksatistutar,                             
                                     ROUND(sum(nvl(atolyecirosucari,0)), 0) atolyecirosucari,                             
                                     ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                     from ( 
                                     select   
                                     /*
                                     1 - isemri 
                                     2 - icmal
                                     3 - satis
                                     */  
                                     to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                       CASE
                                              WHEN faturaturid= 3
                                              THEN
                                                sum (a.toplam)
                                           END  direksatistutar,

                                           CASE
                                              WHEN faturaturid = 1
                                              THEN
                                                sum (a.toplam)
                                           END  atolyecirosucari  , 
                                           CASE
                                              WHEN faturaturid = 2
                                              THEN
                                                sum (a.toplam)
                                           END  atolyecirosugaranti  

                                     from faturalar a 
                                     where   
                                         a.durumid=1 and a.faturaturid in(1,2,3) and
                                         a.islemtarihi BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                     GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid  
                                     ) asd
                                 GROUP BY asd.TARIHh  

                              ) data1 on    data1.TARIH = tarihicin.tar 
                              WHERE   
                                     vv.servisid in (1) and 
                                     vv.dilkod ='Turkish'
                         ) asd
                      group  by tarih   
                      ORDER BY  tarih  asc
            
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
    public function getAfterSalesDetayAtolyeCirosuWithServices($args = array()) {
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid   in (94 ) and 
            $servicesQuery = '  a.servisid in ('.$_GET['src'].') and  ';
            // and vv.servisid   in (94 ) 
            $servicesQuery2 = '  and vv.servisid in  ('.$_GET['src'].')   ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                        SELECT servisid , (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad,
                        tarih,

                        sum(atolyecirosucari)   atolyecirosucari

                     FROM (  
                               SELECT   vv.servisid,
                                 to_char(tarihicin.tar) tarih ,
                                 nvl(data1.direksatistutar,0) direksatistutar,
                                  nvl(data1.atolyecirosucari,0) atolyecirosucari,   
                                   nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti      
                               from vt_servisler vv  
                               left join (
                                    select distinct 
                                        to_date(x.tarih,'dd/mm/yyyy') tar   
                                    from sason.tarihler x where
                                          x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                ) tarihicin on 1=1
                                LEFT JOIN (
                                   select  distinct  servisid,  
                                         tarihh AS TARIH,                
                                         TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar,                             
                                         TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari,                             
                                         TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti                              
                                         from ( 
                                         select   servisid ,  
                                         /*
                                         1 - isemri 
                                         2 - icmal
                                         3 - satis
                                         */  
                                         to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                           CASE
                                                  WHEN faturaturid= 3
                                                  THEN
                                                    sum (a.toplam)
                                               END  direksatistutar,

                                               CASE
                                                  WHEN faturaturid = 1
                                                  THEN
                                                    sum (a.toplam)
                                               END  atolyecirosucari  , 
                                               CASE
                                                  WHEN faturaturid = 2
                                                  THEN
                                                    sum (a.toplam)
                                               END  atolyecirosugaranti  

                                         from faturalar a 
                                         where  
                                             --a.servisid   in (94 ) and 
                                             ".$servicesQuery."
                                             a.durumid=1 and a.faturaturid in(1,2,3) and
                                             a.islemtarihi BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                         GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid ,a.SERVISID
                                         ) asd
                                     GROUP BY asd.TARIHh , asd.SERVISID

                                  ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar 
                                  WHERE 
                                         vv.servisid not in (1,134,136)  
                                         -- and vv.servisid   in (94 ) 
                                         ".$servicesQuery2."
                                         AND vv.dilkod ='Turkish'
                             ) asd
                          group  by servisid,tarih   
                        ORDER BY  tarih  asc
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
    public function getAfterSalesDetayAtolyeCirosuAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try     {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT  
                tarih,
                yil, 
                TO_CHAR(ROUND(sum(nvl( atolyecirosucari ,0)), 0), '999,999,999,999,999') atolyecirosucari

              FROM (
                        SELECT   distinct vv.servisid,
                            tarihicin.tar as tarih,
                            tarihicin.yil as yil,            
                            nvl(data1.direksatistutar,0) direksatistutar,
                             nvl(data1.atolyecirosucari,0) atolyecirosucari,
                              nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti
                          from vt_servisler vv  
                         left join (
                           select distinct 
                                to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                to_number(to_char(x.tarih,'yyyy')) yil  
                           from sason.tarihler x WHERE 
                                 to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                 x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                         ) tarihicin on 1=1             
                         LEFT JOIN (
                          select  distinct  
                                yil,  
                                hafta tarih,  
                                ROUND(sum(nvl(direksatistutar,0)), 0) direksatistutar,                             
                                ROUND(sum(nvl(atolyecirosucari,0)), 0) atolyecirosucari,                             
                                ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                from ( 
                                    select    
                                    /*
                                    1 - isemri 
                                    2 - icmal
                                    3 - satis
                                    */  
                                    to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                    to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                    to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww')) hafta, 
                                      CASE
                                             WHEN faturaturid= 3
                                             THEN
                                               sum (a.toplam)
                                          END  direksatistutar,

                                          CASE
                                             WHEN faturaturid = 1
                                             THEN
                                               sum (a.toplam)
                                          END  atolyecirosucari  , 
                                          CASE
                                             WHEN faturaturid = 2
                                             THEN
                                               sum (a.toplam)
                                          END  atolyecirosugaranti  

                                    from faturalar a 
                                    where  
                                        a.servisid not  in (1,134,136 ) and  
                                        a.durumid=1 and a.faturaturid in(1,2,3) and 
                                        to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                                        a.islemtarihi  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 

                                    GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')), to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))
                                ) asd 

                            GROUP BY yil, hafta 
                         ) data1 on  data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                         WHERE  
                            vv.servisid in (1) and   
                            vv.dilkod ='Turkish'  
                         ) ASD 
                         group by tarih, yil         
                         order by tarih, yil          
            ";
             
            $statement = $pdo->prepare($sql);  
           // print_r($sql);
            
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
    public function getAfterSalesDetayAtolyeCirosuAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid   in (94, 96, 98 ) and
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // vv.servisid   in (94,96, 98) and 
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
                SELECT servisid ,  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
                    tarih,
                    yil,            
                    sum( atolyecirosucari ) atolyecirosucari
                  FROM ( 

                  SELECT   distinct vv.servisid,
                                tarihicin.tar as tarih,
                                tarihicin.yil as yil,            
                                nvl(data1.direksatistutar,0) direksatistutar,
                                 nvl(data1.atolyecirosucari,0) atolyecirosucari,
                                  nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti
                              from vt_servisler vv  
                             left join (
                               select distinct 
                                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                    to_number(to_char(x.tarih,'yyyy')) yil  
                               from sason.tarihler x WHERE 
                                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                     x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                             ) tarihicin on 1=1             
                             LEFT JOIN (
                              select  distinct servisid, 
                                    yil,  
                                    hafta tarih,  
                                    TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar,                             
                                    TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari,                             
                                    TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti                              
                                    from ( 
                                        select   servisid ,  
                                        /*
                                        1 - isemri 
                                        2 - icmal
                                        3 - satis
                                        */  
                                        to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                        to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                        to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww')) hafta, 
                                          CASE
                                                 WHEN faturaturid= 3
                                                 THEN
                                                   sum (a.toplam)
                                              END  direksatistutar,

                                              CASE
                                                 WHEN faturaturid = 1
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosucari  , 
                                              CASE
                                                 WHEN faturaturid = 2
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosugaranti  

                                        from faturalar a 
                                        where  
                                            --a.servisid   in (94, 96, 98 ) and 
                                            ".$servicesQuery."
                                            a.durumid=1 and a.faturaturid in(1,2,3) and 
                                            to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                                            a.islemtarihi  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 

                                        GROUP BY a.SERVISID, to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')), to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))
                                    ) asd



                                GROUP BY   yil, hafta ,SERVISID
                             ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                             WHERE 
                                vv.servisid not in (1,134,136) and 
                                --vv.servisid   in (94,96, 98) and  
                                ".$servicesQuery2."
                                vv.dilkod ='Turkish'  
                             ORDER BY  tarih desc 
                             ) ASD 
                             group by servisid,tarih, yil         
                             order by servisid,tarih, yil 

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
    public function getAfterSalesDetayAtolyeCirosuYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT  
                    ay,
                    yil,    
                    TO_CHAR(ROUND(sum(nvl( atolyecirosucari ,0)), 0), '999,999,999,999,999') atolyecirosucari

                  FROM (     
                        SELECT  vv.servisid,
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.direksatistutar,0) direksatistutar,
                            nvl(data1.atolyecirosucari,0) atolyecirosucari,
                            nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti    
                        from vt_servisler vv  
                         left join (
                               select distinct 
                                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                                    to_number(to_char(x.tarih,'MM')) ay
                               from sason.tarihler x WHERE  
                                     --x.tarih between to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                             ) tarihicin on 1=1
                             LEFT JOIN ( 
                               select  distinct  
                                    yil,  
                                    to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM')) ay,  
                                    ROUND(sum(nvl(direksatistutar,0)), 0)  direksatistutar,                             
                                     ROUND(sum(nvl(atolyecirosucari,0)), 0)  atolyecirosucari,                             
                                    ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                    from ( 
                                        select     
                                        /*
                                        1 - isemri 
                                        2 - icmal
                                        3 - satis
                                        */  
                                        to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                        to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                          CASE
                                                 WHEN faturaturid= 3
                                                 THEN
                                                   sum (a.toplam)
                                              END  direksatistutar,

                                              CASE
                                                 WHEN faturaturid = 1
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosucari  , 
                                              CASE
                                                 WHEN faturaturid = 2
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosugaranti  

                                        from faturalar a 
                                        where  
                                            a.servisid  not in (1,134,136 ) and  
                                            a.durumid=1 and a.faturaturid in(1,2,3) and  
                                         --   a.islemtarihi BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                            a.islemtarihi BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                              
                                        GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')) 
                                    ) asd

                                GROUP BY  yil ,to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM'))  
                             ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                             WHERE 
                                vv.servisid   in (1) and
                                vv.dilkod ='Turkish' 
                             ORDER BY yil, ay     
                            ) ASD 
                             group by yil, ay 
                             order by yil, ay 
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
    public function getAfterSalesDetayAtolyeCirosuYillikWithServices($args = array()) {
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid   in (94, 96, 98 ) and 
            $servicesQuery = ' a.servisid  in ('.$_GET['src'].') and  ';
            // vv.servisid   in (94, 96, 98) and
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
                



SELECT servisid  ,  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
    ay,
    yil,    
                            
    TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari     
    
  FROM (     
        SELECT  vv.servisid,
            tarihicin.ay as ay,
            tarihicin.yil as yil,            
            nvl(data1.direksatistutar,0) direksatistutar,
            nvl(data1.atolyecirosucari,0) atolyecirosucari,
            nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti    
        from vt_servisler vv  
         left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay
               from sason.tarihler x WHERE  
                     --x.tarih between to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select  distinct servisid, 
                    yil,  
                    to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM')) ay,  
                    ROUND(sum(nvl(direksatistutar,0)), 0)  direksatistutar,                             
                     ROUND(sum(nvl(atolyecirosucari,0)), 0)  atolyecirosucari,                             
                    ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                    from ( 
                        select   servisid ,  
                        /*
                        1 - isemri 
                        2 - icmal
                        3 - satis
                        */  
                        to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                        to_number(to_char(a.islemtarihi,'yyyy')) yil,
                          CASE
                                 WHEN faturaturid= 3
                                 THEN
                                   sum (a.toplam)
                              END  direksatistutar,
                                                           
                              CASE
                                 WHEN faturaturid = 1
                                 THEN
                                   sum (a.toplam)
                              END  atolyecirosucari  , 
                              CASE
                                 WHEN faturaturid = 2
                                 THEN
                                   sum (a.toplam)
                              END  atolyecirosugaranti  

                        from faturalar a 
                        where  
                           -- a.servisid   in (94 ) and 
                           ".$servicesQuery."
                            a.durumid=1 and a.faturaturid in(1,2,3) and  
                         --   a.islemtarihi BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                            a.islemtarihi BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                              
                        GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid ,a.SERVISID, to_number(to_char(a.islemtarihi,'yyyy')) 
                    ) asd
              
                GROUP BY SERVISID,yil ,to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM'))  
             ) data1 on  data1.servisid = vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                  vv.servisid not in (1,134,136) and 
                --vv.servisid   in (94) and
                ".$servicesQuery2."
                 vv.dilkod ='Turkish' 
             ORDER BY servisid,yil, ay     
            ) ASD 
             group by servisid, yil, ay 
             order by servisid, yil, ay 
                
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
    public function getAfterSalesDashboardGarantiCirosu($args = array()) {
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select  
                    TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti 

                    from ( 
                    select  
                    /*
                    1 - isemri 
                    2 - icmal
                    3 - satis
                    */  

                      CASE
                             WHEN faturaturid= 3
                             THEN
                               sum (toplam)
                          END  direksatistutar,

                          CASE
                             WHEN faturaturid = 1
                             THEN
                               sum (toplam)
                          END  atolyecirosucari  , 
                          CASE
                             WHEN faturaturid = 2
                             THEN
                               sum (toplam)
                          END  atolyecirosugaranti  

                    from faturalar 
                    where  
                        durumid=1 and faturaturid in(1,2,3) and
                        islemtarihi   < to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                    group  by  faturaturid 
                    ) asd 
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardGarantiCirosuWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and servisid in (94,96)   
            $servicesQuery = ' and  servisid in ('.$_GET['src'].')  ';
            // vv.servisid   in (94, 96, 98 )  AND
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = " 
             select TO_CHAR(ROUND(sum(de.atolyecirosugaranti2),0), '999,999,999,999,999') atolyecirosugaranti from(   
                select  servisid ,  
                (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad,  
                        TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti, 
                        sum(nvl(atolyecirosugaranti,0)) atolyecirosugaranti2
                        from ( 
                        select    
                        /*
                        1 - isemri 
                        2 - icmal
                        3 - satis
                        */  
                        servisid  , 
                          CASE
                                 WHEN faturaturid= 3
                                 THEN
                                   sum (toplam)
                              END  direksatistutar,

                              CASE
                                 WHEN faturaturid = 1
                                 THEN
                                   sum (toplam)
                              END  atolyecirosucari  , 
                              CASE
                                 WHEN faturaturid = 2
                                 THEN
                                   sum (toplam)
                              END  atolyecirosugaranti  

                        from faturalar 
                        where  
                            durumid=1 and faturaturid in(1,2,3) and
                            islemtarihi   < to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                            --and servisid in (94,96)  
                            ".$servicesQuery."
                        group  by  servisid ,faturaturid 
                        ) asd 
                        group by  servisid ) de  
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayGarantiCirosu($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "   
            SELECT  
               tarih,  
                --TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti    
                TO_CHAR(sum(nvl(atolyecirosugaranti,0)), '999,999,999,999,999') atolyecirosugaranti            
            FROM (  
                      SELECT   vv.servisid,
                        to_char(tarihicin.tar) tarih ,
                        nvl(data1.direksatistutar,0) direksatistutar,
                         nvl(data1.atolyecirosucari,0) atolyecirosucari,   
                          nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti      
                      from vt_servisler vv  
                      left join (
                           select distinct 
                               to_date(x.tarih,'dd/mm/yyyy') tar   
                           from sason.tarihler x where
                                 x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                       ) tarihicin on 1=1
                       LEFT JOIN (
                          select  distinct    
                                tarihh AS TARIH,                
                                ROUND(sum(nvl(direksatistutar,0)), 0) direksatistutar,                             
                                ROUND(sum(nvl(atolyecirosucari,0)), 0) atolyecirosucari,                             
                                ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                from ( 
                                select   
                                /*
                                1 - isemri 
                                2 - icmal
                                3 - satis
                                */  
                                to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                  CASE
                                         WHEN faturaturid= 3
                                         THEN
                                           sum (a.toplam)
                                      END  direksatistutar,

                                      CASE
                                         WHEN faturaturid = 1
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosucari  , 
                                      CASE
                                         WHEN faturaturid = 2
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosugaranti  

                                from faturalar a 
                                where   
                                    a.durumid=1 and a.faturaturid in(1,2,3) and
                                    a.islemtarihi BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid  
                                ) asd
                            GROUP BY asd.TARIHh  

                         ) data1 on    data1.TARIH = tarihicin.tar 
                         WHERE   
                                vv.servisid in (1) and 
                                vv.dilkod ='Turkish'
                    ) asd
                 group  by tarih   
                 ORDER BY  tarih  asc
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
    public function getAfterSalesDetayGarantiCirosuWithServices($args = array()) {

        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            // a.servisid   in (94 ) and
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // and vv.servisid   in (94 ) 
            $servicesQuery2 = ' and  vv.servisid in  ('.$_GET['src'].')   ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    

            SELECT servisid , 
                (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad,
               tarih,
               sum(atolyecirosugaranti)   atolyecirosugaranti 
            FROM (  
                      SELECT   vv.servisid,
                        to_char(tarihicin.tar) tarih ,
                        nvl(data1.direksatistutar,0) direksatistutar,
                         nvl(data1.atolyecirosucari,0) atolyecirosucari,   
                          nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti      
                      from vt_servisler vv  
                      left join (
                           select distinct 
                               to_date(x.tarih,'dd/mm/yyyy') tar   
                           from sason.tarihler x where
                                 x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                       ) tarihicin on 1=1
                       LEFT JOIN (
                          select  distinct  servisid,  
                                tarihh AS TARIH,                
                                TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar,                             
                                TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari,                             
                                TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti                              
                                from ( 
                                select   servisid ,  
                                /*
                                1 - isemri 
                                2 - icmal
                                3 - satis
                                */  
                                to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                  CASE
                                         WHEN faturaturid= 3
                                         THEN
                                           sum (a.toplam)
                                      END  direksatistutar,

                                      CASE
                                         WHEN faturaturid = 1
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosucari  , 
                                      CASE
                                         WHEN faturaturid = 2
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosugaranti  

                                from faturalar a 
                                where  
                                    --a.servisid   in (94 ) and
                                    ".$servicesQuery."
                                    a.durumid=1 and a.faturaturid in(1,2,3) and
                                    a.islemtarihi BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid ,a.SERVISID
                                ) asd
                            GROUP BY asd.TARIHh , asd.SERVISID

                         ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar 
                         WHERE 
                                vv.servisid not in (1,134,136)  
                                --and vv.servisid   in (94 ) 
                                ".$servicesQuery2."
                                AND vv.dilkod ='Turkish'
                    ) asd
                 group  by servisid,tarih   
                 ORDER BY  servisid,tarih  asc
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
    public function getAfterSalesDetayGarantiCirosuAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try     {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                   SELECT  
                    tarih,
                    yil, 
                    TO_CHAR(ROUND(sum(nvl(  atolyecirosugaranti ,0)), 0), '999,999,999,999,999')  atolyecirosugaranti

                  FROM (
                            SELECT   distinct vv.servisid,
                                tarihicin.tar as tarih,
                                tarihicin.yil as yil,            
                                nvl(data1.direksatistutar,0) direksatistutar,
                                 nvl(data1.atolyecirosucari,0) atolyecirosucari,
                                  nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti
                              from vt_servisler vv  
                             left join (
                               select distinct 
                                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                    to_number(to_char(x.tarih,'yyyy')) yil  
                               from sason.tarihler x WHERE 
                                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                     x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                             ) tarihicin on 1=1             
                             LEFT JOIN (
                              select  distinct  
                                    yil,  
                                    hafta tarih,  
                                    ROUND(sum(nvl(direksatistutar,0)), 0) direksatistutar,                             
                                    ROUND(sum(nvl(atolyecirosucari,0)), 0) atolyecirosucari,                             
                                    ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                    from ( 
                                        select    
                                        /*
                                        1 - isemri 
                                        2 - icmal
                                        3 - satis
                                        */  
                                        to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                        to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                        to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww')) hafta, 
                                          CASE
                                                 WHEN faturaturid= 3
                                                 THEN
                                                   sum (a.toplam)
                                              END  direksatistutar,

                                              CASE
                                                 WHEN faturaturid = 1
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosucari  , 
                                              CASE
                                                 WHEN faturaturid = 2
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosugaranti  

                                        from faturalar a 
                                        where  
                                            a.servisid not  in (1,134,136 ) and  
                                            a.durumid=1 and a.faturaturid in(1,2,3) and 
                                            to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                                            a.islemtarihi  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 

                                        GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')), to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))
                                    ) asd 

                                GROUP BY yil, hafta 
                             ) data1 on  data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                             WHERE  
                                vv.servisid in (1) and   
                                vv.dilkod ='Turkish'  
                             ) ASD 
                             group by tarih, yil         
                             order by tarih, yil          

            ";
             
            $statement = $pdo->prepare($sql);  
           // print_r($sql);
            
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
    public function getAfterSalesDetayGarantiCirosuAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //zx.servis =94 AND 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // vv.servisid   in (94, 96, 98) and 
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
           SELECT servisid ,  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
    tarih,
    yil,            
    sum(  atolyecirosugaranti )  atolyecirosugaranti
  FROM ( 
  
  SELECT   distinct vv.servisid,
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.direksatistutar,0) direksatistutar,
                 nvl(data1.atolyecirosucari,0) atolyecirosucari,
                  nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE 
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                     x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
             ) tarihicin on 1=1             
             LEFT JOIN (
              select  distinct servisid, 
                    yil,  
                    hafta tarih,  
                    TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar,                             
                    TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari,                             
                    TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti                              
                    from ( 
                        select   servisid ,  
                        /*
                        1 - isemri 
                        2 - icmal
                        3 - satis
                        */  
                        to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                        to_number(to_char(a.islemtarihi,'yyyy')) yil,
                        to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww')) hafta, 
                          CASE
                                 WHEN faturaturid= 3
                                 THEN
                                   sum (a.toplam)
                              END  direksatistutar,
                                                           
                              CASE
                                 WHEN faturaturid = 1
                                 THEN
                                   sum (a.toplam)
                              END  atolyecirosucari  , 
                              CASE
                                 WHEN faturaturid = 2
                                 THEN
                                   sum (a.toplam)
                              END  atolyecirosugaranti  

                        from faturalar a 
                        where  
                            --a.servisid   in (94 ) and 
                            ".$servicesQuery."
                            a.durumid=1 and a.faturaturid in(1,2,3) and 
                            to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                            a.islemtarihi  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                               
                        GROUP BY a.SERVISID, to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')), to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))
                    ) asd
             
               
                
                GROUP BY   yil, hafta ,SERVISID
             ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                vv.servisid not in (1,134,136) and 
                --vv.servisid   in (94) and  
                ".$servicesQuery2."
                vv.dilkod ='Turkish'  
             ORDER BY  tarih desc 
             ) ASD 
             group by servisid,tarih, yil         
             order by servisid,tarih, yil          
  

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
    public function getAfterSalesDetayGarantiCirosuYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT  
                    ay,
                    yil,    
                    TO_CHAR(ROUND(sum(nvl(  atolyecirosugaranti ,0)), 0), '999,999,999,999,999')  atolyecirosugaranti    

                  FROM (     
                        SELECT  vv.servisid,
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.direksatistutar,0) direksatistutar,
                            nvl(data1.atolyecirosucari,0) atolyecirosucari,
                            nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti    
                        from vt_servisler vv  
                         left join (
                               select distinct 
                                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                                    to_number(to_char(x.tarih,'MM')) ay
                               from sason.tarihler x WHERE  
                                     --x.tarih between to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                             ) tarihicin on 1=1
                             LEFT JOIN ( 
                               select  distinct  
                                    yil,  
                                    to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM')) ay,  
                                    ROUND(sum(nvl(direksatistutar,0)), 0)  direksatistutar,                             
                                     ROUND(sum(nvl(atolyecirosucari,0)), 0)  atolyecirosucari,                             
                                    ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                    from ( 
                                        select     
                                        /*
                                        1 - isemri 
                                        2 - icmal
                                        3 - satis
                                        */  
                                        to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                        to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                          CASE
                                                 WHEN faturaturid= 3
                                                 THEN
                                                   sum (a.toplam)
                                              END  direksatistutar,

                                              CASE
                                                 WHEN faturaturid = 1
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosucari  , 
                                              CASE
                                                 WHEN faturaturid = 2
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosugaranti  

                                        from faturalar a 
                                        where  
                                            a.servisid  not in (1,134,136 ) and  
                                            a.durumid=1 and a.faturaturid in(1,2,3) and  
                                         --   a.islemtarihi BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                            a.islemtarihi BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                              
                                        GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')) 
                                    ) asd

                                GROUP BY  yil ,to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM'))  
                             ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                             WHERE 
                                vv.servisid   in (1) and
                                vv.dilkod ='Turkish' 
                             ORDER BY yil, ay     
                            ) ASD 
                             group by yil, ay 
                             order by yil, ay 
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
    public function getAfterSalesDetayGarantiCirosuYillikWithServices($args = array()) {
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' a.servisid  in ('.$_GET['src'].') and  ';
            //vv.servisid   in (94, 96, 98) and
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
                

            SELECT servisid  ,  
            (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
            ay,
            yil,      
            TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti        

          FROM (     
                SELECT  vv.servisid,
                    tarihicin.ay as ay,
                    tarihicin.yil as yil,            
                    nvl(data1.direksatistutar,0) direksatistutar,
                    nvl(data1.atolyecirosucari,0) atolyecirosucari,
                    nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti    
                from vt_servisler vv  
                 left join (
                       select distinct 
                            to_number(to_char(x.tarih,'yyyy')) yil  ,
                            to_number(to_char(x.tarih,'MM')) ay
                       from sason.tarihler x WHERE  
                             --x.tarih between to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                             x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                     ) tarihicin on 1=1
                     LEFT JOIN ( 
                       select  distinct servisid, 
                            yil,  
                            to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM')) ay,  
                            ROUND(sum(nvl(direksatistutar,0)), 0)  direksatistutar,                             
                             ROUND(sum(nvl(atolyecirosucari,0)), 0)  atolyecirosucari,                             
                            ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                            from ( 
                                select   servisid ,  
                                /*
                                1 - isemri 
                                2 - icmal
                                3 - satis
                                */  
                                to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                  CASE
                                         WHEN faturaturid= 3
                                         THEN
                                           sum (a.toplam)
                                      END  direksatistutar,

                                      CASE
                                         WHEN faturaturid = 1
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosucari  , 
                                      CASE
                                         WHEN faturaturid = 2
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosugaranti  

                                from faturalar a 
                                where  
                                    --a.servisid   in (94, 96, 98 ) and
                                    ".$servicesQuery."
                                    a.durumid=1 and a.faturaturid in(1,2,3) and  
                                 --   a.islemtarihi BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                    a.islemtarihi BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                              
                                GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid ,a.SERVISID, to_number(to_char(a.islemtarihi,'yyyy')) 
                            ) asd

                        GROUP BY SERVISID,yil ,to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM'))  
                     ) data1 on  data1.servisid = vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                     WHERE 
                          vv.servisid not in (1,134,136) and 
                        --vv.servisid   in (94, 96, 98) and
                        ".$servicesQuery2."
                         vv.dilkod ='Turkish' 
                     ORDER BY servisid,yil, ay     
                    ) ASD 
                     group by servisid, yil, ay 
                     order by servisid, yil, ay 
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
    public function getAfterSalesDashboardDirekSatisCirosu($args = array()) {
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                   select 
                    TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar 

                    from ( 
                    select   
                    /*
                    1 - isemri 
                    2 - icmal
                    3 - satis
                    */  

                      CASE
                             WHEN faturaturid= 3
                             THEN
                               sum (toplam)
                          END  direksatistutar,

                          CASE
                             WHEN faturaturid = 1
                             THEN
                               sum (toplam)
                          END  atolyecirosucari  , 
                          CASE
                             WHEN faturaturid = 2
                             THEN
                               sum (toplam)
                          END  atolyecirosugaranti  

                    from faturalar 
                    where  
                        durumid=1 and faturaturid in(1,2,3) and
                        islemtarihi   < to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 


                    group  by  faturaturid 
                    ) asd 
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardDirekSatisCirosuWithServices($args = array()) {
           
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and servisid in (94,96)
            $servicesQuery = ' and servisid in ('.$_GET['src'].')   ';
            // vv.servisid   in (94, 96, 98 )  AND
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        }
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = " 
                select TO_CHAR(ROUND(sum(de.direksatistutar2),0), '999,999,999,999,999') direksatistutar from(  
                    select  servisid ,  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad,  
                    TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar,
                    sum(nvl(direksatistutar,0)) direksatistutar2
                    from ( 
                    select    
                    /*
                    1 - isemri 
                    2 - icmal
                    3 - satis
                    */  
                    servisid  , 
                      CASE
                             WHEN faturaturid= 3
                             THEN
                               sum (toplam)
                          END  direksatistutar,

                          CASE
                             WHEN faturaturid = 1
                             THEN
                               sum (toplam)
                          END  atolyecirosucari  , 
                          CASE
                             WHEN faturaturid = 2
                             THEN
                               sum (toplam)
                          END  atolyecirosugaranti  

                    from faturalar 
                    where  
                        durumid=1 and faturaturid in(1,2,3) and
                        islemtarihi   < to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                        --and servisid in (94,96)  
                        ".$servicesQuery."
                    group  by  servisid ,faturaturid 
                    ) asd 
                    group by  servisid ) de   
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayDirekSatisCirosu($args = array()) {
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "   
            SELECT  
               tarih, 
                TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar
            FROM (  
                      SELECT   vv.servisid,
                        to_char(tarihicin.tar) tarih ,
                        nvl(data1.direksatistutar,0) direksatistutar,
                         nvl(data1.atolyecirosucari,0) atolyecirosucari,   
                          nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti      
                      from vt_servisler vv  
                      left join (
                           select distinct 
                               to_date(x.tarih,'dd/mm/yyyy') tar   
                           from sason.tarihler x where
                                 x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                       ) tarihicin on 1=1
                       LEFT JOIN (
                          select  distinct    
                                tarihh AS TARIH,                
                                ROUND(sum(nvl(direksatistutar,0)), 0) direksatistutar,                             
                                ROUND(sum(nvl(atolyecirosucari,0)), 0) atolyecirosucari,                             
                                ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                from ( 
                                select   
                                /*
                                1 - isemri 
                                2 - icmal
                                3 - satis
                                */  
                                to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                  CASE
                                         WHEN faturaturid= 3
                                         THEN
                                           sum (a.toplam)
                                      END  direksatistutar,

                                      CASE
                                         WHEN faturaturid = 1
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosucari  , 
                                      CASE
                                         WHEN faturaturid = 2
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosugaranti  

                                from faturalar a 
                                where   
                                    a.durumid=1 and a.faturaturid in(1,2,3) and
                                    a.islemtarihi BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid  
                                ) asd
                            GROUP BY asd.TARIHh  

                         ) data1 on    data1.TARIH = tarihicin.tar 
                         WHERE   
                                vv.servisid in (1) and 
                                vv.dilkod ='Turkish'
                    ) asd
                 group  by tarih   
                 ORDER BY  tarih  asc
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
    public function getAfterSalesDetayDirekSatisCirosuWithServices($args = array()) {

        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid   in (94, 96, 98 ) and 
            $servicesQuery = ' a.servisid in ('.$_GET['src'].') and  ';
            // and vv.servisid   in (94, 96, 98 )  
            $servicesQuery2 = '  and vv.servisid in  ('.$_GET['src'].')   ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT servisid , (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad,
               tarih,
               sum(direksatistutar)   direksatistutar

            FROM (  
                      SELECT   vv.servisid,
                        to_char(tarihicin.tar) tarih ,
                        nvl(data1.direksatistutar,0) direksatistutar,
                         nvl(data1.atolyecirosucari,0) atolyecirosucari,   
                          nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti      
                      from vt_servisler vv  
                      left join (
                           select distinct 
                               to_date(x.tarih,'dd/mm/yyyy') tar   
                           from sason.tarihler x where
                                 x.tarih between  to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate-1, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                       ) tarihicin on 1=1
                       LEFT JOIN (
                          select  distinct  servisid,  
                                tarihh AS TARIH,                
                                TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar,                             
                                TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari,                             
                                TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti                              
                                from ( 
                                select   servisid ,  
                                /*
                                1 - isemri 
                                2 - icmal
                                3 - satis
                                */  
                                to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                  CASE
                                         WHEN faturaturid= 3
                                         THEN
                                           sum (a.toplam)
                                      END  direksatistutar,

                                      CASE
                                         WHEN faturaturid = 1
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosucari  , 
                                      CASE
                                         WHEN faturaturid = 2
                                         THEN
                                           sum (a.toplam)
                                      END  atolyecirosugaranti  

                                from faturalar a 
                                where  
                                    --a.servisid   in (94, 96, 98 ) and 
                                    ".$servicesQuery."
                                    a.durumid=1 and a.faturaturid in(1,2,3) and
                                    a.islemtarihi BETWEEN to_date(to_char(sysdate-7, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                                GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid ,a.SERVISID
                                ) asd
                            GROUP BY asd.TARIHh , asd.SERVISID

                         ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar 
                         WHERE 
                                vv.servisid not in (1,134,136)  
                                --and vv.servisid   in (94, 96, 98 )
                                ".$servicesQuery2."
                                AND vv.dilkod ='Turkish'
                    ) asd
                 group  by servisid,tarih   
                 ORDER BY  servisid,tarih  asc
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
    public function getAfterSalesDetayDirekSatisCirosuAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try     {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    SELECT  
                        tarih,
                        yil, 
                        TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar          

                      FROM (
                                SELECT   distinct vv.servisid,
                                    tarihicin.tar as tarih,
                                    tarihicin.yil as yil,            
                                    nvl(data1.direksatistutar,0) direksatistutar,
                                     nvl(data1.atolyecirosucari,0) atolyecirosucari,
                                      nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti
                                  from vt_servisler vv  
                                 left join (
                                   select distinct 
                                        to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                        to_number(to_char(x.tarih,'yyyy')) yil  
                                   from sason.tarihler x WHERE 
                                         to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                         x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                 ) tarihicin on 1=1             
                                 LEFT JOIN (
                                  select  distinct  
                                        yil,  
                                        hafta tarih,  
                                        ROUND(sum(nvl(direksatistutar,0)), 0) direksatistutar,                             
                                        ROUND(sum(nvl(atolyecirosucari,0)), 0) atolyecirosucari,                             
                                        ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                        from ( 
                                            select    
                                            /*
                                            1 - isemri 
                                            2 - icmal
                                            3 - satis
                                            */  
                                            to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                            to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                            to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww')) hafta, 
                                              CASE
                                                     WHEN faturaturid= 3
                                                     THEN
                                                       sum (a.toplam)
                                                  END  direksatistutar,

                                                  CASE
                                                     WHEN faturaturid = 1
                                                     THEN
                                                       sum (a.toplam)
                                                  END  atolyecirosucari  , 
                                                  CASE
                                                     WHEN faturaturid = 2
                                                     THEN
                                                       sum (a.toplam)
                                                  END  atolyecirosugaranti  

                                            from faturalar a 
                                            where  
                                                a.servisid not  in (1,134,136 ) and  
                                                a.durumid=1 and a.faturaturid in(1,2,3) and 
                                                to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                                                a.islemtarihi  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 

                                            GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')), to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))
                                        ) asd 

                                    GROUP BY yil, hafta 
                                 ) data1 on  data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                                 WHERE  
                                    vv.servisid in (1) and   
                                    vv.dilkod ='Turkish'  
                                 ) ASD 
                                 group by tarih, yil         
                                 order by tarih, yil     

            ";
             
            $statement = $pdo->prepare($sql);  
           // print_r($sql);
            
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
    public function getAfterSalesDetayDirekSatisCirosuAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid   in (94, 96, 98 ) and  
            $servicesQuery = '  a.servisid in ('.$_GET['src'].') and  ';
            // vv.servisid   in (94, 96, 98) and
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT servisid ,  
                    (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
                    tarih,
                    yil,            
                    sum(direksatistutar) direksatistutar
                  FROM ( 

                  SELECT   distinct vv.servisid,
                                tarihicin.tar as tarih,
                                tarihicin.yil as yil,            
                                nvl(data1.direksatistutar,0) direksatistutar,
                                 nvl(data1.atolyecirosucari,0) atolyecirosucari,
                                  nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti
                              from vt_servisler vv  
                             left join (
                               select distinct 
                                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                                    to_number(to_char(x.tarih,'yyyy')) yil  
                               from sason.tarihler x WHERE 
                                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) and  
                                     x.tarih between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                             ) tarihicin on 1=1             
                             LEFT JOIN (
                              select  distinct servisid, 
                                    yil,  
                                    hafta tarih,  
                                    TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar,                             
                                    TO_CHAR(ROUND(sum(nvl(atolyecirosucari,0)), 0), '999,999,999,999,999') atolyecirosucari,                             
                                    TO_CHAR(ROUND(sum(nvl(atolyecirosugaranti,0)), 0), '999,999,999,999,999') atolyecirosugaranti                              
                                    from ( 
                                        select   servisid ,  
                                        /*
                                        1 - isemri 
                                        2 - icmal
                                        3 - satis
                                        */  
                                        to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                        to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                        to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww')) hafta, 
                                          CASE
                                                 WHEN faturaturid= 3
                                                 THEN
                                                   sum (a.toplam)
                                              END  direksatistutar,

                                              CASE
                                                 WHEN faturaturid = 1
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosucari  , 
                                              CASE
                                                 WHEN faturaturid = 2
                                                 THEN
                                                   sum (a.toplam)
                                              END  atolyecirosugaranti  

                                        from faturalar a 
                                        where  
                                            --a.servisid   in (94, 96, 98 ) and 
                                            ".$servicesQuery."
                                            a.durumid=1 and a.faturaturid in(1,2,3) and 
                                            to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(sysdate, 'ww')) -3  and  to_number(to_char(sysdate,'ww')) AND
                                            a.islemtarihi  between  to_date(to_char(sysdate-30, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 

                                        GROUP BY a.SERVISID, to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')), to_number(to_char(to_date(a.islemtarihi, 'dd/mm/yyyy'), 'ww'))
                                    ) asd



                                GROUP BY   yil, hafta ,SERVISID
                             ) data1 on  data1.servisid = vv.servisid and  data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
                             WHERE 
                                vv.servisid not in (1,134,136) and 
                                --vv.servisid   in (94, 96, 98) and 
                                ".$servicesQuery2."
                                vv.dilkod ='Turkish'  
                             ORDER BY  tarih desc 
                             ) ASD 
                             group by servisid,tarih, yil         
                             order by servisid,tarih, yil   
            

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
    public function getAfterSalesDetayDirekSatisCirosuYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT  
                ay,
                yil,    
                TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar    

              FROM (     
                    SELECT  vv.servisid,
                        tarihicin.ay as ay,
                        tarihicin.yil as yil,            
                        nvl(data1.direksatistutar,0) direksatistutar,
                        nvl(data1.atolyecirosucari,0) atolyecirosucari,
                        nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti    
                    from vt_servisler vv  
                     left join (
                           select distinct 
                                to_number(to_char(x.tarih,'yyyy')) yil  ,
                                to_number(to_char(x.tarih,'MM')) ay
                           from sason.tarihler x WHERE  
                                 --x.tarih between to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                                 x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                         ) tarihicin on 1=1
                         LEFT JOIN ( 
                           select  distinct  
                                yil,  
                                to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM')) ay,  
                                ROUND(sum(nvl(direksatistutar,0)), 0)  direksatistutar,                             
                                 ROUND(sum(nvl(atolyecirosucari,0)), 0)  atolyecirosucari,                             
                                ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                                from ( 
                                    select     
                                    /*
                                    1 - isemri 
                                    2 - icmal
                                    3 - satis
                                    */  
                                    to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                                    to_number(to_char(a.islemtarihi,'yyyy')) yil,
                                      CASE
                                             WHEN faturaturid= 3
                                             THEN
                                               sum (a.toplam)
                                          END  direksatistutar,

                                          CASE
                                             WHEN faturaturid = 1
                                             THEN
                                               sum (a.toplam)
                                          END  atolyecirosucari  , 
                                          CASE
                                             WHEN faturaturid = 2
                                             THEN
                                               sum (a.toplam)
                                          END  atolyecirosugaranti  

                                    from faturalar a 
                                    where  
                                        a.servisid  not in (1,134,136 ) and  
                                        a.durumid=1 and a.faturaturid in(1,2,3) and  
                                     --   a.islemtarihi BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                        a.islemtarihi BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                              
                                    GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid , to_number(to_char(a.islemtarihi,'yyyy')) 
                                ) asd

                            GROUP BY  yil ,to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM'))  
                         ) data1 on   data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                         WHERE 
                            vv.servisid   in (1) and
                            vv.dilkod ='Turkish' 
                         ORDER BY yil, ay     
                        ) ASD 
                         group by yil, ay 
                         order by yil, ay 
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
    public function getAfterSalesDetayDirekSatisCirosuYillikWithServices($args = array()) {
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' a.servisid  in ('.$_GET['src'].') and  ';
            //vv.servisid   in (94, 96, 98) and
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
                SELECT servisid  ,  (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = asd.servisid) as servisad, 
    ay,
    yil,    
    TO_CHAR(ROUND(sum(nvl(direksatistutar,0)), 0), '999,999,999,999,999') direksatistutar    
    
  FROM (     
        SELECT  vv.servisid,
            tarihicin.ay as ay,
            tarihicin.yil as yil,            
            nvl(data1.direksatistutar,0) direksatistutar,
            nvl(data1.atolyecirosucari,0) atolyecirosucari,
            nvl(data1.atolyecirosugaranti,0) atolyecirosugaranti    
        from vt_servisler vv  
         left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay
               from sason.tarihler x WHERE  
                     --x.tarih between to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy')  and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')  
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
             ) tarihicin on 1=1
             LEFT JOIN ( 
               select  distinct servisid, 
                    yil,  
                    to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM')) ay,  
                    ROUND(sum(nvl(direksatistutar,0)), 0)  direksatistutar,                             
                     ROUND(sum(nvl(atolyecirosucari,0)), 0)  atolyecirosucari,                             
                    ROUND(sum(nvl(atolyecirosugaranti,0)), 0) atolyecirosugaranti                              
                    from ( 
                        select   servisid ,  
                        /*
                        1 - isemri 
                        2 - icmal
                        3 - satis
                        */  
                        to_date(a.islemtarihi, 'dd/mm/yyyy') as tarihh,
                        to_number(to_char(a.islemtarihi,'yyyy')) yil,
                          CASE
                                 WHEN faturaturid= 3
                                 THEN
                                   sum (a.toplam)
                              END  direksatistutar,
                                                           
                              CASE
                                 WHEN faturaturid = 1
                                 THEN
                                   sum (a.toplam)
                              END  atolyecirosucari  , 
                              CASE
                                 WHEN faturaturid = 2
                                 THEN
                                   sum (a.toplam)
                              END  atolyecirosugaranti  

                        from faturalar a 
                        where  
                            --a.servisid   in (94 ) and 
                            ".$servicesQuery."
                            a.durumid=1 and a.faturaturid in(1,2,3) and  
                         --   a.islemtarihi BETWEEN to_date(to_char(sysdate-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                            a.islemtarihi BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                              
                        GROUP BY to_date(a.islemtarihi, 'dd/mm/yyyy') ,a.faturaturid ,a.SERVISID, to_number(to_char(a.islemtarihi,'yyyy')) 
                    ) asd
              
                GROUP BY SERVISID,yil ,to_number(to_char(to_date(tarihh, 'dd/mm/yyyy'), 'MM'))  
             ) data1 on  data1.servisid = vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                  vv.servisid not in (1,134,136) and 
                --vv.servisid   in (94) and
                ".$servicesQuery2."
                 vv.dilkod ='Turkish' 
             ORDER BY servisid,yil, ay     
            ) ASD 
             group by servisid, yil, ay 
             order by servisid, yil, ay      

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
    public function getAfterSalesDetayCiro($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        /*print_r($weekBefore);
        print_r($dayAfter);*/
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select 
                --sum(a.toplam)  as FATURATUTAR ,
                --TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) as FATURATUTAR,
                CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR,
                to_char(a.islemtarihi, 'dd/mm/yyyy') as TARIH
                from faturalar a
                where  durumid=1 and faturaturid in(1,2,3) and
                (a.islemtarihi  between to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy'))
                GROUP BY to_char(a.islemtarihi, 'dd/mm/yyyy')
               ORDER BY to_char(a.islemtarihi, 'dd/mm/yyyy') asc
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
    public function getAfterSalesDetayCiroWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $weekBefore = date('d/m/Y', strtotime(' -6 day'));
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //servis.servisid =94 and 
            $servicesQuery = ' servis.servisid in ('.$_GET['src'].') and  ';
            // vv.servisid in (94, 96) and 
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT
                        /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = servis.servisid) as servisad, */ 
                        (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = servis.servisid) as servisad, 
                        --sum(ags.arac_giris) as ARAC_GIRIS_SAYISI
                        servis.servisid,
                        tarihler.tarih, 
                        ags.arac_giris
                    FROM 
                        (SELECT TARIH, YIL, AY FROM TARIHLER WHERE TARIH BETWEEN  to_date('".$weekBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')) tarihler
                    left join vt_servisler servis on 
                    --servis.servisid =94 and 
                                ".$servicesQuery."
                                servis.dilkod = 'Turkish'
                    left join mobilags ags on AGS.SERVIS = servis.servisid and 
                            AGS.TARIH = tarihler.tarih
                    --GROUP BY tarihler.tarih
                    order by servis.servisid ,tarihler.tarih asc 
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
    public function getAfterSalesDetayCiroAylik($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $treeMonthsBefore = date('d/m/Y', strtotime(' -90 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select * FROM(
                select 
                   to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                   to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww')) tarih ,
                    --sum(a.toplam)  as FATURATUTAR ,
                    --TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) as FATURATUTAR,
                    CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR 

                    from faturalar a
                    where  durumid=1 and faturaturid in(1,2,3) and
                    (a.islemtarihi  between to_date('".$treeMonthsBefore."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy'))
                   GROUP BY  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(to_date(a.ISLEMTARIHI, 'dd/mm/yyyy'), 'ww'))    
                    ORDER BY yil desc , tarih desc 
            ) test WHERE rownum<5
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
    public function getAfterSalesDetayCiroAylikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //zx.servis =94 AND 
            $servicesQuery = ' zx.servis in ('.$_GET['src'].') and  ';
            // and vv.servisid in
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                
            SELECT  vv.servisid , /*  (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                        (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.tar as tarih,
                tarihicin.yil as yil,            
                nvl(data1.arac_giris,0) arac_giris  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww')) tar  ,
                    to_number(to_char(x.tarih,'yyyy')) yil  
               from sason.tarihler x WHERE     
                     to_number(to_char(to_date(x.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww'))
                      and to_number(to_char(x.tarih,'yyyy'))  =  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))                                      
             ) tarihicin on 1=1
             LEFT JOIN ( 
                  
                select  distinct  zx.servis servisid,  
                      to_number(to_char(zx.tarih,'yyyy')) yil ,
                      to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww')) tarih ,
                      sum(zx.arac_giris) arac_giris     
                FROM mobilags zx 
                WHERE
                        ".$servicesQuery."   
                        to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww'))  between  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'), 'ww')) -3   and  to_number(to_char(to_date(sysdate, 'dd/mm/yyyy'), 'ww'))
                        and to_number(to_char(zx.tarih,'yyyy'))  =  to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))   
                
                group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(to_date(zx.tarih, 'dd/mm/yyyy'), 'ww'))
             ) data1 on data1.servisid = vv.servisid and data1.TARIH = tarihicin.tar AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
             ORDER BY vv.servisid, yil, tarih desc

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
    public function getAfterSalesDetayCiroYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            select * From(
                select 
                       to_number(to_char(a.ISLEMTARIHI,'yyyy')) yil ,
                       to_number(to_char(a.ISLEMTARIHI,'MM')) ay ,
                    --sum(a.toplam)  as FATURATUTAR ,
                    --TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) as FATURATUTAR,
                    CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR 

                    from faturalar a
                    where  durumid=1 and faturaturid in(1,2,3) and
                    (a.islemtarihi  between to_date('".$yearAgoToday."', 'dd/mm/yyyy') AND to_date('".$lastDay."', 'dd/mm/yyyy'))
                    GROUP BY  to_number(to_char(a.ISLEMTARIHI,'yyyy')),  to_number(to_char(a.ISLEMTARIHI,'MM'))   
                    ORDER BY yil asc , ay asc
            ) test2 WHERE rownum<13
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
    public function getAfterSalesDetayCiroYillikWithServices($args = array()) {
        //print_r($fourWeekBefore);
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //a.servisid in (94, 96) and 
            $servicesQuery = ' zx.servis  in ('.$_GET['src'].') and  ';
            // vv.servisid in (94)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
            SELECT  vv.servisid , 
                /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                   (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                tarihicin.ay as tarih,
                tarihicin.yil as yil,            
                nvl(data1.arac_giris,0) arac_giris  
               
              from vt_servisler vv  
             left join (
               select distinct 
                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                    to_number(to_char(x.tarih,'MM')) ay 
               from sason.tarihler x 
               WHERE     
                     x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                                                           
             ) tarihicin on 1=1
             LEFT JOIN ( 
                  
                select  distinct  zx.servis servisid,  
                      to_number(to_char(zx.tarih,'yyyy')) yil ,
                      to_number(to_char(zx.tarih,'MM')) ay ,
                      sum(zx.arac_giris) arac_giris     
                FROM mobilags zx 
                WHERE
                        ".$servicesQuery."  
                        zx.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy') , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                
                
                group by servis ,  to_number(to_char(zx.tarih,'yyyy')),to_number(to_char(zx.tarih,'MM'))
             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136)  
                 ".$servicesQuery2." 
                 and vv.dilkod ='Turkish'  
              ORDER BY vv.servisid, yil, tarih  
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
    public function getAfterSalesDashboardMMCSI($args = array()) {

        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select  
                    case TYPE_ID when
                      0 then 'CSI'
                      ELSE 'CXI' end TYPE,   
                     zx.YIL,
                     zx.PARTNERCODE,  
                     zx.SERVIS , 
                      zx.ayid,
                      zx.MVALUE ay_data 
                     from  SASON.MUSTERI_MEMNUNIYETI zx 
                     where 
                           zx.YIL =    to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                           and zx.PARTNERCODE = 'R001'
                           and zx.TYPE_ID = 0 
                           and zx.MVALUE > 0 
                           and zx.ayid in (
                                  select 
                                  max(xc.ayid) 
                                   from  SASON.MUSTERI_MEMNUNIYETI xc 
                        where
                             xc.YIL =    to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                             and xc.PARTNERCODE = 'R001'
                             and xc.TYPE_ID = 0 
                             and xc.MVALUE > 0 
                    )  
    
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardMMCSIWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        
        $servicesQuery = '';
        //$servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and servisid in ( 94 )
            $servicesQuery = ' and servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            //$servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select  
                    case TYPE_ID when
                      0 then 'CSI'
                      ELSE 'CXI' end TYPE,   
                     zx.YIL,
                     zx.PARTNERCODE,  
                     zx.SERVIS , 
                      zx.ayid,
                      zx.MVALUE ay_data 
                     from  SASON.MUSTERI_MEMNUNIYETI zx 
                     where 
                           zx.YIL =    to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                           and zx.PARTNERCODE = 'R001'
                           and zx.TYPE_ID = 0 
                           and zx.MVALUE > 0 
                           and zx.ayid in (
                                  select 
                                  max(xc.ayid) 
                                   from  SASON.MUSTERI_MEMNUNIYETI xc 
                        where
                             xc.YIL =    to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                             and xc.PARTNERCODE = 'R001'
                             and xc.TYPE_ID = 0 
                             and xc.MVALUE > 0 
                    )   
    
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayMMCSIYillik($args = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT  vv.servisid , 
            'Türkiye Geneli' servisad,
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.memnuniyet,0) memnuniyet
                          from vt_servisler vv  
                         left join (
                           select distinct  
                                to_number(to_char(x.tarih,'yyyy')) yil  ,
                                to_number(to_char(x.tarih,'MM')) ay 
                           from sason.tarihler x WHERE
                                  x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                       
                         ) tarihicin on 1=1
                         LEFT JOIN (  
                              select   
                                zx.YIL, 
                                zx.servisid , 
                                zx.ayid ay, 
                                CASE 
                                    WHEN LENGTH(TRIM(TO_CHAR( (zx.MVALUE), '999,999,999,999,999')))= 3 THEN '1'
                                    ELSE TRIM(TO_CHAR((zx.MVALUE), '999,999,999,999,999')) END as memnuniyet 
                           from  SASON.MUSTERI_MEMNUNIYETI zx  
                              where 
                                    (( zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))-1 AND zx.ayid >= to_number(to_char(sysdate,'MM'))  )  
                                    or 
                                    (zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy')) and zx.ayid <=  to_number(to_char(sysdate,'MM'))  ))    

                                   and zx.PARTNERCODE = 'R001'
                                    and zx.TYPE_ID = 0   

                         ) data1 on  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid in (1) and 
                 vv.dilkod ='Turkish' 

             ORDER BY vv.servisid, yil, ay
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
    public function getAfterSalesDetayMMCSIYillikWithServices($args = array()) {
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and zx.servisid in (94)  
            $servicesQuery = ' and  zx.servisid  in ('.$_GET['src'].')   ';
            // and vv.servisid in (94) 
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
                
                SELECT  vv.servisid , 
                /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                   (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                                tarihicin.ay as ay,
                                tarihicin.yil as yil,            
                                nvl(data1.memnuniyet,0) memnuniyet
                              from vt_servisler vv  
                             left join (
                               select distinct  
                                    to_number(to_char(x.tarih,'yyyy')) yil  ,
                                    to_number(to_char(x.tarih,'MM')) ay 
                               from sason.tarihler x WHERE
                                      x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                       
                             ) tarihicin on 1=1
                             LEFT JOIN (  
                                  select   
                                    zx.YIL, 
                                    zx.servisid , 
                                    zx.ayid ay, 
                                    CASE 
                                        WHEN LENGTH(TRIM(TO_CHAR( (zx.MVALUE), '999,999,999,999,999')))= 3 THEN '1'
                                        ELSE TRIM(TO_CHAR((zx.MVALUE), '999,999,999,999,999')) END as memnuniyet 
                               from  SASON.MUSTERI_MEMNUNIYETI zx  
                                  where 
                                        (( zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))-1 AND zx.ayid >= to_number(to_char(sysdate,'MM'))  )  
                                        or 
                                        (zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy')) and zx.ayid <=  to_number(to_char(sysdate,'MM'))  ))    

                                      --  and zx.PARTNERCODE = 'R001'
                                        and zx.TYPE_ID = 0  
                                        and zx.servisid is not null 
                                        --and zx.servisid in (94)
                                        ".$servicesQuery."

                             ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
                 WHERE 
                     vv.servisid not in (1,134,136)  
                     --and vv.servisid in (94)  
                     ".$servicesQuery2."
                     and vv.dilkod ='Turkish' 

                 ORDER BY vv.servisid, yil, ay
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
    public function getAfterSalesDetayGridMMCSI($args = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  vv.servisid , 
             (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.memnuniyet,0) memnuniyet
                          from vt_servisler vv  
                         left join (
                           select distinct  
                                to_number(to_char(x.tarih,'yyyy')) yil  ,
                                to_number(to_char(x.tarih,'MM')) ay 
                           from sason.tarihler x WHERE
                                  x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                       
                         ) tarihicin on 1=1
                         LEFT JOIN (  
                              select   
                                zx.YIL, 
                                zx.servisid , 
                                zx.ayid ay, 
                                CASE 
                                    WHEN LENGTH(TRIM(TO_CHAR( (zx.MVALUE), '999,999,999,999,999')))= 3 THEN '1'
                                    ELSE TRIM(TO_CHAR((zx.MVALUE), '999,999,999,999,999')) END as memnuniyet 
                           from  SASON.MUSTERI_MEMNUNIYETI zx  
                              where 
                                    (( zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))-1 AND zx.ayid >= to_number(to_char(sysdate,'MM'))  )  
                                    or 
                                    (zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy')) and zx.ayid <=  to_number(to_char(sysdate,'MM'))  ))    

                                    and zx.servisid is not null 
                                    and zx.TYPE_ID = 0   
                --                    --and zx.servisid in (94)  

                         ) data1 on data1.servisid =  vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and 
              --   vv.servisid in (94) and  ------------------*********************
                 vv.dilkod ='Turkish' 

             ORDER BY vv.servisid, yil, ay
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayGridMMCSIWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and zx.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = '   vv.servisid in  ('.$_GET['src'].') and ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    

            SELECT  vv.servisid , 
             (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.memnuniyet,0) memnuniyet
                          from vt_servisler vv  
                         left join (
                           select distinct  
                                to_number(to_char(x.tarih,'yyyy')) yil  ,
                                to_number(to_char(x.tarih,'MM')) ay 
                           from sason.tarihler x WHERE
                                  x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                       
                         ) tarihicin on 1=1
                         LEFT JOIN (  
                              select   
                                zx.YIL, 
                                zx.servisid , 
                                zx.ayid ay, 
                                CASE 
                                    WHEN LENGTH(TRIM(TO_CHAR( (zx.MVALUE), '999,999,999,999,999')))= 3 THEN '1'
                                    ELSE TRIM(TO_CHAR((zx.MVALUE), '999,999,999,999,999')) END as memnuniyet 
                           from  SASON.MUSTERI_MEMNUNIYETI zx  
                              where 
                                    (( zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))-1 AND zx.ayid >= to_number(to_char(sysdate,'MM'))  )  
                                    or 
                                    (zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy')) and zx.ayid <=  to_number(to_char(sysdate,'MM'))  ))    
 
                                    and zx.TYPE_ID = 0   
                                    --and zx.servisid in (94) 
                                    ".$servicesQuery."

                         ) data1 on data1.servisid =  vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and 
                 --vv.servisid in (94) and  
                 ".$servicesQuery2."
                 vv.dilkod ='Turkish' 

             ORDER BY vv.servisid, yil, ay
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardMMCXI($args = array()) {
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                select  
                    case TYPE_ID when
                      0 then 'CSI'
                      ELSE 'CXI' end TYPE,   
                     zx.YIL,
                     zx.PARTNERCODE,  
                     zx.SERVIS , 
                      zx.ayid,
                      zx.MVALUE ay_data 
                     from  SASON.MUSTERI_MEMNUNIYETI zx 
                     where 
                           zx.YIL =    to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                           and zx.PARTNERCODE = 'R001'
                           and zx.TYPE_ID = 1 
                           and zx.MVALUE > 0 
                           and zx.ayid in (
                                  select 
                                  max(xc.ayid) 
                                   from  SASON.MUSTERI_MEMNUNIYETI xc 
                                  where
                    xc.YIL =    to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                    and xc.PARTNERCODE = 'R001'
                    and xc.TYPE_ID = 1 
                    and xc.MVALUE > 0 
                    ) 
    
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardMMCXIWithServices($args = array()) {

        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and ie.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select  
                    case TYPE_ID when
                      0 then 'CSI'
                      ELSE 'CXI' end TYPE,   
                     zx.YIL,
                     zx.PARTNERCODE,  
                     zx.SERVIS , 
                      zx.ayid,
                      zx.MVALUE ay_data 
                     from  SASON.MUSTERI_MEMNUNIYETI zx 
                     where 
                           zx.YIL =    to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                           and zx.PARTNERCODE = 'R001'
                           and zx.TYPE_ID = 1 
                           and zx.MVALUE > 0 
                           and zx.ayid in (
                                  select 
                                  max(xc.ayid) 
                                   from  SASON.MUSTERI_MEMNUNIYETI xc 
                                  where
                    xc.YIL =    to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))
                    and xc.PARTNERCODE = 'R001'
                    and xc.TYPE_ID = 1 
                    and xc.MVALUE > 0 
                    ) 
     
    
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayMMCXIYillik($args = array()) {
        $date = new \DateTime(); //Today
        $lastDay = $date->format("t/m/Y"); //Get last day
        $dateMinus12 = $date->modify("-12 months"); // Last day 12 months ago
        $yearAgoToday = $dateMinus12->format("t/m/Y"); //Get last day
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                SELECT  vv.servisid , 
            'Türkiye Geneli' servisad,
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.memnuniyet,0) memnuniyet
                          from vt_servisler vv  
                         left join (
                           select distinct  
                                to_number(to_char(x.tarih,'yyyy')) yil  ,
                                to_number(to_char(x.tarih,'MM')) ay 
                           from sason.tarihler x WHERE
                                  x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                       
                         ) tarihicin on 1=1
                         LEFT JOIN (  
                              select   
                                zx.YIL, 
                                zx.servisid , 
                                zx.ayid ay, 
                                CASE 
                                    WHEN LENGTH(TRIM(TO_CHAR( (zx.MVALUE), '999,999,999,999,999')))= 3 THEN '1'
                                    ELSE TRIM(TO_CHAR((zx.MVALUE), '999,999,999,999,999')) END as memnuniyet 
                           from  SASON.MUSTERI_MEMNUNIYETI zx  
                              where 
                                    (( zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))-1 AND zx.ayid >= to_number(to_char(sysdate,'MM'))  )  
                                    or 
                                    (zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy')) and zx.ayid <=  to_number(to_char(sysdate,'MM'))  ))    

                                   and zx.PARTNERCODE = 'R001'
                                    and zx.TYPE_ID = 1   

                         ) data1 on  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid in (1) and 
                 vv.dilkod ='Turkish' 

             ORDER BY vv.servisid, yil, ay
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
    public function getAfterSalesDetayMMCXIYillikWithServices($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $fourWeekBefore = date('d/m/Y', strtotime(' -4 week'));
        //print_r($fourWeekBefore);
        
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and zx.servisid in (94)  
            $servicesQuery = ' and  zx.servisid  in ('.$_GET['src'].')   ';
            // and vv.servisid in (94) 
            $servicesQuery2 = ' and vv.servisid in  ('.$_GET['src'].')  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "     
                    SELECT  vv.servisid , /* (Select vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = vv.servisid) as servisad, */ 
                                             (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                        tarihicin.ay as ay,
                        tarihicin.yil as yil,            
                        nvl(data1.memnuniyet,0) memnuniyet
                      from vt_servisler vv  
                     left join (
                       select distinct  
                            to_number(to_char(x.tarih,'yyyy')) yil  ,
                            to_number(to_char(x.tarih,'MM')) ay 
                       from sason.tarihler x WHERE
                              x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                       
                     ) tarihicin on 1=1
                     LEFT JOIN (  
                          select   
                            zx.YIL, 
                            zx.servisid , 
                            zx.ayid ay, 
                            CASE 
                                WHEN LENGTH(TRIM(TO_CHAR( (zx.MVALUE), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR((zx.MVALUE), '999,999,999,999,999')) END as memnuniyet 
                       from  SASON.MUSTERI_MEMNUNIYETI zx  
                          where 
                                (( zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))-1 AND zx.ayid >= to_number(to_char(sysdate,'MM'))  )  
                                or 
                                (zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy')) and zx.ayid <=  to_number(to_char(sysdate,'MM'))  ))    

                              --  and zx.PARTNERCODE = 'R001'
                                and zx.TYPE_ID = 1  
                                and zx.servisid is not null 
                                --and zx.servisid in (94)
                                ".$servicesQuery."

                     ) data1 on data1.servisid = vv.servisid and data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
         WHERE 
             vv.servisid not in (1,134,136)  
             --and vv.servisid in (94) 
             ".$servicesQuery2."
             and vv.dilkod ='Turkish' 

         ORDER BY vv.servisid, yil, ay 
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
    public function getAfterSalesDetayGridMMCXI($args = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    

SELECT  vv.servisid , 
             (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.memnuniyet,0) memnuniyet
                          from vt_servisler vv  
                         left join (
                           select distinct  
                                to_number(to_char(x.tarih,'yyyy')) yil  ,
                                to_number(to_char(x.tarih,'MM')) ay 
                           from sason.tarihler x WHERE
                                  x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                       
                         ) tarihicin on 1=1
                         LEFT JOIN (  
                              select   
                                zx.YIL, 
                                zx.servisid , 
                                zx.ayid ay, 
                                CASE 
                                    WHEN LENGTH(TRIM(TO_CHAR( (zx.MVALUE), '999,999,999,999,999')))= 3 THEN '1'
                                    ELSE TRIM(TO_CHAR((zx.MVALUE), '999,999,999,999,999')) END as memnuniyet 
                           from  SASON.MUSTERI_MEMNUNIYETI zx  
                              where 
                                    (( zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))-1 AND zx.ayid >= to_number(to_char(sysdate,'MM'))  )  
                                    or 
                                    (zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy')) and zx.ayid <=  to_number(to_char(sysdate,'MM'))  ))    
 
                                    and zx.TYPE_ID = 1   
                 
                         ) data1 on data1.servisid =  vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and 
                 vv.dilkod ='Turkish' 

             ORDER BY vv.servisid, yil, ay
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayGridMMCXIWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and zx.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                   SELECT  vv.servisid , 
             (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = vv.servisid) as servisad, 
                            tarihicin.ay as ay,
                            tarihicin.yil as yil,            
                            nvl(data1.memnuniyet,0) memnuniyet
                          from vt_servisler vv  
                         left join (
                           select distinct  
                                to_number(to_char(x.tarih,'yyyy')) yil  ,
                                to_number(to_char(x.tarih,'MM')) ay 
                           from sason.tarihler x WHERE
                                  x.tarih BETWEEN to_date(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')-365, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and  to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')                                       
                         ) tarihicin on 1=1
                         LEFT JOIN (  
                              select   
                                zx.YIL, 
                                zx.servisid , 
                                zx.ayid ay, 
                                CASE 
                                    WHEN LENGTH(TRIM(TO_CHAR( (zx.MVALUE), '999,999,999,999,999')))= 3 THEN '1'
                                    ELSE TRIM(TO_CHAR((zx.MVALUE), '999,999,999,999,999')) END as memnuniyet 
                           from  SASON.MUSTERI_MEMNUNIYETI zx  
                              where 
                                    (( zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy'))-1 AND zx.ayid >= to_number(to_char(sysdate,'MM'))  )  
                                    or 
                                    (zx.YIL =   to_number(to_char(to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy'),'yyyy')) and zx.ayid <=  to_number(to_char(sysdate,'MM'))  ))    
 
                                    and zx.TYPE_ID = 1   
                                    --and zx.servisid in (94)
                                    ".$servicesQuery."

                         ) data1 on data1.servisid =  vv.servisid and  data1.ay = tarihicin.ay AND data1.yil = tarihicin.yil  
             WHERE 
                 vv.servisid not in (1,134,136) and 
                 --vv.servisid in (94) and  
                 ".$servicesQuery2."
                 vv.dilkod ='Turkish' 

             ORDER BY vv.servisid, yil, ay
    
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayBayiStok($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
            SELECT 
                --u.username Bayi,
                --u.user_id bayiid
                count(stok_id) STOK,
                u.username as BAYI
                                    FROM STOK_MASTER@crm.oracle sm,
                                    users@crm.oracle u 
                                    WHERE sm.bayi=u.user_id
                                    and u.user_id not in (117,120,234,113,230,0,112)
                                    AND sm.bayi is not null
                                    and ((sm.tahsisat_durumu=4 AND sm.DURUM<>6) OR (sm.tahsisat_durumu=3 AND sm.DURUM=5)) 
                                    and u.product=1 and u.aktifmi=1
                                    group by u.username, u.user_id
                                    order by count(stok_id) desc
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
    public function getAfterSalesDashboardIsEmriLastDataMusteri($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    

select  rownum as rid , asd.* from ( 
  SELECT      
                    ORT.AD as SERVIS,
                    
                   a.SERVISID, 
                   --(SELECT vtsx.partnercode FROM vt_servisler vtsx where vtsx.servisid = a.SERVISID  and vtsx.dilkod = 'Turkish') as partnercode,
                   --(SELECT vtsxy.ISORTAKAD FROM vt_servisler vtsxy where  vtsxy.dilkod = 'Turkish' and vtsxy.servisid = a.SERVISID) as servisad, 
                   --(Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = a.servisid) as servisad, 
                   a.id servisisemirid, 
                   
                   to_char(a.KAYITTARIH, 'DD/MM/YYYY HH24:MI:SS') as tarih,
                   a.SERVISISORTAKID ,
                   iso.ad
                 FROM servisisemirler a     
                  LEFT JOIN SASON.servisisortaklar iso ON iso.id = a.SERVISISORTAKID AND iso.durumid=1
                  LEFT JOIN SERVISLER ser ON A.SERVISID = ser.id AND ser.durumid=1
                  LEFT JOIN SASON.isortaklar ort on ser.ISORTAKID = ort.id             
                 --INNER JOIN servisisemirislemler b on a.id=b.servisisemirid
                 --WHERE
                 -- trunc (a.KAYITTARIH) = trunc(sysdate)  
                  --  b.isemirtipid=2 AND 
                --    rownum < 50
                --    a.servisid {servisIdQuery} AND
                   -- to_char(sysdate,'yyyy') =  to_char(KAYITTARIH,'yyyy')
                 --GROUP BY a.servisid, to_number ( to_char(KAYITTARIH,'mm')), to_char(KAYITTARIH, 'Month') 
                 order by   a.KAYITTARIH  desc 
                -- isemirtipler
                ) asd 
                where rownum < 7 ";
             
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
    public function getAfterSalesDashboardIsEmirData($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                    select count(id) a,
                            cast( 'Açık İş Emirleri' AS varchar2(300)) as  aciklama,
                            1 as controler
                                from servisisemirler
                                where  teknikolaraktamamla is null or teknikolaraktamamla = 0 
                    UNION
                    select  NVL(count(si.id), 0) a,
                            cast( 'Kapanan İş Emri' AS varchar2(300)) as  aciklama,
                            3 as controler
                        from servisisemirler  si
                        where  si.teknikolaraktamamla = 1
                        and SI.TAMAMLANMATARIH   BETWEEN to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                    UNION 
                    select count(a.id) a, 
                        cast( 'Açılan İş Emri' AS varchar2(300)) as  aciklama,
                        2 as controler
                        from servisisemirler a
                        where  teknikolaraktamamla is null or teknikolaraktamamla = 0 AND 
                        (a.KAYITTARIH between to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')) 
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
    public function getAfterSalesDashboardIsEmirDataWithServices($args = array()) {
        
        
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and servisid in (  94 )
            $servicesQuery = ' and servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        } 
        
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "
                     select count(id) a,
                            cast( 'Açık İş Emirleri' AS varchar2(300)) as  aciklama,
                            1 as controler
                                from servisisemirler
                                where  teknikolaraktamamla is null or teknikolaraktamamla = 0 
                                --and servisid in (  94 )
                                ".$servicesQuery."
                    UNION
                    select  NVL(count(si.id), 0) a,
                            cast( 'Kapanan İş Emri' AS varchar2(300)) as  aciklama,
                            3 as controler
                        from servisisemirler  si
                        where  si.teknikolaraktamamla = 1
                        and SI.TAMAMLANMATARIH   BETWEEN to_date('10/06/2018', 'dd/mm/yyyy') AND to_date('15/06/2018', 'dd/mm/yyyy') 
                             --and servisid in (  94 )
                             ".$servicesQuery."
                    UNION 
                    select count(a.id) a, 
                        cast( 'Açılan İş Emri' AS varchar2(300)) as  aciklama,
                        2 as controler
                        from servisisemirler a
                        where  teknikolaraktamamla is null or teknikolaraktamamla = 0 AND 
                        (a.KAYITTARIH between to_date('10/06/2018', 'dd/mm/yyyy') AND to_date('15/06/2018', 'dd/mm/yyyy')) 
                            -- and servisid in (  94)
                            ".$servicesQuery."
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
    public function getAfterSalesDashboardFaturaData($args = array()) {
        
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //print_r($today);
        //print_r($dayAfter);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select 
                        TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a,
                        /*CASE
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.NETTUTAR), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.NETTUTAR), 0)= 0 THEN  '0 TL'
                            ELSE  TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.NETTUTAR)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL'))  END as a, */
                        --NVL(sum(a.NETTUTAR), 0) a,
                        cast( 'Alış Faturaları ' AS varchar2(300)) as  aciklama,
                        1 as controler
                        FROM faturalar a
                        WHERE  
                         a.ISLEMTARIHI > 
                          to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and
                         a.faturaturid = 4
                    UNION
                    select
                         TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a, 
                        /*CASE  
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.toplam), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN  '0 TL'
                            ELSE  TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.toplam)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL'))  END as a, */  
                        --NVL(sum(a.toplam), 0) a,
                        cast( 'İş Emri Faturaları ' AS varchar2(300)) as aciklama,
                        2 as controler
                        FROM faturalar a
                        WHERE 
                        a.ISLEMTARIHI >  
                         to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and
                        a.faturaturid=1
                    UNION
                    select
                        TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a, 
                       /* CASE 
                           WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.toplam), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN  '0 TL'
                            ELSE  TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.toplam)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL')) END as a,*/   
                        --NVL(sum(a.toplam), 0) a,
                        cast( 'Satış Faturaları ' AS varchar2(300)) as aciklama,
                        3 as controler
                        FROM faturalar a
                        WHERE  
                        a.islemtarihi >  
                         to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and
                        a.faturaturid=3 
                    UNION
                    select 
                    TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a,
                        /*CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.toplam), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN  '0 TL'
                            ELSE TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.toplam)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL')) END as a, */  
                        --NVL(sum(a.toplam), 0) toplam,
                        cast( 'İcmal Faturaları' AS varchar2(300)) as aciklama,
                        4 as controler
                        FROM faturalar a
                        WHERE  
                        a.ISLEMTARIHI > 
                         to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and
                        a.faturaturid=2
                    UNION
                    select 
                        TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a,
                        /*CASE 
                             WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.NETTUTAR), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.NETTUTAR), 0)= 0 THEN  '0 TL'
                            ELSE  TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.NETTUTAR)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL')) END as a, */
                        --NVL(sum(a.NETTUTAR), 0) a,
                        cast( 'Dış Hizmet Faturaları' AS varchar2(300)) as aciklama,
                        5 as controler
                        FROM faturalar a
                        WHERE 
                        a.islemtarihi >  
                         to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and 
                        a.faturaturid=5 

                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardFaturaDataWithServices($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and a.servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        }
        //print_r($today);
        //print_r($dayAfter);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select 
                        TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a,
                        /*CASE
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.NETTUTAR), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.NETTUTAR), 0)= 0 THEN  '0 TL'
                            ELSE  TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.NETTUTAR)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL'))  END as a, */
                        --NVL(sum(a.NETTUTAR), 0) a,
                        cast( 'Alış Faturaları ' AS varchar2(300)) as  aciklama,
                        1 as controler
                        FROM faturalar a
                        WHERE  
                         a.ISLEMTARIHI > 
                          to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and
                         a.faturaturid = 4
                         --and a.servisid in (94)
                         ".$servicesQuery."
                    UNION
                    select
                         TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a, 
                        /*CASE  
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.toplam), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN  '0 TL'
                            ELSE  TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.toplam)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL'))  END as a, */  
                        --NVL(sum(a.toplam), 0) a,
                        cast( 'İş Emri Faturaları ' AS varchar2(300)) as aciklama,
                        2 as controler
                        FROM faturalar a
                        WHERE 
                        a.ISLEMTARIHI >  
                         to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and
                        a.faturaturid=1
                         --and a.servisid in (94)
                         ".$servicesQuery."
                    UNION
                    select
                        TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a, 
                       /* CASE 
                           WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.toplam), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN  '0 TL'
                            ELSE  TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.toplam)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL')) END as a,*/   
                        --NVL(sum(a.toplam), 0) a,
                        cast( 'Satış Faturaları ' AS varchar2(300)) as aciklama,
                        3 as controler
                        FROM faturalar a
                        WHERE  
                        a.islemtarihi >  
                         to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and
                        a.faturaturid=3 
                        --and a.servisid in (94)
                         ".$servicesQuery."
                    UNION
                    select 
                    TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a,
                        /*CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.toplam), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN  '0 TL'
                            ELSE TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.toplam)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL')) END as a, */  
                        --NVL(sum(a.toplam), 0) toplam,
                        cast( 'İcmal Faturaları' AS varchar2(300)) as aciklama,
                        4 as controler
                        FROM faturalar a
                        WHERE  
                        a.ISLEMTARIHI > 
                         to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and
                        a.faturaturid=2
                         --and a.servisid in (94)
                         ".$servicesQuery."
                    UNION
                    select 
                    TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')) a
                       /* CASE 
                             WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))= 0 THEN '0 TL' 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))< 4 THEN CONCAT(TO_CHAR(round(NVL(sum(a.NETTUTAR), 0),0) , '999,999,999,999,999'), ' TL')
                            WHEN NVL(sum(a.NETTUTAR), 0)= 0 THEN  '0 TL'
                            ELSE  TRIM(CONCAT(TO_CHAR(round(NVL(sum(a.NETTUTAR)/1000, 0),0) , '999,999,999,999,999'), ' BİN TL')) END as a*/, 
                        --NVL(sum(a.NETTUTAR), 0) a,
                        cast( 'Dış Hizmet Faturaları' AS varchar2(300)) as aciklama,
                        5 as controler
                        FROM faturalar a
                        WHERE 
                        a.islemtarihi >  
                         to_date(to_char(sysdate-8, 'dd/mm/yyyy')  , 'dd/mm/yyyy') and 
                        a.faturaturid=5 
                         --and a.servisid in (94)
                         ".$servicesQuery."

                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardCiroYedekParcaData($args = array()) {
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        $dayBefore = date('d/m/Y', strtotime(' -1 day'));
        $dayBeforeTwo = date('d/m/Y', strtotime(' -2 day'));
        //print_r($dayBefore);
        //print_r($dayBeforeTwo);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
        Select NVL(sum(a.toplam), 0) as a ,
           cast('Toplam Ciro' as varchar2(300)) as aciklama,
           1 as controler
            from faturalar a
            where  durumid=1 and faturaturid in(1,2,3) and
            (a.islemtarihi  between to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy'))
        UNION
        Select NVL(sum(a.toplam), 0) as a ,
           cast('Önceki Gün Toplam Ciro' as varchar2(300)) as aciklama,
           5 as controler
            from faturalar a
            where  durumid=1 and faturaturid in(1,2,3) and
            (a.islemtarihi  between to_date('".$dayBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy'))
        UNION
        Select count(*) as a,
               cast('Müşteri Sayısı' as varchar2(300))  as aciklama,
               2 as controler
              from (
                    Select 
                        count(Replace(sie.MUSTERIAD,' ','')) as c,
                        SV.AD as musteri
                        from servisisemirler sie
                        inner join servisisortaklar sio on SIO.SERVISVARLIKID = SIE.SERVISVARLIKID
                        inner join servisvarliklar sv on SV.ID = SIO.SERVISVARLIKID
                       WHERE  SIE.KAYITTARIH between to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                       GROUP BY sv.ad
                       ORDER BY musteri DESC
                   )
         UNION 
         Select count(*) as a,
               cast('Önceki Gün Müşteri Sayısı' as varchar2(300))  as aciklama,
               4 as controler
              from (
                    Select 
                        count(Replace(sie.MUSTERIAD,' ','')) as c,
                        SV.AD as musteri
                        from servisisemirler sie
                        inner join servisisortaklar sio on SIO.SERVISVARLIKID = SIE.SERVISVARLIKID
                        inner join servisvarliklar sv on SV.ID = SIO.SERVISVARLIKID
                       WHERE  SIE.KAYITTARIH between to_date('".$dayBefore."', 'dd/mm/yyyy') AND to_date('".$today."', 'dd/mm/yyyy')
                       GROUP BY sv.ad
                       ORDER BY musteri DESC
                   )
         UNION
         SELECT NVL(sum(toplam), 0) as a,
                cast('Toplam Yedek Parça' as varchar2(300)) as aciklama,
                3 as controler
                 from(
                    Select to_date(ypd.TARIH, 'dd/mm/YYYY') as tar,
                           sst.kod,
                           sum(ypd.tutar) as toplam
                     from servisler  s
                    left join sason.rptable_yedekparcadetay ypd on ypd.servisid = s.id  
                    left join servisstokturler sst on ypd.SERVISSTOKTURID = sst.id 
                                                   
                    Where ypd.TARIH between to_date('".$today."', 'dd/mm/YYYY') AND to_date('".$dayAfter."', 'dd/mm/YYYY')
                    GROUP BY sst.kod, to_date(ypd.TARIH, 'dd/mm/YYYY'), sst.kod
                    ORDER BY to_date(ypd.TARIH, 'dd/mm/YYYY') desc
                    )
    
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    
    
    //detay yedek parça sayfası fonk. baş
    
    /**
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function getAfterSalesDashboardFaalYedekParca($args = array()) {
        
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //print_r($today);
        //print_r($dayAfter);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  
                        TO_CHAR(ROUND(nvl(yedekparcatoplami,0), 0), '999,999,999,999,999')   yedekparcatoplami
                      FROM ( 
                                   SELECT 
                                      sum(yedekparcatoplami) yedekparcatoplami
                                      FROM  sason.ypfaaliyet dsf
                                   WHERE
                                          dsf.servisid not in (1,134,136) and 
                                         dsf.YEDEKPARCAFALIYETRAPORTARIHI <   to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')   

                       ) asd               
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardFaalYedekParcaWithServices($args = array()) { 
        $servicesQuery = ' and dsf.servisid not in (1,134,136) ';
    
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and dsf.servisid in ('.$_GET['src'].')  '; 
        }
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
             SELECT 
              
                  /*----------------------------------üst grup---------Servis içinde onarıma kullanılan----------------------------*/ 
                 TO_CHAR(ROUND(sum(nvl(servisiciuygunparca,0)), 0), '999,999,999,999,999') servisiciuygunparca  ,
                  TO_CHAR(ROUND(sum(nvl(servisiciucretliuygunparca,0)), 0), '999,999,999,999,999') servisiciucretliuygunparca  ,
                 TO_CHAR(ROUND(sum(nvl(servisicigaranti,0)), 0), '999,999,999,999,999') servisicigaranti,  
                 TO_CHAR(ROUND(sum(nvl(servisiciuygunparca,0)), 0), '999,999,999,999,999') kutu1toplam,         /*  1. toplam alanı*/
                 TO_CHAR(ROUND(sum(nvl(servisicioem,0)), 0), '999,999,999,999,999') servisicioem,
                 TO_CHAR(ROUND(sum(nvl(servisicioes,0)), 0), '999,999,999,999,999') servisicioes,
                 TO_CHAR(ROUND(sum(nvl(servisiciesdeger,0)), 0), '999,999,999,999,999') servisiciesdeger, 
                 TO_CHAR(ROUND(sum(nvl(servisiciyansanayitoplam,0)), 0), '999,999,999,999,999') kutu2usttoplam,  /*  2. toplamalanı */
                 TO_CHAR(ROUND(sum(nvl(servisiciyansanayi,0)), 0), '999,999,999,999,999') servisiciyansanayi,
                 TO_CHAR(ROUND(sum(nvl(servisicimyok,0)), 0), '999,999,999,999,999') servisicimyok,
                 TO_CHAR(ROUND(sum(nvl(servisicitoplam,0)), 0), '999,999,999,999,999') kutu2yantoplam      /*   3. toplam alanı*/ 
                 /*----------------------------------alt grup------Servis dışına/direk satılan--------------------------------------*/
               
                 FROM  sason.ypfaaliyet dsf 
                 WHERE    
                    dsf.YEDEKPARCAFALIYETRAPORTARIHI <   to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                    ".$servicesQuery."  
                
                      
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardFaalYedekParcaServisDisiWithServices($args = array()) { 
        $servicesQuery = ' and dsf.servisid not in (1,134,136) ';
    
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and dsf.servisid in ('.$_GET['src'].')  '; 
        }
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
             SELECT 
               
                 
                 /*----------------------------------alt grup------Servis dışına/direk satılan--------------------------------------*/
                 TO_CHAR(ROUND(sum(nvl(servisdisiuygunparca,0)), 0), '999,999,999,999,999') servisdisiuygunparca,  /*  1. toplam alanı*/
                 TO_CHAR(ROUND(sum(nvl(servisdisioem,0)), 0), '999,999,999,999,999') servisdisioem,
                 TO_CHAR(ROUND(sum(nvl(servisdisioes,0)), 0), '999,999,999,999,999') servisdisioes,
                 TO_CHAR(ROUND(sum(nvl(servisdisiesdeger,0)), 0), '999,999,999,999,999') servisdisiesdeger, 
                 TO_CHAR(ROUND(sum(nvl(servisdisiystoplam,0)), 0), '999,999,999,999,999') kutu2usttoplam,  /*  2. toplamalanı */
                 TO_CHAR(ROUND(sum(nvl(servisdisiyansanayi,0)), 0), '999,999,999,999,999') servisdisiyansanayi,
                 TO_CHAR(ROUND(sum(nvl(servisdisimyok,0)), 0), '999,999,999,999,999') servisdisimyok,
                 TO_CHAR(ROUND(sum(nvl(servisdisitoplam,0)), 0), '999,999,999,999,999') servisdisitoplam      /*   3. toplam alanı*/

                 FROM  sason.ypfaaliyet dsf 
                 WHERE    
                    dsf.YEDEKPARCAFALIYETRAPORTARIHI <   to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                    ".$servicesQuery."  
                 
                        
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayFaalYedekParca($args = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    

            SELECT  
                          /*----------------------------------üst grup---------Servis içinde onarıma kullanılan----------------------------*/ 
                         TO_CHAR(ROUND(sum(nvl(servisiciuygunparca,0)), 0), '999,999,999,999,999') servisiciuygunparca  ,
                         TO_CHAR(ROUND(sum(nvl(servisicigaranti,0)), 0), '999,999,999,999,999') servisicigaranti,  
                         TO_CHAR(ROUND(sum(nvl(servisiciuygunparca,0)), 0), '999,999,999,999,999') kutu1toplam,         /*  1. toplam alanı*/
                         TO_CHAR(ROUND(sum(nvl(servisicioem,0)), 0), '999,999,999,999,999') servisicioem,
                         TO_CHAR(ROUND(sum(nvl(servisicioes,0)), 0), '999,999,999,999,999') servisicioes,
                         TO_CHAR(ROUND(sum(nvl(servisiciesdeger,0)), 0), '999,999,999,999,999') servisiciesdeger, 
                         TO_CHAR(ROUND(sum(nvl(servisiciyansanayitoplam,0)), 0), '999,999,999,999,999') kutu2usttoplam,  /*  2. toplamalanı */
                         TO_CHAR(ROUND(sum(nvl(servisiciyansanayi,0)), 0), '999,999,999,999,999') servisiciyansanayi,
                         TO_CHAR(ROUND(sum(nvl(servisicimyok,0)), 0), '999,999,999,999,999') servisicimyok,
                         TO_CHAR(ROUND(sum(nvl(servisicitoplam,0)), 0), '999,999,999,999,999') kutu2yantoplam,       /*   3. toplam alanı*/ 
                         /*----------------------------------alt grup------Servis dışına/direk satılan--------------------------------------*/
                         TO_CHAR(ROUND(sum(nvl(servisdisiuygunparca,0)), 0), '999,999,999,999,999') servisdisiuygunparca,  /*  1. toplam alanı*/
                         TO_CHAR(ROUND(sum(nvl(servisdisioem,0)), 0), '999,999,999,999,999') servisdisioem,
                         TO_CHAR(ROUND(sum(nvl(servisdisioes,0)), 0), '999,999,999,999,999') servisdisioes,
                         TO_CHAR(ROUND(sum(nvl(servisdisiesdeger,0)), 0), '999,999,999,999,999') servisdisiesdeger, 
                         TO_CHAR(ROUND(sum(nvl(servisdisiystoplam,0)), 0), '999,999,999,999,999') kutu2usttoplam,  /*  2. toplamalanı */
                         TO_CHAR(ROUND(sum(nvl(servisdisiyansanayi,0)), 0), '999,999,999,999,999') servisdisiyansanayi,
                         TO_CHAR(ROUND(sum(nvl(servisdisimyok,0)), 0), '999,999,999,999,999') servisdisimyok,
                         TO_CHAR(ROUND(sum(nvl(servisdisitoplam,0)), 0), '999,999,999,999,999') servisdisitoplam      /*   3. toplam alanı*/


                         FROM  sason.ypfaaliyet dsf 
                        WHERE
                    dsf.servisid not in (1,134,136) and      
                    dsf.YEDEKPARCAFALIYETRAPORTARIHI <   to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardFaalYagToplam($args = array()) {
        
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //print_r($today);
        //print_r($dayAfter);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    SELECT  
                        TO_CHAR(ROUND(nvl(yagtoplam,0), 0), '999,999,999,999,999') yagtoplam  
                     FROM ( 
                                  SELECT 
                                     sum( yagtoplam ) yagtoplam
                                     FROM  sason.ypfaaliyet dsf
                                  WHERE
                                         dsf.servisid not in (1,134,136) and 
                                        dsf.YEDEKPARCAFALIYETRAPORTARIHI <   to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy')   
                      ) asd                         
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayFaalYag($args = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    

            SELECT  
                          /*----------------------------------üst grup---------Servis içinde onarıma kullanılan----------------------------*/ 
                         TO_CHAR(ROUND(sum(nvl(servisiciuygunparca,0)), 0), '999,999,999,999,999') servisiciuygunparca  ,
                         TO_CHAR(ROUND(sum(nvl(servisicigaranti,0)), 0), '999,999,999,999,999') servisicigaranti,  
                         TO_CHAR(ROUND(sum(nvl(servisiciuygunparca,0)), 0), '999,999,999,999,999') kutu1toplam,         /*  1. toplam alanı*/
                         TO_CHAR(ROUND(sum(nvl(servisicioem,0)), 0), '999,999,999,999,999') servisicioem,
                         TO_CHAR(ROUND(sum(nvl(servisicioes,0)), 0), '999,999,999,999,999') servisicioes,
                         TO_CHAR(ROUND(sum(nvl(servisiciesdeger,0)), 0), '999,999,999,999,999') servisiciesdeger, 
                         TO_CHAR(ROUND(sum(nvl(servisiciyansanayitoplam,0)), 0), '999,999,999,999,999') kutu2usttoplam,  /*  2. toplamalanı */
                         TO_CHAR(ROUND(sum(nvl(servisiciyansanayi,0)), 0), '999,999,999,999,999') servisiciyansanayi,
                         TO_CHAR(ROUND(sum(nvl(servisicimyok,0)), 0), '999,999,999,999,999') servisicimyok,
                         TO_CHAR(ROUND(sum(nvl(servisicitoplam,0)), 0), '999,999,999,999,999') kutu2yantoplam,       /*   3. toplam alanı*/ 
                         /*----------------------------------alt grup------Servis dışına/direk satılan--------------------------------------*/
                         TO_CHAR(ROUND(sum(nvl(servisdisiuygunparca,0)), 0), '999,999,999,999,999') servisdisiuygunparca,  /*  1. toplam alanı*/
                         TO_CHAR(ROUND(sum(nvl(servisdisioem,0)), 0), '999,999,999,999,999') servisdisioem,
                         TO_CHAR(ROUND(sum(nvl(servisdisioes,0)), 0), '999,999,999,999,999') servisdisioes,
                         TO_CHAR(ROUND(sum(nvl(servisdisiesdeger,0)), 0), '999,999,999,999,999') servisdisiesdeger, 
                         TO_CHAR(ROUND(sum(nvl(servisdisiystoplam,0)), 0), '999,999,999,999,999') kutu2usttoplam,  /*  2. toplamalanı */
                         TO_CHAR(ROUND(sum(nvl(servisdisiyansanayi,0)), 0), '999,999,999,999,999') servisdisiyansanayi,
                         TO_CHAR(ROUND(sum(nvl(servisdisimyok,0)), 0), '999,999,999,999,999') servisdisimyok,
                         TO_CHAR(ROUND(sum(nvl(servisdisitoplam,0)), 0), '999,999,999,999,999') servisdisitoplam      /*   3. toplam alanı*/


                         FROM  sason.ypfaaliyet dsf 
                        WHERE
                    dsf.servisid not in (1,134,136) and      
                    dsf.YEDEKPARCAFALIYETRAPORTARIHI <   to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardFaalYagToplamWithServices($args = array()) { 
        $servicesQuery = ' and dsf.servisid not in (1,134,136) ';
    
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and dsf.servisid in ('.$_GET['src'].')  '; 
        }
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
             
            SELECT   
                 
                 /*----------------------------------------Servis içinde onarıma kullanılan----------------------------*/ 
                TO_CHAR(ROUND(sum(nvl(servisiciyag,0)), 0), '999,999,999,999,999') servisiciyag  ,
                 /*--------------------------------------Servis dışına/direk satılan--------------------------------------*/
                TO_CHAR(ROUND(sum(nvl(servisdisiyag,0)), 0), '999,999,999,999,999') servisdisiyag,  

                TO_CHAR(ROUND(sum(nvl(yagtoplam,0)), 0), '999,999,999,999,999') yagtoplam           /*  1. toplam alanı*/

                FROM  sason.ypfaaliyet dsf 
                WHERE
                        
                    dsf.YEDEKPARCAFALIYETRAPORTARIHI <   to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                    ".$servicesQuery."  
               
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardFaalStokToplam($args = array()) {
        
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //print_r($today);
        //print_r($dayAfter);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select    
                          TO_CHAR(ROUND(nvl( sum( nvl(stok_oes,0) +nvl(stok_oeM,0) + nvl(stok_essanayi,0) + nvl(stok_my,0) +nvl(stok_yansanayi,0)) ,0), 0), '999,999,999,999,999') stok_toplam   
                      FROM ( 

                      SELECT   
                                case asd.SERVISSTOKTURID
                                        when 1 then  sum(nvl(asd.ORTALAMAMALIYET,0) * asd.STOKMIKTAR)
                                     end   as stok_oem, 
                                case asd.SERVISSTOKTURID
                                        when 7 then  sum(nvl(asd.ORTALAMAMALIYET,0) * asd.STOKMIKTAR)                   
                                     end   as stok_oes,
                                case asd.SERVISSTOKTURID
                                        when 8 then  sum(nvl(asd.ORTALAMAMALIYET,0)*asd.STOKMIKTAR)
                                     end   as stok_essanayi,
                                case asd.SERVISSTOKTURID
                                        when 9 then  sum(nvl(asd.ORTALAMAMALIYET,0)*asd.STOKMIKTAR)
                                     end   as stok_yansanayi,
                                case asd.SERVISSTOKTURID
                                        when 11 then sum(nvl(asd.ORTALAMAMALIYET,0) *asd.STOKMIKTAR)
                                     end   as stok_my
                              FROM( 
                SELECT
                       a.kod tur, 
                       p.servisstokturid, 
                       P.ORTALAMAMALIYET ORTALAMAMALIYET,
                       p.STOKMIKTAR 
                  FROM(SELECT servisstokturid,
                                a.id,
                               a.servisid hservisid, 
                               C.STOKMIKTAR,  
                               kurlar_pkg.ORTALAMAMALIYET(a.id) ORTALAMAMALIYET 
                          FROM(SELECT DISTINCT servisstokid
                                  FROM sason.servisstokhareketdetaylar
                                            ) h,
                               sason.servisstoklar a,
                               sason.vt_genelstok c 
                         WHERE  h.servisstokid = a.id
                               AND A.ID = C.SERVISSTOKID
                               AND C.STOKMIKTAR <> 0
                               AND a.servisid = c.servisid
                               AND a.SERVISID  not in (1,134 ,136) 
                            ) p,
                       servisstokturler a
                 WHERE p.servisstokturid = a.id
                 ) asd
                 group by  asd.SERVISSTOKTURID  
              ) asasd                        
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDashboardFaalStokToplamWithServices($args = array()) { 
        $servicesQuery = ' and a.servisid not in (1,134,136) ';
    
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //and ie.servisid in (94,96,98)
            $servicesQuery = ' and a.servisid in ('.$_GET['src'].')  '; 
        }
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
             
    SELECT  
          TO_CHAR(ROUND( sum(nvl(stok_oes,0)) , 0), '999,999,999,999,999') stok_oes  ,
          TO_CHAR(ROUND( sum(nvl(stok_oeM,0)) , 0), '999,999,999,999,999') stok_oeM  ,
          TO_CHAR(ROUND( sum(nvl(stok_essanayi,0)) , 0), '999,999,999,999,999') stok_esdsanayi  ,
          TO_CHAR(ROUND( sum(nvl(stok_my,0)) , 0), '999,999,999,999,999') stok_my  ,
          TO_CHAR(ROUND( sum(nvl(stok_yansanayi,0)) , 0), '999,999,999,999,999') stok_yansanayi  ,
          TO_CHAR(ROUND(nvl( sum( nvl(stok_oes,0) +nvl(stok_oeM,0) + nvl(stok_essanayi,0) + nvl(stok_my,0) +nvl(stok_yansanayi,0)) ,0), 0), '999,999,999,999,999') stok_toplam
    FROM (  
      SELECT   asd.HSERVISID servisid, 
                case asd.SERVISSTOKTURID
                        when 1 then  sum(nvl(asd.ORTALAMAMALIYET,0) * asd.STOKMIKTAR)
                     end   as stok_oem, 
                case asd.SERVISSTOKTURID
                        when 7 then  sum(nvl(asd.ORTALAMAMALIYET,0) * asd.STOKMIKTAR)                   
                     end   as stok_oes,
                case asd.SERVISSTOKTURID
                        when 8 then  sum(nvl(asd.ORTALAMAMALIYET,0)*asd.STOKMIKTAR)
                     end   as stok_essanayi,
                case asd.SERVISSTOKTURID
                        when 9 then  sum(nvl(asd.ORTALAMAMALIYET,0)*asd.STOKMIKTAR)
                     end   as stok_yansanayi,
                case asd.SERVISSTOKTURID
                        when 11 then sum(nvl(asd.ORTALAMAMALIYET,0) *asd.STOKMIKTAR)
                     end   as stok_my
              FROM(
                SELECT
                       a.kod tur, 
                       p.ID,
                       p.HSERVISID,
                       p.servisstokturid,  
                       P.ORTALAMAMALIYET ORTALAMAMALIYET,
                       p.STOKMIKTAR 
                  FROM(SELECT servisstokturid,
                               a.id,
                               a.servisid hservisid, 
                               C.STOKMIKTAR,   
                               kurlar_pkg.ORTALAMAMALIYET(a.id) ORTALAMAMALIYET 
                          FROM(SELECT DISTINCT servisstokid
                                  FROM sason.servisstokhareketdetaylar
                                            ) h,
                               sason.servisstoklar a,
                               sason.vt_genelstok c 
                         WHERE  h.servisstokid = a.id
                               AND A.ID = C.SERVISSTOKID
                               AND C.STOKMIKTAR <> 0
                               AND a.servisid = c.servisid 
                                ".$servicesQuery."  
                            ) p,
                       servisstokturler a
                 WHERE p.servisstokturid = a.id
                 ) asd
                 group by  asd.SERVISSTOKTURID ,asd.HSERVISID
              ) asasd
            
            
                    ";
             
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesDetayFaalStok($args = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    

            SELECT  
                          /*----------------------------------üst grup---------Servis içinde onarıma kullanılan----------------------------*/ 
                         TO_CHAR(ROUND(sum(nvl(servisiciuygunparca,0)), 0), '999,999,999,999,999') servisiciuygunparca  ,
                         TO_CHAR(ROUND(sum(nvl(servisicigaranti,0)), 0), '999,999,999,999,999') servisicigaranti,  
                         TO_CHAR(ROUND(sum(nvl(servisiciuygunparca,0)), 0), '999,999,999,999,999') kutu1toplam,         /*  1. toplam alanı*/
                         TO_CHAR(ROUND(sum(nvl(servisicioem,0)), 0), '999,999,999,999,999') servisicioem,
                         TO_CHAR(ROUND(sum(nvl(servisicioes,0)), 0), '999,999,999,999,999') servisicioes,
                         TO_CHAR(ROUND(sum(nvl(servisiciesdeger,0)), 0), '999,999,999,999,999') servisiciesdeger, 
                         TO_CHAR(ROUND(sum(nvl(servisiciyansanayitoplam,0)), 0), '999,999,999,999,999') kutu2usttoplam,  /*  2. toplamalanı */
                         TO_CHAR(ROUND(sum(nvl(servisiciyansanayi,0)), 0), '999,999,999,999,999') servisiciyansanayi,
                         TO_CHAR(ROUND(sum(nvl(servisicimyok,0)), 0), '999,999,999,999,999') servisicimyok,
                         TO_CHAR(ROUND(sum(nvl(servisicitoplam,0)), 0), '999,999,999,999,999') kutu2yantoplam,       /*   3. toplam alanı*/ 
                         /*----------------------------------alt grup------Servis dışına/direk satılan--------------------------------------*/
                         TO_CHAR(ROUND(sum(nvl(servisdisiuygunparca,0)), 0), '999,999,999,999,999') servisdisiuygunparca,  /*  1. toplam alanı*/
                         TO_CHAR(ROUND(sum(nvl(servisdisioem,0)), 0), '999,999,999,999,999') servisdisioem,
                         TO_CHAR(ROUND(sum(nvl(servisdisioes,0)), 0), '999,999,999,999,999') servisdisioes,
                         TO_CHAR(ROUND(sum(nvl(servisdisiesdeger,0)), 0), '999,999,999,999,999') servisdisiesdeger, 
                         TO_CHAR(ROUND(sum(nvl(servisdisiystoplam,0)), 0), '999,999,999,999,999') kutu2usttoplam,  /*  2. toplamalanı */
                         TO_CHAR(ROUND(sum(nvl(servisdisiyansanayi,0)), 0), '999,999,999,999,999') servisdisiyansanayi,
                         TO_CHAR(ROUND(sum(nvl(servisdisimyok,0)), 0), '999,999,999,999,999') servisdisimyok,
                         TO_CHAR(ROUND(sum(nvl(servisdisitoplam,0)), 0), '999,999,999,999,999') servisdisitoplam      /*   3. toplam alanı*/


                         FROM  sason.ypfaaliyet dsf 
                        WHERE
                    dsf.servisid not in (1,134,136) and      
                    dsf.YEDEKPARCAFALIYETRAPORTARIHI <   to_date(to_char(sysdate, 'dd/mm/yyyy'), 'dd/mm/yyyy') 
                    ";
            $statement = $pdo->prepare($sql);            
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
    
    
    //detay yedek parça sayfası fonk. son
    
    
    
    
    //detay yedek parça hedef fonk. baş
    
    
    /**
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function getAfterSalesYedekParcaHedefServissiz($args = array()) {
        
       
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                select   SERVISID,  
                (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid =  zz.servisid) as servisad,   
                 TYPE ,                    
                 to_number(OCAK_MAYIS2017) OCAK_MAYIS2017,               
                 to_number(OCAK_MAYIS2018) OCAK_MAYIS2018   ,         
                 to_number(KARSILASTIRMA_1718_OM) KARSILASTIRMA_1718_OM   ,   
                 to_number(TOPLAM_2017) TOPLAM_2017 ,               
                 to_number(Y3ILLIK_ORTALAMA) Y3ILLIK_ORTALAMA  ,            
                 to_number(AYLIK_GERCEKLESME_MIKTARI) AYLIK_GERCEKLESME_MIKTARI  ,
                 to_number(AYLIK_7ICIN_GEREKEN_MIKTAR) AYLIK_7ICIN_GEREKEN_MIKTAR , 
                 to_number(AYLIK_8ICIN_GEREKEN_MIKTAR) AYLIK_8ICIN_GEREKEN_MIKTAR ,
                 to_number(AYLIK_9ICIN_GEREKEN_MIKTAR) AYLIK_9ICIN_GEREKEN_MIKTAR  ,
                 to_number(YILLIK_7ICIN_GEREKEN_MIKTAR) YILLIK_7ICIN_GEREKEN_MIKTAR  ,
                 to_number(YILLIK_8ICIN_GEREKEN_MIKTAR) YILLIK_8ICIN_GEREKEN_MIKTAR ,
                 to_number(YILLIK_9ICIN_GEREKEN_MIKTAR) YILLIK_9ICIN_GEREKEN_MIKTAR  ,
                 PARTNERCODE                

               from   SASON.PERFORMANS_YPHEDEF  zz
              where servisid not in (1,134,136)

               order by servisid , id 
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesYedekParcaHedefServisli($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //servisid in (96)
            $servicesQuery = ' servisid  in ('.$_GET['src'].')  ';
            // and vv.servisid in (94,96,98)
            $servicesQuery2 = '  vv.servisid in  ('.$_GET['src'].') and  ';
        }
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
            select   SERVISID,  
                (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid =  zz.servisid) as servisad,   
                 TYPE ,                    
                 to_number(OCAK_MAYIS2017) OCAK_MAYIS2017,               
                 to_number(OCAK_MAYIS2018) OCAK_MAYIS2018   ,         
                 to_number(KARSILASTIRMA_1718_OM) KARSILASTIRMA_1718_OM   ,   
                 to_number(TOPLAM_2017) TOPLAM_2017 ,               
                 to_number(Y3ILLIK_ORTALAMA) Y3ILLIK_ORTALAMA  ,            
                 to_number(AYLIK_GERCEKLESME_MIKTARI) AYLIK_GERCEKLESME_MIKTARI  ,
                 to_number(AYLIK_7ICIN_GEREKEN_MIKTAR) AYLIK_7ICIN_GEREKEN_MIKTAR , 
                 to_number(AYLIK_8ICIN_GEREKEN_MIKTAR) AYLIK_8ICIN_GEREKEN_MIKTAR ,
                 to_number(AYLIK_9ICIN_GEREKEN_MIKTAR) AYLIK_9ICIN_GEREKEN_MIKTAR  ,
                 to_number(YILLIK_7ICIN_GEREKEN_MIKTAR) YILLIK_7ICIN_GEREKEN_MIKTAR  ,
                 to_number(YILLIK_8ICIN_GEREKEN_MIKTAR) YILLIK_8ICIN_GEREKEN_MIKTAR ,
                 to_number(YILLIK_9ICIN_GEREKEN_MIKTAR) YILLIK_9ICIN_GEREKEN_MIKTAR  ,
                 PARTNERCODE                

               from   SASON.PERFORMANS_YPHEDEF  zz
               where 
               --servisid in (96)
                ".$servicesQuery."
               order by id 
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesYedekParcaPDFServissiz($args = array()) {
        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                select  
                servisid , (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = a.servisid) as servisad,  
                concat('http://manperformance.man.com.tr:9000/miya_extras/pdf/',pdfname) linkpdf

                 from SASON.PERFORMANS_PDF a
                    ";
            $statement = $pdo->prepare($sql);            
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
    public function getAfterSalesYedekParcaPDFServisli($args = array()) {
        $servicesQuery = '';
        $servicesQuery2 = '';
        if (isset($_GET['src'])  && $_GET['src']!='') {
            //servisid = 94
            $servicesQuery = ' servisid  in ('.$_GET['src'].')  ';
           
        }
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                select  
                servisid , (Select vtsxy.GIZLIAD FROM SASON.PERFORMANSSERVISLER vtsxy where  vtsxy.servisid = a.servisid) as servisad,  
                concat('http://manperformance.man.com.tr:9000/miya_extras/pdf/',pdfname) linkpdf

                 from SASON.PERFORMANS_PDF a
                where 
                --servisid = 94 
                ".$servicesQuery."
                    ";
            $statement = $pdo->prepare($sql);            
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
    
    
    //detay yedek parça hedef fonk. son
}
