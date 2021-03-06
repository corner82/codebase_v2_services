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
 * @author Okan CIRAN
 */
class LogConnection extends \DAL\DalSlim {

    /**
     * @author Okan CIRAN
     * @ connection_log tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  10.03.2016
     * @param array $params
     * @return array
     * @throws \PDOException
     */
    public function delete($params = array()) {
       try {           
        } catch (\PDOException $e /* Exception $e */) {
        }
    } 

    /**
     * @author Okan CIRAN
     * @ connection_log tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  10.03.2016  
     * @param array $params
     * @return array
     * @throws \PDOException
     */
    public function getAll($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectLogFactory');
            $statement = $pdo->prepare("
           SELECT 
		a.id, 
		a.s_date, 
		a.pk, 
		a.type_id,
		so.operation_name  ,
		b.oid AS user_id ,
		b.username,
                a.log_datetime,
                a.url, 
                a.path, 
                a.ip, 
                a.params,
                a.method,
                a.request_info
            FROM connection_log a 
            INNER JOIN sys_operation_types so ON so.id = a.type_id
            INNER JOIN info_users b ON CRYPT(b.sf_private_key_value,CONCAT('_J9..',REPLACE(a.pk,'*','/'))) = CONCAT('_J9..',REPLACE(a.pk,'*','/')) 
                Or CRYPT(b.sf_private_key_value_temp,CONCAT('_J9..',REPLACE(a.pk,'*','/'))) = CONCAT('_J9..',REPLACE(a.pk,'*','/'))     
            ORDER BY a.s_date
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
     * @ connection_log tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  10.03.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectLogFactory');
            $pdo->beginTransaction();
            
            $pk = NULL;
            $userIdValue = NULL;
            if ((isset($params['pk']) && $params['pk'] != "")) {
                $pk = $params['pk'] ;
                $userId = InfoUsers::getUserId(array('pk' => $params['pk']));
                if (\Utill\Dal\Helper::haveRecord($userId)) {
                    $userIdValue = $userId ['resultSet'][0]['user_id'];                    
                }
            }              
          
            $sql = "
                INSERT INTO connection_log(
                       pk, 
                       type_id,
                       log_datetime,
                       url, 
                       path, 
                       ip, 
                       op_user_id,
                       params,                       
                       method,
                       request_info
                       )
                VALUES (
                        :pk,
                        :type_id,
                        :log_datetime,
                        :url, 
                        :path, 
                        :ip, 
                        :op_user_id, 
                        :params,                        
                        :method,
                        :request_info
                                             )   ";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':pk', $pk, \PDO::PARAM_STR);
            $statement->bindValue(':type_id', $params['type_id'], \PDO::PARAM_INT);
            $statement->bindValue(':log_datetime', $params['log_datetime'], \PDO::PARAM_STR);
            $statement->bindValue(':url', $params['url'], \PDO::PARAM_STR);
            $statement->bindValue(':path', $params['path'], \PDO::PARAM_STR);
            $statement->bindValue(':ip', $params['ip'], \PDO::PARAM_STR);
            $statement->bindValue(':params', $params['params'], \PDO::PARAM_STR);
            $statement->bindValue(':op_user_id', $userIdValue, \PDO::PARAM_INT);            
            $statement->bindValue(':method', $params['method'], \PDO::PARAM_STR);
            $statement->bindValue(':request_info', $params['request_info'], \PDO::PARAM_STR);

          //  echo debugPDO($sql, $params);
            $result = $statement->execute();
            $insertID = $pdo->lastInsertId('connection_log_id_seq');
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * connection_log tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  10.03.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function update($params = array()) {
        try {            
        } catch (\PDOException $e /* Exception $e */) {           
        }
    }
 
    /**
     * Datagrid fill function used for testing
     * user interface datagrid fill operation   
     * @author Okan CIRAN
     * @ Gridi doldurmak için connection_log tablosundan kayıtları döndürür !!
     * @version v 1.0  10.03.2016
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
            $sort = "a.s_date";
        }

        if (isset($args['order']) && $args['order'] != "") {
            $order = trim($args['order']);
            $orderArr = explode(",", $order);
            //print_r($orderArr);
            if (count($orderArr) === 1)
                $order = trim($args['order']);
        } else {            
            $order = "DESC";
        }
       
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectLogFactory');
            $sql = "
             SELECT 
		a.id, 
		a.s_date, 
		a.pk, 
		a.type_id,
		so.operation_name  ,
		b.oid as user_id ,
		b.username,
                a.log_datetime,
                a.url, 
                a.path, 
                a.ip, 
                a.params,
                a.method,
                a.request_info
            FROM connection_log a 
            INNER JOIN sys_operation_types so ON so.id = a.type_id
            INNER JOIN info_users b ON CRYPT(b.sf_private_key_value,CONCAT('_J9..',REPLACE(a.pk,'*','/'))) = CONCAT('_J9..',REPLACE(a.pk,'*','/')) 
                OR CRYPT(b.sf_private_key_value_temp,CONCAT('_J9..',REPLACE(a.pk,'*','/'))) = CONCAT('_J9..',REPLACE(a.pk,'*','/'))     
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
            //   echo debugPDO($sql, $parameters);
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
     * @ Gridi doldurmak için connection_log tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  10.03.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectLogFactory');
            $sql = "
                SELECT 
                    COUNT(a.id) AS COUNT 
                FROM connection_log a 
                INNER JOIN sys_operation_types so ON so.id = a.type_id
                INNER JOIN info_users b ON CRYPT(b.sf_private_key_value,CONCAT('_J9..',REPLACE(a.pk,'*','/'))) = CONCAT('_J9..',REPLACE(a.pk,'*','/')) 
                    Or CRYPT(b.sf_private_key_value_temp,CONCAT('_J9..',REPLACE(a.pk,'*','/'))) = CONCAT('_J9..',REPLACE(a.pk,'*','/'))     
                ORDER BY a.s_date   ";
            $statement = $pdo->prepare($sql);
           // echo debugPDO($sql, $params);
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

 
}
