<?php
/**
 *  Framework 
 *
 * @link       
 * @copyright Copyright (c) 2017
 * @license   
 */

namespace DAL\PDO;


/**
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be used by DAL MAnager
 * @
 * @author Okan CİRANĞ
 */
class SysLanguage extends \DAL\DalSlim {

    /**     
     * @author Okan CIRAN
     * @ sys_language tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  07.12.2015
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function delete($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $userId = $this->getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($userId)) {
                $userIdValue = $userId ['resultSet'][0]['user_id'];
                $statement = $pdo->prepare(" 
                UPDATE sys_language
                SET  deleted= 1 , active = 1 ,
                     op_user_id = " . $userIdValue . "     
                WHERE id = :id");
                //Execute our DELETE statement.
                $update = $statement->execute();
                $afterRows = $statement->rowCount();
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);
            } else {
                $errorInfo = '23502';  /// 23502  not_null_violation
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    } 


    /**      
     * @author Okan CIRAN
     * @ sys_language tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  07.12.2015    
     * @param array | null $params
     * @return array
     * @throws \PDOException
     */
    public function getAll($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $statement = $pdo->prepare("
                  SELECT                    
                    a.id, 
                    a.country_name, 
                    a.country_name_eng, 
                    a.country_id, 		
                    a.language_parent_id,		
                    a.deleted, 
		    sd.description as state_deleted,                 
                    a.active, 
		    sd1.description as state_active, 		
                    a.icon_road, 		
                    a.user_id, 
                    u.username,
                    a.country_code3, 		
                    a.link, 		
                    a.language_code, 		
                    a.language_id, 
		    COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,  		
                    a.parent_id, 		                    
                    COALESCE(NULLIF(a.language, ''), a.language_eng) AS language, 
                    a.language_eng,
                    a.language_main_code,
                    a.priority                    
                FROM sys_language  a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND 
			sd.language_id = a.language_id  AND sd.active =0 AND sd.deleted=0
		INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND 
			sd1.language_id = a.language_id AND sd1.deleted = 0 AND sd1.active = 0
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active = 0 
		INNER JOIN info_users u ON u.id = a.user_id  
                ORDER BY a.priority, language                 
                                 ");
            $statement->execute();
            $result = $statement->fetcAll(\PDO::FETCH_ASSOC); 
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {          
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    
    /**
     * @author Okan CIRAN
     * @ info_firm_working_personnel_education tablosunda name sutununda daha önce oluşturulmuş mu? 
     * @version v 1.0  25.07.2016 
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function haveRecords($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $addSql = " AND deleted =0  ";
            if (isset($params['id'])) {
                $addSql .= " AND id != " . intval($params['id']);
            }
            $sql = " 
            SELECT  
                language_code AS name , 
                '" . $params['language_code'] . "' AS value , 
                language_code ='" . $params['language_code'] . "' as control,
                concat(language_code , ' dil kodu daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) as message                             
            FROM sys_language
            WHERE language_code = '" . $params['language_code'] . "'
                LOWER(REPLACE(language_code,' ','')) = LOWER(REPLACE('" . $params['language_code'] . "',' ','')) AND 
                LOWER(REPLACE(language_eng,' ','')) = LOWER(REPLACE('" . $params['language_eng'] . "',' ',''))                      
                " . $addSql . "  
                               ";
            $statement = $pdo->prepare($sql);
         // echo debugPDO($sql, $params);
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
     * @author Okan CIRAN
     * @ sys_language tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  08.12.2015
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $opUserIdParams = array('pk' =>  $params['pk'],);
            $opUserIdArray = $this->slimApp-> getBLLManager()->get('opUserIdBLL');  
            $opUserId = $opUserIdArray->getUserId($opUserIdParams); 
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                    $pdo->beginTransaction();
                    $statement = $pdo->prepare("
                INSERT INTO sys_language(
                        country_name, 
                        country_name_eng, 
                        country_id, 
                        language_parent_id, 
                        icon_road, 
                        op_user_id, 
                        country_code3, 
                        link,  
                        language_code,
                        language_id, 
                        parent_id, 
                        language_eng, 
                        language_main_code, 
                        language, 
                        priority)  
                VALUES (
                        :country_name, 
                        :country_name_eng, 
                        :country_id, 
                        :language_parent_id, 
                        :icon_road, 
                        ".intval($opUserIdValue).", 
                        :country_code3, 
                        :language_code,
                        :link, 
                        :language_id, 
                        :parent_id, 
                        :language_eng, 
                        :language_main_code, 
                        :language, 
                        :priority
                                                ");
                    $statement->bindValue(':country_name', $params['country_name'], \PDO::PARAM_STR);
                    $statement->bindValue(':country_name_eng', $params['country_name_eng'], \PDO::PARAM_STR);
                    $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':language_parent_id', $params['language_parent_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':icon_road', $params['icon_road'], \PDO::PARAM_STR);                    
                    $statement->bindValue(':country_code3', $params['country_code3'], \PDO::PARAM_STR);
                    $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);                    
                    $statement->bindValue(':link', $params['link'], \PDO::PARAM_STR);                    
                    $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':parent_id', $params['parent_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':language_eng', $params['language_eng'], \PDO::PARAM_STR);
                    $statement->bindValue(':language_main_code', $params['language_main_code'], \PDO::PARAM_STR);
                    $statement->bindValue(':language', $params['language'], \PDO::PARAM_STR);
                    $statement->bindValue(':priority', $params['priority'], \PDO::PARAM_INT);
                    $result = $statement->execute();
                    $insertID = $pdo->lastInsertId('sys_language_id_seq');
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
                } else {
                        // 23505  unique_violation
                        $errorInfo = '23505';
                        $errorInfoColumn = 'language_code';
                        $pdo->rollback();
                        // $result = $kontrol;
                        return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
                    }
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**    
     * @author Okan CIRAN
     * sys_language tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  07.12.2015
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function update($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $opUserIdParams = array('pk' =>  $params['pk'],);
            $opUserIdArray = $this->slimApp-> getBLLManager()->get('opUserIdBLL');  
            $opUserId = $opUserIdArray->getUserId($opUserIdParams); 
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                    $pdo->beginTransaction();
                    $statement = $pdo->prepare("
                UPDATE sys_language
                SET              
                    country_name = :country_name, 
                    country_name_eng = :country_name_eng, 
                    country_id  = :country_id, 
                    language_parent_id  = :language_parent_id, 
                    icon_road  = :icon_road, 
                    op_user_id  = ".intval($opUserIdValue).",
                    country_code3  = :country_code3, 
                    link  = :link, 
                    language_code  = :language_code, 
                    language_id  = :language_id, 
                    parent_id  = :parent_id, 
                    language_eng  = :language_eng, 
                    language_main_code  = :language_main_code, 
                    language  = :language, 
                    priority  = :priority
                WHERE id = :id");
                    //Bind our value to the parameter :id.
                    $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
                    //Bind our :model parameter.     
                    $statement->bindValue(':country_name', $params['country_name'], \PDO::PARAM_STR);
                    $statement->bindValue(':country_name_eng', $params['country_name_eng'], \PDO::PARAM_STR);
                    $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':language_parent_id', $params['language_parent_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':icon_road', $params['icon_road'], \PDO::PARAM_STR);                    
                    $statement->bindValue(':country_code3', $params['country_code3'], \PDO::PARAM_STR);
                    $statement->bindValue(':link', $params['link'], \PDO::PARAM_STR);
                    $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_INT);
                    $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':parent_id', $params['parent_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':language_eng', $params['language_eng'], \PDO::PARAM_STR);
                    $statement->bindValue(':language_main_code', $params['language_main_code'], \PDO::PARAM_STR);
                    $statement->bindValue(':language', $params['language'], \PDO::PARAM_STR);
                    $statement->bindValue(':priority', $params['priority'], \PDO::PARAM_INT);
                    //Execute our UPDATE statement.
                    $update = $statement->execute();
                    $affectedRows = $statement->rowCount();
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
                } else {
                        // 23505  unique_violation
                        $errorInfo = '23505';
                        $errorInfoColumn = 'language_code';
                        $pdo->rollback();
                        // $result = $kontrol;
                        return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
                    }
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /** 
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_language tablosundan kayıtları döndürür !!
     * @version v 1.0  08.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGrid($args = array()) {
        if (isset($args['page']) && $args['page'] != "" && isset($args['rows']) && $args['rows'] != "") {
            $offset = ((intval($args['page']) - 1) * intval($args['rows']));
            $limit = intval($args['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }

        $sortArr = array();
        $orderArr = array();
        if (isset($args['sort']) && $args['sort'] != "") {
            $sort = trim($args['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($args['sort']);
        } else { 
            $sort = " a.priority, language";
        }

        if (isset($args['order']) && $args['order'] != "") {
            $order = trim($args['order']);
            $orderArr = explode(",", $order);    
            if (count($orderArr) === 1)
                $order = trim($args['order']);
        } else {     
            $order = "ASC";
        }    
        $languageCode = 'tr';
        $languageIdValue = 647;
        if (isset($args['language_code']) && $args['language_code'] != "") {
            $languageCode = $args['language_code'];
        }
        $languageCodeParams = array('language_code' => $languageCode,);
        $languageId = $this->slimApp-> getBLLManager()->get('languageIdBLL');  
        $languageIdsArray = $languageId->getLanguageId($languageCodeParams);
        if (\Utill\Dal\Helper::haveRecord($languageIdsArray)) {
            $languageIdValue = $languageIdsArray ['resultSet'][0]['id'];
        }  

        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = " 
                 SELECT                    
                    a.id, 
                    a.country_name, 
                    a.country_name_eng, 
                    a.country_id, 		
                    a.language_parent_id,		
                    a.deleted, 
		    sd.description as state_deleted,                 
                    a.active, 
		    sd1.description as state_active, 		
                    a.icon_road, 		
                    a.user_id, 
                    u.username,
                    a.country_code3, 		
                    a.link, 		
                    a.language_code, 		
                    a.language_id, 
		    COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,  		
                    a.parent_id, 		                    
                    COALESCE(NULLIF(a.language, ''), a.language_eng) AS language, 
                    a.language_eng,
                    a.language_main_code,
                    a.priority
                FROM sys_language  a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND 
			sd.language_id = a.language_id  AND sd.active =0 AND sd.deleted=0
		INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND 
			sd1.language_id = a.language_id AND sd1.deleted = 0 AND sd1.active = 0
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active = 0 
		INNER JOIN info_users u ON u.id = a.op_user_id  
                WHERE a.language_id = ".intval($languageIdValue).",                                              
                ORDER BY    " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql); 
            $parameters = array(
                'sort' => $sort,
                'order' => $order,
                'limit' => $pdo->quote($limit),
                'offset' => $pdo->quote($offset),
            );
           // echo debugPDO($sql, $parameters);            
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
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_language tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  08.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $languageCode = 'tr';
            $languageIdValue = 647;
            if (isset($params['language_code']) && $params['language_code'] != "") {
                $languageCode = $params['language_code'];
            }
            $languageCodeParams = array('language_code' => $languageCode,);
            $languageId = $this->slimApp-> getBLLManager()->get('languageIdBLL');  
            $languageIdsArray = $languageId->getLanguageId($languageCodeParams);
            if (\Utill\Dal\Helper::haveRecord($languageIdsArray)) {
                $languageIdValue = $languageIdsArray ['resultSet'][0]['id'];
            }  

            $sql = "             
                SELECT 
                    COUNT(a.id) AS COUNT
                FROM sys_language a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND 
			sd.language_id = a.language_id AND sd.active =0 AND sd.deleted=0
		INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND 
			sd1.language_id = a.language_id AND sd1.deleted = 0 AND sd1.active = 0
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active = 0 
		INNER JOIN info_users u ON u.id = a.op_user_id  
                WHERE a.language_id = ".intval($languageIdValue).",  
                    ";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':language_id', $args['language_id'], \PDO::PARAM_INT);
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
     * @author Okan CIRAN
     * @ combobox ı doldurmak için sys_language tablosundan çekilen kayıtları döndürür   !!
     * @version v 1.0  17.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillComboBox() {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $statement = $pdo->prepare("
                SELECT                    
                    a.id, 	
                    a.language, 
                    a.language_eng,		
                    a.language_main_code                                 
                FROM sys_language  a       
                WHERE  
                    a.deleted = 0 and a.active =0    
                ORDER BY a.priority                
                                 ");
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
     * @author Okan CIRAN
     * @ combobox ı doldurmak için sys_language tablosundan çekilen kayıtları döndürür   !!
     * @version v 1.0  17.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillComboBoxTsql($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactoryMobil'); 
            $languageIdValue = 647;
            if (isset($params['LanguageID']) && $params['LanguageID'] != "") {
                $languageIdValue = $params['LanguageID'];
            }  
            $statement = $pdo->prepare(" 
                SET NOCOUNT ON; 
                IF OBJECT_ID('tempdb..#alert') IS NOT NULL DROP TABLE #alert; 
                IF OBJECT_ID('tempdb..#definitions') IS NOT NULL DROP TABLE #definitions; 
                SELECT * 
                into #alert
                FROM BILSANET_MOBILE.dbo.Mobile_User_Messages alrt
                WHERE alrt.main_group in (7,9,10) and alrt.deleted = 0 and alrt.active =0 ;
                
                SELECT *   
                into #definitions
                from BILSANET_MOBILE.dbo.sys_specific_definitions ddd 
                where [main_group] in(1,3) and ddd.deleted = 0 and ddd.active =0 

                SELECT                    
                    a.id, 
                    a.language, 
                    a.language_eng, 
                    a.language_main_code,
                    a.url, 
                    COALESCE(NULLIF(six.a1 collate SQL_Latin1_General_CP1254_CI_AS,''),si.a1_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS alan1,
                    COALESCE(NULLIF(six.a2 collate SQL_Latin1_General_CP1254_CI_AS,''),si.a2_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS alan2,
                    COALESCE(NULLIF(six.a3 collate SQL_Latin1_General_CP1254_CI_AS,''),si.a3_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS alan3,
                    COALESCE(NULLIF(six.a4 collate SQL_Latin1_General_CP1254_CI_AS,''),si.a4_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS alan4,
                    COALESCE(NULLIF(six.a5 collate SQL_Latin1_General_CP1254_CI_AS,''),si.a5_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS alan5,
                    COALESCE(NULLIF(six.a6 collate SQL_Latin1_General_CP1254_CI_AS,''),si.a6_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS alan6,
                    '' AS alan7,
                    COALESCE(NULLIF(spx.description,''),spx.description_eng) AS alan8 , 
                    COALESCE(NULLIF(a8x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a8x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alan9,
                    COALESCE(NULLIF(a9x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a9x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alan10,
                    COALESCE(NULLIF(a1x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a1x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert1,
                    COALESCE(NULLIF(a2x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a2x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert2,
                    COALESCE(NULLIF(a3x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a3x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert3,
                    COALESCE(NULLIF(a4x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a4x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert4,
                    COALESCE(NULLIF(a5x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a5x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert5,
                    COALESCE(NULLIF(a6x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a6x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert6,
                    COALESCE(NULLIF(a7x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a7x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert7,
                    COALESCE(NULLIF(a10x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a10x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert8,
                    COALESCE(NULLIF(a11x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a11x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert9,
                    COALESCE(NULLIF(a12x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a12x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert10,
                    COALESCE(NULLIF(a13x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a13x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert11,
                    COALESCE(NULLIF(a14x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),a14x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS alert12,

                    COALESCE(NULLIF(i1x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),i1x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS iletisim1,
                    COALESCE(NULLIF(i2x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),i2x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS iletisim2,
                    COALESCE(NULLIF(i3x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),i3x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS iletisim3,
                    COALESCE(NULLIF(i4x.[description] collate SQL_Latin1_General_CP1254_CI_AS,''),i4x.[description_eng] collate SQL_Latin1_General_CP1254_CI_AS) AS iletisim4,
                    
                    COALESCE(NULLIF(ah8x.description collate SQL_Latin1_General_CP1254_CI_AS,''),ah8x.description_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS gun8x,                    
                    COALESCE(NULLIF(ah1x.description collate SQL_Latin1_General_CP1254_CI_AS,''),ah1x.description_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS gun1x,
                    COALESCE(NULLIF(ah2x.description collate SQL_Latin1_General_CP1254_CI_AS,''),ah2x.description_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS gun2x,
                    COALESCE(NULLIF(ah3x.description collate SQL_Latin1_General_CP1254_CI_AS,''),ah3x.description_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS gun3x,
                    COALESCE(NULLIF(ah4x.description collate SQL_Latin1_General_CP1254_CI_AS,''),ah4x.description_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS gun4x,
                    COALESCE(NULLIF(ah5x.description collate SQL_Latin1_General_CP1254_CI_AS,''),ah5x.description_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS gun5x,
                    COALESCE(NULLIF(ah6x.description collate SQL_Latin1_General_CP1254_CI_AS,''),ah6x.description_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS gun6x,
                    COALESCE(NULLIF(ah7x.description collate SQL_Latin1_General_CP1254_CI_AS,''),ah7x.description_eng  collate SQL_Latin1_General_CP1254_CI_AS) AS gun7x
                    
 
                FROM BILSANET_MOBILE.dbo.sys_language a  
                INNER JOIN BILSANET_MOBILE.dbo.Mobile_User_Screen_Items si on si.language_parent_id =0 and si.screen_id = 1 
                LEFT JOIN BILSANET_MOBILE.dbo.Mobile_User_Screen_Items six on (six.language_parent_id =si.id OR six.id =si.id) and six.language_id=a.id and six.screen_id = 1 	 
                LEFT JOIN #alert a1x on a1x.language_id= a.id  and a1x.[main_group] = 9 and a1x.[first_group] = 1  
                LEFT JOIN #alert a2x on a2x.language_id= a.id  and a2x.[main_group] = 9 and a2x.[first_group] = 2  
                LEFT JOIN #alert a3x on a3x.language_id= a.id  and a3x.[main_group] = 9 and a3x.[first_group] = 3 
                LEFT JOIN #alert a4x on a4x.language_id= a.id  and a4x.[main_group] = 9 and a4x.[first_group] = 4  
                LEFT JOIN #alert a5x on a5x.language_id= a.id  and a5x.[main_group] = 9 and a5x.[first_group] = 5  
                LEFT JOIN #alert a6x on a6x.language_id= a.id  and a6x.[main_group] = 9 and a6x.[first_group] = 6 
                LEFT JOIN #alert a7x on a7x.language_id= a.id  and a7x.[main_group] = 9 and a7x.[first_group] = 7  
                LEFT JOIN #alert a10x on a10x.language_id= a.id  and a10x.[main_group] = 9 and a10x.[first_group] = 8 
                LEFT JOIN #alert a11x on a11x.language_id= a.id  and a11x.[main_group] = 9 and a11x.[first_group] = 9 
                LEFT JOIN #alert a12x on a12x.language_id= a.id  and a12x.[main_group] = 9 and a12x.[first_group] = 10 
                LEFT JOIN #alert a13x on a13x.language_id= a.id  and a13x.[main_group] = 9 and a13x.[first_group] = 11 
                LEFT JOIN #alert a14x on a14x.language_id= a.id  and a14x.[main_group] = 9 and a14x.[first_group] = 12 
                
                LEFT JOIN #alert i1x on i1x.language_id= 647  and i1x.[main_group] = 10 and i1x.[first_group] = 1  
                LEFT JOIN #alert i2x on i2x.language_id= 647  and i2x.[main_group] = 10 and i2x.[first_group] = 2  
                LEFT JOIN #alert i3x on i3x.language_id= 647  and i3x.[main_group] = 10 and i3x.[first_group] = 3  
                LEFT JOIN #alert i4x on i4x.language_id= 647  and i4x.[main_group] = 10 and i4x.[first_group] = 4 
                LEFT JOIN #alert a8x on a8x.language_id= a.id  and a8x.[main_group] = 7 and a8x.[first_group] = 1  
                LEFT JOIN #alert a9x on a9x.language_id= a.id  and a9x.[main_group] = 7 and a9x.[first_group] = 2 
                
                LEFT JOIN #definitions spx on spx.language_id= a.id and spx.main_group =1 and spx.first_group =13 
                
                LEFT JOIN #definitions ah1x on ah1x.language_id= a.id  and ah1x.[main_group] = 3 and ah1x.[first_group] = 1  
                LEFT JOIN #definitions ah2x on ah2x.language_id= a.id  and ah2x.[main_group] = 3 and ah2x.[first_group] = 2  
                LEFT JOIN #definitions ah3x on ah3x.language_id= a.id  and ah3x.[main_group] = 3 and ah3x.[first_group] = 3  
                LEFT JOIN #definitions ah4x on ah4x.language_id= a.id  and ah4x.[main_group] = 3 and ah4x.[first_group] = 4  
                LEFT JOIN #definitions ah5x on ah5x.language_id= a.id  and ah5x.[main_group] = 3 and ah5x.[first_group] = 5  
                LEFT JOIN #definitions ah6x on ah6x.language_id= a.id  and ah6x.[main_group] = 3 and ah6x.[first_group] = 6  
                LEFT JOIN #definitions ah7x on ah7x.language_id= a.id  and ah7x.[main_group] = 3 and ah7x.[first_group] = 7  
                LEFT JOIN #definitions ah8x on ah8x.language_id= a.id  and ah8x.[main_group] = 3 and ah8x.[first_group] = -2  
                WHERE  
                    a.deleted = 0 and a.active =0   
                ORDER BY a.priority ;
                IF OBJECT_ID('tempdb..#alert') IS NOT NULL DROP TABLE #alert; 
                IF OBJECT_ID('tempdb..#definitions') IS NOT NULL DROP TABLE #definitions; 
                SET NOCOUNT OFF;
                                 ");
             
                $statement->execute();
             // echo debugPDO($sql, $params);
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
     * @author Okan CIRAN
     * @ sys_language tablosundan id degerini getirir.  !!
     * @version v 1.0  03.02.2016    
     * @param array | null $params
     * @return array
     * @throws \PDOException
     */
    public function getLanguageId($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "     
                SELECT                    
                    a.id   ,
                    a.language_main_code ='" . $params['language_code'] . "'  as control
                FROM sys_language a                                
                where a.deleted =0 AND a.active = 0 AND 
                    a.language_main_code = '" . $params['language_code'] . "'               
                LIMIT 1                ";
           //  echo debugPDO($sql, $params);   
            $statement = $pdo->prepare($sql);
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
     * @author Okan CIRAN
     * @  dropdown ya da tree ye doldurmak için sys_language tablosundan kayıtları döndürür !!
     * @version v 1.0  25.07.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException 
     */
    public function fillLanguageDdList($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');         
            $languageCode = 'tr';
            $languageIdValue = 647;
            if (isset($params['language_code']) && $params['language_code'] != "") {
                $languageCode = $params['language_code'];
            }
            $languageCodeParams = array('language_code' => $languageCode,);
            $languageId = $this->slimApp-> getBLLManager()->get('languageIdBLL');  
            $languageIdsArray = $languageId->getLanguageId($languageCodeParams);
            if (\Utill\Dal\Helper::haveRecord($languageIdsArray)) {
                $languageIdValue = $languageIdsArray ['resultSet'][0]['id'];
            }  

            $statement = $pdo->prepare("        
                SELECT 
                    a.id,
                    COALESCE(NULLIF(sd.language_local, ''), a.language_eng) AS name,  
                    a.language_eng AS name_eng,
                    0 AS active,
                    'open' AS state_type
                FROM sys_language a
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0  
		LEFT JOIN sys_language lx ON lx.id = " . intval($languageIdValue). " AND lx.deleted =0 AND lx.active =0
                LEFT JOIN sys_language sd ON (sd.id =a.id OR sd.language_parent_id = a.id) AND lx.id = sd.language_id
                WHERE  a.lang_choose = 1  
                ORDER BY a.priority ,name
                                 ");
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
    
    
    

}
