<?php

/*
  Database.php
  Copyright (C) 2005  Eric Kinolik

  This library is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this library; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

  Developer contact: Eric Kinolik eric@narcopia.com
*/

define('DATABASECLASS', 1);

class Database {
   public $hostname;
   public $username;
   public $password;
   public $database;

   public $result;
   public $db_id;
   public $db_x;
   public $db_o;
   public $db_c;
   public $db_cu;
   public $dbg;
   public $error;
   public $rows;
   public $row_count;
   public $echo;
   public $last_seq;
   public $seq_name;
   public $typecasts;
   public $limit;
   public $offset;
   public $sort;
   public $savepoints;
   public $in_transaction;

   public $force_escape;

   public $cur_row;

   public $sql;

   public $last_query;
   public $query_list;
   public $query_count;

   public function __construct() {
	   $this->Database_construct();
   }

   public function Database_construct() {
	   $this->hostname = 'localhost';
	   $this->dbg = 0;
	   $this->error = FALSE;

	   $this->rows = array();
	   $this->typecasts = array();
	   $this->query_list = '';

	   $this->force_escape = 1;
	   $this->query_count = 0;

	   $this->limit = FALSE;
	   $this->offset = FALSE;
	   $this->sort = FALSE;

	   $this->in_transaction = FALSE;
	   $this->savepoints = array();
   }

   private function connect() {
      /* Connect to DB Server */
      if (strtolower($this->sql) == 'pgsql') {
	 $this->db_id = @pg_connect('host='.$this->hostname.' user='.
	                $this->username. ' password=' .$this->password.
		        ' dbname='.$this->database);
	 return $this->db_id;
      } elseif (strtolower($this->sql) == 'mysql') {
         $this->db_id = @mysql_connect($this->hostname, $this->username,
                                       $this->password);
	 if ($this->db_id !== FALSE) $this->select_db();

	 return $this->db_id;
      } elseif (strtolower($this->sql) == 'mongo') {
	 $this->db_x = new Mongo('mongodb://'.$this->hostname);
	 $this->db_o = new MongoDB($this->db_x, $this->database);
	 $ret = $this->db_o->authenticate($this->username, $this->password);
	 if ($ret['ok'] == 0) {
	    $this->error = $ret['errmsg'];
	    return FALSE;
	 }

	 return $this->db_o;
      } else {
	 $this->error = 'Unknown Database Type';
	 return FALSE;
      }
   }

   public function mysql_db_connect() {
      /* Connect to mysql DB Server */
      $this->db_id = @mysql_connect($this->hostname, $this->username,
                                   $this->password);
      return $this->db_id;
   }


   public function select_db() {
      /* Select DB */
      if ($this->sql == 'mysql') {
	 return mysql_select_db($this->database, $this->db_id);
      } else if ($this->sql == 'mongo') {
	 return $this->db_o->selectDB($this->database);
      }
   }


   public function connect_to_db($db='', $user='', $pass='', $host='', $sql='') {
      /* Connect to server, then select db */

      /* Add db properties to struct if they're not empty */
      if ( ! empty($sql)) $this->sql = $sql;
      if ( ! empty($db))       $this->database = $db;
      if ( ! empty($user))     $this->username = $user;
      if ( ! empty($pass))     $this->password = $pass;
      if ( ! empty($host))     $this->hostname = $host;

      if (( $this->connect() ) === FALSE ) {
         if ($this->dbg != 0) $this->error = "Could not connect to DB Server\n";
         return FALSE;
      }

      if ($this->sql == 'mysql') {
	 if (( $this->select_db() ) === FALSE && $this->dbg != 0) {
	    $this->error = "Could not find database\n";
	    return FALSE;
	 }
      }

      return TRUE;
   }


   public function close() {
   /* Close DB Connection */
      if ($this->sql == 'pgsql') {
	 $this->commit();
	 pg_close($this->db_id);
	 $this->db_id = '';
	 return TRUE;
      } elseif ($this->sql == 'mysql') {
	 mysql_close($this->db_id);
         $this->db_id = '';
	 return TRUE; 
      } elseif ($this->sql == 'mongo') {
	 $this->db_x->close();
	 unset($this->db_cu, $this->db_c, $this->db_o, $this->db_x);
	 return TRUE;
      } else {
         return FALSE;
      }
   }


   public function query($query, $table='') {
      /* Query DB */
      if (ERROR_DEBUG === 2) echo $query."\n";

      if (PRODUCTION === FALSE && ERROR_DEBUG === 3) 
	 system('echo '.escapeshellarg($query).' >> /tmp/db_queries');

      if ($this->sql == 'pgsql') {

	 $this->last_query = $query;
	 $this->query_list .= $query . "<BR>\n";

	 if ($this->dbg === 2) echo $query;

	 $this->result = @pg_query($this->db_id, $query) or die (pg_last_error($this->db_id));
	 $this->rows = array();
         $this->row_count = 0;
	 $this->cur_row = 0;
	 $this->typecasts = array();
         $this->query_count++;
//	 if (! empty($table) && ($this->result !== FALSE)) {
//	    $seq = $table . '_seq';
//	    $query = 'SELECT currval(\''.$seq.'\') as currval';
//	    $result = @pg_query($this->db_id, $query);
//	    $row = @pg_fetch_row($result, 0);
//	    $this->last_seq = $row[0];
//	 }
      } elseif ($this->sql == 'mysql') {
	
	 $this->result = mysql_query($query, $this->db_id) or die('mysql error');
	 $this->cur_row = 0;
         $this->row_count = 0;
         $this->query_count++;
//	 if (! empty($table)) {
//	    $query = 'SELECT LAST_INSERT_ID() as last_id';
//	    $result = @mysql_query($query, $this->db_id);
//	    $row = mysql_fetch_row($result);
//	    $this->last_seq = $row[0];
//	 }
      } else {
	 $this->get_error();
	 return FALSE;
      }

      if ($this->result === FALSE && $this->dbg != 0) {
	 $this->get_error();
	 return FALSE;
      }

      return TRUE;
   }
 
   private function get_last_seq($table) {
      if ($this->sql == 'pgsql') {
         if ($this->seq_name == '')
	    $seq = $table . '_seq';
         else
            $seq = $this->seq_name;
	 $query = 'SELECT currval(\''.$seq.'\') as currval';
	 $result = @pg_query($this->db_id, $query);
	 $row = @pg_fetch_row($result, 0);
	 $this->last_seq = $row[0];
         $this->seq_name = '';
         $this->query_count++;
      } elseif ($this->sql == 'mysql') {
	 $query = 'SELECT LAST_INSERT_ID() as last_id';
	 $result = @mysql_query($query, $this->db_id);
	 $row = mysql_fetch_row($result);
	 $this->last_seq = $row[0];
	 $this->query_count++;
      } elseif ($this->sql == 'mongo') {
	 $this->last_seq = $table['_id']->__toString();
	 $this->query_count++;
      } else {
	 $this->error = 'No sql type selected';
	 return FALSE;
      }

      return TRUE;
   }

   public function get_error() {
   /* Get the last error */
      if ($this->sql == 'pgsql') {
	 $this->error = $this->last_query . "<BR>\n";
	 $this->error .= pg_last_error($this->db_id);
	 return TRUE;
      } elseif ($this->sql == 'mysql') {
	 $this->error = $this->last_query . "<BR>\n";
	 $this->error .= mysql_last_error($this->db_id);
	 return TRUE;
      } else {
	 return FALSE;
      }
   }

   public function begin($name='') {
   /* Begin Transaction */
      if ($this->sql == 'pgsql') {
	 if ($this->in_transaction) {
	    $time = microtime(TRUE);
	    $this->savepoint($time);
	    return $time;
	 } else {
	    $this->query('BEGIN');
	    $this->in_transaction = TRUE;
	    $this->save_points = array();
	 }
      } else {
	 $this->error = 'MySQL can not use transactions';
	 return FALSE;
      }
   }

   
   public function commit() {
   /* Commit Transaction */
      if ($this->sql == 'pgsql') {
	 if ($this->in_transaction && count($this->savepoints) > 0) {
	    array_pop($this->savepoints);
	    return TRUE;
	 }

	 $this->query('COMMIT');
	 $this->savepoints = array();
	 $this->in_transactoin = FALSE;
      } else {
	 $this->error = 'MySQL can not use transactions';
	 return FALSE;
      }

      return TRUE;
   }

   
   public function rollback($all=FALSE, $name='') {
   /* Rolback Transaction */
      if ($this->sql == 'pgsql') {
	 if ($all === FALSE && count($this->savepoints) > 0 && strlen(trim($name)) > 0) {

	    $safe = FALSE;
	    /* Verify the savepoint exists */
	    for ($i = 0, $iz = count($this->savepoints); $i < $iz; ++$i) {
	       if ($this->savepoints[$i] == $name) {
		  /* If it exists remove it and all the savepoints after it */
		  while (($latest = array_pop($this->savepoints)) != $name); 
		  $safe = TRUE;
		  break;
	       }
	    }
	    /* If the savepoint didn't exist exit */
	    if ($save === FALSE) return FALSE;

	    $this->query('ROLLBACK TO '.$name);

	 } else if ($all === FALSE && count($this->savepoints) > 0) {
	    $latest = array_pop($this->savepoints);
	    $this->query('ROLLBACK TO "'.$latest.'"');
	 } else {
	    $this->query('ROLLBACK');
	    $this->savepoints = array();
	    $this->in_transaction = FALSE;
	 }
      } else {
	 $this->error = 'MySQL can not use transactions';
	 return FALSE;
      }
   }

   public function savepoint($name='') {
      if ($this->sql === 'pgsql') {
	 if ($this->in_transaction === FALSE) return $this->begin();

	 if (strlen(trim($name)) < 1) {
	    $name = microtime(TRUE);
	    $this->savepoints[] = $name;
	 } else {
	    $this->savepoints[] = $name;
	 }

	 $this->query('SAVEPOINT "'.$name.'"');
	 return $name;
      } else {
	 $this->error = 'MySQL can not use transactions';
	 return FALSE;
      }
   }

  
   public function get_row_count() {
      if ($this->sql == 'pgsql') {
	 $this->row_count = pg_num_rows($this->result);
	 return TRUE;
      } elseif ($this->sql == 'mysql') {
         $this->row_count = mysql_num_rows($this->result);
	 return TRUE;
      }

      return FALSE;
   }


   public function fetch_array() {
      if ($this->sql == 'pgsql') {
	 $this->row_count = pg_num_rows($this->result);
	 while($this->cur_row < $this->row_count) {
	    $tmp = pg_fetch_array($this->result, $this->cur_row, PGSQL_ASSOC);
	    $this->rows[] = $tmp;
	    $this->cur_row++;
	 }
	 return TRUE;
      } elseif ($this->sql == 'mysql') {
	 $this->rows = '';
         $this->row_count = mysql_num_rows($this->result);
	 while(($tmp = mysql_fetch_array($this->result, MYSQL_ASSOC))){
	    $this->rows[] = $tmp;
	    $this->cur_row++;
	    }
	 return TRUE;
      } elseif ($this->sql == 'mongo') {
	 if ( ! is_object($this->db_cu)) return FALSE;

	 if ($this->limit !== FALSE)  $this->db_cu->limit($this->limit);
	 if ($this->offset !== FALSE) $this->db_cu->skip($this->offset); else $this->offset = 0;
	 if (is_array($this->sort))   $this->db_cu->sort($this->sort);
	 $this->row_count = $this->db_cu->count(true);

	 for ($i = 0; $i < $this->row_count; ++$i) {
	    $row = $this->db_cu->getNext();
	    $row['_id'] = $this->db_cu->key();
	    $this->rows[] = $row;
	    $this->cur_row++;
	 }

	 return TRUE;
      }

      return FALSE;
   }
  
   
   public function fetch_row() {
      if ($this->sql == 'pgsql') {
	 $this->row_count = pg_num_rows($this->result);
	 $this->rows = @pg_fetch_array($this->result, $this->cur_row,PGSQL_ASSOC);
	 $this->cur_row++;
	 if ($this->rows === FALSE) {return FALSE;} else {return TRUE;}
      } elseif ($this->sql == 'mysql') {
         $this->row_count = mysql_num_rows($this->result);
	 $this->rows = mysql_fetch_array($this->result, MYSQL_ASSOC);
	 if ($this->rows === FALSE) {return FALSE;} else {return TRUE;}
      } elseif ($this->sql == 'mongo') {
	 if ( ! is_object($this->db_cu)) return FALSE;

	 if (is_array($this->sort))   $this->db_cu->sort($this->sort);
	 $this->row_count = $this->db_cu->count(true);

	 $row = $this->db_cu->getNext();
	 if (! is_array($row)) {
	    $this->rows = array();
	    return FALSE;
	 }

	 $row['_id'] = $this->db_cu->key();
	 $this->rows = $row;
	 $this->cur_row++;

	 return TRUE;
      }

      return FALSE;
   }


   public function encrypt($value, $key, $cipher='aes') {
   /* Creates an sql string to encrypt $value */
      if ($this->sql == 'pgsql') {
	 if ($cipher == 'aes' || $cipher == 'bf') {
	    $sql = ' encrypt(\''.$value.'\', \''.$key.'\', \''.$cipher.'\') ';
	 } else {
	    $this->error = 'Invalid pgsql cipher type';
	    return FALSE;
	 }
      } elseif ($this->sql == 'mysql') {
	 if ($cipher == 'aes') {
	    $sql = " AES_ENCRYPT('" . $value . "', '" . $key . "') ";
	 } elseif ($cipher == 'des') {
	    $sql = " DES_ENCRYPT('" . $value . "', '" . $key . "') ";
	 } else {
	    $this->error = 'Invalid mysql cipher type';
	    return FALSE;
	 }
      } else {
	 $this->error = 'Invalid sql type';
	 return FALSE;
      }

      return $sql;
   }

   public function update_db($table, $input, $where, $exceptions='') {
      if (($sql = $this->create_update_query($table, $input, $where, $exceptions)) === FALSE)
	 return FALSE;

      if ($this->sql == 'pgsql') {
	 if ($this->dbg == '2') echo $sql . "<BR>\n";
	 $return = $this->query($sql);
      } elseif ($this->sql == 'mysql') {
	 if ($this->dbg == '2') echo $sql . "<BR>\n";
	 $return = $this->query($sql);
      } elseif ($this->sql == 'mongo') {
	 $this->db_c = $this->db_x->selectDB($this->database)->selectCollection($table);
	 $this->replace_mongo_id($sql['criteria']);
	 $return = $this->db_c->update($sql['criteria'], $sql['newobj'], $sql['options']);
      }

      return $return;
   }

   public function delete_db($table, $input, $exceptions='') {
      if (($sql = $this->create_delete_query($table, $input, $exceptions)) === FALSE)
	 return FALSE;

      if ($this->sql == 'pgsql') {
	 if ($this->dbg == '2') echo $sql . "<BR>\n";
	 $return = $this->query($sql);
      } elseif ($this->sql == 'mysql') {
	 if ($this->dbg == '2') echo $sql . "<BR>\n";
	 $return = $this->query($sql);
      } elseif ($this->sql == 'mongo') {
	 $this->db_c = $this->db_x->selectDB($this->database)->selectCollection($table);
	 $this->replace_mongo_id($sql['criteria']);
	 $return = $this->db_c->remove($sql['criteria'], $sql['options']);
      }

      return $return;
   }

   public function insert_db($table, $input, $skip_last=FALSE, $exceptions='') {
      if (($sql = $this->create_insert_query($table, $input, $exceptions)) === FALSE)
	 return FALSE;

      if ($this->sql == 'pgsql') {
	 if ($this->dbg == '2') echo $sql . "<BR>\n";
	 $return = $this->query($sql);
	 if ($return === TRUE && $skip_last === FALSE) $this->get_last_seq($table);
      } elseif ($this->sql == 'mysql') {
	 if ($this->dbg == '2') echo $sql . "<BR>\n";
	 $return = $this->query($sql);
	 if ($return === TRUE && $skip_last === FALSE) $this->get_last_seq($table);
      } elseif ($this->sql == 'mongo') {
	 $this->db_c = $this->db_x->selectDB($this->database)->selectCollection($table);

	 $return = $this->db_c->insert($sql, true);
	 if ($return['ok'] == 1 && $skip_last === FALSE) $this->get_last_seq($sql);
      }
      return $return;
   }

   public function find_db($table, $input) {
      if ($this->sql == 'mongo') {
	 if (is_object($this->db_cu)) $this->db_cu->reset();

	 $this->db_c = $this->db_x->selectDB($this->database)->selectCollection($table);
	 $this->db_cu = $this->db_c->find(array('foo'=>'bar'));

	 $this->cur_row = 0;
	 $this->rows = array();
	 
      } else {
	 $this->error = 'Can not use find with this type of database';
	 return FALSE;
      }

      return TRUE;
   }
   
   private function create_update_query($table, $input, $where, $exceptions='') {
      if ($this->sql == 'pgsql') {
	 $FUNC_STRING = '__SQL_FUNCTION__';
	 if (empty($exceptions)) $exceptions = array();

	 $sql = 'UPDATE "'.$table.'" SET ';
     
	 $loop_c = 0;
	 while((list($key, $value) = each($input)) !== FALSE){
	    for ($i=0; $i<count($exceptions);$i++) if ($exceptions[$i] == $key) continue 2;
	    if ($loop_c > 0) $sql .= ', ';

	    $typecast = '';
	    if (array_key_exists($key,$this->typecasts)) $typecast = '::'.$this->escape($this->typecasts[$key]);

	    if ($value === 0) {
	       $sql .= ' "'.$key.'" = NULL ';
	    } elseif (substr($value, 0, strlen($FUNC_STRING)) == $FUNC_STRING) {
	       $sql .= ' "'.$key.'" = '.substr($value, strlen($FUNC_STRING) + 1) . ' ';
	    } else {
	       $sql .= ' "'.$key.'" = \''.$value.'\'' . $typecast . ' ';
	    }
	    $loop_c++;
	 }

	 $loop_c = 0;
	 while((list($key, $value) = each($where)) !== FALSE) {
	    if ($loop_c == 0) { $sql .= ' WHERE '; } else { $sql .= ' AND '; }

	    list($operator, $key) = $this->get_operator($key);

	    if ($value === 0) {
	       $sql .= ' "'.$key.'" IS NULL ';
	    } elseif (substr($value, 0, strlen($FUNC_STRING)) == $FUNC_STRING) {
	       $sql .= ' "'.$key.'" '.$operator.' '.substr($value, strlen($FUNC_STRING) + 1) . ' ';
	    } else {
	       $sql .= ' "'.$key.'" '.$operator.' \''.$value.'\' ';
	    }
	    $loop_c++;
	 }
      } elseif ($this->sql == 'mysql') {
	 if (empty($exceptions)) $exceptions = array();

	 $sql = 'UPDATE '.$table.' SET ';
     
	 $loop_c = 0;
	 while((list($key, $value) = each($input)) !== FALSE){
	    for ($i=0; $i<count($exceptions);$i++) if ($exceptions[$i] == $key) continue 2;
	    if ($loop_c > 0) $sql .= ', ';

	    $sql .= ' '.$key.' = \''.$value.'\' ';
	    $loop_c++;
	 }

	 $loop_c = 0;
	 while((list($key, $value) = each($where)) !== FALSE) {
	    if ($loop_c == 0) { $sql .= ' WHERE '; } else { $sql .= ' AND '; }
	    $sql .= ' '.$key.' = \''.$value.'\' ';
	    $loop_c++;
	 }
      } elseif ($this->sql == 'mongo') {
	 $sql['criteria'] = $where;
	 $sql['newobj'] = array('$set'=>$input);
	 if ( ! is_array($exceptions)) {
	    $sql['options'] = array('upsert'=>false, 'multiple'=>true);
	 } else {
	    $sql['options'] = $exceptions;
	 }
      } else {
	 $this->error = 'No sql type selected';
	 return FALSE;
      }

      return $sql;
   }

   private function create_delete_query($table, $where, $exceptions='') {
      if ($this->sql == 'pgsql') {
	 if (empty($exceptions)) $exceptions = array();

	 $sql = 'DELETE FROM "'.$table.'" WHERE ';
	 $loop_c = 0;
	 while((list($key, $value) = each($where)) !== FALSE) {
	    for ($i=0; $i < count($exceptions);$i++) if ($exceptions[$i] == $key) continue 2;
	    if ($loop_c > 0) $sql .= ' AND ';
	    
	    list($operator, $key) = $this->get_operator($key);

	    if ($value === 0) {
	       $sql .= ' "'.$key.'" IS NULL ';
	    } else {
	       $sql .= ' "'.$key.'" '.$operator.' \''.$value.'\' ';
	    }

	    $loop_c++;
	 }
      } elseif ($this->sql == 'mysql') {
	 if (empty($exceptions)) $exceptions = array();

	 $sql = 'DELETE FROM '.$table.' WHERE ';
	 $loop_c = 0;
	 while((list($key, $value) = each($where)) !== FALSE) {
	    for ($i=0; $i < count($exceptions);$i++) if ($exceptions[$i] == $key) continue 2;
	    if ($loop_c > 0) $sql .= ' AND ';

	    $sql .= ' '.$key.' = \''.$value.'\' ';
	    $loop_c++;
	 }
      } elseif ($this->sql == 'mongo') {
	 $sql['criteria'] = $where;
	 if ( $exceptions === TRUE ) {
	    $sql['options'] = TRUE;
	 } else {
	    $sql['options'] = FALSE;
	 }
      } else {
	 $this->error = 'No sql type selected';
	 return FALSE;
      }

      return $sql;
   }

   private function create_insert_query($table, $input, $exceptions='') {
      if ($this->sql == 'pgsql') {
	 $FUNC_STRING = '__SQL_FUNCTION__';
	 if (empty($input)) {$this->error = 'Invalid Input'; return FALSE;}
	 if (empty($exceptions)) $exceptions = array();

	 $keys   = '';
	 $values = '';
     
	 $loop_c = 0;
	 while((list($key, $value) = each($input)) !== FALSE){
	    for ($i=0; $i<count($exceptions);$i++) if ($exceptions[$i] == $key) continue 2;
	    if ($loop_c > 0) {$keys .= ', '; $values .= ', ';}
	    $loop_c++;

	    $typecast = '';
	    if (array_key_exists($key,$this->typecasts)) $typecast = '::'.$this->escape($this->typecasts[$key]);

	    $keys   .= ' "'.$key.'" ';
	    if ($value === 0) {
	       $values .= ' NULL ';
	    } elseif (substr($value, 0, strlen($FUNC_STRING)) == $FUNC_STRING) {
	       $values .= substr($value, strlen($FUNC_STRING) + 1) . ' ';
	    } else {
	       $values .= ' \''.$value.'\'' . $typecast . ' ';
	    }
	 }
	 $sql = 'INSERT INTO "'.$table.'" ('.$keys.') VALUES ('.$values.')';
	 
      } elseif ($this->sql == 'mysql') {
	 if (empty($exceptions)) $exceptions = array();

	 $keys   = '';
	 $values = '';

	 $loop_c = 0;
	 while((list($key, $value) = each($input)) !== FALSE){
	    for ($i=0; $i<count($exceptions);$i++) if ($exceptions[$i] == $key) continue 2;
	    if ($loop_c > 0) {$keys .= ', '; $values .= ', ';}
	    $loop_c++;

	    $keys   .= ' '.$key.' ';
	    $values .= ' \''.$value.'\' ';
	 }
	 $sql = 'INSERT INTO '.$table.' ('.$keys.') VALUES ('.$values.')';

      } elseif ($this->sql == 'mongo') {
	 if (empty($exceptions)) $exceptions = array();

	 $sql = $input;
      } else {
	 $this->error = 'No sql type selected';
	 return FALSE;
      }

      return $sql;
   }

   public function create_date_format($column, $format) {
      if ($this->sql == 'pgsql') {
	 $switches = explode('%', $format);
	 $time_format = '';
	 for ($i = 0; $i < count($switches); $i++) {
	    $first = substr($switches[$i], 0, 1);
	    $rest = substr($switches[$i], 1);
	    switch ($first) {
	       case "a": $switch = 'am'.$rest; break;
	       case "A": $switch = 'AM'.$rest; break;
	       case "d": $switch = 'DD'.$rest; break;
	       case "D": $switch = 'Dy'.$rest; break;
	       case "F": $switch = 'Month'.$rest; break;
	       case "g": $switch = 'HH12'.$rest; break;
	       case "G": $switch = 'HH24'.$rest; break;
	       case "h": $switch = 'HH12'.$rest; break;
	       case "H": $switch = 'HH24'.$rest; break;
	       case "i": $switch = 'MI'.$rest; break;
	       case "j": $switch = 'DD'.$rest; break;
	       case "l": $switch = 'Day'.$rest; break;
	       case "m": $switch = 'MM'.$rest; break;
	       case "M": $switch = 'Mon'.$rest; break;
	       case "n": $switch = 'MM'.$rest; break;
	       case "r": $switch = 'Dy, DD Mon YYYY HH:MI:SS'.$rest; break;
	       case "s": $switch = 'SS'.$rest; break;
	       case "T": $switch = 'TZ'.$rest; break;
	       case "w": $switch = 'D'.$rest; break;
	       case "W": $switch = 'WW'.$rest; break;
	       case "Y": $switch = 'YYYY'.$rest; break;
	       case "y": $switch = 'YY'.$rest; break;
	       case "z": $switch = 'DDD'.$rest; break;
	       default: $switch = $switches[$i]; break;
	    }

	    $time_format .= $switch;
	 }
	 if (strstr($column, '"')) $column = $column; else $column = '"'.$column.'"';
	 $string = ' TO_CHAR('.$column.', \''.$time_format.'\') ';
      } elseif ($this->sql = 'mysql') {
	 $switches = split('%', $format);
	 $time_format = '';
	 for ($i = 0; $i < count($switches); $i++) {
	    $first = substr($format, 0, 0);
	    $rest = substr($format, 1);
	    switch ($first) {
	       case "a": $switch = '%p'.$rest; break;
	       case "A": $switch = '%p'.$rest; break;
	       case "d": $switch = '%d'.$rest; break;
	       case "D": $switch = '%a'.$rest; break;
	       case "F": $switch = '%M'.$rest; break;
	       case "g": $switch = '%l'.$rest; break;
	       case "G": $switch = '%k'.$rest; break;
	       case "h": $switch = '%h'.$rest; break;
	       case "H": $switch = '%H'.$rest; break;
	       case "i": $switch = '%i'.$rest; break;
	       case "j": $switch = '%e'.$rest; break;
	       case "l": $switch = '%W'.$rest; break;
	       case "m": $switch = '%m'.$rest; break;
	       case "M": $switch = '%b'.$rest; break;
	       case "n": $switch = '%c'.$rest; break;
	       case "r": $switch = '%a, %d %b $Y %T'.$rest; break;
	       case "s": $switch = '%s'.$rest; break;
	       case "S": $switch = '%D'.$rest; break;
	       case "w": $switch = '%w'.$rest; break;
	       case "W": $switch = '%v'.$rest; break;
	       case "Y": $switch = '%Y'.$rest; break;
	       case "y": $switch = '%y'.$rest; break;
	       case "z": $switch = '%j'.$rest; break;
	    }

	    $time_format .= $switch;
	    $string = ' DATE_FORMAT('.$column.', \''.$time_format.'\') ';
	 }
      } else {
	 $this->error = 'Invalid sql type selected';
	 return FALSE;
      }

      return $string;
   }


   public function escape($string) {
      if ($this->sql == 'pgsql') {
	 if ($string === 0) return 0;
	 if ((! is_numeric($string) && get_magic_quotes_runtime() == 0 && get_magic_quotes_gpc() == 0) || $this->force_escape == '1') {
	    $string = pg_escape_string($string);
	 }
      } elseif ($this->sql == 'mysql' && get_magic_quotes_runtime() == 0 && get_magic_quotes_gpc() == 0) {
	 $string = mysql_real_escape_string($string);
      } else {
	 $this->error = 'Invalid sql type selected';
	 return FALSE;
      }

      return $string;
   }

   public function escape_binary($binary) {
      if ($this->sql == 'pgsql') {
	 if ($binary === 0) return 0;
	 $binary = pg_escape_bytea($binary);
      } else if ($this->sql == 'mysql') {
	 $binary = $this->escape($binary);
      } else {
	 $this->error = 'This sql type does not use binary types';
	 return FALSE;
      }

      return $binary;
   }
	       

   public function row_exists($table, $column, $id) {
//      if (is_numeric($id) === FALSE || strstr($id, '.') !== FALSE) { $error = 'Invalid row'; return FALSE; }
	 
      $esc_id = $this->escape($id);

      if ($this->sql == 'pgsql') {
	 $query = 'SELECT "'.$column.'" from "'.$table.'" WHERE "'.$column.'" = \''.$esc_id.'\' LIMIT 1';
      } elseif ($this->sql == 'mysql') {
	 $query = 'SELECT '.$column.' FROM '.$table.' WHERE  '.$column.' = \''.$esc_id.'\' LIMIT 1';
      } else {
	 $this->error = 'Invalid sql type selected';
	 return FALSE;
      }

      $this->query($query);
      $this->fetch_row();
      if (! is_array($this->rows) || (is_array($this->rows) && array_key_exists($column, $this->rows) === FALSE)) {
	 $this->error = 'Row doesn\'t exist';
	 return FALSE;
      }

      return TRUE;
   }

   public function escape_all($array) {
      if ( ! is_array($array)) $array[] = $array;
     
      if ($this->sql == 'pgsql') {
	 while ((list($key, $string) = each($array)) !== FALSE) { 
	    if ($string === 0) { $array[$key] = 0; continue; }
            if (is_array($string)) {
               $string = $this->escape_all($string);
               $array[$key] = $string;
               continue;
            }
	    if ((! is_numeric($string) && get_magic_quotes_runtime() == 0 && get_magic_quotes_gpc() == 0) || $this->force_escape == '1') {
	       if ($array[$key] = pg_escape_string($string));
	    }
	 }
	 
      } elseif ($this->sql == 'mysql') {
	 while ((list($key, $string) = each($array)) !== FALSE) { 
	    if ((! is_numeric($string) && get_magic_quotes_runtime() == 0 && get_magic_quotes_gpc() == 0) || $this->force_escape == '1') {
	       $array[$key] = mysql_real_escape_string($string);
	    }
	 }
	 
      } else {
	 $this->error = 'Invalid sql type selected';
	 return FALSE;
      }

      return $array;
   }

   public function format_array($array) {
      if ($this->sql == 'pgsql') {
         if ( ! is_array($array)) return $array;

         $new_value = '';
         for ($i = 0; $i < count($array); $i++) {
            if ($i > 0) $new_value .= ',';
            $new_value .= '"' .$array[$i]. '"';
         }
         return '{' . $new_value . '}';
      } else {
         $this->error = 'Invalid sql type selected';
         return FALSE;
      }

      return FALSE;

   }

   public function pg_arr2arr($pg_arr) {
      if ($this->sql == 'pgsql') {
	 if (substr($pg_arr, 0, 1) != '{' || substr($pg_arr, -1) != '}') {
            if ($pg_arr == '') return FALSE;
            return array($pg_arr);
         }
   
	 $pg_arr = substr($pg_arr, 1, -1);

	 $values = array();
	 $in_quotes = false;
	 for ($i = 0, $j = 0; $i < strlen($pg_arr); $i++) {
	    $char = substr($pg_arr, $i, 1);
	    if ($char == '"' && ($i == 0 || substr($pg_arr, $i - 1, 1) != '\\')) {
	       $in_quotes = !$in_quotes;
	    } elseif ($char == ',' && !$in_quotes) {
	       $values[] = substr($pg_arr, $j, $i - $j);
	       $j = $i + 1;
	    }
	 }
	 $values[] = substr($pg_arr, $j);
	 
	 for ($i = 0; $i < count($values); $i++) {
	    if (strpos($values[$i], '"') === 0) {
	       $values[$i] = substr($values[$i], 1, -1);
	       $values[$i] = str_replace('\\"', '"', $values[$i]);
	       $values[$i] = str_replace('\\\\', '\\', $values[$i]);
	    }
	 }
	 
	 if (count($values) == 1 && trim($values[0]) == '') $values = array();

	 return $values;
      } else {
	 $this->error = 'Invalid sql type';
	 return FALSE;
      }

      return FALSE;
   }

   public function check_table_exists($table) {
      $table = $this->escape($table);
      $query = 'SELECT "relname" FROM "pg_stat_user_tables" WHERE "relname" = \''.$table.'\'';
      $this->query($query);
      $this->fetch_array();
      if (count($this->rows) > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   private function replace_mongo_id(&$array) {
      /* Replace any document id strings with an actual MongoID object */
      while((list($key, $value) = each($array)) !== FALSE) {
	 if ($key === '_id') $array[$key] = new MongoId($value);
      }
   }

   private function get_operator($string) {
      $c1 = substr($string, 0, 1);
      $c2 = substr($string, 1, 1);
      $c3 = substr($string, 2, 1);

      if ($c1.$c2 === '>=') {
	 return array('>=', substr($string, 2));
      } else if ($c1.$c2 === '<=') {
	 return array('<=', substr($string, 2));
      } else if ($c1.$c2 === '!=') {
	 return array('!=', substr($string, 2));
      } else if ($c1.$c2.$c3 === '!~*') {
	 return array('!~*', substr($string, 3));
      } else if ($c1.$c2.$c3 === '!~~') {
	 return array('NOT LIKE', substr($string, 3));
      } else if ($c1.$c2 === '!~') {
	 return array('!~', substr($string, 2));
      } else if ($c1.$c2 === '~*') {
	 return array('!=', substr($string, 2));
      } else if ($c1.$c2 === '~~') {
	 return array('LIKE', substr($string, 2));
      } else if ($c1 === '=') {
	 return array('=', substr($string, 1));
      } 
      
      return array('=', $string);

   }

}

?>
