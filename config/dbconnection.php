<?php
    
        /**
        * DB Connection class
        */
        class Dbconnect extends PDO
        {
        	// Change the database setting with yours accordingly
            private $dbengine   = 'mysql';
            private $dbhost     = '127.0.0.1';
            private $dbuser     = 'jules'; // Set your database username
            private $dbpassword = ''; //Set your database password
            private $dbname     = 'kp_db';

        	function __construct()
        	{
        		try{

        			// Connect to the database and save the DB instance in $dbh
	                parent::__construct("".$this->dbengine.":host=$this->dbhost; dbname=$this->dbname", $this->dbuser, $this->dbpassword);
	                $this->exec("SET time_zone = '+08:00'");
	               	// This will allow me to have objects format of my data everytime i fetch from my database
	               	// Or we'll have to do it in each function in which we query data from database
	                $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                    $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, FALSE);

	            }
	            catch (PDOException $e){
	            	// if any error throw an exception
	                echo $e->getMessage();
					die();
	            }
        	}


        }