<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------


namespace Addons\Oauthlogin;
use Common\Controller\Addon;
use Think\Db;
/**
 * 社会化登录 
 * @author kingang
 */

class OauthloginAddon extends Addon{

    public $info = array(
        'name'=>'Oauthlogin',
        'title'=>'社会化登录',
        'description'=>'集成各大开放平台的用户登录及接口调用SDK',
        'status'=>1,
        'author'=>'kingang(kingang@live.cn)',
        'version'=>'0.1'
    );
    //实现的login钩子方法    {:hook('login')}
    public function login($param){
    	$config=$this->getConfig();
    	$disp=$config['display'];
    	 $this->assign('list', $disp);
            $this->display('login');
    }
    public function install(){
               	$db_config = array();
        	$db_config['DB_TYPE'] = C('DB_TYPE');
        	$db_config['DB_HOST'] = C('DB_HOST');
        	$db_config['DB_NAME'] = C('DB_NAME');
        	$db_config['DB_USER'] = C('DB_USER');
        	$db_config['DB_PWD'] = C('DB_PWD');
        	$db_config['DB_PORT'] = C('DB_PORT');
        	$db_config['DB_PREFIX'] = C('DB_PREFIX');
        	$db = Db::getInstance($db_config);
        	//读取插件sql文件
        	$sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/install.sql');
        	$sqlFormat = $this->sql_split($sqldata, $db_config['DB_PREFIX']);
        	$counts = count($sqlFormat);
        	
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);

                if (strstr($sql, 'CREATE TABLE')) {
                    preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                    mysql_query("DROP TABLE IF EXISTS `$matches[1]");
                    $db->execute($sql);
                }
            }
            return true;
    }

    public function uninstall(){
               	$db_config = array();
        	$db_config['DB_TYPE'] = C('DB_TYPE');
        	$db_config['DB_HOST'] = C('DB_HOST');
        	$db_config['DB_NAME'] = C('DB_NAME');
        	$db_config['DB_USER'] = C('DB_USER');
        	$db_config['DB_PWD'] = C('DB_PWD');
        	$db_config['DB_PORT'] = C('DB_PORT');
        	$db_config['DB_PREFIX'] = C('DB_PREFIX');
        	$db = Db::getInstance($db_config);
        	//读取插件sql文件
        	$sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/uninstall.sql');
        	$sqlFormat = $this->sql_split($sqldata, $db_config['DB_PREFIX']);
        	$counts = count($sqlFormat);
        	 
        	for ($i = 0; $i < $counts; $i++) {
        		$sql = trim($sqlFormat[$i]);
        		$db->execute($sql);//执行语句
        	}
            return true;
    }
	
	public function sql_split($sql, $tablepre) {
        
        	if ($tablepre != "onethink_")
        		$sql = str_replace("onethink_", $tablepre, $sql);
        	$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        
        	if ($r_tablepre != $s_tablepre)
        		$sql = str_replace($s_tablepre, $r_tablepre, $sql);
        	$sql = str_replace("\r", "\n", $sql);
        	$ret = array();
        	$num = 0;
        	$queriesarray = explode(";\n", trim($sql));
        	unset($sql);
        	foreach ($queriesarray as $query) {
        		$ret[$num] = '';
        		$queries = explode("\n", trim($query));
        		$queries = array_filter($queries);
        		foreach ($queries as $query) {
        			$str1 = substr($query, 0, 1);
        			if ($str1 != '#' && $str1 != '-')
        				$ret[$num] .= $query;
        		}
        		$num++;
        	}
        	return $ret;
        }
        
}