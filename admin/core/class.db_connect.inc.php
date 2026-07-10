<?php
class db_connect
{

    protected $db;

    protected function __construct($db = NULL)
    {

        if (is_object($db)) {

            $this->db = $db;

        }  else  {

            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;

            try  {

                $this->db = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

                if (isset($GLOBALS['logger'])) {
                    $GLOBALS['logger']->info('Database connection established');
                }

            } catch (Exception $e) {

                if (isset($GLOBALS['logger'])) {
                    $GLOBALS['logger']->emergency('Database connection failed', ['error' => $e->getMessage()]);
                }

                die ('Database connection failed. Please check your configuration.');
            }
        }
    }

    public function getPdo()
    {
        return $this->db;
    }

    public function getDatabase()
    {
        if (isset($GLOBALS['flycash_db'])) {
            return $GLOBALS['flycash_db'];
        }
        return null;
    }
}
