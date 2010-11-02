<?php
/*
 * This file is part of the idlErrorManagementPlugin
 * (c) Idael Software <info AT idael.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * PluginApplicationErrorTable
 *
 * @package    idlErrorManagementPlugin
 * @subpackage model
 * @author     David Jeanmonod  <david AT idael.ch>
 */
class PluginApplicationErrorTable extends Doctrine_Table {
  
  /**
   * Returns an instance of this class.
   * @return object PluginApplicationErrorTable
   */
  public static function getInstance(){
    return Doctrine_Core::getTable('PluginApplicationError');
  }
    
}