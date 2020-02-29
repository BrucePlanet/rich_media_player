<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 03/12/2014
 * Time: 16:01
 */

/**
 * Database
 *
*/


use Phalcon\Db;

class Database extends Db{

    protected $_connection;
    protected $_adapterName;
    protected $_eventsManager;
    protected $_dbListener;
    protected $_strQuery;
    protected $_di;
    protected $_bShared;
    protected $errorHandler;

    /**

     * @param $di
     * @param string $adapterName
     * @param bool $bShared
     * @param bool $bConnect

     */

    function __construct( $di, $adapterName = "mysql_db", $bShared = true, $bConnect = true ){

        $this->_di = $di;
        $this->_adapterName = $adapterName;
        $this->errorHandler = new ErrorHandlerController;

        try {
            if ($bConnect === true) {
            $this->dbConnect($this->_di->getShared($this->_adapterName));
        }

        } catch ( PDOException $e ) {
            $this->errorHandler->displaySorryMsg("Cannot connect to DB");

        }



    }

    /**
     * @param $dbAdapter
     *
     * @throws HTTPException
     */

    public function dbConnect( $dbAdapter ) {

        if (is_object( $dbAdapter )) {
            $this->_connection = $dbAdapter;
        } else {
            throw new HTTPException( "DB Adapter not setup for connection type ({$this->_adapterName})", 500 );
        }
    }

    /**
     * setListener
     *
     * creates a new events manager and assigns to the connection object so that any
     * query can be logged and profiled
     *
     */

    /**
     * Fetches all records from a query
     *
     * @param $strQuery
     * @param $arrNamedBindParams
     * @param int $intFetchMode
     *
     * @return array
     * @throws HTTPException
     */

    public function fetchAll( $strQuery, $arrNamedBindParams = array(), $intFetchMode = Db::FETCH_ASSOC ){

        $this->errorHandler = new ErrorHandlerController;

        if (!isset($this->_connection) || !$this->_connection->getInternalHandler()) {
        // connect using the adapter settings stored in the DI
            $this->dbConnect( $this->_di->get( $this->_adapterName ) );

        }

        try {
            $result = $this->_connection->fetchAll( $strQuery, $intFetchMode , $arrNamedBindParams );
//            throw new \PDOException();

        } catch ( PDOException $e ) {

            $this->errorHandler->displaySorryMsg("Query Parameters Incorrect");


            
        }

        $this->disconnect();
        return $result;

    }

    /**
     * used to fetch and return one row
     *
     * @param $strQuery
     * @param $arrNamedBindParams
     * @param int $intFetchMode
     *
     * @return array
     * @throws HTTPException
     */

    public function fetchOne( $strQuery, $arrNamedBindParams, $intFetchMode = Db::FETCH_ASSOC ){

        if (!isset($this->_connection) || !$this->_connection->getInternalHandler()) {
            $this->dbConnect( $this->_di->get( $this->_adapterName ) );

        }

        try {
            $result = $this->_connection->fetchOne( $strQuery, $intFetchMode, $arrNamedBindParams );
            $this->disconnect();
            return $result;

        } catch ( Db\Exception $ex ) {
            throw new HTTPException( $ex->getMessage(), 500 );
        }
    }

    /**
     *
     * used when we don't need any rows returned
     *
     * @param $strQuery
     * @param $arrNamedBindParams
     *
     * @return bool
     * @throws HTTPException
     */

    public function execute( $strQuery, $arrNamedBindParams = array() ){

        if (!isset($this->_connection) || !$this->_connection->getInternalHandler()) {
            $this->dbConnect( $this->_di->get( $this->_adapterName ) );

        }

        try {
            $result = $this->_connection->execute( $strQuery, $arrNamedBindParams );
            if (!$this->_connection->isUnderTransaction()) {
                $this->disconnect();

            }

            return $result;

        } catch ( Db\Exception $ex ) {

            mail(EMAIL_ADDR,"DB Exception",print_r($ex,true),"from:noreply@kondor.co.uk");

            throw new HTTPException( $ex->getMessage(), 500 );

        }
    }

    /**
     * Closes the active connection returning success.
     * \Phalcon automatically closes and destroys
     * active connections when the request ends
     *
     * @return boolean
     */

    public function disconnect(){
        if ($this->_connection) {
            $this->_connection->close();
        }
    }

    /**
     * flush
     *
     * Flushes the prepared statement.
     * @return void
     */

    public function flush(){
        $this->_connection = null;

    }

    /**
     * @throws HTTPException
     */

    public function beginTransaction(){

        if (!isset($this->_connection) || !$this->_connection->getInternalHandler()) {
            $this->dbConnect( $this->_di->get( $this->_adapterName ) );

        }

// stops trying to create duplicate tx on same object

        if (!$this->_connection->isUnderTransaction()) {
            $this->_connection->begin();

        }
    }

    /**
     * @throws HTTPException
     */

    public function commitTransaction(){

        if ($this->_connection->isUnderTransaction()) {
            try {
                $this->_connection->commit();
                $this->flush();
                $this->disconnect();

            } catch ( Db\Exception $ex ) {
                throw new HTTPException( $ex->getMessage(), 500 );

            }

        } else {
            throw new HTTPException( "Current connection is not in transaction mode", 500 );
        }
    }

    /**
     *
     */

    public function rollBackTransaction(){

        if ($this->_connection->isUnderTransaction()) {
            $this->_connection->rollBack();
            $this->flush();
            $this->disconnect();
        }
    }

    /**
     * Get Affected Row Count
     *
     * Gets the number of rows affected by the last query on the prepared statement.
     *
     * @return integer The number of rows affected.
     * @throws Exception - generic "No prepared statement found."
     *
     */

    public function getAffectedRowCount()
    {
        if (!isset($this->_connection) || !$this->_connection->getInternalHandler()) {
            $this->dbConnect( $this->_di->get( $this->_adapterName ) );

        }

        return $this->_connection->affectedRows();

    }


    /**
     * Get Last Insert ID
     *
     * Gets the ID of the last row inserted to a table with an auto increment column in MySQL. This may be a dangerous function to be using on a heavily hit system.
     *
     * @return mixed The Last Insert ID (integer) or boolean false.
     * @throws Exception - a generic "Couldn't prepare query." when something goes wrong.
     *
     */

    public function getLastInsertId()

    {
        if (!isset($this->_connection) || !$this->_connection->getInternalHandler()) {
            $this->dbConnect( $this->_di->get( $this->_adapterName ) );

        }
        return $this->_connection->lastInsertId();

    }
}