<?php

define('CATEGORYCLASS', 1);

if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');

class Category extends Event {

   public $ecp;

   public $category;
   public $category_title;
   public $category_parent;
   public $category_sequence;

   public function __construct(&$db_class, &$error_class) {
      $this->Category_construct($db_class, $error_class);

      return TRUE;
   }

   protected function Category_construct(&$db_class, &$error_class) {
      $this->Event_construct($db_class, $error_class);

      $this->init();

      return TRUE;
   }

   private function init() {
      $this->ecp = 'Category';

      $this->category_sequence = 0;
   }

   public function get_all_categories($active=FALSE) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($active === TRUE) {
	 $where = ' WHERE active = \'t\' ';
      }

      $sql = 'SELECT "id", "title", "parent", "active", "sequence"
	       FROM "'.$this->category_table.'"
	       '.$where.' 
	       ORDER BY "sequence" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->category = $this->dbc->rows;

      return count($this->category);
   }

   public function create_md() {
      $cat_count = count($this->category);
      $idx = 0;
      for ($i = 0; $i < $cat_count; ++$i) {
	 if (strlen($this->category[$i]['parent']) > 0) continue;
	 $array[$idx]['parent'] = $this->category[$i];
	 $array[$idx]['children'] = array();
	 ++$idx;
      }

      for ($i = 0; $i < $cat_count; ++$i) {
	 if (strlen($this->category[$i]['parent']) === 0) continue;
	 $parent = &$this->category[$i]['parent'];
	 for ($j = 0, $jz = count($array); $j < $jz; ++$j) {
	    if ($array[$j]['parent']['id'] === $parent) {
	       $array[$j]['children'][] = $this->category[$i];
	    }
	 }
      }

      $this->category = $array;

      return TRUE;
   }

   public function get_parent_categories($active=FALSE) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $where = '';
      if ($active === TRUE) {
	 $where = ' AND active = \'t\' ';
      }

      $sql = 'SELECT "id", "title", "parent", "active", "sequence"
	       FROM "'.$this->category_table.'"
	       WHERE "parent" IS NULL '.$where.' 
	       ORDER BY "sequence" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->category = $this->dbc->rows;

      return count($this->category);
   }

   public function create() {
      if ($this->verify_category() === FALSE) return FALSE;
      if ($this->update_category_sequence() === FALSE) return FALSE;
      if ($this->create_category() === FALSE) return FALSE;

      return FALSE;
   }

   protected function verify_category() {
      if ($this->verify_column('title') === FALSE) return FALSE;
      if ($this->verify_column('parent') === FALSE) return FALSE;
      if ($this->verify_column('sequence') === FALSE) return FALSE;

      return TRUE;
   }

   protected function verify_column($column) {
      if ($column === 'title') {
	 $this->category_title = trim($this->category_title);
	 if (strlen($this->category_title) < 1 || strlen($this->category_title) > 50) {
	    $this->ec->create_error(1, 'Title must be between 1 and 50 characters', $this->ecp);
	    return FALSE;
	 }
      } else if ($column === 'parent') {
	 if (verify_int($this->category_parent) === FALSE) {
	    $this->ec->create_error(2, 'Invalid parent', $this->ecp);
	    return FALSE;
	 }
      } else if ($column === 'sequence') {
	 if (strlen(trim($this->category_sequence)) < 1) $this->category_sequence = 0;
	 if (verify_int($this->category_sequence) === FALSE) {
	    $this->ec->create_error(4, 'Invalid sequence', $this->ecp);
	    return FALSE;
	 }
      }

      return TRUE;
   }

   private function create_category() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->category_parent == 0) {
	 $this->category_parent = 0;
      }

      $insert['title']    = $this->dbc->escape($this->category_title);
      $insert['parent']   = $this->dbc->escape($this->category_parent);
      $insert['sequence'] = $this->dbc->escape($this->category_sequence);
      if ($this->dbc->insert_db($this->category_table, $insert) === FALSE) {
	 $this->ec->create_error(3, 'Could not create category', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function update_category_sequence() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->verify_column('sequence') === FALSE) return FALSE;
      if ($this->category_sequence === 0) return TRUE;

      if ($this->category_parent == 0) {
	 $this->category_parent = 0;
      }

      $update['sequence'] = '__SQL_FUNCTION__ "sequence" + 1';
      $where['parent'] = $this->dbc->escape($this->category_parent);
      $where['>=sequence'] = $this->dbc->escape($this->category_sequence);
      if ($this->dbc->update_db($this->category_table, $update, $where) === FALSE) {
	 $this->dbc->create_error(5, 'Could not update category sequence', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   /* 
    * The following functions can be used as static class methods
    */

   public function get_by_title($category_title=FALSE, $db_class=FALSE) {
      if ($category_title === FALSE) {
	 $category_title = &$this->category_title;
      }

      $category_title = trim($category_title);
      if (strlen($category_title) < 1) {
	 return FALSE;
      }

      if ($db_class === FALSE) {
	 if ($this->sanity_check() === FALSE) return FALSE;
	 $category_table = &$this->category_table;
	 $db_class = &$this->dbc;
      } else {
	 $category_table = 'category';
      }

      $category_title = strtolower(str_replace(array('_', '-'), ' ', $category_title));
      $sql = 'SELECT c."id", c."title", c."parent", c."active", c."sequence", 
	        p."title" as parent_title 
	       FROM "'.$category_table.'" as c
	       LEFT OUTER JOIN "'.$category_table.'" as p ON (p."id" = c."parent")
	       WHERE lower(c."title") = \''.$category_title.'\' 
	       LIMIT 1';
      $db_class->query($sql);
      $db_class->fetch_row();
      if ($db_class->row_count < 1) return FALSE;

      return $db_class->rows;
   }


}

?>
