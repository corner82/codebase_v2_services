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
                    CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                        CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                        CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.NETTUTAR), 0), '999,999,999,999,999')) END as FATURATUTAR
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
            --TRIM(TO_CHAR(ROUND(sum(a.toplam),0), '999,999,999,999,999')) as FATURATUTAR
            CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                    CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                       CASE 
                               WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                               ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                       CASE 
                               WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                               ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
            CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                            ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
                CASE 
                                WHEN LENGTH(TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')))= 3 THEN '1'
                                ELSE TRIM(TO_CHAR(ROUND(sum(a.toplam), 0), '999,999,999,999,999')) END as FATURATUTAR
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
    public function getAfterSalesDashboardFaturaData($args = array()) {
        
        $today = date('d/m/Y');
        $dayAfter = date('d/m/Y', strtotime(' +1 day'));
        //print_r($today);
        //print_r($dayAfter);
        try {
            $pdo = $this->slimApp->getServiceManager()->get('oracleConnectFactory');
            $sql = "  
                    select 
                        CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))= 3 THEN '1'
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))= 0 THEN '0'
                            WHEN NVL(sum(a.NETTUTAR), 0)= 0 THEN '0'
                            ELSE CONCAT(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')), ' Bin TL') END as a, 
                        --NVL(sum(a.NETTUTAR), 0) a,
                        cast( 'Alış Faturaları ' AS varchar2(300)) as  aciklama,
                        1 as controler
                        FROM faturalar a
                        WHERE  
                        a.ISLEMTARIHI between to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy') and
                         a.faturaturid=4
                    UNION
                    select 
                        CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 3 THEN '1'
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0'
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN '0'
                            ELSE CONCAT(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')), ' Bin TL') END as a, 
                        --NVL(sum(a.toplam), 0) a,
                        cast( 'İş Emri Faturaları ' AS varchar2(300)) as aciklama,
                        2 as controler
                        FROM faturalar a
                        WHERE 
                        a.ISLEMTARIHI between to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                        and a.faturaturid=1
                    UNION
                    select 
                        CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 3 THEN '1'
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0'
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN '0'
                            ELSE CONCAT(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')), ' Bin TL') END as a, 
                        --NVL(sum(a.toplam), 0) a,
                        cast( 'Satış Faturaları ' AS varchar2(300)) as aciklama,
                        3 as controler
                        FROM faturalar a
                        WHERE  
                        a.islemtarihi between to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                        and a.faturaturid=3 
                    UNION
                    select 
                        CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 3 THEN '1 Bin TL'
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')))= 0 THEN '0 TL'
                            WHEN NVL(sum(a.toplam), 0)= 0 THEN '0'
                            ELSE CONCAT(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')), ' Bin TL') END as a,
                        --NVL(sum(a.toplam), 0) toplam,
                        cast( 'İcmal Faturaları' AS varchar2(300)) as aciklama,
                        4 as controler
                        FROM faturalar a
                        WHERE  
                        a.ISLEMTARIHI between to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                        and a.faturaturid=2
                    UNION
                    select 
                        CASE 
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))= 3 THEN '1'
                            WHEN LENGTH(TRIM(TO_CHAR(sum(a.NETTUTAR), '999,999,999,999,999')))= 0 THEN '0'
                            WHEN NVL(sum(a.NETTUTAR), 0)= 0 THEN '0'
                            ELSE CONCAT(TRIM(TO_CHAR(sum(a.toplam), '999,999,999,999,999')), ' Bin TL') END as a,
                        --NVL(sum(a.NETTUTAR), 0) a,
                        cast( 'Dış Hizmet Faturaları' AS varchar2(300)) as aciklama,
                        5 as controler
                        FROM faturalar a
                        WHERE 
                        a.islemtarihi between to_date('".$today."', 'dd/mm/yyyy') AND to_date('".$dayAfter."', 'dd/mm/yyyy')
                        and a.faturaturid=5 
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

}
