<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: david rodal
 * Date: 10/17/11
 * Time: 5:00 PM
 * To change this template use File | Settings | File Templates.
 */

require_once('Sag.php');
require_once('SagFileCache.php');

/**
 * Couchsag codeigniter library to access Sag driver for couch db.
 * See Sag.php for more information...
 */
class Couchsag
{

    public $sag;

    /**
     * @param $params Params to be passed to sag driver. Values can be
     * "host"
     * "port"
     * "database"
     * "user"
     * "password"
     * "auth_type" "AUTH_BASIC" or "AUTH_COOKIE";
     */
    public function __construct($params)
    {
        $host = "127.0.0.1";
        $port = 443;

        if (is_array($params)) {
            foreach ($params as $key => $value) {
                switch ($key) {
                    case 'host':
                        $host = $value;
                        break;
                    case 'port':
                        $port = $value;
                        break;
                    case 'user':
                        $user = $value;
                        break;
                    case 'password':
                        $password = $value;
                        break;
                    case 'auth_type':
                        $auth_type = $value;
                        break;
                    case 'database':
                        $database = $value;
                        break;
                }
            }
            $this->sag = new Sag($host, $port);
		$this->sag->setHttpAdapter('HTTP_NATIVE_SOCKETS');
            if ($password || $user || $auth_type) {
                $this->sag->login($user, $password, $auth_type);
            }
            if ($database) {
                $this->sag->setDatabase($database);
            }
            try {
//        $cache = new SagFileCache("/tmp");
            } catch (Exception $e) {
                throw $e;
            }
//        $this->sag->setCache($cache);
        }
    }

    /**
     * @param $id the Id of the document to be fetched.
     * @return returns the document associated with the $id
     */

    function get($id)
    {
        try {
            $body = $this->sag->get($id)->body;
        } catch (Exception $e) {

            if($e->getCode() == 404){
                throw $e;
            }
            if ($e->getCode() == 402)
                return false;
            var_dump($e->getMessage());
            die("CouchException get");
        }
        return $body;
    }

    /**
     * @param int $count The number of unique id's to be generated.
     * @return returns a list of unique id's generated by couch db.
     */
    function uuids($count = 1)
    {
        return $this->sag->generateIDs($count);
    }

    /**
     * @param $id id of the document to be updated (via http PUT)
     * @param $the new data for the document
     * @return the response of the update
     */
    function update($id, $data)
    {
        try {

            return $this->sag->put($id, $data)->body;
        } catch (Exception $e) {
            var_dump($e->getMessage());
            die("CouchException Update");
        }
    }

    /**
     * @param $id The id of the document to be created (via http POST)
     * @param $data The data to be placed into the document
     * @return mixed The respons of the creation
     */
    function create($id, $data = null)
    {
        try {
            return $this->sag->post($id, $data);
        } catch (Exception $e) {

          throw $e;
        }
    }

    /**
     *
     * @param $id The id of the document to be deleted
     * @param $rev
     * @return mixed the response of the deletion
     */
    function delete($id, $rev)
    {
        try {
            return $this->sag->delete($id, $rev);
        } catch (Exception $e) {

            var_dump($e->getMessage());
            die("CouchException delete");
        }
    }
}
