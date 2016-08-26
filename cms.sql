-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.6.17 - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出 cms 的数据库结构
DROP DATABASE IF EXISTS `cms`;
CREATE DATABASE IF NOT EXISTS `cms` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `cms`;


-- 导出  表 cms.cms_log 结构
DROP TABLE IF EXISTS `cms_log`;
CREATE TABLE IF NOT EXISTS `cms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `manage_id` int(11) NOT NULL,
  `table` varchar(50) NOT NULL,
  `table_id` int(11) NOT NULL,
  `biaoti` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='消息表';

-- 正在导出表  cms.cms_log 的数据：~0 rows (大约)
DELETE FROM `cms_log`;
/*!40000 ALTER TABLE `cms_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_log` ENABLE KEYS */;


-- 导出  表 cms.cms_manage 结构
DROP TABLE IF EXISTS `cms_manage`;
CREATE TABLE IF NOT EXISTS `cms_manage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `mobile` varchar(50) NOT NULL COMMENT '手机号码',
  `pwd` char(32) NOT NULL COMMENT '密码',
  `login_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='管理员表';

-- 正在导出表  cms.cms_manage 的数据：1 rows
DELETE FROM `cms_manage`;
/*!40000 ALTER TABLE `cms_manage` DISABLE KEYS */;
INSERT INTO `cms_manage` (`id`, `created_at`, `updated_at`, `deleted_at`, `name`, `email`, `mobile`, `pwd`, `login_count`, `role_id`) VALUES
	(1, 0, 0, 0, 'root', '704872038@qq.com', '18782988780', '8beff24f77185fe07acaa8159f6a87c1', 8, 1);
/*!40000 ALTER TABLE `cms_manage` ENABLE KEYS */;


-- 导出  表 cms.cms_message 结构
DROP TABLE IF EXISTS `cms_message`;
CREATE TABLE IF NOT EXISTS `cms_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manage_code` varchar(50) NOT NULL,
  `biaoti` varchar(50) NOT NULL,
  `neirong` text NOT NULL,
  `is_readed` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息表';

-- 正在导出表  cms.cms_message 的数据：~0 rows (大约)
DELETE FROM `cms_message`;
/*!40000 ALTER TABLE `cms_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_message` ENABLE KEYS */;


-- 导出  表 cms.cms_permission 结构
DROP TABLE IF EXISTS `cms_permission`;
CREATE TABLE IF NOT EXISTS `cms_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级',
  `mingcheng` varchar(50) NOT NULL COMMENT '名称',
  `url` varchar(50) NOT NULL COMMENT 'url',
  `paixu` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `caidanxianshi` int(11) NOT NULL DEFAULT '0' COMMENT '查询显示',
  `quanxianxianshi` varchar(50) NOT NULL COMMENT '列表显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='权限';

-- 正在导出表  cms.cms_permission 的数据：~7 rows (大约)
DELETE FROM `cms_permission`;
/*!40000 ALTER TABLE `cms_permission` DISABLE KEYS */;
INSERT INTO `cms_permission` (`id`, `created_at`, `updated_at`, `deleted_at`, `parent_id`, `mingcheng`, `url`, `paixu`, `caidanxianshi`, `quanxianxianshi`) VALUES
	(1, 0, 0, 0, 0, '管理员管理', 'manage/index', 0, 0, ''),
	(2, 0, 0, 0, 0, '角色管理', 'role/index', 0, 0, ''),
	(3, 0, 0, 0, 0, '权限管理', 'permission/index', 0, 0, ''),
	(4, 0, 0, 0, 0, '模型管理', 'table/index', 0, 0, ''),
	(5, 0, 0, 0, 0, '测试', 'test/index', 0, 0, ''),
	(6, 0, 0, 0, 5, '测试添加', 'test/add', 0, 0, ''),
	(7, 0, 0, 0, 6, '测试添加子集', 'test/add', 0, 0, '');
/*!40000 ALTER TABLE `cms_permission` ENABLE KEYS */;


-- 导出  表 cms.cms_role 结构
DROP TABLE IF EXISTS `cms_role`;
CREATE TABLE IF NOT EXISTS `cms_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) NOT NULL,
  `mingcheng` varchar(50) NOT NULL COMMENT '名称',
  `quanxian` text NOT NULL COMMENT '权限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='角色表';

-- 正在导出表  cms.cms_role 的数据：2 rows
DELETE FROM `cms_role`;
/*!40000 ALTER TABLE `cms_role` DISABLE KEYS */;
INSERT INTO `cms_role` (`id`, `created_at`, `updated_at`, `deleted_at`, `mingcheng`, `quanxian`) VALUES
	(1, 0, 0, 0, '超级管理员', ''),
	(3, 0, 0, 0, '管理员', '');
/*!40000 ALTER TABLE `cms_role` ENABLE KEYS */;


-- 导出  表 cms.cms_table 结构
DROP TABLE IF EXISTS `cms_table`;
CREATE TABLE IF NOT EXISTS `cms_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mingcheng` varchar(50) NOT NULL,
  `biaoming` varchar(50) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='模型表';

-- 正在导出表  cms.cms_table 的数据：~0 rows (大约)
DELETE FROM `cms_table`;
/*!40000 ALTER TABLE `cms_table` DISABLE KEYS */;
INSERT INTO `cms_table` (`id`, `mingcheng`, `biaoming`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, '测试', 'test', 1464672318, 1464575647, 0);
/*!40000 ALTER TABLE `cms_table` ENABLE KEYS */;


-- 导出  表 cms.cms_table_field 结构
DROP TABLE IF EXISTS `cms_table_field`;
CREATE TABLE IF NOT EXISTS `cms_table_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_biaoming` varchar(50) NOT NULL,
  `ziduanming` varchar(50) NOT NULL,
  `ziduanming_pinyin` varchar(50) NOT NULL,
  `leixing` varchar(50) NOT NULL,
  `table` varchar(50) NOT NULL,
  `weiyi` tinyint(4) NOT NULL DEFAULT '0',
  `bitian` tinyint(4) NOT NULL DEFAULT '1',
  `chaxunxianshi` tinyint(4) NOT NULL DEFAULT '1',
  `liebiaoxianshi` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='模型表';

-- 正在导出表  cms.cms_table_field 的数据：~2 rows (大约)
DELETE FROM `cms_table_field`;
/*!40000 ALTER TABLE `cms_table_field` DISABLE KEYS */;
INSERT INTO `cms_table_field` (`id`, `table_biaoming`, `ziduanming`, `ziduanming_pinyin`, `leixing`, `table`, `weiyi`, `bitian`, `chaxunxianshi`, `liebiaoxianshi`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'manage', '管理员', 'role_id', 'table', 'role', 0, 1, 1, 1, 0, 1464575419, 0),
	(25, 'role', '权限', 'permission_id', 'table', 'permission', 0, 1, 1, 1, 0, 0, 0);
/*!40000 ALTER TABLE `cms_table_field` ENABLE KEYS */;


-- 导出  表 cms.cms_test 结构
DROP TABLE IF EXISTS `cms_test`;
CREATE TABLE IF NOT EXISTS `cms_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `manage_id` varchar(50) NOT NULL,
  `zifuchuan` varchar(50) NOT NULL,
  `riqi` int(11) NOT NULL DEFAULT '0',
  `shuzi` int(11) NOT NULL DEFAULT '0',
  `zhutu` varchar(50) NOT NULL,
  `wenben` text NOT NULL,
  `fuwenben` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='商品';

-- 正在导出表  cms.cms_test 的数据：~7 rows (大约)
DELETE FROM `cms_test`;
/*!40000 ALTER TABLE `cms_test` DISABLE KEYS */;
INSERT INTO `cms_test` (`id`, `created_at`, `updated_at`, `deleted_at`, `manage_id`, `zifuchuan`, `riqi`, `shuzi`, `zhutu`, `wenben`, `fuwenben`) VALUES
	(1, 0, 1464588565, 0, 'zhangsan', 'aaaaaaaaa', 22222222, 3, '', 'wwwwwwww', '&lt;p&gt;&amp;lt;p&amp;gt;bababa a哈哈哈&amp;lt;/p&amp;gt;&amp;lt;p&amp;gt;fasjkfhas&amp;lt;/p&amp;gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;爱上了反馈&lt;/p&gt;'),
	(2, 1464531063, 1464531063, 0, 'lisi', 'dierge', 2016, 2, '', 'wenbenqu', ''),
	(3, 1464531580, 1464575268, 1464575268, 'zhangsan', '3333', 1970, 333, '', 'di33333333333333333333', '&lt;p&gt;di333333di第3333333&lt;/p&gt;'),
	(4, 1464531675, 1464588412, 0, 'lisi', '44444444444444', 2016, 4444, '201605292221101558AID.jpg', '444', '&lt;p&gt;&amp;lt;p&amp;gt;444444呵呵呵呵呵呵&amp;lt;/p&amp;gt;&lt;/p&gt;'),
	(5, 1464539442, 1464539520, 0, 'lisi', 'ee', 1464364800, 88888, '', '88888888', ''),
	(6, 0, 0, 0, '444', '', 0, 0, '', '', ''),
	(7, 1464588940, 1464591694, 0, 'zhangsan', 's777777777777', 1464278400, 6, '201605301415316795AID.png', 'yeryryrt', '&lt;p&gt;凤飞飞凤飞飞凤飞飞&lt;/p&gt;&lt;p&gt;kkkkkk&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;3333&lt;/p&gt;');
/*!40000 ALTER TABLE `cms_test` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
